<?php
// Official guideline formulas organized by tongue and pulse findings.

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
$pageurl = new moodle_url('/blocks/myprogress/formula-tongue-pulse.php', $pageparams);
$languageparams = $query !== '' ? ['q' => $query] : [];
if (!$ises) {
    $languageparams['lang'] = 'es';
}
$languageurl = new moodle_url('/blocks/myprogress/formula-tongue-pulse.php', $languageparams);
$libraryparams = ['area' => 'herbology', 'view' => 'library'];
if ($ises) {
    $libraryparams['lang'] = 'es';
}
$libraryurl = new moodle_url('/blocks/myprogress/study-guide.php', $libraryparams);

$profilefile = __DIR__ . '/formulas-study-guide-data.json';
$rosterfile = __DIR__ . '/formula-board-roster-2026.json';
$findingsfile = __DIR__ . '/formula-tongue-pulse-data.json';
$profiles = is_readable($profilefile) ? json_decode(file_get_contents($profilefile), true) : null;
$roster = is_readable($rosterfile) ? json_decode(file_get_contents($rosterfile), true) : null;
$findings = is_readable($findingsfile) ? json_decode(file_get_contents($findingsfile), true) : null;
if (!is_array($profiles) || !is_array($roster) || !is_array($findings)
        || count($profiles['formulas'] ?? []) !== 63
        || count($roster['formulas'] ?? []) !== 181
        || (int)($findings['total'] ?? 0) !== 63
        || count($findings['formulas'] ?? []) !== 63) {
    throw new coding_exception('Formula tongue and pulse guideline data is unavailable.');
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
$profilemap = [];
foreach ($profiles['formulas'] as $profile) {
    $profilemap[(int)($profile['number'] ?? 0)] = $profile;
}
$aliases = ['jin gui shen qi wan' => 'shen qi wan'];
$seen = [];
$rows = [];
foreach ($findings['formulas'] as $finding) {
    $number = (int)($finding['number'] ?? 0);
    $sourcename = trim((string)($finding['name'] ?? ''));
    $tongue = trim((string)($finding['tongue'] ?? ''));
    $pulse = trim((string)($finding['pulse'] ?? ''));
    $profile = $profilemap[$number] ?? null;
    $sourcekey = $normalize($sourcename);
    $officialkey = $aliases[$sourcekey] ?? $sourcekey;
    if ($number < 1 || $number > 63 || isset($seen[$number]) || !is_array($profile)
            || $normalize((string)($profile['name'] ?? '')) !== $sourcekey
            || !isset($official[$officialkey]) || $tongue === '' || $pulse === '') {
        throw new coding_exception('Invalid Formula Tongue and Pulse row #' . $number . '.');
    }
    $seen[$number] = true;
    $officialname = $official[$officialkey];
    $category = trim((string)($profile[$ises ? 'categoryEs' : 'category'] ?? $profile['category'] ?? ''));
    $searchtext = core_text::strtolower($officialname . ' ' . $sourcename . ' ' . $category . ' ' . $tongue . ' ' . $pulse);
    if ($query !== '' && core_text::strpos($searchtext, core_text::strtolower($query)) === false) {
        continue;
    }
    $params = ['formula' => $officialname, 'sort' => 'az', 'from' => 'tonguepulse'];
    if ($ises) {
        $params['lang'] = 'es';
    }
    $rows[] = [
        'number' => $number,
        'name' => $officialname,
        'sourceName' => $sourcename,
        'category' => $category,
        'tongue' => $tongue,
        'pulse' => $pulse,
        'url' => new moodle_url('/blocks/myprogress/formula-list.php', $params),
    ];
}
usort($rows, static fn(array $left, array $right): int => $left['number'] <=> $right['number']);
if ($query === '' && (count($rows) !== 63 || count($seen) !== 63)) {
    throw new coding_exception('Formula Tongue and Pulse must contain exactly 63 official guideline formulas.');
}

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Lengua y pulso de fórmulas' : 'Formula Tongue and Pulse');
$PAGE->set_heading($ises ? 'Lengua y pulso de fórmulas' : 'Formula Tongue and Pulse');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();
?>
<style>
.formula-tp{width:min(1280px,calc(100% - 28px));margin:24px auto 58px;color:#102a43}
.formula-tp__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:24px;border:1px solid #bfd7d4;border-top:5px solid #087c69;border-radius:14px;background:#fff}
.formula-tp__eyebrow{margin:0 0 6px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.formula-tp h2{margin:0 0 8px;font-size:clamp(29px,4vw,42px)}
.formula-tp__lead{max-width:820px;margin:0;color:#526b84;line-height:1.55}
.formula-tp__actions{display:flex;flex-wrap:wrap;gap:9px}
.formula-tp__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}
.formula-tp__button:hover{background:#eef7f5}
.formula-tp__tools{display:grid;grid-template-columns:minmax(240px,1fr) auto;gap:12px;align-items:end;margin-bottom:18px;padding:16px;border:1px solid #c7ddd9;border-radius:12px;background:#f8fcfb}
.formula-tp__tools label{display:grid;gap:6px;font-size:12px;font-weight:850}.formula-tp__tools input{width:100%;min-height:46px;padding:9px 12px;border:1px solid #aebfca;border-radius:8px;font:inherit}.formula-tp__summary{margin:0;color:#36566c;font-weight:850}
.formula-tp__note{margin:0 0 16px;padding:13px 15px;border-left:5px solid #087c69;border-radius:8px;background:#edf8f5;color:#244e43}
.formula-tp__table-wrap{overflow-x:auto;border:1px solid #bfd0d9;border-radius:12px;background:#fff;box-shadow:0 5px 18px rgba(23,50,77,.06)}
.formula-tp__table{width:100%;min-width:1040px;border-collapse:collapse}.formula-tp__table th,.formula-tp__table td{padding:11px 13px;border-bottom:1px solid #d5e1e7;vertical-align:top;text-align:left;line-height:1.5}.formula-tp__table thead th{background:#0d7868;color:#fff;font-size:.76rem;letter-spacing:.05em;text-transform:uppercase}.formula-tp__table tbody tr:nth-child(even){background:#f8fbfc}.formula-tp__number{width:54px;text-align:center!important;font-weight:900}.formula-tp__category{width:165px;color:#075f50;font-weight:900}.formula-tp__formula{width:210px}.formula-tp__formula-link{color:#0f4c81!important;font-weight:900;text-decoration:none}.formula-tp__formula-link:hover{text-decoration:underline}.formula-tp__alias{display:block;margin-top:3px;color:#657c8d;font-size:11px}.formula-tp__finding{color:#263f53;font-weight:650}.formula-tp__empty{padding:30px;border:1px dashed #9db8c8;border-radius:11px;background:#fff;text-align:center;font-weight:800}
@media(max-width:760px){.formula-tp__hero{display:block}.formula-tp__actions{margin-top:16px}.formula-tp__tools{grid-template-columns:1fr}.formula-tp__table{min-width:0}.formula-tp__table thead{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}.formula-tp__table,.formula-tp__table tbody,.formula-tp__table tr,.formula-tp__table th,.formula-tp__table td{display:block;width:100%}.formula-tp__table tr{padding:12px;border-bottom:5px solid #e5eef2}.formula-tp__table th,.formula-tp__table td{display:grid;grid-template-columns:100px 1fr;gap:10px;border:0;background:#fff!important}.formula-tp__table th::before,.formula-tp__table td::before{content:attr(data-label);color:#087c69;font-size:.7rem;font-weight:900;text-transform:uppercase}.formula-tp__number{text-align:left!important}}
</style>
<main class="formula-tp">
    <header class="formula-tp__hero">
        <div>
            <p class="formula-tp__eyebrow"><?php echo $ises ? 'GUIDELINE 2026 · 63 FÓRMULAS OFICIALES' : '2026 GUIDELINE · 63 OFFICIAL FORMULAS'; ?></p>
            <h2><?php echo $ises ? 'Lengua y pulso de fórmulas' : 'Formula Tongue and Pulse'; ?></h2>
            <p class="formula-tp__lead"><?php echo $ises
                ? 'Compara la categoría, la lengua y el pulso de cada fórmula oficial. El nombre abre su tarjeta detallada.'
                : 'Compare the category, tongue, and pulse of every official formula. Each formula name opens its detailed study card.'; ?></p>
        </div>
        <div class="formula-tp__actions">
            <a class="formula-tp__button" href="<?php echo $libraryurl->out(false); ?>"><?php echo $ises ? '← Guía de estudio' : '← Study Guide'; ?></a>
            <a class="formula-tp__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
        </div>
    </header>

    <form class="formula-tp__tools" method="get" action="<?php echo (new moodle_url('/blocks/myprogress/formula-tongue-pulse.php'))->out(false); ?>">
        <?php if ($ises) { ?><input type="hidden" name="lang" value="es"><?php } ?>
        <label><span><?php echo $ises ? 'Buscar fórmula, categoría, lengua o pulso' : 'Search formula, category, tongue, or pulse'; ?></span><input type="search" name="q" value="<?php echo s($query); ?>" placeholder="<?php echo $ises ? 'Ej.: slippery, red tongue…' : 'Example: slippery, red tongue…'; ?>"></label>
        <button class="formula-tp__button" type="submit"><?php echo $ises ? 'Buscar' : 'Search'; ?></button>
        <p class="formula-tp__summary"><strong><?php echo count($rows); ?></strong> / 63 <?php echo $ises ? 'fórmulas oficiales visibles' : 'official formulas visible'; ?></p>
        <?php if ($query !== '') { ?><a class="formula-tp__button" href="<?php echo (new moodle_url('/blocks/myprogress/formula-tongue-pulse.php', $ises ? ['lang' => 'es'] : []))->out(false); ?>"><?php echo $ises ? 'Limpiar búsqueda' : 'Clear search'; ?></a><?php } ?>
    </form>

    <p class="formula-tp__note"><?php echo $ises
        ? 'Fuente suministrada: CA Formulas, Chart 7. Los descriptores de lengua y pulso conservan la terminología inglesa de la fuente para fidelidad de examen.'
        : 'Supplied source: CA Formulas, Chart 7. Tongue and pulse terminology follows the supplied exam reference.'; ?></p>

    <?php if (!$rows) { ?>
        <p class="formula-tp__empty"><?php echo $ises ? 'No se encontraron fórmulas.' : 'No formulas matched the search.'; ?></p>
    <?php } else { ?>
        <div class="formula-tp__table-wrap">
            <table class="formula-tp__table">
                <caption class="sr-only"><?php echo $ises ? 'Lengua y pulso de 63 fórmulas oficiales' : 'Tongue and pulse for 63 official formulas'; ?></caption>
                <thead><tr><th>#</th><th><?php echo $ises ? 'Categoría' : 'Category'; ?></th><th><?php echo $ises ? 'Fórmula' : 'Formula'; ?></th><th><?php echo $ises ? 'Lengua' : 'Tongue'; ?></th><th><?php echo $ises ? 'Pulso' : 'Pulse'; ?></th></tr></thead>
                <tbody>
                <?php foreach ($rows as $row) { ?>
                    <tr data-formula-tongue-pulse-row data-formula="<?php echo s($row['name']); ?>">
                        <td class="formula-tp__number" data-label="#">#<?php echo $row['number']; ?></td>
                        <td class="formula-tp__category" data-label="<?php echo $ises ? 'Categoría' : 'Category'; ?>"><?php echo s($row['category']); ?></td>
                        <th class="formula-tp__formula" scope="row" data-label="<?php echo $ises ? 'Fórmula' : 'Formula'; ?>">
                            <a class="formula-tp__formula-link" href="<?php echo $row['url']->out(false); ?>"><?php echo s($row['name']); ?></a>
                            <?php if ($row['sourceName'] !== $row['name']) { ?><small class="formula-tp__alias"><?php echo s($row['sourceName']); ?></small><?php } ?>
                        </th>
                        <td class="formula-tp__finding" data-label="<?php echo $ises ? 'Lengua' : 'Tongue'; ?>"><?php echo s($row['tongue']); ?></td>
                        <td class="formula-tp__finding" data-label="<?php echo $ises ? 'Pulso' : 'Pulse'; ?>"><?php echo s($row['pulse']); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</main>
<?php
echo $OUTPUT->footer();
