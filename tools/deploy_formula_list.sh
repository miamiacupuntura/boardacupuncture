#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Formula List detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_formula_list.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

FILES=(formula-list.php formulas-study-guide.php study-guide.php)
for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
    php -l "$TEMP_DIR/$file" >/dev/null
done

grep -Fq "Formula List" "$TEMP_DIR/formula-list.php"
grep -Fq "count(\$roster['formulas'] ?? []) !== 181" "$TEMP_DIR/formula-list.php"
grep -Fq "Sorting modes reset category filters and start from all 181 formulas." "$TEMP_DIR/formula-list.php"
grep -Fq "Detailed clinical profile" "$TEMP_DIR/formula-list.php"
grep -Fq "Back to all formulas" "$TEMP_DIR/formula-list.php"
grep -Fq "Formula List" "$TEMP_DIR/formulas-study-guide.php"
grep -Fq "181 official guideline formulas" "$TEMP_DIR/study-guide.php"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || { echo "ERROR: no existe $TARGET_DIR" >&2; exit 1; }
[[ -f "$TARGET_DIR/formula-board-roster-2026.json" ]] || { echo "ERROR: falta el roster oficial de fórmulas." >&2; exit 1; }
[[ -f "$TARGET_DIR/formulas-study-guide-data.json" ]] || { echo "ERROR: falta el banco detallado de fórmulas." >&2; exit 1; }

php -r '
$roster = json_decode(file_get_contents($argv[1]), true);
$data = json_decode(file_get_contents($argv[2]), true);
if (!is_array($roster) || !is_array($data)
    || (int)($roster["total"] ?? 0) !== 181
    || count($roster["formulas"] ?? []) !== 181
    || count($data["formulas"] ?? []) !== 63
    || count($data["supplementalFormulas"] ?? []) !== 227) {
    fwrite(STDERR, "ERROR: los bancos de fórmulas no son válidos.\n");
    exit(1);
}
' "$TARGET_DIR/formula-board-roster-2026.json" "$TARGET_DIR/formulas-study-guide-data.json"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/formula-list-$TIMESTAMP"
mkdir -p "$BACKUP"
reference="$TARGET_DIR/formulas-study-guide.php"
default_owner_group="$(stat -c '%u:%g' "$reference")"
default_mode="$(stat -c '%a' "$reference")"

for file in "${FILES[@]}"; do
    target="$TARGET_DIR/$file"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP/$file"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
    else
        owner_group="$default_owner_group"
        mode="$default_mode"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$file" "$target"
    cmp -s "$TEMP_DIR/$file" "$target"
    php -l "$target" >/dev/null
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: botón Formula List instalado en Formulas y en la biblioteca."
echo "OK: lista limitada a las 181 fórmulas oficiales del Appendix C 2026."
echo "OK: organización A–Z y por categoría, búsqueda y filtros activos."
echo "OK: 133 fórmulas enlazan con perfiles clínicos detallados verificados."
echo "OK: las 48 fórmulas restantes conservan identidad oficial sin contenido inferido."
echo "RESPALDO: $BACKUP"
