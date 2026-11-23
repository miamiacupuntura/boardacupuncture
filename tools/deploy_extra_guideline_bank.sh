#!/usr/bin/env bash

set -euo pipefail

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
FILES=(
    "blocks/myprogress/admin-herbology-question-bank.php"
    "blocks/myprogress/herbology-question-overrides.php"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_extra_guideline_bank.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv dirname basename id; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

for relative in "${FILES[@]}"; do
    [[ -f "$MOODLE_ROOT/$relative" ]] || {
        echo "ERROR: no existe $MOODLE_ROOT/$relative" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/extra-guideline-bank-$(date +%Y%m%d-%H%M%S)"
cleanup() {
    rm -rf -- "$TEMP_DIR"
}
trap cleanup EXIT
trap 'status=$?; echo "ERROR: despliegue de Extra guideline detenido en la línea $LINENO (código $status)." >&2; exit "$status"' ERR

for relative in "${FILES[@]}"; do
    source_file="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$source_file")"
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/$relative?revision=$REVISION" \
        -o "$source_file"
    php -l "$source_file" >/dev/null
done

grep -Fq 'name="saveoutsideguideline"' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Guardar en Extra guideline' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq "'status' => \$saveoutsideguideline ? 'extra_guideline' : 'draft'" "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '2026-11-23-extra-guideline-bank-v1' "$TEMP_DIR/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq 'Mock-only, Extra guideline and recycled administrator questions' "$TEMP_DIR/blocks/myprogress/herbology-question-overrides.php"

mkdir -p "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    source_file="$TEMP_DIR/$relative"
    backup="$BACKUP_DIR/$relative"
    staged="$MOODLE_ROOT/$(dirname "$relative")/.$(basename "$relative").extra-guideline.$$"
    mkdir -p "$(dirname "$backup")"
    cp -a -- "$target" "$backup"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$source_file" "$staged"
    cmp -s "$source_file" "$staged"
    mv -f -- "$staged" "$target"
    cmp -s "$source_file" "$target"
done

for relative in "${FILES[@]}"; do
    php -l "$MOODLE_ROOT/$relative" >/dev/null
done
grep -Fq 'Guardar en Extra guideline' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
grep -Fq '2026-11-23-extra-guideline-bank-v1' "$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"

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

echo "BANCO EXTRA GUIDELINE INSTALADO"
echo "OK: las preguntas fuera del roster se guardan en un banco administrativo independiente."
echo "OK: no se envían a Board, Mock ni Simulation Exams."
echo "OK: los registros creados por la versión anterior se separan automáticamente de Mock."
echo "RESPALDO: $BACKUP_DIR"
