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
$returnformula = trim(optional_param('returnformula', '', PARAM_TEXT));
$fromlibrary = $fromcontext === 'library';
$fromquiz = $fromcontext === 'quiz';
$fromchief = $fromcontext === 'chief';
$fromingredients = $fromcontext === 'ingredients';
$fromformula = $fromcontext === 'formula' && $returnformula !== '';
$fromsmart = $fromcontext === 'smart';
$fromcautions = $fromcontext === 'cautions';
$returnchapter = optional_param('returnchapter', '', PARAM_FILE);
$returnorigin = optional_param('returnorigin', '', PARAM_ALPHA);
$incompatibilitychapters = [
    'incompatibilities-seven-relationships.html',
    'incompatibilities-xu-vs-shi.html',
    'incompatibilities-wei-vs-sha.html',
    'incompatibilities-wu-vs-fan.html',
    'incompatibilities-shi-ba-fan-aconite.html',
    'incompatibilities-shi-ba-fan-gancao-lilu.html',
    'incompatibilities-shi-jiu-wei.html',
    'incompatibilities-extended-counteraction.html',
    'incompatibilities-extended-antagonism.html',
    'incompatibilities-dietary.html',
    'incompatibilities-medicinal-origins.html',
];
$fromincompatibilities = $fromcontext === 'incompatibilities'
    && in_array($returnchapter, $incompatibilitychapters, true);
$returnorigin = in_array($returnorigin, ['animal', 'plant', 'mineral', 'category'], true) ? $returnorigin : '';
$fromoriginmap = $fromcontext === 'originmap';
$fromformulatree = $fromcontext === 'formulatree';
$treepage = optional_param('treepage', 1, PARAM_INT);
$treepage = $treepage >= 1 && $treepage <= 6 ? $treepage : 1;
$treemode = optional_param('treemode', 'all', PARAM_ALPHA);
$treemode = $treemode === 'official' ? 'official' : 'all';
$smartmode = optional_param('smartmode', 'herb', PARAM_ALPHA);
$smartitem = trim(optional_param('smartitem', '', PARAM_TEXT));
$smartq = trim(optional_param('smartq', '', PARAM_TEXT));

$baseparams = [];
if ($ises) {
    $baseparams['lang'] = 'es';
}
if ($fromlibrary || $fromquiz || $fromchief || $fromingredients || $fromformula || $fromsmart || $fromcautions || $fromincompatibilities || $fromoriginmap || $fromformulatree) {
    $baseparams['from'] = $fromcontext;
}
if ($fromformula) {
    $baseparams['returnformula'] = $returnformula;
}
if ($fromincompatibilities) {
    $baseparams['returnchapter'] = $returnchapter;
    if ($returnorigin !== '') $baseparams['returnorigin'] = $returnorigin;
}
if ($fromoriginmap && $returnorigin !== '') {
    $baseparams['returnorigin'] = $returnorigin;
}
if ($fromformulatree) {
    $baseparams['treepage'] = $treepage;
    $baseparams['treemode'] = $treemode;
}
if ($fromsmart) {
    $baseparams['smartmode'] = $smartmode;
    if ($smartitem !== '') $baseparams['smartitem'] = $smartitem;
    if ($smartq !== '') $baseparams['smartq'] = $smartq;
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
if ($fromlibrary || $fromquiz || $fromchief || $fromingredients || $fromformula || $fromsmart || $fromcautions || $fromincompatibilities || $fromoriginmap || $fromformulatree) {
    $languageparams['from'] = $fromcontext;
}
if ($fromformula) {
    $languageparams['returnformula'] = $returnformula;
}
if ($fromincompatibilities) {
    $languageparams['returnchapter'] = $returnchapter;
    if ($returnorigin !== '') $languageparams['returnorigin'] = $returnorigin;
}
if ($fromoriginmap && $returnorigin !== '') {
    $languageparams['returnorigin'] = $returnorigin;
}
if ($fromformulatree) {
    $languageparams['treepage'] = $treepage;
    $languageparams['treemode'] = $treemode;
}
if ($fromsmart) {
    $languageparams['smartmode'] = $smartmode;
    if ($smartitem !== '') $languageparams['smartitem'] = $smartitem;
    if ($smartq !== '') $languageparams['smartq'] = $smartq;
}
$languageurl = new moodle_url('/blocks/myprogress/single-herbs-list.php', $languageparams);
$studyguideparams = $ises ? ['lang' => 'es'] : [];
if ($fromlibrary) {
    $studyguideparams['from'] = 'library';
}
$studyguideurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $studyguideparams);
$dashboardurl = new moodle_url('/blocks/myprogress/study-guide.php', $ises
    ? ['area' => 'herbology', 'view' => 'library', 'lang' => 'es']
    : ['area' => 'herbology', 'view' => 'library']);
$chiefherbsurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'chief-herbs-property-es.html' : 'chief-herbs-property.html'));
$formulaingredientsurl = new moodle_url('/blocks/myprogress/formula-ingredients.php', $ises ? ['lang' => 'es'] : []);
$formulabackparams = ['formula' => $returnformula, 'sort' => 'az'];
if ($ises) {
    $formulabackparams['lang'] = 'es';
}
$formulabackurl = new moodle_url('/blocks/myprogress/formula-list.php', $formulabackparams);
$smartreturnparams = ['mode' => $smartmode];
if ($smartitem !== '') $smartreturnparams['item'] = $smartitem;
if ($smartq !== '') $smartreturnparams['q'] = $smartq;
if ($ises) $smartreturnparams['lang'] = 'es';
$smarttoolsurl = new moodle_url('/blocks/myprogress/smart-study-tools.php', $smartreturnparams);
$localizedreturnchapter = $returnchapter;
if ($fromincompatibilities && $ises) {
    $localizedreturnchapter = preg_replace('/\.html$/', '-es.html', $returnchapter);
}
$incompatibilityreturnparams = $returnorigin !== '' ? ['origin' => $returnorigin] : [];
$incompatibilityreturnurl = new moodle_url(
    '/study-guide/comparisons/' . $localizedreturnchapter,
    $incompatibilityreturnparams
);
$cautionsreturnurl = new moodle_url(
    '/study-guide/comparisons/' . ($ises ? 'cautions-contraindications-es.html' : 'cautions-contraindications.html')
);
$originmapreturnurl = new moodle_url(
    '/study-guide/comparisons/' . ($ises ? 'guideline-herbs-by-origin-es.html' : 'guideline-herbs-by-origin.html'),
    $returnorigin !== '' ? ['origin' => $returnorigin] : []
);
$formulatreereturnurl = new moodle_url(
    '/study-guide/comparisons/' . ($ises ? 'formula-category-tree-map-es.html' : 'formula-category-tree-map.html'),
    ['page' => $treepage, 'mode' => $treemode]
);

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
$buildcardnarration = static function(array $card, bool $ises): string {
    $segments = [];
    $append = static function(array &$target, string $label, string $value): void {
        $value = trim($value);
        if ($value === '') {
            return;
        }
        $target[] = $label . ': ' . rtrim($value, ". ") . '.';
    };
    $append($segments, $ises ? 'Hierba' : 'Herb', (string)($card['name'] ?? ''));
    $append($segments, $ises ? 'Nombre farmacéutico' : 'Pharmaceutical name', (string)($card['latin'] ?? ''));
    $append($segments, $ises ? 'Categoría' : 'Category', (string)($card['categorylabel'] ?? ''));
    if (trim((string)($card['properties'] ?? '')) !== '') {
        $append($segments, $ises ? 'Propiedades de la hierba' : 'Herb properties', (string)$card['properties']);
    } else {
        $append($segments, $ises ? 'Sabores' : 'Flavors', (string)($card['flavor'] ?? ''));
        $append($segments, $ises ? 'Temperatura' : 'Temperature', (string)($card['temperature'] ?? ''));
    }
    $append($segments, $ises ? 'Meridianos que penetra' : 'Channels entered', (string)($card['channels'] ?? ''));
    $append($segments, $ises ? 'Dosis' : 'Dosage', (string)($card['dose'] ?? ''));
    $append($segments, $ises ? 'Acciones' : 'Actions', (string)($card['identity'] ?? ''));
    $append($segments, $ises ? 'Indicaciones' : 'Indications', (string)($card['clue'] ?? ''));
    $append($segments, $ises ? 'Recurso de memoria' : 'Memory aid', (string)($card['memory'] ?? ''));
    $append($segments, $ises ? 'Huella única' : 'Unique fingerprint', (string)($card['unique'] ?? ''));
    $append($segments, $ises ? 'Precaución' : 'Caution', (string)($card['safety'] ?? ''));
    $append($segments, $ises ? 'Preparación segura' : 'Safe preparation', (string)($card['preparation'] ?? ''));
    $append($segments, $ises ? 'Embarazo' : 'Pregnancy', (string)($card['pregnancy'] ?? ''));
    $append($segments, $ises ? 'Interacciones' : 'Interactions', (string)($card['interactions'] ?? ''));
    return implode(' ', $segments);
};

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
(function() {
    'use strict';
    var reader = document.querySelector('[data-herb-list-reader]');
    if (!reader) {
        return;
    }
    var supported = 'speechSynthesis' in window && 'SpeechSynthesisUtterance' in window;
    var language = reader.getAttribute('data-language') || 'en-US';
    var isSpanish = language.toLowerCase().indexOf('es') === 0;
    var voiceSelect = reader.querySelector('[data-herb-list-voice]');
    var startSelect = reader.querySelector('[data-herb-list-start]');
    var readDisplayedButton = reader.querySelector('[data-herb-list-read-displayed]');
    var pauseButton = reader.querySelector('[data-herb-list-pause]');
    var stopButton = reader.querySelector('[data-herb-list-stop]');
    var backButton = reader.querySelector('[data-herb-list-back]');
    var forwardButton = reader.querySelector('[data-herb-list-forward]');
    var progress = reader.querySelector('[data-herb-list-progress]');
    var progressValue = reader.querySelector('[data-herb-list-progress-value]');
    var status = reader.querySelector('[data-herb-list-status]');
    var speechChunks = [];
    var currentChunk = 0;
    var sessionId = 0;
    var activeCard = null;
    var paused = false;
    var selectedVoice = null;
    var readingCards = [];
    var currentCardPosition = -1;

    function setStatus(message) {
        status.textContent = message;
    }

    function cleanSpeechText(value) {
        var text = String(value || '')
            .replace(/\*/g, '')
            .replace(/[\u3400-\u9fff\uf900-\ufaff]/g, ' ')
            .replace(/[→⇒➜➝]/g, isSpanish ? '. Luego, ' : '. Then, ')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*[–—-]\s*(\d+(?:[.,]\d+)?)\s*mg\b/gi, isSpanish ? '$1 a $2 miligramos' : '$1 to $2 milligrams')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*[–—-]\s*(\d+(?:[.,]\d+)?)\s*g\b/gi, isSpanish ? '$1 a $2 gramos' : '$1 to $2 grams')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*[–—-]\s*(\d+(?:[.,]\d+)?)\s*ml\b/gi, isSpanish ? '$1 a $2 mililitros' : '$1 to $2 milliliters')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*mg\b/gi, isSpanish ? '$1 miligramos' : '$1 milligrams')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*g\b/gi, isSpanish ? '$1 gramos' : '$1 grams')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*ml\b/gi, isSpanish ? '$1 mililitros' : '$1 milliliters')
            .replace(/[•·|]+/g, '. ')
            .replace(/[()\[\]{}]/g, ' ')
            .replace(/\s*\/\s*/g, isSpanish ? ' o ' : ' or ')
            .replace(/\s*;\s*/g, '. ')
            .replace(/\s*:\s*/g, ': ')
            .replace(/\s+/g, ' ')
            .trim();
        var pronunciations = isSpanish ? {
            'Bo He': 'Bó Ge', 'Niu Bang Zi': 'Niú, Bang, Tzí', 'Chan Tui': 'Chan, Tuéi',
            'Sang Ye': 'Sang, Yé', 'Ju Hua': 'Yú, Juá', 'Man Jing Zi': 'Man, Ying, Tzí',
            'Ge Gen': 'Gue, Guen', 'Chai Hu': 'Chái, Jú', 'Sheng Ma': 'Sheng, Má',
            'Dan Dou Chi': 'Dan, Dou, Chi', 'Mu Zei': 'Mu, Tsei', 'Ma Huang': 'Má, Juáng',
            'Gui Zhi': 'Guéi, Yí', 'Zi Su Ye': 'Tzí, Su, Yé', 'Xi Xin': 'Shí, Shín',
            'Bai Zhi': 'Bái, Yí', 'Fang Feng': 'Fang, Feng', 'Qiang Huo': 'Chiang, Juó',
            'Jing Jie': 'Ying, Yie', 'Sheng Jiang': 'Sheng, Chiang', 'Xing Ren': 'Xing Ren',
            'Táo Rén': 'Tao Ren', 'Tao Ren': 'Tao Ren',
            'Dāng Guī': 'Dang Qui', 'Dang Gui': 'Dang Qui',
            'Rén Shēn': 'Ren Shen', 'Ren Shen': 'Ren Shen'
        } : {
            'Bo He': 'Bo Huh', 'Niu Bang Zi': 'Nee-oh, Bahng, Dzuh', 'Chan Tui': 'Chahn, Tway',
            'Sang Ye': 'Sahng, Yeh', 'Ju Hua': 'Joo, Hwah', 'Man Jing Zi': 'Mahn, Jing, Dzuh',
            'Ge Gen': 'Guh, Gun', 'Chai Hu': 'Chai, Hoo', 'Sheng Ma': 'Shung, Mah',
            'Dan Dou Chi': 'Dahn, Doh, Chir', 'Mu Zei': 'Moo, Dzay', 'Ma Huang': 'Mah, Hwahng',
            'Gui Zhi': 'Gway, Jir', 'Zi Su Ye': 'Dzuh, Soo, Yeh', 'Xi Xin': 'Shee, Sheen',
            'Bai Zhi': 'Bye, Jir', 'Fang Feng': 'Fahng, Fung', 'Qiang Huo': 'Chyahng, Hwoh',
            'Jing Jie': 'Jing, Jyeh', 'Sheng Jiang': 'Shung, Jyahng', 'Xing Ren': 'Sheeng, Run',
            'Táo Rén': 'Taow, Run', 'Tao Ren': 'Taow, Run',
            'Dāng Guī': 'Dahng, Gway', 'Dang Gui': 'Dahng, Gway',
            'Rén Shēn': 'Run, Shun', 'Ren Shen': 'Run, Shun'
        };
        Object.keys(pronunciations).sort(function(a, b) { return b.length - a.length; }).forEach(function(name) {
            text = text.replace(new RegExp('\\b' + name.replace(/\s+/g, '\\s+') + '\\b', 'gi'), pronunciations[name]);
        });
        text = text.replace(/(^|[^A-Za-zÀ-ž])Q[iīíǐì](?=$|[^A-Za-zÀ-ž])/gi, function(match, prefix) {
            return prefix + 'Chi';
        });
        return text.replace(/\s+([,.])/g, '$1').replace(/([.!?])\s*([.!?])+/g, '$1').trim();
    }

    function splitSpeechText(text) {
        var cleaned = cleanSpeechText(text);
        var sentences = cleaned.match(/[^.!?]+[.!?]+|[^.!?]+$/g) || [];
        var chunks = [];
        sentences.forEach(function(sentence) {
            sentence = sentence.trim();
            if (!sentence) return;
            if (sentence.length <= 260) {
                chunks.push(sentence);
                return;
            }
            var parts = sentence.split(/,\s+/);
            var current = '';
            parts.forEach(function(part) {
                var candidate = current ? current + ', ' + part : part;
                if (candidate.length > 260 && current) {
                    chunks.push(current + '.');
                    current = part;
                } else {
                    current = candidate;
                }
            });
            if (current) chunks.push(current);
        });
        return chunks;
    }

    function preferredVoice(voices) {
        var stored = '';
        try {
            stored = window.localStorage.getItem('acupuncturetests-reader-voice-' + language.slice(0, 2).toLowerCase()) || '';
        } catch (error) {}
        if (stored) {
            var saved = voices.find(function(voice) { return voice.name === stored; });
            if (saved) return saved;
        }
        var naturalPattern = /natural|neural|premium|enhanced|online|google|microsoft|samantha|daniel|ava|aria|jorge|helena|monica/i;
        return voices.find(function(voice) { return naturalPattern.test(voice.name); }) || voices[0] || null;
    }

    var mobileVoiceMenu = !!(window.matchMedia && (
        window.matchMedia('(max-width: 760px)').matches || window.matchMedia('(pointer: coarse)').matches
    ));

    function isEducationalVoice(voice) {
        var identity = String((voice && voice.name) || '') + ' ' + String((voice && voice.voiceURI) || '');
        var noveltyOrEffect = /\b(?:albert|bad news|bahh|bells|boing|bubbles|cellos|child|cute|eddy|flo|good news|grandma|grandpa|hysterical|jester|kid|niña|niño|organ|princess|ralph|reed|rocko|sandy|shelley|superstar|trinoids|whisper|wobble|zarvox|alien|character|demon|effect|fantasma|ghost|horror|juego|game|monster|monstruo|novelty|robot|scary|singing|susurro|witch|zombie)\b/i;
        var obsoleteOrRobotic = /\b(?:espeak|festival|flite|eloquence|desktop)\b/i;
        return !noveltyOrEffect.test(identity) && !obsoleteOrRobotic.test(identity);
    }

    function isProfessionalMobileVoice(voice) {
        var identity = (String((voice && voice.name) || '') + ' ' + String((voice && voice.voiceURI) || '')).toLowerCase();
        return /google|microsoft|samsung|amazon|natural|neural|premium|enhanced|studio|online|siri|mónica|monica|paulina|dalia|jorge|helena|lucía|lucia|isabela|sofía|sofia|carlos|samantha|daniel|ava|aria|jenny|sonia|ryan|emma|natasha/.test(identity);
    }

    function availableEducationalVoices() {
        var prefix = language.slice(0, 2).toLowerCase();
        var voices = window.speechSynthesis.getVoices().filter(function(voice) {
            return (voice.lang || '').slice(0, 2).toLowerCase() === prefix && isEducationalVoice(voice);
        });
        if (!mobileVoiceMenu) return voices;
        return voices.filter(isProfessionalMobileVoice).sort(function(first, second) {
            var firstNatural = /natural|neural|premium|enhanced|studio|online/i.test(first.name + ' ' + first.voiceURI) ? 1 : 0;
            var secondNatural = /natural|neural|premium|enhanced|studio|online/i.test(second.name + ' ' + second.voiceURI) ? 1 : 0;
            return secondNatural - firstNatural || first.name.localeCompare(second.name);
        }).slice(0, 6);
    }

    function loadVoices() {
        if (!supported) return;
        var voices = availableEducationalVoices();
        selectedVoice = preferredVoice(voices);
        voiceSelect.innerHTML = '';
        var automatic = document.createElement('option');
        automatic.value = '';
        automatic.textContent = isSpanish ? 'Automática — voz educativa recomendada' : 'Automatic — recommended educational voice';
        voiceSelect.appendChild(automatic);
        voices.forEach(function(voice) {
            var option = document.createElement('option');
            option.value = voice.name;
            option.textContent = voice.name + ' (' + voice.lang + ')' + (voice.localService ? '' : ' · online');
            if (selectedVoice && voice.name === selectedVoice.name) option.selected = true;
            voiceSelect.appendChild(option);
        });
    }

    function updateControls() {
        var active = speechChunks.length > 0;
        var readingCardQueue = readingCards.length > 0 && currentCardPosition >= 0;
        var position = readingCardQueue ? currentCardPosition : currentChunk;
        var total = readingCardQueue ? readingCards.length : speechChunks.length;
        pauseButton.disabled = !active;
        stopButton.disabled = !active;
        backButton.disabled = !active || position <= 0;
        forwardButton.disabled = !active || position >= total - 1;
        progress.disabled = !active;
        progress.max = Math.max(0, total - 1);
        progress.value = Math.min(position, Math.max(0, total - 1));
        if (readingCardQueue) {
            progressValue.textContent = (isSpanish ? 'Hierba ' : 'Herb ') + (currentCardPosition + 1) + ' / ' + readingCards.length + ' · ' + readingCards[currentCardPosition].getAttribute('data-herb-read-label');
        } else {
            progressValue.textContent = active
                ? (isSpanish ? 'Fragmento ' : 'Fragment ') + (currentChunk + 1) + ' / ' + speechChunks.length
                : (isSpanish ? 'Sin lectura' : 'Not reading');
        }
        pauseButton.textContent = paused
            ? (isSpanish ? '▶ Continuar' : '▶ Resume')
            : (isSpanish ? '⏸ Pausar' : '⏸ Pause');
        backButton.textContent = readingCardQueue
            ? (isSpanish ? '← Hierba anterior' : '← Previous herb')
            : (isSpanish ? '↶ Retroceder' : '↶ Back');
        forwardButton.textContent = readingCardQueue
            ? (isSpanish ? 'Siguiente hierba →' : 'Next herb →')
            : (isSpanish ? 'Avanzar ↷' : 'Forward ↷');
    }

    function clearActiveCard() {
        if (activeCard) activeCard.classList.remove('is-reading');
        activeCard = null;
    }

    function finishReading(message) {
        window.speechSynthesis.cancel();
        speechChunks = [];
        currentChunk = 0;
        readingCards = [];
        currentCardPosition = -1;
        paused = false;
        clearActiveCard();
        setStatus(message || (isSpanish ? 'Lectura terminada.' : 'Reading finished.'));
        updateControls();
    }

    function speakCurrent(activeSession) {
        if (activeSession !== sessionId || !speechChunks.length) return;
        if (currentChunk >= speechChunks.length) {
            finishReading();
            return;
        }
        var utterance = new SpeechSynthesisUtterance(speechChunks[currentChunk]);
        utterance.lang = language;
        utterance.rate = 1;
        if (selectedVoice) utterance.voice = selectedVoice;
        utterance.onend = function() {
            if (activeSession !== sessionId || paused) return;
            currentChunk += 1;
            if (currentChunk >= speechChunks.length) {
                if (readingCards.length && currentCardPosition < readingCards.length - 1) {
                    startQueuedCard(currentCardPosition + 1);
                    return;
                }
                finishReading();
                return;
            }
            updateControls();
            speakCurrent(activeSession);
        };
        utterance.onerror = function(event) {
            if (activeSession !== sessionId || event.error === 'canceled' || event.error === 'interrupted') return;
            finishReading(isSpanish ? 'No se pudo continuar la lectura.' : 'Reading could not continue.');
        };
        if (!readingCards.length) {
            setStatus(isSpanish ? 'Leyendo…' : 'Reading…');
        }
        updateControls();
        window.speechSynthesis.speak(utterance);
    }

    function startReading(text, card, label, preserveQueue) {
        sessionId += 1;
        window.speechSynthesis.cancel();
        clearActiveCard();
        if (!preserveQueue) {
            readingCards = [];
            currentCardPosition = -1;
        }
        speechChunks = splitSpeechText(text);
        currentChunk = 0;
        paused = false;
        if (!speechChunks.length) {
            setStatus(isSpanish ? 'No hay texto disponible para leer.' : 'No text is available to read.');
            updateControls();
            return;
        }
        if (card) {
            activeCard = card;
            if (!preserveQueue) card.open = true;
            card.classList.add('is-reading');
        }
        setStatus((isSpanish ? 'Preparando: ' : 'Preparing: ') + label + '.');
        updateControls();
        var activeSession = sessionId;
        window.setTimeout(function() { speakCurrent(activeSession); }, 80);
    }

    function startQueuedCard(position) {
        if (!readingCards.length) return;
        currentCardPosition = Math.max(0, Math.min(position, readingCards.length - 1));
        var card = readingCards[currentCardPosition];
        var name = card.getAttribute('data-herb-read-label');
        startSelect.value = String(currentCardPosition);
        var numberedIntroduction = (isSpanish ? 'Hierba número ' : 'Herb number ') + (currentCardPosition + 1) + (isSpanish ? ' de ' : ' of ') + readingCards.length + '. ';
        startReading(numberedIntroduction + card.getAttribute('data-herb-read-text'), card, name, true);
        var nextName = currentCardPosition < readingCards.length - 1
            ? readingCards[currentCardPosition + 1].getAttribute('data-herb-read-label')
            : '';
        setStatus((isSpanish ? 'Leyendo hierba ' : 'Reading herb ') + (currentCardPosition + 1) + (isSpanish ? ' de ' : ' of ') + readingCards.length + ': ' + name + '. ' + (nextName
            ? (isSpanish ? 'Después continuará automáticamente con ' : 'It will continue automatically with ') + nextName + '.'
            : (isSpanish ? 'Esta es la última hierba de la lista.' : 'This is the final herb in the list.')));
        updateControls();
    }

    if (!supported) {
        reader.hidden = false;
        reader.classList.add('is-unsupported');
        setStatus(isSpanish ? 'Este navegador no ofrece lectura en voz alta.' : 'This browser does not provide read aloud.');
        reader.querySelectorAll('button, select, input').forEach(function(control) { control.disabled = true; });
        return;
    }
    reader.hidden = false;
    loadVoices();
    window.speechSynthesis.onvoiceschanged = loadVoices;
    var availableCards = Array.prototype.slice.call(document.querySelectorAll('[data-herb-read-text]'));
    startSelect.innerHTML = '';
    availableCards.forEach(function(card, index) {
        var option = document.createElement('option');
        option.value = String(index);
        option.textContent = (index + 1) + '. ' + card.getAttribute('data-herb-read-label');
        startSelect.appendChild(option);
    });
    voiceSelect.addEventListener('change', function() {
        var voices = availableEducationalVoices();
        selectedVoice = voices.find(function(voice) { return voice.name === voiceSelect.value; }) || preferredVoice(voices);
        try {
            window.localStorage.setItem('acupuncturetests-reader-voice-' + language.slice(0, 2).toLowerCase(), voiceSelect.value);
        } catch (error) {}
    });
    document.querySelectorAll('[data-herb-read-button]').forEach(function(button) {
        button.addEventListener('click', function() {
            var card = button.closest('[data-herb-read-text]');
            startReading(card.getAttribute('data-herb-read-text'), card, card.getAttribute('data-herb-read-label'));
        });
    });
    readDisplayedButton.addEventListener('click', function() {
        readingCards = Array.prototype.slice.call(document.querySelectorAll('[data-herb-read-text]'));
        if (!readingCards.length) {
            setStatus(isSpanish ? 'No hay hierbas mostradas para leer.' : 'There are no displayed herbs to read.');
            return;
        }
        startQueuedCard(Number(startSelect.value || 0));
    });
    pauseButton.addEventListener('click', function() {
        if (!speechChunks.length) return;
        if (paused) {
            paused = false;
            window.speechSynthesis.resume();
            setStatus(isSpanish ? 'Lectura reanudada.' : 'Reading resumed.');
        } else {
            paused = true;
            window.speechSynthesis.pause();
            setStatus(isSpanish ? 'Lectura en pausa.' : 'Reading paused.');
        }
        updateControls();
    });
    stopButton.addEventListener('click', function() {
        sessionId += 1;
        finishReading(isSpanish ? 'Lectura detenida.' : 'Reading stopped.');
    });
    function seekTo(index) {
        if (!speechChunks.length) return;
        if (readingCards.length) {
            startQueuedCard(index);
            return;
        }
        sessionId += 1;
        window.speechSynthesis.cancel();
        paused = false;
        currentChunk = Math.max(0, Math.min(index, speechChunks.length - 1));
        updateControls();
        var activeSession = sessionId;
        window.setTimeout(function() { speakCurrent(activeSession); }, 80);
    }
    backButton.addEventListener('click', function() { seekTo(currentChunk - 1); });
    forwardButton.addEventListener('click', function() { seekTo(currentChunk + 1); });
    progress.addEventListener('change', function() { seekTo(Number(progress.value)); });
    window.addEventListener('beforeunload', function() { window.speechSynthesis.cancel(); });
    updateControls();
})();
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
.single-herbs-list-reader{position:sticky;top:70px;z-index:1020;margin:0 0 18px;padding:13px 16px;border:1px solid #9ed3ce;border-left:5px solid #0b8f87;border-radius:12px;background:#f4fbfa;box-shadow:0 5px 16px rgba(15,76,81,.08)}
.single-herbs-list-reader[hidden]{display:none}.single-herbs-list-reader__disclosure>summary{position:relative;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding-right:40px;cursor:pointer;list-style:none}.single-herbs-list-reader__disclosure>summary::-webkit-details-marker{display:none}.single-herbs-list-reader__disclosure>summary:after{content:'+';position:absolute;right:0;top:0;display:flex;width:27px;height:27px;align-items:center;justify-content:center;border:1px solid #8fc9c4;border-radius:50%;background:#fff;color:#0b6f69;font-size:19px;font-weight:900}.single-herbs-list-reader__disclosure[open]>summary:after{content:'−'}
.single-herbs-list-reader__title{display:block;font-size:17px;font-weight:900}.single-herbs-list-reader__description{display:block;margin-top:2px;color:#5c7184;font-size:12px}.single-herbs-list-reader__panel{padding-top:11px}.single-herbs-list-reader__controls{display:grid;grid-template-columns:minmax(180px,1fr) minmax(180px,1fr) repeat(3,auto);gap:8px;align-items:end}.single-herbs-list-reader__field{display:grid;gap:4px;color:#294b63;font-size:11px;font-weight:850}.single-herbs-list-reader select{box-sizing:border-box;min-height:39px;width:100%;padding:6px 9px;border:1px solid #9db8c8;border-radius:8px;background:#fff;color:#102a43;font:inherit}.single-herbs-list-reader button{min-height:39px;padding:6px 11px;border:1px solid #0b8f87;border-radius:8px;background:#fff;color:#087c69;font-weight:900;cursor:pointer}.single-herbs-list-reader button[data-herb-list-read-displayed]{background:#0b8f87;color:#fff}.single-herbs-list-reader button:disabled{cursor:not-allowed;opacity:.45}.single-herbs-list-reader__progress{display:grid;grid-template-columns:auto minmax(180px,1fr) auto;gap:8px;align-items:end;margin-top:8px}.single-herbs-list-reader__progress-field{display:grid;grid-template-columns:1fr auto;gap:3px 8px;color:#526b84;font-size:10px;font-weight:800}.single-herbs-list-reader__progress-field input{grid-column:1/-1;width:100%;accent-color:#0b8f87}.single-herbs-list-reader__status{min-height:18px;margin:7px 0 0;color:#526b84;font-size:11px;font-weight:750}
.single-herbs-list__controls{margin-bottom:14px;padding:12px 14px;border:1px solid #c7ddd9;border-radius:12px;background:#f8fcfb}
.single-herbs-list__controls-disclosure>summary{position:relative;display:flex;min-height:25px;align-items:center;padding-right:36px;color:#17344a;font-size:14px;font-weight:900;cursor:pointer;list-style:none}.single-herbs-list__controls-disclosure>summary::-webkit-details-marker{display:none}.single-herbs-list__controls-disclosure>summary:after{content:'+';position:absolute;right:0;top:0;display:flex;width:25px;height:25px;align-items:center;justify-content:center;border:1px solid #8fc9c4;border-radius:50%;background:#fff;color:#0b6f69;font-size:18px;font-weight:900;line-height:1}.single-herbs-list__controls-disclosure[open]>summary:after{content:'−'}.single-herbs-list__controls-panel{padding-top:8px}
.single-herbs-list__controls .single-herbs-list__button{min-height:38px;padding:6px 12px}
.single-herbs-list__modes{margin-bottom:9px}
.single-herbs-list__form{display:grid;grid-template-columns:minmax(220px,1.5fr) minmax(220px,1fr) auto;gap:10px;align-items:end}
.single-herbs-list__form label{display:grid;gap:5px;color:#35566f;font-size:12px;font-weight:850}
.single-herbs-list__form input,.single-herbs-list__form select{box-sizing:border-box;min-height:40px;width:100%;padding:6px 10px;border:1px solid #9db8c8;border-radius:8px;background:#fff;color:#102a43;font:inherit}
.single-herbs-list__summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin:9px 0}
.single-herbs-list__stat{padding:7px 11px;border:1px solid #c7ddd9;border-radius:9px;background:#fff}
.single-herbs-list__stat span{display:block;color:#087c69;font-size:11px;font-weight:900;text-transform:uppercase}
.single-herbs-list__stat strong{display:block;margin-top:2px;font-size:19px}
.single-herbs-list__tools{justify-content:flex-end}
.single-herbs-list__group{scroll-margin-top:18px;margin:0 0 22px}
.single-herbs-list__group-header{display:flex;align-items:end;justify-content:space-between;gap:16px;margin:0 0 10px;padding:0 4px 8px;border-bottom:3px solid #f47b20}
.single-herbs-list__group-header h3{margin:0;font-size:21px}
.single-herbs-list__group-header span{color:#526b84;font-weight:800}
.single-herbs-list__cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;align-items:start}
.single-herbs-list__card{overflow:hidden;border:1px solid #cbd9df;border-top:4px solid #f47b20;border-radius:11px;background:#fff}
.single-herbs-list__card[open]{box-shadow:0 6px 18px rgba(23,50,77,.08)}.single-herbs-list__card.is-reading{outline:3px solid rgba(11,143,135,.35);box-shadow:0 8px 24px rgba(11,143,135,.18)}
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
.single-herbs-list__connections{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;padding-top:12px;border-top:1px solid #d8e2e7}.single-herbs-list__connection{display:inline-flex;min-height:38px;align-items:center;padding:7px 11px;border:1px solid #6d28d9;border-radius:8px;background:#f5f0ff;color:#4c1d95!important;font:inherit;font-size:12px;font-weight:900;text-decoration:none!important;cursor:pointer}.single-herbs-list__connection--audio{border-color:#0b8f87;background:#eefaf8;color:#087c69!important}.single-herbs-list__connection:hover,.single-herbs-list__connection:focus{background:#6d28d9;color:#fff!important}.single-herbs-list__connection--audio:hover,.single-herbs-list__connection--audio:focus{background:#0b8f87;color:#fff!important}
.single-herbs-list__profile-note{margin:12px 0 0;padding:10px;border-radius:8px;background:#fff7e8;color:#7a5412;font-size:12px;line-height:1.45}
.single-herbs-list__memory{color:#075f60!important;font-weight:800}
.single-herbs-list__empty{padding:30px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;color:#526b84;text-align:center;font-weight:750}
@media(max-width:930px){.single-herbs-list__cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:700px){.single-herbs-list{width:min(100% - 14px,1220px);margin-top:10px}.single-herbs-list__top{display:block;padding:15px}.single-herbs-list__actions{margin-top:12px;gap:6px}.single-herbs-list__button{min-height:35px;padding:6px 9px;font-size:12px}.single-herbs-list__form,.single-herbs-list__summary,.single-herbs-list__cards{grid-template-columns:1fr}.single-herbs-list__tools{justify-content:flex-start}.single-herbs-list__body dl{grid-template-columns:1fr}.single-herbs-list__body dd{margin-top:-5px}.single-herbs-list-reader{top:58px;padding:10px 11px}.single-herbs-list-reader__disclosure>summary{padding-right:34px}.single-herbs-list-reader__disclosure>summary:after{width:24px;height:24px;font-size:17px}.single-herbs-list-reader__title{font-size:15px}.single-herbs-list-reader__description{font-size:11px;line-height:1.3}.single-herbs-list-reader__panel{padding-top:8px}.single-herbs-list-reader__controls{grid-template-columns:repeat(2,minmax(0,1fr));gap:6px}.single-herbs-list-reader__field{grid-column:1/-1;font-size:10px}.single-herbs-list-reader select,.single-herbs-list-reader button{min-height:34px;padding:5px 6px;font-size:11px;line-height:1.15}.single-herbs-list-reader__progress{grid-template-columns:1fr 1fr;gap:6px;margin-top:7px}.single-herbs-list-reader__progress-field{grid-column:1/-1;grid-row:1}.single-herbs-list-reader__status{margin-top:6px}.single-herbs-list__connections{gap:6px}.single-herbs-list__connection{min-height:34px;padding:5px 8px;font-size:11px}}
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
            <?php if ($fromformulatree) { ?>
                <a class="single-herbs-list__button" href="<?php echo $formulatreereturnurl->out(false); ?>"><?php echo $ises ? '← Volver al mapa visual de fórmulas' : '← Back to Formula Visual Tree'; ?></a>
            <?php } else if ($fromoriginmap) { ?>
                <a class="single-herbs-list__button" href="<?php echo $originmapreturnurl->out(false); ?>"><?php echo $ises ? '← Volver a sustancias por origen' : '← Back to medicinals by origin'; ?></a>
            <?php } else if ($fromcautions) { ?>
                <a class="single-herbs-list__button" href="<?php echo $cautionsreturnurl->out(false); ?>"><?php echo $ises ? '← Volver a Precauciones y contraindicaciones' : '← Back to Cautions & Contraindications'; ?></a>
            <?php } else if ($fromincompatibilities) { ?>
                <a class="single-herbs-list__button" href="<?php echo $incompatibilityreturnurl->out(false); ?>"><?php echo $ises ? '← Volver al capítulo de incompatibilidades' : '← Back to incompatibility chapter'; ?></a>
            <?php } else if ($fromsmart) { ?>
                <a class="single-herbs-list__button" href="<?php echo $smarttoolsurl->out(false); ?>"><?php echo $ises ? '← Volver a Smart Study Tools' : '← Back to Smart Study Tools'; ?></a>
            <?php } else if ($fromformula) { ?>
                <a class="single-herbs-list__button" href="<?php echo $formulabackurl->out(false); ?>"><?php echo ($ises ? '← Volver a ' : '← Back to ') . s($returnformula); ?></a>
            <?php } else if ($fromquiz) { ?>
                <button class="single-herbs-list__button" type="button" data-return-to-herb-question data-fallback-url="<?php echo $studyguideurl->out(false); ?>"><?php echo $ises ? '← Volver a la pregunta' : '← Back to question'; ?></button>
            <?php } else if ($fromchief) { ?>
                <a class="single-herbs-list__button" href="<?php echo $chiefherbsurl->out(false); ?>"><?php echo $ises ? '← Volver a Chief Herbs' : '← Back to Chief Herbs'; ?></a>
            <?php } else if ($fromingredients) { ?>
                <a class="single-herbs-list__button" href="<?php echo $formulaingredientsurl->out(false); ?>"><?php echo $ises ? '← Volver a Fórmulas e ingredientes' : '← Back to Formula and Ingredients'; ?></a>
            <?php } else { ?>
                <a class="single-herbs-list__button" href="<?php echo $studyguideurl->out(false); ?>"><?php echo $ises ? '← Single Herbs' : '← Single Herbs'; ?></a>
            <?php } ?>
            <?php if (!$fromlibrary) { ?>
                <a class="single-herbs-list__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
                <a class="single-herbs-list__button" href="<?php echo $dashboardurl->out(false); ?>"><?php echo $ises ? 'Guía de estudio' : 'Study Guide'; ?></a>
            <?php } ?>
        </div>
    </header>

    <section class="single-herbs-list-reader" data-herb-list-reader data-language="<?php echo $ises ? 'es-ES' : 'en-US'; ?>" hidden>
        <details class="single-herbs-list-reader__disclosure" open>
            <summary>
                <span>
                    <span class="single-herbs-list-reader__title"><?php echo $ises ? 'Escuchar lista de hierbas' : 'Listen to Single Herbs List'; ?></span>
                    <span class="single-herbs-list-reader__description"><?php echo $ises ? 'Elige dónde comenzar; después continuará automáticamente con las demás hierbas mostradas.' : 'Choose where to begin; it will then continue automatically through the remaining displayed herbs.'; ?></span>
                </span>
            </summary>
            <div class="single-herbs-list-reader__panel">
                <div class="single-herbs-list-reader__controls">
                    <label class="single-herbs-list-reader__field">
                        <span><?php echo $ises ? 'Voz' : 'Voice'; ?></span>
                        <select data-herb-list-voice aria-label="<?php echo $ises ? 'Voz de lectura' : 'Reading voice'; ?>"></select>
                    </label>
                    <label class="single-herbs-list-reader__field">
                        <span><?php echo $ises ? 'Comenzar en' : 'Start with'; ?></span>
                        <select data-herb-list-start aria-label="<?php echo $ises ? 'Primera hierba que se leerá' : 'First herb to read'; ?>"></select>
                    </label>
                    <button type="button" data-herb-list-read-displayed>▶ <?php echo $ises ? 'Escuchar desde aquí' : 'Listen from here'; ?></button>
                    <button type="button" data-herb-list-pause disabled>⏸ <?php echo $ises ? 'Pausar' : 'Pause'; ?></button>
                    <button type="button" data-herb-list-stop disabled>■ <?php echo $ises ? 'Detener' : 'Stop'; ?></button>
                </div>
                <div class="single-herbs-list-reader__progress">
                    <button type="button" data-herb-list-back disabled>↶ <?php echo $ises ? 'Retroceder' : 'Back'; ?></button>
                    <label class="single-herbs-list-reader__progress-field">
                        <span><?php echo $ises ? 'Progreso de lectura' : 'Reading progress'; ?></span>
                        <span data-herb-list-progress-value><?php echo $ises ? 'Sin lectura' : 'Not reading'; ?></span>
                        <input type="range" min="0" max="0" value="0" data-herb-list-progress disabled>
                    </label>
                    <button type="button" data-herb-list-forward disabled><?php echo $ises ? 'Avanzar' : 'Forward'; ?> ↷</button>
                </div>
                <p class="single-herbs-list-reader__status" data-herb-list-status aria-live="polite"><?php echo $ises ? 'Lista para escuchar.' : 'Ready to listen.'; ?></p>
            </div>
        </details>
    </section>

    <section class="single-herbs-list__controls" aria-label="<?php echo $ises ? 'Controles de la lista' : 'List controls'; ?>">
        <details class="single-herbs-list__controls-disclosure" open>
            <summary><?php echo $ises ? 'Buscar y organizar hierbas' : 'Search and organize herbs'; ?></summary>
            <div class="single-herbs-list__controls-panel">
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
            <?php if ($fromlibrary || $fromquiz || $fromchief || $fromingredients || $fromformula || $fromsmart || $fromcautions || $fromincompatibilities || $fromoriginmap || $fromformulatree) { ?><input type="hidden" name="from" value="<?php echo s($fromcontext); ?>"><?php } ?>
            <?php if ($fromformula) { ?><input type="hidden" name="returnformula" value="<?php echo s($returnformula); ?>"><?php } ?>
            <?php if ($fromformulatree) { ?><input type="hidden" name="treepage" value="<?php echo $treepage; ?>"><input type="hidden" name="treemode" value="<?php echo s($treemode); ?>"><?php } ?>
            <?php if ($fromsmart) { ?><input type="hidden" name="smartmode" value="<?php echo s($smartmode); ?>"><?php if ($smartitem !== '') { ?><input type="hidden" name="smartitem" value="<?php echo s($smartitem); ?>"><?php } ?><?php if ($smartq !== '') { ?><input type="hidden" name="smartq" value="<?php echo s($smartq); ?>"><?php } ?><?php } ?>
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
            </div>
        </details>
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
                        $cardsmartparams = ['mode' => 'herb', 'item' => $card['name']];
                        if ($ises) $cardsmartparams['lang'] = 'es';
                        $cardsmarturl = new moodle_url('/blocks/myprogress/smart-study-tools.php', $cardsmartparams);
                        $metaparts = $card['properties'] !== ''
                            ? array_values(array_filter([$card['properties'], $card['channels']]))
                            : array_values(array_filter([$card['flavor'], $card['temperature'], $card['channels']]));
                        $cardnarration = $buildcardnarration($card, $ises);
                    ?>
                        <details class="single-herbs-list__card" data-herb-read-text="<?php echo s($cardnarration); ?>" data-herb-read-label="<?php echo s($card['name']); ?>"<?php echo $autoopen ? ' open' : ''; ?>>
                            <summary>
                                <span class="single-herbs-list__name"><?php echo s($card['name']); ?>*</span>
                                <span class="single-herbs-list__latin"><?php echo s($card['latin']); ?></span>
                                <a class="single-herbs-list__category" data-herb-category-link href="<?php echo $cardcategoryurl->out(false); ?>"><?php echo s($card['categorylabel']); ?></a>
                                <?php if ($metaparts) { ?><span class="single-herbs-list__meta"><?php echo s(implode(' · ', $metaparts)); ?></span><?php } ?>
                            </summary>
                            <div class="single-herbs-list__body">
                                <dl>
                                    <?php if ($card['dose'] !== '') { ?><dt><?php echo $ises ? 'Dosis' : 'Dosage'; ?></dt><dd><?php echo s($card['dose']); ?></dd><?php } ?>
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
                                <nav class="single-herbs-list__connections" aria-label="<?php echo $ises ? 'Enlaces relacionados' : 'Related links'; ?>">
                                    <button class="single-herbs-list__connection single-herbs-list__connection--audio" type="button" data-herb-read-button><?php echo $ises ? '🔊 Escuchar esta hierba' : '🔊 Listen to this herb'; ?></button>
                                    <a class="single-herbs-list__connection" href="<?php echo $cardsmarturl->out(false); ?>"><?php echo $ises ? '✨ Encontrar hierbas similares' : '✨ Find similar herbs'; ?></a>
                                </nav>
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
