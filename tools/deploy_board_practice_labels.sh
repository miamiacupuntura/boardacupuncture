#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_board_practice_labels.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

for command in curl php install; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/board-practice-labels-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

FILES=(
    "blocks/menu/block_menu.php"
    "blocks/menu/board.php"
    "blocks/menu/clean-needle-exam-key.php"
    "blocks/menu/styles.css"
    "blocks/menu/version.php"
    "blocks/menu_course/block_menu_course.php"
    "blocks/menu_course/version.php"
    "theme/moove/javascript/study-guide-dashboard.js"
    "theme/moove/layout/drawers.php"
)

mkdir -p "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    sourcefile="$TEMP_DIR/$relative"
    target="$MOODLE_ROOT/$relative"
    mkdir -p "$(dirname "$sourcefile")" "$BACKUP_DIR/$(dirname "$relative")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$sourcefile"
    [[ -f "$target" ]] || { echo "ERROR: no existe $target"; exit 1; }
    cp -a "$target" "$BACKUP_DIR/$relative"
done

php -l "$TEMP_DIR/blocks/menu/block_menu.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/board.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/clean-needle-exam-key.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/version.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu_course/block_menu_course.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu_course/version.php" >/dev/null
php -l "$TEMP_DIR/theme/moove/layout/drawers.php" >/dev/null

for label in \
    "Foundations Practice" \
    "Acupuncture Practice" \
    "Herbology Practice" \
    "Biomedicine Practice" \
    "CNT Practice"; do
    grep -Fq "$label" "$TEMP_DIR/blocks/menu/block_menu.php"
    grep -Fq "$label" "$TEMP_DIR/theme/moove/javascript/study-guide-dashboard.js"
done
! grep -Fq '>Mock Questions<' "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq '$boardlevelactions = $mockquestionsaction . $simulationaction . $studyguideaction;' \
    "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq '.board-hub__card--board-level .study-guide-dashboard-link' \
    "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq "[48, 'Tricky Questions', 'games']" "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq "'title' => 'Tricky Questions'" "$TEMP_DIR/blocks/menu/board.php"
grep -Fq "return ['Tricky Questions', 'games'];" "$TEMP_DIR/blocks/menu_course/block_menu_course.php"
grep -Fq "'v' => '20261106-3'" "$TEMP_DIR/theme/moove/layout/drawers.php"

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/$relative" "$target"
done

if [[ -f "$MOODLE_ROOT/admin/cli/upgrade.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
    else
        php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
    fi
fi

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

for label in \
    "Foundations Practice" \
    "Acupuncture Practice" \
    "Herbology Practice" \
    "Biomedicine Practice" \
    "CNT Practice"; do
    grep -Fq "$label" "$MOODLE_ROOT/blocks/menu/block_menu.php"
done

echo "NOMBRES DE PRÁCTICA ACTUALIZADOS"
echo "Respaldo: $BACKUP_DIR"
echo "Página: https://www.acupuncturetests.com/my/?labels=$REVISION"
