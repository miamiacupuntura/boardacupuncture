<?php
// This file is part of Moodle - http://moodle.org/.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Link the legacy Single Herbs Review Book from Herbology Exam Key.
 *
 * The source Book remains available at its original URL but becomes a stealth
 * activity, so it no longer appears on the Single Herbs Exams course page.
 * A native URL activity is created in Herbology Exam Key. The command is a
 * dry run unless --execute is supplied and is safe to run repeatedly.
 *
 * @package    mod_scorm
 * @copyright  2026 Miami Acupuncture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/resourcelib.php');

global $CFG, $DB;

[$options, $unrecognised] = cli_get_params([
    'execute' => false,
    'help' => false,
], [
    'x' => 'execute',
    'h' => 'help',
]);

if ($unrecognised) {
    cli_error("Unknown options:\n  " . implode("\n  ", $unrecognised), 2);
}

if ($options['help']) {
    echo <<<HELP
Link Single Herbs Review Exam from the Herbology Exam Key section.

The command performs a dry run unless --execute is supplied.

Options:
  -x, --execute    Create the link and make the source Book stealth
  -h, --help       Show this help

Example:
  php rehome_single_herbs_review.php --execute

HELP;
    exit(0);
}

$sourcecmid = 8633;
$targetcourseid = 92;
$targetsectionid = 2020;
$linkname = '9) Single Herbs Review Exam';

$sourcecm = $DB->get_record('course_modules', [
    'id' => $sourcecmid,
    'deletioninprogress' => 0,
], '*', MUST_EXIST);
$sourcemodule = $DB->get_record('modules', ['id' => $sourcecm->module], '*', MUST_EXIST);
if ($sourcemodule->name !== 'book') {
    cli_error("Course module {$sourcecmid} is not a Moodle Book.");
}

$sourcebook = $DB->get_record('book', ['id' => $sourcecm->instance], '*', MUST_EXIST);
if ($sourcebook->name !== 'Single Herbs Review Exam') {
    cli_error(
        "Course module {$sourcecmid} is '{$sourcebook->name}', not 'Single Herbs Review Exam'."
    );
}
$sourcecourse = $DB->get_record('course', ['id' => $sourcecm->course], '*', MUST_EXIST);

$targetcourse = $DB->get_record('course', ['id' => $targetcourseid], '*', MUST_EXIST);
$targetsection = $DB->get_record('course_sections', [
    'id' => $targetsectionid,
    'course' => $targetcourseid,
], '*', MUST_EXIST);
$urlmodule = $DB->get_record('modules', ['name' => 'url'], '*', MUST_EXIST);
if (empty($urlmodule->visible)) {
    cli_error('The Moodle URL activity module is disabled.');
}

$bookurl = (new moodle_url('/mod/book/view.php', ['id' => $sourcecmid]))->out(false);
$matches = $DB->get_records_sql(
    "SELECT cm.id, u.name, u.externalurl
       FROM {course_modules} cm
       JOIN {modules} m
         ON m.id = cm.module
        AND m.name = :modulename
       JOIN {url} u
         ON u.id = cm.instance
      WHERE cm.course = :courseid
        AND cm.section = :sectionid
        AND cm.deletioninprogress = 0
        AND (u.name = :linkname OR u.externalurl = :externalurl)",
    [
        'modulename' => 'url',
        'courseid' => $targetcourseid,
        'sectionid' => $targetsectionid,
        'linkname' => $linkname,
        'externalurl' => $bookurl,
    ]
);

if (count($matches) > 1) {
    cli_error(
        "More than one matching URL activity exists in the target section. " .
        'No changes were made.'
    );
}
$existing = $matches ? reset($matches) : false;

if ($existing && $existing->externalurl !== $bookurl) {
    cli_error(
        "A URL activity named '{$linkname}' already exists but points to " .
        "'{$existing->externalurl}'. No changes were made."
    );
}

echo "Source course: {$sourcecourse->fullname} ({$sourcecourse->id})\n";
echo "Source Book: {$sourcebook->name} (CM {$sourcecmid})\n";
echo "Target course: {$targetcourse->fullname} ({$targetcourse->id})\n";
echo "Target section: {$targetsection->name} (database ID {$targetsectionid})\n";
echo "Link name: {$linkname}\n";
echo "Book URL: {$bookurl}\n";
echo $existing
    ? "Target link already exists (CM {$existing->id}).\n"
    : "Target link will be created.\n";
if ($existing && $existing->name !== $linkname) {
    echo "Existing target link will be renamed from '{$existing->name}' to '{$linkname}'.\n";
}
echo $sourcecm->visibleoncoursepage
    ? "Source Book will be removed from the Single Herbs course page and kept available by URL.\n"
    : "Source Book is already a stealth activity.\n";

if (!$options['execute']) {
    echo "Dry run complete. No Moodle data was changed.\n";
    echo "Run the same command with --execute after reviewing these targets.\n";
    exit(0);
}

$admin = get_admin();
if (!$admin) {
    cli_error('The Moodle administrator account could not be found.');
}
\core\session\manager::set_user($admin);

if (!$existing) {
    $moduleinfo = (object)[
        'modulename' => 'url',
        'module' => $urlmodule->id,
        'section' => (int)$targetsection->section,
        'name' => $linkname,
        'intro' => '<p>Comprehensive review of important herbs and formulas, restricted and ' .
            'pregnancy-contraindicated herbs, toxicity, and other high-yield Herbology references.</p>',
        'introformat' => FORMAT_HTML,
        'externalurl' => $bookurl,
        'display' => RESOURCELIB_DISPLAY_AUTO,
        'printintro' => 0,
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
    $created = add_moduleinfo($moduleinfo, $targetcourse);
    $existing = (object)[
        'id' => (int)$created->coursemodule,
        'name' => $linkname,
        'externalurl' => $bookurl,
    ];
    echo "Created '{$linkname}' (CM {$existing->id}).\n";
} else if ($existing->name !== $linkname) {
    set_coursemodule_name((int)$existing->id, $linkname);
    $existing->name = $linkname;
    echo "Renamed the target link to '{$linkname}'.\n";
}

set_coursemodule_visible($sourcecmid, 1, 0, false);
rebuild_course_cache((int)$sourcecourse->id, true);
rebuild_course_cache($targetcourseid, true);

echo "Single Herbs Review Exam is now stealth in {$sourcecourse->fullname}.\n";
echo "Herbology Exam Key link: " .
    (new moodle_url('/mod/url/view.php', ['id' => $existing->id]))->out(false) . "\n";
echo "Book content remains at: {$bookurl}\n";
