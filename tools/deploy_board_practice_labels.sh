#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_board_practice_labels.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

for command in curl php install; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/board-practice-labels-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT

FILES=(
    "blocks/menu/block_menu.php"
    "blocks/menu/board.php"
    "blocks/menu/clean-needle-exam-key.php"
    "blocks/menu/db/install.xml"
    "blocks/menu/db/upgrade.php"
    "blocks/menu/herbology-board.php"
    "blocks/menu/herbology-exam-history.php"
    "blocks/menu/herbology-domain-candidates.json"
    "blocks/menu/herbology-domain-bank.json"
    "blocks/menu/styles.css"
    "blocks/menu/version.php"
    "blocks/menu_course/block_menu_course.php"
    "blocks/menu_course/version.php"
    "blocks/myprogress/single-herbs-safety-dispensing.php"
    "blocks/myprogress/single-herb-card-index.json"
    "blocks/myprogress/single-herbs-study-guide.php"
    "theme/moove/javascript/study-guide-dashboard.js"
    "theme/moove/layout/drawers.php"
)

mkdir -p "$BACKUP_DIR"
for relative in "${FILES[@]}"; do
    sourcefile="$TEMP_DIR/$relative"
    target="$MOODLE_ROOT/$relative"
    mkdir -p "$(dirname "$sourcefile")" "$BACKUP_DIR/$(dirname "$relative")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$sourcefile"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP_DIR/$relative"
    fi
done

php -l "$TEMP_DIR/blocks/menu/block_menu.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/board.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/clean-needle-exam-key.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/db/upgrade.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/herbology-board.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/herbology-exam-history.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu/version.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu_course/block_menu_course.php" >/dev/null
php -l "$TEMP_DIR/blocks/menu_course/version.php" >/dev/null
php -l "$TEMP_DIR/blocks/myprogress/single-herbs-safety-dispensing.php" >/dev/null
php -l "$TEMP_DIR/blocks/myprogress/single-herbs-study-guide.php" >/dev/null
php -l "$TEMP_DIR/theme/moove/layout/drawers.php" >/dev/null

for label in \
    "Foundations Practice" \
    "Acupuncture Practice" \
    "Herbology Practice" \
    "Biomedicine Practice" \
    "CNT Practice"; do
    grep -Fq "$label" "$TEMP_DIR/blocks/menu/block_menu.php"
    grep -Fq "$label" "$TEMP_DIR/theme/moove/javascript/study-guide-dashboard.js"
done
! grep -Fq '>Mock Questions<' "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq '$boardlevelactions = $mockquestionsaction . $simulationaction . $studyguideaction;' \
    "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq 'board-hub__card--school-level board-hub__card--' \
    "$TEMP_DIR/blocks/menu/block_menu.php"
! grep -Fq 'board-hub__action--board-domains' "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq '.board-hub__card--board-level .study-guide-dashboard-link' \
    "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq '.board-hub__card--board-level .board-hub__action--board-simulation' \
    "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq '.board-hub__card:not(.board-hub__card--board-level) .board-hub__action--games' \
    "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq "[48, 'Tricky Questions', 'games']" "$TEMP_DIR/blocks/menu/block_menu.php"
grep -Fq "'title' => 'Tricky Questions'" "$TEMP_DIR/blocks/menu/board.php"
grep -Fq "return ['Tricky Questions', 'games'];" "$TEMP_DIR/blocks/menu_course/block_menu_course.php"
grep -Fq 'Practice by Domain' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'formula_differentiation'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Focused practice' "$TEMP_DIR/blocks/menu/herbology-board.php"
! grep -Fq 'Save question in this Domain' "$TEMP_DIR/blocks/menu/herbology-board.php"
! grep -Fq "domainaction === 'addquestion'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'comienza el examen completo de 100 preguntas' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Examen lineal' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Examen adaptativo' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__exam-previous' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__exam-next' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__exam-restart' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'restartExam' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Historial de exámenes' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'saveAttempt' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'block_menu_exam_attempts' "$TEMP_DIR/blocks/menu/herbology-exam-history.php"
grep -Fq 'TABLE NAME="block_menu_exam_attempts"' "$TEMP_DIR/blocks/menu/db/install.xml"
grep -Fq "upgrade_block_savepoint(true, 2026110629, 'menu')" "$TEMP_DIR/blocks/menu/db/upgrade.php"
grep -Fq 'data-pending-word' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-next-pending-word' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'reviewingPending' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__exam-score' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Math.round((score / answered) * 100)' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'firstUnanswered' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-exam-source-link' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'data-exam-source-viewer' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Volver a la pregunta' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "'/^RA-\\d{3}$/'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '.herb-board__source-window' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq "data-exam-mode" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__domain-paging--top' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__domain-page-link--direction' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__domain-number' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'herb-board__tab--language' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "\$ises ? 'English' : 'Español'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "['translations']['es']" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "\$ises ? 'Preguntas' : 'Questions'" "$TEMP_DIR/blocks/menu/herbology-board.php"
! grep -Fq '6 domain blocks' "$TEMP_DIR/blocks/menu/herbology-board.php"
! grep -Fq 'Approve for mixed exams' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "\$CFG->dataroot . '/board-domain-banks'" "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq "\$ises ? 'Dominios' : 'Domains'" "$TEMP_DIR/blocks/menu/herbology-board.php"
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
if (count($data["domains"] ?? []) !== 6) {
    throw new RuntimeException("The Herbology Domain bank must contain six Domain buckets.");
}
' "$TEMP_DIR/blocks/menu/herbology-domain-bank.json"
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$total = array_sum(array_map("count", $data["domains"] ?? []));
if ($total !== 1087
        || ($data["summary"]["practiceReadyTotal"] ?? 0) !== 451
        || ($data["status"] ?? "") !== "practice-ready"
        || ($data["classificationVersion"] ?? "") !== "herbology-domain-source-grounded-2026-11-06-v8-safety-source-links"
        || ($data["summary"]["nomenclature"]["questionsWithNomenclature"] ?? 0) !== 1042
        || ($data["summary"]["nomenclature"]["herbReferences"] ?? 0) !== 3557
        || ($data["summary"]["nomenclature"]["formulaReferences"] ?? 0) !== 2266
        || ($data["summary"]["guideline2026ScopeAudit"]["legacyIdentificationQuestions"] ?? 0) !== 164
        || ($data["summary"]["guideline2026ScopeAudit"]["herbsOutsideAppendicesAAndD"] ?? -1) !== 0) {
    throw new RuntimeException("The classified Herbology candidate inventory is incomplete.");
}
' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$weights = [
    "formula_differentiation" => 28,
    "single_herb" => 24,
    "dui_yao" => 18,
    "safety_dispensing" => 15,
    "formula_composition" => 10,
    "scope_aliases" => 5,
];
foreach ($data["domains"] ?? [] as $domain => $questions) {
    $ready = array_values(array_filter($questions, static fn(array $question): bool =>
        !empty($question["prompt"])
        && count($question["options"] ?? []) === 4
        && !empty($question["correctLabel"])
        && !empty($question["explanation"])
        && !empty($question["sourceCard"]["url"])
        && !empty($question["sourceCard"]["urlEs"])));
    $spanish = array_values(array_filter($ready, static fn(array $question): bool =>
        !empty($question["translations"]["es"]["prompt"])
        && count($question["translations"]["es"]["options"] ?? []) === 4
        && !empty($question["translations"]["es"]["explanation"])));
    if (count($ready) < ($weights[$domain] ?? PHP_INT_MAX)
            || count($spanish) < ($weights[$domain] ?? PHP_INT_MAX)) {
        throw new RuntimeException("Domain $domain cannot supply its source-linked bilingual exam weight.");
    }
}
' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$expectedWeights = [
    "formula_differentiation" => 28,
    "single_herb" => 24,
    "dui_yao" => 18,
    "safety_dispensing" => 15,
    "formula_composition" => 10,
    "scope_aliases" => 5,
];
$template = $data["examTemplate"] ?? [];
if (($template["version"] ?? "") !== "herbology-guideline-2026-template-v1"
        || ($template["totalQuestions"] ?? 0) !== 100
        || ($template["weights"] ?? []) !== $expectedWeights
        || count($template["sequence"] ?? []) !== 100
        || count(array_unique($template["sequence"] ?? [])) !== 100) {
    throw new RuntimeException("The fixed 100-question Guideline 2026 template is invalid.");
}
$selected = [];
$domainCounts = array_fill_keys(array_keys($expectedWeights), 0);
foreach ($data["domains"] ?? [] as $domain => $questions) {
    foreach ($questions as $question) {
        if (preg_match("/^\\s*IDENTIFICATION(?:\\s+ACCORDING\\s+TO)?\\b/i", $question["prompt"] ?? "")) {
            throw new RuntimeException("Legacy IDENTIFICATION wording remains in the retained bank.");
        }
        $position = $question["examTemplate"]["globalPosition"] ?? 0;
        if ($position > 0) {
            if (($question["examTemplate"]["version"] ?? "") !== $template["version"]
                    || isset($selected[$position])) {
                throw new RuntimeException("The fixed template contains a duplicate or mismatched position.");
            }
            $selected[$position] = $question["questionId"] ?? "";
            $domainCounts[$domain]++;
        }
    }
}
ksort($selected);
if (count($selected) !== 100
        || array_values($selected) !== $template["sequence"]
        || $domainCounts !== $expectedWeights) {
    throw new RuntimeException("The fixed template selection does not match its declared sequence and weights.");
}
' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
grep -Fq 'RA-SAFE-001' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
! grep -Fiq 'which pinyin name corresponds' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
! grep -Fiq 'IDENTIFICATION ACCORDING TO' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
grep -Fq '"questionId": "FD-001"' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
grep -Fq '"version": "herbology-guideline-2026-template-v1"' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
for unsafe_id in FM-MOD-004-02 FM-MOD-009-05 FM-MOD-026-13 FM-MOD-027-03 FM-MOD-056-01; do
    ! grep -Fq "\"questionId\": \"$unsafe_id\"" "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
done
grep -Fq 'Ma Huang Tang (Ephedra Decoction)' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
grep -Fq 'Ma Huang (Ephedrae Herba)' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
grep -Fq 'Guan Mu Tong (Aristolochiae manshuriensis Caulis)' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
! grep -Fq 'Guan Mu Tong (Akebiae Caulis)' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
grep -Fq 'focus=safety_dispensing&herb=Bai%20Guo#safety-herb-bai-guo' "$TEMP_DIR/blocks/menu/herbology-domain-candidates.json"
grep -Fq "\$focus === '' && \$requestedherbcard !== null" "$TEMP_DIR/blocks/myprogress/single-herbs-study-guide.php"
grep -Fq 'single-herbs-safety__row--requested' "$TEMP_DIR/blocks/myprogress/single-herbs-safety-dispensing.php"
grep -Fq 'requestedRow.scrollIntoView' "$TEMP_DIR/blocks/myprogress/single-herbs-safety-dispensing.php"
grep -Fq '"Yin Xing"' "$TEMP_DIR/blocks/myprogress/single-herb-card-index.json"
grep -Fq 'Yin Xing Ye is Ginkgo Folium, a different medicinal part' "$TEMP_DIR/blocks/myprogress/single-herb-card-index.json"
grep -Fq "\$normalizeherbname((string)(\$herbcard['latin'] ?? ''))" "$TEMP_DIR/blocks/myprogress/single-herbs-study-guide.php"
grep -Fq 'Board nomenclature' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq 'Nomenclatura Board' "$TEMP_DIR/blocks/menu/herbology-board.php"
grep -Fq '.herb-board__exam-nomenclature' "$TEMP_DIR/blocks/menu/styles.css"
grep -Fq '$plugin->version   = 2026110629;' "$TEMP_DIR/blocks/menu/version.php"
grep -Fq "'v' => '20261106-9'" "$TEMP_DIR/theme/moove/layout/drawers.php"

for relative in "${FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    targetdir="$(dirname "$target")"
    if [[ ! -d "$targetdir" ]]; then
        parentdir="$(dirname "$targetdir")"
        owner_group="$(stat -c '%u:%g' "$parentdir")"
        dirmode="$(stat -c '%a' "$parentdir")"
        install -d -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$dirmode" "$targetdir"
    fi
    if [[ -f "$target" ]]; then
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
    else
        owner_group="$(stat -c '%u:%g' "$(dirname "$target")")"
        mode="644"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/$relative" "$target"
done

if [[ -f "$MOODLE_ROOT/admin/cli/upgrade.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
    else
        php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
    fi
fi

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

for label in \
    "Foundations Practice" \
    "Acupuncture Practice" \
    "Herbology Practice" \
    "Biomedicine Practice" \
    "CNT Practice"; do
    grep -Fq "$label" "$MOODLE_ROOT/blocks/menu/block_menu.php"
done

echo "PRÁCTICA E HISTORIAL DE HERBOLOGÍA ACTUALIZADOS"
echo "Respaldo: $BACKUP_DIR"
echo "Página: https://www.acupuncturetests.com/my/?labels=$REVISION"
