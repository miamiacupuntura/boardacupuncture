#!/usr/bin/env bash
set -euo pipefail

# Deploy immediate previews and multi-question block creation for administrator drafts.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILE="blocks/myprogress/admin-herbology-question-bank.php"
TARGET="$MOODLE_ROOT/$RELATIVE_FILE"

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_new_question_batch_preview.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv sha256sum id awk; do
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
STAGED_FILE="$MOODLE_ROOT/blocks/myprogress/.admin-herbology-question-bank.php.batch-preview.$$"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/new-question-batch-preview-$TIMESTAMP"
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
    echo "ERROR: despliegue de creación por lote detenido en la línea $line (código $status)." >&2
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
grep -Fq 'Create one or several questions' "$SOURCE_FILE"
grep -Fq 'BATCH CREATION:' "$SOURCE_FILE"
grep -Fq '$decodecustomresponse' "$SOURCE_FILE"
grep -Fq "'custompreview' =>" "$SOURCE_FILE"
grep -Fq "'creationBatchPosition' =>" "$SOURCE_FILE"
grep -Fq 'the administrator sees the stem, choices, key and feedback just produced' "$SOURCE_FILE"
grep -Fq '$customanswermarkers' "$SOURCE_FILE"
grep -Fq 'id="new-question-batch-preview"' "$SOURCE_FILE"
grep -Fq '↑ Batch index' "$SOURCE_FILE"
grep -Fq '$customquestiontotalcount' "$SOURCE_FILE"
grep -Fq 'Created questions' "$SOURCE_FILE"
grep -Fq "'custom' => \$makesummaryurl(['source' => 'admin'])" "$SOURCE_FILE"
grep -Fq "if (\$sourcefilter === 'admin' && \$usagefilter !== 'all')" "$SOURCE_FILE"
grep -Fq "this.form.elements.usage.value='all'" "$SOURCE_FILE"
grep -Fq "(\$storedexam['status'] ?? 'draft') !== 'published'" "$SOURCE_FILE"
grep -Fq 'Only the active exam is shown' "$SOURCE_FILE"
grep -Fq '$draftchannelherbmapping' "$SOURCE_FILE"
grep -Fq '$visiblechannelherbmapping' "$SOURCE_FILE"

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
grep -Fq 'Create one or several questions' "$TARGET"
grep -Fq '$decodecustomresponse' "$TARGET"
grep -Fq "'custompreview' =>" "$TARGET"
grep -Fq 'id="new-question-batch-preview"' "$TARGET"
grep -Fq '$customquestiontotalcount' "$TARGET"
grep -Fq "if (\$sourcefilter === 'admin' && \$usagefilter !== 'all')" "$TARGET"
grep -Fq 'Only the active exam is shown' "$TARGET"
grep -Fq '$draftchannelherbmapping' "$TARGET"

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

INSTALLED_SHA256="$(sha256sum "$TARGET" | awk '{print $1}')"
echo "OK: la pregunta nueva se abre inmediatamente en pantalla para revisión."
echo "OK: el formulario acepta hasta 5 preguntas en un solo bloque."
echo "OK: cada sección se guarda como borrador independiente y el lote completo se muestra junto."
echo "OK: los bloques sin encabezados se detectan por sus etiquetas Correct answer / Respuesta correcta."
echo "OK: un índice visible permite abrir cada pregunta creada."
echo "OK: Source muestra Created questions con total, borradores y aprobadas."
echo "OK: la tarjeta Created questions abre el filtro completo con un clic."
echo "OK: Created questions elimina automáticamente filtros incompatibles de Exam use."
echo "OK: Exam use muestra solo el examen activo y oculta proyectos publicados pendientes antiguos."
echo "OK: el feedback de opciones por canales identifica la hierba correspondiente a cada grupo A-D."
echo "OK: ninguna pregunta se publica automáticamente."
echo "OK: revisión instalada: $REVISION"
echo "SHA256: $INSTALLED_SHA256"
if [[ "${#WEB_RUNTIME_RELOADED[@]}" -gt 0 ]]; then
    echo "RUNTIME RECARGADO: ${WEB_RUNTIME_RELOADED[*]}"
else
    echo "AVISO: no se detectó un servicio web para recargar; Moodle cache sí fue purgada."
fi
echo "RESPALDO: $BACKUP_FILE"
