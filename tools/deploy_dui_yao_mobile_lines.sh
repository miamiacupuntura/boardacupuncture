#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="${DUI_YAO_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_dui_yao_mobile_lines.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl install grep stat mktemp; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command." >&2
        exit 1
    }
done

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
    [[ -n "$ACTIVE_INDEX" ]] && TARGET_DIR="$(dirname "$ACTIVE_INDEX")"
fi

if [[ -z "$TARGET_DIR" || ! -f "$TARGET_DIR/dui-yao.html" || ! -f "$TARGET_DIR/dui-yao-es.html" ]]; then
    echo "ERROR: no se encontró el módulo Duì Yào activo." >&2
    echo "Indica su ubicación con DUI_YAO_DIR=/ruta/study-guide/comparisons." >&2
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/dui-yao-mobile-lines-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$BACKUP_DIR"

FILES=("dui-yao.html" "dui-yao-es.html" "dui-yao-mobile.css")
for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/$file"
done

grep -Fq 'dui-yao-mobile.css?v=20261105-2' "$TEMP_DIR/dui-yao.html"
grep -Fq 'dui-yao-mobile.css?v=20261105-2' "$TEMP_DIR/dui-yao-es.html"
grep -Fq '[data-student-onboarding] .comparison-card' "$TEMP_DIR/dui-yao-mobile.css"

for file in "${FILES[@]}"; do
    [[ -f "$TARGET_DIR/$file" ]] && cp -a "$TARGET_DIR/$file" "$BACKUP_DIR/"
done

OWNER_GROUP="$(stat -c '%u:%g' "$TARGET_DIR/dui-yao.html")"
for file in "${FILES[@]}"; do
    install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m 0644 "$TEMP_DIR/$file" "$TARGET_DIR/$file"
done

grep -Fq 'dui-yao-mobile.css?v=20261105-2' "$TARGET_DIR/dui-yao.html"
grep -Fq '[data-student-onboarding] .comparison-card' "$TARGET_DIR/dui-yao-mobile.css"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "DISEÑO MÓVIL DUÌ YÀO INSTALADO"
echo "Los cuatro bloques de orientación ahora son filas compactas en pantallas de hasta 720 px."
echo "Respaldo: $BACKUP_DIR"
echo "Prueba: https://www.acupuncturetests.com/study-guide/comparisons/dui-yao.html?mobile=$REVISION"
