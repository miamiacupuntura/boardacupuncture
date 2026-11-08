#!/usr/bin/env python3
"""Extract the Handbook clinical-case SCORM questions into a Quiz JSON bank.

The Moodle database and file pool are read-only. Only the requested JSON output
is created or atomically replaced.
"""

from __future__ import annotations

import argparse
import hashlib
import html
import json
import re
import subprocess
import tempfile
import zipfile
from pathlib import Path
from typing import Any

from scorm_word_export import (
    answer_text,
    choose_placements,
    find_quizdata_member,
    load_inventory,
    normalise_text,
    package_path,
)


SCRIPT_DIR = Path(__file__).resolve().parent
DEFAULT_FORMULA_DATA = (
    SCRIPT_DIR.parents[2] / "blocks" / "myprogress" / "formulas-study-guide-data.json"
)
DEFAULT_COURSES = [45, 76, 41, 82]
TARGET_COURSE = "herbal comp review module exams"
TARGET_TOPIC = "handbook-of-oriental-medicine. clinical cases"
EMBEDDED_ONE = re.compile(r"(?<=[A-Za-z])1(?=[A-Za-z])")
SUSPICIOUS_EMBEDDED_DIGIT = re.compile(r"(?<=[A-Za-z])[0-9](?=[A-Za-z])")


def normalise_name(value: Any) -> str:
    return re.sub(r"\s+", " ", str(value or "").strip()).casefold()


def source_text(value: Any) -> str:
    """Decode source markup and normalize known OCR substitutions."""
    decoded = normalise_text(value)
    for _ in range(3):
        unescaped = html.unescape(decoded)
        if unescaped == decoded:
            break
        decoded = unescaped
    decoded = re.sub(r"\s*&\s*", " and ", decoded)
    return EMBEDDED_ONE.sub("i", decoded)


def load_formula_profiles(path: Path) -> dict[str, dict[str, Any]]:
    """Load source-grounded formula profiles keyed by normalized pinyin name."""
    payload = json.loads(path.read_text(encoding="utf-8"))
    profiles: dict[str, dict[str, Any]] = {}
    for collection in ("formulas", "supplementalFormulas"):
        for profile in payload.get(collection) or []:
            if not isinstance(profile, dict):
                continue
            key = normalise_name(profile.get("name"))
            if key and key not in profiles:
                profiles[key] = profile
    if not profiles:
        raise ValueError(f"No formula profiles found in {path}")
    return profiles


def diagnostic_feedback(
    correctoption: str,
    profiles: dict[str, dict[str, Any]],
    prompt: str = "",
    options: list[str] | None = None,
) -> dict[str, Any] | None:
    """Match the correct option to its guideline profile and build rationale."""
    def match_profile(option: str) -> dict[str, Any] | None:
        normalizedoption = normalise_name(option)
        for name in sorted(profiles, key=len, reverse=True):
            if normalizedoption == name or normalizedoption.startswith(f"{name} "):
                return profiles[name]
        return None

    profile = match_profile(correctoption)
    if profile is None:
        return None

    fields = {
        "formula": profile.get("name"),
        "pattern": profile.get("pattern"),
        "patternEs": profile.get("patternEs"),
        "clue": profile.get("clue"),
        "clueEs": profile.get("clueEs"),
        "action": profile.get("action"),
        "actionEs": profile.get("actionEs"),
        "reasoning": profile.get("examFocus"),
        "reasoningEs": profile.get("examFocusEs"),
        "differential": profile.get("comparison"),
        "differentialEs": profile.get("comparisonEs"),
    }
    cleaned = {
        key: source_text(value)
        for key, value in fields.items()
        if source_text(value)
    }

    normalizedprompt = normalise_name(prompt)
    profilename = normalise_name(profile.get("name"))
    if (
        profilename == "chai ge jie ji tang"
        and "pain around her eye" in normalizedprompt
        and "slightly surging" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The illness began as exterior Wind-Cold, shown by chills, body aches, "
            "runny nose and cough with white phlegm. It is now transforming into Heat: "
            "the fever is rising while aversion to cold is only mild, and thirst, a red "
            "tongue with yellow coat, a floating slightly surging pulse and orbital pain "
            "have appeared. This progression identifies constrained exterior Cold in the "
            "muscle layer transforming into Heat, the key Chai Ge Jie Ji Tang pattern."
        )
        cleaned["caseReasoningEs"] = (
            "El cuadro comenzó como Viento-Frío exterior, indicado por escalofríos, dolores "
            "corporales, rinorrea y tos con flema blanca. Ahora se transforma en Calor: la "
            "fiebre aumenta mientras la aversión al frío es leve, y aparecen sed, lengua "
            "roja con capa amarilla, pulso flotante ligeramente desbordante y dolor orbital. "
            "Esta evolución identifica Frío exterior constreñido en la capa muscular que se "
            "transforma en Calor, el patrón clave de Chai Ge Jie Ji Tang."
        )
    elif (
        profilename == "da cheng qi tang"
        and "solid mass" in normalizedprompt
        and "deep pulse" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "This is severe Yangming-Fu Heat binding with stool impaction. The hard, "
            "palpable abdominal mass, guarding and marked pain establish fullness, "
            "hardness and excess; the dry red tongue with yellow coat and deep, rapid, "
            "forceful pulse confirm interior Heat excess. The earlier green, foul watery "
            "stool can represent overflow around an impaction rather than resolution. "
            "That severe combination calls for Da Cheng Qi Tang rather than the milder "
            "Xiao Cheng Qi Tang pattern."
        )
        cleaned["caseReasoningEs"] = (
            "Se trata de una ligadura grave de Calor en Yangming-Fu con impactación fecal. "
            "La masa abdominal dura y palpable, la defensa y el dolor marcado establecen "
            "plenitud, dureza y exceso; la lengua roja seca con capa amarilla y el pulso "
            "profundo, rápido y fuerte confirman Calor interno de exceso. Las heces acuosas, "
            "verdes y fétidas previas pueden representar rebosamiento alrededor de una "
            "impactación, no resolución. Esta combinación grave requiere Da Cheng Qi Tang, "
            "no el patrón más leve de Xiao Cheng Qi Tang."
        )
    elif (
        profilename == "xiao cheng qi tang"
        and "rice cake" in normalizedprompt
        and "rolling and rapid" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The constipation, abdominal pain, irritability and low fever show Heat and "
            "stagnation binding in the Yangming bowel. A red tongue with a dry yellow coat "
            "and a rolling, rapid pulse support interior Heat with accumulation. The case "
            "does not describe the severe guarding, rock-hard mass, pronounced distention "
            "or deep forceful pulse required for Da Cheng Qi Tang, so the more moderate "
            "purgation of Xiao Cheng Qi Tang is the better match."
        )
        cleaned["caseReasoningEs"] = (
            "El estreñimiento, el dolor abdominal, la irritabilidad y la fiebre baja muestran "
            "Calor y estancamiento ligados en el intestino Yangming. La lengua roja con capa "
            "amarilla seca y el pulso rodante y rápido apoyan Calor interno con acumulación. "
            "El caso no presenta la defensa grave, la masa pétrea, la distensión pronunciada "
            "ni el pulso profundo y fuerte que exigirían Da Cheng Qi Tang; por eso corresponde "
            "la purgación más moderada de Xiao Cheng Qi Tang."
        )
    elif (
        profilename == "tiao wei cheng qi tang"
        and "toothache" in normalizedprompt
        and "foul halitosis" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The toothache, swollen gums, intense thirst and foul breath locate exuberant "
            "Heat in the Stomach/Yangming system. Constipation, dark urine, a red tongue "
            "with yellow coat and a rolling, rapid pulse show that the Heat is consuming "
            "fluids and creating bowel dryness. Because the case lacks marked abdominal "
            "fullness, focal distention or a hard painful mass, the mild Heat-purging and "
            "dryness-softening action of Tiao Wei Cheng Qi Tang fits better than the stronger "
            "Cheng Qi formulas."
        )
        cleaned["caseReasoningEs"] = (
            "El dolor dental, las encías inflamadas, la sed intensa y la halitosis ubican "
            "Calor exuberante en Estómago/Yangming. El estreñimiento, la orina oscura, la "
            "lengua roja con capa amarilla y el pulso rodante y rápido muestran que el Calor "
            "consume líquidos y produce sequedad intestinal. Como no hay plenitud abdominal "
            "marcada, distensión focal ni masa dura y dolorosa, la acción suave de Tiao Wei "
            "Cheng Qi Tang para purgar Calor y ablandar sequedad encaja mejor que las fórmulas "
            "Cheng Qi más fuertes."
        )
    elif (
        profilename == "huo xiang zheng qi san"
        and "child-care facility" in normalizedprompt
        and "diarrhea and vomiting" in normalizedprompt
    ):
        cleaned["caseReasoning"] = (
            "The acute diarrhea, vomiting, nausea, epigastric bloating, flatulence and loss "
            "of appetite show Dampness obstructing the Middle Burner and disrupting the "
            "ascending and descending of Spleen-Stomach Qi. Feeling feverish with headache "
            "adds an exterior component. Huo Xiang Zheng Qi San fits this combined pattern "
            "because it releases the Exterior while transforming Dampness, regulating Qi "
            "and harmonizing the Middle."
        )
        cleaned["caseReasoningEs"] = (
            "La diarrea y el vómito agudos, la náusea, la distensión epigástrica, la "
            "flatulencia y la pérdida de apetito muestran Humedad obstruyendo el Jiao Medio "
            "y alterando el ascenso y descenso del Qi de Bazo-Estómago. La sensación febril "
            "con cefalea añade un componente exterior. Huo Xiang Zheng Qi San corresponde "
            "a este patrón combinado porque libera el Exterior mientras transforma Humedad, "
            "regula Qi y armoniza el Centro."
        )

    if (
        "caseReasoning" not in cleaned
        and cleaned.get("pattern")
        and cleaned.get("clue")
        and cleaned.get("action")
    ):
        cleaned["caseReasoning"] = (
            f"The case is best identified as {cleaned['pattern']} "
            f"The decisive pattern markers are {cleaned['clue']} "
            f"The therapeutic match is {profile.get('name')}: {cleaned['action']}"
        )
    if (
        "caseReasoningEs" not in cleaned
        and cleaned.get("patternEs")
        and cleaned.get("clueEs")
        and cleaned.get("actionEs")
    ):
        cleaned["caseReasoningEs"] = (
            f"El caso se identifica mejor como {cleaned['patternEs']} "
            f"Los marcadores decisivos son {cleaned['clueEs']} "
            f"La correspondencia terapéutica es {profile.get('name')}: {cleaned['actionEs']}"
        )

    alternatives = []
    for option in options or []:
        if option == correctoption:
            continue
        alternative = match_profile(option)
        if alternative is None:
            continue
        alternativefields = {
            "formula": alternative.get("name"),
            "pattern": alternative.get("pattern"),
            "patternEs": alternative.get("patternEs"),
            "clue": alternative.get("clue"),
            "clueEs": alternative.get("clueEs"),
        }
        alternatives.append({
            key: source_text(value)
            for key, value in alternativefields.items()
            if source_text(value)
        })
    if alternatives:
        cleaned["alternatives"] = alternatives
    return cleaned or None


def is_clinical_case(record: dict[str, Any]) -> bool:
    course = normalise_name(record.get("coursename"))
    section = normalise_name(record.get("sectionname"))
    exam = normalise_name(record.get("scormname"))
    exact = course == TARGET_COURSE and section == TARGET_TOPIC
    fallback = "clinical cases" in section and (
        "handbook" in section or "clinical" in exam
    )
    return exact or fallback


def feedback_text(question: dict[str, Any]) -> str:
    values: list[str] = []
    for item in question.get("Feedback") or []:
        value = source_text(answer_text(item))
        if value and value not in values:
            values.append(value)
    if not values:
        notes = source_text(question.get("QuestionNotes"))
        if notes:
            values.append(notes)
    return "\n\n".join(values)


def correct_option_index(question: dict[str, Any], options: list[str]) -> int | None:
    sourceoptions = question.get("Answers") or []
    for index, item in enumerate(sourceoptions):
        if isinstance(item, dict) and bool(item.get("Correct")):
            return index

    rightanswer = question.get("RightAnswer")
    if isinstance(rightanswer, int) and 0 <= rightanswer < len(options):
        return rightanswer
    righttext = source_text(answer_text(rightanswer)).casefold()
    if not righttext:
        return None
    if len(righttext) == 1 and "a" <= righttext <= "z":
        labelindex = ord(righttext) - ord("a")
        if labelindex < len(options):
            return labelindex
    for index, option in enumerate(options):
        if option.casefold() == righttext:
            return index
    for index, item in enumerate(sourceoptions):
        if isinstance(item, dict) and normalise_name(item.get("Id")) == righttext:
            return index
    if righttext.isdigit():
        number = int(righttext)
        if 1 <= number <= len(options):
            return number - 1
        if number == 0:
            return 0
    return None


def extract_question(
    question: dict[str, Any],
    examkey: str,
    questionnumber: int,
    formulaprofiles: dict[str, dict[str, Any]] | None = None,
) -> dict[str, Any] | None:
    prompt = source_text(question.get("QuestionText"))
    sourceoptions = question.get("Answers") or []
    if not prompt or not isinstance(sourceoptions, list):
        return None
    options = [source_text(answer_text(item)) for item in sourceoptions]
    if len(options) < 2 or any(not option for option in options):
        return None
    feedback = feedback_text(question)
    # These Handbook packages store later paragraphs of the clinical vignette
    # in Feedback. They are part of the stem and must be shown before options.
    if feedback:
        compactfeedback = re.sub(r"\s+", " ", feedback).lstrip()
        prompt = f"{prompt.rstrip()} {compactfeedback}"
        feedback = ""
    if (
        SUSPICIOUS_EMBEDDED_DIGIT.search(prompt)
        or SUSPICIOUS_EMBEDDED_DIGIT.search(feedback)
        or any(
            SUSPICIOUS_EMBEDDED_DIGIT.search(option) for option in options
        )
    ):
        return None
    correctindex = correct_option_index(question, options)
    if correctindex is None or correctindex >= len(options):
        return None
    normalizedcorrect = normalise_name(options[correctindex])
    normalizedprompt = normalise_name(prompt)
    if (
        normalizedcorrect.startswith("huo xiang zheng qi san ")
        and "child-care facility" in normalizedprompt
        and "diarrhea and vomiting" in normalizedprompt
    ):
        prompt = (
            "CASE: Tiffany, a 40-year-old woman, presents with recent diarrhea and vomiting "
            "after working in a child-care facility. She also feels feverish and reports "
            "epigastric bloating, nausea, flatulence, loss of appetite and headache."
        )
    labels = [chr(65 + index) if index < 26 else str(index + 1) for index in range(len(options))]
    fingerprint = hashlib.sha256(
        (prompt + "\n" + "\n".join(options)).encode("utf-8")
    ).hexdigest()[:16]
    extracted = {
        "id": f"{examkey}-q{questionnumber}-{fingerprint}",
        "number": questionnumber,
        "type": str(question.get("__type") or "MultipleChoiceQuestion"),
        "prompt": prompt,
        "options": [
            {"label": label, "text": text}
            for label, text in zip(labels, options)
        ],
        "correctLabel": labels[correctindex],
        "feedback": feedback,
    }
    diagnosis = diagnostic_feedback(
        options[correctindex],
        formulaprofiles or {},
        prompt,
        options,
    )
    if diagnosis is not None:
        extracted["diagnosis"] = diagnosis
    return extracted


def exam_number(value: str) -> int:
    match = re.search(r"\bexam\s*(\d+)\b", value, re.IGNORECASE)
    return int(match.group(1)) if match else 9999


def exam_key(value: str, contenthash: str) -> str:
    number = exam_number(value)
    if number != 9999:
        return f"exam-{number}"
    slug = re.sub(r"[^a-z0-9]+", "-", value.casefold()).strip("-") or "exam"
    return f"{slug[:48]}-{contenthash[:8]}"


def load_quiz(packagepath: Path) -> list[Any]:
    with zipfile.ZipFile(packagepath, "r") as package:
        member = find_quizdata_member(package)
        if not member:
            raise ValueError("QUIZDATA.json not found")
        quizdata = json.loads(package.read(member).decode("utf-8-sig"))
    questions = quizdata.get("Questions")
    if not isinstance(questions, list):
        raise ValueError("QUIZDATA.json has no Questions array")
    return questions


def unique_key(basekey: str, used: set[str]) -> str:
    if basekey not in used:
        used.add(basekey)
        return basekey
    suffix = 2
    while f"{basekey}-set-{suffix}" in used:
        suffix += 1
    key = f"{basekey}-set-{suffix}"
    used.add(key)
    return key


def build_bank(
    inventory: dict[str, Any],
    courses: list[int],
    formulaprofiles: dict[str, dict[str, Any]],
) -> dict[str, Any]:
    dataroot = Path(str(inventory["dataroot"])).resolve()
    records = [record for record in inventory.get("records") or [] if is_clinical_case(record)]
    grouped = choose_placements(records, courses)
    exams: list[dict[str, Any]] = []
    errors: list[dict[str, str]] = []
    usedkeys: set[str] = set()
    signatures: set[str] = set()

    for contenthash, placements in grouped.items():
        placement = placements[0]
        title = str(placement.get("scormname") or "Clinical Cases Exam")
        try:
            sourcequestions = load_quiz(package_path(dataroot, contenthash))
            signature = hashlib.sha256(
                json.dumps(sourcequestions, ensure_ascii=False, sort_keys=True).encode("utf-8")
            ).hexdigest()
            if signature in signatures:
                continue
            signatures.add(signature)
            key = unique_key(exam_key(title, contenthash), usedkeys)
            questions = []
            skipped = 0
            for number, sourcequestion in enumerate(sourcequestions, start=1):
                if not isinstance(sourcequestion, dict):
                    skipped += 1
                    continue
                question = extract_question(sourcequestion, key, number, formulaprofiles)
                if question is None:
                    skipped += 1
                    continue
                questions.append(question)
            if questions:
                exams.append({
                    "key": key,
                    "title": title,
                    "number": exam_number(title),
                    "questionCount": len(questions),
                    "sourceQuestionCount": len(sourcequestions),
                    "skippedQuestionCount": skipped,
                    "questions": questions,
                })
        except (OSError, ValueError, json.JSONDecodeError, zipfile.BadZipFile) as error:
            errors.append({"exam": title, "error": str(error)})

    exams.sort(key=lambda item: (int(item["number"]), str(item["title"]).casefold(), str(item["key"])))
    for item in exams:
        if item["number"] == 9999:
            item["number"] = None
    return {
        "schemaVersion": 1,
        "source": "Handbook of Oriental Medicine — Clinical Cases",
        "sourceReadOnly": True,
        "examCount": len(exams),
        "questionCount": sum(int(exam["questionCount"]) for exam in exams),
        "diagnosticFeedbackCount": sum(
            1
            for exam in exams
            for question in exam["questions"]
            if question.get("diagnosis")
        ),
        "errors": errors,
        "exams": exams,
    }


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--courses", default=",".join(str(item) for item in DEFAULT_COURSES))
    parser.add_argument("--php-bin", default="php")
    parser.add_argument("--inventory", type=Path)
    parser.add_argument("--formula-data", type=Path, default=DEFAULT_FORMULA_DATA)
    parser.add_argument("--output", type=Path)
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    courses = [int(value.strip()) for value in args.courses.split(",") if value.strip().isdigit()]
    if not courses:
        raise SystemExit("At least one valid course ID is required.")
    if args.inventory:
        inventory = json.loads(args.inventory.read_text(encoding="utf-8"))
    else:
        inventory = load_inventory(args.php_bin, courses)
    try:
        formulaprofiles = load_formula_profiles(args.formula_data.resolve())
    except (OSError, ValueError, json.JSONDecodeError) as error:
        raise SystemExit(f"Unable to load formula diagnostic profiles: {error}") from error
    bank = build_bank(inventory, courses, formulaprofiles)
    if not bank["exams"] or not bank["questionCount"]:
        raise SystemExit("No usable Handbook clinical-case questions were found.")
    if bank["diagnosticFeedbackCount"] != bank["questionCount"]:
        missing = bank["questionCount"] - bank["diagnosticFeedbackCount"]
        raise SystemExit(
            f"Diagnostic feedback is missing for {missing} clinical-case question(s); "
            "the existing bank was not replaced."
        )
    dataroot = Path(str(inventory["dataroot"])).resolve()
    output = args.output.resolve() if args.output else (
        dataroot / "board-domain-banks" / "herbology-clinical-cases.json"
    )
    output.parent.mkdir(parents=True, exist_ok=True)
    with tempfile.NamedTemporaryFile(
        "w", encoding="utf-8", dir=output.parent, delete=False, prefix=f".{output.name}-"
    ) as handle:
        json.dump(bank, handle, ensure_ascii=False, indent=2)
        handle.write("\n")
        temporary = Path(handle.name)
    temporary.replace(output)
    print(json.dumps({
        "output": str(output),
        "exams": bank["examCount"],
        "questions": bank["questionCount"],
        "diagnosticFeedback": bank["diagnosticFeedbackCount"],
        "errors": len(bank["errors"]),
    }, ensure_ascii=False))
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except subprocess.CalledProcessError as error:
        print(error.stderr or str(error))
        raise SystemExit(error.returncode or 1)
