<?php

require_once(__DIR__ . '/../../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);
$prompt = required_param('prompt', PARAM_RAW);

$course = $DB->get_record(
    'course',
    ['id' => $courseid],
    '*',
    MUST_EXIST
);

$section = $DB->get_record(
    'course_sections',
    [
        'id' => $sectionid,
        'course' => $courseid
    ],
    '*',
    MUST_EXIST
);

$context = context_course::instance($courseid);

require_capability('moodle/course:view', $context);
require_sesskey();

$sourcefile = __DIR__ . '/topic_source_' . $sectionid . '.txt';

if (!is_readable($sourcefile)) {
    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'response' => '',
        'error' => 'Topic Study Guide source file is not available.'
    ]);

    exit;
}

$studyguide = file_get_contents($sourcefile);

if ($studyguide === false || trim($studyguide) === '') {
    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'response' => '',
        'error' => 'Topic Study Guide source file is empty.'
    ]);

    exit;
}

$fullprompt = "You are the Instructor AI for AcupunctureTests.com.

You are assisting the instructor, not the student.

The following material is the authoritative AcupunctureTests Topic Study Guide source for this Topic.

SOURCE CONTROL:
Use ONLY the Topic Study Guide source provided below for Topic-specific facts, terminology, answers, explanations, examples, and question content.

Do not use general AI knowledge to add information that is not contained in the source.

Do not invent facts, terminology, examples, clinical information, answers, distractors, or explanations.

If the provided Topic source does not contain enough information to fulfill the instructor's request, clearly state that limitation instead of inventing additional information.

Preserve the terminology and educational organization used in the Topic source.

The Instructor AI may help the instructor:
- create study material;
- improve study material;
- create comparisons;
- explain concepts contained in the source;
- identify relationships and board-examination clues;
- create practice questions;
- organize educational material;
- suggest memory strategies based on the source.

When creating questions:
- Every question must be directly supported by the Topic source.
- The correct answer must be directly supported by the Topic source.
- Every explanation must be directly supported by the Topic source.
- Do not copy an existing exam question word-for-word.
- Do not introduce unsupported information merely to make a question more complete.

TOPIC STUDY GUIDE SOURCE:
============================================================

" . $studyguide . "

============================================================

INSTRUCTOR REQUEST:
" . $prompt;

try {
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
        'error' => $response->get_errormessage()
    ]);

} catch (\Throwable $e) {
    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'response' => '',
        'error' => $e->getMessage()
    ]);
}
