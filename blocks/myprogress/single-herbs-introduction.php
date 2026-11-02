<?php
// Introductory learning module for the Single Herbs study guide.
defined('MOODLE_INTERNAL') || die();

$introtext = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};

$history = [
    ['Eastern Han · 25–220 CE', 'Han Oriental · 25–220 e.c.',
        'The foundational medical classics were compiled around this period. The Inner Classic supplied the theoretical framework; the Divine Husbandman’s Classic became the archetype for individual-herb study.',
        'Alrededor de este periodo se compilaron los clásicos médicos fundamentales. El Clásico Interno aportó el marco teórico y el Clásico del Divino Labrador se convirtió en el arquetipo del estudio de hierbas individuales.'],
    ['364 materia-medica entries', '364 entradas de materia médica',
        'The early Classic organized botanical, mineral and zoological substances, recorded taste, temperature and toxicity, and later grouped them into upper, middle and lower grades.',
        'El clásico temprano organizó sustancias botánicas, minerales y zoológicas, registró sabor, temperatura y toxicidad y después las agrupó en grados superior, medio e inferior.'],
    ['Tang and Song expansion', 'Expansión Tang y Song',
        'The Tang Materia Medica listed 844 entries in 659; the major Song compilation reached 1,558 in 1108. New substances and new uses continually entered the tradition.',
        'La Materia Médica Tang enumeró 844 entradas en 659; la gran compilación Song alcanzó 1,558 en 1108. Nuevas sustancias y nuevos usos se incorporaron continuamente a la tradición.'],
    ['Li Shi-Zhen · 1596', 'Li Shi-Zhen · 1596',
        'The Grand Materia Medica described 1,892 medicinal substances and became the best-known example of the Chinese materia-medica tradition.',
        'La Gran Materia Médica describió 1,892 sustancias medicinales y se convirtió en el ejemplo más conocido de la tradición china de materia médica.'],
    ['Modern reference works', 'Referencias modernas',
        'Twentieth- and twenty-first-century compilations grew to thousands of entries, but most substances remain local or specialized rather than part of a core examination roster.',
        'Las compilaciones de los siglos XX y XXI crecieron hasta miles de entradas, pero muchas sustancias siguen siendo locales o especializadas y no forman parte de un listado central de examen.'],
    ['Current Board scope', 'Alcance actual del Board',
        'This study guide concentrates on the 304 herbs in the current Appendix A roster. Historical totals explain the tradition; the current roster defines the primary examination scope.',
        'Esta guía se concentra en las 304 hierbas del listado vigente del Appendix A. Los totales históricos explican la tradición; el listado actual define el alcance principal del examen.'],
];

$tastes = [
    ['Acrid', 'Acre', 'Disperses and moves', 'Dispersa y moviliza'],
    ['Sweet', 'Dulce', 'Tonifies, harmonizes and may moisten', 'Tonifica, armoniza y puede humedecer'],
    ['Bitter', 'Amargo', 'Drains and dries', 'Drena y seca'],
    ['Sour', 'Agrio', 'Astringes and restrains leakage', 'Astringe y contiene pérdidas'],
    ['Salty', 'Salado', 'Purges and softens hardness', 'Purga y ablanda durezas'],
    ['Bland', 'Insípido', 'Leaches Dampness and promotes urination', 'Filtra Humedad y favorece la orina'],
];

$profilefields = [
    ['Identity', 'Identidad', 'Pinyin name, pharmaceutical/Latin name and medicinal part.', 'Nombre pinyin, nombre farmacéutico/latino y parte medicinal.'],
    ['Category', 'Categoría', 'The herb’s principal therapeutic family and strategic direction.', 'La familia terapéutica principal y la dirección estratégica de la hierba.'],
    ['Nature', 'Naturaleza', 'Hot, warm, neutral, cool or cold, including slight degrees.', 'Caliente, tibia, neutra, fresca o fría, incluidos los grados ligeros.'],
    ['Flavor', 'Sabor', 'One or more functional tendencies; aromatic and astringent may add important qualifiers.', 'Una o más tendencias funcionales; aromático y astringente pueden añadir calificadores importantes.'],
    ['Channels', 'Meridianos', 'Organ and regional affinities that help localize the herb’s effects.', 'Afinidades orgánicas y regionales que ayudan a localizar los efectos de la hierba.'],
    ['Actions and indications', 'Acciones e indicaciones', 'What the herb does and the patterns, symptoms or clinical clues that justify choosing it.', 'Lo que hace la hierba y los patrones, síntomas o pistas clínicas que justifican elegirla.'],
    ['Dose and preparation', 'Dosis y preparación', 'Study range, processing, timing, wrapping, crushing or special decoction instructions.', 'Rango de estudio, procesamiento, momento, envoltura, trituración o instrucciones especiales de decocción.'],
    ['Safety', 'Seguridad', 'Contraindications, toxicity, pregnancy, interactions and obsolete or protected-substance status.', 'Contraindicaciones, toxicidad, embarazo, interacciones y condición obsoleta o protegida.'],
];

$relationships = [
    ['Mutual accentuation', 'Acentuación mutua', 'Two herbs with similar functions reinforce the same therapeutic action.', 'Dos hierbas con funciones semejantes refuerzan la misma acción terapéutica.'],
    ['Mutual enhancement', 'Potenciación mutua', 'Different actions cooperate so one herb strengthens the other in a defined situation.', 'Acciones diferentes cooperan para que una hierba fortalezca a la otra en una situación definida.'],
    ['Mutual counteraction', 'Contrarresto mutuo', 'One herb reduces the toxicity or adverse effects of another.', 'Una hierba reduce la toxicidad o los efectos adversos de otra.'],
    ['Mutual suppression', 'Supresión mutua', 'The same protective relationship viewed from the herb performing the moderating action.', 'La misma relación protectora vista desde la hierba que realiza la acción moderadora.'],
    ['Mutual antagonism', 'Antagonismo mutuo', 'The combination may weaken or neutralize a desired therapeutic effect.', 'La combinación puede debilitar o neutralizar un efecto terapéutico deseado.'],
    ['Mutual incompatibility', 'Incompatibilidad mutua', 'The combination may create toxicity or adverse effects not produced by either herb alone.', 'La combinación puede crear toxicidad o efectos adversos que ninguna hierba produciría por sí sola.'],
    ['Single effect', 'Efecto único', 'One medicinal substance is used alone for a focused purpose.', 'Una sustancia medicinal se utiliza sola para un propósito específico.'],
];

$specialdecoction = [
    ['Decoct first', 'Decocer primero', 'Toxic substances requiring prolonged preparation, minerals/shells and some bulky lightweight materials.', 'Sustancias tóxicas que requieren preparación prolongada, minerales/conchas y algunos materiales ligeros voluminosos.'],
    ['Add near the end', 'Añadir al final', 'Aromatic or volatile herbs whose active qualities may be lost with prolonged cooking.', 'Hierbas aromáticas o volátiles cuyas cualidades activas pueden perderse con cocción prolongada.'],
    ['Wrap before decocting', 'Envolver antes de decocer', 'Fine hairs, tiny seeds or powders that could irritate or cloud the strained liquid.', 'Vellos finos, semillas pequeñas o polvos que podrían irritar o enturbiar el líquido colado.'],
    ['Prepare separately', 'Preparar por separado', 'Rare or expensive substances when separate extraction protects their intended use.', 'Sustancias raras o costosas cuando la extracción separada protege su uso previsto.'],
    ['Dissolve after straining', 'Disolver después de colar', 'Sticky substances that could adhere to the pot or other ingredients and burn.', 'Sustancias pegajosas que podrían adherirse a la olla u otros ingredientes y quemarse.'],
    ['Take with the decoction', 'Tomar con la decocción', 'Certain finely powdered substances are administered with, rather than cooked inside, the strained liquid.', 'Ciertas sustancias finamente pulverizadas se administran con el líquido colado en vez de cocinarse dentro de él.'],
];

$safetypillars = [
    ['Correct identity', 'Identidad correcta', 'Match pinyin with the pharmaceutical name and medicinal part; one name alone may identify the wrong substance.', 'Relaciona pinyin con nombre farmacéutico y parte medicinal; un solo nombre puede identificar la sustancia equivocada.'],
    ['Appropriate processing', 'Procesamiento apropiado', 'Processing may alter potency, direction, digestibility or toxicity. Raw and prepared forms are not automatically interchangeable.', 'El procesamiento puede modificar potencia, dirección, digestibilidad o toxicidad. Las formas crudas y preparadas no son automáticamente intercambiables.'],
    ['Quality and contamination', 'Calidad y contaminación', 'Source, storage, mold, microbes, pesticides and heavy metals affect safety and consistency.', 'Origen, almacenamiento, moho, microbios, pesticidas y metales pesados afectan seguridad y consistencia.'],
    ['Toxicity and allergy', 'Toxicidad y alergia', 'No reported toxicity does not prove zero risk; monitor uncommon reactions and respect normal dosage and preparation.', 'La ausencia de toxicidad reportada no demuestra riesgo cero; vigila reacciones infrecuentes y respeta dosis y preparación.'],
    ['Interactions and patient factors', 'Interacciones y factores del paciente', 'Medication use, pregnancy, age, constitution, digestive capacity and severity can change the decision.', 'Medicamentos, embarazo, edad, constitución, capacidad digestiva y gravedad pueden cambiar la decisión.'],
];
?>

<section class="single-herbs-intro" aria-labelledby="single-herbs-intro-foundations">
    <div class="single-herbs-intro__callout">
        <p class="single-herbs-guide__eyebrow"><?php echo s($introtext('Orientation', 'Orientación')); ?></p>
        <h4 id="single-herbs-intro-foundations"><?php echo s($introtext('From materia medica to a Board decision', 'De materia médica a una decisión del Board')); ?></h4>
        <p><?php echo s($introtext(
            'Single-herb study connects an observable medicinal substance with a category, properties, channel affinities, actions, indications, dosage and safety. Memorization is the foundation; the goal is to choose and justify the best herb in context.',
            'El estudio de hierbas individuales conecta una sustancia medicinal observable con categoría, propiedades, afinidades de meridianos, acciones, indicaciones, dosis y seguridad. La memorización es la base; la meta es elegir y justificar la mejor hierba en contexto.'
        )); ?></p>
    </div>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-history">
        <h4 id="single-herbs-intro-history"><?php echo s($introtext('A brief history of Chinese materia medica', 'Breve historia de la materia médica china')); ?></h4>
        <p><?php echo s($introtext(
            'Chinese herbal knowledge expanded for nearly two millennia through observation, textual transmission, regional exchange and the gradual integration of empirical effects with medical theory.',
            'El conocimiento herbal chino se amplió durante casi dos milenios mediante observación, transmisión textual, intercambio regional e integración gradual de efectos empíricos con teoría médica.'
        )); ?></p>
        <div class="single-herbs-intro__timeline">
            <?php foreach ($history as [$titleEn, $titleEs, $bodyEn, $bodyEs]): ?>
                <article><strong><?php echo s($introtext($titleEn, $titleEs)); ?></strong><span><?php echo s($introtext($bodyEn, $bodyEs)); ?></span></article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-profile">
        <h4 id="single-herbs-intro-profile"><?php echo s($introtext('How to read an individual-herb profile', 'Cómo leer el perfil de una hierba individual')); ?></h4>
        <div class="single-herbs-intro__reading-levels">
            <article><strong><?php echo s($introtext('Level 1 · Essential', 'Nivel 1 · Esencial')); ?></strong><span><?php echo s($introtext('Identity, properties, channels, key characteristic, dosage and cautions.', 'Identidad, propiedades, meridianos, característica clave, dosis y precauciones.')); ?></span></article>
            <article><strong><?php echo s($introtext('Level 2 · Clinical', 'Nivel 2 · Clínico')); ?></strong><span><?php echo s($introtext('Actions, indications and representative combinations.', 'Acciones, indicaciones y combinaciones representativas.')); ?></span></article>
            <article><strong><?php echo s($introtext('Level 3 · Deep study', 'Nivel 3 · Estudio profundo')); ?></strong><span><?php echo s($introtext('Commentary, mechanisms, comparisons, processing, quality and specialized safety.', 'Comentario, mecanismos, comparaciones, procesamiento, calidad y seguridad especializada.')); ?></span></article>
        </div>
        <div class="single-herbs-intro__profile-grid">
            <?php foreach ($profilefields as [$titleEn, $titleEs, $bodyEn, $bodyEs]): ?>
                <article><strong><?php echo s($introtext($titleEn, $titleEs)); ?></strong><span><?php echo s($introtext($bodyEn, $bodyEs)); ?></span></article>
            <?php endforeach; ?>
        </div>
        <div class="single-herbs-guide__note"><strong><?php echo s($introtext('Identification rule:', 'Regla de identificación:')); ?></strong> <?php echo s($introtext(
            'Do not rely on pinyin, a common English name or a pharmaceutical name alone. Pair the accepted pinyin with the pharmaceutical identity and medicinal part; check the standard species when dispensing is involved.',
            'No dependas únicamente del pinyin, nombre común inglés o nombre farmacéutico. Combina el pinyin aceptado con identidad farmacéutica y parte medicinal; verifica la especie estándar cuando intervenga dispensación.'
        )); ?></div>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-nature">
        <h4 id="single-herbs-intro-nature"><?php echo s($introtext('Temperature or nature: the thermal direction', 'Temperatura o naturaleza: la dirección térmica')); ?></h4>
        <div class="single-herbs-intro__nature-scale" role="img" aria-label="<?php echo s($introtext('Scale from hot to cold', 'Escala de caliente a fría')); ?>">
            <span class="is-hot"><?php echo s($introtext('Hot', 'Caliente')); ?></span>
            <span class="is-warm"><?php echo s($introtext('Warm', 'Tibia')); ?></span>
            <span class="is-neutral"><?php echo s($introtext('Neutral', 'Neutra')); ?></span>
            <span class="is-cool"><?php echo s($introtext('Cool', 'Fresca')); ?></span>
            <span class="is-cold"><?php echo s($introtext('Cold', 'Fría')); ?></span>
        </div>
        <p><?php echo s($introtext(
            'Nature suggests the thermal situation for which an herb may be appropriate: Heat generally calls for cooling and Cold for warming. These labels are comparative and may vary slightly among sources, so they must be interpreted with the complete profile.',
            'La naturaleza sugiere la situación térmica para la cual una hierba puede ser apropiada: el Calor generalmente requiere enfriar y el Frío calentar. Estas etiquetas son comparativas y pueden variar ligeramente entre fuentes, por lo que deben interpretarse con el perfil completo.'
        )); ?></p>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-tastes">
        <h4 id="single-herbs-intro-tastes"><?php echo s($introtext('Flavor: a functional tendency', 'Sabor: una tendencia funcional')); ?></h4>
        <div class="single-herbs-intro__taste-grid">
            <?php foreach ($tastes as [$nameEn, $nameEs, $effectEn, $effectEs]): ?>
                <article><strong><?php echo s($introtext($nameEn, $nameEs)); ?></strong><span><?php echo s($introtext($effectEn, $effectEs)); ?></span></article>
            <?php endforeach; ?>
        </div>
        <p class="single-herbs-guide__note"><strong><?php echo s($introtext('Important:', 'Importante:')); ?></strong> <?php echo s($introtext(
            'Flavor helps explain a tendency; it rarely determines the answer alone. Aromatic describes penetration through turbidity, while astringent emphasizes containment of abnormal leakage.',
            'El sabor ayuda a explicar una tendencia; rara vez determina por sí solo la respuesta. Aromático describe penetración a través de turbidez, mientras astringente enfatiza contener pérdidas anormales.'
        )); ?></p>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-actions">
        <h4 id="single-herbs-intro-actions"><?php echo s($introtext('From properties to actions', 'De las propiedades a las acciones')); ?></h4>
        <p><?php echo s($introtext(
            'Later materia-medica traditions connected empirical effects with classical theory. Taste, nature and channels help explain why an herb acts in a particular direction or organ system, but the recorded actions and indications remain the decisive clinical evidence.',
            'Las tradiciones posteriores conectaron los efectos empíricos con la teoría clásica. Sabor, naturaleza y meridianos ayudan a explicar por qué una hierba actúa en determinada dirección o sistema orgánico, pero las acciones e indicaciones registradas siguen siendo la evidencia clínica decisiva.'
        )); ?></p>
        <div class="single-herbs-intro__methods">
            <?php foreach ([
                ['Promote sweating', 'Promover sudoración'], ['Induce vomiting', 'Inducir vómito'],
                ['Purge', 'Purgar'], ['Harmonize', 'Armonizar'], ['Warm', 'Calentar'],
                ['Clear', 'Aclarar'], ['Tonify', 'Tonificar'], ['Reduce', 'Reducir'],
            ] as [$methodEn, $methodEs]): ?><span><?php echo s($introtext($methodEn, $methodEs)); ?></span><?php endforeach; ?>
        </div>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-channels">
        <h4 id="single-herbs-intro-channels"><?php echo s($introtext('Channels entered: affinity and clinical shorthand', 'Meridianos penetrados: afinidad y síntesis clínica')); ?></h4>
        <div class="single-herbs-intro__two-column">
            <article>
                <strong><?php echo s($introtext('What the channel tells you', 'Qué indica el meridiano')); ?></strong>
                <p><?php echo s($introtext(
                    'A channel entry summarizes where an herb’s major actions are expressed: an organ system, region or pathological change. It helps distinguish herbs that share a category or action.',
                    'La entrada a un meridiano resume dónde se expresan las acciones principales: sistema orgánico, región o cambio patológico. Ayuda a distinguir hierbas que comparten categoría o acción.'
                )); ?></p>
            </article>
            <article>
                <strong><?php echo s($introtext('What it does not tell you', 'Qué no indica')); ?></strong>
                <p><?php echo s($introtext(
                    'Channel attribution is one of the most variable basic properties across sources. Treat it as functional shorthand—not proof that every action occurs only in that channel.',
                    'La atribución de meridianos es una de las propiedades básicas más variables entre fuentes. Trátala como síntesis funcional, no como prueba de que cada acción ocurre únicamente allí.'
                )); ?></p>
            </article>
        </div>
        <p><?php echo s($introtext(
            'Some herbs may also guide a prescription toward a channel or organ. This envoy-like function is contextual: first confirm the formula’s strategy and the herb’s own actions.',
            'Algunas hierbas también pueden guiar una prescripción hacia un meridiano u órgano. Esta función semejante a la de mensajero depende del contexto: primero confirma la estrategia de la fórmula y las acciones propias de la hierba.'
        )); ?></p>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-direction">
        <h4 id="single-herbs-intro-direction"><?php echo s($introtext('Directional tendency: rise, fall, float and sink', 'Tendencia direccional: ascender, descender, flotar y hundir')); ?></h4>
        <div class="single-herbs-intro__direction-grid">
            <article><strong>↑ <?php echo s($introtext('Rise', 'Ascender')); ?></strong><span><?php echo s($introtext('Moves Qi upward and may raise what has fallen.', 'Mueve Qi hacia arriba y puede elevar lo que ha descendido.')); ?></span></article>
            <article><strong>↗ <?php echo s($introtext('Float', 'Flotar')); ?></strong><span><?php echo s($introtext('Moves outward toward the Exterior, disperses and may promote sweating.', 'Se dirige hacia el Exterior, dispersa y puede promover sudoración.')); ?></span></article>
            <article><strong>↓ <?php echo s($introtext('Fall', 'Descender')); ?></strong><span><?php echo s($introtext('Redirects rebellious Qi and moves activity downward.', 'Redirige Qi rebelde y mueve la actividad hacia abajo.')); ?></span></article>
            <article><strong>↘ <?php echo s($introtext('Sink', 'Hundir')); ?></strong><span><?php echo s($introtext('Moves inward, anchors rising Yang or helps contain abnormal leakage.', 'Se dirige hacia dentro, ancla Yang ascendente o ayuda a contener pérdidas anormales.')); ?></span></article>
        </div>
        <p><?php echo s($introtext(
            'These are relative tendencies, not rigid labels. Preparation, dosage and combination can alter how an herb behaves within a prescription.',
            'Son tendencias relativas, no etiquetas rígidas. Preparación, dosis y combinación pueden modificar el comportamiento de una hierba dentro de una prescripción.'
        )); ?></p>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-combination">
        <h4 id="single-herbs-intro-combination"><?php echo s($introtext('From one herb to a combination', 'De una hierba a una combinación')); ?></h4>
        <p><?php echo s($introtext(
            'Herbs are combined to strengthen effectiveness, address complex patterns, change therapeutic direction, or reduce toxicity and side effects. Understanding the individual herbs comes before judging the relationship.',
            'Las hierbas se combinan para fortalecer eficacia, abordar patrones complejos, cambiar dirección terapéutica o reducir toxicidad y efectos adversos. Comprender las hierbas individuales precede a juzgar la relación.'
        )); ?></p>
        <div class="single-herbs-intro__relationships">
            <?php foreach ($relationships as [$titleEn, $titleEs, $bodyEn, $bodyEs]): ?>
                <article><strong><?php echo s($introtext($titleEn, $titleEs)); ?></strong><span><?php echo s($introtext($bodyEn, $bodyEs)); ?></span></article>
            <?php endforeach; ?>
        </div>
        <div class="single-herbs-guide__note"><strong>Jun–Chen–Zuo–Shi:</strong> <?php echo s($introtext(
            'the chief supplies the main therapeutic thrust; deputies reinforce it; assistants treat secondary aspects or moderate harshness; envoys guide or harmonize. Not every prescription requires all four roles.',
            'la hierba principal aporta el impulso terapéutico; las ministras lo refuerzan; las asistentes tratan aspectos secundarios o moderan agresividad; las mensajeras guían o armonizan. No toda prescripción necesita los cuatro papeles.'
        )); ?></div>
        <div class="single-herbs-guide__safety"><strong><?php echo s($introtext('Combination safety', 'Seguridad de combinaciones')); ?></strong><?php echo s($introtext(
            'Traditional antagonisms and incompatibilities are historically important but have changed with evidence and practice. Never infer that a pair is safe or unsafe from its label alone; verify the current monograph, processing, dose, interaction profile and professional guidance.',
            'Los antagonismos e incompatibilidades tradicionales son históricamente importantes, pero han cambiado con la evidencia y la práctica. Nunca deduzcas que una pareja es segura o insegura solo por su etiqueta; verifica monografía vigente, procesamiento, dosis, interacciones y orientación profesional.'
        )); ?></div>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-dose">
        <h4 id="single-herbs-intro-dose"><?php echo s($introtext('Dosage is part of the strategy', 'La dosis forma parte de la estrategia')); ?></h4>
        <p><?php echo s($introtext(
            'A common crude-herb study range is 3–9 g, but it is not a universal default. Dose can emphasize or mute an herb and can change the character of a prescription.',
            'Un rango de estudio común para hierba cruda es 3–9 g, pero no es un valor universal. La dosis puede enfatizar o moderar una hierba y cambiar el carácter de una prescripción.'
        )); ?></p>
        <div class="single-herbs-intro__dose-factors">
            <?php foreach ([
                ['Substance', 'Sustancia', 'Weight, density, flavor strength and toxicity.', 'Peso, densidad, intensidad de sabor y toxicidad.'],
                ['Medicinal part', 'Parte medicinal', 'Flowers, leaves and aromatics often differ from roots, fruits, minerals and shells.', 'Flores, hojas y aromáticas suelen diferir de raíces, frutos, minerales y conchas.'],
                ['Formula context', 'Contexto de fórmula', 'A single herb or small formula may use a different amount than a large combination.', 'Una hierba sola o fórmula pequeña puede usar una cantidad diferente a una combinación grande.'],
                ['Patient and pattern', 'Paciente y patrón', 'Severity, age, constitution, duration and digestive capacity matter.', 'Gravedad, edad, constitución, duración y capacidad digestiva importan.'],
                ['Preparation', 'Preparación', 'Processing and delivery form can alter strength and intended action.', 'Procesamiento y forma de administración pueden modificar fuerza y acción prevista.'],
            ] as [$titleEn, $titleEs, $bodyEn, $bodyEs]): ?>
                <article><strong><?php echo s($introtext($titleEn, $titleEs)); ?></strong><span><?php echo s($introtext($bodyEn, $bodyEs)); ?></span></article>
            <?php endforeach; ?>
        </div>
        <p class="single-herbs-guide__safety"><strong><?php echo s($introtext('Clinical boundary', 'Límite clínico')); ?></strong><?php echo s($introtext(
            'The ranges in this guide are educational references. Individual dosing—especially for children, older or frail patients, pregnancy, medication use or toxic substances—requires qualified professional judgment.',
            'Los rangos de esta guía son referencias educativas. La dosificación individual—especialmente en niños, personas mayores o frágiles, embarazo, uso de medicamentos o sustancias tóxicas—requiere juicio profesional cualificado.'
        )); ?></p>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-processing">
        <h4 id="single-herbs-intro-processing"><?php echo s($introtext('Processing and delivery change the medicine', 'Procesamiento y administración cambian la medicina')); ?></h4>
        <p><?php echo s($introtext(
            'Cleaning, cutting, pulverizing, washing, soaking, dry-frying, frying with liquids, calcining, steaming and other processes may improve extraction, redirect an action, protect digestion or reduce harshness and toxicity.',
            'Limpieza, corte, pulverización, lavado, remojo, salteado, procesado con líquidos, calcinación, cocción al vapor y otros métodos pueden mejorar extracción, redirigir una acción, proteger digestión o reducir agresividad y toxicidad.'
        )); ?></p>
        <div class="single-herbs-intro__processing-grid">
            <article><strong><?php echo s($introtext('Mechanical', 'Mecánico')); ?></strong><span><?php echo s($introtext('Clean, sort, peel, grind or slice to remove non-medicinal matter and improve extraction.', 'Limpiar, seleccionar, pelar, moler o cortar para eliminar material no medicinal y mejorar extracción.')); ?></span></article>
            <article><strong><?php echo s($introtext('Water-based', 'Con agua')); ?></strong><span><?php echo s($introtext('Wash, soak or triturate to clean, soften, prepare for cutting or reduce harsh qualities.', 'Lavar, remojar o triturar para limpiar, ablandar, preparar el corte o reducir cualidades agresivas.')); ?></span></article>
            <article><strong><?php echo s($introtext('Heat-based', 'Con calor')); ?></strong><span><?php echo s($introtext('Dry-fry, bake, calcine or roast to alter dryness, brittleness, direction or toxicity.', 'Saltear, hornear, calcinar o asar para modificar sequedad, fragilidad, dirección o toxicidad.')); ?></span></article>
            <article><strong><?php echo s($introtext('Heat plus liquid', 'Calor más líquido')); ?></strong><span><?php echo s($introtext('Steam, boil, quench or simmer to transform properties or create a prepared form.', 'Cocer al vapor, hervir, templar o concentrar para transformar propiedades o crear una forma preparada.')); ?></span></article>
        </div>

        <h5><?php echo s($introtext('Special decoction instructions to recognize', 'Instrucciones especiales de decocción que debes reconocer')); ?></h5>
        <div class="single-herbs-intro__special-grid">
            <?php foreach ($specialdecoction as [$titleEn, $titleEs, $bodyEn, $bodyEs]): ?>
                <article><strong><?php echo s($introtext($titleEn, $titleEs)); ?></strong><span><?php echo s($introtext($bodyEn, $bodyEs)); ?></span></article>
            <?php endforeach; ?>
        </div>
        <p class="single-herbs-guide__note"><strong><?php echo s($introtext('Delivery forms:', 'Formas de administración:')); ?></strong> <?php echo s($introtext(
            'Decoctions are adaptable and historically common, but cooking time, sequence, timing and temperature vary with the formula. Follow the written prescription and pharmacist instructions rather than applying one method to every herb.',
            'Las decocciones son adaptables e históricamente comunes, pero tiempo, secuencia, momento y temperatura varían según la fórmula. Sigue la prescripción escrita y las instrucciones farmacéuticas en vez de aplicar un método a todas las hierbas.'
        )); ?></p>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-safety-pillars">
        <h4 id="single-herbs-intro-safety-pillars"><?php echo s($introtext('Five safety pillars', 'Cinco pilares de seguridad')); ?></h4>
        <div class="single-herbs-intro__safety-grid">
            <?php foreach ($safetypillars as [$titleEn, $titleEs, $bodyEn, $bodyEs]): ?>
                <article><strong><?php echo s($introtext($titleEn, $titleEs)); ?></strong><span><?php echo s($introtext($bodyEn, $bodyEs)); ?></span></article>
            <?php endforeach; ?>
        </div>
        <p><?php echo s($introtext(
            'A standard species, an alternate species and an adulterant are not interchangeable labels. Precise identity and a reliable supply chain are clinical safety issues, not merely botanical details.',
            'Especie estándar, especie alternativa y adulterante no son etiquetas intercambiables. La identidad precisa y una cadena de suministro confiable son asuntos de seguridad clínica, no simples detalles botánicos.'
        )); ?></p>
    </section>

    <section class="single-herbs-intro__section" aria-labelledby="single-herbs-intro-board">
        <h4 id="single-herbs-intro-board"><?php echo s($introtext('Board study workflow', 'Método de estudio para el Board')); ?></h4>
        <ol class="single-herbs-intro__workflow">
            <li><?php echo s($introtext('Identify the category and treatment direction.', 'Identifica la categoría y la dirección terapéutica.')); ?></li>
            <li><?php echo s($introtext('Build the signature: nature → primary flavor → primary channels.', 'Construye la firma: naturaleza → sabor principal → meridianos principales.')); ?></li>
            <li><?php echo s($introtext('State the distinguishing action and decisive indication.', 'Expresa la acción distintiva y la indicación decisiva.')); ?></li>
            <li><?php echo s($introtext('Compare the closest competing herb.', 'Compárala con la hierba competidora más cercana.')); ?></li>
            <li><?php echo s($introtext('Confirm dose, preparation and safety before choosing.', 'Confirma dosis, preparación y seguridad antes de elegir.')); ?></li>
        </ol>
        <div class="single-herbs-guide__safety"><strong><?php echo s($introtext('Interpretive safeguard', 'Salvaguarda interpretativa')); ?></strong><?php echo s($introtext(
            'Properties are study tools, not automatic prescribing rules. A complete decision requires pattern differentiation, the herb’s full indications, dosage, preparation, contraindications and current professional guidance.',
            'Las propiedades son herramientas de estudio, no reglas automáticas de prescripción. Una decisión completa exige diferenciación de patrón, indicaciones completas, dosis, preparación, contraindicaciones y orientación profesional vigente.'
        )); ?></div>
    </section>

    <p class="single-herbs-guide__source"><strong><?php echo s($introtext('Source note:', 'Nota de fuente:')); ?></strong> <?php echo s($introtext(
        'Educational synthesis based on the supplied introduction excerpts covering materia-medica history, monograph use, identification, tastes and properties, channels, direction, combination theory, dosage, processing, delivery and safety.',
        'Síntesis educativa basada en los fragmentos introductorios suministrados sobre historia de la materia médica, uso de monografías, identificación, sabores y propiedades, meridianos, dirección, combinaciones, dosis, procesamiento, administración y seguridad.'
    )); ?></p>
</section>
