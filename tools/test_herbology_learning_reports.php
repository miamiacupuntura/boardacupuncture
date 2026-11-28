<?php
// Standalone deployment preflight; no production Moodle data is read or written.
define('MOODLE_INTERNAL',true);
error_reporting(E_ALL);
set_error_handler(static function($severity,$message){throw new RuntimeException($message);});
class invalid_parameter_exception extends Exception {}
class moodle_url {private $path;public function __construct($path,$args=[]){$this->path=$path;}public function out($escaped=true){return $this->path;}}
function sesskey(){return 'test-session';}
require $argv[1];
function check($condition,$message){if(!$condition){throw new RuntimeException($message);}}
$items=[];$questions=[];$answers=[];
for($i=0;$i<100;$i++){
    $id='QUESTION-'.$i;$domain=$i<18?'dui_yao':'single_herb';$prompt='Identify the herb for this clinical presentation.';
    if(in_array($i,[18,19],true)){$domain='safety_dispensing';$prompt='Which herb should be avoided because of this contraindication?';}
    if($i===20){$prompt='Which honey-prepared herb is used?';}
    if($i>=21&&$i<=25){$domain='formula_composition';$prompt='Which herb is added in this modification?';}
    $q=['questionId'=>$id,'domainId'=>$domain,'prompt'=>$prompt,'options'=>[['label'=>'A','text'=>'First'],['label'=>'B','text'=>'Second'],['label'=>'C','text'=>'Third'],['label'=>'D','text'=>'Fourth']],'correctLabel'=>'B','explanation'=>'Original explanation'];
    $questions[]=$q;
    $items[]=['id'=>$id,'position'=>$i+1,'domain'=>$domain,'topics'=>block_menu_hlr_classify($q),'prompt'=>$prompt,'options'=>$q['options'],'correct'=>'B','explanation'=>$q['explanation']];
    $answers[]=['id'=>$id,'answer'=>$i<4||($i>=18&&$i<=25)?'A':($i===26?'':'B'),'correct'=>'A'];
}
$snapshot=['items'=>$items,'number'=>1,'version'=>2,'track'=>'simulation','mode'=>'linear','group'=>'group-a','sethash'=>'set-a','lang'=>'en'];
$report=block_menu_hlr_grade($snapshot,array_reverse($answers),str_repeat('a',48));
check($report['overall']===['correct'=>87,'wrong'=>12,'unanswered'=>1,'total'=>100],'Totals or unanswered handling failed');
foreach(['dui_yao'=>4,'precautions'=>2,'processing'=>1,'modifications'=>5] as $key=>$wrong){check($report['topics'][$key]['wrong']===$wrong,'Wrong topic count: '.$key);}
$positioned=$answers;$positioned[0]['position']=9;$positioned[26]['position']=null;
$positionreport=block_menu_hlr_grade($snapshot,$positioned,str_repeat('c',48));
check($positionreport['items'][0]['position']===9&&$positionreport['items'][26]['position']===null&&$positionreport['overall']===$report['overall'],'Adaptive display positions changed grading');
$ambiguous=['domainId'=>'formula_differentiation','prompt'=>'Which formula is appropriate?','explanation'=>'The envoy and processing matter.'];
check(!in_array('hierarchy',block_menu_hlr_classify($ambiguous),true),'Feedback leaked into topic classification');
$bad=$answers;$bad[0]['answer']='X';$blocked=false;
try{block_menu_hlr_grade($snapshot,$bad,str_repeat('b',48));}catch(invalid_parameter_exception $e){$blocked=true;}check($blocked,'Invalid label accepted');
$bad=$answers;$bad[1]['id']=$bad[0]['id'];$blocked=false;
try{block_menu_hlr_grade($snapshot,$bad,str_repeat('b',48));}catch(invalid_parameter_exception $e){$blocked=true;}check($blocked,'Duplicate ID accepted');
$bad=$answers;$bad[0]['id']='UNKNOWN';$blocked=false;
try{block_menu_hlr_grade($snapshot,$bad,str_repeat('b',48));}catch(invalid_parameter_exception $e){$blocked=true;}check($blocked,'Unknown ID accepted');
$SESSION=(object)[];$USER=(object)['id'=>42];$CFG=(object)[];
$store=['publishedExams'=>['simulation_exam_1'=>['version'=>2,'questions'=>$questions]]];
$config=block_menu_hlr_boot($questions,false,'simulation','linear',1,$store);
check($config['active']&&count($config['items'])===100,'Attempt snapshot did not start');
$token=$config['token'];$questions[0]['correctLabel']='A';
check($SESSION->herbology_learning_snapshots[$token]['items'][0]['correct']==='B','Snapshot mutated with later bank changes');
$questions[0]['correctLabel']='B';
$again=block_menu_hlr_boot(array_reverse($questions),true,'simulation','linear',1,$store);
check($SESSION->herbology_learning_snapshots[$token]['group']===$SESSION->herbology_learning_snapshots[$again['token']]['group'],'Order/language changed comparable group');
check(!isset($config['items'][0]['correct'])&&!isset($config['items'][0]['explanation']),'Pre-exam config exposed grading detail');
echo "OK: 4 errores Dui Yao, 2 precauciones, 1 procesamiento y 5 modificaciones; sin responder separados; nota calculada en servidor; versión congelada.\n";
