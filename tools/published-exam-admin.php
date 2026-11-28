<?php
// Administrator-only inspection of exactly the frozen published question set.
require_once(__DIR__.'/../../config.php');
require_once(__DIR__.'/published-exam-admin-view.php');
require_login();
$context=context_system::instance();require_capability('moodle/site:config',$context);
$number=optional_param('exam',0,PARAM_INT);$projectid=optional_param('project','',PARAM_ALPHANUMEXT);
$ises=optional_param('lang','en',PARAM_ALPHA)==='es';
$path=$CFG->dataroot.'/board-domain-banks/herbology-question-overrides.json';
$store=is_readable($path)?json_decode(file_get_contents($path),true):[];
$record=$projectid!==''?($store['examProjects'][$projectid] ?? null):($store['publishedExams']['simulation_exam_'.$number] ?? null);
$draft=$projectid!=='';
$questions=is_array($record)?($draft?($record['approvedSourceSnapshots'] ?? []):($record['questions'] ?? [])):[];
if(!is_array($record)||!is_array($questions)||(!$draft&&($record['status'] ?? '')!=='published')){throw new invalid_parameter_exception('Published exam not found.');}
$title=(string)($record['name'] ?? ('Exam '.$number));
$PAGE->set_context($context);$PAGE->set_url(new moodle_url('/blocks/myprogress/published-exam-admin.php',['exam'=>$number,'project'=>$projectid,'lang'=>$ises?'es':'en']));
$PAGE->set_title($title);$PAGE->set_heading($title);$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();
$back=new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php',['lang'=>$ises?'es':'en']);
?>
<main style="max-width:1150px;margin:24px auto;padding:18px">
<p><a href="<?php echo s($back->out(false)); ?>#exam-builder"><?php echo $ises?'← Volver al constructor':'← Back to exam builder'; ?></a></p>
<h2><?php echo s($title); ?> · <?php echo $draft?($ises?'borrador':'draft'):($ises?'versión publicada':'published version'); ?></h2>
<p><?php echo count($questions); ?> <?php echo $ises?'preguntas en la versión; utilizables:':'questions in the version; usable:'; ?> <?php echo block_myprogress_snapshot_usable_count($questions); ?>.
<?php if(!$draft){echo ($ises?'Versión ':'Version ').(int)($record['version'] ?? 1);} ?></p>
<p><?php echo $ises?'Este listado conserva las preguntas de esta versión. Las revisiones del banco se consultan por separado.':'This list preserves this version’s questions. Live-bank revisions are viewed separately.'; ?></p>
<?php foreach($questions as $i=>$q){
    $es=$q['translations']['es'] ?? [];$view=$ises&&is_array($es)&&!empty($es['prompt'])?array_merge($q,$es):$q;
    $edit=new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php',['q'=>(string)$q['questionId'],'lang'=>$ises?'es':'en']);
?>
<details style="border:1px solid #c6d8df;border-radius:8px;padding:14px;margin:10px 0">
<summary><?php echo (int)($i+1); ?> · <?php echo s((string)$q['questionId']); ?> · <?php echo s((string)($q['domainId'] ?? '')); ?></summary>
<p><strong><?php echo s((string)($view['prompt'] ?? '')); ?></strong></p>
<ul><?php foreach(($view['options'] ?? []) as $option){ ?><li><?php echo s((string)$option['label']); ?>. <?php echo s((string)$option['text']); ?></li><?php } ?></ul>
<p><?php echo $ises?'Respuesta correcta: ':'Correct answer: '; echo s((string)($q['correctLabel'] ?? '')); ?></p>
<p style="white-space:pre-wrap"><?php echo s((string)($view['explanation'] ?? '')); ?></p>
<p><?php echo s((string)($q['source']['title'] ?? '')); ?> · <?php echo s((string)($q['source']['locator'] ?? '')); ?></p>
<a href="<?php echo s($edit->out(false)); ?>"><?php echo $ises?'Abrir revisión del banco':'Open live-bank revision'; ?></a>
</details>
<?php } ?>
</main>
<?php echo $OUTPUT->footer();
