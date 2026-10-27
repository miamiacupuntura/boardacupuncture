#!/usr/bin/env python3
"""Tests for the staged Complete Final Word importer."""

from __future__ import annotations

import importlib.util
import sys
import tempfile
import unittest
import zipfile
from pathlib import Path
from xml.etree import ElementTree


SCRIPT = Path(__file__).resolve().parents[1] / "complete_final_import.py"
SPEC = importlib.util.spec_from_file_location("complete_final_import", SCRIPT)
assert SPEC and SPEC.loader
IMPORTER = importlib.util.module_from_spec(SPEC)
sys.modules[SPEC.name] = IMPORTER
SPEC.loader.exec_module(IMPORTER)


class CompleteFinalImportTest(unittest.TestCase):
    def test_parser_separates_explicit_keys_from_review_content(self) -> None:
        questions = IMPORTER.parse_questions(
            [
                "1. Which formula?",
                "A) Formula A +++++",
                "B) Formula B",
                "Feedback: Source rationale",
                "2. Identify the formula",
                "(A) English name",
                "(B) pinyin name",
                "Feedback: Both names are supplied for study.",
                "3. REFERENCE TABLE",
                "Formula A --- Pattern A",
                "Feedback:",
            ]
        )

        self.assertEqual(3, len(questions))
        self.assertEqual("keyed_multiple_choice", questions[0].classification)
        self.assertEqual(["A"], questions[0].correct_labels)
        self.assertNotIn("+++++", questions[0].options[0].text)
        self.assertEqual("unkeyed_multiple_choice", questions[1].classification)
        self.assertEqual([], questions[1].correct_labels)
        self.assertEqual("reference_or_identification", questions[2].classification)

    def test_parser_never_infers_answer_from_feedback(self) -> None:
        question = IMPORTER.parse_questions(
            [
                "1. Which formula?",
                "A) Formula A",
                "B) Formula B",
                "Feedback: Formula A is correct.",
            ]
        )[0]

        self.assertEqual("unkeyed_multiple_choice", question.classification)
        self.assertEqual([], question.correct_labels)

    def test_moodle_xml_contains_only_uniquely_keyed_questions(self) -> None:
        questions = IMPORTER.parse_questions(
            [
                "1. Which formula?",
                "A) Formula A +++++",
                "B) Formula B",
                "2. Which herb?",
                "A) Herb A",
                "B) Herb B",
            ]
        )
        root = IMPORTER.build_moodle_xml(questions, "$course$/top/Test")
        xml = ElementTree.tostring(root, encoding="unicode")

        self.assertEqual(1, len(root.findall("./question[@type='multichoice']")))
        self.assertIn("Complete Final Q0001", xml)
        self.assertNotIn("Complete Final Q0002", xml)
        answers = root.findall("./question[@type='multichoice']/answer")
        self.assertEqual(1, sum(a.get("fraction") == "100" for a in answers))

    def test_extract_docx_reads_visible_paragraphs(self) -> None:
        document_xml = """<?xml version="1.0" encoding="UTF-8"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body><w:p><w:r><w:t>1. Test question?</w:t></w:r></w:p></w:body>
</w:document>"""
        with tempfile.TemporaryDirectory() as directory:
            path = Path(directory) / "source.docx"
            with zipfile.ZipFile(path, "w") as archive:
                archive.writestr("word/document.xml", document_xml)

            self.assertEqual(
                ["1. Test question?"], IMPORTER.extract_docx_paragraphs(path)
            )


if __name__ == "__main__":
    unittest.main()
