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
marker='data-direct-corrected-draft-publish'
if marker in source:raise SystemExit('El botón Aprobar y publicar ya está instalado; no se cambió nada.')
anchor='            <?php if ($examquestionmanagementview): ?>'
if source.count(anchor)!=1:raise SystemExit('ERROR: no se identificó la barra de gestión del examen; no se cambió nada.')
button=r'''
            <?php if (!$iscustomdraft
                    && in_array($revisionstatus, ['draft', 'pending_reclassification'], true)
                    && !$question['_excluded']
                    && $question['_ready']
                    && $cardguidelineverified
                    && $prohibitedanswerchoice === null
                    && !$prohibitednegativewording
                    && !$visibleherbplacementmissing
                    && $cardreviewfailure === []): ?>
            <form method="post" action="<?php echo $filteractionurl->out(false); ?>" data-direct-corrected-draft-publish>
                <input type="hidden" name="formaction" value="approvequestion">
                <input type="hidden" name="questionid" value="<?php echo s((string)$question['questionId']); ?>">
                <?php if ($questionactionexamprojectid !== ''): ?><input type="hidden" name="examproject" value="<?php echo s($questionactionexamprojectid); ?>"><?php endif; ?>
                <?php if ($cardbatchid !== ''): ?><input type="hidden" name="reviewbatch" value="<?php echo s($cardbatchid); ?>"><?php endif; ?>
                <?php if (!in_array($usagefilter, ['exam2_review', 'exam_project_pending'], true)): ?><input type="hidden" name="returnorigin" value="1"><?php endif; ?>
                <input type="hidden" name="lang" value="<?php echo s($displaylang); ?>">
                <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
                <button class="question-bank__button question-bank__button--approve" type="submit"
                    onclick="return confirm('<?php echo $displaylang === 'es' ? '¿Aprobar y publicar esta versión corregida ahora?' : 'Approve and publish this corrected version now?'; ?>');">
                    <?php echo $displaylang === 'es' ? 'Aprobar y publicar' : 'Approve and publish'; ?><?php echo is_array($questionactionexamproject) ? ' · ' . s((string)$questionactionexamproject['name']) : ''; ?>
                </button>
            </form>
            <?php endif; ?>
'''
source=source.replace(anchor,anchor+button,1)
# In this toolbar, Add only handles exam membership. Do not present it as a
# substitute for approving a corrected draft.
old="if (is_array($questionactionexamproject) && !$questioninexamproject && !$inlineapproval && !$question['_excluded'] && ($question['_ready'] || $questioncanpublishdirectlytoexam) && ($cardguidelineverified || $cardguidelineoverrideactive) && !$questionusedinpriorexam):"
new="if (is_array($questionactionexamproject) && !in_array($revisionstatus, ['draft', 'pending_reclassification'], true) && !$questioninexamproject && !$inlineapproval && !$question['_excluded'] && ($question['_ready'] || $questioncanpublishdirectlytoexam) && ($cardguidelineverified || $cardguidelineoverrideactive) && !$questionusedinpriorexam):"
if source.count(old)!=1:raise SystemExit('ERROR: no se identificó el botón Add de esa barra; no se cambió nada.')
source=source.replace(old,new,1)
Path(sys.argv[2]).write_text(source)
print('Preparado botón de aprobación directa del borrador visible; sin nueva revisión de IA.')
PY
php -l "$TEMP_DIR/page.php"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/corrected-draft-publish-button-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/admin-herbology-question-bank.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/admin-herbology-question-bank.php" "$TARGET"
    echo 'ERROR: se restauró el archivo anterior.' >&2
    exit 1
fi
echo 'OK: botón Aprobar y publicar / Approve and publish para borradores corregidos en la vista del examen.'
echo 'OK: usa la aprobación existente, conserva sus validaciones y no ejecuta otra revisión de IA.'
echo 'OK: no se publica nada hasta que pulses el botón.'
echo "RESPALDO: $BACKUP"
