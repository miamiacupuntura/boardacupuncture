<?php
// Student-facing readiness dashboard calculated from the local Formula Quiz history.

require_once(__DIR__ . '/../../config.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';

$pageparams = $ises ? ['lang' => 'es'] : [];
$pageurl = new moodle_url('/blocks/myprogress/study-score.php', $pageparams);
$languageurl = new moodle_url('/blocks/myprogress/study-score.php', $ises ? [] : ['lang' => 'es']);
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => 'herbology', 'view' => 'library'] + ($ises ? ['lang' => 'es'] : []));
$quizurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'formula-practice-es.html' : 'formula-practice.html'));
$weakquizurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'formula-practice-es.html' : 'formula-practice.html'), ['focus' => 'weak']);
$patternurl = new moodle_url('/blocks/myprogress/smart-study-tools.php', ['mode' => 'pattern'] + ($ises ? ['lang' => 'es'] : []));

$formuladata = json_decode((string)file_get_contents(__DIR__ . '/formulas-study-guide-data.json'), true);
$formulas = [];
foreach (($formuladata['formulas'] ?? []) as $formula) {
    $formulas[] = [
        'number' => (int)($formula['number'] ?? 0),
        'name' => (string)($formula['name'] ?? ''),
        'category' => (string)($formula[$ises ? 'categoryEs' : 'category'] ?? $formula['category'] ?? ''),
    ];
}

$config = [
    'formulas' => $formulas,
    'urls' => [
        'quiz' => $quizurl->out(false),
        'weakQuiz' => $weakquizurl->out(false),
        'pattern' => $patternurl->out(false),
    ],
    'copy' => [
        'answers' => $ises ? 'respuestas registradas' : 'recorded answers',
        'formulas' => $ises ? 'fórmulas practicadas' : 'formulas practiced',
        'lastActivity' => $ises ? 'Última actividad' : 'Last activity',
        'noActivity' => $ises ? 'Sin actividad todavía' : 'No activity yet',
        'lowConfidence' => $ises ? 'Muestra inicial' : 'Early sample',
        'mediumConfidence' => $ises ? 'Estimación en desarrollo' : 'Developing estimate',
        'highConfidence' => $ises ? 'Muestra sólida' : 'Strong sample',
        'beginning' => $ises ? 'Construyendo fundamentos' : 'Building foundations',
        'developing' => $ises ? 'En desarrollo' : 'Developing',
        'progressing' => $ises ? 'Progresando' : 'Progressing',
        'ready' => $ises ? 'Preparación sólida' : 'Strong preparation',
        'mastery' => $ises ? 'Dominio consistente' : 'Consistent mastery',
        'accuracyDetail' => $ises ? 'Aciertos sobre todas las respuestas.' : 'Correct answers across all attempts.',
        'coverageDetail' => $ises ? 'Fórmulas diferentes vistas de 63.' : 'Different formulas seen out of 63.',
        'retentionDetail' => $ises ? 'Precisión en fórmulas repetidas al menos dos veces.' : 'Accuracy on formulas repeated at least twice.',
        'consistencyDetail' => $ises ? 'Fórmulas repetidas que alcanzan 80% o más.' : 'Repeated formulas reaching 80% or higher.',
        'notEnough' => $ises ? 'Se necesitan repeticiones' : 'More repetitions needed',
        'categoryEmpty' => $ises ? 'Completa un quiz para generar resultados por categoría.' : 'Complete a Quiz to generate category results.',
        'weakEmpty' => $ises ? 'Aún no hay fórmulas débiles registradas.' : 'No weak formulas are recorded yet.',
        'attempts' => $ises ? 'intentos' : 'attempts',
        'coverage' => $ises ? 'cobertura' : 'coverage',
        'diagnosticTitle' => $ises ? 'Comienza con un diagnóstico' : 'Start with a diagnostic',
        'diagnosticBody' => $ises ? 'Responde al menos 10 preguntas para producir una estimación más estable.' : 'Answer at least 10 questions to produce a more stable estimate.',
        'diagnosticAction' => $ises ? 'Iniciar quiz general' : 'Start general Quiz',
        'coverageTitle' => $ises ? 'Amplía la cobertura' : 'Expand your coverage',
        'coverageBody' => $ises ? 'Has practicado menos de la mitad de las 63 fórmulas. Añade una categoría nueva.' : 'You have practiced fewer than half of the 63 formulas. Add a new category.',
        'coverageAction' => $ises ? 'Elegir categoría' : 'Choose a category',
        'weakTitle' => $ises ? 'Refuerza errores recurrentes' : 'Reinforce recurring errors',
        'weakBody' => $ises ? 'El quiz personalizado priorizará las fórmulas que están por debajo de 80%.' : 'The personalized Quiz will prioritize formulas currently below 80%.',
        'weakAction' => $ises ? 'Practicar temas débiles' : 'Practice weak topics',
        'retentionTitle' => $ises ? 'Confirma la retención' : 'Confirm retention',
        'retentionBody' => $ises ? 'Repite las fórmulas estudiadas; un acierto aislado todavía no confirma retención.' : 'Repeat studied formulas; one correct answer does not yet confirm retention.',
        'retentionAction' => $ises ? 'Repetir quiz' : 'Repeat Quiz',
        'patternTitle' => $ises ? 'Fortalece Pattern → Formula' : 'Strengthen Pattern → Formula',
        'patternBody' => $ises ? 'Usa las pistas verificadas para distinguir las fórmulas de tu categoría más débil.' : 'Use verified clues to distinguish formulas in your weakest category.',
        'patternAction' => $ises ? 'Abrir Pattern Finder' : 'Open Pattern Finder',
        'maintainTitle' => $ises ? 'Mantén el dominio' : 'Maintain mastery',
        'maintainBody' => $ises ? 'Tu historial es consistente. Continúa con una sesión mixta y repaso espaciado.' : 'Your history is consistent. Continue with a mixed session and spaced review.',
        'maintainAction' => $ises ? 'Iniciar sesión mixta' : 'Start mixed session',
    ],
];

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Mi puntuación de estudio' : 'My Study Score');
$PAGE->set_heading($ises ? 'Mi puntuación de estudio' : 'My Study Score');
$PAGE->set_cacheable(false);

$configjson = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$PAGE->requires->js_init_code('(function(config) {
    const root = document.querySelector("[data-study-score]");
    if (!root) return;

    const clamp = value => Math.max(0, Math.min(100, Math.round(Number(value) || 0)));
    const percent = (correct, total) => total > 0 ? clamp((correct / total) * 100) : 0;
    const readHistory = () => {
        try {
            const value = JSON.parse(localStorage.getItem("formula-practice-progress-v1") || "null");
            if (value && typeof value === "object") return value;
        } catch (_) {}
        return {total: 0, correct: 0, formulas: {}, lastAt: null};
    };
    const setText = (selector, value) => {
        const element = root.querySelector(selector);
        if (element) element.textContent = value;
    };

    const history = readHistory();
    const total = Math.max(0, Number(history.total || 0));
    const correct = Math.max(0, Number(history.correct || 0));
    const formulaMap = new Map(config.formulas.map(formula => [String(formula.number), formula]));
    const availableByCategory = new Map();
    config.formulas.forEach(formula => availableByCategory.set(formula.category, (availableByCategory.get(formula.category) || 0) + 1));

    const attempts = Object.entries(history.formulas || {}).map(([number, value]) => ({
        number,
        total: Math.max(0, Number(value.total || 0)),
        correct: Math.max(0, Number(value.correct || 0)),
        formula: formulaMap.get(String(number)) || {number, name: "Formula #" + number, category: "Other"}
    })).filter(item => item.total > 0);
    const repeated = attempts.filter(item => item.total >= 2);
    const repeatedTotal = repeated.reduce((sum, item) => sum + item.total, 0);
    const repeatedCorrect = repeated.reduce((sum, item) => sum + item.correct, 0);

    const accuracy = percent(correct, total);
    const coverage = percent(attempts.length, config.formulas.length || 63);
    const retention = repeatedTotal ? percent(repeatedCorrect, repeatedTotal) : 0;
    const consistent = repeated.filter(item => percent(item.correct, item.total) >= 80).length;
    const consistency = repeated.length ? percent(consistent, repeated.length) : 0;
    const readiness = total ? clamp(accuracy * 0.40 + coverage * 0.25 + retention * 0.20 + consistency * 0.15) : 0;

    const level = readiness >= 90 ? config.copy.mastery
        : readiness >= 75 ? config.copy.ready
        : readiness >= 60 ? config.copy.progressing
        : readiness >= 40 ? config.copy.developing
        : config.copy.beginning;
    const confidence = total >= 30 ? config.copy.highConfidence : total >= 10 ? config.copy.mediumConfidence : config.copy.lowConfidence;

    const ring = root.querySelector("[data-readiness-ring]");
    if (ring) ring.style.setProperty("--study-score", readiness);
    setText("[data-readiness-value]", readiness);
    setText("[data-readiness-level]", level);
    setText("[data-confidence]", confidence);
    setText("[data-summary]", total + " " + config.copy.answers + " · " + attempts.length + " " + config.copy.formulas);
    setText("[data-last-activity]", history.lastAt ? config.copy.lastActivity + ": " + new Intl.DateTimeFormat(undefined, {dateStyle: "medium"}).format(new Date(history.lastAt)) : config.copy.noActivity);

    const metrics = {
        accuracy: {value: accuracy + "%", width: accuracy, detail: config.copy.accuracyDetail},
        coverage: {value: coverage + "%", width: coverage, detail: attempts.length + "/63 · " + config.copy.coverageDetail},
        retention: {value: repeatedTotal ? retention + "%" : "—", width: retention, detail: repeatedTotal ? config.copy.retentionDetail : config.copy.notEnough},
        consistency: {value: repeated.length ? consistency + "%" : "—", width: consistency, detail: repeated.length ? config.copy.consistencyDetail : config.copy.notEnough}
    };
    Object.entries(metrics).forEach(([key, metric]) => {
        const card = root.querySelector("[data-metric=\"" + key + "\"]");
        if (!card) return;
        const value = card.querySelector("[data-metric-value]");
        const bar = card.querySelector("[data-metric-bar]");
        const detail = card.querySelector("[data-metric-detail]");
        if (value) value.textContent = metric.value;
        if (bar) bar.style.width = metric.width + "%";
        if (detail) detail.textContent = metric.detail;
    });

    const categoryStats = new Map();
    attempts.forEach(item => {
        const name = item.formula.category || "Other";
        const stat = categoryStats.get(name) || {name, total: 0, correct: 0, formulas: new Set()};
        stat.total += item.total;
        stat.correct += item.correct;
        stat.formulas.add(item.number);
        categoryStats.set(name, stat);
    });
    const categories = [...categoryStats.values()].map(stat => {
        const categoryAccuracy = percent(stat.correct, stat.total);
        const categoryCoverage = percent(stat.formulas.size, availableByCategory.get(stat.name) || stat.formulas.size);
        return {...stat, accuracy: categoryAccuracy, coverage: categoryCoverage, score: clamp(categoryAccuracy * 0.75 + categoryCoverage * 0.25)};
    }).sort((left, right) => left.score - right.score || right.total - left.total);

    const categoryBody = root.querySelector("[data-category-body]");
    if (categoryBody) {
        categoryBody.replaceChildren();
        if (!categories.length) {
            const row = document.createElement("tr");
            const cell = document.createElement("td");
            cell.colSpan = 4;
            cell.className = "study-score__empty-cell";
            cell.textContent = config.copy.categoryEmpty;
            row.append(cell);
            categoryBody.append(row);
        } else {
            categories.forEach(category => {
                const row = document.createElement("tr");
                [category.name, category.score + "%", category.accuracy + "%", category.formulas.size + "/" + (availableByCategory.get(category.name) || category.formulas.size) + " · " + config.copy.coverage].forEach(value => {
                    const cell = document.createElement("td");
                    cell.textContent = value;
                    row.append(cell);
                });
                categoryBody.append(row);
            });
        }
    }

    const weak = attempts.filter(item => percent(item.correct, item.total) < 80)
        .sort((left, right) => percent(left.correct, left.total) - percent(right.correct, right.total) || right.total - left.total)
        .slice(0, 5);
    const weakList = root.querySelector("[data-weak-list]");
    if (weakList) {
        weakList.replaceChildren();
        if (!weak.length) {
            const item = document.createElement("li");
            item.className = "study-score__empty-item";
            item.textContent = config.copy.weakEmpty;
            weakList.append(item);
        } else {
            weak.forEach(result => {
                const item = document.createElement("li");
                const copy = document.createElement("span");
                const score = document.createElement("strong");
                copy.textContent = result.formula.name;
                score.textContent = percent(result.correct, result.total) + "% · " + result.total + " " + config.copy.attempts;
                item.append(copy, score);
                weakList.append(item);
            });
        }
    }

    const recommendations = [];
    const addRecommendation = (title, body, action, url) => {
        if (!recommendations.some(item => item.title === title)) recommendations.push({title, body, action, url});
    };
    if (total < 10) addRecommendation(config.copy.diagnosticTitle, config.copy.diagnosticBody, config.copy.diagnosticAction, config.urls.quiz);
    if (coverage < 50) addRecommendation(config.copy.coverageTitle, config.copy.coverageBody, config.copy.coverageAction, config.urls.quiz);
    if (weak.length) addRecommendation(config.copy.weakTitle, config.copy.weakBody, config.copy.weakAction, config.urls.weakQuiz);
    if (total >= 10 && repeated.length < Math.min(5, attempts.length)) addRecommendation(config.copy.retentionTitle, config.copy.retentionBody, config.copy.retentionAction, config.urls.quiz);
    if (categories.length && categories[0].score < 70) addRecommendation(config.copy.patternTitle, config.copy.patternBody, config.copy.patternAction, config.urls.pattern);
    if (!recommendations.length) addRecommendation(config.copy.maintainTitle, config.copy.maintainBody, config.copy.maintainAction, config.urls.quiz);

    const recommendationsNode = root.querySelector("[data-recommendations]");
    if (recommendationsNode) {
        recommendationsNode.replaceChildren();
        recommendations.slice(0, 3).forEach((recommendation, index) => {
            const article = document.createElement("article");
            article.className = "study-score__recommendation";
            const number = document.createElement("span");
            number.className = "study-score__recommendation-number";
            number.textContent = String(index + 1);
            const content = document.createElement("div");
            const title = document.createElement("h4");
            const body = document.createElement("p");
            const link = document.createElement("a");
            title.textContent = recommendation.title;
            body.textContent = recommendation.body;
            link.textContent = recommendation.action + " →";
            link.href = recommendation.url;
            content.append(title, body, link);
            article.append(number, content);
            recommendationsNode.append(article);
        });
    }

    const empty = root.querySelector("[data-empty-history]");
    if (empty) empty.hidden = total > 0;
}(' . $configjson . '));');

echo $OUTPUT->header();
?>
<style>
.study-score{width:min(1180px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}
.study-score__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:22px;margin-bottom:18px;padding:25px;border:1px solid #f0c5a5;border-top:6px solid #ea6a19;border-radius:15px;background:linear-gradient(135deg,#fff8f1,#f7fbff)}
.study-score__eyebrow{margin:0 0 7px;color:#c54b08;font-size:12px;font-weight:950;letter-spacing:.09em;text-transform:uppercase}.study-score h2{margin:0 0 8px;font-size:clamp(30px,4vw,44px)}.study-score__lead{max-width:760px;margin:0;color:#526b84;line-height:1.55}
.study-score__actions{display:flex;flex-wrap:wrap;gap:9px}.study-score__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important}.study-score__button:hover{background:#eef7f5}
.study-score__overview{display:grid;grid-template-columns:310px minmax(0,1fr);gap:18px;margin-bottom:18px}.study-score__panel{padding:22px;border:1px solid #d5e0e6;border-radius:14px;background:#fff;box-shadow:0 5px 18px rgba(23,50,77,.05)}
.study-score__overall{text-align:center}.study-score__ring{--study-score:0;width:190px;height:190px;display:grid;place-items:center;margin:2px auto 15px;border-radius:50%;background:conic-gradient(#ea6a19 calc(var(--study-score) * 1%),#e8eef2 0);position:relative}.study-score__ring:after{content:"";position:absolute;inset:16px;border-radius:50%;background:#fff}.study-score__ring-copy{position:relative;z-index:1}.study-score__ring strong{display:block;font-size:49px;line-height:1}.study-score__ring small{font-weight:900;color:#64748b}.study-score__overall h3{margin:0 0 4px;font-size:23px}.study-score__confidence{display:inline-flex;margin:8px 0;padding:5px 10px;border-radius:999px;background:#fff1df;color:#9a3f08;font-size:12px;font-weight:900}.study-score__meta{margin:5px 0;color:#526b84;font-size:13px}
.study-score__metrics{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.study-score__metric{padding:17px;border:1px solid #d8e2e8;border-radius:11px;background:#fbfdff}.study-score__metric-header{display:flex;justify-content:space-between;gap:12px;align-items:baseline}.study-score__metric h3{margin:0;font-size:16px}.study-score__metric strong{font-size:24px}.study-score__track{height:9px;margin:11px 0;border-radius:999px;background:#e5edf1;overflow:hidden}.study-score__track span{display:block;width:0;height:100%;border-radius:inherit;background:linear-gradient(90deg,#0d8875,#44b59e)}.study-score__metric p{margin:0;color:#60758a;font-size:12px;line-height:1.45}
.study-score__empty{margin-bottom:18px;padding:15px 17px;border-left:5px solid #6d28d9;border-radius:10px;background:#f5f0ff;color:#4c1d95;font-weight:800}.study-score__empty[hidden]{display:none}
.study-score__grid{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(280px,.75fr);gap:18px;margin-bottom:18px}.study-score__panel h3{margin:0 0 6px;font-size:22px}.study-score__subcopy{margin:0 0 16px;color:#60758a;font-size:13px}
.study-score__table-wrap{overflow:auto}.study-score__table{width:100%;border-collapse:collapse}.study-score__table th,.study-score__table td{padding:11px 10px;border-bottom:1px solid #dae4e9;text-align:left;font-size:13px}.study-score__table th{background:#eef8f6;color:#075f50;font-size:12px}.study-score__table td:nth-child(n+2){font-weight:850;white-space:nowrap}.study-score__empty-cell{text-align:center!important;color:#64748b!important;font-weight:700!important}
.study-score__weak-list{display:grid;gap:9px;margin:15px 0 0;padding:0;list-style:none}.study-score__weak-list li{display:flex;justify-content:space-between;gap:12px;padding:11px;border-radius:9px;background:#f8fafc;color:#314e63}.study-score__weak-list strong{color:#b44209;font-size:12px;white-space:nowrap}.study-score__empty-item{display:block!important;text-align:center;color:#64748b!important}
.study-score__recommendations{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:13px;margin-top:16px}.study-score__recommendation{display:flex;gap:12px;padding:17px;border:1px solid #d6dfe5;border-top:4px solid #6d28d9;border-radius:11px;background:#fff}.study-score__recommendation-number{display:grid;width:30px;height:30px;flex:0 0 30px;place-items:center;border-radius:50%;background:#6d28d9;color:#fff;font-weight:950}.study-score__recommendation h4{margin:2px 0 6px;font-size:16px}.study-score__recommendation p{margin:0 0 10px;color:#60758a;font-size:13px;line-height:1.45}.study-score__recommendation a{color:#5b21b6!important;font-weight:900;text-decoration:none!important}.study-score__method{margin-top:18px;padding:14px 16px;border-left:5px solid #0d8875;border-radius:9px;background:#effaf7;color:#285d54;line-height:1.5}
@media(max-width:850px){.study-score__hero{display:block}.study-score__actions{margin-top:16px}.study-score__overview,.study-score__grid{grid-template-columns:1fr}.study-score__recommendations{grid-template-columns:1fr}}
@media(max-width:560px){.study-score__metrics{grid-template-columns:1fr}.study-score__weak-list li{display:grid}}
</style>
<main class="study-score" data-study-score>
    <header class="study-score__hero">
        <div>
            <p class="study-score__eyebrow"><?php echo $ises ? 'PROGRESO LOCAL · PRIVADO · ACCIONABLE' : 'LOCAL · PRIVATE · ACTIONABLE PROGRESS'; ?></p>
            <h2><?php echo $ises ? 'Mi puntuación de estudio' : 'My Study Score'; ?></h2>
            <p class="study-score__lead"><?php echo $ises
                ? 'Convierte tu historial del Formula Quiz en una estimación de preparación, fortalezas y próximos pasos.'
                : 'Turn your Formula Quiz history into a readiness estimate, strengths and clear next steps.'; ?></p>
        </div>
        <div class="study-score__actions">
            <a class="study-score__button" href="<?php echo $libraryurl->out(false); ?>"><?php echo $ises ? '← Main Study Guide' : '← Main Study Guide'; ?></a>
            <a class="study-score__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
        </div>
    </header>

    <aside class="study-score__empty" data-empty-history>
        <?php echo $ises
            ? 'Todavía no hay historial en este dispositivo. Completa un Formula Quiz y regresa para ver tu score real.'
            : 'There is no history on this device yet. Complete a Formula Quiz and return to see your real score.'; ?>
    </aside>

    <section class="study-score__overview" aria-label="<?php echo $ises ? 'Resumen del score' : 'Score summary'; ?>">
        <article class="study-score__panel study-score__overall">
            <div class="study-score__ring" data-readiness-ring>
                <div class="study-score__ring-copy"><strong><span data-readiness-value>0</span></strong><small>/ 100</small></div>
            </div>
            <h3 data-readiness-level><?php echo $ises ? 'Construyendo fundamentos' : 'Building foundations'; ?></h3>
            <span class="study-score__confidence" data-confidence><?php echo $ises ? 'Muestra inicial' : 'Early sample'; ?></span>
            <p class="study-score__meta" data-summary>0 <?php echo $ises ? 'respuestas registradas' : 'recorded answers'; ?></p>
            <p class="study-score__meta" data-last-activity><?php echo $ises ? 'Sin actividad todavía' : 'No activity yet'; ?></p>
        </article>

        <div class="study-score__metrics">
            <?php
            $metrics = [
                'accuracy' => $ises ? 'Precisión' : 'Accuracy',
                'coverage' => $ises ? 'Cobertura' : 'Coverage',
                'retention' => $ises ? 'Retención' : 'Retention',
                'consistency' => $ises ? 'Consistencia' : 'Consistency',
            ];
            foreach ($metrics as $key => $label) {
            ?>
                <article class="study-score__metric" data-metric="<?php echo $key; ?>">
                    <header class="study-score__metric-header"><h3><?php echo $label; ?></h3><strong data-metric-value>0%</strong></header>
                    <div class="study-score__track" aria-hidden="true"><span data-metric-bar></span></div>
                    <p data-metric-detail></p>
                </article>
            <?php } ?>
        </div>
    </section>

    <section class="study-score__grid">
        <article class="study-score__panel">
            <h3><?php echo $ises ? 'Rendimiento por categoría' : 'Performance by category'; ?></h3>
            <p class="study-score__subcopy"><?php echo $ises
                ? 'El score de categoría combina 75% precisión y 25% cobertura.'
                : 'The category score combines 75% accuracy and 25% coverage.'; ?></p>
            <div class="study-score__table-wrap">
                <table class="study-score__table">
                    <thead><tr>
                        <th><?php echo $ises ? 'Categoría' : 'Category'; ?></th>
                        <th>Score</th>
                        <th><?php echo $ises ? 'Precisión' : 'Accuracy'; ?></th>
                        <th><?php echo $ises ? 'Cobertura' : 'Coverage'; ?></th>
                    </tr></thead>
                    <tbody data-category-body></tbody>
                </table>
            </div>
        </article>

        <article class="study-score__panel">
            <h3><?php echo $ises ? 'Fórmulas que necesitan repaso' : 'Formulas needing review'; ?></h3>
            <p class="study-score__subcopy"><?php echo $ises ? 'Hasta cinco fórmulas por debajo de 80%.' : 'Up to five formulas currently below 80%.'; ?></p>
            <ul class="study-score__weak-list" data-weak-list></ul>
        </article>
    </section>

    <section class="study-score__panel">
        <h3><?php echo $ises ? 'Tus próximos pasos' : 'Your next steps'; ?></h3>
        <p class="study-score__subcopy"><?php echo $ises ? 'Tres recomendaciones calculadas desde tu historial local.' : 'Three recommendations calculated from your local history.'; ?></p>
        <div class="study-score__recommendations" data-recommendations></div>
    </section>

    <aside class="study-score__method">
        <strong><?php echo $ises ? 'Cómo se calcula:' : 'How it is calculated:'; ?></strong>
        <?php echo $ises
            ? ' 40% precisión, 25% cobertura, 20% retención y 15% consistencia. Usa únicamente el historial local de las 63 fórmulas del Formula Quiz. Es una guía de estudio, no una predicción de aprobación ni una evaluación clínica.'
            : ' 40% accuracy, 25% coverage, 20% retention and 15% consistency. It uses only the local history for the 63 formulas in the Formula Quiz. This is a study guide, not a pass prediction or clinical assessment.'; ?>
    </aside>
</main>
<?php
echo $OUTPUT->footer();
