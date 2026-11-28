<?php
define('MOODLE_INTERNAL',true);
require __DIR__.'/herbology-published-attempt-fast.php';
function check_fast($ok,$message){if(!$ok){throw new RuntimeException($message);}}
$questions=[];$counts=['formula_differentiation'=>28,'single_herb'=>24,'dui_yao'=>18,'safety_dispensing'=>15,'formula_composition'=>10,'scope_aliases'=>5];
foreach($counts as $domain=>$count){for($i=0;$i<$count;$i++){$questions[]=['questionId'=>'Q'.count($questions),'domainId'=>$domain,'prompt'=>'EN','promptEs'=>'ES','correctLabel'=>'B','options'=>[['label'=>'A','text'=>'a'],['label'=>'B','text'=>'b']]];}}
$record=['status'=>'published','version'=>1,'questions'=>$questions];$store=['publishedExams'=>['simulation_exam_1'=>$record,'simulation_exam_2'=>$record]];
check_fast(block_menu_fast_attempt_supported($store,2),'Published path');
check_fast(!block_menu_fast_attempt_supported($store,8),'Missing exam must fall back');
$draft=$store;$draft['publishedExams']['simulation_exam_2']['status']='draft';check_fast(!block_menu_fast_attempt_supported($draft,2),'Draft fallback');
$dir=sys_get_temp_dir().'/herbology-fast-test-'.bin2hex(random_bytes(8));mkdir($dir);$calls=0;
$normalize=function($q)use(&$calls){$calls++;$q['prompt'].=' normalized';return $q;};
try{
 $cold=block_menu_fast_published_display($record,2,'a',$dir,$normalize);check_fast($calls===100,'Cold normalization');
 $warm=block_menu_fast_published_display($record,2,'a',$dir,$normalize);check_fast($calls===100&&$warm===$cold,'Warm cache equality');
 check_fast($record['questions']===$questions,'Input mutation');
 $changed=$record;$changed['questions'][0]['correctLabel']='C';$new=block_menu_fast_published_display($changed,2,'a',$dir,$normalize);check_fast($calls===200&&$new['questions'][0]['correctLabel']==='C','Same-version invalidation');
 block_menu_fast_published_display($changed,2,'b',$dir,$normalize);check_fast($calls===300,'Normalizer invalidation');
 file_put_contents($dir.'/exam-2.json','broken');block_menu_fast_published_display($record,2,'a',$dir,$normalize);check_fast($calls===400,'Corrupt cache fallback');
 $uncached=block_menu_fast_published_display($record,2,'a','',$normalize);check_fast($uncached===$cold,'Cache disabled equality');
 $domains=block_menu_fast_exam_one_domains($cold);check_fast(array_map('count',$domains)===$counts,'Domain quotas');
 $flat=[];foreach($domains as $qs){foreach($qs as $q){$flat[$q['examTemplate']['globalPosition']]=$q['questionId'];}}ksort($flat);check_fast(array_values($flat)===array_column($questions,'questionId'),'Question ordering');
 echo "OK: published cache, invalidation, EN/ES, keys, positions and domain quotas.\n";
}finally{foreach(glob($dir.'/*') as $file){unlink($file);}rmdir($dir);}
