#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_remove_module_school_panel.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi
for command in curl php install; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done
[[ -f "$MOODLE_ROOT/config.php" ]] || { echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"; exit 1; }

FILES=(
    "theme/moove/javascript/study-guide-dashboard.js"
    "theme/moove/layout/drawers.php"
)
TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/remove-module-school-panel-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$BACKUP_DIR"

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    [[ -f "$target" ]] || { echo "ERROR: no existe $target"; exit 1; }
    mkdir -p "$TEMP_DIR/$(dirname "$relative")" "$BACKUP_DIR/$(dirname "$relative")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$TEMP_DIR/$relative"
done

php -l "$TEMP_DIR/theme/moove/layout/drawers.php" >/dev/null
! grep -Fq '    addModuleSchoolPortal();' "$TEMP_DIR/theme/moove/javascript/study-guide-dashboard.js"
grep -Fq "'v' => '20261105-2'" "$TEMP_DIR/theme/moove/layout/drawers.php"

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    cp -a "$target" "$BACKUP_DIR/$relative"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$relative" "$target"
done

php -l "$MOODLE_ROOT/theme/moove/layout/drawers.php" >/dev/null
! grep -Fq '    addModuleSchoolPortal();' "$MOODLE_ROOT/theme/moove/javascript/study-guide-dashboard.js"

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" && -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "PANEL MODULE SCHOOL ELIMINADO DEL DASHBOARD"
echo "Respaldo: $BACKUP_DIR"
echo "Prueba: https://www.acupuncturetests.com/my/?modulepanel=$REVISION"
