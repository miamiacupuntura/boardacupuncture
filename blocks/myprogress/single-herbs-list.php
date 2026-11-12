<?php
// Official 2026 Single Herbs roster rendered as individual study cards.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/herbology-library-navigation.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
$sortmode = optional_param('sort', 'az', PARAM_ALPHA);
$sortmode = $sortmode === 'category' ? 'category' : 'az';
$query = trim(optional_param('q', '', PARAM_TEXT));
$herbfocus = trim(optional_param('herb', '', PARAM_TEXT));
$categoryfilter = optional_param('category', '', PARAM_ALPHANUMEXT);
$fromcontext = optional_param('from', '', PARAM_ALPHA);
$fromlibrary = $fromcontext === 'library';
$fromquiz = $fromcontext === 'quiz';
$fromchief = $fromcontext === 'chief';

$baseparams = [];
if ($ises) {
    $baseparams['lang'] = 'es';
}
if ($fromlibrary || $fromquiz || $fromchief) {
    $baseparams['from'] = $fromcontext;
}
$pageparams = $baseparams + [
    'sort' => $sortmode,
    'q' => $query,
    'herb' => $herbfocus,
    'category' => $categoryfilter,
];
$pageurl = new moodle_url('/blocks/myprogress/single-herbs-list.php', $pageparams);
$languageparams = [
    'sort' => $sortmode,
    'q' => $query,
    'herb' => $herbfocus,
    'category' => $categoryfilter,
];
if (!$ises) {
    $languageparams['lang'] = 'es';
}
if ($fromlibrary || $fromquiz || $fromchief) {
    $languageparams['from'] = $fromcontext;
}
$languageurl = new moodle_url('/blocks/myprogress/single-herbs-list.php', $languageparams);
$studyguideparams = $ises ? ['lang' => 'es'] : [];
if ($fromlibrary) {
    $studyguideparams['from'] = 'library';
}
$studyguideurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $studyguideparams);
$dashboardurl = new moodle_url('/my/');
$chiefherbsurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'chief-herbs-property-es.html' : 'chief-herbs-property.html'));

$indexpath = __DIR__ . '/single-herb-card-index.json';
$propertiespath = __DIR__ . '/single-herb-properties.json';
$richprofilespath = __DIR__ . '/single-herb-rich-profiles.json';
$actionspath = __DIR__ . '/single-herb-actions-indications-supplement.json';
$safetypath = __DIR__ . '/single-herb-safety-supplement.json';
$cardindex = is_readable($indexpath) ? json_decode(file_get_contents($indexpath), true) : null;
$properties = is_readable($propertiespath) ? json_decode(file_get_contents($propertiespath), true) : null;
$richprofiledata = is_readable($richprofilespath) ? json_decode(file_get_contents($richprofilespath), true) : null;
$actionsdata = is_readable($actionspath) ? json_decode(file_get_contents($actionspath), true) : null;
$safetydata = is_readable($safetypath) ? json_decode(file_get_contents($safetypath), true) : null;
if (!is_array($cardindex) || !is_array($properties) || !is_array($richprofiledata)
        || !is_array($actionsdata) || !is_array($safetydata)
        || (int)($cardindex['total'] ?? 0) !== 304
        || count($cardindex['herbs'] ?? []) !== 304
        || (int)($properties['total'] ?? 0) !== 304
        || (int)($richprofiledata['total'] ?? 0) < 180
        || count($actionsdata['herbs'] ?? []) < 100
        || count($safetydata['categoryCautions'] ?? []) < 30) {
    throw new coding_exception('Official Single Herbs guideline data is unavailable.');
}

$normalize = static function(string $value): string {
    $value = core_text::strtolower(trim($value));
    return trim((string)preg_replace('/[^a-z0-9]+/u', ' ', $value));
};
$localized = static function($field, bool $ises): string {
    if (is_string($field)) {
        return trim($field);
    }
    if (!is_array($field)) {
        return '';
    }
    return trim((string)($field[$ises ? 'es' : 'en'] ?? $field['en'] ?? $field['es'] ?? ''));
};

$propertiesbyname = [];
foreach ($properties['herbs'] ?? [] as $profile) {
    $name = (string)($profile['name'] ?? '');
    if ($name !== '') {
        $propertiesbyname[$normalize($name)] = $profile;
    }
}

$actionsbyname = [];
foreach ($actionsdata['herbs'] ?? [] as $name => $actionprofile) {
    $actionsbyname[$normalize((string)$name)] = $actionprofile;
}
$safetybyname = [];
foreach ($safetydata['herbs'] ?? [] as $name => $safetyprofile) {
    $safetybyname[$normalize((string)$name)] = $safetyprofile;
}
$formatpairfield = static function($profile, bool $ises, int $field): string {
    if (!is_array($profile)) {
        return '';
    }
    $pairs = $profile[$ises ? 'es' : 'en'] ?? $profile['en'] ?? [];
    if (!is_array($pairs)) {
        return '';
    }
    $values = [];
    foreach ($pairs as $pair) {
        $value = is_array($pair) ? trim((string)($pair[$field] ?? '')) : '';
        if ($value !== '') {
            $values[] = rtrim($value, ". ");
        }
    }
    return $values ? implode('; ', array_values(array_unique($values))) . '.' : '';
};
$firstnonempty = static function(...$values): string {
    foreach ($values as $value) {
        $value = trim((string)$value);
        if ($value !== '') {
            return $value;
        }
    }
    return '';
};

// Load the independently generated enrichment bank. No rendered category PHP
// page is included or evaluated at runtime.
$richprofiles = [];
foreach ($richprofiledata['profiles'] ?? [] as $richprofile) {
    $richname = trim((string)($richprofile['name'] ?? ''));
    if ($richname === '') {
        continue;
    }
    $richprofiles[$normalize($richname)] = [
        'properties' => $localized($richprofile['properties'] ?? '', $ises),
        'channels' => $localized($richprofile['channels'] ?? '', $ises),
        'dose' => $localized($richprofile['dose'] ?? '', $ises),
        'identity' => $localized($richprofile['identity'] ?? '', $ises),
        'clue' => $localized($richprofile['clue'] ?? '', $ises),
        'memory' => $localized($richprofile['memory'] ?? '', $ises),
        'unique' => $localized($richprofile['unique'] ?? '', $ises),
        'caution' => $localized($richprofile['caution'] ?? '', $ises),
    ];
}

$categories = [];
$cards = [];
foreach ($cardindex['herbs'] as $position => $officialherb) {
    $name = trim((string)($officialherb['name'] ?? ''));
    if ($name === '') {
        continue;
    }
    $profilekey = $normalize($name);
    $profile = $propertiesbyname[$profilekey] ?? [];
    $richprofile = $richprofiles[$profilekey] ?? [];
    $category = $officialherb['benskyCategory'] ?? [];
    $categorycode = clean_param((string)($category['code'] ?? 'uncategorized'), PARAM_ALPHANUMEXT);
    if ($categorycode === '') {
        $categorycode = 'uncategorized';
    }
    $categoryname = trim((string)($category[$ises ? 'es' : 'en'] ?? $category['en'] ?? ''));
    if ($categoryname === '') {
        $categoryname = $ises ? 'Sin categoría' : 'Uncategorized';
    }
    $internalguidenumber = (int)($officialherb['guideNumber'] ?? 0);
    if ($internalguidenumber >= 12 && $internalguidenumber <= 21) {
        $categoryorder = $internalguidenumber + 1;
    } else if ($internalguidenumber > 0) {
        $categoryorder = $internalguidenumber;
    } else {
        $specialfocus = (string)($officialherb['specialFocus'] ?? '');
        $categoryorder = [
            'bensky4' => 12,
            'bensky17' => 32,
            'bensky18' => 33,
        ][$specialfocus] ?? 99;
    }
    $categorylabel = $categoryorder . ' · ' . $categoryname;
    $categories[$categorycode] = [
        'order' => $categoryorder,
        'title' => $categoryname,
        'label' => $categorylabel,
    ];
    $latin = trim((string)($officialherb['latin'] ?? $profile['latin'] ?? ''));
    $actionprofile = $actionsbyname[$profilekey] ?? [];
    $safetyprofile = $safetybyname[$profilekey] ?? [];
    $supplementactions = $formatpairfield($actionprofile, $ises, 0);
    $supplementindications = $formatpairfield($actionprofile, $ises, 1);
    $categorycaution = $localized($safetydata['categoryCautions'][$categorycode] ?? '', $ises);
    $categorypregnancy = $localized($safetydata['categoryPregnancy'][$categorycode] ?? '', $ises);
    $categoryinteractions = $localized($safetydata['categoryInteractions'][$categorycode] ?? '', $ises);
    $card = [
        'id' => (int)($officialherb['id'] ?? ($position + 1)),
        'name' => $name,
        'latin' => $latin,
        'categorycode' => $categorycode,
        'category' => $categoryname,
        'categoryorder' => $categoryorder,
        'categorylabel' => $categorylabel,
        'guide' => (int)($officialherb['guideNumber'] ?? $profile['guideNumber'] ?? 0),
        'flavor' => $localized($profile['flavor'] ?? '', $ises),
        'temperature' => $localized($profile['temperature'] ?? '', $ises),
        'channels' => trim((string)($richprofile['channels'] ?? $localized($profile['channels'] ?? '', $ises))),
        'properties' => trim((string)($richprofile['properties'] ?? '')),
        'dose' => trim((string)($richprofile['dose'] ?? $profile['dose'] ?? $profile['appendixDose'] ?? '')),
        'identity' => $firstnonempty(
            $richprofile['identity'] ?? '',
            $localized($profile['actionSummary'] ?? '', $ises),
            $supplementactions
        ),
        'clue' => $firstnonempty(
            $richprofile['clue'] ?? '',
            $localized($profile['indicationSummary'] ?? '', $ises),
            $supplementindications
        ),
        'memory' => trim((string)($richprofile['memory'] ?? '')),
        'unique' => trim((string)($richprofile['unique'] ?? '')),
        'safety' => $firstnonempty(
            $richprofile['caution'] ?? '',
            $localized($profile['safetyNote'] ?? '', $ises),
            $localized($safetyprofile['caution'] ?? '', $ises),
            $categorycaution
        ),
        'preparation' => $localized($safetyprofile['preparation'] ?? '', $ises),
        'pregnancy' => $firstnonempty(
            $localized($safetyprofile['pregnancy'] ?? '', $ises),
            $categorypregnancy
        ),
        'interactions' => $firstnonempty(
            $localized($safetyprofile['interactions'] ?? '', $ises),
            $categoryinteractions
        ),
        'status' => (string)($profile['status'] ?? 'properties-only'),
    ];
    $haystack = core_text::strtolower(implode(' ', [
        $card['name'],
        $card['latin'],
        $card['category'],
        $card['flavor'],
        $card['temperature'],
        $card['channels'],
        $card['identity'],
        $card['clue'],
        $card['memory'],
        $card['unique'],
        $card['safety'],
        $card['preparation'],
        $card['pregnancy'],
        $card['interactions'],
    ]));
    if ($herbfocus !== '' && $normalize($name) !== $normalize($herbfocus)) {
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
// Preserve the conventional Bensky category sequence instead of alphabetizing it.
uasort($categories, static function(array $left, array $right): int {
    $ordercompare = ($left['order'] ?? 99) <=> ($right['order'] ?? 99);
    return $ordercompare !== 0 ? $ordercompare : strnatcasecmp($left['title'], $right['title']);
});
usort($cards, static fn(array $left, array $right): int => strnatcasecmp($left['name'], $right['name']));

$groups = [];
foreach ($cards as $card) {
    if ($sortmode === 'category') {
        $groupkey = $card['categorycode'];
        $grouptitle = $card['categorylabel'];
        $grouporder = $card['categoryorder'];
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
        $ordercompare = ($left['order'] ?? 99) <=> ($right['order'] ?? 99);
        if ($ordercompare !== 0) {
            return $ordercompare;
        }
    }
    return strnatcasecmp($left['title'], $right['title']);
});
$autoopen = $herbfocus !== '' || ($query !== '' && count($cards) <= 12);

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Lista de hierbas individuales' : 'Single Herbs List');
$PAGE->set_heading($ises ? 'Lista de hierbas individuales' : 'Single Herbs List');
$PAGE->set_cacheable(false);
$PAGE->requires->js_init_code(<<<'JS'
document.querySelectorAll('[data-return-to-herb-question]').forEach(function(button) {
    button.addEventListener('click', function() {
        if (window.parent !== window) {
            window.parent.postMessage({type: 'single-herb-card-close'}, window.location.origin);
            return;
        }
        if (window.history.length > 1) {
            window.history.back();
            return;
        }
        window.location.href = button.getAttribute('data-fallback-url');
    });
});
document.querySelectorAll('[data-herb-list-expand]').forEach(function(button) {
    button.addEventListener('click', function() {
        document.querySelectorAll('.single-herbs-list__card').forEach(function(card) {
            card.open = true;
        });
    });
});
document.querySelectorAll('[data-herb-category-link]').forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.stopPropagation();
    });
});
document.querySelectorAll('[data-herb-list-collapse]').forEach(function(button) {
    button.addEventListener('click', function() {
        document.querySelectorAll('.single-herbs-list__card').forEach(function(card) {
            card.open = false;
        });
        window.scrollTo({top: 0, behavior: 'smooth'});
    });
});
JS);

echo $OUTPUT->header();
?>
<style>
.single-herbs-list{width:min(1220px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}
.single-herbs-list__top{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:22px;border:1px solid #c8d9df;border-top:5px solid #f47b20;border-radius:14px;background:#fff}
.single-herbs-list__eyebrow{margin:0 0 5px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.single-herbs-list h2{margin:0 0 8px;font-size:clamp(28px,4vw,40px)}
.single-herbs-list__lead{max-width:780px;margin:0;color:#526b84;line-height:1.55}
.single-herbs-list__actions,.single-herbs-list__modes,.single-herbs-list__tools{display:flex;flex-wrap:wrap;gap:9px}
.single-herbs-list__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}
.single-herbs-list__button:hover{background:#eef7f5}
.single-herbs-list__button.is-active{border-color:#087c69;background:#087c69;color:#fff!important}
.single-herbs-list__controls{margin-bottom:18px;padding:16px;border:1px solid #c7ddd9;border-radius:12px;background:#f8fcfb}
.single-herbs-list__modes{margin-bottom:13px}
.single-herbs-list__form{display:grid;grid-template-columns:minmax(220px,1.5fr) minmax(220px,1fr) auto;gap:10px;align-items:end}
.single-herbs-list__form label{display:grid;gap:5px;color:#35566f;font-size:12px;font-weight:850}
.single-herbs-list__form input,.single-herbs-list__form select{box-sizing:border-box;min-height:44px;width:100%;padding:8px 11px;border:1px solid #9db8c8;border-radius:8px;background:#fff;color:#102a43;font:inherit}
.single-herbs-list__summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px;margin:13px 0}
.single-herbs-list__stat{padding:11px 13px;border:1px solid #c7ddd9;border-radius:9px;background:#fff}
.single-herbs-list__stat span{display:block;color:#087c69;font-size:11px;font-weight:900;text-transform:uppercase}
.single-herbs-list__stat strong{display:block;margin-top:4px;font-size:21px}
.single-herbs-list__tools{justify-content:flex-end}
.single-herbs-list__group{scroll-margin-top:18px;margin:0 0 22px}
.single-herbs-list__group-header{display:flex;align-items:end;justify-content:space-between;gap:16px;margin:0 0 10px;padding:0 4px 8px;border-bottom:3px solid #f47b20}
.single-herbs-list__group-header h3{margin:0;font-size:21px}
.single-herbs-list__group-header span{color:#526b84;font-weight:800}
.single-herbs-list__cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;align-items:start}
.single-herbs-list__card{overflow:hidden;border:1px solid #cbd9df;border-top:4px solid #f47b20;border-radius:11px;background:#fff}
.single-herbs-list__card[open]{box-shadow:0 6px 18px rgba(23,50,77,.08)}
.single-herbs-list__card summary{position:relative;display:block;min-height:108px;padding:15px 48px 14px 16px;cursor:pointer;list-style:none}
.single-herbs-list__card summary::-webkit-details-marker{display:none}
.single-herbs-list__card summary::after{content:'+';position:absolute;top:15px;right:15px;display:grid;width:27px;height:27px;place-items:center;border-radius:50%;background:#eaf5f3;color:#087c69;font-size:18px;font-weight:900}
.single-herbs-list__card[open] summary::after{content:'−'}
.single-herbs-list__name{display:block;font-size:18px;font-weight:900}
.single-herbs-list__latin{display:block;margin-top:3px;color:#64748b;font-size:12px;font-style:italic}
.single-herbs-list__category{display:inline-block;margin-top:8px;padding:4px 8px;border-radius:999px;background:#eaf5f3;color:#087c69!important;font-size:11px;font-weight:850;text-decoration:none!important}
.single-herbs-list__category:hover,.single-herbs-list__category:focus{background:#087c69;color:#fff!important}
.single-herbs-list__meta{display:block;margin-top:8px;color:#075f60;font-size:12px;font-weight:800}
.single-herbs-list__body{padding:14px 16px 16px;border-top:1px solid #d8e2e7}
.single-herbs-list__body dl{display:grid;grid-template-columns:94px minmax(0,1fr);gap:9px 10px;margin:0}
.single-herbs-list__body dt{font-size:12px;font-weight:900}
.single-herbs-list__body dd{margin:0;color:#52657a;font-size:13px;line-height:1.5}
.single-herbs-list__body dd.is-safety{color:#a64b13}
.single-herbs-list__profile-note{margin:12px 0 0;padding:10px;border-radius:8px;background:#fff7e8;color:#7a5412;font-size:12px;line-height:1.45}
.single-herbs-list__memory{color:#075f60!important;font-weight:800}
.single-herbs-list__empty{padding:30px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;color:#526b84;text-align:center;font-weight:750}
@media(max-width:930px){.single-herbs-list__cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:700px){.single-herbs-list__top{display:block}.single-herbs-list__actions{margin-top:15px}.single-herbs-list__form,.single-herbs-list__summary,.single-herbs-list__cards{grid-template-columns:1fr}.single-herbs-list__tools{justify-content:flex-start}.single-herbs-list__body dl{grid-template-columns:1fr}.single-herbs-list__body dd{margin-top:-5px}}
</style>
<main class="single-herbs-list">
    <?php if ($fromlibrary) {
        echo block_myprogress_render_herbology_library_navigation(
            $ises,
            $ises ? 'Lista de hierbas individuales' : 'Single Herbs List',
            'guides'
        );
    } ?>
    <header class="single-herbs-list__top">
        <div>
            <p class="single-herbs-list__eyebrow"><?php echo $ises ? 'GUIDELINE VIGENTE 2026' : 'CURRENT 2026 GUIDELINE'; ?></p>
            <h2>Single Herbs List</h2>
            <p class="single-herbs-list__lead"><?php echo $ises
                ? 'Las 304 hierbas oficiales del guideline, organizadas alfabéticamente o según la secuencia convencional de las 33 categorías Bensky.'
                : 'The 304 official guideline herbs, organized alphabetically or in the conventional sequence of the 33 Bensky categories.'; ?></p>
        </div>
        <div class="single-herbs-list__actions">
            <?php if ($fromquiz) { ?>
                <button class="single-herbs-list__button" type="button" data-return-to-herb-question data-fallback-url="<?php echo $studyguideurl->out(false); ?>"><?php echo $ises ? '← Volver a la pregunta' : '← Back to question'; ?></button>
            <?php } else if ($fromchief) { ?>
                <a class="single-herbs-list__button" href="<?php echo $chiefherbsurl->out(false); ?>"><?php echo $ises ? '← Volver a Chief Herbs' : '← Back to Chief Herbs'; ?></a>
            <?php } else { ?>
                <a class="single-herbs-list__button" href="<?php echo $studyguideurl->out(false); ?>"><?php echo $ises ? '← Single Herbs' : '← Single Herbs'; ?></a>
            <?php } ?>
            <a class="single-herbs-list__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
            <a class="single-herbs-list__button" href="<?php echo $dashboardurl->out(false); ?>">Dashboard</a>
        </div>
    </header>

    <section class="single-herbs-list__controls" aria-label="<?php echo $ises ? 'Controles de la lista' : 'List controls'; ?>">
        <nav class="single-herbs-list__modes" aria-label="<?php echo $ises ? 'Organizar tarjetas' : 'Organize cards'; ?>">
            <?php
            // Sorting modes always start from the complete guideline roster.
            // Category filtering is controlled only by the selector or a card badge.
            // Do not carry a previous search or focused herb into a new sorting mode.
            $azurl = new moodle_url('/blocks/myprogress/single-herbs-list.php', $baseparams + [
                'sort' => 'az',
            ]);
            $categoryurl = new moodle_url('/blocks/myprogress/single-herbs-list.php', $baseparams + [
                'sort' => 'category',
            ]);
            ?>
            <a class="single-herbs-list__button<?php echo $sortmode === 'az' ? ' is-active' : ''; ?>" href="<?php echo $azurl->out(false); ?>"<?php echo $sortmode === 'az' ? ' aria-current="page"' : ''; ?>>A–Z</a>
            <a class="single-herbs-list__button<?php echo $sortmode === 'category' ? ' is-active' : ''; ?>" href="<?php echo $categoryurl->out(false); ?>"<?php echo $sortmode === 'category' ? ' aria-current="page"' : ''; ?>><?php echo $ises ? 'Por orden Bensky' : 'By Bensky order'; ?></a>
            <?php if ($query !== '' || $categoryfilter !== '' || $herbfocus !== '') {
                $backtoallurl = new moodle_url('/blocks/myprogress/single-herbs-list.php', $baseparams + ['sort' => 'az']);
            ?>
                <a class="single-herbs-list__button" href="<?php echo $backtoallurl->out(false); ?>"><?php echo $ises ? '← Volver a todas las hierbas' : '← Back to all herbs'; ?></a>
            <?php } ?>
        </nav>

        <form class="single-herbs-list__form" method="get" action="<?php echo (new moodle_url('/blocks/myprogress/single-herbs-list.php'))->out(false); ?>">
            <input type="hidden" name="sort" value="<?php echo s($sortmode); ?>">
            <?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
            <?php if ($fromlibrary || $fromquiz || $fromchief) { ?><input type="hidden" name="from" value="<?php echo s($fromcontext); ?>"><?php } ?>
            <label>
                <span><?php echo $ises ? 'Buscar hierba' : 'Search herb'; ?></span>
                <input type="search" name="q" value="<?php echo s($query); ?>" placeholder="<?php echo $ises ? 'Nombre, latín, acción…' : 'Name, Latin, action…'; ?>">
            </label>
            <label>
                <span><?php echo $ises ? 'Categoría Bensky' : 'Bensky category'; ?></span>
                <select name="category">
                    <option value=""><?php echo $ises ? 'Las 33 categorías vigentes' : 'All 33 current categories'; ?></option>
                    <?php foreach ($categories as $categorycode => $categoryitem) { ?>
                        <option value="<?php echo s($categorycode); ?>"<?php echo $categoryfilter === $categorycode ? ' selected' : ''; ?>><?php echo s($categoryitem['label']); ?></option>
                    <?php } ?>
                </select>
            </label>
            <button class="single-herbs-list__button is-active" type="submit"><?php echo $ises ? 'Aplicar' : 'Apply'; ?></button>
        </form>

        <div class="single-herbs-list__summary">
            <div class="single-herbs-list__stat"><span><?php echo $ises ? 'Guideline' : 'Guideline'; ?></span><strong>304</strong></div>
            <div class="single-herbs-list__stat"><span><?php echo $ises ? 'Mostradas' : 'Displayed'; ?></span><strong><?php echo count($cards); ?></strong></div>
            <div class="single-herbs-list__stat"><span><?php echo $ises ? 'Categorías' : 'Categories'; ?></span><strong><?php echo count($categories); ?></strong></div>
        </div>
        <div class="single-herbs-list__tools">
            <button class="single-herbs-list__button" type="button" data-herb-list-expand><?php echo $ises ? 'Expandir todas' : 'Expand all'; ?></button>
            <button class="single-herbs-list__button" type="button" data-herb-list-collapse><?php echo $ises ? 'Cerrar todas' : 'Collapse all'; ?></button>
        </div>
    </section>

    <?php if (!$cards) { ?>
        <p class="single-herbs-list__empty"><?php echo $ises
            ? 'No hay hierbas del guideline que coincidan con estos filtros.'
            : 'No guideline herbs match these filters.'; ?></p>
    <?php } else { ?>
        <?php foreach ($groups as $groupkey => $group) { ?>
            <section class="single-herbs-list__group" id="herb-group-<?php echo s($groupkey); ?>">
                <header class="single-herbs-list__group-header">
                    <h3><?php echo s($group['title']); ?></h3>
                    <?php $groupcount = count($group['cards']); ?>
                    <span><?php echo $groupcount; ?> <?php echo $ises
                        ? ($groupcount === 1 ? 'hierba' : 'hierbas')
                        : ($groupcount === 1 ? 'herb' : 'herbs'); ?></span>
                </header>
                <div class="single-herbs-list__cards">
                    <?php foreach ($group['cards'] as $card) {
                        $categoryparams = $baseparams + [
                            'sort' => 'category',
                            'category' => $card['categorycode'],
                        ];
                        $cardcategoryurl = new moodle_url('/blocks/myprogress/single-herbs-list.php', $categoryparams);
                        $metaparts = $card['properties'] !== ''
                            ? array_values(array_filter([$card['properties'], $card['channels']]))
                            : array_values(array_filter([$card['flavor'], $card['temperature'], $card['channels']]));
                    ?>
                        <details class="single-herbs-list__card"<?php echo $autoopen ? ' open' : ''; ?>>
                            <summary>
                                <span class="single-herbs-list__name"><?php echo s($card['name']); ?>*</span>
                                <span class="single-herbs-list__latin"><?php echo s($card['latin']); ?></span>
                                <a class="single-herbs-list__category" data-herb-category-link href="<?php echo $cardcategoryurl->out(false); ?>"><?php echo s($card['categorylabel']); ?></a>
                                <?php if ($metaparts) { ?><span class="single-herbs-list__meta"><?php echo s(implode(' · ', $metaparts)); ?></span><?php } ?>
                            </summary>
                            <div class="single-herbs-list__body">
                                <dl>
                                    <?php if ($card['dose'] !== '') { ?><dt><?php echo $ises ? 'Dosis de estudio' : 'Study dose'; ?></dt><dd><?php echo s($card['dose']); ?></dd><?php } ?>
                                    <?php if ($card['identity'] !== '') { ?><dt><?php echo $ises ? 'Acciones' : 'Actions'; ?></dt><dd><?php echo s($card['identity']); ?></dd><?php } ?>
                                    <?php if ($card['clue'] !== '') { ?><dt><?php echo $ises ? 'Indicaciones' : 'Indications'; ?></dt><dd><?php echo s($card['clue']); ?></dd><?php } ?>
                                    <?php if ($card['memory'] !== '') { ?><dt><?php echo $ises ? 'Memoria' : 'Memory'; ?></dt><dd class="single-herbs-list__memory"><?php echo s($card['memory']); ?></dd><?php } ?>
                                    <?php if ($card['unique'] !== '') { ?><dt><?php echo $ises ? 'Huella única' : 'Unique fingerprint'; ?></dt><dd><?php echo s($card['unique']); ?></dd><?php } ?>
                                    <?php if ($card['safety'] !== '') { ?><dt><?php echo $ises ? 'Precaución' : 'Caution'; ?></dt><dd class="is-safety"><?php echo s($card['safety']); ?></dd><?php } ?>
                                    <?php if ($card['preparation'] !== '') { ?><dt><?php echo $ises ? 'Preparación segura' : 'Safe preparation'; ?></dt><dd class="is-safety"><?php echo s($card['preparation']); ?></dd><?php } ?>
                                    <?php if ($card['pregnancy'] !== '') { ?><dt><?php echo $ises ? 'Embarazo' : 'Pregnancy'; ?></dt><dd class="is-safety"><?php echo s($card['pregnancy']); ?></dd><?php } ?>
                                    <?php if ($card['interactions'] !== '') { ?><dt><?php echo $ises ? 'Interacciones' : 'Interactions'; ?></dt><dd class="is-safety"><?php echo s($card['interactions']); ?></dd><?php } ?>
                                </dl>
                                <?php if ($card['identity'] === '' && $card['clue'] === '') { ?>
                                    <p class="single-herbs-list__profile-note"><?php echo $ises
                                        ? 'Hierba oficial del guideline con identidad farmacéutica, propiedades y categoría verificadas; la fuente no incluye todavía un perfil clínico detallado.'
                                        : 'Official guideline herb with verified pharmaceutical identity, properties and category; the source does not yet include a detailed clinical profile.'; ?></p>
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
