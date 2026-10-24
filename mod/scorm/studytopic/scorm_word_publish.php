<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Publish a completed SCORM Word export as Moodle folder activities.
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
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/mod/folder/lib.php');

global $CFG, $DB;

[$options, $unrecognised] = cli_get_params([
    'output' => '/var/tmp/formula-herb-word-export',
    'course' => 92,
    'section' => 2020,
    'execute' => false,
    'help' => false,
], [
    'o' => 'output',
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
Publish generated formula and herb Word documents as Moodle folders.

The command performs a dry run unless --execute is provided.

Options:
  -o, --output=PATH   Completed export directory
  -c, --course=ID     Target Moodle course ID (default: 92)
  -s, --section=ID    Target course_sections database ID (default: 2020)
  -x, --execute       Create the two folder activities
  -h, --help          Show this help

Example:
  php scorm_word_publish.php --output=/var/tmp/formula-herb-word-export \\
      --course=92 --section=2020 --execute

HELP;
    exit(0);
}

$output = realpath((string)$options['output']);
if ($output === false || !is_dir($output)) {
    cli_error('Export directory does not exist: ' . $options['output']);
}

$marker = $output . DIRECTORY_SEPARATOR . '.scorm-word-export';
if (!is_file($marker)) {
    cli_error('Export marker is missing; refusing an unrelated directory: ' . $output);
}

$summarypath = $output . DIRECTORY_SEPARATOR . 'export-summary.json';
$summaryjson = is_file($summarypath) ? file_get_contents($summarypath) : false;
$summaries = $summaryjson === false ? null : json_decode($summaryjson, true);
if (!is_array($summaries) || json_last_error() !== JSON_ERROR_NONE) {
    cli_error('Invalid or missing export-summary.json: ' . $summarypath);
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
$foldermodule = $DB->get_record('modules', ['name' => 'folder'], '*', MUST_EXIST);

$definitions = [
    'Formulas' => 'Formula Exams — Word Downloads',
    'Herbs' => 'Herb Exams — Word Downloads',
];
$plans = [];

foreach ($definitions as $category => $activityname) {
    $summary = null;
    foreach ($summaries as $candidate) {
        if (($candidate['category'] ?? '') === $category) {
            $summary = $candidate;
            break;
        }
    }
    if ($summary === null) {
        cli_error("Summary entry is missing for {$category}.");
    }
    if ((int)($summary['errors'] ?? -1) !== 0) {
        cli_error("{$category} export contains errors; refusing to publish.");
    }

    $categoryroot = $output . DIRECTORY_SEPARATOR . $category;
    if (!is_dir($categoryroot)) {
        cli_error("Export category directory is missing: {$categoryroot}");
    }

    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($categoryroot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === 'docx') {
            $files[] = $file->getPathname();
        }
    }
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    $expected = (int)($summary['documents'] ?? -1);
    if ($expected <= 0 || count($files) !== $expected) {
        cli_error(
            "{$category} document count mismatch: summary={$expected}, files=" . count($files)
        );
    }

    $existing = $DB->get_record_sql(
        "SELECT cm.id AS cmid, f.id AS folderid, f.intro
           FROM {course_modules} cm
           JOIN {folder} f ON f.id = cm.instance
          WHERE cm.course = :courseid
            AND cm.section = :sectionid
            AND cm.module = :moduleid
            AND f.name = :name
            AND cm.deletioninprogress = 0",
        [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'moduleid' => $foldermodule->id,
            'name' => $activityname,
        ],
        IGNORE_MULTIPLE
    );

    $status = 'create';
    if ($existing) {
        if (!str_contains($existing->intro, 'data-scorm-word-export')) {
            cli_error(
                "An unrelated activity already uses the name '{$activityname}' (CM {$existing->cmid})."
            );
        }
        $context = context_module::instance($existing->cmid);
        $storedfiles = get_file_storage()->get_area_files(
            $context->id,
            'mod_folder',
            'content',
            0,
            'id',
            false
        );
        $storeddocx = array_filter($storedfiles, static function(stored_file $file): bool {
            return strtolower(pathinfo($file->get_filename(), PATHINFO_EXTENSION)) === 'docx';
        });
        if (count($storeddocx) !== $expected) {
            cli_error(
                "Generated activity '{$activityname}' exists but contains " . count($storeddocx) .
                " of {$expected} DOCX files. It was not changed."
            );
        }
        $status = 'already published';
    }

    $plans[] = [
        'category' => $category,
        'name' => $activityname,
        'root' => $categoryroot,
        'files' => $files,
        'expected' => $expected,
        'status' => $status,
        'existing' => $existing,
    ];
}

echo "Course: {$course->fullname} ({$course->id})\n";
echo "Section: {$section->name} (database ID {$section->id}, section {$section->section})\n";
foreach ($plans as $plan) {
    echo "{$plan['name']}: {$plan['expected']} DOCX — {$plan['status']}\n";
}

if (!$options['execute']) {
    echo "Dry run complete. No Moodle data was changed.\n";
    echo "Run the same command with --execute to publish.\n";
    exit(0);
}

$admin = get_admin();
if (!$admin) {
    cli_error('The Moodle administrator account could not be found.');
}
\core\session\manager::set_user($admin);
$fs = get_file_storage();

foreach ($plans as $plan) {
    if ($plan['status'] === 'already published') {
        echo "Skipping existing verified activity: {$plan['name']}\n";
        continue;
    }

    $draftitemid = file_get_unused_draft_itemid();
    $usercontext = context_user::instance($admin->id);
    try {
        foreach ($plan['files'] as $pathname) {
            $relative = substr($pathname, strlen($plan['root']) + 1);
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
            $dirname = dirname($relative);
            $filepath = $dirname === '.' ? '/' : '/' . trim($dirname, '/') . '/';
            $filerecord = [
                'contextid' => $usercontext->id,
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => $draftitemid,
                'filepath' => $filepath,
                'filename' => basename($relative),
            ];
            $fs->create_file_from_pathname($filerecord, $pathname);
        }

        $moduleinfo = (object)[
            'modulename' => 'folder',
            'module' => $foldermodule->id,
            'section' => (int)$section->section,
            'name' => $plan['name'],
            'intro' => '<p data-scorm-word-export="1">Generated from the source SCORM ' .
                'question banks. Each document contains questions, answers and available feedback.</p>',
            'introformat' => FORMAT_HTML,
            'files' => $draftitemid,
            'display' => FOLDER_DISPLAY_PAGE,
            'showexpanded' => 0,
            'showdownloadfolder' => 1,
            'forcedownload' => 1,
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
        echo "Created {$plan['name']} (CM {$created->coursemodule}).\n";
    } catch (Throwable $exception) {
        $fs->delete_area_files($usercontext->id, 'user', 'draft', $draftitemid);
        throw $exception;
    }
}

rebuild_course_cache($courseid, true);
echo "Publishing complete. No existing activities were replaced or deleted.\n";
