<?php

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
require_sesskey();

$prompt = required_param('prompt', PARAM_RAW);
$scormid = required_param('id', PARAM_INT);

$scorm = $DB->get_record('scorm', ['id' => $scormid], 'id,intro,introformat');

if (!$scorm) {
    throw new moodle_exception('Invalid SCORM activity.');
}

$studyguide = $scorm->intro;

if ($scormid === 610) {
    $masterstudycardfile = __DIR__ . '/master_study_card_yin_yang.txt';

    if (is_readable($masterstudycardfile)) {
        $masterstudycard = file_get_contents($masterstudycardfile);

        if ($masterstudycard !== false && trim($masterstudycard) !== '') {
            $studyguide = $masterstudycard . "\n\n" . $studyguide;
        }
    }
}

$fullprompt = "You are a study tutor for AcupunctureTests.com.

Use the Instructor Study Guide as the primary source for answering the student's question.

Instructor Study Guide:
" . $studyguide . "

Student's question:
" . $prompt . "

Answer clearly and accurately for a student preparing for an acupuncture board examination. If the answer is not contained in the Instructor Study Guide, do not use outside knowledge to answer it. Clearly tell the student that the answer is not contained in the Instructor Study Guide. Do not invent, infer, or add information that is not supported by the provided study material.";

$action = new \core_ai\aiactions\generate_text(
    contextid: $context->id,
    userid: $USER->id,
    prompttext: $fullprompt
);

$manager = \core\di::get(\core_ai\manager::class);
$response = $manager->process_action($action);

header('Content-Type: application/json');

echo json_encode([
    'success' => $response->get_success(),
    'response' => $response->get_response_data()['generatedcontent'] ?? '',
    'error' => $response->get_errormessage(),
]);
