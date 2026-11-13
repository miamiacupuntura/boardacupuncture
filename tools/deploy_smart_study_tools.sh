#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Smart Study Tools detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_smart_study_tools.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id dirname find; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/moodle" "$TEMP_DIR/data" "$TEMP_DIR/static"

MOODLE_FILES=(smart-study-tools.php study-score.php study-guide.php safety-library.php formula-list.php single-herbs-list.php herbs-category-comparison.php herbology-library-navigation.php formula-tongue-pulse.php formula-ingredients.php formula-actions.php herb-formula-category-map.php)
DATA_FILES=(single-herb-safety-supplement.json formula-architecture-data.json dui-yao-smart-data.json)
STATIC_FILES=(formula-practice.js formula-practice.html formula-practice-es.html formula-practice-data.json formula-practice-translations-es.json dui-yao-data.json formula-modifications-manifest.json formula-modifications-supplement.json formula-modifications-gui-zhi-tang.json formula-modifications-ge-gen-tang.json formula-modifications-generated.json formula-category-expansion.json herbal-incompatibilities.html herbal-incompatibilities-es.html incompatibilities-seven-relationships.html incompatibilities-seven-relationships-es.html incompatibilities-xu-vs-shi.html incompatibilities-xu-vs-shi-es.html incompatibilities-wei-vs-sha.html incompatibilities-wei-vs-sha-es.html incompatibilities-wu-vs-fan.html incompatibilities-wu-vs-fan-es.html incompatibilities-shi-ba-fan-aconite.html incompatibilities-shi-ba-fan-aconite-es.html incompatibilities-shi-ba-fan-gancao-lilu.html incompatibilities-shi-ba-fan-gancao-lilu-es.html incompatibilities-shi-jiu-wei.html incompatibilities-shi-jiu-wei-es.html incompatibilities-extended-counteraction.html incompatibilities-extended-counteraction-es.html incompatibilities-extended-antagonism.html incompatibilities-extended-antagonism-es.html incompatibilities-dietary.html incompatibilities-dietary-es.html incompatibilities-medicinal-origins.html incompatibilities-medicinal-origins-es.html guideline-herbs-by-origin.html guideline-herbs-by-origin-es.html formula-category-map.html formula-category-map-es.html formula-navigator.html formula-navigator-es.html formula-category-tree-map.html formula-category-tree-map-es.html safety-preparation.html safety-preparation-es.html cautions-contraindications.html cautions-contraindications-es.html safety.html safety-es.html advanced-name-traps.html families.html formula-name-traps.html formula-theory.html herb-families.html misleading-formula-names.html most-important-formulas.html name-traps.html patterns-tips-maciocia.html secondary-actions.html supplemental-herb-families.html advanced-name-traps-es.html families-es.html formula-name-traps-es.html formula-theory-es.html herb-families-es.html misleading-formula-names-es.html most-important-formulas-es.html name-traps-es.html patterns-tips-maciocia-es.html secondary-actions-es.html supplemental-herb-families-es.html index.html index-es.html safety-toxicity.html safety-interactions.html safety-regulation.html safety-toxic-herbs.html safety-pregnancy.html safety-anticoagulants.html safety-ma-huang-hypertension.html safety-fu-zi-cardiac-toxicity.html safety-clinical-interaction-cases.html safety-obsolete-prohibited-substances.html safety-herb-specific-cautions.html safety-toxicity-es.html safety-interactions-es.html safety-regulation-es.html safety-toxic-herbs-es.html safety-pregnancy-es.html safety-anticoagulants-es.html safety-ma-huang-hypertension-es.html safety-fu-zi-cardiac-toxicity-es.html safety-clinical-interaction-cases-es.html safety-obsolete-prohibited-substances-es.html safety-herb-specific-cautions-es.html continuous-reader.html continuous-reader.js safety-library.html safety-library-es.html comparisons.js study-return-navigation.js chief-herbs-property.html chief-herbs-property-es.html formulas.html formulas-es.html pathologies.html pathologies-es.html patterns.html patterns-es.html maciocia-formula-tips.html maciocia-formula-tips-es.html dui-yao.html dui-yao-es.html herb-comparison-bank.html herb-comparison-bank-es.html dui-yao-practice.html dui-yao-practice-es.html miscellaneous-practice.html miscellaneous-practice-es.html)

for file in "${MOODLE_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/moodle/$file"
    php -l "$TEMP_DIR/moodle/$file" >/dev/null
done
for file in "${DATA_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/data/$file"
    php -r '$data=json_decode(file_get_contents($argv[1]), true); if (!is_array($data)) { exit(1); }' "$TEMP_DIR/data/$file"
done
for file in "${STATIC_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/static/$file"
done

SAFETY_SECTION_FILES_EN=(safety-toxicity.html safety-interactions.html safety-regulation.html safety-toxic-herbs.html safety-pregnancy.html safety-anticoagulants.html safety-ma-huang-hypertension.html safety-fu-zi-cardiac-toxicity.html safety-clinical-interaction-cases.html safety-obsolete-prohibited-substances.html safety-herb-specific-cautions.html)
SAFETY_SECTION_FILES_ES=(safety-toxicity-es.html safety-interactions-es.html safety-regulation-es.html safety-toxic-herbs-es.html safety-pregnancy-es.html safety-anticoagulants-es.html safety-ma-huang-hypertension-es.html safety-fu-zi-cardiac-toxicity-es.html safety-clinical-interaction-cases-es.html safety-obsolete-prohibited-substances-es.html safety-herb-specific-cautions-es.html)

for file in "${SAFETY_SECTION_FILES_EN[@]}"; do
    php -r '$html=file_get_contents($argv[1]); $url="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library"; if (substr_count($html, $url) !== 2 || strpos($html, "href=\"safety.html\"") !== false) { exit(1); }' "$TEMP_DIR/static/$file"
    grep -Fq '<span>Study Guide</span>' "$TEMP_DIR/static/$file"
    grep -Fq '← Study Guide' "$TEMP_DIR/static/$file"
done
for file in "${SAFETY_SECTION_FILES_ES[@]}"; do
    php -r '$html=file_get_contents($argv[1]); $url="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es"; if (substr_count($html, $url) !== 2 || strpos($html, "href=\"safety-es.html\"") !== false) { exit(1); }' "$TEMP_DIR/static/$file"
    grep -Fq '<span>Guía de estudio</span>' "$TEMP_DIR/static/$file"
    grep -Fq '← Guía de estudio' "$TEMP_DIR/static/$file"
done
grep -Fq 'id="reader-dashboard" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library">← Study Guide</a>' "$TEMP_DIR/static/continuous-reader.html"
grep -Fq "const exitUrl = group === 'safety' ? studyGuideUrl : indexUrl;" "$TEMP_DIR/static/continuous-reader.js"
grep -Fq "ui.dashboard.href = studyGuideUrl;" "$TEMP_DIR/static/continuous-reader.js"
grep -Fq "ui.index.href = ui.bottomIndex.href = exitUrl;" "$TEMP_DIR/static/continuous-reader.js"
grep -Fq "window.location.href = exitUrl;" "$TEMP_DIR/static/continuous-reader.js"

RETURN_LINK_FILES_EN=(advanced-name-traps.html families.html formula-name-traps.html formula-theory.html herb-families.html herbal-incompatibilities.html misleading-formula-names.html most-important-formulas.html name-traps.html patterns-tips-maciocia.html secondary-actions.html supplemental-herb-families.html cautions-contraindications.html index.html safety-preparation.html)
RETURN_LINK_FILES_ES=(advanced-name-traps-es.html families-es.html formula-name-traps-es.html formula-theory-es.html herb-families-es.html herbal-incompatibilities-es.html misleading-formula-names-es.html most-important-formulas-es.html name-traps-es.html patterns-tips-maciocia-es.html secondary-actions-es.html supplemental-herb-families-es.html cautions-contraindications-es.html index-es.html safety-preparation-es.html)

for file in "${RETURN_LINK_FILES_EN[@]}"; do
    grep -Fq '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library' "$TEMP_DIR/static/$file"
done
for file in "${RETURN_LINK_FILES_ES[@]}"; do
    grep -Fq '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es' "$TEMP_DIR/static/$file"
done
if grep -Fq 'href="/my/">← Dashboard' "$TEMP_DIR"/static/*.html \
    || grep -Fq 'href="/my/">← Back to Dashboard' "$TEMP_DIR"/static/*.html \
    || grep -Fq 'href="safety.html">Safety Library' "$TEMP_DIR"/static/*.html \
    || grep -Fq 'href="safety.html">← Safety Library' "$TEMP_DIR"/static/*.html \
    || grep -Fq 'href="safety-es.html">Biblioteca de Seguridad' "$TEMP_DIR"/static/*.html \
    || grep -Fq 'href="safety-es.html">← Biblioteca de Seguridad' "$TEMP_DIR"/static/*.html; then
    echo "ERROR: queda un control Dashboard/Library con un destino anterior dentro de Study Guide." >&2
    exit 1
fi
for file in formula-list.php single-herbs-list.php; do
    grep -Fq "new moodle_url('/blocks/myprogress/study-guide.php'" "$TEMP_DIR/moodle/$file"
    grep -Fq "'area' => 'herbology', 'view' => 'library'" "$TEMP_DIR/moodle/$file"
    grep -Fq "'Guía de estudio' : 'Study Guide'" "$TEMP_DIR/moodle/$file"
    if grep -Fq "\$dashboardurl = new moodle_url('/my/');" "$TEMP_DIR/moodle/$file"; then
        echo "ERROR: $file todavía dirige Dashboard a /my/." >&2
        exit 1
    fi
done

for file in "$TEMP_DIR"/static/herbal-incompatibilities*.html "$TEMP_DIR"/static/incompatibilities-*.html; do
    php -r '$html=file_get_contents($argv[1]); if (preg_match("/\\p{Han}/u", $html)) { fwrite(STDERR, "ERROR: quedan caracteres chinos en $argv[1]\\n"); exit(1); }' "$file"
done
grep -Fq 'guideline-herb-index' "$TEMP_DIR/static/incompatibilities-seven-relationships.html"
grep -Fq 'Official guideline herbs &amp; pharmaceutical names' "$TEMP_DIR/static/incompatibilities-shi-ba-fan-aconite.html"
grep -Fq '<details class="guideline-herb-index" open>' "$TEMP_DIR/static/incompatibilities-shi-ba-fan-aconite.html"
grep -Fq 'Pharmaceutical name:' "$TEMP_DIR/static/incompatibilities-shi-ba-fan-aconite.html"
grep -Fq 'Nombre farmacéutico:' "$TEMP_DIR/static/incompatibilities-shi-ba-fan-aconite-es.html"
grep -Fq 'Pharmaceutical name:' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'Nombre farmacéutico:' "$TEMP_DIR/static/incompatibilities-medicinal-origins-es.html"
grep -Fq 'What this section is about' "$TEMP_DIR/static/incompatibilities-seven-relationships.html"
grep -Fq 'De qué trata esta sección' "$TEMP_DIR/static/incompatibilities-seven-relationships-es.html"
grep -Fq 'Pharmaceutical index' "$TEMP_DIR/static/incompatibilities-shi-ba-fan-aconite.html"
grep -Fq 'Índice farmacéutico' "$TEMP_DIR/static/incompatibilities-shi-ba-fan-aconite-es.html"
grep -Fq 'This is not an incompatibility rule' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'Esto no es una regla de incompatibilidad' "$TEMP_DIR/static/incompatibilities-medicinal-origins-es.html"
grep -Fq 'Reference appendix' "$TEMP_DIR/static/herbal-incompatibilities.html"
grep -Fq 'Guideline Medicinals by Origin' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '/study-guide/comparisons/guideline-herbs-by-origin.html' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '$fromoriginmap' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq 'Back to medicinals by origin' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq 'Animal · 24' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
grep -Fq 'Mineral · 8' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
grep -Fq 'Plant &amp; fungal · 272' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
grep -Fq 'data-source-tab="category"' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
grep -Fq 'By Bensky category · 33' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
grep -Fq 'Por categoría Bensky · 33' "$TEMP_DIR/static/guideline-herbs-by-origin-es.html"
grep -Fq 'returnorigin=category' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
grep -Fq 'datalist id="source-search-options"' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
grep -Fq 'data-source-select' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
grep -Fq 'source-mobile-view' "$TEMP_DIR/static/guideline-herbs-by-origin-es.html"
grep -Fq 'Formula Category Map' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq 'data-main-guide-group="single-herbs"' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq 'data-main-guide-group="formulas"' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq 'data-main-guide-group="dui-yao"' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq 'data-main-guide-group="safety"' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '12 sections · includes incompatibilities' "$TEMP_DIR/moodle/study-guide.php"
if grep -Fq '$herbalincompatibilitiesurl' "$TEMP_DIR/moodle/study-guide.php"; then
  echo "ERROR: Herbal Incompatibilities todavía aparece como opción separada en Main Study Guide." >&2
  exit 1
fi
DUPLICATE_BOARD_FILES_EN=(dui-yao.html herb-comparison-bank.html formula-practice.html dui-yao-practice.html miscellaneous-practice.html herbal-incompatibilities.html formula-theory.html most-important-formulas.html)
DUPLICATE_BOARD_FILES_ES=(dui-yao-es.html herb-comparison-bank-es.html formula-practice-es.html dui-yao-practice-es.html miscellaneous-practice-es.html herbal-incompatibilities-es.html formula-theory-es.html most-important-formulas-es.html)
for file in "${DUPLICATE_BOARD_FILES_EN[@]}"; do
    if grep -Fq 'Board Level Activities' "$TEMP_DIR/static/$file"; then
        echo "ERROR: $file todavía muestra el botón duplicado Board Level Activities." >&2
        exit 1
    fi
done
for file in "${DUPLICATE_BOARD_FILES_ES[@]}"; do
    if grep -Fq 'Actividades Board Level' "$TEMP_DIR/static/$file"; then
        echo "ERROR: $file todavía muestra el botón duplicado Actividades Board Level." >&2
        exit 1
    fi
done

COMPARISON_FILES_EN=(index.html formulas.html formula-navigator.html pathologies.html patterns.html patterns-tips-maciocia.html maciocia-formula-tips.html)
COMPARISON_FILES_ES=(index-es.html formulas-es.html formula-navigator-es.html pathologies-es.html patterns-es.html patterns-tips-maciocia-es.html maciocia-formula-tips-es.html)
for file in "${COMPARISON_FILES_EN[@]}"; do
    grep -Fq '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library' "$TEMP_DIR/static/$file"
    grep -Fq 'Study Guide' "$TEMP_DIR/static/$file"
    if grep -Eq 'Board Level Activities|href="/my/"' "$TEMP_DIR/static/$file"; then
        echo "ERROR: $file conserva un regreso anterior fuera de Study Guide." >&2
        exit 1
    fi
done
for file in "${COMPARISON_FILES_ES[@]}"; do
    grep -Fq '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es' "$TEMP_DIR/static/$file"
    grep -Fq 'Guía de estudio' "$TEMP_DIR/static/$file"
    if grep -Eq 'Actividades Board Level|href="/my/"' "$TEMP_DIR/static/$file"; then
        echo "ERROR: $file conserva un regreso anterior fuera de la Guía de estudio." >&2
        exit 1
    fi
done
grep -Fq "'area' => 'herbology'," "$TEMP_DIR/moodle/herbs-category-comparison.php"
grep -Fq "'view' => 'library'," "$TEMP_DIR/moodle/herbs-category-comparison.php"
grep -Fq "'← Guía de estudio' : '← Study Guide'" "$TEMP_DIR/moodle/herbs-category-comparison.php"
grep -Fq "['area' => 'herbology', 'view' => 'library']" "$TEMP_DIR/moodle/herb-formula-category-map.php"

SECONDARY_GUIDE_FILES_EN=(guideline-herbs-by-origin.html families.html name-traps.html herb-families.html formula-name-traps.html misleading-formula-names.html advanced-name-traps.html supplemental-herb-families.html secondary-actions.html chief-herbs-property.html)
SECONDARY_GUIDE_FILES_ES=(guideline-herbs-by-origin-es.html families-es.html name-traps-es.html herb-families-es.html formula-name-traps-es.html misleading-formula-names-es.html advanced-name-traps-es.html supplemental-herb-families-es.html secondary-actions-es.html chief-herbs-property-es.html)
for file in "${SECONDARY_GUIDE_FILES_EN[@]}"; do
    grep -Fq '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library' "$TEMP_DIR/static/$file"
    grep -Fq '← Study Guide' "$TEMP_DIR/static/$file"
    if grep -Eq '← Board Level Activities|← Secondary Study Guide|href="/my/"' "$TEMP_DIR/static/$file"; then
        echo "ERROR: $file conserva un regreso anterior fuera de Study Guide." >&2
        exit 1
    fi
done
for file in "${SECONDARY_GUIDE_FILES_ES[@]}"; do
    grep -Fq '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es' "$TEMP_DIR/static/$file"
    grep -Fq '← Guía de estudio' "$TEMP_DIR/static/$file"
    if grep -Eq '← Actividades Board Level|← Secondary Study Guide|href="/my/"' "$TEMP_DIR/static/$file"; then
        echo "ERROR: $file conserva un regreso anterior fuera de la Guía de estudio." >&2
        exit 1
    fi
done
for file in formula-tongue-pulse.php formula-ingredients.php formula-actions.php; do
    grep -Fq "'area' => 'herbology', 'view' => 'library'" "$TEMP_DIR/moodle/$file"
    grep -Fq "'← Guía de estudio' : '← Study Guide'" "$TEMP_DIR/moodle/$file"
done
grep -Fq "'← Guía de estudio' : '← Study Guide'" "$TEMP_DIR/moodle/herbology-library-navigation.php"
grep -Fq "const studyGuideUrl = spanish" "$TEMP_DIR/static/study-return-navigation.js"
grep -Fq "view=library&lang=es" "$TEMP_DIR/static/study-return-navigation.js"
grep -Fq "studyGuideLink.textContent = spanish ? '← Guía de estudio' : '← Study Guide';" "$TEMP_DIR/static/study-return-navigation.js"
if grep -Fq "const dashboardUrl = '/my/'" "$TEMP_DIR/static/study-return-navigation.js" \
    || grep -Fq 'dashboardLink.href = dashboardUrl' "$TEMP_DIR/static/study-return-navigation.js"; then
    echo "ERROR: study-return-navigation.js todavía crea un regreso al Dashboard." >&2
    exit 1
fi
grep -Fq "study-return-navigation.js?v=20261113-3" "$TEMP_DIR/static/comparisons.js"
grep -Fq "comparisons.js?v=20261113-3" "$TEMP_DIR/moodle/safety-library.php"
grep -Fq "'/blocks/myprogress/safety-library.php'" "$TEMP_DIR/moodle/study-guide.php"
grep -Fq "header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')" "$TEMP_DIR/moodle/safety-library.php"
grep -Fq "\$returnurl = '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library';" "$TEMP_DIR/moodle/safety-library.php"
grep -Fq "'← Guía de estudio' : '← Study Guide'" "$TEMP_DIR/moodle/safety-library.php"
if grep -Fq "'/study-guide/comparisons/safety-library" "$TEMP_DIR/moodle/study-guide.php"; then
    echo "ERROR: Study Guide todavía abre la copia HTML estática de Safety Library." >&2
    exit 1
fi
grep -Fq '<a class="comparison-brand" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library">' "$TEMP_DIR/static/safety-library.html"
grep -Fq '<a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library">← Study Guide</a>' "$TEMP_DIR/static/safety-library.html"
grep -Fq '<a class="comparison-brand" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es">' "$TEMP_DIR/static/safety-library-es.html"
grep -Fq '<a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es">← Guía de estudio</a>' "$TEMP_DIR/static/safety-library-es.html"
if grep -Fq 'href="/my/"' "$TEMP_DIR/static/safety.html" "$TEMP_DIR/static/safety-es.html" "$TEMP_DIR/static/safety-library.html" "$TEMP_DIR/static/safety-library-es.html"; then
    echo "ERROR: queda una salida de Safety Library hacia /my/." >&2
    exit 1
fi
grep -Fq 'http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"' "$TEMP_DIR/static/safety.html"
grep -Fq 'continuous-reader.html?group=safety&amp;lang=en&amp;v=20261113-6&amp;chapter=2' "$TEMP_DIR/static/safety.html"
grep -Fq 'continuous-reader.html?group=safety&amp;lang=es&amp;v=20261113-6&amp;chapter=2' "$TEMP_DIR/static/safety-es.html"
grep -Fq 'continuous-reader.js?v=20261113-6' "$TEMP_DIR/static/continuous-reader.html"
grep -Fq "const readerVersion = '20261113-6';" "$TEMP_DIR/static/continuous-reader.js"
grep -Fq 'const indexFetchUrl = `${indexUrl}?v=${readerVersion}`;' "$TEMP_DIR/static/continuous-reader.js"
grep -Fq '<a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library">← Study Guide</a>' "$TEMP_DIR/static/safety.html"
grep -Fq '<a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es">← Guía de estudio</a>' "$TEMP_DIR/static/safety-es.html"
if grep -Eiq 'Back to Dashboard|Board Level Activities|Actividades Board Level' "$TEMP_DIR/static/safety.html" "$TEMP_DIR/static/safety-es.html"; then
    echo "ERROR: Safety Library todavía muestra un regreso al Dashboard o al menú anterior." >&2
    exit 1
fi
grep -Fq 'data-sequence="13"' "$TEMP_DIR/static/safety.html"
grep -Fq 'href="herbal-incompatibilities.html?from=safety"' "$TEMP_DIR/static/safety.html"
grep -Fq 'data-sequence="13"' "$TEMP_DIR/static/safety-es.html"
if grep -Fq 'safety-preparation' "$TEMP_DIR/static/safety.html" "$TEMP_DIR/static/safety-es.html"; then
  echo "ERROR: Preparation & Decoction todavía aparece dentro de Safety Library." >&2
  exit 1
fi
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "<article class=\"comparison-card\"") !== 12 || strpos($html, "data-sequence=\"1\"") !== false) { exit(1); }' "$TEMP_DIR/static/safety.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "<article class=\"comparison-card\"") !== 12 || strpos($html, "data-sequence=\"1\"") !== false) { exit(1); }' "$TEMP_DIR/static/safety-es.html"
grep -Fq 'href="herbal-incompatibilities-es.html?from=safety"' "$TEMP_DIR/static/safety-es.html"
grep -Fq '<a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library">← Study Guide</a>' "$TEMP_DIR/static/herbal-incompatibilities.html"
grep -Fq '<a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es">← Guía de estudio</a>' "$TEMP_DIR/static/herbal-incompatibilities-es.html"
grep -Fq "<?php echo s(\$ises ? 'Guía de estudio' : 'Study Guide'); ?>" "$TEMP_DIR/moodle/study-guide.php"
if grep -Fq 'Choose a Board Level activity' "$TEMP_DIR/moodle/study-guide.php" || grep -Fq 'Elige una actividad Board Level' "$TEMP_DIR/moodle/study-guide.php"; then
    echo "ERROR: permanece el título anterior del mapa principal." >&2
    exit 1
fi
grep -Fq 'subject-study-library__option-group' "$TEMP_DIR/moodle/study-guide.php"
php -r '$html=file_get_contents($argv[1]); $groups=["single-herbs","formulas","dui-yao","safety"]; $previous=-1; foreach ($groups as $group) { $position=strpos($html, "data-main-guide-group=\"".$group."\""); if ($position === false || $position <= $previous) { exit(1); } $previous=$position; }' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '/study-guide/comparisons/formula-category-map.html' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '$fromformulamap' "$TEMP_DIR/moodle/formula-list.php"
grep -Fq 'Back to Formula Category Map' "$TEMP_DIR/moodle/formula-list.php"
grep -Fq 'Official guideline only · 181' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'Full source map · +145 non-guideline' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'Original linked map · 63 profiles' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'Visual tree map · Maps 1–5' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'data-tree-return' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'Back to Visual Tree Map' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'href="formula-category-tree-map.html?page=1&amp;mode=all"' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq '<div class="map-mode"><a class="map-tree-link"' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'var extra="Additional official formula in this category";if(a===extra)return 1' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'href="formula-navigator.html"' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq '← Category Map' "$TEMP_DIR/static/formula-navigator.html"
grep -Fq 'href="formula-category-map.html"' "$TEMP_DIR/static/formula-navigator.html"
grep -Fq 'data-map-mode-help' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'source-only formulas' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'Solo guideline oficial · 181' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'Mapa fuente completo · +145 no oficiales' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'Mapa original con enlaces · 63 perfiles' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'Mapa visual en árbol · Mapas 1–5' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'data-tree-return' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'Volver al mapa visual' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'href="formula-category-tree-map-es.html?page=1&amp;mode=all"' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq '<div class="map-mode"><a class="map-tree-link"' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'var extra="Fórmula oficial adicional de esta categoría";if(a===extra)return 1' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'href="formula-navigator-es.html"' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq '← Mapa de categorías' "$TEMP_DIR/static/formula-navigator-es.html"
grep -Fq 'href="formula-category-map-es.html"' "$TEMP_DIR/static/formula-navigator-es.html"
grep -Fq 'data-map-mode-help' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'fórmulas solo de la fuente' "$TEMP_DIR/static/formula-category-map-es.html"
if grep -Fq 'Include source references · 326' "$TEMP_DIR/static/formula-category-map.html"; then
  echo "ERROR: el modo fuente conserva la etiqueta ambigua anterior." >&2
  exit 1
fi
grep -Fq '18 supplied branches + additional section' "$TEMP_DIR/static/formula-category-map.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "\"o\":true") !== 181 || substr_count($html, "\"o\":false") !== 145 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/formula-category-map.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "\"o\":true") !== 181 || substr_count($html, "\"o\":false") !== 145 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq 'data-tree-svg' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'function mapHref(' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'function herbHref(' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'words.chiefHeader' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'Chief pending' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'Chief cross-check complete: all 326 formulas' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'min-width:1650px' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq '0 0 1880' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'chiefX=1370' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'count*36' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'function wrapTwoLines' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'add(groupText,"tspan"' "$TEMP_DIR/static/formula-category-tree-map.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "\"ch\":[") !== 326) { exit(1); }' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'var rootLink=' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'var catName=' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'var groupParent=' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'params.set("from","formulatree")' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'data-tree-download' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'from=formulatree' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq '"1":["1","2"]' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq '"5":["14","15","16","17","18"]' "$TEMP_DIR/static/formula-category-tree-map.html"
grep -Fq 'data-tree-svg' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'function mapHref(' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'function herbHref(' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'Chief pendiente' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'Cruce de Chief completo: las 326 fórmulas' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'min-width:1650px' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq '0 0 1880' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'chiefX=1370' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'count*36' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'function wrapTwoLines' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'add(groupText,"tspan"' "$TEMP_DIR/static/formula-category-tree-map-es.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "\"ch\":[") !== 326) { exit(1); }' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'params.set("from","formulatree")' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'Mapa visual de categorías de fórmulas' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq 'from=formulatree' "$TEMP_DIR/static/formula-category-tree-map-es.html"
php -r '$html=file_get_contents($argv[1]); if (!preg_match("~<script type=\"application/json\" id=\"formula-tree-data\">(.*?)</script>~s", $html, $match)) { exit(1); } $data=json_decode($match[1], true); if (!is_array($data) || !is_array($data["formulas"] ?? null)) { exit(1); } $official=0; $source=0; $chiefprofiles=0; $chiefoccurrences=0; $chieflinks=0; foreach ($data["formulas"] as $formula) { if (($formula["o"] ?? false) === true) { $official++; } else { $source++; } $chiefs=$formula["ch"] ?? []; if (is_array($chiefs) && count($chiefs) > 0) { $chiefprofiles++; foreach ($chiefs as $chief) { $chiefoccurrences++; if (($chief["o"] ?? false) === true) { $chieflinks++; } } } } if ($official !== 181 || $source !== 145 || $chiefprofiles !== 326 || $chiefoccurrences !== 577 || $chieflinks !== 504 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/formula-category-tree-map.html"
php -r '$html=file_get_contents($argv[1]); if (!preg_match("~<script type=\"application/json\" id=\"formula-tree-data\">(.*?)</script>~s", $html, $match)) { exit(1); } $data=json_decode($match[1], true); if (!is_array($data) || !is_array($data["formulas"] ?? null)) { exit(1); } $official=0; $source=0; $chiefprofiles=0; $chiefoccurrences=0; $chieflinks=0; foreach ($data["formulas"] as $formula) { if (($formula["o"] ?? false) === true) { $official++; } else { $source++; } $chiefs=$formula["ch"] ?? []; if (is_array($chiefs) && count($chiefs) > 0) { $chiefprofiles++; foreach ($chiefs as $chief) { $chiefoccurrences++; if (($chief["o"] ?? false) === true) { $chieflinks++; } } } } if ($official !== 181 || $source !== 145 || $chiefprofiles !== 326 || $chiefoccurrences !== 577 || $chieflinks !== 504 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/formula-category-tree-map-es.html"
grep -Fq '$fromformulatree' "$TEMP_DIR/moodle/formula-list.php"
grep -Fq '$fromformulatree' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq 'Back to Formula Visual Tree' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq 'Back to Visual Tree Map' "$TEMP_DIR/moodle/formula-list.php"
grep -Fq 'Searchable cards + visual tree' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '$preparationdecoctionurl' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq 'Preparation & Decoction' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq 'safety-preparation.html' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '<h1 class="study-note__title">Preparation &amp; Decoction</h1>' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq 'Yan Hu Suo is classically vinegar-processed, not salt-processed' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq 'Zhu Sha contains mercury' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '<h1 class="study-note__title">Preparación y Decocción</h1>' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq 'Yan Hu Suo se procesa clásicamente con vinagre, no con sal' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq 'Zhu Sha contiene mercurio' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq 'class="study-note__block prep-block"' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq 'class="study-note__block prep-block"' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq '<h2 class="prep-page-rule"><span>Core rule</span>Confirm identity and required processing before deciding how the substance enters the formula.</h2>' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '<h2 class="prep-page-rule"><span>Regla principal</span>Confirma la identidad y el procesamiento requerido antes de decidir cómo entra la sustancia en la fórmula.</h2>' "$TEMP_DIR/static/safety-preparation-es.html"
if grep -Fq 'What must be confirmed before an herb enters a formula?' "$TEMP_DIR/static/safety-preparation.html" || grep -Fq '¿Qué debe confirmarse antes de añadir una hierba a una fórmula?' "$TEMP_DIR/static/safety-preparation-es.html"; then
    echo "ERROR: la regla superior todavía aparece como pregunta o barra interactiva." >&2
    exit 1
fi
grep -Fq 'How does processing change the herb?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '¿Cómo cambia el procesamiento la hierba?' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq 'Why and how are herbs processed?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '¿Por qué y cómo se procesan las hierbas?' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq 'When is each herb added?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '¿Cuándo se añade cada hierba?' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq '<th>Study question</th><th>Typical purpose</th><th>High-yield example or limit</th>' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '<th>Pregunta de estudio</th><th>Propósito habitual</th><th>Ejemplo o límite de alto rendimiento</th>' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq 'Why can Fù Zǐ, Chuān Wū and Cǎo Wū be fatal without correct processing?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq 'How can ginger release the Exterior, warm the Interior or stop bleeding?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq 'What can honey-frying add—and what risk must be considered?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '¿Por qué Fù Zǐ, Chuān Wū y Cǎo Wū pueden ser mortales sin el procesamiento correcto?' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq '¿Cómo puede el jengibre liberar el Exterior, calentar el Interior o detener sangrado?' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq '¿Qué aporta el procesamiento con miel y qué riesgo añade?' "$TEMP_DIR/static/safety-preparation-es.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "data-label=\"Study question\"") !== 25) { exit(1); }' "$TEMP_DIR/static/safety-preparation.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "data-label=\"Pregunta de estudio\"") !== 25) { exit(1); }' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq 'How can processing strengthen, moderate or redirect an effect?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq 'Why use vinegar instead of salt for Yan Hu Suo?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq 'Which herbs lose activity if they are boiled too long?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq 'Which symptoms require emergency care after a toxic medicinal?' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '¿Cómo puede el procesamiento potenciar, moderar o redirigir un efecto?' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq '¿Por qué se usa vinagre y no sal para Yan Hu Suo?' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq '¿Qué hierbas pierden actividad si se hierven demasiado?' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq '¿Qué síntomas exigen atención urgente tras un medicinal tóxico?' "$TEMP_DIR/static/safety-preparation-es.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "?</th>") !== 27) { exit(1); }' "$TEMP_DIR/static/safety-preparation.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "?</th>") !== 27) { exit(1); }' "$TEMP_DIR/static/safety-preparation-es.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "<details class=\"study-note__block prep-block\"") !== 4 || substr_count($html, "<summary class=\"study-note__heading") !== 4 || substr_count($html, "class=\"prep-page-rule\"") !== 1 || substr_count($html, "class=\"prep-block__headline\"") !== 4 || strpos($html, "<p class=\"prep-block__intro\"") !== false || substr_count($html, "<table") !== 6 || substr_count($html, "<thead") !== 4 || substr_count($html, "data-label=") !== 75 || strpos($html, "<details class=\"prep-table-group\"") !== false || strpos($html, "class=\"prep-block__label\"") !== false || strpos($html, "<details class=\"study-note__block prep-block\" open") !== false) { exit(1); }' "$TEMP_DIR/static/safety-preparation.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "<details class=\"study-note__block prep-block\"") !== 4 || substr_count($html, "<summary class=\"study-note__heading") !== 4 || substr_count($html, "class=\"prep-page-rule\"") !== 1 || substr_count($html, "class=\"prep-block__headline\"") !== 4 || strpos($html, "<p class=\"prep-block__intro\"") !== false || substr_count($html, "<table") !== 6 || substr_count($html, "<thead") !== 4 || substr_count($html, "data-label=") !== 75 || strpos($html, "<details class=\"prep-table-group\"") !== false || strpos($html, "class=\"prep-block__label\"") !== false || strpos($html, "<details class=\"study-note__block prep-block\" open") !== false) { exit(1); }' "$TEMP_DIR/static/safety-preparation-es.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "Confirm identity and required processing before deciding how the substance enters the formula.") !== 1) { exit(1); }' "$TEMP_DIR/static/safety-preparation.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "Confirma la identidad y el procesamiento requerido antes de decidir cómo entra la sustancia en la fórmula.") !== 1) { exit(1); }' "$TEMP_DIR/static/safety-preparation-es.html"
grep -Fq '<a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library">← Study Guide</a>' "$TEMP_DIR/static/safety-preparation.html"
grep -Fq '<a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology&amp;view=library&amp;lang=es">← Guía de estudio</a>' "$TEMP_DIR/static/safety-preparation-es.html"
if grep -Fq 'href="/blocks/myprogress/study-guide.php">← Study Guide' "$TEMP_DIR/static/safety-preparation.html"; then
    echo "ERROR: el regreso a Study Guide no abre directamente la biblioteca de Herbology." >&2
    exit 1
fi
if grep -Fq 'href="/my/">← Dashboard' "$TEMP_DIR/static/safety-preparation.html" "$TEMP_DIR/static/safety-preparation-es.html"; then
    echo "ERROR: Preparation & Decoction todavía regresa al Dashboard general." >&2
    exit 1
fi
if grep -Fq '← Back to Safety' "$TEMP_DIR/static/safety-preparation.html" || grep -Fq '← Volver a Seguridad' "$TEMP_DIR/static/safety-preparation-es.html"; then
    echo "ERROR: el botón de Preparation & Decoction todavía regresa a Safety Library." >&2
    exit 1
fi
grep -Fq '$cautionscontraindicationsurl' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq 'Cautions & Contraindications' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '25 monograph cautions · 60 pregnancy entries' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '$fromcautions' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq 'Back to Cautions & Contraindications' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq '<h1>Cautions &amp; Contraindications</h1>' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq 'GUIDELINE SAFETY' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq 'Contains only warnings documented in the official 304-herb guideline.' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq 'data-caution-filter="monograph"' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq 'data-caution-filter="pregcontra"' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq '15 entries containing “contraindicated”' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq '21 entries containing “avoid”' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq '24 entries containing “use cautiously”' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq 'from=cautions' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq '<h1>Precauciones y contraindicaciones</h1>' "$TEMP_DIR/static/cautions-contraindications-es.html"
grep -Fq 'SEGURIDAD DEL GUIDELINE' "$TEMP_DIR/static/cautions-contraindications-es.html"
grep -Fq '15 entradas con «contraindicada»' "$TEMP_DIR/static/cautions-contraindications-es.html"
grep -Fq '21 entradas con «evitar»' "$TEMP_DIR/static/cautions-contraindications-es.html"
grep -Fq '24 entradas con «usar con cautela»' "$TEMP_DIR/static/cautions-contraindications-es.html"
grep -Fq 'Avoid or use only with specialist direction during pregnancy because of blood-moving activity.' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq 'Evitar o usar solo por indicación especializada durante el embarazo debido a su acción movilizadora de sangre.' "$TEMP_DIR/static/cautions-contraindications-es.html"
if grep -Fq 'Avoid / specialist only' "$TEMP_DIR/static/cautions-contraindications.html" || grep -Fq 'Evitar / solo especialista' "$TEMP_DIR/static/cautions-contraindications-es.html"; then
    echo "ERROR: queda una etiqueta resumida que no reproduce el lenguaje del guideline." >&2
    exit 1
fi
grep -Fq '.cc-section--sticky-heading>h2{position:sticky;top:0' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq '<section class="cc-section cc-section--sticky-heading" id="pregnancy-lactation">' "$TEMP_DIR/static/cautions-contraindications.html"
grep -Fq '.cc-section--sticky-heading>h2{position:sticky;top:0' "$TEMP_DIR/static/cautions-contraindications-es.html"
grep -Fq '<section class="cc-section cc-section--sticky-heading" id="pregnancy-lactation">' "$TEMP_DIR/static/cautions-contraindications-es.html"
if grep -Eiq 'Caution in source|Avoid / contraindicated in source|Source-only|supplied chart|Precaución en la fuente|Solo fuente|tabla suministrada' "$TEMP_DIR/static/cautions-contraindications.html" "$TEMP_DIR/static/cautions-contraindications-es.html"; then
    echo "ERROR: la herramienta de precauciones todavía contiene clasificaciones ajenas al guideline." >&2
    exit 1
fi
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "<article class=\"cc-card\"") !== 25 || substr_count($html, "<tr data-caution-item") !== 21 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/cautions-contraindications.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "<article class=\"cc-card\"") !== 25 || substr_count($html, "<tr data-caution-item") !== 21 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/cautions-contraindications-es.html"
if grep -Fq 'data-category-jump' "$TEMP_DIR/static/guideline-herbs-by-origin.html" "$TEMP_DIR/static/guideline-herbs-by-origin-es.html"; then
    echo "ERROR: todavía existen los 33 botones de salto por categoría." >&2
    exit 1
fi
grep -Fq "'animal', 'plant', 'mineral', 'category'" "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq 'Vegetal y fúngico · 272' "$TEMP_DIR/static/guideline-herbs-by-origin-es.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "<a class=\"source-card\"") !== 304 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/guideline-herbs-by-origin.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "<a class=\"source-card\"") !== 304 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/guideline-herbs-by-origin-es.html"
grep -Fq 'origin-guideline-star' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'Agkistrodon/Bungarus' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'returnorigin=animal' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq '$fromincompatibilities' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq 'Back to incompatibility chapter' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq 'returnchapter' "$TEMP_DIR/moodle/single-herbs-list.php"

for label in "Similar Formulas" "Similar Herbs" "Pattern → Formula Finder" "Tongue & Pulse Matcher" \
    "Formula Ingredient Similarity" "Chief Herb Alternatives" "Duì Yào Pairs by Herb" \
    "Safety & Contraindication Finder" "Clinical Clue Finder" "Name Trap Detector" "Personalized Quiz"; do
    if ! grep -Fq "$label" "$TEMP_DIR/moodle/smart-study-tools.php"; then
        echo "ERROR: validación ausente en smart-study-tools.php: $label" >&2
        exit 1
    fi
done
grep -Fq "single-herb-safety-supplement.json" "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'datalist id="smart-pattern-options"' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'Select or type a pattern' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'datalist id="smart-tongue-pulse-options"' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'Select or type tongue and pulse' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq '$chiefherboptions' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'Documented Chief herb' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'Multiple Chief-herb groups' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq '$chiefgroupoptions' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'data-herb-category-toggle' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'data-herb-category-panel' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'Duì Yào Pairs by Herb' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq '$duiherbcounts' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'datalist id="smart-safety-options"' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'Quick searches:' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq '$safetypresets' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'datalist id="smart-clue-options"' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq 'Quick clinical clues:' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq '$cluepresets' "$TEMP_DIR/moodle/smart-study-tools.php"
grep -Fq '"total": 51' "$TEMP_DIR/data/dui-yao-smart-data.json"
grep -Fq 'is-embedded' "$TEMP_DIR/moodle/herbs-category-comparison.php"
grep -Fq 'PARAM_BOOL' "$TEMP_DIR/moodle/herbs-category-comparison.php"
grep -Fq "subject-study-library__smart-top" "$TEMP_DIR/moodle/study-guide.php"
grep -Fq "subject-study-library__score-top" "$TEMP_DIR/moodle/study-guide.php"
grep -Fq "/blocks/myprogress/study-score.php" "$TEMP_DIR/moodle/study-guide.php"
grep -Fq "formula-practice-progress-v1" "$TEMP_DIR/moodle/study-score.php"
grep -Fq "40% accuracy, 25% coverage, 20% retention and 15% consistency" "$TEMP_DIR/moodle/study-score.php"
grep -Fq "data-category-body" "$TEMP_DIR/moodle/study-score.php"
grep -Fq "data-recommendations" "$TEMP_DIR/moodle/study-score.php"
if grep -Fq "subject-study-library__journey-option subject-study-library__journey-option--smart" "$TEMP_DIR/moodle/study-guide.php"; then
    echo "ERROR: Smart Study Tools todavía aparece dentro de Main Study Guide." >&2
    exit 1
fi
grep -Fq "Find similar formulas" "$TEMP_DIR/moodle/formula-list.php"
grep -Fq "formula-architecture-data.json" "$TEMP_DIR/moodle/formula-list.php"
grep -Fq "formula-list__architecture-groups" "$TEMP_DIR/moodle/formula-list.php"
grep -Fq '"total": 12' "$TEMP_DIR/data/formula-architecture-data.json"
grep -Fq '"name": "Ba Zhen Tang"' "$TEMP_DIR/data/formula-architecture-data.json"
grep -Fq "Find similar herbs" "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq '$fromsmart' "$TEMP_DIR/moodle/formula-list.php"
grep -Fq '$fromsmart' "$TEMP_DIR/moodle/single-herbs-list.php"
grep -Fq "data-practice-mode=\"weak\"" "$TEMP_DIR/static/formula-practice.html"
grep -Fq "data-practice-mode=\"weak\"" "$TEMP_DIR/static/formula-practice-es.html"
grep -Fq "state.mode === 'weak'" "$TEMP_DIR/static/formula-practice.js"
grep -Fq "percentage(result.correct, result.total) < 80" "$TEMP_DIR/static/formula-practice.js"
for focus in dui modifications clinical patterns cautions indications actions; do
    grep -Fq "data-practice-mode=\"$focus\"" "$TEMP_DIR/static/formula-practice.html"
    grep -Fq "data-practice-mode=\"$focus\"" "$TEMP_DIR/static/formula-practice-es.html"
done
for size in 5 10 15 20; do
    grep -Fq "data-practice-size=\"$size\"" "$TEMP_DIR/static/formula-practice.html"
    grep -Fq "data-practice-size=\"$size\"" "$TEMP_DIR/static/formula-practice-es.html"
done
if grep -Eq 'data-practice-size="(25|50|100)"' "$TEMP_DIR/static/formula-practice.html" "$TEMP_DIR/static/formula-practice-es.html"; then
    echo "ERROR: todavía existen tamaños antiguos 25, 50 o 100 en Formula Quiz." >&2
    exit 1
fi
grep -Fq "appendDuiYaoQuestions" "$TEMP_DIR/static/formula-practice.js"
grep -Fq "history.focuses" "$TEMP_DIR/static/formula-practice.js"
grep -Fq "const diagnosticPool" "$TEMP_DIR/static/formula-practice.js"
grep -Fq "if (personalized.length) return personalized" "$TEMP_DIR/static/formula-practice.js"
grep -Fq "if (diagnosticPool.length) return diagnosticPool" "$TEMP_DIR/static/formula-practice.js"
grep -Fq 'data-sequence="11"' "$TEMP_DIR/static/herbal-incompatibilities.html"
grep -Fq 'data-sequence="11"' "$TEMP_DIR/static/herbal-incompatibilities-es.html"
grep -Fq 'id="origin-animal"' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'id="origin-plant"' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'id="origin-mineral"' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'Jīn Qián Bái Huā Shé' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'Yú Nǎo Shí' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'origin-guideline-link' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'origin-group-grid' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'origin-reference-chip' "$TEMP_DIR/static/incompatibilities-medicinal-origins-es.html"
grep -Fq 'Licorice incompatibility group' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'Grupo de incompatibilidad del regaliz' "$TEMP_DIR/static/incompatibilities-medicinal-origins-es.html"
grep -Fq 'Official guideline' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'Guideline oficial' "$TEMP_DIR/static/incompatibilities-medicinal-origins-es.html"
grep -Fq 'single-herbs-list.php?herb=Bai%20Hua%20She' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"
grep -Fq 'Variante de la imagen suministrada' "$TEMP_DIR/static/incompatibilities-dietary-es.html"
grep -Fq 'Chì Sháo' "$TEMP_DIR/static/incompatibilities-shi-ba-fan-gancao-lilu.html"
if grep -Fq 'Multibranded krait' "$TEMP_DIR/static/incompatibilities-medicinal-origins.html"; then
    echo "ERROR: se encontró la identificación errónea Multibranded krait." >&2
    exit 1
fi
php -r '$data=json_decode(file_get_contents($argv[1]), true); if (!is_array($data) || count($data) !== 51) { exit(1); }' "$TEMP_DIR/static/dui-yao-data.json"
php -r '$data=json_decode(file_get_contents($argv[1]), true); if (!is_array($data) || count($data["questions"] ?? []) < 290) { exit(1); }' "$TEMP_DIR/static/formula-modifications-generated.json"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || { echo "ERROR: no existe $TARGET_DIR" >&2; exit 1; }
for file in study-guide.php formula-list.php single-herbs-list.php     formula-board-roster-2026.json formulas-study-guide-data.json formula-guideline-profile-additions.json     formula-tongue-pulse-data.json single-herb-card-index.json single-herb-properties.json     single-herb-rich-profiles.json single-herb-actions-indications-supplement.json; do
    [[ -f "$TARGET_DIR/$file" ]] || { echo "ERROR: falta $TARGET_DIR/$file" >&2; exit 1; }
done

STUDY_NOTE_DIRS=()
for candidate in     "/var/www/acupuncturetests-study-guide/current/comparisons"     "$MOODLE_ROOT/study-guide/comparisons"     "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"     "/var/www/html/study-guide/comparisons"; do
    if [[ -d "$candidate" && -f "$candidate/formula-practice.html" ]]; then
        already_added=0
        for existing_dir in "${STUDY_NOTE_DIRS[@]}"; do
            if [[ "$existing_dir" == "$candidate" ]]; then
                already_added=1
                break
            fi
        done
        if [[ "$already_added" -eq 0 ]]; then
            STUDY_NOTE_DIRS+=("$candidate")
        fi
    fi
done
while IFS= read -r reference_file; do
    candidate="$(dirname "$reference_file")"
    case "$candidate" in
        */backups/*|*/releases/*) continue ;;
    esac
    already_added=0
    for existing_dir in "${STUDY_NOTE_DIRS[@]}"; do
        if [[ "$existing_dir" == "$candidate" ]]; then
            already_added=1
            break
        fi
    done
    if [[ "$already_added" -eq 0 ]]; then
        STUDY_NOTE_DIRS+=("$candidate")
    fi
done < <(find /var/www -type f -name formula-practice.html -path '*/comparisons/formula-practice.html' -print)
[[ "${#STUDY_NOTE_DIRS[@]}" -gt 0 ]] || { echo "ERROR: no se encontró ningún directorio público study-guide/comparisons" >&2; exit 1; }

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/smart-study-tools-$TIMESTAMP"
mkdir -p "$BACKUP/moodle" "$BACKUP/static"
reference="$TARGET_DIR/study-guide.php"
owner_group="$(stat -c '%u:%g' "$reference")"
mode="$(stat -c '%a' "$reference")"

for file in "${MOODLE_FILES[@]}"; do
    target="$TARGET_DIR/$file"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP/moodle/$file"
        file_owner_group="$(stat -c '%u:%g' "$target")"
        file_mode="$(stat -c '%a' "$target")"
    else
        file_owner_group="$owner_group"
        file_mode="$mode"
    fi
    install -o "${file_owner_group%:*}" -g "${file_owner_group#*:}" -m "$file_mode" "$TEMP_DIR/moodle/$file" "$target"
    cmp -s "$TEMP_DIR/moodle/$file" "$target"
    php -l "$target" >/dev/null
done

for file in "${DATA_FILES[@]}"; do
    target="$TARGET_DIR/$file"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP/moodle/$file"
        file_owner_group="$(stat -c '%u:%g' "$target")"
        file_mode="$(stat -c '%a' "$target")"
    else
        file_owner_group="$owner_group"
        file_mode="$mode"
    fi
    install -o "${file_owner_group%:*}" -g "${file_owner_group#*:}" -m "$file_mode" "$TEMP_DIR/data/$file" "$target"
    cmp -s "$TEMP_DIR/data/$file" "$target"
done

static_index=0
for STUDY_NOTE_DIR in "${STUDY_NOTE_DIRS[@]}"; do
    static_index=$((static_index + 1))
    static_backup="$BACKUP/static/$static_index"
    mkdir -p "$static_backup"
    static_reference="$STUDY_NOTE_DIR/formula-practice.html"
    static_owner_group="$(stat -c '%u:%g' "$static_reference")"
    static_mode="$(stat -c '%a' "$static_reference")"
    for file in "${STATIC_FILES[@]}"; do
        target="$STUDY_NOTE_DIR/$file"
        if [[ -f "$target" ]]; then
            cp -a "$target" "$static_backup/$file"
            file_owner_group="$(stat -c '%u:%g' "$target")"
            file_mode="$(stat -c '%a' "$target")"
        else
            file_owner_group="$static_owner_group"
            file_mode="$static_mode"
        fi
        install -o "${file_owner_group%:*}" -g "${file_owner_group#*:}" -m "$file_mode" "$TEMP_DIR/static/$file" "$target"
        cmp -s "$TEMP_DIR/static/$file" "$target"
    done
    grep -Fq '← Study Guide' "$STUDY_NOTE_DIR/safety-library.html"
    grep -Fq '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library' "$STUDY_NOTE_DIR/safety-library.html"
    if grep -Fq 'href="/my/"' "$STUDY_NOTE_DIR/safety-library.html" "$STUDY_NOTE_DIR/safety-library-es.html"; then
        echo "ERROR: $STUDY_NOTE_DIR conserva una salida de Safety Library hacia /my/." >&2
        exit 1
    fi
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

PUBLIC_SAFETY_URL="https://www.acupuncturetests.com/study-guide/comparisons/safety-library.html?rev=$REVISION"
curl -fsSL -H 'Cache-Control: no-cache' -H 'Pragma: no-cache' "$PUBLIC_SAFETY_URL" -o "$TEMP_DIR/public-safety-library.html"
grep -Fq '← Study Guide' "$TEMP_DIR/public-safety-library.html"
grep -Fq '/blocks/myprogress/study-guide.php?area=herbology&amp;view=library' "$TEMP_DIR/public-safety-library.html"
if grep -Fq 'href="/my/"' "$TEMP_DIR/public-safety-library.html"; then
    echo "ERROR: la página pública todavía contiene una salida hacia /my/." >&2
    exit 1
fi

echo "OK: My Study Score visible antes de Smart Study Tools y conectado con el historial local del Formula Quiz."
echo "OK: My Study Score calcula precisión, cobertura, retención, consistencia, categorías y recomendaciones."
echo "OK: Smart Study Tools visible como botón superior, a la izquierda del selector de idioma."
echo "OK: Pattern → Formula incluye una lista desplegable y buscable de patrones verificados."
echo "OK: Tongue & Pulse incluye una lista desplegable y buscable de 63 hallazgos verificados."
echo "OK: Chief Herb Alternatives muestra 117 hierbas individuales y 78 grupos múltiples documentados."
echo "OK: Similar Formulas, Similar Herbs, Pattern, Tongue & Pulse, Ingredients y Chief usan el guideline verificado."
echo "OK: Similar Herbs despliega debajo la tabla comparativa de su categoría Bensky."
echo "OK: Duì Yào permite buscar 51 pares oficiales desde un índice de 77 hierbas."
echo "OK: Safety Finder incluye 10 búsquedas predeterminadas y búsqueda libre."
echo "OK: Clinical Clue Finder incluye 10 pistas clínicas predeterminadas y búsqueda libre."
echo "OK: Duì Yào, Name Traps, Safety y Clinical Clue están conectados con sus bancos oficiales."
echo "OK: Personalized Quiz prioriza fórmulas por debajo de 80% según el historial local."
echo "OK: Formula Quiz incluye Duì Yào, modificaciones, casos clínicos, patrones, precauciones, indicaciones y acciones."
echo "OK: Formula Quiz ofrece sesiones de 5, 10, 15 o 20 preguntas y registra el resultado por enfoque."
echo "OK: Personalized Review nunca queda vacío: usa debilidades, menor dominio o diagnóstico general."
echo "OK: Herbal Incompatibilities incluye 11 capítulos y la variante alimentaria de la imagen suministrada."
echo "OK: Medicinal Origins separa Animal, Vegetal y Mineral mediante tres botones visibles."
echo "OK: 30 entradas animales corregidas incluyen límites legales, éticos y taxonómicos."
echo "OK: los 11 capítulos eliminan caracteres chinos y muestran un índice plegable de hierbas oficiales."
echo "OK: cada capítulo explica primero su regla y presenta después el índice farmacéutico."
echo "OK: Medicinal Source Guide aparece como apéndice de referencia, no como regla de incompatibilidad."
echo "OK: Secondary Study Guide incluye Guideline Medicinals by Origin con las 304 entradas oficiales."
echo "OK: clasificación completa: 24 animales, 8 minerales y 272 vegetales/fúngicas, con buscador y regreso contextual."
echo "OK: botón By Bensky Category agrupa las 304 sustancias en las 33 categorías oficiales."
echo "OK: los 33 botones de categorías fueron reemplazados por una barra de búsqueda con sugerencias."
echo "OK: en teléfonos, las cuatro vistas usan un selector compacto en lugar de una fila de botones."
echo "OK: Main Study Guide está organizado en Single Herbs, Formulas, Duì Yào & Combinations y Safety."
echo "OK: el encabezado del mapa principal se titula Study Guide / Guía de estudio."
echo "OK: Preparation & Decoction aparece en Safety de la guía principal y abre directamente Safety 1."
echo "OK: el botón superior regresa directamente a Study Guide > Herbology > Library, conservando el idioma."
echo "OK: todos los controles Dashboard/Library dentro de Study Guide regresan a Herbology > Library; la navegación global de Moodle no se modifica."
echo "OK: todas las salidas de las secciones de Safety Library, incluida la lectura continua, regresan directamente a Herbology > Study Guide."
echo "OK: el botón superior de Safety Library se titula Study Guide / Guía de estudio y abre directamente Herbology > Library."
echo "OK: Safety Library se abre mediante safety-library.php con no-store; el botón se fuerza a Study Guide y nunca apunta a /my/."
echo "OK: study-return-navigation.js ya no elimina Study Guide ni crea Back to Dashboard; normaliza todos los regresos a Herbology > Library."
echo "OK: las 13 opciones de Secondary Study Guide regresan a Herbology > Library en inglés y español, con etiquetas Study Guide normalizadas."
echo "OK: las 9 opciones de Comparison regresan a Herbology > Library en inglés y español, sin enlaces a Dashboard."
echo "OK: se retiró el botón redundante Board Level Activities de 16 páginas que ya muestran navegación contextual de Study Guide."
echo "OK: Safety Library usa el nuevo archivo safety-library.html; tanto el logotipo como el botón superior regresan a Herbology > Study Guide y no queda ninguna salida a /my/."
echo "OK: la página pública safety-library.html fue descargada y validada después de instalar todas las rutas estáticas detectadas."
echo "OK: Safety 1 integra propósito, métodos, tostado y excipientes líquidos, con correcciones para Yan Hu Suo, sal y Zhu Sha."
echo "OK: la regla principal aparece como encabezado superior informativo, sin pregunta ni control desplegable."
echo "OK: los otros cuatro encabezados mezclan pregunta y respuesta breve en la misma barra desplegable."
echo "OK: en teléfono, las filas abiertas se apilan como tarjetas verticales con etiquetas visibles."
echo "OK: las 27 filas de Preparation & Decoction usan preguntas-gancho en inglés y español."
echo "OK: Preparation & Decoction fue retirado de Safety Library; la biblioteca muestra 12 secciones restantes."
echo "OK: Cautions & Contraindications aparece como herramienta independiente en Main Study Guide."
echo "OK: Cautions & Contraindications muestra 25 precauciones de monografía y 60 hierbas con advertencia de embarazo del guideline."
echo "OK: las filas muestran la frase exacta del guideline; los filtros solo buscan los términos “contraindicated”, “avoid” y “use cautiously”."
echo "OK: el título de Pregnancy & lactation permanece visible mientras se recorre su sección."
echo "OK: Herbal Incompatibilities está integrado como la sección 13 de Safety Library y sus controles de salida regresan a Study Guide."
echo "OK: Formula Category Map convierte las cinco imágenes en 18 ramas interactivas y una sección adicional."
echo "OK: Formula Category Visual Tree recrea los mapas 1–5 como SVG enlazado, con zoom, pantalla completa y descarga."
echo "OK: Visual Tree Map aparece como el primer botón, a la izquierda de los modos Official y Full source."
echo "OK: mapa central, categorías y subgrupos enlazan tarjetas filtradas y conservan regreso al árbol visual."
echo "OK: las 326 fórmulas muestran Chief identificado; 577 menciones incluyen 504 enlaces al guideline y ninguna fórmula queda pendiente."
echo "OK: el árbol visual acerca Formula y Chief Herb, aumenta tipografía y conserva legibilidad con desplazamiento horizontal."
echo "OK: los subgrupos largos se distribuyen en dos líneas sin invadir las fórmulas."
echo "OK: el conteo 181/145 se valida desde el JSON del mapa sin confundir los enlaces Chief."
echo "OK: el segundo modo explica que añade 145 fórmulas no oficiales y muestra conteos official + source-only."
echo "OK: un tercer botón abre el mapa original de 63 perfiles enlazados y permite regresar al mapa de categorías."
echo "OK: las fórmulas oficiales adicionales aparecen después de los subgrupos principales del mapa."
echo "OK: el mapa separa 181 fórmulas oficiales de 145 referencias de la fuente y conserva regreso contextual."
echo "OK: los términos ambiguos o no oficiales permanecen como referencias sin enlace."
echo "OK: 12 tarjetas derivadas muestran la composición por fórmula base y las hierbas de cada grupo."
echo "OK: tarjetas de fórmulas y hierbas conservan acceso contextual y botón de regreso."
echo "OK: motor local gratuito; no usa API externa ni transmite el historial."
echo "OK: archivos públicos instalados en ${#STUDY_NOTE_DIRS[@]} ruta(s): ${STUDY_NOTE_DIRS[*]}."
echo "RESPALDO: $BACKUP"
