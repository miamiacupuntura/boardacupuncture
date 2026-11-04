<?php
// Single Herbs Study Guide category catalog.

require_once(__DIR__ . '/../../config.php');

require_login();

$sourcecourseid = 45;
$focus = optional_param('focus', '', PARAM_ALPHANUMEXT);
$requestedherb = trim(optional_param('herb', '', PARAM_TEXT));
$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
$sections = $DB->get_records_select(
    'course_sections',
    'course = :courseid AND section BETWEEN :firstsection AND :lastsection',
    [
        'courseid' => $sourcecourseid,
        'firstsection' => 0,
        'lastsection' => 34,
    ],
    'section ASC',
    'id,section,name'
);

$categories = [];
foreach ($sections as $section) {
    $number = (int) $section->section;
    $name = trim((string) $section->name);
    if ($number === 0 && $name === '') {
        $name = 'General / Uncategorized';
    } else if ($name === '') {
        $name = 'Topic ' . $number;
    }
    $categories[$number] = [
        'number' => $number,
        'name' => format_string($name),
    ];
}

$extractstudyguidenumber = static function(string $name): ?int {
    $plaintext = trim(html_entity_decode(strip_tags($name), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if (preg_match('/^(\d+)\.?\s+/', $plaintext, $matches) !== 1) {
        return null;
    }
    return (int) $matches[1];
};

$stripstudyguidenumber = static function(string $name): string {
    return trim((string) preg_replace('/^\s*\d+\.?\s+/', '', $name));
};

// Chapter 4 is a complete study card inserted before the former card 12.
// Keep the stored course/category numbers stable while presenting a continuous
// public sequence. The former numbering skipped 22, so inserting Chapter 4 at
// 12 shifts stored cards 12–21 and closes that gap; stored 23–31 stay 23–31.
$displaystudyguidenumber = static function(?int $internalnumber): ?int {
    if ($internalnumber === null) {
        return null;
    }
    return $internalnumber >= 12 && $internalnumber <= 21
        ? $internalnumber + 1
        : $internalnumber;
};

// The orange number is the study-guide card, not the Bensky chapter.
// Several cards split one Bensky chapter into its clinical subcategories.
$benskychapterbyguidenumber = [
    1 => 1,
    2 => 1,
    3 => 2,
    4 => 2,
    5 => 2,
    6 => 2,
    7 => 2,
    8 => 2,
    9 => 3,
    10 => 3,
    11 => 3,
    12 => 5,
    13 => 6,
    14 => 6,
    15 => 6,
    16 => 7,
    17 => 8,
    18 => 9,
    19 => 10,
    20 => 10,
    21 => 11,
    23 => 12,
    24 => 12,
    25 => 12,
    26 => 12,
    27 => 13,
    28 => 14,
    29 => 14,
    30 => 15,
    31 => 16,
];
$benskychapterlabel = static function(?int $guidenumber) use ($benskychapterbyguidenumber, $ises): string {
    if ($guidenumber === null || !isset($benskychapterbyguidenumber[$guidenumber])) {
        return '';
    }
    return 'Bensky · ' . ($ises ? 'Capítulo ' : 'Chapter ') . $benskychapterbyguidenumber[$guidenumber];
};

// Canonical card identity is independent from the legacy Moodle section title.
// Chapter 4 now has its own card 12, so the old section labels from this point
// are one category behind. These names and category codes keep titles, counts
// and direct herb routes aligned with the actual detailed content.
$canonicalcategories = [
    1 => ['code' => 'warm-acrid-release-exterior', 'en' => 'Warm, Acrid Herbs that Release the Exterior', 'es' => 'Hierbas tibias y acres que liberan el Exterior'],
    2 => ['code' => 'cool-acrid-release-exterior', 'en' => 'Cool, Acrid Herbs that Release the Exterior', 'es' => 'Hierbas frescas y acres que liberan el Exterior'],
    3 => ['code' => 'clear-heat-drain-fire', 'en' => 'Herbs that Clear Heat and Drain Fire', 'es' => 'Hierbas que aclaran Calor y drenan Fuego'],
    4 => ['code' => 'cool-blood', 'en' => 'Herbs that Cool the Blood', 'es' => 'Hierbas que enfrían la Sangre'],
    5 => ['code' => 'clear-heat-dry-dampness', 'en' => 'Herbs that Clear Heat and Dry Dampness', 'es' => 'Hierbas que aclaran Calor y secan Humedad'],
    6 => ['code' => 'clear-heat-resolve-toxicity', 'en' => 'Herbs that Clear Heat and Resolve Toxicity', 'es' => 'Hierbas que aclaran Calor y resuelven toxicidad'],
    7 => ['code' => 'clear-deficiency-heat', 'en' => 'Herbs that Clear Heat from Deficiency', 'es' => 'Hierbas que aclaran Calor por deficiencia'],
    8 => ['code' => 'clear-summerheat', 'en' => 'Herbs that Clear Summerheat', 'es' => 'Hierbas que aclaran Calor de Verano'],
    9 => ['code' => 'downward-draining-purgatives', 'en' => 'Downward-Draining Purgatives', 'es' => 'Purgantes que drenan hacia abajo'],
    10 => ['code' => 'moist-laxatives', 'en' => 'Moist Laxatives', 'es' => 'Laxantes humectantes'],
    11 => ['code' => 'harsh-expellants', 'en' => 'Harsh Expellants', 'es' => 'Expulsores drásticos'],
    12 => ['code' => 'dispel-wind-dampness', 'en' => 'Herbs that Dispel Wind-Dampness', 'es' => 'Hierbas que expulsan Viento-Humedad'],
    13 => ['code' => 'cool-transform-phlegm-heat', 'en' => 'Herbs that Cool and Transform Phlegm-Heat', 'es' => 'Hierbas que enfrían y transforman Flema-Calor'],
    14 => ['code' => 'warm-transform-cold-phlegm', 'en' => 'Herbs that Warm and Transform Cold-Phlegm', 'es' => 'Hierbas que calientan y transforman Flema-Frío'],
    15 => ['code' => 'stop-coughing-calm-wheezing', 'en' => 'Herbs that Stop Coughing and Calm Wheezing', 'es' => 'Hierbas que detienen la tos y calman las sibilancias'],
    16 => ['code' => 'aromatic-transform-dampness', 'en' => 'Aromatic Herbs that Transform Dampness', 'es' => 'Hierbas aromáticas que transforman Humedad'],
    17 => ['code' => 'relieve-food-stagnation', 'en' => 'Herbs that Relieve Food Stagnation', 'es' => 'Hierbas que alivian el estancamiento de alimentos'],
    18 => ['code' => 'regulate-qi', 'en' => 'Herbs that Regulate the Qi', 'es' => 'Hierbas que regulan el Qi'],
    19 => ['code' => 'invigorate-blood', 'en' => 'Herbs that Invigorate the Blood', 'es' => 'Hierbas que vigorizan la Sangre'],
    20 => ['code' => 'stop-bleeding', 'en' => 'Herbs that Stop Bleeding', 'es' => 'Hierbas que detienen el sangrado'],
    21 => ['code' => 'warm-interior-expel-cold', 'en' => 'Herbs that Warm the Interior and Expel Cold', 'es' => 'Hierbas que calientan el Interior y expulsan Frío'],
    23 => ['code' => 'tonify-qi', 'en' => 'Herbs that Tonify Qi', 'es' => 'Hierbas que tonifican Qi'],
    24 => ['code' => 'tonify-blood', 'en' => 'Herbs that Tonify Blood', 'es' => 'Hierbas que tonifican Sangre'],
    25 => ['code' => 'tonify-yang', 'en' => 'Herbs that Tonify Yang', 'es' => 'Hierbas que tonifican Yang'],
    26 => ['code' => 'tonify-yin', 'en' => 'Herbs that Tonify Yin', 'es' => 'Hierbas que tonifican Yin'],
    27 => ['code' => 'stabilize-bind', 'en' => 'Herbs that Stabilize and Bind', 'es' => 'Hierbas que estabilizan y contienen'],
    28 => ['code' => 'anchor-settle-calm-spirit', 'en' => 'Substances that Anchor, Settle, and Calm the Spirit', 'es' => 'Sustancias que anclan, asientan y calman el Shen'],
    29 => ['code' => 'nourish-heart-calm-spirit', 'en' => 'Herbs that Nourish the Heart and Calm the Spirit', 'es' => 'Hierbas que nutren el Corazón y calman el Shen'],
    30 => ['code' => 'open-orifices', 'en' => 'Aromatic Substances that Open the Orifices', 'es' => 'Sustancias aromáticas que abren los orificios'],
    31 => ['code' => 'extinguish-wind-stop-tremors', 'en' => 'Substances that Extinguish Wind and Stop Tremors', 'es' => 'Sustancias que extinguen Viento y detienen temblores'],
];
$canonicalcategorytitle = static function(?int $guidenumber) use ($canonicalcategories, $ises): string {
    if ($guidenumber === null || !isset($canonicalcategories[$guidenumber])) {
        return '';
    }
    return (string)$canonicalcategories[$guidenumber][$ises ? 'es' : 'en'];
};

$studyguidecategories = [];
foreach ($categories as $sectionnumber => $category) {
    $guidenumber = $extractstudyguidenumber((string) $category['name']);
    if ($guidenumber !== null) {
        // Legacy guide 22 duplicates the Warm-Interior section. Its complete
        // canonical content lives in stored guide 21, displayed publicly as
        // card 22 after the Chapter 4 insertion.
        if ($guidenumber === 22) {
            continue;
        }
        $category['guide_number'] = $guidenumber;
        $studyguidecategories[$sectionnumber] = $category;
    }
}

$normalizeherbname = static function(string $value): string {
    $value = strtolower(trim((string) preg_replace('/\s*\*$/', '', $value)));
    $value = trim((string) preg_replace('/[^a-z0-9]+/', ' ', $value));
    return $value === 'xin yi hua' ? 'xin yi' : $value;
};
$cardindex = null;
$guidelineherbkeys = [];
$guidelineherbroster = [];
$benskycategorycounts = [];
$cardindexpath = __DIR__ . '/single-herb-card-index.json';
if (is_readable($cardindexpath)) {
    $decodedcardindex = json_decode(file_get_contents($cardindexpath), true);
    if (is_array($decodedcardindex)) {
        $cardindex = $decodedcardindex;
        foreach ($cardindex['herbs'] ?? [] as $herbposition => $herbcard) {
            $herbname = (string)($herbcard['name'] ?? '');
            if ($herbname !== '') {
                $guidelineherbkeys[$normalizeherbname($herbname)] = true;
                $guidelineherbroster[] = [
                    'id' => (int)($herbcard['id'] ?? ($herbposition + 1)),
                    'name' => $herbname,
                    'latin' => (string)($herbcard['latin'] ?? ''),
                    'bensky_category' => (string)($herbcard['benskyCategory'][$ises ? 'es' : 'en'] ?? ''),
                    'guide_number' => (int)($herbcard['guideNumber'] ?? 0),
                    // Chapter 4 contains one current herb in its source annex
                    // that belongs to another primary guide card. Only entries
                    // without a guide number count toward a special chapter card.
                    'special_focus' => empty($herbcard['guideNumber'])
                        ? (string)($herbcard['specialFocus'] ?? '')
                        : '',
                ];
                $benskycategorycode = (string)($herbcard['benskyCategory']['code'] ?? '');
                if ($benskycategorycode !== '') {
                    $benskycategorycounts[$benskycategorycode] = ($benskycategorycounts[$benskycategorycode] ?? 0) + 1;
                }
            }
        }
    }
}
$requestedherbcard = null;
if ($requestedherb !== '' && is_array($cardindex)) {
    $requestedkey = $normalizeherbname($requestedherb);
    foreach ($cardindex['herbs'] ?? [] as $herbcard) {
        if ($normalizeherbname((string)($herbcard['name'] ?? '')) === $requestedkey) {
            $requestedherbcard = $herbcard;
            $requestedherb = (string)$herbcard['name'];
            break;
        }
    }
}
if ($requestedherbcard !== null && !empty($requestedherbcard['guideNumber'])) {
    foreach ($studyguidecategories as $sectionnumber => $category) {
        if ((int)$category['guide_number'] === (int)$requestedherbcard['guideNumber']) {
            $focus = (string)$sectionnumber;
            break;
        }
    }
}
if ($requestedherbcard !== null && !empty($requestedherbcard['specialFocus'])) {
    $focus = (string)$requestedherbcard['specialFocus'];
}

$selected = null;
$ismixed = $focus === 'mixed';
$isproperties = $focus === 'properties';
$issafetydispensing = $focus === 'safety_dispensing';
$isactionsindications = $focus === 'actions_indications';
$isintroduction = $focus === 'introduction';
$isbenskychapterfour = $focus === 'bensky4';
$isbenskychapterseventeen = $focus === 'bensky17';
$isbenskychaptereighteen = $focus === 'bensky18';
$isbenskychapternineteen = $focus === 'bensky19';
if (!$ismixed && !$isproperties && !$issafetydispensing && !$isactionsindications && !$isintroduction && $focus !== '' && ctype_digit($focus)) {
    $number = (int) $focus;
    if (isset($categories[$number])) {
        $selected = $categories[$number];
    }
}

$selectedname = $selected !== null ? (string) $selected['name'] : '';
$selectedguidenumber = $selected !== null ? $extractstudyguidenumber($selectedname) : null;
$selecteddisplayguidenumber = $displaystudyguidenumber($selectedguidenumber);
$istopicone = $selected !== null
    && stripos($selectedname, 'Ma Huang') !== false
    && stripos($selectedname, 'Gui Zhi') !== false
    && stripos($selectedname, 'Xin Yi') !== false;
$istopictwo = $selected !== null
    && (
        (stripos($selectedname, 'Bo He') !== false && stripos($selectedname, 'Niu Bang Zi') !== false)
        || stripos($selectedname, 'Cool, Acrid') !== false
        || stripos($selectedname, 'Wind-Heat') !== false
        || stripos($selectedname, 'Wind Heat') !== false
    );
$istopicthree = $selected !== null
    && (
        (stripos($selectedname, 'Shi Gao') !== false && stripos($selectedname, 'Zhi Mu') !== false)
        || stripos($selectedname, 'Clear Heat and Drain Fire') !== false
        || stripos($selectedname, 'Clear Heat & Drain Fire') !== false
    );
$istopicfour = $selected !== null
    && (
        (stripos($selectedname, 'Sheng Di Huang') !== false && stripos($selectedname, 'Xuan Shen') !== false)
        || stripos($selectedname, 'Clear Heat and Cool Blood') !== false
        || stripos($selectedname, 'Clear Heat & Cool Blood') !== false
    );
$istopicfive = $selected !== null
    && (
        (stripos($selectedname, 'Huang Qin') !== false && stripos($selectedname, 'Huang Lian') !== false && stripos($selectedname, 'Huang Bai') !== false)
        || stripos($selectedname, 'Clear Heat and Dry Damp') !== false
        || stripos($selectedname, 'Clear Heat & Dry Damp') !== false
    );
$istopicsix = $selected !== null
    && (
        (stripos($selectedname, 'Jin Yin Hua') !== false && stripos($selectedname, 'Lian Qiao') !== false && stripos($selectedname, 'Pu Gong Ying') !== false)
        || stripos($selectedname, 'Clear and resolve toxicity') !== false
        || stripos($selectedname, 'Clear Heat and Relieve Toxicity') !== false
        || stripos($selectedname, 'Clear Heat and Resolve Toxicity') !== false
    );
$istopicseven = $selected !== null
    && (
        (stripos($selectedname, 'Qing Hao') !== false && stripos($selectedname, 'Bai Wei') !== false && stripos($selectedname, 'Di Gu Pi') !== false)
        || stripos($selectedname, 'Clear Deficient Heat') !== false
        || stripos($selectedname, 'Clear Heat from Deficiency') !== false
        || stripos($selectedname, 'Clear Deficiency Heat') !== false
    );
$istopiceight = $selected !== null
    && (
        (stripos($selectedname, 'Qing Hao') !== false && stripos($selectedname, 'He Ye') !== false && stripos($selectedname, 'Xi Gua Pi') !== false)
        || stripos($selectedname, 'Clear Summer Heat') !== false
        || stripos($selectedname, 'Clear Summerheat') !== false
    );
$istopicnine = $selected !== null
    && (
        (stripos($selectedname, 'Da Huang') !== false && stripos($selectedname, 'Mang Xiao') !== false && stripos($selectedname, 'Fan Xie Ye') !== false && stripos($selectedname, 'Lu Hui') !== false)
        || stripos($selectedname, 'Downward Draining (Purgatives)') !== false
        || stripos($selectedname, 'Downward-Draining Purgatives') !== false
    );
$istopicten = $selected !== null
    && (
        (stripos($selectedname, 'Huo Ma Ren') !== false && stripos($selectedname, 'Yu Li Ren') !== false)
        || stripos($selectedname, 'Downward Draining (Moist laxative)') !== false
        || stripos($selectedname, 'Moist Laxatives') !== false
    );
$istopiceleven = $selected !== null
    && (
        (stripos($selectedname, 'Gan Sui') !== false && stripos($selectedname, 'Yuan Hua') !== false && stripos($selectedname, 'Qian Niu Zi') !== false)
        || stripos($selectedname, 'Harsh Expellants') !== false
        || stripos($selectedname, 'Cathartics') !== false
    );
$istopictwelve = $selected !== null
    && (
        (stripos($selectedname, 'Du Huo') !== false && stripos($selectedname, 'Qin Jiao') !== false && stripos($selectedname, 'Wei Ling Xian') !== false)
        || stripos($selectedname, 'Herbs That Drain Dampness') !== false
        || stripos($selectedname, 'Drain Dampness (Diuretics)') !== false
        || stripos($selectedname, 'Expel Wind-Dampness') !== false
    );
$istopicthirteen = $selected !== null
    && (
        (stripos($selectedname, 'Qian Hu') !== false && stripos($selectedname, 'Chuan Bei Mu') !== false && stripos($selectedname, 'Zhu Ru') !== false)
        || stripos($selectedname, 'Cool and Transform Phlegm Heat') !== false
        || stripos($selectedname, 'Cool and Transform Phlegm-Heat') !== false
    );
$istopicfourteen = $selected !== null
    && (
        (stripos($selectedname, 'Ban Xia') !== false && stripos($selectedname, 'Tian Nan Xing') !== false && stripos($selectedname, 'Xuan Fu Hua') !== false)
        || stripos($selectedname, 'Warm and Transform Phlegm Cold') !== false
        || stripos($selectedname, 'Warm and Transform Phlegm-Cold') !== false
    );
$istopicfifteen = $selected !== null
    && (
        (stripos($selectedname, 'Jie Geng') !== false && stripos($selectedname, 'Xing Ren') !== false && stripos($selectedname, 'Sang Bai Pi') !== false)
        || stripos($selectedname, 'Relieve Coughing and Wheezing') !== false
        || stripos($selectedname, 'Relieve Cough and Wheezing') !== false
    );
$istopicsixteen = $selected !== null
    && (
        (stripos($selectedname, 'Huo Xiang') !== false && stripos($selectedname, 'Cang Zhu') !== false && stripos($selectedname, 'Bai Dou Kou') !== false)
        || stripos($selectedname, 'Aromatic herbs for transforming dampness') !== false
        || stripos($selectedname, 'Aromatic Herbs that Transform Dampness') !== false
        || stripos($selectedname, 'Aromatics for Damp') !== false
    );
$istopicseventeen = $selected !== null
    && (
        (stripos($selectedname, 'Ji Nei Jin') !== false && stripos($selectedname, 'Lai Fu Zi') !== false && stripos($selectedname, 'Shan Zha') !== false)
        || stripos($selectedname, 'Herbs that Relieve Food Stagnation') !== false
        || stripos($selectedname, 'Relieve Food Stagnation') !== false
        || stripos($selectedname, 'Reduce Food Stagnation') !== false
    );
$istopiceighteen = $selected !== null
    && (
        (stripos($selectedname, 'Chen Pi') !== false && stripos($selectedname, 'Zhi Shi') !== false && stripos($selectedname, 'Xiang Fu') !== false && stripos($selectedname, 'Chuan Lian Zi') !== false)
        || stripos($selectedname, 'Herbs that Regulate the Qi') !== false
        || stripos($selectedname, 'Regulate the Qi') !== false
        || stripos($selectedname, 'Regulate Qi') !== false
    );
$istopicnineteen = $selected !== null
    && (
        (stripos($selectedname, 'Tao Ren') !== false
            && (stripos($selectedname, 'Ru Xiang') !== false || stripos($selectedname, 'Ru Xian') !== false)
            && stripos($selectedname, 'Dan Shen') !== false
            && stripos($selectedname, 'Ze Lan') !== false)
        || stripos($selectedname, 'Invigorate the Blood and Move blood stasis') !== false
        || stripos($selectedname, 'Invigorate the Blood and Move Blood Stasis') !== false
        || stripos($selectedname, 'Invigorate Blood and Dispel Stasis') !== false
    );
$istopictwenty = $selected !== null
    && (
        (stripos($selectedname, 'San Qi') !== false && stripos($selectedname, 'Pu Huang') !== false && stripos($selectedname, 'Bai Ji') !== false && stripos($selectedname, 'Huai Mi') !== false)
        || stripos($selectedname, 'Regulate the Blood and stop bleeding') !== false
        || stripos($selectedname, 'Regulate Blood and Stop Bleeding') !== false
        || stripos($selectedname, 'Herbs that Stop Bleeding') !== false
    );
$istopictwentyone = $selected !== null
    && (
        ((stripos($selectedname, 'Rou Gui') !== false)
            && stripos($selectedname, 'Fu Zi') !== false
            && (stripos($selectedname, 'Gan Jiang') !== false || stripos($selectedname, 'Gao Jiang') !== false)
            && stripos($selectedname, 'Wu Zhu Yu') !== false
            && (stripos($selectedname, 'Xiao Hui Xiang') !== false || stripos($selectedname, 'Xiao Hu Xiang') !== false))
        || stripos($selectedname, 'Herbs That Warm the Interior and Expel Cold') !== false
        || stripos($selectedname, 'Warm the Interior and Expel Cold') !== false
        || stripos($selectedname, 'Warm Interior') !== false
    );
$istopictwentythree = $selected !== null
    && (
        (stripos($selectedname, 'Ren Shen') !== false
            && stripos($selectedname, 'Dang Shen') !== false
            && stripos($selectedname, 'Huang Qi') !== false
            && stripos($selectedname, 'Shan Yao') !== false
            && (stripos($selectedname, 'Yi Tang') !== false || stripos($selectedname, 'Yi tang') !== false))
        || stripos($selectedname, 'Herbs That Tonify Qi') !== false
        || stripos($selectedname, 'Herbs that Tonify Qi') !== false
        || stripos($selectedname, 'Tonify Qi') !== false
    );
$istopictwentyfour = $selected !== null
    && (
        (stripos($selectedname, 'Dang Gui') !== false
            && stripos($selectedname, 'Shu Di Huang') !== false
            && stripos($selectedname, 'E Jiao') !== false
            && stripos($selectedname, 'Bai Shao') !== false)
        || stripos($selectedname, 'Herbs That Tonify Blood') !== false
        || stripos($selectedname, 'Herbs that Tonify Blood') !== false
        || stripos($selectedname, 'Tonify Blood') !== false
    );
$istopictwentyfive = $selected !== null
    && (
        (stripos($selectedname, 'Rou Cong Rong') !== false
            && stripos($selectedname, 'Lu Rong') !== false
            && stripos($selectedname, 'Du Zhong') !== false
            && stripos($selectedname, 'Tu Si Zi') !== false
            && stripos($selectedname, 'Yin Yang Huo') !== false)
        || stripos($selectedname, 'Herbs That Tonify Yang') !== false
        || stripos($selectedname, 'Herbs that Tonify Yang') !== false
        || stripos($selectedname, 'Tonify Yang') !== false
    );
$istopictwentysix = $selected !== null
    && (
        (stripos($selectedname, 'Mai Men Dong') !== false
            && stripos($selectedname, 'Shi Hu') !== false
            && stripos($selectedname, 'Tian Men Dong') !== false
            && stripos($selectedname, 'Bei Sha Shen') !== false
            && stripos($selectedname, 'Nu Zhen Zi') !== false)
        || stripos($selectedname, 'Herbs That Tonify Yin') !== false
        || stripos($selectedname, 'Herbs that Tonify Yin') !== false
        || stripos($selectedname, 'Tonify Yin') !== false
    );
$istopictwentyseven = $selected !== null
    && (
        (stripos($selectedname, 'Wu Mei') !== false
            && stripos($selectedname, 'Wu Wei Zi') !== false
            && stripos($selectedname, 'Shan Zhu Yu') !== false
            && stripos($selectedname, 'Rou Dou Kou') !== false
            && stripos($selectedname, 'Lian Zi') !== false
            && stripos($selectedname, 'He Zi') !== false)
        || stripos($selectedname, 'Herbs That Stabilize and Bind') !== false
        || stripos($selectedname, 'Herbs that Stabilize and Bind') !== false
        || stripos($selectedname, 'Stabilize and Bind') !== false
    );
$istopictwentyeight = $selected !== null
    && (
        (stripos($selectedname, 'Mu Li') !== false
            && stripos($selectedname, 'Long Gu') !== false
            && (stripos($selectedname, 'Ci Shi') !== false || stripos($selectedname, 'Ci Zhi') !== false)
            && stripos($selectedname, 'Zhen Zhu') !== false
            && stripos($selectedname, 'Zhen Zhu Mu') !== false)
        || stripos($selectedname, 'Herbs That Anchor, Settle and Calm The Spirit') !== false
        || stripos($selectedname, 'Herbs that Anchor, Settle and Calm the Spirit') !== false
        || stripos($selectedname, 'Anchor, Settle and Calm') !== false
    );
$istopictwentynine = $selected !== null
    && (
        (stripos($selectedname, 'Yuan Zhi') !== false
            && stripos($selectedname, 'Suan Zao Ren') !== false
            && stripos($selectedname, 'Ye Jiao Teng') !== false
            && (stripos($selectedname, 'He Huan Pi') !== false || stripos($selectedname, 'He Huang Pi') !== false))
        || stripos($selectedname, 'Herbs that Nourish the Heart and Calm the Spirit') !== false
        || stripos($selectedname, 'Herbs That Nourish the Heart and Calm the Spirit') !== false
        || stripos($selectedname, 'Nourish the Heart and Calm') !== false
    );
$istopicthirty = $selected !== null
    && (
        (stripos($selectedname, 'Shi Chang Pu') !== false && stripos($selectedname, 'Niu Huang') !== false)
        || stripos($selectedname, 'Aromatic Substances that Open the Orifices') !== false
        || stripos($selectedname, 'Aromatic Substances That Open the Orifices') !== false
        || stripos($selectedname, 'Open the Orifices') !== false
    );
$istopicthirtyone = $selected !== null
    && (
        (stripos($selectedname, 'Tian Ma') !== false
            && stripos($selectedname, 'Gou Teng') !== false
            && stripos($selectedname, 'Di Long') !== false
            && stripos($selectedname, 'Shi Jue Ming') !== false
            && stripos($selectedname, 'Dai Zhe Shi') !== false)
        || stripos($selectedname, 'Herbs That Extinguish Wind and Stop Tremors') !== false
        || stripos($selectedname, 'Herbs that Extinguish Wind and Stop Tremors') !== false
        || stripos($selectedname, 'Extinguish Wind and Stop Tremors') !== false
    );

// The numeric prefix is the stable category identity. Do not let a stale or
// shifted Moodle section title select the neighboring card's content.
if ($selectedguidenumber !== null) {
    $istopicone = $selectedguidenumber === 1;
    $istopictwo = $selectedguidenumber === 2;
    $istopicthree = $selectedguidenumber === 3;
    $istopicfour = $selectedguidenumber === 4;
    $istopicfive = $selectedguidenumber === 5;
    $istopicsix = $selectedguidenumber === 6;
    $istopicseven = $selectedguidenumber === 7;
    $istopiceight = $selectedguidenumber === 8;
    $istopicnine = $selectedguidenumber === 9;
    $istopicten = $selectedguidenumber === 10;
    $istopiceleven = $selectedguidenumber === 11;
    $istopictwelve = $selectedguidenumber === 12;
    $istopicthirteen = $selectedguidenumber === 13;
    $istopicfourteen = $selectedguidenumber === 14;
    $istopicfifteen = $selectedguidenumber === 15;
    $istopicsixteen = $selectedguidenumber === 16;
    $istopicseventeen = $selectedguidenumber === 17;
    $istopiceighteen = $selectedguidenumber === 18;
    $istopicnineteen = $selectedguidenumber === 19;
    $istopictwenty = $selectedguidenumber === 20;
    $istopictwentyone = $selectedguidenumber === 21;
    $istopictwentythree = $selectedguidenumber === 23;
    $istopictwentyfour = $selectedguidenumber === 24;
    $istopictwentyfive = $selectedguidenumber === 25;
    $istopictwentysix = $selectedguidenumber === 26;
    $istopictwentyseven = $selectedguidenumber === 27;
    $istopictwentyeight = $selectedguidenumber === 28;
    $istopictwentynine = $selectedguidenumber === 29;
    $istopicthirty = $selectedguidenumber === 30;
    $istopicthirtyone = $selectedguidenumber === 31;
}

if ($isbenskychapterfour) {
    $workspaceintro = $ises
        ? 'Aprende a seleccionar hierbas que drenan Humedad según edema, micción dolorosa, cálculos, ictericia, lactancia, prurito y deficiencia del Bazo.'
        : 'Learn to select Dampness-draining herbs by edema, painful urination, stones, jaundice, lactation, itching and Spleen deficiency.';
} else if ($isbenskychapterseventeen) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir sustancias antiparasitarias según organismo, patrón acompañante, constitución, preparación, identidad botánica y margen de seguridad.'
        : 'Learn to distinguish antiparasitic substances by organism, accompanying pattern, constitution, preparation, botanical identity and safety margin.';
} else if ($isbenskychaptereighteen) {
    $workspaceintro = $ises
        ? 'Distingue sustancias de aplicación externa por exudación, prurito, toxicidad, hinchazón, dolor, preparación, vía y riesgo de absorción sistémica.'
        : 'Distinguish topical substances by seepage, itching, toxicity, swelling, pain, preparation, route and risk of systemic absorption.';
} else if ($isbenskychapternineteen) {
    $workspaceintro = $ises
        ? 'Reconoce sustancias obsoletas por toxicidad inaceptable o daño a especies protegidas, identifica trampas de especie y selecciona sustitutos legales.'
        : 'Recognize substances made obsolete by unacceptable toxicity or harm to protected species, identify species traps and select legal substitutes.';
} else if ($isintroduction) {
    $workspaceintro = $ises
        ? 'Construye una base para estudiar hierbas individuales: historia, identidad, propiedades, meridianos, dirección, combinaciones, dosis, procesamiento y seguridad.'
        : 'Build a foundation for single-herb study: history, identity, properties, channels, direction, combinations, dosage, processing and safety.';
} else if ($isproperties) {
    $workspaceintro = $ises
        ? 'Consulta las 304 hierbas oficiales por sabor, temperatura, meridianos y dosis, con búsqueda y cobertura verificada claramente identificada.'
        : 'Review all 304 official herbs by flavor, temperature, channels and dosage, with search and verified coverage clearly identified.';
} else if ($issafetydispensing) {
    $workspaceintro = $ises
        ? 'Consulta las 304 hierbas oficiales por categoría con dosis, preparación, toxicidad, precauciones de embarazo e interacciones estructuradas, además de reconocimiento de sustancias obsoletas y sustitutos legales.'
        : 'Review all 304 official herbs by category with structured dosage, preparation, toxicity, pregnancy and interaction cautions, plus obsolete-substance recognition and legal substitutes.';
} else if ($isactionsindications) {
    $workspaceintro = $ises
        ? 'Consulta las 304 hierbas oficiales por categoría, manteniendo cada acción unida a su indicación en una fila independiente y señalando con claridad el origen del texto.'
        : 'Review all 304 official herbs by category, keeping every action paired with its indication on a separate row and clearly identifying the text source.';
} else if ($ismixed) {
    $workspaceintro = $ises
        ? 'Integra todas las categorías con casos mezclados: identifica primero el patrón, luego el mecanismo y por último la pista decisiva que separa hierbas parecidas.'
        : 'Integrate every category with mixed cases: identify the pattern first, then the mechanism, and finally the decisive clue that separates similar herbs.';
} else if ($istopicone) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas tibias y acres que liberan el Exterior según sudoración, respiración, dolor, náusea y localización de los síntomas.'
        : 'Learn to distinguish warm, acrid Exterior-releasing herbs by sweating, breathing, pain, nausea and symptom location.';
} else if ($istopictwo) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas frescas y acres que liberan el Exterior según garganta, ojos, erupciones, tos y toxicidad por Calor.'
        : 'Learn to distinguish cool, acrid Exterior-releasing herbs by throat, eye, rash, cough and Heat-toxicity clues.';
} else if ($istopicthree) {
    $workspaceintro = $ises
        ? 'Aprende a elegir la hierba que drena Fuego según el órgano afectado, la intensidad de la sed y la combinación de Calor, irritabilidad y sequedad.'
        : 'Learn to choose a Fire-draining herb by the affected organ, intensity of thirst and the combination of Heat, irritability and dryness.';
} else if ($istopicfour) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas que enfrían Sangre según sangrado, erupciones, lesión de Yin, estasis y nivel de profundidad del Calor.'
        : 'Learn to distinguish Blood-cooling herbs by bleeding, rash, Yin injury, stasis and the depth of Heat.';
} else if ($istopicfive) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir Huang Qin, Huang Lian, Huang Bai y Long Dan Cao según órgano, región corporal y patrón de Calor-Humedad.'
        : 'Learn to distinguish Huang Qin, Huang Lian, Huang Bai and Long Dan Cao by organ, body region and Damp-Heat pattern.';
} else if ($istopicsix) {
    $workspaceintro = $ises
        ? 'Aprende a seleccionar hierbas que resuelven toxicidad según garganta, piel, abscesos, mamas, intestinos y localización de la inflamación.'
        : 'Learn to select toxicity-resolving herbs by throat, skin, abscess, breast, intestinal and inflammation-location clues.';
} else if ($istopicseven) {
    $workspaceintro = $ises
        ? 'Aprende a reconocer Calor por deficiencia y a diferenciar estas hierbas según lesión de Yin, fiebre nocturna, huesos humeantes y enfriamiento de Sangre.'
        : 'Learn to recognize deficiency Heat and distinguish these herbs by Yin injury, night fever, steaming bones and Blood-cooling clues.';
} else if ($istopiceight) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas para Calor de Verano según sed, daño de Qi o líquidos, Humedad, orina y síntomas digestivos.'
        : 'Learn to distinguish Summer-Heat herbs by thirst, Qi or fluid injury, Dampness, urination and digestive clues.';
} else if ($istopicnine) {
    $workspaceintro = $ises
        ? 'Aprende a diferenciar los purgantes descendentes según acumulación de Calor, sequedad, fuerza de purga y participación de Hígado o parásitos.'
        : 'Learn to distinguish downward-draining purgatives by Heat accumulation, dryness, purging strength and Liver or parasite involvement.';
} else if ($istopicten) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir Huo Ma Ren y Yu Li Ren según la presencia de sequedad, deficiencia, estancamiento de Qi, edema y dificultad urinaria.'
        : 'Learn to distinguish Huo Ma Ren and Yu Li Ren by dryness, deficiency, Qi stagnation, edema and difficult urination.';
} else if ($istopiceleven) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir los expulsores drásticos según localización del líquido, naturaleza térmica, fuerza catártica y toxicidad.'
        : 'Learn to distinguish harsh expellants by fluid location, thermal nature, cathartic strength and toxicity.';
} else if ($istopictwelve) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas que expulsan Viento-Humedad según el tipo de Bi, movimiento del dolor, temperatura, región corporal y deficiencia subyacente.'
        : 'Learn to distinguish Wind-Damp-expelling herbs by Bi type, pain movement, temperature, body region and underlying deficiency.';
} else if ($istopicthirteen) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas que enfrían y transforman Flema-Calor según esputo, sequedad, nódulos, opresión torácica, intestino, Estómago y Shen.'
        : 'Learn to distinguish herbs that cool and transform Phlegm-Heat by sputum, dryness, nodules, chest constraint, bowel, Stomach and Shen clues.';
} else if ($istopicfourteen) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas que calientan y transforman Flema-Fría según Humedad, Qi rebelde, Viento-Flema, canales, localización y procesamiento.'
        : 'Learn to distinguish herbs that warm and transform Cold-Phlegm by Dampness, rebellious Qi, Wind-Phlegm, channels, location and processing.';
} else if ($istopicfifteen) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas que alivian tos y sibilancia según dirección del Qi de Pulmón, temperatura, cantidad de Flema, garganta, Estómago, edema y cronicidad.'
        : 'Learn to distinguish cough- and wheeze-relieving herbs by Lung-Qi direction, temperature, Phlegm quantity, throat, Stomach, edema and chronicity.';
} else if ($istopicsixteen) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las aromáticas que transforman Humedad según obstrucción del Jiao Medio, movimiento de Qi, Frío, vómito, embarazo, sibilancia y malaria.'
        : 'Learn to distinguish aromatic Dampness-transforming herbs by Middle-Jiao obstruction, Qi movement, Cold, vomiting, pregnancy, wheezing and malaria clues.';
} else if ($istopicseventeen) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas que reducen estancamiento alimentario según el tipo de alimento, fuerza digestiva, movimiento de Qi, estasis de Sangre, lactancia y deficiencia.'
        : 'Learn to distinguish Food-Stagnation herbs by food type, digestive strength, Qi movement, Blood stasis, lactation and deficiency clues.';
} else if ($istopiceighteen) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir las hierbas que regulan Qi según órgano, dirección, fuerza, temperatura, dolor, Flema, Humedad y acumulación.'
        : 'Learn to distinguish Qi-regulating herbs by organ, direction, strength, temperature, pain, Phlegm, Dampness and accumulation clues.';
} else if ($istopicnineteen) {
    $workspaceintro = $ises
        ? 'Aprende a mover Sangre y resolver estasis según fuerza, temperatura, localización, dolor, masas, menstruación, trauma y movimiento de Qi.'
        : 'Learn to invigorate Blood and resolve stasis by strength, temperature, location, pain, masses, menstruation, trauma and Qi movement.';
} else if ($istopictwenty) {
    $workspaceintro = $ises
        ? 'Aprende a detener sangrado sin perder de vista su causa: Calor, Frío, estasis, trauma, deficiencia o localización en Pulmón, intestino, orina y útero.'
        : 'Learn to stop bleeding without losing sight of its cause: Heat, Cold, stasis, trauma, deficiency or location in Lung, intestine, urine and uterus.';
} else if ($istopictwentyone) {
    $workspaceintro = $ises
        ? 'Aprende a calentar el Interior y expulsar Frío según urgencia, órgano, localización del dolor, colapso de Yang, Qi rebelde y toxicidad.'
        : 'Learn to warm the Interior and expel Cold by urgency, organ, pain location, Yang collapse, rebellious Qi and toxicity.';
} else if ($istopictwentythree) {
    $workspaceintro = $ises
        ? 'Aprende a tonificar Qi distinguiendo fuerza, órgano, dirección, Humedad, líquidos, Exterior, sangrado, dolor y seguridad.'
        : 'Learn to tonify Qi by distinguishing strength, organ, direction, Dampness, fluids, Exterior, bleeding, pain and safety.';
} else if ($istopictwentyfour) {
    $workspaceintro = $ises
        ? 'Aprende a tonificar Sangre distinguiendo fuerza, temperatura, movimiento, Yin, Esencia, sangrado y suavización de Hígado.'
        : 'Learn to tonify Blood by distinguishing strength, temperature, movement, Yin, Essence, bleeding and Liver softening.';
} else if ($istopictwentyfive) {
    $workspaceintro = $ises
        ? 'Aprende a tonificar Yang distinguiendo Esencia, huesos, sexualidad, Vejiga, diarrea, Pulmón, intestino y toxicidad.'
        : 'Learn to tonify Yang by distinguishing Essence, bones, sexuality, Bladder, diarrhea, Lung, bowel and toxicity.';
} else if ($istopictwentysix) {
    $workspaceintro = $ises
        ? 'Aprende a tonificar Yin distinguiendo Pulmón, Corazón, Estómago, Hígado, Riñón, líquidos, Esencia, visión y humectación intestinal.'
        : 'Learn to tonify Yin by distinguishing Lung, Heart, Stomach, Liver, Kidney, fluids, Essence, vision and intestinal moisture.';
} else if ($istopictwentyseven) {
    $workspaceintro = $ises
        ? 'Aprende a estabilizar y contener distinguiendo pérdidas de Pulmón, sudor, intestino, Vejiga y Esencia sin encerrar patógenos.'
        : 'Learn to stabilize and bind by distinguishing Lung, sweat, intestinal, Bladder and Essence leakage without trapping pathogens.';
} else if ($istopictwentyeight) {
    $workspaceintro = $ises
        ? 'Aprende a distinguir sedación directa de Shen, anclaje de Yang de Hígado, miedo, mareo, tinnitus, Calor, pérdidas y masas duras.'
        : 'Learn to distinguish direct Spirit sedation, Liver-Yang anchoring, fear, dizziness, tinnitus, Heat, leakage and hard masses.';
} else if ($istopictwentynine) {
    $workspaceintro = $ises
        ? 'Aprende a nutrir Corazón y calmar Shen distinguiendo Sangre de Hígado, Flema y memoria, colaterales inquietos, depresión y opresión torácica.'
        : 'Learn to nourish the Heart and calm the Spirit by distinguishing Liver Blood, Phlegm and memory, restless collaterals, depression and chest constraint.';
} else if ($istopicthirty) {
    $workspaceintro = $ises
        ? 'Aprende a abrir los orificios distinguiendo cierre por Calor-tóxico de cierre por Flema-Humedad, y nunca confundiendo síndrome cerrado con colapso.'
        : 'Learn to open the orifices by distinguishing Heat-toxin closure from Phlegm-Damp closure—and never confusing Locked-up syndrome with collapse.';
} else if ($istopicthirtyone) {
    $workspaceintro = $ises
        ? 'Aprende a anclar Yang y extinguir Viento distinguiendo deficiencia, Calor, Flema, ojos, colaterales, temblor, espasmo y urgencias neurológicas.'
        : 'Learn to anchor Yang and extinguish Wind by distinguishing deficiency, Heat, Phlegm, eyes, collaterals, tremor, spasm and neurologic emergencies.';
} else {
    $workspaceintro = $ises
        ? 'Aprende a identificar las hierbas centrales de esta categoría mediante sus propiedades, funciones, pistas clínicas decisivas y comparaciones del Board.'
        : 'Learn to identify the core herbs in this category through their properties, functions, decisive clinical clues and Board comparisons.';
}

$topiconeherbs = [
    [
        'name' => 'Ma Huang',
        'latin' => 'Ephedrae Herba',
        'properties' => 'Acrid, slightly bitter; warm',
        'channels' => 'Lung, Bladder',
        'dose' => '3–9 g',
        'identity' => 'The strongest diaphoretic in this group; disseminates Lung Qi, calms wheezing and promotes urination.',
        'clue' => 'Marked chills, no sweating, body aches and wheezing in an Exterior excess presentation.',
        'memory' => 'MA = Maximum opening: Ma Huang throws open the pores when Cold has locked the exterior.',
        'unique' => "Within this Board subset it is the hottest and ranks highest for both sweating and light dispersion in Yang's comparative figures.",
        'formulas' => 'Chief: Ma Huang Tang. Co-chief: Xiao Qing Long Tang with Gui Zhi, and Ma Xing Shi Gan Tang with Shi Gao.',
        'caution' => 'Use cautiously with spontaneous sweating, Yin deficiency, hypertension, insomnia or cardiovascular disease.',
    ],
    [
        'name' => 'Gui Zhi',
        'latin' => 'Cinnamomi Ramulus',
        'properties' => 'Acrid, sweet; warm',
        'channels' => 'Heart, Lung, Bladder',
        'dose' => '3–9 g',
        'identity' => 'Releases the muscle layer, warms and unblocks the channels, supports Yang and harmonizes Nutritive and Defensive Qi.',
        'clue' => 'Exterior Wind-Cold with sweating, or Cold obstructing the channels and circulation.',
        'memory' => 'A twig reaches the branches: Gui Zhi carries warmth through the channels and limbs.',
        'unique' => "Yang ranks it second in warmth in this Board subset. Its Heart-channel and Blood-circulating emphasis distinguishes it from Ma Huang's stronger surface release.",
        'formulas' => 'Chief: Gui Zhi Tang. Co-chief: Xiao Qing Long Tang with Ma Huang.',
        'caution' => 'Use cautiously with febrile disease, Yin deficiency with Heat, hot Blood or pregnancy with Heat signs.',
    ],
    [
        'name' => 'Zi Su Ye',
        'latin' => 'Perillae Folium',
        'properties' => 'Acrid, aromatic; warm',
        'channels' => 'Lung, Spleen',
        'dose' => '3–9 g',
        'identity' => 'Gently releases Wind-Cold while moving Qi, harmonizing the Middle and calming the fetus.',
        'clue' => 'A mild exterior pattern with chest or epigastric fullness, nausea, vomiting or pregnancy-related Qi constraint.',
        'memory' => 'The Perilla leaf moves at the surface and settles what is restless in the Middle—including the fetus.',
        'unique' => "Among the least warm herbs in Yang's warm-herb figure, yet it uniquely combines gentle Exterior release with Middle-Jiao Qi regulation and calming the fetus.",
        'formulas' => 'No chief or co-chief formula from the current Board Domain roster is linked here. Keep the herb profile without adding an off-list formula.',
        'caution' => 'Its aromatic oils are best preserved with a relatively short decoction.',
    ],
    [
        'name' => 'Jing Jie',
        'latin' => 'Schizonepetae Herba',
        'properties' => 'Acrid; slightly warm',
        'channels' => 'Lung, Liver',
        'dose' => '3–10 g',
        'identity' => 'A light, gentle Wind herb that releases the Exterior, vents rashes and benefits itchy skin eruptions.',
        'clue' => 'Superficial Wind with rash or itching; raw herb disperses Wind, while the charred form is used differently for bleeding.',
        'memory' => 'Jing Jie sweeps Wind from the skin; once charred, the same herb turns inward to stop bleeding.',
        'unique' => "It is among the least warm yet tied for second in light dispersion in Yang's figures; processing can shift it from dispersing Wind to stopping bleeding.",
        'formulas' => 'Co-chief: Xiao Feng San with Fang Feng. Supporting ingredient: Yin Qiao San and Zhi Sou San.',
        'caution' => 'Do not confuse the actions of raw Jing Jie, Jing Jie Sui and charred Jing Jie.',
    ],
    [
        'name' => 'Fang Feng',
        'latin' => 'Saposhnikoviae Radix',
        'properties' => 'Acrid, sweet; slightly warm',
        'channels' => 'Bladder, Liver, Spleen',
        'dose' => '4.5–9 g',
        'identity' => 'Expels Wind at greater depth than Jing Jie, overcomes Dampness, alleviates pain and addresses spasms.',
        'clue' => 'Wind-Cold-Damp with muscle heaviness or pain, or Wind affecting several levels of the body.',
        'memory' => 'Fang Feng is the Wind shield: broader and deeper than Jing Jie, especially when Dampness adds heaviness.',
        'unique' => "Its sweet taste moderates pungency; compared with Jing Jie it is less superficial and reaches Dampness and pain in the muscles more effectively.",
        'formulas' => 'Co-chief: Xiao Feng San with Jing Jie. Supporting ingredient: Yu Ping Feng San, Juan Bi Tang and Du Huo Ji Sheng Tang.',
        'caution' => 'Use cautiously when there is Blood or Yin deficiency without an exterior pathogenic factor.',
    ],
    [
        'name' => 'Qiang Huo',
        'latin' => 'Notopterygii Rhizoma seu Radix',
        'properties' => 'Acrid, bitter; warm',
        'channels' => 'Bladder, Kidney',
        'dose' => '3–9 g',
        'identity' => 'Strongly releases Wind-Cold-Damp and relieves pain, especially along the Taiyang region and upper body.',
        'clue' => 'Occipital headache, stiff upper back and generalized aches aggravated by Cold-Damp.',
        'memory' => 'Qiang Huo climbs the back of the head: occiput, neck and upper back reveal its Taiyang route.',
        'unique' => "It ties for the highest exogenous-Dampness score among the Board herbs shown by Yang; its decisive location is the occiput, neck and upper Taiyang region.",
        'formulas' => 'Co-chief: Qiang Huo Sheng Shi Tang with Du Huo.',
        'caution' => 'Drying and dispersing; use cautiously with Yin or Blood deficiency and in patients with depleted fluids.',
    ],
    [
        'name' => 'Bai Zhi',
        'latin' => 'Angelicae Dahuricae Radix',
        'properties' => 'Acrid, aromatic; warm',
        'channels' => 'Lung, Stomach',
        'dose' => '3–9 g',
        'identity' => 'Releases Wind-Cold, opens the nasal passages, relieves Yangming frontal pain and reduces swelling and pus.',
        'clue' => 'Frontal headache with nasal obstruction or thick, foul discharge and sinus fullness.',
        'memory' => 'Bai Zhi lights the front of the face: forehead, eyebrows, nose, teeth and purulent swelling.',
        'unique' => "It ties Qiang Huo for the highest exogenous-Dampness score among these Board herbs, but its rich aroma and Yangming route point to frontal pain, the nose and pus.",
        'formulas' => 'Appears in Cang Er Zi San and Chuan Xiong Cha Tiao San, but another herb leads each formula.',
        'caution' => 'Its warm, drying nature is unsuitable for headache or nasal disease caused primarily by Yin deficiency or Heat.',
    ],
    [
        'name' => 'Sheng Jiang',
        'latin' => 'Zingiberis Rhizoma Recens',
        'properties' => 'Acrid; slightly warm',
        'channels' => 'Lung, Spleen, Stomach',
        'dose' => '3–10 g',
        'identity' => 'Gently releases Wind-Cold, warms the Middle, stops vomiting, warms the Lung and reduces toxicity of certain herbs.',
        'clue' => 'A mild exterior pattern accompanied by nausea, vomiting, cough or Middle-Jiao Cold.',
        'memory' => 'Fresh ginger begins at the front door but returns to the kitchen: Exterior release plus Stomach harmony.',
        'unique' => "It sits in the middle of Yang's warmth comparison, but the text calls it a first-line choice for nausea because it uniquely joins mild Exterior release with Stomach harmony.",
        'formulas' => 'Appears in Gui Zhi Tang and Ju Pi Zhu Ru Tang as a supporting ingredient rather than the chief.',
        'caution' => 'Use cautiously with spontaneous sweating from deficiency or prominent internal Heat.',
    ],
    [
        'name' => 'Xin Yi Hua',
        'latin' => 'Magnoliae Flos',
        'properties' => 'Acrid, aromatic; warm',
        'channels' => 'Lung, Stomach',
        'dose' => '3–9 g, wrapped',
        'identity' => 'Focuses on opening the nasal passages, dispersing Wind-Cold and restoring the sense of smell.',
        'clue' => 'Nasal obstruction, reduced smell and sinus pain where the nose is the dominant problem.',
        'memory' => 'The Magnolia flower opens at the nose: when smell disappears, think Xin Yi Hua.',
        'unique' => "It is among the least warm herbs yet tied with Jing Jie for second in light dispersion in Yang's figures; its most specific target is nasal opening and smell.",
        'formulas' => 'Appears in Cang Er Zi San as a supporting nasal-opening ingredient; Cang Er Zi is the chief herb.',
        'caution' => 'The flower hairs can irritate the throat; traditionally decocted in a cloth bag.',
    ],
];

$windcoldcategoryherbs = [
    [
        'name' => 'Bai Zhi',
        'latin' => 'Angelicae Dahuricae Radix',
        'common' => 'Angelica Root',
        'boardfocus' => true,
        'note' => 'Best known for frontal, eyebrow, dental and gum pain; also addresses swelling, pus and vaginal discharge.',
    ],
    [
        'name' => 'Cong Bai',
        'latin' => 'Allii Fistulosi Bulbus',
        'common' => 'Scallion or Green Onion',
        'boardfocus' => false,
        'primarycategory' => 1,
        'category' => 'Category 1 · Warm, Acrid Herbs that Release the Exterior',
        'reason' => 'Included here because it belongs to the same textbook Wind-Cold family and represents the very early, superficial stage; it is in the overall 2026 guideline but is not one of the nine herbs named in this category heading.',
        'note' => 'Gently releases the Exterior in the very early stage of Wind-Cold and may be used topically for abscesses and sores.',
    ],
    [
        'name' => 'E Bu Shi Cao',
        'latin' => 'Centipedae Herba',
        'common' => 'Centipeda Herb',
        'boardfocus' => false,
        'primarycategory' => 1,
        'category' => 'Textbook category · Warm, Acrid Herbs that Release the Exterior',
        'reason' => 'Included only to recognize a nasal Wind-Cold specialist and distinguish it from Xin Yi Hua and Bai Zhi; this source monograph is outside the 2026 guideline.',
        'note' => 'Expels Wind-Cold, opens the nasal passages and is especially associated with nasal obstruction, discharge and cough.',
    ],
    [
        'name' => 'Fang Feng',
        'latin' => 'Saposhnikoviae Radix',
        'common' => 'Ledebouriella Root',
        'boardfocus' => true,
        'note' => 'Releases the Exterior and is useful for painful obstruction, particularly when Wind predominates.',
    ],
    [
        'name' => 'Gao Ben',
        'latin' => 'Ligustici Rhizoma et Radix',
        'common' => 'Chinese Lovage Root',
        'boardfocus' => false,
        'primarycategory' => 1,
        'category' => 'Category 1 · Warm, Acrid Herbs that Release the Exterior',
        'reason' => 'Included to preserve the high-yield location contrast: Gao Ben targets vertex headache, while Qiang Huo favors the occiput and Bai Zhi the forehead. It is in the overall 2026 guideline but not named in this category heading.',
        'note' => 'Expels Wind-Cold and is strongly associated with headache at the vertex.',
    ],
    [
        'name' => 'Gui Zhi',
        'latin' => 'Cinnamomi Ramulus',
        'common' => 'Cinnamon or Cassia Twig',
        'boardfocus' => true,
        'note' => 'Releases the muscle layer and benefits Wind-Cold-Damp painful obstruction in the joints, limbs and shoulders.',
    ],
    [
        'name' => 'Jing Jie',
        'latin' => 'Schizonepetae Herba',
        'common' => 'Schizonepeta Bud or Stem',
        'boardfocus' => true,
        'note' => 'Releases the Exterior, helps vent early carbuncles or boils and, after appropriate processing, can stop bleeding.',
    ],
    [
        'name' => 'Ma Huang',
        'latin' => 'Ephedrae Herba',
        'common' => 'Ephedra Stem',
        'boardfocus' => true,
        'note' => 'Releases the Exterior, induces sweating, disseminates Lung Qi, calms wheezing and promotes urination.',
    ],
    [
        'name' => 'Qiang Huo',
        'latin' => 'Notopterygii Rhizoma seu Radix',
        'common' => 'Notopterygium Root',
        'boardfocus' => true,
        'note' => 'Best for Wind-Cold-Damp body or joint pain and occipital headache, with an affinity for the Taiyang and Governing Vessel regions.',
    ],
    [
        'name' => 'Sheng Jiang',
        'latin' => 'Zingiberis Rhizoma Recens',
        'common' => 'Fresh Ginger Rhizome',
        'boardfocus' => true,
        'note' => 'Disperses Wind-Cold, warms the Middle and Lung, stops vomiting and reduces the toxicity of certain herbs.',
    ],
    [
        'name' => 'Sheng Jiang Pi',
        'latin' => 'Zingiberis Rhizomatis Recens Cortex',
        'common' => 'Fresh Ginger Rhizome Skin',
        'boardfocus' => false,
        'primarycategory' => 12,
        'category' => 'Category 12 · Herbs that Drain Dampness',
        'reason' => 'Included only to distinguish the peel from Sheng Jiang: the fresh rhizome releases the Exterior and harmonizes the Stomach, whereas the peel primarily promotes urination and reduces edema. The peel is outside the 2026 guideline.',
        'note' => 'The peel has a cooler, more superficial emphasis and promotes urination to reduce edema.',
    ],
    [
        'name' => 'Xi Xin',
        'latin' => 'Asari Herba',
        'common' => 'Chinese Wild Ginger or Asarum',
        'boardfocus' => false,
        'primarycategory' => 1,
        'category' => 'Category 1 · Warm, Acrid Herbs that Release the Exterior',
        'reason' => 'Included because it belongs to the broader Wind-Cold family and adds the distinctive combination of Cold pain, nasal opening and Lung warming. It is in the overall 2026 guideline but not named in this category heading.',
        'note' => 'Treats Wind-Cold pain, especially with Dampness or underlying Kidney-Yang deficiency; warms the Lung and opens the nose.',
    ],
    [
        'name' => 'Xiang Ru',
        'latin' => 'Moslae Herba',
        'common' => 'Elsholtzia',
        'boardfocus' => false,
        'primarycategory' => 1,
        'category' => 'Category 1 · Warm, Acrid Exterior release; cross-reference to Category 8 · Clear Summerheat',
        'reason' => 'Included as the summer bridge: it releases an Exterior pattern while transforming Dampness and promoting urination. This explains its comparison with Wind-Cold herbs and its additional appearance in the Summerheat annex.',
        'note' => 'Releases the Exterior in summer, transforms Dampness, promotes urination and reduces swelling.',
    ],
    [
        'name' => 'Xin Yi Hua',
        'latin' => 'Magnoliae Flos',
        'common' => 'Magnolia Flower',
        'boardfocus' => true,
        'note' => 'Most often used with Wind-Cold and remains a major nasal-opening herb for appropriate rhinitis and sinus patterns.',
    ],
    [
        'name' => 'Zi Su Geng',
        'latin' => 'Perillae Caulis',
        'common' => 'Perilla Stalk',
        'boardfocus' => false,
        'primarycategory' => 18,
        'category' => 'Category 18 · Herbs that Regulate Qi',
        'reason' => 'Included only to distinguish parts of the same plant: Zi Su Ye is the Exterior-releasing leaf, whereas Zi Su Geng is the stalk used mainly to move Qi, relieve chest constraint and calm the fetus. The stalk is outside the 2026 guideline.',
        'note' => 'Moves Qi, relieves chest and epigastric constraint, harmonizes the Middle and helps calm the fetus.',
    ],
    [
        'name' => 'Zi Su Ye',
        'latin' => 'Perillae Folium',
        'common' => 'Perilla Leaf',
        'boardfocus' => true,
        'note' => 'Releases mild Wind-Cold, moves Qi, calms the fetus, relieves morning sickness and may be used for seafood poisoning.',
    ],
];

$windcoldguidelinecheck = static function(array $herb) use ($guidelineherbkeys, $normalizeherbname): bool {
    return isset($guidelineherbkeys[$normalizeherbname((string)$herb['name'])]);
};
$windcoldcategorycheck = static function(array $herb): bool {
    return !empty($herb['boardfocus']) || (int)($herb['primarycategory'] ?? 0) === 1;
};
$windcoldboardherbs = array_values(array_filter($windcoldcategoryherbs, static function(array $herb) use ($windcoldguidelinecheck, $windcoldcategorycheck): bool {
    return $windcoldguidelinecheck($herb) && $windcoldcategorycheck($herb);
}));
$windcoldannexherbs = array_values(array_filter($windcoldcategoryherbs, static function(array $herb) use ($windcoldguidelinecheck, $windcoldcategorycheck): bool {
    return !$windcoldguidelinecheck($herb) || !$windcoldcategorycheck($herb);
}));

$yangcomparisoncharts = [
    [
        'title' => 'Relative warmth',
        'figure' => 'Yang Fig. 2.1',
        'items' => ['Ma Huang' => 100, 'Gui Zhi' => 80, 'Sheng Jiang' => 50, 'Qiang Huo' => 50, 'Bai Zhi' => 30, 'Fang Feng' => 30, 'Jing Jie' => 20, 'Xin Yi Hua' => 20, 'Zi Su Ye' => 20],
    ],
    [
        'title' => 'Sweat-inducing strength',
        'figure' => 'Yang Fig. 2.2',
        'items' => ['Ma Huang' => 100, 'Gui Zhi' => 50, 'Zi Su Ye' => 30, 'Jing Jie' => 20],
    ],
    [
        'title' => 'Light and dispersing quality',
        'figure' => 'Yang Fig. 2.3',
        'items' => ['Ma Huang' => 100, 'Jing Jie' => 80, 'Xin Yi Hua' => 80, 'Fang Feng' => 60, 'Zi Su Ye' => 50, 'Gui Zhi' => 40],
    ],
    [
        'title' => 'Expelling exogenous Dampness',
        'figure' => 'Yang Fig. 2.5',
        'items' => ['Qiang Huo' => 70, 'Bai Zhi' => 70, 'Zi Su Ye' => 40, 'Fang Feng' => 40],
    ],
];

$yangquestions = [
    [
        'question' => 'What is an Exterior syndrome, how is it treated, and how do Exterior-releasing herbs work?',
        'answer' => 'An Exterior syndrome is a conflict between an exogenous pathogen and Defensive Qi in the superficial layer, often involving the Lung and Bladder channels. Treatment expels the pathogen, restores Lung-Qi dispersion and harmonizes Nutritive and Defensive Qi. Acrid herbs move Qi, open the pores and disperse the Lung; warm herbs activate Yang and can produce the controlled light sweat that escorts Wind-Cold outward.',
    ],
    [
        'question' => 'What precautions should be observed when using Exterior-releasing herbs?',
        'answer' => 'Avoid excessive dosage and prolonged use: heavy sweating can consume Qi, Yin and Body Fluids. Reduce the dose for children, older or weak patients, mild patterns and warm weather. Use extra caution with Qi or Yin deficiency, spontaneous sweating, bleeding and pregnancy. The therapeutic goal is a mild whole-body sweat, not exhaustion.',
    ],
    [
        'question' => 'How do Ma Huang and Gui Zhi differ in action, clinical presentation and cautions?',
        'answer' => 'Ma Huang is faster and stronger: it opens tightly closed pores, induces sweating, disseminates Lung Qi and treats severe chills, no sweating, aches and wheezing. Gui Zhi is sweeter and gentler: it warms the Blood and channels, supports Heart Yang and suits milder Cold with slight sweating or channel obstruction. Both require caution with Heat, Yin deficiency, hypertension, heart disease or excessive sweating.',
    ],
    [
        'question' => 'How do raw Ma Huang, honey-prepared Ma Huang and Ma Huang Gen differ?',
        'answer' => 'Raw Ma Huang most strongly releases the Exterior and induces sweat. Honey-prepared Ma Huang is gentler and more moistening, with greater emphasis on directing Lung Qi downward and relieving wheezing. Ma Huang Gen is a different plant part: neutral, sweet and astringent, it stops spontaneous or night sweating rather than releasing the Exterior. Ma Huang Gen is outside this Board category list.',
    ],
    [
        'question' => 'What are the characteristic functions of Gui Zhi beyond releasing the Exterior?',
        'answer' => 'Gui Zhi warms and strengthens Heart Yang, promotes Blood circulation, spreads Yang Qi and unblocks the channels, especially in the limbs. This explains its use for Cold painful obstruction, cold extremities and pain caused by obstruction of Qi and Blood—not only for an Exterior pattern.',
    ],
    [
        'question' => 'How do Jing Jie and Fang Feng differ from the stronger Ma Huang and Gui Zhi?',
        'answer' => 'Jing Jie and Fang Feng are gentler and suit mild Wind-Cold or mixed presentations where forceful sweating is unnecessary. Jing Jie is lighter and more superficial, excels at dispersing Wind and venting itchy eruptions. Fang Feng is sweeter, warmer and deeper; it reaches the muscles and is preferred when Dampness causes pain or heaviness.',
    ],
    [
        'question' => 'How do the part used and processing method change the actions of Jing Jie?',
        'answer' => 'Jing Jie Sui, the flower spike, is lighter and quicker for early-stage Wind. Raw Jing Jie is more pungent and opens closed pores to cause mild sweating. Dry-fried Jing Jie is less dispersing and suits an already open exterior. Charred Jing Jie becomes astringent and is used to stop bleeding. Jing Jie Sui and the processed forms are related preparations, not separate herbs in this Board category list.',
    ],
    [
        'question' => 'How do the different preparations of Sheng Jiang change its action?',
        'answer' => 'Fresh Sheng Jiang mildly releases Wind-Cold, harmonizes the Stomach and is a first-line choice for nausea. Its juice acts faster and stronger for acute nausea, vomiting or Phlegm blocking the orifices. Roasted ginger is warmer and less dispersing, emphasizing Middle-Jiao Cold. Sheng Jiang Pi is the only cold ginger product and promotes urination for edema. These preparations are study distinctions; only Sheng Jiang is in this Board category list.',
    ],
    [
        'question' => 'How do Qiang Huo and Bai Zhi differ by headache location and accompanying pattern?',
        'answer' => 'Qiang Huo follows the Taiyang route to the occiput, neck and upper back, especially when Wind-Cold-Damp produces stiffness and generalized aches. Bai Zhi follows the Yangming route to the forehead, eyebrows, nose and teeth, especially with nasal obstruction, thick discharge, turbidity, swelling or pus.',
    ],
    [
        'question' => 'How do Bai Zhi and Xin Yi Hua differ when nasal obstruction is the main complaint?',
        'answer' => 'Bai Zhi is more strongly ascending, aromatic and drying; select it when frontal Yangming pain, foul or thick discharge, sinus fullness, swelling or pus dominates. Xin Yi Hua is lighter and more moderate; its most specific clue is simply opening the obstructed nose and restoring diminished smell.',
    ],
    [
        'question' => 'Beyond a common cold, which pain, respiratory, edema, skin or ENT presentations can these herbs address?',
        'answer' => 'The group can contribute to Wind-Cold-Damp pain and painful obstruction, cough or wheezing from impaired Lung-Qi dispersion, Wind-Water edema, Wind-related itching and eruptions, and nasal or sinus obstruction. Match the herb to the decisive clue: location, sweating, wheezing, Dampness, rash, nausea, edema or loss of smell.',
    ],
];

$topiconecomparisons = [
    [
        'title' => 'Zi Su Ye vs. Ma Huang',
        'summary' => 'Middle-Jiao Qi, nausea or pregnancy favors Zi Su Ye; strong sweating, wheezing or edema favors Ma Huang.',
        'path' => '/study-guide/comparisons/zi-su-ye-vs-ma-huang.html',
        'path_es' => '/study-guide/comparisons/zi-su-ye-vs-ma-huang-es.html',
    ],
    [
        'title' => 'Fang Feng vs. Jing Jie',
        'summary' => 'Fang Feng reaches deeper Wind with Dampness; Jing Jie is lighter, more superficial and stronger for rash or itching.',
        'path' => '/study-guide/comparisons/fang-feng-vs-jing-jie.html',
    ],
    [
        'title' => 'Qiang Huo vs. Gao Ben',
        'summary' => 'Both enter the Taiyang–Du region; Qiang Huo emphasizes upper-body Wind-Cold-Damp pain and stiffness.',
        'path' => '/study-guide/comparisons/qiang-huo-vs-gao-ben.html',
        'path_es' => '/study-guide/comparisons/qiang-huo-vs-gao-ben-es.html',
    ],
    [
        'title' => 'Xin Yi Hua vs. Hou Po',
        'summary' => 'Magnolia flower with nasal clues indicates Xin Yi Hua; Magnolia bark with fullness and Dampness indicates Hou Po.',
        'path' => '/study-guide/comparisons/xin-yi-hua-vs-hou-po.html',
        'path_es' => '/study-guide/comparisons/xin-yi-hua-vs-hou-po-es.html',
    ],
];

$pageparams = [];
if ($focus !== '') {
    $pageparams['focus'] = $focus;
}
if ($requestedherbcard !== null) {
    $pageparams['herb'] = $requestedherb;
}
if ($ises) {
    $pageparams['lang'] = 'es';
}
$pageurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $pageparams);
$categoryindexparams = $ises ? ['lang' => 'es'] : [];
$categoryindexurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $categoryindexparams);
$benskyfourparams = ['focus' => 'bensky4'];
if ($ises) {
    $benskyfourparams['lang'] = 'es';
}
$benskyfoururl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $benskyfourparams);
$benskyseventeenparams = ['focus' => 'bensky17'];
if ($ises) {
    $benskyseventeenparams['lang'] = 'es';
}
$benskyseventeenurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $benskyseventeenparams);
$benskyeighteenparams = ['focus' => 'bensky18'];
if ($ises) {
    $benskyeighteenparams['lang'] = 'es';
}
$benskyeighteenurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $benskyeighteenparams);
$benskynineteenparams = ['focus' => 'bensky19'];
if ($ises) {
    $benskynineteenparams['lang'] = 'es';
}
$benskynineteenurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $benskynineteenparams);
$benskyfourpreviousurl = null;
$benskyfournexturl = null;
$benskyseventeenpreviousurl = null;
foreach ($studyguidecategories as $benskyfourneighbor) {
    $neighborinternalnumber = (int)$benskyfourneighbor['guide_number'];
    if ($neighborinternalnumber === 31) {
        $neighborparams = ['focus' => (string)$benskyfourneighbor['number']];
        if ($ises) {
            $neighborparams['lang'] = 'es';
        }
        $benskyseventeenpreviousurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $neighborparams);
    }
    if ($neighborinternalnumber !== 11 && $neighborinternalnumber !== 12) {
        continue;
    }
    $neighborparams = ['focus' => (string)$benskyfourneighbor['number']];
    if ($ises) {
        $neighborparams['lang'] = 'es';
    }
    $neighborurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $neighborparams);
    if ($neighborinternalnumber === 11) {
        $benskyfourpreviousurl = $neighborurl;
    } else {
        $benskyfournexturl = $neighborurl;
    }
}
$mixedreviewparams = ['focus' => 'mixed'];
if ($ises) {
    $mixedreviewparams['lang'] = 'es';
}
$mixedreviewurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $mixedreviewparams);
$singleherbcategorypath = [];
foreach ($studyguidecategories as $pathcategory) {
    $pathguidenumber = (int)$pathcategory['guide_number'];
    $pathdisplaynumber = $displaystudyguidenumber($pathguidenumber);
    if ($pathdisplaynumber === null) {
        continue;
    }
    $pathparams = ['focus' => (string)$pathcategory['number']];
    if ($ises) {
        $pathparams['lang'] = 'es';
    }
    $singleherbcategorypath[] = [
        'number' => $pathdisplaynumber,
        'focus' => (string)$pathcategory['number'],
        'title' => $canonicalcategorytitle($pathguidenumber)
            ?: $stripstudyguidenumber((string)$pathcategory['name']),
        'url' => new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $pathparams),
    ];
}
$singleherbcategorypath[] = [
    'number' => 12,
    'focus' => 'bensky4',
    'title' => $ises ? 'Hierbas que drenan Humedad' : 'Herbs that Drain Dampness',
    'url' => $benskyfoururl,
];
$singleherbcategorypath[] = [
    'number' => 32,
    'focus' => 'bensky17',
    'title' => $ises ? 'Hierbas que expulsan parásitos' : 'Herbs that Expel Parasites',
    'url' => $benskyseventeenurl,
];
$singleherbcategorypath[] = [
    'number' => 33,
    'focus' => 'bensky18',
    'title' => $ises ? 'Sustancias para aplicación externa' : 'Substances for Topical Application',
    'url' => $benskyeighteenurl,
];
$singleherbcategorypath[] = [
    'number' => 34,
    'focus' => 'bensky19',
    'title' => $ises ? 'Sustancias obsoletas' : 'Obsolete Substances',
    'url' => $benskynineteenurl,
];
usort($singleherbcategorypath, static fn(array $first, array $second): int => $first['number'] <=> $second['number']);
if (count($singleherbcategorypath) !== 34
        || array_column($singleherbcategorypath, 'number') !== range(1, 34)) {
    throw new coding_exception('The Single Herbs category path must contain cards 1 through 34 exactly once.');
}
$singleherbcurrentfocus = null;
if ($selected !== null && $selectedguidenumber !== null) {
    $singleherbcurrentfocus = (string)$selected['number'];
} else if ($isbenskychapterfour) {
    $singleherbcurrentfocus = 'bensky4';
} else if ($isbenskychapterseventeen) {
    $singleherbcurrentfocus = 'bensky17';
} else if ($isbenskychaptereighteen) {
    $singleherbcurrentfocus = 'bensky18';
} else if ($isbenskychapternineteen) {
    $singleherbcurrentfocus = 'bensky19';
} else if ($ismixed) {
    $singleherbcurrentfocus = 'mixed';
}
$singleherbcurrentindex = null;
if ($singleherbcurrentfocus === 'mixed') {
    $singleherbcurrentindex = 34;
} else if ($singleherbcurrentfocus !== null) {
    foreach ($singleherbcategorypath as $pathindex => $pathitem) {
        if ($pathitem['focus'] === $singleherbcurrentfocus) {
            $singleherbcurrentindex = $pathindex;
            break;
        }
    }
}
$previouscategoryurl = null;
$nextcategoryurl = null;
$previouscategory = null;
$nextcategory = null;
$mixedpreviouscategory = null;
$mixedpreviouscategoryurl = null;
$mixedpreviousdisplayguidenumber = null;
$laststudyguidenumber = $studyguidecategories
    ? max(array_column($studyguidecategories, 'guide_number'))
    : 0;
$lastdisplaystudyguidenumber = $displaystudyguidenumber($laststudyguidenumber) ?? 0;
$lastdisplaystudyguidenumber = max($lastdisplaystudyguidenumber, 34);
$previousdisplayguidenumber = null;
$nextdisplayguidenumber = null;
if ($selected !== null && $selectedguidenumber !== null) {
    $categorynumbers = array_keys($studyguidecategories);
    $currentcategoryindex = array_search($selected['number'], $categorynumbers, true);
    if ($currentcategoryindex !== false && $currentcategoryindex > 0) {
        $previousparams = ['focus' => (string) $categorynumbers[$currentcategoryindex - 1]];
        if ($ises) {
            $previousparams['lang'] = 'es';
        }
        $previouscategory = $studyguidecategories[$categorynumbers[$currentcategoryindex - 1]];
        $previouscategoryurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $previousparams);
        $previousdisplayguidenumber = $displaystudyguidenumber((int)$previouscategory['guide_number']);
    }
    if ($currentcategoryindex !== false && $currentcategoryindex < count($categorynumbers) - 1) {
        $nextparams = ['focus' => (string) $categorynumbers[$currentcategoryindex + 1]];
        if ($ises) {
            $nextparams['lang'] = 'es';
        }
        $nextcategory = $studyguidecategories[$categorynumbers[$currentcategoryindex + 1]];
        $nextcategoryurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $nextparams);
        $nextdisplayguidenumber = $displaystudyguidenumber((int)$nextcategory['guide_number']);
    }
    if ($selectedguidenumber === 11) {
        $nextcategoryurl = $benskyfoururl;
        $nextdisplayguidenumber = 12;
    } else if ($selectedguidenumber === 12) {
        $previouscategoryurl = $benskyfoururl;
        $previousdisplayguidenumber = 12;
    }
    if ($selectedguidenumber === 31) {
        $nextcategoryurl = $benskyseventeenurl;
        $nextdisplayguidenumber = 32;
    }
}
if ($ismixed && $studyguidecategories) {
    $mixedprevioussection = array_key_last($studyguidecategories);
    $mixedpreviouscategory = $studyguidecategories[$mixedprevioussection];
    $mixedpreviousdisplayguidenumber = $displaystudyguidenumber((int)$mixedpreviouscategory['guide_number']);
    $mixedpreviousparams = ['focus' => (string) $mixedprevioussection];
    if ($ises) {
        $mixedpreviousparams['lang'] = 'es';
    }
    $mixedpreviouscategoryurl = $benskynineteenurl;
    $mixedpreviousdisplayguidenumber = 34;
}
$languageparams = [];
if ($focus !== '') {
    $languageparams['focus'] = $focus;
}
if ($requestedherbcard !== null) {
    $languageparams['herb'] = $requestedherb;
}
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $languageparams);
$herbologyurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => 'herbology']);
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', [
    'area' => 'herbology',
    'view' => 'library',
]);
$formulasurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $ises ? ['lang' => 'es'] : []);
$duiyaourl = new moodle_url($ises
    ? '/study-guide/comparisons/dui-yao-es.html'
    : '/study-guide/comparisons/dui-yao.html');
$dashboardurl = new moodle_url('/my/');
$guidelinepath = $ises
    ? '/study-guide/guidelines/chinese-herbal-medicine-es.html'
    : '/study-guide/guidelines/chinese-herbal-medicine.html';
$guidelineurl = new moodle_url($guidelinepath, ['return' => $pageurl->out(false)]);
$rendersingleherbcategorynavigation = static function(string $instance) use (
    $singleherbcategorypath,
    $singleherbcurrentindex,
    $mixedreviewurl,
    $categoryindexurl,
    $ises
): string {
    $currentisreview = $singleherbcurrentindex === 34;
    $currentcategory = $singleherbcurrentindex !== null && !$currentisreview
        ? $singleherbcategorypath[$singleherbcurrentindex]
        : null;
    $previousitem = null;
    $nextitem = null;
    if ($currentisreview) {
        $previousitem = $singleherbcategorypath[33];
    } else if ($singleherbcurrentindex !== null) {
        $previousitem = $singleherbcurrentindex > 0
            ? $singleherbcategorypath[$singleherbcurrentindex - 1]
            : null;
        $nextitem = $singleherbcurrentindex < 33
            ? $singleherbcategorypath[$singleherbcurrentindex + 1]
            : [
                'number' => null,
                'focus' => 'mixed',
                'title' => $ises ? 'Repaso final' : 'Final Review',
                'url' => $mixedreviewurl,
            ];
    }
    $selectid = 'single-herbs-category-jump-' . $instance;
    ob_start();
    ?>
    <nav class="single-herbs-guide__category-path<?php echo $singleherbcurrentindex === null ? ' single-herbs-guide__category-path--index' : ''; ?>" aria-label="<?php echo s($ises ? 'Recorrido de las 34 categorías de hierbas' : '34-category Single Herbs path'); ?>">
        <?php if ($singleherbcurrentindex !== null): ?>
            <?php if ($previousitem !== null): ?>
                <a class="single-herbs-guide__category-path-link" href="<?php echo $previousitem['url']->out(false); ?>">
                    <span><?php echo s(($ises ? '← Anterior · Tarjeta ' : '← Previous · Card ') . $previousitem['number']); ?></span>
                    <strong><?php echo s($previousitem['title']); ?></strong>
                </a>
            <?php else: ?>
                <span class="single-herbs-guide__category-path-link single-herbs-guide__category-path-link--disabled">
                    <span><?php echo s($ises ? 'Inicio del recorrido' : 'Start of path'); ?></span>
                    <strong><?php echo s($ises ? 'Esta es la primera categoría' : 'This is the first category'); ?></strong>
                </span>
            <?php endif; ?>
        <?php endif; ?>
        <div class="single-herbs-guide__category-jump">
            <form method="get" action="<?php echo (new moodle_url('/blocks/myprogress/single-herbs-study-guide.php'))->out(false); ?>" data-single-herbs-category-jump>
                <label for="<?php echo s($selectid); ?>"><?php echo s($ises ? 'Ir a la categoría…' : 'Jump to category…'); ?></label>
                <div>
                    <select id="<?php echo s($selectid); ?>" name="focus">
                        <?php if ($singleherbcurrentindex === null): ?><option value="" selected disabled><?php echo s($ises ? 'Selecciona una de las 34 categorías' : 'Select one of the 34 categories'); ?></option><?php endif; ?>
                        <?php foreach ($singleherbcategorypath as $pathitem): ?>
                            <option value="<?php echo s($pathitem['focus']); ?>"<?php echo $currentcategory !== null && $pathitem['focus'] === $currentcategory['focus'] ? ' selected' : ''; ?>><?php echo s($pathitem['number'] . ' · ' . $pathitem['title']); ?></option>
                        <?php endforeach; ?>
                        <option value="mixed"<?php echo $currentisreview ? ' selected' : ''; ?>><?php echo s($ises ? 'M · Repaso final' : 'M · Final Review'); ?></option>
                    </select>
                    <?php if ($ises): ?><input type="hidden" name="lang" value="es"><?php endif; ?>
                    <button class="single-herbs-guide__back" type="submit"><?php echo s($ises ? 'Abrir' : 'Open'); ?></button>
                </div>
            </form>
            <?php if ($singleherbcurrentindex !== null): ?><a href="<?php echo $categoryindexurl->out(false); ?>"><?php echo s($ises ? 'Ver todas las categorías' : 'View all categories'); ?></a><?php endif; ?>
        </div>
        <?php if ($singleherbcurrentindex !== null): ?>
            <?php if ($nextitem !== null): ?>
                <a class="single-herbs-guide__category-path-link single-herbs-guide__category-path-link--next" href="<?php echo $nextitem['url']->out(false); ?>">
                    <span><?php echo s($nextitem['number'] === null
                        ? ($ises ? 'Siguiente · Repaso final →' : 'Next · Final Review →')
                        : (($ises ? 'Siguiente · Tarjeta ' : 'Next · Card ') . $nextitem['number'] . ' →')); ?></span>
                    <strong><?php echo s($nextitem['title']); ?></strong>
                </a>
            <?php else: ?>
                <span class="single-herbs-guide__category-path-link single-herbs-guide__category-path-link--next single-herbs-guide__category-path-link--disabled">
                    <span><?php echo s($ises ? 'Fin del recorrido' : 'End of path'); ?></span>
                    <strong><?php echo s($ises ? 'Repaso completado' : 'Review completed'); ?></strong>
                </span>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
    <?php
    return (string) ob_get_clean();
};

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$pagetitle = $ises ? 'Guía de estudio de hierbas individuales' : 'Single Herbs Study Guide';
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->requires->js_init_code(<<<'JS'
document.querySelectorAll('[data-single-herbs-category-jump] select').forEach(function(select) {
    select.addEventListener('change', function() {
        if (select.value) {
            select.form.submit();
        }
    });
});
JS);

echo $OUTPUT->header();
$spanishtranslations = $ises ? require(__DIR__ . '/single-herbs-study-guide-es.php') : [];
ob_start();
?>
<style>
.single-herbs-guide{max-width:1160px;margin:24px auto 58px;padding:0 16px;color:#17232a}
.single-herbs-guide :is(a,button):focus-visible{outline:3px solid #185cca;outline-offset:3px}
.single-herbs-guide__top{display:flex;align-items:flex-start;justify-content:space-between;gap:22px;margin-bottom:22px}
.single-herbs-guide__eyebrow{margin:0 0 6px;color:#08746f;font-size:13px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.single-herbs-guide h2{margin:0 0 9px;font-size:clamp(29px,4vw,42px);line-height:1.12}
.single-herbs-guide__lead{max-width:760px;margin:0;color:#5b6b79;font-size:16px;line-height:1.6}
.single-herbs-guide__marker-note{display:inline-flex;align-items:center;gap:8px;margin:7px 0 0;padding:0;border:0;background:transparent;color:#075e54;font-size:13px;font-weight:800;text-align:left;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:3px;cursor:pointer}
.single-herbs-guide__marker-note:hover{color:#0b817d}.single-herbs-guide__marker-count{display:inline-flex;min-width:31px;height:23px;align-items:center;justify-content:center;padding:0 7px;border-radius:999px;background:#dff2ee;color:#0d665e;font-size:11px;text-decoration:none}
.single-herbs-guide__roster-modal[hidden]{display:none!important}.single-herbs-guide__roster-modal{position:fixed;z-index:10000;inset:0;display:flex;align-items:center;justify-content:center;padding:22px;background:rgba(15,23,42,.72)}
.single-herbs-guide__roster-dialog{display:flex;width:min(980px,100%);max-height:min(88vh,850px);flex-direction:column;overflow:hidden;border:1px solid #d4dee4;border-radius:15px;background:#fff;box-shadow:0 24px 70px rgba(15,23,42,.32)}
.single-herbs-guide__roster-header{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:22px 24px 17px;border-bottom:1px solid #e1e8ec;background:linear-gradient(135deg,#eef8f6,#f8fbff)}.single-herbs-guide__roster-header h3{margin:0 0 5px;color:#123246;font-size:24px}.single-herbs-guide__roster-header p{margin:0;color:#5b6b79;line-height:1.45}
.single-herbs-guide__roster-close{display:inline-flex;width:40px;height:40px;flex:0 0 40px;align-items:center;justify-content:center;border:1px solid #bdcbd4;border-radius:50%;background:#fff;color:#334155;font-size:25px;line-height:1;cursor:pointer}.single-herbs-guide__roster-close:hover{background:#edf4f6}
.single-herbs-guide__roster-tools{padding:15px 24px 11px}.single-herbs-guide__roster-search{display:block;color:#17364a;font-size:13px;font-weight:900}.single-herbs-guide__roster-search input{display:block;width:100%;min-height:44px;margin-top:6px;padding:9px 12px;border:1px solid #aebbc5;border-radius:8px;background:#fff;color:#17232a;font-size:15px}.single-herbs-guide__roster-count{margin:9px 0 0;color:#60717e;font-size:13px;font-weight:800}
.single-herbs-guide__roster-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 10px;margin:0;padding:0 24px 24px;overflow:auto;list-style:none}.single-herbs-guide__roster-item[hidden]{display:none}.single-herbs-guide__roster-item a{display:grid;grid-template-columns:55px minmax(0,1fr);gap:10px;align-items:center;min-height:76px;padding:10px 11px;border:1px solid #dce4e8;border-radius:9px;background:#fbfcfd;color:inherit;text-decoration:none}.single-herbs-guide__roster-item a:hover{border-color:#0b817d;background:#f2faf8;text-decoration:none}.single-herbs-guide__roster-id{display:inline-flex;align-items:center;justify-content:center;padding:6px 5px;border-radius:6px;background:#dff2ee;color:#0d665e;font-size:11px;font-weight:900;letter-spacing:.04em}.single-herbs-guide__roster-name{display:block;color:#17364a;font-weight:900}.single-herbs-guide__roster-latin{display:block;margin-top:2px;color:#6b7b87;font-size:12px;font-style:italic}.single-herbs-guide__roster-category{display:block;margin-top:5px;color:#8a461b;font-size:11px;font-weight:800;line-height:1.35}.single-herbs-guide__roster-category b{color:#0d665e;text-transform:uppercase;letter-spacing:.03em}
.single-herbs-guide__roster-empty{margin:0 24px 24px;padding:22px;border:1px dashed #c7d3da;border-radius:10px;background:#f8fafc;color:#52677a;text-align:center;line-height:1.55}.single-herbs-guide__roster-empty[hidden]{display:none}
.single-herbs-guide__actions{display:flex;flex-direction:column;gap:9px;align-items:flex-end}
.single-herbs-guide__module-switcher,.single-herbs-guide__utility-actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}
.single-herbs-guide__back{display:inline-flex;min-height:42px;align-items:center;padding:9px 14px;border:1px solid #cbd5dc;border-radius:8px;background:#fff;color:#334155;font-weight:800;text-decoration:none;white-space:nowrap}
.single-herbs-guide__back:hover{background:#f1f5f9;text-decoration:none}
.single-herbs-guide__back--active{border-color:#0b817d;background:#0b817d;color:#fff}.single-herbs-guide__back--active:hover{background:#0b817d;color:#fff}
.single-herbs-guide__category-path{display:grid;grid-template-columns:minmax(0,1fr) minmax(310px,1.15fr) minmax(0,1fr);gap:12px;align-items:stretch;margin:0 0 20px;padding:13px;border:1px solid #d5e0e5;border-radius:12px;background:#fff;box-shadow:0 4px 14px rgba(15,23,42,.06)}
.single-herbs-guide__category-path--index{grid-template-columns:minmax(300px,640px);justify-content:center}.single-herbs-guide__category-path-link{display:flex;min-height:68px;flex-direction:column;justify-content:center;padding:10px 13px;border:1px solid #dbe4e9;border-radius:9px;background:#fff;color:#17344a;text-decoration:none}.single-herbs-guide__category-path-link:hover{border-color:#f77e25;color:#17344a;text-decoration:none}.single-herbs-guide__category-path-link span{color:#64748b;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.single-herbs-guide__category-path-link strong{margin-top:4px;color:#e66716;font-size:13px;line-height:1.35}.single-herbs-guide__category-path-link--next{text-align:right}.single-herbs-guide__category-path-link--disabled{background:#f8fafb;opacity:.72}.single-herbs-guide__category-path-link--disabled strong{color:#64748b}
.single-herbs-guide__category-jump{display:flex;flex-direction:column;justify-content:center;gap:5px}.single-herbs-guide__category-jump form>label{display:block;margin:0 0 4px;color:#64748b;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.single-herbs-guide__category-jump form>div{display:flex;gap:7px}.single-herbs-guide__category-jump select{width:100%;min-width:0;min-height:42px;padding:8px 34px 8px 10px;border:1px solid #aebdc7;border-radius:8px;background:#fff;color:#17232a;font-weight:750}.single-herbs-guide__category-jump>a{color:#075e54;font-size:12px;font-weight:800;text-align:center;text-decoration:none}.single-herbs-guide__category-jump>a:hover{text-decoration:underline}
.single-herbs-guide__overview{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:24px;align-items:center;margin-bottom:24px;padding:23px 26px;border-left:5px solid #0b817d;border-radius:12px;background:#eef8f6;box-shadow:0 3px 12px rgba(15,23,42,.05)}
.single-herbs-guide__overview h3{margin:0 0 7px;font-size:21px}.single-herbs-guide__overview p{margin:0;color:#52677a;line-height:1.55}
.single-herbs-guide__count{display:flex;min-width:116px;flex-direction:column;align-items:center;padding:13px 18px;border-radius:10px;background:#fff;color:#0f5f58}
.single-herbs-guide__count strong{font-size:29px;line-height:1}.single-herbs-guide__count span{margin-top:5px;font-size:12px;font-weight:800;text-transform:uppercase}
.single-herbs-guide__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:14px}
.single-herbs-guide__category{display:flex;min-height:155px;flex-direction:column;padding:18px;border:1px solid #ead8ca;border-top:4px solid #f77e25;border-radius:11px;background:#fff7f0;color:inherit;text-decoration:none;box-shadow:0 2px 8px rgba(15,23,42,.05);transition:transform .15s ease,box-shadow .15s ease}
.single-herbs-guide__category:hover{color:inherit;text-decoration:none;transform:translateY(-2px);box-shadow:0 7px 18px rgba(15,23,42,.1)}.single-herbs-guide__category-link{display:flex;flex:1;flex-direction:column;color:inherit;text-decoration:none}.single-herbs-guide__category-link:hover{color:inherit;text-decoration:none}
.single-herbs-guide__category-number{display:inline-flex;width:34px;height:34px;align-items:center;justify-content:center;margin-bottom:12px;border-radius:50%;background:#fff0e5;color:#a64f12;font-weight:900}
.single-herbs-guide__category-reference{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}.single-herbs-guide__category-reference .single-herbs-guide__category-number{flex:0 0 34px;margin-bottom:0}.single-herbs-guide__bensky-chapter{display:inline-flex;align-items:center;padding:5px 9px;border:1px solid #c9ded9;border-radius:999px;background:#eef8f6;color:#0d665e;font-size:11px;font-weight:900;letter-spacing:.02em}
.single-herbs-guide__category strong{font-size:16px;line-height:1.35}.single-herbs-guide__category small{margin-top:auto;padding-top:12px;color:#19706a;font-weight:800;line-height:1.45}
.single-herbs-guide__category-roster-trigger{display:block;width:100%;margin-top:auto;padding:12px 0 0;border:0;background:transparent;color:#19706a;font-size:12px;font-weight:800;line-height:1.45;text-align:left;cursor:pointer;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:3px}.single-herbs-guide__category-roster-trigger:hover{color:#0b817d}.single-herbs-guide__category-roster-trigger:after{content:' ⓘ';font-size:11px;text-decoration:none}
.single-herbs-guide__category--mixed .single-herbs-guide__category-number{background:#dff2ee;color:#0d665e}
.single-herbs-guide__category--properties .single-herbs-guide__category-number{background:#dbeafe;color:#1d4ed8}
.single-herbs-guide__category--safety{border-top-color:#b45309;background:linear-gradient(145deg,#fff,#fff8eb)}.single-herbs-guide__category--safety .single-herbs-guide__category-number{background:#ffedd5;color:#9a3412}.single-herbs-guide__category--safety small{color:#9a4a12}
.single-herbs-guide__category--introduction .single-herbs-guide__category-number{background:#ede9fe;color:#6d28d9}
.single-herbs-guide__category--bensky-reference{border-top-color:#f77e25;background:#fff7f0}.single-herbs-guide__category--bensky-reference .single-herbs-guide__category-number{background:#fff0e5;color:#a64f12}.single-herbs-guide__category--bensky-reference small,.single-herbs-guide__category--bensky-reference .single-herbs-guide__category-roster-trigger{color:#19706a}
.single-herbs-guide__empty{padding:34px;border:1px dashed #aeb8c0;border-radius:12px;background:#fff;text-align:center;color:#64748b}
.single-herbs-guide__requested-card{margin:0 0 22px;padding:20px 22px;border:1px solid #c9ded9;border-left:5px solid #0b817d;border-radius:11px;background:#f2faf8}.single-herbs-guide__requested-card h3{margin:2px 0 5px;font-size:23px}.single-herbs-guide__requested-card p{margin:0;color:#52677a;line-height:1.5}.single-herbs-guide__requested-card .single-herbs-guide__eyebrow{margin-bottom:4px}.single-herbs-guide__requested-card-actions{display:flex;gap:9px;flex-wrap:wrap;margin-top:14px}.single-herbs-guide__card-target{scroll-margin-top:24px;animation:single-herb-card-highlight 2.2s ease}.single-herbs-guide__card-target>summary,.single-herbs-guide__card-target.single-herbs-guide__category-herb{box-shadow:0 0 0 4px rgba(11,129,125,.2)}@keyframes single-herb-card-highlight{0%,55%{transform:translateY(-2px)}100%{transform:none}}
.single-herbs-guide__workspace{padding:28px;border:1px solid #d9e2e7;border-top:5px solid #0b817d;border-radius:13px;background:#fff;box-shadow:0 3px 12px rgba(15,23,42,.06)}
.single-herbs-guide__workspace-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #e2e8ec}
.single-herbs-guide__workspace h3{margin:0 0 8px;font-size:clamp(18px,1.9vw,22px);line-height:1.25}
.single-herbs-guide__workspace-intro{max-width:740px;margin:0;color:#5b6b79;line-height:1.6}
.single-herbs-guide__plan{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin:0;padding:0;list-style:none;counter-reset:guide-step}
.single-herbs-guide__plan li{counter-increment:guide-step;position:relative;min-height:105px;padding:18px 18px 18px 58px;border:1px solid #dde4e8;border-radius:10px;background:#f9fbfc}
.single-herbs-guide__plan li:before{content:counter(guide-step);position:absolute;left:17px;top:17px;display:flex;width:28px;height:28px;align-items:center;justify-content:center;border-radius:50%;background:#176b70;color:#fff;font-weight:900}
.single-herbs-guide__plan strong{display:block;margin-bottom:5px}.single-herbs-guide__plan span{color:#60717e;font-size:14px;line-height:1.45}
.single-herbs-guide__note{margin:20px 0 0;padding:15px 17px;border-radius:9px;background:#f0f5ff;color:#40536b;line-height:1.5}
.single-herbs-guide__section{margin-top:28px}.single-herbs-guide__section h4{margin:0 0 7px;color:#123246;font-size:22px}.single-herbs-guide__section-lead{margin:0 0 16px;color:#60717e;line-height:1.5}
.single-herbs-guide__pattern{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:18px}
.single-herbs-guide__pattern article{padding:17px;border-radius:10px;background:#f7fafb;border:1px solid #dce4e8}.single-herbs-guide__pattern strong{display:block;margin-bottom:6px;color:#0f5f58}.single-herbs-guide__pattern span{color:#5b6b79;font-size:14px;line-height:1.45}
.single-herbs-guide__memory-path{margin-top:18px;padding:19px 21px;border-left:5px solid #0b817d;border-radius:10px;background:linear-gradient(135deg,#eef8f6,#f8fbff);color:#405565;line-height:1.6}.single-herbs-guide__memory-path strong{color:#0d665e}.single-herbs-guide__memory-path p{margin:0}.single-herbs-guide__memory-path p+p{margin-top:8px}.single-herbs-guide__memory-line{font-size:17px;font-weight:900;color:#123246!important}
.single-herbs-guide__house{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px 18px;margin:13px 0;padding:0;list-style:none}.single-herbs-guide__house li{color:#52677a}.single-herbs-guide__house strong{color:#17364a}
.single-herbs-guide__catalog-heading{margin:25px 0 0;color:#123246;font-size:19px}.single-herbs-guide__catalog-intro{margin:6px 0 14px;color:#60717e;line-height:1.5}.single-herbs-guide__category-herbs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:0;padding:0;list-style:none;counter-reset:wind-cold-herb}.single-herbs-guide__category-herb{counter-increment:wind-cold-herb;position:relative;padding:14px 15px 14px 48px;border:1px solid #dde4e8;border-radius:9px;background:#fbfcfd}.single-herbs-guide__category-herb:before{content:counter(wind-cold-herb);position:absolute;left:14px;top:14px;display:flex;width:24px;height:24px;align-items:center;justify-content:center;border-radius:50%;background:#eaf5f3;color:#0d665e;font-size:12px;font-weight:900}.single-herbs-guide__category-herb--core{border-left:4px solid #0b817d;background:#f7fcfb}.single-herbs-guide__category-herb:not(.single-herbs-guide__category-herb--core){border-left:4px solid #d97706;background:#fffaf2}.single-herbs-guide__category-herb-head{display:flex;align-items:flex-start;gap:7px;flex-wrap:wrap}.single-herbs-guide__category-herb-head strong{color:#17364a}.single-herbs-guide__category-herb-head em{color:#6b7b87;font-size:12px}.single-herbs-guide__category-herb-badge{display:inline-flex;padding:2px 7px;border-radius:999px;background:#dff2ee;color:#0d665e;font-size:10px;font-weight:900;text-transform:uppercase}.single-herbs-guide__category-herb-badge--outside{background:#ffedd5;color:#9a3412}.single-herbs-guide__category-herb-common{display:block;margin-top:3px;color:#9a4a12;font-size:12px;font-weight:800}.single-herbs-guide__category-herb p{margin:7px 0 0;color:#52677a;font-size:13px;line-height:1.45}.single-herbs-guide__annex{margin-top:24px;padding-top:20px;border-top:1px dashed #d3dce2}.single-herbs-guide__annex .single-herbs-guide__catalog-heading{margin-top:0;color:#8a461b}
.single-herbs-guide__category-herbs--featured{grid-template-columns:1fr}.single-herbs-guide__category-herbs--continue{counter-reset:wind-cold-herb 1}.single-herbs-guide__category-herbs--continue-two{counter-reset:wind-cold-herb 2}.single-herbs-guide__category-herbs--continue-twelve{counter-reset:wind-cold-herb 12}
.single-herbs-guide__category-herb-context{display:grid;gap:7px;margin-top:10px;padding-top:10px;border-top:1px solid #eadfce}.single-herbs-guide__category-herb-context p{display:grid;grid-template-columns:112px minmax(0,1fr);gap:8px;margin:0}.single-herbs-guide__category-herb-context strong{color:#8a461b;font-size:11px;text-transform:uppercase}.single-herbs-guide__category-herb-context span{color:#52677a;font-size:13px;line-height:1.45}
.single-herbs-guide__annex-toggle{margin-top:28px;overflow:hidden;border:1px solid #e3d7c7;border-left:5px solid #d97706;border-radius:10px;background:#fffaf2}.single-herbs-guide__annex-toggle>summary{position:relative;display:flex;flex-direction:column;gap:3px;padding:16px 52px 16px 18px;color:#8a461b;cursor:pointer;list-style:none}.single-herbs-guide__annex-toggle>summary::-webkit-details-marker{display:none}.single-herbs-guide__annex-toggle>summary:after{content:'+';position:absolute;right:17px;top:50%;display:flex;width:27px;height:27px;align-items:center;justify-content:center;border-radius:50%;background:#ffedd5;color:#9a3412;font-size:20px;font-weight:900;transform:translateY(-50%)}.single-herbs-guide__annex-toggle[open]>summary:after{content:'−'}.single-herbs-guide__annex-toggle>summary strong{font-size:16px}.single-herbs-guide__annex-toggle>summary span{color:#7b695e;font-size:12px;font-weight:700}.single-herbs-guide__annex-toggle-content{padding:0 18px 18px;border-top:1px solid #eadfce}.single-herbs-guide__annex-toggle-content>.single-herbs-guide__catalog-intro{margin-top:14px}
.single-herbs-guide__annex-rows{display:grid;gap:12px}.single-herbs-guide__annex-row{padding:16px 18px;border:1px solid #dce4e8;border-left:5px solid #0b817d;border-radius:10px;background:#f7fcfb}.single-herbs-guide__annex-row--outside{border-color:#eadfce;border-left-color:#d97706;background:#fffaf2}.single-herbs-guide__annex-row .single-herbs-guide__catalog-heading{margin:0;color:#0d665e}.single-herbs-guide__annex-row--outside .single-herbs-guide__catalog-heading{color:#8a461b}.single-herbs-guide__annex-row .single-herbs-guide__catalog-intro{margin:6px 0 10px}.single-herbs-guide__annex-row .single-herbs-guide__chapter-annex-names{margin:0;padding-top:10px;border-top:1px solid #dbe7e4;color:#52677a;font-size:13px;font-weight:700;line-height:1.65}.single-herbs-guide__annex-row--outside .single-herbs-guide__chapter-annex-names{border-top-color:#eadfce}
.single-herbs-guide__functional-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px 12px;margin:0;padding:0;list-style:none}.single-herbs-guide__functional-summary li{display:flex;gap:6px;align-items:flex-start;padding:12px 14px;border:1px solid #dce4e8;border-left:4px solid #0b817d;border-radius:8px;background:#f8fbfb;color:#52677a;line-height:1.45}.single-herbs-guide__functional-summary strong{color:#17364a}.single-herbs-guide__functional-summary span{flex:1}
.single-herbs-guide__yang-charts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.single-herbs-guide__yang-chart{padding:16px;border:1px solid #dce4e8;border-radius:10px;background:#fff}.single-herbs-guide__yang-chart h5{margin:0;color:#17364a;font-size:16px}.single-herbs-guide__yang-chart small{display:block;margin:2px 0 12px;color:#7a647e}.single-herbs-guide__yang-bar{display:grid;grid-template-columns:82px minmax(0,1fr) 30px;gap:8px;align-items:center;margin-top:7px;font-size:11px;color:#4c6070}.single-herbs-guide__yang-bar-track{height:8px;overflow:hidden;border-radius:99px;background:#e9eef2}.single-herbs-guide__yang-bar-fill{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#ef5b72,#f48276)}.single-herbs-guide__yang-bar b{text-align:right;color:#17364a;font-size:11px}.single-herbs-guide__yang-note{margin-top:13px;padding:12px 15px;border-radius:8px;background:#f4f0ff;color:#5b4283;font-size:13px;line-height:1.5}
.single-herbs-guide__formula-leads{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.single-herbs-guide__formula-lead{padding:17px;border:1px solid #d9e2e7;border-left:4px solid #7c3aed;border-radius:10px;background:#faf8ff}.single-herbs-guide__formula-lead h5{margin:0 0 7px;color:#50259a;font-size:17px}.single-herbs-guide__formula-lead p{margin:0;color:#52677a;line-height:1.55}.single-herbs-guide__formula-lead strong{color:#25364a}
.single-herbs-properties__summary{display:flex;justify-content:space-between;gap:20px;align-items:flex-start}.single-herbs-properties__summary h4{margin:0 0 7px}.single-herbs-properties__summary p{max-width:690px;margin:0;color:#60717e;line-height:1.55}.single-herbs-properties__stats{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}.single-herbs-properties__stats span{padding:8px 10px;border-radius:8px;background:#eef6ff;color:#38526e;font-size:12px;font-weight:700}.single-herbs-properties__stats strong{margin-right:4px;color:#174ea6;font-size:16px}.single-herbs-properties__source-note{margin:16px 0;padding:13px 15px;border-left:4px solid #2563eb;border-radius:7px;background:#eff6ff;color:#36536f;font-size:13px;line-height:1.5}.single-herbs-properties__controls{display:grid;grid-template-columns:minmax(240px,1fr) minmax(240px,360px) auto;gap:12px 14px;align-items:end;margin:18px 0}.single-herbs-properties__control-field{display:grid;gap:7px}.single-herbs-properties__controls label{font-weight:800;color:#17364a}.single-herbs-properties__controls input,.single-herbs-properties__controls select{width:100%;min-height:42px;padding:8px 11px;border:1px solid #aebbc5;border-radius:8px;background:#fff;color:#17364a}.single-herbs-properties__controls select{cursor:pointer}.single-herbs-properties__controls input:focus,.single-herbs-properties__controls select:focus{border-color:#2563eb;outline:2px solid rgba(37,99,235,.15)}.single-herbs-properties__results{margin:0 0 12px;color:#60717e;font-size:13px;white-space:nowrap}.single-herbs-properties__table-wrap{overflow:auto;border:1px solid #d9e2e7;border-radius:10px}.single-herbs-properties__table{width:100%;min-width:1100px;border-collapse:collapse;background:#fff;font-size:13px}.single-herbs-properties__table th,.single-herbs-properties__table td{padding:11px 12px;border-bottom:1px solid #e4eaee;text-align:left;vertical-align:top}.single-herbs-properties__table thead th{position:sticky;top:0;background:#edf7f5;color:#153c4b;font-size:12px;text-transform:uppercase;letter-spacing:.03em}.single-herbs-properties__sort-button{display:inline-flex;width:100%;align-items:center;justify-content:space-between;gap:8px;padding:0;border:0;background:transparent;color:inherit;font:inherit;font-weight:inherit;text-align:left;text-transform:inherit;letter-spacing:inherit;cursor:pointer}.single-herbs-properties__sort-button:hover{color:#075eaa}.single-herbs-properties__sort-button:focus-visible{outline:2px solid #2563eb;outline-offset:4px;border-radius:3px}.single-herbs-properties__sort-indicator{font-size:16px;line-height:1;color:#2563eb}.single-herbs-properties__table tbody th{min-width:165px;color:#17364a}.single-herbs-properties__table tbody th a{color:#075eaa;font-weight:900}.single-herbs-properties__table small{display:block;margin-top:3px;color:#6b7b87;font-weight:500}.single-herbs-properties__category{display:inline-block;max-width:230px;padding:5px 8px;border-radius:7px;background:#f0f7ff;color:#174ea6;font-size:11px;font-weight:800;line-height:1.35}.single-herbs-properties__empty-results{padding:20px;text-align:center;color:#64748b}
.single-herbs-safety__topics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:16px 0}.single-herbs-safety__topics article{padding:14px;border:1px solid #eadfce;border-left:4px solid #d97706;border-radius:8px;background:#fffaf2}.single-herbs-safety__topics strong{display:block;margin-bottom:5px;color:#8a461b}.single-herbs-safety__topics span{display:block;color:#68594f;font-size:13px;line-height:1.45}.single-herbs-safety__obsolete{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin:16px 0;padding:17px 19px;border:1px solid #fed7aa;border-left:5px solid #b45309;border-radius:10px;background:#fff7ed;color:#713f12}.single-herbs-safety__obsolete strong{display:block;margin-bottom:5px}.single-herbs-safety__obsolete p{max-width:780px;margin:0;font-size:13px;line-height:1.55}.single-herbs-safety__controls{grid-template-columns:minmax(220px,1fr) minmax(220px,320px) minmax(220px,320px) auto}.single-herbs-safety .single-herbs-properties__table-wrap{overflow:visible}.single-herbs-safety__table{min-width:0;table-layout:fixed}.single-herbs-safety__table thead th{z-index:5;box-shadow:0 2px 0 #cddfdd,0 5px 10px rgba(15,23,42,.08)}.single-herbs-safety__table :is(th,td){overflow-wrap:anywhere}.single-herbs-safety__table :is(th,td):nth-child(1){width:14%}.single-herbs-safety__table :is(th,td):nth-child(2){width:16%}.single-herbs-safety__table :is(th,td):nth-child(3){width:8%}.single-herbs-safety__table :is(th,td):nth-child(4){width:17%}.single-herbs-safety__table :is(th,td):nth-child(5){width:19%}.single-herbs-safety__table :is(th,td):nth-child(6),.single-herbs-safety__table :is(th,td):nth-child(7){width:13%}.single-herbs-safety__table td{line-height:1.45}.single-herbs-safety__floating-head{position:fixed;display:none;overflow:hidden;z-index:1100;border:1px solid #cddfdd;border-radius:9px;background:#edf7f5;box-shadow:0 8px 20px rgba(15,23,42,.16)}.single-herbs-safety__floating-head.is-visible{display:block}.single-herbs-safety__floating-head table{margin:0;table-layout:fixed}.single-herbs-safety__floating-head thead th{position:static;box-sizing:border-box;box-shadow:none}
.single-herbs-ai__controls{grid-template-columns:minmax(240px,1fr) minmax(210px,300px) minmax(210px,280px) auto}.single-herbs-ai__table{min-width:900px;table-layout:fixed}.single-herbs-ai__table :is(th,td){overflow-wrap:anywhere;line-height:1.46}.single-herbs-ai__table :is(th,td):nth-child(1){width:15%}.single-herbs-ai__table :is(th,td):nth-child(2){width:18%}.single-herbs-ai__table :is(th,td):nth-child(3){width:30%}.single-herbs-ai__table :is(th,td):nth-child(4){width:37%}.single-herbs-ai__group[hidden]{display:none}.single-herbs-ai__group+tbody tr:first-child>*{border-top:2px solid #cbdde2}
.single-herbs-guide__yang-deck{max-width:820px;margin:0 auto}.single-herbs-guide__yang-card{display:none;min-height:300px;overflow:hidden;border:1px solid #d7e0e5;border-top:5px solid #7c3aed;border-radius:13px;background:#fff;box-shadow:0 7px 22px rgba(15,23,42,.08)}.single-herbs-guide__yang-card.is-active{display:block}.single-herbs-guide__yang-face{min-height:295px;padding:25px 27px}.single-herbs-guide__yang-face--back{display:none;background:linear-gradient(145deg,#faf8ff,#f6fbfa)}.single-herbs-guide__yang-card.is-flipped .single-herbs-guide__yang-face--front{display:none}.single-herbs-guide__yang-card.is-flipped .single-herbs-guide__yang-face--back{display:block}.single-herbs-guide__yang-label{display:block;margin-bottom:17px;color:#6d28d9;font-size:12px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}.single-herbs-guide__yang-question{margin:0;color:#17364a;font-size:clamp(20px,2.4vw,27px);line-height:1.35}.single-herbs-guide__yang-answer{margin:0;color:#405565;font-size:16px;line-height:1.65}.single-herbs-guide__yang-answer strong{color:#0d665e}.single-herbs-guide__yang-controls{display:grid;grid-template-columns:1fr auto 1fr;gap:10px;align-items:center;margin-top:13px}.single-herbs-guide__yang-control{min-height:42px;padding:9px 14px;border:1px solid #cbd5dc;border-radius:8px;background:#fff;color:#334155;font-weight:900;cursor:pointer}.single-herbs-guide__yang-control:hover{background:#f1f5f9}.single-herbs-guide__yang-control--next{justify-self:end}.single-herbs-guide__yang-control--flip{border-color:#7c3aed;background:#7c3aed;color:#fff}.single-herbs-guide__yang-control--flip:hover{background:#6d28d9}.single-herbs-guide__yang-progress{margin:10px 0 0;color:#60717e;font-size:13px;text-align:center}.single-herbs-guide__scope-note{margin-top:14px;padding:12px 15px;border-radius:8px;background:#fff8e8;color:#725110;line-height:1.5}
.single-herbs-guide__yang-deck.is-consecutive{max-width:920px}.single-herbs-guide__yang-controls.is-consecutive-controls{display:flex;flex-wrap:wrap;gap:9px;margin:0 0 14px}.single-herbs-guide__yang-controls.is-consecutive-controls .single-herbs-guide__yang-control{min-height:40px;border-color:#7c3aed;color:#6d28d9}.single-herbs-guide__yang-controls.is-consecutive-controls .single-herbs-guide__yang-control:first-child{background:#7c3aed;color:#fff}.single-herbs-guide__yang-controls.is-consecutive-controls .single-herbs-guide__yang-control:hover{box-shadow:0 3px 10px rgba(109,40,217,.16)}.single-herbs-guide__yang-question-item{overflow:hidden;margin-bottom:12px;border:1px solid #d7e0e5;border-left:5px solid #7c3aed;border-radius:11px;background:#fff;box-shadow:0 4px 14px rgba(15,23,42,.06)}.single-herbs-guide__yang-question-item summary{position:relative;cursor:pointer;padding:19px 54px 19px 21px;list-style:none}.single-herbs-guide__yang-question-item summary::-webkit-details-marker{display:none}.single-herbs-guide__yang-question-item summary:after{content:'+';position:absolute;right:18px;top:18px;display:flex;width:28px;height:28px;align-items:center;justify-content:center;border-radius:50%;background:#ede9fe;color:#6d28d9;font-size:21px;font-weight:900}.single-herbs-guide__yang-question-item[open] summary:after{content:'−'}.single-herbs-guide__yang-question-item[open]{border-color:#8b5cf6;box-shadow:0 5px 15px rgba(109,40,217,.1)}.single-herbs-guide__yang-question-item .single-herbs-guide__yang-label{margin-bottom:8px}.single-herbs-guide__yang-question-item .single-herbs-guide__yang-question{font-size:clamp(18px,2vw,23px)}.single-herbs-guide__yang-sequence-answer{padding:18px 21px;border-top:1px solid #e3dcfa;background:linear-gradient(145deg,#faf8ff,#f6fbfa)}.single-herbs-guide__yang-sequence-answer .single-herbs-guide__yang-label{margin-bottom:8px}.single-herbs-guide__scope-note{margin-top:14px;padding:12px 15px;border-radius:8px;background:#fff8e8;color:#725110;line-height:1.5}
.single-herbs-intro__callout{padding:22px;border-left:5px solid #7c3aed;border-radius:11px;background:linear-gradient(135deg,#faf8ff,#f3faf8)}.single-herbs-intro__callout h4{margin:0 0 8px}.single-herbs-intro__callout p:last-child{margin:0;color:#405565;line-height:1.65}.single-herbs-intro__section{margin-top:30px}.single-herbs-intro__section>h4{margin:0 0 8px;color:#123246;font-size:22px}.single-herbs-intro__section>h5{margin:22px 0 10px;color:#17364a;font-size:17px}.single-herbs-intro__section>p{color:#52677a;line-height:1.62}.single-herbs-intro__timeline,.single-herbs-intro__profile-grid,.single-herbs-intro__relationships,.single-herbs-intro__special-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px;margin-top:15px}.single-herbs-intro__timeline article,.single-herbs-intro__profile-grid article,.single-herbs-intro__relationships article,.single-herbs-intro__special-grid article,.single-herbs-intro__reading-levels article,.single-herbs-intro__dose-factors article,.single-herbs-intro__processing-grid article,.single-herbs-intro__safety-grid article{padding:15px;border:1px solid #dce4e8;border-radius:9px;background:#f9fbfc}.single-herbs-intro :is(article) strong{display:block;margin-bottom:5px;color:#17364a}.single-herbs-intro :is(article) span,.single-herbs-intro :is(article) p{color:#5b6b79;font-size:13px;line-height:1.5}.single-herbs-intro__taste-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:15px}.single-herbs-intro__taste-grid article{padding:15px;border-left:4px solid #f77e25;border-radius:8px;background:#fff7f0}.single-herbs-intro__nature-scale{display:grid;grid-template-columns:repeat(5,1fr);overflow:hidden;margin:15px 0;border-radius:9px;color:#fff;text-align:center;font-weight:900}.single-herbs-intro__nature-scale span{padding:13px 7px}.single-herbs-intro__nature-scale .is-hot{background:#b91c1c}.single-herbs-intro__nature-scale .is-warm{background:#ea580c}.single-herbs-intro__nature-scale .is-neutral{background:#64748b}.single-herbs-intro__nature-scale .is-cool{background:#0284c7}.single-herbs-intro__nature-scale .is-cold{background:#1d4ed8}.single-herbs-intro__methods{display:flex;gap:7px;flex-wrap:wrap;margin-top:14px}.single-herbs-intro__methods span{padding:7px 10px;border-radius:999px;background:#eaf5f3;color:#0d665e;font-size:12px;font-weight:800}.single-herbs-intro__two-column,.single-herbs-intro__direction-grid,.single-herbs-intro__processing-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:14px}.single-herbs-intro__two-column article,.single-herbs-intro__direction-grid article{padding:17px;border:1px solid #dce4e8;border-radius:10px;background:#fff}.single-herbs-intro__direction-grid strong{color:#6d28d9!important;font-size:17px}.single-herbs-intro__reading-levels{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:14px 0}.single-herbs-intro__dose-factors,.single-herbs-intro__safety-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:9px;margin-top:14px}.single-herbs-intro__workflow{display:grid;gap:8px;margin:14px 0;padding-left:24px}.single-herbs-intro__workflow li{padding:9px 12px;border-radius:7px;background:#f6fafb;color:#405565}.single-herbs-intro .single-herbs-guide__safety{margin-top:15px}.single-herbs-intro .single-herbs-guide__source{margin-top:28px;color:#6b7b87;line-height:1.55}
.single-herbs-intro__special-grid small{display:block;margin-top:8px;color:#6d28d9;font-size:11px;font-weight:700;line-height:1.45}.single-herbs-intro__delivery-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:13px}.single-herbs-intro__delivery-grid article{padding:15px;border:1px solid #dce4e8;border-top:3px solid #0b817d;border-radius:9px;background:#fff}
.single-herbs-guide__profiles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
.single-herbs-guide__profile{overflow:hidden;border:1px solid #d9e2e7;border-top:4px solid #f77e25;border-radius:10px;background:#fff}
.single-herbs-guide__profile summary{position:relative;cursor:pointer;padding:17px 42px 17px 17px;list-style:none}.single-herbs-guide__profile summary::-webkit-details-marker{display:none}.single-herbs-guide__profile summary:after{content:'+';position:absolute;right:16px;top:16px;display:flex;width:24px;height:24px;align-items:center;justify-content:center;border-radius:50%;background:#eaf5f3;color:#0d665e;font-size:19px;font-weight:900}.single-herbs-guide__profile[open] summary:after{content:'−'}
.single-herbs-guide__profile-name{display:block;color:#123246;font-size:18px;font-weight:900}.single-herbs-guide__profile-latin{display:block;margin-top:3px;color:#6b7b87;font-size:12px;font-style:italic}.single-herbs-guide__profile-meta{display:block;margin-top:9px;color:#0d665e;font-size:12px;font-weight:800;line-height:1.4}
.single-herbs-guide__profile-body{padding:0 17px 17px;border-top:1px solid #e5eaed}.single-herbs-guide__profile-body dl{display:grid;grid-template-columns:max-content 1fr;gap:7px 10px;margin:15px 0 0;font-size:13px;line-height:1.45}.single-herbs-guide__profile-body dt{color:#17364a;font-weight:900}.single-herbs-guide__profile-body dd{margin:0;color:#5b6b79}.single-herbs-guide__profile-body .single-herbs-guide__profile-safety{color:#8a461b}
.single-herbs-guide__profile-body .single-herbs-guide__profile-memory{color:#0d665e;font-weight:700}.single-herbs-guide__profile-body .single-herbs-guide__profile-formulas{color:#364f63}
.single-herbs-guide__chapter-atlas>.single-herbs-guide__profiles{grid-template-columns:1fr;gap:16px}.single-herbs-guide__chapter-atlas .single-herbs-guide__profile-body .single-herbs-guide__category-herbs{grid-template-columns:repeat(2,minmax(280px,1fr));gap:12px}.single-herbs-guide__chapter-atlas .single-herbs-guide__category-herb{padding:16px 18px 16px 52px}.single-herbs-guide__chapter-atlas .single-herbs-guide__category-herb:before{left:16px;top:16px}
.single-herbs-guide__chapter-annex{margin-top:18px;padding:17px 19px;border:1px solid #e3d7c7;border-left:5px solid #d97706;border-radius:10px;background:#fffaf2}.single-herbs-guide__chapter-annex h5{margin:0 0 7px;color:#8a461b;font-size:16px}.single-herbs-guide__chapter-annex p{margin:0;color:#68594f;font-size:13px;line-height:1.55}.single-herbs-guide__chapter-annex .single-herbs-guide__chapter-annex-names{margin-top:10px;padding-top:10px;border-top:1px solid #eadfce;color:#52677a;font-weight:700}
.single-herbs-guide__decision{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.single-herbs-guide__decision div{padding:14px;border-left:4px solid #f77e25;border-radius:8px;background:#fff7f0}.single-herbs-guide__decision strong{display:block;margin-bottom:4px}.single-herbs-guide__decision span{color:#68594f;font-size:14px;line-height:1.4}
.single-herbs-guide__comparisons{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.single-herbs-guide__comparison{display:block;padding:17px;border:1px solid #d9e2e7;border-radius:10px;background:#fff;color:inherit;text-decoration:none}.single-herbs-guide__comparison:hover{border-color:#0b817d;color:inherit;text-decoration:none}.single-herbs-guide__comparison strong{display:block;margin-bottom:6px;color:#0d665e}.single-herbs-guide__comparison span{display:block;color:#60717e;font-size:14px;line-height:1.45}.single-herbs-guide__comparison small{display:block;margin-top:10px;color:#185cca;font-weight:800}
.single-herbs-guide__safety{padding:19px 21px;border-left:5px solid #b45309;border-radius:10px;background:#fff7ed;color:#713f12;line-height:1.55}.single-herbs-guide__safety strong{display:block;margin-bottom:6px}
.single-herbs-guide__recall{display:grid;gap:9px}.single-herbs-guide__recall details{border:1px solid #dce4e8;border-radius:9px;background:#fff}.single-herbs-guide__recall summary{cursor:pointer;padding:15px 17px;color:#17364a;font-weight:800}.single-herbs-guide__recall details[open] summary{border-bottom:1px solid #e4eaed;background:#f6fafb}.single-herbs-guide__recall p{margin:0;padding:15px 17px;color:#52677a;line-height:1.5}.single-herbs-guide__source{font-size:13px}
.single-herbs-guide__final-question summary{display:grid;grid-template-columns:110px 1fr;gap:14px;align-items:start;line-height:1.5}.single-herbs-guide__final-number{color:#6d28d9;font-size:12px;font-weight:900;letter-spacing:.05em;text-transform:uppercase}.single-herbs-guide__final-answer{padding:15px 17px;background:linear-gradient(145deg,#faf8ff,#f6fbfa)}.single-herbs-guide__final-answer>strong{display:block;color:#0d665e}.single-herbs-guide__final-answer p{padding:8px 0 0}.single-herbs-guide__final-question[open]{border-color:#8b5cf6;box-shadow:0 4px 12px rgba(109,40,217,.08)}
.single-herbs-guide__unified-questions .single-herbs-guide__final-question summary{grid-template-columns:150px minmax(0,1fr)}.single-herbs-guide__final-number small{display:block;margin-top:3px;color:#8b5cf6;font-size:9px;font-weight:800;letter-spacing:.04em;line-height:1.35;text-transform:uppercase}
.single-herbs-guide__final-controls{display:flex;flex-wrap:wrap;gap:9px;margin:0 0 14px}.single-herbs-guide__final-controls button{min-height:40px;padding:8px 14px;border:1px solid #7c3aed;border-radius:8px;background:#fff;color:#6d28d9;font-weight:900;cursor:pointer}.single-herbs-guide__final-controls button:first-child{background:#7c3aed;color:#fff}.single-herbs-guide__final-controls button:hover{box-shadow:0 3px 10px rgba(109,40,217,.16)}
.single-herbs-guide__bensky-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}.single-herbs-guide__bensky-card{overflow:hidden;border:1px solid #d9e2e7;border-top:4px solid #2563eb;border-radius:11px;background:#fff}.single-herbs-guide__bensky-card h5{margin:0;padding:15px 17px;background:#eff6ff;color:#174ea6}.single-herbs-guide__bensky-card table{width:100%;border-collapse:collapse;font-size:13px}.single-herbs-guide__bensky-card th,.single-herbs-guide__bensky-card td{padding:10px 12px;border-top:1px solid #e4eaee;text-align:left;vertical-align:top;line-height:1.45}.single-herbs-guide__bensky-card th{width:31%;color:#0d665e}.single-herbs-guide__bensky-source{margin-top:14px;padding:12px 15px;border-left:4px solid #2563eb;border-radius:7px;background:#eff6ff;color:#36536f;font-size:13px;line-height:1.5}
@media(max-width:850px){.single-herbs-guide__pattern,.single-herbs-guide__decision,.single-herbs-guide__profiles,.single-herbs-guide__house,.single-herbs-guide__formula-leads,.single-herbs-guide__category-herbs,.single-herbs-guide__yang-charts{grid-template-columns:1fr 1fr}}
@media(max-width:900px){.single-herbs-safety .single-herbs-properties__table-wrap{max-height:calc(100vh - 88px);overflow:auto;overscroll-behavior:contain}.single-herbs-safety__table{min-width:1080px}}
@media(max-width:720px){.single-herbs-guide__top,.single-herbs-guide__workspace-header,.single-herbs-properties__summary{display:block}.single-herbs-guide__actions{align-items:flex-start;margin-top:16px}.single-herbs-guide__module-switcher,.single-herbs-guide__utility-actions{justify-content:flex-start}.single-herbs-guide__category-path{grid-template-columns:1fr 1fr}.single-herbs-guide__category-path--index{grid-template-columns:1fr}.single-herbs-guide__category-jump{grid-column:1/-1;grid-row:1}.single-herbs-guide__overview{grid-template-columns:1fr}.single-herbs-guide__count{align-items:flex-start}.single-herbs-guide__plan,.single-herbs-guide__pattern,.single-herbs-guide__decision,.single-herbs-guide__comparisons,.single-herbs-guide__profiles,.single-herbs-guide__house,.single-herbs-guide__formula-leads,.single-herbs-guide__category-herbs,.single-herbs-guide__yang-charts,.single-herbs-guide__bensky-grid,.single-herbs-guide__functional-summary,.single-herbs-properties__controls,.single-herbs-safety__topics,.single-herbs-guide__chapter-atlas .single-herbs-guide__profile-body .single-herbs-guide__category-herbs{grid-template-columns:1fr}.single-herbs-safety__obsolete{display:block}.single-herbs-safety__obsolete .single-herbs-guide__back{margin-top:12px;white-space:normal}.single-herbs-properties__stats{justify-content:flex-start;margin-top:14px}.single-herbs-properties__results{white-space:normal}.single-herbs-guide__yang-controls{grid-template-columns:1fr 1fr}.single-herbs-guide__yang-control--flip{grid-column:1/-1;grid-row:1}.single-herbs-guide__yang-control--next{justify-self:stretch}.single-herbs-guide__yang-face{padding:21px 19px}.single-herbs-guide__final-question summary{grid-template-columns:1fr;gap:6px}}
@media(max-width:720px){.single-herbs-guide__unified-questions .single-herbs-guide__final-question summary{grid-template-columns:1fr}}
@media(max-width:720px){.single-herbs-guide__roster-modal{padding:10px}.single-herbs-guide__roster-dialog{max-height:94vh}.single-herbs-guide__roster-header{padding:18px 17px 14px}.single-herbs-guide__roster-header h3{font-size:20px}.single-herbs-guide__roster-tools{padding:13px 17px 10px}.single-herbs-guide__roster-list{grid-template-columns:1fr;padding:0 17px 18px}}
@media(max-width:520px){.single-herbs-guide__category-herb-context p{grid-template-columns:1fr;gap:2px}}
@media(max-width:520px){.single-herbs-guide__category-path{grid-template-columns:1fr}.single-herbs-guide__category-jump{grid-column:auto;grid-row:auto}.single-herbs-guide__category-path-link--next{text-align:left}.single-herbs-guide__category-jump form>div{align-items:stretch;flex-direction:column}.single-herbs-guide__back{white-space:normal}}
@media(max-width:900px){.single-herbs-intro__dose-factors,.single-herbs-intro__safety-grid,.single-herbs-intro__delivery-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.single-herbs-intro__reading-levels,.single-herbs-intro__taste-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:600px){.single-herbs-intro__timeline,.single-herbs-intro__profile-grid,.single-herbs-intro__relationships,.single-herbs-intro__special-grid,.single-herbs-intro__reading-levels,.single-herbs-intro__taste-grid,.single-herbs-intro__two-column,.single-herbs-intro__direction-grid,.single-herbs-intro__processing-grid,.single-herbs-intro__dose-factors,.single-herbs-intro__safety-grid,.single-herbs-intro__delivery-grid{grid-template-columns:1fr}.single-herbs-intro__nature-scale{grid-template-columns:1fr}.single-herbs-intro__nature-scale span{padding:9px}}
</style>

<main class="single-herbs-guide">
    <header class="single-herbs-guide__top">
        <div>
            <p class="single-herbs-guide__eyebrow">Chinese Herbology · Study Guide</p>
            <h2>Single Herbs</h2>
            <p class="single-herbs-guide__lead"><?php echo s($ises
                ? 'Conecta patrones de categoría, pistas clínicas decisivas, comparaciones y memoria activa para reconocer cada hierba en el Board.'
                : 'Connect category patterns, decisive clinical clues, comparisons and active recall to recognize each herb on the Board.'); ?></p>
            <button class="single-herbs-guide__marker-note" type="button" data-guideline-roster-open aria-haspopup="dialog" aria-controls="single-herbs-guideline-roster" aria-expanded="false">
                <span><?php echo s($ises ? '* Incluida en el guideline vigente 2026 · ver listado' : '* Included in the current guideline 2026 · view list'); ?></span>
                <span class="single-herbs-guide__marker-count" aria-label="<?php echo s(($ises ? 'Total de hierbas: ' : 'Total herbs: ') . count($guidelineherbroster)); ?>"><?php echo count($guidelineherbroster); ?></span>
            </button>
        </div>
        <nav class="single-herbs-guide__actions" aria-label="Single Herbs Study Guide navigation">
            <div class="single-herbs-guide__module-switcher" aria-label="<?php echo s($ises ? 'Cambiar módulo de Herbología China' : 'Switch Chinese Herbology module'); ?>">
                <a class="single-herbs-guide__back single-herbs-guide__back--active" aria-current="page" href="<?php echo $categoryindexurl->out(false); ?>">Single Herbs</a>
                <a class="single-herbs-guide__back" href="<?php echo $duiyaourl->out(false); ?>">Duì Yào</a>
                <a class="single-herbs-guide__back" href="<?php echo $formulasurl->out(false); ?>"><?php echo $ises ? 'Fórmulas' : 'Formulas'; ?></a>
            </div>
            <div class="single-herbs-guide__utility-actions">
                <a class="single-herbs-guide__back" href="<?php echo $guidelineurl->out(false); ?>"><?php echo s($ises ? '← Guía de Board · Medicina Herbal China' : '← Board Guideline · Chinese Herbal Medicine'); ?></a>
                <a class="single-herbs-guide__back" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
                <a class="single-herbs-guide__back" href="<?php echo $libraryurl->out(false); ?>"><?php echo $ises ? '← Biblioteca' : '← Library'; ?></a>
                <a class="single-herbs-guide__back" href="<?php echo $herbologyurl->out(false); ?>"><?php echo $ises ? '← Guía de estudio' : '← Study Guide'; ?></a>
                <a class="single-herbs-guide__back" href="<?php echo $dashboardurl->out(false); ?>">Dashboard</a>
            </div>
        </nav>
    </header>

    <?php echo $rendersingleherbcategorynavigation('top'); ?>

    <div class="single-herbs-guide__roster-modal" id="single-herbs-guideline-roster" data-guideline-roster-modal data-count-label="<?php echo s($ises ? 'hierbas mostradas' : 'herbs shown'); ?>" hidden>
        <section class="single-herbs-guide__roster-dialog" role="dialog" aria-modal="true" aria-labelledby="single-herbs-guideline-roster-title" aria-describedby="single-herbs-guideline-roster-description" tabindex="-1">
            <header class="single-herbs-guide__roster-header">
                <div>
                    <p class="single-herbs-guide__eyebrow"><?php echo s($ises ? 'Guideline vigente 2026' : 'Current guideline 2026'); ?></p>
                    <h3 id="single-herbs-guideline-roster-title" data-guideline-roster-title><?php echo s($ises ? 'Listado oficial de hierbas' : 'Official herb roster'); ?></h3>
                    <p id="single-herbs-guideline-roster-description" data-guideline-roster-description><?php echo s($ises
                        ? 'Cada hierba tiene un ID estable y su categoría primaria según Bensky. Selecciona una para abrir su ficha.'
                        : 'Each herb has a stable ID and its primary Bensky category. Select one to open its card.'); ?></p>
                </div>
                <button class="single-herbs-guide__roster-close" type="button" data-guideline-roster-close aria-label="<?php echo s($ises ? 'Cerrar listado de hierbas' : 'Close herb roster'); ?>">&times;</button>
            </header>
            <div class="single-herbs-guide__roster-tools">
                <label class="single-herbs-guide__roster-search">
                    <?php echo s($ises ? 'Buscar por ID, pinyin, nombre farmacéutico o categoría de Bensky' : 'Search by ID, pinyin, pharmaceutical name, or Bensky category'); ?>
                    <input type="search" data-guideline-roster-search autocomplete="off" placeholder="<?php echo s($ises ? 'Ej.: 041, Chuan Bei Mu…' : 'Example: 041, Chuan Bei Mu…'); ?>">
                </label>
                <p class="single-herbs-guide__roster-count" data-guideline-roster-count aria-live="polite"><?php echo count($guidelineherbroster) . ' ' . s($ises ? 'hierbas mostradas' : 'herbs shown'); ?></p>
            </div>
            <ol class="single-herbs-guide__roster-list">
                <?php foreach ($guidelineherbroster as $guidelineherb):
                    $rosterparams = ['herb' => $guidelineherb['name']];
                    if ($ises) {
                        $rosterparams['lang'] = 'es';
                    }
                    $rosterurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $rosterparams);
                    $rosterid = str_pad((string)$guidelineherb['id'], 3, '0', STR_PAD_LEFT);
                    $rostersearch = $rosterid . ' ' . $guidelineherb['name'] . ' ' . $guidelineherb['latin'] . ' ' . $guidelineherb['bensky_category'];
                    ?>
                    <li class="single-herbs-guide__roster-item" id="guideline-herb-<?php echo s($rosterid); ?>" data-guideline-roster-item data-guideline-roster-search-value="<?php echo s($rostersearch); ?>" data-guideline-roster-guide="<?php echo (int)$guidelineherb['guide_number']; ?>" data-guideline-roster-special="<?php echo s($guidelineherb['special_focus']); ?>">
                        <a href="<?php echo $rosterurl->out(false); ?>">
                            <span class="single-herbs-guide__roster-id">ID <?php echo s($rosterid); ?></span>
                            <span>
                                <span class="single-herbs-guide__roster-name"><?php echo s($guidelineherb['name']); ?></span>
                                <?php if ($guidelineherb['latin'] !== ''): ?><span class="single-herbs-guide__roster-latin"> (<em><?php echo s($guidelineherb['latin']); ?></em>)</span><?php endif; ?>
                                <span class="single-herbs-guide__roster-category"><b>Bensky</b> · <?php echo s($guidelineherb['bensky_category']); ?></span>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ol>
            <p class="single-herbs-guide__roster-empty" data-guideline-roster-empty hidden><?php echo s($ises
                ? 'Este capítulo no contiene sustancias incluidas en el guideline vigente 2026. Consulta la ficha para reconocimiento histórico, seguridad y sustitutos legales.'
                : 'This chapter contains no substances included in the current 2026 guideline. Open the card for historical recognition, safety, and legal substitutes.'); ?></p>
        </section>
    </div>

    <?php if ($requestedherbcard !== null && empty($requestedherbcard['guideNumber']) && empty($requestedherbcard['specialFocus'])): ?>
        <section class="single-herbs-guide__requested-card" id="requested-herb-card" aria-labelledby="requested-herb-card-title">
            <p class="single-herbs-guide__eyebrow"><?php echo s($ises ? 'Ficha oficial · Appendix A' : 'Official card · Appendix A'); ?></p>
            <h3 id="requested-herb-card-title"><?php echo s($requestedherbcard['name']); ?></h3>
            <p><em><?php echo s($requestedherbcard['latin']); ?></em></p>
            <p><?php echo s($ises
                ? 'Esta hierba pertenece al listado oficial de 304. Su perfil detallado todavía no está incorporado a una categoría de la guía; esta ficha mantiene el enlace válido y permite continuar con las comparaciones disponibles.'
                : 'This herb belongs to the official 304-herb roster. Its detailed profile has not yet been incorporated into a Study Guide category; this card keeps the link valid and provides access to the available comparisons.'); ?></p>
            <div class="single-herbs-guide__requested-card-actions">
                <a class="single-herbs-guide__back" href="/study-guide/comparisons/index<?php echo $ises ? '-es' : ''; ?>.html?q=<?php echo rawurlencode($requestedherbcard['name']); ?>"><?php echo s($ises ? 'Buscar comparaciones →' : 'Search comparisons →'); ?></a>
                <a class="single-herbs-guide__back" href="<?php echo $categoryindexurl->out(false); ?>"><?php echo s($ises ? 'Todas las categorías' : 'All Single Herbs categories'); ?></a>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($selected !== null || $ismixed || $isproperties || $issafetydispensing || $isactionsindications || $isintroduction || $isbenskychapterfour || $isbenskychapterseventeen || $isbenskychaptereighteen || $isbenskychapternineteen): ?>
        <?php
        $selectedbenskychapter = $benskychapterlabel($selectedguidenumber);
        if ($isbenskychapterfour) {
            $focuslabel = ($ises ? 'Tarjeta de la guía 12' : 'Study-guide card 12') . ' · ' . ($ises ? 'Bensky · Capítulo 4' : 'Bensky · Chapter 4');
            $focustitle = $ises ? 'Bensky, capítulo 4 · Hierbas que drenan Humedad' : 'Bensky, Chapter 4 · Herbs that Drain Dampness';
        } else if ($isbenskychapterseventeen) {
            $focuslabel = ($ises ? 'Tarjeta de la guía 32' : 'Study-guide card 32') . ' · ' . ($ises ? 'Bensky · Capítulo 17' : 'Bensky · Chapter 17');
            $focustitle = $ises ? 'Bensky, capítulo 17 · Hierbas que expulsan parásitos' : 'Bensky, Chapter 17 · Herbs that Expel Parasites';
        } else if ($isbenskychaptereighteen) {
            $focuslabel = ($ises ? 'Tarjeta de la guía 33' : 'Study-guide card 33') . ' · ' . ($ises ? 'Bensky · Capítulo 18' : 'Bensky · Chapter 18');
            $focustitle = $ises ? 'Bensky, capítulo 18 · Sustancias para aplicación externa' : 'Bensky, Chapter 18 · Substances for Topical Application';
        } else if ($isbenskychapternineteen) {
            $focuslabel = ($ises ? 'Tarjeta de la guía 34' : 'Study-guide card 34') . ' · ' . ($ises ? 'Bensky · Capítulo 19' : 'Bensky · Chapter 19');
            $focustitle = $ises ? 'Bensky, capítulo 19 · Sustancias obsoletas' : 'Bensky, Chapter 19 · Obsolete Substances';
        } else if ($isintroduction) {
            $focuslabel = $ises ? 'Fundamentos' : 'Foundations';
            $focustitle = $ises ? 'Introducción a las hierbas individuales' : 'Introduction to Single Herbs';
        } else if ($isproperties) {
            $focuslabel = $ises ? 'Referencia Board' : 'Board Reference';
            $focustitle = $ises ? 'Tabla de propiedades de hierbas' : 'Herb Properties Table';
        } else if ($issafetydispensing) {
            $focuslabel = $ises ? 'Referencia de seguridad' : 'Safety Reference';
            $focustitle = $ises ? 'Seguridad y dispensación' : 'Safety and Dispensing';
        } else if ($isactionsindications) {
            $focuslabel = $ises ? 'Referencia clínica' : 'Clinical Reference';
            $focustitle = $ises ? 'Acciones e indicaciones' : 'Actions and Indications';
        } else if ($ismixed) {
            $focuslabel = $ises ? 'Repaso final' : 'Final Review';
            $focustitle = $ises ? 'Repaso final' : 'Final Review';
        } else if ($selectedguidenumber !== null) {
            $focuslabel = ($ises ? 'Tarjeta de la guía ' : 'Study-guide card ') . $selecteddisplayguidenumber
                . ($selectedbenskychapter !== '' ? ' · ' . $selectedbenskychapter : '');
            $focustitle = $canonicalcategorytitle($selectedguidenumber) ?: $stripstudyguidenumber((string)$selected['name']);
        } else {
            $focuslabel = $selected['number'] === 0 ? 'General' : 'Category ' . $selected['number'];
            $focustitle = $selected['name'];
        }
        ?>
        <section class="single-herbs-guide__workspace" aria-labelledby="single-herbs-focus-title">
            <div class="single-herbs-guide__workspace-header">
                <div>
                    <p class="single-herbs-guide__eyebrow"><?php echo s($focuslabel); ?></p>
                    <h3 id="single-herbs-focus-title"><?php echo s($focustitle); ?></h3>
                    <p class="single-herbs-guide__workspace-intro"><?php echo s($workspaceintro); ?></p>
                </div>
            </div>
            <?php if ($isbenskychapterfour): ?>
                <?php require(__DIR__ . '/single-herbs-bensky-chapter-four.php'); ?>
            <?php elseif ($isbenskychapterseventeen): ?>
                <?php require(__DIR__ . '/single-herbs-bensky-chapter-seventeen.php'); ?>
            <?php elseif ($isbenskychaptereighteen): ?>
                <?php require(__DIR__ . '/single-herbs-bensky-chapter-eighteen.php'); ?>
            <?php elseif ($isbenskychapternineteen): ?>
                <?php require(__DIR__ . '/single-herbs-bensky-chapter-nineteen.php'); ?>
            <?php elseif ($isintroduction): ?>
                <?php require(__DIR__ . '/single-herbs-introduction.php'); ?>
            <?php elseif ($isproperties): ?>
                <?php require(__DIR__ . '/single-herbs-properties-table.php'); ?>
            <?php elseif ($issafetydispensing): ?>
                <?php require(__DIR__ . '/single-herbs-safety-dispensing.php'); ?>
            <?php elseif ($isactionsindications): ?>
                <?php require(__DIR__ . '/single-herbs-actions-indications.php'); ?>
            <?php elseif ($ismixed): ?>
                <?php require(__DIR__ . '/single-herbs-mixed-review.php'); ?>
            <?php elseif ($istopicone): ?>
                <section aria-labelledby="topic-one-foundations">
                    <h4 id="topic-one-foundations">Wind-Cold Exterior-releasing herbs: general introduction</h4>
                    <p class="single-herbs-guide__section-lead">Many herbs in this group are acrid and warm. They disperse Wind-Cold, restore the Lung's dispersing function and open the superficial layer.</p>
                    <div class="single-herbs-guide__pattern">
                        <article><strong>Typical presentation</strong><span>Mild fever, severe chills, headache, body or neck pain and no thirst.</span></article>
                        <article><strong>Primary focus</strong><span>The superficial layer of the body and the Lung. The treatment aim is a controlled, light sweat rather than heavy diaphoresis.</span></article>
                        <article><strong>Board distinction</strong><span>Identify the decisive secondary clue: wheezing, sweating, nausea, rash, Damp pain, headache location or nasal obstruction.</span></article>
                    </div>
                    <p class="single-herbs-guide__scope-note"><strong>General precautions:</strong> do not overdose herbs that promote sweating, because excessive dispersion consumes Qi and Yin. Use additional caution with bleeding conditions and pregnancy because these herbs are moving and dispersing.</p>
                    <aside class="single-herbs-guide__memory-path" aria-label="Memory path for warm acrid exterior-releasing herbs">
                        <p class="single-herbs-guide__memory-line">Picture the Exterior as a house.</p>
                        <ul class="single-herbs-guide__house">
                            <li><strong>Roof and walls:</strong> body surface</li>
                            <li><strong>Doors and windows:</strong> pores</li>
                            <li><strong>Ventilation:</strong> Lung</li>
                            <li><strong>Exterior guard:</strong> Wei Qi</li>
                            <li><strong>Intruder:</strong> Wind-Cold</li>
                            <li><strong>Exit strategy:</strong> a light sweat</li>
                        </ul>
                        <p><strong>Acrid disperses, Warm mobilizes, the Lung opens and sweat escorts Wind-Cold out.</strong> This is the mechanism represented by the house image.</p>
                        <p>Walk through the house to remember the group: <strong>Ma Huang opens the locked door; Gui Zhi warms the branches; Zi Su Ye settles the kitchen; Jing Jie sweeps the skin; Fang Feng raises a Wind shield; Qiang Huo climbs the neck; Bai Zhi clears the forehead and sinuses; Sheng Jiang stops at the Stomach; Xin Yi Hua opens at the nose.</strong></p>
                    </aside>
                    <h5 class="single-herbs-guide__catalog-heading">Current Board guideline herbs</h5>
                    <p class="single-herbs-guide__catalog-intro">Study these thirteen official herbs as Category 1. The nine herbs named in the category heading remain the original core, and Cong Bai, Gao Ben, Xi Xin and Xiang Ru are added here because they are also in the current 2026 guideline and share this primary classification.</p>
                    <ol class="single-herbs-guide__category-herbs">
                        <?php foreach ($windcoldboardherbs as $categoryherb): ?>
                            <li class="single-herbs-guide__category-herb single-herbs-guide__category-herb--core">
                                <div class="single-herbs-guide__category-herb-head">
                                    <strong><?php echo s($categoryherb['name']); ?></strong>
                                    <em><?php echo s($categoryherb['latin']); ?></em>
                                    <span class="single-herbs-guide__category-herb-badge">In current guideline 2026</span>
                                </div>
                                <span class="single-herbs-guide__category-herb-common"><?php echo s($categoryherb['common']); ?></span>
                                <p><?php echo s($categoryherb['note']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ol>

                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-yang-title">
                    <h4 id="topic-one-yang-title">Yang comparative fingerprint</h4>
                    <p class="single-herbs-guide__section-lead">Use the bars to see what makes each Board herb stand out relative to its neighbors. The view below keeps only the nine herbs in this Board category.</p>
                    <div class="single-herbs-guide__yang-charts">
                        <?php foreach ($yangcomparisoncharts as $chart): ?>
                            <article class="single-herbs-guide__yang-chart">
                                <h5><?php echo s($chart['title']); ?></h5>
                                <small><?php echo s($chart['figure']); ?></small>
                                <?php foreach ($chart['items'] as $herbname => $value): ?>
                                    <div class="single-herbs-guide__yang-bar">
                                        <span><?php echo s($herbname); ?></span>
                                        <span class="single-herbs-guide__yang-bar-track"><span class="single-herbs-guide__yang-bar-fill" style="width:<?php echo (int) $value; ?>%"></span></span>
                                        <b><?php echo (int) $value; ?></b>
                                    </div>
                                <?php endforeach; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <p class="single-herbs-guide__yang-note"><strong>How to read these bars:</strong> they are relative comparison indices transcribed from Yang, Chapter 2, Figures 2.1, 2.2, 2.3 and 2.5. They are not doses, efficacy percentages or prescribing instructions. Herbs outside this Board category that appear in the original figures are intentionally omitted here.</p>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-bensky-summary-title">
                    <h4 id="topic-one-bensky-summary-title"><?php echo s($ises ? 'Bensky · Resumen de funciones comparativas' : 'Bensky · Summary of Comparative Functions'); ?></h4>
                    <p class="single-herbs-guide__section-lead"><?php echo s($ises
                        ? 'Este mapa amplía la comparación por fuerza, profundidad, localización y especialidad. Complementa a Yang; no sustituye sus perfiles, gráficos ni preguntas.'
                        : 'This map expands the comparison by strength, depth, location and specialty. It supplements Yang; it does not replace Yang profiles, charts or questions.'); ?></p>
                    <div class="single-herbs-guide__bensky-grid">
                        <article class="single-herbs-guide__bensky-card">
                            <h5><?php echo s($ises ? '1 · Las tres más fuertes para dispersar Frío' : '1 · Three stronger Cold-dispersing herbs'); ?></h5>
                            <table><tbody>
                                <tr><th>Ma Huang*</th><td><?php echo s($ises ? 'Abre y difunde Qi de Pulmón en el nivel Wei. Pista: Exterior cerrado, ausencia de sudor y posible sibilancia.' : 'Opens and disseminates Lung Qi at the protective level. Clue: closed Exterior, no sweating and possible wheezing.'); ?></td></tr>
                                <tr><th>Gui Zhi*</th><td><?php echo s($ises ? 'Desbloquea Yang Qi y actúa en nivel nutritivo y capa muscular. Pista: sudor espontáneo o desarmonía Ying-Wei.' : 'Unblocks Yang Qi and works at the nutritive and muscle layers. Clue: spontaneous sweating or Ying-Wei disharmony.'); ?></td></tr>
                                <tr><th>Xi Xin*</th><td><?php echo s($ises ? 'Penetra Frío más profundo, incluso en Shaoyin; abre orificios y alivia dolor. Pista: pulso profundo, dolor por Frío o mucosidad clara y fina.' : 'Pierces more deeply lodged Cold, including Shaoyin; opens orifices and relieves pain. Clue: deep pulse, Cold pain or thin clear mucus.'); ?></td></tr>
                            </tbody></table>
                        </article>
                        <article class="single-herbs-guide__bensky-card">
                            <h5><?php echo s($ises ? '2 · Grupo moderado para Viento-Frío común' : '2 · Moderate group for common Wind-Cold'); ?></h5>
                            <table><tbody>
                                <tr><th>Zi Su Ye*</th><td><?php echo s($ises ? 'La más potente del trío para dispersar Frío; añade turbidez, náusea y movimiento de Qi.' : 'The strongest of this trio for dispersing Cold; adds turbidity, nausea and Qi movement.'); ?></td></tr>
                                <tr><th>Jing Jie*</th><td><?php echo s($ises ? 'Más superficial; favorece piel, prurito y erupciones, y puede emplearse en presentaciones frías o calientes.' : 'More superficial; favors skin, itching and rashes, and can be used in either Cold or Heat presentations.'); ?></td></tr>
                                <tr><th>Fang Feng*</th><td><?php echo s($ises ? 'Alcanza algo más profundo; favorece dolor corporal y Humedad. Con Jing Jie cubre Viento a distintas profundidades.' : 'Reaches somewhat deeper; favors body pain and Dampness. With Jing Jie it covers Wind at different depths.'); ?></td></tr>
                            </tbody></table>
                        </article>
                        <article class="single-herbs-guide__bensky-card">
                            <h5><?php echo s($ises ? '3 · La localización decide la cefalea' : '3 · Headache location decides'); ?></h5>
                            <table><tbody>
                                <tr><th>Qiang Huo*</th><td><?php echo s($ises ? 'Taiyang, occipucio, cuello y parte superior; añade Bi por Viento-Humedad.' : 'Taiyang, occiput, neck and upper body; adds Wind-Damp Bi pain.'); ?></td></tr>
                                <tr><th>Gao Ben*</th><td><?php echo s($ises ? 'Vaso Gobernador, vértex y dolor que puede alcanzar dientes o mandíbula.' : 'Governing Vessel, vertex and pain that may reach the teeth or jaw.'); ?></td></tr>
                                <tr><th>Bai Zhi*</th><td><?php echo s($ises ? 'Yangming: frente, órbita, dientes y congestión nasal.' : 'Yangming: forehead, orbit, teeth and nasal congestion.'); ?></td></tr>
                            </tbody></table>
                        </article>
                        <article class="single-herbs-guide__bensky-card">
                            <h5><?php echo s($ises ? '4 · Especialistas que evitan respuestas genéricas' : '4 · Specialists that prevent generic answers'); ?></h5>
                            <table><tbody>
                                <tr><th>Sheng Jiang*</th><td><?php echo s($ises ? 'Viento-Frío con turbidez de Estómago, náusea o vómito.' : 'Wind-Cold with Stomach turbidity, nausea or vomiting.'); ?></td></tr>
                                <tr><th>Cong Bai*</th><td><?php echo s($ises ? 'Acción muy superficial para una fase muy temprana; desbloquea Yang Qi.' : 'Very superficial action for a very early stage; unblocks Yang Qi.'); ?></td></tr>
                                <tr><th>Xiang Ru*</th><td><?php echo s($ises ? 'Resfriado de verano con Humedad; Bensky la compara con una “Ma Huang* de verano”.' : 'Summer cold with Dampness; Bensky compares it to a “summer Ma Huang*.”'); ?></td></tr>
                                <tr><th>Xin Yi*</th><td><?php echo s($ises ? 'Elección estrecha cuando domina obstrucción nasal.' : 'Narrow specialist when nasal obstruction dominates.'); ?></td></tr>
                            </tbody></table>
                        </article>
                    </div>
                    <div class="single-herbs-guide__safety" style="margin-top:14px">
                        <strong><?php echo s($ises ? 'Suplemento de seguridad Bensky · Xi Xin*' : 'Bensky safety supplement · Xi Xin*'); ?></strong>
                        <?php echo s($ises
                            ? 'Bensky registra sabor acre, naturaleza tibia, entrada en Pulmón-Corazón-Riñón y dosis de estudio de 1–3 g. Destaca que la dosis debe mantenerse baja en polvo, píldoras o preparaciones sin cocción y advierte toxicidad grave por sobredosis o tiempo insuficiente de decocción. El texto histórico señala un máximo de 1.5 g al día cuando no se hierve. Appendix 1 del guideline vigente mantiene el límite general de hasta 3 g. Esta nota es para reconocimiento y seguridad de examen, no para prescripción.'
                            : 'Bensky records an acrid, warm herb entering Lung-Heart-Kidney, with a 1–3 g study dose. It emphasizes a low dose in powders, pills or uncooked preparations and warns of serious toxicity from overdose or insufficient decoction time. The historical text gives a 1.5 g daily maximum when unboiled. The current guideline Appendix 1 retains a general limit of up to 3 g. This is an exam-recognition and safety note, not prescribing guidance.'); ?>
                    </div>
                    <p class="single-herbs-guide__bensky-source"><strong><?php echo s($ises ? 'Fuente y límite:' : 'Source and boundary:'); ?></strong> <?php echo s($ises ? 'síntesis de Bensky et al., Chinese Herbal Medicine: Materia Medica, 3.ª ed., capítulo 1, pp. 22–29 y 82–83, a partir del texto suministrado. Los valores existentes de Yang permanecen sin cambios.' : 'synthesis of Bensky et al., Chinese Herbal Medicine: Materia Medica, 3rd ed., Chapter 1, pp. 22–29 and 82–83, from the supplied text. Existing Yang values remain unchanged.'); ?></p>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-decision-title">
                    <h4 id="topic-one-decision-title">Fast decision map</h4>
                    <p class="single-herbs-guide__section-lead">Begin with the dominant clue, then confirm the properties and channels.</p>
                    <div class="single-herbs-guide__decision">
                        <div><strong>No sweat + wheezing</strong><span>Ma Huang</span></div>
                        <div><strong>Sweating + channel Cold</strong><span>Gui Zhi</span></div>
                        <div><strong>Nausea or pregnancy Qi</strong><span>Zi Su Ye</span></div>
                        <div><strong>Rash or superficial itching</strong><span>Jing Jie</span></div>
                        <div><strong>Wind-Damp muscle pain</strong><span>Fang Feng</span></div>
                        <div><strong>Occiput and upper back</strong><span>Qiang Huo</span></div>
                        <div><strong>Frontal sinus pain or pus</strong><span>Bai Zhi</span></div>
                        <div><strong>Vomiting + mild exterior</strong><span>Sheng Jiang</span></div>
                        <div><strong>Nasal obstruction dominates</strong><span>Xin Yi Hua</span></div>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-profiles-title">
                    <h4 id="topic-one-profiles-title">Individual herb profiles</h4>
                    <p class="single-herbs-guide__section-lead">Each profile compresses the facts into one exam clue, one memorable image and a bridge only to formulas found in the current Board Domain rosters.</p>
                    <div class="single-herbs-guide__profiles">
                        <?php foreach ($topiconeherbs as $herb): ?>
                            <details class="single-herbs-guide__profile"<?php echo $herb['name'] === 'Ma Huang' ? ' open' : ''; ?>>
                                <summary>
                                    <span class="single-herbs-guide__profile-name"><?php echo s($herb['name']); ?></span>
                                    <span class="single-herbs-guide__profile-latin"><?php echo s($herb['latin']); ?></span>
                                    <span class="single-herbs-guide__profile-meta"><?php echo s($herb['properties']); ?> · <?php echo s($herb['channels']); ?></span>
                                </summary>
                                <div class="single-herbs-guide__profile-body">
                                    <dl>
                                        <dt>Dose</dt><dd><?php echo s($herb['dose']); ?></dd>
                                        <dt>Identity</dt><dd><?php echo s($herb['identity']); ?></dd>
                                        <dt>Exam clue</dt><dd><?php echo s($herb['clue']); ?></dd>
                                        <dt>Memory</dt><dd class="single-herbs-guide__profile-memory"><?php echo s($herb['memory']); ?></dd>
                                        <dt>Unique fingerprint</dt><dd><?php echo s($herb['unique']); ?></dd>
                                        <dt>Formula bridge</dt><dd class="single-herbs-guide__profile-formulas"><?php echo s($herb['formulas']); ?></dd>
                                        <dt>Safety</dt><dd class="single-herbs-guide__profile-safety"><?php echo s($herb['caution']); ?></dd>
                                    </dl>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-formula-leads-title">
                    <h4 id="topic-one-formula-leads-title">When the herb leads the formula</h4>
                    <p class="single-herbs-guide__section-lead">These connections are limited to formulas in the current Board Domain rosters in which one of the nine herbs is a chief or co-chief ingredient. Off-list textbook connections are deliberately omitted.</p>
                    <div class="single-herbs-guide__formula-leads">
                        <article class="single-herbs-guide__formula-lead">
                            <h5>Ma Huang</h5>
                            <p><strong>Chief:</strong> Ma Huang Tang. <strong>Co-chief:</strong> Xiao Qing Long Tang with Gui Zhi; Ma Xing Shi Gan Tang with Shi Gao.</p>
                        </article>
                        <article class="single-herbs-guide__formula-lead">
                            <h5>Gui Zhi</h5>
                            <p><strong>Chief:</strong> Gui Zhi Tang, where it releases the muscle layer and harmonizes Ying and Wei. <strong>Co-chief:</strong> Xiao Qing Long Tang with Ma Huang.</p>
                        </article>
                        <article class="single-herbs-guide__formula-lead">
                            <h5>Jing Jie + Fang Feng</h5>
                            <p><strong>Co-chief pair:</strong> Xiao Feng San, where Jing Jie and Fang Feng expel Wind and relieve itching.</p>
                        </article>
                        <article class="single-herbs-guide__formula-lead">
                            <h5>Qiang Huo</h5>
                            <p><strong>Co-chief:</strong> Qiang Huo Sheng Shi Tang with Du Huo for Wind-Damp pain concentrated in the upper body.</p>
                        </article>
                    </div>
                    <p class="single-herbs-guide__scope-note"><strong>Role check:</strong> Fang Feng supports Yu Ping Feng San, Bai Zhi and Xin Yi Hua support Cang Er Zi San, and Sheng Jiang supports Gui Zhi Tang and Ju Pi Zhu Ru Tang. These formulas are on the Board rosters, but the named herb does not lead each formula.</p>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-comparisons-title">
                    <h4 id="topic-one-comparisons-title">High-yield comparisons</h4>
                    <p class="single-herbs-guide__section-lead">Open the complete comparison when two choices appear plausible.</p>
                    <div class="single-herbs-guide__comparisons">
                        <?php foreach ($topiconecomparisons as $comparison): ?>
                            <?php
                            $comparisonpath = $ises && !empty($comparison['path_es']) ? $comparison['path_es'] : $comparison['path'];
                            $comparisonurl = new moodle_url($comparisonpath, [
                                'from' => 'single-herbs',
                                'focus' => (string) $selected['number'],
                            ]);
                            ?>
                            <a class="single-herbs-guide__comparison" href="<?php echo $comparisonurl->out(false); ?>">
                                <strong><?php echo s($comparison['title']); ?></strong>
                                <span><?php echo s($comparison['summary']); ?></span>
                                <small>Open full comparison →</small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-safety-title">
                    <h4 id="topic-one-safety-title">Ma Huang safety checkpoint</h4>
                    <div class="single-herbs-guide__safety"><strong>Ma Huang requires a separate safety review beyond the general category precautions above.</strong>It has important cardiovascular, medication-interaction and regulatory considerations. Professional supervision, individual screening and current local rules are essential.</div>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-yang-questions-title">
                    <h4 id="topic-one-yang-questions-title">Yang questions for this category</h4>
                    <p class="single-herbs-guide__section-lead"><?php echo s($ises ? 'Todas las preguntas seleccionadas del capítulo 2 aparecen consecutivamente. Responde primero; después abre cada respuesta, o usa los controles para abrirlas y cerrarlas todas.' : 'All selected Chapter 2 questions appear consecutively. Answer first; then open each response, or use the controls to open and close them all.'); ?></p>
                    <div class="single-herbs-guide__yang-deck" data-yang-deck data-card-label="<?php echo $ises ? 'Tarjeta' : 'Card'; ?>" data-of-label="<?php echo $ises ? 'de' : 'of'; ?>">
                        <?php foreach ($yangquestions as $questionindex => $yangquestion): ?>
                            <article class="single-herbs-guide__yang-card<?php echo $questionindex === 0 ? ' is-active' : ''; ?>" data-yang-card aria-hidden="<?php echo $questionindex === 0 ? 'false' : 'true'; ?>">
                                <div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--front">
                                    <span class="single-herbs-guide__yang-label"><?php echo $ises ? 'Pregunta' : 'Question'; ?> <?php echo $questionindex + 1; ?> <?php echo $ises ? 'de' : 'of'; ?> <?php echo count($yangquestions); ?> · Yang, <?php echo $ises ? 'capítulo' : 'Chapter'; ?> 2</span>
                                    <p class="single-herbs-guide__yang-question"><?php echo s($yangquestion['question']); ?></p>
                                </div>
                                <div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--back">
                                    <span class="single-herbs-guide__yang-label"><?php echo $ises ? 'Respuesta' : 'Answer'; ?> <?php echo $questionindex + 1; ?> <?php echo $ises ? 'de' : 'of'; ?> <?php echo count($yangquestions); ?></span>
                                    <p class="single-herbs-guide__yang-answer"><?php echo s($yangquestion['answer']); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                        <div class="single-herbs-guide__yang-controls" aria-label="Yang study card controls">
                            <button class="single-herbs-guide__yang-control" type="button" data-yang-prev>← Previous</button>
                            <button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--flip" type="button" data-yang-flip data-show-label="Show answer" data-hide-label="Show question">Show answer</button>
                            <button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--next" type="button" data-yang-next>Next →</button>
                        </div>
                        <p class="single-herbs-guide__yang-progress" data-yang-progress><?php echo $ises ? 'Tarjeta' : 'Card'; ?> 1 <?php echo $ises ? 'de' : 'of'; ?> <?php echo count($yangquestions); ?></p>
                    </div>
                    <p class="single-herbs-guide__scope-note"><strong>Category boundary:</strong> questions concerning cool, acrid Exterior-releasing herbs belong to later Board Level categories and are intentionally excluded here.</p>
                </section>

                <section class="single-herbs-guide__section" aria-labelledby="topic-one-recall-title">
                    <h4 id="topic-one-recall-title">Quick recall</h4>
                    <p class="single-herbs-guide__section-lead">Open each item only after choosing an answer mentally.</p>
                    <div class="single-herbs-guide__recall">
                        <details><summary>Which herb best matches severe Wind-Cold with no sweating and wheezing?</summary><p><strong>Ma Huang.</strong> Its forceful diaphoresis and Lung-Qi dissemination distinguish it from the gentler herbs.</p></details>
                        <details><summary>Which herb is favored when nausea or Qi constraint in pregnancy accompanies a mild exterior pattern?</summary><p><strong>Zi Su Ye.</strong> It releases Wind-Cold while moving Qi, harmonizing the Middle and calming the fetus.</p></details>
                        <details><summary>Which herb is most associated with frontal headache, nasal obstruction and thick or foul sinus discharge?</summary><p><strong>Bai Zhi.</strong> It enters the Lung and Stomach, reaches the Yangming forehead and transforms turbidity and pus.</p></details>
                        <details><summary>How do Jing Jie and Fang Feng differ?</summary><p><strong>Jing Jie</strong> is lighter and more superficial, especially useful for rash and itching. <strong>Fang Feng</strong> reaches deeper Wind, addresses Dampness and is stronger for muscle pain or heaviness.</p></details>
                        <details><summary>Which herb points to occipital headache and stiff upper back from Wind-Cold-Damp?</summary><p><strong>Qiang Huo.</strong> Its Taiyang and upper-body emphasis is the decisive clue.</p></details>
                    </div>
                </section>

                <p class="single-herbs-guide__note single-herbs-guide__source"><strong>Sources used for this prototype:</strong> Bensky, Clavey, Stöger and Gamble, <em>Chinese Herbal Medicine: Materia Medica</em>, 3rd ed., category and individual monographs; and Yang, <em>Chinese Herbal Medicines: Comparisons and Characteristics</em>, 2nd ed., Chapter 2. This is educational review material and not prescribing guidance.</p>

                <details class="single-herbs-guide__annex-toggle">
                    <summary>
                        <strong>Annex · Additional Wind-Cold materia medica</strong>
                        <span>3 contextual entries outside the 2026 guideline · click to open</span>
                    </summary>
                    <div class="single-herbs-guide__annex-toggle-content">
                        <p class="single-herbs-guide__catalog-intro">These three entries remain hidden at the end because they are outside the 2026 guideline. They are retained only to recognize a source monograph or distinguish a related plant part; they do not expand Category 1.</p>
                        <ol class="single-herbs-guide__category-herbs">
                            <?php foreach ($windcoldannexherbs as $categoryherb): ?>
                                <li class="single-herbs-guide__category-herb">
                                    <div class="single-herbs-guide__category-herb-head">
                                        <strong><?php echo s($categoryherb['name']); ?></strong>
                                        <em><?php echo s($categoryherb['latin']); ?></em>
                                        <?php $annexisinguideline = $windcoldguidelinecheck($categoryherb); ?>
                                        <span class="single-herbs-guide__category-herb-badge<?php echo $annexisinguideline ? '' : ' single-herbs-guide__category-herb-badge--outside'; ?>"><?php echo s($annexisinguideline
                                            ? 'In 2026 guideline · not named in this category heading'
                                            : 'Source monograph · outside 2026 guideline'); ?></span>
                                    </div>
                                    <span class="single-herbs-guide__category-herb-common"><?php echo s($categoryherb['common']); ?></span>
                                    <p><?php echo s($categoryherb['note']); ?></p>
                                    <div class="single-herbs-guide__category-herb-context">
                                        <p><strong>Primary category</strong><span><?php echo s($categoryherb['category']); ?></span></p>
                                        <p><strong>Why it appears here</strong><span><?php echo s($categoryherb['reason']); ?></span></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </details>
            <?php elseif ($istopictwo): ?>
                <?php require(__DIR__ . '/single-herbs-category-cool-acrid.php'); ?>
            <?php elseif ($istopicthree): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-heat-drain-fire.php'); ?>
            <?php elseif ($istopicfour): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-heat-cool-blood.php'); ?>
            <?php elseif ($istopicfive): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-heat-dry-damp.php'); ?>
            <?php elseif ($istopicsix): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-resolve-toxicity.php'); ?>
            <?php elseif ($istopicseven): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-deficient-heat.php'); ?>
            <?php elseif ($istopiceight): ?>
                <?php require(__DIR__ . '/single-herbs-category-clear-summer-heat.php'); ?>
            <?php elseif ($istopicnine): ?>
                <?php require(__DIR__ . '/single-herbs-category-downward-draining-purgatives.php'); ?>
            <?php elseif ($istopicten): ?>
                <?php require(__DIR__ . '/single-herbs-category-moist-laxatives.php'); ?>
            <?php elseif ($istopiceleven): ?>
                <?php require(__DIR__ . '/single-herbs-category-harsh-expellants.php'); ?>
            <?php elseif ($istopictwelve): ?>
                <?php require(__DIR__ . '/single-herbs-category-drain-dampness.php'); ?>
            <?php elseif ($istopicthirteen): ?>
                <?php require(__DIR__ . '/single-herbs-category-cool-transform-phlegm.php'); ?>
            <?php elseif ($istopicfourteen): ?>
                <?php require(__DIR__ . '/single-herbs-category-warm-transform-phlegm.php'); ?>
            <?php elseif ($istopicfifteen): ?>
                <?php require(__DIR__ . '/single-herbs-category-relieve-cough-wheezing.php'); ?>
            <?php elseif ($istopicsixteen): ?>
                <?php require(__DIR__ . '/single-herbs-category-aromatic-transform-dampness.php'); ?>
            <?php elseif ($istopicseventeen): ?>
                <?php require(__DIR__ . '/single-herbs-category-relieve-food-stagnation.php'); ?>
            <?php elseif ($istopiceighteen): ?>
                <?php require(__DIR__ . '/single-herbs-category-regulate-qi.php'); ?>
            <?php elseif ($istopicnineteen): ?>
                <?php require(__DIR__ . '/single-herbs-category-invigorate-blood.php'); ?>
            <?php elseif ($istopictwenty): ?>
                <?php require(__DIR__ . '/single-herbs-category-stop-bleeding.php'); ?>
            <?php elseif ($istopictwentyone): ?>
                <?php require(__DIR__ . '/single-herbs-category-warm-interior.php'); ?>
            <?php elseif ($istopictwentythree): ?>
                <?php require(__DIR__ . '/single-herbs-category-tonify-qi.php'); ?>
            <?php elseif ($istopictwentyfour): ?>
                <?php require(__DIR__ . '/single-herbs-category-tonify-blood.php'); ?>
            <?php elseif ($istopictwentyfive): ?>
                <?php require(__DIR__ . '/single-herbs-category-tonify-yang.php'); ?>
            <?php elseif ($istopictwentysix): ?>
                <?php require(__DIR__ . '/single-herbs-category-tonify-yin.php'); ?>
            <?php elseif ($istopictwentyseven): ?>
                <?php require(__DIR__ . '/single-herbs-category-stabilize-bind.php'); ?>
            <?php elseif ($istopictwentyeight): ?>
                <?php require(__DIR__ . '/single-herbs-category-anchor-settle-spirit.php'); ?>
            <?php elseif ($istopictwentynine): ?>
                <?php require(__DIR__ . '/single-herbs-category-nourish-heart-calm-spirit.php'); ?>
            <?php elseif ($istopicthirty): ?>
                <?php require(__DIR__ . '/single-herbs-category-open-orifices.php'); ?>
            <?php elseif ($istopicthirtyone): ?>
                <?php require(__DIR__ . '/single-herbs-category-extinguish-wind.php'); ?>
            <?php else: ?>
                <ol class="single-herbs-guide__plan">
                    <li><strong>Category foundations</strong><span>Definition, therapeutic strategy and the distinctions that define this group.</span></li>
                    <li><strong>Core herbs</strong><span>Names, properties, channels, dosage ranges and the most testable identifiers.</span></li>
                    <li><strong>Functions and indications</strong><span>Primary actions, clinical presentations and symptom-pattern relationships.</span></li>
                    <li><strong>Comparisons</strong><span>Differentiate commonly confused herbs and recognize the best choice in context.</span></li>
                    <li><strong>Safety</strong><span>Contraindications, cautions, toxicity, preparation and clinically relevant interactions.</span></li>
                    <li><strong>Recall and Board review</strong><span>Flashcards, image recognition and category-focused multiple-choice review.</span></li>
                </ol>
                <p class="single-herbs-guide__note"><strong>Content stage:</strong> this category framework is ready. Verified educational content will be developed from the supplied Bensky materia medica without changing the Original Bank.</p>
            <?php endif; ?>
            <?php if ($selected !== null): ?>
                <?php require(__DIR__ . '/single-herbs-dui-yao-reference.php'); ?>
            <?php endif; ?>
        </section>
        <?php echo $rendersingleherbcategorynavigation('bottom'); ?>
    <?php else: ?>
        <section class="single-herbs-guide__overview" aria-labelledby="single-herbs-categories-title">
            <div>
                <h3 id="single-herbs-categories-title">Board-aligned category index</h3>
                <p><?php echo s($ises ? 'Comienza con la introducción, continúa por las categorías del Board Level y termina con Seguridad y dispensación, la tabla maestra y el repaso final.' : 'Start with the introduction, continue through Board Level categories, and finish with Safety and dispensing, the master table and Final Review.'); ?></p>
            </div>
            <div class="single-herbs-guide__count" aria-label="Category count">
                <strong><?php echo count($studyguidecategories) + 8; ?></strong>
                <span><?php echo s($ises ? 'áreas de estudio' : 'study areas'); ?></span>
            </div>
        </section>

        <?php if ($studyguidecategories): ?>
            <section class="single-herbs-guide__grid" aria-label="Single Herbs categories">
                <?php
                $introductionparams = ['focus' => 'introduction'];
                if ($ises) {
                    $introductionparams['lang'] = 'es';
                }
                $introductionurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $introductionparams);
                ?>
                <a class="single-herbs-guide__category single-herbs-guide__category--introduction" href="<?php echo $introductionurl->out(false); ?>">
                    <span class="single-herbs-guide__category-reference">
                        <span class="single-herbs-guide__category-number">I</span>
                        <span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Guía de estudio' : 'Study guide'); ?></span>
                    </span>
                    <strong><?php echo s($ises ? 'Introducción a las hierbas individuales' : 'Introduction to Single Herbs'); ?></strong>
                    <small><?php echo s($ises ? 'Historia · propiedades · meridianos · dosis · seguridad →' : 'History · properties · channels · dosage · safety →'); ?></small>
                </a>
                <?php foreach ($studyguidecategories as $category): ?>
                    <?php
                    $categoryguidenumber = (int) $category['guide_number'];
                    $categorydisplayguidenumber = $displaystudyguidenumber($categoryguidenumber);
                    $canonicalcategory = $canonicalcategories[$categoryguidenumber] ?? null;
                    $canonicalcategorycode = (string)($canonicalcategory['code'] ?? '');
                    $categoryherbcount = (int)($benskycategorycounts[$canonicalcategorycode] ?? 0);
                    $categorybenskychapter = $benskychapterlabel($categoryguidenumber);
                    $categorydisplayname = $canonicalcategorytitle($categoryguidenumber)
                        ?: $stripstudyguidenumber((string) $category['name']);
                    if ($categoryguidenumber === 12) {
                        $categoryfooter = $ises
                            ? $categoryherbcount . ' hierbas oficiales · atlas de 33 monografías · seguridad y comparaciones →'
                            : $categoryherbcount . ' official herbs · 33-monograph atlas · safety and comparisons →';
                    } else if ($categoryherbcount > 0) {
                        $categoryfooter = $ises
                            ? $categoryherbcount . ' hierbas oficiales · marco de estudio · seguridad y comparaciones →'
                            : $categoryherbcount . ' official herbs · study framework · safety and comparisons →';
                    } else {
                        $categoryfooter = $ises
                            ? 'Atlas clínico · marco de estudio · seguridad y comparaciones →'
                            : 'Clinical atlas · study framework · safety and comparisons →';
                    }
                    $categoryparams = ['focus' => (string) $category['number']];
                    if ($ises) {
                        $categoryparams['lang'] = 'es';
                    }
                    $categoryurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $categoryparams);
                    ?>
                    <?php if ($categoryguidenumber === 12): ?>
                        <article id="bensky-chapter-four-reference" class="single-herbs-guide__category single-herbs-guide__category--bensky-reference">
                            <a class="single-herbs-guide__category-link" href="<?php echo $benskyfoururl->out(false); ?>" aria-label="<?php echo s($ises ? 'Bensky, capítulo 4: Hierbas que drenan Humedad' : 'Bensky Chapter 4: Herbs that Drain Dampness'); ?>">
                                <span class="single-herbs-guide__category-reference">
                                    <span class="single-herbs-guide__category-number" aria-label="<?php echo s($ises ? 'Tarjeta de la guía 12' : 'Study-guide card 12'); ?>">12</span>
                                    <span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Bensky · Capítulo 4' : 'Bensky · Chapter 4'); ?></span>
                                </span>
                                <strong><?php echo s($ises ? 'Hierbas que drenan Humedad' : 'Herbs that Drain Dampness'); ?></strong>
                            </a>
                            <button class="single-herbs-guide__category-roster-trigger" type="button" aria-haspopup="dialog" aria-controls="single-herbs-guideline-roster" aria-expanded="false" data-category-roster-open data-roster-special="bensky4" data-roster-count="21" data-roster-title="<?php echo s($ises ? 'Hierbas oficiales · Bensky, capítulo 4' : 'Official herbs · Bensky, Chapter 4'); ?>" aria-label="<?php echo s($ises ? 'Ver las 21 hierbas oficiales del capítulo 4' : 'View the 21 official herbs in Chapter 4'); ?>"><?php echo s($ises ? '21 hierbas oficiales · atlas de 30 monografías · seguridad y comparaciones →' : '21 official herbs · 30-monograph atlas · safety and comparisons →'); ?></button>
                        </article>
                    <?php endif; ?>
                    <article class="single-herbs-guide__category">
                        <a class="single-herbs-guide__category-link" href="<?php echo $categoryurl->out(false); ?>">
                            <span class="single-herbs-guide__category-reference">
                                <span class="single-herbs-guide__category-number" aria-label="<?php echo s(($ises ? 'Tarjeta de la guía ' : 'Study-guide card ') . $categorydisplayguidenumber); ?>"><?php echo $categorydisplayguidenumber; ?></span>
                                <?php if ($categorybenskychapter !== ''): ?><span class="single-herbs-guide__bensky-chapter"><?php echo s($categorybenskychapter); ?></span><?php endif; ?>
                            </span>
                            <strong><?php echo $categorydisplayname; ?></strong>
                        </a>
                        <button class="single-herbs-guide__category-roster-trigger" type="button" aria-haspopup="dialog" aria-controls="single-herbs-guideline-roster" aria-expanded="false" data-category-roster-open data-roster-guide="<?php echo $categoryguidenumber; ?>" data-roster-count="<?php echo $categoryherbcount; ?>" data-roster-title="<?php echo s($categorydisplayname); ?>" aria-label="<?php echo s(($ises ? 'Ver las ' : 'View the ') . $categoryherbcount . ($ises ? ' hierbas oficiales de ' : ' official herbs in ') . $categorydisplayname); ?>"><?php echo s($categoryfooter); ?></button>
                    </article>
                <?php endforeach; ?>
                <article id="bensky-chapter-seventeen-reference" class="single-herbs-guide__category single-herbs-guide__category--bensky-reference">
                    <a class="single-herbs-guide__category-link" href="<?php echo $benskyseventeenurl->out(false); ?>" aria-label="<?php echo s($ises ? 'Bensky, capítulo 17: Hierbas que expulsan parásitos' : 'Bensky Chapter 17: Herbs that Expel Parasites'); ?>">
                        <span class="single-herbs-guide__category-reference"><span class="single-herbs-guide__category-number" aria-label="<?php echo s($ises ? 'Tarjeta de la guía 32' : 'Study-guide card 32'); ?>">32</span><span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Bensky · Capítulo 17' : 'Bensky · Chapter 17'); ?></span></span>
                        <strong><?php echo s($ises ? 'Hierbas que expulsan parásitos' : 'Herbs that Expel Parasites'); ?></strong>
                    </a>
                    <button class="single-herbs-guide__category-roster-trigger" type="button" aria-haspopup="dialog" aria-controls="single-herbs-guideline-roster" aria-expanded="false" data-category-roster-open data-roster-special="bensky17" data-roster-count="1" data-roster-title="<?php echo s($ises ? 'Hierbas oficiales · Bensky, capítulo 17' : 'Official herbs · Bensky, Chapter 17'); ?>" aria-label="<?php echo s($ises ? 'Ver la hierba oficial del capítulo 17' : 'View the official herb in Chapter 17'); ?>"><?php echo s($ises ? '1 hierba oficial · atlas de 13 monografías · seguridad y comparaciones →' : '1 official herb · 13-monograph atlas · safety and comparisons →'); ?></button>
                </article>
                <article id="bensky-chapter-eighteen-reference" class="single-herbs-guide__category single-herbs-guide__category--bensky-reference">
                    <a class="single-herbs-guide__category-link" href="<?php echo $benskyeighteenurl->out(false); ?>" aria-label="<?php echo s($ises ? 'Bensky, capítulo 18: Sustancias para aplicación externa' : 'Bensky Chapter 18: Substances for Topical Application'); ?>">
                        <span class="single-herbs-guide__category-reference"><span class="single-herbs-guide__category-number" aria-label="<?php echo s($ises ? 'Tarjeta de la guía 33' : 'Study-guide card 33'); ?>">33</span><span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Bensky · Capítulo 18' : 'Bensky · Chapter 18'); ?></span></span>
                        <strong><?php echo s($ises ? 'Sustancias para aplicación externa' : 'Substances for Topical Application'); ?></strong>
                    </a>
                    <button class="single-herbs-guide__category-roster-trigger" type="button" aria-haspopup="dialog" aria-controls="single-herbs-guideline-roster" aria-expanded="false" data-category-roster-open data-roster-special="bensky18" data-roster-count="2" data-roster-title="<?php echo s($ises ? 'Sustancias oficiales · Bensky, capítulo 18' : 'Official substances · Bensky, Chapter 18'); ?>" aria-label="<?php echo s($ises ? 'Ver las 2 sustancias oficiales del capítulo 18' : 'View the 2 official substances in Chapter 18'); ?>"><?php echo s($ises ? '2 sustancias oficiales · atlas de 13 monografías · seguridad y comparaciones →' : '2 official substances · 13-monograph atlas · safety and comparisons →'); ?></button>
                </article>
                <article id="bensky-chapter-nineteen-reference" class="single-herbs-guide__category single-herbs-guide__category--bensky-reference">
                    <a class="single-herbs-guide__category-link" href="<?php echo $benskynineteenurl->out(false); ?>" aria-label="<?php echo s($ises ? 'Bensky, capítulo 19: Sustancias obsoletas' : 'Bensky Chapter 19: Obsolete Substances'); ?>">
                        <span class="single-herbs-guide__category-reference"><span class="single-herbs-guide__category-number" aria-label="<?php echo s($ises ? 'Tarjeta de la guía 34' : 'Study-guide card 34'); ?>">34</span><span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Bensky · Capítulo 19' : 'Bensky · Chapter 19'); ?></span></span>
                        <strong><?php echo s($ises ? 'Sustancias obsoletas' : 'Obsolete Substances'); ?></strong>
                    </a>
                    <button class="single-herbs-guide__category-roster-trigger" type="button" aria-haspopup="dialog" aria-controls="single-herbs-guideline-roster" aria-expanded="false" data-category-roster-open data-roster-special="bensky19" data-roster-count="0" data-roster-title="<?php echo s($ises ? 'Sustancias oficiales · Bensky, capítulo 19' : 'Official substances · Bensky, Chapter 19'); ?>" aria-label="<?php echo s($ises ? 'Ver el estado del guideline para el capítulo 19' : 'View the guideline status for Chapter 19'); ?>"><?php echo s($ises ? '0 sustancias oficiales · atlas de seguridad de 17 monografías · toxicidad, conservación y sustitutos →' : '0 official substances · 17-monograph safety atlas · toxicity, conservation and substitutes →'); ?></button>
                </article>
                <?php
                $actionsindicationsparams = ['focus' => 'actions_indications'];
                if ($ises) {
                    $actionsindicationsparams['lang'] = 'es';
                }
                $actionsindicationsurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $actionsindicationsparams);
                ?>
                <a class="single-herbs-guide__category single-herbs-guide__category--properties" href="<?php echo $actionsindicationsurl->out(false); ?>">
                    <span class="single-herbs-guide__category-reference">
                        <span class="single-herbs-guide__category-number">A</span>
                        <span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Referencia clínica' : 'Clinical reference'); ?></span>
                    </span>
                    <strong><?php echo s($ises ? 'Acciones e indicaciones' : 'Actions and indications'); ?></strong>
                    <small><?php echo s($ises ? '304 hierbas · categoría · parejas acción–indicación · búsqueda y orden →' : '304 herbs · category · action–indication pairs · search and sorting →'); ?></small>
                </a>
                <?php
                $safetyparams = ['focus' => 'safety_dispensing'];
                if ($ises) {
                    $safetyparams['lang'] = 'es';
                }
                $safetyurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $safetyparams);
                ?>
                <a class="single-herbs-guide__category single-herbs-guide__category--safety" href="<?php echo $safetyurl->out(false); ?>">
                    <span class="single-herbs-guide__category-reference">
                        <span class="single-herbs-guide__category-number">S</span>
                        <span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Seguridad' : 'Safety'); ?></span>
                    </span>
                    <strong><?php echo s($ises ? 'Seguridad y dispensación' : 'Safety and dispensing'); ?></strong>
                    <small><?php echo s($ises ? '304 hierbas · precauciones completas · 20 preguntas y respuestas →' : '304 herbs · complete precautions · 20 questions and answers →'); ?></small>
                </a>
                <?php
                $propertiesparams = ['focus' => 'properties'];
                if ($ises) {
                    $propertiesparams['lang'] = 'es';
                }
                $propertiesurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $propertiesparams);
                ?>
                <a class="single-herbs-guide__category single-herbs-guide__category--properties" href="<?php echo $propertiesurl->out(false); ?>">
                    <span class="single-herbs-guide__category-reference">
                        <span class="single-herbs-guide__category-number">T</span>
                        <span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Referencia' : 'Reference'); ?></span>
                    </span>
                    <strong><?php echo s($ises ? 'Tabla de propiedades de hierbas' : 'Herb Properties Table'); ?></strong>
                    <small><?php echo s($ises ? '304 hierbas · sabor · temperatura · meridianos · dosis →' : '304 herbs · flavor · temperature · channels · dose →'); ?></small>
                </a>
                <?php
                $mixedparams = ['focus' => 'mixed'];
                if ($ises) {
                    $mixedparams['lang'] = 'es';
                }
                $mixedurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $mixedparams);
                ?>
                <a class="single-herbs-guide__category single-herbs-guide__category--mixed" href="<?php echo $mixedurl->out(false); ?>">
                    <span class="single-herbs-guide__category-reference">
                        <span class="single-herbs-guide__category-number">M</span>
                        <span class="single-herbs-guide__bensky-chapter"><?php echo s($ises ? 'Repaso final' : 'Final review'); ?></span>
                    </span>
                    <strong><?php echo s($ises ? 'Repaso final' : 'Final Review'); ?></strong>
                    <small><?php echo s($ises ? 'Abrir preguntas consecutivas →' : 'Open consecutive questions →'); ?></small>
                </a>
            </section>
        <?php else: ?>
            <div class="single-herbs-guide__empty">The Single Herbs category source is temporarily unavailable. No course content was changed.</div>
        <?php endif; ?>
    <?php endif; ?>
</main>
<script>
(function() {
    const rosterTrigger = document.querySelector('[data-guideline-roster-open]');
    const rosterModal = document.querySelector('[data-guideline-roster-modal]');
    const rosterDialog = rosterModal ? rosterModal.querySelector('[role="dialog"]') : null;
    const rosterClose = rosterModal ? rosterModal.querySelector('[data-guideline-roster-close]') : null;
    const rosterSearch = rosterModal ? rosterModal.querySelector('[data-guideline-roster-search]') : null;
    const rosterCount = rosterModal ? rosterModal.querySelector('[data-guideline-roster-count]') : null;
    const rosterTitle = rosterModal ? rosterModal.querySelector('[data-guideline-roster-title]') : null;
    const rosterDescription = rosterModal ? rosterModal.querySelector('[data-guideline-roster-description]') : null;
    const rosterEmpty = rosterModal ? rosterModal.querySelector('[data-guideline-roster-empty]') : null;
    const rosterItems = rosterModal ? Array.from(rosterModal.querySelectorAll('[data-guideline-roster-item]')) : [];
    const categoryRosterTriggers = Array.from(document.querySelectorAll('[data-category-roster-open]'));
    const allRosterTriggers = [rosterTrigger].concat(categoryRosterTriggers).filter(Boolean);
    const defaultRosterTitle = rosterTitle ? rosterTitle.textContent : '';
    const defaultRosterDescription = rosterDescription ? rosterDescription.textContent : '';
    let rosterPreviousFocus = null;
    let previousBodyOverflow = '';
    let rosterScope = null;
    const normalizeRosterSearch = function(value) {
        return String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    };
    const updateRoster = function() {
        const query = normalizeRosterSearch(rosterSearch ? rosterSearch.value : '');
        let visible = 0;
        rosterItems.forEach(function(item) {
            const matchesScope = !rosterScope
                || (rosterScope.type === 'guide' && item.dataset.guidelineRosterGuide === rosterScope.value)
                || (rosterScope.type === 'special' && item.dataset.guidelineRosterSpecial === rosterScope.value);
            const matches = matchesScope && (!query || normalizeRosterSearch(item.dataset.guidelineRosterSearchValue).includes(query));
            item.hidden = !matches;
            if (matches) {
                visible += 1;
            }
        });
        if (rosterCount && rosterModal) {
            rosterCount.textContent = visible + ' ' + rosterModal.dataset.countLabel;
        }
        if (rosterEmpty) {
            rosterEmpty.hidden = !rosterScope || visible !== 0 || query !== '';
        }
    };
    const setRosterScope = function(trigger) {
        const guideNumber = trigger ? trigger.dataset.rosterGuide : '';
        const specialFocus = trigger ? trigger.dataset.rosterSpecial : '';
        rosterScope = guideNumber
            ? {type: 'guide', value: guideNumber}
            : (specialFocus ? {type: 'special', value: specialFocus} : null);
        if (rosterSearch) {
            rosterSearch.value = '';
        }
        if (!rosterScope) {
            if (rosterTitle) {
                rosterTitle.textContent = defaultRosterTitle;
            }
            if (rosterDescription) {
                rosterDescription.textContent = defaultRosterDescription;
            }
            return;
        }
        const expectedCount = Number(trigger.dataset.rosterCount || 0);
        if (rosterTitle) {
            rosterTitle.textContent = trigger.dataset.rosterTitle || defaultRosterTitle;
        }
        if (rosterDescription) {
            rosterDescription.textContent = expectedCount === 0
                ? <?php echo json_encode($ises
                    ? 'No hay entradas oficiales de esta tarjeta en el guideline vigente 2026.'
                    : 'There are no official entries from this card in the current 2026 guideline.', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>
                : expectedCount + ' ' + <?php echo json_encode($ises
                    ? 'entradas oficiales. Selecciona una para abrir su ficha.'
                    : 'official entries. Select one to open its card.', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        }
    };
    const openRoster = function(trigger) {
        if (!rosterModal || !trigger) {
            return;
        }
        rosterPreviousFocus = document.activeElement;
        previousBodyOverflow = document.body.style.overflow;
        setRosterScope(trigger);
        rosterModal.hidden = false;
        allRosterTriggers.forEach(function(candidate) {
            candidate.setAttribute('aria-expanded', candidate === trigger ? 'true' : 'false');
        });
        document.body.style.overflow = 'hidden';
        updateRoster();
        window.requestAnimationFrame(function() {
            (rosterSearch || rosterDialog).focus();
        });
    };
    const closeRoster = function() {
        if (!rosterModal || rosterModal.hidden) {
            return;
        }
        rosterModal.hidden = true;
        allRosterTriggers.forEach(function(trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        });
        document.body.style.overflow = previousBodyOverflow;
        if (rosterPreviousFocus && typeof rosterPreviousFocus.focus === 'function') {
            rosterPreviousFocus.focus();
        }
    };
    if (rosterTrigger && rosterModal) {
        rosterTrigger.addEventListener('click', function() {
            if (rosterModal.hidden) {
                openRoster(rosterTrigger);
            } else {
                closeRoster();
            }
        });
        rosterClose.addEventListener('click', closeRoster);
        rosterModal.addEventListener('click', function(event) {
            if (event.target === rosterModal) {
                closeRoster();
            }
        });
        rosterSearch.addEventListener('input', updateRoster);
        rosterModal.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeRoster();
                return;
            }
            if (event.key !== 'Tab') {
                return;
            }
            const focusable = Array.from(rosterModal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])')).filter(function(element) {
                return !element.closest('[hidden]');
            });
            if (!focusable.length) {
                return;
            }
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });
    }
    categoryRosterTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            openRoster(trigger);
        });
    });

    const requestedHerb = <?php echo json_encode($requestedherbcard['name'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const guidelineHerbs = new Set(<?php echo json_encode(array_keys($guidelineherbkeys), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
    const normalizeHerb = function(value) {
        return String(value || '').toLowerCase().replace(/\s*\*$/, '').replace(/[^a-z0-9]+/g, ' ').trim();
    };
    document.querySelectorAll([
        '#requested-herb-card-title',
        '.single-herbs-guide__category-herb-head strong',
        '.single-herbs-guide__profile-name',
        '.single-herbs-guide__yang-bar > span:first-child',
        '.single-herbs-guide__formula-lead h5',
        '.single-herbs-guide__decision div > span'
    ].join(',')).forEach(function(element) {
        const rawHerbKey = normalizeHerb(element.textContent);
        const herbKey = rawHerbKey === 'xin yi hua' ? 'xin yi' : rawHerbKey;
        if (guidelineHerbs.has(herbKey) && !/\*\s*$/.test(element.textContent)) {
            element.appendChild(document.createTextNode('*'));
        }
    });
    if (requestedHerb) {
        const requestedKey = normalizeHerb(requestedHerb);
        const profileNames = Array.from(document.querySelectorAll('.single-herbs-guide__profile-name'));
        const categoryNames = Array.from(document.querySelectorAll('.single-herbs-guide__category-herb-head strong'));
        const matchedName = profileNames.concat(categoryNames).find(function(element) {
            const candidate = normalizeHerb(element.textContent);
            return candidate === requestedKey || (requestedKey === 'xin yi' && candidate === 'xin yi hua');
        });
        if (matchedName) {
            const target = matchedName.closest('details, .single-herbs-guide__category-herb');
            if (target) {
                if (target.tagName === 'DETAILS') {
                    target.open = true;
                }
                target.id = 'herb-card-' + requestedKey.replace(/\s+/g, '-');
                target.classList.add('single-herbs-guide__card-target');
                window.requestAnimationFrame(function() {
                    target.scrollIntoView({behavior: 'smooth', block: 'start'});
                });
            }
        }
    }

    <?php if ($selected !== null): ?>
    document.querySelectorAll('.single-herbs-guide__profile-formulas a[href*="/formula-navigator-"]').forEach(function(link) {
        const formulaUrl = new URL(link.getAttribute('href'), window.location.origin);
        formulaUrl.searchParams.set('from', 'single-herbs');
        formulaUrl.searchParams.set('focus', '<?php echo (int) $selected['number']; ?>');
        link.href = formulaUrl.pathname + formulaUrl.search;
    });
    <?php endif; ?>

    const finalReview = document.querySelector('.single-herbs-guide__final-review');
    if (finalReview) {
        const finalQuestions = Array.from(finalReview.querySelectorAll('.single-herbs-guide__final-question'));
        const openAll = document.querySelector('[data-final-open-all]');
        const closeAll = document.querySelector('[data-final-close-all]');
        if (openAll) {
            openAll.addEventListener('click', function() {
                finalQuestions.forEach(function(question) {
                    question.open = true;
                });
            });
        }
        if (closeAll) {
            closeAll.addEventListener('click', function() {
                finalQuestions.forEach(function(question) {
                    question.open = false;
                });
            });
        }
        return;
    }

    const workspace = document.querySelector('.single-herbs-guide__workspace');
    if (!workspace) {
        return;
    }
    const isSpanishQuestions = <?php echo $ises ? 'true' : 'false'; ?>;
    const hasLegacyYangQuestions = workspace.querySelector('[data-yang-deck]') !== null;
    const questionSections = [];
    const registerSection = function(section) {
        if (section && !questionSections.includes(section)) {
            questionSections.push(section);
        }
    };

    // Convert every legacy Yang deck to the compact Chapter 17 disclosure format.
    Array.from(workspace.querySelectorAll('[data-yang-deck]')).forEach(function(deck) {
        registerSection(deck.closest('.single-herbs-guide__section'));
        Array.from(deck.querySelectorAll('[data-yang-card]')).forEach(function(card) {
            const front = card.querySelector('.single-herbs-guide__yang-face--front');
            const back = card.querySelector('.single-herbs-guide__yang-face--back');
            const questionText = front ? front.querySelector('.single-herbs-guide__yang-question') : null;
            const answerText = back ? back.querySelector('.single-herbs-guide__yang-answer') : null;
            if (!front || !back || !questionText || !answerText) {
                return;
            }
            const originalLabel = front.querySelector('.single-herbs-guide__yang-label');
            const sourceParts = originalLabel ? originalLabel.textContent.split('·') : [];
            const question = document.createElement('details');
            const summary = document.createElement('summary');
            const number = document.createElement('span');
            const prompt = document.createElement('span');
            const answer = document.createElement('div');
            question.className = 'single-herbs-guide__final-question';
            question.dataset.questionSource = sourceParts.length > 1
                ? sourceParts.slice(1).join('·').trim()
                : (isSpanishQuestions ? 'Yang · capítulo' : 'Yang · chapter');
            number.className = 'single-herbs-guide__final-number';
            Array.from(questionText.childNodes).forEach(function(node) {
                prompt.appendChild(node.cloneNode(true));
            });
            answer.className = 'single-herbs-guide__final-answer';
            Array.from(answerText.childNodes).forEach(function(node) {
                answer.appendChild(node.cloneNode(true));
            });
            summary.appendChild(number);
            summary.appendChild(prompt);
            question.appendChild(summary);
            question.appendChild(answer);
            card.replaceWith(question);
        });
    });

    // Register Bensky questions and the older quick-recall questions so all sources merge.
    Array.from(workspace.querySelectorAll('.single-herbs-guide__recall')).forEach(function(recall) {
        if (!recall.classList.contains('single-herbs-guide__final-review')) {
            registerSection(recall.closest('.single-herbs-guide__section'));
        }
    });
    questionSections.sort(function(left, right) {
        return left === right ? 0 : (left.compareDocumentPosition(right) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1);
    });
    if (!questionSections.length) {
        return;
    }

    const primarySection = questionSections[0];
    const unifiedQuestions = [];
    questionSections.forEach(function(section) {
        const isBenskySection = /^bensky-/.test(section.getAttribute('aria-labelledby') || '');
        const heading = section.querySelector('h4');
        const headingText = heading ? heading.textContent.trim() : '';
        const chapterMatch = headingText.match(/(?:chapter|capítulo)\s+(\d+)/i);
        const benskySource = isSpanishQuestions
            ? 'Bensky · capítulo ' + (chapterMatch ? chapterMatch[1] : '')
            : 'Bensky · Chapter ' + (chapterMatch ? chapterMatch[1] : '');
        Array.from(section.querySelectorAll('details')).forEach(function(question) {
            if (question.closest('.single-herbs-guide__annex-toggle')) {
                return;
            }
            const summary = question.querySelector(':scope > summary');
            if (!summary) {
                return;
            }
            if (!question.classList.contains('single-herbs-guide__final-question')) {
                const prompt = document.createElement('span');
                Array.from(summary.childNodes).forEach(function(node) {
                    prompt.appendChild(node.cloneNode(true));
                });
                const number = document.createElement('span');
                number.className = 'single-herbs-guide__final-number';
                summary.replaceChildren(number, prompt);
                const answer = document.createElement('div');
                answer.className = 'single-herbs-guide__final-answer';
                Array.from(question.childNodes).forEach(function(node) {
                    if (node !== summary) {
                        answer.appendChild(node);
                    }
                });
                question.appendChild(answer);
                question.classList.add('single-herbs-guide__final-question');
                question.dataset.questionSource = isSpanishQuestions ? 'Repaso Bensky' : 'Bensky review';
            } else if (!question.dataset.questionSource) {
                question.dataset.questionSource = isBenskySection
                    ? benskySource.replace(/\s+$/, '')
                    : (isSpanishQuestions ? 'Repaso Bensky' : 'Bensky review');
            }
            unifiedQuestions.push(question);
        });
    });
    if (!unifiedQuestions.length) {
        return;
    }
    const hasBenskyReviewQuestions = unifiedQuestions.some(function(question) {
        return !/^Yang\b/i.test(question.dataset.questionSource || '');
    });

    const heading = primarySection.querySelector('h4');
    let lead = primarySection.querySelector(':scope > .single-herbs-guide__section-lead');
    if (!lead) {
        lead = document.createElement('p');
        lead.className = 'single-herbs-guide__section-lead';
        if (heading) {
            heading.insertAdjacentElement('afterend', lead);
        } else {
            primarySection.prepend(lead);
        }
    }
    if (heading) {
        const questionCountLabel = isSpanishQuestions
            ? (unifiedQuestions.length === 1 ? ' pregunta' : ' preguntas')
            : (unifiedQuestions.length === 1 ? ' question' : ' questions');
        heading.textContent = isSpanishQuestions
            ? 'Repaso unificado del capítulo · ' + unifiedQuestions.length + questionCountLabel
            : 'Unified chapter review · ' + unifiedQuestions.length + questionCountLabel;
    }
    if (hasLegacyYangQuestions && hasBenskyReviewQuestions) {
        lead.textContent = isSpanishQuestions
            ? 'Las preguntas anteriores de Yang y las nuevas preguntas de repaso Bensky aparecen en una sola secuencia. Responde primero y después abre cada respuesta, o usa los controles para abrirlas y cerrarlas todas.'
            : 'Earlier Yang questions and newer Bensky review questions now appear in one sequence. Answer first, then open each response, or use the controls to open and close them all.';
    } else if (hasLegacyYangQuestions) {
        lead.textContent = isSpanishQuestions
            ? 'Todas las preguntas de Yang y de repaso del capítulo aparecen en una sola secuencia. Responde primero y después abre cada respuesta, o usa los controles para abrirlas y cerrarlas todas.'
            : 'All Yang and chapter-review questions appear in one sequence. Answer first, then open each response, or use the controls to open and close them all.';
    } else {
        lead.textContent = isSpanishQuestions
            ? 'Responde mentalmente antes de abrir cada elemento, o usa los controles para abrir y cerrar todas las respuestas.'
            : 'Answer mentally before opening each item, or use the controls to open and close all answers.';
    }

    const controls = document.createElement('div');
    const openAll = document.createElement('button');
    const closeAll = document.createElement('button');
    const questionList = document.createElement('div');
    controls.className = 'single-herbs-guide__final-controls';
    controls.setAttribute('aria-label', isSpanishQuestions ? 'Controles de respuestas del capítulo' : 'Chapter answer controls');
    openAll.type = 'button';
    closeAll.type = 'button';
    openAll.textContent = isSpanishQuestions ? 'Abrir todas las respuestas' : 'Open all answers';
    closeAll.textContent = isSpanishQuestions ? 'Cerrar todas las respuestas' : 'Close all answers';
    controls.appendChild(openAll);
    controls.appendChild(closeAll);
    questionList.className = 'single-herbs-guide__recall single-herbs-guide__unified-questions';
    unifiedQuestions.forEach(function(question, index) {
        const number = question.querySelector(':scope > summary .single-herbs-guide__final-number');
        const source = question.dataset.questionSource || (isSpanishQuestions ? 'Repaso del capítulo' : 'Chapter review');
        if (number) {
            number.replaceChildren();
            number.appendChild(document.createTextNode((isSpanishQuestions ? 'Pregunta ' : 'Question ') + (index + 1)));
            const sourceLabel = document.createElement('small');
            sourceLabel.textContent = source;
            number.appendChild(sourceLabel);
        }
        questionList.appendChild(question);
    });

    Array.from(primarySection.querySelectorAll(':scope > [data-yang-deck], :scope > .single-herbs-guide__recall, :scope > .single-herbs-guide__final-controls')).forEach(function(element) {
        element.remove();
    });
    lead.insertAdjacentElement('afterend', controls);
    controls.insertAdjacentElement('afterend', questionList);
    questionSections.slice(1).forEach(function(section) {
        section.remove();
    });
    openAll.addEventListener('click', function() {
        unifiedQuestions.forEach(function(question) {
            question.open = true;
        });
    });
    closeAll.addEventListener('click', function() {
        unifiedQuestions.forEach(function(question) {
            question.open = false;
        });
    });
})();
</script>
<?php
$guidecontent = ob_get_clean();
echo $ises ? strtr($guidecontent, $spanishtranslations) : $guidecontent;
echo $OUTPUT->footer();
