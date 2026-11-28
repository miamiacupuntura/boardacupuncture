<?php
// Own-user learning report API; old aggregate exam history remains untouched.
define('AJAX_SCRIPT',true);
require_once(__DIR__.'/../../config.php');
require_once(__DIR__.'/herbology-learning-report-lib.php');
require_login();
if (isguestuser()) { throw new invalid_parameter_exception('A personal account is required.'); }
require_sesskey();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if(($_SERVER['REQUEST_METHOD'] ?? '')!=='POST'){throw new invalid_parameter_exception('POST required.');}
$context=context_course::instance(116);
if(!is_enrolled($context,$USER->id,'',true)&&!has_capability('moodle/course:view',$context,$USER->id)){
    throw new required_capability_exception($context,'moodle/course:view','nopermissions','');
}
try {
    $action=required_param('action',PARAM_ALPHA);
    if($action==='history'){
        echo json_encode(['success'=>true,'history'=>block_menu_hlr_history()]);exit;
    }
    if($action==='load'){
        $file=block_menu_hlr_file(required_param('id',PARAM_ALPHANUMEXT));
        if(!$file){throw new invalid_parameter_exception('Report not found.');}
        echo json_encode(['success'=>true,'report'=>json_decode($file->get_content(),true),'history'=>block_menu_hlr_history()]);exit;
    }
    $token=required_param('token',PARAM_ALPHANUMEXT);
    $snapshot=$SESSION->herbology_learning_snapshots[$token] ?? null;
    if(!is_array($snapshot)||(int)($snapshot['userid'] ?? 0)!==(int)$USER->id||($snapshot['started'] ?? 0)<time()-172800){
        throw new invalid_parameter_exception('This attempt snapshot has expired.');
    }
    if($action==='begin'){
        $newtoken=bin2hex(random_bytes(24));$snapshot['started']=time();
        $sessions=(array)$SESSION->herbology_learning_snapshots;
        while(count($sessions)>=8){array_shift($sessions);}
        $sessions[$newtoken]=$snapshot;$SESSION->herbology_learning_snapshots=$sessions;
        echo json_encode(['success'=>true,'token'=>$newtoken]);exit;
    }
    if($action!=='submit'){throw new invalid_parameter_exception('Unknown action.');}
    $raw=required_param('answers',PARAM_RAW);
    if(strlen($raw)>100000){throw new invalid_parameter_exception('Answer payload too large.');}
    $answers=json_decode($raw,true);
    if(!is_array($answers)){throw new invalid_parameter_exception('Invalid answers.');}
    $factory=\core\lock\lock_config::get_lock_factory('block_menu_hlr');
    $lock=$factory->get_lock($USER->id.'-'.$token,10);
    if(!$lock){throw new RuntimeException('Report is busy. Please retry.');}
    try {
        $existing=block_menu_hlr_file($token);
        if($existing){$report=json_decode($existing->get_content(),true);}
        else {
            $report=block_menu_hlr_grade($snapshot,$answers,$token);
            get_file_storage()->create_file_from_string([
                'contextid'=>context_user::instance($USER->id)->id,'component'=>'block_menu',
                'filearea'=>'herbology_learning','itemid'=>0,'filepath'=>'/','filename'=>$token.'.json',
                'userid'=>$USER->id,'mimetype'=>'application/json',
            ],json_encode($report,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));
        }
    } finally {$lock->release();}
    echo json_encode(['success'=>true,'report'=>$report,'history'=>block_menu_hlr_history()],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
} catch(Throwable $error) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'The detailed report could not be loaded or saved. Please retry.']);
}
