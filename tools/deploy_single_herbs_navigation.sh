#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
PHP_BIN="${PHP_BIN:-}"
RELATIVE_PATH="blocks/myprogress/single-herbs-study-guide.php"
TARGET="$MOODLE_ROOT/$RELATIVE_PATH"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_single_herbs_navigation.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

if [[ ! -f "$TARGET" ]]; then
    echo "ERROR: no se encontró la guía activa en $TARGET"
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
TEMP_FILE="$TEMP_DIR/single-herbs-study-guide.php"
BACKUP_DIR="$BACKUP_ROOT/single-herbs-navigation-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

echo "Descargando Single Herbs desde $REVISION..."
curl -fsSL "$REPOSITORY/$REVISION/$RELATIVE_PATH" -o "$TEMP_FILE"

"$PHP_BIN" -l "$TEMP_FILE" >/dev/null
grep -Fq "range(1, 34)" "$TEMP_FILE"
grep -Fq "rendersingleherbcategorynavigation('top')" "$TEMP_FILE"
grep -Fq "rendersingleherbcategorynavigation('bottom')" "$TEMP_FILE"
grep -Fq "data-single-herbs-category-jump" "$TEMP_FILE"
grep -Fq "34-category Single Herbs path" "$TEMP_FILE"
grep -Fq '$dashboardurl = new moodle_url' "$TEMP_FILE"
grep -Fq '← Back to Dashboard' "$TEMP_FILE"
grep -Fq "\$ises ? 'English' : 'Español'" "$TEMP_FILE"
! grep -Fq '<div class="single-herbs-guide__module-switcher"' "$TEMP_FILE"
! grep -Fq '← Board Guideline · Chinese Herbal Medicine' "$TEMP_FILE"
! grep -Fq '← Library' "$TEMP_FILE"
! grep -Fq '← Study Guide' "$TEMP_FILE"

mkdir -p "$BACKUP_DIR"
cp -a "$TARGET" "$BACKUP_DIR/"

OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
FILE_MODE="$(stat -c '%a' "$TARGET")"
install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$FILE_MODE" "$TEMP_FILE" "$TARGET"

"$PHP_BIN" -l "$TARGET" >/dev/null
cmp -s "$TEMP_FILE" "$TARGET"

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" && -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- "$PHP_BIN" "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        "$PHP_BIN" "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "SINGLE HERBS: NAVEGACIÓN CONTINUA INSTALADA"
echo "Categorías: 34 + Repaso final"
echo "Controles: anterior con nombre, selector funcional y siguiente con nombre"
echo "Ubicación: parte superior e inferior de cada categoría"
echo "Cabecera: idioma y Dashboard; botones duplicados eliminados"
echo "Respaldo: $BACKUP_DIR/single-herbs-study-guide.php"
echo "Prueba: https://www.acupuncturetests.com/blocks/myprogress/single-herbs-study-guide.php?focus=2&nav=$REVISION"
