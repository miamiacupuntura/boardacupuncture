<?php
// Cumulative, cross-category review for the completed Single Herbs study notes.
defined('MOODLE_INTERNAL') || die();

$mixedtext = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};

$mixedcards = [
    [
        'category' => $mixedtext('Category 1 · Release Wind-Cold', 'Categoría 1 · Liberar Viento-Frío'),
        'question' => $mixedtext('Two Wind-Cold patients both have chills. One has no sweating plus wheezing; the other has spontaneous sweating and a disharmony of the muscle layer. Which herb leads each case?', 'Dos pacientes con Viento-Frío tienen escalofríos. Uno no suda y presenta sibilancia; el otro tiene sudor espontáneo y desarmonía de la capa muscular. ¿Qué hierba dirige cada caso?'),
        'answer' => $mixedtext('Ma Huang leads the closed, no-sweat exterior with wheezing because it strongly induces sweating and disseminates Lung Qi. Gui Zhi leads spontaneous sweating and Ying-Wei/muscle-layer disharmony. Formula bridge: Ma Huang is chief in Ma Huang Tang.', 'Ma Huang dirige el Exterior cerrado sin sudor y con sibilancia porque induce sudor con fuerza y difunde Qi de Pulmón. Gui Zhi dirige sudor espontáneo y desarmonía Ying-Wei/capa muscular. Puente de fórmula: Ma Huang es principal en Ma Huang Tang.'),
    ],
    [
        'category' => $mixedtext('Category 2 · Release Wind-Heat', 'Categoría 2 · Liberar Viento-Calor'),
        'question' => $mixedtext('A Wind-Heat case has a swollen toxic sore throat, cough with Phlegm and constipation. Another has heat in the face and eyes with constrained Liver Qi. Niu Bang Zi or Bo He?', 'Un caso de Viento-Calor presenta garganta tóxica e hinchada, tos con Flema y estreñimiento. Otro tiene calor en cara y ojos con Qi de Hígado estancado. ¿Niu Bang Zi o Bo He?'),
        'answer' => $mixedtext('Niu Bang Zi fits toxic throat swelling, Phlegm and constipation: it is bitter, slippery and stronger for toxicity. Bo He fits face/eye Heat and Liver-Qi constraint; its aromatic, rising action also vents rashes.', 'Niu Bang Zi corresponde a hinchazón tóxica de garganta, Flema y estreñimiento: es amarga, resbaladiza y más fuerte para toxicidad. Bo He corresponde a Calor de cara/ojos y estancamiento de Qi de Hígado; su acción aromática ascendente también ventila erupciones.'),
    ],
    [
        'category' => $mixedtext('Category 3 · Clear Heat and Drain Fire', 'Categoría 3 · Aclarar Calor y drenar Fuego'),
        'question' => $mixedtext('High fever, profuse sweat, intense thirst and a flooding pulse demand one mineral. Persistent Heat with injured Yin, dryness and deficiency signs favors its frequent partner. Identify both.', 'Fiebre alta, sudor profuso, sed intensa y pulso desbordante exigen un mineral. Calor persistente con Yin lesionado, sequedad y signos de deficiencia favorece a su compañera frecuente. Identifica ambas.'),
        'answer' => $mixedtext('Shi Gao is the very cold, acrid-sweet mineral for blazing Qi-level Heat and great thirst. Zhi Mu is bitter-sweet and cold; it clears Heat while enriching Yin and moistening dryness. Think: Shi Gao extinguishes the blaze; Zhi Mu protects the fluid reservoir.', 'Shi Gao es el mineral muy frío, picante-dulce para Calor ardiente en nivel Qi y sed intensa. Zhi Mu es amarga-dulce y fría; aclara Calor mientras enriquece Yin y humedece sequedad. Piensa: Shi Gao apaga el incendio; Zhi Mu protege el depósito de líquidos.'),
    ],
    [
        'category' => $mixedtext('Category 4 · Cool the Blood', 'Categoría 4 · Enfriar la Sangre'),
        'question' => $mixedtext('Both herbs cool Blood and nourish Yin. Which one is more centered on fresh Blood-Heat with bleeding, and which one adds toxic sore throat, nodules and softening hardness?', 'Ambas hierbas enfrían Sangre y nutren Yin. ¿Cuál se centra más en Calor de Sangre fresco con sangrado y cuál añade garganta tóxica, nódulos y ablandar durezas?'),
        'answer' => $mixedtext('Sheng Di Huang is the core choice for Blood Heat, bleeding, fever and injured fluids. Xuan Shen is salty-bitter and excels when Fire-toxicity attacks the throat or when nodules/hardness must be softened.', 'Sheng Di Huang es la opción central para Calor de Sangre, sangrado, fiebre y líquidos lesionados. Xuan Shen es salada-amarga y destaca cuando Fuego-tóxico ataca la garganta o hay nódulos/durezas que ablandar.'),
    ],
    [
        'category' => $mixedtext('Category 5 · Clear Heat and Dry Damp', 'Categoría 5 · Aclarar Calor y secar Humedad'),
        'question' => $mixedtext('Match the San Huang and Long Dan Cao to their strongest Board location: Upper Burner/Lung, Middle Burner/Stomach-Heart, Lower Burner/Kidney, or Liver-Gallbladder Damp-Heat.', 'Relaciona los San Huang y Long Dan Cao con su localización Board más fuerte: Jiao Superior/Pulmón, Jiao Medio/Estómago-Corazón, Jiao Inferior/Riñón o Humedad-Calor de Hígado-Vesícula.'),
        'answer' => $mixedtext('Huang Qin: Upper Burner and Lung Heat. Huang Lian: Middle Burner plus Heart/Stomach Fire. Huang Bai: Lower Burner, Kidney deficiency Fire and Damp-Heat below. Long Dan Cao: strongest bitter-cold drain for Liver-Gallbladder Fire and Damp-Heat.', 'Huang Qin: Jiao Superior y Calor de Pulmón. Huang Lian: Jiao Medio más Fuego de Corazón/Estómago. Huang Bai: Jiao Inferior, Fuego deficiente de Riñón y Humedad-Calor abajo. Long Dan Cao: drenaje amargo-frío más fuerte para Fuego y Humedad-Calor de Hígado-Vesícula.'),
    ],
    [
        'category' => $mixedtext('Category 6 · Clear Heat and Resolve Toxicity', 'Categoría 6 · Aclarar Calor y resolver toxicidad'),
        'question' => $mixedtext('Choose among Jin Yin Hua, Lian Qiao and Pu Gong Ying: early warm disease with broad toxicity; toxicity with Heart/upper-burner Heat and nodules; breast abscess with Liver/Stomach Heat.', 'Elige entre Jin Yin Hua, Lian Qiao y Pu Gong Ying: enfermedad tibia inicial con toxicidad amplia; toxicidad con Calor de Corazón/Jiao Superior y nódulos; absceso mamario con Calor de Hígado/Estómago.'),
        'answer' => $mixedtext('Jin Yin Hua is broad, sweet-cold and especially useful early in warm disease. Lian Qiao clears Heart/upper-burner Heat, disperses clumps and is the “sore toxin” partner. Pu Gong Ying is the breast-abscess specialist and also benefits urination.', 'Jin Yin Hua es amplia, dulce-fría y especialmente útil al inicio de enfermedad tibia. Lian Qiao aclara Corazón/Jiao Superior, dispersa acumulaciones y es la compañera de “toxina con llaga”. Pu Gong Ying es especialista en absceso mamario y también beneficia la orina.'),
    ],
    [
        'category' => $mixedtext('Category 7 · Clear Deficiency Heat', 'Categoría 7 · Aclarar Calor por deficiencia'),
        'question' => $mixedtext('Night fever without sweating, postpartum deficiency Heat, and steaming-bone fever each point in a different direction. Distinguish Qing Hao, Bai Wei and Di Gu Pi.', 'Fiebre nocturna sin sudor, Calor por deficiencia posparto y fiebre de huesos humeantes apuntan en direcciones distintas. Distingue Qing Hao, Bai Wei y Di Gu Pi.'),
        'answer' => $mixedtext('Qing Hao vents lurking Heat from the Yin level and is classic for night fever without sweating. Bai Wei cools Blood and is especially associated with postpartum deficiency Heat. Di Gu Pi treats steaming-bone disorder and also clears Lung Heat.', 'Qing Hao ventila Calor oculto del nivel Yin y es clásica para fiebre nocturna sin sudor. Bai Wei enfría Sangre y se asocia especialmente con Calor por deficiencia posparto. Di Gu Pi trata huesos humeantes y también aclara Calor de Pulmón.'),
    ],
    [
        'category' => $mixedtext('Category 8 · Clear Summer Heat', 'Categoría 8 · Aclarar Calor de verano'),
        'question' => $mixedtext('Summer Heat causes fever, thirst and scant urine. Which current herb cools and generates fluids through the Lung/Stomach, and which fruit peel is the lighter food-like option for thirst and urination?', 'Calor de verano causa fiebre, sed y orina escasa. ¿Qué hierba actual enfría y genera líquidos por Pulmón/Estómago y qué cáscara de fruta es la opción más ligera y alimentaria para sed y orina?'),
        'answer' => $mixedtext('Lu Gen clears Lung/Stomach Heat, generates fluids and can stop vomiting. Xi Gua Pi lightly clears Summer Heat, relieves thirst and promotes urination. Severe collapse from heat is an emergency, not a self-treatment scenario.', 'Lu Gen aclara Calor de Pulmón/Estómago, genera líquidos y puede detener vómito. Xi Gua Pi aclara suavemente Calor de verano, alivia sed y promueve orina. Colapso grave por calor es una emergencia, no escenario de autotratamiento.'),
    ],
    [
        'category' => $mixedtext('Category 9 · Downward-Draining Purgatives', 'Categoría 9 · Purgantes de drenaje descendente'),
        'question' => $mixedtext('A Heat-accumulation constipation case has a hard, dry focal mass. Which herb breaks accumulation and which salt softens hardness by drawing water into the bowel?', 'Un estreñimiento por acumulación de Calor presenta masa focal dura y seca. ¿Qué hierba rompe acumulación y qué sal ablanda dureza atrayendo agua al intestino?'),
        'answer' => $mixedtext('Da Huang bitter-coldly attacks accumulation, drains Fire and invigorates Blood. Mang Xiao is the salty-cold softener for very hard, dry stool. These are short-course Excess treatments; deficiency constipation needs another strategy.', 'Da Huang ataca acumulación con amargor/frío, drena Fuego y vigoriza Sangre. Mang Xiao es la salada-fría que ablanda heces muy duras y secas. Son tratamientos breves de Exceso; estreñimiento por deficiencia requiere otra estrategia.'),
    ],
    [
        'category' => $mixedtext('Category 10 · Moist Laxatives', 'Categoría 10 · Laxantes humectantes'),
        'question' => $mixedtext('Both seeds moisten dry constipation. Which favors weak, elderly, postpartum or post-febrile patients, and which adds edema, Qi fullness or difficult urination?', 'Ambas semillas humedecen estreñimiento seco. ¿Cuál favorece pacientes débiles, ancianos, posparto o posfebriles y cuál añade edema, plenitud de Qi u orina difícil?'),
        'answer' => $mixedtext('Huo Ma Ren more clearly nourishes and moistens deficiency Dryness. Yu Li Ren moistens while moving Qi and promoting urination, so it fits constipation with water retention or edema.', 'Huo Ma Ren nutre y humedece con mayor claridad la Sequedad por deficiencia. Yu Li Ren humedece mientras mueve Qi y promueve orina, por lo que corresponde a estreñimiento con retención de agua o edema.'),
    ],
    [
        'category' => $mixedtext('Category 11 · Harsh Expellants', 'Categoría 11 · Expulsantes drásticos'),
        'question' => $mixedtext('Massive retained fluid requires a drastic expellant. Which herb is strongest for channel/chest-abdominal fluid, which adds bowel and parasite clues, and which is the very toxic hot cathartic for Cold?', 'Retención masiva de líquidos requiere expulsante drástico. ¿Cuál es más fuerte para líquido en canales/tórax-abdomen, cuál añade intestino y parásitos y cuál es el catártico muy tóxico y caliente para Frío?'),
        'answer' => $mixedtext('Gan Sui is the strongest channel-fluid expellant. Qian Niu Zi drives water through stool/urine and also expels parasites. Ba Dou is the exceptional very hot, extremely toxic cathartic for severe Cold obstruction. All require expert supervision; pregnancy and frailty are major boundaries.', 'Gan Sui es el expulsante más fuerte de líquido en canales. Qian Niu Zi conduce agua por heces/orina y también expulsa parásitos. Ba Dou es el catártico excepcional muy caliente y extremadamente tóxico para obstrucción grave por Frío. Todas requieren supervisión experta; embarazo y debilidad son límites mayores.'),
    ],
    [
        'category' => $mixedtext('Category 12 · Expel Wind-Damp', 'Categoría 12 · Expulsar Viento-Humedad'),
        'question' => $mixedtext('Choose the location clue: lower-body Cold-Damp pain; shifting Wind-predominant pain; fixed obstruction that “threads” through channels; chronic Bi with Liver-Kidney deficiency.', 'Elige la pista de localización: dolor Frío-Humedad en parte inferior; dolor móvil con predominio de Viento; obstrucción fija que “atraviesa” canales; Bi crónico con deficiencia de Hígado-Riñón.'),
        'answer' => $mixedtext('Du Huo: lower body and Cold-Damp. Qin Jiao: Wind-predominant, relatively gentle and also clears deficiency Heat. Wei Ling Xian: powerfully opens channels for fixed obstruction. Sang Ji Sheng: chronic Bi while tonifying Liver-Kidney and supporting pregnancy.', 'Du Huo: parte inferior y Frío-Humedad. Qin Jiao: predominio de Viento, relativamente suave y también aclara Calor deficiente. Wei Ling Xian: abre canales con fuerza para obstrucción fija. Sang Ji Sheng: Bi crónico mientras tonifica Hígado-Riñón y apoya embarazo.'),
    ],
    [
        'category' => $mixedtext('Category 13 · Cool and Transform Phlegm-Heat', 'Categoría 13 · Enfriar y transformar Flema-Calor'),
        'question' => $mixedtext('Both Bei Mu herbs clear Phlegm-Heat. Which is gentler, moistens Lung Dryness and fits chronic deficiency cough; which is colder, stronger and better for nodules or acute excess?', 'Ambas Bei Mu aclaran Flema-Calor. ¿Cuál es más suave, humedece Sequedad de Pulmón y corresponde a tos crónica por deficiencia; cuál es más fría, fuerte y mejor para nódulos o Exceso agudo?'),
        'answer' => $mixedtext('Chuan Bei Mu is the gentler moistening choice for chronic cough and Lung Yin deficiency. Zhe Bei Mu is colder and stronger for acute Phlegm-Heat, toxicity and nodules. This moist-versus-dispersing distinction is more useful than memorizing that both stop cough.', 'Chuan Bei Mu es la opción más suave y humectante para tos crónica y deficiencia de Yin de Pulmón. Zhe Bei Mu es más fría y fuerte para Flema-Calor aguda, toxicidad y nódulos. Esta distinción humedecer versus dispersar es más útil que memorizar que ambas detienen tos.'),
    ],
    [
        'category' => $mixedtext('Category 14 · Warm and Transform Phlegm-Cold', 'Categoría 14 · Calentar y transformar Flema-Frío'),
        'question' => $mixedtext('Which herb is the core damp-Phlegm and rebellious-Stomach-Qi remedy, and which is stronger for stubborn Phlegm with Wind, spasm or nodules?', '¿Qué hierba es el remedio central para Flema-Humedad y Qi rebelde de Estómago y cuál es más fuerte para Flema persistente con Viento, espasmo o nódulos?'),
        'answer' => $mixedtext('Processed Ban Xia dries Dampness, transforms Phlegm and descends rebellious Stomach Qi. Tian Nan Xing is hotter and more toxic, stronger for stubborn Phlegm, Wind-Phlegm and spasm. Formula bridge: Ban Xia is chief in Ban Xia Xie Xin Tang and Ban Xia Hou Po Tang, and co-chief in Er Chen Tang and Ban Xia Bai Zhu Tian Ma Tang.', 'Ban Xia procesada seca Humedad, transforma Flema y desciende Qi rebelde de Estómago. Tian Nan Xing es más caliente y tóxica, más fuerte para Flema persistente, Viento-Flema y espasmo. Puente de fórmula: Ban Xia es principal en Ban Xia Xie Xin Tang y Ban Xia Hou Po Tang, y coprincipal en Er Chen Tang y Ban Xia Bai Zhu Tian Ma Tang.'),
    ],
    [
        'category' => $mixedtext('Category 15 · Relieve Coughing and Wheezing', 'Categoría 15 · Aliviar tos y sibilancia'),
        'question' => $mixedtext('Match the direction: open Lung and guide upward to the throat; descend Lung Qi for cough/wheeze; drain Lung Heat and edema.', 'Relaciona la dirección: abrir Pulmón y guiar hacia arriba a garganta; descender Qi de Pulmón para tos/sibilancia; drenar Calor de Pulmón y edema.'),
        'answer' => $mixedtext('Jie Geng opens and disseminates Lung Qi, benefits the throat and guides other herbs upward. Xing Ren descends Lung Qi to stop cough and wheeze while moistening the bowel. Sang Bai Pi drains Lung Heat and promotes urination for edema.', 'Jie Geng abre y difunde Qi de Pulmón, beneficia garganta y guía otras hierbas hacia arriba. Xing Ren desciende Qi de Pulmón para detener tos/sibilancia mientras humedece intestino. Sang Bai Pi drena Calor de Pulmón y promueve orina para edema.'),
    ],
    [
        'category' => $mixedtext('Category 16 · Aromatically Transform Dampness', 'Categoría 16 · Transformar Humedad aromáticamente'),
        'question' => $mixedtext('A Board stem emphasizes external invasion plus Middle-Burner Dampness; another emphasizes thick greasy coating and strong drying; another emphasizes focal fullness and distention; another needs pregnancy-safe Qi movement. Match the herbs.', 'Un caso Board enfatiza invasión externa más Humedad en Jiao Medio; otro saburra gruesa/grasosa y secado fuerte; otro plenitud focal y distensión; otro necesita mover Qi de forma compatible con embarazo. Relaciona las hierbas.'),
        'answer' => $mixedtext('Huo Xiang: exterior plus Middle Jiao, nausea and vomiting. Cang Zhu: strongest dry, aromatic Dampness treatment. Hou Po: fullness/distention through strong Qi movement. Sha Ren: moves Qi, warms Middle and calms the fetus; add late.', 'Huo Xiang: Exterior más Jiao Medio, náusea y vómito. Cang Zhu: tratamiento aromático más secante para Humedad. Hou Po: plenitud/distensión mediante movimiento fuerte de Qi. Sha Ren: mueve Qi, calienta Centro y calma feto; añadir al final.'),
    ],
    [
        'category' => $mixedtext('Category 17 · Relieve Food Stagnation', 'Categoría 17 · Aliviar estancamiento de alimentos'),
        'question' => $mixedtext('Choose the food clue: meat/fat plus Blood stasis; grains and lactation/Liver Qi; virtually any food plus stones; fermented grain/alcohol.', 'Elige la pista alimentaria: carne/grasa más estasis de Sangre; granos y lactancia/Qi de Hígado; casi cualquier alimento más cálculos; grano fermentado/alcohol.'),
        'answer' => $mixedtext('Shan Zha: meat, fat and Blood stasis. Mai Ya: grains, moves Liver Qi and reduces lactation. Ji Nei Jin: broad food stagnation and dissolves stones. Shen Qu: fermented foods, grains and alcohol.', 'Shan Zha: carne, grasa y estasis de Sangre. Mai Ya: granos, mueve Qi de Hígado y reduce lactancia. Ji Nei Jin: estancamiento alimentario amplio y disuelve cálculos. Shen Qu: fermentados, granos y alcohol.'),
    ],
    [
        'category' => $mixedtext('Category 18 · Regulate Qi', 'Categoría 18 · Regular Qi'),
        'question' => $mixedtext('Identify the organ-level specialists: Spleen/Lung Qi with Phlegm; Liver Qi and gynecology; strong focal accumulation; painful Liver Qi stagnation with Heat.', 'Identifica especialistas por órgano: Qi de Bazo/Pulmón con Flema; Qi de Hígado y ginecología; acumulación focal fuerte; estancamiento doloroso de Qi de Hígado con Calor.'),
        'answer' => $mixedtext('Chen Pi regulates Spleen/Lung Qi and dries Damp-Phlegm. Xiang Fu is the major Liver-Qi and gynecologic regulator. Zhi Shi strongly breaks focal Qi accumulation. Chuan Lian Zi is bitter-cold for painful Liver-Qi constraint with Heat.', 'Chen Pi regula Qi de Bazo/Pulmón y seca Flema-Humedad. Xiang Fu es la gran reguladora de Qi de Hígado y ginecología. Zhi Shi rompe con fuerza acumulación focal de Qi. Chuan Lian Zi es amarga-fría para estancamiento doloroso de Qi de Hígado con Calor.'),
    ],
    [
        'category' => $mixedtext('Category 19 · Invigorate Blood and Move Stasis', 'Categoría 19 · Vigorizar Sangre y mover estasis'),
        'question' => $mixedtext('Which herb treats Blood stasis while cooling Blood and calming irritability; which resin acts more on Qi/sinews; which paired resin acts more on Blood; which guides Blood downward and strengthens sinews?', '¿Qué hierba trata estasis mientras enfría Sangre y calma irritabilidad; qué resina actúa más sobre Qi/tendones; qué resina pareja actúa más sobre Sangre; cuál guía Sangre hacia abajo y fortalece tendones?'),
        'answer' => $mixedtext('Dan Shen cools and invigorates Blood and calms the Spirit. Ru Xiang is warmer and more Qi/sinew oriented; Mo Yao more strongly breaks Blood stasis. Niu Xi guides Blood and Fire downward and strengthens Liver-Kidney sinews/bones.', 'Dan Shen enfría y vigoriza Sangre y calma Shen. Ru Xiang es más tibia y orientada a Qi/tendones; Mo Yao rompe estasis de Sangre con más fuerza. Niu Xi guía Sangre y Fuego hacia abajo y fortalece tendones/huesos de Hígado-Riñón.'),
    ],
    [
        'category' => $mixedtext('Category 20 · Stop Bleeding', 'Categoría 20 · Detener sangrado'),
        'question' => $mixedtext('Match the bleeding mechanism: stops bleeding while transforming stasis; bleeding from Lung/Stomach tissue damage; warm uterine bleeding from Cold; cools Blood and stops bleeding while invigorating.', 'Relaciona el mecanismo de sangrado: detiene mientras transforma estasis; sangrado por daño tisular de Pulmón/Estómago; sangrado uterino por Frío; enfría Sangre y detiene mientras vigoriza.'),
        'answer' => $mixedtext('San Qi stops bleeding without leaving stasis and strongly relieves traumatic pain. Bai Ji astringes and heals Lung/Stomach tissue. Ai Ye warms channels for Cold uterine bleeding. Pu Huang stops bleeding and invigorates Blood; raw versus charred preparation changes emphasis.', 'San Qi detiene sangrado sin dejar estasis y alivia con fuerza dolor traumático. Bai Ji astringe y repara tejido de Pulmón/Estómago. Ai Ye calienta canales para sangrado uterino por Frío. Pu Huang detiene y vigoriza Sangre; preparación cruda o carbonizada cambia el énfasis.'),
    ],
    [
        'category' => $mixedtext('Category 21 · Warm the Interior and Expel Cold', 'Categoría 21 · Calentar Interior y expulsar Frío'),
        'question' => $mixedtext('Which herb rescues devastated Yang and warms all channels; which strengthens Mingmen Fire and guides fire home; which chiefly warms the Middle and transforms cold Phlegm?', '¿Qué hierba rescata Yang devastado y calienta todos los canales; cuál fortalece Fuego de Mingmen y guía fuego a su origen; cuál principalmente calienta Centro y transforma Flema fría?'),
        'answer' => $mixedtext('Properly processed Fu Zi rescues collapsed Yang and reaches all twelve channels. Rou Gui warms Kidney/Mingmen Fire and leads floating fire back to its source. Gan Jiang strongly warms Spleen-Stomach and warms Lung to transform cold Phlegm. Fu Zi toxicity makes correct processing non-negotiable.', 'Fu Zi correctamente procesada rescata colapso de Yang y alcanza los doce canales. Rou Gui calienta Fuego de Riñón/Mingmen y devuelve fuego flotante a su origen. Gan Jiang calienta con fuerza Bazo-Estómago y Pulmón para transformar Flema fría. La toxicidad de Fu Zi hace indispensable su procesamiento correcto.'),
    ],
    [
        'category' => $mixedtext('Category 23 · Tonify Qi', 'Categoría 23 · Tonificar Qi'),
        'question' => $mixedtext('Severe Yuan-Qi collapse, Spleen-Qi deficiency with prolapse, Spleen deficiency with Dampness, and gentle Lung-Spleen-Kidney support each favor which tonic?', 'Colapso grave de Yuan Qi, deficiencia de Qi de Bazo con prolapso, deficiencia de Bazo con Humedad y apoyo suave Pulmón-Bazo-Riñón favorecen cada uno ¿qué tónico?'),
        'answer' => $mixedtext('Ren Shen strongly restores Yuan Qi. Huang Qi raises clear Yang, treats prolapse and secures the exterior. Bai Zhu tonifies Spleen while drying Dampness. Shan Yao gently tonifies Lung, Spleen and Kidney while stabilizing leakage.', 'Ren Shen restaura con fuerza Yuan Qi. Huang Qi eleva Yang claro, trata prolapso y asegura Exterior. Bai Zhu tonifica Bazo mientras seca Humedad. Shan Yao tonifica suavemente Pulmón, Bazo y Riñón mientras estabiliza pérdidas.'),
    ],
    [
        'category' => $mixedtext('Category 24 · Tonify Blood', 'Categoría 24 · Tonificar Sangre'),
        'question' => $mixedtext('Assign the signature: most enriching Liver-Kidney Blood/Yin tonic; Blood tonic that also moves and regulates menses; gelatin that stops bleeding; softens Liver and preserves Yin.', 'Asigna la firma: tónico más enriquecedor de Sangre/Yin de Hígado-Riñón; tónico de Sangre que también mueve y regula menstruación; gelatina que detiene sangrado; suaviza Hígado y preserva Yin.'),
        'answer' => $mixedtext('Shu Di Huang is the richest Liver-Kidney Blood/Yin tonic. Dang Gui nourishes and moves Blood and regulates menstruation. E Jiao nourishes Blood/Yin and stops bleeding. Bai Shao nourishes Blood, preserves Yin and softens/constrains the Liver.', 'Shu Di Huang es el tónico más rico de Sangre/Yin de Hígado-Riñón. Dang Gui nutre y mueve Sangre y regula menstruación. E Jiao nutre Sangre/Yin y detiene sangrado. Bai Shao nutre Sangre, preserva Yin y suaviza/contiene Hígado.'),
    ],
    [
        'category' => $mixedtext('Category 25 · Tonify Yang', 'Categoría 25 · Tonificar Yang'),
        'question' => $mixedtext('Which animal medicine is the strongest Essence/Yang builder; which bark strengthens low back, knees and pregnancy; which seed secures Essence while tonifying Yin and Yang; which herb strongly warms Kidney Yang and dispels Wind-Damp?', '¿Qué medicina animal es el constructor más fuerte de Esencia/Yang; qué corteza fortalece lumbar, rodillas y embarazo; qué semilla asegura Esencia mientras tonifica Yin y Yang; qué hierba calienta con fuerza Yang de Riñón y expulsa Viento-Humedad?'),
        'answer' => $mixedtext('Lu Rong strongly tonifies Kidney Yang, Essence and Blood. Du Zhong strengthens sinews/bones, low back and fetus. Tu Si Zi tonifies both Yin and Yang while securing Essence. Yin Yang Huo strongly warms Kidney Yang and expels Wind-Damp.', 'Lu Rong tonifica con fuerza Yang de Riñón, Esencia y Sangre. Du Zhong fortalece tendones/huesos, lumbar y feto. Tu Si Zi tonifica Yin y Yang mientras asegura Esencia. Yin Yang Huo calienta con fuerza Yang de Riñón y expulsa Viento-Humedad.'),
    ],
    [
        'category' => $mixedtext('Category 26 · Tonify Yin', 'Categoría 26 · Tonificar Yin'),
        'question' => $mixedtext('Dry Lung cough with Heart irritability, stronger Lung-Kidney dryness, Stomach-Yin thirst, and gentle Lung-Stomach nourishment each favor which herb?', 'Tos seca de Pulmón con irritabilidad de Corazón, sequedad más fuerte Pulmón-Riñón, sed por Yin de Estómago y nutrición suave Pulmón-Estómago favorecen cada una ¿qué hierba?'),
        'answer' => $mixedtext('Mai Men Dong nourishes Lung/Stomach Yin and clears Heart irritability. Tian Men Dong is colder and richer for Lung-Kidney Yin. Shi Hu emphasizes Stomach Yin and fluid generation. Bei Sha Shen gently nourishes Lung/Stomach Yin while clearing deficiency Heat.', 'Mai Men Dong nutre Yin de Pulmón/Estómago y aclara irritabilidad de Corazón. Tian Men Dong es más fría y rica para Yin de Pulmón-Riñón. Shi Hu enfatiza Yin de Estómago y generación de líquidos. Bei Sha Shen nutre suavemente Yin de Pulmón/Estómago mientras aclara Calor deficiente.'),
    ],
    [
        'category' => $mixedtext('Category 27 · Stabilize and Bind', 'Categoría 27 · Estabilizar y contener'),
        'question' => $mixedtext('Choose the leakage: chronic Lung cough plus diarrhea and parasites; Lung/Kidney leakage with palpitations and irritability; chronic hoarseness/diarrhea; spontaneous sweating from Qi deficiency.', 'Elige la pérdida: tos crónica de Pulmón más diarrea y parásitos; pérdida Pulmón/Riñón con palpitaciones e irritabilidad; ronquera/diarrea crónica; sudor espontáneo por deficiencia de Qi.'),
        'answer' => $mixedtext('Wu Mei contains Lung and intestines and expels roundworms. Wu Wei Zi contains Lung and Kidney while generating fluids and calming the Heart. He Zi is especially associated with chronic cough/hoarseness and diarrhea. Fu Xiao Mai stops deficiency sweating and clears floating Heat.', 'Wu Mei contiene Pulmón e intestinos y expulsa áscaris. Wu Wei Zi contiene Pulmón y Riñón mientras genera líquidos y calma Corazón. He Zi se asocia especialmente con tos/ronquera crónica y diarrea. Fu Xiao Mai detiene sudor por deficiencia y aclara Calor flotante.'),
    ],
    [
        'category' => $mixedtext('Category 28 · Anchor, Settle and Calm the Spirit', 'Categoría 28 · Anclar, asentar y calmar Shen'),
        'question' => $mixedtext('Which shell calms, softens nodules and controls leakage; which fossil calms and strongly astringes; which magnet emphasizes Kidney, hearing and reception of Qi; which pearl shell emphasizes Liver Yang and eyes?', '¿Qué concha calma, ablanda nódulos y controla pérdidas; qué fósil calma y astringe con fuerza; qué imán enfatiza Riñón, audición y recepción de Qi; qué concha nacarada enfatiza Yang de Hígado y ojos?'),
        'answer' => $mixedtext('Mu Li calms, softens hardness and astringes leakage. Long Gu anchors and astringes strongly. Ci Shi calms while benefiting Kidney, hearing and reception of Lung Qi. Zhen Zhu Mu calms the Spirit, anchors Liver Yang and benefits the eyes.', 'Mu Li calma, ablanda dureza y astringe pérdidas. Long Gu ancla y astringe con fuerza. Ci Shi calma mientras beneficia Riñón, audición y recepción de Qi de Pulmón. Zhen Zhu Mu calma Shen, ancla Yang de Hígado y beneficia ojos.'),
    ],
    [
        'category' => $mixedtext('Category 29 · Nourish the Heart and Calm the Spirit', 'Categoría 29 · Nutrir Corazón y calmar Shen'),
        'question' => $mixedtext('Deficiency insomnia with night sweating, Phlegm blocking the Heart orifice, and insomnia with irritability plus skin itching each suggest which herb?', 'Insomnio por deficiencia con sudor nocturno, Flema bloqueando orificio de Corazón e insomnio con irritabilidad más prurito cutáneo sugieren cada uno ¿qué hierba?'),
        'answer' => $mixedtext('Suan Zao Ren nourishes Heart/Liver Blood and stops sweating; it is chief in Suan Zao Ren Tang. Yuan Zhi transforms Phlegm and reconnects Heart-Kidney while opening the orifice. Ye Jiao Teng nourishes Blood, calms the Spirit and relieves itching.', 'Suan Zao Ren nutre Sangre de Corazón/Hígado y detiene sudor; es principal en Suan Zao Ren Tang. Yuan Zhi transforma Flema y reconecta Corazón-Riñón mientras abre orificio. Ye Jiao Teng nutre Sangre, calma Shen y alivia prurito.'),
    ],
    [
        'category' => $mixedtext('Category 30 · Aromatically Open the Orifices', 'Categoría 30 · Abrir orificios aromáticamente'),
        'question' => $mixedtext('One current herb opens sensory orifices, transforms Damp-Phlegm and improves memory; the other is cold, clears toxic Heat and stops convulsions. Which is which?', 'Una hierba actual abre orificios sensoriales, transforma Flema-Humedad y mejora memoria; la otra es fría, aclara Calor tóxico y detiene convulsiones. ¿Cuál es cuál?'),
        'answer' => $mixedtext('Shi Chang Pu is warm-aromatic: opens orifices, transforms Damp-Phlegm, harmonizes the Middle and supports cognition. Niu Huang is cold: clears Heart/Liver toxic Heat, opens the orifices and extinguishes Wind. Sudden altered consciousness is an emergency.', 'Shi Chang Pu es tibia-aromática: abre orificios, transforma Flema-Humedad, armoniza Centro y apoya cognición. Niu Huang es fría: aclara Calor tóxico de Corazón/Hígado, abre orificios y extingue Viento. Alteración súbita de conciencia es una emergencia.'),
    ],
    [
        'category' => $mixedtext('Category 31 · Extinguish Wind and Stop Tremors', 'Categoría 31 · Extinguir Viento y detener temblores'),
        'question' => $mixedtext('Which neutral herb handles the widest range of dizziness/Wind patterns; which cool hook fits obvious Liver Heat; which shell has the eye signature; which mineral produces the strongest rapid downward descent?', '¿Qué hierba neutra cubre la mayor variedad de mareo/Viento; qué gancho fresco corresponde a Calor de Hígado evidente; qué concha tiene firma ocular; qué mineral produce el descenso rápido más fuerte?'),
        'answer' => $mixedtext('Tian Ma is the neutral, versatile Wind herb. Gou Teng is the cool Liver-Heat choice and should be added late. Shi Jue Ming strongly anchors while clearing Liver Fire and benefiting the eyes. Dai Zhe Shi is the heaviest, fastest descent of rebellious Yang/Qi/Blood. Formula bridge: Tian Ma, Gou Teng and Shi Jue Ming are co-chiefs in Tian Ma Gou Teng Yin.', 'Tian Ma es la hierba neutra y versátil para Viento. Gou Teng es la opción fresca para Calor de Hígado y se añade al final. Shi Jue Ming ancla con fuerza mientras aclara Fuego y beneficia ojos. Dai Zhe Shi es el descenso más pesado y rápido de Yang/Qi/Sangre rebeldes. Puente de fórmula: Tian Ma, Gou Teng y Shi Jue Ming son coprincipales en Tian Ma Gou Teng Yin.'),
    ],
];

// Every visit changes the sequence so recall depends on the clue, not the category order.
shuffle($mixedcards);
?>

<section class="single-herbs-guide__section" aria-labelledby="mixed-review-method">
    <h4 id="mixed-review-method"><?php echo s($mixedtext('Mixed Review: pass through three gates', 'Repaso mixto: atraviesa tres puertas')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($mixedtext('Do not guess the herb from one symptom. Each card asks you to rebuild the Board route before revealing the answer.', 'No adivines la hierba por un solo síntoma. Cada tarjeta te pide reconstruir la ruta Board antes de revelar la respuesta.')); ?></p>
    <div class="single-herbs-guide__decision">
        <div><strong><?php echo s($mixedtext('1 · Pattern', '1 · Patrón')); ?></strong><span><?php echo s($mixedtext('Hot or Cold? Excess or deficiency? Exterior or Interior?', '¿Calor o Frío? ¿Exceso o deficiencia? ¿Exterior o Interior?')); ?></span></div>
        <div><strong><?php echo s($mixedtext('2 · Mechanism', '2 · Mecanismo')); ?></strong><span><?php echo s($mixedtext('Release, clear, drain, transform, move, tonify or secure?', '¿Liberar, aclarar, drenar, transformar, mover, tonificar o contener?')); ?></span></div>
        <div><strong><?php echo s($mixedtext('3 · Decisive clue', '3 · Pista decisiva')); ?></strong><span><?php echo s($mixedtext('Sweat, location, fluid, food, bleeding, Shen or Wind?', '¿Sudor, localización, líquido, comida, sangrado, Shen o Viento?')); ?></span></div>
    </div>
    <aside class="single-herbs-guide__memory-path">
        <p class="single-herbs-guide__memory-line"><?php echo s($mixedtext('Imagine the Board control room: the first gate names the terrain, the second chooses the lever, and the third reads the one warning light that separates look-alike herbs.', 'Imagina la sala de control del Board: la primera puerta nombra el terreno, la segunda elige la palanca y la tercera lee la única luz de alarma que separa hierbas parecidas.')); ?></p>
        <p><?php echo s($mixedtext('Say your three-part answer aloud before flipping: “Pattern → mechanism → decisive herb.” The cards are reshuffled every time this page opens.', 'Di en voz alta tu respuesta de tres partes antes de voltear: “Patrón → mecanismo → hierba decisiva”. Las tarjetas se mezclan cada vez que abres esta página.')); ?></p>
    </aside>
</section>

<section class="single-herbs-guide__section" aria-labelledby="mixed-review-deck">
    <h4 id="mixed-review-deck"><?php echo s($mixedtext('30-category challenge deck', 'Baraja de desafío de 30 categorías')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($mixedtext('One card represents each completed Board category. Answer first; then flip to verify the distinction and any validated formula bridge.', 'Una tarjeta representa cada categoría Board terminada. Responde primero; luego voltéala para verificar la distinción y cualquier puente de fórmula validado.')); ?></p>
    <div class="single-herbs-guide__yang-deck" data-yang-deck data-card-label="<?php echo s($mixedtext('Challenge', 'Reto')); ?>" data-of-label="<?php echo s($mixedtext('of', 'de')); ?>">
        <?php foreach ($mixedcards as $index => $card): ?>
            <article class="single-herbs-guide__yang-card<?php echo $index === 0 ? ' is-active' : ''; ?>" data-yang-card aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                <div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--front">
                    <span class="single-herbs-guide__yang-label"><?php echo s($mixedtext('Mixed challenge', 'Reto mixto') . ' ' . ($index + 1) . ' ' . $mixedtext('of', 'de') . ' ' . count($mixedcards)); ?></span>
                    <p class="single-herbs-guide__yang-question"><?php echo s($card['question']); ?></p>
                </div>
                <div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--back">
                    <span class="single-herbs-guide__yang-label"><?php echo s($card['category']); ?></span>
                    <p class="single-herbs-guide__yang-answer"><?php echo s($card['answer']); ?></p>
                </div>
            </article>
        <?php endforeach; ?>
        <div class="single-herbs-guide__yang-controls">
            <button class="single-herbs-guide__yang-control" type="button" data-yang-prev>← <?php echo s($mixedtext('Previous', 'Anterior')); ?></button>
            <button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--flip" type="button" data-yang-flip data-show-label="<?php echo s($mixedtext('Show answer', 'Mostrar respuesta')); ?>" data-hide-label="<?php echo s($mixedtext('Show challenge', 'Mostrar reto')); ?>"><?php echo s($mixedtext('Show answer', 'Mostrar respuesta')); ?></button>
            <button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--next" type="button" data-yang-next><?php echo s($mixedtext('Next', 'Siguiente')); ?> →</button>
        </div>
        <p class="single-herbs-guide__yang-progress" data-yang-progress><?php echo s($mixedtext('Challenge', 'Reto')); ?> 1 <?php echo s($mixedtext('of', 'de')); ?> <?php echo count($mixedcards); ?></p>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="mixed-review-map">
    <h4 id="mixed-review-map"><?php echo s($mixedtext('Cross-category map', 'Mapa entre categorías')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($mixedtext('When two answers feel possible, locate the case in one of these five routes and then return to the decisive clue.', 'Cuando dos respuestas parezcan posibles, ubica el caso en una de estas cinco rutas y luego vuelve a la pista decisiva.')); ?></p>
    <div class="single-herbs-guide__decision">
        <div><strong><?php echo s($mixedtext('Release → Clear', 'Liberar → Aclarar')); ?></strong><span><?php echo s($mixedtext('Exterior first; Qi, Blood, toxin or deficiency Heat only after the pathogen enters.', 'Primero Exterior; Qi, Sangre, toxicidad o Calor deficiente cuando el patógeno entra.')); ?></span></div>
        <div><strong><?php echo s($mixedtext('Drain → Transform', 'Drenar → Transformar')); ?></strong><span><?php echo s($mixedtext('Stool, urine, Dampness and Phlegm reveal the exit route.', 'Heces, orina, Humedad y Flema revelan la vía de salida.')); ?></span></div>
        <div><strong><?php echo s($mixedtext('Move Qi → Move Blood', 'Mover Qi → Mover Sangre')); ?></strong><span><?php echo s($mixedtext('Distention and changeability suggest Qi; fixed stabbing pain suggests Blood stasis.', 'Distensión y variabilidad sugieren Qi; dolor fijo punzante sugiere estasis de Sangre.')); ?></span></div>
        <div><strong><?php echo s($mixedtext('Tonify → Secure', 'Tonificar → Contener')); ?></strong><span><?php echo s($mixedtext('Replace what is deficient before closing the gate on leakage.', 'Repón lo deficiente antes de cerrar la puerta de la pérdida.')); ?></span></div>
        <div><strong><?php echo s($mixedtext('Spirit → Orifices → Wind', 'Shen → Orificios → Viento')); ?></strong><span><?php echo s($mixedtext('Restlessness, consciousness and involuntary movement are different diagnostic doors.', 'Inquietud, conciencia y movimiento involuntario son puertas diagnósticas distintas.')); ?></span></div>
    </div>
    <p class="single-herbs-guide__safety"><strong><?php echo s($mixedtext('Safety stop:', 'Alto de seguridad:')); ?></strong> <?php echo s($mixedtext('Sudden neurologic change, altered consciousness, severe abdominal pain or obstruction, uncontrolled bleeding, collapse, respiratory distress or suspected toxic-herb exposure requires urgent medical evaluation. This review supports exam study; it is not prescribing guidance.', 'Cambio neurológico súbito, alteración de conciencia, dolor abdominal grave u obstrucción, sangrado no controlado, colapso, dificultad respiratoria o exposición sospechada a hierba tóxica requiere evaluación médica urgente. Este repaso apoya el estudio para examen; no es guía de prescripción.')); ?></p>
</section>

<section class="single-herbs-guide__section" aria-labelledby="mixed-review-sources">
    <h4 id="mixed-review-sources"><?php echo s($mixedtext('Source boundary', 'Límite de fuentes')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($mixedtext('The distinctions synthesize the completed study notes using Yang, Chinese Herbal Medicines: Comparisons and Characteristics, 2nd ed.; Bensky et al., Chinese Herbal Medicine: Materia Medica; and the current 2026 NCBAHM herb/formula roster. Formula bridges appear only where the herb is a validated chief or co-chief in the current 63-formula roster.', 'Las distinciones sintetizan las guías terminadas usando Yang, Chinese Herbal Medicines: Comparisons and Characteristics, 2.ª ed.; Bensky et al., Chinese Herbal Medicine: Materia Medica; y el listado vigente 2026 de hierbas/fórmulas del NCBAHM. Los puentes de fórmula aparecen solo cuando la hierba es principal o coprincipal validada en el listado actual de 63 fórmulas.')); ?></p>
</section>
