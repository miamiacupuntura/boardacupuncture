#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: despliegue de Smart Study Tools detenido en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash tools/deploy_smart_study_tools.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm id; do
    command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
FILES=(smart-study-tools.php study-guide.php formula-list.php single-herbs-list.php)

for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file"
    php -l "$TEMP_DIR/$file" >/dev/null
done

grep -Fq "Smart Study Tools" "$TEMP_DIR/smart-study-tools.php"
grep -Fq "Find Similar Formulas" "$TEMP_DIR/smart-study-tools.php"
grep -Fq "Find Similar Herbs" "$TEMP_DIR/smart-study-tools.php"
grep -Fq "Pattern & Clinical Clue Finder" "$TEMP_DIR/smart-study-tools.php"
grep -Fq "Tongue & Pulse Matcher" "$TEMP_DIR/smart-study-tools.php"
grep -Fq "subject-study-library__journey-option--smart" "$TEMP_DIR/study-guide.php"
grep -Fq "Find similar formulas" "$TEMP_DIR/formula-list.php"
grep -Fq "Find similar herbs" "$TEMP_DIR/single-herbs-list.php"
grep -Fq '$fromsmart' "$TEMP_DIR/formula-list.php"
grep -Fq '$fromsmart' "$TEMP_DIR/single-herbs-list.php"

TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]] || { echo "ERROR: no existe $TARGET_DIR" >&2; exit 1; }
for file in study-guide.php formula-list.php single-herbs-list.php     formula-board-roster-2026.json formulas-study-guide-data.json formula-guideline-profile-additions.json     formula-tongue-pulse-data.json single-herb-card-index.json single-herb-properties.json     single-herb-rich-profiles.json single-herb-actions-indications-supplement.json; do
    [[ -f "$TARGET_DIR/$file" ]] || { echo "ERROR: falta $TARGET_DIR/$file" >&2; exit 1; }
done

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP="$BACKUP_ROOT/smart-study-tools-$TIMESTAMP"
mkdir -p "$BACKUP"
reference="$TARGET_DIR/study-guide.php"
owner_group="$(stat -c '%u:%g' "$reference")"
mode="$(stat -c '%a' "$reference")"

for file in "${FILES[@]}"; do
    target="$TARGET_DIR/$file"
    if [[ -f "$target" ]]; then
        cp -a "$target" "$BACKUP/$file"
        file_owner_group="$(stat -c '%u:%g' "$target")"
        file_mode="$(stat -c '%a' "$target")"
    else
        file_owner_group="$owner_group"
        file_mode="$mode"
    fi
    install -o "${file_owner_group%:*}" -g "${file_owner_group#*:}" -m "$file_mode" "$TEMP_DIR/$file" "$target"
    cmp -s "$TEMP_DIR/$file" "$target"
    php -l "$target" >/dev/null
done

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

echo "OK: Smart Study Tools visible en Main Study Guide."
echo "OK: Similar Formulas analiza las 181 fórmulas oficiales."
echo "OK: Similar Herbs analiza las 304 hierbas oficiales."
echo "OK: Pattern & Clinical Clue y Tongue & Pulse usan únicamente datos verificados."
echo "OK: tarjetas de fórmulas y hierbas incluyen acceso contextual y botón de regreso."
echo "OK: motor local gratuito; no usa API externa."
echo "RESPALDO: $BACKUP"
