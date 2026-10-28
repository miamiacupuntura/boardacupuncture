#!/usr/bin/env python3
"""Regression checks for Study Guide Instructor AI context wiring."""

from __future__ import annotations

import unittest
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]


class InstructorAiContextTest(unittest.TestCase):
    @classmethod
    def setUpClass(cls) -> None:
        cls.endpoint = (ROOT / "instructor_ai.php").read_text(encoding="utf-8")
        cls.index = (ROOT / "index.php").read_text(encoding="utf-8")
        cls.index_v2 = (ROOT / "index_v2.php").read_text(encoding="utf-8")

    def test_current_activity_title_is_authoritative_context(self) -> None:
        self.assertIn("CURRENT STUDY GUIDE TITLE:", self.endpoint)
        self.assertIn("$scorm->name", self.endpoint)
        self.assertIn("'this group'", self.endpoint)
        self.assertIn("'este grupo'", self.endpoint)
        self.assertIn("parenthetical list of herbs", self.endpoint)

    def test_saved_instructor_materials_are_included(self) -> None:
        self.assertIn("scorm_studyguide_instructor", self.endpoint)
        self.assertIn("INSTRUCTOR STUDY MATERIALS:", self.endpoint)

    def test_both_page_versions_send_exact_course_module(self) -> None:
        expected = "cmid: <?php echo (int)$cmid; ?>"
        self.assertIn(expected, self.index)
        self.assertIn(expected, self.index_v2)

    def test_both_page_versions_offer_directed_questions(self) -> None:
        for page in (self.index, self.index_v2):
            self.assertIn("Preguntas dirigidas", page)
            self.assertEqual(4, page.count('class="instructor_ai_guided_prompt"'))
            self.assertIn("Hierbas del grupo", page)
            self.assertIn("Acción compartida", page)
            self.assertIn("Diferencias clave", page)
            self.assertIn("Pistas de examen", page)
            self.assertIn("CURRENT STUDY GUIDE TITLE", page)
            self.assertIn("document.getElementById('instructor_ai_button').click()", page)

    def test_narrow_requests_cannot_expand_to_unrelated_categories(self) -> None:
        self.assertIn("RESPONSE-PRECISION RULES:", self.endpoint)
        self.assertIn("list only the members", self.endpoint)
        self.assertIn("general survey of unrelated herbal categories", self.endpoint)


if __name__ == "__main__":
    unittest.main()
