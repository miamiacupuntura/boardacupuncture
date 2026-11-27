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
from pathlib import Path
import re,sys
source=Path(sys.argv[1]).read_text()
match=re.search(r'\$guidelineherbaliases\s*=\s*\[(.*?)\n\];',source,re.S)
if not match:raise SystemExit('ERROR: no se identificó el mapa de validación; no se cambió nada.')
aliases=['sheng da huang','jiu da huang','chao da huang','da huang tan','tan da huang']
missing=[]
for alias in aliases:
    existing=re.search(r"'"+re.escape(alias)+r"'\s*=>\s*'([^']+)'",match[1])
    if existing and existing[1]!='da huang':
        raise SystemExit('ERROR: existe una equivalencia distinta para '+alias+'; revisar antes de modificar.')
    if not existing:missing.append(alias)
if missing:
    additions='\n    // Da Huang preparations: canonical substance for roster validation only.\n    // Keep preparation names in questions; raw, wine-prepared, toasted and\n    // carbonized forms have different actions and are not interchangeable.\n'
    additions+=''.join("    '"+alias+"' => 'da huang',\n" for alias in missing)
    end=match.end(1)
    source=source[:end]+additions.rstrip('\n')+source[end:]
    print('Preparaciones reconocidas:',', '.join(missing))
else:
    print('OK: todas las preparaciones ya están reconocidas.')
Path(sys.argv[2]).write_text(source)
PY
php -l "$TEMP_DIR/page.php"
if cmp -s "$TEMP_DIR/page.php" "$TARGET"; then
    echo 'Sin cambios: puedes volver a intentar crear la pregunta.'
    exit 0
fi
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/da-huang-preparation-validation-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/admin-herbology-question-bank.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/admin-herbology-question-bank.php" "$TARGET"
    echo 'ERROR: se restauró el archivo anterior.' >&2
    exit 1
fi
echo 'OK: Sheng Da Huang, Jiu Da Huang, Chao Da Huang y Da Huang Tan reconocidos para validar el roster.'
echo 'OK: también se reconoce la variante de nombre Tan Da Huang.'
echo 'OK: se conservan las preparaciones en las preguntas; no se modifican preguntas ni exámenes.'
echo "RESPALDO: $BACKUP"
