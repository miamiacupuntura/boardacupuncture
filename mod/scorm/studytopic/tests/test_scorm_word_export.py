#!/usr/bin/env python3
"""Tests for the dependency-free SCORM Word exporter."""

from __future__ import annotations

import importlib.util
import json
import tempfile
import unittest
import zipfile
from pathlib import Path
from xml.etree import ElementTree


SCRIPT = Path(__file__).resolve().parents[1] / "scorm_word_export.py"
SPEC = importlib.util.spec_from_file_location("scorm_word_export", SCRIPT)
assert SPEC and SPEC.loader
EXPORTER = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(EXPORTER)


ONE_PIXEL_PNG = (
    b"\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01"
    b"\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15\xc4\x89"
    b"\x00\x00\x00\rIDAT\x08\xd7c\xf8\xcf\xc0\xf0\x1f\x00\x05"
    b"\x00\x01\xff\x89\x99=\x1d\x00\x00\x00\x00IEND\xaeB`\x82"
)


class ScormWordExportTest(unittest.TestCase):
    def test_build_document_creates_valid_ooxml_with_answer_and_feedback(self) -> None:
        quizdata = {
            "Questions": [
                {
                    "__type": "MultipleChoiceQuestion",
                    "QuestionText": "Which formula is correct?",
                    "Answers": ["Formula A", "Formula B"],
                    "RightAnswer": "Formula B",
                    "Feedback": ["Correct feedback", "Incorrect feedback"],
                    "ImageURL": "images/question.png",
                },
                {
                    "__type": "MatchingQuestion",
                    "QuestionText": "Match each herb.",
                    "LeftColumn": [{"Id": 1, "Content": "Herb A"}],
                    "RightColumn": [{"Id": 1, "Content": "Function A"}],
                    "Feedback": [],
                },
            ]
        }
        placement = {
            "scormname": "Formula Mock Exam",
            "coursename": "Formulas Exams",
            "sectionname": "Practice Test 01",
            "cmid": 123,
        }

        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            package = root / "package.zip"
            document = root / "exam.docx"
            with zipfile.ZipFile(package, "w") as archive:
                archive.writestr("QUIZDATA.json", json.dumps(quizdata))
                archive.writestr("images/question.png", ONE_PIXEL_PNG)

            count = EXPORTER.build_document(package, document, placement)
            self.assertEqual(2, count)
            self.assertTrue(document.is_file())

            with zipfile.ZipFile(document) as archive:
                names = set(archive.namelist())
                self.assertIn("word/document.xml", names)
                self.assertIn("word/media/image1.png", names)
                xml = archive.read("word/document.xml").decode("utf-8")
                self.assertIn("Formula Mock Exam", xml)
                self.assertIn("Correct answer: Formula B", xml)
                self.assertIn("Correct feedback", xml)
                self.assertIn("Herb A", xml)
                self.assertIn("Function A", xml)
                self.assertIn("Match 1", xml)
                self.assertIn("<w:tbl>", xml)
                self.assertIn(">Item</w:t>", xml)
                self.assertIn(">Correct match</w:t>", xml)
                for name in names:
                    if name.endswith(".xml") or name.endswith(".rels"):
                        ElementTree.fromstring(archive.read(name))

    def test_choose_placements_deduplicates_activity_versions_and_prefers_course_order(self) -> None:
        records = [
            {"courseid": 44, "cmid": 10, "contenthash": "a" * 40, "timemodified": 1},
            {"courseid": 44, "cmid": 10, "contenthash": "b" * 40, "timemodified": 2},
            {"courseid": 117, "cmid": 20, "contenthash": "b" * 40, "timemodified": 3},
        ]
        grouped = EXPORTER.choose_placements(records, [117, 44])
        self.assertNotIn("a" * 40, grouped)
        self.assertEqual(117, int(grouped["b" * 40][0]["courseid"]))
        self.assertEqual(2, len(grouped["b" * 40]))

    def test_duplicate_feedback_uses_related_mock_question_to_explain_distractor(self) -> None:
        questions = [
            {
                "QuestionText": "What is the modification for cough?",
                "Answers": ["Add Herb A", "Add Herb B"],
                "RightAnswer": "Add Herb A",
            },
            {
                "QuestionText": "What is the modification for palpitation?",
                "Answers": ["Add Herb A", "Add Herb B"],
                "RightAnswer": "Add Herb B",
            },
        ]
        EXPORTER.annotate_related_answer_targets(questions)

        related = questions[0]["__related_answer_targets"]
        self.assertEqual("cough", related["add herb a"])
        self.assertEqual("palpitation", related["add herb b"])

    def test_missing_feedback_still_produces_transparent_mock_guidance(self) -> None:
        question = {
            "QuestionText": "What are the ingredients of Formula A?",
            "Answers": ["Ingredient set A", "Ingredient set B"],
            "RightAnswer": "Ingredient set A",
        }
        builder = EXPORTER.DocxBuilder("Test")
        EXPORTER.add_missing_source_feedback(builder, question)
        xml = "".join(builder.body)

        self.assertIn("Why the correct answer is correct", xml)
        self.assertIn("the ingredients of Formula A", xml)
        self.assertIn("does not contain a more detailed rationale", xml)
        self.assertIn("Why the other options are incorrect", xml)
        self.assertIn("No option-specific rationale was stored", xml)

    def test_fill_in_blank_feedback_uses_accepted_answers(self) -> None:
        question = {
            "__type": "FillInTheBlankQuestion",
            "QuestionText": "Name the formula: ____",
            "Answers": ["Formula A", "Formula A Decoction"],
            "Feedback": [],
        }
        builder = EXPORTER.DocxBuilder("Test")
        EXPORTER.add_feedback(builder, question)
        xml = "".join(builder.body)

        self.assertIn("Why the accepted answer is correct", xml)
        self.assertIn("shown under Accepted answer(s)", xml)
        self.assertNotIn("Formula A; Formula A Decoction", xml)
        self.assertIn("How other responses are evaluated", xml)
        self.assertIn("matches one of the accepted answers", xml)

    def test_matching_feedback_uses_keyed_pairs(self) -> None:
        question = {
            "__type": "MatchingQuestion",
            "QuestionText": "Match each herb to its function.",
            "LeftColumn": [
                {"Id": 1, "Content": "Herb A"},
                {"Id": 2, "Content": "Herb B"},
            ],
            "RightColumn": [
                {"Id": 1, "Content": "Function A"},
                {"Id": 2, "Content": "Function B"},
            ],
            "Feedback": [],
        }
        builder = EXPORTER.DocxBuilder("Test")
        EXPORTER.add_feedback(builder, question)
        xml = "".join(builder.body)

        self.assertIn("Why these matches are correct", xml)
        self.assertIn("the matching exercise", xml)
        self.assertIn("source key distinguishes the items", xml)
        self.assertIn("Match 1 — Herb A", xml)
        self.assertIn("1 keyed association", xml)
        self.assertNotIn("Herb A → Function A", xml)
        self.assertNotIn("Herb B → Function B", xml)
        self.assertIn("How incorrect matches are evaluated", xml)
        self.assertIn("Interchanging the groups for Herb A and Herb B", xml)

    def test_long_duplicate_matching_reference_list_is_not_rendered(self) -> None:
        question = {
            "__type": "MatchingQuestion",
            "QuestionText": "Comparison Herb A Vs. Herb B\nMatch each herb.",
            "LeftColumn": [
                {"Id": 1, "Content": "Herb A\nCategory: Warm interior herbs"}
            ],
            "RightColumn": [
                {"Id": 1, "Content": "Formulas:\nFormula A\nFormula B"}
            ],
            "Feedback": ["Long source " * 150, "Long source " * 150],
        }
        builder = EXPORTER.DocxBuilder("Test")
        EXPORTER.add_feedback(builder, question)
        xml = "".join(builder.body)

        self.assertNotIn("Detailed source review", xml)
        self.assertNotIn("Long source", xml)
        self.assertIn("Comparison Herb A Vs. Herb B", xml)
        self.assertIn("classified as Warm interior herbs", xml)
        self.assertIn("2 formula associations", xml)
        self.assertNotIn("Herb A → Function A", xml)

    def test_matching_clues_are_separated_into_a_study_tip_card(self) -> None:
        prompt, tips = EXPORTER.matching_prompt_and_tips(
            "Comparison Herb A Vs. Herb B\n"
            "Both herbs settle nausea.\n"
            "Primary conditions or symptoms for which Herb A may be prescribed: "
            "Pain. Vomiting.\n"
            "Primary conditions or symptoms for which Herb B may be prescribed: "
            "Coughing. Phlegm."
        )

        self.assertEqual("Comparison Herb A Vs. Herb B", prompt)
        self.assertEqual("Shared clues", tips[0][0])
        self.assertEqual("Both herbs settle nausea.", tips[0][1])
        self.assertEqual("Herb A", tips[1][0])
        self.assertEqual("• Pain\n• Vomiting", tips[1][1])
        self.assertEqual("Herb B", tips[2][0])
        self.assertEqual("• Coughing\n• Phlegm", tips[2][1])

        comparison = EXPORTER.matching_tip_comparison_data(tips)
        self.assertIsNotNone(comparison)
        columns, rows = comparison
        self.assertEqual(["Herb A", "Herb B"], columns)
        self.assertEqual(("Shared clues", ["Both herbs settle nausea."]), rows[0])
        self.assertEqual("Symptoms / conditions", rows[1][0])
        self.assertEqual("• Pain\n• Vomiting", rows[1][1][0])
        self.assertEqual("• Coughing\n• Phlegm", rows[1][1][1])

    def test_two_matching_items_are_aligned_as_comparison_data(self) -> None:
        question = {
            "LeftColumn": [
                {"Id": 1, "Content": "Herb A\nCategory: Warm herbs"},
                {"Id": 2, "Content": "Herb B\nCategory: Cool herbs"},
            ],
            "RightColumn": [
                {"Id": 1, "Content": "Formulas:\nFormula A1\nFormula A2"},
                {"Id": 2, "Content": "Formulas:\nFormula B1"},
            ],
        }
        comparison = EXPORTER.matching_comparison_data(question)

        self.assertIsNotNone(comparison)
        columns, rows = comparison
        self.assertEqual(["Herb A", "Herb B"], columns)
        self.assertEqual("Category", rows[0][0])
        self.assertEqual(["Warm herbs", "Cool herbs"], rows[0][1])
        self.assertEqual("Formulas", rows[1][0])
        self.assertEqual("• Formula A1\n• Formula A2", rows[1][1][0])
        self.assertEqual("• Formula B1", rows[1][1][1])

    def test_safe_segment_removes_path_characters(self) -> None:
        self.assertEqual("Herbs - Final - Exam", EXPORTER.safe_segment("Herbs/Final:Exam", "Exam"))

    def test_clean_xml_text_removes_invalid_control_and_surrogate(self) -> None:
        self.assertEqual("Valid\ntext", EXPORTER.clean_xml_text("Valid\x00\ud800\ntext"))

    def test_export_category_creates_manifest_document_and_zip(self) -> None:
        contenthash = "1" * 40
        quizdata = {
            "Questions": [
                {
                    "__type": "FillInTheBlankQuestion",
                    "QuestionText": "Name the herb.",
                    "Answers": ["Herb A"],
                    "Feedback": ["Correct explanation", "Try again"],
                }
            ]
        }
        record = {
            "courseid": 45,
            "coursename": "Single Herbs Exams",
            "cmid": 55,
            "sectionnumber": 2,
            "sectionname": "Herbal Practice",
            "scormid": 77,
            "scormname": "Herb Exam 1",
            "packagefilename": "exam.zip",
            "contenthash": contenthash,
            "timemodified": 10,
        }

        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            dataroot = root / "moodledata"
            package = EXPORTER.package_path(dataroot, contenthash)
            package.parent.mkdir(parents=True)
            with zipfile.ZipFile(package, "w") as archive:
                archive.writestr("QUIZDATA.json", json.dumps(quizdata))

            output = root / "output"
            summary = EXPORTER.export_category(
                "Herbs", [record], [45], dataroot, output
            )

            self.assertEqual(1, summary["documents"])
            self.assertEqual(0, summary["errors"])
            self.assertEqual("Herbs_Exams_Word.zip", summary["zip"])
            self.assertTrue((output / summary["zip"]).is_file())
            manifest = json.loads(
                (output / "Herbs" / "manifest.json").read_text(encoding="utf-8")
            )
            self.assertEqual(1, manifest[0]["questions"])
            self.assertTrue((output / manifest[0]["docx"]).is_file())


if __name__ == "__main__":
    unittest.main()
