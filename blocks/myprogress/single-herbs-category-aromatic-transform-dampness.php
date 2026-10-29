<?php
// Full study content for aromatic herbs that transform Dampness.
defined('MOODLE_INTERNAL') || die();

$aromatictext = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};

$aromaticherbs = [
    [
        'name' => 'Huo Xiang',
        'latin' => 'Pogostemonis/Agastachis Herba',
        'properties' => $aromatictext('Acrid; slightly warm', 'Acre; ligeramente tibia'),
        'channels' => $aromatictext('Lung, Spleen, Stomach', 'Pulmón, Bazo, Estómago'),
        'dose' => '3–9 g',
        'identity' => $aromatictext(
            'Aromatically transforms Dampness, releases the Exterior, harmonizes the Middle and stops vomiting.',
            'Transforma Humedad aromáticamente, libera el Exterior, armoniza el Centro y detiene el vómito.'
        ),
        'clue' => $aromatictext(
            'Summer or humid-weather illness with chills, fever, heavy head, chest oppression, nausea, vomiting or diarrhea.',
            'Enfermedad de verano o clima húmedo con escalofríos, fiebre, cabeza pesada, opresión torácica, náusea, vómito o diarrea.'
        ),
        'memory' => $aromatictext(
            'The fragrant doorman opens the Exterior while clearing the damp kitchen and calming the upset stomach.',
            'El portero fragante abre el Exterior mientras despeja la cocina húmeda y calma el Estómago revuelto.'
        ),
        'unique' => $aromatictext(
            'The principal Exterior-plus-Middle herb in this list and especially recognizable for nausea and vomiting.',
            'La principal hierba Exterior-más-Centro de esta lista y especialmente reconocible por náusea y vómito.'
        ),
        'caution' => $aromatictext(
            'Its volatile oils favor shorter cooking or late addition; use cautiously with Yin deficiency or pronounced dryness.',
            'Sus aceites volátiles favorecen cocción corta o adición tardía; usar con cautela en deficiencia de Yin o sequedad marcada.'
        ),
        'formulas' => [[21, 'huo-xiang-zheng-qi-san', 'Huo Xiang Zheng Qi San', $aromatictext('chief', 'principal')]],
    ],
    [
        'name' => 'Cang Zhu',
        'latin' => 'Atractylodis Rhizoma',
        'properties' => $aromatictext('Acrid, bitter; warm', 'Acre, amarga; tibia'),
        'channels' => $aromatictext('Spleen, Stomach', 'Bazo, Estómago'),
        'dose' => '3–9 g',
        'identity' => $aromatictext(
            'Strongly dries Dampness, strengthens Spleen transport, induces sweating and expels Wind-Dampness.',
            'Seca Humedad con fuerza, fortalece el transporte del Bazo, induce sudoración y expulsa Viento-Humedad.'
        ),
        'clue' => $aromatictext(
            'Heavy body, thick greasy coating and poor appetite with marked Dampness; may include Bi pain or night blindness.',
            'Cuerpo pesado, capa gruesa-grasosa y poco apetito con Humedad marcada; puede incluir dolor Bi o ceguera nocturna.'
        ),
        'memory' => $aromatictext(
            'The dry workshop fan removes moisture from the center and blows Dampness out through muscles and joints.',
            'El ventilador seco del taller elimina humedad del Centro y expulsa Humedad por músculos y articulaciones.'
        ),
        'unique' => $aromatictext(
            'The strongest direct Dampness-drier in this group; unlike Bai Zhu, its Board identity is drying and dispersing rather than tonifying.',
            'La secante directa de Humedad más fuerte del grupo; a diferencia de Bai Zhu, su identidad Board es secar y dispersar, no tonificar.'
        ),
        'caution' => $aromatictext(
            'Warm and drying; avoid or combine carefully with Yin deficiency, fluid injury or excessive sweating.',
            'Tibia y secante; evitar o combinar con cuidado en deficiencia de Yin, lesión de líquidos o sudoración excesiva.'
        ),
        'formulas' => [[20, 'ping-wei-san', 'Ping Wei San', $aromatictext('chief', 'principal')]],
    ],
    [
        'name' => 'Hou Po',
        'latin' => 'Magnoliae officinalis Cortex',
        'properties' => $aromatictext('Bitter, acrid; warm', 'Amarga, acre; tibia'),
        'channels' => $aromatictext('Lung, Spleen, Stomach, Large Intestine', 'Pulmón, Bazo, Estómago, Intestino Grueso'),
        'dose' => '3–9 g',
        'identity' => $aromatictext(
            'Moves Qi, dries Dampness, reduces focal fullness and directs rebellious Qi downward.',
            'Mueve Qi, seca Humedad, reduce plenitud focal y dirige Qi rebelde hacia abajo.'
        ),
        'clue' => $aromatictext(
            'Epigastric or abdominal distension is the center of the case, sometimes with constipation, cough or wheezing.',
            'La distensión epigástrica o abdominal es el centro del caso, a veces con estreñimiento, tos o sibilancia.'
        ),
        'memory' => $aromatictext(
            'Magnolia bark presses the swollen middle flat, then pushes blocked Qi downward.',
            'La corteza de magnolia aplana el Centro distendido y empuja Qi bloqueado hacia abajo.'
        ),
        'unique' => $aromatictext(
            'The fullness-and-Qi-movement specialist; its downward action links the Middle, bowel and Lung.',
            'La especialista en plenitud y movimiento de Qi; su acción descendente conecta Centro, intestino y Pulmón.'
        ),
        'caution' => $aromatictext(
            'Qi-moving and descending; use cautiously in pregnancy or marked Qi deficiency without stagnation.',
            'Mueve y desciende Qi; usar con cautela en embarazo o deficiencia marcada de Qi sin estancamiento.'
        ),
        'formulas' => [],
    ],
    [
        'name' => 'Sha Ren',
        'latin' => 'Amomi Fructus',
        'properties' => $aromatictext('Acrid; warm', 'Acre; tibia'),
        'channels' => $aromatictext('Spleen, Stomach, Kidney', 'Bazo, Estómago, Riñón'),
        'dose' => '3–6 g',
        'identity' => $aromatictext(
            'Moves Middle-Jiao Qi, transforms Dampness, warms the Middle, stops diarrhea and calms the fetus.',
            'Mueve Qi del Jiao Medio, transforma Humedad, calienta el Centro, detiene diarrea y calma el feto.'
        ),
        'clue' => $aromatictext(
            'Damp obstruction with distension or nausea plus pregnancy, restless fetus, diarrhea or Kidney involvement.',
            'Obstrucción por Humedad con distensión o náusea más embarazo, feto inquieto, diarrea o participación de Riñón.'
        ),
        'memory' => $aromatictext(
            'The fastest spice cart moves Qi through the kitchen, secures the pregnancy room and warms the lower floor.',
            'El carro de especias más rápido mueve Qi por la cocina, asegura el cuarto del embarazo y calienta el piso inferior.'
        ),
        'unique' => $aromatictext(
            'Yang ranks it first of the four seed spices for regulating Middle-Jiao Qi and relieving distension and pain.',
            'Yang la coloca primera entre las cuatro semillas aromáticas para regular Qi del Centro y aliviar distensión y dolor.'
        ),
        'caution' => $aromatictext(
            'Crush and add late to preserve aroma; use cautiously when Heat or Yin deficiency predominates.',
            'Triturar y añadir tarde para conservar aroma; usar con cautela cuando predominan Calor o deficiencia de Yin.'
        ),
        'formulas' => [],
    ],
    [
        'name' => 'Cao Dou Kou',
        'latin' => 'Alpiniae katsumadai Semen',
        'properties' => $aromatictext('Acrid; warm', 'Acre; tibia'),
        'channels' => $aromatictext('Spleen, Stomach', 'Bazo, Estómago'),
        'dose' => '3–6 g',
        'identity' => $aromatictext(
            'Dries turbid Dampness, warms the Middle, moves Qi and stops vomiting.',
            'Seca Humedad turbia, calienta el Centro, mueve Qi y detiene el vómito.'
        ),
        'clue' => $aromatictext(
            'Cold-Damp in the Middle with a cold painful abdomen, vomiting and diarrhea; more drying than Bai Dou Kou.',
            'Frío-Humedad en el Centro con abdomen frío y doloroso, vómito y diarrea; más secante que Bai Dou Kou.'
        ),
        'memory' => $aromatictext(
            'The brown cardamom is the second-hot furnace: it dries the damp kitchen more than it moves the furniture.',
            'El cardamomo marrón es el segundo horno más caliente: seca la cocina húmeda más de lo que mueve los muebles.'
        ),
        'unique' => $aromatictext(
            'Warmer and better at drying turbid Dampness than Bai Dou Kou, but weaker at moving Qi and disseminating Lung Qi.',
            'Más tibia y mejor para secar Humedad turbia que Bai Dou Kou, pero más débil moviendo Qi y difundiendo Qi de Pulmón.'
        ),
        'caution' => $aromatictext(
            'Warm and drying; avoid in Heat, Yin deficiency or fluid injury. Crush before use and protect volatile oils.',
            'Tibia y secante; evitar en Calor, deficiencia de Yin o lesión de líquidos. Triturar y proteger aceites volátiles.'
        ),
        'formulas' => [],
    ],
    [
        'name' => 'Cao Guo',
        'latin' => 'Tsaoko Fructus',
        'properties' => $aromatictext('Acrid; warm', 'Acre; tibia'),
        'channels' => $aromatictext('Spleen, Stomach', 'Bazo, Estómago'),
        'dose' => '3–6 g',
        'identity' => $aromatictext(
            'Strongly warms the Middle, dries Dampness, expels Cold, resolves clumping and treats malarial disorders.',
            'Calienta con fuerza el Centro, seca Humedad, expulsa Frío, resuelve aglomeración y trata trastornos maláricos.'
        ),
        'clue' => $aromatictext(
            'Severe Cold-Damp with cold abdominal pain or alternating chills and fever from malaria.',
            'Frío-Humedad intenso con dolor abdominal frío o alternancia de escalofríos y fiebre por malaria.'
        ),
        'memory' => $aromatictext(
            'The largest hot fruit is the furnace and malaria bell at the back of the damp kitchen.',
            'El fruto grande y caliente es el horno y la campana de malaria al fondo de la cocina húmeda.'
        ),
        'unique' => $aromatictext(
            'Yang ranks it strongest of the four seed spices for warming and drying Cold-Dampness, but weakest for Qi movement.',
            'Yang la coloca como la más fuerte de las cuatro semillas para calentar y secar Frío-Humedad, pero la más débil moviendo Qi.'
        ),
        'caution' => $aromatictext(
            'Strongly warm and drying; avoid when Heat or fluid deficiency predominates.',
            'Fuertemente tibia y secante; evitar cuando predominan Calor o deficiencia de líquidos.'
        ),
        'formulas' => [],
    ],
    [
        'name' => 'Bai Dou Kou',
        'latin' => 'Amomi Fructus rotundus',
        'properties' => $aromatictext('Acrid; warm', 'Acre; tibia'),
        'channels' => $aromatictext('Lung, Spleen, Stomach', 'Pulmón, Bazo, Estómago'),
        'dose' => '3–6 g',
        'identity' => $aromatictext(
            'Moves Qi, transforms Dampness, warms the Middle, stops vomiting and disseminates Lung Qi.',
            'Mueve Qi, transforma Humedad, calienta el Centro, detiene vómito y difunde Qi de Pulmón.'
        ),
        'clue' => $aromatictext(
            'Damp obstruction with poor appetite and distension plus an Upper-Jiao or Lung-Qi clue.',
            'Obstrucción por Humedad con poco apetito y distensión más una pista de Jiao Superior o Qi de Pulmón.'
        ),
        'memory' => $aromatictext(
            'White cardamom opens both kitchen windows: one for appetite and one for Lung Qi.',
            'El cardamomo blanco abre dos ventanas de la cocina: una para apetito y otra para Qi de Pulmón.'
        ),
        'unique' => $aromatictext(
            'Compared with Cao Dou Kou, it moves Qi more effectively and uniquely emphasizes dissemination of Lung Qi.',
            'Comparada con Cao Dou Kou, mueve Qi con más eficacia y destaca de manera única la difusión del Qi de Pulmón.'
        ),
        'caution' => $aromatictext(
            'Crush and add near the end of decoction; warm aromatics can injure Yin and fluids if overused.',
            'Triturar y añadir casi al final de la decocción; las aromáticas tibias pueden lesionar Yin y líquidos si se abusa.'
        ),
        'formulas' => [],
    ],
];

$aromaticcomparisons = [
    [
        '/study-guide/comparisons/hou-po-vs-cang-zhu.html',
        '/study-guide/comparisons/hou-po-vs-cang-zhu-es.html',
        'Hou Po vs. Cang Zhu',
        $aromatictext('Move Qi and reduce fullness versus strongly dry and disperse Dampness.', 'Mover Qi y reducir plenitud frente a secar y dispersar Humedad con fuerza.'),
    ],
    [
        '/study-guide/comparisons/cao-dou-kou-vs-bai-dou-kou.html',
        '/study-guide/comparisons/cao-dou-kou-vs-bai-dou-kou-es.html',
        'Cao Dou Kou vs. Bai Dou Kou',
        $aromatictext('Stronger warming-drying versus stronger Qi movement and Lung dissemination.', 'Mayor calentamiento-secado frente a mayor movimiento de Qi y difusión pulmonar.'),
    ],
    [
        '/study-guide/comparisons/guang-huo-xiang-vs-tu-huo-xiang.html',
        '/study-guide/comparisons/guang-huo-xiang-vs-tu-huo-xiang-es.html',
        'Guang Huo Xiang vs. Tu Huo Xiang',
        $aromatictext('Board naming trap: Patchouli versus Korean mint.', 'Trampa de nombres Board: pachulí frente a menta coreana.'),
    ],
    [
        '/study-guide/comparisons/rou-dou-kou-vs-cao-guo.html',
        '/study-guide/comparisons/rou-dou-kou-vs-cao-guo-es.html',
        'Rou Dou Kou vs. Cao Guo',
        $aromatictext('Outside-category astringent leakage clue versus aromatic Cold-Damp clumping and malaria.', 'Pista astringente externa a la categoría frente a aglomeración Frío-Humedad y malaria.'),
    ],
];

$aromaticquestions = [
    [
        $aromatictext('What makes aromatic herbs different from ordinary Qi-regulating or Damp-draining herbs?', '¿Qué distingue las aromáticas de las hierbas comunes que regulan Qi o drenan Humedad?'),
        $aromatictext('Their light, sharp fragrance penetrates turbidity, separates clear from turbid, awakens the Spleen and activates Qi movement.', 'Su fragancia ligera y penetrante atraviesa turbidez, separa limpio de turbio, despierta el Bazo y activa movimiento de Qi.'),
    ],
    [
        $aromatictext('What is the core Middle-Jiao presentation?', '¿Cuál es la presentación central del Jiao Medio?'),
        $aromatictext('Chest or epigastric fullness, abdominal distension, poor appetite, sticky mouth, nausea or vomiting, altered stool, heavy body and a thick greasy coating.', 'Plenitud torácica o epigástrica, distensión abdominal, poco apetito, boca pegajosa, náusea o vómito, heces alteradas, cuerpo pesado y capa gruesa-grasosa.'),
    ],
    [
        $aromatictext('How do Huo Xiang and Pei Lan differ?', '¿Cómo difieren Huo Xiang y Pei Lan?'),
        $aromatictext('Huo Xiang is slightly warm, stronger for Middle-Jiao Qi, nausea, vomiting and Exterior Cold-Dampness. Pei Lan—outside this current list—is neutral and especially clears foul, sweet-sticky turbidity.', 'Huo Xiang es ligeramente tibia y más fuerte para Qi del Centro, náusea, vómito y Frío-Humedad Exterior. Pei Lan, fuera de esta lista vigente, es neutra y destaca para turbidez fétida, dulce-pegajosa.'),
    ],
    [
        $aromatictext('What are Yang’s two opposite rankings for the four seed spices?', '¿Cuáles son los dos órdenes opuestos de Yang para las cuatro semillas aromáticas?'),
        $aromatictext('Qi movement/distension: Sha Ren > Bai Dou Kou > Cao Dou Kou > Cao Guo. Warming-drying Cold-Dampness: Cao Guo > Cao Dou Kou > Bai Dou Kou > Sha Ren.', 'Movimiento de Qi/distensión: Sha Ren > Bai Dou Kou > Cao Dou Kou > Cao Guo. Calentar-secar Frío-Humedad: Cao Guo > Cao Dou Kou > Bai Dou Kou > Sha Ren.'),
    ],
    [
        $aromatictext('Which extra clue identifies each seed spice?', '¿Qué pista adicional identifica cada semilla aromática?'),
        $aromatictext('Sha Ren calms the fetus; Bai Dou Kou improves appetite and disseminates Lung Qi; Cao Dou Kou warms and strengthens the Spleen; Cao Guo treats malaria.', 'Sha Ren calma el feto; Bai Dou Kou mejora apetito y difunde Qi de Pulmón; Cao Dou Kou calienta y fortalece Bazo; Cao Guo trata malaria.'),
    ],
    [
        $aromatictext('What preparation and safety rule applies to this category?', '¿Qué regla de preparación y seguridad aplica a esta categoría?'),
        $aromatictext('Protect volatile oils by crushing the fruits and adding aromatic herbs late or cooking briefly. Prolonged or excessive warm-dry use can consume Yin and fluids.', 'Proteger aceites volátiles triturando frutos y añadiendo aromáticas tarde o cocinándolas poco. El uso tibio-secante prolongado o excesivo puede consumir Yin y líquidos.'),
    ],
];
?>

<section class="single-herbs-guide__section" aria-labelledby="topic-sixteen-foundations">
    <h4 id="topic-sixteen-foundations"><?php echo s($aromatictext('Aromatic herbs that transform Dampness: general introduction', 'Hierbas aromáticas que transforman Humedad: introducción general')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($aromatictext(
        'Think of Dampness as a wet blanket over the Spleen. Aroma penetrates the blanket, wakes transport and restores Qi movement; the exam then asks which companion clue decides the herb.',
        'Piensa en la Humedad como una manta mojada sobre el Bazo. El aroma atraviesa la manta, despierta el transporte y restaura movimiento de Qi; el examen pregunta qué pista acompañante decide la hierba.'
    )); ?></p>
    <div class="single-herbs-guide__decision">
        <div><strong><?php echo s($aromatictext('Exterior + vomiting', 'Exterior + vómito')); ?></strong><span>Huo Xiang</span></div>
        <div><strong><?php echo s($aromatictext('Strongest drying', 'Secado más fuerte')); ?></strong><span>Cang Zhu</span></div>
        <div><strong><?php echo s($aromatictext('Fullness + descending Qi', 'Plenitud + descenso de Qi')); ?></strong><span>Hou Po</span></div>
        <div><strong><?php echo s($aromatictext('Pregnancy + fastest Qi movement', 'Embarazo + movimiento rápido de Qi')); ?></strong><span>Sha Ren</span></div>
    </div>
    <aside class="single-herbs-guide__memory-path">
        <p class="single-herbs-guide__memory-line"><?php echo s($aromatictext('Enter the damp spice kitchen.', 'Entra a la cocina húmeda de las especias.')); ?></p>
        <p><?php echo s($aromatictext(
            'Huo Xiang opens the door and calms the vomiting guest. Cang Zhu runs the strongest dryer. Hou Po flattens the swollen counter. Sha Ren moves the carts and protects the pregnancy room. Cao Dou Kou is the second-hot furnace, Cao Guo the hottest furnace and malaria bell, while Bai Dou Kou opens the appetite and Lung windows.',
            'Huo Xiang abre la puerta y calma al huésped que vomita. Cang Zhu enciende la secadora más fuerte. Hou Po aplana el mostrador distendido. Sha Ren mueve los carros y protege el cuarto del embarazo. Cao Dou Kou es el segundo horno caliente, Cao Guo el horno más fuerte y campana de malaria, mientras Bai Dou Kou abre ventanas de apetito y Pulmón.'
        )); ?></p>
        <p><strong><?php echo s($aromatictext('Wet blanket → wake Spleen → move Qi → identify the companion clue.', 'Manta mojada → despertar Bazo → mover Qi → identificar la pista acompañante.')); ?></strong></p>
    </aside>
    <p class="single-herbs-guide__scope-note"><strong><?php echo s($aromatictext('Current Board list:', 'Lista Board vigente:')); ?></strong> <?php echo s($aromatictext('the seven requested herbs are shown once each. Pei Lan and other Yang comparison herbs are explicitly marked outside this category whenever they appear in a question.', 'las siete hierbas solicitadas aparecen una vez cada una. Pei Lan y otras comparaciones de Yang se marcan explícitamente fuera de esta categoría cuando aparecen en una pregunta.')); ?></p>
    <ol class="single-herbs-guide__category-herbs">
        <?php foreach ($aromaticherbs as $herb): ?>
            <li class="single-herbs-guide__category-herb single-herbs-guide__category-herb--core">
                <div class="single-herbs-guide__category-herb-head">
                    <strong><?php echo s($herb['name']); ?></strong>
                    <em><?php echo s($herb['latin']); ?></em>
                    <span class="single-herbs-guide__category-herb-badge"><?php echo s($aromatictext('Current category list', 'Lista vigente de la categoría')); ?></span>
                </div>
                <p><?php echo s($herb['identity']); ?></p>
            </li>
        <?php endforeach; ?>
    </ol>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-sixteen-yang">
    <h4 id="topic-sixteen-yang"><?php echo s($aromatictext('Yang comparative fingerprints', 'Huellas comparativas de Yang')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($aromatictext('Yang figure 7.2 gives all four listed Middle-Jiao aromatics the same relative index of 100, so that graph confirms their shared strength but does not distinguish which one to choose. Use the study question below to separate them.', 'La figura 7.2 de Yang asigna a las cuatro aromáticas del Jiao Medio listadas el mismo índice relativo de 100; la gráfica confirma su fuerza compartida, pero no distingue cuál elegir. Usa la pregunta de estudio siguiente para separarlas.')); ?></p>
    <aside class="single-herbs-guide__memory-path">
        <p class="single-herbs-guide__memory-line"><?php echo s($aromatictext('Yang question shown in full', 'Pregunta de Yang mostrada completa')); ?></p>
        <p><strong><?php echo s($aromatictext('What are the differences between the functions of Sha Ren (Amomi xanthioidis Fructus), Bai Dou Kou (Amomi Fructus rotundus), Cao Dou Kou (Alpiniae katsumadai Semen) and Cao Guo (Tsaoko Fructus)?', '¿Cuáles son las diferencias entre las funciones de Sha Ren (Amomi xanthioidis Fructus), Bai Dou Kou (Amomi Fructus rotundus), Cao Dou Kou (Alpiniae katsumadai Semen) y Cao Guo (Tsaoko Fructus)?')); ?></strong></p>
        <p><?php echo s($aromatictext('Answer it on two axes: which herb moves Qi and relieves distension best, and which herb warms and dries Cold-Dampness most strongly?', 'Respóndela con dos ejes: ¿cuál mueve mejor el Qi y alivia la distensión, y cuál calienta y seca Frío-Humedad con mayor fuerza?')); ?></p>
    </aside>
    <div class="single-herbs-guide__yang-charts">
        <article class="single-herbs-guide__yang-chart">
            <h5><?php echo s($aromatictext('Transform Dampness from the Middle Jiao', 'Transformar Humedad del Jiao Medio')); ?></h5>
            <small>Yang fig. 7.2</small>
            <?php foreach (['Huo Xiang' => 100, 'Hou Po' => 100, 'Sha Ren' => 100, 'Bai Dou Kou' => 100] as $name => $value): ?>
                <div class="single-herbs-guide__yang-bar"><span><?php echo s($name); ?></span><span class="single-herbs-guide__yang-bar-track"><span class="single-herbs-guide__yang-bar-fill" style="width:<?php echo $value; ?>%"></span></span><b><?php echo $value; ?></b></div>
            <?php endforeach; ?>
        </article>
        <article class="single-herbs-guide__yang-chart">
            <h5><?php echo s($aromatictext('Move Qi / reduce distension and pain', 'Mover Qi / reducir distensión y dolor')); ?></h5>
            <small><?php echo s($aromatictext('Answer axis 1 · strongest to weakest', 'Eje de respuesta 1 · de mayor a menor fuerza')); ?></small>
            <ol><li><strong>Sha Ren</strong></li><li><strong>Bai Dou Kou</strong></li><li><strong>Cao Dou Kou</strong></li><li><strong>Cao Guo</strong></li></ol>
        </article>
        <article class="single-herbs-guide__yang-chart">
            <h5><?php echo s($aromatictext('Warm and dry Cold-Dampness', 'Calentar y secar Frío-Humedad')); ?></h5>
            <small><?php echo s($aromatictext('Answer axis 2 · strongest to weakest', 'Eje de respuesta 2 · de mayor a menor fuerza')); ?></small>
            <ol><li><strong>Cao Guo</strong></li><li><strong>Cao Dou Kou</strong></li><li><strong>Bai Dou Kou</strong></li><li><strong>Sha Ren</strong></li></ol>
        </article>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-sixteen-profiles">
    <h4 id="topic-sixteen-profiles"><?php echo s($aromatictext('Individual herb profiles', 'Perfiles individuales de las hierbas')); ?></h4>
    <div class="single-herbs-guide__profiles">
        <?php foreach ($aromaticherbs as $herb): ?>
            <details class="single-herbs-guide__profile"<?php echo $herb['name'] === 'Huo Xiang' ? ' open' : ''; ?>>
                <summary><span class="single-herbs-guide__profile-name"><?php echo s($herb['name']); ?></span><span class="single-herbs-guide__profile-latin"><?php echo s($herb['latin']); ?></span><span class="single-herbs-guide__profile-meta"><?php echo s($herb['properties']); ?> · <?php echo s($herb['channels']); ?></span></summary>
                <div class="single-herbs-guide__profile-body"><dl>
                    <dt><?php echo s($aromatictext('Study dose', 'Dosis de estudio')); ?></dt><dd><?php echo s($herb['dose']); ?></dd>
                    <dt><?php echo s($aromatictext('Identity', 'Identidad')); ?></dt><dd><?php echo s($herb['identity']); ?></dd>
                    <dt><?php echo s($aromatictext('Exam clue', 'Pista de examen')); ?></dt><dd><?php echo s($herb['clue']); ?></dd>
                    <dt><?php echo s($aromatictext('Memory', 'Memoria')); ?></dt><dd class="single-herbs-guide__profile-memory"><?php echo s($herb['memory']); ?></dd>
                    <dt><?php echo s($aromatictext('Unique fingerprint', 'Huella única')); ?></dt><dd><?php echo s($herb['unique']); ?></dd>
                    <dt><?php echo s($aromatictext('Formula boundary', 'Límite de fórmulas')); ?></dt>
                    <dd class="single-herbs-guide__profile-formulas">
                        <?php if ($herb['formulas']): ?>
                            <?php foreach ($herb['formulas'] as $index => $formula): ?><?php if ($index): ?> · <?php endif; ?><a href="/study-guide/comparisons/formula-navigator-<?php echo sprintf('%02d', $formula[0]); ?>-<?php echo s($formula[1]); ?><?php echo $ises ? '-es' : ''; ?>.html"><strong><?php echo s($formula[2]); ?></strong></a> (<?php echo s($formula[3]); ?>)<?php endforeach; ?> — <?php echo s($aromatictext('current 63-formula roster.', 'listado vigente de 63 fórmulas.')); ?>
                        <?php else: ?>
                            <?php echo s($aromatictext('No chief or co-chief role in the current 63-formula Board roster.', 'No tiene papel principal ni coprincipal en el listado Board vigente de 63 fórmulas.')); ?>
                        <?php endif; ?>
                    </dd>
                    <dt><?php echo s($aromatictext('Safety / preparation', 'Seguridad / preparación')); ?></dt><dd class="single-herbs-guide__profile-safety"><?php echo s($herb['caution']); ?></dd>
                </dl></div>
            </details>
        <?php endforeach; ?>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-sixteen-comparisons">
    <h4 id="topic-sixteen-comparisons"><?php echo s($aromatictext('High-yield comparisons', 'Comparaciones de alto rendimiento')); ?></h4>
    <div class="single-herbs-guide__comparisons">
        <?php foreach ($aromaticcomparisons as $comparison): ?>
            <?php $comparisonurl = new moodle_url($ises ? $comparison[1] : $comparison[0], ['from' => 'single-herbs', 'focus' => (string) $selected['number']]); ?>
            <a class="single-herbs-guide__comparison" href="<?php echo $comparisonurl->out(false); ?>"><strong><?php echo s($comparison[2]); ?></strong><span><?php echo s($comparison[3]); ?></span><small><?php echo s($aromatictext('Open full comparison →', 'Abrir comparación completa →')); ?></small></a>
        <?php endforeach; ?>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-sixteen-questions">
    <h4 id="topic-sixteen-questions"><?php echo s($aromatictext('Yang questions for this category', 'Preguntas de Yang para esta categoría')); ?></h4>
    <p class="single-herbs-guide__section-lead"><?php echo s($aromatictext('One reversible deck keeps the relevant chapter 7 distinctions together and identifies references outside the current list.', 'Una sola baraja reversible mantiene juntas las distinciones pertinentes del capítulo 7 e identifica referencias fuera de la lista vigente.')); ?></p>
    <div class="single-herbs-guide__yang-deck" data-yang-deck data-card-label="<?php echo s($aromatictext('Card', 'Tarjeta')); ?>" data-of-label="<?php echo s($aromatictext('of', 'de')); ?>">
        <?php foreach ($aromaticquestions as $index => $question): ?>
            <article class="single-herbs-guide__yang-card<?php echo $index === 0 ? ' is-active' : ''; ?>" data-yang-card aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                <div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--front"><span class="single-herbs-guide__yang-label"><?php echo s($aromatictext('Question', 'Pregunta')); ?> <?php echo $index + 1; ?> <?php echo s($aromatictext('of', 'de')); ?> <?php echo count($aromaticquestions); ?> · Yang, <?php echo s($aromatictext('Chapter', 'capítulo')); ?> 7</span><p class="single-herbs-guide__yang-question"><?php echo s($question[0]); ?></p></div>
                <div class="single-herbs-guide__yang-face single-herbs-guide__yang-face--back"><span class="single-herbs-guide__yang-label"><?php echo s($aromatictext('Answer', 'Respuesta')); ?> <?php echo $index + 1; ?></span><p class="single-herbs-guide__yang-answer"><?php echo s($question[1]); ?></p></div>
            </article>
        <?php endforeach; ?>
        <div class="single-herbs-guide__yang-controls">
            <button class="single-herbs-guide__yang-control" type="button" data-yang-prev>← <?php echo s($aromatictext('Previous', 'Anterior')); ?></button>
            <button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--flip" type="button" data-yang-flip data-show-label="<?php echo s($aromatictext('Show answer', 'Mostrar respuesta')); ?>" data-hide-label="<?php echo s($aromatictext('Show question', 'Mostrar pregunta')); ?>"><?php echo s($aromatictext('Show answer', 'Mostrar respuesta')); ?></button>
            <button class="single-herbs-guide__yang-control single-herbs-guide__yang-control--next" type="button" data-yang-next><?php echo s($aromatictext('Next', 'Siguiente')); ?> →</button>
        </div>
        <p class="single-herbs-guide__yang-progress" data-yang-progress><?php echo s($aromatictext('Card', 'Tarjeta')); ?> 1 <?php echo s($aromatictext('of', 'de')); ?> <?php echo count($aromaticquestions); ?></p>
    </div>
</section>

<section class="single-herbs-guide__section" aria-labelledby="topic-sixteen-recall">
    <h4 id="topic-sixteen-recall"><?php echo s($aromatictext('Quick recall', 'Repaso rápido')); ?></h4>
    <div class="single-herbs-guide__recall">
        <details><summary><?php echo s($aromatictext('Exterior Cold-Dampness plus vomiting?', '¿Frío-Humedad Exterior más vómito?')); ?></summary><p><strong>Huo Xiang.</strong></p></details>
        <details><summary><?php echo s($aromatictext('Strongest direct Dampness drying?', '¿Secado directo de Humedad más fuerte?')); ?></summary><p><strong>Cang Zhu.</strong></p></details>
        <details><summary><?php echo s($aromatictext('Distension and fullness with downward Qi movement?', '¿Distensión y plenitud con movimiento descendente de Qi?')); ?></summary><p><strong>Hou Po.</strong></p></details>
        <details><summary><?php echo s($aromatictext('Pregnancy plus Damp obstruction?', '¿Embarazo más obstrucción por Humedad?')); ?></summary><p><strong>Sha Ren.</strong></p></details>
        <details><summary><?php echo s($aromatictext('Strongest warming-drying plus malaria?', '¿Mayor calentamiento-secado más malaria?')); ?></summary><p><strong>Cao Guo.</strong></p></details>
    </div>
</section>

<p class="single-herbs-guide__note single-herbs-guide__source"><strong><?php echo s($aromatictext('Sources:', 'Fuentes:')); ?></strong> <?php echo s($aromatictext('Bensky et al., Chinese Herbal Medicine: Materia Medica, 3rd ed.; Yang, Chinese Herbal Medicines: Comparisons and Characteristics, 2nd ed., Chapter 7; current 2026 NCBAHM Appendix A. Educational Board review only, not prescribing guidance.', 'Bensky et al., Chinese Herbal Medicine: Materia Medica, 3.ª ed.; Yang, Chinese Herbal Medicines: Comparisons and Characteristics, 2.ª ed., capítulo 7; Appendix A vigente del NCBAHM 2026. Solo repaso educativo Board, no guía de prescripción.')); ?></p>
