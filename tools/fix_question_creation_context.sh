#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/admin-herbology-question-bank.php"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
[[ -f "$TARGET" ]] || { echo "ERROR: no existe $TARGET" >&2; exit 1; }
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$TEMP_DIR/page.php" <<'PY'
from pathlib import Path
import sys
source=Path(sys.argv[1]).read_text()
marker='// persistent-question-creation-context-v1'
if marker in source:raise SystemExit('La conservación del filtro ya está instalada; no se cambió nada.')
def replace(old,new):
    global source
    if source.count(old)!=1:raise SystemExit('ERROR: no se identificó un punto de actualización; no se cambió nada: '+old[:85])
    source=source.replace(old,new,1)
anchor="$aipromptversion = 'ncbahm-mock-editor-5.1';"
restore=r'''
// persistent-question-creation-context-v1
$resumecreationcontext = (bool)optional_param('resumecreationcontext', 0, PARAM_BOOL);
if ((bool)optional_param('resetcreationcontext', 0, PARAM_BOOL) && confirm_sesskey()) {
    unset($SESSION->ncbahm_question_creation_context);
    unset($SESSION->ncbahm_new_question_recovery);
}
$savedcreationcontext = is_array($SESSION->ncbahm_question_creation_context ?? null)
    ? $SESSION->ncbahm_question_creation_context : [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $shownewquestionform && $resumecreationcontext) {
    $savedparams = (array)($savedcreationcontext['params'] ?? []);
    // Explicit URL choices take priority over the previous successful request.
    if (!array_key_exists('targetdomain', $_GET)) { $newquestiontargetdomain = (string)($savedparams['targetdomain'] ?? ''); }
    if (!array_key_exists('targetexamproject', $_GET)) { $newquestiontargetexam = (string)($savedparams['targetexamproject'] ?? ''); }
    if (!array_key_exists('instructiontemplate', $_GET)) { $selectedinstructiontemplateid = (string)($savedparams['instructiontemplate'] ?? ''); }
    if (!array_key_exists('needed', $_GET)) { $newquestionneededcount = max(0, min(10, (int)($savedparams['needed'] ?? 0))); }
    foreach (['domain'=>'domainfilter', 'cognitive'=>'cognitivefilter', 'content'=>'contentfilter',
        'formulacategory'=>'formulacategoryfilter', 'herbcategory'=>'herbcategoryfilter', 'detail'=>'detailfilter'] as $param=>$variable) {
        if (!array_key_exists($param, $_GET) && isset($savedparams[$param])) { $$variable = (string)$savedparams[$param]; }
    }
}
'''
replace(anchor,anchor+'\n'+restore)
capture=r'''    if (!$iscustomrewrite) {
        $SESSION->ncbahm_question_creation_context = [
            // Do not repopulate a finished imported question for accidental reimport.
            'idea' => $preserveexactquestion ? '' : $idea,
            'params' => [
                'targetdomain' => $customtargetdomain,
                'targetexamproject' => $customtargetexamid,
                'instructiontemplate' => $requestedinstructiontemplateid,
                'needed' => $newquestionneededcount,
                'domain' => $domainfilter, 'cognitive' => $cognitivefilter,
                'content' => $contentfilter, 'formulacategory' => $formulacategoryfilter,
                'herbcategory' => $herbcategoryfilter, 'detail' => $detailfilter,
            ],
        ];
    }
'''
success="    unset($SESSION->ncbahm_new_question_recovery);\n    if ($saveoutsideguideline) {"
replace(success,capture+success)
old="""$newquestionurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', array_filter([
    'newquestion' => 1,
    'lang' => $displaylang !== 'en' ? $displaylang : null,
]));"""
new=r'''$continuecreationparams = (array)($savedcreationcontext['params'] ?? []);
foreach (['domain'=>$domainfilter, 'cognitive'=>$cognitivefilter, 'content'=>$contentfilter,
    'formulacategory'=>$formulacategoryfilter, 'herbcategory'=>$herbcategoryfilter, 'detail'=>$detailfilter] as $param=>$value) {
    if ($value !== 'all' || array_key_exists($param, $_GET)) { $continuecreationparams[$param] = $value; }
}
if ($newquestiontargetdomain !== '') { $continuecreationparams['targetdomain'] = $newquestiontargetdomain; }
else if ($domainfilter !== 'all') { $continuecreationparams['targetdomain'] = $domainfilter; }
if ($newquestiontargetexam !== '') { $continuecreationparams['targetexamproject'] = $newquestiontargetexam; }
else if ($examprojectid !== '') { $continuecreationparams['targetexamproject'] = $examprojectid; }
if ($selectedinstructiontemplateid !== '') { $continuecreationparams['instructiontemplate'] = $selectedinstructiontemplateid; }
if ($newquestionneededcount > 0) { $continuecreationparams['needed'] = $newquestionneededcount; }
$newquestionurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', array_filter(array_merge(
    $continuecreationparams,
    ['newquestion'=>1, 'resumecreationcontext'=>1, 'lang'=>$displaylang !== 'en' ? $displaylang : null]
)));
$resetcreationurl = new moodle_url('/blocks/myprogress/admin-herbology-question-bank.php', array_filter([
    'newquestion'=>1, 'resetcreationcontext'=>1, 'sesskey'=>sesskey(),
    'lang'=>$displaylang !== 'en' ? $displaylang : null,
]));'''
replace(old,new)
prefill="""        $newquestionprefillidea = is_array($selectedinstructiontemplate)
            ? (string)$selectedinstructiontemplate['instruction']
            : (string)($newquestionrecovery['idea'] ?? '');"""
prefillnew=prefill+r'''
        if ($resumecreationcontext && trim((string)($newquestionrecovery['idea'] ?? '')) === ''
                && trim((string)($savedcreationcontext['idea'] ?? '')) !== ''
                && $selectedinstructiontemplateid === (string)($savedcreationcontext['params']['instructiontemplate'] ?? '')) {
            $newquestionprefillidea = (string)$savedcreationcontext['idea'];
        }'''
replace(prefill,prefillnew)
form='''        <form id="new-question-entry" class="question-bank__creation-form" method="post" action="<?php echo $pageurl->out_omit_querystring(false); ?>">'''
hidden=r'''
            <input type="hidden" name="lang" value="<?php echo s($displaylang); ?>">
            <?php foreach (['domain'=>$domainfilter, 'cognitive'=>$cognitivefilter, 'content'=>$contentfilter,
                'formulacategory'=>$formulacategoryfilter, 'herbcategory'=>$herbcategoryfilter, 'detail'=>$detailfilter] as $creationfilter=>$creationvalue) { ?>
                <input type="hidden" name="<?php echo s($creationfilter); ?>" value="<?php echo s($creationvalue); ?>">
            <?php } ?>
            <div class="question-bank__applied-summary">
                <?php echo $displaylang === 'es'
                    ? 'El filtro y la instrucción se conservarán para crear la siguiente pregunta. Puede modificarlos antes de enviar.'
                    : 'The filter and instruction will be kept for the next question. You can edit them before submitting.'; ?>
                <a class="question-bank__button" href="<?php echo $resetcreationurl->out(false); ?>#new-question"><?php echo $displaylang === 'es' ? 'Limpiar filtro e instrucción' : 'Clear filter and instruction'; ?></a>
            </div>'''
replace(form,form+hidden)
close='''        <a class="question-bank__button" href="<?php echo ($creationtargetexamid !== '' && $pendingexamprojecturl !== null ? $pendingexamprojecturl : $clearurl)->out(false); ?>">'''
button='''        <a id="create-another-same-filter" class="question-bank__button question-bank__button--primary" href="<?php echo $newquestionurl->out(false); ?>#new-question"><?php echo $displaylang === 'es' ? 'Crear otra con el mismo filtro' : 'Create another with the same filter'; ?></a>\n'''
replace(close,button+close)
Path(sys.argv[2]).write_text(source)
print('Preparada conservación de instrucción, plantilla, filtros, dominio, examen e idioma.')
PY
php -l "$TEMP_DIR/page.php"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/question-creation-context-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/admin-herbology-question-bank.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/admin-herbology-question-bank.php" "$TARGET"
    echo 'ERROR: se restauró el archivo anterior.' >&2
    exit 1
fi
echo 'OK: el filtro y la instrucción se conservan durante la sesión después de crear una pregunta.'
echo 'OK: botón Crear otra con el mismo filtro y opción Limpiar filtro e instrucción.'
echo 'OK: español/inglés; no se crean ni publican preguntas automáticamente.'
echo "RESPALDO: $BACKUP"
