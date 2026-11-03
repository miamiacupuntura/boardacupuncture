<?php
// Actions and indications reference for the official Appendix A roster.

defined('MOODLE_INTERNAL') || die();

$aitext = static function(string $english, string $spanish) use ($ises): string {
    return $ises ? $spanish : $english;
};
$ailang = $ises ? 'es' : 'en';
$aipropertiespath = __DIR__ . '/single-herb-properties.json';
$aiindexpath = __DIR__ . '/single-herb-card-index.json';
$aisupplementpath = __DIR__ . '/single-herb-actions-indications-supplement.json';
$aiproperties = is_readable($aipropertiespath)
    ? json_decode((string)file_get_contents($aipropertiespath), true)
    : null;
$aiindex = is_readable($aiindexpath)
    ? json_decode((string)file_get_contents($aiindexpath), true)
    : null;
$aisupplement = is_readable($aisupplementpath)
    ? json_decode((string)file_get_contents($aisupplementpath), true)
    : null;
$aipropertyrows = is_array($aiproperties) ? ($aiproperties['herbs'] ?? []) : [];
$aiindexrows = is_array($aiindex) ? ($aiindex['herbs'] ?? []) : [];
$aisupplementrows = is_array($aisupplement) ? ($aisupplement['herbs'] ?? []) : [];

$ainormalize = static function(string $value): string {
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
    return strtolower((string)preg_replace('/[^a-z0-9]+/i', '', $value));
};

// Literal source wording transcribed from the supplied summary-table scans.
// Each inner array is one action -> indication row; rows are never collapsed.
$aiscanpairs = [
    'Ma Huang' => [
        ['Induces sweating, disperses cold', 'Wind-cold exterior pattern'],
        ['Disseminates Lung qi, calms wheezing', 'Wind-cold cough and wheezing'],
        ['Facilitates urination', 'Exterior pattern and fluid retention'],
    ],
    'Gui Zhi' => [
        ['Releases the exterior muscle layer', 'Wind-cold exterior pattern'],
        ['Transforms the qi to mobilize water', 'Thin mucus, urinary difficulty'],
        ['Warms the channels to move the blood', 'Wind-damp painful obstruction, cold amenorrhea, painful menstruation, masses'],
        ['Assists the yang', 'Chest yang unaroused'],
        ['Warms the middle, disperses cold; smooths the flow of qi and blood', 'Cold pain in the center and abdomen'],
    ],
    'Zi Su Ye' => [
        ['Disperses wind-cold', 'Exterior wind-cold'],
        ['Regulates the qi, promotes urination, calms the fetus', 'Spleen and Stomach qi stagnation, restless fetus'],
    ],
    'Jing Jie' => [
        ['Disperses wind, stops itching', 'Exterior pathogenic wind'],
        ['Relieves spasms', 'Postpartum muscular tetany'],
        ['Stops bleeding (charred)', 'Abnormal bleeding'],
    ],
    'Fang Feng' => [
        ['Disperses wind, overcomes dampness', 'Externally-contracted pathogenic wind; wind-damp painful obstruction; abdominal pain and diarrhea from dampness encumbering the Spleen yang'],
    ],
    'Qiang Huo' => [
        ['Releases the exterior', 'Externally-contracted wind-cold'],
        ['Expels wind-dampness', 'Wind-cold-damp painful obstruction, pain in the nape and upper limbs'],
    ],
    'Gao Ben' => [
        ['Disperses greater yang channel wind-cold', 'Vertex headache'],
        ['Eliminates internal cold-dampness', 'Abdominal pain and diarrhea'],
    ],
    'Bai Zhi' => [
        ['Disperses cold, overcomes dampness', 'Head and body pain from exposure to cold; damp sores, leukorrhea'],
        ['Expels pus, stops pain', 'Pustular swellings, sores, furuncles'],
    ],
    'Xi Xin' => [
        ['Warms the Lungs, transforms thin mucus', 'Cold thin mucus in the Lungs'],
        ['Disperses cold, stops pain', 'External pathogen plus yang deficiency; headache, toothache, wind-damp painful obstruction, pain in the chest'],
    ],
    'Sheng Jiang' => [
        ['Induces sweating', 'Exterior wind-cold'],
        ['Warms the center', 'Nausea and vomiting from Stomach cold'],
        ['Stops coughs', 'Profuse phlegm, coughing, and wheezing from Lung cold'],
    ],
    'Cong Bai' => [
        ['Releases the exterior', 'Exterior wind-cold'],
        ['Unblocks the yang qi', 'Excessive yin forcing weakened yang upward: diarrhea, lassitude, cold extremities, flushed face, and a faint pulse'],
    ],
    'Xiang Ru' => [
        ['Assists the yang to overcome exterior cold in summer', 'Summertime exterior cold oppressing the yang qi'],
        ['Transforms dampness, promotes urination', 'Fluid retention, urinary difficulty'],
    ],
    'Cang Er Zi' => [
        ['Disperses wind-dampness, opens the nasal passages', 'Nasal or sinus problems with thick, viscous discharge'],
        ['Disperses wind, dispels dampness', 'Painful obstruction, itchy skin'],
    ],
    'Xin Yi' => [
        ['Disperses wind, unblocks the nose', 'Blocked nose, sinusitis'],
    ],
    'Xi He Liu' => [
        ['Disperses rashes, vents heat toxin', 'Unexpressed rashes such as measles'],
    ],
    'Fu Ping' => [
        ['Disperses wind-heat', 'Exterior wind-heat, wind rash, or damp-heat rash and itching'],
        ['Promotes urination, unblocks the fluid pathways', 'Fluid retention and urinary difficulty due to dampness or pathogenic water'],
    ],
    'Mu Zei' => [
        ['Clears superficial visual obstruction', 'Wind-heat or Liver fire affecting the eyes'],
        ['Releases the muscle layer', 'Exterior wind-heat; fire constraint in the Lungs, Liver, or Gallbladder'],
    ],
    'Ge Gen' => [
        ['Releases muscle layer heat', 'Exterior pathogen in greater yang and yang brightness stages, stiff neck due to exterior pathogen'],
        ['Vents rashes', 'Rashes and sores that are slow to express'],
        ['Restores the ascent of Stomach fluids, raises clear Stomach qi', 'Diarrhea or thirst due to inability of fluids to ascend'],
    ],
    'Chai Hu' => [
        ['Resolves lesser yang stage disorders', 'Alternating fever and chills, fullness of chest and hypochondria'],
        ['Raises the yang qi', 'Prolapse, abnormal sinking of Gall Bladder or Stomach qi'],
        ['Releases the muscle layer', 'Exterior wind-heat, fire constraint in Liver or Gallbladder'],
        ['Clears heat from consumption', 'Deficiency consumption with steaming bones and tidal fever'],
        ['Relieves qi constraint', 'Liver qi stagnation'],
        ['Regulates menstruation', 'Heat entering the blood chamber, qi constraint, puerperal fever'],
        ['Guides to the eyes and ears', 'Guiding herb for lesser yang and terminal yin channels'],
    ],
    'Sheng Ma' => [
        ['Raises the yang qi', 'Abnormal sinking of Stomach or Spleen qi, dragging sensation in lower body, chronic diarrhea'],
        ['Disperses wind pathogen and vents heat', 'Pathogenic wind in skin, wind-heat with body aches and unexpressed rash'],
        ['Headache or toothache', 'Yang brightness headache, toothache, mouth ulcers'],
        ['Resolves toxicity, guides to yang brightness channel', 'Sores, spots, and blotches'],
    ],
    'Shi Gao' => [
        ['Clears heat and drains fire at the qi level', 'High fever without chills, intense thirst, irritability, profuse sweating, big and flooding pulse'],
        ['Clears excess heat from Lungs', 'Cough and wheezing with thick, viscous sputum'],
        ['Clears blazing Stomach fire', 'Headache, toothache, or swollen painful gums due to Stomach fire'],
        ['Vents the exterior and releases muscle layer heat', 'Heat in the muscle layer'],
        ['Preserves and generates flesh', 'Eczema, burns, ulcerated sores (topically)'],
    ],
    'Ban Xia' => [
        ['Dries dampness, expels phlegm', 'Cough; stifling sensation in the chest from stubborn phlegm'],
        ['Disperses wind-phlegm in the channels and collaterals, stops spasms', 'Wind-stroke with phlegm welling up; wind-phlegm leading to numbness of the limbs, vertigo, fits, convulsions, and facial paralysis'],
        ['Reduces swelling, alleviates pain', 'Toxic boils and swellings and phlegm nodes; hematoma from external trauma'],
        ['Transforms phlegm-heat and extinguishes wind to settle convulsions and spasms (bile-prepared)', 'Phlegm-heat; convulsions, and spasms due to wind'],
    ],
    'Zhi Bai Fu Zi' => [
        ['Dries dampness, transforms phlegm, expels wind, stops spasms', 'Wind-phlegm in the face and head, with facial paralysis; hemiplegia; scrofula; migraine'],
        ['Expels wind-cold-dampness, alleviates pain', 'Dizziness, severe lateral headaches, any pain in the head from wind-cold or wind-dampness'],
        ['Resolves toxicity, dissipates nodules', 'Scrofula, phlegm nodules; snakebite (topically)'],
    ],
    'Bai Jie Zi' => [
        ['Warms the Lungs, regulates the qi, expels phlegm', 'Phlegm in the subcostal region, and between the skin and the membranes'],
        ['Transforms phlegm-cold, dissipates nodules, reduces swelling, alleviates pain', 'Phlegm-cold causing cough and wheezing, or pain in the joints; yin-type ulcers'],
    ],
    'Zao Jiao' => [
        ['Dispels phlegm', 'Phlegm obstruction; phlegm nodules; cough or wheezing with profuse, difficult-to-expectorate phlegm'],
        ['Opens the orifices, revives the spirit', 'Sudden loss of consciousness with seizures or facial paralysis from excessive phlegm'],
        ['Dissipates clumps, reduces swelling', 'Initial stage of abscess or boils; difficult discharge of pus'],
        ['Unblocks the bowels, expels roundworms', 'Constipation or intestinal obstruction due to roundworms (suppository)'],
    ],
    'Jie Geng' => [
        ['Opens up and disseminates the Lung qi, expels phlegm', 'Externally-contracted pathogens; internal accumulation of phlegm obstructing the Lung qi'],
        ['Promotes the discharge of pus', 'Lung abscess with coughing of pus'],
        ['Benefits the throat', 'Sore throat, loss of voice'],
        ['Raises the qi and the actions of other herbs to the upper body', 'Abnormal descent of Spleen qi'],
    ],
    'Xuan Fu Hua' => [
        ['Redirects the qi downward, softens and expels hardened phlegm', 'Cough and wheezing with profuse phlegm; sticky, difficult-to-expectorate phlegm'],
        ['Redirects the qi downward, alleviates nausea, eliminates pathogenic water', 'Epigastric fullness, nausea, and burping with accumulation of pathogenic fluids'],
    ],
    'Bai Qian' => [
        ['Redirects the Lung qi downward, expels phlegm', 'Profuse phlegm obstructing the Lungs leading to cough, sensation of fullness in the chest, and wheezing; phlegm in the throat'],
    ],
];

$aiscanpairsbynormalized = [];
foreach ($aiscanpairs as $ainame => $aipairs) {
    $aiscanpairsbynormalized[$ainormalize($ainame)] = $aipairs;
}
$aipropertiesbyname = [];
foreach ($aipropertyrows as $aipropertyrow) {
    $aipropertiesbyname[$ainormalize((string)($aipropertyrow['name'] ?? ''))] = $aipropertyrow;
}
$aisupplementbyname = [];
foreach ($aisupplementrows as $aisupplementname => $aisupplementrow) {
    $aisupplementbyname[$ainormalize((string)$aisupplementname)] = $aisupplementrow;
}

$aicategoryoptions = [];
$aipreparedrows = [];
$aimissingherbs = 0;
$aipaircount = 0;
foreach ($aiindexrows as $aiposition => $aiindexrow) {
    $ainame = (string)($aiindexrow['name'] ?? '');
    $aikey = $ainormalize($ainame);
    $aicategory = is_array($aiindexrow['benskyCategory'] ?? null) ? $aiindexrow['benskyCategory'] : [];
    if (!empty($aicategory['code'])) {
        $aicategoryoptions[(string)$aicategory['code']] = $aicategory;
    }
    $aipropertyrow = $aipropertiesbyname[$aikey] ?? [];
    $aipairs = [];
    if (!empty($aiscanpairsbynormalized[$aikey])) {
        foreach ($aiscanpairsbynormalized[$aikey] as $aiscanpair) {
            $aipairs[] = ['action' => (string)$aiscanpair[0], 'indication' => (string)$aiscanpair[1]];
        }
    } else {
        $aiaction = trim((string)($aipropertyrow['actionSummary'][$ailang] ?? ''));
        $aiindication = trim((string)($aipropertyrow['indicationSummary'][$ailang] ?? ''));
        if ($aiaction !== '' && $aiindication !== '') {
            $aipairs[] = ['action' => $aiaction, 'indication' => $aiindication];
        } else if (!empty($aisupplementbyname[$aikey][$ailang])) {
            foreach ($aisupplementbyname[$aikey][$ailang] as $aisupplementpair) {
                if (!is_array($aisupplementpair) || count($aisupplementpair) < 2) {
                    continue;
                }
                $aipairs[] = [
                    'action' => trim((string)$aisupplementpair[0]),
                    'indication' => trim((string)$aisupplementpair[1]),
                ];
            }
            if (!$aipairs) {
                $aipairs[] = ['action' => '', 'indication' => ''];
                $aimissingherbs++;
            }
        } else {
            $aipairs[] = ['action' => '', 'indication' => ''];
            $aimissingherbs++;
        }
    }
    $aipaircount += count($aipairs);
    $aipreparedrows[] = [
        'position' => $aiposition,
        'name' => $ainame,
        'latin' => (string)($aiindexrow['latin'] ?? ''),
        'categorycode' => (string)($aicategory['code'] ?? ''),
        'category' => (string)($aicategory[$ailang] ?? $aicategory['en'] ?? ''),
        'pairs' => $aipairs,
    ];
}
uasort($aicategoryoptions, static function(array $left, array $right) use ($ailang): int {
    return strcasecmp((string)($left[$ailang] ?? $left['en'] ?? ''), (string)($right[$ailang] ?? $right['en'] ?? ''));
});
?>

<?php if (!$aipreparedrows): ?>
    <div class="single-herbs-guide__empty"><?php echo s($aitext('Actions and indications data is temporarily unavailable.', 'Los datos de acciones e indicaciones no están disponibles temporalmente.')); ?></div>
<?php else: ?>
    <section class="single-herbs-properties single-herbs-actions-indications" aria-labelledby="single-herbs-ai-heading">
        <div class="single-herbs-properties__summary">
            <div>
                <h4 id="single-herbs-ai-heading"><?php echo s($aitext('Actions and indications master table', 'Tabla maestra de acciones e indicaciones')); ?></h4>
                <p><?php echo s($aitext(
                    'Restricted to the 304 herbs in the official guideline and completed with the Bensky actions-and-indications summary. Each action remains paired with its indication.',
                    'Limitada a las 304 hierbas del guideline oficial y completada con el resumen de acciones e indicaciones de Bensky. Cada acción permanece unida a su indicación.'
                )); ?></p>
            </div>
            <div class="single-herbs-properties__stats" aria-label="<?php echo s($aitext('Table summary', 'Resumen de la tabla')); ?>">
                <span><strong><?php echo count($aipreparedrows); ?></strong><?php echo s($aitext(' official', ' oficiales')); ?></span>
                <span><strong><?php echo $aipaircount; ?></strong><?php echo s($aitext(' action-indication pairs', ' pares acción-indicación')); ?></span>
                <span><strong><?php echo $aimissingherbs; ?></strong><?php echo s($aitext(' missing', ' faltantes')); ?></span>
                <span><strong><?php echo count($aicategoryoptions); ?></strong><?php echo s($aitext(' categories', ' categorías')); ?></span>
            </div>
        </div>

        <div class="single-herbs-properties__controls single-herbs-ai__controls">
            <div class="single-herbs-properties__control-field">
                <label for="single-herbs-ai-search"><?php echo s($aitext('Search herb, action or indication', 'Buscar hierba, acción o indicación')); ?></label>
                <input id="single-herbs-ai-search" type="search" placeholder="<?php echo s($aitext('Name, category, action, indication…', 'Nombre, categoría, acción, indicación…')); ?>" autocomplete="off">
            </div>
            <div class="single-herbs-properties__control-field">
                <label for="single-herbs-ai-category"><?php echo s($aitext('Category', 'Categoría')); ?></label>
                <select id="single-herbs-ai-category" data-ai-category-filter>
                    <option value=""><?php echo s($aitext('All categories', 'Todas las categorías')); ?></option>
                    <?php foreach ($aicategoryoptions as $aicode => $aicategory): ?>
                        <option value="<?php echo s($aicode); ?>"><?php echo s((string)($aicategory[$ailang] ?? $aicategory['en'] ?? $aicode)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="single-herbs-properties__control-field">
                <label for="single-herbs-ai-sort"><?php echo s($aitext('Sort', 'Ordenar')); ?></label>
                <select id="single-herbs-ai-sort" data-ai-sort>
                    <option value="category"><?php echo s($aitext('Category, then herb', 'Categoría y luego hierba')); ?></option>
                    <option value="herb-asc"><?php echo s($aitext('Herb A–Z', 'Hierba A–Z')); ?></option>
                    <option value="herb-desc"><?php echo s($aitext('Herb Z–A', 'Hierba Z–A')); ?></option>
                    <option value="official"><?php echo s($aitext('Official roster order', 'Orden del roster oficial')); ?></option>
                </select>
            </div>
            <button class="single-herbs-guide__back" type="button" data-ai-reset><?php echo s($aitext('Reset', 'Restablecer')); ?></button>
        </div>

        <p class="single-herbs-properties__results" aria-live="polite" data-ai-results></p>
        <div class="single-herbs-properties__table-wrap">
            <table class="single-herbs-properties__table single-herbs-ai__table" data-ai-body>
                <thead><tr>
                    <th scope="col"><?php echo s($aitext('Herb', 'Hierba')); ?></th>
                    <th scope="col"><?php echo s($aitext('Category', 'Categoría')); ?></th>
                    <th scope="col"><?php echo s($aitext('Action', 'Acción')); ?></th>
                    <th scope="col"><?php echo s($aitext('Indication', 'Indicación')); ?></th>
                </tr></thead>
                <?php foreach ($aipreparedrows as $airow):
                    $aisearchparts = [$airow['name'], $airow['latin'], $airow['category']];
                    foreach ($airow['pairs'] as $aipair) {
                        $aisearchparts[] = $aipair['action'];
                        $aisearchparts[] = $aipair['indication'];
                    }
                    $aisearchvalue = strtolower(implode(' ', $aisearchparts));
                ?>
                    <tbody class="single-herbs-ai__group" data-ai-row data-position="<?php echo (int)$airow['position']; ?>" data-name="<?php echo s(strtolower($airow['name'])); ?>" data-category="<?php echo s($airow['categorycode']); ?>" data-category-label="<?php echo s(strtolower($airow['category'])); ?>" data-search="<?php echo s($aisearchvalue); ?>">
                    <?php foreach ($airow['pairs'] as $aipairindex => $aipair): ?>
                        <tr>
                            <?php if ($aipairindex === 0): ?>
                                <th scope="rowgroup" rowspan="<?php echo count($airow['pairs']); ?>"><strong><?php echo s($airow['name']); ?></strong><small><?php echo s($airow['latin']); ?></small></th>
                                <td rowspan="<?php echo count($airow['pairs']); ?>"><span class="single-herbs-properties__category"><?php echo s($airow['category'] ?: '—'); ?></span></td>
                            <?php endif; ?>
                            <td><?php echo s($aipair['action'] !== '' ? $aipair['action'] : '—'); ?></td>
                            <td><?php echo s($aipair['indication'] !== '' ? $aipair['indication'] : '—'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <p class="single-herbs-properties__empty-results" hidden data-ai-empty><?php echo s($aitext('No herbs match these filters.', 'Ninguna hierba coincide con estos filtros.')); ?></p>
    </section>

    <script>
    (function() {
        const root = document.querySelector('.single-herbs-actions-indications');
        if (!root) return;
        const search = root.querySelector('#single-herbs-ai-search');
        const category = root.querySelector('[data-ai-category-filter]');
        const sort = root.querySelector('[data-ai-sort]');
        const reset = root.querySelector('[data-ai-reset]');
        const body = root.querySelector('[data-ai-body]');
        const results = root.querySelector('[data-ai-results]');
        const empty = root.querySelector('[data-ai-empty]');
        const groups = Array.from(root.querySelectorAll('[data-ai-row]'));
        const normalize = value => (value || '').toLocaleLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const total = groups.length;
        function update() {
            const query = normalize(search.value.trim());
            const selectedCategory = category.value;
            let visible = 0;
            groups.forEach(group => {
                const matchesSearch = !query || normalize(group.dataset.search).includes(query);
                const matchesCategory = !selectedCategory || group.dataset.category === selectedCategory;
                group.hidden = !(matchesSearch && matchesCategory);
                if (!group.hidden) visible++;
            });
            const sorted = groups.slice().sort((left, right) => {
                if (sort.value === 'official') return Number(left.dataset.position) - Number(right.dataset.position);
                if (sort.value === 'herb-desc') return right.dataset.name.localeCompare(left.dataset.name);
                if (sort.value === 'herb-asc') return left.dataset.name.localeCompare(right.dataset.name);
                return left.dataset.categoryLabel.localeCompare(right.dataset.categoryLabel) || left.dataset.name.localeCompare(right.dataset.name);
            });
            sorted.forEach(group => body.appendChild(group));
            results.textContent = '<?php echo s($aitext('Showing', 'Mostrando')); ?> ' + visible + ' <?php echo s($aitext('of', 'de')); ?> ' + total + ' <?php echo s($aitext('official herbs', 'hierbas oficiales')); ?>';
            empty.hidden = visible !== 0;
            root.querySelector('.single-herbs-properties__table-wrap').hidden = visible === 0;
        }
        [search, category, sort].forEach(control => control.addEventListener(control === search ? 'input' : 'change', update));
        reset.addEventListener('click', function() {
            search.value = '';
            category.value = '';
            sort.value = 'category';
            update();
            search.focus();
        });
        update();
    }());
    </script>
<?php endif; ?>
