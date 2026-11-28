<?php
// Original citation and existing explanation for a published exam question.
require_once(__DIR__ . '/../../config.php');
require_login();

$questionid = required_param('question', PARAM_ALPHANUMEXT);
$lang = optional_param('lang', '', PARAM_ALPHA);
$ises = $lang === 'es' || ($lang === '' && current_language() === 'es');
$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/blocks/myprogress/herbology-exam-source-note.php', [
    'question' => $questionid, 'lang' => $ises ? 'es' : 'en',
]));
$title = $ises ? 'Referencia original y explicación guardada' : 'Original reference and saved explanation';
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$path = $CFG->dataroot . '/board-domain-banks/herbology-question-overrides.json';
$store = is_readable($path) ? json_decode(file_get_contents($path), true) : [];
$question = null;
foreach ((array)($store['publishedExams'] ?? []) as $exam) {
    if (!is_array($exam) || ($exam['status'] ?? '') !== 'published'
            || !is_array($exam['questions'] ?? null) || count($exam['questions']) !== 100) {
        continue;
    }
    foreach ($exam['questions'] as $candidate) {
        if (is_array($candidate) && ($candidate['questionId'] ?? '') === $questionid) {
            $question = $candidate;
            break 2;
        }
    }
}
if ($question === null) {
    http_response_code(404);
    echo $OUTPUT->header();
    echo $OUTPUT->notification($ises ? 'Esta pregunta no está disponible en un examen publicado.' : 'This question is not available in a published exam.', 'info');
    echo $OUTPUT->footer();
    exit;
}
$plain = static function($text): string { return s(trim(strip_tags((string)$text))); };
$translation = is_array($question['translations']['es'] ?? null) ? $question['translations']['es'] : [];
$prompt = $ises && !empty($translation['prompt']) ? $translation['prompt'] : ($question['prompt'] ?? '');
$hastranslatedexplanation = !empty($translation['explanation']);
$explanation = $ises && $hastranslatedexplanation ? $translation['explanation'] : ($question['explanation'] ?? '');
$source = is_array($question['source'] ?? null) ? $question['source'] : [];
$back = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'exams', 'lang' => $ises ? 'es' : 'en']);
$switch = new moodle_url('/blocks/myprogress/herbology-exam-source-note.php', ['question' => $questionid, 'lang' => $ises ? 'en' : 'es']);
echo $OUTPUT->header();
?>
<section style="max-width:900px;margin:20px auto;padding:22px;border:1px solid #bcd2df;border-top:4px solid #008e89;border-radius:10px">
    <p><a href="<?php echo s($back->out(false)); ?>"><?php echo $ises ? '← Regresar a los exámenes' : '← Back to exams'; ?></a>
        · <a href="<?php echo s($switch->out(false)); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a></p>
    <h2><?php echo s($title); ?></h2>
    <p><?php echo $ises
        ? 'Esta ficha reúne la referencia de origen y la explicación guardadas con la pregunta. No añade una fuente independiente.'
        : 'This note displays the original citation and explanation saved with the question. It does not add an independent source.'; ?></p>
    <dl>
        <dt><strong><?php echo $ises ? 'Fuente original' : 'Original source'; ?></strong></dt>
        <dd><?php echo $plain($source['title'] ?? ''); ?></dd>
        <dt><strong><?php echo $ises ? 'Localización en la fuente' : 'Source location'; ?></strong></dt>
        <dd><?php echo $plain($source['locator'] ?? ''); ?></dd>
    </dl>
    <h3><?php echo $ises ? 'Pregunta de referencia' : 'Reference question'; ?></h3>
    <p><?php echo $plain($prompt); ?></p>
    <h3><?php echo $ises ? 'Explicación guardada' : 'Saved explanation'; ?></h3>
    <?php if ($ises && !$hastranslatedexplanation): ?>
        <p><?php echo 'La explicación original está disponible en inglés; la traducción sigue pendiente.'; ?></p>
    <?php endif; ?>
    <div><?php echo nl2br($plain($explanation)); ?></div>
</section>
<?php echo $OUTPUT->footer();
