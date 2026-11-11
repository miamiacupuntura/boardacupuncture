#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Exam History detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_DIR="${STUDY_NOTE_DIR:-}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_exam_history_navigation.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp readlink tr date rm id dirname; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/tools"

curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/library-navigation.js" \
    -o "$TEMP_DIR/library-navigation.js"
curl -fsSL "$REPOSITORY/$REVISION/tools/inject_library_navigation.php" \
    -o "$TEMP_DIR/tools/inject_library_navigation.php"

php -l "$TEMP_DIR/tools/inject_library_navigation.php" >/dev/null
grep -Fq "allResources: 'Exam History'" "$TEMP_DIR/library-navigation.js"
grep -Fq "allResources: 'Historial de exámenes'" "$TEMP_DIR/library-navigation.js"
grep -Fq "/blocks/menu/exam-history.php?lang=" "$TEMP_DIR/library-navigation.js"
grep -Fq "library-navigation.js?v=20261111-1" "$TEMP_DIR/tools/inject_library_navigation.php"

declare -a TARGETS=()
declare -A SEEN=()

add_target() {
    local candidate="$1"
    [[ -d "$candidate" ]] || return 0
    [[ -f "$candidate/ma-huang-gui-zhi-dui-yao.html" || -f "$candidate/dui-yao.html" ]] || return 0
    local resolved
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || return 0
    if [[ -z "${SEEN[$resolved]:-}" ]]; then
        SEEN["$resolved"]=1
        TARGETS+=("$resolved")
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

[[ "${#TARGETS[@]}" -gt 0 ]] || {
    echo "ERROR: no se encontró ninguna ubicación activa de las fichas de comparación." >&2
    exit 1
}

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/exam-history-navigation-$TIMESTAMP"
mkdir -p "$BACKUP"

for target in "${TARGETS[@]}"; do
    sub="$BACKUP/$(echo "$target" | tr '/ ' '__')"
    mkdir -p "$sub/html"

    if [[ -f "$target/library-navigation.js" ]]; then
        cp -a "$target/library-navigation.js" "$sub/library-navigation.js"
        reference="$target/library-navigation.js"
    else
        reference="$target/ma-huang-gui-zhi-dui-yao.html"
        [[ -f "$reference" ]] || reference="$target/dui-yao.html"
    fi

    for html in "$target"/*.html; do
        [[ -f "$html" ]] || continue
        cp -a "$html" "$sub/html/"
    done

    owner_group="$(stat -c '%u:%g' "$reference")"
    mode="$(stat -c '%a' "$reference")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/library-navigation.js" "$target/library-navigation.js"
    cmp -s "$TEMP_DIR/library-navigation.js" "$target/library-navigation.js"

    php "$TEMP_DIR/tools/inject_library_navigation.php" --write --root="$target"
    php "$TEMP_DIR/tools/inject_library_navigation.php" --check --root="$target"

    grep -Fq "allResources: 'Exam History'" "$target/library-navigation.js"
    grep -Fq "/blocks/menu/exam-history.php?lang=" "$target/library-navigation.js"

    dui_yao_page="$target/ma-huang-gui-zhi-dui-yao.html"
    [[ -f "$dui_yao_page" ]] || dui_yao_page="$target/dui-yao.html"
    grep -Fq "library-navigation.js?v=20261111-1" "$dui_yao_page"

    if grep -Fq "library-navigation.js?v=20261108-14" "$target"/*.html; then
        echo "ERROR: quedan fichas con la versión anterior de Library navigation en $target" >&2
        exit 1
    fi

    echo "ACTUALIZADO Y VERIFICADO: $target"
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: Library home fue reemplazado por Exam History."
echo "OK: Exam History enlaza al historial de exámenes en English y Español."
echo "OK: las fichas Dui Yao cargan library-navigation.js?v=20261111-1."
echo "RESPALDO: $BACKUP"
