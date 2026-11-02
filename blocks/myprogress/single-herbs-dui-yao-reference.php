<?php
// Shared Duì Yào cross-reference for every Single Herbs category.

defined('MOODLE_INTERNAL') || die();

$duiyaodatafile = dirname(__DIR__, 2) . '/study-guide/comparisons/dui-yao-data.json';
$duiyaopairs = [];
if (is_readable($duiyaodatafile)) {
    $duiyaodecoded = json_decode((string) file_get_contents($duiyaodatafile), true);
    if (is_array($duiyaodecoded)) {
        $duiyaopairs = $duiyaodecoded;
    }
}

$duiyaonormalize = static function(string $value): string {
    $value = strtr($value, [
        'ā' => 'a', 'á' => 'a', 'ǎ' => 'a', 'à' => 'a',
        'Ā' => 'a', 'Á' => 'a', 'Ǎ' => 'a', 'À' => 'a',
        'ē' => 'e', 'é' => 'e', 'ě' => 'e', 'è' => 'e',
        'Ē' => 'e', 'É' => 'e', 'Ě' => 'e', 'È' => 'e',
        'ī' => 'i', 'í' => 'i', 'ǐ' => 'i', 'ì' => 'i',
        'Ī' => 'i', 'Í' => 'i', 'Ǐ' => 'i', 'Ì' => 'i',
        'ō' => 'o', 'ó' => 'o', 'ǒ' => 'o', 'ò' => 'o',
        'Ō' => 'o', 'Ó' => 'o', 'Ǒ' => 'o', 'Ò' => 'o',
        'ū' => 'u', 'ú' => 'u', 'ǔ' => 'u', 'ù' => 'u',
        'Ū' => 'u', 'Ú' => 'u', 'Ǔ' => 'u', 'Ù' => 'u',
        'ǖ' => 'u', 'ǘ' => 'u', 'ǚ' => 'u', 'ǜ' => 'u', 'ü' => 'u',
        'Ǖ' => 'u', 'Ǘ' => 'u', 'Ǚ' => 'u', 'Ǜ' => 'u', 'Ü' => 'u',
    ]);
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);
    return trim((string) preg_replace('/\s+/', ' ', (string) $value));
};

$duiyaocanonicalize = static function(string $value) use ($duiyaonormalize): string {
    $value = $duiyaonormalize($value);
    return [
        'xin yi hua' => 'xin yi',
    ][$value] ?? $value;
};

$duiyaoselectedtitle = (string) ($selected['name'] ?? '');
$duiyaocategoryherbs = [];
if (preg_match_all('/\(([^()]*)\)/u', $duiyaoselectedtitle, $duiyaoparentheses) && !empty($duiyaoparentheses[1])) {
    $duiyaolastlist = (string) end($duiyaoparentheses[1]);
    foreach (explode(',', $duiyaolastlist) as $duiyaocategoryherb) {
        $duiyaocategoryherb = $duiyaocanonicalize($duiyaocategoryherb);
        if ($duiyaocategoryherb !== '') {
            $duiyaocategoryherbs[] = $duiyaocategoryherb;
        }
    }
}
if (!empty($duiyaoadditionalherbs) && is_array($duiyaoadditionalherbs)) {
    foreach ($duiyaoadditionalherbs as $duiyaoadditionalherb) {
        $duiyaoadditionalherb = $duiyaocanonicalize((string) $duiyaoadditionalherb);
        if ($duiyaoadditionalherb !== '') {
            $duiyaocategoryherbs[] = $duiyaoadditionalherb;
        }
    }
}
$duiyaocategoryherbs = array_values(array_unique($duiyaocategoryherbs));
$duiyaomatches = [];
foreach ($duiyaopairs as $duiyaopair) {
    if (empty($duiyaopair['title']) || empty($duiyaopair['slug'])) {
        continue;
    }
    $duiyaomembers = preg_split('/\s*\+\s*/u', (string) $duiyaopair['title']);
    $duiyaohasmember = false;
    foreach ($duiyaomembers as $duiyaomember) {
        $duiyaoneedle = $duiyaocanonicalize((string) $duiyaomember);
        if ($duiyaoneedle === '') {
            continue;
        }
        foreach ($duiyaocategoryherbs as $duiyaocategoryherb) {
            if ($duiyaoneedle === $duiyaocategoryherb) {
                $duiyaohasmember = true;
                break 2;
            }
        }
    }
    if ($duiyaohasmember) {
        $duiyaomatches[] = $duiyaopair;
    }
}

$duiyaoindexurl = new moodle_url($ises
    ? '/study-guide/comparisons/dui-yao-es.html'
    : '/study-guide/comparisons/dui-yao.html');
?>

<section class="single-herbs-guide__section single-herbs-guide__dui-yao-reference" data-dui-yao-reference aria-labelledby="single-herbs-dui-yao-title">
    <h4 id="single-herbs-dui-yao-title"><?php echo s($ises ? 'Referencia Duì Yào' : 'Duì Yào reference'); ?></h4>
    <?php if ((int) $selectedguidenumber === 10): ?>
        <p class="single-herbs-guide__scope-note"><strong><?php echo s($ises
            ? 'Huo Ma Ren no tiene ninguna pareja Duì Yào en el listado vigente de 51 parejas.'
            : 'Huo Ma Ren has no Duì Yào pair in the current 51-pair roster.'); ?></strong> <?php echo s($ises
            ? 'Yu Li Ren tampoco tiene una coincidencia vigente. Las parejas mostradas a continuación no pertenecen a la categoría de laxantes humectantes: aparecen solamente porque contienen Xing Ren, Tao Ren, Dang Gui o Rou Cong Rong, hierbas de otras categorías que pueden humedecer intestinos y facilitar la evacuación como acción secundaria. Esta referencia no amplía el núcleo de la categoría.'
            : 'Yu Li Ren also has no current match. The pairs shown below do not belong to the moist-laxative category: they appear only because they contain Xing Ren, Tao Ren, Dang Gui or Rou Cong Rong, herbs from other categories that can moisten the Intestines and facilitate bowel movement as a secondary action. This reference does not expand the category core.'); ?></p>
    <?php endif; ?>
    <?php if ($duiyaomatches): ?>
        <p class="single-herbs-guide__section-lead"><?php echo s($ises
            ? count($duiyaomatches) . ' pareja(s) vigente(s) contienen al menos una hierba nombrada en esta página, incluidas sus referencias cruzadas.'
            : count($duiyaomatches) . ' current pair(s) contain at least one herb named on this page, including its cross-category references.'); ?></p>
        <div class="single-herbs-guide__comparisons">
            <?php foreach ($duiyaomatches as $duiyaopair): ?>
                <?php
                $duiyaopath = '/study-guide/comparisons/' . $duiyaopair['slug'] . ($ises ? '-es' : '') . '.html';
                $duiyaourl = new moodle_url($duiyaopath, [
                    'from' => 'single-herbs',
                    'focus' => (string) $selected['number'],
                ]);
                $duiyaoaction = (string) ($duiyaopair['action'][$ises ? 'es' : 'en'] ?? '');
                $duiyaoformulas = (string) ($duiyaopair['formulas'][$ises ? 'es' : 'en'] ?? '');
                ?>
                <a class="single-herbs-guide__comparison" data-dui-yao-pair="<?php echo (int) ($duiyaopair['number'] ?? 0); ?>" href="<?php echo $duiyaourl->out(false); ?>">
                    <strong><?php echo s((string) $duiyaopair['title']); ?><?php if (!empty($duiyaopair['characters'])): ?> · <?php echo s((string) $duiyaopair['characters']); ?><?php endif; ?></strong>
                    <span><?php echo s($duiyaoaction); ?></span>
                    <small><?php echo s(($ises ? 'Fórmulas: ' : 'Formulas: ') . ($duiyaoformulas !== '' ? $duiyaoformulas : '—')); ?></small>
                </a>
            <?php endforeach; ?>
        </div>
        <p class="single-herbs-guide__scope-note"><?php echo s($ises
            ? 'La coincidencia significa que al menos un miembro de la pareja está nombrado en esta página; puede ser una hierba principal o una referencia cruzada de otra categoría. Abre la ficha para estudiar la acción conjunta.'
            : 'A match means at least one pair member is named on this page; it may be a primary herb or a cross-reference from another category. Open the card to study their combined action.'); ?></p>
    <?php else: ?>
        <div class="single-herbs-guide__comparisons">
            <a class="single-herbs-guide__comparison" href="<?php echo $duiyaoindexurl->out(false); ?>">
                <strong><?php echo s($ises ? 'Sin coincidencias en el listado vigente de 51 parejas Duì Yào' : 'No match in the current 51-pair Duì Yào roster'); ?></strong>
                <span><?php echo s($ises
                    ? 'Ninguna de las hierbas nombradas en esta página pertenece a una pareja Duì Yào vigente del Board.'
                    : 'None of the herbs named on this page belongs to a current Board Duì Yào pair.'); ?><?php if ((int) $selectedguidenumber === 11): ?> <?php echo s($ises
                    ? 'Gan Sui–Gan Cao y Ba Dou–Qian Niu Zi son relaciones de incompatibilidad o combinación prohibida, no Duì Yào.'
                    : 'Gan Sui–Gan Cao and Ba Dou–Qian Niu Zi are incompatibility or prohibited-combination relationships, not Duì Yào.'); ?><?php endif; ?></span>
                <small><?php echo s($ises ? 'Abrir el índice Duì Yào vigente →' : 'Open the current Duì Yào index →'); ?></small>
            </a>
        </div>
    <?php endif; ?>
</section>
