#!/usr/bin/env bash
set -euo pipefail

# Deploy the corrected CF-0006 source item and the strengthened automatic-review flow.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILES=(
    "blocks/myprogress/admin-herbology-question-bank.php"
    "blocks/menu/herbology-domain-candidates.json"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_cf0006_review_fix.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv sha256sum id awk dirname basename; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

for relative_file in "${RELATIVE_FILES[@]}"; do
    [[ -f "$MOODLE_ROOT/$relative_file" ]] || {
        echo "ERROR: no existe $MOODLE_ROOT/$relative_file" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/cf0006-review-fix-$TIMESTAMP"
INSTALLED_FILES=()

cleanup() {
    rm -rf -- "$TEMP_DIR"
    for relative_file in "${RELATIVE_FILES[@]}"; do
        rm -f -- "$MOODLE_ROOT/$(dirname "$relative_file")/.$(basename "$relative_file").cf0006.$$"
    done
}

on_error() {
    status=$?
    line=$1
    for relative_file in "${INSTALLED_FILES[@]}"; do
        if [[ -f "$BACKUP_DIR/$relative_file" ]]; then
            cp -a -- "$BACKUP_DIR/$relative_file" "$MOODLE_ROOT/$relative_file"
            echo "RECUPERACIÓN: se restauró $relative_file" >&2
        fi
    done
    cleanup
    echo "ERROR: despliegue de CF-0006 detenido en la línea $line (código $status)." >&2
    exit "$status"
}

trap 'on_error $LINENO' ERR
trap cleanup EXIT

for relative_file in "${RELATIVE_FILES[@]}"; do
    source_file="$TEMP_DIR/$relative_file"
    mkdir -p "$(dirname "$source_file")"
    curl -fsSL \
        -H 'Cache-Control: no-cache' \
        -H 'Pragma: no-cache' \
        "$REPOSITORY/$REVISION/$relative_file?revision=$REVISION" \
        -o "$source_file"
done

QUESTION_BANK_SOURCE="$TEMP_DIR/${RELATIVE_FILES[0]}"
SOURCE_DATA="$TEMP_DIR/${RELATIVE_FILES[1]}"
php -l "$QUESTION_BANK_SOURCE" >/dev/null
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data)) { exit(1); }' "$SOURCE_DATA"
grep -Fq "ncbahm-mock-editor-4.1" "$QUESTION_BANK_SOURCE"
grep -Fq "sourceFeedbackMissing" "$QUESTION_BANK_SOURCE"
grep -Fq '"questionId": "CF-0006"' "$SOURCE_DATA"
grep -Fq 'Which statement correctly differentiates Gui Zhi Tang' "$SOURCE_DATA"
grep -Fq 'Option D: Incorrect. It reverses the formulas' "$SOURCE_DATA"
if grep -A 90 -F '"questionId": "CF-0006"' "$SOURCE_DATA" | grep -Eq 'None of above|None of the above|Ninguna de las anteriores'; then
    echo "ERROR: CF-0006 todavía contiene una respuesta combinada prohibida." >&2
    exit 1
fi

mkdir -p "$BACKUP_DIR"
for relative_file in "${RELATIVE_FILES[@]}"; do
    target="$MOODLE_ROOT/$relative_file"
    source_file="$TEMP_DIR/$relative_file"
    backup_file="$BACKUP_DIR/$relative_file"
    staged_file="$MOODLE_ROOT/$(dirname "$relative_file")/.$(basename "$relative_file").cf0006.$$"
    mkdir -p "$(dirname "$backup_file")"
    cp -a -- "$target" "$backup_file"
    target_owner_group="$(stat -c '%u:%g' "$target")"
    target_mode="$(stat -c '%a' "$target")"
    install \
        -o "${target_owner_group%:*}" \
        -g "${target_owner_group#*:}" \
        -m "$target_mode" \
        "$source_file" \
        "$staged_file"
    cmp -s "$source_file" "$staged_file"
    mv -f -- "$staged_file" "$target"
    INSTALLED_FILES+=("$relative_file")
done

cmp -s "$QUESTION_BANK_SOURCE" "$MOODLE_ROOT/${RELATIVE_FILES[0]}"
cmp -s "$SOURCE_DATA" "$MOODLE_ROOT/${RELATIVE_FILES[1]}"
php -l "$MOODLE_ROOT/${RELATIVE_FILES[0]}" >/dev/null
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data)) { exit(1); }' "$MOODLE_ROOT/${RELATIVE_FILES[1]}"

if [[ -f "$MOODLE_ROOT/admin/cli/purge_caches.php" ]]; then
    if command -v runuser >/dev/null 2>&1 && id www-data >/dev/null 2>&1; then
        runuser -u www-data -- php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    else
        php "$MOODLE_ROOT/admin/cli/purge_caches.php"
    fi
fi

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

INSTALLED_SHA256="$(sha256sum "$MOODLE_ROOT/${RELATIVE_FILES[1]}" | awk '{print $1}')"
echo "OK: CF-0006 ya no contiene None of above ni su equivalente en español."
echo "OK: CF-0006 incluye feedback completo para A, B, C y D en ambos idiomas."
echo "OK: la revisión automática fuerza una segunda comprobación cuando falta feedback o existe una respuesta combinada prohibida."
echo "OK: revisión instalada: $REVISION"
echo "SHA256 DATOS: $INSTALLED_SHA256"
if [[ "${#WEB_RUNTIME_RELOADED[@]}" -gt 0 ]]; then
    echo "RUNTIME RECARGADO: ${WEB_RUNTIME_RELOADED[*]}"
else
    echo "AVISO: no se detectó un servicio web para recargar; Moodle cache sí fue purgada."
fi
echo "RESPALDO: $BACKUP_DIR"
