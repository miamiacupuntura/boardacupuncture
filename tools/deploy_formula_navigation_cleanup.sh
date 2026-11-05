#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/formulas-study-guide.php"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_formula_navigation_cleanup.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php install grep stat mktemp; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command." >&2
        exit 1
    }
done

if [[ ! -f "$MOODLE_ROOT/config.php" || ! -f "$TARGET" ]]; then
    echo "ERROR: no se encontró Formula Study Guide en $TARGET." >&2
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/formula-navigation-cleanup-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$BACKUP_DIR"

curl -fsSL \
    "$REPOSITORY/$REVISION/blocks/myprogress/formulas-study-guide.php" \
    -o "$TEMP_DIR/formulas-study-guide.php"

php -l "$TEMP_DIR/formulas-study-guide.php" >/dev/null
grep -Fq '$dashboardurl = new moodle_url' "$TEMP_DIR/formulas-study-guide.php"
grep -Fq '← Back to Dashboard' "$TEMP_DIR/formulas-study-guide.php"
grep -Fq "\$ises ? 'English' : 'Español'" "$TEMP_DIR/formulas-study-guide.php"
grep -Fq 'formula-study-guide__study-route' "$TEMP_DIR/formulas-study-guide.php"
grep -Fq 'data-formula-chapter-jump' "$TEMP_DIR/formulas-study-guide.php"
! grep -Fq '<div class="formula-study-guide__module-switcher"' "$TEMP_DIR/formulas-study-guide.php"
! grep -Fq '← Board Guideline · Chinese Herbal Medicine' "$TEMP_DIR/formulas-study-guide.php"
! grep -Fq '← Library' "$TEMP_DIR/formulas-study-guide.php"

cp -a "$TARGET" "$BACKUP_DIR/"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" \
    "$TEMP_DIR/formulas-study-guide.php" "$TARGET"

php -l "$TARGET" >/dev/null
grep -Fq '← Back to Dashboard' "$TARGET"
! grep -Fq '<div class="formula-study-guide__module-switcher"' "$TARGET"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "NAVEGACIÓN DE FÓRMULAS LIMPIADA"
echo "Cabecera: idioma y Dashboard. La ruta continua y Previous/Jump/Next se conservan."
echo "Respaldo: $BACKUP_DIR"
echo "Prueba: https://www.acupuncturetests.com/blocks/myprogress/formulas-study-guide.php?nav=$REVISION"
