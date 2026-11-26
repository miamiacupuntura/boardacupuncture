#!/usr/bin/env python3
"""Tests for the read-only Herbology SCORM question classifier."""

from __future__ import annotations

import importlib.util
import json
import sys
import tempfile
import unittest
import zipfile
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(ROOT))
SCRIPT = ROOT / "scorm_herbology_question_extract.py"
SPEC = importlib.util.spec_from_file_location("scorm_herbology_question_extract", SCRIPT)
assert SPEC and SPEC.loader
EXTRACTOR = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(EXTRACTOR)


def source_question(prompt: str, feedback: str = "") -> dict:
    return {
        "__type": "MultipleChoiceQuestion",
        "QuestionText": prompt,
        "Answers": [
            {"Content": "Correct option", "Correct": True},
            {"Content": "Distractor one", "Correct": False},
            {"Content": "Distractor two", "Correct": False},
            {"Content": "Distractor three", "Correct": False},
        ],
        "Feedback": [{"Content": feedback}] if feedback else [],
    }


def placement(module: str, courseid: int = 45, cmid: int = 101) -> dict:
    return {
        "courseid": courseid,
        "coursename": "Herbal Comp Review Module Exams",
        "cmid": cmid,
        "sectionname": module,
        "scormname": module,
        "contenthash": "a" * 40,
    }


class ScormHerbologyQuestionExtractTest(unittest.TestCase):
    def test_build_bank_filters_the_complete_hierarchy_topic(self) -> None:
        source = {
            "Questions": [{
                "__type": "MatchingQuestion",
                "QuestionText": "Identify the hierarchy role.",
                "LeftColumn": [
                    {"Id": number, "Content": f"Ingredient {number}"}
                    for number in range(1, 6)
                ],
                "RightColumn": [
                    {"Id": number, "Content": f"Role {number}"}
                    for number in range(1, 6)
                ],
            }],
        }
        with tempfile.TemporaryDirectory() as directory:
            dataroot = Path(directory)
            content_hash = "b" * 40
            package = EXTRACTOR.package_path(dataroot, content_hash)
            package.parent.mkdir(parents=True)
            with zipfile.ZipFile(package, "w") as archive:
                archive.writestr("story_content/QUIZDATA.json", json.dumps(source))
            selected = placement(
                "IDENTIFICATION OF THE FORMULA ACCORDING TO THE HIERARCHY OF INGREDIENTS (MT) Exam 35",
                44,
                296,
            )
            selected["contenthash"] = content_hash
            selected["timemodified"] = 1
            unrelated = placement("Another SCORM topic", 44, 297)
            unrelated["contenthash"] = "c" * 40
            unrelated["timemodified"] = 1
            bank = EXTRACTOR.build_bank(
                {"dataroot": str(dataroot), "records": [selected, unrelated]},
                [44],
                None,
                EXTRACTOR.re.compile(r"hierarchy of ingredients", EXTRACTOR.re.IGNORECASE),
            )
        self.assertEqual(1, bank["moduleCount"])
        self.assertEqual(5, bank["questionCount"])
        self.assertEqual(5, bank["domainCounts"]["formula_composition"])

    def test_matching_table_becomes_independent_four_choice_questions(self) -> None:
        source = {
            "__type": "MatchingQuestion",
            "QuestionText": "Identify each formula ingredient by its hierarchy role.",
            "LeftColumn": [
                {"Id": number, "Content": f"Ingredient {number}"}
                for number in range(1, 6)
            ],
            "RightColumn": [
                {"Id": number, "Content": f"Role {number}"}
                for number in range(1, 6)
            ],
            "Feedback": [{"Content": "Match the source-supported hierarchy roles."}],
        }
        questions, skipped = EXTRACTOR.extract_source_questions(
            source,
            placement("IDENTIFICATION OF THE FORMULA ACCORDING TO THE HIERARCHY OF INGREDIENTS (MT) Exam 21", 44),
            1,
        )
        self.assertEqual({}, dict(skipped))
        self.assertEqual(5, len(questions))
        self.assertEqual(5, len({question["questionId"] for question in questions}))
        for number, question in enumerate(questions, start=1):
            self.assertEqual("multiple-choice", question["itemFormat"])
            self.assertEqual(4, len(question["options"]))
            self.assertEqual("MatchingQuestion", question["scorm"]["convertedFrom"])
            keyed = next(
                option["text"] for option in question["options"]
                if option["label"] == question["correctLabel"]
            )
            self.assertEqual(f"Role {number}", keyed)

    def test_merge_replaces_only_matching_course_modules(self) -> None:
        old_target, _ = EXTRACTOR.extract_question(
            source_question("Which old hierarchy answer is correct?", "Old feedback."),
            placement("Hierarchy of Ingredients Exam 21", 44, 210),
            1,
        )
        preserved, _ = EXTRACTOR.extract_question(
            source_question("Which preserved answer is correct?", "Preserved feedback."),
            placement("Herb Properties", 45, 450),
            1,
        )
        replacement, _ = EXTRACTOR.extract_question(
            source_question("Which new hierarchy answer is correct?", "New feedback."),
            placement("Hierarchy of Ingredients Exam 21", 44, 210),
            1,
        )
        existing = EXTRACTOR.summarize_bank(
            {"formula_composition": [old_target], "single_herb": [preserved]},
            courses=[44, 45],
        )
        targeted = EXTRACTOR.summarize_bank(
            {"formula_composition": [replacement]},
            courses=[44],
        )
        merged = EXTRACTOR.merge_bank(
            existing,
            targeted,
            [44],
            EXTRACTOR.re.compile(r"hierarchy of ingredients", EXTRACTOR.re.IGNORECASE),
        )
        prompts = {
            question["prompt"]
            for questions in merged["domains"].values()
            for question in questions
        }
        self.assertNotIn("Which old hierarchy answer is correct?", prompts)
        self.assertIn("Which new hierarchy answer is correct?", prompts)
        self.assertIn("Which preserved answer is correct?", prompts)

    def test_classifies_requested_scorm_content_families(self) -> None:
        cases = [
            (
                "Herb Properties",
                "Which herb property includes an acrid flavor and warm temperature?",
                "single_herb",
                "herb_properties",
            ),
            (
                "Formula Ingredient Hierarchy",
                "In this formula, which ingredient serves as the chief herb?",
                "formula_composition",
                "formula_hierarchy",
            ),
            (
                "Herb Processing and Preparation",
                "Which preparation reduces toxicity before decoction?",
                "safety_dispensing",
                "herb_processing",
            ),
        ]
        for module, prompt, expected_domain, expected_content in cases:
            with self.subTest(module=module):
                question, error = EXTRACTOR.extract_question(
                    source_question(prompt, "The keyed answer is correct."),
                    placement(module),
                    1,
                )
                self.assertIsNone(error)
                self.assertEqual(expected_domain, question["domainId"])
                self.assertEqual(expected_content, question["contentType"])
                self.assertTrue(question["editorial"]["originalPreserved"])

    def test_direct_question_is_flagged_for_inference_and_feedback_expansion(self) -> None:
        question, error = EXTRACTOR.extract_question(
            source_question(
                "Which herb has an acrid flavor and warm temperature?",
                "The keyed answer is correct.",
            ),
            placement("Herb Properties"),
            2,
        )
        self.assertIsNone(error)
        self.assertTrue(question["editorial"]["inferenceUpgradeRequired"])
        self.assertEqual("brief", question["editorial"]["feedbackQuality"])
        self.assertFalse(question["editorial"]["simulationCandidate"])

    def test_multiclue_case_with_detailed_reasoning_is_simulation_candidate(self) -> None:
        feedback = (
            "The complete cluster indicates Heat affecting the Lung rather than an isolated symptom. "
            "The keyed answer accounts for the fever, cough, thirst, red tongue, and rapid pulse "
            "together, while every distractor fails to explain at least two decisive findings."
        )
        question, error = EXTRACTOR.extract_question(
            source_question(
                "A patient presents with fever, cough, thirst, a red tongue, and a rapid pulse. "
                "Which formula best fits?",
                feedback,
            ),
            placement("Clinical Cases"),
            3,
        )
        self.assertIsNone(error)
        self.assertEqual("infer", question["cognitiveLevel"])
        self.assertEqual("detailed", question["editorial"]["feedbackQuality"])
        self.assertTrue(question["editorial"]["simulationCandidate"])

    def test_multiple_response_item_is_not_misrepresented_as_single_answer(self) -> None:
        question = source_question("Select the correct herbs.")
        question["Answers"][1]["Correct"] = True
        extracted, error = EXTRACTOR.extract_question(question, placement("Herb Properties"), 4)
        self.assertIsNone(extracted)
        self.assertEqual("not a single-answer multiple-choice question", error)


if __name__ == "__main__":
    unittest.main()
