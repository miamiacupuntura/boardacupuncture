#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
STUDY_NOTE_DIR="${STUDY_NOTE_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_herbology_library_navigation.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi
for command in curl php install tar; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done
[[ -f "$MOODLE_ROOT/config.php" ]] || { echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"; exit 1; }

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
[[ -n "$STUDY_NOTE_DIR" && -d "$STUDY_NOTE_DIR" ]] || {
    echo "ERROR: no se encontró el directorio activo de comparisons. Indica STUDY_NOTE_DIR=/ruta/comparisons"
    exit 1
}

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/herbology-library-navigation-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/moodle" "$TEMP_DIR/static" "$TEMP_DIR/tools" "$BACKUP_DIR/moodle" "$BACKUP_DIR/static"

MOODLE_FILES=(
    "blocks/menu/herbology-board.php"
    "blocks/menu/styles.css"
    "blocks/menu/version.php"
    "blocks/menu/db/install.xml"
    "blocks/menu/db/upgrade.php"
    "blocks/myprogress/herbology-library-navigation.php"
    "blocks/myprogress/study-guide.php"
    "blocks/myprogress/single-herbs-study-guide.php"
    "blocks/myprogress/formulas-study-guide.php"
    "blocks/myprogress/herb-formula-category-map.php"
    "blocks/myprogress/common-herbal-combinations.php"
)
STATIC_FILES=(
    "library-navigation.js"
    "study-note-navigation.js"
    "index.html"
    "index-es.html"
    "pathologies.html"
    "pathologies-es.html"
    "patterns.html"
    "patterns-es.html"
    "safety.html"
    "safety-es.html"
)

for relative in "${MOODLE_FILES[@]}"; do
    mkdir -p "$TEMP_DIR/moodle/$(dirname "$relative")"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$TEMP_DIR/moodle/$relative"
done
for file in "${STATIC_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/static/$file"
done
curl -fsSL "$REPOSITORY/$REVISION/tools/inject_library_navigation.php" \
    -o "$TEMP_DIR/tools/inject_library_navigation.php"

for relative in "${MOODLE_FILES[@]}"; do
    if [[ "$relative" == *.php ]]; then
        php -l "$TEMP_DIR/moodle/$relative" >/dev/null
    fi
done
php -l "$TEMP_DIR/tools/inject_library_navigation.php" >/dev/null
grep -Fq '$plugin->version   = 2026110655;' "$TEMP_DIR/moodle/blocks/menu/version.php"
grep -Fq '.herb-board__page-navigation' "$TEMP_DIR/moodle/blocks/menu/styles.css"
if grep -Fq 'class="herb-board__hero"' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"; then
    echo "ERROR: el encabezado grande de Herbology continúa en las hojas." >&2
    exit 1
fi
grep -Fq "optional_param('from', 'library', PARAM_ALPHA) !== 'dashboard'" "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
if grep -Fq 'aria-current="page">Quiz</a>' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"; then
    echo "ERROR: el encabezado de Herbology Quiz todavía contiene el botón Quiz redundante." >&2
    exit 1
fi
grep -Fq 'data-herbology-step-back' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
grep -Fq 'herbology-step-back-scroll' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
grep -Fq 'event?.persisted' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"
if grep -Fq "<strong><?php echo \$category['questions']; ?></strong>" "$TEMP_DIR/moodle/blocks/menu/herbology-board.php" \
        || grep -Fq "<strong><?php echo \$category['count']; ?></strong>" "$TEMP_DIR/moodle/blocks/menu/herbology-board.php" \
        || grep -Fq '<strong><?php echo $formulamodificationcount; ?></strong>' "$TEMP_DIR/moodle/blocks/menu/herbology-board.php"; then
    echo "ERROR: los contadores de preguntas continúan en la navegación de temas." >&2
    exit 1
fi
grep -Fq 'Library is the permanent navigation hub' "$TEMP_DIR/moodle/blocks/myprogress/herbology-library-navigation.php"
grep -Fq 'data-library-step-back' "$TEMP_DIR/moodle/blocks/myprogress/herbology-library-navigation.php"
grep -Fq 'herbology-step-back-scroll' "$TEMP_DIR/moodle/blocks/myprogress/herbology-library-navigation.php"
grep -Fq 'event?.persisted' "$TEMP_DIR/moodle/blocks/myprogress/herbology-library-navigation.php"
if [[ "$(grep -Fc 'class="herb-formula-map__button"' "$TEMP_DIR/moodle/blocks/myprogress/herb-formula-category-map.php")" -ne 1 ]]; then
    echo "ERROR: el mapa Bensky debe mostrar solamente el selector de idioma." >&2
    exit 1
fi
grep -Fq "param('from', 'library')" "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq 'herbology-step-back-scroll' "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq 'event?.persisted' "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq 'data-library-footer' "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq 'data-library-page-back' "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq 'subject-study-library__map-actions' "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
if grep -Fq 'class="subject-study-library__section-links"' "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"; then
    echo "ERROR: los botones de conteo continúan en el mapa principal de Library." >&2
    exit 1
fi
if grep -Fq 'subject-study-library__top' "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"; then
    echo "ERROR: el encabezado genérico Chinese Herbology Library continúa en la portada." >&2
    exit 1
fi
grep -Fq "set_heading(\$showlibrary && \$areaid === 'herbology' ? ''" "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq "if (\$areaid !== 'herbology')" "$TEMP_DIR/moodle/blocks/myprogress/study-guide.php"
grep -Fq 'data-herbology-library-context' "$TEMP_DIR/moodle/blocks/myprogress/herbology-library-navigation.php"
grep -Fq 'herbology-sheet-hero' "$TEMP_DIR/moodle/blocks/myprogress/herbology-library-navigation.php"
grep -Fq 'dataset.libraryContextNavigation' "$TEMP_DIR/static/library-navigation.js"
grep -Fq 'data-library-step-back' "$TEMP_DIR/static/library-navigation.js"
grep -Fq 'herbology-step-back-scroll' "$TEMP_DIR/static/library-navigation.js"
grep -Fq 'event.persisted' "$TEMP_DIR/static/library-navigation.js"
grep -Fq 'library-standard-header' "$TEMP_DIR/static/library-navigation.js"
grep -Fq "document.querySelectorAll('.comparison-collection-links')" "$TEMP_DIR/static/library-navigation.js"
if grep -Fq 'herb-bank-entry' "$TEMP_DIR/static/index.html" \
        || grep -Fq 'herb-bank-entry' "$TEMP_DIR/static/index-es.html"; then
    echo "ERROR: el bloque promocional del banco de preguntas continúa en Single Herbs Comparisons." >&2
    exit 1
fi
for comparison_hub in index.html index-es.html pathologies.html pathologies-es.html patterns.html patterns-es.html safety.html safety-es.html; do
    if grep -Fq 'comparison-collection-links' "$TEMP_DIR/static/$comparison_hub"; then
        echo "ERROR: la franja duplicada de botones continúa en $comparison_hub." >&2
        exit 1
    fi
done
if grep -Fq 'libraryName' "$TEMP_DIR/static/library-navigation.js"; then
    echo "ERROR: la navegación estática todavía reemplaza el nombre propio de las hojas." >&2
    exit 1
fi
if grep -Fq 'Back to Library' "$TEMP_DIR/static/library-navigation.js" \
        || grep -Fq 'Back to Library' "$TEMP_DIR/moodle/blocks/myprogress/herbology-library-navigation.php"; then
    echo "ERROR: la navegación todavía contiene el botón Back to Library." >&2
    exit 1
fi

(cd "$STUDY_NOTE_DIR" && tar -czf "$BACKUP_DIR/static/comparison-html.tar.gz" -- *.html)
for file in "${STATIC_FILES[@]}"; do
    if [[ -f "$STUDY_NOTE_DIR/$file" ]]; then
        cp -a "$STUDY_NOTE_DIR/$file" "$BACKUP_DIR/static/$file"
    fi
done
for relative in "${MOODLE_FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    if [[ -f "$target" ]]; then
        mkdir -p "$BACKUP_DIR/moodle/$(dirname "$relative")"
        cp -a "$target" "$BACKUP_DIR/moodle/$relative"
    fi
done

static_owner_group="$(stat -c '%u:%g' "$STUDY_NOTE_DIR/comparisons.css")"
static_mode="$(stat -c '%a' "$STUDY_NOTE_DIR/comparisons.css")"
for file in "${STATIC_FILES[@]}"; do
    install -o "${static_owner_group%:*}" -g "${static_owner_group#*:}" -m "$static_mode" \
        "$TEMP_DIR/static/$file" "$STUDY_NOTE_DIR/$file"
done
php "$TEMP_DIR/tools/inject_library_navigation.php" --write --root="$STUDY_NOTE_DIR"
php "$TEMP_DIR/tools/inject_library_navigation.php" --check --root="$STUDY_NOTE_DIR"

default_owner_group="$(stat -c '%u:%g' "$MOODLE_ROOT/config.php")"
for relative in "${MOODLE_FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    mkdir -p "$(dirname "$target")"
    if [[ -f "$target" ]]; then
        owner_group="$(stat -c '%u:%g' "$target")"
        mode="$(stat -c '%a' "$target")"
    else
        owner_group="$default_owner_group"
        mode="644"
    fi
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/moodle/$relative" "$target"
done

for relative in "${MOODLE_FILES[@]}"; do
    if [[ "$relative" == *.php ]]; then
        php -l "$MOODLE_ROOT/$relative" >/dev/null
    fi
done
grep -Fq '/study-guide/comparisons/library-navigation.js?v=20261107-11' "$STUDY_NOTE_DIR/dui-yao.html"
grep -Fq 'dataset.libraryContextNavigation' "$STUDY_NOTE_DIR/library-navigation.js"
grep -Fq 'library-standard-header' "$STUDY_NOTE_DIR/library-navigation.js"
grep -Fq "document.querySelectorAll('.comparison-collection-links')" "$STUDY_NOTE_DIR/library-navigation.js"
if grep -Fq 'herb-bank-entry' "$STUDY_NOTE_DIR/index.html" \
        || grep -Fq 'herb-bank-entry' "$STUDY_NOTE_DIR/index-es.html"; then
    echo "ERROR: el bloque promocional del banco de preguntas continúa instalado." >&2
    exit 1
fi
for comparison_hub in index.html index-es.html pathologies.html pathologies-es.html patterns.html patterns-es.html safety.html safety-es.html; do
    if grep -Fq 'comparison-collection-links' "$STUDY_NOTE_DIR/$comparison_hub"; then
        echo "ERROR: la franja duplicada de botones continúa instalada en $comparison_hub." >&2
        exit 1
    fi
done
if grep -Fq 'libraryName' "$STUDY_NOTE_DIR/library-navigation.js"; then
    echo "ERROR: la navegación instalada reemplaza el nombre propio de las hojas." >&2
    exit 1
fi
if grep -Fq 'Back to Library' "$STUDY_NOTE_DIR/library-navigation.js" \
        || grep -Fq 'Back to Library' "$MOODLE_ROOT/blocks/myprogress/herbology-library-navigation.php"; then
    echo "ERROR: el botón Back to Library continúa instalado." >&2
    exit 1
fi
if grep -Fq "currentUrl.searchParams.get('from') !== 'library'" "$STUDY_NOTE_DIR/library-navigation.js"; then
    echo "ERROR: la navegación estática todavía depende de from=library." >&2
    exit 1
fi
grep -Fq 'data-herbology-library-context' "$MOODLE_ROOT/blocks/myprogress/herbology-library-navigation.php"
grep -Fq 'herbology-sheet-hero' "$MOODLE_ROOT/blocks/myprogress/herbology-library-navigation.php"
grep -Fq 'data-library-step-back' "$MOODLE_ROOT/blocks/myprogress/herbology-library-navigation.php"
grep -Fq 'herbology-step-back-scroll' "$MOODLE_ROOT/blocks/myprogress/study-guide.php"
grep -Fq 'data-library-footer' "$MOODLE_ROOT/blocks/myprogress/study-guide.php"
grep -Fq 'data-library-page-back' "$MOODLE_ROOT/blocks/myprogress/study-guide.php"
grep -Fq 'subject-study-library__map-actions' "$MOODLE_ROOT/blocks/myprogress/study-guide.php"
if grep -Fq 'class="subject-study-library__section-links"' "$MOODLE_ROOT/blocks/myprogress/study-guide.php"; then
    echo "ERROR: los botones de conteo continúan instalados en el mapa principal de Library." >&2
    exit 1
fi
if grep -Fq 'subject-study-library__top' "$MOODLE_ROOT/blocks/myprogress/study-guide.php"; then
    echo "ERROR: el encabezado genérico Chinese Herbology Library continúa instalado." >&2
    exit 1
fi
grep -Fq "set_heading(\$showlibrary && \$areaid === 'herbology' ? ''" "$MOODLE_ROOT/blocks/myprogress/study-guide.php"
grep -Fq "if (\$areaid !== 'herbology')" "$MOODLE_ROOT/blocks/myprogress/study-guide.php"
grep -Fq 'data-herbology-step-back' "$MOODLE_ROOT/blocks/menu/herbology-board.php"
grep -Fq '.herb-board__page-navigation' "$MOODLE_ROOT/blocks/menu/styles.css"
if grep -Fq "<strong><?php echo \$category['questions']; ?></strong>" "$MOODLE_ROOT/blocks/menu/herbology-board.php" \
        || grep -Fq "<strong><?php echo \$category['count']; ?></strong>" "$MOODLE_ROOT/blocks/menu/herbology-board.php" \
        || grep -Fq '<strong><?php echo $formulamodificationcount; ?></strong>' "$MOODLE_ROOT/blocks/menu/herbology-board.php"; then
    echo "ERROR: los contadores de preguntas continúan instalados en la navegación de temas." >&2
    exit 1
fi
if grep -Fq 'class="herb-board__hero"' "$MOODLE_ROOT/blocks/menu/herbology-board.php"; then
    echo "ERROR: el encabezado grande de Herbology continúa instalado." >&2
    exit 1
fi
if grep -Fq 'aria-current="page">Quiz</a>' "$MOODLE_ROOT/blocks/menu/herbology-board.php"; then
    echo "ERROR: el botón Quiz redundante continúa instalado." >&2
    exit 1
fi

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

echo "Despliegue completado: $REVISION"
echo "HTML actualizados: $STUDY_NOTE_DIR"
echo "Backup: $BACKUP_DIR"
echo "Prueba: https://www.acupuncturetests.com/study-guide/comparisons/dui-yao.html?from=library&revision=$REVISION"
