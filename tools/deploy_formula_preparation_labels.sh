#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET_DIR" "$TEMP_DIR" <<'PY'
import sys
from pathlib import Path
target,temp=map(Path,sys.argv[1:])
page=(target/'formula-ingredient-hierarchy.php').read_text()
card=(target/'formula-ingredient-classification.php').read_text()
marker='// familiar-gan-cao-label-v1'
if marker in page:
    raise SystemExit('El nombre Gan Cao y las notas resaltadas ya están instalados; no se cambió nada.')
if '// formula-differential-questions-v1' not in page:
    raise SystemExit('ERROR: primero deben estar instaladas las pistas diferenciales.')
needle='const question=es'
if page.count(needle)!=1 or page.count("node('h3',c.herb)")!=1 or page.count("'formula-hierarchy__source'));\n     const answer=node('details');")!=1:
    raise SystemExit('ERROR: la página no coincide con la versión esperada; no se cambió nada.')
page=page.replace(needle,marker+"\n     const displayHerb=c.herb==='Sheng Gan Cao'?'Gan Cao':c.herb;\n     "+needle)
page=page.replace('${c.herb}','${displayHerb}').replace("node('h3',c.herb)","node('h3',displayHerb)")
page=page.replace("'formula-hierarchy__source'));\n     const answer=node('details');","'formula-hierarchy__preparation'));\n     const answer=node('details');")
# Keep the canonical preparation as an internal lookup key; use the familiar
# name on the front. Never remove the Zhi prefix from honey-prepared licorice.
old="const preparation=preparationClues[c.herb];"
new="""const preparation=c.herb==='Sheng Gan Cao'
       ? ['Gan Cao without honey preparation: favors Heat clearing and throat benefit while also harmonizing. Also called Sheng Gan Cao.',
          'Gan Cao sin preparar con miel: favorece aclarar Calor y beneficiar la garganta, además de armonizar. También se denomina Sheng Gan Cao.']
       : preparationClues[c.herb];"""
if page.count(old)!=1:raise SystemExit('ERROR: no se encontró la nota de preparación.')
page=page.replace(old,new)
style='''
<style>
#ingredient-role-deck .formula-hierarchy__preparation{margin:0;padding:10px 12px;border-left:4px solid #d58a00;border-radius:6px;background:#fff8e6;color:#57401b;font-size:15px;font-weight:600;line-height:1.6}
</style>
'''
if page.count('<script>')!=1:raise SystemExit('ERROR: no se identificó el bloque de fichas.')
page=page.replace('<script>',style+'<script>',1)
old="<?php echo s($herb); ?>"
if card.count(old)!=1:raise SystemExit('ERROR: no se identificó el nombre en la tarjeta de clasificación.')
card=card.replace(old,"<?php echo s($herb === 'Sheng Gan Cao' ? 'Gan Cao' : $herb); ?>")
note='''
  <?php if (isset($rows['Sheng Gan Cao'])) { ?>
  <p class="ingredient-classification__note"><?php echo $ises
    ? 'Gan Cao es el nombre habitual usado aquí para el regaliz sin preparar con miel; también puede aparecer como Sheng Gan Cao. Se distingue de Zhi Gan Cao, preparado con miel.'
    : 'Gan Cao is the familiar name used here for licorice without honey preparation; it may also appear as Sheng Gan Cao. It is distinct from honey-prepared Zhi Gan Cao.'; ?></p>
  <?php } ?>
'''
if card.count(' </article>')!=1:raise SystemExit('ERROR: no se identificó la tarjeta de clasificación.')
card=card.replace(' </article>',note+' </article>')
(temp/'formula-ingredient-hierarchy.php').write_text(page)
(temp/'formula-ingredient-classification.php').write_text(card)
print('Preparadas notas más grandes y resaltadas; Gan Cao como nombre principal y Sheng Gan Cao como aclaración secundaria.')
PY
FILES=(formula-ingredient-classification.php formula-ingredient-hierarchy.php)
for file in "${FILES[@]}"; do php -l "$TEMP_DIR/$file"; done
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/formula-preparation-labels-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
for file in "${FILES[@]}"; do cp -a "$TARGET_DIR/$file" "$BACKUP/$file"; done
rollback() {
    local file
    for file in "${FILES[@]}"; do cp -a "$BACKUP/$file" "$TARGET_DIR/$file"; done
    echo "ERROR: se restauraron los archivos. Respaldo: $BACKUP" >&2
}
trap 'rollback' ERR
for file in "${FILES[@]}"; do
    OWNER_GROUP="$(stat -c '%u:%g' "$TARGET_DIR/$file")"
    MODE="$(stat -c '%a' "$TARGET_DIR/$file")"
    install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/$file" "$TARGET_DIR/$file"
done
trap - ERR
echo 'OK: preparación con letra de 15 px, resaltada y con fondo suave.'
echo 'OK: Gan Cao en preguntas y tarjetas; Sheng Gan Cao queda como aclaración secundaria.'
echo 'OK: Zhi Gan Cao conserva su nombre y la distinción de preparación con miel.'
echo 'OK: español e inglés; pistas y enlaces de ida y regreso conservados.'
echo "RESPALDO: $BACKUP"
