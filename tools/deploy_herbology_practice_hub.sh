#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/herbology-practice-hub-$(date +%Y%m%d-%H%M%S)"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_herbology_practice_hub.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl php install grep stat cp; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done
[[ -f "$MOODLE_ROOT/config.php" ]] || {
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT" >&2
    exit 1
}

FILES=(
    "blocks/menu/block_menu.php"
    "blocks/menu/herbology-board.php"
    "blocks/menu/styles.css"
    "blocks/myprogress/study-guide.php"
)

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TEMP_DIR"' EXIT

for relative in "${FILES[@]}"; do
    staged="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$staged")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$staged"
done

php -l "$TEMP_DIR/blocks/menu/block_menu.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/herbology-board.php" >/dev/null
php -l "$TEMP_DIR/blocks/myprogress/study-guide.php" >/dev/null

grep -Fq "'track' => 'practice'" "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq '$boardcompletepracticeurl' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq 'href="<?php echo $boardcompletepracticeurl->out(false); ?>"' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq 'foreach ($practicechapters as $practicechapter)' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "return !empty(\$chapter['study_bank']) && empty(\$chapter['practice_exam']);" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "return !empty(\$chapter['quiz']) && empty(\$chapter['practice_exam']);" "$TEMP_DIR/blocks/myprogress/study-guide.php"
if grep -Fq 'id="practice-exams-title"' "$TEMP_DIR/blocks/myprogress/study-guide.php"; then
    echo "ERROR: el bloque Practice Exams todavía aparece fuera de Herbology Practice." >&2
    exit 1
fi
grep -Fq "'practice', 'herbs', 'formulas'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "if (\$track === 'practice')" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Single Herbs Practice' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Formulas Practice' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Domains Practice' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "in_array(\$track, ['practice', 'herbs', 'quiz'], true)" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'id="practice-herb-categories"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'id="practice-formula-categories"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'id="practice-domains"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '.herb-board__launches--practice {' "$TEMP_DIR/blocks/menu/styles.css"

mkdir -p "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    staged="$TEMP_DIR/$relative"
    mkdir -p "$BACKUP_DIR/$(dirname "$relative")"
    cp -a "$target" "$BACKUP_DIR/$relative"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$staged" "$target"
done

if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
    runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
else
    php "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "OK: Open complete Herbology Practice abre el hub de tres módulos."
echo "OK: Single Herbs, Formulas y Domains verificados."
echo "Respaldo: $BACKUP_DIR"
