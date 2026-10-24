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

This creates two visible Moodle folder activities in `Formulas Tips`:

- `Formula Exams — Word Downloads`
- `Herb Exams — Word Downloads`

Each activity preserves the course and section subfolders and provides Moodle's
folder-download button. Existing activities are never replaced or deleted. A
repeat run only skips a generated activity after verifying its DOCX count.

## Create online Moodle Books

The Book builder uses the generated Word folders as its source. It creates one
Formula Book and one Herb Book, with source courses as chapters and source
sections as subchapters. Exam names open a secure in-Moodle preview of their
questions, answers, feedback, and images; the original Word download remains
available from the preview.

Run the validation first:

```bash
sudo -u www-data php mod/scorm/studytopic/scorm_word_books.php \
  --course=92 \
  --section=2020
```

Then create the Books explicitly:

```bash
sudo -u www-data php mod/scorm/studytopic/scorm_word_books.php \
  --course=92 \
  --section=2020 \
  --execute
```

The command creates:

- `Formula Exams — Online Book`
- `Herb Exams — Online Book`

It never replaces or deletes an existing activity. Repeated execution verifies
and skips Books previously created by this tool.

## Custom course selection

```bash
sudo -u www-data python3 mod/scorm/studytopic/scorm_word_export.py \
  --formula-courses 117,44 \
  --herb-courses 45,76,41,82 \
  --output /var/tmp/formula-herb-word-export
```
