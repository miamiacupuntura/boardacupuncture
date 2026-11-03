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
                ? 'La categoría es la clasificación primaria de Bensky. “Dosis de estudio” procede de la ficha detallada y “Appendix 1” es el límite diario excepcional del texto suministrado. Un guion indica que la fuente disponible no aporta ese dato.'
                : 'Category is the primary Bensky classification. “Study dose” comes from the detailed profile and “Appendix 1” is the exceptional daily limit in the supplied text. A dash means that the available source does not provide that field.'); ?>
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
                    <th scope="col"><?php echo s($ises ? 'Categoría' : 'Category'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Sabor' : 'Flavor'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Temperatura / naturaleza' : 'Temperature / nature'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Meridianos' : 'Channels'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Dosis' : 'Dose'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($propertyrows as $propertyrow): ?>
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
                    <tr data-properties-row data-search="<?php echo s(strtolower($searchtext)); ?>">
                        <th scope="row"><a href="<?php echo $herburl->out(false); ?>"><?php echo s($propertyrow['name']); ?></a><small><?php echo s($propertyrow['latin']); ?></small></th>
                        <td><span class="single-herbs-properties__category"><?php echo $categorylabel ? s($categorylabel) : '—'; ?></span></td>
                        <td><?php echo $flavor ? s($flavor) : '<span aria-label="' . s($ises ? 'Sin dato' : 'No data') . '">—</span>'; ?></td>
                        <td><?php echo $temperature ? s($temperature) : '<span aria-label="' . s($ises ? 'Sin dato' : 'No data') . '">—</span>'; ?></td>
                        <td><?php echo $channels ? s($channels) : '<span aria-label="' . s($ises ? 'Sin dato' : 'No data') . '">—</span>'; ?></td>
                        <td>
                            <?php if ($dose): ?><span><?php echo s($dose); ?></span><?php endif; ?>
                            <?php if ($dose && $dosesource === 'bensky-chapter-1'): ?><small><?php echo s('Bensky · ' . ($ises ? 'capítulo 1' : 'Chapter 1') . ($benskyreference ? ' · ' . $benskyreference : '')); ?></small><?php endif; ?>
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
    })();
    </script>
<?php endif; ?>
