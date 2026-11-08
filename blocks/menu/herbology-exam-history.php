<?php
// Saves a completed or interrupted Chinese Herbology practice exam attempt.

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    throw new invalid_parameter_exception('POST is required.');
}

$membershipcourseid = 116;
$membershipcontext = context_course::instance($membershipcourseid);
$hasaccess = is_enrolled($membershipcontext, $USER->id, '', true)
    || has_capability('moodle/course:view', $membershipcontext, $USER->id);

if (!$hasaccess) {
    throw new required_capability_exception($membershipcontext, 'moodle/course:view', 'nopermissions', '');
}

$attemptuid = required_param('attemptuid', PARAM_ALPHANUMEXT);
$examtype = required_param('examtype', PARAM_ALPHANUMEXT);
$score = required_param('score', PARAM_INT);
$total = required_param('total', PARAM_INT);
$duration = required_param('duration', PARAM_INT);
$language = optional_param('language', 'en', PARAM_ALPHA);

$validexamtype = in_array(
    $examtype,
    ['linear', 'adaptive', 'linearincomplete', 'adaptiveincomplete', 'clinicalcases', 'clinicalincomplete'],
    true
);
$isclinicalattempt = in_array($examtype, ['clinicalcases', 'clinicalincomplete'], true);
$isincompleteattempt = in_array(
    $examtype,
    ['linearincomplete', 'adaptiveincomplete', 'clinicalincomplete'],
    true
);
$validtotal = $isclinicalattempt
    ? $total >= 1 && $total <= 500
    : $total === 100;

if (!$validexamtype
        || !$validtotal
        || $score < 0
        || $score > $total
        || core_text::strlen($attemptuid) < 12
        || core_text::strlen($attemptuid) > 64) {
    throw new invalid_parameter_exception('Invalid Herbology exam attempt.');
}

$language = in_array($language, ['en', 'es'], true) ? $language : 'en';
$duration = min(max(0, $duration), 604800);
$table = new xmldb_table('block_menu_exam_attempts');

if (!$DB->get_manager()->table_exists($table)) {
    throw new moodle_exception('The exam history database is not installed.');
}

$existingattempt = $DB->get_record('block_menu_exam_attempts', ['attemptuid' => $attemptuid]);
if ($existingattempt && (int) $existingattempt->userid !== (int) $USER->id) {
    throw new invalid_parameter_exception('Invalid attempt identifier.');
}
if (!$existingattempt) {
    $completedat = time();
    $record = (object) [
        'userid' => $USER->id,
        'courseid' => $membershipcourseid,
        'attemptuid' => $attemptuid,
        'examtype' => $examtype,
        'score' => $score,
        'total' => $total,
        'duration' => $duration,
        'language' => $language,
        'timecompleted' => $completedat,
    ];
    $record->id = (int) $DB->insert_record('block_menu_exam_attempts', $record);
    $existingattempt = $record;
}
$attemptpercentage = (int) round(
    ((int) $existingattempt->score / max(1, (int) $existingattempt->total)) * 100
);
$attemptminutes = intdiv((int) $existingattempt->duration, 60);
$attemptseconds = (int) $existingattempt->duration % 60;
$attemptduration = $attemptminutes > 0
    ? $attemptminutes . ' min ' . $attemptseconds . ' s'
    : $attemptseconds . ' s';

echo json_encode([
    'success' => true,
    'attemptid' => (int) $existingattempt->id,
    'attempt' => [
        'score' => (int) $existingattempt->score,
        'total' => (int) $existingattempt->total,
        'percentage' => $attemptpercentage,
        'completed' => !$isincompleteattempt,
        'passed' => !$isincompleteattempt && $attemptpercentage >= 70,
        'duration' => $attemptduration,
        'timecompleted' => (int) $existingattempt->timecompleted,
        'completedlabel' => userdate(
            (int) $existingattempt->timecompleted,
            get_string('strftimedatetimeshort')
        ),
    ],
]);
