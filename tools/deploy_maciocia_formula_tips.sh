#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue de Maciocia Formula Tips detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_STUDY_NOTE_DIR="${STUDY_NOTE_DIR:-}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_maciocia_formula_tips.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl grep install stat cp cmp mkdir mktemp readlink tr wc dirname date php rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
GUIDE_PHP="$TEMP_DIR/study-guide.php"
EN_PAGE="$TEMP_DIR/maciocia-formula-tips.html"
ES_PAGE="$TEMP_DIR/maciocia-formula-tips-es.html"
MISC_EN="$TEMP_DIR/miscellaneous-practice.html"
MISC_ES="$TEMP_DIR/miscellaneous-practice-es.html"
MISC_JS="$TEMP_DIR/miscellaneous-practice.js"
SECONDARY_REVIEW="$TEMP_DIR/secondary-action-review.html"
SECONDARY_REVIEW_ES="$TEMP_DIR/secondary-action-review-es.html"
FAMILY_REVIEW="$TEMP_DIR/formula-family-review.html"
FAMILY_REVIEW_ES="$TEMP_DIR/formula-family-review-es.html"
NAME_TRAP_REVIEW="$TEMP_DIR/name-trap-review.html"
NAME_TRAP_REVIEW_ES="$TEMP_DIR/name-trap-review-es.html"
HERB_FAMILY_REVIEW="$TEMP_DIR/herb-family-review.html"
HERB_FAMILY_REVIEW_ES="$TEMP_DIR/herb-family-review-es.html"
FORMULA_NAME_REVIEW="$TEMP_DIR/formula-name-trap-review.html"
FORMULA_NAME_REVIEW_ES="$TEMP_DIR/formula-name-trap-review-es.html"
MISLEADING_REVIEW="$TEMP_DIR/misleading-formula-review.html"
MISLEADING_REVIEW_ES="$TEMP_DIR/misleading-formula-review-es.html"

curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" -o "$GUIDE_PHP"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/maciocia-formula-tips.html" -o "$EN_PAGE"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/maciocia-formula-tips-es.html" -o "$ES_PAGE"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/miscellaneous-practice.html" -o "$MISC_EN"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/miscellaneous-practice-es.html" -o "$MISC_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/miscellaneous-practice.js" -o "$MISC_JS"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/secondary-action-review.html" -o "$SECONDARY_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/secondary-action-review-es.html" -o "$SECONDARY_REVIEW_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/formula-family-review.html" -o "$FAMILY_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/formula-family-review-es.html" -o "$FAMILY_REVIEW_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/name-trap-review.html" -o "$NAME_TRAP_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/name-trap-review-es.html" -o "$NAME_TRAP_REVIEW_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/herb-family-review.html" -o "$HERB_FAMILY_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/herb-family-review-es.html" -o "$HERB_FAMILY_REVIEW_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/formula-name-trap-review.html" -o "$FORMULA_NAME_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/formula-name-trap-review-es.html" -o "$FORMULA_NAME_REVIEW_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/misleading-formula-review.html" -o "$MISLEADING_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/misleading-formula-review-es.html" -o "$MISLEADING_REVIEW_ES"

php -l "$GUIDE_PHP" >/dev/null
grep -Fq "Maciocia Formula Tips" "$GUIDE_PHP"
grep -Fq '$maciociaformulatipsurl = new moodle_url(' "$GUIDE_PHP"
grep -Fq "72 guideline formulas · 167 questions" "$GUIDE_PHP"
grep -Fq "Module 2 · Comparison Study Guide" "$EN_PAGE"
grep -Fq "Módulo 2 · Guía de comparaciones" "$ES_PAGE"
grep -Fq "167 pattern-to-answer associations" "$EN_PAGE"
grep -Fq "167 asociaciones de patrón y respuesta" "$ES_PAGE"
grep -Fq "miscellaneous-practice.js?v=20261111-20" "$MISC_EN"
grep -Fq "miscellaneous-practice.js?v=20261111-20" "$MISC_ES"
grep -Fq "maciociaformulas" "$MISC_JS"
grep -Fq "function secondaryReviewPanel" "$MISC_JS"
grep -Fq "Review the four options" "$MISC_JS"
grep -Fq "Back to quiz" "$SECONDARY_REVIEW"
grep -Fq "Regresar al quiz" "$SECONDARY_REVIEW_ES"
grep -Fq "function familyReviewPanel" "$MISC_JS"
grep -Fq "Review the four families" "$MISC_JS"
grep -Fq "Back to quiz" "$FAMILY_REVIEW"
grep -Fq "Regresar al quiz" "$FAMILY_REVIEW_ES"
grep -Fq "function nameTrapReviewPanel" "$MISC_JS"
grep -Fq "Review the four name traps" "$MISC_JS"
grep -Fq "Back to quiz" "$NAME_TRAP_REVIEW"
grep -Fq "Regresar al quiz" "$NAME_TRAP_REVIEW_ES"
grep -Fq "function herbFamilyReviewPanel" "$MISC_JS"
grep -Fq "Review the four botanical families" "$MISC_JS"
grep -Fq "Back to quiz" "$HERB_FAMILY_REVIEW"
grep -Fq "Regresar al quiz" "$HERB_FAMILY_REVIEW_ES"
grep -Fq "function formulaNameTrapReviewPanel" "$MISC_JS"
grep -Fq "Review the four formula-name distinctions" "$MISC_JS"
grep -Fq "Back to quiz" "$FORMULA_NAME_REVIEW"
grep -Fq "Regresar al quiz" "$FORMULA_NAME_REVIEW_ES"
grep -Fq "function misleadingNameReviewPanel" "$MISC_JS"
grep -Fq "Review the four formulas and their actual actions" "$MISC_JS"
grep -Fq "Back to quiz" "$MISLEADING_REVIEW"
grep -Fq "Regresar al quiz" "$MISLEADING_REVIEW_ES"
grep -Fq "Which guideline formula or formula set matches this Maciocia pattern?" "$MISC_JS"
[[ "$(grep -Fo '"answer":' "$EN_PAGE" | wc -l)" -eq 167 ]]
[[ "$(grep -Fo '"answer":' "$ES_PAGE" | wc -l)" -eq 167 ]]

declare -a TARGETS=()
declare -A SEEN_TARGETS=()
add_target() {
    local candidate="$1"
    [[ -d "$candidate" ]] || return 0
    [[ -f "$candidate/formula-practice.html" || -f "$candidate/miscellaneous-practice.html" ]] || return 0
    local resolved
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || return 0
    if [[ -z "${SEEN_TARGETS[$resolved]:-}" ]]; then
        SEEN_TARGETS["$resolved"]=1
        TARGETS+=("$resolved")
    fi
}
if [[ -n "$REQUESTED_STUDY_NOTE_DIR" ]]; then
    add_target "$REQUESTED_STUDY_NOTE_DIR"
else
    add_target "/var/www/acupuncturetests-study-guide/current/comparisons"
    add_target "$MOODLE_ROOT/study-guide/comparisons"
    add_target "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
    add_target "/var/www/html/study-guide/comparisons"
fi
if [[ "${#TARGETS[@]}" -eq 0 ]]; then
    echo "ERROR: no se encontró ninguna ubicación de Study Notes." >&2
    exit 1
fi

GUIDE_TARGET="$MOODLE_ROOT/blocks/myprogress/study-guide.php"
[[ -f "$GUIDE_TARGET" ]] || { echo "ERROR: no existe $GUIDE_TARGET" >&2; exit 1; }
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/maciocia-formula-tips-$TIMESTAMP"
mkdir -p "$BACKUP_DIR/blocks/myprogress" "$BACKUP_DIR/study-guide/comparisons"
cp -a "$GUIDE_TARGET" "$BACKUP_DIR/blocks/myprogress/study-guide.php"

install_preserving() {
    local source_file="$1" target_file="$2" reference_file="$3"
    local owner_group mode
    if [[ -f "$target_file" ]]; then
        owner_group="$(stat -c '%u:%g' "$target_file")"
        mode="$(stat -c '%a' "$target_file")"
    else
        owner_group="$(stat -c '%u:%g' "$reference_file")"
        mode="$(stat -c '%a' "$reference_file")"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$source_file" "$target_file"
}

install_preserving "$GUIDE_PHP" "$GUIDE_TARGET" "$GUIDE_TARGET"
for target_dir in "${TARGETS[@]}"; do
    reference="$target_dir/formula-practice.html"
    [[ -f "$reference" ]] || reference="$target_dir/miscellaneous-practice.html"
    target_backup="$BACKUP_DIR/study-guide/comparisons/$(echo "$target_dir" | tr '/ ' '__')"
    mkdir -p "$target_backup"
    for file in maciocia-formula-tips.html maciocia-formula-tips-es.html miscellaneous-practice.html miscellaneous-practice-es.html miscellaneous-practice.js secondary-action-review.html secondary-action-review-es.html formula-family-review.html formula-family-review-es.html name-trap-review.html name-trap-review-es.html herb-family-review.html herb-family-review-es.html formula-name-trap-review.html formula-name-trap-review-es.html misleading-formula-review.html misleading-formula-review-es.html; do
        [[ -f "$target_dir/$file" ]] && cp -a "$target_dir/$file" "$target_backup/$file"
    done
    install_preserving "$EN_PAGE" "$target_dir/maciocia-formula-tips.html" "$reference"
    install_preserving "$ES_PAGE" "$target_dir/maciocia-formula-tips-es.html" "$reference"
    install_preserving "$MISC_EN" "$target_dir/miscellaneous-practice.html" "$reference"
    install_preserving "$MISC_ES" "$target_dir/miscellaneous-practice-es.html" "$reference"
    install_preserving "$MISC_JS" "$target_dir/miscellaneous-practice.js" "$reference"
    install_preserving "$SECONDARY_REVIEW" "$target_dir/secondary-action-review.html" "$reference"
    install_preserving "$SECONDARY_REVIEW_ES" "$target_dir/secondary-action-review-es.html" "$reference"
    install_preserving "$FAMILY_REVIEW" "$target_dir/formula-family-review.html" "$reference"
    install_preserving "$FAMILY_REVIEW_ES" "$target_dir/formula-family-review-es.html" "$reference"
    install_preserving "$NAME_TRAP_REVIEW" "$target_dir/name-trap-review.html" "$reference"
    install_preserving "$NAME_TRAP_REVIEW_ES" "$target_dir/name-trap-review-es.html" "$reference"
    install_preserving "$HERB_FAMILY_REVIEW" "$target_dir/herb-family-review.html" "$reference"
    install_preserving "$HERB_FAMILY_REVIEW_ES" "$target_dir/herb-family-review-es.html" "$reference"
    install_preserving "$FORMULA_NAME_REVIEW" "$target_dir/formula-name-trap-review.html" "$reference"
    install_preserving "$FORMULA_NAME_REVIEW_ES" "$target_dir/formula-name-trap-review-es.html" "$reference"
    install_preserving "$MISLEADING_REVIEW" "$target_dir/misleading-formula-review.html" "$reference"
    install_preserving "$MISLEADING_REVIEW_ES" "$target_dir/misleading-formula-review-es.html" "$reference"
    cmp -s "$EN_PAGE" "$target_dir/maciocia-formula-tips.html"
    cmp -s "$ES_PAGE" "$target_dir/maciocia-formula-tips-es.html"
    cmp -s "$MISC_EN" "$target_dir/miscellaneous-practice.html"
    cmp -s "$MISC_ES" "$target_dir/miscellaneous-practice-es.html"
    cmp -s "$MISC_JS" "$target_dir/miscellaneous-practice.js"
    cmp -s "$SECONDARY_REVIEW" "$target_dir/secondary-action-review.html"
    cmp -s "$SECONDARY_REVIEW_ES" "$target_dir/secondary-action-review-es.html"
    cmp -s "$FAMILY_REVIEW" "$target_dir/formula-family-review.html"
    cmp -s "$FAMILY_REVIEW_ES" "$target_dir/formula-family-review-es.html"
    cmp -s "$NAME_TRAP_REVIEW" "$target_dir/name-trap-review.html"
    cmp -s "$NAME_TRAP_REVIEW_ES" "$target_dir/name-trap-review-es.html"
    cmp -s "$HERB_FAMILY_REVIEW" "$target_dir/herb-family-review.html"
    cmp -s "$HERB_FAMILY_REVIEW_ES" "$target_dir/herb-family-review-es.html"
    cmp -s "$FORMULA_NAME_REVIEW" "$target_dir/formula-name-trap-review.html"
    cmp -s "$FORMULA_NAME_REVIEW_ES" "$target_dir/formula-name-trap-review-es.html"
    cmp -s "$MISLEADING_REVIEW" "$target_dir/misleading-formula-review.html"
    cmp -s "$MISLEADING_REVIEW_ES" "$target_dir/misleading-formula-review-es.html"
done
cmp -s "$GUIDE_PHP" "$GUIDE_TARGET"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: Maciocia Formula Tips instalado en el módulo 2."
echo "OK: 72 fórmulas oficiales del guideline y 167 preguntas verificadas."
echo "RESPALDO: $BACKUP_DIR"
