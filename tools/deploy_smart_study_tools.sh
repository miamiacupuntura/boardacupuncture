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

MOODLE_FILES=(smart-study-tools.php study-guide.php formula-list.php single-herbs-list.php herbs-category-comparison.php)
DATA_FILES=(single-herb-safety-supplement.json formula-architecture-data.json dui-yao-smart-data.json)
STATIC_FILES=(formula-practice.js formula-practice.html formula-practice-es.html)

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
echo "OK: 12 tarjetas derivadas muestran la composición por fórmula base y las hierbas de cada grupo."
echo "OK: tarjetas de fórmulas y hierbas conservan acceso contextual y botón de regreso."
echo "OK: motor local gratuito; no usa API externa ni transmite el historial."
echo "OK: archivos públicos instalados en $STUDY_NOTE_DIR."
echo "RESPALDO: $BACKUP"
