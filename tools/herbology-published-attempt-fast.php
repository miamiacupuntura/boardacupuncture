<?php
// Cache deterministic published display data only, never an attempt or shuffle.
defined('MOODLE_INTERNAL') || die();
function block_menu_fast_attempt_supported(array $store,int $selected): bool {
    $required=array_values(array_unique([1,$selected]));
    foreach($required as $n){
        $record=$store['publishedExams']['simulation_exam_'.$n] ?? null;
        if(!is_array($record)||($record['status'] ?? '')!=='published'||count($record['questions'] ?? [])!==100){return false;}
        $counts=array_fill_keys(['formula_differentiation','single_herb','dui_yao','safety_dispensing','formula_composition','scope_aliases'],0);
        $ids=[];foreach($record['questions'] as $q){if(!is_array($q)||empty($q['questionId'])){return false;}$domain=(string)($q['domainId'] ?? '');if(!array_key_exists($domain,$counts)){return false;}$counts[$domain]++;$ids[(string)$q['questionId']]=true;}
        if(count($ids)!==100||array_values($counts)!==[28,24,18,15,10,5]){return false;}
    }
    return true;
}
function block_menu_fast_published_display(array $record,int $number,string $stamp,string $cachepath,callable $normalize): array {
    $key=hash('sha256',json_encode(['published-display-v1',$stamp,$record],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));
    $file=$cachepath!==''?$cachepath.'/exam-'.$number.'.json':'';
    if($file!==''&&is_readable($file)){
        $cached=json_decode((string)file_get_contents($file),true);
        if(is_array($cached)&&hash_equals($key,(string)($cached['key'] ?? ''))&&is_array($cached['record'] ?? null)
                &&count($cached['record']['questions'] ?? [])===count($record['questions'] ?? [])){
            $GLOBALS['herbology_fast_display_hits']=($GLOBALS['herbology_fast_display_hits'] ?? 0)+1;
            return $cached['record'];
        }
    }
    $GLOBALS['herbology_fast_display_misses']=($GLOBALS['herbology_fast_display_misses'] ?? 0)+1;
    foreach(($record['questions'] ?? []) as $i=>$q){$record['questions'][$i]=$normalize($q);}
    if($file!==''){
        if(!is_dir($cachepath)){@mkdir($cachepath,0770,true);}
        if(is_dir($cachepath)&&is_writable($cachepath)){
            $temp=@tempnam($cachepath,'.display-');
            if($temp!==false){
                $encoded=json_encode(['key'=>$key,'record'=>$record],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
                if(@file_put_contents($temp,$encoded,LOCK_EX)!==false){@chmod($temp,0660);@rename($temp,$file);}
                if(is_file($temp)){@unlink($temp);}
            }
        }
    }
    return $record;
}
function block_menu_fast_exam_one_domains(array $record): array {
    $domains=[];
    foreach($record['questions'] as $i=>$q){
        $q['examTemplate']=['examNumber'=>1,'globalPosition'=>$i+1];
        $domains[(string)$q['domainId']][]=$q;
    }
    return $domains;
}
