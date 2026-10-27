#!/usr/bin/env python3
"""Audit a Word question bank and stage its explicitly keyed questions.

The source document uses ``+++++`` beside an answer to identify the key.  This
tool deliberately refuses to infer answers from feedback or from clinical
wording.  Questions without exactly one explicit key are written to a review
queue instead of the Moodle XML file.
"""

from __future__ import annotations

import argparse
import hashlib
import html
import json
import re
import sys
import zipfile
from collections import Counter
from dataclasses import asdict, dataclass
from pathlib import Path
from typing import Iterable, Sequence
from xml.etree import ElementTree


WORD_NAMESPACE = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
QUESTION_RE = re.compile(r"^(\d+)\.\s+([\s\S]+)$")
OPTION_RE = re.compile(r"^\s*(?:\(([A-Z])\)|([A-Z])[.)])\s*([\s\S]*)$")
FEEDBACK_RE = re.compile(r"^\s*Feedback:\s*([\s\S]*)$", re.IGNORECASE)
ANSWER_MARKER = "+++++"


@dataclass
class Option:
    label: str
    text: str
    explicitly_keyed: bool


@dataclass
class Question:
    source_number: int
    classification: str
    prompt: str
    body: list[str]
    options: list[Option]
    feedback: list[str]
    correct_labels: list[str]


def extract_docx_paragraphs(path: Path) -> list[str]:
    """Return visible paragraph text in document order from a DOCX file."""

    if not path.is_file():
        raise ValueError(f"Source file does not exist: {path}")
    if not zipfile.is_zipfile(path):
        raise ValueError(f"Source is not a valid DOCX/ZIP file: {path}")

    try:
        with zipfile.ZipFile(path) as archive:
            document_xml = archive.read("word/document.xml")
    except KeyError as exc:
        raise ValueError("Source DOCX has no word/document.xml") from exc

    root = ElementTree.fromstring(document_xml)
    namespace = {"w": WORD_NAMESPACE}
    paragraphs: list[str] = []
    for paragraph in root.findall(".//w:p", namespace):
        text = "".join(
            node.text or "" for node in paragraph.findall(".//w:t", namespace)
        ).strip()
        if text:
            paragraphs.append(text)
    return paragraphs


def _clean_text(value: str) -> str:
    value = value.replace(ANSWER_MARKER, "")
    for _ in range(3):
        decoded = html.unescape(value)
        if decoded == value:
            break
        value = decoded
    return re.sub(r"[ \t]+", " ", value).strip()


def _deduplicate_nonempty(values: Iterable[str]) -> list[str]:
    result: list[str] = []
    seen: set[str] = set()
    for value in values:
        cleaned = value.strip()
        if cleaned and cleaned not in seen:
            result.append(cleaned)
            seen.add(cleaned)
    return result


def parse_questions(paragraphs: Sequence[str]) -> list[Question]:
    """Parse numbered Word paragraphs into auditable question records."""

    raw_questions: list[tuple[int, str, list[str]]] = []
    current: tuple[int, str, list[str]] | None = None

    for paragraph in paragraphs:
        match = QUESTION_RE.match(paragraph)
        if match:
            if current is not None:
                raw_questions.append(current)
            current = (int(match.group(1)), match.group(2).strip(), [])
        elif current is not None:
            current[2].append(paragraph.strip())

    if current is not None:
        raw_questions.append(current)
    if not raw_questions:
        raise ValueError("No numbered questions were found in the source document")

    numbers = [record[0] for record in raw_questions]
    expected = list(range(numbers[0], numbers[-1] + 1))
    if numbers != expected:
        raise ValueError(
            "Question numbering is not continuous and unique: "
            f"found {len(numbers)} items from {numbers[0]} to {numbers[-1]}"
        )

    questions: list[Question] = []
    for number, prompt, lines in raw_questions:
        body: list[str] = []
        feedback: list[str] = []
        options: list[Option] = []
        current_option: Option | None = None
        in_feedback = False

        for line in lines:
            feedback_match = FEEDBACK_RE.match(line)
            if feedback_match:
                in_feedback = True
                current_option = None
                feedback.append(feedback_match.group(1).strip())
                continue

            option_match = OPTION_RE.match(line) if not in_feedback else None
            if option_match:
                option_text = option_match.group(3)
                current_option = Option(
                    label=option_match.group(1) or option_match.group(2),
                    text=_clean_text(option_text),
                    explicitly_keyed=ANSWER_MARKER in option_text,
                )
                options.append(current_option)
                continue

            if in_feedback:
                feedback.append(line)
            elif current_option is not None:
                current_option.explicitly_keyed = (
                    current_option.explicitly_keyed or ANSWER_MARKER in line
                )
                current_option.text = _clean_text(f"{current_option.text} {line}")
            else:
                body.append(line)

        correct_labels = [option.label for option in options if option.explicitly_keyed]
        if len(options) >= 2 and len(correct_labels) == 1:
            classification = "keyed_multiple_choice"
        elif len(options) >= 2 and not correct_labels:
            classification = "unkeyed_multiple_choice"
        elif len(options) >= 2:
            classification = "ambiguous_multiple_choice"
        else:
            classification = "reference_or_identification"

        questions.append(
            Question(
                source_number=number,
                classification=classification,
                prompt=_clean_text(prompt),
                body=_deduplicate_nonempty(body),
                options=options,
                feedback=_deduplicate_nonempty(feedback),
                correct_labels=correct_labels,
            )
        )

    return questions


def _question_record(question: Question) -> dict[str, object]:
    return asdict(question)


def _write_jsonl(path: Path, questions: Iterable[Question]) -> None:
    with path.open("w", encoding="utf-8") as handle:
        for question in questions:
            json.dump(_question_record(question), handle, ensure_ascii=False)
            handle.write("\n")


def _html_paragraphs(values: Iterable[str]) -> str:
    parts: list[str] = []
    for value in values:
        for paragraph in re.split(r"\n\s*\n", value):
            cleaned = paragraph.strip()
            if cleaned:
                parts.append(f"<p>{html.escape(cleaned)}</p>")
    return "".join(parts)


def build_moodle_xml(questions: Sequence[Question], category: str) -> ElementTree.Element:
    """Build Moodle XML containing only explicitly and uniquely keyed MCQs."""

    keyed = [
        question
        for question in questions
        if question.classification == "keyed_multiple_choice"
    ]
    quiz = ElementTree.Element("quiz")
    category_question = ElementTree.SubElement(quiz, "question", {"type": "category"})
    category_element = ElementTree.SubElement(category_question, "category")
    ElementTree.SubElement(category_element, "text").text = category

    for question in keyed:
        item = ElementTree.SubElement(quiz, "question", {"type": "multichoice"})
        name = ElementTree.SubElement(item, "name")
        ElementTree.SubElement(name, "text").text = (
            f"Complete Final Q{question.source_number:04d}"
        )
        question_text = ElementTree.SubElement(
            item, "questiontext", {"format": "html"}
        )
        ElementTree.SubElement(question_text, "text").text = _html_paragraphs(
            [question.prompt, *question.body]
        )
        general_feedback = ElementTree.SubElement(
            item, "generalfeedback", {"format": "html"}
        )
        ElementTree.SubElement(general_feedback, "text").text = _html_paragraphs(
            question.feedback
        )
        ElementTree.SubElement(item, "defaultgrade").text = "1.0000000"
        ElementTree.SubElement(item, "penalty").text = "0.3333333"
        ElementTree.SubElement(item, "hidden").text = "0"
        ElementTree.SubElement(item, "idnumber").text = (
            f"complete-final-{question.source_number:04d}"
        )
        ElementTree.SubElement(item, "single").text = "true"
        ElementTree.SubElement(item, "shuffleanswers").text = "true"
        ElementTree.SubElement(item, "answernumbering").text = "ABCD"
        ElementTree.SubElement(item, "showstandardinstruction").text = "0"

        correct_label = question.correct_labels[0]
        for option in question.options:
            answer = ElementTree.SubElement(
                item,
                "answer",
                {
                    "fraction": "100" if option.label == correct_label else "0",
                    "format": "html",
                },
            )
            ElementTree.SubElement(answer, "text").text = html.escape(option.text)
            answer_feedback = ElementTree.SubElement(
                answer, "feedback", {"format": "html"}
            )
            ElementTree.SubElement(answer_feedback, "text").text = ""

    return quiz


def _write_moodle_xml(path: Path, questions: Sequence[Question], category: str) -> None:
    root = build_moodle_xml(questions, category)
    ElementTree.indent(root, space="  ")
    ElementTree.ElementTree(root).write(
        path, encoding="utf-8", xml_declaration=True, short_empty_elements=True
    )


def _source_sha256(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        for block in iter(lambda: handle.read(1024 * 1024), b""):
            digest.update(block)
    return digest.hexdigest()


def stage_import(source: Path, output: Path, category: str) -> dict[str, object]:
    if output.exists():
        raise ValueError(f"Output path already exists: {output}")

    questions = parse_questions(extract_docx_paragraphs(source))
    counts = Counter(question.classification for question in questions)
    keyed = [q for q in questions if q.classification == "keyed_multiple_choice"]
    review = [q for q in questions if q.classification != "keyed_multiple_choice"]

    output.mkdir(parents=True)
    _write_jsonl(output / "keyed-questions.jsonl", keyed)
    _write_jsonl(output / "review-required.jsonl", review)
    _write_moodle_xml(output / "complete-final-keyed-moodle.xml", questions, category)

    audit: dict[str, object] = {
        "source": {
            "filename": source.name,
            "sha256": _source_sha256(source),
        },
        "question_range": [questions[0].source_number, questions[-1].source_number],
        "total_questions": len(questions),
        "counts": dict(sorted(counts.items())),
        "moodle_xml_questions": len(keyed),
        "review_required": len(review),
        "answer_policy": (
            "Only an option containing the explicit +++++ source marker is keyed. "
            "No answer is inferred from feedback or clinical wording."
        ),
        "artifacts": {
            "moodle_xml": "complete-final-keyed-moodle.xml",
            "keyed_jsonl": "keyed-questions.jsonl",
            "review_jsonl": "review-required.jsonl",
        },
    }
    with (output / "audit.json").open("w", encoding="utf-8") as handle:
        json.dump(audit, handle, ensure_ascii=False, indent=2)
        handle.write("\n")

    markdown = [
        "# Complete Final import audit",
        "",
        f"- Source: `{source.name}`",
        f"- SHA-256: `{audit['source']['sha256']}`",  # type: ignore[index]
        f"- Numbered items: **{len(questions)}**",
        f"- Moodle-ready keyed multiple choice: **{len(keyed)}**",
        f"- Held for review: **{len(review)}**",
        "",
        "## Classification",
        "",
    ]
    for classification, count in sorted(counts.items()):
        markdown.append(f"- `{classification}`: {count}")
    markdown.extend(
        [
            "",
            "## Safety gate",
            "",
            "Only answers marked `+++++` in the source are included in Moodle XML. ",
            "Unkeyed choices and reference/identification records remain in the review queue. ",
            "No clinical answer was inferred from feedback.",
            "",
        ]
    )
    (output / "audit.md").write_text("\n".join(markdown), encoding="utf-8")
    return audit


def parse_args(argv: Sequence[str] | None = None) -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description=(
            "Audit Complete Final.docx and export only explicitly keyed MCQs "
            "as Moodle XML."
        )
    )
    parser.add_argument("source", type=Path, help="source .docx file")
    parser.add_argument("--output", required=True, type=Path, help="new output directory")
    parser.add_argument(
        "--category",
        default="$course$/top/Complete Final/Explicitly keyed",
        help="Moodle question-bank category path",
    )
    return parser.parse_args(argv)


def main(argv: Sequence[str] | None = None) -> int:
    args = parse_args(argv)
    try:
        audit = stage_import(args.source.resolve(), args.output.resolve(), args.category)
    except (OSError, ValueError, ElementTree.ParseError, zipfile.BadZipFile) as exc:
        print(f"Error: {exc}", file=sys.stderr)
        return 1

    print(
        f"Staged {audit['moodle_xml_questions']} explicitly keyed questions; "
        f"held {audit['review_required']} items for review in {args.output}."
    )
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
