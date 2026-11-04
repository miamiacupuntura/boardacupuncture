#!/usr/bin/env bash
set -euo pipefail

REVISION="${1:-bensky-chapter-four}"
MOODLE_ROOT="${2:-/var/www/html}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
STUDY_ROOT="$MOODLE_ROOT/study-guide/comparisons"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
SCRIPT_RELATIVE="study-guide/comparisons/study-note-navigation.js"
LOADER='/study-guide/comparisons/study-note-navigation.js?v=20261104-1'

for command in curl php tar find install; do
    command -v "$command" >/dev/null 2>&1 || { echo "Missing required command: $command" >&2; exit 1; }
done
if [[ ! -f "$MOODLE_ROOT/config.php" ]]; then
    echo "Moodle config.php was not found at $MOODLE_ROOT" >&2
    exit 1
fi
if [[ ! -d "$STUDY_ROOT" ]]; then
    echo "Study Note directory was not found at $STUDY_ROOT" >&2
    exit 1
fi

TEMP_DIR="$(mktemp -d)"
BACKUP_DIR="$BACKUP_ROOT/study-note-navigation-$(date +%Y%m%d-%H%M%S)"
trap 'rm -rf "$TEMP_DIR"' EXIT
mkdir -p "$TEMP_DIR/$(dirname "$SCRIPT_RELATIVE")" "$BACKUP_DIR"

curl -fsSL "$REPOSITORY/$REVISION/$SCRIPT_RELATIVE" -o "$TEMP_DIR/$SCRIPT_RELATIVE"
grep -Fq 'data-study-note-navigation' "$TEMP_DIR/$SCRIPT_RELATIVE"
grep -Fq 'study-note-activity-nav' "$TEMP_DIR/$SCRIPT_RELATIVE"

(
    cd "$MOODLE_ROOT"
    find study-guide/comparisons -type f -name '*.html' -print0 \
        | tar --null -czf "$BACKUP_DIR/study-note-html-before-navigation.tar.gz" --files-from=-
)
if [[ -f "$STUDY_ROOT/study-note-navigation.js" ]]; then
    cp -a "$STUDY_ROOT/study-note-navigation.js" "$BACKUP_DIR/study-note-navigation.js.previous"
fi

install -m 0644 "$TEMP_DIR/$SCRIPT_RELATIVE" "$STUDY_ROOT/study-note-navigation.js"
if [[ -f "$STUDY_ROOT/comparisons.css" ]]; then
    chown --reference="$STUDY_ROOT/comparisons.css" "$STUDY_ROOT/study-note-navigation.js"
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
' "$STUDY_ROOT" "$LOADER"

php "$MOODLE_ROOT/admin/cli/purge_caches.php" >/dev/null
echo "Backup: $BACKUP_DIR/study-note-html-before-navigation.tar.gz"
echo "Global Study Note navigation deployed from revision: $REVISION"
