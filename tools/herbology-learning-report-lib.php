<?php
// Private, versioned learning reports for published Herbology practice exams.
defined('MOODLE_INTERNAL') || die();

function block_menu_hlr_topics(): array {
    return [
        'dui_yao'=>['Dui Yao','Dui Yao'],
        'precautions'=>['Precauciones generales','General precautions'],
        'processing'=>['Procesamiento y preparación','Processing and preparation'],
        'modifications'=>['Modificaciones y composición','Modifications and composition'],
        'hierarchy'=>['Jerarquía de ingredientes','Ingredient hierarchy'],
        'dosage'=>['Dosis y administración','Dosage and administration'],
        'pregnancy'=>['Embarazo y lactancia','Pregnancy and lactation'],
        'interactions'=>['Interacciones','Interactions'],
        'channels'=>['Canales y meridianos','Channels and meridians'],
        'properties'=>['Propiedades y sabores','Properties and flavors'],
        'differentiation'=>['Diferenciación de fórmulas','Formula differentiation'],
        'single_herb'=>['Hierbas individuales','Individual herbs'],
        'scope_aliases'=>['Nombres y sustituciones','Names and substitutions'],
        'other'=>['Otros objetivos de estudio','Other study objectives'],
    ];
}

function block_menu_hlr_classify(array $q): array {
    // Classify the question stem and tested content, never incidental feedback.
    $text = html_entity_decode(strip_tags((string)($q['prompt'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $tags=[];
    $domain=(string)($q['domainId'] ?? '');
    foreach (['dui_yao'=>'dui_yao','formula_differentiation'=>'differentiation','single_herb'=>'single_herb','scope_aliases'=>'scope_aliases'] as $d=>$tag) {
        if ($domain===$d) {$tags[]=$tag;}
    }
    $patterns=[
        'precautions'=>'\b(?:contraindicat\w*|toxic\w*|caution|unsafe|avoid|precauci[oó]n|evitar)\b',
        'processing'=>'\b(?:process\w*|prepar\w*|honey|wine.prepared|charred|roasted|miel|carboniz\w*|tostad\w*)\b',
        'modifications'=>'\b(?:composition|modif\w*|add(?:ed|ing)?|remove\w*|substitut\w*|composici[oó]n|agreg\w*|añad\w*|retir\w*)\b',
        'hierarchy'=>'\b(?:hierarch\w*|jerarqu\w*|emperador|mensajero)\b|\b(?:chief|deputy|assistant|envoy)\s+(?:herbs?|ingredients?|role)|(?:matches?|identify|role|as an?)\W+(?:the\W+)?(?:chief|deputy|assistant|envoy)\b',
        'dosage'=>'\b(?:dose|dosage|grams?|milligrams?|dosis|gramos?|miligramos?|decoct\w*|decocc\w*)\b|\b\d+(?:\.\d+)?\s*(?:g|mg)\b',
        'pregnancy'=>'\b(?:pregnan\w*|lactat\w*|breastfeed\w*|embaraz\w*|lactancia)\b',
        'interactions'=>'\b(?:interact\w*|warfarin|digoxin|anticoagul\w*|interaccion\w*|medicamentos?)\b',
        'channels'=>'\b(?:channels?|meridians?|canales?|meridianos?)\b',
        'properties'=>'\b(?:temperature|flavo[u]?r|acrid|sweet|bitter|salty|sour|temperatura|sabores?|acre|dulce|amargo|salado|[aá]cido)\b',
    ];
    foreach($patterns as $tag=>$pattern){if(preg_match('~'.$pattern.'~iu',$text)){$tags[]=$tag;}}
    if(($q['contentType'] ?? '')==='formula_hierarchy'){$tags[]='hierarchy';}
    return $tags ? array_values(array_unique($tags)) : ['other'];
}

function block_menu_hlr_boot(array $questions, bool $ises, string $track, string $mode, int $number, array $store): array {
    global $SESSION,$USER,$CFG;
    $config=['lang'=>$ises?'es':'en','sesskey'=>sesskey(),
        'url'=>(new moodle_url('/blocks/myprogress/herbology-learning-report.php'))->out(false),
        'topics'=>block_menu_hlr_topics(),'mode'=>$mode,'active'=>false];
    if(count($questions)!==100){return $config;}
    $published=$store['publishedExams']['simulation_exam_'.$number] ?? [];
    $english=[];
    foreach(($published['questions'] ?? []) as $q){$english[(string)$q['questionId']]=$q;}
    $snapshot=[];$identities=[];
    foreach($questions as $i=>$q){
        $id=(string)($q['questionId'] ?? '');
        if($id==='' || isset($identities[$id])){return $config;}
        $identities[$id]=true;
        $canonical=$english[$id] ?? $q;
        $snapshot[]=[
            'id'=>$id,'position'=>$i+1,'domain'=>(string)($q['domainId'] ?? ''),
            'topics'=>block_menu_hlr_classify($canonical),
            'prompt'=>(string)($q['prompt'] ?? ''),'options'=>array_values($q['options'] ?? []),
            'correct'=>(string)($q['correctLabel'] ?? ''),'explanation'=>(string)($q['explanation'] ?? ''),
        ];
    }
    $fingerprints=[];
    foreach($questions as $q){$c=$english[$q['questionId']] ?? $q;$fingerprints[]=[$q['questionId'],$c['prompt'] ?? '',$c['options'] ?? [],$c['correctLabel'] ?? ''];}
    usort($fingerprints,static fn($a,$b)=>strcmp($a[0],$b[0]));
    $sethash=hash('sha256',json_encode($fingerprints,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    $token=bin2hex(random_bytes(24));
    $sessions=(array)($SESSION->herbology_learning_snapshots ?? []);
    foreach($sessions as $key=>$value){if(($value['started'] ?? 0)<time()-172800){unset($sessions[$key]);}}
    while(count($sessions)>=8){array_shift($sessions);}
    $version=(int)($published['version'] ?? 0);
    $sessions[$token]=['userid'=>(int)$USER->id,'started'=>time(),'lang'=>$config['lang'],
        'number'=>$number,'version'=>$version,'track'=>$track,'mode'=>$mode,'sethash'=>$sethash,
        'group'=>hash('sha256',json_encode([$number,$version,$track,$mode,$sethash])), 'items'=>$snapshot];
    $SESSION->herbology_learning_snapshots=$sessions;
    $config['active']=true;$config['token']=$token;
    $config['items']=array_map(static fn($q)=>['id'=>$q['id'],'topics'=>$q['topics'],'domain'=>$q['domain']],$snapshot);
    return $config;
}

function block_menu_hlr_grade(array $snapshot, array $answers, string $id): array {
    if(count($answers)!==count($snapshot['items'])){throw new invalid_parameter_exception('Answer count does not match this attempt.');}
    $selected=[];$positions=[];
    foreach($answers as $answer){
        if(!is_array($answer)||!is_string($answer['id'] ?? null)||!is_string($answer['answer'] ?? null)||isset($selected[$answer['id']])){
            throw new invalid_parameter_exception('Invalid or duplicate answer identifier.');
        }
        $selected[$answer['id']]=$answer['answer'];
        if(array_key_exists('position',$answer)){
            $pos=$answer['position'];
            if($pos!==null&&(!is_int($pos)||$pos<1||$pos>count($snapshot['items']))){throw new invalid_parameter_exception('Invalid displayed position.');}
            $positions[$answer['id']]=$pos;
        }
    }
    $totals=['correct'=>0,'wrong'=>0,'unanswered'=>0,'total'=>count($snapshot['items'])];
    $topics=[];$domains=[];$items=[];
    foreach($snapshot['items'] as $q){
        if(!array_key_exists($q['id'],$selected)){throw new invalid_parameter_exception('Unknown question set.');}
        $choice=$selected[$q['id']];$allowed=array_column($q['options'],'label');
        if($choice!==''&&!in_array($choice,$allowed,true)){throw new invalid_parameter_exception('Invalid answer label.');}
        $state=$choice===''?'unanswered':($choice===$q['correct']?'correct':'wrong');
        $totals[$state]++;
        foreach($q['topics'] as $tag){
            if(!isset($topics[$tag])){$topics[$tag]=['correct'=>0,'wrong'=>0,'unanswered'=>0,'total'=>0];}
            $topics[$tag][$state]++;$topics[$tag]['total']++;
        }
        $d=$q['domain'];
        if(!isset($domains[$d])){$domains[$d]=['correct'=>0,'wrong'=>0,'unanswered'=>0,'total'=>0];}
        $domains[$d][$state]++;$domains[$d]['total']++;
        $items[]=array_merge($q,['selected'=>$choice,'state'=>$state,'plannedPosition'=>$q['position'],
            'position'=>array_key_exists($q['id'],$positions)?$positions[$q['id']]:$q['position']]);
    }
    return ['schemaVersion'=>1,'id'=>$id,'completed'=>microtime(true),'number'=>$snapshot['number'],'version'=>$snapshot['version'],
        'track'=>$snapshot['track'],'mode'=>$snapshot['mode'],'group'=>$snapshot['group'],'sethash'=>$snapshot['sethash'],
        'lang'=>$snapshot['lang'],'overall'=>$totals,'topics'=>$topics,'domains'=>$domains,'items'=>$items];
}

function block_menu_hlr_file(string $id) {
    global $USER;
    if(!preg_match('/^[a-f0-9]{48}$/D',$id)){throw new invalid_parameter_exception('Invalid report identifier.');}
    return get_file_storage()->get_file(context_user::instance($USER->id)->id,'block_menu','herbology_learning',0,'/',$id.'.json');
}

function block_menu_hlr_history(): array {
    global $USER;
    $files=get_file_storage()->get_area_files(context_user::instance($USER->id)->id,'block_menu','herbology_learning',0,'timecreated DESC, id DESC',false);
    $history=[];
    foreach($files as $file){
        $row=json_decode($file->get_content(),true);
        if(!is_array($row)||($row['schemaVersion'] ?? 0)!==1){continue;}
        unset($row['items']);$history[]=$row;
        if(count($history)>=50){break;}
    }
    return $history;
}
