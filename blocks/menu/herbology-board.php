<?php
// Chinese Herbology Board Level workspace.

require_once(__DIR__ . '/../../config.php');

require_login();

$lang = optional_param('lang', '', PARAM_ALPHA);
$ises = $lang === 'es' || ($lang === '' && current_language() === 'es');
$fromlibrary = optional_param('from', 'library', PARAM_ALPHA) !== 'dashboard';
$track = optional_param('track', 'herbs', PARAM_ALPHA);
if (!in_array($track, ['herbs', 'formulas', 'quiz', 'clinicalcases', 'domains', 'exams', 'simulation'], true)) {
    $track = 'herbs';
}
$sectionkey = optional_param('section', '', PARAM_ALPHANUMEXT);
$domainkey = optional_param('domain', '', PARAM_ALPHANUMEXT);
$clinicalexamkey = optional_param('caseexam', '', PARAM_ALPHANUMEXT);
$exammode = optional_param('exam', '', PARAM_ALPHA);
if (!in_array($exammode, ['linear', 'adaptive'], true)) {
    $exammode = optional_param('mix', 0, PARAM_BOOL) ? 'linear' : '';
}
if ($track === 'domains' && $exammode !== '') {
    // Keep previously shared exam URLs working while moving exams to their own section.
    $track = 'exams';
} else if (!in_array($track, ['exams', 'simulation'], true)) {
    $exammode = '';
}
if ($track !== 'domains') {
    $domainkey = '';
}
$mixdomains = $exammode !== '';
$domainpage = max(0, optional_param('page', 0, PARAM_INT));

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

$pageparams = ['track' => $track];
if ($fromlibrary) {
    $pageparams['from'] = 'library';
}
if ($ises) {
    $pageparams['lang'] = 'es';
}
if ($sectionkey !== '') {
    $pageparams['section'] = $sectionkey;
}
if ($domainkey !== '') {
    $pageparams['domain'] = $domainkey;
}
if ($mixdomains) {
    $pageparams['exam'] = $exammode;
}
if ($domainpage > 0) {
    $pageparams['page'] = $domainpage;
}
if ($clinicalexamkey !== '') {
    $pageparams['caseexam'] = $clinicalexamkey;
}
$pageurl = new moodle_url('/blocks/menu/herbology-board.php', $pageparams);
$languageparams = $pageparams;
if ($ises) {
    unset($languageparams['lang']);
} else {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/menu/herbology-board.php', $languageparams);
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', [
    'area' => 'herbology',
    'view' => 'library',
] + ($ises ? ['lang' => 'es'] : []));
$dashboardurl = $fromlibrary ? $libraryurl : new moodle_url('/my/');
$languagecontext = ($fromlibrary ? ['from' => 'library'] : []) + ($ises ? ['lang' => 'es'] : []);
$singleherbsurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'herbs'] + $languagecontext);
$formulasurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'formulas'] + $languagecontext);
$quizurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'quiz'] + $languagecontext);
$clinicalcasesurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'clinicalcases'] + $languagecontext);
$examsurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'exams'] + $languagecontext);
$simulationurl = new moodle_url('/blocks/menu/herbology-board.php', ['track' => 'simulation'] + $languagecontext);
$examsdashboardurl = $fromlibrary ? $libraryurl : new moodle_url('/my/', [], 'board-level-herbology');
$examhistoryurl = $examsurl->out(false) . '#herb-board-exam-history';
$examhistory = [];
if ($track === 'exams' && !$mixdomains) {
    $examhistorytable = new xmldb_table('block_menu_exam_attempts');
    if ($DB->get_manager()->table_exists($examhistorytable)) {
        $examhistory = array_values($DB->get_records(
            'block_menu_exam_attempts',
            ['userid' => $USER->id, 'courseid' => $membershipcourseid],
            'timecompleted DESC, id DESC',
            '*',
            0,
            10
        ));
    }
}

$domainblueprint = [
    [
        'number' => 1,
        'id' => 'formula_differentiation',
        'name' => 'Formula selection and differentiation',
        'name_es' => 'Selección y diferenciación de fórmulas',
        'weight' => 28,
        'focus' => 'Pattern-to-formula selection, close comparisons and the decisive clinical clue.',
        'focus_es' => 'Selección de la fórmula según el patrón, comparaciones cercanas y la pista clínica decisiva.',
    ],
    [
        'number' => 2,
        'id' => 'single_herb',
        'name' => 'Individual herbs',
        'name_es' => 'Hierbas individuales',
        'weight' => 24,
        'focus' => 'Category, actions, indications, nature, channels and clinical distinctions.',
        'focus_es' => 'Categoría, acciones, indicaciones, naturaleza, canales y diferencias clínicas.',
    ],
    [
        'number' => 3,
        'id' => 'dui_yao',
        'name' => 'Duì Yào and compatibility',
        'name_es' => 'Duì Yào y compatibilidad',
        'weight' => 18,
        'focus' => 'Combined action, each herb’s contribution and the pair’s traditional relationship.',
        'focus_es' => 'Acción combinada, contribución de cada hierba y relación tradicional del par.',
    ],
    [
        'number' => 4,
        'id' => 'safety_dispensing',
        'name' => 'Safety and dispensing',
        'name_es' => 'Seguridad y dispensación',
        'weight' => 15,
        'focus' => 'Dosage, preparation, toxicity, pregnancy, interactions and substitutions.',
        'focus_es' => 'Dosis, preparación, toxicidad, embarazo, interacciones y sustituciones.',
    ],
    [
        'number' => 5,
        'id' => 'formula_composition',
        'name' => 'Formula composition and modification',
        'name_es' => 'Composición y modificación de fórmulas',
        'weight' => 10,
        'focus' => 'Jun–Chen–Zuo–Shi, strategic ingredients and presentation-based modifications.',
        'focus_es' => 'Jun–Chen–Zuo–Shi, ingredientes estratégicos y modificaciones según la presentación.',
    ],
    [
        'number' => 6,
        'id' => 'scope_aliases',
        'name' => 'Roster boundaries, aliases and substitutions',
        'name_es' => 'Límites del listado, alias y sustituciones',
        'weight' => 5,
        'focus' => 'Name variants, official-list boundaries and obsolete or historical alternatives.',
        'focus_es' => 'Variantes de nombres, límites del listado oficial y alternativas obsoletas o históricas.',
    ],
];

$clinicalcasesbankpath = $CFG->dataroot . '/board-domain-banks/herbology-clinical-cases.json';
$clinicalcasesbank = ['exams' => []];
if (in_array($track, ['quiz', 'clinicalcases'], true) && is_readable($clinicalcasesbankpath)) {
    $decodedclinicalcases = json_decode(file_get_contents($clinicalcasesbankpath), true);
    if (is_array($decodedclinicalcases) && isset($decodedclinicalcases['exams'])
            && is_array($decodedclinicalcases['exams'])) {
        $clinicalcasesbank = $decodedclinicalcases;
    }
}
$clinicalcaseexams = array_values(array_filter(
    $clinicalcasesbank['exams'] ?? [],
    static fn($exam): bool => is_array($exam)
        && !empty($exam['key'])
        && !empty($exam['title'])
        && !empty($exam['questions'])
        && is_array($exam['questions'])
));
$selectedclinicalexam = null;
$clinicalquestions = [];
if ($track === 'clinicalcases') {
    foreach ($clinicalcaseexams as $clinicalcaseexam) {
        if ((string)$clinicalcaseexam['key'] === $clinicalexamkey) {
            $selectedclinicalexam = $clinicalcaseexam;
            break;
        }
    }
    if ($selectedclinicalexam === null && $clinicalcaseexams) {
        $selectedclinicalexam = $clinicalcaseexams[0];
        $clinicalexamkey = (string)$selectedclinicalexam['key'];
    }
    if ($selectedclinicalexam !== null) {
        $clinicalquestions = array_values(array_filter(
            $selectedclinicalexam['questions'],
            static fn($question): bool => is_array($question)
                && !empty($question['prompt'])
                && !empty($question['correctLabel'])
                && !empty($question['options'])
                && is_array($question['options'])
        ));
    }
}
if ($ises) {
    foreach ($domainblueprint as &$domaintranslation) {
        $domaintranslation['name'] = $domaintranslation['name_es'];
        $domaintranslation['focus'] = $domaintranslation['focus_es'];
    }
    unset($domaintranslation);
}
$domainseedpath = __DIR__ . '/herbology-domain-bank.json';
$domainbankdirectory = $CFG->dataroot . '/board-domain-banks';
$usesdomainbank = in_array($track, ['domains', 'exams', 'simulation'], true);
if ($usesdomainbank) {
    make_writable_directory($domainbankdirectory);
}
$domainbankpath = $domainbankdirectory . '/herbology-domain-bank.json';
if ($usesdomainbank && !is_readable($domainbankpath) && is_readable($domainseedpath)) {
    $seeddata = file_get_contents($domainseedpath);
    if ($seeddata === false || file_put_contents($domainbankpath, $seeddata, LOCK_EX) === false) {
        throw new RuntimeException('Unable to initialize the Herbology Domain question bank.');
    }
}
$domainbank = ['domains' => []];
if ($usesdomainbank && is_readable($domainbankpath)) {
    $decodedbank = json_decode(file_get_contents($domainbankpath), true);
    if (is_array($decodedbank) && isset($decodedbank['domains']) && is_array($decodedbank['domains'])) {
        $domainbank = $decodedbank;
    }
}
$savedomainbank = static function(array $bank) use ($domainbankpath): void {
    $encodedbank = json_encode(
        $bank,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
    if (file_put_contents($domainbankpath, $encodedbank . PHP_EOL, LOCK_EX) === false) {
        throw new RuntimeException('Unable to save the Herbology Domain question bank.');
    }
};
$domaincandidatepath = __DIR__ . '/herbology-domain-candidates.json';
$domaincandidateinventory = [];
if ($usesdomainbank && is_readable($domaincandidatepath)) {
    $domaincandidates = json_decode(file_get_contents($domaincandidatepath), true);
    if (is_array($domaincandidates) && isset($domaincandidates['domains'])) {
        $domaincandidateinventory = $domaincandidates['sourceInventory'] ?? [];
        $candidateversion = (string)($domaincandidates['classificationVersion'] ?? '');
        $importedversion = (string)($domainbank['imports']['classifiedContentVersion'] ?? '');
        if ($candidateversion !== '' && $candidateversion !== $importedversion) {
            // Remove the earlier English-name-to-pinyin roster items. They tested
            // translation recall rather than a Board-level clinical or safety decision.
            if (isset($domainbank['domains']['scope_aliases'])) {
                $domainbank['domains']['scope_aliases'] = array_values(array_filter(
                    $domainbank['domains']['scope_aliases'],
                    static fn(array $question): bool => preg_match(
                        '/^RA-\d{3}$/',
                        (string)($question['questionId'] ?? '')
                    ) !== 1
                ));
            }
            $currentcandidateids = [];
            foreach ($domaincandidates['domains'] as $candidatequestions) {
                foreach ($candidatequestions as $candidatequestion) {
                    if (!empty($candidatequestion['questionId'])) {
                        $currentcandidateids[(string)$candidatequestion['questionId']] = true;
                    }
                }
            }
            foreach ($domainbank['domains'] as &$existingquestions) {
                $existingquestions = array_values(array_filter(
                    $existingquestions,
                    static function(array $question) use ($currentcandidateids): bool {
                        $questionid = (string)($question['questionId'] ?? '');
                        $isgenerated = preg_match('/^(?:CF|FD|SH|DY|SD|FM|RA-SAFE)-/', $questionid) === 1;
                        return !$isgenerated || isset($currentcandidateids[$questionid]);
                    }
                ));
            }
            unset($existingquestions);
            $existinglocations = [];
            foreach ($domainbank['domains'] as $existingdomainid => $existingquestions) {
                foreach ($existingquestions as $existingindex => $existingquestion) {
                    if (!empty($existingquestion['questionId'])) {
                        $existinglocations[$existingquestion['questionId']] = [$existingdomainid, $existingindex];
                    }
                }
            }
            foreach ($domaincandidates['domains'] as $candidateid => $candidatequestions) {
                if (!isset($domainbank['domains'][$candidateid]) || !is_array($domainbank['domains'][$candidateid])) {
                    $domainbank['domains'][$candidateid] = [];
                }
                foreach ($candidatequestions as $candidatequestion) {
                    $candidatequestionid = $candidatequestion['questionId'] ?? '';
                    if ($candidatequestionid === '') {
                        continue;
                    }
                    if (isset($existinglocations[$candidatequestionid])) {
                        [$existingdomainid, $existingindex] = $existinglocations[$candidatequestionid];
                        $domainbank['domains'][$existingdomainid][$existingindex] = $candidatequestion;
                    } else {
                        $domainbank['domains'][$candidateid][] = $candidatequestion;
                        $existinglocations[$candidatequestionid] = [
                            $candidateid,
                            count($domainbank['domains'][$candidateid]) - 1,
                        ];
                    }
                }
            }
            $domainbank['imports']['classifiedContentVersion'] = $candidateversion;
            $domainbank['imports']['classifiedContentImportedAt'] = gmdate(DATE_ATOM);
            $domainbank['classificationSummary'] = $domaincandidates['summary'] ?? [];
            $domainbank['sourceInventory'] = $domaincandidateinventory;
            $savedomainbank($domainbank);
        }
    }
}
$domaincandidateinventory = $domainbank['sourceInventory'] ?? $domaincandidateinventory;
$canmanagedomains = is_siteadmin($USER)
    || has_capability('moodle/course:manageactivities', $membershipcontext, $USER->id);
$questionisready = static function(array $question): bool {
    return !empty($question['prompt'])
        && count($question['options'] ?? []) === 4
        && !empty($question['correctLabel'])
        && !empty($question['explanation'])
        && !empty($question['source']['title'])
        && !empty($question['source']['locator'])
        && !empty($question['sourceCard']['url'])
        && !empty($question['sourceCard']['urlEs']);
};
$selecteddomain = null;
foreach ($domainblueprint as &$domain) {
    $domain['questions'] = $domainbank['domains'][$domain['id']] ?? [];
    if ($ises) {
        $domain['questions'] = array_values(array_filter(
            $domain['questions'],
            static function(array $question): bool {
                $translation = $question['translations']['es'] ?? null;
                return is_array($translation)
                    && !empty($translation['prompt'])
                    && count($translation['options'] ?? []) === 4
                    && !empty($translation['explanation']);
            }
        ));
        foreach ($domain['questions'] as &$translatedquestion) {
            $translation = $translatedquestion['translations']['es'] ?? null;
            if (!is_array($translation)) {
                continue;
            }
            $translatedquestion['prompt'] = $translation['prompt'] ?? $translatedquestion['prompt'];
            $translatedquestion['options'] = $translation['options'] ?? $translatedquestion['options'];
            $translatedquestion['explanation'] = $translation['explanation'] ?? $translatedquestion['explanation'];
        }
        unset($translatedquestion);
    }
    $domain['questions'] = array_values(array_filter($domain['questions'], $questionisready));
    $domain['questionCount'] = count($domain['questions']);
    $domain['sourceLabel'] = (string)($domaincandidateinventory[$domain['id']]['authoringSourceLabel'] ?? '');
    if ($domainkey !== '' && $domain['id'] === $domainkey) {
        $selecteddomain = $domain;
    }
}
unset($domain);
if ($track === 'domains' && $domainkey !== '' && $selecteddomain === null) {
    redirect($quizurl, 'The requested Domain does not exist.', null, \core\output\notification::NOTIFY_ERROR);
}
$domainperpage = 25;
$selecteddomainpagequestions = [];
$selecteddomainstart = 0;
$selecteddomainend = 0;
$selecteddomaintotalpages = 0;
$domainpageurls = [];
if ($selecteddomain !== null) {
    $selecteddomaintotalpages = max(1, (int)ceil($selecteddomain['questionCount'] / $domainperpage));
    $domainpage = min($domainpage, $selecteddomaintotalpages - 1);
    $selecteddomainpagequestions = array_slice(
        $selecteddomain['questions'],
        $domainpage * $domainperpage,
        $domainperpage
    );
    if ($selecteddomain['questionCount'] > 0) {
        $selecteddomainstart = ($domainpage * $domainperpage) + 1;
        $selecteddomainend = min($selecteddomainstart + $domainperpage - 1, $selecteddomain['questionCount']);
    }
    for ($pageindex = 0; $pageindex < $selecteddomaintotalpages; $pageindex++) {
        $domainpageurls[$pageindex] = new moodle_url('/blocks/menu/herbology-board.php', [
            'track' => 'domains',
            'domain' => $selecteddomain['id'],
            'page' => $pageindex,
        ] + $languagecontext);
    }
}
$mixedquestions = [];
$domainshortages = [];
if (in_array($track, ['exams', 'simulation'], true) && $mixdomains) {
    foreach ($domainblueprint as $domain) {
        $templatequestions = array_values(array_filter(
            $domain['questions'],
            static fn(array $question): bool => !empty($question['examTemplate']['globalPosition'])
        ));
        if (count($templatequestions) !== $domain['weight']) {
            $domainshortages[] = [
                'name' => $domain['name'],
                'required' => $domain['weight'],
                'available' => count($templatequestions),
                'missing' => max(0, $domain['weight'] - count($templatequestions)),
            ];
            continue;
        }
        $mixedquestions = array_merge($mixedquestions, $templatequestions);
    }
    if (!$domainshortages) {
        usort($mixedquestions, static fn(array $left, array $right): int =>
            ((int)($left['examTemplate']['globalPosition'] ?? PHP_INT_MAX))
            <=> ((int)($right['examTemplate']['globalPosition'] ?? PHP_INT_MAX))
        );
    }
}

if ($track === 'exams' && $mixdomains && count($mixedquestions) === 100) {
    $PAGE->requires->js_init_code(<<<'JS'
const exam = document.getElementById('herbology-domain-exam');
if (exam) {
    const questions = [...exam.querySelectorAll('.herb-board__exam-question')];
    const previousButton = exam.querySelector('.herb-board__exam-previous');
    const nextButton = exam.querySelector('.herb-board__exam-next');
    const restartButton = exam.querySelector('.herb-board__exam-restart');
    const navigation = exam.querySelector('.herb-board__exam-navigation');
    const result = document.getElementById('herbology-domain-exam-result');
    const saveStatus = exam.querySelector('.herb-board__exam-save-status');
    const historyLink = exam.querySelector('.herb-board__exam-history-link');
    const progressText = exam.querySelector('.herb-board__exam-progress-text');
    const scoreText = exam.querySelector('.herb-board__exam-score');
    const progressBar = exam.querySelector('.herb-board__exam-progress-bar span');
    const sourceViewer = document.querySelector('[data-exam-source-viewer]');
    const sourceFrame = sourceViewer?.querySelector('[data-exam-source-frame]');
    const sourceTitle = sourceViewer?.querySelector('[data-exam-source-title]');
    const sourceReturn = sourceViewer?.querySelector('[data-exam-source-return]');
    const mode = exam.dataset.examMode || 'linear';
    const difficultyRank = {foundation: 0, application: 1, analysis: 2};
    let remaining = [...questions];
    const history = [];
    let historyIndex = -1;
    let currentQuestion = null;
    let reviewingPending = false;
    let answered = 0;
    let score = 0;
    let activeSourceLink = null;
    let attemptUid = '';
    let attemptStartedAt = Date.now();
    let attemptSaving = false;
    let attemptSaved = false;

    previousButton.textContent = exam.dataset.previousWord;

    const createAttemptUid = () => {
        if (globalThis.crypto && typeof globalThis.crypto.randomUUID === 'function') {
            return globalThis.crypto.randomUUID();
        }
        return `attempt-${Date.now()}-${Math.random().toString(36).slice(2)}`;
    };
    attemptUid = createAttemptUid();

    const closeSourceViewer = () => {
        if (!sourceViewer || sourceViewer.hidden) {
            return;
        }
        sourceViewer.hidden = true;
        document.body.classList.remove('herb-board-source-open');
        if (sourceFrame) {
            sourceFrame.src = 'about:blank';
        }
        activeSourceLink?.focus();
        activeSourceLink = null;
    };

    if (sourceViewer && sourceFrame && sourceTitle && sourceReturn) {
        sourceViewer.querySelectorAll('[data-exam-source-close]').forEach(control => {
            control.addEventListener('click', closeSourceViewer);
        });
        sourceFrame.addEventListener('load', () => {
            const query = activeSourceLink?.dataset.sourceQuery || '';
            if (!query || sourceFrame.src === 'about:blank') {
                return;
            }
            try {
                const sourceDocument = sourceFrame.contentDocument;
                const search = sourceDocument?.querySelector('#q');
                if (search) {
                    search.value = query;
                    search.dispatchEvent(new Event('input', {bubbles: true}));
                    const visibleCard = [...sourceDocument.querySelectorAll('#roster li')]
                        .find(card => !card.hidden);
                    visibleCard?.scrollIntoView({block: 'center'});
                }
            } catch (error) {
                // Same-origin cards are enhanced when available; the source remains usable without it.
            }
        });
        exam.querySelectorAll('[data-exam-source-link]').forEach(link => {
            link.addEventListener('click', event => {
                event.preventDefault();
                activeSourceLink = link;
                sourceTitle.textContent = link.dataset.sourceTitle || link.textContent.trim();
                sourceReturn.textContent = `${sourceViewer.dataset.returnLabel} ${link.dataset.questionLabel}`;
                sourceFrame.src = link.href;
                sourceViewer.hidden = false;
                document.body.classList.add('herb-board-source-open');
                sourceReturn.focus();
            });
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !sourceViewer.hidden) {
                closeSourceViewer();
            }
        });
    }

    questions.forEach(question => {
        question.hidden = true;
        question.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', () => answerQuestion(question, input));
        });
    });

    const takeNextQuestion = () => {
        if (mode !== 'adaptive' || currentQuestion === null || currentQuestion.dataset.answered !== '1') {
            return remaining.shift();
        }
        const lastCorrect = currentQuestion.dataset.wasCorrect === '1';
        const currentRank = difficultyRank[currentQuestion.dataset.difficulty] ?? 1;
        const targetRank = lastCorrect ? Math.min(2, currentRank + 1) : Math.max(0, currentRank - 1);
        let nextIndex = remaining.findIndex(question =>
            !lastCorrect
            && question.dataset.domain === currentQuestion.dataset.domain
            && (difficultyRank[question.dataset.difficulty] ?? 1) === targetRank
        );
        if (nextIndex < 0) {
            nextIndex = remaining.findIndex(question =>
                (difficultyRank[question.dataset.difficulty] ?? 1) === targetRank
            );
        }
        if (nextIndex < 0 && !lastCorrect) {
            nextIndex = remaining.findIndex(question => question.dataset.domain === currentQuestion.dataset.domain);
        }
        if (nextIndex < 0) {
            nextIndex = 0;
        }
        return remaining.splice(nextIndex, 1)[0];
    };

    const getPendingQuestions = () => questions
        .filter(question => question.dataset.answered !== '1')
        .sort((left, right) => Number(left.dataset.displayNumber) - Number(right.dataset.displayNumber));

    const updateNavigation = () => {
        const pendingQuestions = getPendingQuestions();
        if (reviewingPending) {
            previousButton.disabled = pendingQuestions.length < 2;
            nextButton.textContent = pendingQuestions.length > 0
                ? exam.dataset.nextPendingWord
                : exam.dataset.resultsWord;
            return;
        }
        previousButton.disabled = historyIndex <= 0;
        const hasForwardHistory = historyIndex < history.length - 1;
        const hasNewQuestion = remaining.length > 0;
        if (hasForwardHistory || hasNewQuestion) {
            nextButton.textContent = exam.dataset.nextWord;
        } else if (pendingQuestions.length > 0) {
            nextButton.textContent = exam.dataset.pendingWord;
        } else {
            nextButton.textContent = exam.dataset.resultsWord;
        }
    };

    const updateScore = () => {
        const percentage = answered > 0 ? Math.round((score / answered) * 100) : 0;
        scoreText.textContent = `${exam.dataset.scoreWord}: ${percentage}% (${score}/${answered})`;
    };

    const saveAttempt = async () => {
        if (attemptSaving || attemptSaved) {
            return;
        }
        attemptSaving = true;
        const savingUid = attemptUid;
        saveStatus.textContent = exam.dataset.historySaving;
        saveStatus.classList.remove('is-saved', 'is-error');
        saveStatus.hidden = false;
        const body = new URLSearchParams({
            sesskey: exam.dataset.sesskey,
            attemptuid: savingUid,
            examtype: mode,
            score: String(score),
            total: String(questions.length),
            duration: String(Math.max(0, Math.round((Date.now() - attemptStartedAt) / 1000))),
            language: exam.dataset.examLanguage,
        });
        try {
            const response = await fetch(exam.dataset.historyUrl, {
                method: 'POST',
                credentials: 'same-origin',
                keepalive: true,
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                body,
            });
            const payload = await response.json();
            if (!response.ok || !payload.success) {
                throw new Error('Exam history save failed.');
            }
            if (savingUid === attemptUid) {
                attemptSaved = true;
                saveStatus.textContent = exam.dataset.historySaved;
                saveStatus.classList.add('is-saved');
                historyLink.hidden = false;
            }
        } catch (error) {
            if (savingUid === attemptUid) {
                saveStatus.textContent = exam.dataset.historyError;
                saveStatus.classList.add('is-error');
            }
        } finally {
            if (savingUid === attemptUid) {
                attemptSaving = false;
            }
        }
    };

    const showQuestion = (question, shouldScroll = true) => {
        questions.forEach(item => item.hidden = item !== question);
        currentQuestion = question;
        const number = Number(question.dataset.displayNumber);
        question.querySelector('.herb-board__exam-question-number').textContent = `${exam.dataset.questionWord} ${number}`;
        if (reviewingPending) {
            const pendingQuestions = getPendingQuestions();
            const pendingPosition = pendingQuestions.indexOf(question) + 1;
            progressText.textContent = `${exam.dataset.pendingLabel} ${pendingPosition} ${exam.dataset.scoreConnector} ${pendingQuestions.length}`
                + ` · ${exam.dataset.questionWord} ${number} ${exam.dataset.scoreConnector} ${questions.length}`;
            progressBar.style.width = `${(answered / questions.length) * 100}%`;
        } else {
            progressText.textContent = `${exam.dataset.questionWord} ${number} ${exam.dataset.scoreConnector} ${questions.length}`;
            progressBar.style.width = `${(number / questions.length) * 100}%`;
        }
        updateNavigation();
        if (shouldScroll) {
            question.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
    };

    const addNextQuestion = () => {
        const question = takeNextQuestion();
        if (!question) {
            return false;
        }
        history.push(question);
        historyIndex = history.length - 1;
        question.dataset.displayNumber = String(history.length);
        showQuestion(question);
        return true;
    };

    const answerQuestion = (question, selected) => {
        if (question.dataset.answered === '1') {
            return;
        }
        question.dataset.answered = '1';
        const lastCorrect = selected.value === question.dataset.correct;
        question.dataset.wasCorrect = lastCorrect ? '1' : '0';
        if (lastCorrect) {
            score++;
            question.classList.add('is-correct');
        } else {
            question.classList.add('is-incorrect');
        }
        answered++;
        question.querySelectorAll('input[type="radio"]').forEach(input => input.disabled = true);
        const feedback = question.querySelector('.herb-board__exam-feedback');
        const verdict = feedback.querySelector('.herb-board__exam-verdict');
        verdict.textContent = lastCorrect ? exam.dataset.correctFeedback : exam.dataset.incorrectFeedback;
        feedback.hidden = false;
        if (reviewingPending) {
            progressBar.style.width = `${(answered / questions.length) * 100}%`;
        }
        updateScore();
        updateNavigation();
    };

    const finishExam = () => {
        const connector = exam.dataset.scoreConnector || 'of';
        const correctWord = exam.dataset.correctWord || 'correct';
        const percentage = Math.round((score / questions.length) * 100);
        progressText.textContent = exam.dataset.completedWord;
        progressBar.style.width = '100%';
        result.textContent = `${score} ${connector} ${questions.length} ${correctWord} · ${percentage}%`;
        result.hidden = false;
        navigation.hidden = true;
        restartButton.hidden = false;
        void saveAttempt();
        result.scrollIntoView({behavior: 'smooth', block: 'center'});
    };

    const restartExam = () => {
        questions.forEach(question => {
            question.hidden = true;
            question.classList.remove('is-correct', 'is-incorrect');
            delete question.dataset.answered;
            delete question.dataset.wasCorrect;
            delete question.dataset.displayNumber;
            question.querySelectorAll('input[type="radio"]').forEach(input => {
                input.checked = false;
                input.disabled = false;
            });
            question.querySelector('.herb-board__exam-feedback').hidden = true;
        });
        remaining = [...questions];
        history.length = 0;
        historyIndex = -1;
        currentQuestion = null;
        reviewingPending = false;
        answered = 0;
        score = 0;
        attemptUid = createAttemptUid();
        attemptStartedAt = Date.now();
        attemptSaving = false;
        attemptSaved = false;
        result.textContent = '';
        result.hidden = true;
        saveStatus.textContent = '';
        saveStatus.classList.remove('is-saved', 'is-error');
        saveStatus.hidden = true;
        historyLink.hidden = true;
        restartButton.hidden = true;
        navigation.hidden = false;
        updateScore();
        addNextQuestion();
    };

    const showPreviousQuestion = () => {
        if (reviewingPending) {
            const pendingQuestions = getPendingQuestions();
            const currentNumber = Number(currentQuestion?.dataset.displayNumber || 0);
            const previousPending = [...pendingQuestions]
                .reverse()
                .find(question => Number(question.dataset.displayNumber) < currentNumber)
                || pendingQuestions[pendingQuestions.length - 1];
            if (previousPending && previousPending !== currentQuestion) {
                historyIndex = history.indexOf(previousPending);
                showQuestion(previousPending);
            }
            return;
        }
        if (historyIndex <= 0) {
            return;
        }
        historyIndex--;
        showQuestion(history[historyIndex]);
    };

    const showFollowingQuestion = () => {
        if (reviewingPending) {
            const pendingQuestions = getPendingQuestions();
            if (pendingQuestions.length === 0) {
                finishExam();
                return;
            }
            const currentNumber = Number(currentQuestion?.dataset.displayNumber || 0);
            const nextPending = pendingQuestions
                .find(question => Number(question.dataset.displayNumber) > currentNumber)
                || pendingQuestions[0];
            historyIndex = history.indexOf(nextPending);
            showQuestion(nextPending);
            return;
        }
        if (historyIndex < history.length - 1) {
            historyIndex++;
            showQuestion(history[historyIndex]);
            return;
        }
        if (addNextQuestion()) {
            return;
        }
        const firstUnanswered = history.findIndex(question => question.dataset.answered !== '1');
        if (firstUnanswered >= 0) {
            reviewingPending = true;
            historyIndex = firstUnanswered;
            showQuestion(history[historyIndex]);
        } else {
            finishExam();
        }
    };

    previousButton.addEventListener('click', showPreviousQuestion);
    nextButton.addEventListener('click', showFollowingQuestion);
    restartButton.addEventListener('click', restartExam);
    exam.addEventListener('submit', event => {
        event.preventDefault();
        showFollowingQuestion();
    });

    updateScore();
    addNextQuestion();
}
JS);
}

if ($track === 'simulation' && $mixdomains && count($mixedquestions) === 100) {
    $PAGE->requires->js(new moodle_url('/blocks/menu/herbology-simulation.js', ['v' => '2026110801']));
}

if ($track === 'clinicalcases' && $selectedclinicalexam !== null) {
    $PAGE->requires->js(new moodle_url('/blocks/menu/herbology-clinical-cases.js', ['v' => '2026110801']));
}

$PAGE->set_url($pageurl);
$PAGE->set_context($membershipcontext);
$PAGE->set_pagelayout('standard');
$pagetitle = in_array($track, ['exams', 'simulation'], true)
    ? ($track === 'simulation'
        ? ($ises ? 'Simulación de Herbología' : 'Herbology Simulation')
        : ($ises ? 'Exámenes de Herbología' : 'Herbology Exams'))
    : (in_array($track, ['quiz', 'clinicalcases', 'domains'], true)
        ? ($ises ? 'Quiz de Herbología' : 'Herbology Quiz')
        : ($ises ? 'Práctica de Herbología' : 'Herbology Practice'));
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$topics = [];
$clearheatsections = [
    'drain-fire' => [
        'number' => '2.1',
        'name' => 'Clear Heat and Drain Fire',
        'focus' => 'Qi-level excess heat, especially in the Lung and Stomach.',
        'distinction' => 'Choose by site and severity: Shi Gao is strongest for blazing Lung/Stomach heat; Zhi Mu adds yin nourishment and moistening; Lu Gen is milder and generates fluids.',
        'coreherbs' => [
            ['name' => 'Shí Gāo', 'pharmaceutical' => 'Gypsum fibrosum', 'use' => 'Strongly clears qi-level heat from the Lung and Stomach.'],
            ['name' => 'Zhī Mǔ', 'pharmaceutical' => 'Anemarrhenae Rhizoma', 'use' => 'Drains fire while nourishing yin and moistening dryness.'],
            ['name' => 'Lú Gēn', 'pharmaceutical' => 'Phragmitis Rhizoma', 'use' => 'Clears Lung/Stomach heat and generates fluids.'],
            ['name' => 'Zhī Zǐ', 'pharmaceutical' => 'Gardeniae Fructus', 'use' => 'Drains fire and also addresses damp-heat.'],
            ['name' => 'Dàn Zhú Yè', 'pharmaceutical' => 'Lophatheri Herba', 'use' => 'Clears Heart/Stomach heat and promotes urination.'],
        ],
        'warnings' => [
            'These cold substances can aggravate Spleen/Stomach cold or deficiency; pattern differentiation remains essential.',
            'High fever, delirium, coma or convulsions require urgent biomedical assessment and are not self-treatment indications.',
        ],
        'annex' => [
            'comparison' => 'Shi Gao emphasizes forceful heat clearing, Zhi Mu combines cooling with yin support, and Lu Gen emphasizes fluids and Lung involvement. Gardeniae Fructus is milder and additionally drains damp-heat; Lophatheri Herba emphasizes Heart and Stomach fire.',
            'preparation' => 'Mineral substances and aromatic or fresh materials may require different preparation methods. Verify preparation and dosage in the individual monograph and local pharmacopeia.',
            'nomenclature' => 'Names are presented as pinyin plus pharmaceutical name to reduce ambiguity across regional common names.',
            'supplemental' => [
                'Hán Shuǐ Shí, Lián Zǐ Xīn, Xī Guā and related summerheat materials remain supplemental within the present five-part structure.',
                'Eye-focused materials in the source summary include Jué Míng Zǐ, Gǔ Jīng Cǎo, Mì Méng Huā, Qīng Xiāng Zǐ, Xià Kū Cǎo and Yè Míng Shā.',
            ],
        ],
    ],
    'cool-blood' => [
        'number' => '2.2',
        'name' => 'Clear Heat and Cool the Blood',
        'focus' => 'Nutritive- or blood-level heat, bleeding and heat injury to yin.',
        'distinction' => 'Shui Niu Jiao leads for intense blood-level heat; Sheng Di Huang and Xuan Shen cool while nourishing yin; Mu Dan Pi cools and moves blood; Zi Cao vents rashes.',
        'coreherbs' => [
            ['name' => 'Shuǐ Niú Jiǎo', 'pharmaceutical' => 'Bubali Cornu', 'use' => 'Clears intense nutritive- and blood-level heat.'],
            ['name' => 'Shēng Dì Huáng', 'pharmaceutical' => 'Rehmanniae Radix', 'use' => 'Cools blood, nourishes yin and generates fluids.'],
            ['name' => 'Xuán Shēn', 'pharmaceutical' => 'Scrophulariae Radix', 'use' => 'Cools blood, nourishes yin and resolves fire toxicity.'],
            ['name' => 'Mǔ Dān Pí', 'pharmaceutical' => 'Moutan Cortex', 'use' => 'Cools and gently moves the blood without causing stasis.'],
            ['name' => 'Zǐ Cǎo', 'pharmaceutical' => 'Arnebiae/Lithospermi Radix', 'use' => 'Cools and moves blood and helps vent rashes.'],
        ],
        'warnings' => [
            'Active bleeding, altered consciousness or very high fever requires urgent biomedical evaluation.',
            'Cold, rich blood-cooling herbs may be inappropriate with digestive cold/deficiency or without a true heat pattern.',
        ],
        'annex' => [
            'comparison' => 'Rehmanniae Radix is emphasized for reckless movement of hot blood, while Scrophulariae Radix is preferred when heat toxin affects the throat or produces nodules. Moutan Cortex adds dispersing action; Zi Cao is distinguished by rash venting.',
            'preparation' => 'Shui Niu Jiao is a legal substitute for endangered rhinoceros horn and is commonly prepared separately or longer than ordinary botanicals. Confirm the current pharmacopeial method.',
            'nomenclature' => 'Use Bubali Cornu rather than obsolete endangered-animal terminology. Zi Cao may refer to more than one accepted botanical source, so supplier identity matters.',
            'supplemental' => ['Bensky expands comparison by depth of heat, bleeding, yin injury and whether blood stasis or rash is present.'],
        ],
    ],
    'dry-dampness' => [
        'number' => '2.3',
        'name' => 'Clear Heat and Dry Dampness',
        'focus' => 'Damp-heat or fire toxicity differentiated by burner and organ system.',
        'distinction' => 'Huang Qin favors the upper burner, Huang Lian the middle and Heart, and Huang Bai the lower burner; Long Dan Cao targets Liver/Gallbladder damp-heat.',
        'coreherbs' => [
            ['name' => 'Huáng Qín', 'pharmaceutical' => 'Scutellariae Radix', 'use' => 'Clears upper-burner heat and dries dampness.'],
            ['name' => 'Huáng Lián', 'pharmaceutical' => 'Coptidis Rhizoma', 'use' => 'Strongly clears Heart and middle-burner fire.'],
            ['name' => 'Huáng Bǎi', 'pharmaceutical' => 'Phellodendri Cortex', 'use' => 'Clears lower-burner damp-heat and deficiency fire.'],
            ['name' => 'Lóng Dǎn Cǎo', 'pharmaceutical' => 'Gentianae Radix', 'use' => 'Drains Liver/Gallbladder fire and damp-heat.'],
            ['name' => 'Kǔ Shēn', 'pharmaceutical' => 'Sophorae flavescentis Radix', 'use' => 'Clears lower-burner damp-heat and relieves itching.'],
            ['name' => 'Qín Pí', 'pharmaceutical' => 'Fraxini Cortex', 'use' => 'Clears damp-heat, especially in the Large Intestine and eyes.'],
        ],
        'warnings' => [
            'These strongly bitter-cold herbs can damage Spleen/Stomach qi and yin when used excessively or in the wrong pattern.',
            'Persistent jaundice, bloody dysentery or painful urinary symptoms require biomedical assessment rather than self-treatment.',
        ],
        'annex' => [
            'comparison' => 'The source organizes the three Huang herbs by burner: Scutellariae Radix for the upper, Coptidis Rhizoma for the middle, and Phellodendri Cortex for the lower. Gentianae Radix narrows the focus to Liver/Gallbladder; Ku Shen emphasizes the lower burner and genitals; Qin Pi adds Large Intestine and eye indications.',
            'preparation' => 'Processing can redirect or moderate bitter-cold actions. Use the exact processed form specified by a formula or individual monograph.',
            'nomenclature' => 'The pharmaceutical name is especially important for distinguishing similarly translated “yellow” herbs and regional substitutes.',
            'supplemental' => ['The source also includes historical comparisons for dysenteric disorders, jaundice, urinary dysfunction and hot skin lesions.'],
        ],
    ],
    'resolve-toxicity' => [
        'number' => '2.4',
        'name' => 'Clear Heat and Resolve Toxicity',
        'focus' => 'Heat toxin with sores, abscesses, throat swelling or dysenteric presentations.',
        'distinction' => 'Jin Yin Hua vents and disperses; Lian Qiao reduces clumps; Pu Gong Ying favors firm sores and urination; Zi Hua Di Ding favors deep-rooted sores; Ye Ju Hua adds throat and eye use.',
        'coreherbs' => [
            ['name' => 'Jīn Yín Huā', 'pharmaceutical' => 'Lonicerae Flos', 'use' => 'Clears heat toxin with a stronger dispersing and venting action.'],
            ['name' => 'Lián Qiào', 'pharmaceutical' => 'Forsythiae Fructus', 'use' => 'Clears heat toxin and disperses constraint and clumps.'],
            ['name' => 'Pú Gōng Yīng', 'pharmaceutical' => 'Taraxaci Herba', 'use' => 'Addresses firm sores and supports urination.'],
            ['name' => 'Zǐ Huā Dì Dīng', 'pharmaceutical' => 'Violae Herba', 'use' => 'Traditionally emphasized for deep-rooted sores.'],
            ['name' => 'Yě Jú Huā', 'pharmaceutical' => 'Chrysanthemi indici Flos', 'use' => 'Clears heat toxin involving the throat or eyes.'],
        ],
        'warnings' => [
            'Yā Dǎn Zǐ, Shān Dòu Gēn, Yǔ Zhōu Lòu Lú and other supplemental substances have major dose-related toxicity; they are not self-treatment herbs.',
            'Bái Xiǎn Pí has reports of serious liver injury. Běi Dòu Gēn requires verified identity because hazardous Aristolochia adulteration or substitution has occurred.',
            'Suspected abscess, systemic infection, diphtheria, snakebite or cancer requires prompt conventional medical care.',
        ],
        'annex' => [
            'comparison' => 'The source distinguishes this group by location and texture of swelling, degree of heat toxin, dampness or blood stasis, and involvement of the throat, breast, intestine, Lung, skin or urinary tract.',
            'preparation' => 'External use does not eliminate toxicity risk. Verify species, processing, route and dose; do not infer interchangeability from a shared common name.',
            'nomenclature' => 'Botanical verification is essential for Běi Dòu Gēn and similarly named products. Lòu Lú and Yǔ Zhōu Lòu Lú are not interchangeable.',
            'supplemental' => [
                'Supplemental materials include Yā Dǎn Zǐ, Mǎ Chǐ Xiàn, Hóng Téng, Bái Xiǎn Pí, Tǔ Fú Líng, Chuí Pén Cǎo, Jī Gǔ Cǎo, Jī Xuě Cǎo, Bàn Zhī Lián, Mǎ Bó, Shān Dòu Gēn, Běi Dòu Gēn, Qīng Guǒ, Shè Gān, Tǔ Niú Xī, Bái Liǎn, Lòu Lú and Yǔ Zhōu Lòu Lú.',
                'Historical/source-reported only—not a recommendation: older materia medica describe uses for cancers, diphtheria, syphilitic sores, snakebite and antidotal purposes. These claims must not replace evidence-based care.',
            ],
        ],
    ],
    'deficiency-heat' => [
        'number' => '2.5',
        'name' => 'Clear Heat from Deficiency',
        'focus' => 'Lingering or deficiency heat, steaming-bone patterns and afternoon/night fever.',
        'distinction' => 'Qing Hao vents hidden heat; Di Gu Pi cools blood and Lung deficiency fire; Bai Wei adds blood-deficient steaming bone; Yin Chai Hu favors childhood nutritional impairment; Hu Huang Lian adds damp-heat.',
        'coreherbs' => [
            ['name' => 'Qīng Hāo', 'pharmaceutical' => 'Artemisiae annuae Herba', 'use' => 'Vents hidden heat and treats night heat with morning coolness.'],
            ['name' => 'Dì Gǔ Pí', 'pharmaceutical' => 'Lycii Cortex', 'use' => 'Cools blood and clears Lung or deficiency fire.'],
            ['name' => 'Bái Wēi', 'pharmaceutical' => 'Cynanchi atrati Radix', 'use' => 'Cools blood and clears deficiency heat.'],
            ['name' => 'Yín Chái Hú', 'pharmaceutical' => 'Stellariae Radix', 'use' => 'Clears deficiency heat and childhood nutritional-impairment heat.'],
            ['name' => 'Hú Huáng Lián', 'pharmaceutical' => 'Picrorhizae Rhizoma', 'use' => 'Clears deficiency heat and drains damp-heat.'],
        ],
        'warnings' => [
            'Qīng Hāo should not be subjected to prolonged cooking; its preparation differs from dense roots and minerals.',
            'Bái Wēi can be toxic in excessive doses. Hú Huáng Lián is intensely bitter-cold and is CITES Appendix II regulated.',
            'Use caution with Spleen/Stomach cold or deficiency. Yín Chái Hú is not for blood deficiency without heat.',
        ],
        'annex' => [
            'comparison' => 'Qing Hao vents warm pathogens from the yin aspect and is distinguished by night heat with morning coolness. Yin Chai Hu specifically cools deficiency heat without a raising action. Hu Huang Lian is more intensely bitter-cold and adds a downward, damp-heat-draining action.',
            'preparation' => 'Add Qīng Hāo late or use the preparation specified by the formula. Picrorhizae Rhizoma trade may require documentation because the standard species is CITES Appendix II listed.',
            'nomenclature' => 'The standard Hú Huáng Lián species is Picrorhiza scrophulariiflora; common-name substitution should not override botanical verification.',
            'supplemental' => [
                'Bensky discusses Stellariae Radix with Trionycis Carapax for deeper yin-level deficiency heat and compares it with Bupleuri Radix and Artemisiae annuae Herba.',
                'Childhood nutritional impairment and steaming-bone disorder are traditional diagnostic terms, not biomedical diagnoses.',
            ],
        ],
    ],
];
$downwardsections = [
    'purgatives' => [
        'number' => '3.1',
        'name' => 'Purgatives',
        'focus' => 'Cold, downward-draining substances for excess heat, dry stool and intestinal accumulation.',
        'distinction' => 'Da Huang has the broadest action and also clears heat and moves blood; Mang Xiao moistens dryness and softens hardness; Fan Xie Ye is a focused, stronger purgative; Lu Hui also drains Liver fire and kills parasites.',
        'coreherbs' => [
            [
                'name' => 'Dà Huáng',
                'pharmaceutical' => 'Rhei Radix et Rhizoma',
                'nature' => 'Bitter · cold',
                'dose' => '3–15 g',
                'use' => 'Drains heat and purges accumulation; also clears fire toxicity, cools and moves blood, and drains damp-heat.',
            ],
            [
                'name' => 'Máng Xiāo',
                'pharmaceutical' => 'Natrii Sulfas',
                'nature' => 'Salty, bitter · cold',
                'dose' => '6–18 g',
                'use' => 'Purges accumulation, moistens intestinal dryness and softens hard stool or focal hardness.',
            ],
            [
                'name' => 'Fān Xiè Yè',
                'pharmaceutical' => 'Sennae Folium',
                'nature' => 'Sweet, bitter · cold',
                'dose' => '1.5–3 g mild; 4.5–9 g strong',
                'use' => 'A focused, strong purgative for heat accumulation and constipation; the source also notes edema use.',
            ],
            [
                'name' => 'Lú Huì',
                'pharmaceutical' => 'Aloe',
                'nature' => 'Bitter · cold',
                'dose' => '1.5–4.5 g',
                'use' => 'Drains fire and purges accumulation, clears Liver fire and traditionally kills parasites.',
            ],
        ],
        'warnings' => [
            'Pregnancy and lactation restrictions differ by substance: Da Huang requires great caution; Mang Xiao is contraindicated in pregnancy; Fan Xie Ye is contraindicated in pregnancy, breastfeeding and heavy bleeding; Lu Hui is contraindicated in pregnancy and used cautiously during lactation.',
            'Avoid or use only with expert supervision in Spleen/Stomach cold or deficiency, frailty, qi/blood deficiency, dehydration, active intestinal inflammation or an undifferentiated abdominal emergency.',
            'Overuse may cause severe diarrhea, cramping, vomiting, dehydration and electrolyte loss. Long-term anthraquinone exposure can injure the gastrointestinal tract and liver and may cause hypokalemia.',
            'Aloe drug is the dried yellow latex, not aloe-vera gel. Excess can cause gastrointestinal or urinary bleeding, colitis and allergic reactions.',
        ],
        'annex' => [
            'comparison' => 'Da Huang is broad and especially suited to excess heat while also addressing blood and damp-heat. Mang Xiao is selected when dryness and hardness predominate. Fan Xie Ye is stronger and narrower, so smaller doses are emphasized. Lu Hui adds Liver-fire and parasite indications.',
            'preparation' => 'Add Da Huang late or decoct it separately for about three minutes when a stronger purgative action is intended. Dissolve Mang Xiao into the strained decoction or warm water; do not decoct it. Prepare Fan Xie Ye as a tea or add it late. Lu Hui is used in pills or capsules and is not decocted.',
            'nomenclature' => 'Pharmaceutical names distinguish the mineral Natrii Sulfas, medicinal Aloe latex and the accepted botanical sources of rhubarb and senna from similarly named commercial products.',
            'supplemental' => [
                'Historical/source-reported indications and mechanisms are traditional materia-medica descriptions, not instructions for self-treatment.',
                'Persistent constipation with severe pain, vomiting, bleeding, fever, distention or inability to pass gas requires prompt biomedical assessment.',
            ],
        ],
    ],
    'moist-laxatives' => [
        'number' => '3.2',
        'name' => 'Moist Laxatives',
        'focus' => 'Oil-rich seeds that moisten the Intestines when dryness contributes to constipation.',
        'distinction' => 'Huo Ma Ren is gentler, nourishing and suited to deficiency dryness; Yu Li Ren is stronger, promotes urination and is more often used when excess and edema accompany constipation.',
        'coreherbs' => [
            [
                'name' => 'Huǒ Má Rén',
                'pharmaceutical' => 'Cannabis Semen',
                'nature' => 'Sweet · neutral',
                'dose' => '9–15 g, crushed',
                'use' => 'Moistens the Intestines, nourishes yin and supports bowel movement in dry or deficiency constipation.',
            ],
            [
                'name' => 'Yù Lǐ Rén',
                'pharmaceutical' => 'Pruni Semen',
                'nature' => 'Acrid, bitter · neutral',
                'dose' => '3–9 g, crushed',
                'use' => 'Moistens the Intestines and promotes urination to reduce edema; stronger and more draining than Huo Ma Ren.',
            ],
        ],
        'warnings' => [
            'Use Yu Li Ren cautiously during pregnancy and in yin or fluid deficiency because its downward-draining action may further injure fluids.',
            'Excessive Huo Ma Ren can cause nausea, vomiting, diarrhea, numbness, agitation, confusion, dilated pupils or loss of consciousness.',
            'Neither seed is appropriate for unexplained severe abdominal pain, suspected obstruction or persistent constipation requiring diagnostic evaluation.',
        ],
        'annex' => [
            'comparison' => 'Both seeds contain oils that moisten the Intestines. Huo Ma Ren is milder and more nourishing; Yu Li Ren is more forceful, adds a diuretic action and fits excess-type edema and constipation more closely.',
            'preparation' => 'Crush the seeds before use. Species identity, storage and rancidity matter for oil-rich medicinal seeds.',
            'nomenclature' => 'Cannabis Semen refers to the medicinal seed, not cannabis flower or resin. Pruni Semen designates the accepted yuliren botanical sources rather than culinary stone-fruit kernels in general.',
            'supplemental' => [
                'Traditional texts caution that Yu Li Ren can injure fluids and frame it as short-term treatment of the branch rather than unsupervised long-term therapy.',
            ],
        ],
    ],
    'harsh-expellants' => [
        'number' => '3.3',
        'name' => 'Harsh Expellants',
        'focus' => 'Toxic, forceful cathartics that expel severe pathogenic water, phlegm or accumulation.',
        'distinction' => 'Qian Niu Zi is the most moderate and also addresses parasites and food stagnation. Gan Sui, Jing Da Ji and Yuan Hua are short-term severe-water remedies with different tissue emphases. Ba Dou is hot for cold accumulation; Shang Lu expels water through both bowel and bladder.',
        'coreherbs' => [
            [
                'name' => 'Qiān Niú Zǐ',
                'pharmaceutical' => 'Pharbitidis Semen',
                'nature' => 'Bitter · cold · toxic',
                'dose' => '3–6 g decoction; ≤3 g powder',
                'use' => 'Drains water, reduces accumulation and traditionally kills parasites; more moderate than the other harsh expellants.',
            ],
            [
                'name' => 'Gān Suì',
                'pharmaceutical' => 'Kansui Radix',
                'nature' => 'Bitter · cold · toxic',
                'dose' => '0.5–1.5 g',
                'use' => 'Forcefully drains water downward and reduces focal distention, hidden fluid and phlegm accumulation.',
            ],
            [
                'name' => 'Hóng Dà Jǐ',
                'pharmaceutical' => 'Knoxiae Radix',
                'nature' => 'Bitter, acrid · cold · slightly toxic',
                'dose' => '1.5–3 g',
                'use' => 'Drives pathogenic fluids downward and can be applied externally for toxic sores or scrofula.',
            ],
            [
                'name' => 'Jīng Dà Jǐ',
                'pharmaceutical' => 'Euphorbiae pekinensis Radix',
                'nature' => 'Bitter, acrid · cold · toxic',
                'dose' => '1.5–3 g',
                'use' => 'A stronger da ji form for severe water and thin-mucus accumulation; also used externally in the source tradition.',
            ],
            [
                'name' => 'Yuán Huā',
                'pharmaceutical' => 'Genkwa Flos',
                'nature' => 'Bitter, acrid · warm · toxic',
                'dose' => '1.5–3 g',
                'use' => 'Drives out water and thin mucus, especially from the chest and Lung; also has external antiparasitic uses.',
            ],
            [
                'name' => 'Bā Dòu',
                'pharmaceutical' => 'Crotonis Fructus',
                'nature' => 'Acrid · hot · highly toxic',
                'dose' => '0.1–0.3 g/day, defatted only',
                'use' => 'Violently purges severe cold accumulation, drives out water and breaks up clogged phlegm; raw forms are not for internal use.',
            ],
            [
                'name' => 'Shāng Lù',
                'pharmaceutical' => 'Phytolaccae Radix',
                'nature' => 'Bitter · cold · highly toxic',
                'dose' => '3–9 g',
                'use' => 'Expels pathogenic water through the bowel and bladder and is traditionally applied externally for hot sores and swelling.',
            ],
        ],
        'warnings' => [
            'HIGH-RISK GROUP: these substances can cause life-threatening gastrointestinal, renal, neurologic, respiratory and cardiovascular toxicity. They are not appropriate for self-prescribing or home experimentation.',
            'Contraindicated during pregnancy. Avoid in frail or debilitated patients and use only briefly in carefully selected excess patterns under expert supervision.',
            'Do not combine Qian Niu Zi with Ba Dou. Traditional antagonism with Gan Cao is especially emphasized for Gan Sui, Jing Da Ji and Yuan Hua; the source recommends avoiding Euphorbiae pekinensis Radix with Gan Cao while evidence remains unsettled.',
            'Ba Dou is exceptionally hazardous: the source reports 15–20 seeds or about 20 drops of croton oil as potentially lethal. Seeds, oil and vapors can severely irritate skin, eyes, nose and mucosa. Only a correctly defatted pharmaceutical preparation is described for internal use.',
            'Shang Lu poisoning may progress from vomiting and diarrhea to bleeding, hypotension, bradycardia, coma, respiratory or cardiac paralysis and death. Its raw-root dust can injure the eyes, nose and gastrointestinal tract.',
        ],
        'annex' => [
            'comparison' => 'Gan Sui, Jing Da Ji and Yuan Hua treat severe edema and chest/abdominal fluid but differ in emphasis: Gan Sui reaches hidden pockets and channels; da ji drains water from yin and yang organs; Yuan Hua especially drains chest/Lung phlegm and thin mucus. Ba Dou is hot and fits cold accumulation, whereas Da Huang is cold and fits heat accumulation. Shang Lu opens both bowel and urinary routes; Qian Niu Zi is comparatively moderate.',
            'preparation' => 'Vinegar processing reduces toxicity in Gan Sui, Jing Da Ji, Yuan Hua and Shang Lu. Ba Dou should be defatted as Crotonis Fructus pulveratum; controlled oil removal is preferred to unpredictable heating. Processing reduces risk but does not make these herbs suitable for unsupervised use.',
            'nomenclature' => 'Historical da ji may refer to Knoxiae Radix or Euphorbiae pekinensis Radix; this resource separates Hong Da Ji and Jing Da Ji. Botanical and processed-form verification is essential for every herb in this group.',
            'supplemental' => [
                'Historical/source-reported only—not a recommendation: the monographs describe treatment of ascites, pleuritis, severe edema, abscesses, chronic diarrhea and other dangerous conditions. These require conventional diagnostic and emergency care.',
                'The source reports extensive poisoning syndromes and preparation traditions because product identity and processing materially change toxicity; those descriptions are not household preparation instructions.',
            ],
        ],
    ],
];
$draindampnesssections = [
    'drain-dampness' => [
        'number' => '4',
        'name' => 'Herbs that Drain Dampness',
        'focus' => 'Promote urination, transform fluid accumulation and differentiate edema, painful urinary dribbling, jaundice and wind-damp obstruction.',
        'distinction' => 'Choose by mechanism and site: Fu Ling supports the Spleen while draining; Zhu Ling and Ze Xie emphasize urination; urinary-dribbling herbs differ by heat, blood, stones or turbidity; Yin Chen targets jaundice; Han Fang Ji and related agents reach wind-damp edema but carry substantially greater safety concerns.',
        'herbgroups' => [
            [
                'name' => 'Fluid metabolism and edema',
                'focus' => 'Core diuretics and fluid-pathway herbs, differentiated by tonification, temperature and channel emphasis.',
                'herbs' => [
                    ['name' => 'Fú Líng', 'pharmaceutical' => 'Poria', 'nature' => 'Sweet, bland · neutral', 'dose' => '9–15 g', 'use' => 'Promotes urination without injuring qi, strengthens the Spleen and calms the spirit.'],
                    ['name' => 'Zhū Líng', 'pharmaceutical' => 'Polyporus', 'nature' => 'Sweet, bland · slightly cool', 'dose' => '6–12 g', 'use' => 'Promotes urination more strongly than Fu Ling but has no Spleen-tonifying action.'],
                    ['name' => 'Zé Xiè', 'pharmaceutical' => 'Alismatis Rhizoma', 'nature' => 'Sweet, bland · cold', 'dose' => '4.5–9 g', 'use' => 'Drains lower-burner dampness and clears Kidney ministerial fire.'],
                    ['name' => 'Yì Yǐ Rén', 'pharmaceutical' => 'Coicis Semen', 'nature' => 'Sweet, bland · slightly cold', 'dose' => '9–30 g', 'use' => 'Drains dampness, strengthens the Spleen, treats wind-damp obstruction and helps expel pus.'],
                    ['name' => 'Chē Qián Zǐ', 'pharmaceutical' => 'Plantaginis Semen', 'nature' => 'Sweet · cold', 'dose' => '9–15 g, bagged', 'use' => 'Promotes urination, clears damp-heat, solidifies stool, brightens the eyes and transforms phlegm.'],
                    ['name' => 'Huá Shí', 'pharmaceutical' => 'Talcum', 'nature' => 'Sweet, bland · cold', 'dose' => '9–15 g, bagged', 'use' => 'Clears summerheat and damp-heat through urination; also used topically for damp skin lesions.'],
                    ['name' => 'Mù Tōng', 'pharmaceutical' => 'Akebiae Caulis', 'nature' => 'Bitter · slightly cold', 'dose' => '3–6 g', 'use' => 'Promotes urination, drains Heart fire through the Small Intestine and promotes lactation.'],
                    ['name' => 'Chuān Mù Tōng', 'pharmaceutical' => 'Clematidis armandii Caulis', 'nature' => 'Bland, bitter · cold', 'dose' => '3–6 g', 'use' => 'Used similarly to Mu Tong for damp-heat, painful urination and blocked fluid pathways.'],
                    ['name' => 'Tōng Cǎo', 'pharmaceutical' => 'Tetrapanacis Medulla', 'nature' => 'Sweet, bland · slightly cold', 'dose' => '2–4.5 g', 'use' => 'Gently promotes urination through the Lung and Stomach and supports lactation.'],
                    ['name' => 'Dēng Xīn Cǎo', 'pharmaceutical' => 'Junci Medulla', 'nature' => 'Sweet, bland · slightly cold', 'dose' => '1.5–4.5 g', 'use' => 'Guides Heart and Lung heat downward through the urine; traditionally used for restlessness.'],
                ],
            ],
            [
                'name' => 'Painful urinary dribbling and damp-heat',
                'focus' => 'Select by burning heat, blood, stones, turbidity, itching or Liver/Gallbladder involvement.',
                'herbs' => [
                    ['name' => 'Qú Mài', 'pharmaceutical' => 'Dianthi Herba', 'nature' => 'Bitter · cold', 'dose' => '9–15 g', 'use' => 'Clears hot or bloody urinary dribbling and also moves blood stasis.'],
                    ['name' => 'Biǎn Xù', 'pharmaceutical' => 'Polygoni avicularis Herba', 'nature' => 'Bitter · slightly cold', 'dose' => '9–15 g', 'use' => 'Clears Bladder damp-heat, promotes urination and relieves damp-heat itching.'],
                    ['name' => 'Shí Wéi', 'pharmaceutical' => 'Pyrrosiae Folium', 'nature' => 'Bitter, sweet · slightly cold', 'dose' => '6–9 g', 'use' => 'Treats hot, stony or bloody urinary dribbling, cools blood and clears Lung heat.'],
                    ['name' => 'Dì Fū Zǐ', 'pharmaceutical' => 'Kochiae Fructus', 'nature' => 'Sweet, bitter · cold', 'dose' => '9–15 g', 'use' => 'Promotes urination and treats damp-heat itching of the skin and genital region.'],
                    ['name' => 'Jīn Qián Cǎo', 'pharmaceutical' => 'Lysimachiae Herba', 'nature' => 'Sweet, salty · slightly cold', 'dose' => '15–30 g', 'use' => 'Expels urinary and biliary stones, clears damp-heat jaundice and resolves toxic swelling.'],
                    ['name' => 'Guǎng Jīn Qián Cǎo', 'pharmaceutical' => 'Desmodii styracifolii Herba', 'nature' => 'Sweet, bland · cool', 'dose' => '15–30 g', 'use' => 'Primarily treats urinary stones, painful urinary dribbling and blood in the urine.'],
                    ['name' => 'Lián Qián Cǎo', 'pharmaceutical' => 'Glechomae Herba', 'nature' => 'Slightly sweet · slightly cold', 'dose' => '15–30 g', 'use' => 'Clears heat, promotes urination and addresses swelling, toxicity and cough.'],
                    ['name' => 'Hǎi Jīn Shā', 'pharmaceutical' => 'Lygodii Spora', 'nature' => 'Sweet, bland · cold', 'dose' => '6–15 g, bagged', 'use' => 'Excels at rough, stony painful urination and also treats biliary stones.'],
                    ['name' => 'Yù Mǐ Xū', 'pharmaceutical' => 'Maydis Stigma', 'nature' => 'Sweet, bland · neutral', 'dose' => '15–30 g', 'use' => 'Promotes urination, reduces edema and clears damp-heat from the Liver and Gallbladder.'],
                ],
            ],
            [
                'name' => 'Turbidity, jaundice and special functions',
                'focus' => 'Separate clear from turbid, combine drainage with bowel or skin actions, or focus on jaundice and toxicity.',
                'herbs' => [
                    ['name' => 'Bì Xiè', 'pharmaceutical' => 'Dioscoreae hypoglaucae Rhizoma', 'nature' => 'Bitter · neutral', 'dose' => '9–15 g', 'use' => 'Separates the clear from the turbid for cloudy urine or discharge and treats wind-damp obstruction.'],
                    ['name' => 'Mián Bì Xiè', 'pharmaceutical' => 'Dioscoreae septemlobae Rhizoma', 'nature' => 'Bitter · neutral', 'dose' => '9–15 g', 'use' => 'Used interchangeably with Bi Xie to separate clear from turbid and expel dampness.'],
                    ['name' => 'Dōng Guā Zǐ', 'pharmaceutical' => 'Benincasae Semen', 'nature' => 'Sweet · cold', 'dose' => '9–15 g', 'use' => 'Clears heat, transforms phlegm and expels pus in Lung or intestinal abscess patterns.'],
                    ['name' => 'Dōng Guā Pí', 'pharmaceutical' => 'Benincasae Exocarpium', 'nature' => 'Sweet, bland · slightly cold', 'dose' => '15–30 g', 'use' => 'Gently promotes urination and reduces edema without strongly injuring normal qi.'],
                    ['name' => 'Dōng Kuí Guǒ', 'pharmaceutical' => 'Malvae Fructus', 'nature' => 'Sweet · cold', 'dose' => '3–9 g', 'use' => 'Promotes urination and lactation and moistens the Intestines when constipation accompanies edema.'],
                    ['name' => 'Qīng Má Zǐ', 'pharmaceutical' => 'Abutili Semen', 'nature' => 'Bitter · neutral', 'dose' => '3–9 g', 'use' => 'Clears heat, resolves dampness and toxicity, and treats superficial visual obstruction.'],
                    ['name' => 'Chì Xiǎo Dòu', 'pharmaceutical' => 'Phaseoli Semen', 'nature' => 'Sweet, sour · neutral', 'dose' => '9–30 g', 'use' => 'Promotes urination, reduces edema, resolves toxicity and disperses swelling.'],
                    ['name' => 'Yīn Chén', 'pharmaceutical' => 'Artemisiae scopariae Herba', 'nature' => 'Bitter · slightly cold', 'dose' => '9–30 g', 'use' => 'The principal herb for damp-heat jaundice; also used in cold-damp jaundice with appropriate warming partners.'],
                ],
            ],
            [
                'name' => 'Wind-damp edema and higher-risk agents',
                'focus' => 'Powerful or toxic agents that require verified identity, precise dosing and professional supervision.',
                'herbs' => [
                    ['name' => 'Hàn Fáng Jǐ', 'pharmaceutical' => 'Stephaniae tetrandrae Radix', 'nature' => 'Bitter, acrid · cold · toxic', 'dose' => '4.5–9 g', 'use' => 'Strongly drains lower-burner dampness and edema and treats hot wind-damp painful obstruction.', 'risk' => true],
                    ['name' => 'Mù Fáng Jǐ', 'pharmaceutical' => 'Cocculi Radix', 'nature' => 'Bitter, acrid · cold', 'dose' => '6–12 g', 'use' => 'Dispels wind-dampness, unblocks the channels and reduces edema with urinary difficulty.'],
                    ['name' => 'Bàn Biān Lián', 'pharmaceutical' => 'Lobeliae chinensis Herba', 'nature' => 'Sweet, bland · slightly cold · toxic', 'dose' => '3–15 g', 'use' => 'Promotes urination, cools the blood and resolves toxicity in edema or toxic swelling patterns.', 'risk' => true],
                ],
            ],
        ],
        'warnings' => [
            'IDENTITY ALERT: do not use Guan Mu Tong, Guang Fang Ji or any Aristolochia source. Aristolochic acid is nephrotoxic and carcinogenic; morphology alone is insufficient, so chemical verification of identity and absence of aristolochic acid is essential.',
            'PREGNANCY: Mu Tong is traditionally contraindicated; Chuan Mu Tong, Qu Mai, Yi Yi Ren, Dong Kui Guo, Qing Ma Zi, Han Fang Ji and other downward or blood-moving herbs require avoidance or specialist assessment. Historical abortifacient claims are not treatment guidance.',
            'Talcum must never be inhaled or injected. Che Qian Zi and Hai Jin Sha are decocted in a bag; correct pharmaceutical preparation matters for seeds, spores, minerals and processed products.',
            'Han Fang Ji overdose has caused vomiting, tremor, ataxia, convulsions, quadriplegia and fatal respiratory failure. Ban Bian Lian overdose may cause salivation, diarrhea, pulse changes, spasms and fatal respiratory paralysis.',
            'Long or excessive use of draining herbs may injure fluids, yin, Spleen qi or Kidney qi. Painful urination, blood in urine, jaundice, edema, breathing difficulty, stones, fever or suspected poisoning requires biomedical assessment.',
        ],
        'annex' => [
            'comparison' => 'Fu Ling is the best Spleen-supporting core drain, Zhu Ling is more purely diuretic, and Ze Xie is colder and reaches Kidney/Bladder damp-heat. Qu Mai favors heat and blood, Bian Xu balanced damp-heat, Bi Xie predominant turbidity, Shi Wei bleeding, and Hai Jin Sha or Jin Qian Cao stones. Yin Chen is specific for jaundice; Jin Qian Cao adds stone-softening; Dong Kui Guo adds constipation; Han Fang Ji emphasizes lower-body edema and hot wind-damp obstruction.',
            'preparation' => 'Use a cloth bag for Plantaginis Semen, Talcum and Lygodii Spora. Crush seeds when the monograph specifies it. Raw and processed forms may change temperature or emphasis: raw Coicis Semen drains more, dry-fried forms support the Spleen; salt-processing directs some herbs toward the Kidney. These notes are identification and Board-review points, not household preparation instructions.',
            'nomenclature' => 'Common names are unsafe guides in this chapter. Mu Tong, Fang Ji, Jin Qian Cao and Bi Xie each have regional or historical substitutes. Require the pharmaceutical and botanical identity; for Fang Ji and Mu Tong products, verify the absence of Aristolochia and aristolochic acid by appropriate chemical methods.',
            'supplemental' => [
                'Historical/source-reported only—not a recommendation: the monographs discuss treatment of abscesses, snakebite, schistosomiasis, syphilitic lesions, diabetes, hypertension and other serious diseases. These claims do not replace evidence-based diagnosis or care.',
                'The chapter summary groups herbs by bland leaching, bitter-cold draining, slippery movement through the orifices, stone resolution, blood involvement, jaundice, itching and wind-damp obstruction; the groups above preserve those distinctions without creating new materia-medica categories.',
                'Product quality notes remain relevant for examination and dispensing: correct species, plant part, processing, freedom from foreign matter and appropriate chemical testing can materially change safety.',
            ],
        ],
    ],
];
$selectedbensky = null;
$selectedbenskygroup = '';
if ($track === 'herbs' && $sectionkey !== '' && isset($clearheatsections[$sectionkey])) {
    $selectedbensky = $clearheatsections[$sectionkey];
    $selectedbenskygroup = 'clear-heat';
} else if ($track === 'herbs' && $sectionkey !== '' && isset($downwardsections[$sectionkey])) {
    $selectedbensky = $downwardsections[$sectionkey];
    $selectedbenskygroup = 'downward-draining';
} else if ($track === 'herbs' && $sectionkey !== '' && isset($draindampnesssections[$sectionkey])) {
    $selectedbensky = $draindampnesssections[$sectionkey];
    $selectedbenskygroup = 'drain-dampness';
} else if ($sectionkey !== '') {
    $sectionkey = '';
}
$comparisonfile = static function(string $filename) use ($CFG): string {
    $filename = basename($filename);
    $directories = [
        $CFG->dirroot . '/study-guide/comparisons',
        '/var/www/acupuncturetests-study-guide/current/comparisons',
        dirname($CFG->dirroot) . '/study-guide/comparisons',
        '/var/www/html/study-guide/comparisons',
    ];
    foreach (array_unique($directories) as $directory) {
        $path = rtrim($directory, '/') . '/' . $filename;
        if (is_readable($path)) {
            return $path;
        }
    }
    return '';
};

$formulacategories = [];
$formulamodificationcount = 0;
$formulatotals = [
    'questions' => 0,
    'formulas' => 0,
    'categories' => 0,
];
$herbcategories = [];
$herbtotals = [
    'questions' => 0,
    'chapters' => 0,
    'charts' => 0,
];
if ($track === 'herbs' || $track === 'quiz') {
    $herbbankpath = $comparisonfile('herb-comparison-bank-data.json');
    if ($herbbankpath !== '') {
        $herbbank = json_decode(file_get_contents($herbbankpath), true);
        if (is_array($herbbank)) {
            $herbtotals['questions'] = (int)($herbbank['totals']['questions'] ?? 0);
            $herbtotals['chapters'] = (int)($herbbank['totals']['chapters'] ?? 0);
            $herbtotals['charts'] = (int)($herbbank['totals']['charts'] ?? 0);
            foreach ($herbbank['chapters'] ?? [] as $chapter) {
                $number = (int)($chapter['number'] ?? 0);
                $name = trim((string)($chapter['title'] ?? ''));
                if ($number < 1 || $name === '') {
                    continue;
                }
                $herbcategories[] = [
                    'number' => $number,
                    'name' => $name,
                    'questions' => (int)($chapter['questionCount'] ?? 0),
                    'charts' => (int)($chapter['chartCount'] ?? 0),
                ];
            }
        }
    }
}

if ($track === 'herbs') {
    $sections = $DB->get_records_select(
        'course_sections',
        'course = :courseid AND section BETWEEN :firstsection AND :lastsection',
        [
            'courseid' => 45,
            'firstsection' => 0,
            'lastsection' => 34,
        ],
        'section ASC',
        'id,section,name'
    );

    foreach ($sections as $section) {
        $number = (int)$section->section;
        $name = trim((string)$section->name);
        if ($number === 0 && $name === '') {
            $name = 'General / Uncategorized';
        } else if ($name === '') {
            $name = 'Topic ' . $number;
        }
        $topics[] = [
            'number' => $number,
            'name' => format_string($name),
        ];
    }
} else {
    $formulabankpath = $comparisonfile('formula-practice-data.json');
    if ($formulabankpath !== '') {
        $formulabank = json_decode(file_get_contents($formulabankpath), true);
        if (is_array($formulabank)) {
            $formulabankdir = dirname($formulabankpath);
            $manifestpath = $formulabankdir . '/formula-modifications-manifest.json';
            if (is_readable($manifestpath)) {
                $manifest = json_decode(file_get_contents($manifestpath), true);
                $knowncategories = array_column($formulabank['categories'] ?? [], null, 'key');
                $knownformulas = array_fill_keys(array_map(
                    static fn($formula) => (int)($formula['number'] ?? 0),
                    $formulabank['formulas'] ?? []
                ), true);
                $knownquestions = array_fill_keys(array_map(
                    static fn($question) => (string)($question['sourceNumber'] ?? ''),
                    $formulabank['questions'] ?? []
                ), true);
                foreach ($manifest['files'] ?? [] as $filename) {
                    $safe = basename((string)$filename);
                    $supplementpath = $formulabankdir . '/' . $safe;
                    if (!is_readable($supplementpath)) {
                        continue;
                    }
                    $supplement = json_decode(file_get_contents($supplementpath), true);
                    if (!is_array($supplement)) {
                        continue;
                    }
                    foreach ($supplement['categories'] ?? [] as $category) {
                        $key = (string)($category['key'] ?? '');
                        if ($key !== '' && !isset($knowncategories[$key])) {
                            $formulabank['categories'][] = $category;
                            $knowncategories[$key] = $category;
                        }
                    }
                    foreach ($supplement['formulas'] ?? [] as $formula) {
                        $number = (int)($formula['number'] ?? 0);
                        if ($number > 0 && !isset($knownformulas[$number])) {
                            $formulabank['formulas'][] = $formula;
                            $knownformulas[$number] = true;
                        }
                    }
                    foreach ($supplement['questions'] ?? [] as $question) {
                        $source = (string)($question['sourceNumber'] ?? '');
                        if ($source !== '' && !isset($knownquestions[$source])) {
                            unset($question['translationEs']);
                            $formulabank['questions'][] = $question;
                            $knownquestions[$source] = true;
                        }
                    }
                }
                $formulabank['totals']['questions'] = count($formulabank['questions'] ?? []);
                $formulabank['totals']['formulas'] = count($formulabank['formulas'] ?? []);
                $formulabank['totals']['categories'] = count($formulabank['categories'] ?? []);
            }
            $formulatotals['questions'] = (int)($formulabank['totals']['questions'] ?? 0);
            $formulatotals['formulas'] = (int)($formulabank['totals']['formulas'] ?? 0);
            $formulatotals['categories'] = (int)($formulabank['totals']['categories'] ?? 0);
            foreach ($formulabank['categories'] ?? [] as $category) {
                $key = clean_param($category['key'] ?? '', PARAM_ALPHANUMEXT);
                $name = trim((string)($category[$ises ? 'es' : 'en'] ?? $category['en'] ?? ''));
                if ($key === '' || $name === '') {
                    continue;
                }
                $count = 0;
                foreach ($formulabank['questions'] ?? [] as $question) {
                    if (in_array($key, $question['categoryKeys'] ?? [], true)) {
                        $count++;
                    }
                }
                if ($key === 'modifications') {
                    $formulamodificationcount = $count;
                    continue;
                }
                $formulacategories[] = [
                    'key' => $key,
                    'name' => $name,
                    'count' => $count,
                ];
            }
            $categoryorder = [
                'release-exterior' => 1,
                'purge' => 2,
                'harmonize' => 3,
                'clear-heat' => 4,
                'dispel-summerheat' => 5,
                'warm-interior' => 6,
                'exterior-interior-excess' => 7,
                'tonify' => 8,
                'stabilize-bind' => 9,
                'calm-shen' => 10,
                'regulate-qi' => 12,
                'invigorate-blood' => 13,
                'expel-wind' => 14,
                'treat-dryness' => 15,
                'expel-damp' => 16,
                'transform-phlegm' => 17,
                'digestive' => 18,
                'treat-abscesses-sores' => 20,
            ];
            usort($formulacategories, static function(array $left, array $right) use ($categoryorder): int {
                return ($categoryorder[$left['key']] ?? 999) <=> ($categoryorder[$right['key']] ?? 999);
            });
            $formulatotals['categories'] = count($formulacategories);
        }
    }
}

$singleherbpracticepath = '/study-guide/comparisons/'
    . ($ises ? 'single-herb-practice-es.html' : 'single-herb-practice.html');
$singleherbpracticeurl = new moodle_url($singleherbpracticepath, $fromlibrary ? ['from' => 'library'] : []);
$herbquestionbankpath = '/study-guide/comparisons/'
    . ($ises ? 'herb-comparison-bank-es.html' : 'herb-comparison-bank.html');
$herbquestionbankurl = new moodle_url($herbquestionbankpath, [
    'chapter' => 0,
    'mode' => 'questions',
] + ($fromlibrary ? ['from' => 'library'] : []));
$formulapracticepath = '/study-guide/comparisons/'
    . ($ises ? 'formula-practice-es.html' : 'formula-practice.html');
$formulapracticeurl = new moodle_url($formulapracticepath, $fromlibrary ? ['from' => 'library'] : []);
$formulaquestionbankpath = '/study-guide/comparisons/'
    . ($ises ? 'formula-question-bank-es.html' : 'formula-question-bank.html');
$formulaquestionbankurl = new moodle_url($formulaquestionbankpath, $fromlibrary ? ['from' => 'library'] : []);

echo $OUTPUT->header();
?>
<main class="herb-board">
    <nav class="herb-board__page-navigation" aria-label="<?php echo $ises ? 'Navegación de Herbología' : 'Herbology navigation'; ?>">
        <a class="herb-board__back" data-herbology-step-back href="<?php echo $dashboardurl->out(false); ?>"><?php echo $ises
            ? '← Atrás' : '← Back'; ?></a>
        <?php if (!in_array($track, ['exams', 'quiz', 'clinicalcases', 'domains', 'simulation'], true)) { ?>
            <a class="herb-board__tab" href="<?php echo $singleherbsurl->out(false); ?>"
                <?php echo $track === 'herbs' ? 'aria-current="page"' : ''; ?>><?php echo $ises ? 'Hierbas individuales' : 'Single Herbs'; ?></a>
            <a class="herb-board__tab" href="<?php echo $formulasurl->out(false); ?>"
                <?php echo $track === 'formulas' ? 'aria-current="page"' : ''; ?>><?php echo $ises ? 'Fórmulas' : 'Formulas'; ?></a>
        <?php } ?>
        <a class="herb-board__tab herb-board__tab--language" hreflang="<?php echo $ises ? 'en' : 'es'; ?>"
            href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
    </nav>

    <?php if ($track === 'exams') { ?>
        <nav class="herb-board__practice-route" aria-label="<?php echo $ises
            ? 'Ruta de navegación de exámenes' : 'Exam navigation path'; ?>">
            <a href="<?php echo ($mixdomains ? $examsurl : $examsdashboardurl)->out(false); ?>"><?php echo $ises
                ? 'Exámenes' : 'Exams'; ?></a>
            <span aria-hidden="true">›</span>
            <span aria-current="page"><?php echo $ises
                ? ($mixdomains
                    ? ($exammode === 'adaptive' ? 'Examen adaptativo' : 'Examen lineal')
                    : 'Exámenes de Herbología')
                : ($mixdomains
                    ? ($exammode === 'adaptive' ? 'Adaptive exam' : 'Linear exam')
                    : 'Herbology Exams'); ?></span>
        </nav>
    <?php } ?>

    <?php if ($track === 'simulation') { ?>
        <nav class="herb-board__practice-route" aria-label="<?php echo $ises
            ? 'Ruta de navegación de simulación' : 'Simulation navigation path'; ?>">
            <a href="<?php echo ($mixdomains ? $simulationurl : $examsdashboardurl)->out(false); ?>"><?php echo $ises
                ? 'Simulación' : 'Simulation'; ?></a>
            <span aria-hidden="true">›</span>
            <span aria-current="page"><?php echo $mixdomains
                ? ($exammode === 'adaptive'
                    ? ($ises ? 'Simulación adaptativa 1' : 'Adaptive Simulation 1')
                    : ($ises ? 'Simulación lineal 1' : 'Linear Simulation 1'))
                : ($ises ? 'Simulación de Herbología' : 'Herbology Simulation'); ?></span>
        </nav>
    <?php } ?>

    <?php if ($track === 'herbs') { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-practice-title">
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-practice-title"><?php echo $ises
                        ? 'Estudia hierbas individuales'
                        : 'Study Single Herbs'; ?></h3>
                    <p><?php echo $ises
                        ? 'Lee preguntas con sus respuestas y explicaciones, o utiliza flashcards de memoria.'
                        : 'Read questions with answers and explanations, or use recall flashcards.'; ?></p>
                </div>
            </div>
            <div class="herb-board__summary herb-board__summary--panel" aria-label="<?php echo $ises
                ? 'Totales del banco de hierbas' : 'Single Herbs bank totals'; ?>">
                <span><strong><?php echo $herbtotals['questions']; ?></strong> <?php echo $ises
                    ? 'preguntas verificadas' : 'verified questions'; ?></span>
                <span><strong><?php echo $herbtotals['chapters']; ?></strong> <?php echo $ises
                    ? 'capítulos de estudio' : 'study chapters'; ?></span>
                <span><strong><?php echo $herbtotals['charts']; ?></strong> <?php echo $ises
                    ? 'gráficos comparativos' : 'comparison charts'; ?></span>
            </div>
            <div class="herb-board__launches herb-board__launches--single">
                <a class="herb-board__launch" href="<?php echo $herbquestionbankurl->out(false); ?>">
                    <strong><?php echo $ises
                        ? 'Preguntas de estudio y flashcards'
                        : 'Study Questions &amp; Flashcards'; ?></strong>
                    <span><?php echo $ises
                        ? 'Estudia 249 preguntas con sus respuestas, explicaciones y flashcards.'
                        : 'Study 249 questions with answers, explanations, and flashcards.'; ?></span>
                </a>
            </div>
            <h4 class="herb-board__subheading"><?php echo $ises
                ? 'Estudiar por capítulos de Yang'
                : "Study by Yang Chapter's"; ?></h4>
            <div class="herb-board__topics">
                <?php foreach ($herbcategories as $category) {
                    $categoryurl = new moodle_url($herbquestionbankpath, [
                        'chapter' => $category['number'],
                        'mode' => 'questions',
                    ] + ($fromlibrary ? ['from' => 'library'] : []));
                ?>
                    <a class="herb-board__topic" href="<?php echo $categoryurl->out(false); ?>">
                        <span><?php echo s($category['name']); ?></span>
                    </a>
                <?php } ?>
            </div>
            <?php if (!$herbcategories) { ?>
                <div class="herb-board__notice">
                    The Single Herbs comparison bank is not installed on this server yet. Deploy the bank data to enable chapter practice.
                </div>
            <?php } ?>
        </section>

        <?php if ($selectedbensky) { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-topics-title">
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-topics-title">Choose a Single Herbs topic</h3>
                    <p>Includes the opening general section, Topics 1–34 and a mixed review.</p>
                </div>
            </div>
            <div class="herb-board__topics">
                <?php foreach ($topics as $topic) {
                    $normalizedname = core_text::strtolower(trim(strip_tags($topic['name'])));
                    $isclearheat = in_array($normalizedname, ['clear heat', 'herbs that clear heat'], true);
                    $isdownwarddraining = in_array($normalizedname, [
                        'downward-draining herbs',
                        'downward draining herbs',
                        'downward-draining',
                        'downward draining',
                        'herbs that drain downward',
                        'drain downward',
                    ], true) || (strpos($normalizedname, 'downward') !== false
                        && (strpos($normalizedname, 'drain') !== false || strpos($normalizedname, 'herb') !== false));
                    $isdraindampness = in_array($normalizedname, [
                        'herbs that drain dampness',
                        'drain dampness',
                        'draining dampness',
                        'herbs that drain damp',
                    ], true) || (strpos($normalizedname, 'damp') !== false
                        && strpos($normalizedname, 'drain') !== false);
                ?>
                    <div class="herb-board__topic">
                        <strong><?php echo $topic['number'] === 0 ? 'G' : $topic['number']; ?></strong>
                        <span><?php echo $topic['name']; ?></span>
                    </div>
                    <?php if ($isclearheat) { ?>
                        <section class="herb-board__subcategories" aria-labelledby="clear-heat-subcategories-title">
                            <div class="herb-board__subcategories-heading">
                                <span class="herb-board__subcategories-kicker">Subcategories</span>
                                <h4 id="clear-heat-subcategories-title">Clear Heat sections</h4>
                            </div>
                            <div class="herb-board__subcategories-grid">
                                <?php foreach ($clearheatsections as $subcategorykey => $subcategory) {
                                    $subcategoryurl = new moodle_url('/blocks/menu/herbology-board.php', [
                                        'track' => 'herbs',
                                        'section' => $subcategorykey,
                                    ] + $languagecontext, 'bensky-section');
                                    $isselected = $sectionkey === $subcategorykey;
                                ?>
                                    <a class="herb-board__topic herb-board__topic--subcategory<?php echo $isselected ? ' herb-board__topic--selected' : ''; ?>"
                                        href="<?php echo $subcategoryurl->out(false); ?>"
                                        <?php echo $isselected ? 'aria-current="page"' : ''; ?>>
                                        <strong><?php echo s($subcategory['number']); ?></strong>
                                        <span>
                                            <?php echo s($subcategory['name']); ?>
                                            <small><?php echo s($subcategory['focus']); ?></small>
                                        </span>
                                    </a>
                                <?php } ?>
                            </div>
                            <p class="herb-board__subcategories-note">The original Clear Heat topic remains the main
                                Yang-based card. These five Bensky sections are study supplements, not replacements.</p>
                        </section>
                    <?php } ?>
                    <?php if ($isdownwarddraining) { ?>
                        <section class="herb-board__subcategories herb-board__subcategories--downward"
                            aria-labelledby="downward-draining-subcategories-title">
                            <div class="herb-board__subcategories-heading">
                                <span class="herb-board__subcategories-kicker">Subcategories</span>
                                <h4 id="downward-draining-subcategories-title">Downward-Draining Herbs sections</h4>
                            </div>
                            <div class="herb-board__subcategories-grid herb-board__subcategories-grid--three">
                                <?php foreach ($downwardsections as $subcategorykey => $subcategory) {
                                    $subcategoryurl = new moodle_url('/blocks/menu/herbology-board.php', [
                                        'track' => 'herbs',
                                        'section' => $subcategorykey,
                                    ] + $languagecontext, 'bensky-section');
                                    $isselected = $sectionkey === $subcategorykey;
                                ?>
                                    <a class="herb-board__topic herb-board__topic--subcategory<?php echo $isselected ? ' herb-board__topic--selected' : ''; ?>"
                                        href="<?php echo $subcategoryurl->out(false); ?>"
                                        <?php echo $isselected ? 'aria-current="page"' : ''; ?>>
                                        <strong><?php echo s($subcategory['number']); ?></strong>
                                        <span>
                                            <?php echo s($subcategory['name']); ?>
                                            <small><?php echo s($subcategory['focus']); ?></small>
                                        </span>
                                    </a>
                                <?php } ?>
                            </div>
                            <p class="herb-board__subcategories-note">The original Downward-Draining topic remains the
                                main Yang-based card. These three Bensky sections are study supplements, not replacements.</p>
                        </section>
                    <?php } ?>
                    <?php if ($isdraindampness) { ?>
                        <section class="herb-board__subcategories herb-board__subcategories--dampness"
                            aria-labelledby="drain-dampness-subcategories-title">
                            <div class="herb-board__subcategories-heading">
                                <span class="herb-board__subcategories-kicker">Bensky chapter</span>
                                <h4 id="drain-dampness-subcategories-title">Herbs that Drain Dampness</h4>
                            </div>
                            <div class="herb-board__subcategories-grid herb-board__subcategories-grid--single">
                                <?php foreach ($draindampnesssections as $subcategorykey => $subcategory) {
                                    $subcategoryurl = new moodle_url('/blocks/menu/herbology-board.php', [
                                        'track' => 'herbs',
                                        'section' => $subcategorykey,
                                    ] + $languagecontext, 'bensky-section');
                                    $isselected = $sectionkey === $subcategorykey;
                                ?>
                                    <a class="herb-board__topic herb-board__topic--subcategory<?php echo $isselected ? ' herb-board__topic--selected' : ''; ?>"
                                        href="<?php echo $subcategoryurl->out(false); ?>"
                                        <?php echo $isselected ? 'aria-current="page"' : ''; ?>>
                                        <strong><?php echo s($subcategory['number']); ?></strong>
                                        <span>
                                            <?php echo s($subcategory['name']); ?>
                                            <small><?php echo s($subcategory['focus']); ?></small>
                                        </span>
                                    </a>
                                <?php } ?>
                            </div>
                            <p class="herb-board__subcategories-note">The original course topic remains the main
                                Yang-based card. This Bensky chapter adds 30 grouped materia-medica profiles, high-yield
                                comparisons and persistent safety alerts.</p>
                        </section>
                    <?php } ?>
                <?php } ?>
                <div class="herb-board__topic herb-board__topic--mixed">
                    <strong>M</strong>
                    <span>Mixed Review</span>
                </div>
            </div>
        </section>
        <?php } ?>

        <?php if ($selectedbensky) { ?>
            <section class="herb-board__panel herb-board__clear-heat<?php echo $selectedbenskygroup === 'downward-draining'
                ? ' herb-board__downward-draining'
                : ($selectedbenskygroup === 'drain-dampness' ? ' herb-board__drain-dampness' : ''); ?>"
                id="bensky-section" aria-labelledby="bensky-section-title">
                <header class="herb-board__clear-heat-heading">
                    <div>
                        <p class="herb-board__eyebrow">BENSKY SUPPLEMENT · SECTION <?php echo s($selectedbensky['number']); ?></p>
                        <h3 id="bensky-section-title"><?php echo s($selectedbensky['name']); ?></h3>
                        <p><?php echo s($selectedbensky['focus']); ?></p>
                    </div>
                    <span class="herb-board__scope-badge"><?php echo $selectedbenskygroup === 'downward-draining'
                        ? '3-section Downward-Draining structure'
                        : ($selectedbenskygroup === 'drain-dampness'
                            ? '30 materia-medica profiles · 4 study groups'
                            : '5-section Clear Heat structure'); ?></span>
                </header>

                <div class="herb-board__high-yield">
                    <strong>High-yield distinction</strong>
                    <span><?php echo s($selectedbensky['distinction']); ?></span>
                </div>

                <?php $herbgroups = $selectedbensky['herbgroups'] ?? [[
                    'name' => 'Core herbs',
                    'focus' => '',
                    'herbs' => $selectedbensky['coreherbs'] ?? [],
                ]]; ?>
                <?php foreach ($herbgroups as $herbgroup) { ?>
                    <section class="herb-board__herb-group">
                        <div class="herb-board__herb-group-heading">
                            <h4 class="herb-board__subheading"><?php echo s($herbgroup['name']); ?></h4>
                            <?php if (!empty($herbgroup['focus'])) { ?><p><?php echo s($herbgroup['focus']); ?></p><?php } ?>
                        </div>
                        <div class="herb-board__herbs">
                            <?php foreach ($herbgroup['herbs'] as $herb) {
                                $istoxic = !empty($herb['risk'])
                                    || (!empty($herb['nature'])
                                        && strpos(core_text::strtolower($herb['nature']), 'toxic') !== false);
                            ?>
                                <article class="herb-board__herb-card<?php echo $istoxic ? ' herb-board__herb-card--toxic' : ''; ?>">
                                    <h5><?php echo s($herb['name']); ?></h5>
                                    <span><?php echo s($herb['pharmaceutical']); ?></span>
                                    <?php if (!empty($herb['nature']) || !empty($herb['dose'])) { ?>
                                        <div class="herb-board__herb-meta">
                                            <?php if (!empty($herb['nature'])) { ?><small><?php echo s($herb['nature']); ?></small><?php } ?>
                                            <?php if (!empty($herb['dose'])) { ?><small>Dose: <?php echo s($herb['dose']); ?></small><?php } ?>
                                        </div>
                                    <?php } ?>
                                    <p><?php echo s($herb['use']); ?></p>
                                </article>
                            <?php } ?>
                        </div>
                    </section>
                <?php } ?>

                <aside class="herb-board__safety" aria-labelledby="bensky-safety-title">
                    <h4 id="bensky-safety-title">Safety stays visible</h4>
                    <ul>
                        <?php foreach ($selectedbensky['warnings'] as $warning) { ?>
                            <li><?php echo s($warning); ?></li>
                        <?php } ?>
                    </ul>
                </aside>

                <details class="herb-board__annex">
                    <summary>
                        <span>Bensky extended annex</span>
                        <small>Comparisons, preparation, nomenclature and supplemental herbs</small>
                    </summary>
                    <div class="herb-board__annex-body">
                        <section>
                            <h4>Comparative notes</h4>
                            <p><?php echo s($selectedbensky['annex']['comparison']); ?></p>
                        </section>
                        <section>
                            <h4>Preparation and product notes</h4>
                            <p><?php echo s($selectedbensky['annex']['preparation']); ?></p>
                        </section>
                        <section>
                            <h4>Nomenclature and identity</h4>
                            <p><?php echo s($selectedbensky['annex']['nomenclature']); ?></p>
                        </section>
                        <section class="herb-board__annex-wide">
                            <h4>Supplemental/source notes</h4>
                            <ul>
                                <?php foreach ($selectedbensky['annex']['supplemental'] as $note) { ?>
                                    <li><?php echo s($note); ?></li>
                                <?php } ?>
                            </ul>
                        </section>
                    </div>
                </details>
                <p class="herb-board__educational-note">Educational Board-review content only. Traditional indications
                    describe pattern-based materia medica and do not constitute diagnosis, prescribing or emergency care.</p>
            </section>
        <?php } ?>
    <?php } else if ($track === 'simulation') { ?>
        <section class="herb-board__panel herb-board__simulation" aria-labelledby="herb-board-simulation-title">
            <?php if (!$mixdomains) { ?>
                <div class="herb-board__simulation-intro">
                    <span><?php echo $ises ? 'SIMULACIÓN CRONOMETRADA' : 'TIMED SIMULATION'; ?></span>
                    <h3 id="herb-board-simulation-title"><?php echo $ises
                        ? 'Examen de simulación 1'
                        : 'Simulation Exam 1'; ?></h3>
                    <p><?php echo $ises
                        ? 'Reproduce una sesión completa de 100 preguntas en 2 horas y 30 minutos. No verás respuestas, explicaciones ni feedback hasta finalizar.'
                        : 'Rehearse a complete 100-question session in 2 hours and 30 minutes. Answers, explanations, and feedback stay hidden until you finish.'; ?></p>
                    <div class="herb-board__simulation-facts" aria-label="<?php echo $ises ? 'Datos de la simulación' : 'Simulation facts'; ?>">
                        <span><strong>100</strong> <?php echo $ises ? 'preguntas' : 'questions'; ?></span>
                        <span><strong>150</strong> <?php echo $ises ? 'minutos' : 'minutes'; ?></span>
                        <span><strong>6</strong> <?php echo $ises ? 'dominios ponderados' : 'weighted domains'; ?></span>
                        <span><strong>0</strong> <?php echo $ises ? 'feedback durante el intento' : 'feedback during the attempt'; ?></span>
                    </div>
                </div>

                <section class="herb-board__simulation-blueprint" aria-labelledby="simulation-blueprint-title">
                    <header>
                        <div>
                            <span><?php echo $ises ? 'COMPOSICIÓN DEL EXAMEN' : 'EXAM BLUEPRINT'; ?></span>
                            <h4 id="simulation-blueprint-title"><?php echo $ises
                                ? 'Peso exacto por dominio'
                                : 'Exact domain weighting'; ?></h4>
                        </div>
                        <strong>100%</strong>
                    </header>
                    <div class="herb-board__simulation-weights">
                        <?php foreach ($domainblueprint as $domain) { ?>
                            <article>
                                <span><?php echo $ises ? 'Dominio' : 'Domain'; ?> <?php echo (int)$domain['number']; ?></span>
                                <h5><?php echo s($domain['name']); ?></h5>
                                <div aria-label="<?php echo (int)$domain['weight']; ?>%">
                                    <i style="width: <?php echo (int)$domain['weight']; ?>%"></i>
                                </div>
                                <strong><?php echo (int)$domain['weight']; ?> <?php echo $ises ? 'preguntas' : 'questions'; ?> · <?php echo (int)$domain['weight']; ?>%</strong>
                            </article>
                        <?php } ?>
                    </div>
                </section>

                <?php
                $simulationlinearurl = new moodle_url('/blocks/menu/herbology-board.php', [
                    'track' => 'simulation',
                    'exam' => 'linear',
                ] + $languagecontext);
                $simulationadaptiveurl = new moodle_url('/blocks/menu/herbology-board.php', [
                    'track' => 'simulation',
                    'exam' => 'adaptive',
                ] + $languagecontext);
                ?>
                <div class="herb-board__simulation-modes" aria-label="<?php echo $ises ? 'Modalidades de simulación' : 'Simulation modes'; ?>">
                    <a href="<?php echo $simulationlinearurl->out(false); ?>">
                        <span>01</span>
                        <div>
                            <strong><?php echo $ises ? 'Simulación lineal' : 'Linear simulation'; ?></strong>
                            <p><?php echo $ises
                                ? 'Secuencia fija. Permite regresar, marcar preguntas y usar la pantalla de revisión antes de entregar.'
                                : 'Fixed sequence. Return, flag questions, and use the review screen before submitting.'; ?></p>
                        </div>
                        <b><?php echo $ises ? 'Comenzar →' : 'Begin →'; ?></b>
                    </a>
                    <a href="<?php echo $simulationadaptiveurl->out(false); ?>">
                        <span>02</span>
                        <div>
                            <strong><?php echo $ises ? 'Simulación adaptativa' : 'Adaptive simulation'; ?></strong>
                            <p><?php echo $ises
                                ? 'La dificultad se ajusta a tus respuestas. Al avanzar, la respuesta queda bloqueada y no se puede regresar.'
                                : 'Difficulty responds to your answers. Moving forward locks the answer and prevents returning.'; ?></p>
                        </div>
                        <b><?php echo $ises ? 'Comenzar →' : 'Begin →'; ?></b>
                    </a>
                </div>
                <p class="herb-board__simulation-note"><?php echo $ises
                    ? 'El reloj comienza al abrir una modalidad. A los 5 minutos restantes cambia a amarillo y, al llegar a cero, el intento se entrega automáticamente.'
                    : 'The clock begins when you open a mode. It turns yellow with 5 minutes remaining and submits automatically at zero.'; ?></p>
            <?php } else if ($domainshortages) { ?>
                <div class="herb-board__mix-result herb-board__mix-result--shortage">
                    <h3 id="herb-board-simulation-title"><?php echo $ises ? 'La simulación se está preparando' : 'The simulation is being prepared'; ?></h3>
                    <p><?php echo $ises ? 'Faltan preguntas para conservar el peso exacto de los dominios.' : 'More questions are needed to preserve the exact domain weighting.'; ?></p>
                </div>
            <?php } else { ?>
                <form id="herbology-simulation-exam" class="herb-board__simulation-player"
                    data-simulation-exam
                    data-mode="<?php echo s($exammode); ?>"
                    data-duration="9000"
                    data-question-word="<?php echo $ises ? 'Pregunta' : 'Question'; ?>"
                    data-of-word="<?php echo $ises ? 'de' : 'of'; ?>"
                    data-unanswered-word="<?php echo $ises ? 'Sin responder' : 'Unanswered'; ?>"
                    data-answered-word="<?php echo $ises ? 'Respondida' : 'Answered'; ?>"
                    data-flagged-word="<?php echo $ises ? 'Marcada' : 'Flagged'; ?>"
                    data-submit-confirm="<?php echo $ises ? '¿Deseas entregar el examen ahora? Las preguntas sin responder se contarán como incorrectas.' : 'Submit the exam now? Unanswered questions will be counted as incorrect.'; ?>"
                    data-time-expired="<?php echo $ises ? 'El tiempo terminó. El examen se entregó automáticamente.' : 'Time expired. The exam was submitted automatically.'; ?>">
                    <header class="herb-board__simulation-bar">
                        <div>
                            <span><?php echo $ises ? 'EXAMEN DE SIMULACIÓN 1' : 'SIMULATION EXAM 1'; ?></span>
                            <strong><?php echo $ises ? 'Herbología China' : 'Chinese Herbology'; ?> · <?php echo $exammode === 'adaptive'
                                ? ($ises ? 'Adaptativo' : 'Adaptive')
                                : ($ises ? 'Lineal' : 'Linear'); ?></strong>
                        </div>
                        <div class="herb-board__simulation-status">
                            <button type="button" class="herb-board__simulation-clock" data-simulation-clock
                                aria-label="<?php echo $ises ? 'Ocultar o mostrar el tiempo restante' : 'Hide or show time remaining'; ?>">
                                <span><?php echo $ises ? 'Tiempo restante' : 'Time Remaining'; ?></span>
                                <strong data-simulation-time>02:30:00</strong>
                            </button>
                            <div>
                                <span><?php echo $ises ? 'Progreso' : 'Progress'; ?></span>
                                <strong data-simulation-counter>1 <?php echo $ises ? 'de' : 'of'; ?> 100</strong>
                            </div>
                        </div>
                    </header>
                    <div class="herb-board__simulation-toolbar">
                        <button type="button" data-simulation-review <?php echo $exammode === 'adaptive' ? 'hidden' : ''; ?>>
                            <?php echo $ises ? 'Pantalla de revisión' : 'Review Screen'; ?>
                        </button>
                        <button type="button" data-simulation-flag <?php echo $exammode === 'adaptive' ? 'hidden' : ''; ?>>
                            ⚑ <?php echo $ises ? 'Marcar para revisar' : 'Flag for Review'; ?>
                        </button>
                        <?php if ($exammode === 'adaptive') { ?>
                            <span><?php echo $ises ? 'Las respuestas se bloquean al avanzar.' : 'Answers lock when you move forward.'; ?></span>
                        <?php } ?>
                    </div>

                    <div class="herb-board__simulation-questions" data-simulation-questions>
                        <?php foreach ($mixedquestions as $questionnumber => $question) { ?>
                            <?php $domainnumber = 0; $domainname = '';
                            foreach ($domainblueprint as $domain) {
                                if ($domain['id'] === ($question['domainId'] ?? '')) {
                                    $domainnumber = (int)$domain['number'];
                                    $domainname = (string)$domain['name'];
                                    break;
                                }
                            } ?>
                            <fieldset class="herb-board__simulation-question" hidden
                                data-question-index="<?php echo $questionnumber; ?>"
                                data-correct="<?php echo s($question['correctLabel']); ?>"
                                data-domain="<?php echo s($question['domainId'] ?? ''); ?>"
                                data-domain-number="<?php echo $domainnumber; ?>"
                                data-domain-name="<?php echo s($domainname); ?>"
                                data-difficulty="<?php echo s($question['difficultyBand'] ?? 'application'); ?>">
                                <legend>
                                    <span><?php echo $ises ? 'Pregunta' : 'Question'; ?> <b data-simulation-number><?php echo $questionnumber + 1; ?></b></span>
                                    <?php echo s($question['prompt']); ?>
                                </legend>
                                <div class="herb-board__simulation-options">
                                    <?php foreach ($question['options'] as $option) { ?>
                                        <label>
                                            <input type="radio" name="simulation-question-<?php echo $questionnumber + 1; ?>"
                                                value="<?php echo s($option['label']); ?>">
                                            <strong><?php echo s($option['label']); ?></strong>
                                            <span><?php echo s($option['text']); ?></span>
                                        </label>
                                    <?php } ?>
                                </div>
                            </fieldset>
                        <?php } ?>
                    </div>

                    <section class="herb-board__simulation-review" data-simulation-review-panel hidden
                        aria-labelledby="simulation-review-title">
                        <h3 id="simulation-review-title"><?php echo $ises ? 'Revisión del examen' : 'Exam Review'; ?></h3>
                        <p data-simulation-review-summary></p>
                        <div data-simulation-review-grid></div>
                    </section>

                    <section class="herb-board__simulation-result" data-simulation-result hidden aria-live="polite">
                        <span><?php echo $ises ? 'SIMULACIÓN FINALIZADA' : 'SIMULATION COMPLETE'; ?></span>
                        <h3><?php echo $ises ? 'Resultado del intento' : 'Attempt result'; ?></h3>
                        <strong data-simulation-score></strong>
                        <p data-simulation-result-message></p>
                        <div data-simulation-domain-results></div>
                        <a href="<?php echo $simulationurl->out(false); ?>"><?php echo $ises ? 'Configurar otra simulación' : 'Set up another simulation'; ?></a>
                    </section>

                    <footer class="herb-board__simulation-controls" data-simulation-controls>
                        <button type="button" class="herb-board__simulation-end" data-simulation-end><?php echo $ises ? 'Finalizar examen' : 'End Exam'; ?></button>
                        <div>
                            <button type="button" data-simulation-previous <?php echo $exammode === 'adaptive' ? 'hidden' : ''; ?>><?php echo $ises ? 'Anterior' : 'Previous'; ?></button>
                            <button type="button" class="herb-board__simulation-next" data-simulation-next><?php echo $ises ? 'Siguiente' : 'Next'; ?></button>
                        </div>
                    </footer>
                </form>
            <?php } ?>
        </section>
    <?php } else if ($track === 'quiz') { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-quiz-title">
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-quiz-title"><?php echo $ises
                        ? 'Elige un tipo de Quiz'
                        : 'Choose a Quiz type'; ?></h3>
                    <p><?php echo $ises
                        ? 'Selecciona la respuesta correcta y recibe feedback inmediato después de razonar cada pregunta.'
                        : 'Select the correct answer and receive immediate feedback after reasoning through each question.'; ?></p>
                </div>
            </div>
            <div class="herb-board__launches herb-board__launches--quiz">
                <a class="herb-board__launch" href="<?php echo $singleherbpracticeurl->out(false); ?>">
                    <strong><?php echo $ises ? 'Quiz de hierbas individuales' : 'Single Herbs Quiz'; ?></strong>
                    <span><?php echo $ises
                        ? 'Crea un Quiz de 10, 25, 50 o 100 preguntas.'
                        : 'Build a 10, 25, 50, or 100-question Quiz.'; ?></span>
                </a>
                <a class="herb-board__launch" href="<?php echo $formulapracticeurl->out(false); ?>">
                    <strong><?php echo $ises ? 'Quiz de fórmulas' : 'Formula Quiz'; ?></strong>
                    <span><?php echo $ises
                        ? 'Configura el Quiz por banco completo, fórmula o categoría.'
                        : 'Configure the Quiz by full bank, formula, or category.'; ?></span>
                </a>
                <a class="herb-board__launch" href="<?php echo $quizurl->out(false); ?>#quiz-domains">
                    <strong><?php echo $ises ? 'Quiz por dominio' : 'Quiz by Domain'; ?></strong>
                    <span><?php echo $ises
                        ? 'Trabaja uno de los seis dominios de Herbología.'
                        : 'Work through one of the six Herbology Domains.'; ?></span>
                </a>
            </div>

            <h4 class="herb-board__subheading" id="quiz-herb-categories"><?php echo $ises ? 'Quiz de hierbas individuales por categoría de Yang' : 'Single Herbs Quiz by Yang category'; ?></h4>
            <div class="herb-board__topics">
                <?php foreach ($herbcategories as $category) {
                    if ($category['number'] === 1) {
                        continue;
                    }
                    $categoryurl = new moodle_url($singleherbpracticepath, [
                        'category' => $category['number'],
                        'size' => 25,
                    ] + ($fromlibrary ? ['from' => 'library'] : []));
                ?>
                    <a class="herb-board__topic" href="<?php echo $categoryurl->out(false); ?>">
                        <span><?php echo s($category['name']); ?></span>
                    </a>
                <?php } ?>
            </div>

            <h4 class="herb-board__subheading herb-board__subheading--spaced" id="quiz-formula-categories"><?php echo $ises ? 'Quiz de fórmulas por categoría' : 'Formula Quiz by category'; ?></h4>
            <div class="herb-board__topics">
                <?php foreach ($formulacategories as $category) {
                    $categoryurl = new moodle_url($formulapracticepath, [
                        'category' => $category['key'],
                        'size' => 25,
                    ] + ($fromlibrary ? ['from' => 'library'] : []));
                ?>
                    <a class="herb-board__topic" href="<?php echo $categoryurl->out(false); ?>">
                        <span><?php echo s($category['name']); ?></span>
                    </a>
                <?php } ?>
                <?php $modificationsurl = new moodle_url($formulapracticepath, [
                    'category' => 'modifications',
                    'size' => 25,
                ] + ($fromlibrary ? ['from' => 'library'] : [])); ?>
                <a class="herb-board__topic" href="<?php echo $modificationsurl->out(false); ?>">
                    <span><?php echo $ises ? 'Modificaciones de fórmulas' : 'Formula Modifications'; ?></span>
                </a>
            </div>

            <section class="herb-board__clinical-cases-block" id="quiz-clinical-cases"
                aria-labelledby="quiz-clinical-cases-title">
                <header>
                    <div>
                        <h4 class="herb-board__subheading herb-board__subheading--spaced"
                            id="quiz-clinical-cases-title"><?php echo $ises
                                ? 'Quiz de casos clínicos'
                                : 'Clinical Cases Quiz'; ?></h4>
                        <p><?php echo $ises
                            ? 'Aplica la diferenciación de fórmulas a los casos clínicos del Handbook of Oriental Medicine.'
                            : 'Apply formula differentiation to cases from the Handbook of Oriental Medicine.'; ?></p>
                    </div>
                </header>
                <?php if ($clinicalcaseexams) { ?>
                    <div class="herb-board__topics herb-board__topics--clinical-cases">
                        <?php foreach ($clinicalcaseexams as $clinicalcaseexam) {
                            $clinicalcaselabel = preg_replace(
                                '/^Handbook(?:-| )of(?:-| )Oriental(?:-| )Medicine[. ]*/i',
                                '',
                                (string)$clinicalcaseexam['title']
                            );
                            $clinicalcaselabel = preg_replace('/\s*\(\d+\s+Questions?\)\s*$/i', '', $clinicalcaselabel);
                            $clinicalcaseexamurl = new moodle_url('/blocks/menu/herbology-board.php', [
                                'track' => 'clinicalcases',
                                'caseexam' => (string)$clinicalcaseexam['key'],
                            ] + $languagecontext);
                        ?>
                            <a class="herb-board__topic" href="<?php echo $clinicalcaseexamurl->out(false); ?>">
                                <span><?php echo s($clinicalcaselabel); ?></span>
                            </a>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p class="herb-board__clinical-cases-pending"><?php echo $ises
                        ? 'El banco se habilitará al completar la extracción segura desde Moodle.'
                        : 'This bank will appear after the safe Moodle extraction completes.'; ?></p>
                <?php } ?>
            </section>

            <h4 class="herb-board__subheading herb-board__subheading--spaced" id="quiz-domains">
                <?php echo $ises ? 'Quiz por dominio' : 'Quiz by Domain'; ?>
            </h4>
            <div class="herb-board__domains">
                <?php foreach ($domainblueprint as $domain) {
                    $domainurl = new moodle_url('/blocks/menu/herbology-board.php', [
                        'track' => 'domains',
                        'domain' => $domain['id'],
                    ] + $languagecontext);
                ?>
                    <a class="herb-board__domain" data-domain-id="<?php echo s($domain['id']); ?>"
                        href="<?php echo $domainurl->out(false); ?>">
                        <span class="herb-board__domain-number"><?php echo $ises ? 'Dominio' : 'Domain'; ?> <?php echo (int)$domain['number']; ?></span>
                        <h4><?php echo s($domain['name']); ?></h4>
                        <p><?php echo s($domain['focus']); ?></p>
                        <span class="herb-board__domain-status">
                            <?php echo (int)$domain['questionCount']; ?> <?php echo $ises ? 'preguntas de Quiz' : 'Quiz questions'; ?> →
                        </span>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } else if ($track === 'clinicalcases') { ?>
        <section class="herb-board__panel herb-board__clinical-quiz" aria-labelledby="clinical-case-quiz-title">
            <a class="herb-board__domain-back" href="<?php echo $quizurl->out(false); ?>#quiz-clinical-cases">
                <?php echo $ises ? '← Volver al Quiz' : '← Back to Quiz'; ?>
            </a>
            <?php if ($selectedclinicalexam === null || !$clinicalquestions) { ?>
                <div class="herb-board__notice">
                    <h3 id="clinical-case-quiz-title"><?php echo $ises
                        ? 'El cuestionario de casos clínicos se está preparando'
                        : 'The Clinical Cases Quiz is being prepared'; ?></h3>
                    <p><?php echo $ises
                        ? 'La extracción todavía no ha producido preguntas utilizables.'
                        : 'The source extraction has not produced usable questions yet.'; ?></p>
                </div>
            <?php } else { ?>
                <?php
                $clinicaltitle = preg_replace(
                    '/^Handbook(?:-| )of(?:-| )Oriental(?:-| )Medicine[. ]*/i',
                    '',
                    (string)$selectedclinicalexam['title']
                );
                ?>
                <div class="herb-board__panel-heading">
                    <div>
                        <span class="herb-board__domain-kicker"><?php echo $ises
                            ? 'HANDBOOK OF ORIENTAL MEDICINE · CASOS CLÍNICOS'
                            : 'HANDBOOK OF ORIENTAL MEDICINE · CLINICAL CASES'; ?></span>
                        <h3 id="clinical-case-quiz-title"><?php echo s($clinicaltitle); ?></h3>
                        <p><?php echo $ises
                            ? 'Selecciona una respuesta y revisa la corrección antes de continuar.'
                            : 'Select one answer and receive feedback before continuing.'; ?></p>
                    </div>
                </div>

                <form class="herb-board__clinical-player" data-clinical-cases-quiz
                    data-question-word="<?php echo $ises ? 'Pregunta' : 'Question'; ?>"
                    data-of-word="<?php echo $ises ? 'de' : 'of'; ?>"
                    data-correct-word="<?php echo $ises ? 'Correcta' : 'Correct'; ?>"
                    data-incorrect-word="<?php echo $ises ? 'Incorrecta' : 'Incorrect'; ?>"
                    data-next-word="<?php echo $ises ? 'Siguiente →' : 'Next →'; ?>"
                    data-results-word="<?php echo $ises ? 'Ver resultado' : 'View result'; ?>">
                    <div class="herb-board__exam-progress" aria-live="polite">
                        <div class="herb-board__exam-progress-summary">
                            <strong data-clinical-progress></strong>
                            <strong class="herb-board__exam-score" data-clinical-score></strong>
                        </div>
                        <div class="herb-board__exam-progress-bar"><span data-clinical-progress-bar></span></div>
                    </div>

                    <?php
                    $cleanclinicaltext = static function ($value) {
                        $decoded = (string)$value;
                        for ($decodepass = 0; $decodepass < 3; $decodepass++) {
                            $unescaped = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            if ($unescaped === $decoded) {
                                break;
                            }
                            $decoded = $unescaped;
                        }
                        return preg_replace('/\s*&\s*/u', ' and ', $decoded);
                    };
                    ?>
                    <?php foreach ($clinicalquestions as $clinicalindex => $clinicalquestion) { ?>
                        <?php
                        $correctclinicaloption = '';
                        foreach ($clinicalquestion['options'] as $clinicaloption) {
                            if (($clinicaloption['label'] ?? '') === $clinicalquestion['correctLabel']) {
                                $correctclinicaloption = $cleanclinicaltext($clinicaloption['text'] ?? '');
                                break;
                            }
                        }
                        $clinicalprompt = $cleanclinicaltext($clinicalquestion['prompt'] ?? '');
                        $clinicalfeedback = $cleanclinicaltext($clinicalquestion['feedback'] ?? '');
                        $clinicalfeedback = preg_replace('/\s+/u', ' ', trim($clinicalfeedback));
                        $clinicaldiagnosis = is_array($clinicalquestion['diagnosis'] ?? null)
                            ? $clinicalquestion['diagnosis']
                            : [];
                        $clinicalpattern = $cleanclinicaltext($clinicaldiagnosis[
                            $ises && !empty($clinicaldiagnosis['patternEs']) ? 'patternEs' : 'pattern'
                        ] ?? '');
                        $clinicalclue = $cleanclinicaltext($clinicaldiagnosis[
                            $ises && !empty($clinicaldiagnosis['clueEs']) ? 'clueEs' : 'clue'
                        ] ?? '');
                        $clinicalaction = $cleanclinicaltext($clinicaldiagnosis[
                            $ises && !empty($clinicaldiagnosis['actionEs']) ? 'actionEs' : 'action'
                        ] ?? '');
                        $clinicalreasoning = $cleanclinicaltext($clinicaldiagnosis[
                            $ises && !empty($clinicaldiagnosis['reasoningEs']) ? 'reasoningEs' : 'reasoning'
                        ] ?? '');
                        $clinicaldifferential = $cleanclinicaltext($clinicaldiagnosis[
                            $ises && !empty($clinicaldiagnosis['differentialEs']) ? 'differentialEs' : 'differential'
                        ] ?? '');
                        ?>
                        <fieldset class="herb-board__exam-question herb-board__clinical-question" hidden
                            data-clinical-question
                            data-correct="<?php echo s($clinicalquestion['correctLabel']); ?>">
                            <legend>
                                <span><?php echo $ises ? 'Pregunta' : 'Question'; ?> <?php echo $clinicalindex + 1; ?></span>
                                <?php echo s($clinicalprompt); ?>
                            </legend>
                            <div class="herb-board__exam-options">
                                <?php foreach ($clinicalquestion['options'] as $clinicaloption) { ?>
                                    <?php
                                    $clinicaloptiontext = $cleanclinicaltext($clinicaloption['text'] ?? '');
                                    ?>
                                    <label>
                                        <input type="radio" name="clinical-question-<?php echo $clinicalindex + 1; ?>"
                                            value="<?php echo s($clinicaloption['label'] ?? ''); ?>">
                                        <strong><?php echo s($clinicaloption['label'] ?? ''); ?>.</strong>
                                        <span><?php echo s($clinicaloptiontext); ?></span>
                                    </label>
                                <?php } ?>
                            </div>
                            <div class="herb-board__exam-feedback" data-clinical-feedback hidden>
                                <span class="herb-board__exam-verdict" data-clinical-verdict></span>
                                <strong><?php echo $ises ? 'Respuesta correcta' : 'Correct answer'; ?>:
                                    <?php echo s($clinicalquestion['correctLabel']); ?>. <?php echo s($correctclinicaloption); ?></strong>
                                <?php if ($clinicaldiagnosis) { ?>
                                    <div class="herb-board__clinical-diagnosis">
                                        <span><?php echo $ises ? 'Razonamiento diagnóstico' : 'Diagnostic reasoning'; ?></span>
                                        <dl>
                                            <?php if ($clinicalpattern !== '') { ?>
                                                <div>
                                                    <dt><?php echo $ises ? 'Patrón' : 'Pattern'; ?></dt>
                                                    <dd><?php echo s($clinicalpattern); ?></dd>
                                                </div>
                                            <?php } ?>
                                            <?php if ($clinicalclue !== '') { ?>
                                                <div>
                                                    <dt><?php echo $ises ? 'Signos decisivos' : 'Decisive signs'; ?></dt>
                                                    <dd><?php echo s($clinicalclue); ?></dd>
                                                </div>
                                            <?php } ?>
                                            <?php if ($clinicalreasoning !== '') { ?>
                                                <div>
                                                    <dt><?php echo $ises ? 'Interpretación' : 'Interpretation'; ?></dt>
                                                    <dd><?php echo s($clinicalreasoning); ?></dd>
                                                </div>
                                            <?php } ?>
                                            <?php if ($clinicalaction !== '') { ?>
                                                <div>
                                                    <dt><?php echo $ises ? 'Correspondencia terapéutica' : 'Therapeutic match'; ?></dt>
                                                    <dd><?php echo s($clinicalaction); ?></dd>
                                                </div>
                                            <?php } ?>
                                            <?php if ($clinicaldifferential !== '') { ?>
                                                <div>
                                                    <dt><?php echo $ises ? 'Diferencial' : 'Differential'; ?></dt>
                                                    <dd><?php echo s($clinicaldifferential); ?></dd>
                                                </div>
                                            <?php } ?>
                                        </dl>
                                    </div>
                                <?php } ?>
                                <?php if ($clinicalfeedback !== '') { ?>
                                    <div class="herb-board__clinical-explanation">
                                        <span><?php echo $ises ? 'Explicación de la fuente' : 'Source explanation'; ?></span>
                                        <p><?php echo s($clinicalfeedback); ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                        </fieldset>
                    <?php } ?>

                    <div class="herb-board__exam-submit">
                        <button type="button" data-clinical-next><?php echo $ises ? 'Siguiente →' : 'Next →'; ?></button>
                        <output data-clinical-result hidden></output>
                        <a class="herb-board__exam-history-link" href="<?php echo $quizurl->out(false); ?>#quiz-clinical-cases" hidden
                            data-clinical-return><?php echo $ises ? 'Elegir otro examen' : 'Choose another exam'; ?></a>
                    </div>
                </form>
            <?php } ?>
        </section>
    <?php } else if ($track === 'formulas') { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-formulas-title">
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-formulas-title"><?php echo $ises
                        ? 'Estudia fórmulas'
                        : 'Study Formulas'; ?></h3>
                    <p><?php echo $ises
                        ? 'Lee preguntas con respuestas y feedback visibles, o utiliza flashcards de memoria.'
                        : 'Read questions with visible answers and feedback, or use recall flashcards.'; ?></p>
                </div>
            </div>
            <div class="herb-board__summary herb-board__summary--panel" aria-label="<?php echo $ises
                ? 'Totales del banco de fórmulas' : 'Formula bank totals'; ?>">
                <span><strong><?php echo $formulatotals['questions']; ?></strong> <?php echo $ises
                    ? 'preguntas verificadas' : 'verified questions'; ?></span>
                <span><strong><?php echo $formulatotals['formulas']; ?></strong> <?php echo $ises
                    ? 'fórmulas para estudiar' : 'formulas to study'; ?></span>
                <span><strong><?php echo $formulatotals['categories']; ?></strong> <?php echo $ises
                    ? 'categorías oficiales' : 'official categories'; ?></span>
            </div>
            <div class="herb-board__launches herb-board__launches--single">
                <a class="herb-board__launch" href="<?php echo $formulaquestionbankurl->out(false); ?>">
                    <strong><?php echo $ises
                        ? 'Preguntas de estudio y flashcards'
                        : 'Study Questions &amp; Flashcards'; ?></strong>
                    <span><?php echo $ises
                        ? 'Repasa las preguntas con la respuesta correcta y el feedback siempre visibles.'
                        : 'Review questions with the correct answer and feedback always visible.'; ?></span>
                </a>
            </div>
            <h4 class="herb-board__subheading"><?php echo $ises
                ? 'Estudiar por categoría de fórmulas'
                : 'Study by formula category'; ?></h4>
            <div class="herb-board__topics">
                <?php foreach ($formulacategories as $category) {
                    $categoryurl = new moodle_url($formulaquestionbankpath, [
                        'category' => $category['key'],
                    ] + ($fromlibrary ? ['from' => 'library'] : []));
                ?>
                    <a class="herb-board__topic" href="<?php echo $categoryurl->out(false); ?>">
                        <span><?php echo s($category['name']); ?></span>
                    </a>
                <?php } ?>
                <?php $modificationstudyurl = new moodle_url($formulaquestionbankpath, [
                    'category' => 'modifications',
                ] + ($fromlibrary ? ['from' => 'library'] : [])); ?>
                <a class="herb-board__topic" href="<?php echo $modificationstudyurl->out(false); ?>">
                    <span><?php echo $ises ? 'Modificaciones de fórmulas' : 'Formula Modifications'; ?></span>
                </a>
            </div>
        </section>
    <?php } else if ($selecteddomain === null) { ?>
        <section class="herb-board__panel"
            <?php echo $track === 'exams' && $mixdomains
                ? 'aria-label="' . ($ises ? 'Examen de Herbología' : 'Herbology exam') . '"'
                : 'aria-labelledby="herb-board-workspace-title"'; ?>>
            <?php if ($track === 'domains' || !$mixdomains) { ?>
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-workspace-title"><?php echo $track === 'exams'
                        ? ($ises ? 'Elige una modalidad' : 'Choose an exam mode')
                        : ($ises ? 'Quiz por dominio' : 'Quiz by Domain'); ?></h3>
                    <p><?php echo $track === 'exams'
                        ? ($ises
                            ? 'Elige el examen lineal o adaptativo de 100 preguntas y revisa tu historial.'
                            : 'Choose the 100-question linear or adaptive exam and review your history.')
                        : ($ises
                            ? 'Elige un dominio para completar un Quiz enfocado.'
                            : 'Choose one Domain for a focused Quiz.'); ?></p>
                </div>
            </div>
            <div class="herb-board__domain-summary" aria-label="Domain blueprint summary">
                <?php if ($track === 'exams') { ?>
                    <span><strong>2</strong> <?php echo $ises ? 'modalidades de examen' : 'exam modes'; ?></span>
                    <span><strong>100</strong> <?php echo $ises ? 'preguntas por examen' : 'questions per exam'; ?></span>
                <?php } else { ?>
                    <span><strong>6</strong> <?php echo $ises ? 'dominios de estudio' : 'study Domains'; ?></span>
                <?php } ?>
            </div>
            <?php } ?>
            <?php
            $linearurl = new moodle_url('/blocks/menu/herbology-board.php', [
                'track' => 'exams',
                'exam' => 'linear',
            ] + $languagecontext);
            $adaptiveurl = new moodle_url('/blocks/menu/herbology-board.php', [
                'track' => 'exams',
                'exam' => 'adaptive',
            ] + $languagecontext);
            ?>
            <?php if ($track === 'exams' && !$mixdomains) { ?>
                <div class="herb-board__exam-models" aria-label="<?php echo $ises ? 'Modalidades de examen' : 'Exam modes'; ?>">
                    <a class="herb-board__exam-model" href="<?php echo $linearurl->out(false); ?>">
                        <strong><?php echo $ises ? 'Examen lineal' : 'Linear exam'; ?></strong>
                        <span><?php echo $ises
                            ? '100 preguntas en una secuencia fija con feedback tipo mock después de cada respuesta.'
                            : '100 questions in a fixed sequence with mock-style feedback after every answer.'; ?></span>
                    </a>
                    <a class="herb-board__exam-model herb-board__exam-model--adaptive" href="<?php echo $adaptiveurl->out(false); ?>">
                        <strong><?php echo $ises ? 'Examen adaptativo' : 'Adaptive exam'; ?></strong>
                        <span><?php echo $ises
                            ? 'Ajusta el orden y la dificultad según cada respuesta, sin cambiar la composición final.'
                            : 'Adjusts order and difficulty after each answer without changing the final composition.'; ?></span>
                    </a>
                </div>
                <p class="herb-board__exam-minimum"><?php echo $ises
                    ? '100 preguntas es el formato más corto que conserva exactamente la distribución establecida.'
                    : '100 questions is the shortest form that preserves the current distribution exactly.'; ?></p>
            <?php } ?>
            <?php if ($track === 'domains') { ?>
            <div class="herb-board__domains">
                <?php foreach ($domainblueprint as $domain) {
                    $domainurl = new moodle_url('/blocks/menu/herbology-board.php', [
                        'track' => 'domains',
                        'domain' => $domain['id'],
                    ] + $languagecontext);
                ?>
                    <a class="herb-board__domain" data-domain-id="<?php echo s($domain['id']); ?>"
                        href="<?php echo $domainurl->out(false); ?>">
                        <span class="herb-board__domain-number"><?php echo $ises ? 'Dominio' : 'Domain'; ?> <?php echo (int)$domain['number']; ?></span>
                        <h4><?php echo s($domain['name']); ?></h4>
                        <p><?php echo s($domain['focus']); ?></p>
                        <span class="herb-board__domain-status">
                            <?php echo (int)$domain['questionCount']; ?> <?php echo $ises ? 'preguntas de Quiz' : 'Quiz questions'; ?> →
                        </span>
                    </a>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($track === 'exams' && !$mixdomains) { ?>
                <section class="herb-board__exam-history" id="herb-board-exam-history"
                    aria-labelledby="herb-board-exam-history-title">
                    <header>
                        <div>
                            <span><?php echo $ises ? 'PROGRESO PERSONAL' : 'PERSONAL PROGRESS'; ?></span>
                            <h4 id="herb-board-exam-history-title">
                                <?php echo $ises ? 'Historial de exámenes' : 'Exam history'; ?>
                            </h4>
                        </div>
                        <small><?php echo $ises ? 'Últimos 10 intentos completados' : 'Last 10 completed attempts'; ?></small>
                    </header>
                    <?php if (!$examhistory) { ?>
                        <p class="herb-board__exam-history-empty">
                            <?php echo $ises
                                ? 'Todavía no has completado un examen de 100 preguntas.'
                                : 'You have not completed a 100-question exam yet.'; ?>
                        </p>
                    <?php } else { ?>
                        <div class="herb-board__exam-history-list" role="list">
                            <?php foreach ($examhistory as $attempt) {
                                $attemptpercentage = $attempt->total > 0
                                    ? (int) round(($attempt->score / $attempt->total) * 100)
                                    : 0;
                                $attemptminutes = intdiv((int) $attempt->duration, 60);
                                $attemptseconds = (int) $attempt->duration % 60;
                                $attemptduration = $attemptminutes > 0
                                    ? $attemptminutes . ' min ' . $attemptseconds . ' s'
                                    : $attemptseconds . ' s';
                                $attempttype = $attempt->examtype === 'adaptive'
                                    ? ($ises ? 'Adaptativo' : 'Adaptive')
                                    : ($ises ? 'Lineal' : 'Linear');
                            ?>
                                <article role="listitem">
                                    <div>
                                        <span><?php echo $ises ? 'Fecha' : 'Date'; ?></span>
                                        <strong><?php echo s(userdate((int) $attempt->timecompleted, get_string('strftimedatetimeshort'))); ?></strong>
                                    </div>
                                    <div>
                                        <span><?php echo $ises ? 'Modalidad' : 'Mode'; ?></span>
                                        <strong><?php echo s($attempttype); ?></strong>
                                    </div>
                                    <div>
                                        <span><?php echo $ises ? 'Resultado' : 'Result'; ?></span>
                                        <strong><?php echo (int) $attempt->score; ?>/<?php echo (int) $attempt->total; ?> · <?php echo $attemptpercentage; ?>%</strong>
                                    </div>
                                    <div>
                                        <span><?php echo $ises ? 'Duración' : 'Duration'; ?></span>
                                        <strong><?php echo s($attemptduration); ?></strong>
                                    </div>
                                </article>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </section>
            <?php } ?>
            <?php if ($mixdomains && $domainshortages) { ?>
                <section class="herb-board__mix-result herb-board__mix-result--shortage" aria-labelledby="domain-shortage-title">
                    <h4 id="domain-shortage-title">The practice exam is being prepared</h4>
                    <p>Please return shortly while the remaining questions are connected.</p>
                </section>
            <?php } else if ($mixdomains && count($mixedquestions) === 100) { ?>
                <form id="herbology-domain-exam" class="herb-board__exam"
                    data-exam-mode="<?php echo s($exammode); ?>"
                    data-exam-language="<?php echo $ises ? 'es' : 'en'; ?>"
                    data-history-url="<?php echo s((new moodle_url('/blocks/menu/herbology-exam-history.php'))->out(false)); ?>"
                    data-sesskey="<?php echo s(sesskey()); ?>"
                    data-history-saving="<?php echo $ises ? 'Guardando en el historial…' : 'Saving to exam history…'; ?>"
                    data-history-saved="<?php echo $ises ? 'Resultado guardado en el historial.' : 'Result saved to exam history.'; ?>"
                    data-history-error="<?php echo $ises ? 'No se pudo guardar el resultado.' : 'The result could not be saved.'; ?>"
                    data-score-connector="<?php echo $ises ? 'de' : 'of'; ?>"
                    data-correct-word="<?php echo $ises ? 'correctas' : 'correct'; ?>"
                    data-score-word="<?php echo $ises ? 'Aciertos' : 'Accuracy'; ?>"
                    data-question-word="<?php echo $ises ? 'Pregunta' : 'Question'; ?>"
                    data-correct-feedback="<?php echo $ises ? 'Correcto' : 'Correct'; ?>"
                    data-incorrect-feedback="<?php echo $ises ? 'Incorrecto' : 'Incorrect'; ?>"
                    data-previous-word="<?php echo $ises ? '← Anterior' : '← Previous'; ?>"
                    data-next-word="<?php echo $ises ? 'Siguiente →' : 'Next →'; ?>"
                    data-pending-word="<?php echo $ises ? 'Ir a pendiente →' : 'Review unanswered →'; ?>"
                    data-next-pending-word="<?php echo $ises ? 'Siguiente pendiente →' : 'Next unanswered →'; ?>"
                    data-pending-label="<?php echo $ises ? 'Pendiente' : 'Unanswered'; ?>"
                    data-results-word="<?php echo $ises ? 'Ver resultado' : 'View result'; ?>"
                    data-completed-word="<?php echo $ises ? 'Examen completado' : 'Exam completed'; ?>">
                    <div class="herb-board__exam-heading">
                        <div>
                            <span><?php echo $exammode === 'adaptive'
                                ? ($ises ? 'Examen adaptativo' : 'Adaptive exam')
                                : ($ises ? 'Examen lineal' : 'Linear exam'); ?></span>
                            <h4><?php echo $ises ? 'Herbología China · 100 preguntas' : 'Chinese Herbology · 100 Questions'; ?></h4>
                        </div>
                        <p><?php echo $ises
                            ? 'Responde o salta una pregunta a la vez. Después de cada respuesta recibirás feedback tipo mock.'
                            : 'Answer or skip one question at a time. Mock-style feedback appears after every answer.'; ?></p>
                    </div>
                    <div class="herb-board__exam-progress" aria-live="polite">
                        <div class="herb-board__exam-progress-summary">
                            <strong class="herb-board__exam-progress-text"></strong>
                            <strong class="herb-board__exam-score"></strong>
                        </div>
                        <div class="herb-board__exam-progress-bar"><span></span></div>
                    </div>
                    <?php foreach ($mixedquestions as $questionnumber => $question) { ?>
                        <?php
                        $correctoptiontext = '';
                        foreach ($question['options'] as $questionoption) {
                            if (($questionoption['label'] ?? '') === ($question['correctLabel'] ?? '')) {
                                $correctoptiontext = (string)($questionoption['text'] ?? '');
                                break;
                            }
                        }
                        $sourcecard = $question['sourceCard'] ?? [];
                        $sourcecardurl = (string)($sourcecard[$ises ? 'urlEs' : 'url'] ?? '');
                        $sourcecardlabel = (string)($sourcecard[$ises ? 'labelEs' : 'label']
                            ?? ($ises ? 'Abrir tarjeta fuente' : 'Open source card'));
                        $sourcecardquery = (string)($sourcecard['query'] ?? '');
                        $questionlabel = ($ises ? 'Pregunta ' : 'Question ') . ($questionnumber + 1);
                        $feedbacknomenclature = [];
                        foreach ($question['nomenclature']['feedback']['herbs'] ?? [] as $herbidentity) {
                            $pinyin = trim((string)($herbidentity['pinyin'] ?? ''));
                            $pharmaceutical = trim((string)($herbidentity['pharmaceuticalName'] ?? ''));
                            if ($pinyin !== '' && $pharmaceutical !== '') {
                                $feedbacknomenclature[] = $pinyin . ' — ' . $pharmaceutical;
                            }
                        }
                        foreach ($question['nomenclature']['feedback']['formulas'] ?? [] as $formulaidentity) {
                            $pinyin = trim((string)($formulaidentity['pinyin'] ?? ''));
                            $englishname = trim((string)($formulaidentity['englishName'] ?? ''));
                            if ($pinyin !== '' && $englishname !== '') {
                                $feedbacknomenclature[] = $pinyin . ' — ' . $englishname;
                            }
                        }
                        $feedbacknomenclature = array_values(array_unique($feedbacknomenclature));
                        ?>
                        <fieldset class="herb-board__exam-question" id="herbology-exam-question-<?php echo $questionnumber + 1; ?>"
                            data-correct="<?php echo s($question['correctLabel']); ?>"
                            data-domain="<?php echo s($question['domainId'] ?? ''); ?>"
                            data-difficulty="<?php echo s($question['difficultyBand'] ?? 'application'); ?>">
                            <legend>
                                <span class="herb-board__exam-question-number"><?php echo $ises ? 'Pregunta' : 'Question'; ?> <?php echo $questionnumber + 1; ?></span>
                                <?php echo s($question['prompt']); ?>
                            </legend>
                            <div class="herb-board__exam-options">
                                <?php foreach ($question['options'] as $option) { ?>
                                    <label>
                                        <input type="radio" name="question-<?php echo $questionnumber + 1; ?>"
                                            value="<?php echo s($option['label']); ?>">
                                        <strong><?php echo s($option['label']); ?>.</strong>
                                        <span><?php echo s($option['text']); ?></span>
                                    </label>
                                <?php } ?>
                            </div>
                            <div class="herb-board__exam-feedback" hidden>
                                <span class="herb-board__exam-verdict"></span>
                                <strong><?php echo $ises ? 'Respuesta correcta' : 'Correct answer'; ?>:
                                    <?php echo s($question['correctLabel']); ?>. <?php echo s($correctoptiontext); ?></strong>
                                <p><?php echo s($question['explanation']); ?></p>
                                <?php if ($feedbacknomenclature) { ?>
                                    <p class="herb-board__exam-nomenclature">
                                        <strong><?php echo $ises ? 'Nomenclatura Board' : 'Board nomenclature'; ?>:</strong>
                                        <?php echo s(implode(' · ', $feedbacknomenclature)); ?>
                                    </p>
                                <?php } ?>
                                <a class="herb-board__exam-source-link" href="<?php echo s($sourcecardurl); ?>"
                                    data-exam-source-link
                                    data-source-title="<?php echo s($sourcecardlabel); ?>"
                                    data-source-query="<?php echo s($sourcecardquery); ?>"
                                    data-question-label="<?php echo s($questionlabel); ?>">
                                    <?php echo s($sourcecardlabel); ?> →
                                </a>
                            </div>
                        </fieldset>
                    <?php } ?>
                    <div class="herb-board__exam-submit">
                        <div class="herb-board__exam-navigation" role="group"
                            aria-label="<?php echo $ises ? 'Navegación entre preguntas' : 'Question navigation'; ?>">
                            <button class="herb-board__exam-previous" type="button">
                                <?php echo $ises ? '← Anterior' : '← Previous'; ?>
                            </button>
                            <button class="herb-board__exam-next" type="button">
                                <?php echo $ises ? 'Siguiente →' : 'Next →'; ?>
                            </button>
                        </div>
                        <output id="herbology-domain-exam-result" hidden></output>
                        <span class="herb-board__exam-save-status" role="status" hidden></span>
                        <a class="herb-board__exam-history-link" href="<?php echo s($examhistoryurl); ?>" hidden>
                            <?php echo $ises ? 'Ver historial' : 'View history'; ?>
                        </a>
                        <button class="herb-board__exam-restart" type="button" hidden>
                            <?php echo $ises ? 'Reiniciar examen' : 'Restart exam'; ?>
                        </button>
                    </div>
                </form>
                <div class="herb-board__source-viewer" data-exam-source-viewer hidden
                    data-return-label="<?php echo $ises ? '← Volver a la' : '← Back to'; ?>">
                    <button class="herb-board__source-backdrop" type="button" data-exam-source-close
                        aria-label="<?php echo $ises ? 'Volver al examen' : 'Return to exam'; ?>"></button>
                    <section class="herb-board__source-window" role="dialog" aria-modal="true"
                        aria-labelledby="herb-board-source-title">
                        <header>
                            <div>
                                <span><?php echo $ises ? 'Fuente del feedback' : 'Feedback source'; ?></span>
                                <h4 id="herb-board-source-title" data-exam-source-title></h4>
                            </div>
                            <button type="button" data-exam-source-return data-exam-source-close>
                                <?php echo $ises ? '← Volver a la pregunta' : '← Back to question'; ?>
                            </button>
                        </header>
                        <iframe title="<?php echo $ises ? 'Tarjeta fuente de la respuesta' : 'Answer source card'; ?>"
                            data-exam-source-frame src="about:blank"></iframe>
                    </section>
                </div>
            <?php } ?>
        </section>
    <?php } else { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-domain-bank-title">
            <a class="herb-board__domain-back" href="<?php echo $quizurl->out(false); ?>#quiz-domains"><?php echo $ises ? '← Volver a Quiz' : '← Back to Quiz'; ?></a>
            <div class="herb-board__panel-heading herb-board__panel-heading--domain-bank">
                <div>
                    <span class="herb-board__domain-kicker"><?php echo $ises ? 'Dominio' : 'Domain'; ?> <?php echo (int)$selecteddomain['number']; ?> · <?php echo $ises ? 'Práctica enfocada' : 'Focused practice'; ?></span>
                    <h3 id="herb-board-domain-bank-title"><?php echo s($selecteddomain['name']); ?></h3>
                    <p><?php echo s($selecteddomain['focus']); ?></p>
                </div>
            </div>
            <div class="herb-board__domain-summary" aria-label="Selected Domain bank totals">
                <span><?php echo $ises ? 'Preguntas' : 'Questions'; ?> <strong><?php echo (int)$selecteddomainstart; ?>–<?php echo (int)$selecteddomainend; ?></strong>
                    <?php echo $ises ? 'de' : 'of'; ?> <strong><?php echo (int)$selecteddomain['questionCount']; ?></strong></span>
            </div>
            <?php if (!$selecteddomain['questions']) { ?>
                <div class="herb-board__notice">
                    <?php echo $ises
                        ? 'Las preguntas del Quiz para este tema están en preparación.'
                        : 'Quiz questions for this subject are being prepared.'; ?>
                </div>
            <?php } else { ?>
                <?php if ($selecteddomaintotalpages > 1) { ?>
                    <div class="herb-board__domain-paging herb-board__domain-paging--top">
                        <strong><?php echo $ises ? 'Página' : 'Page'; ?> <?php echo $domainpage + 1; ?> <?php echo $ises ? 'de' : 'of'; ?> <?php echo $selecteddomaintotalpages; ?></strong>
                        <nav class="herb-board__domain-page-links" aria-label="<?php echo $ises ? 'Páginas de preguntas' : 'Question pages'; ?>">
                            <?php if ($domainpage > 0) { ?>
                                <a class="herb-board__domain-page-link herb-board__domain-page-link--direction"
                                    href="<?php echo $domainpageurls[$domainpage - 1]->out(false); ?>">← <?php echo $ises ? 'Anterior' : 'Previous'; ?></a>
                            <?php } ?>
                            <?php foreach ($domainpageurls as $pageindex => $domainpageurl) { ?>
                                <a class="herb-board__domain-page-link<?php echo $pageindex === $domainpage ? ' is-current' : ''; ?>"
                                    href="<?php echo $domainpageurl->out(false); ?>"
                                    <?php echo $pageindex === $domainpage ? 'aria-current="page"' : ''; ?>>
                                    <?php echo $pageindex + 1; ?>
                                </a>
                            <?php } ?>
                            <?php if ($domainpage + 1 < $selecteddomaintotalpages) { ?>
                                <a class="herb-board__domain-page-link herb-board__domain-page-link--direction"
                                    href="<?php echo $domainpageurls[$domainpage + 1]->out(false); ?>"><?php echo $ises ? 'Siguiente' : 'Next'; ?> →</a>
                            <?php } ?>
                        </nav>
                    </div>
                <?php } ?>
                <div class="herb-board__domain-questions">
                    <?php foreach ($selecteddomainpagequestions as $question) { ?>
                        <article class="herb-board__domain-question">
                            <span><?php echo s($question['questionId'] ?? 'Unnumbered item'); ?></span>
                            <h4><?php echo s($question['prompt'] ?? 'Question awaiting prompt'); ?></h4>
                            <?php if ($canmanagedomains) { ?>
                                <details>
                                    <summary><?php echo $ises ? 'Detalles de la pregunta' : 'Question details'; ?></summary>
                                    <ol type="A">
                                        <?php foreach ($question['options'] ?? [] as $option) { ?>
                                            <li><?php echo s($option['text'] ?? ''); ?></li>
                                        <?php } ?>
                                    </ol>
                                    <p><strong><?php echo $ises ? 'Correcta' : 'Correct'; ?>:</strong> <?php echo s($question['correctLabel'] ?? ''); ?></p>
                                    <p><strong><?php echo $ises ? 'Explicación' : 'Explanation'; ?>:</strong> <?php echo s($question['explanation'] ?? ''); ?></p>
                                    <p><strong><?php echo $ises ? 'Fuente' : 'Source'; ?>:</strong> <?php echo s($question['source']['title'] ?? ''); ?> ·
                                        <?php echo s($question['source']['locator'] ?? ''); ?></p>
                                </details>
                            <?php } ?>
                        </article>
                    <?php } ?>
                </div>
                <?php if ($selecteddomaintotalpages > 1) { ?>
                    <div class="herb-board__domain-paging herb-board__domain-paging--bottom">
                        <strong><?php echo $ises ? 'Página' : 'Page'; ?> <?php echo $domainpage + 1; ?> <?php echo $ises ? 'de' : 'of'; ?> <?php echo $selecteddomaintotalpages; ?></strong>
                        <nav class="herb-board__domain-page-links" aria-label="<?php echo $ises ? 'Páginas de preguntas' : 'Question pages'; ?>">
                            <?php if ($domainpage > 0) { ?>
                                <a class="herb-board__domain-page-link herb-board__domain-page-link--direction"
                                    href="<?php echo $domainpageurls[$domainpage - 1]->out(false); ?>">← <?php echo $ises ? 'Anterior' : 'Previous'; ?></a>
                            <?php } ?>
                            <?php foreach ($domainpageurls as $pageindex => $domainpageurl) { ?>
                                <a class="herb-board__domain-page-link<?php echo $pageindex === $domainpage ? ' is-current' : ''; ?>"
                                    href="<?php echo $domainpageurl->out(false); ?>"
                                    <?php echo $pageindex === $domainpage ? 'aria-current="page"' : ''; ?>>
                                    <?php echo $pageindex + 1; ?>
                                </a>
                            <?php } ?>
                            <?php if ($domainpage + 1 < $selecteddomaintotalpages) { ?>
                                <a class="herb-board__domain-page-link herb-board__domain-page-link--direction"
                                    href="<?php echo $domainpageurls[$domainpage + 1]->out(false); ?>"><?php echo $ises ? 'Siguiente' : 'Next'; ?> →</a>
                            <?php } ?>
                        </nav>
                    </div>
                <?php } ?>
            <?php } ?>
        </section>
    <?php } ?>

    <?php if ($track === 'herbs' && !$herbcategories) { ?>
        <section class="herb-board__panel" aria-labelledby="herb-board-session-title">
            <div class="herb-board__panel-heading">
                <div>
                    <h3 id="herb-board-session-title">Configure the session</h3>
                    <p>These controls establish the Board Level practice structure before the new question pool is connected.</p>
                </div>
            </div>
            <div class="herb-board__setup">
                <div class="herb-board__option">
                    <strong>Number of questions</strong>
                    <span>Choose from the questions available in the selected topic.</span>
                    <div class="herb-board__choices">
                        <span class="herb-board__choice herb-board__choice--selected">10</span>
                        <span class="herb-board__choice">25</span>
                        <span class="herb-board__choice">50</span>
                        <span class="herb-board__choice">100</span>
                    </div>
                </div>
                <div class="herb-board__option">
                    <strong>Feedback</strong>
                    <span>Use the same answer-and-explanation style as the current mock exams.</span>
                    <div class="herb-board__choices">
                        <span class="herb-board__choice herb-board__choice--selected">After each answer</span>
                        <span class="herb-board__choice">At the end</span>
                    </div>
                </div>
                <div class="herb-board__option">
                    <strong>Time limit</strong>
                    <span>Practice without pressure or turn on an exam countdown.</span>
                    <div class="herb-board__choices">
                        <span class="herb-board__choice herb-board__choice--selected">No limit</span>
                        <span class="herb-board__choice">Timed</span>
                    </div>
                </div>
            </div>
            <div class="herb-board__notice">
                Design stage: topic selection is visible now. Starting attempts and saving history will be enabled when the verified Board question pool is connected.
            </div>
        </section>
    <?php } ?>
</main>
<script>
(() => {
    const stepBackScrollKey = 'herbology-step-back-scroll';
    const revealStepBackDestination = event => {
        try {
            if (window.sessionStorage.getItem(stepBackScrollKey) !== 'top') {
                return;
            }
            window.sessionStorage.removeItem(stepBackScrollKey);
            if (event?.persisted) {
                window.location.reload();
                return;
            }
            window.scrollTo(0, 0);
            window.requestAnimationFrame(() => window.scrollTo(0, 0));
        } catch (error) {
            // Navigation still works when session storage is unavailable.
        }
    };
    window.addEventListener('pageshow', revealStepBackDestination);
    revealStepBackDestination();
    const stepBack = document.querySelector('[data-herbology-step-back]');
    stepBack?.addEventListener('click', event => {
        if (window.history.length > 1) {
            event.preventDefault();
            try {
                window.sessionStorage.setItem(stepBackScrollKey, 'top');
            } catch (error) {
                // History navigation still works when session storage is unavailable.
            }
            window.history.back();
        }
        // The link destination remains the safe fallback without history.
    });
})();
</script>
<?php
echo $OUTPUT->footer();
