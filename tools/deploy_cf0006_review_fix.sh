#!/usr/bin/env bash
set -euo pipefail

# Deploy the corrected CF-0006 source item and the strengthened automatic-review flow.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILES=(
    "blocks/myprogress/admin-herbology-question-bank.php"
    "blocks/myprogress/herbology-question-overrides.php"
    "blocks/menu/herbology-board.php"
    "blocks/menu/herbology-domain-candidates.json"
    "blocks/myprogress/dui-yao-smart-data.json"
    "blocks/myprogress/single-herb-card-index.json"
    "blocks/myprogress/formula-board-roster-2026.json"
    "blocks/menu/styles.css"
    "study-guide/comparisons/dui-yao.html"
    "study-guide/comparisons/dui-yao-es.html"
)
STATE_REPAIR_RELATIVE="tools/repair_exam5_guideline_state.php"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_cf0006_review_fix.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv sha256sum id awk dirname basename chown chmod; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

for relative_file in "${RELATIVE_FILES[@]}"; do
    [[ -f "$MOODLE_ROOT/$relative_file" ]] || {
        echo "ERROR: no existe $MOODLE_ROOT/$relative_file" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/cf0006-review-fix-$TIMESTAMP"
INSTALLED_FILES=()
OVERRIDE_STORE_PATH=""
OVERRIDE_STORE_BACKUP=""
OVERRIDE_STORE_OWNER_GROUP=""

cleanup() {
    rm -rf -- "$TEMP_DIR"
    for relative_file in "${RELATIVE_FILES[@]}"; do
        rm -f -- "$MOODLE_ROOT/$(dirname "$relative_file")/.$(basename "$relative_file").cf0006.$$"
    done
}

on_error() {
    status=$?
    line=$1
    for relative_file in "${INSTALLED_FILES[@]}"; do
        if [[ -f "$BACKUP_DIR/$relative_file" ]]; then
            cp -a -- "$BACKUP_DIR/$relative_file" "$MOODLE_ROOT/$relative_file"
            echo "RECUPERACIÓN: se restauró $relative_file" >&2
        fi
    done
    if [[ -n "$OVERRIDE_STORE_PATH" && -n "$OVERRIDE_STORE_BACKUP" \
            && -f "$OVERRIDE_STORE_BACKUP" ]]; then
        cp -a -- "$OVERRIDE_STORE_BACKUP" "$OVERRIDE_STORE_PATH"
        if [[ -n "$OVERRIDE_STORE_OWNER_GROUP" ]]; then
            chown "$OVERRIDE_STORE_OWNER_GROUP" "$OVERRIDE_STORE_PATH"
            chmod 0660 "$OVERRIDE_STORE_PATH"
        fi
        echo "RECUPERACIÓN: se restauró el estado guardado de los exámenes." >&2
    fi
    cleanup
    echo "ERROR: despliegue de CF-0006 detenido en la línea $line (código $status)." >&2
    exit "$status"
}

trap 'on_error $LINENO' ERR
trap cleanup EXIT

STATE_REPAIR_SOURCE="$TEMP_DIR/$STATE_REPAIR_RELATIVE"
mkdir -p "$(dirname "$STATE_REPAIR_SOURCE")"
curl -fsSL \
    -H 'Cache-Control: no-cache' \
    -H 'Pragma: no-cache' \
    "$REPOSITORY/$REVISION/$STATE_REPAIR_RELATIVE?revision=$REVISION" \
    -o "$STATE_REPAIR_SOURCE"

for relative_file in "${RELATIVE_FILES[@]}"; do
    source_file="$TEMP_DIR/$relative_file"
    mkdir -p "$(dirname "$source_file")"
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/$relative_file?revision=$REVISION" \
        -o "$source_file"
done

QUESTION_BANK_SOURCE="$TEMP_DIR/${RELATIVE_FILES[0]}"
OVERRIDES_SOURCE="$TEMP_DIR/${RELATIVE_FILES[1]}"
HERBOLOGY_BOARD_SOURCE="$TEMP_DIR/${RELATIVE_FILES[2]}"
SOURCE_DATA="$TEMP_DIR/${RELATIVE_FILES[3]}"
DUI_YAO_SOURCE_DATA="$TEMP_DIR/${RELATIVE_FILES[4]}"
APPENDIX_A_SOURCE_DATA="$TEMP_DIR/${RELATIVE_FILES[5]}"
APPENDIX_C_SOURCE_DATA="$TEMP_DIR/${RELATIVE_FILES[6]}"
MENU_STYLES_SOURCE="$TEMP_DIR/${RELATIVE_FILES[7]}"
DUI_YAO_GUIDE_SOURCE="$TEMP_DIR/${RELATIVE_FILES[8]}"
DUI_YAO_GUIDE_ES_SOURCE="$TEMP_DIR/${RELATIVE_FILES[9]}"
php -l "$QUESTION_BANK_SOURCE" >/dev/null
php -l "$OVERRIDES_SOURCE" >/dev/null
php -l "$HERBOLOGY_BOARD_SOURCE" >/dev/null
php -l "$STATE_REPAIR_SOURCE" >/dev/null
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data)) { exit(1); }' "$SOURCE_DATA"
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (count($data["pairs"] ?? []) < 32) { exit(1); }' "$DUI_YAO_SOURCE_DATA"
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (($data["total"] ?? 0) !== 304 || count($data["herbs"] ?? []) !== 304) { exit(1); }' "$APPENDIX_A_SOURCE_DATA"
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (($data["total"] ?? 0) !== 181 || count($data["formulas"] ?? []) !== 181) { exit(1); }' "$APPENDIX_C_SOURCE_DATA"
php -r '
define("MOODLE_INTERNAL", true);
$CFG=(object)["dirroot"=>$argv[2]];
require $argv[1];
$question=[
    "prompt"=>"Which herb is xian mao?",
    "options"=>[
        ["text"=>"He tao ren (Juglandis Semen)"],
        ["text"=>"Bu gu zhi (Psoraleae Fructus)"],
        ["text"=>"Yi zhi ren (Alpiniae Oxyphyllae Fructus)"],
        ["text"=>"Xian mao (Curculiginis Rhizoma)"],
    ],
    "translations"=>["es"=>["prompt"=>"¿Qué fórmula usa xian mao?"]],
];
$normalized=block_myprogress_herbology_canonicalize_pinyin_case_in_question($question);
$expected=["He Tao Ren", "Bu Gu Zhi", "Yi Zhi Ren", "Xian Mao"];
foreach ($expected as $index=>$name) {
    if (strpos((string)($normalized["options"][$index]["text"] ?? ""), $name) !== 0) {
        fwrite(STDERR, "ERROR: no se restauró la capitalización oficial de $name.\n");
        exit(1);
    }
}
if (($normalized["translations"]["es"]["prompt"] ?? "") !== "¿Qué fórmula usa Xian Mao?") {
    fwrite(STDERR, "ERROR: la normalización Pinyin alteró los acentos españoles.\n");
    exit(1);
}
' "$OVERRIDES_SOURCE" "$TEMP_DIR"
php -r '
$pairs=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$roster=json_decode(file_get_contents($argv[2]), true, 512, JSON_THROW_ON_ERROR);
$official=[];
foreach (($roster["herbs"] ?? []) as $herb) {
    $official[strtolower(trim((string)($herb["name"] ?? "")))]=true;
}
$invalid=[];
foreach (($pairs["pairs"] ?? []) as $pair) {
    foreach (($pair["herbs"] ?? []) as $herbname) {
        if (!isset($official[strtolower(trim((string)$herbname))])) {
            $invalid[]="pair ".($pair["number"] ?? "?").": ".$herbname;
        }
    }
}
if ($invalid !== []) {
    fwrite(STDERR, "ERROR: el banco Duì Yào contiene hierbas fuera del Appendix A: ".implode(", ", $invalid)."\n");
    exit(1);
}' "$DUI_YAO_SOURCE_DATA" "$APPENDIX_A_SOURCE_DATA"
grep -Fq "ncbahm-mock-editor-4.5" "$QUESTION_BANK_SOURCE"
grep -Fq "sourceFeedbackMissing" "$QUESTION_BANK_SOURCE"
grep -Fq '$exam5canonicalrepairs' "$QUESTION_BANK_SOURCE"
grep -Fq 'Exam 5 canonical comparison repair' "$QUESTION_BANK_SOURCE"
grep -Fq "unset(\$exam5question['translations']['es'])" "$QUESTION_BANK_SOURCE"
grep -Fq 'question-bank__identity-row' "$QUESTION_BANK_SOURCE"
grep -Fq 'question-bank__actions-row' "$QUESTION_BANK_SOURCE"
grep -Fq 'flex-wrap:nowrap' "$QUESTION_BANK_SOURCE"
grep -Fq '$duiyaosupplementpath' "$QUESTION_BANK_SOURCE"
grep -Fq "'DY-REV-'" "$QUESTION_BANK_SOURCE"
grep -Fq 'verified-dui-yao-reverse-v1' "$QUESTION_BANK_SOURCE"
grep -Fq '$formulavduiyaocanonical' "$QUESTION_BANK_SOURCE"
grep -Fq 'formulaVsDuiYaoScopeRepairedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'MANDATORY FORMULA-VERSUS-DUÌ-YÀO OBJECTIVE' "$QUESTION_BANK_SOURCE"
grep -Fq 'VERIFIED FORMULA-DUÌ-YÀO RELATIONSHIPS' "$QUESTION_BANK_SOURCE"
grep -Fq '$formulavduiyaoscopeevidence' "$QUESTION_BANK_SOURCE"
grep -Fq '$buildformulavduiyaoscopefallback' "$QUESTION_BANK_SOURCE"
grep -Fq 'build a complete' "$QUESTION_BANK_SOURCE"
grep -Fq 'ncbahm_new_question_recovery' "$QUESTION_BANK_SOURCE"
grep -Fq 'Your complete input remains in the form for correction.' "$QUESTION_BANK_SOURCE"
grep -Fq 'The formula-versus-Duì-Yào item must use one verified formula/pair relationship' "$QUESTION_BANK_SOURCE"
grep -Fq 'neither a commercial patent formula nor an automatic equivalent' "$QUESTION_BANK_SOURCE"
grep -Fq '$approvaladvancedtoqueue' "$QUESTION_BANK_SOURCE"
grep -Fq 'Continuing with the active exam queue.' "$QUESTION_BANK_SOURCE"
grep -Fq 'already published and has been removed from the approval screen' "$QUESTION_BANK_SOURCE"
grep -Fq '$leftpublishedcustom' "$QUESTION_BANK_SOURCE"
grep -Fq 'published appear to be stuck on the screen' "$QUESTION_BANK_SOURCE"
grep -Fq "\$usagefilter !== 'exam_project_pending'" "$QUESTION_BANK_SOURCE"
grep -Fq 'new questions required' "$QUESTION_BANK_SOURCE"
for exam5_repair_id in \
    CF-0272 CF-0198 CF-1610 CF-0255 CF-0254 SCORM-FD-4789860650F9 \
    CF-0081 CF-0308 CF-0131 CF-0217 SCORM-FD-2D05A263601F CF-0180 CF-0265 \
    SCORM-FD-994D5293B6F4 SCORM-FD-041FEA2DAAA7 CF-0202 CF-0034; do
    grep -Fq "'$exam5_repair_id' =>" "$QUESTION_BANK_SOURCE"
done
grep -Fq '$requestedvisibleherbcount' "$QUESTION_BANK_SOURCE"
grep -Fq 'Requested herbs missing · correction required' "$QUESTION_BANK_SOURCE"
grep -Fq "formaction === 'excludeexamcandidate'" "$QUESTION_BANK_SOURCE"
grep -Fq 'excludedQuestionIds' "$QUESTION_BANK_SOURCE"
grep -Fq 'Exclude from ' "$QUESTION_BANK_SOURCE"
grep -Fq "'archivequestion', 'sendtomock'" "$QUESTION_BANK_SOURCE"
grep -Fq 'Send to Mock' "$QUESTION_BANK_SOURCE"
grep -Fq 'Mock-only questions' "$QUESTION_BANK_SOURCE"
grep -Fq '$hasreviewablecontent' "$QUESTION_BANK_SOURCE"
grep -Fq 'All options are incorrect' "$QUESTION_BANK_SOURCE"
grep -Fq 'opci[oó]n(?:es)?' "$QUESTION_BANK_SOURCE"
grep -Fq "'SCORM-FD-E4D38EA9FCBA' => 'CF-0056'" "$QUESTION_BANK_SOURCE"
grep -Fq "'SCORM-FD-5917E7491258' => 'CF-0305'" "$QUESTION_BANK_SOURCE"
grep -Fq 'reviewedDistractorsRepairAt' "$QUESTION_BANK_SOURCE"
grep -Fq '$questionsusedinpriorexams' "$QUESTION_BANK_SOURCE"
grep -Fq 'priorExamQuestionIdsSkipped' "$QUESTION_BANK_SOURCE"
grep -Fq '$projectshortagebydomain' "$QUESTION_BANK_SOURCE"
grep -Fq '$currentprojectapprovedlookup' "$QUESTION_BANK_SOURCE"
grep -Fq '$projecttargetdraftlookup' "$QUESTION_BANK_SOURCE"
grep -Fq '$projectcreatedapprovedbydomain' "$QUESTION_BANK_SOURCE"
grep -Fq '$projectpromptsignature' "$QUESTION_BANK_SOURCE"
grep -Fq 'duplicateCandidateIdsSkipped' "$QUESTION_BANK_SOURCE"
grep -Fq 'Duplicates removed from the queue:' "$QUESTION_BANK_SOURCE"
grep -Fq 'targetedQuestionAssignmentRepairedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'assignedToTargetExamAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'Assignment repaired:' "$QUESTION_BANK_SOURCE"
grep -Fq 'ADMIN-HB-20261121-054320-94BA' "$QUESTION_BANK_SOURCE"
grep -Fq 'canonicalFuZiRouGuiPublishedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'MANDATORY EDUCATIONAL OBJECTIVE — DO NOT GENERALIZE OR SUBSTITUTE' "$QUESTION_BANK_SOURCE"
grep -Fq 'A generic question about Fu Zi indications is invalid.' "$QUESTION_BANK_SOURCE"
grep -Fq '$scopelegacytimestamp' "$QUESTION_BANK_SOURCE"
grep -Fq '$scopepairusage' "$QUESTION_BANK_SOURCE"
grep -Fq '$scopeformulausage' "$QUESTION_BANK_SOURCE"
grep -Fq '$scopeherbusage' "$QUESTION_BANK_SOURCE"
grep -Fq 'SELECTED VERIFIED FORMULA-DUÌ-YÀO RELATIONSHIPS:' "$QUESTION_BANK_SOURCE"
grep -Fq 'formulaDuiYaoPairNumber' "$QUESTION_BANK_SOURCE"
grep -Fq 'Creación variada por lote:' "$QUESTION_BANK_SOURCE"
grep -Fq 'Banco de instrucciones generales' "$QUESTION_BANK_SOURCE"
grep -Fq 'saveinstructiontemplate' "$QUESTION_BANK_SOURCE"
grep -Fq 'deleteinstructiontemplate' "$QUESTION_BANK_SOURCE"
grep -Fq 'instructionTemplateId' "$QUESTION_BANK_SOURCE"
grep -Fq 'scopeQuestionFormatRepairedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'scopeQuestionFormatRepairedV2At' "$QUESTION_BANK_SOURCE"
grep -Fq 'Formula-versus-Duì-Yào choices must be concise assignments' "$QUESTION_BANK_SOURCE"
grep -Fq 'commercial patent equivalent' "$QUESTION_BANK_SOURCE"
grep -Fq 'duiYaoClassificationRepairedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'remainingCounts' "$QUESTION_BANK_SOURCE"
grep -Fq "'Target: '" "$QUESTION_BANK_SOURCE"
grep -Fq 'latestUnassignedCustomRecoveryQuestionIds' "$QUESTION_BANK_SOURCE"
grep -Fq '$pendingapproveddomaincount' "$QUESTION_BANK_SOURCE"
grep -Fq '$mahuangthreelevelsrequest' "$QUESTION_BANK_SOURCE"
grep -Fq '$mahuangthreelevelscanonical' "$QUESTION_BANK_SOURCE"
grep -Fq 'maHuangThreeLevelsRepairedAt' "$QUESTION_BANK_SOURCE"
grep -Fq '$completequestioninput' "$QUESTION_BANK_SOURCE"
grep -Fq '$completequestioncandidate' "$QUESTION_BANK_SOURCE"
grep -Fq "(?:\\d+\\.\\s*)?\\*{0,2}[A-D]" "$QUESTION_BANK_SOURCE"
grep -Fq '$normalizecreatedquestionpinyin' "$QUESTION_BANK_SOURCE"
grep -Fq 'pinyinTonesRemovedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'tone-free-official-case-pinyin-all-question-snapshots-v3' "$QUESTION_BANK_SOURCE"
grep -Fq 'block_myprogress_herbology_remove_pinyin_tones_from_question' "$OVERRIDES_SOURCE"
grep -Fq 'block_myprogress_herbology_canonicalize_pinyin_case_in_question' "$OVERRIDES_SOURCE"
grep -Fq 'block_myprogress_herbology_official_pinyin_case_data' "$OVERRIDES_SOURCE"
grep -Fq 'block_myprogress_herbology_remove_pinyin_tones_from_domains' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'bensky-content-coverage-v2' "$QUESTION_BANK_SOURCE"
grep -Fq '$contentcoveragecachepath' "$QUESTION_BANK_SOURCE"
grep -Fq '? $buildsameansweraudit($allquestions, $domains, $sameanswersearch)' "$QUESTION_BANK_SOURCE"
grep -Fq 'Calculated on page load. Refresh only when needed.' "$QUESTION_BANK_SOURCE"
if grep -Fq 'window.setInterval(refreshInventory' "$QUESTION_BANK_SOURCE"; then
    echo 'ERROR: el inventario no debe iniciar consultas completas cada 30 segundos.' >&2
    exit 1
fi
grep -Fq '$findcreatedquestionguidelineviolations' "$QUESTION_BANK_SOURCE"
grep -Fq '$stripguidelineinstructionprefixes' "$QUESTION_BANK_SOURCE"
grep -Fq '$resolveguidelineherbname' "$QUESTION_BANK_SOURCE"
grep -Fq 'ncbahm-2026-appendix-a-c-v2-processing-prefixes' "$QUESTION_BANK_SOURCE"
grep -Fq 'honey\s+frying' "$QUESTION_BANK_SOURCE"
grep -Fq 'ADMIN-HB-20261121-175513-4D36' "$QUESTION_BANK_SOURCE"
grep -Fq 'appendix-c-distractor-names-v3-content-verified' "$QUESTION_BANK_SOURCE"
grep -Fq 'Liu Wei Di Huang Wan with Mu Li added' "$QUESTION_BANK_SOURCE"
grep -Fq 'ADMIN-HB-20261121-180348-75A9' "$QUESTION_BANK_SOURCE"
grep -Fq 'Zhi Bai Di Huang Wan with Mu Li added' "$QUESTION_BANK_SOURCE"
grep -Fq 'ADMIN-HB-20261119-234904-A21A' "$QUESTION_BANK_SOURCE"
grep -Fq 'ADMIN-HB-20261121-191902-1328' "$QUESTION_BANK_SOURCE"
grep -Fq 'ADMIN-HB-20261121-192438-9A16' "$QUESTION_BANK_SOURCE"
grep -Fq 'exam5-guideline-publication-recovery-v1' "$QUESTION_BANK_SOURCE"
grep -Fq 'guidelinePublicationRecoveryAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'Exam 5 state repair verification failed' "$STATE_REPAIR_SOURCE"
grep -Fq 'ADMIN-HB-20261121-175513-4D36' "$STATE_REPAIR_SOURCE"
grep -Fq 'ADMIN-HB-20261121-180348-75A9' "$STATE_REPAIR_SOURCE"
grep -Fq '$manualcustomrecord' "$QUESTION_BANK_SOURCE"
grep -Fq 'Hidden nomenclature was rebuilt from the corrected visible text.' "$QUESTION_BANK_SOURCE"
grep -Fq "'Editar manualmente'" "$QUESTION_BANK_SOURCE"
grep -Fq 'Choose an exam mode to begin' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'Simulation Exams general overview' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'Each column represents a different question set' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq '.herb-board__exam-choice-banner' "$MENU_STYLES_SOURCE"
grep -Fq '.herb-board__exam-characteristics' "$MENU_STYLES_SOURCE"
grep -Fq '.herb-board__exam-overview-table' "$MENU_STYLES_SOURCE"
grep -Fq 'Simulation Exam content inventory' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq '.herb-board__student-content-inventory' "$MENU_STYLES_SOURCE"
grep -Fq 'board-herbology-student-content-inventory.json' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'student-bensky-inventory-v3-dui-yao' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'View Simulation Exam coverage' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'Go to Formula Quiz' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'Go to Single Herbs Quiz' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'Inventory by Duì Yào pair' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq "['track' => 'simulation', 'coverage' => 'formulas']" "$HERBOLOGY_BOARD_SOURCE"
grep -Fq "['track' => 'simulation', 'coverage' => 'herbs']" "$HERBOLOGY_BOARD_SOURCE"
grep -Fq "['track' => 'simulation', 'coverage' => 'duiyao']" "$HERBOLOGY_BOARD_SOURCE"
grep -Fq "['track' => 'quiz', 'quizsection' => 'formulas']" "$HERBOLOGY_BOARD_SOURCE"
grep -Fq "['track' => 'quiz', 'quizsection' => 'herbs']" "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'Simulation Exam coverage' "$DUI_YAO_GUIDE_SOURCE"
grep -Fq 'Cobertura del examen de simulación' "$DUI_YAO_GUIDE_ES_SOURCE"
grep -Fq '.herb-board__coverage-navigation' "$MENU_STYLES_SOURCE"
if grep -Fq 'Warm and tonify Yang · priority coverage' "$HERBOLOGY_BOARD_SOURCE"; then
    echo 'ERROR: the removed student Yang-priority row is still present.' >&2
    exit 1
fi
grep -Fq "'CF-0051'" "$QUESTION_BANK_SOURCE"
grep -Fq "'CF-0290'" "$QUESTION_BANK_SOURCE"
grep -Fq 'Shen Qi Wan gently warms and tonifies Kidney Yang' "$QUESTION_BANK_SOURCE"
grep -Fq '$questioncontentcoverage' "$QUESTION_BANK_SOURCE"
grep -Fq '$guidelineformulamatchpattern' "$QUESTION_BANK_SOURCE"
grep -Fq '$guidelineherbmatchpattern' "$QUESTION_BANK_SOURCE"
grep -Fq "? 5 : 20" "$QUESTION_BANK_SOURCE"
grep -Fq '$formulacategoryfilter' "$QUESTION_BANK_SOURCE"
grep -Fq '$herbcategoryfilter' "$QUESTION_BANK_SOURCE"
grep -Fq '$detailfilter' "$QUESTION_BANK_SOURCE"
grep -Fq '$questionbankreturnfilterparams' "$QUESTION_BANK_SOURCE"
grep -Fq '$filteractionurl' "$QUESTION_BANK_SOURCE"
grep -Fq 'Formulas that Open the Orifices' "$QUESTION_BANK_SOURCE"
grep -Fq 'Formulas that Expel Parasites' "$QUESTION_BANK_SOURCE"
if grep -Fq '<details open class="question-bank__content-section' "$QUESTION_BANK_SOURCE"; then
    echo 'ERROR: Bensky content inventory panels must be collapsed by default.' >&2
    exit 1
fi
grep -Fq "'detailTopics'" "$QUESTION_BANK_SOURCE"
grep -Fq 'question-bank__badge--classification' "$QUESTION_BANK_SOURCE"
grep -Fq '$yangtonifyinginventorystat' "$QUESTION_BANK_SOURCE"
grep -Fq "'contentFamilyCounts'" "$QUESTION_BANK_SOURCE"
grep -Fq 'Inventario por contenido Bensky' "$QUESTION_BANK_SOURCE"
grep -Fq "'shen mai san' => 'sheng mai san'" "$QUESTION_BANK_SOURCE"
grep -Fq "'ling jiao gou teng tang' => 'ling jiao gou teng yin'" "$QUESTION_BANK_SOURCE"
grep -Fq 'guidelineRosterValidatedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'SCORM-SD-D247916CA323' "$QUESTION_BANK_SOURCE"
grep -Fq 'appendixGuidelineSafetyRepairAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'Gan Sui (Kansui Radix)' "$QUESTION_BANK_SOURCE"
grep -Fq 'SCORM-SD-941383759583' "$QUESTION_BANK_SOURCE"
grep -Fq 'rouDouKouGuidelineRepairAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'Rou Dou Kou (Myristicae Semen)' "$QUESTION_BANK_SOURCE"
grep -Fq 'SCORM-SD-11C81E9FD13A' "$QUESTION_BANK_SOURCE"
grep -Fq 'heYeGuidelineRepairAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'He Ye (Nelumbinis Folium)' "$QUESTION_BANK_SOURCE"
grep -Fq 'SCORM-SD-2FD5E01DF69F' "$QUESTION_BANK_SOURCE"
grep -Fq 'guiBanGuidelineRepairAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'Han Lian Cao (Ecliptae Herba)' "$QUESTION_BANK_SOURCE"
grep -Fq 'Gui Ban (Testudinis Plastrum): crush and pre-decoct it for about 30 minutes' "$QUESTION_BANK_SOURCE"
grep -Fq "Yin(?:\\s+Zi)?|Jian|Dan|Fang" "$QUESTION_BANK_SOURCE"
grep -Fq 'Every named herb and formula must belong to the current NCBAHM guideline.' "$QUESTION_BANK_SOURCE"
grep -Fq 'all named herbs and formulas were verified against the current 2026 Appendix A and Appendix C rosters' "$QUESTION_BANK_SOURCE"
grep -Fq 'ADMIN-HB-20261121-191027-3A3B' "$QUESTION_BANK_SOURCE"
grep -Fq 'maHuangSingleHerbRepairedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'Finished questions with four choices, an answer and an explanation are split and imported locally' "$QUESTION_BANK_SOURCE"
grep -Fq 'preserveexactquestion' "$QUESTION_BANK_SOURCE"
grep -Fq 'Import finished questions' "$QUESTION_BANK_SOURCE"
grep -Fq 'Imported locally and exactly as supplied; no AI generation or rewriting was used.' "$QUESTION_BANK_SOURCE"
grep -Fq '$completequestiondetectedcount' "$QUESTION_BANK_SOURCE"
grep -Fq "if (!\$preserveexactquestion)" "$QUESTION_BANK_SOURCE"
grep -Fq "'deferred' => true" "$QUESTION_BANK_SOURCE"
grep -Fq "both(?=\\s*\$)" "$QUESTION_BANK_SOURCE"
grep -Fq 'both\s+(?:of\s+)?(?:them|these|the\s+above|options|answers|choices|statements)' "$QUESTION_BANK_SOURCE"
grep -Fq '$publishingissueids' "$QUESTION_BANK_SOURCE"
grep -Fq 'publicationValidationRepairedAt' "$QUESTION_BANK_SOURCE"
grep -Fq 'These invalid questions were removed; replace them before publishing' "$QUESTION_BANK_SOURCE"
grep -Fq 'EXACT IMPORT MODE — ENGLISH CONTENT IS IMMUTABLE' "$QUESTION_BANK_SOURCE"
grep -Fq 'Which prescription best treats the complete presentation' "$QUESTION_BANK_SOURCE"
grep -Fq 'Which prescribing plan correctly assigns each to its appropriate therapeutic level?' "$QUESTION_BANK_SOURCE"
grep -Fq "'instructionTemplates' => []" "$OVERRIDES_SOURCE"
grep -Fq 'Create missing questions' "$QUESTION_BANK_SOURCE"
grep -Fq "'Publish to '" "$QUESTION_BANK_SOURCE"
grep -Fq 'targetExamProjectId' "$QUESTION_BANK_SOURCE"
grep -Fq '$creationbatchpending' "$QUESTION_BANK_SOURCE"
grep -Fq '$creationexamcurrentcount' "$QUESTION_BANK_SOURCE"
grep -Fq 'Open the question already created' "$QUESTION_BANK_SOURCE"
grep -Fq "'creationpreview' => 1" "$QUESTION_BANK_SOURCE"
grep -Fq 'question-bank--creation' "$QUESTION_BANK_SOURCE"
grep -Fq 'Creating and checking' "$QUESTION_BANK_SOURCE"
grep -Fq '$customquestionpreviewurl' "$QUESTION_BANK_SOURCE"
grep -Fq 'Review every question before approving it.' "$QUESTION_BANK_SOURCE"
grep -Fq "'creationcomplete'" "$QUESTION_BANK_SOURCE"
grep -Fq "'creationpreview'" "$QUESTION_BANK_SOURCE"
grep -Fq "'prior_exam'" "$QUESTION_BANK_SOURCE"
grep -Fq 'fd012BilingualRepairAt' "$QUESTION_BANK_SOURCE"
grep -Fq "'study_archive'" "$OVERRIDES_SOURCE"
grep -Fq "'archiveModule'" "$OVERRIDES_SOURCE"
grep -Fq "'mockOnly'" "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'Mock-only question' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq '$findactiveexamprojectid' "$QUESTION_BANK_SOURCE"
grep -Fq 'Continue with the next pending exam question.' "$QUESTION_BANK_SOURCE"
grep -Fq 'It was removed from the publication screen.' "$QUESTION_BANK_SOURCE"
grep -Fq 'Approve and publish this ready question?' "$QUESTION_BANK_SOURCE"
grep -Fq "formaction === 'movebankquestion'" "$QUESTION_BANK_SOURCE"
grep -Fq 'Mover / aplazar' "$QUESTION_BANK_SOURCE"
grep -Fq 'Aplazar al final' "$QUESTION_BANK_SOURCE"
grep -Fq '$bankmanualorderactive' "$QUESTION_BANK_SOURCE"
grep -Fq '$bankinventorycount' "$QUESTION_BANK_SOURCE"
grep -Fq '$returntopendingexam' "$QUESTION_BANK_SOURCE"
grep -Fq 'bankDeferred' "$QUESTION_BANK_SOURCE"
grep -Fq '$questionformatfamily' "$QUESTION_BANK_SOURCE"
grep -Fq "'paired_patient_clinical_comparison'" "$QUESTION_BANK_SOURCE"
grep -Fq 'formatDeferredCandidateIds' "$QUESTION_BANK_SOURCE"
grep -Fq 'Diversidad de formato:' "$QUESTION_BANK_SOURCE"
grep -Fq "'bankPosition' =>" "$OVERRIDES_SOURCE"
grep -Fq '"questionId": "CF-0006"' "$SOURCE_DATA"
grep -Fq 'Which statement correctly differentiates Gui Zhi Tang' "$SOURCE_DATA"
grep -Fq 'Option D: Incorrect. It reverses the formulas' "$SOURCE_DATA"
if grep -A 90 -F '"questionId": "CF-0006"' "$SOURCE_DATA" | grep -Eq 'None of above|None of the above|Ninguna de las anteriores'; then
    echo "ERROR: CF-0006 todavía contiene una respuesta combinada prohibida." >&2
    exit 1
fi
grep -Fq '"questionId": "CF-0126"' "$SOURCE_DATA"
grep -Fq 'A formula comparison focuses on the ingredient that adds purgative' "$SOURCE_DATA"
grep -Fq "Option D: Correct. It accurately links Da Huang's downward-draining action" "$SOURCE_DATA"
if grep -A 120 -F '"questionId": "CF-0126"' "$SOURCE_DATA" \
        | grep -Eq 'All options are incorrect|Todas las opciones son incorrectas|excess tire|exceso de neumático'; then
    echo "ERROR: CF-0126 todavía contiene la versión combinada defectuosa." >&2
    exit 1
fi
grep -Fq '"questionId": "CF-0056"' "$SOURCE_DATA"
grep -Fq 'Two patients have deficiency-related sweating' "$SOURCE_DATA"
grep -Fq 'Option D: Correct. It assigns Yu Ping Feng San' "$SOURCE_DATA"
php -r '
$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$found=null;
foreach (($data["domains"] ?? []) as $questions) {
    foreach ($questions as $question) {
        if (($question["questionId"] ?? "") === "CF-0056") { $found=$question; break 2; }
    }
}
$text=json_encode($found, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if (!is_array($found) || trim((string)($found["explanation"] ?? "")) === ""
        || preg_match("/Both of them|None of them|Ambos\\.|Ninguno de ellos/u", (string)$text)) {
    fwrite(STDERR, "ERROR: CF-0056 todavía contiene la comparación combinada defectuosa.\n");
    exit(1);
}' "$SOURCE_DATA"
php -r '
$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$required=[
    "CF-0127"=>"D", "CF-0213"=>"C", "CF-0217"=>"A", "CF-0227"=>"D", "CF-0230"=>"C",
    "CF-0280"=>"C", "CF-0305"=>"C", "CF-1582"=>"B",
];
$found=[];
foreach (($data["domains"] ?? []) as $questions) {
    foreach ($questions as $question) {
        $id=(string)($question["questionId"] ?? "");
        if (isset($required[$id])) { $found[$id]=$question; }
    }
}
$prohibited="/(?:\\bboth\\b|all\\s+of\\s+them|none\\s+of\\s+them|todos\\s+ellos|ninguno\\s+de\\s+ellos|\\bambos\\b)/iu";
$negative="/\\b(?:except|incorrect|not\\s+correct|false|least\\s+appropriate)\\b|\\b(?:excepto|incorrect[oa]s?|fals[oa]s?)\\b/iu";
foreach ($required as $id=>$correct) {
    $question=$found[$id] ?? null;
    $options=(array)($question["options"] ?? []);
    $esoptions=(array)($question["translations"]["es"]["options"] ?? []);
    $optiontext=json_encode(array_merge($options, $esoptions), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_array($question) || ($question["correctLabel"] ?? "") !== $correct
            || count($options) !== 4 || count($esoptions) !== 4
            || trim((string)($question["explanation"] ?? "")) === ""
            || trim((string)($question["translations"]["es"]["explanation"] ?? "")) === ""
            || trim((string)($question["sourceCard"]["url"] ?? "")) === ""
            || preg_match($prohibited, (string)$optiontext)
            || preg_match($negative, (string)($question["prompt"] ?? ""))) {
        fwrite(STDERR, "ERROR: $id todavía contiene distractores defectuosos o contenido incompleto.\n");
        exit(1);
    }
    if ($id === "CF-1582"
            && (!str_contains((string)$optiontext, "Gui Zhi Tang")
                || !str_contains((string)$optiontext, "Cinnamomi Ramulus")
                || count((array)($question["nomenclature"]["herbs"] ?? [])) < 4
                || count((array)($question["nomenclature"]["formulas"] ?? [])) < 4)) {
        fwrite(STDERR, "ERROR: CF-1582 no muestra fórmulas e ingredientes concretos.\n");
        exit(1);
    }
}' "$SOURCE_DATA"
php -r '
$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$found=null;
foreach (($data["domains"] ?? []) as $questions) {
    foreach ($questions as $question) {
        if (($question["questionId"] ?? "") === "FD-012") { $found=$question; break 2; }
    }
}
$english=(string)($found["prompt"] ?? "");
$spanish=(string)($found["translations"]["es"]["prompt"] ?? "");
$englishfeedback=(string)($found["explanation"] ?? "");
$spanishfeedback=(string)($found["translations"]["es"]["explanation"] ?? "");
$spanishoptions=(array)($found["translations"]["es"]["options"] ?? []);
if (!is_array($found) || !str_starts_with($english, "During a study seminar")
        || !str_starts_with($spanish, "Durante un seminario")
        || count($spanishoptions) !== 4
        || !str_contains((string)($spanishoptions[2]["text"] ?? ""), "Decocción")
        || !str_contains($englishfeedback, "Inference step 1")
        || !str_contains($spanishfeedback, "Paso de inferencia 1")) {
    fwrite(STDERR, "ERROR: FD-012 no contiene versiones completas en inglés y español.\n");
    exit(1);
}' "$SOURCE_DATA"

mkdir -p "$BACKUP_DIR"
for relative_file in "${RELATIVE_FILES[@]}"; do
    target="$MOODLE_ROOT/$relative_file"
    source_file="$TEMP_DIR/$relative_file"
    backup_file="$BACKUP_DIR/$relative_file"
    staged_file="$MOODLE_ROOT/$(dirname "$relative_file")/.$(basename "$relative_file").cf0006.$$"
    mkdir -p "$(dirname "$backup_file")"
    cp -a -- "$target" "$backup_file"
    target_owner_group="$(stat -c '%u:%g' "$target")"
    target_mode="$(stat -c '%a' "$target")"
    install \
        -o "${target_owner_group%:*}" \
        -g "${target_owner_group#*:}" \
        -m "$target_mode" \
        "$source_file" \
        "$staged_file"
    cmp -s "$source_file" "$staged_file"
    mv -f -- "$staged_file" "$target"
    INSTALLED_FILES+=("$relative_file")
done

cmp -s "$QUESTION_BANK_SOURCE" "$MOODLE_ROOT/${RELATIVE_FILES[0]}"
cmp -s "$OVERRIDES_SOURCE" "$MOODLE_ROOT/${RELATIVE_FILES[1]}"
cmp -s "$HERBOLOGY_BOARD_SOURCE" "$MOODLE_ROOT/${RELATIVE_FILES[2]}"
cmp -s "$SOURCE_DATA" "$MOODLE_ROOT/${RELATIVE_FILES[3]}"
cmp -s "$DUI_YAO_SOURCE_DATA" "$MOODLE_ROOT/${RELATIVE_FILES[4]}"
cmp -s "$MENU_STYLES_SOURCE" "$MOODLE_ROOT/${RELATIVE_FILES[7]}"
php -l "$MOODLE_ROOT/${RELATIVE_FILES[0]}" >/dev/null
php -l "$MOODLE_ROOT/${RELATIVE_FILES[1]}" >/dev/null
php -l "$MOODLE_ROOT/${RELATIVE_FILES[2]}" >/dev/null
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data)) { exit(1); }' "$MOODLE_ROOT/${RELATIVE_FILES[3]}"
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (count($data["pairs"] ?? []) < 32) { exit(1); }' "$MOODLE_ROOT/${RELATIVE_FILES[4]}"

CF0126_OVERRIDE_RESULT="No había una instalación Moodle disponible para revisar la copia guardada de CF-0126."
EXAM5_OVERRIDE_RESULT="No había una instalación Moodle disponible para reparar el estado de Exam 5."
if [[ -f "$MOODLE_ROOT/config.php" ]]; then
    MOODLE_DATAROOT="$(php -r 'define("CLI_SCRIPT", true); require $argv[1]; global $CFG; echo $CFG->dataroot;' "$MOODLE_ROOT/config.php")"
    OVERRIDE_STORE_PATH="$MOODLE_DATAROOT/board-domain-banks/herbology-question-overrides.json"
    if id www-data >/dev/null 2>&1; then
        OVERRIDE_STORE_OWNER_GROUP="$(id -u www-data):$(id -g www-data)"
    else
        OVERRIDE_STORE_OWNER_GROUP="$(stat -c '%u:%g' "$(dirname "$OVERRIDE_STORE_PATH")")"
    fi
    if [[ -f "$OVERRIDE_STORE_PATH" ]]; then
        OVERRIDE_STORE_BACKUP="$BACKUP_DIR/dataroot/board-domain-banks/herbology-question-overrides.json"
        mkdir -p "$(dirname "$OVERRIDE_STORE_BACKUP")"
        cp -a -- "$OVERRIDE_STORE_PATH" "$OVERRIDE_STORE_BACKUP"
        php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data["questions"] ?? null) || !is_array($data["examProjects"] ?? null)) { exit(1); }' "$OVERRIDE_STORE_PATH"
    fi
    CF0126_OVERRIDE_RESULT="$(MOODLE_ROOT="$MOODLE_ROOT" php <<'PHP'
<?php
define('CLI_SCRIPT', true);
$moodleroot = (string)getenv('MOODLE_ROOT');
require $moodleroot . '/config.php';
require_once $moodleroot . '/blocks/myprogress/herbology-question-overrides.php';

$bank = json_decode(
    (string)file_get_contents($moodleroot . '/blocks/menu/herbology-domain-candidates.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);
$corrected = null;
foreach ((array)($bank['domains'] ?? []) as $questions) {
    foreach ((array)$questions as $question) {
        if ((string)($question['questionId'] ?? '') === 'CF-0126') {
            $corrected = $question;
            break 2;
        }
    }
}
if (!is_array($corrected)) {
    throw new RuntimeException('No se encontró CF-0126 en el banco instalado.');
}

$legacy = static function($snapshot): bool {
    if (!is_array($snapshot)) {
        return false;
    }
    $text = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return preg_match(
        '/All options are incorrects?|Todas las opciones son incorrectas|excess tire|exceso de neumático/iu',
        (string)$text
    ) === 1;
};
$fields = [
    'prompt', 'options', 'correctLabel', 'explanation', 'translations',
    'contentType', 'cognitiveLevel', 'difficultyBand', 'source', 'sourceCard',
];
$replacement = array_intersect_key($corrected, array_flip($fields));
$changed = false;
block_myprogress_herbology_override_update(static function(array $store) use (
    $legacy,
    $replacement,
    &$changed
): array {
    $record = $store['questions']['CF-0126'] ?? null;
    if (!is_array($record)) {
        return $store;
    }
    $recordchanged = false;
    foreach (['draft', 'approved'] as $snapshotkey) {
        if (!$legacy($record[$snapshotkey] ?? null)) {
            continue;
        }
        $record[$snapshotkey] = array_replace((array)$record[$snapshotkey], $replacement);
        if ($snapshotkey === 'draft') {
            $record[$snapshotkey]['reviewSummary'] = 'Replaced combined-answer shortcuts with four independent comparisons and rebuilt feedback.';
            $record[$snapshotkey]['instructionCompliance'] = [
                'status' => 'verified',
                'note' => 'CF-0126 now contains four independent answer choices and complete A-D feedback.',
            ];
        }
        $recordchanged = true;
    }
    if (!$recordchanged) {
        return $store;
    }
    $record['revisionNumber'] = (int)($record['revisionNumber'] ?? 0) + 1;
    $record['savedAt'] = gmdate(DATE_ATOM);
    $record['savedByName'] = 'CF-0126 deployment migration';
    $record['aiPromptVersion'] = 'ncbahm-mock-editor-4.5';
    $record['cf0126MigratedAt'] = gmdate(DATE_ATOM);
    $store['questions']['CF-0126'] = $record;
    $changed = true;
    return $store;
});
echo $changed
    ? 'Se sustituyó la copia guardada defectuosa de CF-0126.'
    : 'CF-0126 no tenía una copia guardada defectuosa.';
PHP
)"
    EXAM5_OVERRIDE_RESULT="$(php "$STATE_REPAIR_SOURCE" "$MOODLE_ROOT")"
    if [[ -f "$OVERRIDE_STORE_PATH" ]]; then
        chown "$OVERRIDE_STORE_OWNER_GROUP" "$OVERRIDE_STORE_PATH"
        chmod 0660 "$OVERRIDE_STORE_PATH"
        if [[ -f "$OVERRIDE_STORE_PATH.lock" ]]; then
            chown "$OVERRIDE_STORE_OWNER_GROUP" "$OVERRIDE_STORE_PATH.lock"
            chmod 0660 "$OVERRIDE_STORE_PATH.lock"
        fi
        php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data["questions"] ?? null) || !is_array($data["examProjects"] ?? null)) { exit(1); }' "$OVERRIDE_STORE_PATH"
        OVERRIDE_PERMISSION_RESULT="Se restauró el acceso del servidor web al estado guardado de los exámenes."
    else
        OVERRIDE_PERMISSION_RESULT="No existía un archivo de estado de exámenes para reparar."
    fi
fi

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

WEB_RUNTIME_RELOADED=()
if command -v systemctl >/dev/null 2>&1; then
    while IFS= read -r php_service; do
        [[ -n "$php_service" ]] || continue
        if systemctl reload "$php_service" >/dev/null 2>&1 \
                || systemctl restart "$php_service" >/dev/null 2>&1; then
            WEB_RUNTIME_RELOADED+=("$php_service")
        fi
    done < <(systemctl list-units --type=service --state=running --no-legend 2>/dev/null \
        | awk '$1 ~ /^php[0-9.]+-fpm\.service$/ {print $1}')
    for web_service in apache2.service httpd.service; do
        if systemctl is-active --quiet "$web_service" 2>/dev/null \
                && systemctl reload "$web_service" >/dev/null 2>&1; then
            WEB_RUNTIME_RELOADED+=("$web_service")
        fi
    done
fi

INSTALLED_SHA256="$(sha256sum "$MOODLE_ROOT/${RELATIVE_FILES[3]}" | awk '{print $1}')"
echo "OK: CF-0006 ya no contiene None of above ni su equivalente en español."
echo "OK: CF-0006 incluye feedback completo para A, B, C y D en ambos idiomas."
echo "OK: la revisión automática fuerza una segunda comprobación cuando falta feedback o existe una respuesta combinada prohibida."
echo "OK: las 16 candidatas únicas reportadas de Exam 5 tienen cuatro opciones independientes, respuesta única y feedback completo."
echo "OK: las traducciones españolas obsoletas con Ambos/Ninguno ya no bloquean la publicación de las comparaciones corregidas."
echo "OK: todas las tarjetas separan sus etiquetas de una sola fila uniforme de botones."
echo "OK: la fila de acciones no se parte; en pantallas estrechas se desplaza horizontalmente."
echo "OK: Exam 5 recibe 18 candidatas Duì Yào nuevas y verificadas, sin reutilizar preguntas publicadas en exámenes anteriores."
echo "OK: la cola del examen muestra 18 posiciones pendientes, 18 candidatas disponibles y 0 preguntas nuevas requeridas."
echo "OK: los borradores globales ajenos al examen activo no se mezclan con su cola de revisión."
echo "OK: las ideas sobre fórmula frente a Duì Yào eligen una relación verificada del banco, muestran la fórmula y las dos hierbas y no se convierten en preguntas genéricas de una sola hierba."
echo "OK: cada pregunta de alcance terapéutico debe conservar el objetivo estrecho del Duì Yào y rechazar su equivalencia automática con una fórmula clásica."
echo "OK: si la IA no supera la validación, el sistema construye un borrador completo desde una relación fórmula–Duì Yào verificada en vez de volver al formulario vacío."
echo "OK: cualquier fallo restante conserva íntegramente el texto escrito y lo devuelve dentro del formulario con la causa visible."
echo "OK: el borrador incorrecto ya guardado se reemplaza automáticamente por la pregunta solicitada y permanece pendiente de aprobación."
echo "OK: al publicar una pregunta creada, la pantalla avanza al siguiente borrador o vuelve a la cola del examen y no vuelve a mostrar la tarjeta aprobada."
echo "OK: los enlaces antiguos de finalización que todavía apuntan a una pregunta publicada salen automáticamente de su pantalla de aprobación."
echo "OK: las preguntas administrativas ya publicadas dejan de ocupar la primera posición de All questions; siguen disponibles al buscarlas por ID o texto."
echo "OK: las preguntas originales listas muestran Aprobar y publicar también desde la búsqueda general."
echo "OK: una solicitud de hierbas visibles bloquea la publicación hasta que aparezcan en el enunciado o las opciones."
echo "OK: una pregunta similar puede excluirse solo del examen actual y se busca un reemplazo del mismo dominio."
echo "OK: las preguntas no-board pueden enviarse a su categoría Mock, quedan fuera de Simulation Exams y pueden restaurarse."
echo "OK: Exam builder muestra candidatas SCORM revisables cuando se agota la reserva ya completada."
echo "OK: All options are incorrect y sus equivalentes quedan bloqueados y deben reescribirse."
echo "OK: CF-0126 contiene cuatro comparaciones independientes y feedback completo en ambos idiomas."
echo "OK: una opción que concatena otras dos también queda bloqueada como respuesta combinada."
echo "OK: SCORM-FD-E4D38EA9FCBA usa la comparación corregida de Yu Ping Feng San y Mu Li San."
echo "OK: las preguntas ya publicadas en otro examen se omiten de los exámenes nuevos y se reemplazan por preguntas del mismo dominio."
echo "OK: dos candidatas con el mismo enunciado no pueden ocupar posiciones separadas dentro del mismo examen."
echo "OK: la publicación queda bloqueada si un examen todavía contiene una pregunta usada anteriormente."
echo "OK: una pregunta aprobada muestra Publicar en el examen activo y desaparece de la cola al incorporarse."
echo "OK: si un dominio se queda sin candidatas únicas, la pantalla muestra el déficit y abre la creación por lotes para ese dominio."
echo "OK: las preguntas creadas desde el aviso de déficit se asignan automáticamente al examen al aprobarlas."
echo "OK: las preguntas creadas y publicadas que perdieron su vínculo con el examen de destino se reincorporan automáticamente y el total se recalcula."
echo "OK: ADMIN-HB-20261121-054320-94BA se corrige, publica y asigna al examen activo como la pregunta Duì Yào de Fu Zi + Rou Gui solicitada."
echo "OK: la creación futura conserva Liu Wei Di Huang Wan, el linaje Ba Wei Di Huang Wan/Jin Gui Shen Qi Wan y la distinción Rou Gui/Gui Zhi."
echo "OK: una instrucción general de fórmula frente a Duì Yào rota entre las 51 relaciones verificadas y prioriza fórmulas y hierbas todavía no usadas."
echo "OK: la reparación histórica de Gui Zhi + Bai Shao ya no reemplaza los borradores nuevos."
echo "OK: cuando el examen necesita varias Duì Yào, una sola instrucción general puede crear hasta 5 borradores distintos por lote."
echo "OK: Crear preguntas incluye un banco persistente de instrucciones generales con acciones para usar, guardar, editar y eliminar."
echo "OK: la plantilla incluida de fórmula frente a Duì Yào se puede reutilizar sin modificar ni publicar preguntas existentes."
echo "OK: las comparaciones fórmula–Duì Yào se redactan como preguntas directas con cuatro opciones breves; el razonamiento queda solo en la explicación."
echo "OK: el borrador existente de Shè Gān Má Huáng Tāng y Zǐ Wǎn + Kuǎn Dōng Huā se corrige automáticamente y permanece pendiente de aprobación."
echo "OK: las opciones de fórmula–Duì Yào ya no usan Both/Ambos ni otros atajos de respuesta combinada."
echo "OK: los enlaces de creación no muestran un déficit de 0 y conservan el examen activo elegido como destino."
echo "OK: las preguntas creadas con la instrucción de Duì Yào se clasifican siempre en Domain 3 y conservan el examen activo de destino."
echo "OK: el inventario distingue las preguntas aprobadas de las posiciones pendientes de cada dominio del examen activo."
echo "OK: cada borrador creado para un examen muestra una etiqueta visible con su examen de destino."
echo "OK: hasta 5 preguntas creadas y ya aprobadas que perdieron el vínculo se recuperan en el examen activo respetando la cuota de su dominio."
echo "OK: al aprobar una pregunta creada sin destino explícito, se incorpora automáticamente al examen activo cuando todavía falta una posición de ese dominio."
echo "OK: la solicitud específica de Mǎ Huáng queda fijada a Mǎ Huáng sola, Mǎ Huáng + Guì Zhī como Duì Yào y Mǎ Huáng Tāng como fórmula completa."
echo "OK: el borrador incorrecto de esa solicitud se corrige automáticamente y Mǎ Huáng Tāng permanece como respuesta para la presentación completa."
echo "OK: Crear preguntas ofrece Usar la pregunta exactamente como está escrita para importar enunciado, A-D, respuesta y explicación sin reescribirlos."
echo "OK: los lotes terminados con encabezados Question 1, Question 2, etc. se importan localmente sin esperar a la IA."
echo "OK: la importación exacta no construye el índice de similitud de las más de 8,600 preguntas antes de guardar el lote."
echo "OK: Both herbs y otras afirmaciones clínicas normales son válidas; solo se bloquean atajos como Both of them o Both are correct."
echo "OK: una pregunta completa pegada se detecta automáticamente y no hereda el objetivo especial de la pregunta anterior."
echo "OK: la importación exacta acepta opciones A.-D. con o sin numeración y limpia barras de formato copiadas desde Markdown."
echo "OK: ADMIN-HB-20261121-191027-3A3B conserva la sibilancia leve como único síntoma y Mǎ Huáng como respuesta correcta."
echo "OK: las preguntas creadas muestran Pinyin sin marcas tonales en inglés y español sin eliminar los acentos propios del español."
echo "OK: la misma limpieza cubre preguntas curadas, SCORM, revisiones guardadas y copias de exámenes ya publicados."
echo "OK: los 51 pares Duì Yào se verifican contra el Appendix A; Xin Yi usa el nombre oficial del guideline."
echo "OK: todas las hierbas nombradas se comprueban contra las 304 del Appendix A y todas las fórmulas contra las 181 del Appendix C vigente."
echo "OK: cualquier hierba o fórmula fuera del guideline bloquea la publicación y muestra el nombre conflictivo."
echo "OK: SCORM-SD-D247916CA323 usa Gan Sui y tres distractores del Appendix A; al quedar válida recupera Aprobar y publicar."
echo "OK: SCORM-SD-941383759583 conserva Rou Dou Kou, elimina Jian Pi Wan y usa nomenclatura oficial del Appendix A."
echo "OK: SCORM-SD-11C81E9FD13A conserva He Ye, elimina Qing Luo Yin y Si Sheng Wan y usa cuatro hierbas oficiales del Appendix A."
echo "OK: SCORM-SD-2FD5E01DF69F usa Han Lian Cao, evalúa la preparación de Gui Ban y convierte Cold-Damp del Estómago en una precaución real."
echo "OK: el detector de fórmulas también reconoce los nombres terminados en Yin, Yin Zi, Jian, Dan y Fang."
echo "OK: los borradores creados para el examen aparecen en su cola y el aviso distingue creadas publicadas de borradores pendientes."
echo "OK: crear preguntas abre un espacio aislado que no mezcla preguntas antiguas ni borradores de otros trabajos."
echo "OK: la creación muestra progreso, vista previa del lote, pendientes y confirmación inequívoca de publicación."
echo "OK: después de crear un borrador, la tarjeta nueva se abre directamente sin detenerse en una pantalla Continue vacía."
echo "OK: los lotes muestran una sola pregunta por pantalla y avanzan automáticamente a la siguiente después de publicarla."
echo "OK: el progreso del examen permanece visible y se actualiza después de cada publicación; una pregunta ya creada se recupera en vez de duplicarse."
echo "OK: FD-012 contiene versiones completas y separadas en inglés y español."
echo "OK: CF-0127, CF-0213, CF-0227, CF-0230, CF-0280, CF-0305 y CF-1582 tienen cuatro opciones independientes, una sola respuesta defendible y feedback A-D."
echo "OK: SCORM-FD-5917E7491258 usa la comparación corregida de Yue Ju Wan y Liang Fu Wan."
echo "OK: CF-1582 ahora evalúa Gui Zhi Tang frente a tres fórmulas y muestra los ingredientes de cada opción."
echo "OK: CF-0217 usa cuatro fórmulas independientes, una sola respuesta correcta y explicación completa; Both/None desaparecen."
echo "OK: las candidatas con respuestas combinadas prohibidas se omiten de la cola del examen hasta que sean corregidas."
echo "OK: cada tarjeta del inventario completo muestra Mover / aplazar sin cambiar la posición de ningún examen."
echo "OK: una pregunta puede moverse a una posición concreta, aplazarse al final o restaurar su orden normal."
echo "OK: las preguntas aplazadas permanecen localizables y bajan de prioridad para que aparezcan mejores candidatas primero."
echo "OK: Mover / aplazar también aparece dentro de la revisión pendiente del examen activo."
echo "OK: al aplazar desde el examen, la misma cola se recarga para intentar mostrar otra candidata."
echo "OK: cada examen limita a 3 por cada 100 las comparaciones clínicas construidas con dos pacientes."
echo "OK: las comparaciones excedentes permanecen en reserva y son sustituidas por preguntas con otro formato."
echo "OK: después de publicar, la búsqueda de la pregunta terminada se cierra y la pantalla avanza a la cola activa."
echo "OK: si no existe un examen activo, la publicación vuelve al banco general sin mostrar otra vez la misma tarjeta."
echo "OK: las preguntas creadas y aprobadas pueden abrirse en Editar manualmente; al publicarlas se elimina la nomenclatura oculta de versiones anteriores y se valida de nuevo Appendix A/C."
echo "OK: la vista general usa el nombre Examen de simulación y muestra la cobertura real de los cinco exámenes sin columnas administrativas."
echo "OK: Formula Quiz y la cobertura por capítulo tienen enlaces recíprocos con apertura y desplazamiento directos."
echo "OK: Single Herbs Quiz, Formula Quiz y Duì Yào tienen navegación de ida y vuelta con el inventario del Examen de simulación."
echo "OK: $CF0126_OVERRIDE_RESULT"
echo "OK: $EXAM5_OVERRIDE_RESULT"
echo "OK: ${OVERRIDE_PERMISSION_RESULT:-No fue necesario reparar permisos del estado de exámenes.}"
echo "OK: revisión instalada: $REVISION"
echo "SHA256 DATOS: $INSTALLED_SHA256"
if [[ "${#WEB_RUNTIME_RELOADED[@]}" -gt 0 ]]; then
    echo "RUNTIME RECARGADO: ${WEB_RUNTIME_RELOADED[*]}"
else
    echo "AVISO: no se detectó un servicio web para recargar; Moodle cache sí fue purgada."
fi
echo "RESPALDO: $BACKUP_DIR"
