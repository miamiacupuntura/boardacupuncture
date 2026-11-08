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
    echo "Uso: sudo bash tools/deploy_library_dashboard_button.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi
for command in curl php install; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command"
        exit 1
    }
done
[[ -f "$MOODLE_ROOT/config.php" ]] || {
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
}
[[ -f "$TARGET" ]] || {
    echo "ERROR: no existe $TARGET"
    exit 1
}

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/library-dashboard-button-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$BACKUP_DIR"

SOURCE="$TEMP_DIR/study-guide.php"
curl -fsSL "$REPOSITORY/$REVISION/$RELATIVE_PATH" -o "$SOURCE"
php -l "$SOURCE" >/dev/null
grep -Fq 'data-library-dashboard' "$SOURCE"
grep -Fq '← Back to Dashboard' "$SOURCE"
grep -Fq '← Volver al Dashboard' "$SOURCE"
grep -Fq "new moodle_url('/my/')" "$SOURCE"

cp -a "$TARGET" "$BACKUP_DIR/study-guide.php"
owner_group="$(stat -c '%u:%g' "$TARGET")"
mode="$(stat -c '%a' "$TARGET")"
install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$SOURCE" "$TARGET"

php -l "$TARGET" >/dev/null
grep -Fq 'data-library-dashboard' "$TARGET"
grep -Fq '← Back to Dashboard' "$TARGET"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: botón Back to Dashboard instalado en el mapa de Library."
echo "Respaldo: $BACKUP_DIR/study-guide.php"
echo "Prueba: /blocks/myprogress/study-guide.php?area=herbology&view=library"
