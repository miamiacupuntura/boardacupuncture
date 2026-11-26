<?php
// Source-supported guide to formula ingredient hierarchy and functional roles.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/herbology-library-navigation.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA) === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
$query = trim(optional_param('q', '', PARAM_TEXT));

$pageparams = $ises ? ['lang' => 'es'] : [];
if ($query !== '') {
    $pageparams['q'] = $query;
}
$pageurl = new moodle_url('/blocks/myprogress/formula-ingredient-hierarchy.php', $pageparams);
$languageparams = $query !== '' ? ['q' => $query] : [];
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/formula-ingredient-hierarchy.php', $languageparams);
$libraryparams = ['area' => 'herbology', 'view' => 'library'];
if ($ises) {
    $libraryparams['lang'] = 'es';
}
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', $libraryparams);
$theoryurl = new moodle_url('/study-guide/comparisons/formula-theory-jun-chen-zuo-shi' . ($ises ? '-es' : '') . '.html');

$systemcontext = context_system::instance();
$canmanage = has_capability('moodle/site:config', $systemcontext);
$questionsurl = null;
if ($canmanage) {
    $questionparams = [
        'domain' => 'formula_composition',
        'source' => 'scorm',
        'content' => 'formula_hierarchy',
        'detail' => 'hierarchy_ingredients',
    ];
    if ($ises) {
        $questionparams['lang'] = 'es';
    }
    $questionsurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', $questionparams);
}

$datafile = __DIR__ . '/formulas-study-guide-data.json';
$data = is_readable($datafile) ? json_decode(file_get_contents($datafile), true) : null;
if (!is_array($data) || count($data['formulas'] ?? []) !== 63) {
    throw new coding_exception('Formula ingredient hierarchy data is unavailable.');
}

$rows = [];
foreach ($data['formulas'] as $profile) {
    $roles = trim((string)($profile[$ises ? 'ingredientRolesEs' : 'ingredientRoles']
        ?? $profile['ingredientRoles'] ?? ''));
    if ($roles === '') {
        continue;
    }
    $name = trim((string)($profile['name'] ?? ''));
    $category = trim((string)($profile[$ises ? 'categoryEs' : 'category'] ?? $profile['category'] ?? ''));
    $pattern = trim((string)($profile[$ises ? 'patternEs' : 'pattern'] ?? $profile['pattern'] ?? ''));
    $action = trim((string)($profile[$ises ? 'actionEs' : 'action'] ?? $profile['action'] ?? ''));
    $ingredients = trim((string)($profile[$ises ? 'ingredientsEs' : 'ingredients'] ?? $profile['ingredients'] ?? ''));
    $chiefs = trim((string)($profile['kings'] ?? ''));
    $searchtext = core_text::strtolower(implode(' ', [
        $name, $category, $pattern, $action, $ingredients, $chiefs, $roles,
    ]));
    if ($query !== '' && core_text::strpos($searchtext, core_text::strtolower($query)) === false) {
        continue;
    }
    $formulaparams = ['formula' => $name, 'sort' => 'az', 'from' => 'hierarchy'];
    if ($ises) {
        $formulaparams['lang'] = 'es';
    }
    $rows[] = [
        'number' => (int)($profile['number'] ?? 0),
        'name' => $name,
        'category' => $category,
        'pattern' => $pattern,
        'action' => $action,
        'ingredients' => $ingredients,
        'chiefs' => $chiefs,
        'roles' => $roles,
        'url' => new moodle_url('/blocks/myprogress/formula-list.php', $formulaparams),
    ];
}
usort($rows, static fn(array $left, array $right): int => $left['number'] <=> $right['number']);
if ($query === '' && count($rows) !== 19) {
    throw new coding_exception('Formula ingredient hierarchy must contain exactly 19 source-supported profiles.');
}

$PAGE->set_url($pageurl);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Jerarquía de ingredientes de fórmulas' : 'Formula Ingredient Hierarchy');
$PAGE->set_heading($ises ? 'Jerarquía de ingredientes de fórmulas' : 'Formula Ingredient Hierarchy');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
?>
<style>
.formula-hierarchy{width:min(1260px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}.formula-hierarchy__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;padding:24px;border:1px solid #bfd7d4;border-top:5px solid #087c69;border-radius:14px;background:#fff}.formula-hierarchy__eyebrow{margin:0 0 6px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}.formula-hierarchy h2{margin:0 0 8px;font-size:clamp(29px,4vw,42px)}.formula-hierarchy__lead{max-width:800px;margin:0;color:#526b84;line-height:1.55}.formula-hierarchy__actions{display:flex;flex-wrap:wrap;gap:9px}.formula-hierarchy__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}.formula-hierarchy__button:hover{background:#eef7f5}.formula-hierarchy__button--primary{border-color:#087c69;background:#087c69;color:#fff!important}.formula-hierarchy__button--primary:hover{background:#056554}.formula-hierarchy__rule{margin:16px 0;padding:15px 18px;border-left:5px solid #6d28d9;border-radius:9px;background:#f5f0ff;color:#43207b;line-height:1.5}.formula-hierarchy__roles{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:18px 0}.formula-hierarchy__role{padding:17px;border:1px solid #c7d8e2;border-radius:12px;background:#fff}.formula-hierarchy__role b{display:block;margin-bottom:5px;color:#087c69;font-size:17px}.formula-hierarchy__role p{margin:0;color:#526b84;line-height:1.45}.formula-hierarchy__method{margin:0 0 18px;padding:18px;border:1px solid #f0d49f;border-radius:12px;background:#fff8e8}.formula-hierarchy__method h3{margin:0 0 9px}.formula-hierarchy__method ol{margin:0;padding-left:22px;line-height:1.6}.formula-hierarchy__tools{display:grid;grid-template-columns:minmax(240px,1fr) auto;gap:12px;align-items:end;margin-bottom:18px;padding:16px;border:1px solid #c7ddd9;border-radius:12px;background:#f8fcfb}.formula-hierarchy__tools label{display:grid;gap:6px;font-size:12px;font-weight:850}.formula-hierarchy__tools input{width:100%;min-height:46px;padding:9px 12px;border:1px solid #aebfca;border-radius:8px;font:inherit}.formula-hierarchy__summary{margin:0;color:#36566c;font-weight:850}.formula-hierarchy__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.formula-hierarchy__card{padding:18px;border:1px solid #c8d8e1;border-radius:12px;background:#fff;box-shadow:0 4px 14px rgba(23,50,77,.05)}.formula-hierarchy__card-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}.formula-hierarchy__card h3{margin:0 0 5px;font-size:20px}.formula-hierarchy__card h3 a{color:#0f4c81!important;text-decoration:none}.formula-hierarchy__card h3 a:hover{text-decoration:underline}.formula-hierarchy__category{color:#087c69;font-size:12px;font-weight:900;text-transform:uppercase}.formula-hierarchy__chief{display:inline-flex;margin:10px 0;padding:5px 9px;border:1px solid #d58a00;border-radius:999px;background:#fff0b8;color:#6f4300;font-weight:900}.formula-hierarchy__card dl{display:grid;grid-template-columns:100px 1fr;gap:7px 10px;margin:8px 0 0}.formula-hierarchy__card dt{color:#526b84;font-size:12px;font-weight:900;text-transform:uppercase}.formula-hierarchy__card dd{margin:0;line-height:1.48}.formula-hierarchy__roles-note{margin-top:12px;padding:12px;border-left:4px solid #087c69;border-radius:7px;background:#edf8f5;line-height:1.55}.formula-hierarchy__source{margin:18px 0 0;color:#5d7181;font-size:12px}.formula-hierarchy__empty{padding:30px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;text-align:center;font-weight:800}
@media(max-width:920px){.formula-hierarchy__roles,.formula-hierarchy__grid{grid-template-columns:1fr 1fr}}@media(max-width:680px){.formula-hierarchy__hero{display:block}.formula-hierarchy__actions{margin-top:16px}.formula-hierarchy__roles,.formula-hierarchy__grid{grid-template-columns:1fr}.formula-hierarchy__tools{grid-template-columns:1fr}.formula-hierarchy__card dl{grid-template-columns:1fr}.formula-hierarchy__card dd{margin-bottom:6px}}
</style>
<main class="formula-hierarchy">
    <header class="formula-hierarchy__hero">
        <div>
            <p class="formula-hierarchy__eyebrow"><?php echo $ises ? 'COMPOSICIÓN DE FÓRMULAS · JUN–CHEN–ZUO–SHI' : 'FORMULA COMPOSITION · JUN–CHEN–ZUO–SHI'; ?></p>
            <h2><?php echo $ises ? 'Jerarquía de ingredientes' : 'Formula Ingredient Hierarchy'; ?></h2>
            <p class="formula-hierarchy__lead"><?php echo $ises
                ? 'Aprende a reconocer la función estratégica de cada ingrediente y practica con los perfiles respaldados por la fuente y las preguntas convertidas del SCORM antiguo.'
                : 'Learn to recognize each ingredient’s strategic job, then work with source-supported profiles and the converted legacy SCORM questions.'; ?></p>
        </div>
        <div class="formula-hierarchy__actions">
            <a class="formula-hierarchy__button" href="<?php echo $libraryurl->out(false); ?>"><?php echo $ises ? '← Guía de estudio' : '← Study Guide'; ?></a>
            <a class="formula-hierarchy__button" href="<?php echo $theoryurl->out(false); ?>"><?php echo $ises ? 'Abrir teoría completa' : 'Open full theory'; ?></a>
            <?php if ($questionsurl !== null) { ?><a class="formula-hierarchy__button formula-hierarchy__button--primary" href="<?php echo $questionsurl->out(false); ?>"><?php echo $ises ? 'Trabajar preguntas SCORM' : 'Work SCORM questions'; ?></a><?php } ?>
            <a class="formula-hierarchy__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
        </div>
    </header>

    <p class="formula-hierarchy__rule"><strong><?php echo $ises ? 'Regla central:' : 'Core rule:'; ?></strong> <?php echo $ises
        ? 'el rol pertenece al ingrediente dentro de una fórmula concreta; no es un rango permanente de la hierba. La dosis mayor tampoco demuestra por sí sola cuál es el Chief.'
        : 'the role belongs to the ingredient inside a particular formula; it is not the herb’s permanent rank. The largest dose alone also does not prove which ingredient is Chief.'; ?></p>

    <section class="formula-hierarchy__roles" aria-label="<?php echo $ises ? 'Cuatro funciones jerárquicas' : 'Four hierarchy roles'; ?>">
        <article class="formula-hierarchy__role"><b><?php echo $ises ? 'Jun · Chief / Emperador' : 'Jun · Chief / Emperor'; ?></b><p><?php echo $ises ? 'Trata directamente el patrón principal y sus manifestaciones definitorias.' : 'Directly treats the principal pattern and its defining manifestations.'; ?></p></article>
        <article class="formula-hierarchy__role"><b><?php echo $ises ? 'Chen · Deputy / Ministro' : 'Chen · Deputy / Minister'; ?></b><p><?php echo $ises ? 'Refuerza al Chief o trata un patrón coexistente importante.' : 'Reinforces the Chief or directly treats an important coexisting pattern.'; ?></p></article>
        <article class="formula-hierarchy__role"><b><?php echo $ises ? 'Zuo · Assistant / Asistente' : 'Zuo · Assistant'; ?></b><p><?php echo $ises ? 'Apoya, trata síntomas secundarios o corrige toxicidad, aspereza o dirección.' : 'Supports, treats secondary signs, or corrects toxicity, harshness, or direction.'; ?></p></article>
        <article class="formula-hierarchy__role"><b><?php echo $ises ? 'Shi · Envoy / Mensajero' : 'Shi · Envoy'; ?></b><p><?php echo $ises ? 'Guía hacia un canal o región y/o armoniza la fórmula.' : 'Guides to a channel or region and/or harmonizes the formula.'; ?></p></article>
    </section>

    <section class="formula-hierarchy__method">
        <h3><?php echo $ises ? 'Método para responder la pregunta' : 'How to solve the question'; ?></h3>
        <ol>
            <li><?php echo $ises ? 'Identifica el patrón principal y la acción que debe dominar.' : 'Identify the principal pattern and the action that must dominate.'; ?></li>
            <li><?php echo $ises ? 'Busca el ingrediente o la pareja que realiza directamente esa acción: ese es el candidato a Jun.' : 'Find the ingredient or pair that directly performs that action: that is the Jun candidate.'; ?></li>
            <li><?php echo $ises ? 'Clasifica los demás por su trabajo real: reforzar, tratar una rama, corregir o guiar/armonizar.' : 'Classify the remainder by their actual jobs: reinforce, treat a branch, correct, or guide/harmonize.'; ?></li>
            <li><?php echo $ises ? 'Confirma con la fuente. No deduzcas Deputy, Assistant o Envoy solamente por el orden de ingredientes.' : 'Confirm against the source. Never infer Deputy, Assistant, or Envoy from ingredient order alone.'; ?></li>
        </ol>
    </section>

    <form class="formula-hierarchy__tools" method="get" action="<?php echo (new moodle_url('/blocks/myprogress/formula-ingredient-hierarchy.php'))->out(false); ?>">
        <?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
        <label><span><?php echo $ises ? 'Buscar fórmula, ingrediente, patrón o función' : 'Search formula, ingredient, pattern, or role'; ?></span><input type="search" name="q" value="<?php echo s($query); ?>" placeholder="<?php echo $ises ? 'Ej.: Bai Hu Tang, Shi Gao, armoniza…' : 'Example: Bai Hu Tang, Shi Gao, harmonizes…'; ?>"></label>
        <button class="formula-hierarchy__button" type="submit"><?php echo $ises ? 'Buscar' : 'Search'; ?></button>
        <p class="formula-hierarchy__summary"><strong><?php echo count($rows); ?></strong> / 19 <?php echo $ises ? 'perfiles respaldados por la fuente' : 'source-supported profiles'; ?></p>
        <?php if ($query !== '') { ?><a class="formula-hierarchy__button" href="<?php echo (new moodle_url('/blocks/myprogress/formula-ingredient-hierarchy.php', $ises ? ['lang' => 'es'] : []))->out(false); ?>"><?php echo $ises ? 'Limpiar búsqueda' : 'Clear search'; ?></a><?php } ?>
    </form>

    <?php if (!$rows) { ?>
        <p class="formula-hierarchy__empty"><?php echo $ises ? 'No se encontraron perfiles.' : 'No profiles matched the search.'; ?></p>
    <?php } else { ?>
        <section class="formula-hierarchy__grid" aria-label="<?php echo $ises ? 'Perfiles de arquitectura de fórmulas' : 'Formula architecture profiles'; ?>">
            <?php foreach ($rows as $row) { ?>
                <article class="formula-hierarchy__card">
                    <div class="formula-hierarchy__card-head"><div><span class="formula-hierarchy__category"><?php echo s($row['category']); ?></span><h3><a href="<?php echo $row['url']->out(false); ?>"><?php echo s($row['name']); ?></a></h3></div><strong>#<?php echo $row['number']; ?></strong></div>
                    <span class="formula-hierarchy__chief">★ <?php echo $ises ? 'Chief documentado:' : 'Documented Chief:'; ?> <?php echo s($row['chiefs']); ?></span>
                    <dl>
                        <dt><?php echo $ises ? 'Patrón' : 'Pattern'; ?></dt><dd><?php echo s($row['pattern']); ?></dd>
                        <dt><?php echo $ises ? 'Acción' : 'Action'; ?></dt><dd><?php echo s($row['action']); ?></dd>
                        <dt><?php echo $ises ? 'Ingredientes' : 'Ingredients'; ?></dt><dd><?php echo s($row['ingredients']); ?></dd>
                    </dl>
                    <p class="formula-hierarchy__roles-note"><strong><?php echo $ises ? 'Funciones documentadas:' : 'Documented roles:'; ?></strong> <?php echo s($row['roles']); ?></p>
                </article>
            <?php } ?>
        </section>
    <?php } ?>
    <p class="formula-hierarchy__source"><?php echo $ises
        ? 'Fuente: perfiles de fórmulas suministrados al sitio. Solo se muestran las 19 fórmulas que incluyen una descripción explícita de funciones de ingredientes; no se inventan asignaciones faltantes.'
        : 'Source: supplied site formula profiles. Only the 19 formulas with explicit ingredient-role descriptions are shown; missing assignments are not invented.'; ?></p>
</main>
<?php
echo $OUTPUT->footer();
