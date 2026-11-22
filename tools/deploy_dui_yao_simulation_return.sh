#!/usr/bin/env bash

set -euo pipefail

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
EXPLICIT_DIR="${DUI_YAO_DIR:-}"
PAGES=("dui-yao.html" "dui-yao-es.html")

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_dui_yao_simulation_return.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl grep install stat cp cmp mkdir mktemp date rm mv readlink dirname; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/dui-yao-simulation-return-$(date +%Y%m%d-%H%M%S)"
cleanup() {
    rm -rf -- "$TEMP_DIR"
}
trap cleanup EXIT
trap 'status=$?; echo "ERROR: instalación detenida en la línea $LINENO (código $status)." >&2; exit "$status"' ERR

for page in "${PAGES[@]}"; do
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/study-guide/comparisons/$page?revision=$REVISION" \
        -o "$TEMP_DIR/$page"
done

grep -Fq 'href="/blocks/menu/herbology-board.php?track=simulation&amp;coverage=duiyao#student-duiyao-coverage"' \
    "$TEMP_DIR/dui-yao.html"
grep -Fq '← Return to Simulation Exams' "$TEMP_DIR/dui-yao.html"
grep -Fq 'href="/blocks/menu/herbology-board.php?track=simulation&amp;coverage=duiyao&amp;lang=es#student-duiyao-coverage"' \
    "$TEMP_DIR/dui-yao-es.html"
grep -Fq '← Volver a exámenes de simulación' "$TEMP_DIR/dui-yao-es.html"

candidate_dirs=()
if [[ -n "$EXPLICIT_DIR" ]]; then
    candidate_dirs+=("$EXPLICIT_DIR")
fi
candidate_dirs+=(
    "/var/www/acupuncturetests-study-guide/current/comparisons"
    "$MOODLE_ROOT/study-guide/comparisons"
    "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
    "/var/www/html/study-guide/comparisons"
)

target_dirs=()
declare -A seen_dirs=()
for candidate in "${candidate_dirs[@]}"; do
    [[ -f "$candidate/${PAGES[0]}" && -f "$candidate/${PAGES[1]}" ]] || continue
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || continue
    if [[ -z "${seen_dirs[$resolved]:-}" ]]; then
        target_dirs+=("$candidate")
        seen_dirs[$resolved]=1
    fi
done

if [[ "${#target_dirs[@]}" -eq 0 ]]; then
    echo "ERROR: no se encontró ninguna instalación activa de Duì Yào." >&2
    echo "Indique la ruta con DUI_YAO_DIR=/ruta/publica/comparisons." >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR"
root_number=0
for target_dir in "${target_dirs[@]}"; do
    root_number=$((root_number + 1))
    for page in "${PAGES[@]}"; do
        target="$target_dir/$page"
        backup="$BACKUP_DIR/root-$root_number/$page"
        staged="$target_dir/.$page.simulation-return.$$"
        mkdir -p "$(dirname "$backup")"
        cp -a -- "$target" "$backup"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
        install \
            -o "${owner_group%:*}" \
            -g "${owner_group#*:}" \
            -m "$mode" \
            "$TEMP_DIR/$page" \
            "$staged"
        cmp -s "$TEMP_DIR/$page" "$staged"
        mv -f -- "$staged" "$target"
        cmp -s "$TEMP_DIR/$page" "$target"
    done
    grep -Fq '← Return to Simulation Exams' "$target_dir/dui-yao.html"
    grep -Fq '← Volver a exámenes de simulación' "$target_dir/dui-yao-es.html"
    echo "OK: botón instalado y comprobado en $(readlink -f "$target_dir")"
done

echo "BOTÓN DE RETORNO DUÌ YÀO INSTALADO"
echo "English: ← Return to Simulation Exams"
echo "Español: ← Volver a exámenes de simulación"
echo "Destino: /blocks/menu/herbology-board.php?track=simulation&coverage=duiyao#student-duiyao-coverage"
echo "Respaldo: $BACKUP_DIR"
echo "Prueba sin caché: https://acupuncturetests.com/study-guide/comparisons/dui-yao.html?return=$REVISION"
