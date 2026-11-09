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
grep -Fq "<strong><?php echo s(\$ises ? 'Guía de estudio principal' : 'Main Study Guide'); ?></strong>" "$LIBRARY_TEMP"
grep -Fq "<strong><?php echo s(\$ises ? 'Comparación' : 'Comparison'); ?></strong>" "$LIBRARY_TEMP"
grep -Fq "<strong><?php echo s(\$ises ? 'Guía de estudio secundaria' : 'Secondary Study Guide'); ?></strong>" "$LIBRARY_TEMP"
grep -Fq "<b><?php echo s(\$ises ? 'Material de estudio adicional' : 'Additional study material'); ?></b>" "$LIBRARY_TEMP"
grep -Fq "<strong><?php echo s(\$ises ? 'Misceláneos de fórmulas y hierbas' : 'Formulas and Herbs Miscellaneous'); ?></strong>" "$LIBRARY_TEMP"
grep -Fq "<b><?php echo s(\$ises ? 'Material adicional de fórmulas y hierbas' : 'Additional formulas and herbs'); ?></b>" "$LIBRARY_TEMP"
other_chapters_loop_count="$(grep -Fc '$otherchapters as $chapter' "$LIBRARY_TEMP")"
secondary_guide_loop_count="$(grep -Fc '$secondaryguidechapters as $chapter' "$LIBRARY_TEMP")"
herb_formula_misc_loop_count="$(grep -Fc '$herbformulamiscchapters as $chapter' "$LIBRARY_TEMP")"
[[ "$other_chapters_loop_count" -eq 1 && "$secondary_guide_loop_count" -eq 1 \
        && "$herb_formula_misc_loop_count" -eq 1 ]] || {
    echo "ERROR: los recursos de Other chapters no están distribuidos entre los módulos 3 y 4." >&2
    exit 1
}
grep -Fq "\$isHerbFormulaChapter = static function(array \$chapter): bool" "$LIBRARY_TEMP"
grep -Fq "preg_match('/(?:herb|formula|hierba|fórmula)/iu'" "$LIBRARY_TEMP"
grep -Fq '$secondarychapterurl = $makechapterurl($chapter);' "$LIBRARY_TEMP"
grep -Fq '$secondarychapterurl->out(false)' "$LIBRARY_TEMP"
grep -Fq '$miscchapterurl = $makechapterurl($chapter);' "$LIBRARY_TEMP"
grep -Fq '$miscchapterurl->out(false)' "$LIBRARY_TEMP"
grep -Fq "<b><?php echo s(\$ises ? 'Material complementario' : 'Supplemental material'); ?></b>" "$LIBRARY_TEMP"
journey_card_count="$(grep -Fc '<section class="subject-study-library__journey-card">' "$LIBRARY_TEMP")"
[[ "$journey_card_count" -eq 8 ]] || {
    echo "ERROR: el mapa Board Level no contiene exactamente ocho módulos." >&2
    exit 1
}
for step in 1 2 3 4 5 6 7 8; do
    grep -Fq "<span class=\"subject-study-library__journey-step\">$step</span>" "$LIBRARY_TEMP"
done
secondary_study_guides_block="$(sed -n '/subject-study-library__journey-step">2</,+50p' "$LIBRARY_TEMP")"
grep -Fq "Single Herbs Comparisons" <<< "$secondary_study_guides_block"
grep -Fq "Formula Comparisons" <<< "$secondary_study_guides_block"
grep -Fq "Bensky Category Crosswalk" <<< "$secondary_study_guides_block"
grep -Fq "Pathology Comparisons" <<< "$secondary_study_guides_block"
grep -Fq "Pattern Comparisons" <<< "$secondary_study_guides_block"
grep -Fq '$singleherbscomparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$formulacomparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$benskycrosswalkurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$pathologycomparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$patterncomparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$otherchaptersurl->out(false)' <<< "$secondary_study_guides_block"
if grep -Fq '$comparisonsurl' "$LIBRARY_TEMP"; then
    echo "ERROR: el botón general Comparisons todavía aparece en el módulo Comparison." >&2
    exit 1
fi
grep -Fq "'/study-guide/comparisons/index-es.html' : '/study-guide/comparisons/index.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/formulas-es.html' : '/study-guide/comparisons/formulas.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/pathologies-es.html' : '/study-guide/comparisons/pathologies.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/patterns-es.html' : '/study-guide/comparisons/patterns.html'" "$LIBRARY_TEMP"
grep -Fq "'/blocks/myprogress/herb-formula-category-map.php'" "$LIBRARY_TEMP"
grep -Fq "\$comparisonoverviewchapters = array_values(array_filter(\$comparisonchapters" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/formulas.html'," "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/index.html'," "$LIBRARY_TEMP"
if grep -Fq 'foreach ($comparisonchapters as $chapter)' "$LIBRARY_TEMP"; then
    echo "ERROR: Formula Comparisons y Single Herbs Comparisons todavía aparecen en la vista general Comparisons." >&2
    exit 1
fi
single_herbs_study_route_count="$(grep -Fc '$singleherbsstudyurl->out(false)' "$LIBRARY_TEMP")"
formulas_study_route_count="$(grep -Fc '$formulasstudyurl->out(false)' "$LIBRARY_TEMP")"
single_herbs_comparisons_route_count="$(grep -Fc '$singleherbscomparisonsurl->out(false)' "$LIBRARY_TEMP")"
formula_comparisons_route_count="$(grep -Fc '$formulacomparisonsurl->out(false)' "$LIBRARY_TEMP")"
bensky_crosswalk_route_count="$(grep -Fc '$benskycrosswalkurl->out(false)' "$LIBRARY_TEMP")"
pathology_comparisons_route_count="$(grep -Fc '$pathologycomparisonsurl->out(false)' "$LIBRARY_TEMP")"
pattern_comparisons_route_count="$(grep -Fc '$patterncomparisonsurl->out(false)' "$LIBRARY_TEMP")"
other_chapters_route_count="$(grep -Fc '$otherchaptersurl->out(false)' "$LIBRARY_TEMP")"
[[ "$single_herbs_study_route_count" -eq 1 && "$formulas_study_route_count" -eq 1 \
        && "$single_herbs_comparisons_route_count" -eq 1 && "$formula_comparisons_route_count" -eq 1 \
        && "$bensky_crosswalk_route_count" -eq 1 && "$pathology_comparisons_route_count" -eq 1 \
        && "$pattern_comparisons_route_count" -eq 1 && "$other_chapters_route_count" -eq 1 ]] || {
    echo "ERROR: los botones de Main Study Guide y Comparison están incompletos o duplicados." >&2
    exit 1
}
grep -Fq '.subject-study-library__journey-card:nth-child(6){border-top-color:#6d4bc3}' "$LIBRARY_TEMP"
grep -Fq '.subject-study-library__journey-card:nth-child(7){border-top-color:#2563eb}' "$LIBRARY_TEMP"
grep -Fq '.subject-study-library__journey-card:nth-child(8){border-top-color:#f77e25}' "$LIBRARY_TEMP"
if grep -Fq '.subject-study-library__journey-card:nth-child(6){grid-column:span 3' "$LIBRARY_TEMP" \
        || grep -Fq '.subject-study-library__journey-card:nth-child(7){grid-column:span 3' "$LIBRARY_TEMP" \
        || grep -Fq '.subject-study-library__journey-card:nth-child(8){grid-column:span 3' "$LIBRARY_TEMP"; then
    echo "ERROR: los ocho módulos no conservan el diseño uniforme." >&2
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
for required in     '$primarychapters as $chapter'     '$practicechapters as $chapter'     '$comparisonoverviewchapters as $chapter'     '$otherchapters as $chapter'; do
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

echo "OK: Comparison quedó como módulo 2 con seis botones independientes."
echo "OK: Secondary Study Guide conserva los recursos restantes de Other chapters."
echo "OK: Formulas and Herbs Miscellaneous contiene los recursos cuyos títulos indican herbs o formulas."
echo "OK: Single Herbs Comparisons y Formula Comparisons abren directamente sus colecciones."
echo "OK: ambas colecciones fueron retiradas de la vista general Comparisons sin eliminar su contenido."
echo "OK: Bensky Category Crosswalk, Pathology Comparisons y Pattern Comparisons tienen botones directos."
echo "OK: el botón general Comparisons fue eliminado."
echo "OK: el módulo 1 se llama Main Study Guide."
echo "OK: los ocho módulos conservan su contenido, numeración y diseño uniforme."
echo "OK: cada botón de los módulos 1, 2 y 3 abre un solo bloque."
echo "OK: se conservaron Main Study Guide, Other chapters, las colecciones comparativas, Practice Exams y Quiz."
echo "OK: la vista general interna permanece como respaldo, pero no se muestra como botón."
echo "Respaldo: $BACKUP_DIR"
