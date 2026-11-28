<?php
// Preflight against stale/filtered inventory: no production data is read.
define('MOODLE_INTERNAL',true);error_reporting(E_ALL);
set_error_handler(static function($level,$message){throw new RuntimeException($message);});
function s($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
class moodle_url{private $url;public function __construct($path,$params){$this->url=$path.'?'.http_build_query($params);}public function out($escaped=true){return $escaped?s($this->url):$this->url;}}
require $argv[1];
$questionrevisionstore=['publishedExams'=>[]];$CFG=(object)[];
for($n=1;$n<=7;$n++){
    $qs=[];
    for($i=0;$i<100;$i++){
        $options=[];foreach(['A','B','C','D'] as $letter){$options[]=['label'=>$letter,'text'=>'Option '.$letter];}
        $q=['questionId'=>'EX'.$n.'-'.$i,'prompt'=>$i<2?'Which herb is the chief?':'Patient with DO_NOT_DISPLAY symptoms.',
            'contentType'=>'single_herb','options'=>$options,'correctLabel'=>'A','explanation'=>'DO_NOT_DISPLAY_FEEDBACK',
            'translations'=>['es'=>['prompt'=>'Pregunta original','options'=>$options,'explanation'=>'Explicación']]];
        if(($n===6&&$i===99)||($n===7&&$i>=95)){$q['translations']['es']['prompt']='';}
        $qs[]=$q;
    }
    $questionrevisionstore['publishedExams']['simulation_exam_'.$n]=['status'=>'published','version'=>3,'questions'=>$qs];
}
$questionrevisionstore['publishedExams']['simulation_exam_8']=['status'=>'draft','questions'=>array_slice($qs,0,82)];
$filtered=[1=>array_slice($qs,0,97),3=>$qs,4=>$qs,5=>$qs];
$sets=block_myprogress_exam_audit_snapshot_sets($questionrevisionstore,$filtered);
if(array_keys($sets)!==range(1,7)){throw new RuntimeException('Frozen published exam cards were omitted.');}
foreach([false,true] as $es){
    ob_start();block_myprogress_render_exam_audit_cards($filtered,$es,'simulation',[],1);$html=ob_get_clean();
    for($n=1;$n<=7;$n++){if(strpos($html,($es?'Examen ':'Exam ').$n.'</h4>')===false){throw new RuntimeException('Missing card '.$n);}}
    $expected=$es?'Jerarquía de ingredientes: 2/100':'Ingredient hierarchy: 2/100';
    if(substr_count($html,$expected)!==7){throw new RuntimeException('Hierarchy counts disagree with the audit.');}
    if(strpos($html,'99/100')===false||strpos($html,'95/100')===false){throw new RuntimeException('Incomplete Spanish was hidden.');}
    foreach(['DO_NOT_DISPLAY','Exam 8</h4>','Examen 8</h4>'] as $bad){if(strpos($html,$bad)!==false){throw new RuntimeException('Private content/draft leaked.');}}
}
if(block_myprogress_exam_audit_is_hierarchy(['prompt'=>'Which chief complaint is typical?'])
 ||block_myprogress_exam_audit_is_hierarchy(['prompt'=>'Select a formula.','explanation'=>'Chief ingredient role.'])){
    throw new RuntimeException('Incidental chief/feedback falsely counted as hierarchy.');
}
echo "OK: siete fichas publicadas pese al inventario filtrado; jerarquía 2/100 real; español 99/100 y 95/100; borrador y respuestas excluidos.\n";
