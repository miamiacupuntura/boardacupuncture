#!/usr/bin/env bash

set -euo pipefail

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
PRIMARY_STUDY_GUIDE_ROOT="${STUDY_GUIDE_ROOT:-/var/www/acupuncturetests-study-guide/current}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
MOODLE_FILES=(
    "blocks/menu/block_menu.php"
    "theme/moove/javascript/study-guide-dashboard.js"
)
STUDY_GUIDE_FILES=(
    "board-guidelines.html"
    "board-guidelines-es.html"
    "guidelines/adaptive-exam-help.html"
    "guidelines/adaptive-exam-help-es.html"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_adaptive_exam_help.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv readlink dirname basename id; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

for relative in "${MOODLE_FILES[@]}"; do
    [[ -f "$MOODLE_ROOT/$relative" ]] || {
        echo "ERROR: no existe $MOODLE_ROOT/$relative" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/adaptive-exam-help-$(date +%Y%m%d-%H%M%S)"
cleanup() {
    rm -rf -- "$TEMP_DIR"
}
trap cleanup EXIT
trap 'status=$?; echo "ERROR: despliegue de la ayuda adaptativa detenido en la línea $LINENO (código $status)." >&2; exit "$status"' ERR

for relative in "${MOODLE_FILES[@]}"; do
    source_file="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$source_file")"
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/$relative?revision=$REVISION" \
        -o "$source_file"
done
for relative in "${STUDY_GUIDE_FILES[@]}"; do
    source_file="$TEMP_DIR/study-guide/$relative"
    mkdir -p "$(dirname "$source_file")"
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/study-guide/$relative?revision=$REVISION" \
        -o "$source_file"
done

php -l "$TEMP_DIR/${MOODLE_FILES[0]}" >/dev/null
if command -v node >/dev/null 2>&1; then
    node --check "$TEMP_DIR/${MOODLE_FILES[1]}"
fi
grep -Fq 'Adaptive Exam Help' "$TEMP_DIR/${MOODLE_FILES[0]}"
grep -Fq 'adaptive-exam-help.html' "$TEMP_DIR/${MOODLE_FILES[1]}"
grep -Fq 'Open Adaptive Exam Help' "$TEMP_DIR/study-guide/board-guidelines.html"
grep -Fq 'Abrir ayuda del examen adaptativo' "$TEMP_DIR/study-guide/board-guidelines-es.html"
grep -Fq 'Current system status:' "$TEMP_DIR/study-guide/guidelines/adaptive-exam-help.html"
grep -Fq 'Estado actual del sistema:' "$TEMP_DIR/study-guide/guidelines/adaptive-exam-help-es.html"
grep -Fq 'content-balanced adaptive practice' "$TEMP_DIR/study-guide/guidelines/adaptive-exam-help.html"
grep -Fq 'práctica adaptativa con balance de contenido' "$TEMP_DIR/study-guide/guidelines/adaptive-exam-help-es.html"

candidate_roots=(
    "$PRIMARY_STUDY_GUIDE_ROOT"
    "$MOODLE_ROOT/study-guide"
    "$(dirname "$MOODLE_ROOT")/study-guide"
    "/var/www/html/study-guide"
)
study_guide_roots=()
declare -A seen_roots=()
for candidate in "${candidate_roots[@]}"; do
    [[ -f "$candidate/board-guidelines.html" \
        && -f "$candidate/board-guidelines-es.html" \
        && -d "$candidate/guidelines" ]] || continue
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || continue
    if [[ -z "${seen_roots[$resolved]:-}" ]]; then
        study_guide_roots+=("$candidate")
        seen_roots[$resolved]=1
    fi
done
if [[ "${#study_guide_roots[@]}" -eq 0 ]]; then
    echo "ERROR: no se encontró una instalación activa de study-guide." >&2
    echo "Indique la ruta con STUDY_GUIDE_ROOT=/ruta/publica/study-guide." >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR"
for relative in "${MOODLE_FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    source_file="$TEMP_DIR/$relative"
    backup="$BACKUP_DIR/moodle/$relative"
    staged="$MOODLE_ROOT/$(dirname "$relative")/.$(basename "$relative").adaptive-help.$$"
    mkdir -p "$(dirname "$backup")"
    cp -a -- "$target" "$backup"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$source_file" "$staged"
    cmp -s "$source_file" "$staged"
    mv -f -- "$staged" "$target"
    cmp -s "$source_file" "$target"
done

root_number=0
for root in "${study_guide_roots[@]}"; do
    root_number=$((root_number + 1))
    for relative in "${STUDY_GUIDE_FILES[@]}"; do
        target="$root/$relative"
        source_file="$TEMP_DIR/study-guide/$relative"
        backup="$BACKUP_DIR/study-guide-root-$root_number/$relative"
        staged="$root/$(dirname "$relative")/.$(basename "$relative").adaptive-help.$$"
        mkdir -p "$(dirname "$target")" "$(dirname "$backup")"
        if [[ -f "$target" ]]; then
            cp -a -- "$target" "$backup"
            owner_group="$(stat -c '%u:%g' "$target")"
            mode="$(stat -c '%a' "$target")"
        else
            owner_group="$(stat -c '%u:%g' "$(dirname "$target")")"
            mode="644"
        fi
        install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$source_file" "$staged"
        cmp -s "$source_file" "$staged"
        mv -f -- "$staged" "$target"
        cmp -s "$source_file" "$target"
    done
    grep -Fq 'Open Adaptive Exam Help' "$root/board-guidelines.html"
    grep -Fq 'Abrir ayuda del examen adaptativo' "$root/board-guidelines-es.html"
    grep -Fq 'Current system status:' "$root/guidelines/adaptive-exam-help.html"
    grep -Fq 'Estado actual del sistema:' "$root/guidelines/adaptive-exam-help-es.html"
    echo "OK: ayuda adaptativa instalada en $(readlink -f "$root")"
done

php -l "$MOODLE_ROOT/${MOODLE_FILES[0]}" >/dev/null
grep -Fq 'Adaptive Exam Help' "$MOODLE_ROOT/${MOODLE_FILES[0]}"
grep -Fq 'adaptive-exam-help.html' "$MOODLE_ROOT/${MOODLE_FILES[1]}"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "AYUDA DEL EXAMEN ADAPTATIVO INSTALADA"
echo "OK: el dashboard muestra Open Board Guidelines y Adaptive Exam Help."
echo "OK: Board Guidelines contiene la nueva tarjeta de ayuda en inglés y español."
echo "OK: la respuesta distingue el comportamiento actual de la política recomendada."
echo "RESPALDO: $BACKUP_DIR"
echo "PRUEBA: https://acupuncturetests.com/study-guide/guidelines/adaptive-exam-help.html?revision=$REVISION"
