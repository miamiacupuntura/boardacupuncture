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

## Build the connected 63-formula practice bank

The Study Guide practice generator consumes only `keyed-questions.jsonl` and
matches explicit formula-name mentions to the existing 63-formula catalog:

```bash
node tools/generate_formula_practice.mjs \
  --source /var/tmp/complete-final-import/keyed-questions.jsonl
ARGOS_CHUNK_TYPE=MINISBD ARGOS_INTRA_THREADS=1 \
  python3 tools/translate_formula_practice_es.py
node tools/generate_formula_navigator.mjs
node tools/validate_formula_practice.mjs
```

This creates the bilingual Formula Practice pages and their validated static
data. A question may be linked to more than one formula when it is comparative
or names another formula as a distractor. The interface describes these as
related questions rather than implying that every linked formula is correct.
General keyed questions remain available in comprehensive mock exams. The
review queue is never read by the practice generator.

The Spanish question, option, and feedback text is an automatic translation
layer stored separately from the authoritative English bank. The Spanish page
shows a language-quality warning and lets the student reveal the original
English for each question. Formula associations, answer labels, and the
correct-answer key always come from the English source data, so translation
cannot change which option is scored as correct.

Reviewed wording corrections live in
`tools/formula_practice_copy_overrides.json`. The practice generator also
normalizes recurring source grammar and punctuation before translation. Keep
clinical copy edits in the override file so regenerating the bank does not
discard them, and never place answer-key changes in that file.
