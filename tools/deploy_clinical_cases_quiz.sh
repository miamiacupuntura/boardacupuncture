#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
EXPECTED_VERSION="2026110671"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_clinical_cases_quiz.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi
for command in curl php python3 install; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command"
        exit 1
    }
done
[[ -f "$MOODLE_ROOT/config.php" ]] || {
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
}

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/clinical-cases-quiz-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

FILES=(
    "blocks/menu/block_menu.php"
    "blocks/menu/herbology-board.php"
    "blocks/menu/herbology-clinical-cases.js"
    "blocks/menu/herbology-exam-history.php"
    "blocks/menu/herbology-simulation.js"
    "blocks/menu/styles.css"
    "blocks/menu/version.php"
    "blocks/menu/db/install.xml"
    "blocks/menu/db/upgrade.php"
    "blocks/myprogress/formulas-study-guide-data.json"
    "blocks/myprogress/study-guide.php"
    "mod/scorm/studytopic/clinical_cases_extract.py"
    "mod/scorm/studytopic/scorm_word_export.py"
    "mod/scorm/studytopic/scorm_word_inventory.php"
)

for relative in "${FILES[@]}"; do
    staged="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$staged")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$staged"
done

for relative in "${FILES[@]}"; do
    if [[ "$relative" == *.php ]]; then
        php -l "$TEMP_DIR/$relative" >/dev/null
    fi
done
python3 -m py_compile \
    "$TEMP_DIR/mod/scorm/studytopic/clinical_cases_extract.py" \
    "$TEMP_DIR/mod/scorm/studytopic/scorm_word_export.py"
grep -Fq "\$plugin->version   = $EXPECTED_VERSION;" "$TEMP_DIR/blocks/menu/version.php"
grep -Fq "VERSION=\"$EXPECTED_VERSION\"" "$TEMP_DIR/blocks/menu/db/install.xml"
grep -Fq "upgrade_block_savepoint(true, $EXPECTED_VERSION, 'menu')" "$TEMP_DIR/blocks/menu/db/upgrade.php"
grep -Fq 'id="quiz-clinical-cases"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-clinical-cases-quiz' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-clinical-retry-errors' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'filemtime($clinicalscriptpath)' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'clinicalcases'" "$TEMP_DIR/blocks/menu/herbology-exam-history.php"
grep -Fq "'completedlabel'" "$TEMP_DIR/blocks/menu/herbology-exam-history.php"
grep -Fq 'data-clinical-history-list' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'TARGET_TOPIC = "handbook-of-oriental-medicine. clinical cases"' \
    "$TEMP_DIR/mod/scorm/studytopic/clinical_cases_extract.py"
grep -Fq '"patternEs"' "$TEMP_DIR/blocks/myprogress/formulas-study-guide-data.json"
grep -Fq 'Diagnostic reasoning' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'clinicalquestionfallback' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'array_replace(' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'PINYIN_I_OCR' "$TEMP_DIR/mod/scorm/studytopic/clinical_cases_extract.py"

formula_line="$(grep -n -m1 'id="quiz-formula-categories"' "$TEMP_DIR/blocks/menu/herbology-board.php" | cut -d: -f1)"
clinical_line="$(grep -n -m1 'id="quiz-clinical-cases"' "$TEMP_DIR/blocks/menu/herbology-board.php" | cut -d: -f1)"
domain_line="$(grep -n -m1 'id="quiz-domains"' "$TEMP_DIR/blocks/menu/herbology-board.php" | cut -d: -f1)"
if (( formula_line >= clinical_line || clinical_line >= domain_line )); then
    echo "ERROR: el bloque «Clinical Cases» no está entre «Formula Quiz» y «Quiz by Domain»." >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR"
default_owner_group="$(stat -c '%u:%g' "$MOODLE_ROOT/config.php")"
for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    staged="$TEMP_DIR/$relative"
    mkdir -p "$(dirname "$target")" "$BACKUP_DIR/$(dirname "$relative")"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP_DIR/$relative"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
    else
        owner_group="$default_owner_group"
        mode="644"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$staged" "$target"
done

run_web_command() {
    if [[ "$(id -u)" -eq 0 ]] && command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- "$@"
    else
        "$@"
    fi
}

DATAROOT="$(run_web_command php -r \
    'define("CLI_SCRIPT", true); require $argv[1]; global $CFG; echo $CFG->dataroot;' \
    "$MOODLE_ROOT/config.php")"
CLINICAL_BANK="$DATAROOT/board-domain-banks/herbology-clinical-cases.json"
if [[ -f "$CLINICAL_BANK" ]]; then
    mkdir -p "$BACKUP_DIR/moodledata"
    cp -a "$CLINICAL_BANK" "$BACKUP_DIR/moodledata/herbology-clinical-cases.json"
fi

EXTRACTION_OUTPUT="$(run_web_command python3 \
    "$MOODLE_ROOT/mod/scorm/studytopic/clinical_cases_extract.py")"
echo "$EXTRACTION_OUTPUT"
python3 -c '
import json, sys
payload = json.loads(sys.argv[1])
assert payload["exams"] > 0 and payload["questions"] > 0
assert payload["diagnosticFeedback"] == payload["questions"]
' "$EXTRACTION_OUTPUT"

if [[ -f "$MOODLE_ROOT/admin/cli/upgrade.php" ]]; then
    run_web_command php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
fi
if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    run_web_command php "$MOODLE_ROOT/admin/cli/purge_caches.php"
fi

echo "OK: Quiz de casos clínicos instalado con razonamiento diagnóstico verificado."
echo "Respaldo: $BACKUP_DIR"
echo "Prueba: /blocks/menu/herbology-board.php?track=quiz#quiz-clinical-cases"
