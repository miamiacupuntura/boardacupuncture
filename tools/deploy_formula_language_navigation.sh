#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/formula-language-navigation-$(date +%Y%m%d-%H%M%S)"
TARGET="$MOODLE_ROOT/blocks/myprogress/formulas-study-guide.php"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_formula_language_navigation.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl php install grep stat cp mkdir mktemp rm; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done
[[ -f "$TARGET" ]] || {
    echo "ERROR: no se encontró $TARGET" >&2
    exit 1
}

TEMP_FILE="$(mktemp)"
trap 'rm -f "$TEMP_FILE"' EXIT
curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/formulas-study-guide.php" -o "$TEMP_FILE"
php -l "$TEMP_FILE" >/dev/null

grep -Fq 'block_myprogress_render_herbology_library_navigation' "$TEMP_FILE"
grep -Fq '<?php if (!$fromlibrary): ?>' "$TEMP_FILE"
grep -Fq "<?php echo \$ises ? 'English' : 'Español'; ?>" "$TEMP_FILE"
language_button_count="$(grep -Fc "<?php echo \$ises ? 'English' : 'Español'; ?>" "$TEMP_FILE")"
[[ "$language_button_count" -eq 1 ]] || {
    echo "ERROR: la guía de fórmulas contiene más de un botón de idioma propio." >&2
    exit 1
}

mkdir -p "$BACKUP_DIR/blocks/myprogress"
cp -a "$TARGET" "$BACKUP_DIR/blocks/myprogress/formulas-study-guide.php"
owner_group="$(stat -c '%u:%g' "$TARGET")"
mode="$(stat -c '%a' "$TARGET")"
install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_FILE" "$TARGET"

if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
    runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
else
    php "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "OK: Formula Study Guide muestra un solo control Español/English."
echo "OK: se conserva el botón de idioma de la navegación Board Level."
echo "Respaldo: $BACKUP_DIR"
