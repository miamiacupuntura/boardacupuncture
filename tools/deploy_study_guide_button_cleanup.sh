#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_PATH="blocks/myprogress/study-guide.php"
TARGET="$MOODLE_ROOT/$RELATIVE_PATH"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_study_guide_button_cleanup.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi
for command in curl php install; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done
[[ -f "$MOODLE_ROOT/config.php" ]] || { echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"; exit 1; }
[[ -f "$TARGET" ]] || { echo "ERROR: no existe $TARGET"; exit 1; }

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/study-guide-button-cleanup-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$BACKUP_DIR"

SOURCE="$TEMP_DIR/study-guide.php"
curl -fsSL "$REPOSITORY/$REVISION/$RELATIVE_PATH" -o "$SOURCE"
php -l "$SOURCE" >/dev/null
! grep -Fq 'Board Guideline · Chinese Herbal Medicine' "$SOURCE"
! grep -Fq '$currentguidelineurl' "$SOURCE"

cp -a "$TARGET" "$BACKUP_DIR/study-guide.php"
owner_group="$(stat -c '%u:%g' "$TARGET")"
mode="$(stat -c '%a' "$TARGET")"
install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$SOURCE" "$TARGET"
php -l "$TARGET" >/dev/null
! grep -Fq 'Board Guideline · Chinese Herbal Medicine' "$TARGET"

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" && -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "BOTÓN BOARD GUIDELINE ELIMINADO"
echo "Respaldo: $BACKUP_DIR/study-guide.php"
echo "Prueba: https://www.acupuncturetests.com/blocks/myprogress/study-guide.php?area=herbology&cleanup=$REVISION"
