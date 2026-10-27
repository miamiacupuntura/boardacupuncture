# Complete Final staged question-bank import

`complete_final_import.py` connects the supplied `Complete Final.docx` bank to
the existing Moodle question workflow without treating unreviewed study notes
as scored questions.

The Word source marks a correct option with `+++++`. The importer uses only that
explicit marker. It never derives an answer from feedback, formula names, or
clinical wording.

## Run the audit and staging export

From the Moodle application root:

```bash
python3 mod/scorm/studytopic/complete_final_import.py \
  /path/to/Complete\ Final.docx \
  --output /var/tmp/complete-final-import
```

The output directory must not already exist. The command creates:

```text
complete-final-import/
├── audit.json
├── audit.md
├── complete-final-keyed-moodle.xml
├── keyed-questions.jsonl
└── review-required.jsonl
```

- `complete-final-keyed-moodle.xml` contains only multiple-choice questions
  with exactly one explicitly marked answer.
- `keyed-questions.jsonl` preserves the parsed source records for audit.
- `review-required.jsonl` holds unkeyed choices, identification material, and
  any ambiguous item. These records are not silently converted into quizzes.
- `audit.json` records the source SHA-256, counts, policy, and artifact names.

## Connection to the current workflow

The existing `scorm_word_export.py` works in the opposite direction: it
reconstructs instructor Word downloads from SCORM packages already stored in
Moodle. This staged importer prepares new, explicitly keyed source material as
standard Moodle XML.

Use Moodle's question-bank import screen to import the XML into a review
category. Preview a representative sample, verify the item count and clinical
key, then move approved questions into the desired production category. The
importer does not publish activities, alter attempts or grades, or make the
unreviewed items student-facing.

Do not import `review-required.jsonl` as scored content. After a subject-matter
expert supplies explicit answer keys, update the controlled source and rerun
the staging process into a new output directory so the audit trail remains
reproducible.
