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


if __name__ == "__main__":
    unittest.main()
