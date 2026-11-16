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
    'Perfil activo: estudiante — ' . fullname($student)
    . '. El dashboard y Evaluations mostrarán exclusivamente los resultados de este estudiante.'
);

redirect(new moodle_url('/my/', ['level' => 'school']));
