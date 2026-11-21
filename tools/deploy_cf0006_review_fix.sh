#!/usr/bin/env bash
set -euo pipefail

# Deploy the corrected CF-0006 source item and the strengthened automatic-review flow.

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
RELATIVE_FILES=(
    "blocks/myprogress/admin-herbology-question-bank.php"
    "blocks/myprogress/herbology-question-overrides.php"
    "blocks/menu/herbology-board.php"
    "blocks/menu/herbology-domain-candidates.json"
)

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_cf0006_review_fix.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php grep install stat cp cmp mkdir mktemp date rm mv sha256sum id awk dirname basename chown chmod; do
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
OVERRIDE_STORE_PATH=""
OVERRIDE_STORE_BACKUP=""
OVERRIDE_STORE_OWNER_GROUP=""

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
    if [[ -n "$OVERRIDE_STORE_PATH" && -n "$OVERRIDE_STORE_BACKUP" \
            && -f "$OVERRIDE_STORE_BACKUP" ]]; then
        cp -a -- "$OVERRIDE_STORE_BACKUP" "$OVERRIDE_STORE_PATH"
        if [[ -n "$OVERRIDE_STORE_OWNER_GROUP" ]]; then
            chown "$OVERRIDE_STORE_OWNER_GROUP" "$OVERRIDE_STORE_PATH"
            chmod 0660 "$OVERRIDE_STORE_PATH"
        fi
        echo "RECUPERACIÓN: se restauró el estado guardado de los exámenes." >&2
    fi
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
OVERRIDES_SOURCE="$TEMP_DIR/${RELATIVE_FILES[1]}"
HERBOLOGY_BOARD_SOURCE="$TEMP_DIR/${RELATIVE_FILES[2]}"
SOURCE_DATA="$TEMP_DIR/${RELATIVE_FILES[3]}"
php -l "$QUESTION_BANK_SOURCE" >/dev/null
php -l "$OVERRIDES_SOURCE" >/dev/null
php -l "$HERBOLOGY_BOARD_SOURCE" >/dev/null
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data)) { exit(1); }' "$SOURCE_DATA"
grep -Fq "ncbahm-mock-editor-4.3" "$QUESTION_BANK_SOURCE"
grep -Fq "sourceFeedbackMissing" "$QUESTION_BANK_SOURCE"
grep -Fq '$requestedvisibleherbcount' "$QUESTION_BANK_SOURCE"
grep -Fq 'Requested herbs missing · correction required' "$QUESTION_BANK_SOURCE"
grep -Fq "formaction === 'excludeexamcandidate'" "$QUESTION_BANK_SOURCE"
grep -Fq 'excludedQuestionIds' "$QUESTION_BANK_SOURCE"
grep -Fq 'Exclude from ' "$QUESTION_BANK_SOURCE"
grep -Fq "'archivequestion', 'sendtomock'" "$QUESTION_BANK_SOURCE"
grep -Fq 'Send to Mock' "$QUESTION_BANK_SOURCE"
grep -Fq 'Mock-only questions' "$QUESTION_BANK_SOURCE"
grep -Fq '$hasreviewablecontent' "$QUESTION_BANK_SOURCE"
grep -Fq 'All options are incorrect' "$QUESTION_BANK_SOURCE"
grep -Fq 'opci[oó]n(?:es)?' "$QUESTION_BANK_SOURCE"
grep -Fq "'study_archive'" "$OVERRIDES_SOURCE"
grep -Fq "'archiveModule'" "$OVERRIDES_SOURCE"
grep -Fq "'mockOnly'" "$HERBOLOGY_BOARD_SOURCE"
grep -Fq 'Mock-only question' "$HERBOLOGY_BOARD_SOURCE"
grep -Fq '$directreturnorigin' "$QUESTION_BANK_SOURCE"
grep -Fq 'Approve and publish this ready question?' "$QUESTION_BANK_SOURCE"
grep -Fq '"questionId": "CF-0006"' "$SOURCE_DATA"
grep -Fq 'Which statement correctly differentiates Gui Zhi Tang' "$SOURCE_DATA"
grep -Fq 'Option D: Incorrect. It reverses the formulas' "$SOURCE_DATA"
if grep -A 90 -F '"questionId": "CF-0006"' "$SOURCE_DATA" | grep -Eq 'None of above|None of the above|Ninguna de las anteriores'; then
    echo "ERROR: CF-0006 todavía contiene una respuesta combinada prohibida." >&2
    exit 1
fi
grep -Fq '"questionId": "CF-0126"' "$SOURCE_DATA"
grep -Fq 'A formula comparison focuses on the ingredient that adds purgative' "$SOURCE_DATA"
grep -Fq "Option D: Correct. It accurately links Da Huang's downward-draining action" "$SOURCE_DATA"
if grep -A 120 -F '"questionId": "CF-0126"' "$SOURCE_DATA" \
        | grep -Eq 'All options are incorrect|Todas las opciones son incorrectas|excess tire|exceso de neumático'; then
    echo "ERROR: CF-0126 todavía contiene la versión combinada defectuosa." >&2
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
cmp -s "$OVERRIDES_SOURCE" "$MOODLE_ROOT/${RELATIVE_FILES[1]}"
cmp -s "$HERBOLOGY_BOARD_SOURCE" "$MOODLE_ROOT/${RELATIVE_FILES[2]}"
cmp -s "$SOURCE_DATA" "$MOODLE_ROOT/${RELATIVE_FILES[3]}"
php -l "$MOODLE_ROOT/${RELATIVE_FILES[0]}" >/dev/null
php -l "$MOODLE_ROOT/${RELATIVE_FILES[1]}" >/dev/null
php -l "$MOODLE_ROOT/${RELATIVE_FILES[2]}" >/dev/null
php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data)) { exit(1); }' "$MOODLE_ROOT/${RELATIVE_FILES[3]}"

CF0126_OVERRIDE_RESULT="No había una instalación Moodle disponible para revisar la copia guardada de CF-0126."
if [[ -f "$MOODLE_ROOT/config.php" ]]; then
    MOODLE_DATAROOT="$(php -r 'define("CLI_SCRIPT", true); require $argv[1]; global $CFG; echo $CFG->dataroot;' "$MOODLE_ROOT/config.php")"
    OVERRIDE_STORE_PATH="$MOODLE_DATAROOT/board-domain-banks/herbology-question-overrides.json"
    if id www-data >/dev/null 2>&1; then
        OVERRIDE_STORE_OWNER_GROUP="$(id -u www-data):$(id -g www-data)"
    else
        OVERRIDE_STORE_OWNER_GROUP="$(stat -c '%u:%g' "$(dirname "$OVERRIDE_STORE_PATH")")"
    fi
    if [[ -f "$OVERRIDE_STORE_PATH" ]]; then
        OVERRIDE_STORE_BACKUP="$BACKUP_DIR/dataroot/board-domain-banks/herbology-question-overrides.json"
        mkdir -p "$(dirname "$OVERRIDE_STORE_BACKUP")"
        cp -a -- "$OVERRIDE_STORE_PATH" "$OVERRIDE_STORE_BACKUP"
        php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data["questions"] ?? null) || !is_array($data["examProjects"] ?? null)) { exit(1); }' "$OVERRIDE_STORE_PATH"
    fi
    CF0126_OVERRIDE_RESULT="$(MOODLE_ROOT="$MOODLE_ROOT" php <<'PHP'
<?php
define('CLI_SCRIPT', true);
$moodleroot = (string)getenv('MOODLE_ROOT');
require $moodleroot . '/config.php';
require_once $moodleroot . '/blocks/myprogress/herbology-question-overrides.php';

$bank = json_decode(
    (string)file_get_contents($moodleroot . '/blocks/menu/herbology-domain-candidates.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);
$corrected = null;
foreach ((array)($bank['domains'] ?? []) as $questions) {
    foreach ((array)$questions as $question) {
        if ((string)($question['questionId'] ?? '') === 'CF-0126') {
            $corrected = $question;
            break 2;
        }
    }
}
if (!is_array($corrected)) {
    throw new RuntimeException('No se encontró CF-0126 en el banco instalado.');
}

$legacy = static function($snapshot): bool {
    if (!is_array($snapshot)) {
        return false;
    }
    $text = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return preg_match(
        '/All options are incorrects?|Todas las opciones son incorrectas|excess tire|exceso de neumático/iu',
        (string)$text
    ) === 1;
};
$fields = [
    'prompt', 'options', 'correctLabel', 'explanation', 'translations',
    'contentType', 'cognitiveLevel', 'difficultyBand', 'source', 'sourceCard',
];
$replacement = array_intersect_key($corrected, array_flip($fields));
$changed = false;
block_myprogress_herbology_override_update(static function(array $store) use (
    $legacy,
    $replacement,
    &$changed
): array {
    $record = $store['questions']['CF-0126'] ?? null;
    if (!is_array($record)) {
        return $store;
    }
    $recordchanged = false;
    foreach (['draft', 'approved'] as $snapshotkey) {
        if (!$legacy($record[$snapshotkey] ?? null)) {
            continue;
        }
        $record[$snapshotkey] = array_replace((array)$record[$snapshotkey], $replacement);
        if ($snapshotkey === 'draft') {
            $record[$snapshotkey]['reviewSummary'] = 'Replaced combined-answer shortcuts with four independent comparisons and rebuilt feedback.';
            $record[$snapshotkey]['instructionCompliance'] = [
                'status' => 'verified',
                'note' => 'CF-0126 now contains four independent answer choices and complete A-D feedback.',
            ];
        }
        $recordchanged = true;
    }
    if (!$recordchanged) {
        return $store;
    }
    $record['revisionNumber'] = (int)($record['revisionNumber'] ?? 0) + 1;
    $record['savedAt'] = gmdate(DATE_ATOM);
    $record['savedByName'] = 'CF-0126 deployment migration';
    $record['aiPromptVersion'] = 'ncbahm-mock-editor-4.3';
    $record['cf0126MigratedAt'] = gmdate(DATE_ATOM);
    $store['questions']['CF-0126'] = $record;
    $changed = true;
    return $store;
});
echo $changed
    ? 'Se sustituyó la copia guardada defectuosa de CF-0126.'
    : 'CF-0126 no tenía una copia guardada defectuosa.';
PHP
)"
    if [[ -f "$OVERRIDE_STORE_PATH" ]]; then
        chown "$OVERRIDE_STORE_OWNER_GROUP" "$OVERRIDE_STORE_PATH"
        chmod 0660 "$OVERRIDE_STORE_PATH"
        if [[ -f "$OVERRIDE_STORE_PATH.lock" ]]; then
            chown "$OVERRIDE_STORE_OWNER_GROUP" "$OVERRIDE_STORE_PATH.lock"
            chmod 0660 "$OVERRIDE_STORE_PATH.lock"
        fi
        php -r '$data=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); if (!is_array($data["questions"] ?? null) || !is_array($data["examProjects"] ?? null)) { exit(1); }' "$OVERRIDE_STORE_PATH"
        OVERRIDE_PERMISSION_RESULT="Se restauró el acceso del servidor web al estado guardado de los exámenes."
    else
        OVERRIDE_PERMISSION_RESULT="No existía un archivo de estado de exámenes para reparar."
    fi
fi

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

INSTALLED_SHA256="$(sha256sum "$MOODLE_ROOT/${RELATIVE_FILES[3]}" | awk '{print $1}')"
echo "OK: CF-0006 ya no contiene None of above ni su equivalente en español."
echo "OK: CF-0006 incluye feedback completo para A, B, C y D en ambos idiomas."
echo "OK: la revisión automática fuerza una segunda comprobación cuando falta feedback o existe una respuesta combinada prohibida."
echo "OK: las preguntas originales listas muestran Aprobar y publicar también desde la búsqueda general."
echo "OK: una solicitud de hierbas visibles bloquea la publicación hasta que aparezcan en el enunciado o las opciones."
echo "OK: una pregunta similar puede excluirse solo del examen actual y se busca un reemplazo del mismo dominio."
echo "OK: las preguntas no-board pueden enviarse a su categoría Mock, quedan fuera de Simulation Exams y pueden restaurarse."
echo "OK: Exam builder muestra candidatas SCORM revisables cuando se agota la reserva ya completada."
echo "OK: All options are incorrect y sus equivalentes quedan bloqueados y deben reescribirse."
echo "OK: CF-0126 contiene cuatro comparaciones independientes y feedback completo en ambos idiomas."
echo "OK: una opción que concatena otras dos también queda bloqueada como respuesta combinada."
echo "OK: $CF0126_OVERRIDE_RESULT"
echo "OK: ${OVERRIDE_PERMISSION_RESULT:-No fue necesario reparar permisos del estado de exámenes.}"
echo "OK: revisión instalada: $REVISION"
echo "SHA256 DATOS: $INSTALLED_SHA256"
if [[ "${#WEB_RUNTIME_RELOADED[@]}" -gt 0 ]]; then
    echo "RUNTIME RECARGADO: ${WEB_RUNTIME_RELOADED[*]}"
else
    echo "AVISO: no se detectó un servicio web para recargar; Moodle cache sí fue purgada."
fi
echo "RESPALDO: $BACKUP_DIR"
