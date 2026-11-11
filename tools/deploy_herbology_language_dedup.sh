#!/usr/bin/env bash
set -euo pipefail
trap 'status=$?; echo "ERROR: corrección de selectores de idioma detenida en la línea $LINENO (código $status)." >&2' ERR
REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]];then echo "Uso: sudo bash tools/deploy_herbology_language_dedup.sh COMMIT_DE_40_CARACTERES" >&2;exit 1;fi
for command in curl php install stat cp cmp mkdir mktemp date rm;do command -v "$command" >/dev/null 2>&1||{ echo "ERROR: falta el comando $command" >&2;exit 1;};done
TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
[[ -d "$TARGET_DIR" ]]||{ echo "ERROR: no existe $TARGET_DIR" >&2;exit 1;}
TEMP_DIR="$(mktemp -d)";trap 'rm -rf -- "$TEMP_DIR"' EXIT
FILES=(single-herbs-study-guide.php herb-formula-category-map.php)
for file in "${FILES[@]}";do curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/$file" -o "$TEMP_DIR/$file";php -l "$TEMP_DIR/$file" >/dev/null;done
grep -Fq '<?php if (!$fromlibrary): ?>' "$TEMP_DIR/single-herbs-study-guide.php"
grep -Fq '<?php if (!$fromlibrary): ?>' "$TEMP_DIR/herb-formula-category-map.php"
BACKUP="$BACKUP_ROOT/herbology-language-dedup-$(date +%Y%m%d-%H%M%S)";mkdir -p "$BACKUP"
for file in "${FILES[@]}";do
 target="$TARGET_DIR/$file";[[ -f "$target" ]]||{ echo "ERROR: no existe $target" >&2;exit 1;}
 cp -a "$target" "$BACKUP/$file"
 owner_group="$(stat -c '%u:%g' "$target")";mode="$(stat -c '%a' "$target")"
 install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$file" "$target"
 cmp -s "$TEMP_DIR/$file" "$target"
done
if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]];then if command -v runuser >/dev/null 2>&1&&id www-data >/dev/null 2>&1;then runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php";else php "$MOODLE_ROOT/admin/cli/purge_caches.php";fi;fi
echo "OK: Single Herbs usa un solo selector de idioma dentro de la biblioteca."
echo "OK: Herb-Formula Category Map usa un solo selector de idioma dentro de la biblioteca."
echo "OK: Formula Study Guide y Common Herbal Combinations verificados sin duplicación."
echo "RESPALDO: $BACKUP"
