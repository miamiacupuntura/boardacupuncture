#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
EXPECTED_VERSION="2026110658"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_herbology_timed_simulation.sh COMMIT_DE_40_CARACTERES"
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
BACKUP_DIR="$BACKUP_ROOT/herbology-timed-simulation-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

FILES=(
    "blocks/menu/block_menu.php"
    "blocks/menu/herbology-board.php"
    "blocks/menu/herbology-simulation.js"
    "blocks/menu/styles.css"
    "blocks/menu/version.php"
    "blocks/menu/db/install.xml"
    "blocks/menu/db/upgrade.php"
    "blocks/myprogress/study-guide.php"
)

for relative in "${FILES[@]}"; do
    staged="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$staged")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$staged"
done

for relative in "${FILES[@]}"; do
    if [[ "$relative" == *.php ]]; then
        php -l "$TEMP_DIR/$relative" >/dev/null
    fi
done
grep -Fq "\$plugin->version   = $EXPECTED_VERSION;" "$TEMP_DIR/blocks/menu/version.php"
grep -Fq "VERSION=\"$EXPECTED_VERSION\"" "$TEMP_DIR/blocks/menu/db/install.xml"
grep -Fq "upgrade_block_savepoint(true, $EXPECTED_VERSION, 'menu')" "$TEMP_DIR/blocks/menu/db/upgrade.php"
grep -Fq "'simulation'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-simulation-exam' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-duration="9000"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__simulation-clock' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq 'finishExam(true)' "$TEMP_DIR/blocks/menu/herbology-simulation.js"
grep -Fq "['track' => 'simulation']" "$TEMP_DIR/blocks/myprogress/study-guide.php"

if grep -Fq 'herb-board__exam-feedback' "$TEMP_DIR/blocks/menu/herbology-simulation.js"; then
    echo "ERROR: la simulación contiene lógica de feedback inmediato." >&2
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

echo "OK: Simulation Exam 1 quedó disponible en modalidad lineal y adaptativa."
echo "Respaldo: $BACKUP_DIR"
echo "Verifica: /blocks/menu/herbology-board.php?track=simulation&from=library"
