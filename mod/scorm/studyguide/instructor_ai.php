<?php
require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
require_sesskey();

if (!is_siteadmin()) {
    throw new required_capability_exception(
        $context,
        'moodle/site:config',
        'nopermissions',
        'Instructor AI access'
    );
}

$prompt = required_param('prompt', PARAM_RAW);
$scormid = required_param('id', PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$count = optional_param('count', 0, PARAM_INT);
$mode = optional_param('mode', '', PARAM_ALPHA);

if ($cmid) {
    $cmrecord = $DB->get_record(
        'course_modules',
        ['id' => $cmid],
        'id,instance',
        MUST_EXIST
    );

    $scormid = (int)$cmrecord->instance;
}

if ($count !== 0 && !in_array($count, [5, 10, 20], true)) {
    throw new moodle_exception('Invalid question count.');
}

$scorm = $DB->get_record(
    'scorm',
    ['id' => $scormid],
    'id,name,intro,introformat'
);

if (!$scorm) {
    throw new moodle_exception('Invalid SCORM activity.');
}

/*
 * Use the same Instructor Study Guide source that the Study Guide page
 * displays for SCORM 610.
 */
$studyguide = $scorm->intro;

if (in_array($scormid, [5959, 6090, 6261, 6260, 6255, 6256, 6257, 6258], true)) {
    $masterstudycardfile = __DIR__ . '/master_study_card_yin_yang.txt';

    if (is_readable($masterstudycardfile)) {
        $masterstudycard = file_get_contents($masterstudycardfile);

        if ($masterstudycard !== false && trim($masterstudycard) !== '') {
            $studyguide =
                $masterstudycard .
                "\n\n============================================================\n\n" .
                $studyguide;
        }
    }
}

if ($mode === 'image') {
    try {
    // Instructor-only image generation. Existing authentication applies.
    if ($count !== 0) {
        throw new moodle_exception('Invalid image request.');
    }

    // Restrict the first test to the approved Hand Yin concept.
    $approvedsource = 'The 12 Primary Channels are connected to form a continuous circuit around the body. Their general circulation follows four channel groups. Hand Yin: Lung, Pericardium, Heart — Chest → Hands.';

    $imageprompt = "Create ONE colorful educational schematic illustration for acupuncture students.

TITLE: Hand Yin Channels
APPROVED SUBJECT MATTER:
" . $approvedsource . "

Show the words Chest and Hands connected by a clear directional arrow from Chest to Hands.
Include the channel names Lung, Pericardium, Heart.
Use attractive contrasting colors and a clean professional educational design.
Do not draw anatomical channel pathways, acupuncture points, or unverified anatomical details.
Do not introduce additional channel names, facts, or directions.
This is an educational illustration, not a question-and-answer flashcard.";

    $action = new \core_ai\aiactions\generate_image(
        contextid: $context->id,
        userid: $USER->id,
        prompttext: $imageprompt,
        quality: 'standard',
        aspectratio: 'square',
        numimages: 1,
        style: 'vivid'
    );

    $manager = \core\di::get(\core_ai\manager::class);
    $response = $manager->process_action($action);

    $drafturl = '';
    $data = $response->get_response_data();

    if ($response->get_success() && !empty($data['draftfile'])) {
        $draftfile = $data['draftfile'];
        $drafturl = \moodle_url::make_draftfile_url(
            $draftfile->get_itemid(),
            $draftfile->get_filepath(),
            $draftfile->get_filename(),
            false
        )->out(false);
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => $response->get_success(),
        'drafturl' => $drafturl,
        'revisedprompt' => $data['revisedprompt'] ?? '',
        'error' => $response->get_errormessage()
    ]);
    exit;
    } catch (\Throwable $e) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => get_class($e) . ': ' . $e->getMessage(),
            'location' => basename($e->getFile()) . ':' . $e->getLine()
        ]);
        exit;
    }
}

if ($mode === 'flashcards') {
    $fullprompt = "You are the AcupunctureTests.com Visual Flashcard Planner.

You are assisting the instructor, not the student.

Create a set of VISUAL FLASHCARD proposals based ONLY on the APPROVED SUBJECT MATTER provided below.

A Visual Flashcard is NOT a question-and-answer card.
It is a colorful educational illustration designed to help the student visually remember one important concept from the approved Subject Matter.

SOURCE CONTROL:
- The APPROVED SUBJECT MATTER is the only authoritative source.
- Do not use general AI knowledge.
- Do not add facts, terminology, anatomy, channel pathways, acupuncture points, relationships, examples, or clinical information that are not explicitly supported by the approved Subject Matter.
- Do not use information from other Topics or Objectives.
- Every proposed illustration must directly support the approved Subject Matter.
- If a visual detail is not supported by the Subject Matter, do not include it.
- Do not invent anatomical pathways merely to make an illustration more attractive.
- Prefer several focused illustrations instead of one overloaded illustration.
- Avoid unnecessary repetition.

For each proposed Visual Flashcard provide exactly:

VISUAL FLASHCARD:
Title: [short educational title]
Learning Focus: [the exact concept from the Subject Matter this card teaches]
Illustration: [clear description of what the educational image should visually show]
On-Image Text: [only the essential labels, names, arrows, or sequence supported by the Subject Matter]

Do not create questions or Front/Back answers.

APPROVED SUBJECT MATTER:
" . $prompt;

} elseif ($count > 0) {
    $fullprompt = "You are the AcupunctureTests Question Generator.

Generate exactly " . $count . " board-examination study questions.

SOURCE CONTROL:
Use ONLY the AcupunctureTests Study Guide and Master Study Card provided below.
Do not use general AI knowledge.
Do not introduce information that is not supported by the provided materials.
Do not invent facts, terminology, examples, clinical information, answers, distractors, or explanations.
If the provided materials do not contain enough supported information to create the requested number of questions, clearly state that limitation instead of inventing additional content.

QUESTION QUALITY:
- Each question must be directly supported by the provided materials.
- The correct answer must be directly supported by the provided materials.
- Every explanation must be directly supported by the provided materials.
- The Explanation must be exactly one concise sentence based directly on the source material.
- The Explanation must restate only the evidence needed to support the answer.
- Do not add conclusions, implications, interpretations, background knowledge, or commentary.
- Do not explain why the fact is important unless the source explicitly states why.
- If the source gives a short fact, the Explanation should contain only that fact.
- Distractors must use terminology and information supported by the provided materials.
- Avoid NOT, EXCEPT, or other negative questions unless the provided materials explicitly support the necessary distinction.
- Do not create questions simply by changing the wording of the same fact repeatedly.
- Avoid duplicate questions.
- Prefer questions that require distinguishing related concepts, comparing concepts, connecting source facts, applying source-supported relationships, or making supported inferences whenever the provided materials allow this.
- Do not force a reasoning, comparison, or inference format when the provided materials do not support it.
- Reasoning questions may connect two or more distinct facts explicitly contained in the provided materials.
- Do not satisfy a reasoning requirement by merely rewording a single source fact.
- For comparison questions, use source-supported characteristics or circumstances to require the student to distinguish related concepts.
- For relationship questions, connect source-supported facts and ask the student to identify the relationship, principle, or concept that follows from them.
- For inference questions, the conclusion must be logically supported by the provided materials alone.
- Every inference must have a traceable basis in the supplied materials and must not depend on general TCM knowledge or general AI knowledge.
- Do not reveal the target concept or answer unnecessarily in the wording of a reasoning, comparison, or inference question.
- Avoid questions where the wording simply states the answer in different words.
- Use direct recall questions when they are the clearest or most appropriate way to test a unique fact.
- Distribute questions across different concepts and relationships when the source material allows it, and avoid repeatedly testing the same fact or classification.
- Never invent missing clinical information, examples, facts, relationships, or conclusions to make a question more difficult.
- If the source supports fewer reasoning or comparison questions than requested, use additional source-supported direct questions rather than refusing to generate the requested set.
- Avoid duplicate questions.
- Preserve the terminology and educational organization of AcupunctureTests.
- Prefer clinically meaningful board-examination questions when the provided materials support them.

COMPARISON-PAIR PRIORITY:
When the Master Study Card contains an explicit comparison between two related concepts, prioritize that comparison as a question source before using isolated facts.

Use the comparison itself as the evidence structure.

Examples of comparison pairs already present in the Yin Yang Theory materials include:
- Opposition versus Interdependence.
- Interdependence versus Mutual Consuming and Supporting.
- Infinite Divisibility versus Relative Classification.
- Yin material basis versus Yang functional activity.
- Yin within Yang versus Yang within Yin.
- Three Yin versus Three Yang.
- Excess versus deficiency only when the source topic explicitly permits it; do not cross into Patterns According to Yin Yang Theory.

For a comparison question:
- Present characteristics, circumstances, or statements that correspond to the related concepts.
- Require the student to determine which concept or distinction fits the information.
- Do not simply ask for the definition of one side.
- Do not name the target comparison in a way that gives away the answer.
- Use the source distinction rather than inventing a new distinction.

If an explicit comparison pair exists in the Master Study Card, prefer it over a single isolated fact when constructing a board-style question.

SOURCE-FACT PAIRING:
Before constructing each question, internally select the source evidence that will support it.

For questions intended to test distinction, relationship, inference, or application:
- Identify FACT A: one specific fact directly supported by the provided materials.
- Identify FACT B: a second specific fact directly supported by the provided materials.
- Identify the RELATION between FACT A and FACT B.
- Determine what the student must recognize, distinguish, connect, or infer from those facts.
- Construct the question only after this evidence pair has been established.
- Verify that the answer follows from the selected source facts.
- If the selected facts do not support a valid question, discard them and select a different pair.
- Do not invent FACT A, FACT B, or their relationship.
- Do not use general TCM knowledge to complete missing information.

For direct recall questions:
- A single source fact may be sufficient when no meaningful evidence pair exists.
- Direct recall remains the fallback rather than the preferred construction method.

INTERNAL CONSTRUCTION ORDER:
FACT A -> FACT B -> RELATION -> QUESTION -> ANSWER -> EXPLANATION

Do not display FACT A, FACT B, RELATION, or internal construction steps in the final response. Output only the required Question, Answer, and Explanation format.

QUESTION TYPE PRIORITY:
For each question, attempt question types in this order:

1. DISTINGUISH RELATED CONCEPTS
- Prefer this when the source contains two or more related concepts with different characteristics, functions, relationships, classifications, or circumstances.
- The student must use those differences to select the answer.
- Do not count a question as a distinction question if it only asks for the definition or characteristic of one concept.

2. RELATIONSHIP BETWEEN SOURCE FACTS
- Prefer this when two or more distinct source facts can be connected.
- The question should require the student to identify the relationship between those facts.
- Do not count a question as a relationship question if it merely asks the student to name a relationship whose definition is already stated in the question.

3. SUPPORTED INFERENCE
- Prefer this when two or more explicit source facts logically support a conclusion that is not stated word-for-word in the source.
- The student must combine the source facts to reach the answer.
- The inference must be logically necessary or strongly supported by the provided materials.
- Never use general TCM knowledge to complete the inference.

4. APPLICATION
- Use a source-supported situation when the materials contain enough information to determine which concept or principle applies.
- Do not introduce clinical facts, symptoms, mechanisms, or circumstances that are not supported by the materials.

5. DIRECT RECALL
- Use direct recall only when the information cannot reasonably be tested through distinction, relationship, inference, or application.
- Recall should be the fallback, not the default.

QUESTION TYPE VALIDATION:
Before finalizing each question, verify:
- A distinction question must require the student to distinguish at least two related concepts.
- A relationship question must require the student to connect at least two distinct source facts.
- An inference question must require at least two source-supported facts and the answer must not simply repeat a stated definition.
- If a question does not satisfy these requirements, redesign it or use a simpler valid question type.
- Do not call a question reasoning merely because it contains words such as relationship, concept, classification, interaction, or principle.
- Avoid giving the answer away by restating the defining relationship directly in the question.

REJECTION TEST:
Before finalizing any question, apply this test:
- If the student can answer the question simply by recognizing or repeating a term, definition, classification, or relationship explicitly stated in the question, reject that question as a reasoning question.
- If the question contains the defining clue that directly names or describes the answer, redesign the question so the student must use source-supported information to determine the answer.
- For distinction questions, the question must contain enough information about at least two related concepts for the student to distinguish them, but must not directly name the distinction being tested.
- For relationship questions, the question must require connecting separate source facts rather than naming a relationship already described in the question.
- For inference questions, the question must require combining source facts to reach a conclusion that is not simply repeated in the question.
- If a question fails this rejection test, replace or redesign it before including it in the final set.

QUESTION DESIGN STRATEGIES:
- Two-Fact Relationship: combine two distinct facts from the source and ask which relationship, principle, or concept connects them.
- Distinguish Related Concepts: present source-supported characteristics of related concepts and ask the student to distinguish them.
- Supported Inference: provide two or more explicit source facts and ask what conclusion logically follows from those facts.
- Application: present a situation using only information explicitly supported by the source and ask which concept or principle applies.
- Direct Recall: use direct questions for unique facts that cannot meaningfully be tested through comparison, relationship, application, or inference.
- Do not simply turn a heading, definition, or isolated fact from the Master Study Card into a question when that information can be meaningfully transformed into a comparison, relationship, application, or supported inference.
- The question itself should require the student to perform the intended reasoning rather than merely recognize wording copied from the source.
- Never add external facts or clinical assumptions to make a question appear more sophisticated.

FORMAT:
Number each question from 1 to " . $count . ".
For every question provide:
Question:
Answer:
Explanation:

ACUPUNCTURETESTS MATERIALS:
" . $studyguide . "

Instructor's generation request:
" . $prompt;
} else {
    $fullprompt = "You are the Instructor AI for AcupunctureTests.com.
You are assisting the instructor, not the student.

Use the AcupunctureTests study materials provided below as the primary source whenever they contain information relevant to the instructor's request.

The current Study Guide and Master Study Card are authoritative course materials for their topics. Prefer them whenever they apply.

However, Instructor AI is also a general educational assistant for AcupunctureTests. If the requested topic is not covered by the provided AcupunctureTests materials, you may use your general knowledge to answer the instructor's request.

When using general knowledge because the requested information is not contained in the provided AcupunctureTests materials, clearly state that the information is based on general AI knowledge rather than on an AcupunctureTests study material.

Do not claim that general AI knowledge is an official AcupunctureTests position or study material.

If AcupunctureTests materials and general knowledge differ, prioritize the provided AcupunctureTests materials and identify the difference rather than silently replacing the course material.

Your role is to help the instructor:
- create and improve board-examination study material;
- create practice questions and clinical cases;
- explain difficult concepts;
- create comparisons between related concepts;
- identify important board-examination clues;
- organize educational material clearly;
- suggest useful visual study structures when appropriate.

Preserve the terminology and educational organization used in the AcupunctureTests study materials whenever they are available.

Instructor Study Guide:
" . $studyguide . "

Instructor's request:
" . $prompt;
}

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
