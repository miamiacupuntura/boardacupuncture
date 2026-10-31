#!/usr/bin/env bash

set -euo pipefail

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="${GUIDELINE_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_herbal_guideline_search.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

if [[ -z "$TARGET_DIR" ]]; then
    CANDIDATE_DIRS=(
        "/var/www/acupuncturetests-study-guide/current/guidelines"
        "$MOODLE_ROOT/study-guide/guidelines"
        "$(dirname "$MOODLE_ROOT")/study-guide/guidelines"
        "/var/www/html/study-guide/guidelines"
    )
    for candidate in "${CANDIDATE_DIRS[@]}"; do
        if [[ -f "$candidate/guideline-return.js" && -f "$candidate/guidelines.css" ]]; then
            TARGET_DIR="$candidate"
            break
        fi
    done
fi

if [[ -z "$TARGET_DIR" ]]; then
    ACTIVE_GUIDE="$(find /var/www -type f -path '*/study-guide/guidelines/guideline-return.js' -print -quit 2>/dev/null || true)"
    if [[ -n "$ACTIVE_GUIDE" ]]; then
        TARGET_DIR="$(dirname "$ACTIVE_GUIDE")"
    fi
fi

if [[ -z "$TARGET_DIR" || ! -f "$TARGET_DIR/guideline-return.js" || ! -f "$TARGET_DIR/guidelines.css" ]]; then
    echo "ERROR: no se encontró la guía activa. Puedes indicar su ruta con GUIDELINE_DIR=/ruta/study-guide/guidelines"
    exit 1
fi

echo "Guía detectada: $TARGET_DIR"

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/boardacupuncture-guideline-search-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

for asset in guidelines.css guideline-return.js guideline-search.js; do
    curl -fsSL \
        "$REPOSITORY/$REVISION/study-guide/guidelines/$asset" \
        -o "$TEMP_DIR/$asset"
done

for page in chinese-herbal-medicine.html chinese-herbal-medicine-es.html; do
    if [[ ! -f "$TARGET_DIR/$page" ]]; then
        echo "ERROR: falta la página activa $TARGET_DIR/$page"
        exit 1
    fi
    cp -a "$TARGET_DIR/$page" "$TEMP_DIR/$page"
    sed -E -i \
        -e 's/guidelines\.css\?v=20261031-[0-9]+/guidelines.css?v=20261031-4/g' \
        -e 's/guideline-return\.js\?v=20261031-[0-9]+/guideline-return.js?v=20261031-4/g' \
        "$TEMP_DIR/$page"
done

grep -q "guide-smart-search" "$TEMP_DIR/guidelines.css"
grep -q "guideline-search.js?v=20261031-1" "$TEMP_DIR/guideline-return.js"
grep -q "Search the complete guide" "$TEMP_DIR/guideline-search.js"
grep -q "Mu Li San" "$TEMP_DIR/guideline-search.js"
grep -q "formulas-study-guide-data.json" "$TEMP_DIR/guideline-search.js"
grep -q "guidelines.css?v=20261031-4" "$TEMP_DIR/chinese-herbal-medicine.html"
grep -q "guideline-return.js?v=20261031-4" "$TEMP_DIR/chinese-herbal-medicine.html"
grep -q "guideline-return.js?v=20261031-4" "$TEMP_DIR/chinese-herbal-medicine-es.html"

mkdir -p "$BACKUP_DIR"
cp -a "$TARGET_DIR/guidelines.css" "$BACKUP_DIR/"
cp -a "$TARGET_DIR/guideline-return.js" "$BACKUP_DIR/"
if [[ -f "$TARGET_DIR/guideline-search.js" ]]; then
    cp -a "$TARGET_DIR/guideline-search.js" "$BACKUP_DIR/"
fi
cp -a "$TARGET_DIR/chinese-herbal-medicine.html" "$BACKUP_DIR/"
cp -a "$TARGET_DIR/chinese-herbal-medicine-es.html" "$BACKUP_DIR/"

OWNER="$(stat -c '%U' "$TARGET_DIR/guideline-return.js")"
GROUP="$(stat -c '%G' "$TARGET_DIR/guideline-return.js")"

for asset in guidelines.css guideline-return.js guideline-search.js; do
    install -m 0644 -o "$OWNER" -g "$GROUP" "$TEMP_DIR/$asset" "$TARGET_DIR/$asset"
done
for page in chinese-herbal-medicine.html chinese-herbal-medicine-es.html; do
    install -m 0644 -o "$OWNER" -g "$GROUP" "$TEMP_DIR/$page" "$TARGET_DIR/$page"
done

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

grep -q "guide-smart-search" "$TARGET_DIR/guidelines.css"
grep -q "guideline-search.js?v=20261031-1" "$TARGET_DIR/guideline-return.js"
grep -q "Mu Li San" "$TARGET_DIR/guideline-search.js"
grep -q "guideline-return.js?v=20261031-4" "$TARGET_DIR/chinese-herbal-medicine.html"
grep -q "guideline-return.js?v=20261031-4" "$TARGET_DIR/chinese-herbal-medicine-es.html"

echo "BÚSQUEDA INTELIGENTE INSTALADA EN LA GUÍA DE HERBOLOGÍA"
echo "Busca secciones, 304 hierbas, fórmulas Board y fórmulas Bensky"
echo "Respaldo: $BACKUP_DIR"
