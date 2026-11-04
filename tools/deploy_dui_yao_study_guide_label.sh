#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: corrección detenida en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="${DUI_YAO_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_dui_yao_study_guide_label.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ -z "$TARGET_DIR" ]]; then
    CANDIDATE_DIRS=(
        "/var/www/acupuncturetests-study-guide/current/comparisons"
        "$MOODLE_ROOT/study-guide/comparisons"
        "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
        "/var/www/html/study-guide/comparisons"
    )
    for candidate in "${CANDIDATE_DIRS[@]}"; do
        if [[ -f "$candidate/dui-yao.html" && -f "$candidate/dui-yao-es.html" ]]; then
            TARGET_DIR="$candidate"
            break
        fi
    done
fi

if [[ -z "$TARGET_DIR" ]]; then
    ACTIVE_INDEX="$(find /var/www -type f -path '*/study-guide/comparisons/dui-yao.html' -print -quit 2>/dev/null || true)"
    if [[ -n "$ACTIVE_INDEX" ]]; then
        TARGET_DIR="$(dirname "$ACTIVE_INDEX")"
    fi
fi

if [[ -z "$TARGET_DIR" || ! -f "$TARGET_DIR/dui-yao.html" || ! -f "$TARGET_DIR/dui-yao-es.html" ]]; then
    echo "ERROR: no se encontró el módulo Duì Yào activo. Indica DUI_YAO_DIR=/ruta/study-guide/comparisons"
    exit 1
fi

if ! command -v php >/dev/null 2>&1; then
    echo "ERROR: se requiere PHP CLI."
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/dui-yao-study-guide-label-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$BACKUP_DIR"

for page in dui-yao.html dui-yao-es.html; do
    cp -a "$TARGET_DIR/$page" "$BACKUP_DIR/$page"
    cp -a "$TARGET_DIR/$page" "$TEMP_DIR/$page"
done

php -r '
$replacements = [
    $argv[1] => ["← Chinese Herbology", "← Study Guide"],
    $argv[2] => ["← Herbología China", "← Guía de estudio"],
];
foreach ($replacements as $path => [$old, $new]) {
    $html = file_get_contents($path);
    if ($html === false) {
        fwrite(STDERR, "ERROR: no se pudo leer $path\n");
        exit(1);
    }
    if (!str_contains($html, $new)) {
        $html = str_replace($old, $new, $html, $count);
        if ($count !== 1) {
            fwrite(STDERR, "ERROR: se esperó una etiqueta anterior en $path; se encontraron $count.\n");
            exit(1);
        }
        file_put_contents($path, $html, LOCK_EX);
    }
    if (!str_contains($html, $new) || str_contains($html, $old)) {
        fwrite(STDERR, "ERROR: la etiqueta no quedó corregida en $path.\n");
        exit(1);
    }
}
' "$TEMP_DIR/dui-yao.html" "$TEMP_DIR/dui-yao-es.html"

for page in dui-yao.html dui-yao-es.html; do
    OWNER_GROUP="$(stat -c '%u:%g' "$TARGET_DIR/$page")"
    FILE_MODE="$(stat -c '%a' "$TARGET_DIR/$page")"
    install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$FILE_MODE" "$TEMP_DIR/$page" "$TARGET_DIR/$page"
done

grep -Fq '← Study Guide' "$TARGET_DIR/dui-yao.html"
grep -Fq '← Guía de estudio' "$TARGET_DIR/dui-yao-es.html"
if grep -Fq '← Chinese Herbology' "$TARGET_DIR/dui-yao.html" \
        || grep -Fq '← Herbología China' "$TARGET_DIR/dui-yao-es.html"; then
    echo "ERROR: todavía existe una etiqueta anterior."
    exit 1
fi

echo "BOTÓN DUÌ YÀO CORREGIDO"
echo "English: ← Study Guide"
echo "Español: ← Guía de estudio"
echo "Respaldo: $BACKUP_DIR"
echo "Prueba: https://www.acupuncturetests.com/study-guide/comparisons/dui-yao.html?label=$REVISION"
