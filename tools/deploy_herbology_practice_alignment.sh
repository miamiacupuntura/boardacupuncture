#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
EXPECTED_VERSION="2026110657"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_herbology_practice_alignment.sh COMMIT_DE_40_CARACTERES"
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

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/herbology-practice-alignment-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

FILES=(
    "blocks/menu/herbology-board.php"
    "blocks/menu/styles.css"
    "blocks/menu/version.php"
    "blocks/menu/db/install.xml"
    "blocks/menu/db/upgrade.php"
)

for relative in "${FILES[@]}"; do
    staged="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$staged")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$staged"
done

php -l "$TEMP_DIR/blocks/menu/herbology-board.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/version.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/db/upgrade.php" >/dev/null

grep -Fq "\$plugin->version   = $EXPECTED_VERSION;" "$TEMP_DIR/blocks/menu/version.php"
grep -Fq "VERSION=\"$EXPECTED_VERSION\"" "$TEMP_DIR/blocks/menu/db/install.xml"
grep -Fq "upgrade_block_savepoint(true, $EXPECTED_VERSION, 'menu')" "$TEMP_DIR/blocks/menu/db/upgrade.php"
grep -Fq "'Single Herbs bank totals'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'Study by formula category'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-herbology-step-back' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '.herb-board__summary--panel' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq '.herb-board__back:hover' "$TEMP_DIR/blocks/menu/styles.css"

if grep -Fq "This bank includes <?php echo" "$TEMP_DIR/blocks/menu/herbology-board.php"; then
    echo "ERROR: la vista de hierbas conserva el formato anterior." >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR"
default_owner_group="$(stat -c '%u:%g' "$MOODLE_ROOT/config.php")"

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    staged="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$target")" "$BACKUP_DIR/$(dirname "$relative")"

    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP_DIR/$relative"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
    else
        owner_group="$default_owner_group"
        mode="644"
    fi

    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$staged" "$target"
done

php -l "$MOODLE_ROOT/blocks/menu/herbology-board.php" >/dev/null
php -l "$MOODLE_ROOT/blocks/menu/version.php" >/dev/null
php -l "$MOODLE_ROOT/blocks/menu/db/upgrade.php" >/dev/null
grep -Fq "'Single Herbs bank totals'" "$MOODLE_ROOT/blocks/menu/herbology-board.php"
grep -Fq "'Study by formula category'" "$MOODLE_ROOT/blocks/menu/herbology-board.php"
grep -Fq '.herb-board__back:hover' "$MOODLE_ROOT/blocks/menu/styles.css"

run_moodle_cli() {
    local script="$1"
    shift
    if [[ "$(id -u)" -eq 0 ]] && command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$script" "$@"
    else
        php "$script" "$@"
    fi
}

if [[ -f "$MOODLE_ROOT/admin/cli/upgrade.php" ]]; then
    run_moodle_cli "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
fi
if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    run_moodle_cli "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "OK: Herbology Practice quedó alineado en Single Herbs y Formulas."
echo "Respaldo: $BACKUP_DIR"
echo "Verifica: /blocks/menu/herbology-board.php?track=herbs"
echo "Verifica: /blocks/menu/herbology-board.php?track=formulas&from=library"
