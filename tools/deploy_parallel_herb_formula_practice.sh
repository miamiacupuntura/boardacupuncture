#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
STUDY_NOTE_DIR="${STUDY_NOTE_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_parallel_herb_formula_practice.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

for command in curl php install; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

if [[ -z "$STUDY_NOTE_DIR" ]]; then
    for candidate in \
        "/var/www/acupuncturetests-study-guide/current/comparisons" \
        "$MOODLE_ROOT/study-guide/comparisons" \
        "$(dirname "$MOODLE_ROOT")/study-guide/comparisons" \
        "/var/www/html/study-guide/comparisons"; do
        if [[ -d "$candidate" && -f "$candidate/comparisons.css" ]]; then
            STUDY_NOTE_DIR="$candidate"
            break
        fi
    done
fi
if [[ -z "$STUDY_NOTE_DIR" || ! -d "$STUDY_NOTE_DIR" ]]; then
    echo "ERROR: no se encontró el directorio activo de Study Notes. Indica STUDY_NOTE_DIR=/ruta/comparisons"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/herb-formula-practice-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/moodle" "$TEMP_DIR/static" "$BACKUP_DIR/moodle" "$BACKUP_DIR/static"

MOODLE_FILES=(
    "blocks/menu/db/install.xml"
    "blocks/menu/db/upgrade.php"
    "blocks/menu/herbology-board.php"
    "blocks/menu/version.php"
    "blocks/myprogress/single-herb-properties.json"
    "blocks/myprogress/single-herbs-study-guide.php"
    "blocks/myprogress/study-guide.php"
)
STATIC_FILES=(
    "comparisons.css"
    "question-study-bank.css"
    "single-herb-practice.html"
    "single-herb-practice-es.html"
    "single-herb-practice.js"
    "herb-comparison-bank.html"
    "herb-comparison-bank-es.html"
    "herb-comparison-bank.css"
    "herb-comparison-bank.js"
    "herb-comparison-bank-data.json"
    "herb-comparison-bank-translations-es.json"
    "formula-practice.html"
    "formula-practice-es.html"
    "formula-practice.js"
    "formula-navigator.html"
    "formula-navigator-es.html"
    "formula-navigator.js"
    "formula-practice-data.json"
    "formula-practice-translations-es.json"
    "formula-question-bank.html"
    "formula-question-bank-es.html"
    "formula-question-bank.js"
    "formula-modifications-manifest.json"
    "formula-modifications-supplement.json"
    "formula-modifications-gui-zhi-tang.json"
    "formula-modifications-ge-gen-tang.json"
    "formula-modifications-generated.json"
    "formula-category-expansion.json"
    "study-return-navigation.js"
)

for relative in "${MOODLE_FILES[@]}"; do
    sourcefile="$TEMP_DIR/moodle/$relative"
    mkdir -p "$(dirname "$sourcefile")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$sourcefile"
done
for file in "${STATIC_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/static/$file"
done

php -l "$TEMP_DIR/moodle/blocks/menu/db/upgrade.php" >/dev/null
php -l "$TEMP_DIR/moodle/blocks/menu/herbology-board.php" >/dev/null
php -l "$TEMP_DIR/moodle/blocks/menu/version.php" >/dev/null
php -l "$TEMP_DIR/moodle/blocks/myprogress/single-herbs-study-guide.php" >/dev/null
php -l "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php" >/dev/null
php -r '
$xml = simplexml_load_file($argv[1]);
if (!$xml || (string)$xml["VERSION"] !== "2026110634") {
    throw new RuntimeException("Versión install.xml incorrecta.");
}
' "$TEMP_DIR/moodle/blocks/menu/db/install.xml"
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
$verified = array_filter($data["herbs"] ?? [], static function($herb) {
    return ($herb["status"] ?? "") === "verified"
        && !empty($herb["actionSummary"]["en"])
        && !empty($herb["indicationSummary"]["en"]);
});
if (count($verified) < 100) {
    throw new RuntimeException("El banco verificado de hierbas es insuficiente.");
}
' "$TEMP_DIR/moodle/blocks/myprogress/single-herb-properties.json"
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
if (count($data["questions"] ?? []) < 500) {
    throw new RuntimeException("El banco de fórmulas es insuficiente.");
}
' "$TEMP_DIR/static/formula-practice-data.json"
php -r '
$data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
if (count($data["categories"] ?? []) !== 4
        || count($data["formulas"] ?? []) !== 13
        || count($data["questions"] ?? []) !== 52) {
    throw new RuntimeException("La expansión de categorías de fórmulas está incompleta.");
}
' "$TEMP_DIR/static/formula-category-expansion.json"
php -r '
$directory = $argv[1];
$bank = json_decode(file_get_contents($directory . "/formula-practice-data.json"), true, 512, JSON_THROW_ON_ERROR);
$manifest = json_decode(file_get_contents($directory . "/formula-modifications-manifest.json"), true, 512, JSON_THROW_ON_ERROR);
$questions = $bank["questions"] ?? [];
$formulas = array_column($bank["formulas"] ?? [], null, "number");
$categories = array_column($bank["categories"] ?? [], null, "key");
foreach ($manifest["files"] ?? [] as $filename) {
    $supplement = json_decode(file_get_contents($directory . "/" . basename($filename)), true, 512, JSON_THROW_ON_ERROR);
    $questions = array_merge($questions, $supplement["questions"] ?? []);
    foreach ($supplement["formulas"] ?? [] as $formula) {
        $formulas[(int)$formula["number"]] = $formula;
    }
    foreach ($supplement["categories"] ?? [] as $category) {
        $categories[(string)$category["key"]] = $category;
    }
}
unset($categories["modifications"]);
if (count($questions) !== 929 || count($formulas) !== 76 || count($categories) !== 18) {
    throw new RuntimeException("Totales ampliados incorrectos: se esperaban 929 preguntas, 76 fórmulas y 18 categorías oficiales.");
}
' "$TEMP_DIR/static"

grep -Fq "'title' => 'Single Herbs'" "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq "'path' => '/blocks/myprogress/single-herbs-study-guide.php'" "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq 'single-herb-practice-es.html' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
grep -Fq 'formula-question-bank-es.html' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
grep -Fq 'Practice Questions' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
grep -Fq 'Questions &amp; Flashcards' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
grep -Fq '$plugin->version   = 2026110634;' "$TEMP_DIR/moodle/blocks/menu/version.php"
grep -Fq 'official categories' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
grep -Fq 'Additional practice filter' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
grep -Fq 'Single Herbs Practice' "$TEMP_DIR/static/single-herb-practice.html"
grep -Fq 'class="practice-study-route"' "$TEMP_DIR/static/single-herb-practice.html"
grep -Fq 'Herbology Practice</a><span aria-hidden="true">›</span><a href="/blocks/menu/herbology-board.php?track=herbs">Single Herbs' "$TEMP_DIR/static/single-herb-practice.html"
grep -Fq '← Back to Dashboard' "$TEMP_DIR/static/single-herb-practice.html"
grep -Fq 'Formula Questions &amp; Flashcards' "$TEMP_DIR/static/formula-question-bank.html"
grep -Fq 'class="practice-study-route"' "$TEMP_DIR/static/formula-navigator.html"
grep -Fq 'class="practice-study-route"' "$TEMP_DIR/static/formula-practice.html"
grep -Fq 'class="practice-study-route"' "$TEMP_DIR/static/formula-question-bank.html"
grep -Fq 'Herbology Practice</a><span aria-hidden="true">›</span><a href="/blocks/menu/herbology-board.php?track=formulas">Formulas' "$TEMP_DIR/static/formula-practice.html"
grep -Fq 'formula-question-bank.html">Questions &amp; flashcards' "$TEMP_DIR/static/formula-navigator.html"
grep -Fq 'formula-navigator.html">Find a formula' "$TEMP_DIR/static/formula-question-bank.html"
grep -Fq '← Back to Dashboard' "$TEMP_DIR/static/formula-practice.html"
! grep -Fq 'study-return-navigation.js' "$TEMP_DIR/static/formula-practice.js"
! grep -Fq 'study-return-navigation.js' "$TEMP_DIR/static/formula-navigator.js"
grep -Fq "dashboardUrl = '/my/'" "$TEMP_DIR/static/study-return-navigation.js"
grep -Fq "hero.insertAdjacentElement('afterend', nav)" "$TEMP_DIR/static/study-return-navigation.js"
grep -Fq 'class="practice-study-route"' "$TEMP_DIR/static/herb-comparison-bank.html"
grep -Fq 'Herbology Practice</a><span aria-hidden="true">›</span><a href="/blocks/menu/herbology-board.php?track=herbs">Single Herbs' "$TEMP_DIR/static/herb-comparison-bank.html"
grep -Fq '← Back to Dashboard' "$TEMP_DIR/static/herb-comparison-bank.html"
! grep -Fq 'Back to herb library' "$TEMP_DIR/static/herb-comparison-bank.html"
! grep -Fq 'study-return-navigation.js' "$TEMP_DIR/static/herb-comparison-bank.js"

mkdir -p "$BACKUP_DIR/moodle" "$BACKUP_DIR/static"
for relative in "${MOODLE_FILES[@]}"; do
    sourcefile="$TEMP_DIR/moodle/$relative"
    target="$MOODLE_ROOT/$relative"
    mkdir -p "$(dirname "$target")" "$BACKUP_DIR/moodle/$(dirname "$relative")"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP_DIR/moodle/$relative"
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
    else
        owner_group="$(stat -c '%u:%g' "$MOODLE_ROOT/config.php")"
        mode="0644"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$sourcefile" "$target"
done

STATIC_REFERENCE="$STUDY_NOTE_DIR/comparisons.css"
static_owner_group="$(stat -c '%u:%g' "$STATIC_REFERENCE")"
static_mode="$(stat -c '%a' "$STATIC_REFERENCE")"
for file in "${STATIC_FILES[@]}"; do
    target="$STUDY_NOTE_DIR/$file"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP_DIR/static/$file"
    fi
    install -o "${static_owner_group%:*}" -g "${static_owner_group#*:}" -m "$static_mode" \
        "$TEMP_DIR/static/$file" "$target"
done

php -l "$MOODLE_ROOT/blocks/menu/herbology-board.php" >/dev/null
php -l "$MOODLE_ROOT/blocks/myprogress/study-guide.php" >/dev/null

if [[ -f "$MOODLE_ROOT/admin/cli/upgrade.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
    else
        php "$MOODLE_ROOT/admin/cli/upgrade.php" --non-interactive
    fi
fi
if [[ "${SKIP_CACHE_PURGE:-0}" != "1" && -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "FORMATOS PARALELOS DE HIERBAS Y FÓRMULAS INSTALADOS"
echo "Single Herbs permanece en Study Guide."
echo "Respaldo: $BACKUP_DIR"
echo "Prueba: https://www.acupuncturetests.com/blocks/menu/herbology-board.php?track=herbs&practice=$REVISION"
