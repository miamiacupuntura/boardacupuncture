#!/usr/bin/env python3
"""Extract the Handbook clinical-case SCORM questions into a Quiz JSON bank.

The Moodle database and file pool are read-only. Only the requested JSON output
is created or atomically replaced.
"""

from __future__ import annotations

import argparse
import hashlib
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
DEFAULT_COURSES = [45, 76, 41, 82]
TARGET_COURSE = "herbal comp review module exams"
TARGET_TOPIC = "handbook-of-oriental-medicine. clinical cases"


def normalise_name(value: Any) -> str:
    return re.sub(r"\s+", " ", str(value or "").strip()).casefold()


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
        value = answer_text(item)
        if value and value not in values:
            values.append(value)
    if not values:
        notes = normalise_text(question.get("QuestionNotes"))
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
    righttext = answer_text(rightanswer).casefold()
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
) -> dict[str, Any] | None:
    prompt = normalise_text(question.get("QuestionText"))
    sourceoptions = question.get("Answers") or []
    if not prompt or not isinstance(sourceoptions, list):
        return None
    options = [answer_text(item) for item in sourceoptions]
    if len(options) < 2 or any(not option for option in options):
        return None
    correctindex = correct_option_index(question, options)
    if correctindex is None or correctindex >= len(options):
        return None
    labels = [chr(65 + index) if index < 26 else str(index + 1) for index in range(len(options))]
    fingerprint = hashlib.sha256(
        (prompt + "\n" + "\n".join(options)).encode("utf-8")
    ).hexdigest()[:16]
    return {
        "id": f"{examkey}-q{questionnumber}-{fingerprint}",
        "number": questionnumber,
        "type": str(question.get("__type") or "MultipleChoiceQuestion"),
        "prompt": prompt,
        "options": [
            {"label": label, "text": text}
            for label, text in zip(labels, options)
        ],
        "correctLabel": labels[correctindex],
        "feedback": feedback_text(question),
    }


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


def build_bank(inventory: dict[str, Any], courses: list[int]) -> dict[str, Any]:
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
                question = extract_question(sourcequestion, key, number)
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
        "errors": errors,
        "exams": exams,
    }


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--courses", default=",".join(str(item) for item in DEFAULT_COURSES))
    parser.add_argument("--php-bin", default="php")
    parser.add_argument("--inventory", type=Path)
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
    bank = build_bank(inventory, courses)
    if not bank["exams"] or not bank["questionCount"]:
        raise SystemExit("No usable Handbook clinical-case questions were found.")
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
        "errors": len(bank["errors"]),
    }, ensure_ascii=False))
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except subprocess.CalledProcessError as error:
        print(error.stderr or str(error))
        raise SystemExit(error.returncode or 1)
