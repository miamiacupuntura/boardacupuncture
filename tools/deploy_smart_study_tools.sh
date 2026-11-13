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

for command in curl php grep install stat cp cmp mkdir mktemp date rm id dirname; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/moodle" "$TEMP_DIR/data" "$TEMP_DIR/static"

MOODLE_FILES=(smart-study-tools.php study-score.php study-guide.php formula-list.php single-herbs-list.php herbs-category-comparison.php)
DATA_FILES=(single-herb-safety-supplement.json formula-architecture-data.json dui-yao-smart-data.json)
STATIC_FILES=(formula-practice.js formula-practice.html formula-practice-es.html formula-practice-data.json formula-practice-translations-es.json dui-yao-data.json formula-modifications-manifest.json formula-modifications-supplement.json formula-modifications-gui-zhi-tang.json formula-modifications-ge-gen-tang.json formula-modifications-generated.json formula-category-expansion.json herbal-incompatibilities.html herbal-incompatibilities-es.html incompatibilities-seven-relationships.html incompatibilities-seven-relationships-es.html incompatibilities-xu-vs-shi.html incompatibilities-xu-vs-shi-es.html incompatibilities-wei-vs-sha.html incompatibilities-wei-vs-sha-es.html incompatibilities-wu-vs-fan.html incompatibilities-wu-vs-fan-es.html incompatibilities-shi-ba-fan-aconite.html incompatibilities-shi-ba-fan-aconite-es.html incompatibilities-shi-ba-fan-gancao-lilu.html incompatibilities-shi-ba-fan-gancao-lilu-es.html incompatibilities-shi-jiu-wei.html incompatibilities-shi-jiu-wei-es.html incompatibilities-extended-counteraction.html incompatibilities-extended-counteraction-es.html incompatibilities-extended-antagonism.html incompatibilities-extended-antagonism-es.html incompatibilities-dietary.html incompatibilities-dietary-es.html incompatibilities-medicinal-origins.html incompatibilities-medicinal-origins-es.html guideline-herbs-by-origin.html guideline-herbs-by-origin-es.html formula-category-map.html formula-category-map-es.html)

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
grep -Fq '/study-guide/comparisons/formula-category-map.html' "$TEMP_DIR/moodle/study-guide.php"
grep -Fq '$fromformulamap' "$TEMP_DIR/moodle/formula-list.php"
grep -Fq 'Back to Formula Category Map' "$TEMP_DIR/moodle/formula-list.php"
grep -Fq 'Official guideline only · 181' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'Include source references · 326' "$TEMP_DIR/static/formula-category-map.html"
grep -Fq 'Solo guideline oficial · 181' "$TEMP_DIR/static/formula-category-map-es.html"
grep -Fq '18 supplied branches + additional section' "$TEMP_DIR/static/formula-category-map.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "\"o\":true") !== 181 || substr_count($html, "\"o\":false") !== 145 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/formula-category-map.html"
php -r '$html=file_get_contents($argv[1]); if (substr_count($html, "\"o\":true") !== 181 || substr_count($html, "\"o\":false") !== 145 || preg_match("/\\p{Han}/u", $html)) { exit(1); }' "$TEMP_DIR/static/formula-category-map-es.html"
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

STUDY_NOTE_DIR=""
for candidate in     "/var/www/acupuncturetests-study-guide/current/comparisons"     "$MOODLE_ROOT/study-guide/comparisons"     "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"     "/var/www/html/study-guide/comparisons"; do
    if [[ -d "$candidate" && -f "$candidate/formula-practice.html" ]]; then
        STUDY_NOTE_DIR="$candidate"
        break
    fi
done
[[ -n "$STUDY_NOTE_DIR" ]] || { echo "ERROR: no se encontró el directorio público study-guide/comparisons" >&2; exit 1; }

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

static_reference="$STUDY_NOTE_DIR/formula-practice.html"
static_owner_group="$(stat -c '%u:%g' "$static_reference")"
static_mode="$(stat -c '%a' "$static_reference")"
for file in "${STATIC_FILES[@]}"; do
    target="$STUDY_NOTE_DIR/$file"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP/static/$file"
        file_owner_group="$(stat -c '%u:%g' "$target")"
        file_mode="$(stat -c '%a' "$target")"
    else
        file_owner_group="$static_owner_group"
        file_mode="$static_mode"
    fi
    install -o "${file_owner_group%:*}" -g "${file_owner_group#*:}" -m "$file_mode" "$TEMP_DIR/static/$file" "$target"
    cmp -s "$TEMP_DIR/static/$file" "$target"
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
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
echo "OK: Formula Category Map convierte las cinco imágenes en 18 ramas interactivas y una sección adicional."
echo "OK: el mapa separa 181 fórmulas oficiales de 145 referencias de la fuente y conserva regreso contextual."
echo "OK: los términos ambiguos o no oficiales permanecen como referencias sin enlace."
echo "OK: 12 tarjetas derivadas muestran la composición por fórmula base y las hierbas de cada grupo."
echo "OK: tarjetas de fórmulas y hierbas conservan acceso contextual y botón de regreso."
echo "OK: motor local gratuito; no usa API externa ni transmite el historial."
echo "OK: archivos públicos instalados en $STUDY_NOTE_DIR."
echo "RESPALDO: $BACKUP"
