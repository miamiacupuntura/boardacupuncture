<?php
// Official guideline formulas organized by category and principal actions.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/herbology-library-navigation.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';
$query = trim(optional_param('q', '', PARAM_TEXT));

$pageparams = $ises ? ['lang' => 'es'] : [];
if ($query !== '') {
    $pageparams['q'] = $query;
}
$pageurl = new moodle_url('/blocks/myprogress/formula-actions.php', $pageparams);
$languageparams = $query !== '' ? ['q' => $query] : [];
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/formula-actions.php', $languageparams);
$libraryparams = ['area' => 'herbology', 'view' => 'library'];
if ($ises) {
    $libraryparams['lang'] = 'es';
}
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', $libraryparams);

$datafile = __DIR__ . '/formulas-study-guide-data.json';
$rosterfile = __DIR__ . '/formula-board-roster-2026.json';
$data = is_readable($datafile) ? json_decode(file_get_contents($datafile), true) : null;
$roster = is_readable($rosterfile) ? json_decode(file_get_contents($rosterfile), true) : null;
if (!is_array($data) || !is_array($roster)
        || count($data['formulas'] ?? []) !== 63
        || (int)($roster['total'] ?? 0) !== 181
        || count($roster['formulas'] ?? []) !== 181) {
    throw new coding_exception('Formula action guideline data is unavailable.');
}

$normalize = static function(string $value): string {
    $value = core_text::strtolower(trim($value));
    return trim((string)preg_replace('/[^a-z0-9]+/u', ' ', $value));
};
$official = [];
foreach ($roster['formulas'] as $formula) {
    $name = trim((string)($formula['name'] ?? ''));
    if ($name !== '') {
        $official[$normalize($name)] = $name;
    }
}
$aliases = ['jin gui shen qi wan' => 'shen qi wan'];

$rows = [];
foreach ($data['formulas'] as $profile) {
    $sourcename = trim((string)($profile['name'] ?? ''));
    $sourcekey = $normalize($sourcename);
    $officialkey = $aliases[$sourcekey] ?? $sourcekey;
    if (!isset($official[$officialkey])) {
        continue;
    }
    $officialname = $official[$officialkey];
    $category = trim((string)($profile[$ises ? 'categoryEs' : 'category'] ?? $profile['category'] ?? ''));
    $action = trim((string)($profile[$ises ? 'actionEs' : 'action'] ?? $profile['action'] ?? ''));
    if ($category === '' || $action === '') {
        continue;
    }
    $searchtext = core_text::strtolower($officialname . ' ' . $sourcename . ' ' . $category . ' ' . $action);
    if ($query !== '' && core_text::strpos($searchtext, core_text::strtolower($query)) === false) {
        continue;
    }
    $params = ['formula' => $officialname, 'sort' => 'az', 'from' => 'actions'];
    if ($ises) {
        $params['lang'] = 'es';
    }
    $rows[] = [
        'number' => (int)($profile['number'] ?? 0),
        'name' => $officialname,
        'sourceName' => $sourcename,
        'category' => $category,
        'action' => $action,
        'url' => new moodle_url('/blocks/myprogress/formula-list.php', $params),
    ];
}
usort($rows, static fn(array $left, array $right): int => $left['number'] <=> $right['number']);
if ($query === '' && count($rows) !== 63) {
    throw new coding_exception('Formula and Actions must contain exactly 63 official guideline formulas.');
}

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Fórmulas y acciones' : 'Formula and Actions');
$PAGE->set_heading($ises ? 'Fórmulas y acciones' : 'Formula and Actions');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
?>
<style>
.formula-actions{width:min(1260px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}
.formula-actions__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:24px;border:1px solid #bfd7d4;border-top:5px solid #087c69;border-radius:14px;background:#fff}
.formula-actions__eyebrow{margin:0 0 6px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.formula-actions h2{margin:0 0 8px;font-size:clamp(29px,4vw,42px)}
.formula-actions__lead{max-width:820px;margin:0;color:#526b84;line-height:1.55}
.formula-actions__actions{display:flex;flex-wrap:wrap;gap:9px}
.formula-actions__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}
.formula-actions__button:hover{background:#eef7f5}
.formula-actions__tools{display:grid;grid-template-columns:minmax(240px,1fr) auto;gap:12px;align-items:end;margin-bottom:18px;padding:16px;border:1px solid #c7ddd9;border-radius:12px;background:#f8fcfb}
.formula-actions__tools label{display:grid;gap:6px;font-size:12px;font-weight:850}.formula-actions__tools input{width:100%;min-height:46px;padding:9px 12px;border:1px solid #aebfca;border-radius:8px;font:inherit}.formula-actions__summary{margin:0;color:#36566c;font-weight:850}
.formula-actions__note{margin:0 0 16px;padding:13px 15px;border-left:5px solid #087c69;border-radius:8px;background:#edf8f5;color:#244e43}
.formula-actions__table-wrap{overflow-x:auto;border:1px solid #bfd0d9;border-radius:12px;background:#fff;box-shadow:0 5px 18px rgba(23,50,77,.06)}
.formula-actions__table{width:100%;min-width:930px;border-collapse:collapse}.formula-actions__table th,.formula-actions__table td{padding:11px 13px;border-bottom:1px solid #d5e1e7;vertical-align:top;text-align:left;line-height:1.5}.formula-actions__table thead th{background:#0d7868;color:#fff;font-size:.76rem;letter-spacing:.05em;text-transform:uppercase}.formula-actions__table tbody tr:nth-child(even){background:#f8fbfc}.formula-actions__number{width:54px;text-align:center!important;font-weight:900}.formula-actions__category{width:180px;color:#075f50;font-weight:900}.formula-actions__formula{width:235px}.formula-actions__formula-link{color:#0f4c81!important;font-weight:900;text-decoration:none}.formula-actions__formula-link:hover{text-decoration:underline}.formula-actions__alias{display:block;margin-top:3px;color:#657c8d;font-size:11px}.formula-actions__action{color:#263f53;font-weight:650}.formula-actions__empty{padding:30px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;text-align:center;font-weight:800}
@media(max-width:760px){.formula-actions__hero{display:block}.formula-actions__actions{margin-top:16px}.formula-actions__tools{grid-template-columns:1fr}.formula-actions__table{min-width:0}.formula-actions__table thead{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}.formula-actions__table,.formula-actions__table tbody,.formula-actions__table tr,.formula-actions__table th,.formula-actions__table td{display:block;width:100%}.formula-actions__table tr{padding:12px;border-bottom:5px solid #e5eef2}.formula-actions__table th,.formula-actions__table td{display:grid;grid-template-columns:100px 1fr;gap:10px;border:0;background:#fff!important}.formula-actions__table th::before,.formula-actions__table td::before{content:attr(data-label);color:#087c69;font-size:.7rem;font-weight:900;text-transform:uppercase}.formula-actions__number{text-align:left!important}}
</style>
<main class="formula-actions">
    <header class="formula-actions__hero">
        <div>
            <p class="formula-actions__eyebrow"><?php echo $ises ? 'GUIDELINE 2026 · 63 FÓRMULAS OFICIALES' : '2026 GUIDELINE · 63 OFFICIAL FORMULAS'; ?></p>
            <h2><?php echo $ises ? 'Fórmulas y acciones' : 'Formula and Actions'; ?></h2>
            <p class="formula-actions__lead"><?php echo $ises
                ? 'Repasa cada fórmula oficial por categoría y acción principal. El nombre de la fórmula abre su tarjeta detallada.'
                : 'Review every official formula by category and principal action. Each formula name opens its detailed study card.'; ?></p>
        </div>
        <div class="formula-actions__actions">
            <a class="formula-actions__button" href="<?php echo $libraryurl->out(false); ?>"><?php echo $ises ? '← Guía secundaria' : '← Secondary Study Guide'; ?></a>
            <a class="formula-actions__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
        </div>
    </header>

    <form class="formula-actions__tools" method="get" action="<?php echo (new moodle_url('/blocks/myprogress/formula-actions.php'))->out(false); ?>">
        <?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
        <label><span><?php echo $ises ? 'Buscar fórmula, categoría o acción' : 'Search formula, category, or action'; ?></span><input type="search" name="q" value="<?php echo s($query); ?>" placeholder="<?php echo $ises ? 'Ej.: Clear Heat, tonifica el qi…' : 'Example: Clear Heat, tonifies qi…'; ?>"></label>
        <button class="formula-actions__button" type="submit"><?php echo $ises ? 'Buscar' : 'Search'; ?></button>
        <p class="formula-actions__summary"><strong><?php echo count($rows); ?></strong> / 63 <?php echo $ises ? 'fórmulas oficiales visibles' : 'official formulas visible'; ?></p>
        <?php if ($query !== '') { ?><a class="formula-actions__button" href="<?php echo (new moodle_url('/blocks/myprogress/formula-actions.php', $ises ? ['lang' => 'es'] : []))->out(false); ?>"><?php echo $ises ? 'Limpiar búsqueda' : 'Clear search'; ?></a><?php } ?>
    </form>

    <p class="formula-actions__note"><?php echo $ises
        ? 'Solo se muestran fórmulas incluidas en el guideline oficial. Fuente suministrada: CA Formulas, Chart 3.'
        : 'Only formulas included in the official guideline are shown. Supplied source: CA Formulas, Chart 3.'; ?></p>

    <?php if (!$rows) { ?>
        <p class="formula-actions__empty"><?php echo $ises ? 'No se encontraron fórmulas.' : 'No formulas matched the search.'; ?></p>
    <?php } else { ?>
        <div class="formula-actions__table-wrap">
            <table class="formula-actions__table">
                <caption class="sr-only"><?php echo $ises ? 'Fórmulas oficiales organizadas por categoría y acción' : 'Official formulas organized by category and action'; ?></caption>
                <thead><tr><th>#</th><th><?php echo $ises ? 'Categoría' : 'Category'; ?></th><th><?php echo $ises ? 'Fórmula' : 'Formula'; ?></th><th><?php echo $ises ? 'Acción' : 'Action'; ?></th></tr></thead>
                <tbody>
                <?php foreach ($rows as $row) { ?>
                    <tr data-formula-action-row data-formula="<?php echo s($row['name']); ?>">
                        <td class="formula-actions__number" data-label="#">#<?php echo $row['number']; ?></td>
                        <td class="formula-actions__category" data-label="<?php echo $ises ? 'Categoría' : 'Category'; ?>"><?php echo s($row['category']); ?></td>
                        <th class="formula-actions__formula" scope="row" data-label="<?php echo $ises ? 'Fórmula' : 'Formula'; ?>">
                            <a class="formula-actions__formula-link" href="<?php echo $row['url']->out(false); ?>"><?php echo s($row['name']); ?></a>
                            <?php if ($row['sourceName'] !== $row['name']) { ?><small class="formula-actions__alias"><?php echo s($row['sourceName']); ?></small><?php } ?>
                        </th>
                        <td class="formula-actions__action" data-label="<?php echo $ises ? 'Acción' : 'Action'; ?>"><?php echo s($row['action']); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</main>
<?php
echo $OUTPUT->footer();
