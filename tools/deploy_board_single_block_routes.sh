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
MENU_TARGET="$MOODLE_ROOT/blocks/menu/block_menu.php"
STYLES_TARGET="$MOODLE_ROOT/blocks/menu/styles.css"
CATEGORY_TARGET="$MOODLE_ROOT/blocks/myprogress/herbs-category-comparison.php"

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
for target in "$LIBRARY_TARGET" "$BOARD_TARGET" "$MENU_TARGET" "$STYLES_TARGET"; do
    [[ -f "$target" ]] || {
        echo "ERROR: no se encontró $target" >&2
        exit 1
    }
done

LIBRARY_TEMP="$(mktemp)"
BOARD_TEMP="$(mktemp)"
MENU_TEMP="$(mktemp)"
STYLES_TEMP="$(mktemp)"
CATEGORY_TEMP="$(mktemp)"
trap 'rm -f "$LIBRARY_TEMP" "$BOARD_TEMP" "$MENU_TEMP" "$STYLES_TEMP" "$CATEGORY_TEMP"' EXIT

curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" -o "$LIBRARY_TEMP"
curl -fsSL "$REPOSITORY/$REVISION/blocks/menu/herbology-board.php" -o "$BOARD_TEMP"
curl -fsSL "$REPOSITORY/$REVISION/blocks/menu/block_menu.php" -o "$MENU_TEMP"
curl -fsSL "$REPOSITORY/$REVISION/blocks/menu/styles.css" -o "$STYLES_TEMP"
curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/herbs-category-comparison.php" -o "$CATEGORY_TEMP"
php -l "$LIBRARY_TEMP" >/dev/null
php -l "$BOARD_TEMP" >/dev/null
php -l "$MENU_TEMP" >/dev/null
php -l "$CATEGORY_TEMP" >/dev/null

grep -Fq "Herbs Category Comparison" "$CATEGORY_TEMP"
grep -Fq 'id="disperse-wind-cold"' "$CATEGORY_TEMP"
grep -Fq 'href="#disperse-wind-heat"' "$CATEGORY_TEMP"
grep -Fq 'id="disperse-wind-heat"' "$CATEGORY_TEMP"
grep -Fq 'href="#clear-and-purge-heat"' "$CATEGORY_TEMP"
grep -Fq 'id="clear-and-purge-heat"' "$CATEGORY_TEMP"
grep -Fq 'href="#clear-blood-heat"' "$CATEGORY_TEMP"
grep -Fq 'id="clear-blood-heat"' "$CATEGORY_TEMP"
grep -Fq 'href="#clear-damp-heat"' "$CATEGORY_TEMP"
grep -Fq 'id="clear-damp-heat"' "$CATEGORY_TEMP"
grep -Fq 'href="#clear-toxic-heat"' "$CATEGORY_TEMP"
grep -Fq 'id="clear-toxic-heat"' "$CATEGORY_TEMP"
grep -Fq '$toxicheatherbs = [' "$CATEGORY_TEMP"
grep -Fq '$toxicheatsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Jin Yin Hua'" "$CATEGORY_TEMP"
grep -Fq "'Lian Qiao'" "$CATEGORY_TEMP"
grep -Fq "'She Gan'" "$CATEGORY_TEMP"
grep -Fq "'Ban Lan Gen'" "$CATEGORY_TEMP"
grep -Fq "'Shan Dou Gen'" "$CATEGORY_TEMP"
grep -Fq "'Tu Fu Ling'" "$CATEGORY_TEMP"
grep -Fq "'Pu Gong Ying'" "$CATEGORY_TEMP"
grep -Fq "'Bai Jiang Cao'" "$CATEGORY_TEMP"
grep -Fq "'He Ye'" "$CATEGORY_TEMP"
grep -Fq '$dampheatherbs = [' "$CATEGORY_TEMP"
grep -Fq '$dampheatsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Huang Qin'" "$CATEGORY_TEMP"
grep -Fq "'Huang Lian'" "$CATEGORY_TEMP"
grep -Fq "'Huang Bai'" "$CATEGORY_TEMP"
grep -Fq "'Long Dan Cao'" "$CATEGORY_TEMP"
grep -Fq "'Ku Shen'" "$CATEGORY_TEMP"
grep -Fq '$bloodheatherbs = [' "$CATEGORY_TEMP"
grep -Fq '$bloodheatsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Shui Niu Jiao'" "$CATEGORY_TEMP"
grep -Fq "'Sheng Di Huang'" "$CATEGORY_TEMP"
grep -Fq "'Xuan Shen'" "$CATEGORY_TEMP"
grep -Fq "'Mu Dan Pi'" "$CATEGORY_TEMP"
grep -Fq "'Zi Cao'" "$CATEGORY_TEMP"
grep -Fq '$clearheatherbs = [' "$CATEGORY_TEMP"
grep -Fq '$clearheatsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Shi Gao'" "$CATEGORY_TEMP"
grep -Fq "'Zhi Mu'" "$CATEGORY_TEMP"
grep -Fq "'Tian Hua Fen'" "$CATEGORY_TEMP"
grep -Fq "'Zhi Zi'" "$CATEGORY_TEMP"
grep -Fq "'Jue Ming Zi'" "$CATEGORY_TEMP"
grep -Fq '$windheatherbs = [' "$CATEGORY_TEMP"
grep -Fq '$windheatsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Bo He'" "$CATEGORY_TEMP"
grep -Fq "'Niu Bang Zi'" "$CATEGORY_TEMP"
grep -Fq "'Dan Dou Chi'" "$CATEGORY_TEMP"
grep -Fq "'Chai Hu'" "$CATEGORY_TEMP"
grep -Fq "'Ge Gen'" "$CATEGORY_TEMP"
similarities_block_count="$(grep -Fc 'class="herb-category-library__similarities"' "$CATEGORY_TEMP")"
comparison_table_count="$(grep -Fc 'class="herb-category-library__comparison-table"' "$CATEGORY_TEMP")"
[[ "$similarities_block_count" -eq 6 && "$comparison_table_count" -eq 6 ]] || {
    echo "ERROR: las seis categorías no contienen ambos bloques comparativos." >&2
    exit 1
}
grep -Fq "Similarities and Clinical Overlaps" "$CATEGORY_TEMP"
similarities_line="$(grep -n -m1 'class="herb-category-library__similarities"' "$CATEGORY_TEMP")"
similarities_line="${similarities_line%%:*}"
comparison_table_line="$(grep -n -m1 'class="herb-category-library__comparison-table"' "$CATEGORY_TEMP")"
comparison_table_line="${comparison_table_line%%:*}"
[[ "$similarities_line" -lt "$comparison_table_line" ]] || {
    echo "ERROR: Similarities and Clinical Overlaps no aparece encima de la tabla detallada." >&2
    exit 1
}
grep -Fq "Herb / Pharmaceutical name" "$CATEGORY_TEMP"
grep -Fq 'class="herb-category-library__comparison-table"' "$CATEGORY_TEMP"
grep -Fq "table-layout:fixed" "$CATEGORY_TEMP"
grep -Fq "nth-child(7){width:8%}" "$CATEGORY_TEMP"
grep -Fq "nth-child(8){width:14%}" "$CATEGORY_TEMP"
grep -Fq ".herb-category-library__dosage{white-space:normal;text-align:center;overflow-wrap:anywhere}" "$CATEGORY_TEMP"
if grep -Fq ".herb-category-library__dosage{white-space:nowrap" "$CATEGORY_TEMP"; then
    echo "ERROR: Dosage todavía impide el ajuste de texto y se superpone con Preparation." >&2
    exit 1
fi
grep -Fq "'Xin Yi'" "$CATEGORY_TEMP"
grep -Fq "'Cong Bai'" "$CATEGORY_TEMP"
grep -Fq "'Dan Dou Chi'" "$CATEGORY_TEMP"

grep -Fq "[45, 'Single Herbs', 'questions']" "$MENU_TEMP"
grep -Fq "[44, 'Formulas', 'questions']" "$MENU_TEMP"
if grep -Fq "[45, 'Single Herbs', 'questions', null, null, null," "$MENU_TEMP"; then
    echo "ERROR: Single Herbs School Level todavía usa una ruta personalizada de Board Level." >&2
    exit 1
fi
grep -Fq "\$libraryaction = '<a class=\"board-hub__action board-hub__action--guide '" "$MENU_TEMP"
grep -Fq ".board-hub__card--board-level .board-hub__action--guide.comparisons-dashboard-link {" "$STYLES_TEMP"
grep -Fq "background: #087c69 !important;" "$STYLES_TEMP"
grep -Fq "color: #fff !important;" "$STYLES_TEMP"
grep -Fq ". 'comparisons-dashboard-link\" href=\"' . \$libraryurl->out(false) . '\">Study Guide</a>';" "$MENU_TEMP"
if grep -Fq "\$libraryaction = '<a class=\"board-hub__action board-hub__action--board-library '" "$MENU_TEMP"; then
    echo "ERROR: Study Guide todavía conserva el estilo naranja." >&2
    exit 1
fi
grep -Fq "if (\$showstaffresources && \$subject['area'] !== 'herbology')" "$MENU_TEMP"
if grep -Fq ". 'comparisons-dashboard-link\" href=\"' . \$libraryurl->out(false) . '\">Library</a>';" "$MENU_TEMP"; then
    echo "ERROR: el botón naranja de Herbology todavía conserva Library." >&2
    exit 1
fi

if grep -Fq "Previous page" "$LIBRARY_TEMP" \
        || grep -Fq "Página anterior" "$LIBRARY_TEMP" \
        || grep -Fq "data-library-page-back" "$LIBRARY_TEMP"; then
    echo "ERROR: el botón Previous page todavía aparece en la cabecera." >&2
    exit 1
fi
grep -Fq "data-library-dashboard" "$LIBRARY_TEMP"

# Cada botón de los tres primeros módulos debe resolver una sola vista.
grep -Fq "['guides', 'comparisons', 'other', 'practice']" "$LIBRARY_TEMP"
if grep -Fq '$studyguidesblockurl' "$LIBRARY_TEMP"; then
    echo "ERROR: Main Study Guides todavía aparece como un solo botón." >&2
    exit 1
fi
grep -Fq "'/blocks/myprogress/single-herbs-study-guide.php'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/dui-yao.html'" "$LIBRARY_TEMP"
grep -Fq "'/blocks/myprogress/formulas-study-guide.php'" "$LIBRARY_TEMP"
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
other_chapters_loop_count="$(grep -Fc '$otherchapters as $chapter' "$LIBRARY_TEMP")"
secondary_guide_loop_count="$(grep -Fc '$secondaryguidechapters as $chapter' "$LIBRARY_TEMP")"
[[ "$other_chapters_loop_count" -eq 1 && "$secondary_guide_loop_count" -eq 1 ]] || {
    echo "ERROR: Secondary Study Guide no contiene la lista completa de Other chapters." >&2
    exit 1
}
if grep -Fq '$herbmiscchapters as $chapter' "$LIBRARY_TEMP" \
        || grep -Fq '$formulamiscchapters as $chapter' "$LIBRARY_TEMP" \
        || grep -Fq '$isHerbChapter' "$LIBRARY_TEMP" \
        || grep -Fq '$isFormulaChapter' "$LIBRARY_TEMP"; then
    echo "ERROR: todavía existe la división anterior entre Herbs y Formulas Miscellaneous." >&2
    exit 1
fi
grep -Fq "\$secondaryguidechapters = array_values(array_filter(" "$LIBRARY_TEMP"
grep -Fq "return !in_array(\$chapter['path'] ?? '', [" "$LIBRARY_TEMP"
grep -Fq "'title' => 'Herbs Secondary Actions'" "$LIBRARY_TEMP"
grep -Fq "'Herbs Secondary Actions' => 'Acciones secundarias de hierbas'" "$LIBRARY_TEMP"
if grep -Fq "'title' => 'Secondary Actions'" "$LIBRARY_TEMP"; then
    echo "ERROR: Secondary Actions todavía conserva el nombre anterior." >&2
    exit 1
fi
grep -Fq '$secondarychapterurl = $makechapterurl($chapter);' "$LIBRARY_TEMP"
grep -Fq '$secondarychapterurl->out(false)' "$LIBRARY_TEMP"
grep -Fq "<b><?php echo s(\$ises ? 'Material complementario' : 'Supplemental material'); ?></b>" "$LIBRARY_TEMP"
journey_card_count="$(grep -Fc '<section class="subject-study-library__journey-card">' "$LIBRARY_TEMP")"
[[ "$journey_card_count" -eq 7 ]] || {
    echo "ERROR: el mapa Board Level no contiene exactamente siete módulos." >&2
    exit 1
}
for step in 1 2 3 4 5 6 7; do
    grep -Fq "<span class=\"subject-study-library__journey-step\">$step</span>" "$LIBRARY_TEMP"
done
main_study_guide_block="$(sed -n '/subject-study-library__journey-step">1</,/subject-study-library__journey-step">2</p' "$LIBRARY_TEMP")"
secondary_study_guides_block="$(sed -n '/subject-study-library__journey-step">2</,/subject-study-library__journey-step">3</p' "$LIBRARY_TEMP")"
secondary_guide_block="$(sed -n '/subject-study-library__journey-step">3</,/subject-study-library__journey-step">4</p' "$LIBRARY_TEMP")"
grep -Fq "Safety Library" <<< "$main_study_guide_block"
grep -Fq '$safetylibraryurl->out(false)' <<< "$main_study_guide_block"
grep -Fq "Formula Composition" <<< "$main_study_guide_block"
grep -Fq '$formulacompositionurl->out(false)' <<< "$main_study_guide_block"
grep -Fq "Herbal Incompatibilities" <<< "$main_study_guide_block"
grep -Fq '$herbalincompatibilitiesurl->out(false)' <<< "$main_study_guide_block"
grep -Fq "Most Important Formulas" <<< "$main_study_guide_block"
grep -Fq '$mostimportantformulasurl->out(false)' <<< "$main_study_guide_block"
grep -Fq "Formula Modifications" <<< "$main_study_guide_block"
grep -Fq '$formulamodificationsurl->out(false)' <<< "$main_study_guide_block"
grep -Fq "Commonly Used Herbal Combinations" <<< "$main_study_guide_block"
grep -Fq '$commonherbalcombinationsurl->out(false)' <<< "$main_study_guide_block"
if grep -Fq '$safetylibraryurl->out(false)' <<< "$secondary_guide_block"; then
    echo "ERROR: Safety Library todavía aparece en Secondary Study Guide." >&2
    exit 1
fi
grep -Fq "Secondary Study Guide" <<< "$secondary_guide_block"
grep -Fq '$secondaryguidechapters as $chapter' <<< "$secondary_guide_block"
grep -Fq '$secondarychapterurl->out(false)' <<< "$secondary_guide_block"
grep -Fq "Single Herbs Comparisons" <<< "$secondary_study_guides_block"
grep -Fq "Herbs Category Comparison" <<< "$secondary_study_guides_block"
grep -Fq '$herbscategorycomparisonurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq "Formula Comparisons" <<< "$secondary_study_guides_block"
grep -Fq "63 Formulas Comparison" <<< "$secondary_study_guides_block"
grep -Fq "Bensky Category Crosswalk" <<< "$secondary_study_guides_block"
grep -Fq "Pathology Comparisons" <<< "$secondary_study_guides_block"
grep -Fq "Pattern Comparisons" <<< "$secondary_study_guides_block"
grep -Fq "Pattern Comparison Maciocia" <<< "$secondary_study_guides_block"
grep -Fq "\$ises ? 'Estudio conectado de fórmulas' : 'Connected formula study'" "$LIBRARY_TEMP"
grep -Fq "'63 Formulas Comparison' => 'Comparación de 63 fórmulas'" "$LIBRARY_TEMP"
grep -Fq "'Pattern Comparison Maciocia' => 'Comparación de patrones Maciocia'" "$LIBRARY_TEMP"
if grep -Fq "'title' => 'Formula Study Center'" "$LIBRARY_TEMP"; then
    echo "ERROR: el recurso todavía conserva el nombre Formula Study Center." >&2
    exit 1
fi
if grep -Fq "'title' => 'Patterns Tips Maciocia'" "$LIBRARY_TEMP"; then
    echo "ERROR: Patterns Tips Maciocia todavía conserva el nombre anterior." >&2
    exit 1
fi
grep -Fq '$singleherbscomparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$formulacomparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$formulastudycenterurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$benskycrosswalkurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$pathologycomparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$patterncomparisonsurl->out(false)' <<< "$secondary_study_guides_block"
grep -Fq '$patternmaciociacomparisonurl->out(false)' <<< "$secondary_study_guides_block"
main_study_guide_button_count="$(grep -Fc 'subject-study-library__journey-option"' <<< "$main_study_guide_block")"
comparison_button_count="$(grep -Fc 'subject-study-library__journey-option"' <<< "$secondary_study_guides_block")"
[[ "$main_study_guide_button_count" -eq 9 && "$comparison_button_count" -eq 8 ]] || {
    echo "ERROR: Main Study Guide debe tener nueve botones y Comparison debe tener ocho." >&2
    exit 1
}
for comparison_route in \
        '$singleherbscomparisonsurl->out(false)' \
        '$formulacomparisonsurl->out(false)' \
        '$formulastudycenterurl->out(false)' \
        '$benskycrosswalkurl->out(false)' \
        '$pathologycomparisonsurl->out(false)' \
        '$patterncomparisonsurl->out(false)' \
        '$patternmaciociacomparisonurl->out(false)'; do
    if grep -Fq "$comparison_route" <<< "$main_study_guide_block"; then
        echo "ERROR: un botón de Comparison todavía aparece en Main Study Guide." >&2
        exit 1
    fi
done
if grep -Fq '$comparisonsurl' "$LIBRARY_TEMP" || grep -Fq '$otherchaptersurl' "$LIBRARY_TEMP"; then
    echo "ERROR: todavía aparece un botón general Comparisons u Other chapters." >&2
    exit 1
fi
grep -Fq "'/study-guide/comparisons/safety-es.html' : '/study-guide/comparisons/safety.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/safety.html'," "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/formula-theory.html'," "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/herbal-incompatibilities.html'," "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/most-important-formulas.html'," "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/formula-modifications.html'," "$LIBRARY_TEMP"
grep -Fq "'/blocks/myprogress/common-herbal-combinations.php'," "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/index-es.html' : '/study-guide/comparisons/index.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/formulas-es.html' : '/study-guide/comparisons/formulas.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/formula-navigator-es.html' : '/study-guide/comparisons/formula-navigator.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/formula-navigator.html'," "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/pathologies-es.html' : '/study-guide/comparisons/pathologies.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/patterns-es.html' : '/study-guide/comparisons/patterns.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/patterns-tips-maciocia-es.html' : '/study-guide/comparisons/patterns-tips-maciocia.html'" "$LIBRARY_TEMP"
grep -Fq "'/study-guide/comparisons/patterns-tips-maciocia.html'," "$LIBRARY_TEMP"
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
safety_library_route_count="$(grep -Fc '$safetylibraryurl->out(false)' "$LIBRARY_TEMP")"
formula_composition_route_count="$(grep -Fc '$formulacompositionurl->out(false)' "$LIBRARY_TEMP")"
herbal_incompatibilities_route_count="$(grep -Fc '$herbalincompatibilitiesurl->out(false)' "$LIBRARY_TEMP")"
most_important_formulas_route_count="$(grep -Fc '$mostimportantformulasurl->out(false)' "$LIBRARY_TEMP")"
formula_modifications_route_count="$(grep -Fc '$formulamodificationsurl->out(false)' "$LIBRARY_TEMP")"
common_herbal_combinations_route_count="$(grep -Fc '$commonherbalcombinationsurl->out(false)' "$LIBRARY_TEMP")"
single_herbs_comparisons_route_count="$(grep -Fc '$singleherbscomparisonsurl->out(false)' "$LIBRARY_TEMP")"
herbs_category_comparison_route_count="$(grep -Fc '$herbscategorycomparisonurl->out(false)' "$LIBRARY_TEMP")"
formula_comparisons_route_count="$(grep -Fc '$formulacomparisonsurl->out(false)' "$LIBRARY_TEMP")"
formula_study_center_route_count="$(grep -Fc '$formulastudycenterurl->out(false)' "$LIBRARY_TEMP")"
bensky_crosswalk_route_count="$(grep -Fc '$benskycrosswalkurl->out(false)' "$LIBRARY_TEMP")"
pathology_comparisons_route_count="$(grep -Fc '$pathologycomparisonsurl->out(false)' "$LIBRARY_TEMP")"
pattern_comparisons_route_count="$(grep -Fc '$patterncomparisonsurl->out(false)' "$LIBRARY_TEMP")"
pattern_maciocia_route_count="$(grep -Fc '$patternmaciociacomparisonurl->out(false)' "$LIBRARY_TEMP")"
[[ "$single_herbs_study_route_count" -eq 1 && "$formulas_study_route_count" -eq 1 \
        && "$safety_library_route_count" -eq 1 \
        && "$formula_composition_route_count" -eq 1 && "$herbal_incompatibilities_route_count" -eq 1 \
        && "$most_important_formulas_route_count" -eq 1 && "$formula_modifications_route_count" -eq 1 \
        && "$common_herbal_combinations_route_count" -eq 1 \
        && "$single_herbs_comparisons_route_count" -eq 1 && "$herbs_category_comparison_route_count" -eq 1 \
        && "$formula_comparisons_route_count" -eq 1 \
        && "$formula_study_center_route_count" -eq 1 \
        && "$bensky_crosswalk_route_count" -eq 1 && "$pathology_comparisons_route_count" -eq 1 \
        && "$pattern_comparisons_route_count" -eq 1 && "$pattern_maciocia_route_count" -eq 1 ]] || {
    echo "ERROR: los botones de Main Study Guide y Comparison están incompletos o duplicados." >&2
    exit 1
}
grep -Fq '.subject-study-library__journey-card:nth-child(5){border-top-color:#6d4bc3}' "$LIBRARY_TEMP"
grep -Fq '.subject-study-library__journey-card:nth-child(6){border-top-color:#2563eb}' "$LIBRARY_TEMP"
grep -Fq '.subject-study-library__journey-card:nth-child(7){border-top-color:#f77e25}' "$LIBRARY_TEMP"
if grep -Fq '.subject-study-library__journey-card:nth-child(5){grid-column:span 3' "$LIBRARY_TEMP" \
        || grep -Fq '.subject-study-library__journey-card:nth-child(6){grid-column:span 3' "$LIBRARY_TEMP" \
        || grep -Fq '.subject-study-library__journey-card:nth-child(7){grid-column:span 3' "$LIBRARY_TEMP"; then
    echo "ERROR: los siete módulos no conservan el diseño uniforme." >&2
    exit 1
fi
grep -Fq "\$ises ? 'Categorías de hierbas individuales' : 'Single Herbs Categories'" "$LIBRARY_TEMP"
grep -Fq "\$ises ? 'Categorías de pares Duì Yào' : 'Duì Yào Pairs Categories'" "$LIBRARY_TEMP"
grep -Fq "\$ises ? 'Categorías de fórmulas' : 'Formulas Categories'" "$LIBRARY_TEMP"
if grep -Fq "\$ises ? 'Guía de estudio de hierbas individuales' : 'Single Herbs Study Guide'" "$LIBRARY_TEMP" \
        || grep -Fq "\$ises ? 'Guía de estudio de pares Duì Yào' : 'Duì Yào Pairs Study Guide'" "$LIBRARY_TEMP" \
        || grep -Fq "\$ises ? 'Guía de estudio de fórmulas' : 'Formulas Study Guide'" "$LIBRARY_TEMP"; then
    echo "ERROR: uno de los tres botones principales todavía conserva Study Guide." >&2
    exit 1
fi
grep -Fq '.subject-study-library__journey-option-heading{color:inherit;font-size:inherit;font-weight:inherit' "$LIBRARY_TEMP"
grep -Fq '.subject-study-library__journey-options{display:grid;gap:7px;margin-top:0}' "$LIBRARY_TEMP"
if grep -Fq '.subject-study-library__journey-options{display:grid;gap:7px;margin-top:auto}' "$LIBRARY_TEMP"; then
    echo "ERROR: los botones todavía están empujados hacia la parte inferior." >&2
    exit 1
fi
grep -Fq '.subject-study-library__journey-option small{display:block;margin-top:4px;color:#64748b;font-size:13px' "$LIBRARY_TEMP"
if grep -Fq '.subject-study-library__journey-option small{display:block;margin-top:3px;color:#64748b;font-size:11px' "$LIBRARY_TEMP"; then
    echo "ERROR: el segundo renglón de los botones todavía usa 11px." >&2
    exit 1
fi
grep -Fq "<strong>Herbology Practice</strong>" "$LIBRARY_TEMP"
grep -Fq "<strong>Quiz</strong>" "$LIBRARY_TEMP"
grep -Fq "<strong><?php echo s(\$ises ? 'Exámenes' : 'Exams'); ?></strong>" "$LIBRARY_TEMP"
grep -Fq "<strong><?php echo s(\$ises ? 'Simulación' : 'Simulation'); ?></strong>" "$LIBRARY_TEMP"
formula_study_block="$(sed -n "/'title' => '63 Formulas Comparison'/,+8p" "$LIBRARY_TEMP")"
grep -Fq "'secondary' => true" <<< "$formula_study_block"
if grep -Fq "'practice_exam' => true" <<< "$formula_study_block"; then
    echo "ERROR: 63 Formulas Comparison todavía pertenece a Practice Exams." >&2
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
cp -a "$MENU_TARGET" "$BACKUP_DIR/blocks/menu/block_menu.php"
cp -a "$STYLES_TARGET" "$BACKUP_DIR/blocks/menu/styles.css"
if [[ -f "$CATEGORY_TARGET" ]]; then
    cp -a "$CATEGORY_TARGET" "$BACKUP_DIR/blocks/myprogress/herbs-category-comparison.php"
fi

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
install_preserving_metadata "$MENU_TEMP" "$MENU_TARGET"
install_preserving_metadata "$STYLES_TEMP" "$STYLES_TARGET"
if [[ -f "$CATEGORY_TARGET" ]]; then
    install_preserving_metadata "$CATEGORY_TEMP" "$CATEGORY_TARGET"
else
    category_owner_group="$(stat -c '%u:%g' "$LIBRARY_TARGET")"
    category_mode="$(stat -c '%a' "$LIBRARY_TARGET")"
    install -o "${category_owner_group%:*}" -g "${category_owner_group#*:}" -m "$category_mode" \
        "$CATEGORY_TEMP" "$CATEGORY_TARGET"
fi

if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
    runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
else
    php "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "OK: Single Herbs School Level vuelve a abrir el curso 45, igual que Formulas abre el curso 44."
echo "OK: Herbology muestra un solo botón Study Guide con el estilo verde."
echo "OK: Previous page fue eliminado; Español y Dashboard permanecen en la cabecera."
echo "OK: la hoja styles.css instala una regla verde específica con prioridad."
echo "OK: Comparison quedó como módulo 2 con ocho botones independientes."
echo "OK: Herbs Category Comparison abre una página nueva preparada para tablas por categoría."
echo "OK: Herb y Pharmaceutical name están combinadas; las columnas compactas dejan visible Preparation."
echo "OK: Dosage permite varias líneas y Preparation usa una columna más ancha."
echo "OK: Similarities and Clinical Overlaps aparece encima de la tabla detallada."
echo "OK: Disperse Wind-Heat quedó como categoría 2 con once hierbas y sus semejanzas clínicas."
echo "OK: Clear and Purge Heat quedó como categoría 3 con ocho hierbas y sus semejanzas clínicas."
echo "OK: Clear Blood-Heat quedó como categoría 4 con cinco hierbas y sus semejanzas clínicas."
echo "OK: Clear Damp-Heat quedó como categoría 5 con cinco hierbas y sus semejanzas clínicas."
echo "OK: Clear Toxic-Heat quedó como categoría 6 con nueve hierbas y sus semejanzas clínicas."
echo "OK: 63 Formulas Comparison está en Comparison y excluido de Formulas Miscellaneous."
echo "OK: Pattern Comparison Maciocia está en Comparison y excluido de los otros módulos."
echo "OK: el segundo renglón de los botones usa 13px."
echo "OK: los botones comienzan inmediatamente debajo del título de cada módulo."
echo "OK: Secondary Study Guide contiene todos los recursos secundarios de hierbas y fórmulas."
echo "OK: Comparison ocupa la posición 2 y Secondary Study Guide ocupa la 3."
echo "OK: Safety Library está en Main Study Guide y excluido de Secondary Study Guide."
echo "OK: Main Study Guide contiene nueve botones independientes."
echo "OK: Formula Composition y Herbal Incompatibilities están en Main Study Guide y excluidos de Secondary Study Guide."
echo "OK: Most Important Formulas, Formula Modifications y Commonly Used Herbal Combinations están en Main Study Guide."
echo "OK: las exclusiones de recursos se validan sin depender del punto y coma."
echo "OK: las validaciones de los módulos 1, 2 y 3 usan límites exactos."
echo "OK: Herbs Secondary Actions está dentro de Secondary Study Guide."
echo "OK: los ocho botones comparativos están en Comparison."
echo "OK: ambas colecciones fueron retiradas de la vista general Comparisons sin eliminar su contenido."
echo "OK: Bensky Category Crosswalk, Pathology y Pattern están en Comparison."
echo "OK: el botón general Comparisons fue eliminado."
echo "OK: el botón general Other chapters fue eliminado."
echo "OK: el módulo 1 se llama Main Study Guide."
echo "OK: Single Herbs, Duì Yào Pairs y Formulas usan Categories como nombre del botón."
echo "OK: los módulos 3, 5 y 6 fueron eliminados; los siete restantes quedaron renumerados y uniformes."
echo "OK: cada botón visible abre un solo bloque."
echo "OK: se conservaron Main Study Guide, los recursos secundarios, las colecciones comparativas, Practice Exams y Quiz."
echo "OK: la vista general interna permanece como respaldo, pero no se muestra como botón."
echo "Respaldo: $BACKUP_DIR"
