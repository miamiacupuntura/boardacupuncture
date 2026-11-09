#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/board-hub-dashboard-$(date +%Y%m%d-%H%M%S)"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_board_hub_dashboard.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl php install grep stat cp; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done
[[ -f "$MOODLE_ROOT/config.php" ]] || {
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT" >&2
    exit 1
}

FILES=(
    "blocks/menu/block_menu.php"
    "blocks/menu/styles.css"
)

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TEMP_DIR"' EXIT

for relative in "${FILES[@]}"; do
    staged="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$staged")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$staged"
done

php -l "$TEMP_DIR/blocks/menu/block_menu.php" >/dev/null
grep -Fq 'board-hub__action--board-library' "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq '$boardlevelactions = $libraryaction . $studyguideaction' "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq '.board-hub__action--board-library {' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq 'height: 38px;' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq 'min-height: 38px;' "$TEMP_DIR/blocks/menu/styles.css"

mkdir -p "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    staged="$TEMP_DIR/$relative"
    mkdir -p "$BACKUP_DIR/$(dirname "$relative")"
    cp -a "$target" "$BACKUP_DIR/$relative"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$staged" "$target"
done

if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
    runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
else
    php "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "OK: Library movido a Chinese Herbology Board Level."
echo "OK: botones Board Level ajustados a 38 px."
echo "Respaldo: $BACKUP_DIR"
