# SCORM question-bank Word export

This CLI export reconstructs instructor Word downloads from the source SCORM
packages already stored by Moodle. It does not change courses, attempts, grades,
activities, or Moodle's file pool.

The default recovery scope is:

- **Formulas:** courses 117 and 44, preferring course 117 when the same package
  is present in both courses.
- **Herbs:** courses 45, 76, 41, and 82, preferring course 45 when the same
  package is present more than once.

Packages are deduplicated by Moodle `contenthash`. If a Moodle activity has more
than one stored package revision, only its newest revision is considered.

## Run a production export

From the Moodle application root:

```bash
sudo -u www-data python3 mod/scorm/studytopic/scorm_word_export.py \
  --output /var/tmp/formula-herb-word-export
```

The output directory must not already exist unless `--overwrite` is explicitly
provided. A normal run creates:

```text
formula-herb-word-export/
├── Formulas/
│   ├── manifest.csv
│   ├── manifest.json
│   ├── matching-study-guide-index.html
│   ├── matching-study-guide-index.csv
│   ├── matching-study-guide-index.json
│   ├── errors.json
│   └── ... true .docx files grouped by course and section
├── Herbs/
│   └── ...
├── Formulas_Exams_Word.zip
├── Herbs_Exams_Word.zip
├── export-summary.json
└── .scorm-word-export
```

Each Word document includes the question wording, available images, answer
choices or matches, the correct answer, question notes, and source feedback.
Legacy click-map questions include the source image and correct hotspot data.

Documents that contain Matching study cards open directly with the actual
comparison title. The former branded cover, instructor metadata, generic
Contents/Chapter pages, internal index sheet, and generic `Question / Matching`
labels are omitted. Each Matching comparison is formatted as a compact
landscape card intended to fit on one printed sheet: clues, comparative answer
key, and exam-focused guidance remain together. Card titles retain each full
botanical name plus its pinyin; only the separate library index shortens entries
to the parenthetical pinyin names. Other documents also omit the
former administrative cover and chapter contents.

Every category export also creates `matching-study-guide-index.html`, `.csv`,
and `.json`. The browser index reports total Matching occurrences, unique
comparisons, and first-herb categories. Repeated comparisons from category and
simulation modules are combined under one readable title with an occurrence
count, while CSV/JSON preserve every source exam and question number for later
deduplication work. These totals are included in `export-summary.json` and the
CLI completion message.

Each document uses readable question typography, shaded options, green
correct-answer boxes, orange note boxes, running headers, page numbers, and
structured Mock-style feedback. Combined legacy feedback is divided
into `Why the correct answer is correct` and a separate explanation for every
incorrect option. This layout is generated automatically and must not be
recreated manually in individual files.

Some legacy packages store the same formula-and-pattern text as both correct
and incorrect feedback. The exporter prints that source text once, then creates
separate Mock-style answer guidance. When an answer choice is correct in a
related question from the same document, the distractor explanation names the
symptom for which that choice is actually keyed; otherwise it clearly states
that the source bank does not key the choice for the symptom in the stem.
Questions that contain no source feedback still receive the two Mock-style
answer sections. Those sections identify the keyed answer and each distractor,
while explicitly noting that the original package supplied no detailed or
option-specific rationale instead of inventing clinical content.
The answer guidance is interaction-aware: Fill in the Blank questions list all
accepted responses and explain matching rules; Matching questions show the
keyed pairs; Multiple Response questions identify every correct selection;
Sequence questions preserve the keyed order; and Click Map questions retain
the source hotspot information.
For non-choice interactions, the feedback refers back to the answer key already
printed above instead of repeating long accepted-answer, matching, sequence, or
hotspot data. Long retained feedback is labelled `Detailed source review` for
question types where it is relevant. Duplicate legacy formula-reference lists
are omitted from Matching questions because they are not match-specific
feedback. Matching explanations identify the comparison title rather than using
a generic `question prompt` label. Two-item comparisons use one side-by-side
table with shared `Category`, `Details`, and answer-group rows; Matching sets of
other sizes fall back to numbered cards. Long multiline values no longer run
together around a single arrow. The feedback states each item's classification
and the number/type of keyed associations, and explains that swapping those
groups attaches the source relationships to the wrong item.
When a Matching prompt contains shared actions or item-specific symptoms, the
exporter separates those lines from the actual question and places them in a
blue `Study tip · Clues to help answer` card. Shared clues and each item's clues
receive their own labelled row and symptom lists are converted to bullets. For
two-item comparisons, the tips use the same side-by-side columns as the answer
key: shared clues span both columns and item-specific symptoms align in one
comparison row. The shared-clue row uses an amber background and bold accent
text so the common feature is immediately distinguishable from the contrasts.
Two-item Matching comparisons also include a compact purple `Study guide ·
Exam focus` card derived from the source categories and clue lists. It appears
after the comparative answer key and explains why the distinction matters,
identifies overlapping findings that can cause confusion, and shows how each
side could become an exam vignette without inventing new answer-key facts. A
final row explains why reversing the two groups is incorrect. When this complete
card is available, duplicated legacy Matching feedback is suppressed so the
same categories and associations are not printed a second time. Compact fonts,
cell padding, margins, and landscape width keep the entire Matching study card
on one sheet without dropping any of its four explanation rows. A final
`Possible exam questions · Study prompts` block motivates active review of the
two categories, shared findings, distinguishing symptom clusters, and formula
associations. These are study prompts only and are not yet scored or reported.
Related-question surveys, progress scoring, and student checklists are deferred
to a later reporting phase so the current work can prioritize Study Guide card
quality and source accuracy.

## Validate before publishing

Inspect the summary and errors first:

```bash
cat /var/tmp/formula-herb-word-export/export-summary.json
```

Open a sample of Word documents from each question type before placing either ZIP
in Moodle. Publishing the ZIP files into the `Formulas Tips` section is a
separate, deliberate step.

`--overwrite` only replaces a directory that contains the exporter marker
`.scorm-word-export`; it refuses to remove any unrelated directory.

## Publish as Moodle folders

The publisher validates the completed export, target course, target section,
document totals, and existing activities. Its default invocation is a dry run:

```bash
sudo -u www-data php mod/scorm/studytopic/scorm_word_publish.php \
  --output=/var/tmp/formula-herb-word-export \
  --course=92 \
  --section=2020
```

After reviewing the dry-run output, publish with the explicit flag:

```bash
sudo -u www-data php mod/scorm/studytopic/scorm_word_publish.php \
  --output=/var/tmp/formula-herb-word-export \
  --course=92 \
  --section=2020 \
  --execute
```

This initially creates two visible Moodle folder activities in `Formulas Tips`:

- `Formula Exams — Word Downloads`
- `Herb Exams — Word Downloads`

Each activity preserves the course and section subfolders and provides Moodle's
folder-download button. Existing activities are never replaced or deleted. A
repeat run only skips a generated activity after verifying its DOCX count.

To replace the files inside the two verified generated folders after a template
change, first run a dry run with the explicit refresh flag:

```bash
sudo -u www-data php mod/scorm/studytopic/scorm_word_publish.php \
  --output=/var/tmp/formula-herb-word-export \
  --course=92 \
  --section=2020 \
  --refresh-existing
```

After reviewing the counts, add `--execute`. The refresh preserves the Folder
course-module IDs. Run `scorm_word_books.php --execute` immediately afterward
so every Book preview link is regenerated with the refreshed Moodle file IDs.

After the Online Books are created, the Book builder changes these source
folders to stealth activities (`visible=1`, `visibleoncoursepage=0`). Students
use the Books, while secure preview and Word-download links continue to work.

## Create online Moodle Books

The Book builder uses the generated Word folders as its source. It creates one
Formula Book and one Herb Book with a main index, category indexes, source
courses as chapters, and source sections as subchapters. Exam names open a
secure in-Moodle preview of their questions, answers, feedback, and images; the
original Word download remains available from the preview. On generated Book
and preview pages, the old course-index drawer is hidden so the native Book
table of contents is the only side navigation.
The document preview keeps that Book table of contents visible and provides
sticky `Book Index`, `Back to Exam List`, `Previous Exam`, and `Next Exam`
controls for long documents.
On a course containing generated Word Books, the legacy course-index drawer is
also hidden while edit mode is off. Teachers can turn edit mode on whenever
they need the original course navigation for maintenance.

Run the validation first:

```bash
sudo -u www-data php mod/scorm/studytopic/scorm_word_books.php \
  --course=92 \
  --section=2020
```

Then create or update the Books explicitly:

```bash
sudo -u www-data php mod/scorm/studytopic/scorm_word_books.php \
  --course=92 \
  --section=2020 \
  --execute
```

The command creates:

- `Formula Exams — Online Book`
- `Herb Exams — Online Book`

It never replaces or deletes an existing activity. Repeated execution safely
refreshes Books previously created by this tool. Books created by the first
version are upgraded in place with a main index; chapter and document links are
preserved and regenerated from the source folders.

## Custom course selection

```bash
sudo -u www-data python3 mod/scorm/studytopic/scorm_word_export.py \
  --formula-courses 117,44 \
  --herb-courses 45,76,41,82 \
  --output /var/tmp/formula-herb-word-export
```
