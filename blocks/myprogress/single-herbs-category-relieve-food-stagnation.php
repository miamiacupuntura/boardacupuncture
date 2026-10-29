<?php
// Full study content for herbs that relieve Food Stagnation.
defined('MOODLE_INTERNAL') || die();

$foodtext = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};

$foodherbs = [
    [
        'name' => 'Ji Nei Jin',
        'latin' => 'Galli Gigerii Endothelium Corneum',
        'properties' => $foodtext('Sweet; neutral', 'Dulce; neutra'),
        'channels' => $foodtext('Spleen, Stomach, Small Intestine, Bladder', 'Bazo, Estómago, Intestino Delgado, Vejiga'),
        'dose' => '3–9 g',
        'identity' => $foodtext('Strongly digests every type of retained food, strengthens the Stomach, dissolves stones and secures essence and urine.', 'Digiere con fuerza todo tipo de alimento retenido, fortalece Estómago, disuelve cálculos y asegura Esencia y orina.'),
        'clue' => $foodtext('Mixed or stubborn Food Stagnation, especially with childhood malnutrition, stones, enuresis or seminal leakage.', 'Estancamiento alimentario mixto o persistente, especialmente con desnutrición infantil, cálculos, enuresis o pérdida seminal.'),
        'memory' => $foodtext('The chicken gizzard mill grinds every meal and even wears down stones.', 'El molino de la molleja de pollo tritura cualquier comida e incluso desgasta cálculos.'),
        'unique' => $foodtext('Yang gives it the highest index for both grain/fruit digestion and cereal/alcohol digestion; it also remains strong for meat and fat.', 'Yang le da el índice más alto tanto para trigo/arroz/fruta como para cereales/alcohol; también permanece fuerte para carne y grasa.'),
        'caution' => $foodtext('Strongly disperses accumulation; use carefully when there is marked Spleen-Stomach deficiency without retained food.', 'Dispersa acumulación con fuerza; usar con cuidado en deficiencia marcada de Bazo-Estómago sin alimento retenido.'),
        'formulas' => [],
    ],
    [
        'name' => 'Lai Fu Zi',
        'latin' => 'Raphani Semen',
        'properties' => $foodtext('Acrid, sweet; neutral', 'Acre, dulce; neutra'),
        'channels' => $foodtext('Lung, Spleen, Stomach', 'Pulmón, Bazo, Estómago'),
        'dose' => '6–12 g',
        'identity' => $foodtext('Reduces Food Stagnation, moves and descends Qi, transforms Phlegm and relieves fullness.', 'Reduce estancamiento alimentario, mueve y desciende Qi, transforma Flema y alivia plenitud.'),
        'clue' => $foodtext('Grain or starch retention with marked distension, nausea, vomiting, constipation, cough or copious Phlegm.', 'Retención de granos o almidones con distensión marcada, náusea, vómito, estreñimiento, tos o Flema abundante.'),
        'memory' => $foodtext('The radish seed clears the jammed grain conveyor, lowers the elevator and sweeps Phlegm from the Lung.', 'La semilla de rábano despeja la cinta de granos, baja el elevador y barre Flema del Pulmón.'),
        'unique' => $foodtext('Strongest of Lai Fu Zi, Mai Ya and Gu Ya for wheat, rice and fruit retention; unlike the other two it strongly moves Qi and bowel movement.', 'La más fuerte de Lai Fu Zi, Mai Ya y Gu Ya para retención de trigo, arroz y fruta; a diferencia de las otras mueve Qi e intestino con fuerza.'),
        'caution' => $foodtext('Traditionally said to reduce the tonic effect of Ren Shen; use cautiously in Qi deficiency without stagnation.', 'Tradicionalmente se considera que reduce el efecto tónico de Ren Shen; usar con cautela en deficiencia de Qi sin estancamiento.'),
        'formulas' => [],
    ],
    [
        'name' => 'Shan Zha',
        'latin' => 'Crataegi Fructus',
        'properties' => $foodtext('Sour, sweet; slightly warm', 'Ácida, dulce; ligeramente tibia'),
        'channels' => $foodtext('Spleen, Stomach, Liver', 'Bazo, Estómago, Hígado'),
        'dose' => '9–12 g',
        'identity' => $foodtext('Digests meat, fat and dairy accumulation while moving Blood and dispersing stasis.', 'Digiere acumulación de carne, grasa y lácteos mientras mueve Sangre y dispersa estasis.'),
        'clue' => $foodtext('Food retention after rich or greasy meals, especially with postpartum abdominal pain, amenorrhea or fixed Blood-stasis pain.', 'Retención tras comidas ricas o grasosas, especialmente con dolor abdominal posparto, amenorrea o dolor fijo por estasis de Sangre.'),
        'memory' => $foodtext('The hawthorn butcher cuts meat and fat, then clears the Blood traffic behind the kitchen.', 'El carnicero de espino corta carne y grasa y luego despeja el tráfico de Sangre detrás de la cocina.'),
        'unique' => $foodtext('Yang gives it the maximum meat-and-fat digestion index, 100; its Liver entry and Blood-moving action distinguish it from the other digestives.', 'Yang le da el índice máximo para digerir carne y grasa, 100; su entrada al Hígado y acción sobre Sangre la distinguen de las otras digestivas.'),
        'caution' => $foodtext('Sour and dispersing; use cautiously with gastric hyperacidity, active ulceration or pregnancy when Blood-moving is inappropriate.', 'Ácida y dispersante; usar con cautela en hiperacidez, ulceración activa o embarazo cuando mover Sangre sea inapropiado.'),
        'formulas' => [[63, 'bao-he-wan', 'Bao He Wan', $foodtext('chief', 'principal')]],
    ],
    [
        'name' => 'Mai Ya',
        'latin' => 'Hordei Fructus germinatus',
        'properties' => $foodtext('Sweet; neutral', 'Dulce; neutra'),
        'channels' => $foodtext('Spleen, Stomach, Liver', 'Bazo, Estómago, Hígado'),
        'dose' => '9–15 g',
        'identity' => $foodtext('Digests starches and grains, strengthens the Stomach, courses constrained Liver Qi and stops lactation.', 'Digiere almidones y granos, fortalece Estómago, moviliza Qi de Hígado constreñido y detiene lactancia.'),
        'clue' => $foodtext('Wheat, rice or fruit retention plus a Liver-Qi or lactation clue.', 'Retención de trigo, arroz o fruta más una pista de Qi de Hígado o lactancia.'),
        'memory' => $foodtext('The barley sprout clears the bread shelf, opens the Liver side door and turns off the milk tap.', 'El brote de cebada despeja el estante de pan, abre la puerta lateral del Hígado y cierra el grifo de leche.'),
        'unique' => $foodtext('Stronger than Gu Ya but gentler than Lai Fu Zi for grain and fruit retention; the only herb in this list with the classic lactation-stopping clue.', 'Más fuerte que Gu Ya pero más suave que Lai Fu Zi para granos y frutas; la única de esta lista con la pista clásica de detener lactancia.'),
        'caution' => $foodtext('Avoid when continued breastfeeding is desired; large doses are traditionally used to suppress lactation.', 'Evitar cuando se desea continuar amamantando; dosis altas se usan tradicionalmente para suprimir lactancia.'),
        'formulas' => [],
    ],
    [
        'name' => 'Gu Ya',
        'latin' => 'Oryzae Fructus germinatus',
        'properties' => $foodtext('Sweet; neutral', 'Dulce; neutra'),
        'channels' => $foodtext('Spleen, Stomach', 'Bazo, Estómago'),
        'dose' => '9–15 g',
        'identity' => $foodtext('Gently improves digestion of grains and starches while strengthening the Spleen and awakening appetite.', 'Mejora suavemente la digestión de granos y almidones mientras fortalece Bazo y despierta apetito.'),
        'clue' => $foodtext('Chronic weak digestion, poor appetite or mild Food Stagnation in children, older adults or deficient patients.', 'Digestión débil crónica, poco apetito o estancamiento leve en niños, adultos mayores o pacientes deficientes.'),
        'memory' => $foodtext('The rice sprout is the gentle kitchen helper who can work longer without exhausting the weak patient.', 'El brote de arroz es el ayudante suave de cocina que puede trabajar más tiempo sin agotar al paciente débil.'),
        'unique' => $foodtext('Weakest and gentlest digestive in Yang’s grain comparisons, but the most tonifying and suitable for longer use in deficiency.', 'La digestiva más débil y suave en las comparaciones de granos de Yang, pero la más tonificante y adecuada para uso más prolongado en deficiencia.'),
        'caution' => $foodtext('Do not mistake gentleness for an indication to treat persistent obstruction without evaluation.', 'No confundir suavidad con indicación para tratar obstrucción persistente sin evaluación.'),
        'formulas' => [],
    ],
    [
        'name' => 'Shen Qu',
        'latin' => 'Massa Medicata Fermentata',
        'properties' => $foodtext('Acrid, sweet; warm', 'Acre, dulce; tibia'),
        'channels' => $foodtext('Spleen, Stomach', 'Bazo, Estómago'),
        'dose' => '6–15 g',
        'identity' => $foodtext('Digests grains and heavy starchy foods, moves Qi, harmonizes the Stomach and dispels the effects of alcohol.', 'Digiere granos y almidones pesados, mueve Qi, armoniza Estómago y disipa los efectos del alcohol.'),
        'clue' => $foodtext('Cereal or alcohol retention with pronounced epigastric distension, heaviness or explosive diarrhea.', 'Retención de cereales o alcohol con distensión epigástrica marcada, pesadez o diarrea explosiva.'),
        'memory' => $foodtext('The fermented kitchen culture breaks down heavy bread and cleans up after the wine banquet.', 'El fermento de cocina descompone pan pesado y limpia después del banquete con vino.'),
        'unique' => $foodtext('Stronger and more Qi-moving than Gu Ya; Yang specifically distinguishes its ability to dispel alcohol effects.', 'Más fuerte y movilizadora de Qi que Gu Ya; Yang distingue específicamente su capacidad para disipar efectos del alcohol.'),
        'caution' => $foodtext('Contains fermented grain material; verify product composition when grain sensitivity matters.', 'Contiene material de granos fermentados; verificar composición del producto cuando importe la sensibilidad a granos.'),
        'formulas' => [],
    ],
];

$foodcharts = [
    [
        'title' => $foodtext('Digest wheat, rice and fruits', 'Digerir trigo, arroz y frutas'),
        'figure' => $foodtext('Yang figure 9.1 · relative study indices', 'Figura 9.1 de Yang · índices relativos de estudio'),
        'items' => ['Ji Nei Jin' => 100, 'Lai Fu Zi' => 80, 'Mai Ya' => 60, 'Gu Ya' => 40],
    ],
    [
        'title' => $foodtext('Digest cereals / dispel alcohol effects', 'Digerir cereales / disipar efectos del alcohol'),
        'figure' => $foodtext('Yang figure 9.2 · relative study indices', 'Figura 9.2 de Yang · índices relativos de estudio'),
        'items' => ['Ji Nei Jin' => 100, 'Shen Qu' => 80, 'Gu Ya' => 60],
    ],
    [
        'title' => $foodtext('Digest meat and remove fat', 'Digerir carne y eliminar grasa'),
        'figure' => $foodtext('Yang figure 9.3 · current-list values', 'Figura 9.3 de Yang · valores de la lista vigente'),
        'items' => ['Shan Zha' => 100, 'Ji Nei Jin' => 80],
    ],
];

$foodcomparisons = [
    ['/study-guide/comparisons/ji-nei-jin-vs-shan-zha.html', '/study-guide/comparisons/ji-nei-jin-vs-shan-zha-es.html', 'Ji Nei Jin vs. Shan Zha', $foodtext('All foods and stones versus meat, fat and Blood stasis.', 'Todos los alimentos y cálculos frente a carne, grasa y estasis de Sangre.')],
    ['/study-guide/comparisons/gu-ya-vs-mai-ya.html', '/study-guide/comparisons/gu-ya-vs-mai-ya-es.html', 'Gu Ya vs. Mai Ya', $foodtext('Gentle deficiency-friendly rice sprout versus stronger barley sprout with Liver and lactation clues.', 'Brote de arroz suave para deficiencia frente a brote de cebada más fuerte con pistas de Hígado y lactancia.')],
    ['/study-guide/comparisons/shen-qu-vs-gu-ya.html', '/study-guide/comparisons/shen-qu-vs-gu-ya-es.html', 'Shen Qu vs. Gu Ya', $foodtext('Stronger fermented grain/alcohol digestion versus gentle Spleen support.', 'Digestión más fuerte de granos fermentados/alcohol frente a apoyo suave del Bazo.')],
    ['/study-guide/comparisons/lai-fu-zi-vs-bai-jie-zi.html', '/study-guide/comparisons/lai-fu-zi-vs-bai-jie-zi-es.html', 'Lai Fu Zi vs. Bai Jie Zi', $foodtext('Neutral organ-level Food-Phlegm regulation versus strongly warm channel-level Cold-Phlegm dispersion.', 'Regulación neutra de Alimento-Flema en órganos frente a dispersión tibia intensa de Flema-Frío en canales.')],
];

$foodquestions = [
    [
        $foodtext('What are the causes and indications for Food Stagnation, and what precautions should be observed when using herbs that relieve it?', '¿Cuáles son las causas e indicaciones del estancamiento alimentario y qué precauciones deben observarse al usar hierbas que lo alivian?'),
        $foodtext('Weak digestion in children, older adults or chronic deficiency, and acute overeating can obstruct Middle-Jiao Qi. Look for fullness, foul belching or vomiting, distending pain, altered stool and a thick greasy coating. Use digestive herbs briefly and support Spleen-Stomach deficiency.', 'Digestión débil en niños, adultos mayores o deficiencia crónica y comer en exceso pueden obstruir Qi del Jiao Medio. Busca plenitud, eructos o vómito fétidos, dolor distensivo, heces alteradas y capa gruesa-grasosa. Usa digestivas por poco tiempo y apoya deficiencia de Bazo-Estómago.'),
    ],
    [
        $foodtext('What are the characteristics of Mai Ya (Hordei Fructus germinatus), Shen Qu (Massa Medicata Fermentata) and Shan Zha (Crataegi Fructus)?', '¿Cuáles son las características de Mai Ya (Hordei Fructus germinatus), Shen Qu (Massa Medicata Fermentata) y Shan Zha (Crataegi Fructus)?'),
        $foodtext('Mai Ya favors wheat, rice and fruit; Shen Qu favors cereals and alcohol; Shan Zha favors meat, fat and dairy and also moves Blood. Deep-dry-fried together they form Jiao San Xian.', 'Mai Ya favorece trigo, arroz y fruta; Shen Qu favorece cereales y alcohol; Shan Zha favorece carne, grasa y lácteos y además mueve Sangre. Tostadas profundamente juntas forman Jiao San Xian.'),
    ],
    [
        $foodtext('Lai Fu Zi, Mai Ya and Gu Ya all aid digestion of wheat, rice and fruits. What are the differences between their actions?', 'Lai Fu Zi, Mai Ya y Gu Ya ayudan a digerir trigo, arroz y frutas. ¿Cuáles son las diferencias entre sus acciones?'),
        $foodtext('Lai Fu Zi is strongest and also moves Qi, descends Lung Qi, transforms Phlegm and promotes bowel movement. Mai Ya is gentler and dissolves grain and fruit retention without that strong Qi action. Gu Ya is weakest but gently strengthens Spleen-Stomach and suits deficiency.', 'Lai Fu Zi es la más fuerte y además mueve Qi, desciende Qi de Pulmón, transforma Flema y promueve intestino. Mai Ya es más suave y disuelve retención de granos y frutas sin esa fuerte acción de Qi. Gu Ya es la más débil, pero fortalece suavemente Bazo-Estómago y conviene en deficiencia.'),
    ],
    [
        $foodtext('Shen Qu and Gu Ya both aid digestion of grains. What are the differences between their actions?', 'Shen Qu y Gu Ya ayudan a digerir granos. ¿Cuáles son las diferencias entre sus acciones?'),
        $foodtext('Shen Qu is stronger, warmer and more Qi-moving, reduces heavy cereal retention and dispels alcohol effects. Gu Ya is much gentler, strengthens Spleen-Stomach and can be used longer in deficient patients.', 'Shen Qu es más fuerte, tibia y movilizadora de Qi, reduce retención de cereales pesados y disipa efectos del alcohol. Gu Ya es mucho más suave, fortalece Bazo-Estómago y puede usarse más tiempo en pacientes deficientes.'),
    ],
    [
        $foodtext('What are the characteristics of Ji Nei Jin (Galli Gigerii Endothelium Corneum) and Shan Zha (Crataegi Fructus)?', '¿Cuáles son las características de Ji Nei Jin (Galli Gigerii Endothelium Corneum) y Shan Zha (Crataegi Fructus)?'),
        $foodtext('Both strongly digest meat and fat. Ji Nei Jin digests every food type and adds stones, enuresis and essence clues. Shan Zha specializes in rich foods and adds Liver-channel Blood movement and stasis clues.', 'Ambas digieren con fuerza carne y grasa. Ji Nei Jin digiere todo tipo de alimento y añade pistas de cálculos, enuresis y Esencia. Shan Zha se especializa en alimentos ricos y añade movimiento de Sangre del canal de Hígado y estasis.'),
    ],
];
?>

<section class="single-herbs-guide__section" aria-labelledby="topic-seventeen-foundations">
    <h4 id="topic-seventeen-foundations"><?php echo s($foodtext('Herbs that relieve Food Stagnation: general introduction', 'Hierbas que alivian estancamiento alimentario: introducción general')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($foodtext('The Board distinction is not simply “indigestion.” First identify the retained food, then the needed secondary action: move Qi, transform Phlegm, move Blood, stop lactation or gently support deficiency.', 'La distinción Board no es simplemente “indigestión”. Primero identifica el alimento retenido y luego la acción secundaria: mover Qi, transformar Flema, mover Sangre, detener lactancia o apoyar suavemente la deficiencia.')); ?></p>
    <div class="single-herbs-guide__decision">
        <div><strong><?php echo s($foodtext('Every food + stones', 'Todo alimento + cálculos')); ?></strong><span>Ji Nei Jin</span></div>
        <div><strong><?php echo s($foodtext('Grain + Qi/Phlegm', 'Granos + Qi/Flema')); ?></strong><span>Lai Fu Zi</span></div>
        <div><strong><?php echo s($foodtext('Meat/fat + Blood', 'Carne/grasa + Sangre')); ?></strong><span>Shan Zha</span></div>
        <div><strong><?php echo s($foodtext('Weak digestion', 'Digestión débil')); ?></strong><span>Gu Ya</span></div>
    </div>
    <aside class="single-herbs-guide__memory-path">
        <p class="single-herbs-guide__memory-line"><?php echo s($foodtext('Enter the blocked food factory.', 'Entra a la fábrica de alimentos bloqueada.')); ?></p>
        <p><?php echo s($foodtext('Ji Nei Jin is the universal grinder. Lai Fu Zi clears the grain conveyor and lowers Qi. Shan Zha is the butcher who cuts fat and moves Blood. Mai Ya clears the bread shelf and closes the milk tap. Gu Ya is the gentle rice-sprout helper. Shen Qu is the ferment that digests heavy grain and cleans up alcohol.', 'Ji Nei Jin es el molino universal. Lai Fu Zi despeja la cinta de granos y baja Qi. Shan Zha es el carnicero que corta grasa y mueve Sangre. Mai Ya despeja el estante de pan y cierra el grifo de leche. Gu Ya es el ayudante suave de brote de arroz. Shen Qu es el fermento que digiere granos pesados y limpia el alcohol.')); ?></p>
        <p><strong><?php echo s($foodtext('Food type → strength → secondary action → patient constitution.', 'Tipo de alimento → fuerza → acción secundaria → constitución del paciente.')); ?></strong></p>
    </aside>
    <p class="single-herbs-guide__scope-note"><strong><?php echo s($foodtext('Current Board list:', 'Lista Board vigente:')); ?></strong> <?php echo s($foodtext('the six requested herbs are shown once each. Herbs outside this category are labeled whenever they appear in a source comparison.', 'las seis hierbas solicitadas aparecen una vez cada una. Las hierbas fuera de esta categoría se identifican cuando aparecen en una comparación de fuente.')); ?></p>
    <ol class="single-herbs-guide__category-herbs">
        <?php foreach ($foodherbs as $herb): ?>
            <li class="single-herbs-guide__category-herb single-herbs-guide__category-herb--core"><div class="single-herbs-guide__category-herb-head"><strong><?php echo s($herb['name']); ?></strong><em><?php echo s($herb['latin']); ?></em><span class="single-herbs-guide__category-herb-badge"><?php echo s($foodtext('Current category list', 'Lista vigente de la categoría')); ?></span></div><p><?php echo s($herb['identity']); ?></p></li>
        <?php endforeach; ?>
    </ol>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-seventeen-yang">
    <h4 id="topic-seventeen-yang"><?php echo s($foodtext('Yang comparative fingerprints', 'Huellas comparativas de Yang')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($foodtext('These graphs answer three practical questions: which herb handles grains and fruit, which handles cereals and alcohol, and which handles meat and fat? Values are relative study indices, not doses or clinical percentages.', 'Estas gráficas responden tres preguntas prácticas: ¿cuál maneja granos y frutas, cuál cereales y alcohol, y cuál carne y grasa? Los valores son índices relativos de estudio, no dosis ni porcentajes clínicos.')); ?></p>
    <div class="single-herbs-guide__yang-charts">
        <?php foreach ($foodcharts as $chart): ?>
            <article class="single-herbs-guide__yang-chart"><h5><?php echo s($chart['title']); ?></h5><small><?php echo s($chart['figure']); ?></small><?php foreach ($chart['items'] as $name => $value): ?><div class="single-herbs-guide__yang-bar"><span><?php echo s($name); ?></span><span class="single-herbs-guide__yang-bar-track"><span class="single-herbs-guide__yang-bar-fill" style="width:<?php echo (int) $value; ?>%"></span></span><b><?php echo (int) $value; ?></b></div><?php endforeach; ?></article>
        <?php endforeach; ?>
    </div>
    <p class="single-herbs-guide__yang-note"><strong><?php echo s($foodtext('Outside-list note:', 'Nota fuera de lista:')); ?></strong> <?php echo s($foodtext('Yang figure 9.3 also includes A Wei between Shan Zha and Ji Nei Jin; it is omitted from the bars because it is not in this current category list.', 'La figura 9.3 de Yang también incluye A Wei entre Shan Zha y Ji Nei Jin; se omite de las barras porque no está en esta lista vigente de categoría.')); ?></p>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-seventeen-profiles">
    <h4 id="topic-seventeen-profiles"><?php echo s($foodtext('Individual herb profiles', 'Perfiles individuales de las hierbas')); ?></h4>
    <div class="single-herbs-guide__profiles">
        <?php foreach ($foodherbs as $herb): ?>
            <details class="single-herbs-guide__profile"<?php echo $herb['name'] === 'Ji Nei Jin' ? ' open' : ''; ?>><summary><span class="single-herbs-guide__profile-name"><?php echo s($herb['name']); ?></span><span class="single-herbs-guide__profile-latin"><?php echo s($herb['latin']); ?></span><span class="single-herbs-guide__profile-meta"><?php echo s($herb['properties']); ?> · <?php echo s($herb['channels']); ?></span></summary><div class="single-herbs-guide__profile-body"><dl>
                <dt><?php echo s($foodtext('Study dose', 'Dosis de estudio')); ?></dt><dd><?php echo s($herb['dose']); ?></dd>
                <dt><?php echo s($foodtext('Identity', 'Identidad')); ?></dt><dd><?php echo s($herb['identity']); ?></dd>
                <dt><?php echo s($foodtext('Exam clue', 'Pista de examen')); ?></dt><dd><?php echo s($herb['clue']); ?></dd>
                <dt><?php echo s($foodtext('Memory', 'Memoria')); ?></dt><dd class="single-herbs-guide__profile-memory"><?php echo s($herb['memory']); ?></dd>
                <dt><?php echo s($foodtext('Unique fingerprint', 'Huella única')); ?></dt><dd><?php echo s($herb['unique']); ?></dd>
                <dt><?php echo s($foodtext('Formula boundary', 'Límite de fórmulas')); ?></dt><dd class="single-herbs-guide__profile-formulas"><?php if ($herb['formulas']): foreach ($herb['formulas'] as $index => $formula): ?><?php if ($index): ?> · <?php endif; ?><?php $formulapath = '/study-guide/comparisons/formula-navigator-' . sprintf('%02d', $formula[0]) . '-' . $formula[1] . ($ises ? '-es' : '') . '.html'; $formulaurl = new moodle_url($formulapath, ['from' => 'single-herbs', 'focus' => (string) $selected['number']]); ?><a href="<?php echo $formulaurl->out(false); ?>"><strong><?php echo s($formula[2]); ?></strong></a> (<?php echo s($formula[3]); ?>)<?php endforeach; ?> — <?php echo s($foodtext('current 63-formula roster.', 'listado vigente de 63 fórmulas.')); ?><?php else: echo s($foodtext('No chief or co-chief role in the current 63-formula Board roster.', 'No tiene papel principal ni coprincipal en el listado Board vigente de 63 fórmulas.')); endif; ?></dd>
                <dt><?php echo s($foodtext('Safety', 'Seguridad')); ?></dt><dd class="single-herbs-guide__profile-safety"><?php echo s($herb['caution']); ?></dd>
            </dl></div></details>
        <?php endforeach; ?>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-seventeen-comparisons">
    <h4 id="topic-seventeen-comparisons"><?php echo s($foodtext('High-yield comparisons', 'Comparaciones de alto rendimiento')); ?></h4>
    <div class="single-herbs-guide__comparisons">
        <?php foreach ($foodcomparisons as $comparison): ?><?php $comparisonurl = new moodle_url($ises ? $comparison[1] : $comparison[0], ['from' => 'single-herbs', 'focus' => (string) $selected['number']]); ?><a class="single-herbs-guide__comparison" href="<?php echo $comparisonurl->out(false); ?>"><strong><?php echo s($comparison[2]); ?></strong><span><?php echo s($comparison[3]); ?></span><small><?php echo s($foodtext('Open full comparison →', 'Abrir comparación completa →')); ?></small></a><?php endforeach; ?>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-seventeen-questions">
    <h4 id="topic-seventeen-questions"><?php echo s($foodtext('Yang questions shown in full', 'Preguntas de Yang mostradas completas')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($foodtext('The five relevant chapter 9 questions appear on the front of the cards; no external question number is required to understand them.', 'Las cinco preguntas pertinentes del capítulo 9 aparecen completas al frente de las tarjetas; no hace falta conocer un número externo.')); ?></p>
    <div class="single-herbs-guide__yang-deck" data-yang-deck data-card-label="<?php echo s($foodtext('Card', 'Tarjeta')); ?>" data-of-label="<?php echo s($foodtext('of', 'de')); ?>">
        <?php foreach ($foodquestions as $index => $question): ?>
            <article class="single-herbs-guide__yang-card<?php echo $index === 0 ? ' is-active' : ''; ?>" data-yang-card aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>"><div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--front"><span class="single-herbs-guide__yang-label"><?php echo s($foodtext('Question', 'Pregunta')); ?> <?php echo $index + 1; ?> <?php echo s($foodtext('of', 'de')); ?> <?php echo count($foodquestions); ?> · Yang, <?php echo s($foodtext('Chapter', 'capítulo')); ?> 9</span><p class="single-herbs-guide__yang-question"><?php echo s($question[0]); ?></p></div><div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--back"><span class="single-herbs-guide__yang-label"><?php echo s($foodtext('Answer', 'Respuesta')); ?> <?php echo $index + 1; ?></span><p class="single-herbs-guide__yang-answer"><?php echo s($question[1]); ?></p></div></article>
        <?php endforeach; ?>
        <div class="single-herbs-guide__yang-controls"><button class="single-herbs-guide__yang-control" type="button" data-yang-prev>← <?php echo s($foodtext('Previous', 'Anterior')); ?></button><button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--flip" type="button" data-yang-flip data-show-label="<?php echo s($foodtext('Show answer', 'Mostrar respuesta')); ?>" data-hide-label="<?php echo s($foodtext('Show question', 'Mostrar pregunta')); ?>"><?php echo s($foodtext('Show answer', 'Mostrar respuesta')); ?></button><button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--next" type="button" data-yang-next><?php echo s($foodtext('Next', 'Siguiente')); ?> →</button></div>
        <p class="single-herbs-guide__yang-progress" data-yang-progress><?php echo s($foodtext('Card', 'Tarjeta')); ?> 1 <?php echo s($foodtext('of', 'de')); ?> <?php echo count($foodquestions); ?></p>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-seventeen-recall">
    <h4 id="topic-seventeen-recall"><?php echo s($foodtext('Quick recall', 'Repaso rápido')); ?></h4>
    <div class="single-herbs-guide__recall">
        <details><summary><?php echo s($foodtext('Every food type plus stones or enuresis?', '¿Todo tipo de alimento más cálculos o enuresis?')); ?></summary><p><strong>Ji Nei Jin.</strong></p></details>
        <details><summary><?php echo s($foodtext('Grain retention plus Phlegm and descending Qi?', '¿Retención de granos más Flema y descenso de Qi?')); ?></summary><p><strong>Lai Fu Zi.</strong></p></details>
        <details><summary><?php echo s($foodtext('Meat and fat plus Blood stasis?', '¿Carne y grasa más estasis de Sangre?')); ?></summary><p><strong>Shan Zha.</strong></p></details>
        <details><summary><?php echo s($foodtext('Food Stagnation plus lactation clue?', '¿Estancamiento alimentario más pista de lactancia?')); ?></summary><p><strong>Mai Ya.</strong></p></details>
        <details><summary><?php echo s($foodtext('Gentlest choice for deficiency?', '¿Opción más suave para deficiencia?')); ?></summary><p><strong>Gu Ya.</strong></p></details>
        <details><summary><?php echo s($foodtext('Cereals plus alcohol?', '¿Cereales más alcohol?')); ?></summary><p><strong>Shen Qu.</strong></p></details>
    </div>
</section>

<p class="single-herbs-guide__note single-herbs-guide__source"><strong><?php echo s($foodtext('Sources:', 'Fuentes:')); ?></strong> <?php echo s($foodtext('Bensky et al., Chinese Herbal Medicine: Materia Medica, 3rd ed.; Yang, Chinese Herbal Medicines: Comparisons and Characteristics, 2nd ed., Chapter 9; current 2026 NCBAHM Appendix A. Educational Board review only, not prescribing guidance.', 'Bensky et al., Chinese Herbal Medicine: Materia Medica, 3.ª ed.; Yang, Chinese Herbal Medicines: Comparisons and Characteristics, 2.ª ed., capítulo 9; Appendix A vigente del NCBAHM 2026. Solo repaso educativo Board, no guía de prescripción.')); ?></p>
