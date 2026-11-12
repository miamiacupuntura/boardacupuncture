<?php
// Vector Chief Herbs map for the 63 official guideline formulas.

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/herbology-library-navigation.php');

require_login();

$displaylang = optional_param('lang', 'en', PARAM_ALPHA);
$displaylang = $displaylang === 'es' ? 'es' : 'en';
$ises = $displaylang === 'es';

$pageurl = new moodle_url('/blocks/myprogress/formula-chief-herbs-map.php', $ises ? ['lang' => 'es'] : []);
$languageurl = new moodle_url('/blocks/myprogress/formula-chief-herbs-map.php', $ises ? [] : ['lang' => 'es']);
$guideparams = ['area' => 'herbology', 'view' => 'library'];
if ($ises) {
    $guideparams['lang'] = 'es';
}
$guideurl = new moodle_url('/blocks/myprogress/study-guide.php', $guideparams);

$datafile = __DIR__ . '/formulas-study-guide-data.json';
$rosterfile = __DIR__ . '/formula-board-roster-2026.json';
$data = is_readable($datafile) ? json_decode(file_get_contents($datafile), true) : null;
$roster = is_readable($rosterfile) ? json_decode(file_get_contents($rosterfile), true) : null;
if (!is_array($data) || !is_array($roster)
        || count($data['formulas'] ?? []) !== 63
        || count($roster['formulas'] ?? []) !== 181) {
    throw new coding_exception('Chief Herbs formula map data is unavailable.');
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
$groups = [];
$grouporder = [];
$seen = [];
foreach ($data['formulas'] as $profile) {
    $number = (int)($profile['number'] ?? 0);
    $sourcename = trim((string)($profile['name'] ?? ''));
    $sourcekey = $normalize($sourcename);
    $officialkey = $aliases[$sourcekey] ?? $sourcekey;
    $chief = trim((string)($profile['kings'] ?? ''));
    $categorykey = trim((string)($profile['categoryKey'] ?? ''));
    $category = trim((string)($profile[$ises ? 'categoryEs' : 'category'] ?? $profile['category'] ?? ''));
    if ($number < 1 || $number > 63 || isset($seen[$number]) || !isset($official[$officialkey])
            || $chief === '' || $categorykey === '' || $category === '') {
        throw new coding_exception('Invalid Chief Herbs map row #' . $number . '.');
    }
    $seen[$number] = true;
    if (!isset($groups[$categorykey])) {
        $groups[$categorykey] = ['title' => $category, 'rows' => []];
        $grouporder[] = $categorykey;
    }
    $officialname = $official[$officialkey];
    $params = ['formula' => $officialname, 'sort' => 'az', 'from' => 'chiefmap'];
    if ($ises) {
        $params['lang'] = 'es';
    }
    $groups[$categorykey]['rows'][] = [
        'number' => $number,
        'name' => $officialname,
        'sourceName' => $sourcename,
        'chief' => $chief,
        'url' => new moodle_url('/blocks/myprogress/formula-list.php', $params),
    ];
}
if (count($seen) !== 63 || count($grouporder) !== 14) {
    throw new coding_exception('Chief Herbs map must contain 63 formulas in 14 categories.');
}
$orderedgroups = [];
foreach ($grouporder as $key) {
    $orderedgroups[] = $groups[$key];
}
$leftgroups = array_slice($orderedgroups, 0, 6);
$rightgroups = array_slice($orderedgroups, 6);

$panelheight = static fn(array $group): int => 60 + (count($group['rows']) * 29);
$sideheight = static function(array $side) use ($panelheight): int {
    $height = 0;
    foreach ($side as $index => $group) {
        $height += $panelheight($group);
        if ($index + 1 < count($side)) {
            $height += 18;
        }
    }
    return $height;
};
$leftheight = $sideheight($leftgroups);
$rightheight = $sideheight($rightgroups);
$contentheight = max($leftheight, $rightheight);
$canvasheight = $contentheight + 230;
$huby = 150 + ($contentheight / 2);
$layoutside = static function(array $side, int $sideheight, int $contentheight, float $x) use ($panelheight): array {
    $y = 150 + (($contentheight - $sideheight) / 2);
    $layout = [];
    foreach ($side as $group) {
        $height = $panelheight($group);
        $layout[] = ['x' => $x, 'y' => $y, 'height' => $height, 'group' => $group];
        $y += $height + 18;
    }
    return $layout;
};
$leftlayout = $layoutside($leftgroups, $leftheight, $contentheight, 30);
$rightlayout = $layoutside($rightgroups, $rightheight, $contentheight, 1150);

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($ises ? 'Mapa de fórmulas y hierbas Chief' : 'Chief Herbs Formula Map');
$PAGE->set_heading($ises ? 'Mapa de fórmulas y hierbas Chief' : 'Chief Herbs Formula Map');
$PAGE->set_cacheable(false);
$PAGE->requires->js_init_code(<<<'JS'
const downloadButton=document.querySelector('[data-chief-map-download]');
const zoomIn=document.querySelector('[data-chief-map-zoom-in]');
const zoomOut=document.querySelector('[data-chief-map-zoom-out]');
const zoomReset=document.querySelector('[data-chief-map-zoom-reset]');
const fullscreen=document.querySelector('[data-chief-map-fullscreen]');
const canvas=document.querySelector('.chief-map__canvas');
const svg=document.querySelector('[data-chief-map-svg]');
let zoom=1;
function applyZoom(){
  if(!svg)return;
  svg.style.width=Math.round(zoom*100)+'%';
  svg.style.maxWidth='none';
  if(zoomReset)zoomReset.textContent=Math.round(zoom*100)+'%';
}
if(downloadButton&&svg){
  downloadButton.addEventListener('click',function(){
    const copy=svg.cloneNode(true);
    copy.removeAttribute('style');
    copy.setAttribute('xmlns','http://www.w3.org/2000/svg');
    const blob=new Blob([new XMLSerializer().serializeToString(copy)],{type:'image/svg+xml;charset=utf-8'});
    const url=URL.createObjectURL(blob);
    const link=document.createElement('a');
    link.href=url;link.download='chief-herbs-formula-map.svg';link.click();
    setTimeout(function(){URL.revokeObjectURL(url);},1000);
  });
}
if(zoomIn)zoomIn.addEventListener('click',function(){zoom=Math.min(2.5,zoom+0.2);applyZoom();});
if(zoomOut)zoomOut.addEventListener('click',function(){zoom=Math.max(1,zoom-0.2);applyZoom();});
if(zoomReset)zoomReset.addEventListener('click',function(){zoom=1;applyZoom();if(canvas)canvas.scrollTo({top:0,left:0,behavior:'smooth'});});
if(fullscreen&&canvas)fullscreen.addEventListener('click',function(){
  if(document.fullscreenElement){document.exitFullscreen();return;}
  if(canvas.requestFullscreen)canvas.requestFullscreen();
});
if(svg)svg.addEventListener('dblclick',function(){zoom=Math.min(2.5,zoom+0.2);applyZoom();});
JS);

echo $OUTPUT->header();
?>
<style>
.chief-map{width:min(1460px,calc(100% - 24px));margin:24px auto 58px;color:#102a43}
.chief-map__hero{display:flex;align-items:flex-start;justify-content:space-between;gap:20px;margin-bottom:18px;padding:24px;border:1px solid #bfd7d4;border-top:5px solid #087c69;border-radius:14px;background:#fff}
.chief-map__eyebrow{margin:0 0 6px;color:#087c69;font-size:12px;font-weight:900;letter-spacing:.09em;text-transform:uppercase}
.chief-map h2{margin:0 0 8px;font-size:clamp(29px,4vw,42px)}
.chief-map__lead{max-width:850px;margin:0;color:#526b84;line-height:1.55}
.chief-map__actions{display:flex;flex-wrap:wrap;gap:9px}.chief-map__button{display:inline-flex;min-height:42px;align-items:center;justify-content:center;padding:8px 14px;border:1px solid #b9c9d5;border-radius:9px;background:#fff;color:#0f4c81!important;font-weight:850;text-decoration:none!important;cursor:pointer}.chief-map__button:hover{background:#eef7f5}.chief-map__zoom-value{min-width:68px}.chief-map__canvas:fullscreen{padding:14px;background:#fffaf1}.chief-map__canvas:fullscreen svg{min-width:1180px}
.chief-map__legend{display:flex;flex-wrap:wrap;gap:14px;margin:0 0 14px;padding:12px 15px;border:1px solid #c7ddd9;border-radius:10px;background:#f8fcfb;color:#36566c;font-weight:750}.chief-map__legend span{display:inline-flex;align-items:center;gap:7px}.chief-map__swatch{width:18px;height:12px;border-radius:999px;background:#ffedd5;border:1px solid #fb923c}.chief-map__swatch.is-category{background:#0f766e;border-color:#0f766e}
.chief-map__canvas{overflow:auto;border:1px solid #bfd0d9;border-radius:14px;background:#fffaf1;box-shadow:0 7px 22px rgba(23,50,77,.08)}.chief-map__canvas svg{display:block;min-width:1180px;width:100%;height:auto}
.chief-map__note{margin:14px 0 0;color:#526b84;font-size:13px;line-height:1.5}
@media(max-width:760px){.chief-map__hero{display:block}.chief-map__actions{margin-top:16px}}
@media print{header,footer,.chief-map__hero,.chief-map__legend,.chief-map__note{display:none!important}.chief-map{width:100%;margin:0}.chief-map__canvas{border:0;box-shadow:none}.chief-map__canvas svg{min-width:0}}
</style>
<main class="chief-map">
    <header class="chief-map__hero">
        <div>
            <p class="chief-map__eyebrow"><?php echo $ises ? 'GUIDELINE 2026 · 63 FÓRMULAS OFICIALES' : '2026 GUIDELINE · 63 OFFICIAL FORMULAS'; ?></p>
            <h2><?php echo $ises ? 'Mapa de fórmulas y hierbas Chief' : 'Chief Herbs Formula Map'; ?></h2>
            <p class="chief-map__lead"><?php echo $ises
                ? 'Mapa vectorial de las 63 fórmulas oficiales, organizado por categoría. Cada fórmula enlaza su tarjeta y su hierba Chief aparece resaltada.'
                : 'A vector map of the 63 official formulas organized by category. Every formula links to its study card, with its Chief herb highlighted.'; ?></p>
        </div>
        <div class="chief-map__actions">
            <a class="chief-map__button" href="<?php echo $guideurl->out(false); ?>"><?php echo $ises ? '← Guía principal' : '← Main Study Guide'; ?></a>
            <button class="chief-map__button" type="button" data-chief-map-zoom-out aria-label="<?php echo $ises ? 'Reducir mapa' : 'Zoom out'; ?>">−</button>
            <button class="chief-map__button chief-map__zoom-value" type="button" data-chief-map-zoom-reset aria-label="<?php echo $ises ? 'Restablecer tamaño' : 'Reset zoom'; ?>">100%</button>
            <button class="chief-map__button" type="button" data-chief-map-zoom-in aria-label="<?php echo $ises ? 'Agrandar mapa' : 'Zoom in'; ?>">＋</button>
            <button class="chief-map__button" type="button" data-chief-map-fullscreen><?php echo $ises ? 'Pantalla completa' : 'Full screen'; ?></button>
            <button class="chief-map__button" type="button" data-chief-map-download><?php echo $ises ? 'Descargar SVG' : 'Download SVG'; ?></button>
            <a class="chief-map__button" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
        </div>
    </header>
    <div class="chief-map__legend">
        <span><i class="chief-map__swatch is-category"></i><?php echo $ises ? 'Categoría de fórmulas' : 'Formula category'; ?></span>
        <span><i class="chief-map__swatch"></i><?php echo $ises ? 'Hierba Chief' : 'Chief herb'; ?></span>
        <span><?php echo $ises ? 'Selecciona el nombre de una fórmula para abrir su tarjeta.' : 'Select a formula name to open its study card.'; ?></span>
    </div>
    <div class="chief-map__canvas">
        <svg data-chief-map-svg viewBox="0 0 1900 <?php echo (int)$canvasheight; ?>" role="img" aria-labelledby="chief-map-title chief-map-desc">
            <title id="chief-map-title"><?php echo $ises ? 'Mapa de 63 fórmulas y sus hierbas Chief' : 'Map of 63 formulas and their Chief herbs'; ?></title>
            <desc id="chief-map-desc"><?php echo $ises ? 'Diagrama radial organizado en catorce categorías.' : 'Radial diagram organized into fourteen formula categories.'; ?></desc>
            <rect width="1900" height="<?php echo (int)$canvasheight; ?>" fill="#fffaf1"/>
            <text x="950" y="48" text-anchor="middle" fill="#102a43" font-family="Arial,sans-serif" font-size="31" font-weight="800"><?php echo $ises ? 'FÓRMULAS CHINAS · MAPA DE HIERBAS CHIEF' : 'CHINESE FORMULAS · CHIEF HERBS MAP'; ?></text>
            <text x="950" y="78" text-anchor="middle" fill="#0f766e" font-family="Arial,sans-serif" font-size="15" font-weight="700" letter-spacing="4"><?php echo $ises ? '63 FÓRMULAS OFICIALES DEL GUIDELINE' : '63 OFFICIAL GUIDELINE FORMULAS'; ?></text>

            <g fill="none" stroke="#123b5d" stroke-width="4" stroke-linecap="round">
                <?php foreach ($leftlayout as $panel) { $mid = $panel['y'] + ($panel['height'] / 2); ?>
                    <path d="M 750 <?php echo $mid; ?> C 820 <?php echo $mid; ?>, 810 <?php echo $huby; ?>, 825 <?php echo $huby; ?>"/>
                <?php } ?>
                <?php foreach ($rightlayout as $panel) { $mid = $panel['y'] + ($panel['height'] / 2); ?>
                    <path d="M 1150 <?php echo $mid; ?> C 1080 <?php echo $mid; ?>, 1090 <?php echo $huby; ?>, 1075 <?php echo $huby; ?>"/>
                <?php } ?>
            </g>

            <?php
            $renderpanel = static function(array $panel) use ($ises): void {
                $x = $panel['x'];
                $y = $panel['y'];
                $height = $panel['height'];
                $group = $panel['group'];
                $width = 720;
            ?>
                <g>
                    <rect x="<?php echo $x; ?>" y="<?php echo $y; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" rx="11" fill="#ffffff" stroke="#0f766e" stroke-width="2"/>
                    <path d="M <?php echo $x + 11; ?> <?php echo $y; ?> H <?php echo $x + $width - 11; ?> Q <?php echo $x + $width; ?> <?php echo $y; ?> <?php echo $x + $width; ?> <?php echo $y + 11; ?> V <?php echo $y + 35; ?> H <?php echo $x; ?> V <?php echo $y + 11; ?> Q <?php echo $x; ?> <?php echo $y; ?> <?php echo $x + 11; ?> <?php echo $y; ?> Z" fill="#0f766e"/>
                    <text x="<?php echo $x + 16; ?>" y="<?php echo $y + 24; ?>" fill="#fff" font-family="Arial,sans-serif" font-size="16" font-weight="800"><?php echo s($group['title']); ?></text>
                    <g fill="#36566c" font-family="Arial,sans-serif" font-size="11" font-weight="800">
                        <text x="<?php echo $x + 14; ?>" y="<?php echo $y + 53; ?>">#</text>
                        <text x="<?php echo $x + 52; ?>" y="<?php echo $y + 53; ?>"><?php echo $ises ? 'FÓRMULA' : 'FORMULA'; ?></text>
                        <text x="<?php echo $x + 410; ?>" y="<?php echo $y + 53; ?>"><?php echo $ises ? 'HIERBA CHIEF' : 'CHIEF HERB'; ?></text>
                    </g>
                    <?php foreach ($group['rows'] as $index => $row) {
                        $rowy = $y + 60 + ($index * 29);
                        $chiefwidth = min(292, max(82, (core_text::strlen($row['chief']) * 7) + 20));
                    ?>
                        <rect x="<?php echo $x + 1; ?>" y="<?php echo $rowy; ?>" width="<?php echo $width - 2; ?>" height="29" fill="<?php echo $index % 2 ? '#f7fbfa' : '#ffffff'; ?>"/>
                        <line x1="<?php echo $x + 1; ?>" y1="<?php echo $rowy; ?>" x2="<?php echo $x + $width - 1; ?>" y2="<?php echo $rowy; ?>" stroke="#d8e4e4"/>
                        <text x="<?php echo $x + 14; ?>" y="<?php echo $rowy + 19; ?>" fill="#526b84" font-family="Arial,sans-serif" font-size="12" font-weight="700"><?php echo $row['number']; ?></text>
                        <a href="<?php echo s($row['url']->out(false)); ?>" target="_top">
                            <text x="<?php echo $x + 52; ?>" y="<?php echo $rowy + 19; ?>" fill="#0f4c81" font-family="Arial,sans-serif" font-size="12.5" font-weight="800" text-decoration="underline"><?php echo s($row['name']); ?></text>
                        </a>
                        <rect x="<?php echo $x + 400; ?>" y="<?php echo $rowy + 5; ?>" width="<?php echo $chiefwidth; ?>" height="20" rx="10" fill="#ffedd5" stroke="#fdba74"/>
                        <text x="<?php echo $x + 410; ?>" y="<?php echo $rowy + 19; ?>" fill="#b45309" font-family="Arial,sans-serif" font-size="11.5" font-weight="800"><?php echo s($row['chief']); ?></text>
                    <?php } ?>
                </g>
            <?php
            };
            foreach ($leftlayout as $panel) {
                $renderpanel($panel);
            }
            foreach ($rightlayout as $panel) {
                $renderpanel($panel);
            }
            ?>

            <g>
                <rect x="825" y="<?php echo $huby - 48; ?>" width="250" height="96" rx="28" fill="#ecfdf5" stroke="#0f766e" stroke-width="5"/>
                <text x="950" y="<?php echo $huby - 6; ?>" text-anchor="middle" fill="#102a43" font-family="Arial,sans-serif" font-size="25" font-weight="900"><?php echo $ises ? 'HIERBAS' : 'CHIEF'; ?></text>
                <text x="950" y="<?php echo $huby + 27; ?>" text-anchor="middle" fill="#102a43" font-family="Arial,sans-serif" font-size="25" font-weight="900"><?php echo $ises ? 'CHIEF' : 'HERBS'; ?></text>
            </g>
            <text x="950" y="<?php echo $canvasheight - 30; ?>" text-anchor="middle" fill="#64748b" font-family="Arial,sans-serif" font-size="12">CA Formulas · 2026 Guideline · 63 verified formulas</text>
        </svg>
    </div>
    <p class="chief-map__note"><?php echo $ises
        ? 'Este mapa se genera directamente del banco verificado de 63 fórmulas; no incluye fórmulas suplementarias fuera del guideline.'
        : 'This map is generated directly from the verified 63-formula bank; supplemental formulas outside the guideline are excluded.'; ?></p>
</main>
<?php
echo $OUTPUT->footer();
