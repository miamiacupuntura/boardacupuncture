<?php
// Shared renderer for the three independent Question Bank appendix lists.

require_once(__DIR__ . '/../../config.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

if (!isset($herbologyappendixkind) || !in_array($herbologyappendixkind, ['a', 'b', 'c'], true)) {
    throw new coding_exception('Unknown Herbology appendix list.');
}

$displaylang = optional_param('lang', 'en', PARAM_ALPHA) === 'es' ? 'es' : 'en';
$query = trim(optional_param('q', '', PARAM_TEXT));
$page = max(0, optional_param('page', 0, PARAM_INT));
$pagesize = 50;
$ises = $displaylang === 'es';

$config = [
    'a' => [
        'path' => '/blocks/myprogress/admin-herbology-appendix-a.php',
        'title' => $ises ? 'Appendix A · Hierbas' : 'Appendix A · Herbs',
        'subtitle' => $ises
            ? 'Lista independiente de 304 hierbas con nombre Pinyin, nombre farmacéutico y datos principales.'
            : 'Independent list of 304 herbs with Pinyin, pharmaceutical name, and key facts.',
        'file' => __DIR__ . '/single-herb-properties.json',
        'collection' => 'herbs',
        'expected' => 304,
    ],
    'b' => [
        'path' => '/blocks/myprogress/admin-herbology-appendix-b.php',
        'title' => 'Appendix B · Dui Yao',
        'subtitle' => $ises
            ? 'Lista independiente de 51 parejas Dui Yao con acción, categoría y fórmulas relacionadas.'
            : 'Independent list of 51 Dui Yao pairs with action, category, and related formulas.',
        'file' => __DIR__ . '/dui-yao-smart-data.json',
        'collection' => 'pairs',
        'expected' => 51,
    ],
    'c' => [
        'path' => '/blocks/myprogress/admin-herbology-appendix-c.php',
        'title' => $ises ? 'Appendix C · Fórmulas' : 'Appendix C · Formulas',
        'subtitle' => $ises
            ? 'Lista independiente de 181 fórmulas con nombre oficial, traducción y capítulo Bensky.'
            : 'Independent list of 181 formulas with official name, translation, and Bensky chapter.',
        'file' => __DIR__ . '/formula-board-roster-2026.json',
        'collection' => 'formulas',
        'expected' => 181,
    ],
][$herbologyappendixkind];

$data = is_readable($config['file'])
    ? json_decode((string)file_get_contents($config['file']), true)
    : null;
$records = is_array($data) && is_array($data[$config['collection']] ?? null)
    ? array_values(array_filter($data[$config['collection']], 'is_array'))
    : [];
if (count($records) !== $config['expected']) {
    throw new moodle_exception('The requested NCBAHM appendix roster is temporarily unavailable.');
}

$normalizesearch = static function(string $value): string {
    $value = core_text::strtolower(core_text::specialtoascii($value));
    return trim((string)preg_replace('/[^a-z0-9]+/', ' ', $value));
};
$queryneedle = $normalizesearch($query);
$searchrecord = static function(array $record) use ($herbologyappendixkind, $displaylang): string {
    if ($herbologyappendixkind === 'a') {
        return implode(' ', [
            (string)($record['name'] ?? ''),
            (string)($record['latin'] ?? ''),
            (string)($record['flavor'][$displaylang] ?? ''),
            (string)($record['temperature'][$displaylang] ?? ''),
            (string)($record['channels'][$displaylang] ?? ''),
            (string)($record['actionSummary'][$displaylang] ?? ''),
            (string)($record['indicationSummary'][$displaylang] ?? ''),
            (string)($record['dose'] ?? ''),
        ]);
    }
    if ($herbologyappendixkind === 'b') {
        return implode(' ', [
            (string)($record['title'] ?? ''),
            (string)($record['characters'] ?? ''),
            implode(' ', array_map('strval', (array)($record['herbs'] ?? []))),
            (string)($record['category'][$displaylang] ?? ''),
            (string)($record['action'][$displaylang] ?? ''),
            (string)($record['formulas'][$displaylang] ?? ''),
        ]);
    }
    return implode(' ', [
        (string)($record['name'] ?? ''),
        (string)($record['englishName'] ?? ''),
        (string)($record['benskyCategory']['chapter'] ?? ''),
        (string)($record['benskyCategory']['code'] ?? ''),
        (string)($record['benskyCategory'][$displaylang] ?? ''),
    ]);
};
if ($queryneedle !== '') {
    $records = array_values(array_filter(
        $records,
        static fn(array $record): bool => core_text::strpos(
            $normalizesearch($searchrecord($record)),
            $queryneedle
        ) !== false
    ));
}

$resultcount = count($records);
$pagecount = max(1, (int)ceil($resultcount / $pagesize));
$page = min($page, $pagecount - 1);
$pagerecords = array_slice($records, $page * $pagesize, $pagesize);

$pageparams = array_filter([
    'q' => $query !== '' ? $query : null,
    'page' => $page > 0 ? $page : null,
    'lang' => $ises ? 'es' : null,
]);
$pageurl = new moodle_url($config['path'], $pageparams);
$questionbankurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', [
    'lang' => $ises ? 'es' : 'en',
]);
$languageurl = new moodle_url($config['path'], array_filter([
    'q' => $query !== '' ? $query : null,
    'lang' => $ises ? 'en' : 'es',
]));
$clearurl = new moodle_url($config['path'], array_filter([
    'lang' => $ises ? 'es' : null,
]));

$PAGE->set_url($pageurl);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($config['title']);
$PAGE->set_heading($config['title']);

echo $OUTPUT->header();
?>
<style>
.appendix-list{max-width:1240px;margin:0 auto 3rem;color:#082f49}.appendix-list__toolbar{position:sticky;top:.5rem;z-index:20;display:flex;justify-content:space-between;gap:.75rem;flex-wrap:wrap;margin-bottom:1rem;padding:.65rem;border:1px solid #cbdbe5;border-radius:.75rem;background:rgba(248,250,252,.97);box-shadow:0 4px 14px rgba(8,47,73,.08)}.appendix-list__group{display:flex;gap:.6rem;flex-wrap:wrap}.appendix-list__button{display:inline-flex;align-items:center;justify-content:center;border:1px solid #b8cbd8;border-radius:.6rem;padding:.65rem .9rem;background:#fff;color:#083b5c;font-weight:750;text-decoration:none}.appendix-list__button:hover{background:#f1f7fa;text-decoration:none}.appendix-list__button--primary{background:#087f6b;border-color:#087f6b;color:#fff}.appendix-list__hero{border:1px solid #b8d8d1;border-left:6px solid #087f6b;border-radius:.85rem;background:#eefaf6;padding:1.2rem 1.35rem;margin-bottom:1rem}.appendix-list__hero h2{margin:0 0 .35rem}.appendix-list__hero p{margin:.25rem 0}.appendix-list__search{display:flex;align-items:end;gap:.65rem;flex-wrap:wrap;border:1px solid #cbdbe5;border-radius:.8rem;background:#fff;padding:1rem;margin-bottom:1rem}.appendix-list__search label{display:grid;gap:.35rem;flex:1;min-width:260px;font-weight:750}.appendix-list__search input{width:100%;min-height:44px;border:1px solid #aebfcb;border-radius:.5rem;padding:.55rem .7rem;font-size:1rem}.appendix-list__count{font-weight:750;color:#34566b;margin:.3rem 0 1rem}.appendix-list__table-wrap{overflow-x:auto;border:1px solid #cbdbe5;border-radius:.8rem;background:#fff}.appendix-list__table{width:100%;border-collapse:collapse}.appendix-list__table th,.appendix-list__table td{text-align:left;vertical-align:top;padding:.8rem .75rem;border-bottom:1px solid #dce6ec}.appendix-list__table th{background:#edf6f5;color:#0a3d52;font-size:.86rem}.appendix-list__table tr:last-child td{border-bottom:0}.appendix-list__name{font-weight:800;color:#083b5c}.appendix-list__secondary{display:block;color:#527084;margin-top:.2rem}.appendix-list__cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem}.appendix-list__card{border:1px solid #cbdbe5;border-radius:.8rem;background:#fff;padding:1rem}.appendix-list__card h3{margin:0 0 .5rem;font-size:1.05rem}.appendix-list__card p{margin:.35rem 0}.appendix-list__pagination{display:flex;justify-content:center;gap:.45rem;flex-wrap:wrap;margin-top:1.2rem}.appendix-list__pagination a{display:inline-flex;min-width:40px;min-height:40px;align-items:center;justify-content:center;border:1px solid #b8cbd8;border-radius:.45rem;background:#fff;text-decoration:none;font-weight:750}.appendix-list__pagination a[aria-current="page"]{background:#087f6b;border-color:#087f6b;color:#fff}.appendix-list__empty{border:1px solid #d6e0e6;border-radius:.8rem;background:#fff;padding:1.25rem}.appendix-list__footer-nav{display:flex;justify-content:center;margin-top:1.25rem}.appendix-list__floating-back{position:fixed;right:1.2rem;bottom:1.2rem;z-index:30;box-shadow:0 5px 18px rgba(8,47,73,.24)}@media(max-width:760px){.appendix-list__toolbar{top:.25rem}.appendix-list__cards{grid-template-columns:1fr}.appendix-list__search>*{width:100%}.appendix-list__button{width:100%}.appendix-list__floating-back{right:.65rem;bottom:.65rem;width:auto;max-width:calc(100vw - 1.3rem)}}
</style>
<main class="appendix-list">
    <nav class="appendix-list__toolbar" aria-label="<?php echo $ises ? 'Navegación' : 'Navigation'; ?>">
        <div class="appendix-list__group"><a class="appendix-list__button" href="<?php echo $questionbankurl->out(false); ?>"><?php echo $ises ? 'Regresar al NCBAHM Question Bank' : 'Back to NCBAHM Question Bank'; ?></a></div>
        <a class="appendix-list__button" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
    </nav>
    <section class="appendix-list__hero">
        <h2><?php echo s($config['title']); ?></h2>
        <p><?php echo s($config['subtitle']); ?></p>
    </section>
    <form class="appendix-list__search" method="get" action="<?php echo (new moodle_url($config['path']))->out(false); ?>">
        <label><?php echo $ises ? 'Buscar en esta lista' : 'Search this list'; ?><input type="search" name="q" value="<?php echo s($query); ?>" placeholder="<?php echo $ises ? 'Escriba un nombre, acción, categoría o palabra clave' : 'Enter a name, action, category, or keyword'; ?>"></label>
        <input type="hidden" name="lang" value="<?php echo s($displaylang); ?>">
        <button class="appendix-list__button" type="submit"><?php echo $ises ? 'Buscar' : 'Search'; ?></button>
        <?php if ($query !== ''): ?><a class="appendix-list__button" href="<?php echo $clearurl->out(false); ?>"><?php echo $ises ? 'Limpiar' : 'Clear'; ?></a><?php endif; ?>
    </form>
    <p class="appendix-list__count"><?php echo $resultcount; ?> <?php echo $ises ? 'resultado(s)' : 'result(s)'; ?><?php if ($query !== ''): ?> · “<?php echo s($query); ?>”<?php endif; ?></p>

    <?php if ($pagerecords === []): ?>
        <div class="appendix-list__empty"><?php echo $ises ? 'No se encontraron coincidencias en esta lista.' : 'No matches were found in this list.'; ?></div>
    <?php elseif ($herbologyappendixkind === 'b'): ?>
        <section class="appendix-list__cards" aria-label="Dui Yao">
            <?php foreach ($pagerecords as $record): ?>
                <article class="appendix-list__card">
                    <h3><?php echo (int)($record['number'] ?? 0); ?>. <?php echo s((string)($record['title'] ?? '')); ?><?php if (!empty($record['characters'])): ?> · <?php echo s((string)$record['characters']); ?><?php endif; ?></h3>
                    <p><strong><?php echo $ises ? 'Categoría:' : 'Category:'; ?></strong> <?php echo s((string)($record['category'][$displaylang] ?? '')); ?></p>
                    <p><strong><?php echo $ises ? 'Acción:' : 'Action:'; ?></strong> <?php echo s((string)($record['action'][$displaylang] ?? '')); ?></p>
                    <p><strong><?php echo $ises ? 'Fórmulas relacionadas:' : 'Related formulas:'; ?></strong> <?php echo s((string)($record['formulas'][$displaylang] ?? '—')); ?></p>
                </article>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <div class="appendix-list__table-wrap"><table class="appendix-list__table">
            <?php if ($herbologyappendixkind === 'a'): ?>
                <thead><tr><th><?php echo $ises ? 'Hierba' : 'Herb'; ?></th><th><?php echo $ises ? 'Propiedades y canales' : 'Properties and channels'; ?></th><th><?php echo $ises ? 'Acción e indicaciones' : 'Action and indications'; ?></th><th><?php echo $ises ? 'Dosis' : 'Dose'; ?></th></tr></thead>
                <tbody><?php foreach ($pagerecords as $record): ?><tr><td><span class="appendix-list__name"><?php echo s((string)($record['name'] ?? '')); ?></span><span class="appendix-list__secondary"><?php echo s((string)($record['latin'] ?? '')); ?></span></td><td><?php echo s(trim((string)($record['flavor'][$displaylang] ?? '') . '; ' . (string)($record['temperature'][$displaylang] ?? '')), '; '); ?><span class="appendix-list__secondary"><?php echo s((string)($record['channels'][$displaylang] ?? '')); ?></span></td><td><?php echo s((string)($record['actionSummary'][$displaylang] ?? '')); ?><span class="appendix-list__secondary"><?php echo s((string)($record['indicationSummary'][$displaylang] ?? '')); ?></span></td><td><?php echo s((string)($record['dose'] ?? '—')); ?></td></tr><?php endforeach; ?></tbody>
            <?php else: ?>
                <thead><tr><th><?php echo $ises ? 'Fórmula' : 'Formula'; ?></th><th><?php echo $ises ? 'Traducción' : 'Translation'; ?></th><th><?php echo $ises ? 'Capítulo Bensky' : 'Bensky chapter'; ?></th></tr></thead>
                <tbody><?php foreach ($pagerecords as $record): ?><tr><td><span class="appendix-list__name"><?php echo s((string)($record['name'] ?? '')); ?></span></td><td><?php echo s((string)($record['englishName'] ?? '')); ?></td><td><strong><?php echo (int)($record['benskyCategory']['chapter'] ?? 0); ?></strong> · <?php echo s((string)($record['benskyCategory'][$displaylang] ?? '')); ?></td></tr><?php endforeach; ?></tbody>
            <?php endif; ?>
        </table></div>
    <?php endif; ?>

    <?php if ($pagecount > 1): ?><nav class="appendix-list__pagination" aria-label="<?php echo $ises ? 'Páginas' : 'Pages'; ?>"><?php for ($pageindex = 0; $pageindex < $pagecount; $pageindex++): ?><a href="<?php echo (new moodle_url($config['path'], array_filter(['q' => $query !== '' ? $query : null, 'page' => $pageindex > 0 ? $pageindex : null, 'lang' => $ises ? 'es' : null])))->out(false); ?>"<?php echo $pageindex === $page ? ' aria-current="page"' : ''; ?>><?php echo $pageindex + 1; ?></a><?php endfor; ?></nav><?php endif; ?>
    <nav class="appendix-list__footer-nav"><a class="appendix-list__button appendix-list__button--primary" href="<?php echo $questionbankurl->out(false); ?>"><?php echo $ises ? 'Regresar al NCBAHM Question Bank' : 'Back to NCBAHM Question Bank'; ?></a></nav>
    <a class="appendix-list__button appendix-list__button--primary appendix-list__floating-back" href="<?php echo $questionbankurl->out(false); ?>"><?php echo $ises ? 'Regresar al Question Bank' : 'Back to Question Bank'; ?></a>
</main>
<?php
echo $OUTPUT->footer();
