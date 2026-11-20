#!/usr/bin/env bash
set -euo pipefail

# Deploy the field-by-field question comparison and reversible merge workflow.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILES=(
    "blocks/myprogress/admin-herbology-question-bank.php"
    "blocks/myprogress/herbology-question-overrides.php"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_side_by_side_question_merge.sh COMMIT_DE_40_CARACTERES" >&2
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
BACKUP_DIR="$BACKUP_ROOT/side-by-side-question-merge-$TIMESTAMP"
INSTALLED_FILES=()

cleanup() {
    rm -rf -- "$TEMP_DIR"
    for relative_file in "${RELATIVE_FILES[@]}"; do
        rm -f -- "$MOODLE_ROOT/$(dirname "$relative_file")/.$(basename "$relative_file").merge.$$"
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
    echo "ERROR: despliegue de la tabla de fusión detenido en la línea $line (código $status)." >&2
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
    php -l "$source_file" >/dev/null
done

QUESTION_BANK_SOURCE="$TEMP_DIR/${RELATIVE_FILES[0]}"
OVERRIDES_SOURCE="$TEMP_DIR/${RELATIVE_FILES[1]}"
grep -Fq "formaction === 'composemergequestions'" "$QUESTION_BANK_SOURCE"
grep -Fq 'question-bank__merge-table' "$QUESTION_BANK_SOURCE"
grep -Fq 'Aprobar fusión y eliminar el resto' "$QUESTION_BANK_SOURCE"
grep -Fq 'Compare fields side by side' "$QUESTION_BANK_SOURCE"
grep -Fq 'mergeKeptRestoreRecord' "$QUESTION_BANK_SOURCE"
grep -Fq 'mergeProjectRestore' "$QUESTION_BANK_SOURCE"
grep -Fq "'source', 'sourceCard'" "$OVERRIDES_SOURCE"

mkdir -p "$BACKUP_DIR"
for relative_file in "${RELATIVE_FILES[@]}"; do
    target="$MOODLE_ROOT/$relative_file"
    source_file="$TEMP_DIR/$relative_file"
    backup_file="$BACKUP_DIR/$relative_file"
    staged_file="$MOODLE_ROOT/$(dirname "$relative_file")/.$(basename "$relative_file").merge.$$"
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
    php -l "$staged_file" >/dev/null
    mv -f -- "$staged_file" "$target"
    INSTALLED_FILES+=("$relative_file")
done

for relative_file in "${RELATIVE_FILES[@]}"; do
    source_file="$TEMP_DIR/$relative_file"
    target="$MOODLE_ROOT/$relative_file"
    cmp -s "$source_file" "$target"
    php -l "$target" >/dev/null
done

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

INSTALLED_SHA256="$(sha256sum "$MOODLE_ROOT/${RELATIVE_FILES[0]}" | awk '{print $1}')"
echo "OK: tabla comparativa lado a lado instalada."
echo "OK: permite escoger ID, enunciado, respuestas, explicación, clasificación y fuente por filas."
echo "OK: la pregunta combinada se aprueba y la sobrante pasa a la papelera recuperable."
echo "OK: la pertenencia a proyectos de examen se conserva y la fusión puede deshacerse."
echo "OK: revisión instalada: $REVISION"
echo "SHA256: $INSTALLED_SHA256"
if [[ "${#WEB_RUNTIME_RELOADED[@]}" -gt 0 ]]; then
    echo "RUNTIME RECARGADO: ${WEB_RUNTIME_RELOADED[*]}"
else
    echo "AVISO: no se detectó un servicio web para recargar; Moodle cache sí fue purgada."
fi
echo "RESPALDO: $BACKUP_DIR"
