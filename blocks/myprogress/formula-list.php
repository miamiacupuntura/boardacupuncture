<?php
// Official 2026 Formula roster rendered as independent study cards.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/herbology-library-navigation.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
$sortmode = optional_param('sort', 'az', PARAM_ALPHA);
$sortmode = $sortmode === 'category' ? 'category' : 'az';
$query = trim(optional_param('q', '', PARAM_TEXT));
$formulafocus = trim(optional_param('formula', '', PARAM_TEXT));
$categoryfilter = optional_param('category', '', PARAM_ALPHANUMEXT);
$fromcontext = optional_param('from', '', PARAM_ALPHA);
$fromlibrary = $fromcontext === 'library';
$fromchief = $fromcontext === 'chief';
$fromingredients = $fromcontext === 'ingredients';
$fromactions = $fromcontext === 'actions';
$fromquiz = $fromcontext === 'quiz';

$baseparams = [];
if ($ises) {
    $baseparams['lang'] = 'es';
}
if ($fromlibrary || $fromchief || $fromingredients || $fromactions || $fromquiz) {
    $baseparams['from'] = $fromcontext;
}
$pageurl = new moodle_url('/blocks/myprogress/formula-list.php', $baseparams + [
    'sort' => $sortmode,
    'q' => $query,
    'formula' => $formulafocus,
    'category' => $categoryfilter,
]);
$languageparams = [
    'sort' => $sortmode,
    'q' => $query,
    'formula' => $formulafocus,
    'category' => $categoryfilter,
];
if (!$ises) {
    $languageparams['lang'] = 'es';
}
if ($fromlibrary || $fromchief || $fromingredients || $fromactions || $fromquiz) {
    $languageparams['from'] = $fromcontext;
}
$languageurl = new moodle_url('/blocks/myprogress/formula-list.php', $languageparams);
$studyparams = $ises ? ['lang' => 'es'] : [];
if ($fromlibrary) {
    $studyparams['from'] = 'library';
}
$formulastudyurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $studyparams);
$dashboardurl = new moodle_url('/my/');
$chiefherbsurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'chief-herbs-property-es.html' : 'chief-herbs-property.html'));
$formulaingredientsurl = new moodle_url('/blocks/myprogress/formula-ingredients.php', $ises ? ['lang' => 'es'] : []);
$formulaactionsurl = new moodle_url('/blocks/myprogress/formula-actions.php', $ises ? ['lang' => 'es'] : []);

$rosterpath = __DIR__ . '/formula-board-roster-2026.json';
$detailpath = __DIR__ . '/formulas-study-guide-data.json';
$roster = is_readable($rosterpath) ? json_decode(file_get_contents($rosterpath), true) : null;
$studydata = is_readable($detailpath) ? json_decode(file_get_contents($detailpath), true) : null;
if (!is_array($roster) || !is_array($studydata)
        || (int)($roster['total'] ?? 0) !== 181
        || count($roster['formulas'] ?? []) !== 181
        || count($studydata['formulas'] ?? []) !== 63
        || count($studydata['supplementalFormulas'] ?? []) !== 227) {
    throw new coding_exception('Official Formula guideline data is unavailable.');
}

$normalize = static function(string $value): string {
    $value = core_text::strtolower(trim($value));
    return trim((string)preg_replace('/[^a-z0-9]+/u', ' ', $value));
};
$detailtext = static function(array $profile, string $field, bool $ises): string {
    if ($ises) {
        $spanishkey = $field . 'Es';
        $spanish = trim((string)($profile[$spanishkey] ?? ''));
        if ($spanish !== '') {
            return $spanish;
        }
    }
    return trim((string)($profile[$field] ?? ''));
};

$detailsbyname = [];
foreach (array_merge($studydata['formulas'] ?? [], $studydata['supplementalFormulas'] ?? []) as $profile) {
    $name = trim((string)($profile['name'] ?? ''));
    if ($name !== '') {
        $detailsbyname[$normalize($name)] = $profile;
    }
}

$categories = [];
$cards = [];
$detailedcount = 0;
foreach ($roster['formulas'] as $position => $officialformula) {
    $name = trim((string)($officialformula['name'] ?? ''));
    if ($name === '') {
        continue;
    }
    $detail = $detailsbyname[$normalize($name)] ?? [];
    $category = $officialformula['benskyCategory'] ?? [];
    $categorycode = clean_param((string)($category['code'] ?? 'uncategorized'), PARAM_ALPHANUMEXT);
    if ($categorycode === '') {
        $categorycode = 'uncategorized';
    }
    $categoryname = trim((string)($category[$ises ? 'es' : 'en'] ?? $category['en'] ?? ''));
    if ($categoryname === '') {
        $categoryname = $ises ? 'Sin categoría' : 'Uncategorized';
    }
    $categorychapter = (int)($category['chapter'] ?? 0);
    $categorylabel = ($ises ? 'Capítulo ' : 'Chapter ')
        . ($categorychapter > 0 ? $categorychapter . ' · ' : '')
        . $categoryname;
    $categories[$categorycode] = [
        'chapter' => $categorychapter,
        'title' => $categoryname,
        'label' => $categorylabel,
    ];
    $isdetailed = !empty($detail);
    if ($isdetailed) {
        $detailedcount++;
    }
    $card = [
        'id' => (int)($officialformula['id'] ?? ($position + 1)),
        'name' => $name,
        'english' => trim((string)($officialformula['englishName'] ?? '')),
        'categorycode' => $categorycode,
        'category' => $categoryname,
        'categorychapter' => $categorychapter,
        'categorylabel' => $categorylabel,
        'detailed' => $isdetailed,
        'pattern' => $detailtext($detail, 'pattern', $ises),
        'action' => $detailtext($detail, 'action', $ises),
        'clue' => $detailtext($detail, 'clue', $ises),
        'examfocus' => $detailtext($detail, 'examFocus', $ises),
        'ingredients' => $detailtext($detail, 'ingredients', $ises),
        'roles' => $detailtext($detail, 'ingredientRoles', $ises),
        'comparison' => $detailtext($detail, 'comparison', $ises),
        'safety' => $detailtext($detail, 'safety', $ises),
        'kings' => trim((string)($detail['kings'] ?? '')),
    ];
    $haystack = core_text::strtolower(implode(' ', array_values($card)));
    if ($formulafocus !== '' && $normalize($name) !== $normalize($formulafocus)) {
        continue;
    }
    if ($query !== '' && core_text::strpos($haystack, core_text::strtolower($query)) === false) {
        continue;
    }
    if ($categoryfilter !== '' && $categoryfilter !== $categorycode) {
        continue;
    }
    $cards[] = $card;
}
// Preserve the conventional Bensky book sequence instead of alphabetizing chapters.
uasort($categories, static function(array $left, array $right): int {
    $chaptercompare = ($left['chapter'] ?? 0) <=> ($right['chapter'] ?? 0);
    return $chaptercompare !== 0 ? $chaptercompare : strnatcasecmp($left['title'], $right['title']);
});
usort($cards, static fn(array $left, array $right): int => strnatcasecmp($left['name'], $right['name']));

$groups = [];
foreach ($cards as $card) {
    if ($sortmode === 'category') {
        $groupkey = $card['categorycode'];
        $grouptitle = $card['categorylabel'];
        $grouporder = $card['categorychapter'];
    } else {
        $groupkey = strtoupper(substr($card['name'], 0, 1));
        $grouptitle = $groupkey;
        $grouporder = 0;
    }
    if (!isset($groups[$groupkey])) {
        $groups[$groupkey] = ['title' => $grouptitle, 'order' => $grouporder, 'cards' => []];
    }
    $groups[$groupkey]['cards'][] = $card;
}
uasort($groups, static function(array $left, array $right) use ($sortmode): int {
    if ($sortmode === 'category') {
        $ordercompare = ($left['order'] ?? 0) <=> ($right['order'] ?? 0);
        if ($ordercompare !== 0) {
            return $ordercompare;
        }
    }
    return strnatcasecmp($left['title'], $right['title']);
});
$autoopen = $formulafocus !== '' || ($query !== '' && count($cards) <= 10);

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Formula List');
$PAGE->set_heading('Formula List');
$PAGE->set_cacheable(false);
$PAGE->requires->js_init_code(<<<'JS'
document.querySelectorAll('[data-return-to-formula-question]').forEach(function(button) {
    button.addEventListener('click', function() {
        if (window.history.length > 1) {
            window.history.back();
            return;
        }
        window.location.href = button.getAttribute('data-fallback-url');
    });
});
document.querySelectorAll('[data-formula-list-expand]').forEach(function(button) {
    button.addEventListener('click', function() {
        document.querySelectorAll('.formula-list__card').forEach(function(card) { card.open = true; });
    });
});
document.querySelectorAll('[data-formula-category-link]').forEach(function(link) {
    link.addEventListener('click', function(event) { event.stopPropagation(); });
});
document.querySelectorAll('[data-formula-list-collapse]').forEach(function(button) {
    button.addEventListener('click', function() {
        document.querySelectorAll('.formula-list__card').forEach(function(card) { card.open = false; });
        window.scrollTo({top: 0, behavior: 'smooth'});
    });
});
JS);

echo $OUTPUT->header();
?>
<style>
.formula-list{width:min(1220px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}
.formula-list__top{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:22px;border:1px solid #c8d9df;border-top:5px solid #f47b20;border-radius:14px;background:#fff}
.formula-list__eyebrow{margin:0 0 5px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.formula-list h2{margin:0 0 8px;font-size:clamp(28px,4vw,40px)}
.formula-list__lead{max-width:780px;margin:0;color:#526b84;line-height:1.55}
.formula-list__actions,.formula-list__modes,.formula-list__tools{display:flex;flex-wrap:wrap;gap:9px}
.formula-list__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}
.formula-list__button:hover{background:#eef7f5}.formula-list__button.is-active{border-color:#087c69;background:#087c69;color:#fff!important}
.formula-list__controls{margin-bottom:18px;padding:16px;border:1px solid #c7ddd9;border-radius:12px;background:#f8fcfb}
.formula-list__modes{margin-bottom:13px}
.formula-list__form{display:grid;grid-template-columns:minmax(220px,1.5fr) minmax(220px,1fr) auto;gap:10px;align-items:end}
.formula-list__form label{display:grid;gap:5px;color:#35566f;font-size:12px;font-weight:850}
.formula-list__form input,.formula-list__form select{box-sizing:border-box;min-height:44px;width:100%;padding:8px 11px;border:1px solid #9db8c8;border-radius:8px;background:#fff;color:#102a43;font:inherit}
.formula-list__summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px;margin:13px 0}
.formula-list__stat{padding:11px 13px;border:1px solid #c7ddd9;border-radius:9px;background:#fff}.formula-list__stat span{display:block;color:#087c69;font-size:11px;font-weight:900;text-transform:uppercase}.formula-list__stat strong{display:block;margin-top:4px;font-size:21px}
.formula-list__tools{justify-content:flex-end}
.formula-list__group{scroll-margin-top:18px;margin:0 0 22px}.formula-list__group-header{display:flex;align-items:end;justify-content:space-between;gap:16px;margin:0 0 10px;padding:0 4px 8px;border-bottom:3px solid #f47b20}.formula-list__group-header h3{margin:0;font-size:21px}.formula-list__group-header span{color:#526b84;font-weight:800}
.formula-list__cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;align-items:start}
.formula-list__card{overflow:hidden;border:1px solid #cbd9df;border-top:4px solid #f47b20;border-radius:11px;background:#fff}.formula-list__card[open]{box-shadow:0 6px 18px rgba(23,50,77,.08)}
.formula-list__card summary{position:relative;display:block;min-height:112px;padding:15px 48px 14px 16px;cursor:pointer;list-style:none}.formula-list__card summary::-webkit-details-marker{display:none}.formula-list__card summary::after{content:'+';position:absolute;top:15px;right:15px;display:grid;width:27px;height:27px;place-items:center;border-radius:50%;background:#eaf5f3;color:#087c69;font-size:18px;font-weight:900}.formula-list__card[open] summary::after{content:'−'}
.formula-list__name{display:block;font-size:18px;font-weight:900}.formula-list__english{display:block;margin-top:3px;color:#64748b;font-size:12px;font-style:italic}
.formula-list__category{display:inline-block;margin-top:8px;padding:4px 8px;border-radius:999px;background:#eaf5f3;color:#087c69!important;font-size:11px;font-weight:850;text-decoration:none!important}.formula-list__category:hover{background:#087c69;color:#fff!important}
.formula-list__status{display:block;margin-top:7px;color:#64748b;font-size:11px;font-weight:800}.formula-list__status.is-detailed{color:#087c69}
.formula-list__body{padding:14px 16px 16px;border-top:1px solid #d8e2e7}.formula-list__body dl{display:grid;grid-template-columns:104px minmax(0,1fr);gap:9px 10px;margin:0}.formula-list__body dt{font-size:12px;font-weight:900}.formula-list__body dd{margin:0;color:#52657a;font-size:13px;line-height:1.5}.formula-list__body dd.is-safety{color:#a64b13}.formula-list__clue{color:#075f60!important;font-weight:800}
.formula-list__profile-note{margin:0;padding:11px;border-radius:8px;background:#fff7e8;color:#7a5412;font-size:12px;line-height:1.5}
.formula-list__empty{padding:30px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;color:#526b84;text-align:center;font-weight:750}
@media(max-width:930px){.formula-list__cards{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:700px){.formula-list__top{display:block}.formula-list__actions{margin-top:15px}.formula-list__form,.formula-list__summary,.formula-list__cards{grid-template-columns:1fr}.formula-list__tools{justify-content:flex-start}.formula-list__body dl{grid-template-columns:1fr}.formula-list__body dd{margin-top:-5px}}
</style>
<main class="formula-list">
    <?php if ($fromlibrary) {
        echo block_myprogress_render_herbology_library_navigation(
            $ises,
            $ises ? 'Lista de fórmulas' : 'Formula List',
            'guides'
        );
    } ?>
    <header class="formula-list__top">
        <div>
            <p class="formula-list__eyebrow"><?php echo $ises ? 'GUIDELINE VIGENTE 2026' : 'CURRENT 2026 GUIDELINE'; ?></p>
            <h2>Formula List</h2>
            <p class="formula-list__lead"><?php echo $ises
                ? 'Las 181 fórmulas oficiales del Appendix C, organizadas de la A a la Z o en el orden convencional de los capítulos 1–21 de Bensky. Solo se muestran los capítulos que contienen fórmulas del roster oficial.'
                : 'The 181 official Appendix C formulas, organized A–Z or in the conventional Bensky Chapters 1–21 sequence. Only chapters containing official roster formulas are shown.'; ?></p>
        </div>
        <div class="formula-list__actions">
            <?php if ($fromquiz) { ?>
                <button class="formula-list__button" type="button" data-return-to-formula-question data-fallback-url="<?php echo $formulastudyurl->out(false); ?>"><?php echo $ises ? '← Volver a la pregunta' : '← Back to question'; ?></button>
            <?php } else if ($fromchief) { ?>
                <a class="formula-list__button" href="<?php echo $chiefherbsurl->out(false); ?>"><?php echo $ises ? '← Volver a Chief Herbs' : '← Back to Chief Herbs'; ?></a>
            <?php } else if ($fromingredients) { ?>
                <a class="formula-list__button" href="<?php echo $formulaingredientsurl->out(false); ?>"><?php echo $ises ? '← Volver a Fórmulas e ingredientes' : '← Back to Formula and Ingredients'; ?></a>
            <?php } else if ($fromactions) { ?>
                <a class="formula-list__button" href="<?php echo $formulaactionsurl->out(false); ?>"><?php echo $ises ? '← Volver a Fórmulas y acciones' : '← Back to Formula and Actions'; ?></a>
            <?php } else { ?>
                <a class="formula-list__button" href="<?php echo $formulastudyurl->out(false); ?>"><?php echo $ises ? '← Fórmulas' : '← Formulas'; ?></a>
            <?php } ?>
            <a class="formula-list__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
            <a class="formula-list__button" href="<?php echo $dashboardurl->out(false); ?>">Dashboard</a>
        </div>
    </header>

    <section class="formula-list__controls" aria-label="<?php echo $ises ? 'Controles de la lista' : 'List controls'; ?>">
        <nav class="formula-list__modes" aria-label="<?php echo $ises ? 'Organizar fórmulas' : 'Organize formulas'; ?>">
            <?php
            // Sorting modes reset category filters and start from all 181 formulas.
            // Do not carry a previous search or focused formula into a new sorting mode.
            $azurl = new moodle_url('/blocks/myprogress/formula-list.php', $baseparams + ['sort' => 'az']);
            $categoryurl = new moodle_url('/blocks/myprogress/formula-list.php', $baseparams + ['sort' => 'category']);
            ?>
            <a class="formula-list__button<?php echo $sortmode === 'az' ? ' is-active' : ''; ?>" href="<?php echo $azurl->out(false); ?>"<?php echo $sortmode === 'az' ? ' aria-current="page"' : ''; ?>>A–Z</a>
            <a class="formula-list__button<?php echo $sortmode === 'category' ? ' is-active' : ''; ?>" href="<?php echo $categoryurl->out(false); ?>"<?php echo $sortmode === 'category' ? ' aria-current="page"' : ''; ?>><?php echo $ises ? 'Por capítulo Bensky' : 'By Bensky chapter'; ?></a>
            <?php if ($query !== '' || $categoryfilter !== '' || $formulafocus !== '') {
                $backurl = new moodle_url('/blocks/myprogress/formula-list.php', $baseparams + ['sort' => 'az']);
            ?>
                <a class="formula-list__button" href="<?php echo $backurl->out(false); ?>"><?php echo $ises ? '← Volver a todas las fórmulas' : '← Back to all formulas'; ?></a>
            <?php } ?>
        </nav>

        <form class="formula-list__form" method="get" action="<?php echo (new moodle_url('/blocks/myprogress/formula-list.php'))->out(false); ?>">
            <input type="hidden" name="sort" value="<?php echo s($sortmode); ?>">
            <?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
            <?php if ($fromlibrary || $fromchief || $fromingredients || $fromactions || $fromquiz) { ?><input type="hidden" name="from" value="<?php echo s($fromcontext); ?>"><?php } ?>
            <label><span><?php echo $ises ? 'Buscar fórmula' : 'Search formula'; ?></span><input type="search" name="q" value="<?php echo s($query); ?>" placeholder="<?php echo $ises ? 'Nombre, patrón, acción…' : 'Name, pattern, action…'; ?>"></label>
            <label><span><?php echo $ises ? 'Capítulo Bensky' : 'Bensky chapter'; ?></span><select name="category">
                <option value=""><?php echo $ises ? 'Todos los capítulos con fórmulas oficiales' : 'All chapters with official formulas'; ?></option>
                <?php foreach ($categories as $code => $categoryitem) { ?><option value="<?php echo s($code); ?>"<?php echo $categoryfilter === $code ? ' selected' : ''; ?>><?php echo s($categoryitem['label']); ?></option><?php } ?>
            </select></label>
            <button class="formula-list__button is-active" type="submit"><?php echo $ises ? 'Aplicar' : 'Apply'; ?></button>
        </form>

        <div class="formula-list__summary">
            <div class="formula-list__stat"><span>Guideline</span><strong>181</strong></div>
            <div class="formula-list__stat"><span><?php echo $ises ? 'Mostradas' : 'Displayed'; ?></span><strong><?php echo count($cards); ?></strong></div>
            <div class="formula-list__stat"><span><?php echo $ises ? 'Capítulos Bensky' : 'Bensky chapters'; ?></span><strong><?php echo count($categories); ?></strong></div>
        </div>
        <div class="formula-list__tools">
            <button class="formula-list__button" type="button" data-formula-list-expand><?php echo $ises ? 'Expandir todas' : 'Expand all'; ?></button>
            <button class="formula-list__button" type="button" data-formula-list-collapse><?php echo $ises ? 'Cerrar todas' : 'Collapse all'; ?></button>
        </div>
    </section>

    <?php if (!$cards) { ?>
        <p class="formula-list__empty"><?php echo $ises ? 'Ninguna fórmula del guideline coincide con estos filtros.' : 'No guideline formula matches these filters.'; ?></p>
    <?php } else { ?>
        <?php foreach ($groups as $groupkey => $group) { ?>
            <section class="formula-list__group" id="formula-group-<?php echo s($groupkey); ?>">
                <header class="formula-list__group-header"><h3><?php echo s($group['title']); ?></h3><?php $groupcount = count($group['cards']); ?><span><?php echo $groupcount; ?> <?php echo $ises ? ($groupcount === 1 ? 'fórmula' : 'fórmulas') : ($groupcount === 1 ? 'formula' : 'formulas'); ?></span></header>
                <div class="formula-list__cards">
                    <?php foreach ($group['cards'] as $card) {
                        $cardcategoryurl = new moodle_url('/blocks/myprogress/formula-list.php', $baseparams + [
                            'sort' => 'category',
                            'category' => $card['categorycode'],
                        ]);
                    ?>
                        <details class="formula-list__card"<?php echo $autoopen ? ' open' : ''; ?>>
                            <summary>
                                <span class="formula-list__name"><?php echo s($card['name']); ?>*</span>
                                <?php if ($card['english'] !== '') { ?><span class="formula-list__english"><?php echo s($card['english']); ?></span><?php } ?>
                                <a class="formula-list__category" data-formula-category-link href="<?php echo $cardcategoryurl->out(false); ?>"><?php echo s($card['categorylabel']); ?></a>
                                <span class="formula-list__status<?php echo $card['detailed'] ? ' is-detailed' : ''; ?>"><?php echo $card['detailed']
                                    ? ($ises ? 'Perfil clínico detallado' : 'Detailed clinical profile')
                                    : ($ises ? 'Roster oficial · perfil pendiente' : 'Official roster · profile pending'); ?></span>
                            </summary>
                            <div class="formula-list__body">
                                <?php if ($card['detailed']) { ?><dl>
                                    <?php if ($card['pattern'] !== '') { ?><dt><?php echo $ises ? 'Patrón' : 'Pattern'; ?></dt><dd><?php echo s($card['pattern']); ?></dd><?php } ?>
                                    <?php if ($card['action'] !== '') { ?><dt><?php echo $ises ? 'Acción' : 'Action'; ?></dt><dd><?php echo s($card['action']); ?></dd><?php } ?>
                                    <?php if ($card['clue'] !== '') { ?><dt><?php echo $ises ? 'Pista decisiva' : 'Decisive clue'; ?></dt><dd class="formula-list__clue"><?php echo s($card['clue']); ?></dd><?php } ?>
                                    <?php if ($card['examfocus'] !== '') { ?><dt><?php echo $ises ? 'Enfoque Board' : 'Board focus'; ?></dt><dd><?php echo s($card['examfocus']); ?></dd><?php } ?>
                                    <?php if ($card['kings'] !== '') { ?><dt><?php echo $ises ? 'Hierba principal' : 'Chief herb'; ?></dt><dd><?php echo s($card['kings']); ?></dd><?php } ?>
                                    <?php if ($card['ingredients'] !== '') { ?><dt><?php echo $ises ? 'Ingredientes' : 'Ingredients'; ?></dt><dd><?php echo s($card['ingredients']); ?></dd><?php } ?>
                                    <?php if ($card['roles'] !== '') { ?><dt><?php echo $ises ? 'Arquitectura' : 'Architecture'; ?></dt><dd><?php echo s($card['roles']); ?></dd><?php } ?>
                                    <?php if ($card['comparison'] !== '') { ?><dt><?php echo $ises ? 'Contraste' : 'Contrast'; ?></dt><dd><?php echo s($card['comparison']); ?></dd><?php } ?>
                                    <?php if ($card['safety'] !== '') { ?><dt><?php echo $ises ? 'Seguridad' : 'Safety'; ?></dt><dd class="is-safety"><?php echo s($card['safety']); ?></dd><?php } ?>
                                </dl><?php } else { ?>
                                    <p class="formula-list__profile-note"><?php echo $ises
                                        ? 'Esta fórmula pertenece al Appendix C oficial de 2026. El banco suministrado todavía no contiene un perfil clínico detallado verificado; por eso no se completan campos por inferencia.'
                                        : 'This formula belongs to the official 2026 Appendix C roster. The supplied bank does not yet contain a verified detailed clinical profile, so no fields are completed by inference.'; ?></p>
                                <?php } ?>
                            </div>
                        </details>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>
    <?php } ?>
</main>
<?php
echo $OUTPUT->footer();
