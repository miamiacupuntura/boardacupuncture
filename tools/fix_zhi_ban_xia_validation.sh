#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
for tool in python3 php cp stat install mktemp date cmp; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$MOODLE_ROOT/blocks/myprogress/single-herb-card-index.json" "$TEMP_DIR/page.php" <<'PY'
from pathlib import Path
import json,re,sys
source=Path(sys.argv[1]).read_text()
roster=json.loads(Path(sys.argv[2]).read_text())
if not any(h.get('name')=='Ban Xia' and h.get('latin')=='Pinelliae Rhizoma' for h in roster.get('herbs',[])):
    raise SystemExit('ERROR: no coincide la identidad Ban Xia (Pinelliae Rhizoma) en el roster.')
match=re.search(r'\$guidelineherbaliases\s*=\s*\[(.*?)\n\];',source,re.S)
if not match:raise SystemExit('ERROR: no se identificó el mapa de validación; no se cambió nada.')
existing=re.search(r"'zhi ban xia'\s*=>\s*'([^']+)'",match[1])
if existing and existing[1]!='ban xia':raise SystemExit('ERROR: existe otra equivalencia; revisar antes de cambiarla.')
if not existing:
    addition="\n    // Roster identity only: Zhi Ban Xia is processed Ban Xia.\n    // Preserve the preparation name; do not infer a specific processing method.\n    'zhi ban xia' => 'ban xia',"
    source=source[:match.end(1)]+addition+source[match.end(1):]
Path(sys.argv[3]).write_text(source)
print('Comprobada identidad del roster: Ban Xia (Pinelliae Rhizoma).')
PY
php -l "$TEMP_DIR/page.php"
if cmp -s "$TEMP_DIR/page.php" "$TARGET"; then
    echo 'Sin cambios: Zhi Ban Xia ya está reconocido. Recarga la tarjeta.'
    exit 0
fi
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/zhi-ban-xia-validation-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/admin-herbology-question-bank.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/admin-herbology-question-bank.php" "$TARGET"
    echo 'ERROR: se restauró el archivo anterior.' >&2
    exit 1
fi
echo 'OK: Zhi Ban Xia reconocido como preparación de Ban Xia para comprobar el roster.'
echo 'OK: el nombre de preparación, las opciones y la respuesta C se conservan; no se publica nada.'
echo "RESPALDO: $BACKUP"
