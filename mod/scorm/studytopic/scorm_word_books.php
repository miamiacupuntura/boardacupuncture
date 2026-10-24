<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Build native Moodle Books that index generated SCORM Word documents.
 *
 * The command is a dry run unless --execute is supplied. Existing activities
 * are never replaced or deleted.
 *
 * @package    mod_scorm
 * @copyright  2026 Miami Acupuncture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/book/locallib.php');
require_once($CFG->libdir . '/clilib.php');

global $CFG, $DB;

[$options, $unrecognised] = cli_get_params([
    'course' => 92,
    'section' => 2020,
    'execute' => false,
    'help' => false,
], [
    'c' => 'course',
    's' => 'section',
    'x' => 'execute',
    'h' => 'help',
]);

if ($unrecognised) {
    $unrecognised = implode("\n  ", $unrecognised);
    cli_error("Unknown options:\n  {$unrecognised}", 2);
}

if ($options['help']) {
    echo <<<HELP
Create native Moodle Books for online viewing of generated Word exam libraries.

The command performs a dry run unless --execute is provided.

Options:
  -c, --course=ID     Target Moodle course ID (default: 92)
  -s, --section=ID    Target course_sections database ID (default: 2020)
  -x, --execute       Create the two Book activities
  -h, --help          Show this help

Example:
  php scorm_word_books.php --course=92 --section=2020 --execute

HELP;
    exit(0);
}

/**
 * Locate a generated Word folder in the target section.
 *
 * @param int $courseid Course ID.
 * @param int $sectionid Section database ID.
 * @param string $name Folder activity name.
 * @return stdClass
 */
function scorm_word_find_folder(int $courseid, int $sectionid, string $name): stdClass {
    global $DB;

    $folder = $DB->get_record_sql(
        "SELECT f.*, cm.id AS cmid
           FROM {course_modules} cm
           JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
           JOIN {folder} f ON f.id = cm.instance
          WHERE cm.course = :courseid
            AND cm.section = :sectionid
            AND cm.deletioninprogress = 0
            AND f.name = :name",
        [
            'modulename' => 'folder',
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'name' => $name,
        ],
        MUST_EXIST
    );
    if (!str_contains((string)$folder->intro, 'data-scorm-word-export')) {
        cli_error("Folder '{$name}' is not marked as a generated SCORM Word export.");
    }
    return $folder;
}

/**
 * Group folder DOCX files by source course and source section.
 *
 * @param stdClass $folder Folder record with cmid.
 * @return array<string, array<string, array<int, stored_file>>>
 */
function scorm_word_library(stdClass $folder): array {
    $context = context_module::instance($folder->cmid);
    $files = get_file_storage()->get_area_files(
        $context->id,
        'mod_folder',
        'content',
        0,
        'filepath, filename',
        false
    );
    $library = [];
    foreach ($files as $file) {
        if (strtolower(pathinfo($file->get_filename(), PATHINFO_EXTENSION)) !== 'docx') {
            continue;
        }
        $segments = array_values(array_filter(explode('/', trim($file->get_filepath(), '/')), 'strlen'));
        $sourcecourse = $segments[0] ?? 'Exam Library';
        $sourcesection = count($segments) > 1
            ? implode(' — ', array_slice($segments, 1))
            : 'General';
        $library[$sourcecourse][$sourcesection][] = $file;
    }

    uksort($library, 'strnatcasecmp');
    foreach ($library as &$sections) {
        uksort($sections, 'strnatcasecmp');
        foreach ($sections as &$sectionfiles) {
            usort($sectionfiles, static function(stored_file $left, stored_file $right): int {
                return strnatcasecmp($left->get_filename(), $right->get_filename());
            });
        }
        unset($sectionfiles);
    }
    unset($sections);
    return $library;
}

/**
 * Count all Word files in a library plan.
 *
 * @param array $library Grouped library.
 * @return int
 */
function scorm_word_library_count(array $library): int {
    $count = 0;
    foreach ($library as $sections) {
        foreach ($sections as $files) {
            $count += count($files);
        }
    }
    return $count;
}

/**
 * Count planned Book chapters.
 *
 * @param array $library Grouped library.
 * @return int
 */
function scorm_word_chapter_count(array $library): int {
    $count = count($library);
    foreach ($library as $sections) {
        $count += count($sections);
    }
    return $count;
}

/**
 * Insert a Book chapter.
 *
 * @param stdClass $book Book record.
 * @param int $pagenum Chapter position.
 * @param bool $subchapter Whether this is a subchapter.
 * @param string $title Chapter title.
 * @param string $content Chapter HTML.
 * @return stdClass
 */
function scorm_word_add_chapter(
    stdClass $book,
    int $pagenum,
    bool $subchapter,
    string $title,
    string $content
): stdClass {
    global $DB;

    $now = time();
    $chapter = (object)[
        'bookid' => $book->id,
        'pagenum' => $pagenum,
        'subchapter' => $subchapter ? 1 : 0,
        'title' => core_text::substr($title, 0, 255),
        'content' => $content,
        'contentformat' => FORMAT_HTML,
        'hidden' => 0,
        'timecreated' => $now,
        'timemodified' => $now,
        'importsrc' => 'scorm-word-library',
    ];
    $chapter->id = $DB->insert_record('book_chapters', $chapter);
    return $chapter;
}

/**
 * Generate a list of preview links for a Book subchapter.
 *
 * @param array<int, stored_file> $files Word files.
 * @param int $foldercmid Source folder course-module ID.
 * @param int $bookcmid Book course-module ID.
 * @param int $chapterid Book chapter ID.
 * @return string
 */
function scorm_word_exam_links(array $files, int $foldercmid, int $bookcmid, int $chapterid): string {
    $items = [];
    foreach ($files as $file) {
        $url = new moodle_url('/mod/scorm/studytopic/scorm_word_view.php', [
            'cmid' => $foldercmid,
            'fileid' => $file->get_id(),
            'bookcmid' => $bookcmid,
            'chapterid' => $chapterid,
        ]);
        $label = pathinfo($file->get_filename(), PATHINFO_FILENAME);
        $items[] = html_writer::tag(
            'li',
            html_writer::link($url, s($label), ['class' => 'd-block py-2']),
            ['class' => 'border-bottom']
        );
    }
    return html_writer::tag(
        'p',
        'Select an exam to read its questions, correct answers and feedback directly in Moodle.'
    ) . html_writer::tag('ul', implode('', $items), ['class' => 'list-unstyled']);
}

$courseid = clean_param($options['course'], PARAM_INT);
$sectionid = clean_param($options['section'], PARAM_INT);
if ($courseid <= 0 || $sectionid <= 0) {
    cli_error('Course and section IDs must be positive integers.');
}

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$section = $DB->get_record('course_sections', [
    'id' => $sectionid,
    'course' => $courseid,
], '*', MUST_EXIST);
$bookmodule = $DB->get_record('modules', ['name' => 'book'], '*', MUST_EXIST);
if (empty($bookmodule->visible)) {
    cli_error('The Moodle Book activity module is disabled.');
}
if (!class_exists('ZipArchive') || !class_exists('DOMDocument')) {
    cli_error('The PHP Zip and DOM extensions are required for online Word previews.');
}

$definitions = [
    [
        'foldername' => 'Formula Exams — Word Downloads',
        'bookname' => 'Formula Exams — Online Book',
        'expected' => 432,
    ],
    [
        'foldername' => 'Herb Exams — Word Downloads',
        'bookname' => 'Herb Exams — Online Book',
        'expected' => 952,
    ],
];
$plans = [];

foreach ($definitions as $definition) {
    $folder = scorm_word_find_folder($courseid, $sectionid, $definition['foldername']);
    $library = scorm_word_library($folder);
    $documents = scorm_word_library_count($library);
    $chapters = scorm_word_chapter_count($library);
    if ($documents !== $definition['expected']) {
        cli_error(
            "{$definition['foldername']} contains {$documents} DOCX files; " .
            "expected {$definition['expected']}."
        );
    }
    if ($chapters === 0) {
        cli_error("No Book chapters could be planned for {$definition['bookname']}.");
    }

    $existing = $DB->get_record_sql(
        "SELECT cm.id AS cmid, b.*
           FROM {course_modules} cm
           JOIN {book} b ON b.id = cm.instance
          WHERE cm.course = :courseid
            AND cm.section = :sectionid
            AND cm.module = :moduleid
            AND cm.deletioninprogress = 0
            AND b.name = :name",
        [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'moduleid' => $bookmodule->id,
            'name' => $definition['bookname'],
        ],
        IGNORE_MULTIPLE
    );
    $status = 'create';
    if ($existing) {
        if (!str_contains((string)$existing->intro, 'data-scorm-word-book')) {
            cli_error(
                "An unrelated Book already uses the name '{$definition['bookname']}' " .
                "(CM {$existing->cmid})."
            );
        }
        $existingchapters = $DB->count_records('book_chapters', ['bookid' => $existing->id]);
        if ($existingchapters !== $chapters) {
            cli_error(
                "Generated Book '{$definition['bookname']}' has {$existingchapters} of " .
                "{$chapters} expected chapters. It was not changed."
            );
        }
        $status = 'already published';
    }

    $plans[] = [
        'folder' => $folder,
        'library' => $library,
        'bookname' => $definition['bookname'],
        'documents' => $documents,
        'chapters' => $chapters,
        'status' => $status,
    ];
}

echo "Course: {$course->fullname} ({$course->id})\n";
echo "Section: {$section->name} (database ID {$section->id}, section {$section->section})\n";
foreach ($plans as $plan) {
    echo "{$plan['bookname']}: {$plan['documents']} exams, {$plan['chapters']} chapters — " .
        "{$plan['status']}\n";
}

if (!$options['execute']) {
    echo "Dry run complete. No Moodle data was changed.\n";
    echo "Run the same command with --execute to create the Books.\n";
    exit(0);
}

$admin = get_admin();
if (!$admin) {
    cli_error('The Moodle administrator account could not be found.');
}
\core\session\manager::set_user($admin);

foreach ($plans as $plan) {
    if ($plan['status'] === 'already published') {
        echo "Skipping existing verified Book: {$plan['bookname']}\n";
        continue;
    }

    $transaction = $DB->start_delegated_transaction();
    try {
        $moduleinfo = (object)[
            'modulename' => 'book',
            'module' => $bookmodule->id,
            'section' => (int)$section->section,
            'name' => $plan['bookname'],
            'intro' => '<p data-scorm-word-book="1">Online index of the generated SCORM exam ' .
                'library. Select a category and then an exam to read it in Moodle.</p>',
            'introformat' => FORMAT_HTML,
            'numbering' => BOOK_NUM_BULLETS,
            'customtitles' => 0,
            'visible' => 1,
            'visibleoncoursepage' => 1,
            'groupmode' => 0,
            'groupingid' => 0,
            'completion' => COMPLETION_DISABLED,
            'completionview' => COMPLETION_VIEW_NOT_REQUIRED,
            'completionexpected' => 0,
            'availability' => null,
            'showdescription' => 0,
        ];
        $created = add_moduleinfo($moduleinfo, $course);
        $book = $DB->get_record('book', ['id' => $created->instance], '*', MUST_EXIST);
        $context = context_module::instance($created->coursemodule);
        $pagenum = 1;

        foreach ($plan['library'] as $sourcecourse => $sections) {
            $coursecontent = html_writer::tag(
                'p',
                'Choose a topic from the Book table of contents. Each topic lists the available exams.'
            );
            $coursechapter = scorm_word_add_chapter(
                $book,
                $pagenum++,
                false,
                $sourcecourse,
                $coursecontent
            );
            \mod_book\event\chapter_created::create_from_chapter(
                $book,
                $context,
                $coursechapter
            )->trigger();
            foreach ($sections as $sourcesection => $files) {
                $chapter = scorm_word_add_chapter(
                    $book,
                    $pagenum++,
                    true,
                    $sourcesection,
                    ''
                );
                $chapter->content = scorm_word_exam_links(
                    $files,
                    $plan['folder']->cmid,
                    $created->coursemodule,
                    $chapter->id
                );
                $chapter->timemodified = time();
                $DB->update_record('book_chapters', $chapter);
                \mod_book\event\chapter_created::create_from_chapter(
                    $book,
                    $context,
                    $chapter
                )->trigger();
            }
        }

        $DB->set_field('book', 'revision', $book->revision + 1, ['id' => $book->id]);
        book_preload_chapters($book);
        $transaction->allow_commit();
        echo "Created {$plan['bookname']} (CM {$created->coursemodule}).\n";
    } catch (Throwable $exception) {
        $transaction->rollback($exception);
    }
}

rebuild_course_cache($courseid, true);
echo "Online Books published. Existing folders and Word files were not changed.\n";
