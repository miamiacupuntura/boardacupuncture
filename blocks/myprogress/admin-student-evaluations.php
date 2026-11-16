<?php
// Administrator report for reviewing server-side student assessment results.

require_once(__DIR__ . '/../../config.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$subjectkey = optional_param('subject', 'all', PARAM_ALPHANUMEXT);
$program = optional_param('program', 'all', PARAM_ALPHA);
$search = trim(optional_param('q', '', PARAM_TEXT));
$detailuserid = optional_param('userid', 0, PARAM_INT);
$export = optional_param('export', '', PARAM_ALPHA);
$view = optional_param('view', 'students', PARAM_ALPHA);
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 50;

if (!in_array($view, ['students', 'courses'], true)) {
    $view = 'students';
}
if (!in_array($program, ['all', 'nccaom', 'ncbahm'], true)) {
    $program = 'all';
}

$subjects = [
    'all' => ['name' => 'Todas las materias', 'courseids' => [30, 34, 45, 44, 117, 36, 113]],
    'foundations' => ['name' => 'Foundations', 'courseids' => [30]],
    'acupuncture' => ['name' => 'Acupuncture & Point Location', 'courseids' => [34]],
    'single-herbs' => ['name' => 'Chinese Herbology · Single Herbs', 'courseids' => [45]],
    'formulas' => ['name' => 'Chinese Herbology · Formulas', 'courseids' => [44, 117]],
    'biomedicine' => ['name' => 'Biomedicine', 'courseids' => [36]],
    'cnt' => ['name' => 'Clean Needle Techniques', 'courseids' => [113]],
];
if (!isset($subjects[$subjectkey])) {
    $subjectkey = 'all';
}
$courseids = $subjects[$subjectkey]['courseids'];
$reporturl = new moodle_url('/blocks/myprogress/admin-student-evaluations.php');
$pageurl = new moodle_url($reporturl, array_filter([
    'subject' => $subjectkey !== 'all' ? $subjectkey : null,
    'program' => $program !== 'all' ? $program : null,
    'q' => $search !== '' ? $search : null,
    'userid' => $detailuserid ?: null,
    'view' => $view !== 'students' ? $view : null,
]));

$PAGE->set_url($pageurl);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('report');
$PAGE->set_title('Evaluación de estudiantes');
$PAGE->set_heading('Evaluación de estudiantes');
$PAGE->set_cacheable(false);
$PAGE->requires->js_init_code(<<<'JS'
(function() {
    var table = document.querySelector('[data-student-exam-table]');
    if (!table) return;
    var body = table.querySelector('tbody');
    var rows = Array.prototype.slice.call(body.querySelectorAll('[data-student-exam-row]'));
    var subjectFilter = document.querySelector('[data-exam-subject-filter]');
    var statusFilter = document.querySelector('[data-exam-status-filter]');
    var sortSelect = document.querySelector('[data-exam-sort]');
    var resetButton = document.querySelector('[data-exam-filters-reset]');
    var resultCount = document.querySelector('[data-exam-results-count]');
    var emptyRow = body.querySelector('[data-exam-filter-empty]');
    var collator = new Intl.Collator('es', {sensitivity: 'base', numeric: true});

    function numeric(row, attribute) {
        return Number(row.getAttribute(attribute) || 0);
    }

    function compareRows(left, right, mode) {
        if (mode === 'score-desc') return numeric(right, 'data-score') - numeric(left, 'data-score');
        if (mode === 'score-asc') return numeric(left, 'data-score') - numeric(right, 'data-score');
        if (mode === 'activity-desc') return numeric(right, 'data-activity') - numeric(left, 'data-activity');
        if (mode === 'attempts-desc') return numeric(right, 'data-attempts') - numeric(left, 'data-attempts');
        if (mode === 'evaluation') return collator.compare(left.getAttribute('data-name') || '', right.getAttribute('data-name') || '');
        var subjectComparison = collator.compare(left.getAttribute('data-subject-name') || '', right.getAttribute('data-subject-name') || '');
        return subjectComparison || collator.compare(left.getAttribute('data-name') || '', right.getAttribute('data-name') || '');
    }

    function applyExamControls() {
        var selectedSubject = subjectFilter ? subjectFilter.value : '';
        var selectedStatus = statusFilter ? statusFilter.value : '';
        var sortMode = sortSelect ? sortSelect.value : 'subject';
        var visible = rows.filter(function(row) {
            var subjectMatches = !selectedSubject || row.getAttribute('data-subject') === selectedSubject;
            var statusMatches = !selectedStatus || row.getAttribute('data-status') === selectedStatus;
            row.hidden = !(subjectMatches && statusMatches);
            return !row.hidden;
        });
        rows.slice().sort(function(left, right) {
            return compareRows(left, right, sortMode);
        }).forEach(function(row) {
            body.appendChild(row);
        });
        if (emptyRow) {
            emptyRow.hidden = visible.length !== 0;
            body.appendChild(emptyRow);
        }
        if (resultCount) {
            resultCount.textContent = visible.length + (visible.length === 1 ? ' evaluación' : ' evaluaciones');
        }
    }

    [subjectFilter, statusFilter, sortSelect].forEach(function(control) {
        if (control) control.addEventListener('change', applyExamControls);
    });
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            if (subjectFilter) subjectFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (sortSelect) sortSelect.value = 'subject';
            applyExamControls();
        });
    }
    applyExamControls();
})();
JS);

$coursesubject = [];
foreach ($subjects as $key => $subject) {
    if ($key === 'all') {
        continue;
    }
    foreach ($subject['courseids'] as $courseid) {
        $coursesubject[(int)$courseid] = $key;
    }
}

$boardstudentids = [];
$boardmembershipcontext = context_course::instance(116, IGNORE_MISSING);
if ($boardmembershipcontext) {
    $boardmembers = get_enrolled_users(
        $boardmembershipcontext,
        '',
        0,
        'u.id',
        '',
        0,
        0,
        false
    );
    $boardstudentids = array_fill_keys(array_map('intval', array_keys($boardmembers)), true);
}

$courserecords = $DB->get_records_list('course', 'id', $courseids, 'sortorder ASC, fullname ASC', 'id,fullname,shortname');
$students = [];
$coursestudents = [];
foreach ($courseids as $courseid) {
    $coursestudents[(int)$courseid] = [];
    $coursecontext = context_course::instance((int)$courseid, IGNORE_MISSING);
    if (!$coursecontext) {
        continue;
    }
    $enrolled = get_enrolled_users(
        $coursecontext,
        '',
        0,
        'u.id,u.firstname,u.lastname,u.email,u.lastaccess',
        'u.lastname ASC,u.firstname ASC',
        0,
        0,
        false
    );
    $attemptstudents = $DB->get_records_sql(
        "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.lastaccess
           FROM {user} u
           JOIN {scorm_attempt} a ON a.userid = u.id
           JOIN {scorm} s ON s.id = a.scormid
          WHERE s.course = :attemptcourseid
            AND u.deleted = 0
       ORDER BY u.lastname ASC, u.firstname ASC",
        ['attemptcourseid' => (int)$courseid]
    );
    $enrolled += $attemptstudents;
    $enrolled = array_filter($enrolled, static function($user): bool {
        return !is_siteadmin($user->id);
    });
    if ($program === 'ncbahm') {
        $enrolled = array_intersect_key($enrolled, $boardstudentids);
    } else if ($program === 'nccaom') {
        $enrolled = array_diff_key($enrolled, $boardstudentids);
    }
    $coursestudents[(int)$courseid] = $enrolled;
    foreach ($enrolled as $student) {
        $students[(int)$student->id] = $student;
    }
}

if ($search !== '') {
    $needle = core_text::strtolower($search);
    $students = array_filter($students, static function($student) use ($needle): bool {
        $haystack = core_text::strtolower(fullname($student) . ' ' . $student->email);
        return strpos($haystack, $needle) !== false;
    });
}
uasort($students, static function($left, $right): int {
    return strcasecmp($left->lastname . ' ' . $left->firstname, $right->lastname . ' ' . $right->firstname);
});
foreach ($coursestudents as $courseid => $enrolled) {
    $coursestudents[$courseid] = array_intersect_key($enrolled, $students);
}

$courselastaccess = [];
$lastaccessrecords = $DB->get_records_list(
    'user_lastaccess',
    'courseid',
    $courseids,
    '',
    'id,userid,courseid,timeaccess'
);
foreach ($lastaccessrecords as $lastaccessrecord) {
    $courseid = (int)$lastaccessrecord->courseid;
    $studentid = (int)$lastaccessrecord->userid;
    if (isset($coursestudents[$courseid][$studentid])) {
        $courselastaccess[$courseid][$studentid] = (int)$lastaccessrecord->timeaccess;
    }
}

$scorms = $DB->get_records_list('scorm', 'course', $courseids, 'course ASC, name ASC', 'id,course,name');
$subjecttotals = [];
foreach ($subjects as $key => $subject) {
    if ($key !== 'all') {
        $subjecttotals[$key] = 0;
    }
}
foreach ($scorms as $scorm) {
    $key = $coursesubject[(int)$scorm->course] ?? null;
    if ($key !== null) {
        $subjecttotals[$key]++;
    }
}

$studentstats = [];
$examdetails = [];
foreach ($students as $studentid => $student) {
    $studentstats[$studentid] = [
        'sum' => 0.0,
        'attempted' => 0,
        'passed' => 0,
        'total' => count($scorms),
        'attempts' => 0,
        'lastactivity' => 0,
        'subjects' => [],
    ];
    foreach ($subjecttotals as $key => $total) {
        $studentstats[$studentid]['subjects'][$key] = [
            'sum' => 0.0,
            'attempted' => 0,
            'passed' => 0,
            'total' => $total,
            'attempts' => 0,
            'lastactivity' => 0,
        ];
    }
}

if ($students !== [] && $scorms !== []) {
    [$userinsql, $userparams] = $DB->get_in_or_equal(array_keys($students), SQL_PARAMS_NAMED, 'student');
    [$courseinsql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'reportcourse');
    $params = $userparams + $courseparams + [
        'scoreelement12' => 'cmi.core.score.raw',
        'scoreelement2004' => 'cmi.score.raw',
    ];
    $sql = "SELECT v.id, a.id AS attemptid, a.userid, a.scormid, v.value, v.timemodified
              FROM {scorm_attempt} a
              JOIN {scorm} s ON s.id = a.scormid
              JOIN {scorm_scoes_value} v ON v.attemptid = a.id
              JOIN {scorm_element} e ON e.id = v.elementid
             WHERE a.userid $userinsql
               AND s.course $courseinsql
               AND e.element IN (:scoreelement12, :scoreelement2004)
          ORDER BY v.timemodified ASC";
    $recordset = $DB->get_recordset_sql($sql, $params);
    foreach ($recordset as $record) {
        $studentid = (int)$record->userid;
        $scormid = (int)$record->scormid;
        if (!isset($studentstats[$studentid], $scorms[$scormid]) || !is_numeric($record->value)) {
            continue;
        }
        $score = max(0.0, min(100.0, (float)$record->value));
        if (!isset($examdetails[$studentid][$scormid])) {
            $examdetails[$studentid][$scormid] = [
                'score' => $score,
                'attemptids' => [],
                'lastactivity' => 0,
            ];
        }
        $examdetails[$studentid][$scormid]['score'] = max($examdetails[$studentid][$scormid]['score'], $score);
        $examdetails[$studentid][$scormid]['attemptids'][(int)$record->attemptid] = true;
        $examdetails[$studentid][$scormid]['lastactivity'] = max(
            $examdetails[$studentid][$scormid]['lastactivity'],
            (int)$record->timemodified
        );
    }
    $recordset->close();
}

foreach ($examdetails as $studentid => $exams) {
    foreach ($exams as $scormid => $exam) {
        $scorm = $scorms[$scormid];
        $key = $coursesubject[(int)$scorm->course] ?? null;
        $studentstats[$studentid]['sum'] += $exam['score'];
        $studentstats[$studentid]['attempted']++;
        $attemptcount = count($exam['attemptids']);
        $studentstats[$studentid]['attempts'] += $attemptcount;
        $studentstats[$studentid]['lastactivity'] = max($studentstats[$studentid]['lastactivity'], $exam['lastactivity']);
        if ($exam['score'] >= 70) {
            $studentstats[$studentid]['passed']++;
        }
        if ($key !== null && isset($studentstats[$studentid]['subjects'][$key])) {
            $subjectstat =& $studentstats[$studentid]['subjects'][$key];
            $subjectstat['sum'] += $exam['score'];
            $subjectstat['attempted']++;
            $subjectstat['attempts'] += $attemptcount;
            $subjectstat['lastactivity'] = max($subjectstat['lastactivity'], $exam['lastactivity']);
            if ($exam['score'] >= 70) {
                $subjectstat['passed']++;
            }
            unset($subjectstat);
        }
    }
}

$average = static function(array $stat): ?float {
    return $stat['attempted'] > 0 ? round($stat['sum'] / $stat['attempted'], 1) : null;
};
$datevalue = static function(int $timestamp): string {
    return $timestamp > 0 ? userdate($timestamp, get_string('strftimedatetimeshort', 'langconfig')) : 'Sin actividad';
};

if ($export === 'csv') {
    $filename = ($view === 'courses' ? 'estudiantes-por-curso-' : 'evaluacion-estudiantes-')
        . userdate(time(), '%Y-%m-%d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    $output = fopen('php://output', 'wb');
    fwrite($output, "\xEF\xBB\xBF");
    if ($view === 'courses') {
        fputcsv($output, ['Curso', 'Estudiante', 'Correo', 'Último acceso al curso']);
        foreach ($courseids as $courseid) {
            if (!isset($courserecords[$courseid])) {
                continue;
            }
            foreach ($coursestudents[$courseid] ?? [] as $studentid => $student) {
                fputcsv($output, [
                    format_string($courserecords[$courseid]->fullname),
                    fullname($student),
                    $student->email,
                    $datevalue($courselastaccess[$courseid][$studentid] ?? 0),
                ]);
            }
        }
    } else {
        fputcsv($output, ['Estudiante', 'Correo', 'Promedio', 'Exámenes aprobados', 'Exámenes disponibles', 'Intentos', 'Última actividad']);
        foreach ($students as $studentid => $student) {
            $stat = $studentstats[$studentid];
            $studentaverage = $average($stat);
            fputcsv($output, [
                fullname($student),
                $student->email,
                $studentaverage === null ? '' : $studentaverage . '%',
                $stat['passed'],
                $stat['total'],
                $stat['attempts'],
                $datevalue($stat['lastactivity']),
            ]);
        }
    }
    fclose($output);
    exit;
}

$selectedstudent = $detailuserid > 0 && isset($students[$detailuserid]) ? $students[$detailuserid] : null;
$selectedexams = [];
if ($selectedstudent !== null) {
    foreach ($examdetails[$detailuserid] ?? [] as $scormid => $exam) {
        if (!isset($scorms[$scormid])) {
            continue;
        }
        $scorm = $scorms[$scormid];
        $key = $coursesubject[(int)$scorm->course] ?? null;
        $selectedexams[] = [
            'name' => $scorm->name,
            'subject' => $key !== null ? $subjects[$key]['name'] : 'Otra materia',
            'subjectkey' => $key ?? 'other',
            'score' => $exam['score'],
            'attempts' => count($exam['attemptids']),
            'lastactivity' => $exam['lastactivity'],
        ];
    }
    usort($selectedexams, static function(array $left, array $right): int {
        $subjectcomparison = strcasecmp($left['subject'], $right['subject']);
        return $subjectcomparison !== 0 ? $subjectcomparison : strcasecmp($left['name'], $right['name']);
    });
}
$selectedexamsubjects = [];
foreach ($selectedexams as $exam) {
    $selectedexamsubjects[$exam['subjectkey']] = $exam['subject'];
}
asort($selectedexamsubjects, SORT_NATURAL | SORT_FLAG_CASE);
$totalstudents = count($students);
$pagedstudents = array_slice($students, $page * $perpage, $perpage, true);
$exporturl = new moodle_url($reporturl, array_filter([
    'subject' => $subjectkey !== 'all' ? $subjectkey : null,
    'program' => $program !== 'all' ? $program : null,
    'q' => $search !== '' ? $search : null,
    'view' => $view !== 'students' ? $view : null,
    'export' => 'csv',
]));
$studentlisturl = new moodle_url($reporturl);
$courselisturl = new moodle_url($reporturl, ['view' => 'courses']);
$clearurl = new moodle_url($reporturl, $view === 'courses' ? ['view' => 'courses'] : []);

echo $OUTPUT->header();
?>
<style>
.student-evaluations{max-width:1280px;margin:0 auto 40px}.student-evaluations__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:23px 25px;border-left:5px solid #0b8f87;border-radius:12px;background:linear-gradient(135deg,#eef9f6,#fff8ef);box-shadow:0 3px 12px rgba(15,23,42,.06)}.student-evaluations__hero h2{margin:0 0 6px;color:#102a3b;font-size:30px}.student-evaluations__hero p{max-width:780px;margin:0;color:#52677a;line-height:1.5}.student-evaluations__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:9px 14px;border:1px solid #0b8f87;border-radius:8px;background:#0b8f87;color:#fff;font-weight:800;text-decoration:none;white-space:nowrap}.student-evaluations__button:hover{background:#076d68;color:#fff;text-decoration:none}.student-evaluations__button--secondary{border-color:#b8c8d2;background:#fff;color:#24465b}.student-evaluations__button--secondary:hover{background:#f1f6f8;color:#173448}.student-evaluations__notice{margin:0 0 18px;padding:13px 16px;border:1px solid #f0cf96;border-left:5px solid #d97706;border-radius:9px;background:#fff8ea;color:#6f4a15;line-height:1.5}.student-evaluations__filters{display:grid;grid-template-columns:minmax(220px,1fr) minmax(190px,.65fr) minmax(210px,.75fr) auto;gap:10px;margin-bottom:18px;padding:16px;border:1px solid #d7e2e7;border-radius:11px;background:#fff}.student-evaluations__filters label span{display:block;margin-bottom:5px;color:#365469;font-size:12px;font-weight:800}.student-evaluations__filters input,.student-evaluations__filters select{width:100%;min-height:42px;box-sizing:border-box;padding:8px 10px;border:1px solid #b9cbd4;border-radius:8px;background:#fff}.student-evaluations__filter-actions{display:flex;align-items:end;gap:8px}.student-evaluations__panel{margin-bottom:18px;border:1px solid #d7e2e7;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.05);overflow:hidden}.student-evaluations__panel-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:16px 18px;background:#f3f8f8}.student-evaluations__panel-head h3{margin:0;color:#173448;font-size:19px}.student-evaluations__panel-head span{color:#607487;font-size:13px;font-weight:700}.student-evaluations__table-wrap{overflow-x:auto}.student-evaluations__table{width:100%;border-collapse:collapse}.student-evaluations__table th,.student-evaluations__table td{padding:12px 14px;border-top:1px solid #e1e8ec;text-align:left;vertical-align:middle}.student-evaluations__table th{background:#fbfcfd;color:#365469;font-size:11px;letter-spacing:.035em;text-transform:uppercase}.student-evaluations__table td{color:#435d6f}.student-evaluations__student strong{display:block;color:#142f40}.student-evaluations__student small{display:block;margin-top:2px;color:#718394}.student-evaluations__score{color:#08746f;font-size:18px;font-weight:900}.student-evaluations__status{display:inline-block;padding:5px 9px;border-radius:999px;font-size:12px;font-weight:850}.student-evaluations__status--passed{background:#ddf6ec;color:#076c55}.student-evaluations__status--review{background:#fff0dd;color:#a34d00}.student-evaluations__empty{padding:35px!important;color:#64748b!important;text-align:center!important}.student-evaluations__cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:0 0 18px}.student-evaluations__card{padding:16px;border:1px solid #d7e2e7;border-radius:10px;background:#fff}.student-evaluations__card span{display:block;color:#64748b;font-size:12px;font-weight:750}.student-evaluations__card strong{display:block;margin-top:5px;color:#0b655f;font-size:25px}.student-evaluations__detail-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px}.student-evaluations__detail-head h3{margin:0 0 3px}.student-evaluations__detail-head p{margin:0;color:#64748b}.student-evaluations__pagination{padding:15px;text-align:center}.student-evaluations__pagination a{display:inline-block;margin:2px;padding:6px 10px;border:1px solid #cbd8de;border-radius:6px;color:#24516a;text-decoration:none}.student-evaluations__pagination a[aria-current="page"]{border-color:#0b8f87;background:#0b8f87;color:#fff}@media(max-width:980px){.student-evaluations__filters{grid-template-columns:1fr 1fr}}@media(max-width:780px){.student-evaluations__hero,.student-evaluations__detail-head{align-items:stretch;flex-direction:column}.student-evaluations__filters{grid-template-columns:1fr}.student-evaluations__filter-actions{align-items:stretch;flex-direction:column}.student-evaluations__filter-actions .student-evaluations__button{width:100%}.student-evaluations__cards{grid-template-columns:1fr 1fr}.student-evaluations__table th,.student-evaluations__table td{padding:10px 9px}}
.student-evaluations__hero-actions{display:flex;align-items:center;gap:8px}.student-evaluations__views{display:flex;gap:8px;margin:0 0 14px}.student-evaluations__view{display:inline-flex;min-height:40px;align-items:center;padding:8px 14px;border:1px solid #b8c8d2;border-radius:8px;background:#fff;color:#24465b;font-weight:800;text-decoration:none}.student-evaluations__view:hover{background:#f1f6f8;color:#173448;text-decoration:none}.student-evaluations__view[aria-current="page"]{border-color:#0b8f87;background:#e5f6f3;color:#076d68}.student-evaluations__course-name{display:block;color:#142f40}.student-evaluations__course-code{display:block;margin-top:2px;color:#718394;font-size:12px}@media(max-width:780px){.student-evaluations__hero-actions,.student-evaluations__views{align-items:stretch;flex-direction:column}.student-evaluations__hero-actions .student-evaluations__button,.student-evaluations__view{width:100%;box-sizing:border-box}}
.student-evaluations__exam-tools{display:grid;grid-template-columns:repeat(3,minmax(180px,1fr)) auto;gap:10px;padding:14px 18px;border-top:1px solid #e1e8ec;background:#fbfcfd}.student-evaluations__exam-tools label span{display:block;margin-bottom:5px;color:#365469;font-size:11px;font-weight:800;text-transform:uppercase}.student-evaluations__exam-tools select{width:100%;min-height:40px;padding:7px 9px;border:1px solid #b9cbd4;border-radius:7px;background:#fff}.student-evaluations__exam-tools .student-evaluations__button{align-self:end}@media(max-width:900px){.student-evaluations__exam-tools{grid-template-columns:1fr 1fr}}@media(max-width:600px){.student-evaluations__exam-tools{grid-template-columns:1fr}.student-evaluations__exam-tools .student-evaluations__button{width:100%}}
</style>
<main class="student-evaluations">
    <header class="student-evaluations__hero">
        <div>
            <h2>Evaluación de estudiantes</h2>
            <p>Consulta el rendimiento registrado en Moodle, compara materias y abre el detalle de cada estudiante sin entrar en su dashboard.</p>
        </div>
        <div class="student-evaluations__hero-actions"><a class="student-evaluations__button" href="<?php echo $exporturl->out(false); ?>">Exportar CSV</a></div>
    </header>
    <div class="student-evaluations__notice"><strong>Alcance de esta primera versión:</strong> incluye las evaluaciones SCORM guardadas en Moodle. El Formula Quiz y My Study Score todavía conservan su historial en el navegador de cada estudiante y se integrarán cuando ese historial se sincronice con el servidor.</div>

    <?php if ($selectedstudent === null): ?>
        <nav class="student-evaluations__views" aria-label="Vista del informe">
            <a class="student-evaluations__view" href="<?php echo $studentlisturl->out(false); ?>"<?php echo $view === 'students' ? ' aria-current="page"' : ''; ?>>Resultados de estudiantes</a>
            <a class="student-evaluations__view" href="<?php echo $courselisturl->out(false); ?>"<?php echo $view === 'courses' ? ' aria-current="page"' : ''; ?>>Lista completa por cursos</a>
        </nav>
        <form class="student-evaluations__filters" method="get" action="<?php echo $reporturl->out(false); ?>">
            <?php if ($view === 'courses'): ?><input type="hidden" name="view" value="courses"><?php endif; ?>
            <label><span>Buscar estudiante</span><input type="search" name="q" value="<?php echo s($search); ?>" placeholder="Nombre o correo electrónico"></label>
            <label><span>Materia</span><select name="subject">
                <?php foreach ($subjects as $key => $subject): ?>
                    <option value="<?php echo s($key); ?>"<?php echo $key === $subjectkey ? ' selected' : ''; ?>><?php echo s($subject['name']); ?></option>
                <?php endforeach; ?>
            </select></label>
            <label><span>Programa</span><select name="program">
                <option value="all"<?php echo $program === 'all' ? ' selected' : ''; ?>>Todos los programas</option>
                <option value="nccaom"<?php echo $program === 'nccaom' ? ' selected' : ''; ?>>NCCAOM Focused Practice</option>
                <option value="ncbahm"<?php echo $program === 'ncbahm' ? ' selected' : ''; ?>>NCBAHM Comprehensive Review</option>
            </select></label>
            <div class="student-evaluations__filter-actions">
                <button class="student-evaluations__button" type="submit">Aplicar</button>
                <a class="student-evaluations__button student-evaluations__button--secondary" href="<?php echo $clearurl->out(false); ?>"><?php echo $view === 'courses' ? 'Ver todos los estudiantes' : 'Limpiar'; ?></a>
            </div>
        </form>
        <?php if ($view === 'courses'): ?>
            <?php foreach ($courseids as $courseid): if (!isset($courserecords[$courseid])) continue; $course = $courserecords[$courseid]; $courseenrolled = $coursestudents[$courseid] ?? []; ?>
            <section class="student-evaluations__panel" aria-labelledby="course-<?php echo (int)$courseid; ?>-title">
                <header class="student-evaluations__panel-head">
                    <h3 id="course-<?php echo (int)$courseid; ?>-title"><span class="student-evaluations__course-name"><?php echo s(format_string($course->fullname)); ?></span><small class="student-evaluations__course-code"><?php echo s($course->shortname); ?></small></h3>
                    <span><?php echo count($courseenrolled); ?> estudiante<?php echo count($courseenrolled) === 1 ? '' : 's'; ?></span>
                </header>
                <div class="student-evaluations__table-wrap"><table class="student-evaluations__table">
                    <thead><tr><th>Estudiante</th><th>Correo electrónico</th><th>Último acceso al curso</th><th></th></tr></thead>
                    <tbody>
                    <?php if ($courseenrolled === []): ?>
                        <tr><td class="student-evaluations__empty" colspan="4">No se encontraron estudiantes en este curso con los filtros seleccionados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($courseenrolled as $studentid => $student): ?>
                        <tr>
                            <td class="student-evaluations__student"><strong><?php echo s(fullname($student)); ?></strong></td>
                            <td><?php echo s($student->email); ?></td>
                            <td><?php echo s($datevalue($courselastaccess[$courseid][$studentid] ?? 0)); ?></td>
                            <td><a class="student-evaluations__button student-evaluations__button--secondary" href="<?php echo (new moodle_url($reporturl, ['userid' => $studentid, 'subject' => $coursesubject[$courseid] ?? 'all', 'program' => $program]))->out(false); ?>">Ver evaluación</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table></div>
            </section>
            <?php endforeach; ?>
        <?php else: ?>
        <section class="student-evaluations__panel" aria-labelledby="student-results-title">
            <header class="student-evaluations__panel-head"><h3 id="student-results-title">Estudiantes</h3><span><?php echo $totalstudents; ?> resultado<?php echo $totalstudents === 1 ? '' : 's'; ?></span></header>
            <div class="student-evaluations__table-wrap"><table class="student-evaluations__table">
                <thead><tr><th>Estudiante</th><th>Promedio</th><th>Aprobados</th><th>Intentos</th><th>Última actividad</th><th></th></tr></thead>
                <tbody>
                <?php if ($pagedstudents === []): ?>
                    <tr><td class="student-evaluations__empty" colspan="6">No se encontraron estudiantes con estos filtros.</td></tr>
                <?php else: ?>
                    <?php foreach ($pagedstudents as $studentid => $student): $stat = $studentstats[$studentid]; $studentaverage = $average($stat); ?>
                    <tr>
                        <td class="student-evaluations__student"><strong><?php echo s(fullname($student)); ?></strong><small><?php echo s($student->email); ?></small></td>
                        <td><span class="student-evaluations__score"><?php echo $studentaverage === null ? '—' : s($studentaverage . '%'); ?></span></td>
                        <td><?php echo (int)$stat['passed']; ?> de <?php echo (int)$stat['total']; ?></td>
                        <td><?php echo (int)$stat['attempts']; ?></td>
                        <td><?php echo s($datevalue($stat['lastactivity'])); ?></td>
                        <td><a class="student-evaluations__button student-evaluations__button--secondary" href="<?php echo (new moodle_url($reporturl, ['userid' => $studentid, 'subject' => $subjectkey, 'program' => $program, 'q' => $search]))->out(false); ?>">Ver evaluación</a></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table></div>
            <?php if ($totalstudents > $perpage): ?>
                <nav class="student-evaluations__pagination" aria-label="Páginas del informe">
                    <?php for ($index = 0; $index < (int)ceil($totalstudents / $perpage); $index++): ?>
                        <a href="<?php echo (new moodle_url($reporturl, ['subject' => $subjectkey, 'program' => $program, 'q' => $search, 'page' => $index]))->out(false); ?>"<?php echo $index === $page ? ' aria-current="page"' : ''; ?>><?php echo $index + 1; ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    <?php else: ?>
        <?php $detailstat = $studentstats[$detailuserid]; $detailaverage = $average($detailstat); ?>
        <div class="student-evaluations__detail-head"><div><h3><?php echo s(fullname($selectedstudent)); ?></h3><p><?php echo s($selectedstudent->email); ?></p></div><a class="student-evaluations__button student-evaluations__button--secondary" href="<?php echo (new moodle_url($reporturl, ['subject' => $subjectkey, 'program' => $program, 'q' => $search]))->out(false); ?>">← Todos los estudiantes</a></div>
        <div class="student-evaluations__cards">
            <article class="student-evaluations__card"><span>Promedio</span><strong><?php echo $detailaverage === null ? '—' : s($detailaverage . '%'); ?></strong></article>
            <article class="student-evaluations__card"><span>Exámenes aprobados</span><strong><?php echo (int)$detailstat['passed']; ?> / <?php echo (int)$detailstat['total']; ?></strong></article>
            <article class="student-evaluations__card"><span>Intentos registrados</span><strong><?php echo (int)$detailstat['attempts']; ?></strong></article>
            <article class="student-evaluations__card"><span>Última actividad</span><strong style="font-size:16px"><?php echo s($datevalue($detailstat['lastactivity'])); ?></strong></article>
        </div>
        <section class="student-evaluations__panel" aria-labelledby="subject-detail-title">
            <header class="student-evaluations__panel-head"><h3 id="subject-detail-title">Resultados por materia</h3></header>
            <div class="student-evaluations__table-wrap"><table class="student-evaluations__table"><thead><tr><th>Materia</th><th>Promedio</th><th>Aprobados</th><th>Intentos</th><th>Última actividad</th></tr></thead><tbody>
            <?php foreach ($detailstat['subjects'] as $key => $subjectstat): if ($subjectstat['total'] === 0) continue; $subjectaverage = $average($subjectstat); ?>
                <tr><td><strong><?php echo s($subjects[$key]['name']); ?></strong></td><td><span class="student-evaluations__score"><?php echo $subjectaverage === null ? '—' : s($subjectaverage . '%'); ?></span></td><td><?php echo (int)$subjectstat['passed']; ?> de <?php echo (int)$subjectstat['total']; ?></td><td><?php echo (int)$subjectstat['attempts']; ?></td><td><?php echo s($datevalue($subjectstat['lastactivity'])); ?></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </section>
        <section class="student-evaluations__panel" aria-labelledby="exam-detail-title">
            <header class="student-evaluations__panel-head"><h3 id="exam-detail-title">Evaluaciones individuales</h3><span data-exam-results-count><?php echo count($selectedexams); ?> evaluación<?php echo count($selectedexams) === 1 ? '' : 'es'; ?></span></header>
            <?php if ($selectedexams !== []): ?>
            <div class="student-evaluations__exam-tools" aria-label="Filtros y orden de evaluaciones">
                <label><span>Filtrar por materia</span><select data-exam-subject-filter><option value="">Todas las materias</option><?php foreach ($selectedexamsubjects as $key => $name): ?><option value="<?php echo s($key); ?>"><?php echo s($name); ?></option><?php endforeach; ?></select></label>
                <label><span>Filtrar por estado</span><select data-exam-status-filter><option value="">Todos los estados</option><option value="passed">Aprobadas</option><option value="review">Requieren repaso</option></select></label>
                <label><span>Ordenar por</span><select data-exam-sort><option value="subject">Materia y evaluación</option><option value="evaluation">Nombre de evaluación</option><option value="score-desc">Mejor resultado: mayor a menor</option><option value="score-asc">Mejor resultado: menor a mayor</option><option value="attempts-desc">Más intentos</option><option value="activity-desc">Actividad más reciente</option></select></label>
                <button class="student-evaluations__button student-evaluations__button--secondary" type="button" data-exam-filters-reset>Restablecer</button>
            </div>
            <?php endif; ?>
            <div class="student-evaluations__table-wrap"><table class="student-evaluations__table" data-student-exam-table>
                <thead><tr><th>Materia</th><th>Evaluación</th><th>Mejor resultado</th><th>Estado</th><th>Intentos</th><th>Última actividad</th></tr></thead>
                <tbody>
                <?php if ($selectedexams === []): ?>
                    <tr><td class="student-evaluations__empty" colspan="6">Este estudiante todavía no tiene evaluaciones SCORM registradas con el filtro seleccionado.</td></tr>
                <?php else: ?>
                    <?php foreach ($selectedexams as $exam): $passed = $exam['score'] >= 70; ?>
                    <tr data-student-exam-row data-subject="<?php echo s($exam['subjectkey']); ?>" data-subject-name="<?php echo s($exam['subject']); ?>" data-name="<?php echo s($exam['name']); ?>" data-status="<?php echo $passed ? 'passed' : 'review'; ?>" data-score="<?php echo s((string)$exam['score']); ?>" data-attempts="<?php echo (int)$exam['attempts']; ?>" data-activity="<?php echo (int)$exam['lastactivity']; ?>">
                        <td><?php echo s($exam['subject']); ?></td>
                        <td><strong><?php echo s($exam['name']); ?></strong></td>
                        <td><span class="student-evaluations__score"><?php echo s(round($exam['score'], 1) . '%'); ?></span></td>
                        <td><span class="student-evaluations__status student-evaluations__status--<?php echo $passed ? 'passed' : 'review'; ?>"><?php echo $passed ? 'Aprobada' : 'Requiere repaso'; ?></span></td>
                        <td><?php echo (int)$exam['attempts']; ?></td>
                        <td><?php echo s($datevalue($exam['lastactivity'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr data-exam-filter-empty hidden><td class="student-evaluations__empty" colspan="6">No hay evaluaciones que coincidan con los filtros seleccionados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table></div>
        </section>
    <?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();

