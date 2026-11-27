#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
[[ -f "$TARGET" ]] || { echo "ERROR: no existe $TARGET" >&2; exit 1; }
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$TEMP_DIR/page.php" <<'PY'
import sys
from pathlib import Path
source=Path(sys.argv[1]).read_text()
if 'id="question-bank-study-guide-back"' in source:
    raise SystemExit('El botón Study Guide ya está instalado; no se cambió nada.')
anchor='<nav class="question-bank__actions" aria-label="Question bank actions">'
if source.count(anchor)!=1 or "$displaylang = optional_param('lang'" not in source:
    raise SystemExit('ERROR: no se identificó la cabecera esperada; no se cambió nada.')
button='''<a id="question-bank-study-guide-back" class="question-bank__button" href="<?php echo (new moodle_url('/blocks/myprogress/study-guide.php', array_merge(['area' => 'herbology', 'view' => 'library'], $displaylang === 'es' ? ['lang' => 'es'] : [])))->out(false); ?>"><?php echo $displaylang === 'es' ? '← Guía de estudio' : '← Study Guide'; ?></a>'''
Path(sys.argv[2]).write_text(source.replace(anchor,anchor+button,1))
print('Preparado botón de regreso a Study Guide, conservando el idioma.')
PY
php -l "$TEMP_DIR/page.php"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/question-bank-study-guide-button-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/admin-herbology-question-bank.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/admin-herbology-question-bank.php" "$TARGET"
    echo 'ERROR: se restauró la página anterior.' >&2
    exit 1
fi
echo 'OK: botón ← Study Guide / ← Guía de estudio agregado a la cabecera.'
echo "RESPALDO: $BACKUP"
