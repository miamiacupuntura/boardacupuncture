#!/usr/bin/env bash
set -euo pipefail

trap 'status=$?; echo "ERROR: extracción del tópico SCORM detenida en la línea $LINENO (código $status)." >&2' ERR

REVISION="${1:-}"
REPOSITORY="https://raw.githubusercontent.com/miamiacupuntura/boardacupuncture"
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
SCORM_DIR="$MOODLE_ROOT/mod/scorm/studytopic"
MODULE_PATTERN='IDENTIFICATION OF THE FORMULA ACCORDING TO THE HIERARCHY OF INGREDIENTS'

if [[ ! "$REVISION" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Uso: sudo bash /tmp/deploy_scorm_hierarchy_multiple_choice.sh COMMIT_DE_40_CARACTERES" >&2
    exit 1
fi

for command in curl php python3 runuser install cp mkdir mktemp date rm chown chmod; do
    command -v "$command" >/dev/null 2>&1 || {
        echo "ERROR: falta el comando $command" >&2
        exit 1
    }
done

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
FILES=(
    scorm_herbology_question_extract.py
    clinical_cases_extract.py
    scorm_word_export.py
    scorm_word_inventory.php
)

for file in "${FILES[@]}"; do
    curl -fsSL "$REPOSITORY/$REVISION/mod/scorm/studytopic/$file" -o "$TEMP_DIR/$file"
done
python3 -m py_compile \
    "$TEMP_DIR/scorm_herbology_question_extract.py" \
    "$TEMP_DIR/clinical_cases_extract.py" \
    "$TEMP_DIR/scorm_word_export.py"
php -l "$TEMP_DIR/scorm_word_inventory.php" >/dev/null

mkdir -p "$SCORM_DIR" "$BACKUP_ROOT"
BACKUP_DIR="$BACKUP_ROOT/scorm-hierarchy-multiple-choice-$(date -u +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"
for file in "${FILES[@]}"; do
    if [[ -f "$SCORM_DIR/$file" ]]; then
        cp -a "$SCORM_DIR/$file" "$BACKUP_DIR/$file"
    fi
    install -o root -g www-data -m 0644 "$TEMP_DIR/$file" "$SCORM_DIR/$file"
done

INVENTORY_JSON="$(runuser -u www-data -- php "$SCORM_DIR/scorm_word_inventory.php" --courses=44)"
MOODLE_DATA_ROOT="$(php -r '
$payload = json_decode(stream_get_contents(STDIN), true);
$root = $payload["dataroot"] ?? "";
if (!is_string($root) || $root === "") { fwrite(STDERR, "No se pudo determinar moodledata.\n"); exit(1); }
echo $root;
' <<<"$INVENTORY_JSON")"

BANK_DIR="$MOODLE_DATA_ROOT/board-domain-banks"
MAIN_BANK="$BANK_DIR/herbology-scorm-question-bank.json"
TARGET_EXPORT="$BANK_DIR/course-44-hierarchy-of-ingredients-multiple-choice.json"
mkdir -p "$BANK_DIR"
chown www-data:www-data "$BANK_DIR"
if [[ -f "$MAIN_BANK" ]]; then
    cp -a "$MAIN_BANK" "$BACKUP_DIR/herbology-scorm-question-bank.json"
fi
if [[ -f "$TARGET_EXPORT" ]]; then
    cp -a "$TARGET_EXPORT" "$BACKUP_DIR/course-44-hierarchy-of-ingredients-multiple-choice.json"
fi

runuser -u www-data -- python3 "$SCORM_DIR/scorm_herbology_question_extract.py" \
    --courses 44 \
    --module-pattern "$MODULE_PATTERN" \
    --no-static-dedupe \
    --merge-existing \
    --target-export "$TARGET_EXPORT" \
    --output "$MAIN_BANK"

VALIDATION="$(python3 - "$TARGET_EXPORT" <<'PY'
import json
import re
import sys
from pathlib import Path

path = Path(sys.argv[1])
payload = json.loads(path.read_text(encoding="utf-8"))
questions = [
    question
    for domain in (payload.get("domains") or {}).values()
    for question in (domain or [])
]
if not questions:
    raise SystemExit("ERROR: no se extrajo ninguna pregunta del tópico solicitado.")
converted = 0
for question in questions:
    source = question.get("source") or {}
    options = question.get("options") or []
    labels = {str(option.get("label") or "") for option in options}
    if int(source.get("courseId") or 0) != 44:
        raise SystemExit("ERROR: el archivo contiene una pregunta de otro curso.")
    if not re.search(r"hierarchy of ingredients", str(source.get("title") or ""), re.I):
        raise SystemExit("ERROR: el archivo contiene una actividad ajena al tópico.")
    if question.get("itemFormat") != "multiple-choice" or len(options) < 2:
        raise SystemExit("ERROR: quedó una pregunta que no es multiple choice.")
    if str(question.get("correctLabel") or "") not in labels:
        raise SystemExit("ERROR: una pregunta no tiene una clave válida.")
    if (question.get("scorm") or {}).get("convertedFrom") == "MatchingQuestion":
        converted += 1
        if len(options) != 4:
            raise SystemExit("ERROR: una correspondencia convertida no tiene cuatro opciones.")
print(json.dumps({
    "modules": payload.get("moduleCount", 0),
    "questions": len(questions),
    "matchingItemsConverted": converted,
}, ensure_ascii=False))
PY
)"

chown www-data:www-data "$MAIN_BANK" "$TARGET_EXPORT"
chmod 0640 "$MAIN_BANK" "$TARGET_EXPORT"

echo "DESPLIEGUE COMPLETADO"
echo "Curso: 44"
echo "Tópico: $MODULE_PATTERN"
echo "Resultado: $VALIDATION"
echo "Banco combinado: $MAIN_BANK"
echo "Exportación independiente: $TARGET_EXPORT"
echo "Respaldo: $BACKUP_DIR"
