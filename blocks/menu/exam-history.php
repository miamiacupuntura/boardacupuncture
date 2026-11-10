<?php
// Unified exam and quiz attempt history for the current user.

require_once(__DIR__ . '/../../config.php');

require_login();

$requestedui = optional_param('ui', '', PARAM_ALPHA);
$requestedlang = optional_param('lang', '', PARAM_ALPHANUMEXT);
$displaylang = $requestedui !== '' ? $requestedui : ($requestedlang !== '' ? $requestedlang : current_language());
$ises = preg_match('/^es(?:[_-].*)?$/i', $displaylang) === 1;
$historytype = optional_param('type', 'all', PARAM_ALPHA);
if (!in_array($historytype, ['all', 'board', 'quiz'], true)) {
    $historytype = 'all';
}
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 25;
$languageparams = $ises ? ['lang' => 'es', 'ui' => 'es'] : ['lang' => 'en', 'ui' => 'en'];
$pageurl = new moodle_url('/blocks/menu/exam-history.php', [
    'type' => $historytype,
    'page' => $page,
] + $languageparams);
$dashboardurl = new moodle_url('/my/', [], 'board-level-herbology');
$languageurl = new moodle_url('/blocks/menu/exam-history.php', [
    'type' => $historytype,
    'page' => $page,
] + ($ises ? ['lang' => 'en', 'ui' => 'en'] : ['lang' => 'es', 'ui' => 'es']));

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
        $boardresumeurl = null;
        if ($isincomplete) {
            if (in_array($examtype, ['clinicalcases', 'clinicalincomplete'], true)) {
                $boardresumeurl = new moodle_url('/blocks/menu/herbology-board.php', [
                    'track' => 'clinicalcases',
                ] + $languageparams);
            } else {
                $boardresumemode = in_array($examtype, ['adaptive', 'adaptiveincomplete'], true)
                    ? 'adaptive'
                    : 'linear';
                $boardresumeurl = new moodle_url('/blocks/menu/herbology-board.php', [
                    'track' => 'exams',
                    'exam' => $boardresumemode,
                ] + $languageparams);
            }
        }
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
            'resumeurl' => $boardresumeurl ? $boardresumeurl->out(false) : '',
            'resumelabel' => $ises ? 'Retomar examen →' : 'Resume Exam →',
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
            'resumeurl' => $resumeurl ? $resumeurl->out(false) : '',
            'resumelabel' => $ises ? 'Retomar quiz →' : 'Resume Quiz →',
        ];
    }
}

usort($historyrows, static function(array $left, array $right): int {
    if ($left['timestamp'] === $right['timestamp']) {
        return strcmp($right['key'], $left['key']);
    }
    return $right['timestamp'] <=> $left['timestamp'];
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
.exam-history__panel{padding:18px;border:1px solid #b9d8d2;border-top:4px solid #087c69;border-radius:13px;background:#f8fcfb}
.exam-history__panel-header{display:flex;align-items:end;justify-content:space-between;gap:16px;margin-bottom:14px}
.exam-history__panel-header span{display:block;color:#087c69;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}
.exam-history__panel-header h3{margin:4px 0 0;font-size:21px}
.exam-history__panel-header small{color:#526b84;font-weight:700}
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
@media(max-width:850px){.exam-history__attempt{grid-template-columns:1fr 1fr}.exam-history__top{display:block}.exam-history__actions{justify-content:flex-start;margin-top:14px}}
@media(max-width:520px){.exam-history__attempt{grid-template-columns:1fr}.exam-history{width:min(100% - 16px,1180px)}}
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
            ] + $languageparams);
        ?>
            <a href="<?php echo $filterurl->out(false); ?>"<?php echo $historytype === $filterkey ? ' aria-current="page"' : ''; ?>><?php echo s($filterlabel); ?></a>
        <?php } ?>
    </nav>

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
            ] + $languageparams);
            $nexturl = new moodle_url('/blocks/menu/exam-history.php', [
                'type' => $historytype,
                'page' => min($totalpages - 1, $page + 1),
            ] + $languageparams);
        ?>
            <nav class="exam-history__pagination" aria-label="<?php echo $ises ? 'Paginación' : 'Pagination'; ?>">
                <?php if ($page > 0) { ?><a rel="prev" href="<?php echo $previousurl->out(false); ?>"><?php echo $ises ? '← Anterior' : '← Previous'; ?></a><?php } ?>
                <span><?php echo $ises ? 'Página' : 'Page'; ?> <?php echo $page + 1; ?> / <?php echo $totalpages; ?></span>
                <?php if ($page < $totalpages - 1) { ?><a rel="next" href="<?php echo $nexturl->out(false); ?>"><?php echo $ises ? 'Siguiente →' : 'Next →'; ?></a><?php } ?>
            </nav>
        <?php } ?>
    </section>
</main>
<?php
echo $OUTPUT->footer();
