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
if grep -Fq '$studyguidesblockurl' "$LIBRARY_TEMP"; then
    echo "ERROR: Main Study Guides todavía aparece como un solo botón." >&2
    exit 1
fi
grep -Fq "'/blocks/myprogress/single-herbs-study-guide.php'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/dui-yao.html'" "$LIBRARY_TEMP"
grep -Fq "'/blocks/myprogress/formulas-study-guide.php'" "$LIBRARY_TEMP"
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
grep -Fq "<strong><?php echo s(\$ises ? 'Guías de estudio secundarias' : 'Secondary Study Guides'); ?></strong>" "$LIBRARY_TEMP"
grep -Fq "<b><?php echo s(\$ises ? 'Material complementario' : 'Supplemental material'); ?></b>" "$LIBRARY_TEMP"
journey_card_count="$(grep -Fc '<section class="subject-study-library__journey-card">' "$LIBRARY_TEMP")"
[[ "$journey_card_count" -eq 6 ]] || {
    echo "ERROR: el mapa Board Level no contiene exactamente seis módulos." >&2
    exit 1
}
for step in 1 2 3 4 5 6; do
    grep -Fq "<span class=\"subject-study-library__journey-step\">$step</span>" "$LIBRARY_TEMP"
done
secondary_study_guides_block="$(sed -n "/Secondary Study Guides/,+32p" "$LIBRARY_TEMP")"
grep -Fq "Single Herbs Comparisons" <<< "$secondary_study_guides_block"
grep -Fq "Formula Comparisons" <<< "$secondary_study_guides_block"
grep -Fq '$singleherbsstudyurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$formulasstudyurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$comparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$otherchaptersurl->out(false)' <<< "$secondary_study_guides_block"
single_herbs_study_route_count="$(grep -Fc '$singleherbsstudyurl->out(false)' "$LIBRARY_TEMP")"
formulas_study_route_count="$(grep -Fc '$formulasstudyurl->out(false)' "$LIBRARY_TEMP")"
comparisons_route_count="$(grep -Fc '$comparisonsurl->out(false)' "$LIBRARY_TEMP")"
other_chapters_route_count="$(grep -Fc '$otherchaptersurl->out(false)' "$LIBRARY_TEMP")"
[[ "$single_herbs_study_route_count" -eq 2 && "$formulas_study_route_count" -eq 2 \
        && "$comparisons_route_count" -eq 1 && "$other_chapters_route_count" -eq 1 ]] || {
    echo "ERROR: los cuatro botones de Secondary Study Guides están incompletos o duplicados." >&2
    exit 1
}
grep -Fq '.subject-study-library__journey-card:nth-child(4){border-top-color:#6d4bc3}' "$LIBRARY_TEMP"
grep -Fq '.subject-study-library__journey-card:nth-child(5){border-top-color:#2563eb}' "$LIBRARY_TEMP"
grep -Fq '.subject-study-library__journey-card:nth-child(6){border-top-color:#f77e25}' "$LIBRARY_TEMP"
if grep -Fq '.subject-study-library__journey-card:nth-child(4){grid-column:span 3' "$LIBRARY_TEMP" \
        || grep -Fq '.subject-study-library__journey-card:nth-child(5){grid-column:span 3' "$LIBRARY_TEMP" \
        || grep -Fq '.subject-study-library__journey-card:nth-child(6){grid-column:span 3' "$LIBRARY_TEMP"; then
    echo "ERROR: los seis módulos no conservan el diseño uniforme de tres columnas." >&2
    exit 1
fi
grep -Fq "\$ises ? 'Guía de estudio de hierbas individuales' : 'Single Herbs Study Guide'" "$LIBRARY_TEMP"
grep -Fq "\$ises ? 'Guía de estudio de pares Duì Yào' : 'Duì Yào Pairs Study Guide'" "$LIBRARY_TEMP"
grep -Fq "\$ises ? 'Guía de estudio de fórmulas' : 'Formulas Study Guide'" "$LIBRARY_TEMP"
categories_label_count="$(grep -Fc "\$ises ? 'Categorías' : 'Categories'" "$LIBRARY_TEMP")"
[[ "$categories_label_count" -eq 3 ]] || {
    echo "ERROR: Single Herbs, Duì Yào y Formulas no muestran Categories como subtítulo destacado." >&2
    exit 1
}
grep -Fq '.subject-study-library__journey-option-heading{color:inherit;font-size:inherit;font-weight:inherit' "$LIBRARY_TEMP"
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
bensky_block="$(sed -n "/'title' => 'Bensky Category Crosswalk'/,+8p" "$LIBRARY_TEMP")"
grep -Fq "'comparison' => true" <<< "$bensky_block"
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

echo "OK: Secondary Study Guides quedó como módulo 2 con cuatro botones independientes."
echo "OK: los botones dedicados se llaman Single Herbs Comparisons y Formula Comparisons."
echo "OK: los seis módulos conservan su contenido, numeración y diseño uniforme."
echo "OK: cada botón de los módulos 1, 2 y 3 abre un solo bloque."
echo "OK: se conservaron Study Guides, Comparisons, Other chapters, Practice Exams y las tres colecciones de Quiz."
echo "OK: la vista general interna permanece como respaldo, pero no se muestra como botón."
echo "Respaldo: $BACKUP_DIR"
