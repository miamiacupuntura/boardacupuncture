<?php
// Returns the active assessment identity so static quizzes can label supervised sessions.

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

require_login();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$isstudentprofile = \core\session\manager::is_loggedinas() && !empty($USER->realuser);
$administratorname = '';
if ($isstudentprofile) {
    $administrator = $DB->get_record(
        'user',
        ['id' => (int)$USER->realuser],
        'id,firstname,lastname',
        IGNORE_MISSING
    );
    if ($administrator) {
        $administratorname = fullname($administrator);
    }
}

echo json_encode([
    'supervised' => $isstudentprofile,
    'student' => $isstudentprofile ? fullname($USER) : '',
    'administrator' => $administratorname,
    'exiturl' => $isstudentprofile ? (new moodle_url('/course/loginas.php', [
        'id' => 116,
        'sesskey' => sesskey(),
    ]))->out(false) : '',
]);
