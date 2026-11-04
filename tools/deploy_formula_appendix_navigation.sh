#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO: $BASH_COMMAND (código $status)" >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
GUIDELINE_DIR="${GUIDELINE_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"
PREVIEW_FILE="formulas-appendix-c-2026-preview.html"
PREVIEW_PATH="study-guide/guidelines/$PREVIEW_FILE"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_formula_appendix_navigation.sh COMMIT_DE_40_CARACTERES"
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
BACKUP_DIR="$BACKUP_ROOT/formula-appendix-navigation-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

validate_preview() {
    local preview="$1"
    if [[ "$(grep -o 'data-search=' "$preview" | wc -l)" -ne 181 ]]; then
        echo "ERROR: el índice de fórmulas no contiene los 181 registros oficiales"
        exit 1
    fi
    grep -Fq 'class="page-nav"' "$preview"
    grep -Fq 'href="/my/">← Dashboard</a>' "$preview"
    grep -Fq 'href="/blocks/myprogress/formulas-study-guide.php?lang=es">Guía de estudio de fórmulas →</a>' "$preview"
}

echo "Descargando navegación del Appendix C desde $REVISION..."
curl -fsSL "$REPOSITORY/$REVISION/$PREVIEW_PATH" -o "$TEMP_DIR/$PREVIEW_FILE"
validate_preview "$TEMP_DIR/$PREVIEW_FILE"

TARGET="$GUIDELINE_DIR/$PREVIEW_FILE"
mkdir -p "$BACKUP_DIR"
if [[ -f "$TARGET" ]]; then
    cp -a "$TARGET" "$BACKUP_DIR/$PREVIEW_FILE"
    OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
    FILE_MODE="$(stat -c '%a' "$TARGET")"
else
    OWNER_GROUP="$(stat -c '%u:%g' "$GUIDELINE_DIR")"
    FILE_MODE="644"
fi

install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$FILE_MODE" "$TEMP_DIR/$PREVIEW_FILE" "$TARGET"
validate_preview "$TARGET"

echo "NAVEGACIÓN DEL APPENDIX C INSTALADA"
echo "Botón Dashboard: /my/"
echo "Botón Guía de estudio: /blocks/myprogress/formulas-study-guide.php?lang=es"
echo "Página actualizada: $TARGET"
echo "Respaldo: $BACKUP_DIR"
