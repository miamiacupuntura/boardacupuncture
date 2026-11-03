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

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_harsh_expellants_audit.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

for relative_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$CARD_INDEX_PATH" "$PROPERTIES_PATH" "$DUI_DATA_PATH"; do
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

for relative_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$CARD_INDEX_PATH" "$PROPERTIES_PATH" "$REFERENCE_PATH" "$DUI_DATA_PATH"; do
    mkdir -p "$TEMP_DIR/$(dirname "$relative_path")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative_path" -o "$TEMP_DIR/$relative_path"
done

for php_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$REFERENCE_PATH"; do
    "$PHP_BIN" -l "$TEMP_DIR/$php_path" >/dev/null
done
python3 -m json.tool "$TEMP_DIR/$PROPERTIES_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$CARD_INDEX_PATH" >/dev/null
python3 -m json.tool "$TEMP_DIR/$DUI_DATA_PATH" >/dev/null

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
if ids != list(range(1, 305)):
    raise SystemExit('ERROR: los IDs del guideline deben ser únicos y consecutivos de 1 a 304')
if herbs[0].get('name') != 'Ai Ye' or herbs[-1].get('name') != 'Zi Wan':
    raise SystemExit('ERROR: el orden alfabético estable del índice no coincide con Ai Ye…Zi Wan')
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
grep -Fq "Primary category" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Why it appears here" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "Study these thirteen official herbs as Category 1" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "\$windcoldguidelinecheck" "$TEMP_DIR/$GUIDE_PATH"
grep -Fq "\$windcoldcategorycheck" "$TEMP_DIR/$GUIDE_PATH"
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

mkdir -p "$BACKUP_DIR"
for relative_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$CARD_INDEX_PATH" "$PROPERTIES_PATH" "$REFERENCE_PATH" "$DUI_DATA_PATH"; do
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

for php_path in "$CATEGORY_PATH" "$MOIST_PATH" "${LABEL_CATEGORY_PATHS[@]}" "$GUIDE_PATH" "$GUIDE_TRANSLATION_PATH" "$REFERENCE_PATH"; do
    "$PHP_BIN" -l "$MOODLE_ROOT/$php_path" >/dev/null
done
python3 -m json.tool "$MOODLE_ROOT/$PROPERTIES_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$CARD_INDEX_PATH" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/$DUI_DATA_PATH" >/dev/null

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
echo "Popup del guideline: 304 hierbas con IDs estables 001–304 y búsqueda"
echo "Anexo Viento-Frío: categoría principal y motivo pedagógico explícitos"
echo "Anexo Viento-Frío: plegado y movido al final de la categoría"
echo "Categoría 1: 13 hierbas oficiales; solo 3 entradas externas permanecen en el anexo"
echo "Hai Zao, Zao Jiao Ci, Ting Li Zi y Zi Su Zi: perfiles verificados y completos"
echo "Respaldo: $BACKUP_DIR"
