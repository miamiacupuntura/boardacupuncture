#!/usr/bin/env bash

set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
STUDY_NOTE_DIR="${STUDY_NOTE_DIR:-}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
LOADER='/study-guide/comparisons/study-note-navigation.js?v=20261104-5'

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash deploy_herbology_study_route.sh COMMIT_DE_40_CARACTERES"
    exit 1
fi

for command in curl php tar find install; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command"; exit 1; }
done

if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "ERROR: no se encontró Moodle en $MOODLE_ROOT"
    exit 1
fi

PHP_FILES=(
    "blocks/myprogress/herb-formula-category-map.php"
    "blocks/myprogress/single-herbs-study-guide.php"
    "blocks/myprogress/formulas-study-guide.php"
)
for relative in "${PHP_FILES[@]}"; do
    if [[ ! -f "$MOODLE_ROOT/$relative" ]]; then
        echo "ERROR: no existe $MOODLE_ROOT/$relative"
        exit 1
    fi
done

if [[ -z "$STUDY_NOTE_DIR" ]]; then
    CANDIDATE_DIRS=(
        "/var/www/acupuncturetests-study-guide/current/comparisons"
        "$MOODLE_ROOT/study-guide/comparisons"
        "$(dirname "$MOODLE_ROOT")/study-guide/comparisons"
        "/var/www/html/study-guide/comparisons"
    )
    for candidate in "${CANDIDATE_DIRS[@]}"; do
        if [[ -f "$candidate/dui-yao.html" && -f "$candidate/comparisons.css" ]]; then
            STUDY_NOTE_DIR="$candidate"
            break
        fi
    done
fi
if [[ -z "$STUDY_NOTE_DIR" ]]; then
    ACTIVE_INDEX="$(find /var/www -type f -path '*/study-guide/comparisons/dui-yao.html' -print -quit 2>/dev/null || true)"
    [[ -n "$ACTIVE_INDEX" ]] && STUDY_NOTE_DIR="$(dirname "$ACTIVE_INDEX")"
fi
if [[ -z "$STUDY_NOTE_DIR" || ! -f "$STUDY_NOTE_DIR/dui-yao.html" || ! -f "$STUDY_NOTE_DIR/dui-yao-es.html" ]]; then
    echo "ERROR: no se encontró el directorio activo de Study Notes. Indica STUDY_NOTE_DIR=/ruta/comparisons"
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/herbology-study-route-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/blocks/myprogress" "$TEMP_DIR/study-guide/comparisons" "$BACKUP_DIR/moodle" "$BACKUP_DIR/static"

for relative in "${PHP_FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$TEMP_DIR/$relative"
    php -l "$TEMP_DIR/$relative" >/dev/null
done
for file in dui-yao.html dui-yao-es.html study-note-navigation.js; do
    relative="study-guide/comparisons/$file"
    curl -fsSL "$REPOSITORY/$REVISION/$relative" -o "$TEMP_DIR/$relative"
done

grep -Fq 'Bensky Map' "$TEMP_DIR/blocks/myprogress/herb-formula-category-map.php"
grep -Fq 'single-herbs-guide__study-route' "$TEMP_DIR/blocks/myprogress/single-herbs-study-guide.php"
grep -Fq 'formula-study-guide__study-route' "$TEMP_DIR/blocks/myprogress/formulas-study-guide.php"
grep -Fq 'All 51 pairs' "$TEMP_DIR/study-guide/comparisons/dui-yao.html"
grep -Fq 'Las 51 parejas' "$TEMP_DIR/study-guide/comparisons/dui-yao-es.html"
grep -Fq 'isDuiYaoNote' "$TEMP_DIR/study-guide/comparisons/study-note-navigation.js"

for relative in "${PHP_FILES[@]}"; do
    cp -a "$MOODLE_ROOT/$relative" "$BACKUP_DIR/moodle/$(basename "$relative")"
done
cp -a "$STUDY_NOTE_DIR/dui-yao.html" "$BACKUP_DIR/static/"
cp -a "$STUDY_NOTE_DIR/dui-yao-es.html" "$BACKUP_DIR/static/"
[[ -f "$STUDY_NOTE_DIR/study-note-navigation.js" ]] \
    && cp -a "$STUDY_NOTE_DIR/study-note-navigation.js" "$BACKUP_DIR/static/"
(
    cd "$STUDY_NOTE_DIR"
    find . -type f -name '*.html' -print0 \
        | tar --null -czf "$BACKUP_DIR/study-note-html-before-route.tar.gz" --files-from=-
)

for relative in "${PHP_FILES[@]}"; do
    target="$MOODLE_ROOT/$relative"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$relative" "$target"
    php -l "$target" >/dev/null
done

for file in dui-yao.html dui-yao-es.html; do
    target="$STUDY_NOTE_DIR/$file"
    owner_group="$(stat -c '%u:%g' "$target")"
    mode="$(stat -c '%a' "$target")"
    install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" \
        "$TEMP_DIR/study-guide/comparisons/$file" "$target"
done

NAV_SOURCE="$STUDY_NOTE_DIR/study-note-navigation.js"
[[ -f "$NAV_SOURCE" ]] || NAV_SOURCE="$STUDY_NOTE_DIR/comparisons.css"
NAV_OWNER_GROUP="$(stat -c '%u:%g' "$NAV_SOURCE")"
NAV_MODE="$(stat -c '%a' "$NAV_SOURCE")"
install -o "${NAV_OWNER_GROUP%:*}" -g "${NAV_OWNER_GROUP#*:}" -m "$NAV_MODE" \
    "$TEMP_DIR/study-guide/comparisons/study-note-navigation.js" "$STUDY_NOTE_DIR/study-note-navigation.js"

php -r '
$root = $argv[1];
$loader = $argv[2];
$tag = "<script src=\"{$loader}\" defer></script>";
$marker = "/study-guide/comparisons/study-note-navigation.js";
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$notes = 0; $changed = 0;
foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== "html") { continue; }
    $path = $file->getPathname();
    $html = file_get_contents($path);
    if (!preg_match("/class=[\"\x27][^\"\x27]*\\bstudy-note\\b/i", $html)) { continue; }
    $notes++;
    if (strpos($html, $marker) !== false) {
        $updated = preg_replace(
            "#<script\\s+src=[\"\x27]/study-guide/comparisons/study-note-navigation\\.js(?:\\?[^\"\x27]*)?[\"\x27]\\s+defer></script>#i",
            $tag,
            $html,
            1
        );
    } else {
        $updated = preg_replace("#</body>#i", $tag . "</body>", $html, 1, $count);
        if ($count !== 1) { throw new RuntimeException("Missing </body> in " . $path); }
    }
    if ($updated !== $html) { file_put_contents($path, $updated, LOCK_EX); $changed++; }
}
if ($notes < 1) { throw new RuntimeException("No Study Notes were found."); }
echo "Study Note routes updated: notes={$notes}, changed={$changed}\n";
' "$STUDY_NOTE_DIR" "$LOADER"

grep -Fq 'single-herbs-guide__study-route' "$MOODLE_ROOT/blocks/myprogress/single-herbs-study-guide.php"
grep -Fq 'formula-study-guide__study-route' "$MOODLE_ROOT/blocks/myprogress/formulas-study-guide.php"
grep -Fq 'All 51 pairs' "$STUDY_NOTE_DIR/dui-yao.html"
grep -Fq 'isDuiYaoNote' "$STUDY_NOTE_DIR/study-note-navigation.js"

if [[ "${SKIP_CACHE_PURGE:-0}" != "1" && -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if [[ "$(id -u)" -eq 0 ]] && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "RUTA DE ESTUDIO DE HERBOLOGÍA INSTALADA"
echo "Study Guide › Bensky Map › Herbs → Duì Yào → Formulas › Current page"
echo "Respaldo: $BACKUP_DIR"
echo "Mapa: https://www.acupuncturetests.com/blocks/myprogress/herb-formula-category-map.php?route=$REVISION"
echo "Duì Yào: https://www.acupuncturetests.com/study-guide/comparisons/du-zhong-xu-duan-dui-yao.html?route=$REVISION"
