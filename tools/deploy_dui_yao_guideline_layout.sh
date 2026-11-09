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
    grep -Fq 'class="comparison-category-jump"' "$page"
    grep -Fq 'data-dui-category-select' "$page"
    grep -Fq 'class="comparison-category-index"' "$page"
    grep -Fq 'id="available-pairs"' "$page"
    [[ "$(grep -o '<option value="#' "$page" | wc -l)" -eq 15 ]]
    [[ "$(grep -o 'data-dui-category><header' "$page" | wc -l)" -eq 15 ]]
    [[ "$(grep -o 'data-type="dui-yao"' "$page" | wc -l)" -eq 51 ]]
    if grep -Fq 'id="how-to-use-dui-yao-module"' "$page" \
            || grep -Fq 'id="formula-vs-dui-yao"' "$page" \
            || grep -Fq 'id="formula-modification-bridges"' "$page"; then
        echo "ERROR: todavía queda contenido general dentro del índice de fichas Duì Yào." >&2
        exit 1
    fi
done

for page in "$EN_GUIDE" "$ES_GUIDE"; do
    grep -Fq 'id="appendix-b-study"' "$page"
    grep -Fq 'class="guide-dui-yao-general"' "$page"
    grep -Fq 'id="how-to-use-dui-yao-module"' "$page"
    grep -Fq 'id="formula-vs-dui-yao"' "$page"
    grep -Fq 'id="formula-modification-bridges"' "$page"
    page_content="$(<"$page")"
    before_general="${page_content%%class=\"guide-dui-yao-general\"*}"
    after_general="${page_content#*class=\"guide-dui-yao-general\"}"
    [[ "$before_general" == *'id="appendix-b-study"'* ]]
    [[ "$after_general" == *'guide-reading-step__number">6'* ]]
    if grep -Fq 'class="guide-dui-yao-hub"' "$page"; then
        echo "ERROR: permanece el bloque de navegación anterior dentro del guideline." >&2
        exit 1
    fi
done
grep -Fq '.comparison-category-jump {' "$TEMP_DIR/comparisons/comparisons.css"
grep -Fq '.guide-dui-yao-general {' "$TEMP_DIR/guidelines/guidelines.css"

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
echo "OK: todo el contenido general de Duì Yào fue movido al bloque Appendix B del guideline."
echo "OK: Duì Yào conserva el selector de 15 categorías y las 51 fichas Read chapter."
echo "Respaldo: $BACKUP_DIR"
