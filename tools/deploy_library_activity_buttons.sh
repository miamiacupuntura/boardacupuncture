#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/library-activity-buttons-$(date +%Y%m%d-%H%M%S)"
TARGET="$MOODLE_ROOT/blocks/myprogress/study-guide.php"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_library_activity_buttons.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl php install grep stat cp; do
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
curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" -o "$TEMP_FILE"
php -l "$TEMP_FILE" >/dev/null

grep -Fq "\$comparisonsurl->set_anchor('comparisons-title')" "$TEMP_FILE"
grep -Fq "\$otherchaptersurl->set_anchor('other-chapters-title')" "$TEMP_FILE"
grep -Fq "\$practiceexamsurl->set_anchor('practice-exams-title')" "$TEMP_FILE"
grep -Fq "s(\$tr('Comparisons'))" "$TEMP_FILE"
grep -Fq "s(\$tr('Other chapters'))" "$TEMP_FILE"
grep -Fq "s(\$tr('Practice Exams'))" "$TEMP_FILE"
grep -Fq 'id="practice-exams-title"' "$TEMP_FILE"

mkdir -p "$BACKUP_DIR/blocks/myprogress"
cp -a "$TARGET" "$BACKUP_DIR/blocks/myprogress/study-guide.php"
owner_group="$(stat -c '%u:%g' "$TARGET")"
mode="$(stat -c '%a' "$TARGET")"
install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_FILE" "$TARGET"

if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
    runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
else
    php "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "OK: Comparisons y Other chapters añadidos a Study Guides."
echo "OK: Practice Exams añadido a Practice with answers."
echo "OK: no se movió ningún bloque de contenido en esta etapa."
echo "Respaldo: $BACKUP_DIR"
