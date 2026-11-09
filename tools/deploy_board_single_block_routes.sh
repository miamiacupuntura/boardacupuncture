#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
BACKUP_DIR="$BACKUP_ROOT/board-single-block-routes-$(date +%Y%m%d-%H%M%S)"
LIBRARY_TARGET="$MOODLE_ROOT/blocks/myprogress/study-guide.php"
BOARD_TARGET="$MOODLE_ROOT/blocks/menu/herbology-board.php"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_board_single_block_routes.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi
for command in curl php install grep sed stat cp mkdir mktemp rm; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done
for target in "$LIBRARY_TARGET" "$BOARD_TARGET"; do
    [[ -f "$target" ]] || {
        echo "ERROR: no se encontró $target" >&2
        exit 1
    }
done

LIBRARY_TEMP="$(mktemp)"
BOARD_TEMP="$(mktemp)"
trap 'rm -f "$LIBRARY_TEMP" "$BOARD_TEMP"' EXIT

curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" -o "$LIBRARY_TEMP"
curl -fsSL "$REPOSITORY/$REVISION/blocks/menu/herbology-board.php" -o "$BOARD_TEMP"
php -l "$LIBRARY_TEMP" >/dev/null
php -l "$BOARD_TEMP" >/dev/null

# Cada botón de los tres primeros módulos debe resolver una sola vista.
grep -Fq "['guides', 'comparisons', 'other', 'practice']" "$LIBRARY_TEMP"
grep -Fq "\$baseparams + ['view' => 'guides']" "$LIBRARY_TEMP"
grep -Fq "\$baseparams + ['view' => 'comparisons']" "$LIBRARY_TEMP"
grep -Fq "\$baseparams + ['view' => 'other']" "$LIBRARY_TEMP"
if grep -Fq '$practiceexamsurl' "$LIBRARY_TEMP"; then
    echo "ERROR: el botón Practice Exams todavía aparece en Board Level Activities." >&2
    exit 1
fi
grep -Fq "empty(\$otherchapters)): ?>" "$LIBRARY_TEMP"
grep -Fq "'Open' => 'Abrir'" "$LIBRARY_TEMP"
open_button_count="$(grep -Fc "\$tr('Open')" "$LIBRARY_TEMP")"
[[ "$open_button_count" -eq 4 ]] || {
    echo "ERROR: no todos los botones del catálogo usan la etiqueta Open/Abrir." >&2
    exit 1
}
grep -Fq "<strong><?php echo s(\$ises ? 'Guías de estudio' : 'Study Guides'); ?></strong>" "$LIBRARY_TEMP"
grep -Fq "<strong>Herbology Practice</strong>" "$LIBRARY_TEMP"
grep -Fq "<strong>Quiz</strong>" "$LIBRARY_TEMP"
grep -Fq "<strong><?php echo s(\$ises ? 'Exámenes' : 'Exams'); ?></strong>" "$LIBRARY_TEMP"
grep -Fq "<strong><?php echo s(\$ises ? 'Simulación' : 'Simulation'); ?></strong>" "$LIBRARY_TEMP"
formula_study_block="$(sed -n "/'title' => 'Formula Study Center'/,+8p" "$LIBRARY_TEMP")"
grep -Fq "'secondary' => true" <<< "$formula_study_block"
if grep -Fq "'practice_exam' => true" <<< "$formula_study_block"; then
    echo "ERROR: Formula Study Center todavía pertenece a Practice Exams." >&2
    exit 1
fi
grep -Fq "return !empty(\$chapter['practice_exam']) && empty(\$chapter['quiz']);" "$LIBRARY_TEMP"
grep -Fq "['track' => 'quiz', 'quizsection' => 'herbs']" "$LIBRARY_TEMP"
grep -Fq "['track' => 'quiz', 'quizsection' => 'formulas']" "$LIBRARY_TEMP"
grep -Fq "['track' => 'quiz', 'quizsection' => 'clinical']" "$LIBRARY_TEMP"
grep -Fq "in_array(\$quizsection, ['all', 'herbs'], true)" "$BOARD_TEMP"
grep -Fq "in_array(\$quizsection, ['all', 'formulas'], true)" "$BOARD_TEMP"
grep -Fq "in_array(\$quizsection, ['all', 'clinical'], true)" "$BOARD_TEMP"

# Guardas de conservación: las colecciones y sus recorridos deben seguir presentes.
for required in     '$primarychapters as $chapter'     '$practicechapters as $chapter'     '$comparisonchapters as $chapter'     '$otherchapters as $chapter'; do
    grep -Fq "$required" "$LIBRARY_TEMP"
done
for required in     '$herbcategories as $category'     '$formulacategories as $category'     '$clinicalcaseexams as $clinicalcaseexam'; do
    grep -Fq "$required" "$BOARD_TEMP"
done

# Los botones generales que mezclaban varios bloques ya no deben aparecer en los módulos 1–3.
if grep -Fq "Open all Study Guides" "$LIBRARY_TEMP"         || grep -Fq "Open complete Herbology Practice" "$LIBRARY_TEMP"         || grep -Fq "Open all Quizzes" "$LIBRARY_TEMP"; then
    echo "ERROR: todavía existe un botón general dentro de los tres primeros módulos." >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR/blocks/myprogress" "$BACKUP_DIR/blocks/menu"
cp -a "$LIBRARY_TARGET" "$BACKUP_DIR/blocks/myprogress/study-guide.php"
cp -a "$BOARD_TARGET" "$BACKUP_DIR/blocks/menu/herbology-board.php"

install_preserving_metadata() {
    local source_file="$1"
    local target_file="$2"
    local owner_group mode
    owner_group="$(stat -c '%u:%g' "$target_file")"
    mode="$(stat -c '%a' "$target_file")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$source_file" "$target_file"
}

install_preserving_metadata "$LIBRARY_TEMP" "$LIBRARY_TARGET"
install_preserving_metadata "$BOARD_TEMP" "$BOARD_TARGET"

if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
    runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
else
    php "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "OK: cada botón de los módulos 1, 2 y 3 abre un solo bloque."
echo "OK: se conservaron Study Guides, Comparisons, Other chapters, Practice Exams y las tres colecciones de Quiz."
echo "OK: la vista general interna permanece como respaldo, pero no se muestra como botón."
echo "Respaldo: $BACKUP_DIR"
