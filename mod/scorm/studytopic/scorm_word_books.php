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
 * The command is a dry run unless --execute is supplied. Existing generated
 * Books are upgraded in place; activities and source documents are never
 * replaced or deleted.
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
  -x, --execute       Create or update the two Book activities
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

/**
 * Count the Word files in all sections of one source course.
 *
 * @param array<string, array<int, stored_file>> $sections Source sections.
 * @return int
 */
function scorm_word_source_course_count(array $sections): int {
    $count = 0;
    foreach ($sections as $files) {
        $count += count($files);
    }
    return $count;
}

/**
 * Build a link to a specific chapter in a generated Book.
 *
 * @param int $bookcmid Book course-module ID.
 * @param int $chapterid Chapter ID.
 * @param string $label Link label.
 * @param array $attributes HTML attributes.
 * @return string
 */
function scorm_word_chapter_link(
    int $bookcmid,
    int $chapterid,
    string $label,
    array $attributes = []
): string {
    $url = new moodle_url('/mod/book/view.php', [
        'id' => $bookcmid,
        'chapterid' => $chapterid,
    ]);
    return html_writer::link($url, s($label), $attributes);
}

/**
 * Build the main, compact library index.
 *
 * @param array $library Grouped Word library.
 * @param int $bookcmid Book course-module ID.
 * @param array<string, stdClass> $coursechapters Parent chapters by source course.
 * @return string
 */
function scorm_word_main_index(array $library, int $bookcmid, array $coursechapters): string {
    $cards = [];
    foreach ($library as $sourcecourse => $sections) {
        $examcount = scorm_word_source_course_count($sections);
        $sectioncount = count($sections);
        $header = html_writer::tag('h4', s($sourcecourse), ['class' => 'mb-0']);
        $summary = html_writer::tag(
            'p',
            "{$examcount} exams in {$sectioncount} topics",
            ['class' => 'card-text text-muted']
        );
        $button = scorm_word_chapter_link(
            $bookcmid,
            $coursechapters[$sourcecourse]->id,
            'Open category',
            ['class' => 'btn btn-primary']
        );
        $cards[] = html_writer::div(
            html_writer::div($header, 'card-header') .
            html_writer::div($summary . $button, 'card-body'),
            'card h-100'
        );
    }

    $columns = [];
    foreach ($cards as $card) {
        $columns[] = html_writer::div($card, 'col-12 col-md-6 mb-3');
    }
    return html_writer::tag(
        'p',
        'Choose a category. The next page lists its topics and the number of available exams.'
    ) . html_writer::div(implode('', $columns), 'row scorm-word-library-index');
}

/**
 * Build a topic index for one source course.
 *
 * @param array<string, array<int, stored_file>> $sections Source sections.
 * @param int $bookcmid Book course-module ID.
 * @param array<string, stdClass> $sectionchapters Section chapters by title.
 * @return string
 */
function scorm_word_category_index(array $sections, int $bookcmid, array $sectionchapters): string {
    $items = [];
    foreach ($sections as $title => $files) {
        $label = scorm_word_chapter_link(
            $bookcmid,
            $sectionchapters[$title]->id,
            $title,
            ['class' => 'font-weight-bold']
        );
        $count = html_writer::tag(
            'span',
            count($files) . ' exams',
            ['class' => 'badge badge-light ml-2']
        );
        $items[] = html_writer::tag(
            'li',
            $label . $count,
            ['class' => 'list-group-item d-flex justify-content-between align-items-center']
        );
    }
    return html_writer::tag('p', 'Choose a topic to see all its exams.') .
        html_writer::tag('ul', implode('', $items), [
            'class' => 'list-group scorm-word-category-index',
        ]);
}

/**
 * Load and validate a generated Book's chapter structure.
 *
 * @param stdClass $book Book record.
 * @param array $library Grouped Word library.
 * @return array{index: stdClass, courses: array, sections: array}
 */
function scorm_word_map_chapters(stdClass $book, array $library): array {
    global $DB;

    $chapters = array_values($DB->get_records(
        'book_chapters',
        ['bookid' => $book->id],
        'pagenum ASC'
    ));
    $expected = scorm_word_chapter_count($library) + 1;
    if (count($chapters) !== $expected) {
        cli_error("Generated Book '{$book->name}' has an unexpected chapter count.");
    }

    $index = array_shift($chapters);
    if ($index->importsrc !== 'scorm-word-library-index' || $index->subchapter) {
        cli_error("Generated Book '{$book->name}' does not have the expected main index.");
    }

    $coursechapters = [];
    $sectionchapters = [];
    foreach ($library as $sourcecourse => $sections) {
        $coursechapter = array_shift($chapters);
        if (!$coursechapter || $coursechapter->subchapter ||
                $coursechapter->importsrc !== 'scorm-word-library' ||
                $coursechapter->title !== $sourcecourse) {
            cli_error("Generated Book '{$book->name}' has an unexpected category structure.");
        }
        $coursechapters[$sourcecourse] = $coursechapter;
        $sectionchapters[$sourcecourse] = [];
        foreach ($sections as $sourcesection => $files) {
            $sectionchapter = array_shift($chapters);
            if (!$sectionchapter || !$sectionchapter->subchapter ||
                    $sectionchapter->importsrc !== 'scorm-word-library' ||
                    $sectionchapter->title !== $sourcesection) {
                cli_error("Generated Book '{$book->name}' has an unexpected topic structure.");
            }
            $sectionchapters[$sourcecourse][$sourcesection] = $sectionchapter;
        }
    }
    return [
        'index' => $index,
        'courses' => $coursechapters,
        'sections' => $sectionchapters,
    ];
}

/**
 * Add the initial chapter structure for a new generated Book.
 *
 * @param stdClass $book Book record.
 * @param context_module $context Book context.
 * @param array $library Grouped Word library.
 * @return void
 */
function scorm_word_create_chapters(stdClass $book, context_module $context, array $library): void {
    global $DB;

    $pagenum = 1;
    $index = scorm_word_add_chapter($book, $pagenum++, false, 'Exam Library Index', '');
    $index->importsrc = 'scorm-word-library-index';
    $index->timemodified = time();
    $DB->update_record('book_chapters', $index);
    \mod_book\event\chapter_created::create_from_chapter($book, $context, $index)->trigger();

    foreach ($library as $sourcecourse => $sections) {
        $coursechapter = scorm_word_add_chapter($book, $pagenum++, false, $sourcecourse, '');
        \mod_book\event\chapter_created::create_from_chapter($book, $context, $coursechapter)->trigger();
        foreach ($sections as $sourcesection => $files) {
            $chapter = scorm_word_add_chapter($book, $pagenum++, true, $sourcesection, '');
            \mod_book\event\chapter_created::create_from_chapter($book, $context, $chapter)->trigger();
        }
    }
}

/**
 * Add a main index to a previously generated Book.
 *
 * @param stdClass $book Book record.
 * @param context_module $context Book context.
 * @return void
 */
function scorm_word_add_main_index(stdClass $book, context_module $context): void {
    global $DB;

    $chapters = $DB->get_records('book_chapters', ['bookid' => $book->id], 'pagenum DESC');
    foreach ($chapters as $chapter) {
        if ($chapter->importsrc !== 'scorm-word-library') {
            cli_error("Book '{$book->name}' contains a chapter not owned by this generator.");
        }
        $chapter->pagenum++;
        $DB->update_record('book_chapters', $chapter);
    }
    $index = scorm_word_add_chapter($book, 1, false, 'Exam Library Index', '');
    $index->importsrc = 'scorm-word-library-index';
    $index->timemodified = time();
    $DB->update_record('book_chapters', $index);
    \mod_book\event\chapter_created::create_from_chapter($book, $context, $index)->trigger();
}

/**
 * Refresh all generated chapter content and links.
 *
 * @param stdClass $book Book record.
 * @param int $bookcmid Book course-module ID.
 * @param context_module $context Book context.
 * @param stdClass $folder Source folder record.
 * @param array $library Grouped Word library.
 * @return void
 */
function scorm_word_refresh_book(
    stdClass $book,
    int $bookcmid,
    context_module $context,
    stdClass $folder,
    array $library
): void {
    global $DB;

    $existingcount = $DB->count_records('book_chapters', ['bookid' => $book->id]);
    $contentcount = scorm_word_chapter_count($library);
    if ($existingcount === 0) {
        scorm_word_create_chapters($book, $context, $library);
    } else if ($existingcount === $contentcount) {
        scorm_word_add_main_index($book, $context);
    } else if ($existingcount !== $contentcount + 1) {
        cli_error("Generated Book '{$book->name}' has an unexpected chapter count.");
    }

    $map = scorm_word_map_chapters($book, $library);
    $now = time();
    $map['index']->content = scorm_word_main_index($library, $bookcmid, $map['courses']);
    $map['index']->timemodified = $now;
    $DB->update_record('book_chapters', $map['index']);

    foreach ($library as $sourcecourse => $sections) {
        $coursechapter = $map['courses'][$sourcecourse];
        $coursechapter->content = scorm_word_category_index(
            $sections,
            $bookcmid,
            $map['sections'][$sourcecourse]
        );
        $coursechapter->timemodified = $now;
        $DB->update_record('book_chapters', $coursechapter);

        foreach ($sections as $sourcesection => $files) {
            $sectionchapter = $map['sections'][$sourcecourse][$sourcesection];
            $sectionchapter->content = scorm_word_exam_links(
                $files,
                $folder->cmid,
                $bookcmid,
                $sectionchapter->id
            );
            $sectionchapter->timemodified = $now;
            $DB->update_record('book_chapters', $sectionchapter);
        }
    }

    $book->intro = '<p data-scorm-word-book="2">Online index of the generated SCORM exam ' .
        'library. Select a category, topic and exam to read it in Moodle.</p>';
    $book->introformat = FORMAT_HTML;
    $book->revision++;
    $book->timemodified = $now;
    $DB->update_record('book', $book);
    book_preload_chapters($book);
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
    $contentchapters = scorm_word_chapter_count($library);
    $chapters = $contentchapters + 1;
    if ($documents !== $definition['expected']) {
        cli_error(
            "{$definition['foldername']} contains {$documents} DOCX files; " .
            "expected {$definition['expected']}."
        );
    }
    if ($contentchapters === 0) {
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
        if ($existingchapters === $contentchapters) {
            $status = 'add clear index';
        } else if ($existingchapters === $chapters) {
            $firstchapter = $DB->get_record(
                'book_chapters',
                ['bookid' => $existing->id, 'pagenum' => 1],
                '*',
                MUST_EXIST
            );
            if ($firstchapter->importsrc !== 'scorm-word-library-index') {
                cli_error(
                    "Generated Book '{$definition['bookname']}' has an unrecognised first chapter."
                );
            }
            $status = 'refresh clear index';
        } else {
            cli_error(
                "Generated Book '{$definition['bookname']}' has {$existingchapters} chapters; " .
                "expected {$contentchapters} before upgrade or {$chapters} after upgrade. " .
                'It was not changed.'
            );
        }
    }

    $plans[] = [
        'folder' => $folder,
        'library' => $library,
        'bookname' => $definition['bookname'],
        'documents' => $documents,
        'chapters' => $chapters,
        'status' => $status,
        'existing' => $existing,
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
    echo "Run the same command with --execute to create or update the Books.\n";
    exit(0);
}

$admin = get_admin();
if (!$admin) {
    cli_error('The Moodle administrator account could not be found.');
}
\core\session\manager::set_user($admin);

foreach ($plans as $plan) {
    $transaction = $DB->start_delegated_transaction();
    try {
        if ($plan['existing']) {
            $book = $plan['existing'];
            $bookcmid = (int)$book->cmid;
        } else {
            $moduleinfo = (object)[
                'modulename' => 'book',
                'module' => $bookmodule->id,
                'section' => (int)$section->section,
                'name' => $plan['bookname'],
                'intro' => '<p data-scorm-word-book="2">Online index of the generated SCORM exam ' .
                    'library. Select a category, topic and exam to read it in Moodle.</p>',
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
            $bookcmid = (int)$created->coursemodule;
            $book = $DB->get_record('book', ['id' => $created->instance], '*', MUST_EXIST);
        }

        $context = context_module::instance($bookcmid);
        scorm_word_refresh_book(
            $book,
            $bookcmid,
            $context,
            $plan['folder'],
            $plan['library']
        );
        $DB->set_field(
            'course_modules',
            'visibleoncoursepage',
            0,
            ['id' => $plan['folder']->cmid]
        );
        $transaction->allow_commit();
        echo "Updated {$plan['bookname']} (CM {$bookcmid}); source folder is now stealth.\n";
    } catch (Throwable $exception) {
        $transaction->rollback($exception);
    }
}

rebuild_course_cache($courseid, true);
echo "Online Books published with clear indexes. Source Word files were not changed.\n";
