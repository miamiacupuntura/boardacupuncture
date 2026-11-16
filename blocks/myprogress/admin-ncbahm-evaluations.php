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
if (!in_array($statusfilter, ['all', 'passed', 'failed', 'incomplete'], true)) {
    $statusfilter = 'all';
}
if (!in_array($sort, ['recent', 'name', 'result'], true)) {
    $sort = 'recent';
}

$reporturl = new moodle_url('/blocks/myprogress/admin-ncbahm-evaluations.php');
$scormreporturl = new moodle_url('/blocks/myprogress/admin-student-evaluations.php');
$allprogramsurl = new moodle_url('/blocks/myprogress/admin-student-evaluations.php', ['view' => 'courses']);
$pageparams = array_filter([
    'q' => $search !== '' ? $search : null,
    'assessment' => $assessmenttype !== 'all' ? $assessmenttype : null,
    'status' => $statusfilter !== 'all' ? $statusfilter : null,
    'sort' => $sort !== 'recent' ? $sort : null,
    'userid' => $detailuserid ?: null,
]);

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
    if (str_starts_with($base, 'bqf_')) return 'Formula Quiz · supplemental category';
    if (str_starts_with($base, 'bqd_')) return 'Dui Yao Quiz';
    if (str_starts_with($base, 'bqm_')) return 'Supplemental Quiz · ' . substr($base, 4);
    $names = [
        'simulationlinear' => 'Linear Simulation',
        'simulationadaptive' => 'Adaptive Simulation',
        'linear' => 'Linear Board Exam',
        'adaptive' => 'Adaptive Board Exam',
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
        $kind = $attemptkind((string)$record->examtype);
        $incomplete = str_ends_with((string)$record->examtype, 'incomplete');
        $percentage = (int)$record->total > 0
            ? round(((float)$record->score / (float)$record->total) * 100, 1)
            : 0.0;
        $status = $incomplete ? 'incomplete' : ($percentage >= 70 ? 'passed' : 'failed');
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
</style>
<main class="ncbahm-report">
    <header class="ncbahm-report__hero"><div><h2>Student Evaluations</h2><p>Review NCBAHM Quiz, Simulation, and Exam results stored on the server.</p></div><div class="ncbahm-report__actions"><a class="ncbahm-report__button" href="<?php echo $exporturl->out(false); ?>">Export CSV</a></div></header>
    <nav class="ncbahm-report__programs" aria-label="Evaluation program"><a class="ncbahm-report__program" href="<?php echo $scormreporturl->out(false); ?>">NCCAOM Focused Practice</a><a class="ncbahm-report__program" href="<?php echo $reporturl->out(false); ?>" aria-current="page">NCBAHM Comprehensive Review</a><a class="ncbahm-report__program" href="<?php echo $allprogramsurl->out(false); ?>">All Programs</a></nav>
    <div class="ncbahm-report__section-title">Student results</div>
    <?php if (!$tableavailable): ?><div class="alert alert-warning">NCBAHM history is not yet installed in the database.</div><?php endif; ?>
    <form class="ncbahm-report__filters" method="get" action="<?php echo $reporturl->out(false); ?>">
        <label><span>Search student</span><input type="search" name="q" value="<?php echo s($search); ?>" placeholder="Name or email address"></label>
        <label><span>Evaluation type</span><select name="assessment"><option value="all">All</option><option value="quiz"<?php echo $assessmenttype === 'quiz' ? ' selected' : ''; ?>>Quiz</option><option value="simulation"<?php echo $assessmenttype === 'simulation' ? ' selected' : ''; ?>>Simulation</option><option value="exam"<?php echo $assessmenttype === 'exam' ? ' selected' : ''; ?>>Exams</option></select></label>
        <label><span>Status</span><select name="status"><option value="all">All</option><option value="passed"<?php echo $statusfilter === 'passed' ? ' selected' : ''; ?>>Passed</option><option value="failed"<?php echo $statusfilter === 'failed' ? ' selected' : ''; ?>>Not passed</option><option value="incomplete"<?php echo $statusfilter === 'incomplete' ? ' selected' : ''; ?>>Incomplete</option></select></label>
        <label><span>Sort by</span><select name="sort"><option value="recent">Recent activity</option><option value="name"<?php echo $sort === 'name' ? ' selected' : ''; ?>>Name</option><option value="result"<?php echo $sort === 'result' ? ' selected' : ''; ?>>Best average</option></select></label>
        <div class="ncbahm-report__filter-actions"><button class="ncbahm-report__button" type="submit">Apply</button><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo $reporturl->out(false); ?>">Clear</a></div>
    </form>
    <?php if ($selectedstudent === null): ?>
    <section class="ncbahm-report__panel"><header class="ncbahm-report__panel-head"><h3>Students with NCBAHM evaluations</h3><span><?php echo $totalstudents; ?> result<?php echo $totalstudents === 1 ? '' : 's'; ?></span></header><div class="ncbahm-report__table-wrap"><table><thead><tr><th>Student</th><th>Average</th><th>Quiz</th><th>Simulations</th><th>Exams</th><th>Last activity</th><th>Actions</th></tr></thead><tbody>
    <?php if ($pagedstudents === []): ?><tr><td class="ncbahm-report__empty" colspan="7">No NCBAHM evaluations match these filters.</td></tr><?php else: foreach ($pagedstudents as $userid => $stat): ?>
        <tr><td><strong><?php echo s($stat['name']); ?></strong><br><small><?php echo s($stat['email']); ?></small></td><td><span class="ncbahm-report__score"><?php echo $stat['average'] === null ? '—' : s($stat['average'] . '%'); ?></span></td><td><?php echo (int)$stat['quiz']; ?></td><td><?php echo (int)$stat['simulation']; ?></td><td><?php echo (int)$stat['exam']; ?></td><td><?php echo s($datevalue($stat['lastactivity'])); ?></td><td><div class="ncbahm-report__row-actions"><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo (new moodle_url($reporturl, $pageparams + ['userid' => $userid]))->out(false); ?>">View evaluations</a><a class="ncbahm-report__button ncbahm-report__button--student" href="<?php echo (new moodle_url('/blocks/myprogress/admin-open-student-dashboard.php', ['userid' => $userid, 'sesskey' => sesskey()]))->out(false); ?>">Open as student</a></div></td></tr>
    <?php endforeach; endif; ?></tbody></table></div>
    <?php if ($totalstudents > $perpage): ?><nav class="ncbahm-report__pagination"><?php for ($index = 0; $index < (int)ceil($totalstudents / $perpage); $index++): ?><a href="<?php echo (new moodle_url($reporturl, $pageparams + ['page' => $index]))->out(false); ?>"><?php echo $index + 1; ?></a><?php endfor; ?></nav><?php endif; ?>
    </section>
    <?php else: ?>
    <div class="ncbahm-report__actions" style="margin-bottom:14px"><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo (new moodle_url($reporturl, array_diff_key($pageparams, ['userid' => true])))->out(false); ?>">← All students</a></div>
    <div class="ncbahm-report__cards"><article class="ncbahm-report__card"><span>Average</span><strong><?php echo $selectedstudent['average'] === null ? '—' : s($selectedstudent['average'] . '%'); ?></strong></article><article class="ncbahm-report__card"><span>Quiz</span><strong><?php echo (int)$selectedstudent['quiz']; ?></strong></article><article class="ncbahm-report__card"><span>Simulations</span><strong><?php echo (int)$selectedstudent['simulation']; ?></strong></article><article class="ncbahm-report__card"><span>Exams</span><strong><?php echo (int)$selectedstudent['exam']; ?></strong></article></div>
    <div class="alert alert-info">Supervised mode: the administrator temporarily switches to the selected student’s profile. The evaluation will display the student’s name, and the result will be saved exclusively to that profile.</div>
    <section class="ncbahm-report__panel"><header class="ncbahm-report__panel-head"><h3><?php echo s($selectedstudent['name']); ?></h3><span><?php echo count($selectedattempts); ?> attempt<?php echo count($selectedattempts) === 1 ? '' : 's'; ?></span></header><div class="ncbahm-report__table-wrap"><table><thead><tr><th>Type</th><th>Evaluation</th><th>Result</th><th>Status</th><th>Duration</th><th>Date</th><th>Action</th></tr></thead><tbody>
    <?php foreach ($selectedattempts as $attempt): $statuslabels = ['passed' => 'Passed', 'failed' => 'Not passed', 'incomplete' => 'Incomplete']; ?>
        <tr><td><?php echo s($attempt['kindname']); ?></td><td><strong><?php echo s($attempt['name']); ?></strong></td><td><span class="ncbahm-report__score"><?php echo s($attempt['percentage'] . '%'); ?></span></td><td><span class="ncbahm-report__status ncbahm-report__status--<?php echo s($attempt['status']); ?>"><?php echo s($statuslabels[$attempt['status']]); ?></span></td><td><?php echo s($durationvalue($attempt['duration'])); ?></td><td><?php echo s($datevalue($attempt['timecompleted'])); ?></td><td><a class="ncbahm-report__button ncbahm-report__button--secondary ncbahm-report__button--compact" href="<?php echo s($attempt['openasurl']); ?>">Open as <?php echo s($attempt['student']); ?></a></td></tr>
    <?php endforeach; ?></tbody></table></div></section>
    <?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
