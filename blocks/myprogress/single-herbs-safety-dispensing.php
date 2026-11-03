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
$safetycategoryoptions = [];
foreach (is_array($safetyindex) ? ($safetyindex['herbs'] ?? []) : [] as $safetyindexrow) {
    $safetycategory = $safetyindexrow['benskyCategory'] ?? null;
    if (!is_array($safetycategory) || empty($safetyindexrow['name']) || empty($safetycategory['code'])) {
        continue;
    }
    $safetycategorybyname[(string)$safetyindexrow['name']] = $safetycategory;
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
                    $searchvalue = implode(' ', array_filter([$preparedrow['name'], $preparedrow['latin'], $preparedrow['category'], $preparedrow['dose'], $preparedrow['preparation'], $preparedrow['toxicity'], $preparedrow['pregnancy'], $preparedrow['interactions']]));
                    ?>
                    <tr data-safety-row data-original-index="<?php echo (int)$preparedrow['position']; ?>" data-category="<?php echo s($preparedrow['category']); ?>" data-category-code="<?php echo s($preparedrow['categorycode']); ?>" data-herb="<?php echo s($preparedrow['name']); ?>" data-has-preparation="<?php echo $preparedrow['haspreparation'] ? '1' : '0'; ?>" data-has-toxicity="<?php echo $preparedrow['hastoxicity'] ? '1' : '0'; ?>" data-has-pregnancy="<?php echo $preparedrow['haspregnancy'] ? '1' : '0'; ?>" data-has-interactions="<?php echo $preparedrow['hasinteractions'] ? '1' : '0'; ?>" data-search="<?php echo s(strtolower($searchvalue)); ?>">
                        <th scope="row"><a href="<?php echo $herburl->out(false); ?>"><?php echo s($preparedrow['name']); ?></a><small><?php echo s($preparedrow['latin']); ?></small></th>
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
        const tbody = root.querySelector('tbody');
        const sortButton = root.querySelector('[data-safety-sort]');
        const sortHeader = root.querySelector('[data-safety-sort-header]');
        const sortIndicator = root.querySelector('[data-safety-sort-indicator]');
        const collator = new Intl.Collator(<?php echo json_encode($ises ? 'es' : 'en'); ?>, {sensitivity: 'base', numeric: true});
        const normalize = value => String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
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
        });
    })();
    </script>
<?php endif; ?>
