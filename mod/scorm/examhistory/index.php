<?php
require('../../../config.php');

require_login();

$cmid = optional_param('cmid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$sectionid = optional_param('sectionid', 0, PARAM_INT);

if (!$cmid && !$courseid) {
    throw new moodle_exception('Missing course information');
}

if ($cmid) {
    $cm = get_coursemodule_from_id('scorm', $cmid, 0, false, MUST_EXIST);
    $courseid = $cm->course;
}

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

$section = null;
if ($sectionid) {
    $section = $DB->get_record(
        'course_sections',
        ['id' => $sectionid, 'course' => $courseid],
        'id,course,section,name',
        MUST_EXIST
    );
}
require_login($course);

$PAGE->set_url(new moodle_url('/mod/scorm/examhistory/index.php', [
    'cmid' => $cmid,
    'courseid' => $courseid
]));

$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_title('Exam History');
$PAGE->set_heading('Exam History');

echo $OUTPUT->header();

echo html_writer::tag('h2', 'Exam History');

if ($section) {
    echo html_writer::tag('h3', s($section->name));
}

// Get the SCORM exams that belong to this Topic.
$exams = [];
if ($section) {
    $exams = $DB->get_records_sql(
        "SELECT cm.id AS cmid, cm.instance AS scormid, s.name
           FROM {course_modules} cm
           JOIN {scorm} s ON s.id = cm.instance
          WHERE cm.course = :courseid
            AND cm.section = :sectionid
            AND cm.module = (
                SELECT id FROM {modules} WHERE name = 'scorm'
            )
          ORDER BY cm.id ASC",
        [
            'courseid' => $courseid,
            'sectionid' => $section->id
        ]
    );
}

$backurl = new moodle_url('/course/view.php', ['id' => $courseid]);

echo html_writer::start_div('examhistory-wrapper');

echo html_writer::link(
    $backurl,
    '← Back to Exams List',
    ['class' => 'btn btn-secondary mb-3']
);

echo html_writer::start_tag('div', [
    'style' => 'overflow-x:auto;'
]);

echo html_writer::start_tag('table', [
    'class' => 'generaltable examhistory-table',
    'style' => 'width:100%;'
]);

$headers = ['Exam', 'Attempts', 'Last Attempt', 'Average', 'Grade', 'Action'];

echo html_writer::start_tag('thead');
echo html_writer::start_tag('tr');

foreach ($headers as $header) {
    echo html_writer::tag(
        'th',
        $header,
        ['style' => 'text-align:center;']
    );
}

echo html_writer::end_tag('tr');
echo html_writer::end_tag('thead');

echo html_writer::start_tag('tbody');

foreach ($exams as $exam) {
    $attempts = $DB->get_records(
        'scorm_attempt',
        [
            'scormid' => $exam->scormid,
            'userid' => $USER->id
        ],
        'attempt ASC'
    );

    $attemptcount = count($attempts);
    $scores = [];
    $latestAttemptTime = 0;

    foreach ($attempts as $attempt) {
        $tracks = $DB->get_records(
            'scorm_scoes_value',
            [
                'attemptid' => $attempt->id,
                'elementid' => 3
            ],
            'timemodified DESC'
        );

        foreach ($tracks as $track) {
            $value = trim((string)$track->value);

            if ($value !== '' && is_numeric($value)) {
                $score = (float)$value;

                if ($score >= 0 && $score <= 100) {
                    $scores[] = $score;
                }
            }

            break;
        }

        $lasttrack = $DB->get_record_sql(
            "SELECT MAX(timemodified) AS lasttime
               FROM {scorm_scoes_value}
              WHERE attemptid = :attemptid",
            ['attemptid' => $attempt->id]
        );

        if ($lasttrack && !empty($lasttrack->lasttime)) {
            $attemptTime = (int)$lasttrack->lasttime;

            if ($attemptTime > $latestAttemptTime) {
                $latestAttemptTime = $attemptTime;
            }
        }
    }

    $average = null;
    $grade = 'Pending';

    if (!empty($scores)) {
        $average = array_sum($scores) / count($scores);

        if ($average >= 90) {
            $grade = 'A';
        } else if ($average >= 80) {
            $grade = 'B';
        } else if ($average >= 70) {
            $grade = 'C';
        } else {
            $grade = 'D';
        }
    }

    $lastAttempt = '-';

    if ($latestAttemptTime) {
        $lastAttempt =
            userdate($latestAttemptTime, '%m/%d/%y') .
            '<br>' .
            userdate($latestAttemptTime, '%l:%M %p');
    }

    $averageDisplay = ($average !== null)
        ? number_format($average, 2) . '%'
        : '-';

    $starturl = new moodle_url('/mod/scorm/studyguide/', [
        'id' => $exam->scormid,
        'cmid' => $exam->cmid
    ]);

    echo html_writer::start_tag('tr');

    echo html_writer::tag(
        'td',
        format_string($exam->name),
        ['style' => 'text-align:left;']
    );

    echo html_writer::tag(
        'td',
        (string)$attemptcount,
        ['style' => 'text-align:center;']
    );

    echo html_writer::tag(
        'td',
        $lastAttempt,
        ['style' => 'text-align:center;']
    );

    echo html_writer::tag(
        'td',
        $averageDisplay,
        ['style' => 'text-align:center;']
    );

    echo html_writer::tag(
        'td',
        $grade,
        ['style' => 'text-align:center;']
    );

    echo html_writer::tag(
        'td',
        html_writer::link(
            $starturl,
            'Start Exam',
            ['class' => 'btn btn-primary btn-sm']
        ),
        ['style' => 'text-align:center;']
    );

    echo html_writer::end_tag('tr');
}

if (empty($exams)) {
    echo html_writer::tag(
        'tr',
        html_writer::tag(
            'td',
            'No exams found in this Topic.',
            [
                'colspan' => 6,
                'style' => 'text-align:center;'
            ]
        )
    );
}

echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');
echo html_writer::end_div();

echo html_writer::tag(
    'div',
    html_writer::link(
        $backurl,
        '← Back to Exams List',
        ['class' => 'btn btn-secondary']
    ),
    ['style' => 'margin-top:20px;']
);

echo html_writer::end_div();

echo $OUTPUT->footer();
