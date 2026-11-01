<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');

require_login();

$lang = optional_param('lang', '', PARAM_ALPHA);
$ises = $lang === 'es' || ($lang === '' && current_language() === 'es');
$focus = optional_param('focus', '', PARAM_ALPHANUMEXT);
$datafile = __DIR__ . '/formulas-study-guide-data.json';
$data = is_readable($datafile) ? json_decode((string) file_get_contents($datafile), true) : null;
if (!is_array($data)
        || count($data['categories'] ?? []) !== 14
        || count($data['formulas'] ?? []) !== 63
        || count($data['supplementalCategories'] ?? []) !== 12
        || count($data['supplementalFormulas'] ?? []) !== 97) {
    throw new moodle_exception('The Formula Study Guide data is temporarily unavailable.');
}

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
        'Compare the three Cheng Qi formulas by severity, dryness, fullness and focal distention.',
        'Compara las tres fórmulas Cheng Qi según gravedad, sequedad, plenitud y distensión focal.',
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
        'Study all 17 principal formulas in Bensky Chapter 9 across five leakage-control sections while preserving the separate official Board roster.',
        'Estudia las 17 fórmulas principales del capítulo 9 de Bensky en cinco secciones de control de pérdidas, conservando separado el listado oficial del Board.',
    ],
    'calm-shen-chapter-10' => [
        'Compare all nine principal formulas in Bensky Chapter 10 by deficiency, Fire, Phlegm, floating Yang, sensory signs, and safety.',
        'Compara las nueve fórmulas principales del capítulo 10 de Bensky por deficiencia, Fuego, Flema, Yang flotante, signos sensoriales y seguridad.',
    ],
    'open-orifices-chapter-11' => [
        'Compare seven principal formulas in Bensky Chapter 11 by hot versus cold closure, Heat, Phlegm, internal Wind, and emergency safety.',
        'Compara las siete fórmulas principales del capítulo 11 de Bensky por cierre caliente o frío, Calor, Flema, Viento interno y seguridad de urgencia.',
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
    'stabilize-bind' => [
        'Official Board category: 3 formulas. Cross-reference: complete Bensky Chapter 9 has 17 formulas; only Si Shen Wan and Gu Jing Wan overlap.',
        'Categoría oficial del Board: 3 fórmulas. Referencia cruzada: el capítulo 9 completo de Bensky tiene 17; solo coinciden Si Shen Wan y Gu Jing Wan.',
    ],
    'stabilize-bind-chapter-9' => [
        'Complete Bensky source: 17 formulas. Board overlap: 2. Yu Ping Feng San is the third official Board formula, but Bensky studies it in Chapter 8.',
        'Fuente Bensky completa: 17 fórmulas. Coincidencia Board: 2. Yu Ping Feng San es la tercera fórmula oficial, pero Bensky la estudia en el capítulo 8.',
    ],
    'calm-shen' => [
        'Official Board category: 3 formulas. Cross-reference: all three also appear within the nine principal formulas of complete Bensky Chapter 10.',
        'Categoría oficial del Board: 3 fórmulas. Referencia cruzada: las tres también aparecen entre las 9 fórmulas principales del capítulo 10 completo de Bensky.',
    ],
    'calm-shen-chapter-10' => [
        'Complete Bensky source: 9 formulas. Board overlap: 3—Suan Zao Ren Tang, Tian Wang Bu Xin Dan, and Gan Mai Da Zao Tang.',
        'Fuente Bensky completa: 9 fórmulas. Coincidencia Board: 3—Suan Zao Ren Tang, Tian Wang Bu Xin Dan y Gan Mai Da Zao Tang.',
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
$displayformulaname = static function(string $name) use ($getboardformula): string {
    return $name . ($getboardformula($name) !== null ? '*' : '');
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

$supplementalindexcategories = [];
foreach ($supplementalcategories as $categorykey => $category) {
    if (!in_array($categorykey, $tonifychapterkeys, true)
            && $categorykey !== 'warm-interior-cold'
            && $categorykey !== 'stabilize-bind-ch9'
            && $categorykey !== 'calm-shen-ch10'
            && $categorykey !== 'open-orifices-ch11') {
        $supplementalindexcategories[$categorykey] = $category;
    }
}
$supplementalindexcategories[$tonifychapteroverview['key']] = $tonifychapteroverview;
$supplementalindexcategories[$stabilizechapteroverview['key']] = $stabilizechapteroverview;
$supplementalindexcategories[$calmshenchapteroverview['key']] = $calmshenchapteroverview;
$supplementalindexcategories[$openorificeschapteroverview['key']] = $openorificeschapteroverview;
$supplementalcategories[$tonifychapteroverview['key']] = $tonifychapteroverview;
$supplementalcategories[$stabilizechapteroverview['key']] = $stabilizechapteroverview;
$supplementalcategories[$calmshenchapteroverview['key']] = $calmshenchapteroverview;
$supplementalcategories[$openorificeschapteroverview['key']] = $openorificeschapteroverview;
unset($supplementalcategories['stabilize-bind-ch9']);
unset($supplementalcategories['calm-shen-ch10']);
unset($supplementalcategories['open-orifices-ch11']);

$allcategories = $categories + $supplementalcategories;
$selected = $allcategories[$focus] ?? null;
$issupplemental = $selected !== null && isset($supplementalcategories[$selected['key']]);
$istonifychapterpage = $selected !== null
    && ($selected['key'] === 'tonify-chapter-8' || in_array($selected['key'], $tonifychapterkeys, true));
$iswarmchapterpage = $selected !== null && $selected['key'] === 'warm-interior-cold';
$isstabilizechapterpage = $selected !== null && $selected['key'] === 'stabilize-bind-chapter-9';
$iscalmshenchapterpage = $selected !== null && $selected['key'] === 'calm-shen-chapter-10';
$isopenorificeschapterpage = $selected !== null && $selected['key'] === 'open-orifices-chapter-11';
$selectedguide = $selected === null ? null : ($data['categoryGuides'][$selected['key']] ?? null);
$categorykeys = array_keys($allcategories);
$selectedindex = $selected === null ? null : array_search($selected['key'], $categorykeys, true);

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
$calmshenparentparams = ['focus' => 'calm-shen'];
if ($ises) {
    $calmshenparentparams['lang'] = 'es';
}
$calmshenparenturl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $calmshenparentparams);
$calmshenchapterparams = ['focus' => 'calm-shen-chapter-10'];
if ($ises) {
    $calmshenchapterparams['lang'] = 'es';
}
$calmshenchapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $calmshenchapterparams);
$openorificeschapterparams = ['focus' => 'open-orifices-chapter-11'];
if ($ises) {
    $openorificeschapterparams['lang'] = 'es';
}
$openorificeschapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $openorificeschapterparams);
$languageparams = $selected === null ? [] : ['focus' => $selected['key']];
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $languageparams);
$herbologyurl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => 'herbology']);
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', [
    'area' => 'herbology',
    'view' => 'library',
]);
$guidelinepath = $ises
    ? '/study-guide/guidelines/chinese-herbal-medicine-es.html'
    : '/study-guide/guidelines/chinese-herbal-medicine.html';
$guidelineurl = new moodle_url($guidelinepath, ['return' => $pageurl->out(false)], 'appendix-c-study');
$guidelinerosterurl = new moodle_url($guidelinepath, ['return' => $pageurl->out(false)], 'guide-formula-roster-title');

$previousurl = null;
$nexturl = null;
if ($selectedindex !== null && $selectedindex > 0) {
    $previousparams = ['focus' => $categorykeys[$selectedindex - 1]];
    if ($ises) {
        $previousparams['lang'] = 'es';
    }
    $previousurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $previousparams);
}
if ($selectedindex !== null && $selectedindex < count($categorykeys) - 1) {
    $nextparams = ['focus' => $categorykeys[$selectedindex + 1]];
    if ($ises) {
        $nextparams['lang'] = 'es';
    }
    $nexturl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $nextparams);
}

$pagetitle = $ises ? 'Guía de estudio de fórmulas' : 'Formula Study Guide';
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

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
.formula-study-guide__actions{display:flex;gap:9px;flex-wrap:wrap;justify-content:flex-end}
.formula-study-guide__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:9px 14px;border:1px solid #cbd5dc;border-radius:8px;background:#fff;color:#334155;font-weight:800;text-decoration:none;white-space:nowrap}
.formula-study-guide__button:hover{background:#f1f5f9;color:#17232a;text-decoration:none}
.formula-study-guide__button--primary{border-color:#2563eb;background:#2563eb;color:#fff}.formula-study-guide__button--primary:hover{background:#1d4ed8;color:#fff}
.formula-study-guide__overview{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:24px;align-items:center;margin-bottom:24px;padding:23px 26px;border-left:5px solid #0b817d;border-radius:12px;background:#eef8f6;box-shadow:0 3px 12px rgba(15,23,42,.05)}
.formula-study-guide__overview h3{margin:0 0 7px;font-size:22px}.formula-study-guide__overview p{margin:0;color:#52677a;line-height:1.55}
.formula-study-guide__stats{display:flex;gap:10px}.formula-study-guide__stat{min-width:92px;padding:13px 15px;border-radius:10px;background:#fff;color:#0f5f58;text-align:center}.formula-study-guide__stat strong{display:block;font-size:28px;line-height:1}.formula-study-guide__stat span{display:block;margin-top:5px;font-size:11px;font-weight:800;text-transform:uppercase}
.formula-study-guide__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:14px}
.formula-study-guide__category{display:flex;min-height:176px;flex-direction:column;padding:18px;border:1px solid #d9e2e7;border-top:4px solid #f77e25;border-radius:11px;background:#fff;color:inherit;text-decoration:none;box-shadow:0 2px 8px rgba(15,23,42,.05);transition:transform .15s ease,box-shadow .15s ease}
.formula-study-guide__category:hover{color:inherit;text-decoration:none;transform:translateY(-2px);box-shadow:0 7px 18px rgba(15,23,42,.1)}
.formula-study-guide__category-number{display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;margin-bottom:10px;border-radius:50%;background:#e9f6f3;color:#0b6b63;font-weight:900}
.formula-study-guide__category strong{font-size:18px;line-height:1.3}.formula-study-guide__category p{margin:7px 0 12px;color:#5b6b79;font-size:14px;line-height:1.45}.formula-study-guide__category small{margin-top:auto;color:#075e54;font-weight:800}
.formula-study-guide__category-meta{display:block;margin-top:5px;color:#64748b;font-size:12px;font-weight:700}
.formula-study-guide__category-reference{display:block;margin:0 0 12px;padding:8px 9px;border-left:3px solid #2563eb;border-radius:5px;background:#eff6ff;color:#234b7b;font-size:12px;font-weight:750;line-height:1.4}.formula-study-guide__category-header .formula-study-guide__category-reference{max-width:840px;margin:12px 0 0}
.formula-study-guide__supplement{margin-top:26px;padding:22px 24px;border:1px solid #c9ded9;border-top:4px solid #0b817d;border-radius:12px;background:#f2faf8}.formula-study-guide__supplement-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:15px}.formula-study-guide__supplement-heading h3{margin:2px 0 5px;font-size:22px}.formula-study-guide__supplement-heading p{max-width:780px;margin:0;color:#52677a;line-height:1.5}.formula-study-guide__supplement-badge{padding:6px 10px;border-radius:999px;background:#d9f1eb;color:#08695f;font-size:11px;font-weight:900;text-transform:uppercase;white-space:nowrap}.formula-study-guide__category--supplemental{min-height:156px;border-top-color:#0b817d}.formula-study-guide__category--supplemental .formula-study-guide__category-number{width:auto;padding:0 10px;border-radius:999px}.formula-study-guide__category--supplemental small{color:#075fc8}
.formula-study-guide__category-header{margin-bottom:18px;padding:22px 24px;border-top:5px solid #f77e25;border-radius:12px;background:linear-gradient(135deg,#fffaf5,#f7fbfb)}
.formula-study-guide__category-header--supplemental{border-top-color:#0b817d;background:linear-gradient(135deg,#effaf7,#fffaf5)}
.formula-study-guide__category-header h3{margin:4px 0 8px;font-size:clamp(25px,3vw,34px)}.formula-study-guide__category-header p{max-width:800px;margin:0;color:#52677a;line-height:1.55}
.formula-study-guide__chapter-shortcut{margin:0 0 18px;padding:18px 20px;border:1px solid #add8cf;border-left:5px solid #0b817d;border-radius:11px;background:#effaf7}.formula-study-guide__chapter-shortcut h4{margin:0 0 5px;font-size:20px}.formula-study-guide__chapter-shortcut>p{margin:0 0 14px;color:#496474;line-height:1.5}.formula-study-guide__chapter-shortcut-links{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px}.formula-study-guide__chapter-shortcut-link{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 12px;border:1px solid #c7e3dd;border-radius:8px;background:#fff;color:#075e54;font-weight:800;text-decoration:none}.formula-study-guide__chapter-shortcut-link:hover{background:#e7f7f3;color:#064e47;text-decoration:none}.formula-study-guide__chapter-shortcut-link span{color:#64748b;font-size:12px;white-space:nowrap}
.formula-study-guide__chapter-shortcut-action{margin:0 0 14px}
.formula-study-guide__board-verification{display:flex;align-items:center;justify-content:space-between;gap:18px;margin:0 0 18px;padding:16px 18px;border:1px solid #b9ddd5;border-left:5px solid #0b817d;border-radius:10px;background:#effaf7}.formula-study-guide__board-verification h4{margin:0 0 4px;font-size:17px}.formula-study-guide__board-verification p{margin:0;color:#496474;font-size:13px;line-height:1.5}.formula-study-guide__board-verification .formula-study-guide__button{flex:0 0 auto}
.formula-study-guide__scope-analysis{margin:0 0 20px;border:1px solid #edc989;border-left:5px solid #d97706;border-radius:11px;background:#fffaf0;overflow:hidden}.formula-study-guide__scope-analysis-header{padding:18px 20px}.formula-study-guide__scope-analysis-header h4{margin:0 0 7px;font-size:20px}.formula-study-guide__scope-analysis-header p{margin:0;color:#68543a;line-height:1.55}.formula-study-guide__scope-analysis-header p+p{margin-top:8px}.formula-study-guide__scope-analysis-caveat{font-size:13px}.formula-study-guide__scope-analysis .formula-study-guide__table-wrap{background:#fff}.formula-study-guide__scope-analysis .formula-study-guide__table td:first-child{white-space:normal}.formula-study-guide__scope-analysis-count{display:inline-flex;align-items:center;margin-right:6px;padding:3px 7px;border-radius:999px;background:#ffedd5;color:#9a3412;font-size:11px;font-weight:900;white-space:nowrap}
.formula-study-guide__chapter-shortcut-actions{display:flex;gap:9px;flex-wrap:wrap}
.formula-study-guide__board-roster{margin:16px 0;padding:15px;border:1px solid #b9ddd5;border-radius:9px;background:#fff}.formula-study-guide__board-roster h5{margin:0 0 4px;font-size:16px}.formula-study-guide__board-roster>p{margin:0 0 12px;color:#496474;font-size:13px}.formula-study-guide__board-roster-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.formula-study-guide__board-roster-group{padding:11px 12px;border:1px solid #d8e8e4;border-radius:8px;background:#f8fbfa}.formula-study-guide__board-roster-group h6{margin:0 0 7px;color:#075e54;font-size:13px}.formula-study-guide__board-roster-group ul{margin:0;padding:0;list-style:none}.formula-study-guide__board-roster-group li+li{margin-top:7px}.formula-study-guide__board-roster-formula{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}.formula-study-guide__board-roster-formula a{color:#172033;font-weight:800;text-decoration:none}.formula-study-guide__board-roster-formula a:hover{color:#075e54;text-decoration:underline}.formula-study-guide__board-roster-meta{display:block;margin-top:2px;color:#64748b;font-size:11px;line-height:1.35}.formula-study-guide__board-roster-number{flex:0 0 auto;padding:3px 6px;border-radius:999px;background:#e0edff;color:#134eaf;font-size:10px;font-weight:900;white-space:nowrap}
.formula-study-guide__chapter-directory{margin:0 0 20px;padding:22px;border:1px solid #c9ded9;border-top:5px solid #0b817d;border-radius:12px;background:#f2faf8}.formula-study-guide__chapter-directory h4{margin:0 0 7px;font-size:22px}.formula-study-guide__chapter-directory>p{margin:0 0 16px;color:#496474;line-height:1.55}.formula-study-guide__chapter-directory-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.formula-study-guide__chapter-directory-group{padding:15px;border-radius:9px;background:#fff}.formula-study-guide__chapter-directory-group h5{margin:0 0 8px;font-size:16px}.formula-study-guide__chapter-directory-group h5 a{color:#075e54;text-decoration:none}.formula-study-guide__chapter-directory-group h5 a:hover{text-decoration:underline}.formula-study-guide__chapter-directory-group ol{margin:0;padding-left:22px;columns:2;column-gap:22px}.formula-study-guide__chapter-directory-group li{margin:0 0 5px;break-inside:avoid;color:#52677a;font-size:13px;line-height:1.35}.formula-study-guide__chapter-directory-group li a{color:#334155;text-decoration:none}.formula-study-guide__chapter-directory-group li a:hover{color:#075fc8;text-decoration:underline}
.formula-study-guide__chapter-section{margin:18px 0 0;padding:20px 22px;border-left:6px solid #0b817d;border-radius:11px;background:linear-gradient(135deg,#eaf8f5,#fff7ed)}.formula-study-guide__chapter-section .formula-study-guide__eyebrow{margin-bottom:5px}.formula-study-guide__chapter-section h4{margin:0 0 7px;font-size:25px}.formula-study-guide__chapter-section>p{margin:0;color:#496474;line-height:1.55}.formula-study-guide__chapter-section-meta{display:inline-block;margin-top:9px;padding:5px 9px;border-radius:999px;background:#fff;color:#52677a;font-size:12px;font-weight:800}
.formula-study-guide__priority{margin:0 0 18px;padding:20px 22px;border:1px solid #b9dfd7;border-left:5px solid #0b817d;border-radius:11px;background:#eef9f6}.formula-study-guide__priority h4{margin:0 0 7px;font-size:20px}.formula-study-guide__priority h5{margin:17px 0 0;font-size:15px}.formula-study-guide__priority>p{margin:0;color:#496474;line-height:1.55}
.formula-study-guide__route{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:17px}.formula-study-guide__route-step{padding:13px;border-radius:9px;background:#fff}.formula-study-guide__route-step b{display:flex;align-items:center;gap:8px;color:#123246}.formula-study-guide__route-number{display:inline-flex;width:25px;height:25px;flex:0 0 25px;align-items:center;justify-content:center;border-radius:50%;background:#0b817d;color:#fff;font-size:12px}.formula-study-guide__route-step p{margin:7px 0 0;color:#52677a;font-size:13px;line-height:1.45}
.formula-study-guide__compare{margin:0 0 20px;border:1px solid #d8e1e7;border-radius:11px;background:#fff;overflow:hidden}.formula-study-guide__compare h4{margin:0;padding:16px 18px;background:#f5f8fa;font-size:18px}.formula-study-guide__table-wrap{overflow-x:auto}.formula-study-guide__table{width:100%;border-collapse:collapse}.formula-study-guide__table th,.formula-study-guide__table td{padding:11px 14px;border-top:1px solid #e2e8ec;text-align:left;vertical-align:top}.formula-study-guide__table th{color:#334155;font-size:12px;text-transform:uppercase}.formula-study-guide__table td{color:#52677a;line-height:1.4}.formula-study-guide__table td:first-child{color:#17232a;font-weight:800;white-space:nowrap}
.formula-study-guide__domain-badge{display:inline-flex;align-items:center;margin:5px 0 0;padding:4px 7px;border-radius:999px;background:#e0edff;color:#134eaf;font-size:10px;font-weight:900;letter-spacing:.04em;text-decoration:none;text-transform:uppercase;white-space:nowrap}.formula-study-guide__domain-badge:hover{background:#c9ddff;color:#0b3b8c;text-decoration:none}
.formula-study-guide__category-nav{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:0 0 18px}.formula-study-guide__category-nav>span{flex:1}.formula-study-guide__category-nav>span:last-child{text-align:right}.formula-study-guide__category-nav-center{display:flex;align-items:center;justify-content:center;gap:9px;flex-wrap:wrap}
.formula-study-guide__formula-list{display:grid;gap:16px}
.formula-study-guide__formula{padding:22px;border:1px solid #d8e1e7;border-radius:12px;background:#fff;box-shadow:0 2px 8px rgba(15,23,42,.04)}
.formula-study-guide__formula-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:13px}.formula-study-guide__formula-label{display:block;margin-bottom:4px;color:#08746f;font-size:12px;font-weight:900;letter-spacing:.07em;text-transform:uppercase}.formula-study-guide__formula h4{margin:0;font-size:23px;line-height:1.25}.formula-study-guide__badges{display:flex;align-items:center;justify-content:flex-end;gap:7px;flex-wrap:wrap}.formula-study-guide__core{padding:5px 9px;border-radius:999px;background:#dff4ee;color:#0b675b;font-size:11px;font-weight:900;text-transform:uppercase;white-space:nowrap}
.formula-study-guide__risk{padding:5px 9px;border-radius:999px;background:#e8f5f1;color:#116553;font-size:11px;font-weight:900;text-transform:uppercase;white-space:nowrap}.formula-study-guide__risk--high{background:#fff0d8;color:#955000}.formula-study-guide__risk--critical{background:#fde2df;color:#a12218}
.formula-study-guide__formula-pattern{margin:0 0 15px;color:#334155;font-size:16px;line-height:1.55}
.formula-study-guide__facts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.formula-study-guide__fact{padding:14px;border-radius:9px;background:#f6f9fa}.formula-study-guide__fact strong{display:block;margin-bottom:5px;color:#123246;font-size:13px}.formula-study-guide__fact span{display:block;color:#52677a;line-height:1.5}
.formula-study-guide__exam-focus{margin:14px 0 0;padding:11px 14px;border-left:4px solid #f77e25;border-radius:6px;background:#fff7ed;color:#6e481f;line-height:1.5}.formula-study-guide__exam-focus strong{color:#7c3f0a}
.formula-study-guide__deep-dive{margin-top:12px;border:1px solid #d5e4e1;border-radius:8px;background:#f8fbfa}.formula-study-guide__deep-dive summary{padding:10px 13px;color:#17675e;font-weight:800;cursor:pointer}.formula-study-guide__deep-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:12px;padding:0 13px 13px}.formula-study-guide__deep-grid p{margin:0;color:#52677a;line-height:1.5}.formula-study-guide__deep-grid strong{display:block;margin-bottom:4px;color:#17232a}
.formula-study-guide__modification{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:13px;padding:12px 13px;border:1px solid #bddfd7;border-left:4px solid #12816f;border-radius:8px;background:#f3fbf8;color:#365762;line-height:1.45}.formula-study-guide__modification p{margin:0}.formula-study-guide__modification strong{color:#087263}.formula-study-guide__modification b{color:#9b450e}.formula-study-guide__modification a{flex:0 0 auto;color:#075fc8;font-weight:800;text-decoration:none}.formula-study-guide__modification a:hover{text-decoration:underline}
.formula-study-guide__branches{margin-top:12px;border:1px solid #d7e1e6;border-radius:8px;background:#fff}.formula-study-guide__branches summary{padding:11px 13px;color:#174c69;font-weight:800;cursor:pointer}.formula-study-guide__branch-body{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:0 13px 13px}.formula-study-guide__branch-group{padding:12px;border-radius:8px;background:#f6f9fa}.formula-study-guide__branch-group h5{margin:0 0 8px;color:#17232a;font-size:14px}.formula-study-guide__branch-group ul{margin:0;padding-left:18px}.formula-study-guide__branch-group li{margin:0 0 8px;color:#52677a;line-height:1.45}.formula-study-guide__branch-group li:last-child{margin-bottom:0}.formula-study-guide__branch-group strong{color:#17232a}
.formula-study-guide__formula-footer{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:14px;padding-top:14px;border-top:1px solid #dde5e9}.formula-study-guide__ingredients{margin:0;color:#52677a;line-height:1.5}.formula-study-guide__ingredients strong{color:#17232a}
.formula-study-guide__source-pages{flex:0 0 auto;padding:7px 10px;border-radius:7px;background:#eef3f6;color:#415a6b;font-size:12px;font-weight:800;white-space:nowrap}
.formula-study-guide__safety{margin-top:12px;border:1px solid #ded6ec;border-radius:8px;background:#fbf9ff}.formula-study-guide__safety summary{padding:10px 13px;color:#60409a;font-weight:800;cursor:pointer}.formula-study-guide__safety p{margin:0;padding:0 13px 13px;color:#52677a;line-height:1.5}
.formula-study-guide__references{margin:20px 0;border:1px solid #d8e1e7;border-radius:11px;background:#fff}.formula-study-guide__references>summary{padding:16px 18px;color:#334155;font-size:17px;font-weight:900;cursor:pointer}.formula-study-guide__references-body{padding:0 18px 18px}.formula-study-guide__references-body>p{margin:0 0 15px;color:#52677a;line-height:1.5}.formula-study-guide__reference-groups{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.formula-study-guide__reference-group{padding:14px;border-radius:9px;background:#f6f9fa}.formula-study-guide__reference-group h5{margin:0 0 10px;font-size:15px}.formula-study-guide__reference-group ul{margin:0;padding-left:18px}.formula-study-guide__reference-group li{margin:0 0 9px;color:#52677a;line-height:1.4}.formula-study-guide__reference-group li:last-child{margin-bottom:0}.formula-study-guide__reference-group strong{color:#17232a}
.formula-study-guide__source{margin-top:20px;padding:14px 17px;border-radius:9px;background:#f1f5f9;color:#52677a;font-size:13px;line-height:1.5}
@media(max-width:900px){.formula-study-guide__route,.formula-study-guide__reference-groups{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:800px){.formula-study-guide__top{display:block}.formula-study-guide__actions{justify-content:flex-start;margin-top:15px}.formula-study-guide__facts,.formula-study-guide__deep-grid,.formula-study-guide__branch-body{grid-template-columns:1fr}.formula-study-guide__chapter-shortcut-links{grid-template-columns:repeat(2,minmax(0,1fr))}.formula-study-guide__board-roster-grid,.formula-study-guide__chapter-directory-grid{grid-template-columns:1fr}.formula-study-guide__board-verification,.formula-study-guide__formula-footer,.formula-study-guide__modification{align-items:flex-start;flex-direction:column}.formula-study-guide__modification a{width:100%}.formula-study-guide__overview{grid-template-columns:1fr}.formula-study-guide__stats{justify-content:flex-start}.formula-study-guide__supplement-heading{display:block}.formula-study-guide__supplement-badge{display:inline-block;margin-top:10px}}
@media(max-width:560px){.formula-study-guide__actions,.formula-study-guide__category-nav{align-items:stretch;flex-direction:column}.formula-study-guide__button{white-space:normal}.formula-study-guide__category-nav>span,.formula-study-guide__category-nav>span:last-child{width:100%;text-align:stretch}.formula-study-guide__category-nav-center{align-items:stretch;flex-direction:column}.formula-study-guide__formula-heading{flex-direction:column}.formula-study-guide__stats{display:grid;grid-template-columns:1fr 1fr}}
@media(max-width:560px){.formula-study-guide__route,.formula-study-guide__reference-groups,.formula-study-guide__chapter-shortcut-links{grid-template-columns:1fr}.formula-study-guide__chapter-directory-group ol{columns:1}.formula-study-guide__badges{justify-content:flex-start}}
</style>
<main class="formula-study-guide">
    <header class="formula-study-guide__top">
        <div>
            <p class="formula-study-guide__eyebrow"><?php echo s($ises ? 'Guía de estudio · Fórmulas' : 'Study Guide · Formulas'); ?></p>
            <h2><?php echo s($pagetitle); ?></h2>
            <p class="formula-study-guide__lead"><?php echo s($ises
                ? 'Estudia las 63 fórmulas principales en 14 categorías. Cada ficha conecta patrón, acción, pista decisiva, arquitectura y seguridad.'
                : 'Study the 63 core formulas in 14 categories. Every card connects pattern, action, decisive clue, architecture and safety.'); ?></p>
            <p class="formula-study-guide__marker-note"><?php echo s($ises ? '* Incluida en el guideline vigente del Board' : '* Included in the current Board guideline'); ?></p>
        </div>
        <nav class="formula-study-guide__actions" aria-label="<?php echo s($ises ? 'Navegación de la guía' : 'Study Guide navigation'); ?>">
            <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelineurl->out(false); ?>"><?php echo s($ises ? '← Guía de Board · Medicina Herbal China' : '← Board Guideline · Chinese Herbal Medicine'); ?></a>
            <a class="formula-study-guide__button" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
            <a class="formula-study-guide__button" href="<?php echo $libraryurl->out(false); ?>"><?php echo s($ises ? '← Biblioteca' : '← Library'); ?></a>
            <a class="formula-study-guide__button" href="<?php echo $herbologyurl->out(false); ?>"><?php echo s($ises ? '← Guía de estudio' : '← Study Guide'); ?></a>
        </nav>
    </header>

    <?php if ($selected === null): ?>
        <section class="formula-study-guide__overview" aria-labelledby="formula-categories-title">
            <div>
                <h3 id="formula-categories-title"><?php echo s($ises ? 'Índice de categorías de fórmulas' : 'Formula category index'); ?></h3>
                <p><?php echo s($ises
                    ? 'El orden y la numeración coinciden con la biblioteca actual de Herbs Practice Exams. Abre una categoría para estudiar sus fórmulas en secuencia.'
                    : 'The order and numbering match the current Herbs Practice Exams library. Open a category to study its formulas in sequence.'); ?></p>
            </div>
            <div class="formula-study-guide__stats" aria-label="<?php echo s($ises ? 'Totales de la guía' : 'Guide totals'); ?>">
                <div class="formula-study-guide__stat"><strong>14</strong><span><?php echo $ises ? 'categorías' : 'categories'; ?></span></div>
                <div class="formula-study-guide__stat"><strong>63</strong><span><?php echo $ises ? 'fórmulas' : 'formulas'; ?></span></div>
            </div>
        </section>
        <section class="formula-study-guide__grid" aria-label="<?php echo s($ises ? 'Categorías de fórmulas' : 'Formula categories'); ?>">
            <?php foreach ($categories as $category): ?>
                <?php
                $categoryparams = ['focus' => $category['key']];
                if ($ises) {
                    $categoryparams['lang'] = 'es';
                }
                $categoryurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $categoryparams);
                $copy = $categorycopy[$category['key']][$ises ? 1 : 0];
                $reference = $categoryreferences[$category['key']][$ises ? 1 : 0] ?? null;
                ?>
                <a class="formula-study-guide__category" href="<?php echo $categoryurl->out(false); ?>">
                    <span class="formula-study-guide__category-number"><?php echo (int) $category['number']; ?></span>
                    <strong><?php echo s($ises ? $category['nameEs'] : $category['name']); ?></strong>
                    <span class="formula-study-guide__category-meta"><?php echo count($category['formulas']); ?> <?php echo $ises ? 'fórmulas' : 'formulas'; ?></span>
                    <p><?php echo s($copy); ?></p>
                    <?php if ($reference !== null): ?><span class="formula-study-guide__category-reference"><?php echo s($reference); ?></span><?php endif; ?>
                    <small><?php echo s($ises ? 'Abrir categoría →' : 'Open category →'); ?></small>
                </a>
            <?php endforeach; ?>
        </section>
        <section class="formula-study-guide__supplement" aria-labelledby="supplemental-formula-title">
            <div class="formula-study-guide__supplement-heading">
                <div>
                    <p class="formula-study-guide__eyebrow"><?php echo s($ises ? 'Capítulos suplementarios' : 'Supplementary chapters'); ?></p>
                    <h3 id="supplemental-formula-title"><?php echo s($ises ? 'Fórmulas adicionales de la fuente' : 'Additional source formulas'); ?></h3>
                    <p><?php echo s($ises
                        ? 'Amplían el estudio del libro sin cambiar las 63 fórmulas ni los totales del banco oficial de práctica.'
                        : 'These extend study from the source text without changing the 63 formulas or official practice-bank totals.'); ?></p>
                </div>
                <span class="formula-study-guide__supplement-badge"><?php echo s($ises ? 'Separadas del listado Board' : 'Separate from Board roster'); ?></span>
            </div>
            <div class="formula-study-guide__grid">
                <?php foreach ($supplementalindexcategories as $category): ?>
                    <?php
                    $categoryparams = ['focus' => $category['key']];
                    if ($ises) {
                        $categoryparams['lang'] = 'es';
                    }
                    $categoryurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $categoryparams);
                    $copy = $categorycopy[$category['key']][$ises ? 1 : 0];
                    $reference = $categoryreferences[$category['key']][$ises ? 1 : 0] ?? null;
                    ?>
                    <a class="formula-study-guide__category formula-study-guide__category--supplemental" href="<?php echo $categoryurl->out(false); ?>">
                        <span class="formula-study-guide__category-number"><?php echo s($ises ? 'Capítulo ' : 'Chapter '); ?><?php echo (int) $category['sourceChapter']; ?></span>
                        <strong><?php echo s($ises ? $category['nameEs'] : $category['name']); ?></strong>
                        <span class="formula-study-guide__category-meta"><?php echo count($category['formulas']); ?> <?php echo $ises ? 'fórmulas principales' : 'principal formulas'; ?> · <?php echo s(($ises ? 'págs. ' : 'pp. ') . $category['sourcePages']); ?></span>
                        <p><?php echo s($copy); ?></p>
                        <?php if ($reference !== null): ?><span class="formula-study-guide__category-reference"><?php echo s($reference); ?></span><?php endif; ?>
                        <small><?php echo s($ises ? 'Abrir capítulo suplementario →' : 'Open supplementary chapter →'); ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php else: ?>
        <?php $selectedcopy = $categorycopy[$selected['key']][$ises ? 1 : 0]; ?>
        <?php $selectedreference = $categoryreferences[$selected['key']][$ises ? 1 : 0] ?? null; ?>
        <nav class="formula-study-guide__category-nav" aria-label="<?php echo s($ises ? 'Navegación entre categorías' : 'Category navigation'); ?>">
            <span><?php if ($previousurl !== null): ?><a class="formula-study-guide__button" href="<?php echo $previousurl->out(false); ?>"><?php echo s($ises ? '← Categoría anterior' : '← Previous category'); ?></a><?php endif; ?></span>
            <div class="formula-study-guide__category-nav-center">
                <?php if ($istonifychapterpage): ?><a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $tonifyparenturl->out(false); ?>"><?php echo s($ises ? '← Categoría principal Tonificar' : '← Tonify main category'); ?></a><?php endif; ?>
                <?php if ($iswarmchapterpage): ?><a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $warmparenturl->out(false); ?>"><?php echo s($ises ? '← Categoría principal Calentar el Interior' : '← Warm the Interior main category'); ?></a><?php endif; ?>
                <?php if ($isstabilizechapterpage): ?><a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $stabilizeparenturl->out(false); ?>"><?php echo s($ises ? '← Categoría principal Estabilizar y contener' : '← Stabilize and Bind main category'); ?></a><?php endif; ?>
                <?php if ($iscalmshenchapterpage): ?><a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $calmshenparenturl->out(false); ?>"><?php echo s($ises ? '← Categoría principal Calmar el Shen' : '← Calm Shen main category'); ?></a><?php endif; ?>
                <a class="formula-study-guide__button" href="<?php echo $indexurl->out(false); ?>"><?php echo s($ises ? 'Todas las categorías' : 'All categories'); ?></a>
            </div>
            <span><?php if ($nexturl !== null): ?><a class="formula-study-guide__button" href="<?php echo $nexturl->out(false); ?>"><?php echo s($ises ? 'Siguiente categoría →' : 'Next category →'); ?></a><?php endif; ?></span>
        </nav>
        <section class="formula-study-guide__category-header<?php echo $issupplemental ? ' formula-study-guide__category-header--supplemental' : ''; ?>" aria-labelledby="selected-category-title">
            <p class="formula-study-guide__eyebrow"><?php echo s($issupplemental
                ? (($ises ? 'Capítulo suplementario ' : 'Supplementary chapter ') . $selected['sourceChapter'] . ' · ' . ($ises ? 'páginas ' : 'pages ') . $selected['sourcePages'])
                : (($ises ? 'Categoría ' : 'Category ') . $selected['number'] . ' ' . ($ises ? 'de' : 'of') . ' 14')); ?></p>
            <h3 id="selected-category-title"><?php echo s($ises ? $selected['nameEs'] : $selected['name']); ?></h3>
            <p><?php echo s($selectedcopy); ?> <strong><?php echo count($selected['formulas']); ?> <?php echo $issupplemental ? ($ises ? 'fórmulas principales en este capítulo.' : 'principal formulas in this chapter.') : ($ises ? 'fórmulas en esta categoría.' : 'formulas in this category.'); ?></strong></p>
            <?php if ($selectedreference !== null): ?><span class="formula-study-guide__category-reference"><?php echo s($selectedreference); ?></span><?php endif; ?>
        </section>
        <?php if (!$issupplemental && $selected['key'] !== 'tonify'): ?>
            <section class="formula-study-guide__board-verification" aria-labelledby="board-verification-title">
                <div>
                    <h4 id="board-verification-title"><?php echo s($ises ? 'Comprueba el listado oficial del Board' : 'Verify the official Board list'); ?></h4>
                    <p><?php echo s($ises
                        ? 'Abre el listado completo del Appendix C —63 fórmulas en 14 categorías— para comprobar directamente cuáles pertenecen al guideline.'
                        : 'Open the complete Appendix C list—63 formulas in 14 categories—to verify directly which formulas belong to the guideline.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Abrir en Board Domain →' : 'Open in Board Domain →'); ?></a>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'tonify'): ?>
            <section class="formula-study-guide__chapter-shortcut" aria-labelledby="chapter-eight-shortcut-title">
                <h4 id="chapter-eight-shortcut-title"><?php echo s($ises ? 'Capítulo 8 completo · 45 fórmulas' : 'Complete Chapter 8 · 45 formulas'); ?></h4>
                <p><?php echo s($ises
                    ? 'El capítulo está dividido en seis tipos de tonificación. Nueve de sus fórmulas aparecen en los Board Domains: ocho en Tonify y una en Stabilize and Bind.'
                    : 'The chapter is divided into six tonification types. Nine of its formulas appear in the Board Domains: eight in Tonify and one in Stabilize and Bind.'); ?></p>
                <?php
                $masterchapterparams = ['focus' => 'tonify-chapter-8'];
                if ($ises) {
                    $masterchapterparams['lang'] = 'es';
                }
                $masterchapterurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $masterchapterparams);
                ?>
                <section class="formula-study-guide__board-roster" aria-labelledby="chapter-eight-board-roster-title">
                    <h5 id="chapter-eight-board-roster-title"><?php echo s($ises ? 'Fórmulas de este capítulo incluidas en los Board Domains · 9' : 'Chapter formulas included in the Board Domains · 9'); ?></h5>
                    <p><?php echo s($ises
                        ? 'La categoría grande indica el tipo exacto de tonificación del capítulo; debajo de cada fórmula aparece su dominio oficial del Board. Usa el enlace para comprobar estas fórmulas directamente contra el listado completo de 63 fórmulas del Appendix C.'
                        : 'The group heading gives the exact chapter tonification type; each formula also shows its official Board domain. Use the link to verify these formulas directly against the complete 63-formula Appendix C list.'); ?></p>
                    <div class="formula-study-guide__chapter-shortcut-action">
                        <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Abrir en Board Domain →' : 'Open in Board Domain →'); ?></a>
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
                                                <strong><?php echo s($displayformulaname($boardformula['name'])); ?></strong>
                                                <small class="formula-study-guide__board-roster-meta"><?php echo s(($ises ? 'Dominio Board: ' : 'Board domain: ') . $boardcategoryname); ?></small>
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
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo s($displayformulaname($chapterformula['name'])); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selectedguide !== null): ?>
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
            <section class="formula-study-guide__compare" aria-labelledby="core-comparison-title">
                <h4 id="core-comparison-title"><?php echo s($ises ? $selectedguide['quickCompareTitleEs'] : $selectedguide['quickCompareTitle']); ?></h4>
                <div class="formula-study-guide__table-wrap">
                    <table class="formula-study-guide__table">
                        <thead><tr><th><?php echo $ises ? 'Fórmula' : 'Formula'; ?></th><th><?php echo $ises ? 'Patrón' : 'Pattern'; ?></th><th><?php echo $ises ? 'Separador decisivo' : 'Decisive separator'; ?></th></tr></thead>
                        <tbody>
                            <?php foreach ($selectedguide['quickCompare'] as $row): ?>
                                <?php $boardmatch = $getboardformula($row[0]); ?>
                                <tr>
                                    <td>
                                        <?php echo s($displayformulaname($row[0])); ?>
                                        <?php if ($boardmatch !== null): ?>
                                            <br><span class="formula-study-guide__domain-badge"><?php echo s(($ises ? 'En los Board Domains · Fórmula ' : 'Board Domains · Formula ') . $boardmatch['number']); ?></span>
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
        <?php if ($selected['key'] === 'calm-shen'): ?>
            <section class="formula-study-guide__chapter-shortcut" aria-labelledby="chapter-ten-shortcut-title">
                <h4 id="chapter-ten-shortcut-title"><?php echo s($ises ? 'Capítulo 10 completo · 9 fórmulas' : 'Complete Chapter 10 · 9 formulas'); ?></h4>
                <p><?php echo s($ises
                    ? 'Tres fórmulas del capítulo aparecen en los Board Domains: Suan Zao Ren Tang (#52), Tian Wang Bu Xin Dan (#53) y Gan Mai Da Zao Tang (#54). La ampliación añade seis perfiles principales de Bensky con alertas reforzadas para sustancias históricas tóxicas, obsoletas o amenazadas.'
                    : 'Three chapter formulas appear in the Board Domains: Suan Zao Ren Tang (#52), Tian Wang Bu Xin Dan (#53), and Gan Mai Da Zao Tang (#54). The expansion adds six principal Bensky profiles with reinforced alerts for toxic, obsolete, or endangered historical substances.'); ?></p>
                <div class="formula-study-guide__chapter-shortcut-actions">
                    <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $calmshenchapterurl->out(false); ?>"><?php echo s($ises ? 'Abrir el capítulo 10 completo →' : 'Open complete Chapter 10 →'); ?></a>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'stabilize-bind'): ?>
            <section class="formula-study-guide__chapter-shortcut" aria-labelledby="chapter-nine-shortcut-title">
                <h4 id="chapter-nine-shortcut-title"><?php echo s($ises ? 'Capítulo 9 completo · 17 fórmulas' : 'Complete Chapter 9 · 17 formulas'); ?></h4>
                <p><?php echo s($ises
                    ? 'Dos fórmulas del capítulo 9 de Bensky aparecen en los Board Domains—Si Shen Wan (#50) y Gu Jing Wan (#51)—y la ampliación añade 15 perfiles principales relacionados. Yu Ping Feng San (#49) sigue siendo la tercera fórmula oficial de esta categoría, pero Bensky la estudia en el capítulo 8.'
                    : 'Two formulas in Bensky Chapter 9 appear in the Board Domains—Si Shen Wan (#50) and Gu Jing Wan (#51)—and the expansion adds 15 related principal profiles. Yu Ping Feng San (#49) remains the third official formula in this category, but Bensky discusses it in Chapter 8.'); ?></p>
                <div class="formula-study-guide__chapter-shortcut-actions">
                    <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $stabilizechapterurl->out(false); ?>"><?php echo s($ises ? 'Abrir el capítulo 9 completo →' : 'Open complete Chapter 9 →'); ?></a>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'stabilize-bind-chapter-9'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 9, páginas 419–454.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 9, pages 419–454.'); ?></p>
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
                    <h4 id="chapter-nine-board-title"><?php echo s($ises ? 'Coincidencia con los Board Domains · 2 de 17' : 'Board Domains overlap · 2 of 17'); ?></h4>
                    <p><?php echo s($ises ? 'Marcadas en el directorio y en sus fichas: Si Shen Wan es la fórmula Board #50 y Gu Jing Wan la #51. Las otras 15 fichas proceden del capítulo 9 de Bensky y no modifican el listado oficial de 63.' : 'Marked in the directory and on their cards: Si Shen Wan is Board formula #50 and Gu Jing Wan is #51. The other 15 cards come from Bensky Chapter 9 and do not change the official roster of 63.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Board Domain →' : 'Verify Board Domain →'); ?></a>
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
                                        <a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo s($displayformulaname($chapterformula['name'])); ?></a>
                                        <?php if (is_numeric($chapterformula['number'])): ?><span class="formula-study-guide__domain-badge"><?php echo s(($ises ? 'Dominio Board #' : 'Board Domain #') . $chapterformula['number']); ?></span><?php endif; ?>
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
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 10, páginas 457–482.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 10, pages 457–482.'); ?></p>
            <section class="formula-study-guide__priority" aria-labelledby="chapter-ten-principles-title">
                <h4 id="chapter-ten-principles-title"><?php echo s($ises ? 'Principio central: identifica primero raíz, exceso y sustancia que perturba el Shen' : 'Core principle: first identify the root, excess, and substance disturbing the Spirit'); ?></h4>
                <p><?php echo s($ises
                    ? 'La perturbación del Shen puede surgir de deficiencia de Qi, Sangre o Yin, o de Fuego, Flema y Yang ascendente. La presentación suele ser mixta. Las fórmulas históricas con minerales pesados no son equivalentes a tranquilizantes modernos y nunca justifican usar cinabrio, cuerno de rinoceronte u otras sustancias prohibidas.'
                    : 'Spirit disturbance may arise from Qi, Blood, or Yin deficiency, or from Fire, Phlegm, and ascendant Yang. Presentations are often mixed. Historical heavy-mineral formulas are not equivalents of modern tranquilizers and never justify using cinnabar, rhinoceros horn, or other prohibited substances.'); ?></p>
                <h5><?php echo s($ises ? 'Ruta de estudio del capítulo' : 'Chapter study route'); ?></h5>
                <div class="formula-study-guide__route">
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">1</span><?php echo s($ises ? 'Define deficiencia o exceso' : 'Define deficiency or excess'); ?></b><p><?php echo s($ises ? 'Qi, Sangre y Yin frente a Fuego, Flema o Yang ascendente.' : 'Qi, Blood, and Yin versus Fire, Phlegm, or ascendant Yang.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">2</span><?php echo s($ises ? 'Busca el separador' : 'Find the separator'); ?></b><p><?php echo s($ises ? 'Sudor nocturno, miedo, saburra grasosa, tinnitus, mareo o signos sensoriales.' : 'Night sweating, fright, greasy coat, tinnitus, dizziness, or sensory signs.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">3</span><?php echo s($ises ? 'Distingue la estrategia' : 'Distinguish the strategy'); ?></b><p><?php echo s($ises ? 'Nutrir y armonizar frente a asentar, aclarar Fuego y transformar Flema.' : 'Nourish and harmonize versus settle, clear Fire, and transform Phlegm.'); ?></p></div>
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Aplica el filtro de seguridad' : 'Apply the safety filter'); ?></b><p><?php echo s($ises ? 'Excluye cinabrio, especies amenazadas y cuadros psiquiátricos o neurológicos urgentes.' : 'Exclude cinnabar, endangered species, and urgent psychiatric or neurologic presentations.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-ten-board-title">
                <div>
                    <h4 id="chapter-ten-board-title"><?php echo s($ises ? 'Coincidencia con los Board Domains · 3 de 9' : 'Board Domains overlap · 3 of 9'); ?></h4>
                    <p><?php echo s($ises ? 'Marcadas en el directorio y en sus fichas: Suan Zao Ren Tang (#52), Tian Wang Bu Xin Dan (#53) y Gan Mai Da Zao Tang (#54). Las otras seis fichas son ampliaciones académicas de Bensky y no modifican el listado oficial de 63.' : 'Marked in the directory and on their cards: Suan Zao Ren Tang (#52), Tian Wang Bu Xin Dan (#53), and Gan Mai Da Zao Tang (#54). The other six cards are academic Bensky expansions and do not change the official roster of 63.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Board Domain →' : 'Verify Board Domain →'); ?></a>
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
                                        <a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo s($displayformulaname($chapterformula['name'])); ?></a>
                                        <?php if (is_numeric($chapterformula['number'])): ?><span class="formula-study-guide__domain-badge"><?php echo s(($ises ? 'Dominio Board #' : 'Board Domain #') . $chapterformula['number']); ?></span><?php endif; ?>
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
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 11, páginas 485–502.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 11, pages 485–502.'); ?></p>
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
                    <h4 id="chapter-eleven-board-title"><?php echo s($ises ? 'Dato relevante para el examen · 0 de 7 en Appendix C 2026' : 'Exam-relevant fact · 0 of 7 in the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'Ninguna de las siete fórmulas principales del capítulo 11 aparece en el listado oficial vigente de 63 fórmulas de NCBAHM. Por eso no llevan asterisco y no deben recibir prioridad principal de examen; se conservan como ampliación académica de Bensky.' : 'None of the seven principal Chapter 11 formulas appears in the current official NCBAHM roster of 63 formulas. They therefore carry no asterisk and should not receive primary exam priority; they remain as an academic Bensky expansion.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Board Domain →' : 'Verify Board Domain →'); ?></a>
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
                    <table class="formula-study-guide__table">
                        <thead><tr><th><?php echo $ises ? 'Factor' : 'Factor'; ?></th><th><?php echo $ises ? 'Evidencia en el capítulo' : 'Chapter evidence'; ?></th><th><?php echo $ises ? 'Relevancia actual' : 'Current relevance'; ?></th></tr></thead>
                        <tbody>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">5/7</span><?php echo s($ises ? 'Metales tóxicos históricos' : 'Historical toxic metals'); ?></td>
                                <td><?php echo s($ises ? 'An Gong Niu Huang Wan, Zi Xue Dan, Zhi Bao Dan, Hui Chun Dan y Su He Xiang Wan incluían Zhu Sha/cinabrio; An Gong Niu Huang Wan y Zhi Bao Dan también Xiong Huang/rejalgar.' : 'An Gong Niu Huang Wan, Zi Xue Dan, Zhi Bao Dan, Hui Chun Dan, and Su He Xiang Wan historically included Zhu Sha/cinnabar; An Gong Niu Huang Wan and Zhi Bao Dan also included Xiong Huang/realgar.'); ?></td>
                                <td><?php echo s($ises ? 'Cinabrio aporta mercurio y rejalgar arsénico. Zhu Sha figura como sustancia obsoleta en Appendix D; los perfiles modernos de esta guía los excluyen.' : 'Cinnabar contributes mercury and realgar arsenic. Zhu Sha is listed as obsolete in Appendix D; this guide’s modern profiles exclude both.'); ?></td>
                            </tr>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">4/7</span><?php echo s($ises ? 'Especies protegidas o prohibidas' : 'Protected or prohibited species'); ?></td>
                                <td><?php echo s($ises ? 'Cuatro fórmulas usaban históricamente Xi Jiao/cuerno de rinoceronte; Zhi Bao Dan también Dai Mao/tortuga carey. Zi Xue Dan plantea además la procedencia de Ling Yang Jiao.' : 'Four formulas historically used Xi Jiao/rhinoceros horn; Zhi Bao Dan also used Dai Mao/hawksbill turtle. Zi Xue Dan additionally raises sourcing concerns for Ling Yang Jiao.'); ?></td>
                                <td><?php echo s($ises ? 'La protección de especies y las restricciones comerciales obligan a excluir o sustituir componentes, por lo que la fórmula moderna ya no es idéntica a la clásica.' : 'Species protection and trade restrictions require components to be excluded or substituted, so the modern formula is no longer identical to the classical one.'); ?></td>
                            </tr>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">6/7</span><?php echo s($ises ? 'Ingredientes animales o regulados' : 'Animal-derived or regulated ingredients'); ?></td>
                                <td><?php echo s($ises ? 'Niu Huang, She Xiang, Shui Niu Jiao y otros ingredientes animales aparecen en seis perfiles; Chen Xiang también requiere procedencia legal y sostenible.' : 'Niu Huang, She Xiang, Shui Niu Jiao, and other animal-derived ingredients appear across six profiles; Chen Xiang also requires legal, sustainable sourcing.'); ?></td>
                                <td><?php echo s($ises ? 'Identidad, sustitución, control de calidad, legalidad y abastecimiento ético dificultan estandarizar estas fórmulas para un listado nacional.' : 'Identity, substitution, quality control, legality, and ethical sourcing complicate standardization for a national roster.'); ?></td>
                            </tr>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">1/7</span><?php echo s($ises ? 'Vía de administración peligrosa' : 'Hazardous administration route'); ?></td>
                                <td><?php echo s($ises ? 'Tong Guan San se insuflaba en la nariz para inducir estornudos y contenía Xi Xin.' : 'Tong Guan San was insufflated into the nose to induce sneezing and contained Xi Xin.'); ?></td>
                                <td><?php echo s($ises ? 'Existe riesgo de irritación y aspiración pulmonar; Xi Xin exige además control estricto de identidad y dosis. La guía lo conserva solo para reconocimiento histórico.' : 'There is a risk of irritation and pulmonary aspiration; Xi Xin also requires strict identity and dose control. The guide retains it only for historical recognition.'); ?></td>
                            </tr>
                            <tr>
                                <td><span class="formula-study-guide__scope-analysis-count">7/7</span><?php echo s($ises ? 'Uso en cuadros de urgencia' : 'Emergency presentations'); ?></td>
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
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo s($displayformulaname($chapterformula['name'])); ?></a></li>
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
        <section class="formula-study-guide__formula-list" aria-label="<?php echo s($ises ? 'Fórmulas de la categoría' : 'Category formulas'); ?>">
            <?php $chapterformulaindex = 0; $lastformulacategory = null; ?>
            <?php foreach ($selected['formulas'] as $formula): ?>
                <?php
                $chapterformulaindex++;
                $currentsectionkey = in_array($selected['key'], ['stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11'], true)
                    ? $formula['chapterSectionKey'] : $formula['categoryKey'];
                $newchaptersection = in_array($selected['key'], ['tonify-chapter-8', 'stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11'], true)
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
                } else {
                    $formulacategory = $newchaptersection ? $supplementalcategories[$formula['categoryKey']] : null;
                }
                $formulacategoryguide = $newchaptersection && $selected['key'] === 'tonify-chapter-8'
                    ? ($data['categoryGuides'][$formula['categoryKey']] ?? null) : null;
                $lastformulacategory = $currentsectionkey;
                $tier = $formula['safetyTier'];
                $risklabel = $tier === 'critical'
                    ? ($ises ? 'Seguridad crítica' : 'Critical safety')
                    : ($tier === 'high' ? ($ises ? 'Precaución alta' : 'High caution') : ($ises ? 'Seguridad revisada' : 'Safety reviewed'));
                $formulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $formula['number']);
                $practiceurl = null;
                if (!$issupplemental) {
                    $practicepath = $ises ? '/study-guide/comparisons/formula-practice-es.html' : '/study-guide/comparisons/formula-practice.html';
                    $practiceurl = new moodle_url($practicepath, ['formula' => (int) $formula['number']]);
                }
                if (in_array($selected['key'], ['tonify-chapter-8', 'stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11'], true)) {
                    $formulalabel = ($ises ? 'Fórmula ' : 'Formula ') . $chapterformulaindex
                        . ' ' . ($ises ? 'de' : 'of') . ' ' . count($selected['formulas'])
                        . ' · ' . ($ises ? 'págs. ' : 'pp. ') . $formula['sourcePages'];
                } elseif ($issupplemental) {
                    $formulalabel = ($ises ? 'Fórmula principal ' : 'Principal formula ') . $formula['sequence']
                        . ' ' . ($ises ? 'de' : 'of') . ' ' . count($selected['formulas'])
                        . ' · ' . ($ises ? 'págs. ' : 'pp. ') . $formula['sourcePages'];
                } else {
                    $formulalabel = ($ises ? 'Fórmula ' : 'Formula ') . $formula['number']
                        . ' ' . ($ises ? 'de' : 'of') . ' 63';
                }
                ?>
                <?php if ($newchaptersection && $formulacategory !== null): ?>
                    <section class="formula-study-guide__chapter-section" id="chapter-section-<?php echo s($formulacategory['key']); ?>">
                        <p class="formula-study-guide__eyebrow"><?php echo s($ises
                            ? 'Sección del capítulo ' . $selected['sourceChapter']
                            : 'Chapter ' . $selected['sourceChapter'] . ' section'); ?></p>
                        <h4><?php echo s($ises ? $formulacategory['nameEs'] : $formulacategory['name']); ?></h4>
                        <p><?php echo s(in_array($selected['key'], ['stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11'], true)
                            ? ($ises ? $formulacategory['copyEs'] : $formulacategory['copy'])
                            : $categorycopy[$formulacategory['key']][$ises ? 1 : 0]); ?></p>
                        <span class="formula-study-guide__chapter-section-meta"><?php echo count($formulacategory['formulas']); ?> <?php echo $ises ? 'fórmulas' : 'formulas'; ?> · <?php echo s(($ises ? 'págs. ' : 'pp. ') . $formulacategory['sourcePages']); ?></span>
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
                                <table class="formula-study-guide__table">
                                    <thead><tr><th><?php echo $ises ? 'Fórmula' : 'Formula'; ?></th><th><?php echo $ises ? 'Patrón' : 'Pattern'; ?></th><th><?php echo $ises ? 'Separador decisivo' : 'Decisive separator'; ?></th></tr></thead>
                                    <tbody>
                                        <?php foreach ($formulacategoryguide['quickCompare'] as $row): ?>
                                            <?php $boardmatch = $getboardformula($row[0]); ?>
                                            <tr>
                                                <td>
                                                    <?php echo s($displayformulaname($row[0])); ?>
                                                    <?php if ($boardmatch !== null): ?>
                                                        <br><span class="formula-study-guide__domain-badge"><?php echo s(($ises ? 'En los Board Domains · Fórmula ' : 'Board Domains · Formula ') . $boardmatch['number']); ?></span>
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
                <article class="formula-study-guide__formula" id="formula-<?php echo s($formulaid); ?>">
                    <header class="formula-study-guide__formula-heading">
                        <div>
                            <span class="formula-study-guide__formula-label"><?php echo s($formulalabel); ?></span>
                            <h4><?php echo s($displayformulaname($formula['name'])); ?></h4>
                            <?php if ($issupplemental): ?><span class="formula-study-guide__category-meta"><?php echo s($formula['englishName']); ?> · <?php echo s($ises ? $formula['groupEs'] : $formula['group']); ?></span><?php endif; ?>
                        </div>
                        <div class="formula-study-guide__badges">
                            <?php if (($formula['priority'] ?? '') === 'core'): ?><span class="formula-study-guide__core"><?php echo s($ises ? 'Principal del guideline' : 'Guideline core'); ?></span><?php endif; ?>
                            <?php if ($issupplemental): ?><span class="formula-study-guide__core"><?php echo s(is_numeric($formula['number'])
                                ? (($ises ? 'Dominio Board · Fórmula #' : 'Board Domain · Formula #') . $formula['number'])
                                : (($ises ? 'Bensky · Capítulo ' : 'Bensky · Chapter ') . $selected['sourceChapter'])); ?></span><?php endif; ?>
                            <span class="formula-study-guide__risk<?php echo $tier !== 'standard' ? ' formula-study-guide__risk--' . s($tier) : ''; ?>"><?php echo s($risklabel); ?></span>
                        </div>
                    </header>
                    <p class="formula-study-guide__formula-pattern"><strong><?php echo $ises ? 'Patrón:' : 'Pattern:'; ?></strong> <?php echo s($ises ? $formula['patternEs'] : $formula['pattern']); ?></p>
                    <div class="formula-study-guide__facts">
                        <div class="formula-study-guide__fact"><strong><?php echo $ises ? 'Acción' : 'Action'; ?></strong><span><?php echo s($ises ? $formula['actionEs'] : $formula['action']); ?></span></div>
                        <div class="formula-study-guide__fact"><strong><?php echo $ises ? 'Pista decisiva' : 'Decisive clue'; ?></strong><span><?php echo s($ises ? $formula['clueEs'] : $formula['clue']); ?></span></div>
                        <div class="formula-study-guide__fact"><strong><?php echo $ises ? 'Hierba(s) emperador' : 'King herb(s)'; ?></strong><span><?php echo s($formula['kings']); ?></span></div>
                    </div>
                    <?php if (isset($formula['examFocus'])): ?>
                        <p class="formula-study-guide__exam-focus"><strong><?php echo $selected['key'] === 'open-orifices-chapter-11'
                            ? ($ises ? 'Clave de estudio (fuera del Appendix C 2026):' : 'Study key (outside the 2026 Appendix C):')
                            : ($ises ? 'Enfoque de examen:' : 'Exam focus:'); ?></strong> <?php echo s($ises ? $formula['examFocusEs'] : $formula['examFocus']); ?></p>
                    <?php endif; ?>
                    <?php if (isset($formula['ingredientRoles']) || isset($formula['comparison'])): ?>
                        <details class="formula-study-guide__deep-dive">
                            <summary><?php echo s($ises ? 'Arquitectura y comparación de alto rendimiento' : 'High-yield architecture and comparison'); ?></summary>
                            <div class="formula-study-guide__deep-grid">
                                <?php if (isset($formula['ingredientRoles'])): ?><p><strong><?php echo $ises ? 'Funciones de ingredientes' : 'Ingredient roles'; ?></strong><?php echo s($ises ? $formula['ingredientRolesEs'] : $formula['ingredientRoles']); ?></p><?php endif; ?>
                                <?php if (isset($formula['comparison'])): ?><p><strong><?php echo $ises ? 'Comparación decisiva' : 'Decisive comparison'; ?></strong><?php echo s($ises ? $formula['comparisonEs'] : $formula['comparison']); ?></p><?php endif; ?>
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
                                            <li><strong><?php echo s($displayformulaname($item[0])); ?>:</strong> <?php echo s($ises ? $item[2] : $item[1]); ?></li>
                                        <?php endforeach; ?></ul>
                                    </section>
                                <?php endif; ?>
                                <?php if (!empty($formula['associated'])): ?>
                                    <section class="formula-study-guide__branch-group">
                                        <h5><?php echo s($ises ? 'Fórmulas asociadas' : 'Associated formulas'); ?></h5>
                                        <ul><?php foreach ($formula['associated'] as $item): ?>
                                            <li><strong><?php echo s($displayformulaname($item[0])); ?>:</strong> <?php echo s($ises ? $item[2] : $item[1]); ?></li>
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
                        <p class="formula-study-guide__ingredients"><strong><?php echo $ises ? 'Ingredientes:' : 'Ingredients:'; ?></strong> <?php echo s($ises ? $formula['ingredientsEs'] : $formula['ingredients']); ?></p>
                        <?php if ($practiceurl !== null): ?>
                            <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $practiceurl->out(false); ?>"><?php echo s(($ises ? 'Practicar ' : 'Practice ') . $formula['practiceCount'] . ($ises ? ' preguntas →' : ' questions →')); ?></a>
                        <?php else: ?>
                            <span class="formula-study-guide__source-pages"><?php echo s('Bensky · '
                                . ($ises ? 'capítulo ' : 'Chapter ') . $selected['sourceChapter']
                                . ($ises ? ' · págs. ' : ' · pp. ') . $formula['sourcePages']); ?></span>
                        <?php endif; ?>
                    </div>
                    <details class="formula-study-guide__safety">
                        <summary><?php echo s($ises ? 'Revisar seguridad y precauciones' : 'Review safety and cautions'); ?></summary>
                        <p><?php echo s($ises ? $formula['safetyEs'] : $formula['safety']); ?></p>
                    </details>
                </article>
            <?php endforeach; ?>
        </section>
        <?php if ($selectedguide !== null): ?>
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
                                        <li><strong><?php echo s($displayformulaname($item[0])); ?>:</strong> <?php echo s($ises ? $item[2] : $item[1]); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </div>
            </details>
        <?php endif; ?>
        <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Límite educativo:' : 'Educational boundary:'; ?></strong> <?php echo s($ises
            ? 'Esta guía organiza contenido histórico normalizado para repaso académico. No sustituye diagnóstico, evaluación de seguridad ni prescripción profesional.'
            : 'This guide organizes normalized historical content for academic review. It does not replace diagnosis, safety assessment or professional prescribing.'); ?></p>
        <nav class="formula-study-guide__category-nav" aria-label="<?php echo s($ises ? 'Navegación entre categorías' : 'Category navigation'); ?>">
            <span><?php if ($previousurl !== null): ?><a class="formula-study-guide__button" href="<?php echo $previousurl->out(false); ?>"><?php echo s($ises ? '← Categoría anterior' : '← Previous category'); ?></a><?php endif; ?></span>
            <a class="formula-study-guide__button" href="<?php echo $indexurl->out(false); ?>"><?php echo s($ises ? 'Todas las categorías' : 'All categories'); ?></a>
            <span><?php if ($nexturl !== null): ?><a class="formula-study-guide__button" href="<?php echo $nexturl->out(false); ?>"><?php echo s($ises ? 'Siguiente categoría →' : 'Next category →'); ?></a><?php endif; ?></span>
        </nav>
    <?php endif; ?>
</main>
<?php
echo $OUTPUT->footer();
