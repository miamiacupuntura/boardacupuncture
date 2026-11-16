<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/herbology-library-navigation.php');

require_login();

$fromlibrary = block_myprogress_from_herbology_library();
$lang = optional_param('lang', '', PARAM_ALPHA);
$ises = $lang === 'es' || ($lang === '' && current_language() === 'es');
$focus = optional_param('focus', '', PARAM_ALPHANUMEXT);
$chapterfocusaliases = [
    'official-formulas-ch3' => 'purge',
    'calm-shen' => 'calm-shen-chapter-10',
    'regulate-qi' => 'regulate-qi-chapter-12',
    'invigorate-blood' => 'regulate-blood-chapter-13',
    'expel-wind' => 'expel-wind-chapter-14',
    'expel-damp' => 'expel-damp-chapter-16',
    'transform-phlegm' => 'transform-phlegm-chapter-17',
    'digestive' => 'digestive-chapter-18',
];
$focus = $chapterfocusaliases[$focus] ?? $focus;
$boardcategorybenskychapters = [
    'release-exterior' => 1,
    'clear-heat' => 4,
    'purge' => 2,
    'harmonize' => 3,
    'expel-damp' => 16,
    'warm-interior' => 6,
    'tonify' => 8,
    'regulate-qi' => 12,
    'invigorate-blood' => 13,
    'stabilize-bind' => 9,
    'calm-shen' => 10,
    'expel-wind' => 14,
    'transform-phlegm' => 17,
    'digestive' => 18,
];
$datafile = __DIR__ . '/formulas-study-guide-data.json';
$data = is_readable($datafile) ? json_decode((string) file_get_contents($datafile), true) : null;
if (!is_array($data)
        || ($data['version'] ?? null) !== 7
        || count($data['categories'] ?? []) !== 14
        || count($data['formulas'] ?? []) !== 63
        || count($data['supplementalCategories'] ?? []) !== 23
        || count($data['supplementalFormulas'] ?? []) !== 227) {
    throw new moodle_exception('The Formula Study Guide data is temporarily unavailable.');
}

$officialrosterfile = __DIR__ . '/formula-board-roster-2026.json';
$officialroster = is_readable($officialrosterfile)
    ? json_decode((string) file_get_contents($officialrosterfile), true)
    : null;
if (!is_array($officialroster)
        || ($officialroster['version'] ?? null) !== 1
        || ($officialroster['total'] ?? null) !== 181
        || count($officialroster['formulas'] ?? []) !== 181) {
    throw new moodle_exception('The official 2026 NCBAHM Appendix C roster is temporarily unavailable.');
}

$duiyaosmartfile = __DIR__ . '/dui-yao-smart-data.json';
$duiyaosmart = is_readable($duiyaosmartfile)
    ? json_decode((string) file_get_contents($duiyaosmartfile), true)
    : null;
$herbpropertiesfile = __DIR__ . '/single-herb-properties.json';
$herbproperties = is_readable($herbpropertiesfile)
    ? json_decode((string) file_get_contents($herbpropertiesfile), true)
    : null;
if (!is_array($duiyaosmart)
        || ($duiyaosmart['total'] ?? null) !== 51
        || count($duiyaosmart['pairs'] ?? []) !== 51
        || !is_array($herbproperties)
        || ($herbproperties['total'] ?? null) !== 304
        || count($herbproperties['herbs'] ?? []) !== 304) {
    throw new moodle_exception('The Duì Yào pharmaceutical-name data is temporarily unavailable.');
}
$duiyaosmartbyslug = [];
foreach ($duiyaosmart['pairs'] as $duiyaosmartpair) {
    $duiyaosmartbyslug[$duiyaosmartpair['slug']] = $duiyaosmartpair;
}
$herblatinbyname = [];
foreach ($herbproperties['herbs'] as $herbproperty) {
    $herblatinbyname[$herbproperty['name']] = $herbproperty['latin'];
}
$herblatinbyname['Xin Yi Hua'] = $herblatinbyname['Xin Yi'] ?? 'Magnoliae Flos';
$herbenglishbyname = [
    'Ai Ye' => 'Mugwort Leaf',
    'Bai Shao' => 'White Peony Root',
    'Bai Zhu' => 'Largehead Atractylodes Rhizome',
    'Bai Zi Ren' => 'Arborvitae Seed',
    'Ban Xia' => 'Pinellia Rhizome',
    'Bie Jia' => 'Softshell Turtle Shell',
    'Bing Pian' => 'Borneol',
    'Bu Gu Zhi' => 'Psoralea Fruit',
    'Cang Er Zi' => 'Cocklebur Fruit',
    'Cang Zhu' => 'Atractylodes Rhizome',
    'Ce Bai Ye' => 'Arborvitae Leaf',
    'Chai Hu' => 'Bupleurum Root',
    'Chan Tui' => 'Cicada Molting',
    'Chen Pi' => 'Aged Tangerine Peel',
    'Chi Shao' => 'Red Peony Root',
    'Chuan Bei Mu' => 'Fritillaria Bulb',
    'Chuan Lian Zi' => 'Sichuan Chinaberry Fruit',
    'Chuan Xiong' => 'Sichuan Lovage Rhizome',
    'Da Huang' => 'Rhubarb Root and Rhizome',
    'Da Zao' => 'Jujube Fruit',
    'Dan Shen' => 'Red Sage Root',
    'Dang Gui' => 'Chinese Angelica Root',
    'Dang Shen' => 'Codonopsis Root',
    'Di Gu Pi' => 'Lycium Root Bark',
    'Ding Xiang' => 'Clove',
    'Du Huo' => 'Pubescent Angelica Root',
    'Du Zhong' => 'Eucommia Bark',
    'E Jiao' => 'Donkey-Hide Gelatin',
    'Fang Feng' => 'Siler Root',
    'Fu Ling' => 'Poria',
    'Fu Zi' => 'Prepared Aconite Root',
    'Gan Cao' => 'Licorice Root',
    'Gan Jiang' => 'Dried Ginger Rhizome',
    'Ge Gen' => 'Kudzu Root',
    'Gou Qi Zi' => 'Goji Berry',
    'Gou Teng' => 'Gambir Vine Hook',
    'Gua Lou' => 'Trichosanthes Fruit',
    'Gui Ban' => 'Tortoise Plastron',
    'Gui Zhi' => 'Cinnamon Twig',
    'Hai Zao' => 'Sargassum Seaweed',
    'Han Fang Ji' => 'Stephania Root',
    'He Shou Wu' => 'Fleeceflower Root',
    'Hong Hua' => 'Safflower',
    'Hou Po' => 'Magnolia Bark',
    'Hua Jiao' => 'Sichuan Pepper',
    'Hua Shi' => 'Talc',
    'Huang Bai' => 'Phellodendron Bark',
    'Huang Lian' => 'Coptis Rhizome',
    'Huang Qi' => 'Astragalus Root',
    'Huang Qin' => 'Scutellaria Root',
    'Huo Ma Ren' => 'Hemp Seed',
    'Huo Xiang' => 'Patchouli Herb',
    'Jiang Huang' => 'Turmeric Rhizome',
    'Jin Yin Hua' => 'Honeysuckle Flower',
    'Jing Jie' => 'Schizonepeta Herb',
    'Ju Hua' => 'Chrysanthemum Flower',
    'Ku Shen' => 'Sophora Root',
    'Kun Bu' => 'Kelp',
    'Lai Fu Zi' => 'Radish Seed',
    'Lian Qiao' => 'Forsythia Fruit',
    'Ling Yang Jiao' => 'Antelope Horn',
    'Long Dan Cao' => 'Gentian Root',
    'Long Gu' => 'Fossilized Bone',
    'Long Yan Rou' => 'Longan Fruit',
    'Lu Jiao Jiao' => 'Deer Antler Gelatin',
    'Lu Rong' => 'Deer Antler Velvet',
    'Ma Huang' => 'Ephedra Herb',
    'Mai Men Dong' => 'Ophiopogon Root',
    'Mang Xiao' => 'Mirabilite',
    'Mo Yao' => 'Myrrh',
    'Mu Dan Pi' => 'Moutan Root Bark',
    'Mu Li' => 'Oyster Shell',
    'Mu Tong' => 'Akebia Stem',
    'Mu Xiang' => 'Costus Root',
    'Niu Bang Zi' => 'Burdock Fruit',
    'Niu Huang' => 'Cattle Gallstone',
    'Niu Xi' => 'Achyranthes Root',
    'Nu Zhen Zi' => 'Privet Fruit',
    'Pu Huang' => 'Cattail Pollen',
    'Qiang Huo' => 'Notopterygium Rhizome and Root',
    'Qin Jiao' => 'Gentiana Root',
    'Qing Dai' => 'Indigo Naturalis',
    'Qing Hao' => 'Sweet Wormwood Herb',
    'Quan Xie' => 'Scorpion',
    'Ren Shen' => 'Ginseng Root',
    'Rou Cong Rong' => 'Cistanche Stem',
    'Rou Gui' => 'Cinnamon Bark',
    'Ru Xiang' => 'Frankincense',
    'San Leng' => 'Sparganium Rhizome',
    'Sang Piao Xiao' => 'Mantis Egg Case',
    'Sang Ye' => 'Mulberry Leaf',
    'Sha Ren' => 'Amomum Fruit',
    'Sha Shen' => 'Glehnia Root',
    'Shan Yao' => 'Chinese Yam Rhizome',
    'Shan Zha' => 'Hawthorn Fruit',
    'Shan Zhu Yu' => 'Cornus Fruit',
    'She Xiang' => 'Musk',
    'Sheng Di Huang' => 'Raw Rehmannia Root',
    'Sheng Jiang' => 'Fresh Ginger Rhizome',
    'Shi Gao' => 'Gypsum',
    'Shi Jue Ming' => 'Abalone Shell',
    'Shu Di Huang' => 'Prepared Rehmannia Root',
    'Shui Niu Jiao' => 'Water Buffalo Horn',
    'Suan Zao Ren' => 'Sour Jujube Seed',
    'Tao Ren' => 'Peach Kernel',
    'Tian Hua Fen' => 'Trichosanthes Root',
    'Tian Ma' => 'Gastrodia Rhizome',
    'Tian Nan Xing' => 'Arisaema Rhizome',
    'Tu Si Zi' => 'Dodder Seed',
    'Wu Gong' => 'Centipede',
    'Wu Ling Zhi' => 'Flying Squirrel Feces',
    'Wu Mei' => 'Mume Fruit',
    'Wu Yao' => 'Lindera Root',
    'Wu Zhu Yu' => 'Evodia Fruit',
    'Xi Xin' => 'Asarum Herb',
    'Xi Yang Shen' => 'American Ginseng Root',
    'Xian Mao' => 'Curculigo Rhizome',
    'Xiang Fu' => 'Cyperus Rhizome',
    'Xiang Ru' => 'Mosla Herb',
    'Xiao Hui Xiang' => 'Fennel Fruit',
    'Xing Ren' => 'Apricot Kernel',
    'Xuan Fu Hua' => 'Inula Flower',
    'Xuan Shen' => 'Scrophularia Root',
    'Yan Hu Suo' => 'Corydalis Rhizome',
    'Yi Tang' => 'Maltose',
    'Yi Yi Ren' => 'Coix Seed',
    'Yi Zhi Ren' => 'Alpinia Fruit',
    'Yin Chen' => 'Capillaris Herb',
    'Yin Chen Hao' => 'Capillaris Herb',
    'Yin Yang Huo' => 'Epimedium Herb',
    'Yu Jin' => 'Curcuma Root Tuber',
    'Ze Xie' => 'Alisma Rhizome',
    'Zhe Bei Mu' => 'Zhejiang Fritillaria Bulb',
    'Zhi Ke' => 'Immature Bitter Orange Fruit',
    'Zhi Mu' => 'Anemarrhena Rhizome',
    'Zhi Shi' => 'Immature Bitter Orange',
    'Zhi Zi' => 'Gardenia Fruit',
    'Zhu Ling' => 'Polyporus',
    'Zhu Ru' => 'Bamboo Shavings',
    'Zi Cao' => 'Purple Gromwell Root',
    'Zi Su Ye' => 'Perilla Leaf',
    'Zi Su Zi' => 'Perilla Seed',
    'Zi Wan' => 'Aster Root and Rhizome',
];
foreach ($herblatinbyname as $herbname => $pharmaceuticalname) {
    if (!isset($herbenglishbyname[$herbname])) {
        $herbenglishbyname[$herbname] = $pharmaceuticalname;
    }
}
$herbenglishbyname += [
    'An Xi Xiang' => 'Benzoin Resin',
    'Bai Fu Zi' => 'Typhonium Rhizome',
    'Chao Bai Zhu' => 'Dry-Fried Largehead Atractylodes Rhizome',
    'Chao Qian Shi' => 'Dry-Fried Foxnut Seed',
    'Chao Sha Yuan Zi' => 'Dry-Fried Astragalus Seed',
    'Chao Shan Yao' => 'Dry-Fried Chinese Yam Rhizome',
    'Chuan Jiao' => 'Sichuan Pepper',
    'Dan Nan Xing' => 'Bile-Prepared Arisaema Rhizome',
    'Duan Meng Shi' => 'Calcined Chlorite Schist',
    'Duan Mu Li' => 'Calcined Oyster Shell',
    'Fu Ling Pi' => 'Poria Peel',
    'Fu Rong Ye' => 'Hibiscus Leaf',
    'Ge Hua' => 'Kudzu Flower',
    'Gua Di' => 'Melon Pedicel',
    'Gui Ban Jiao' => 'Tortoise Plastron Gelatin',
    'Hai Dai' => 'Kelp',
    'He Shi' => 'Carpesium Fruit',
    'Huai Hua' => 'Sophora Flower',
    'Ji Zi Huang' => 'Egg Yolk',
    'Jiang Can' => 'Stiff Silkworm',
    'Jiu Da Huang' => 'Wine-Prepared Rhubarb Root and Rhizome',
    'Ju He' => 'Tangerine Seed',
    'Long Chi' => 'Fossilized Tooth',
    'Meng Chong' => 'Horsefly',
    'Mo Han Lian' => 'Eclipta Herb',
    'Pao Jiang' => 'Quick-Fried Ginger Rhizome',
    'Peng Sha' => 'Borax',
    'Sheng Ce Bai Ye' => 'Raw Arborvitae Leaf',
    'Sheng Chuan Wu' => 'Raw Sichuan Aconite Root',
    'Sheng Fu Zi' => 'Raw Aconite Root',
    'Sheng Nan Xing' => 'Raw Arisaema Rhizome',
    'Sheng Tie Luo' => 'Iron Filings',
    'Shi Di' => 'Persimmon Calyx',
    'Shi Jun Zi' => 'Quisqualis Fruit',
    'Shui Zhi' => 'Leech',
    'Su He Xiang' => 'Styrax Resin',
    'Su Zi' => 'Perilla Seed',
    'Tou Gu Cao' => 'Bone-Penetrating Herb',
    'Tu Chao Bai Zhu' => 'Earth-Fried Largehead Atractylodes Rhizome',
    'Wei Jing' => 'Reed Rhizome',
    'Wu Yi' => 'Elm Seed',
    'Xi Gua' => 'Watermelon',
    'Xian He Ye' => 'Fresh Lotus Leaf',
    'Xian Jin Yin Hua' => 'Fresh Honeysuckle Flower',
    'Xue Jie' => "Dragon's Blood Resin",
    'Yang Rou' => 'Mutton',
    'Yu Yu Liang' => 'Limonite',
    'Zao Xin Tu' => 'Yellow Hearth Earth',
    'Zhe Chong' => 'Ground Beetle',
    'Zhen Zhu Mu' => 'Mother-of-Pearl',
    'Zhi Ban Xia' => 'Prepared Pinellia Rhizome',
    'Zhi Cao Wu' => 'Prepared Kusnezoff Aconite Root',
    'Zhi Chuan Wu' => 'Prepared Sichuan Aconite Root',
    'Zhi Fu Zi' => 'Prepared Aconite Root',
    'Zhi Gan Cao' => 'Honey-Prepared Licorice Root',
    'Zhu Li' => 'Bamboo Sap',
    'Zhu Sha' => 'Cinnabar',
    'Zhu Ya Zao' => 'Chinese Honeylocust Fruit',
    'Zi He Che' => 'Human Placenta',
    'Zi Shi Ying' => 'Fluorite',
];
$herbenglishnames = array_keys($herbenglishbyname);
usort($herbenglishnames, static fn(string $left, string $right): int => strlen($right) <=> strlen($left));
$herbenglishpattern = '/(?<![A-Za-z])(' . implode('|', array_map(
    static fn(string $name): string => preg_quote($name, '/'),
    $herbenglishnames
)) . ')(?![A-Za-z])/u';
$renderherbswithenglish = static function(string $text) use ($herbenglishbyname, $herbenglishpattern): string {
    $parts = preg_split($herbenglishpattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($parts)) {
        return s($text);
    }
    $markup = '';
    foreach ($parts as $part) {
        if (isset($herbenglishbyname[$part])) {
            $englishname = $herbenglishbyname[$part];
            $markup .= s($part) . ' <small class="formula-study-guide__herb-english" lang="en"'
                . ' data-herb-english-speech="' . s($englishname) . '">(' . s($englishname) . ')</small>';
        } else {
            $markup .= s($part);
        }
    }
    return $markup;
};

$categorycopy = [
    'release-exterior' => [
        'Release external pathogens and distinguish Wind-Cold from Wind-Heat presentations.',
        'Libera patógenos externos y distingue los cuadros de Viento-Frío de los de Viento-Calor.',
    ],
    'clear-heat' => [
        'Differentiate Qi-level Heat, Fire-Toxin, Lung Heat, Damp-Heat and deficiency Heat.',
        'Distingue Calor del nivel Qi, Fuego-Toxicidad, Calor de Pulmón, Humedad-Calor y Calor por deficiencia.',
    ],
    'purge' => [
        'Compare the three Cheng Qi formulas by severity, dryness, fullness and focal distention; then separate Run Chang Wan as a moistening laxative for Blood-fluid deficiency.',
        'Compara las tres fórmulas Cheng Qi según gravedad, sequedad, plenitud y distensión focal; después distingue Run Chang Wan como laxante humectante para deficiencia de Sangre-líquidos.',
    ],
    'harmonize' => [
        'Recognize Shaoyang, Liver-Spleen and mixed Cold-Heat disharmony.',
        'Reconoce desarmonías de Shaoyang, Hígado-Bazo y Frío-Calor mixtos.',
    ],
    'expel-damp' => [
        'Separate Dampness, water retention, painful urinary dysfunction and Wind-Damp obstruction.',
        'Separa Humedad, retención de agua, disfunción urinaria dolorosa y obstrucción por Viento-Humedad.',
    ],
    'warm-interior' => [
        'Distinguish Middle-Burner deficiency-Cold, Jueyin Cold and devastated Yang.',
        'Distingue Frío por deficiencia del Jiao Medio, Frío de Jueyin y colapso de Yang.',
    ],
    'tonify' => [
        'Compare Qi, Blood, Yin and Yang deficiency through decisive accompanying signs.',
        'Compara deficiencias de Qi, Sangre, Yin y Yang mediante los signos acompañantes decisivos.',
    ],
    'regulate-qi' => [
        'Follow rebellious Qi, constraint, Phlegm and Heat across the chest, throat, Lung and Stomach.',
        'Sigue el Qi rebelde, el estancamiento, la Flema y el Calor en pecho, garganta, Pulmón y Estómago.',
    ],
    'invigorate-blood' => [
        'Locate Blood stasis and distinguish Heat, Cold, chest and uterine presentations.',
        'Localiza la estasis de Sangre y distingue cuadros de Calor, Frío, pecho y útero.',
    ],
    'stabilize-bind' => [
        'Differentiate unstable Exterior, daybreak diarrhea and uterine bleeding.',
        'Distingue Exterior inestable, diarrea al amanecer y sangrado uterino.',
    ],
    'calm-shen' => [
        'Separate Blood/Yin deficiency insomnia from Zang Zao emotional lability.',
        'Separa el insomnio por deficiencia de Sangre/Yin de la labilidad emocional de Zang Zao.',
    ],
    'expel-wind' => [
        'Distinguish external skin Wind, channel obstruction and internal Liver Wind.',
        'Distingue Viento externo en la piel, obstrucción de canales y Viento interno de Hígado.',
    ],
    'transform-phlegm' => [
        'Identify Damp-, Heat-, Dry- and Wind-Phlegm by sputum, digestion and Shen clues.',
        'Identifica Flema-Humedad, Flema-Calor, Flema-Sequedad y Flema-Viento por esputo, digestión y Shen.',
    ],
    'digestive' => [
        'Recognize food retention and the hallmark sour, foul and greasy signs.',
        'Reconoce retención de alimentos y sus signos ácidos, fétidos y grasosos característicos.',
    ],
    'digestive-chapter-18' => [
        'Compare all eight principal Chapter 18 formulas by excess versus deficiency, Damp-Heat, stool pattern, parasites, focal fullness and alcohol-Dampness.',
        'Compara las ocho fórmulas principales del capítulo 18 por exceso o deficiencia, Humedad-Calor, patrón de heces, parásitos, plenitud focal y Humedad por alcohol.',
    ],
    'expel-parasites-chapter-19' => [
        'Compare the three principal Chapter 19 formulas by mixed Heat-Cold, direct attack versus support, constitutional deficiency, pediatric wasting and modern toxicity.',
        'Compara las tres fórmulas principales del capítulo 19 por Calor-Frío mixto, ataque directo frente a apoyo, deficiencia constitucional, emaciación pediátrica y toxicidad moderna.',
    ],
    'treat-abscesses-chapter-20' => [
        'Compare all fourteen principal Chapter 20 formulas by external versus internal location, lesion stage, Heat versus Cold, pus, deficiency and emergency priority.',
        'Compara las catorce fórmulas principales del capítulo 20 por localización externa o interna, etapa de la lesión, Calor o Frío, pus, deficiencia y prioridad de urgencia.',
    ],
    'external-application-chapter-21' => [
        'Compare all eleven principal Chapter 21 formulas by dosage form, tissue integrity, moisture, temperature, lesion stage, internal versus external intent and modern safety.',
        'Compara las once fórmulas principales del capítulo 21 por forma de aplicación, integridad del tejido, humedad, temperatura, etapa, intención interna o externa y seguridad moderna.',
    ],
    'dispel-summerheat' => [
        'Choose among five Summerheat strategies: clear mild Heat, release constrained Exterior, drain Dampness, restore Qi and fluids, or preserve severely damaged Yin.',
        'Elige entre cinco estrategias para Calor de Verano: aclarar Calor leve, liberar el Exterior constreñido, drenar Humedad, restaurar Qi y líquidos o conservar Yin gravemente lesionado.',
    ],
    'warm-interior-cold' => [
        'Locate Cold by depth: channels and vessels, the Middle Burner, or life-threatening devastation of Yang.',
        'Localiza el Frío por profundidad: canales y vasos, Jiao Medio o devastación potencialmente mortal del Yang.',
    ],
    'exterior-interior-excess' => [
        'Differentiate simultaneous exterior-interior excess by whether the interior requires purging, clearing Heat, or warming Cold.',
        'Distingue el exceso simultáneo Exterior-Interior según si el Interior requiere purgar, aclarar Calor o calentar Frío.',
    ],
    'tonify-chapter-8' => [
        'Study the complete tonification chapter in six sections: Qi, Blood, Qi and Blood, Yin, Yang, and combined Yin-Yang.',
        'Estudia el capítulo completo de tonificación en seis secciones: Qi, Sangre, Qi y Sangre, Yin, Yang y Yin-Yang combinados.',
    ],
    'stabilize-bind-chapter-9' => [
        'Study all 17 principal formulas in Bensky Chapter 9 across five leakage-control sections while preserving the separate official NCBAHM Appendix C roster.',
        'Estudia las 17 fórmulas principales del capítulo 9 de Bensky en cinco secciones de control de pérdidas, conservando separado el listado oficial del Appendix C de NCBAHM.',
    ],
    'calm-shen-chapter-10' => [
        'Compare all nine principal formulas in Bensky Chapter 10 by deficiency, Fire, Phlegm, floating Yang, sensory signs, and safety.',
        'Compara las nueve fórmulas principales del capítulo 10 de Bensky por deficiencia, Fuego, Flema, Yang flotante, signos sensoriales y seguridad.',
    ],
    'open-orifices-chapter-11' => [
        'Compare seven principal formulas in Bensky Chapter 11 by hot versus cold closure, Heat, Phlegm, internal Wind, and emergency safety.',
        'Compara las siete fórmulas principales del capítulo 11 de Bensky por cierre caliente o frío, Calor, Flema, Viento interno y seguridad de urgencia.',
    ],
    'regulate-qi-chapter-12' => [
        'Compare all 20 principal formulas in Bensky Chapter 12 by Qi constraint, organ location, Cold, Heat, Phlegm, deficiency, and the direction of rebellious Qi.',
        'Compara las 20 fórmulas principales del capítulo 12 de Bensky por estancamiento de Qi, localización orgánica, Frío, Calor, Flema, deficiencia y dirección del Qi rebelde.',
    ],
    'regulate-blood-chapter-13' => [
        'Compare all 23 principal formulas in Bensky Chapter 13 across Blood stasis and bleeding patterns, using Heat, Cold, deficiency, location, and urgency as key distinguishing features.',
        'Compara las 23 fórmulas principales del capítulo 13 de Bensky entre patrones de estasis y sangrado, usando Calor, Frío, deficiencia, localización y urgencia como datos clave para diferenciarlas.',
    ],
    'expel-wind-chapter-14' => [
        'Compare all 17 principal formulas in Bensky Chapter 14 by external versus internal Wind, Heat, Cold, Phlegm, Blood-Yin deficiency, channel location, and urgency.',
        'Compara las 17 fórmulas principales del capítulo 14 de Bensky por Viento externo o interno, Calor, Frío, Flema, deficiencia de Sangre-Yin, localización en canales y urgencia.',
    ],
    'expel-damp-chapter-16' => [
        'Compare all 33 principal formulas in Bensky Chapter 16 across five routes: transform the Middle, clear Damp-Heat, promote urination, warm water metabolism, and dispel Wind-Dampness.',
        'Compara las 33 fórmulas principales del capítulo 16 de Bensky en cinco rutas: transformar el Centro, aclarar Humedad-Calor, promover la orina, calentar el metabolismo del agua y dispersar Viento-Humedad.',
    ],
    'transform-phlegm-chapter-17' => [
        'Compare all 17 principal formulas in Bensky Chapter 17 across seven routes: dry Dampness, clear Phlegm-Heat, disperse clumping, moisten dryness, warm Phlegm-Cold, extinguish Wind-Phlegm, and recognize the historical emetic method. Xiao Xian Xiong Tang remains an associated formula under Qing Qi Hua Tan Wan rather than a separate principal entry.',
        'Compara las 17 fórmulas principales del capítulo 17 de Bensky en siete rutas: secar Humedad, aclarar Flema-Calor, dispersar aglomeraciones, humedecer Sequedad, calentar Flema-Frío, extinguir Viento-Flema y reconocer el método emético histórico. Xiao Xian Xiong Tang permanece como fórmula asociada bajo Qing Qi Hua Tan Wan, no como entrada principal separada.',
    ],
    'treat-dryness-ch15' => [
        'Compare eight principal formulas in Bensky Chapter 15 by external versus internal dryness, cool versus warm dryness, organ location, fluid damage, rebellious Qi, thirst, and constipation.',
        'Compara las ocho fórmulas principales del capítulo 15 de Bensky por Sequedad externa o interna, fresca o cálida, localización orgánica, lesión de líquidos, Qi rebelde, sed y estreñimiento.',
    ],
    'official-formulas-ch3' => [
        'Additional detailed profiles extend the platform coverage without changing the official Appendix C roster.',
        'Las fichas detalladas adicionales amplían la cobertura de la plataforma sin cambiar el listado oficial del Appendix C.',
    ],
    'tonify-qi-ch8' => [
        'Differentiate Qi deficiency by organ, sinking, unstable exterior, fluid loss, Dampness, and failure to grasp Qi.',
        'Distingue la deficiencia de Qi por órgano, hundimiento, Exterior inestable, pérdida de líquidos, Humedad e incapacidad para captar Qi.',
    ],
    'tonify-blood-ch8' => [
        'Separate simple Blood deficiency from Cold, stasis, spasm, menstrual loss, and postpartum pain.',
        'Separa deficiencia simple de Sangre de Frío, estasis, espasmo, pérdida menstrual y dolor posparto.',
    ],
    'tonify-qi-blood-ch8' => [
        'Choose combined tonification by temperature, dominant organ, bleeding, irregular pulse, and obstetric context.',
        'Elige tonificación combinada por temperatura, órgano dominante, sangrado, pulso irregular y contexto obstétrico.',
    ],
    'nourish-tonify-yin-ch8' => [
        'Locate Yin deficiency in the Kidney-Liver, Lung, or Stomach and grade deficiency Fire, dryness, and essence depletion.',
        'Localiza la deficiencia de Yin en Riñón-Hígado, Pulmón o Estómago y gradúa Fuego por deficiencia, sequedad y agotamiento de esencia.',
    ],
    'warm-tonify-yang-ch8' => [
        'Distinguish impaired water transformation, deep exhaustion, leakage, and fertility presentations of Kidney Yang deficiency.',
        'Distingue transformación alterada del agua, agotamiento profundo, pérdidas y cuadros de fertilidad por deficiencia de Yang de Riñón.',
    ],
    'tonify-yin-yang-ch8' => [
        'Separate severe pure depletion from Phlegm obstruction, Liver-Blood deficiency, and the pattern of Cold below with Fire above.',
        'Separa agotamiento puro grave de obstrucción por Flema, deficiencia de Sangre de Hígado y el patrón de Frío abajo con Fuego arriba.',
    ],
];

$categoryreferences = [
    'expel-damp' => [
        'Original detailed-profile group: 8 formulas. All eight also appear among the 33 principal formulas in complete Bensky Chapter 16.',
        'Grupo original de fichas detalladas: 8 fórmulas. Las ocho también aparecen entre las 33 fórmulas principales del capítulo 16 completo de Bensky.',
    ],
    'expel-damp-chapter-16' => [
        'Complete Bensky source: 33 principal formulas in five sections. Original 63-profile overlap: 8—Ping Wei San, Huo Xiang Zheng Qi San, Ba Zheng San, Wu Ling San, Zhu Ling Tang, Wu Pi San, Zhen Wu Tang, and Du Huo Ji Sheng Tang.',
        'Fuente Bensky completa: 33 fórmulas principales en cinco secciones. Coincidencia con las 63 fichas originales: 8—Ping Wei San, Huo Xiang Zheng Qi San, Ba Zheng San, Wu Ling San, Zhu Ling Tang, Wu Pi San, Zhen Wu Tang y Du Huo Ji Sheng Tang.',
    ],
    'transform-phlegm' => [
        'Original detailed-profile group: 5 formulas. All five also appear among the 17 principal formulas in complete Bensky Chapter 17.',
        'Grupo original de fichas detalladas: 5 fórmulas. Las cinco también aparecen entre las 17 fórmulas principales del capítulo 17 completo de Bensky.',
    ],
    'transform-phlegm-chapter-17' => [
        'Complete Bensky source: 17 principal formulas in seven sections, plus Xiao Xian Xiong Tang as an associated formula under Qing Qi Hua Tan Wan. Original 63-profile overlap: 5—Er Chen Tang, Wen Dan Tang, Bei Mu Gua Lou San, Zhi Sou San, and Ban Xia Bai Zhu Tian Ma Tang.',
        'Fuente Bensky completa: 17 fórmulas principales en siete secciones, más Xiao Xian Xiong Tang como fórmula asociada bajo Qing Qi Hua Tan Wan. Coincidencia con las 63 fichas originales: 5—Er Chen Tang, Wen Dan Tang, Bei Mu Gua Lou San, Zhi Sou San y Ban Xia Bai Zhu Tian Ma Tang.',
    ],
    'digestive' => [
        'Original detailed-profile group: 1 formula—Bao He Wan. It also appears among the eight principal formulas in complete Bensky Chapter 18.',
        'Grupo original de fichas detalladas: 1 fórmula—Bao He Wan. También aparece entre las ocho fórmulas principales del capítulo 18 completo de Bensky.',
    ],
    'digestive-chapter-18' => [
        'Complete Bensky source: 8 principal formulas in two sections. Original 63-profile overlap: 1—Bao He Wan. The other seven profiles are clearly supplemental.',
        'Fuente Bensky completa: 8 fórmulas principales en dos secciones. Coincidencia con las 63 fichas originales: 1—Bao He Wan. Las otras siete fichas son claramente suplementarias.',
    ],
    'expel-parasites-chapter-19' => [
        'Complete Bensky source: 3 principal formulas in one section. Original 63-profile overlap: 0. All three profiles are supplemental, and two are primarily retained for historical toxicity recognition.',
        'Fuente Bensky completa: 3 fórmulas principales en una sección. Coincidencia con las 63 fichas originales: 0. Las tres fichas son suplementarias y dos se conservan principalmente para reconocer toxicidad histórica.',
    ],
    'treat-abscesses-chapter-20' => [
        'Complete Bensky source: 14 principal formulas in two sections. Original 63-profile overlap: 0. All profiles are supplemental and require modern medical or surgical triage before historical pattern comparison.',
        'Fuente Bensky completa: 14 fórmulas principales en dos secciones. Coincidencia con las 63 fichas originales: 0. Todas las fichas son suplementarias y exigen triaje médico o quirúrgico moderno antes de comparar patrones históricos.',
    ],
    'external-application-chapter-21' => [
        'Complete Bensky source: 11 principal formulas in two sections. Original 63-profile overlap: 0. All profiles are supplemental; associated formulas and variations remain reference comparisons with explicit modern safety boundaries.',
        'Fuente Bensky completa: 11 fórmulas principales en dos secciones. Coincidencia con las 63 fichas originales: 0. Todas las fichas son suplementarias; fórmulas asociadas y variaciones quedan como comparaciones con límites modernos explícitos de seguridad.',
    ],
    'stabilize-bind' => [
        'Original detailed-profile group: 3 formulas. Cross-reference: complete Bensky Chapter 9 has 17 formulas; only Si Shen Wan and Gu Jing Wan overlap.',
        'Grupo original de fichas detalladas: 3 fórmulas. Referencia cruzada: el capítulo 9 completo de Bensky tiene 17; solo coinciden Si Shen Wan y Gu Jing Wan.',
    ],
    'stabilize-bind-chapter-9' => [
        'Complete Bensky source: 17 formulas. Original 63-profile overlap: 2. Yu Ping Feng San is the third internal Stabilize and Bind profile and is also in official Appendix C, but Bensky studies it in Chapter 8.',
        'Fuente Bensky completa: 17 fórmulas. Coincidencia con las 63 fichas originales: 2. Yu Ping Feng San es la tercera ficha interna de Estabilizar y contener y también aparece en el Appendix C oficial, pero Bensky la estudia en el capítulo 8.',
    ],
    'calm-shen' => [
        'Terminology: “Calm Shen” and “Calm the Spirit” are used interchangeably for the same formula family. Original detailed-profile group: 3 formulas. All three also appear in complete Bensky Chapter 10.',
        'Terminología: “Calm Shen”, “Calmar el Shen” y “Calmar el Espíritu” se usan indistintamente para la misma familia. Grupo original de fichas detalladas: 3 fórmulas; las tres aparecen en el capítulo 10 completo de Bensky.',
    ],
    'calm-shen-chapter-10' => [
        'Terminology: “Formulas That Calm the Spirit” is the same family called “Calm Shen” by the platform, not a second category. Complete Bensky source: 9 formulas; Original 63-profile overlap: 3.',
        'Terminología: “Fórmulas que calman el Espíritu” es la misma familia que la plataforma llama “Calm Shen”, no otra categoría. Fuente Bensky completa: 9 fórmulas; coincidencia con las 63 fichas originales: 3.',
    ],
    'regulate-qi' => [
        'Original detailed-profile group: 5 formulas. All five also appear among the 20 principal formulas in complete Bensky Chapter 12.',
        'Grupo original de fichas detalladas: 5 fórmulas. Las cinco también aparecen entre las 20 fórmulas principales del capítulo 12 completo de Bensky.',
    ],
    'regulate-qi-chapter-12' => [
        'Complete Bensky source: 20 principal formulas. Original 63-profile overlap: 5—Yue Ju Wan, Ban Xia Hou Po Tang, Su Zi Jiang Qi Tang, Ding Chuan Tang, and Ju Pi Zhu Ru Tang.',
        'Fuente Bensky completa: 20 fórmulas principales. Coincidencia con las 63 fichas originales: 5—Yue Ju Wan, Ban Xia Hou Po Tang, Su Zi Jiang Qi Tang, Ding Chuan Tang y Ju Pi Zhu Ru Tang.',
    ],
    'invigorate-blood' => [
        'Original detailed-profile group: 4 formulas. All four also appear among the 23 principal formulas in complete Bensky Chapter 13.',
        'Grupo original de fichas detalladas: 4 fórmulas. Las cuatro también aparecen entre las 23 fórmulas principales del capítulo 13 completo de Bensky.',
    ],
    'regulate-blood-chapter-13' => [
        'Complete Bensky source: 23 principal formulas in two sections. Original 63-profile overlap: 4—Tao He Cheng Qi Tang, Xue Fu Zhu Yu Tang, Wen Jing Tang, and Gui Zhi Fu Ling Wan.',
        'Fuente Bensky completa: 23 fórmulas principales en dos secciones. Coincidencia con las 63 fichas originales: 4—Tao He Cheng Qi Tang, Xue Fu Zhu Yu Tang, Wen Jing Tang y Gui Zhi Fu Ling Wan.',
    ],
    'expel-wind' => [
        'Original detailed-profile group: 3 formulas. All three also appear among the 17 principal formulas in complete Bensky Chapter 14.',
        'Grupo original de fichas detalladas: 3 fórmulas. Las tres también aparecen entre las 17 fórmulas principales del capítulo 14 completo de Bensky.',
    ],
    'expel-wind-chapter-14' => [
        'Complete Bensky source: 17 principal formulas in two sections. Original 63-profile overlap: 3—Xiao Feng San, Xiao Huo Luo Dan, and Tian Ma Gou Teng Yin.',
        'Fuente Bensky completa: 17 fórmulas principales en dos secciones. Coincidencia con las 63 fichas originales: 3—Xiao Feng San, Xiao Huo Luo Dan y Tian Ma Gou Teng Yin.',
    ],
    'official-formulas-ch3' => [
        'Bensky Chapter 2 expansion: a detailed profile for an official formula beyond the original 63-profile internal sequence.',
        'Ampliación del capítulo 2 de Bensky: una ficha detallada de una fórmula oficial adicional a la secuencia interna original de 63 perfiles.',
    ],
];

$categories = [];
foreach ($data['categories'] as $category) {
    $category['formulas'] = [];
    $categories[$category['key']] = $category;
}
foreach ($data['formulas'] as $formula) {
    if (isset($categories[$formula['categoryKey']])) {
        $categories[$formula['categoryKey']]['formulas'][] = $formula;
    }
}

$boardformulasbyname = [];
foreach ($data['formulas'] as $boardformula) {
    $boardformulasbyname[$boardformula['name']] = $boardformula;
}
$boardformulaaliases = [
    'Shen Qi Wan' => 'Jin Gui Shen Qi Wan',
];
$getboardformula = static function(string $name) use ($boardformulasbyname, $boardformulaaliases): ?array {
    $boardname = $boardformulaaliases[$name] ?? $name;
    return $boardformulasbyname[$boardname] ?? null;
};
$officialformulasbyname = [];
foreach ($officialroster['formulas'] as $officialformula) {
    $officialformulasbyname[$officialformula['name']] = $officialformula;
}
$officialformulaaliases = [
    'Jin Gui Shen Qi Wan' => 'Shen Qi Wan',
];
$formulaenglishnamesbyname = [];
foreach ($officialroster['formulas'] as $officialformula) {
    $formulaenglishnamesbyname[$officialformula['name']] = $officialformula['englishName'];
}
foreach ($data['supplementalFormulas'] as $supplementalformula) {
    $formulaenglishnamesbyname[$supplementalformula['name']] = $supplementalformula['englishName'];
}
$formulaenglishnamesbyname += [
    'Zhi Zi Chi Tang' => 'Gardenia and Prepared Soybean Decoction',
    'Jiao Tai Wan' => 'Pill to Restore Proper Communication',
    'Jie Geng Tang' => 'Platycodon Decoction',
    'Fo Shou San' => "Buddha's Hand Powder",
    'Xiang Fu Si Wu Tang' => 'Cyperus Four-Substance Decoction',
    'Xiang Leng Wan' => 'Aucklandia and Sparganium Pill',
    'Xiao Ban Xia Tang' => 'Minor Pinellia Decoction',
    'She Gan Ma Huang Tang' => 'Belamcanda and Ephedra Decoction',
    'Ting Li Da Zao Xie Fei Tang' => 'Descurainia and Jujube Decoction to Drain the Lungs',
    'San Jin Tang' => 'Three-Gold Decoction',
];
$formulaenglishnamealiases = [
    'Jin Gui Shen Qi Wan' => 'Shen Qi Wan',
    'Qing Shu Yi Qi Tang' => 'Qing Shu Yi Qi Tang (Wang Shi-Xiong)',
];
$getformulaenglishname = static function(string $name) use ($formulaenglishnamesbyname, $formulaenglishnamealiases): string {
    $lookupname = $formulaenglishnamealiases[$name] ?? $name;
    return trim((string) ($formulaenglishnamesbyname[$lookupname] ?? ''));
};
$normalizepinyin = static function(string $value): string {
    return strtr($value, [
        'ā' => 'a', 'á' => 'a', 'ǎ' => 'a', 'à' => 'a', 'Ā' => 'A', 'Á' => 'A', 'Ǎ' => 'A', 'À' => 'A',
        'ē' => 'e', 'é' => 'e', 'ě' => 'e', 'è' => 'e', 'Ē' => 'E', 'É' => 'E', 'Ě' => 'E', 'È' => 'E',
        'ī' => 'i', 'í' => 'i', 'ǐ' => 'i', 'ì' => 'i', 'Ī' => 'I', 'Í' => 'I', 'Ǐ' => 'I', 'Ì' => 'I',
        'ō' => 'o', 'ó' => 'o', 'ǒ' => 'o', 'ò' => 'o', 'Ō' => 'O', 'Ó' => 'O', 'Ǒ' => 'O', 'Ò' => 'O',
        'ū' => 'u', 'ú' => 'u', 'ǔ' => 'u', 'ù' => 'u', 'ǖ' => 'u', 'ǘ' => 'u', 'ǚ' => 'u', 'ǜ' => 'u',
        'Ū' => 'U', 'Ú' => 'U', 'Ǔ' => 'U', 'Ù' => 'U', 'Ǖ' => 'U', 'Ǘ' => 'U', 'Ǚ' => 'U', 'Ǜ' => 'U',
    ]);
};
$getrelatedformulaidentities = static function(string $text) use ($formulaenglishnamesbyname, $normalizepinyin): array {
    $haystack = strtolower($normalizepinyin($text));
    $matches = [];
    foreach ($formulaenglishnamesbyname as $name => $englishname) {
        $needle = strtolower($normalizepinyin($name));
        $offset = strpos($haystack, $needle);
        if ($offset === false) {
            continue;
        }
        $matches[] = [
            'offset' => $offset,
            'length' => strlen($needle),
            'name' => $name,
            'englishName' => $englishname,
        ];
    }
    usort($matches, static function(array $left, array $right): int {
        return $left['offset'] === $right['offset']
            ? $right['length'] <=> $left['length']
            : $left['offset'] <=> $right['offset'];
    });
    $identities = [];
    $occupieduntil = -1;
    foreach ($matches as $match) {
        if ($match['offset'] < $occupieduntil) {
            continue;
        }
        $identities[] = [
            'name' => $match['name'],
            'englishName' => $match['englishName'],
        ];
        $occupieduntil = $match['offset'] + $match['length'];
    }
    return $identities;
};
$isofficialformula = static function(string $name) use ($officialformulasbyname, $officialformulaaliases): bool {
    $officialname = $officialformulaaliases[$name] ?? $name;
    return isset($officialformulasbyname[$officialname]);
};
$officialformulanamesformarking = array_values(array_unique(array_merge(
    array_keys($officialformulasbyname),
    array_keys($officialformulaaliases)
)));
usort($officialformulanamesformarking, static fn(string $left, string $right): int => strlen($right) <=> strlen($left));
$officialformulacharpatterns = [
    'a' => '[aāáǎà]',
    'e' => '[eēéěè]',
    'i' => '[iīíǐì]',
    'o' => '[oōóǒò]',
    'u' => '[uūúǔùǖǘǚǜü]',
];
$officialformulapatterns = [];
foreach ($officialformulanamesformarking as $officialformulaname) {
    $pattern = '';
    foreach (str_split($officialformulaname) as $character) {
        $lowercharacter = strtolower($character);
        if (isset($officialformulacharpatterns[$lowercharacter])) {
            $pattern .= $officialformulacharpatterns[$lowercharacter];
        } elseif ($character === ' ') {
            $pattern .= '\\s+';
        } else {
            $pattern .= preg_quote($character, '/');
        }
    }
    $officialformulapatterns[] = $pattern;
}
$markofficialformulasintext = static function(string $text) use ($officialformulapatterns): string {
    foreach ($officialformulapatterns as $pattern) {
        $markedtext = preg_replace(
            '/(?<![\\p{L}\\p{N}])(' . $pattern . ')(?![\\p{L}\\p{N}*])/iu',
            '$1*',
            $text
        );
        if ($markedtext !== null) {
            $text = $markedtext;
        }
    }
    return $text;
};
$getboardstatus = static function(array $formulas, bool $spanish) use ($isofficialformula): array {
    $total = count($formulas);
    $included = 0;
    foreach ($formulas as $formula) {
        if ($isofficialformula($formula['name'])) {
            $included++;
        }
    }
    if ($included === 0) {
        $state = 'none';
        $summary = $spanish ? 'Ninguna incluida' : 'None included';
    } elseif ($included === $total) {
        $state = 'all';
        $summary = '';
    } else {
        $state = 'some';
        $summary = ($spanish ? 'Solo ' : 'Only ') . $included . ($spanish ? ' incluidas' : ' included');
    }
    return [
        'included' => $included,
        'total' => $total,
        'state' => $state,
        'label' => $state === 'all'
            ? ''
            : 'Appendix C: ' . $included . ($spanish ? ' de ' : ' of ') . $total . ' · ' . $summary,
    ];
};
$displayformulaname = static function(string $name) use ($isofficialformula): string {
    return $name . ($isofficialformula($name) ? '*' : '');
};
$renderformulanamewithenglish = static function(string $name) use ($displayformulaname, $getformulaenglishname): string {
    $englishname = $getformulaenglishname($name);
    $markup = s($displayformulaname($name));
    if ($englishname !== '') {
        $markup .= ' <small class="formula-study-guide__formula-english-inline" lang="en"'
            . ' data-formula-english-speech="' . s($englishname) . '">(' . s($englishname) . ')</small>';
    }
    return $markup;
};
$supplementalcategories = [];
foreach ($data['supplementalCategories'] as $category) {
    $category['formulas'] = [];
    $supplementalcategories[$category['key']] = $category;
}
foreach ($data['supplementalFormulas'] as $formula) {
    if (isset($supplementalcategories[$formula['categoryKey']])) {
        $supplementalcategories[$formula['categoryKey']]['formulas'][] = $formula;
    }
}

$tonifychapterkeys = [
    'tonify-qi-ch8',
    'tonify-blood-ch8',
    'tonify-qi-blood-ch8',
    'nourish-tonify-yin-ch8',
    'warm-tonify-yang-ch8',
    'tonify-yin-yang-ch8',
];
$tonifychaptercategories = array_intersect_key($supplementalcategories, array_flip($tonifychapterkeys));
$warmchaptercategory = $supplementalcategories['warm-interior-cold'];
$stabilizechaptersourcecategory = $supplementalcategories['stabilize-bind-ch9'];
$calmshenchaptersourcecategory = $supplementalcategories['calm-shen-ch10'];
$openorificeschaptersourcecategory = $supplementalcategories['open-orifices-ch11'];
$regulateqichaptersourcecategory = $supplementalcategories['regulate-qi-ch12'];
$regulatebloodchaptersourcecategory = $supplementalcategories['regulate-blood-ch13'];
$expelwindchaptersourcecategory = $supplementalcategories['expel-wind-ch14'];
$expeldampchaptersourcecategory = $supplementalcategories['expel-damp-ch16'];
$transformphlegmchaptersourcecategory = $supplementalcategories['transform-phlegm-ch17'];
$digestivechaptersourcecategory = $supplementalcategories['digestive-ch18'];
$expelparasiteschaptersourcecategory = $supplementalcategories['expel-parasites-ch19'];
$treatabscesseschaptersourcecategory = $supplementalcategories['treat-abscesses-ch20'];
$externalapplicationchaptersourcecategory = $supplementalcategories['external-application-ch21'];

$tonifyboardgroups = [
    'tonify-qi-ch8' => ['Si Jun Zi Tang', 'Bu Zhong Yi Qi Tang', 'Sheng Mai San', 'Yu Ping Feng San'],
    'tonify-blood-ch8' => ['Si Wu Tang'],
    'tonify-qi-blood-ch8' => ['Gui Pi Tang', 'Shi Quan Da Bu Tang'],
    'nourish-tonify-yin-ch8' => ['Liu Wei Di Huang Wan'],
    'warm-tonify-yang-ch8' => ['Jin Gui Shen Qi Wan'],
];

$tonifychapterformulas = [];
foreach ($tonifychaptercategories as $chaptercategory) {
    foreach ($chaptercategory['formulas'] as $chapterformula) {
        $tonifychapterformulas[] = $chapterformula;
    }
}
$tonifychapteroverview = [
    'number' => 'S4',
    'key' => 'tonify-chapter-8',
    'name' => 'Formulas That Tonify · Complete Chapter 8',
    'nameEs' => 'Fórmulas que tonifican · Capítulo 8 completo',
    'sourceChapter' => 8,
    'sourcePages' => '307–416',
    'formulas' => $tonifychapterformulas,
];

$stabilizechaptersections = [
    'exterior' => [
        'key' => 'exterior', 'name' => 'Stabilize the Exterior and Stop Sweating',
        'nameEs' => 'Estabilizar el Exterior y detener la sudoración', 'sourcePages' => '420–422',
        'copy' => 'Secure an unstable exterior only after excluding unresolved excess.',
        'copyEs' => 'Asegura un Exterior inestable solo después de excluir exceso no resuelto.',
    ],
    'lung' => [
        'key' => 'lung', 'name' => 'Secure the Lungs and Stop Coughing',
        'nameEs' => 'Asegurar los Pulmones y detener la tos', 'sourcePages' => '423–424',
        'copy' => 'Late-stage cough requires simultaneous support of depleted Lung Qi and Yin.',
        'copyEs' => 'La tos tardía exige apoyar simultáneamente Qi y Yin de Pulmón agotados.',
    ],
    'intestine' => [
        'key' => 'intestine', 'name' => 'Bind the Intestines and Secure Abandoned Conditions',
        'nameEs' => 'Contener los Intestinos y asegurar cuadros de abandono', 'sourcePages' => '425–434',
        'copy' => 'Differentiate deficiency-Cold leakage from unresolved Damp-Heat before binding the bowels.',
        'copyEs' => 'Diferencia pérdidas por Frío-deficiencia de Humedad-Calor no resuelta antes de contener los intestinos.',
    ],
    'essence' => [
        'key' => 'essence', 'name' => 'Secure the Essence and Stop Enuresis',
        'nameEs' => 'Asegurar la Esencia y detener la enuresis', 'sourcePages' => '435–442',
        'copy' => 'Separate Kidney leakage, Heart-Kidney disconnection, deficiency-Cold and Yin-Yang separation.',
        'copyEs' => 'Separa pérdida de Riñón, desconexión Corazón-Riñón, Frío-deficiencia y separación Yin-Yang.',
    ],
    'gynecology' => [
        'key' => 'gynecology', 'name' => 'Secure Irregular Uterine Bleeding and Stop Vaginal Discharge',
        'nameEs' => 'Asegurar el sangrado uterino irregular y detener el flujo vaginal', 'sourcePages' => '443–452',
        'copy' => 'Color, consistency, odor, temperature and deficiency-versus-excess determine the route.',
        'copyEs' => 'Color, consistencia, olor, temperatura y deficiencia frente a exceso determinan la ruta.',
    ],
];
$stabilizechapterplan = [
    ['Mu Li San', 1, 'exterior', 'Oyster Shell Powder', '420–422'],
    ['Jiu Xian San', 2, 'lung', 'Nine-Immortal Powder', '423–424'],
    ['Zhen Ren Yang Zang Tang', 3, 'intestine', "True Man's Decoction to Nourish the Organs", '425–426'],
    ['Tao Hua Tang', 4, 'intestine', 'Peach Blossom Decoction', '427–429'],
    ['Si Shen Wan', 5, 'intestine', 'Four-Miracle Pill', '429–431'],
    ['Zhu Che Wan', 6, 'intestine', 'Halt the Carts Pill', '432–433'],
    ['Yi Huang San', 7, 'intestine', 'Benefit the Yellow Powder', '433–434'],
    ['Jin Suo Gu Jing Wan', 8, 'essence', 'Metal Lock Pill to Stabilize the Essence', '435–436'],
    ['Sang Piao Xiao San', 9, 'essence', 'Mantis Egg-Case Powder', '436–438'],
    ['Suo Quan Wan', 10, 'essence', 'Restrict the Fountain Pill', '438–439'],
    ['Gui Zhi Jia Long Gu Mu Li Tang', 11, 'essence', 'Cinnamon Twig Decoction plus Dragon Bone and Oyster Shell', '439–441'],
    ['Shou Tai Wan', 12, 'essence', 'Fetus Longevity Pill', '441–442'],
    ['Gu Chong Tang', 13, 'gynecology', 'Stabilize Gushing Decoction', '443–444'],
    ['Gu Jing Wan', 14, 'gynecology', 'Stabilize the Menses Pill', '445–446'],
    ['Shen Ling Dan', 15, 'gynecology', 'Pregnancy Panacea', '446–447'],
    ['Wan Dai Tang', 16, 'gynecology', 'End Discharge Decoction', '447–450'],
    ['Yi Huang Tang', 17, 'gynecology', 'Change Yellow Discharge Decoction', '450–452'],
];
$stabilizesupplementbyname = [];
foreach ($stabilizechaptersourcecategory['formulas'] as $formula) {
    $stabilizesupplementbyname[$formula['name']] = $formula;
}
$stabilizechapterformulas = [];
foreach ($stabilizechapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $stabilizesupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 9 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $stabilizechaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $stabilizechaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $stabilizechapterformulas[] = $formula;
}
$stabilizechapteroverview = [
    'number' => 'S5',
    'key' => 'stabilize-bind-chapter-9',
    'name' => 'Formulas That Stabilize and Bind · Complete Bensky Chapter 9',
    'nameEs' => 'Fórmulas que estabilizan y contienen · Capítulo 9 completo de Bensky',
    'sourceChapter' => 9,
    'sourcePages' => '419–454',
    'formulas' => $stabilizechapterformulas,
];

$calmshenchaptersections = [
    'nourish' => [
        'key' => 'nourish', 'name' => 'Nourish the Heart and Calm the Spirit',
        'nameEs' => 'Nutrir el Corazón y calmar el Shen', 'sourcePages' => '459–474',
        'copy' => 'Rebuild deficient Qi, Blood or Yin while differentiating constrained Fire and Phlegm.',
        'copyEs' => 'Reconstruye Qi, Sangre o Yin deficientes diferenciando Fuego constreñido y Flema.',
    ],
    'sedate' => [
        'key' => 'sedate', 'name' => 'Sedate and Calm the Spirit',
        'nameEs' => 'Sedar y calmar el Shen', 'sourcePages' => '475–481',
        'copy' => 'Recognize heavy-settling historical formulas while excluding toxic, endangered and obsolete substances.',
        'copyEs' => 'Reconoce fórmulas históricas que asientan intensamente excluyendo sustancias tóxicas, amenazadas y obsoletas.',
    ],
];
$calmshenchapterplan = [
    ['Tian Wang Bu Xin Dan', 1, 'nourish', "Emperor of Heaven's Special Pill to Tonify the Heart", '459–461'],
    ['Suan Zao Ren Tang', 2, 'nourish', 'Sour Jujube Decoction', '462–465'],
    ['Ding Zhi Wan', 3, 'nourish', 'Settle the Emotions Pill', '465–468'],
    ['Huang Lian E Jiao Tang', 4, 'nourish', 'Coptis and Ass-Hide Gelatin Decoction', '469–470'],
    ['Gan Mai Da Zao Tang', 5, 'nourish', 'Licorice, Wheat, and Jujube Decoction', '471–474'],
    ['Zhu Sha An Shen Wan', 6, 'sedate', 'Cinnabar Pill to Calm the Spirit', '475–476'],
    ['Ci Zhu Wan', 7, 'sedate', 'Magnetite and Cinnabar Pill', '476–478'],
    ['Sheng Tie Luo Yin', 8, 'sedate', 'Iron Filings Drink', '478–480'],
    ['Zhen Zhu Mu Wan', 9, 'sedate', 'Mother-of-Pearl Pill', '480–481'],
];
$calmshensupplementbyname = [];
foreach ($calmshenchaptersourcecategory['formulas'] as $formula) {
    $calmshensupplementbyname[$formula['name']] = $formula;
}
$calmshenchapterformulas = [];
foreach ($calmshenchapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $calmshensupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 10 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $calmshenchaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $calmshenchaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $calmshenchapterformulas[] = $formula;
}
$calmshenchapteroverview = [
    'number' => 'S6',
    'key' => 'calm-shen-chapter-10',
    'name' => 'Formulas That Calm the Spirit · Complete Bensky Chapter 10',
    'nameEs' => 'Fórmulas que calman el Shen · Capítulo 10 completo de Bensky',
    'sourceChapter' => 10,
    'sourcePages' => '457–482',
    'formulas' => $calmshenchapterformulas,
];

$openorificeschaptersections = [
    'clear' => [
        'key' => 'clear', 'name' => 'Clear Heat and Open the Sensory Orifices',
        'nameEs' => 'Aclarar Calor y abrir los orificios sensoriales', 'sourcePages' => '486–497',
        'copy' => 'Differentiate hot closure by the relative dominance of Heat-toxin, turbid Phlegm, internal Wind, and pediatric food stagnation.',
        'copyEs' => 'Diferencia cierre caliente por el predominio relativo de Calor-Toxicidad, Flema turbia, Viento interno y estancamiento alimentario pediátrico.',
    ],
    'warm' => [
        'key' => 'warm', 'name' => 'Warm and Open the Sensory Orifices',
        'nameEs' => 'Calentar y abrir los orificios sensoriales', 'sourcePages' => '497–501',
        'copy' => 'Recognize cold closure through pale-purple color, cold extremities, white greasy coating, turbid Phlegm, and a submerged pulse.',
        'copyEs' => 'Reconoce cierre frío por color pálido-morado, extremidades frías, saburra blanca grasosa, Flema turbia y pulso sumergido.',
    ],
];
$openorificeschapterplan = [
    ['Wan Shi Niu Huang Qing Xin Wan', 1, 'clear', "Wan's Cattle Gallstone Pill to Clear the Heart", '486–488'],
    ['An Gong Niu Huang Wan', 2, 'clear', 'Calm the Palace Pill with Cattle Gallstone', '488–490'],
    ['Zi Xue Dan', 3, 'clear', 'Purple Snow Special Pill', '490–493'],
    ['Zhi Bao Dan', 4, 'clear', 'Greatest Treasure Special Pill', '493–495'],
    ['Hui Chun Dan', 5, 'clear', 'Special Pill to Restore Life', '495–497'],
    ['Su He Xiang Wan', 6, 'warm', 'Liquid Styrax Pill', '498–500'],
    ['Tong Guan San', 7, 'warm', 'Open the Gate Powder', '500–501'],
];
$openorificessupplementbyname = [];
foreach ($openorificeschaptersourcecategory['formulas'] as $formula) {
    $openorificessupplementbyname[$formula['name']] = $formula;
}
$openorificeschapterformulas = [];
foreach ($openorificeschapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $openorificessupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 11 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $openorificeschaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $openorificeschaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $openorificeschapterformulas[] = $formula;
}
$openorificeschapteroverview = [
    'number' => 'S7',
    'key' => 'open-orifices-chapter-11',
    'name' => 'Formulas That Open the Sensory Orifices · Complete Bensky Chapter 11',
    'nameEs' => 'Fórmulas que abren los orificios sensoriales · Capítulo 11 completo de Bensky',
    'sourceChapter' => 11,
    'sourcePages' => '485–502',
    'formulas' => $openorificeschapterformulas,
];

$regulateqichaptersections = [
    'move' => [
        'key' => 'move', 'name' => 'Promote the Movement of Qi',
        'nameEs' => 'Promover el movimiento del Qi', 'sourcePages' => '506–536',
        'copy' => 'Locate constraint in the whole body, chest, throat, Middle Burner, Liver channel, lower abdomen, or menstruation before choosing how strongly to move Qi.',
        'copyEs' => 'Localiza el estancamiento en todo el cuerpo, pecho, garganta, Jiao Medio, canal de Hígado, abdomen inferior o menstruación antes de elegir con qué intensidad mover el Qi.',
    ],
    'descend' => [
        'key' => 'descend', 'name' => 'Direct Rebellious Qi Downward',
        'nameEs' => 'Dirigir el Qi rebelde hacia abajo', 'sourcePages' => '536–552',
        'copy' => 'Separate Lung rebellion with cough or wheezing from Stomach rebellion with hiccup, nausea, retching, or vomiting, then distinguish excess from deficiency.',
        'copyEs' => 'Separa rebelión de Pulmón con tos o sibilancias de rebelión de Estómago con hipo, náusea, arcadas o vómito, y luego distingue exceso de deficiencia.',
    ],
];
$regulateqichapterplan = [
    ['Yue Ju Wan', 1, 'move', 'Escape Restraint Pill', '507–511'],
    ['Chai Hu Shu Gan San', 2, 'move', 'Bupleurum Powder to Dredge the Liver', '512–513'],
    ['Shu Gan Tang', 3, 'move', 'Dredge the Liver Decoction', '513–514'],
    ['Zhi Shi Xie Bai Gui Zhi Tang', 4, 'move', 'Unripe Bitter Orange, Chinese Garlic, and Cinnamon Twig Decoction', '514–516'],
    ['Ban Xia Hou Po Tang', 5, 'move', 'Pinellia and Magnolia Bark Decoction', '516–519'],
    ['Hou Po Wen Zhong Tang', 6, 'move', 'Magnolia Bark Decoction for Warming the Middle', '519–521'],
    ['Liang Fu Wan', 7, 'move', 'Galangal and Cyperus Pill', '521–522'],
    ['Jin Ling Zi San', 8, 'move', 'Melia Toosendan Powder', '522–524'],
    ['Si Mo Tang', 9, 'move', 'Four Milled-Herb Decoction', '525–527'],
    ['Tian Tai Wu Yao San', 10, 'move', 'Top-Quality Lindera Powder', '527–529'],
    ['Ju He Wan', 11, 'move', 'Tangerine Seed Pill', '529–531'],
    ['Nuan Gan Jian', 12, 'move', 'Warm the Liver Decoction', '531–533'],
    ['Qi Ge San', 13, 'move', 'Open Up the Diaphragm Powder', '533–535'],
    ['Jia Wei Wu Yao Tang', 14, 'move', 'Augmented Lindera Decoction', '535–536'],
    ['Su Zi Jiang Qi Tang', 15, 'descend', 'Perilla Fruit Decoction for Directing Qi Downward', '537–540'],
    ['Ding Chuan Tang', 16, 'descend', 'Arrest Wheezing Decoction', '540–542'],
    ['Xuan Fu Dai Zhe Tang', 17, 'descend', 'Inula and Haematite Decoction', '542–544'],
    ['Ju Pi Zhu Ru Tang', 18, 'descend', 'Tangerine Peel and Bamboo Shavings Decoction', '544–547'],
    ['Ding Xiang Shi Di Tang', 19, 'descend', 'Clove and Persimmon Calyx Decoction', '547–549'],
    ['Da Ban Xia Tang', 20, 'descend', 'Major Pinellia Decoction', '549–552'],
];
$regulateqisupplementbyname = [];
foreach ($regulateqichaptersourcecategory['formulas'] as $formula) {
    $regulateqisupplementbyname[$formula['name']] = $formula;
}
$regulateqichapterformulas = [];
foreach ($regulateqichapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $regulateqisupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 12 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $regulateqichaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $regulateqichaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $regulateqichapterformulas[] = $formula;
}
$regulateqichapteroverview = [
    'number' => 'S8',
    'key' => 'regulate-qi-chapter-12',
    'name' => 'Formulas That Regulate the Qi · Complete Bensky Chapter 12',
    'nameEs' => 'Fórmulas que regulan el Qi · Capítulo 12 completo de Bensky',
    'sourceChapter' => 12,
    'sourcePages' => '505–554',
    'formulas' => $regulateqichapterformulas,
];

$regulatebloodchaptersections = [
    'invigorate' => [
        'key' => 'invigorate', 'name' => 'Invigorate the Blood and Dispel Blood Stasis',
        'nameEs' => 'Vigorizar la Sangre y dispersar la estasis', 'sourcePages' => '558–597',
        'copy' => 'Locate the stasis, then distinguish excess from deficiency and Heat from Cold before choosing how strongly to move, break, warm, or nourish the Blood.',
        'copyEs' => 'Localiza la estasis y luego distingue exceso de deficiencia y Calor de Frío antes de decidir con qué intensidad mover, romper, calentar o nutrir la Sangre.',
    ],
    'stop' => [
        'key' => 'stop', 'name' => 'Stop Bleeding',
        'nameEs' => 'Detener el sangrado', 'sourcePages' => '598–612',
        'copy' => 'Identify Heat, deficiency-Cold, stasis, organ location, and the direction of bleeding; acute or heavy bleeding always requires medical assessment.',
        'copyEs' => 'Identifica Calor, Frío-deficiencia, estasis, localización orgánica y dirección del sangrado; el sangrado agudo o abundante siempre requiere valoración médica.',
    ],
];
$regulatebloodchapterplan = [
    ['Tao He Cheng Qi Tang', 1, 'invigorate', 'Peach Pit Decoction to Order the Qi', '558–562'],
    ['Di Dang Tang', 2, 'invigorate', 'Appropriate Decoction', '562–563'],
    ['Xue Fu Zhu Yu Tang', 3, 'invigorate', 'Drive Out Stasis from the Mansion of Blood Decoction', '563–567'],
    ['Bu Yang Huan Wu Tang', 4, 'invigorate', 'Tonify the Yang to Restore Five-Tenths Decoction', '568–571'],
    ['Fu Yuan Huo Xue Tang', 5, 'invigorate', 'Revive Health by Invigorating the Blood Decoction', '571–573'],
    ['Qi Li San', 6, 'invigorate', 'Seven-Thousandths of a Tael Powder', '573–575'],
    ['Xuan Fu Hua Tang', 7, 'invigorate', 'Inula Decoction', '575–576'],
    ['Wen Jing Tang', 8, 'invigorate', 'Flow-Warming Decoction', '576–581'],
    ['Sheng Hua Tang', 9, 'invigorate', 'Generating and Transforming Decoction', '581–583'],
    ['Gui Zhi Fu Ling Wan', 10, 'invigorate', 'Cinnamon Twig and Poria Pill', '583–587'],
    ['Dang Gui Shao Yao San', 11, 'invigorate', 'Tang-kuei and Peony Powder', '587–590'],
    ['Shi Xiao San', 12, 'invigorate', 'Sudden Smile Powder', '591–592'],
    ['Huo Luo Xiao Ling Dan', 13, 'invigorate', 'Fantastically Effective Pill to Invigorate the Collaterals', '592–594'],
    ['Dan Shen Yin', 14, 'invigorate', 'Salvia Drink', '594'],
    ['Da Huang Zhe Chong Wan', 15, 'invigorate', 'Rhubarb and Ground Beetle Pill', '595–597'],
    ['Shi Hui San', 16, 'stop', 'Ten Partially-Charred Substances Powder', '598–600'],
    ['Si Sheng Wan', 17, 'stop', 'Four-Fresh Pill', '600–602'],
    ['Ke Xue Fang', 18, 'stop', 'Coughing of Blood Formula', '601–603'],
    ['Xiao Ji Yin Zi', 19, 'stop', 'Small Thistle Drink', '603–605'],
    ['Huai Hua San', 20, 'stop', 'Sophora Japonica Flower Powder', '605–607'],
    ['Bai Ye Tang', 21, 'stop', 'Arborvitae Twig Decoction', '607–608'],
    ['Huang Tu Tang', 22, 'stop', 'Yellow Earth Decoction', '608–610'],
    ['Jiao Ai Tang', 23, 'stop', 'Ass-Hide Gelatin and Mugwort Decoction', '610–612'],
];
$regulatebloodsupplementbyname = [];
foreach ($regulatebloodchaptersourcecategory['formulas'] as $formula) {
    $regulatebloodsupplementbyname[$formula['name']] = $formula;
}
$regulatebloodchapterformulas = [];
foreach ($regulatebloodchapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $regulatebloodsupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 13 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $regulatebloodchaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $regulatebloodchaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $regulatebloodchapterformulas[] = $formula;
}
$regulatebloodchapteroverview = [
    'number' => 'S9',
    'key' => 'regulate-blood-chapter-13',
    'name' => 'Formulas That Regulate the Blood · Complete Bensky Chapter 13',
    'nameEs' => 'Fórmulas que regulan la Sangre · Capítulo 13 completo de Bensky',
    'sourceChapter' => 13,
    'sourcePages' => '557–615',
    'formulas' => $regulatebloodchapterformulas,
];

$expelwindchaptersections = [
    'board' => [
        'key' => 'board', 'name' => 'Detailed core profiles first',
        'nameEs' => 'Fichas detalladas del núcleo primero', 'sourcePages' => '634–642, 647–649',
        'copy' => 'Begin with the three original detailed profiles. Official 2026 membership is shown independently by an asterisk; the remaining 14 cards then complete the Bensky chapter.',
        'copyEs' => 'Empieza con las tres fichas detalladas originales. La pertenencia oficial de 2026 se muestra de forma independiente con asterisco; las 14 fichas restantes completan después el capítulo de Bensky.',
    ],
    'external' => [
        'key' => 'external', 'name' => 'Additional Bensky formulas · Dredge and Disperse External Wind',
        'nameEs' => 'Fórmulas adicionales de Bensky · Desobstruir y dispersar Viento externo', 'sourcePages' => '619–643',
        'copy' => 'After the detailed core profiles, identify an external or channel-level Wind presentation, then distinguish Cold, Heat, Phlegm, Dampness, Blood deficiency, trauma, and toxic emergency patterns.',
        'copyEs' => 'Después de las fichas detalladas del núcleo, identifica un cuadro de Viento externo o de canales y distingue Frío, Calor, Flema, Humedad, deficiencia de Sangre, trauma y patrones tóxicos de urgencia.',
    ],
    'internal' => [
        'key' => 'internal', 'name' => 'Additional Bensky formulas · Pacify and Extinguish Internal Wind',
        'nameEs' => 'Fórmulas adicionales de Bensky · Pacificar y extinguir Viento interno', 'sourcePages' => '639–655',
        'copy' => 'Separate Heat excess generating Wind, Liver-Yang ascendance, and late-stage Blood-Yin-fluid depletion; the root determines whether to clear, anchor, direct downward, or deeply enrich.',
        'copyEs' => 'Separa Calor de exceso que genera Viento, ascenso de Yang de Hígado y agotamiento tardío de Sangre-Yin-líquidos; la raíz determina si aclarar, anclar, dirigir hacia abajo o enriquecer profundamente.',
    ],
];
$expelwindchaptersourcegroups = [
    'external' => [
        'name' => 'Dredge and Disperse External Wind',
        'nameEs' => 'Desobstruir y dispersar Viento externo',
    ],
    'internal' => [
        'name' => 'Pacify and Extinguish Internal Wind',
        'nameEs' => 'Pacificar y extinguir Viento interno',
    ],
];
$expelwindchapterplan = [
    ['Xiao Feng San', 1, 'board', 'Eliminate Wind Powder from Orthodox Lineage', '639–642', 'external'],
    ['Xiao Huo Luo Dan', 2, 'board', 'Minor Invigorate the Collaterals Special Pill', '634–636', 'external'],
    ['Tian Ma Gou Teng Yin', 3, 'board', 'Gastrodia and Uncaria Drink', '647–649', 'internal'],
    ['Xiao Xu Ming Tang', 4, 'external', 'Minor Extend Life Decoction', '619–622', 'external'],
    ['San Sheng Yin', 5, 'external', 'Drink from Three Unprepared Ingredients', '622–624', 'external'],
    ['Chuan Xiong Cha Tiao San', 6, 'external', 'Chuanxiong Powder to Be Taken with Green Tea', '624–628', 'external'],
    ['Cang Er Zi San', 7, 'external', 'Xanthium Powder', '628–630', 'external'],
    ['Da Qin Jiao Tang', 8, 'external', 'Major Large Gentian Decoction', '630–634', 'external'],
    ['Qian Zheng San', 9, 'external', 'Lead to Symmetry Powder', '636–637', 'external'],
    ['Zhi Jing San', 10, 'external', 'Stop Spasms Powder', '637–638', 'external'],
    ['Yu Zhen San', 11, 'external', 'True Jade Powder', '638–639', 'external'],
    ['Feng Yin Tang', 12, 'external', 'Wind-Drawing Decoction', '642–643', 'external'],
    ['Ling Jiao Gou Teng Tang', 13, 'internal', 'Antelope Horn and Uncaria Decoction', '639–644', 'internal'],
    ['Zhen Gan Xi Feng Tang', 14, 'internal', 'Sedate the Liver and Extinguish Wind Decoction', '644–647', 'internal'],
    ['San Jia Fu Mai Tang', 15, 'internal', 'Three-Shell Decoction to Restore the Pulse', '649–651', 'internal'],
    ['Da Ding Feng Zhu', 16, 'internal', 'Major Arrest Wind Pearls', '651–653', 'internal'],
    ['E Jiao Ji Zi Huang Tang', 17, 'internal', 'Ass-Hide Gelatin and Egg Yolk Decoction', '653–655', 'internal'],
];
$expelwindsupplementbyname = [];
foreach ($expelwindchaptersourcecategory['formulas'] as $formula) {
    $expelwindsupplementbyname[$formula['name']] = $formula;
}
$expelwindchapterformulas = [];
foreach ($expelwindchapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages, $sourcesectionkey]) {
    $formula = $expelwindsupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 14 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['chapterSourceSectionKey'] = $sourcesectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $expelwindchaptersourcegroups[$sourcesectionkey]['name'];
    $formula['groupEs'] = $expelwindchaptersourcegroups[$sourcesectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $expelwindchapterformulas[] = $formula;
}
$expelwindchapteroverview = [
    'number' => 'S10',
    'key' => 'expel-wind-chapter-14',
    'name' => 'Formulas That Expel Wind · Complete Bensky Chapter 14',
    'nameEs' => 'Fórmulas que expulsan el Viento · Capítulo 14 completo de Bensky',
    'sourceChapter' => 14,
    'sourcePages' => '619–657',
    'formulas' => $expelwindchapterformulas,
];

$expeldampchaptersections = [
    'transform-harmonize-middle' => [
        'key' => 'transform-harmonize-middle', 'name' => 'Transform Dampness and Harmonize the Stomach',
        'nameEs' => 'Transformar Humedad y armonizar el Estómago', 'sourcePages' => '685–699',
        'copy' => 'Restore the Middle Burner with aromatic, drying, and Qi-moving methods when Dampness obstructs transformation and descent.',
        'copyEs' => 'Restaura el Jiao Medio con métodos aromáticos, secantes y movilizadores de Qi cuando Humedad obstruye transformación y descenso.',
    ],
    'clear-damp-heat' => [
        'key' => 'clear-damp-heat', 'name' => 'Clear Heat and Expel Dampness',
        'nameEs' => 'Aclarar Calor y expulsar Humedad', 'sourcePages' => '699–724',
        'copy' => 'Match bitter-cold, bland-draining, and aromatic methods to the burner and to toxicity, jaundice, urinary pain, or painful obstruction.',
        'copyEs' => 'Ajusta métodos amargo-fríos, insípidos-drenantes y aromáticos al Jiao y a toxicidad, ictericia, dolor urinario u obstrucción dolorosa.',
    ],
    'promote-urination' => [
        'key' => 'promote-urination', 'name' => 'Promote Urination and Leach Out Dampness',
        'nameEs' => 'Promover la orina y filtrar Humedad', 'sourcePages' => '724–738',
        'copy' => 'Open the water pathways while distinguishing impaired Qi transformation, water-Heat, skin edema, and exterior Qi deficiency.',
        'copyEs' => 'Abre las vías del agua distinguiendo transformación alterada del Qi, agua-Calor, edema cutáneo y deficiencia de Qi Exterior.',
    ],
    'warm-transform-water' => [
        'key' => 'warm-transform-water', 'name' => 'Warm and Transform Water and Dampness',
        'nameEs' => 'Calentar y transformar Agua y Humedad', 'sourcePages' => '738–754',
        'copy' => 'Restore Spleen-Kidney Yang, move Qi, and provide an exit for cold water, thin mucus, or turbid Dampness.',
        'copyEs' => 'Restaura Yang de Bazo-Riñón, mueve Qi y da salida a agua fría, líquidos claros patológicos o Humedad turbia.',
    ],
    'dispel-wind-damp' => [
        'key' => 'dispel-wind-damp', 'name' => 'Dispel Wind-Dampness',
        'nameEs' => 'Dispersar Viento-Humedad', 'sourcePages' => '754–765',
        'copy' => 'Locate painful obstruction, grade Wind, Cold, Dampness, or Heat, and decide whether Qi, Blood, Liver, or Kidney deficiency must also be treated.',
        'copyEs' => 'Localiza la obstrucción dolorosa, gradúa Viento, Frío, Humedad o Calor y decide si también se trata deficiencia de Qi, Sangre, Hígado o Riñón.',
    ],
];
$expeldampchapterplan = [
    ['Ping Wei San', 1, 'transform-harmonize-middle', 'Calm the Stomach Powder', '685–689'],
    ['Bu Huan Jin Zheng Qi San', 2, 'transform-harmonize-middle', 'Rectify the Qi Powder Worth More than Gold', '689–691'],
    ['Huo Xiang Zheng Qi San', 3, 'transform-harmonize-middle', 'Patchouli/Agastache Powder to Rectify the Qi', '691–695'],
    ['Liu He Tang', 4, 'transform-harmonize-middle', 'Harmonize the Six Decoction', '695–697'],
    ['Ba Wei Dai Xia Fang', 5, 'transform-harmonize-middle', 'Eight-Ingredient Formula for Vaginal Discharge', '697–699'],
    ['San Ren Tang', 6, 'clear-damp-heat', 'Three-Seed Decoction', '699–702'],
    ['Gan Lu Xiao Du Dan', 7, 'clear-damp-heat', 'Sweet Dew Special Pill to Eliminate Toxin', '702–705'],
    ['Lian Po Yin', 8, 'clear-damp-heat', 'Coptis and Magnolia Bark Drink', '705–707'],
    ['Dang Gui Nian Tong Tang', 9, 'clear-damp-heat', 'Tangkuei Decoction to Pry Out Pain', '707–710'],
    ['Yin Chen Hao Tang', 10, 'clear-damp-heat', 'Virgate Wormwood Decoction', '710–713'],
    ['Ba Zheng San', 11, 'clear-damp-heat', 'Eight-Herb Powder for Rectification', '713–716'],
    ['Wu Lin San', 12, 'clear-damp-heat', 'Powder for Five Types of Painful Urinary Dribbling', '716–718'],
    ['Tong Guan Wan', 13, 'clear-damp-heat', 'Open the Gate Pill', '718–720'],
    ['Er Miao San', 14, 'clear-damp-heat', 'Two-Marvel Powder', '720–722'],
    ['Zhong Man Fen Xiao Wan', 15, 'clear-damp-heat', 'Separate and Reduce Fullness in the Middle Pill', '722–724'],
    ['Wu Ling San', 16, 'promote-urination', 'Five-Ingredient Powder with Poria', '724–729'],
    ['Zhu Ling Tang', 17, 'promote-urination', 'Polyporus Decoction', '729–731'],
    ['Wu Pi San', 18, 'promote-urination', 'Five-Peel Powder', '731–735'],
    ['Fang Ji Huang Qi Tang', 19, 'promote-urination', 'Stephania and Astragalus Decoction', '735–737'],
    ['Fang Ji Fu Ling Tang', 20, 'promote-urination', 'Stephania and Poria Decoction', '737–738'],
    ['Ling Gui Zhu Gan Tang', 21, 'warm-transform-water', 'Poria, Cinnamon Twig, Atractylodes, and Licorice Decoction', '738–742'],
    ['Gan Cao Gan Jiang Fu Ling Bai Zhu Tang', 22, 'warm-transform-water', 'Licorice, Ginger, Poria, and White Atractylodes Decoction', '742–743'],
    ['Zhen Wu Tang', 23, 'warm-transform-water', 'True Warrior Decoction', '744–747'],
    ['Fu Zi Tang', 24, 'warm-transform-water', 'Aconite Accessory Root Decoction', '747–749'],
    ['Shi Pi Yin', 25, 'warm-transform-water', 'Bolster the Spleen Drink', '749–751'],
    ['Bi Xie Fen Qing Yin', 26, 'warm-transform-water', 'Tokoro Drink to Separate the Clear', '751–753'],
    ['Ji Ming San', 27, 'warm-transform-water', "Powder to Take at Cock's Crow", '753–754'],
    ['Qiang Huo Sheng Shi Tang', 28, 'dispel-wind-damp', 'Notopterygium Decoction to Overcome Dampness', '755–756'],
    ['Juan Bi Tang', 29, 'dispel-wind-damp', 'Remove Painful Obstruction Decoction', '756–758'],
    ['Du Huo Ji Sheng Tang', 30, 'dispel-wind-damp', 'Pubescent Angelica and Taxillus Decoction', '758–760'],
    ['Gui Zhi Shao Yao Zhi Mu Tang', 31, 'dispel-wind-damp', 'Cinnamon Twig, Peony, and Anemarrhena Decoction', '760–762'],
    ['Xuan Bi Tang', 32, 'dispel-wind-damp', 'Disband Painful Obstruction Decoction', '762–763'],
    ['Shang Zhong Xia Tong Yong Tong Feng Wan', 33, 'dispel-wind-damp', 'Pill to Treat Painful Wind Anywhere', '763–765'],
];
$expeldampsupplementbyname = [];
foreach ($expeldampchaptersourcecategory['formulas'] as $formula) {
    $expeldampsupplementbyname[$formula['name']] = $formula;
}
$expeldampchapterformulas = [];
foreach ($expeldampchapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $expeldampsupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 16 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['chapterSourceSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $expeldampchaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $expeldampchaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $expeldampchapterformulas[] = $formula;
}
$expeldampchapteroverview = [
    'number' => 'S12',
    'key' => 'expel-damp-chapter-16',
    'name' => 'Formulas That Expel Dampness · Complete Bensky Chapter 16',
    'nameEs' => 'Fórmulas que expulsan la Humedad · Capítulo 16 completo de Bensky',
    'sourceChapter' => 16,
    'sourcePages' => '685–765',
    'formulas' => $expeldampchapterformulas,
];

$transformphlegmchaptersections = [
    'dry-damp-transform' => [
        'key' => 'dry-damp-transform', 'name' => 'Dry Dampness and Transform Phlegm',
        'nameEs' => 'Secar Humedad y transformar Flema', 'sourcePages' => '773–790',
        'copy' => 'Regulate Qi and restore fluid movement when Phlegm-Dampness obstructs the Lung, Middle Burner, channels, or sensory orifices.',
        'copyEs' => 'Regula Qi y restaura el movimiento de líquidos cuando Flema-Humedad obstruye Pulmón, Jiao Medio, canales u orificios sensoriales.',
    ],
    'clear-heat-transform' => [
        'key' => 'clear-heat-transform', 'name' => 'Clear Heat and Transform Phlegm',
        'nameEs' => 'Aclarar Calor y transformar Flema', 'sourcePages' => '790–798',
        'copy' => 'Clear the Heat that condenses fluids while moving Qi and directing Phlegm downward.',
        'copyEs' => 'Aclara el Calor que condensa líquidos mientras mueve Qi y dirige Flema hacia abajo.',
    ],
    'disperse-clumping' => [
        'key' => 'disperse-clumping', 'name' => 'Transform Phlegm and Disperse Clumping',
        'nameEs' => 'Transformar Flema y dispersar aglomeraciones', 'sourcePages' => '798–801',
        'copy' => 'Soften hardness and combine Phlegm treatment with Heat, Qi, or Blood regulation according to the nodule.',
        'copyEs' => 'Ablanda dureza y combina tratamiento de Flema con regulación de Calor, Qi o Sangre según el nódulo.',
    ],
    'moisten-dryness' => [
        'key' => 'moisten-dryness', 'name' => 'Moisten Dryness and Transform Phlegm',
        'nameEs' => 'Humedecer Sequedad y transformar Flema', 'sourcePages' => '801–805',
        'copy' => 'Moisten the Lung without cloying while clearing Heat and transforming deep, sticky dry Phlegm.',
        'copyEs' => 'Humedece Pulmón sin empalagar mientras aclara Calor y transforma Flema-Seca profunda y pegajosa.',
    ],
    'warm-phlegm-cold' => [
        'key' => 'warm-phlegm-cold', 'name' => 'Warm and Transform Phlegm-Cold',
        'nameEs' => 'Calentar y transformar Flema-Frío', 'sourcePages' => '806–810',
        'copy' => 'Warm and move cold thin mucus while distinguishing Yang deficiency from Phlegm with food and Qi stagnation.',
        'copyEs' => 'Calienta y moviliza líquidos claros fríos distinguiendo deficiencia de Yang de Flema con estancamiento de alimento y Qi.',
    ],
    'transform-extinguish-wind' => [
        'key' => 'transform-extinguish-wind', 'name' => 'Transform Phlegm and Extinguish Wind',
        'nameEs' => 'Transformar Flema y extinguir Viento', 'sourcePages' => '810–817',
        'copy' => 'Separate internal Wind-Phlegm vertigo or seizure from the gentle exterior-dispersing treatment of a residual cough.',
        'copyEs' => 'Separa vértigo o convulsión por Viento-Flema interno del tratamiento exterior suave de una tos residual.',
    ],
    'induce-vomiting' => [
        'key' => 'induce-vomiting', 'name' => 'Induce Vomiting',
        'nameEs' => 'Inducir vómito', 'sourcePages' => '817–818',
        'copy' => 'A historical emetic method retained for recognition and safety—not for self-treatment.',
        'copyEs' => 'Método emético histórico conservado para reconocimiento y seguridad, no para automedicación.',
    ],
];
$transformphlegmchapterplan = [
    ['Er Chen Tang', 1, 'dry-damp-transform', 'Two-Aged Herb Decoction', '773–779'],
    ['Dao Tan Tang', 2, 'dry-damp-transform', 'Guide Out Phlegm Decoction', '779–781'],
    ['Di Tan Tang', 3, 'dry-damp-transform', 'Scour Out Phlegm Decoction', '782–783'],
    ['Fu Ling Wan', 4, 'dry-damp-transform', 'Poria Pill', '783–786'],
    ['Wen Dan Tang', 5, 'dry-damp-transform', 'Warm Gallbladder Decoction', '786–789'],
    ['Xiang Fu Xuan Fu Hua Tang', 6, 'dry-damp-transform', 'Cyperus and Inula Decoction', '789–790'],
    ['Qing Qi Hua Tan Wan', 7, 'clear-heat-transform', 'Clear the Qi and Transform Phlegm Pill', '790–792'],
    ['Gun Tan Wan', 8, 'clear-heat-transform', 'Flushing Away Roiling Phlegm Pill', '795–798'],
    ['Xiao Luo Wan', 9, 'disperse-clumping', 'Reduce Scrofula Pill', '798–800'],
    ['Hai Zao Yu Hu Tang', 10, 'disperse-clumping', 'Sargassum Decoction for the Jade Flask', '800–801'],
    ['Bei Mu Gua Lou San', 11, 'moisten-dryness', 'Fritillaria and Trichosanthes Fruit Powder', '802–804'],
    ['Ling Gan Wu Wei Jiang Xin Tang', 12, 'warm-phlegm-cold', 'Poria, Licorice, Schisandra, Ginger, and Asarum Decoction', '806–808'],
    ['San Zi Yang Qin Tang', 13, 'warm-phlegm-cold', "Three-Seed Decoction to Nourish One's Parents", '808–810'],
    ['Ban Xia Bai Zhu Tian Ma Tang', 14, 'transform-extinguish-wind', 'Pinellia, White Atractylodes, and Gastrodia Decoction', '811–813'],
    ['Ding Xian Wan', 15, 'transform-extinguish-wind', 'Arrest Seizures Pill', '813–815'],
    ['Zhi Sou San', 16, 'transform-extinguish-wind', 'Stop Coughing Powder', '815–817'],
    ['Gua Di San', 17, 'induce-vomiting', 'Melon Pedicle Powder', '817–818'],
];
$transformphlegmsupplementbyname = [];
foreach ($transformphlegmchaptersourcecategory['formulas'] as $formula) {
    $transformphlegmsupplementbyname[$formula['name']] = $formula;
}
$transformphlegmchapterformulas = [];
foreach ($transformphlegmchapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $transformphlegmsupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 17 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['chapterSourceSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $transformphlegmchaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $transformphlegmchaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $transformphlegmchapterformulas[] = $formula;
}
$transformphlegmassociatedformula = $transformphlegmsupplementbyname['Xiao Xian Xiong Tang'] ?? null;
if ($transformphlegmassociatedformula === null) {
    throw new moodle_exception('Missing Chapter 17 associated formula profile: Xiao Xian Xiong Tang');
}
$transformphlegmassociatedformula['chapterRole'] = 'associated';
$transformphlegmassociatedformula['associatedWith'] = 'Qing Qi Hua Tan Wan';
$transformphlegmassociatedformula['chapterSectionKey'] = 'clear-heat-transform';
$transformphlegmassociatedformula['chapterSourceSectionKey'] = 'clear-heat-transform';
$transformphlegmassociatedformula['group'] = $transformphlegmchaptersections['clear-heat-transform']['name'];
$transformphlegmassociatedformula['groupEs'] = $transformphlegmchaptersections['clear-heat-transform']['nameEs'];
// Keep the complete associated profile visible directly after its parent formula,
// without counting it among the 17 principal contents entries.
array_splice($transformphlegmchapterformulas, 7, 0, [$transformphlegmassociatedformula]);
$transformphlegmchapteroverview = [
    'number' => 'S13',
    'key' => 'transform-phlegm-chapter-17',
    'name' => 'Formulas That Dispel Phlegm · Complete Bensky Chapter 17',
    'nameEs' => 'Fórmulas que eliminan la Flema · Capítulo 17 completo de Bensky',
    'sourceChapter' => 17,
    'sourcePages' => '773–821',
    'formulas' => $transformphlegmchapterformulas,
];

$digestivechaptersections = [
    'reduce-transform-food' => [
        'key' => 'reduce-transform-food', 'name' => 'Reduce Food Accumulation and Transform Stagnation',
        'nameEs' => 'Reducir acumulación de alimentos y transformar estancamiento', 'sourcePages' => '826–835',
        'copy' => 'Grade primarily excessive accumulation from mild acute food retention through Damp-Heat obstruction, forceful purging and pediatric parasites.',
        'copyEs' => 'Gradúa acumulación principalmente excesiva desde retención alimentaria aguda leve hasta obstrucción por Humedad-Calor, purgación fuerte y parásitos pediátricos.',
    ],
    'reduce-tonify-food' => [
        'key' => 'reduce-tonify-food', 'name' => 'Reduce Food Stagnation but Also Tonify',
        'nameEs' => 'Reducir estancamiento alimentario y también tonificar', 'sourcePages' => '835–842',
        'copy' => 'Support a deficient Spleen and Stomach while reducing food, Qi, Phlegm or alcohol-Damp accumulation according to the dominant mechanism.',
        'copyEs' => 'Apoya Bazo y Estómago deficientes mientras reduce acumulación de alimento, Qi, Flema o Humedad por alcohol según el mecanismo dominante.',
    ],
];
$digestivechapterplan = [
    ['Bao He Wan', 1, 'reduce-transform-food', 'Preserve Harmony Pill', '826–829'],
    ['Zhi Shi Dao Zhi Wan', 2, 'reduce-transform-food', 'Unripe Bitter Orange Pill to Guide Out Stagnation', '829–831'],
    ['Mu Xiang Bing Lang Wan', 3, 'reduce-transform-food', 'Aucklandia and Betel Nut Pill', '831–833'],
    ['Fei Er Wan', 4, 'reduce-transform-food', 'Fat Baby Pill', '833–835'],
    ['Jian Pi Wan', 5, 'reduce-tonify-food', 'Strengthen the Spleen Pill', '835–837'],
    ['Zhi Zhu Wan', 6, 'reduce-tonify-food', 'Unripe Bitter Orange and Atractylodes Pill', '837–839'],
    ['Zhi Shi Xiao Pi Wan', 7, 'reduce-tonify-food', 'Unripe Bitter Orange Pill to Reduce Focal Distention', '839–841'],
    ['Ge Hua Jie Cheng San', 8, 'reduce-tonify-food', 'Kudzu Flower Powder to Relieve Hangovers', '841–842'],
];
$digestivesupplementbyname = [];
foreach ($digestivechaptersourcecategory['formulas'] as $formula) {
    $digestivesupplementbyname[$formula['name']] = $formula;
}
$digestivechapterformulas = [];
foreach ($digestivechapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $digestivesupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 18 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['chapterSourceSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $digestivechaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $digestivechaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $digestivechapterformulas[] = $formula;
}
$digestivechapteroverview = [
    'number' => 'S14',
    'key' => 'digestive-chapter-18',
    'name' => 'Formulas That Reduce Food Stagnation · Complete Bensky Chapter 18',
    'nameEs' => 'Fórmulas que reducen el estancamiento alimentario · Capítulo 18 completo de Bensky',
    'sourceChapter' => 18,
    'sourcePages' => '823–843',
    'formulas' => $digestivechapterformulas,
];

$expelparasiteschaptersections = [
    'expel-intestinal-parasites' => [
        'key' => 'expel-intestinal-parasites', 'name' => 'Expel Intestinal Parasites',
        'nameEs' => 'Expulsar parásitos intestinales', 'sourcePages' => '846–855',
        'copy' => 'Compare harmonizing mixed Heat-Cold, direct historical attack, and attack-plus-tonification while prioritizing modern diagnosis and toxicity screening.',
        'copyEs' => 'Compara armonización de Calor-Frío mixto, ataque histórico directo y ataque más tonificación, priorizando diagnóstico moderno y evaluación de toxicidad.',
    ],
];
$expelparasiteschapterplan = [
    ['Wu Mei Wan', 1, 'expel-intestinal-parasites', 'Mume Pill', '846–852'],
    ['Hua Chong Wan', 2, 'expel-intestinal-parasites', 'Dissolve Parasites Pill', '853–854'],
    ['Bu Dai Wan', 3, 'expel-intestinal-parasites', 'Cloth Sack Pill', '854–855'],
];
$expelparasitessupplementbyname = [];
foreach ($expelparasiteschaptersourcecategory['formulas'] as $formula) {
    $expelparasitessupplementbyname[$formula['name']] = $formula;
}
$expelparasiteschapterformulas = [];
foreach ($expelparasiteschapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $expelparasitessupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 19 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['chapterSourceSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $expelparasiteschaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $expelparasiteschaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $expelparasiteschapterformulas[] = $formula;
}
$expelparasiteschapteroverview = [
    'number' => 'S15',
    'key' => 'expel-parasites-chapter-19',
    'name' => 'Formulas That Expel Parasites · Complete Bensky Chapter 19',
    'nameEs' => 'Fórmulas que expulsan parásitos · Capítulo 19 completo de Bensky',
    'sourceChapter' => 19,
    'sourcePages' => '846–855',
    'formulas' => $expelparasiteschapterformulas,
];

$treatabscesseschaptersections = [
    'external-abscesses-sores' => [
        'key' => 'external-abscesses-sores', 'name' => 'Treat External Abscesses and Sores',
        'nameEs' => 'Tratar abscesos y llagas externas', 'sourcePages' => '861–877',
        'copy' => 'Compare the lesion stage and the balance of Heat, Cold, pus, stasis and constitutional deficiency while preserving urgent infection and mass evaluation.',
        'copyEs' => 'Compara etapa de lesión y equilibrio de Calor, Frío, pus, estasis y deficiencia constitucional, manteniendo evaluación urgente de infección y masas.',
    ],
    'internal-abscesses' => [
        'key' => 'internal-abscesses', 'name' => 'Treat Internal Abscesses',
        'nameEs' => 'Tratar abscesos internos', 'sourcePages' => '878–890',
        'copy' => 'Separate Lung, intestinal, throat and deep toxic-swelling patterns, with emergency biomedical or surgical care taking priority whenever indicated.',
        'copyEs' => 'Separa patrones pulmonares, intestinales, faríngeos y de hinchazón tóxica profunda, dando prioridad a atención biomédica o quirúrgica urgente cuando corresponda.',
    ],
];
$treatabscesseschapterplan = [
    ['Xian Fang Huo Ming Yin', 1, 'external-abscesses-sores', "Immortals' Formula for Sustaining Life", '861–863'],
    ['Wu Wei Xiao Du Yin', 2, 'external-abscesses-sores', 'Five-Ingredient Drink to Eliminate Toxin', '863–865'],
    ['Si Miao Yong An Tang', 3, 'external-abscesses-sores', 'Four-Valiant Decoction for Well-Being', '865–867'],
    ['Tuo Li Xiao Du Yin', 4, 'external-abscesses-sores', 'Support the Interior and Eliminate Toxin Drink', '867–869'],
    ['Yang He Tang', 5, 'external-abscesses-sores', 'Balmy Yang Decoction', '869–872'],
    ['Shi Wei Bai Du San', 6, 'external-abscesses-sores', 'Ten-Ingredient Powder to Overcome Toxicity', '872–873'],
    ['Qian Jin Nei Tuo San', 7, 'external-abscesses-sores', 'Powder to Support the Interior Worth a Thousand Gold Pieces', '873–875'],
    ['San Zhong Kui Jian Tang', 8, 'external-abscesses-sores', 'Decoction to Disperse Swelling and Ulcerate What Is Hard', '875–877'],
    ['Wei Jing Tang', 9, 'internal-abscesses', 'Reed Decoction', '878–880'],
    ['Da Huang Mu Dan Tang', 10, 'internal-abscesses', 'Rhubarb and Moutan Decoction', '880–882'],
    ['Qing Chang Yin', 11, 'internal-abscesses', 'Clear the Intestines Drink', '882–884'],
    ['Yi Yi Fu Zi Bai Jiang San', 12, 'internal-abscesses', 'Coix, Aconite Accessory Root, and Patrinia Powder', '884–886'],
    ['Qing Xin Li Ge Tang', 13, 'internal-abscesses', 'Clear the Heart and Enable the Diaphragm Decoction', '886–888'],
    ['Shi Liu Wei Liu Qi Yin', 14, 'internal-abscesses', 'Sixteen-Ingredient Drink for Qi Flow', '888–890'],
];
$treatabscessessupplementbyname = [];
foreach ($treatabscesseschaptersourcecategory['formulas'] as $formula) {
    $treatabscessessupplementbyname[$formula['name']] = $formula;
}
$treatabscesseschapterformulas = [];
foreach ($treatabscesseschapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $treatabscessessupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 20 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['chapterSourceSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $treatabscesseschaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $treatabscesseschaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $treatabscesseschapterformulas[] = $formula;
}
$treatabscesseschapteroverview = [
    'number' => 'S16',
    'key' => 'treat-abscesses-chapter-20',
    'name' => 'Formulas That Treat Abscesses and Sores · Complete Bensky Chapter 20',
    'nameEs' => 'Fórmulas que tratan abscesos y llagas · Capítulo 20 completo de Bensky',
    'sourceChapter' => 20,
    'sourcePages' => '859–891',
    'formulas' => $treatabscesseschapterformulas,
];

$externalapplicationchaptersections = [
    'external-formulas-external-disorders' => [
        'key' => 'external-formulas-external-disorders', 'name' => 'External Formulas for External Disorders',
        'nameEs' => 'Fórmulas externas para trastornos externos', 'sourcePages' => '896–903',
        'copy' => 'Compare powders, plasters, ointments, washes and warming compresses by tissue integrity, moisture, temperature and lesion stage.',
        'copyEs' => 'Compara polvos, emplastos, ungüentos, lavados y compresas calientes según integridad del tejido, humedad, temperatura y etapa de lesión.',
    ],
    'external-formulas-internal-disorders' => [
        'key' => 'external-formulas-internal-disorders', 'name' => 'External Formulas for Internal Disorders',
        'nameEs' => 'Fórmulas externas para trastornos internos', 'sourcePages' => '904–905',
        'copy' => 'Separate external point applications, warming cakes, breast washes and a hazardous historical pregnancy plaster from definitive internal diagnosis and care.',
        'copyEs' => 'Separa aplicaciones externas en puntos, tortas calientes, lavados mamarios y un emplasto histórico peligroso del embarazo del diagnóstico y cuidado interno definitivos.',
    ],
];
$externalapplicationchapterplan = [
    ['Bing Peng San', 1, 'external-formulas-external-disorders', 'Borneol and Borax Powder', '896–897'],
    ['Jin Huang San / Jin Huang Gao', 2, 'external-formulas-external-disorders', 'Golden-Yellow Powder / Golden-Yellow Plaster', '897–898'],
    ['Zi Dang Gao', 3, 'external-formulas-external-disorders', 'Lithospermum/Arnebia and Tangkuei Ointment', '898–899'],
    ['Er Qing Gao', 4, 'external-formulas-external-disorders', 'Double-Dark Plaster', '899–900'],
    ['Qing Dai San', 5, 'external-formulas-external-disorders', 'Indigo Powder', '900–901'],
    ['Ku Shen Tang', 6, 'external-formulas-external-disorders', 'Sophora Root Wash', '901–902'],
    ['Hai Tong Pi Tang', 7, 'external-formulas-external-disorders', 'Erythrina Wash', '902–903'],
    ['Wu Zhu Yu Gao', 8, 'external-formulas-internal-disorders', 'Evodia Plaster', '904'],
    ['Bi Ba Bing', 9, 'external-formulas-internal-disorders', 'Long Pepper Cake', '904'],
    ['San Leng Xi Ru Fang', 10, 'external-formulas-internal-disorders', 'Sparganium Breast Wash', '904'],
    ['An Tai Zhu Gao', 11, 'external-formulas-internal-disorders', 'Fetus-Quieting Plaster', '904–905'],
];
$externalapplicationsupplementbyname = [];
foreach ($externalapplicationchaptersourcecategory['formulas'] as $formula) {
    $externalapplicationsupplementbyname[$formula['name']] = $formula;
}
$externalapplicationchapterformulas = [];
foreach ($externalapplicationchapterplan as [$name, $sequence, $sectionkey, $englishname, $sourcepages]) {
    $formula = $externalapplicationsupplementbyname[$name] ?? $getboardformula($name);
    if ($formula === null) {
        throw new moodle_exception('Missing Chapter 21 formula profile: ' . $name);
    }
    $formula['sequence'] = $sequence;
    $formula['chapterSectionKey'] = $sectionkey;
    $formula['chapterSourceSectionKey'] = $sectionkey;
    $formula['englishName'] = $englishname;
    $formula['group'] = $externalapplicationchaptersections[$sectionkey]['name'];
    $formula['groupEs'] = $externalapplicationchaptersections[$sectionkey]['nameEs'];
    $formula['sourcePages'] = $sourcepages;
    $externalapplicationchapterformulas[] = $formula;
}
$externalapplicationchapteroverview = [
    'number' => 'S17',
    'key' => 'external-application-chapter-21',
    'name' => 'Formulas for External Application · Complete Bensky Chapter 21',
    'nameEs' => 'Fórmulas para aplicación externa · Capítulo 21 completo de Bensky',
    'sourceChapter' => 21,
    'sourcePages' => '893–906',
    'formulas' => $externalapplicationchapterformulas,
];

$chapterinterestinginsights = [
    'dispel-summerheat' => [
        'title' => 'Interesting point: the original 63-profile sequence distributed Summerheat across broader study groups',
        'titleEs' => 'Dato interesante: la secuencia original de 63 fichas distribuyó Calor de Verano entre grupos de estudio más amplios',
        'stat' => '0 of 7 direct overlaps',
        'statEs' => '0 de 7 coincidencias directas',
        'intro' => 'The platform’s original sequence has no dedicated Dispel Summerheat category, yet several component mechanisms appear under Clear Heat, Expel Dampness, and Tonify. Official membership is checked separately against all 181 formulas.',
        'introEs' => 'La secuencia original de la plataforma no tiene una categoría propia de Dispersar Calor de Verano, aunque varios mecanismos aparecen en Aclarar Calor, Expulsar Humedad y Tonificar. La pertenencia oficial se comprueba por separado contra las 181 fórmulas.',
        'rows' => [
            ['coverage' => 'Strong Heat with thirst and fluid injury', 'coverageEs' => 'Calor intenso con sed y lesión de líquidos', 'boardNames' => ['Bai Hu Tang'], 'additional' => 'Seasonal Heat presentations differentiated by exterior signs, Dampness, and residual Heat.', 'additionalEs' => 'Cuadros estacionales de Calor diferenciados por signos de Exterior, Humedad y Calor residual.'],
            ['coverage' => 'Dampness with exterior and digestive symptoms', 'coverageEs' => 'Humedad con síntomas exteriores y digestivos', 'boardNames' => ['Huo Xiang Zheng Qi San'], 'additional' => 'Xiang Ru San, Liu Yi San, and related formulas grade the balance of Summerheat, Dampness, water, and the exterior.', 'additionalEs' => 'Xiang Ru San, Liu Yi San y fórmulas relacionadas gradúan el equilibrio entre Calor de Verano, Humedad, agua y Exterior.'],
            ['coverage' => 'Qi-Yin and fluid depletion after Heat', 'coverageEs' => 'Agotamiento de Qi-Yin y líquidos después del Calor', 'boardNames' => ['Sheng Mai San'], 'additional' => 'Qing Shu Yi Qi Tang and Wu Shi Lian Mei Tang add narrower recovery patterns after prolonged seasonal Heat.', 'additionalEs' => 'Qing Shu Yi Qi Tang y Wu Shi Lian Mei Tang añaden patrones de recuperación más específicos tras Calor estacional prolongado.'],
        ],
        'hypothesis' => 'This is an internal organizational choice, not evidence that NCBAHM excludes Summerheat formulas; the official 181-formula roster controls scope.',
        'hypothesisEs' => 'Esta es una decisión organizativa interna, no evidencia de que NCBAHM excluya fórmulas de Calor de Verano; el listado oficial de 181 controla el alcance.',
        'value' => 'The seven formulas still matter because season, climate, humidity, thirst, sweating, and digestive signs change the treatment strategy. Suspected heat stroke or severe dehydration requires emergency care.',
        'valueEs' => 'Las siete fórmulas siguen siendo importantes porque estación, clima, humedad, sed, sudoración y signos digestivos cambian la estrategia. Sospecha de golpe de calor o deshidratación grave requiere urgencias.',
    ],
    'warm-interior-cold' => [
        'title' => 'Interesting point: four formulas sample four clinically distinct depths of Interior Cold',
        'titleEs' => 'Dato interesante: cuatro fórmulas representan cuatro profundidades clínicas del Frío interno',
        'stat' => '4 of 11 in the original 63-profile sequence',
        'statEs' => '4 de 11 en la secuencia original de 63 fichas',
        'intro' => 'The selected formulas form a compact diagnostic ladder from Middle-Burner deficiency-Cold to counterflow, severe abdominal Cold, and collapse of Yang.',
        'introEs' => 'Las fórmulas seleccionadas forman una escala diagnóstica compacta: Frío-deficiencia del Jiao Medio, contracorriente, Frío abdominal intenso y colapso de Yang.',
        'rows' => [
            ['coverage' => 'Middle-Burner deficiency-Cold', 'coverageEs' => 'Frío-deficiencia del Jiao Medio', 'boardNames' => ['Li Zhong Wan'], 'additional' => 'Bensky adds gentler or more targeted warming strategies for pain, fluids, and Spleen weakness.', 'additionalEs' => 'Bensky añade estrategias de calentamiento más suaves o específicas para dolor, líquidos y debilidad de Bazo.'],
            ['coverage' => 'Cold counterflow and severe abdominal Cold', 'coverageEs' => 'Frío en contracorriente y Frío abdominal intenso', 'boardNames' => ['Wu Zhu Yu Tang', 'Da Jian Zhong Tang'], 'additional' => 'The broader chapter distinguishes channel Cold, Blood deficiency, sensory numbness, and different abdominal mechanisms.', 'additionalEs' => 'El capítulo amplio distingue Frío en canales, deficiencia de Sangre, entumecimiento sensorial y distintos mecanismos abdominales.'],
            ['coverage' => 'Devastated Yang and collapse', 'coverageEs' => 'Yang devastado y colapso', 'boardNames' => ['Si Ni Tang'], 'additional' => 'Hui Yang Jiu Ji Tang and Shen Fu Tang represent specialized rescue variations with major safety and acuity concerns.', 'additionalEs' => 'Hui Yang Jiu Ji Tang y Shen Fu Tang representan variantes especializadas de rescate con importantes consideraciones de seguridad y gravedad.'],
        ],
        'hypothesis' => 'The likely curricular logic is prototype compression: one memorable formula for each major depth or direction of Cold, while narrower channel, Blood, and rescue variations remain in Bensky.',
        'hypothesisEs' => 'La posible lógica curricular es una compresión por prototipos: una fórmula memorable para cada profundidad o dirección principal del Frío, mientras las variantes de canales, Sangre y rescate permanecen en Bensky.',
        'value' => 'The omitted formulas refine location and root; they are not interchangeable. Collapse, shock, severe abdominal pain, or formulas containing Fu Zi require urgent evaluation and specialist safety review.',
        'valueEs' => 'Las fórmulas omitidas refinan localización y raíz; no son intercambiables. Colapso, shock, dolor abdominal intenso o fórmulas con Fu Zi requieren valoración urgente y revisión especializada.',
    ],
    'exterior-interior-excess' => [
        'title' => 'Interesting point: the original 63-profile sequence decomposes mixed formulas into foundational strategies',
        'titleEs' => 'Dato interesante: la secuencia original de 63 fichas descompone fórmulas mixtas en estrategias fundamentales',
        'stat' => '0 of 6 direct overlaps',
        'statEs' => '0 de 6 coincidencias directas',
        'intro' => 'This chapter is built from simultaneous exterior and interior disease. The original 63-profile sequence teaches many component strategies, but did not group these six combined prescriptions as a separate category.',
        'introEs' => 'Este capítulo se construye sobre enfermedad simultánea de Exterior e Interior. La secuencia original de 63 fichas enseña muchas estrategias componentes, pero no agrupó estas seis prescripciones combinadas como categoría separada.',
        'rows' => [
            ['coverage' => 'Harmonize Shaoyang and purge Yangming', 'coverageEs' => 'Armonizar Shaoyang y purgar Yangming', 'boardNames' => ['Xiao Chai Hu Tang', 'Da Cheng Qi Tang'], 'additional' => 'Da Chai Hu Tang integrates both operations in one mixed excess presentation.', 'additionalEs' => 'Da Chai Hu Tang integra ambas operaciones en un solo cuadro mixto de exceso.'],
            ['coverage' => 'Release the exterior while clearing interior Heat', 'coverageEs' => 'Liberar Exterior mientras se aclara Calor interno', 'boardNames' => ['Ge Gen Tang', 'Huang Lian Jie Du Tang'], 'additional' => 'Ge Gen Huang Qin Huang Lian Tang and Fang Feng Tong Sheng San require more specific combinations of diarrhea, Heat, and exterior constraint.', 'additionalEs' => 'Ge Gen Huang Qin Huang Lian Tang y Fang Feng Tong Sheng San exigen combinaciones más específicas de diarrea, Calor y constricción exterior.'],
            ['coverage' => 'Treat exterior signs with interior Cold-Dampness or deficiency', 'coverageEs' => 'Tratar signos exteriores con Frío-Humedad o deficiencia interna', 'boardNames' => ['Huo Xiang Zheng Qi San', 'Li Zhong Wan'], 'additional' => 'Wu Ji San and Gui Zhi Ren Shen Tang combine warming, releasing, transforming, and tonifying in narrower mixed patterns.', 'additionalEs' => 'Wu Ji San y Gui Zhi Ren Shen Tang combinan calentar, liberar, transformar y tonificar en patrones mixtos más estrechos.'],
        ],
        'hypothesis' => 'This is a platform study design: it favors clear single-strategy anchors before mixed formulas. It is not an inference about the official 181-formula roster.',
        'hypothesisEs' => 'Este es un diseño de estudio de la plataforma: favorece anclas de una estrategia antes de las fórmulas mixtas. No es una inferencia sobre el listado oficial de 181 fórmulas.',
        'value' => 'Bensky’s six formulas remain valuable precisely because real patients can show two layers at once. Their complexity also increases the risk of treating the wrong layer or applying an overly strong method.',
        'valueEs' => 'Las seis fórmulas de Bensky siguen siendo valiosas precisamente porque un paciente real puede mostrar dos capas a la vez. Su complejidad también aumenta el riesgo de tratar la capa equivocada o aplicar un método demasiado fuerte.',
    ],
    'tonify-chapter-8' => [
        'title' => 'Interesting point: nine prototypes compress a forty-five-formula tonification chapter',
        'titleEs' => 'Dato interesante: nueve prototipos comprimen un capítulo de cuarenta y cinco fórmulas tónicas',
        'stat' => '9 of 45 across core profiles',
        'statEs' => '9 de 45 entre las fichas del núcleo',
        'intro' => 'Eight formulas appear under Tonify and Yu Ping Feng San appears under Stabilize and Bind. Together they sample the major deficient substances and several clinically important dynamics.',
        'introEs' => 'Ocho fórmulas aparecen en Tonificar y Yu Ping Feng San aparece en Estabilizar y contener. Juntas representan las principales sustancias deficientes y varias dinámicas clínicas importantes.',
        'rows' => [
            ['coverage' => 'Foundational Qi, Blood, and combined deficiency', 'coverageEs' => 'Deficiencia básica de Qi, Sangre y combinación de ambas', 'boardNames' => ['Si Jun Zi Tang', 'Si Wu Tang', 'Gui Pi Tang', 'Shi Quan Da Bu Tang'], 'additional' => 'Bensky adds organ-specific, obstetric, postpartum, Cold, stasis, and pain refinements.', 'additionalEs' => 'Bensky añade refinamientos por órgano, obstetricia, posparto, Frío, estasis y dolor.'],
            ['coverage' => 'Sinking Qi, Qi-Yin fluid loss, and an unstable exterior', 'coverageEs' => 'Hundimiento de Qi, pérdida de líquidos Qi-Yin y Exterior inestable', 'boardNames' => ['Bu Zhong Yi Qi Tang', 'Sheng Mai San', 'Yu Ping Feng San'], 'additional' => 'The remaining formulas distinguish Lung failure, failure to grasp Qi, Dampness, prolapse, sweating, and deeper exhaustion.', 'additionalEs' => 'Las fórmulas restantes distinguen falla de Pulmón, incapacidad para captar Qi, Humedad, prolapso, sudoración y agotamiento más profundo.'],
            ['coverage' => 'Liver-Kidney Yin deficiency and Kidney Yang deficiency', 'coverageEs' => 'Deficiencia de Yin de Hígado-Riñón y deficiencia de Yang de Riñón', 'boardNames' => ['Liu Wei Di Huang Wan', 'Jin Gui Shen Qi Wan'], 'additional' => 'Bensky expands into deficiency Fire, Lung or Stomach Yin, essence, fertility, pure Yang depletion, and combined Yin-Yang patterns.', 'additionalEs' => 'Bensky amplía hacia Fuego por deficiencia, Yin de Pulmón o Estómago, Esencia, fertilidad, agotamiento puro de Yang y patrones combinados Yin-Yang.'],
        ],
        'hypothesis' => 'The likely exam strategy is to test archetypal deficiency patterns and transformation rules; the other thirty-six formulas are mostly finer branches built from those foundations.',
        'hypothesisEs' => 'La probable estrategia de examen es evaluar patrones arquetípicos de deficiencia y reglas de transformación; las otras treinta y seis fórmulas son, en gran parte, ramas más finas construidas sobre esas bases.',
        'value' => 'Those branches matter clinically because the same word—“deficiency”—can hide different organs, temperatures, fluids, reproductive contexts, and secondary pathogens.',
        'valueEs' => 'Esas ramas importan clínicamente porque una misma palabra—«deficiencia»—puede ocultar órganos, temperaturas, líquidos, contextos reproductivos y patógenos secundarios distintos.',
    ],
    'stabilize-bind-chapter-9' => [
        'title' => 'Interesting point: the original detailed group samples three kinds of leakage, but one lives in another Bensky chapter',
        'titleEs' => 'Dato interesante: el grupo detallado original representa tres tipos de pérdida, pero una fórmula está en otro capítulo de Bensky',
        'stat' => '2 of 17 overlap here; 3 in the core profile',
        'statEs' => '2 de 17 coinciden aquí; 3 en el núcleo detallado',
        'intro' => 'Si Shen Wan and Gu Jing Wan overlap directly with Chapter 9. Yu Ping Feng San is the third internal Stabilize and Bind profile and also belongs to official Appendix C, but Bensky places it in Chapter 8 because it tonifies defensive Qi while securing the exterior.',
        'introEs' => 'Si Shen Wan y Gu Jing Wan coinciden directamente con el capítulo 9. Yu Ping Feng San es la tercera ficha interna de Estabilizar y contener y también pertenece al Appendix C oficial, pero Bensky la coloca en el capítulo 8 porque tonifica Qi defensivo mientras asegura el Exterior.',
        'rows' => [
            ['coverage' => 'Unstable exterior with spontaneous sweating and recurrent invasion', 'coverageEs' => 'Exterior inestable con sudor espontáneo e invasiones repetidas', 'boardNames' => ['Yu Ping Feng San'], 'additional' => 'Mu Li San offers another sweating pattern, while Chapter 9 also separates Lung leakage and late-stage cough.', 'additionalEs' => 'Mu Li San ofrece otro patrón de sudoración, mientras el capítulo 9 también separa pérdida de Pulmón y tos tardía.'],
            ['coverage' => 'Chronic intestinal leakage from Spleen-Kidney Yang deficiency', 'coverageEs' => 'Pérdida intestinal crónica por deficiencia de Yang de Bazo-Riñón', 'boardNames' => ['Si Shen Wan'], 'additional' => 'Bensky distinguishes deficiency-Cold diarrhea, Damp-Heat residue, prolapse, and different levels of astringency.', 'additionalEs' => 'Bensky distingue diarrea por Frío-deficiencia, residuo de Humedad-Calor, prolapso y distintos grados de astringencia.'],
            ['coverage' => 'Persistent uterine bleeding with deficiency and constrained Heat', 'coverageEs' => 'Sangrado uterino persistente con deficiencia y Calor constreñido', 'boardNames' => ['Gu Jing Wan'], 'additional' => 'The chapter adds essence, urinary, pregnancy, vaginal discharge, and other gynecologic leakage patterns.', 'additionalEs' => 'El capítulo añade patrones de pérdida de Esencia, urinarios, embarazo, flujo vaginal y otras pérdidas ginecológicas.'],
        ],
        'hypothesis' => 'The original platform sequence sampled three high-yield leakage territories—exterior, intestines, and uterus—while the official 181-formula roster is evaluated independently.',
        'hypothesisEs' => 'La secuencia original de la plataforma representó tres territorios de pérdida de alto rendimiento—Exterior, Intestinos y útero—mientras el listado oficial de 181 se evalúa de forma independiente.',
        'value' => 'The omitted formulas teach an essential safety rule: never bind an unresolved pathogen or excess. Color, odor, temperature, timing, and the presence of Heat or Dampness change whether astringency is appropriate.',
        'valueEs' => 'Las fórmulas omitidas enseñan una regla esencial de seguridad: nunca contener un patógeno o exceso no resuelto. Color, olor, temperatura, momento y presencia de Calor o Humedad cambian si la astringencia es apropiada.',
    ],
    'calm-shen-chapter-10' => [
        'examFocus' => true,
        'title' => 'Exam review: distinguish three high-yield formulas that calm Shen',
        'titleEs' => 'Repaso para el examen: diferencia tres fórmulas clave que calman el Shen',
        'stat' => '3 high-yield formulas',
        'statEs' => '3 fórmulas de alto rendimiento',
        'intro' => 'Do not choose from insomnia alone. Identify the deficient substance and then match the differentiating symptoms: Liver Blood with Heart Yin, Heart-Kidney Yin and Blood, or the emotional pattern of Zang Zao.',
        'introEs' => 'No elijas una fórmula solo por el insomnio. Identifica primero la sustancia deficiente y después relaciona los síntomas clave: Sangre de Hígado con Yin de Corazón, Yin y Sangre de Corazón-Riñón, o el patrón emocional Zang Zao.',
        'rows' => [
            ['coverage' => 'Liver Blood deficiency with Heart Yin deficiency and Empty Heat disturbing Shen', 'coverageEs' => 'Deficiencia de Sangre de Hígado con Yin de Corazón deficiente y Calor Vacío perturbando el Shen', 'boardNames' => ['Suan Zao Ren Tang'], 'additional' => 'Insomnia with irritability, palpitations, night sweating, dizziness, and a fine wiry pulse.', 'additionalEs' => 'Insomnio con irritabilidad, palpitaciones, sudor nocturno, mareo y pulso fino cuerda.'],
            ['coverage' => 'Heart-Kidney Yin and Blood deficiency with deficient Fire disturbing Shen', 'coverageEs' => 'Deficiencia de Yin y Sangre de Corazón-Riñón con Fuego por deficiencia perturbando el Shen', 'boardNames' => ['Tian Wang Bu Xin Dan'], 'additional' => 'Restless insomnia, palpitations, night sweats, dryness, and a red tongue with little coat.', 'additionalEs' => 'Insomnio inquieto, palpitaciones, sudor nocturno, sequedad y lengua roja con poca capa.'],
            ['coverage' => 'Heart nourishment or Yin deficiency with Liver constraint causing Zang Zao', 'coverageEs' => 'Desnutrición o deficiencia de Yin de Corazón con constricción de Hígado que causa Zang Zao', 'boardNames' => ['Gan Mai Da Zao Tang'], 'additional' => 'Frequent crying, emotional lability, restlessness, and yawning.', 'additionalEs' => 'Llanto frecuente, labilidad emocional, inquietud y bostezos.'],
        ],
        'hypothesis' => 'Irritable insomnia with night sweating and a fine wiry pulse points to Suan Zao Ren Tang. Marked dryness with a red, scantily coated tongue points to Tian Wang Bu Xin Dan. Crying, emotional lability, restlessness, and yawning point to Gan Mai Da Zao Tang.',
        'hypothesisEs' => 'Insomnio irritable con sudor nocturno y pulso fino cuerda orienta a Suan Zao Ren Tang. Sequedad marcada con lengua roja y poca capa orienta a Tian Wang Bu Xin Dan. Llanto, labilidad emocional, inquietud y bostezos orientan a Gan Mai Da Zao Tang.',
        'value' => 'Do not answer from the shared symptom of insomnia. The tested distinction is the complete pattern and its differentiating signs.',
        'valueEs' => 'No respondas solo por el síntoma compartido de insomnio. La distinción examinable es el patrón completo y sus signos clave.',
    ],
    'open-orifices-chapter-11' => [
        'title' => 'Interesting point: the original 63-profile sequence omitted this entire classical family',
        'titleEs' => 'Dato interesante: la secuencia original de 63 fichas omitió esta familia clásica completa',
        'stat' => '0 of 7 in the original detailed sequence',
        'statEs' => '0 de 7 en la secuencia detallada original',
        'intro' => 'The seven formulas address hot or cold sensory-orifice closure with impaired consciousness—presentations that overlap modern neurologic, infectious, toxicologic, or cardiopulmonary emergencies.',
        'introEs' => 'Las siete fórmulas tratan cierre caliente o frío de los orificios sensoriales con alteración de conciencia, cuadros que se superponen con urgencias neurológicas, infecciosas, toxicológicas o cardiopulmonares modernas.',
        'rows' => [
            ['coverage' => 'Original detailed-profile sequence', 'coverageEs' => 'Secuencia original de fichas detalladas', 'boardNames' => [], 'additional' => 'Bensky preserves hot closure, cold closure, pediatric convulsion, Phlegm, internal Wind, and historical revival strategies.', 'additionalEs' => 'Bensky conserva cierre caliente, cierre frío, convulsión pediátrica, Flema, Viento interno y estrategias históricas de reanimación.'],
            ['coverage' => 'Modern emergency and safety competence', 'coverageEs' => 'Competencia moderna de urgencia y seguridad', 'boardNames' => [], 'additional' => 'The chapter requires recognizing protected species, animal-derived substances, metals, variable substitutions, and a hazardous nasal administration route.', 'additionalEs' => 'El capítulo exige reconocer especies protegidas, sustancias animales, metales, sustituciones variables y una vía nasal peligrosa.'],
        ],
        'hypothesis' => 'The internal omission should not be mistaken for an official NCBAHM exclusion. Emergency acuity, obsolete or restricted ingredients, substitution variability, and standardization problems still help explain why these formulas require special contextual treatment.',
        'hypothesisEs' => 'La omisión interna no debe confundirse con una exclusión oficial de NCBAHM. La urgencia, los ingredientes obsoletos o restringidos, la variabilidad de sustituciones y los problemas de estandarización sí ayudan a explicar por qué estas fórmulas requieren un tratamiento contextual especial.',
        'value' => 'Its educational value is safety recognition and differential diagnosis. Altered consciousness, seizure, collapse, severe fever, or respiratory compromise requires emergency services, not self-treatment with a classical formula.',
        'valueEs' => 'Su valor educativo está en reconocer seguridad y diagnóstico diferencial. Alteración de conciencia, convulsión, colapso, fiebre grave o compromiso respiratorio requieren urgencias, no automedicación con una fórmula clásica.',
    ],
    'regulate-qi-chapter-12' => [
        'title' => 'Interesting point: five formulas map the two master directions of Qi pathology',
        'titleEs' => 'Dato interesante: cinco fórmulas cartografían las dos direcciones maestras de la patología del Qi',
        'stat' => '5 of 20 in the original 63-profile sequence',
        'statEs' => '5 de 20 en la secuencia original de 63 fichas',
        'intro' => 'The original detailed sample includes Qi that is stuck and Qi that rebels upward, then distributes those dynamics across general constraint, throat, Lung, and Stomach presentations.',
        'introEs' => 'La muestra detallada original incluye Qi estancado y Qi que asciende en contracorriente, y distribuye esas dinámicas entre cuadros generales, garganta, Pulmón y Estómago.',
        'rows' => [
            ['coverage' => 'General constraint and Phlegm-Qi in the throat', 'coverageEs' => 'Estancamiento general y Flema-Qi en garganta', 'boardNames' => ['Yue Ju Wan', 'Ban Xia Hou Po Tang'], 'additional' => 'Bensky adds Liver-channel pain, chest obstruction, Middle-Burner Cold, hernia, menstrual, and food-related variants.', 'additionalEs' => 'Bensky añade variantes de dolor del canal de Hígado, obstrucción torácica, Frío del Jiao Medio, hernia, menstruación y alimento.'],
            ['coverage' => 'Rebellious Lung Qi with Cold-Phlegm or Phlegm-Heat', 'coverageEs' => 'Qi rebelde de Pulmón con Flema-Frío o Flema-Calor', 'boardNames' => ['Su Zi Jiang Qi Tang', 'Ding Chuan Tang'], 'additional' => 'The wider chapter grades deficiency below, exterior constraint, sputum quality, wheezing, and the strength of descent.', 'additionalEs' => 'El capítulo amplio gradúa deficiencia abajo, constricción exterior, calidad del esputo, sibilancia y fuerza del descenso.'],
            ['coverage' => 'Rebellious Stomach Qi from deficiency-Heat', 'coverageEs' => 'Qi rebelde de Estómago por Calor-deficiencia', 'boardNames' => ['Ju Pi Zhu Ru Tang'], 'additional' => 'Xuan Fu Dai Zhe Tang, Ding Xiang Shi Di Tang, and Da Ban Xia Tang refine retching, hiccup, vomiting, Phlegm, Cold, and deficiency.', 'additionalEs' => 'Xuan Fu Dai Zhe Tang, Ding Xiang Shi Di Tang y Da Ban Xia Tang refinan arcadas, hipo, vómito, Flema, Frío y deficiencia.'],
        ],
        'hypothesis' => 'The probable curricular compression is directional: master movement versus descent, then use five high-yield organ examples; the other fifteen formulas are narrower combinations of location, temperature, Phlegm, food, Blood, and deficiency.',
        'hypothesisEs' => 'La probable compresión curricular es direccional: dominar movimiento frente a descenso y usar cinco ejemplos orgánicos de alto rendimiento; las otras quince fórmulas son combinaciones más estrechas de localización, temperatura, Flema, alimento, Sangre y deficiencia.',
        'value' => 'The omitted formulas matter because the same symptom—pain, cough, hiccup, or vomiting—can require opposite treatment depending on direction, timing, temperature, and root.',
        'valueEs' => 'Las fórmulas omitidas importan porque un mismo síntoma—dolor, tos, hipo o vómito—puede requerir tratamiento opuesto según dirección, momento, temperatura y raíz.',
    ],
    'regulate-blood-chapter-13' => [
        'title' => 'Interesting point: the original 63-profile sequence sampled Blood stasis more deeply than stop-bleeding formulas',
        'titleEs' => 'Dato interesante: la secuencia original de 63 fichas desarrolló más estasis de Sangre que fórmulas hemostáticas',
        'stat' => '4 of 23 in the original 63-profile sequence',
        'statEs' => '4 de 23 en la secuencia original de 63 fichas',
        'intro' => 'All four original deep profiles come from the section that invigorates Blood and dispels stasis. The asterisk now checks every chapter formula independently against all 181 official entries.',
        'introEs' => 'Las cuatro fichas profundas originales provienen de la sección que vigoriza Sangre y dispersa estasis. El asterisco ahora comprueba cada fórmula del capítulo de forma independiente contra las 181 entradas oficiales.',
        'rows' => [
            ['coverage' => 'Heat-stasis in the Lower Burner and Qi-stasis in the chest', 'coverageEs' => 'Calor-estasis en Jiao Inferior y Qi-estasis en pecho', 'boardNames' => ['Tao He Cheng Qi Tang', 'Xue Fu Zhu Yu Tang'], 'additional' => 'Bensky adds trauma, postpartum stasis, severe fixed pain, chronic masses, collaterals, and stronger breaking methods.', 'additionalEs' => 'Bensky añade trauma, estasis posparto, dolor fijo intenso, masas crónicas, colaterales y métodos de ruptura más fuertes.'],
            ['coverage' => 'Gynecologic Cold-stasis and uterine masses', 'coverageEs' => 'Frío-estasis ginecológico y masas uterinas', 'boardNames' => ['Wen Jing Tang', 'Gui Zhi Fu Ling Wan'], 'additional' => 'The broader chapter distinguishes deficiency, pregnancy, postpartum recovery, menstrual pain, and different degrees of Blood nourishment.', 'additionalEs' => 'El capítulo amplio distingue deficiencia, embarazo, recuperación posparto, dolor menstrual y distintos grados de nutrición de Sangre.'],
            ['coverage' => 'Stop bleeding by location and mechanism', 'coverageEs' => 'Detener sangrado según localización y mecanismo', 'boardNames' => [], 'additional' => 'Eight formulas separate Heat, deficiency-Cold, stasis, Lung, Intestine, Bladder, uterus, and Middle-Burner bleeding.', 'additionalEs' => 'Ocho fórmulas separan Calor, Frío-deficiencia, estasis y sangrado de Pulmón, Intestino, Vejiga, útero y Jiao Medio.'],
        ],
        'hypothesis' => 'One plausible reason is that four stasis archetypes provide reusable pattern logic, whereas hemostatic prescriptions are highly location- and cause-specific and often overlap situations that require urgent biomedical evaluation.',
        'hypothesisEs' => 'Una razón plausible es que cuatro arquetipos de estasis ofrecen lógica de patrón reutilizable, mientras las prescripciones hemostáticas dependen mucho de localización y causa y suelen superponerse con situaciones que exigen valoración biomédica urgente.',
        'value' => 'The nineteen additional formulas are clinically important for avoiding a dangerous shortcut: moving Blood can worsen active bleeding, while stopping bleeding without addressing stasis can retain the cause. Acute or heavy bleeding is an emergency.',
        'valueEs' => 'Las diecinueve fórmulas adicionales son importantes para evitar un atajo peligroso: mover Sangre puede empeorar sangrado activo, mientras detenerlo sin tratar estasis puede retener la causa. Sangrado agudo o abundante es una urgencia.',
    ],
    'expel-damp-chapter-16' => [
        'title' => 'Interesting point: eight detailed core profiles preserve one archetype from every major Dampness route',
        'titleEs' => 'Dato interesante: ocho fichas detalladas conservan un arquetipo de cada gran ruta de Humedad',
        'stat' => '8 of 33 in the original 63-profile sequence',
        'statEs' => '8 de 33 en la secuencia original de 63 fichas',
        'intro' => 'The original detailed sample reaches all five sections, but compresses 25 additional Bensky profiles into eight high-yield anchors for the Middle Burner, Damp-Heat, water pathways, Yang deficiency, and painful obstruction.',
        'introEs' => 'La muestra detallada original alcanza las cinco secciones, pero comprime 25 perfiles adicionales de Bensky en ocho anclas de alto rendimiento para Jiao Medio, Humedad-Calor, vías del agua, deficiencia de Yang y obstrucción dolorosa.',
        'rows' => [
            ['coverage' => 'Transform the Middle and clear Damp-Heat', 'coverageEs' => 'Transformar el Centro y aclarar Humedad-Calor', 'boardNames' => ['Ping Wei San', 'Huo Xiang Zheng Qi San', 'Ba Zheng San'], 'additional' => 'The full chapter adds Summerheat, epidemic toxin, jaundice, vaginal discharge, severe fullness, and several urinary or joint variants.', 'additionalEs' => 'El capítulo completo añade Calor de Verano, toxicidad epidémica, ictericia, flujo vaginal, plenitud intensa y varias variantes urinarias o articulares.'],
            ['coverage' => 'Open and regulate the water pathways', 'coverageEs' => 'Abrir y regular las vías del agua', 'boardNames' => ['Wu Ling San', 'Zhu Ling Tang', 'Wu Pi San'], 'additional' => 'Fang Ji Huang Qi Tang and Fang Ji Fu Ling Tang refine exterior deficiency, skin edema, and constrained Yang-Qi.', 'additionalEs' => 'Fang Ji Huang Qi Tang y Fang Ji Fu Ling Tang refinan deficiencia Exterior, edema cutáneo y Yang-Qi constreñido.'],
            ['coverage' => 'Warm water metabolism and treat painful obstruction', 'coverageEs' => 'Calentar metabolismo del agua y tratar obstrucción dolorosa', 'boardNames' => ['Zhen Wu Tang', 'Du Huo Ji Sheng Tang'], 'additional' => 'The remaining formulas distinguish thin mucus, fixed Kidney disorder, yin-type edema, turbid urine, leg qi, upper-body Wind-Dampness, Damp-Heat, and migratory joint pain.', 'additionalEs' => 'Las restantes distinguen líquidos claros patológicos, trastorno renal fijo, edema tipo Yin, orina turbia, qi de pierna, Viento-Humedad superior, Humedad-Calor y dolor articular migratorio.'],
        ],
        'hypothesis' => 'The probable curricular logic is broad-route compression: retain formulas that expose reusable water, Heat, Yang, Qi, and channel mechanisms, while leaving narrower location and combination patterns in the source chapter.',
        'hypothesisEs' => 'La probable lógica curricular es comprimir por grandes rutas: conservar fórmulas que muestran mecanismos reutilizables de agua, Calor, Yang, Qi y canales, dejando en la fuente patrones más estrechos por localización y combinación.',
        'value' => 'The other 25 profiles matter because “Dampness” does not prescribe a single action. The decisive choice may be to aromatically transform, clear Heat, promote urination, warm Yang, move Qi, nourish deficiency, or unblock channels.',
        'valueEs' => 'Los otros 25 perfiles importan porque “Humedad” no prescribe una sola acción. La decisión puede ser transformar aromáticamente, aclarar Calor, promover orina, calentar Yang, mover Qi, nutrir deficiencia o desobstruir canales.',
    ],
    'digestive-chapter-18' => [
        'title' => 'Interesting point: one original core profile anchors an eight-formula excess-to-deficiency continuum',
        'titleEs' => 'Dato interesante: una ficha original del núcleo ancla un continuo de ocho fórmulas entre exceso y deficiencia',
        'stat' => '1 of 8 in the original 63-profile sequence',
        'statEs' => '1 de 8 en la secuencia original de 63 fichas',
        'intro' => 'Bao He Wan provides the mild, high-yield archetype for acute food stagnation. The seven supplemental formulas show how the strategy changes when accumulation becomes Damp-Heat, requires purging, involves parasites, rests on Spleen deficiency, creates focal clumping, or follows alcohol excess.',
        'introEs' => 'Bao He Wan ofrece el arquetipo suave y de alto rendimiento para estancamiento alimentario agudo. Las siete fórmulas suplementarias muestran cómo cambia la estrategia cuando la acumulación se vuelve Humedad-Calor, exige purgación, implica parásitos, descansa sobre deficiencia de Bazo, forma aglomeración focal o sigue al exceso de alcohol.',
        'rows' => [
            ['coverage' => 'Acute uncomplicated food stagnation', 'coverageEs' => 'Estancamiento alimentario agudo no complicado', 'boardNames' => ['Bao He Wan'], 'additional' => 'The core profile combines food reduction with Qi regulation and Phlegm transformation without strong purging or tonification.', 'additionalEs' => 'La ficha del núcleo combina reducción de alimento con regulación de Qi y transformación de Flema sin purgación fuerte ni tonificación.'],
            ['coverage' => 'Damp-Heat, constipation, dysentery or parasites', 'coverageEs' => 'Humedad-Calor, estreñimiento, disentería o parásitos', 'boardNames' => [], 'additional' => 'Zhi Shi Dao Zhi Wan, Mu Xiang Bing Lang Wan and Fei Er Wan require much stronger excess confirmation and modern safety screening.', 'additionalEs' => 'Zhi Shi Dao Zhi Wan, Mu Xiang Bing Lang Wan y Fei Er Wan exigen confirmar exceso con mucha mayor claridad y revisión moderna de seguridad.'],
            ['coverage' => 'Spleen deficiency, focal fullness and alcohol-Dampness', 'coverageEs' => 'Deficiencia de Bazo, plenitud focal y Humedad por alcohol', 'boardNames' => [], 'additional' => 'Jian Pi Wan, Zhi Zhu Wan, Zhi Shi Xiao Pi Wan and Ge Hua Jie Cheng San grade how much tonification, movement, Heat treatment and water regulation are needed.', 'additionalEs' => 'Jian Pi Wan, Zhi Zhu Wan, Zhi Shi Xiao Pi Wan y Ge Hua Jie Cheng San gradúan cuánta tonificación, movilización, aclaramiento de Calor y regulación de agua se necesita.'],
        ],
        'hypothesis' => 'A plausible curricular logic is that Bao He Wan captures the reusable core of acute food stagnation, while the omitted formulas depend on narrower mixed patterns, stronger purgatives, pediatric parasite care or alcohol-related safety contexts.',
        'hypothesisEs' => 'Una lógica curricular plausible es que Bao He Wan captura el núcleo reutilizable del estancamiento alimentario agudo, mientras las fórmulas omitidas dependen de patrones mixtos más estrechos, purgantes fuertes, atención parasitaria pediátrica o contextos de seguridad relacionados con alcohol.',
        'value' => 'The chapter prevents a dangerous shortcut: not every distended abdomen is simple food retention, and reduction can worsen deficiency. Pressure response, stool, urine, tongue, pulse, age and exposure history determine both treatment and urgency.',
        'valueEs' => 'El capítulo evita un atajo peligroso: no todo abdomen distendido es simple retención alimentaria y reducir puede empeorar deficiencia. Respuesta a presión, heces, orina, lengua, pulso, edad e historia de exposición determinan tratamiento y urgencia.',
    ],
    'expel-parasites-chapter-19' => [
        'title' => 'Interesting point: the Board omits all three parasite formulas while modern care has become organism-specific',
        'titleEs' => 'Dato interesante: el Board omite las tres fórmulas antiparasitarias mientras la atención moderna es específica para cada organismo',
        'stat' => '0 of 3 direct overlaps',
        'statEs' => '0 de 3 coincidencias directas',
        'intro' => 'The chapter preserves three very different historical strategies: complex harmonization with Wu Mei Wan, direct toxic attack with Hua Chong Wan, and antiparasitic treatment plus constitutional support with Bu Dai Wan. None belongs to the platform’s original 63-profile sequence; the asterisk independently identifies official Appendix C membership.',
        'introEs' => 'El capítulo conserva tres estrategias históricas muy diferentes: armonización compleja con Wu Mei Wan, ataque tóxico directo con Hua Chong Wan y tratamiento antiparasitario más apoyo constitucional con Bu Dai Wan. Ninguna pertenece a la secuencia interna original de 63 fichas; el asterisco identifica de forma independiente la pertenencia oficial al Appendix C.',
        'rows' => [
            ['coverage' => 'Mixed Heat-Cold with deficiency', 'coverageEs' => 'Calor-Frío mixto con deficiencia', 'boardNames' => [], 'additional' => 'Wu Mei Wan combines sour binding, bitter draining, acrid warming and tonification for a narrow terminal-yin pattern.', 'additionalEs' => 'Wu Mei Wan combina contención ácida, drenaje amargo, calentamiento acre y tonificación para un patrón estrecho de yin terminal.'],
            ['coverage' => 'Direct destruction and expulsion', 'coverageEs' => 'Destrucción y expulsión directa', 'boardNames' => [], 'additional' => 'Hua Chong Wan contains a lead compound and other high-risk substances; the source preparation is obsolete and unsafe.', 'additionalEs' => 'Hua Chong Wan contiene un compuesto de plomo y otras sustancias de alto riesgo; la preparación de la fuente es obsoleta e insegura.'],
            ['coverage' => 'Pediatric parasites with Spleen deficiency', 'coverageEs' => 'Parásitos pediátricos con deficiencia de Bazo', 'boardNames' => [], 'additional' => 'Bu Dai Wan combines attack and support but uses an obsolete bat-feces preparation; pediatric wasting requires prompt medical and nutritional care.', 'additionalEs' => 'Bu Dai Wan combina ataque y apoyo, pero usa una preparación obsoleta con heces de murciélago; la emaciación pediátrica exige atención médica y nutricional pronta.'],
        ],
        'hypothesis' => 'A plausible curricular explanation is that parasite therapy now depends on laboratory identification, organism-specific pharmaceuticals, follow-up testing and management of complications, while these historical formulas are narrow and carry unusually high toxicity or sanitation concerns.',
        'hypothesisEs' => 'Una explicación curricular plausible es que la terapia antiparasitaria actual depende de identificación de laboratorio, fármacos específicos, pruebas de seguimiento y manejo de complicaciones, mientras estas fórmulas históricas son estrechas y presentan riesgos inusualmente altos de toxicidad o higiene.',
        'value' => 'The chapter remains useful for source literacy and pattern logic, especially the mixed Heat-Cold architecture of Wu Mei Wan. Its practical lesson is also a safety lesson: do not substitute historical formulas for diagnosis and evidence-based antiparasitic care.',
        'valueEs' => 'El capítulo sigue siendo útil para comprender la fuente y la lógica de patrones, sobre todo la arquitectura Calor-Frío mixta de Wu Mei Wan. Su lección práctica también es de seguridad: no sustituir diagnóstico y atención antiparasitaria basada en evidencia por fórmulas históricas.',
    ],
    'treat-abscesses-chapter-20' => [
        'title' => 'Interesting point: the original 63-profile sequence omitted this chapter, while the official 181-formula roster includes selected entries',
        'titleEs' => 'Dato interesante: la secuencia original de 63 fichas omitió este capítulo, mientras el listado oficial de 181 incluye entradas seleccionadas',
        'stat' => '0 original core profiles · official status shown by asterisk',
        'statEs' => '0 fichas originales del núcleo · estatus oficial indicado con asterisco',
        'intro' => 'The source chapter supplies a detailed stage-and-constitution map for lesions, but its historical category combines conditions that modern medicine separates urgently: superficial infection, deep abscess, ischemia, appendicitis, airway disease and solid masses.',
        'introEs' => 'El capítulo fuente ofrece un mapa detallado de etapa y constitución para lesiones, pero su categoría histórica reúne afecciones que la medicina moderna separa con urgencia: infección superficial, absceso profundo, isquemia, apendicitis, enfermedad de vía aérea y masas sólidas.',
        'rows' => [
            ['coverage' => 'Early hot external sores', 'coverageEs' => 'Llagas externas calientes y tempranas', 'boardNames' => [], 'additional' => 'Xian Fang Huo Ming Yin, Wu Wei Xiao Du Yin and Shi Wei Bai Du San grade toxin clearing, Blood movement, exterior release and whether pus has formed.', 'additionalEs' => 'Xian Fang Huo Ming Yin, Wu Wei Xiao Du Yin y Shi Wei Bai Du San gradúan aclaramiento de toxicidad, movimiento de Sangre, liberación Exterior y si ya se formó pus.'],
            ['coverage' => 'Cold or deficient chronic lesions', 'coverageEs' => 'Lesiones crónicas frías o deficientes', 'boardNames' => [], 'additional' => 'Tuo Li Xiao Du Yin, Yang He Tang and Qian Jin Nei Tuo San distinguish watery pus, failure to heal, constitutional weakness and pale nonhot swelling.', 'additionalEs' => 'Tuo Li Xiao Du Yin, Yang He Tang y Qian Jin Nei Tuo San distinguen pus acuoso, falta de cicatrización, debilidad constitucional e hinchazón pálida no caliente.'],
            ['coverage' => 'Internal abscesses and dangerous mimics', 'coverageEs' => 'Abscesos internos e imitadores peligrosos', 'boardNames' => [], 'additional' => 'The six internal formulas preserve Lung, intestinal and throat pattern logic, but imaging, airway management, antibiotics, drainage and surgery take priority when indicated.', 'additionalEs' => 'Las seis fórmulas internas conservan lógica de patrones pulmonares, intestinales y faríngeos, pero imagen, manejo de vía aérea, antibióticos, drenaje y cirugía tienen prioridad cuando proceda.'],
        ],
        'hypothesis' => 'A plausible curricular explanation is that the formulas are highly stage- and disease-specific, while several source indications now depend on urgent diagnostics, procedures and toxic or endangered ingredients outside a compact foundational roster.',
        'hypothesisEs' => 'Una explicación curricular plausible es que las fórmulas dependen mucho de etapa y enfermedad, mientras varias indicaciones de fuente ahora exigen diagnóstico urgente, procedimientos e ingredientes tóxicos o amenazados fuera de un listado fundacional compacto.',
        'value' => 'The chapter remains valuable for learning that “abscess” is not one pattern: Heat versus Cold, pus stage, location and constitutional strength change the strategy. Its modern lesson is equally important—pattern comparison begins only after emergencies and structural disease are addressed.',
        'valueEs' => 'El capítulo sigue siendo valioso para aprender que “absceso” no es un solo patrón: Calor o Frío, etapa del pus, localización y fuerza constitucional cambian la estrategia. Su lección moderna es igual de importante: la comparación de patrones empieza después de atender urgencias y enfermedad estructural.',
    ],
    'external-application-chapter-21' => [
        'title' => 'Interesting point: the chapter is organized by route of administration, but external use can still create systemic toxicity and emergencies',
        'titleEs' => 'Dato interesante: el capítulo se organiza por vía de administración, pero el uso externo aún puede causar toxicidad sistémica y urgencias',
        'stat' => '0 of 11 direct overlaps',
        'statEs' => '0 de 11 coincidencias directas',
        'intro' => 'The source compares powders, ointments, plasters, washes, compresses and point applications. Modern safety reframes the chapter around intact versus open tissue, contamination, absorption, burns, toxic ingredients and conditions that require diagnosis rather than a topical preparation.',
        'introEs' => 'La fuente compara polvos, ungüentos, emplastos, lavados, compresas y aplicaciones en puntos. La seguridad moderna replantea el capítulo alrededor de tejido intacto o abierto, contaminación, absorción, quemaduras, ingredientes tóxicos y afecciones que requieren diagnóstico, no un preparado tópico.',
        'rows' => [
            ['coverage' => 'Hot swelling and weeping skin', 'coverageEs' => 'Hinchazón caliente y piel exudativa', 'boardNames' => [], 'additional' => 'Golden-Yellow, Double-Dark and Indigo preparations separate a swelling ready to head, firm clumping, first-stage trauma and wet Damp-Heat lesions.', 'additionalEs' => 'Preparaciones Golden-Yellow, Double-Dark e Indigo separan hinchazón por formar cabeza, aglomeración firme, trauma temprano y lesiones húmedas por Humedad-Calor.'],
            ['coverage' => 'Dry skin, stubborn itch and later trauma', 'coverageEs' => 'Piel seca, prurito persistente y trauma tardío', 'boardNames' => [], 'additional' => 'Zi Dang Gao moistens; Ku Shen Tang washes a larger itchy area; Hai Tong Pi Tang introduces warming only after acute inflammation subsides.', 'additionalEs' => 'Zi Dang Gao humecta; Ku Shen Tang lava un área pruriginosa mayor; Hai Tong Pi Tang introduce calor solo tras ceder la inflamación aguda.'],
            ['coverage' => 'External treatment of internal complaints', 'coverageEs' => 'Tratamiento externo de síntomas internos', 'boardNames' => [], 'additional' => 'Point plasters, herb cakes, a breast wash and a pregnancy plaster preserve historical route logic, but none replaces diagnosis of oral disease, hypertension, persistent nasal disease, mastitis or obstetric symptoms.', 'additionalEs' => 'Emplastos en puntos, tortas herbales, lavado mamario y emplasto del embarazo conservan lógica histórica de vía, pero ninguno sustituye diagnosticar enfermedad oral, hipertensión, enfermedad nasal persistente, mastitis o síntomas obstétricos.'],
        ],
        'hypothesis' => 'The absence of these profiles from the original internal sequence must not be treated as an official exclusion. Appendix C membership is checked independently by asterisk.',
        'hypothesisEs' => 'La ausencia de estas fichas en la secuencia interna original no debe tratarse como exclusión oficial. La pertenencia al Appendix C se comprueba de forma independiente con asterisco.',
        'value' => 'The chapter remains useful because dosage form changes clinical reasoning: wet versus dry, intact versus open, cooling versus warming and brief versus occlusive exposure all matter. Its strongest modern lesson is that “external” never means harmless.',
        'valueEs' => 'El capítulo sigue siendo útil porque la forma cambia el razonamiento: húmedo o seco, intacto o abierto, refrescar o calentar y exposición breve u oclusiva importan. Su lección moderna más fuerte es que “externo” nunca significa inocuo.',
    ],
    'treat-dryness-ch15' => [
        'title' => 'Interesting point: the platform’s original 14-group sequence has no dedicated Treat Dryness category',
        'titleEs' => 'Dato interesante: la secuencia original de 14 grupos de la plataforma no tiene una categoría propia para tratar Sequedad',
        'stat' => '0 of 8 direct overlaps',
        'statEs' => '0 de 8 coincidencias directas',
        'intro' => 'None of the eight principal Chapter 15 formulas belongs to the platform’s original 63-profile sequence. Official 2026 membership is checked separately against all 181 Appendix C formulas and shown with an asterisk.',
        'introEs' => 'Ninguna de las ocho fórmulas principales del capítulo 15 pertenece a la secuencia interna original de 63 fichas. La pertenencia oficial de 2026 se comprueba por separado contra las 181 fórmulas del Appendix C y se muestra con asterisco.',
        'rows' => [
            ['coverage' => 'Heat with thirst and injury to fluids', 'coverageEs' => 'Calor con sed y lesión de líquidos', 'boardNames' => ['Bai Hu Tang', 'Qing Hao Bie Jia Tang'], 'additional' => 'Chapter 15 distinguishes a still-active external dryness pathogen from established internal Yin-fluid depletion.', 'additionalEs' => 'El capítulo 15 distingue un patógeno de Sequedad externa todavía activo del agotamiento interno ya establecido de Yin-líquidos.'],
            ['coverage' => 'Lung dryness, cough, Phlegm, and rebellious Qi', 'coverageEs' => 'Sequedad de Pulmón, tos, Flema y Qi rebelde', 'boardNames' => ['Bei Mu Gua Lou San', 'Ding Chuan Tang'], 'additional' => 'The chapter grades cool versus warm dryness, superficial versus Qi-level penetration, and Lung-Stomach Yin failure.', 'additionalEs' => 'El capítulo gradúa Sequedad fresca o cálida, penetración superficial o al nivel Qi y falla de Yin de Pulmón-Estómago.'],
            ['coverage' => 'Yin-fluid deficiency with thirst or dry constipation', 'coverageEs' => 'Deficiencia de Yin-líquidos con sed o estreñimiento seco', 'boardNames' => ['Liu Wei Di Huang Wan'], 'additional' => 'Ma Zi Ren Wan appears only in the supplemental purge taxonomy; Yu Ye Tang and Zeng Ye Tang further separate nonascending primal Qi with copious urine from exhausted intestinal fluids after febrile disease.', 'additionalEs' => 'Ma Zi Ren Wan aparece solo en la taxonomía suplementaria de purgación; Yu Ye Tang y Zeng Ye Tang separan además Qi primordial no ascendente con orina copiosa de líquidos intestinales agotados tras enfermedad febril.'],
        ],
        'hypothesis' => 'The internal platform organization distributes dryness across broader mechanisms—Heat, Phlegm, Yin deficiency, Qi rebellion, and constipation. This does not alter official Appendix C membership.',
        'hypothesisEs' => 'La organización interna de la plataforma distribuye Sequedad entre mecanismos más amplios—Calor, Flema, deficiencia de Yin, Qi rebelde y estreñimiento. Esto no modifica la pertenencia oficial al Appendix C.',
        'value' => 'The chapter remains valuable because the same dry cough can require gentle warming dispersion, cooling dispersion, deep moistening, Qi support, or Yin enrichment. Treating external and internal dryness as interchangeable can trap a pathogen or further injure depleted fluids.',
        'valueEs' => 'El capítulo sigue siendo valioso porque una misma tos seca puede requerir dispersión cálida suave, dispersión fresca, humectación profunda, apoyo de Qi o enriquecimiento de Yin. Tratar Sequedad externa e interna como intercambiables puede retener un patógeno o lesionar más los líquidos agotados.',
    ],
];

$runchangwanlegacycategory = $supplementalcategories['official-formulas-ch3'] ?? null;
if ($runchangwanlegacycategory === null || count($runchangwanlegacycategory['formulas']) !== 1
        || $runchangwanlegacycategory['formulas'][0]['name'] !== 'Run Chang Wan') {
    throw new moodle_exception('Run Chang Wan official profile is missing or ambiguous.');
}
$runchangwanprofile = $runchangwanlegacycategory['formulas'][0];
$runchangwanprofile['categoryKey'] = 'purge';
$runchangwanprofile['category'] = 'Purge';
$runchangwanprofile['categoryEs'] = 'Purgar';
$runchangwanprofile['sourceChapter'] = 2;
$runchangwanprofile['integratedOfficialProfile'] = true;
$categories['purge']['formulas'][] = $runchangwanprofile;
$supplementalcategories[$tonifychapteroverview['key']] = $tonifychapteroverview;
$supplementalcategories[$stabilizechapteroverview['key']] = $stabilizechapteroverview;
$supplementalcategories[$calmshenchapteroverview['key']] = $calmshenchapteroverview;
$supplementalcategories[$openorificeschapteroverview['key']] = $openorificeschapteroverview;
$supplementalcategories[$regulateqichapteroverview['key']] = $regulateqichapteroverview;
$supplementalcategories[$regulatebloodchapteroverview['key']] = $regulatebloodchapteroverview;
$supplementalcategories[$expelwindchapteroverview['key']] = $expelwindchapteroverview;
$supplementalcategories[$expeldampchapteroverview['key']] = $expeldampchapteroverview;
$supplementalcategories[$transformphlegmchapteroverview['key']] = $transformphlegmchapteroverview;
$supplementalcategories[$digestivechapteroverview['key']] = $digestivechapteroverview;
$supplementalcategories[$expelparasiteschapteroverview['key']] = $expelparasiteschapteroverview;
$supplementalcategories[$treatabscesseschapteroverview['key']] = $treatabscesseschapteroverview;
$supplementalcategories[$externalapplicationchapteroverview['key']] = $externalapplicationchapteroverview;
unset($supplementalcategories['stabilize-bind-ch9']);
unset($supplementalcategories['calm-shen-ch10']);
unset($supplementalcategories['open-orifices-ch11']);
unset($supplementalcategories['regulate-qi-ch12']);
unset($supplementalcategories['regulate-blood-ch13']);
unset($supplementalcategories['expel-wind-ch14']);
unset($supplementalcategories['expel-damp-ch16']);
unset($supplementalcategories['transform-phlegm-ch17']);
unset($supplementalcategories['digestive-ch18']);
unset($supplementalcategories['expel-parasites-ch19']);
unset($supplementalcategories['treat-abscesses-ch20']);
unset($supplementalcategories['external-application-ch21']);

$benskychapters = [
    ['number' => 1, 'key' => 'release-exterior', 'name' => 'Formulas That Release the Exterior', 'nameEs' => 'Fórmulas que liberan el Exterior', 'complete' => false, 'formulas' => $categories['release-exterior']['formulas']],
    ['number' => 2, 'key' => 'purge', 'name' => 'Formulas That Drain Downward', 'nameEs' => 'Fórmulas que drenan hacia abajo', 'complete' => false, 'formulas' => $categories['purge']['formulas']],
    ['number' => 3, 'key' => 'harmonize', 'name' => 'Formulas That Harmonize', 'nameEs' => 'Fórmulas que armonizan', 'complete' => false, 'formulas' => $categories['harmonize']['formulas']],
    ['number' => 4, 'key' => 'clear-heat', 'name' => 'Formulas That Clear Heat', 'nameEs' => 'Fórmulas que aclaran el Calor', 'complete' => false, 'formulas' => $categories['clear-heat']['formulas']],
    ['number' => 5, 'key' => 'dispel-summerheat', 'name' => 'Formulas That Dispel Summerheat', 'nameEs' => 'Fórmulas que eliminan el Calor de Verano', 'complete' => true, 'formulas' => $supplementalcategories['dispel-summerheat']['formulas']],
    ['number' => 6, 'key' => 'warm-interior-cold', 'name' => 'Formulas That Warm Interior Cold', 'nameEs' => 'Fórmulas que calientan el Frío Interior', 'complete' => true, 'formulas' => $supplementalcategories['warm-interior-cold']['formulas']],
    ['number' => 7, 'key' => 'exterior-interior-excess', 'name' => 'Formulas That Release Exterior-Interior Excess', 'nameEs' => 'Fórmulas que liberan el exceso Exterior-Interior', 'complete' => true, 'formulas' => $supplementalcategories['exterior-interior-excess']['formulas']],
    ['number' => 8, 'key' => 'tonify-chapter-8', 'name' => 'Formulas That Tonify', 'nameEs' => 'Fórmulas que tonifican', 'complete' => true, 'formulas' => $tonifychapteroverview['formulas']],
    ['number' => 9, 'key' => 'stabilize-bind-chapter-9', 'name' => 'Formulas That Stabilize and Bind', 'nameEs' => 'Fórmulas que estabilizan y contienen', 'complete' => true, 'formulas' => $stabilizechapteroverview['formulas']],
    ['number' => 10, 'key' => 'calm-shen-chapter-10', 'name' => 'Formulas That Calm the Spirit', 'nameEs' => 'Fórmulas que calman el Espíritu', 'complete' => true, 'formulas' => $calmshenchapteroverview['formulas']],
    ['number' => 11, 'key' => 'open-orifices-chapter-11', 'name' => 'Formulas That Open the Sensory Orifices', 'nameEs' => 'Fórmulas que abren los orificios sensoriales', 'complete' => true, 'formulas' => $openorificeschapteroverview['formulas']],
    ['number' => 12, 'key' => 'regulate-qi-chapter-12', 'name' => 'Formulas That Regulate the Qi', 'nameEs' => 'Fórmulas que regulan el Qi', 'complete' => true, 'formulas' => $regulateqichapteroverview['formulas']],
    ['number' => 13, 'key' => 'regulate-blood-chapter-13', 'name' => 'Formulas That Regulate the Blood', 'nameEs' => 'Fórmulas que regulan la Sangre', 'complete' => true, 'formulas' => $regulatebloodchapteroverview['formulas']],
    ['number' => 14, 'key' => 'expel-wind-chapter-14', 'name' => 'Formulas That Expel Wind', 'nameEs' => 'Fórmulas que expulsan el Viento', 'complete' => true, 'formulas' => $expelwindchapteroverview['formulas']],
    ['number' => 15, 'key' => 'treat-dryness-ch15', 'name' => 'Formulas That Treat Dryness', 'nameEs' => 'Fórmulas que tratan la Sequedad', 'complete' => true, 'formulas' => $supplementalcategories['treat-dryness-ch15']['formulas']],
    ['number' => 16, 'key' => 'expel-damp-chapter-16', 'name' => 'Formulas That Expel Dampness', 'nameEs' => 'Fórmulas que expulsan la Humedad', 'complete' => true, 'formulas' => $expeldampchapteroverview['formulas']],
    ['number' => 17, 'key' => 'transform-phlegm-chapter-17', 'name' => 'Formulas That Dispel Phlegm', 'nameEs' => 'Fórmulas que eliminan la Flema', 'complete' => true, 'formulas' => $transformphlegmchapteroverview['formulas']],
    ['number' => 18, 'key' => 'digestive-chapter-18', 'name' => 'Formulas That Reduce Food Stagnation', 'nameEs' => 'Fórmulas que reducen el estancamiento de alimentos', 'complete' => true, 'formulas' => $digestivechapteroverview['formulas']],
    ['number' => 19, 'key' => 'expel-parasites-chapter-19', 'name' => 'Formulas That Expel Parasites', 'nameEs' => 'Fórmulas que expulsan parásitos', 'complete' => true, 'formulas' => $expelparasiteschapteroverview['formulas']],
    ['number' => 20, 'key' => 'treat-abscesses-chapter-20', 'name' => 'Formulas That Treat Abscesses and Sores', 'nameEs' => 'Fórmulas que tratan abscesos y llagas', 'complete' => true, 'formulas' => $treatabscesseschapteroverview['formulas']],
    ['number' => 21, 'key' => 'external-application-chapter-21', 'name' => 'Formulas for External Application', 'nameEs' => 'Fórmulas para aplicación externa', 'complete' => true, 'formulas' => $externalapplicationchapteroverview['formulas']],
];
$benskychapterbykey = [];
foreach ($benskychapters as $benskychapter) {
    $benskychapterbykey[$benskychapter['key']] = $benskychapter;
}

$allcategories = $categories + $supplementalcategories;
$selected = $allcategories[$focus] ?? null;
$issupplemental = $selected !== null && isset($supplementalcategories[$selected['key']]);
$isofficialexpansionpage = $selected !== null && ($selected['sourceType'] ?? '') === 'official-expansion';
$istonifychapterpage = $selected !== null
    && ($selected['key'] === 'tonify-chapter-8' || in_array($selected['key'], $tonifychapterkeys, true));
$iswarmchapterpage = $selected !== null && $selected['key'] === 'warm-interior-cold';
$isstabilizechapterpage = $selected !== null && $selected['key'] === 'stabilize-bind-chapter-9';
$isopenorificeschapterpage = $selected !== null && $selected['key'] === 'open-orifices-chapter-11';
$selectedguide = $selected === null ? null : ($data['categoryGuides'][$selected['key']] ?? null);
if ($selected !== null && $selected['key'] === 'purge') {
    $selectedguide['formulaSections'][] = [
        'key' => 'moisten-blood-fluid-deficiency',
        'name' => 'Moisten dry Intestines from Blood-fluid deficiency',
        'nameEs' => 'Humedecer Intestinos secos por deficiencia de Sangre-líquidos',
        'formulaNames' => ['Run Chang Wan'],
        'copy' => 'Run Chang Wan nourishes Blood and fluids and gently moistens the Intestines. It does not attack Yangming Heat clumping like the three Cheng Qi formulas.',
        'copyEs' => 'Run Chang Wan nutre Sangre y líquidos y humedece suavemente los Intestinos. No ataca la aglomeración de Calor Yangming como las tres fórmulas Cheng Qi.',
    ];
    $selectedguide['quickCompare'][] = [
        'Run Chang Wan',
        'Blood-fluid deficiency with dry Intestines',
        'Chronic dry stool after illness, childbirth, or blood loss; pallor and dizziness; nourish and moisten rather than purge forcefully',
        'Deficiencia de Sangre-líquidos con Intestinos secos',
        'Heces secas crónicas tras enfermedad, parto o pérdida de Sangre; palidez y mareo; nutrir y humedecer en vez de purgar con fuerza',
    ];
}
$selectedformulasections = $selectedguide['formulaSections'] ?? [];
$selectedformulasectionbyname = [];
if ($selected !== null && $selectedformulasections !== []) {
    $selectedformulasbyname = [];
    foreach ($selected['formulas'] as $selectedformula) {
        $selectedformulasbyname[$selectedformula['name']] = $selectedformula;
    }
    $orderedselectedformulas = [];
    foreach ($selectedformulasections as $formulasection) {
        foreach ($formulasection['formulaNames'] as $formulaname) {
            if (!isset($selectedformulasbyname[$formulaname])) {
                throw new moodle_exception('Missing formula in clinical section: ' . $formulaname);
            }
            if (isset($selectedformulasectionbyname[$formulaname])) {
                throw new moodle_exception('Formula assigned to multiple clinical sections: ' . $formulaname);
            }
            $selectedformulasectionbyname[$formulaname] = $formulasection;
            $orderedselectedformulas[] = $selectedformulasbyname[$formulaname];
        }
    }
    if (count($orderedselectedformulas) !== count($selected['formulas'])) {
        throw new moodle_exception('Clinical sections do not cover every selected formula.');
    }
    $selected['formulas'] = $orderedselectedformulas;
}
$unifiedfamilystatusformulas = [
    'tonify' => $categories['tonify']['formulas'],
    'warm-interior' => $warmchaptercategory['formulas'],
    'stabilize-bind' => $stabilizechapteroverview['formulas'],
    'calm-shen' => $calmshenchapteroverview['formulas'],
    'regulate-qi' => $regulateqichapteroverview['formulas'],
    'invigorate-blood' => $regulatebloodchapteroverview['formulas'],
    'expel-wind' => $expelwindchapteroverview['formulas'],
    'expel-damp' => $expeldampchapteroverview['formulas'],
    'transform-phlegm' => array_values(array_filter(
        $transformphlegmchapteroverview['formulas'],
        static fn(array $formula): bool => ($formula['chapterRole'] ?? 'principal') !== 'associated'
    )),
    'digestive' => $digestivechapteroverview['formulas'],
];
$selectedstatusformulas = $selected === null
    ? []
    : ($unifiedfamilystatusformulas[$selected['key']] ?? $selected['formulas']);
$selectedprincipalformulas = $selected === null ? [] : array_values(array_filter(
    $selected['formulas'],
    static fn(array $formula): bool => ($formula['chapterRole'] ?? 'principal') !== 'associated'
));
if ($selected !== null && $selected['key'] === 'transform-phlegm-chapter-17') {
    $selectedstatusformulas = $selectedprincipalformulas;
}
$selectedprincipalformulacount = count($selectedprincipalformulas);
$selectedboardstatus = $selected === null ? null : $getboardstatus($selectedstatusformulas, $ises);
$categorykeys = array_column($benskychapters, 'key');
$selectedindex = $selected === null ? null : array_search($selected['key'], $categorykeys, true);
$selectedbenskychapter = $selected === null ? null : ($benskychapterbykey[$selected['key']] ?? null);

$params = [];
if ($selected !== null) {
    $params['focus'] = $selected['key'];
}
if ($ises) {
    $params['lang'] = 'es';
}
$pageurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $params);
$indexparams = $ises ? ['lang' => 'es'] : [];
$indexurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $indexparams);
$tonifyparentparams = ['focus' => 'tonify'];
if ($ises) {
    $tonifyparentparams['lang'] = 'es';
}
$tonifyparenturl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $tonifyparentparams);
$warmparentparams = ['focus' => 'warm-interior'];
if ($ises) {
    $warmparentparams['lang'] = 'es';
}
$warmparenturl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $warmparentparams);
$warmchapterparams = ['focus' => 'warm-interior-cold'];
if ($ises) {
    $warmchapterparams['lang'] = 'es';
}
$warmchapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $warmchapterparams);
$stabilizeparentparams = ['focus' => 'stabilize-bind'];
if ($ises) {
    $stabilizeparentparams['lang'] = 'es';
}
$stabilizeparenturl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $stabilizeparentparams);
$stabilizechapterparams = ['focus' => 'stabilize-bind-chapter-9'];
if ($ises) {
    $stabilizechapterparams['lang'] = 'es';
}
$stabilizechapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $stabilizechapterparams);
$openorificeschapterparams = ['focus' => 'open-orifices-chapter-11'];
if ($ises) {
    $openorificeschapterparams['lang'] = 'es';
}
$openorificeschapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $openorificeschapterparams);
$regulateqichapterparams = ['focus' => 'regulate-qi-chapter-12'];
if ($ises) {
    $regulateqichapterparams['lang'] = 'es';
}
$regulateqichapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $regulateqichapterparams);
$regulatebloodchapterparams = ['focus' => 'regulate-blood-chapter-13'];
if ($ises) {
    $regulatebloodchapterparams['lang'] = 'es';
}
$regulatebloodchapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $regulatebloodchapterparams);
$expelwindchapterparams = ['focus' => 'expel-wind-chapter-14'];
if ($ises) {
    $expelwindchapterparams['lang'] = 'es';
}
$expelwindchapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $expelwindchapterparams);
$languageparams = $selected === null ? [] : ['focus' => $selected['key']];
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $languageparams);
$herbologyurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => 'herbology']);
$dashboardurl = new moodle_url('/my/');
$formulalistparams = $ises ? ['lang' => 'es'] : [];
if ($fromlibrary) {
    $formulalistparams['from'] = 'library';
}
$formulalisturl = new moodle_url('/blocks/myprogress/formula-list.php', $formulalistparams);
$singleherbsurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $ises ? ['lang' => 'es'] : []);
$duiyaomoduleurl = new moodle_url($ises
    ? '/study-guide/comparisons/dui-yao-es.html'
    : '/study-guide/comparisons/dui-yao.html');
$benskymapurl = new moodle_url('/blocks/myprogress/herb-formula-category-map.php', $ises ? ['lang' => 'es'] : []);
$formularoutetitle = $selected === null
    ? ($ises ? 'Todos los capítulos de fórmulas' : 'All formula chapters')
    : ($selectedbenskychapter !== null
        ? ($ises ? $selectedbenskychapter['nameEs'] : $selectedbenskychapter['name'])
        : ($ises ? $selected['nameEs'] : $selected['name']));
$guidelinepath = $ises
    ? '/study-guide/guidelines/chinese-herbal-medicine-es.html'
    : '/study-guide/guidelines/chinese-herbal-medicine.html';
$guidelinerosterurl = new moodle_url($guidelinepath, ['return' => $pageurl->out(false)], 'guide-formula-roster-title');

$previousurl = null;
$nexturl = null;
$previouschapter = null;
$nextchapter = null;
if ($selectedindex !== null && $selectedindex > 0) {
    $previouschapter = $benskychapters[$selectedindex - 1];
    $previousparams = ['focus' => $categorykeys[$selectedindex - 1]];
    if ($ises) {
        $previousparams['lang'] = 'es';
    }
    $previousurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $previousparams);
}
if ($selectedindex !== null && $selectedindex < count($categorykeys) - 1) {
    $nextchapter = $benskychapters[$selectedindex + 1];
    $nextparams = ['focus' => $categorykeys[$selectedindex + 1]];
    if ($ises) {
        $nextparams['lang'] = 'es';
    }
    $nexturl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $nextparams);
}

$renderchapternavigation = static function(string $instance) use (
    $benskychapters,
    $selectedbenskychapter,
    $previouschapter,
    $previousurl,
    $nextchapter,
    $nexturl,
    $indexurl,
    $ises
): string {
    $selectedkey = $selectedbenskychapter['key'] ?? '';
    $selectid = 'formula-chapter-jump-' . $instance;
    ob_start();
    ?>
    <nav class="formula-study-guide__chapter-path<?php echo $selectedbenskychapter === null ? ' formula-study-guide__chapter-path--index' : ''; ?>" aria-label="<?php echo s($ises ? 'Recorrido de los 21 capítulos de Bensky' : '21-chapter Bensky path'); ?>">
        <?php if ($selectedbenskychapter !== null): ?>
            <?php if ($previousurl !== null && $previouschapter !== null): ?>
                <a class="formula-study-guide__chapter-path-link" href="<?php echo $previousurl->out(false); ?>">
                    <span><?php echo s(($ises ? '← Anterior · Capítulo ' : '← Previous · Chapter ') . $previouschapter['number']); ?></span>
                    <strong><?php echo s($ises ? $previouschapter['nameEs'] : $previouschapter['name']); ?></strong>
                </a>
            <?php else: ?>
                <span class="formula-study-guide__chapter-path-link formula-study-guide__chapter-path-link--disabled">
                    <span><?php echo s($ises ? 'Inicio del recorrido' : 'Start of path'); ?></span>
                    <strong><?php echo s($ises ? 'Este es el primer capítulo' : 'This is the first chapter'); ?></strong>
                </span>
            <?php endif; ?>
        <?php endif; ?>
        <div class="formula-study-guide__chapter-jump">
            <form method="get" action="<?php echo (new moodle_url('/blocks/myprogress/formulas-study-guide.php'))->out(false); ?>" data-formula-chapter-jump>
                <label for="<?php echo s($selectid); ?>"><?php echo s($ises ? 'Ir al capítulo…' : 'Jump to chapter…'); ?></label>
                <div>
                    <select id="<?php echo s($selectid); ?>" name="focus">
                        <?php if ($selectedkey === ''): ?><option value="" selected disabled><?php echo s($ises ? 'Selecciona uno de los 21 capítulos' : 'Select one of the 21 chapters'); ?></option><?php endif; ?>
                        <?php foreach ($benskychapters as $chapter): ?>
                            <option value="<?php echo s($chapter['key']); ?>"<?php echo $chapter['key'] === $selectedkey ? ' selected' : ''; ?>><?php echo s($chapter['number'] . ' · ' . ($ises ? $chapter['nameEs'] : $chapter['name'])); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($ises): ?><input type="hidden" name="lang" value="es"><?php endif; ?>
                    <button class="formula-study-guide__button" type="submit"><?php echo s($ises ? 'Abrir' : 'Open'); ?></button>
                </div>
            </form>
            <?php if ($selectedbenskychapter !== null): ?><a href="<?php echo $indexurl->out(false); ?>"><?php echo s($ises ? 'Ver los 21 capítulos' : 'View all 21 chapters'); ?></a><?php endif; ?>
        </div>
        <?php if ($selectedbenskychapter !== null): ?>
            <?php if ($nexturl !== null && $nextchapter !== null): ?>
                <a class="formula-study-guide__chapter-path-link formula-study-guide__chapter-path-link--next" href="<?php echo $nexturl->out(false); ?>">
                    <span><?php echo s(($ises ? 'Siguiente · Capítulo ' : 'Next · Chapter ') . $nextchapter['number'] . ' →'); ?></span>
                    <strong><?php echo s($ises ? $nextchapter['nameEs'] : $nextchapter['name']); ?></strong>
                </a>
            <?php else: ?>
                <span class="formula-study-guide__chapter-path-link formula-study-guide__chapter-path-link--next formula-study-guide__chapter-path-link--disabled">
                    <span><?php echo s($ises ? 'Fin del recorrido' : 'End of path'); ?></span>
                    <strong><?php echo s($ises ? 'Este es el último capítulo' : 'This is the final chapter'); ?></strong>
                </span>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
    <?php
    return (string) ob_get_clean();
};

$pagetitle = $ises ? 'Guía de estudio de fórmulas' : 'Formula Study Guide';
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->set_cacheable(false);
$PAGE->requires->js_init_code(<<<'JS'
document.querySelectorAll('[data-formula-chapter-jump] select').forEach(function(select) {
    select.addEventListener('change', function() {
        if (select.value) {
            select.form.submit();
        }
    });
});
(function() {
    'use strict';
    var reader = document.querySelector('[data-formula-read-aloud]');
    var page = document.querySelector('.formula-study-guide');
    if (!reader || !page) return;
    var supported = 'speechSynthesis' in window && 'SpeechSynthesisUtterance' in window;
    var language = reader.getAttribute('data-language') || 'en-US';
    var isSpanish = language.toLowerCase().indexOf('es') === 0;
    var mobileVoiceMenu = !!(window.matchMedia && (
        window.matchMedia('(max-width: 760px)').matches || window.matchMedia('(pointer: coarse)').matches
    ));
    var sectionSelect = reader.querySelector('[data-formula-read-section-select]');
    var voiceSelect = reader.querySelector('[data-formula-read-voice]');
    var englishVoiceSelect = reader.querySelector('[data-formula-read-english-voice]');
    var explanationModeSelect = reader.querySelector('[data-formula-read-mode]');
    var fullButton = reader.querySelector('[data-formula-read-full]');
    var sectionButton = reader.querySelector('[data-formula-read-section]');
    var selectionButton = reader.querySelector('[data-formula-read-selection]');
    var visibleButton = reader.querySelector('[data-formula-read-visible]');
    var pauseButton = reader.querySelector('[data-formula-read-pause]');
    var stopButton = reader.querySelector('[data-formula-read-stop]');
    var backButton = reader.querySelector('[data-formula-read-back]');
    var forwardButton = reader.querySelector('[data-formula-read-forward]');
    var progress = reader.querySelector('[data-formula-read-progress]');
    var progressValue = reader.querySelector('[data-formula-read-progress-value]');
    var status = reader.querySelector('[data-formula-read-status]');
    var sections = [];
    var speechChunks = [];
    var currentChunk = 0;
    var sessionId = 0;
    var paused = false;
    var selectedVoice = null;
    var selectedEnglishVoice = null;
    var cachedSelection = '';

    if (explanationModeSelect) {
        try {
            explanationModeSelect.value = window.localStorage.getItem('acupuncturetests-formula-reader-mode') === 'extended'
                ? 'extended'
                : 'summary';
        } catch (error) {
            explanationModeSelect.value = 'summary';
        }
    }

    function setStatus(message) {
        status.textContent = message;
    }

    function addSentenceBoundaries(root) {
        root.querySelectorAll('h1,h2,h3,h4,h5,h6,p,li,dt,dd,summary,th,td,figcaption,blockquote,.formula-study-guide__fact,.formula-study-guide__formula-translation,.formula-study-guide__category>strong,.formula-study-guide__category-meta,.formula-study-guide__board-status').forEach(function(element) {
            var text = (element.textContent || '').trim();
            if (text && !/[.!?:]$/.test(text)) element.appendChild(document.createTextNode('. '));
        });
    }

    function comparisonCellText(cell, preserveEnglish) {
        var clone = cell.cloneNode(true);
        if (preserveEnglish && isSpanish) {
            clone.querySelectorAll('[data-formula-english-speech]').forEach(function(node) {
                var englishName = (node.getAttribute('data-formula-english-speech') || '').trim();
                node.replaceWith(document.createTextNode(englishName ? ' ([[en:' + englishName + ']]) ' : ''));
            });
        }
        clone.querySelectorAll('.formula-study-guide__domain-badge,.formula-study-guide__formula-translation,.formula-study-guide__formula-english-inline,[aria-hidden="true"]').forEach(function(node) {
            node.remove();
        });
        return (clone.textContent || '').replace(/[*＊]+/g, '').replace(/\s+/g, ' ').trim();
    }

    function joinComparisonSigns(value) {
        var items = String(value || '').split(/\s*[·•;]\s*/).map(function(item) {
            item = item.trim();
            if (!item) return '';
            if (!isSpanish) return item;
            var normalized = item
                .replace(/^Sin sudor$/i, 'la falta de sudoración')
                .replace(/^Sudor espontáneo$/i, 'la sudoración espontánea')
                .replace(/^Nuca y espalda superior rígidas$/i, 'la rigidez de la nuca y de la parte superior de la espalda')
                .replace(/^pulso\s+(.+)-(.+)$/i, 'un pulso $1 y $2');
            if (normalized !== item) return normalized;
            if (/^sibilancias$/i.test(item)) return 'las sibilancias';
            if (/^pulso\b/i.test(item)) return 'un ' + item.toLowerCase();
            if (/^(aversión|falta|rigidez|saburra|sudoración)\b/i.test(item)) return 'la ' + item.toLowerCase();
            if (/^dolor\b/i.test(item)) return 'el ' + item.toLowerCase();
            return item.charAt(0).toLowerCase() + item.slice(1);
        }).filter(Boolean);
        if (items.length < 2) return items[0] || '';
        var connector = isSpanish ? ' y ' : ' and ';
        return items.slice(0, -1).join(', ') + connector + items[items.length - 1];
    }

    function installComparisonNarrations(root) {
        var tables = Array.from(root.querySelectorAll('[data-formula-comparison-table]'));
        if (root.matches && root.matches('[data-formula-comparison-table]')) tables.unshift(root);
        tables.forEach(function(table) {
            var rows = [];
            table.querySelectorAll('tbody tr').forEach(function(row) {
                var cells = row.querySelectorAll('td');
                if (cells.length < 3) return;
                var formula = comparisonCellText(cells[0]);
                var englishName = cells[0].getAttribute('data-formula-english-name') || '';
                var pattern = comparisonCellText(cells[1]);
                var signs = joinComparisonSigns(comparisonCellText(cells[2]));
                if (!formula || !pattern || !signs) return;
                if (isSpanish) {
                    pattern = pattern.replace(/^Viento-Frío de exceso$/i, 'Viento-Frío en Exceso');
                    rows.push('La fórmula ' + formula
                        + (englishName ? ' ([[en:' + englishName + ']]),' : '')
                        + ' tiene un patrón de ' + pattern + '. Sus síntomas clave son ' + signs + '.');
                } else {
                    rows.push('The formula ' + formula
                        + (englishName ? ', ' + englishName + ',' : '')
                        + ' is used for a pattern of ' + pattern + '. Its key symptoms are ' + signs + '.');
                }
            });
            if (!rows.length) return;
            var narration = document.createElement('p');
            narration.textContent = (isSpanish
                ? 'Esta tabla compara fórmulas de la misma sección. Para comprenderla, relaciona cada fórmula con el patrón que trata y después con los signos que permiten distinguirla de las demás. '
                : 'This table compares formulas in the same section. To understand it, connect each formula first with the pattern it treats and then with the signs that distinguish it from the others. ') + rows.join(' ');
            if (table === root) {
                root.replaceChildren(narration);
            } else {
                table.replaceWith(narration);
            }
        });
    }

    function installContextTableNarrations(root) {
        var tables = Array.from(root.querySelectorAll('[data-formula-context-table]'));
        if (root.matches && root.matches('[data-formula-context-table]')) tables.unshift(root);
        tables.forEach(function(table) {
            var introduction = (table.getAttribute('data-table-narration') || '').trim();
            var headers = Array.from(table.querySelectorAll('thead th')).map(function(header) {
                return (header.textContent || '').replace(/\s*:\s*$/, '').replace(/\s+/g, ' ').trim();
            });
            var rows = [];
            table.querySelectorAll('tbody tr').forEach(function(row) {
                var cells = Array.from(row.querySelectorAll('td'));
                var fields = cells.map(function(cell, index) {
                    var value = comparisonCellText(cell, true);
                    if (!value) return '';
                    var heading = headers[index] || (isSpanish ? 'Dato' : 'Detail');
                    return heading + ': ' + value;
                }).filter(Boolean);
                if (fields.length) rows.push(fields.join('. ') + '.');
            });
            if (!introduction && !rows.length) return;
            var narration = document.createElement('p');
            narration.textContent = (introduction ? introduction + ' ' : '') + rows.join(' ');
            if (table === root) {
                root.replaceChildren(narration);
            } else {
                table.replaceWith(narration);
            }
        });
    }

    function installDuiYaoNarrations(root) {
        var spanishOrdinals = ['primer', 'segundo', 'tercer', 'cuarto', 'quinto', 'sexto', 'séptimo'];
        var englishOrdinals = ['first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh'];
        var cards = Array.from(root.querySelectorAll('[data-formula-dui-yao-card]'));
        if (root.matches && root.matches('[data-formula-dui-yao-card]')) cards.unshift(root);
        cards.forEach(function(card) {
            var titleElement = card.querySelector('h5');
            var actionElement = card.querySelector('p');
            var title = titleElement ? (titleElement.textContent || '').trim() : '';
            var action = actionElement ? (actionElement.textContent || '').trim().replace(/[.]+$/, '') : '';
            var position = Math.max(1, Number(card.getAttribute('data-dui-yao-related-position') || 1));
            var categoryName = (card.getAttribute('data-dui-yao-category-name') || '').trim();
            var firstName = card.getAttribute('data-dui-yao-first-name') || '';
            var firstLatin = card.getAttribute('data-dui-yao-first-latin') || '';
            var secondName = card.getAttribute('data-dui-yao-second-name') || '';
            var secondLatin = card.getAttribute('data-dui-yao-second-latin') || '';
            var formulas = [];
            try {
                formulas = JSON.parse(card.getAttribute('data-dui-yao-related-formulas') || '[]');
            } catch (error) {
                formulas = [];
            }
            if (!title) return;
            title = title.replace(/\s*\+\s*/g, isSpanish ? ' y ' : ' and ');
            var ordinal = isSpanish
                ? (spanishOrdinals[position - 1] || String(position))
                : (englishOrdinals[position - 1] || String(position));
            var categoryRelation = categoryName
                ? (isSpanish ? ' con la categoría ' + categoryName : ' to the ' + categoryName + ' category')
                : '';
            var introduction;
            if (firstName && firstLatin && secondName && secondLatin) {
                introduction = isSpanish
                    ? 'El ' + ordinal + ' Duì Yào relacionado' + categoryRelation + ' que analizaremos combina ' + firstName
                        + ', ' + firstLatin + ', con ' + secondName + ', ' + secondLatin + '.'
                    : 'The ' + ordinal + ' Duì Yào related' + categoryRelation + ' combines ' + firstName
                        + ', ' + firstLatin + ', with ' + secondName + ', ' + secondLatin + '.';
            } else {
                introduction = isSpanish
                    ? 'El ' + ordinal + ' Duì Yào relacionado' + categoryRelation + ' que analizaremos combina ' + title + '.'
                    : 'The ' + ordinal + ' Duì Yào related' + categoryRelation + ' combines ' + title + '.';
            }
            var relation = '';
            var formulaNames = formulas.map(function(formula) {
                var name = String(formula.name || '').trim();
                var englishName = String(formula.englishName || '').trim();
                return name + (englishName
                    ? (isSpanish ? ' ([[en:' + englishName + ']])' : ', ' + englishName)
                    : '');
            }).filter(Boolean);
            if (formulaNames.length === 1) {
                relation = (isSpanish
                    ? 'Este Duì Yào está relacionado con la fórmula '
                    : 'This Duì Yào is related to the formula ') + formulaNames[0] + '.';
            } else if (formulaNames.length > 1) {
                var connector = isSpanish ? ' y ' : ' and ';
                var formulaList = formulaNames.slice(0, -1).join(', ') + connector + formulaNames[formulaNames.length - 1];
                relation = (isSpanish
                    ? 'Este Duì Yào está relacionado con las fórmulas '
                    : 'This Duì Yào is related to the formulas ') + formulaList + '.';
            }
            var narration = document.createElement('p');
            var actionNarration = '';
            if (action) {
                var normalizedAction = action.charAt(0).toLowerCase() + action.slice(1);
                actionNarration = isSpanish
                    ? 'Su acción principal es ' + normalizedAction + '. '
                    : 'Its main action is to ' + normalizedAction + '. ';
            }
            narration.textContent = introduction + ' ' + actionNarration + relation;
            if (card === root) {
                root.replaceChildren(narration);
            } else {
                card.replaceWith(narration);
            }
        });
    }

    function installFormulaRiskNarrations(root) {
        var risks = Array.from(root.querySelectorAll('.formula-study-guide__risk'));
        if (root.matches && root.matches('.formula-study-guide__risk')) risks.unshift(root);
        risks.forEach(function(risk) {
            var card = risk.closest('.formula-study-guide__formula');
            var pattern = card ? card.querySelector('.formula-study-guide__formula-pattern') : null;
            var riskText = (risk.textContent || '').replace(/[.:;,—-]+\s*$/, '').trim();
            if (!pattern || !riskText) return;
            var patternText = (pattern.textContent || '')
                .replace(/^\s*(?:Patrón|Pattern)\s*:\s*/i, '')
                .trim();
            if (!patternText) return;
            pattern.textContent = riskText + (isSpanish ? ' — Patrón: ' : ' — Pattern: ') + patternText;
            risk.remove();
        });
    }

    function installKingHerbNarrations(root) {
        var facts = Array.from(root.querySelectorAll('.formula-study-guide__fact'));
        if (root.matches && root.matches('.formula-study-guide__fact')) facts.unshift(root);
        facts.forEach(function(fact) {
            var label = fact.querySelector('strong');
            var value = fact.querySelector('span');
            var labelText = label ? (label.textContent || '').trim() : '';
            var herbs = value ? (value.textContent || '').split(/\s*,\s*/).filter(Boolean) : [];
            if (!/^(?:Hierbas? emperador|King herbs?)/i.test(labelText) || !herbs.length) return;
            var herbList = herbs.length > 1
                ? herbs.slice(0, -1).join(', ') + (isSpanish ? ' y ' : ' and ') + herbs[herbs.length - 1]
                : herbs[0];
            fact.textContent = isSpanish
                ? (herbs.length > 1 ? 'Las hierbas emperador son ' : 'La hierba emperador es ') + herbList + '.'
                : (herbs.length > 1 ? 'The king herbs are ' : 'The king herb is ') + herbList + '.';
        });
    }

    function installEnglishFormulaNarrations(root) {
        if (!isSpanish) return;
        var names = Array.from(root.querySelectorAll('[data-formula-english-speech],[data-herb-english-speech]'));
        if (root.matches && root.matches('[data-formula-english-speech],[data-herb-english-speech]')) names.unshift(root);
        names.forEach(function(name) {
            var englishName = (name.getAttribute('data-formula-english-speech')
                || name.getAttribute('data-herb-english-speech') || '')
                .replace(/^\s*\(|\)\s*$/g, '')
                .trim();
            if (!englishName) return;
            name.replaceWith(document.createTextNode(' ([[en:' + englishName + ']]) '));
        });
    }

    function extractText(element) {
        if (!element) return '';
        var clone = element.cloneNode(true);
        installComparisonNarrations(clone);
        installContextTableNarrations(clone);
        installDuiYaoNarrations(clone);
        installEnglishFormulaNarrations(clone);
        installFormulaRiskNarrations(clone);
        installKingHerbNarrations(clone);
        clone.querySelectorAll('[data-formula-read-aloud],nav,form,button,input,select,option,script,style,[hidden],[aria-hidden="true"],[data-read-aloud-ignore],.formula-study-guide__domain-badge').forEach(function(node) {
            node.remove();
        });
        addSentenceBoundaries(clone);
        return (clone.textContent || '').replace(/\s+/g, ' ').trim();
    }

    function formulaSummaryField(element, labelPattern) {
        if (!element) return '';
        var clone = element.cloneNode(true);
        installEnglishFormulaNarrations(clone);
        clone.querySelectorAll('[data-read-aloud-ignore],[aria-hidden="true"]').forEach(function(node) {
            node.remove();
        });
        return (clone.textContent || '')
            .replace(labelPattern || /^$/, '')
            .replace(/[*＊]+/g, '')
            .replace(/\s+/g, ' ')
            .replace(/[.;:,\s]+$/, '')
            .trim();
    }

    function guidelineFormulaSummary(root) {
        if (!root) return '';
        var allCards = Array.from(page.querySelectorAll('[data-formula-summary-card]'));
        if (!allCards.length) return '';
        var categoryNode = root.matches && root.matches('[data-formula-summary-category]')
            ? root
            : (root.closest ? root.closest('[data-formula-summary-category]') : null);
        if (!categoryNode && root !== page && root.querySelector) {
            categoryNode = root.querySelector('[data-formula-summary-category]');
        }
        var categoryKey = categoryNode ? (categoryNode.getAttribute('data-formula-summary-category') || '') : '';
        var categoryName = categoryNode ? (categoryNode.getAttribute('data-formula-summary-category-name') || '') : '';
        var officialCards = allCards.filter(function(card) {
            return card.getAttribute('data-guideline-formula') === '1'
                && (!categoryKey || card.getAttribute('data-formula-summary-category') === categoryKey);
        });
        if (!categoryKey && root !== page) {
            var cardsInsideRoot = officialCards.filter(function(card) { return root.contains(card); });
            if (!cardsInsideRoot.length) return '';
            officialCards = cardsInsideRoot;
        }
        if (categoryKey && !officialCards.length) {
            return isSpanish
                ? 'Resumen de diferencias decisivas de la categoría ' + categoryName
                    + '. Esta categoría no contiene fórmulas incluidas en el guideline vigente.'
                : 'Summary of decisive differences for the category ' + categoryName
                    + '. This category has no formulas included in the current guideline.';
        }
        if (!officialCards.length) return '';
        var groups = [];
        officialCards.forEach(function(card) {
            var key = card.getAttribute('data-formula-summary-category') || 'category';
            var group = groups.find(function(item) { return item.key === key; });
            if (!group) {
                group = {
                    key: key,
                    name: card.getAttribute('data-formula-summary-category-name') || categoryName,
                    cards: []
                };
                groups.push(group);
            }
            group.cards.push(card);
        });
        var narration = [];
        groups.forEach(function(group) {
            narration.push(isSpanish
                ? 'Resumen de diferencias decisivas de la categoría ' + group.name
                    + '. Se mencionan solamente las fórmulas incluidas en el guideline vigente.'
                : 'Summary of decisive differences for the category ' + group.name
                    + '. Only formulas included in the current guideline are mentioned.');
            group.cards.forEach(function(card) {
                var name = card.getAttribute('data-formula-name') || '';
                var englishName = card.getAttribute('data-formula-english-name') || '';
                var pattern = formulaSummaryField(
                    card.querySelector('.formula-study-guide__formula-pattern'),
                    /^\s*(?:Patrón|Pattern)\s*:\s*/i
                );
                var facts = card.querySelectorAll('.formula-study-guide__fact');
                var action = formulaSummaryField(facts[0] ? facts[0].querySelector('span') : null);
                var clue = formulaSummaryField(facts[1] ? facts[1].querySelector('span') : null);
                var spokenName = name + (isSpanish && englishName ? ' ([[en:' + englishName + ']])' : (englishName ? ' (' + englishName + ')' : ''));
                narration.push(isSpanish
                    ? 'La fórmula ' + spokenName + ' trata ' + pattern + '. Su acción principal es ' + action
                        + '. Se distingue por ' + clue + '.'
                    : 'The formula ' + spokenName + ' treats ' + pattern + '. Its main action is ' + action
                        + '. Its decisive features are ' + clue + '.');
            });
        });
        return narration.join(' ');
    }

    function readerText(root) {
        if (explanationModeSelect && explanationModeSelect.value === 'summary') {
            var summary = guidelineFormulaSummary(root);
            if (summary) return summary;
        }
        return extractText(root);
    }

    function pronounceQiForSpeech(value) {
        return String(value || '').replace(/(^|[^A-Za-zÀ-ž])Q[iīíǐì](?=$|[^A-Za-zÀ-ž])/gi, function(match, prefix) {
            return prefix + 'Chi';
        });
    }

    function normalizeSpeechText(value) {
        var speechText = String(value || '').normalize('NFC');
        if (isSpanish) {
            var pinyinPronunciations = [
                ['Ai Ye', 'Ài Yè', 'Ái Yé'],
                ['Bai Shao', 'Bái Sháo', 'Bái Shao'],
                ['Bai Zhu', 'Bái Zhú', 'Bái Yú'],
                ['Ban Xia', 'Bàn Xià', 'Ban Shiá'],
                ['Bie Jia', 'Biē Jiǎ', 'Bié Yiá'],
                ['Cang Er Zi', 'Cāng Ěr Zǐ', 'Tsang Er Tzí'],
                ['Chai Hu', 'Chái Hú', 'Chái Jú'],
                ['Chen Pi', 'Chén Pí', 'Chen Pí'],
                ['Chuan Xiong', 'Chuān Xiōng', 'Chuán Shióng'],
                ['Da Huang', 'Dà Huáng', 'Dá Juáng'],
                ['Da Zao', 'Dà Zǎo', 'Dá Tsáo'],
                ['Dan Dou Chi', 'Dàn Dòu Chǐ', 'Dan Dou Chi'],
                ['Dang Gui', 'Dāng Guī', 'Dang Qui'],
                ['Di Gu Pi', 'Dì Gǔ Pí', 'Dí Gú Pí'],
                ['Di Yu', 'Dì Yú', 'Dí Yú'],
                ['Du Zhong', 'Dù Zhòng', 'Dú Yong'],
                ['E Jiao', 'Ē Jiāo', 'E Chiáo'],
                ['E Zhu', 'É Zhú', 'É Yú'],
                ['Fang Feng', 'Fáng Fēng', 'Fang Feng'],
                ['Fu Ling', 'Fú Líng', 'Fú Líng'],
                ['Fu Zi', 'Fù Zǐ', 'Fú Tzí'],
                ['Gan Cao', 'Gān Cǎo', 'Gan Tsáo'],
                ['Gan Jiang', 'Gān Jiāng', 'Gan Chiang'],
                ['Ge Gen', 'Gé Gēn', 'Gue Guen'],
                ['Gou Teng', 'Gōu Téng', 'Gou Téng'],
                ['Gou Qi Zi', 'Gǒu Qǐ Zǐ', 'Gou Chí Tzí'],
                ['Gua Lou', 'Guā Lóu', 'Guá Lou'],
                ['Gui Ban', 'Guī Bǎn', 'Guéi Bán'],
                ['Gui Zhi', 'Guì Zhī', 'Guéi Yí'],
                ['Hai Jin Sha', 'Hǎi Jīn Shā', 'Jái Yin Sha'],
                ['Han Lian Cao', 'Hàn Lián Cǎo', 'Jan Lián Tsáo'],
                ['Hong Hua', 'Hóng Huā', 'Jong Juá'],
                ['Huai Mi', 'Huái Mǐ', 'Juái Mí'],
                ['Huang Bai', 'Huáng Bǎi', 'Juáng Bái'],
                ['Huang Lian', 'Huáng Lián', 'Juáng Lián'],
                ['Huang Qi', 'Huáng Qí', 'Juáng Chí'],
                ['Huang Qin', 'Huáng Qín', 'Juáng Chín'],
                ['Huo Xiang', 'Huò Xiāng', 'Juó Shiáng'],
                ['Jin Yin Hua', 'Jīn Yín Huā', 'Yin Yin Juá'],
                ['Jin Qian Cao', 'Jīn Qián Cǎo', 'Yin Chián Tsáo'],
                ['Jie Geng', 'Jié Gěng', 'Yié Gueng'],
                ['Jing Jie', 'Jīng Jiè', 'Ying Yie'],
                ['Ju Hua', 'Jú Huā', 'Yú Juá'],
                ['Kuan Dong Hua', 'Kuǎn Dōng Huā', 'Kuán Dong Juá'],
                ['Lian Qiao', 'Lián Qiào', 'Lián Chiao'],
                ['Long Gu', 'Lóng Gǔ', 'Long Gú'],
                ['Ma Huang', 'Má Huáng', 'Má Juáng'],
                ['Mai Men Dong', 'Mài Mén Dōng', 'Mai Mén Dong'],
                ['Mang Xiao', 'Máng Xiāo', 'Máng Shiao'],
                ['Mo Yao', 'Mò Yào', 'Mó Yáo'],
                ['Mu Li', 'Mǔ Lì', 'Mú Lí'],
                ['Niu Bang Zi', 'Niú Bàng Zǐ', 'Niú Bang Tzí'],
                ['Nu Zhen Zi', 'Nǚ Zhēn Zǐ', 'Nú Yen Tzí'],
                ['Pei Lan', 'Pèi Lán', 'Pei Lán'],
                ['Qing Hao', 'Qīng Hāo', 'Ching Jáo'],
                ['Ren Shen', 'Rén Shēn', 'Ren Shen'],
                ['Rou Cong Rong', 'Ròu Cōng Róng', 'Rou Tsong Rong'],
                ['Rou Gui', 'Ròu Guì', 'Rou Guéi'],
                ['Ru Xiang', 'Rǔ Xiāng', 'Rú Shiáng'],
                ['San Leng', 'Sān Léng', 'San Léng'],
                ['Sang Bai Pi', 'Sāng Bái Pí', 'Sang Bái Pí'],
                ['Sang Ye', 'Sāng Yè', 'Sang Yé'],
                ['Sha Shen', 'Shā Shēn', 'Sha Shen'],
                ['Sheng Jiang', 'Shēng Jiāng', 'Sheng Chiang'],
                ['Sheng Ma', 'Shēng Má', 'Sheng Má'],
                ['Shi Gao', 'Shí Gāo', 'Shí Gao'],
                ['Tao Ren', 'Táo Rén', 'Tao Ren'],
                ['Tian Ma', 'Tiān Má', 'Tián Má'],
                ['Ting Li Zi', 'Tíng Lì Zǐ', 'Ting Lí Tzí'],
                ['Xing Ren', 'Xìng Rén', 'Xing Ren'],
                ['Xiang Fu', 'Xiāng Fù', 'Shiáng Fú'],
                ['Xie Bai', 'Xiè Bái', 'Shié Bái'],
                ['Xin Yi Hua', 'Xīn Yí Huā', 'Shin Yí Juá'],
                ['Xu Duan', 'Xù Duàn', 'Shú Duán'],
                ['Zhi Mu', 'Zhī Mǔ', 'Yí Mú'],
                ['Zhi Zi', 'Zhī Zǐ', 'Yí Tzí'],
                ['Zi Wan', 'Zǐ Wǎn', 'Tzí Uán']
            ];
            pinyinPronunciations.forEach(function(pronunciation) {
                [pronunciation[1], pronunciation[0]].forEach(function(writtenName) {
                    var escapedName = writtenName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    var namePattern = new RegExp('(^|[^A-Za-zÀ-ž])' + escapedName + '(?=$|[^A-Za-zÀ-ž])', 'gi');
                    speechText = speechText.replace(namePattern, function(match, prefix) {
                        return prefix + pronunciation[2];
                    });
                });
            });
            if (/(?:Appendix|[Aa]péndice) B\b/.test(speechText)
                    && !/(?:Appendix|[Aa]péndice) B[^.]{0,180}listado oficial de parejas/i.test(speechText)) {
                speechText = speechText.replace(/(?:Appendix|[Aa]péndice) B\b/,
                    'apéndice B, que es el listado oficial de parejas de hierbas conocidas como Duì Yào');
            }
            if (/(?:Appendix|[Aa]péndice) C\b/.test(speechText)
                    && !/(?:Appendix|[Aa]péndice) C[^.]{0,180}listado oficial de fórmulas/i.test(speechText)) {
                speechText = speechText.replace(/(?:Appendix|[Aa]péndice) C\b/,
                    'apéndice C, que es el listado oficial de fórmulas del contenido vigente');
            }
            if (/(?:Appendix|[Aa]péndice) D\b/.test(speechText)
                    && !/(?:Appendix|[Aa]péndice) D[^.]{0,180}listado de sustancias obsoletas/i.test(speechText)) {
                speechText = speechText.replace(/(?:Appendix|[Aa]péndice) D\b/,
                    'apéndice D, que es el listado de sustancias obsoletas');
            }
        } else {
            if (/Appendix B\b/.test(speechText) && !/Appendix B[^.]{0,180}official list of herb pairs/i.test(speechText)) {
                speechText = speechText.replace(/Appendix B\b/,
                    'Appendix B, the guideline official list of herb pairs known as Duì Yào');
            }
            if (/Appendix C\b/.test(speechText) && !/Appendix C[^.]{0,180}official list of formulas/i.test(speechText)) {
                speechText = speechText.replace(/Appendix C\b/,
                    'Appendix C, the guideline official list of current formulas');
            }
            if (/Appendix D\b/.test(speechText) && !/Appendix D[^.]{0,180}list of obsolete substances/i.test(speechText)) {
                speechText = speechText.replace(/Appendix D\b/,
                    'Appendix D, the guideline list of obsolete substances');
            }
        }
        return pronounceQiForSpeech(speechText
            .replace(/[*＊]+/g, '')
            .replace(/[\u3400-\u9fff\uf900-\ufaff]/g, ' ')
            .replace(/[→⇒➜➝]/g, isSpanish ? '. Luego, ' : '. Then, ')
            .replace(/[←↶↷]/g, ' ')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*[–—-]\s*(\d+(?:[.,]\d+)?)\s*mg\b/gi, isSpanish ? '$1 a $2 miligramos' : '$1 to $2 milligrams')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*[–—-]\s*(\d+(?:[.,]\d+)?)\s*g\b/gi, isSpanish ? '$1 a $2 gramos' : '$1 to $2 grams')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*mg\b/gi, isSpanish ? '$1 miligramos' : '$1 milligrams')
            .replace(/\b(\d+(?:[.,]\d+)?)\s*g\b/gi, isSpanish ? '$1 gramos' : '$1 grams')
            .replace(/[•·|]+/g, '. ')
            .replace(/\s*\/\s*/g, isSpanish ? ' o ' : ' or ')
            .replace(/\s*;\s*/g, '. ')
            .replace(/\s+/g, ' ')
            .replace(/\s+([,.!?])/g, '$1')
            .replace(/([.!?])\s*([.!?])+/g, '$1')
            .trim());
    }

    function normalizeEnglishFormulaText(value) {
        return pronounceQiForSpeech(String(value || '').normalize('NFC')
            .replace(/[*＊]+/g, '')
            .replace(/[\u3400-\u9fff\uf900-\ufaff]/g, ' ')
            .replace(/\s+/g, ' ')
            .replace(/\s+([,.!?])/g, '$1')
            .trim());
    }

    function appendSpeechChunks(chunks, value, chunkLanguage, englishFormula) {
        var cleaned = englishFormula ? normalizeEnglishFormulaText(value) : normalizeSpeechText(value);
        var sentences = cleaned.match(/[^.!?]+[.!?]+|[^.!?]+$/g) || [];
        sentences.forEach(function(sentence) {
            sentence = sentence.trim();
            if (!sentence || !/[A-Za-zÀ-ž0-9]/.test(sentence)) return;
            if (sentence.length <= 280) {
                chunks.push({text: sentence, lang: chunkLanguage});
                return;
            }
            var parts = sentence.split(/,\s+/);
            var current = '';
            parts.forEach(function(part) {
                var candidate = current ? current + ', ' + part : part;
                if (candidate.length > 280 && current) {
                    chunks.push({text: current + '.', lang: chunkLanguage});
                    current = part;
                } else {
                    current = candidate;
                }
            });
            if (current) chunks.push({text: current, lang: chunkLanguage});
        });
    }

    function splitSpeechText(text) {
        var chunks = [];
        var marker = /\(\[\[en:([^\]]+)\]\]\)/g;
        var cursor = 0;
        var match;
        while ((match = marker.exec(String(text || ''))) !== null) {
            appendSpeechChunks(chunks, String(text).slice(cursor, match.index), language, false);
            appendSpeechChunks(chunks, match[1], 'en-US', true);
            cursor = marker.lastIndex;
        }
        appendSpeechChunks(chunks, String(text || '').slice(cursor), language, false);
        return chunks;
    }

    function buildSections() {
        sectionSelect.innerHTML = '';
        sections = [];
        page.querySelectorAll('h3,h4').forEach(function(heading) {
            if (heading.closest('[data-formula-read-aloud]')) return;
            if (heading.closest('[data-formula-read-section-ignore]')) return;
            var label = (heading.textContent || '').replace(/\s+/g, ' ').trim();
            if (!label) return;
            var root = heading.closest('section,article,details,.formula-study-guide__overview,.formula-study-guide__category-header,.formula-study-guide__formula') || heading.parentElement;
            if (!root || sections.some(function(item) { return item.root === root; })) return;
            sections.push({label: label, root: root});
        });
        if (!sections.length) sections.push({label: isSpanish ? 'Contenido de la página' : 'Page content', root: page});
        sections.forEach(function(section, index) {
            var option = document.createElement('option');
            option.value = String(index);
            option.textContent = section.label;
            sectionSelect.appendChild(option);
        });
    }

    function revealSelectedSection(section) {
        if (!section) return;
        var parentDetails = section.root.closest('details');
        if (parentDetails) parentDetails.open = true;
        window.requestAnimationFrame(function() {
            var readerHeight = reader.getBoundingClientRect().height;
            var targetTop = section.root.getBoundingClientRect().top + window.scrollY - readerHeight - 12;
            window.scrollTo({top: Math.max(0, targetTop), behavior: 'smooth'});
        });
    }

    function voiceProvider(voice) {
        var name = (String((voice && voice.name) || '') + ' ' + String((voice && voice.voiceURI) || '')).toLowerCase();
        var providers = ['google', 'microsoft', 'apple', 'amazon', 'samsung'];
        return providers.find(function(provider) { return name.indexOf(provider) !== -1; }) || '';
    }

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

    function mobileVoiceScore(voice, targetLanguage) {
        var identity = (String((voice && voice.name) || '') + ' ' + String((voice && voice.voiceURI) || '')).toLowerCase();
        var target = String(targetLanguage || '').toLowerCase();
        var voiceLanguage = String((voice && voice.lang) || '').toLowerCase();
        var score = voiceLanguage === target ? 35 : 15;
        if (/natural|neural|premium|enhanced|studio|online/.test(identity)) score += 100;
        if (/google|microsoft|samsung|amazon/.test(identity)) score += 70;
        if (/mónica|monica|paulina|dalia|jorge|helena|lucía|lucia|isabela|sofía|sofia|carlos|samantha|daniel|ava|aria|jenny|sonia|ryan|emma|natasha/.test(identity)) score += 60;
        if (voice && voice.default) score += 20;
        return score;
    }

    function educationalVoicesForLanguage(targetLanguage) {
        var prefix = String(targetLanguage || '').slice(0, 2).toLowerCase();
        var voices = window.speechSynthesis.getVoices().filter(function(voice) {
            return (voice.lang || '').slice(0, 2).toLowerCase() === prefix && isEducationalVoice(voice);
        });
        if (!mobileVoiceMenu) return voices;
        return voices.filter(isProfessionalMobileVoice).sort(function(first, second) {
            return mobileVoiceScore(second, targetLanguage) - mobileVoiceScore(first, targetLanguage)
                || first.name.localeCompare(second.name);
        }).slice(0, 6);
    }

    function bestNaturalVoice(voices, targetLanguage, referenceVoice) {
        var target = String(targetLanguage || '').toLowerCase();
        var targetPrefix = target.slice(0, 2);
        var referenceProvider = voiceProvider(referenceVoice);
        var naturalNames = /natural|neural|premium|enhanced|studio|online|google|aria|ava|jenny|sonia|ryan|emma|guy|samantha|daniel|natasha|mónica|monica|paulina|dalia|jorge|helena|lucía|lucia|isabela|sofía|sofia|carlos/i;
        var roboticNames = /espeak|compact|eloquence|festival|flite|david|zira|mark|hazel|desktop/i;
        return voices.slice().sort(function(first, second) {
            function score(voice) {
                var voiceLanguage = String(voice.lang || '').toLowerCase();
                var value = 0;
                if (voiceLanguage === target) value += 30;
                else if (voiceLanguage.slice(0, 2) === targetPrefix) value += 15;
                if (naturalNames.test(voice.name)) value += 90;
                if (referenceProvider && voiceProvider(voice) === referenceProvider) value += 70;
                if (voice.localService === false) value += 25;
                if (roboticNames.test(voice.name)) value -= 150;
                return value;
            }
            return score(second) - score(first);
        })[0] || null;
    }

    function preferredVoice(voices) {
        var stored = '';
        try { stored = window.localStorage.getItem('acupuncturetests-reader-voice-' + language.slice(0, 2).toLowerCase()) || ''; } catch (error) {}
        if (stored) {
            var saved = voices.find(function(voice) { return voice.name === stored; });
            if (saved) return saved;
        }
        return bestNaturalVoice(voices, language, null);
    }

    function voiceForLanguage(targetLanguage) {
        var prefix = String(targetLanguage || '').slice(0, 2).toLowerCase();
        if (prefix === 'en' && selectedEnglishVoice) return selectedEnglishVoice;
        var voices = educationalVoicesForLanguage(targetLanguage);
        return bestNaturalVoice(voices, targetLanguage, selectedVoice);
    }

    function loadEnglishVoices() {
        if (!englishVoiceSelect || !supported) return;
        var voices = educationalVoicesForLanguage('en-US');
        var stored = '';
        try { stored = window.localStorage.getItem('acupuncturetests-reader-voice-en-formulas') || ''; } catch (error) {}
        if (stored && !voices.some(function(voice) { return voice.name === stored; })) {
            try { window.localStorage.removeItem('acupuncturetests-reader-voice-en-formulas'); } catch (error) {}
            stored = '';
        }
        selectedEnglishVoice = voices.find(function(voice) { return voice.name === stored; })
            || bestNaturalVoice(voices, 'en-US', selectedVoice);
        englishVoiceSelect.innerHTML = '';
        var automatic = document.createElement('option');
        automatic.value = '';
        automatic.textContent = 'Automática — voz inglesa educativa similar a la española'
            + (selectedEnglishVoice ? ': ' + selectedEnglishVoice.name : '');
        automatic.selected = !stored;
        englishVoiceSelect.appendChild(automatic);
        voices.forEach(function(voice) {
            var option = document.createElement('option');
            option.value = voice.name;
            option.textContent = voice.name + ' (' + voice.lang + ')' + (voice.localService ? '' : ' · online');
            option.selected = stored === voice.name;
            englishVoiceSelect.appendChild(option);
        });
    }

    function loadVoices() {
        if (!supported) return;
        var voices = educationalVoicesForLanguage(language);
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
            if (selectedVoice && selectedVoice.name === voice.name) option.selected = true;
            voiceSelect.appendChild(option);
        });
        loadEnglishVoices();
    }

    function updateControls() {
        var active = speechChunks.length > 0;
        pauseButton.disabled = !active;
        stopButton.disabled = !active;
        backButton.disabled = !active || currentChunk <= 0;
        forwardButton.disabled = !active || currentChunk >= speechChunks.length - 1;
        progress.disabled = !active;
        progress.max = Math.max(0, speechChunks.length - 1);
        progress.value = Math.min(currentChunk, Math.max(0, speechChunks.length - 1));
        progressValue.textContent = active
            ? (isSpanish ? 'Fragmento ' : 'Fragment ') + (currentChunk + 1) + ' / ' + speechChunks.length
            : (isSpanish ? 'Sin lectura' : 'Not reading');
        pauseButton.textContent = paused
            ? (isSpanish ? '▶ Continuar' : '▶ Resume')
            : (isSpanish ? '⏸ Pausar' : '⏸ Pause');
    }

    function finishReading(message) {
        window.speechSynthesis.cancel();
        speechChunks = [];
        currentChunk = 0;
        paused = false;
        setStatus(message || (isSpanish ? 'Lectura terminada.' : 'Reading finished.'));
        updateControls();
    }

    function speakCurrent(activeSession) {
        if (activeSession !== sessionId || !speechChunks.length) return;
        if (currentChunk >= speechChunks.length) {
            finishReading();
            return;
        }
        var chunk = speechChunks[currentChunk];
        var chunkText = typeof chunk === 'string' ? chunk : chunk.text;
        var chunkLanguage = typeof chunk === 'string' ? language : (chunk.lang || language);
        var utterance = new SpeechSynthesisUtterance(chunkText);
        utterance.lang = chunkLanguage;
        utterance.rate = chunkLanguage.slice(0, 2).toLowerCase() === 'en' ? 0.96 : 1;
        var chunkVoice = chunkLanguage.slice(0, 2).toLowerCase() === language.slice(0, 2).toLowerCase()
            ? selectedVoice
            : voiceForLanguage(chunkLanguage);
        if (chunkVoice) utterance.voice = chunkVoice;
        utterance.onend = function() {
            if (activeSession !== sessionId || paused) return;
            currentChunk += 1;
            if (currentChunk >= speechChunks.length) {
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
        setStatus(isSpanish ? 'Leyendo…' : 'Reading…');
        updateControls();
        window.speechSynthesis.speak(utterance);
    }

    function startReading(text, label) {
        sessionId += 1;
        window.speechSynthesis.cancel();
        speechChunks = splitSpeechText(text);
        currentChunk = 0;
        paused = false;
        if (!speechChunks.length) {
            setStatus(isSpanish ? 'No hay texto disponible para leer.' : 'No text is available to read.');
            updateControls();
            return;
        }
        setStatus((isSpanish ? 'Preparando: ' : 'Preparing: ') + label + '.');
        updateControls();
        var activeSession = sessionId;
        window.setTimeout(function() { speakCurrent(activeSession); }, 80);
    }

    function selectedText() {
        return cachedSelection;
    }

    function structuredSelectionRoot(node) {
        var element = node && node.nodeType === 1 ? node : (node ? node.parentElement : null);
        if (!element || !element.closest) return null;
        return element.closest('[data-formula-dui-yao-card],[data-formula-comparison-table],[data-formula-context-table]');
    }

    function visibleText() {
        var seen = new Set();
        var pieces = [];
        page.querySelectorAll('h2,h3,h4,h5,p,li,dt,dd,summary,.formula-study-guide__fact,.formula-study-guide__category,[data-formula-comparison-table],[data-formula-context-table],[data-formula-dui-yao-card]').forEach(function(element) {
            if (element.closest('[data-formula-read-aloud],nav,form') || element.offsetParent === null) return;
            if (element.closest('.formula-study-guide__category') && !element.classList.contains('formula-study-guide__category')) return;
            if (element.closest('[data-formula-dui-yao-card]') && !element.hasAttribute('data-formula-dui-yao-card')) return;
            if (element.closest('[data-formula-context-table]') && !element.hasAttribute('data-formula-context-table')) return;
            var rect = element.getBoundingClientRect();
            if (rect.bottom < 0 || rect.top > window.innerHeight) return;
            var text = extractText(element);
            if (text && !seen.has(text)) {
                seen.add(text);
                pieces.push(text);
            }
        });
        return pieces.join(' ');
    }

    function seekTo(index) {
        if (!speechChunks.length) return;
        sessionId += 1;
        window.speechSynthesis.cancel();
        paused = false;
        currentChunk = Math.max(0, Math.min(index, speechChunks.length - 1));
        updateControls();
        var activeSession = sessionId;
        window.setTimeout(function() { speakCurrent(activeSession); }, 80);
    }

    if (!supported) {
        reader.hidden = false;
        setStatus(isSpanish ? 'Este navegador no ofrece lectura en voz alta.' : 'This browser does not provide read aloud.');
        reader.querySelectorAll('button,select,input').forEach(function(control) { control.disabled = true; });
        return;
    }
    reader.hidden = false;
    buildSections();
    loadVoices();
    window.speechSynthesis.onvoiceschanged = loadVoices;
    document.addEventListener('selectionchange', function() {
        var selection = window.getSelection();
        if (!selection || selection.isCollapsed || !selection.rangeCount) return;
        var range = selection.getRangeAt(0);
        if (!page.contains(range.commonAncestorContainer) || reader.contains(range.commonAncestorContainer)) return;
        var startRoot = structuredSelectionRoot(range.startContainer);
        var endRoot = structuredSelectionRoot(range.endContainer);
        var text = '';
        if (startRoot && startRoot === endRoot) {
            text = extractText(startRoot);
        } else {
            var wrapper = document.createElement('div');
            wrapper.appendChild(range.cloneContents());
            text = extractText(wrapper);
        }
        if (text) cachedSelection = text;
    });
    voiceSelect.addEventListener('change', function() {
        var voices = educationalVoicesForLanguage(language);
        selectedVoice = voices.find(function(voice) { return voice.name === voiceSelect.value; }) || preferredVoice(voices);
        try { window.localStorage.setItem('acupuncturetests-reader-voice-' + language.slice(0, 2).toLowerCase(), voiceSelect.value); } catch (error) {}
        loadEnglishVoices();
    });
    if (englishVoiceSelect) englishVoiceSelect.addEventListener('change', function() {
        var voices = educationalVoicesForLanguage('en-US');
        selectedEnglishVoice = voices.find(function(voice) { return voice.name === englishVoiceSelect.value; })
            || bestNaturalVoice(voices, 'en-US', selectedVoice);
        try {
            if (englishVoiceSelect.value) {
                window.localStorage.setItem('acupuncturetests-reader-voice-en-formulas', englishVoiceSelect.value);
            } else {
                window.localStorage.removeItem('acupuncturetests-reader-voice-en-formulas');
            }
        } catch (error) {}
    });
    if (explanationModeSelect) explanationModeSelect.addEventListener('change', function() {
        try {
            window.localStorage.setItem('acupuncturetests-formula-reader-mode', explanationModeSelect.value);
        } catch (error) {}
    });
    sectionSelect.addEventListener('change', function() {
        var section = sections[Number(sectionSelect.value || 0)];
        if (!section) return;
        revealSelectedSection(section);
    });
    fullButton.addEventListener('click', function() {
        startReading(readerText(page), isSpanish ? 'página completa' : 'full page');
    });
    sectionButton.addEventListener('click', function() {
        var section = sections[Number(sectionSelect.value || 0)];
        if (!section) return;
        revealSelectedSection(section);
        startReading(readerText(section.root), section.label);
    });
    selectionButton.addEventListener('click', function() {
        startReading(selectedText(), isSpanish ? 'texto seleccionado' : 'selected text');
    });
    visibleButton.addEventListener('click', function() {
        startReading(visibleText(), isSpanish ? 'texto visible' : 'visible text');
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
.formula-study-guide{max-width:1160px;margin:24px auto 58px;padding:0 16px;color:#17232a}
.formula-study-guide :is(a,button,summary):focus-visible{outline:3px solid #185cca;outline-offset:3px}
.formula-study-guide__top{display:flex;align-items:flex-start;justify-content:space-between;gap:22px;margin-bottom:22px}
.formula-study-guide__eyebrow{margin:0 0 6px;color:#08746f;font-size:13px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.formula-study-guide h2{margin:0 0 9px;font-size:clamp(29px,4vw,42px);line-height:1.12}
.formula-study-guide__lead{max-width:760px;margin:0;color:#5b6b79;font-size:16px;line-height:1.6}
.formula-study-guide__marker-note{margin:7px 0 0;color:#075e54;font-size:13px;font-weight:800}
.formula-study-guide__actions{display:flex;flex-direction:column;gap:9px;align-items:flex-end}
.formula-study-guide__module-switcher,.formula-study-guide__utility-actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}
.formula-study-guide__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:9px 14px;border:1px solid #cbd5dc;border-radius:8px;background:#fff;color:#334155;font-weight:800;text-decoration:none;white-space:nowrap}
.formula-study-guide__button:hover{background:#f1f5f9;color:#17232a;text-decoration:none}
.formula-study-guide__button--primary{border-color:#2563eb;background:#2563eb;color:#fff}.formula-study-guide__button--primary:hover{background:#1d4ed8;color:#fff}
.formula-study-guide__button--active{border-color:#0b817d;background:#0b817d;color:#fff}.formula-study-guide__button--active:hover{background:#0b817d;color:#fff}
.formula-study-guide__study-route{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 0 18px;padding:11px 14px;border:1px solid #d9e2e7;border-radius:9px;background:#fff;color:#64748b;font-size:12px;font-weight:750}.formula-study-guide__study-route a{color:#315fa5;text-decoration:none}.formula-study-guide__study-route a:hover{text-decoration:underline}.formula-study-guide__study-route-current{color:#17232a}
.formula-study-reader{position:sticky;top:70px;z-index:1020;margin:0 0 18px;border:1px solid #9ed3ce;border-left:5px solid #0b8f87;border-radius:12px;background:#f4fbfa;box-shadow:0 4px 14px rgba(15,23,42,.08)}
.formula-study-reader[hidden]{display:none}.formula-study-reader__disclosure>summary{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:12px 15px;cursor:pointer;list-style:none}.formula-study-reader__disclosure>summary::-webkit-details-marker{display:none}.formula-study-reader__disclosure>summary:after{display:flex;width:28px;height:28px;flex:0 0 28px;align-items:center;justify-content:center;border:1px solid #85c8c1;border-radius:50%;background:#fff;color:#08746f;content:'+';font-size:20px;font-weight:800}.formula-study-reader__disclosure[open]>summary:after{content:'−'}
.formula-study-reader__title{display:block;color:#163247;font-size:19px;font-weight:900}.formula-study-reader__description{display:block;margin-top:2px;color:#607487;font-size:12px;font-weight:500}.formula-study-reader__panel{padding:0 15px 13px}.formula-study-reader__fields{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:9px}.formula-study-reader__fields--bilingual{grid-template-columns:minmax(0,1fr) minmax(0,1fr) minmax(0,1fr)}.formula-study-reader__mode{grid-column:1/-1}.formula-study-reader__mode small{display:block;margin-top:4px;color:#52677a;font-size:11px;line-height:1.35}.formula-study-reader label>span{display:block;margin:0 0 4px;color:#26445a;font-size:12px;font-weight:800}.formula-study-reader select{width:100%;min-height:40px;padding:7px 32px 7px 10px;border:1px solid #a9c7d2;border-radius:8px;background:#fff;color:#243b53;font-size:13px}
.formula-study-reader__actions{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:8px;margin-top:9px}.formula-study-reader button{min-height:40px;padding:7px 9px;border:1px solid #0b8f87;border-radius:8px;background:#fff;color:#08746f;font-size:13px;font-weight:850;line-height:1.2}.formula-study-reader button:hover:not(:disabled){background:#e7f7f3}.formula-study-reader__actions button:first-child,.formula-study-reader__actions button:nth-child(2){background:#0b8f87;color:#fff}.formula-study-reader__actions button:first-child:hover:not(:disabled),.formula-study-reader__actions button:nth-child(2):hover:not(:disabled){border-color:#076d68;background:#076d68;color:#fff}.formula-study-reader button:disabled{cursor:not-allowed;opacity:.48}
.formula-study-reader__progress{display:grid;grid-template-columns:auto minmax(160px,1fr) auto;gap:10px;align-items:end;margin-top:10px}.formula-study-reader__progress button{min-width:104px}.formula-study-reader__range-heading{display:flex;align-items:center;justify-content:space-between;gap:8px}.formula-study-reader__range input{width:100%;margin:4px 0 0;accent-color:#0b8f87}.formula-study-reader__status{margin:8px 0 0;color:#607487;font-size:12px;font-weight:700}
.formula-study-guide__chapter-path{display:grid;grid-template-columns:minmax(0,1fr) minmax(310px,1.15fr) minmax(0,1fr);gap:12px;align-items:stretch;margin:0 0 20px;padding:13px;border:1px solid #d5e0e5;border-radius:12px;background:#fff;box-shadow:0 4px 14px rgba(15,23,42,.06)}
.formula-study-guide__chapter-path--index{grid-template-columns:minmax(300px,640px);justify-content:center}.formula-study-guide__chapter-path-link{display:flex;min-height:68px;flex-direction:column;justify-content:center;padding:10px 13px;border:1px solid #dbe4e9;border-radius:9px;background:#fff;color:#17344a;text-decoration:none}.formula-study-guide__chapter-path-link:hover{border-color:#f77e25;color:#17344a;text-decoration:none}.formula-study-guide__chapter-path-link span{color:#64748b;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.formula-study-guide__chapter-path-link strong{margin-top:4px;color:#e66716;font-size:13px;line-height:1.35}.formula-study-guide__chapter-path-link--next{text-align:right}.formula-study-guide__chapter-path-link--disabled{background:#f8fafb;opacity:.72}.formula-study-guide__chapter-path-link--disabled strong{color:#64748b}
.formula-study-guide__chapter-jump{display:flex;flex-direction:column;justify-content:center;gap:5px}.formula-study-guide__chapter-jump form>label{display:block;margin:0 0 4px;color:#64748b;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.formula-study-guide__chapter-jump form>div{display:flex;gap:7px}.formula-study-guide__chapter-jump select{width:100%;min-width:0;min-height:42px;padding:8px 34px 8px 10px;border:1px solid #aebdc7;border-radius:8px;background:#fff;color:#17232a;font-weight:750}.formula-study-guide__chapter-jump>a{color:#075e54;font-size:12px;font-weight:800;text-align:center;text-decoration:none}.formula-study-guide__chapter-jump>a:hover{text-decoration:underline}
.formula-study-guide__overview{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:24px;align-items:center;margin-bottom:24px;padding:23px 26px;border-left:5px solid #0b817d;border-radius:12px;background:#eef8f6;box-shadow:0 3px 12px rgba(15,23,42,.05)}
.formula-study-guide__overview h3{margin:0 0 7px;font-size:22px}.formula-study-guide__overview p{margin:0;color:#52677a;line-height:1.55}
.formula-study-guide__stats{display:flex;gap:10px}.formula-study-guide__stat{min-width:92px;padding:13px 15px;border-radius:10px;background:#fff;color:#0f5f58;text-align:center}.formula-study-guide__stat strong{display:block;font-size:28px;line-height:1}.formula-study-guide__stat span{display:block;margin-top:5px;font-size:11px;font-weight:800;text-transform:uppercase}
.formula-study-guide__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:14px}
.formula-study-guide__category{display:flex;min-height:176px;flex-direction:column;padding:18px;border:1px solid #d9e2e7;border-top:4px solid #f77e25;border-radius:11px;background:#fff;color:inherit;text-decoration:none;box-shadow:0 2px 8px rgba(15,23,42,.05);transition:transform .15s ease,box-shadow .15s ease}
.formula-study-guide__category:hover{color:inherit;text-decoration:none;transform:translateY(-2px);box-shadow:0 7px 18px rgba(15,23,42,.1)}
.formula-study-guide__category-number{display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;margin-bottom:10px;border-radius:50%;background:#e9f6f3;color:#0b6b63;font-weight:900}
.formula-study-guide__category strong{font-size:18px;line-height:1.3}.formula-study-guide__category p{margin:7px 0 12px;color:#5b6b79;font-size:14px;line-height:1.45}.formula-study-guide__category small{margin-top:auto;color:#075e54;font-weight:800}
.formula-study-guide__category-meta{display:block;margin-top:5px;color:#64748b;font-size:12px;font-weight:700}
.formula-study-guide__category--board-priority{border-top-color:#0b817d;background:linear-gradient(155deg,#f4fbf9 0,#fff 48%)}.formula-study-guide__chapter-merge{display:block;margin:2px 0 12px;padding:10px 11px;border:1px solid #b9ddd5;border-radius:8px;background:#eef8f6}.formula-study-guide__chapter-merge b{display:block;color:#075e54;font-size:13px}.formula-study-guide__chapter-merge span{display:block;margin-top:3px;color:#52677a;font-size:12px;font-weight:700;line-height:1.4}
.formula-study-guide__category-reference{display:block;margin:0 0 12px;padding:8px 9px;border-left:3px solid #2563eb;border-radius:5px;background:#eff6ff;color:#234b7b;font-size:12px;font-weight:750;line-height:1.4}.formula-study-guide__category-header .formula-study-guide__category-reference{max-width:840px;margin:12px 0 0}
.formula-study-guide__supplement{margin-top:26px;padding:22px 24px;border:1px solid #c9ded9;border-top:4px solid #0b817d;border-radius:12px;background:#f2faf8}.formula-study-guide__supplement-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:15px}.formula-study-guide__supplement-heading h3{margin:2px 0 5px;font-size:22px}.formula-study-guide__supplement-heading p{max-width:780px;margin:0;color:#52677a;line-height:1.5}.formula-study-guide__supplement-badge{padding:6px 10px;border-radius:999px;background:#d9f1eb;color:#08695f;font-size:11px;font-weight:900;text-transform:uppercase;white-space:nowrap}.formula-study-guide__category--supplemental{min-height:156px;border-top-color:#0b817d}.formula-study-guide__category--supplemental .formula-study-guide__category-number{width:auto;padding:0 10px;border-radius:999px}.formula-study-guide__category--supplemental small{color:#075fc8}
.formula-study-guide__category-header{margin-bottom:18px;padding:22px 24px;border-top:5px solid #f77e25;border-radius:12px;background:linear-gradient(135deg,#fffaf5,#f7fbfb)}
.formula-study-guide__category-header--supplemental{border-top-color:#0b817d;background:linear-gradient(135deg,#effaf7,#fffaf5)}
.formula-study-guide__category-header h3{margin:4px 0 8px;font-size:clamp(25px,3vw,34px)}.formula-study-guide__category-header p{max-width:800px;margin:0;color:#52677a;line-height:1.55}
.formula-study-guide__board-status{display:inline-flex;width:max-content;margin:9px 0 2px;padding:5px 9px;border:1px solid #c4d7f2;border-radius:999px;background:#eef4ff;color:#214f91;font-size:11px;font-weight:900;letter-spacing:.035em;text-transform:uppercase}
.formula-study-guide__board-status--none{border-color:#edc989;background:#fff4df;color:#8a3f08}.formula-study-guide__board-status--all{border-color:#add8cf;background:#e7f7f3;color:#075e54}
.formula-study-guide__category-header .formula-study-guide__board-status{margin:0 0 11px;padding:6px 10px;font-size:12px}
.formula-study-guide__chapter-shortcut{margin:0 0 18px;padding:18px 20px;border:1px solid #add8cf;border-left:5px solid #0b817d;border-radius:11px;background:#effaf7}.formula-study-guide__chapter-shortcut h4{margin:0 0 5px;font-size:20px}.formula-study-guide__chapter-shortcut>p{margin:0 0 14px;color:#496474;line-height:1.5}.formula-study-guide__chapter-shortcut-links{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px}.formula-study-guide__chapter-shortcut-link{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 12px;border:1px solid #c7e3dd;border-radius:8px;background:#fff;color:#075e54;font-weight:800;text-decoration:none}.formula-study-guide__chapter-shortcut-link:hover{background:#e7f7f3;color:#064e47;text-decoration:none}.formula-study-guide__chapter-shortcut-link span{color:#64748b;font-size:12px;white-space:nowrap}
.formula-study-guide__chapter-shortcut-action{margin:0 0 14px}
.formula-study-guide__board-verification{display:flex;align-items:center;justify-content:space-between;gap:18px;margin:0 0 18px;padding:16px 18px;border:1px solid #b9ddd5;border-left:5px solid #0b817d;border-radius:10px;background:#effaf7}.formula-study-guide__board-verification h4{margin:0 0 4px;font-size:17px}.formula-study-guide__board-verification p{margin:0;color:#496474;font-size:13px;line-height:1.5}.formula-study-guide__board-verification .formula-study-guide__button{flex:0 0 auto}
.formula-study-guide__scope-analysis{margin:0 0 20px;border:1px solid #edc989;border-left:5px solid #d97706;border-radius:11px;background:#fffaf0;overflow:hidden}.formula-study-guide__scope-analysis-header{padding:18px 20px}.formula-study-guide__scope-analysis-header h4{margin:0 0 7px;font-size:20px}.formula-study-guide__scope-analysis-header p{margin:0;color:#68543a;line-height:1.55}.formula-study-guide__scope-analysis-header p+p{margin-top:8px}.formula-study-guide__scope-analysis-caveat{font-size:13px}.formula-study-guide__scope-analysis-board-note{border-top:1px solid #edc989;border-bottom:1px solid #edc989;background:#fff4df}.formula-study-guide__scope-analysis .formula-study-guide__table-wrap{background:#fff}.formula-study-guide__scope-analysis .formula-study-guide__table td:first-child{white-space:normal}.formula-study-guide__scope-analysis-count{display:inline-flex;align-items:center;margin-right:6px;padding:3px 7px;border-radius:999px;background:#ffedd5;color:#9a3412;font-size:11px;font-weight:900;white-space:nowrap}
.formula-study-guide__medical-rationale{margin:0 0 20px;border:1px solid #c4d7f2;border-left:5px solid #315fa5;border-radius:11px;background:#f5f8ff;overflow:hidden}.formula-study-guide__medical-rationale-header{padding:18px 20px}.formula-study-guide__medical-rationale-header h4{margin:0 0 7px;color:#173f78;font-size:20px}.formula-study-guide__medical-rationale-header p{margin:0;color:#435d7a;line-height:1.55}.formula-study-guide__medical-rationale-header p+p{margin-top:8px}.formula-study-guide__medical-rationale-stat{display:inline-flex;margin:0 0 9px;padding:4px 9px;border-radius:999px;background:#dce9ff;color:#214f91;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.03em}.formula-study-guide__medical-rationale .formula-study-guide__table-wrap{background:#fff}.formula-study-guide__medical-rationale .formula-study-guide__table td:first-child{white-space:normal}.formula-study-guide__medical-rationale-board{color:#173f78;font-weight:800}.formula-study-guide__medical-rationale-secondary{border-top:1px solid #c4d7f2;background:#eef4ff}.formula-study-guide__medical-rationale-caveat{font-size:13px}
.formula-study-guide__chapter-shortcut-actions{display:flex;gap:9px;flex-wrap:wrap}
.formula-study-guide__board-roster{margin:16px 0;padding:15px;border:1px solid #b9ddd5;border-radius:9px;background:#fff}.formula-study-guide__board-roster h5{margin:0 0 4px;font-size:16px}.formula-study-guide__board-roster>p{margin:0 0 12px;color:#496474;font-size:13px}.formula-study-guide__board-roster-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.formula-study-guide__board-roster-group{padding:11px 12px;border:1px solid #d8e8e4;border-radius:8px;background:#f8fbfa}.formula-study-guide__board-roster-group h6{margin:0 0 7px;color:#075e54;font-size:13px}.formula-study-guide__board-roster-group ul{margin:0;padding:0;list-style:none}.formula-study-guide__board-roster-group li+li{margin-top:7px}.formula-study-guide__board-roster-formula{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}.formula-study-guide__board-roster-formula a{color:#172033;font-weight:800;text-decoration:none}.formula-study-guide__board-roster-formula a:hover{color:#075e54;text-decoration:underline}.formula-study-guide__board-roster-meta{display:block;margin-top:2px;color:#64748b;font-size:11px;line-height:1.35}.formula-study-guide__board-roster-number{flex:0 0 auto;padding:3px 6px;border-radius:999px;background:#e0edff;color:#134eaf;font-size:10px;font-weight:900;white-space:nowrap}
.formula-study-guide__chapter-directory{margin:0 0 20px;padding:22px;border:1px solid #c9ded9;border-top:5px solid #0b817d;border-radius:12px;background:#f2faf8}.formula-study-guide__chapter-directory h4{margin:0 0 7px;font-size:22px}.formula-study-guide__chapter-directory>p{margin:0 0 16px;color:#496474;line-height:1.55}.formula-study-guide__chapter-directory-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.formula-study-guide__chapter-directory-group{padding:15px;border-radius:9px;background:#fff}.formula-study-guide__chapter-directory-group h5{margin:0 0 8px;font-size:16px}.formula-study-guide__chapter-directory-group h5 a{color:#075e54;text-decoration:none}.formula-study-guide__chapter-directory-group h5 a:hover{text-decoration:underline}.formula-study-guide__chapter-directory-group ol{margin:0;padding-left:22px;columns:2;column-gap:22px}.formula-study-guide__chapter-directory-group li{margin:0 0 5px;break-inside:avoid;color:#52677a;font-size:13px;line-height:1.35}.formula-study-guide__chapter-directory-group li a{color:#334155;text-decoration:none}.formula-study-guide__chapter-directory-group li a:hover{color:#075fc8;text-decoration:underline}
.formula-study-guide__chapter-section{margin:18px 0 0;padding:20px 22px;border-left:6px solid #0b817d;border-radius:11px;background:linear-gradient(135deg,#eaf8f5,#fff7ed)}.formula-study-guide__chapter-section .formula-study-guide__eyebrow{margin-bottom:5px}.formula-study-guide__chapter-section h4{margin:0 0 7px;font-size:25px}.formula-study-guide__chapter-section>p{margin:0;color:#496474;line-height:1.55}.formula-study-guide__chapter-section-meta{display:inline-block;margin-top:9px;padding:5px 9px;border-radius:999px;background:#fff;color:#52677a;font-size:12px;font-weight:800}
.formula-study-guide__priority{margin:0 0 18px;padding:20px 22px;border:1px solid #b9dfd7;border-left:5px solid #0b817d;border-radius:11px;background:#eef9f6}.formula-study-guide__priority h4{margin:0 0 7px;font-size:20px}.formula-study-guide__priority h5{margin:17px 0 0;font-size:15px}.formula-study-guide__priority>p{margin:0;color:#496474;line-height:1.55}
.formula-study-guide__route{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:17px}.formula-study-guide__route-step{padding:13px;border-radius:9px;background:#fff}.formula-study-guide__route-step b{display:flex;align-items:center;gap:8px;color:#123246}.formula-study-guide__route-number{display:inline-flex;width:25px;height:25px;flex:0 0 25px;align-items:center;justify-content:center;border-radius:50%;background:#0b817d;color:#fff;font-size:12px}.formula-study-guide__route-step p{margin:7px 0 0;color:#52677a;font-size:13px;line-height:1.45}
.formula-study-guide__dui-yao{margin:0 0 20px;padding:22px;border:1px solid #d8d4f0;border-top:5px solid #6d4bc3;border-radius:12px;background:linear-gradient(135deg,#faf8ff,#f3fbfa)}.formula-study-guide__dui-yao-header{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:16px}.formula-study-guide__dui-yao-header h4{margin:2px 0 7px;font-size:22px}.formula-study-guide__dui-yao-header p{max-width:790px;margin:0;color:#52677a;line-height:1.55}.formula-study-guide__dui-yao-header .formula-study-guide__dui-yao-study-intro{margin-top:9px;color:#304d60;font-weight:750}.formula-study-guide__dui-yao-count{flex:0 0 auto;padding:6px 10px;border-radius:999px;background:#ece7fb;color:#5a37ac;font-size:11px;font-weight:900;text-transform:uppercase;white-space:nowrap}.formula-study-guide__dui-yao-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px}.formula-study-guide__dui-yao-card{display:flex;min-height:212px;flex-direction:column;padding:14px;border:1px solid #e2def3;border-radius:10px;background:#fff;color:inherit;text-decoration:none}.formula-study-guide__dui-yao-card:hover{border-color:#8b6bd1;color:inherit;text-decoration:none;box-shadow:0 5px 14px rgba(71,50,122,.12)}.formula-study-guide__dui-yao-number{color:#6d4bc3;font-size:10px;font-weight:900;letter-spacing:.06em;text-transform:uppercase}.formula-study-guide__dui-yao-card h5{margin:6px 0 2px;font-size:16px;line-height:1.3}.formula-study-guide__dui-yao-pharmaceutical{color:#7a6b8d;font-size:13px;font-style:italic;line-height:1.35}.formula-study-guide__dui-yao-card p{margin:10px 0;color:#52677a;font-size:13px;line-height:1.45}.formula-study-guide__dui-yao-card small{display:block;margin-top:auto;color:#394b5a;font-size:11px;line-height:1.4}.formula-study-guide__dui-yao-footer{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-top:16px;padding-top:15px;border-top:1px solid #ded9ef}.formula-study-guide__dui-yao-footer p{margin:0;color:#5f5670;font-size:12px;line-height:1.5}
.formula-study-guide__compare{margin:0 0 20px;border:1px solid #d8e1e7;border-radius:11px;background:#fff;overflow:hidden}.formula-study-guide__compare h4{margin:0;padding:16px 18px;background:#f5f8fa;font-size:18px}.formula-study-guide__table-wrap{overflow-x:auto}.formula-study-guide__table{width:100%;border-collapse:collapse}.formula-study-guide__table th,.formula-study-guide__table td{padding:11px 14px;border-top:1px solid #e2e8ec;text-align:left;vertical-align:top}.formula-study-guide__table th{color:#334155;font-size:12px;text-transform:uppercase}.formula-study-guide__table td{color:#52677a;line-height:1.4}.formula-study-guide__table td:first-child{color:#17232a;font-weight:800;white-space:nowrap}
.formula-study-guide__domain-badge{display:inline-flex;align-items:center;margin:5px 0 0;padding:4px 7px;border-radius:999px;background:#e0edff;color:#134eaf;font-size:10px;font-weight:900;letter-spacing:.04em;text-decoration:none;text-transform:uppercase;white-space:nowrap}.formula-study-guide__domain-badge:hover{background:#c9ddff;color:#0b3b8c;text-decoration:none}
.formula-study-guide__formula-translation{display:block;margin-top:5px;color:#315fa5;font-size:12px;font-weight:750;line-height:1.35}
.formula-study-guide__category-nav{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:0 0 18px}.formula-study-guide__category-nav>span{flex:1}.formula-study-guide__category-nav>span:last-child{text-align:right}.formula-study-guide__category-nav-center{display:flex;align-items:center;justify-content:center;gap:9px;flex-wrap:wrap}
.formula-study-guide__formula-list{display:grid;gap:16px}
.formula-study-guide__formula{padding:22px;border:1px solid #d8e1e7;border-radius:12px;background:#fff;box-shadow:0 2px 8px rgba(15,23,42,.04)}
.formula-study-guide__formula-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:13px}.formula-study-guide__formula-label{display:block;margin-bottom:4px;color:#08746f;font-size:12px;font-weight:900;letter-spacing:.07em;text-transform:uppercase}.formula-study-guide__formula h4{margin:0;font-size:23px;line-height:1.25}.formula-study-guide__badges{display:flex;align-items:center;justify-content:flex-end;gap:7px;flex-wrap:wrap}.formula-study-guide__core{padding:5px 9px;border-radius:999px;background:#dff4ee;color:#0b675b;font-size:11px;font-weight:900;text-transform:uppercase;white-space:nowrap}
.formula-study-guide__risk{max-width:260px;padding:5px 9px;border-radius:999px;background:#e8f5f1;color:#116553;font-size:11px;font-weight:900;line-height:1.25;text-align:center;text-transform:uppercase;white-space:normal}.formula-study-guide__risk--high{background:#fff0d8;color:#955000}.formula-study-guide__risk--critical{background:#fde2df;color:#a12218}
.formula-study-guide__formula-pattern{margin:0 0 15px;color:#334155;font-size:16px;line-height:1.55}
.formula-study-guide__safety-summary{margin:-2px 0 14px;padding:9px 11px;border-left:4px solid #d38a19;border-radius:7px;background:#fff7e8;color:#704508;font-size:13px;line-height:1.45}.formula-study-guide__safety-summary--critical{border-left-color:#bd3429;background:#fff0ee;color:#7e211a}
.formula-study-guide__facts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.formula-study-guide__fact{padding:14px;border-radius:9px;background:#f6f9fa}.formula-study-guide__fact strong{display:block;margin-bottom:5px;color:#123246;font-size:13px}.formula-study-guide__fact span{display:block;color:#52677a;line-height:1.5}
.formula-study-guide__exam-focus{margin:14px 0 0;padding:11px 14px;border-left:4px solid #f77e25;border-radius:6px;background:#fff7ed;color:#6e481f;line-height:1.5}.formula-study-guide__exam-focus strong{color:#7c3f0a}
.formula-study-guide__deep-dive{margin-top:12px;border:1px solid #d5e4e1;border-radius:8px;background:#f8fbfa}.formula-study-guide__deep-dive summary{padding:10px 13px;color:#17675e;font-weight:800;cursor:pointer}.formula-study-guide__deep-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:12px;padding:0 13px 13px}.formula-study-guide__deep-grid p{margin:0;color:#52677a;line-height:1.5}.formula-study-guide__deep-grid strong{display:block;margin-bottom:4px;color:#17232a}
.formula-study-guide__modification{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:13px;padding:12px 13px;border:1px solid #bddfd7;border-left:4px solid #12816f;border-radius:8px;background:#f3fbf8;color:#365762;line-height:1.45}.formula-study-guide__modification p{margin:0}.formula-study-guide__modification strong{color:#087263}.formula-study-guide__modification b{color:#9b450e}.formula-study-guide__modification a{flex:0 0 auto;color:#075fc8;font-weight:800;text-decoration:none}.formula-study-guide__modification a:hover{text-decoration:underline}
.formula-study-guide__branches{margin-top:12px;border:1px solid #d7e1e6;border-radius:8px;background:#fff}.formula-study-guide__branches summary{padding:11px 13px;color:#174c69;font-weight:800;cursor:pointer}.formula-study-guide__branch-body{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:0 13px 13px}.formula-study-guide__branch-group{padding:12px;border-radius:8px;background:#f6f9fa}.formula-study-guide__branch-group h5{margin:0 0 8px;color:#17232a;font-size:14px}.formula-study-guide__branch-group ul{margin:0;padding-left:18px}.formula-study-guide__branch-group li{margin:0 0 8px;color:#52677a;line-height:1.45}.formula-study-guide__branch-group li:last-child{margin-bottom:0}.formula-study-guide__branch-group strong{color:#17232a}
.formula-study-guide__formula-footer{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:14px;padding-top:14px;border-top:1px solid #dde5e9}.formula-study-guide__ingredients{margin:0;color:#52677a;line-height:1.5}.formula-study-guide__ingredients strong{color:#17232a}.formula-study-guide__herb-english,.formula-study-guide__formula-english-inline{color:#64748b;font-size:.72em;font-weight:650;line-height:1.2}
.formula-study-guide__source-pages{flex:0 0 auto;padding:7px 10px;border-radius:7px;background:#eef3f6;color:#415a6b;font-size:12px;font-weight:800;white-space:nowrap}
.formula-study-guide__safety{margin-top:12px;border:1px solid #ded6ec;border-radius:8px;background:#fbf9ff}.formula-study-guide__safety summary{padding:10px 13px;color:#60409a;font-weight:800;cursor:pointer}.formula-study-guide__safety p{margin:0;padding:0 13px 13px;color:#52677a;line-height:1.5}
.formula-study-guide__references{margin:20px 0;border:1px solid #d8e1e7;border-radius:11px;background:#fff}.formula-study-guide__references>summary{padding:16px 18px;color:#334155;font-size:17px;font-weight:900;cursor:pointer}.formula-study-guide__references-body{padding:0 18px 18px}.formula-study-guide__references-body>p{margin:0 0 15px;color:#52677a;line-height:1.5}.formula-study-guide__reference-groups{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.formula-study-guide__reference-group{padding:14px;border-radius:9px;background:#f6f9fa}.formula-study-guide__reference-group h5{margin:0 0 10px;font-size:15px}.formula-study-guide__reference-group ul{margin:0;padding-left:18px}.formula-study-guide__reference-group li{margin:0 0 9px;color:#52677a;line-height:1.4}.formula-study-guide__reference-group li:last-child{margin-bottom:0}.formula-study-guide__reference-group strong{color:#17232a}
.formula-study-guide__additional{margin:20px 0;border:1px solid #add8cf;border-left:5px solid #0b817d;border-radius:11px;background:#effaf7}.formula-study-guide__additional>summary{padding:17px 18px;color:#075e54;font-size:18px;font-weight:900;cursor:pointer}.formula-study-guide__additional-body{padding:0 18px 18px}.formula-study-guide__additional-body>p{margin:0 0 15px;color:#496474;line-height:1.5}.formula-study-guide__additional-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:15px}.formula-study-guide__additional-group{padding:15px;border-radius:9px;background:#fff}.formula-study-guide__additional-group h5{margin:0 0 9px;color:#17232a;font-size:15px}.formula-study-guide__additional-group ol{margin:0;padding-left:22px}.formula-study-guide__additional-group li{margin:0 0 7px;color:#52677a;line-height:1.4}.formula-study-guide__additional-group a{color:#174c69;font-weight:750;text-decoration:none}.formula-study-guide__additional-group a:hover{color:#075fc8;text-decoration:underline}
.formula-study-guide__source{margin-top:20px;padding:14px 17px;border-radius:9px;background:#f1f5f9;color:#52677a;font-size:13px;line-height:1.5}
@media(max-width:1050px){.formula-study-guide__dui-yao-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:900px){.formula-study-guide__route,.formula-study-guide__reference-groups{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:800px){.formula-study-guide__top{display:block}.formula-study-guide__actions{align-items:flex-start;margin-top:15px}.formula-study-guide__module-switcher,.formula-study-guide__utility-actions{justify-content:flex-start}.formula-study-reader{top:8px}.formula-study-reader__fields--bilingual{grid-template-columns:1fr 1fr}.formula-study-reader__fields--bilingual label:first-child{grid-column:1/-1}.formula-study-reader__actions{grid-template-columns:repeat(3,minmax(0,1fr))}.formula-study-guide__chapter-path{grid-template-columns:1fr 1fr}.formula-study-guide__chapter-path--index{grid-template-columns:1fr}.formula-study-guide__chapter-jump{grid-column:1/-1;grid-row:1}.formula-study-guide__facts,.formula-study-guide__deep-grid,.formula-study-guide__branch-body{grid-template-columns:1fr}.formula-study-guide__chapter-shortcut-links{grid-template-columns:repeat(2,minmax(0,1fr))}.formula-study-guide__board-roster-grid,.formula-study-guide__chapter-directory-grid,.formula-study-guide__additional-grid{grid-template-columns:1fr}.formula-study-guide__board-verification,.formula-study-guide__formula-footer,.formula-study-guide__modification,.formula-study-guide__dui-yao-header,.formula-study-guide__dui-yao-footer{align-items:flex-start;flex-direction:column}.formula-study-guide__modification a{width:100%}.formula-study-guide__overview{grid-template-columns:1fr}.formula-study-guide__stats{justify-content:flex-start}.formula-study-guide__supplement-heading{display:block}.formula-study-guide__supplement-badge{display:inline-block;margin-top:10px}.formula-study-guide__dui-yao-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:560px){.formula-study-guide__actions,.formula-study-guide__category-nav{align-items:stretch;flex-direction:column}.formula-study-guide__chapter-path{grid-template-columns:1fr}.formula-study-guide__chapter-jump{grid-column:auto;grid-row:auto}.formula-study-guide__chapter-path-link--next{text-align:left}.formula-study-guide__chapter-jump form>div{align-items:stretch;flex-direction:column}.formula-study-guide__button{white-space:normal}.formula-study-guide__category-nav>span,.formula-study-guide__category-nav>span:last-child{width:100%;text-align:stretch}.formula-study-guide__category-nav-center{align-items:stretch;flex-direction:column}.formula-study-guide__formula-heading{flex-direction:column}.formula-study-guide__stats{display:grid;grid-template-columns:1fr 1fr}}
@media(max-width:560px){.formula-study-reader__disclosure>summary{padding:10px 11px}.formula-study-reader__title{font-size:16px}.formula-study-reader__description{font-size:11px}.formula-study-reader__panel{padding:0 10px 10px}.formula-study-reader__fields{grid-template-columns:1fr;gap:6px}.formula-study-reader__actions{grid-template-columns:repeat(2,minmax(0,1fr));gap:6px}.formula-study-reader button{min-height:36px;padding:6px;font-size:12px}.formula-study-reader__progress{grid-template-columns:1fr 1fr;gap:6px}.formula-study-reader__progress button{min-width:0}.formula-study-reader__range{grid-column:1/-1;grid-row:1}.formula-study-guide__route,.formula-study-guide__reference-groups,.formula-study-guide__chapter-shortcut-links,.formula-study-guide__dui-yao-grid{grid-template-columns:1fr}.formula-study-guide__chapter-directory-group ol{columns:1}.formula-study-guide__badges{justify-content:flex-start}}
</style>
<main class="formula-study-guide">
    <header class="formula-study-guide__top herbology-sheet-hero">
        <div>
            <p class="formula-study-guide__eyebrow"><?php echo s($ises ? 'Guía de estudio · Fórmulas' : 'Study Guide · Formulas'); ?></p>
            <h2><?php echo s($pagetitle); ?></h2>
            <p class="formula-study-guide__lead"><?php echo s($ises
                ? 'El apéndice C del guideline es el listado oficial de fórmulas que forman parte del contenido vigente de NCBAHM 2026. Recorre los 21 capítulos de Bensky en el orden de la fuente y comprueba cuáles de sus fórmulas aparecen en ese listado.'
                : 'Appendix C of the guideline is the official list of formulas included in the current 2026 NCBAHM content. Work through all 21 Bensky chapters in source order and check which formulas appear in that list.'); ?></p>
            <p class="formula-study-guide__marker-note"><?php echo s($ises ? '* El asterisco identifica una fórmula incluida en ese listado oficial.' : '* The asterisk identifies a formula included in that official list.'); ?></p>
        </div>
        <nav class="formula-study-guide__actions" aria-label="<?php echo s($ises ? 'Navegación de la guía' : 'Study Guide navigation'); ?>">
            <div class="formula-study-guide__utility-actions">
                <?php if (!$fromlibrary): ?>
                    <a class="formula-study-guide__button" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
                <?php endif; ?>
                <a class="formula-study-guide__button formula-study-guide__button--active" href="<?php echo $formulalisturl->out(false); ?>">Formula List</a>
                <a class="formula-study-guide__button" href="<?php echo $dashboardurl->out(false); ?>"><?php echo s($ises ? '← Dashboard' : '← Back to Dashboard'); ?></a>
            </div>
        </nav>
    </header>

    <?php if ($fromlibrary): ?>
        <?php echo block_myprogress_render_herbology_library_navigation($ises, $ises ? 'Fórmulas' : 'Formulas', 'guides'); ?>
    <?php else: ?>
    <nav class="formula-study-guide__study-route" aria-label="<?php echo s($ises ? 'Ruta de estudio' : 'Study path'); ?>">
        <a href="<?php echo $herbologyurl->out(false); ?>"><?php echo $ises ? 'Guía de estudio' : 'Study Guide'; ?></a><span aria-hidden="true">›</span>
        <a href="<?php echo $benskymapurl->out(false); ?>"><?php echo $ises ? 'Mapa Bensky' : 'Bensky Map'; ?></a><span aria-hidden="true">›</span>
        <a href="<?php echo $singleherbsurl->out(false); ?>"><?php echo $ises ? 'Hierbas' : 'Herbs'; ?></a><span aria-hidden="true">→</span>
        <a href="<?php echo $duiyaomoduleurl->out(false); ?>">Duì Yào</a><span aria-hidden="true">→</span>
        <a href="<?php echo $indexurl->out(false); ?>"><?php echo $ises ? 'Fórmulas' : 'Formulas'; ?></a><span aria-hidden="true">›</span>
        <span class="formula-study-guide__study-route-current" aria-current="page"><?php echo s($formularoutetitle); ?></span>
    </nav>
    <?php endif; ?>

    <section class="formula-study-reader" data-formula-read-aloud data-language="<?php echo $ises ? 'es-ES' : 'en-US'; ?>" hidden>
        <details class="formula-study-reader__disclosure" open>
            <summary>
                <span>
                    <span class="formula-study-reader__title"><?php echo s($ises ? 'Escuchar esta guía' : 'Listen to this Study Guide'); ?></span>
                    <span class="formula-study-reader__description"><?php echo s($ises
                        ? 'Escucha la página completa, una sección, una selección o el texto visible.'
                        : 'Listen to the full page, one section, a selection, or the visible text.'); ?></span>
                </span>
            </summary>
            <div class="formula-study-reader__panel">
                <div class="formula-study-reader__fields<?php echo $ises ? ' formula-study-reader__fields--bilingual' : ''; ?>">
                    <label>
                        <span><?php echo s($ises ? 'Sección' : 'Section'); ?></span>
                        <select data-formula-read-section-select aria-label="<?php echo s($ises ? 'Sección para escuchar' : 'Section to read'); ?>"></select>
                    </label>
                    <label>
                        <span><?php echo s($ises ? 'Voz' : 'Voice'); ?></span>
                        <select data-formula-read-voice aria-label="<?php echo s($ises ? 'Voz de narración' : 'Narration voice'); ?>"></select>
                    </label>
                    <?php if ($ises): ?>
                    <label>
                        <span>Voz para los nombres en inglés</span>
                        <select data-formula-read-english-voice aria-label="Voz para los nombres de fórmulas en inglés"></select>
                    </label>
                    <?php endif; ?>
                    <label class="formula-study-reader__mode">
                        <span><?php echo s($ises ? 'Explicación de tablas, recuadros y fórmulas' : 'Tables, cards, and formulas explanation'); ?></span>
                        <select data-formula-read-mode aria-label="<?php echo s($ises ? 'Nivel de detalle de la explicación' : 'Explanation detail level'); ?>">
                            <option value="summary"><?php echo s($ises ? 'Resumida · diferencias decisivas' : 'Summary · decisive differences'); ?></option>
                            <option value="extended"><?php echo s($ises ? 'Extendida · razonamiento completo' : 'Extended · full reasoning'); ?></option>
                        </select>
                        <small><?php echo s($ises
                            ? 'El resumen incluye solamente las fórmulas marcadas con asterisco porque pertenecen al guideline vigente.'
                            : 'The summary includes only formulas marked with an asterisk because they belong to the current guideline.'); ?></small>
                    </label>
                </div>
                <div class="formula-study-reader__actions">
                    <button type="button" data-formula-read-full><?php echo s($ises ? '▶ Página completa' : '▶ Full Page'); ?></button>
                    <button type="button" data-formula-read-section><?php echo s($ises ? '▶ Escuchar sección' : '▶ Read Section'); ?></button>
                    <button type="button" data-formula-read-selection><?php echo s($ises ? '🔊 Selección' : '🔊 Selection'); ?></button>
                    <button type="button" data-formula-read-visible><?php echo s($ises ? '◉ Texto visible' : '◉ Visible Text'); ?></button>
                    <button type="button" data-formula-read-pause disabled><?php echo s($ises ? '⏸ Pausar' : '⏸ Pause'); ?></button>
                    <button type="button" data-formula-read-stop disabled><?php echo s($ises ? '■ Detener' : '■ Stop'); ?></button>
                </div>
                <div class="formula-study-reader__progress">
                    <button type="button" data-formula-read-back disabled><?php echo s($ises ? '↶ Retroceder' : '↶ Back'); ?></button>
                    <label class="formula-study-reader__range">
                        <span class="formula-study-reader__range-heading">
                            <span><?php echo s($ises ? 'Progreso de lectura' : 'Reading progress'); ?></span>
                            <span data-formula-read-progress-value><?php echo s($ises ? 'Sin lectura' : 'Not reading'); ?></span>
                        </span>
                        <input type="range" min="0" max="0" value="0" data-formula-read-progress disabled>
                    </label>
                    <button type="button" data-formula-read-forward disabled><?php echo s($ises ? 'Avanzar ↷' : 'Forward ↷'); ?></button>
                </div>
                <p class="formula-study-reader__status" data-formula-read-status aria-live="polite"><?php echo s($ises ? 'Lista para escuchar.' : 'Ready to listen.'); ?></p>
            </div>
        </details>
    </section>

    <?php echo $renderchapternavigation('top'); ?>

    <?php if ($selected === null): ?>
        <section class="formula-study-guide__overview" aria-labelledby="formula-categories-title" data-formula-read-section-ignore>
            <div>
                <h3 id="formula-categories-title"><?php echo s($ises ? '21 capítulos de Bensky' : '21 Bensky chapters'); ?></h3>
                <p><?php echo s($ises
                    ? 'Este índice reproduce los capítulos 1–21 que entregaste. Los capítulos 5–21 abren la cobertura completa ya incorporada; los capítulos 1–4 muestran las fichas detalladas actualmente integradas sin confundirlas con el alcance total del libro.'
                    : 'This index follows the supplied Chapters 1–21. Chapters 5–21 open the complete coverage already incorporated; Chapters 1–4 show the detailed profiles currently integrated without confusing them with the book’s full scope.'); ?></p>
            </div>
            <div class="formula-study-guide__stats" aria-label="<?php echo s($ises ? 'Totales de la guía' : 'Guide totals'); ?>">
                <div class="formula-study-guide__stat"><strong>21</strong><span><?php echo $ises ? 'capítulos' : 'chapters'; ?></span></div>
                <div class="formula-study-guide__stat"><strong>17</strong><span><?php echo $ises ? 'capítulos completos' : 'complete chapters'; ?></span></div>
                <div class="formula-study-guide__stat"><strong>181</strong><span><?php echo $ises ? 'fórmulas oficiales' : 'official formulas'; ?></span></div>
            </div>
        </section>
        <section class="formula-study-guide__grid" aria-label="<?php echo s($ises ? 'Capítulos de Bensky' : 'Bensky chapters'); ?>">
            <?php foreach ($benskychapters as $chapter): ?>
                <?php
                $categoryparams = ['focus' => $chapter['key']];
                if ($ises) {
                    $categoryparams['lang'] = 'es';
                }
                $categoryurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $categoryparams);
                $copy = $categorycopy[$chapter['key']][$ises ? 1 : 0];
                $principalformulas = array_values(array_filter(
                    $chapter['formulas'],
                    static fn(array $formula): bool => ($formula['chapterRole'] ?? 'principal') !== 'associated'
                ));
                $associatedcount = count($chapter['formulas']) - count($principalformulas);
                $categoryboardstatus = $getboardstatus($principalformulas, $ises);
                if ($chapter['complete']) {
                    $categorymeta = count($principalformulas)
                        . ($ises ? ' fórmulas principales · capítulo completo' : ' principal formulas · complete chapter');
                    if ($associatedcount > 0) {
                        $categorymeta .= ' + ' . $associatedcount . ($ises ? ' asociada' : ' associated');
                    }
                    $statuslabel = $categoryboardstatus['label'];
                } else {
                    $categorymeta = count($principalformulas)
                        . ($ises ? ' fichas detalladas actualmente integradas' : ' detailed profiles currently integrated');
                    $statuslabel = ($ises ? 'Cobertura detallada: ' : 'Detailed coverage: ')
                        . $categoryboardstatus['included'] . ($ises ? ' oficiales' : ' official');
                }
                ?>
                <a class="formula-study-guide__category<?php echo $chapter['complete'] ? ' formula-study-guide__category--board-priority' : ''; ?>" href="<?php echo $categoryurl->out(false); ?>">
                    <span class="formula-study-guide__category-number"><?php echo (int) $chapter['number']; ?></span>
                    <strong><?php echo s($ises ? $chapter['nameEs'] : $chapter['name']); ?></strong>
                    <span class="formula-study-guide__category-meta"><?php echo s($categorymeta); ?></span>
                    <?php if ($statuslabel !== ''): ?><span class="formula-study-guide__board-status formula-study-guide__board-status--<?php echo s($categoryboardstatus['state']); ?>"><?php echo s($statuslabel); ?></span><?php endif; ?>
                    <p><?php echo s($copy); ?></p>
                    <small><?php echo s($ises ? 'Abrir capítulo de Bensky →' : 'Open Bensky chapter →'); ?></small>
                </a>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <?php $selectedcopy = $categorycopy[$selected['key']][$ises ? 1 : 0]; ?>
        <?php $selectedreference = $categoryreferences[$selected['key']][$ises ? 1 : 0] ?? null; ?>
        <?php
        if ($selectedbenskychapter !== null) {
            $selectedeyebrow = ($ises ? 'Capítulo de Bensky ' : 'Bensky Chapter ')
                . $selectedbenskychapter['number'] . ($ises ? ' de ' : ' of ') . '21';
            $selectedtitle = $ises ? $selectedbenskychapter['nameEs'] : $selectedbenskychapter['name'];
            $selectedvisiblecount = count(array_filter(
                $selected['formulas'],
                static fn(array $formula): bool => ($formula['chapterRole'] ?? 'principal') !== 'associated'
            ));
            $selectedcountlabel = $selectedbenskychapter['complete']
                ? ($ises ? 'fórmulas principales en este capítulo.' : 'principal formulas in this chapter.')
                : ($ises ? 'fichas detalladas actualmente integradas.' : 'detailed profiles currently integrated.');
        } else {
            $selectedeyebrow = $issupplemental
                ? ($isofficialexpansionpage
                    ? (($ises ? 'Ampliación oficial · Capítulo ' : 'Official expansion · Chapter ')
                        . $selected['sourceChapter'])
                    : (($ises ? 'Capítulo suplementario ' : 'Supplementary chapter ')
                        . $selected['sourceChapter']))
                : (($ises ? 'Categoría ' : 'Category ') . $selected['number']);
            $selectedtitle = $ises ? $selected['nameEs'] : $selected['name'];
            $selectedvisiblecount = count($selected['formulas']);
            $selectedcountlabel = $isofficialexpansionpage
                ? ($ises ? 'ficha oficial adicional en esta sección.' : 'additional official profile in this section.')
                : ($issupplemental
                    ? ($ises ? 'fórmulas principales en este capítulo.' : 'principal formulas in this chapter.')
                    : ($ises ? 'fórmulas en esta categoría.' : 'formulas in this category.'));
        }
        ?>
        <section class="formula-study-guide__category-header<?php echo $issupplemental ? ' formula-study-guide__category-header--supplemental' : ''; ?>" aria-labelledby="selected-category-title">
            <p class="formula-study-guide__eyebrow"><?php echo s($selectedeyebrow); ?></p>
            <h3 id="selected-category-title"><?php echo s($selectedtitle); ?></h3>
            <?php if (($selectedbenskychapter !== null || $issupplemental || isset($unifiedfamilystatusformulas[$selected['key']]))
                    && $selectedboardstatus['label'] !== ''): ?><span class="formula-study-guide__board-status formula-study-guide__board-status--<?php echo s($selectedboardstatus['state']); ?>" role="note"><?php echo s($selectedboardstatus['label']); ?></span><?php endif; ?>
            <p><?php echo s($selectedcopy); ?> <strong><?php echo $selectedvisiblecount; ?> <?php echo s($selectedcountlabel); ?></strong></p>
            <?php if ($selectedreference !== null): ?><span class="formula-study-guide__category-reference"><?php echo s($selectedreference); ?></span><?php endif; ?>
        </section>
        <?php if (!$issupplemental && $selected['key'] !== 'tonify'): ?>
            <section class="formula-study-guide__board-verification" aria-labelledby="board-verification-title">
                <div>
                    <h4 id="board-verification-title"><?php echo s($ises ? 'Comprueba el listado oficial del Board' : 'Verify the official Board list'); ?></h4>
                    <p><?php echo s($ises
                        ? 'Abre el listado oficial completo del Appendix C —181 fórmulas— para comprobar directamente cuáles pertenecen al guideline. Este capítulo forma parte del índice completo de 21 capítulos de Bensky.'
                        : 'Open the complete official Appendix C roster—181 formulas—to verify directly which formulas belong to the guideline. This chapter is part of the complete 21-chapter Bensky index.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Abrir listado oficial →' : 'Open official roster →'); ?></a>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'tonify'): ?>
            <section class="formula-study-guide__chapter-shortcut" aria-labelledby="chapter-eight-shortcut-title">
                <h4 id="chapter-eight-shortcut-title"><?php echo s($ises ? 'Capítulo 8 completo · 45 fórmulas' : 'Complete Chapter 8 · 45 formulas'); ?></h4>
                <p><?php echo s($ises
                    ? 'El capítulo está dividido en seis tipos de tonificación. Nueve de sus fórmulas aparecen en la secuencia detallada del núcleo: ocho en Tonify y una en Stabilize and Bind.'
                    : 'The chapter is divided into six tonification types. Nine of its formulas appear in the detailed core sequence: eight in Tonify and one in Stabilize and Bind.'); ?></p>
                <?php
                $masterchapterparams = ['focus' => 'tonify-chapter-8'];
                if ($ises) {
                    $masterchapterparams['lang'] = 'es';
                }
                $masterchapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $masterchapterparams);
                ?>
                <section class="formula-study-guide__board-roster" aria-labelledby="chapter-eight-board-roster-title">
                    <h5 id="chapter-eight-board-roster-title"><?php echo s($ises ? 'Fórmulas de este capítulo incluidas en la secuencia detallada del núcleo · 9' : 'Chapter formulas included in the detailed core sequence · 9'); ?></h5>
                    <p><?php echo s($ises
                        ? 'La categoría grande indica el tipo exacto de tonificación del capítulo. Usa el enlace para comprobar estas fórmulas directamente contra el listado oficial completo de 181 fórmulas del Appendix C.'
                        : 'The group heading gives the exact chapter tonification type. Use the link to verify these formulas directly against the complete official Appendix C roster of 181 formulas.'); ?></p>
                    <div class="formula-study-guide__chapter-shortcut-action">
                        <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Abrir listado oficial →' : 'Open official roster →'); ?></a>
                    </div>
                    <div class="formula-study-guide__board-roster-grid">
                        <?php foreach ($tonifyboardgroups as $chaptercategorykey => $boardformulanames): ?>
                            <?php
                            $chaptercategory = $supplementalcategories[$chaptercategorykey];
                            $chapterparams = ['focus' => $chaptercategorykey];
                            if ($ises) {
                                $chapterparams['lang'] = 'es';
                            }
                            $chapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $chapterparams);
                            ?>
                            <section class="formula-study-guide__board-roster-group">
                                <h6><a href="<?php echo $chapterurl->out(false); ?>"><?php echo s($ises ? $chaptercategory['nameEs'] : $chaptercategory['name']); ?></a></h6>
                                <ul>
                                    <?php foreach ($boardformulanames as $boardformulaname): ?>
                                        <?php
                                        $boardformula = $getboardformula($boardformulaname);
                                        if ($boardformula === null) {
                                            continue;
                                        }
                                        $boardcategory = $categories[$boardformula['categoryKey']] ?? null;
                                        $boardcategoryname = $boardcategory === null
                                            ? $boardformula['categoryKey']
                                            : ($ises ? $boardcategory['nameEs'] : $boardcategory['name']);
                                        ?>
                                        <li class="formula-study-guide__board-roster-formula">
                                            <span>
                                                <strong><?php echo $renderformulanamewithenglish($boardformula['name']); ?></strong>
                                                <small class="formula-study-guide__board-roster-meta"><?php echo s(($ises ? 'Ficha del núcleo: ' : 'core profile: ') . $boardcategoryname); ?></small>
                                            </span>
                                            <span class="formula-study-guide__board-roster-number"><?php echo s(($ises ? 'Fórmula ' : 'Formula ') . $boardformula['number']); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </section>
                <div class="formula-study-guide__chapter-shortcut-action"><a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $masterchapterurl->out(false); ?>"><?php echo s($ises ? 'Abrir el capítulo 8 completo →' : 'Open complete Chapter 8 →'); ?></a></div>
                <div class="formula-study-guide__chapter-shortcut-links">
                    <?php foreach ($tonifychaptercategories as $chaptercategory): ?>
                        <?php
                        $chapterparams = ['focus' => $chaptercategory['key']];
                        if ($ises) {
                            $chapterparams['lang'] = 'es';
                        }
                        $chapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $chapterparams);
                        ?>
                        <a class="formula-study-guide__chapter-shortcut-link" href="<?php echo $chapterurl->out(false); ?>">
                            <?php echo s($ises ? $chaptercategory['nameEs'] : $chaptercategory['name']); ?>
                            <span><?php echo count($chaptercategory['formulas']); ?> <?php echo $ises ? 'fórmulas' : 'formulas'; ?> →</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'tonify-chapter-8'): ?>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-eight-principles-title">
                <h4 id="chapter-eight-principles-title"><?php echo s($ises ? 'Cómo se organiza la tonificación' : 'How tonification is organized'); ?></h4>
                <p><?php echo s($ises
                    ? 'Las fórmulas tónicas se eligen por la sustancia deficiente y por el órgano o mecanismo dominante. Primero se diferencia Qi, Sangre, Yin, Yang o una deficiencia combinada; después se comprueban Frío, Calor por deficiencia, Humedad, Flema, estasis, hundimiento o pérdidas.'
                    : 'Tonifying formulas are selected by the deficient substance and the dominant organ or mechanism. First differentiate Qi, Blood, Yin, Yang, or combined deficiency; then check for Cold, deficiency Heat, Dampness, Phlegm, stasis, sinking, or leakage.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Identifica qué falta' : 'Identify what is deficient'); ?></b><p><?php echo s($ises ? 'Qi, Sangre, Yin, Yang o varias sustancias a la vez.' : 'Qi, Blood, Yin, Yang, or several substances together.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Localiza el sistema' : 'Locate the system'); ?></b><p><?php echo s($ises ? 'Bazo, Pulmón, Corazón, Hígado, Riñón o Estómago.' : 'Spleen, Lung, Heart, Liver, Kidney, or Stomach.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Busca la complicación' : 'Find the complication'); ?></b><p><?php echo s($ises ? 'Hundimiento, pérdidas, Frío, Calor vacío, Humedad, Flema o estasis.' : 'Sinking, leakage, Cold, empty Heat, Dampness, Phlegm, or stasis.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Comprueba seguridad' : 'Check safety'); ?></b><p><?php echo s($ises ? 'Descarta patógeno activo, sangrado, urgencias y sustancias tóxicas o restringidas.' : 'Exclude active pathogens, bleeding, emergencies, and toxic or restricted substances.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-eight-directory-title">
                <h4 id="chapter-eight-directory-title"><?php echo s($ises ? 'Listado completo de fórmulas que tonifican' : 'Complete list of formulas that tonify'); ?></h4>
                <p><?php echo s($ises
                    ? 'Las 45 fórmulas están separadas por tipo de tonificación. Selecciona una fórmula para ir directamente a su ficha o continúa hacia abajo para estudiar cada sección completa.'
                    : 'The 45 formulas are separated by tonification type. Select a formula to jump to its card or continue downward to study every complete section.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($tonifychaptercategories as $chaptercategory): ?>
                        <section class="formula-study-guide__chapter-directory-group">
                            <h5><a href="#chapter-section-<?php echo s($chaptercategory['key']); ?>"><?php echo s($ises ? $chaptercategory['nameEs'] : $chaptercategory['name']); ?> · <?php echo count($chaptercategory['formulas']); ?></a></h5>
                            <ol>
                                <?php foreach ($chaptercategory['formulas'] as $chapterformula): ?>
                                    <?php $chapterformulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $chapterformula['number']); ?>
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo $renderformulanamewithenglish($chapterformula['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php
        $relatedduiyaomarkup = '';
        $moverelatedduiyaototheend = $selected['key'] === 'regulate-qi-chapter-12';
        $deferrelatedduiyao = $moverelatedduiyaototheend || in_array($selected['key'], [
            'calm-shen-chapter-10',
            'expel-wind-chapter-14',
            'digestive-chapter-18',
        ], true);
        ?>
        <?php if ($selectedguide !== null): ?>
            <?php if ($selectedformulasections !== []): ?>
                <section class="formula-study-guide__medical-rationale" aria-labelledby="clinical-frame-title">
                    <div class="formula-study-guide__medical-rationale-header">
                        <span class="formula-study-guide__medical-rationale-stat"><?php echo s(count($selected['formulas'])
                            . ($issupplemental ? ($ises ? ' fórmulas principales · ' : ' principal formulas · ') : ($ises ? ' fichas detalladas · ' : ' detailed core profiles · '))
                            . count($selectedformulasections) . ($ises ? ' secciones clínicas:' : ' clinical sections:')); ?></span>
                        <h4 id="clinical-frame-title"><?php echo s($ises ? $selectedguide['clinicalFrameTitleEs'] : $selectedguide['clinicalFrameTitle']); ?></h4>
                        <p><?php echo s($ises ? $selectedguide['clinicalFrameTextEs'] : $selectedguide['clinicalFrameText']); ?></p>
                    </div>
                    <div class="formula-study-guide__table-wrap">
                        <table class="formula-study-guide__table" data-formula-context-table data-table-narration="<?php echo s($ises
                            ? 'Esta tabla relaciona cada sección clínica con sus claves para el diagnóstico y con las fórmulas principales que se estudian en ella.'
                            : 'This table connects each clinical section with its diagnostic keys and the principal formulas studied within it.'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $ises ? 'Sección clínica:' : 'Clinical section:'; ?></th>
                                    <th><?php echo $ises ? 'Claves para el diagnóstico:' : 'Diagnostic keys:'; ?></th>
                                    <th><?php echo $issupplemental ? ($ises ? 'Fórmulas principales:' : 'Principal formulas:') : ($ises ? 'Fichas detalladas:' : 'Detailed core profiles:'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selectedformulasections as $formulasection): ?>
                                    <tr>
                                        <td><?php echo s($ises ? $formulasection['nameEs'] : $formulasection['name']); ?></td>
                                        <td><?php echo s($ises ? $formulasection['diagnosticEs'] : $formulasection['diagnostic']); ?></td>
                                        <td class="formula-study-guide__medical-rationale-board">
                                            <?php foreach ($formulasection['formulaNames'] as $sectionformulaindex => $sectionformulaname): ?>
                                                <?php $sectionformula = $getboardformula($sectionformulaname) ?? ($selectedformulasbyname[$sectionformulaname] ?? null); ?>
                                                <?php if ($sectionformulaindex > 0): ?>, <?php endif; ?>
                                                <a href="#formula-<?php echo s(preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $sectionformula['number'])); ?>"><?php echo $renderformulanamewithenglish($sectionformulaname); ?></a>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="formula-study-guide__medical-rationale-header formula-study-guide__medical-rationale-secondary">
                        <h4><?php echo s($ises ? 'Aclaración médica importante' : 'Important medical clarification'); ?></h4>
                        <p><?php echo s($ises ? $selectedguide['clinicalFrameCaveatEs'] : $selectedguide['clinicalFrameCaveat']); ?></p>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($selectedguide['therapeuticClasses'])): ?>
                <section class="formula-study-guide__scope-analysis" aria-labelledby="therapeutic-classification-title">
                    <header class="formula-study-guide__scope-analysis-header">
                        <span class="formula-study-guide__scope-analysis-count"><?php echo s(count($selectedguide['therapeuticClasses']) . ($ises ? ' clases terapéuticas:' : ' therapeutic classes:')); ?></span>
                        <h4 id="therapeutic-classification-title"><?php echo s($ises ? $selectedguide['classificationTitleEs'] : $selectedguide['classificationTitle']); ?></h4>
                        <p><?php echo s($ises ? $selectedguide['classificationTextEs'] : $selectedguide['classificationText']); ?></p>
                        <p><?php echo s($ises
                            ? '“Fórmulas estudiadas en detalle” identifica las que tienen una ficha principal en esta sección. “Otras fórmulas para comparar” añade ejemplos de la misma clase terapéutica. Estas etiquetas no determinan por sí solas si una fórmula pertenece al apéndice C, el listado oficial de fórmulas del guideline.'
                            : '“Formulas studied in detail” identifies formulas with a principal profile in this section. “Other formulas for comparison” adds examples from the same therapeutic class. These labels do not by themselves determine whether a formula belongs to Appendix C, the guideline official list of formulas.'); ?></p>
                    </header>
                    <div class="formula-study-guide__table-wrap">
                        <table class="formula-study-guide__table" data-formula-context-table data-table-narration="<?php echo s($ises
                            ? 'Esta tabla compara las clases terapéuticas de la categoría. Para cada clase indica qué hace, qué fórmulas la representan y cuáles son sus síntomas clave.'
                            : 'This table compares the category therapeutic classes. For each class, it explains what it does, which formulas represent it, and its key symptoms.'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $ises ? 'Clase terapéutica:' : 'Therapeutic class:'; ?></th>
                                    <th><?php echo $ises ? 'Qué hace:' : 'What it does:'; ?></th>
                                    <th><?php echo $ises ? 'Fórmulas representativas:' : 'Representative formulas:'; ?></th>
                                    <th><?php echo $ises ? 'Síntomas clave:' : 'Key symptoms:'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selectedguide['therapeuticClasses'] as $therapeuticclass): ?>
                                    <tr>
                                        <td><strong><?php echo s($ises ? $therapeuticclass['nameEs'] : $therapeuticclass['name']); ?></strong></td>
                                        <td><?php echo s($ises ? $therapeuticclass['logicEs'] : $therapeuticclass['logic']); ?></td>
                                        <td>
                                            <?php if ($therapeuticclass['boardNames'] !== []): ?>
                                                <strong><?php echo s($ises
                                                    ? (count($therapeuticclass['boardNames']) === 1 ? 'Fórmula estudiada en detalle:' : 'Fórmulas estudiadas en detalle:')
                                                    : (count($therapeuticclass['boardNames']) === 1 ? 'Formula studied in detail:' : 'Formulas studied in detail:')); ?></strong>
                                                <?php foreach ($therapeuticclass['boardNames'] as $classnameindex => $classname): ?>
                                                    <?php $classboardformula = $getboardformula($classname); ?>
                                                    <?php if ($classnameindex > 0): ?>, <?php endif; ?>
                                                    <a href="#formula-<?php echo s((string) $classboardformula['number']); ?>"><?php echo $renderformulanamewithenglish($classname); ?></a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <?php if ($therapeuticclass['boardNames'] !== [] && $therapeuticclass['otherNames'] !== []): ?><br><?php endif; ?>
                                            <?php if ($therapeuticclass['otherNames'] !== []): ?>
                                                <strong><?php echo s($ises
                                                    ? (count($therapeuticclass['otherNames']) === 1 ? 'Otra fórmula para comparar:' : 'Otras fórmulas para comparar:')
                                                    : (count($therapeuticclass['otherNames']) === 1 ? 'Other formula for comparison:' : 'Other formulas for comparison:')); ?></strong>
                                                <?php foreach ($therapeuticclass['otherNames'] as $othernameindex => $othername): ?>
                                                    <?php if ($othernameindex > 0): ?>, <?php endif; ?>
                                                    <?php echo $renderformulanamewithenglish($othername); ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo s($ises ? $therapeuticclass['separatorEs'] : $therapeuticclass['separator']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($selectedguide['boardScopeTitle'])): ?>
                        <aside class="formula-study-guide__scope-analysis-header formula-study-guide__scope-analysis-board-note">
                            <span class="formula-study-guide__scope-analysis-count"><?php echo s($ises ? 'Fuera del Appendix C vigente:' : 'Outside the current Appendix C:'); ?></span>
                            <h4><?php echo s($ises ? $selectedguide['boardScopeTitleEs'] : $selectedguide['boardScopeTitle']); ?></h4>
                            <p><?php echo s($ises ? $selectedguide['boardScopeTextEs'] : $selectedguide['boardScopeText']); ?></p>
                            <p class="formula-study-guide__scope-analysis-caveat"><strong><?php echo s($ises ? 'Límite de la explicación:' : 'Limit of the explanation:'); ?></strong> <?php echo s($ises ? $selectedguide['boardScopeCaveatEs'] : $selectedguide['boardScopeCaveat']); ?></p>
                        </aside>
                    <?php endif; ?>
                    <footer class="formula-study-guide__scope-analysis-header">
                        <p class="formula-study-guide__scope-analysis-caveat"><strong><?php echo s($ises ? 'Distinción clínica y de seguridad:' : 'Clinical and safety distinction:'); ?></strong> <?php echo s($ises ? $selectedguide['classificationCaveatEs'] : $selectedguide['classificationCaveat']); ?></p>
                    </footer>
                </section>
            <?php endif; ?>
            <?php if (!empty($selectedguide['priorityTitle']) && !empty($selectedguide['route'])): ?>
            <section class="formula-study-guide__priority" aria-labelledby="guideline-priority-title">
                <h4 id="guideline-priority-title"><?php echo s($ises ? $selectedguide['priorityTitleEs'] : $selectedguide['priorityTitle']); ?></h4>
                <p><?php echo s($ises ? $selectedguide['priorityTextEs'] : $selectedguide['priorityText']); ?></p>
                <h5><?php echo s($ises ? $selectedguide['routeTitleEs'] : $selectedguide['routeTitle']); ?></h5>
                <div class="formula-study-guide__route">
                    <?php foreach ($selectedguide['route'] as $step): ?>
                        <div class="formula-study-guide__route-step">
                            <b><span class="formula-study-guide__route-number"><?php echo s($step[0]); ?></span><?php echo s($ises ? $step[3] : $step[1]); ?></b>
                            <p><?php echo s($ises ? $step[4] : $step[2]); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
            <?php if (!empty($selectedguide['relatedDuiYao'])): ?>
                <?php
                ob_start();
                $duiyaoindexpath = $ises
                    ? '/study-guide/comparisons/dui-yao-es.html'
                    : '/study-guide/comparisons/dui-yao.html';
                $duiyaoindexanchor = (string) ($selectedguide['relatedDuiYaoAnchor'] ?? 'cough-phlegm');
                $duiyaocategoryname = $ises ? $selected['nameEs'] : $selected['name'];
                $duiyaoreturnfocusmap = [
                    'tonify-chapter-8' => 'tonify',
                    'stabilize-bind-chapter-9' => 'stabilize-bind',
                    'calm-shen-chapter-10' => 'calm-shen',
                    'regulate-qi-chapter-12' => 'regulate-qi',
                    'regulate-blood-chapter-13' => 'invigorate-blood',
                    'expel-wind-chapter-14' => 'expel-wind',
                    'expel-damp-chapter-16' => 'expel-damp',
                    'transform-phlegm-chapter-17' => 'transform-phlegm',
                    'digestive-chapter-18' => 'digestive',
                ];
                $duiyaoreturnfocus = $duiyaoreturnfocusmap[$selected['key']] ?? $selected['key'];
                $duiyaoreturnparams = $duiyaoreturnfocus === '' ? [] : [
                    'from' => 'formula-study-guide',
                    'focus' => $duiyaoreturnfocus,
                ];
                $duiyaorelatedslugs = array_values(array_filter(
                    array_column($selectedguide['relatedDuiYao'], 'slug'),
                    static fn($slug): bool => is_string($slug) && preg_match('/^[a-z0-9-]+$/', $slug) === 1
                ));
                if ($duiyaorelatedslugs !== []) {
                    $duiyaoreturnparams['related-pairs'] = implode(',', $duiyaorelatedslugs);
                }
                $duiyaoindexurl = new moodle_url($duiyaoindexpath, $duiyaoreturnparams, $duiyaoindexanchor);
                ?>
                <section class="formula-study-guide__dui-yao" aria-labelledby="related-dui-yao-title">
                    <header class="formula-study-guide__dui-yao-header">
                        <div>
                            <p class="formula-study-guide__eyebrow" data-read-aloud-ignore><?php echo s($ises ? 'Appendix B · Parejas oficiales' : 'Appendix B · Official pairs'); ?></p>
                            <h4 id="related-dui-yao-title"><?php echo s($ises ? $selectedguide['relatedDuiYaoTitleEs'] : $selectedguide['relatedDuiYaoTitle']); ?></h4>
                            <p><?php echo s($ises
                                ? 'El apéndice B del guideline es el listado oficial de parejas de hierbas, conocidas como Duì Yào, que forman parte del contenido de estudio.'
                                : 'Appendix B of the guideline is the official list of herb pairs, known as Duì Yào, included in the study content.'); ?></p>
                            <p><?php echo s($ises ? $selectedguide['relatedDuiYaoTextEs'] : $selectedguide['relatedDuiYaoText']); ?></p>
                            <p class="formula-study-guide__dui-yao-study-intro"><?php echo s($ises
                                ? 'A continuación, comenzaremos a estudiar individualmente los Duì Yào relacionados con la categoría ' . $duiyaocategoryname . '.'
                                : 'Next, we will begin studying the Duì Yào related to the ' . $duiyaocategoryname . ' category one at a time.'); ?></p>
                        </div>
                        <span class="formula-study-guide__dui-yao-count" data-read-aloud-ignore><?php echo s(count($selectedguide['relatedDuiYao']) . ($ises ? ' parejas relacionadas' : ' related pairs')); ?></span>
                    </header>
                    <div class="formula-study-guide__dui-yao-grid">
                        <?php foreach ($selectedguide['relatedDuiYao'] as $duiyaoindex => $duiyaopair): ?>
                            <?php
                            $duiyaopath = '/study-guide/comparisons/' . $duiyaopair['slug'] . ($ises ? '-es' : '') . '.html';
                            $duiyaourl = new moodle_url($duiyaopath, $duiyaoreturnparams);
                            $duiyaosmartpair = $duiyaosmartbyslug[$duiyaopair['slug']] ?? null;
                            $duiyaopinyinherbs = preg_split('/\s*\+\s*/u', $duiyaopair['title']) ?: [];
                            $duiyaoplainherbs = is_array($duiyaosmartpair['herbs'] ?? null) ? $duiyaosmartpair['herbs'] : [];
                            $duiyaofirstname = trim((string) ($duiyaopinyinherbs[0] ?? ''));
                            $duiyaosecondname = trim((string) ($duiyaopinyinherbs[1] ?? ''));
                            $duiyaofirstlatin = (string) ($herblatinbyname[$duiyaoplainherbs[0] ?? ''] ?? '');
                            $duiyaosecondlatin = (string) ($herblatinbyname[$duiyaoplainherbs[1] ?? ''] ?? '');
                            $duiyaopharmaceuticaltitle = implode(' + ', array_filter([$duiyaofirstlatin, $duiyaosecondlatin]));
                            $duiyaorelatedformulaidentities = $getrelatedformulaidentities((string) $duiyaopair['formulas']);
                            $duiyaoenglishformulanames = array_values(array_filter(array_map(
                                static fn(array $identity): string => trim((string) ($identity['englishName'] ?? '')),
                                $duiyaorelatedformulaidentities
                            )));
                            $duiyaoformulasdisplay = preg_replace('/[.\s]+$/u', '', trim((string) $duiyaopair['formulas'])) ?? trim((string) $duiyaopair['formulas']);
                            $duiyaorelatedformulasjson = json_encode($duiyaorelatedformulaidentities, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            ?>
                            <a class="formula-study-guide__dui-yao-card" data-formula-dui-yao-card data-dui-yao-related-position="<?php echo (int) $duiyaoindex + 1; ?>" data-dui-yao-category-name="<?php echo s($duiyaocategoryname); ?>" data-dui-yao-related-formulas="<?php echo s($duiyaorelatedformulasjson === false ? '[]' : $duiyaorelatedformulasjson); ?>" data-dui-yao-first-name="<?php echo s($duiyaofirstname); ?>" data-dui-yao-first-latin="<?php echo s($duiyaofirstlatin); ?>" data-dui-yao-second-name="<?php echo s($duiyaosecondname); ?>" data-dui-yao-second-latin="<?php echo s($duiyaosecondlatin); ?>" href="<?php echo $duiyaourl->out(false); ?>">
                                <span class="formula-study-guide__dui-yao-number" data-read-aloud-ignore><?php echo s(($ises ? 'Pareja oficial ' : 'Official pair ') . $duiyaopair['number'] . ' / 51'); ?></span>
                                <h5><?php echo s($duiyaopair['title']); ?></h5>
                                <span class="formula-study-guide__dui-yao-pharmaceutical" data-read-aloud-ignore><?php echo s($duiyaopharmaceuticaltitle); ?></span>
                                <p><?php echo s($ises ? $duiyaopair['actionEs'] : $duiyaopair['action']); ?></p>
                                <small><strong><?php echo $ises ? 'Vínculo con fórmula del guideline:' : 'Guideline formula link:'; ?></strong> <?php echo s($markofficialformulasintext($duiyaoformulasdisplay)); ?><?php if ($duiyaoenglishformulanames !== []): ?> <span class="formula-study-guide__dui-yao-formula-translation" lang="en" data-formula-english-speech="<?php echo s(implode('; ', $duiyaoenglishformulanames)); ?>"><?php echo s('(' . implode('; ', $duiyaoenglishformulanames) . ')'); ?></span><?php endif; ?>.<br><span data-read-aloud-ignore><?php echo $ises ? 'Abrir ficha →' : 'Open profile →'; ?></span></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <footer class="formula-study-guide__dui-yao-footer">
                        <p><strong><?php echo s($ises ? 'Límite de estudio:' : 'Study boundary:'); ?></strong> <?php echo s($ises ? $selectedguide['relatedDuiYaoCaveatEs'] : $selectedguide['relatedDuiYaoCaveat']); ?></p>
                        <a class="formula-study-guide__button" href="<?php echo $duiyaoindexurl->out(false); ?>"><?php echo s($ises ? 'Ver el bloque Duì Yào completo →' : 'Open the complete Duì Yào block →'); ?></a>
                    </footer>
                </section>
                <?php
                $relatedduiyaomarkup = (string) ob_get_clean();
                if (!$deferrelatedduiyao) {
                    echo $relatedduiyaomarkup;
                }
                ?>
            <?php endif; ?>
            <?php if (!in_array($selected['key'], ['expel-damp-chapter-16', 'transform-phlegm-chapter-17'], true) && !empty($selectedguide['quickCompare'])): ?>
                <?php $quickcomparesections = $selectedformulasections !== [] ? $selectedformulasections : [['key' => 'all', 'name' => $selectedguide['quickCompareTitle'], 'nameEs' => $selectedguide['quickCompareTitleEs'], 'formulaNames' => array_column($selectedguide['quickCompare'], 0)]]; ?>
                <?php foreach ($quickcomparesections as $comparesection): ?>
                <?php $compareformulanames = array_flip($comparesection['formulaNames']); ?>
                <?php $comparerows = array_values(array_filter($selectedguide['quickCompare'], static fn(array $row): bool => isset($compareformulanames[$row[0]]))); ?>
                <section class="formula-study-guide__compare" aria-labelledby="core-comparison-<?php echo s($comparesection['key']); ?>-title">
                    <h4 id="core-comparison-<?php echo s($comparesection['key']); ?>-title"><?php echo s($selectedformulasections !== []
                        ? (($ises ? 'Comparación · ' : 'Comparison · ') . ($ises ? $comparesection['nameEs'] : $comparesection['name']))
                        : ($ises ? $selectedguide['quickCompareTitleEs'] : $selectedguide['quickCompareTitle'])); ?></h4>
                    <div class="formula-study-guide__table-wrap">
                        <table class="formula-study-guide__table" data-formula-comparison-table>
                            <thead><tr><th><?php echo $ises ? 'Fórmula:' : 'Formula:'; ?></th><th><?php echo $ises ? 'Patrón:' : 'Pattern:'; ?></th><th><?php echo $ises ? 'Síntomas clave:' : 'Key symptoms:'; ?></th></tr></thead>
                            <tbody>
                                <?php foreach ($comparerows as $row): ?>
                                    <?php
                                    $formulaenglishname = $getformulaenglishname($row[0]);
                                    ?>
                                    <tr>
                                        <td<?php echo $formulaenglishname !== '' ? ' data-formula-english-name="' . s($formulaenglishname) . '"' : ''; ?>>
                                            <?php echo s($displayformulaname($row[0])); ?>
                                            <?php if ($formulaenglishname !== ''): ?>
                                                <span class="formula-study-guide__formula-translation" lang="en" data-formula-english-speech="<?php echo s($formulaenglishname); ?>"><?php echo s('(' . $formulaenglishname . ')'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo s($ises ? $row[3] : $row[1]); ?></td>
                                        <td><?php echo s($ises ? $row[4] : $row[2]); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($selected['key'] === 'stabilize-bind'): ?>
            <section class="formula-study-guide__chapter-shortcut" aria-labelledby="chapter-nine-shortcut-title">
                <h4 id="chapter-nine-shortcut-title"><?php echo s($ises ? 'Capítulo 9 completo · 17 fórmulas' : 'Complete Chapter 9 · 17 formulas'); ?></h4>
                <p><?php echo s($ises
                    ? 'Dos fórmulas del capítulo 9 de Bensky aparecen en la secuencia detallada del núcleo—Si Shen Wan (#50) y Gu Jing Wan (#51)—y la ampliación añade 15 perfiles principales relacionados. Yu Ping Feng San (#49) es la tercera ficha interna de esta categoría y también pertenece al Appendix C oficial, pero Bensky la estudia en el capítulo 8.'
                    : 'Two formulas in Bensky Chapter 9 appear in the detailed core sequence—Si Shen Wan (#50) and Gu Jing Wan (#51)—and the expansion adds 15 related principal profiles. Yu Ping Feng San (#49) is the third internal profile in this category and also belongs to official Appendix C, but Bensky discusses it in Chapter 8.'); ?></p>
                <div class="formula-study-guide__chapter-shortcut-actions">
                    <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $stabilizechapterurl->out(false); ?>"><?php echo s($ises ? 'Abrir el capítulo 9 completo →' : 'Open complete Chapter 9 →'); ?></a>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'stabilize-bind-chapter-9'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 9.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 9.'); ?></p>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-nine-principles-title">
                <h4 id="chapter-nine-principles-title"><?php echo s($ises ? 'Principio central: tonificar la raíz y contener la rama' : 'Core principle: tonify the root and secure the branch'); ?></h4>
                <p><?php echo s($ises
                    ? 'El capítulo trata pérdidas por debilidad del mecanismo de contención. Antes de astringir, descarta un patógeno, Calor, Humedad-Calor, estasis u otro exceso no resuelto; contenerlo puede atraparlo. El colapso, el sangrado abundante, la dificultad respiratoria y la pérdida grave de líquidos son urgencias médicas.'
                    : 'The chapter treats leakage caused by a weak containment mechanism. Before astringing, exclude an unresolved pathogen, Heat, Damp-Heat, stasis, or another excess; binding can trap it. Collapse, heavy bleeding, respiratory distress, and severe fluid loss are medical emergencies.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Localiza la pérdida' : 'Locate the leakage'); ?></b><p><?php echo s($ises ? 'Sudor, tos, intestino, orina/Esencia o útero/flujo.' : 'Sweat, cough, bowel, urine/Essence, or uterus/discharge.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Confirma deficiencia' : 'Confirm deficiency'); ?></b><p><?php echo s($ises ? 'Qi, Yin, Yang, Sangre o comunicación funcional debilitada.' : 'Qi, Yin, Yang, Blood, or weakened functional communication.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Descarta exceso' : 'Exclude excess'); ?></b><p><?php echo s($ises ? 'No atrapes patógeno, Humedad-Calor, Flema, estasis o Calor pleno.' : 'Do not trap a pathogen, Damp-Heat, Phlegm, stasis, or full Heat.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Revisa seguridad' : 'Check safety'); ?></b><p><?php echo s($ises ? 'Identifica urgencias y sustancias obsoletas, tóxicas, animales o minerales.' : 'Identify emergencies and obsolete, toxic, animal-derived, or mineral substances.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-nine-board-title">
                <div>
                    <h4 id="chapter-nine-board-title"><?php echo s($ises ? 'Comprobación contra Appendix C 2026' : 'Check against the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco identifica cada fórmula incluida entre las 181 oficiales. Las fichas sin asterisco se conservan como ampliación académica de Bensky.' : 'An asterisk identifies every formula included among the 181 official entries. Unstarred profiles remain as an academic Bensky expansion.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Appendix C →' : 'Verify Appendix C →'); ?></a>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-nine-directory-title">
                <h4 id="chapter-nine-directory-title"><?php echo s($ises ? 'Listado completo del capítulo 9' : 'Complete Chapter 9 directory'); ?></h4>
                <p><?php echo s($ises ? 'Las 17 fórmulas están ordenadas en las cinco secciones de la fuente. Selecciona una para ir a su ficha.' : 'The 17 formulas follow the source order in five sections. Select one to jump to its card.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($stabilizechaptersections as $sectionkey => $section): ?>
                        <?php $sectionformulas = array_values(array_filter($selected['formulas'], static fn(array $formula): bool => $formula['chapterSectionKey'] === $sectionkey)); ?>
                        <section class="formula-study-guide__chapter-directory-group">
                            <h5><a href="#chapter-section-<?php echo s($sectionkey); ?>"><?php echo s($ises ? $section['nameEs'] : $section['name']); ?> · <?php echo count($sectionformulas); ?></a></h5>
                            <ol>
                                <?php foreach ($sectionformulas as $chapterformula): ?>
                                    <?php $chapterformulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $chapterformula['number']); ?>
                                    <li>
                                        <a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo $renderformulanamewithenglish($chapterformula['name']); ?></a>
                                        <?php if (is_numeric($chapterformula['number'])): ?><span class="formula-study-guide__domain-badge"><?php echo s(($ises ? 'Ficha del núcleo #' : 'Core profile #') . $chapterformula['number']); ?></span><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'calm-shen-chapter-10'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 10.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 10.'); ?></p>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-ten-principles-title">
                <h4 id="chapter-ten-principles-title"><?php echo s($ises ? 'Principio central: identifica primero raíz, exceso y sustancia que perturba el Shen' : 'Core principle: first identify the root, excess, and substance disturbing the Spirit'); ?></h4>
                <p><?php echo s($ises
                    ? 'La perturbación del Shen puede surgir de deficiencia de Qi, Sangre o Yin, o de Fuego, Flema y Yang ascendente. La presentación suele ser mixta. Las fórmulas históricas con minerales pesados no son equivalentes a tranquilizantes modernos y nunca justifican usar cinabrio, cuerno de rinoceronte u otras sustancias prohibidas.'
                    : 'Spirit disturbance may arise from Qi, Blood, or Yin deficiency, or from Fire, Phlegm, and ascendant Yang. Presentations are often mixed. Historical heavy-mineral formulas are not equivalents of modern tranquilizers and never justify using cinnabar, rhinoceros horn, or other prohibited substances.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Define deficiencia o exceso' : 'Define deficiency or excess'); ?></b><p><?php echo s($ises ? 'Qi, Sangre y Yin frente a Fuego, Flema o Yang ascendente.' : 'Qi, Blood, and Yin versus Fire, Phlegm, or ascendant Yang.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Busca los síntomas clave' : 'Find the key symptoms'); ?></b><p><?php echo s($ises ? 'Sudor nocturno, miedo, saburra grasosa, tinnitus, mareo o signos sensoriales.' : 'Night sweating, fright, greasy coat, tinnitus, dizziness, or sensory signs.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Distingue la estrategia' : 'Distinguish the strategy'); ?></b><p><?php echo s($ises ? 'Nutrir y armonizar frente a asentar, aclarar Fuego y transformar Flema.' : 'Nourish and harmonize versus settle, clear Fire, and transform Phlegm.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Aplica el filtro de seguridad' : 'Apply the safety filter'); ?></b><p><?php echo s($ises ? 'Excluye cinabrio, especies amenazadas y cuadros psiquiátricos o neurológicos urgentes.' : 'Exclude cinnabar, endangered species, and urgent psychiatric or neurologic presentations.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-ten-board-title">
                <div>
                    <h4 id="chapter-ten-board-title"><?php echo s($ises ? 'Comprobación contra Appendix C 2026' : 'Check against the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco identifica cada fórmula incluida entre las 181 oficiales. Las fichas sin asterisco se conservan como ampliación académica de Bensky.' : 'An asterisk identifies every formula included among the 181 official entries. Unstarred profiles remain as an academic Bensky expansion.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Appendix C →' : 'Verify Appendix C →'); ?></a>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-ten-directory-title">
                <h4 id="chapter-ten-directory-title"><?php echo s($ises ? 'Listado completo del capítulo 10' : 'Complete Chapter 10 directory'); ?></h4>
                <p><?php echo s($ises ? 'Las nueve fórmulas siguen las dos secciones de la fuente. Las fórmulas y variaciones asociadas aparecen dentro de la ficha principal correspondiente.' : 'The nine formulas follow the source’s two sections. Associated formulas and named variations appear within the corresponding principal card.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($calmshenchaptersections as $sectionkey => $section): ?>
                        <?php $sectionformulas = array_values(array_filter($selected['formulas'], static fn(array $formula): bool => $formula['chapterSectionKey'] === $sectionkey)); ?>
                        <section class="formula-study-guide__chapter-directory-group">
                            <h5><a href="#chapter-section-<?php echo s($sectionkey); ?>"><?php echo s($ises ? $section['nameEs'] : $section['name']); ?> · <?php echo count($sectionformulas); ?></a></h5>
                            <ol>
                                <?php foreach ($sectionformulas as $chapterformula): ?>
                                    <?php $chapterformulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $chapterformula['number']); ?>
                                    <li>
                                        <a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo $renderformulanamewithenglish($chapterformula['name']); ?></a>
                                        <?php if (is_numeric($chapterformula['number'])): ?><span class="formula-study-guide__domain-badge"><?php echo s(($ises ? 'Ficha del núcleo #' : 'Core profile #') . $chapterformula['number']); ?></span><?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'open-orifices-chapter-11'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 11.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 11.'); ?></p>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-eleven-principles-title">
                <h4 id="chapter-eleven-principles-title"><?php echo s($ises ? 'Principio central: confirma cierre y separa inmediatamente Calor de Frío' : 'Core principle: confirm closure and immediately separate Heat from Cold'); ?></h4>
                <p><?php echo s($ises
                    ? 'Estas fórmulas históricas tratan trastornos cerrados agudos por obstrucción de Flema, Calor, Frío o Qi, no cuadros tipo abandono por colapso de Qi y Yang. La pérdida súbita de conciencia, convulsiones, coma, fiebre alta o dificultad respiratoria son urgencias médicas. Las fichas son para identificación académica y excluyen metales tóxicos, especies amenazadas y procedimientos nasales peligrosos.'
                    : 'These historical formulas address acute closed disorders from obstruction by Phlegm, Heat, Cold, or Qi—not abandoned-type collapse of Qi and Yang. Sudden loss of consciousness, seizures, coma, high fever, or breathing difficulty are medical emergencies. The cards are for academic identification and exclude toxic metals, endangered species, and hazardous nasal procedures.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Cierre o abandono' : 'Closed or abandoned'); ?></b><p><?php echo s($ises ? 'Mandíbula cerrada y pulso fuerte indican cierre; boca abierta, sudor y pulso tenue sugieren abandono.' : 'Clenched jaw and forceful pulse indicate closure; open mouth, sweating, and a faint pulse suggest abandonment.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Caliente o frío' : 'Hot or cold'); ?></b><p><?php echo s($ises ? 'Lengua roja y pulso rápido frente a tez pálida, extremidades frías, saburra blanca y pulso sumergido.' : 'Red tongue and rapid pulse versus pale complexion, cold limbs, white coat, and submerged pulse.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Encuentra el predominio' : 'Find the dominant feature'); ?></b><p><?php echo s($ises ? 'Calor-Toxicidad, Flema turbia, Viento con convulsiones, estancamiento de Qi o contexto pediátrico.' : 'Heat-toxin, turbid Phlegm, Wind with convulsions, Qi constraint, or pediatric context.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Aplica seguridad crítica' : 'Apply critical safety'); ?></b><p><?php echo s($ises ? 'No usar cinabrio, rejalgar, cuerno de rinoceronte, tortuga carey ni otras sustancias prohibidas o no verificadas.' : 'Do not use cinnabar, realgar, rhinoceros horn, hawksbill turtle, or other prohibited or unverified substances.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-eleven-board-title">
                <div>
                    <h4 id="chapter-eleven-board-title"><?php echo s($ises ? 'Comprobación contra Appendix C 2026' : 'Check against the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco identifica cualquier fórmula incluida entre las 181 oficiales. Las fichas sin asterisco se conservan para reconocimiento académico y seguridad, no como expansión del alcance oficial.' : 'An asterisk identifies any formula included among the 181 official entries. Unstarred profiles are retained for academic recognition and safety, not as an expansion of official scope.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Appendix C →' : 'Verify Appendix C →'); ?></a>
            </section>
            <section class="formula-study-guide__scope-analysis" aria-labelledby="chapter-eleven-scope-analysis-title">
                <header class="formula-study-guide__scope-analysis-header">
                    <h4 id="chapter-eleven-scope-analysis-title"><?php echo s($ises ? 'Por qué podrían estar fuera del temario vigente' : 'Why they may be outside the current exam scope'); ?></h4>
                    <p><?php echo s($ises
                        ? 'El contraste entre la composición histórica de estas fórmulas y los criterios modernos de seguridad muestra varios factores que pueden ayudar a explicar su ausencia.'
                        : 'Comparing the historical composition of these formulas with modern safety standards reveals several factors that may help explain their absence.'); ?></p>
                    <p class="formula-study-guide__scope-analysis-caveat"><strong><?php echo s($ises ? 'Límite de la conclusión:' : 'Limit of the conclusion:'); ?></strong> <?php echo s($ises
                        ? 'NCBAHM publica el listado, pero no una justificación individual para cada omisión. Lo siguiente es un análisis documentado y plausible, no una explicación oficial del Board.'
                        : 'NCBAHM publishes the roster but not an individual rationale for every omission. The following is a documented, plausible analysis, not an official Board explanation.'); ?></p>
                </header>
                <div class="formula-study-guide__table-wrap">
                    <table class="formula-study-guide__table" data-formula-context-table data-table-narration="<?php echo s($ises
                        ? 'Esta tabla resume los factores que pueden explicar por qué ciertas fórmulas no forman parte del temario vigente. Relaciona cada factor con la evidencia del capítulo y con su relevancia actual.'
                        : 'This table summarizes factors that may explain why certain formulas are outside the current syllabus. It connects each factor with chapter evidence and its current relevance.'); ?>">
                        <thead><tr><th><?php echo $ises ? 'Factor:' : 'Factor:'; ?></th><th><?php echo $ises ? 'Evidencia en el capítulo:' : 'Chapter evidence:'; ?></th><th><?php echo $ises ? 'Relevancia actual:' : 'Current relevance:'; ?></th></tr></thead>
                        <tbody>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">5/7:</span> <?php echo s($ises ? 'Metales tóxicos históricos' : 'Historical toxic metals'); ?></td>
                                <td><?php echo s($ises ? 'An Gong Niu Huang Wan, Zi Xue Dan, Zhi Bao Dan, Hui Chun Dan y Su He Xiang Wan incluían Zhu Sha/cinabrio; An Gong Niu Huang Wan y Zhi Bao Dan también Xiong Huang/rejalgar.' : 'An Gong Niu Huang Wan, Zi Xue Dan, Zhi Bao Dan, Hui Chun Dan, and Su He Xiang Wan historically included Zhu Sha/cinnabar; An Gong Niu Huang Wan and Zhi Bao Dan also included Xiong Huang/realgar.'); ?></td>
                                <td><?php echo s($ises ? 'Cinabrio aporta mercurio y rejalgar arsénico. El apéndice D del guideline es el listado de sustancias obsoletas; allí figura Zhu Sha. Por esta razón, los perfiles modernos de esta guía excluyen ambas sustancias.' : 'Cinnabar contributes mercury and realgar arsenic. Appendix D of the guideline is the list of obsolete substances; Zhu Sha appears there. For this reason, the modern profiles in this guide exclude both substances.'); ?></td>
                            </tr>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">4/7:</span> <?php echo s($ises ? 'Especies protegidas o prohibidas' : 'Protected or prohibited species'); ?></td>
                                <td><?php echo s($ises ? 'Cuatro fórmulas usaban históricamente Xi Jiao/cuerno de rinoceronte; Zhi Bao Dan también Dai Mao/tortuga carey. Zi Xue Dan plantea además la procedencia de Ling Yang Jiao.' : 'Four formulas historically used Xi Jiao/rhinoceros horn; Zhi Bao Dan also used Dai Mao/hawksbill turtle. Zi Xue Dan additionally raises sourcing concerns for Ling Yang Jiao.'); ?></td>
                                <td><?php echo s($ises ? 'La protección de especies y las restricciones comerciales obligan a excluir o sustituir componentes, por lo que la fórmula moderna ya no es idéntica a la clásica.' : 'Species protection and trade restrictions require components to be excluded or substituted, so the modern formula is no longer identical to the classical one.'); ?></td>
                            </tr>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">6/7:</span> <?php echo s($ises ? 'Ingredientes animales o regulados' : 'Animal-derived or regulated ingredients'); ?></td>
                                <td><?php echo s($ises ? 'Niu Huang, She Xiang, Shui Niu Jiao y otros ingredientes animales aparecen en seis perfiles; Chen Xiang también requiere procedencia legal y sostenible.' : 'Niu Huang, She Xiang, Shui Niu Jiao, and other animal-derived ingredients appear across six profiles; Chen Xiang also requires legal, sustainable sourcing.'); ?></td>
                                <td><?php echo s($ises ? 'Identidad, sustitución, control de calidad, legalidad y abastecimiento ético dificultan estandarizar estas fórmulas para un listado nacional.' : 'Identity, substitution, quality control, legality, and ethical sourcing complicate standardization for a national roster.'); ?></td>
                            </tr>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">1/7:</span> <?php echo s($ises ? 'Vía de administración peligrosa' : 'Hazardous administration route'); ?></td>
                                <td><?php echo s($ises ? 'Tong Guan San se insuflaba en la nariz para inducir estornudos y contenía Xi Xin.' : 'Tong Guan San was insufflated into the nose to induce sneezing and contained Xi Xin.'); ?></td>
                                <td><?php echo s($ises ? 'Existe riesgo de irritación y aspiración pulmonar; Xi Xin exige además control estricto de identidad y dosis. La guía lo conserva solo para reconocimiento histórico.' : 'There is a risk of irritation and pulmonary aspiration; Xi Xin also requires strict identity and dose control. The guide retains it only for historical recognition.'); ?></td>
                            </tr>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">7/7:</span> <?php echo s($ises ? 'Uso en cuadros de urgencia' : 'Emergency presentations'); ?></td>
                                <td><?php echo s($ises ? 'Las siete se relacionan con alteración de conciencia, convulsiones, colapso, fiebre grave, dificultad respiratoria u otros cuadros cerrados agudos.' : 'All seven are associated with impaired consciousness, seizures, collapse, severe fever, breathing difficulty, or other acute closed disorders.'); ?></td>
                                <td><?php echo s($ises ? 'Son situaciones que requieren servicios de emergencia y reducen el papel de estas prescripciones históricas en la práctica moderna y en un temario de selección terapéutica habitual.' : 'These situations require emergency services and reduce the role of these historical prescriptions in modern practice and in a routine therapeutic-selection syllabus.'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-eleven-directory-title">
                <h4 id="chapter-eleven-directory-title"><?php echo s($ises ? 'Listado completo del capítulo 11' : 'Complete Chapter 11 directory'); ?></h4>
                <p><?php echo s($ises ? 'Las siete fórmulas principales siguen las dos secciones de la fuente. Ninguna lleva asterisco porque ninguna pertenece al Appendix C vigente. Niu Huang Cheng Qi Tang aparece como variación de An Gong Niu Huang Wan y Bao Long Wan como fórmula asociada de Hui Chun Dan.' : 'The seven principal formulas follow the source’s two sections. None carries an asterisk because none belongs to the current Appendix C. Niu Huang Cheng Qi Tang appears as a variation of An Gong Niu Huang Wan, and Bao Long Wan as an associated formula of Hui Chun Dan.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($openorificeschaptersections as $sectionkey => $section): ?>
                        <?php $sectionformulas = array_values(array_filter($selected['formulas'], static fn(array $formula): bool => $formula['chapterSectionKey'] === $sectionkey)); ?>
                        <section class="formula-study-guide__chapter-directory-group">
                            <h5><a href="#chapter-section-<?php echo s($sectionkey); ?>"><?php echo s($ises ? $section['nameEs'] : $section['name']); ?> · <?php echo count($sectionformulas); ?></a></h5>
                            <ol>
                                <?php foreach ($sectionformulas as $chapterformula): ?>
                                    <?php $chapterformulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $chapterformula['number']); ?>
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo $renderformulanamewithenglish($chapterformula['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'regulate-qi-chapter-12'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 12.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 12.'); ?></p>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-twelve-principles-title">
                <h4 id="chapter-twelve-principles-title"><?php echo s($ises ? 'Principio central: localiza el bloqueo y sigue la dirección del Qi' : 'Core principle: locate the obstruction and follow the direction of Qi'); ?></h4>
                <p><?php echo s($ises
                    ? 'Primero determina si el problema dominante es Qi estancado que necesita movimiento o Qi rebelde que necesita descenso. Después localiza Hígado, pecho, garganta, Jiao Medio, Pulmón o Estómago y confirma Frío, Calor, Flema, Humedad, Sangre, alimento o deficiencia.'
                    : 'First decide whether the dominant problem is stagnant Qi that needs movement or rebellious Qi that needs descent. Then locate the Liver, chest, throat, Middle Burner, Lung, or Stomach and confirm Cold, Heat, Phlegm, Dampness, Blood, food, or deficiency.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Movimiento o descenso' : 'Movement or descent'); ?></b><p><?php echo s($ises ? 'Dolor y distensión apuntan a estancamiento; tos, sibilancia, hipo o vómito indican rebelión.' : 'Pain and distention point to stagnation; cough, wheezing, hiccup, or vomiting indicate rebellion.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Localiza el órgano' : 'Locate the organ'); ?></b><p><?php echo s($ises ? 'Hígado y canales, pecho y garganta, Pulmón o Estómago.' : 'Liver and channels, chest and throat, Lung, or Stomach.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Identifica el acompañante' : 'Identify the companion'); ?></b><p><?php echo s($ises ? 'Frío, Calor, Flema, Humedad, Sangre, alimento, sequedad o deficiencia.' : 'Cold, Heat, Phlegm, Dampness, Blood, food, dryness, or deficiency.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Usa los síntomas clave' : 'Use the key symptoms'); ?></b><p><?php echo s($ises ? 'Momento del vómito, tipo de esputo, sed, lengua, pulso y relación con emociones, comida o frío.' : 'Timing of vomiting, sputum quality, thirst, tongue, pulse, and relationship to emotion, food, or Cold.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-twelve-board-title">
                <div>
                    <h4 id="chapter-twelve-board-title"><?php echo s($ises ? 'Comprobación contra Appendix C 2026' : 'Check against the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco identifica cada fórmula incluida entre las 181 oficiales. La pertenencia oficial se calcula desde el listado completo y no desde las cinco fichas internas originales de esta categoría.' : 'An asterisk identifies every formula included among the 181 official entries. Official membership is calculated from the complete roster, not from this category’s five original internal profiles.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Appendix C →' : 'Verify Appendix C →'); ?></a>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-twelve-directory-title">
                <h4 id="chapter-twelve-directory-title"><?php echo s($ises ? 'Listado completo del capítulo 12' : 'Complete Chapter 12 directory'); ?></h4>
                <p><?php echo s($ises ? 'Las 20 fórmulas principales siguen las dos secciones de la fuente. Las fórmulas asociadas y variaciones con nombre propio se conservan dentro de la ficha principal correspondiente.' : 'The 20 principal formulas follow the source’s two sections. Associated formulas and named variations remain inside the corresponding principal card.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($regulateqichaptersections as $sectionkey => $section): ?>
                        <?php $sectionformulas = array_values(array_filter($selected['formulas'], static fn(array $formula): bool => $formula['chapterSectionKey'] === $sectionkey)); ?>
                        <section class="formula-study-guide__chapter-directory-group">
                            <h5><a href="#chapter-section-<?php echo s($sectionkey); ?>"><?php echo s($ises ? $section['nameEs'] : $section['name']); ?> · <?php echo count($sectionformulas); ?></a></h5>
                            <ol>
                                <?php foreach ($sectionformulas as $chapterformula): ?>
                                    <?php $chapterformulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $chapterformula['number']); ?>
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo $renderformulanamewithenglish($chapterformula['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'regulate-blood-chapter-13'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 13.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 13.'); ?></p>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-thirteen-principles-title">
                <h4 id="chapter-thirteen-principles-title"><?php echo s($ises ? 'Principio central: regula el movimiento de la Sangre sin perder de vista la raíz' : 'Core principle: regulate Blood movement without losing sight of the root'); ?></h4>
                <p><?php echo s($ises
                    ? 'La primera sección trata Sangre que no circula; la segunda, Sangre que abandona sus vasos. En ambos casos se localiza el trastorno, se distingue Calor de Frío y exceso de deficiencia, y se evita que mover o contener la Sangre agrave el sangrado, la estasis o el daño de Yin.'
                    : 'The first section treats Blood that does not circulate; the second treats Blood that leaves its vessels. In both, locate the disorder, distinguish Heat from Cold and excess from deficiency, and avoid letting movement or restraint worsen bleeding, stasis, or Yin injury.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Estasis o sangrado' : 'Stasis or bleeding'); ?></b><p><?php echo s($ises ? 'Dolor fijo, masas y color violáceo indican estasis; Sangre fuera de los vasos exige localizar la hemorragia.' : 'Fixed pain, masses, and purple color indicate stasis; Blood outside the vessels requires locating the hemorrhage.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Localiza' : 'Locate'); ?></b><p><?php echo s($ises ? 'Jiao Inferior, pecho, canales, útero, Pulmón, Intestinos, Vejiga o Jiao Medio.' : 'Lower Burner, chest, channels, uterus, Lung, Intestines, Bladder, or Middle Burner.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Temperatura y fuerza' : 'Temperature and strength'); ?></b><p><?php echo s($ises ? 'Calor o Frío; exceso, estasis, deficiencia de Qi/Sangre/Yin o Yang debilitado.' : 'Heat or Cold; excess, stasis, Qi/Blood/Yin deficiency, or weakened Yang.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Prioriza la urgencia' : 'Prioritize urgency'); ?></b><p><?php echo s($ises ? 'Sangrado agudo, abundante, respiratorio, digestivo, urinario, obstétrico o con inestabilidad requiere atención médica inmediata.' : 'Acute, heavy, respiratory, gastrointestinal, urinary, obstetric, or unstable bleeding requires immediate medical care.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-thirteen-board-title">
                <div>
                    <h4 id="chapter-thirteen-board-title"><?php echo s($ises ? 'Comprobación contra Appendix C 2026' : 'Check against the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco identifica cada fórmula incluida entre las 181 oficiales. Las demás fichas completan el capítulo de Bensky sin ampliar el alcance oficial.' : 'An asterisk identifies every formula included among the 181 official entries. The remaining profiles complete the Bensky chapter without expanding official scope.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Appendix C →' : 'Verify Appendix C →'); ?></a>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-thirteen-directory-title">
                <h4 id="chapter-thirteen-directory-title"><?php echo s($ises ? 'Listado completo del capítulo 13' : 'Complete Chapter 13 directory'); ?></h4>
                <p><?php echo s($ises ? 'Las 23 fórmulas principales siguen las dos secciones de la fuente. Las fórmulas asociadas y variaciones con nombre propio permanecen dentro de la ficha principal correspondiente.' : 'The 23 principal formulas follow the source’s two sections. Associated formulas and named variations remain inside the corresponding principal card.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($regulatebloodchaptersections as $sectionkey => $section): ?>
                        <?php $sectionformulas = array_values(array_filter($selected['formulas'], static fn(array $formula): bool => $formula['chapterSectionKey'] === $sectionkey)); ?>
                        <section class="formula-study-guide__chapter-directory-group">
                            <h5><a href="#chapter-section-<?php echo s($sectionkey); ?>"><?php echo s($ises ? $section['nameEs'] : $section['name']); ?> · <?php echo count($sectionformulas); ?></a></h5>
                            <ol>
                                <?php foreach ($sectionformulas as $chapterformula): ?>
                                    <?php $chapterformulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $chapterformula['number']); ?>
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo $renderformulanamewithenglish($chapterformula['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'expel-damp-chapter-16'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 16.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 16.'); ?></p>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-sixteen-principles-title">
                <h4 id="chapter-sixteen-principles-title"><?php echo s($ises ? 'Principio central: identifica qué impide transformar, mover o eliminar la Humedad' : 'Core principle: identify what prevents Dampness from being transformed, moved, or eliminated'); ?></h4>
                <p><?php echo s($ises
                    ? 'La Humedad puede obstruir el Jiao Medio, combinarse con Calor, bloquear las vías del agua, surgir de Yang deficiente o alojarse con Viento en canales. Antes de elegir fórmula, determina temperatura, localización, exceso o deficiencia y la vía de salida apropiada.'
                    : 'Dampness may obstruct the Middle Burner, combine with Heat, block the water pathways, arise from deficient Yang, or lodge with Wind in the channels. Before choosing a formula, determine temperature, location, excess or deficiency, and the appropriate route of exit.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Localiza' : 'Locate'); ?></b><p><?php echo s($ises ? 'Jiao Medio, Triple Jiao, piel, vías urinarias, Jiao Inferior o canales.' : 'Middle Burner, Triple Burner, skin, urinary pathways, Lower Burner, or channels.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Temperatura' : 'Temperature'); ?></b><p><?php echo s($ises ? 'Calor, Frío, Frío que genera Calor por constricción o ausencia de signos térmicos fuertes.' : 'Heat, Cold, Cold generating constrained Heat, or no strong thermal signs.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Raíz y rama' : 'Root and branch'); ?></b><p><?php echo s($ises ? 'Exceso patógeno, Qi estancado, Qi protector débil, Yang de Bazo-Riñón deficiente o deficiencia de Sangre/Hígado-Riñón.' : 'Pathogenic excess, stagnant Qi, weak protective Qi, deficient Spleen-Kidney Yang, or Blood/Liver-Kidney deficiency.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Elige la salida' : 'Choose the exit'); ?></b><p><?php echo s($ises ? 'Transformar, drenar por orina, mover Qi, calentar, dispersar por canales o combinar varios métodos.' : 'Transform, drain through urine, move Qi, warm, disperse through channels, or combine methods.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">5</span><?php echo s($ises ? 'Comprueba seguridad' : 'Check safety'); ?></b><p><?php echo s($ises ? 'Edema, retención urinaria, ictericia, fiebre alta, articulación roja aguda o fórmulas con aconito exigen evaluación clínica.' : 'Edema, urinary retention, jaundice, high fever, an acutely red joint, or aconite formulas require clinical assessment.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-sixteen-board-title">
                <div>
                    <h4 id="chapter-sixteen-board-title"><?php echo s($ises ? 'Comprobación contra Appendix C 2026' : 'Check against the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco identifica cada fórmula incluida entre las 181 oficiales. Las demás fichas completan el capítulo de Bensky sin ampliar el alcance oficial.' : 'An asterisk identifies every formula included among the 181 official entries. The remaining profiles complete the Bensky chapter without expanding official scope.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Appendix C →' : 'Verify Appendix C →'); ?></a>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-sixteen-directory-title">
                <h4 id="chapter-sixteen-directory-title"><?php echo s($ises ? 'Listado completo del capítulo 16' : 'Complete Chapter 16 directory'); ?></h4>
                <p><?php echo s($ises ? 'Las 33 fórmulas principales siguen las cinco secciones de la fuente. El asterisco identifica cuáles pertenecen al Appendix C oficial.' : 'The 33 principal formulas follow the source’s five sections. An asterisk identifies those that belong to official Appendix C.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($expeldampchaptersections as $sectionkey => $section): ?>
                        <?php $sectionformulas = array_values(array_filter($selected['formulas'], static fn(array $formula): bool => $formula['chapterSectionKey'] === $sectionkey)); ?>
                        <section class="formula-study-guide__chapter-directory-group">
                            <h5><a href="#chapter-section-<?php echo s($sectionkey); ?>"><?php echo s($ises ? $section['nameEs'] : $section['name']); ?> · <?php echo count($sectionformulas); ?></a></h5>
                            <ol>
                                <?php foreach ($sectionformulas as $chapterformula): ?>
                                    <?php $chapterformulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $chapterformula['number']); ?>
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo $renderformulanamewithenglish($chapterformula['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'expel-wind-chapter-14'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 14.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 14.'); ?></p>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-fourteen-principles-title">
                <h4 id="chapter-fourteen-principles-title"><?php echo s($ises ? 'Principio central: localiza el Viento y determina qué lo impulsa' : 'Core principle: locate the Wind and determine what drives it'); ?></h4>
                <p><?php echo s($ises
                    ? 'El Viento externo invade piel o canales y suele combinarse con Frío, Calor, Humedad o Flema. El Viento interno surge de Calor extremo, ascenso de Yang o agotamiento de Sangre, Yin y líquidos. La fórmula correcta depende de tratar simultáneamente la manifestación móvil y su raíz.'
                    : 'External Wind invades the skin or channels and commonly combines with Cold, Heat, Dampness, or Phlegm. Internal Wind arises from extreme Heat, rising Yang, or depletion of Blood, Yin, and fluids. The correct formula treats both the moving manifestation and its root.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Externo o interno' : 'External or internal'); ?></b><p><?php echo s($ises ? 'Piel, nariz y canales apuntan a invasión; convulsión, vértigo y movimiento involuntario exigen buscar raíz interna.' : 'Skin, nose, and channels point to invasion; seizure, vertigo, and involuntary movement require an internal-root analysis.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Exceso o deficiencia' : 'Excess or deficiency'); ?></b><p><?php echo s($ises ? 'Calor alto y pulso fuerte indican exceso; lengua pelada, sequedad y pulso frágil indican agotamiento.' : 'High fever and a forceful pulse indicate excess; a peeled tongue, dryness, and frail pulse indicate depletion.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Identifica la raíz' : 'Identify the root'); ?></b><p><?php echo s($ises ? 'Frío, Calor, Flema, Humedad, Yang ascendente o deficiencia de Sangre-Yin-líquidos.' : 'Cold, Heat, Phlegm, Dampness, rising Yang, or Blood-Yin-fluid deficiency.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Reconoce urgencias' : 'Recognize emergencies'); ?></b><p><?php echo s($ises ? 'Ictus, convulsión, pérdida de conciencia, tétanos, meningitis o crisis hipertensiva requieren atención inmediata.' : 'Stroke, seizure, loss of consciousness, tetanus, meningitis, or hypertensive crisis requires immediate care.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-fourteen-board-title">
                <div>
                    <h4 id="chapter-fourteen-board-title"><?php echo s($ises ? 'Comprobación contra Appendix C 2026' : 'Check against the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco identifica cada fórmula incluida entre las 181 oficiales. Las demás fichas completan el capítulo de Bensky sin ampliar el alcance oficial.' : 'An asterisk identifies every formula included among the 181 official entries. The remaining profiles complete the Bensky chapter without expanding official scope.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Appendix C →' : 'Verify Appendix C →'); ?></a>
            </section>
            <section class="formula-study-guide__medical-rationale" aria-labelledby="chapter-fourteen-medical-rationale-title">
                <div class="formula-study-guide__medical-rationale-header">
                    <h4 id="chapter-fourteen-medical-rationale-title"><?php echo s($ises ? 'Explicación médica: por qué estas tres son representativas' : 'Medical explanation: why these three are representative'); ?></h4>
                    <p><?php echo s($ises
                        ? 'En medicina china, “Viento” no designa una sola enfermedad. Describe manifestaciones súbitas, cambiantes o móviles—como prurito migratorio, espasmo, temblor y vértigo—y obliga a preguntar dónde se manifiesta y qué mecanismo lo impulsa.'
                        : 'In Chinese medicine, “Wind” does not name a single disease. It describes sudden, changing, or moving manifestations—such as migrating itch, spasm, tremor, and vertigo—and requires asking where it appears and what mechanism drives it.'); ?></p>
                    <p><?php echo s($ises
                        ? 'Las tres fichas detalladas del núcleo condensan tres territorios clínicos amplios: piel, canales dolorosos e Interior. Juntas permiten evaluar localización, raíz y manifestación sin convertir todo el capítulo clásico en contenido prioritario de examen.'
                        : 'The three detailed core profiles condense three broad clinical territories: skin, painful channels, and the Interior. Together they test location, root, and manifestation without making the entire classical chapter a primary exam priority.'); ?></p>
                </div>
                <div class="formula-study-guide__table-wrap">
                    <table class="formula-study-guide__table" data-formula-context-table data-table-narration="<?php echo s($ises
                        ? 'Esta tabla compara tres fórmulas representativas según el lugar donde se manifiesta el Viento, el mecanismo responsable y la presentación clínica característica.'
                        : 'This table compares three representative formulas by where Wind manifests, the responsible mechanism, and the characteristic clinical presentation.'); ?>">
                        <thead>
                            <tr>
                                <th><?php echo $ises ? 'Fórmula:' : 'Formula:'; ?></th>
                                <th><?php echo $ises ? 'Dónde se manifiesta:' : 'Where it manifests:'; ?></th>
                                <th><?php echo $ises ? 'Mecanismo:' : 'Mechanism:'; ?></th>
                                <th><?php echo $ises ? 'Presentación característica:' : 'Characteristic presentation:'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $renderformulanamewithenglish('Xiao Feng San'); ?></td>
                                <td><?php echo s($ises ? 'Piel' : 'Skin'); ?></td>
                                <td><?php echo s($ises ? 'Viento-Humedad-Calor con Sequedad de Sangre subyacente' : 'Wind-Damp-Heat with underlying Blood dryness'); ?></td>
                                <td><?php echo s($ises ? 'Erupción roja, exudativa, cambiante y muy pruriginosa' : 'Red, weeping, changing, intensely itchy eruption'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $renderformulanamewithenglish('Xiao Huo Luo Dan'); ?></td>
                                <td><?php echo s($ises ? 'Canales y articulaciones' : 'Channels and joints'); ?></td>
                                <td><?php echo s($ises ? 'Viento-Frío-Humedad, Flema y estasis de Sangre' : 'Wind-Cold-Dampness, Phlegm, and Blood stasis'); ?></td>
                                <td><?php echo s($ises ? 'Dolor crónico agravado por frío, rigidez, entumecimiento y movilidad reducida' : 'Chronic cold-aggravated pain, stiffness, numbness, and reduced movement'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo $renderformulanamewithenglish('Tian Ma Gou Teng Yin'); ?></td>
                                <td><?php echo s($ises ? 'Interior · Hígado' : 'Interior · Liver'); ?></td>
                                <td><?php echo s($ises ? 'Ascenso de Yang de Hígado que genera Viento interno' : 'Rising Liver Yang generating internal Wind'); ?></td>
                                <td><?php echo s($ises ? 'Cefalea, vértigo, tinnitus e insomnio; en casos graves, temblor o espasmo' : 'Headache, vertigo, tinnitus, and insomnia; in severe cases, tremor or spasm'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="formula-study-guide__medical-rationale-header formula-study-guide__medical-rationale-secondary">
                    <h4><?php echo s($ises ? 'Por qué las otras 14 siguen siendo importantes' : 'Why the other 14 still matter'); ?></h4>
                    <p><?php echo s($ises
                        ? 'Bensky conserva el mapa clásico completo. Las fórmulas restantes incluyen cuadros neurológicos agudos que hoy son urgencias; ingredientes históricos tóxicos; materiales animales protegidos o restringidos; y patrones muy especializados de agotamiento de Yin después de enfermedades febriles graves.'
                        : 'Bensky preserves the complete classical map. The remaining formulas include acute neurologic presentations that are emergencies today; historically toxic ingredients; protected or restricted animal materials; and highly specialized Yin-depletion patterns after severe febrile disease.'); ?></p>
                    <p><?php echo s($ises
                        ? 'Por eso son valiosas para comprender diagnóstico diferencial, historia, seguridad y límites clínicos. El asterisco distingue las que sí pertenecen al Appendix C oficial de 181 fórmulas. Ictus, convulsiones, pérdida de conciencia, tétanos, meningitis o crisis hipertensiva requieren atención médica inmediata; una fórmula clásica no sustituye el tratamiento agudo.'
                        : 'They therefore remain valuable for understanding differential diagnosis, history, safety, and clinical limits. The asterisk distinguishes those that belong to the official 181-formula Appendix C. Stroke, seizure, loss of consciousness, tetanus, meningitis, or hypertensive crisis require immediate medical care; a classical formula does not replace acute treatment.'); ?></p>
                    <p class="formula-study-guide__medical-rationale-caveat"><?php echo s($ises
                        ? 'Nota de alcance: el Board no publica una justificación médica fórmula por fórmula. Esta es una interpretación clínica y educativa basada en la cobertura de patrones, la aplicabilidad y la seguridad; no una declaración oficial del Board.'
                        : 'Scope note: the Board does not publish a formula-by-formula medical rationale. This is a clinical and educational interpretation based on pattern coverage, applicability, and safety—not an official Board statement.'); ?></p>
                </div>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-fourteen-directory-title">
                <h4 id="chapter-fourteen-directory-title"><?php echo s($ises ? 'Listado completo del capítulo 14' : 'Complete Chapter 14 directory'); ?></h4>
                <p><?php echo s($ises ? 'Las 3 fichas detalladas del núcleo aparecen primero. Después siguen las otras 14 fórmulas, organizadas según las dos secciones de la fuente. Las fórmulas asociadas y variaciones permanecen dentro de su ficha principal.' : 'The 3 detailed core profiles appear first. The other 14 formulas then follow, organized by the source’s two sections. Associated formulas and variations remain inside their principal card.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($expelwindchaptersections as $sectionkey => $section): ?>
                        <?php $sectionformulas = array_values(array_filter($selected['formulas'], static fn(array $formula): bool => $formula['chapterSectionKey'] === $sectionkey)); ?>
                        <section class="formula-study-guide__chapter-directory-group">
                            <h5><a href="#chapter-section-<?php echo s($sectionkey); ?>"><?php echo s($ises ? $section['nameEs'] : $section['name']); ?> · <?php echo count($sectionformulas); ?></a></h5>
                            <ol>
                                <?php foreach ($sectionformulas as $chapterformula): ?>
                                    <?php $chapterformulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $chapterformula['number']); ?>
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo $renderformulanamewithenglish($chapterformula['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'warm-interior'): ?>
            <section class="formula-study-guide__chapter-shortcut" aria-labelledby="chapter-six-shortcut-title">
                <h4 id="chapter-six-shortcut-title"><?php echo s($ises ? 'Ampliación del capítulo 6 · ' : 'Chapter 6 expansion · '); ?><?php echo count($warmchaptercategory['formulas']); ?> <?php echo $ises ? 'fórmulas' : 'formulas'; ?></h4>
                <p><?php echo s($ises
                    ? 'Los términos “Calentar el Interior” y “Calentar el Interior y dispersar Frío” se utilizan en ocasiones de manera indistinta para esta familia. Después de dominar las 4 fórmulas del guideline, el capítulo 6 añade 7 fórmulas relacionadas para Frío en canales, Jiao Medio y colapso de Yang.'
                    : 'The terms “Warm the Interior” and “Warm Interior Cold” are sometimes used interchangeably for this formula family. After the 4 guideline formulas are secure, Chapter 6 adds 7 related formulas for Cold in the channels, Middle Burner, and Yang collapse.'); ?></p>
                <div class="formula-study-guide__chapter-shortcut-actions">
                    <a class="formula-study-guide__button" href="<?php echo $warmchapterurl->out(false); ?>"><?php echo s($ises ? 'Abrir la ampliación del capítulo 6 →' : 'Open the Chapter 6 expansion →'); ?></a>
                </div>
            </section>
        <?php endif; ?>
        <?php if (isset($chapterinterestinginsights[$selected['key']])): ?>
            <?php
            $chapterinsight = $chapterinterestinginsights[$selected['key']];
            $chapterinsightisexamreview = !empty($chapterinsight['examFocus']);
            ?>
            <section class="formula-study-guide__medical-rationale" aria-labelledby="chapter-interesting-insight-title">
                <div class="formula-study-guide__medical-rationale-header">
                    <span class="formula-study-guide__medical-rationale-stat"><?php echo s(($ises ? $chapterinsight['statEs'] : $chapterinsight['stat']) . ':'); ?></span>
                    <h4 id="chapter-interesting-insight-title"><?php echo s($ises ? $chapterinsight['titleEs'] : $chapterinsight['title']); ?></h4>
                    <p><?php echo s($ises ? $chapterinsight['introEs'] : $chapterinsight['intro']); ?></p>
                </div>
                <div class="formula-study-guide__table-wrap">
                    <table class="formula-study-guide__table" data-formula-context-table data-table-narration="<?php echo s($ises
                        ? ($chapterinsightisexamreview
                            ? 'Esta tabla resume tres patrones examinables. Relaciona cada patrón con su fórmula y con sus síntomas clave.'
                            : 'Esta tabla contrasta la cobertura clínica de las fichas detalladas con los temas adicionales que desarrolla el capítulo completo de Bensky.')
                        : ($chapterinsightisexamreview
                            ? 'This table summarizes three testable patterns. It links each pattern to its formula and differentiating symptoms.'
                            : 'This table contrasts the clinical coverage of the detailed profiles with the additional topics developed in the complete Bensky chapter.')); ?>">
                        <thead>
                            <tr>
                                <th><?php echo $chapterinsightisexamreview
                                    ? ($ises ? 'Patrón que debes reconocer:' : 'Pattern to recognize:')
                                    : ($ises ? 'Cobertura clínica representada en las fichas detalladas:' : 'Clinical coverage represented in the detailed profiles:'); ?></th>
                                <th><?php echo $chapterinsightisexamreview
                                    ? ($ises ? 'Fórmula clave:' : 'Key formula:')
                                    : ($ises ? 'Fichas detalladas representativas:' : 'Representative detailed core profiles:'); ?></th>
                                <th><?php echo $chapterinsightisexamreview
                                    ? ($ises ? 'Síntomas clave para el examen:' : 'Key exam symptoms:')
                                    : ($ises ? 'Qué añade el capítulo completo de Bensky:' : 'What the complete Bensky chapter adds:'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($chapterinsight['rows'] as $insightrow): ?>
                                <?php $boardlabels = array_map($renderformulanamewithenglish, $insightrow['boardNames']); ?>
                                <tr>
                                    <td><?php echo s($ises ? $insightrow['coverageEs'] : $insightrow['coverage']); ?></td>
                                    <td class="formula-study-guide__medical-rationale-board"><?php echo $boardlabels === []
                                        ? s($ises ? 'Ninguna fórmula clave directa' : 'No direct key formula')
                                        : implode(', ', $boardlabels); ?></td>
                                    <td><?php echo s($ises ? $insightrow['additionalEs'] : $insightrow['additional']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!$chapterinsightisexamreview): ?>
                    <div class="formula-study-guide__medical-rationale-header formula-study-guide__medical-rationale-secondary">
                        <h4><?php echo s($ises ? 'Una sospecha clínica y curricular razonable' : 'A reasonable clinical and curricular hypothesis'); ?></h4>
                        <p><?php echo s($ises ? $chapterinsight['hypothesisEs'] : $chapterinsight['hypothesis']); ?></p>
                        <p><strong><?php echo $ises ? 'Por qué sigue siendo interesante:' : 'Why this remains interesting:'; ?></strong> <?php echo s($ises ? $chapterinsight['valueEs'] : $chapterinsight['value']); ?></p>
                        <p class="formula-study-guide__medical-rationale-caveat"><?php echo s($ises
                            ? 'Nota de alcance: el Board no publica una justificación médica fórmula por fórmula. Esta es una hipótesis clínica y educativa basada en cobertura de patrones, aplicabilidad, seguridad y organización del temario; no una declaración oficial del Board.'
                            : 'Scope note: the Board does not publish a formula-by-formula medical rationale. This is a clinical and educational hypothesis based on pattern coverage, applicability, safety, and syllabus organization—not an official Board statement.'); ?></p>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        <?php if ($deferrelatedduiyao && !$moverelatedduiyaototheend): ?>
            <?php echo $relatedduiyaomarkup; ?>
        <?php endif; ?>
        <section class="formula-study-guide__formula-list" aria-label="<?php echo s($ises ? 'Fórmulas de la categoría' : 'Category formulas'); ?>">
            <?php
            $chapterformulaindex = 0;
            $lastformulacategory = null;
            $lastclinicalsectionkey = null;
            ?>
            <?php foreach ($selected['formulas'] as $formula): ?>
                <?php
                $isassociatedformula = ($formula['chapterRole'] ?? 'principal') === 'associated';
                if (!$isassociatedformula) {
                    $chapterformulaindex++;
                }
                $clinicalsection = $selectedformulasectionbyname[$formula['name']] ?? null;
                $newclinicalsection = $clinicalsection !== null
                    && $lastclinicalsectionkey !== $clinicalsection['key']
                    && !in_array($selected['key'], ['expel-damp-chapter-16', 'transform-phlegm-chapter-17', 'digestive-chapter-18', 'expel-parasites-chapter-19', 'treat-abscesses-chapter-20', 'external-application-chapter-21'], true);
                $currentsectionkey = in_array($selected['key'], ['stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11', 'regulate-qi-chapter-12', 'regulate-blood-chapter-13', 'expel-wind-chapter-14', 'expel-damp-chapter-16', 'transform-phlegm-chapter-17', 'digestive-chapter-18', 'expel-parasites-chapter-19', 'treat-abscesses-chapter-20', 'external-application-chapter-21'], true)
                    ? $formula['chapterSectionKey'] : $formula['categoryKey'];
                $newchaptersection = in_array($selected['key'], ['tonify-chapter-8', 'stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11', 'regulate-qi-chapter-12', 'regulate-blood-chapter-13', 'expel-wind-chapter-14', 'expel-damp-chapter-16', 'transform-phlegm-chapter-17', 'digestive-chapter-18', 'expel-parasites-chapter-19', 'treat-abscesses-chapter-20', 'external-application-chapter-21'], true)
                    && $lastformulacategory !== $currentsectionkey;
                if ($newchaptersection && $selected['key'] === 'stabilize-bind-chapter-9') {
                    $formulacategory = $stabilizechaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'calm-shen-chapter-10') {
                    $formulacategory = $calmshenchaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'open-orifices-chapter-11') {
                    $formulacategory = $openorificeschaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'regulate-qi-chapter-12') {
                    $formulacategory = $regulateqichaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'regulate-blood-chapter-13') {
                    $formulacategory = $regulatebloodchaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'expel-wind-chapter-14') {
                    $formulacategory = $expelwindchaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'expel-damp-chapter-16') {
                    $formulacategory = $expeldampchaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'transform-phlegm-chapter-17') {
                    $formulacategory = $transformphlegmchaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                    $formulacategory['principalCount'] = count(array_filter(
                        $formulacategory['formulas'],
                        static fn(array $item): bool => ($item['chapterRole'] ?? 'principal') !== 'associated'
                    ));
                    $formulacategory['associatedCount'] = count($formulacategory['formulas']) - $formulacategory['principalCount'];
                } elseif ($newchaptersection && $selected['key'] === 'digestive-chapter-18') {
                    $formulacategory = $digestivechaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'expel-parasites-chapter-19') {
                    $formulacategory = $expelparasiteschaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'treat-abscesses-chapter-20') {
                    $formulacategory = $treatabscesseschaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } elseif ($newchaptersection && $selected['key'] === 'external-application-chapter-21') {
                    $formulacategory = $externalapplicationchaptersections[$currentsectionkey];
                    $formulacategory['formulas'] = array_values(array_filter(
                        $selected['formulas'],
                        static fn(array $item): bool => $item['chapterSectionKey'] === $currentsectionkey
                    ));
                } else {
                    $formulacategory = $newchaptersection ? $supplementalcategories[$formula['categoryKey']] : null;
                }
                $formulacategoryguide = $newchaptersection && $selected['key'] === 'tonify-chapter-8'
                    ? ($data['categoryGuides'][$formula['categoryKey']] ?? null) : null;
                $lastformulacategory = $currentsectionkey;
                if ($clinicalsection !== null) {
                    $lastclinicalsectionkey = $clinicalsection['key'];
                }
                $tier = $formula['safetyTier'];
                $isintegratedofficialprofile = !empty($formula['integratedOfficialProfile']);
                $safetysummary = '';
                if (in_array($tier, ['high', 'critical'], true)) {
                    $safetytext = trim((string) ($ises ? $formula['safetyEs'] : $formula['safety']));
                    if ($formula['name'] === 'Ding Zhi Wan') {
                        $safetysummary = $ises
                            ? 'No es una elección general para deficiencia de Sangre o Yin. Confusión grave, manía, ideas suicidas, síncope o cambios neurológicos requieren evaluación médica urgente.'
                            : 'Not a general choice for Blood or Yin deficiency. Severe confusion, mania, suicidal thoughts, syncope, or neurologic changes require urgent medical assessment.';
                    } elseif (preg_match('/^(.+?[.!?])(?:\\s|$)/u', $safetytext, $safetymatch) === 1) {
                        $safetysummary = trim($safetymatch[1]);
                    } else {
                        $safetysummary = $safetytext;
                    }
                }
                $safetybadgesummary = '';
                if ($safetysummary !== '') {
                    if ($formula['name'] === 'Ding Zhi Wan') {
                        $safetybadgesummary = $ises
                            ? 'No indicada como elección general para deficiencia de Sangre o Yin'
                            : 'Not a general choice for Blood or Yin deficiency';
                    } else {
                        foreach (preg_split('/\\s+/u', $safetysummary) ?: [] as $safetyword) {
                            $candidate = trim($safetybadgesummary . ' ' . $safetyword);
                            if ($safetybadgesummary !== '' && strlen($candidate) > 82) {
                                break;
                            }
                            $safetybadgesummary = $candidate;
                        }
                        $safetybadgesummary = rtrim($safetybadgesummary, " \\t\\n\\r\\0\\x0B,;:.");
                    }
                }
                if ($tier === 'critical') {
                    $risklabel = $formula['name'] === 'Tian Wang Bu Xin Dan'
                        ? ($ises ? 'Riesgo de mercurio: Zhu Sha histórico' : 'Mercury risk: historical Zhu Sha')
                        : (($ises ? 'Advertencia crítica' : 'Critical warning')
                            . ($safetybadgesummary !== '' ? ': ' . $safetybadgesummary : ''));
                } else {
                    $risklabel = $tier === 'high'
                        ? (($ises ? 'Precaución alta' : 'High caution')
                            . ($safetybadgesummary !== '' ? ': ' . $safetybadgesummary : ''))
                        : '';
                }
                $kingherbsplural = preg_match('/[,\/]/', (string) $formula['kings']) === 1;
                $kingherbsdisplay = $renderherbswithenglish((string) $formula['kings']);
                $ingredientsdisplay = $renderherbswithenglish((string) ($ises
                    ? $formula['ingredientsEs']
                    : $formula['ingredients']));
                $formulaenglishname = trim((string) ($formula['englishName'] ?? ''));
                if ($formulaenglishname === '') {
                    $formulaenglishname = $getformulaenglishname($formula['name']);
                }
                $iscurrentguidelineformula = $isofficialformula($formula['name']);
                $summarycategorykey = (string) ($formulacategory['key'] ?? ($clinicalsection['key'] ?? $selected['key']));
                $summarycategoryname = $formulacategory !== null
                    ? (string) ($ises ? $formulacategory['nameEs'] : $formulacategory['name'])
                    : ($clinicalsection !== null
                        ? (string) ($ises ? $clinicalsection['nameEs'] : $clinicalsection['name'])
                        : (string) ($ises ? $selected['nameEs'] : $selected['name']));
                $formulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $formula['number']);
                $practiceurl = null;
                if (!$issupplemental && !$isintegratedofficialprofile) {
                    $practicepath = $ises ? '/study-guide/comparisons/formula-practice-es.html' : '/study-guide/comparisons/formula-practice.html';
                    $practiceurl = new moodle_url($practicepath, ['formula' => (int) $formula['number']]);
                }
                if (in_array($selected['key'], ['tonify-chapter-8', 'stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11', 'regulate-qi-chapter-12', 'regulate-blood-chapter-13', 'expel-wind-chapter-14', 'expel-damp-chapter-16', 'transform-phlegm-chapter-17', 'digestive-chapter-18', 'expel-parasites-chapter-19', 'treat-abscesses-chapter-20', 'external-application-chapter-21'], true)) {
                    $formulalabel = $isassociatedformula
                        ? (($ises ? 'Fórmula asociada bajo ' : 'Associated formula under ')
                            . ($formula['associatedWith'] ?? ''))
                        : (($ises ? 'Fórmula ' : 'Formula ') . $chapterformulaindex
                            . ' ' . ($ises ? 'de' : 'of') . ' ' . $selectedprincipalformulacount);
                } elseif ($isintegratedofficialprofile) {
                    $formulalabel = $ises ? 'Ficha oficial integrada' : 'Integrated official profile';
                } elseif ($issupplemental) {
                    $formulalabel = $isofficialexpansionpage
                        ? (($ises ? 'Ficha oficial adicional ' : 'Additional official profile ') . $formula['sequence']
                            . ' ' . ($ises ? 'de' : 'of') . ' ' . count($selected['formulas']))
                        : (($ises ? 'Fórmula principal ' : 'Principal formula ') . $formula['sequence']
                            . ' ' . ($ises ? 'de' : 'of') . ' ' . count($selected['formulas']));
                } else {
                    $formulalabel = ($ises ? 'Fórmula ' : 'Formula ') . $formula['number']
                        . ' ' . ($ises ? 'de' : 'of') . ' 63';
                }
                ?>
                <?php if ($newchaptersection && $formulacategory !== null): ?>
                    <section class="formula-study-guide__chapter-section" id="chapter-section-<?php echo s($formulacategory['key']); ?>" data-formula-summary-category="<?php echo s($summarycategorykey); ?>" data-formula-summary-category-name="<?php echo s($summarycategoryname); ?>">
                        <p class="formula-study-guide__eyebrow"><?php echo s($ises
                            ? 'Sección del capítulo ' . $selected['sourceChapter']
                            : 'Chapter ' . $selected['sourceChapter'] . ' section'); ?></p>
                        <h4><?php echo s($ises ? $formulacategory['nameEs'] : $formulacategory['name']); ?></h4>
                        <p><?php echo s(in_array($selected['key'], ['stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11', 'regulate-qi-chapter-12', 'regulate-blood-chapter-13', 'expel-wind-chapter-14', 'expel-damp-chapter-16', 'transform-phlegm-chapter-17', 'digestive-chapter-18', 'expel-parasites-chapter-19', 'treat-abscesses-chapter-20', 'external-application-chapter-21'], true)
                            ? ($ises ? $formulacategory['copyEs'] : $formulacategory['copy'])
                            : $categorycopy[$formulacategory['key']][$ises ? 1 : 0]); ?></p>
                        <span class="formula-study-guide__chapter-section-meta"><?php echo isset($formulacategory['principalCount'])
                            ? s($formulacategory['principalCount'] . ($ises ? ' principales' : ' principal')
                                . (($formulacategory['associatedCount'] ?? 0) > 0
                                    ? (' + ' . $formulacategory['associatedCount'] . ($ises ? ' asociada' : ' associated'))
                                    : ''))
                            : s(count($formulacategory['formulas']) . ' ' . ($ises ? 'fórmulas' : 'formulas')); ?></span>
                    </section>
                    <?php if ($formulacategoryguide !== null): ?>
                        <section class="formula-study-guide__priority" aria-label="<?php echo s($ises ? 'Diferenciación de la sección' : 'Section differentiation'); ?>">
                            <h4><?php echo s($ises ? $formulacategoryguide['routeTitleEs'] : $formulacategoryguide['routeTitle']); ?></h4>
                            <div class="formula-study-guide__route">
                                <?php foreach ($formulacategoryguide['route'] as $step): ?>
                                    <div class="formula-study-guide__route-step">
                                        <b><span class="formula-study-guide__route-number"><?php echo s($step[0]); ?></span><?php echo s($ises ? $step[3] : $step[1]); ?></b>
                                        <p><?php echo s($ises ? $step[4] : $step[2]); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section class="formula-study-guide__compare" aria-label="<?php echo s($ises ? 'Comparación de fórmulas' : 'Formula comparison'); ?>">
                            <h4><?php echo s($ises ? $formulacategoryguide['quickCompareTitleEs'] : $formulacategoryguide['quickCompareTitle']); ?></h4>
                            <div class="formula-study-guide__table-wrap">
                                <table class="formula-study-guide__table" data-formula-comparison-table>
                                    <thead><tr><th><?php echo $ises ? 'Fórmula:' : 'Formula:'; ?></th><th><?php echo $ises ? 'Patrón:' : 'Pattern:'; ?></th><th><?php echo $ises ? 'Síntomas clave:' : 'Key symptoms:'; ?></th></tr></thead>
                                    <tbody>
                                        <?php foreach ($formulacategoryguide['quickCompare'] as $row): ?>
                                            <?php
                                            $formulaenglishname = $getformulaenglishname($row[0]);
                                            ?>
                                            <tr>
                                                <td<?php echo $formulaenglishname !== '' ? ' data-formula-english-name="' . s($formulaenglishname) . '"' : ''; ?>>
                                                    <?php echo s($displayformulaname($row[0])); ?>
                                                    <?php if ($formulaenglishname !== ''): ?>
                                                        <span class="formula-study-guide__formula-translation" lang="en" data-formula-english-speech="<?php echo s($formulaenglishname); ?>"><?php echo s('(' . $formulaenglishname . ')'); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo s($ises ? $row[3] : $row[1]); ?></td>
                                                <td><?php echo s($ises ? $row[4] : $row[2]); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($newclinicalsection): ?>
                    <section class="formula-study-guide__chapter-section" id="clinical-section-<?php echo s($clinicalsection['key']); ?>" data-formula-summary-category="<?php echo s($summarycategorykey); ?>" data-formula-summary-category-name="<?php echo s($summarycategoryname); ?>">
                        <p class="formula-study-guide__eyebrow"><?php echo s($issupplemental
                            ? ($ises ? 'Sección clínica del capítulo' : 'Chapter clinical section')
                            : ($ises ? 'Sección clínica del guideline' : 'Guideline clinical section')); ?></p>
                        <h4><?php echo s($ises ? $clinicalsection['nameEs'] : $clinicalsection['name']); ?></h4>
                        <p><?php echo s($ises ? $clinicalsection['copyEs'] : $clinicalsection['copy']); ?></p>
                    </section>
                <?php endif; ?>
                <article class="formula-study-guide__formula" id="formula-<?php echo s($formulaid); ?>" data-formula-summary-card data-guideline-formula="<?php echo $iscurrentguidelineformula ? '1' : '0'; ?>" data-formula-name="<?php echo s($formula['name']); ?>" data-formula-english-name="<?php echo s($formulaenglishname); ?>" data-formula-summary-category="<?php echo s($summarycategorykey); ?>" data-formula-summary-category-name="<?php echo s($summarycategoryname); ?>">
                    <header class="formula-study-guide__formula-heading">
                        <div>
                            <span class="formula-study-guide__formula-label"><?php echo s($formulalabel . ' —'); ?></span>
                            <h4><?php echo s($displayformulaname($formula['name'])); ?></h4>
                            <?php if ($formulaenglishname !== ''): ?><span class="formula-study-guide__formula-translation" lang="en" data-formula-english-speech="<?php echo s($formulaenglishname); ?>"><?php echo s('(' . $formulaenglishname . ')'); ?></span><?php endif; ?>
                            <?php if ($issupplemental || $isintegratedofficialprofile): ?><span class="formula-study-guide__category-meta"><?php echo s($ises ? $formula['groupEs'] : $formula['group']); ?></span><?php endif; ?>
                        </div>
                        <div class="formula-study-guide__badges">
                            <?php if (($formula['priority'] ?? '') === 'core'): ?><span class="formula-study-guide__core" data-read-aloud-ignore><?php echo s($ises ? 'Ficha detallada del núcleo' : 'Detailed core profile'); ?></span><?php endif; ?>
                            <?php if ($issupplemental || $isintegratedofficialprofile): ?><span class="formula-study-guide__core"><?php echo s($isintegratedofficialprofile || $isofficialexpansionpage
                                ? ($ises ? 'Appendix C 2026 · oficial' : 'Appendix C 2026 · official')
                                : (is_numeric($formula['number'])
                                    ? (($ises ? 'Ficha del núcleo · Fórmula #' : 'Core profile · Formula #') . $formula['number'])
                                    : (($ises ? 'Bensky · Capítulo ' : 'Bensky · Chapter ') . $selected['sourceChapter']))); ?></span><?php endif; ?>
                            <?php if ($risklabel !== ''): ?><span class="formula-study-guide__risk formula-study-guide__risk--<?php echo s($tier); ?>"><?php echo s($risklabel . ' —'); ?></span><?php endif; ?>
                        </div>
                    </header>
                    <p class="formula-study-guide__formula-pattern"><strong><?php echo $ises ? 'Patrón:' : 'Pattern:'; ?></strong> <?php echo s($ises ? $formula['patternEs'] : $formula['pattern']); ?></p>
                    <?php if ($safetysummary !== ''): ?>
                        <p class="formula-study-guide__safety-summary formula-study-guide__safety-summary--<?php echo s($tier); ?>"><strong><?php echo $ises ? 'Advertencia de seguridad:' : 'Safety warning:'; ?></strong> <?php echo s($safetysummary); ?></p>
                    <?php endif; ?>
                    <div class="formula-study-guide__facts">
                        <div class="formula-study-guide__fact"><strong><?php echo $ises ? 'Acción:' : 'Action:'; ?></strong> <span><?php echo s($ises ? $formula['actionEs'] : $formula['action']); ?></span></div>
                        <div class="formula-study-guide__fact"><strong><?php echo $ises ? 'Síntomas clave:' : 'Key symptoms:'; ?></strong> <span><?php echo s($ises ? $formula['clueEs'] : $formula['clue']); ?></span></div>
                        <div class="formula-study-guide__fact"><strong><?php echo s($ises
                            ? ($kingherbsplural ? 'Hierbas emperador:' : 'Hierba emperador:')
                            : ($kingherbsplural ? 'King herbs:' : 'King herb:')); ?></strong> <span><?php echo $kingherbsdisplay; ?></span></div>
                    </div>
                    <?php if (isset($formula['examFocus'])): ?>
                        <p class="formula-study-guide__exam-focus"><strong><?php echo $issupplemental && !$isofficialformula($formula['name'])
                            ? ($ises ? 'Clave de estudio (fuera del Appendix C 2026):' : 'Study key (outside the 2026 Appendix C):')
                            : ($ises ? 'Enfoque de examen:' : 'Exam focus:'); ?></strong> <?php echo s($ises ? $formula['examFocusEs'] : $formula['examFocus']); ?></p>
                    <?php endif; ?>
                    <?php if (isset($formula['ingredientRoles']) || isset($formula['comparison'])): ?>
                        <details class="formula-study-guide__deep-dive">
                            <summary><?php echo s($ises ? 'Arquitectura y comparación de alto rendimiento' : 'High-yield architecture and comparison'); ?></summary>
                            <div class="formula-study-guide__deep-grid">
                                <?php if (isset($formula['ingredientRoles'])): ?><p><strong><?php echo $ises ? 'Funciones de ingredientes:' : 'Ingredient roles:'; ?></strong> <?php echo s($ises ? $formula['ingredientRolesEs'] : $formula['ingredientRoles']); ?></p><?php endif; ?>
                                <?php if (isset($formula['comparison'])): ?><p><strong><?php echo $ises ? 'Comparación decisiva:' : 'Decisive comparison:'; ?></strong> <?php echo s($ises ? $formula['comparisonEs'] : $formula['comparison']); ?></p><?php endif; ?>
                            </div>
                        </details>
                    <?php endif; ?>
                    <?php if (!empty($formula['modifications']) || !empty($formula['variations']) || !empty($formula['associated'])): ?>
                        <details class="formula-study-guide__branches">
                            <summary><?php echo s($ises ? 'Modificaciones, variaciones y fórmulas asociadas' : 'Modifications, variations, and associated formulas'); ?></summary>
                            <div class="formula-study-guide__branch-body">
                                <?php if (!empty($formula['modifications'])): ?>
                                    <section class="formula-study-guide__branch-group">
                                        <h5><?php echo s($ises ? 'Modificaciones clínicas' : 'Clinical modifications'); ?></h5>
                                        <ul><?php foreach ($formula['modifications'] as $item): ?>
                                            <li><strong><?php echo s($ises ? $item[2] : $item[0]); ?>:</strong> <?php echo s($ises ? $item[3] : $item[1]); ?></li>
                                        <?php endforeach; ?></ul>
                                    </section>
                                <?php endif; ?>
                                <?php if (!empty($formula['variations'])): ?>
                                    <section class="formula-study-guide__branch-group">
                                        <h5><?php echo s($ises ? 'Variaciones con nombre propio' : 'Named variations'); ?></h5>
                                        <ul><?php foreach ($formula['variations'] as $item): ?>
                                            <li><strong><?php echo $renderformulanamewithenglish($item[0]); ?>:</strong> <?php echo s($ises ? $item[2] : $item[1]); ?></li>
                                        <?php endforeach; ?></ul>
                                    </section>
                                <?php endif; ?>
                                <?php if (!empty($formula['associated'])): ?>
                                    <section class="formula-study-guide__branch-group">
                                        <h5><?php echo s($ises ? 'Fórmulas asociadas' : 'Associated formulas'); ?></h5>
                                        <ul><?php foreach ($formula['associated'] as $item): ?>
                                            <li><strong><?php echo $renderformulanamewithenglish($item[0]); ?>:</strong> <?php echo s($ises ? $item[2] : $item[1]); ?></li>
                                        <?php endforeach; ?></ul>
                                    </section>
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php endif; ?>
                    <?php if (isset($formula['modificationPreview'], $formula['modificationPreviewEs'])):
                        $preview = $ises ? $formula['modificationPreviewEs'] : $formula['modificationPreview'];
                        $modificationspath = $ises
                            ? '/study-guide/comparisons/formula-modifications-es.html'
                            : '/study-guide/comparisons/formula-modifications.html';
                        $modificationurl = new moodle_url($modificationspath, ['q' => $formula['name']]);
                    ?>
                        <div class="formula-study-guide__modification">
                            <p><strong><?php echo $ises ? 'Modificación representativa:' : 'Representative modification:'; ?></strong>
                                <?php echo s($preview['trigger']); ?> <b>→ <?php echo s($preview['change']); ?></b>
                            </p>
                            <a href="<?php echo $modificationurl->out(false); ?>"><?php echo $ises ? 'Ver todas →' : 'View all →'; ?></a>
                        </div>
                    <?php endif; ?>
                    <div class="formula-study-guide__formula-footer">
                        <p class="formula-study-guide__ingredients"><strong><?php echo $ises ? 'Ingredientes:' : 'Ingredients:'; ?></strong> <?php echo $ingredientsdisplay; ?></p>
                        <?php if ($practiceurl !== null): ?>
                            <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $practiceurl->out(false); ?>"><?php echo s(($ises ? 'Practicar ' : 'Practice ') . $formula['practiceCount'] . ($ises ? ' preguntas →' : ' questions →')); ?></a>
                        <?php elseif ($isintegratedofficialprofile): ?>
                            <span class="formula-study-guide__source-pages"><?php echo s('Bensky · '
                                . ($ises ? 'capítulo ' : 'Chapter ') . $formula['sourceChapter']); ?></span>
                        <?php else: ?>
                            <span class="formula-study-guide__source-pages"><?php echo s('Bensky · '
                                . ($ises ? 'capítulo ' : 'Chapter ') . $selected['sourceChapter']); ?></span>
                        <?php endif; ?>
                    </div>
                    <details class="formula-study-guide__safety">
                        <summary><?php echo s($ises ? 'Revisar seguridad y precauciones' : 'Review safety and cautions'); ?></summary>
                        <p><?php echo s($ises ? $formula['safetyEs'] : $formula['safety']); ?></p>
                    </details>
                </article>
            <?php endforeach; ?>
        </section>
        <?php if (in_array($selected['key'], ['expel-damp-chapter-16', 'transform-phlegm-chapter-17'], true) && $selectedguide !== null): ?>
            <?php $comparisonchapternumber = (int) $selected['sourceChapter']; ?>
            <details class="formula-study-guide__references" id="chapter-<?php echo s((string) $comparisonchapternumber); ?>-comparison-annex">
                <summary><?php echo s(($ises ? 'Anexo · Tablas comparativas del capítulo ' : 'Annex · Chapter ') . $comparisonchapternumber . ($ises ? '' : ' comparison tables')); ?></summary>
                <div class="formula-study-guide__references-body">
                    <p><?php echo s($ises
                        ? 'Consulta estas tablas después de estudiar las fichas. Permanecen plegadas para mantener breve la ruta principal del capítulo.'
                        : 'Use these tables after studying the formula cards. They remain collapsed to keep the chapter’s main study route concise.'); ?></p>
                    <?php $appendixcomparesections = $selectedformulasections; ?>
                    <?php foreach ($appendixcomparesections as $comparesection): ?>
                        <?php $compareformulanames = array_flip($comparesection['formulaNames']); ?>
                        <?php $comparerows = array_values(array_filter($selectedguide['quickCompare'], static fn(array $row): bool => isset($compareformulanames[$row[0]]))); ?>
                        <section class="formula-study-guide__compare" aria-labelledby="annex-comparison-<?php echo s($comparesection['key']); ?>-title">
                            <h4 id="annex-comparison-<?php echo s($comparesection['key']); ?>-title"><?php echo s(($ises ? 'Comparación · ' : 'Comparison · ') . ($ises ? $comparesection['nameEs'] : $comparesection['name'])); ?></h4>
                            <div class="formula-study-guide__table-wrap">
                                <table class="formula-study-guide__table" data-formula-comparison-table>
                                    <thead><tr><th><?php echo $ises ? 'Fórmula:' : 'Formula:'; ?></th><th><?php echo $ises ? 'Patrón:' : 'Pattern:'; ?></th><th><?php echo $ises ? 'Síntomas clave:' : 'Key symptoms:'; ?></th></tr></thead>
                                    <tbody>
                                        <?php foreach ($comparerows as $row): ?>
                                            <?php
                                            $formulaenglishname = $getformulaenglishname($row[0]);
                                            ?>
                                            <tr>
                                                <td<?php echo $formulaenglishname !== '' ? ' data-formula-english-name="' . s($formulaenglishname) . '"' : ''; ?>>
                                                    <?php echo s($displayformulaname($row[0])); ?>
                                                    <?php if ($formulaenglishname !== ''): ?>
                                                        <span class="formula-study-guide__formula-translation" lang="en" data-formula-english-speech="<?php echo s($formulaenglishname); ?>"><?php echo s('(' . $formulaenglishname . ')'); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo s($ises ? $row[3] : $row[1]); ?></td>
                                                <td><?php echo s($ises ? $row[4] : $row[2]); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>
            </details>
        <?php endif; ?>
        <?php if ($selectedguide !== null && !empty($selectedguide['referenceGroups'])): ?>
            <details class="formula-study-guide__references">
                <summary><?php echo s($ises ? $selectedguide['referencesTitleEs'] : $selectedguide['referencesTitle']); ?></summary>
                <div class="formula-study-guide__references-body">
                    <p><?php echo s($ises ? $selectedguide['referencesTextEs'] : $selectedguide['referencesText']); ?></p>
                    <div class="formula-study-guide__reference-groups">
                        <?php foreach ($selectedguide['referenceGroups'] as $group): ?>
                            <section class="formula-study-guide__reference-group">
                                <h5><?php echo s($ises ? $group['titleEs'] : $group['title']); ?></h5>
                                <ul>
                                    <?php foreach ($group['items'] as $item): ?>
                                        <li><strong><?php echo $renderformulanamewithenglish($item[0]); ?>:</strong> <?php echo s($ises ? $item[2] : $item[1]); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </div>
            </details>
        <?php endif; ?>
        <?php if ($moverelatedduiyaototheend): ?>
            <?php echo $relatedduiyaomarkup; ?>
        <?php endif; ?>
        <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Límite educativo:' : 'Educational boundary:'; ?></strong> <?php echo s($ises
            ? 'Esta guía organiza contenido histórico normalizado para repaso académico. No sustituye diagnóstico, evaluación de seguridad ni prescripción profesional.'
            : 'This guide organizes normalized historical content for academic review. It does not replace diagnosis, safety assessment or professional prescribing.'); ?></p>
        <?php echo $renderchapternavigation('bottom'); ?>
    <?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
