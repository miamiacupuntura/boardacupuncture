#!/usr/bin/env python3
"""Tests for the read-only Herbology SCORM question classifier."""

from __future__ import annotations

import importlib.util
import sys
import unittest
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


def placement(module: str) -> dict:
    return {
        "courseid": 45,
        "coursename": "Herbal Comp Review Module Exams",
        "cmid": 101,
        "sectionname": module,
        "scormname": module,
        "contenthash": "a" * 40,
    }


class ScormHerbologyQuestionExtractTest(unittest.TestCase):
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
