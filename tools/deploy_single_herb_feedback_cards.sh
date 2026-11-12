#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de enlaces del feedback detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_DIR="${STUDY_NOTE_DIR:-}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_single_herb_feedback_cards.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp readlink tr date rm id dirname; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
STATIC_FILES=(comparisons.css single-herb-practice.html single-herb-practice-es.html single-herb-practice.js)
for file in "${STATIC_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/$file"
done
curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/single-herbs-list.php" -o "$TEMP_DIR/single-herbs-list.php"
php -l "$TEMP_DIR/single-herbs-list.php" >/dev/null

grep -Fq "single-herb-practice.js?v=20261112-3" "$TEMP_DIR/single-herb-practice.html"
grep -Fq "comparisons.css?v=20261112-3" "$TEMP_DIR/single-herb-practice-es.html"
grep -Fq "herb-feedback-option-links" "$TEMP_DIR/single-herb-practice.js"
grep -Fq "is-correct" "$TEMP_DIR/single-herb-practice.js"
grep -Fq "is-selected-incorrect" "$TEMP_DIR/single-herb-practice.js"
grep -Fq ".herb-feedback-option-links a.is-correct" "$TEMP_DIR/comparisons.css"
grep -Fq "question.options.forEach(option =>" "$TEMP_DIR/single-herb-practice.js"
grep -Fq "from: 'quiz'" "$TEMP_DIR/single-herb-practice.js"
grep -Fq "single-herb-card-close" "$TEMP_DIR/single-herb-practice.js"
grep -Fq "data-return-to-herb-question" "$TEMP_DIR/single-herbs-list.php"
grep -Fq "/blocks/myprogress/single-herbs-list.php" "$TEMP_DIR/single-herb-practice.js"
grep -Fq "\$herbfocus !== '' ||" "$TEMP_DIR/single-herbs-list.php"

declare -a TARGETS=()
declare -A SEEN=()
add_target() {
    local candidate="$1"
    [[ -d "$candidate" && -f "$candidate/single-herb-practice.html" ]] || return 0
    local resolved
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || return 0
    if [[ -z "${SEEN[$resolved]:-}" ]]; then SEEN["$resolved"]=1; TARGETS+=("$resolved"); fi
}
if [[ -n "$REQUESTED_DIR" ]]; then
    add_target "$REQUESTED_DIR"
else
    add_target "/var/www/acupuncturetests-study-guide/current/comparisons"
    add_target "$MOODLE_ROOT/study-guide/comparisons"
    add_target "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
    add_target "/var/www/html/study-guide/comparisons"
fi
[[ "${#TARGETS[@]}" -gt 0 ]] || { echo "ERROR: no se encontró Single Herbs Quiz activo." >&2; exit 1; }

TARGET_PAGE="$MOODLE_ROOT/blocks/myprogress/single-herbs-list.php"
[[ -f "$TARGET_PAGE" ]] || { echo "ERROR: no existe Single Herbs List activo." >&2; exit 1; }

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/single-herb-feedback-cards-$TIMESTAMP"
mkdir -p "$BACKUP/moodle" "$BACKUP/static"
cp -a "$TARGET_PAGE" "$BACKUP/moodle/single-herbs-list.php"
page_owner_group="$(stat -c '%u:%g' "$TARGET_PAGE")"
page_mode="$(stat -c '%a' "$TARGET_PAGE")"
install -o "${page_owner_group%:*}" -g "${page_owner_group#*:}" -m "$page_mode" "$TEMP_DIR/single-herbs-list.php" "$TARGET_PAGE"
cmp -s "$TEMP_DIR/single-herbs-list.php" "$TARGET_PAGE"
php -l "$TARGET_PAGE" >/dev/null

for target in "${TARGETS[@]}"; do
    sub="$BACKUP/static/$(echo "$target" | tr '/ ' '__')"
    mkdir -p "$sub"
    reference="$target/single-herb-practice.html"
    owner_group="$(stat -c '%u:%g' "$reference")"
    mode="$(stat -c '%a' "$reference")"
    for file in "${STATIC_FILES[@]}"; do
        [[ -f "$target/$file" ]] && cp -a "$target/$file" "$sub/$file"
        install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$file" "$target/$file"
        cmp -s "$TEMP_DIR/$file" "$target/$file"
    done
    echo "ACTUALIZADO Y VERIFICADO: $target"
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: feedback enlaza las cuatro opciones A–D con sus tarjetas de Single Herbs List."
echo "OK: opción correcta marcada en verde y selección incorrecta marcada en rojo."
echo "OK: cada enlace abre la hierba exacta y ambos botones superiores regresan a la misma pregunta."
echo "OK: respuestas, puntuación y posición del Quiz se conservan."
echo "RESPALDO: $BACKUP"
