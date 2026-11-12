<?php
// Free, local similarity and discovery tools built only from verified guideline data.

require_once(__DIR__ . '/../../config.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
$mode = optional_param('mode', 'formula', PARAM_ALPHA);
$allowedmodes = ['formula', 'herb', 'pattern', 'clinical', 'clue', 'tongue', 'ingredients', 'chief', 'dui', 'safety', 'traps', 'quiz'];
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
$duiyaourl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'dui-yao-es.html' : 'dui-yao.html'));
$nametrapsurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'name-traps-es.html' : 'name-traps.html'));
$formulanametrapsurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'formula-name-traps-es.html' : 'formula-name-traps.html'));
$personalizedquizurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'formula-practice-es.html' : 'formula-practice.html'), ['focus' => 'weak']);
$safetylibraryurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'safety-es.html' : 'safety.html'));
$incompatibilitiesurl = new moodle_url('/study-guide/comparisons/' . ($ises ? 'herbal-incompatibilities-es.html' : 'herbal-incompatibilities.html'));
$herbcategorypageids = [
    'warm-acrid-release-exterior' => 'disperse-wind-cold',
    'cool-acrid-release-exterior' => 'disperse-wind-heat',
    'clear-heat-drain-fire' => 'clear-and-purge-heat',
    'cool-blood' => 'clear-blood-heat',
    'clear-heat-dry-dampness' => 'clear-damp-heat',
    'clear-heat-resolve-toxicity' => 'clear-toxic-heat',
    'clear-deficiency-heat' => 'clear-deficient-heat',
    'downward-draining-purgatives' => 'purgatives',
    'moist-laxatives' => 'laxatives',
    'harsh-expellants' => 'cathartics',
    'drain-dampness' => 'diuretics',
    'dispel-wind-dampness' => 'dispel-wind-damp',
    'cool-transform-phlegm-heat' => 'dissolve-hot-phlegm',
    'warm-transform-cold-phlegm' => 'dissolve-cold-phlegm',
    'stop-coughing-calm-wheezing' => 'stop-cough-wheezing',
    'aromatic-transform-dampness' => 'aromatics-for-damp',
    'relieve-food-stagnation' => 'digestives',
    'regulate-qi' => 'regulate-qi',
    'stop-bleeding' => 'stop-bleeding',
    'invigorate-blood' => 'activate-blood',
    'warm-interior-expel-cold' => 'warm-interior',
    'tonify-qi' => 'qi-tonics',
    'tonify-blood' => 'blood-tonics',
    'tonify-yang' => 'yang-tonics',
    'tonify-yin' => 'yin-tonics',
    'stabilize-bind' => 'astringents',
    'anchor-settle-calm-spirit' => 'anchor-calm-shen',
    'nourish-heart-calm-spirit' => 'nourish-calm-shen',
    'open-orifices' => 'orifice-opening-aromatics',
    'extinguish-wind-stop-tremors' => 'extinguish-liver-wind',
    'expel-parasites' => 'expel-parasites',
    'external-application' => 'external-use',
];

$formularosterpath = __DIR__ . '/formula-board-roster-2026.json';
$formulaprofilespath = __DIR__ . '/formulas-study-guide-data.json';
$formulaadditionspath = __DIR__ . '/formula-guideline-profile-additions.json';
$tonguepulsepath = __DIR__ . '/formula-tongue-pulse-data.json';
$herbindexpath = __DIR__ . '/single-herb-card-index.json';
$herbpropertiespath = __DIR__ . '/single-herb-properties.json';
$herbrichpath = __DIR__ . '/single-herb-rich-profiles.json';
$herbactionspath = __DIR__ . '/single-herb-actions-indications-supplement.json';
$herbsafetypath = __DIR__ . '/single-herb-safety-supplement.json';
$duiyaodatapath = __DIR__ . '/dui-yao-smart-data.json';

$formularoster = is_readable($formularosterpath) ? json_decode(file_get_contents($formularosterpath), true) : null;
$formulaprofiles = is_readable($formulaprofilespath) ? json_decode(file_get_contents($formulaprofilespath), true) : null;
$formulaadditions = is_readable($formulaadditionspath) ? json_decode(file_get_contents($formulaadditionspath), true) : null;
$tonguepulse = is_readable($tonguepulsepath) ? json_decode(file_get_contents($tonguepulsepath), true) : null;
$herbindex = is_readable($herbindexpath) ? json_decode(file_get_contents($herbindexpath), true) : null;
$herbproperties = is_readable($herbpropertiespath) ? json_decode(file_get_contents($herbpropertiespath), true) : null;
$herbrich = is_readable($herbrichpath) ? json_decode(file_get_contents($herbrichpath), true) : null;
$herbactions = is_readable($herbactionspath) ? json_decode(file_get_contents($herbactionspath), true) : null;
$herbsafety = is_readable($herbsafetypath) ? json_decode(file_get_contents($herbsafetypath), true) : null;
$duiyaodata = is_readable($duiyaodatapath) ? json_decode(file_get_contents($duiyaodatapath), true) : null;

if (!is_array($formularoster) || !is_array($formulaprofiles) || !is_array($formulaadditions)
        || !is_array($tonguepulse) || !is_array($herbindex) || !is_array($herbproperties)
        || !is_array($herbrich) || !is_array($herbactions) || !is_array($herbsafety) || !is_array($duiyaodata)
        || count($formularoster['formulas'] ?? []) !== 181
        || count($formulaprofiles['formulas'] ?? []) !== 63
        || count($formulaprofiles['supplementalFormulas'] ?? []) !== 227
        || count($tonguepulse['formulas'] ?? []) !== 63
        || count($herbindex['herbs'] ?? []) !== 304
        || count($herbproperties['herbs'] ?? []) !== 304
        || count($herbrich['profiles'] ?? []) < 180
        || (int)($duiyaodata['total'] ?? 0) !== 51
        || count($duiyaodata['pairs'] ?? []) !== 51) {
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
$stops = array_fill_keys(explode(' ', 'a an and are as at be by con de del el en es for from la las los of on or para por que se the to un una y with formulas formula herbs herb tang san wan'), true);
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

$duipairs = [];
$duiherbcounts = [];
foreach ($duiyaodata['pairs'] as $pair) {
    $pairherbs = array_values(array_filter(array_map('trim', $pair['herbs'] ?? [])));
    if (count($pairherbs) !== 2) {
        continue;
    }
    foreach ($pairherbs as $pairherb) {
        $duiherbcounts[$pairherb] = ($duiherbcounts[$pairherb] ?? 0) + 1;
    }
    $duipairs[] = [
        'number' => (int)($pair['number'] ?? 0),
        'slug' => trim((string)($pair['slug'] ?? '')),
        'title' => trim((string)($pair['title'] ?? '')),
        'characters' => trim((string)($pair['characters'] ?? '')),
        'herbs' => $pairherbs,
        'category' => $localized($pair['category'] ?? '', $ises),
        'action' => $localized($pair['action'] ?? '', $ises),
        'formulas' => $localized($pair['formulas'] ?? '', $ises),
    ];
}
uksort($duiherbcounts, 'strnatcasecmp');

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
        'categorycode' => trim((string)($profile['categoryKey'] ?? $category['code'] ?? '')),
        'pattern' => $detailtext($profile, 'pattern', $ises),
        'action' => $detailtext($profile, 'action', $ises),
        'clue' => $detailtext($profile, 'clue', $ises),
        'examfocus' => $detailtext($profile, 'examFocus', $ises),
        'ingredients' => $detailtext($profile, 'ingredients', $ises),
        'kings' => trim((string)($profile['kings'] ?? '')),
    ];
}
uasort($formulas, static fn(array $left, array $right): int => strnatcasecmp($left['name'], $right['name']));
$patternoptions = [];
foreach ($formulas as $formula) {
    $patternlabel = trim((string)$formula['pattern']);
    if ($patternlabel !== '') {
        $patternkey = $normalize($patternlabel);
        if (!isset($patternoptions[$patternkey])) {
            $patternoptions[$patternkey] = [
                'pattern' => $patternlabel,
                'formula' => $formula['name'],
            ];
        }
    }
}
uasort($patternoptions, static fn(array $left, array $right): int => strnatcasecmp($left['pattern'], $right['pattern']));

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
    $categorycode = trim((string)($category['code'] ?? ''));
    $safetyprofile = $herbsafety['herbs'][$name] ?? [];
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
        'categorycode' => $categorycode,
        'flavor' => $localized($property['flavor'] ?? '', $ises),
        'temperature' => $localized($property['temperature'] ?? '', $ises),
        'channels' => $localized($rich['channels'] ?? ($property['channels'] ?? ''), $ises),
        'properties' => $localized($rich['properties'] ?? '', $ises),
        'identity' => $identity,
        'clue' => $clue,
        'caution' => $localized($safetyprofile['caution'] ?? ($herbsafety['categoryCautions'][$categorycode] ?? ''), $ises),
        'pregnancy' => $localized($safetyprofile['pregnancy'] ?? ($herbsafety['categoryPregnancy'][$categorycode] ?? ''), $ises),
        'interactions' => $localized($safetyprofile['interactions'] ?? ($herbsafety['categoryInteractions'][$categorycode] ?? ''), $ises),
        'preparation' => $localized($safetyprofile['preparation'] ?? '', $ises),
    ];
}
uasort($herbs, static fn(array $left, array $right): int => strnatcasecmp($left['name'], $right['name']));
$haschief = static function(string $chieftext, string $herbname) use ($normalize): bool {
    $target = $normalize($herbname);
    $haystack = $normalize($chieftext);
    return $target !== '' && $haystack !== ''
        && preg_match('/(^| )' . preg_quote($target, '/') . '( |$)/', $haystack) === 1;
};
$chiefherboptions = [];
foreach ($herbs as $herb) {
    $formulacount = 0;
    foreach ($formulas as $formula) {
        if ($haschief($formula['kings'], $herb['name'])) {
            $formulacount++;
        }
    }
    if ($formulacount > 0) {
        $chiefherboptions[] = [
            'name' => $herb['name'],
            'count' => $formulacount,
        ];
    }
}
$chiefgroupmap = [];
foreach ($formulas as $formula) {
    $chiefgroup = trim((string)$formula['kings']);
    if ($chiefgroup === '' || !preg_match('/[,\/]/', $chiefgroup)) {
        continue;
    }
    $groupkey = $normalize($chiefgroup);
    if (!isset($chiefgroupmap[$groupkey])) {
        $chiefgroupmap[$groupkey] = [
            'value' => 'group:' . $chiefgroup,
            'label' => trim((string)preg_replace('/\s*[,\/]\s*/', ' + ', $chiefgroup)),
            'count' => 0,
        ];
    }
    $chiefgroupmap[$groupkey]['count']++;
}
$chiefgroupoptions = array_values($chiefgroupmap);
usort($chiefgroupoptions, static fn(array $left, array $right): int => strnatcasecmp($left['label'], $right['label']));

$categorycomparisonurl = null;
$categorycomparisonfullurl = null;
if ($mode === 'herb' && $item !== '' && isset($herbs[$normalize($item)])) {
    $selectedherbcategorycode = $herbs[$normalize($item)]['categorycode'];
    $selectedherbcategoryid = $herbcategorypageids[$selectedherbcategorycode] ?? '';
    if ($selectedherbcategoryid !== '') {
        $comparisonparams = [
            'category' => $selectedherbcategoryid,
            'lang' => $ises ? 'es' : 'en',
            'ui' => $ises ? 'es' : 'en',
        ];
        $categorycomparisonfullurl = new moodle_url('/blocks/myprogress/herbs-category-comparison.php', $comparisonparams);
        $comparisonparams['embed'] = 1;
        $categorycomparisonurl = new moodle_url('/blocks/myprogress/herbs-category-comparison.php', $comparisonparams);
    }
}

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
$tonguepulseoptions = $tonguerows;
usort($tonguepulseoptions, static fn(array $left, array $right): int => strnatcasecmp($left['name'], $right['name']));

$formularesults = [];
$herbresults = [];
$ingredientresults = [];
$chiefresults = [];
$safetyresults = [];
$duiresults = [];
$searchresults = [];

if ($mode === 'formula' && $item !== '' && isset($formulas[$normalize($item)])) {
    $source = $formulas[$normalize($item)];
    foreach ($formulas as $candidate) {
        if ($candidate['name'] === $source['name']) continue;
        $scores = [
            'category' => $source['categorycode'] !== '' && $source['categorycode'] === $candidate['categorycode'] ? 1.0 : 0.0,
            'pattern' => $fieldscore($source['pattern'], $candidate['pattern']),
            'action' => $fieldscore($source['action'], $candidate['action']),
            'ingredients' => $fieldscore($source['ingredients'], $candidate['ingredients']),
            'chief' => $fieldscore($source['kings'], $candidate['kings']),
            'clue' => $fieldscore($source['clue'] . ' ' . $source['examfocus'], $candidate['clue'] . ' ' . $candidate['examfocus']),
        ];
        $score = $scores['category'] * 0.08 + $scores['pattern'] * 0.30 + $scores['action'] * 0.27
            + $scores['ingredients'] * 0.17 + $scores['chief'] * 0.08 + $scores['clue'] * 0.10;
        if ($score <= 0.01) continue;
        $reasons = [];
        if ($scores['category'] > 0) $reasons[] = $ises ? 'Misma categoría funcional' : 'Same functional category';
        if ($scores['pattern'] >= 0.12) $reasons[] = $ises ? 'Patrón relacionado' : 'Related pattern';
        if ($scores['action'] >= 0.12) $reasons[] = $ises ? 'Acciones relacionadas' : 'Related actions';
        if ($scores['ingredients'] >= 0.10) $reasons[] = $ises ? 'Ingredientes superpuestos' : 'Overlapping ingredients';
        if ($scores['chief'] >= 0.10) $reasons[] = $ises ? 'Chief herbs relacionados' : 'Related Chief herbs';
        $formularesults[] = ['score' => $score, 'card' => $candidate, 'reasons' => $reasons];
    }
    usort($formularesults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $formularesults = array_slice($formularesults, 0, 10);
} else if ($mode === 'ingredients' && $item !== '' && isset($formulas[$normalize($item)])) {
    $source = $formulas[$normalize($item)];
    foreach ($formulas as $candidate) {
        if ($candidate['name'] === $source['name']) continue;
        $ingredientscore = $fieldscore($source['ingredients'], $candidate['ingredients']);
        $chiefscore = $fieldscore($source['kings'], $candidate['kings']);
        $score = $ingredientscore * 0.75 + $chiefscore * 0.25;
        if ($score <= 0.01) continue;
        $reasons = [];
        if ($ingredientscore > 0) $reasons[] = $ises ? 'Comparte ingredientes' : 'Shares ingredients';
        if ($chiefscore > 0) $reasons[] = $ises ? 'Comparte Chief herbs' : 'Shares Chief herbs';
        $ingredientresults[] = ['score' => $score, 'card' => $candidate, 'reasons' => $reasons];
    }
    usort($ingredientresults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $ingredientresults = array_slice($ingredientresults, 0, 12);
} else if ($mode === 'chief' && $item !== '') {
    $ischiefgroup = strpos($item, 'group:') === 0;
    $selectedchiefgroup = $ischiefgroup ? trim(substr($item, 6)) : '';
    foreach ($formulas as $candidate) {
        $matches = $ischiefgroup
            ? $normalize($candidate['kings']) === $normalize($selectedchiefgroup)
            : $haschief($candidate['kings'], $item);
        if (!$matches) continue;
        $chiefresults[] = [
            'score' => 1.0,
            'card' => $candidate,
            'reasons' => [$ischiefgroup
                ? ($ises ? 'Usa este grupo completo de Chief herbs' : 'Uses this complete Chief-herb group')
                : ($ises ? 'Usa esta hierba como Chief' : 'Uses this herb as a Chief')],
        ];
    }
    usort($chiefresults, static fn(array $left, array $right): int => strnatcasecmp($left['card']['name'], $right['card']['name']));
} else if ($mode === 'herb' && $item !== '' && isset($herbs[$normalize($item)])) {
    $source = $herbs[$normalize($item)];
    foreach ($herbs as $candidate) {
        if ($candidate['name'] === $source['name']) continue;
        $scores = [
            'category' => $source['categorycode'] !== '' && $source['categorycode'] === $candidate['categorycode'] ? 1.0 : 0.0,
            'properties' => $fieldscore($source['properties'] . ' ' . $source['flavor'] . ' ' . $source['temperature'], $candidate['properties'] . ' ' . $candidate['flavor'] . ' ' . $candidate['temperature']),
            'channels' => $fieldscore($source['channels'], $candidate['channels']),
            'action' => $fieldscore($source['identity'], $candidate['identity']),
            'clue' => $fieldscore($source['clue'], $candidate['clue']),
            'safety' => $fieldscore($source['caution'] . ' ' . $source['pregnancy'] . ' ' . $source['interactions'], $candidate['caution'] . ' ' . $candidate['pregnancy'] . ' ' . $candidate['interactions']),
        ];
        $score = $scores['category'] * 0.25 + $scores['properties'] * 0.15 + $scores['channels'] * 0.12
            + $scores['action'] * 0.23 + $scores['clue'] * 0.10 + $scores['safety'] * 0.15;
        if ($score <= 0.01) continue;
        $reasons = [];
        if ($scores['category'] > 0) $reasons[] = $ises ? 'Misma categoría Bensky' : 'Same Bensky category';
        if ($scores['action'] >= 0.12) $reasons[] = $ises ? 'Acciones relacionadas' : 'Related actions';
        if ($scores['channels'] >= 0.20) $reasons[] = $ises ? 'Canales compartidos' : 'Shared channels';
        if ($scores['properties'] >= 0.12) $reasons[] = $ises ? 'Sabor y temperatura similares' : 'Similar flavor and temperature';
        if ($scores['safety'] >= 0.12) $reasons[] = $ises ? 'Precauciones relacionadas' : 'Related safety cautions';
        $herbresults[] = ['score' => $score, 'card' => $candidate, 'reasons' => $reasons];
    }
    usort($herbresults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $herbresults = array_slice($herbresults, 0, 10);
} else if (($mode === 'pattern' || $mode === 'clinical') && $query !== '') {
    foreach ($formulas as $candidate) {
        $text = implode(' ', [$candidate['category'], $candidate['pattern'], $candidate['action']]);
        $score = $queryscore($query, $text);
        if ($score > 0) $searchresults[] = ['score' => $score, 'card' => $candidate];
    }
    usort($searchresults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $searchresults = array_slice($searchresults, 0, 12);
} else if ($mode === 'clue' && $query !== '') {
    foreach ($formulas as $candidate) {
        $text = implode(' ', [$candidate['clue'], $candidate['examfocus'], $candidate['pattern']]);
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
} else if ($mode === 'safety' && $query !== '') {
    foreach ($herbs as $candidate) {
        $text = implode(' ', [$candidate['name'], $candidate['category'], $candidate['caution'], $candidate['pregnancy'], $candidate['interactions'], $candidate['preparation']]);
        $score = $queryscore($query, $text);
        if ($score > 0) $safetyresults[] = ['score' => $score, 'card' => $candidate];
    }
    usort($safetyresults, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
    $safetyresults = array_slice($safetyresults, 0, 15);
} else if ($mode === 'dui' && $item !== '') {
    foreach ($duipairs as $candidate) {
        $matches = false;
        foreach ($candidate['herbs'] as $pairherb) {
            if ($normalize($pairherb) === $normalize($item)) {
                $matches = true;
                break;
            }
        }
        if (!$matches) continue;
        $duiresults[] = ['score' => 1.0, 'card' => $candidate];
    }
    usort($duiresults, static fn(array $left, array $right): int => $left['card']['number'] <=> $right['card']['number']);
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
$PAGE->requires->js_init_code(<<<'JS'
(function() {
    const button = document.querySelector('[data-herb-category-toggle]');
    const panel = document.querySelector('[data-herb-category-panel]');
    const frame = document.querySelector('[data-herb-category-frame]');
    if (!button || !panel || !frame) return;

    const resizeFrame = function() {
        try {
            const doc = frame.contentDocument;
            if (!doc) return;
            const height = Math.max(
                doc.body ? doc.body.scrollHeight : 0,
                doc.documentElement ? doc.documentElement.scrollHeight : 0
            );
            if (height > 0) frame.style.height = Math.min(Math.max(height + 12, 360), 1100) + 'px';
        } catch (_) {
            frame.style.height = '620px';
        }
    };
    frame.addEventListener('load', function() {
        resizeFrame();
        window.setTimeout(resizeFrame, 250);
    });
    button.addEventListener('click', function() {
        const opening = panel.hidden;
        panel.hidden = !opening;
        button.setAttribute('aria-expanded', String(opening));
        button.textContent = opening ? button.dataset.hideLabel : button.dataset.showLabel;
        if (opening && !frame.getAttribute('src')) frame.setAttribute('src', frame.dataset.src);
        if (opening) window.setTimeout(function() { panel.scrollIntoView({behavior: 'smooth', block: 'start'}); }, 60);
    });
}());
JS
);

echo $OUTPUT->header();
?>
<style>
.smart-tools{width:min(1280px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}
.smart-tools__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:26px;border:1px solid #d8c8ed;border-top:6px solid #6d28d9;border-radius:15px;background:linear-gradient(135deg,#faf7ff,#fff9ed)}
.smart-tools__eyebrow{margin:0 0 7px;color:#6d28d9;font-size:12px;font-weight:950;letter-spacing:.09em;text-transform:uppercase}.smart-tools h2{margin:0 0 9px;font-size:clamp(30px,4vw,44px)}.smart-tools__lead{max-width:820px;margin:0;color:#526b84;line-height:1.55}.smart-tools__actions,.smart-tools__tabs{display:flex;flex-wrap:wrap;gap:9px}
.smart-tools__button,.smart-tools__tab{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}.smart-tools__button:hover,.smart-tools__tab:hover{background:#eef7f5}.smart-tools__tab.is-active{border-color:#6d28d9;background:#6d28d9;color:#fff!important}
.smart-tools__tabs{margin-bottom:18px}.smart-tools__panel{padding:20px;border:1px solid #d6dfe5;border-radius:13px;background:#fff;box-shadow:0 5px 18px rgba(23,50,77,.05)}.smart-tools__panel h3{margin:0 0 7px;font-size:24px}.smart-tools__panel>p{margin:0 0 16px;color:#526b84}
.smart-tools__form{display:grid;grid-template-columns:minmax(250px,1fr) auto;gap:11px;align-items:end}.smart-tools__form label{display:grid;gap:6px;font-size:12px;font-weight:900}.smart-tools__form select,.smart-tools__form input{width:100%;min-height:48px;padding:9px 12px;border:1px solid #aebfca;border-radius:8px;background:#fff;color:#102a43;font:inherit}
.smart-tools__launchers{display:flex;flex-wrap:wrap;gap:10px;margin-top:14px}.smart-tools__launcher{display:inline-flex;min-height:48px;align-items:center;justify-content:center;padding:10px 16px;border-radius:9px;background:#0d7868;color:#fff!important;font-weight:900;text-decoration:none!important}.smart-tools__launcher--alt{background:#6d28d9}.smart-tools__notice{margin:18px 0 0;padding:13px 15px;border-left:5px solid #6d28d9;border-radius:9px;background:#f5f0ff;color:#4c1d95}.smart-tools__results-heading{display:flex;align-items:baseline;justify-content:space-between;gap:16px;margin:26px 0 13px}.smart-tools__results-heading h3{margin:0;font-size:24px}.smart-tools__results-heading span{color:#526b84;font-weight:800}
.smart-tools__results{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.smart-tools__result{padding:18px;border:1px solid #cbd9df;border-top:5px solid #087c69;border-radius:12px;background:#fff}.smart-tools__result-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.smart-tools__result h4{margin:0;font-size:20px}.smart-tools__score{flex:0 0 auto;padding:5px 9px;border-radius:999px;background:#e8f7f1;color:#075f50;font-size:12px;font-weight:950}.smart-tools__category{margin:5px 0 10px;color:#6d28d9;font-size:12px;font-weight:850}.smart-tools__result dl{display:grid;grid-template-columns:105px 1fr;gap:8px 10px;margin:0}.smart-tools__result dt{font-size:12px;font-weight:900}.smart-tools__result dd{margin:0;color:#52657a;font-size:13px;line-height:1.5}.smart-tools__reason{display:flex;flex-wrap:wrap;gap:6px;margin:12px 0}.smart-tools__reason span{padding:4px 8px;border-radius:999px;background:#fff1d6;color:#7c4308;font-size:11px;font-weight:850}.smart-tools__open{display:inline-flex;min-height:40px;align-items:center;padding:8px 12px;border-radius:8px;background:#0d7868;color:#fff!important;font-weight:850;text-decoration:none!important}.smart-tools__empty{margin-top:22px;padding:26px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;text-align:center;color:#526b84;font-weight:800}
.smart-tools__method{margin-top:22px;padding:15px;border-left:5px solid #e9a11b;border-radius:9px;background:#fff8e9;color:#71420c;line-height:1.5}
.smart-tools__category-actions{margin-top:14px}.smart-tools__category-panel{scroll-margin-top:18px;margin-top:24px;padding:14px;border:1px solid #b9d8d2;border-top:5px solid #087c69;border-radius:13px;background:#fff}.smart-tools__category-panel[hidden]{display:none}.smart-tools__category-panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 0 10px}.smart-tools__category-panel-head h3{margin:0;font-size:22px}.smart-tools__category-frame{display:block;width:100%;height:520px;border:1px solid #d1e0e4;border-radius:9px;background:#fff}
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
            'pattern' => $ises ? '🧭 Patrón → Fórmula' : '🧭 Pattern → Formula',
            'tongue' => $ises ? '👅 Lengua y pulso' : '👅 Tongue & Pulse',
            'ingredients' => $ises ? '🧪 Ingredientes similares' : '🧪 Ingredient Similarity',
            'chief' => $ises ? '⭐ Alternativas Chief' : '⭐ Chief Alternatives',
            'dui' => $ises ? '🔗 Pares Duì Yào' : '🔗 Duì Yào Pairs',
            'safety' => $ises ? '⚠ Seguridad' : '⚠ Safety Finder',
            'clue' => $ises ? '🔎 Pista clínica' : '🔎 Clinical Clue',
            'traps' => $ises ? '🧠 Nombres confundibles' : '🧠 Name Traps',
            'quiz' => $ises ? '🎯 Quiz personalizado' : '🎯 Personalized Quiz',
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
        <?php if ($mode === 'formula' || $mode === 'ingredients') { ?>
            <h3><?php echo $mode === 'formula'
                ? ($ises ? 'Encontrar fórmulas similares' : 'Find Similar Formulas')
                : ($ises ? 'Similitud por ingredientes' : 'Formula Ingredient Similarity'); ?></h3>
            <p><?php echo $mode === 'formula'
                ? ($ises ? 'Compara categoría, patrón, acción, ingredientes, Chief herbs y pista decisiva.' : 'Compare category, pattern, action, ingredients, Chief herbs and decisive clues.')
                : ($ises ? 'Encuentra fórmulas que comparten ingredientes o Chief herbs.' : 'Find formulas that share ingredients or Chief herbs.'); ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="<?php echo s($mode); ?>"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo $ises ? 'Fórmula de origen' : 'Source formula'; ?></span><select name="item" required><option value=""><?php echo $ises ? 'Seleccionar fórmula…' : 'Select a formula…'; ?></option><?php foreach ($formulas as $formula) { ?><option value="<?php echo s($formula['name']); ?>"<?php echo $normalize($item) === $normalize($formula['name']) ? ' selected' : ''; ?>><?php echo s($formula['name']); ?></option><?php } ?></select></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Buscar relaciones' : 'Find relationships'; ?></button>
            </form>
        <?php } else if ($mode === 'herb') { ?>
            <h3><?php echo $ises ? 'Encontrar hierbas similares' : 'Find Similar Herbs'; ?></h3>
            <p><?php echo $ises ? 'Compara acción, categoría Bensky, temperatura, sabor, canales, indicaciones y seguridad.' : 'Compare action, Bensky category, temperature, flavor, channels, indications and safety.'; ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="herb"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo $ises ? 'Hierba de origen' : 'Source herb'; ?></span><select name="item" required><option value=""><?php echo $ises ? 'Seleccionar hierba…' : 'Select an herb…'; ?></option><?php foreach ($herbs as $herb) { ?><option value="<?php echo s($herb['name']); ?>"<?php echo $normalize($item) === $normalize($herb['name']) ? ' selected' : ''; ?>><?php echo s($herb['name']); ?></option><?php } ?></select></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Encontrar similares' : 'Find similar'; ?></button>
            </form>
            <?php if ($categorycomparisonurl !== null) { ?>
                <div class="smart-tools__category-actions">
                    <button class="smart-tools__button" type="button" data-herb-category-toggle aria-expanded="false"
                        data-show-label="<?php echo s($ises ? '▦ Mostrar comparación de la categoría' : '▦ Show category comparison'); ?>"
                        data-hide-label="<?php echo s($ises ? '▦ Ocultar comparación de la categoría' : '▦ Hide category comparison'); ?>"><?php echo $ises ? '▦ Mostrar comparación de la categoría' : '▦ Show category comparison'; ?></button>
                </div>
            <?php } ?>
        <?php } else if ($mode === 'chief') { ?>
            <h3><?php echo $ises ? 'Alternativas por Chief herb' : 'Chief Herb Alternatives'; ?></h3>
            <p><?php echo $ises ? 'Busca por una Chief herb individual o por un grupo completo de varias Chief herbs.' : 'Search by one Chief herb or by a complete group of multiple Chief herbs.'; ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="chief"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo $ises ? 'Chief herb o grupo documentado' : 'Documented Chief herb or group'; ?></span><select name="item" required>
                    <option value=""><?php echo $ises ? 'Seleccionar Chief herb o grupo…' : 'Select a Chief herb or group…'; ?></option>
                    <optgroup label="<?php echo $ises ? 'Grupos de varias Chief herbs' : 'Multiple Chief-herb groups'; ?>">
                        <?php foreach ($chiefgroupoptions as $chiefgroupoption) { ?><option value="<?php echo s($chiefgroupoption['value']); ?>"<?php echo $normalize($item) === $normalize($chiefgroupoption['value']) ? ' selected' : ''; ?>><?php echo s($chiefgroupoption['label'] . ' (' . $chiefgroupoption['count'] . ')'); ?></option><?php } ?>
                    </optgroup>
                    <optgroup label="<?php echo $ises ? 'Chief herbs individuales' : 'Individual Chief herbs'; ?>">
                        <?php foreach ($chiefherboptions as $chiefoption) { ?><option value="<?php echo s($chiefoption['name']); ?>"<?php echo $normalize($item) === $normalize($chiefoption['name']) ? ' selected' : ''; ?>><?php echo s($chiefoption['name'] . ' (' . $chiefoption['count'] . ')'); ?></option><?php } ?>
                    </optgroup>
                </select></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Buscar fórmulas' : 'Find formulas'; ?></button>
            </form>
        <?php } else if ($mode === 'pattern' || $mode === 'clinical' || $mode === 'clue' || $mode === 'safety' || $mode === 'tongue') {
            $paneltitles = [
                'pattern' => $ises ? 'Patrón → Buscador de fórmulas' : 'Pattern → Formula Finder',
                'clinical' => $ises ? 'Patrón → Buscador de fórmulas' : 'Pattern → Formula Finder',
                'clue' => $ises ? 'Buscador por pista clínica' : 'Clinical Clue Finder',
                'safety' => $ises ? 'Seguridad y contraindicaciones' : 'Safety & Contraindication Finder',
                'tongue' => $ises ? 'Coincidencia por lengua y pulso' : 'Tongue & Pulse Matcher',
            ];
            $paneldescriptions = [
                'pattern' => $ises ? 'Busca patrones clínicos y fórmulas oficiales relacionadas.' : 'Search clinical patterns and related official formulas.',
                'clinical' => $ises ? 'Busca patrones clínicos y fórmulas oficiales relacionadas.' : 'Search clinical patterns and related official formulas.',
                'clue' => $ises ? 'Busca pistas decisivas como “tos seca con sudores nocturnos”.' : 'Search decisive clues such as “dry cough with night sweats.”',
                'safety' => $ises ? 'Busca embarazo, toxicidad, interacciones, preparación e incompatibilidades.' : 'Search pregnancy, toxicity, interactions, preparation and incompatibilities.',
                'tongue' => $ises ? 'Busca entre 63 fórmulas con hallazgos verificados de lengua y pulso.' : 'Search 63 formulas with verified tongue and pulse findings.',
            ];
            $placeholders = [
                'pattern' => $ises ? 'Ej.: deficiencia Yin de Pulmón y Riñón' : 'Example: Lung and Kidney Yin deficiency',
                'clinical' => $ises ? 'Ej.: deficiencia Yin de Pulmón y Riñón' : 'Example: Lung and Kidney Yin deficiency',
                'clue' => $ises ? 'Ej.: tos seca, sudores nocturnos' : 'Example: dry cough, night sweats',
                'safety' => $ises ? 'Ej.: embarazo, anticoagulantes, toxicidad' : 'Example: pregnancy, anticoagulants, toxicity',
                'tongue' => $ises ? 'Ej.: lengua roja, pulso fino y rápido' : 'Example: red tongue, thin rapid pulse',
            ];
        ?>
            <h3><?php echo s($paneltitles[$mode]); ?></h3>
            <p><?php echo s($paneldescriptions[$mode]); ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="<?php echo s($mode); ?>"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo ($mode === 'pattern' || $mode === 'clinical')
                    ? ($ises ? 'Selecciona o escribe un patrón' : 'Select or type a pattern')
                    : ($mode === 'tongue'
                        ? ($ises ? 'Selecciona o escribe lengua y pulso' : 'Select or type tongue and pulse')
                        : ($ises ? 'Términos de búsqueda' : 'Search terms')); ?></span><input type="search" name="q" value="<?php echo s($query); ?>" required placeholder="<?php echo s($placeholders[$mode]); ?>"<?php echo ($mode === 'pattern' || $mode === 'clinical')
                        ? ' list="smart-pattern-options" autocomplete="off"'
                        : ($mode === 'tongue' ? ' list="smart-tongue-pulse-options" autocomplete="off"' : ''); ?>></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Buscar' : 'Search'; ?></button>
                <?php if ($mode === 'pattern' || $mode === 'clinical') { ?>
                    <datalist id="smart-pattern-options">
                        <?php foreach ($patternoptions as $patternoption) { ?>
                            <option value="<?php echo s($patternoption['pattern']); ?>"><?php echo s($patternoption['formula']); ?></option>
                        <?php } ?>
                    </datalist>
                <?php } ?>
                <?php if ($mode === 'tongue') { ?>
                    <datalist id="smart-tongue-pulse-options">
                        <?php foreach ($tonguepulseoptions as $tonguepulseoption) {
                            $findinglabel = trim($tonguepulseoption['tongue'] . '; ' . $tonguepulseoption['pulse'], '; ');
                        ?>
                            <option value="<?php echo s($findinglabel); ?>"><?php echo s($tonguepulseoption['name']); ?></option>
                        <?php } ?>
                    </datalist>
                <?php } ?>
            </form>
        <?php } else if ($mode === 'dui') { ?>
            <h3><?php echo $ises ? 'Buscador de pares Duì Yào por hierba' : 'Duì Yào Pairs by Herb'; ?></h3>
            <p><?php echo $ises ? 'Selecciona una hierba para ver todos sus pares oficiales, acciones combinadas y fórmulas relacionadas.' : 'Select an herb to see all of its official pairs, combined actions and related formulas.'; ?></p>
            <form class="smart-tools__form" method="get">
                <input type="hidden" name="mode" value="dui"><?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
                <label><span><?php echo $ises ? 'Hierba con Duì Yào' : 'Herb with Duì Yào pairs'; ?></span><select name="item" required>
                    <option value=""><?php echo $ises ? 'Seleccionar hierba…' : 'Select an herb…'; ?></option>
                    <?php foreach ($duiherbcounts as $duiherb => $duicount) { ?><option value="<?php echo s($duiherb); ?>"<?php echo $normalize($item) === $normalize($duiherb) ? ' selected' : ''; ?>><?php echo s($duiherb . ' (' . $duicount . ')'); ?></option><?php } ?>
                </select></label>
                <button class="smart-tools__button" type="submit"><?php echo $ises ? 'Buscar pares' : 'Find pairs'; ?></button>
            </form>
            <div class="smart-tools__launchers"><a class="smart-tools__button" href="<?php echo $duiyaourl->out(false); ?>"><?php echo $ises ? 'Abrir biblioteca completa →' : 'Open complete library →'; ?></a></div>
        <?php } else if ($mode === 'traps') { ?>
            <h3><?php echo $ises ? 'Detector de nombres confundibles' : 'Name Trap Detector'; ?></h3>
            <p><?php echo $ises ? 'Compara fórmulas y hierbas cuyos nombres se confunden fácilmente en el examen.' : 'Compare formulas and herbs whose names are easily confused on exams.'; ?></p>
            <div class="smart-tools__launchers">
                <a class="smart-tools__launcher" href="<?php echo $formulanametrapsurl->out(false); ?>"><?php echo $ises ? 'Trampas de fórmulas →' : 'Formula name traps →'; ?></a>
                <a class="smart-tools__launcher smart-tools__launcher--alt" href="<?php echo $nametrapsurl->out(false); ?>"><?php echo $ises ? 'Trampas de hierbas →' : 'Herb name traps →'; ?></a>
            </div>
        <?php } else { ?>
            <h3><?php echo $ises ? 'Quiz personalizado' : 'Personalized Quiz'; ?></h3>
            <p><?php echo $ises ? 'Genera preguntas con prioridad para las fórmulas donde el estudiante obtiene menos de 80%.' : 'Prioritize questions from formulas where the student scores below 80%.'; ?></p>
            <div class="smart-tools__launchers"><a class="smart-tools__launcher" href="<?php echo $personalizedquizurl->out(false); ?>"><?php echo $ises ? 'Comenzar repaso personalizado →' : 'Start personalized review →'; ?></a></div>
        <?php } ?>
        <p class="smart-tools__notice"><?php echo $ises
            ? 'Los resultados usan únicamente datos verificados del guideline. Son material educativo y no sustituyen el diagnóstico profesional.'
            : 'Results use only verified guideline data. They are educational material and do not replace professional diagnosis.'; ?></p>
        <?php if ($mode === 'safety') { ?><div class="smart-tools__launchers"><a class="smart-tools__button" href="<?php echo $safetylibraryurl->out(false); ?>"><?php echo $ises ? 'Biblioteca de seguridad' : 'Safety Library'; ?></a><a class="smart-tools__button" href="<?php echo $incompatibilitiesurl->out(false); ?>"><?php echo $ises ? 'Incompatibilidades herbales' : 'Herbal incompatibilities'; ?></a></div><?php } ?>
    </section>

    <?php
    $resultsets = [
        'formula' => $formularesults,
        'ingredients' => $ingredientresults,
        'chief' => $chiefresults,
        'herb' => $herbresults,
        'pattern' => $searchresults,
        'clinical' => $searchresults,
        'clue' => $searchresults,
        'tongue' => $searchresults,
        'safety' => $safetyresults,
        'dui' => $duiresults,
    ];
    $activeresults = $resultsets[$mode] ?? [];
    $resultcount = count($activeresults);
    $showresults = isset($resultsets[$mode]) && ($item !== '' || $query !== '');
    if ($showresults) {
    ?>
        <header class="smart-tools__results-heading"><h3><?php echo $ises ? 'Resultados relacionados' : 'Related results'; ?></h3><span><?php echo $resultcount; ?> <?php echo $ises ? 'resultados' : 'results'; ?></span></header>
        <?php if ($resultcount === 0) { ?>
            <p class="smart-tools__empty"><?php echo $mode === 'chief'
                ? ($ises ? 'Esta hierba no está documentada como Chief en ninguna fórmula oficial del guideline.' : 'This herb is not documented as a Chief in any official guideline formula.')
                : ($ises ? 'No se encontraron coincidencias verificadas. Prueba términos más generales u otra hierba.' : 'No verified matches were found. Try broader terms or another herb.'); ?></p>
        <?php } else { ?>
            <div class="smart-tools__results">
                <?php if (in_array($mode, ['formula', 'ingredients', 'chief', 'pattern', 'clinical', 'clue', 'tongue'], true)) {
                    foreach ($activeresults as $result) { $card = $result['card']; ?>
                    <article class="smart-tools__result">
                        <header class="smart-tools__result-head"><h4><?php echo s($card['name']); ?></h4><span class="smart-tools__score"><?php echo (int)round($result['score'] * 100); ?>%</span></header>
                        <p class="smart-tools__category"><?php echo s($card['category']); ?></p>
                        <dl>
                            <?php if ($mode === 'tongue') { ?>
                                <dt><?php echo $ises ? 'Lengua' : 'Tongue'; ?></dt><dd><?php echo s($card['tongue']); ?></dd>
                                <dt><?php echo $ises ? 'Pulso' : 'Pulse'; ?></dt><dd><?php echo s($card['pulse']); ?></dd>
                            <?php } else { ?>
                                <?php if (($mode === 'ingredients' || $mode === 'chief') && $card['kings'] !== '') { ?><dt>Chief</dt><dd><?php echo s($card['kings']); ?></dd><?php } ?>
                                <?php if ($mode === 'ingredients' && $card['ingredients'] !== '') { ?><dt><?php echo $ises ? 'Ingredientes' : 'Ingredients'; ?></dt><dd><?php echo s($card['ingredients']); ?></dd><?php } ?>
                                <?php if ($card['pattern'] !== '') { ?><dt><?php echo $ises ? 'Patrón' : 'Pattern'; ?></dt><dd><?php echo s($card['pattern']); ?></dd><?php } ?>
                                <?php if ($card['action'] !== '') { ?><dt><?php echo $ises ? 'Acción' : 'Action'; ?></dt><dd><?php echo s($card['action']); ?></dd><?php } ?>
                                <?php if (($mode === 'formula' || $mode === 'clue') && $card['clue'] !== '') { ?><dt><?php echo $ises ? 'Pista decisiva' : 'Decisive clue'; ?></dt><dd><?php echo s($card['clue']); ?></dd><?php } ?>
                            <?php } ?>
                        </dl>
                        <div class="smart-tools__reason"><?php if (!empty($result['reasons'])) { foreach ($result['reasons'] as $reason) { ?><span><?php echo s($reason); ?></span><?php } } else { ?><span><?php echo $ises ? 'Coincidencia con la búsqueda' : 'Search match'; ?></span><?php } ?></div>
                        <a class="smart-tools__open" href="<?php echo $formulalink($card['name'])->out(false); ?>"><?php echo $ises ? 'Abrir tarjeta de fórmula →' : 'Open formula card →'; ?></a>
                    </article>
                <?php }} else if ($mode === 'dui') {
                    foreach ($activeresults as $result) { $card = $result['card'];
                        $pairurl = new moodle_url('/study-guide/comparisons/' . $card['slug'] . ($ises ? '-es' : '') . '.html');
                    ?>
                    <article class="smart-tools__result">
                        <header class="smart-tools__result-head"><h4><?php echo s($card['title']); ?></h4><span class="smart-tools__score">#<?php echo $card['number']; ?></span></header>
                        <p class="smart-tools__category"><?php echo s($card['characters'] . ' · ' . $card['category']); ?></p>
                        <dl>
                            <dt><?php echo $ises ? 'Acción conjunta' : 'Combined action'; ?></dt><dd><?php echo s($card['action']); ?></dd>
                            <dt><?php echo $ises ? 'Fórmulas' : 'Formulas'; ?></dt><dd><?php echo s($card['formulas']); ?></dd>
                        </dl>
                        <div class="smart-tools__reason"><span><?php echo s($ises ? 'Contiene ' . $item : 'Contains ' . $item); ?></span></div>
                        <a class="smart-tools__open" href="<?php echo $pairurl->out(false); ?>"><?php echo $ises ? 'Abrir ficha Duì Yào →' : 'Open Duì Yào card →'; ?></a>
                    </article>
                <?php }} else if ($mode === 'herb' || $mode === 'safety') {
                    foreach ($activeresults as $result) { $card = $result['card']; ?>
                    <article class="smart-tools__result">
                        <header class="smart-tools__result-head"><h4><?php echo s($card['name']); ?></h4><span class="smart-tools__score"><?php echo (int)round($result['score'] * 100); ?>%</span></header>
                        <p class="smart-tools__category"><?php echo s($card['category']); ?></p>
                        <dl>
                            <?php if ($mode === 'herb') { ?>
                                <?php if ($card['identity'] !== '') { ?><dt><?php echo $ises ? 'Acción' : 'Action'; ?></dt><dd><?php echo s($card['identity']); ?></dd><?php } ?>
                                <?php if ($card['clue'] !== '') { ?><dt><?php echo $ises ? 'Indicaciones' : 'Indications'; ?></dt><dd><?php echo s($card['clue']); ?></dd><?php } ?>
                                <?php if ($card['flavor'] !== '' || $card['temperature'] !== '') { ?><dt><?php echo $ises ? 'Sabor / temp.' : 'Flavor / temp.'; ?></dt><dd><?php echo s(trim($card['flavor'] . ' · ' . $card['temperature'], ' ·')); ?></dd><?php } ?>
                                <?php if ($card['channels'] !== '') { ?><dt><?php echo $ises ? 'Canales' : 'Channels'; ?></dt><dd><?php echo s($card['channels']); ?></dd><?php } ?>
                            <?php } ?>
                            <?php if ($card['caution'] !== '') { ?><dt><?php echo $ises ? 'Precaución' : 'Caution'; ?></dt><dd><?php echo s($card['caution']); ?></dd><?php } ?>
                            <?php if ($card['pregnancy'] !== '') { ?><dt><?php echo $ises ? 'Embarazo' : 'Pregnancy'; ?></dt><dd><?php echo s($card['pregnancy']); ?></dd><?php } ?>
                            <?php if ($card['interactions'] !== '') { ?><dt><?php echo $ises ? 'Interacciones' : 'Interactions'; ?></dt><dd><?php echo s($card['interactions']); ?></dd><?php } ?>
                            <?php if ($card['preparation'] !== '') { ?><dt><?php echo $ises ? 'Preparación' : 'Preparation'; ?></dt><dd><?php echo s($card['preparation']); ?></dd><?php } ?>
                        </dl>
                        <?php if (!empty($result['reasons'])) { ?><div class="smart-tools__reason"><?php foreach ($result['reasons'] as $reason) { ?><span><?php echo s($reason); ?></span><?php } ?></div><?php } ?>
                        <a class="smart-tools__open" href="<?php echo $herblink($card['name'])->out(false); ?>"><?php echo $ises ? 'Abrir tarjeta de hierba →' : 'Open herb card →'; ?></a>
                    </article>
                <?php }} ?>
            </div>
        <?php } ?>
    <?php } ?>

    <?php if ($mode === 'herb' && $categorycomparisonurl !== null) { ?>
        <section class="smart-tools__category-panel" data-herb-category-panel hidden aria-label="<?php echo s($ises ? 'Comparación de la categoría Bensky' : 'Bensky category comparison'); ?>">
            <header class="smart-tools__category-panel-head">
                <h3><?php echo s(($ises ? 'Comparación de categoría: ' : 'Category comparison: ') . ($herbs[$normalize($item)]['category'] ?? '')); ?></h3>
                <a class="smart-tools__button" href="<?php echo $categorycomparisonfullurl->out(false); ?>"><?php echo $ises ? 'Abrir tabla completa →' : 'Open full table →'; ?></a>
            </header>
            <iframe class="smart-tools__category-frame" data-herb-category-frame data-src="<?php echo $categorycomparisonurl->out(false); ?>"
                title="<?php echo s($ises ? 'Tabla comparativa de la categoría de hierbas' : 'Herb category comparison table'); ?>" loading="lazy"></iframe>
        </section>
    <?php } ?>

    <aside class="smart-tools__method"><strong><?php echo $ises ? 'Cómo funciona:' : 'How it works:'; ?></strong> <?php echo $ises
        ? 'la puntuación pondera categoría, patrón, acción, ingredientes, Chief herbs, propiedades, canales y pistas verificadas. El sistema no llama a ChatGPT ni a servicios externos.'
        : 'the score weights category, pattern, action, ingredients, Chief herbs, properties, channels and verified clues. The system does not call ChatGPT or any external service.'; ?></aside>
</main>
<?php
echo $OUTPUT->footer();
