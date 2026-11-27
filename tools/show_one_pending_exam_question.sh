#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$TEMP_DIR/page.php" <<'PY'
from pathlib import Path
import sys
source=Path(sys.argv[1]).read_text()
marker='// single-pending-exam-question-v1'
if marker in source:raise SystemExit('La vista de una pregunta por vez ya está instalada; no se cambió nada.')
anchor='$totalpages = max(1, (int)ceil($resultcount / $perpage));'
if source.count(anchor)!=1:raise SystemExit('ERROR: no se identificó la paginación; no se cambió nada.')
source=source.replace(anchor,marker+"\nif ($usagefilter === 'exam_project_pending') {\n    $perpage = 1;\n}\n"+anchor,1)
items='    <section class="question-bank__items"><?php foreach ($pagequestions as $question):'
if source.count(items)!=1:raise SystemExit('ERROR: no se identificó la lista de preguntas; no se cambió nada.')
notice='''    <?php if ($usagefilter === 'exam_project_pending' && $resultcount > 0): ?>
    <div class="question-bank__applied-summary" id="single-pending-question-status">
        <strong><?php echo $displaylang === 'es' ? 'Revisión individual:' : 'One-at-a-time review:'; ?></strong>
        <?php echo $displaylang === 'es'
            ? 'Se muestra una sola pregunta. Pregunta ' . ($page + 1) . ' de ' . $resultcount . ' pendientes que coinciden con los filtros.'
            : 'Only one question is shown. Question ' . ($page + 1) . ' of ' . $resultcount . ' pending questions matching the filters.'; ?>
    </div>
    <?php endif; ?>
'''
source=source.replace(items,notice+items,1)
Path(sys.argv[2]).write_text(source)
print('Preparada revisión de una pregunta por vez en la cola pendiente del examen.')
PY
php -l "$TEMP_DIR/page.php"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/single-pending-exam-question-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/admin-herbology-question-bank.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/admin-herbology-question-bank.php" "$TARGET"
    echo 'ERROR: se restauró el archivo anterior.' >&2
    exit 1
fi
echo 'OK: una sola pregunta visible en la revisión de pendientes del examen.'
echo 'OK: filtros, total de pendientes y navegación conservados; otras vistas sin cambios.'
echo "RESPALDO: $BACKUP"
