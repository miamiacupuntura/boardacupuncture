<?php
// Searchable reference table for the official Appendix A herb roster.
defined('MOODLE_INTERNAL') || die();

$propertiespath = __DIR__ . '/single-herb-properties.json';
$propertiesdata = is_readable($propertiespath)
    ? json_decode((string)file_get_contents($propertiespath), true)
    : null;
$propertyrows = is_array($propertiesdata) ? ($propertiesdata['herbs'] ?? []) : [];
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
                    ? 'Incluye las 304 hierbas oficiales del Appendix A. Los campos se muestran solo cuando existe una ficha verificada; las demás filas quedan identificadas como pendientes.'
                    : 'Includes all 304 official Appendix A herbs. Fields appear only when a verified profile exists; every remaining row is explicitly identified as pending.'); ?></p>
            </div>
            <div class="single-herbs-properties__stats" aria-label="<?php echo s($ises ? 'Cobertura de la tabla' : 'Table coverage'); ?>">
                <span><strong><?php echo (int)$propertiesdata['total']; ?></strong><?php echo s($ises ? ' oficiales' : ' official'); ?></span>
                <span><strong><?php echo (int)$propertiesdata['verified']; ?></strong><?php echo s($ises ? ' fichas detalladas' : ' detailed profiles'); ?></span>
                <span><strong><?php echo (int)($propertiesdata['propertiesOnly'] ?? 0); ?></strong><?php echo s($ises ? ' con propiedades del Chart 6' : ' with Chart 6 properties'); ?></span>
                <span><strong><?php echo (int)$propertiesdata['doseOnly']; ?></strong><?php echo s($ises ? ' solo con dosis del apéndice' : ' appendix-dose only'); ?></span>
            </div>
        </div>

        <div class="single-herbs-properties__source-note">
            <strong><?php echo s($ises ? 'Cómo leer la dosis:' : 'How to read dosage:'); ?></strong>
            <?php echo s($ises
                ? '“Dosis de estudio” procede de la ficha detallada. “Appendix 1” es el límite diario excepcional del texto suministrado. “Chart 6” completa propiedades, pero no aporta dosis para todas sus hierbas.'
                : '“Study dose” comes from the detailed profile. “Appendix 1” is the exceptional daily limit in the supplied text. “Chart 6” completes properties but does not provide dosage for every herb.'); ?>
        </div>

        <div class="single-herbs-properties__controls">
            <label for="single-herbs-properties-search"><?php echo s($ises ? 'Buscar hierba o propiedad' : 'Search herb or property'); ?></label>
            <input id="single-herbs-properties-search" type="search" placeholder="<?php echo s($ises ? 'Nombre, sabor, temperatura, meridiano…' : 'Name, flavor, temperature, channel…'); ?>" autocomplete="off">
            <div class="single-herbs-properties__filters" role="group" aria-label="<?php echo s($ises ? 'Filtrar cobertura' : 'Filter coverage'); ?>">
                <button type="button" class="is-active" data-properties-filter="all"><?php echo s($ises ? 'Todas' : 'All'); ?></button>
                <button type="button" data-properties-filter="verified"><?php echo s($ises ? 'Verificadas' : 'Verified'); ?></button>
                <button type="button" data-properties-filter="properties-only"><?php echo s($ises ? 'Propiedades Chart 6' : 'Chart 6 properties'); ?></button>
                <button type="button" data-properties-filter="dose-only"><?php echo s($ises ? 'Solo dosis' : 'Dose only'); ?></button>
                <button type="button" data-properties-filter="pending"><?php echo s($ises ? 'Pendientes' : 'Pending'); ?></button>
            </div>
            <p class="single-herbs-properties__results" aria-live="polite"><span data-properties-count><?php echo count($propertyrows); ?></span> <?php echo s($ises ? 'hierbas visibles' : 'visible herbs'); ?></p>
        </div>

        <div class="single-herbs-properties__table-wrap">
            <table class="single-herbs-properties__table">
                <thead><tr>
                    <th scope="col"><?php echo s($ises ? 'Hierba' : 'Herb'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Sabor' : 'Flavor'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Temperatura / naturaleza' : 'Temperature / nature'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Meridianos' : 'Channels'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Dosis' : 'Dose'); ?></th>
                    <th scope="col"><?php echo s($ises ? 'Estado' : 'Status'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($propertyrows as $propertyrow): ?>
                    <?php
                    $status = (string)$propertyrow['status'];
                    $herbparams = ['herb' => (string)$propertyrow['name']];
                    if ($ises) {
                        $herbparams['lang'] = 'es';
                    }
                    $herburl = new moodle_url('/blocks/myprogress/single-herbs-study-guide.php', $herbparams);
                    $flavor = $propertyrow['flavor'][$propertylang] ?? null;
                    $temperature = $propertyrow['temperature'][$propertylang] ?? null;
                    $channels = $propertyrow['channels'][$propertylang] ?? null;
                    $dose = $propertyrow['dose'] ?? null;
                    $appendixdose = $propertyrow['appendixDose'] ?? null;
                    $searchtext = implode(' ', array_filter([
                        $propertyrow['name'], $propertyrow['latin'], $flavor, $temperature, $channels, $dose, $appendixdose,
                    ]));
                    $statuslabel = $status === 'verified'
                        ? ($ises ? 'Verificada' : 'Verified')
                        : ($status === 'properties-only'
                            ? ($ises ? 'Propiedades Chart 6' : 'Chart 6 properties')
                            : ($status === 'dose-only'
                            ? ($ises ? 'Solo dosis' : 'Dose only')
                            : ($ises ? 'Pendiente' : 'Pending')));
                    ?>
                    <tr data-properties-row data-status="<?php echo s($status); ?>" data-search="<?php echo s(strtolower($searchtext)); ?>">
                        <th scope="row"><a href="<?php echo $herburl->out(false); ?>"><?php echo s($propertyrow['name']); ?></a><small><?php echo s($propertyrow['latin']); ?></small></th>
                        <td><?php echo $flavor ? s($flavor) : '<span aria-label="Pending">—</span>'; ?></td>
                        <td><?php echo $temperature ? s($temperature) : '<span aria-label="Pending">—</span>'; ?></td>
                        <td><?php echo $channels ? s($channels) : '<span aria-label="Pending">—</span>'; ?></td>
                        <td>
                            <?php if ($dose): ?><span><?php echo s($dose); ?></span><?php endif; ?>
                            <?php if ($appendixdose): ?><small><?php echo s('Appendix 1 · ' . ($ises ? str_replace(['Less than', 'Up to', '(source lists both)'], ['Menos de', 'Hasta', '(la fuente muestra ambos)'], $appendixdose) : $appendixdose)); ?></small><?php endif; ?>
                            <?php if (!$dose && !$appendixdose): ?><span aria-label="Pending">—</span><?php endif; ?>
                        </td>
                        <td><span class="single-herbs-properties__status single-herbs-properties__status--<?php echo s($status); ?>"><?php echo s($statuslabel); ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="single-herbs-properties__empty-results" data-properties-empty hidden><?php echo s($ises ? 'No hay hierbas que coincidan con esos filtros.' : 'No herbs match those filters.'); ?></p>
    </section>
    <script>
    (function() {
        const root = document.querySelector('.single-herbs-properties');
        if (!root) return;
        const input = root.querySelector('#single-herbs-properties-search');
        const rows = Array.from(root.querySelectorAll('[data-properties-row]'));
        const buttons = Array.from(root.querySelectorAll('[data-properties-filter]'));
        const count = root.querySelector('[data-properties-count]');
        const empty = root.querySelector('[data-properties-empty]');
        let filter = 'all';
        const normalize = value => String(value || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
        const refresh = function() {
            const query = normalize(input.value);
            let visible = 0;
            rows.forEach(function(row) {
                const matchesFilter = filter === 'all' || row.dataset.status === filter;
                const matchesQuery = !query || normalize(row.dataset.search).includes(query);
                row.hidden = !(matchesFilter && matchesQuery);
                if (!row.hidden) visible++;
            });
            count.textContent = String(visible);
            empty.hidden = visible !== 0;
        };
        input.addEventListener('input', refresh);
        buttons.forEach(function(button) {
            button.addEventListener('click', function() {
                filter = button.dataset.propertiesFilter;
                buttons.forEach(item => item.classList.toggle('is-active', item === button));
                refresh();
            });
        });
    })();
    </script>
<?php endif; ?>
