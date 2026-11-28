<?php
// Mirror the audit's hierarchy rule; never inspect answer explanations.
defined('MOODLE_INTERNAL') || die();
function block_myprogress_exam_audit_normalize($text): string {
    $text=strip_tags((string)$text);
    $text=strtr($text,['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n','Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','Ü'=>'u','Ñ'=>'n','ā'=>'a','ē'=>'e','ī'=>'i','ō'=>'o','ū'=>'u','ǎ'=>'a','ě'=>'e','ǐ'=>'i','ǒ'=>'o','ǔ'=>'u']);
    return trim((string)preg_replace('/[^a-z0-9]+/',' ',strtolower($text)));
}
function block_myprogress_exam_audit_is_hierarchy(array $q): bool {
    if(($q['contentType'] ?? '')==='formula_hierarchy'){return true;}
    $stem=block_myprogress_exam_audit_normalize($q['prompt'] ?? '');
    return preg_match('~\b(?:hierarch\w*|jerarqu\w*|jun chen zuo shi|emperador|mensajero)\b|\b(?:chief|deputy|envoy|monarch|minister|assistant)\s+(?:herbs?|ingredients?|role)|(?:matches?|identify|identif\w*|actua como|corresponde|papel|role|as an?)\W+(?:the\W+)?(?:chief|deputy|assistant|envoy|jun|chen|zuo|shi)\b~',$stem)===1
        ||preg_match('~\b(?:which|what|que|cual)\b.{0,100}\b(?:chief|deputy|assistant|envoy)\b(?!\s+complaint)~',$stem)===1;
}
function block_myprogress_exam_audit_snapshot_sets(array $store,array $fallback=[]): array {
    $sets=[];
    foreach(($store['publishedExams'] ?? []) as $key=>$record){
        if(!preg_match('/^simulation_exam_(\d+)$/D',(string)$key,$match)||!is_array($record)||($record['status'] ?? '')!=='published'){continue;}
        $number=(int)$match[1];if($number<1||$number>99){continue;}
        $qs=$record['questions'] ?? null;
        if(!is_array($qs)||count($qs)!==100){continue;}
        $ids=[];foreach($qs as $q){if(is_array($q)&&!empty($q['questionId'])){$ids[(string)$q['questionId']]=true;}}
        if(count($ids)!==100){continue;}
        $sets[(int)$match[1]]=array_values($qs);
    }
    // Legacy sites may not yet have a frozen Exam 1. Never reconstruct later
    // published exams from filtered live-bank inventories.
    if(!isset($store['publishedExams']['simulation_exam_1'])&&count($fallback[1] ?? [])===100){$sets[1]=array_values($fallback[1]);}
    ksort($sets,SORT_NUMERIC);return $sets;
}
