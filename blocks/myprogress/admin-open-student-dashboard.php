<?php
// Opens the dashboard under Moodle's explicit supervised student identity.

require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);
require_capability('moodle/user:loginas', $systemcontext);

if (\core\session\manager::is_loggedinas()) {
    throw new moodle_exception('nologinas');
}

$studentid = required_param('userid', PARAM_INT);
$destination = optional_param('destination', 'dashboard', PARAM_ALPHA);
if (!in_array($destination, ['dashboard', 'studyscore'], true)) {
    throw new invalid_parameter_exception('Invalid student destination.');
}
$student = $DB->get_record(
    'user',
    ['id' => $studentid, 'deleted' => 0, 'suspended' => 0],
    'id,firstname,lastname',
    MUST_EXIST
);
if (is_siteadmin($student->id) || (int)$student->id === (int)$USER->id) {
    throw new moodle_exception('nologinas');
}

foreach ([30, 34, 36, 44, 45, 113, 116, 117] as $courseid) {
    $coursecontext = context_course::instance($courseid, IGNORE_MISSING);
    if ($coursecontext
            && (has_capability('moodle/grade:viewall', $coursecontext, $student->id)
                || has_capability('moodle/course:manageactivities', $coursecontext, $student->id))) {
        throw new moodle_exception('nologinas');
    }
}

\core\session\manager::loginas($student->id, $systemcontext);
\core\notification::info(
    'Active profile: student — ' . fullname($student)
    . ($destination === 'studyscore'
        ? '. My Study Score is now open under this student profile.'
        : '. The dashboard and Evaluations will show only this student’s results.')
);

$destinationurl = $destination === 'studyscore'
    ? new moodle_url('/blocks/myprogress/study-score.php')
    : new moodle_url('/my/', ['level' => 'school']);
redirect($destinationurl);
