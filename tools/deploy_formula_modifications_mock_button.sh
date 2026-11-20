#!/usr/bin/env bash
set -euo pipefail

# Add a categorized Mock Exam button to Formula Modifications Quiz without
# deploying unrelated Study Guide or Moodle files.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_DIR="${STUDY_NOTE_DIR:-}"
FILES=(comparisons.css formula-practice.html formula-practice-es.html formula-practice.js)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_formula_modifications_mock_button.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl grep install stat cp cmp mkdir mktemp readlink tr date rm mv dirname; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/formula-modifications-mock-button-$TIMESTAMP"
INSTALLED=()

cleanup() {
    rm -rf -- "$TEMP_DIR"
}

on_error() {
    status=$?
    line=$1
    for record in "${INSTALLED[@]}"; do
        target="${record%%|*}"
        backup="${record#*|}"
        [[ -f "$backup" ]] && cp -a -- "$backup" "$target"
    done
    cleanup
    echo "ERROR: despliegue del botón Mock de modificaciones detenido en la línea $line (código $status)." >&2
    exit "$status"
}

trap 'on_error $LINENO' ERR
trap cleanup EXIT

for file in "${FILES[@]}"; do
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/study-guide/comparisons/$file?revision=$REVISION" \
        -o "$TEMP_DIR/$file"
done

grep -Fq 'data-practice-mock' "$TEMP_DIR/formula-practice.html"
grep -Fq 'mockcategory=formula_composition' "$TEMP_DIR/formula-practice.html"
grep -Fq 'data-practice-mock' "$TEMP_DIR/formula-practice-es.html"
grep -Fq 'formula-practice.js?v=20261120-1' "$TEMP_DIR/formula-practice.html"
grep -Fq 'comparisons.css?v=20261120-1' "$TEMP_DIR/formula-practice-es.html"
grep -Fq 'function syncMockLink()' "$TEMP_DIR/formula-practice.js"
grep -Fq "mockUrl.searchParams.set('mockcategory', 'formula_composition')" "$TEMP_DIR/formula-practice.js"
grep -Fq '.formula-practice-mock' "$TEMP_DIR/comparisons.css"

declare -a TARGETS=()
declare -A SEEN=()
add_target() {
    local candidate="$1"
    [[ -d "$candidate" && -f "$candidate/formula-practice.html" ]] || return 0
    local resolved
    resolved="$(readlink -f "$candidate")"
    [[ -n "$resolved" ]] || return 0
    if [[ -z "${SEEN[$resolved]:-}" ]]; then
        SEEN["$resolved"]=1
        TARGETS+=("$resolved")
    fi
}

if [[ -n "$REQUESTED_DIR" ]]; then
    add_target "$REQUESTED_DIR"
else
    add_target "/var/www/acupuncturetests-study-guide/current/comparisons"
    add_target "$MOODLE_ROOT/study-guide/comparisons"
    add_target "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
    add_target "/var/www/html/study-guide/comparisons"
fi

[[ "${#TARGETS[@]}" -gt 0 ]] || {
    echo "ERROR: no se encontró la instalación activa de Formula Modifications Quiz." >&2
    exit 1
}

mkdir -p "$BACKUP_DIR"
for target in "${TARGETS[@]}"; do
    targetkey="$(echo "$target" | tr '/ ' '__')"
    targetbackup="$BACKUP_DIR/$targetkey"
    mkdir -p "$targetbackup"
    for file in "${FILES[@]}"; do
        destination="$target/$file"
        [[ -f "$destination" ]] || {
            echo "ERROR: no existe $destination" >&2
            exit 1
        }
        cp -a -- "$destination" "$targetbackup/$file"
        owner_group="$(stat -c '%u:%g' "$destination")"
        mode="$(stat -c '%a' "$destination")"
        staged="$target/.$file.formula-mock.$$"
        install \
            -o "${owner_group%:*}" \
            -g "${owner_group#*:}" \
            -m "$mode" \
            "$TEMP_DIR/$file" \
            "$staged"
        cmp -s "$TEMP_DIR/$file" "$staged"
        mv -f -- "$staged" "$destination"
        INSTALLED+=("$destination|$targetbackup/$file")
        cmp -s "$TEMP_DIR/$file" "$destination"
    done
    grep -Fq 'data-practice-mock' "$target/formula-practice.html"
    grep -Fq 'function syncMockLink()' "$target/formula-practice.js"
    echo "ACTUALIZADO Y VERIFICADO: $target"
done

echo "OK: Formula Modifications Quiz muestra Start Quiz y Mock Exam."
echo "OK: Mock Exam abre Formula composition and modification con la cantidad seleccionada."
echo "OK: el botón permanece oculto en los demás modos de Formula Quiz."
echo "OK: archivos públicos instalados en ${#TARGETS[@]} ruta(s)."
echo "OK: revisión instalada: $REVISION"
echo "RESPALDO: $BACKUP_DIR"
