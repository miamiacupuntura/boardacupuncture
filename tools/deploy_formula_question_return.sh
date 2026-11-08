#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
STUDY_NOTE_DIR="${STUDY_NOTE_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_formula_question_return.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

for command in curl php install tar; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command"
        exit 1
    }
done

if [[ -z "$STUDY_NOTE_DIR" ]]; then
    for candidate in \
        "/var/www/acupuncturetests-study-guide/current/comparisons" \
        "$MOODLE_ROOT/study-guide/comparisons" \
        "$(dirname "$MOODLE_ROOT")/study-guide/comparisons" \
        "/var/www/html/study-guide/comparisons"; do
        if [[ -d "$candidate" && -f "$candidate/comparisons.css" ]]; then
            STUDY_NOTE_DIR="$candidate"
            break
        fi
    done
fi
[[ -n "$STUDY_NOTE_DIR" && -d "$STUDY_NOTE_DIR" ]] || {
    echo "ERROR: no se encontró el directorio activo de comparisons. Indica STUDY_NOTE_DIR=/ruta/comparisons"
    exit 1
}

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/formula-question-return-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/static" "$TEMP_DIR/tools" "$BACKUP_DIR"

STATIC_FILES=(
    "formula-question-bank.html"
    "formula-question-bank-es.html"
    "formula-question-bank.js"
    "library-navigation.js"
    "question-study-bank.css"
)

for file in "${STATIC_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/static/$file"
done
curl -fsSL "$REPOSITORY/$REVISION/tools/inject_library_navigation.php" \
    -o "$TEMP_DIR/tools/inject_library_navigation.php"

php -l "$TEMP_DIR/tools/inject_library_navigation.php" >/dev/null
grep -Fq 'formula-question-bank.js?v=20261107-4' "$TEMP_DIR/static/formula-question-bank.html"
grep -Fq 'question-study-bank.css?v=20261108-3' "$TEMP_DIR/static/formula-question-bank.html"
grep -Fq "profileUrl.searchParams.set('return', 'formula-question-bank')" "$TEMP_DIR/static/formula-question-bank.js"
grep -Fq 'formula-question-' "$TEMP_DIR/static/formula-question-bank.js"
grep -Fq 'comparison-back--question-return' "$TEMP_DIR/static/library-navigation.js"
grep -Fq 'formula-question-bank(?:-es)?' "$TEMP_DIR/static/library-navigation.js"
grep -Fq '/study-guide/comparisons/library-navigation.js?v=20261108-14' \
    "$TEMP_DIR/tools/inject_library_navigation.php"

(cd "$STUDY_NOTE_DIR" && tar -czf "$BACKUP_DIR/comparison-html.tar.gz" -- *.html)
for file in "${STATIC_FILES[@]}"; do
    if [[ -f "$STUDY_NOTE_DIR/$file" ]]; then
        cp -a "$STUDY_NOTE_DIR/$file" "$BACKUP_DIR/$file"
    fi
done

reference_file="$STUDY_NOTE_DIR/comparisons.css"
owner_group="$(stat -c '%u:%g' "$reference_file")"
mode="$(stat -c '%a' "$reference_file")"
for file in "${STATIC_FILES[@]}"; do
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/static/$file" "$STUDY_NOTE_DIR/$file"
done

php "$TEMP_DIR/tools/inject_library_navigation.php" --write --root="$STUDY_NOTE_DIR"
php "$TEMP_DIR/tools/inject_library_navigation.php" --check --root="$STUDY_NOTE_DIR"

grep -Fq 'formula-question-bank.js?v=20261107-4' "$STUDY_NOTE_DIR/formula-question-bank.html"
grep -Fq 'question-study-bank.css?v=20261108-3' "$STUDY_NOTE_DIR/formula-question-bank.html"
grep -Fq "profileUrl.searchParams.set('return', 'formula-question-bank')" \
    "$STUDY_NOTE_DIR/formula-question-bank.js"
grep -Fq 'comparison-back--question-return' "$STUDY_NOTE_DIR/library-navigation.js"
grep -Fq '/study-guide/comparisons/library-navigation.js?v=20261108-14' \
    "$STUDY_NOTE_DIR/formula-navigator-02-gui-zhi-tang.html"

echo "OK: las fichas de fórmulas regresan a la pregunta de origen."
echo "OK: se eliminó el botón Back to Formulas Practice Exams."
echo "Respaldo: $BACKUP_DIR"
