<?php
// Searchable reference table for the official Appendix A herb roster.
defined('MOODLE_INTERNAL') || die();

$propertiespath = __DIR__ . '/single-herb-properties.json';
$propertiesdata = is_readable($propertiespath)
    ? json_decode((string)file_get_contents($propertiespath), true)
    : null;
$propertyrows = is_array($propertiesdata) ? ($propertiesdata['herbs'] ?? []) : [];
$categorypath = __DIR__ . '/single-herb-card-index.json';
$categorydata = is_readable($categorypath)
    ? json_decode((string)file_get_contents($categorypath), true)
    : null;
$categorybyname = [];
$categorycodes = [];
foreach (is_array($categorydata) ? ($categorydata['herbs'] ?? []) : [] as $categoryrow) {
    $category = $categoryrow['benskyCategory'] ?? null;
    if (!is_array($category) || empty($categoryrow['name'])) {
        continue;
    }
    $categorybyname[(string)$categoryrow['name']] = $category;
    if (!empty($category['code'])) {
        $categorycodes[(string)$category['code']] = true;
    }
}
$propertylang = $ises ? 'es' : 'en';
?>
<?php if (!$propertyrows): ?>
    <div class="single-herbs-guide__empty"><?php echo s($ises
        ? 'La tabla de propiedades no está disponible temporalmente.'
        : 'The herb properties table is temporarily unavailable.'); ?></div>
<?php else: ?>
    <section class="single-herbs-properties" aria-labelledby="single-herbs-properties-heading">
        <div class="single-herbs-properties__summary">
            <div>
                <h4 id="single-herbs-properties-heading"><?php echo s($ises ? 'Tabla maestra de propiedades' : 'Master herb properties table'); ?></h4>
                <p><?php echo s($ises
                    ? 'Incluye las 304 hierbas oficiales del Appendix A vigente, verificadas contra el guideline y clasificadas por su categoría primaria de Bensky.'
                    : 'Includes all 304 herbs in the current official Appendix A, verified against the guideline and classified by primary Bensky category.'); ?></p>
            </div>
            <div class="single-herbs-properties__stats" aria-label="<?php echo s($ises ? 'Resumen de la tabla' : 'Table summary'); ?>">
                <span><strong><?php echo (int)$propertiesdata['total']; ?></strong><?php echo s($ises ? ' oficiales' : ' official'); ?></span>
                <span><strong><?php echo count($categorycodes); ?></strong><?php echo s($ises ? ' categorías Bensky' : ' Bensky categories'); ?></span>
                <span><strong><?php echo count($categorybyname); ?></strong><?php echo s($ises ? ' categorizadas' : ' categorized'); ?></span>
            </div>
        </div>

        <div class="single-herbs-properties__source-note">
            <strong><?php echo s($ises ? 'Cómo leer la dosis:' : 'How to read dosage:'); ?></strong>
            <?php echo s($ises
                ? 'La categoría es la clasificación primaria de Bensky. Las propiedades, meridianos y dosis de las 304 hierbas están completos; Bensky completa los campos que no aparecían en las tablas previas. “Appendix 1” muestra el límite diario excepcional del texto suministrado.'
                : 'Category is the primary Bensky classification. Properties, channels and dosage are complete for all 304 herbs; Bensky supplies fields absent from the earlier tables. “Appendix 1” shows the exceptional daily limit in the supplied text.'); ?>
        </div>

        <div class="single-herbs-properties__controls">
            <label for="single-herbs-properties-search"><?php echo s($ises ? 'Buscar hierba o propiedad' : 'Search herb or property'); ?></label>
            <input id="single-herbs-properties-search" type="search" placeholder="<?php echo s($ises ? 'Nombre, categoría, sabor, temperatura, meridiano…' : 'Name, category, flavor, temperature, channel…'); ?>" autocomplete="off">
            <p class="single-herbs-properties__results" aria-live="polite"><span data-properties-count><?php echo count($propertyrows); ?></span> <?php echo s($ises ? 'hierbas visibles' : 'visible herbs'); ?></p>
        </div>

        <div class="single-herbs-properties__table-wrap">
            <table class="single-herbs-properties__table">
                <thead><tr>
                    <th scope="col"><?php echo s($ises ? 'Hierba' : 'Herb'); ?></th>
                    <th scope="col" aria-sort="none" data-properties-sort-header>
                        <button type="button" class="single-herbs-properties__sort-button" data-properties-sort data-direction="none">
                            <span><?php echo s($ises ? 'Categoría' : 'Category'); ?></span>
                            <span class="single-herbs-properties__sort-indicator" data-properties-sort-indicator aria-hidden="true">⇅</span>
                        </button>
                    </th>
                    <th scope="col"><?php echo s($ises ? 'Sabor' : 'Flavor'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Temperatura / naturaleza' : 'Temperature / nature'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Meridianos' : 'Channels'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Dosis' : 'Dose'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($propertyrows as $propertyindex => $propertyrow): ?>
                    <?php
                    $herbparams = ['herb' => (string)$propertyrow['name']];
                    if ($ises) {
                        $herbparams['lang'] = 'es';
                    }
                    $herburl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $herbparams);
                    $flavor = $propertyrow['flavor'][$propertylang] ?? null;
                    $temperature = $propertyrow['temperature'][$propertylang] ?? null;
                    $channels = $propertyrow['channels'][$propertylang] ?? null;
                    $dose = $propertyrow['dose'] ?? null;
                    $dosesource = $propertyrow['doseSource'] ?? null;
                    $benskyreference = $propertyrow['benskyReference'] ?? null;
                    $appendixdose = $propertyrow['appendixDose'] ?? null;
                    $category = $categorybyname[(string)$propertyrow['name']] ?? [];
                    $categorylabel = $category[$propertylang] ?? null;
                    $categorycode = $category['code'] ?? null;
                    $searchtext = implode(' ', array_filter([
                        $propertyrow['name'], $propertyrow['latin'], $categorylabel, $categorycode,
                        $flavor, $temperature, $channels, $dose, $appendixdose,
                    ]));
                    ?>
                    <tr data-properties-row
                        data-original-index="<?php echo (int)$propertyindex; ?>"
                        data-category="<?php echo s((string)$categorylabel); ?>"
                        data-herb="<?php echo s((string)$propertyrow['name']); ?>"
                        data-search="<?php echo s(strtolower($searchtext)); ?>">
                        <th scope="row"><a href="<?php echo $herburl->out(false); ?>"><?php echo s($propertyrow['name']); ?></a><small><?php echo s($propertyrow['latin']); ?></small></th>
                        <td><span class="single-herbs-properties__category"><?php echo $categorylabel ? s($categorylabel) : '—'; ?></span></td>
                        <td><?php echo $flavor ? s($flavor) : '<span aria-label="' . s($ises ? 'Sin dato' : 'No data') . '">—</span>'; ?></td>
                        <td><?php echo $temperature ? s($temperature) : '<span aria-label="' . s($ises ? 'Sin dato' : 'No data') . '">—</span>'; ?></td>
                        <td><?php echo $channels ? s($channels) : '<span aria-label="' . s($ises ? 'Sin dato' : 'No data') . '">—</span>'; ?></td>
                        <td>
                            <?php if ($dose): ?><span><?php echo s($dose); ?></span><?php endif; ?>
                            <?php if ($dose && $dosesource === 'bensky-chapter-1'): ?><small><?php echo s('Bensky · ' . ($ises ? 'capítulo 1' : 'Chapter 1') . ($benskyreference ? ' · ' . $benskyreference : '')); ?></small><?php endif; ?>
                            <?php if ($dose && $dosesource === 'bensky-supplied-monographs'): ?><small><?php echo s($ises ? 'Bensky · monografía suministrada' : 'Bensky · supplied monograph'); ?></small><?php endif; ?>
                            <?php if ($appendixdose): ?><small><?php echo s('Appendix 1 · ' . ($ises ? str_replace(['Less than', 'Up to', '(source lists both)'], ['Menos de', 'Hasta', '(la fuente muestra ambos)'], $appendixdose) : $appendixdose)); ?></small><?php endif; ?>
                            <?php if (!$dose && !$appendixdose): ?><span aria-label="<?php echo s($ises ? 'Sin dato' : 'No data'); ?>">—</span><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="single-herbs-properties__empty-results" data-properties-empty hidden><?php echo s($ises ? 'No hay hierbas que coincidan con esa búsqueda.' : 'No herbs match that search.'); ?></p>
    </section>
    <script>
    (function() {
        const root = document.querySelector('.single-herbs-properties');
        if (!root) return;
        const input = root.querySelector('#single-herbs-properties-search');
        const rows = Array.from(root.querySelectorAll('[data-properties-row]'));
        const count = root.querySelector('[data-properties-count]');
        const empty = root.querySelector('[data-properties-empty]');
        const tbody = root.querySelector('tbody');
        const sortButton = root.querySelector('[data-properties-sort]');
        const sortHeader = root.querySelector('[data-properties-sort-header]');
        const sortIndicator = root.querySelector('[data-properties-sort-indicator]');
        const sortLabels = <?php echo json_encode([
            'none' => $ises ? 'Ordenar por categoría de A a Z' : 'Sort category A to Z',
            'ascending' => $ises ? 'Orden ascendente por categoría. Cambiar a descendente' : 'Category sorted ascending. Change to descending',
            'descending' => $ises ? 'Orden descendente por categoría. Volver al orden original' : 'Category sorted descending. Return to original order',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        const collator = new Intl.Collator(<?php echo json_encode($ises ? 'es' : 'en'); ?>, {sensitivity: 'base', numeric: true});
        const normalize = value => String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
        const refresh = function() {
            const query = normalize(input.value);
            let visible = 0;
            rows.forEach(function(row) {
                const matchesQuery = !query || normalize(row.dataset.search).includes(query);
                row.hidden = !matchesQuery;
                if (!row.hidden) visible++;
            });
            count.textContent = String(visible);
            empty.hidden = visible !== 0;
        };
        input.addEventListener('input', refresh);
        sortButton.addEventListener('click', function() {
            const current = sortButton.dataset.direction || 'none';
            const next = current === 'none' ? 'ascending' : (current === 'ascending' ? 'descending' : 'none');
            const sorted = rows.slice().sort(function(a, b) {
                if (next === 'none') {
                    return Number(a.dataset.originalIndex) - Number(b.dataset.originalIndex);
                }
                const categoryOrder = collator.compare(a.dataset.category || '', b.dataset.category || '');
                const herbOrder = collator.compare(a.dataset.herb || '', b.dataset.herb || '');
                const order = categoryOrder || herbOrder;
                return next === 'ascending' ? order : -order;
            });
            sorted.forEach(row => tbody.appendChild(row));
            sortButton.dataset.direction = next;
            sortButton.setAttribute('aria-label', sortLabels[next]);
            sortHeader.setAttribute('aria-sort', next);
            sortIndicator.textContent = next === 'ascending' ? '↑' : (next === 'descending' ? '↓' : '⇅');
        });
    })();
    </script>
<?php endif; ?>
