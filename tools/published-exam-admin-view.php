<?php
// Published counters use the frozen student version, never live-bank approvals.
defined('MOODLE_INTERNAL') || die();
function block_myprogress_snapshot_usable_count(array $questions): int {
    $ids=[];
    foreach($questions as $q){
        if(!is_array($q)||empty($q['questionId'])||empty($q['prompt'])||empty($q['explanation'])
                ||count($q['options'] ?? [])!==4||empty($q['correctLabel'])
                ||empty($q['source']['title'])||empty($q['source']['locator'])
                ||empty($q['sourceCard']['url'])||empty($q['sourceCard']['urlEs'])){continue;}
        $ids[(string)$q['questionId']]=true;
    }
    return count($ids);
}
function block_myprogress_published_exam_count(array $store, int $number): ?int {
    $exam=$store['publishedExams']['simulation_exam_'.$number] ?? null;
    if(!is_array($exam)||($exam['status'] ?? '')!=='published'||!is_array($exam['questions'] ?? null)){return null;}
    return block_myprogress_snapshot_usable_count($exam['questions']);
}
