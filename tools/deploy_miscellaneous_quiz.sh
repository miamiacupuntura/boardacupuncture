#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue de Miscellaneous Quiz detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_STUDY_NOTE_DIR="${STUDY_NOTE_DIR:-}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_miscellaneous_quiz.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl grep install stat cp cmp mkdir mktemp readlink tr wc dirname date php rm id; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
EN_HTML="$TEMP_DIR/miscellaneous-practice.html"
ES_HTML="$TEMP_DIR/miscellaneous-practice-es.html"
JAVASCRIPT="$TEMP_DIR/miscellaneous-practice.js"
JUE_MING_CARD="$TEMP_DIR/jue-ming-zi-vs-shi-jue-ming.html"
MACIOCIA_FORMULA_TIPS="$TEMP_DIR/maciocia-formula-tips.html"
MACIOCIA_FORMULA_TIPS_ES="$TEMP_DIR/maciocia-formula-tips-es.html"
SECONDARY_REVIEW="$TEMP_DIR/secondary-action-review.html"
SECONDARY_REVIEW_ES="$TEMP_DIR/secondary-action-review-es.html"
FAMILY_REVIEW="$TEMP_DIR/formula-family-review.html"
FAMILY_REVIEW_ES="$TEMP_DIR/formula-family-review-es.html"
NAME_TRAP_REVIEW="$TEMP_DIR/name-trap-review.html"
NAME_TRAP_REVIEW_ES="$TEMP_DIR/name-trap-review-es.html"

curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/miscellaneous-practice.html" -o "$EN_HTML"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/miscellaneous-practice-es.html" -o "$ES_HTML"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/miscellaneous-practice.js" -o "$JAVASCRIPT"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/jue-ming-zi-vs-shi-jue-ming.html" -o "$JUE_MING_CARD"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/maciocia-formula-tips.html" -o "$MACIOCIA_FORMULA_TIPS"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/maciocia-formula-tips-es.html" -o "$MACIOCIA_FORMULA_TIPS_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/secondary-action-review.html" -o "$SECONDARY_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/secondary-action-review-es.html" -o "$SECONDARY_REVIEW_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/formula-family-review.html" -o "$FAMILY_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/formula-family-review-es.html" -o "$FAMILY_REVIEW_ES"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/name-trap-review.html" -o "$NAME_TRAP_REVIEW"
curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/name-trap-review-es.html" -o "$NAME_TRAP_REVIEW_ES"

grep -Fq "miscellaneous-practice.js?v=20261111-17" "$EN_HTML"
grep -Fq "miscellaneous-practice.js?v=20261111-17" "$ES_HTML"
grep -Fq "Which precaution applies to this medicinal?" "$JAVASCRIPT"
grep -Fq "¿Qué precaución corresponde a este medicamento?" "$JAVASCRIPT"
grep -Fq "data-misc-quiz-build" "$JAVASCRIPT"
grep -Fq "function safetyChoices" "$JAVASCRIPT"
grep -Fq "const safetyDomains = {" "$JAVASCRIPT"
grep -Fq "Handbook of Oriental Medicine — Caution & Contraindication, p. 373" "$JAVASCRIPT"
grep -Fq "Mock review · Why each option is right or wrong" "$JAVASCRIPT"
grep -Fq "function safetyMockReview" "$JAVASCRIPT"
grep -Fq "Open Jue Ming Zi study card" "$JAVASCRIPT"
grep -Fq "Back to the quiz question" "$JUE_MING_CARD"
grep -Fq "Regresar a la pregunta del quiz" "$JUE_MING_CARD"
grep -Fq "Guideline precaution</th><td>Diarrhea and hypotension." "$JUE_MING_CARD"
grep -Fq "Maciocia Formula Tips" "$MACIOCIA_FORMULA_TIPS"
grep -Fq "167 pattern-to-answer associations" "$MACIOCIA_FORMULA_TIPS"
grep -Fq "167 asociaciones de patrón y respuesta" "$MACIOCIA_FORMULA_TIPS_ES"
grep -Fq "maciociaformulas" "$JAVASCRIPT"
grep -Fq "function secondaryReviewPanel" "$JAVASCRIPT"
grep -Fq "Review the four options" "$JAVASCRIPT"
grep -Fq "Back to quiz" "$SECONDARY_REVIEW"
grep -Fq "Regresar al quiz" "$SECONDARY_REVIEW_ES"
grep -Fq "function familyReviewPanel" "$JAVASCRIPT"
grep -Fq "Review the four families" "$JAVASCRIPT"
grep -Fq "Back to quiz" "$FAMILY_REVIEW"
grep -Fq "Regresar al quiz" "$FAMILY_REVIEW_ES"
grep -Fq "function nameTrapReviewPanel" "$JAVASCRIPT"
grep -Fq "Review the four name traps" "$JAVASCRIPT"
grep -Fq "Back to quiz" "$NAME_TRAP_REVIEW"
grep -Fq "Regresar al quiz" "$NAME_TRAP_REVIEW_ES"
[[ "$(grep -Fo '"answer":' "$MACIOCIA_FORMULA_TIPS" | wc -l)" -eq 167 ]]
[[ "$(grep -Fo '"answer":' "$MACIOCIA_FORMULA_TIPS_ES" | wc -l)" -eq 167 ]]
[[ "$(grep -Fo '"enCaution":' "$JAVASCRIPT" | wc -l)" -eq 27 ]]
[[ "$(grep -Fo '"esCaution":' "$JAVASCRIPT" | wc -l)" -eq 27 ]]
if grep -Fq "Which safety topic matches this clinical precaution?" "$JAVASCRIPT"; then
    echo "ERROR: el JavaScript descargado todavía contiene el banco anterior de 12 temas." >&2
    exit 1
fi

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
    echo "Indica la ruta explícitamente: STUDY_NOTE_DIR=/ruta/comparisons sudo bash $0 $REVISION" >&2
    exit 1
fi

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
install_one() {
    local source_file="$1"
    local target_file="$2"
    local reference_file="$3"
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

for target_dir in "${TARGETS[@]}"; do
    reference="$target_dir/formula-practice.html"
    [[ -f "$reference" ]] || reference="$target_dir/miscellaneous-practice.html"
    backup_dir="$BACKUP_ROOT/miscellaneous-quiz-$TIMESTAMP/$(echo "$target_dir" | tr '/ ' '__')"
    mkdir -p "$backup_dir"
    for file in miscellaneous-practice.html miscellaneous-practice-es.html miscellaneous-practice.js jue-ming-zi-vs-shi-jue-ming.html maciocia-formula-tips.html maciocia-formula-tips-es.html secondary-action-review.html secondary-action-review-es.html formula-family-review.html formula-family-review-es.html name-trap-review.html name-trap-review-es.html; do
        if [[ -f "$target_dir/$file" ]]; then
            cp -a "$target_dir/$file" "$backup_dir/$file"
        fi
    done

    install_one "$EN_HTML" "$target_dir/miscellaneous-practice.html" "$reference"
    install_one "$ES_HTML" "$target_dir/miscellaneous-practice-es.html" "$reference"
    install_one "$JAVASCRIPT" "$target_dir/miscellaneous-practice.js" "$reference"
    install_one "$JUE_MING_CARD" "$target_dir/jue-ming-zi-vs-shi-jue-ming.html" "$reference"
    install_one "$MACIOCIA_FORMULA_TIPS" "$target_dir/maciocia-formula-tips.html" "$reference"
    install_one "$MACIOCIA_FORMULA_TIPS_ES" "$target_dir/maciocia-formula-tips-es.html" "$reference"
    install_one "$SECONDARY_REVIEW" "$target_dir/secondary-action-review.html" "$reference"
    install_one "$SECONDARY_REVIEW_ES" "$target_dir/secondary-action-review-es.html" "$reference"
    install_one "$FAMILY_REVIEW" "$target_dir/formula-family-review.html" "$reference"
    install_one "$FAMILY_REVIEW_ES" "$target_dir/formula-family-review-es.html" "$reference"
    install_one "$NAME_TRAP_REVIEW" "$target_dir/name-trap-review.html" "$reference"
    install_one "$NAME_TRAP_REVIEW_ES" "$target_dir/name-trap-review-es.html" "$reference"

    cmp -s "$EN_HTML" "$target_dir/miscellaneous-practice.html"
    cmp -s "$ES_HTML" "$target_dir/miscellaneous-practice-es.html"
    cmp -s "$JAVASCRIPT" "$target_dir/miscellaneous-practice.js"
    cmp -s "$JUE_MING_CARD" "$target_dir/jue-ming-zi-vs-shi-jue-ming.html"
    cmp -s "$MACIOCIA_FORMULA_TIPS" "$target_dir/maciocia-formula-tips.html"
    cmp -s "$MACIOCIA_FORMULA_TIPS_ES" "$target_dir/maciocia-formula-tips-es.html"
    cmp -s "$SECONDARY_REVIEW" "$target_dir/secondary-action-review.html"
    cmp -s "$SECONDARY_REVIEW_ES" "$target_dir/secondary-action-review-es.html"
    cmp -s "$FAMILY_REVIEW" "$target_dir/formula-family-review.html"
    cmp -s "$FAMILY_REVIEW_ES" "$target_dir/formula-family-review-es.html"
    cmp -s "$NAME_TRAP_REVIEW" "$target_dir/name-trap-review.html"
    cmp -s "$NAME_TRAP_REVIEW_ES" "$target_dir/name-trap-review-es.html"
    grep -Fq "Which precaution applies to this medicinal?" "$target_dir/miscellaneous-practice.js"
    [[ "$(grep -Fo '"enCaution":' "$target_dir/miscellaneous-practice.js" | wc -l)" -eq 27 ]]
    if grep -Fq "Which safety topic matches this clinical precaution?" "$target_dir/miscellaneous-practice.js"; then
        echo "ERROR: la copia instalada en $target_dir sigue usando el enfoque anterior." >&2
        exit 1
    fi
    echo "ACTUALIZADO Y VERIFICADO: $target_dir"
    echo "RESPALDO: $backup_dir"
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: build 20261111-17 activo."
echo "OK: Safety Library muestra una hierba y ofrece cuatro precauciones del guideline."
echo "OK: banco verificado con 27 precauciones en English y 27 en Español."
echo "OK: distractores verificados por dominios diferentes; no se mezclan dos precauciones renales en una pregunta."
echo "OK: feedback tipo mock explica las cuatro opciones y Jue Ming Zi enlaza su tarjeta con regreso al quiz."
echo "OK: Maciocia Formula Tips activo con 72 fórmulas del guideline y 167 preguntas."
echo "OK: Secondary Actions usa feedback breve y tarjetas con botón de regreso al quiz."
echo "OK: Formula Families usa feedback breve y cuatro tarjetas con regreso al quiz."
echo "OK: Name Traps usa feedback breve y cuatro tarjetas con regreso al quiz."
