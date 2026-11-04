#!/usr/bin/env bash
set -euo pipefail

REVISION="${1:-bensky-chapter-four}"
TARGET_DIR="${2:-${STUDY_NOTE_DIR:-}}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
SCRIPT_RELATIVE="study-guide/comparisons/study-note-navigation.js"
MANIFEST_RELATIVE="study-guide/comparisons/study-note-navigation-manifest.js"
LOADER='/study-guide/comparisons/study-note-navigation.js?v=20261104-5'

for command in curl php tar find install; do
    command -v "$command" >/dev/null 2>&1 || { echo "Missing required command: $command" >&2; exit 1; }
done
if [[ -z "$TARGET_DIR" ]]; then
    CANDIDATE_DIRS=(
        "/var/www/acupuncturetests-study-guide/current/comparisons"
        "/var/www/html/moodle/study-guide/comparisons"
        "/var/www/html/study-guide/comparisons"
    )
    for candidate in "${CANDIDATE_DIRS[@]}"; do
        if [[ -f "$candidate/dui-yao.html" && -f "$candidate/comparisons.css" ]]; then
            TARGET_DIR="$candidate"
            break
        fi
    done
fi
if [[ -z "$TARGET_DIR" ]]; then
    ACTIVE_NOTE="$(find /var/www -type f -path '*/study-guide/comparisons/dui-yao.html' -print -quit 2>/dev/null || true)"
    [[ -n "$ACTIVE_NOTE" ]] && TARGET_DIR="$(dirname "$ACTIVE_NOTE")"
fi
if [[ -z "$TARGET_DIR" || ! -f "$TARGET_DIR/dui-yao.html" || ! -f "$TARGET_DIR/comparisons.css" ]]; then
    echo "Active Study Note directory was not found. Pass it as the second argument or set STUDY_NOTE_DIR." >&2
    exit 1
fi
echo "Active Study Note directory: $TARGET_DIR"

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/study-note-navigation-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/$(dirname "$SCRIPT_RELATIVE")" "$BACKUP_DIR"

curl -fsSL "$REPOSITORY/$REVISION/$SCRIPT_RELATIVE" -o "$TEMP_DIR/$SCRIPT_RELATIVE"
curl -fsSL "$REPOSITORY/$REVISION/$MANIFEST_RELATIVE" -o "$TEMP_DIR/$MANIFEST_RELATIVE"
grep -Fq 'data-study-note-navigation' "$TEMP_DIR/$SCRIPT_RELATIVE"
grep -Fq 'study-note-activity-nav' "$TEMP_DIR/$SCRIPT_RELATIVE"
grep -Fq 'window.studyNoteNavigationManifest=' "$TEMP_DIR/$MANIFEST_RELATIVE"

(
    cd "$TARGET_DIR"
    find . -type f -name '*.html' -print0 \
        | tar --null -czf "$BACKUP_DIR/study-note-html-before-navigation.tar.gz" --files-from=-
)
if [[ -f "$TARGET_DIR/study-note-navigation.js" ]]; then
    cp -a "$TARGET_DIR/study-note-navigation.js" "$BACKUP_DIR/study-note-navigation.js.previous"
fi
if [[ -f "$TARGET_DIR/study-note-navigation-manifest.js" ]]; then
    cp -a "$TARGET_DIR/study-note-navigation-manifest.js" "$BACKUP_DIR/study-note-navigation-manifest.js.previous"
fi

install -m 0644 "$TEMP_DIR/$SCRIPT_RELATIVE" "$TARGET_DIR/study-note-navigation.js"
install -m 0644 "$TEMP_DIR/$MANIFEST_RELATIVE" "$TARGET_DIR/study-note-navigation-manifest.js"
if [[ -f "$TARGET_DIR/comparisons.css" ]]; then
    chown --reference="$TARGET_DIR/comparisons.css" "$TARGET_DIR/study-note-navigation.js"
    chown --reference="$TARGET_DIR/comparisons.css" "$TARGET_DIR/study-note-navigation-manifest.js"
fi

php -r '
$root = $argv[1];
$loader = $argv[2];
$tag = "<script src=\"{$loader}\" defer></script>";
$marker = "/study-guide/comparisons/study-note-navigation.js";
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$notes = 0; $changed = 0; $installed = 0;
foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== "html") { continue; }
    $path = $file->getPathname();
    $html = file_get_contents($path);
    if (!preg_match("/class=[\"\x27][^\"\x27]*\\bstudy-note\\b/i", $html)) { continue; }
    $notes++;
    if (strpos($html, $marker) !== false) {
        $normalized = preg_replace(
            "#<script\\s+src=[\"\x27]/study-guide/comparisons/study-note-navigation\\.js(?:\\?[^\"\x27]*)?[\"\x27]\\s+defer></script>#i",
            $tag,
            $html,
            1
        );
        if ($normalized !== $html) { file_put_contents($path, $normalized); $changed++; }
        $installed++;
        continue;
    }
    $updated = preg_replace("#</body>#i", $tag . "</body>", $html, 1, $count);
    if ($count !== 1) { throw new RuntimeException("Missing </body> in " . $path); }
    file_put_contents($path, $updated);
    $changed++; $installed++;
}
if ($notes < 1 || $installed !== $notes) {
    throw new RuntimeException("Incomplete Study Note coverage: notes={$notes}, installed={$installed}");
}
echo "Study Note navigation installed: notes={$notes}, changed={$changed}\n";
' "$TARGET_DIR" "$LOADER"

for moodle_root in /var/www/html/moodle /var/www/html; do
    if [[ -f "$moodle_root/config.php" && -f "$moodle_root/admin/cli/purge_caches.php" ]]; then
        php "$moodle_root/admin/cli/purge_caches.php" >/dev/null
        break
    fi
done
echo "Backup: $BACKUP_DIR/study-note-html-before-navigation.tar.gz"
echo "Global Study Note navigation deployed from revision: $REVISION"
echo "Test: https://www.acupuncturetests.com/study-guide/comparisons/du-zhong-xu-duan-dui-yao.html?nav=20261104-5"
