<?php
// Keep Exam 1's student content aligned with its frozen published audit.
defined('MOODLE_INTERNAL') || die();

function block_myprogress_herbology_apply_exam_one_snapshot(array $domains, array $store): array {
    $exam = $store['publishedExams']['simulation_exam_1'] ?? null;
    if (!is_array($exam) || ($exam['status'] ?? '') !== 'published') {
        return $domains;
    }
    $questions = $exam['questions'] ?? null;
    if (!is_array($questions) || count($questions) !== 100) {
        throw new RuntimeException('Published Exam 1 must contain exactly 100 questions.');
    }
    $expected = ['formula_differentiation' => 28, 'single_herb' => 24, 'dui_yao' => 18,
        'safety_dispensing' => 15, 'formula_composition' => 10, 'scope_aliases' => 5];
    $counts = array_fill_keys(array_keys($expected), 0);
    $ids = [];
    foreach ($questions as $question) {
        $id = (string)($question['questionId'] ?? '');
        $domain = (string)($question['domainId'] ?? '');
        if ($id === '' || isset($ids[$id]) || !array_key_exists($domain, $counts)) {
            throw new RuntimeException('Published Exam 1 has duplicate IDs or an invalid domain.');
        }
        $ids[$id] = true;
        $counts[$domain]++;
    }
    if ($counts !== $expected) {
        throw new RuntimeException('Published Exam 1 has invalid domain counts.');
    }
    foreach ($domains as &$domainquestions) {
        $domainquestions = array_values(array_filter($domainquestions,
            static fn(array $question): bool => !isset($ids[(string)($question['questionId'] ?? '')])));
        foreach ($domainquestions as &$question) {
            if (!empty($question['examTemplate']['globalPosition'])) {
                $question['examTemplate']['globalPosition'] = 0;
            }
        }
        unset($question);
    }
    unset($domainquestions);
    foreach ($questions as $position => $question) {
        if (function_exists('block_myprogress_herbology_remove_pinyin_tones_from_question')) {
            $question = block_myprogress_herbology_remove_pinyin_tones_from_question($question);
        }
        $question['examTemplate'] = ['examNumber' => 1, 'globalPosition' => $position + 1];
        $domains[$question['domainId']][] = $question;
    }
    return $domains;
}
