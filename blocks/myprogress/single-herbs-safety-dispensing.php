<?php
// Safety and dispensing reference for the official Appendix A roster.

defined('MOODLE_INTERNAL') || die();

$safetytext = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};
$safetylang = $ises ? 'es' : 'en';
$safetypropertiespath = __DIR__ . '/single-herb-properties.json';
$safetyindexpath = __DIR__ . '/single-herb-card-index.json';
$safetysupplementpath = __DIR__ . '/single-herb-safety-supplement.json';
$safetyproperties = is_readable($safetypropertiespath)
    ? json_decode((string)file_get_contents($safetypropertiespath), true)
    : null;
$safetyindex = is_readable($safetyindexpath)
    ? json_decode((string)file_get_contents($safetyindexpath), true)
    : null;
$safetysupplement = is_readable($safetysupplementpath)
    ? json_decode((string)file_get_contents($safetysupplementpath), true)
    : null;
$safetyrows = is_array($safetyproperties) ? ($safetyproperties['herbs'] ?? []) : [];
$safetydefaults = is_array($safetysupplement) ? ($safetysupplement['defaults'] ?? []) : [];
$safetycategorycautions = is_array($safetysupplement) ? ($safetysupplement['categoryCautions'] ?? []) : [];
$safetycategorypregnancy = is_array($safetysupplement) ? ($safetysupplement['categoryPregnancy'] ?? []) : [];
$safetycategoryinteractions = is_array($safetysupplement) ? ($safetysupplement['categoryInteractions'] ?? []) : [];
$safetyherbsupplements = is_array($safetysupplement) ? ($safetysupplement['herbs'] ?? []) : [];
$safetycategorybyname = [];
$safetyidentitybyname = [];
$safetycategoryoptions = [];
$safetyrowid = static function(string $name): string {
    return 'safety-herb-' . trim(strtolower((string)preg_replace('/[^a-z0-9]+/i', '-', $name)), '-');
};
foreach (is_array($safetyindex) ? ($safetyindex['herbs'] ?? []) : [] as $safetyindexrow) {
    $safetycategory = $safetyindexrow['benskyCategory'] ?? null;
    if (!is_array($safetycategory) || empty($safetyindexrow['name']) || empty($safetycategory['code'])) {
        continue;
    }
    $safetycategorybyname[(string)$safetyindexrow['name']] = $safetycategory;
    $safetyidentitybyname[(string)$safetyindexrow['name']] = [
        'aliases' => array_values(array_filter(array_map('strval', $safetyindexrow['aliases'] ?? []))),
        'note' => (string)($safetyindexrow['identityNote'][$safetylang] ?? ''),
    ];
    $safetycategoryoptions[(string)$safetycategory['code']] = $safetycategory;
}
uasort($safetycategoryoptions, static function(array $left, array $right) use ($safetylang): int {
    return strcasecmp((string)($left[$safetylang] ?? ''), (string)($right[$safetylang] ?? ''));
});

$safetycontains = static function(string $value, array $needles): bool {
    $value = strtolower($value);
    foreach ($needles as $needle) {
        if (str_contains($value, $needle)) {
            return true;
        }
    }
    return false;
};
$preparationkeywords = ['decoct', 'prepar', 'raw', 'calcined', 'charred', 'wrapped', 'powder', 'form', 'fry', 'soak', 'cruda', 'calcin', 'carboniz', 'decoc', 'envuel', 'polvo', 'forma'];
$pregnancykeywords = ['pregnan', 'fetus', 'postpartum', 'embaraz', 'feto', 'posparto'];
$interactionkeywords = ['incompatib', 'interaction', 'antagon', 'combine with', 'warfarin', 'anticoagul', 'interacción', 'combinar con'];

$completecoverage = 0;
$herbspecificsafety = 0;
$structuredpreparation = 0;
$structuredpregnancy = 0;
$structuredinteractions = 0;
$preparedrows = [];
foreach ($safetyrows as $safetyposition => $safetyrow) {
    $dose = trim((string)($safetyrow['dose'] ?? ''));
    $doseparts = array_map('trim', explode(';', $dose, 2));
    $dosage = $doseparts[0] ?? '';
    $doseinstruction = $doseparts[1] ?? '';
    $safetynote = trim((string)($safetyrow['safetyNote'][$safetylang] ?? ''));
    $safetysearchnote = trim(implode(' ', array_filter([
        (string)($safetyrow['safetyNote']['en'] ?? ''),
        (string)($safetyrow['safetyNote']['es'] ?? ''),
    ])));
    $category = $safetycategorybyname[(string)($safetyrow['name'] ?? '')] ?? [];
    $identity = $safetyidentitybyname[(string)($safetyrow['name'] ?? '')] ?? ['aliases' => [], 'note' => ''];
    $categorycode = (string)($category['code'] ?? '');
    $herbsupplement = $safetyherbsupplements[(string)($safetyrow['name'] ?? '')] ?? [];

    $specificpreparation = trim((string)($herbsupplement['preparation'][$safetylang] ?? ''));
    if ($specificpreparation === '') {
        $specificpreparation = $doseinstruction;
    }
    if ($specificpreparation === '' && $safetynote !== '' && $safetycontains($safetysearchnote, $preparationkeywords)) {
        $specificpreparation = $safetynote;
    }
    $preparationnote = $specificpreparation !== ''
        ? $specificpreparation
        : trim((string)($safetydefaults['preparation'][$safetylang] ?? ''));

    $specificcaution = trim((string)($herbsupplement['caution'][$safetylang] ?? ''));
    $toxicitynote = $specificcaution !== ''
        ? $specificcaution
        : ($safetynote !== ''
            ? $safetynote
            : trim((string)($safetycategorycautions[$categorycode][$safetylang] ?? '')));

    $specificpregnancy = trim((string)($herbsupplement['pregnancy'][$safetylang] ?? ''));
    if ($specificpregnancy === '' && $safetynote !== '' && $safetycontains($safetysearchnote, $pregnancykeywords)) {
        $specificpregnancy = $safetynote;
    }
    if ($specificpregnancy === '') {
        $specificpregnancy = trim((string)($safetycategorypregnancy[$categorycode][$safetylang] ?? ''));
    }
    $pregnancynote = $specificpregnancy !== ''
        ? $specificpregnancy
        : trim((string)($safetydefaults['pregnancy'][$safetylang] ?? ''));

    $specificinteraction = trim((string)($herbsupplement['interactions'][$safetylang] ?? ''));
    if ($specificinteraction === '' && $safetynote !== '' && $safetycontains($safetysearchnote, $interactionkeywords)) {
        $specificinteraction = $safetynote;
    }
    if ($specificinteraction === '') {
        $specificinteraction = trim((string)($safetycategoryinteractions[$categorycode][$safetylang] ?? ''));
    }
    $interactionnote = $specificinteraction !== ''
        ? $specificinteraction
        : trim((string)($safetydefaults['interactions'][$safetylang] ?? ''));

    $preparedrows[] = [
        'position' => $safetyposition,
        'name' => (string)($safetyrow['name'] ?? ''),
        'latin' => (string)($safetyrow['latin'] ?? ''),
        'categorycode' => $categorycode,
        'category' => (string)($category[$safetylang] ?? ''),
        'dose' => $dosage,
        'preparation' => $preparationnote,
        'toxicity' => $toxicitynote,
        'pregnancy' => $pregnancynote,
        'interactions' => $interactionnote,
        'aliases' => $identity['aliases'],
        'identitynote' => $identity['note'],
        'haspreparation' => $specificpreparation !== '',
        'hastoxicity' => $specificcaution !== '' || $safetynote !== '',
        'haspregnancy' => $specificpregnancy !== '',
        'hasinteractions' => $specificinteraction !== '',
    ];
    $completecoverage += $preparationnote !== '' && $toxicitynote !== '' && $pregnancynote !== '' && $interactionnote !== '' ? 1 : 0;
    $herbspecificsafety += $specificcaution !== '' || $safetynote !== '' ? 1 : 0;
    $structuredpreparation += $specificpreparation !== '' ? 1 : 0;
    $structuredpregnancy += $specificpregnancy !== '' ? 1 : 0;
    $structuredinteractions += $specificinteraction !== '' ? 1 : 0;
}

$safetyquestions = [
    [
        $safetytext('What does a “not documented here” statement mean in the pregnancy or interaction column?', '¿Qué significa una frase de “no documentado aquí” en la columna de embarazo o interacciones?'),
        $safetytext('It marks the boundary of the structured table. It never means that the herb is safe, permitted in pregnancy, or free of interactions; the complete monograph, medicines and patient context must still be reviewed.', 'Marca el límite de la tabla estructurada. Nunca significa que la hierba sea segura, permitida en embarazo o libre de interacciones; todavía deben revisarse la monografía completa, los medicamentos y el contexto del paciente.'),
    ],
    [
        $safetytext('Bai Tou Weng has no additional herb-specific warning in this table. Which category precaution still applies?', 'Bai Tou Weng no tiene una advertencia adicional específica en esta tabla. ¿Qué precaución de categoría todavía se aplica?'),
        $safetytext('It belongs to herbs that clear Heat and resolve toxicity: its cold, clearing nature requires caution with Spleen/Stomach deficiency or chronic diarrhea, and serious infection requires biomedical evaluation.', 'Pertenece a las hierbas que aclaran Calor y resuelven toxicidad: su naturaleza fría y depurativa exige cautela en deficiencia de Bazo/Estómago o diarrea crónica, y una infección grave requiere evaluación biomédica.'),
    ],
    [
        $safetytext('What is the non-negotiable dispensing rule for Ban Xia?', '¿Cuál es la regla de dispensación no negociable para Ban Xia?'),
        $safetytext('Use only properly processed Ban Xia for internal administration. Raw material is toxic; avoid it with bleeding or yin-deficiency dry cough, and remember that sedative effects may be intensified by barbiturates.', 'Usar solo Ban Xia correctamente procesada para administración interna. La materia cruda es tóxica; evitarla con sangrado o tos seca por deficiencia de yin y recordar que los barbitúricos pueden intensificar los efectos sedantes.'),
    ],
    [
        $safetytext('Which processing and interaction warnings identify Chuan Wu as a high-risk herb?', '¿Qué advertencias de procesamiento e interacción identifican a Chuan Wu como una hierba de alto riesgo?'),
        $safetytext('Only properly processed material may be used, with the prescribed extended pre-decoction. Raw, underprocessed or excessive Chuan Wu is cardiotoxic and neurotoxic; avoid antiarrhythmics, cardiotoxic drugs, stimulants and other aconite products.', 'Solo puede usarse material correctamente procesado, con la decocción previa prolongada indicada. Chuan Wu cruda, insuficientemente procesada o excesiva es cardiotóxica y neurotóxica; evitar antiarrítmicos, fármacos cardiotóxicos, estimulantes y otros productos de acónito.'),
    ],
    [
        $safetytext('Why is Ba Dou never a self-treatment herb?', '¿Por qué Ba Dou nunca es una hierba de automedicación?'),
        $safetytext('It is a very toxic harsh expellant requiring specialist-only dosing and the correctly defatted preparation. It is contraindicated in pregnancy and weakness, and must not be combined with Qian Niu Zi.', 'Es un expulsor drástico muy tóxico que requiere dosificación exclusiva de especialista y la preparación correctamente desgrasada. Está contraindicada en embarazo y debilidad, y no debe combinarse con Qian Niu Zi.'),
    ],
    [
        $safetytext('Which history and medication clues make Ma Huang unsafe or inappropriate?', '¿Qué antecedentes y medicamentos vuelven insegura o inapropiada a Ma Huang?'),
        $safetytext('Uncontrolled hypertension, significant heart disease, hyperthyroidism, insomnia and spontaneous sweating are major cautions. Avoid additive stimulation with MAO inhibitors, stimulants, decongestants and medicines that raise heart rate or blood pressure.', 'Hipertensión no controlada, cardiopatía importante, hipertiroidismo, insomnio y sudor espontáneo son precauciones principales. Evitar estimulación aditiva con inhibidores de MAO, estimulantes, descongestionantes y medicamentos que aumentan frecuencia cardíaca o presión arterial.'),
    ],
    [
        $safetytext('What common safety rule links Chuan Mu Tong, Mu Tong and Han Fang Ji?', '¿Qué regla de seguridad común relaciona Chuan Mu Tong, Mu Tong y Han Fang Ji?'),
        $safetytext('Exact botanical identity is mandatory. Use only authenticated species and never accept unidentified or aristolochic-acid-containing substitutes; for Han Fang Ji, also monitor kidney function and fluid status.', 'La identidad botánica exacta es obligatoria. Usar solo especies autenticadas y nunca aceptar sustitutos no identificados o con ácido aristolóquico; con Han Fang Ji, además vigilar función renal y estado de líquidos.'),
    ],
    [
        $safetytext('What two distinctions must be checked before dispensing He Shou Wu?', '¿Qué dos distinciones deben verificarse antes de dispensar He Shou Wu?'),
        $safetytext('Prepared and unprepared forms have different actions and are not interchangeable. Review liver disease and hepatotoxic medicines because clinically significant hepatotoxicity has been reported; also use caution with anticoagulants, glucose-lowering drugs and laxatives.', 'Las formas preparada y sin preparar tienen acciones distintas y no son intercambiables. Revisar enfermedad hepática y medicamentos hepatotóxicos porque se ha descrito hepatotoxicidad clínicamente significativa; también usar cautela con anticoagulantes, hipoglucemiantes y laxantes.'),
    ],
    [
        $safetytext('What preparation prevents avoidable Bai Guo toxicity?', '¿Qué preparación previene toxicidad evitable por Bai Guo?'),
        $safetytext('Use mature, properly prepared seeds, cook them thoroughly and remove the toxic embryo when required. Excessive or raw use may cause nausea, vomiting, dyspnea, seizures or loss of consciousness.', 'Usar semillas maduras y correctamente preparadas, cocinarlas por completo y retirar el embrión tóxico cuando corresponda. El uso excesivo o crudo puede causar náuseas, vómitos, disnea, convulsiones o pérdida de conciencia.'),
    ],
    [
        $safetytext('Which shared dispensing rule applies to She Xiang and Bing Pian?', '¿Qué regla de dispensación comparten She Xiang y Bing Pian?'),
        $safetytext('Do not decoct them; add only the prescribed small amount to pills, powders or topical preparations. Both are potent aromatic substances and are avoided in pregnancy; She Xiang additionally requires legal authenticated sourcing.', 'No deben decoctarse; añadir solo la pequeña cantidad indicada a píldoras, polvos o preparados tópicos. Ambas son sustancias aromáticas potentes y se evitan en embarazo; She Xiang además requiere origen legal y autenticado.'),
    ],
    [
        $safetytext('How do the preparation instructions for Gui Ban, Bie Jia and Hua Shi differ?', '¿Cómo difieren las instrucciones de preparación de Gui Ban, Bie Jia y Hua Shi?'),
        $safetytext('Crush and pre-decoct the heavy Gui Ban and Bie Jia before the other herbs. Wrap the fine mineral Hua Shi in cloth or a decoction bag before boiling.', 'Triturar y realizar decocción previa de las pesadas Gui Ban y Bie Jia antes de las demás hierbas. Envolver el mineral fino Hua Shi en tela o bolsa de decocción antes de hervir.'),
    ],
    [
        $safetytext('Which three seeds require a mechanical preparation step immediately before or during decoction?', '¿Qué tres semillas requieren un paso mecánico inmediatamente antes o durante la decocción?'),
        $safetytext('Che Qian Zi is wrapped in cloth or a decoction bag because it becomes mucilaginous. Ting Li Zi and Zi Su Zi are crushed immediately before decoction.', 'Che Qian Zi se envuelve en tela o bolsa porque se vuelve mucilaginosa. Ting Li Zi y Zi Su Zi se trituran inmediatamente antes de la decocción.'),
    ],
    [
        $safetytext('Name the pregnancy rule shared by strong blood-moving herbs such as Hong Hua, San Leng, Chuan Niu Xi, Huai Niu Xi, Yi Mu Cao, Tu Bie Chong and Wang Bu Liu Xing.', 'Indica la regla de embarazo compartida por movilizadoras fuertes de Sangre como Hong Hua, San Leng, Chuan Niu Xi, Huai Niu Xi, Yi Mu Cao, Tu Bie Chong y Wang Bu Liu Xing.'),
        $safetytext('They are contraindicated or avoided during pregnancy because they invigorate Blood, break accumulations, guide Blood downward or stimulate the uterus and labor pathways. A specialist exception requires a compelling indication.', 'Están contraindicadas o se evitan durante el embarazo porque vigorizan Sangre, rompen acumulaciones, guían Sangre hacia abajo o estimulan útero y vías del parto. Una excepción especializada requiere una indicación imprescindible.'),
    ],
    [
        $safetytext('What safety pattern links Quan Xie and Wu Gong?', '¿Qué patrón de seguridad relaciona Quan Xie y Wu Gong?'),
        $safetytext('Both are toxic animal substances requiring exact low dosing. Avoid them with Blood deficiency, bleeding tendency or pregnancy, and confirm allergy risk and legal authenticated sourcing.', 'Ambas son sustancias animales tóxicas que requieren dosis bajas exactas. Evitarlas en deficiencia de Sangre, tendencia al sangrado o embarazo y confirmar riesgo de alergia y origen legal autenticado.'),
    ],
    [
        $safetytext('When may Liu Huang be considered internally, and what is prohibited?', '¿Cuándo puede considerarse Liu Huang internamente y qué está prohibido?'),
        $safetytext('It is primarily external. Internal use requires the specifically prepared form, a clear Kidney-yang deficiency indication and expert supervision; crude sulfur must never be taken internally or applied unsupervised to large damaged skin areas.', 'Es principalmente externa. El uso interno requiere la forma específicamente preparada, una indicación clara de deficiencia de Yang de Riñón y supervisión experta; nunca debe ingerirse azufre crudo ni aplicarse sin supervisión sobre áreas extensas de piel lesionada.'),
    ],
    [
        $safetytext('Which medication class deserves special review with herbs that invigorate Blood?', '¿Qué clase de medicamentos requiere revisión especial con hierbas que vigorizan Sangre?'),
        $safetytext('Anticoagulant, antiplatelet and thrombolytic medicines, because additive bleeding may occur. Bleeding disorders, heavy menses, procedures and pregnancy also change the risk assessment.', 'Medicamentos anticoagulantes, antiagregantes y trombolíticos, porque puede ocurrir sangrado aditivo. Trastornos hemorrágicos, menstruación abundante, procedimientos y embarazo también cambian la evaluación de riesgo.'),
    ],
    [
        $safetytext('What must be monitored when a dampness-draining herb is combined with a pharmaceutical diuretic?', '¿Qué debe vigilarse al combinar una hierba que drena Humedad con un diurético farmacéutico?'),
        $safetytext('Fluid balance, electrolytes, blood pressure and kidney function. Yin-fluid deficiency, dehydration and marked urinary frequency increase concern.', 'Equilibrio de líquidos, electrolitos, presión arterial y función renal. Deficiencia de líquidos yin, deshidratación y frecuencia urinaria marcada aumentan la preocupación.'),
    ],
    [
        $safetytext('Which medicines require interaction review with herbs that calm the Spirit or strongly extinguish Wind?', '¿Qué medicamentos requieren revisión de interacción con hierbas que calman Shen o extinguen Viento con fuerza?'),
        $safetytext('Sedatives, hypnotics, anxiolytics, anticonvulsants, antihypertensives, neurologic medicines and alcohol may produce additive effects. Heavy mineral substances may also need separation from oral medicines.', 'Sedantes, hipnóticos, ansiolíticos, anticonvulsivos, antihipertensivos, medicamentos neurológicos y alcohol pueden producir efectos aditivos. Las sustancias minerales pesadas también pueden requerir separación de medicamentos orales.'),
    ],
    [
        $safetytext('Why should stabilizing and binding herbs not be used too early?', '¿Por qué no deben usarse demasiado pronto las hierbas que estabilizan y contienen?'),
        $safetytext('Their astringent action can trap an unresolved exterior pathogen, Heat, Dampness or another excess that still needs to be expelled. First confirm that leakage—not retained pathogenic excess—is the treatment target.', 'Su acción astringente puede atrapar un patógeno exterior no resuelto, Calor, Humedad u otro exceso que todavía debe expulsarse. Primero confirmar que la pérdida —y no un exceso patógeno retenido— es el objetivo.'),
    ],
    [
        $safetytext('What is the correct hierarchy for answering any safety question in this unit?', '¿Cuál es la jerarquía correcta para responder cualquier pregunta de seguridad de esta unidad?'),
        $safetytext('Start with the herb-specific warning and preparation. Then apply the Bensky category precaution, review pregnancy and medication interactions, confirm exact identity and legal sourcing, and finally consult the complete monograph and applicable law before dispensing.', 'Comenzar con la advertencia y preparación específicas de la hierba. Luego aplicar la precaución de categoría Bensky, revisar embarazo e interacciones medicamentosas, confirmar identidad exacta y origen legal y, por último, consultar la monografía completa y la normativa aplicable antes de dispensar.'),
    ],
];

$obsoleteparams = ['focus' => 'bensky19'];
if ($ises) {
    $obsoleteparams['lang'] = 'es';
}
$obsoleteurl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $obsoleteparams);
?>

<?php if (!$preparedrows): ?>
    <div class="single-herbs-guide__empty"><?php echo s($safetytext('Safety data is temporarily unavailable.', 'Los datos de seguridad no están disponibles temporalmente.')); ?></div>
<?php else: ?>
    <section class="single-herbs-properties single-herbs-safety" aria-labelledby="single-herbs-safety-heading">
        <div class="single-herbs-properties__summary">
            <div>
                <h4 id="single-herbs-safety-heading"><?php echo s($safetytext('Safety and dispensing master table', 'Tabla maestra de seguridad y dispensación')); ?></h4>
                <p><?php echo s($safetytext(
                    'Keeps the primary Bensky category and adds dosage, preparation, toxicity and cautions, pregnancy, interactions, obsolete-substance recognition and legal substitutes.',
                    'Conserva la categoría primaria de Bensky y añade dosis, preparación, toxicidad y precauciones, embarazo, interacciones, reconocimiento de sustancias obsoletas y sustitutos legales.'
                )); ?></p>
            </div>
            <div class="single-herbs-properties__stats" aria-label="<?php echo s($safetytext('Safety table summary', 'Resumen de la tabla de seguridad')); ?>">
                <span><strong><?php echo count($preparedrows); ?></strong><?php echo s($safetytext(' official', ' oficiales')); ?></span>
                <span><strong><?php echo count($safetycategoryoptions); ?></strong><?php echo s($safetytext(' categories', ' categorías')); ?></span>
                <span><strong><?php echo $completecoverage; ?></strong><?php echo s($safetytext(' complete safety rows', ' filas de seguridad completas')); ?></span>
                <span><strong><?php echo $herbspecificsafety; ?></strong><?php echo s($safetytext(' herb-specific cautions', ' precauciones específicas')); ?></span>
            </div>
        </div>

        <div class="single-herbs-properties__source-note">
            <strong><?php echo s($safetytext('Safety boundary:', 'Límite de seguridad:')); ?></strong>
            <?php echo s($safetytext(
                'Every official herb now has an explicit entry. Herb-specific warnings take priority; otherwise the table shows the Bensky category precaution or clearly states that no herb-specific permission or interaction is documented. Absence of a special warning never means “safe.”',
                'Cada hierba oficial tiene ahora una entrada explícita. Las advertencias específicas tienen prioridad; de lo contrario, la tabla muestra la precaución de la categoría Bensky o indica claramente que no se documenta un permiso o interacción específica. La ausencia de una advertencia especial nunca significa “segura”.'
            )); ?>
        </div>

        <section class="single-herbs-guide__section single-herbs-safety__review" aria-labelledby="single-herbs-safety-questions">
            <h4 id="single-herbs-safety-questions"><?php echo s($safetytext('Safety and dispensing recall questions', 'Preguntas de repaso de seguridad y dispensación')); ?></h4>
            <p class="single-herbs-guide__section-lead"><?php echo s($safetytext(
                'Answer before opening each panel. These questions test the same hierarchy used by the table: exact herb, preparation, category risk, pregnancy, interactions, identity and legal sourcing.',
                'Responde antes de abrir cada panel. Estas preguntas evalúan la misma jerarquía de la tabla: hierba exacta, preparación, riesgo de categoría, embarazo, interacciones, identidad y origen legal.'
            )); ?></p>
            <div class="single-herbs-guide__final-controls" aria-label="<?php echo s($safetytext('Safety answer controls', 'Controles de respuestas de seguridad')); ?>">
                <button type="button" data-safety-open-all><?php echo s($safetytext('Open all answers', 'Abrir todas las respuestas')); ?></button>
                <button type="button" data-safety-close-all><?php echo s($safetytext('Close all answers', 'Cerrar todas las respuestas')); ?></button>
            </div>
            <div class="single-herbs-guide__recall single-herbs-safety__questions" data-safety-questions>
                <?php foreach ($safetyquestions as $questionindex => $safetyquestion): ?>
                    <details class="single-herbs-guide__final-question" data-safety-question>
                        <summary>
                            <span class="single-herbs-guide__final-number"><?php echo s($safetytext('Question', 'Pregunta') . ' ' . ($questionindex + 1)); ?></span>
                            <span><?php echo s($safetyquestion[0]); ?></span>
                        </summary>
                        <div class="single-herbs-guide__final-answer"><p><?php echo s($safetyquestion[1]); ?></p></div>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="single-herbs-safety__topics" aria-label="<?php echo s($safetytext('Safety and dispensing topics', 'Temas de seguridad y dispensación')); ?>">
            <article><strong><?php echo s($safetytext('Dosage', 'Dosis')); ?></strong><span><?php echo s($safetytext('Study range plus exceptional Appendix limit when supplied.', 'Rango de estudio más límite excepcional del Appendix cuando se suministra.')); ?></span></article>
            <article><strong><?php echo s($safetytext('Preparation', 'Preparación')); ?></strong><span><?php echo s($safetytext('Special decoction, wrapping, processing, raw/calcined and powder instructions.', 'Instrucciones especiales de decocción, envoltura, procesamiento, forma cruda/calcinada y polvo.')); ?></span></article>
            <article><strong><?php echo s($safetytext('Toxicity', 'Toxicidad')); ?></strong><span><?php echo s($safetytext('Dose limits, contraindications, identity traps and toxicity warnings.', 'Límites de dosis, contraindicaciones, trampas de identidad y advertencias de toxicidad.')); ?></span></article>
            <article><strong><?php echo s($safetytext('Pregnancy', 'Embarazo')); ?></strong><span><?php echo s($safetytext('Herb-specific warnings, category precautions, or an explicit not-documented boundary.', 'Advertencias específicas, precauciones de categoría o un límite explícito de información no documentada.')); ?></span></article>
            <article><strong><?php echo s($safetytext('Interactions', 'Interacciones')); ?></strong><span><?php echo s($safetytext('Documented combination warnings, category-level risks, or an explicit review requirement.', 'Advertencias documentadas de combinación, riesgos de categoría o un requisito explícito de revisión.')); ?></span></article>
            <article><strong><?php echo s($safetytext('Obsolete / substitutes', 'Obsoletas / sustitutos')); ?></strong><span><?php echo s($safetytext('Avoidance, exact-species recognition, conservation and legal alternatives.', 'Evitación, reconocimiento de especie exacta, conservación y alternativas legales.')); ?></span></article>
        </div>

        <aside class="single-herbs-safety__obsolete">
            <div>
                <strong><?php echo s($safetytext('Obsolete substances require substitution—not preparation guidance.', 'Las sustancias obsoletas requieren sustitución, no instrucciones de preparación.')); ?></strong>
                <p><?php echo s($safetytext(
                    'High-yield examples: Guan Mu Tong → authenticated Mu Tong; Guang Fang Ji → Han Fang Ji; Chuan Shan Jia → Wang Bu Liu Xing, Zao Jiao Ci, Tu Bie Chong or Wa Leng Zi by function; protected animal products require legal non-wildlife alternatives.',
                    'Ejemplos clave: Guan Mu Tong → Mu Tong autenticado; Guang Fang Ji → Han Fang Ji; Chuan Shan Jia → Wang Bu Liu Xing, Zao Jiao Ci, Tu Bie Chong o Wa Leng Zi según función; los productos de animales protegidos requieren alternativas legales sin fauna silvestre.'
                )); ?></p>
            </div>
            <a class="single-herbs-guide__back" href="<?php echo $obsoleteurl->out(false); ?>"><?php echo s($safetytext('Open the 17-monograph obsolete-substances atlas →', 'Abrir el atlas de 17 monografías de sustancias obsoletas →')); ?></a>
        </aside>

        <div class="single-herbs-properties__controls single-herbs-safety__controls">
            <div class="single-herbs-properties__control-field">
                <label for="single-herbs-safety-search"><?php echo s($safetytext('Search herb or safety note', 'Buscar hierba o nota de seguridad')); ?></label>
                <input id="single-herbs-safety-search" type="search" placeholder="<?php echo s($safetytext('Name, category, dose, toxicity…', 'Nombre, categoría, dosis, toxicidad…')); ?>" autocomplete="off">
            </div>
            <div class="single-herbs-properties__control-field">
                <label for="single-herbs-safety-category"><?php echo s($safetytext('Filter by category', 'Filtrar por categoría')); ?></label>
                <select id="single-herbs-safety-category" data-safety-category-filter>
                    <option value=""><?php echo s($safetytext('All categories', 'Todas las categorías')); ?> (<?php echo count($safetycategoryoptions); ?>)</option>
                    <?php foreach ($safetycategoryoptions as $categorycode => $categoryoption): ?>
                        <option value="<?php echo s($categorycode); ?>"><?php echo s((string)$categoryoption[$safetylang]); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="single-herbs-properties__control-field">
                <label for="single-herbs-safety-topic"><?php echo s($safetytext('Filter by safety topic', 'Filtrar por tema de seguridad')); ?></label>
                <select id="single-herbs-safety-topic" data-safety-topic-filter>
                    <option value=""><?php echo s($safetytext('All safety records', 'Todos los registros')); ?></option>
                    <option value="preparation"><?php echo s($safetytext('Preparation documented', 'Preparación documentada')); ?> (<?php echo $structuredpreparation; ?>)</option>
                    <option value="toxicity"><?php echo s($safetytext('Herb-specific caution documented', 'Precaución específica documentada')); ?> (<?php echo $herbspecificsafety; ?>)</option>
                    <option value="pregnancy"><?php echo s($safetytext('Pregnancy caution documented', 'Precaución de embarazo documentada')); ?> (<?php echo $structuredpregnancy; ?>)</option>
                    <option value="interactions"><?php echo s($safetytext('Interaction documented', 'Interacción documentada')); ?> (<?php echo $structuredinteractions; ?>)</option>
                </select>
            </div>
            <p class="single-herbs-properties__results" aria-live="polite"><span data-safety-count><?php echo count($preparedrows); ?></span> <?php echo s($safetytext('visible herbs', 'hierbas visibles')); ?></p>
        </div>

        <div class="single-herbs-properties__table-wrap">
            <table class="single-herbs-properties__table single-herbs-safety__table">
                <thead><tr>
                    <th scope="col"><?php echo s($safetytext('Herb', 'Hierba')); ?></th>
                    <th scope="col" aria-sort="none" data-safety-sort-header><button type="button" class="single-herbs-properties__sort-button" data-safety-sort data-direction="none"><span><?php echo s($safetytext('Category', 'Categoría')); ?></span><span class="single-herbs-properties__sort-indicator" data-safety-sort-indicator aria-hidden="true">⇅</span></button></th>
                    <th scope="col"><?php echo s($safetytext('Dosage', 'Dosis')); ?></th>
                    <th scope="col"><?php echo s($safetytext('Preparation', 'Preparación')); ?></th>
                    <th scope="col"><?php echo s($safetytext('Toxicity / cautions', 'Toxicidad / precauciones')); ?></th>
                    <th scope="col"><?php echo s($safetytext('Pregnancy', 'Embarazo')); ?></th>
                    <th scope="col"><?php echo s($safetytext('Interactions', 'Interacciones')); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($preparedrows as $preparedrow): ?>
                    <?php
                    $herbparams = ['herb' => $preparedrow['name']];
                    if ($ises) {
                        $herbparams['lang'] = 'es';
                    }
                    $herburl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $herbparams);
                    $searchvalue = implode(' ', array_filter([$preparedrow['name'], $preparedrow['latin'], implode(' ', $preparedrow['aliases']), $preparedrow['identitynote'], $preparedrow['category'], $preparedrow['dose'], $preparedrow['preparation'], $preparedrow['toxicity'], $preparedrow['pregnancy'], $preparedrow['interactions']]));
                    ?>
                    <?php $isrequestedrow = strcasecmp((string)($requestedherb ?? ''), $preparedrow['name']) === 0; ?>
                    <tr id="<?php echo s($safetyrowid($preparedrow['name'])); ?>"
                        class="<?php echo $isrequestedrow ? 'single-herbs-safety__row--requested' : ''; ?>"
                        data-safety-row data-original-index="<?php echo (int)$preparedrow['position']; ?>" data-category="<?php echo s($preparedrow['category']); ?>" data-category-code="<?php echo s($preparedrow['categorycode']); ?>" data-herb="<?php echo s($preparedrow['name']); ?>" data-has-preparation="<?php echo $preparedrow['haspreparation'] ? '1' : '0'; ?>" data-has-toxicity="<?php echo $preparedrow['hastoxicity'] ? '1' : '0'; ?>" data-has-pregnancy="<?php echo $preparedrow['haspregnancy'] ? '1' : '0'; ?>" data-has-interactions="<?php echo $preparedrow['hasinteractions'] ? '1' : '0'; ?>" data-search="<?php echo s(strtolower($searchvalue)); ?>">
                        <th scope="row"><a href="<?php echo $herburl->out(false); ?>"><?php echo s($preparedrow['name']); ?></a><small><?php echo s($preparedrow['latin']); ?></small><?php if ($preparedrow['aliases']): ?><small><?php echo s('Alias: ' . implode(', ', $preparedrow['aliases'])); ?></small><?php endif; ?><?php if ($preparedrow['identitynote'] !== ''): ?><small><?php echo s($preparedrow['identitynote']); ?></small><?php endif; ?></th>
                        <td><span class="single-herbs-properties__category"><?php echo s($preparedrow['category']); ?></span></td>
                        <td><?php echo s($preparedrow['dose']); ?></td>
                        <td><?php echo s($preparedrow['preparation']); ?></td>
                        <td><?php echo s($preparedrow['toxicity']); ?></td>
                        <td><?php echo s($preparedrow['pregnancy']); ?></td>
                        <td><?php echo s($preparedrow['interactions']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="single-herbs-properties__empty-results" data-safety-empty hidden><?php echo s($safetytext('No herbs match these filters.', 'Ninguna hierba coincide con estos filtros.')); ?></p>
    </section>
    <script>
    (function() {
        const root = document.querySelector('.single-herbs-safety');
        if (!root) return;
        const input = root.querySelector('#single-herbs-safety-search');
        const categoryFilter = root.querySelector('[data-safety-category-filter]');
        const topicFilter = root.querySelector('[data-safety-topic-filter]');
        const rows = Array.from(root.querySelectorAll('[data-safety-row]'));
        const count = root.querySelector('[data-safety-count]');
        const empty = root.querySelector('[data-safety-empty]');
        const tableWrap = root.querySelector('.single-herbs-properties__table-wrap');
        const table = root.querySelector('.single-herbs-safety__table');
        const tableHead = table ? table.querySelector('thead') : null;
        const tbody = root.querySelector('tbody');
        const sortButton = root.querySelector('[data-safety-sort]');
        const sortHeader = root.querySelector('[data-safety-sort-header]');
        const sortIndicator = root.querySelector('[data-safety-sort-indicator]');
        const reviewQuestions = Array.from(root.querySelectorAll('[data-safety-question]'));
        const openAllQuestions = root.querySelector('[data-safety-open-all]');
        const closeAllQuestions = root.querySelector('[data-safety-close-all]');
        let floatingFrame = 0;
        let floatingHead = null;
        let floatingTable = null;
        let floatingSortButton = null;
        let floatingSortIndicator = null;
        let floatingCells = [];
        const originalHeadCells = tableHead ? Array.from(tableHead.querySelectorAll('th')) : [];
        if (tableWrap && table && tableHead) {
            floatingHead = document.createElement('div');
            floatingHead.className = 'single-herbs-safety__floating-head';
            floatingHead.setAttribute('aria-hidden', 'true');
            floatingTable = table.cloneNode(false);
            floatingTable.appendChild(tableHead.cloneNode(true));
            floatingHead.appendChild(floatingTable);
            document.body.appendChild(floatingHead);
            floatingCells = Array.from(floatingTable.querySelectorAll('th'));
            floatingSortButton = floatingTable.querySelector('[data-safety-sort]');
            floatingSortIndicator = floatingTable.querySelector('[data-safety-sort-indicator]');
            if (floatingSortButton) {
                floatingSortButton.addEventListener('click', function() {
                    sortButton.click();
                });
            }
        }
        const fixedTopOffset = function() {
            let offset = 0;
            document.querySelectorAll('.navbar.fixed-top, header.fixed-top, [data-region="header"].fixed-top').forEach(function(element) {
                const rect = element.getBoundingClientRect();
                const position = window.getComputedStyle(element).position;
                if ((position === 'fixed' || position === 'sticky') && rect.top <= 1 && rect.bottom > 0 && rect.height < 180) {
                    offset = Math.max(offset, rect.bottom);
                }
            });
            return Math.max(0, Math.round(offset));
        };
        const syncFloatingHead = function() {
            floatingFrame = 0;
            if (!floatingHead || !floatingTable || !tableWrap || !table || !tableHead) return;
            const wrapRect = tableWrap.getBoundingClientRect();
            const tableRect = table.getBoundingClientRect();
            const top = fixedTopOffset();
            const headHeight = tableHead.getBoundingClientRect().height;
            const left = Math.max(0, wrapRect.left);
            const right = Math.min(window.innerWidth, wrapRect.right);
            const visible = tableRect.top < top && tableRect.bottom > top + headHeight && right > left;
            floatingHead.classList.toggle('is-visible', visible);
            if (!visible) return;
            floatingHead.style.top = top + 'px';
            floatingHead.style.left = left + 'px';
            floatingHead.style.width = (right - left) + 'px';
            floatingTable.style.width = Math.max(table.scrollWidth, tableRect.width) + 'px';
            floatingTable.style.marginLeft = (tableRect.left - left) + 'px';
            originalHeadCells.forEach(function(cell, index) {
                const width = cell.getBoundingClientRect().width;
                if (!floatingCells[index]) return;
                floatingCells[index].style.width = width + 'px';
                floatingCells[index].style.minWidth = width + 'px';
                floatingCells[index].style.maxWidth = width + 'px';
            });
        };
        const scheduleFloatingHead = function() {
            if (floatingFrame) return;
            floatingFrame = window.requestAnimationFrame(syncFloatingHead);
        };
        const collator = new Intl.Collator(<?php echo json_encode($ises ? 'es' : 'en'); ?>, {sensitivity: 'base', numeric: true});
        const normalize = value => String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
        const requestedHerb = <?php echo json_encode((string)($requestedherb ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const refresh = function() {
            const query = normalize(input.value);
            const category = categoryFilter.value;
            const topic = topicFilter.value;
            let visible = 0;
            rows.forEach(function(row) {
                const matchesQuery = !query || normalize(row.dataset.search).includes(query);
                const matchesCategory = !category || row.dataset.categoryCode === category;
                const matchesTopic = !topic || row.dataset['has' + topic.charAt(0).toUpperCase() + topic.slice(1)] === '1';
                row.hidden = !(matchesQuery && matchesCategory && matchesTopic);
                if (!row.hidden) visible++;
            });
            count.textContent = String(visible);
            empty.hidden = visible !== 0;
            scheduleFloatingHead();
        };
        input.addEventListener('input', refresh);
        categoryFilter.addEventListener('change', refresh);
        topicFilter.addEventListener('change', refresh);
        sortButton.addEventListener('click', function() {
            const current = sortButton.dataset.direction || 'none';
            const next = current === 'none' ? 'ascending' : (current === 'ascending' ? 'descending' : 'none');
            const sorted = rows.slice().sort(function(a, b) {
                if (next === 'none') return Number(a.dataset.originalIndex) - Number(b.dataset.originalIndex);
                const order = collator.compare(a.dataset.category || '', b.dataset.category || '') || collator.compare(a.dataset.herb || '', b.dataset.herb || '');
                return next === 'ascending' ? order : -order;
            });
            sorted.forEach(row => tbody.appendChild(row));
            sortButton.dataset.direction = next;
            sortHeader.setAttribute('aria-sort', next);
            sortIndicator.textContent = next === 'ascending' ? '↑' : (next === 'descending' ? '↓' : '⇅');
            if (floatingSortButton) floatingSortButton.dataset.direction = next;
            if (floatingSortIndicator) floatingSortIndicator.textContent = sortIndicator.textContent;
            scheduleFloatingHead();
        });
        if (openAllQuestions) {
            openAllQuestions.addEventListener('click', function() {
                reviewQuestions.forEach(question => { question.open = true; });
            });
        }
        if (closeAllQuestions) {
            closeAllQuestions.addEventListener('click', function() {
                reviewQuestions.forEach(question => { question.open = false; });
            });
        }
        window.addEventListener('scroll', scheduleFloatingHead, {passive: true});
        window.addEventListener('resize', scheduleFloatingHead);
        if (tableWrap) tableWrap.addEventListener('scroll', scheduleFloatingHead, {passive: true});
        if ('ResizeObserver' in window && table) {
            new ResizeObserver(scheduleFloatingHead).observe(table);
        }
        if (requestedHerb) {
            input.value = requestedHerb;
            categoryFilter.value = '';
            topicFilter.value = '';
            refresh();
            const requestedRow = rows.find(row => normalize(row.dataset.herb) === normalize(requestedHerb));
            if (requestedRow) {
                requestedRow.classList.add('single-herbs-safety__row--requested');
                window.requestAnimationFrame(function() {
                    requestedRow.scrollIntoView({behavior: 'smooth', block: 'center'});
                });
            }
        }
        scheduleFloatingHead();
    })();
    </script>
<?php endif; ?>
