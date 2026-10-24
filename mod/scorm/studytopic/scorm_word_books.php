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
  -x, --execute       Create or update the five Book activities
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
 * Topics whose source is a Word Bank rather than a readable answer key.
 *
 * @return array<string, array<int, string>> Topics grouped by source course.
 */
function scorm_word_excluded_topics(): array {
    return [
        'Playing Herbology Module Exams' => [
            'Formulas Groups. Pinyin Name',
            'NCCAOM FORMULA MAP. Word Bank',
        ],
    ];
}

/**
 * Exclude unsupported Word Bank topics from the student-facing Book.
 *
 * The source DOCX files remain untouched in the download folder.
 *
 * @param array $library Grouped library.
 * @return array Filtered library.
 */
function scorm_word_filter_unsupported_topics(array $library): array {
    foreach (scorm_word_excluded_topics() as $sourcecourse => $topics) {
        foreach ($topics as $topic) {
            unset($library[$sourcecourse][$topic]);
        }
        if (isset($library[$sourcecourse]) && !$library[$sourcecourse]) {
            unset($library[$sourcecourse]);
        }
    }
    return $library;
}

/**
 * Topic promoted from the Herb library to its own Clinical Cases library.
 *
 * @return array{course: string, topic: string}
 */
function scorm_word_clinical_cases_topic(): array {
    return [
        'course' => 'Herbal Comp Review Module Exams',
        'topic' => 'Handbook-of-Oriental-Medicine. Clinical Cases',
    ];
}

/**
 * Remove Clinical Cases from the general Herb library.
 *
 * @param array $library Grouped library.
 * @return array Filtered library.
 */
function scorm_word_without_clinical_cases(array $library): array {
    $clinical = scorm_word_clinical_cases_topic();
    unset($library[$clinical['course']][$clinical['topic']]);
    if (isset($library[$clinical['course']]) && !$library[$clinical['course']]) {
        unset($library[$clinical['course']]);
    }
    return $library;
}

/**
 * Move the Formula-only review topic out of the Herb library.
 *
 * @param array $library Grouped Herb library.
 * @return array Filtered library.
 */
function scorm_word_without_formula_review(array $library): array {
    $sourcecourse = 'Herbal Comp Review Module Exams';
    unset($library[$sourcecourse]['Formula']);
    if (isset($library[$sourcecourse]) && !$library[$sourcecourse]) {
        unset($library[$sourcecourse]);
    }
    return $library;
}

/**
 * Place the Single-Herbs-only review inside the main Single Herbs collection.
 *
 * @param array $library Grouped Herb library after mixed topics were removed.
 * @return array Herb library with the review filed under Single Herbs.
 */
function scorm_word_rehome_single_herbs_review(array $library): array {
    $sourcecourse = 'Herbal Comp Review Module Exams';
    $files = $library[$sourcecourse]['Single Herbs'] ?? [];
    if (count($files) !== 20) {
        cli_error('The Single Herbs review topic must contain exactly 20 answer keys.');
    }
    $library['Single Herbs Exams']['Herbal Combinations Review — Single Herbs'] = $files;
    unset($library[$sourcecourse]);
    uksort($library['Single Herbs Exams'], 'strnatcasecmp');
    return $library;
}

/**
 * Add the Formula-only review topic to the Formula library.
 *
 * Files remain in their original generated folder; preview links resolve the
 * owning folder from each stored file's module context.
 *
 * @param array $formulalibrary Grouped Formula library.
 * @param array $herblibrary Grouped Herb library.
 * @return array Combined Formula library.
 */
function scorm_word_add_formula_review(array $formulalibrary, array $herblibrary): array {
    $sourcecourse = 'Herbal Comp Review Module Exams';
    $files = $herblibrary[$sourcecourse]['Formula'] ?? [];
    if (count($files) !== 18) {
        cli_error('The Formula-only herbal review topic must contain exactly 18 answer keys.');
    }
    $formulalibrary['Formulas Exams']['Herbal Combinations Review — Formula'] = $files;
    uksort($formulalibrary['Formulas Exams'], 'strnatcasecmp');
    return $formulalibrary;
}

/**
 * Build the standalone Clinical Cases library from the Herb source folder.
 *
 * @param array $library Grouped Herb library.
 * @return array Standalone Clinical Cases library.
 */
function scorm_word_only_clinical_cases(array $library): array {
    $clinical = scorm_word_clinical_cases_topic();
    if (empty($library[$clinical['course']][$clinical['topic']])) {
        cli_error("Clinical Cases source topic '{$clinical['topic']}' was not found.");
    }
    return [
        'Combination' => [
            'Handbook of Oriental Medicine — Clinical Cases' =>
                $library[$clinical['course']][$clinical['topic']],
        ],
    ];
}

/**
 * Remove the Simulation collection from the general Herb library.
 *
 * @param array $library Grouped library.
 * @return array Filtered library.
 */
function scorm_word_without_simulations(array $library): array {
    unset($library['Herbal Medicine Simulation Exams']);
    return $library;
}

/**
 * Build the standalone Simulation library from the Herb source folder.
 *
 * @param array $library Grouped Herb library.
 * @return array Standalone Simulation library.
 */
function scorm_word_only_simulations(array $library): array {
    $sourcecourse = 'Herbal Medicine Simulation Exams';
    if (empty($library[$sourcecourse])) {
        cli_error("Simulation source collection '{$sourcecourse}' was not found.");
    }
    return [$sourcecourse => $library[$sourcecourse]];
}

/**
 * Remove mnemonic resources from the general Herb library.
 *
 * @param array $library Grouped library.
 * @return array Filtered library.
 */
function scorm_word_without_mnemonics(array $library): array {
    unset($library['Playing Herbology Module Exams']);
    return $library;
}

/**
 * Build the standalone mnemonic-resource library.
 *
 * @param array $library Grouped Herb library.
 * @return array Standalone mnemonic library.
 */
function scorm_word_only_mnemonics(array $library): array {
    $sourcecourse = 'Playing Herbology Module Exams';
    if (empty($library[$sourcecourse])) {
        cli_error("Mnemonic source collection '{$sourcecourse}' was not found.");
    }
    return [$sourcecourse => $library[$sourcecourse]];
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
 * @param int $bookcmid Book course-module ID.
 * @param int $chapterid Book chapter ID.
 * @return string
 */
function scorm_word_exam_links(array $files, int $bookcmid, int $chapterid): string {
    $items = [];
    foreach ($files as $file) {
        $filecontext = context::instance_by_id($file->get_contextid(), MUST_EXIST);
        if ($filecontext->contextlevel !== CONTEXT_MODULE) {
            throw new coding_exception('A generated Word file is not stored in a module context.');
        }
        $url = new moodle_url('/mod/scorm/studytopic/scorm_word_view.php', [
            'cmid' => $filecontext->instanceid,
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
        'Select an exam key to review its questions, correct answers and available feedback.'
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
 * Return a student-facing collection name for a source course.
 *
 * @param string $sourcecourse Original source course name.
 * @return string
 */
function scorm_word_collection_name(string $sourcecourse): string {
    $names = [
        'Herbal Comp Review Module Exams' => 'Herbal Combinations Review',
        'Herbal Medicine Simulation Exams' => 'Herbal Medicine Simulations',
        'Playing Herbology Module Exams' => 'Herbology Mnemonic Resources',
        'Single Herbs Exams' => 'Single Herbs',
        'Formulas Exams' => 'Formulas',
    ];
    return $names[$sourcecourse] ?? $sourcecourse;
}

/**
 * Return a short explanation for a student-facing collection.
 *
 * @param string $sourcecourse Original source course name.
 * @return string
 */
function scorm_word_collection_description(string $sourcecourse): string {
    $descriptions = [
        'Playing Herbology Module Exams' =>
            'Memory aids connecting numbered plates, herb properties, pinyin and formula maps.',
    ];
    return $descriptions[$sourcecourse] ?? '';
}

/**
 * Build the student-facing description shown on generated Exam Key Books.
 *
 * @param string $bookname Book activity name.
 * @return string
 */
function scorm_word_book_intro(string $bookname): string {
    if (str_starts_with($bookname, 'Handbook of Oriental Medicine')) {
        return '<p data-scorm-word-book="2"><strong>Handbook of Oriental Medicine — Clinical Cases.</strong> ' .
            'Open the available clinical case answer key directly.</p>';
    } else if (str_starts_with($bookname, 'Herbology Mnemonic')) {
        return '<p data-scorm-word-book="2"><strong>Herbology Mnemonic Resources.</strong> ' .
            'Review memory aids connecting numbered plates, herb properties and pinyin.</p>';
    } else if (str_starts_with($bookname, 'Combination')) {
        $subject = 'Combination';
    } else if (str_starts_with($bookname, 'Simulation')) {
        $subject = 'Simulation';
    } else {
        $subject = str_starts_with($bookname, 'Herb') ? 'Herb' : 'Formula';
    }
    return '<p data-scorm-word-book="2"><strong>' . $subject . ' Exam Key Library.</strong> ' .
        'Choose a collection, then a topic, to review questions, correct answers and available feedback.</p>';
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
        $header = html_writer::tag(
            'h4',
            s(scorm_word_collection_name($sourcecourse)),
            ['class' => 'mb-0']
        );
        $description = scorm_word_collection_description($sourcecourse);
        $descriptionhtml = $description === ''
            ? ''
            : html_writer::tag('p', s($description), ['class' => 'card-text mb-2']);
        $summary = html_writer::tag(
            'p',
            "{$examcount} answer keys organized into {$sectioncount} topics",
            ['class' => 'card-text text-muted']
        );
        $button = scorm_word_chapter_link(
            $bookcmid,
            $coursechapters[$sourcecourse]->id,
            'View topics',
            ['class' => 'btn btn-primary']
        );
        $cards[] = html_writer::div(
            html_writer::div($header, 'card-header') .
            html_writer::div($descriptionhtml . $summary . $button, 'card-body'),
            'card h-100'
        );
    }

    $columns = [];
    foreach ($cards as $card) {
        $columns[] = html_writer::div($card, 'col-12 col-md-6 mb-3');
    }
    return html_writer::tag(
        'p',
        'Choose a collection. Then select a topic to find the exam key you want to review.'
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
            count($files) . ' answer keys',
            ['class' => 'badge badge-light ml-2']
        );
        $items[] = html_writer::tag(
            'li',
            $label . $count,
            ['class' => 'list-group-item d-flex justify-content-between align-items-center']
        );
    }
    return html_writer::tag('p', 'Choose a topic to see its available exam keys.') .
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
        $collectionname = scorm_word_collection_name($sourcecourse);
        $validtitles = [$sourcecourse, $collectionname];
        if ($sourcecourse === 'Playing Herbology Module Exams') {
            $validtitles[] = 'Interactive Herbology Review';
        }
        if (!$coursechapter || $coursechapter->subchapter ||
                $coursechapter->importsrc !== 'scorm-word-library' ||
                !in_array($coursechapter->title, $validtitles, true)) {
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
        $coursechapter = scorm_word_add_chapter(
            $book,
            $pagenum++,
            false,
            scorm_word_collection_name($sourcecourse),
            ''
        );
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
 * @param array $library Grouped Word library.
 * @param string $bookname Student-facing Book name.
 * @return void
 */
function scorm_word_refresh_book(
    stdClass $book,
    int $bookcmid,
    context_module $context,
    array $library,
    string $bookname
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
    if (str_starts_with($bookname, 'Combination')) {
        $map['index']->title = 'Combination Exam Key Index';
    } else if (str_starts_with($bookname, 'Herbology Mnemonic')) {
        $map['index']->title = 'Herbology Mnemonic Resources Index';
    } else if (str_starts_with($bookname, 'Simulation')) {
        $map['index']->title = 'Simulation Exam Key Index';
    } else {
        $map['index']->title = str_starts_with($bookname, 'Herb')
            ? 'Herb Exam Key Index'
            : 'Formula Exam Key Index';
    }
    $map['index']->content = scorm_word_main_index($library, $bookcmid, $map['courses']);
    $map['index']->timemodified = $now;
    $DB->update_record('book_chapters', $map['index']);

    foreach ($library as $sourcecourse => $sections) {
        $coursechapter = $map['courses'][$sourcecourse];
        $coursechapter->title = scorm_word_collection_name($sourcecourse);
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
                $bookcmid,
                $sectionchapter->id
            );
            $sectionchapter->timemodified = $now;
            $DB->update_record('book_chapters', $sectionchapter);
        }
    }

    $book->name = $bookname;
    $book->intro = scorm_word_book_intro($bookname);
    $book->introformat = FORMAT_HTML;
    $book->revision++;
    $book->timemodified = $now;
    $DB->update_record('book', $book);
    book_preload_chapters($book);
}

/**
 * Refresh a one-page Book that opens directly to its answer-key list.
 *
 * @param stdClass $book Book record.
 * @param int $bookcmid Book course-module ID.
 * @param context_module $context Book context.
 * @param array $library Library containing exactly one topic.
 * @param string $bookname Student-facing Book name.
 * @return void
 */
function scorm_word_refresh_flat_book(
    stdClass $book,
    int $bookcmid,
    context_module $context,
    array $library,
    string $bookname
): void {
    global $DB;

    $files = [];
    foreach ($library as $sections) {
        foreach ($sections as $topicfiles) {
            $files = array_merge($files, $topicfiles);
        }
    }
    if (!$files) {
        cli_error("Flat Book '{$bookname}' does not contain any answer keys.");
    }

    $chapters = array_values($DB->get_records(
        'book_chapters',
        ['bookid' => $book->id],
        'pagenum ASC'
    ));
    if (!$chapters) {
        $chapter = scorm_word_add_chapter($book, 1, false, $bookname, '');
        $chapter->importsrc = 'scorm-word-library-index';
        \mod_book\event\chapter_created::create_from_chapter($book, $context, $chapter)->trigger();
    } else if (count($chapters) === 1 &&
            $chapters[0]->importsrc === 'scorm-word-library-index') {
        $chapter = $chapters[0];
    } else {
        cli_error("Flat Book '{$bookname}' has an unexpected generated structure.");
    }

    $now = time();
    $chapter->pagenum = 1;
    $chapter->subchapter = 0;
    $chapter->title = $bookname;
    $chapter->content = scorm_word_exam_links($files, $bookcmid, $chapter->id);
    $chapter->contentformat = FORMAT_HTML;
    $chapter->timemodified = $now;
    $DB->update_record('book_chapters', $chapter);

    $book->name = $bookname;
    $book->intro = scorm_word_book_intro($bookname);
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
        'bookname' => 'Formula Exam Key Library',
        'legacybookname' => 'Formula Exams — Online Book',
        'expected' => 432,
        'scope' => 'formulas',
        'flat' => false,
        'visibleoncoursepage' => 1,
    ],
    [
        'foldername' => 'Herb Exams — Word Downloads',
        'bookname' => 'Herb Exam Key Library',
        'legacybookname' => 'Herb Exams — Online Book',
        'expected' => 950,
        'scope' => 'herbs',
        'flat' => false,
        'visibleoncoursepage' => 1,
    ],
    [
        'foldername' => 'Herb Exams — Word Downloads',
        'bookname' => 'Handbook of Oriental Medicine — Clinical Cases',
        'legacybookname' => 'Combination Exam Key Library',
        'legacybookname2' => 'Clinical Cases Exam Key Library',
        'expected' => 950,
        'scope' => 'clinicalcases',
        'flat' => true,
        'visibleoncoursepage' => 1,
    ],
    [
        'foldername' => 'Herb Exams — Word Downloads',
        'bookname' => 'Simulation Exam Key Library',
        'legacybookname' => 'Simulation Exam Key Library',
        'expected' => 950,
        'scope' => 'simulations',
        'flat' => false,
        'visibleoncoursepage' => 1,
    ],
    [
        'foldername' => 'Herb Exams — Word Downloads',
        'bookname' => 'Herbology Mnemonic Resources',
        'legacybookname' => 'Herbology Mnemonic Resources',
        'expected' => 950,
        'scope' => 'mnemonics',
        'flat' => false,
        'visibleoncoursepage' => 1,
    ],
];
$plans = [];

foreach ($definitions as $definition) {
    $folder = scorm_word_find_folder($courseid, $sectionid, $definition['foldername']);
    $sourcelibrary = scorm_word_filter_unsupported_topics(scorm_word_library($folder));
    $sourcedocuments = scorm_word_library_count($sourcelibrary);
    if ($sourcedocuments !== $definition['expected']) {
        cli_error(
            "{$definition['foldername']} contains {$sourcedocuments} supported DOCX files; " .
            "expected {$definition['expected']}."
        );
    }
    if ($definition['scope'] === 'formulas') {
        $herbfolder = scorm_word_find_folder(
            $courseid,
            $sectionid,
            'Herb Exams — Word Downloads'
        );
        $herbsourcelibrary = scorm_word_filter_unsupported_topics(
            scorm_word_library($herbfolder)
        );
        if (scorm_word_library_count($herbsourcelibrary) !== 950) {
            cli_error('Herb Exams — Word Downloads must contain 950 supported DOCX files.');
        }
        $library = scorm_word_add_formula_review($sourcelibrary, $herbsourcelibrary);
    } else if ($definition['scope'] === 'herbs') {
        $library = scorm_word_rehome_single_herbs_review(
            scorm_word_without_mnemonics(
                scorm_word_without_simulations(
                    scorm_word_without_formula_review(
                        scorm_word_without_clinical_cases($sourcelibrary)
                    )
                )
            )
        );
    } else if ($definition['scope'] === 'clinicalcases') {
        $library = scorm_word_only_clinical_cases($sourcelibrary);
    } else if ($definition['scope'] === 'simulations') {
        $library = scorm_word_only_simulations($sourcelibrary);
    } else if ($definition['scope'] === 'mnemonics') {
        $library = scorm_word_only_mnemonics($sourcelibrary);
    } else {
        $library = $sourcelibrary;
    }
    $documents = scorm_word_library_count($library);
    $contentchapters = $definition['flat'] ? 0 : scorm_word_chapter_count($library);
    $chapters = $definition['flat'] ? 1 : $contentchapters + 1;
    if (!$definition['flat'] && $contentchapters === 0) {
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
            AND (b.name = :name OR b.name = :legacyname OR b.name = :legacyname2)",
        [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'moduleid' => $bookmodule->id,
            'name' => $definition['bookname'],
            'legacyname' => $definition['legacybookname'],
            'legacyname2' => $definition['legacybookname2'] ?? $definition['legacybookname'],
        ],
        IGNORE_MULTIPLE
    );
    $status = 'create';
    $stalechapters = [];
    if ($existing) {
        if (!str_contains((string)$existing->intro, 'data-scorm-word-book')) {
            cli_error(
                "An unrelated Book already uses the name '{$definition['bookname']}' " .
                "(CM {$existing->cmid})."
            );
        }
        $stalechaptertitles = [];
        if (str_starts_with($definition['bookname'], 'Herb')) {
            foreach (scorm_word_excluded_topics() as $topics) {
                $stalechaptertitles = array_merge($stalechaptertitles, $topics);
            }
        }
        if ($definition['scope'] === 'herbs') {
            $clinical = scorm_word_clinical_cases_topic();
            $stalechaptertitles[] = $clinical['topic'];
            $stalechaptertitles[] = 'Formula';
            $simulationcourse = 'Herbal Medicine Simulation Exams';
            $stalechaptertitles[] = $simulationcourse;
            $stalechaptertitles[] = scorm_word_collection_name($simulationcourse);
            $stalechaptertitles = array_merge(
                $stalechaptertitles,
                array_keys($sourcelibrary[$simulationcourse] ?? [])
            );
            $mnemoniccourse = 'Playing Herbology Module Exams';
            $stalechaptertitles[] = $mnemoniccourse;
            $stalechaptertitles[] = scorm_word_collection_name($mnemoniccourse);
            $stalechaptertitles = array_merge(
                $stalechaptertitles,
                array_keys($sourcelibrary[$mnemoniccourse] ?? [])
            );
        }
        $generatedchapters = $DB->get_records(
            'book_chapters',
            [
                'bookid' => $existing->id,
                'importsrc' => 'scorm-word-library',
            ],
            'pagenum ASC'
        );
        foreach ($generatedchapters as $generatedchapter) {
            if (in_array($generatedchapter->title, $stalechaptertitles, true)) {
                $stalechapters[$generatedchapter->id] = $generatedchapter;
            }
        }

        $plannedtitles = [];
        if (!$definition['flat']) {
            foreach ($library as $sourcecourse => $sections) {
                $plannedtitles[] = scorm_word_collection_name($sourcecourse);
                $plannedtitles = array_merge($plannedtitles, array_keys($sections));
            }
        }
        $currenttitles = [];
        foreach ($generatedchapters as $generatedchapter) {
            if (!isset($stalechapters[$generatedchapter->id])) {
                $currenttitles[] = $generatedchapter->title;
            }
        }
        $indexcount = $DB->count_records('book_chapters', [
            'bookid' => $existing->id,
            'importsrc' => 'scorm-word-library-index',
        ]);
        $existingchapters = $DB->count_records('book_chapters', ['bookid' => $existing->id]);
        if ($existingchapters !== count($generatedchapters) + $indexcount) {
            cli_error(
                "Generated Book '{$definition['bookname']}' contains a chapter not owned by this tool."
            );
        }

        $rebuildstructure = $currenttitles !== $plannedtitles || $indexcount > 1;
        if ($rebuildstructure) {
            $status = 'rebuild generated index for the new subject split';
        } else if ($indexcount === 0) {
            $status = 'add clear index';
        } else if ($indexcount === 1) {
            $status = 'refresh clear index';
        } else {
            cli_error(
                "Generated Book '{$definition['bookname']}' has an invalid generated index."
            );
        }
        if ($stalechapters && !$rebuildstructure) {
            $status = 'move or exclude ' . count($stalechapters) . ' topics and refresh';
        }
    } else {
        $rebuildstructure = false;
    }

    $plans[] = [
        'folder' => $folder,
        'library' => $library,
        'bookname' => $definition['bookname'],
        'documents' => $documents,
        'chapters' => $chapters,
        'status' => $status,
        'existing' => $existing,
        'stalechapters' => $stalechapters,
        'rebuildstructure' => $rebuildstructure,
        'flat' => $definition['flat'],
        'visibleoncoursepage' => $definition['visibleoncoursepage'],
    ];
}

echo "Course: {$course->fullname} ({$course->id})\n";
echo "Section: {$section->name} (database ID {$section->id}, section {$section->section})\n";
foreach ($plans as $plan) {
    echo "{$plan['bookname']}: {$plan['documents']} answer keys, {$plan['chapters']} chapters — " .
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
                'intro' => scorm_word_book_intro($plan['bookname']),
                'introformat' => FORMAT_HTML,
                'numbering' => BOOK_NUM_BULLETS,
                'customtitles' => 0,
                'visible' => 1,
                'visibleoncoursepage' => $plan['visibleoncoursepage'],
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
        $chapterstodelete = $plan['stalechapters'];
        if ($plan['rebuildstructure']) {
            $chapterstodelete = $DB->get_records('book_chapters', ['bookid' => $book->id]);
            foreach ($chapterstodelete as $chapter) {
                if (!in_array(
                    $chapter->importsrc,
                    ['scorm-word-library', 'scorm-word-library-index'],
                    true
                )) {
                    throw new coding_exception(
                        "Refusing to rebuild unrecognised chapter '{$chapter->title}'."
                    );
                }
            }
        }
        if ($chapterstodelete) {
            $filestorage = get_file_storage();
            foreach ($chapterstodelete as $stalechapter) {
                core_tag_tag::remove_all_item_tags('mod_book', 'book_chapters', $stalechapter->id);
                $filestorage->delete_area_files(
                    $context->id,
                    'mod_book',
                    'chapter',
                    $stalechapter->id
                );
                $DB->delete_records('book_chapters', ['id' => $stalechapter->id]);
                \mod_book\event\chapter_deleted::create_from_chapter(
                    $book,
                    $context,
                    $stalechapter
                )->trigger();
                echo "Removed topic from {$plan['bookname']}: {$stalechapter->title}\n";
            }
            book_preload_chapters($book);
        }
        if ($plan['flat']) {
            scorm_word_refresh_flat_book(
                $book,
                $bookcmid,
                $context,
                $plan['library'],
                $plan['bookname']
            );
        } else {
            scorm_word_refresh_book(
                $book,
                $bookcmid,
                $context,
                $plan['library'],
                $plan['bookname']
            );
        }
        $DB->set_field(
            'course_modules',
            'visibleoncoursepage',
            0,
            ['id' => $plan['folder']->cmid]
        );
        $DB->set_field(
            'course_modules',
            'visibleoncoursepage',
            $plan['visibleoncoursepage'],
            ['id' => $bookcmid]
        );
        $transaction->allow_commit();
        $placement = $plan['visibleoncoursepage'] ? 'listed on the Exam Key page' : 'linked from Formulas';
        echo "Updated {$plan['bookname']} (CM {$bookcmid}); {$placement}; source folder is stealth.\n";
    } catch (Throwable $exception) {
        $transaction->rollback($exception);
    }
}

rebuild_course_cache($courseid, true);
echo "Online Books published with clear indexes. Source Word files were not changed.\n";
