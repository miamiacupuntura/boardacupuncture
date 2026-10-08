<?php

require_once(__DIR__ . '/../../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);
$questiontype = required_param('questiontype', PARAM_ALPHANUMEXT);
$count = required_param('count', PARAM_INT);
$sourcequestion = optional_param('source_question', '', PARAM_RAW);

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

$allowedtypes = [
    'multiple_choice' => 'Multiple Choice',
    'true_false' => 'True / False',
    'matching' => 'Matching',
    'visual_identification' => 'Visual Identification',
    'mock' => 'Mock'
];

if (!isset($allowedtypes[$questiontype])) {
    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'questions' => [],
        'error' => 'Invalid question type.'
    ]);

    exit;
}

if ($count < 1 || $count > 50) {
    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'questions' => [],
        'error' => 'Question count must be between 1 and 50.'
    ]);

    exit;
}

$sourcefile = __DIR__ . '/topic_source_' . $sectionid . '.txt';

if (!is_readable($sourcefile)) {
    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'questions' => [],
        'error' => 'Topic Study Guide source file is not available.'
    ]);

    exit;
}

$studyguide = file_get_contents($sourcefile);

if ($studyguide === false || trim($studyguide) === '') {
    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'questions' => [],
        'error' => 'Topic Study Guide source file is empty.'
    ]);

    exit;
}

$typename = $allowedtypes[$questiontype];

// Prepare Point Location library retrieval context.
// The Topic Study Guide remains the authoritative factual source.
$retrievalquery = trim($section->name);
$retrievaltopic = trim($section->name);

if ($questiontype === 'mock' && trim($sourcequestion) !== '') {
    $sourcedata = json_decode($sourcequestion, true);

    if (is_array($sourcedata)) {
        $queryparts = [];

        if (!empty($sourcedata['question'])) {
            $queryparts[] = trim((string)$sourcedata['question']);
        }

        if (!empty($sourcedata['question_focus'])) {
            $queryparts[] = trim((string)$sourcedata['question_focus']);
        }

        if (!empty($sourcedata['related_concepts']) &&
                is_array($sourcedata['related_concepts'])) {
            foreach ($sourcedata['related_concepts'] as $concept) {
                if (is_scalar($concept) && trim((string)$concept) !== '') {
                    $queryparts[] = trim((string)$concept);
                }
            }
        }

        if (!empty($queryparts)) {
            $retrievalquery = implode(' ', $queryparts);
        }
    }
}

// Retrieve a small set of relevant questions from the Point Location library.
// Retrieval failure must never prevent normal question generation.
$retrievedcontext = '';

$retrieverpython = '/usr/bin/python3';
$retrieverscript = __DIR__ . '/pointlocation_library/retrieve.py';
$retrieverlimit = 5;

if ($retrievalquery !== '' && is_readable($retrieverscript)) {
    $retrievercommand =
        escapeshellarg($retrieverpython) . ' ' .
        escapeshellarg($retrieverscript) . ' ' .
        escapeshellarg($retrievalquery) . ' ' .
        escapeshellarg($retrievaltopic) . ' ' .
        escapeshellarg((string)$retrieverlimit) .
        ' 2>&1';

    $retrieveroutput = [];
    $retrieverreturncode = 0;

    exec(
        $retrievercommand,
        $retrieveroutput,
        $retrieverreturncode
    );

    if ($retrieverreturncode === 0 && !empty($retrieveroutput)) {
        $retrievedcontext = trim(implode("\n", $retrieveroutput));
    }
}

$visualsource = [];
$visualimages = [];

if ($questiontype === 'visual_identification') {

    $visualscorms = $DB->get_records_sql(
        "SELECT s.id, s.name, s.revision, cm.id AS cmid
           FROM {scorm} s
           JOIN {course_modules} cm ON cm.instance = s.id
           JOIN {modules} m ON m.id = cm.module AND m.name = 'scorm'
          WHERE cm.course = ?
            AND cm.section = ?
          ORDER BY cm.id",
        [$courseid, $sectionid]
    );

    $fs = get_file_storage();

    foreach ($visualscorms as $visualscorm) {

        $modulecontext = context_module::instance($visualscorm->cmid);

        $quizfile = $fs->get_file(
            $modulecontext->id,
            'mod_scorm',
            'content',
            0,
            '/',
            'QUIZDATA.json'
        );

        if (!$quizfile) {
            continue;
        }

        $quizdata = json_decode($quizfile->get_content(), true);

        if (!is_array($quizdata) || empty($quizdata['Questions']) || !is_array($quizdata['Questions'])) {
            continue;
        }

        foreach ($quizdata['Questions'] as $visualquestion) {

            if (
                !is_array($visualquestion) ||
                empty($visualquestion['ImageURLs']) ||
                !is_array($visualquestion['ImageURLs']) ||
                ($visualquestion['__type'] ?? '') !== 'MultipleChoiceQuestion'
            ) {
                continue;
            }

            foreach ($visualquestion['ImageURLs'] as $imagefilename) {

                $imagefilename = trim((string)$imagefilename);

                if ($imagefilename === '') {
                    continue;
                }

                $visualsource[] = [
                    'image_filename' => $imagefilename,
                    'question' => (string)($visualquestion['QuestionText'] ?? ''),
                    'options' => array_values($visualquestion['Answers'] ?? []),
                    'answer' => (string)($visualquestion['RightAnswer'] ?? '')
                ];

                $visualimages[$imagefilename] = [
                    'contextid' => $modulecontext->id,
                    'revision' => (int)$visualscorm->revision
                ];
            }
        }

        if (!empty($visualsource)) {
            break;
        }
    }
}

if ($questiontype === 'visual_identification' && !empty($visualsource)) {
    $count = min($count, count($visualsource));
}

$fullprompt = "You are the AcupunctureTests.com Question Generator.

You are assisting the instructor, not the student.

Create {$count} new {$typename} question(s) based ONLY on the authoritative Topic Study Guide source provided below.

SOURCE CONTROL:
- Every question must be directly supported by the Topic source.
- The correct answer must be directly supported by the Topic source.
- Every explanation must be directly supported by the Topic source.
- Use only terminology, facts, relationships, sequences, examples, and visual-identification information contained in the Topic source.
- Do not use general AI knowledge to add information that is not contained in the source.
- Do not invent facts, terminology, clinical information, answers, distractors, or explanations.
- Do not copy an existing exam question word-for-word.
- Create NEW questions that test the source material in a different way.
- If the Topic source does not contain enough information to create the requested number of valid questions, create only the number that can be fully supported and report that limitation.

QUESTION TYPE:
{$typename}

OUTPUT FORMAT:
Return ONLY valid JSON.
Do not use Markdown.
Do not add introductory or closing text.

For Multiple Choice, return:
{
  \"question_type\": \"multiple_choice\",
  \"questions\": [
    {
      \"question\": \"...\",
      \"options\": [\"...\", \"...\", \"...\", \"...\"],
      \"answer\": \"...\",
      \"explanation\": \"...\",
      \"choice_feedback\": {
        \"A\": \"...\",
        \"B\": \"...\",
        \"C\": \"...\",
        \"D\": \"...\"
      }
    }
  ]
}

For True / False, return:
{
  \"question_type\": \"true_false\",
  \"questions\": [
    {
      \"question\": \"...\",
      \"answer\": \"True or False\",
      \"explanation\": \"...\"
    }
  ]
}

For Matching, return:
{
  \"question_type\": \"matching\",
  \"questions\": [
    {
      \"question\": \"...\",
      \"pairs\": [
        {\"left\": \"...\", \"right\": \"...\"},
        {\"left\": \"...\", \"right\": \"...\"},
        {\"left\": \"...\", \"right\": \"...\"},
        {\"left\": \"...\", \"right\": \"...\"}
      ],
      \"explanation\": \"...\"
    }
  ]
}

MATCHING REQUIREMENTS:
- The pairs array is mandatory.
- Create at least 4 complete matching pairs for every Matching question.
- Every pair MUST contain a non-empty \"left\" value and a non-empty \"right\" value.
- The left and right values must come directly from the Topic Study Guide source.
- The pairs themselves are the answer. Do NOT replace the pairs with a sentence such as \"See pairs for correct matches.\".
- Do NOT include an \"answer\" field for Matching.
- The explanation must be directly supported by the Topic Study Guide source.

For Visual Identification, return:
{
  \"question_type\": \"visual_identification\",
  \"questions\": [
    {
      \"question\": \"...\",
      \"image_filename\": \"EXACT_IMAGE_FILENAME_FROM_VISUAL_REFERENCE_SOURCE\",
      \"options\": [\"...\", \"...\", \"...\", \"...\"],
      \"answer\": \"...\",
      \"explanation\": \"...\"
    }
  ]
}

For Visual Identification:
- Every question MUST include an \"image_filename\" field.
- The \"image_filename\" MUST exactly match one of the image_filename values provided in the VISUAL REFERENCE SOURCE.
- NEVER invent, modify, abbreviate, or guess an image filename.
- The question MUST ask the student to identify the meridian or visual structure shown in the selected image.
- Use the image-specific question, options, and answer from the VISUAL REFERENCE SOURCE when available.
- The answer MUST correspond to the selected image.
- The explanation must be directly supported by the Topic Study Guide source and/or the supplied visual reference data.
- Do not return a Visual Identification question without a valid image_filename.

For Mock, return:
{
  \"question_type\": \"mock\",
  \"questions\": [
    {
      \"question\": \"...\",
      \"question_type\": \"multiple_choice\",
      \"options\": [\"...\", \"...\", \"...\", \"...\"],
      \"answer\": \"...\",
      \"explanation\": \"...\",
      \"domain\": \"...\",
      \"question_focus\": \"...\",
      \"related_concepts\": [\"...\", \"...\"],
      \"next_learning_direction\": \"...\"
    }
  ]
}

MOCK METADATA REQUIREMENTS:
- Every Mock question MUST include all of these fields: domain, question_focus, related_concepts, and next_learning_direction.
- The domain MUST describe the specific knowledge area being tested using terminology supported by the Topic Study Guide.
- The question_focus MUST identify the main reasoning pattern used by the question.
- Use one of these question_focus values when applicable: comparison, multi_attribute_discrimination, sequence, relationship, classification, application, direct_knowledge.
- related_concepts MUST contain concepts from the Topic Study Guide that are meaningfully connected to the question.
- next_learning_direction MUST identify a useful next area of study that is directly supported by the Topic Study Guide.
- Do not invent metadata that is not supported by the Topic Study Guide.
- If the Topic Study Guide does not support meaningful related concepts, return an empty related_concepts array.
- The metadata must remain consistent with the question, options, answer, and explanation.


For Mock, create a board-examination-style simulation using ONLY the Topic Study Guide source.

MOCK RULES:
- Mock is 100% Multiple Choice.
- Every question MUST have exactly 4 answer options.
- Every question MUST have exactly one correct answer.
- Do NOT create True/False questions.
- Do NOT create Matching questions.
- Do NOT create Visual Identification questions.
- Make Mock questions more challenging and exam-like than standard Multiple Choice practice.
- Vary the way the knowledge is tested while remaining strictly within the Topic source.
- Include comparison questions when the Topic source contains related concepts that can be meaningfully distinguished.
- Include sequence, relationship, classification, pathway, function, indication, characteristic, or application questions when explicitly supported by the Topic source.
- When the Topic source supports it, use short clinical-style or board-style scenarios, but do NOT introduce clinical facts that are not present in the Topic source.
- Distractors must be plausible and must be derived from concepts or terminology contained in the Topic source.
- The explanation must clearly state why the correct answer is correct.
- The choice_feedback object is mandatory for every Multiple Choice question and MUST contain exactly four entries: A, B, C, and D.
- choice_feedback.A, choice_feedback.B, choice_feedback.C, and choice_feedback.D must each explain the selected option using only information supported by the Topic Study Guide.
- For the correct option, explain why it is correct and identify the source-supported fact, relationship, classification, pathway, or other evidence that supports it.
- For each incorrect option, explain why it is incorrect by giving the specific source-supported fact, relationship, classification, pathway, or other distinction that makes it incorrect.
- Do not merely write correct or incorrect; provide the educational reason whenever the Topic Study Guide supports it.
- Do not invent reasons for any choice. If the Topic Study Guide does not provide enough information to distinguish an incorrect option, state that the source does not provide enough information to establish that distinction rather than using outside knowledge.
- The four choice_feedback entries must remain consistent with the question, options, answer, and explanation.
- Do not use general AI knowledge to create distractors or answers.
- Do not invent facts, terminology, relationships, clinical information, or explanations.
- Do not copy existing exam questions word-for-word.
- Create NEW questions that test the same source material in different ways.
- Avoid making every question a simple direct-definition or recall question.
- For a Mock of 10 or more questions, aim for approximately 40% comparison/discrimination questions, 30% relationship/sequence/classification/application questions, and 30% direct knowledge questions.
- For a Mock of fewer than 10 questions, still prioritize comparison, discrimination, relationships, and application whenever the Topic source supports them.
- Every question must be written in clear, natural, professional English appropriate for a board-examination question.
- Do not use awkward, meaningless, or contextually inappropriate words or phrases.
- Do not use terminology that is not supported by the Topic source unless it is necessary grammatical language.
- Every question must have one clearly correct answer based on the Topic source.
- For every Mock question, identify the specific domain of knowledge being tested and return it in the domain field.
- For every Mock question, identify the main reasoning pattern used to answer the question and return it in the question_focus field.
- Prefer question_focus values such as comparison, multi_attribute_discrimination, sequence, relationship, classification, application, or direct_knowledge when supported by the question.
- For every Mock question, list meaningful related concepts from the Topic source in the related_concepts array.
- For every Mock question, identify a useful next learning direction supported by the Topic source and return it in next_learning_direction.
- Keep all four metadata fields consistent with the question, options, answer, and explanation.
- Do not invent metadata from general AI knowledge. Every metadata value must be supported by the Topic Study Guide.
- Avoid ambiguous questions that could reasonably have more than one correct answer.
- Avoid incomplete sequences or partial relationships when the source provides a complete sequence or relationship that can be tested.
- When testing a sequence, relationship, classification, or combination, make the tested relationship explicit and meaningful.
- Distractors must be plausible alternatives but clearly incorrect according to the Topic source.
- Do not create distractors that are obviously unrelated to the question.
- Do not make the correct answer identifiable merely because it is longer, more detailed, or differently worded than the distractors.
- Avoid unnecessary repetition of the same concept or question pattern within the same Mock.
- For every Mock question, choice_feedback is mandatory and must contain exactly four entries: A, B, C, and D.
- choice_feedback must explain every answer choice individually.
- For the correct choice, explain why it is correct using the specific fact, classification, relationship, pathway, sequence, or other evidence supported by the Topic Study Guide.
- For each incorrect choice, explain why it is incorrect using the specific distinguishing information supported by the Topic Study Guide.
- Do not merely label a choice as correct or incorrect. Give the educational reason that distinguishes the choice whenever the Topic Study Guide supports it.
- Do not use outside knowledge to explain any answer choice.
- If the Topic Study Guide does not provide enough information to explain why an incorrect choice is wrong, explicitly state that the Topic source does not provide enough information to establish that distinction rather than inventing an explanation.
- The choice_feedback entries must be consistent with the question, all four options, the answer, and the explanation.
- Before returning the questions, internally verify that each question, all four options, the correct answer, the explanation, and all four choice_feedback entries are consistent with the Topic Study Guide.
- Do not place the same question pattern repeatedly in consecutive questions.
- When two or more related concepts appear in the Topic source, prefer questions that require the student to distinguish between them rather than simply recall one isolated fact.
- When the Topic source contains multiple related attributes for the same concept, combine two or more attributes in the question and require the student to select the option containing the complete correct combination.
- Prefer questions that require discrimination between two or more related channels, pathways, classifications, or relationships when the Topic source supports that comparison.
- When a sequence or relationship is tested, make the distractors represent plausible but incorrect arrangements or relationships supported by terminology from the Topic source.
- Write every Mock question as a positive question or positive statement.
- Do NOT use negative question constructions such as NOT, EXCEPT, incorrect, false, least, least likely, not associated, or equivalent negative wording.
- Avoid asking the student to identify what is absent, incorrect, excluded, or opposite.
- Board-style questions should require the student to identify the best answer among plausible alternatives, not merely recognize an isolated definition.
- Every correct answer must be directly supported by the Topic Study Guide.
- Every explanation must be directly supported by the Topic Study Guide.
- The requested number of questions should be generated whenever the Topic source contains enough material.
- Keep the difficulty appropriate for board-examination preparation.

TOPIC STUDY GUIDE SOURCE:
============================================================

{$studyguide}

============================================================";

if ($retrievedcontext !== '') {
    $fullprompt .= "

============================================================
POINT LOCATION QUESTION LIBRARY REFERENCE
============================================================

The following material was retrieved from existing Point Location exam content
because it is relevant to the current Topic or selected source question.

IMPORTANT RULES FOR THIS REFERENCE:
- The Topic Study Guide above remains the ONLY factual authority for generating the new question.
- Use this library reference only to understand relevant concepts, relationships, terminology, and authentic exam-question patterns.
- Do NOT assume a fact is valid merely because it appears in the library reference.
- Every fact used in a new question, answer, distractor, explanation, or feedback must also be supported by the Topic Study Guide.
- Do NOT copy any retrieved question word-for-word.
- Do NOT reproduce a retrieved question by merely changing one channel, point, organ, number, or answer choice.
- Create a genuinely new question while preserving the learning relevance shown by the retrieved material.
- Ignore retrieval metadata such as SCORE, ID, WHY, file names, and package names when writing the new question.

RETRIEVED LIBRARY MATERIAL:
============================================================

{$retrievedcontext}

============================================================";
}

if ($questiontype === 'mock' && trim($sourcequestion) !== '') {

    $fullprompt .= "

============================================================
GENERATE SIMILAR QUESTIONS MODE
============================================================

A previously generated Mock question has been selected as the learning reference.

SOURCE QUESTION:
{$sourcequestion}

Generate new Multiple Choice question(s) that are closely related to the selected question.

SIMILAR QUESTION RULES:
- Preserve the central concept, relationship, comparison, sequence, classification, or application tested by the source question.
- Use the source question metadata to understand the intended learning focus.
- Create NEW questions. Do not copy the source question, its wording, or its answer choices.
- Change the wording, structure, and distractors while testing the same underlying knowledge.
- Keep the same or slightly higher level of difficulty.
- Use only information supported by the current Topic Study Guide.
- Keep exactly 4 options and exactly 1 correct answer.
- Before returning each question, verify ALL FOUR answer choices against the current Topic Study Guide.
- EXACTLY ONE answer choice must fully satisfy the question stem.
- A distractor must be clearly incorrect according to the Topic Study Guide, not merely less specific, less complete, or differently worded.
- If two or more answer choices could correctly satisfy the stem, DISCARD that question and create a new one.
- Never use two members of the same source-defined group as competing choices when the stem describes a property shared by both. For example, if the Topic source states that several channels share the same origin, destination, classification, pathway pattern, or relationship, do not ask which single one has that shared property unless the stem includes another attribute that uniquely distinguishes one answer.
- The explanation and choice_feedback must agree with this validation. Never label an option incorrect if the Topic Study Guide also supports it as correct.
- Keep the question positive and board-examination oriented.
- Avoid negative constructions such as NOT, EXCEPT, incorrect, false, least, least likely, or equivalent wording.
- Preserve meaningful comparison or multi-attribute reasoning when the source question uses it.
- If the source question tests a sequence or relationship, create a new question that tests that same relationship from a different angle.
- If the source question tests multiple attributes, preserve the need to integrate those attributes.
- Do not introduce information from outside the current Topic Study Guide.
- Keep the metadata consistent with the newly generated question.
- The selected question is a learning reference, not a template to copy.

SIMILAR QUESTION QUALITY:
- Generate ONE new Similar question based on the selected source question.
- The new question must test the same central concept, relationship, comparison, sequence, classification, or application as the source question, but from a meaningfully different angle.
- The new question MUST require reasoning beyond isolated direct recall.
- Prefer comparison, multi-attribute discrimination, relationship reasoning, sequence reasoning, classification combined with another supported attribute, or application.
- Do not create a simple substitution by changing only the channel, organ, point, classification, or answer while preserving the same recall pattern.
- When the source question is a simple classification question, use the selected concept as the starting point but expand the new question into a meaningful comparison, relationship, pathway, combination, or application supported by the Topic Study Guide.
- When the source question already tests comparison, multi-attribute reasoning, sequence, relationship, classification, or application, preserve that level of reasoning while testing it from a different angle.
- When supported by the Topic Study Guide, require the student to distinguish between closely related concepts or combine two or more source-supported facts or attributes.
- Treat the source question's related_concepts as the primary conceptual neighborhood for the new question.
- Do not merely repeat the exact fact tested by the source question.
- When the source question is about a classification, prefer comparing related classifications or combining classification with another supported attribute.
- When the source question is about a pathway, prefer comparing pathways, direction, channel category, or related sequence when supported by the Topic.
- When the source question is about a relationship or sequence, test that relationship from a different angle rather than replacing the entities while preserving the identical question pattern.
- Do not use unrelated concepts merely to make the question appear different.

SIMILAR QUESTION OUTPUT REQUIREMENTS:
- Every Similar question MUST use the complete Multiple Choice Mock structure.
- Every Similar question MUST contain exactly 4 options and exactly 1 correct answer.
- Every Similar question MUST contain a non-empty explanation.
- Every Similar question MUST contain a choice_feedback object with exactly four entries: A, B, C, and D.
- choice_feedback.A, choice_feedback.B, choice_feedback.C, and choice_feedback.D MUST all be non-empty.
- For the correct option, choice_feedback must explain why that option is correct using the Topic Study Guide.
- For each incorrect option, choice_feedback must explain the specific source-supported reason why that option is incorrect.
- Do not omit choice_feedback from any Similar question.
- A Similar question is incomplete and must not be returned unless question, 4 options, answer, explanation, and choice_feedback A-D are all present.

============================================================";

}

if ($questiontype === 'visual_identification') {

    if (empty($visualsource)) {
        header('Content-Type: application/json');

        echo json_encode([
            'success' => false,
            'questions' => [],
            'error' => 'No Visual Identification source was found for this Topic.'
        ]);

        exit;
    }

    $fullprompt .= "\n\nVISUAL REFERENCE SOURCE:\n";
    $fullprompt .= "============================================================\n";
    $fullprompt .= json_encode(
        $visualsource,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
    $fullprompt .= "\n============================================================";
}

try {

    $manager = \core\di::get(\core_ai\manager::class);

    /*
     * Matching uses controlled retries so that incomplete AI responses
     * do not cause the entire requested set to fail.
     */
    if ($questiontype === 'matching') {

        $validquestions = [];
        $maxattempts = 5;
        $attempt = 0;

        while (count($validquestions) < $count && $attempt < $maxattempts) {

            $attempt++;
            $remaining = $count - count($validquestions);

            $attemptprompt = $fullprompt;

            if ($attempt > 1) {
                $attemptprompt .= "\n\nRETRY REQUIREMENT:\n";
                $attemptprompt .= "Generate exactly {$remaining} additional NEW Matching question(s).\n";
                $attemptprompt .= "Every question MUST contain at least 4 complete pairs.\n";
                $attemptprompt .= "Do not return incomplete questions.\n";
            }

            $action = new \core_ai\aiactions\generate_text(
                contextid: $context->id,
                userid: $USER->id,
                prompttext: $attemptprompt
            );

            $response = $manager->process_action($action);

            if (!$response->get_success()) {
                continue;
            }

            $generatedcontent = $response->get_response_data()['generatedcontent'] ?? '';
            $decoded = json_decode($generatedcontent, true);

            if (
                !is_array($decoded) ||
                !isset($decoded['questions']) ||
                !is_array($decoded['questions'])
            ) {
                continue;
            }

            foreach ($decoded['questions'] as $question) {

                if (
                    !is_array($question) ||
                    !isset($question['pairs']) ||
                    !is_array($question['pairs'])
                ) {
                    continue;
                }

                $validpairs = [];

                foreach ($question['pairs'] as $pair) {

                    if (
                        is_array($pair) &&
                        isset($pair['left']) &&
                        isset($pair['right']) &&
                        trim((string)$pair['left']) !== '' &&
                        trim((string)$pair['right']) !== ''
                    ) {
                        $validpairs[] = $pair;
                    }
                }

                if (count($validpairs) < 4) {
                    continue;
                }

                $question['pairs'] = $validpairs;
                $validquestions[] = $question;

                if (count($validquestions) >= $count) {
                    break;
                }
            }
        }

        if (count($validquestions) < $count) {
            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'questions' => $validquestions,
                'error' => 'The AI could not generate the requested number of complete Matching questions after multiple attempts.'
            ]);

            exit;
        }

        $validquestions = array_slice($validquestions, 0, $count);

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'question_type' => $questiontype,
            'questions' => $validquestions
        ]);

        exit;
    }

    /*
     * Mock uses controlled retries so that the final result contains
     * exactly the number of questions requested by the user.
     */
    if ($questiontype === 'mock') {

        $validquestions = [];
        $maxattempts = 5;
        $attempt = 0;

        // Similar Questions are Mock requests that include a source question.
        $issimilar = trim($sourcequestion) !== '';

        while (count($validquestions) < $count && $attempt < $maxattempts) {

            $attempt++;
            $remaining = $count - count($validquestions);
            $attemptprompt = $fullprompt;

            if ($attempt > 1) {
                $attemptprompt .= "\n\nMOCK COUNT RETRY REQUIREMENT:\n";
                $attemptprompt .= "Generate exactly {$remaining} additional NEW Mock question(s).\n";
                $attemptprompt .= "Do not repeat questions already generated in this request.\n";
                $attemptprompt .= "Return only the additional questions requested.\n";
            }

            $action = new \core_ai\aiactions\generate_text(
                contextid: $context->id,
                userid: $USER->id,
                prompttext: $attemptprompt
            );

            $response = $manager->process_action($action);

            if (!$response->get_success()) {

                continue;
            }

            $generatedcontent = $response->get_response_data()['generatedcontent'] ?? '';

            $decoded = json_decode($generatedcontent, true);

            if (
                !is_array($decoded) ||
                !isset($decoded['questions']) ||
                !is_array($decoded['questions'])
            ) {
                continue;
            }

            foreach ($decoded['questions'] as $question) {

                if (
                    !is_array($question) ||
                    empty($question['question']) ||
                    !isset($question['options']) ||
                    !is_array($question['options']) ||
                    count($question['options']) !== 4 ||
                    empty($question['answer']) ||
                    empty($question['explanation']) ||
                    !isset($question['choice_feedback']) ||
                    !is_array($question['choice_feedback']) ||
                    empty($question['choice_feedback']['A']) ||
                    empty($question['choice_feedback']['B']) ||
                    empty($question['choice_feedback']['C']) ||
                    empty($question['choice_feedback']['D'])
                ) {
                    continue;
                }

                // Semantic validation applies to Similar Questions.
                if ($issimilar) {

                    $validationprompt = "You are a strict question validator for AcupunctureTests.com.

Evaluate ONE proposed multiple-choice question using ONLY the authoritative Topic Study Guide below.

Do not correct or rewrite the question.
Do not use outside knowledge.
Return ONLY valid JSON in exactly this format:
{\"valid\":true,\"reason\":\"...\"}
or
{\"valid\":false,\"reason\":\"...\"}

A question is valid ONLY if ALL of these conditions are satisfied:
1. The question stem is fully supported by the Topic Study Guide.
2. The correct answer is directly supported by the Topic Study Guide.
3. EXACTLY ONE of the four answer choices satisfies the question stem.
4. Every distractor is clearly incorrect according to the Topic Study Guide.
5. No fact, relationship, classification, pathway, sequence, explanation, or feedback requires information outside the Topic Study Guide.
6. The explanation and all choice_feedback entries are consistent with the Topic Study Guide.
7. If the stem describes a property shared by multiple concepts in the Topic Study Guide, the question must contain enough additional information to identify exactly one answer. Otherwise mark it invalid.
8. Do not accept a question merely because only one of the four displayed options happens to match. If the stem itself falsely presents a shared source-defined property as unique to one concept, mark it invalid.
9. Compare the proposed Similar question with the ORIGINAL SOURCE QUESTION by the TASK THE STUDENT MUST PERFORM, not merely by the words or entities used. Reject it if the student is being asked to retrieve the same TYPE of fact in the same way.
10. ENTITY SUBSTITUTION IS ALWAYS INVALID when the task pattern remains the same. For example, if the original asks for the internal connection of the Lung channel, a proposed question asking for the internal connection of the Stomach channel is INVALID. Lung -> Stomach and LU-LI-ST -> ST-SP-HT do NOT create a new reasoning task; they only substitute entities and answers.
11. A different channel, organ, point, classification, sequence member, correct answer, or set of distractors does NOT by itself make the question meaningfully different. Do not call such substitution a distinct reasoning step.
12. The proposed Similar question must make the student DO something meaningfully different with related knowledge, such as identify a concept from a relationship or clue, compare concepts, discriminate using multiple attributes, connect two source-supported facts, reason through a sequence, or apply a relationship. Different wording or different entities alone is NOT sufficient.

AUTHORITATIVE TOPIC STUDY GUIDE:
============================================================
" . $studyguide . "
============================================================

ORIGINAL SOURCE QUESTION:
============================================================
" . $sourcequestion . "
============================================================

PROPOSED SIMILAR QUESTION:
============================================================
" . json_encode(
                        $question,
                        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                    ) . "
============================================================";

                    $validationaction = new \core_ai\aiactions\generate_text(
                        contextid: $context->id,
                        userid: $USER->id,
                        prompttext: $validationprompt
                    );

                    $validationresponse = $manager->process_action($validationaction);

                    if (!$validationresponse->get_success()) {
                        continue;
                    }

                    $validationcontent =
                        $validationresponse->get_response_data()['generatedcontent'] ?? '';

                    $validationdecoded = json_decode($validationcontent, true);

                    if (
                        !is_array($validationdecoded) ||
                        !array_key_exists('valid', $validationdecoded) ||
                        $validationdecoded['valid'] !== true
                    ) {
                        continue;
                    }
                }


                // Every Similar question must use a recognized reasoning focus.
                if ($issimilar) {

                    $questionfocus = trim((string)($question['question_focus'] ?? ''));

                    $reasoningfocuses = [
                        'comparison',
                        'multi_attribute_discrimination',
                        'sequence',
                        'relationship',
                        'classification',
                        'application'
                    ];

                    if (!in_array($questionfocus, $reasoningfocuses, true)) {
                        continue;
                    }
                }

                $validquestions[] = $question;

                if (count($validquestions) >= $count) {
                    break;
                }
            }
        }

        if (count($validquestions) < $count) {
            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'questions' => $validquestions,
                'error' => 'The AI could not generate the requested number of Mock questions after multiple attempts. Valid complete questions: ' . count($validquestions) . ' of ' . $count . '.'
            ]);

            exit;
        }

        $validquestions = array_slice($validquestions, 0, $count);

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'question_type' => $questiontype,
            'questions' => $validquestions
        ]);

        exit;
    }

    /*
     * Original generation flow for all non-Matching question types.
     */
    $action = new \core_ai\aiactions\generate_text(
        contextid: $context->id,
        userid: $USER->id,
        prompttext: $fullprompt
    );

    $response = $manager->process_action($action);

    $generatedcontent = $response->get_response_data()['generatedcontent'] ?? '';

    if (!$response->get_success()) {
        header('Content-Type: application/json');

        echo json_encode([
            'success' => false,
            'questions' => [],
            'error' => $response->get_errormessage()
        ]);

        exit;
    }

    $decoded = json_decode($generatedcontent, true);

    if (!is_array($decoded) || !isset($decoded['questions']) || !is_array($decoded['questions'])) {
        header('Content-Type: application/json');

        echo json_encode([
            'success' => false,
            'questions' => [],
            'error' => 'The AI response was not valid question JSON.',
            'raw_response' => $generatedcontent
        ]);

        exit;
    }

    if ($questiontype === 'visual_identification') {

        foreach ($decoded['questions'] as &$visualquestion) {

            if (
                !is_array($visualquestion) ||
                empty($visualquestion['image_filename'])
            ) {
                continue;
            }

            $imagefilename = trim((string)$visualquestion['image_filename']);

            if (!isset($visualimages[$imagefilename])) {
                continue;
            }

            $imageinfo = $visualimages[$imagefilename];

            $visualquestion['image_url'] =
                '/pluginfile.php/' .
                (int)$imageinfo['contextid'] .
                '/mod_scorm/content/' .
                (int)$imageinfo['revision'] .
                '/images/' .
                rawurlencode($imagefilename);
        }

        unset($visualquestion);
    }

    header('Content-Type: application/json');

    echo json_encode([
        'success' => true,
        'question_type' => $questiontype,
        'questions' => $decoded['questions']
    ]);

} catch (\Throwable $e) {
    header('Content-Type: application/json');

    echo json_encode([
        'success' => false,
        'questions' => [],
        'error' => $e->getMessage()
    ]);
}
