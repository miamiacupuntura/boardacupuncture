#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
[[ -f "$TARGET_DIR/formula-ingredient-hierarchy.php" ]] || { echo 'ERROR: falta la página de fichas.' >&2; exit 1; }
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
cat > "$TEMP_DIR/formula-ingredient-classification.php" <<'PHP'
<?php
// Dedicated ingredient classification card; existing clinical cards are untouched.
require_once(__DIR__ . '/../../config.php');
require_login();
$ises = optional_param('lang', 'en', PARAM_ALPHA) === 'es';
$name = optional_param('formula', 'Si Jun Zi Tang', PARAM_TEXT);
$role = optional_param('role', 'jun', PARAM_ALPHA);
if (!in_array($role, ['jun', 'chen', 'zuo', 'shi'], true)) {
    $role = 'jun';
}
$origin = optional_param('origin', '', PARAM_ALPHANUMEXT);
$query = optional_param('q', '', PARAM_TEXT);
$params = ['formula' => $name, 'role' => $role];
if ($origin !== '') { $params['origin'] = $origin; }
if ($query !== '') { $params['q'] = $query; }
if ($ises) { $params['lang'] = 'es'; }
$pageurl = new moodle_url('/blocks/myprogress/formula-ingredient-classification.php', $params);
$languageparams = $params;
if ($ises) { unset($languageparams['lang']); } else { $languageparams['lang'] = 'es'; }
$languageurl = new moodle_url('/blocks/myprogress/formula-ingredient-classification.php', $languageparams);
$backparams = ['role' => $role];
if ($ises) { $backparams['lang'] = 'es'; }
if ($query !== '') { $backparams['q'] = $query; }
$backurl = new moodle_url('/blocks/myprogress/formula-ingredient-hierarchy.php', $backparams);
if (preg_match('/^ingredient-role-[0-9]+$/D', $origin)) { $backurl->set_anchor($origin); }
$clinicalparams = ['formula' => $name, 'sort' => 'az', 'from' => 'hierarchy'];
if ($ises) { $clinicalparams['lang'] = 'es'; }
$clinicalurl = new moodle_url('/blocks/myprogress/formula-list.php', $clinicalparams);
$bank = json_decode(file_get_contents(__DIR__ . '/formula-ingredient-classification-data.json'), true);
$source = json_decode(file_get_contents(__DIR__ . '/formulas-study-guide-data.json'), true);
if (!is_array($bank) || !is_array($source)) {
    throw new coding_exception('Ingredient classification data is unavailable.');
}
$profile = null;
foreach ($source['formulas'] ?? [] as $candidate) {
    if (($candidate['name'] ?? '') === $name) { $profile = $candidate; break; }
}
if ($profile === null || !isset($bank[$name])) {
    throw new moodle_exception('invalidparameter');
}
$labels = $ises
    ? ['jun'=>'Emperador (Jun)', 'chen'=>'Ministro (Chen)', 'zuo'=>'Asistente (Zuo)', 'shi'=>'Mensajero (Shi)']
    : ['jun'=>'Emperor (Jun)', 'chen'=>'Minister (Chen)', 'zuo'=>'Assistant (Zuo)', 'shi'=>'Envoy (Shi)'];
$rows = [];
foreach (['jun', 'chen', 'zuo', 'shi'] as $key) {
    foreach ($bank[$name][$key] ?? [] as $herb) {
        if (!isset($rows[$herb])) { $rows[$herb] = ['roles'=>[], 'function'=>'']; }
        $rows[$herb]['roles'][] = $labels[$key];
    }
}
$model = [
    'Ren Shen' => [
        'Replenishes Spleen Qi and provides the formula’s main tonifying action.',
        'Repone el Qi de Bazo y aporta la acción tonificante principal de la fórmula.',
    ],
    'Bai Zhu' => [
        'Strengthens the Spleen and dries Dampness, supporting Ren Shen’s Qi-tonifying action.',
        'Fortalece el Bazo y seca la Humedad, reforzando la tonificación del Qi que realiza Ren Shen.',
    ],
    'Fu Ling' => [
        'Drains Dampness and supports Spleen transformation, complementing the tonifying herbs.',
        'Drena la Humedad y apoya la transformación del Bazo, complementando a los ingredientes tonificantes.',
    ],
    'Zhi Gan Cao' => [
        'Harmonizes the ingredients and also supports middle-burner Qi.',
        'Armoniza los ingredientes y también apoya el Qi del calentador medio.',
    ],
];
$roles = (string)($profile[$ises ? 'ingredientRolesEs' : 'ingredientRoles'] ?? $profile['ingredientRoles'] ?? '');
$sentences = preg_split('/;|\.\s+/u', $roles, -1, PREG_SPLIT_NO_EMPTY);
foreach ($rows as $herb => &$row) {
    if ($name === 'Si Jun Zi Tang') {
        $row['function'] = $model[$herb][$ises ? 1 : 0];
        continue;
    }
    $alias = preg_replace('/^(Zhi|Sheng) Gan Cao$/', 'Gan Cao', $herb);
    $matches = [];
    foreach ($sentences as $sentence) {
        if (stripos($sentence, $alias) !== false) { $matches[] = trim($sentence); }
    }
    $row['function'] = $matches ? implode('; ', $matches) : $roles;
}
unset($row);
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title($name . ($ises ? ' — Clasificación de ingredientes' : ' — Ingredient classification'));
$PAGE->set_heading($ises ? 'Clasificación de ingredientes' : 'Ingredient classification');
$PAGE->set_cacheable(false);
echo $OUTPUT->header();
?>
<style>
.ingredient-classification{max-width:960px;margin:24px auto 48px;padding:0 14px;color:#102a43}.ingredient-classification__nav{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:18px}.ingredient-classification__link{display:inline-flex;align-items:center;min-height:44px;padding:9px 14px;border:1px solid #087c69;border-radius:8px;color:#006b65!important;background:#f2faf8;text-decoration:none!important;font-weight:700}.ingredient-classification__card{border:1px solid #c8d8e1;border-top:4px solid #ee8525;border-radius:12px;background:#fff;box-shadow:0 4px 14px rgba(23,50,77,.06);overflow:hidden}.ingredient-classification header{padding:22px;border-bottom:1px solid #d2dfe5}.ingredient-classification h2{margin:0 0 8px;font-size:24px}.ingredient-classification header p{margin:0;color:#087c69}.ingredient-classification table{width:100%;border-collapse:collapse;table-layout:fixed}.ingredient-classification th,.ingredient-classification td{text-align:left;padding:16px;vertical-align:top;border-bottom:1px solid #e0e9ed;overflow-wrap:anywhere;line-height:1.55}.ingredient-classification thead th{color:#006b65;background:#f4faf8}.ingredient-classification th:nth-child(1){width:21%}.ingredient-classification th:nth-child(2){width:25%}.ingredient-classification tbody th{color:#0f4c81}.ingredient-classification__note{padding:18px 22px;margin:0;color:#526b84;font-size:13px;line-height:1.6}.ingredient-classification__link:focus-visible{outline:3px solid #a06b00;outline-offset:3px}
@media(max-width:600px){.ingredient-classification table,.ingredient-classification tbody,.ingredient-classification tr,.ingredient-classification td,.ingredient-classification tbody th{display:block;width:auto}.ingredient-classification tbody th:nth-child(1){width:auto}.ingredient-classification thead{position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%)}.ingredient-classification tbody th{padding-bottom:6px;border-bottom:0}.ingredient-classification td{padding-top:6px}.ingredient-classification td:nth-child(2){color:#006b65;font-weight:700;border-bottom:0}.ingredient-classification td:last-child{padding-bottom:20px}}
</style>
<main class="ingredient-classification">
 <nav class="ingredient-classification__nav" aria-label="<?php echo $ises ? 'Navegación de la tarjeta' : 'Card navigation'; ?>">
  <a class="ingredient-classification__link" href="<?php echo $backurl->out(false); ?>"><?php echo $ises ? '← Regresar a la ficha del ingrediente' : '← Back to ingredient flashcard'; ?></a>
  <a class="ingredient-classification__link" hreflang="<?php echo $ises ? 'en' : 'es'; ?>" href="<?php echo $languageurl->out(false); ?>"><?php echo $ises ? 'English' : 'Español'; ?></a>
 </nav>
 <article class="ingredient-classification__card">
  <header><h2><?php echo s($name); ?></h2><p><?php echo $ises ? 'Clasificación de cada ingrediente dentro de esta fórmula' : 'Classification of each ingredient within this formula'; ?></p></header>
  <table>
   <thead><tr><th scope="col"><?php echo $ises ? 'Ingrediente' : 'Ingredient'; ?></th><th scope="col"><?php echo $ises ? 'Clasificación' : 'Classification'; ?></th><th scope="col"><?php echo $ises ? 'Función en esta fórmula' : 'Function in this formula'; ?></th></tr></thead>
   <tbody><?php foreach ($rows as $herb => $row) { ?>
    <tr><th scope="row"><?php echo s($herb); ?></th><td><?php echo s(implode(' / ', $row['roles'])); ?></td><td><?php echo s($row['function']); ?></td></tr>
   <?php } ?></tbody>
  </table>
  <p class="ingredient-classification__note"><?php echo $ises
    ? 'Clasificación de estudio: el papel depende de la fórmula y puede variar según la fuente. Un ingrediente puede cumplir más de una función.'
    : 'Study classification: the role depends on the formula and may vary by source. An ingredient may serve more than one function.'; ?></p>
 </article>
 <nav class="ingredient-classification__nav" style="margin-top:18px" aria-label="<?php echo $ises ? 'Continuar el estudio' : 'Continue studying'; ?>">
  <a class="ingredient-classification__link" href="<?php echo $backurl->out(false); ?>"><?php echo $ises ? '← Regresar a la ficha del ingrediente' : '← Back to ingredient flashcard'; ?></a>
  <a class="ingredient-classification__link" href="<?php echo $clinicalurl->out(false); ?>"><?php echo $ises ? 'Ver tarjeta clínica existente →' : 'View existing clinical card →'; ?></a>
 </nav>
</main>
<?php echo $OUTPUT->footer(); ?>
PHP
python3 - "$TARGET_DIR" "$TEMP_DIR" <<'PY'
import ast, json, re, sys
from pathlib import Path
target, temp = map(Path, sys.argv[1:])
page = (target / 'formula-ingredient-hierarchy.php').read_text()
marker = '// ingredient-classification-links-v1'
if marker in page:
    raise SystemExit('Los enlaces de ida y regreso ya están instalados; no se cambió nada.')
if '<!-- ingredient-role-flashcards-v1 -->' not in page:
    raise SystemExit('ERROR: primero debes instalar las fichas por función.')
match = re.search(r'const assignments=(\{.*?\n \});', page, re.S)
if not match:
    raise SystemExit('ERROR: no se encontró el mapa de clasificación esperado.')
mapping = ast.literal_eval(re.sub(r'([{,]\s*)(chen|zuo|shi)(\s*:)', r"\1'\2'\3", match[1]))
data = json.loads((target / 'formulas-study-guide-data.json').read_text())
profiles = {p['name']: p for p in data['formulas']}
if len(mapping) != 19 or any(name not in profiles for name in mapping) or 'Si Jun Zi Tang' not in profiles:
    raise SystemExit('ERROR: se esperaban las 19 fórmulas de las fichas actuales.')
for name, roles in mapping.items():
    roles['jun'] = [h.strip() for h in profiles[name]['kings'].split(',') if h.strip()]
mapping['Si Jun Zi Tang'] = {'jun':['Ren Shen'],'chen':['Bai Zhu'],'zuo':['Fu Ling'],'shi':['Zhi Gan Cao']}
(temp / 'formula-ingredient-classification-data.json').write_text(json.dumps(mapping, ensure_ascii=False, indent=2)+'\n')
old = "const link=node('a',c.profile.name);link.href=c.profile.url;"
new = """// ingredient-classification-links-v1
     const link=node('a',c.profile.name);
     const destination=new URL('formula-ingredient-classification.php',window.location.href);
     destination.search='';destination.hash='';
     destination.searchParams.set('formula',c.profile.name);
     destination.searchParams.set('role',active);
     destination.searchParams.set('origin',article.id);
     if(es)destination.searchParams.set('lang','es');
     const query=new URLSearchParams(window.location.search).get('q');
     if(query)destination.searchParams.set('q',query);
     link.href=destination.href;"""
if page.count(old) != 1 or page.count("let active='jun';") != 1:
    raise SystemExit('ERROR: la página fue modificada; se detuvo el cambio para conservarla.')
page = page.replace(old, new).replace("let active='jun';", "const requestedRole=new URLSearchParams(window.location.search).get('role');\n let active=Object.hasOwn(labels,requestedRole)?requestedRole:'jun';")
needle = "const article=node('article',null,'formula-hierarchy__card');"
if page.count(needle) != 1:
    raise SystemExit('ERROR: no se pudo identificar la ficha de origen.')
page = page.replace(needle, needle+"\n     article.id='ingredient-role-'+cards.indexOf(c);")
last = ' render();\n})();'
if page.count(last) != 1:
    raise SystemExit('ERROR: no se pudo identificar la inicialización de fichas.')
page = page.replace(last, """ render();
 const anchor=window.location.hash.slice(1);
 if(/^ingredient-role-[0-9]+$/.test(anchor)){
   const origin=document.getElementById(anchor);
   if(origin){
     const details=origin.querySelector('details');if(details)details.open=true;
     requestAnimationFrame(()=>origin.scrollIntoView({block:'center'}));
   }
 }
})();""")
(temp / 'formula-ingredient-hierarchy.php').write_text(page)
print('Preparadas tarjetas de clasificación bilingües y enlaces de ida y regreso.')
PY
FILES=(formula-ingredient-classification-data.json formula-ingredient-classification.php formula-ingredient-hierarchy.php)
php -l "$TEMP_DIR/formula-ingredient-classification.php"
php -l "$TEMP_DIR/formula-ingredient-hierarchy.php"
BACKUP="$BACKUP_ROOT/formula-classification-cards-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
for file in "${FILES[@]}"; do
    if [[ -e "$TARGET_DIR/$file" ]]; then cp -a "$TARGET_DIR/$file" "$BACKUP/$file"; else touch "$BACKUP/$file.absent"; fi
done
rollback() {
    local file
    for file in "${FILES[@]}"; do
        if [[ -f "$BACKUP/$file.absent" ]]; then rm -f -- "$TARGET_DIR/$file"; else cp -a "$BACKUP/$file" "$TARGET_DIR/$file"; fi
    done
    echo "ERROR: se restauraron los archivos. Respaldo: $BACKUP" >&2
}
trap 'rollback' ERR
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET_DIR/formula-ingredient-hierarchy.php")"
MODE="$(stat -c '%a' "$TARGET_DIR/formula-ingredient-hierarchy.php")"
for file in "${FILES[@]}"; do
    install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/$file" "$TARGET_DIR/$file"
done
trap - ERR
echo 'OK: nombre de fórmula → tarjeta de clasificación → ficha de origen.'
echo 'OK: español e inglés; regreso al mismo grupo, búsqueda e ingrediente.'
echo 'OK: modelo adicional Si Jun Zi Tang; su tarjeta clínica original no se modificó.'
echo "RESPALDO: $BACKUP"
