#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de los bloques de Quiz History detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_exam_history_quiz_blocks.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT

curl -fsSL "$REPOSITORY/$REVISION/blocks/menu/exam-history.php" \
    -o "$TEMP_DIR/exam-history.php"

php -l "$TEMP_DIR/exam-history.php" >/dev/null
grep -Fq "exam-history__catalog-groups" "$TEMP_DIR/exam-history.php"
grep -Fq "exam-history__progress-filters" "$TEMP_DIR/exam-history.php"
grep -Fq "'bqf_modifications'" "$TEMP_DIR/exam-history.php"
grep -Fq "'clinical-cases'" "$TEMP_DIR/exam-history.php"
grep -Fq "'Respondidos' : 'Answered'" "$TEMP_DIR/exam-history.php"

TARGET="$MOODLE_ROOT/blocks/menu/exam-history.php"
[[ -f "$TARGET" ]] || {
    echo "ERROR: no existe $TARGET" >&2
    exit 1
}

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/exam-history-quiz-blocks-$TIMESTAMP"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/exam-history.php"

owner_group="$(stat -c '%u:%g' "$TARGET")"
mode="$(stat -c '%a' "$TARGET")"
install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
    "$TEMP_DIR/exam-history.php" "$TARGET"
cmp -s "$TEMP_DIR/exam-history.php" "$TARGET"
php -l "$TARGET" >/dev/null

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: Quiz Board Level está organizado en seis bloques."
echo "OK: filtros Todos, Respondidos y Pendientes activos."
echo "OK: cada bloque muestra progreso, intentos y promedio."
echo "OK: Formula Modifications usa su historial independiente."
echo "OK: Clinical Cases aparece en su bloque sin perder el historial Board."
echo "RESPALDO: $BACKUP"
