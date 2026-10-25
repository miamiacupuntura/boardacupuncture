<?php
// Remove the misplaced anatomy exam from the Biomedicine Notes course index.

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

[$options] = cli_get_params(
    [
        'help' => false,
        'execute' => false,
    ],
    [
        'h' => 'help',
        'x' => 'execute',
    ]
);

if ($options['help']) {
    echo "Remove the misplaced anatomy exam from the Biomedicine Notes index.\n\n";
    echo "The activity and all attempts are preserved; the course module is made stealth.\n\n";
    echo "Options:\n";
    echo "  --execute, -x    Apply the change. Without this option, only show the plan.\n";
    echo "  --help, -h       Show this help.\n";
    exit(0);
}

$notescourseid = 91;
$examcourseid = 36;
$examname = 'Introduction to Human Anatomy (MC) Exam 1 (20 Questions)';

$source = $DB->get_record_sql(
    "SELECT cm.id AS cmid,
            cm.visible,
            cm.visibleoncoursepage,
            s.id AS scormid,
            s.name,
            c.fullname AS coursename
       FROM {scorm} s
       JOIN {course_modules} cm ON cm.instance = s.id
       JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
       JOIN {course} c ON c.id = cm.course
      WHERE cm.course = :courseid
        AND cm.deletioninprogress = 0
        AND s.name = :examname",
    [
        'modulename' => 'scorm',
        'courseid' => $notescourseid,
        'examname' => $examname,
    ],
    MUST_EXIST
);

$examcopies = $DB->get_records_sql(
    "SELECT cm.id AS cmid, s.id AS scormid, s.name
       FROM {scorm} s
       JOIN {course_modules} cm ON cm.instance = s.id
       JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
      WHERE cm.course = :courseid
        AND cm.deletioninprogress = 0
        AND s.name = :examname",
    [
        'modulename' => 'scorm',
        'courseid' => $examcourseid,
        'examname' => $examname,
    ]
);

mtrace("Notes course: {$source->coursename} ({$notescourseid})");
mtrace("Misplaced activity: {$source->name} (CM {$source->cmid})");
mtrace('Matching copies in Biomedicine Exams (course 36): ' . count($examcopies));

if (!(int) $source->visibleoncoursepage) {
    mtrace('No change needed: the activity is already absent from the Notes course index.');
    exit(0);
}

if (!$options['execute']) {
    mtrace('PLAN: make this activity stealth in Biomedicine Notes.');
    mtrace('No activity, package, attempt or grade will be deleted.');
    mtrace('Run again with --execute after reviewing this plan.');
    exit(0);
}

$transaction = $DB->start_delegated_transaction();
$DB->set_field(
    'course_modules',
    'visibleoncoursepage',
    0,
    ['id' => $source->cmid]
);
$transaction->allow_commit();

rebuild_course_cache($notescourseid, true);
mtrace("Removed CM {$source->cmid} from the Biomedicine Notes index.");
mtrace('The SCORM activity and all learner data were preserved.');
