#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: instalación detenida en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
WEB_ROOT_SEARCH="${WEB_ROOT_SEARCH:-/var/www}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/study-guide-navigation-$(date +%Y%m%d-%H%M%S)"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_study_guide_navigation.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl install grep wc tr find sort stat cp readlink dirname; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done
[[ -d "$WEB_ROOT_SEARCH" ]] || {
    echo "ERROR: no existe el directorio de búsqueda $WEB_ROOT_SEARCH." >&2
    exit 1
}

FILES=(
    "dui-yao.html"
    "dui-yao-es.html"
    "herb-comparison-bank.html"
    "herb-comparison-bank-es.html"
    "formula-navigator.html"
    "formula-navigator-es.html"
    "families.html"
    "families-es.html"
    "name-traps.html"
    "name-traps-es.html"
    "herb-families.html"
    "herb-families-es.html"
    "safety.html"
    "safety-es.html"
    "formula-name-traps.html"
    "formula-name-traps-es.html"
    "misleading-formula-names.html"
    "misleading-formula-names-es.html"
    "advanced-name-traps.html"
    "advanced-name-traps-es.html"
    "supplemental-herb-families.html"
    "supplemental-herb-families-es.html"
    "patterns-tips-maciocia.html"
    "patterns-tips-maciocia-es.html"
    "most-important-formulas.html"
    "most-important-formulas-es.html"
    "formula-theory.html"
    "formula-theory-es.html"
    "herbal-incompatibilities.html"
    "herbal-incompatibilities-es.html"
    "secondary-actions.html"
    "secondary-actions-es.html"
    "formulas.html"
    "formulas-es.html"
    "pathologies.html"
    "pathologies-es.html"
    "patterns.html"
    "patterns-es.html"
    "formula-practice.html"
    "formula-practice-es.html"
)

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TEMP_DIR"' EXIT

validate_navigation() {
    local file="$1"
    local relative="$2"
    local navigation_count

    grep -Eq '← Board Level Activities|← Actividades Board Level' "$file"
    grep -Eq '>Español</a>|>English</a>' "$file"
    navigation_count="$(grep -o 'class="comparison-back"' "$file" | wc -l | tr -d ' ')"
    if [[ "$navigation_count" -ne 2 ]]; then
        echo "ERROR: $relative debe tener exactamente dos controles globales; encontrados: $navigation_count." >&2
        exit 1
    fi
    if grep -Eq 'Back to Dashboard|← Dashboard|practice-study-route|Library home' "$file"; then
        echo "ERROR: $relative todavía contiene la navegación global anterior." >&2
        exit 1
    fi
}

for file in "${FILES[@]}"; do
    staged="$TEMP_DIR/$file"
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$staged"
    validate_navigation "$staged" "$file"
done

mapfile -t COMPARISON_ROOTS < <(
    while IFS= read -r candidate; do
        dirname "$(readlink -f "$candidate")"
    done < <(find -L "$WEB_ROOT_SEARCH" -type f -path '*/comparisons/dui-yao.html' -print 2>/dev/null)
)

mapfile -t COMPARISON_ROOTS < <(printf '%s\n' "${COMPARISON_ROOTS[@]}" | sort -u)

if [[ "${#COMPARISON_ROOTS[@]}" -eq 0 ]]; then
    echo "ERROR: no se encontró ninguna copia activa de comparisons/dui-yao.html bajo $WEB_ROOT_SEARCH." >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR"
installed_total=0
root_index=0
for comparison_root in "${COMPARISON_ROOTS[@]}"; do
    root_index=$((root_index + 1))
    root_backup="$BACKUP_DIR/root-$root_index"
    mkdir -p "$root_backup"
    root_installed=0

    for file in "${FILES[@]}"; do
        target="$comparison_root/$file"
        staged="$TEMP_DIR/$file"
        if [[ ! -f "$target" ]]; then
            continue
        fi

        cp -a "$target" "$root_backup/$file"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
        install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$staged" "$target"
        validate_navigation "$target" "$file"
        root_installed=$((root_installed + 1))
        installed_total=$((installed_total + 1))
    done

    validate_navigation "$comparison_root/dui-yao.html" "dui-yao.html en $comparison_root"
    echo "OK: $root_installed páginas actualizadas en $comparison_root"
done

echo "OK: $installed_total archivos instalados en ${#COMPARISON_ROOTS[@]} ubicaciones detectadas."
echo "Respaldo: $BACKUP_DIR"
