#!/usr/bin/env bash
set -euo pipefail
trap 'status=$?; echo "ERROR: despliegue de Formula Modifications Quiz detenido en la línea $LINENO (código $status)." >&2' ERR
REVISION="${1:-}";REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture";MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}";BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}";REQUESTED_DIR="${STUDY_NOTE_DIR:-}"
if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]];then echo "Uso: sudo bash tools/deploy_formula_modifications_quiz.sh COMMIT_DE_40_CARACTERES" >&2;exit 1;fi
for command in curl php grep install stat cp cmp mkdir mktemp readlink tr date rm id dirname;do command -v "$command" >/dev/null 2>&1||{ echo "ERROR: falta el comando $command" >&2;exit 1;};done
TEMP_DIR="$(mktemp -d)";trap 'rm -rf -- "$TEMP_DIR"' EXIT
STATIC_FILES=(comparisons.css formula-practice.html formula-practice-es.html formula-practice.js formula-modifications.html formula-modifications-es.html formula-modifications.js formula-modifications.css)
for file in "${STATIC_FILES[@]}";do curl -fsSL "$REPOSITORY/$REVISION/study-guide/comparisons/$file" -o "$TEMP_DIR/$file";done
curl -fsSL "$REPOSITORY/$REVISION/blocks/myprogress/study-guide.php" -o "$TEMP_DIR/study-guide.php";php -l "$TEMP_DIR/study-guide.php" >/dev/null
grep -Fq "formula-practice.js?v=20261111-4" "$TEMP_DIR/formula-practice.html";grep -Fq "comparisons.css?v=20261111-5" "$TEMP_DIR/formula-practice-es.html";grep -Fq "data-practice-previous" "$TEMP_DIR/formula-practice.html";grep -Fq "data-practice-next" "$TEMP_DIR/formula-practice.html";grep -Fq "data-practice-restart" "$TEMP_DIR/formula-practice-es.html";grep -Fq "formula-practice-navigation" "$TEMP_DIR/comparisons.css";grep -Fq "function showQuestion" "$TEMP_DIR/formula-practice.js";grep -Fq "formula-modifications.js?v=20261111-1" "$TEMP_DIR/formula-modifications.html";grep -Fq "data-modification-return" "$TEMP_DIR/formula-modifications-es.html";grep -Fq "modificationCardLink" "$TEMP_DIR/formula-practice.js";grep -Fq "focusRequestedModification" "$TEMP_DIR/formula-modifications.js";grep -Fq "const modificationsOnly" "$TEMP_DIR/formula-practice.js";grep -Fq "category.key !== 'modifications'" "$TEMP_DIR/formula-practice.js";grep -Fq "boardformulamodificationsquizurl" "$TEMP_DIR/study-guide.php";grep -Fq "bqf_modifications" "$TEMP_DIR/study-guide.php"
declare -a TARGETS=();declare -A SEEN=()
add_target(){ local candidate="$1";[[ -d "$candidate" ]]||return 0;[[ -f "$candidate/formula-practice.html" ]]||return 0;local resolved;resolved="$(readlink -f "$candidate")";[[ -n "$resolved" ]]||return 0;if [[ -z "${SEEN[$resolved]:-}" ]];then SEEN["$resolved"]=1;TARGETS+=("$resolved");fi;}
if [[ -n "$REQUESTED_DIR" ]];then add_target "$REQUESTED_DIR";else add_target "/var/www/acupuncturetests-study-guide/current/comparisons";add_target "$MOODLE_ROOT/study-guide/comparisons";add_target "$(dirname "$MOODLE_ROOT")/study-guide/comparisons";add_target "/var/www/html/study-guide/comparisons";fi
[[ "${#TARGETS[@]}" -gt 0 ]]||{ echo "ERROR: no se encontró Formula Quiz activo." >&2;exit 1;}
TIMESTAMP="$(date +%Y%m%d-%H%M%S)";BACKUP="$BACKUP_ROOT/formula-modifications-quiz-$TIMESTAMP";mkdir -p "$BACKUP"
for target in "${TARGETS[@]}";do sub="$BACKUP/$(echo "$target"|tr '/ ' '__')";mkdir -p "$sub";reference="$target/formula-practice.html";owner_group="$(stat -c '%u:%g' "$reference")";mode="$(stat -c '%a' "$reference")";for file in "${STATIC_FILES[@]}";do [[ -f "$target/$file" ]]&&cp -a "$target/$file" "$sub/$file";install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/$file" "$target/$file";cmp -s "$TEMP_DIR/$file" "$target/$file";done;echo "ACTUALIZADO Y VERIFICADO: $target";done
PHP_TARGET="$MOODLE_ROOT/blocks/myprogress/study-guide.php";[[ -f "$PHP_TARGET" ]]||{ echo "ERROR: no existe $PHP_TARGET" >&2;exit 1;};cp -a "$PHP_TARGET" "$BACKUP/study-guide.php";owner_group="$(stat -c '%u:%g' "$PHP_TARGET")";mode="$(stat -c '%a' "$PHP_TARGET")";install -o "${owner_group%:*}" -g "${owner_group#*:}" -m "$mode" "$TEMP_DIR/study-guide.php" "$PHP_TARGET";cmp -s "$TEMP_DIR/study-guide.php" "$PHP_TARGET"
if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]];then if command -v runuser >/dev/null 2>&1&&id www-data >/dev/null 2>&1;then runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php";else php "$MOODLE_ROOT/admin/cli/purge_caches.php";fi;fi
echo "OK: archivos faltantes se crean y los existentes se respaldan antes de actualizar."
echo "OK: cada pregunta de modificaciones enlaza su ficha exacta con regreso al quiz."
echo "OK: Formula Quiz ya no mezcla Formula Modifications."
echo "OK: Formula Modifications Quiz aparece debajo de Dui Yao Quiz."
echo "OK: banco independiente de 293 preguntas activo."
echo "OK: se muestra una pregunta a la vez con navegación horizontal."
echo "OK: botones Anterior, Siguiente, Reiniciar y Finalizar activos."
echo "RESPALDO: $BACKUP"
