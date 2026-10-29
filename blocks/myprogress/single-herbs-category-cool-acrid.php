<?php
// Full study content for Cool, Acrid herbs that release the Exterior.

defined('MOODLE_INTERNAL') || die();

$cooltext = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};

$coolherbs = [
    [
        'name' => 'Bo He', 'latin' => 'Menthae haplocalycis Herba',
        'properties' => $cooltext('Acrid, aromatic; cool', 'Acre, aromática; fresca'),
        'channels' => $cooltext('Lung, Liver', 'Pulmón, Hígado'), 'dose' => '3–6 g',
        'identity' => $cooltext('Quickly disperses Wind-Heat from the head, eyes and throat, vents rashes and courses constrained Liver Qi.', 'Dispersa rápidamente Viento-Calor de cabeza, ojos y garganta, exterioriza erupciones y moviliza el Qi de Hígado estancado.'),
        'clue' => $cooltext('Wind-Heat with headache, red eyes, sore throat or rash plus irritability or hypochondriac constraint.', 'Viento-Calor con cefalea, ojos rojos, dolor de garganta o erupción, junto con irritabilidad o tensión hipocondríaca.'),
        'memory' => $cooltext('Mint opens the upstairs windows: head, eyes and throat clear while constrained Liver Qi begins to move.', 'La menta abre las ventanas del piso superior: despeja cabeza, ojos y garganta mientras moviliza el Qi de Hígado.'),
        'unique' => $cooltext("Yang ranks Bo He with Niu Bang Zi as the coldest of this group and first for light dispersion. Its aromatic Liver-Qi action distinguishes it from the heavier seed.", "Yang coloca a Bo He, junto con Niu Bang Zi, como la más fría del grupo y primera en dispersión ligera. Su acción aromática sobre el Qi de Hígado la distingue de la semilla más pesada."),
        'formulas' => $cooltext('Supporting ingredient in Yin Qiao San and Sang Ju Yin. It is not the chief herb in either current Board formula.', 'Ingrediente de apoyo en Yin Qiao San y Sang Ju Yin. No es la hierba principal en ninguna de estas fórmulas Board vigentes.'),
        'caution' => $cooltext('Add near the end of the decoction. Use cautiously with Yin deficiency, spontaneous sweating or depleted fluids.', 'Añadir al final de la decocción. Usar con precaución ante deficiencia de Yin, sudoración espontánea o líquidos agotados.'),
    ],
    [
        'name' => 'Niu Bang Zi', 'latin' => 'Arctii Fructus',
        'properties' => $cooltext('Acrid, bitter; cold', 'Acre, amarga; fría'),
        'channels' => $cooltext('Lung, Stomach', 'Pulmón, Estómago'), 'dose' => '6–12 g, crushed',
        'identity' => $cooltext('Disperses Wind-Heat, benefits the throat, resolves toxicity, vents rashes and moistens the intestines.', 'Dispersa Viento-Calor, beneficia la garganta, resuelve toxicidad, exterioriza erupciones y humedece los intestinos.'),
        'clue' => $cooltext('Painful swollen throat, cough, incompletely expressed rash or red itchy lesions accompanied by constipation.', 'Garganta dolorosa e inflamada, tos, erupción que no exterioriza por completo o lesiones rojas con prurito y estreñimiento.'),
        'memory' => $cooltext('The burdock seed rolls downward: it opens the throat and skin while its oil keeps the bowel moving.', 'La semilla de bardana rueda hacia abajo: abre garganta y piel mientras su aceite mantiene el intestino en movimiento.'),
        'unique' => $cooltext("It is tied with Bo He as Yang's coldest herb in the group, but its heavy, oily seed both disperses outward and drains downward.", 'Empata con Bo He como la hierba más fría según Yang, pero su semilla pesada y oleosa dispersa hacia afuera y drena hacia abajo.'),
        'formulas' => $cooltext('Supporting ingredient in Yin Qiao San; Jin Yin Hua and Lian Qiao lead that current Board formula.', 'Ingrediente de apoyo en Yin Qiao San; Jin Yin Hua y Lian Qiao dirigen esa fórmula Board vigente.'),
        'caution' => $cooltext('Its slippery nature may aggravate loose stools or Spleen deficiency diarrhea.', 'Su naturaleza deslizante puede agravar heces sueltas o diarrea por deficiencia de Bazo.'),
    ],
    [
        'name' => 'Chan Tui', 'latin' => 'Cicadae Periostracum',
        'properties' => $cooltext('Acrid, sweet; slightly cold', 'Acre, dulce; ligeramente fría'),
        'channels' => $cooltext('Lung, Liver', 'Pulmón, Hígado'), 'dose' => '3–6 g',
        'identity' => $cooltext('Gently disperses Wind-Heat, vents rashes, stops itching, benefits the eyes and throat, and calms spasms.', 'Dispersa suavemente Viento-Calor, exterioriza erupciones, detiene prurito, beneficia ojos y garganta y calma espasmos.'),
        'clue' => $cooltext('A child or sensitive patient with rash, itching, hoarse voice, red eyes or febrile spasms.', 'Niño o paciente sensible con erupción, prurito, voz ronca, ojos rojos o espasmos febriles.'),
        'memory' => $cooltext('A cicada leaves its shell: the skin opens, the voice emerges and the light shell reaches the eyes.', 'La cigarra deja su caparazón: la piel se abre, aparece la voz y la envoltura ligera alcanza los ojos.'),
        'unique' => $cooltext("Yang ranks it second for light dispersion. Its sloughed-skin symbolism connects skin, voice and the superficial visual obstruction of the eyes.", 'Yang la coloca segunda en dispersión ligera. El simbolismo de la muda conecta piel, voz y obstrucción visual superficial.'),
        'formulas' => $cooltext('No chief or co-chief role in the current 63-formula Board roster is assigned here.', 'No se asigna aquí ningún papel de hierba principal o coprincipal dentro de las 63 fórmulas Board vigentes.'),
        'caution' => $cooltext('Use cautiously during pregnancy and in patients without a Wind-Heat presentation.', 'Usar con precaución durante el embarazo y en pacientes sin una presentación de Viento-Calor.'),
    ],
    [
        'name' => 'Sang Ye', 'latin' => 'Mori Folium',
        'properties' => $cooltext('Bitter, sweet; cold', 'Amarga, dulce; fría'),
        'channels' => $cooltext('Lung, Liver', 'Pulmón, Hígado'), 'dose' => '4.5–9 g',
        'identity' => $cooltext('Disperses Wind-Heat, clears Lung Heat, moistens Lung dryness, calms the Liver and brightens the eyes.', 'Dispersa Viento-Calor, aclara Calor de Pulmón, humedece sequedad pulmonar, calma el Hígado y mejora los ojos.'),
        'clue' => $cooltext('Dry cough with scant sputum, dry throat or red eyes—especially when Lung dryness is more important than toxicity.', 'Tos seca con poco esputo, garganta seca u ojos rojos, especialmente cuando la sequedad de Pulmón pesa más que la toxicidad.'),
        'memory' => $cooltext('The frost-touched mulberry leaf cools the Lung and leaves a thin layer of moisture behind.', 'La hoja de morera tocada por la escarcha enfría el Pulmón y deja una fina capa de humedad.'),
        'unique' => $cooltext("Yang ranks it colder and more dispersing than Ju Hua. Unlike Ju Hua, it directly moistens the Lung and can cool Blood to stop bleeding.", 'Yang la considera más fría y dispersante que Ju Hua. A diferencia de Ju Hua, humedece directamente el Pulmón y puede enfriar Sangre para detener sangrado.'),
        'formulas' => $cooltext('Co-chief with Ju Hua in Sang Ju Yin, a current Board formula for early Wind-Warmth with cough.', 'Coprincipal con Ju Hua en Sang Ju Yin, fórmula Board vigente para Viento-Tibieza inicial con tos.'),
        'caution' => $cooltext('Use cautiously with cough caused primarily by Cold or marked Yang deficiency.', 'Usar con precaución cuando la tos se deba principalmente a Frío o exista deficiencia marcada de Yang.'),
    ],
    [
        'name' => 'Ju Hua', 'latin' => 'Chrysanthemi Flos',
        'properties' => $cooltext('Acrid, sweet, bitter; slightly cold', 'Acre, dulce, amarga; ligeramente fría'),
        'channels' => $cooltext('Lung, Liver', 'Pulmón, Hígado'), 'dose' => '6–15 g',
        'identity' => $cooltext('Disperses Wind-Heat, clears and calms the Liver, brightens the eyes and mildly resolves toxicity.', 'Dispersa Viento-Calor, aclara y calma el Hígado, mejora los ojos y resuelve toxicidad de manera suave.'),
        'clue' => $cooltext('Red painful eyes, headache or dizziness where Liver Heat or rising Liver Yang is more prominent than Lung dryness.', 'Ojos rojos y dolorosos, cefalea o mareo donde predomina Calor de Hígado o ascenso de Yang de Hígado más que sequedad pulmonar.'),
        'memory' => $cooltext('The chrysanthemum turns its flower-face toward the eyes: Liver Heat settles and vision clears.', 'El crisantemo dirige su rostro floral hacia los ojos: calma el Calor de Hígado y aclara la visión.'),
        'unique' => $cooltext("Less cold and less dispersing than Sang Ye in Yang's figures, but stronger for calming the Liver and brightening the eyes.", 'Es menos fría y menos dispersante que Sang Ye en los gráficos de Yang, pero más potente para calmar el Hígado y mejorar los ojos.'),
        'formulas' => $cooltext('Co-chief with Sang Ye in Sang Ju Yin.', 'Coprincipal con Sang Ye en Sang Ju Yin.'),
        'caution' => $cooltext('Use cautiously with Spleen or Stomach deficiency and poor appetite.', 'Usar con precaución ante deficiencia de Bazo o Estómago y poco apetito.'),
    ],
    [
        'name' => 'Man Jing Zi', 'latin' => 'Viticis Fructus',
        'properties' => $cooltext('Acrid, bitter; slightly cold', 'Acre, amarga; ligeramente fría'),
        'channels' => $cooltext('Bladder, Liver, Stomach', 'Vejiga, Hígado, Estómago'), 'dose' => '6–12 g',
        'identity' => $cooltext('Disperses Wind-Heat concentrated in the head, clears the eyes and can alleviate Wind-Damp pain.', 'Dispersa Viento-Calor concentrado en la cabeza, aclara los ojos y puede aliviar dolor por Viento-Humedad.'),
        'clue' => $cooltext('Headache with red painful eyes, excessive tearing or gum and tooth pain; the head is the center of the case.', 'Cefalea con ojos rojos y dolorosos, lagrimeo excesivo o dolor de encías y dientes; la cabeza domina el caso.'),
        'memory' => $cooltext('The Vitex fruit rises like a small lighthouse to the crown, eyes and teeth.', 'El fruto de Vitex sube como un pequeño faro hacia coronilla, ojos y dientes.'),
        'unique' => $cooltext("Yang places it among the least cold herbs in the group. Its narrow strength is head-and-eye Wind-Heat with a secondary Wind-Damp pain action.", 'Yang la sitúa entre las menos frías del grupo. Su fuerza específica es Viento-Calor de cabeza y ojos, con acción secundaria sobre dolor por Viento-Humedad.'),
        'formulas' => $cooltext('No chief or co-chief role in the current 63-formula Board roster is assigned here.', 'No se asigna aquí ningún papel principal o coprincipal dentro de las 63 fórmulas Board vigentes.'),
        'caution' => $cooltext('Use cautiously with headache or eye problems from Blood or Yin deficiency without Wind-Heat.', 'Usar con precaución en cefalea o trastornos oculares por deficiencia de Sangre o Yin sin Viento-Calor.'),
    ],
    [
        'name' => 'Ge Gen', 'latin' => 'Puerariae Radix',
        'properties' => $cooltext('Sweet, acrid; cool', 'Dulce, acre; fresca'),
        'channels' => $cooltext('Spleen, Stomach', 'Bazo, Estómago'), 'dose' => '9–15 g',
        'identity' => $cooltext('Releases the muscle layer, treats stiff neck, generates fluids, vents rashes and raises clear Yang to stop diarrhea.', 'Libera la capa muscular, trata rigidez cervical, genera líquidos, exterioriza erupciones y eleva Yang claro para detener diarrea.'),
        'clue' => $cooltext('Exterior fever with stiff neck and upper back, thirst, incompletely expressed rash or diarrhea.', 'Fiebre exterior con rigidez de cuello y espalda superior, sed, erupción incompleta o diarrea.'),
        'memory' => $cooltext('Kudzu climbs the neck like a vine, then lifts Stomach fluids upward to loosen muscles and quench thirst.', 'El kudzu trepa por el cuello como una enredadera y eleva líquidos de Estómago para soltar músculos y aliviar la sed.'),
        'unique' => $cooltext("Neutral-to-cool and only moderately dispersing in Yang's figure, it is the group's defining muscle-layer, fluid-generating and antidiarrheal herb.", 'De neutra a fresca y solo moderadamente dispersante en Yang, es la hierba distintiva del grupo para capa muscular, generación de líquidos y diarrea.'),
        'formulas' => $cooltext('Chief herb in Ge Gen Tang; co-chief with Chai Hu in Chai Ge Jie Ji Tang. Both are current Board formulas.', 'Hierba principal de Ge Gen Tang; coprincipal con Chai Hu en Chai Ge Jie Ji Tang. Ambas son fórmulas Board vigentes.'),
        'caution' => $cooltext('Use cautiously with excessive sweating or a Cold Stomach with vomiting.', 'Usar con precaución ante sudoración excesiva o Estómago frío con vómitos.'),
    ],
    [
        'name' => 'Chai Hu', 'latin' => 'Bupleuri Radix',
        'properties' => $cooltext('Bitter, acrid; slightly cold', 'Amarga, acre; ligeramente fría'),
        'channels' => $cooltext('Liver, Gallbladder', 'Hígado, Vesícula Biliar'), 'dose' => '3–9 g',
        'identity' => $cooltext('Harmonizes Shaoyang, spreads constrained Liver Qi and raises clear Yang.', 'Armoniza Shaoyang, moviliza Qi de Hígado estancado y eleva Yang claro.'),
        'clue' => $cooltext('Alternating chills and fever, bitter taste and rib-side fullness—or Liver-Qi constraint with emotional symptoms.', 'Alternancia de escalofríos y fiebre, sabor amargo y plenitud costal, o estancamiento de Qi de Hígado con síntomas emocionales.'),
        'memory' => $cooltext('Chai Hu stands at the half-exterior, half-interior doorway and swings it open while lifting clear Yang.', 'Chai Hu se coloca en la puerta entre exterior e interior, la abre y eleva Yang claro.'),
        'unique' => $cooltext("Yang shows strong light dispersion despite a neutral-to-slightly-cold temperature. No other herb in this group combines Shaoyang release, Liver-Qi coursing and clear-Yang lifting so completely.", 'Yang muestra una dispersión ligera potente pese a su temperatura neutra a ligeramente fría. Ninguna otra del grupo combina tan plenamente liberación de Shaoyang, movilización de Qi de Hígado y elevación de Yang claro.'),
        'formulas' => $cooltext('Chief in Xiao Chai Hu Tang and Si Ni San; co-chief with Ge Gen in Chai Ge Jie Ji Tang. It supports, but does not lead, Xiao Yao San.', 'Principal en Xiao Chai Hu Tang y Si Ni San; coprincipal con Ge Gen en Chai Ge Jie Ji Tang. Apoya, pero no dirige, Xiao Yao San.'),
        'caution' => $cooltext('Use cautiously with Liver-Yang rising, Yin deficiency, bleeding or excessive upward movement.', 'Usar con precaución ante ascenso de Yang de Hígado, deficiencia de Yin, sangrado o movimiento ascendente excesivo.'),
    ],
    [
        'name' => 'Sheng Ma', 'latin' => 'Cimicifugae Rhizoma',
        'properties' => $cooltext('Acrid, sweet; slightly cold', 'Acre, dulce; ligeramente fría'),
        'channels' => $cooltext('Lung, Spleen, Stomach, Large Intestine', 'Pulmón, Bazo, Estómago, Intestino Grueso'), 'dose' => '3–9 g',
        'identity' => $cooltext('Disperses Wind-Heat, vents rashes, clears toxicity and raises the clear Yang of the Spleen and Stomach.', 'Dispersa Viento-Calor, exterioriza erupciones, aclara toxicidad y eleva Yang claro de Bazo y Estómago.'),
        'clue' => $cooltext('Yangming headache or toxic swelling plus an incompletely expressed rash, prolapse or chronic sinking.', 'Cefalea Yangming o inflamación tóxica junto con erupción incompleta, prolapso o hundimiento crónico.'),
        'memory' => $cooltext('Sheng Ma is an elevator with a detoxification fan: it raises what has sunk and vents what is trapped.', 'Sheng Ma es un ascensor con ventilador desintoxicante: eleva lo hundido y exterioriza lo atrapado.'),
        'unique' => $cooltext("Among Yang's least cold and less dispersing herbs, it uniquely couples Yangming Heat-toxin clearing with strong clear-Yang elevation.", 'Entre las menos frías y menos dispersantes de Yang, une de forma única la eliminación de Calor-Toxina Yangming con una potente elevación de Yang claro.'),
        'formulas' => $cooltext('Supporting lifting ingredient in Bu Zhong Yi Qi Tang; Huang Qi is the chief of that current Board formula.', 'Ingrediente ascendente de apoyo en Bu Zhong Yi Qi Tang; Huang Qi es la hierba principal de esa fórmula Board vigente.'),
        'caution' => $cooltext('Use cautiously with Yin deficiency, rising Yang, wheezing or rebellious Qi.', 'Usar con precaución ante deficiencia de Yin, ascenso de Yang, sibilancias o Qi rebelde.'),
    ],
];

$coolannex = [
    ['name' => 'Dan Dou Chi', 'latin' => 'Sojae Semen preparatum', 'common' => $cooltext('Prepared Soybean', 'Soja preparada'), 'note' => $cooltext('Gently releases the Exterior and relieves restlessness. It appears in the official 2026 Appendix A, but is not named in this category module title.', 'Libera suavemente el Exterior y alivia inquietud. Aparece en el Apéndice A oficial 2026, pero no está nombrada en el título de este módulo.')],
    ['name' => 'Mu Zei', 'latin' => 'Equiseti hiemalis Herba', 'common' => $cooltext('Scouring Rush', 'Cola de caballo áspera'), 'note' => $cooltext('Disperses Wind-Heat and reduces superficial visual obstruction. It appears in the official 2026 Appendix A, but is retained here as annex recognition.', 'Dispersa Viento-Calor y reduce obstrucción visual superficial. Aparece en el Apéndice A oficial 2026, pero se conserva aquí como reconocimiento de anexo.')],
];

$coolcharts = [
    ['title' => $cooltext('Relative coldness', 'Frialdad relativa'), 'figure' => 'Yang Fig. 2.6', 'items' => ['Bo He' => 100, 'Niu Bang Zi' => 100, 'Sang Ye' => 80, 'Chan Tui' => 40, 'Ju Hua' => 40, 'Sheng Ma' => 20, 'Man Jing Zi' => 20]],
    ['title' => $cooltext('Light and dispersing quality', 'Cualidad ligera y dispersante'), 'figure' => 'Yang Fig. 2.7', 'items' => ['Bo He' => 100, 'Chan Tui' => 80, 'Sang Ye' => 80, 'Chai Hu' => 80, 'Ju Hua' => 60, 'Sheng Ma' => 40, 'Ge Gen' => 40]],
];

$cooldecisions = [
    [$cooltext('Head + throat + Liver constraint', 'Cabeza + garganta + estancamiento de Hígado'), 'Bo He'],
    [$cooltext('Swollen throat + constipation', 'Garganta inflamada + estreñimiento'), 'Niu Bang Zi'],
    [$cooltext('Itching + voice + spasms', 'Prurito + voz + espasmos'), 'Chan Tui'],
    [$cooltext('Dry cough + Lung dryness', 'Tos seca + sequedad de Pulmón'), 'Sang Ye'],
    [$cooltext('Liver Heat + red eyes', 'Calor de Hígado + ojos rojos'), 'Ju Hua'],
    [$cooltext('Headache and eye Wind-Heat', 'Cefalea y Viento-Calor ocular'), 'Man Jing Zi'],
    [$cooltext('Stiff neck + thirst or diarrhea', 'Rigidez cervical + sed o diarrea'), 'Ge Gen'],
    [$cooltext('Alternating chills and fever', 'Alternancia de frío y fiebre'), 'Chai Hu'],
    [$cooltext('Toxicity + prolapse or sinking', 'Toxicidad + prolapso o hundimiento'), 'Sheng Ma'],
];

$coolcomparisons = [
    ['title' => 'Bo He vs. Niu Bang Zi', 'summary' => $cooltext('Bo He is lighter, aromatic and moves Liver Qi; Niu Bang Zi is heavier, benefits the throat and moistens the bowel.', 'Bo He es más ligera, aromática y moviliza Qi de Hígado; Niu Bang Zi es más pesada, beneficia la garganta y humedece el intestino.'), 'path' => '/study-guide/comparisons/bo-he-vs-niu-bang-zi.html', 'path_es' => '/study-guide/comparisons/bo-he-vs-niu-bang-zi-es.html'],
    ['title' => 'Sang Ye vs. Ju Hua', 'summary' => $cooltext('Lung dryness and dry cough favor Sang Ye; Liver and eye findings favor Ju Hua.', 'Sequedad de Pulmón y tos seca favorecen Sang Ye; hallazgos de Hígado y ojos favorecen Ju Hua.'), 'path' => '/study-guide/comparisons/sang-ye-vs-ju-hua.html', 'path_es' => '/study-guide/comparisons/sang-ye-vs-ju-hua-es.html'],
    ['title' => 'Ge Gen vs. Sheng Ma vs. Chai Hu', 'summary' => $cooltext('Ge Gen targets Yangming muscles and fluids; Sheng Ma raises Spleen Qi and clears toxicity; Chai Hu releases Shaoyang and courses Liver Qi.', 'Ge Gen actúa en músculos y líquidos Yangming; Sheng Ma eleva Qi de Bazo y aclara toxicidad; Chai Hu libera Shaoyang y moviliza Qi de Hígado.'), 'path' => '/study-guide/comparisons/ge-gen-vs-sheng-ma-vs-chai-hu.html', 'path_es' => '/study-guide/comparisons/ge-gen-vs-sheng-ma-vs-chai-hu-es.html'],
    ['title' => 'Chan Tui vs. Bai Jiang Can', 'summary' => $cooltext('Chan Tui is lighter for superficial rash, voice and eyes; Bai Jiang Can is stronger for Phlegm, nodules and spasms.', 'Chan Tui es más ligera para erupción superficial, voz y ojos; Bai Jiang Can es más potente para Flema, nódulos y espasmos.'), 'path' => '/study-guide/comparisons/chan-tui-vs-bai-jiang-can.html'],
];

$coolquestions = [
    ['question' => $cooltext('How do Bo He and Niu Bang Zi differ although both disperse Wind-Heat?', '¿Cómo se diferencian Bo He y Niu Bang Zi aunque ambas dispersan Viento-Calor?'), 'answer' => $cooltext('Bo He is light, aromatic and sweat-inducing, with special affinity for the head, eyes, throat and constrained Liver Qi. Niu Bang Zi is a heavy, oily seed that benefits a swollen throat, resolves toxicity, vents rashes and moistens the bowel. Think upward aromatic dispersion versus outward-and-downward drainage.', 'Bo He es ligera, aromática e induce sudoración, con afinidad por cabeza, ojos, garganta y Qi de Hígado estancado. Niu Bang Zi es una semilla pesada y oleosa que beneficia garganta inflamada, resuelve toxicidad, exterioriza erupciones y humedece el intestino. Piensa en dispersión aromática ascendente frente a drenaje hacia afuera y abajo.')],
    ['question' => $cooltext('How do Bo He, Chan Tui and Niu Bang Zi differ in itchy skin disease?', '¿Cómo se diferencian Bo He, Chan Tui y Niu Bang Zi en enfermedades cutáneas con prurito?'), 'answer' => $cooltext('Bo He fits acute Wind-Heat with fever, dry throat or rash. Chan Tui is exceptionally light and gentle, linking itching with hoarseness, red eyes or pediatric spasms. Niu Bang Zi is the coldest choice when lesions are red, burning or painful and constipation suggests Heat in the Yangming.', 'Bo He corresponde a Viento-Calor agudo con fiebre, garganta seca o erupción. Chan Tui es muy ligera y suave, y conecta prurito con ronquera, ojos rojos o espasmos pediátricos. Niu Bang Zi es la opción más fría cuando las lesiones son rojas, ardientes o dolorosas y el estreñimiento sugiere Calor en Yangming.')],
    ['question' => $cooltext('How do Bo He, Ju Hua, Sang Ye and Chai Hu differ in Upper-Jiao Wind-Heat?', '¿Cómo se diferencian Bo He, Ju Hua, Sang Ye y Chai Hu en Viento-Calor del Jiao Superior?'), 'answer' => $cooltext('Bo He is the coldest, most aromatic and most dispersing for head, throat and Liver-Qi constraint. Ju Hua emphasizes Liver Heat, eyes and Yang rising. Sang Ye emphasizes Lung dryness and dry cough while also calming the Liver. Chai Hu releases Shaoyang, shown by alternating chills and fever, bitter taste and rib-side fullness.', 'Bo He es la más fría, aromática y dispersante para cabeza, garganta y estancamiento de Qi de Hígado. Ju Hua enfatiza Calor de Hígado, ojos y ascenso de Yang. Sang Ye enfatiza sequedad de Pulmón y tos seca mientras calma el Hígado. Chai Hu libera Shaoyang, indicado por alternancia de frío y fiebre, sabor amargo y plenitud costal.')],
    ['question' => $cooltext('How do Ge Gen, Sheng Ma and Chai Hu differ when all can release the Exterior and raise Yang?', '¿Cómo se diferencian Ge Gen, Sheng Ma y Chai Hu si las tres liberan el Exterior y elevan Yang?'), 'answer' => $cooltext('Ge Gen enters Yangming and the muscle layer, treats stiff neck, raises Stomach fluids and can stop diarrhea. Sheng Ma raises Spleen-Stomach clear Yang while clearing Yangming toxicity and venting rashes. Chai Hu releases Shaoyang, courses Liver Qi and raises clear Yang from a Liver-Gallbladder axis.', 'Ge Gen entra en Yangming y la capa muscular, trata rigidez cervical, eleva líquidos de Estómago y puede detener diarrea. Sheng Ma eleva Yang claro de Bazo-Estómago mientras aclara toxicidad Yangming y exterioriza erupciones. Chai Hu libera Shaoyang, moviliza Qi de Hígado y eleva Yang claro desde el eje Hígado-Vesícula Biliar.')],
    ['question' => $cooltext('How should the different Ju Hua varieties be distinguished for review?', '¿Cómo deben distinguirse las variedades de Ju Hua para el repaso?'), 'answer' => $cooltext('Yellow Ju Hua emphasizes dispersing Wind-Heat from the Lung and Upper Jiao. White Ju Hua is sweeter and emphasizes nourishing, calming the Liver and benefiting the eyes. Ye Ju Hua is more bitter and stronger for Heat-toxin, sores and swellings; it is a separate Appendix A herb and not part of this category module.', 'Ju Hua amarilla enfatiza dispersar Viento-Calor del Pulmón y Jiao Superior. Ju Hua blanca es más dulce y enfatiza nutrir, calmar el Hígado y beneficiar los ojos. Ye Ju Hua es más amarga y potente para Calor-Toxina, llagas e inflamaciones; es una hierba separada del Apéndice A y no forma parte de este módulo.')],
];
?>
<section aria-labelledby="topic-two-foundations">
    <h4 id="topic-two-foundations"><?php echo s($cooltext('Wind-Heat Exterior-releasing herbs: general introduction', 'Hierbas que liberan Viento-Calor del Exterior: introducción general')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($cooltext('These herbs are generally acrid and cool. They disperse Wind-Heat from the superficial layer and often reach the Lung, head, eyes, throat and skin.', 'Estas hierbas suelen ser acres y frescas. Dispersan Viento-Calor de la capa superficial y con frecuencia alcanzan Pulmón, cabeza, ojos, garganta y piel.')); ?></p>
    <div class="single-herbs-guide__pattern">
        <article><strong><?php echo s($cooltext('Typical presentation', 'Presentación típica')); ?></strong><span><?php echo s($cooltext('Fever greater than chills, thirst, sore throat, headache, red eyes, cough or an emerging rash.', 'Fiebre mayor que los escalofríos, sed, dolor de garganta, cefalea, ojos rojos, tos o erupción emergente.')); ?></span></article>
        <article><strong><?php echo s($cooltext('Primary focus', 'Foco principal')); ?></strong><span><?php echo s($cooltext('The Upper Jiao and superficial layer. The goal is light dispersion and Heat clearing—not forceful sweating.', 'El Jiao Superior y la capa superficial. La meta es dispersión ligera y aclarar Calor, no sudoración forzada.')); ?></span></article>
        <article><strong><?php echo s($cooltext('Board distinction', 'Distinción para Board')); ?></strong><span><?php echo s($cooltext('Separate throat, rash, dry cough, eye, stiff-neck, Shaoyang and sinking-Qi clues before choosing the herb.', 'Separa las pistas de garganta, erupción, tos seca, ojos, rigidez cervical, Shaoyang y hundimiento de Qi antes de elegir.')); ?></span></article>
    </div>
    <p class="single-herbs-guide__scope-note"><strong><?php echo s($cooltext('General precautions:', 'Precauciones generales:')); ?></strong> <?php echo s($cooltext('these ascending and dispersing herbs can consume Qi and fluids when overused. Adjust for constitution, season and severity, and use caution with deficiency, spontaneous sweating, bleeding or pregnancy.', 'estas hierbas ascendentes y dispersantes pueden consumir Qi y líquidos si se usan en exceso. Ajusta según constitución, estación y gravedad, y usa precaución ante deficiencia, sudoración espontánea, sangrado o embarazo.')); ?></p>
    <aside class="single-herbs-guide__memory-path" aria-label="<?php echo s($cooltext('Wind-Heat house memory path', 'Recorrido mnemotécnico de la casa de Viento-Calor')); ?>">
        <p class="single-herbs-guide__memory-line"><?php echo s($cooltext('Return to the Exterior house—now summer heat has entered upstairs.', 'Regresa a la casa del Exterior: ahora el calor de verano entró al piso superior.')); ?></p>
        <p><?php echo s($cooltext('Bo He opens the windows; Niu Bang Zi clears the throat and hallway; Chan Tui sheds the curtains; Sang Ye moistens the dry Lung room; Ju Hua cools the eye lamps; Man Jing Zi reaches the roof; Ge Gen loosens the stiff staircase; Chai Hu opens the half-inside door; Sheng Ma runs the elevator upward.', 'Bo He abre las ventanas; Niu Bang Zi despeja la garganta y el pasillo; Chan Tui muda las cortinas; Sang Ye humedece el cuarto seco del Pulmón; Ju Hua enfría las lámparas de los ojos; Man Jing Zi llega al techo; Ge Gen afloja la escalera rígida; Chai Hu abre la puerta medio interior; Sheng Ma sube el ascensor.')); ?></p>
        <p><strong><?php echo s($cooltext('Acrid disperses, Cool clears, and light herbs rise to the head and Upper Jiao.', 'Lo acre dispersa, lo fresco aclara y las hierbas ligeras suben a la cabeza y al Jiao Superior.')); ?></strong></p>
    </aside>

    <h5 class="single-herbs-guide__catalog-heading"><?php echo s($cooltext('Current Board category herbs', 'Hierbas de la categoría Board vigente')); ?></h5>
    <p class="single-herbs-guide__catalog-intro"><?php echo s($cooltext('Study these nine named herbs first. The annex does not enlarge this module boundary.', 'Estudia primero estas nueve hierbas nombradas. El anexo no amplía el límite de este módulo.')); ?></p>
    <ol class="single-herbs-guide__category-herbs">
        <?php foreach ($coolherbs as $categoryherb): ?>
            <li class="single-herbs-guide__category-herb single-herbs-guide__category-herb--core">
                <div class="single-herbs-guide__category-herb-head"><strong><?php echo s($categoryherb['name']); ?></strong><em><?php echo s($categoryherb['latin']); ?></em><span class="single-herbs-guide__category-herb-badge"><?php echo s($cooltext('In Board category list', 'En el listado de la categoría Board')); ?></span></div>
                <p><?php echo s($categoryherb['identity']); ?></p>
            </li>
        <?php endforeach; ?>
    </ol>
    <div class="single-herbs-guide__annex">
        <h5 class="single-herbs-guide__catalog-heading"><?php echo s($cooltext('Annex · Additional official recognition herbs', 'Anexo · Hierbas oficiales adicionales para reconocimiento')); ?></h5>
        <p class="single-herbs-guide__catalog-intro"><?php echo s($cooltext('These herbs remain in the official Appendix A but are not mixed into the nine-herb study sequence above.', 'Estas hierbas permanecen en el Apéndice A oficial, pero no se mezclan con la secuencia de nueve hierbas anterior.')); ?></p>
        <ol class="single-herbs-guide__category-herbs">
            <?php foreach ($coolannex as $categoryherb): ?>
                <li class="single-herbs-guide__category-herb"><div class="single-herbs-guide__category-herb-head"><strong><?php echo s($categoryherb['name']); ?></strong><em><?php echo s($categoryherb['latin']); ?></em><span class="single-herbs-guide__category-herb-badge single-herbs-guide__category-herb-badge--outside"><?php echo s($cooltext('Annex recognition', 'Reconocimiento de anexo')); ?></span></div><span class="single-herbs-guide__category-herb-common"><?php echo s($categoryherb['common']); ?></span><p><?php echo s($categoryherb['note']); ?></p></li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-two-yang-title">
    <h4 id="topic-two-yang-title"><?php echo s($cooltext('Yang comparative fingerprint', 'Huella comparativa de Yang')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($cooltext('The two Chapter 2 figures reveal the category at a glance: coldness is not the same as light dispersion.', 'Las dos figuras del capítulo 2 revelan la categoría de un vistazo: frialdad no es lo mismo que dispersión ligera.')); ?></p>
    <div class="single-herbs-guide__yang-charts">
        <?php foreach ($coolcharts as $chart): ?><article class="single-herbs-guide__yang-chart"><h5><?php echo s($chart['title']); ?></h5><small><?php echo s($chart['figure']); ?></small><?php foreach ($chart['items'] as $herbname => $value): ?><div class="single-herbs-guide__yang-bar"><span><?php echo s($herbname); ?></span><span class="single-herbs-guide__yang-bar-track"><span class="single-herbs-guide__yang-bar-fill" style="width:<?php echo (int) $value; ?>%"></span></span><b><?php echo (int) $value; ?></b></div><?php endforeach; ?></article><?php endforeach; ?>
    </div>
    <p class="single-herbs-guide__yang-note"><strong><?php echo s($cooltext('Reading rule:', 'Regla de lectura:')); ?></strong> <?php echo s($cooltext('these are relative indices transcribed from Yang, Figures 2.6 and 2.7—not doses or efficacy percentages. Bo He and Niu Bang Zi are equally cold, but Bo He is much lighter and more dispersing.', 'son índices relativos transcritos de Yang, figuras 2.6 y 2.7; no son dosis ni porcentajes de eficacia. Bo He y Niu Bang Zi son igualmente frías, pero Bo He es mucho más ligera y dispersante.')); ?></p>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-two-decision-title">
    <h4 id="topic-two-decision-title"><?php echo s($cooltext('Fast decision map', 'Mapa de decisión rápida')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($cooltext('Find the dominant clue first; then confirm temperature, channel and secondary action.', 'Encuentra primero la pista dominante; luego confirma temperatura, canal y acción secundaria.')); ?></p>
    <div class="single-herbs-guide__decision"><?php foreach ($cooldecisions as $decision): ?><div><strong><?php echo s($decision[0]); ?></strong><span><?php echo s($decision[1]); ?></span></div><?php endforeach; ?></div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-two-profiles-title">
    <h4 id="topic-two-profiles-title"><?php echo s($cooltext('Individual herb profiles', 'Perfiles individuales de las hierbas')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($cooltext('Each profile joins Bensky fundamentals, a Yang distinction, a memory image and only current Board-formula links.', 'Cada perfil une fundamentos de Bensky, una distinción de Yang, una imagen mnemotécnica y solamente enlaces con fórmulas Board vigentes.')); ?></p>
    <div class="single-herbs-guide__profiles">
        <?php foreach ($coolherbs as $herb): ?><details class="single-herbs-guide__profile"<?php echo $herb['name'] === 'Bo He' ? ' open' : ''; ?>><summary><span class="single-herbs-guide__profile-name"><?php echo s($herb['name']); ?></span><span class="single-herbs-guide__profile-latin"><?php echo s($herb['latin']); ?></span><span class="single-herbs-guide__profile-meta"><?php echo s($herb['properties']); ?> · <?php echo s($herb['channels']); ?></span></summary><div class="single-herbs-guide__profile-body"><dl>
            <dt><?php echo s($cooltext('Dose', 'Dosis')); ?></dt><dd><?php echo s($herb['dose']); ?></dd>
            <dt><?php echo s($cooltext('Identity', 'Identidad')); ?></dt><dd><?php echo s($herb['identity']); ?></dd>
            <dt><?php echo s($cooltext('Exam clue', 'Pista de examen')); ?></dt><dd><?php echo s($herb['clue']); ?></dd>
            <dt><?php echo s($cooltext('Memory', 'Memoria')); ?></dt><dd class="single-herbs-guide__profile-memory"><?php echo s($herb['memory']); ?></dd>
            <dt><?php echo s($cooltext('Unique fingerprint', 'Huella única')); ?></dt><dd><?php echo s($herb['unique']); ?></dd>
            <dt><?php echo s($cooltext('Formula bridge', 'Puente con fórmulas')); ?></dt><dd class="single-herbs-guide__profile-formulas"><?php echo s($herb['formulas']); ?></dd>
            <dt><?php echo s($cooltext('Safety', 'Seguridad')); ?></dt><dd class="single-herbs-guide__profile-safety"><?php echo s($herb['caution']); ?></dd>
        </dl></div></details><?php endforeach; ?>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-two-formulas-title">
    <h4 id="topic-two-formulas-title"><?php echo s($cooltext('When the herb leads the formula', 'Cuando la hierba dirige la fórmula')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($cooltext('Only chief or co-chief connections within the current 63-formula Board roster appear here.', 'Aquí aparecen solamente relaciones principales o coprincipales dentro del listado Board vigente de 63 fórmulas.')); ?></p>
    <div class="single-herbs-guide__formula-leads">
        <article class="single-herbs-guide__formula-lead"><h5>Sang Ye + Ju Hua</h5><p><strong><?php echo s($cooltext('Co-chief pair:', 'Pareja coprincipal:')); ?></strong> Sang Ju Yin.</p></article>
        <article class="single-herbs-guide__formula-lead"><h5>Ge Gen</h5><p><strong><?php echo s($cooltext('Chief:', 'Principal:')); ?></strong> Ge Gen Tang. <strong><?php echo s($cooltext('Co-chief:', 'Coprincipal:')); ?></strong> Chai Ge Jie Ji Tang.</p></article>
        <article class="single-herbs-guide__formula-lead"><h5>Chai Hu</h5><p><strong><?php echo s($cooltext('Chief:', 'Principal:')); ?></strong> Xiao Chai Hu Tang; Si Ni San. <strong><?php echo s($cooltext('Co-chief:', 'Coprincipal:')); ?></strong> Chai Ge Jie Ji Tang.</p></article>
    </div>
    <p class="single-herbs-guide__scope-note"><strong><?php echo s($cooltext('Role check:', 'Verificación de función:')); ?></strong> <?php echo s($cooltext('Bo He and Niu Bang Zi support Yin Qiao San; Sheng Ma supports Bu Zhong Yi Qi Tang. They are important connections, but those herbs do not lead the formulas.', 'Bo He y Niu Bang Zi apoyan Yin Qiao San; Sheng Ma apoya Bu Zhong Yi Qi Tang. Son conexiones importantes, pero esas hierbas no dirigen las fórmulas.')); ?></p>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-two-comparisons-title">
    <h4 id="topic-two-comparisons-title"><?php echo s($cooltext('High-yield comparisons', 'Comparaciones de alto rendimiento')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($cooltext('Use the decisive clue before opening the full comparison.', 'Usa la pista decisiva antes de abrir la comparación completa.')); ?></p>
    <div class="single-herbs-guide__comparisons">
        <?php foreach ($coolcomparisons as $comparison): ?><?php $comparisonpath = $ises && !empty($comparison['path_es']) ? $comparison['path_es'] : $comparison['path']; $comparisonurl = new moodle_url($comparisonpath, ['from' => 'single-herbs', 'focus' => (string) $selected['number']]); ?><a class="single-herbs-guide__comparison" href="<?php echo $comparisonurl->out(false); ?>"><strong><?php echo s($comparison['title']); ?></strong><span><?php echo s($comparison['summary']); ?></span><small><?php echo s($cooltext('Open full comparison →', 'Abrir comparación completa →')); ?></small></a><?php endforeach; ?>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-two-yang-questions-title">
    <h4 id="topic-two-yang-questions-title"><?php echo s($cooltext('Yang questions for this category', 'Preguntas de Yang para esta categoría')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($cooltext('One reversible deck holds the selected Chapter 2 questions. References outside the module are identified in the answer.', 'Una sola baraja reversible contiene las preguntas seleccionadas del capítulo 2. Las referencias fuera del módulo se identifican en la respuesta.')); ?></p>
    <div class="single-herbs-guide__yang-deck" data-yang-deck data-card-label="<?php echo s($cooltext('Card', 'Tarjeta')); ?>" data-of-label="<?php echo s($cooltext('of', 'de')); ?>">
        <?php foreach ($coolquestions as $questionindex => $yangquestion): ?><article class="single-herbs-guide__yang-card<?php echo $questionindex === 0 ? ' is-active' : ''; ?>" data-yang-card aria-hidden="<?php echo $questionindex === 0 ? 'false' : 'true'; ?>"><div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--front"><span class="single-herbs-guide__yang-label"><?php echo s($cooltext('Question', 'Pregunta')); ?> <?php echo $questionindex + 1; ?> <?php echo s($cooltext('of', 'de')); ?> <?php echo count($coolquestions); ?> · Yang, <?php echo s($cooltext('Chapter', 'capítulo')); ?> 2</span><p class="single-herbs-guide__yang-question"><?php echo s($yangquestion['question']); ?></p></div><div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--back"><span class="single-herbs-guide__yang-label"><?php echo s($cooltext('Answer', 'Respuesta')); ?> <?php echo $questionindex + 1; ?> <?php echo s($cooltext('of', 'de')); ?> <?php echo count($coolquestions); ?></span><p class="single-herbs-guide__yang-answer"><?php echo s($yangquestion['answer']); ?></p></div></article><?php endforeach; ?>
        <div class="single-herbs-guide__yang-controls" aria-label="<?php echo s($cooltext('Yang study card controls', 'Controles de tarjetas de Yang')); ?>"><button class="single-herbs-guide__yang-control" type="button" data-yang-prev>← <?php echo s($cooltext('Previous', 'Anterior')); ?></button><button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--flip" type="button" data-yang-flip data-show-label="<?php echo s($cooltext('Show answer', 'Mostrar respuesta')); ?>" data-hide-label="<?php echo s($cooltext('Show question', 'Mostrar pregunta')); ?>"><?php echo s($cooltext('Show answer', 'Mostrar respuesta')); ?></button><button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--next" type="button" data-yang-next><?php echo s($cooltext('Next', 'Siguiente')); ?> →</button></div>
        <p class="single-herbs-guide__yang-progress" data-yang-progress><?php echo s($cooltext('Card', 'Tarjeta')); ?> 1 <?php echo s($cooltext('of', 'de')); ?> <?php echo count($coolquestions); ?></p>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-two-recall-title">
    <h4 id="topic-two-recall-title"><?php echo s($cooltext('Quick recall', 'Repaso rápido')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($cooltext('Commit to an answer before opening each item.', 'Elige una respuesta antes de abrir cada elemento.')); ?></p>
    <div class="single-herbs-guide__recall">
        <details><summary><?php echo s($cooltext('Which herb best matches Wind-Heat with a dry cough and Lung dryness?', '¿Qué hierba corresponde a Viento-Calor con tos seca y sequedad de Pulmón?')); ?></summary><p><strong>Sang Ye.</strong> <?php echo s($cooltext('It disperses Wind-Heat while moistening Lung dryness.', 'Dispersa Viento-Calor mientras humedece sequedad de Pulmón.')); ?></p></details>
        <details><summary><?php echo s($cooltext('Which herb combines a swollen throat, rash venting and constipation?', '¿Qué hierba combina garganta inflamada, exteriorización de erupción y estreñimiento?')); ?></summary><p><strong>Niu Bang Zi.</strong> <?php echo s($cooltext('Its heavy, oily seed disperses outward and drains downward.', 'Su semilla pesada y oleosa dispersa hacia afuera y drena hacia abajo.')); ?></p></details>
        <details><summary><?php echo s($cooltext('Which herb is selected for stiff neck, thirst or diarrhea?', '¿Qué hierba se elige para rigidez cervical, sed o diarrea?')); ?></summary><p><strong>Ge Gen.</strong> <?php echo s($cooltext('It releases the muscle layer and raises Stomach fluids and clear Yang.', 'Libera la capa muscular y eleva líquidos de Estómago y Yang claro.')); ?></p></details>
        <details><summary><?php echo s($cooltext('Which herb points to alternating chills and fever with rib-side fullness?', '¿Qué hierba indica alternancia de frío y fiebre con plenitud costal?')); ?></summary><p><strong>Chai Hu.</strong> <?php echo s($cooltext('That is the characteristic Shaoyang doorway.', 'Esa es la puerta característica de Shaoyang.')); ?></p></details>
        <details><summary><?php echo s($cooltext('Sang Ye or Ju Hua when red eyes dominate?', '¿Sang Ye o Ju Hua cuando dominan los ojos rojos?')); ?></summary><p><strong>Ju Hua</strong> <?php echo s($cooltext('when Liver Heat, dizziness or Yang rising dominates; choose Sang Ye when Lung dryness and dry cough dominate.', 'cuando predominan Calor de Hígado, mareo o ascenso de Yang; elige Sang Ye cuando predominan sequedad de Pulmón y tos seca.')); ?></p></details>
    </div>
</section>

<p class="single-herbs-guide__note single-herbs-guide__source"><strong><?php echo s($cooltext('Sources:', 'Fuentes:')); ?></strong> Bensky, Clavey, Stöger and Gamble, <em>Chinese Herbal Medicine: Materia Medica</em>, 3rd ed.; Yang, <em>Chinese Herbal Medicines: Comparisons and Characteristics</em>, 2nd ed., Chapter 2; NCBAHM Chinese Herbology Content Outline, effective September 1, 2026. <?php echo s($cooltext('Educational review only; not prescribing guidance.', 'Solo para repaso educativo; no constituye guía de prescripción.')); ?></p>
