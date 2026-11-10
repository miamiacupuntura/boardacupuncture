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
for command in curl php install grep sed stat cp cmp mkdir mktemp rm sha256sum wc; do
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
grep -Fq '$languageurl = new moodle_url(' "$CATEGORY_TEMP"
grep -Fq 'herb-category-library__actions' "$CATEGORY_TEMP"
grep -Fq 'hreflang=' "$CATEGORY_TEMP"
grep -Fq 'Español' "$CATEGORY_TEMP"
grep -Fq "'English'" "$CATEGORY_TEMP"
grep -Fq "optional_param('ui', '', PARAM_ALPHA)" "$CATEGORY_TEMP"
grep -Fq '$moodlelang = current_language();' "$CATEGORY_TEMP"
grep -Fq '$PAGE->set_cacheable(false);' "$CATEGORY_TEMP"
grep -Fq 'data-language="<?php echo $ises' "$CATEGORY_TEMP"
grep -Fq 'Idioma: Español' "$CATEGORY_TEMP"
grep -Fq "'ui' => 'es'" "$CATEGORY_TEMP"
grep -Fq "'ui' => 'en'" "$CATEGORY_TEMP"
grep -Fq "'ui' => 'es'" "$LIBRARY_TEMP"
grep -Fq "'ui' => 'en'" "$LIBRARY_TEMP"
grep -Fq 'function board_herb_category_localize' "$CATEGORY_TEMP"
grep -Fq 'return $translations[$text] ?? $text;' "$CATEGORY_TEMP"
if grep -Fq 'return strtr($text, $translations);' "$CATEGORY_TEMP"; then
    echo "ERROR: la traducción todavía usa sustituciones parciales que producen Spanglish." >&2
    exit 1
fi
grep -Fq 'Libera intensamente el patrón exterior de exceso' "$CATEGORY_TEMP"
grep -Fq 'Aclara la cabeza y los ojos; beneficia la garganta' "$CATEGORY_TEMP"
grep -Fq 'Elimina Calor intenso del nivel Qi' "$CATEGORY_TEMP"
grep -Fq 'Ambas eliminan Calor interno intenso' "$CATEGORY_TEMP"
grep -Fq 'Semejanzas y superposiciones clínicas' "$CATEGORY_TEMP"
grep -Fq 'Hierba / nombre farmacéutico' "$CATEGORY_TEMP"
grep -Fq 'Funciones e indicaciones principales' "$CATEGORY_TEMP"
grep -Fq 'Precauciones / contraindicaciones' "$CATEGORY_TEMP"
grep -Fq '12. Dispersar Viento-Humedad' "$CATEGORY_TEMP"
grep -Fq 'Guía Board 2026' "$CATEGORY_TEMP"
grep -Fq "optional_param('category', '', PARAM_ALPHANUMEXT)" "$CATEGORY_TEMP"
grep -Fq 'foreach ($categoryids as $categoryid)' "$CATEGORY_TEMP"
grep -Fq "\$languageparams['category'] = \$selectedcategory;" "$CATEGORY_TEMP"
grep -Fq 'Selecciona una categoría para abrir solamente su tabla comparativa.' "$CATEGORY_TEMP"
grep -Fq '$previouscategoryurl = null;' "$CATEGORY_TEMP"
grep -Fq '$nextcategoryurl = null;' "$CATEGORY_TEMP"
grep -Fq '$selectedcategoryindex = array_search($selectedcategory, $categoryids, true);' "$CATEGORY_TEMP"
grep -Fq '$previouscategoryurl = $categoryurls[$categoryids[$selectedcategoryindex - 1]];' "$CATEGORY_TEMP"
grep -Fq '$nextcategoryurl = $categoryurls[$categoryids[$selectedcategoryindex + 1]];' "$CATEGORY_TEMP"
grep -Fq '"Previous" => "Anterior"' "$CATEGORY_TEMP"
grep -Fq '"Next" => "Siguiente"' "$CATEGORY_TEMP"
grep -Fq '"Category navigation" => "Navegación de categorías"' "$CATEGORY_TEMP"
grep -Fq 'herb-category-library__section-heading' "$CATEGORY_TEMP"
grep -Fq 'herb-category-library__pager' "$CATEGORY_TEMP"
grep -Fq 'rel="prev"' "$CATEGORY_TEMP"
grep -Fq 'rel="next"' "$CATEGORY_TEMP"
grep -Fq '$PAGE->requires->js_init_code(' "$CATEGORY_TEMP"
grep -Fq 'id="herb-category-search"' "$CATEGORY_TEMP"
grep -Fq 'id="herb-category-search-clear"' "$CATEGORY_TEMP"
grep -Fq 'id="herb-category-search-status"' "$CATEGORY_TEMP"
grep -Fq 'id="herb-category-index"' "$CATEGORY_TEMP"
grep -Fq 'Buscar categorías o hierbas' "$CATEGORY_TEMP"
grep -Fq 'Search categories or herbs' "$CATEGORY_TEMP"
grep -Fq 'Escribe una categoría, hierba, función o precaución' "$CATEGORY_TEMP"
grep -Fq "normalize('NFD')" "$CATEGORY_TEMP"
grep -Fq "event.key === 'Enter'" "$CATEGORY_TEMP"
grep -Fq "event.key === 'Escape'" "$CATEGORY_TEMP"
grep -Fq 'document.createTreeWalker' "$CATEGORY_TEMP"
grep -Fq 'normaliseWithMap' "$CATEGORY_TEMP"
grep -Fq 'showCategory(section.id)' "$CATEGORY_TEMP"
grep -Fq 'activateMatch(currentMatch + 1, true)' "$CATEGORY_TEMP"
grep -Fq 'activateMatch(currentMatch - 1, true)' "$CATEGORY_TEMP"
grep -Fq 'id="herb-category-search-previous"' "$CATEGORY_TEMP"
grep -Fq 'id="herb-category-search-next"' "$CATEGORY_TEMP"
grep -Fq 'Coincidencia anterior' "$CATEGORY_TEMP"
grep -Fq 'Siguiente coincidencia' "$CATEGORY_TEMP"
grep -Fq 'herb-category-library__search-highlight.is-current' "$CATEGORY_TEMP"
grep -Fq 'rgba(255,193,7,.48)' "$CATEGORY_TEMP"
grep -Fq 'data-results=' "$CATEGORY_TEMP"
search_input_count="$(grep -Fo 'id="herb-category-search"' "$CATEGORY_TEMP" | wc -l)"
search_previous_count="$(grep -Fo 'id="herb-category-search-previous"' "$CATEGORY_TEMP" | wc -l)"
search_next_count="$(grep -Fo 'id="herb-category-search-next"' "$CATEGORY_TEMP" | wc -l)"
search_clear_count="$(grep -Fo 'id="herb-category-search-clear"' "$CATEGORY_TEMP" | wc -l)"
search_status_count="$(grep -Fo 'id="herb-category-search-status"' "$CATEGORY_TEMP" | wc -l)"
search_index_count="$(grep -Fo 'id="herb-category-index"' "$CATEGORY_TEMP" | wc -l)"
[[ "$search_input_count" -eq 1 && "$search_previous_count" -eq 1 && "$search_next_count" -eq 1 && "$search_clear_count" -eq 1 && "$search_status_count" -eq 1 && "$search_index_count" -eq 1 ]] || {
    echo "ERROR: la búsqueda no contiene exactamente un campo, dos controles de coincidencias, Borrar, estado e índice." >&2
    echo "Conteos: campo=$search_input_count anterior=$search_previous_count siguiente=$search_next_count borrar=$search_clear_count estado=$search_status_count índice=$search_index_count" >&2
    exit 1
}
category_button_count="$(grep -Fo "\$categoryurls['" "$CATEGORY_TEMP" | wc -l)"
hidden_category_count="$(grep -Fo "? '' : ' hidden'" "$CATEGORY_TEMP" | wc -l)"
[[ "$category_button_count" -eq 32 && "$hidden_category_count" -eq 32 ]] || {
    echo "ERROR: los treinta y dos botones no abren exactamente una categoría cada uno." >&2
    echo "Conteos: botones=$category_button_count secciones=$hidden_category_count" >&2
    exit 1
}
previous_category_link_count="$(grep -Fo 'rel="prev"' "$CATEGORY_TEMP" | wc -l)"
next_category_link_count="$(grep -Fo 'rel="next"' "$CATEGORY_TEMP" | wc -l)"
category_pager_count="$(grep -Fo '<nav class="herb-category-library__pager"' "$CATEGORY_TEMP" | wc -l)"
[[ "$previous_category_link_count" -eq 32 && "$next_category_link_count" -eq 32 && "$category_pager_count" -eq 32 ]] || {
    echo "ERROR: los controles Anterior y Siguiente no están presentes en las treinta y dos categorías." >&2
    echo "Conteos: anteriores=$previous_category_link_count siguientes=$next_category_link_count navegadores=$category_pager_count" >&2
    exit 1
}
localized_herb_field_count="$(grep -Fo 'board_herb_category_localize($herb[' "$CATEGORY_TEMP" | wc -l)"
localized_similarity_field_count="$(grep -Fo 'board_herb_category_localize($similarity[' "$CATEGORY_TEMP" | wc -l)"
localized_title_count="$(grep -Fo '<h3><?php echo s(board_herb_category_localize' "$CATEGORY_TEMP" | wc -l)"
localized_summary_count="$(grep -Fo 'class="herb-category-library__summary"><?php echo s(board_herb_category_localize' "$CATEGORY_TEMP" | wc -l)"
[[ "$localized_herb_field_count" -eq 224 && "$localized_similarity_field_count" -eq 96 && "$localized_title_count" -eq 32 && "$localized_summary_count" -eq 32 ]] || {
    echo "ERROR: la vista en Español no localiza todos los campos de las treinta y dos categorías." >&2
    echo "Conteos: hierbas=$localized_herb_field_count semejanzas=$localized_similarity_field_count títulos=$localized_title_count resúmenes=$localized_summary_count" >&2
    exit 1
}
grep -Fq 'id="disperse-wind-cold"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['disperse-wind-cold']" "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['disperse-wind-heat']" "$CATEGORY_TEMP"
grep -Fq 'id="disperse-wind-heat"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['clear-and-purge-heat']" "$CATEGORY_TEMP"
grep -Fq 'id="clear-and-purge-heat"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['clear-blood-heat']" "$CATEGORY_TEMP"
grep -Fq 'id="clear-blood-heat"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['clear-damp-heat']" "$CATEGORY_TEMP"
grep -Fq 'id="clear-damp-heat"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['clear-toxic-heat']" "$CATEGORY_TEMP"
grep -Fq 'id="clear-toxic-heat"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['clear-deficient-heat']" "$CATEGORY_TEMP"
grep -Fq 'id="clear-deficient-heat"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['purgatives']" "$CATEGORY_TEMP"
grep -Fq 'id="purgatives"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['laxatives']" "$CATEGORY_TEMP"
grep -Fq 'id="laxatives"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['cathartics']" "$CATEGORY_TEMP"
grep -Fq 'id="cathartics"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['diuretics']" "$CATEGORY_TEMP"
grep -Fq 'id="diuretics"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['dispel-wind-damp']" "$CATEGORY_TEMP"
grep -Fq 'id="dispel-wind-damp"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['dissolve-hot-phlegm']" "$CATEGORY_TEMP"
grep -Fq 'id="dissolve-hot-phlegm"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['dissolve-cold-phlegm']" "$CATEGORY_TEMP"
grep -Fq 'id="dissolve-cold-phlegm"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['stop-cough-wheezing']" "$CATEGORY_TEMP"
grep -Fq 'id="stop-cough-wheezing"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['aromatics-for-damp']" "$CATEGORY_TEMP"
grep -Fq 'id="aromatics-for-damp"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['digestives']" "$CATEGORY_TEMP"
grep -Fq 'id="digestives"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['regulate-qi']" "$CATEGORY_TEMP"
grep -Fq 'id="regulate-qi"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['stop-bleeding']" "$CATEGORY_TEMP"
grep -Fq 'id="stop-bleeding"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['activate-blood']" "$CATEGORY_TEMP"
grep -Fq 'id="activate-blood"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['warm-interior']" "$CATEGORY_TEMP"
grep -Fq 'id="warm-interior"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['qi-tonics']" "$CATEGORY_TEMP"
grep -Fq 'id="qi-tonics"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['blood-tonics']" "$CATEGORY_TEMP"
grep -Fq 'id="blood-tonics"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['yang-tonics']" "$CATEGORY_TEMP"
grep -Fq 'id="yang-tonics"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['yin-tonics']" "$CATEGORY_TEMP"
grep -Fq 'id="yin-tonics"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['astringents']" "$CATEGORY_TEMP"
grep -Fq 'id="astringents"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['anchor-calm-shen']" "$CATEGORY_TEMP"
grep -Fq 'id="anchor-calm-shen"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['nourish-calm-shen']" "$CATEGORY_TEMP"
grep -Fq 'id="nourish-calm-shen"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['orifice-opening-aromatics']" "$CATEGORY_TEMP"
grep -Fq 'id="orifice-opening-aromatics"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['extinguish-liver-wind']" "$CATEGORY_TEMP"
grep -Fq 'id="extinguish-liver-wind"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['expel-parasites']" "$CATEGORY_TEMP"
grep -Fq 'id="expel-parasites"' "$CATEGORY_TEMP"
grep -Fq "\$categoryurls['external-use']" "$CATEGORY_TEMP"
grep -Fq 'id="external-use"' "$CATEGORY_TEMP"
grep -Fq '$orificeherbs = [' "$CATEGORY_TEMP"
grep -Fq '$orificesimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($orificesimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq 'foreach ($orificeherbs as $herb)' "$CATEGORY_TEMP"
grep -Fq "'Niu Huang'" "$CATEGORY_TEMP"
grep -Fq '29. Aromáticas que abren los orificios' "$CATEGORY_TEMP"
grep -Fq 'Elimina Calor de Corazón, abre los orificios, despierta el Shen' "$CATEGORY_TEMP"
grep -Fq 'Niu Huang se administra en dosis pequeñas como píldora o polvo' "$CATEGORY_TEMP"
grep -Fq '$liverwindherbs = [' "$CATEGORY_TEMP"
grep -Fq '$liverwindsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($liverwindsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq 'foreach ($liverwindherbs as $herb)' "$CATEGORY_TEMP"
grep -Fq "'Tian Ma'" "$CATEGORY_TEMP"
grep -Fq "'Gou Teng'" "$CATEGORY_TEMP"
grep -Fq "'Shi Jue Ming'" "$CATEGORY_TEMP"
grep -Fq "'Bai Ji Li'" "$CATEGORY_TEMP"
grep -Fq "'Dai Zhe Shi'" "$CATEGORY_TEMP"
grep -Fq "'Di Long'" "$CATEGORY_TEMP"
grep -Fq '30. Extinguir Viento de Hígado' "$CATEGORY_TEMP"
grep -Fq 'Las seis sustancias calman Viento interno o sus manifestaciones' "$CATEGORY_TEMP"
grep -Fq 'origen por Calor o Frío del Viento interno' "$CATEGORY_TEMP"
grep -Fq '$parasiteherbs = [' "$CATEGORY_TEMP"
grep -Fq '$parasitesimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($parasitesimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq 'foreach ($parasiteherbs as $herb)' "$CATEGORY_TEMP"
grep -Fq "'Bing Lang'" "$CATEGORY_TEMP"
grep -Fq '31. Expulsar parásitos' "$CATEGORY_TEMP"
grep -Fq 'Elimina una amplia variedad de parásitos intestinales' "$CATEGORY_TEMP"
grep -Fq 'Como Bing Lang mueve el Qi hacia abajo' "$CATEGORY_TEMP"
grep -Fq '$externaluseherbs = [' "$CATEGORY_TEMP"
grep -Fq '$externalusesimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($externalusesimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq 'foreach ($externaluseherbs as $herb)' "$CATEGORY_TEMP"
grep -Fq "'She Chuang Zi'" "$CATEGORY_TEMP"
grep -Fq '32. Uso externo' "$CATEGORY_TEMP"
grep -Fq 'Seca Humedad, elimina parásitos y detiene picazón por vía tópica' "$CATEGORY_TEMP"
grep -Fq 'Su ligera toxicidad y naturaleza tibia exigen precaución interna' "$CATEGORY_TEMP"
grep -Fq '$coldphlegmherbs = [' "$CATEGORY_TEMP"
grep -Fq '$coldphlegmsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Tian Nan Xing'" "$CATEGORY_TEMP"
grep -Fq "'Ban Xia'" "$CATEGORY_TEMP"
grep -Fq "'Xuan Fu Hua'" "$CATEGORY_TEMP"
grep -Fq "'Bai Qian'" "$CATEGORY_TEMP"
grep -Fq "'Bai Jie Zi'" "$CATEGORY_TEMP"
grep -Fq "'Jie Geng'" "$CATEGORY_TEMP"
grep -Fq '14. Disolver Flema-Frío' "$CATEGORY_TEMP"
grep -Fq 'Seca Humedad y transforma flema' "$CATEGORY_TEMP"
grep -Fq 'Todas transforman flema, pero difieren' "$CATEGORY_TEMP"
grep -Fq '$coughherbs = [' "$CATEGORY_TEMP"
grep -Fq '$coughsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Xing Ren'" "$CATEGORY_TEMP"
grep -Fq "'Zi Su Zi'" "$CATEGORY_TEMP"
grep -Fq "'Zi Wan'" "$CATEGORY_TEMP"
grep -Fq "'Kuan Dong Hua'" "$CATEGORY_TEMP"
grep -Fq "'Bai Bu'" "$CATEGORY_TEMP"
grep -Fq "'Pi Pa Ye'" "$CATEGORY_TEMP"
grep -Fq "'Sang Bai Pi'" "$CATEGORY_TEMP"
grep -Fq '15. Detener la tos y las sibilancias' "$CATEGORY_TEMP"
grep -Fq 'Hace descender el Qi de Pulmón, detiene la tos y las sibilancias' "$CATEGORY_TEMP"
grep -Fq 'Todas hacen descender el Qi rebelde' "$CATEGORY_TEMP"
grep -Fq 'temperatura de la tos, tos seca frente a tos con flema' "$CATEGORY_TEMP"
grep -Fq '$aromaticherbs = [' "$CATEGORY_TEMP"
grep -Fq '$aromaticsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Huo Xiang'" "$CATEGORY_TEMP"
grep -Fq "'Cang Zhu'" "$CATEGORY_TEMP"
grep -Fq "'Hou Po'" "$CATEGORY_TEMP"
grep -Fq "'Bai Dou Kou'" "$CATEGORY_TEMP"
grep -Fq "'Sha Ren'" "$CATEGORY_TEMP"
grep -Fq "'Cao Dou Kou'" "$CATEGORY_TEMP"
grep -Fq "'Cao Guo'" "$CATEGORY_TEMP"
grep -Fq '16. Aromáticas que transforman Humedad' "$CATEGORY_TEMP"
grep -Fq 'Transforma Humedad; detiene náuseas y vómitos' "$CATEGORY_TEMP"
grep -Fq 'Todas transforman Humedad excesiva o turbia en el centro' "$CATEGORY_TEMP"
grep -Fq 'intensidad de la Humedad, participación exterior' "$CATEGORY_TEMP"
grep -Fq '$digestiveherbs = [' "$CATEGORY_TEMP"
grep -Fq '$digestivesimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Shan Zha'" "$CATEGORY_TEMP"
grep -Fq "'Mai Ya'" "$CATEGORY_TEMP"
grep -Fq "'Gu Ya'" "$CATEGORY_TEMP"
grep -Fq "'Shen Qu'" "$CATEGORY_TEMP"
grep -Fq "'Ji Nei Jin'" "$CATEGORY_TEMP"
grep -Fq "'Lai Fu Zi'" "$CATEGORY_TEMP"
grep -Fq '17. Digestivas' "$CATEGORY_TEMP"
grep -Fq 'Reduce estancamiento de alimentos, especialmente por carne' "$CATEGORY_TEMP"
grep -Fq 'Las seis reducen retención de alimentos' "$CATEGORY_TEMP"
grep -Fq 'tipo de alimento, grado de retención' "$CATEGORY_TEMP"
grep -Fq '$regulateqiherbs = [' "$CATEGORY_TEMP"
grep -Fq '$regulateqisimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Chen Pi'" "$CATEGORY_TEMP"
grep -Fq "'Qing Pi'" "$CATEGORY_TEMP"
grep -Fq "'Zhi Shi'" "$CATEGORY_TEMP"
grep -Fq "'Zhi Ke'" "$CATEGORY_TEMP"
grep -Fq "'Da Fu Pi'" "$CATEGORY_TEMP"
grep -Fq "'Xiang Fu'" "$CATEGORY_TEMP"
grep -Fq "'Chuan Lian Zi'" "$CATEGORY_TEMP"
grep -Fq "'Wu Yao'" "$CATEGORY_TEMP"
grep -Fq "'Chen Xiang'" "$CATEGORY_TEMP"
grep -Fq "'Mu Xiang'" "$CATEGORY_TEMP"
grep -Fq '18. Regular el Qi' "$CATEGORY_TEMP"
grep -Fq 'Regula el Jiao Medio; alivia plenitud del diafragma' "$CATEGORY_TEMP"
grep -Fq 'Las diez regulan el Qi' "$CATEGORY_TEMP"
grep -Fq 'localización del estancamiento de Qi' "$CATEGORY_TEMP"
grep -Fq '$bleedingherbs = [' "$CATEGORY_TEMP"
grep -Fq '$bleedingsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($regulateqisimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq 'foreach ($bleedingsimilarities as $similarity)' "$CATEGORY_TEMP"
if grep -Eq '\$[A-Za-z_][A-Za-z0-9_]*-[A-Za-z0-9_-]+' "$CATEGORY_TEMP"; then
    echo "ERROR: la página contiene una variable PHP inválida con guion y cortaría el renderizado." >&2
    exit 1
fi
grep -Fq "'Pu Huang'" "$CATEGORY_TEMP"
grep -Fq "'San Qi'" "$CATEGORY_TEMP"
grep -Fq "'Qian Cao Gen'" "$CATEGORY_TEMP"
grep -Fq "'Xian He Cao'" "$CATEGORY_TEMP"
grep -Fq "'Bai Ji'" "$CATEGORY_TEMP"
grep -Fq "'Huai Hua Mi'" "$CATEGORY_TEMP"
grep -Fq "'Di Yu'" "$CATEGORY_TEMP"
grep -Fq "'Ce Bai Ye'" "$CATEGORY_TEMP"
grep -Fq "'Bai Mao Gen'" "$CATEGORY_TEMP"
grep -Fq "'Ai Ye'" "$CATEGORY_TEMP"
grep -Fq '19. Detener el sangrado' "$CATEGORY_TEMP"
grep -Fq 'Astringe la Sangre y detiene el sangrado mientras mueve Sangre' "$CATEGORY_TEMP"
grep -Fq 'Las diez detienen el sangrado' "$CATEGORY_TEMP"
grep -Fq 'mecanismo y localización del sangrado' "$CATEGORY_TEMP"
grep -Fq '$activatebloodherbs = [' "$CATEGORY_TEMP"
grep -Fq '$activatebloodsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($activatebloodsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Chuan Xiong'" "$CATEGORY_TEMP"
grep -Fq "'Yan Hu Suo'" "$CATEGORY_TEMP"
grep -Fq "'Yu Jin'" "$CATEGORY_TEMP"
grep -Fq "'Dan Shen'" "$CATEGORY_TEMP"
grep -Fq "'Chi Shao'" "$CATEGORY_TEMP"
grep -Fq "'Yi Mu Cao'" "$CATEGORY_TEMP"
grep -Fq "'Ze Lan'" "$CATEGORY_TEMP"
grep -Fq "'Tao Ren'" "$CATEGORY_TEMP"
grep -Fq "'Hong Hua'" "$CATEGORY_TEMP"
grep -Fq "'Mo Yao'" "$CATEGORY_TEMP"
grep -Fq "'Ru Xiang'" "$CATEGORY_TEMP"
grep -Fq "'San Leng'" "$CATEGORY_TEMP"
grep -Fq "'E Zhu'" "$CATEGORY_TEMP"
grep -Fq "'Huai Niu Xi'" "$CATEGORY_TEMP"
grep -Fq "'Chuan Niu Xi'" "$CATEGORY_TEMP"
grep -Fq "'Wang Bu Liu Xing'" "$CATEGORY_TEMP"
grep -Fq '20. Activar la Sangre' "$CATEGORY_TEMP"
grep -Fq 'Las dieciséis activan la Sangre' "$CATEGORY_TEMP"
grep -Fq 'localización y causa del dolor' "$CATEGORY_TEMP"
grep -Fq '$warminteriorherbs = [' "$CATEGORY_TEMP"
grep -Fq '$warminteriorsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($warminteriorsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Chuan Wu'" "$CATEGORY_TEMP"
grep -Fq "'Cao Wu'" "$CATEGORY_TEMP"
grep -Fq "'Fu Zi'" "$CATEGORY_TEMP"
grep -Fq "'Gan Jiang'" "$CATEGORY_TEMP"
grep -Fq "'Rou Gui'" "$CATEGORY_TEMP"
grep -Fq "'Hua Jiao'" "$CATEGORY_TEMP"
grep -Fq "'Wu Zhu Yu'" "$CATEGORY_TEMP"
grep -Fq "'Xiao Hui Xiang'" "$CATEGORY_TEMP"
grep -Fq "'Ding Xiang'" "$CATEGORY_TEMP"
grep -Fq "'Gao Liang Jiang'" "$CATEGORY_TEMP"
grep -Fq "'Bi Ba'" "$CATEGORY_TEMP"
grep -Fq "'Hu Jiao'" "$CATEGORY_TEMP"
grep -Fq '21. Calentar el Interior' "$CATEGORY_TEMP"
grep -Fq 'Las doce calientan el Interior' "$CATEGORY_TEMP"
grep -Fq 'localización e intensidad del Frío' "$CATEGORY_TEMP"
grep -Fq '$qitonicherbs = [' "$CATEGORY_TEMP"
grep -Fq '$qitonicsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($qitonicsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Ren Shen'" "$CATEGORY_TEMP"
grep -Fq "'Dang Shen'" "$CATEGORY_TEMP"
grep -Fq "'Huang Qi'" "$CATEGORY_TEMP"
grep -Fq "'Bai Zhu'" "$CATEGORY_TEMP"
grep -Fq "'Bai Bian Dou'" "$CATEGORY_TEMP"
grep -Fq "'Shan Yao'" "$CATEGORY_TEMP"
grep -Fq "'Da Zao'" "$CATEGORY_TEMP"
grep -Fq "'Gan Cao'" "$CATEGORY_TEMP"
grep -Fq "'Yi Tang'" "$CATEGORY_TEMP"
grep -Fq "'Feng Mi'" "$CATEGORY_TEMP"
grep -Fq "'Geng Mi'" "$CATEGORY_TEMP"
grep -Fq '22. Tónicos de Qi' "$CATEGORY_TEMP"
grep -Fq 'Las once apoyan el Qi' "$CATEGORY_TEMP"
grep -Fq 'grado y localización de la deficiencia de Qi' "$CATEGORY_TEMP"
grep -Fq '$bloodtonicherbs = [' "$CATEGORY_TEMP"
grep -Fq '$bloodtonicsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($bloodtonicsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Shu Di Huang'" "$CATEGORY_TEMP"
grep -Fq "'Dang Gui'" "$CATEGORY_TEMP"
grep -Fq "'Bai Shao'" "$CATEGORY_TEMP"
grep -Fq "'E Jiao'" "$CATEGORY_TEMP"
grep -Fq "'Gou Qi Zi'" "$CATEGORY_TEMP"
grep -Fq "'Sang Shen'" "$CATEGORY_TEMP"
grep -Fq "'Long Yan Rou'" "$CATEGORY_TEMP"
grep -Fq '23. Tónicos de Sangre' "$CATEGORY_TEMP"
grep -Fq 'Las siete nutren Sangre' "$CATEGORY_TEMP"
grep -Fq 'deficiencia de Sangre frente a Yin' "$CATEGORY_TEMP"
grep -Fq '$yangtonicherbs = [' "$CATEGORY_TEMP"
grep -Fq '$yangtonicsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($yangtonicsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Lu Rong'" "$CATEGORY_TEMP"
grep -Fq "'Rou Cong Rong'" "$CATEGORY_TEMP"
grep -Fq "'Suo Yang'" "$CATEGORY_TEMP"
grep -Fq "'Hu Tao Ren'" "$CATEGORY_TEMP"
grep -Fq "'Yin Yang Huo'" "$CATEGORY_TEMP"
grep -Fq "'Xian Mao'" "$CATEGORY_TEMP"
grep -Fq "'Ba Ji Tian'" "$CATEGORY_TEMP"
grep -Fq "'Bu Gu Zhi'" "$CATEGORY_TEMP"
grep -Fq "'Yi Zhi Ren'" "$CATEGORY_TEMP"
grep -Fq "'Gou Ji'" "$CATEGORY_TEMP"
grep -Fq "'Gu Sui Bu'" "$CATEGORY_TEMP"
grep -Fq "'Xu Duan'" "$CATEGORY_TEMP"
grep -Fq "'Du Zhong'" "$CATEGORY_TEMP"
grep -Fq "'Tu Si Zi'" "$CATEGORY_TEMP"
grep -Fq '24. Tónicos de Yang' "$CATEGORY_TEMP"
grep -Fq 'Las catorce apoyan Yang de Riñón' "$CATEGORY_TEMP"
grep -Fq 'grado de deficiencia de Yang' "$CATEGORY_TEMP"
grep -Fq '$yintonicherbs = [' "$CATEGORY_TEMP"
grep -Fq '$yintonicsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($yintonicsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Bai He'" "$CATEGORY_TEMP"
grep -Fq "'Tian Men Dong'" "$CATEGORY_TEMP"
grep -Fq "'Mai Men Dong'" "$CATEGORY_TEMP"
grep -Fq "'Sha Shen'" "$CATEGORY_TEMP"
grep -Fq "'Shi Hu'" "$CATEGORY_TEMP"
grep -Fq "'Nu Zhen Zi'" "$CATEGORY_TEMP"
grep -Fq "'Hei Zhi Ma'" "$CATEGORY_TEMP"
grep -Fq "'Gui Ban'" "$CATEGORY_TEMP"
grep -Fq "'Bie Jia'" "$CATEGORY_TEMP"
grep -Fq '25. Tónicos de Yin' "$CATEGORY_TEMP"
grep -Fq 'Las nueve nutren Yin' "$CATEGORY_TEMP"
grep -Fq 'deficiencia de Yin de Pulmón' "$CATEGORY_TEMP"
grep -Fq '$astringentherbs = [' "$CATEGORY_TEMP"
grep -Fq '$astringentsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($astringentsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Shan Zhu Yu'" "$CATEGORY_TEMP"
grep -Fq "'Jin Ying Zi'" "$CATEGORY_TEMP"
grep -Fq "'Hai Piao Xiao'" "$CATEGORY_TEMP"
grep -Fq "'Fu Pen Zi'" "$CATEGORY_TEMP"
grep -Fq "'Qian Shi'" "$CATEGORY_TEMP"
grep -Fq "'Lian Zi'" "$CATEGORY_TEMP"
grep -Fq "'Wu Wei Zi'" "$CATEGORY_TEMP"
grep -Fq "'Bai Guo'" "$CATEGORY_TEMP"
grep -Fq "'Wu Mei'" "$CATEGORY_TEMP"
grep -Fq "'He Zi'" "$CATEGORY_TEMP"
grep -Fq "'Chi Shi Zhi'" "$CATEGORY_TEMP"
grep -Fq "'Rou Dou Kou'" "$CATEGORY_TEMP"
grep -Fq "'Chun Pi'" "$CATEGORY_TEMP"
grep -Fq "'Fu Xiao Mai'" "$CATEGORY_TEMP"
grep -Fq '26. Astringentes' "$CATEGORY_TEMP"
grep -Fq 'Las catorce restringen pérdidas' "$CATEGORY_TEMP"
grep -Fq 'tipo y localización de la pérdida' "$CATEGORY_TEMP"
grep -Fq '$anchorcalmherbs = [' "$CATEGORY_TEMP"
grep -Fq '$anchorcalmsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($anchorcalmsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Long Gu'" "$CATEGORY_TEMP"
grep -Fq "'Mu Li'" "$CATEGORY_TEMP"
grep -Fq "'Ci Shi'" "$CATEGORY_TEMP"
grep -Fq "'Zhen Zhu'" "$CATEGORY_TEMP"
grep -Fq '27. Anclar y calmar el Shen' "$CATEGORY_TEMP"
grep -Fq 'Las cuatro son sustancias pesadas' "$CATEGORY_TEMP"
grep -Fq 'ascenso de Yang de Hígado' "$CATEGORY_TEMP"
grep -Fq '$nourishcalmherbs = [' "$CATEGORY_TEMP"
grep -Fq '$nourishcalmsimilarities = [' "$CATEGORY_TEMP"
grep -Fq 'foreach ($nourishcalmsimilarities as $similarity)' "$CATEGORY_TEMP"
grep -Fq "'Suan Zao Ren'" "$CATEGORY_TEMP"
grep -Fq "'Bai Zi Ren'" "$CATEGORY_TEMP"
grep -Fq "'Yuan Zhi'" "$CATEGORY_TEMP"
grep -Fq "'Ye Jiao Teng'" "$CATEGORY_TEMP"
grep -Fq '28. Nutrir y calmar el Shen' "$CATEGORY_TEMP"
grep -Fq 'Las cuatro calman el Shen' "$CATEGORY_TEMP"
grep -Fq 'deficiencia de Sangre o Yin' "$CATEGORY_TEMP"
grep -Fq '$hotphlegmherbs = [' "$CATEGORY_TEMP"
grep -Fq '$hotphlegmsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Qian Hu'" "$CATEGORY_TEMP"
grep -Fq "'Chuan Bei Mu'" "$CATEGORY_TEMP"
grep -Fq "'Zhe Bei Mu'" "$CATEGORY_TEMP"
grep -Fq "'Gua Lou Pi'" "$CATEGORY_TEMP"
grep -Fq "'Gua Lou'" "$CATEGORY_TEMP"
grep -Fq "'Gua Lou Ren'" "$CATEGORY_TEMP"
grep -Fq "'Zhu Ru'" "$CATEGORY_TEMP"
grep -Fq "'Hai Zao'" "$CATEGORY_TEMP"
grep -Fq '13. Disolver Flema-Calor' "$CATEGORY_TEMP"
grep -Fq 'Hace descender el Qi de Pulmón y expulsa flema' "$CATEGORY_TEMP"
grep -Fq 'Todas enfrían o transforman Flema-Calor' "$CATEGORY_TEMP"
grep -Fq '$winddampherbs = [' "$CATEGORY_TEMP"
grep -Fq '$winddampsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Du Huo'" "$CATEGORY_TEMP"
grep -Fq "'Wei Ling Xian'" "$CATEGORY_TEMP"
grep -Fq "'Hai Tong Pi'" "$CATEGORY_TEMP"
grep -Fq "'Qin Jiao'" "$CATEGORY_TEMP"
grep -Fq "'Mu Gua'" "$CATEGORY_TEMP"
grep -Fq "'Sang Zhi'" "$CATEGORY_TEMP"
grep -Fq "'Xi Xian Cao'" "$CATEGORY_TEMP"
grep -Fq "'Bai Hua She'" "$CATEGORY_TEMP"
grep -Fq "'Wu Jia Pi'" "$CATEGORY_TEMP"
grep -Fq "'Sang Ji Sheng'" "$CATEGORY_TEMP"
grep -Fq '$diureticherbs = [' "$CATEGORY_TEMP"
grep -Fq '$diureticsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Fu Ling'" "$CATEGORY_TEMP"
grep -Fq "'Yi Yi Ren'" "$CATEGORY_TEMP"
grep -Fq "'Zhu Ling'" "$CATEGORY_TEMP"
grep -Fq "'Ze Xie'" "$CATEGORY_TEMP"
grep -Fq "'Dong Gua Ren'" "$CATEGORY_TEMP"
grep -Fq "'Han Fang Ji'" "$CATEGORY_TEMP"
grep -Fq "'Yin Chen'" "$CATEGORY_TEMP"
grep -Fq "'Deng Xin Cao'" "$CATEGORY_TEMP"
grep -Fq "'Mu Tong'" "$CATEGORY_TEMP"
grep -Fq "'Dong Kui Zi'" "$CATEGORY_TEMP"
grep -Fq "'Qu Mai'" "$CATEGORY_TEMP"
grep -Fq "'Bian Xu'" "$CATEGORY_TEMP"
grep -Fq "'Shi Wei'" "$CATEGORY_TEMP"
grep -Fq "'Che Qian Zi'" "$CATEGORY_TEMP"
grep -Fq "'Hua Shi'" "$CATEGORY_TEMP"
grep -Fq "'Di Fu Zi'" "$CATEGORY_TEMP"
grep -Fq "'Bi Xie'" "$CATEGORY_TEMP"
grep -Fq "Wrap in a porous bag before decocting." "$CATEGORY_TEMP"
grep -Fq '$purgativeherbs = [' "$CATEGORY_TEMP"
grep -Fq '$purgativesimilarities = [' "$CATEGORY_TEMP"
grep -Fq '$laxativeherbs = [' "$CATEGORY_TEMP"
grep -Fq '$laxativesimilarities = [' "$CATEGORY_TEMP"
grep -Fq '$catharticherbs = [' "$CATEGORY_TEMP"
grep -Fq '$catharticsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Da Huang'" "$CATEGORY_TEMP"
grep -Fq "'Mang Xiao'" "$CATEGORY_TEMP"
grep -Fq "'Lu Hui'" "$CATEGORY_TEMP"
grep -Fq "'Huo Ma Ren'" "$CATEGORY_TEMP"
grep -Fq "'Da Ji'" "$CATEGORY_TEMP"
grep -Fq "Cooking longer than 10 minutes" "$CATEGORY_TEMP"
grep -Fq "Dissolve into the strained decoction." "$CATEGORY_TEMP"
grep -Fq "Crush before cooking." "$CATEGORY_TEMP"
grep -Fq '$deficientheatherbs = [' "$CATEGORY_TEMP"
grep -Fq '$deficientheatsimilarities = [' "$CATEGORY_TEMP"
grep -Fq "'Qing Hao'" "$CATEGORY_TEMP"
grep -Fq "'Di Gu Pi'" "$CATEGORY_TEMP"
grep -Fq "'Bai Wei'" "$CATEGORY_TEMP"
grep -Fq "Do not subject to prolonged cooking." "$CATEGORY_TEMP"
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
[[ "$similarities_block_count" -eq 32 && "$comparison_table_count" -eq 32 ]] || {
    echo "ERROR: las treinta y dos categorías no contienen ambos bloques comparativos." >&2
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

# Verificación posterior: el archivo servido debe ser idéntico al descargado y contener la localización completa.
cmp -s "$LIBRARY_TEMP" "$LIBRARY_TARGET" || {
    echo "ERROR: study-guide.php instalado no coincide con la revisión descargada." >&2
    exit 1
}
cmp -s "$CATEGORY_TEMP" "$CATEGORY_TARGET" || {
    echo "ERROR: herbs-category-comparison.php instalado no coincide con la revisión descargada." >&2
    exit 1
}
php -l "$CATEGORY_TARGET" >/dev/null
grep -Fq 'function board_herb_category_localize' "$CATEGORY_TARGET"
grep -Fq 'return $translations[$text] ?? $text;' "$CATEGORY_TARGET"
grep -Fq 'Libera intensamente el patrón exterior de exceso' "$CATEGORY_TARGET"
grep -Fq 'Elimina Calor intenso del nivel Qi' "$CATEGORY_TARGET"
grep -Fq '15. Detener la tos y las sibilancias' "$CATEGORY_TARGET"
grep -Fq 'Hace descender el Qi de Pulmón, detiene la tos y las sibilancias' "$CATEGORY_TARGET"
grep -Fq '16. Aromáticas que transforman Humedad' "$CATEGORY_TARGET"
grep -Fq 'Transforma Humedad; detiene náuseas y vómitos' "$CATEGORY_TARGET"
grep -Fq '17. Digestivas' "$CATEGORY_TARGET"
grep -Fq 'Reduce estancamiento de alimentos, especialmente por carne' "$CATEGORY_TARGET"
grep -Fq '18. Regular el Qi' "$CATEGORY_TARGET"
grep -Fq 'Regula el Jiao Medio; alivia plenitud del diafragma' "$CATEGORY_TARGET"
grep -Fq '19. Detener el sangrado' "$CATEGORY_TARGET"
grep -Fq 'foreach ($regulateqisimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'foreach ($bleedingsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Astringe la Sangre y detiene el sangrado mientras mueve Sangre' "$CATEGORY_TARGET"
grep -Fq '20. Activar la Sangre' "$CATEGORY_TARGET"
grep -Fq 'foreach ($activatebloodsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Vigoriza la Sangre y el Qi; expulsa Viento' "$CATEGORY_TARGET"
grep -Fq '21. Calentar el Interior' "$CATEGORY_TARGET"
grep -Fq 'foreach ($warminteriorsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Expulsa Viento-Frío-Humedad y alivia obstrucción dolorosa' "$CATEGORY_TARGET"
grep -Fq '22. Tónicos de Qi' "$CATEGORY_TARGET"
grep -Fq 'foreach ($qitonicsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Tonifica intensamente el Yuan Qi' "$CATEGORY_TARGET"
grep -Fq '23. Tónicos de Sangre' "$CATEGORY_TARGET"
grep -Fq 'foreach ($bloodtonicsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Nutre Sangre y Yin, tonifica Esencia y Médula' "$CATEGORY_TARGET"
grep -Fq '24. Tónicos de Yang' "$CATEGORY_TARGET"
grep -Fq 'foreach ($yangtonicsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Tonifica intensamente Yang de Riñón, Esencia y Sangre' "$CATEGORY_TARGET"
grep -Fq '25. Tónicos de Yin' "$CATEGORY_TARGET"
grep -Fq 'foreach ($yintonicsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Nutre Yin de Pulmón, humedece sequedad' "$CATEGORY_TARGET"
grep -Fq '26. Astringentes' "$CATEGORY_TARGET"
grep -Fq 'foreach ($astringentsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Estabiliza Riñón e Hígado, contiene Esencia y orina' "$CATEGORY_TARGET"
grep -Fq '27. Anclar y calmar el Shen' "$CATEGORY_TARGET"
grep -Fq 'foreach ($anchorcalmsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Asienta y calma el Shen, ancla Yang de Hígado' "$CATEGORY_TARGET"
grep -Fq '28. Nutrir y calmar el Shen' "$CATEGORY_TARGET"
grep -Fq 'foreach ($nourishcalmsimilarities as $similarity)' "$CATEGORY_TARGET"
grep -Fq 'Nutre Sangre de Hígado, calma el Shen' "$CATEGORY_TARGET"
grep -Fq 'Idioma: Español' "$CATEGORY_TARGET"
grep -Fq "optional_param('ui', '', PARAM_ALPHA)" "$CATEGORY_TARGET"
grep -Fq "'ui' => 'es'" "$LIBRARY_TARGET"
installed_herb_field_count="$(grep -Fo 'board_herb_category_localize($herb[' "$CATEGORY_TARGET" | wc -l)"
installed_similarity_field_count="$(grep -Fo 'board_herb_category_localize($similarity[' "$CATEGORY_TARGET" | wc -l)"
[[ "$installed_herb_field_count" -eq 224 && "$installed_similarity_field_count" -eq 96 ]] || {
    echo "ERROR: el archivo instalado no contiene la localización completa en Español." >&2
    echo "Conteos instalados: hierbas=$installed_herb_field_count semejanzas=$installed_similarity_field_count" >&2
    exit 1
}
category_sha256="$(sha256sum "$CATEGORY_TARGET")"
category_sha256="${category_sha256%% *}"

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
echo "OK: Herbs Category Comparison conserva un botón visible para cambiar entre Español y English."
echo "OK: la vista Español localiza índice, títulos, tablas, semejanzas, funciones, precauciones y preparación."
echo "OK: las validaciones cuentan ocurrencias reales: 224 campos de hierbas y 96 campos de semejanzas."
echo "OK: los treinta y dos botones abren una sola categoría y conservan el idioma seleccionado."
echo "OK: cada categoría tiene controles Anterior y Siguiente en la esquina superior derecha."
echo "OK: los controles conservan el idioma y no avanzan fuera de las categorías 1 a 32."
echo "OK: la barra busca todas las coincidencias en categorías, hierbas, nombres farmacéuticos, funciones y precauciones."
echo "OK: Anterior, Siguiente, Enter y Mayús+Enter recorren coincidencias entre las treinta y dos categorías."
echo "OK: la categoría de cada resultado se abre automáticamente y la coincidencia activa recibe resaltado y sombra amarillos."
echo "OK: la búsqueda ignora acentos e informa posición, total de coincidencias y número de categorías."
echo "OK: Borrar y Escape eliminan resaltados y restauran los treinta y dos botones de categorías."
echo "OK: las categorías 1, 2 y 3 usan traducciones exactas por frase; strtr fue eliminado para evitar Spanglish."
echo "OK: el idioma usa lang y ui, consulta el idioma de Moodle, evita caché y muestra su estado visible."
echo "OK: archivo instalado verificado byte por byte: $CATEGORY_TARGET"
echo "OK: SHA-256 instalado: $category_sha256"
echo "OK: Herb y Pharmaceutical name están combinadas; las columnas compactas dejan visible Preparation."
echo "OK: Dosage permite varias líneas y Preparation usa una columna más ancha."
echo "OK: Similarities and Clinical Overlaps aparece encima de la tabla detallada."
echo "OK: Disperse Wind-Heat quedó como categoría 2 con once hierbas y sus semejanzas clínicas."
echo "OK: Clear and Purge Heat quedó como categoría 3 con ocho hierbas y sus semejanzas clínicas."
echo "OK: Clear Blood-Heat quedó como categoría 4 con cinco hierbas y sus semejanzas clínicas."
echo "OK: Clear Damp-Heat quedó como categoría 5 con cinco hierbas y sus semejanzas clínicas."
echo "OK: Clear Toxic-Heat quedó como categoría 6 con nueve hierbas y sus semejanzas clínicas."
echo "OK: Clear Deficient-Heat quedó como categoría 7 con tres hierbas y sus semejanzas clínicas."
echo "OK: Purgatives quedó como categoría 8 con tres hierbas y sus semejanzas clínicas."
echo "OK: Laxatives quedó como categoría 9 con Huo Ma Ren y su comparación clínica."
echo "OK: Cathartics quedó como categoría 10 con Da Ji y su comparación clínica."
echo "OK: Diuretics quedó como categoría 11 con diecisiete hierbas y sus semejanzas clínicas."
echo "OK: Dispel Wind-Damp quedó como categoría 12 con diez hierbas y sus semejanzas clínicas."
echo "OK: Dissolve Hot Phlegm quedó como categoría 13 con ocho hierbas y traducción exacta en Español."
echo "OK: Dissolve Cold Phlegm quedó como categoría 14 con seis hierbas y traducción exacta en Español."
echo "OK: Stop Cough and Wheezing quedó como categoría 15 con siete hierbas y traducción exacta en Español."
echo "OK: Aromatics for Damp quedó como categoría 16 con siete hierbas y traducción exacta en Español."
echo "OK: Digestives quedó como categoría 17 con seis hierbas y traducción exacta en Español."
echo "OK: Regulate Qi quedó como categoría 18 con diez hierbas y traducción exacta en Español."
echo "OK: Stop Bleeding quedó como categoría 19 con diez hierbas y traducción exacta en Español."
echo "OK: Activate Blood quedó como categoría 20 con dieciséis hierbas y traducción exacta en Español."
echo "OK: Warm Interior quedó como categoría 21 con doce hierbas y traducción exacta en Español."
echo "OK: Qi Tonics quedó como categoría 22 con once sustancias y traducción exacta en Español."
echo "OK: Blood Tonics quedó como categoría 23 con siete hierbas y traducción exacta en Español."
echo "OK: Yang Tonics quedó como categoría 24 con catorce hierbas y traducción exacta en Español."
echo "OK: Yin Tonics quedó como categoría 25 con nueve sustancias y traducción exacta en Español."
echo "OK: Astringents quedó como categoría 26 con catorce sustancias y traducción exacta en Español."
echo "OK: Anchor & Calm Shen quedó como categoría 27 con cuatro sustancias y traducción exacta en Español."
echo "OK: Nourish & Calm Shen quedó como categoría 28 con cuatro hierbas y traducción exacta en Español."
echo "OK: Orifice Opening Aromatics quedó como categoría 29 con Niu Huang y traducción exacta en Español."
echo "OK: Extinguish Liver Wind quedó como categoría 30 con seis sustancias y traducción exacta en Español."
echo "OK: Expel Parasites quedó como categoría 31 con Bing Lang y traducción exacta en Español."
echo "OK: External Use quedó como categoría 32 con She Chuang Zi y traducción exacta en Español."
echo "OK: las categorías 18 y 19 renderizan sus semejanzas sin variables PHP con guiones."
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
