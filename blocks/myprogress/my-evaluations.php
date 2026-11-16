<?php
// Personal evaluation history across NCCAOM Focused Practice and NCBAHM Comprehensive Review.

require_once(__DIR__ . '/../../config.php');

require_login();

$requestedlang = optional_param('lang', '', PARAM_ALPHA);
$ises = $requestedlang === 'es' || ($requestedlang === '' && preg_match('/^es(?:[_-].*)?$/i', current_language()));
$text = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};

$pageurl = new moodle_url('/blocks/myprogress/my-evaluations.php', ['lang' => $ises ? 'es' : 'en']);
$languageurl = new moodle_url('/blocks/myprogress/my-evaluations.php', ['lang' => $ises ? 'en' : 'es']);
$dashboardurl = new moodle_url('/my/');

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($text('My Evaluations', 'Mis evaluaciones'));
$PAGE->set_heading($text('My Evaluations', 'Mis evaluaciones'));
$PAGE->set_cacheable(false);

$subjects = [
    30 => $text('Foundations', 'Fundamentos'),
    34 => $text('Acupuncture & Point Location', 'Acupuntura y localización de puntos'),
    45 => $text('Chinese Herbology · Single Herbs', 'Herbología china · Hierbas individuales'),
    44 => $text('Chinese Herbology · Formulas', 'Herbología china · Fórmulas'),
    117 => $text('Chinese Herbology · Formulas', 'Herbología china · Fórmulas'),
    36 => $text('Biomedicine', 'Biomedicina'),
    113 => $text('Clean Needle Techniques', 'Técnicas de aguja limpia'),
];
$nccaomcourseids = array_keys($subjects);
$scorms = $DB->get_records_list(
    'scorm',
    'course',
    $nccaomcourseids,
    'course ASC, name ASC',
    'id,course,name'
);
$nccaombyassessment = [];
if ($scorms !== []) {
    [$scorminsql, $scormparams] = $DB->get_in_or_equal(array_keys($scorms), SQL_PARAMS_NAMED, 'personalscorm');
    $records = $DB->get_records_sql(
        "SELECT v.id, a.id AS attemptid, a.scormid, v.value, v.timemodified
           FROM {scorm_attempt} a
           JOIN {scorm_scoes_value} v ON v.attemptid = a.id
           JOIN {scorm_element} e ON e.id = v.elementid
          WHERE a.userid = :userid
            AND a.scormid $scorminsql
            AND e.element IN (:scoreelement12, :scoreelement2004)
       ORDER BY v.timemodified DESC",
        ['userid' => $USER->id, 'scoreelement12' => 'cmi.core.score.raw', 'scoreelement2004' => 'cmi.score.raw']
            + $scormparams
    );
    foreach ($records as $record) {
        if (!isset($scorms[$record->scormid]) || !is_numeric($record->value)) {
            continue;
        }
        $scormid = (int)$record->scormid;
        $score = max(0.0, min(100.0, (float)$record->value));
        if (!isset($nccaombyassessment[$scormid])) {
            $nccaombyassessment[$scormid] = [
                'score' => $score,
                'attemptids' => [],
                'lastactivity' => 0,
            ];
        }
        $nccaombyassessment[$scormid]['score'] = max($nccaombyassessment[$scormid]['score'], $score);
        $nccaombyassessment[$scormid]['attemptids'][(int)$record->attemptid] = true;
        $nccaombyassessment[$scormid]['lastactivity'] = max(
            $nccaombyassessment[$scormid]['lastactivity'],
            (int)$record->timemodified
        );
    }
}

$nccaomrows = [];
foreach ($nccaombyassessment as $scormid => $result) {
    $scorm = $scorms[$scormid];
    $nccaomrows[] = [
        'subject' => $subjects[(int)$scorm->course] ?? $text('Other subject', 'Otra materia'),
        'name' => (string)$scorm->name,
        'score' => round((float)$result['score'], 1),
        'attempts' => count($result['attemptids']),
        'lastactivity' => (int)$result['lastactivity'],
        'status' => (float)$result['score'] >= 70 ? 'passed' : 'review',
    ];
}
usort($nccaomrows, static fn(array $left, array $right): int => $right['lastactivity'] <=> $left['lastactivity']);

$quiznames = [
    'bqd_duiyao' => $text('Dui Yao Quiz', 'Quiz de Dui Yao'),
    'bqm_families' => $text('Formula Families and Variations', 'Familias y variaciones de fórmulas'),
    'bqm_nametraps' => $text('Name Traps', 'Trampas de nombres'),
    'bqm_herbparts' => $text('Herb Families and Parts', 'Familias y partes de hierbas'),
    'bqm_formulanames' => $text('Formula Name Traps', 'Trampas de nombres de fórmulas'),
    'bqm_misleading' => $text('Misleading Formula Names', 'Nombres engañosos de fórmulas'),
    'bqm_advanced' => $text('Advanced Name Traps', 'Trampas avanzadas'),
    'bqm_supplements' => $text('Supplemental Herb Families', 'Familias suplementarias de hierbas'),
    'bqm_secondary' => $text('Secondary Herb Actions', 'Acciones secundarias de hierbas'),
];
$formulabankdirectories = [
    $CFG->dirroot . '/study-guide/comparisons',
    '/var/www/acupuncturetests-study-guide/current/comparisons',
    dirname($CFG->dirroot) . '/study-guide/comparisons',
    '/var/www/html/study-guide/comparisons',
];
foreach (array_unique($formulabankdirectories) as $directory) {
    $formulabankpath = rtrim($directory, '/') . '/formula-practice-data.json';
    if (!is_readable($formulabankpath)) {
        continue;
    }
    $formulafiles = [$formulabankpath];
    $manifestpath = rtrim($directory, '/') . '/formula-modifications-manifest.json';
    if (is_readable($manifestpath)) {
        $manifest = json_decode(file_get_contents($manifestpath), true);
        foreach ($manifest['files'] ?? [] as $filename) {
            $supplementpath = rtrim($directory, '/') . '/' . basename((string)$filename);
            if (is_readable($supplementpath)) {
                $formulafiles[] = $supplementpath;
            }
        }
    }
    foreach (array_unique($formulafiles) as $formulafile) {
        $formulabank = json_decode(file_get_contents($formulafile), true);
        foreach ($formulabank['categories'] ?? [] as $category) {
            $key = clean_param($category['key'] ?? '', PARAM_ALPHANUMEXT);
            $name = trim((string)($category[$ises ? 'es' : 'en'] ?? $category['en'] ?? ''));
            if ($key !== '' && $name !== '') {
                $quiznames['bqf_' . substr(sha1($key), 0, 12)] = $text('Formula Quiz · ', 'Quiz de fórmulas · ') . $name;
            }
        }
    }
    $quiznames['bqf_modifications'] = $text('Formula Modifications Quiz', 'Quiz de modificaciones de fórmulas');
    break;
}

$assessmentname = static function(string $examtype) use (&$quiznames, $text): string {
    $base = str_ends_with($examtype, 'incomplete')
        ? substr($examtype, 0, -strlen('incomplete'))
        : $examtype;
    if (isset($quiznames[$base])) {
        return $quiznames[$base];
    }
    if (preg_match('/^bqh_(\d+)$/', $base, $matches)) {
        return $text('Single Herbs Quiz · Chapter ', 'Quiz de hierbas individuales · Capítulo ') . $matches[1];
    }
    $names = [
        'simulationlinear' => $text('Linear Simulation', 'Simulación lineal'),
        'simulationadaptive' => $text('Adaptive Simulation', 'Simulación adaptativa'),
        'linear' => $text('Linear Board Exam', 'Examen Board lineal'),
        'adaptive' => $text('Adaptive Board Exam', 'Examen Board adaptativo'),
        'clinicalcases' => $text('Clinical Cases Exam', 'Examen de casos clínicos'),
    ];
    if (isset($names[$base])) {
        return $names[$base];
    }
    if (str_starts_with($base, 'bqf_')) {
        return $text('Formula Quiz · supplemental category', 'Quiz de fórmulas · categoría complementaria');
    }
    return ucwords(str_replace(['_', '-'], ' ', $base));
};
$assessmentkind = static function(string $examtype) use ($text): string {
    if (str_starts_with($examtype, 'bq')) {
        return $text('Quiz', 'Quiz');
    }
    if (str_starts_with($examtype, 'simulation')) {
        return $text('Simulation', 'Simulación');
    }
    return $text('Exam', 'Examen');
};

$ncbahmrows = [];
$attempttable = new xmldb_table('block_menu_exam_attempts');
if ($DB->get_manager()->table_exists($attempttable)) {
    $attempts = $DB->get_records(
        'block_menu_exam_attempts',
        ['userid' => $USER->id],
        'timecompleted DESC, id DESC'
    );
    foreach ($attempts as $attempt) {
        $percentage = (int)$attempt->total > 0
            ? round(((float)$attempt->score / (float)$attempt->total) * 100, 1)
            : 0.0;
        $incomplete = str_ends_with((string)$attempt->examtype, 'incomplete');
        $ncbahmrows[] = [
            'kind' => $assessmentkind((string)$attempt->examtype),
            'name' => $assessmentname((string)$attempt->examtype),
            'score' => $percentage,
            'duration' => (int)$attempt->duration,
            'lastactivity' => (int)$attempt->timecompleted,
            'status' => $incomplete ? 'incomplete' : ($percentage >= 70 ? 'passed' : 'review'),
        ];
    }
}

$average = static function(array $rows): ?float {
    if ($rows === []) {
        return null;
    }
    return round(array_sum(array_column($rows, 'score')) / count($rows), 1);
};
$durationvalue = static function(int $seconds) use ($text): string {
    $minutes = intdiv(max(0, $seconds), 60);
    $remaining = max(0, $seconds) % 60;
    return $minutes > 0
        ? $minutes . ' ' . $text('min', 'min') . ' ' . $remaining . ' s'
        : $remaining . ' s';
};
$datevalue = static function(int $timestamp) use ($text): string {
    return $timestamp > 0
        ? userdate($timestamp, get_string('strftimedatetimeshort', 'langconfig'))
        : $text('No activity', 'Sin actividad');
};
$statuslabel = static function(string $status) use ($text): string {
    return [
        'passed' => $text('Passed', 'Aprobada'),
        'review' => $text('Needs review', 'Requiere repaso'),
        'incomplete' => $text('Incomplete', 'Incompleta'),
    ][$status] ?? $status;
};

echo $OUTPUT->header();
?>
<style>
.my-evaluations{max-width:1240px;margin:0 auto 42px;color:#173448}.my-evaluations__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:22px 24px;border-left:5px solid #0b8f87;border-radius:12px;background:linear-gradient(135deg,#eef9f6,#fff8ef)}.my-evaluations__hero h2{margin:0 0 5px;font-size:30px}.my-evaluations__hero p{margin:0;color:#52677a}.my-evaluations__actions{display:flex;flex-wrap:wrap;gap:8px}.my-evaluations__button{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:8px 13px;border:1px solid #0b8f87;border-radius:8px;background:#fff;color:#11635f;font-weight:850;text-decoration:none}.my-evaluations__button:hover{background:#0b8f87;color:#fff;text-decoration:none}.my-evaluations__program{margin-bottom:20px;overflow:hidden;border:1px solid #d7e2e7;border-radius:12px;background:#fff}.my-evaluations__program-header{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:17px 19px;background:#f3f8f8}.my-evaluations__program-header h3{margin:0;font-size:21px}.my-evaluations__summary{display:flex;gap:18px}.my-evaluations__summary span{display:block;color:#64748b;font-size:11px;font-weight:800;text-transform:uppercase}.my-evaluations__summary strong{display:block;margin-top:2px;color:#08746f;font-size:20px}.my-evaluations__table-wrap{overflow-x:auto}.my-evaluations table{width:100%;border-collapse:collapse}.my-evaluations th,.my-evaluations td{padding:11px 13px;border-top:1px solid #e1e8ec;text-align:left;vertical-align:middle}.my-evaluations th{background:#fbfcfd;color:#365469;font-size:11px;text-transform:uppercase}.my-evaluations__score{color:#08746f;font-size:17px;font-weight:900}.my-evaluations__status{display:inline-block;padding:5px 8px;border-radius:999px;font-size:12px;font-weight:850}.my-evaluations__status--passed{background:#ddf6ec;color:#076c55}.my-evaluations__status--review{background:#fff0dd;color:#a34d00}.my-evaluations__status--incomplete{background:#edf1f4;color:#596b78}.my-evaluations__empty{padding:28px!important;color:#64748b;text-align:center!important}@media(max-width:700px){.my-evaluations__hero,.my-evaluations__program-header{align-items:stretch;flex-direction:column}.my-evaluations__actions,.my-evaluations__summary{width:100%}.my-evaluations__button{flex:1}.my-evaluations th,.my-evaluations td{padding:9px 8px}}
</style>
<main class="my-evaluations">
    <header class="my-evaluations__hero">
        <div><h2><?php echo s($text('My Evaluations', 'Mis evaluaciones')); ?></h2><p><?php echo s($text(
            'Review your personal results from both study programs.',
            'Revisa tus resultados personales de ambos programas de estudio.'
        )); ?></p></div>
        <nav class="my-evaluations__actions" aria-label="<?php echo s($text('Page actions', 'Acciones de la página')); ?>">
            <a class="my-evaluations__button" href="<?php echo $languageurl->out(false); ?>" hreflang="<?php echo $ises ? 'en' : 'es'; ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
            <a class="my-evaluations__button" href="<?php echo $dashboardurl->out(false); ?>"><?php echo s($text('Back to Dashboard', 'Volver al Dashboard')); ?></a>
        </nav>
    </header>

    <section class="my-evaluations__program" aria-labelledby="nccaom-evaluations-title">
        <header class="my-evaluations__program-header"><h3 id="nccaom-evaluations-title">NCCAOM Focused Practice</h3><div class="my-evaluations__summary"><div><span><?php echo s($text('Evaluations', 'Evaluaciones')); ?></span><strong><?php echo count($nccaomrows); ?></strong></div><div><span><?php echo s($text('Average', 'Promedio')); ?></span><strong><?php echo $average($nccaomrows) === null ? '—' : s($average($nccaomrows) . '%'); ?></strong></div></div></header>
        <div class="my-evaluations__table-wrap"><table><thead><tr><th><?php echo s($text('Subject', 'Materia')); ?></th><th><?php echo s($text('Evaluation', 'Evaluación')); ?></th><th><?php echo s($text('Best result', 'Mejor resultado')); ?></th><th><?php echo s($text('Status', 'Estado')); ?></th><th><?php echo s($text('Attempts', 'Intentos')); ?></th><th><?php echo s($text('Last activity', 'Última actividad')); ?></th></tr></thead><tbody>
        <?php if ($nccaomrows === []): ?><tr><td colspan="6" class="my-evaluations__empty"><?php echo s($text('No NCCAOM evaluations have been recorded yet.', 'Todavía no hay evaluaciones NCCAOM registradas.')); ?></td></tr><?php else: foreach ($nccaomrows as $row): ?>
            <tr><td><?php echo s($row['subject']); ?></td><td><strong><?php echo s($row['name']); ?></strong></td><td><span class="my-evaluations__score"><?php echo s($row['score'] . '%'); ?></span></td><td><span class="my-evaluations__status my-evaluations__status--<?php echo s($row['status']); ?>"><?php echo s($statuslabel($row['status'])); ?></span></td><td><?php echo (int)$row['attempts']; ?></td><td><?php echo s($datevalue($row['lastactivity'])); ?></td></tr>
        <?php endforeach; endif; ?></tbody></table></div>
    </section>

    <section class="my-evaluations__program" aria-labelledby="ncbahm-evaluations-title">
        <header class="my-evaluations__program-header"><h3 id="ncbahm-evaluations-title">NCBAHM Comprehensive Review</h3><div class="my-evaluations__summary"><div><span><?php echo s($text('Evaluations', 'Evaluaciones')); ?></span><strong><?php echo count($ncbahmrows); ?></strong></div><div><span><?php echo s($text('Average', 'Promedio')); ?></span><strong><?php echo $average($ncbahmrows) === null ? '—' : s($average($ncbahmrows) . '%'); ?></strong></div></div></header>
        <div class="my-evaluations__table-wrap"><table><thead><tr><th><?php echo s($text('Type', 'Tipo')); ?></th><th><?php echo s($text('Evaluation', 'Evaluación')); ?></th><th><?php echo s($text('Result', 'Resultado')); ?></th><th><?php echo s($text('Status', 'Estado')); ?></th><th><?php echo s($text('Duration', 'Duración')); ?></th><th><?php echo s($text('Date', 'Fecha')); ?></th></tr></thead><tbody>
        <?php if ($ncbahmrows === []): ?><tr><td colspan="6" class="my-evaluations__empty"><?php echo s($text('No NCBAHM evaluations have been recorded yet.', 'Todavía no hay evaluaciones NCBAHM registradas.')); ?></td></tr><?php else: foreach ($ncbahmrows as $row): ?>
            <tr><td><?php echo s($row['kind']); ?></td><td><strong><?php echo s($row['name']); ?></strong></td><td><span class="my-evaluations__score"><?php echo s($row['score'] . '%'); ?></span></td><td><span class="my-evaluations__status my-evaluations__status--<?php echo s($row['status']); ?>"><?php echo s($statuslabel($row['status'])); ?></span></td><td><?php echo s($durationvalue($row['duration'])); ?></td><td><?php echo s($datevalue($row['lastactivity'])); ?></td></tr>
        <?php endforeach; endif; ?></tbody></table></div>
    </section>
</main>
<?php
echo $OUTPUT->footer();
