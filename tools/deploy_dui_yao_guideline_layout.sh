#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/dui-yao-guideline-layout-$(date +%Y%m%d-%H%M%S)"
PRIMARY_ROOT="${STUDY_GUIDE_ROOT:-/var/www/acupuncturetests-study-guide/current}"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
FILES=(
    "comparisons/dui-yao.html"
    "comparisons/dui-yao-es.html"
    "comparisons/comparisons.css"
    "guidelines/chinese-herbal-medicine.html"
    "guidelines/chinese-herbal-medicine-es.html"
    "guidelines/guidelines.css"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_dui_yao_guideline_layout.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl install grep stat cp mkdir mktemp rm readlink wc dirname; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
for relative in "${FILES[@]}"; do
    mkdir -p "$TEMP_DIR/$(dirname "$relative")"
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/$relative" -o "$TEMP_DIR/$relative"
done

EN_DUI="$TEMP_DIR/comparisons/dui-yao.html"
ES_DUI="$TEMP_DIR/comparisons/dui-yao-es.html"
EN_GUIDE="$TEMP_DIR/guidelines/chinese-herbal-medicine.html"
ES_GUIDE="$TEMP_DIR/guidelines/chinese-herbal-medicine-es.html"

for page in "$EN_DUI" "$ES_DUI"; do
    grep -Fq 'comparison-topbar--study-guide' "$page"
    grep -Fq 'comparison-hero--study-guide' "$page"
    grep -Fq "new URLSearchParams(window.location.search).get('search')" "$page"
    grep -Fq 'id="available-pairs"' "$page"
    [[ "$(grep -o 'data-dui-category' "$page" | wc -l)" -eq 15 ]]
    if grep -Fq 'comparison-tools--search-only' "$page"; then
        echo "ERROR: la búsqueda antigua todavía permanece dentro de Duì Yào." >&2
        exit 1
    fi
done

for page in "$EN_GUIDE" "$ES_GUIDE"; do
    grep -Fq 'id="appendix-b-study"' "$page"
    grep -Fq 'class="guide-dui-yao-hub"' "$page"
    grep -Fq 'class="guide-dui-yao-hub__search"' "$page"
    page_content="$(<"$page")"
    after_hub="${page_content#*class=\"guide-dui-yao-hub\"}"
    [[ "$before_hub" == *'id="appendix-b-study"'* ]]
    [[ "$after_hub" == *'guide-reading-step__number">6'* ]]
done
[[ "$(grep -o '<a href="../comparisons/dui-yao.html#' "$EN_GUIDE" | wc -l)" -eq 15 ]]
[[ "$(grep -o '<a href="../comparisons/dui-yao-es.html#' "$ES_GUIDE" | wc -l)" -eq 15 ]]
grep -Fq '.comparison-topbar--study-guide' "$TEMP_DIR/comparisons/comparisons.css"
grep -Fq '.guide-dui-yao-hub {' "$TEMP_DIR/guidelines/guidelines.css"

roots=()
declare -A seen_roots=()
for candidate in "$PRIMARY_ROOT" "$MOODLE_ROOT/study-guide"; do
    [[ -d "$candidate" ]] || continue
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || continue
    if [[ -z "${seen_roots[$resolved]:-}" ]]; then
        roots+=("$candidate")
        seen_roots[$resolved]=1
    fi
done
[[ "${#roots[@]}" -gt 0 ]] || {
    echo "ERROR: no se encontró ninguna instalación activa de study-guide." >&2
    exit 1
}

root_number=0
for root in "${roots[@]}"; do
    root_number=$((root_number + 1))
    for relative in "${FILES[@]}"; do
        target="$root/$relative"
        [[ -f "$target" ]] || {
            echo "ERROR: no se encontró $target" >&2
            exit 1
        }
        backup="$BACKUP_DIR/root-$root_number/$relative"
        mkdir -p "$(dirname "$backup")"
        cp -a "$target" "$backup"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
        install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$relative" "$target"
    done
    echo "OK: archivos instalados en $(readlink -f "$root")"
done

echo "OK: Duì Yào usa la misma estructura superior de Single Herbs y Formulas."
echo "OK: búsqueda y 15 categorías movidas al bloque Appendix B · Duì Yào del guideline."
echo "OK: los enlaces abren la categoría correspondiente y la búsqueda abre resultados filtrados."
echo "Respaldo: $BACKUP_DIR"
