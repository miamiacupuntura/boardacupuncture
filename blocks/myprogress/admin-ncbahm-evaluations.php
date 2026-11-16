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
$PAGE->set_title('Evaluaciones NCBAHM');
$PAGE->set_heading('Evaluaciones NCBAHM');
$PAGE->set_cacheable(false);

$quiznames = [
    'bqd_duiyao' => 'Quiz de Dui Yao',
    'bqm_families' => 'Familias y variaciones de fórmulas',
    'bqm_nametraps' => 'Trampas de nombres',
    'bqm_herbparts' => 'Familias y partes de hierbas',
    'bqm_formulanames' => 'Trampas de nombres de fórmulas',
    'bqm_misleading' => 'Nombres engañosos de fórmulas',
    'bqm_advanced' => 'Trampas avanzadas',
    'bqm_supplements' => 'Familias suplementarias de hierbas',
    'bqm_secondary' => 'Acciones secundarias de hierbas',
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
            $spanishname = trim((string)($category['es'] ?? $category['en'] ?? ''));
            $englishname = trim((string)($category['en'] ?? ''));
            if ($key !== '' && $spanishname !== '') {
                $displayname = $spanishname;
                if ($englishname !== '' && strcasecmp($englishname, $spanishname) !== 0) {
                    $displayname .= ' (' . $englishname . ')';
                }
                $quiznames['bqf_' . substr(sha1($key), 0, 12)] = 'Quiz de fórmulas · ' . $displayname;
            }
        }
    }
    $quiznames['bqf_modifications'] = 'Quiz de modificaciones de fórmulas';
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
    if (preg_match('/^bqh_(\d+)$/', $base, $matches)) return 'Quiz de hierbas individuales · Capítulo ' . $matches[1];
    if (str_starts_with($base, 'bqf_')) return 'Quiz de fórmulas · categoría complementaria';
    if (str_starts_with($base, 'bqd_')) return 'Quiz de Dui Yao';
    if (str_starts_with($base, 'bqm_')) return 'Quiz complementario · ' . substr($base, 4);
    $names = [
        'simulationlinear' => 'Simulación lineal',
        'simulationadaptive' => 'Simulación adaptativa',
        'linear' => 'Examen Board lineal',
        'adaptive' => 'Examen Board adaptativo',
        'clinicalcases' => 'Examen de casos clínicos',
    ];
    return $names[$base] ?? ucwords(str_replace(['_', '-'], ' ', $base));
};
$kindname = static function(string $kind): string {
    return ['quiz' => 'Quiz', 'simulation' => 'Simulación', 'exam' => 'Examen'][$kind] ?? 'Evaluación';
};
$durationvalue = static function(int $seconds): string {
    $seconds = max(0, $seconds);
    $minutes = intdiv($seconds, 60);
    return $minutes > 0 ? $minutes . ' min ' . ($seconds % 60) . ' s' : $seconds . ' s';
};
$datevalue = static function(int $timestamp): string {
    return $timestamp > 0 ? userdate($timestamp, get_string('strftimedatetimeshort', 'langconfig')) : 'Sin actividad';
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
            'restarturl' => (new moodle_url('/blocks/menu/exam-history.php', [
                'start' => (string)$record->examtype,
                'lang' => 'es',
                'ui' => 'es',
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
    $filename = 'evaluaciones-ncbahm-' . userdate(time(), '%Y-%m-%d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    $output = fopen('php://output', 'wb');
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, ['Estudiante', 'Correo', 'Tipo', 'Evaluación', 'Resultado', 'Estado', 'Duración', 'Fecha']);
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
</style>
<main class="ncbahm-report">
    <header class="ncbahm-report__hero"><div><h2>NCBAHM Comprehensive Review</h2><p>Resultados reales guardados en el servidor para Quiz, Simulación y Exámenes.</p></div><div class="ncbahm-report__actions"><a class="ncbahm-report__button" href="<?php echo $exporturl->out(false); ?>">Exportar CSV</a></div></header>
    <nav class="ncbahm-report__tabs" aria-label="Fuente de evaluaciones"><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo $scormreporturl->out(false); ?>">NCCAOM · SCORM</a><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo $reporturl->out(false); ?>" aria-current="page">NCBAHM Comprehensive Review</a></nav>
    <?php if (!$tableavailable): ?><div class="alert alert-warning">El historial NCBAHM todavía no está instalado en la base de datos.</div><?php endif; ?>
    <form class="ncbahm-report__filters" method="get" action="<?php echo $reporturl->out(false); ?>">
        <label><span>Buscar estudiante</span><input type="search" name="q" value="<?php echo s($search); ?>" placeholder="Nombre o correo"></label>
        <label><span>Tipo de evaluación</span><select name="assessment"><option value="all">Todos</option><option value="quiz"<?php echo $assessmenttype === 'quiz' ? ' selected' : ''; ?>>Quiz</option><option value="simulation"<?php echo $assessmenttype === 'simulation' ? ' selected' : ''; ?>>Simulación</option><option value="exam"<?php echo $assessmenttype === 'exam' ? ' selected' : ''; ?>>Exámenes</option></select></label>
        <label><span>Estado</span><select name="status"><option value="all">Todos</option><option value="passed"<?php echo $statusfilter === 'passed' ? ' selected' : ''; ?>>Aprobados</option><option value="failed"<?php echo $statusfilter === 'failed' ? ' selected' : ''; ?>>No aprobados</option><option value="incomplete"<?php echo $statusfilter === 'incomplete' ? ' selected' : ''; ?>>Incompletos</option></select></label>
        <label><span>Ordenar</span><select name="sort"><option value="recent">Actividad reciente</option><option value="name"<?php echo $sort === 'name' ? ' selected' : ''; ?>>Nombre</option><option value="result"<?php echo $sort === 'result' ? ' selected' : ''; ?>>Mejor promedio</option></select></label>
        <div class="ncbahm-report__filter-actions"><button class="ncbahm-report__button" type="submit">Aplicar</button><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo $reporturl->out(false); ?>">Limpiar</a></div>
    </form>
    <?php if ($selectedstudent === null): ?>
    <section class="ncbahm-report__panel"><header class="ncbahm-report__panel-head"><h3>Estudiantes con evaluaciones NCBAHM</h3><span><?php echo $totalstudents; ?> resultado<?php echo $totalstudents === 1 ? '' : 's'; ?></span></header><div class="ncbahm-report__table-wrap"><table><thead><tr><th>Estudiante</th><th>Promedio</th><th>Quiz</th><th>Simulaciones</th><th>Exámenes</th><th>Última actividad</th><th></th></tr></thead><tbody>
    <?php if ($pagedstudents === []): ?><tr><td class="ncbahm-report__empty" colspan="7">No existen evaluaciones NCBAHM con estos filtros.</td></tr><?php else: foreach ($pagedstudents as $userid => $stat): ?>
        <tr><td><strong><?php echo s($stat['name']); ?></strong><br><small><?php echo s($stat['email']); ?></small></td><td><span class="ncbahm-report__score"><?php echo $stat['average'] === null ? '—' : s($stat['average'] . '%'); ?></span></td><td><?php echo (int)$stat['quiz']; ?></td><td><?php echo (int)$stat['simulation']; ?></td><td><?php echo (int)$stat['exam']; ?></td><td><?php echo s($datevalue($stat['lastactivity'])); ?></td><td><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo (new moodle_url($reporturl, $pageparams + ['userid' => $userid]))->out(false); ?>">Ver evaluaciones</a></td></tr>
    <?php endforeach; endif; ?></tbody></table></div>
    <?php if ($totalstudents > $perpage): ?><nav class="ncbahm-report__pagination"><?php for ($index = 0; $index < (int)ceil($totalstudents / $perpage); $index++): ?><a href="<?php echo (new moodle_url($reporturl, $pageparams + ['page' => $index]))->out(false); ?>"><?php echo $index + 1; ?></a><?php endfor; ?></nav><?php endif; ?>
    </section>
    <?php else: ?>
    <div class="ncbahm-report__actions" style="margin-bottom:14px"><a class="ncbahm-report__button ncbahm-report__button--secondary" href="<?php echo (new moodle_url($reporturl, array_diff_key($pageparams, ['userid' => true])))->out(false); ?>">← Todos los estudiantes</a></div>
    <div class="ncbahm-report__cards"><article class="ncbahm-report__card"><span>Promedio</span><strong><?php echo $selectedstudent['average'] === null ? '—' : s($selectedstudent['average'] . '%'); ?></strong></article><article class="ncbahm-report__card"><span>Quiz</span><strong><?php echo (int)$selectedstudent['quiz']; ?></strong></article><article class="ncbahm-report__card"><span>Simulaciones</span><strong><?php echo (int)$selectedstudent['simulation']; ?></strong></article><article class="ncbahm-report__card"><span>Exámenes</span><strong><?php echo (int)$selectedstudent['exam']; ?></strong></article></div>
    <div class="alert alert-info">El enlace debe abrirlo el estudiante en su propia cuenta. Los intentos terminados comienzan de nuevo; los incompletos sin respuestas guardadas también deben reiniciarse.</div>
    <section class="ncbahm-report__panel"><header class="ncbahm-report__panel-head"><h3><?php echo s($selectedstudent['name']); ?></h3><span><?php echo count($selectedattempts); ?> intento<?php echo count($selectedattempts) === 1 ? '' : 's'; ?></span></header><div class="ncbahm-report__table-wrap"><table><thead><tr><th>Tipo</th><th>Evaluación</th><th>Resultado</th><th>Estado</th><th>Duración</th><th>Fecha</th><th>Acción</th></tr></thead><tbody>
    <?php foreach ($selectedattempts as $attempt): $statuslabels = ['passed' => 'Aprobado', 'failed' => 'No aprobado', 'incomplete' => 'Incompleto']; ?>
        <tr><td><?php echo s($attempt['kindname']); ?></td><td><strong><?php echo s($attempt['name']); ?></strong></td><td><span class="ncbahm-report__score"><?php echo s($attempt['percentage'] . '%'); ?></span></td><td><span class="ncbahm-report__status ncbahm-report__status--<?php echo s($attempt['status']); ?>"><?php echo s($statuslabels[$attempt['status']]); ?></span></td><td><?php echo s($durationvalue($attempt['duration'])); ?></td><td><?php echo s($datevalue($attempt['timecompleted'])); ?></td><td><button type="button" class="ncbahm-report__button ncbahm-report__button--secondary ncbahm-report__button--compact" data-copy-assessment-url="<?php echo s($attempt['restarturl']); ?>">Copiar enlace para el estudiante</button></td></tr>
    <?php endforeach; ?></tbody></table></div></section>
    <?php endif; ?>
</main>
<script>
document.querySelectorAll('[data-copy-assessment-url]').forEach(button => {
    button.addEventListener('click', async () => {
        const originalLabel = button.textContent;
        try {
            await navigator.clipboard.writeText(button.dataset.copyAssessmentUrl || '');
            button.textContent = 'Enlace copiado';
        } catch (error) {
            window.prompt('Copia este enlace y envíalo al estudiante:', button.dataset.copyAssessmentUrl || '');
        }
        window.setTimeout(() => { button.textContent = originalLabel; }, 2500);
    });
});
</script>
<?php
echo $OUTPUT->footer();
