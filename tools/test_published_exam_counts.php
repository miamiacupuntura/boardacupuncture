<?php
// Standalone preflight: counters must reflect the actual frozen question set.
define('MOODLE_INTERNAL',true);
error_reporting(E_ALL);set_error_handler(static function($level,$message){throw new RuntimeException($message);});
require $argv[1];
$qs=[];
for($i=0;$i<100;$i++){$qs[]=['questionId'=>'Q-'.$i,'prompt'=>'Published question','options'=>array_fill(0,4,['label'=>'A','text'=>'Option']),'correctLabel'=>'A','explanation'=>'Saved feedback','source'=>['title'=>'Original source','locator'=>'Question '.$i],'sourceCard'=>['url'=>'/source','urlEs'=>'/source-es']];}
$store=['publishedExams'=>['simulation_exam_1'=>['status'=>'published','questions'=>$qs]]];
if(block_myprogress_published_exam_count($store,1)!==100){throw new RuntimeException('Published counter used live-bank status.');}
unset($store['publishedExams']['simulation_exam_1']['questions'][3]['sourceCard']['url']);
if(block_myprogress_published_exam_count($store,1)!==99){throw new RuntimeException('An unusable question was counted as usable.');}
if(block_myprogress_published_exam_count($store,8)!==null){throw new RuntimeException('An unpublished exam was presented as published.');}
echo "OK: el constructor cuenta preguntas reales de la versión publicada; no fuerza 100 ni mezcla borradores.\n";
