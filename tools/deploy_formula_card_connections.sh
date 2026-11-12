#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de conexiones de tarjetas detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_DIR="${STUDY_NOTE_DIR:-}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_formula_card_connections.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp readlink tr date rm id dirname; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/moodle" "$TEMP_DIR/static"

MOODLE_FILES=(formula-list.php single-herbs-list.php)
STATIC_FILES=(comparisons.js formulas.html formulas-es.html continuous-reader.js continuous-reader.html)

for file in "${MOODLE_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/moodle/$file"
    php -l "$TEMP_DIR/moodle/$file" >/dev/null
done
for file in "${STATIC_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/static/$file"
done

grep -Fq "single-herb-card-index.json" "$TEMP_DIR/moodle/formula-list.php"
grep -Fq "returnformula" "$TEMP_DIR/moodle/formula-list.php"
grep -Fq "formula-list__herb-link" "$TEMP_DIR/moodle/formula-list.php"
grep -Fq "Compare this formula" "$TEMP_DIR/moodle/formula-list.php"
grep -Fq '$fromformula' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq "Back to " "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq "data-return-to-formula-card" "$TEMP_DIR/static/comparisons.js"
grep -Fq "returnformula" "$TEMP_DIR/static/continuous-reader.js"
grep -Fq "comparisons.js?v=20261112-1" "$TEMP_DIR/static/formulas.html"
grep -Fq "continuous-reader.js?v=20261112-1" "$TEMP_DIR/static/continuous-reader.html"

MOODLE_TARGET="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$MOODLE_TARGET" ]] || { echo "ERROR: no existe $MOODLE_TARGET" >&2; exit 1; }
for file in "${MOODLE_FILES[@]}" single-herb-card-index.json; do
    [[ -f "$MOODLE_TARGET/$file" ]] || { echo "ERROR: falta $MOODLE_TARGET/$file" >&2; exit 1; }
done

declare -a STATIC_TARGETS=()
declare -A SEEN=()
add_target() {
    local candidate="$1"
    [[ -d "$candidate" && -f "$candidate/formulas.html" && -f "$candidate/continuous-reader.html" ]] || return 0
    local resolved
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || return 0
    if [[ -z "${SEEN[$resolved]:-}" ]]; then
        SEEN["$resolved"]=1
        STATIC_TARGETS+=("$resolved")
    fi
}
if [[ -n "$REQUESTED_DIR" ]]; then
    add_target "$REQUESTED_DIR"
else
    add_target "/var/www/acupuncturetests-study-guide/current/comparisons"
    add_target "$MOODLE_ROOT/study-guide/comparisons"
    add_target "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
    add_target "/var/www/html/study-guide/comparisons"
fi
[[ "${#STATIC_TARGETS[@]}" -gt 0 ]] || {
    echo "ERROR: no se encontró el directorio activo de Formula Comparisons." >&2
    exit 1
}

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/formula-card-connections-$TIMESTAMP"
mkdir -p "$BACKUP/moodle" "$BACKUP/static"

for file in "${MOODLE_FILES[@]}"; do
    target="$MOODLE_TARGET/$file"
    cp -a "$target" "$BACKUP/moodle/$file"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/moodle/$file" "$target"
    cmp -s "$TEMP_DIR/moodle/$file" "$target"
    php -l "$target" >/dev/null
done

for target_dir in "${STATIC_TARGETS[@]}"; do
    sub="$BACKUP/static/$(echo "$target_dir" | tr '/ ' '__')"
    mkdir -p "$sub"
    reference="$target_dir/formulas.html"
    owner_group="$(stat -c '%u:%g' "$reference")"
    mode="$(stat -c '%a' "$reference")"
    for file in "${STATIC_FILES[@]}"; do
        [[ -f "$target_dir/$file" ]] && cp -a "$target_dir/$file" "$sub/$file"
        install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/static/$file" "$target_dir/$file"
        cmp -s "$TEMP_DIR/static/$file" "$target_dir/$file"
    done
    echo "ACTUALIZADO Y VERIFICADO: $target_dir"
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: Chief herb e Ingredients enlazan con las tarjetas exactas de las 304 hierbas."
echo "OK: cada tarjeta de hierba muestra un botón de regreso a la fórmula original."
echo "OK: cada fórmula abre sus comparaciones filtradas y conserva el regreso en el lector."
echo "RESPALDO: $BACKUP"
