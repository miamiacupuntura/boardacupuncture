#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/herbology-feature-copy-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_herbology_feature_copy.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi
if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

FILES=(
    "blocks/menu/herbology-board.php"
    "blocks/menu/styles.css"
    "blocks/menu/version.php"
    "blocks/menu/db/install.xml"
    "blocks/menu/db/upgrade.php"
)

mkdir -p "$TEMP_DIR" "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    mkdir -p "$TEMP_DIR/$(dirname "$relative")" "$BACKUP_DIR/$(dirname "$relative")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$TEMP_DIR/$relative"
done

php -l "$TEMP_DIR/blocks/menu/herbology-board.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/version.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/db/upgrade.php" >/dev/null
grep -Fq 'class="herb-board__feature-copy"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '.herb-board__feature-copy {' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq '$plugin->version   = 2026110635;' "$TEMP_DIR/blocks/menu/version.php"
! grep -Fq '<span>Questions and explanations</span>' "$TEMP_DIR/blocks/menu/herbology-board.php"
! grep -Fq '<span>Recall flashcards</span>' "$TEMP_DIR/blocks/menu/herbology-board.php"

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    [[ -f "$target" ]] || { echo "ERROR: no existe $target"; exit 1; }
    cp -a "$target" "$BACKUP_DIR/$relative"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/$relative" "$target"
done

php -l "$MOODLE_ROOT/blocks/menu/herbology-board.php" >/dev/null
grep -Fq 'class="herb-board__feature-copy"' "$MOODLE_ROOT/blocks/menu/herbology-board.php"
! grep -Fq '<span>Questions and explanations</span>' "$MOODLE_ROOT/blocks/menu/herbology-board.php"

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

echo "TEXTO EXPLICATIVO DE HERBOLOGY PRACTICE INSTALADO"
echo "Respaldo: $BACKUP_DIR"
echo "Prueba: https://www.acupuncturetests.com/blocks/menu/herbology-board.php?track=herbs&copy=$REVISION"
