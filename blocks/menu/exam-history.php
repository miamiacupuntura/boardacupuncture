<?php
// Unified exam and quiz attempt history for the current user.

require_once(__DIR__ . '/../../config.php');

require_login();

$requestedui = optional_param('ui', '', PARAM_ALPHA);
$requestedlang = optional_param('lang', '', PARAM_ALPHANUMEXT);
$displaylang = $requestedui !== '' ? $requestedui : ($requestedlang !== '' ? $requestedlang : current_language());
$ises = preg_match('/^es(?:[_-].*)?$/i', $displaylang) === 1;
$historytype = optional_param('type', 'all', PARAM_ALPHA);
if (!in_array($historytype, ['all', 'board', 'quiz', 'boardquizcatalog', 'boardexamcatalog'], true)) {
    $historytype = 'all';
}
$progressfilter = optional_param('progress', 'all', PARAM_ALPHA);
if (!in_array($progressfilter, ['all', 'completed', 'pending'], true)) {
    $progressfilter = 'all';
}
$sortfield = optional_param('sort', 'date', PARAM_ALPHA);
if (!in_array($sortfield, ['date', 'exam', 'result', 'status', 'duration'], true)) {
    $sortfield = 'date';
}
$sortdirection = optional_param('direction', 'desc', PARAM_ALPHA);
if (!in_array($sortdirection, ['asc', 'desc'], true)) {
    $sortdirection = 'desc';
}
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 25;
$languageparams = $ises ? ['lang' => 'es', 'ui' => 'es'] : ['lang' => 'en', 'ui' => 'en'];
$sortparams = ['sort' => $sortfield, 'direction' => $sortdirection];
$pageurl = new moodle_url('/blocks/menu/exam-history.php', [
    'type' => $historytype,
    'page' => $page,
    'progress' => $progressfilter,
] + $sortparams + $languageparams);
$dashboardurl = new moodle_url('/my/', [], 'board-level-herbology');
$languageurl = new moodle_url('/blocks/menu/exam-history.php', [
    'type' => $historytype,
    'page' => $page,
    'progress' => $progressfilter,
] + $sortparams + ($ises ? ['lang' => 'en', 'ui' => 'en'] : ['lang' => 'es', 'ui' => 'es']));

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Historial de exámenes' : 'Exam History');
$PAGE->set_heading($ises ? 'Historial de exámenes' : 'Exam History');
$PAGE->set_cacheable(false);

function board_exam_history_number(float $value): string {
    return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
}

function board_exam_history_duration(int $seconds, bool $ises): string {
    $seconds = max(0, $seconds);
    $minutes = intdiv($seconds, 60);
    $remainingseconds = $seconds % 60;
    if ($minutes > 0) {
        return $minutes . ' ' . ($ises ? 'min' : 'min') . ' ' . $remainingseconds . ' s';
    }
    return $remainingseconds . ' s';
}

$catalogcomparisonfile = static function(string $filename) use ($CFG): string {
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

$boardquizrows = [];
$singleherbquizpath = '/study-guide/comparisons/'
    . ($ises ? 'single-herb-practice-es.html' : 'single-herb-practice.html');
$herbbankpath = $catalogcomparisonfile('herb-comparison-bank-data.json');
if ($herbbankpath !== '') {
    $herbbank = json_decode(file_get_contents($herbbankpath), true);
    if (is_array($herbbank)) {
        foreach ($herbbank['chapters'] ?? [] as $chapter) {
            $number = (int) ($chapter['number'] ?? 0);
            $name = trim((string) ($chapter['title'] ?? ''));
            $questioncount = (int) ($chapter['questionCount'] ?? 0);
            if ($number <= 1 || $name === '' || $questioncount < 1) {
                continue;
            }
            $quizhistorytype = 'bqh_' . $number;
            $quizurl = new moodle_url($singleherbquizpath, [
                'category' => $number,
                'size' => min(25, $questioncount),
                'historytype' => $quizhistorytype,
                'sesskey' => sesskey(),
            ]);
            $boardquizrows[] = [
                'key' => 'herb-' . $number,
                'examtype' => $quizhistorytype,
                'topic' => $name,
                'source' => $ises ? 'Quiz de hierbas individuales' : 'Single Herbs Quiz',
                'course' => $ises ? 'Herbología China Board Level' : 'Chinese Herbology Board Level',
                'questions' => min(25, $questioncount),
                'status' => $ises ? 'Disponible' : 'Available',
                'statusclass' => 'is-available',
                'attempts' => null,
                'url' => $quizurl->out(false),
                'action' => $ises ? 'Abrir' : 'Open',
            ];
        }
    }
}

$formulapracticepath = '/study-guide/comparisons/'
    . ($ises ? 'formula-practice-es.html' : 'formula-practice.html');
$formulabankpath = $catalogcomparisonfile('formula-practice-data.json');
if ($formulabankpath !== '') {
    $formulabank = json_decode(file_get_contents($formulabankpath), true);
    if (is_array($formulabank)) {
        $manifestpath = dirname($formulabankpath) . '/formula-modifications-manifest.json';
        if (is_readable($manifestpath)) {
            $manifest = json_decode(file_get_contents($manifestpath), true);
            $knowncategories = array_fill_keys(array_filter(array_map(
                static fn($category): string => (string) ($category['key'] ?? ''),
                $formulabank['categories'] ?? []
            )), true);
            $knownquestions = array_fill_keys(array_filter(array_map(
                static fn($question): string => (string) ($question['sourceNumber'] ?? ''),
                $formulabank['questions'] ?? []
            )), true);
            foreach ($manifest['files'] ?? [] as $filename) {
                $supplementpath = dirname($formulabankpath) . '/' . basename((string) $filename);
                if (!is_readable($supplementpath)) {
                    continue;
                }
                $supplement = json_decode(file_get_contents($supplementpath), true);
                if (!is_array($supplement)) {
                    continue;
                }
                foreach ($supplement['categories'] ?? [] as $category) {
                    $key = (string) ($category['key'] ?? '');
                    if ($key !== '' && !isset($knowncategories[$key])) {
                        $formulabank['categories'][] = $category;
                        $knowncategories[$key] = true;
                    }
                }
                foreach ($supplement['questions'] ?? [] as $question) {
                    $source = (string) ($question['sourceNumber'] ?? '');
                    if ($source !== '' && !isset($knownquestions[$source])) {
                        $formulabank['questions'][] = $question;
                        $knownquestions[$source] = true;
                    }
                }
            }
        }
        foreach ($formulabank['categories'] ?? [] as $category) {
            $key = clean_param($category['key'] ?? '', PARAM_ALPHANUMEXT);
            $name = trim((string) ($category[$ises ? 'es' : 'en'] ?? $category['en'] ?? ''));
            if ($key === '' || $name === '') {
                continue;
            }
            $questioncount = 0;
            foreach ($formulabank['questions'] ?? [] as $question) {
                if (in_array($key, $question['categoryKeys'] ?? [], true)) {
                    $questioncount++;
                }
            }
            if ($questioncount < 1) {
                continue;
            }
            $ismodifications = $key === 'modifications';
            $quizhistorytype = $ismodifications
                ? 'bqf_modifications'
                : 'bqf_' . substr(sha1($key), 0, 12);
            $quizparams = [
                'category' => $key,
                'size' => min(25, $questioncount),
                'historytype' => $quizhistorytype,
                'sesskey' => sesskey(),
                'from' => 'history',
            ];
            if ($ismodifications) {
                $quizparams['scope'] = 'modifications';
            }
            $quizurl = new moodle_url($formulapracticepath, $quizparams);
            $boardquizrows[] = [
                'key' => $ismodifications ? 'formula-modifications' : 'formula-' . $key,
                'examtype' => $quizhistorytype,
                'topic' => $name,
                'source' => $ismodifications
                    ? ($ises ? 'Quiz de modificaciones de fórmulas' : 'Formula Modifications Quiz')
                    : ($ises ? 'Quiz de fórmulas' : 'Formula Quiz'),
                'course' => $ises ? 'Herbología China Board Level' : 'Chinese Herbology Board Level',
                'questions' => min(25, $questioncount),
                'status' => $ises ? 'Disponible' : 'Available',
                'statusclass' => 'is-available',
                'attempts' => null,
                'url' => $quizurl->out(false),
                'action' => $ises ? 'Abrir' : 'Open',
            ];
        }
    }
}
$duiyaopracticepath = '/study-guide/comparisons/'
    . ($ises ? 'dui-yao-practice-es.html' : 'dui-yao-practice.html');
$duiyaoquizurl = new moodle_url($duiyaopracticepath, [
    'size' => 25,
    'historytype' => 'bqd_duiyao',
    'sesskey' => sesskey(),
    'from' => 'history',
]);
$boardquizrows[] = [
    'key' => 'dui-yao',
    'examtype' => 'bqd_duiyao',
    'topic' => 'Dui Yao',
    'source' => $ises ? 'Quiz de Dui Yao' : 'Dui Yao Quiz',
    'course' => $ises ? 'Herbología China Board Level' : 'Chinese Herbology Board Level',
    'questions' => 51,
    'status' => $ises ? 'Disponible' : 'Available',
    'statusclass' => 'is-available',
    'attempts' => null,
    'url' => $duiyaoquizurl->out(false),
    'action' => $ises ? 'Abrir' : 'Open',
];

$miscquizdefinitions = [
    'families' => ['Formula Families / Variations', 'Familias y variaciones de fórmulas', 22],
    'nametraps' => ['Name Traps', 'Trampas de nombres', 22],
    'herbparts' => ['Herb Families & Parts', 'Familias y partes de hierbas', 20],
    'formulanames' => ['Formula Name Traps', 'Trampas de nombres de fórmulas', 22],
    'misleading' => ['Misleading Formula Names', 'Nombres engañosos de fórmulas', 6],
    'advanced' => ['Advanced Name Traps', 'Trampas avanzadas de nombres', 8],
    'supplements' => ['Supplemental Herb Families', 'Familias suplementarias de hierbas', 6],
    'secondary' => ['Herbs Secondary Actions', 'Acciones secundarias de hierbas', 60],
    'pathology' => ['Pathology Comparisons', 'Comparaciones de patologías', 66],
    'patterns' => ['Pattern Comparisons', 'Comparaciones de patrones', 50],
    'maciocia' => ['Pattern Comparison Maciocia', 'Comparación de patrones Maciocia', 100],
    'safety' => ['Safety Library', 'Biblioteca de seguridad', 12],
    'composition' => ['Formula Composition', 'Composición de fórmulas', 12],
    'incompat' => ['Herbal Incompatibilities', 'Incompatibilidades herbales', 10],
    'combinations' => ['Herbal Combination', 'Combinaciones herbales', 70],
];
$miscpracticepath = '/study-guide/comparisons/'
    . ($ises ? 'miscellaneous-practice-es.html' : 'miscellaneous-practice.html');
foreach ($miscquizdefinitions as $misckey => [$misctitleen, $misctitlees, $miscquestioncount]) {
    $miscexamtype = 'bqm_' . $misckey;
    $miscquizurl = new moodle_url($miscpracticepath, [
        'source' => $misckey,
        'sesskey' => sesskey(),
        'from' => 'history',
    ]);
    $boardquizrows[] = [
        'key' => 'misc-' . $misckey,
        'examtype' => $miscexamtype,
        'topic' => $ises ? $misctitlees : $misctitleen,
        'source' => $ises ? 'Quiz misceláneo' : 'Miscellaneous Quiz',
        'course' => $ises ? 'Herbología China Board Level' : 'Chinese Herbology Board Level',
        'questions' => min(25, $miscquestioncount),
        'status' => $ises ? 'Disponible' : 'Available',
        'statusclass' => 'is-available',
        'attempts' => null,
        'url' => $miscquizurl->out(false),
        'action' => $ises ? 'Abrir' : 'Open',
    ];
}

usort($boardquizrows, static function(array $left, array $right): int {
    $sourcecomparison = strnatcasecmp($left['source'], $right['source']);
    return $sourcecomparison !== 0
        ? $sourcecomparison
        : strnatcasecmp($left['topic'], $right['topic']);
});

$boardquiznamesbytype = [];
$boardquizcounts = [];
foreach ($boardquizrows as $boardquizrow) {
    $boardquiznamesbytype[$boardquizrow['examtype']] = $boardquizrow['topic'];
    $boardquizcounts[$boardquizrow['examtype']] = ['attempts' => 0, 'percentagesum' => 0.0];
}
$boardexamcounts = [
    'linear' => ['attempts' => 0, 'completed' => 0, 'percentagesum' => 0.0],
    'adaptive' => ['attempts' => 0, 'completed' => 0, 'percentagesum' => 0.0],
    'clinicalcases' => ['attempts' => 0, 'completed' => 0, 'percentagesum' => 0.0],
];
$customtable = new xmldb_table('block_menu_exam_attempts');
if ($DB->get_manager()->table_exists($customtable)) {
    $boardattempts = $DB->get_records_select(
        'block_menu_exam_attempts',
        'userid = :userid',
        ['userid' => $USER->id],
        '',
        'id, examtype, score, total'
    );
    foreach ($boardattempts as $boardattempt) {
        $examtype = (string) $boardattempt->examtype;
        $attemptpercentage = (int) $boardattempt->total > 0
            ? ((float) $boardattempt->score / (float) $boardattempt->total) * 100
            : 0.0;
        if (array_key_exists($examtype, $boardquizcounts)) {
            $boardquizcounts[$examtype]['attempts']++;
            $boardquizcounts[$examtype]['percentagesum'] += $attemptpercentage;
            continue;
        }
        $examkey = '';
        if (in_array($examtype, ['linear', 'linearincomplete'], true)) {
            $examkey = 'linear';
        } else if (in_array($examtype, ['adaptive', 'adaptiveincomplete'], true)) {
            $examkey = 'adaptive';
        } else if (in_array($examtype, ['clinicalcases', 'clinicalincomplete'], true)) {
            $examkey = 'clinicalcases';
        }
        if ($examkey === '') {
            continue;
        }
        $boardexamcounts[$examkey]['attempts']++;
        if (!str_ends_with($examtype, 'incomplete')) {
            $boardexamcounts[$examkey]['completed']++;
            $boardexamcounts[$examkey]['percentagesum'] += $attemptpercentage;
        }
    }
}
foreach ($boardquizrows as &$boardquizrow) {
    $quizcount = $boardquizcounts[$boardquizrow['examtype']] ?? ['attempts' => 0, 'percentagesum' => 0.0];
    $attemptcount = $quizcount['attempts'];
    $boardquizrow['attempts'] = $attemptcount;
    $boardquizrow['average'] = $attemptcount > 0
        ? round($quizcount['percentagesum'] / $attemptcount, 1)
        : null;
    if ($attemptcount > 0) {
        $boardquizrow['status'] = $ises ? 'Completado' : 'Completed';
        $boardquizrow['statusclass'] = 'is-completed';
    }
}
unset($boardquizrow);

$clinicalquestioncount = 63;
$clinicalbankpath = $CFG->dataroot . '/board-domain-banks/herbology-clinical-cases.json';
if (is_readable($clinicalbankpath)) {
    $clinicalbank = json_decode(file_get_contents($clinicalbankpath), true);
    foreach ($clinicalbank['exams'] ?? [] as $clinicalexam) {
        if (!empty($clinicalexam['questions']) && is_array($clinicalexam['questions'])) {
            $clinicalquestioncount = count($clinicalexam['questions']);
            break;
        }
    }
}
$boardexamdefinitions = [
    'linear' => [
        'topic' => $ises ? 'Examen Board lineal' : 'Linear Board Exam',
        'questions' => 100,
        'url' => new moodle_url('/blocks/menu/herbology-board.php', [
            'track' => 'exams',
            'exam' => 'linear',
        ] + $languageparams),
    ],
    'adaptive' => [
        'topic' => $ises ? 'Examen Board adaptativo' : 'Adaptive Board Exam',
        'questions' => 100,
        'url' => new moodle_url('/blocks/menu/herbology-board.php', [
            'track' => 'exams',
            'exam' => 'adaptive',
        ] + $languageparams),
    ],
    'clinicalcases' => [
        'topic' => $ises ? 'Casos clínicos' : 'Clinical Cases',
        'questions' => $clinicalquestioncount,
        'url' => new moodle_url('/blocks/menu/herbology-board.php', [
            'track' => 'clinicalcases',
        ] + $languageparams),
    ],
];
$boardexamrows = [];
foreach ($boardexamdefinitions as $examkey => $definition) {
    $attemptcount = $boardexamcounts[$examkey]['attempts'];
    $completedcount = $boardexamcounts[$examkey]['completed'];
    if ($completedcount > 0) {
        $status = $ises ? 'Completado' : 'Completed';
        $statusclass = 'is-completed';
    } else if ($attemptcount > 0) {
        $status = $ises ? 'En progreso' : 'In progress';
        $statusclass = 'is-progress';
    } else {
        $status = $ises ? 'No intentado' : 'Not attempted';
        $statusclass = 'is-not-started';
    }
    $boardexamrows[] = [
        'key' => 'exam-' . $examkey,
        'topic' => $definition['topic'],
        'source' => $ises ? 'Examen Board Level' : 'Board Level Exam',
        'course' => $ises ? 'Herbología China Board Level' : 'Chinese Herbology Board Level',
        'questions' => $definition['questions'],
        'status' => $status,
        'statusclass' => $statusclass,
        'attempts' => $attemptcount,
        'average' => $completedcount > 0
            ? round($boardexamcounts[$examkey]['percentagesum'] / $completedcount, 1)
            : null,
        'url' => $definition['url']->out(false),
        'action' => $ises ? 'Abrir' : 'Open',
    ];
}

$boardquizcatalogrows = $boardquizrows;
foreach ($boardexamrows as $boardexamrow) {
    if ($boardexamrow['key'] !== 'exam-clinicalcases') {
        continue;
    }
    $clinicalcatalogrow = $boardexamrow;
    $clinicalcatalogrow['key'] = 'clinical-cases';
    $clinicalcatalogrow['source'] = $ises ? 'Casos clínicos' : 'Clinical Cases';
    $boardquizcatalogrows[] = $clinicalcatalogrow;
    break;
}

$boardquizgroupdefinitions = [
    'single-herbs' => [
        'title' => $ises ? 'Quiz de hierbas individuales' : 'Single Herbs Quiz',
        'description' => $ises ? 'Categorías de hierbas individuales.' : 'Single-herb category quizzes.',
    ],
    'formulas' => [
        'title' => $ises ? 'Quiz de fórmulas' : 'Formula Quiz',
        'description' => $ises ? 'Fórmulas organizadas por estrategia terapéutica.' : 'Formulas organized by treatment strategy.',
    ],
    'dui-yao' => [
        'title' => $ises ? 'Quiz de Dui Yao' : 'Dui Yao Quiz',
        'description' => $ises ? 'Los 51 pares oficiales.' : 'The 51 official pairs.',
    ],
    'formula-modifications' => [
        'title' => $ises ? 'Quiz de modificaciones de fórmulas' : 'Formula Modifications Quiz',
        'description' => $ises ? 'Modificaciones clínicas de fórmulas.' : 'Clinical formula modifications.',
    ],
    'miscellaneous' => [
        'title' => $ises ? 'Quiz misceláneo' : 'Miscellaneous Quiz',
        'description' => $ises ? 'Recursos suplementarios organizados por tópico.' : 'Supplemental resources organized by topic.',
    ],
    'clinical-cases' => [
        'title' => $ises ? 'Casos clínicos' : 'Clinical Cases',
        'description' => $ises ? 'Casos integrados con retroalimentación.' : 'Integrated cases with feedback.',
    ],
];

foreach ($boardquizcatalogrows as &$boardquizcatalogrow) {
    $rowkey = (string) $boardquizcatalogrow['key'];
    if ($rowkey === 'formula-modifications') {
        $boardquizcatalogrow['group'] = 'formula-modifications';
    } else if (str_starts_with($rowkey, 'herb-')) {
        $boardquizcatalogrow['group'] = 'single-herbs';
    } else if (str_starts_with($rowkey, 'formula-')) {
        $boardquizcatalogrow['group'] = 'formulas';
    } else if ($rowkey === 'dui-yao') {
        $boardquizcatalogrow['group'] = 'dui-yao';
    } else if (str_starts_with($rowkey, 'misc-')) {
        $boardquizcatalogrow['group'] = 'miscellaneous';
    } else {
        $boardquizcatalogrow['group'] = 'clinical-cases';
    }

    if ($boardquizcatalogrow['statusclass'] === 'is-completed') {
        $boardquizcatalogrow['status'] = $ises ? 'Respondido' : 'Answered';
    } else if ($boardquizcatalogrow['statusclass'] === 'is-available'
            || $boardquizcatalogrow['statusclass'] === 'is-not-started') {
        $boardquizcatalogrow['status'] = $ises ? 'Pendiente' : 'Pending';
        $boardquizcatalogrow['statusclass'] = 'is-pending';
    }
}
unset($boardquizcatalogrow);

$boardquiztotal = count($boardquizcatalogrows);
$boardexamtotal = count($boardexamrows);
$catalogallrows = $historytype === 'boardquizcatalog' ? $boardquizcatalogrows : $boardexamrows;
$catalogtotal = count($catalogallrows);
$catalogquestions = array_sum(array_column($catalogallrows, 'questions'));
$catalogcompleted = count(array_filter(
    $catalogallrows,
    static fn(array $row): bool => $row['statusclass'] === 'is-completed'
));
$catalogpending = $catalogtotal - $catalogcompleted;
$catalogprogress = count(array_filter(
    $catalogallrows,
    static fn(array $row): bool => $row['statusclass'] === 'is-progress'
));
$catalognotstarted = count(array_filter(
    $catalogallrows,
    static fn(array $row): bool => in_array($row['statusclass'], ['is-not-started', 'is-pending'], true)
));
$catalogrows = $catalogallrows;
if ($historytype === 'boardquizcatalog' && $progressfilter !== 'all') {
    $catalogrows = array_values(array_filter(
        $catalogallrows,
        static function(array $row) use ($progressfilter): bool {
            $iscompleted = $row['statusclass'] === 'is-completed';
            return $progressfilter === 'completed' ? $iscompleted : !$iscompleted;
        }
    ));
}
if ($historytype === 'boardquizcatalog') {
    $catalogtitle = $ises ? 'Catálogo de Quiz Board Level' : 'Quiz Board Level Catalog';
    $catalogkicker = $ises ? 'QUIZZES DEL SISTEMA NUEVO' : 'NEW-SYSTEM QUIZZES';
    $catalogsummaries = [
        [$ises ? 'Quizzes' : 'Quizzes', $catalogtotal],
        [$ises ? 'Respondidos' : 'Answered', $catalogcompleted],
        [$ises ? 'Pendientes' : 'Pending', $catalogpending],
        [$ises ? 'Preguntas' : 'Questions', $catalogquestions],
    ];
} else {
    $catalogtitle = $ises ? 'Catálogo de Exam Board Level' : 'Exam Board Level Catalog';
    $catalogkicker = $ises ? 'EXÁMENES DEL SISTEMA NUEVO' : 'NEW-SYSTEM EXAMS';
    $catalogsummaries = [
        [$ises ? 'Exámenes' : 'Exams', $catalogtotal],
        [$ises ? 'Completados' : 'Completed', $catalogcompleted],
        [$ises ? 'En progreso' : 'In progress', $catalogprogress],
        [$ises ? 'No intentados' : 'Not attempted', $catalognotstarted],
    ];
}

$cataloggroups = [];
if ($historytype === 'boardquizcatalog') {
    foreach ($boardquizgroupdefinitions as $groupkey => $groupdefinition) {
        $allgrouprows = array_values(array_filter(
            $catalogallrows,
            static fn(array $row): bool => ($row['group'] ?? '') === $groupkey
        ));
        $visiblegrouprows = array_values(array_filter(
            $catalogrows,
            static fn(array $row): bool => ($row['group'] ?? '') === $groupkey
        ));
        if (!$allgrouprows || !$visiblegrouprows) {
            continue;
        }
        $groupcompleted = count(array_filter(
            $allgrouprows,
            static fn(array $row): bool => $row['statusclass'] === 'is-completed'
        ));
        $cataloggroups[] = [
            'key' => $groupkey,
            'title' => $groupdefinition['title'],
            'description' => $groupdefinition['description'],
            'total' => count($allgrouprows),
            'completed' => $groupcompleted,
            'pending' => count($allgrouprows) - $groupcompleted,
            'rows' => $visiblegrouprows,
        ];
    }
} else {
    $cataloggroups[] = [
        'key' => 'board-exams',
        'title' => '',
        'description' => '',
        'total' => count($catalogrows),
        'completed' => $catalogcompleted,
        'pending' => $catalogtotal - $catalogcompleted,
        'rows' => $catalogrows,
    ];
}

$historyrows = [];
$customtable = new xmldb_table('block_menu_exam_attempts');
if (($historytype === 'all' || $historytype === 'board') && $DB->get_manager()->table_exists($customtable)) {
    $customattempts = $DB->get_records_sql(
        "SELECT a.id, a.examtype, a.score, a.total, a.duration, a.timecompleted,
                c.fullname AS coursename
           FROM {block_menu_exam_attempts} a
      LEFT JOIN {course} c ON c.id = a.courseid
          WHERE a.userid = :userid
       ORDER BY a.timecompleted DESC, a.id DESC",
        ['userid' => $USER->id]
    );
    foreach ($customattempts as $attempt) {
        $examtype = (string) $attempt->examtype;
        $isincomplete = str_ends_with($examtype, 'incomplete');
        $percentage = (int) $attempt->total > 0
            ? (int) round(((float) $attempt->score / (float) $attempt->total) * 100)
            : 0;
        $isboardquiz = isset($boardquiznamesbytype[$examtype]);
        if ($isboardquiz) {
            $examname = $boardquiznamesbytype[$examtype];
        } else if (in_array($examtype, ['adaptive', 'adaptiveincomplete'], true)) {
            $examname = $ises ? 'Examen Board adaptativo' : 'Adaptive Board Exam';
        } else if (in_array($examtype, ['clinicalcases', 'clinicalincomplete'], true)) {
            $examname = $ises ? 'Casos clínicos' : 'Clinical Cases';
        } else if (in_array($examtype, ['linear', 'linearincomplete'], true)) {
            $examname = $ises ? 'Examen Board lineal' : 'Linear Board Exam';
        } else {
            $examname = ucwords(str_replace(['_', '-'], ' ', $examtype));
        }
        $status = $isincomplete
            ? ($ises ? 'No completado' : 'Not completed')
            : ($percentage >= 70
                ? ($ises ? 'Aprobado' : 'Passed')
                : ($ises ? 'No aprobado' : 'Not passed'));
        $historyrows[] = [
            'key' => 'board-' . (int) $attempt->id,
            'timestamp' => (int) $attempt->timecompleted,
            'exam' => $examname,
            'source' => $isboardquiz
                ? ($ises ? 'Quiz Board Level' : 'Board Level Quiz')
                : ($ises ? 'Examen Board' : 'Board Exam'),
            'course' => (string) ($attempt->coursename ?? ''),
            'score' => board_exam_history_number((float) $attempt->score),
            'total' => board_exam_history_number((float) $attempt->total),
            'percentage' => $percentage,
            'status' => $status,
            'statusclass' => $isincomplete ? 'is-incomplete' : ($percentage >= 70 ? 'is-passed' : 'is-failed'),
            'duration' => board_exam_history_duration((int) $attempt->duration, $ises),
            'durationseconds' => (int) $attempt->duration,
            'resumeurl' => '',
        ];
    }
}

$quizattemptstable = new xmldb_table('quiz_attempts');
if (($historytype === 'all' || $historytype === 'quiz') && $DB->get_manager()->table_exists($quizattemptstable)) {
    $quizattempts = $DB->get_records_sql(
        "SELECT qa.id, qa.attempt, qa.state, qa.timestart, qa.timefinish, qa.timemodified,
                qa.sumgrades AS attemptscore, q.sumgrades AS quiztotal,
                q.name AS examname, c.fullname AS coursename, cm.id AS cmid
           FROM {quiz_attempts} qa
           JOIN {quiz} q ON q.id = qa.quiz
           JOIN {course} c ON c.id = q.course
           JOIN {modules} m ON m.name = :quizmodulename
           JOIN {course_modules} cm ON cm.module = m.id
                AND cm.instance = q.id AND cm.course = q.course
          WHERE qa.userid = :userid AND qa.preview = 0
       ORDER BY qa.timemodified DESC, qa.id DESC",
        [
            'userid' => $USER->id,
            'quizmodulename' => 'quiz',
        ]
    );
    foreach ($quizattempts as $attempt) {
        $isfinished = (string) $attempt->state === 'finished';
        $score = $attempt->attemptscore === null ? 0.0 : (float) $attempt->attemptscore;
        $total = (float) $attempt->quiztotal;
        $percentage = $total > 0 ? (int) round(($score / $total) * 100) : 0;
        $endtime = (int) $attempt->timefinish > 0
            ? (int) $attempt->timefinish
            : max((int) $attempt->timemodified, (int) $attempt->timestart);
        $duration = max(0, $endtime - (int) $attempt->timestart);
        $canresume = in_array((string) $attempt->state, ['inprogress', 'overdue'], true);
        $resumeurl = $canresume ? new moodle_url('/mod/quiz/attempt.php', [
            'attempt' => (int) $attempt->id,
            'cmid' => (int) $attempt->cmid,
        ]) : null;
        $status = !$isfinished
            ? ($ises ? 'No completado' : 'Not completed')
            : ($percentage >= 70
                ? ($ises ? 'Aprobado' : 'Passed')
                : ($ises ? 'No aprobado' : 'Not passed'));
        $historyrows[] = [
            'key' => 'quiz-' . (int) $attempt->id,
            'timestamp' => $endtime,
            'exam' => (string) $attempt->examname,
            'source' => $ises ? 'Quiz de Moodle' : 'Moodle Quiz',
            'course' => (string) $attempt->coursename,
            'score' => board_exam_history_number($score),
            'total' => board_exam_history_number($total),
            'percentage' => $percentage,
            'status' => $status,
            'statusclass' => !$isfinished ? 'is-incomplete' : ($percentage >= 70 ? 'is-passed' : 'is-failed'),
            'duration' => board_exam_history_duration($duration, $ises),
            'durationseconds' => $duration,
            'resumeurl' => $resumeurl ? $resumeurl->out(false) : '',
            'resumelabel' => $ises ? 'Retomar quiz →' : 'Resume Quiz →',
        ];
    }
}

usort($historyrows, static function(array $left, array $right) use ($sortfield, $sortdirection): int {
    switch ($sortfield) {
        case 'exam':
            $comparison = strnatcasecmp($left['exam'], $right['exam']);
            break;
        case 'result':
            $comparison = $left['percentage'] <=> $right['percentage'];
            break;
        case 'status':
            $comparison = strnatcasecmp($left['status'], $right['status']);
            break;
        case 'duration':
            $comparison = $left['durationseconds'] <=> $right['durationseconds'];
            break;
        case 'date':
        default:
            $comparison = $left['timestamp'] <=> $right['timestamp'];
            break;
    }
    if ($comparison !== 0) {
        return $sortdirection === 'asc' ? $comparison : -$comparison;
    }
    if ($left['timestamp'] !== $right['timestamp']) {
        return $right['timestamp'] <=> $left['timestamp'];
    }
    return strcmp($right['key'], $left['key']);
});
$totalattempts = count($historyrows);
$totalpages = max(1, (int) ceil($totalattempts / $perpage));
if ($page >= $totalpages) {
    $page = $totalpages - 1;
}
$pagedrows = array_slice($historyrows, $page * $perpage, $perpage);
$filterlabels = [
    'all' => $ises ? 'Todos' : 'All',
    'board' => $ises ? 'Exámenes Board' : 'Board Exams',
    'quiz' => $ises ? 'Quizzes de Moodle' : 'Moodle Quizzes',
    'boardquizcatalog' => ($ises ? 'Quiz Board Level' : 'Quiz Board Level') . ' (' . $boardquiztotal . ')',
    'boardexamcatalog' => ($ises ? 'Exam Board Level' : 'Exam Board Level') . ' (' . $boardexamtotal . ')',
];

$sortlabels = [
    'date' => $ises ? 'Fecha y hora' : 'Date and time',
    'exam' => $ises ? 'Examen / Quiz' : 'Exam / Quiz',
    'result' => $ises ? 'Resultado' : 'Result',
    'status' => $ises ? 'Estado' : 'Status',
    'duration' => $ises ? 'Duración' : 'Duration',
];
echo $OUTPUT->header();
?>
<style>
.exam-history{width:min(1180px,calc(100% - 28px));margin:24px auto 56px;color:#102a43}
.exam-history__top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:18px;padding:22px;border:1px solid #b9d8d2;border-top:5px solid #087c69;border-radius:14px;background:#fff}
.exam-history__eyebrow{margin:0 0 5px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.exam-history h2{margin:0 0 7px;font-size:clamp(27px,4vw,38px)}
.exam-history__intro{max-width:720px;margin:0;color:#526b84;line-height:1.5}
.exam-history__actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:9px}
.exam-history__button{display:inline-flex;min-height:40px;align-items:center;padding:8px 13px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81;font-weight:800;text-decoration:none;white-space:nowrap}
.exam-history__button:hover{background:#eef7f5;text-decoration:none}
.exam-history__filters{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 16px}
.exam-history__filters a{padding:8px 13px;border:1px solid #8fb9b1;border-radius:999px;background:#fff;color:#075f52;font-weight:800;text-decoration:none}
.exam-history__filters a[aria-current="page"]{background:#087c69;color:#fff}
.exam-history__sort{display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px;margin:-2px 0 16px;padding:12px 14px;border:1px solid #c7ddd9;border-radius:10px;background:#f8fcfb}
.exam-history__sort label{display:grid;gap:4px;margin:0;padding:0;color:#35566f;font-size:12px;font-weight:800}
.exam-history__sort select{box-sizing:border-box;height:40px;padding:6px 34px 6px 10px;border:1px solid #9db8c8;border-radius:8px;background:#fff;color:#102a43;font-weight:700}
.exam-history__sort button{box-sizing:border-box;height:40px;align-self:flex-end;margin:0;padding:7px 15px;border:0;border-radius:8px;background:#087c69;color:#fff;font-weight:900;line-height:1;cursor:pointer}
.exam-history__sort button:hover,.exam-history__sort button:focus{background:#066354}
.exam-history__panel{padding:18px;border:1px solid #b9d8d2;border-top:4px solid #087c69;border-radius:13px;background:#f8fcfb}
.exam-history__panel-header{display:flex;align-items:end;justify-content:space-between;gap:16px;margin-bottom:14px}
.exam-history__panel-header span{display:block;color:#087c69;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}
.exam-history__panel-header h3{margin:4px 0 0;font-size:21px}
.exam-history__panel-header small{color:#526b84;font-weight:700}
.exam-history__catalog-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px;margin-bottom:14px}
.exam-history__catalog-stat{padding:11px 13px;border:1px solid #c7ddd9;border-radius:9px;background:#fff}
.exam-history__catalog-stat span{display:block;color:#087c69;font-size:11px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}
.exam-history__catalog-stat strong{display:block;margin-top:4px;font-size:20px}
.exam-history__progress-filters{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 16px}
.exam-history__progress-filters a{padding:8px 13px;border:1px solid #8fb9b1;border-radius:999px;background:#fff;color:#075f52;font-weight:850;text-decoration:none}
.exam-history__progress-filters a[aria-current="page"]{background:#102a43;border-color:#102a43;color:#fff}
.exam-history__catalog-groups{display:grid;gap:16px}
.exam-history__catalog-group{overflow:hidden;border:1px solid #b9d8d2;border-radius:12px;background:#eef8f6}
.exam-history__catalog-group-header{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:14px;align-items:center;padding:15px 16px;border-bottom:1px solid #c7ddd9;background:#e6f5f1}
.exam-history__catalog-group-header h4{margin:0 0 3px;font-size:18px}
.exam-history__catalog-group-header p{margin:0;color:#526b84;font-size:13px}
.exam-history__catalog-group-counts{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:7px}
.exam-history__catalog-group-counts span{padding:5px 9px;border-radius:999px;background:#fff;color:#35566f;font-size:12px;font-weight:850;white-space:nowrap}
.exam-history__catalog-group-counts .is-done{background:#dff4e8;color:#087c3b}
.exam-history__catalog-group-counts .is-pending{background:#fff2d7;color:#8a5b00}
.exam-history__catalog-progress{grid-column:1/-1;height:7px;overflow:hidden;border-radius:999px;background:#d7e4e8}
.exam-history__catalog-progress span{display:block;height:100%;border-radius:inherit;background:#087c69}
.exam-history__catalog-list{display:grid;gap:9px;padding:12px}
.exam-history__catalog-item{display:grid;grid-template-columns:1.3fr 1.1fr .5fr .65fr .5fr .55fr auto;align-items:center;gap:14px;padding:13px;border:1px solid #c7ddd9;border-radius:10px;background:#fff}
.exam-history__catalog-item>div{min-width:0}
.exam-history__catalog-item strong{display:block;overflow-wrap:anywhere;font-size:14px}
.exam-history__catalog-item small{display:block;margin-top:3px;color:#61758a;overflow-wrap:anywhere}
.exam-history__catalog-status.is-completed{color:#087c3b}
.exam-history__catalog-status.is-progress{color:#9a6700}
.exam-history__catalog-status.is-not-started{color:#526b84}
.exam-history__catalog-status.is-available{color:#0f4c81}
.exam-history__catalog-status.is-pending{color:#9a6700}
.exam-history__catalog-open{display:inline-flex;min-height:36px;align-items:center;justify-content:center;padding:7px 12px;border-radius:8px;background:#2b63e8;color:#fff!important;font-weight:900;text-decoration:none;white-space:nowrap}
.exam-history__catalog-open:hover,.exam-history__catalog-open:focus{background:#1749bc;color:#fff!important;text-decoration:none}
.exam-history__list{display:grid;gap:9px}
.exam-history__attempt{display:grid;grid-template-columns:1.4fr 1fr .8fr .8fr .75fr;gap:14px;padding:13px;border:1px solid #c7ddd9;border-radius:10px;background:#fff}
.exam-history__attempt div{min-width:0}
.exam-history__label{display:block;margin-bottom:5px;color:#087c69;font-size:11px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}
.exam-history__attempt strong{display:block;overflow-wrap:anywhere;font-size:14px}
.exam-history__attempt small{display:block;margin-top:3px;color:#61758a;overflow-wrap:anywhere}
.exam-history__resume{display:inline-flex;margin-top:8px;padding:6px 10px;border-radius:7px;background:#2b63e8;color:#fff!important;font-size:12px;font-weight:900;text-decoration:none}
.exam-history__resume:hover,.exam-history__resume:focus{background:#1749bc;color:#fff!important;text-decoration:none}
.exam-history__attempt.is-passed .exam-history__status{color:#087c3b}
.exam-history__attempt.is-failed .exam-history__status{color:#b45309}
.exam-history__attempt.is-incomplete .exam-history__status{color:#765b20}
.exam-history__empty{margin:0;padding:28px;border:1px dashed #8fb9b1;border-radius:10px;background:#fff;color:#526b84;text-align:center;font-weight:700}
.exam-history__pagination{display:flex;align-items:center;justify-content:center;gap:12px;margin-top:18px}
.exam-history__pagination span{font-weight:800;color:#526b84}
.exam-history__pagination a{padding:8px 12px;border:1px solid #8fb9b1;border-radius:8px;background:#fff;color:#075f52;font-weight:800;text-decoration:none}
@media(max-width:850px){.exam-history__attempt{grid-template-columns:1fr 1fr}.exam-history__catalog-summary{grid-template-columns:1fr 1fr}.exam-history__catalog-item{grid-template-columns:1fr 1fr}.exam-history__catalog-item>a{justify-self:start}.exam-history__catalog-group-header{grid-template-columns:1fr}.exam-history__catalog-group-counts{justify-content:flex-start}.exam-history__top{display:block}.exam-history__actions{justify-content:flex-start;margin-top:14px}.exam-history__sort{align-items:stretch}.exam-history__sort label{flex:1 1 150px}.exam-history__sort button{flex:1 1 100%}}
@media(max-width:520px){.exam-history__attempt,.exam-history__catalog-item,.exam-history__catalog-summary{grid-template-columns:1fr}.exam-history{width:min(100% - 16px,1180px)}}
</style>
<main class="exam-history">
    <header class="exam-history__top">
        <div>
            <p class="exam-history__eyebrow"><?php echo $ises ? 'PROGRESO PERSONAL' : 'PERSONAL PROGRESS'; ?></p>
            <h2><?php echo $ises ? 'Historial de exámenes' : 'Exam History'; ?></h2>
            <p class="exam-history__intro"><?php echo $ises
                ? 'Resultados de tus exámenes Board y quizzes de Moodle, reunidos en orden cronológico.'
                : 'Results from your Board exams and Moodle quizzes, combined in chronological order.'; ?></p>
        </div>
        <div class="exam-history__actions">
            <a class="exam-history__button" href="<?php echo $dashboardurl->out(false); ?>"><?php echo $ises ? '← Dashboard' : '← Dashboard'; ?></a>
            <a class="exam-history__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
        </div>
    </header>

    <nav class="exam-history__filters" aria-label="<?php echo $ises ? 'Filtros del historial' : 'History filters'; ?>">
        <?php foreach ($filterlabels as $filterkey => $filterlabel) {
            $filterurl = new moodle_url('/blocks/menu/exam-history.php', [
                'type' => $filterkey,
                'page' => 0,
                'progress' => $filterkey === 'boardquizcatalog' ? $progressfilter : 'all',
            ] + $sortparams + $languageparams);
        ?>
            <a href="<?php echo $filterurl->out(false); ?>"<?php echo $historytype === $filterkey ? ' aria-current="page"' : ''; ?>><?php echo s($filterlabel); ?></a>
        <?php } ?>
    </nav>

    <?php if (!in_array($historytype, ['boardquizcatalog', 'boardexamcatalog'], true)) { ?>
    <form class="exam-history__sort" method="get" action="<?php echo (new moodle_url('/blocks/menu/exam-history.php'))->out(false); ?>">
        <input type="hidden" name="type" value="<?php echo s($historytype); ?>">
        <input type="hidden" name="lang" value="<?php echo $ises ? 'es' : 'en'; ?>">
        <input type="hidden" name="ui" value="<?php echo $ises ? 'es' : 'en'; ?>">
        <label>
            <span><?php echo $ises ? 'Ordenar por' : 'Sort by'; ?></span>
            <select name="sort">
                <?php foreach ($sortlabels as $sortkey => $sortlabel) { ?>
                    <option value="<?php echo s($sortkey); ?>"<?php echo $sortfield === $sortkey ? ' selected' : ''; ?>>
                        <?php echo s($sortlabel); ?>
                    </option>
                <?php } ?>
            </select>
        </label>
        <label>
            <span><?php echo $ises ? 'Dirección' : 'Direction'; ?></span>
            <select name="direction">
                <option value="desc"<?php echo $sortdirection === 'desc' ? ' selected' : ''; ?>>
                    <?php echo $ises ? 'Descendente' : 'Descending'; ?>
                </option>
                <option value="asc"<?php echo $sortdirection === 'asc' ? ' selected' : ''; ?>>
                    <?php echo $ises ? 'Ascendente' : 'Ascending'; ?>
                </option>
            </select>
        </label>
        <button type="submit"><?php echo $ises ? 'Ordenar' : 'Sort'; ?></button>
    </form>
    <?php } ?>

    <?php if (in_array($historytype, ['boardquizcatalog', 'boardexamcatalog'], true)) { ?>
    <section class="exam-history__panel" aria-labelledby="exam-catalog-title">
        <header class="exam-history__panel-header">
            <div>
                <span><?php echo s($catalogkicker); ?></span>
                <h3 id="exam-catalog-title"><?php echo s($catalogtitle); ?></h3>
            </div>
            <small><?php echo $catalogtotal; ?> <?php echo $ises ? 'tópicos' : 'topics'; ?></small>
        </header>

        <div class="exam-history__catalog-summary" aria-label="<?php echo $ises ? 'Resumen del catálogo' : 'Catalog summary'; ?>">
            <?php foreach ($catalogsummaries as [$summarylabel, $summaryvalue]) { ?>
                <div class="exam-history__catalog-stat">
                    <span><?php echo s($summarylabel); ?></span>
                    <strong><?php echo s((string) $summaryvalue); ?></strong>
                </div>
            <?php } ?>
        </div>

        <?php if ($historytype === 'boardquizcatalog') {
            $progresslabels = [
                'all' => $ises ? 'Todos' : 'All',
                'completed' => $ises ? 'Respondidos' : 'Answered',
                'pending' => $ises ? 'Pendientes' : 'Pending',
            ];
        ?>
            <nav class="exam-history__progress-filters" aria-label="<?php echo $ises ? 'Filtrar quizzes por progreso' : 'Filter quizzes by progress'; ?>">
                <?php foreach ($progresslabels as $progresskey => $progresslabel) {
                    $progressurl = new moodle_url('/blocks/menu/exam-history.php', [
                        'type' => 'boardquizcatalog',
                        'page' => 0,
                        'progress' => $progresskey,
                    ] + $sortparams + $languageparams);
                ?>
                    <a href="<?php echo $progressurl->out(false); ?>"<?php echo $progressfilter === $progresskey ? ' aria-current="page"' : ''; ?>>
                        <?php echo s($progresslabel); ?>
                    </a>
                <?php } ?>
            </nav>
        <?php } ?>

        <?php if (!$catalogrows) { ?>
            <p class="exam-history__empty"><?php echo $ises
                ? 'No hay evaluaciones en este filtro.'
                : 'No assessments match this filter.'; ?></p>
        <?php } else { ?>
            <div class="exam-history__catalog-groups">
                <?php foreach ($cataloggroups as $cataloggroup) {
                    $isquizgroup = $historytype === 'boardquizcatalog';
                    $completionpercentage = $cataloggroup['total'] > 0
                        ? (int) round(($cataloggroup['completed'] / $cataloggroup['total']) * 100)
                        : 0;
                ?>
                    <?php if ($isquizgroup) { ?>
                    <section class="exam-history__catalog-group" aria-labelledby="quiz-group-<?php echo s($cataloggroup['key']); ?>">
                        <header class="exam-history__catalog-group-header">
                            <div>
                                <h4 id="quiz-group-<?php echo s($cataloggroup['key']); ?>"><?php echo s($cataloggroup['title']); ?></h4>
                                <p><?php echo s($cataloggroup['description']); ?></p>
                            </div>
                            <div class="exam-history__catalog-group-counts" aria-label="<?php echo $ises ? 'Progreso del bloque' : 'Block progress'; ?>">
                                <span><?php echo (int) $cataloggroup['total']; ?> <?php echo $ises ? 'quizzes' : 'quizzes'; ?></span>
                                <span class="is-done"><?php echo (int) $cataloggroup['completed']; ?> <?php echo $ises ? 'respondidos' : 'answered'; ?></span>
                                <span class="is-pending"><?php echo (int) $cataloggroup['pending']; ?> <?php echo $ises ? 'pendientes' : 'pending'; ?></span>
                            </div>
                            <div class="exam-history__catalog-progress" role="progressbar"
                                aria-valuemin="0" aria-valuemax="<?php echo (int) $cataloggroup['total']; ?>"
                                aria-valuenow="<?php echo (int) $cataloggroup['completed']; ?>">
                                <span style="width:<?php echo $completionpercentage; ?>%"></span>
                            </div>
                        </header>
                    <?php } ?>
                        <div class="exam-history__catalog-list">
                            <?php foreach ($cataloggroup['rows'] as $catalogrow) { ?>
                                <article class="exam-history__catalog-item">
                                    <div>
                                        <span class="exam-history__label"><?php echo $ises ? 'Tópico / Evaluación' : 'Topic / Assessment'; ?></span>
                                        <strong><?php echo s($catalogrow['topic']); ?></strong>
                                    </div>
                                    <div>
                                        <span class="exam-history__label"><?php echo $ises ? 'Sistema' : 'System'; ?></span>
                                        <strong><?php echo s($catalogrow['source']); ?></strong>
                                        <small><?php echo s($catalogrow['course']); ?></small>
                                    </div>
                                    <div>
                                        <span class="exam-history__label"><?php echo $ises ? 'Preguntas' : 'Questions'; ?></span>
                                        <strong><?php echo (int) $catalogrow['questions']; ?></strong>
                                    </div>
                                    <div>
                                        <span class="exam-history__label"><?php echo $ises ? 'Estado' : 'Status'; ?></span>
                                        <strong class="exam-history__catalog-status <?php echo s($catalogrow['statusclass']); ?>">
                                            <?php echo s($catalogrow['status']); ?>
                                        </strong>
                                    </div>
                                    <div>
                                        <span class="exam-history__label"><?php echo $ises ? 'Intentos' : 'Attempts'; ?></span>
                                        <strong><?php echo $catalogrow['attempts'] === null ? '—' : (int) $catalogrow['attempts']; ?></strong>
                                    </div>
                                    <div>
                                        <span class="exam-history__label"><?php echo $ises ? 'Promedio' : 'Average'; ?></span>
                                        <strong><?php echo $catalogrow['average'] === null
                                            ? '—'
                                            : s(board_exam_history_number((float) $catalogrow['average']) . '%'); ?></strong>
                                    </div>
                                    <a class="exam-history__catalog-open" href="<?php echo s($catalogrow['url']); ?>">
                                        <?php echo s($catalogrow['action']); ?> →
                                    </a>
                                </article>
                            <?php } ?>
                        </div>
                    <?php if ($isquizgroup) { ?>
                    </section>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
    <?php } else { ?>

    <section class="exam-history__panel" aria-labelledby="exam-history-title">
        <header class="exam-history__panel-header">
            <div>
                <span><?php echo $ises ? 'PROGRESO PERSONAL' : 'PERSONAL PROGRESS'; ?></span>
                <h3 id="exam-history-title"><?php echo $ises ? 'Historial de exámenes' : 'Exam History'; ?></h3>
            </div>
            <small><?php echo $totalattempts; ?> <?php echo $ises ? 'intentos' : 'attempts'; ?></small>
        </header>

        <?php if (!$pagedrows) { ?>
            <p class="exam-history__empty"><?php echo $ises
                ? 'Todavía no hay resultados de exámenes o quizzes registrados.'
                : 'No exam or quiz results have been recorded yet.'; ?></p>
        <?php } else { ?>
            <div class="exam-history__list" role="list">
                <?php foreach ($pagedrows as $attempt) { ?>
                    <article class="exam-history__attempt <?php echo s($attempt['statusclass']); ?>" role="listitem">
                        <div>
                            <span class="exam-history__label"><?php echo $ises ? 'Fecha y hora' : 'Date and time'; ?></span>
                            <strong><?php echo s(userdate($attempt['timestamp'], get_string('strftimedatetimeshort'))); ?></strong>
                        </div>
                        <div>
                            <span class="exam-history__label"><?php echo $ises ? 'Examen / Quiz' : 'Exam / Quiz'; ?></span>
                            <strong><?php echo s($attempt['exam']); ?></strong>
                            <small><?php echo s($attempt['source'] . ($attempt['course'] !== '' ? ' · ' . $attempt['course'] : '')); ?></small>
                            <?php if ($attempt['resumeurl'] !== '') { ?>
                                <a class="exam-history__resume" href="<?php echo s($attempt['resumeurl']); ?>">
                                    <?php echo s($attempt['resumelabel']); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div>
                            <span class="exam-history__label"><?php echo $ises ? 'Resultado' : 'Result'; ?></span>
                            <strong><?php echo s($attempt['score'] . '/' . $attempt['total'] . ' · ' . $attempt['percentage'] . '%'); ?></strong>
                        </div>
                        <div>
                            <span class="exam-history__label"><?php echo $ises ? 'Estado' : 'Status'; ?></span>
                            <strong class="exam-history__status"><?php echo s($attempt['status']); ?></strong>
                        </div>
                        <div>
                            <span class="exam-history__label"><?php echo $ises ? 'Duración' : 'Duration'; ?></span>
                            <strong><?php echo s($attempt['duration']); ?></strong>
                        </div>
                    </article>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($totalpages > 1) {
            $previousurl = new moodle_url('/blocks/menu/exam-history.php', [
                'type' => $historytype,
                'page' => max(0, $page - 1),
            ] + $sortparams + $languageparams);
            $nexturl = new moodle_url('/blocks/menu/exam-history.php', [
                'type' => $historytype,
                'page' => min($totalpages - 1, $page + 1),
            ] + $sortparams + $languageparams);
        ?>
            <nav class="exam-history__pagination" aria-label="<?php echo $ises ? 'Paginación' : 'Pagination'; ?>">
                <?php if ($page > 0) { ?><a rel="prev" href="<?php echo $previousurl->out(false); ?>"><?php echo $ises ? '← Anterior' : '← Previous'; ?></a><?php } ?>
                <span><?php echo $ises ? 'Página' : 'Page'; ?> <?php echo $page + 1; ?> / <?php echo $totalpages; ?></span>
                <?php if ($page < $totalpages - 1) { ?><a rel="next" href="<?php echo $nexturl->out(false); ?>"><?php echo $ises ? 'Siguiente →' : 'Next →'; ?></a><?php } ?>
            </nav>
        <?php } ?>
    </section>
    <?php } ?>
</main>
<?php
echo $OUTPUT->footer();
