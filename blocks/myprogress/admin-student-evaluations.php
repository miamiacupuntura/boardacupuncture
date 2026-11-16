<?php
// Administrator report for reviewing server-side student assessment results.

require_once(__DIR__ . '/../../config.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$subjectkey = optional_param('subject', 'all', PARAM_ALPHANUMEXT);
$search = trim(optional_param('q', '', PARAM_TEXT));
$detailuserid = optional_param('userid', 0, PARAM_INT);
$export = optional_param('export', '', PARAM_ALPHA);
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 50;

$subjects = [
    'all' => ['name' => 'Todas las materias', 'courseids' => [30, 34, 45, 117, 36, 113]],
    'foundations' => ['name' => 'Foundations', 'courseids' => [30]],
    'acupuncture' => ['name' => 'Acupuncture & Point Location', 'courseids' => [34]],
    'single-herbs' => ['name' => 'Chinese Herbology · Single Herbs', 'courseids' => [45]],
    'formulas' => ['name' => 'Chinese Herbology · Formulas', 'courseids' => [117]],
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
    'q' => $search !== '' ? $search : null,
    'userid' => $detailuserid ?: null,
]));

$PAGE->set_url($pageurl);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('report');
$PAGE->set_title('Evaluación de estudiantes');
$PAGE->set_heading('Evaluación de estudiantes');
$PAGE->set_cacheable(false);

$coursesubject = [];
foreach ($subjects as $key => $subject) {
    if ($key === 'all') {
        continue;
    }
    foreach ($subject['courseids'] as $courseid) {
        $coursesubject[(int)$courseid] = $key;
    }
}

$students = [];
foreach ($courseids as $courseid) {
    $coursecontext = context_course::instance((int)$courseid, IGNORE_MISSING);
    if (!$coursecontext) {
        continue;
    }
    $enrolled = get_enrolled_users(
        $coursecontext,
        'mod/scorm:submit',
        0,
        'u.id,u.firstname,u.lastname,u.email,u.lastaccess',
        'u.lastname ASC,u.firstname ASC',
        0,
        0,
        true
    );
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
    $filename = 'evaluacion-estudiantes-' . userdate(time(), '%Y-%m-%d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    $output = fopen('php://output', 'wb');
    fwrite($output, "\xEF\xBB\xBF");
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
    fclose($output);
    exit;
}

$selectedstudent = $detailuserid > 0 && isset($students[$detailuserid]) ? $students[$detailuserid] : null;
$totalstudents = count($students);
$pagedstudents = array_slice($students, $page * $perpage, $perpage, true);
$exporturl = new moodle_url($reporturl, array_filter([
    'subject' => $subjectkey !== 'all' ? $subjectkey : null,
    'q' => $search !== '' ? $search : null,
    'export' => 'csv',
]));

echo $OUTPUT->header();
?>
<style>
.student-evaluations{max-width:1280px;margin:0 auto 40px}.student-evaluations__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:23px 25px;border-left:5px solid #0b8f87;border-radius:12px;background:linear-gradient(135deg,#eef9f6,#fff8ef);box-shadow:0 3px 12px rgba(15,23,42,.06)}.student-evaluations__hero h2{margin:0 0 6px;color:#102a3b;font-size:30px}.student-evaluations__hero p{max-width:780px;margin:0;color:#52677a;line-height:1.5}.student-evaluations__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:9px 14px;border:1px solid #0b8f87;border-radius:8px;background:#0b8f87;color:#fff;font-weight:800;text-decoration:none;white-space:nowrap}.student-evaluations__button:hover{background:#076d68;color:#fff;text-decoration:none}.student-evaluations__button--secondary{border-color:#b8c8d2;background:#fff;color:#24465b}.student-evaluations__button--secondary:hover{background:#f1f6f8;color:#173448}.student-evaluations__notice{margin:0 0 18px;padding:13px 16px;border:1px solid #f0cf96;border-left:5px solid #d97706;border-radius:9px;background:#fff8ea;color:#6f4a15;line-height:1.5}.student-evaluations__filters{display:grid;grid-template-columns:minmax(240px,1fr) minmax(230px,.7fr) auto;gap:10px;margin-bottom:18px;padding:16px;border:1px solid #d7e2e7;border-radius:11px;background:#fff}.student-evaluations__filters label span{display:block;margin-bottom:5px;color:#365469;font-size:12px;font-weight:800}.student-evaluations__filters input,.student-evaluations__filters select{width:100%;min-height:42px;box-sizing:border-box;padding:8px 10px;border:1px solid #b9cbd4;border-radius:8px;background:#fff}.student-evaluations__filter-actions{display:flex;align-items:end;gap:8px}.student-evaluations__panel{border:1px solid #d7e2e7;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.05);overflow:hidden}.student-evaluations__panel-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:16px 18px;background:#f3f8f8}.student-evaluations__panel-head h3{margin:0;color:#173448;font-size:19px}.student-evaluations__panel-head span{color:#607487;font-size:13px;font-weight:700}.student-evaluations__table-wrap{overflow-x:auto}.student-evaluations__table{width:100%;border-collapse:collapse}.student-evaluations__table th,.student-evaluations__table td{padding:12px 14px;border-top:1px solid #e1e8ec;text-align:left;vertical-align:middle}.student-evaluations__table th{background:#fbfcfd;color:#365469;font-size:11px;letter-spacing:.035em;text-transform:uppercase}.student-evaluations__table td{color:#435d6f}.student-evaluations__student strong{display:block;color:#142f40}.student-evaluations__student small{display:block;margin-top:2px;color:#718394}.student-evaluations__score{color:#08746f;font-size:18px;font-weight:900}.student-evaluations__empty{padding:35px!important;color:#64748b!important;text-align:center!important}.student-evaluations__cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:0 0 18px}.student-evaluations__card{padding:16px;border:1px solid #d7e2e7;border-radius:10px;background:#fff}.student-evaluations__card span{display:block;color:#64748b;font-size:12px;font-weight:750}.student-evaluations__card strong{display:block;margin-top:5px;color:#0b655f;font-size:25px}.student-evaluations__detail-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px}.student-evaluations__detail-head h3{margin:0 0 3px}.student-evaluations__detail-head p{margin:0;color:#64748b}.student-evaluations__pagination{padding:15px;text-align:center}.student-evaluations__pagination a{display:inline-block;margin:2px;padding:6px 10px;border:1px solid #cbd8de;border-radius:6px;color:#24516a;text-decoration:none}.student-evaluations__pagination a[aria-current="page"]{border-color:#0b8f87;background:#0b8f87;color:#fff}@media(max-width:780px){.student-evaluations__hero,.student-evaluations__detail-head{align-items:stretch;flex-direction:column}.student-evaluations__filters{grid-template-columns:1fr}.student-evaluations__filter-actions{align-items:stretch;flex-direction:column}.student-evaluations__filter-actions .student-evaluations__button{width:100%}.student-evaluations__cards{grid-template-columns:1fr 1fr}.student-evaluations__table th,.student-evaluations__table td{padding:10px 9px}}
</style>
<main class="student-evaluations">
    <header class="student-evaluations__hero">
        <div>
            <h2>Evaluación de estudiantes</h2>
            <p>Consulta el rendimiento registrado en Moodle, compara materias y abre el detalle de cada estudiante sin entrar en su dashboard.</p>
        </div>
        <a class="student-evaluations__button" href="<?php echo $exporturl->out(false); ?>">Exportar CSV</a>
    </header>
    <div class="student-evaluations__notice"><strong>Alcance de esta primera versión:</strong> incluye las evaluaciones SCORM guardadas en Moodle. El Formula Quiz y My Study Score todavía conservan su historial en el navegador de cada estudiante y se integrarán cuando ese historial se sincronice con el servidor.</div>

    <?php if ($selectedstudent === null): ?>
        <form class="student-evaluations__filters" method="get" action="<?php echo $reporturl->out(false); ?>">
            <label><span>Buscar estudiante</span><input type="search" name="q" value="<?php echo s($search); ?>" placeholder="Nombre o correo electrónico"></label>
            <label><span>Materia</span><select name="subject">
                <?php foreach ($subjects as $key => $subject): ?>
                    <option value="<?php echo s($key); ?>"<?php echo $key === $subjectkey ? ' selected' : ''; ?>><?php echo s($subject['name']); ?></option>
                <?php endforeach; ?>
            </select></label>
            <div class="student-evaluations__filter-actions">
                <button class="student-evaluations__button" type="submit">Aplicar</button>
                <a class="student-evaluations__button student-evaluations__button--secondary" href="<?php echo $reporturl->out(false); ?>">Limpiar</a>
            </div>
        </form>
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
                        <td><a class="student-evaluations__button student-evaluations__button--secondary" href="<?php echo (new moodle_url($reporturl, ['userid' => $studentid, 'subject' => $subjectkey, 'q' => $search]))->out(false); ?>">Ver evaluación</a></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table></div>
            <?php if ($totalstudents > $perpage): ?>
                <nav class="student-evaluations__pagination" aria-label="Páginas del informe">
                    <?php for ($index = 0; $index < (int)ceil($totalstudents / $perpage); $index++): ?>
                        <a href="<?php echo (new moodle_url($reporturl, ['subject' => $subjectkey, 'q' => $search, 'page' => $index]))->out(false); ?>"<?php echo $index === $page ? ' aria-current="page"' : ''; ?>><?php echo $index + 1; ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <?php $detailstat = $studentstats[$detailuserid]; $detailaverage = $average($detailstat); ?>
        <div class="student-evaluations__detail-head"><div><h3><?php echo s(fullname($selectedstudent)); ?></h3><p><?php echo s($selectedstudent->email); ?></p></div><a class="student-evaluations__button student-evaluations__button--secondary" href="<?php echo (new moodle_url($reporturl, ['subject' => $subjectkey, 'q' => $search]))->out(false); ?>">← Todos los estudiantes</a></div>
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
    <?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();

