#!/usr/bin/env bash
set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue de Dui Yao Quiz detenido en la línea $LINENO (código $status)." >&2' ERR
REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
REQUESTED_DIR="${STUDY_NOTE_DIR:-}"
if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then echo "Uso: sudo bash tools/deploy_dui_yao_quiz.sh COMMIT_DE_40_CARACTERES" >&2; exit 1; fi
for command in curl grep install stat cp cmp mkdir mktemp readlink tr date php rm id; do command -v "$command" >/dev/null 2>&1 || { echo "ERROR: falta el comando $command" >&2; exit 1; }; done
TEMP_DIR="$(mktemp -d)"; trap 'rm -rf -- "$TEMP_DIR"' EXIT
FILES=(dui-yao-practice.html dui-yao-practice-es.html dui-yao-practice.js dui-yao-option-review.html dui-yao-option-review-es.html dui-yao-data.json)
for file in "${FILES[@]}"; do curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/$file"; done
grep -Fq "dui-yao-practice.js?v=20261111-4" "$TEMP_DIR/dui-yao-practice.html"
grep -Fq "dui-yao-practice.js?v=20261111-4" "$TEMP_DIR/dui-yao-practice-es.html"
grep -Fq "function appendReviewFeedback" "$TEMP_DIR/dui-yao-practice.js"
grep -Fq "prompt: copy.actionPrompt" "$TEMP_DIR/dui-yao-practice.js"
grep -Fq "reviewType: 'pair'" "$TEMP_DIR/dui-yao-practice.js"
grep -Fq "Review the four options" "$TEMP_DIR/dui-yao-practice.js"
grep -Fq "Back to quiz" "$TEMP_DIR/dui-yao-option-review.html"
grep -Fq "Regresar al quiz" "$TEMP_DIR/dui-yao-option-review-es.html"
php -r '$rows=json_decode(file_get_contents($argv[1]),true,512,JSON_THROW_ON_ERROR);if(count($rows)!==51){fwrite(STDERR,"ERROR: se esperaban 51 parejas Dui Yao.\n");exit(1);}' "$TEMP_DIR/dui-yao-data.json"
declare -a TARGETS=(); declare -A SEEN=()
add_target(){ local candidate="$1"; [[ -d "$candidate" ]]||return 0; [[ -f "$candidate/dui-yao-practice.html" || -f "$candidate/dui-yao.html" ]]||return 0; local resolved; resolved="$(readlink -f "$candidate")"; [[ -n "$resolved" ]]||return 0; if [[ -z "${SEEN[$resolved]:-}" ]];then SEEN["$resolved"]=1;TARGETS+=("$resolved");fi; }
if [[ -n "$REQUESTED_DIR" ]];then add_target "$REQUESTED_DIR";else add_target "/var/www/acupuncturetests-study-guide/current/comparisons";add_target "$MOODLE_ROOT/study-guide/comparisons";add_target "$(dirname "$MOODLE_ROOT")/study-guide/comparisons";add_target "/var/www/html/study-guide/comparisons";fi
if [[ "${#TARGETS[@]}" -eq 0 ]];then echo "ERROR: no se encontró el módulo Dui Yao activo." >&2;exit 1;fi
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
for target in "${TARGETS[@]}";do
 reference="$target/dui-yao-practice.html";[[ -f "$reference" ]]||reference="$target/dui-yao.html"
 backup="$BACKUP_ROOT/dui-yao-quiz-$TIMESTAMP/$(echo "$target"|tr '/ ' '__')";mkdir -p "$backup"
 owner_group="$(stat -c '%u:%g' "$reference")";mode="$(stat -c '%a' "$reference")"
 for file in "${FILES[@]}";do [[ -f "$target/$file" ]]&&cp -a "$target/$file" "$backup/$file";install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$file" "$target/$file";cmp -s "$TEMP_DIR/$file" "$target/$file";done
 echo "ACTUALIZADO Y VERIFICADO: $target";echo "RESPALDO: $backup"
done
if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]];then if command -v runuser >/dev/null 2>&1&&id www-data >/dev/null 2>&1;then runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php";else php "$MOODLE_ROOT/admin/cli/purge_caches.php";fi;fi
echo "OK: Dui Yao Quiz build 20261111-4 activo."
echo "OK: 51 parejas oficiales verificadas."
echo "OK: feedback breve, Why not, cuatro fichas y regreso al mismo quiz."
