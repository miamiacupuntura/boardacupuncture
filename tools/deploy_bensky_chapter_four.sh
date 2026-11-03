#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO: $BASH_COMMAND (código $status)" >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups}"
PHP_BIN="${PHP_BIN:-}"
FILES=(
    "blocks/myprogress/single-herbs-study-guide.php"
    "blocks/myprogress/single-herbs-bensky-chapter-four.php"
    "blocks/myprogress/single-herbs-bensky-chapter-seventeen.php"
    "blocks/myprogress/single-herbs-bensky-chapter-eighteen.php"
    "blocks/myprogress/single-herbs-bensky-chapter-nineteen.php"
    "blocks/myprogress/single-herb-card-index.json"
    "blocks/myprogress/single-herb-properties.json"
    "blocks/myprogress/single-herb-actions-indications-supplement.json"
    "blocks/myprogress/single-herb-safety-supplement.json"
    "blocks/myprogress/single-herbs-properties-table.php"
    "blocks/myprogress/single-herbs-safety-dispensing.php"
    "blocks/myprogress/single-herbs-actions-indications.php"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_bensky_chapter_four.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

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

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/bensky-chapter-four-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

echo "Moodle detectado: $MOODLE_ROOT"
echo "PHP CLI detectado: $PHP_BIN"
echo "Descargando atlas de Bensky (capítulos 4, 17, 18 y 19) desde $REVISION..."

for relative_path in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative_path"
    if [[ "$relative_path" != "blocks/myprogress/single-herbs-bensky-chapter-four.php" \
        && "$relative_path" != "blocks/myprogress/single-herbs-bensky-chapter-seventeen.php" \
        && "$relative_path" != "blocks/myprogress/single-herbs-bensky-chapter-eighteen.php" \
        && "$relative_path" != "blocks/myprogress/single-herbs-bensky-chapter-nineteen.php" \
        && "$relative_path" != "blocks/myprogress/single-herbs-safety-dispensing.php" \
        && "$relative_path" != "blocks/myprogress/single-herbs-actions-indications.php" \
        && "$relative_path" != "blocks/myprogress/single-herb-actions-indications-supplement.json" \
        && "$relative_path" != "blocks/myprogress/single-herb-safety-supplement.json" \
        && ! -f "$target" ]]; then
        echo "ERROR: no se encontró el archivo activo $target"
        exit 1
    fi
    mkdir -p "$TEMP_DIR/$(dirname "$relative_path")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative_path" -o "$TEMP_DIR/$relative_path"
done

"$PHP_BIN" -l "$TEMP_DIR/blocks/myprogress/single-herbs-study-guide.php" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/blocks/myprogress/single-herbs-bensky-chapter-four.php" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/blocks/myprogress/single-herbs-bensky-chapter-seventeen.php" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/blocks/myprogress/single-herbs-bensky-chapter-eighteen.php" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/blocks/myprogress/single-herbs-bensky-chapter-nineteen.php" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/blocks/myprogress/single-herbs-properties-table.php" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/blocks/myprogress/single-herbs-safety-dispensing.php" >/dev/null
"$PHP_BIN" -l "$TEMP_DIR/blocks/myprogress/single-herbs-actions-indications.php" >/dev/null
python3 -m json.tool "$TEMP_DIR/blocks/myprogress/single-herb-card-index.json" >/dev/null
python3 -m json.tool "$TEMP_DIR/blocks/myprogress/single-herb-properties.json" >/dev/null
python3 -m json.tool "$TEMP_DIR/blocks/myprogress/single-herb-actions-indications-supplement.json" >/dev/null
python3 -m json.tool "$TEMP_DIR/blocks/myprogress/single-herb-safety-supplement.json" >/dev/null

CHAPTER_FILE="$TEMP_DIR/blocks/myprogress/single-herbs-bensky-chapter-four.php"
CHAPTER_SEVENTEEN_FILE="$TEMP_DIR/blocks/myprogress/single-herbs-bensky-chapter-seventeen.php"
CHAPTER_EIGHTEEN_FILE="$TEMP_DIR/blocks/myprogress/single-herbs-bensky-chapter-eighteen.php"
CHAPTER_NINETEEN_FILE="$TEMP_DIR/blocks/myprogress/single-herbs-bensky-chapter-nineteen.php"
INDEX_FILE="$TEMP_DIR/blocks/myprogress/single-herb-card-index.json"
PROPERTIES_FILE="$TEMP_DIR/blocks/myprogress/single-herb-properties.json"
ACTIONS_INDICATIONS_DATA_FILE="$TEMP_DIR/blocks/myprogress/single-herb-actions-indications-supplement.json"
SAFETY_SUPPLEMENT_FILE="$TEMP_DIR/blocks/myprogress/single-herb-safety-supplement.json"
PROPERTIES_TABLE_FILE="$TEMP_DIR/blocks/myprogress/single-herbs-properties-table.php"
SAFETY_FILE="$TEMP_DIR/blocks/myprogress/single-herbs-safety-dispensing.php"
ACTIONS_INDICATIONS_FILE="$TEMP_DIR/blocks/myprogress/single-herbs-actions-indications.php"
GUIDE_FILE="$TEMP_DIR/blocks/myprogress/single-herbs-study-guide.php"

grep -Fq "Board guideline atlas: 21 official herbs" "$CHAPTER_FILE"
grep -Fq "Aristolochia warning" "$CHAPTER_FILE"
grep -Fq "single-herbs-bensky-chapter-four.php" "$GUIDE_FILE"
grep -Fq "focus' => 'bensky4'" "$GUIDE_FILE"
grep -Fq "single-herbs-properties__sort-button" "$GUIDE_FILE"
grep -Fq "data-properties-sort" "$PROPERTIES_TABLE_FILE"
grep -Fq "data-properties-category-filter" "$PROPERTIES_TABLE_FILE"
grep -Fq "focus === 'safety_dispensing'" "$GUIDE_FILE"
grep -Fq "single-herbs-safety-dispensing.php" "$GUIDE_FILE"
grep -Fq "data-safety-category-filter" "$SAFETY_FILE"
grep -Fq "data-safety-topic-filter" "$SAFETY_FILE"
grep -Fq "single-herb-safety-supplement.json" "$SAFETY_FILE"
for language in en es; do
    rendered_safety="$TEMP_DIR/safety-dispensing-$language.html"
    "$PHP_BIN" -r '
        define("MOODLE_INTERNAL", true);
        function s($value) { return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8"); }
        class moodle_url {
            public function __construct($path = "", $params = []) {}
            public function out($escaped = true) { return "#"; }
        }
        $ises = $argv[2] === "es";
        include $argv[1];
    ' "$SAFETY_FILE" "$language" > "$rendered_safety"
    rendered_safety_count="$(grep -c '<tr data-safety-row' "$rendered_safety")"
    rendered_safety_question_count="$(grep -c 'data-safety-question>' "$rendered_safety")"
    if [[ "$rendered_safety_count" -ne 304 ]] || grep -Fq '>—<' "$rendered_safety"; then
        echo "ERROR: la tabla de precauciones $language no contiene 304 filas completas"
        exit 1
    fi
    if [[ "$rendered_safety_question_count" -ne 20 ]]; then
        echo "ERROR: la unidad de precauciones $language no contiene 20 preguntas y respuestas"
        exit 1
    fi
done
grep -Fq '<strong>304</strong> complete safety rows' "$TEMP_DIR/safety-dispensing-en.html"
grep -Fq '<strong>304</strong> filas de seguridad completas' "$TEMP_DIR/safety-dispensing-es.html"
grep -Fq 'Safety and dispensing recall questions' "$TEMP_DIR/safety-dispensing-en.html"
grep -Fq 'Preguntas de repaso de seguridad y dispensación' "$TEMP_DIR/safety-dispensing-es.html"
grep -Fq 'data-safety-open-all' "$SAFETY_FILE"
grep -Fq 'data-safety-close-all' "$SAFETY_FILE"
python3 - "$TEMP_DIR/safety-dispensing-en.html" "$TEMP_DIR/safety-dispensing-es.html" <<'PY'
import sys

for rendered_path in sys.argv[1:]:
    rendered = open(rendered_path, encoding='utf-8').read()
    questions_at = rendered.index('id="single-herbs-safety-questions"')
    topics_at = rendered.index('class="single-herbs-safety__topics"')
    table_at = rendered.index('class="single-herbs-properties__table-wrap"')
    if not questions_at < topics_at < table_at:
        raise SystemExit('ERROR: las preguntas de seguridad deben aparecer antes de los temas y la tabla')
PY
grep -Fq "focus === 'actions_indications'" "$GUIDE_FILE"
grep -Fq "single-herbs-actions-indications.php" "$GUIDE_FILE"
grep -Fq "data-ai-category-filter" "$ACTIONS_INDICATIONS_FILE"
grep -Fq "data-ai-sort" "$ACTIONS_INDICATIONS_FILE"
grep -Fq "Restricted to the 304 herbs in the official guideline" "$ACTIONS_INDICATIONS_FILE"
grep -Fq "single-herb-actions-indications-supplement.json" "$ACTIONS_INDICATIONS_FILE"
if grep -Eq "Study profile|Not structured|<th scope=\"col\">.*(Text|Texto)" "$ACTIONS_INDICATIONS_FILE"; then
    echo "ERROR: la tabla de acciones e indicaciones todavía contiene la columna editorial de texto"
    exit 1
fi
for language in en es; do
    rendered_actions="$TEMP_DIR/actions-indications-$language.html"
    "$PHP_BIN" -r '
        define("MOODLE_INTERNAL", true);
        function s($value) { return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8"); }
        $ises = $argv[2] === "es";
        include $argv[1];
    ' "$ACTIONS_INDICATIONS_FILE" "$language" > "$rendered_actions"
    rendered_group_count="$(grep -c '<tbody class="single-herbs-ai__group"' "$rendered_actions")"
    if [[ "$rendered_group_count" -ne 304 ]] || grep -Fq '>—<' "$rendered_actions"; then
        echo "ERROR: la tabla $language no contiene 304 hierbas completas"
        exit 1
    fi
done
grep -Fq '<strong>0</strong> missing' "$TEMP_DIR/actions-indications-en.html"
grep -Fq '<strong>0</strong> faltantes' "$TEMP_DIR/actions-indications-es.html"
grep -Fq "single-herbs-bensky-chapter-seventeen.php" "$GUIDE_FILE"
grep -Fq "focus' => 'bensky17'" "$GUIDE_FILE"
grep -Fq "single-herbs-bensky-chapter-eighteen.php" "$GUIDE_FILE"
grep -Fq "focus' => 'bensky18'" "$GUIDE_FILE"
grep -Fq "single-herbs-bensky-chapter-nineteen.php" "$GUIDE_FILE"
grep -Fq "focus' => 'bensky19'" "$GUIDE_FILE"
grep -Fq "single-herbs-guide__unified-questions" "$GUIDE_FILE"
grep -Fq "Earlier Yang questions and newer Bensky review questions" "$GUIDE_FILE"
grep -Fq "Open all answers" "$GUIDE_FILE"
grep -Fq "data-category-roster-open" "$GUIDE_FILE"
grep -Fq "data-guideline-roster-special" "$GUIDE_FILE"
grep -Fq "single-herbs-guide__roster-empty" "$GUIDE_FILE"
grep -Fq "single-herbs-properties__category" "$GUIDE_FILE"
grep -Fq "Categoría" "$PROPERTIES_TABLE_FILE"
if grep -Eq "data-properties-filter|single-herbs-properties__status|>Estado<|>Status<" "$PROPERTIES_TABLE_FILE"; then
    echo "ERROR: la tabla maestra todavía contiene controles o columnas de estado"
    exit 1
fi
grep -Fq "Source-chapter atlas: 13 monographs" "$CHAPTER_SEVENTEEN_FILE"
grep -Fq "Source-chapter atlas: 17 obsolete substances" "$CHAPTER_NINETEEN_FILE"
grep -Fq "max(\$lastdisplaystudyguidenumber, 34)" "$GUIDE_FILE"

CORE_COUNT="$(sed -n '/^\$b4groups = \[/,/^\$b4annex = \[/p' "$CHAPTER_FILE" | grep -c "\['name' =>")"
ANNEX_COUNT="$(sed -n '/^\$b4annex = \[/,/^\$b4comparisons = \[/p' "$CHAPTER_FILE" | grep -c "\['name' =>")"
QUESTION_COUNT="$(sed -n '/^\$b4questions = \[/,/^\];/p' "$CHAPTER_FILE" | grep -c '^    \[')"
SPECIAL_ROUTE_COUNT="$(grep -c '"specialFocus": "bensky4"' "$INDEX_FILE")"
CHAPTER_SEVENTEEN_MONOGRAPH_COUNT="$(sed -n '/^\$b17herbs = \[/,/^\$b17questions = \[/p' "$CHAPTER_SEVENTEEN_FILE" | grep -c "\['name' =>")"
CHAPTER_SEVENTEEN_QUESTION_COUNT="$(sed -n '/^\$b17questions = \[/,/^\];/p' "$CHAPTER_SEVENTEEN_FILE" | grep -c '^    \[')"
CHAPTER_SEVENTEEN_ROUTE_COUNT="$(grep -c '"specialFocus": "bensky17"' "$INDEX_FILE")"
CHAPTER_EIGHTEEN_MONOGRAPH_COUNT="$(sed -n '/^\$b18herbs = \[/,/^\$b18officialherbs = /p' "$CHAPTER_EIGHTEEN_FILE" | grep -c "\['name' =>")"
CHAPTER_EIGHTEEN_QUESTION_COUNT="$(sed -n '/^\$b18questions = \[/,/^\];/p' "$CHAPTER_EIGHTEEN_FILE" | grep -c '^    \[')"
CHAPTER_EIGHTEEN_ROUTE_COUNT="$(grep -c '"specialFocus": "bensky18"' "$INDEX_FILE")"
CHAPTER_NINETEEN_TOXICITY_COUNT="$(sed -n '/^\$b19toxicity = \[/,/^\$b19endangered = \[/p' "$CHAPTER_NINETEEN_FILE" | grep -c "'name' =>")"
CHAPTER_NINETEEN_ENDANGERED_COUNT="$(sed -n '/^\$b19endangered = \[/,/^\$b19questions = \[/p' "$CHAPTER_NINETEEN_FILE" | grep -c "'name' =>")"
CHAPTER_NINETEEN_QUESTION_COUNT="$(sed -n '/^\$b19questions = \[/,/^\];/p' "$CHAPTER_NINETEEN_FILE" | grep -c '^    \[')"

if [[ "$CORE_COUNT" -ne 21 || "$ANNEX_COUNT" -ne 9 || "$QUESTION_COUNT" -ne 14 ]]; then
    echo "ERROR: conteos inesperados: core=$CORE_COUNT anexo=$ANNEX_COUNT preguntas=$QUESTION_COUNT"
    exit 1
fi
if [[ "$SPECIAL_ROUTE_COUNT" -lt 21 ]]; then
    echo "ERROR: se esperaban al menos 21 rutas directas y se detectaron $SPECIAL_ROUTE_COUNT"
    exit 1
fi
if [[ "$CHAPTER_SEVENTEEN_MONOGRAPH_COUNT" -ne 13 || "$CHAPTER_SEVENTEEN_QUESTION_COUNT" -ne 12 ]]; then
    echo "ERROR: conteos inesperados del capítulo 17: monografías=$CHAPTER_SEVENTEEN_MONOGRAPH_COUNT preguntas=$CHAPTER_SEVENTEEN_QUESTION_COUNT"
    exit 1
fi
if [[ "$CHAPTER_SEVENTEEN_ROUTE_COUNT" -ne 1 ]]; then
    echo "ERROR: se esperaba 1 ruta directa al capítulo 17 y se detectaron $CHAPTER_SEVENTEEN_ROUTE_COUNT"
    exit 1
fi
if [[ "$CHAPTER_EIGHTEEN_MONOGRAPH_COUNT" -ne 13 || "$CHAPTER_EIGHTEEN_QUESTION_COUNT" -ne 12 ]]; then
    echo "ERROR: conteos inesperados del capítulo 18: monografías=$CHAPTER_EIGHTEEN_MONOGRAPH_COUNT preguntas=$CHAPTER_EIGHTEEN_QUESTION_COUNT"
    exit 1
fi
if [[ "$CHAPTER_EIGHTEEN_ROUTE_COUNT" -ne 2 ]]; then
    echo "ERROR: se esperaban 2 rutas directas al capítulo 18 y se detectaron $CHAPTER_EIGHTEEN_ROUTE_COUNT"
    exit 1
fi
if [[ "$CHAPTER_NINETEEN_TOXICITY_COUNT" -ne 12 || "$CHAPTER_NINETEEN_ENDANGERED_COUNT" -ne 5 || "$CHAPTER_NINETEEN_QUESTION_COUNT" -ne 15 ]]; then
    echo "ERROR: conteos inesperados del capítulo 19: toxicidad=$CHAPTER_NINETEEN_TOXICITY_COUNT especies_protegidas=$CHAPTER_NINETEEN_ENDANGERED_COUNT preguntas=$CHAPTER_NINETEEN_QUESTION_COUNT"
    exit 1
fi

python3 - "$INDEX_FILE" "$PROPERTIES_FILE" "$ACTIONS_INDICATIONS_DATA_FILE" "$SAFETY_SUPPLEMENT_FILE" <<'PY'
import json
import sys

index = json.load(open(sys.argv[1], encoding='utf-8'))
properties = json.load(open(sys.argv[2], encoding='utf-8'))
actions_supplement = json.load(open(sys.argv[3], encoding='utf-8'))
safety_supplement = json.load(open(sys.argv[4], encoding='utf-8'))
index_rows = index.get('herbs', [])
property_rows = properties.get('herbs', [])
index_names = [row.get('name') for row in index_rows]
property_names = [row.get('name') for row in property_rows]

if len(index_rows) != 304 or len(set(index_names)) != 304:
    raise SystemExit('ERROR: el índice no contiene 304 hierbas oficiales únicas')
if len(property_rows) != 304 or len(set(property_names)) != 304:
    raise SystemExit('ERROR: la tabla maestra no contiene 304 hierbas oficiales únicas')
if set(index_names) != set(property_names):
    raise SystemExit('ERROR: el índice y la tabla maestra no contienen el mismo roster')
if any(
    not (row.get('flavor') or {}).get('en')
    or not (row.get('flavor') or {}).get('es')
    or not (row.get('temperature') or {}).get('en')
    or not (row.get('temperature') or {}).get('es')
    or not (row.get('channels') or {}).get('en')
    or not (row.get('channels') or {}).get('es')
    or not row.get('dose')
    for row in property_rows
):
    raise SystemExit('ERROR: existen hierbas con propiedades, meridianos o dosis incompletos')
if any(not all((row.get('benskyCategory') or {}).get(key) for key in ('code', 'en', 'es')) for row in index_rows):
    raise SystemExit('ERROR: existen hierbas sin categoría Bensky completa')
gu_ya = next((row for row in index_rows if row.get('name') == 'Gu Ya'), None)
if not gu_ya or gu_ya.get('latin') != 'Setariae (Oryzae) Fructus germinatus':
    raise SystemExit('ERROR: Gu Ya no coincide con Appendix A')
if len({row['benskyCategory']['code'] for row in index_rows}) != 33:
    raise SystemExit('ERROR: se esperaban 33 categorías Bensky')
supplement_rows = actions_supplement.get('herbs', {})
if len(supplement_rows) != 106:
    raise SystemExit(f'ERROR: se esperaban 106 hierbas Bensky suplementarias y se detectaron {len(supplement_rows)}')
if not set(supplement_rows).issubset(set(index_names)):
    raise SystemExit('ERROR: el suplemento incluye hierbas fuera del guideline oficial')
if any(
    not row.get('en') or not row.get('es')
    or any(not isinstance(pair, list) or len(pair) != 2 or not all(pair) for pair in row['en'])
    or any(not isinstance(pair, list) or len(pair) != 2 or not all(pair) for pair in row['es'])
    for row in supplement_rows.values()
):
    raise SystemExit('ERROR: el suplemento contiene acciones o indicaciones incompletas')
safety_category_rows = safety_supplement.get('categoryCautions', {})
category_codes = {row['benskyCategory']['code'] for row in index_rows}
if set(safety_category_rows) != category_codes:
    raise SystemExit('ERROR: el suplemento de precauciones no cubre exactamente las 33 categorías Bensky')
if any(not all((row.get(key) or '').strip() for key in ('en', 'es')) for row in safety_category_rows.values()):
    raise SystemExit('ERROR: existen precauciones de categoría no bilingües')
safety_herb_rows = safety_supplement.get('herbs', {})
if not set(safety_herb_rows).issubset(set(index_names)):
    raise SystemExit('ERROR: el suplemento de precauciones incluye hierbas fuera del guideline')
safety_rows = [row for row in property_rows if all((row.get('safetyNote') or {}).get(key) for key in ('en', 'es'))]
if len(safety_rows) < 180:
    raise SystemExit(f'ERROR: solo se detectaron {len(safety_rows)} notas de seguridad bilingües estructuradas')
PY

mkdir -p "$BACKUP_DIR"
for relative_path in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative_path"
    mkdir -p "$BACKUP_DIR/$(dirname "$relative_path")"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP_DIR/$relative_path"
        metadata_source="$target"
    else
        metadata_source="$MOODLE_ROOT/blocks/myprogress/single-herbs-study-guide.php"
    fi
    owner_group="$(stat -c '%u:%g' "$metadata_source")"
    file_mode="$(stat -c '%a' "$metadata_source")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$file_mode" "$TEMP_DIR/$relative_path" "$target"
done

"$PHP_BIN" -l "$MOODLE_ROOT/blocks/myprogress/single-herbs-study-guide.php" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/blocks/myprogress/single-herbs-bensky-chapter-four.php" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/blocks/myprogress/single-herbs-bensky-chapter-seventeen.php" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/blocks/myprogress/single-herbs-bensky-chapter-eighteen.php" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/blocks/myprogress/single-herbs-bensky-chapter-nineteen.php" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/blocks/myprogress/single-herbs-properties-table.php" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/blocks/myprogress/single-herbs-safety-dispensing.php" >/dev/null
"$PHP_BIN" -l "$MOODLE_ROOT/blocks/myprogress/single-herbs-actions-indications.php" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/blocks/myprogress/single-herb-card-index.json" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/blocks/myprogress/single-herb-properties.json" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/blocks/myprogress/single-herb-actions-indications-supplement.json" >/dev/null
python3 -m json.tool "$MOODLE_ROOT/blocks/myprogress/single-herb-safety-supplement.json" >/dev/null

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    "$PHP_BIN" "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "ATLAS DE BENSKY INSTALADO CORRECTAMENTE"
echo "Capítulo 4: 21 perfiles oficiales · 9 entradas de anexo · 14 preguntas"
echo "Capítulo 17: 1 perfil oficial · 13 monografías · 12 preguntas"
echo "Capítulo 18: 2 perfiles oficiales · 13 monografías · 12 preguntas"
echo "Capítulo 19: 0 perfiles oficiales · 12 sustancias por toxicidad · 5 de especies protegidas · 15 preguntas"
echo "Preguntas: formato unificado Yang + Bensky y controles abrir/cerrar en las 34 tarjetas"
echo "Pop-up por tarjeta: listado oficial filtrado, buscador, enlaces directos y estado vacío verificados"
echo "Tabla maestra: 304 hierbas oficiales · 33 categorías Bensky · orden y filtro por categoría verificados"
echo "Seguridad y dispensación: 20 preguntas bilingües arriba · 304 filas completas · 0 guiones"
echo "Acciones e indicaciones: 304 hierbas del guideline · 0 faltantes · columna editorial eliminada"
echo "Rutas directas verificadas: capítulo 4=$SPECIAL_ROUTE_COUNT · capítulo 17=$CHAPTER_SEVENTEEN_ROUTE_COUNT · capítulo 18=$CHAPTER_EIGHTEEN_ROUTE_COUNT"
echo "Respaldo: $BACKUP_DIR"
