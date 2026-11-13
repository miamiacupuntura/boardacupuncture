<?php
// Official guideline formulas with linked ingredients and highlighted Chief herbs.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/herbology-library-navigation.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
$query = trim(optional_param('q', '', PARAM_TEXT));

$pageparams = $ises ? ['lang' => 'es'] : [];
if ($query !== '') {
    $pageparams['q'] = $query;
}
$pageurl = new moodle_url('/blocks/myprogress/formula-ingredients.php', $pageparams);
$languageparams = $query !== '' ? ['q' => $query] : [];
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/formula-ingredients.php', $languageparams);
$libraryparams = ['area' => 'herbology', 'view' => 'library'];
if ($ises) {
    $libraryparams['lang'] = 'es';
}
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', $libraryparams);

$formuladatafile = __DIR__ . '/formulas-study-guide-data.json';
$formularosterfile = __DIR__ . '/formula-board-roster-2026.json';
$herbrosterfile = __DIR__ . '/single-herb-card-index.json';
$formuladata = is_readable($formuladatafile) ? json_decode(file_get_contents($formuladatafile), true) : null;
$formularoster = is_readable($formularosterfile) ? json_decode(file_get_contents($formularosterfile), true) : null;
$herbroster = is_readable($herbrosterfile) ? json_decode(file_get_contents($herbrosterfile), true) : null;
if (!is_array($formuladata) || !is_array($formularoster) || !is_array($herbroster)
        || count($formuladata['formulas'] ?? []) !== 63
        || (int)($formularoster['total'] ?? 0) !== 181
        || count($formularoster['formulas'] ?? []) !== 181
        || (int)($herbroster['total'] ?? 0) !== 304
        || count($herbroster['herbs'] ?? []) !== 304) {
    throw new coding_exception('Formula and ingredient guideline data is unavailable.');
}

$normalize = static function(string $value): string {
    $value = core_text::strtolower(trim($value));
    return trim((string)preg_replace('/[^a-z0-9]+/u', ' ', $value));
};

$officialformulas = [];
foreach ($formularoster['formulas'] as $formula) {
    $name = trim((string)($formula['name'] ?? ''));
    if ($name !== '') {
        $officialformulas[$normalize($name)] = $name;
    }
}
$formulaaliases = [
    'jin gui shen qi wan' => 'shen qi wan',
];

$herbaliases = [];
foreach ($herbroster['herbs'] as $herb) {
    $name = trim((string)($herb['name'] ?? ''));
    if ($name !== '') {
        $herbaliases[$name] = $name;
    }
}
$manualaliases = [
    'Chuan Jiao' => 'Hua Jiao',
    'Fu Ling Pi' => 'Fu Ling',
    'Ju Pi' => 'Chen Pi',
    'Jing Jie Sui' => 'Jing Jie',
    'Sheng Di' => 'Sheng Di Huang',
    'Sheng Gan Cao' => 'Gan Cao',
    'Sheng Jiang Pi' => 'Sheng Jiang',
    'Shu Di' => 'Shu Di Huang',
    'Su Ye' => 'Zi Su Ye',
    'Su Zi' => 'Zi Su Zi',
    'Zhi Cao Wu' => 'Cao Wu',
    'Zhi Chuan Wu' => 'Chuan Wu',
    'Zhi Fu Zi' => 'Fu Zi',
    'Zhi Gan Cao' => 'Gan Cao',
];
foreach ($manualaliases as $alias => $target) {
    $herbaliases[$alias] = $target;
}
uksort($herbaliases, static function(string $left, string $right): int {
    return strlen($right) <=> strlen($left);
});
$aliaslookup = [];
foreach ($herbaliases as $alias => $target) {
    $aliaslookup[core_text::strtolower($alias)] = ['label' => $alias, 'target' => $target];
}
$aliaspattern = '/(?<![A-Za-z])(' . implode('|', array_map(
    static fn(string $alias): string => preg_quote($alias, '/'),
    array_keys($herbaliases)
)) . ')(?![A-Za-z])/iu';

$renderingredients = static function(
    string $ingredients,
    string $chiefs,
    bool $ises,
    string $query
) use ($aliaspattern, $aliaslookup, $herbaliases, $normalize): string {
    $chieftargets = [];
    $chiefaliases = [];
    foreach ($herbaliases as $alias => $target) {
        if (preg_match('/(?<![A-Za-z])' . preg_quote($alias, '/') . '(?![A-Za-z])/iu', $chiefs)) {
            $chiefaliases[$normalize($alias)] = true;
            $chieftargets[$normalize($target)] = true;
        }
    }
    $parts = preg_split($aliaspattern, $ingredients, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($parts)) {
        return s($ingredients);
    }
    $output = '';
    foreach ($parts as $part) {
        $lookup = $aliaslookup[core_text::strtolower($part)] ?? null;
        if (!$lookup) {
            $output .= s($part);
            continue;
        }
        $target = $lookup['target'];
        $partkey = $normalize($part);
        $targetkey = $normalize($target);
        $querykey = $normalize($query);
        $ischief = isset($chiefaliases[$partkey]) || isset($chieftargets[$targetkey]);
        $issearchmatch = $querykey !== ''
            && (core_text::strpos($partkey, $querykey) !== false
                || core_text::strpos($targetkey, $querykey) !== false);
        $params = ['herb' => $target, 'sort' => 'az', 'from' => 'ingredients'];
        if ($ises) {
            $params['lang'] = 'es';
        }
        $url = new moodle_url('/blocks/myprogress/single-herbs-list.php', $params);
        $classes = 'formula-ingredients__herb-link'
            . ($ischief ? ' is-chief' : '')
            . ($issearchmatch ? ' is-search-match' : '');
        $output .= html_writer::link(
            $url,
            ($ischief ? '★ ' : '') . $part,
            [
                'class' => $classes,
                'title' => $ischief
                    ? ($ises ? 'Ingrediente Chief; no necesariamente es el término buscado · Abrir tarjeta' : 'Chief ingredient; not necessarily the searched term · Open card')
                    : ($ises ? 'Abrir tarjeta de hierba' : 'Open herb card'),
            ]
        );
    }
    return $output;
};

$rows = [];
foreach ($formuladata['formulas'] as $profile) {
    $sourcename = trim((string)($profile['name'] ?? ''));
    $sourcekey = $normalize($sourcename);
    $officialkey = $formulaaliases[$sourcekey] ?? $sourcekey;
    if (!isset($officialformulas[$officialkey])) {
        continue;
    }
    $officialname = $officialformulas[$officialkey];
    $ingredients = trim((string)($profile[$ises ? 'ingredientsEs' : 'ingredients'] ?? $profile['ingredients'] ?? ''));
    $chiefs = trim((string)($profile['kings'] ?? ''));
    if ($ingredients === '' || $chiefs === '') {
        continue;
    }
    $searchtext = core_text::strtolower(implode(' ', [
        $officialname,
        $sourcename,
        $ingredients,
        $chiefs,
        (string)($profile[$ises ? 'categoryEs' : 'category'] ?? ''),
    ]));
    if ($query !== '' && core_text::strpos($searchtext, core_text::strtolower($query)) === false) {
        continue;
    }
    $formulaparams = ['formula' => $officialname, 'sort' => 'az', 'from' => 'ingredients'];
    if ($ises) {
        $formulaparams['lang'] = 'es';
    }
    $querykey = $normalize($query);
    $matchtype = '';
    if ($querykey !== '') {
        if (core_text::strpos($normalize($officialname . ' ' . $sourcename), $querykey) !== false) {
            $matchtype = 'formula';
        } else if (core_text::strpos($normalize($chiefs), $querykey) !== false) {
            $matchtype = 'chief';
        } else {
            $matchtype = 'ingredient';
        }
    }
    $rows[] = [
        'number' => (int)($profile['number'] ?? 0),
        'name' => $officialname,
        'sourceName' => $sourcename,
        'formulaUrl' => new moodle_url('/blocks/myprogress/formula-list.php', $formulaparams),
        'ingredients' => $renderingredients($ingredients, $chiefs, $ises, $query),
        'chiefs' => $chiefs,
        'matchtype' => $matchtype,
        'category' => trim((string)($profile[$ises ? 'categoryEs' : 'category'] ?? '')),
    ];
}
usort($rows, static fn(array $left, array $right): int => $left['number'] <=> $right['number']);
if ($query === '' && count($rows) !== 63) {
    throw new coding_exception('Formula and Ingredients must contain exactly 63 official guideline formulas.');
}

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Fórmulas e ingredientes' : 'Formula and Ingredients');
$PAGE->set_heading($ises ? 'Fórmulas e ingredientes' : 'Formula and Ingredients');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
?>
<style>
.formula-ingredients{width:min(1260px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}
.formula-ingredients__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:24px;border:1px solid #bfd7d4;border-top:5px solid #087c69;border-radius:14px;background:#fff}
.formula-ingredients__eyebrow{margin:0 0 6px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.formula-ingredients h2{margin:0 0 8px;font-size:clamp(29px,4vw,42px)}
.formula-ingredients__lead{max-width:810px;margin:0;color:#526b84;line-height:1.55}
.formula-ingredients__actions{display:flex;flex-wrap:wrap;gap:9px}
.formula-ingredients__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}
.formula-ingredients__button:hover{background:#eef7f5}
.formula-ingredients__tools{display:grid;grid-template-columns:minmax(240px,1fr) auto;gap:12px;align-items:end;margin-bottom:18px;padding:16px;border:1px solid #c7ddd9;border-radius:12px;background:#f8fcfb}
.formula-ingredients__tools label{display:grid;gap:6px;font-size:12px;font-weight:850}.formula-ingredients__tools input{width:100%;min-height:46px;padding:9px 12px;border:1px solid #aebfca;border-radius:8px;font:inherit}
.formula-ingredients__summary{margin:0;color:#36566c;font-weight:850}
.formula-ingredients__legend{margin:0 0 10px;padding:13px 15px;border-left:5px solid #e9a11b;border-radius:8px;background:#fff7df;color:#5a4310}.formula-ingredients__search-legend{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin:0 0 16px;padding:12px 15px;border-left:5px solid #6d28d9;border-radius:8px;background:#f4efff;color:#4c1d95}.formula-ingredients__search-chip{display:inline-flex;padding:4px 9px;border:3px solid #6d28d9;border-radius:999px;background:#6d28d9;color:#fff;font-weight:900}
.formula-ingredients__table-wrap{overflow-x:auto;border:1px solid #bfd0d9;border-radius:12px;background:#fff;box-shadow:0 5px 18px rgba(23,50,77,.06)}
.formula-ingredients__table{width:100%;min-width:960px;border-collapse:collapse}.formula-ingredients__table th,.formula-ingredients__table td{padding:11px 13px;border-bottom:1px solid #d5e1e7;vertical-align:top;text-align:left;line-height:1.55}.formula-ingredients__table thead th{background:#0d7868;color:#fff;font-size:.76rem;letter-spacing:.05em;text-transform:uppercase}.formula-ingredients__table tbody tr:nth-child(even){background:#f8fbfc}.formula-ingredients__formula-link{color:#0f4c81!important;font-weight:900;text-decoration:none}.formula-ingredients__formula-link:hover{text-decoration:underline}.formula-ingredients__alias{display:block;margin-top:3px;color:#657c8d;font-size:11px}
.formula-ingredients__herb-link{display:inline-flex;align-items:center;margin:2px 1px;padding:3px 7px;border:1px solid #b7ccd7;border-radius:999px;background:#f4f8fa;color:#0f4c81!important;font-weight:800;text-decoration:none!important}.formula-ingredients__herb-link:hover{background:#e5f3ef}.formula-ingredients__herb-link.is-chief{border:2px solid #d58a00;background:#fff0b8;color:#6f4300!important;box-shadow:0 0 0 2px rgba(255,193,7,.18);font-weight:950}.formula-ingredients__herb-link.is-search-match{border:2px solid #6d28d9!important;background:#6d28d9!important;color:#fff!important;font-weight:950}.formula-ingredients__herb-link.is-chief.is-search-match{box-shadow:0 0 0 3px #e9a11b}
.formula-ingredients__category{display:block;margin-top:4px;color:#526b84;font-size:12px;font-weight:750}.formula-ingredients__match{display:inline-flex;margin-top:6px;padding:3px 7px;border-radius:999px;background:#eaf3ff;color:#0b4f87;font-size:11px;font-weight:900;text-transform:uppercase}.formula-ingredients__match.is-chief{background:#fff0b8;color:#6f4300}.formula-ingredients__match.is-ingredient{background:#edf6ff;color:#155b91}.formula-ingredients__match.is-formula{background:#e8f7f1;color:#075f50}.formula-ingredients__empty{padding:30px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;text-align:center;font-weight:800}
@media(max-width:760px){.formula-ingredients__hero{display:block}.formula-ingredients__actions{margin-top:16px}.formula-ingredients__tools{grid-template-columns:1fr}.formula-ingredients__table{min-width:0}.formula-ingredients__table thead{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}.formula-ingredients__table,.formula-ingredients__table tbody,.formula-ingredients__table tr,.formula-ingredients__table th,.formula-ingredients__table td{display:block;width:100%}.formula-ingredients__table tr{padding:12px;border-bottom:5px solid #e5eef2}.formula-ingredients__table th,.formula-ingredients__table td{display:grid;grid-template-columns:105px 1fr;gap:10px;border:0;background:#fff!important}.formula-ingredients__table th::before,.formula-ingredients__table td::before{content:attr(data-label);color:#087c69;font-size:.7rem;font-weight:900;text-transform:uppercase}}
</style>
<main class="formula-ingredients">
    <header class="formula-ingredients__hero">
        <div>
            <p class="formula-ingredients__eyebrow"><?php echo $ises ? 'GUIDELINE 2026 · 63 FÓRMULAS OFICIALES' : '2026 GUIDELINE · 63 OFFICIAL FORMULAS'; ?></p>
            <h2><?php echo $ises ? 'Fórmulas e ingredientes' : 'Formula and Ingredients'; ?></h2>
            <p class="formula-ingredients__lead"><?php echo $ises
                ? 'Relaciona cada fórmula oficial con sus ingredientes. El ingrediente Chief aparece resaltado y todos los nombres abren su tarjeta exacta.'
                : 'Connect every official formula with its ingredients. The Chief ingredient is highlighted, and every name opens its exact study card.'; ?></p>
        </div>
        <div class="formula-ingredients__actions">
            <a class="formula-ingredients__button" href="<?php echo $libraryurl->out(false); ?>"><?php echo $ises ? '← Guía de estudio' : '← Study Guide'; ?></a>
            <a class="formula-ingredients__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
        </div>
    </header>

    <form class="formula-ingredients__tools" method="get" action="<?php echo (new moodle_url('/blocks/myprogress/formula-ingredients.php'))->out(false); ?>">
        <?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
        <label><span><?php echo $ises ? 'Buscar fórmula o ingrediente' : 'Search formula or ingredient'; ?></span><input type="search" name="q" value="<?php echo s($query); ?>" placeholder="<?php echo $ises ? 'Ej.: Ma Huang Tang, Gui Zhi…' : 'Example: Ma Huang Tang, Gui Zhi…'; ?>"></label>
        <button class="formula-ingredients__button" type="submit"><?php echo $ises ? 'Buscar' : 'Search'; ?></button>
        <p class="formula-ingredients__summary"><strong><?php echo count($rows); ?></strong> / 63 <?php echo $ises ? 'fórmulas oficiales visibles' : 'official formulas visible'; ?></p>
        <?php if ($query !== '') { ?><a class="formula-ingredients__button" href="<?php echo (new moodle_url('/blocks/myprogress/formula-ingredients.php', $ises ? ['lang' => 'es'] : []))->out(false); ?>"><?php echo $ises ? 'Limpiar búsqueda' : 'Clear search'; ?></a><?php } ?>
    </form>

    <p class="formula-ingredients__legend"><strong>★ Chief:</strong> <?php echo $ises
        ? 'el ingrediente o grupo de ingredientes principales aparece en amarillo.'
        : 'the principal ingredient or principal group is highlighted in yellow.'; ?> <?php echo $ises ? 'Fuente suministrada: CA Formulas, Chart 4.' : 'Supplied source: CA Formulas, Chart 4.'; ?></p>
    <?php if ($query !== '') { ?>
        <p class="formula-ingredients__search-legend">
            <strong class="formula-ingredients__search-chip"><?php echo $ises ? 'Morado = término buscado' : 'Purple = searched term'; ?>: <?php echo s($query); ?></strong>
            <span><?php echo $ises
                ? 'El morado identifica la palabra buscada. El amarillo con ★ continúa mostrando los demás Chief de la fórmula. Si coinciden, la hierba morada conserva la estrella y recibe un borde dorado.'
                : 'Purple identifies the searched term. Yellow with ★ continues to show the other Chief ingredients. When both meanings coincide, the purple herb keeps its star and receives a gold outline.'; ?></span>
        </p>
    <?php } ?>

    <?php if (!$rows) { ?>
        <p class="formula-ingredients__empty"><?php echo $ises ? 'No se encontraron fórmulas.' : 'No formulas matched the search.'; ?></p>
    <?php } else { ?>
        <div class="formula-ingredients__table-wrap">
            <table class="formula-ingredients__table">
                <caption class="sr-only"><?php echo $ises ? 'Fórmulas oficiales, ingredientes y hierbas Chief' : 'Official formulas, ingredients, and Chief herbs'; ?></caption>
                <thead><tr><th>#</th><th><?php echo $ises ? 'Fórmula' : 'Formula'; ?></th><th><?php echo $ises ? 'Ingredientes · Chief resaltado' : 'Ingredients · Chief highlighted'; ?></th></tr></thead>
                <tbody>
                <?php foreach ($rows as $row) { ?>
                    <tr data-formula-ingredient-row data-formula="<?php echo s($row['name']); ?>" data-chief="<?php echo s($row['chiefs']); ?>">
                        <td data-label="#"><?php echo $row['number']; ?></td>
                        <th scope="row" data-label="<?php echo $ises ? 'Fórmula' : 'Formula'; ?>">
                            <a class="formula-ingredients__formula-link" href="<?php echo $row['formulaUrl']->out(false); ?>"><?php echo s($row['name']); ?></a>
                            <?php if ($row['sourceName'] !== $row['name']) { ?><small class="formula-ingredients__alias"><?php echo s($row['sourceName']); ?></small><?php } ?>
                            <small class="formula-ingredients__category"><?php echo s($row['category']); ?></small>
                            <?php if ($row['matchtype'] !== '') {
                                $matchlabels = [
                                    'formula' => $ises ? 'Coincidencia de fórmula' : 'Formula match',
                                    'chief' => $ises ? 'Coincidencia Chief' : 'Chief match',
                                    'ingredient' => $ises ? 'Coincidencia de ingrediente' : 'Ingredient match',
                                ];
                            ?>
                                <small class="formula-ingredients__match is-<?php echo s($row['matchtype']); ?>"><?php echo s($matchlabels[$row['matchtype']]); ?></small>
                            <?php } ?>
                        </th>
                        <td data-label="<?php echo $ises ? 'Ingredientes' : 'Ingredients'; ?>"><?php echo $row['ingredients']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</main>
<?php
echo $OUTPUT->footer();
