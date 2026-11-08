#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: instalación detenida en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
COMPARISON_ROOT="$MOODLE_ROOT/study-guide/comparisons"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/study-guide-navigation-$(date +%Y%m%d-%H%M%S)"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_study_guide_navigation.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl install grep wc tr; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done
[[ -d "$COMPARISON_ROOT" ]] || {
    echo "ERROR: no existe $COMPARISON_ROOT. Ajuste MOODLE_ROOT si el DocumentRoot es diferente." >&2
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

mkdir -p "$BACKUP_DIR"
for file in "${FILES[@]}"; do
    target="$COMPARISON_ROOT/$file"
    staged="$TEMP_DIR/$file"

    [[ -f "$target" ]] || {
        echo "ERROR: no se encontró el archivo activo $target." >&2
        exit 1
    }

    cp -a "$target" "$BACKUP_DIR/$file"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$staged" "$target"
    validate_navigation "$target" "$file"
done

echo "OK: navegación actualizada en ${#FILES[@]} páginas comparativas."
echo "Duì Yào instalado en: $COMPARISON_ROOT/dui-yao.html"
echo "Respaldo: $BACKUP_DIR"
