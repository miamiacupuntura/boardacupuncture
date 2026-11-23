#!/usr/bin/env bash

set -euo pipefail

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILE="blocks/myprogress/admin-herbology-question-bank.php"
TARGET="$MOODLE_ROOT/$RELATIVE_FILE"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_question_creation_diagnostics.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv dirname basename id; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

[[ -f "$TARGET" ]] || {
    echo "ERROR: no existe $TARGET" >&2
    exit 1
}

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/question-creation-diagnostics-$(date +%Y%m%d-%H%M%S)"
cleanup() {
    rm -rf -- "$TEMP_DIR"
}
trap cleanup EXIT
trap 'status=$?; echo "ERROR: despliegue de creación de preguntas detenido en la línea $LINENO (código $status)." >&2; exit "$status"' ERR

SOURCE_FILE="$TEMP_DIR/$RELATIVE_FILE"
mkdir -p "$(dirname "$SOURCE_FILE")"
curl -fsSL \
    -H 'Cache-Control: no-cache' \
    -H 'Pragma: no-cache' \
    "$REPOSITORY/$REVISION/$RELATIVE_FILE?revision=$REVISION" \
    -o "$SOURCE_FILE"

php -l "$SOURCE_FILE" >/dev/null
grep -Fq 'question-bank__creation-failure' "$SOURCE_FILE"
grep -Fq 'Fallo detectado:' "$SOURCE_FILE"
grep -Fq "'exact_import'" "$SOURCE_FILE"
grep -Fq "'ai_generation'" "$SOURCE_FILE"
grep -Fq 'A bold heading without a colon is also accepted.' "$SOURCE_FILE"

mkdir -p "$BACKUP_DIR/$(dirname "$RELATIVE_FILE")"
cp -a -- "$TARGET" "$BACKUP_DIR/$RELATIVE_FILE"

owner_group="$(stat -c '%u:%g' "$TARGET")"
mode="$(stat -c '%a' "$TARGET")"
staged="$MOODLE_ROOT/$(dirname "$RELATIVE_FILE")/.$(basename "$RELATIVE_FILE").creation-diagnostics.$$"
install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$SOURCE_FILE" "$staged"
cmp -s "$SOURCE_FILE" "$staged"
mv -f -- "$staged" "$TARGET"
cmp -s "$SOURCE_FILE" "$TARGET"

php -l "$TARGET" >/dev/null
grep -Fq 'question-bank__creation-failure' "$TARGET"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

if command -v systemctl >/dev/null 2>&1 && systemctl is-active --quiet apache2.service; then
    systemctl reload apache2.service
    echo "RUNTIME RECARGADO: apache2.service"
fi

echo "CREACIÓN DE PREGUNTAS CORREGIDA"
echo "OK: la importación reconoce encabezados Markdown y explicaciones sin dos puntos."
echo "OK: los fallos conservan el texto y muestran la causa y la corrección necesaria."
echo "OK: la disponibilidad del proveedor de IA se muestra antes de enviar el formulario."
echo "RESPALDO: $BACKUP_DIR"
