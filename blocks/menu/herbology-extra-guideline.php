<?php
// Student view for the separately published Extra guideline collection.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../myprogress/herbology-question-overrides.php');

require_login();

$lang = optional_param('lang', '', PARAM_ALPHA);
$ises = $lang === 'es' || ($lang === '' && current_language() === 'es');
$pagenumber = max(0, optional_param('page', 0, PARAM_INT));
$pagesize = 20;

$membershipcourseid = 116;
$membershipcourse = $DB->get_record('course', ['id' => $membershipcourseid], '*', MUST_EXIST);
$membershipcontext = context_course::instance($membershipcourseid);
$hasaccess = is_enrolled($membershipcontext, $USER->id, '', true)
    || has_capability('moodle/course:view', $membershipcontext, $USER->id);
if (!$hasaccess) {
    redirect(
        new moodle_url('/my/'),
        'Purchase the 30-day Board access before opening this learning resource.',
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

$store = block_myprogress_herbology_override_load();
$publication = is_array($store['publishedExtraGuideline'] ?? null)
    ? $store['publishedExtraGuideline']
    : null;
$questions = is_array($publication['questions'] ?? null)
    ? array_values(array_filter($publication['questions'], 'is_array'))
    : [];
$questions = array_map(
    'block_myprogress_herbology_canonicalize_pinyin_case_in_question',
    $questions
);
$questioncount = count($questions);
$pagecount = max(1, (int)ceil($questioncount / $pagesize));
$pagenumber = min($pagenumber, $pagecount - 1);
$pagequestions = array_slice($questions, $pagenumber * $pagesize, $pagesize);

$pageparams = array_filter([
    'lang' => $ises ? 'es' : null,
    'page' => $pagenumber > 0 ? $pagenumber : null,
]);
$pageurl = new moodle_url('/blocks/menu/herbology-extra-guideline.php', $pageparams);
$languageurl = new moodle_url('/blocks/menu/herbology-extra-guideline.php', array_filter([
    'lang' => $ises ? 'en' : 'es',
    'page' => $pagenumber > 0 ? $pagenumber : null,
]));
$simulationurl = new moodle_url('/blocks/menu/herbology-board.php', array_filter([
    'track' => 'simulation',
    'from' => 'library',
    'lang' => $ises ? 'es' : null,
]));
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', array_filter([
    'area' => 'herbology',
    'view' => 'library',
    'lang' => $ises ? 'es' : null,
]));

$PAGE->set_url($pageurl);
$PAGE->set_context($membershipcontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Práctica Extra guideline' : 'Extra guideline practice');
$PAGE->set_heading($ises ? 'Práctica Extra guideline' : 'Extra guideline practice');

$PAGE->requires->js_init_code(<<<'JS'
document.querySelectorAll('[data-extra-question]').forEach(function (card) {
    var button = card.querySelector('[data-check-answer]');
    if (!button) return;
    button.addEventListener('click', function () {
        var selected = card.querySelector('input[type="radio"]:checked');
        var result = card.querySelector('[data-answer-result]');
        var explanation = card.querySelector('[data-answer-explanation]');
        if (!selected) {
            result.textContent = card.dataset.chooseMessage;
            result.className = 'extra-guideline__result is-warning';
            return;
        }
        var correct = card.dataset.correctLabel;
        var iscorrect = selected.value === correct;
        card.querySelectorAll('.extra-guideline__choice').forEach(function (choice) {
            var input = choice.querySelector('input');
            choice.classList.toggle('is-correct', input && input.value === correct);
            choice.classList.toggle('is-incorrect', input && input.checked && input.value !== correct);
        });
        result.textContent = iscorrect ? card.dataset.correctMessage : card.dataset.incorrectMessage + ' ' + correct + '.';
        result.className = 'extra-guideline__result ' + (iscorrect ? 'is-correct' : 'is-incorrect');
        explanation.hidden = false;
        button.disabled = true;
        card.querySelectorAll('input[type="radio"]').forEach(function (input) { input.disabled = true; });
    });
});
JS);

echo $OUTPUT->header();
?>
<style>
.extra-guideline{max-width:1180px;margin:0 auto 3rem;color:#082f49}.extra-guideline__toolbar{display:flex;justify-content:space-between;gap:.75rem;flex-wrap:wrap;margin-bottom:1rem}.extra-guideline__toolbar-group{display:flex;gap:.6rem;flex-wrap:wrap}.extra-guideline__button{display:inline-flex;align-items:center;justify-content:center;border:1px solid #b8cbd8;border-radius:.6rem;padding:.6rem .9rem;background:#fff;color:#083b5c;font-weight:700;text-decoration:none}.extra-guideline__button:hover{background:#f1f7fa;text-decoration:none}.extra-guideline__button--primary{background:#087f6b;border-color:#087f6b;color:#fff}.extra-guideline__hero{border:1px solid #b8d8d1;border-left:6px solid #087f6b;border-radius:.85rem;background:#eefaf6;padding:1.25rem 1.4rem;margin-bottom:1rem}.extra-guideline__hero h2{margin:0 0 .35rem}.extra-guideline__hero p{margin:.35rem 0}.extra-guideline__warning{border:1px solid #e6b34b;border-radius:.7rem;background:#fff8e7;padding:.8rem 1rem;margin-top:1rem}.extra-guideline__meta{display:flex;gap:1rem;flex-wrap:wrap;margin-top:.8rem;color:#38566b}.extra-guideline__list{display:grid;gap:1rem}.extra-guideline__card{border:1px solid #cbdbe5;border-radius:.85rem;background:#fff;padding:1.2rem}.extra-guideline__card h3{font-size:1.12rem;line-height:1.45;margin:.2rem 0 1rem}.extra-guideline__eyebrow{font-size:.78rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:#087f6b}.extra-guideline__scope-note{border-left:4px solid #e6a700;background:#fff8e7;padding:.65rem .8rem;margin:.65rem 0 .9rem;color:#704500}.extra-guideline__choices{display:grid;grid-template-columns:1fr 1fr;gap:.55rem}.extra-guideline__choice{display:flex;gap:.55rem;border:1px solid #d5e1e8;border-radius:.55rem;background:#f8fafc;padding:.75rem;cursor:pointer}.extra-guideline__choice.is-correct{border-color:#16a085;background:#eaf8f3}.extra-guideline__choice.is-incorrect{border-color:#df5c5c;background:#fff0f0}.extra-guideline__actions{display:flex;align-items:center;gap:.8rem;flex-wrap:wrap;margin-top:1rem}.extra-guideline__result{font-weight:800}.extra-guideline__result.is-correct{color:#087f5b}.extra-guideline__result.is-incorrect{color:#b42318}.extra-guideline__result.is-warning{color:#9a5b00}.extra-guideline__explanation{margin-top:1rem;border-left:4px solid #0e9f9a;background:#edf9f7;padding:.85rem 1rem}.extra-guideline__pagination{display:flex;justify-content:center;gap:.5rem;flex-wrap:wrap;margin-top:1.3rem}.extra-guideline__empty{border:1px solid #cbdbe5;border-radius:.8rem;background:#fff;padding:1.4rem}@media(max-width:760px){.extra-guideline__choices{grid-template-columns:1fr}.extra-guideline__card{padding:.9rem}}
</style>
<main class="extra-guideline">
    <nav class="extra-guideline__toolbar" aria-label="<?php echo $ises ? 'Navegación' : 'Navigation'; ?>">
        <div class="extra-guideline__toolbar-group"><a class="extra-guideline__button" href="<?php echo $simulationurl->out(false); ?>">← <?php echo $ises ? 'Exámenes de simulación' : 'Simulation exams'; ?></a><a class="extra-guideline__button" href="<?php echo $libraryurl->out(false); ?>"><?php echo $ises ? 'Guía de estudio' : 'Study guide'; ?></a></div>
        <a class="extra-guideline__button" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
    </nav>
    <section class="extra-guideline__hero">
        <div class="extra-guideline__eyebrow"><?php echo $ises ? 'Colección suplementaria publicada' : 'Published supplemental collection'; ?></div>
        <h2>Extra guideline</h2>
        <p><?php echo $ises
            ? 'Preguntas adicionales revisadas para práctica. Esta colección se publica por separado y no modifica la composición de los exámenes de simulación.'
            : 'Additional reviewed questions for practice. This collection is published separately and does not change the composition of the simulation exams.'; ?></p>
        <div class="extra-guideline__warning"><strong><?php echo $ises ? 'Importante:' : 'Important:'; ?></strong> <?php echo $ises
            ? 'El contenido puede incluir fórmulas, hierbas o temas fuera del roster usado por este sitio para los exámenes NCBAHM. No se presenta como contenido oficial del Board.'
            : 'Content may include formulas, herbs, or topics outside the roster this site uses for NCBAHM exams. It is not presented as official Board content.'; ?></div>
        <?php if ($publication !== null): ?><div class="extra-guideline__meta"><span><strong><?php echo $questioncount; ?></strong> <?php echo $ises ? 'preguntas' : 'questions'; ?></span><span><?php echo $ises ? 'Versión' : 'Version'; ?> <strong><?php echo (int)($publication['version'] ?? 1); ?></strong></span><span><?php echo $ises ? 'Publicada' : 'Published'; ?>: <?php echo s(userdate((int)strtotime((string)($publication['publishedAt'] ?? 'now')), get_string('strftimedatetimeshort'))); ?></span></div><?php endif; ?>
    </section>

    <?php if ($publication === null || $questions === []): ?>
        <div class="extra-guideline__empty"><?php echo $ises ? 'La colección Extra guideline todavía no ha sido publicada.' : 'The Extra guideline collection has not been published yet.'; ?></div>
    <?php else: ?>
        <section class="extra-guideline__list" aria-label="<?php echo $ises ? 'Preguntas Extra guideline' : 'Extra guideline questions'; ?>">
        <?php foreach ($pagequestions as $pageindex => $question):
            $position = (int)($question['extraGuidelinePosition'] ?? ($pagenumber * $pagesize + $pageindex + 1));
            $translation = is_array($question['translations']['es'] ?? null) ? $question['translations']['es'] : [];
            $prompt = $ises ? (string)($translation['prompt'] ?? $question['prompt'] ?? '') : (string)($question['prompt'] ?? '');
            $options = $ises && is_array($translation['options'] ?? null) ? $translation['options'] : ($question['options'] ?? []);
            $explanation = $ises ? (string)($translation['explanation'] ?? $question['explanation'] ?? '') : (string)($question['explanation'] ?? '');
            $guidelineviolations = array_values(array_filter(array_map(
                'strval',
                (array)($question['guidelineViolations'] ?? [])
            )));
            $outsiderosternames = [];
            foreach ($guidelineviolations as $guidelineviolation) {
                if (preg_match('/:\s*([^|]+?)\s*$/u', $guidelineviolation, $namematch)) {
                    $outsiderostername = trim((string)$namematch[1]);
                    if ($outsiderostername !== '') {
                        $outsiderosternames[] = $outsiderostername;
                    }
                }
            }
            $outsiderosternames = array_values(array_unique($outsiderosternames));
            if ($outsiderosternames !== []) {
                $outsiderosterlabel = implode(', ', $outsiderosternames);
                $statuspresent = $ises
                    ? stripos($explanation, 'fuera del roster') !== false
                    : stripos($explanation, 'outside the current NCBAHM') !== false;
                if (!$statuspresent) {
                    $explanation = rtrim($explanation) . ($ises
                        ? ' Estado del guideline: ' . $outsiderosterlabel
                            . ' está fuera del roster vigente de los Appendix A/C de NCBAHM; esta pregunta se '
                            . 'conserva únicamente en el banco independiente Extra guideline.'
                        : ' Guideline status: ' . $outsiderosterlabel
                            . ' is outside the current NCBAHM Appendix A/C roster; this question is preserved only '
                            . 'in the independent Extra guideline bank.');
                }
            }
            $correctlabel = strtoupper((string)($question['correctLabel'] ?? ''));
            $cardid = 'extra-guideline-question-' . $position;
        ?>
            <article id="<?php echo s($cardid); ?>" class="extra-guideline__card" data-extra-question data-correct-label="<?php echo s($correctlabel); ?>" data-choose-message="<?php echo s($ises ? 'Seleccione una respuesta primero.' : 'Choose an answer first.'); ?>" data-correct-message="<?php echo s($ises ? 'Respuesta correcta.' : 'Correct answer.'); ?>" data-incorrect-message="<?php echo s($ises ? 'Respuesta incorrecta. La respuesta correcta es' : 'Incorrect. The correct answer is'); ?>">
                <div class="extra-guideline__eyebrow"><?php echo $ises ? 'Pregunta' : 'Question'; ?> <?php echo $position; ?> <?php echo $ises ? 'de' : 'of'; ?> <?php echo $questioncount; ?></div>
                <div class="extra-guideline__scope-note"><strong><?php echo $ises ? 'Contenido suplementario:' : 'Supplemental content:'; ?></strong> <?php echo $ises
                    ? 'Esta pregunta puede incluir hierbas o fórmulas fuera del Appendix A/C vigente. Después de comprobar la respuesta, la explicación identifica cuáles están fuera del guideline.'
                    : 'This question may include herbs or formulas outside the current Appendix A/C. After you check the answer, the explanation identifies which names are outside the guideline.'; ?></div>
                <h3><?php echo s($prompt); ?></h3>
                <div class="extra-guideline__choices">
                <?php foreach (array_values((array)$options) as $optionindex => $option):
                    $label = strtoupper((string)(is_array($option) ? ($option['label'] ?? chr(65 + $optionindex)) : chr(65 + $optionindex)));
                    $optiontext = (string)(is_array($option) ? ($option['text'] ?? '') : $option);
                ?>
                    <label class="extra-guideline__choice"><input type="radio" name="extra-question-<?php echo $position; ?>" value="<?php echo s($label); ?>"><span><strong><?php echo s($label); ?>.</strong> <?php echo s($optiontext); ?></span></label>
                <?php endforeach; ?>
                </div>
                <div class="extra-guideline__actions"><button class="extra-guideline__button extra-guideline__button--primary" type="button" data-check-answer><?php echo $ises ? 'Comprobar respuesta' : 'Check answer'; ?></button><span class="extra-guideline__result" data-answer-result aria-live="polite"></span></div>
                <div class="extra-guideline__explanation" data-answer-explanation hidden><strong><?php echo $ises ? 'Explicación:' : 'Explanation:'; ?></strong> <?php echo s($explanation); ?></div>
            </article>
        <?php endforeach; ?>
        </section>
        <?php if ($pagecount > 1): ?><nav class="extra-guideline__pagination" aria-label="<?php echo $ises ? 'Páginas' : 'Pages'; ?>"><?php for ($page = 0; $page < $pagecount; $page++): ?><a class="extra-guideline__button<?php echo $page === $pagenumber ? ' extra-guideline__button--primary' : ''; ?>" href="<?php echo (new moodle_url('/blocks/menu/herbology-extra-guideline.php', array_filter(['lang' => $ises ? 'es' : null, 'page' => $page > 0 ? $page : null])))->out(false); ?>"><?php echo $page + 1; ?></a><?php endfor; ?></nav><?php endif; ?>
    <?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
