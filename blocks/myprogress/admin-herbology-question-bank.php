<?php
// Administrator-only NCBAHM Chinese Herbology question bank browser.

require_once(__DIR__ . '/../../config.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$domainfilter = optional_param('domain', 'all', PARAM_ALPHANUMEXT);
$query = trim(optional_param('q', '', PARAM_TEXT));
$readinessfilter = optional_param('readiness', 'all', PARAM_ALPHA);
$usagefilter = optional_param('usage', 'all', PARAM_ALPHANUMEXT);
$displaylang = optional_param('lang', 'en', PARAM_ALPHA) === 'es' ? 'es' : 'en';
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 50;

$domains = [
    'formula_differentiation' => ['number' => 1, 'name' => 'Formula selection and differentiation'],
    'single_herb' => ['number' => 2, 'name' => 'Individual herbs'],
    'dui_yao' => ['number' => 3, 'name' => 'Duì Yào and compatibility'],
    'safety_dispensing' => ['number' => 4, 'name' => 'Safety and dispensing'],
    'formula_composition' => ['number' => 5, 'name' => 'Formula composition and modification'],
    'scope_aliases' => ['number' => 6, 'name' => 'Roster boundaries, aliases and substitutions'],
];
if ($domainfilter !== 'all' && !isset($domains[$domainfilter])) {
    $domainfilter = 'all';
}
if (!in_array($readinessfilter, ['all', 'ready', 'incomplete'], true)) {
    $readinessfilter = 'all';
}
if (!in_array($usagefilter, ['all', 'exam1', 'reserve'], true)) {
    $usagefilter = 'all';
}

$bankpath = $CFG->dirroot . '/blocks/menu/herbology-domain-candidates.json';
if (!is_readable($bankpath)) {
    throw new coding_exception('The NCBAHM Herbology question bank is not installed.');
}
$bank = json_decode((string)file_get_contents($bankpath), true);
if (!is_array($bank) || !isset($bank['domains']) || !is_array($bank['domains'])) {
    throw new coding_exception('The NCBAHM Herbology question bank is invalid.');
}

$isready = static function(array $question): bool {
    return trim((string)($question['prompt'] ?? '')) !== ''
        && count($question['options'] ?? []) === 4
        && trim((string)($question['correctLabel'] ?? '')) !== ''
        && trim((string)($question['explanation'] ?? '')) !== ''
        && trim((string)($question['source']['title'] ?? '')) !== ''
        && trim((string)($question['source']['locator'] ?? '')) !== ''
        && trim((string)($question['sourceCard']['url'] ?? '')) !== ''
        && trim((string)($question['sourceCard']['urlEs'] ?? '')) !== '';
};
$missingfields = static function(array $question): array {
    $missing = [];
    if (trim((string)($question['prompt'] ?? '')) === '') $missing[] = 'prompt';
    if (count($question['options'] ?? []) !== 4) $missing[] = 'four options';
    if (trim((string)($question['correctLabel'] ?? '')) === '') $missing[] = 'correct answer';
    if (trim((string)($question['explanation'] ?? '')) === '') $missing[] = 'explanation';
    if (trim((string)($question['source']['title'] ?? '')) === ''
            || trim((string)($question['source']['locator'] ?? '')) === '') {
        $missing[] = 'source';
    }
    if (trim((string)($question['sourceCard']['url'] ?? '')) === ''
            || trim((string)($question['sourceCard']['urlEs'] ?? '')) === '') {
        $missing[] = 'source card';
    }
    return $missing;
};

$allquestions = [];
$domainstats = [];
foreach ($domains as $domainid => $domain) {
    $domainstats[$domainid] = ['total' => 0, 'ready' => 0, 'exam1' => 0, 'reserve' => 0];
    foreach (($bank['domains'][$domainid] ?? []) as $question) {
        $ready = $isready($question);
        $examposition = (int)($question['examTemplate']['globalPosition'] ?? 0);
        $domainstats[$domainid]['total']++;
        if ($ready) $domainstats[$domainid]['ready']++;
        if ($examposition > 0) $domainstats[$domainid]['exam1']++;
        if ($ready && $examposition === 0) $domainstats[$domainid]['reserve']++;
        $question['_domainid'] = $domainid;
        $question['_ready'] = $ready;
        $question['_examposition'] = $examposition;
        $question['_missing'] = $missingfields($question);
        $allquestions[] = $question;
    }
}

$totals = [
    'total' => count($allquestions),
    'ready' => count(array_filter($allquestions, static fn(array $question): bool => $question['_ready'])),
    'exam1' => count(array_filter($allquestions, static fn(array $question): bool => $question['_examposition'] > 0)),
];
$totals['reserve'] = count(array_filter(
    $allquestions,
    static fn(array $question): bool => $question['_ready'] && $question['_examposition'] === 0
));

$filteredquestions = array_values(array_filter(
    $allquestions,
    static function(array $question) use (
        $domainfilter,
        $query,
        $readinessfilter,
        $usagefilter
    ): bool {
        if ($domainfilter !== 'all' && $question['_domainid'] !== $domainfilter) return false;
        if ($readinessfilter === 'ready' && !$question['_ready']) return false;
        if ($readinessfilter === 'incomplete' && $question['_ready']) return false;
        if ($usagefilter === 'exam1' && $question['_examposition'] === 0) return false;
        if ($usagefilter === 'reserve' && (!$question['_ready'] || $question['_examposition'] > 0)) return false;
        if ($query !== '') {
            $translation = $question['translations']['es'] ?? [];
            $searchparts = [
                $question['questionId'] ?? '',
                $question['prompt'] ?? '',
                $question['explanation'] ?? '',
                $translation['prompt'] ?? '',
                $translation['explanation'] ?? '',
            ];
            foreach ($question['options'] ?? [] as $option) $searchparts[] = $option['text'] ?? '';
            foreach ($translation['options'] ?? [] as $option) $searchparts[] = $option['text'] ?? '';
            $haystack = core_text::strtolower(implode(' ', $searchparts));
            if (strpos($haystack, core_text::strtolower($query)) === false) return false;
        }
        return true;
    }
));
usort($filteredquestions, static function(array $left, array $right) use ($domains): int {
    $domaincomparison = $domains[$left['_domainid']]['number'] <=> $domains[$right['_domainid']]['number'];
    return $domaincomparison !== 0
        ? $domaincomparison
        : strnatcasecmp((string)($left['questionId'] ?? ''), (string)($right['questionId'] ?? ''));
});

$resultcount = count($filteredquestions);
$totalpages = max(1, (int)ceil($resultcount / $perpage));
$page = min($page, $totalpages - 1);
$pagequestions = array_slice($filteredquestions, $page * $perpage, $perpage);
$baseparams = array_filter([
    'domain' => $domainfilter !== 'all' ? $domainfilter : null,
    'q' => $query !== '' ? $query : null,
    'readiness' => $readinessfilter !== 'all' ? $readinessfilter : null,
    'usage' => $usagefilter !== 'all' ? $usagefilter : null,
    'lang' => $displaylang !== 'en' ? $displaylang : null,
]);
$pageurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', $baseparams);
$reporturl = new moodle_url('/blocks/myprogress/admin-ncbahm-evaluations.php');
$clearurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php');
$languageurl = new moodle_url(
    '/blocks/myprogress/admin-herbology-question-bank.php',
    array_merge($baseparams, ['lang' => $displaylang === 'es' ? 'en' : 'es'])
);

$PAGE->set_url($pageurl);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('report');
$PAGE->set_title('NCBAHM Question Bank');
$PAGE->set_heading('NCBAHM Question Bank');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
?>
<style>
.question-bank{max-width:1380px;margin:0 auto 45px;color:#173448}.question-bank__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:16px;padding:22px 24px;border-left:5px solid #0b8f87;border-radius:12px;background:linear-gradient(135deg,#eef9f6,#fff8ef)}.question-bank__hero h2{margin:0 0 5px;font-size:30px}.question-bank__hero p{max-width:850px;margin:0;color:#52677a;line-height:1.5}.question-bank__actions{display:flex;flex-wrap:wrap;gap:8px}.question-bank__button{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:8px 13px;border:1px solid #b8c8d2;border-radius:8px;background:#fff;color:#24465b!important;font-weight:850;text-decoration:none!important}.question-bank__button:hover{border-color:#0b8f87;background:#eef9f6}.question-bank__summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:16px}.question-bank__summary article{padding:15px;border:1px solid #d5e0e6;border-radius:10px;background:#fff}.question-bank__summary span{display:block;color:#64748b;font-size:12px;font-weight:800}.question-bank__summary strong{display:block;margin-top:4px;color:#08746f;font-size:25px}.question-bank__filters{display:grid;grid-template-columns:minmax(220px,1.1fr) minmax(210px,.8fr) repeat(2,minmax(170px,.65fr)) auto;gap:10px;margin-bottom:16px;padding:15px;border:1px solid #d7e2e7;border-radius:11px;background:#fff}.question-bank__filters label span{display:block;margin-bottom:5px;font-size:12px;font-weight:850}.question-bank__filters input,.question-bank__filters select{width:100%;min-height:42px;box-sizing:border-box;padding:8px 10px;border:1px solid #b9cbd4;border-radius:7px;background:#fff}.question-bank__filter-actions{display:flex;align-items:end;gap:7px}.question-bank__filter-actions button{border-color:#0b8f87;background:#0b8f87;color:#fff!important}.question-bank__domains{margin-bottom:16px;overflow-x:auto;border:1px solid #d7e2e7;border-radius:11px;background:#fff}.question-bank__domains table{width:100%;border-collapse:collapse}.question-bank__domains th,.question-bank__domains td{padding:10px 12px;border-top:1px solid #e1e8ec;text-align:left}.question-bank__domains th{background:#f3f8f8;font-size:11px;text-transform:uppercase}.question-bank__result-head{display:flex;justify-content:space-between;gap:12px;margin-bottom:12px;padding:12px 15px;border-left:4px solid #0b8f87;border-radius:7px;background:#f3f8f8;font-weight:850}.question-bank__items{display:grid;gap:13px}.question-bank__item{padding:17px;border:1px solid #d5e0e6;border-radius:11px;background:#fff}.question-bank__meta{display:flex;flex-wrap:wrap;align-items:center;gap:7px;margin-bottom:10px}.question-bank__id{color:#102a43;font-size:16px;font-weight:950}.question-bank__badge{display:inline-flex;padding:4px 8px;border-radius:999px;background:#edf2f5;color:#455d6d;font-size:11px;font-weight:850}.question-bank__badge--ready{background:#ddf6ec;color:#076c55}.question-bank__badge--incomplete{background:#fff0dd;color:#a34d00}.question-bank__badge--exam{background:#eee8ff;color:#5630a8}.question-bank__item h3{margin:0 0 12px;font-size:18px;line-height:1.45}.question-bank__options{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px;margin:0 0 12px;padding:0;list-style:none}.question-bank__options li{padding:9px 11px;border:1px solid #e0e7eb;border-radius:7px;background:#f8fafc}.question-bank__options li.is-correct{border-color:#7bcbb6;background:#eaf9f3;color:#075f50;font-weight:850}.question-bank__explanation{margin-top:10px;padding:12px 14px;border-left:4px solid #0b8f87;border-radius:7px;background:#effaf7;line-height:1.5}.question-bank__source{margin:10px 0 0;color:#60758a;font-size:12px}.question-bank__missing{margin-top:10px;padding:10px 12px;border-left:4px solid #d97706;border-radius:7px;background:#fff7ed;color:#9a3412;font-weight:750}.question-bank__pagination{display:flex;flex-wrap:wrap;justify-content:center;gap:6px;margin-top:18px}.question-bank__pagination a{display:inline-flex;min-width:36px;min-height:36px;align-items:center;justify-content:center;border:1px solid #c6d4dc;border-radius:7px;background:#fff;text-decoration:none}.question-bank__pagination a[aria-current="page"]{border-color:#0b8f87;background:#0b8f87;color:#fff}@media(max-width:950px){.question-bank__filters{grid-template-columns:1fr 1fr}.question-bank__filter-actions{align-items:stretch}}@media(max-width:700px){.question-bank__hero{flex-direction:column}.question-bank__summary,.question-bank__filters,.question-bank__options{grid-template-columns:1fr}.question-bank__filter-actions{flex-direction:column}.question-bank__button{width:100%;box-sizing:border-box}}
</style>
<main class="question-bank">
    <header class="question-bank__hero"><div><h2>NCBAHM Question Bank</h2><p>Administrator-only inventory of the classified Chinese Herbology questions. Review each ID, answer, explanation and source before expanding or recombining the exam bank.</p></div><nav class="question-bank__actions" aria-label="Question bank actions"><a class="question-bank__button" href="<?php echo $reporturl->out(false); ?>">← Student Evaluations</a><a class="question-bank__button" href="<?php echo $languageurl->out(false); ?>"><?php echo $displaylang === 'es' ? 'English' : 'Español'; ?></a></nav></header>
    <section class="question-bank__summary" aria-label="Question bank totals"><article><span>Classified candidates</span><strong><?php echo (int)$totals['total']; ?></strong></article><article><span>Ready for practice</span><strong><?php echo (int)$totals['ready']; ?></strong></article><article><span>Simulation Exam 1</span><strong><?php echo (int)$totals['exam1']; ?></strong></article><article><span>Ready reserve bank</span><strong><?php echo (int)$totals['reserve']; ?></strong></article></section>
    <form class="question-bank__filters" method="get" action="<?php echo $pageurl->out_omit_querystring(false); ?>">
        <label><span>Search ID or text</span><input type="search" name="q" value="<?php echo s($query); ?>" placeholder="Example: DY-001, Ma Huang Tang"></label>
        <label><span>Domain</span><select name="domain"><option value="all">All domains</option><?php foreach ($domains as $domainid => $domain): ?><option value="<?php echo s($domainid); ?>"<?php echo $domainfilter === $domainid ? ' selected' : ''; ?>>Domain <?php echo (int)$domain['number']; ?> · <?php echo s($domain['name']); ?></option><?php endforeach; ?></select></label>
        <label><span>Readiness</span><select name="readiness"><option value="all">All questions</option><option value="ready"<?php echo $readinessfilter === 'ready' ? ' selected' : ''; ?>>Ready for practice</option><option value="incomplete"<?php echo $readinessfilter === 'incomplete' ? ' selected' : ''; ?>>Needs completion</option></select></label>
        <label><span>Exam use</span><select name="usage"><option value="all">All uses</option><option value="exam1"<?php echo $usagefilter === 'exam1' ? ' selected' : ''; ?>>Simulation Exam 1</option><option value="reserve"<?php echo $usagefilter === 'reserve' ? ' selected' : ''; ?>>Ready reserve bank</option></select></label>
        <input type="hidden" name="lang" value="<?php echo s($displaylang); ?>">
        <div class="question-bank__filter-actions"><button class="question-bank__button" type="submit">Apply</button><a class="question-bank__button" href="<?php echo $clearurl->out(false); ?>">Clear</a></div>
    </form>
    <section class="question-bank__domains"><table><thead><tr><th>Domain</th><th>Classified</th><th>Ready</th><th>Exam 1</th><th>Ready reserve</th></tr></thead><tbody><?php foreach ($domains as $domainid => $domain): ?><tr><td><strong>Domain <?php echo (int)$domain['number']; ?></strong> · <?php echo s($domain['name']); ?></td><td><?php echo (int)$domainstats[$domainid]['total']; ?></td><td><?php echo (int)$domainstats[$domainid]['ready']; ?></td><td><?php echo (int)$domainstats[$domainid]['exam1']; ?></td><td><?php echo (int)$domainstats[$domainid]['reserve']; ?></td></tr><?php endforeach; ?></tbody></table></section>
    <div class="question-bank__result-head"><span>Filtered questions</span><span><?php echo (int)$resultcount; ?> result<?php echo $resultcount === 1 ? '' : 's'; ?></span></div>
    <?php if ($pagequestions === []): ?><div class="question-bank__item">No questions match these filters.</div><?php else: ?><section class="question-bank__items"><?php foreach ($pagequestions as $question):
        $translation = $displaylang === 'es' ? ($question['translations']['es'] ?? []) : [];
        $prompt = trim((string)($translation['prompt'] ?? '')) ?: (string)($question['prompt'] ?? 'Question awaiting prompt');
        $options = count($translation['options'] ?? []) === 4 ? $translation['options'] : ($question['options'] ?? []);
        $explanation = trim((string)($translation['explanation'] ?? '')) ?: (string)($question['explanation'] ?? '');
        $correctlabel = (string)($question['correctLabel'] ?? '');
        $domain = $domains[$question['_domainid']];
    ?><article class="question-bank__item"><div class="question-bank__meta"><span class="question-bank__id"><?php echo s($question['questionId'] ?? 'Unnumbered'); ?></span><span class="question-bank__badge">Domain <?php echo (int)$domain['number']; ?> · <?php echo s($domain['name']); ?></span><span class="question-bank__badge question-bank__badge--<?php echo $question['_ready'] ? 'ready' : 'incomplete'; ?>"><?php echo $question['_ready'] ? 'Ready for practice' : 'Needs completion'; ?></span><?php if ($question['_examposition'] > 0): ?><span class="question-bank__badge question-bank__badge--exam">Simulation Exam 1 · position <?php echo (int)$question['_examposition']; ?></span><?php else: ?><span class="question-bank__badge">Not used in Exam 1</span><?php endif; ?><span class="question-bank__badge"><?php echo s(ucfirst((string)($question['difficultyBand'] ?? 'unclassified'))); ?></span></div><h3><?php echo s($prompt); ?></h3><ol class="question-bank__options"><?php foreach ($options as $option): $optionlabel = (string)($option['label'] ?? ''); ?><li class="<?php echo $optionlabel === $correctlabel ? 'is-correct' : ''; ?>"><strong><?php echo s($optionlabel); ?>.</strong> <?php echo s($option['text'] ?? ''); ?></li><?php endforeach; ?></ol><p><strong>Correct answer:</strong> <?php echo s($correctlabel !== '' ? $correctlabel : 'Not assigned'); ?></p><div class="question-bank__explanation"><strong>Explanation:</strong> <?php echo s($explanation !== '' ? $explanation : 'Not completed'); ?></div><p class="question-bank__source"><strong>Source:</strong> <?php echo s($question['source']['title'] ?? 'Not assigned'); ?><?php if (!empty($question['source']['locator'])): ?> · <?php echo s($question['source']['locator']); ?><?php endif; ?><?php
        $sourcecardurl = (string)($question['sourceCard'][$displaylang === 'es' ? 'urlEs' : 'url'] ?? '');
        $sourcecardlabel = (string)($question['sourceCard'][$displaylang === 'es' ? 'labelEs' : 'label'] ?? 'Open source card');
        if ($sourcecardurl !== ''):
        ?> · <a href="<?php echo s($sourcecardurl); ?>" target="_blank" rel="noopener"><?php echo s($sourcecardlabel); ?></a><?php endif; ?></p><?php if (!$question['_ready']): ?><div class="question-bank__missing">Missing for practice readiness: <?php echo s(implode(', ', $question['_missing'])); ?>.</div><?php endif; ?></article><?php endforeach; ?></section><?php endif; ?>
    <?php if ($totalpages > 1): ?><nav class="question-bank__pagination" aria-label="Question bank pages"><?php for ($pageindex = 0; $pageindex < $totalpages; $pageindex++): ?><a href="<?php echo (new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', array_merge($baseparams, ['page' => $pageindex])))->out(false); ?>"<?php echo $pageindex === $page ? ' aria-current="page"' : ''; ?>><?php echo $pageindex + 1; ?></a><?php endfor; ?></nav><?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
