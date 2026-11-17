<?php
// Administrator-only NCBAHM Chinese Herbology question bank browser.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/herbology-question-overrides.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$domainfilter = optional_param('domain', 'all', PARAM_ALPHANUMEXT);
$query = trim(optional_param('q', '', PARAM_TEXT));
$readinessfilter = optional_param('readiness', 'all', PARAM_ALPHA);
$usagefilter = optional_param('usage', 'all', PARAM_ALPHANUMEXT);
$sourcefilter = optional_param('source', 'all', PARAM_ALPHA);
$cognitivefilter = optional_param('cognitive', 'all', PARAM_ALPHA);
$contentfilter = optional_param('content', 'all', PARAM_ALPHANUMEXT);
$reviewfilter = optional_param('review', 'all', PARAM_ALPHANUMEXT);
$editquestionid = optional_param('edit', '', PARAM_ALPHANUMEXT);
$download = optional_param('download', '', PARAM_ALPHANUMEXT);
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
if (!in_array($usagefilter, ['all', 'exam1', 'exam2_review', 'reserve'], true)) {
    $usagefilter = 'all';
}
if (!in_array($sourcefilter, ['all', 'curated', 'scorm'], true)) {
    $sourcefilter = 'all';
}
if (!in_array($cognitivefilter, ['all', 'recall', 'application', 'inference', 'analysis'], true)) {
    $cognitivefilter = 'all';
}
if (!in_array($contentfilter, ['all', 'herb_properties', 'formula_hierarchy', 'herb_processing', 'clinical_case', 'other'], true)) {
    $contentfilter = 'all';
}
if (!in_array($reviewfilter, [
        'all', 'needs_inference', 'needs_feedback', 'simulation_candidate',
        'reclassification_requested', 'possible_exclusion',
    ], true)) {
    $reviewfilter = 'all';
}

$bankpath = $CFG->dirroot . '/blocks/menu/herbology-domain-candidates.json';
if (!is_readable($bankpath)) {
    throw new coding_exception('The NCBAHM Herbology question bank is not installed.');
}
$bank = json_decode((string)file_get_contents($bankpath), true);
if (!is_array($bank) || !isset($bank['domains']) || !is_array($bank['domains'])) {
    throw new coding_exception('The NCBAHM Herbology question bank is invalid.');
}
$scormbankpath = $CFG->dataroot . '/board-domain-banks/herbology-scorm-question-bank.json';
$scormquestioncount = 0;
if (is_readable($scormbankpath)) {
    $scormbank = json_decode((string)file_get_contents($scormbankpath), true);
    if (is_array($scormbank) && isset($scormbank['domains']) && is_array($scormbank['domains'])) {
        foreach ($domains as $domainid => $unused) {
            foreach (($scormbank['domains'][$domainid] ?? []) as $question) {
                if (!is_array($question)) {
                    continue;
                }
                $question['sourceKind'] = 'scorm';
                $bank['domains'][$domainid][] = $question;
                $scormquestioncount++;
            }
        }
    }
}
$basebank = $bank;
$revisionstore = block_myprogress_herbology_override_load();
$findquestion = static function(array $questionbank, string $questionid): ?array {
    foreach (($questionbank['domains'] ?? []) as $domainid => $questions) {
        foreach ($questions as $question) {
            if ((string)($question['questionId'] ?? '') === $questionid) {
                return ['domainId' => $domainid, 'question' => $question];
            }
        }
    }
    return null;
};

$formaction = optional_param('formaction', '', PARAM_ALPHA);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $formaction === 'editquestion') {
    require_sesskey();
    $questionid = required_param('questionid', PARAM_ALPHANUMEXT);
    $baseentry = $findquestion($basebank, $questionid);
    if ($baseentry === null) {
        throw new invalid_parameter_exception('Unknown Herbology question ID.');
    }
    $prompt = trim(required_param('prompt', PARAM_RAW));
    $explanation = trim(required_param('explanation', PARAM_RAW));
    $correctlabel = required_param('correctlabel', PARAM_ALPHA);
    $options = [];
    foreach (['A', 'B', 'C', 'D'] as $label) {
        $text = trim(required_param('option' . strtolower($label), PARAM_RAW));
        if ($text === '') {
            throw new invalid_parameter_exception('All four English answer choices are required.');
        }
        $options[] = ['label' => $label, 'text' => $text];
    }
    if ($prompt === '' || $explanation === '' || !in_array($correctlabel, ['A', 'B', 'C', 'D'], true)) {
        throw new invalid_parameter_exception('The English question, answer key and feedback are required.');
    }
    $normalizedoptions = array_map(
        static fn(array $option): string => core_text::strtolower(trim($option['text'])),
        $options
    );
    if (count(array_unique($normalizedoptions)) !== 4) {
        throw new invalid_parameter_exception('The four English answer choices must be different.');
    }

    $spanishprompt = trim(optional_param('promptes', '', PARAM_RAW));
    $spanishexplanation = trim(optional_param('explanationes', '', PARAM_RAW));
    $spanishoptions = [];
    foreach (['A', 'B', 'C', 'D'] as $label) {
        $spanishoptions[] = [
            'label' => $label,
            'text' => trim(optional_param('option' . strtolower($label) . 'es', '', PARAM_RAW)),
        ];
    }
    $hasspanish = $spanishprompt !== '' || $spanishexplanation !== ''
        || array_filter($spanishoptions, static fn(array $option): bool => $option['text'] !== '');
    if ($hasspanish && ($spanishprompt === '' || $spanishexplanation === ''
            || count(array_filter($spanishoptions, static fn(array $option): bool => $option['text'] !== '')) !== 4)) {
        throw new invalid_parameter_exception('Complete all Spanish fields or leave all of them empty.');
    }

    $contenttype = optional_param('contenttype', 'other', PARAM_ALPHANUMEXT);
    $cognitivelevel = optional_param('cognitivelevel', 'apply', PARAM_ALPHA);
    $difficultyband = optional_param('difficultyband', 'application', PARAM_ALPHA);
    if (!in_array($contenttype, [
            'recall', 'case', 'comparison', 'safety', 'herb_properties',
            'formula_hierarchy', 'herb_processing', 'clinical_case', 'other',
            'roster_aliases', 'dui_yao', 'safety_dispensing', 'formula_composition',
            'single_herb', 'formula_selection',
        ], true)
            || !in_array($cognitivelevel, ['remember', 'understand', 'apply', 'infer', 'analyze'], true)
            || !in_array($difficultyband, ['foundation', 'application', 'inference', 'analysis'], true)) {
        throw new invalid_parameter_exception('Invalid content, cognitive or complexity classification.');
    }
    $draft = [
        'prompt' => $prompt,
        'options' => $options,
        'correctLabel' => $correctlabel,
        'explanation' => $explanation,
        'contentType' => $contenttype,
        'cognitiveLevel' => $cognitivelevel,
        'difficultyBand' => $difficultyband,
    ];
    if ($hasspanish) {
        $draft['translations'] = ['es' => [
            'prompt' => $spanishprompt,
            'options' => $spanishoptions,
            'explanation' => $spanishexplanation,
        ]];
    }
    $requestreclassification = (bool)optional_param('requestreclassification', 0, PARAM_BOOL);
    $returnusage = optional_param('returnusage', '', PARAM_ALPHANUMEXT);
    if (!in_array($returnusage, ['exam1', 'exam2_review', 'reserve'], true)) {
        $returnusage = '';
    }
    $returnpage = max(0, optional_param('returnpage', 0, PARAM_INT));
    $savemode = optional_param('savemode', 'draft', PARAM_ALPHA);
    if (!in_array($savemode, ['draft', 'publish', 'exclude', 'restore'], true)) {
        $savemode = 'draft';
    }
    $exclusionreason = trim(optional_param('exclusionreason', '', PARAM_TEXT));
    if ($savemode === 'exclude' && $exclusionreason === '') {
        throw new invalid_parameter_exception('Explain why this question may need exclusion.');
    }
    $status = $savemode === 'exclude'
        ? 'exclusion_candidate'
        : ($savemode === 'restore'
            ? 'draft'
            : ($requestreclassification
                ? 'pending_reclassification'
                : ($savemode === 'publish' ? 'approved' : 'draft')));
    if ($status === 'exclusion_candidate' || $savemode === 'restore') {
        $requestreclassification = false;
    }
    $savedat = gmdate(DATE_ATOM);
    $savedbyname = fullname($USER);
    $basequestion = $baseentry['question'];
    $original = array_intersect_key($basequestion, array_flip([
        'questionId', 'domainId', 'prompt', 'options', 'correctLabel', 'explanation',
        'translations', 'contentType', 'cognitiveLevel', 'difficultyBand', 'source',
        'sourceCard', 'examTemplate',
    ]));
    block_myprogress_herbology_override_update(static function(array $store) use (
        $questionid,
        $baseentry,
        $original,
        $draft,
        $status,
        $requestreclassification,
        $exclusionreason,
        $savedat,
        $savedbyname,
        $USER
    ): array {
        $previous = $store['questions'][$questionid] ?? null;
        $history = is_array($previous['history'] ?? null) ? $previous['history'] : [];
        if (is_array($previous)) {
            $history[] = array_diff_key($previous, ['history' => true, 'original' => true]);
            $history = array_slice($history, -20);
        }
        $approved = is_array($previous['approved'] ?? null) ? $previous['approved'] : null;
        if ($approved === null && ($previous['status'] ?? '') === 'approved'
                && is_array($previous['draft'] ?? null)) {
            $approved = $previous['draft'];
        }
        if ($status === 'approved') {
            $approved = $draft;
        }
        $store['questions'][$questionid] = [
            'questionId' => $questionid,
            'domainId' => (string)$baseentry['domainId'],
            'status' => $status,
            'requestReclassification' => $requestreclassification,
            'exclusionReason' => $status === 'exclusion_candidate' ? $exclusionreason : '',
            'revisionNumber' => (int)($previous['revisionNumber'] ?? 0) + 1,
            'savedAt' => $savedat,
            'savedById' => (int)$USER->id,
            'savedByName' => $savedbyname,
            'original' => $previous['original'] ?? $original,
            'approved' => $approved,
            'draft' => $draft,
            'history' => $history,
        ];
        return $store;
    });
    $message = $status === 'approved'
        ? 'The approved revision is now active in the question bank.'
        : ($status === 'pending_reclassification'
            ? 'Draft saved and added to the reclassification queue. The approved exam version is unchanged.'
            : ($status === 'exclusion_candidate'
                ? 'Question moved to Possible exclusions and removed from active practice and exams.'
                : ($savemode === 'restore'
                    ? 'Question restored from Possible exclusions as a draft. Review it before publishing.'
                    : 'Draft saved. The approved exam version is unchanged.')));
    $redirectparams = ['edit' => $questionid];
    if ($returnusage !== '') {
        $redirectparams['usage'] = $returnusage;
    }
    if ($returnpage > 0) {
        $redirectparams['page'] = $returnpage;
    }
    redirect(
        new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', $redirectparams),
        $message,
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$revisionstore = block_myprogress_herbology_override_load();
if (in_array($download, ['review_queue', 'exclusion_queue'], true)) {
    $targetstatus = $download === 'exclusion_queue'
        ? 'exclusion_candidate'
        : 'pending_reclassification';
    $queue = [];
    foreach (($revisionstore['questions'] ?? []) as $questionid => $revision) {
        if (($revision['status'] ?? '') === $targetstatus) {
            $queue[] = $revision + ['questionId' => $questionid];
        }
    }
    \core\session\manager::write_close();
    header('Content-Type: application/json; charset=utf-8');
    $filename = $download === 'exclusion_queue'
        ? 'ncbahm-question-possible-exclusions.json'
        : 'ncbahm-question-reclassification-queue.json';
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo json_encode([
        'schemaVersion' => 1,
        'exportedAt' => gmdate(DATE_ATOM),
        'queueType' => $targetstatus,
        'questionCount' => count($queue),
        'questions' => $queue,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo PHP_EOL;
    exit;
}
$bank['domains'] = block_myprogress_herbology_apply_revisions_to_domains(
    $bank['domains'],
    $revisionstore,
    true
);
$pendingreclassificationcount = count(array_filter(
    $revisionstore['questions'] ?? [],
    static fn(array $revision): bool => ($revision['status'] ?? '') === 'pending_reclassification'
));
$possibleexclusioncount = count(array_filter(
    $revisionstore['questions'] ?? [],
    static fn(array $revision): bool => ($revision['status'] ?? '') === 'exclusion_candidate'
));

$cognitivebucket = static function(array $question): string {
    $value = strtolower(trim((string)($question['cognitiveLevel'] ?? '')));
    if (in_array($value, ['analysis', 'analyze'], true)) return 'analysis';
    if (in_array($value, ['inference', 'infer'], true)) return 'inference';
    if (in_array($value, ['application', 'apply'], true)) return 'application';
    return 'recall';
};

$isready = static function(array $question): bool {
    return empty($question['_excludedFromUse'])
        && trim((string)($question['prompt'] ?? '')) !== ''
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
    if (!empty($question['_excludedFromUse'])) $missing[] = 'possible exclusion review';
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
    $domainstats[$domainid] = ['total' => 0, 'ready' => 0, 'exam1' => 0, 'exam2' => 0, 'reserve' => 0, 'scorm' => 0];
    foreach (($bank['domains'][$domainid] ?? []) as $question) {
        $ready = $isready($question);
        $isexcluded = !empty($question['_excludedFromUse']);
        $configuredexamposition = (int)($question['examTemplate']['globalPosition'] ?? 0);
        $examposition = $isexcluded ? 0 : $configuredexamposition;
        $domainstats[$domainid]['total']++;
        if ($ready) $domainstats[$domainid]['ready']++;
        if ($examposition > 0) $domainstats[$domainid]['exam1']++;
        if ($ready && $examposition === 0) $domainstats[$domainid]['reserve']++;
        if (($question['sourceKind'] ?? 'curated') === 'scorm') $domainstats[$domainid]['scorm']++;
        $question['_domainid'] = $domainid;
        $question['_ready'] = $ready;
        $question['_examposition'] = $examposition;
        $question['_configuredexamposition'] = $configuredexamposition;
        $question['_exam2position'] = 0;
        $question['_missing'] = $missingfields($question);
        $question['_sourcekind'] = ($question['sourceKind'] ?? 'curated') === 'scorm' ? 'scorm' : 'curated';
        $question['_cognitive'] = $cognitivebucket($question);
        $editorial = is_array($question['editorial'] ?? null) ? $question['editorial'] : [];
        $question['_needsinference'] = (bool)($editorial['inferenceUpgradeRequired'] ?? false);
        $question['_feedbackquality'] = (string)($editorial['feedbackQuality'] ?? ($question['_ready'] ? 'curated' : 'missing'));
        $question['_simulationcandidate'] = !$isexcluded && (bool)($editorial['simulationCandidate'] ?? false);
        $question['_excluded'] = $isexcluded;
        $question['_revision'] = is_array($question['_editorialRevision'] ?? null)
            ? $question['_editorialRevision']
            : [];
        $allquestions[] = $question;
    }
}

$exam2slotsbydomain = array_fill_keys(array_keys($domains), []);
foreach ($allquestions as $question) {
    if ($question['_configuredexamposition'] > 0) {
        $exam2slotsbydomain[$question['_domainid']][] = $question['_configuredexamposition'];
    }
}
foreach ($exam2slotsbydomain as &$positions) {
    sort($positions, SORT_NUMERIC);
}
unset($positions);
$exam2targetcount = array_sum(array_map('count', $exam2slotsbydomain));

$exam2eligiblebydomain = array_fill_keys(array_keys($domains), []);
foreach ($allquestions as $question) {
    if ($question['_ready'] && !$question['_excluded'] && $question['_configuredexamposition'] === 0) {
        $exam2eligiblebydomain[$question['_domainid']][] = $question;
    }
}
$exam2quality = static function(array $question): array {
    $cognitiverank = ['analysis' => 0, 'inference' => 1, 'application' => 2, 'recall' => 3];
    $feedbackrank = in_array($question['_feedbackquality'], ['detailed', 'curated'], true) ? 0 : 1;
    return [
        $question['_simulationcandidate'] ? 0 : 1,
        $cognitiverank[$question['_cognitive']] ?? 4,
        $feedbackrank,
        hash('sha256', 'ncbahm-simulation-exam-2|' . (string)($question['questionId'] ?? '')),
    ];
};
$exam2positions = [];
$exam2shortfalls = [];
foreach ($domains as $domainid => $unused) {
    usort($exam2eligiblebydomain[$domainid], static function(array $left, array $right) use ($exam2quality): int {
        return $exam2quality($left) <=> $exam2quality($right);
    });
    $slots = $exam2slotsbydomain[$domainid];
    $selected = array_slice($exam2eligiblebydomain[$domainid], 0, count($slots));
    foreach ($selected as $index => $question) {
        $exam2positions[(string)$question['questionId']] = (int)$slots[$index];
    }
    if (count($selected) < count($slots)) {
        $exam2shortfalls[$domainid] = count($slots) - count($selected);
    }
}
$exam2reviewedcount = 0;
foreach ($allquestions as &$question) {
    $questionid = (string)($question['questionId'] ?? '');
    $question['_exam2position'] = (int)($exam2positions[$questionid] ?? 0);
    if ($question['_exam2position'] > 0) {
        $domainstats[$question['_domainid']]['exam2']++;
        if (($question['_revision']['status'] ?? '') === 'approved') {
            $exam2reviewedcount++;
        }
    }
}
unset($question);
$exam2candidatecount = count($exam2positions);

$totals = [
    'total' => count($allquestions),
    'ready' => count(array_filter($allquestions, static fn(array $question): bool => $question['_ready'])),
    'exam1' => count(array_filter($allquestions, static fn(array $question): bool => $question['_examposition'] > 0)),
];
$totals['reserve'] = count(array_filter(
    $allquestions,
    static fn(array $question): bool => $question['_ready'] && $question['_examposition'] === 0
));
$scormneedsinference = count(array_filter(
    $allquestions,
    static fn(array $question): bool => $question['_sourcekind'] === 'scorm' && $question['_needsinference']
));
$scormneedsfeedback = count(array_filter(
    $allquestions,
    static fn(array $question): bool => $question['_sourcekind'] === 'scorm'
        && in_array($question['_feedbackquality'], ['missing', 'brief'], true)
));
$scormsimulationcandidates = count(array_filter(
    $allquestions,
    static fn(array $question): bool => $question['_sourcekind'] === 'scorm' && $question['_simulationcandidate']
));

$filteredquestions = array_values(array_filter(
    $allquestions,
    static function(array $question) use (
        $domainfilter,
        $query,
        $readinessfilter,
        $usagefilter,
        $sourcefilter,
        $cognitivefilter,
        $contentfilter,
        $reviewfilter
    ): bool {
        if ($domainfilter !== 'all' && $question['_domainid'] !== $domainfilter) return false;
        if ($readinessfilter === 'ready' && !$question['_ready']) return false;
        if ($readinessfilter === 'incomplete' && $question['_ready']) return false;
        if ($usagefilter === 'exam1' && $question['_examposition'] === 0) return false;
        if ($usagefilter === 'exam2_review' && $question['_exam2position'] === 0) return false;
        if ($usagefilter === 'reserve' && (!$question['_ready'] || $question['_examposition'] > 0)) return false;
        if ($sourcefilter !== 'all' && $question['_sourcekind'] !== $sourcefilter) return false;
        if ($cognitivefilter !== 'all' && $question['_cognitive'] !== $cognitivefilter) return false;
        if ($reviewfilter === 'needs_inference' && !$question['_needsinference']) return false;
        if ($reviewfilter === 'needs_feedback' && !in_array($question['_feedbackquality'], ['missing', 'brief'], true)) return false;
        if ($reviewfilter === 'simulation_candidate' && !$question['_simulationcandidate']) return false;
        if ($reviewfilter === 'reclassification_requested'
                && ($question['_revision']['status'] ?? '') !== 'pending_reclassification') return false;
        if ($reviewfilter === 'possible_exclusion'
                && ($question['_revision']['status'] ?? '') !== 'exclusion_candidate') return false;
        $questioncontent = (string)($question['contentType'] ?? 'other');
        if ($contentfilter !== 'all') {
            if ($contentfilter === 'other') {
                if (in_array($questioncontent, ['herb_properties', 'formula_hierarchy', 'herb_processing', 'clinical_case'], true)) return false;
            } else if ($questioncontent !== $contentfilter) {
                return false;
            }
        }
        if ($query !== '') {
            $translation = $question['translations']['es'] ?? [];
            $searchparts = [
                $question['questionId'] ?? '',
                $question['prompt'] ?? '',
                $question['explanation'] ?? '',
                $translation['prompt'] ?? '',
                $translation['explanation'] ?? '',
                $question['source']['title'] ?? '',
                $question['source']['section'] ?? '',
                $question['contentType'] ?? '',
            ];
            foreach ($question['options'] ?? [] as $option) $searchparts[] = $option['text'] ?? '';
            foreach ($translation['options'] ?? [] as $option) $searchparts[] = $option['text'] ?? '';
            $haystack = core_text::strtolower(implode(' ', $searchparts));
            if (strpos($haystack, core_text::strtolower($query)) === false) return false;
        }
        return true;
    }
));
usort($filteredquestions, static function(array $left, array $right) use ($domains, $usagefilter): int {
    if ($usagefilter === 'exam2_review') {
        return $left['_exam2position'] <=> $right['_exam2position'];
    }
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
    'source' => $sourcefilter !== 'all' ? $sourcefilter : null,
    'cognitive' => $cognitivefilter !== 'all' ? $cognitivefilter : null,
    'content' => $contentfilter !== 'all' ? $contentfilter : null,
    'review' => $reviewfilter !== 'all' ? $reviewfilter : null,
    'lang' => $displaylang !== 'en' ? $displaylang : null,
]);
$editquestion = null;
if ($editquestionid !== '') {
    foreach ($allquestions as $candidatequestion) {
        if ((string)($candidatequestion['questionId'] ?? '') === $editquestionid) {
            $editquestion = $candidatequestion;
            break;
        }
    }
    if ($editquestion === null) {
        throw new invalid_parameter_exception('Unknown Herbology question ID.');
    }
}
$pageurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', $baseparams);
$reporturl = new moodle_url('/blocks/myprogress/admin-ncbahm-evaluations.php');
$clearurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php');
$queueurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', ['download' => 'review_queue']);
$exclusionurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', ['review' => 'possible_exclusion']);
$exclusionqueueurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', ['download' => 'exclusion_queue']);
$exam2url = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', ['usage' => 'exam2_review']);
$languageurl = new moodle_url(
    '/blocks/myprogress/admin-herbology-question-bank.php',
    array_merge($baseparams, ['lang' => $displaylang === 'es' ? 'en' : 'es'])
);
$editoroptions = [];
$editorspanishoptions = [];
$editortranslation = [];
$editorrevision = [];
$editorrevisionfull = [];
$suggestedexclusionnotes = [
    'SCORM-SH-0AB93AB99753' => 'The stem appears to combine Wind Bi findings (migrating pain) with Cold Bi findings (no redness or heat and a tight wiry pulse), while the feedback shifts to Wu Tou Tang and formula ingredients that are not established by the stem or answer. Verify the source context and answer key before reuse.',
];
$editorexclusionnote = '';
$contenttypelabels = [
    'recall' => 'Knowledge / recall',
    'case' => 'Clinical case',
    'comparison' => 'Comparison / differentiation',
    'safety' => 'Safety',
    'herb_properties' => 'Herb properties',
    'formula_hierarchy' => 'Formula hierarchy',
    'herb_processing' => 'Herb processing / preparation',
    'clinical_case' => 'Clinical case (classified)',
    'roster_aliases' => 'Roster boundaries / aliases',
    'dui_yao' => 'Duì Yào / compatibility',
    'safety_dispensing' => 'Safety / dispensing',
    'formula_composition' => 'Formula composition',
    'single_herb' => 'Individual herb',
    'formula_selection' => 'Formula selection',
    'other' => 'Other content',
];
$cognitivelevellabels = [
    'remember' => 'Recall / direct',
    'understand' => 'Understanding',
    'apply' => 'Application',
    'infer' => 'Inference',
    'analyze' => 'Analysis / differential',
];
$difficultylabels = [
    'foundation' => 'Foundation',
    'application' => 'Application',
    'inference' => 'Inference',
    'analysis' => 'Analysis',
];
if ($editquestion !== null) {
    foreach ($editquestion['options'] ?? [] as $option) {
        $editoroptions[(string)($option['label'] ?? '')] = (string)($option['text'] ?? '');
    }
    $editortranslation = is_array($editquestion['translations']['es'] ?? null)
        ? $editquestion['translations']['es']
        : [];
    foreach ($editortranslation['options'] ?? [] as $option) {
        $editorspanishoptions[(string)($option['label'] ?? '')] = (string)($option['text'] ?? '');
    }
    $editorrevision = $editquestion['_revision'] ?? [];
    $editorrevisionfull = is_array($revisionstore['questions'][$editquestion['questionId']] ?? null)
        ? $revisionstore['questions'][$editquestion['questionId']]
        : [];
    $editorexclusionnote = (string)($editorrevisionfull['exclusionReason']
        ?? $suggestedexclusionnotes[$editquestion['questionId']]
        ?? '');
}

$PAGE->set_url($pageurl);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('report');
$PAGE->set_title('NCBAHM Question Bank');
$PAGE->set_heading('NCBAHM Question Bank');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
?>
<style>
.question-bank{max-width:1380px;margin:0 auto 45px;color:#173448}.question-bank__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:16px;padding:22px 24px;border-left:5px solid #0b8f87;border-radius:12px;background:linear-gradient(135deg,#eef9f6,#fff8ef)}.question-bank__hero h2{margin:0 0 5px;font-size:30px}.question-bank__hero p{max-width:850px;margin:0;color:#52677a;line-height:1.5}.question-bank__actions{display:flex;flex-wrap:wrap;gap:8px}.question-bank__button{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:8px 13px;border:1px solid #b8c8d2;border-radius:8px;background:#fff;color:#24465b!important;font-weight:850;text-decoration:none!important}.question-bank__button:hover{border-color:#0b8f87;background:#eef9f6}.question-bank__summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:16px}.question-bank__summary article{padding:15px;border:1px solid #d5e0e6;border-radius:10px;background:#fff}.question-bank__summary span{display:block;color:#64748b;font-size:12px;font-weight:800}.question-bank__summary strong{display:block;margin-top:4px;color:#08746f;font-size:25px}.question-bank__filters{display:grid;grid-template-columns:repeat(4,minmax(180px,1fr));gap:10px;margin-bottom:16px;padding:15px;border:1px solid #d7e2e7;border-radius:11px;background:#fff}.question-bank__filters label span{display:block;margin-bottom:5px;font-size:12px;font-weight:850}.question-bank__filters input,.question-bank__filters select{width:100%;min-height:42px;box-sizing:border-box;padding:8px 10px;border:1px solid #b9cbd4;border-radius:7px;background:#fff}.question-bank__filter-actions{display:flex;align-items:end;gap:7px}.question-bank__filter-actions button{border-color:#0b8f87;background:#0b8f87;color:#fff!important}.question-bank__domains{margin-bottom:16px;overflow-x:auto;border:1px solid #d7e2e7;border-radius:11px;background:#fff}.question-bank__domains table{width:100%;border-collapse:collapse}.question-bank__domains th,.question-bank__domains td{padding:10px 12px;border-top:1px solid #e1e8ec;text-align:left}.question-bank__domains th{background:#f3f8f8;font-size:11px;text-transform:uppercase}.question-bank__result-head{display:flex;justify-content:space-between;gap:12px;margin-bottom:12px;padding:12px 15px;border-left:4px solid #0b8f87;border-radius:7px;background:#f3f8f8;font-weight:850}.question-bank__items{display:grid;gap:13px}.question-bank__item{padding:17px;border:1px solid #d5e0e6;border-radius:11px;background:#fff}.question-bank__meta{display:flex;flex-wrap:wrap;align-items:center;gap:7px;margin-bottom:10px}.question-bank__id{color:#102a43;font-size:16px;font-weight:950}.question-bank__badge{display:inline-flex;padding:4px 8px;border-radius:999px;background:#edf2f5;color:#455d6d;font-size:11px;font-weight:850}.question-bank__badge--ready{background:#ddf6ec;color:#076c55}.question-bank__badge--incomplete{background:#fff0dd;color:#a34d00}.question-bank__badge--exam{background:#eee8ff;color:#5630a8}.question-bank__badge--scorm{background:#e8f1ff;color:#1d4f91}.question-bank__item h3{margin:0 0 12px;font-size:18px;line-height:1.45}.question-bank__options{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px;margin:0 0 12px;padding:0;list-style:none}.question-bank__options li{padding:9px 11px;border:1px solid #e0e7eb;border-radius:7px;background:#f8fafc}.question-bank__options li.is-correct{border-color:#7bcbb6;background:#eaf9f3;color:#075f50;font-weight:850}.question-bank__explanation{margin-top:10px;padding:12px 14px;border-left:4px solid #0b8f87;border-radius:7px;background:#effaf7;line-height:1.5}.question-bank__source{margin:10px 0 0;color:#60758a;font-size:12px}.question-bank__missing{margin-top:10px;padding:10px 12px;border-left:4px solid #d97706;border-radius:7px;background:#fff7ed;color:#9a3412;font-weight:750}.question-bank__pagination{display:flex;flex-wrap:wrap;justify-content:center;gap:6px;margin-top:18px}.question-bank__pagination a{display:inline-flex;min-width:36px;min-height:36px;align-items:center;justify-content:center;border:1px solid #c6d4dc;border-radius:7px;background:#fff;text-decoration:none}.question-bank__pagination a[aria-current="page"]{border-color:#0b8f87;background:#0b8f87;color:#fff}@media(max-width:1050px){.question-bank__summary{grid-template-columns:repeat(2,minmax(0,1fr))}.question-bank__filters{grid-template-columns:1fr 1fr}.question-bank__filter-actions{align-items:stretch}}@media(max-width:700px){.question-bank__hero{flex-direction:column}.question-bank__summary,.question-bank__filters,.question-bank__options{grid-template-columns:1fr}.question-bank__filter-actions{flex-direction:column}.question-bank__button{width:100%;box-sizing:border-box}}
.question-bank__editor{margin-bottom:18px;padding:20px;border:2px solid #6d28d9;border-radius:12px;background:#faf8ff}.question-bank__editor-head{display:flex;justify-content:space-between;gap:16px;margin-bottom:14px}.question-bank__editor-head h3{margin:0 0 4px;font-size:23px}.question-bank__editor-head p{margin:0;color:#66557e}.question-bank__editor-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.question-bank__editor-panel{padding:15px;border:1px solid #ded7ef;border-radius:9px;background:#fff}.question-bank__editor-panel h4{margin:0 0 12px}.question-bank__editor-field{display:block;margin-bottom:11px}.question-bank__editor-field>span{display:block;margin-bottom:5px;font-size:12px;font-weight:850}.question-bank__editor input[type=text],.question-bank__editor textarea,.question-bank__editor select{box-sizing:border-box;width:100%;padding:9px 10px;border:1px solid #b8c8d2;border-radius:7px;background:#fff}.question-bank__editor textarea{min-height:92px;resize:vertical}.question-bank__editor-classification{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:13px}.question-bank__editor-check{display:flex;align-items:flex-start;gap:9px;margin:15px 0;padding:13px;border-left:4px solid #d97706;border-radius:7px;background:#fff7ed}.question-bank__editor-check input{margin-top:4px}.question-bank__editor-actions{display:flex;flex-wrap:wrap;gap:8px}.question-bank__button--primary{border-color:#0b8f87;background:#0b8f87;color:#fff!important}.question-bank__button--publish{border-color:#6d28d9;background:#6d28d9;color:#fff!important}.question-bank__button--exclude{border-color:#b42318;background:#fff1f0;color:#9f1d16!important}.question-bank__revision-note{margin:10px 0;padding:10px 12px;border-left:4px solid #6d28d9;border-radius:7px;background:#f4f0ff;color:#4c1d95}.question-bank__exclusion-note{margin:10px 0;padding:12px 14px;border-left:4px solid #b42318;border-radius:7px;background:#fff1f0;color:#7a271a}.question-bank__item--excluded{border-color:#f1a6a0;background:#fffafa}.question-bank__edit-link{margin-left:auto}@media(max-width:850px){.question-bank__editor-grid,.question-bank__editor-classification{grid-template-columns:1fr}.question-bank__editor-head{display:block}.question-bank__editor-head .question-bank__button{margin-top:10px}}
.question-bank__temporary-order{display:flex;align-items:center;gap:10px;margin:-17px -17px 14px;padding:11px 16px;border-radius:10px 10px 0 0;background:#4c1d95;color:#fff}.question-bank__temporary-order span{font-size:12px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.question-bank__temporary-order strong{font-size:24px;line-height:1}.question-bank__temporary-order small{font-weight:800;opacity:.85}.question-bank__temporary-order--editor{margin:0 0 12px;border-radius:8px}.question-bank__review-progress{margin-bottom:16px;padding:14px 16px;border-left:5px solid #6d28d9;border-radius:9px;background:#f4f0ff;color:#4c1d95}.question-bank__review-progress strong{font-size:17px}.question-bank__review-progress p{margin:4px 0 0}
</style>
<main class="question-bank">
    <header class="question-bank__hero"><div><h2>NCBAHM Question Bank</h2><p>Administrator-only inventory of curated and SCORM Chinese Herbology questions. Review each ID, domain, cognitive level, answer, explanation and source before approving it for an exam.</p></div><nav class="question-bank__actions" aria-label="Question bank actions"><a class="question-bank__button" href="<?php echo $reporturl->out(false); ?>">← Student Evaluations</a><?php if ($pendingreclassificationcount > 0): ?><a class="question-bank__button" href="<?php echo $queueurl->out(false); ?>">Export reclassification queue (<?php echo (int)$pendingreclassificationcount; ?>)</a><?php endif; ?><?php if ($possibleexclusioncount > 0): ?><a class="question-bank__button question-bank__button--exclude" href="<?php echo $exclusionurl->out(false); ?>">Possible exclusions (<?php echo (int)$possibleexclusioncount; ?>)</a><a class="question-bank__button" href="<?php echo $exclusionqueueurl->out(false); ?>">Export exclusions</a><?php endif; ?><a class="question-bank__button" href="<?php echo $languageurl->out(false); ?>"><?php echo $displaylang === 'es' ? 'English' : 'Español'; ?></a></nav></header>
    <section class="question-bank__review-progress" aria-label="Simulation Exam 2 review progress"><strong><?php echo $usagefilter === 'exam2_review' ? 'Simulation Exam 2 review set' : 'Review one simulation exam at a time'; ?></strong><p><?php if ($usagefilter === 'exam2_review'): ?><?php echo (int)$exam2reviewedcount; ?> of <?php echo (int)$exam2candidatecount; ?> candidate questions have an approved review. Candidates follow the 100-question Exam 1 domain blueprint and exclude Exam 1 questions and recycle-bin items.<?php if ($exam2shortfalls !== []): ?> The current bank is short <?php echo (int)array_sum($exam2shortfalls); ?> required domain question<?php echo array_sum($exam2shortfalls) === 1 ? '' : 's'; ?>.<?php endif; ?><?php else: ?><a class="question-bank__button question-bank__button--publish" href="<?php echo $exam2url->out(false); ?>">Review Simulation Exam 2 candidates (<?php echo (int)$exam2candidatecount; ?>)</a><?php endif; ?></p></section>
    <?php if ($editquestion !== null):
        $editorstatus = (string)($editorrevision['status'] ?? 'original');
        $editorstatuslabel = [
            'draft' => 'Draft — not published',
            'pending_reclassification' => 'Reclassification requested — not published',
            'exclusion_candidate' => 'Possible exclusion — removed from active use',
            'approved' => 'Approved revision — active in exams',
            'original' => 'Original bank version',
        ][$editorstatus] ?? 'Original bank version';
        $editordomain = $domains[$editquestion['_domainid']];
    ?>
    <form id="question-editor" class="question-bank__editor" method="post" action="<?php echo $pageurl->out_omit_querystring(false); ?>">
        <?php if ($editquestion['_exam2position'] > 0): ?><div class="question-bank__temporary-order question-bank__temporary-order--editor"><span>Temporary review order</span><strong>Proposed Exam 2 Question <?php echo str_pad((string)$editquestion['_exam2position'], 3, '0', STR_PAD_LEFT); ?></strong><small>of <?php echo (int)$exam2targetcount; ?></small></div><?php endif; ?>
        <div class="question-bank__editor-head"><div><h3>Edit <?php echo s($editquestion['questionId']); ?></h3><p><?php echo s($editorstatuslabel); ?> · Domain <?php echo (int)$editordomain['number']; ?> remains fixed to protect the exam blueprint.</p></div><a class="question-bank__button" href="<?php echo $pageurl->out(false); ?>">Cancel editing</a></div>
        <?php if ($editorrevision !== []): ?><div class="question-bank__revision-note">Revision <?php echo (int)($editorrevision['revisionNumber'] ?? 1); ?> saved by <?php echo s($editorrevision['savedByName'] ?? 'administrator'); ?><?php if (!empty($editorrevision['savedAt'])): ?> on <?php echo s(userdate(strtotime($editorrevision['savedAt']))); ?><?php endif; ?>.<?php if ($editorstatus === 'exclusion_candidate'): ?> This question is preserved in the recycle bin and unavailable to active practice and exams.<?php elseif ($editorstatus !== 'approved'): ?> The currently approved exam version remains unchanged.<?php endif; ?><?php if (!empty($editorrevisionfull['history'])): ?> <details><summary>Revision history (<?php echo count($editorrevisionfull['history']); ?>)</summary><ol><?php foreach (array_reverse($editorrevisionfull['history']) as $historicalrevision): ?><li>Revision <?php echo (int)($historicalrevision['revisionNumber'] ?? 1); ?> · <?php echo s(str_replace('_', ' ', (string)($historicalrevision['status'] ?? 'draft'))); ?> · <?php echo s($historicalrevision['savedByName'] ?? 'administrator'); ?><?php if (!empty($historicalrevision['savedAt'])): ?> · <?php echo s(userdate(strtotime($historicalrevision['savedAt']))); ?><?php endif; ?></li><?php endforeach; ?></ol></details><?php endif; ?></div><?php endif; ?>
        <div class="question-bank__editor-grid">
            <section class="question-bank__editor-panel"><h4>English (required)</h4>
                <label class="question-bank__editor-field"><span>Question</span><textarea name="prompt" required><?php echo s($editquestion['prompt'] ?? ''); ?></textarea></label>
                <?php foreach (['A', 'B', 'C', 'D'] as $label): ?><label class="question-bank__editor-field"><span>Answer <?php echo $label; ?></span><input type="text" name="option<?php echo strtolower($label); ?>" value="<?php echo s($editoroptions[$label] ?? ''); ?>" required></label><?php endforeach; ?>
                <label class="question-bank__editor-field"><span>Correct answer</span><select name="correctlabel" required><?php foreach (['A', 'B', 'C', 'D'] as $label): ?><option value="<?php echo $label; ?>"<?php echo ($editquestion['correctLabel'] ?? '') === $label ? ' selected' : ''; ?>><?php echo $label; ?></option><?php endforeach; ?></select></label>
                <label class="question-bank__editor-field"><span>Answer feedback / explanation</span><textarea name="explanation" required><?php echo s($editquestion['explanation'] ?? ''); ?></textarea></label>
            </section>
            <section class="question-bank__editor-panel"><h4>Spanish (optional, but complete as a set)</h4>
                <label class="question-bank__editor-field"><span>Pregunta</span><textarea name="promptes"><?php echo s($editortranslation['prompt'] ?? ''); ?></textarea></label>
                <?php foreach (['A', 'B', 'C', 'D'] as $label): ?><label class="question-bank__editor-field"><span>Respuesta <?php echo $label; ?></span><input type="text" name="option<?php echo strtolower($label); ?>es" value="<?php echo s($editorspanishoptions[$label] ?? ''); ?>"></label><?php endforeach; ?>
                <label class="question-bank__editor-field"><span>Retroalimentación / explicación</span><textarea name="explanationes"><?php echo s($editortranslation['explanation'] ?? ''); ?></textarea></label>
            </section>
        </div>
        <div class="question-bank__editor-classification">
            <label class="question-bank__editor-field"><span>Content classification</span><select name="contenttype"><?php foreach ($contenttypelabels as $value => $label): ?><option value="<?php echo s($value); ?>"<?php echo ($editquestion['contentType'] ?? 'other') === $value ? ' selected' : ''; ?>><?php echo s($label); ?></option><?php endforeach; ?></select></label>
            <label class="question-bank__editor-field"><span>Cognitive level</span><select name="cognitivelevel"><?php foreach ($cognitivelevellabels as $value => $label): ?><option value="<?php echo s($value); ?>"<?php echo ($editquestion['cognitiveLevel'] ?? 'apply') === $value ? ' selected' : ''; ?>><?php echo s($label); ?></option><?php endforeach; ?></select></label>
            <label class="question-bank__editor-field"><span>Complexity</span><select name="difficultyband"><?php foreach ($difficultylabels as $value => $label): ?><option value="<?php echo s($value); ?>"<?php echo ($editquestion['difficultyBand'] ?? 'application') === $value ? ' selected' : ''; ?>><?php echo s($label); ?></option><?php endforeach; ?></select></label>
        </div>
        <label class="question-bank__editor-check"><input type="checkbox" name="requestreclassification" value="1"<?php echo !empty($editorrevision['requestReclassification']) ? ' checked' : ''; ?>><span><strong>Request reclassification after this edit</strong><br>Check this when the wording, answer, feedback or educational objective changed enough that the domain, content type, cognitive level, complexity or exam placement should be reviewed again. The draft will enter the exportable review queue and will not replace the approved exam version.</span></label>
        <label class="question-bank__editor-field"><span>Possible error / exclusion note</span><textarea name="exclusionreason" placeholder="Describe the conflict, unsupported source, ambiguous answer or other reason this question may need exclusion."><?php echo s($editorexclusionnote); ?></textarea></label>
        <input type="hidden" name="formaction" value="editquestion"><input type="hidden" name="questionid" value="<?php echo s($editquestion['questionId']); ?>"><input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>"><input type="hidden" name="returnusage" value="<?php echo s($usagefilter); ?>"><input type="hidden" name="returnpage" value="<?php echo (int)$page; ?>">
        <div class="question-bank__editor-actions"><?php if ($editorstatus === 'exclusion_candidate'): ?><button class="question-bank__button question-bank__button--primary" type="submit" name="savemode" value="restore">Restore as draft</button><?php else: ?><button class="question-bank__button question-bank__button--primary" type="submit" name="savemode" value="draft">Save draft</button><button class="question-bank__button question-bank__button--publish" type="submit" name="savemode" value="publish">Publish approved version</button><button class="question-bank__button question-bank__button--exclude" type="submit" name="savemode" value="exclude" onclick="return confirm('Move this question to Possible exclusions and remove it from active practice and exams?');">Move to possible exclusion</button><?php endif; ?></div>
    </form>
    <?php endif; ?>
    <section class="question-bank__summary" aria-label="Question bank totals"><article><span>Classified candidates</span><strong><?php echo (int)$totals['total']; ?></strong></article><article><span>Pending reclassification</span><strong><?php echo (int)$pendingreclassificationcount; ?></strong></article><article><span>Possible exclusions</span><strong><?php echo (int)$possibleexclusioncount; ?></strong></article><article><span>SCORM classified</span><strong><?php echo (int)$scormquestioncount; ?></strong></article><article><span>SCORM needing inference</span><strong><?php echo (int)$scormneedsinference; ?></strong></article><article><span>SCORM needing feedback</span><strong><?php echo (int)$scormneedsfeedback; ?></strong></article><article><span>SCORM simulation candidates</span><strong><?php echo (int)$scormsimulationcandidates; ?></strong></article><article><span>Ready for practice</span><strong><?php echo (int)$totals['ready']; ?></strong></article><article><span>Simulation Exam 1</span><strong><?php echo (int)$totals['exam1']; ?></strong></article><article><span>Ready reserve bank</span><strong><?php echo (int)$totals['reserve']; ?></strong></article></section>
    <form class="question-bank__filters" method="get" action="<?php echo $pageurl->out_omit_querystring(false); ?>">
        <label><span>Search ID or text</span><input type="search" name="q" value="<?php echo s($query); ?>" placeholder="Example: DY-001, Ma Huang Tang"></label>
        <label><span>Domain</span><select name="domain"><option value="all">All domains</option><?php foreach ($domains as $domainid => $domain): ?><option value="<?php echo s($domainid); ?>"<?php echo $domainfilter === $domainid ? ' selected' : ''; ?>>Domain <?php echo (int)$domain['number']; ?> · <?php echo s($domain['name']); ?></option><?php endforeach; ?></select></label>
        <label><span>Readiness</span><select name="readiness"><option value="all">All questions</option><option value="ready"<?php echo $readinessfilter === 'ready' ? ' selected' : ''; ?>>Ready for practice</option><option value="incomplete"<?php echo $readinessfilter === 'incomplete' ? ' selected' : ''; ?>>Needs completion</option></select></label>
        <label><span>Exam use</span><select name="usage"><option value="all">All uses</option><option value="exam1"<?php echo $usagefilter === 'exam1' ? ' selected' : ''; ?>>Simulation Exam 1</option><option value="exam2_review"<?php echo $usagefilter === 'exam2_review' ? ' selected' : ''; ?>>Simulation Exam 2 review set</option><option value="reserve"<?php echo $usagefilter === 'reserve' ? ' selected' : ''; ?>>Ready reserve bank</option></select></label>
        <label><span>Source</span><select name="source"><option value="all">All sources</option><option value="curated"<?php echo $sourcefilter === 'curated' ? ' selected' : ''; ?>>Curated bank</option><option value="scorm"<?php echo $sourcefilter === 'scorm' ? ' selected' : ''; ?>>SCORM modules</option></select></label>
        <label><span>Cognitive level</span><select name="cognitive"><option value="all">All cognitive levels</option><option value="recall"<?php echo $cognitivefilter === 'recall' ? ' selected' : ''; ?>>Recall / direct</option><option value="application"<?php echo $cognitivefilter === 'application' ? ' selected' : ''; ?>>Application</option><option value="inference"<?php echo $cognitivefilter === 'inference' ? ' selected' : ''; ?>>Inference</option><option value="analysis"<?php echo $cognitivefilter === 'analysis' ? ' selected' : ''; ?>>Analysis / differential</option></select></label>
        <label><span>Content focus</span><select name="content"><option value="all">All content</option><option value="herb_properties"<?php echo $contentfilter === 'herb_properties' ? ' selected' : ''; ?>>Herb properties</option><option value="formula_hierarchy"<?php echo $contentfilter === 'formula_hierarchy' ? ' selected' : ''; ?>>Formula hierarchy</option><option value="herb_processing"<?php echo $contentfilter === 'herb_processing' ? ' selected' : ''; ?>>Herb processing</option><option value="clinical_case"<?php echo $contentfilter === 'clinical_case' ? ' selected' : ''; ?>>Clinical cases</option><option value="other"<?php echo $contentfilter === 'other' ? ' selected' : ''; ?>>Other content</option></select></label>
        <label><span>Editorial review</span><select name="review"><option value="all">All review states</option><option value="needs_inference"<?php echo $reviewfilter === 'needs_inference' ? ' selected' : ''; ?>>Needs more inference</option><option value="needs_feedback"<?php echo $reviewfilter === 'needs_feedback' ? ' selected' : ''; ?>>Needs stronger feedback</option><option value="simulation_candidate"<?php echo $reviewfilter === 'simulation_candidate' ? ' selected' : ''; ?>>Simulation candidate</option><option value="reclassification_requested"<?php echo $reviewfilter === 'reclassification_requested' ? ' selected' : ''; ?>>Reclassification requested</option><option value="possible_exclusion"<?php echo $reviewfilter === 'possible_exclusion' ? ' selected' : ''; ?>>Possible exclusions (recycle bin)</option></select></label>
        <input type="hidden" name="lang" value="<?php echo s($displaylang); ?>">
        <div class="question-bank__filter-actions"><button class="question-bank__button" type="submit">Apply</button><a class="question-bank__button" href="<?php echo $clearurl->out(false); ?>">Clear</a></div>
    </form>
    <section class="question-bank__domains"><table><thead><tr><th>Domain</th><th>Classified</th><th>SCORM</th><th>Ready</th><th>Exam 1</th><th>Exam 2 review</th><th>Ready reserve</th></tr></thead><tbody><?php foreach ($domains as $domainid => $domain): ?><tr><td><strong>Domain <?php echo (int)$domain['number']; ?></strong> · <?php echo s($domain['name']); ?></td><td><?php echo (int)$domainstats[$domainid]['total']; ?></td><td><?php echo (int)$domainstats[$domainid]['scorm']; ?></td><td><?php echo (int)$domainstats[$domainid]['ready']; ?></td><td><?php echo (int)$domainstats[$domainid]['exam1']; ?></td><td><?php echo (int)$domainstats[$domainid]['exam2']; ?></td><td><?php echo (int)$domainstats[$domainid]['reserve']; ?></td></tr><?php endforeach; ?></tbody></table></section>
    <div class="question-bank__result-head"><span>Filtered questions</span><span><?php echo (int)$resultcount; ?> result<?php echo $resultcount === 1 ? '' : 's'; ?></span></div>
    <?php if ($pagequestions === []): ?><div class="question-bank__item">No questions match these filters.</div><?php else: ?><section class="question-bank__items"><?php foreach ($pagequestions as $question):
        $translation = $displaylang === 'es' ? ($question['translations']['es'] ?? []) : [];
        $prompt = trim((string)($translation['prompt'] ?? '')) ?: (string)($question['prompt'] ?? 'Question awaiting prompt');
        $options = count($translation['options'] ?? []) === 4 ? $translation['options'] : ($question['options'] ?? []);
        $explanation = trim((string)($translation['explanation'] ?? '')) ?: (string)($question['explanation'] ?? '');
        $correctlabel = (string)($question['correctLabel'] ?? '');
        $domain = $domains[$question['_domainid']];
        $contentlabel = ucwords(str_replace('_', ' ', (string)($question['contentType'] ?? 'other')));
        $cognitivelabel = ucwords((string)$question['_cognitive']);
        $sourcelabel = $question['_sourcekind'] === 'scorm' ? 'SCORM source' : 'Curated source';
        $revision = $question['_revision'];
        $revisionstatus = (string)($revision['status'] ?? '');
        $editurl = new moodle_url(
            '/blocks/myprogress/admin-herbology-question-bank.php',
            array_merge($baseparams, ['page' => $page, 'edit' => $question['questionId']])
        );
    ?><article class="question-bank__item<?php echo $question['_excluded'] ? ' question-bank__item--excluded' : ''; ?>">
        <?php if ($usagefilter === 'exam2_review' && $question['_exam2position'] > 0): ?><div class="question-bank__temporary-order"><span>Temporary review order</span><strong>Proposed Exam 2 Question <?php echo str_pad((string)$question['_exam2position'], 3, '0', STR_PAD_LEFT); ?></strong><small>of <?php echo (int)$exam2targetcount; ?></small></div><?php endif; ?>
        <div class="question-bank__meta">
            <span class="question-bank__id"><?php echo s($question['questionId'] ?? 'Unnumbered'); ?></span>
            <span class="question-bank__badge">Domain <?php echo (int)$domain['number']; ?> · <?php echo s($domain['name']); ?></span>
            <span class="question-bank__badge<?php echo $question['_sourcekind'] === 'scorm' ? ' question-bank__badge--scorm' : ''; ?>"><?php echo s($sourcelabel); ?></span>
            <span class="question-bank__badge">Content: <?php echo s($contentlabel); ?></span>
            <span class="question-bank__badge">Cognitive: <?php echo s($cognitivelabel); ?></span>
            <?php if ($question['_sourcekind'] === 'scorm' && $question['_needsinference']): ?><span class="question-bank__badge question-bank__badge--incomplete">Needs more inference</span><?php endif; ?>
            <?php if ($question['_sourcekind'] === 'scorm' && in_array($question['_feedbackquality'], ['missing', 'brief'], true)): ?><span class="question-bank__badge question-bank__badge--incomplete"><?php echo s(ucfirst($question['_feedbackquality'])); ?> feedback</span><?php endif; ?>
            <?php if ($question['_simulationcandidate']): ?><span class="question-bank__badge question-bank__badge--ready">Simulation candidate</span><?php endif; ?>
            <span class="question-bank__badge question-bank__badge--<?php echo $question['_ready'] ? 'ready' : 'incomplete'; ?>"><?php echo $question['_excluded'] ? 'Excluded from active use' : ($question['_ready'] ? 'Ready for practice' : 'Needs completion'); ?></span>
            <?php if ($question['_examposition'] > 0): ?><span class="question-bank__badge question-bank__badge--exam">Simulation Exam 1 · position <?php echo (int)$question['_examposition']; ?></span><?php else: ?><span class="question-bank__badge">Not used in Exam 1</span><?php endif; ?>
            <?php if ($question['_exam2position'] > 0): ?><span class="question-bank__badge question-bank__badge--exam">Simulation Exam 2 review · position <?php echo (int)$question['_exam2position']; ?></span><?php endif; ?>
            <span class="question-bank__badge">Complexity: <?php echo s(ucfirst((string)($question['difficultyBand'] ?? 'unclassified'))); ?></span>
            <?php if ($revisionstatus === 'pending_reclassification'): ?><span class="question-bank__badge question-bank__badge--incomplete">Reclassification requested · draft preview</span><?php elseif ($revisionstatus === 'exclusion_candidate'): ?><span class="question-bank__badge question-bank__badge--incomplete">Possible exclusion · recycle bin</span><?php elseif ($revisionstatus === 'draft'): ?><span class="question-bank__badge question-bank__badge--incomplete">Draft preview · not published</span><?php elseif ($revisionstatus === 'approved'): ?><span class="question-bank__badge question-bank__badge--ready">Approved revision</span><?php endif; ?>
            <a class="question-bank__button question-bank__edit-link" href="<?php echo $editurl->out(false); ?>#question-editor">Edit question</a>
        </div>
        <?php if ($revision !== []): ?><div class="question-bank__revision-note">Revision <?php echo (int)($revision['revisionNumber'] ?? 1); ?> · <?php echo s($revisionstatus === 'pending_reclassification' ? 'awaiting reclassification' : str_replace('_', ' ', $revisionstatus)); ?> · saved by <?php echo s($revision['savedByName'] ?? 'administrator'); ?><?php if (!empty($revision['savedAt'])): ?> on <?php echo s(userdate(strtotime($revision['savedAt']))); ?><?php endif; ?>.</div><?php endif; ?>
        <?php if ($question['_excluded']): ?><div class="question-bank__exclusion-note"><strong>Possible error / exclusion note:</strong> <?php echo s($revision['exclusionReason'] ?? 'No reason recorded.'); ?> This question is preserved but unavailable to students and exams.</div><?php endif; ?>
        <h3><?php echo s($prompt); ?></h3>
        <ol class="question-bank__options"><?php foreach ($options as $option): $optionlabel = (string)($option['label'] ?? ''); ?><li class="<?php echo $optionlabel === $correctlabel ? 'is-correct' : ''; ?>"><strong><?php echo s($optionlabel); ?>.</strong> <?php echo s($option['text'] ?? ''); ?></li><?php endforeach; ?></ol>
        <p><strong>Correct answer:</strong> <?php echo s($correctlabel !== '' ? $correctlabel : 'Not assigned'); ?></p>
        <div class="question-bank__explanation"><strong>Explanation:</strong> <?php echo s($explanation !== '' ? $explanation : 'Not completed'); ?></div>
        <?php if ($question['_sourcekind'] === 'scorm'): ?><div class="question-bank__explanation"><strong>Editorial recommendation:</strong> <?php echo s((string)($question['editorial']['feedbackRecommendation'] ?? 'Review the original question before exam use.')); ?><?php echo $question['_needsinference'] ? ' Convert a future variant into a multi-clue inference item while preserving this original.' : ''; ?></div><?php endif; ?>
        <p class="question-bank__source"><strong>Source:</strong> <?php echo s($question['source']['title'] ?? 'Not assigned'); ?><?php if (!empty($question['source']['locator'])): ?> · <?php echo s($question['source']['locator']); ?><?php endif; ?><?php
        $sourcecardurl = (string)($question['sourceCard'][$displaylang === 'es' ? 'urlEs' : 'url'] ?? '');
        $sourcecardlabel = (string)($question['sourceCard'][$displaylang === 'es' ? 'labelEs' : 'label'] ?? 'Open source card');
        if ($sourcecardurl !== ''):
        ?> · <a href="<?php echo s($sourcecardurl); ?>" target="_blank" rel="noopener"><?php echo s($sourcecardlabel); ?></a><?php endif; ?></p><?php if (!$question['_ready']): ?><div class="question-bank__missing">Missing for practice readiness: <?php echo s(implode(', ', $question['_missing'])); ?>.</div><?php endif; ?></article><?php endforeach; ?></section><?php endif; ?>
    <?php if ($totalpages > 1): ?><nav class="question-bank__pagination" aria-label="Question bank pages"><?php for ($pageindex = 0; $pageindex < $totalpages; $pageindex++): ?><a href="<?php echo (new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', array_merge($baseparams, ['page' => $pageindex])))->out(false); ?>"<?php echo $pageindex === $page ? ' aria-current="page"' : ''; ?>><?php echo $pageindex + 1; ?></a><?php endfor; ?></nav><?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
