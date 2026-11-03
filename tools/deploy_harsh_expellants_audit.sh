#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO: $BASH_COMMAND (código $status)" >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"
PHP_BIN="${PHP_BIN:-}"
CATEGORY_PATH="blocks/myprogress/single-herbs-category-harsh-expellants.php"
MOIST_PATH="blocks/myprogress/single-herbs-category-moist-laxatives.php"
LABEL_CATEGORY_PATHS=(
    "blocks/myprogress/single-herbs-category-cool-acrid.php"
    "blocks/myprogress/single-herbs-category-clear-heat-cool-blood.php"
    "blocks/myprogress/single-herbs-category-clear-heat-dry-damp.php"
    "blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"
    "blocks/myprogress/single-herbs-category-clear-deficient-heat.php"
    "blocks/myprogress/single-herbs-category-clear-heat-drain-fire.php"
    "blocks/myprogress/single-herbs-category-clear-summer-heat.php"
    "blocks/myprogress/single-herbs-category-cool-transform-phlegm.php"
    "blocks/myprogress/single-herbs-category-downward-draining-purgatives.php"
    "blocks/myprogress/single-herbs-category-drain-dampness.php"
    "blocks/myprogress/single-herbs-category-relieve-cough-wheezing.php"
    "blocks/myprogress/single-herbs-category-warm-transform-phlegm.php"
)
GUIDE_PATH="blocks/myprogress/single-herbs-study-guide.php"
GUIDE_TRANSLATION_PATH="blocks/myprogress/single-herbs-study-guide-es.php"
CARD_INDEX_PATH="blocks/myprogress/single-herb-card-index.json"
PROPERTIES_PATH="blocks/myprogress/single-herb-properties.json"
REFERENCE_PATH="blocks/myprogress/single-herbs-dui-yao-reference.php"
DUI_DATA_PATH="study-guide/comparisons/dui-yao-data.json"
FORMULA_GUIDE_PATH="blocks/myprogress/formulas-study-guide.php"
FORMULA_DATA_PATH="blocks/myprogress/formulas-study-guide-data.json"
FORMULA_ROSTER_PATH="blocks/myprogress/formula-board-roster-2026.json"
BOARD_GUIDELINE_EN_PATH="study-guide/guidelines/chinese-herbal-medicine.html"
BOARD_GUIDELINE_ES_PATH="study-guide/guidelines/chinese-herbal-medicine-es.html"
BOARD_GUIDELINE_CSS_PATH="study-guide/guidelines/guidelines.css"
FORMULA_PREVIEW_PATH="study-guide/guidelines/formulas-appendix-c-2026-preview.html"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_harsh_expellants_audit.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

for relative_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$CARD_INDEX_PATH" "$PROPERTIES_PATH" "$DUI_DATA_PATH" "$FORMULA_GUIDE_PATH" "$FORMULA_DATA_PATH" "$BOARD_GUIDELINE_EN_PATH" "$BOARD_GUIDELINE_ES_PATH" "$BOARD_GUIDELINE_CSS_PATH"; do
    if [[ ! -f "$MOODLE_ROOT/$relative_path" ]]; then
        echo "ERROR: no se encontró el archivo activo $MOODLE_ROOT/$relative_path"
        exit 1
    fi
done

if [[ -z "$PHP_BIN" ]]; then
    for candidate in php php8.4 php8.3 php8.2 php8.1 php8.0; do
        if command -v "$candidate" >/dev/null 2>&1; then
            PHP_BIN="$(command -v "$candidate")"
            break
        fi
    done
fi

if [[ -z "$PHP_BIN" || ! -x "$PHP_BIN" ]]; then
    echo "ERROR: no se encontró PHP CLI. Indica la ruta con PHP_BIN=/ruta/al/php"
    exit 1
fi

if ! command -v python3 >/dev/null 2>&1; then
    echo "ERROR: se necesita python3 para validar el JSON"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/single-herbs-dui-yao-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

echo "Moodle detectado: $MOODLE_ROOT"
echo "PHP CLI detectado: $PHP_BIN"
echo "Descargando referencias Duì Yào para todas las categorías desde $REVISION..."

for relative_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$CARD_INDEX_PATH" "$PROPERTIES_PATH" "$REFERENCE_PATH" "$DUI_DATA_PATH" "$FORMULA_GUIDE_PATH" "$FORMULA_DATA_PATH" "$FORMULA_ROSTER_PATH" "$BOARD_GUIDELINE_EN_PATH" "$BOARD_GUIDELINE_ES_PATH" "$BOARD_GUIDELINE_CSS_PATH" "$FORMULA_PREVIEW_PATH"; do
    mkdir -p "$TEMP_DIR/$(dirname "$relative_path")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative_path" -o "$TEMP_DIR/$relative_path"
done

for php_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$REFERENCE_PATH" "$FORMULA_GUIDE_PATH"; do
    "$PHP_BIN" -l "$TEMP_DIR/$php_path" >/dev/null
done
python3 -m json.tool "$TEMP_DIR/$PROPERTIES_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$CARD_INDEX_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$DUI_DATA_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$FORMULA_DATA_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$FORMULA_ROSTER_PATH" >/dev/null

python3 - "$TEMP_DIR/$CARD_INDEX_PATH" <<'PY'
import json
import sys

with open(sys.argv[1], encoding='utf-8') as source:
    catalog = json.load(source)

herbs = catalog.get('herbs', [])
ids = [herb.get('id') for herb in herbs]
if catalog.get('version') != 2:
    raise SystemExit(f"ERROR: versión del índice={catalog.get('version')!r}; se esperaba 2")
if catalog.get('total') != 304 or len(herbs) != 304:
    raise SystemExit(f"ERROR: el índice debe contener 304 hierbas; contiene {len(herbs)}")
expected_routed = sum(herb.get('guideNumber') is not None for herb in herbs)
if catalog.get('routedToDetailedStudy') != 203 or expected_routed != 203:
    raise SystemExit(
        f"ERROR: rutas detalladas={catalog.get('routedToDetailedStudy')!r}/{expected_routed!r}; se esperaban 203"
    )
if ids != list(range(1, 305)):
    raise SystemExit('ERROR: los IDs del guideline deben ser únicos y consecutivos de 1 a 304')
if herbs[0].get('name') != 'Ai Ye' or herbs[-1].get('name') != 'Zi Wan':
    raise SystemExit('ERROR: el orden alfabético estable del índice no coincide con Ai Ye…Zi Wan')
by_name = {herb.get('name'): herb for herb in herbs}
if not catalog.get('benskySource'):
    raise SystemExit('ERROR: falta la fuente de clasificación de Bensky en el índice')
for herb in herbs:
    category = herb.get('benskyCategory')
    if not isinstance(category, dict):
        raise SystemExit(f"ERROR: {herb.get('name')}: falta benskyCategory")
    for key in ('code', 'en', 'es'):
        if not isinstance(category.get(key), str) or not category[key].strip():
            raise SystemExit(f"ERROR: {herb.get('name')}: benskyCategory.{key} está vacío")
expected_bensky_categories = {
    # These checks deliberately cross current Study Guide routing boundaries.
    'He Ye': 'clear-summerheat',
    'Bai Bian Dou': 'tonify-qi',
    'Hua Shi': 'drain-dampness',
    'Dan Shen': 'invigorate-blood',
    'Dang Gui': 'tonify-blood',
    'Xing Ren': 'stop-coughing-calm-wheezing',
    'Cang Er Zi': 'warm-acrid-release-exterior',
    # Author-dependent placements that must follow Bensky, not Board/Yang routing.
    'Bie Jia': 'tonify-yin',
    'Cao Wu': 'dispel-wind-dampness',
    'Chuan Wu': 'dispel-wind-dampness',
    'Jie Geng': 'warm-transform-cold-phlegm',
    'Long Yan Rou': 'tonify-blood',
}
for name, expected_code in expected_bensky_categories.items():
    actual_code = by_name[name]['benskyCategory']['code']
    if actual_code != expected_code:
        raise SystemExit(
            f'ERROR: {name}: categoría Bensky={actual_code!r}; se esperaba {expected_code!r}'
        )
expected_category_two = {
    'Dan Dou Chi': (67, 2),
    'Mu Zei': (182, 2),
}
expected_category_three = {
    'Jue Ming Zi': (148, 3),
    'Lian Zi Xin': (156, 3),
    'Xia Ku Cao': (264, 3),
}
expected_category_four = {
    'Chi Shao': (46, 4),
    'Dan Shen': (68, 4),
    'Qing Dai': (198, 4),
    'Shui Niu Jiao': (236, 4),
    'Zi Cao': (301, 4),
}
expected_category_five = {
    'Bai Xian Pi': (20, 5),
    'Ku Shen': (149, 5),
    'Qin Pi': (197, 5),
}
expected_category_six = {
    'Bai Hua She She Cao': (10, 6),
    'Bai Jiang Cao': (13, 6),
    'Ban Lan Gen': (24, 6),
    'Da Qing Ye': (64, 6),
    'Jin Yin Hua': (142, 6),
    'Lian Qiao': (153, 6),
    'Pu Gong Ying': (190, 6),
    'Shan Dou Gen': (219, 6),
    'She Gan': (224, 6),
    'Tu Fu Ling': (250, 6),
    'Yu Xing Cao': (287, 6),
}
expected_cross_category_eight = {
    'He Ye': (120, 8),
}
expected_cross_category_seven = {
    'Bai Wei': (19, 7),
    'Di Gu Pi': (74, 7),
}
expected_guideline_entries = expected_category_two.copy()
expected_guideline_entries.update(expected_category_three)
expected_guideline_entries.update(expected_category_four)
expected_guideline_entries.update(expected_category_five)
expected_guideline_entries.update(expected_category_six)
expected_guideline_entries.update(expected_cross_category_seven)
expected_guideline_entries.update(expected_cross_category_eight)
for name, (expected_id, expected_guide) in expected_guideline_entries.items():
    herb = by_name.get(name, {})
    actual = (herb.get('id'), herb.get('guideNumber'))
    if actual != (expected_id, expected_guide):
        raise SystemExit(f'ERROR: {name}: ID/categoría={actual!r}; se esperaba {(expected_id, expected_guide)!r}')
PY

python3 - "$TEMP_DIR/$PROPERTIES_PATH" <<'PY'
import json
import sys

with open(sys.argv[1], encoding='utf-8') as source:
    catalog = json.load(source)

expected_counts = {
    'verified': 180,
    'propertiesOnly': 61,
    'doseOnly': 21,
    'pending': 42,
}
for key, expected in expected_counts.items():
    if catalog.get(key) != expected:
        raise SystemExit(f'ERROR: {key}={catalog.get(key)!r}; se esperaba {expected}')

herbs = {item['name']: item for item in catalog['herbs']}
expected = {
    'Hai Zao': ('verified', '6–15 g', 'Kidney, Liver, Lung, Stomach'),
    'Zao Jiao Ci': ('verified', '3–9 g', 'Liver, Stomach, Lung'),
    'Ting Li Zi': ('verified', '3–9 g', 'Lung, Bladder'),
    'Zi Su Zi': ('verified', '3–9 g', 'Large Intestine, Lung'),
    'Qian Hu': ('verified', '3–9 g', 'Lung'),
    'Gua Lou': ('verified', '9–21 g', 'Lung, Stomach, Large Intestine'),
    'Pi Pa Ye': ('verified', '6–15 g', 'Lung, Stomach'),
    'Zi Wan': ('verified', '5–9 g', 'Lung'),
}
for name, values in expected.items():
    item = herbs[name]
    actual = (item['status'], item['dose'], item['channels']['en'])
    if actual != values:
        raise SystemExit(f'ERROR: {name}: {actual!r}; se esperaba {values!r}')
PY

EXPECTED_TITLE='Downward Draining Harsh Expellants (Cathartics) (Gan Sui*, Hong Da Ji, Yuan Hua, Qian Niu Zi, Ba Dou*)'
grep -Fq "$EXPECTED_TITLE" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Only Gan Sui and Ba Dou are in the current Board guideline" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq "single-herbs-dui-yao-reference.php" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "No match in the current 51-pair Duì Yào roster" "$TEMP_DIR/$REFERENCE_PATH"
grep -Fq "Gan Sui–Gan Cao and Ba Dou–Qian Niu Zi are incompatibility or prohibited-combination relationships, not Duì Yào" "$TEMP_DIR/$REFERENCE_PATH"
grep -Fq "data-dui-yao-pair" "$TEMP_DIR/$REFERENCE_PATH"
grep -Fq "'name' => 'Hong Da Ji', 'latin' => 'Knoxiae Radix', 'official' => false" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq "3–6 g decoction; ≤3 g powder" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq "0.1–0.3 g/day; correctly defatted preparation only" "$TEMP_DIR/$CATEGORY_PATH"
grep -Fq "Cross-category reference · Official herbs with secondary bowel-moistening action" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "Primary category:" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "Relieve Coughing and Wheezing" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "Invigorate Blood and Dispel Stasis" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "Tonify Yin" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "Tonify Yang" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "Tonify Blood" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "Tonify Qi" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "In current guideline 2026" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "Included in the current guideline 2026" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "data-guideline-roster-open" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "data-guideline-roster-modal" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "data-guideline-roster-search" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "data-guideline-roster-item" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "primary Bensky category" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "single-herbs-guide__roster-category" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Primary category" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Why it appears here" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Study these thirteen official herbs as Category 1" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "\$windcoldguidelinecheck" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "\$windcoldcategorycheck" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Study these eleven official herbs as Category 2" "$TEMP_DIR/blocks/myprogress/single-herbs-category-cool-acrid.php"
grep -Fq "\$coolguidelinecheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-cool-acrid.php"
grep -Fq "\$coolcategorycheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-cool-acrid.php"
if grep -Fq "Annex · Additional official recognition herbs" "$TEMP_DIR/blocks/myprogress/single-herbs-category-cool-acrid.php"; then
    echo "ERROR: Dan Dou Chi y Mu Zei todavía aparecen como anexo de la Categoría 2"
    exit 1
fi
grep -Fq "Study these nine official herbs as Category 3" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-drain-fire.php"
grep -Fq "\$fireguidelinecheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-drain-fire.php"
grep -Fq "\$firecategorycheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-drain-fire.php"
grep -Fq "3 contextual entries outside the 2026 guideline · click to open" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-drain-fire.php"
if grep -Fq "Annex · Wider textbook recognition" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-drain-fire.php"; then
    echo "ERROR: Xia Ku Cao, Jue Ming Zi o Lian Zi Xin todavía aparecen en el anexo de la Categoría 3"
    exit 1
fi
grep -Fq "Study these eight official herbs as Category 4" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-cool-blood.php"
grep -Fq "\$bloodguidelinecheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-cool-blood.php"
grep -Fq "\$bloodcategorycheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-cool-blood.php"
grep -Fq "2 official Category 7 herbs · click to open" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-cool-blood.php"
if grep -Fq "Annex · Additional official recognition herbs" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-cool-blood.php"; then
    echo "ERROR: hierbas oficiales de la Categoría 4 todavía aparecen como anexo"
    exit 1
fi
grep -Fq "Study these seven official herbs as Category 5" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-dry-damp.php"
grep -Fq "\$dampguidelinecheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-dry-damp.php"
grep -Fq "\$dampcategorycheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-dry-damp.php"
if grep -Fq "Annex · Additional official recognition herbs" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-heat-dry-damp.php"; then
    echo "ERROR: Ku Shen, Qin Pi o Bai Xian Pi todavía aparecen como anexo de la Categoría 5"
    exit 1
fi
grep -Fq "Study these eleven official herbs as Category 6" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"
grep -Fq "\$toxinguidelinecheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"
grep -Fq "\$toxincategorycheck" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"
grep -Fq "'name' => 'Da Qing Ye'" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"
grep -Fq "'name' => 'Ban Lan Gen'" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"
grep -Fq "Primary Bensky category herbs" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"
if grep -Fq "without a primary category" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"; then
    echo "ERROR: Da Qing Ye o Ban Lan Gen todavía aparecen sin categoría primaria"
    exit 1
fi
if grep -Fq "'name' => 'He Ye'" "$TEMP_DIR/blocks/myprogress/single-herbs-category-clear-resolve-toxicity.php"; then
    echo "ERROR: He Ye todavía aparece como hierba principal de la Categoría 6"
    exit 1
fi
grep -Fq "single-herbs-guide__annex-toggle" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "3 contextual entries outside the 2026 guideline · click to open" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Categoría principal" "$TEMP_DIR/$GUIDE_TRANSLATION_PATH"
grep -Fq "Por qué aparece aquí" "$TEMP_DIR/$GUIDE_TRANSLATION_PATH"
grep -Fq "\$duiyaoadditionalherbs = array_column(\$moistannex, 'name');" "$TEMP_DIR/$MOIST_PATH"
grep -Fq "including its cross-category references" "$TEMP_DIR/$REFERENCE_PATH"
grep -Fq "Huo Ma Ren has no Duì Yào pair in the current 51-pair roster" "$TEMP_DIR/$REFERENCE_PATH"
grep -Fq "The pairs shown below do not belong to the moist-laxative category" "$TEMP_DIR/$REFERENCE_PATH"
grep -Fq '"en": "Bitter"' "$TEMP_DIR/$PROPERTIES_PATH"
grep -Fq '"dose": "0.1–0.3 g/day; defatted only"' "$TEMP_DIR/$PROPERTIES_PATH"
grep -Fq "Chapter 6 recognition annex" "$TEMP_DIR/blocks/myprogress/single-herbs-category-cool-transform-phlegm.php"
grep -Fq "Monographs in current guideline 2026" "$TEMP_DIR/blocks/myprogress/single-herbs-category-cool-transform-phlegm.php"
grep -Fq "Source monographs outside current guideline 2026" "$TEMP_DIR/blocks/myprogress/single-herbs-category-cool-transform-phlegm.php"
grep -Fq "Chapter 6 recognition annex" "$TEMP_DIR/blocks/myprogress/single-herbs-category-warm-transform-phlegm.php"
grep -Fq "Monographs in current guideline 2026" "$TEMP_DIR/blocks/myprogress/single-herbs-category-warm-transform-phlegm.php"
grep -Fq "Source monographs outside current guideline 2026" "$TEMP_DIR/blocks/myprogress/single-herbs-category-warm-transform-phlegm.php"
grep -Fq "Chapter 6 final selection map" "$TEMP_DIR/blocks/myprogress/single-herbs-category-relieve-cough-wheezing.php"
grep -Fq "Registered functional summary" "$TEMP_DIR/blocks/myprogress/single-herbs-category-relieve-cough-wheezing.php"
grep -Fq "Official pair 40/51 · Kuan Dong Hua + Zi Wan" "$TEMP_DIR/blocks/myprogress/single-herbs-category-relieve-cough-wheezing.php"
grep -Fq "Monographs in current guideline 2026" "$TEMP_DIR/blocks/myprogress/single-herbs-category-relieve-cough-wheezing.php"
grep -Fq "Source monographs outside current guideline 2026" "$TEMP_DIR/blocks/myprogress/single-herbs-category-relieve-cough-wheezing.php"
grep -Fq "37/51 · Ban Xia + Chen Pi" "$TEMP_DIR/blocks/myprogress/single-herbs-category-warm-transform-phlegm.php"
grep -Fq "41/51 · Ting Li Zi + Da Zao" "$TEMP_DIR/blocks/myprogress/single-herbs-category-relieve-cough-wheezing.php"

for label_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}"; do
    if grep -Fq "current Appendix A" "$TEMP_DIR/$label_path"; then
        echo "ERROR: la etiqueta antigua todavía aparece en $label_path"
        exit 1
    fi
    grep -Fq "current guideline 2026" "$TEMP_DIR/$label_path"
done

OFFICIAL_COUNT="$(grep -c "'official' => true" "$TEMP_DIR/$CATEGORY_PATH")"
OUTSIDE_COUNT="$(grep -c "'official' => false" "$TEMP_DIR/$CATEGORY_PATH")"
if [[ "$OFFICIAL_COUNT" -ne 2 || "$OUTSIDE_COUNT" -ne 3 ]]; then
    echo "ERROR: se esperaban 2 hierbas vigentes y 3 fuera del guideline; se detectaron $OFFICIAL_COUNT y $OUTSIDE_COUNT"
    exit 1
fi

DUI_PAIR_COUNT="$(python3 - "$TEMP_DIR/$DUI_DATA_PATH" <<'PY'
import json
import sys
with open(sys.argv[1], encoding='utf-8') as source:
    print(len(json.load(source)))
PY
)"
if [[ "$DUI_PAIR_COUNT" -ne 51 ]]; then
    echo "ERROR: se esperaban 51 parejas Duì Yào y se detectaron $DUI_PAIR_COUNT"
    exit 1
fi

python3 - "$TEMP_DIR/$FORMULA_ROSTER_PATH" "$TEMP_DIR/$FORMULA_DATA_PATH" "$TEMP_DIR/$BOARD_GUIDELINE_EN_PATH" "$TEMP_DIR/$BOARD_GUIDELINE_ES_PATH" <<'PY'
import json
import re
import sys

roster_path, profiles_path, english_path, spanish_path = sys.argv[1:]
with open(roster_path, encoding='utf-8') as source:
    roster = json.load(source)
with open(profiles_path, encoding='utf-8') as source:
    profiles = json.load(source)

formulas = roster.get('formulas', [])
if roster.get('version') != 1 or roster.get('total') != 181 or len(formulas) != 181:
    raise SystemExit('ERROR: el listado oficial debe contener exactamente 181 fórmulas')
if [item.get('id') for item in formulas] != list(range(1, 182)):
    raise SystemExit('ERROR: los IDs oficiales de fórmulas deben ser consecutivos de 1 a 181')
if len({item.get('name') for item in formulas}) != 181:
    raise SystemExit('ERROR: el listado oficial contiene nombres de fórmulas duplicados')
if any(not item.get('englishName') for item in formulas):
    raise SystemExit('ERROR: falta al menos un nombre inglés oficial de fórmula')
for item in formulas:
    category = item.get('benskyCategory')
    if not isinstance(category, dict):
        raise SystemExit(f"ERROR: {item.get('name')}: falta la categoría primaria de Bensky")
    if category.get('chapter') not in range(1, 22):
        raise SystemExit(f"ERROR: {item.get('name')}: capítulo Bensky inválido")
    for key in ('code', 'en', 'es'):
        if not isinstance(category.get(key), str) or not category[key].strip():
            raise SystemExit(f"ERROR: {item.get('name')}: benskyCategory.{key} está vacío")
if len(profiles.get('formulas', [])) != 63 or len(profiles.get('categories', [])) != 14:
    raise SystemExit('ERROR: se esperaban 63 fichas detalladas en 14 categorías internas')

official_names = {item.get('name') for item in formulas}
official_aliases = {'Jin Gui Shen Qi Wan': 'Shen Qi Wan'}
missing_profiles = [
    profile.get('name')
    for profile in profiles.get('formulas', [])
    if official_aliases.get(profile.get('name'), profile.get('name')) not in official_names
]
if missing_profiles:
    raise SystemExit(
        'ERROR: estas fichas internas no se encontraron en el Appendix C oficial: '
        + ', '.join(missing_profiles)
    )

for path in (english_path, spanish_path):
    html = open(path, encoding='utf-8').read()
    if len(re.findall(r'data-official-formula-id="\d+"', html)) != 181:
        raise SystemExit(f'ERROR: {path} no contiene las 181 fórmulas oficiales')
    if len(re.findall(r'data-formula-core-profile="\d+"', html)) != 63:
        raise SystemExit(f'ERROR: {path} no contiene las 63 fichas detalladas del núcleo')
    if 'NCBAHM_CH_Exam_Content_Outline-w-Bibliography_Jan_2026.pdf' not in html:
        raise SystemExit(f'ERROR: {path} no enlaza la fuente oficial de NCBAHM 2026')
PY

grep -Fq "Study 63 detailed profiles in 14 internal categories" "$TEMP_DIR/$FORMULA_GUIDE_PATH"
grep -Fq "all 181 official formulas" "$TEMP_DIR/$FORMULA_GUIDE_PATH"
grep -Fq "\$isofficialformula" "$TEMP_DIR/$FORMULA_GUIDE_PATH"
if [[ "$(grep -o 'data-search=' "$TEMP_DIR/$FORMULA_PREVIEW_PATH" | wc -l)" -ne 181 ]]; then
    echo "ERROR: la vista previa independiente no contiene 181 fórmulas"
    exit 1
fi
if [[ "$(grep -o 'Chapter [0-9][0-9]* · Formulas' "$TEMP_DIR/$FORMULA_PREVIEW_PATH" | wc -l)" -ne 181 ]]; then
    echo "ERROR: la vista previa independiente no muestra 181 categorías de Bensky"
    exit 1
fi

mkdir -p "$BACKUP_DIR"
for relative_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$CARD_INDEX_PATH" "$PROPERTIES_PATH" "$REFERENCE_PATH" "$DUI_DATA_PATH" "$FORMULA_GUIDE_PATH" "$FORMULA_DATA_PATH" "$FORMULA_ROSTER_PATH" "$BOARD_GUIDELINE_EN_PATH" "$BOARD_GUIDELINE_ES_PATH" "$BOARD_GUIDELINE_CSS_PATH" "$FORMULA_PREVIEW_PATH"; do
    target="$MOODLE_ROOT/$relative_path"
    temporary="$TEMP_DIR/$relative_path"
    backup_target="$BACKUP_DIR/$relative_path"
    if [[ -f "$target" ]]; then
        mkdir -p "$(dirname "$backup_target")"
        cp -a "$target" "$backup_target"
        owner_group="$(stat -c '%u:%g' "$target")"
        file_mode="$(stat -c '%a' "$target")"
    else
        owner_group="$(stat -c '%u:%g' "$MOODLE_ROOT/$GUIDE_PATH")"
        file_mode="644"
    fi
    mkdir -p "$(dirname "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$file_mode" "$temporary" "$target"
done

for php_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$REFERENCE_PATH" "$FORMULA_GUIDE_PATH"; do
    "$PHP_BIN" -l "$MOODLE_ROOT/$php_path" >/dev/null
done
python3 -m json.tool "$MOODLE_ROOT/$PROPERTIES_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$CARD_INDEX_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$DUI_DATA_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$FORMULA_DATA_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$FORMULA_ROSTER_PATH" >/dev/null

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    "$PHP_BIN" "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "AUDITORÍA DEL CAPÍTULO 6 Y REFERENCIAS DUÌ YÀO INSTALADAS"
echo "Las coincidencias se calculan desde las 51 parejas vigentes"
echo "Las categorías sin pareja muestran una nota breve con enlace al índice"
echo "Categoría 10: seis referencias cruzadas ampliadas con su categoría principal"
echo "Etiquetas actualizadas: current guideline 2026 en todas las categorías afectadas"
echo "Con asterisco: Gan Sui y Ba Dou"
echo "Sin asterisco: Hong Da Ji, Yuan Hua y Qian Niu Zi"
echo "Duì Yào: ninguna de estas cinco hierbas pertenece a las 51 parejas vigentes"
echo "Propiedades, identidad botánica, dosis y advertencias auditadas"
echo "Categorías 13–15: monografías formales, anexos y mapa comparativo actualizados"
echo "Anexos del capítulo 6: guideline vigente arriba y monografías fuente debajo"
echo "Categoría 15: resumen funcional completo y pareja oficial 40/51 registrados"
echo "Categoría 2: 11 hierbas oficiales; Dan Dou Chi y Mu Zei integradas, sin anexo"
echo "Categoría 3: 9 hierbas oficiales; tres comparaciones externas ocultas al final"
echo "Categoría 4: 8 hierbas oficiales; Bai Wei y Di Gu Pi como referencias de Categoría 7"
echo "Categoría 5: 7 hierbas oficiales; Ku Shen, Qin Pi y Bai Xian Pi integradas, sin anexo"
echo "Categoría 6: 11 hierbas Bensky; Da Qing Ye y Ban Lan Gen integradas, sin anexo"
echo "Categoría 8: He Ye ruteada según su categoría primaria Bensky"
echo "Popup del guideline: 304 hierbas con IDs estables 001–304, categoría Bensky y búsqueda"
echo "Anexo Viento-Frío: categoría principal y motivo pedagógico explícitos"
echo "Anexo Viento-Frío: plegado y movido al final de la categoría"
echo "Categoría 1: 13 hierbas oficiales; solo 3 entradas externas permanecen en el anexo"
echo "Hai Zao, Zao Jiao Ci, Ting Li Zi y Zi Su Zi: perfiles verificados y completos"
echo "Appendix C 2026: 181 fórmulas oficiales con ID, pinyin y nombre inglés"
echo "Las 63 fichas anteriores quedan identificadas únicamente como perfiles detallados internos"
echo "Respaldo: $BACKUP_DIR"
