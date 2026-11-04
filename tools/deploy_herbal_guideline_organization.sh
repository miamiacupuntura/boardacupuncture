#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO: $BASH_COMMAND (código $status)" >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
GUIDELINE_DIR="${GUIDELINE_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"
FILES=(
    "chinese-herbal-medicine.html"
    "chinese-herbal-medicine-es.html"
    "guidelines.css"
    "guideline-return.js"
    "guideline-organization.js"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_herbal_guideline_organization.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ -z "$GUIDELINE_DIR" ]]; then
    GUIDELINE_CANDIDATES=(
        "/var/www/acupuncturetests-study-guide/current/guidelines"
        "/var/www/html/moodle/study-guide/guidelines"
        "/var/www/html/study-guide/guidelines"
    )
    for candidate in "${GUIDELINE_CANDIDATES[@]}"; do
        if [[ -f "$candidate/chinese-herbal-medicine.html" ]]; then
            GUIDELINE_DIR="$candidate"
            break
        fi
    done
fi

if [[ -z "$GUIDELINE_DIR" ]]; then
    ACTIVE_GUIDELINE="$(find /var/www -type f -path '*/study-guide/guidelines/chinese-herbal-medicine.html' -print -quit 2>/dev/null || true)"
    if [[ -n "$ACTIVE_GUIDELINE" ]]; then
        GUIDELINE_DIR="$(dirname "$ACTIVE_GUIDELINE")"
    fi
fi

if [[ -z "$GUIDELINE_DIR" || ! -d "$GUIDELINE_DIR" ]]; then
    echo "ERROR: no se encontró la guía pública activa. Indica su ruta con GUIDELINE_DIR=/ruta/study-guide/guidelines"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/herbal-guideline-organization-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

validate_page() {
    local page="$1"
    if [[ "$(grep -o 'data-official-formula-id=' "$page" | wc -l)" -ne 181 ]]; then
        echo "ERROR: $page no conserva las 181 fórmulas oficiales"
        exit 1
    fi
    if [[ "$(grep -o 'data-formula-core-profile=' "$page" | wc -l)" -ne 63 ]]; then
        echo "ERROR: $page no conserva las 63 fichas detalladas"
        exit 1
    fi
    if [[ "$(grep -o 'guide-herb-roster__card-link' "$page" | wc -l)" -ne 304 ]]; then
        echo "ERROR: $page no conserva las 304 hierbas"
        exit 1
    fi
    if [[ "$(grep -o 'data-dui-action=' "$page" | wc -l)" -ne 51 ]]; then
        echo "ERROR: $page no conserva las 51 parejas Duì Yào"
        exit 1
    fi
    if [[ "$(grep -o '<details[^>]*guide-organized-panel[^>]*>' "$page" | wc -l)" -ne 10 ]]; then
        echo "ERROR: $page no contiene los 10 paneles organizados"
        exit 1
    fi
    grep -Fq 'id="organized-guide-map"' "$page"
    grep -Fq 'id="core-study-route"' "$page"
    grep -Fq 'guideline-organization.js?v=20261103-1' "$page"
}

echo "Descargando la guía organizada desde $REVISION..."
for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/guidelines/$file" -o "$TEMP_DIR/$file"
done

validate_page "$TEMP_DIR/chinese-herbal-medicine.html"
validate_page "$TEMP_DIR/chinese-herbal-medicine-es.html"
grep -Fq '.guide-content-map {' "$TEMP_DIR/guidelines.css"
grep -Fq '.guide-organized-panel {' "$TEMP_DIR/guidelines.css"
grep -Fq "details.guide-organized-panel" "$TEMP_DIR/guideline-organization.js"

mkdir -p "$BACKUP_DIR"
for file in "${FILES[@]}"; do
    target="$GUIDELINE_DIR/$file"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP_DIR/$file"
        owner_group="$(stat -c '%u:%g' "$target")"
        file_mode="$(stat -c '%a' "$target")"
    else
        owner_group="$(stat -c '%u:%g' "$GUIDELINE_DIR")"
        file_mode="644"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$file_mode" "$TEMP_DIR/$file" "$target"
done

validate_page "$GUIDELINE_DIR/chinese-herbal-medicine.html"
validate_page "$GUIDELINE_DIR/chinese-herbal-medicine-es.html"

echo "GUÍA DE MEDICINA HERBAL ORGANIZADA"
echo "Todo el contenido permanece disponible en 10 paneles plegables"
echo "181 fórmulas, 304 hierbas, 51 parejas y 63 fichas verificadas"
echo "Mapa de lectura, enlaces directos y apertura automática verificados"
echo "Guía actualizada: $GUIDELINE_DIR"
echo "Respaldo: $BACKUP_DIR"
