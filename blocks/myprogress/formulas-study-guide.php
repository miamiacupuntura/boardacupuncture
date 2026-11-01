<?php
// This file is part of Moodle - http://moodle.org/.

require_once(__DIR__ . '/../../config.php');

require_login();

$lang = optional_param('lang', '', PARAM_ALPHA);
$ises = $lang === 'es' || ($lang === '' && current_language() === 'es');
$focus = optional_param('focus', '', PARAM_ALPHANUMEXT);
$chapterfocusaliases = [
    'regulate-qi' => 'regulate-qi-chapter-12',
    'invigorate-blood' => 'regulate-blood-chapter-13',
    'expel-wind' => 'expel-wind-chapter-14',
];
$focus = $chapterfocusaliases[$focus] ?? $focus;
$datafile = __DIR__ . '/formulas-study-guide-data.json';
$data = is_readable($datafile) ? json_decode((string) file_get_contents($datafile), true) : null;
if (!is_array($data)
        || count($data['categories'] ?? []) !== 14
        || count($data['formulas'] ?? []) !== 63
        || count($data['supplementalCategories'] ?? []) !== 15
        || count($data['supplementalFormulas'] ?? []) !== 145) {
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
    'regulate-qi-chapter-12' => [
        'Compare all 20 principal formulas in Bensky Chapter 12 by Qi constraint, organ location, Cold, Heat, Phlegm, deficiency, and the direction of rebellious Qi.',
        'Compara las 20 fórmulas principales del capítulo 12 de Bensky por estancamiento de Qi, localización orgánica, Frío, Calor, Flema, deficiencia y dirección del Qi rebelde.',
    ],
    'regulate-blood-chapter-13' => [
        'Compare all 23 principal formulas in Bensky Chapter 13 across Blood stasis and bleeding patterns, with Heat, Cold, deficiency, location, and urgency as decisive separators.',
        'Compara las 23 fórmulas principales del capítulo 13 de Bensky entre patrones de estasis y sangrado, usando Calor, Frío, deficiencia, localización y urgencia como separadores decisivos.',
    ],
    'expel-wind-chapter-14' => [
        'Compare all 17 principal formulas in Bensky Chapter 14 by external versus internal Wind, Heat, Cold, Phlegm, Blood-Yin deficiency, channel location, and urgency.',
        'Compara las 17 fórmulas principales del capítulo 14 de Bensky por Viento externo o interno, Calor, Frío, Flema, deficiencia de Sangre-Yin, localización en canales y urgencia.',
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
        'Terminology: “Calm Shen” and “Calm the Spirit” are used interchangeably for the same formula family. Official Board category: 3 formulas. All three also appear in complete Bensky Chapter 10.',
        'Terminología: “Calm Shen”, “Calmar el Shen” y “Calmar el Espíritu” se usan indistintamente para la misma familia. Categoría oficial del Board: 3 fórmulas; las tres aparecen en el capítulo 10 completo de Bensky.',
    ],
    'calm-shen-chapter-10' => [
        'Terminology: “Formulas That Calm the Spirit” is the same family called “Calm Shen” by the Board, not a second category. Complete Bensky source: 9 formulas; Board overlap: 3.',
        'Terminología: “Fórmulas que calman el Espíritu” es la misma familia que el Board llama “Calm Shen”, no otra categoría. Fuente Bensky completa: 9 fórmulas; coincidencia Board: 3.',
    ],
    'regulate-qi' => [
        'Official Board category: 5 formulas. All five also appear among the 20 principal formulas in complete Bensky Chapter 12.',
        'Categoría oficial del Board: 5 fórmulas. Las cinco también aparecen entre las 20 fórmulas principales del capítulo 12 completo de Bensky.',
    ],
    'regulate-qi-chapter-12' => [
        'Complete Bensky source: 20 principal formulas. Board overlap: 5—Yue Ju Wan, Ban Xia Hou Po Tang, Su Zi Jiang Qi Tang, Ding Chuan Tang, and Ju Pi Zhu Ru Tang.',
        'Fuente Bensky completa: 20 fórmulas principales. Coincidencia Board: 5—Yue Ju Wan, Ban Xia Hou Po Tang, Su Zi Jiang Qi Tang, Ding Chuan Tang y Ju Pi Zhu Ru Tang.',
    ],
    'invigorate-blood' => [
        'Official Board category: 4 formulas. All four also appear among the 23 principal formulas in complete Bensky Chapter 13.',
        'Categoría oficial del Board: 4 fórmulas. Las cuatro también aparecen entre las 23 fórmulas principales del capítulo 13 completo de Bensky.',
    ],
    'regulate-blood-chapter-13' => [
        'Complete Bensky source: 23 principal formulas in two sections. Board overlap: 4—Tao He Cheng Qi Tang, Xue Fu Zhu Yu Tang, Wen Jing Tang, and Gui Zhi Fu Ling Wan.',
        'Fuente Bensky completa: 23 fórmulas principales en dos secciones. Coincidencia Board: 4—Tao He Cheng Qi Tang, Xue Fu Zhu Yu Tang, Wen Jing Tang y Gui Zhi Fu Ling Wan.',
    ],
    'expel-wind' => [
        'Official Board category: 3 formulas. All three also appear among the 17 principal formulas in complete Bensky Chapter 14.',
        'Categoría oficial del Board: 3 fórmulas. Las tres también aparecen entre las 17 fórmulas principales del capítulo 14 completo de Bensky.',
    ],
    'expel-wind-chapter-14' => [
        'Complete Bensky source: 17 principal formulas in two sections. Board overlap: 3—Xiao Feng San, Xiao Huo Luo Dan, and Tian Ma Gou Teng Yin.',
        'Fuente Bensky completa: 17 fórmulas principales en dos secciones. Coincidencia Board: 3—Xiao Feng San, Xiao Huo Luo Dan y Tian Ma Gou Teng Yin.',
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
$regulateqichaptersourcecategory = $supplementalcategories['regulate-qi-ch12'];
$regulatebloodchaptersourcecategory = $supplementalcategories['regulate-blood-ch13'];
$expelwindchaptersourcecategory = $supplementalcategories['expel-wind-ch14'];

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
        'key' => 'board', 'name' => 'Board formulas first',
        'nameEs' => 'Fórmulas del Board primero', 'sourcePages' => '634–642, 647–649',
        'copy' => 'Begin with the three formulas in the current Board guideline. Their asterisks remain visible; the remaining 14 cards then complete the Bensky chapter.',
        'copyEs' => 'Empieza con las tres fórmulas del guideline vigente del Board. Se conservan sus asteriscos; las 14 fichas restantes completan después el capítulo de Bensky.',
    ],
    'external' => [
        'key' => 'external', 'name' => 'Additional Bensky formulas · Dredge and Disperse External Wind',
        'nameEs' => 'Fórmulas adicionales de Bensky · Desobstruir y dispersar Viento externo', 'sourcePages' => '619–643',
        'copy' => 'After the Board formulas, identify an external or channel-level Wind presentation, then distinguish Cold, Heat, Phlegm, Dampness, Blood deficiency, trauma, and toxic emergency patterns.',
        'copyEs' => 'Después de las fórmulas del Board, identifica un cuadro de Viento externo o de canales y distingue Frío, Calor, Flema, Humedad, deficiencia de Sangre, trauma y patrones tóxicos de urgencia.',
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

$chapterinterestinginsights = [
    'dispel-summerheat' => [
        'title' => 'Interesting point: Summerheat may be distributed across other Board competencies',
        'titleEs' => 'Dato interesante: el Calor de Verano puede estar distribuido entre otras competencias del Board',
        'stat' => '0 of 7 direct overlaps',
        'statEs' => '0 de 7 coincidencias directas',
        'intro' => 'The current roster has no dedicated Dispel Summerheat category, yet it tests several component mechanisms through formulas filed under Clear Heat, Expel Dampness, and Tonify.',
        'introEs' => 'El listado vigente no tiene una categoría propia de Dispersar Calor de Verano, aunque evalúa varios mecanismos componentes mediante fórmulas clasificadas en Aclarar Calor, Expulsar Humedad y Tonificar.',
        'rows' => [
            ['coverage' => 'Strong Heat with thirst and fluid injury', 'coverageEs' => 'Calor intenso con sed y lesión de líquidos', 'boardNames' => ['Bai Hu Tang'], 'additional' => 'Seasonal Heat presentations differentiated by exterior signs, Dampness, and residual Heat.', 'additionalEs' => 'Cuadros estacionales de Calor diferenciados por signos de Exterior, Humedad y Calor residual.'],
            ['coverage' => 'Dampness with exterior and digestive symptoms', 'coverageEs' => 'Humedad con síntomas exteriores y digestivos', 'boardNames' => ['Huo Xiang Zheng Qi San'], 'additional' => 'Xiang Ru San, Liu Yi San, and related formulas grade the balance of Summerheat, Dampness, water, and the exterior.', 'additionalEs' => 'Xiang Ru San, Liu Yi San y fórmulas relacionadas gradúan el equilibrio entre Calor de Verano, Humedad, agua y Exterior.'],
            ['coverage' => 'Qi-Yin and fluid depletion after Heat', 'coverageEs' => 'Agotamiento de Qi-Yin y líquidos después del Calor', 'boardNames' => ['Sheng Mai San'], 'additional' => 'Qing Shu Yi Qi Tang and Wu Shi Lian Mei Tang add narrower recovery patterns after prolonged seasonal Heat.', 'additionalEs' => 'Qing Shu Yi Qi Tang y Wu Shi Lian Mei Tang añaden patrones de recuperación más específicos tras Calor estacional prolongado.'],
        ],
        'hypothesis' => 'A reasonable hypothesis is that the Board tests the underlying Heat, Dampness, exterior, and Qi-Yin mechanisms without preserving Summerheat as a separate climatic chapter.',
        'hypothesisEs' => 'Una sospecha razonable es que el Board evalúa los mecanismos subyacentes de Calor, Humedad, Exterior y Qi-Yin sin conservar Calor de Verano como capítulo climático independiente.',
        'value' => 'The seven formulas still matter because season, climate, humidity, thirst, sweating, and digestive signs change the treatment strategy. Suspected heat stroke or severe dehydration requires emergency care.',
        'valueEs' => 'Las siete fórmulas siguen siendo importantes porque estación, clima, humedad, sed, sudoración y signos digestivos cambian la estrategia. Sospecha de golpe de calor o deshidratación grave requiere urgencias.',
    ],
    'warm-interior-cold' => [
        'title' => 'Interesting point: four formulas sample four clinically distinct depths of Interior Cold',
        'titleEs' => 'Dato interesante: cuatro fórmulas representan cuatro profundidades clínicas del Frío interno',
        'stat' => '4 of 11 in the Board roster',
        'statEs' => '4 de 11 en el listado Board',
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
        'title' => 'Interesting point: the Board may decompose mixed formulas into foundational strategies',
        'titleEs' => 'Dato interesante: el Board puede descomponer las fórmulas mixtas en estrategias fundamentales',
        'stat' => '0 of 6 direct overlaps',
        'statEs' => '0 de 6 coincidencias directas',
        'intro' => 'This chapter is built from simultaneous exterior and interior disease. The current roster tests many of the component strategies, but not these six combined prescriptions as their own category.',
        'introEs' => 'Este capítulo se construye sobre enfermedad simultánea de Exterior e Interior. El listado vigente evalúa muchas estrategias componentes, pero no estas seis prescripciones combinadas como categoría propia.',
        'rows' => [
            ['coverage' => 'Harmonize Shaoyang and purge Yangming', 'coverageEs' => 'Armonizar Shaoyang y purgar Yangming', 'boardNames' => ['Xiao Chai Hu Tang', 'Da Cheng Qi Tang'], 'additional' => 'Da Chai Hu Tang integrates both operations in one mixed excess presentation.', 'additionalEs' => 'Da Chai Hu Tang integra ambas operaciones en un solo cuadro mixto de exceso.'],
            ['coverage' => 'Release the exterior while clearing interior Heat', 'coverageEs' => 'Liberar Exterior mientras se aclara Calor interno', 'boardNames' => ['Ge Gen Tang', 'Huang Lian Jie Du Tang'], 'additional' => 'Ge Gen Huang Qin Huang Lian Tang and Fang Feng Tong Sheng San require more specific combinations of diarrhea, Heat, and exterior constraint.', 'additionalEs' => 'Ge Gen Huang Qin Huang Lian Tang y Fang Feng Tong Sheng San exigen combinaciones más específicas de diarrea, Calor y constricción exterior.'],
            ['coverage' => 'Treat exterior signs with interior Cold-Dampness or deficiency', 'coverageEs' => 'Tratar signos exteriores con Frío-Humedad o deficiencia interna', 'boardNames' => ['Huo Xiang Zheng Qi San', 'Li Zhong Wan'], 'additional' => 'Wu Ji San and Gui Zhi Ren Shen Tang combine warming, releasing, transforming, and tonifying in narrower mixed patterns.', 'additionalEs' => 'Wu Ji San y Gui Zhi Ren Shen Tang combinan calentar, liberar, transformar y tonificar en patrones mixtos más estrechos.'],
        ],
        'hypothesis' => 'A plausible explanation is that the Board favors clearer single-strategy anchors, then expects the learner to reason through combinations rather than memorize a separate hybrid category.',
        'hypothesisEs' => 'Una explicación plausible es que el Board favorece anclas claras de una estrategia y espera que el estudiante razone las combinaciones en vez de memorizar una categoría híbrida separada.',
        'value' => 'Bensky’s six formulas remain valuable precisely because real patients can show two layers at once. Their complexity also increases the risk of treating the wrong layer or applying an overly strong method.',
        'valueEs' => 'Las seis fórmulas de Bensky siguen siendo valiosas precisamente porque un paciente real puede mostrar dos capas a la vez. Su complejidad también aumenta el riesgo de tratar la capa equivocada o aplicar un método demasiado fuerte.',
    ],
    'tonify-chapter-8' => [
        'title' => 'Interesting point: nine prototypes compress a forty-five-formula tonification chapter',
        'titleEs' => 'Dato interesante: nueve prototipos comprimen un capítulo de cuarenta y cinco fórmulas tónicas',
        'stat' => '9 of 45 across Board domains',
        'statEs' => '9 de 45 entre los dominios Board',
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
        'title' => 'Interesting point: the official category samples three kinds of leakage, but one lives in another Bensky chapter',
        'titleEs' => 'Dato interesante: la categoría oficial representa tres tipos de pérdida, pero una fórmula vive en otro capítulo de Bensky',
        'stat' => '2 of 17 overlap here; 3 in the Board domain',
        'statEs' => '2 de 17 coinciden aquí; 3 en el dominio Board',
        'intro' => 'Si Shen Wan and Gu Jing Wan overlap directly with Chapter 9. Yu Ping Feng San is the third official Stabilize and Bind formula, but Bensky places it in Chapter 8 because it tonifies defensive Qi while securing the exterior.',
        'introEs' => 'Si Shen Wan y Gu Jing Wan coinciden directamente con el capítulo 9. Yu Ping Feng San es la tercera fórmula oficial de Estabilizar y contener, pero Bensky la coloca en el capítulo 8 porque tonifica Qi defensivo mientras asegura el Exterior.',
        'rows' => [
            ['coverage' => 'Unstable exterior with spontaneous sweating and recurrent invasion', 'coverageEs' => 'Exterior inestable con sudor espontáneo e invasiones repetidas', 'boardNames' => ['Yu Ping Feng San'], 'additional' => 'Mu Li San offers another sweating pattern, while Chapter 9 also separates Lung leakage and late-stage cough.', 'additionalEs' => 'Mu Li San ofrece otro patrón de sudoración, mientras el capítulo 9 también separa pérdida de Pulmón y tos tardía.'],
            ['coverage' => 'Chronic intestinal leakage from Spleen-Kidney Yang deficiency', 'coverageEs' => 'Pérdida intestinal crónica por deficiencia de Yang de Bazo-Riñón', 'boardNames' => ['Si Shen Wan'], 'additional' => 'Bensky distinguishes deficiency-Cold diarrhea, Damp-Heat residue, prolapse, and different levels of astringency.', 'additionalEs' => 'Bensky distingue diarrea por Frío-deficiencia, residuo de Humedad-Calor, prolapso y distintos grados de astringencia.'],
            ['coverage' => 'Persistent uterine bleeding with deficiency and constrained Heat', 'coverageEs' => 'Sangrado uterino persistente con deficiencia y Calor constreñido', 'boardNames' => ['Gu Jing Wan'], 'additional' => 'The chapter adds essence, urinary, pregnancy, vaginal discharge, and other gynecologic leakage patterns.', 'additionalEs' => 'El capítulo añade patrones de pérdida de Esencia, urinarios, embarazo, flujo vaginal y otras pérdidas ginecológicas.'],
        ],
        'hypothesis' => 'The Board may be sampling three high-yield leakage territories—exterior, intestines, and uterus—while leaving the chapter’s many organ- and discharge-specific binders as advanced differentiation.',
        'hypothesisEs' => 'El Board puede estar representando tres territorios de pérdida de alto rendimiento—Exterior, Intestinos y útero—y dejando los numerosos astringentes específicos por órgano y secreción como diferenciación avanzada.',
        'value' => 'The omitted formulas teach an essential safety rule: never bind an unresolved pathogen or excess. Color, odor, temperature, timing, and the presence of Heat or Dampness change whether astringency is appropriate.',
        'valueEs' => 'Las fórmulas omitidas enseñan una regla esencial de seguridad: nunca contener un patógeno o exceso no resuelto. Color, olor, temperatura, momento y presencia de Calor o Humedad cambian si la astringencia es apropiada.',
    ],
    'calm-shen-chapter-10' => [
        'title' => 'Interesting point: the Board favors nourishing Shen formulas over the mineral-heavy historical branch',
        'titleEs' => 'Dato interesante: el Board favorece fórmulas que nutren el Shen sobre la rama histórica rica en minerales',
        'stat' => '3 of 9 in the Board roster',
        'statEs' => '3 de 9 en el listado Board',
        'intro' => 'All three official formulas belong to the nourishing side of the chapter and present recognizable deficiency patterns rather than relying primarily on heavy settling substances.',
        'introEs' => 'Las tres fórmulas oficiales pertenecen al lado nutritivo del capítulo y presentan patrones reconocibles de deficiencia, en vez de depender principalmente de sustancias pesadas que asientan.',
        'rows' => [
            ['coverage' => 'Liver Blood and Heart Yin deficiency with insomnia', 'coverageEs' => 'Deficiencia de Sangre de Hígado y Yin de Corazón con insomnio', 'boardNames' => ['Suan Zao Ren Tang'], 'additional' => 'Ding Zhi Wan and Huang Lian E Jiao Tang add Qi deficiency, Phlegm, constrained Fire, and Heart-Kidney disharmony.', 'additionalEs' => 'Ding Zhi Wan y Huang Lian E Jiao Tang añaden deficiencia de Qi, Flema, Fuego constreñido y desarmonía Corazón-Riñón.'],
            ['coverage' => 'Heart-Kidney Yin and Blood deficiency', 'coverageEs' => 'Deficiencia de Yin y Sangre de Corazón-Riñón', 'boardNames' => ['Tian Wang Bu Xin Dan'], 'additional' => 'Bensky grades deeper Fire, sensory symptoms, and heavier anchoring methods.', 'additionalEs' => 'Bensky gradúa Fuego más profundo, síntomas sensoriales y métodos de anclaje más pesados.'],
            ['coverage' => 'Zang Zao with emotional lability', 'coverageEs' => 'Zang Zao con labilidad emocional', 'boardNames' => ['Gan Mai Da Zao Tang'], 'additional' => 'The sedating branch includes Zhu Sha, magnetite, iron filings, and mother-of-pearl formulas with greater sourcing and safety complexity.', 'additionalEs' => 'La rama sedante incluye fórmulas con Zhu Sha, magnetita, limaduras de hierro y madreperla, con mayor complejidad de procedencia y seguridad.'],
        ],
        'hypothesis' => 'A strong clinical suspicion is that broad deficiency archetypes are easier to standardize nationally, while cinnabar-containing and heavy-mineral formulas raise toxicity, quality-control, and modern-use concerns.',
        'hypothesisEs' => 'Una sospecha clínica fuerte es que los arquetipos amplios de deficiencia son más fáciles de estandarizar nacionalmente, mientras las fórmulas con cinabrio y minerales pesados plantean problemas de toxicidad, control de calidad y uso moderno.',
        'value' => 'The six additional formulas remain important for recognizing Fire, Phlegm, sensory changes, and historical sedating strategies—but historical Zhu Sha contains mercury and should not be treated as a routine modern ingredient.',
        'valueEs' => 'Las seis fórmulas adicionales siguen siendo importantes para reconocer Fuego, Flema, cambios sensoriales y estrategias sedantes históricas; pero el Zhu Sha histórico contiene mercurio y no debe considerarse un ingrediente moderno rutinario.',
    ],
    'open-orifices-chapter-11' => [
        'title' => 'Interesting point: an entire classical family can remain outside a modern national roster',
        'titleEs' => 'Dato interesante: una familia clásica completa puede quedar fuera de un listado nacional moderno',
        'stat' => '0 of 7 direct overlaps',
        'statEs' => '0 de 7 coincidencias directas',
        'intro' => 'The seven formulas address hot or cold sensory-orifice closure with impaired consciousness—presentations that overlap modern neurologic, infectious, toxicologic, or cardiopulmonary emergencies.',
        'introEs' => 'Las siete fórmulas tratan cierre caliente o frío de los orificios sensoriales con alteración de conciencia, cuadros que se superponen con urgencias neurológicas, infecciosas, toxicológicas o cardiopulmonares modernas.',
        'rows' => [
            ['coverage' => 'Routine Board roster', 'coverageEs' => 'Listado terapéutico rutinario del Board', 'boardNames' => [], 'additional' => 'Bensky preserves hot closure, cold closure, pediatric convulsion, Phlegm, internal Wind, and historical revival strategies.', 'additionalEs' => 'Bensky conserva cierre caliente, cierre frío, convulsión pediátrica, Flema, Viento interno y estrategias históricas de reanimación.'],
            ['coverage' => 'Modern emergency and safety competence', 'coverageEs' => 'Competencia moderna de urgencia y seguridad', 'boardNames' => [], 'additional' => 'The chapter requires recognizing protected species, animal-derived substances, metals, variable substitutions, and a hazardous nasal administration route.', 'additionalEs' => 'El capítulo exige reconocer especies protegidas, sustancias animales, metales, sustituciones variables y una vía nasal peligrosa.'],
        ],
        'hypothesis' => 'The strongest suspicion is not that the theory lacks value, but that emergency acuity, obsolete or restricted ingredients, substitution variability, and standardization problems make the family poorly suited to a routine national formula roster.',
        'hypothesisEs' => 'La sospecha más fuerte no es que la teoría carezca de valor, sino que la urgencia, los ingredientes obsoletos o restringidos, la variabilidad de sustituciones y los problemas de estandarización hacen que la familia sea poco adecuada para un listado nacional rutinario.',
        'value' => 'Its educational value is safety recognition and differential diagnosis. Altered consciousness, seizure, collapse, severe fever, or respiratory compromise requires emergency services, not self-treatment with a classical formula.',
        'valueEs' => 'Su valor educativo está en reconocer seguridad y diagnóstico diferencial. Alteración de conciencia, convulsión, colapso, fiebre grave o compromiso respiratorio requieren urgencias, no automedicación con una fórmula clásica.',
    ],
    'regulate-qi-chapter-12' => [
        'title' => 'Interesting point: five formulas map the two master directions of Qi pathology',
        'titleEs' => 'Dato interesante: cinco fórmulas cartografían las dos direcciones maestras de la patología del Qi',
        'stat' => '5 of 20 in the Board roster',
        'statEs' => '5 de 20 en el listado Board',
        'intro' => 'The Board sample includes Qi that is stuck and Qi that rebels upward, then distributes those dynamics across general constraint, throat, Lung, and Stomach presentations.',
        'introEs' => 'La muestra del Board incluye Qi estancado y Qi que asciende en contracorriente, y distribuye esas dinámicas entre cuadros generales, garganta, Pulmón y Estómago.',
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
        'title' => 'Interesting point: the Board samples Blood stasis, but none of the chapter’s stop-bleeding formulas',
        'titleEs' => 'Dato interesante: el Board representa estasis de Sangre, pero ninguna fórmula hemostática del capítulo',
        'stat' => '4 of 23 in the Board roster',
        'statEs' => '4 de 23 en el listado Board',
        'intro' => 'All four overlaps come from the section that invigorates Blood and dispels stasis. The eight principal stop-bleeding formulas and eleven additional stasis formulas remain outside the official roster.',
        'introEs' => 'Las cuatro coincidencias provienen de la sección que vigoriza Sangre y dispersa estasis. Las ocho fórmulas principales para detener sangrado y once fórmulas adicionales de estasis quedan fuera del listado oficial.',
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
];

$supplementalindexcategories = [];
foreach ($supplementalcategories as $categorykey => $category) {
    if (!in_array($categorykey, $tonifychapterkeys, true)
            && $categorykey !== 'warm-interior-cold'
            && $categorykey !== 'stabilize-bind-ch9'
            && $categorykey !== 'calm-shen-ch10'
            && $categorykey !== 'open-orifices-ch11'
            && $categorykey !== 'regulate-qi-ch12'
            && $categorykey !== 'regulate-blood-ch13'
            && $categorykey !== 'expel-wind-ch14') {
        $supplementalindexcategories[$categorykey] = $category;
    }
}
$supplementalindexcategories[$tonifychapteroverview['key']] = $tonifychapteroverview;
$supplementalindexcategories[$openorificeschapteroverview['key']] = $openorificeschapteroverview;
$supplementalcategories[$tonifychapteroverview['key']] = $tonifychapteroverview;
$supplementalcategories[$stabilizechapteroverview['key']] = $stabilizechapteroverview;
$supplementalcategories[$calmshenchapteroverview['key']] = $calmshenchapteroverview;
$supplementalcategories[$openorificeschapteroverview['key']] = $openorificeschapteroverview;
$supplementalcategories[$regulateqichapteroverview['key']] = $regulateqichapteroverview;
$supplementalcategories[$regulatebloodchapteroverview['key']] = $regulatebloodchapteroverview;
$supplementalcategories[$expelwindchapteroverview['key']] = $expelwindchapteroverview;
unset($supplementalcategories['stabilize-bind-ch9']);
unset($supplementalcategories['calm-shen-ch10']);
unset($supplementalcategories['open-orifices-ch11']);
unset($supplementalcategories['regulate-qi-ch12']);
unset($supplementalcategories['regulate-blood-ch13']);
unset($supplementalcategories['expel-wind-ch14']);

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
.formula-study-guide__scope-analysis{margin:0 0 20px;border:1px solid #edc989;border-left:5px solid #d97706;border-radius:11px;background:#fffaf0;overflow:hidden}.formula-study-guide__scope-analysis-header{padding:18px 20px}.formula-study-guide__scope-analysis-header h4{margin:0 0 7px;font-size:20px}.formula-study-guide__scope-analysis-header p{margin:0;color:#68543a;line-height:1.55}.formula-study-guide__scope-analysis-header p+p{margin-top:8px}.formula-study-guide__scope-analysis-caveat{font-size:13px}.formula-study-guide__scope-analysis-board-note{border-top:1px solid #edc989;border-bottom:1px solid #edc989;background:#fff4df}.formula-study-guide__scope-analysis .formula-study-guide__table-wrap{background:#fff}.formula-study-guide__scope-analysis .formula-study-guide__table td:first-child{white-space:normal}.formula-study-guide__scope-analysis-count{display:inline-flex;align-items:center;margin-right:6px;padding:3px 7px;border-radius:999px;background:#ffedd5;color:#9a3412;font-size:11px;font-weight:900;white-space:nowrap}
.formula-study-guide__medical-rationale{margin:0 0 20px;border:1px solid #c4d7f2;border-left:5px solid #315fa5;border-radius:11px;background:#f5f8ff;overflow:hidden}.formula-study-guide__medical-rationale-header{padding:18px 20px}.formula-study-guide__medical-rationale-header h4{margin:0 0 7px;color:#173f78;font-size:20px}.formula-study-guide__medical-rationale-header p{margin:0;color:#435d7a;line-height:1.55}.formula-study-guide__medical-rationale-header p+p{margin-top:8px}.formula-study-guide__medical-rationale-stat{display:inline-flex;margin:0 0 9px;padding:4px 9px;border-radius:999px;background:#dce9ff;color:#214f91;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.03em}.formula-study-guide__medical-rationale .formula-study-guide__table-wrap{background:#fff}.formula-study-guide__medical-rationale .formula-study-guide__table td:first-child{white-space:normal}.formula-study-guide__medical-rationale-board{color:#173f78;font-weight:800}.formula-study-guide__medical-rationale-secondary{border-top:1px solid #c4d7f2;background:#eef4ff}.formula-study-guide__medical-rationale-caveat{font-size:13px}
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
.formula-study-guide__additional{margin:20px 0;border:1px solid #add8cf;border-left:5px solid #0b817d;border-radius:11px;background:#effaf7}.formula-study-guide__additional>summary{padding:17px 18px;color:#075e54;font-size:18px;font-weight:900;cursor:pointer}.formula-study-guide__additional-body{padding:0 18px 18px}.formula-study-guide__additional-body>p{margin:0 0 15px;color:#496474;line-height:1.5}.formula-study-guide__additional-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:15px}.formula-study-guide__additional-group{padding:15px;border-radius:9px;background:#fff}.formula-study-guide__additional-group h5{margin:0 0 9px;color:#17232a;font-size:15px}.formula-study-guide__additional-group ol{margin:0;padding-left:22px}.formula-study-guide__additional-group li{margin:0 0 7px;color:#52677a;line-height:1.4}.formula-study-guide__additional-group a{color:#174c69;font-weight:750;text-decoration:none}.formula-study-guide__additional-group a:hover{color:#075fc8;text-decoration:underline}
.formula-study-guide__source{margin-top:20px;padding:14px 17px;border-radius:9px;background:#f1f5f9;color:#52677a;font-size:13px;line-height:1.5}
@media(max-width:900px){.formula-study-guide__route,.formula-study-guide__reference-groups{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:800px){.formula-study-guide__top{display:block}.formula-study-guide__actions{justify-content:flex-start;margin-top:15px}.formula-study-guide__facts,.formula-study-guide__deep-grid,.formula-study-guide__branch-body{grid-template-columns:1fr}.formula-study-guide__chapter-shortcut-links{grid-template-columns:repeat(2,minmax(0,1fr))}.formula-study-guide__board-roster-grid,.formula-study-guide__chapter-directory-grid,.formula-study-guide__additional-grid{grid-template-columns:1fr}.formula-study-guide__board-verification,.formula-study-guide__formula-footer,.formula-study-guide__modification{align-items:flex-start;flex-direction:column}.formula-study-guide__modification a{width:100%}.formula-study-guide__overview{grid-template-columns:1fr}.formula-study-guide__stats{justify-content:flex-start}.formula-study-guide__supplement-heading{display:block}.formula-study-guide__supplement-badge{display:inline-block;margin-top:10px}}
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
                $iscalmshenindexcard = $category['key'] === 'calm-shen';
                $isstabilizeindexcard = $category['key'] === 'stabilize-bind';
                $isregulateqiindexcard = $category['key'] === 'regulate-qi';
                $isinvigoratebloodindexcard = $category['key'] === 'invigorate-blood';
                $isexpelwindindexcard = $category['key'] === 'expel-wind';
                $iscombinedindexcard = $iscalmshenindexcard || $isstabilizeindexcard || $isregulateqiindexcard || $isinvigoratebloodindexcard || $isexpelwindindexcard;
                $categoryname = $iscalmshenindexcard
                    ? ($ises ? 'Calmar el Shen / Calmar el Espíritu' : 'Calm Shen / Calm the Spirit')
                    : ($ises ? $category['nameEs'] : $category['name']);
                if ($iscalmshenindexcard) {
                    $categorymeta = $ises
                        ? '3 fórmulas Board · Capítulo 10 de Bensky: 9'
                        : '3 Board formulas · Bensky Chapter 10: 9';
                } elseif ($isstabilizeindexcard) {
                    $categorymeta = $ises
                        ? '3 fórmulas Board · Capítulo 9 de Bensky: 17'
                        : '3 Board formulas · Bensky Chapter 9: 17';
                } elseif ($isregulateqiindexcard) {
                    $categorymeta = $ises
                        ? '5 fórmulas Board · Capítulo 12 de Bensky: 20'
                        : '5 Board formulas · Bensky Chapter 12: 20';
                } elseif ($isinvigoratebloodindexcard) {
                    $categorymeta = $ises
                        ? '4 fórmulas Board · Capítulo 13 de Bensky: 23'
                        : '4 Board formulas · Bensky Chapter 13: 23';
                } elseif ($isexpelwindindexcard) {
                    $categorymeta = $ises
                        ? '3 fórmulas Board · Capítulo 14 de Bensky: 17'
                        : '3 Board formulas · Bensky Chapter 14: 17';
                } else {
                    $categorymeta = count($category['formulas']) . ' ' . ($ises ? 'fórmulas' : 'formulas');
                }
                ?>
                <a class="formula-study-guide__category" href="<?php echo $categoryurl->out(false); ?>">
                    <span class="formula-study-guide__category-number"><?php echo (int) $category['number']; ?></span>
                    <strong><?php echo s($categoryname); ?></strong>
                    <span class="formula-study-guide__category-meta"><?php echo s($categorymeta); ?></span>
                    <p><?php echo s($copy); ?></p>
                    <?php if ($reference !== null): ?><span class="formula-study-guide__category-reference"><?php echo s($reference); ?></span><?php endif; ?>
                    <small><?php echo s($iscombinedindexcard
                        ? ($ises ? 'Abrir familia unificada →' : 'Open unified family →')
                        : ($ises ? 'Abrir categoría →' : 'Open category →')); ?></small>
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
            <?php if ($selectedformulasections !== []): ?>
                <section class="formula-study-guide__medical-rationale" aria-labelledby="clinical-frame-title">
                    <div class="formula-study-guide__medical-rationale-header">
                        <span class="formula-study-guide__medical-rationale-stat"><?php echo s(count($selected['formulas']) . ($ises ? ' fórmulas Board · ' : ' Board formulas · ') . count($selectedformulasections) . ($ises ? ' secciones clínicas' : ' clinical sections')); ?></span>
                        <h4 id="clinical-frame-title"><?php echo s($ises ? $selectedguide['clinicalFrameTitleEs'] : $selectedguide['clinicalFrameTitle']); ?></h4>
                        <p><?php echo s($ises ? $selectedguide['clinicalFrameTextEs'] : $selectedguide['clinicalFrameText']); ?></p>
                    </div>
                    <div class="formula-study-guide__table-wrap">
                        <table class="formula-study-guide__table">
                            <thead>
                                <tr>
                                    <th><?php echo $ises ? 'Sección clínica' : 'Clinical section'; ?></th>
                                    <th><?php echo $ises ? 'Centro diagnóstico' : 'Diagnostic center'; ?></th>
                                    <th><?php echo $ises ? 'Fórmulas Board' : 'Board formulas'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selectedformulasections as $formulasection): ?>
                                    <tr>
                                        <td><?php echo s($ises ? $formulasection['nameEs'] : $formulasection['name']); ?></td>
                                        <td><?php echo s($ises ? $formulasection['diagnosticEs'] : $formulasection['diagnostic']); ?></td>
                                        <td class="formula-study-guide__medical-rationale-board">
                                            <?php foreach ($formulasection['formulaNames'] as $sectionformulaindex => $sectionformulaname): ?>
                                                <?php $sectionboardformula = $getboardformula($sectionformulaname); ?>
                                                <?php if ($sectionformulaindex > 0): ?>, <?php endif; ?>
                                                <a href="#formula-<?php echo s((string) $sectionboardformula['number']); ?>"><?php echo s($displayformulaname($sectionformulaname)); ?></a>
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
                        <span class="formula-study-guide__scope-analysis-count"><?php echo s(count($selectedguide['therapeuticClasses']) . ($ises ? ' clases terapéuticas' : ' therapeutic classes')); ?></span>
                        <h4 id="therapeutic-classification-title"><?php echo s($ises ? $selectedguide['classificationTitleEs'] : $selectedguide['classificationTitle']); ?></h4>
                        <p><?php echo s($ises ? $selectedguide['classificationTextEs'] : $selectedguide['classificationText']); ?></p>
                    </header>
                    <div class="formula-study-guide__table-wrap">
                        <table class="formula-study-guide__table">
                            <thead>
                                <tr>
                                    <th><?php echo $ises ? 'Clase terapéutica' : 'Therapeutic class'; ?></th>
                                    <th><?php echo $ises ? 'Qué hace' : 'What it does'; ?></th>
                                    <th><?php echo $ises ? 'Fórmulas representativas' : 'Representative formulas'; ?></th>
                                    <th><?php echo $ises ? 'Separador crítico' : 'Critical separator'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($selectedguide['therapeuticClasses'] as $therapeuticclass): ?>
                                    <tr>
                                        <td><strong><?php echo s($ises ? $therapeuticclass['nameEs'] : $therapeuticclass['name']); ?></strong></td>
                                        <td><?php echo s($ises ? $therapeuticclass['logicEs'] : $therapeuticclass['logic']); ?></td>
                                        <td>
                                            <?php if ($therapeuticclass['boardNames'] !== []): ?>
                                                <strong><?php echo $ises ? 'Board:' : 'Board:'; ?></strong>
                                                <?php foreach ($therapeuticclass['boardNames'] as $classnameindex => $classname): ?>
                                                    <?php $classboardformula = $getboardformula($classname); ?>
                                                    <?php if ($classnameindex > 0): ?>, <?php endif; ?>
                                                    <a href="#formula-<?php echo s((string) $classboardformula['number']); ?>"><?php echo s($displayformulaname($classname)); ?></a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <?php if ($therapeuticclass['boardNames'] !== [] && $therapeuticclass['otherNames'] !== []): ?><br><?php endif; ?>
                                            <?php if ($therapeuticclass['otherNames'] !== []): ?>
                                                <strong><?php echo $ises ? 'Referencia:' : 'Reference:'; ?></strong>
                                                <?php echo s(implode(', ', $therapeuticclass['otherNames'])); ?>
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
                            <span class="formula-study-guide__scope-analysis-count"><?php echo s($ises ? 'Fuera del Appendix C vigente' : 'Outside the current Appendix C'); ?></span>
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
            <?php $quickcomparesections = $selectedformulasections !== [] ? $selectedformulasections : [['key' => 'all', 'name' => $selectedguide['quickCompareTitle'], 'nameEs' => $selectedguide['quickCompareTitleEs'], 'formulaNames' => array_column($selectedguide['quickCompare'], 0)]]; ?>
            <?php foreach ($quickcomparesections as $comparesection): ?>
                <?php $compareformulanames = array_flip($comparesection['formulaNames']); ?>
                <?php $comparerows = array_values(array_filter($selectedguide['quickCompare'], static fn(array $row): bool => isset($compareformulanames[$row[0]]))); ?>
                <section class="formula-study-guide__compare" aria-labelledby="core-comparison-<?php echo s($comparesection['key']); ?>-title">
                    <h4 id="core-comparison-<?php echo s($comparesection['key']); ?>-title"><?php echo s($selectedformulasections !== []
                        ? (($ises ? 'Comparación · ' : 'Comparison · ') . ($ises ? $comparesection['nameEs'] : $comparesection['name']))
                        : ($ises ? $selectedguide['quickCompareTitleEs'] : $selectedguide['quickCompareTitle'])); ?></h4>
                    <div class="formula-study-guide__table-wrap">
                        <table class="formula-study-guide__table">
                            <thead><tr><th><?php echo $ises ? 'Fórmula' : 'Formula'; ?></th><th><?php echo $ises ? 'Patrón' : 'Pattern'; ?></th><th><?php echo $ises ? 'Separador decisivo' : 'Decisive separator'; ?></th></tr></thead>
                            <tbody>
                                <?php foreach ($comparerows as $row): ?>
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
            <?php endforeach; ?>
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
        <?php if ($selected['key'] === 'regulate-qi-chapter-12'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 12, páginas 505–554.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 12, pages 505–554.'); ?></p>
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
                    <div class="formula-study-guide__route-step"><b><span class="formula-study-guide__route-number">4</span><?php echo s($ises ? 'Usa el separador decisivo' : 'Use the decisive separator'); ?></b><p><?php echo s($ises ? 'Momento del vómito, tipo de esputo, sed, lengua, pulso y relación con emociones, comida o frío.' : 'Timing of vomiting, sputum quality, thirst, tongue, pulse, and relationship to emotion, food, or Cold.'); ?></p></div>
                </div>
            </section>
            <section class="formula-study-guide__board-verification" aria-labelledby="chapter-twelve-board-title">
                <div>
                    <h4 id="chapter-twelve-board-title"><?php echo s($ises ? 'Dato relevante para el examen · 5 de 20 en Appendix C 2026' : 'Exam-relevant fact · 5 of 20 in the 2026 Appendix C'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco marca las cinco fórmulas del capítulo que sí pertenecen al listado oficial vigente. Las otras quince amplían la comparación clínica de Bensky, pero no deben desplazar la prioridad de las fórmulas Board.' : 'An asterisk marks the five chapter formulas that belong to the current official roster. The other fifteen extend Bensky’s clinical comparison but should not displace Board-formula priority.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Board Domain →' : 'Verify Board Domain →'); ?></a>
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
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo s($displayformulaname($chapterformula['name'])); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'regulate-blood-chapter-13'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 13, páginas 557–615.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 13, pages 557–615.'); ?></p>
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
                    <h4 id="chapter-thirteen-board-title"><?php echo s($ises ? 'Coincidencia con los Board Domains · 4 de 23' : 'Board Domains overlap · 4 of 23'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco marca Tao He Cheng Qi Tang (#45), Xue Fu Zhu Yu Tang (#46), Wen Jing Tang (#47) y Gui Zhi Fu Ling Wan (#48). Las otras 19 fichas completan Bensky sin ampliar el listado oficial de 63.' : 'An asterisk marks Tao He Cheng Qi Tang (#45), Xue Fu Zhu Yu Tang (#46), Wen Jing Tang (#47), and Gui Zhi Fu Ling Wan (#48). The other 19 cards complete Bensky without enlarging the official roster of 63.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Board Domain →' : 'Verify Board Domain →'); ?></a>
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
                                    <li><a href="#formula-<?php echo s($chapterformulaid); ?>"><?php echo s($displayformulaname($chapterformula['name'])); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($selected['key'] === 'expel-wind-chapter-14'): ?>
            <p class="formula-study-guide__source"><strong><?php echo $ises ? 'Fuente del capítulo:' : 'Chapter source:'; ?></strong> <?php echo s($ises
                ? 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, capítulo 14, páginas 619–657.'
                : 'Bensky et al., Chinese Herbal Medicine: Formulas & Strategies, Chapter 14, pages 619–657.'); ?></p>
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
                    <h4 id="chapter-fourteen-board-title"><?php echo s($ises ? 'Coincidencia con los Board Domains · 3 de 17' : 'Board Domains overlap · 3 of 17'); ?></h4>
                    <p><?php echo s($ises ? 'El asterisco marca Xiao Feng San (#55), Xiao Huo Luo Dan (#56) y Tian Ma Gou Teng Yin (#57). Las otras 14 fichas completan Bensky sin ampliar el listado oficial de 63.' : 'An asterisk marks Xiao Feng San (#55), Xiao Huo Luo Dan (#56), and Tian Ma Gou Teng Yin (#57). The other 14 cards complete Bensky without enlarging the official roster of 63.'); ?></p>
                </div>
                <a class="formula-study-guide__button formula-study-guide__button--primary" href="<?php echo $guidelinerosterurl->out(false); ?>"><?php echo s($ises ? 'Comprobar Board Domain →' : 'Verify Board Domain →'); ?></a>
            </section>
            <section class="formula-study-guide__medical-rationale" aria-labelledby="chapter-fourteen-medical-rationale-title">
                <div class="formula-study-guide__medical-rationale-header">
                    <h4 id="chapter-fourteen-medical-rationale-title"><?php echo s($ises ? 'Explicación médica: por qué estas tres son representativas' : 'Medical explanation: why these three are representative'); ?></h4>
                    <p><?php echo s($ises
                        ? 'En medicina china, “Viento” no designa una sola enfermedad. Describe manifestaciones súbitas, cambiantes o móviles—como prurito migratorio, espasmo, temblor y vértigo—y obliga a preguntar dónde se manifiesta y qué mecanismo lo impulsa.'
                        : 'In Chinese medicine, “Wind” does not name a single disease. It describes sudden, changing, or moving manifestations—such as migrating itch, spasm, tremor, and vertigo—and requires asking where it appears and what mechanism drives it.'); ?></p>
                    <p><?php echo s($ises
                        ? 'Las tres fórmulas del Board condensan tres territorios clínicos amplios: piel, canales dolorosos e Interior. Juntas permiten evaluar localización, raíz y manifestación sin convertir todo el capítulo clásico en contenido prioritario de examen.'
                        : 'The three Board formulas condense three broad clinical territories: skin, painful channels, and the Interior. Together they test location, root, and manifestation without making the entire classical chapter a primary exam priority.'); ?></p>
                </div>
                <div class="formula-study-guide__table-wrap">
                    <table class="formula-study-guide__table">
                        <thead>
                            <tr>
                                <th><?php echo $ises ? 'Fórmula' : 'Formula'; ?></th>
                                <th><?php echo $ises ? 'Dónde se manifiesta' : 'Where it manifests'; ?></th>
                                <th><?php echo $ises ? 'Mecanismo' : 'Mechanism'; ?></th>
                                <th><?php echo $ises ? 'Presentación característica' : 'Characteristic presentation'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo s($displayformulaname('Xiao Feng San')); ?></td>
                                <td><?php echo s($ises ? 'Piel' : 'Skin'); ?></td>
                                <td><?php echo s($ises ? 'Viento-Humedad-Calor con Sequedad de Sangre subyacente' : 'Wind-Damp-Heat with underlying Blood dryness'); ?></td>
                                <td><?php echo s($ises ? 'Erupción roja, exudativa, cambiante y muy pruriginosa' : 'Red, weeping, changing, intensely itchy eruption'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo s($displayformulaname('Xiao Huo Luo Dan')); ?></td>
                                <td><?php echo s($ises ? 'Canales y articulaciones' : 'Channels and joints'); ?></td>
                                <td><?php echo s($ises ? 'Viento-Frío-Humedad, Flema y estasis de Sangre' : 'Wind-Cold-Dampness, Phlegm, and Blood stasis'); ?></td>
                                <td><?php echo s($ises ? 'Dolor crónico agravado por frío, rigidez, entumecimiento y movilidad reducida' : 'Chronic cold-aggravated pain, stiffness, numbness, and reduced movement'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo s($displayformulaname('Tian Ma Gou Teng Yin')); ?></td>
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
                        ? 'Por eso son valiosas para comprender diagnóstico diferencial, historia, seguridad y límites clínicos, aunque no formen parte del listado oficial de 63 fórmulas. Ictus, convulsiones, pérdida de conciencia, tétanos, meningitis o crisis hipertensiva requieren atención médica inmediata; una fórmula clásica no sustituye el tratamiento agudo.'
                        : 'They therefore remain valuable for understanding differential diagnosis, history, safety, and clinical limits even though they are outside the official 63-formula roster. Stroke, seizure, loss of consciousness, tetanus, meningitis, or hypertensive crisis require immediate medical care; a classical formula does not replace acute treatment.'); ?></p>
                    <p class="formula-study-guide__medical-rationale-caveat"><?php echo s($ises
                        ? 'Nota de alcance: el Board no publica una justificación médica fórmula por fórmula. Esta es una interpretación clínica y educativa basada en la cobertura de patrones, la aplicabilidad y la seguridad; no una declaración oficial del Board.'
                        : 'Scope note: the Board does not publish a formula-by-formula medical rationale. This is a clinical and educational interpretation based on pattern coverage, applicability, and safety—not an official Board statement.'); ?></p>
                </div>
            </section>
            <section class="formula-study-guide__chapter-directory" aria-labelledby="chapter-fourteen-directory-title">
                <h4 id="chapter-fourteen-directory-title"><?php echo s($ises ? 'Listado completo del capítulo 14' : 'Complete Chapter 14 directory'); ?></h4>
                <p><?php echo s($ises ? 'Las 3 fórmulas del Board aparecen primero. Después siguen las otras 14 fórmulas, organizadas según las dos secciones de la fuente. Las fórmulas asociadas y variaciones permanecen dentro de su ficha principal.' : 'The 3 Board formulas appear first. The other 14 formulas then follow, organized by the source’s two sections. Associated formulas and variations remain inside their principal card.'); ?></p>
                <div class="formula-study-guide__chapter-directory-grid">
                    <?php foreach ($expelwindchaptersections as $sectionkey => $section): ?>
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
        <?php if (isset($chapterinterestinginsights[$selected['key']])): ?>
            <?php $chapterinsight = $chapterinterestinginsights[$selected['key']]; ?>
            <section class="formula-study-guide__medical-rationale" aria-labelledby="chapter-interesting-insight-title">
                <div class="formula-study-guide__medical-rationale-header">
                    <span class="formula-study-guide__medical-rationale-stat"><?php echo s($ises ? $chapterinsight['statEs'] : $chapterinsight['stat']); ?></span>
                    <h4 id="chapter-interesting-insight-title"><?php echo s($ises ? $chapterinsight['titleEs'] : $chapterinsight['title']); ?></h4>
                    <p><?php echo s($ises ? $chapterinsight['introEs'] : $chapterinsight['intro']); ?></p>
                </div>
                <div class="formula-study-guide__table-wrap">
                    <table class="formula-study-guide__table">
                        <thead>
                            <tr>
                                <th><?php echo $ises ? 'Cobertura clínica que el Board ya evalúa' : 'Clinical coverage the Board already tests'; ?></th>
                                <th><?php echo $ises ? 'Fórmulas Board representativas' : 'Representative Board formulas'; ?></th>
                                <th><?php echo $ises ? 'Qué añade Bensky fuera del Board' : 'What Bensky adds outside the Board'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($chapterinsight['rows'] as $insightrow): ?>
                                <?php $boardlabels = array_map($displayformulaname, $insightrow['boardNames']); ?>
                                <tr>
                                    <td><?php echo s($ises ? $insightrow['coverageEs'] : $insightrow['coverage']); ?></td>
                                    <td class="formula-study-guide__medical-rationale-board"><?php echo s($boardlabels === []
                                        ? ($ises ? 'Ninguna fórmula directa en el listado vigente' : 'No direct formula in the current roster')
                                        : implode(', ', $boardlabels)); ?></td>
                                    <td><?php echo s($ises ? $insightrow['additionalEs'] : $insightrow['additional']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="formula-study-guide__medical-rationale-header formula-study-guide__medical-rationale-secondary">
                    <h4><?php echo s($ises ? 'Una sospecha clínica y curricular razonable' : 'A reasonable clinical and curricular hypothesis'); ?></h4>
                    <p><?php echo s($ises ? $chapterinsight['hypothesisEs'] : $chapterinsight['hypothesis']); ?></p>
                    <p><strong><?php echo $ises ? 'Por qué sigue siendo interesante:' : 'Why this remains interesting:'; ?></strong> <?php echo s($ises ? $chapterinsight['valueEs'] : $chapterinsight['value']); ?></p>
                    <p class="formula-study-guide__medical-rationale-caveat"><?php echo s($ises
                        ? 'Nota de alcance: el Board no publica una justificación médica fórmula por fórmula. Esta es una hipótesis clínica y educativa basada en cobertura de patrones, aplicabilidad, seguridad y organización del temario; no una declaración oficial del Board.'
                        : 'Scope note: the Board does not publish a formula-by-formula medical rationale. This is a clinical and educational hypothesis based on pattern coverage, applicability, safety, and syllabus organization—not an official Board statement.'); ?></p>
                </div>
            </section>
        <?php endif; ?>
        <section class="formula-study-guide__formula-list" aria-label="<?php echo s($ises ? 'Fórmulas de la categoría' : 'Category formulas'); ?>">
            <?php $chapterformulaindex = 0; $lastformulacategory = null; $lastclinicalsectionkey = null; ?>
            <?php foreach ($selected['formulas'] as $formula): ?>
                <?php
                $chapterformulaindex++;
                $clinicalsection = $selectedformulasectionbyname[$formula['name']] ?? null;
                $newclinicalsection = $clinicalsection !== null && $lastclinicalsectionkey !== $clinicalsection['key'];
                $currentsectionkey = in_array($selected['key'], ['stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11', 'regulate-qi-chapter-12', 'regulate-blood-chapter-13', 'expel-wind-chapter-14'], true)
                    ? $formula['chapterSectionKey'] : $formula['categoryKey'];
                $newchaptersection = in_array($selected['key'], ['tonify-chapter-8', 'stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11', 'regulate-qi-chapter-12', 'regulate-blood-chapter-13', 'expel-wind-chapter-14'], true)
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
                $risklabel = $tier === 'critical'
                    ? ($ises ? 'Seguridad crítica' : 'Critical safety')
                    : ($tier === 'high' ? ($ises ? 'Precaución alta' : 'High caution') : ($ises ? 'Seguridad revisada' : 'Safety reviewed'));
                $formulaid = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $formula['number']);
                $practiceurl = null;
                if (!$issupplemental) {
                    $practicepath = $ises ? '/study-guide/comparisons/formula-practice-es.html' : '/study-guide/comparisons/formula-practice.html';
                    $practiceurl = new moodle_url($practicepath, ['formula' => (int) $formula['number']]);
                }
                if (in_array($selected['key'], ['tonify-chapter-8', 'stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11', 'regulate-qi-chapter-12', 'regulate-blood-chapter-13', 'expel-wind-chapter-14'], true)) {
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
                        <p><?php echo s(in_array($selected['key'], ['stabilize-bind-chapter-9', 'calm-shen-chapter-10', 'open-orifices-chapter-11', 'regulate-qi-chapter-12', 'regulate-blood-chapter-13', 'expel-wind-chapter-14'], true)
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
                <?php if ($newclinicalsection): ?>
                    <section class="formula-study-guide__chapter-section" id="clinical-section-<?php echo s($clinicalsection['key']); ?>">
                        <p class="formula-study-guide__eyebrow"><?php echo s($ises ? 'Sección clínica del guideline' : 'Guideline clinical section'); ?></p>
                        <h4><?php echo s($ises ? $clinicalsection['nameEs'] : $clinicalsection['name']); ?></h4>
                        <p><?php echo s($ises ? $clinicalsection['copyEs'] : $clinicalsection['copy']); ?></p>
                        <span class="formula-study-guide__chapter-section-meta"><?php echo count($clinicalsection['formulaNames']); ?> <?php echo $ises ? 'fórmulas Board' : 'Board formulas'; ?></span>
                    </section>
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
                        <p class="formula-study-guide__exam-focus"><strong><?php echo $issupplemental && $getboardformula($formula['name']) === null
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
