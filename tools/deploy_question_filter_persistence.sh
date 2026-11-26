#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de conservación de filtros detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILE="blocks/myprogress/admin-herbology-question-bank.php"
TARGET="$MOODLE_ROOT/$RELATIVE_FILE"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_question_filter_persistence.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php install cp mkdir mktemp date; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
SOURCE="$TEMP_DIR/admin-herbology-question-bank.php"
curl -fsSL "$REPOSITORY/$REVISION/$RELATIVE_FILE" -o "$SOURCE"
php -l "$SOURCE" >/dev/null
grep -Fq '$failureoriginfilters = $questionbankreturnfilterparams($rewritequestionid);' "$SOURCE"
grep -Fq '$revieworiginfilters = $questionbankreturnfilterparams($rewritequestionid);' "$SOURCE"
grep -Fq 'same filters expose the next matching question automatically' "$SOURCE"

BACKUP_DIR="$BACKUP_ROOT/question-filter-persistence-$(date -u +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"
if [[ -f "$TARGET" ]]; then
    cp -a "$TARGET" "$BACKUP_DIR/admin-herbology-question-bank.php"
fi

install -o root -g www-data -m 0644 "$SOURCE" "$TARGET"
php -l "$TARGET" >/dev/null

echo "DESPLIEGUE COMPLETADO"
echo "Los filtros del banco se conservan después de revisar o publicar cada pregunta."
echo "Respaldo: $BACKUP_DIR"
