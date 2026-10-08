<?php
define('CLI_SCRIPT', true);
require_once('/var/www/html/moodle/config.php');

global $DB;

$courseid = 34;

$sql = "
    SELECT
        f.id AS fileid,
        cm.id AS cmid,
        cm.instance AS instanceid,
        cm.section AS sectionid,
        cs.section AS sectionnumber,
        cs.name AS sectionname,
        s.id AS scormid,
        s.name AS scormname,
        f.filename AS packagefilename,
        f.contenthash
    FROM {course_modules} cm
    JOIN {modules} m
      ON m.id = cm.module
     AND m.name = 'scorm'
    JOIN {context} ctx
      ON ctx.contextlevel = 70
     AND ctx.instanceid = cm.id
    JOIN {files} f
      ON f.contextid = ctx.id
     AND f.component = 'mod_scorm'
     AND f.filearea = 'package'
     AND f.filename <> '.'
    LEFT JOIN {scorm} s
      ON s.id = cm.instance
    LEFT JOIN {course_sections} cs
      ON cs.id = cm.section
    WHERE cm.course = :courseid
    ORDER BY cm.id, f.id
";

$recordset = $DB->get_recordset_sql($sql, ['courseid' => $courseid]);

$records = [];
foreach ($recordset as $record) {
    $records[] = $record;
}
$recordset->close();

echo json_encode(
    $records,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);
echo PHP_EOL;
