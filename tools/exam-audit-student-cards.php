<?php
// Public aggregate metrics only; never render questions, keys, or draft projects.
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__.'/exam-audit-classification.php');

function block_myprogress_render_exam_audit_cards(array $sets, bool $ises, string $track,
        array $languagecontext, int $selected): void {
    global $questionrevisionstore,$CFG;
    $store=is_array($questionrevisionstore ?? null)?$questionrevisionstore:[];
    if(!isset($store['publishedExams'])&&isset($CFG->dataroot)){
        $path=$CFG->dataroot.'/board-domain-banks/herbology-question-overrides.json';
        if(is_readable($path)){$store=json_decode(file_get_contents($path),true) ?: []; }
    }
    $sets=block_myprogress_exam_audit_snapshot_sets($store,$sets);
    $topics = [
        ['Jerarquía de ingredientes', 'Ingredient hierarchy', '~\b(?:hierarch\w*|jerarqu\w*|emperador|mensajero)\b|\b(?:chief|deputy|envoy|assistant)\s+(?:herbs?|ingredients?|role)|(?:matches?|identify|role|as an?)\W+(?:the\W+)?(?:chief|deputy|assistant|envoy)\b~iu'],
        ['Composición y modificaciones', 'Composition and modifications', '~\b(?:composition|modif\w*|add(?:ed|ing)?|remove\w*|substitut\w*|composición|agreg\w*|añad\w*|retir\w*)\b~iu'],
        ['Procesamiento y preparación', 'Processing and preparation', '~\b(?:process\w*|prepar\w*|honey|wine.prepared|charred|roasted|miel|carboniz\w*|tostad\w*)\b~iu'],
        ['Dosis y administración', 'Dosage and administration', '~\b(?:dose|dosage|grams?|milligrams?|dosis|gramos?|miligramos?|decoct\w*|decocc\w*)\b|\b\d+(?:\.\d+)?\s*(?:g|mg)\b~iu'],
        ['Canales y meridianos', 'Channels and meridians', '~\b(?:channels?|meridians?|canales?|meridianos?)\b~iu'],
        ['Propiedades y sabores', 'Properties and flavors', '~\b(?:temperature|flavo[u]?r|acrid|sweet|bitter|salty|sour|temperatura|sabores?|acre|dulce|amargo|salado|ácido)\b~iu'],
        ['Embarazo y lactancia', 'Pregnancy and lactation', '~\b(?:pregnan\w*|lactat\w*|breastfeed\w*|embaraz\w*|lactancia)\b~iu'],
        ['Interacciones', 'Interactions', '~\b(?:interact\w*|warfarin|digoxin|anticoagul\w*|interaccion\w*|medicamentos?)\b~iu'],
        ['Contraindicaciones y toxicidad', 'Contraindications and toxicity', '~\b(?:contraindicat\w*|toxic\w*|caution|unsafe|avoid|precaución|evitar)\b~iu'],
        ['Comparación y diferenciación', 'Comparison and differentiation', '~\b(?:distinguish\w*|differentiat\w*|compar\w*|distinc\w*|diferenci\w*)\b~iu'],
    ];
    $ready = static function($value): bool {
        $text = trim(strip_tags((string)$value));
        return $text !== '' && !in_array(block_myprogress_exam_audit_normalize($text), ['not completed', 'pending', 'missing', 'no completado', 'pendiente'], true);
    };
    $cards = [];
    foreach ($sets as $number => $questions) {
        // Only complete sets offered by the student chooser qualify.
        if (count($questions) !== 100) { continue; }
        $counts = array_fill(0, count($topics), 0); $clinical = 0; $escomplete = 0;
        foreach ($questions as $q) {
            $stem = html_entity_decode(strip_tags((string)($q['prompt'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            foreach ($topics as $i => $topic) {
                if ($i===0 ? block_myprogress_exam_audit_is_hierarchy($q) : preg_match($topic[2], $stem)) { $counts[$i]++; }
            }
            if (preg_match('~\b(?:patient|presents?|paciente|persona presenta|year.old|years.old|años de edad)\b~iu', $stem)) { $clinical++; }
            $es = $q['translations']['es'] ?? [];
            $esoptions = is_array($es['options'] ?? null) ? $es['options'] : [];
            $esvalid = $ready($es['prompt'] ?? '') && $ready($es['explanation'] ?? '') && count($esoptions) === 4;
            foreach ($esoptions as $o) { $esvalid = $esvalid && is_array($o) && $ready($o['text'] ?? ''); }
            $eslabels=array_column($esoptions,'label');
            $esvalid=$esvalid && count(array_unique($eslabels))===4 && (empty($es['correctLabel']) || $es['correctLabel']===($q['correctLabel'] ?? ''));
            if ($esvalid) { $escomplete++; }
        }
        $cards[] = [$number, $counts, $clinical, $escomplete];
    }
    if (!$cards) { return; }
    ?>
    <section class="exam-audit-cards" aria-label="<?php echo $ises ? 'Perfil de cada examen' : 'Each exam at a glance'; ?>">
        <style>
        .exam-audit-cards{margin:20px 0}.exam-audit-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(265px,1fr));gap:14px}.exam-audit-card{border:1px solid #bcd2df;border-top:4px solid #008e89;border-radius:10px;padding:18px;background:#fff;color:#173d50}.exam-audit-card h4{margin:0 0 10px;font-size:1.2rem}.exam-audit-card p{margin:8px 0}.exam-audit-card ul{padding-left:20px;margin:10px 0}.exam-audit-card li{margin:5px 0}.exam-audit-card a{display:inline-block;border:1px solid #008e89;border-radius:6px;padding:8px 12px;margin-top:10px;color:#006d69;font-weight:700}.exam-audit-card details{margin-top:12px}.exam-audit-card summary{cursor:pointer;font-weight:600}.exam-audit-card .exam-audit-note{font-size:.85rem;line-height:1.5;color:#506777}
        </style>
        <h3><?php echo $ises ? 'Elige el examen que necesitas practicar' : 'Choose the exam you want to practice'; ?></h3>
        <p><?php echo $ises ? 'Perfil de la versión publicada de cada examen. Los temas se detectan en los enunciados; una pregunta puede abarcar varios temas. Estos datos no miden la dificultad.' : 'Profile of each published exam version. Topics are detected in question stems; one question can cover several topics. These counts do not measure difficulty.'; ?></p>
        <div class="exam-audit-grid">
        <?php foreach ($cards as [$number, $counts, $clinical, $escomplete]) {
            $ranked = $counts; arsort($ranked, SORT_NUMERIC);
            $url = new moodle_url('/blocks/menu/herbology-board.php', ['track' => $track,
                ($track === 'simulation' ? 'simulation' : 'examset') => (int)$number] + $languagecontext);
            ?>
            <article class="exam-audit-card">
                <h4><?php echo $ises ? 'Examen ' : 'Exam '; echo (int)$number; ?></h4>
                <p><strong>100 <?php echo $ises ? 'preguntas' : 'questions'; ?></strong></p>
                <p><?php echo $ises ? 'Casos clínicos explícitos: ' : 'Explicit clinical cases: '; echo (int)$clinical; ?>/100</p>
                <p><?php echo $ises ? 'Jerarquía de ingredientes: ' : 'Ingredient hierarchy: '; echo (int)$counts[0]; ?>/100</p>
                <p><strong><?php echo $ises ? 'Temas más presentes' : 'Most represented topics'; ?></strong></p>
                <ul><?php $shown = 0; foreach ($ranked as $i => $count) {
                    if (!$count || $shown >= 3) { continue; } $shown++;
                    ?><li><?php echo s($topics[$i][$ises ? 0 : 1]); ?>: <strong><?php echo (int)$count; ?></strong></li><?php
                } if (!$shown) { ?><li><?php echo $ises ? 'Sin temas específicos detectados' : 'No specific topics detected'; ?></li><?php } ?></ul>
                <details><summary><?php echo $ises ? 'Ver todos los temas' : 'View all topics'; ?></summary>
                <ul><?php foreach ($topics as $i => $topic) { ?><li><?php echo s($topic[$ises ? 0 : 1]); ?>: <?php echo (int)$counts[$i]; ?></li><?php } ?></ul></details>
                <p class="exam-audit-note"><?php echo $ises ? 'Español: ' : 'Spanish: '; echo (int)$escomplete; ?>/100 <?php echo $ises ? 'preguntas con enunciado, opciones y explicación completos.' : 'questions with complete stem, choices and explanation.'; ?></p>
                <a href="<?php echo s($url->out(false)); ?>"<?php echo (int)$number === $selected ? ' aria-current="page"' : ''; ?>><?php echo $ises ? 'Elegir examen ' : 'Choose Exam '; echo (int)$number; ?></a>
            </article>
        <?php } ?>
        </div>
    </section>
    <?php
}
