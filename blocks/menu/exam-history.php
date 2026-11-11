<?php
// Unified exam and quiz attempt history for the current user.

require_once(__DIR__ . '/../../config.php');

require_login();

$requestedui = optional_param('ui', '', PARAM_ALPHA);
$requestedlang = optional_param('lang', '', PARAM_ALPHANUMEXT);
$displaylang = $requestedui !== '' ? $requestedui : ($requestedlang !== '' ? $requestedlang : current_language());
$ises = preg_match('/^es(?:[_-].*)?$/i', $displaylang) === 1;
$historytype = optional_param('type', 'all', PARAM_ALPHA);
if (!in_array($historytype, ['all', 'board', 'quiz', 'catalog'], true)) {
    $historytype = 'all';
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
] + $sortparams + $languageparams);
$dashboardurl = new moodle_url('/my/', [], 'board-level-herbology');
$languageurl = new moodle_url('/blocks/menu/exam-history.php', [
    'type' => $historytype,
    'page' => $page,
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

$catalogrows = [];
$catalogboardcounts = [
    'linear' => ['attempts' => 0, 'completed' => 0],
    'adaptive' => ['attempts' => 0, 'completed' => 0],
    'clinicalcases' => ['attempts' => 0, 'completed' => 0],
];
$customtable = new xmldb_table('block_menu_exam_attempts');
if ($DB->get_manager()->table_exists($customtable)) {
    $catalogboardattempts = $DB->get_records_select(
        'block_menu_exam_attempts',
        'userid = :userid',
        ['userid' => $USER->id],
        '',
        'id, examtype'
    );
    foreach ($catalogboardattempts as $catalogattempt) {
        $catalogtype = (string) $catalogattempt->examtype;
        $catalogbase = '';
        if (in_array($catalogtype, ['linear', 'linearincomplete'], true)) {
            $catalogbase = 'linear';
        } else if (in_array($catalogtype, ['adaptive', 'adaptiveincomplete'], true)) {
            $catalogbase = 'adaptive';
        } else if (in_array($catalogtype, ['clinicalcases', 'clinicalincomplete'], true)) {
            $catalogbase = 'clinicalcases';
        }
        if ($catalogbase === '') {
            continue;
        }
        $catalogboardcounts[$catalogbase]['attempts']++;
        if (!str_ends_with($catalogtype, 'incomplete')) {
            $catalogboardcounts[$catalogbase]['completed']++;
        }
    }
}
$boardcatalogdefinitions = [
    'linear' => [
        'topic' => $ises ? 'Examen Board lineal' : 'Linear Board Exam',
        'url' => new moodle_url('/blocks/menu/herbology-board.php', [
            'track' => 'exams',
            'exam' => 'linear',
        ] + $languageparams),
    ],
    'adaptive' => [
        'topic' => $ises ? 'Examen Board adaptativo' : 'Adaptive Board Exam',
        'url' => new moodle_url('/blocks/menu/herbology-board.php', [
            'track' => 'exams',
            'exam' => 'adaptive',
        ] + $languageparams),
    ],
    'clinicalcases' => [
        'topic' => $ises ? 'Casos clínicos' : 'Clinical Cases',
        'url' => new moodle_url('/blocks/menu/herbology-board.php', [
            'track' => 'clinicalcases',
        ] + $languageparams),
    ],
];
foreach ($boardcatalogdefinitions as $catalogkey => $catalogdefinition) {
    $attemptcount = $catalogboardcounts[$catalogkey]['attempts'];
    $completedcount = $catalogboardcounts[$catalogkey]['completed'];
    if ($completedcount > 0) {
        $catalogstatus = $ises ? 'Completado' : 'Completed';
        $catalogstatusclass = 'is-completed';
    } else if ($attemptcount > 0) {
        $catalogstatus = $ises ? 'En progreso' : 'In progress';
        $catalogstatusclass = 'is-progress';
    } else {
        $catalogstatus = $ises ? 'No intentado' : 'Not attempted';
        $catalogstatusclass = 'is-not-started';
    }
    $catalogrows[] = [
        'key' => 'board-' . $catalogkey,
        'topic' => $catalogdefinition['topic'],
        'source' => $ises ? 'Evaluación Board' : 'Board assessment',
        'course' => $ises ? 'Herbología China Board Level' : 'Chinese Herbology Board Level',
        'status' => $catalogstatus,
        'statusclass' => $catalogstatusclass,
        'attempts' => $attemptcount,
        'url' => $catalogdefinition['url']->out(false),
        'action' => $ises ? 'Abrir' : 'Open',
    ];
}

$quizcatalogtable = new xmldb_table('quiz');
if ($DB->get_manager()->table_exists($quizcatalogtable)) {
    $quizcatalogrecords = $DB->get_records_sql(
        "SELECT q.id, q.name AS topic, c.fullname AS coursename, cm.id AS cmid,
                COUNT(qa.id) AS attemptcount,
                SUM(CASE WHEN qa.state = 'finished' THEN 1 ELSE 0 END) AS completedcount,
                SUM(CASE WHEN qa.state IN ('inprogress', 'overdue') THEN 1 ELSE 0 END) AS activecount,
                MAX(CASE WHEN qa.state IN ('inprogress', 'overdue') THEN qa.id ELSE 0 END) AS activeattemptid
           FROM {quiz} q
           JOIN {course} c ON c.id = q.course
           JOIN {modules} m ON m.name = :catalogquizmodule
           JOIN {course_modules} cm ON cm.module = m.id
                AND cm.instance = q.id AND cm.course = q.course
      LEFT JOIN {quiz_attempts} qa ON qa.quiz = q.id
                AND qa.userid = :cataloguserid AND qa.preview = 0
          WHERE cm.visible = 1 AND cm.deletioninprogress = 0
       GROUP BY q.id, q.name, c.fullname, cm.id
       ORDER BY c.fullname ASC, q.name ASC",
        [
            'catalogquizmodule' => 'quiz',
            'cataloguserid' => $USER->id,
        ]
    );
    foreach ($quizcatalogrecords as $catalogquiz) {
        $modulecontext = context_module::instance((int) $catalogquiz->cmid, IGNORE_MISSING);
        if (!$modulecontext || !has_capability('mod/quiz:view', $modulecontext, $USER->id)) {
            continue;
        }
        $attemptcount = (int) $catalogquiz->attemptcount;
        $completedcount = (int) $catalogquiz->completedcount;
        $activecount = (int) $catalogquiz->activecount;
        $activeattemptid = (int) $catalogquiz->activeattemptid;
        if ($activecount > 0) {
            $catalogstatus = $ises ? 'En progreso' : 'In progress';
            $catalogstatusclass = 'is-progress';
        } else if ($completedcount > 0) {
            $catalogstatus = $ises ? 'Completado' : 'Completed';
            $catalogstatusclass = 'is-completed';
        } else {
            $catalogstatus = $ises ? 'No intentado' : 'Not attempted';
            $catalogstatusclass = 'is-not-started';
        }
        if ($activeattemptid > 0) {
            $catalogurl = new moodle_url('/mod/quiz/attempt.php', [
                'attempt' => $activeattemptid,
                'cmid' => (int) $catalogquiz->cmid,
            ]);
            $catalogaction = $ises ? 'Continuar' : 'Continue';
        } else {
            $catalogurl = new moodle_url('/mod/quiz/view.php', ['id' => (int) $catalogquiz->cmid]);
            $catalogaction = $ises ? 'Abrir' : 'Open';
        }
        $catalogrows[] = [
            'key' => 'quiz-' . (int) $catalogquiz->id,
            'topic' => (string) $catalogquiz->topic,
            'source' => $ises ? 'Quiz de Moodle' : 'Moodle Quiz',
            'course' => (string) $catalogquiz->coursename,
            'status' => $catalogstatus,
            'statusclass' => $catalogstatusclass,
            'attempts' => $attemptcount,
            'url' => $catalogurl->out(false),
            'action' => $catalogaction,
        ];
    }
}
usort($catalogrows, static function(array $left, array $right): int {
    $sourcecomparison = strnatcasecmp($left['source'], $right['source']);
    if ($sourcecomparison !== 0) {
        return $sourcecomparison;
    }
    $coursecomparison = strnatcasecmp($left['course'], $right['course']);
    if ($coursecomparison !== 0) {
        return $coursecomparison;
    }
    return strnatcasecmp($left['topic'], $right['topic']);
});
$catalogtotal = count($catalogrows);
$catalogcompleted = count(array_filter(
    $catalogrows,
    static fn(array $row): bool => $row['statusclass'] === 'is-completed'
));
$catalogprogress = count(array_filter(
    $catalogrows,
    static fn(array $row): bool => $row['statusclass'] === 'is-progress'
));
$catalognotstarted = $catalogtotal - $catalogcompleted - $catalogprogress;

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
        if (in_array($examtype, ['adaptive', 'adaptiveincomplete'], true)) {
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
            'source' => $ises ? 'Examen Board' : 'Board Exam',
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
    'catalog' => ($ises ? 'Catálogo de exámenes' : 'Exam Catalog') . ' (' . $catalogtotal . ')',
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
.exam-history__catalog-list{display:grid;gap:9px}
.exam-history__catalog-item{display:grid;grid-template-columns:1.35fr 1.15fr .7fr .55fr auto;align-items:center;gap:14px;padding:13px;border:1px solid #c7ddd9;border-radius:10px;background:#fff}
.exam-history__catalog-item>div{min-width:0}
.exam-history__catalog-item strong{display:block;overflow-wrap:anywhere;font-size:14px}
.exam-history__catalog-item small{display:block;margin-top:3px;color:#61758a;overflow-wrap:anywhere}
.exam-history__catalog-status.is-completed{color:#087c3b}
.exam-history__catalog-status.is-progress{color:#9a6700}
.exam-history__catalog-status.is-not-started{color:#526b84}
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
@media(max-width:850px){.exam-history__attempt{grid-template-columns:1fr 1fr}.exam-history__catalog-summary{grid-template-columns:1fr 1fr}.exam-history__catalog-item{grid-template-columns:1fr 1fr}.exam-history__catalog-item>a{justify-self:start}.exam-history__top{display:block}.exam-history__actions{justify-content:flex-start;margin-top:14px}.exam-history__sort{align-items:stretch}.exam-history__sort label{flex:1 1 150px}.exam-history__sort button{flex:1 1 100%}}
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
            ] + $sortparams + $languageparams);
        ?>
            <a href="<?php echo $filterurl->out(false); ?>"<?php echo $historytype === $filterkey ? ' aria-current="page"' : ''; ?>><?php echo s($filterlabel); ?></a>
        <?php } ?>
    </nav>

    <?php if ($historytype !== 'catalog') { ?>
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

    <?php if ($historytype === 'catalog') { ?>
    <section class="exam-history__panel" aria-labelledby="exam-catalog-title">
        <header class="exam-history__panel-header">
            <div>
                <span><?php echo $ises ? 'INVENTARIO DE EVALUACIONES' : 'ASSESSMENT INVENTORY'; ?></span>
                <h3 id="exam-catalog-title"><?php echo $ises ? 'Catálogo completo de exámenes' : 'Complete Exam Catalog'; ?></h3>
            </div>
            <small><?php echo $catalogtotal; ?> <?php echo $ises ? 'evaluaciones' : 'assessments'; ?></small>
        </header>

        <div class="exam-history__catalog-summary" aria-label="<?php echo $ises ? 'Resumen del catálogo' : 'Catalog summary'; ?>">
            <div class="exam-history__catalog-stat">
                <span><?php echo $ises ? 'Total' : 'Total'; ?></span>
                <strong><?php echo $catalogtotal; ?></strong>
            </div>
            <div class="exam-history__catalog-stat">
                <span><?php echo $ises ? 'Completados' : 'Completed'; ?></span>
                <strong><?php echo $catalogcompleted; ?></strong>
            </div>
            <div class="exam-history__catalog-stat">
                <span><?php echo $ises ? 'En progreso' : 'In progress'; ?></span>
                <strong><?php echo $catalogprogress; ?></strong>
            </div>
            <div class="exam-history__catalog-stat">
                <span><?php echo $ises ? 'No intentados' : 'Not attempted'; ?></span>
                <strong><?php echo $catalognotstarted; ?></strong>
            </div>
        </div>

        <?php if (!$catalogrows) { ?>
            <p class="exam-history__empty"><?php echo $ises
                ? 'No se encontraron evaluaciones disponibles.'
                : 'No available assessments were found.'; ?></p>
        <?php } else { ?>
            <div class="exam-history__catalog-list">
                <?php foreach ($catalogrows as $catalogrow) { ?>
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
                            <span class="exam-history__label"><?php echo $ises ? 'Estado' : 'Status'; ?></span>
                            <strong class="exam-history__catalog-status <?php echo s($catalogrow['statusclass']); ?>">
                                <?php echo s($catalogrow['status']); ?>
                            </strong>
                        </div>
                        <div>
                            <span class="exam-history__label"><?php echo $ises ? 'Intentos' : 'Attempts'; ?></span>
                            <strong><?php echo (int) $catalogrow['attempts']; ?></strong>
                        </div>
                        <a class="exam-history__catalog-open" href="<?php echo s($catalogrow['url']); ?>">
                            <?php echo s($catalogrow['action']); ?> →
                        </a>
                    </article>
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
