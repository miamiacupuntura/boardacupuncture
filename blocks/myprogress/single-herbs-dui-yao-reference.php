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
$duiyaonumberwords = $ises
    ? [0 => 'cero', 1 => 'una', 2 => 'dos', 3 => 'tres', 4 => 'cuatro', 5 => 'cinco', 6 => 'seis', 7 => 'siete', 8 => 'ocho', 9 => 'nueve', 10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince', 16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve', 20 => 'veinte']
    : [0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty'];
$duiyaospeechclean = static function(string $value, bool $spanish): string {
    $value = strtr($value, [
        'ā' => 'a', 'á' => 'a', 'ǎ' => 'a', 'à' => 'a', 'Ā' => 'A', 'Á' => 'A', 'Ǎ' => 'A', 'À' => 'A',
        'ē' => 'e', 'é' => 'e', 'ě' => 'e', 'è' => 'e', 'Ē' => 'E', 'É' => 'E', 'Ě' => 'E', 'È' => 'E',
        'ī' => 'i', 'í' => 'i', 'ǐ' => 'i', 'ì' => 'i', 'Ī' => 'I', 'Í' => 'I', 'Ǐ' => 'I', 'Ì' => 'I',
        'ō' => 'o', 'ó' => 'o', 'ǒ' => 'o', 'ò' => 'o', 'Ō' => 'O', 'Ó' => 'O', 'Ǒ' => 'O', 'Ò' => 'O',
        'ū' => 'u', 'ú' => 'u', 'ǔ' => 'u', 'ù' => 'u', 'Ū' => 'U', 'Ú' => 'U', 'Ǔ' => 'U', 'Ù' => 'U',
        'ǖ' => 'u', 'ǘ' => 'u', 'ǚ' => 'u', 'ǜ' => 'u', 'ü' => 'u', 'Ǖ' => 'U', 'Ǘ' => 'U', 'Ǚ' => 'U', 'Ǜ' => 'U', 'Ü' => 'U',
    ]);
    $value = (string) preg_replace('/[\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{F900}-\x{FAFF}]/u', ' ', $value);
    $value = (string) preg_replace('/\s*\+\s*/u', $spanish ? ' y ' : ' and ', $value);
    $value = (string) preg_replace('/\s*\/\s*/u', $spanish ? ' y ' : ' and ', $value);
    $value = str_replace(['→', '↔', '·', '—', '–', '*', '＊'], [', ', ', ', ', ', ', ', ', ', '', ''], $value);
    $value = (string) preg_replace('/\s+([,.;:!?])/u', '$1', $value);
    return trim((string) preg_replace('/\s+/u', ' ', $value));
};
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
        <?php
        $duiyaocount = count($duiyaomatches);
        $duiyaocountword = $duiyaonumberwords[$duiyaocount] ?? (string) $duiyaocount;
        $duiyaovisiblecount = $ises
            ? $duiyaocount . ($duiyaocount === 1
                ? ' pareja vigente contiene al menos una hierba nombrada en esta página, incluidas sus referencias cruzadas.'
                : ' parejas vigentes contienen al menos una hierba nombrada en esta página, incluidas sus referencias cruzadas.')
            : $duiyaocount . ($duiyaocount === 1
                ? ' current pair contains at least one herb named on this page, including its cross-category references.'
                : ' current pairs contain at least one herb named on this page, including their cross-category references.');
        $duiyaospokencount = ucfirst($duiyaocountword) . ($ises
            ? ($duiyaocount === 1
                ? ' pareja vigente contiene al menos una hierba nombrada en esta página, incluidas sus referencias cruzadas.'
                : ' parejas vigentes contienen al menos una hierba nombrada en esta página, incluidas sus referencias cruzadas.')
            : ($duiyaocount === 1
                ? ' current pair contains at least one herb named on this page, including its cross-category references.'
                : ' current pairs contain at least one herb named on this page, including their cross-category references.'));
        ?>
        <p class="single-herbs-guide__section-lead" data-read-aloud-text="<?php echo s($duiyaospokencount); ?>"><?php echo s($duiyaovisiblecount); ?></p>
        <div class="single-herbs-guide__comparisons">
            <?php foreach ($duiyaomatches as $duiyaoindex => $duiyaopair): ?>
                <?php
                $duiyaopath = '/study-guide/comparisons/' . $duiyaopair['slug'] . ($ises ? '-es' : '') . '.html';
                $duiyaourl = new moodle_url($duiyaopath, [
                    'from' => 'single-herbs',
                    'focus' => (string) $selected['number'],
                ]);
                $duiyaoaction = (string) ($duiyaopair['action'][$ises ? 'es' : 'en'] ?? '');
                $duiyaoformulas = (string) ($duiyaopair['formulas'][$ises ? 'es' : 'en'] ?? '');
                $duiyaopairnumber = $duiyaoindex + 1;
                $duiyaopairnumberword = $duiyaonumberwords[$duiyaopairnumber] ?? (string) $duiyaopairnumber;
                $duiyaospokentitle = rtrim($duiyaospeechclean((string) $duiyaopair['title'], $ises), " \t\n\r\0\x0B.!?;:");
                $duiyaospokenaction = rtrim($duiyaospeechclean($duiyaoaction, $ises), " \t\n\r\0\x0B.!?;:");
                $duiyaospokenformulas = rtrim($duiyaospeechclean($duiyaoformulas, $ises), " \t\n\r\0\x0B.!?;:");
                $duiyaocardnarration = ($ises ? 'Pareja número ' : 'Pair number ')
                    . $duiyaopairnumberword . '. ' . $duiyaospokentitle . '. '
                    . ($ises ? 'Acción conjunta: ' : 'Combined action: ') . $duiyaospokenaction . '.';
                if ($duiyaospokenformulas !== '') {
                    $duiyaocardnarration .= ' ' . ($ises ? 'Fórmulas relacionadas: ' : 'Related formulas: ')
                        . $duiyaospokenformulas . '.';
                }
                ?>
                <a class="single-herbs-guide__comparison" data-read-aloud-text="<?php echo s($duiyaocardnarration); ?>" data-dui-yao-pair="<?php echo (int) ($duiyaopair['number'] ?? 0); ?>" href="<?php echo $duiyaourl->out(false); ?>">
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
