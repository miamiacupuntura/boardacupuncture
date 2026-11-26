#!/usr/bin/env python3
"""Extract and classify NCBAHM Herbology questions from Moodle SCORM packages.

The Moodle database and file pool are read-only. The script creates one JSON
inventory in moodledata so administrators can audit source questions before
they are approved for practice or simulation exams.
"""

from __future__ import annotations

import argparse
import hashlib
import json
import re
import subprocess
import tempfile
import zipfile
from collections import Counter
from pathlib import Path
from typing import Any

from clinical_cases_extract import correct_option_index, feedback_text, source_text
from scorm_word_export import (
    answer_text,
    choose_placements,
    find_quizdata_member,
    load_inventory,
    package_path,
)


SCRIPT_DIR = Path(__file__).resolve().parent
DEFAULT_COURSES = [45, 76, 41, 82]
DEFAULT_STATIC_BANK = SCRIPT_DIR.parents[2] / "blocks" / "menu" / "herbology-domain-candidates.json"
DOMAIN_IDS = (
    "formula_differentiation",
    "single_herb",
    "dui_yao",
    "safety_dispensing",
    "formula_composition",
    "scope_aliases",
)
DOMAIN_PREFIXES = {
    "formula_differentiation": "FD",
    "single_herb": "SH",
    "dui_yao": "DY",
    "safety_dispensing": "SD",
    "formula_composition": "FC",
    "scope_aliases": "SA",
}

PROPERTY_PATTERN = re.compile(
    r"\b(?:propert(?:y|ies)|flavou?r|taste|temperature|thermal nature|channel(?:s)?|"
    r"meridian(?:s)?|organ entr(?:y|ies)|acrid|pungent|bitter|sweet|sour|salty|"
    r"aromatic|astringent)\b",
    re.IGNORECASE,
)
HIERARCHY_PATTERN = re.compile(
    r"\b(?:ingredient hierarchy|hierarchy of ingredients|hierarch(?:y|ical)|"
    r"chief|emperor|deputy|minister|assistant|envoy|messenger|jun[–\- ]chen|"
    r"jun[–\- ]chen[–\- ]zuo[–\- ]shi|ingredient role)\b",
    re.IGNORECASE,
)
COMPOSITION_PATTERN = re.compile(
    r"\b(?:ingredients?|composition|composed of|consists? of|component|"
    r"which herbs? (?:are|is|in)|modify|modification|add (?:which|what)|"
    r"remove|subtract|replace)\b",
    re.IGNORECASE,
)
PROCESSING_PATTERN = re.compile(
    r"\b(?:processing|processed|preparation|prepare|pao\s*zhi|decoct|decoction|"
    r"pre[- ]?decoct|add(?:ed)? (?:near|at) the end|bag[- ]?decoct|crush|"
    r"dissolve|raw form|prepared form|dry[- ]?fried|honey[- ]?prepared|"
    r"wine[- ]?processed|vinegar[- ]?processed|charred|calcined|roasted|"
    r"toxic if raw|reduce toxicity)\b",
    re.IGNORECASE,
)
SAFETY_PATTERN = re.compile(
    r"\b(?:safety|caution|contraindicat|toxic|toxicity|overdose|adverse|"
    r"interaction|pregnan|dosage|dose|dispens|prohibited|protected|"
    r"preparation|processing|decoct)\w*\b",
    re.IGNORECASE,
)
PAIR_PATTERN = re.compile(
    r"\b(?:du[iì]\s*y[aà]o|herb pair|paired herbs?|compatibility relationship|"
    r"mutual reinforcement|mutual assistance|mutual restraint|mutual suppression|"
    r"mutual antagonism|mutual incompatibility)\b",
    re.IGNORECASE,
)
SCOPE_PATTERN = re.compile(
    r"\b(?:alias(?:es)?|name variant|pharmaceutical name|official (?:list|roster)|"
    r"obsolete|historical alternative|legal substitute|protected species|"
    r"endangered|botanical identity|authenticated identity)\b",
    re.IGNORECASE,
)
CLINICAL_PATTERN = re.compile(
    r"\b(?:case|patient|presents?|complains?|history of|tongue|pulse|"
    r"manifest(?:s|ed)?|symptoms?|signs?|diagnosis|presentation)\b",
    re.IGNORECASE,
)
COMPARISON_PATTERN = re.compile(
    r"\b(?:differentiat|difference|compare|comparison|versus|vs\.?|except|"
    r"not correct|most appropriate|best explains|decisive clue)\w*\b",
    re.IGNORECASE,
)
DIRECT_PATTERN = re.compile(
    r"^\s*(?:what (?:is|are)|which (?:herb|formula|ingredient)|identify|name|"
    r"how much|true or false)\b",
    re.IGNORECASE,
)


def normalize(value: Any) -> str:
    return re.sub(r"\s+", " ", source_text(value)).strip().casefold()


def question_fingerprint(prompt: str, options: list[str]) -> str:
    normalized_options = sorted(normalize(option) for option in options)
    payload = normalize(prompt) + "\n" + "\n".join(normalized_options)
    return hashlib.sha256(payload.encode("utf-8")).hexdigest()


def module_text(placement: dict[str, Any]) -> str:
    return " ".join(
        str(placement.get(key) or "")
        for key in ("coursename", "sectionname", "scormname")
    )


def classify_domain(prompt: str, placement: dict[str, Any]) -> tuple[str, str, str]:
    metadata = module_text(placement)
    combined = f"{metadata}\n{prompt}"
    if SCOPE_PATTERN.search(combined):
        return "scope_aliases", "roster_aliases", "Nomenclature, roster boundary or substitution language"
    if PAIR_PATTERN.search(combined):
        return "dui_yao", "dui_yao", "Duì Yào or herb-pair compatibility language"
    if PROCESSING_PATTERN.search(combined):
        return "safety_dispensing", "herb_processing", "Preparation or processing changes safe dispensing or herb behavior"
    if SAFETY_PATTERN.search(combined):
        return "safety_dispensing", "safety_dispensing", "Explicit safety, dosage, interaction or dispensing language"
    if HIERARCHY_PATTERN.search(combined):
        return "formula_composition", "formula_hierarchy", "Jun–Chen–Zuo–Shi or ingredient-role language"
    if COMPOSITION_PATTERN.search(combined):
        return "formula_composition", "formula_composition", "Formula ingredients, construction or modification language"
    if PROPERTY_PATTERN.search(metadata) or PROPERTY_PATTERN.search(prompt):
        return "single_herb", "herb_properties", "Flavor, temperature, channel or other individual-herb property language"
    if re.search(r"\b(?:single herbs?|materia medica|individual herbs?)\b", metadata, re.IGNORECASE):
        return "single_herb", "single_herb", "Question originates in an individual-herb SCORM module"
    if CLINICAL_PATTERN.search(combined):
        return "formula_differentiation", "clinical_case", "Clinical presentation requires formula or pattern selection"
    return "formula_differentiation", "formula_selection", "Formula, pattern or treatment-selection question"


def classify_cognitive(prompt: str) -> tuple[str, str, str]:
    if COMPARISON_PATTERN.search(prompt):
        return "analysis", "analyze", "comparison or differential reasoning"
    clinical_markers = len(re.findall(
        r"\b(?:patient|presents?|complains?|tongue|pulse|fever|pain|sweat|"
        r"stool|urine|cough|bleeding|thirst|appetite|sleep)\b",
        prompt,
        re.IGNORECASE,
    ))
    if CLINICAL_PATTERN.search(prompt) and clinical_markers >= 3:
        return "inference", "infer", "multi-clue clinical inference"
    if CLINICAL_PATTERN.search(prompt):
        return "application", "apply", "clinical application"
    if DIRECT_PATTERN.search(prompt):
        return "recall", "remember", "direct knowledge recall"
    return "application", "apply", "applied recognition"


def classify_feedback(feedback: str) -> tuple[str, str]:
    """Classify feedback depth without rewriting the original SCORM text."""
    word_count = len(re.findall(r"\b[\w’'-]+\b", feedback, re.UNICODE))
    if word_count == 0:
        return "missing", "Add an explanation for the keyed answer and the distractors."
    reasoning = re.search(
        r"\b(?:because|therefore|indicates?|accounts? for|fails? to|rather than|"
        r"while|whereas|however|distractor|correct|incorrect)\b",
        feedback,
        re.IGNORECASE,
    )
    if word_count < 35 or reasoning is None:
        return "brief", "Expand the explanation and state why the distractors are weaker."
    return "detailed", "Retain the source feedback and verify its clinical reasoning."


def load_static_fingerprints(path: Path | None) -> tuple[set[str], set[str]]:
    fingerprints: set[str] = set()
    ids: set[str] = set()
    if path is None or not path.is_file():
        return fingerprints, ids
    payload = json.loads(path.read_text(encoding="utf-8"))
    for questions in (payload.get("domains") or {}).values():
        for question in questions or []:
            options = [str(option.get("text") or "") for option in question.get("options") or []]
            prompt = str(question.get("prompt") or "")
            if prompt and options:
                fingerprints.add(question_fingerprint(prompt, options))
            if question.get("questionId"):
                ids.add(str(question["questionId"]))
    return fingerprints, ids


def load_questions(packagepath: Path) -> list[Any]:
    with zipfile.ZipFile(packagepath, "r") as package:
        member = find_quizdata_member(package)
        if not member:
            raise ValueError("QUIZDATA.json not found")
        questions = json.loads(package.read(member).decode("utf-8-sig")).get("Questions")
    if not isinstance(questions, list):
        raise ValueError("QUIZDATA.json has no Questions array")
    return questions


def deterministic_rank(seed: str, value: str) -> str:
    """Return a stable pseudo-random rank without depending on process state."""
    return hashlib.sha256(f"{seed}\n{normalize(value)}".encode("utf-8")).hexdigest()


def matching_multiple_choice_questions(
    source: dict[str, Any],
    placement: dict[str, Any],
    question_number: int,
) -> tuple[list[dict[str, Any]], Counter[str]]:
    """Convert one SCORM matching table into independent four-choice items.

    The answer key and every distractor come from the same SCORM table. The
    Moodle source package remains untouched.
    """
    left_column = source.get("LeftColumn") or []
    right_column = source.get("RightColumn") or []
    skipped: Counter[str] = Counter()
    if not isinstance(left_column, list) or not isinstance(right_column, list):
        return [], Counter({"matching question has invalid columns": 1})

    right_by_id: dict[str, str] = {}
    right_values: list[str] = []
    right_value_keys: set[str] = set()
    for item in right_column:
        if not isinstance(item, dict):
            continue
        text = source_text(answer_text(item))
        item_id = normalize(item.get("Id"))
        if not text:
            continue
        if item_id and item_id not in right_by_id:
            right_by_id[item_id] = text
        text_key = normalize(text)
        if text_key not in right_value_keys:
            right_value_keys.add(text_key)
            right_values.append(text)

    base_prompt = source_text(source.get("QuestionText"))
    source_feedback = feedback_text(source)
    converted: list[dict[str, Any]] = []
    for item_number, item in enumerate(left_column, start=1):
        if not isinstance(item, dict):
            skipped["invalid matching left-column item"] += 1
            continue
        left_text = source_text(answer_text(item))
        item_id = normalize(item.get("Id"))
        correct_text = right_by_id.get(item_id, "")
        if not correct_text and item_number <= len(right_column):
            correct_text = source_text(answer_text(right_column[item_number - 1]))
        if not left_text or not correct_text:
            skipped["matching answer key not found"] += 1
            continue

        distractors = [
            value for value in right_values
            if normalize(value) != normalize(correct_text)
        ]
        seed = f"{placement.get('contenthash')}:{question_number}:{item_number}:{left_text}"
        distractors.sort(key=lambda value: deterministic_rank(seed, value))
        if len(distractors) < 3:
            skipped["matching item has fewer than three source distractors"] += 1
            continue

        option_texts = [correct_text, *distractors[:3]]
        option_texts.sort(key=lambda value: deterministic_rank(f"option:{seed}", value))
        if base_prompt:
            prompt = f"{base_prompt}\n\nWhich option correctly matches {left_text}?"
        else:
            prompt = f"Which hierarchy description correctly matches {left_text}?"
        explanation_parts = [source_feedback] if source_feedback else []
        explanation_parts.append(f"Correct match: {left_text} — {correct_text}.")
        generated_source = {
            "__type": "MultipleChoiceQuestion",
            "QuestionText": prompt,
            "Answers": [
                {"Content": text, "Correct": normalize(text) == normalize(correct_text)}
                for text in option_texts
            ],
            "Feedback": [{"Content": "\n\n".join(explanation_parts)}],
        }
        question, reason = extract_question(generated_source, placement, question_number)
        if question is None:
            skipped[reason or "converted matching item was not extractable"] += 1
            continue
        question["review"]["notes"] = (
            "Converted deterministically from a SCORM matching table. The correct answer and "
            "all distractors come from that same source table; the Moodle package was not changed."
        )
        question["scorm"].update({
            "questionType": "MatchingQuestion",
            "convertedFrom": "MatchingQuestion",
            "matchingItemNumber": item_number,
            "matchingItem": left_text,
        })
        converted.append(question)
    return converted, skipped


def extract_source_questions(
    source: dict[str, Any],
    placement: dict[str, Any],
    question_number: int,
) -> tuple[list[dict[str, Any]], Counter[str]]:
    if str(source.get("__type") or "") == "MatchingQuestion":
        return matching_multiple_choice_questions(source, placement, question_number)
    question, reason = extract_question(source, placement, question_number)
    if question is None:
        return [], Counter({reason or "not extractable": 1})
    return [question], Counter()


def extract_question(
    source: dict[str, Any],
    placement: dict[str, Any],
    question_number: int,
) -> tuple[dict[str, Any] | None, str | None]:
    prompt = source_text(source.get("QuestionText"))
    answers = source.get("Answers") or []
    if not prompt or not isinstance(answers, list):
        return None, "missing prompt or answers"
    option_texts = [source_text(answer_text(answer)) for answer in answers]
    if len(option_texts) < 2 or any(not option for option in option_texts):
        return None, "fewer than two complete options"
    keyed_answers = sum(
        1 for answer in answers if isinstance(answer, dict) and bool(answer.get("Correct"))
    )
    if keyed_answers > 1:
        return None, "not a single-answer multiple-choice question"
    correct_index = correct_option_index(source, option_texts)
    if correct_index is None or correct_index >= len(option_texts):
        return None, "answer key not found"
    labels = [chr(65 + index) if index < 26 else str(index + 1) for index in range(len(option_texts))]
    fingerprint = question_fingerprint(prompt, option_texts)
    domain_id, content_focus, domain_reason = classify_domain(prompt, placement)
    difficulty, cognitive_level, cognitive_reason = classify_cognitive(prompt)
    feedback = feedback_text(source)
    feedback_quality, feedback_recommendation = classify_feedback(feedback)
    inference_upgrade = cognitive_level not in {"infer", "analyze"}
    simulation_candidate = (
        len(option_texts) == 4
        and cognitive_level in {"infer", "analyze"}
        and feedback_quality == "detailed"
    )
    editorial_needs = []
    if inference_upgrade:
        editorial_needs.append("inference-upgrade")
    if feedback_quality != "detailed":
        editorial_needs.append("feedback-expansion")
    source_url = f"/mod/scorm/view.php?id={int(placement.get('cmid') or 0)}"
    question_id = f"SCORM-{DOMAIN_PREFIXES[domain_id]}-{fingerprint[:12].upper()}"
    source_title = str(placement.get("scormname") or "SCORM activity")
    source_locator = " · ".join(filter(None, [
        str(placement.get("coursename") or ""),
        str(placement.get("sectionname") or ""),
        source_title,
        f"Question {question_number}",
    ]))
    return {
        "questionId": question_id,
        "questionTrack": "board-source-audit",
        "criteriaVersion": "scorm-classifier-1.0",
        "itemFormat": "multiple-choice",
        "feedbackStatus": "practice-ready" if feedback and len(option_texts) == 4 else "source-keyed",
        "examId": "chinese-herbal-medicine",
        "outlineVersion": "NCBAHM-2026-working-map",
        "domainId": domain_id,
        "objectiveId": f"working-{domain_id}",
        "difficultyBand": difficulty,
        "cognitiveLevel": cognitive_level,
        "contentType": content_focus,
        "language": "en",
        "sourceKind": "scorm",
        "source": {
            "title": source_title,
            "locator": source_locator,
            "courseId": int(placement.get("courseid") or 0),
            "courseModuleId": int(placement.get("cmid") or 0),
            "section": str(placement.get("sectionname") or ""),
        },
        "sourceCard": {
            "url": source_url,
            "urlEs": source_url,
            "label": f"Open SCORM source: {source_title}",
            "labelEs": f"Abrir fuente SCORM: {source_title}",
        },
        "review": {
            "status": "practice-ready" if feedback and len(option_texts) == 4 else "source-keyed",
            "notes": "Extracted without changing the Moodle SCORM source. Administrative review is required before exam use.",
        },
        "prompt": prompt,
        "options": [
            {"label": label, "text": text}
            for label, text in zip(labels, option_texts)
        ],
        "correctLabel": labels[correct_index],
        "explanation": feedback,
        "classification": {
            "method": "deterministic-scorm-rule-v1",
            "confidence": "high" if content_focus in {"herb_properties", "formula_hierarchy", "herb_processing"} else "medium",
            "reason": domain_reason,
            "cognitiveReason": cognitive_reason,
        },
        "editorial": {
            "originalPreserved": True,
            "inferenceUpgradeRequired": inference_upgrade,
            "feedbackQuality": feedback_quality,
            "feedbackRecommendation": feedback_recommendation,
            "simulationCandidate": simulation_candidate,
            "needs": editorial_needs,
        },
        "scorm": {
            "fingerprint": fingerprint,
            "contentHash": str(placement.get("contenthash") or ""),
            "questionNumber": question_number,
            "questionType": str(source.get("__type") or "MultipleChoiceQuestion"),
        },
    }, None


def build_bank(
    inventory: dict[str, Any],
    courses: list[int],
    static_bank: Path | None,
    module_pattern: re.Pattern[str] | None = None,
) -> dict[str, Any]:
    dataroot = Path(str(inventory["dataroot"])).resolve()
    records = inventory.get("records") or []
    if module_pattern is not None:
        records = [
            record for record in records
            if module_pattern.search(str(record.get("scormname") or ""))
        ]
    grouped = choose_placements(records, courses)
    static_fingerprints, static_ids = load_static_fingerprints(static_bank)
    domains: dict[str, list[dict[str, Any]]] = {domain_id: [] for domain_id in DOMAIN_IDS}
    seen_fingerprints: set[str] = set()
    skipped = Counter()
    errors: list[dict[str, str]] = []
    module_count = 0

    for content_hash, placements in grouped.items():
        placement = placements[0]
        placement["contenthash"] = content_hash
        try:
            source_questions = load_questions(package_path(dataroot, content_hash))
        except (OSError, ValueError, json.JSONDecodeError, zipfile.BadZipFile) as error:
            errors.append({"module": str(placement.get("scormname") or content_hash), "error": str(error)})
            continue
        module_count += 1
        for number, source_question in enumerate(source_questions, start=1):
            if not isinstance(source_question, dict):
                skipped["invalid question record"] += 1
                continue
            extracted, extraction_skips = extract_source_questions(
                source_question, placement, number
            )
            skipped.update(extraction_skips)
            for question in extracted:
                fingerprint = str(question["scorm"]["fingerprint"])
                if fingerprint in static_fingerprints:
                    skipped["duplicate of classified bank"] += 1
                    continue
                if fingerprint in seen_fingerprints:
                    skipped["duplicate SCORM question"] += 1
                    continue
                if question["questionId"] in static_ids:
                    skipped["duplicate question ID"] += 1
                    continue
                seen_fingerprints.add(fingerprint)
                domains[str(question["domainId"])].append(question)

    for questions in domains.values():
        questions.sort(key=lambda question: (
            str(question["source"]["title"]).casefold(),
            int(question["scorm"]["questionNumber"]),
            str(question["questionId"]),
        ))
    return summarize_bank(
        domains,
        courses=courses,
        module_count=module_count,
        skipped=skipped,
        errors=errors,
        module_pattern=module_pattern.pattern if module_pattern else "",
    )


def summarize_bank(
    domains: dict[str, list[dict[str, Any]]],
    *,
    courses: list[int],
    module_count: int | None = None,
    skipped: Counter[str] | dict[str, int] | None = None,
    errors: list[dict[str, str]] | None = None,
    module_pattern: str = "",
) -> dict[str, Any]:
    for domain_id in DOMAIN_IDS:
        domains.setdefault(domain_id, [])
    all_questions = [question for questions in domains.values() for question in questions]
    if module_count is None:
        module_count = len({
            (
                int(question.get("source", {}).get("courseId") or 0),
                int(question.get("source", {}).get("courseModuleId") or 0),
            )
            for question in all_questions
        })
    content_counts = Counter(str(question["contentType"]) for question in all_questions)
    cognitive_counts = Counter(str(question["cognitiveLevel"]) for question in all_questions)
    feedback_counts = Counter(str(question["editorial"]["feedbackQuality"]) for question in all_questions)
    inference_upgrade_count = sum(
        1 for question in all_questions if question["editorial"]["inferenceUpgradeRequired"]
    )
    simulation_candidate_count = sum(
        1 for question in all_questions if question["editorial"]["simulationCandidate"]
    )
    domain_counts = {domain_id: len(domains[domain_id]) for domain_id in DOMAIN_IDS}
    return {
        "schemaVersion": 1,
        "source": "Moodle SCORM Herbology activities",
        "sourceReadOnly": True,
        "courseIds": courses,
        "modulePattern": module_pattern,
        "moduleCount": module_count,
        "questionCount": len(all_questions),
        "domainCounts": domain_counts,
        "contentTypeCounts": dict(sorted(content_counts.items())),
        "cognitiveLevelCounts": dict(sorted(cognitive_counts.items())),
        "feedbackQualityCounts": dict(sorted(feedback_counts.items())),
        "inferenceUpgradeCount": inference_upgrade_count,
        "simulationCandidateCount": simulation_candidate_count,
        "skipped": dict(sorted((skipped or {}).items())),
        "errors": errors or [],
        "domains": domains,
    }


def merge_bank(
    existing: dict[str, Any],
    targeted: dict[str, Any],
    courses: list[int],
    module_pattern: re.Pattern[str] | None,
) -> dict[str, Any]:
    """Replace only selected course/modules while preserving the rest."""
    course_set = set(courses)
    domains: dict[str, list[dict[str, Any]]] = {domain_id: [] for domain_id in DOMAIN_IDS}
    seen: set[str] = set()

    def is_replaced(question: dict[str, Any]) -> bool:
        source = question.get("source") or {}
        if int(source.get("courseId") or 0) not in course_set:
            return False
        if module_pattern is None:
            return True
        title = str(source.get("title") or "")
        locator = str(source.get("locator") or "")
        return bool(module_pattern.search(title) or module_pattern.search(locator))

    for domain_id in DOMAIN_IDS:
        for question in (existing.get("domains") or {}).get(domain_id) or []:
            if not isinstance(question, dict) or is_replaced(question):
                continue
            key = str(question.get("questionId") or question_fingerprint(
                str(question.get("prompt") or ""),
                [str(option.get("text") or "") for option in question.get("options") or []],
            ))
            if key not in seen:
                seen.add(key)
                domains[domain_id].append(question)
        for question in (targeted.get("domains") or {}).get(domain_id) or []:
            if not isinstance(question, dict):
                continue
            key = str(question.get("questionId") or "")
            if key and key not in seen:
                seen.add(key)
                domains[domain_id].append(question)
        domains[domain_id].sort(key=lambda question: (
            str(question.get("source", {}).get("title") or "").casefold(),
            int(question.get("scorm", {}).get("questionNumber") or 0),
            int(question.get("scorm", {}).get("matchingItemNumber") or 0),
            str(question.get("questionId") or ""),
        ))

    merged_courses = sorted({
        *[int(value) for value in existing.get("courseIds") or []],
        *courses,
    })
    return summarize_bank(
        domains,
        courses=merged_courses,
        skipped=targeted.get("skipped") or {},
        errors=targeted.get("errors") or [],
    )


def write_json_atomic(path: Path, payload: dict[str, Any]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    with tempfile.NamedTemporaryFile(
        "w", encoding="utf-8", dir=path.parent, delete=False, prefix=f".{path.name}-"
    ) as handle:
        json.dump(payload, handle, ensure_ascii=False, indent=2)
        handle.write("\n")
        temporary = Path(handle.name)
    temporary.replace(path)


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--courses", default=",".join(str(course) for course in DEFAULT_COURSES))
    parser.add_argument("--php-bin", default="php")
    parser.add_argument("--inventory", type=Path)
    parser.add_argument("--static-bank", type=Path, default=DEFAULT_STATIC_BANK)
    parser.add_argument("--no-static-dedupe", action="store_true")
    parser.add_argument("--module-pattern", help="Regular expression matched against SCORM activity names")
    parser.add_argument("--merge-existing", action="store_true")
    parser.add_argument("--target-export", type=Path)
    parser.add_argument("--output", type=Path)
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    courses = [int(value.strip()) for value in args.courses.split(",") if value.strip().isdigit()]
    if not courses:
        raise SystemExit("At least one valid Herbology course ID is required.")
    inventory = (
        json.loads(args.inventory.read_text(encoding="utf-8"))
        if args.inventory
        else load_inventory(args.php_bin, courses)
    )
    try:
        module_pattern = re.compile(args.module_pattern, re.IGNORECASE) if args.module_pattern else None
    except re.error as error:
        raise SystemExit(f"Invalid --module-pattern: {error}") from error
    static_bank = None if args.no_static_dedupe else (
        args.static_bank.resolve() if args.static_bank else None
    )
    bank = build_bank(inventory, courses, static_bank, module_pattern)
    dataroot = Path(str(inventory["dataroot"])).resolve()
    output = args.output.resolve() if args.output else (
        dataroot / "board-domain-banks" / "herbology-scorm-question-bank.json"
    )
    if args.target_export:
        write_json_atomic(args.target_export.resolve(), bank)
    if args.merge_existing and output.is_file():
        existing = json.loads(output.read_text(encoding="utf-8"))
        bank = merge_bank(existing, bank, courses, module_pattern)
    write_json_atomic(output, bank)
    print(json.dumps({
        "output": str(output),
        "targetExport": str(args.target_export.resolve()) if args.target_export else "",
        "modules": bank["moduleCount"],
        "questions": bank["questionCount"],
        "domains": bank["domainCounts"],
        "contentTypes": bank["contentTypeCounts"],
        "cognitiveLevels": bank["cognitiveLevelCounts"],
        "feedbackQuality": bank["feedbackQualityCounts"],
        "inferenceUpgrades": bank["inferenceUpgradeCount"],
        "simulationCandidates": bank["simulationCandidateCount"],
        "errors": len(bank["errors"]),
    }, ensure_ascii=False))
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except subprocess.CalledProcessError as error:
        print(error.stderr or str(error))
        raise SystemExit(error.returncode or 1)
