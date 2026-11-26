#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de CF-1557 detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILE="blocks/myprogress/admin-herbology-question-bank.php"
TARGET="$MOODLE_ROOT/$RELATIVE_FILE"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_cf1557_bai_hu_tang.sh COMMIT_DE_40_CARACTERES" >&2
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
grep -Fq "'CF-1557' => [" "$SOURCE"
grep -Fq "Bai Hu Tang (White Tiger Decoction) for a Yang Ming Qi-level disorder" "$SOURCE"
grep -Fq "los cuatro grandes signos" "$SOURCE"

BACKUP_DIR="$BACKUP_ROOT/cf1557-bai-hu-tang-$(date -u +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"
if [[ -f "$TARGET" ]]; then
    cp -a "$TARGET" "$BACKUP_DIR/admin-herbology-question-bank.php"
fi

install -o root -g www-data -m 0644 "$SOURCE" "$TARGET"
php -l "$TARGET" >/dev/null

echo "DESPLIEGUE COMPLETADO"
echo "Pregunta: CF-1557"
echo "Fórmula visible: Bai Hu Tang (White Tiger Decoction)"
echo "Respuesta conservada: B"
echo "Respaldo: $BACKUP_DIR"
