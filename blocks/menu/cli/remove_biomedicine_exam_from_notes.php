<?php
// Remove the misplaced anatomy exam from the Biomedicine Notes course index.

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/course/lib.php');

[$options] = cli_get_params(
    [
        'help' => false,
        'execute' => false,
        'delete-source' => false,
    ],
    [
        'h' => 'help',
        'x' => 'execute',
        'd' => 'delete-source',
    ]
);

if ($options['help']) {
    echo "Remove the misplaced anatomy exam from the Biomedicine Notes index.\n\n";
    echo "The activity and all attempts are preserved; the course module is made stealth.\n\n";
    echo "Options:\n";
    echo "  --execute, -x    Apply the change. Without this option, only show the plan.\n";
    echo "  --delete-source, -d\n";
    echo "                   Permanently delete the Notes copy only when a Questions copy exists.\n";
    echo "                   Existing Notes attempts must also exist in Questions and are archived\n";
    echo "                   to JSON before deletion. Requires --execute.\n";
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
            s.sha1hash,
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
    "SELECT cm.id AS cmid, s.id AS scormid, s.name, s.sha1hash
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
$sourceattempts = $DB->count_records('scorm_attempt', ['scormid' => $source->scormid]);

mtrace("Notes course: {$source->coursename} ({$notescourseid})");
mtrace("Misplaced activity: {$source->name} (CM {$source->cmid})");
mtrace('Matching copies in Biomedicine Exams (course 36): ' . count($examcopies));
mtrace('Attempts attached to the Notes copy: ' . $sourceattempts);

if ($options['delete-source']) {
    if (!$examcopies) {
        cli_error('Refusing deletion: no matching copy exists in Biomedicine Exams.');
    }
    $target = reset($examcopies);
    $sourceattemptrecords = $DB->get_records(
        'scorm_attempt',
        ['scormid' => $source->scormid],
        'userid ASC, attempt ASC'
    );
    foreach ($sourceattemptrecords as $attempt) {
        if (!$DB->record_exists('scorm_attempt', [
            'scormid' => $target->scormid,
            'userid' => $attempt->userid,
        ])) {
            cli_error(
                "Refusing deletion: user {$attempt->userid} has an attempt in Notes " .
                'but none in Biomedicine Exams.'
            );
        }
    }

    $archivepath = '/var/tmp/biomedicine-notes-cm' . $source->cmid . '-attempts.json';
    if (!$options['execute']) {
        mtrace("PLAN: permanently delete duplicate CM {$source->cmid} from Biomedicine Notes.");
        mtrace("PLAN: archive {$sourceattempts} Notes attempts to {$archivepath}.");
        mtrace('The matching activity in Biomedicine Exams will not be changed.');
        mtrace('Run again with --delete-source --execute after reviewing this plan.');
        exit(0);
    }

    $archive = [
        'created_at' => gmdate('c'),
        'source' => [
            'courseid' => $notescourseid,
            'cmid' => (int) $source->cmid,
            'scormid' => (int) $source->scormid,
            'name' => $source->name,
            'sha1hash' => $source->sha1hash,
        ],
        'target' => [
            'courseid' => $examcourseid,
            'cmid' => (int) $target->cmid,
            'scormid' => (int) $target->scormid,
            'name' => $target->name,
            'sha1hash' => $target->sha1hash,
        ],
        'attempts' => [],
    ];
    foreach ($sourceattemptrecords as $attempt) {
        $user = $DB->get_record('user', ['id' => $attempt->userid], 'id,username', MUST_EXIST);
        $values = $DB->get_records_sql(
            "SELECT v.id,
                    v.scoid,
                    sco.identifier AS scoidentifier,
                    sco.title AS scotitle,
                    e.element,
                    v.value,
                    v.timemodified
               FROM {scorm_scoes_value} v
               JOIN {scorm_scoes} sco ON sco.id = v.scoid
               JOIN {scorm_element} e ON e.id = v.elementid
              WHERE v.attemptid = :attemptid
           ORDER BY v.id ASC",
            ['attemptid' => $attempt->id]
        );
        $archive['attempts'][] = [
            'id' => (int) $attempt->id,
            'userid' => (int) $attempt->userid,
            'username' => $user->username,
            'attempt' => (int) $attempt->attempt,
            'values' => array_values(array_map(
                static fn(stdClass $value): array => (array) $value,
                $values
            )),
        ];
    }
    $archivejson = json_encode(
        $archive,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
    if ($archivejson === false || file_put_contents($archivepath, $archivejson, LOCK_EX) === false) {
        cli_error("Could not write the attempt archive to {$archivepath}; nothing was deleted.");
    }

    course_delete_module((int) $source->cmid, false);
    rebuild_course_cache($notescourseid, true);
    mtrace("Deleted duplicate CM {$source->cmid} from Biomedicine Notes.");
    mtrace("Archived {$sourceattempts} Notes attempts to {$archivepath}.");
    mtrace('The matching activity in Biomedicine Exams was not changed.');
    exit(0);
}

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
