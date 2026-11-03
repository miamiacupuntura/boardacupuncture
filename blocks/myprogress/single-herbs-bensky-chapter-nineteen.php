<?php
// Bensky Chapter 19 atlas: Obsolete Substances.

defined('MOODLE_INTERNAL') || die();

$b19text = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};

$b19toxicity = [
    [
        'name' => 'Zhu Sha',
        'latin' => 'Cinnabaris',
        'meta' => $b19text('Mercury sulfide mineral · historically sweet, cool and Heart-directed', 'Mineral de sulfuro de mercurio · históricamente dulce, fresco y dirigido al Corazón'),
        'focus' => $b19text('Historically calmed the spirit, settled convulsions and was applied to toxic sores.', 'Históricamente calmaba el espíritu, controlaba convulsiones y se aplicaba a llagas tóxicas.'),
        'clue' => $b19text('Heavy red mineral associated with spirit disturbance and phlegm-heat convulsions.', 'Mineral rojo y pesado asociado con alteración del espíritu y convulsiones por Flema-Calor.'),
        'safety' => $b19text('Mercury exposure can cause severe chronic poisoning. Heating forms more soluble, highly toxic mercury oxide.', 'La exposición al mercurio puede causar intoxicación crónica grave. Calentarlo forma óxido de mercurio más soluble y muy tóxico.'),
        'identity' => $b19text('Aqueous trituration historically reduced soluble salts but does not place it in the current guideline.', 'La trituración acuosa reducía históricamente sales solubles, pero no la incorpora al guideline vigente.'),
    ],
    [
        'name' => 'Ying Su Ke',
        'latin' => 'Papaveris Pericarpium',
        'meta' => $b19text('Opium poppy husk · sour, astringent and narcotic', 'Cáscara de adormidera · ácida, astringente y narcótica'),
        'focus' => $b19text('Historically restrained chronic cough and diarrhea, secured leakage and relieved pain.', 'Históricamente restringía tos y diarrea crónicas, contenía pérdidas y aliviaba dolor.'),
        'clue' => $b19text('A very strong binding substance for deficiency-only chronic leakage.', 'Sustancia fijadora muy fuerte para pérdidas crónicas únicamente por deficiencia.'),
        'safety' => $b19text('Addictive and respiratory-depressant; overdose may cause coma and death. Controlled by narcotics laws.', 'Adictiva y depresora respiratoria; la sobredosis puede causar coma y muerte. Controlada por leyes de narcóticos.'),
        'identity' => $b19text('Poppy capsule/pericarp; vinegar preparation emphasized diarrhea and honey preparation cough.', 'Cápsula/pericarpio de adormidera; preparación con vinagre enfatizaba diarrea y con miel, tos.'),
    ],
    [
        'name' => 'Ma Qian Zi',
        'latin' => 'Strychni Semen',
        'meta' => $b19text('Nux-vomica seed · bitter, cold and extremely toxic', 'Semilla de nuez vómica · amarga, fría y extremadamente tóxica'),
        'focus' => $b19text('Historically unblocked collaterals, dispersed clumps and treated severe pain or traumatic swelling.', 'Históricamente desbloqueaba colaterales, dispersaba masas y trataba dolor intenso o hinchazón traumática.'),
        'clue' => $b19text('Very stubborn pain, spasm or numbness with an exceptionally narrow safety margin.', 'Dolor, espasmo o entumecimiento muy resistentes con margen de seguridad excepcionalmente estrecho.'),
        'safety' => $b19text('Strychnine poisoning causes stimulus-triggered tetanic convulsions, respiratory arrest and death.', 'La intoxicación por estricnina causa convulsiones tetánicas provocadas por estímulos, paro respiratorio y muerte.'),
        'identity' => $b19text('Sand-frying reduced but did not eliminate toxicity; raw seed was never acceptable.', 'Tostar en arena reducía pero no eliminaba la toxicidad; la semilla cruda nunca era aceptable.'),
    ],
    [
        'name' => 'Lei Gong Teng',
        'latin' => 'Tripterygii wilfordii Radix',
        'meta' => $b19text('Thunder-god vine root · bitter, acrid, cold and highly toxic', 'Raíz de vid del dios del trueno · amarga, acre, fría y muy tóxica'),
        'focus' => $b19text('Historically used for recalcitrant painful obstruction and severe pruritic skin disease.', 'Históricamente usada para obstrucción dolorosa resistente y enfermedad cutánea pruriginosa grave.'),
        'clue' => $b19text('Powerful anti-inflammatory reputation paired with a poor benefit-to-harm ratio.', 'Reputación antiinflamatoria potente acompañada de mala relación beneficio-daño.'),
        'safety' => $b19text('Documented cardiac, hepatic, renal, gastrointestinal, marrow and reproductive toxicity, including deaths.', 'Toxicidad cardíaca, hepática, renal, gastrointestinal, medular y reproductiva documentada, incluidas muertes.'),
        'identity' => $b19text('Historical processing removed both root-bark layers and cooked the xylem for hours; this did not make routine use acceptable.', 'El procesamiento histórico retiraba ambas capas de corteza y cocía el xilema por horas; esto no hacía aceptable el uso rutinario.'),
    ],
    [
        'name' => 'Ma Dou Ling',
        'latin' => 'Aristolochiae Fructus',
        'meta' => $b19text('Birthwort fruit · bitter and cold', 'Fruto de aristoloquia · amargo y frío'),
        'focus' => $b19text('Historically cleared Lung heat, directed qi downward and treated cough, wheezing or hemorrhoidal swelling.', 'Históricamente aclaraba Calor de Pulmón, descendía Qi y trataba tos, sibilancias o hinchazón hemorroidal.'),
        'clue' => $b19text('A Lung-heat cough herb whose fruit, stem and root all share the same aristolochic-acid hazard.', 'Hierba para tos por Calor de Pulmón cuyo fruto, tallo y raíz comparten el peligro del ácido aristolóquico.'),
        'safety' => $b19text('Aristolochic acid is nephrotoxic, mutagenic and carcinogenic; products are banned or restricted in many countries.', 'El ácido aristolóquico es nefrotóxico, mutagénico y carcinógeno; sus productos están prohibidos o restringidos en muchos países.'),
        'identity' => $b19text('Honey preparation changed taste and Lung action, not the underlying carcinogenic identity.', 'La preparación con miel cambiaba sabor y acción pulmonar, no la identidad carcinógena subyacente.'),
    ],
    [
        'name' => 'Guan Mu Tong',
        'latin' => 'Aristolochiae manshuriensis Caulis',
        'meta' => $b19text('Aristolochia stem · historically bitter and cold', 'Tallo de Aristolochia · históricamente amargo y frío'),
        'focus' => $b19text('Historically substituted for Mu Tong to clear Heart fire and promote urination or lactation.', 'Históricamente sustituía a Mu Tong para aclarar Fuego de Corazón y promover micción o lactancia.'),
        'clue' => $b19text('The dangerous “Mu Tong” name-confusion trap.', 'La peligrosa trampa de confusión bajo el nombre “Mu Tong”.'),
        'safety' => $b19text('Aristolochic-acid exposure has caused fatal acute renal failure; the substance should not be used.', 'La exposición al ácido aristolóquico ha causado falla renal aguda mortal; la sustancia no debe usarse.'),
        'identity' => $b19text('Do not confuse with Akebiae Caulis or Clematidis armandii Caulis; similar trade names are not interchangeable identities.', 'No confundir con Akebiae Caulis o Clematidis armandii Caulis; nombres comerciales similares no equivalen a identidad.'),
    ],
    [
        'name' => 'Guang Fang Ji',
        'latin' => 'Aristolochiae fangchi Radix',
        'meta' => $b19text('Southern fangji root · acrid, bitter and cold', 'Raíz fangji del sur · acre, amarga y fría'),
        'focus' => $b19text('Historically expelled Wind-Dampness, stopped pain and promoted urination.', 'Históricamente expulsaba Viento-Humedad, detenía dolor y promovía micción.'),
        'clue' => $b19text('The Aristolochia product mistakenly substituted for Han Fang Ji in slimming formulas.', 'El producto de Aristolochia sustituido erróneamente por Han Fang Ji en fórmulas para adelgazar.'),
        'safety' => $b19text('Linked to Belgian slimming-cocktail nephropathy, progressive renal fibrosis and urothelial cancer.', 'Vinculada a nefropatía por cócteles adelgazantes belgas, fibrosis renal progresiva y cáncer urotelial.'),
        'identity' => $b19text('Stephaniae tetrandrae Radix is a different herb; “fang ji” and “wood fang ji” naming requires exact species verification.', 'Stephaniae tetrandrae Radix es otra hierba; los nombres “fang ji” y “wood fang ji” exigen verificación exacta de especie.'),
    ],
    [
        'name' => 'Xun Gu Feng',
        'latin' => 'Aristolochiae mollissimae Herba',
        'meta' => $b19text('Mollissima herb · bitter and historically neutral', 'Hierba mollissima · amarga e históricamente neutra'),
        'focus' => $b19text('Historically dispelled Wind-Dampness and relieved joint, traumatic or abdominal pain.', 'Históricamente expulsaba Viento-Humedad y aliviaba dolor articular, traumático o abdominal.'),
        'clue' => $b19text('A hairy Aristolochia used for painful obstruction.', 'Una Aristolochia vellosa usada para obstrucción dolorosa.'),
        'safety' => $b19text('Contains aristolochic acid and carries the same nephrotoxic and carcinogenic class risk.', 'Contiene ácido aristolóquico y comparte el mismo riesgo nefrotóxico y carcinógeno de la clase.'),
        'identity' => $b19text('Common names such as “cat ear” or “hairy fragrance” are unreliable because they are shared with unrelated plants.', 'Nombres comunes como “oreja de gato” o “fragancia vellosa” son poco fiables porque se comparten con plantas no relacionadas.'),
    ],
    [
        'name' => 'Qing Fen',
        'latin' => 'Calomelas',
        'meta' => $b19text('Mercurous chloride mineral · acrid, cold and very toxic', 'Mineral de cloruro mercurioso · acre, frío y muy tóxico'),
        'focus' => $b19text('Historically used externally for parasites and nonhealing sores and internally as a drastic water-and-stool expellant.', 'Históricamente usado externamente para parásitos y llagas crónicas e internamente como expulsante drástico de agua y heces.'),
        'clue' => $b19text('“Light powder” that can dissociate into more toxic mercury compounds.', '“Polvo ligero” que puede disociarse en compuestos de mercurio más tóxicos.'),
        'safety' => $b19text('Mercury toxicity, mucosal injury, shock and local tissue necrosis make both internal and excessive topical use unacceptable.', 'Toxicidad por mercurio, lesión mucosa, choque y necrosis local hacen inaceptables el uso interno y el tópico excesivo.'),
        'identity' => $b19text('Light and heat accelerate formation of metallic mercury and corrosive mercuric chloride.', 'Luz y calor aceleran la formación de mercurio metálico y cloruro mercúrico corrosivo.'),
    ],
    [
        'name' => 'Xiong Huang',
        'latin' => 'Realgar',
        'meta' => $b19text('Arsenic sulfide mineral · acrid, warm and toxic', 'Mineral de sulfuro de arsénico · acre, tibio y tóxico'),
        'focus' => $b19text('Historically used mainly externally for toxic sores, scabies, tinea and bites.', 'Históricamente usado sobre todo externamente para llagas tóxicas, sarna, tiña y mordeduras.'),
        'clue' => $b19text('“Male yellow” arsenic mineral contrasted with orpiment or “female yellow.”', 'Mineral arsenical “amarillo macho” contrastado con oropimente o “amarillo hembra”.'),
        'safety' => $b19text('Heat and light can create more toxic arsenic products; arsenic may be absorbed through skin and cause fatal systemic poisoning.', 'Calor y luz pueden crear productos arsenicales más tóxicos; el arsénico puede absorberse por piel y causar intoxicación sistémica mortal.'),
        'identity' => $b19text('Aqueous trituration reduced arsenic content but did not make it a current guideline substance.', 'La trituración acuosa reducía el contenido de arsénico, pero no la convierte en sustancia del guideline vigente.'),
    ],
    [
        'name' => 'Mi Tuo Seng',
        'latin' => 'Lithargyrum',
        'meta' => $b19text('Lead oxide mineral · salty, acrid, neutral and toxic', 'Mineral de óxido de plomo · salado, acre, neutro y tóxico'),
        'focus' => $b19text('Historically absorbed seepage, reduced swelling and bleeding, and treated Damp skin lesions.', 'Históricamente absorbía exudación, reducía hinchazón y sangrado y trataba lesiones cutáneas húmedas.'),
        'clue' => $b19text('Lead-oxide topical powder compared with Minium.', 'Polvo tópico de óxido de plomo comparado con Minium.'),
        'safety' => $b19text('Acute or cumulative lead poisoning may cause anemia, neuropathy, gastrointestinal injury, hepatitis and renal damage.', 'La intoxicación aguda o acumulativa por plomo puede causar anemia, neuropatía, lesión gastrointestinal, hepatitis y daño renal.'),
        'identity' => $b19text('Mostly an artificially produced lead-oxide product rather than a naturally occurring medicinal mineral.', 'Principalmente producto de óxido de plomo fabricado, no mineral medicinal natural.'),
    ],
    [
        'name' => 'Peng Sha',
        'latin' => 'Borax',
        'meta' => $b19text('Borate mineral · sweet, salty and cool', 'Mineral borato · dulce, salado y fresco'),
        'focus' => $b19text('Historically cleared heat and phlegm and was applied to oral, throat and eye lesions.', 'Históricamente aclaraba Calor y Flema y se aplicaba a lesiones de boca, garganta y ojos.'),
        'clue' => $b19text('A relatively nonirritating crystal once compared with Calamina for mucosal and eye disorders.', 'Cristal relativamente no irritante antes comparado con Calamina para trastornos mucosos y oculares.'),
        'safety' => $b19text('Systemic absorption through injured skin or mucosa can be fatal, especially in newborns and children; use is restricted in many countries.', 'La absorción sistémica por piel lesionada o mucosa puede ser mortal, especialmente en recién nacidos y niños; su uso está restringido en muchos países.'),
        'identity' => $b19text('Calcining increased drying and astringency but did not remove systemic borate toxicity.', 'Calcinar aumentaba sequedad y astringencia, pero no eliminaba la toxicidad sistémica del borato.'),
    ],
];

$b19endangered = [
    [
        'name' => 'Xiong Dan',
        'latin' => 'Vesica Fellea Ursi',
        'meta' => $b19text('Bear gallbladder · historically bitter and cold', 'Vesícula biliar de oso · históricamente amarga y fría'),
        'focus' => $b19text('Historically cleared intense Liver heat, brightened the eyes and settled spasms.', 'Históricamente aclaraba Calor intenso de Hígado, beneficiaba los ojos y calmaba espasmos.'),
        'clue' => $b19text('Heat-toxin, red painful eyes or convulsions—now a conservation recognition item only.', 'Calor-Toxina, ojos rojos dolorosos o convulsiones; ahora solo elemento de reconocimiento por conservación.'),
        'safety' => $b19text('Obtaining the traditional product kills or exploits protected bears and drives illegal wildlife trade.', 'Obtener el producto tradicional mata o explota osos protegidos e impulsa el comercio ilegal de fauna.'),
        'identity' => $b19text('Bovine gall products and synthetic/biotechnological bile acids are the appropriate alternatives.', 'Productos biliares bovinos y ácidos biliares sintéticos/biotecnológicos son las alternativas apropiadas.'),
    ],
    [
        'name' => 'Chuan Shan Jia',
        'latin' => 'Manitis Squama',
        'meta' => $b19text('Pangolin scales · historically salty and slightly cold', 'Escamas de pangolín · históricamente saladas y ligeramente frías'),
        'focus' => $b19text('Historically moved Blood, opened menstruation and lactation, and promoted discharge of pus.', 'Históricamente movía Sangre, desbloqueaba menstruación y lactancia y promovía descarga de pus.'),
        'clue' => $b19text('Piercing treatment for stasis, closed abscess or insufficient lactation.', 'Tratamiento penetrante para estasis, absceso cerrado o lactancia insuficiente.'),
        'safety' => $b19text('Pangolins are protected and heavily trafficked; medicinal demand contributes directly to population collapse.', 'Los pangolines están protegidos y son objeto de tráfico intenso; la demanda medicinal contribuye directamente al colapso poblacional.'),
        'identity' => $b19text('Use function-specific substitutes such as Wang Bu Liu Xing, Zao Jiao Ci, Tu Bie Chong or Wa Leng Zi.', 'Usar sustitutos según función como Wang Bu Liu Xing, Zao Jiao Ci, Tu Bie Chong o Wa Leng Zi.'),
    ],
    [
        'name' => 'Dai Mao',
        'latin' => 'Eretmochelydis Carapax',
        'meta' => $b19text('Hawksbill turtle shell · historically sweet, salty and cold', 'Caparazón de tortuga carey · históricamente dulce, salado y frío'),
        'focus' => $b19text('Historically cleared heat, extinguished Wind, calmed convulsions and anchored Liver yang.', 'Históricamente aclaraba Calor, extinguía Viento, calmaba convulsiones y anclaba Yang de Hígado.'),
        'clue' => $b19text('Heart/Liver heat with impaired consciousness or convulsions.', 'Calor de Corazón/Hígado con alteración de conciencia o convulsiones.'),
        'safety' => $b19text('Hawksbill sea turtles are critically endangered; shell use requires killing the animal and supports illegal trade.', 'Las tortugas carey están en peligro crítico; usar el caparazón exige matar al animal y sostiene comercio ilegal.'),
        'identity' => $b19text('Shui Niu Jiao, Ling Yang Jiao or other legal heat/Wind alternatives should be selected by function.', 'Shui Niu Jiao, Ling Yang Jiao u otras alternativas legales para Calor/Viento deben seleccionarse según función.'),
    ],
    [
        'name' => 'Hou Zao',
        'latin' => 'Macacae mulattae Calculus',
        'meta' => $b19text('Macaque bezoar · historically bitter, salty and cold', 'Bezoar de macaco · históricamente amargo, salado y frío'),
        'focus' => $b19text('Historically dislodged phlegm, calmed wheezing and convulsions, cleared heat and softened nodules.', 'Históricamente desalojaba Flema, calmaba sibilancias y convulsiones, aclaraba Calor y ablandaba nódulos.'),
        'clue' => $b19text('A “monkey date” bezoar for severe phlegm-heat.', 'Un bezoar “dátil de mono” para Flema-Calor grave.'),
        'safety' => $b19text('Animal-derived bezoar from aged macaques; authenticity problems and wildlife impact make its use obsolete.', 'Bezoar animal de macacos viejos; problemas de autenticidad e impacto sobre fauna vuelven obsoleto su uso.'),
        'identity' => $b19text('Niu Huang is the recognized comparison and is stronger for heat, toxicity and opening the orifices.', 'Niu Huang es la comparación reconocida y es más fuerte para Calor, toxicidad y apertura de orificios.'),
    ],
    [
        'name' => 'Hu Gu',
        'latin' => 'Tigris Os',
        'meta' => $b19text('Tiger bone · historically acrid, salty and warming', 'Hueso de tigre · históricamente acre, salado y tibio'),
        'focus' => $b19text('Historically dispelled Wind, relieved wandering pain and strengthened sinews and bones.', 'Históricamente expulsaba Viento, aliviaba dolor migratorio y fortalecía tendones y huesos.'),
        'clue' => $b19text('Severe wandering Wind-Damp pain with weakness or spasm.', 'Dolor migratorio grave por Viento-Humedad con debilidad o espasmo.'),
        'safety' => $b19text('Tigers are protected and endangered; tiger-bone trade is illegal and fuels poaching and fraudulent animal products.', 'Los tigres están protegidos y amenazados; el comercio de hueso de tigre es ilegal y fomenta caza furtiva y productos animales fraudulentos.'),
        'identity' => $b19text('Use legal combinations built from herbs such as Mu Gua, Niu Xi and Wu Jia Pi according to the pattern.', 'Usar combinaciones legales con hierbas como Mu Gua, Niu Xi y Wu Jia Pi según el patrón.'),
    ],
];

$b19questions = [
    [$b19text('How many Chapter 19 substances appear in the current 2026 guideline?', '¿Cuántas sustancias del capítulo 19 aparecen en el guideline vigente 2026?'), '<strong>None.</strong>'],
    [$b19text('Which three obsolete entries form the high-yield aristolochic-acid identity trap?', '¿Qué tres entradas obsoletas forman la trampa principal de identidad por ácido aristolóquico?'), '<strong>Ma Dou Ling, Guan Mu Tong and Guang Fang Ji</strong> (with Xun Gu Feng sharing the same class hazard).'],
    [$b19text('Which Aristolochia product was confused with Han Fang Ji in the Belgian slimming-cocktail disaster?', '¿Qué producto de Aristolochia se confundió con Han Fang Ji en el desastre de los cócteles adelgazantes belgas?'), '<strong>Guang Fang Ji.</strong>'],
    [$b19text('Which obsolete “Mu Tong” caused fatal acute renal failure?', '¿Qué “Mu Tong” obsoleto causó falla renal aguda mortal?'), '<strong>Guan Mu Tong.</strong>'],
    [$b19text('Which red mineral contains mercury and becomes more dangerous when heated?', '¿Qué mineral rojo contiene mercurio y se vuelve más peligroso al calentarse?'), '<strong>Zhu Sha.</strong>'],
    [$b19text('Which “light powder” can dissociate into more toxic mercury compounds?', '¿Qué “polvo ligero” puede disociarse en compuestos de mercurio más tóxicos?'), '<strong>Qing Fen.</strong>'],
    [$b19text('Which seed can cause stimulus-triggered tetanic convulsions from strychnine?', '¿Qué semilla puede causar convulsiones tetánicas provocadas por estímulos debido a estricnina?'), '<strong>Ma Qian Zi.</strong>'],
    [$b19text('Which narcotic husk historically restrained chronic cough and diarrhea?', '¿Qué cáscara narcótica restringía históricamente tos y diarrea crónicas?'), '<strong>Ying Su Ke.</strong>'],
    [$b19text('Which highly toxic root was used for stubborn painful obstruction and recalcitrant skin disease?', '¿Qué raíz muy tóxica se usaba para obstrucción dolorosa resistente y enfermedad cutánea recalcitrante?'), '<strong>Lei Gong Teng.</strong>'],
    [$b19text('Which arsenic mineral becomes more toxic with heat or light?', '¿Qué mineral arsenical se vuelve más tóxico con calor o luz?'), '<strong>Xiong Huang.</strong>'],
    [$b19text('Which lead-oxide mineral historically absorbed seepage and treated Damp skin lesions?', '¿Qué mineral de óxido de plomo absorbía históricamente exudación y trataba lesiones cutáneas húmedas?'), '<strong>Mi Tuo Seng.</strong>'],
    [$b19text('Which endangered product should be replaced by bovine or synthetic bile products?', '¿Qué producto de especie amenazada debe sustituirse por productos biliares bovinos o sintéticos?'), '<strong>Xiong Dan.</strong>'],
    [$b19text('Which protected scales were historically used to move Blood and promote lactation?', '¿Qué escamas protegidas se usaban históricamente para mover Sangre y promover lactancia?'), '<strong>Chuan Shan Jia.</strong>'],
    [$b19text('Which critically endangered shell was compared with Shui Niu Jiao and Ling Yang Jiao?', '¿Qué caparazón de especie en peligro crítico se comparaba con Shui Niu Jiao y Ling Yang Jiao?'), '<strong>Dai Mao.</strong>'],
    [$b19text('Which illegal wildlife product historically treated wandering Wind-Damp pain and weak bones?', '¿Qué producto ilegal de fauna trataba históricamente dolor migratorio por Viento-Humedad y huesos débiles?'), '<strong>Hu Gu.</strong>'],
];
?>

<section aria-labelledby="bensky-nineteen-foundations">
    <h4 id="bensky-nineteen-foundations"><?php echo s($b19text('Safety foundation: recognition is not authorization', 'Fundamento de seguridad: reconocer no significa autorizar')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($b19text('Chapter 19 preserves historical materia-medica recognition while explicitly identifying products that should not be used because of unacceptable toxicity or harm to protected species. None is part of the current 304-substance Board roster.', 'El capítulo 19 preserva reconocimiento histórico de materia médica mientras identifica explícitamente productos que no deben usarse por toxicidad inaceptable o daño a especies protegidas. Ninguno forma parte del listado Board vigente de 304 sustancias.')); ?></p>
    <div class="single-herbs-guide__pattern">
        <article><strong><?php echo s($b19text('Mercury / lead / arsenic', 'Mercurio / plomo / arsénico')); ?></strong><span>Zhu Sha · Qing Fen · Xiong Huang · Mi Tuo Seng</span></article>
        <article><strong><?php echo s($b19text('Aristolochic acid', 'Ácido aristolóquico')); ?></strong><span>Ma Dou Ling · Guan Mu Tong · Guang Fang Ji · Xun Gu Feng</span></article>
        <article><strong><?php echo s($b19text('Narrow-margin toxins', 'Toxinas de margen estrecho')); ?></strong><span>Ying Su Ke · Ma Qian Zi · Lei Gong Teng · Peng Sha</span></article>
        <article><strong><?php echo s($b19text('Protected wildlife', 'Fauna protegida')); ?></strong><span>Xiong Dan · Chuan Shan Jia · Dai Mao · Hou Zao · Hu Gu</span></article>
    </div>
    <aside class="single-herbs-guide__safety"><strong><?php echo s($b19text('Category rule', 'Regla de la categoría')); ?></strong> <?php echo s($b19text('Do not interpret historical actions, preparations or comparisons as prescribing guidance. Recognition questions should lead to avoidance, substitution, exact species verification and legal conservation practice.', 'No interpretar acciones, preparaciones o comparaciones históricas como guía de prescripción. Las preguntas de reconocimiento deben conducir a evitar, sustituir, verificar especie exacta y respetar la conservación legal.')); ?></aside>
</section>

<section class="single-herbs-guide__section single-herbs-guide__chapter-atlas" aria-labelledby="bensky-nineteen-atlas">
    <h4 id="bensky-nineteen-atlas"><?php echo s($b19text('Source-chapter atlas: 17 obsolete substances', 'Atlas del capítulo fuente: 17 sustancias obsoletas')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($b19text('Current guideline status: 0 official entries. All profiles below are source-only safety and historical-recognition material.', 'Estado en el guideline vigente: 0 entradas oficiales. Todos los perfiles siguientes son material fuente únicamente para seguridad y reconocimiento histórico.')); ?></p>

    <h5 class="single-herbs-guide__catalog-heading"><?php echo s($b19text('Section 1 · 12 substances with unacceptable toxicity', 'Sección 1 · 12 sustancias con toxicidad inaceptable')); ?></h5>
    <ol class="single-herbs-guide__category-herbs">
        <?php foreach ($b19toxicity as $herb): ?>
            <li class="single-herbs-guide__category-herb">
                <div class="single-herbs-guide__category-herb-head"><strong><?php echo s($herb['name']); ?></strong><em><?php echo s($herb['latin']); ?></em><span class="single-herbs-guide__category-herb-badge single-herbs-guide__category-herb-badge--outside"><?php echo s($b19text('Obsolete · toxicity', 'Obsoleta · toxicidad')); ?></span></div>
                <span class="single-herbs-guide__category-herb-common"><?php echo s($herb['meta']); ?></span><p><?php echo s($herb['focus']); ?></p>
                <div class="single-herbs-guide__category-herb-context"><p><strong><?php echo s($b19text('Historical clue', 'Pista histórica')); ?></strong><span><?php echo s($herb['clue']); ?></span></p><p><strong><?php echo s($b19text('Why obsolete', 'Por qué es obsoleta')); ?></strong><span><?php echo s($herb['safety']); ?></span></p><p><strong><?php echo s($b19text('Identity / processing', 'Identidad / proceso')); ?></strong><span><?php echo s($herb['identity']); ?></span></p></div>
            </li>
        <?php endforeach; ?>
    </ol>

    <div class="single-herbs-guide__annex">
        <h5 class="single-herbs-guide__catalog-heading"><?php echo s($b19text('Section 2 · 5 substances from endangered or protected species', 'Sección 2 · 5 sustancias de especies amenazadas o protegidas')); ?></h5>
        <p class="single-herbs-guide__catalog-intro"><?php echo s($b19text('The therapeutic study point is the legal, ethical substitute—not procurement or preparation of the animal product.', 'El punto de estudio terapéutico es el sustituto legal y ético, no la obtención o preparación del producto animal.')); ?></p>
        <ol class="single-herbs-guide__category-herbs single-herbs-guide__category-herbs--continue-twelve">
            <?php foreach ($b19endangered as $herb): ?>
                <li class="single-herbs-guide__category-herb">
                    <div class="single-herbs-guide__category-herb-head"><strong><?php echo s($herb['name']); ?></strong><em><?php echo s($herb['latin']); ?></em><span class="single-herbs-guide__category-herb-badge single-herbs-guide__category-herb-badge--outside"><?php echo s($b19text('Obsolete · protected wildlife', 'Obsoleta · fauna protegida')); ?></span></div>
                    <span class="single-herbs-guide__category-herb-common"><?php echo s($herb['meta']); ?></span><p><?php echo s($herb['focus']); ?></p>
                    <div class="single-herbs-guide__category-herb-context"><p><strong><?php echo s($b19text('Historical clue', 'Pista histórica')); ?></strong><span><?php echo s($herb['clue']); ?></span></p><p><strong><?php echo s($b19text('Why obsolete', 'Por qué es obsoleta')); ?></strong><span><?php echo s($herb['safety']); ?></span></p><p><strong><?php echo s($b19text('Legal substitute', 'Sustituto legal')); ?></strong><span><?php echo s($herb['identity']); ?></span></p></div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="bensky-nineteen-comparisons">
    <h4 id="bensky-nineteen-comparisons"><?php echo s($b19text('High-yield distinctions', 'Distinciones de alto rendimiento')); ?></h4>
    <div class="single-herbs-guide__decision">
        <div><strong>Ma Dou Ling · Guan Mu Tong · Guang Fang Ji</strong><span><?php echo s($b19text('Fruit for Lung heat, stem mislabeled as Mu Tong, and root confused with Han Fang Ji—three identities, one unacceptable aristolochic-acid hazard.', 'Fruto para Calor de Pulmón, tallo mal identificado como Mu Tong y raíz confundida con Han Fang Ji: tres identidades, un peligro inaceptable por ácido aristolóquico.')); ?></span></div>
        <div><strong>Zhu Sha vs. Qing Fen</strong><span><?php echo s($b19text('Cinnabar mercury sulfide vs. calomel mercurous chloride; both obsolete mercury products whose processing can generate more toxic compounds.', 'Sulfuro de mercurio cinabrio vs. cloruro mercurioso calomel; ambos productos obsoletos de mercurio cuyo procesamiento puede generar compuestos más tóxicos.')); ?></span></div>
        <div><strong>Xiong Huang vs. Mi Tuo Seng</strong><span><?php echo s($b19text('Arsenic sulfide vs. lead oxide: neither topical tradition prevents systemic heavy-metal poisoning.', 'Sulfuro de arsénico vs. óxido de plomo: ninguna tradición tópica evita intoxicación sistémica por metales pesados.')); ?></span></div>
        <div><strong>Xiong Dan vs. Niu Huang / bile alternatives</strong><span><?php echo s($b19text('Bear gall is obsolete because of wildlife harm; bovine or synthetic bile products provide legal alternatives according to the intended function.', 'La bilis de oso es obsoleta por daño a fauna; productos biliares bovinos o sintéticos ofrecen alternativas legales según la función buscada.')); ?></span></div>
        <div><strong>Chuan Shan Jia vs. Zao Jiao Ci / Wang Bu Liu Xing</strong><span><?php echo s($b19text('Replace the pangolin product by function: pierce a closed sore with Zao Jiao Ci or promote lactation with Wang Bu Liu Xing.', 'Sustituir el producto de pangolín según función: penetrar absceso cerrado con Zao Jiao Ci o promover lactancia con Wang Bu Liu Xing.')); ?></span></div>
        <div><strong>Dai Mao vs. Shui Niu Jiao / Ling Yang Jiao</strong><span><?php echo s($b19text('The protected turtle shell is not required: choose a legal heat-clearing or Wind-extinguishing substitute according to the pattern.', 'El caparazón protegido no es necesario: elegir sustituto legal para aclarar Calor o extinguir Viento según el patrón.')); ?></span></div>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="bensky-nineteen-safety">
    <h4 id="bensky-nineteen-safety"><?php echo s($b19text('Exam-priority safety rules', 'Reglas de seguridad prioritarias para examen')); ?></h4>
    <div class="single-herbs-guide__safety">
        <strong><?php echo s($b19text('Aristolochia means stop', 'Aristolochia significa detenerse')); ?></strong><p><?php echo s($b19text('Any suspected Aristolochia identity requires rejection and exact botanical verification. Processing, honey, dose reduction or a familiar trade name does not neutralize aristolochic acid.', 'Toda identidad sospechosa de Aristolochia exige rechazo y verificación botánica exacta. Procesamiento, miel, reducción de dosis o nombre comercial familiar no neutralizan el ácido aristolóquico.')); ?></p>
        <strong><?php echo s($b19text('External does not mean safe', 'Externo no significa seguro')); ?></strong><p><?php echo s($b19text('Mercury, arsenic, lead and borate compounds may enter through damaged skin, mucosa or inhalation. Topical history is not evidence of an acceptable modern risk.', 'Compuestos de mercurio, arsénico, plomo y borato pueden entrar por piel dañada, mucosa o inhalación. Historia tópica no demuestra riesgo moderno aceptable.')); ?></p>
        <strong><?php echo s($b19text('Conservation is part of clinical safety', 'La conservación forma parte de la seguridad clínica')); ?></strong><p><?php echo s($b19text('Do not purchase, prescribe or normalize products from protected wildlife. Identify the historical function and select a legal plant, mineral or manufactured substitute.', 'No comprar, prescribir ni normalizar productos de fauna protegida. Identificar la función histórica y elegir sustituto legal vegetal, mineral o manufacturado.')); ?></p>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="bensky-nineteen-questions">
    <h4 id="bensky-nineteen-questions"><?php echo s($b19text('Chapter 19 recall questions', 'Preguntas de repaso del capítulo 19')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($b19text('Answer mentally before opening each item.', 'Responde mentalmente antes de abrir cada elemento.')); ?></p>
    <div class="single-herbs-guide__recall">
        <?php foreach ($b19questions as $index => $question): ?><details class="single-herbs-guide__final-question"><summary><span class="single-herbs-guide__final-number"><?php echo s($b19text('Question', 'Pregunta') . ' ' . ($index + 1)); ?></span><span><?php echo s($question[0]); ?></span></summary><div class="single-herbs-guide__final-answer"><?php echo $question[1]; ?></div></details><?php endforeach; ?>
    </div>
</section>

<p class="single-herbs-guide__note single-herbs-guide__source"><strong><?php echo s($b19text('Sources:', 'Fuentes:')); ?></strong> <?php echo s($b19text('Bensky et al., Chinese Herbal Medicine: Materia Medica, 3rd ed., Chapter 19, scanned pp. 1043–1069 supplied for this module; current 2026 NCBAHM Appendix A. Historical recognition and safety education only; not prescribing or procurement guidance.', 'Bensky et al., Chinese Herbal Medicine: Materia Medica, 3.ª ed., capítulo 19, pp. escaneadas 1043–1069 suministradas para este módulo; Appendix A vigente del NCBAHM 2026. Solo reconocimiento histórico y educación de seguridad; no guía de prescripción ni adquisición.')); ?></p>
