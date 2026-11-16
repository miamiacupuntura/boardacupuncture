<?php
// Opens a recorded assessment under Moodle's explicit supervised student profile.

require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);
require_capability('moodle/user:loginas', $systemcontext);

if (\core\session\manager::is_loggedinas()) {
    throw new moodle_exception('nologinas');
}

$attemptid = required_param('attemptid', PARAM_INT);
$attempttable = new xmldb_table('block_menu_exam_attempts');
if (!$DB->get_manager()->table_exists($attempttable)) {
    throw new moodle_exception('The NCBAHM assessment history is not installed.');
}

$attempt = $DB->get_record('block_menu_exam_attempts', ['id' => $attemptid], '*', MUST_EXIST);
$student = $DB->get_record(
    'user',
    ['id' => (int)$attempt->userid, 'deleted' => 0, 'suspended' => 0],
    'id,firstname,lastname',
    MUST_EXIST
);
if (is_siteadmin($student->id)) {
    throw new moodle_exception('nologinas');
}

$coursecontext = context_course::instance((int)$attempt->courseid);
if (!is_enrolled($coursecontext, $student->id, '', true)) {
    throw new moodle_exception('usernotincourse');
}

\core\session\manager::loginas($student->id, $systemcontext);
\core\notification::info(
    'Active profile: student — ' . fullname($student)
    . '. Answers and results will be saved to this profile.'
);

redirect(new moodle_url('/blocks/menu/exam-history.php', [
    'start' => (string)$attempt->examtype,
    'lang' => 'en',
    'ui' => 'en',
]));
