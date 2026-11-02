#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO: $BASH_COMMAND (código $status)" >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"
PHP_BIN="${PHP_BIN:-}"
RELATIVE_PATH="blocks/myprogress/single-herbs-category-drain-dampness.php"
TARGET="$MOODLE_ROOT/$RELATIVE_PATH"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_wind_damp_chapter.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

if [[ ! -f "$TARGET" ]]; then
    echo "ERROR: no se encontró la categoría activa en $TARGET"
    exit 1
fi

if [[ -z "$PHP_BIN" ]]; then
    for candidate in php php8.4 php8.3 php8.2 php8.1 php8.0; do
        if command -v "$candidate" >/dev/null 2>&1; then
            PHP_BIN="$(command -v "$candidate")"
            break
        fi
    done
fi

if [[ -z "$PHP_BIN" || ! -x "$PHP_BIN" ]]; then
    echo "ERROR: no se encontró PHP CLI. Indica la ruta con PHP_BIN=/ruta/al/php"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
TEMP_FILE="$TEMP_DIR/single-herbs-category-drain-dampness.php"
BACKUP_DIR="$BACKUP_ROOT/wind-damp-chapter-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

echo "Moodle detectado: $MOODLE_ROOT"
echo "PHP CLI detectado: $PHP_BIN"
echo "Descargando capítulo Wind-Damp desde $REVISION..."

curl -fsSL "$REPOSITORY/$REVISION/$RELATIVE_PATH" -o "$TEMP_FILE"

"$PHP_BIN" -l "$TEMP_FILE" >/dev/null
grep -q "Complete chapter atlas: 33 source monographs" "$TEMP_FILE"
grep -q "Chapter 5 selection framework" "$TEMP_FILE"
grep -q "Snake products and sloughed skin" "$TEMP_FILE"
grep -q "scanned pp. 323–369" "$TEMP_FILE"

QUESTION_COUNT="$(grep -c "^    \[\$damptext(" "$TEMP_FILE")"
if [[ "$QUESTION_COUNT" -ne 12 ]]; then
    echo "ERROR: se esperaban 12 preguntas y se detectaron $QUESTION_COUNT"
    exit 1
fi

CHAPTER_HERB_COUNT="$(grep -c "'core' => \(true\|false\)\]," "$TEMP_FILE")"
if [[ "$CHAPTER_HERB_COUNT" -ne 33 ]]; then
    echo "ERROR: se esperaban 33 monografías y se detectaron $CHAPTER_HERB_COUNT"
    exit 1
fi

mkdir -p "$BACKUP_DIR"
cp -a "$TARGET" "$BACKUP_DIR/"

OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
FILE_MODE="$(stat -c '%a' "$TARGET")"
install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$FILE_MODE" "$TEMP_FILE" "$TARGET"

"$PHP_BIN" -l "$TARGET" >/dev/null

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    "$PHP_BIN" "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "CAPÍTULO WIND-DAMP INSTALADO CORRECTAMENTE"
echo "33 monografías fuente"
echo "9 perfiles Board"
echo "12 preguntas de repaso"
echo "Respaldo: $BACKUP_DIR"
