#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: corrección de navegación detenida en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/practice-navigation-$(date +%Y%m%d-%H%M%S)"
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TEMP_DIR"' EXIT

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_practice_navigation_fix.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

for command in curl find install readlink cmp; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done

FILES=(
    "comparisons.css"
    "formula-navigator.html"
    "formula-navigator-es.html"
    "formula-navigator.js"
    "formula-practice.html"
    "formula-practice-es.html"
    "formula-practice.js"
    "formula-question-bank.html"
    "formula-question-bank-es.html"
    "formula-question-bank.js"
    "single-herb-practice.html"
    "single-herb-practice-es.html"
    "single-herb-practice.js"
    "study-return-navigation.js"
)

mkdir -p "$TEMP_DIR/static" "$BACKUP_DIR"
for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" \
        -o "$TEMP_DIR/static/$file"
done

grep -Fq 'class="practice-study-route"' "$TEMP_DIR/static/formula-practice.html"
grep -Fq 'Herbology Practice</a><span aria-hidden="true">›</span><a href="/blocks/menu/herbology-board.php?track=formulas">Formulas' "$TEMP_DIR/static/formula-practice.html"
grep -Fq '← Back to Dashboard' "$TEMP_DIR/static/formula-practice.html"
grep -Fq 'class="practice-study-route"' "$TEMP_DIR/static/single-herb-practice.html"
! grep -Fq 'study-return-navigation.js' "$TEMP_DIR/static/formula-practice.js"
! grep -Fq '← Library' "$TEMP_DIR/static/formula-practice.html"
! grep -Fq '← Study Guide' "$TEMP_DIR/static/formula-practice.html"

declare -a discovered=()
declare -A seen=()

add_directory() {
    local candidate="$1"
    [[ -d "$candidate" && -f "$candidate/formula-practice.html" && -f "$candidate/comparisons.css" ]] || return 0
    local resolved
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" && -z "${seen[$resolved]:-}" ]] || return 0
    seen[$resolved]=1
    discovered+=("$resolved")
}

if [[ -n "${STUDY_NOTE_DIR:-}" ]]; then
    add_directory "$STUDY_NOTE_DIR"
fi

for candidate in \
    "/var/www/acupuncturetests-study-guide/current/comparisons" \
    "/var/www/html/moodle/study-guide/comparisons" \
    "/var/www/html/study-guide/comparisons"; do
    add_directory "$candidate"
done

while IFS= read -r file; do
    add_directory "$(dirname "$file")"
done < <(find /var/www -type f -path '*/comparisons/formula-practice.html' -print 2>/dev/null | sort -u)

if [[ "${#discovered[@]}" -eq 0 ]]; then
    echo "ERROR: no se encontró ninguna copia instalada de formula-practice.html bajo /var/www."
    echo "Puedes indicar la ruta con STUDY_NOTE_DIR=/ruta/comparisons."
    exit 1
fi

echo "Copias de Practice encontradas: ${#discovered[@]}"
for index in "${!discovered[@]}"; do
    directory="${discovered[$index]}"
    backup="$BACKUP_DIR/target-$((index + 1))"
    mkdir -p "$backup"
    echo "Actualizando: $directory"

    for file in "${FILES[@]}"; do
        target="$directory/$file"
        reference="$target"
        [[ -f "$reference" ]] || reference="$directory/comparisons.css"
        [[ -f "$target" ]] && cp -a "$target" "$backup/$file"
        owner_group="$(stat -c '%u:%g' "$reference")"
        mode="$(stat -c '%a' "$reference")"
        install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
            "$TEMP_DIR/static/$file" "$target"
        cmp -s "$TEMP_DIR/static/$file" "$target" || {
            echo "ERROR: $target no coincide con la versión descargada."
            exit 1
        }
    done

    grep -Fq 'class="practice-study-route"' "$directory/formula-practice.html"
    grep -Fq '← Back to Dashboard' "$directory/formula-practice.html"
    ! grep -Fq '← Library' "$directory/formula-practice.html"
    ! grep -Fq '← Study Guide' "$directory/formula-practice.html"
    ! grep -Fq 'study-return-navigation.js' "$directory/formula-practice.js"
done

echo "NAVEGACIÓN DE PRACTICE CORREGIDA EN TODAS LAS COPIAS"
echo "Respaldo: $BACKUP_DIR"
printf 'Directorio actualizado: %s\n' "${discovered[@]}"
echo "Prueba sin caché: https://www.acupuncturetests.com/study-guide/comparisons/formula-practice.html?size=25&nav=$REVISION"
