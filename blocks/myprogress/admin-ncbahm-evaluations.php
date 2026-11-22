<?php
// Administrator report for NCBAHM quiz, simulation and Board exam attempts.

require_once(__DIR__ . '/../../config.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$search = trim(optional_param('q', '', PARAM_TEXT));
$assessmenttype = optional_param('assessment', 'all', PARAM_ALPHA);
$statusfilter = optional_param('status', 'all', PARAM_ALPHA);
$detailuserid = optional_param('userid', 0, PARAM_INT);
$sort = optional_param('sort', 'recent', PARAM_ALPHA);
$export = optional_param('export', '', PARAM_ALPHA);
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 50;

if (!in_array($assessmenttype, ['all', 'quiz', 'simulation', 'exam'], true)) {
    $assessmenttype = 'all';
}
if (!in_array($statusfilter, ['all', 'passed', 'failed', 'practice', 'incomplete'], true)) {
    $statusfilter = 'all';
}
if (!in_array($sort, ['recent', 'name', 'result'], true)) {
    $sort = 'recent';
}

$reporturl = new moodle_url('/blocks/myprogress/admin-ncbahm-evaluations.php');
$scormreporturl = new moodle_url('/blocks/myprogress/admin-student-evaluations.php');
$allprogramsurl = new moodle_url('/blocks/myprogress/admin-student-evaluations.php', ['view' => 'courses']);
$ncbahmstudentids = null;
$ncbahmcontext = context_course::instance(116, IGNORE_MISSING);
if ($ncbahmcontext) {
    $ncbahmmembers = get_enrolled_users(
        $ncbahmcontext,
        '',
        0,
        'u.id,u.firstname,u.lastname,u.email',
        '',
        0,
        0,
        false
    );
    $ncbahmmembers = array_filter($ncbahmmembers, static function($user) use ($ncbahmcontext): bool {
        return !is_siteadmin($user->id)
            && !has_capability('moodle/grade:viewall', $ncbahmcontext, $user->id)
            && !has_capability('moodle/course:manageactivities', $ncbahmcontext, $user->id);
    });
    $ncbahmstudentids = array_fill_keys(array_map('intval', array_keys($ncbahmmembers)), true);
}
$pageparams = array_filter([
    'q' => $search !== '' ? $search : null,
    'assessment' => $assessmenttype !== 'all' ? $assessmenttype : null,
    'status' => $statusfilter !== 'all' ? $statusfilter : null,
    'sort' => $sort !== 'recent' ? $sort : null,
    'userid' => $detailuserid ?: null,
]);
$detailparams = $pageparams;
$studentlistparams = array_diff_key($pageparams, ['userid' => true]);
if ($page > 0) {
    $detailparams['page'] = $page;
    $studentlistparams['page'] = $page;
}

$PAGE->set_url(new moodle_url($reporturl, $pageparams));
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('report');
$PAGE->set_title('Student Evaluations');
$PAGE->set_heading('Student Evaluations');
$PAGE->set_cacheable(false);

$quiznames = [
    'bqd_duiyao' => 'Dui Yao Quiz',
    'bqm_families' => 'Formula Families and Variations',
    'bqm_nametraps' => 'Name Traps',
    'bqm_herbparts' => 'Herb Families and Parts',
    'bqm_formulanames' => 'Formula Name Traps',
    'bqm_misleading' => 'Misleading Formula Names',
    'bqm_advanced' => 'Advanced Name Traps',
    'bqm_supplements' => 'Supplemental Herb Families',
    'bqm_secondary' => 'Secondary Herb Actions',
];
$formulabankdirectories = [
    $CFG->dirroot . '/study-guide/comparisons',
    '/var/www/acupuncturetests-study-guide/current/comparisons',
    dirname($CFG->dirroot) . '/study-guide/comparisons',
    '/var/www/html/study-guide/comparisons',
];
foreach (array_unique($formulabankdirectories) as $directory) {
    $formulabankpath = rtrim($directory, '/') . '/formula-practice-data.json';
    if (!is_readable($formulabankpath)) continue;

    $formulafiles = [$formulabankpath];
    $manifestpath = rtrim($directory, '/') . '/formula-modifications-manifest.json';
    if (is_readable($manifestpath)) {
        $manifest = json_decode(file_get_contents($manifestpath), true);
        foreach ($manifest['files'] ?? [] as $filename) {
            $filename = basename((string)$filename);
            $supplementpath = rtrim($directory, '/') . '/' . $filename;
            if ($filename !== '' && is_readable($supplementpath)) {
                $formulafiles[] = $supplementpath;
            }
        }
    }

    foreach (array_unique($formulafiles) as $formulafile) {
        $formulabank = json_decode(file_get_contents($formulafile), true);
        foreach ($formulabank['categories'] ?? [] as $category) {
            $key = clean_param($category['key'] ?? '', PARAM_ALPHANUMEXT);
            $englishname = trim((string)($category['en'] ?? ''));
            if ($key !== '' && $englishname !== '') {
                $quiznames['bqf_' . substr(sha1($key), 0, 12)] = 'Formula Quiz · ' . $englishname;
            }
        }
    }
    $quiznames['bqf_modifications'] = 'Formula Modifications Quiz';
    break;
}

$attemptkind = static function(string $examtype): string {
    if (str_starts_with($examtype, 'bq')) {
        return 'quiz';
    }
    if (str_starts_with($examtype, 'simulation')) {
        return 'simulation';
    }
    return 'exam';
};
$attemptname = static function(string $examtype) use (&$quiznames): string {
    $base = str_ends_with($examtype, 'incomplete')
        ? substr($examtype, 0, -strlen('incomplete'))
        : $examtype;
    if (isset($quiznames[$base])) return $quiznames[$base];
    if (preg_match('/^bqh_(\d+)$/', $base, $matches)) return 'Single Herbs Quiz · Chapter ' . $matches[1];
    if (preg_match('/^simulation(\d+)(linear|adaptive|officialadaptive)$/', $base, $matches)) {
        $mode = $matches[2] === 'officialadaptive'
            ? 'NCBAHM 2026 blueprint'
            : ($matches[2] === 'adaptive' ? 'Current adaptive' : 'Linear');
        return 'Exam ' . (int)$matches[1] . ' · ' . $mode;
    }
    if (preg_match('/^(?:exam|e)(\d+)(linear|adaptive)$/', $base, $matches)) {
        return 'Exam ' . (int)$matches[1] . ' · ' . ucfirst($matches[2]);
    }
    if (str_starts_with($base, 'bqf_')) return 'Formula Quiz · supplemental category';
    if (str_starts_with($base, 'bqd_')) return 'Dui Yao Quiz';
    if (str_starts_with($base, 'bqm_')) return 'Supplemental Quiz · ' . substr($base, 4);
    $names = [
        'simulationlinear' => 'Exam 1 · Linear',
        'simulationadaptive' => 'Exam 1 · Adaptive',
        'simulation1linear' => 'Exam 1 · Linear',
        'simulation1adaptive' => 'Exam 1 · Adaptive',
        'simulation2linear' => 'Exam 2 · Linear',
        'simulation2adaptive' => 'Exam 2 · Adaptive',
        'linear' => 'Exam 1 · Linear',
        'adaptive' => 'Exam 1 · Adaptive',
        'exam2linear' => 'Exam 2 · Linear',
        'exam2adaptive' => 'Exam 2 · Adaptive',
        'e2linear' => 'Exam 2 · Linear',
        'e2adaptive' => 'Exam 2 · Adaptive',
        'clinicalcases' => 'Clinical Cases Exam',
    ];
    return $names[$base] ?? ucwords(str_replace(['_', '-'], ' ', $base));
};
$kindname = static function(string $kind): string {
    return ['quiz' => 'Quiz', 'simulation' => 'Simulation', 'exam' => 'Exam'][$kind] ?? 'Evaluation';
};
$durationvalue = static function(int $seconds): string {
    $seconds = max(0, $seconds);
    $minutes = intdiv($seconds, 60);
    return $minutes > 0 ? $minutes . ' min ' . ($seconds % 60) . ' s' : $seconds . ' s';
};
$datevalue = static function(int $timestamp): string {
    return $timestamp > 0 ? userdate($timestamp, get_string('strftimedatetimeshort', 'langconfig')) : 'No activity';
};

$attempts = [];
$attempttable = new xmldb_table('block_menu_exam_attempts');
$tableavailable = $DB->get_manager()->table_exists($attempttable);
if ($tableavailable) {
    $records = $DB->get_records_sql(
        "SELECT a.id, a.userid, a.examtype, a.score, a.total, a.duration, a.language, a.timecompleted,
                u.firstname, u.lastname, u.email
           FROM {block_menu_exam_attempts} a
           JOIN {user} u ON u.id = a.userid
          WHERE u.deleted = 0
       ORDER BY a.timecompleted DESC, a.id DESC"
    );
    foreach ($records as $record) {
        $recorduserid = (int)$record->userid;
        if (is_siteadmin($recorduserid)
                || ($ncbahmstudentids !== null && !isset($ncbahmstudentids[$recorduserid]))) {
            continue;
        }
        $kind = $attemptkind((string)$record->examtype);
        $incomplete = str_ends_with((string)$record->examtype, 'incomplete');
        $percentage = (int)$record->total > 0
            ? round(((float)$record->score / (float)$record->total) * 100, 1)
            : 0.0;
        $isofficialblueprint = str_ends_with((string)$record->examtype, 'officialadaptive');
        $status = $incomplete
            ? 'incomplete'
            : ($isofficialblueprint ? 'practice' : ($percentage >= 70 ? 'passed' : 'failed'));
        $fullname = fullname($record);
        if ($assessmenttype !== 'all' && $kind !== $assessmenttype) continue;
        if ($statusfilter !== 'all' && $status !== $statusfilter) continue;
        if ($search !== '') {
            $haystack = core_text::strtolower($fullname . ' ' . $record->email);
            if (strpos($haystack, core_text::strtolower($search)) === false) continue;
        }
        $attempts[] = [
            'id' => (int)$record->id,
            'userid' => (int)$record->userid,
            'student' => $fullname,
            'email' => (string)$record->email,
            'examtype' => (string)$record->examtype,
            'kind' => $kind,
            'kindname' => $kindname($kind),
            'name' => $attemptname((string)$record->examtype),
            'score' => (int)$record->score,
            'total' => (int)$record->total,
            'percentage' => $percentage,
            'status' => $status,
            'duration' => (int)$record->duration,
            'timecompleted' => (int)$record->timecompleted,
            'openasurl' => (new moodle_url('/blocks/menu/admin-open-assessment-as.php', [
                'attemptid' => (int)$record->id,
                'sesskey' => sesskey(),
            ]))->out(false),
        ];
    }
}

$studentstats = [];
foreach ($attempts as $attempt) {
    $userid = $attempt['userid'];
    if (!isset($studentstats[$userid])) {
        $studentstats[$userid] = [
            'name' => $attempt['student'], 'email' => $attempt['email'], 'sum' => 0.0,
            'completed' => 0, 'passed' => 0, 'attempts' => 0, 'lastactivity' => 0,
            'quiz' => 0, 'simulation' => 0, 'exam' => 0,
        ];
    }
    $studentstats[$userid]['attempts']++;
    $studentstats[$userid][$attempt['kind']]++;
    $studentstats[$userid]['lastactivity'] = max($studentstats[$userid]['lastactivity'], $attempt['timecompleted']);
    if ($attempt['status'] !== 'incomplete') {
        $studentstats[$userid]['completed']++;
        $studentstats[$userid]['sum'] += $attempt['percentage'];
        if ($attempt['status'] === 'passed') $studentstats[$userid]['passed']++;
    }
}
foreach ($studentstats as &$stat) {
    $stat['average'] = $stat['completed'] > 0 ? round($stat['sum'] / $stat['completed'], 1) : null;
}
unset($stat);

uasort($studentstats, static function(array $left, array $right) use ($sort): int {
    if ($sort === 'name') return strcasecmp($left['name'], $right['name']);
    if ($sort === 'result') return ($right['average'] ?? -1) <=> ($left['average'] ?? -1);
    return $right['lastactivity'] <=> $left['lastactivity'];
});

$selectedstudent = $detailuserid > 0 && isset($studentstats[$detailuserid]) ? $studentstats[$detailuserid] : null;
$selectedattempts = $selectedstudent === null ? [] : array_values(array_filter(
    $attempts,
    static fn(array $attempt): bool => $attempt['userid'] === $detailuserid
));

$selectedanalytics = null;
if ($selectedstudent !== null) {
    $completedattempts = array_values(array_filter(
        $selectedattempts,
        static fn(array $attempt): bool => $attempt['status'] !== 'incomplete'
    ));
    $answeredquestions = array_sum(array_column($completedattempts, 'total'));
    $correctanswers = array_sum(array_column($completedattempts, 'score'));
    $accuracy = $answeredquestions > 0
        ? round(($correctanswers / $answeredquestions) * 100)
        : 0;
    $attemptedkinds = array_fill_keys(array_column($selectedattempts, 'kind'), true);
    $coverage = round((count($attemptedkinds) / 3) * 100);
    $consistencyattempts = array_values(array_filter(
        $completedattempts,
        static fn(array $attempt): bool => $attempt['status'] !== 'practice'
    ));
    $passedattempts = count(array_filter(
        $consistencyattempts,
        static fn(array $attempt): bool => $attempt['status'] === 'passed'
    ));
    $consistency = $consistencyattempts !== []
        ? round(($passedattempts / count($consistencyattempts)) * 100)
        : 0;

    $attemptgroups = [];
    foreach ($completedattempts as $attempt) {
        $attemptkey = str_ends_with($attempt['examtype'], 'incomplete')
            ? substr($attempt['examtype'], 0, -strlen('incomplete'))
            : $attempt['examtype'];
        $attemptgroups[$attemptkey][] = $attempt;
    }
    $repeatedattempts = [];
    foreach ($attemptgroups as $group) {
        if (count($group) >= 2) {
            $repeatedattempts = array_merge($repeatedattempts, $group);
        }
    }
    $repeatedtotal = array_sum(array_column($repeatedattempts, 'total'));
    $repeatedscore = array_sum(array_column($repeatedattempts, 'score'));
    $retention = $repeatedtotal > 0
        ? round(($repeatedscore / $repeatedtotal) * 100)
        : null;
    $readiness = round(
        ($accuracy * 0.40)
        + ($coverage * 0.25)
        + (($retention ?? 0) * 0.20)
        + ($consistency * 0.15)
    );
    $readinesslabel = $readiness >= 90 ? 'Consistent mastery'
        : ($readiness >= 75 ? 'Strong preparation'
        : ($readiness >= 60 ? 'Progressing'
        : ($readiness >= 40 ? 'Developing' : 'Building foundations')));

    $kindlabels = ['quiz' => 'Quiz', 'simulation' => 'Simulations', 'exam' => 'Exams'];
    $kindperformance = [];
    foreach ($kindlabels as $kind => $label) {
        $kindattempts = array_values(array_filter(
            $completedattempts,
            static fn(array $attempt): bool => $attempt['kind'] === $kind
        ));
        $kindtotal = array_sum(array_column($kindattempts, 'total'));
        $kindscore = array_sum(array_column($kindattempts, 'score'));
        $kindperformance[] = [
            'label' => $label,
            'attempts' => count($kindattempts),
            'average' => $kindtotal > 0 ? round(($kindscore / $kindtotal) * 100) : null,
        ];
    }

    $reviewattempts = array_values(array_filter(
        $selectedattempts,
        static fn(array $attempt): bool => in_array($attempt['status'], ['failed', 'incomplete'], true)
    ));
    usort($reviewattempts, static fn(array $left, array $right): int =>
        $left['percentage'] <=> $right['percentage']
    );
    $reviewattempts = array_slice($reviewattempts, 0, 5);

    $recommendations = [];
    if (!isset($attemptedkinds['quiz'])) {
        $recommendations[] = ['Start a Quiz diagnostic', 'Complete a Quiz to add question-level evidence to this overview.'];
    }
    if (!isset($attemptedkinds['simulation'])) {
        $recommendations[] = ['Add a Simulation', 'Use a Simulation to measure clinical decision-making under a different format.'];
    }
    if ($reviewattempts !== []) {
        $recommendations[] = ['Review the lowest result', 'Revisit ' . $reviewattempts[0]['name'] . ' before the next attempt.'];
    }
    if ($retention === null) {
        $recommendations[] = ['Confirm retention', 'Repeat one completed evaluation to compare performance over time.'];
    }
    if ($recommendations === []) {
        $recommendations[] = ['Maintain readiness', 'Continue with mixed Quiz, Simulation and Exam practice.'];
    }

    $selectedanalytics = [
        'accuracy' => $accuracy,
        'coverage' => $coverage,
        'retention' => $retention,
        'consistency' => $consistency,
        'readiness' => $readiness,
        'readinesslabel' => $readinesslabel,
        'answered' => $answeredquestions,
        'completed' => count($completedattempts),
        'kindperformance' => $kindperformance,
        'reviewattempts' => $reviewattempts,
        'recommendations' => array_slice($recommendations, 0, 3),
    ];
}

if ($export === 'csv') {
    $filename = 'ncbahm-evaluations-' . userdate(time(), '%Y-%m-%d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    $output = fopen('php://output', 'wb');
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, ['Student', 'Email', 'Type', 'Evaluation', 'Result', 'Status', 'Duration', 'Date']);
    foreach ($attempts as $attempt) {
        fputcsv($output, [
            $attempt['student'], $attempt['email'], $attempt['kindname'], $attempt['name'],
            $attempt['percentage'] . '%', $attempt['status'], $durationvalue($attempt['duration']),
            $datevalue($attempt['timecompleted']),
        ]);
    }
    fclose($output);
    exit;
}

$totalstudents = count($studentstats);
$pagedstudents = array_slice($studentstats, $page * $perpage, $perpage, true);
$exporturl = new moodle_url($reporturl, $pageparams + ['export' => 'csv']);

echo $OUTPUT->header();
?>
<style>
.ncbahm-report{max-width:1280px;margin:0 auto 40px;color:#173448}.ncbahm-report__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:16px;padding:22px 24px;border-left:5px solid #0b8f87;border-radius:12px;background:linear-gradient(135deg,#eef9f6,#fff8ef)}.ncbahm-report__hero h2{margin:0 0 6px}.ncbahm-report__hero p{margin:0;color:#52677a}.ncbahm-report__actions,.ncbahm-report__tabs{display:flex;flex-wrap:wrap;gap:8px}.ncbahm-report__button{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:8px 13px;border:1px solid #0b8f87;border-radius:8px;background:#0b8f87;color:#fff!important;font-weight:800;text-decoration:none}.ncbahm-report__button--secondary{border-color:#b8c8d2;background:#fff;color:#24465b!important}.ncbahm-report__button--compact{min-height:34px;padding:6px 9px;font-size:12px}.ncbahm-report__tabs{margin-bottom:14px}.ncbahm-report__tabs a[aria-current="page"]{background:#e5f6f3;color:#076d68!important}.ncbahm-report__filters{display:grid;grid-template-columns:minmax(220px,1fr) repeat(3,minmax(170px,.6fr)) auto;gap:10px;margin-bottom:18px;padding:15px;border:1px solid #d7e2e7;border-radius:11px;background:#fff}.ncbahm-report__filters label span{display:block;margin-bottom:5px;font-size:12px;font-weight:800}.ncbahm-report__filters input,.ncbahm-report__filters select{width:100%;min-height:40px;box-sizing:border-box;padding:7px 9px;border:1px solid #b9cbd4;border-radius:7px;background:#fff}.ncbahm-report__filter-actions{display:flex;align-items:end;gap:7px}.ncbahm-report__panel{margin-bottom:18px;overflow:hidden;border:1px solid #d7e2e7;border-radius:12px;background:#fff}.ncbahm-report__panel-head{display:flex;justify-content:space-between;gap:12px;padding:15px 17px;background:#f3f8f8}.ncbahm-report__panel-head h3{margin:0}.ncbahm-report__table-wrap{overflow-x:auto}.ncbahm-report table{width:100%;border-collapse:collapse}.ncbahm-report th,.ncbahm-report td{padding:11px 13px;border-top:1px solid #e1e8ec;text-align:left}.ncbahm-report th{background:#fbfcfd;font-size:11px;text-transform:uppercase}.ncbahm-report__score{color:#08746f;font-size:17px;font-weight:900}.ncbahm-report__status{display:inline-block;padding:5px 8px;border-radius:999px;font-size:12px;font-weight:850}.ncbahm-report__status--passed{background:#ddf6ec;color:#076c55}.ncbahm-report__status--failed{background:#fff0dd;color:#a34d00}.ncbahm-report__status--incomplete{background:#edf1f4;color:#596b78}.ncbahm-report__cards{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px}.ncbahm-report__card{padding:14px;border:1px solid #d7e2e7;border-radius:9px;background:#fff}.ncbahm-report__card span{display:block;color:#64748b;font-size:12px;font-weight:800}.ncbahm-report__card strong{display:block;margin-top:4px;color:#0b655f;font-size:24px}.ncbahm-report__empty{padding:30px!important;text-align:center!important;color:#64748b}.ncbahm-report__pagination{padding:14px;text-align:center}.ncbahm-report__pagination a{display:inline-block;margin:2px;padding:6px 10px;border:1px solid #cbd8de;border-radius:6px}@media(max-width:950px){.ncbahm-report__filters{grid-template-columns:1fr 1fr}}@media(max-width:650px){.ncbahm-report__hero{flex-direction:column}.ncbahm-report__filters,.ncbahm-report__cards{grid-template-columns:1fr}.ncbahm-report__filter-actions{align-items:stretch;flex-direction:column}}
.ncbahm-report__programs{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:0 0 10px}.ncbahm-report__program{display:flex;min-height:44px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b8c8d2;border-radius:8px;background:#fff;color:#24465b!important;font-weight:850;text-align:center;text-decoration:none}.ncbahm-report__program:hover{background:#f1f6f8;color:#173448!important;text-decoration:none}.ncbahm-report__program[aria-current="page"]{border-color:#0b8f87;background:#0b8f87;color:#fff!important}.ncbahm-report__section-title{margin:0 0 14px;padding:10px 14px;border-left:4px solid #0b8f87;border-radius:7px;background:#f3f8f8;color:#173448;font-weight:850}@media(max-width:700px){.ncbahm-report__programs{grid-template-columns:1fr}}
.ncbahm-report__hero{gap:20px;margin-bottom:18px;padding:23px 25px;box-shadow:0 3px 12px rgba(15,23,42,.06)}.ncbahm-report__hero h2{margin:0 0 6px;color:#102a3b;font-size:30px}.ncbahm-report__hero p{max-width:780px;line-height:1.5}.ncbahm-report__hero .ncbahm-report__button{min-height:42px;padding:9px 14px}
.ncbahm-report__row-actions{display:flex;flex-wrap:wrap;gap:7px}.ncbahm-report__button--student{border-color:#7047c8;background:#f5f1ff;color:#5630a8!important}.ncbahm-report__button--student:hover{background:#7047c8;color:#fff!important}@media(max-width:780px){.ncbahm-report__row-actions{min-width:190px;flex-direction:column}.ncbahm-report__row-actions .ncbahm-report__button{width:100%;box-sizing:border-box}}
.ncbahm-analytics{margin-top:24px;padding-top:22px;border-top:2px solid #e4ebef}.ncbahm-analytics__hero{margin-bottom:14px;padding:18px 20px;border:1px solid #f0c5a5;border-top:5px solid #ea6a19;border-radius:12px;background:linear-gradient(135deg,#fff8f1,#f7fbff)}.ncbahm-analytics__hero h3{margin:0 0 5px;font-size:25px}.ncbahm-analytics__hero p{margin:0;color:#526b84}.ncbahm-analytics__notice{margin-bottom:14px;padding:12px 14px;border-left:4px solid #0b8f87;border-radius:8px;background:#eef9f6;color:#285d54}.ncbahm-analytics__overview{display:grid;grid-template-columns:245px minmax(0,1fr);gap:14px;margin-bottom:14px}.ncbahm-analytics__panel{padding:17px;border:1px solid #d5e0e6;border-radius:12px;background:#fff}.ncbahm-analytics__overall{text-align:center}.ncbahm-analytics__ring{--score:0;width:145px;height:145px;display:grid;place-items:center;margin:0 auto 12px;border-radius:50%;background:conic-gradient(#ea6a19 calc(var(--score) * 1%),#e8eef2 0);position:relative}.ncbahm-analytics__ring:after{content:"";position:absolute;inset:13px;border-radius:50%;background:#fff}.ncbahm-analytics__ring strong{position:relative;z-index:1;font-size:38px}.ncbahm-analytics__overall h4{margin:0 0 5px;font-size:19px}.ncbahm-analytics__overall p{margin:0;color:#60758a;font-size:12px}.ncbahm-analytics__metrics{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.ncbahm-analytics__metric{padding:14px;border:1px solid #d8e2e8;border-radius:10px;background:#fbfdff}.ncbahm-analytics__metric-head{display:flex;align-items:baseline;justify-content:space-between;gap:10px}.ncbahm-analytics__metric h4{margin:0;font-size:14px}.ncbahm-analytics__metric strong{font-size:21px}.ncbahm-analytics__track{height:8px;margin:10px 0;border-radius:999px;background:#e5edf1;overflow:hidden}.ncbahm-analytics__track span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#0d8875,#44b59e)}.ncbahm-analytics__metric p{margin:0;color:#60758a;font-size:11px}.ncbahm-analytics__details{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(260px,.7fr);gap:14px;margin-bottom:14px}.ncbahm-analytics__panel h4{margin:0 0 5px;font-size:19px}.ncbahm-analytics__subcopy{margin:0 0 13px;color:#60758a;font-size:12px}.ncbahm-analytics__type{display:grid;grid-template-columns:125px 1fr 52px;align-items:center;gap:10px;margin-top:11px}.ncbahm-analytics__type span{font-weight:800}.ncbahm-analytics__type small{color:#60758a}.ncbahm-analytics__review{margin:0;padding:0;list-style:none}.ncbahm-analytics__review li{display:flex;justify-content:space-between;gap:12px;margin-top:8px;padding:10px;border-radius:8px;background:#f8fafc}.ncbahm-analytics__review strong{color:#a94408;white-space:nowrap}.ncbahm-analytics__empty{padding:18px;border-radius:8px;background:#f8fafc;color:#64748b;text-align:center}.ncbahm-analytics__steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.ncbahm-analytics__step{padding:14px;border:1px solid #d7dfea;border-top:4px solid #6d28d9;border-radius:10px;background:#fff}.ncbahm-analytics__step strong{display:block;margin-bottom:6px;color:#4c1d95}.ncbahm-analytics__step p{margin:0;color:#60758a;font-size:12px;line-height:1.45}.ncbahm-analytics__method{margin-top:14px;padding:12px 14px;border-left:4px solid #0b8f87;border-radius:8px;background:#effaf7;color:#285d54;font-size:12px;line-height:1.5}@media(max-width:800px){.ncbahm-analytics__overview,.ncbahm-analytics__details{grid-template-columns:1fr}.ncbahm-analytics__steps{grid-template-columns:1fr}}@media(max-width:560px){.ncbahm-analytics__metrics{grid-template-columns:1fr}.ncbahm-analytics__type{grid-template-columns:105px 1fr 45px}}
.ncbahm-report__status--practice{background:#f1e8ff;color:#6528a8}
</style>
<main class="ncbahm-report">
    <header class="ncbahm-report__hero"><div><h2>Student Evaluations</h2><p>Review NCBAHM Quiz, Simulation, and Exam results stored on the server.</p></div><div class="ncbahm-report__actions"><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo (new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php'))->out(false); ?>">Question Bank</a><a class="ncbahm-report__button" href="<?php echo $exporturl->out(false); ?>">Export CSV</a></div></header>
    <nav class="ncbahm-report__programs" aria-label="Evaluation program"><a class="ncbahm-report__program" href="<?php echo $scormreporturl->out(false); ?>">NCCAOM Focused Practice</a><a class="ncbahm-report__program" href="<?php echo $reporturl->out(false); ?>" aria-current="page">NCBAHM Comprehensive Review</a><a class="ncbahm-report__program" href="<?php echo $allprogramsurl->out(false); ?>">All Programs</a></nav>
    <div class="ncbahm-report__section-title">Student results</div>
    <?php if (!$tableavailable): ?><div class="alert alert-warning">NCBAHM history is not yet installed in the database.</div><?php endif; ?>
    <form class="ncbahm-report__filters" method="get" action="<?php echo $reporturl->out(false); ?>">
        <label><span>Search student</span><input type="search" name="q" value="<?php echo s($search); ?>" placeholder="Name or email address"></label>
        <label><span>Evaluation type</span><select name="assessment"><option value="all">All</option><option value="quiz"<?php echo $assessmenttype === 'quiz' ? ' selected' : ''; ?>>Quiz</option><option value="simulation"<?php echo $assessmenttype === 'simulation' ? ' selected' : ''; ?>>Simulation</option><option value="exam"<?php echo $assessmenttype === 'exam' ? ' selected' : ''; ?>>Exams</option></select></label>
        <label><span>Status</span><select name="status"><option value="all">All</option><option value="passed"<?php echo $statusfilter === 'passed' ? ' selected' : ''; ?>>Passed</option><option value="failed"<?php echo $statusfilter === 'failed' ? ' selected' : ''; ?>>Not passed</option><option value="practice"<?php echo $statusfilter === 'practice' ? ' selected' : ''; ?>>Practice result</option><option value="incomplete"<?php echo $statusfilter === 'incomplete' ? ' selected' : ''; ?>>Incomplete</option></select></label>
        <label><span>Sort by</span><select name="sort"><option value="recent">Recent activity</option><option value="name"<?php echo $sort === 'name' ? ' selected' : ''; ?>>Name</option><option value="result"<?php echo $sort === 'result' ? ' selected' : ''; ?>>Best average</option></select></label>
        <div class="ncbahm-report__filter-actions"><button class="ncbahm-report__button" type="submit">Apply</button><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo $reporturl->out(false); ?>">Clear</a></div>
    </form>
    <?php if ($selectedstudent === null): ?>
    <section class="ncbahm-report__panel"><header class="ncbahm-report__panel-head"><h3>Students with NCBAHM evaluations</h3><span><?php echo $totalstudents; ?> result<?php echo $totalstudents === 1 ? '' : 's'; ?></span></header><div class="ncbahm-report__table-wrap"><table><thead><tr><th>Student</th><th>Average</th><th>Quiz</th><th>Simulations</th><th>Exams</th><th>Last activity</th><th>Actions</th></tr></thead><tbody>
    <?php if ($pagedstudents === []): ?><tr><td class="ncbahm-report__empty" colspan="7">No NCBAHM evaluations match these filters.</td></tr><?php else: foreach ($pagedstudents as $userid => $stat): ?>
        <tr><td><strong><?php echo s($stat['name']); ?></strong><br><small><?php echo s($stat['email']); ?></small></td><td><span class="ncbahm-report__score"><?php echo $stat['average'] === null ? '—' : s($stat['average'] . '%'); ?></span></td><td><?php echo (int)$stat['quiz']; ?></td><td><?php echo (int)$stat['simulation']; ?></td><td><?php echo (int)$stat['exam']; ?></td><td><?php echo s($datevalue($stat['lastactivity'])); ?></td><td><div class="ncbahm-report__row-actions"><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo (new moodle_url($reporturl, array_merge($detailparams, ['userid' => $userid])))->out(false); ?>">View evaluations</a><a class="ncbahm-report__button ncbahm-report__button--student" href="<?php echo (new moodle_url('/blocks/myprogress/admin-open-student-dashboard.php', ['userid' => $userid, 'sesskey' => sesskey()]))->out(false); ?>">Open as student</a></div></td></tr>
    <?php endforeach; endif; ?></tbody></table></div>
    <?php if ($totalstudents > $perpage): ?><nav class="ncbahm-report__pagination"><?php for ($index = 0; $index < (int)ceil($totalstudents / $perpage); $index++): ?><a href="<?php echo (new moodle_url($reporturl, $pageparams + ['page' => $index]))->out(false); ?>"><?php echo $index + 1; ?></a><?php endfor; ?></nav><?php endif; ?>
    </section>
    <?php else: ?>
    <div class="ncbahm-report__actions" style="margin-bottom:14px"><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo (new moodle_url($reporturl, $studentlistparams))->out(false); ?>">← Back</a></div>
    <div class="ncbahm-report__cards"><article class="ncbahm-report__card"><span>Average</span><strong><?php echo $selectedstudent['average'] === null ? '—' : s($selectedstudent['average'] . '%'); ?></strong></article><article class="ncbahm-report__card"><span>Quiz</span><strong><?php echo (int)$selectedstudent['quiz']; ?></strong></article><article class="ncbahm-report__card"><span>Simulations</span><strong><?php echo (int)$selectedstudent['simulation']; ?></strong></article><article class="ncbahm-report__card"><span>Exams</span><strong><?php echo (int)$selectedstudent['exam']; ?></strong></article></div>
    <div class="alert alert-info">Supervised mode: the administrator temporarily switches to the selected student’s profile. The evaluation will display the student’s name, and the result will be saved exclusively to that profile.</div>
    <section class="ncbahm-report__panel"><header class="ncbahm-report__panel-head"><h3><?php echo s($selectedstudent['name']); ?></h3><span><?php echo count($selectedattempts); ?> attempt<?php echo count($selectedattempts) === 1 ? '' : 's'; ?></span></header><div class="ncbahm-report__table-wrap"><table><thead><tr><th>Type</th><th>Evaluation</th><th>Result</th><th>Status</th><th>Duration</th><th>Date</th><th>Action</th></tr></thead><tbody>
    <?php foreach ($selectedattempts as $attempt): $statuslabels = ['passed' => 'Passed', 'failed' => 'Not passed', 'practice' => 'Practice result', 'incomplete' => 'Incomplete']; ?>
        <tr><td><?php echo s($attempt['kindname']); ?></td><td><strong><?php echo s($attempt['name']); ?></strong></td><td><span class="ncbahm-report__score"><?php echo s($attempt['percentage'] . '%'); ?></span></td><td><span class="ncbahm-report__status ncbahm-report__status--<?php echo s($attempt['status']); ?>"><?php echo s($statuslabels[$attempt['status']]); ?></span></td><td><?php echo s($durationvalue($attempt['duration'])); ?></td><td><?php echo s($datevalue($attempt['timecompleted'])); ?></td><td><a class="ncbahm-report__button ncbahm-report__button--secondary ncbahm-report__button--compact" href="<?php echo s($attempt['openasurl']); ?>">Open as <?php echo s($attempt['student']); ?></a></td></tr>
    <?php endforeach; ?></tbody></table></div></section>
    <?php if ($selectedanalytics !== null): ?>
    <section class="ncbahm-analytics" aria-labelledby="student-study-score-title">
        <header class="ncbahm-analytics__hero">
            <h3 id="student-study-score-title">My Study Score (NCBAHM Comprehensive Review)</h3>
            <p>Performance charts for <?php echo s($selectedstudent['name']); ?> based on evaluations recorded on the server.</p>
        </header>
        <div class="ncbahm-analytics__notice"><strong>Student-specific server view:</strong> these charts use this student’s saved Quiz, Simulation and Exam attempts. They do not use the administrator’s browser history.</div>
        <div class="ncbahm-analytics__overview">
            <article class="ncbahm-analytics__panel ncbahm-analytics__overall">
                <div class="ncbahm-analytics__ring" style="--score:<?php echo (int)$selectedanalytics['readiness']; ?>"><strong><?php echo (int)$selectedanalytics['readiness']; ?></strong></div>
                <h4><?php echo s($selectedanalytics['readinesslabel']); ?></h4>
                <p><?php echo (int)$selectedanalytics['completed']; ?> completed attempts · <?php echo (int)$selectedanalytics['answered']; ?> scored questions</p>
            </article>
            <div class="ncbahm-analytics__metrics">
                <?php
                $metriccopy = [
                    ['Accuracy', 'accuracy', 'Correct answers across completed server attempts.'],
                    ['Evaluation coverage', 'coverage', 'Quiz, Simulation and Exam formats represented.'],
                    ['Retention', 'retention', 'Performance in evaluations attempted at least twice.'],
                    ['Consistency', 'consistency', 'Completed attempts meeting the 70% passing mark.'],
                ];
                foreach ($metriccopy as [$label, $key, $description]):
                    $metricvalue = $selectedanalytics[$key];
                ?>
                <article class="ncbahm-analytics__metric"><div class="ncbahm-analytics__metric-head"><h4><?php echo s($label); ?></h4><strong><?php echo $metricvalue === null ? '—' : (int)$metricvalue . '%'; ?></strong></div><div class="ncbahm-analytics__track"><span style="width:<?php echo $metricvalue === null ? 0 : (int)$metricvalue; ?>%"></span></div><p><?php echo s($description); ?></p></article>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ncbahm-analytics__details">
            <article class="ncbahm-analytics__panel">
                <h4>Performance by evaluation type</h4><p class="ncbahm-analytics__subcopy">Weighted average from the student’s completed attempts.</p>
                <?php foreach ($selectedanalytics['kindperformance'] as $kindrow): ?>
                <div class="ncbahm-analytics__type"><span><?php echo s($kindrow['label']); ?><br><small><?php echo (int)$kindrow['attempts']; ?> attempt<?php echo (int)$kindrow['attempts'] === 1 ? '' : 's'; ?></small></span><div class="ncbahm-analytics__track"><span style="width:<?php echo $kindrow['average'] === null ? 0 : (int)$kindrow['average']; ?>%"></span></div><strong><?php echo $kindrow['average'] === null ? '—' : (int)$kindrow['average'] . '%'; ?></strong></div>
                <?php endforeach; ?>
            </article>
            <article class="ncbahm-analytics__panel">
                <h4>Evaluations needing review</h4><p class="ncbahm-analytics__subcopy">Up to five incomplete or below-passing attempts.</p>
                <?php if ($selectedanalytics['reviewattempts'] === []): ?><div class="ncbahm-analytics__empty">No recorded evaluations currently need review.</div><?php else: ?><ul class="ncbahm-analytics__review"><?php foreach ($selectedanalytics['reviewattempts'] as $reviewattempt): ?><li><span><?php echo s($reviewattempt['name']); ?></span><strong><?php echo s($reviewattempt['percentage'] . '%'); ?></strong></li><?php endforeach; ?></ul><?php endif; ?>
            </article>
        </div>
        <article class="ncbahm-analytics__panel"><h4>Recommended next steps</h4><p class="ncbahm-analytics__subcopy">Recommendations generated from the selected student’s server history.</p><div class="ncbahm-analytics__steps"><?php foreach ($selectedanalytics['recommendations'] as $index => $recommendation): ?><div class="ncbahm-analytics__step"><strong><?php echo (int)$index + 1; ?>. <?php echo s($recommendation[0]); ?></strong><p><?php echo s($recommendation[1]); ?></p></div><?php endforeach; ?></div></article>
        <aside class="ncbahm-analytics__method"><strong>How this administrative score is calculated:</strong> 40% accuracy, 25% evaluation coverage, 20% retention and 15% consistency. Formula-level coverage remains part of the student’s device-based My Study Score until that detailed history is synchronized with the server.</aside>
    </section>
    <?php endif; ?>
    <?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
