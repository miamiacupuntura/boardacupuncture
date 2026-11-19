#!/usr/bin/env bash
set -euo pipefail

# Deploy only the NCBAHM Question Bank. This recovery path intentionally
# avoids unrelated Study Guide and menu files so that a failure elsewhere in
# the full Smart Study Tools release cannot leave the question bank stale.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILE="blocks/myprogress/admin-herbology-question-bank.php"
TARGET="$MOODLE_ROOT/$RELATIVE_FILE"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_ncbahm_question_bank.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv sha256sum id; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

[[ -f "$TARGET" ]] || {
    echo "ERROR: no existe $TARGET" >&2
    exit 1
}

TEMP_DIR="$(mktemp -d)"
SOURCE_FILE="$TEMP_DIR/admin-herbology-question-bank.php"
STAGED_FILE="$MOODLE_ROOT/blocks/myprogress/.admin-herbology-question-bank.php.deploy.$$"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/ncbahm-question-bank-$TIMESTAMP"
BACKUP_FILE="$BACKUP_DIR/admin-herbology-question-bank.php"
INSTALLED=0

cleanup() {
    rm -rf -- "$TEMP_DIR"
    rm -f -- "$STAGED_FILE"
}

on_error() {
    status=$?
    line=$1
    if [[ "$INSTALLED" -eq 1 && -f "$BACKUP_FILE" ]]; then
        cp -a -- "$BACKUP_FILE" "$TARGET"
        echo "RECUPERACIÓN: se restauró $BACKUP_FILE" >&2
    fi
    cleanup
    echo "ERROR: despliegue aislado del NCBAHM Question Bank detenido en la línea $line (código $status)." >&2
    exit "$status"
}

trap 'on_error $LINENO' ERR
trap cleanup EXIT

curl -fsSL \
    -H 'Cache-Control: no-cache' \
    -H 'Pragma: no-cache' \
    "$REPOSITORY/$REVISION/$RELATIVE_FILE?revision=$REVISION" \
    -o "$SOURCE_FILE"

php -l "$SOURCE_FILE" >/dev/null

# These markers identify the current consecutive Exam 3 workflow and the
# editorial safeguards. Their absence means the requested revision is not the
# recovery build expected by this installer.
grep -Fq "\$aipromptversion = 'ncbahm-mock-editor-3.4';" "$SOURCE_FILE"
grep -Fq "'exam_project_pending'" "$SOURCE_FILE"
grep -Fq 'if ($publishedexam2 === null):' "$SOURCE_FILE"
grep -Fq 'question-bank__active-filter-actions' "$SOURCE_FILE"
grep -Fq 'findprohibitedanswerchoice' "$SOURCE_FILE"
grep -Fq 'hasprohibitednegativewording' "$SOURCE_FILE"
grep -Fq 'For channel-combination questions' "$SOURCE_FILE"
grep -Fq 'buildsimilaritysearch' "$SOURCE_FILE"
grep -Fq 'value="mergequestions"' "$SOURCE_FILE"
grep -Fq 'value="undomergequestions"' "$SOURCE_FILE"
grep -Fq 'Similar question and answer finder' "$SOURCE_FILE"
grep -Fq 'customcanpublish' "$SOURCE_FILE"
grep -Fq 'Complete the question, four answer choices, answer key and explanation before publishing.' "$SOURCE_FILE"
grep -Fq 'Always show the exact question that just completed automatic review.' "$SOURCE_FILE"

mkdir -p "$BACKUP_DIR"
cp -a -- "$TARGET" "$BACKUP_FILE"

target_owner_group="$(stat -c '%u:%g' "$TARGET")"
target_mode="$(stat -c '%a' "$TARGET")"
install \
    -o "${target_owner_group%:*}" \
    -g "${target_owner_group#*:}" \
    -m "$target_mode" \
    "$SOURCE_FILE" \
    "$STAGED_FILE"

cmp -s "$SOURCE_FILE" "$STAGED_FILE"
php -l "$STAGED_FILE" >/dev/null
mv -f -- "$STAGED_FILE" "$TARGET"
INSTALLED=1

cmp -s "$SOURCE_FILE" "$TARGET"
php -l "$TARGET" >/dev/null
grep -Fq "\$aipromptversion = 'ncbahm-mock-editor-3.4';" "$TARGET"
grep -Fq 'if ($publishedexam2 === null):' "$TARGET"
grep -Fq "'exam_project_pending'" "$TARGET"
grep -Fq 'buildsimilaritysearch' "$TARGET"
grep -Fq 'value="mergequestions"' "$TARGET"
grep -Fq 'customcanpublish' "$TARGET"
grep -Fq 'Always show the exact question that just completed automatic review.' "$TARGET"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

# Moodle cache purging does not necessarily invalidate a PHP-FPM OPcache.
WEB_RUNTIME_RELOADED=()
if command -v systemctl >/dev/null 2>&1; then
    while IFS= read -r php_service; do
        [[ -n "$php_service" ]] || continue
        if systemctl reload "$php_service" >/dev/null 2>&1 \
                || systemctl restart "$php_service" >/dev/null 2>&1; then
            WEB_RUNTIME_RELOADED+=("$php_service")
        fi
    done < <(systemctl list-units --type=service --state=running --no-legend 2>/dev/null \
        | awk '$1 ~ /^php[0-9.]+-fpm\.service$/ {print $1}')
    for web_service in apache2.service httpd.service; do
        if systemctl is-active --quiet "$web_service" 2>/dev/null \
                && systemctl reload "$web_service" >/dev/null 2>&1; then
            WEB_RUNTIME_RELOADED+=("$web_service")
        fi
    done
fi

INSTALLED_SHA256="$(sha256sum "$TARGET" | awk '{print $1}')"
SOURCE_SHA256="$(sha256sum "$SOURCE_FILE" | awk '{print $1}')"
[[ "$INSTALLED_SHA256" == "$SOURCE_SHA256" ]]

echo "OK: NCBAHM Question Bank recuperado con la revisión $REVISION."
echo "OK: Exam 2 publicado queda fuera del constructor; Exam 3 usa revisión consecutiva."
echo "OK: buscador global de respuestas similares y fusión recuperable instalados."
echo "OK: versión editorial ncbahm-mock-editor-3.4 instalada."
echo "SHA256: $INSTALLED_SHA256"
if [[ "${#WEB_RUNTIME_RELOADED[@]}" -gt 0 ]]; then
    echo "RUNTIME RECARGADO: ${WEB_RUNTIME_RELOADED[*]}"
else
    echo "AVISO: no se detectó un servicio web para recargar; Moodle cache sí fue purgada."
fi
echo "RESPALDO: $BACKUP_FILE"

