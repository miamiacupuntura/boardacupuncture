#!/usr/bin/env bash
set -euo pipefail

# Add a direct categorized Mock Exam button to the public Duì Yào Quiz without
# deploying unrelated Study Guide or Moodle files.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_DIR="${STUDY_NOTE_DIR:-}"
FILES=(dui-yao-practice.html dui-yao-practice-es.html dui-yao-practice.js)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_dui_yao_mock_button.sh COMMIT_DE_40_CARACTERES" >&2
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
BACKUP_DIR="$BACKUP_ROOT/dui-yao-mock-button-$TIMESTAMP"
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
    echo "ERROR: despliegue del botón Mock de Duì Yào detenido en la línea $line (código $status)." >&2
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

grep -Fq 'data-dui-mock' "$TEMP_DIR/dui-yao-practice.html"
grep -Fq 'mockcategory=dui_yao' "$TEMP_DIR/dui-yao-practice.html"
grep -Fq 'data-dui-mock' "$TEMP_DIR/dui-yao-practice-es.html"
grep -Fq 'dui-yao-practice.js?v=20261120-1' "$TEMP_DIR/dui-yao-practice.html"
grep -Fq 'dui-yao-practice.js?v=20261120-1' "$TEMP_DIR/dui-yao-practice-es.html"
grep -Fq 'function syncMockLink()' "$TEMP_DIR/dui-yao-practice.js"
grep -Fq "mockUrl.searchParams.set('mockcategory', 'dui_yao')" "$TEMP_DIR/dui-yao-practice.js"

declare -a TARGETS=()
declare -A SEEN=()
add_target() {
    local candidate="$1"
    [[ -d "$candidate" && -f "$candidate/dui-yao-practice.html" ]] || return 0
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
    echo "ERROR: no se encontró la instalación activa de Dui Yao Quiz." >&2
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
        staged="$target/.$file.dui-mock.$$"
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
    grep -Fq 'data-dui-mock' "$target/dui-yao-practice.html"
    grep -Fq 'function syncMockLink()' "$target/dui-yao-practice.js"
    echo "ACTUALIZADO Y VERIFICADO: $target"
done

echo "OK: Dui Yao Quiz muestra Start Quiz y Mock Exam."
echo "OK: Mock Exam abre exclusivamente la categoría Duì Yào y usa la cantidad seleccionada."
echo "OK: archivos públicos instalados en ${#TARGETS[@]} ruta(s)."
echo "OK: revisión instalada: $REVISION"
echo "RESPALDO: $BACKUP_DIR"
