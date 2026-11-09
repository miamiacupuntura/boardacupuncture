#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
EXPECTED_VERSION="2026110685"
EXPECTED_MYPROGRESS_VERSION="2026110812"

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
    "blocks/myprogress/herbology-library-navigation.php"
    "blocks/myprogress/study-guide.php"
    "blocks/myprogress/version.php"
    "study-guide/comparisons/dui-yao.html"
    "study-guide/comparisons/dui-yao-es.html"
    "study-guide/comparisons/herb-comparison-bank.html"
    "study-guide/comparisons/herb-comparison-bank-es.html"
    "study-guide/comparisons/formula-navigator.html"
    "study-guide/comparisons/formula-navigator-es.html"
    "study-guide/comparisons/families.html"
    "study-guide/comparisons/families-es.html"
    "study-guide/comparisons/name-traps.html"
    "study-guide/comparisons/name-traps-es.html"
    "study-guide/comparisons/herb-families.html"
    "study-guide/comparisons/herb-families-es.html"
    "study-guide/comparisons/safety.html"
    "study-guide/comparisons/safety-es.html"
    "study-guide/comparisons/formula-name-traps.html"
    "study-guide/comparisons/formula-name-traps-es.html"
    "study-guide/comparisons/misleading-formula-names.html"
    "study-guide/comparisons/misleading-formula-names-es.html"
    "study-guide/comparisons/advanced-name-traps.html"
    "study-guide/comparisons/advanced-name-traps-es.html"
    "study-guide/comparisons/supplemental-herb-families.html"
    "study-guide/comparisons/supplemental-herb-families-es.html"
    "study-guide/comparisons/patterns-tips-maciocia.html"
    "study-guide/comparisons/patterns-tips-maciocia-es.html"
    "study-guide/comparisons/most-important-formulas.html"
    "study-guide/comparisons/most-important-formulas-es.html"
    "study-guide/comparisons/formula-theory.html"
    "study-guide/comparisons/formula-theory-es.html"
    "study-guide/comparisons/herbal-incompatibilities.html"
    "study-guide/comparisons/herbal-incompatibilities-es.html"
    "study-guide/comparisons/secondary-actions.html"
    "study-guide/comparisons/secondary-actions-es.html"
    "study-guide/comparisons/formulas.html"
    "study-guide/comparisons/formulas-es.html"
    "study-guide/comparisons/pathologies.html"
    "study-guide/comparisons/pathologies-es.html"
    "study-guide/comparisons/patterns.html"
    "study-guide/comparisons/patterns-es.html"
    "study-guide/comparisons/formula-practice.html"
    "study-guide/comparisons/formula-practice-es.html"
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
grep -Fq "\$plugin->version = $EXPECTED_MYPROGRESS_VERSION;" "$TEMP_DIR/blocks/myprogress/version.php"
grep -Fq 'board-hub__action--board-library' "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq '\$boardlevelactions = \$libraryaction . \$studyguideaction' "$TEMP_DIR/blocks/menu/block_menu.php"
if grep -Fq '\$actions .= '\''<a class="board-hub__action comparisons-dashboard-link"'\''' "$TEMP_DIR/blocks/menu/block_menu.php"; then
    echo "ERROR: Library todavía aparece en la tarjeta School Level." >&2
    exit 1
fi
grep -Fq '.board-hub__action--board-library {' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq 'height: 38px;' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq "'← Board Level Activities'" "$TEMP_DIR/blocks/myprogress/herbology-library-navigation.php"
grep -Fq "'← Actividades Board Level'" "$TEMP_DIR/blocks/myprogress/herbology-library-navigation.php"
grep -Fq "\$languageurl = clone \$PAGE->url;" "$TEMP_DIR/blocks/myprogress/herbology-library-navigation.php"
grep -Fq "<?php echo \$ises ? 'English' : 'Español'; ?>" "$TEMP_DIR/blocks/myprogress/herbology-library-navigation.php"
if grep -Fq "data-library-step-back" "$TEMP_DIR/blocks/myprogress/herbology-library-navigation.php"; then
    echo "ERROR: el botón todavía usa navegación por historial." >&2
    exit 1
fi
if grep -Fq "herbology-library-context__routes" "$TEMP_DIR/blocks/myprogress/herbology-library-navigation.php"; then
    echo "ERROR: la barra antigua de navegación todavía está presente." >&2
    exit 1
fi
grep -Fq "'Each block contains the actual options that belong to that activity.'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq 'journey-option--primary' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq '$chapterlisturl->out(false)' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq '$boardpracticeurl->out(false)' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq '$boardquizurl->out(false)' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq '$boardexamsurl->out(false)' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq '$boardsimulationurl->out(false)' "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "Questions with visible answers" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "Passive review by domain" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "Clinical Cases exam" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "100 questions · 150 minutes" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Open all Study Guides'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'← Board Level Activities'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'← Actividades Board Level'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "href=\"<?php echo \$languageurl->out(false); ?>\"" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "<?php echo \$ises ? 'English' : 'Español'; ?>" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "\$libraryurl->out(false)" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "\$chapter['title'] . ' Study Guide'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Guía de estudio: ' . \$chapter['title']" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Open complete Herbology Practice'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Single Herbs Practice'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Formulas Practice'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Domains Practice'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Open all Quizzes'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Open all Exams'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq "'Open all Simulations'" "$TEMP_DIR/blocks/myprogress/study-guide.php"
grep -Fq 'class="herb-board__page-navigation"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'← Board Level Activities'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'← Actividades Board Level'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "\$libraryurl->out(false)" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__tab herb-board__tab--language' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "href=\"<?php echo \$languageurl->out(false); ?>\"" "$TEMP_DIR/blocks/menu/herbology-board.php"
if grep -Fq 'data-herbology-step-back' "$TEMP_DIR/blocks/menu/herbology-board.php"; then
    echo "ERROR: el regreso todavía usa el historial del navegador." >&2
    exit 1
fi
grep -Fq '← Board Level Activities' "$TEMP_DIR/study-guide/comparisons/dui-yao.html"
grep -Fq '>Español</a>' "$TEMP_DIR/study-guide/comparisons/dui-yao.html"
grep -Fq '← Actividades Board Level' "$TEMP_DIR/study-guide/comparisons/dui-yao-es.html"
grep -Fq '>English</a>' "$TEMP_DIR/study-guide/comparisons/dui-yao-es.html"
if grep -Eq 'Study path|Ruta de estudio|Library home|Herbology Practice|comparison-return\.js' \
        "$TEMP_DIR/study-guide/comparisons/dui-yao.html" \
        "$TEMP_DIR/study-guide/comparisons/dui-yao-es.html"; then
    echo "ERROR: Duì Yào todavía contiene navegación adicional." >&2
    exit 1
fi
for comparison in "$TEMP_DIR"/study-guide/comparisons/*.html; do
    if grep -Fq 'comparison-topbar' "$comparison"; then
        grep -Eq '← Board Level Activities|← Actividades Board Level' "$comparison"
        grep -Eq '>Español</a>|>English</a>' "$comparison"
        comparison_navigation_count="$(grep -o 'class="comparison-back"' "$comparison" | wc -l | tr -d ' ')"
        if [ "$comparison_navigation_count" -ne 2 ]; then
            echo "ERROR: ${comparison#"$TEMP_DIR/"} debe mostrar exactamente dos controles globales." >&2
            exit 1
        fi
        if grep -Eq 'Back to Dashboard|← Dashboard|practice-study-route|Library home' "$comparison"; then
            echo "ERROR: navegación adicional encontrada en ${comparison#"$TEMP_DIR/"}." >&2
            exit 1
        fi
    fi
done
grep -Fq 'id="quiz-clinical-cases"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__exam-model--clinical' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '<strong>3</strong>' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '<strong>63–100</strong>' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-clinical-cases-quiz' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'Study by Domain'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'← Back to Domains'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '<details open>' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-clinical-retry-errors' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-clinical-incorrect-count' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'incorrectReview.open = false' "$TEMP_DIR/blocks/menu/herbology-clinical-cases.js"
grep -Fq 'data-clinical-restart' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-clinical-finish' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'finishExamEarly' "$TEMP_DIR/blocks/menu/herbology-clinical-cases.js"
grep -Fq 'data-seconds-per-question="90"' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-clinical-timer-value' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'startTimer(activeQuestions.length)' "$TEMP_DIR/blocks/menu/herbology-clinical-cases.js"
grep -Fq 'data-clinical-analysis' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'renderAnalysis' "$TEMP_DIR/blocks/menu/herbology-clinical-cases.js"
grep -Fq "'Incorrect answers'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "quiz.dataset.ofWord" "$TEMP_DIR/blocks/menu/herbology-clinical-cases.js"
grep -Fq 'restartExam' "$TEMP_DIR/blocks/menu/herbology-clinical-cases.js"
grep -Fq 'shuffledQuestions' "$TEMP_DIR/blocks/menu/herbology-clinical-cases.js"
grep -Fq 'startAttempt(questions, true)' "$TEMP_DIR/blocks/menu/herbology-clinical-cases.js"
grep -Fq 'Restart and shuffle' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'linearincomplete' "$TEMP_DIR/blocks/menu/herbology-exam-history.php"
grep -Fq 'adaptiveincomplete' "$TEMP_DIR/blocks/menu/herbology-exam-history.php"
grep -Fq 'saveAttempt(`${mode}incomplete`)' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'filemtime($clinicalscriptpath)' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'clinicalcases'" "$TEMP_DIR/blocks/menu/herbology-exam-history.php"
grep -Fq "'clinicalincomplete'" "$TEMP_DIR/blocks/menu/herbology-exam-history.php"
grep -Fq "herb-board__exam-history--combined" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'clinical' => 'clinicalcases'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'clinicalincomplete' => 'clinicalincomplete'" "$TEMP_DIR/blocks/menu/herbology-board.php"
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
if (( formula_line >= clinical_line )); then
    echo "ERROR: el bloque «Clinical Cases» debe aparecer después de «Formula Quiz»." >&2
    exit 1
fi
if grep -Fq "'Quiz by Domain'" "$TEMP_DIR/blocks/menu/herbology-board.php"; then
    echo "ERROR: Domains todavía aparece dentro del módulo Quiz." >&2
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

grep -Fq "'← Board Level Activities'" "$MOODLE_ROOT/blocks/myprogress/herbology-library-navigation.php"
if grep -Fq "data-library-step-back" "$MOODLE_ROOT/blocks/myprogress/herbology-library-navigation.php"; then
    echo "ERROR: el servidor conserva el regreso por historial." >&2
    exit 1
fi
if grep -Fq "herbology-library-context__routes" "$MOODLE_ROOT/blocks/myprogress/herbology-library-navigation.php"; then
    echo "ERROR: el servidor conserva la barra antigua de navegación." >&2
    exit 1
fi

for relative in "${FILES[@]}"; do
    if [[ "$relative" == study-guide/comparisons/*.html ]]; then
        installed_comparison="$MOODLE_ROOT/$relative"
        grep -Eq '← Board Level Activities|← Actividades Board Level' "$installed_comparison"
        grep -Eq '>Español</a>|>English</a>' "$installed_comparison"
        installed_navigation_count="$(grep -o 'class="comparison-back"' "$installed_comparison" | wc -l | tr -d ' ')"
        if [ "$installed_navigation_count" -ne 2 ]; then
            echo "ERROR: $relative no quedó instalado con exactamente dos controles globales." >&2
            exit 1
        fi
        if grep -Eq 'Back to Dashboard|← Dashboard|practice-study-route|Library home' "$installed_comparison"; then
            echo "ERROR: $relative conserva navegación global antigua después de instalarse." >&2
            exit 1
        fi
    fi
done
echo "OK: navegación Board Level instalada y comprobada en las páginas comparativas."

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
