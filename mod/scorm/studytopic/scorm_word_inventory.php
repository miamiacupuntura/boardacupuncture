<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Return the SCORM package inventory used by scorm_word_export.py.
 *
 * This helper is CLI-only and read-only. It deliberately returns file content
 * hashes instead of copying packages out of Moodle's file pool.
 *
 * @package    mod_scorm
 * @copyright  2026 Miami Acupuncture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');

global $CFG, $DB;

$options = getopt('', ['courses:']);
$rawcourses = $options['courses'] ?? '';
$courseids = array_values(array_unique(array_filter(array_map(
    static function(string $value): int {
        return ctype_digit(trim($value)) ? (int)trim($value) : 0;
    },
    explode(',', $rawcourses)
))));

if (empty($courseids)) {
    fwrite(STDERR, "Usage: php scorm_word_inventory.php --courses=117,44,45\n");
    exit(2);
}

[$insql, $inparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');

$sql = "SELECT f.id AS fileid,
               c.id AS courseid,
               c.fullname AS coursename,
               c.timemodified AS coursetimemodified,
               cm.id AS cmid,
               cm.visible,
               cm.visibleoncoursepage,
               cs.id AS sectionid,
               cs.section AS sectionnumber,
               cs.name AS sectionname,
               s.id AS scormid,
               s.name AS scormname,
               f.filename AS packagefilename,
               f.contenthash,
               f.filesize,
               f.timemodified
          FROM {course_modules} cm
          JOIN {modules} m
            ON m.id = cm.module
           AND m.name = :modulename
          JOIN {scorm} s
            ON s.id = cm.instance
          JOIN {course} c
            ON c.id = cm.course
          JOIN {course_sections} cs
            ON cs.id = cm.section
          JOIN {context} ctx
            ON ctx.contextlevel = :modulecontext
           AND ctx.instanceid = cm.id
          JOIN {files} f
            ON f.contextid = ctx.id
           AND f.component = :component
           AND f.filearea = :filearea
           AND f.filename <> :directory
         WHERE cm.course $insql
           AND cm.deletioninprogress = 0
      ORDER BY c.id, cs.section, s.name, f.timemodified DESC, f.id DESC";

$params = array_merge($inparams, [
    'modulename' => 'scorm',
    'modulecontext' => CONTEXT_MODULE,
    'component' => 'mod_scorm',
    'filearea' => 'package',
    'directory' => '.',
]);

$records = [];
$recordset = $DB->get_recordset_sql($sql, $params);

foreach ($recordset as $record) {
    $records[] = $record;
}

$recordset->close();

echo json_encode([
    'dataroot' => $CFG->dataroot,
    'courseids' => $courseids,
    'records' => $records,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
echo PHP_EOL;
