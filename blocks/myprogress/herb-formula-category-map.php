<?php
// Bensky category bridge between single herbs and formulas.

require_once(__DIR__ . '/../../config.php');

require_login();

$ises = optional_param('lang', 'en', PARAM_ALPHA) === 'es';

$formulachapters = [
    1 => ['Formulas That Release the Exterior', 'Fórmulas que liberan el Exterior', 'Ma Huang Tang, Gui Zhi Tang, Yin Qiao San, Sang Ju Yin'],
    2 => ['Formulas That Drain Downward', 'Fórmulas que drenan hacia abajo', 'Da Cheng Qi Tang, Xiao Cheng Qi Tang, Tiao Wei Cheng Qi Tang, Run Chang Wan'],
    3 => ['Formulas That Harmonize', 'Fórmulas que armonizan', 'Xiao Chai Hu Tang, Si Ni San, Ban Xia Xie Xin Tang, Xiao Yao San'],
    4 => ['Formulas That Clear Heat', 'Fórmulas que aclaran el Calor', 'Bai Hu Tang, Huang Lian Jie Du Tang, Qing Ying Tang, Qing Hao Bie Jia Tang'],
    5 => ['Formulas That Dispel Summerheat', 'Fórmulas que eliminan el Calor de Verano', 'Qing Luo Yin, Xiang Ru San, Liu Yi San, Qing Shu Yi Qi Tang'],
    6 => ['Formulas That Warm Interior Cold', 'Fórmulas que calientan el Frío Interior', 'Li Zhong Wan, Xiao Jian Zhong Tang, Si Ni Tang, Dang Gui Si Ni Tang'],
    7 => ['Formulas That Release Exterior-Interior Excess', 'Fórmulas que liberan exceso Exterior-Interior', 'Da Chai Hu Tang, Ge Gen Huang Qin Huang Lian Tang, Gui Zhi Ren Shen Tang'],
    8 => ['Formulas That Tonify', 'Fórmulas que tonifican', 'Si Jun Zi Tang, Si Wu Tang, Ba Zhen Tang, Liu Wei Di Huang Wan'],
    9 => ['Formulas That Stabilize and Bind', 'Fórmulas que estabilizan y contienen', 'Yu Ping Feng San, Mu Li San, Si Shen Wan, Gu Jing Wan'],
    10 => ['Formulas That Calm the Spirit', 'Fórmulas que calman el Espíritu', 'Tian Wang Bu Xin Dan, Suan Zao Ren Tang, Gan Mai Da Zao Tang, Ci Zhu Wan'],
    11 => ['Formulas That Open the Sensory Orifices', 'Fórmulas que abren los orificios sensoriales', 'An Gong Niu Huang Wan, Zi Xue Dan, Su He Xiang Wan'],
    12 => ['Formulas That Regulate the Qi', 'Fórmulas que regulan el Qi', 'Yue Ju Wan, Ban Xia Hou Po Tang, Ding Chuan Tang, Ju Pi Zhu Ru Tang'],
    13 => ['Formulas That Regulate the Blood', 'Fórmulas que regulan la Sangre', 'Xue Fu Zhu Yu Tang, Tao He Cheng Qi Tang, Gui Zhi Fu Ling Wan, Shi Xiao San'],
    14 => ['Formulas That Expel Wind', 'Fórmulas que expulsan el Viento', 'Xiao Feng San, Tian Ma Gou Teng Yin, Zhen Gan Xi Feng Tang, Xiao Huo Luo Dan'],
    15 => ['Formulas That Treat Dryness', 'Fórmulas que tratan la Sequedad', 'Xing Su San, Sang Xing Tang, Qing Zao Jiu Fei Tang, Zeng Ye Tang'],
    16 => ['Formulas That Expel Dampness', 'Fórmulas que expulsan la Humedad', 'Ping Wei San, Huo Xiang Zheng Qi San, Wu Ling San, Du Huo Ji Sheng Tang'],
    17 => ['Formulas That Dispel Phlegm', 'Fórmulas que eliminan la Flema', 'Er Chen Tang, Wen Dan Tang, Qing Qi Hua Tan Wan, Bei Mu Gua Lou San'],
    18 => ['Formulas That Reduce Food Stagnation', 'Fórmulas que reducen el estancamiento de alimentos', 'Bao He Wan, Zhi Shi Dao Zhi Wan, Jian Pi Wan'],
    19 => ['Formulas That Expel Parasites', 'Fórmulas que expulsan parásitos', 'Wu Mei Wan, Hua Chong Wan, Bu Dai Wan'],
    20 => ['Formulas That Treat Abscesses and Sores', 'Fórmulas que tratan abscesos y llagas', 'Xian Fang Huo Ming Yin, Wu Wei Xiao Du Yin, Yang He Tang, Wei Jing Tang'],
    21 => ['Formulas for External Application', 'Fórmulas para aplicación externa', 'Jin Huang San / Gao, Zi Dang Gao, Ku Shen Tang, Hai Tong Pi Tang'],
];

// Card, Bensky chapter, English name, Spanish name, representative herbs, related formula chapters, relationship type.
$herbcategories = [
    [1, 1, 'Warm, Acrid Herbs that Release the Exterior', 'Hierbas tibias y acres que liberan el Exterior', 'Ma Huang, Gui Zhi, Zi Su Ye, Jing Jie, Fang Feng', [1, 7], 'direct'],
    [2, 1, 'Cool, Acrid Herbs that Release the Exterior', 'Hierbas frescas y acres que liberan el Exterior', 'Bo He, Niu Bang Zi, Sang Ye, Ju Hua, Chai Hu, Ge Gen', [1, 7], 'direct'],
    [3, 2, 'Herbs that Clear Heat and Drain Fire', 'Hierbas que aclaran Calor y drenan Fuego', 'Shi Gao, Zhi Mu, Zhi Zi, Xia Ku Cao', [4, 7], 'direct'],
    [4, 2, 'Herbs that Cool the Blood', 'Hierbas que enfrían la Sangre', 'Sheng Di Huang, Xuan Shen, Mu Dan Pi, Chi Shao', [4, 13], 'direct'],
    [5, 2, 'Herbs that Clear Heat and Dry Dampness', 'Hierbas que aclaran Calor y secan Humedad', 'Huang Qin, Huang Lian, Huang Bai, Long Dan Cao, Ku Shen', [4, 16], 'direct'],
    [6, 2, 'Herbs that Clear Heat and Resolve Toxicity', 'Hierbas que aclaran Calor y resuelven toxicidad', 'Jin Yin Hua, Lian Qiao, Pu Gong Ying, Ban Lan Gen, Da Qing Ye', [1, 4, 20], 'direct'],
    [7, 2, 'Herbs that Clear Heat from Deficiency', 'Hierbas que aclaran Calor por deficiencia', 'Qing Hao, Di Gu Pi, Bai Wei, Yin Chai Hu, Hu Huang Lian', [4, 8], 'direct'],
    [8, 2, 'Herbs that Clear Summerheat', 'Hierbas que aclaran Calor de Verano', 'Xiang Ru, He Ye, Lu Dou, Xi Gua', [5], 'exact'],
    [9, 3, 'Downward-Draining Purgatives', 'Purgantes que drenan hacia abajo', 'Da Huang, Mang Xiao, Lu Hui, Fan Xie', [2], 'exact'],
    [10, 3, 'Moist Laxatives', 'Laxantes humectantes', 'Huo Ma Ren, Yu Li Ren', [2, 15], 'direct'],
    [11, 3, 'Harsh Expellants', 'Expulsores drásticos', 'Gan Sui, Da Ji, Yuan Hua, Shang Lu, Ba Dou', [2], 'direct'],
    [12, 4, 'Herbs that Drain Dampness', 'Hierbas que drenan Humedad', 'Fu Ling, Zhu Ling, Ze Xie, Yi Yi Ren, Che Qian Zi, Mu Tong', [16], 'exact'],
    [13, 5, 'Herbs that Dispel Wind-Dampness', 'Hierbas que expulsan Viento-Humedad', 'Du Huo, Wei Ling Xian, Qin Jiao, Fang Ji, Mu Gua, Sang Ji Sheng', [14, 16], 'direct'],
    [14, 6, 'Herbs that Cool and Transform Phlegm-Heat', 'Hierbas que enfrían y transforman Flema-Calor', 'Chuan Bei Mu, Zhe Bei Mu, Gua Lou, Zhu Ru, Tian Zhu Huang', [17, 4], 'direct'],
    [15, 6, 'Herbs that Warm and Transform Cold-Phlegm', 'Hierbas que calientan y transforman Flema-Frío', 'Ban Xia, Tian Nan Xing, Bai Jie Zi, Bai Fu Zi, Zao Jia', [17], 'direct'],
    [16, 6, 'Herbs that Stop Coughing and Calm Wheezing', 'Hierbas que detienen la tos y calman las sibilancias', 'Xing Ren, Zi Su Zi, Bai Bu, Zi Wan, Kuan Dong Hua, Sang Bai Pi', [17, 12], 'partial'],
    [17, 7, 'Aromatic Herbs that Transform Dampness', 'Hierbas aromáticas que transforman Humedad', 'Huo Xiang, Pei Lan, Cang Zhu, Hou Po, Sha Ren, Bai Dou Kou', [16], 'direct'],
    [18, 8, 'Herbs that Relieve Food Stagnation', 'Hierbas que alivian el estancamiento de alimentos', 'Shan Zha, Shen Qu, Mai Ya, Lai Fu Zi, Ji Nei Jin', [18], 'exact'],
    [19, 9, 'Herbs that Regulate the Qi', 'Hierbas que regulan el Qi', 'Chen Pi, Qing Pi, Zhi Shi, Mu Xiang, Xiang Fu, Chuan Lian Zi', [12, 3], 'exact'],
    [20, 10, 'Herbs that Invigorate the Blood', 'Hierbas que vigorizan la Sangre', 'Chuan Xiong, Dan Shen, Tao Ren, Hong Hua, Yi Mu Cao, Niu Xi', [13], 'direct'],
    [21, 10, 'Herbs that Stop Bleeding', 'Hierbas que detienen el sangrado', 'San Qi, Bai Ji, Pu Huang, Xian He Cao, Ai Ye', [13], 'direct'],
    [22, 11, 'Herbs that Warm the Interior and Expel Cold', 'Hierbas que calientan el Interior y expulsan Frío', 'Fu Zi, Gan Jiang, Rou Gui, Wu Zhu Yu, Gao Liang Jiang, Ding Xiang', [6, 7], 'exact'],
    [23, 12, 'Herbs that Tonify Qi', 'Hierbas que tonifican Qi', 'Ren Shen, Huang Qi, Bai Zhu, Shan Yao, Gan Cao', [8, 9], 'exact'],
    [24, 12, 'Herbs that Tonify Blood', 'Hierbas que tonifican Sangre', 'Dang Gui, Shu Di Huang, Bai Shao, He Shou Wu, E Jiao', [8, 13], 'exact'],
    [25, 12, 'Herbs that Tonify Yang', 'Hierbas que tonifican Yang', 'Lu Rong, Yin Yang Huo, Ba Ji Tian, Du Zhong, Xu Duan, Tu Si Zi', [8, 6], 'exact'],
    [26, 12, 'Herbs that Tonify Yin', 'Hierbas que tonifican Yin', 'Bei Sha Shen, Mai Men Dong, Tian Men Dong, Shi Hu, Yu Zhu, Gou Qi Zi', [8, 15], 'exact'],
    [27, 13, 'Herbs that Stabilize and Bind', 'Hierbas que estabilizan y contienen', 'Wu Wei Zi, Wu Mei, He Zi, Rou Dou Kou, Fu Pen Zi, Jin Ying Zi', [9], 'exact'],
    [28, 14, 'Substances that Anchor, Settle, and Calm the Spirit', 'Sustancias que anclan, asientan y calman el Espíritu', 'Long Gu, Mu Li, Ci Shi, Zhen Zhu Mu, Hu Po', [10], 'direct'],
    [29, 14, 'Herbs that Nourish the Heart and Calm the Spirit', 'Hierbas que nutren el Corazón y calman el Espíritu', 'Suan Zao Ren, Bai Zi Ren, Yuan Zhi, He Huan Pi, Ye Jiao Teng', [10], 'direct'],
    [30, 15, 'Aromatic Substances that Open the Orifices', 'Sustancias aromáticas que abren los orificios', 'Shi Chang Pu, Bing Pian, Su He Xiang, She Xiang', [11], 'exact'],
    [31, 16, 'Substances that Extinguish Wind and Stop Tremors', 'Sustancias que extinguen Viento y detienen temblores', 'Gou Teng, Tian Ma, Di Long, Jiang Can, Quan Xie', [14], 'direct'],
    [32, 17, 'Herbs that Expel Parasites', 'Hierbas que expulsan parásitos', 'Bing Lang, Shi Jun Zi, Ku Lian Pi, Lei Wan', [19], 'exact'],
    [33, 18, 'Substances for Topical Application', 'Sustancias para aplicación externa', 'Liu Huang, She Chuang Zi, Ming Fan, Lu Feng Fang', [21, 20], 'direct'],
    [34, 19, 'Obsolete Substances', 'Sustancias obsoletas', 'Zhu Sha, Xiong Huang, Qing Fen, Mi Tuo Seng', [], 'none'],
];

$covered = [];
foreach ($herbcategories as $category) {
    foreach ($category[5] as $chapter) {
        $covered[$chapter] = true;
    }
}
ksort($covered);
if (count($herbcategories) !== 34 || array_keys($formulachapters) !== range(1, 21)
        || array_keys($covered) !== range(1, 21)) {
    throw new moodle_exception('The herb-formula Bensky category map is incomplete.');
}

$relationshiplabels = [
    'exact' => ['Exact or nearly exact category-name match', 'Coincidencia exacta o casi exacta del nombre'],
    'direct' => ['Direct therapeutic relationship; scope still differs', 'Relación terapéutica directa; el alcance sigue siendo diferente'],
    'partial' => ['Partial mechanism relationship, not a category equivalent', 'Relación parcial de mecanismo; no son categorías equivalentes'],
    'none' => ['No direct formula category; historical safety reference only', 'Sin categoría directa de fórmulas; solo referencia histórica de seguridad'],
];

// Card-specific explanation: herb action on the left, complete formula strategy on the right.
$relationshipnotes = [
    1 => ['These herbs disperse Wind-Cold from the surface. The formulas combine that action with support for the complete Exterior or Exterior-Interior pattern.', 'Estas hierbas dispersan Viento-Frío de la superficie. Las fórmulas combinan esa acción con apoyo para el patrón completo de Exterior o Exterior-Interior.'],
    2 => ['These herbs disperse Wind-Heat from the surface. The formulas add Heat clearing, throat relief, or fluid protection according to the complete pattern.', 'Estas hierbas dispersan Viento-Calor de la superficie. Las fórmulas añaden aclarar Calor, aliviar la garganta o proteger líquidos según el patrón completo.'],
    3 => ['These herbs clear intense Qi-level Heat or drain Fire. Clear-Heat formulas make that action part of a complete pattern-based prescription.', 'Estas hierbas aclaran Calor intenso del nivel Qi o drenan Fuego. Las fórmulas de Clear Heat integran esa acción en una prescripción completa basada en el patrón.'],
    4 => ['These herbs cool Heat in the nutritive or Blood level. The formulas add Yin protection, blood movement, or bleeding control as required.', 'Estas hierbas enfrían el Calor en los niveles Nutritivo o Sangre. Las fórmulas añaden protección del Yin, movimiento de Sangre o control del sangrado según se requiera.'],
    5 => ['These bitter, cold herbs clear Damp-Heat. They support Clear-Heat formulas when Heat dominates and Dampness formulas when fluid obstruction dominates.', 'Estas hierbas amargas y frías aclaran Humedad-Calor. Apoyan fórmulas de Clear Heat cuando domina el Calor y fórmulas de Humedad cuando domina la obstrucción de líquidos.'],
    6 => ['These herbs resolve Heat toxicity. Exterior formulas use them when Wind-Heat and toxicity begin at the surface; Clear-Heat formulas treat the systemic pattern; abscess-and-sore formulas focus on swelling, pus, and localized toxicity.', 'Estas hierbas resuelven toxicidad por Calor. Las fórmulas de Exterior las usan cuando Viento-Calor y toxicidad comienzan en la superficie; las de Clear Heat tratan el patrón sistémico; las de abscesos y llagas enfocan hinchazón, pus y toxicidad localizada.'],
    7 => ['These herbs clear lingering Heat from Yin deficiency. The formulas combine them with Yin- or Blood-nourishing support to treat both Heat and its deficient root.', 'Estas hierbas aclaran Calor persistente por deficiencia de Yin. Las fórmulas las combinan con apoyo para nutrir Yin o Sangre y tratar el Calor y su raíz deficiente.'],
    8 => ['These herbs provide the defining Summerheat-clearing action. The formulas add fluid protection, Qi support, or Dampness drainage.', 'Estas hierbas aportan la acción definitoria de aclarar Calor de Verano. Las fórmulas añaden protección de líquidos, apoyo del Qi o drenaje de Humedad.'],
    9 => ['These purgatives drive accumulation downward. The formulas control their strength and add Qi-moving, Heat-clearing, or protective ingredients.', 'Estos purgantes conducen la acumulación hacia abajo. Las fórmulas controlan su potencia y añaden ingredientes que mueven Qi, aclaran Calor o protegen.'],
    10 => ['These herbs lubricate the Intestines. Purging formulas relieve dry constipation; Dryness formulas may also nourish the fluids or Blood causing the dryness.', 'Estas hierbas lubrican los Intestinos. Las fórmulas purgantes alivian estreñimiento seco; las de Sequedad también pueden nutrir los líquidos o la Sangre que originan la sequedad.'],
    11 => ['These strong herbs expel severe water accumulation. Downward-draining formulas moderate and direct that action with protective ingredients.', 'Estas hierbas fuertes expulsan acumulaciones severas de agua. Las fórmulas que drenan hacia abajo moderan y dirigen esa acción con ingredientes protectores.'],
    12 => ['These herbs promote urination and leach Dampness. Dampness formulas combine them while also treating the related Spleen, Heat, Cold, or edema pattern.', 'Estas hierbas promueven la micción y filtran Humedad. Las fórmulas para Humedad las combinan mientras tratan además el patrón relacionado de Bazo, Calor, Frío o edema.'],
    13 => ['These herbs dispel Wind-Dampness from painful channels. The related formulas emphasize either Dampness drainage or Wind and obstruction according to the pattern.', 'Estas hierbas expulsan Viento-Humedad de canales dolorosos. Las fórmulas relacionadas enfatizan drenaje de Humedad o Viento y obstrucción según el patrón.'],
    14 => ['These herbs cool and transform Phlegm-Heat. Phlegm formulas organize the transforming action; Clear-Heat formulas strengthen the Heat-clearing side.', 'Estas hierbas enfrían y transforman Flema-Calor. Las fórmulas para Flema organizan la transformación; las de Clear Heat refuerzan el aspecto de aclarar Calor.'],
    15 => ['These herbs warm and transform Cold-Phlegm. Complete Phlegm formulas add Qi regulation, Dampness transformation, or cough relief.', 'Estas hierbas calientan y transforman Flema-Frío. Las fórmulas completas para Flema añaden regulación de Qi, transformación de Humedad o alivio de tos.'],
    16 => ['These herbs relieve cough and direct Lung Qi downward. They support Phlegm formulas when mucus dominates and Qi formulas when rebellious Lung Qi dominates.', 'Estas hierbas alivian la tos y descienden el Qi de Pulmón. Apoyan fórmulas para Flema cuando domina el moco y fórmulas de Qi cuando domina el Qi rebelde de Pulmón.'],
    17 => ['These aromatic herbs awaken the Spleen and transform turbid Dampness. The formulas add Qi regulators, diuretics, or warming support.', 'Estas hierbas aromáticas despiertan el Bazo y transforman Humedad turbia. Las fórmulas añaden reguladores de Qi, diuréticos o apoyo calentador.'],
    18 => ['These herbs digest retained food. Food-stagnation formulas combine several digestive actions and add Qi regulation or Spleen support.', 'Estas hierbas digieren alimentos retenidos. Las fórmulas para estancamiento combinan varias acciones digestivas y añaden regulación de Qi o apoyo del Bazo.'],
    19 => ['These herbs move constrained Qi or direct rebellious Qi downward. Qi formulas select them by organ and direction; harmonizing formulas use them when constraint disrupts another system.', 'Estas hierbas mueven Qi estancado o descienden Qi rebelde. Las fórmulas de Qi las seleccionan por órgano y dirección; las armonizantes las usan cuando el estancamiento altera otro sistema.'],
    20 => ['These herbs move Blood stasis. Blood formulas combine them with Qi movers, Blood tonics, warming herbs, or Heat clearers according to the cause.', 'Estas hierbas mueven estasis de Sangre. Las fórmulas de Sangre las combinan con movilizadores de Qi, tónicos de Sangre, hierbas tibias o aclaradores de Calor según la causa.'],
    21 => ['These herbs stop bleeding through different mechanisms. Blood formulas combine the appropriate hemostatics with cooling, warming, tonifying, or stasis-resolving support.', 'Estas hierbas detienen sangrado mediante distintos mecanismos. Las fórmulas de Sangre combinan los hemostáticos apropiados con apoyo refrescante, calentador, tónico o para resolver estasis.'],
    22 => ['These herbs warm the Interior and dispel Cold. Warming formulas balance that heat with Qi, Blood, or fluid support; Chapter 7 applies it to combined Exterior-Interior disorders.', 'Estas hierbas calientan el Interior y dispersan Frío. Las fórmulas calentadoras equilibran ese calor con apoyo de Qi, Sangre o líquidos; el capítulo 7 lo aplica a trastornos Exterior-Interior combinados.'],
    23 => ['These herbs tonify deficient Qi. Tonifying formulas rebuild function; stabilizing formulas may add Qi support to prevent further leakage.', 'Estas hierbas tonifican Qi deficiente. Las fórmulas tónicas reconstruyen función; las estabilizadoras pueden añadir apoyo de Qi para evitar pérdidas adicionales.'],
    24 => ['These herbs replenish Blood. Tonifying formulas rebuild deficiency; Blood-regulating formulas add them when moving or stopping Blood must not cause further injury.', 'Estas hierbas reponen Sangre. Las fórmulas tónicas reconstruyen deficiencia; las que regulan Sangre las añaden cuando moverla o detenerla no debe causar más lesión.'],
    25 => ['These herbs tonify chronic Yang deficiency. Tonifying formulas rebuild the root; warming formulas use hotter actions to rescue Yang or disperse active Cold.', 'Estas hierbas tonifican deficiencia crónica de Yang. Las fórmulas tónicas reconstruyen la raíz; las calentadoras usan acciones más calientes para rescatar Yang o dispersar Frío activo.'],
    26 => ['These herbs replenish Yin, fluids, and essence. Tonifying formulas treat the root deficiency; Dryness formulas emphasize moistening the affected organ or tissue.', 'Estas hierbas reponen Yin, líquidos y esencia. Las fórmulas tónicas tratan la deficiencia de raíz; las de Sequedad enfatizan humedecer el órgano o tejido afectado.'],
    27 => ['These herbs prevent abnormal leakage. Stabilizing formulas pair them with tonics or corrective herbs so the branch is contained while the root is treated.', 'Estas hierbas evitan pérdidas anormales. Las fórmulas estabilizadoras las combinan con tónicos o hierbas correctivas para contener la rama mientras se trata la raíz.'],
    28 => ['These heavy substances anchor agitation and settle the Spirit. The formulas add treatment for the underlying Heat, Phlegm, deficiency, or constraint.', 'Estas sustancias pesadas anclan la agitación y asientan el Espíritu. Las fórmulas añaden tratamiento para Calor, Flema, deficiencia o estancamiento subyacente.'],
    29 => ['These herbs nourish Heart Blood or Yin while calming the Spirit. The formulas add tonics, Heat clearers, or Qi regulators according to the cause.', 'Estas hierbas nutren Sangre o Yin de Corazón mientras calman el Espíritu. Las fórmulas añaden tónicos, aclaradores de Calor o reguladores de Qi según la causa.'],
    30 => ['These substances open closed sensory orifices and restore consciousness. The formulas add cooling or warming ingredients according to a hot or cold closure.', 'Estas sustancias abren orificios sensoriales cerrados y restauran la conciencia. Las fórmulas añaden ingredientes refrescantes o calentadores según un cierre caliente o frío.'],
    31 => ['These substances extinguish internal Wind and control tremor or spasm. Wind formulas add Liver-calming, Heat-clearing, Phlegm-transforming, or Blood-nourishing support.', 'Estas sustancias extinguen Viento interno y controlan temblor o espasmo. Las fórmulas para Viento añaden apoyo para calmar Hígado, aclarar Calor, transformar Flema o nutrir Sangre.'],
    32 => ['These herbs attack or expel parasites. Parasite formulas add digestive, purgative, warming, or strengthening herbs to treat the organism and the patient pattern.', 'Estas hierbas atacan o expulsan parásitos. Las fórmulas añaden hierbas digestivas, purgantes, tibias o fortalecedoras para tratar al organismo y el patrón del paciente.'],
    33 => ['These substances act directly on skin or local sores. External formulas determine the vehicle and combination; abscess formulas add systemic treatment when the disorder is deeper.', 'Estas sustancias actúan directamente sobre piel o llagas locales. Las fórmulas externas determinan vehículo y combinación; las de abscesos añaden tratamiento sistémico cuando el trastorno es más profundo.'],
    34 => ['These substances remain for historical recognition and safety. Their toxicity or obsolescence means there is no modern therapeutic formula-category match.', 'Estas sustancias permanecen para reconocimiento histórico y seguridad. Su toxicidad u obsolescencia significa que no existe una correspondencia terapéutica moderna con fórmulas.'],
];

// Concrete formula-composition examples showing herbs from the category inside related formulas.
$compositionexamples = [
    1 => ['Ma Huang Tang → Ma Huang + Gui Zhi; Gui Zhi Tang → Gui Zhi', 'Ma Huang Tang → Ma Huang + Gui Zhi; Gui Zhi Tang → Gui Zhi'],
    2 => ['Yin Qiao San → Bo He + Niu Bang Zi; Sang Ju Yin → Sang Ye + Ju Hua', 'Yin Qiao San → Bo He + Niu Bang Zi; Sang Ju Yin → Sang Ye + Ju Hua'],
    3 => ['Bai Hu Tang → Shi Gao + Zhi Mu; Huang Lian Jie Du Tang → Zhi Zi', 'Bai Hu Tang → Shi Gao + Zhi Mu; Huang Lian Jie Du Tang → Zhi Zi'],
    4 => ['Qing Ying Tang → Sheng Di Huang + Xuan Shen', 'Qing Ying Tang → Sheng Di Huang + Xuan Shen'],
    5 => ['Huang Lian Jie Du Tang → Huang Lian + Huang Qin + Huang Bai', 'Huang Lian Jie Du Tang → Huang Lian + Huang Qin + Huang Bai'],
    6 => ['Yin Qiao San → Jin Yin Hua + Lian Qiao; Wu Wei Xiao Du Yin → Jin Yin Hua + Pu Gong Ying; Xian Fang Huo Ming Yin → Jin Yin Hua', 'Yin Qiao San → Jin Yin Hua + Lian Qiao; Wu Wei Xiao Du Yin → Jin Yin Hua + Pu Gong Ying; Xian Fang Huo Ming Yin → Jin Yin Hua'],
    7 => ['Qing Hao Bie Jia Tang → Qing Hao + Di Gu Pi', 'Qing Hao Bie Jia Tang → Qing Hao + Di Gu Pi'],
    8 => ['Xiang Ru San → Xiang Ru; Qing Luo Yin → He Ye + Xi Gua Cui Yi', 'Xiang Ru San → Xiang Ru; Qing Luo Yin → He Ye + Xi Gua Cui Yi'],
    9 => ['Da Cheng Qi Tang → Da Huang + Mang Xiao', 'Da Cheng Qi Tang → Da Huang + Mang Xiao'],
    10 => ['Run Chang Wan → Huo Ma Ren + Yu Li Ren', 'Run Chang Wan → Huo Ma Ren + Yu Li Ren'],
    11 => ['Shi Zao Tang → Gan Sui + Da Ji + Yuan Hua', 'Shi Zao Tang → Gan Sui + Da Ji + Yuan Hua'],
    12 => ['Wu Ling San → Fu Ling + Zhu Ling + Ze Xie', 'Wu Ling San → Fu Ling + Zhu Ling + Ze Xie'],
    13 => ['Du Huo Ji Sheng Tang → Du Huo + Qin Jiao + Fang Ji + Sang Ji Sheng', 'Du Huo Ji Sheng Tang → Du Huo + Qin Jiao + Fang Ji + Sang Ji Sheng'],
    14 => ['Bei Mu Gua Lou San → Chuan Bei Mu + Gua Lou; Wen Dan Tang → Zhu Ru', 'Bei Mu Gua Lou San → Chuan Bei Mu + Gua Lou; Wen Dan Tang → Zhu Ru'],
    15 => ['Er Chen Tang → Ban Xia; Wen Dan Tang → Ban Xia', 'Er Chen Tang → Ban Xia; Wen Dan Tang → Ban Xia'],
    16 => ['Ding Chuan Tang → Xing Ren + Zi Su Zi + Sang Bai Pi', 'Ding Chuan Tang → Xing Ren + Zi Su Zi + Sang Bai Pi'],
    17 => ['Huo Xiang Zheng Qi San → Huo Xiang + Hou Po; Ping Wei San → Cang Zhu + Hou Po', 'Huo Xiang Zheng Qi San → Huo Xiang + Hou Po; Ping Wei San → Cang Zhu + Hou Po'],
    18 => ['Bao He Wan → Shan Zha + Shen Qu + Lai Fu Zi; Jian Pi Wan → Shan Zha + Shen Qu + Mai Ya', 'Bao He Wan → Shan Zha + Shen Qu + Lai Fu Zi; Jian Pi Wan → Shan Zha + Shen Qu + Mai Ya'],
    19 => ['Yue Ju Wan → Xiang Fu; Ju Pi Zhu Ru Tang → Chen Pi', 'Yue Ju Wan → Xiang Fu; Ju Pi Zhu Ru Tang → Chen Pi'],
    20 => ['Xue Fu Zhu Yu Tang → Chuan Xiong + Tao Ren + Hong Hua + Niu Xi; Gui Zhi Fu Ling Wan → Tao Ren', 'Xue Fu Zhu Yu Tang → Chuan Xiong + Tao Ren + Hong Hua + Niu Xi; Gui Zhi Fu Ling Wan → Tao Ren'],
    21 => ['Shi Xiao San → Pu Huang', 'Shi Xiao San → Pu Huang'],
    22 => ['Li Zhong Wan → Gan Jiang; Si Ni Tang → Fu Zi + Gan Jiang', 'Li Zhong Wan → Gan Jiang; Si Ni Tang → Fu Zi + Gan Jiang'],
    23 => ['Si Jun Zi Tang → Ren Shen + Bai Zhu + Gan Cao; Yu Ping Feng San → Huang Qi + Bai Zhu', 'Si Jun Zi Tang → Ren Shen + Bai Zhu + Gan Cao; Yu Ping Feng San → Huang Qi + Bai Zhu'],
    24 => ['Si Wu Tang → Dang Gui + Shu Di Huang + Bai Shao; Ba Zhen Tang → the same Blood-tonifying core', 'Si Wu Tang → Dang Gui + Shu Di Huang + Bai Shao; Ba Zhen Tang → el mismo núcleo que tonifica Sangre'],
    25 => ['You Gui Wan → Du Zhong + Tu Si Zi; Er Xian Tang → Yin Yang Huo + Ba Ji Tian', 'You Gui Wan → Du Zhong + Tu Si Zi; Er Xian Tang → Yin Yang Huo + Ba Ji Tian'],
    26 => ['Yi Guan Jian → Bei Sha Shen + Mai Men Dong + Gou Qi Zi; Zeng Ye Tang → Mai Men Dong', 'Yi Guan Jian → Bei Sha Shen + Mai Men Dong + Gou Qi Zi; Zeng Ye Tang → Mai Men Dong'],
    27 => ['Si Shen Wan → Wu Wei Zi + Rou Dou Kou', 'Si Shen Wan → Wu Wei Zi + Rou Dou Kou'],
    28 => ['Ci Zhu Wan → Ci Shi', 'Ci Zhu Wan → Ci Shi'],
    29 => ['Suan Zao Ren Tang → Suan Zao Ren; Tian Wang Bu Xin Dan → Suan Zao Ren + Bai Zi Ren + Yuan Zhi', 'Suan Zao Ren Tang → Suan Zao Ren; Tian Wang Bu Xin Dan → Suan Zao Ren + Bai Zi Ren + Yuan Zhi'],
    30 => ['An Gong Niu Huang Wan → Bing Pian + She Xiang; Su He Xiang Wan → Su He Xiang + Bing Pian', 'An Gong Niu Huang Wan → Bing Pian + She Xiang; Su He Xiang Wan → Su He Xiang + Bing Pian'],
    31 => ['Tian Ma Gou Teng Yin → Tian Ma + Gou Teng', 'Tian Ma Gou Teng Yin → Tian Ma + Gou Teng'],
    32 => ['Hua Chong Wan → Lei Wan + Bing Lang', 'Hua Chong Wan → Lei Wan + Bing Lang'],
    33 => ['Ku Shen Tang → Ku Shen + She Chuang Zi', 'Ku Shen Tang → Ku Shen + She Chuang Zi'],
    34 => ['No modern formula example: these obsolete substances are shown for historical safety recognition only.', 'Sin ejemplo de fórmula moderna: estas sustancias obsoletas aparecen solo para reconocimiento histórico y seguridad.'],
];

// Selected official Appendix B pairs. Only categories with a clear teaching bridge receive Duì Yào.
$duiyaopairs = [
    1 => ['ma-huang-gui-zhi-dui-yao', 'Má Huáng + Guì Zhī', '麻黄 + 桂枝', 'Má Huáng Tāng'],
    2 => ['ma-huang-xing-ren-dui-yao', 'Má Huáng + Xìng Rén', '麻黄 + 杏仁', 'Má Huáng Tāng; Má Xìng Shí Gān Tāng'],
    5 => ['jing-jie-fang-feng-dui-yao', 'Jīng Jiè + Fáng Fēng', '荆芥 + 防风', 'Jīng Fáng Bài Dú Sǎn; Xiāo Fēng Sǎn'],
    7 => ['sang-ye-ju-hua-dui-yao', 'Sāng Yè + Jú Huā', '桑叶 + 菊花', 'Sāng Jú Yǐn'],
    11 => ['shi-gao-zhi-mu-dui-yao', 'Shí Gāo + Zhī Mǔ', '石膏 + 知母', 'Bái Hǔ Tāng'],
    12 => ['zhi-mu-huang-bai-dui-yao', 'Zhī Mǔ + Huáng Bǎi', '知母 + 黄柏', 'Zhī Bǎi Dì Huáng Wán; Dà Bǔ Yīn Wán'],
    15 => ['jin-yin-hua-lian-qiao-dui-yao', 'Jīn Yín Huā + Lián Qiào', '金银花 + 连翘', 'Yín Qiáo Sǎn'],
    16 => ['niu-bang-zi-lian-qiao-dui-yao', 'Niú Bàng Zǐ + Lián Qiào', '牛蒡子 + 连翘', 'Yín Qiáo Sǎn; Pǔ Jì Xiāo Dú Yǐn'],
    17 => ['qing-hao-bie-jia-dui-yao', 'Qīng Hāo + Biē Jiǎ', '青蒿 + 鳖甲', 'Qīng Hāo Biē Jiǎ Tāng'],
    18 => ['ren-shen-huang-qi-dui-yao', 'Rén Shēn + Huáng Qí', '人参 + 黄芪', 'Bǔ Zhōng Yì Qì Tāng; Guī Pí Tāng'],
    19 => ['bai-zhu-fu-ling-dui-yao', 'Bái Zhú + Fú Líng', '白术 + 茯苓', 'Sì Jūn Zǐ Tāng; Wǔ Líng Sǎn'],
    24 => ['dang-gui-chuan-xiong-dui-yao', 'Dāng Guī + Chuān Xiōng', '当归 + 川芎', 'Sì Wù Tāng'],
    25 => ['dang-gui-xiang-fu-dui-yao', 'Dāng Guī + Xiāng Fù', '当归 + 香附', 'Xiāng Fù Sì Wù Tāng'],
    26 => ['ai-ye-e-jiao-dui-yao', 'Ài Yè + Ē Jiāo', '艾叶 + 阿胶', 'Jiāo Ài Tāng'],
    27 => ['mai-men-dong-sha-shen-dui-yao', 'Mài Mén Dōng + Shā Shēn', '麦门冬 + 沙参', 'Shā Shēn Mài Mén Dōng Tāng; Yī Guàn Jiān'],
    29 => ['han-lian-cao-nu-zhen-zi-dui-yao', 'Hàn Lián Cǎo + Nǚ Zhēn Zǐ', '旱莲草 + 女贞子', 'Èr Zhì Wán'],
    31 => ['du-zhong-xu-duan-dui-yao', 'Dù Zhòng + Xù Duàn', '杜仲 + 续断', 'Dú Huó Jì Shēng Tāng'],
    32 => ['dang-gui-rou-cong-rong-dui-yao', 'Dāng Guī + Ròu Cōng Róng', '当归 + 肉苁蓉', 'Jì Chuān Jiān'],
    33 => ['di-yu-huai-mi-dui-yao', 'Dì Yú + Huái Mǐ', '地榆 + 槐米', 'Huái Huā Sǎn'],
    34 => ['tao-ren-hong-hua-dui-yao', 'Táo Rén + Hóng Huā', '桃仁 + 红花', 'Xuè Fǔ Zhú Yū Tāng'],
    35 => ['ru-xiang-mo-yao-dui-yao', 'Rǔ Xiāng + Mò Yào', '乳香 + 没药', 'Xiǎo Huó Luò Dān'],
    37 => ['ban-xia-chen-pi-dui-yao', 'Bàn Xià + Chén Pí', '半夏 + 陈皮', 'Èr Chén Tāng; Bǎo Hé Wán'],
    38 => ['ban-xia-sheng-jiang-dui-yao', 'Bàn Xià + Shēng Jiāng', '半夏 + 生姜', 'Xiǎo Bàn Xià Tāng; Xiǎo Chái Hú Tāng'],
    39 => ['gua-lou-xie-bai-dui-yao', 'Guā Lóu + Xiè Bái', '瓜蒌 + 薤白', 'Guā Lóu Xiè Bái Bàn Xià Tāng'],
    40 => ['zi-wan-kuan-dong-hua-dui-yao', 'Zǐ Wǎn + Kuǎn Dōng Huā', '紫菀 + 款冬花', 'Shè Gān Má Huáng Tāng'],
    42 => ['fu-zi-gan-jiang-dui-yao', 'Fù Zǐ + Gān Jiāng', '附子 + 干姜', 'Sì Nì Tāng'],
    43 => ['fu-zi-rou-gui-dui-yao', 'Fù Zǐ + Ròu Guì', '附子 + 肉桂', 'Yòu Guī Wán'],
    46 => ['chai-hu-bai-shao-dui-yao', 'Chái Hú + Bái Sháo', '柴胡 + 白芍', 'Sì Nì Sǎn; Xiāo Yáo Sǎn'],
    47 => ['huo-xiang-pei-lan-dui-yao', 'Huò Xiāng + Pèi Lán', '藿香 + 佩兰', 'Huò Xiāng Zhèng Qì Sǎn (modified)'],
    48 => ['jin-qian-cao-hai-jin-sha-dui-yao', 'Jīn Qián Cǎo + Hǎi Jīn Shā', '金钱草 + 海金沙', 'Sān Jīn Tāng'],
    49 => ['da-huang-mang-xiao-dui-yao', 'Dà Huáng + Máng Xiāo', '大黄 + 芒硝', 'Dà Chéng Qì Tāng'],
    50 => ['long-gu-mu-li-dui-yao', 'Lóng Gǔ + Mǔ Lì', '龙骨 + 牡蛎', 'Guì Zhī Jiā Lóng Gǔ Mǔ Lì Tāng'],
    51 => ['tian-ma-gou-teng-dui-yao', 'Tiān Má + Gōu Téng', '天麻 + 钩藤', 'Tiān Má Gōu Téng Yǐn'],
];

$duiyaocategories = [
    1 => ['Exterior-Resolving · Pungent-Warm', 'Liberar Exterior · Acre-Tibio'],
    2 => ['Exterior-Resolving · Regulate Lung Qi', 'Liberar Exterior · Regular Qi de Pulmón'],
    5 => ['Dispel Wind from the Exterior', 'Expulsar Viento del Exterior'],
    7 => ['Disperse Wind-Heat · Brighten the Eyes', 'Dispersar Viento-Calor · Aclarar Ojos'],
    11 => ['Clear Qi-Level Heat', 'Aclarar Calor de Nivel Qi'],
    12 => ['Drain Deficiency Fire', 'Drenar Fuego por Deficiencia'],
    15 => ['Clear Heat and Resolve Toxicity', 'Aclarar Calor y Resolver Toxicidad'],
    16 => ['Benefit the Throat · Reduce Swelling', 'Beneficiar Garganta · Reducir Hinchazón'],
    17 => ['Vent Heat Lurking in Yin', 'Ventilar Calor Oculto en Yin'],
    18 => ['Strongly Supplement Qi', 'Suplementar Qi con Fuerza'],
    19 => ['Strengthen the Spleen · Drain Dampness', 'Fortalecer Bazo · Drenar Humedad'],
    24 => ['Nourish and Invigorate Blood', 'Nutrir y Movilizar Sangre'],
    25 => ['Regulate Qi, Blood and Menses', 'Regular Qi, Sangre y Menstruación'],
    26 => ['Warm the Womb · Stop Bleeding', 'Calentar Útero · Detener Sangrado'],
    27 => ['Nourish Lung–Stomach Yin', 'Nutrir Yin de Pulmón–Estómago'],
    29 => ['Gently Enrich Liver–Kidney Yin', 'Enriquecer Suavemente Yin Hígado–Riñón'],
    31 => ['Strengthen the Back · Calm the Fetus', 'Fortalecer Espalda · Calmar Feto'],
    32 => ['Warm and Moisten the Intestines', 'Calentar y Humedecer Intestinos'],
    33 => ['Cool Blood · Stop Lower Bleeding', 'Enfriar Sangre · Detener Sangrado Inferior'],
    34 => ['Invigorate Blood · Dispel Stasis', 'Movilizar Sangre · Disipar Estasis'],
    35 => ['Move Qi and Blood · Stop Pain', 'Mover Qi y Sangre · Detener Dolor'],
    37 => ['Dry Dampness · Transform Phlegm', 'Secar Humedad · Transformar Flema'],
    38 => ['Stop Vomiting · Transform Phlegm', 'Detener Vómito · Transformar Flema'],
    39 => ['Unblock Chest Yang · Dissipate Binding', 'Desbloquear Yang Torácico · Disipar Opresión'],
    40 => ['Descend Lung Qi · Stop Cough', 'Descender Qi de Pulmón · Detener Tos'],
    42 => ['Restore Devastated Yang', 'Restaurar Yang Devastado'],
    43 => ['Warm and Supplement Kidney Yang', 'Calentar y Suplementar Yang de Riñón'],
    46 => ['Course the Liver · Nourish Blood', 'Mover Hígado · Nutrir Sangre'],
    47 => ['Aromatically Transform Dampness', 'Transformar Humedad Aromáticamente'],
    48 => ['Unblock Lin · Expel Stones', 'Desbloquear Lin · Expulsar Cálculos'],
    49 => ['Purge Heat · Soften Hardness', 'Purgar Calor · Ablandar Dureza'],
    50 => ['Anchor Yang · Calm Spirit · Secure Leakage', 'Anclar Yang · Calmar Shen · Asegurar Pérdidas'],
    51 => ['Calm the Liver · Extinguish Wind', 'Calmar Hígado · Extinguir Viento'],
];

$categoryduiyao = [
    1 => [1, 5], 2 => [7, 16], 3 => [11], 6 => [15], 7 => [12, 17],
    9 => [49], 10 => [32], 12 => [19, 48], 13 => [31], 14 => [39],
    15 => [37, 38], 16 => [2, 40], 17 => [47], 18 => [37], 19 => [25, 46],
    20 => [34, 35], 21 => [26, 33], 22 => [42], 23 => [18, 19], 24 => [24],
    25 => [31, 43], 26 => [27, 29], 27 => [50], 28 => [50], 31 => [51],
];

// Individual Bensky herb-category card for every herb used in the selected Duì Yào.
$duiyaoherbcategories = [
    'Ma Huang' => 1, 'Gui Zhi' => 1, 'Xing Ren' => 16, 'Jing Jie' => 1, 'Fang Feng' => 1,
    'Sang Ye' => 2, 'Ju Hua' => 2, 'Shi Gao' => 3, 'Zhi Mu' => 3, 'Huang Bai' => 5,
    'Jin Yin Hua' => 6, 'Lian Qiao' => 6, 'Niu Bang Zi' => 2, 'Qing Hao' => 7, 'Bie Jia' => 26,
    'Ren Shen' => 23, 'Huang Qi' => 23, 'Bai Zhu' => 23, 'Fu Ling' => 12,
    'Dang Gui' => 24, 'Chuan Xiong' => 20, 'Xiang Fu' => 19, 'Ai Ye' => 21, 'E Jiao' => 24,
    'Mai Men Dong' => 26, 'Sha Shen' => 26, 'Han Lian Cao' => 26, 'Nu Zhen Zi' => 26,
    'Du Zhong' => 25, 'Xu Duan' => 25, 'Rou Cong Rong' => 25, 'Di Yu' => 21, 'Huai Mi' => 21,
    'Tao Ren' => 20, 'Hong Hua' => 20, 'Ru Xiang' => 20, 'Mo Yao' => 20,
    'Ban Xia' => 15, 'Chen Pi' => 19, 'Sheng Jiang' => 1, 'Gua Lou' => 14, 'Xie Bai' => 19,
    'Zi Wan' => 16, 'Kuan Dong Hua' => 16, 'Fu Zi' => 22, 'Gan Jiang' => 22, 'Rou Gui' => 22,
    'Chai Hu' => 2, 'Bai Shao' => 24, 'Huo Xiang' => 17, 'Pei Lan' => 17,
    'Jin Qian Cao' => 12, 'Hai Jin Sha' => 12, 'Da Huang' => 9, 'Mang Xiao' => 9,
    'Long Gu' => 28, 'Mu Li' => 28, 'Tian Ma' => 31, 'Gou Teng' => 31,
];

if (array_keys($relationshipnotes) !== range(1, 34)
        || array_keys($compositionexamples) !== range(1, 34)) {
    throw new moodle_exception('Every herb category must include a relationship explanation and composition example.');
}
foreach ($categoryduiyao as $cardnumber => $pairnumbers) {
    if ($cardnumber < 1 || $cardnumber > 34 || array_diff($pairnumbers, array_keys($duiyaopairs)) !== []) {
        throw new moodle_exception('The representative Dui Yao category map is invalid.');
    }
}
if (array_keys($duiyaocategories) !== array_keys($duiyaopairs)) {
    throw new moodle_exception('Every representative Dui Yao must include its official pair category.');
}

$plainpinyin = static function(string $text): string {
    return strtr($text, [
        'ā' => 'a', 'á' => 'a', 'ǎ' => 'a', 'à' => 'a', 'Ā' => 'A', 'Á' => 'A', 'Ǎ' => 'A', 'À' => 'A',
        'ē' => 'e', 'é' => 'e', 'ě' => 'e', 'è' => 'e', 'Ē' => 'E', 'É' => 'E', 'Ě' => 'E', 'È' => 'E',
        'ī' => 'i', 'í' => 'i', 'ǐ' => 'i', 'ì' => 'i', 'Ī' => 'I', 'Í' => 'I', 'Ǐ' => 'I', 'Ì' => 'I',
        'ō' => 'o', 'ó' => 'o', 'ǒ' => 'o', 'ò' => 'o', 'Ō' => 'O', 'Ó' => 'O', 'Ǒ' => 'O', 'Ò' => 'O',
        'ū' => 'u', 'ú' => 'u', 'ǔ' => 'u', 'ù' => 'u', 'ǖ' => 'u', 'ǘ' => 'u', 'ǚ' => 'u', 'ǜ' => 'u',
        'Ū' => 'U', 'Ú' => 'U', 'Ǔ' => 'U', 'Ù' => 'U', 'Ǖ' => 'U', 'Ǘ' => 'U', 'Ǚ' => 'U', 'Ǜ' => 'U',
    ]);
};

foreach ($duiyaopairs as $pair) {
    foreach (explode(' + ', $pair[1]) as $displayherb) {
        $plainherb = $plainpinyin($displayherb);
        if (!isset($duiyaoherbcategories[$plainherb])
                || !isset($herbcategories[$duiyaoherbcategories[$plainherb] - 1])) {
            throw new moodle_exception('Every displayed Dui Yao herb must map to an individual Bensky herb category.');
        }
    }
}

$bridgecolorsbycategory = [];
$paircolorsbycategory = [];
$formulacolorsbycategory = [];
foreach ($categoryduiyao as $cardnumber => $pairnumbers) {
    $nextcolor = 1;
    foreach ($pairnumbers as $pairnumber) {
        $paircolors = [];
        foreach (explode(' + ', $duiyaopairs[$pairnumber][1]) as $displayherb) {
            $plainherb = $plainpinyin($displayherb);
            if (!isset($bridgecolorsbycategory[$cardnumber][$plainherb])) {
                $bridgecolorsbycategory[$cardnumber][$plainherb] = $nextcolor;
                $nextcolor = $nextcolor === 8 ? 1 : $nextcolor + 1;
            }
            $paircolors[] = $bridgecolorsbycategory[$cardnumber][$plainherb];
        }
        $paircolorsbycategory[$cardnumber][$pairnumber] = array_values(array_unique($paircolors));
        foreach (explode(';', $duiyaopairs[$pairnumber][3]) as $formulalabel) {
            $plainformula = trim((string) preg_replace('/\s*\([^)]*\)\s*$/u', '', $plainpinyin(trim($formulalabel))));
            if ($plainformula !== '') {
                $formulacolorsbycategory[$cardnumber][$plainformula] = $paircolorsbycategory[$cardnumber][$pairnumber];
            }
        }
    }
}

$highlightbridgetext = static function(string $text, array $herbcolors): string {
    $escaped = s($text);
    if ($herbcolors === []) {
        return $escaped;
    }
    $herbs = array_keys($herbcolors);
    usort($herbs, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));
    $lookup = [];
    foreach ($herbcolors as $herb => $color) {
        $lookup[strtolower($herb)] = $color;
    }
    $pattern = '/(?<![A-Za-z])(' . implode('|', array_map(static fn(string $herb): string => preg_quote($herb, '/'), $herbs)) . ')(?![A-Za-z])/i';
    return (string) preg_replace_callback($pattern, static function(array $match) use ($lookup): string {
        $color = $lookup[strtolower($match[0])] ?? 1;
        return '<span class="herb-formula-map__bridge-herb herb-formula-map__bridge-herb--' . $color . '">' . $match[0] . '</span>';
    }, $escaped);
};

$renderduiyaotitle = static function(array $pair, array $herbcolors) use ($plainpinyin): string {
    $markup = [];
    foreach (explode(' + ', $pair[1]) as $displayherb) {
        $plainherb = $plainpinyin($displayherb);
        $color = $herbcolors[$plainherb] ?? 1;
        $markup[] = '<span class="herb-formula-map__bridge-herb herb-formula-map__bridge-herb--' . $color . '">' . s($displayherb) . '</span>';
    }
    return implode(' <span aria-hidden="true">+</span> ', $markup);
};

$renderduiyaomembers = static function(array $pair, array $herbcolors) use (
    $plainpinyin,
    $duiyaoherbcategories,
    $herbcategories,
    $ises
): string {
    $markup = [];
    foreach (explode(' + ', $pair[1]) as $displayherb) {
        $plainherb = $plainpinyin($displayherb);
        $color = $herbcolors[$plainherb] ?? 1;
        $categorycard = $duiyaoherbcategories[$plainherb];
        $herbcategory = $herbcategories[$categorycard - 1];
        $categoryname = $herbcategory[$ises ? 3 : 2];
        $markup[] = '<span class="herb-formula-map__duiyao-member">'
            . '<span class="herb-formula-map__bridge-herb herb-formula-map__bridge-herb--' . $color . '">' . s($displayherb) . '</span>'
            . '<small><b>' . ($ises ? 'Categoría herbal:' : 'Herb category:') . '</b> ' . s($categoryname) . '</small>'
            . '</span>';
    }
    return implode('<span class="herb-formula-map__duiyao-plus" aria-hidden="true">+</span>', $markup);
};

$formulapalette = [
    1 => '#fee2e2', 2 => '#dbeafe', 3 => '#dcfce7', 4 => '#fef3c7',
    5 => '#ede9fe', 6 => '#cffafe', 7 => '#fce7f3', 8 => '#ecfccb',
];
$renderformulabadge = static function(string $label, array $colors) use ($formulapalette): string {
    $colors = array_values(array_unique($colors));
    if ($colors === []) {
        return s($label);
    }
    $segments = [];
    $count = count($colors);
    foreach ($colors as $index => $color) {
        $start = (int) round(($index * 100) / $count);
        $end = (int) round((($index + 1) * 100) / $count);
        $hex = $formulapalette[$color] ?? '#f1f5f9';
        $segments[] = $hex . ' ' . $start . '%';
        $segments[] = $hex . ' ' . $end . '%';
    }
    $background = $count === 1
        ? ($formulapalette[$colors[0]] ?? '#f1f5f9')
        : 'linear-gradient(90deg,' . implode(',', $segments) . ')';
    return '<span class="herb-formula-map__bridge-formula" style="background:' . $background . '">' . s($label) . '</span>';
};

$highlightbridgeformulas = static function(string $text, array $formulacolors) use ($renderformulabadge): string {
    $escaped = s($text);
    if ($formulacolors === []) {
        return $escaped;
    }
    $formulas = array_keys($formulacolors);
    usort($formulas, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));
    $lookup = [];
    foreach ($formulacolors as $formula => $colors) {
        $lookup[strtolower($formula)] = $colors;
    }
    $pattern = '/(?<![A-Za-z])(' . implode('|', array_map(static fn(string $formula): string => preg_quote($formula, '/'), $formulas)) . ')(?![A-Za-z])/i';
    return (string) preg_replace_callback($pattern, static function(array $match) use ($lookup, $renderformulabadge): string {
        return $renderformulabadge($match[0], $lookup[strtolower($match[0])] ?? []);
    }, $escaped);
};

$renderbridgeformulas = static function(string $text, array $colors) use ($renderformulabadge): string {
    return implode('; ', array_map(
        static fn(string $formula): string => $renderformulabadge(trim($formula), $colors),
        explode(';', $text)
    ));
};

$langparams = $ises ? ['lang' => 'es'] : [];
$pageurl = new moodle_url('/blocks/myprogress/herb-formula-category-map.php', $langparams);
$languageurl = new moodle_url('/blocks/myprogress/herb-formula-category-map.php', $ises ? [] : ['lang' => 'es']);
$huburl = new moodle_url('/blocks/myprogress/study-guide.php', ['area' => 'herbology'] + $langparams);
$herbsurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $langparams);
$duiyaourl = new moodle_url($ises ? '/study-guide/comparisons/dui-yao-es.html' : '/study-guide/comparisons/dui-yao.html');
$formulasurl = new moodle_url('/blocks/myprogress/formulas-study-guide.php', $langparams);
$mapnavscripturl = new moodle_url('/study-guide/comparisons/bensky-crosswalk-navigation.js');
$pagetitle = $ises ? 'Mapa comparativo Bensky' : 'Bensky Category Crosswalk';

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();
?>
<style>
.herb-formula-map{max-width:1220px;margin:24px auto 60px;padding:0 16px;color:#17232a}.herb-formula-map__top{display:flex;align-items:flex-start;justify-content:space-between;gap:22px;margin-bottom:22px}.herb-formula-map__eyebrow{margin:0 0 6px;color:#08746f;font-size:13px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}.herb-formula-map h2{margin:0 0 9px;font-size:clamp(29px,4vw,42px);line-height:1.12}.herb-formula-map__lead{max-width:790px;margin:0;color:#52677a;font-size:16px;line-height:1.6}.herb-formula-map__actions{display:flex;justify-content:flex-end;gap:9px;flex-wrap:wrap}.herb-formula-map__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:9px 14px;border:1px solid #cbd5dc;border-radius:8px;background:#fff;color:#334155;font-weight:800;text-decoration:none;white-space:nowrap}.herb-formula-map__button:hover{background:#f1f5f9;color:#17232a;text-decoration:none}.herb-formula-map__overview{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:24px;align-items:center;margin-bottom:22px;padding:22px 25px;border-left:5px solid #0b817d;border-radius:12px;background:#eef8f6}.herb-formula-map__overview h3{margin:0 0 7px;font-size:22px}.herb-formula-map__overview p{max-width:820px;margin:0;color:#52677a;line-height:1.55}.herb-formula-map__stats{display:flex;gap:9px}.herb-formula-map__stat{min-width:96px;padding:12px;border-radius:9px;background:#fff;text-align:center}.herb-formula-map__stat strong{display:block;color:#075e54;font-size:26px}.herb-formula-map__stat span{font-size:10px;font-weight:900;text-transform:uppercase}.herb-formula-map__table-wrap{overflow-x:auto;border:1px solid #d5e0e5;border-radius:12px;background:#fff;box-shadow:0 4px 16px rgba(15,23,42,.06)}.herb-formula-map__table{width:100%;min-width:860px;border-collapse:collapse;table-layout:fixed}.herb-formula-map__table th{padding:16px 18px;background:#17344a;color:#fff;text-align:left;font-size:14px}.herb-formula-map__table th+th,.herb-formula-map__table td+td{border-left:1px solid #dfe7eb}.herb-formula-map__table td{padding:14px;border-top:1px solid #dfe7eb;vertical-align:top}.herb-formula-map__cell{height:100%;padding:15px;border:1px solid #dce5e9;border-top:4px solid #f77e25;border-radius:10px;background:#fffaf6}.herb-formula-map__cell--formula{border-top-color:#0b817d;background:#f4fbf9}.herb-formula-map__cell--none{border-top-color:#94a3b8;background:#f8fafc}.herb-formula-map__meta{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px}.herb-formula-map__number,.herb-formula-map__chapter{display:inline-flex;padding:4px 8px;border-radius:999px;background:#eaf4f1;color:#07665f;font-size:10px;font-weight:900;text-transform:uppercase}.herb-formula-map__number{background:#fff0e5;color:#a54d13}.herb-formula-map__cell h4{margin:0 0 9px;font-size:17px;line-height:1.35}.herb-formula-map__examples{margin:0;color:#52677a;font-size:13px;line-height:1.5}.herb-formula-map__examples strong{color:#17232a}.herb-formula-map__match+.herb-formula-map__match{margin-top:11px;padding-top:11px;border-top:1px dashed #bed5cf}.herb-formula-map__relation{margin:10px 0 0;padding:8px 9px;border-radius:7px;background:#e7f4f1;color:#245b55;font-size:12px;font-weight:750;line-height:1.45}.herb-formula-map__note{margin:18px 0 0;padding:15px 17px;border-left:4px solid #315fa5;border-radius:8px;background:#f3f7fd;color:#435d7a;line-height:1.55}.herb-formula-map__note+.herb-formula-map__table-wrap{margin-top:18px}.herb-formula-map a:focus-visible{outline:3px solid #185cca;outline-offset:3px}@media(max-width:760px){.herb-formula-map__top{display:block}.herb-formula-map__actions{justify-content:flex-start;margin-top:16px}.herb-formula-map__overview{display:block}.herb-formula-map__stats{margin-top:16px}.herb-formula-map__stat{flex:1;min-width:0}}
.herb-formula-map__table{min-width:1120px}
.herb-formula-map__table th:nth-child(1){width:32%}
.herb-formula-map__table th:nth-child(2){width:32%}
.herb-formula-map__table th:nth-child(3){width:36%}
.herb-formula-map__cell--duiyao{border-top-color:#6d4bc3;background:#faf8ff}
.herb-formula-map__cell--relation{border-top-color:#315fa5;background:#f3f7fd}
.herb-formula-map__explanation{margin:0;color:#435d7a;font-size:13px;line-height:1.55}
.herb-formula-map__duiyao{margin-top:12px;padding:10px 11px;border:1px solid #ddd6f3;border-radius:8px;background:#faf8ff}
.herb-formula-map__duiyao-title{display:block;margin-bottom:7px;color:#5a37ac;font-size:11px;font-weight:900;text-transform:uppercase}
.herb-formula-map__duiyao-list{display:grid;gap:7px}
.herb-formula-map__duiyao-pair{display:block;padding:8px 9px;border:1px solid #e3def2;border-radius:7px;background:#fff;color:#334155;text-decoration:none}
.herb-formula-map__duiyao-pair:hover{border-color:#8b6bd1;color:#334155;text-decoration:none}
.herb-formula-map__duiyao-pair span{display:block;color:#6d4bc3;font-size:9px;font-weight:900;text-transform:uppercase}
.herb-formula-map__duiyao-pair strong{display:inline-block;margin-top:3px;color:#2f2350;font-size:13px}
.herb-formula-map__duiyao-pair em{margin-left:6px;color:#7a6b8d;font-size:12px;font-style:normal}
.herb-formula-map__duiyao-pair small{display:block;margin-top:3px;color:#52677a;font-size:11px;line-height:1.4}
.herb-formula-map__duiyao-pair small+small{margin-top:4px;padding-top:4px;border-top:1px dashed #e3def2}
.herb-formula-map__duiyao-members{display:grid;grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);gap:7px;align-items:start;margin-top:4px}
.herb-formula-map__duiyao-member{min-width:0}
.herb-formula-map__duiyao-member small{margin-top:5px;padding-top:5px;border-top:1px dashed #e3def2}
.herb-formula-map__duiyao-member small b{display:block;color:#4c3b75}
.herb-formula-map__duiyao-plus{padding-top:3px;color:#6d4bc3;font-weight:900}
.herb-formula-map__duiyao-open{display:block;margin-top:8px;padding-top:7px;border-top:1px solid #e3def2;color:#5a37ac;font-size:11px;font-weight:900}
.herb-formula-map__duiyao-empty{margin:0;color:#6b7280;font-size:13px;line-height:1.5}
.herb-formula-map__composition{margin:12px 0 0;padding:10px 11px;border-radius:8px;background:#fff;color:#435d7a;font-size:12px;line-height:1.5}
.herb-formula-map__composition strong{display:block;margin-bottom:3px;color:#17344a}
.herb-formula-map__formula-bridges{margin-top:12px;padding:10px 11px;border:1px solid #b9ddd7;border-radius:8px;background:#fff}
.herb-formula-map__formula-bridges>strong{display:block;margin-bottom:7px;color:#07665f;font-size:11px;text-transform:uppercase}
.herb-formula-map__formula-bridge{margin:5px 0 0;color:#52677a;font-size:12px;line-height:1.45}
.herb-formula-map__formula-bridge b{color:#17344a}
.herb-formula-map__color-key{margin:12px 0 0;padding:10px 12px;border:1px solid #d9e2ef;border-radius:8px;background:#fff;color:#435d7a;font-size:12px;font-weight:750}
.herb-formula-map__bridge-herb{display:inline-block;margin:1px 0;padding:1px 5px;border:1px solid transparent;border-radius:999px;font-weight:850;line-height:1.35;white-space:nowrap}
.herb-formula-map__bridge-herb--1{border-color:#f4a6a6;background:#fee2e2;color:#8f1d1d}
.herb-formula-map__bridge-herb--2{border-color:#93c5fd;background:#dbeafe;color:#1e4f91}
.herb-formula-map__bridge-herb--3{border-color:#86d7ad;background:#dcfce7;color:#166534}
.herb-formula-map__bridge-herb--4{border-color:#f5c56b;background:#fef3c7;color:#854d0e}
.herb-formula-map__bridge-herb--5{border-color:#c4b5fd;background:#ede9fe;color:#5b21b6}
.herb-formula-map__bridge-herb--6{border-color:#67d8e5;background:#cffafe;color:#155e75}
.herb-formula-map__bridge-herb--7{border-color:#f0a7cb;background:#fce7f3;color:#9d174d}
.herb-formula-map__bridge-herb--8{border-color:#b5d66b;background:#ecfccb;color:#3f6212}
.herb-formula-map__bridge-formula{display:inline-block;margin:1px 0;padding:2px 7px;border:1px solid #94a3b8;border-radius:7px;color:#17232a;font-weight:850;line-height:1.4;box-decoration-break:clone;-webkit-box-decoration-break:clone}
.herb-formula-map__route{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 0 18px;padding:11px 14px;border:1px solid #d9e2e7;border-radius:9px;background:#fff;color:#64748b;font-size:12px;font-weight:750}
.herb-formula-map__route a{color:#315fa5;text-decoration:none}.herb-formula-map__route a:hover{text-decoration:underline}.herb-formula-map__route-current{color:#17232a}
.herb-formula-map__journey{position:sticky;top:76px;z-index:12;display:grid;grid-template-columns:minmax(190px,1fr) minmax(260px,1.15fr) minmax(190px,1fr);gap:12px;align-items:stretch;margin:18px 0;padding:13px;border:1px solid #d5e0e5;border-radius:12px;background:rgba(255,255,255,.97);box-shadow:0 7px 22px rgba(15,23,42,.1);backdrop-filter:blur(8px)}
.herb-formula-map__journey-link{display:flex;min-height:58px;flex-direction:column;justify-content:center;padding:9px 12px;border:1px solid #dbe4e9;border-radius:9px;background:#fff;color:#17344a;text-decoration:none}.herb-formula-map__journey-link:hover{border-color:#f77e25;color:#17344a;text-decoration:none}.herb-formula-map__journey-link--next{text-align:right}.herb-formula-map__journey-link[aria-disabled="true"]{visibility:hidden;pointer-events:none}.herb-formula-map__journey-label{color:#64748b;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.herb-formula-map__journey-name{margin-top:3px;color:#e66716;font-size:12px;font-weight:850;line-height:1.35}
.herb-formula-map__journey-jump{display:flex;flex-direction:column;justify-content:center}.herb-formula-map__journey-jump label{margin:0 0 4px;color:#64748b;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.herb-formula-map__journey-jump select{width:100%;min-height:40px;padding:7px 34px 7px 10px;border:1px solid #aebdc7;border-radius:8px;background:#fff;color:#17232a;font-weight:750}.herb-formula-map tr[id^="map-card-"]{scroll-margin-top:108px}
@media(max-width:800px){.herb-formula-map__journey{position:static;grid-template-columns:1fr 1fr}.herb-formula-map__journey-jump{grid-column:1/-1;grid-row:1}.herb-formula-map__journey-link--next{text-align:right}}
.herb-formula-map__relationship-row td{padding:0 14px 18px;border-top:0}
.herb-formula-map__relationship-row td+td{border-left:0}
.herb-formula-map__relationship-card{padding:15px 17px;border:1px solid #d9e2ef;border-left:5px solid #315fa5;border-radius:10px;background:#f3f7fd}
</style>
<main class="herb-formula-map">
    <header class="herb-formula-map__top">
        <div>
            <p class="herb-formula-map__eyebrow"><?php echo s($ises ? 'Guía combinada · Bensky' : 'Combined study guide · Bensky'); ?></p>
            <h2><?php echo s($pagetitle); ?></h2>
            <p class="herb-formula-map__lead"><?php echo s($ises ? 'Relaciona dos niveles distintos de Bensky: acciones de sustancias individuales y estrategias de prescripciones completas. Los nombres parecidos facilitan el estudio, pero no representan la misma categoría.' : 'Connect two distinct Bensky levels: actions of individual substances and strategies of complete prescriptions. Similar names aid study but do not represent the same category.'); ?></p>
        </div>
        <nav class="herb-formula-map__actions" aria-label="<?php echo s($ises ? 'Navegación del mapa' : 'Map navigation'); ?>">
            <a class="herb-formula-map__button" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
            <a class="herb-formula-map__button" href="<?php echo $herbsurl->out(false); ?>"><?php echo s($ises ? 'Hierbas individuales' : 'Single Herbs'); ?></a>
            <a class="herb-formula-map__button" href="<?php echo $duiyaourl->out(false); ?>">Duì Yào</a>
            <a class="herb-formula-map__button" href="<?php echo $formulasurl->out(false); ?>"><?php echo s($ises ? 'Fórmulas' : 'Formulas'); ?></a>
            <a class="herb-formula-map__button" href="<?php echo $huburl->out(false); ?>"><?php echo s($ises ? '← Guía de estudio' : '← Study Guide'); ?></a>
        </nav>
    </header>
    <nav class="herb-formula-map__route" aria-label="<?php echo s($ises ? 'Ruta de estudio' : 'Study path'); ?>">
        <a href="<?php echo $huburl->out(false); ?>"><?php echo $ises ? 'Guía de estudio' : 'Study Guide'; ?></a><span aria-hidden="true">›</span>
        <span><?php echo $ises ? 'Mapa Bensky' : 'Bensky Map'; ?></span><span aria-hidden="true">›</span>
        <a href="<?php echo $herbsurl->out(false); ?>"><?php echo $ises ? 'Hierbas' : 'Herbs'; ?></a><span aria-hidden="true">→</span>
        <a href="<?php echo $duiyaourl->out(false); ?>">Duì Yào</a><span aria-hidden="true">→</span>
        <a href="<?php echo $formulasurl->out(false); ?>"><?php echo $ises ? 'Fórmulas' : 'Formulas'; ?></a><span aria-hidden="true">›</span>
        <span class="herb-formula-map__route-current" data-map-route-current><?php echo s($ises ? $herbcategories[0][3] : $herbcategories[0][2]); ?></span>
    </nav>
    <section class="herb-formula-map__overview" aria-labelledby="map-overview-title">
        <div><h3 id="map-overview-title"><?php echo s($ises ? 'Cómo leer el mapa' : 'How to read this crosswalk'); ?></h3><p><?php echo s($ises ? 'Nivel 1 clasifica hierbas individuales por sus acciones. Nivel 2, Duì Yào, muestra cómo dos hierbas trabajan juntas. Nivel 3 agrupa fórmulas completas por su estrategia terapéutica. Las flechas indican relación, no equivalencia.' : 'Level 1 classifies individual herbs by their actions. Level 2, Duì Yào, shows how two herbs work together. Level 3 groups complete formulas by therapeutic strategy. Arrows indicate relationship, not equivalence.'); ?></p><p class="herb-formula-map__color-key"><?php echo s($ises ? 'Clave visual: dentro de cada fila, el mismo color identifica la misma hierba en la categoría, el Duì Yào y los ejemplos de fórmula.' : 'Visual key: within each row, the same color identifies the same herb in the category, Duì Yào, and formula examples.'); ?></p></div>
        <div class="herb-formula-map__stats"><div class="herb-formula-map__stat"><strong>34</strong><span><?php echo $ises ? 'categorías herbales' : 'herb categories'; ?></span></div><div class="herb-formula-map__stat"><strong>21</strong><span><?php echo $ises ? 'capítulos de fórmulas' : 'formula chapters'; ?></span></div></div>
    </section>
    <p class="herb-formula-map__note"><strong><?php echo $ises ? 'Dos relaciones claras:' : 'Two clear relationships:'; ?></strong> <?php echo s($ises ? 'Las hierbas tibias y frescas que liberan el Exterior se relacionan con el capítulo 1 de fórmulas, Release the Exterior. En Clear Heat, Bensky divide las hierbas en cinco acciones —Drain Fire, Cool the Blood, Dry Dampness, Resolve Toxicity y Deficiency Heat— mientras el capítulo 4 agrupa fórmulas completas. Se relacionan, pero no se llaman ni se clasifican de la misma manera.' : 'Warm and cool herbs that release the Exterior relate to Formula Chapter 1, Release the Exterior. For Clear Heat, Bensky divides herbs into five actions—Drain Fire, Cool the Blood, Dry Dampness, Resolve Toxicity, and Deficiency Heat—while Formula Chapter 4 groups complete prescriptions. They relate, but are not named or classified at the same level.'); ?></p>
    <nav class="herb-formula-map__journey" data-map-navigation aria-label="<?php echo s($ises ? 'Navegación entre categorías' : 'Category navigation'); ?>">
        <a class="herb-formula-map__journey-link" href="#map-card-1" data-map-previous aria-disabled="true"><span class="herb-formula-map__journey-label"><?php echo $ises ? '← Categoría anterior' : '← Previous category'; ?></span><span class="herb-formula-map__journey-name" data-map-previous-name></span></a>
        <div class="herb-formula-map__journey-jump"><label for="bensky-map-jump"><?php echo $ises ? 'Ir a…' : 'Jump to…'; ?></label><select id="bensky-map-jump" data-map-jump><?php foreach ($herbcategories as $jumpcategory): ?><option value="<?php echo (int) $jumpcategory[0]; ?>"><?php echo s($jumpcategory[0] . ' · ' . ($ises ? $jumpcategory[3] : $jumpcategory[2])); ?></option><?php endforeach; ?></select></div>
        <a class="herb-formula-map__journey-link herb-formula-map__journey-link--next" href="#map-card-2" data-map-next><span class="herb-formula-map__journey-label"><?php echo $ises ? 'Categoría siguiente →' : 'Next category →'; ?></span><span class="herb-formula-map__journey-name" data-map-next-name><?php echo s($ises ? $herbcategories[1][3] : $herbcategories[1][2]); ?></span></a>
    </nav>
    <div class="herb-formula-map__table-wrap"><table class="herb-formula-map__table">
        <thead><tr><th><?php echo s($ises ? 'NIVEL 1 · HIERBAS' : 'LEVEL 1 · HERBS'); ?></th><th><?php echo s($ises ? 'NIVEL 2 · DUÌ YÀO' : 'LEVEL 2 · DUÌ YÀO'); ?></th><th><?php echo s($ises ? 'NIVEL 3 · FÓRMULAS' : 'LEVEL 3 · FORMULAS'); ?></th></tr></thead>
        <tbody><?php foreach ($herbcategories as $category): $rowbridgecolors = $bridgecolorsbycategory[$category[0]] ?? []; $rowformulacolors = $formulacolorsbycategory[$category[0]] ?? []; ?><tr id="map-card-<?php echo (int) $category[0]; ?>">
            <td><article class="herb-formula-map__cell"><div class="herb-formula-map__meta"><span class="herb-formula-map__number"><?php echo s(($ises ? 'Tarjeta ' : 'Card ') . $category[0]); ?></span><span class="herb-formula-map__chapter">Bensky · <?php echo s(($ises ? 'Capítulo ' : 'Chapter ') . $category[1]); ?></span></div><h4><?php echo s($ises ? $category[3] : $category[2]); ?></h4><p class="herb-formula-map__examples"><strong><?php echo $ises ? 'Hierbas principales:' : 'Representative herbs:'; ?></strong> <?php echo $highlightbridgetext($category[4], $rowbridgecolors); ?></p></article></td>
            <td><article class="herb-formula-map__cell herb-formula-map__cell--duiyao"><div class="herb-formula-map__meta"><span class="herb-formula-map__chapter"><?php echo s($ises ? 'Puente de parejas' : 'Pair bridge'); ?></span></div><?php if (isset($categoryduiyao[$category[0]])): ?><strong class="herb-formula-map__duiyao-title"><?php echo $ises ? 'Duì Yào representativos' : 'Representative Duì Yào'; ?></strong><div class="herb-formula-map__duiyao-list"><?php foreach ($categoryduiyao[$category[0]] as $pairnumber): $pair = $duiyaopairs[$pairnumber]; $paircategory = $duiyaocategories[$pairnumber]; $pairurl = new moodle_url('/study-guide/comparisons/' . $pair[0] . ($ises ? '-es' : '') . '.html', ['from' => 'bensky-crosswalk', 'focus' => $category[0]]); ?><a class="herb-formula-map__duiyao-pair" href="<?php echo $pairurl->out(false); ?>"><span><?php echo s(($ises ? 'Pareja oficial ' : 'Official pair ') . $pairnumber . '/51'); ?></span><span class="herb-formula-map__duiyao-members"><?php echo $renderduiyaomembers($pair, $rowbridgecolors); ?></span><small><b><?php echo $ises ? 'Categoría de la pareja:' : 'Pair category:'; ?></b> <?php echo s($paircategory[$ises ? 1 : 0]); ?></small><span class="herb-formula-map__duiyao-open"><?php echo $ises ? 'Abrir ficha Duì Yào →' : 'Open Duì Yào profile →'; ?></span></a><?php endforeach; ?></div><?php else: ?><h4><?php echo s($ises ? 'Sin pareja oficial representativa seleccionada' : 'No representative official pair selected'); ?></h4><p class="herb-formula-map__duiyao-empty"><?php echo s($ises ? 'La relación de estudio pasa directamente de la acción herbal a la estrategia de la fórmula; no se fuerza una pareja que no sea clara.' : 'The study relationship moves directly from the herb action to the formula strategy; no unclear pair is forced.'); ?></p><?php endif; ?></article></td>
            <td><article class="herb-formula-map__cell herb-formula-map__cell--formula<?php echo $category[5] === [] ? ' herb-formula-map__cell--none' : ''; ?>">
                <?php if ($category[5] === []): ?><div class="herb-formula-map__meta"><span class="herb-formula-map__chapter"><?php echo s($ises ? 'Sin categoría directa' : 'No direct category'); ?></span></div><h4><?php echo s($ises ? 'Referencia histórica de seguridad' : 'Historical safety cross-reference'); ?></h4><?php endif; ?>
                <?php foreach ($category[5] as $chapternumber): $formulachapter = $formulachapters[$chapternumber]; ?><section class="herb-formula-map__match"><div class="herb-formula-map__meta"><span class="herb-formula-map__chapter">Bensky · <?php echo s(($ises ? 'Capítulo ' : 'Chapter ') . $chapternumber); ?></span></div><h4><?php echo s($formulachapter[$ises ? 1 : 0]); ?></h4><p class="herb-formula-map__examples"><strong><?php echo $ises ? 'Fórmulas principales:' : 'Representative formulas:'; ?></strong> <?php echo $highlightbridgeformulas($formulachapter[2], $rowformulacolors); ?></p></section><?php endforeach; ?><?php if (isset($categoryduiyao[$category[0]])): ?><section class="herb-formula-map__formula-bridges"><strong><?php echo $ises ? 'Fórmulas puente desde Duì Yào' : 'Formula bridges from Duì Yào'; ?></strong><?php foreach ($categoryduiyao[$category[0]] as $pairnumber): $pair = $duiyaopairs[$pairnumber]; ?><p class="herb-formula-map__formula-bridge"><b><?php echo $renderduiyaotitle($pair, $rowbridgecolors); ?>:</b> <?php echo $renderbridgeformulas($pair[3], $paircolorsbycategory[$category[0]][$pairnumber] ?? []); ?></p><?php endforeach; ?></section><?php endif; ?>
            </article></td>
        </tr><tr class="herb-formula-map__relationship-row"><td colspan="3"><article class="herb-formula-map__relationship-card"><div class="herb-formula-map__meta"><span class="herb-formula-map__chapter"><?php echo s($ises ? 'CÓMO SE RELACIONAN · EXPLICACIÓN CLÍNICA' : 'HOW THEY RELATE · CLINICAL EXPLANATION'); ?></span></div><p class="herb-formula-map__explanation"><?php echo s($relationshipnotes[$category[0]][$ises ? 1 : 0]); ?></p><p class="herb-formula-map__relation"><?php echo s($relationshiplabels[$category[6]][$ises ? 1 : 0]); ?></p><p class="herb-formula-map__composition"><strong><?php echo $ises ? 'Ejemplos dentro de fórmulas:' : 'Examples inside formulas:'; ?></strong><?php echo $highlightbridgetext($compositionexamples[$category[0]][$ises ? 1 : 0], $rowbridgecolors); ?></p></article></td></tr><?php endforeach; ?></tbody>
    </table></div>
    <p class="herb-formula-map__note"><strong><?php echo $ises ? 'Límite educativo:' : 'Educational boundary:'; ?></strong> <?php echo s($ises ? 'La semejanza del nombre no sustituye patrón, arquitectura de la fórmula, dosis ni seguridad. Esta tabla organiza el estudio; no constituye guía de prescripción.' : 'Name similarity does not replace pattern, formula architecture, dosage, or safety. This table organizes study and is not prescribing guidance.'); ?></p>
</main>
<script src="<?php echo $mapnavscripturl->out(false); ?>" defer></script>
<?php
echo $OUTPUT->footer();
