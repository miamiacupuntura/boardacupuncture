#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Chief Herbs Property detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_DIR="${STUDY_NOTE_DIR:-}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_chief_herbs_property.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp readlink tr wc dirname date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" -o "$TEMP_DIR/study-guide.php"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/chief-herbs-property.html" -o "$TEMP_DIR/chief-herbs-property.html"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/chief-herbs-property-es.html" -o "$TEMP_DIR/chief-herbs-property-es.html"

php -l "$TEMP_DIR/study-guide.php" >/dev/null
grep -Fq "Chief Herbs Property" "$TEMP_DIR/study-guide.php"
grep -Fq "/study-guide/comparisons/chief-herbs-property.html" "$TEMP_DIR/study-guide.php"
grep -Fq "AVAILABLE · 60 CHIEF HERBS" "$TEMP_DIR/study-guide.php"
grep -Fq "Property of Chief Herbs" "$TEMP_DIR/chief-herbs-property.html"
grep -Fq "Propiedades de las hierbas principales" "$TEMP_DIR/chief-herbs-property-es.html"
grep -Fq "data-chief-search" "$TEMP_DIR/chief-herbs-property.html"
[[ "$(grep -o 'data-chief-row' "$TEMP_DIR/chief-herbs-property.html" | wc -l)" -eq 60 ]]
[[ "$(grep -o 'data-chief-row' "$TEMP_DIR/chief-herbs-property-es.html" | wc -l)" -eq 60 ]]

TARGET_PHP="$MOODLE_ROOT/blocks/myprogress/study-guide.php"
[[ -f "$TARGET_PHP" ]] || { echo "ERROR: no existe $TARGET_PHP" >&2; exit 1; }

declare -a TARGETS=()
declare -A SEEN=()
add_target() {
    local candidate="$1"
    [[ -d "$candidate" && -f "$candidate/comparisons.css" ]] || return 0
    local resolved
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || return 0
    if [[ -z "${SEEN[$resolved]:-}" ]]; then SEEN["$resolved"]=1; TARGETS+=("$resolved"); fi
}
if [[ -n "$REQUESTED_DIR" ]]; then
    add_target "$REQUESTED_DIR"
else
    add_target "/var/www/acupuncturetests-study-guide/current/comparisons"
    add_target "$MOODLE_ROOT/study-guide/comparisons"
    add_target "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
    add_target "/var/www/html/study-guide/comparisons"
fi
[[ "${#TARGETS[@]}" -gt 0 ]] || { echo "ERROR: no se encontró el directorio activo de comparaciones." >&2; exit 1; }

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/chief-herbs-property-$TIMESTAMP"
mkdir -p "$BACKUP/moodle" "$BACKUP/static"
cp -a "$TARGET_PHP" "$BACKUP/moodle/study-guide.php"
php_owner_group="$(stat -c '%u:%g' "$TARGET_PHP")"
php_mode="$(stat -c '%a' "$TARGET_PHP")"
install -o "${php_owner_group%:*}" -g "${php_owner_group#*:}" -m "$php_mode" "$TEMP_DIR/study-guide.php" "$TARGET_PHP"
cmp -s "$TEMP_DIR/study-guide.php" "$TARGET_PHP"
php -l "$TARGET_PHP" >/dev/null

for target in "${TARGETS[@]}"; do
    sub="$BACKUP/static/$(echo "$target" | tr '/ ' '__')"
    mkdir -p "$sub"
    reference="$target/comparisons.css"
    owner_group="$(stat -c '%u:%g' "$reference")"
    mode="$(stat -c '%a' "$reference")"
    for file in chief-herbs-property.html chief-herbs-property-es.html; do
        [[ -f "$target/$file" ]] && cp -a "$target/$file" "$sub/$file"
        install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$file" "$target/$file"
        cmp -s "$TEMP_DIR/$file" "$target/$file"
    done
    echo "ACTUALIZADO Y VERIFICADO: $target"
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: botón Chief Herbs Property instalado en Secondary Study Guide."
echo "OK: tabla bilingüe con 60 hierbas, propiedades, canales y fórmulas."
echo "OK: búsqueda instantánea y vista adaptable a móvil activas."
echo "OK: terminología Chief aplicada."
echo "RESPALDO: $BACKUP"
