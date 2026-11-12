<?php
// Free, local similarity and discovery tools built only from verified guideline data.

require_once(__DIR__ . '/../../config.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
$mode = optional_param('mode', 'formula', PARAM_ALPHA);
$allowedmodes = ['formula', 'herb', 'clinical', 'tongue'];
$mode = in_array($mode, $allowedmodes, true) ? $mode : 'formula';
$item = trim(optional_param('item', '', PARAM_TEXT));
$query = trim(optional_param('q', '', PARAM_TEXT));

$pageparams = ['mode' => $mode];
if ($ises) {
    $pageparams['lang'] = 'es';
}
if ($item !== '') {
    $pageparams['item'] = $item;
}
if ($query !== '') {
    $pageparams['q'] = $query;
}
$pageurl = new moodle_url('/blocks/myprogress/smart-study-tools.php', $pageparams);
$languageparams = $pageparams;
if ($ises) {
    unset($languageparams['lang']);
} else {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/smart-study-tools.php', $languageparams);
$libraryparams = ['area' => 'herbology', 'view' => 'library'];
if ($ises) {
    $libraryparams['lang'] = 'es';
}
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', $libraryparams);

$formularosterpath = __DIR__ . '/formula-board-roster-2026.json';
$formulaprofilespath = __DIR__ . '/formulas-study-guide-data.json';
$formulaadditionspath = __DIR__ . '/formula-guideline-profile-additions.json';
$tonguepulsepath = __DIR__ . '/formula-tongue-pulse-data.json';
$herbindexpath = __DIR__ . '/single-herb-card-index.json';
$herbpropertiespath = __DIR__ . '/single-herb-properties.json';
$herbrichpath = __DIR__ . '/single-herb-rich-profiles.json';
$herbactionspath = __DIR__ . '/single-herb-actions-indications-supplement.json';

$formularoster = is_readable($formularosterpath) ? json_decode(file_get_contents($formularosterpath), true) : null;
$formulaprofiles = is_readable($formulaprofilespath) ? json_decode(file_get_contents($formulaprofilespath), true) : null;
$formulaadditions = is_readable($formulaadditionspath) ? json_decode(file_get_contents($formulaadditionspath), true) : null;
$tonguepulse = is_readable($tonguepulsepath) ? json_decode(file_get_contents($tonguepulsepath), true) : null;
$herbindex = is_readable($herbindexpath) ? json_decode(file_get_contents($herbindexpath), true) : null;
$herbproperties = is_readable($herbpropertiespath) ? json_decode(file_get_contents($herbpropertiespath), true) : null;
$herbrich = is_readable($herbrichpath) ? json_decode(file_get_contents($herbrichpath), true) : null;
$herbactions = is_readable($herbactionspath) ? json_decode(file_get_contents($herbactionspath), true) : null;

if (!is_array($formularoster) || !is_array($formulaprofiles) || !is_array($formulaadditions)
        || !is_array($tonguepulse) || !is_array($herbindex) || !is_array($herbproperties)
        || !is_array($herbrich) || !is_array($herbactions)
        || count($formularoster['formulas'] ?? []) !== 181
        || count($formulaprofiles['formulas'] ?? []) !== 63
        || count($formulaprofiles['supplementalFormulas'] ?? []) !== 227
        || count($tonguepulse['formulas'] ?? []) !== 63
        || count($herbindex['herbs'] ?? []) !== 304
        || count($herbproperties['herbs'] ?? []) !== 304
        || count($herbrich['profiles'] ?? []) < 180) {
    throw new coding_exception('Smart Study Tools guideline data is unavailable.');
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
$detailtext = static function(array $profile, string $field, bool $ises): string {
    if ($ises) {
        $spanish = trim((string)($profile[$field . 'Es'] ?? ''));
        if ($spanish !== '') {
            return $spanish;
        }
    }
    return trim((string)($profile[$field] ?? ''));
};
$stops = array_fill_keys(explode(' ', 'a an and are as at be by con de del el en es for from la las los of on or para por que se the to un una y with formulas formula herbs herb tang san wan yin qi'), true);
$tokenize = static function(string $value) use ($normalize, $stops): array {
    $tokens = preg_split('/\s+/', $normalize($value), -1, PREG_SPLIT_NO_EMPTY);
    $set = [];
    foreach ($tokens ?: [] as $token) {
        if (strlen($token) > 1 && !isset($stops[$token])) {
            $set[$token] = true;
        }
    }
    return $set;
};
$fieldscore = static function(string $left, string $right) use ($tokenize): float {
    $a = $tokenize($left);
    $b = $tokenize($right);
    if (!$a || !$b) {
        return 0.0;
    }
    $intersection = count(array_intersect_key($a, $b));
    $union = count($a + $b);
    return $union > 0 ? $intersection / $union : 0.0;
};
$queryscore = static function(string $queryvalue, string $text) use ($tokenize, $normalize): float {
    $querytokens = $tokenize($queryvalue);
    if (!$querytokens) {
        return 0.0;
    }
    $texttokens = $tokenize($text);
    $coverage = count(array_intersect_key($querytokens, $texttokens)) / count($querytokens);
    $exact = core_text::strpos($normalize($text), $normalize($queryvalue)) !== false ? 0.25 : 0.0;
    return min(1.0, $coverage * 0.75 + $exact);
};

$profilemap = [];
foreach (array_merge(
    $formulaprofiles['formulas'] ?? [],
    $formulaprofiles['supplementalFormulas'] ?? [],
    $formulaadditions['formulas'] ?? []
) as $profile) {
    $name = trim((string)($profile['name'] ?? ''));
    if ($name !== '') {
        $profilemap[$normalize($name)] = $profile;
    }
}

$formulas = [];
foreach ($formularoster['formulas'] as $position => $official) {
    $name = trim((string)($official['name'] ?? ''));
    if ($name === '') {
        continue;
    }
    $profile = $profilemap[$normalize($name)] ?? [];
    $category = $official['benskyCategory'] ?? [];
    $categoryname = trim((string)($category[$ises ? 'es' : 'en'] ?? $category['en'] ?? ''));
    $formulas[$normalize($name)] = [
        'id' => (int)($official['id'] ?? ($position + 1)),
        'name' => $name,
        'english' => trim((string)($official['englishName'] ?? '')),
        'category' => $categoryname,
        'categorycode' => trim((string)($category['code'] ?? '')),
        'pattern' => $detailtext($profile, 'pattern', $ises),
        'action' => $detailtext($profile, 'action', $ises),
        'clue' => $detailtext($profile, 'clue', $ises),
        'examfocus' => $detailtext($profile, 'examFocus', $ises),
        'ingredients' => $detailtext($profile, 'ingredients', $ises),
        'kings' => trim((string)($profile['kings'] ?? '')),
    ];
}
uasort($formulas, static fn(array $left, array $right): int => strnatcasecmp($left['name'], $right['name']));

$propertymap = [];
foreach ($herbproperties['herbs'] as $profile) {
    $name = trim((string)($profile['name'] ?? ''));
    if ($name !== '') {
        $propertymap[$normalize($name)] = $profile;
    }
}
$richmap = [];
foreach ($herbrich['profiles'] as $profile) {
    $name = trim((string)($profile['name'] ?? ''));
    if ($name !== '') {
        $richmap[$normalize($name)] = $profile;
    }
}
$actionmap = [];
foreach ($herbactions['herbs'] ?? [] as $name => $profile) {
    $actionmap[$normalize((string)$name)] = $profile;
}
$pairtext = static function($profile, bool $ises, int $index): string {
    if (!is_array($profile)) {
        return '';
    }
    $rows = $profile[$ises ? 'es' : 'en'] ?? $profile['en'] ?? [];
    $values = [];
    foreach (is_array($rows) ? $rows : [] as $row) {
        $value = is_array($row) ? trim((string)($row[$index] ?? '')) : '';
        if ($value !== '') {
            $values[] = $value;
        }
    }
    return implode('; ', array_values(array_unique($values)));
};
$herbs = [];
foreach ($herbindex['herbs'] as $position => $official) {
    $name = trim((string)($official['name'] ?? ''));
    if ($name === '') {
        continue;
    }
    $key = $normalize($name);
    $property = $propertymap[$key] ?? [];
    $rich = $richmap[$key] ?? [];
    $action = $actionmap[$key] ?? [];
    $category = $official['benskyCategory'] ?? [];
    $identity = $localized($rich['identity'] ?? '', $ises);
    if ($identity === '') {
        $identity = $localized($property['actionSummary'] ?? '', $ises);
    }
    if ($identity === '') {
        $identity = $pairtext($action, $ises, 0);
    }
    $clue = $localized($rich['clue'] ?? '', $ises);
    if ($clue === '') {
        $clue = $pairtext($action, $ises, 1);
    }
    $herbs[$key] = [
        'id' => (int)($official['id'] ?? ($position + 1)),
        'name' => $name,
        'latin' => trim((string)($official['latin'] ?? $property['latin'] ?? '')),
        'category' => trim((string)($category[$ises ? 'es' : 'en'] ?? $category['en'] ?? '')),
        'categorycode' => trim((string)($category['code'] ?? '')),
        'flavor' => $localized($property['flavor'] ?? '', $ises),
        'temperature' => $localized($property['temperature'] ?? '', $ises),
        'channels' => $localized($rich['channels'] ?? ($property['channels'] ?? ''), $ises),
        'properties' => $localized($rich['properties'] ?? '', $ises),
        'identity' => $identity,
        'clue' => $clue,
    ];
}
uasort($herbs, static fn(array $left, array $right): int => strnatcasecmp($left['name'], $right['name']));

$tonguerows = [];
$officialformula = [];
foreach ($formularoster['formulas'] as $formula) {
    $officialformula[$normalize((string)($formula['name'] ?? ''))] = trim((string)($formula['name'] ?? ''));
}
$aliases = ['jin gui shen qi wan' => 'shen qi wan'];
foreach ($tonguepulse['formulas'] as $finding) {
    $source = trim((string)($finding['name'] ?? ''));
    $sourcekey = $normalize($source);
    $officialkey = $aliases[$sourcekey] ?? $sourcekey;
    if (!isset($officialformula[$officialkey])) {
        continue;
    }
    $name = $officialformula[$officialkey];
    $formula = $formulas[$normalize($name)] ?? [];
    $tonguerows[] = [
        'name' => $name,
        'category' => (string)($formula['category'] ?? ''),
        'tongue' => trim((string)($finding['tongue'] ?? '')),
        'pulse' => trim((string)($finding['pulse'] ?? '')),
    ];
}

$formularesults = [];
$herbresults = [];
$searchresults = [];
if ($mode === 'formula' && $item !== '' && isset($formulas[$normalize($item)])) {
    $source = $formulas[$normalize($item)];
    foreach ($formulas as $candidate) {
        if ($candidate['name'] === $source['name']) {
            continue;
        }
        $categorymatch = $source['categorycode'] !== '' && $source['categorycode'] === $candidate['categorycode'] ? 1.0 : 0.0;
        $scores = [
            'category' => $categorymatch,
            'pattern' => $fieldscore($source['pattern'], $candidate['pattern']),
            'action' => $fieldscore($source['action'], $candidate['action']),
            'ingredients' => $fieldscore($source['ingredients'], $candidate['ingredients']),
            'chief' => $fieldscore($source['kings'], $candidate['kings']),
            'clue' => $fieldscore($source['clue'] . ' ' . $source['examfocus'], $candidate['clue'] . ' ' . $candidate['examfocus']),
        ];
        $score = $scores['category'] * 0.28 + $scores['pattern'] * 0.22 + $scores['action'] * 0.20
            + $scores['ingredients'] * 0.14 + $scores['chief'] * 0.10 + $scores['clue'] * 0.06;
        if ($score <= 0.01) {
            continue;
        }
        $reasons = [];
        if ($scores['category'] > 0) $reasons[] = $ises ? 'Misma categoría Bensky' : 'Same Bensky category';
        if ($scores['pattern'] >= 0.12) $reasons[] = $ises ? 'Patrón relacionado' : 'Related pattern';
        if ($scores['action'] >= 0.12) $reasons[] = $ises ? 'Acciones relacionadas' : 'Related actions';
        if ($scores['ingredients'] >= 0.10) $reasons[] = $ises ? 'Ingredientes superpuestos' : 'Overlapping ingredients';
        if ($scores['chief'] >= 0.10) $reasons[] = $ises ? 'Chief herbs relacionados' : 'Related Chief herbs';
        $formularesults[] = ['score' => $score, 'card' => $candidate, 'reasons' => $reasons];
    }
    usort($formularesults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $formularesults = array_slice($formularesults, 0, 6);
} else if ($mode === 'herb' && $item !== '' && isset($herbs[$normalize($item)])) {
    $source = $herbs[$normalize($item)];
    foreach ($herbs as $candidate) {
        if ($candidate['name'] === $source['name']) {
            continue;
        }
        $scores = [
            'category' => $source['categorycode'] !== '' && $source['categorycode'] === $candidate['categorycode'] ? 1.0 : 0.0,
            'properties' => $fieldscore($source['properties'] . ' ' . $source['flavor'] . ' ' . $source['temperature'], $candidate['properties'] . ' ' . $candidate['flavor'] . ' ' . $candidate['temperature']),
            'channels' => $fieldscore($source['channels'], $candidate['channels']),
            'action' => $fieldscore($source['identity'], $candidate['identity']),
            'clue' => $fieldscore($source['clue'], $candidate['clue']),
        ];
        $score = $scores['category'] * 0.32 + $scores['properties'] * 0.18 + $scores['channels'] * 0.15
            + $scores['action'] * 0.25 + $scores['clue'] * 0.10;
        if ($score <= 0.01) continue;
        $reasons = [];
        if ($scores['category'] > 0) $reasons[] = $ises ? 'Misma categoría Bensky' : 'Same Bensky category';
        if ($scores['action'] >= 0.12) $reasons[] = $ises ? 'Acciones relacionadas' : 'Related actions';
        if ($scores['channels'] >= 0.20) $reasons[] = $ises ? 'Canales compartidos' : 'Shared channels';
        if ($scores['properties'] >= 0.12) $reasons[] = $ises ? 'Propiedades similares' : 'Similar properties';
        $herbresults[] = ['score' => $score, 'card' => $candidate, 'reasons' => $reasons];
    }
    usort($herbresults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $herbresults = array_slice($herbresults, 0, 6);
} else if ($mode === 'clinical' && $query !== '') {
    foreach ($formulas as $candidate) {
        $text = implode(' ', [$candidate['category'], $candidate['pattern'], $candidate['action'], $candidate['clue'], $candidate['examfocus'], $candidate['ingredients'], $candidate['kings']]);
        $score = $queryscore($query, $text);
        if ($score > 0) $searchresults[] = ['score' => $score, 'card' => $candidate];
    }
    usort($searchresults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $searchresults = array_slice($searchresults, 0, 12);
} else if ($mode === 'tongue' && $query !== '') {
    foreach ($tonguerows as $candidate) {
        $score = $queryscore($query, $candidate['tongue'] . ' ' . $candidate['pulse'] . ' ' . $candidate['category']);
        if ($score > 0) $searchresults[] = ['score' => $score, 'card' => $candidate];
    }
    usort($searchresults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $searchresults = array_slice($searchresults, 0, 12);
}

$returnparams = ['mode' => $mode];
if ($item !== '') $returnparams['item'] = $item;
if ($query !== '') $returnparams['q'] = $query;
if ($ises) $returnparams['lang'] = 'es';
$formulalink = static function(string $name) use ($returnparams): moodle_url {
    $params = [
        'formula' => $name,
        'sort' => 'az',
        'from' => 'smart',
        'smartmode' => $returnparams['mode'],
    ];
    if (isset($returnparams['item'])) $params['smartitem'] = $returnparams['item'];
    if (isset($returnparams['q'])) $params['smartq'] = $returnparams['q'];
    if (isset($returnparams['lang'])) $params['lang'] = 'es';
    return new moodle_url('/blocks/myprogress/formula-list.php', $params);
};
$herblink = static function(string $name) use ($returnparams): moodle_url {
    $params = [
        'herb' => $name,
        'sort' => 'az',
        'from' => 'smart',
        'smartmode' => $returnparams['mode'],
    ];
    if (isset($returnparams['item'])) $params['smartitem'] = $returnparams['item'];
    if (isset($returnparams['q'])) $params['smartq'] = $returnparams['q'];
    if (isset($returnparams['lang'])) $params['lang'] = 'es';
    return new moodle_url('/blocks/myprogress/single-herbs-list.php', $params);
};

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Herramientas de estudio inteligentes' : 'Smart Study Tools');
$PAGE->set_heading($ises ? 'Herramientas de estudio inteligentes' : 'Smart Study Tools');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
?>
<style>
.smart-tools{width:min(1280px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}
.smart-tools__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:26px;border:1px solid #d8c8ed;border-top:6px solid #6d28d9;border-radius:15px;background:linear-gradient(135deg,#faf7ff,#fff9ed)}
.smart-tools__eyebrow{margin:0 0 7px;color:#6d28d9;font-size:12px;font-weight:950;letter-spacing:.09em;text-transform:uppercase}.smart-tools h2{margin:0 0 9px;font-size:clamp(30px,4vw,44px)}.smart-tools__lead{max-width:820px;margin:0;color:#526b84;line-height:1.55}.smart-tools__actions,.smart-tools__tabs{display:flex;flex-wrap:wrap;gap:9px}
.smart-tools__button,.smart-tools__tab{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}.smart-tools__button:hover,.smart-tools__tab:hover{background:#eef7f5}.smart-tools__tab.is-active{border-color:#6d28d9;background:#6d28d9;color:#fff!important}
.smart-tools__tabs{margin-bottom:18px}.smart-tools__panel{padding:20px;border:1px solid #d6dfe5;border-radius:13px;background:#fff;box-shadow:0 5px 18px rgba(23,50,77,.05)}.smart-tools__panel h3{margin:0 0 7px;font-size:24px}.smart-tools__panel>p{margin:0 0 16px;color:#526b84}
.smart-tools__form{display:grid;grid-template-columns:minmax(250px,1fr) auto;gap:11px;align-items:end}.smart-tools__form label{display:grid;gap:6px;font-size:12px;font-weight:900}.smart-tools__form select,.smart-tools__form input{width:100%;min-height:48px;padding:9px 12px;border:1px solid #aebfca;border-radius:8px;background:#fff;color:#102a43;font:inherit}
.smart-tools__notice{margin:18px 0 0;padding:13px 15px;border-left:5px solid #6d28d9;border-radius:9px;background:#f5f0ff;color:#4c1d95}.smart-tools__results-heading{display:flex;align-items:baseline;justify-content:space-between;gap:16px;margin:26px 0 13px}.smart-tools__results-heading h3{margin:0;font-size:24px}.smart-tools__results-heading span{color:#526b84;font-weight:800}
.smart-tools__results{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.smart-tools__result{padding:18px;border:1px solid #cbd9df;border-top:5px solid #087c69;border-radius:12px;background:#fff}.smart-tools__result-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.smart-tools__result h4{margin:0;font-size:20px}.smart-tools__score{flex:0 0 auto;padding:5px 9px;border-radius:999px;background:#e8f7f1;color:#075f50;font-size:12px;font-weight:950}.smart-tools__category{margin:5px 0 10px;color:#6d28d9;font-size:12px;font-weight:850}.smart-tools__result dl{display:grid;grid-template-columns:105px 1fr;gap:8px 10px;margin:0}.smart-tools__result dt{font-size:12px;font-weight:900}.smart-tools__result dd{margin:0;color:#52657a;font-size:13px;line-height:1.5}.smart-tools__reason{display:flex;flex-wrap:wrap;gap:6px;margin:12px 0}.smart-tools__reason span{padding:4px 8px;border-radius:999px;background:#fff1d6;color:#7c4308;font-size:11px;font-weight:850}.smart-tools__open{display:inline-flex;min-height:40px;align-items:center;padding:8px 12px;border-radius:8px;background:#0d7868;color:#fff!important;font-weight:850;text-decoration:none!important}.smart-tools__empty{margin-top:22px;padding:26px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;text-align:center;color:#526b84;font-weight:800}
.smart-tools__method{margin-top:22px;padding:15px;border-left:5px solid #e9a11b;border-radius:9px;background:#fff8e9;color:#71420c;line-height:1.5}
@media(max-width:800px){.smart-tools__hero{display:block}.smart-tools__actions{margin-top:16px}.smart-tools__form,.smart-tools__results{grid-template-columns:1fr}.smart-tools__result dl{grid-template-columns:1fr}.smart-tools__result dd{margin-top:-4px}}
</style>
<main class="smart-tools">
    <header class="smart-tools__hero">
        <div>
            <p class="smart-tools__eyebrow"><?php echo $ises ? 'GRATIS · MOTOR LOCAL · GUIDELINE VERIFICADO' : 'FREE · LOCAL ENGINE · VERIFIED GUIDELINE'; ?></p>
            <h2><?php echo $ises ? 'Herramientas de estudio inteligentes' : 'Smart Study Tools'; ?></h2>
            <p class="smart-tools__lead"><?php echo $ises
                ? 'Encuentra relaciones dentro de las 181 fórmulas y 304 hierbas oficiales. El análisis se ejecuta con nuestra propia base de datos, sin API de pago y sin generar contenido clínico nuevo.'
                : 'Discover relationships across the 181 official formulas and 304 official herbs. Analysis runs on our own database, without a paid API and without generating new clinical content.'; ?></p>
        </div>
        <div class="smart-tools__actions">
            <a class="smart-tools__button" href="<?php echo $libraryurl->out(false); ?>"><?php echo $ises ? '← Main Study Guide' : '← Main Study Guide'; ?></a>
            <a class="smart-tools__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
        </div>
    </header>

    <nav class="smart-tools__tabs" aria-label="<?php echo $ises ? 'Herramientas disponibles' : 'Available tools'; ?>">
        <?php
        $tabs = [
            'formula' => $ises ? '✨ Fórmulas similares' : '✨ Similar Formulas',
            'herb' => $ises ? '🌿 Hierbas similares' : '🌿 Similar Herbs',
            'clinical' => $ises ? '🔎 Patrón y pista clínica' : '🔎 Pattern & Clinical Clue',
            'tongue' => $ises ? '👅 Lengua y pulso' : '👅 Tongue & Pulse',
        ];
        foreach ($tabs as $tabmode => $label) {
            $params = ['mode' => $tabmode];
            if ($ises) $params['lang'] = 'es';
            $taburl = new moodle_url('/blocks/myprogress/smart-study-tools.php', $params);
        ?>
            <a class="smart-tools__tab<?php echo $mode === $tabmode ? ' is-active' : ''; ?>" href="<?php echo $taburl->out(false); ?>"<?php echo $mode === $tabmode ? ' aria-current="page"' : ''; ?>><?php echo s($label); ?></a>
        <?php } ?>
    </nav>

    <section class="smart-tools__panel">
        <?php if ($mode === 'formula') { ?>
            <h3><?php echo $ises ? 'Encontrar fórmulas similares' : 'Find Similar Formulas'; ?></h3>
            <p><?php echo $ises ? 'Compara categoría, patrón, acción, ingredientes, Chief herbs y pista decisiva.' : 'Compare category, pattern, action, ingredients, Chief herbs and decisive clues.'; ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="formula"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo $ises ? 'Fórmula de origen' : 'Source formula'; ?></span><select name="item" required><option value=""><?php echo $ises ? 'Seleccionar fórmula…' : 'Select a formula…'; ?></option><?php foreach ($formulas as $formula) { ?><option value="<?php echo s($formula['name']); ?>"<?php echo $normalize($item) === $normalize($formula['name']) ? ' selected' : ''; ?>><?php echo s($formula['name']); ?></option><?php } ?></select></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Encontrar similares' : 'Find similar'; ?></button>
            </form>
        <?php } else if ($mode === 'herb') { ?>
            <h3><?php echo $ises ? 'Encontrar hierbas similares' : 'Find Similar Herbs'; ?></h3>
            <p><?php echo $ises ? 'Compara categoría Bensky, propiedades, canales, acciones e indicaciones.' : 'Compare Bensky category, properties, channels, actions and indications.'; ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="herb"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo $ises ? 'Hierba de origen' : 'Source herb'; ?></span><select name="item" required><option value=""><?php echo $ises ? 'Seleccionar hierba…' : 'Select an herb…'; ?></option><?php foreach ($herbs as $herb) { ?><option value="<?php echo s($herb['name']); ?>"<?php echo $normalize($item) === $normalize($herb['name']) ? ' selected' : ''; ?>><?php echo s($herb['name']); ?></option><?php } ?></select></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Encontrar similares' : 'Find similar'; ?></button>
            </form>
        <?php } else if ($mode === 'clinical') { ?>
            <h3><?php echo $ises ? 'Buscador por patrón y pista clínica' : 'Pattern & Clinical Clue Finder'; ?></h3>
            <p><?php echo $ises ? 'Describe una pista de estudio; se buscará únicamente dentro de perfiles verificados.' : 'Enter a study clue; only verified profiles are searched.'; ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="clinical"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo $ises ? 'Patrón, acción o pista' : 'Pattern, action or clue'; ?></span><input type="search" name="q" value="<?php echo s($query); ?>" required placeholder="<?php echo $ises ? 'Ej.: tos seca, sudores nocturnos, esputo con sangre' : 'Example: dry cough, night sweats, blood-streaked sputum'; ?>"></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Buscar' : 'Search'; ?></button>
            </form>
        <?php } else { ?>
            <h3><?php echo $ises ? 'Coincidencia por lengua y pulso' : 'Tongue & Pulse Matcher'; ?></h3>
            <p><?php echo $ises ? 'Busca entre las 63 fórmulas oficiales que cuentan con hallazgos verificados de lengua y pulso.' : 'Search the 63 official formulas with verified tongue and pulse findings.'; ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="tongue"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo $ises ? 'Hallazgos de lengua o pulso' : 'Tongue or pulse findings'; ?></span><input type="search" name="q" value="<?php echo s($query); ?>" required placeholder="<?php echo $ises ? 'Ej.: lengua roja, pulso fino y rápido' : 'Example: red tongue, thin rapid pulse'; ?>"></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Buscar' : 'Search'; ?></button>
            </form>
        <?php } ?>
        <p class="smart-tools__notice"><?php echo $ises
            ? 'El porcentaje indica similitud educativa dentro de nuestra base; no equivale a una recomendación clínica ni sustituye el diagnóstico profesional.'
            : 'The percentage represents educational similarity inside our database; it is not a clinical recommendation and does not replace professional diagnosis.'; ?></p>
    </section>

    <?php
    $resultcount = $mode === 'formula' ? count($formularesults) : ($mode === 'herb' ? count($herbresults) : count($searchresults));
    if (($item !== '' || $query !== '')) {
    ?>
        <header class="smart-tools__results-heading"><h3><?php echo $ises ? 'Resultados relacionados' : 'Related results'; ?></h3><span><?php echo $resultcount; ?> <?php echo $ises ? 'resultados' : 'results'; ?></span></header>
        <?php if ($resultcount === 0) { ?>
            <p class="smart-tools__empty"><?php echo $ises ? 'No se encontraron coincidencias verificadas. Prueba con términos más generales.' : 'No verified matches were found. Try broader terms.'; ?></p>
        <?php } else { ?>
            <div class="smart-tools__results">
                <?php if ($mode === 'formula') { foreach ($formularesults as $result) { $card = $result['card']; ?>
                    <article class="smart-tools__result">
                        <header class="smart-tools__result-head"><h4><?php echo s($card['name']); ?></h4><span class="smart-tools__score"><?php echo (int)round($result['score'] * 100); ?>%</span></header>
                        <p class="smart-tools__category"><?php echo s($card['category']); ?></p>
                        <dl><?php if ($card['pattern'] !== '') { ?><dt><?php echo $ises ? 'Patrón' : 'Pattern'; ?></dt><dd><?php echo s($card['pattern']); ?></dd><?php } ?><?php if ($card['action'] !== '') { ?><dt><?php echo $ises ? 'Acción' : 'Action'; ?></dt><dd><?php echo s($card['action']); ?></dd><?php } ?><?php if ($card['clue'] !== '') { ?><dt><?php echo $ises ? 'Diferencia clave' : 'Key distinction'; ?></dt><dd><?php echo s($card['clue']); ?></dd><?php } ?></dl>
                        <?php if ($result['reasons']) { ?><div class="smart-tools__reason"><?php foreach ($result['reasons'] as $reason) { ?><span><?php echo s($reason); ?></span><?php } ?></div><?php } ?>
                        <a class="smart-tools__open" href="<?php echo $formulalink($card['name'])->out(false); ?>"><?php echo $ises ? 'Abrir tarjeta de fórmula →' : 'Open formula card →'; ?></a>
                    </article>
                <?php }} else if ($mode === 'herb') { foreach ($herbresults as $result) { $card = $result['card']; ?>
                    <article class="smart-tools__result">
                        <header class="smart-tools__result-head"><h4><?php echo s($card['name']); ?></h4><span class="smart-tools__score"><?php echo (int)round($result['score'] * 100); ?>%</span></header>
                        <p class="smart-tools__category"><?php echo s($card['category']); ?></p>
                        <dl><?php if ($card['identity'] !== '') { ?><dt><?php echo $ises ? 'Acción' : 'Action'; ?></dt><dd><?php echo s($card['identity']); ?></dd><?php } ?><?php if ($card['clue'] !== '') { ?><dt><?php echo $ises ? 'Indicaciones' : 'Indications'; ?></dt><dd><?php echo s($card['clue']); ?></dd><?php } ?><?php if ($card['channels'] !== '') { ?><dt><?php echo $ises ? 'Canales' : 'Channels'; ?></dt><dd><?php echo s($card['channels']); ?></dd><?php } ?></dl>
                        <?php if ($result['reasons']) { ?><div class="smart-tools__reason"><?php foreach ($result['reasons'] as $reason) { ?><span><?php echo s($reason); ?></span><?php } ?></div><?php } ?>
                        <a class="smart-tools__open" href="<?php echo $herblink($card['name'])->out(false); ?>"><?php echo $ises ? 'Abrir tarjeta de hierba →' : 'Open herb card →'; ?></a>
                    </article>
                <?php }} else { foreach ($searchresults as $result) { $card = $result['card']; ?>
                    <article class="smart-tools__result">
                        <header class="smart-tools__result-head"><h4><?php echo s($card['name']); ?></h4><span class="smart-tools__score"><?php echo (int)round($result['score'] * 100); ?>%</span></header>
                        <p class="smart-tools__category"><?php echo s($card['category']); ?></p>
                        <dl>
                            <?php if ($mode === 'clinical') { ?>
                                <?php if ($card['pattern'] !== '') { ?><dt><?php echo $ises ? 'Patrón' : 'Pattern'; ?></dt><dd><?php echo s($card['pattern']); ?></dd><?php } ?>
                                <?php if ($card['action'] !== '') { ?><dt><?php echo $ises ? 'Acción' : 'Action'; ?></dt><dd><?php echo s($card['action']); ?></dd><?php } ?>
                                <?php if ($card['clue'] !== '') { ?><dt><?php echo $ises ? 'Pista' : 'Clue'; ?></dt><dd><?php echo s($card['clue']); ?></dd><?php } ?>
                            <?php } else { ?>
                                <dt><?php echo $ises ? 'Lengua' : 'Tongue'; ?></dt><dd><?php echo s($card['tongue']); ?></dd>
                                <dt><?php echo $ises ? 'Pulso' : 'Pulse'; ?></dt><dd><?php echo s($card['pulse']); ?></dd>
                            <?php } ?>
                        </dl>
                        <div class="smart-tools__reason"><span><?php echo $ises ? 'Coincidencia con la búsqueda' : 'Search match'; ?></span></div>
                        <a class="smart-tools__open" href="<?php echo $formulalink($card['name'])->out(false); ?>"><?php echo $ises ? 'Abrir tarjeta de fórmula →' : 'Open formula card →'; ?></a>
                    </article>
                <?php }} ?>
            </div>
        <?php } ?>
    <?php } ?>

    <aside class="smart-tools__method"><strong><?php echo $ises ? 'Cómo funciona:' : 'How it works:'; ?></strong> <?php echo $ises
        ? 'la puntuación pondera categoría, patrón, acción, ingredientes, Chief herbs, propiedades, canales y pistas verificadas. El sistema no llama a ChatGPT ni a servicios externos.'
        : 'the score weights category, pattern, action, ingredients, Chief herbs, properties, channels and verified clues. The system does not call ChatGPT or any external service.'; ?></aside>
</main>
<?php
echo $OUTPUT->footer();
