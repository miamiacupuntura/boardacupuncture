#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET_DIR="$MOODLE_ROOT/blocks/myprogress"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET_DIR" "$TEMP_DIR" <<'PY'
from pathlib import Path
import re,sys
root,temp=map(Path,sys.argv[1:])
admin=(root/'admin-herbology-question-bank.php').read_text()
helper=(root/'herbology-question-overrides.php').read_text()
def insert_aliases(source,variable,entries):
    match=re.search(re.escape(variable)+r'\s*=\s*\[(.*?)\n    ?\];',source,re.S)
    if not match:match=re.search(re.escape(variable)+r'\s*=\s*\[(.*?)\n\];',source,re.S)
    if not match:raise SystemExit('ERROR: no se encontró '+variable)
    addition=''
    for alias,value in entries:
        if re.search(r"'"+re.escape(alias)+r"'\s*=>",match[1]):continue
        addition+='\n    '+"'"+alias+"' => "+value+','
    return source[:match.end(1)]+addition+source[match.end(1):]
admin=insert_aliases(admin,'$guidelineherbaliases',[
    ('yin chen hao',"'yin chen'"),('dong gua ren',"'dong gua zi'")])
helper=insert_aliases(helper,'$appendixaherbaliases',[
    ('Yin Chen',"['Yin Chen Hao']"),('Dong Gua Zi',"['Dong Gua Ren']")])
marker='// complete-yin-chen-dong-gua-identities-v1'
if marker not in helper:
    anchor='        $embeddedherbformularepairs = ['
    if helper.count(anchor)!=1:raise SystemExit('ERROR: no se identificó el normalizador de nombres.')
    repair=r'''
        // complete-yin-chen-dong-gua-identities-v1
        $identityrepairs = [
            '/(?<![A-Za-z0-9])(?:Dong\s+Gua\s+Zi(?:\s*\(\s*Benincasae\s+Semen\s*\))?\s*\/\s*)?'
                . 'Dong\s+Gua\s+Ren(?:\s*\(\s*Benincasae\s+Semen\s*\))?(?![A-Za-z0-9])/iu'
                => 'Dong Gua Zi (Benincasae Semen)',
            '/(?<![A-Za-z0-9])Yin\s+Chen\s*\(\s*Artemisiae\s+scopariae\s+Herba\s*\)\s*Hao'
                . '(?:\s*\(\s*Artemisiae\s+scopariae\s+Herba\s*\))?(?![A-Za-z0-9])/iu'
                => 'Yin Chen (Artemisiae scopariae Herba)',
            '/(?<![A-Za-z0-9])Yin\s+Chen\s*\(\s*Artemisiae\s+scopariae\s+Herba\s*\)\s*\/\s*'
                . 'Yin\s+Chen\s*\(\s*Artemisiae\s+scopariae\s+Herba\s*\)/iu'
                => 'Yin Chen (Artemisiae scopariae Herba)',
        ];
        foreach ($identityrepairs as $identitypattern=>$identitydisplay) {
            $identityupdated = preg_replace($identitypattern, $identitydisplay, $value);
            if (is_string($identityupdated)) { $value=$identityupdated; }
        }
'''
    helper=helper.replace(anchor,repair+anchor,1)
(temp/'admin-herbology-question-bank.php').write_text(admin)
(temp/'herbology-question-overrides.php').write_text(helper)
PY
FILES=(admin-herbology-question-bank.php herbology-question-overrides.php)
for file in "${FILES[@]}"; do php -l "$TEMP_DIR/$file"; done
cat > "$TEMP_DIR/test.php" <<'PHP'
<?php
define('MOODLE_INTERNAL',true);
$CFG=(object)['dirroot'=>$argv[2]];
require $argv[1];
foreach ([
    'Yin Chen Hao'=>'Yin Chen (Artemisiae scopariae Herba)',
    'Yin Chen (Artemisiae scopariae Herba) / Yin Chen (Artemisiae scopariae Herba) Hao (Artemisiae scopariae Herba)'=>'Yin Chen (Artemisiae scopariae Herba)',
    'Dong Gua Ren'=>'Dong Gua Zi (Benincasae Semen)',
    'Dong Gua Zi (Benincasae Semen) / Dong Gua Ren (Benincasae Semen)'=>'Dong Gua Zi (Benincasae Semen)',
] as $input=>$expected) {
    $q=block_myprogress_herbology_canonicalize_pinyin_case_in_question(['options'=>[['label'=>'A','text'=>$input]]]);
    if (($q['options'][0]['text'] ?? '')!==$expected) { throw new RuntimeException('Herb name regression: '.$input); }
    $again=block_myprogress_herbology_canonicalize_pinyin_case_in_question($q);
    if (($again['options'][0]['text'] ?? '')!==$expected) { throw new RuntimeException('Repeated normalization regression'); }
}
$q=block_myprogress_herbology_canonicalize_pinyin_case_in_question(['prompt'=>'Yin Chen Hao Tang']);
if (strpos($q['prompt'],'Yin Chen Hao Tang')!==0 || strpos($q['prompt'],'Yin Chen (')!==false) { throw new RuntimeException('Complete formula name must remain protected'); }
echo "OK: sinónimos completos y nombres de fórmula comprobados.\n";
PHP
php "$TEMP_DIR/test.php" "$TEMP_DIR/herbology-question-overrides.php" "$MOODLE_ROOT"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/yin-chen-dong-gua-names-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
for file in "${FILES[@]}"; do cp -a "$TARGET_DIR/$file" "$BACKUP/$file"; done
rollback() {
    local file
    for file in "${FILES[@]}"; do cp -a "$BACKUP/$file" "$TARGET_DIR/$file"; done
    echo "ERROR: se restauraron los archivos. Respaldo: $BACKUP" >&2
}
trap 'rollback' ERR
for file in "${FILES[@]}"; do
    OWNER_GROUP="$(stat -c '%u:%g' "$TARGET_DIR/$file")"
    MODE="$(stat -c '%a' "$TARGET_DIR/$file")"
    install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/$file" "$TARGET_DIR/$file"
done
trap - ERR
echo 'OK: Yin Chen Hao → Yin Chen; Dong Gua Ren → Dong Gua Zi. No se admite Hao como hierba aislada.'
echo "RESPALDO DE CÓDIGO: $BACKUP"

# Rebuild the affected question as an unpublished bilingual draft.
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
MOODLE_DATA_ROOT="${MOODLE_DATA_ROOT:-/mnt/volume_nyc1_01/moodledata}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
command -v python3 >/dev/null || { echo 'ERROR: falta python3' >&2; exit 1; }
python3 - "$MOODLE_ROOT" "$MOODLE_DATA_ROOT" "$BACKUP_ROOT" <<'PY'
import copy,datetime,fcntl,json,os,shutil,sys,tempfile
from pathlib import Path
root,dataroot,backuproot=map(Path,sys.argv[1:])
qid='SCORM-SH-6ECE95F7CF3D'
version='yin-chen-damp-heat-jaundice-v1'
directory=dataroot/'board-domain-banks'
path=directory/'herbology-question-overrides.json'
roster=json.loads((root/'blocks/myprogress/single-herb-card-index.json').read_text())
official={h['name']:h['latin'] for h in roster['herbs']}
choices=[('A','Che Qian Zi','Plantaginis Semen'),('B','Ze Xie','Alismatis Rhizoma'),
         ('C','Yin Chen','Artemisiae scopariae Herba'),('D','Di Fu Zi','Kochiae Fructus')]
for _,name,latin in choices:
    if official.get(name)!=latin:raise SystemExit('ERROR: nombre no coincidente con el roster: '+name)
base=None;domain=None
for sourcefile in [directory/'herbology-scorm-question-bank.json',root/'blocks/menu/herbology-domain-candidates.json']:
    if not sourcefile.is_file():continue
    bank=json.loads(sourcefile.read_text())
    for domainid,questions in bank.get('domains',{}).items():
        for question in questions:
            if question.get('questionId')==qid:base=question;domain=domainid;break
        if base:break
    if base:break
if base is None:raise SystemExit('ERROR: no se encontró la pregunta fuente '+qid+'; no se cambió nada.')
if domain!='single_herb':raise SystemExit('ERROR: la pregunta no está en el dominio de hierbas individuales.')
options=[{'label':label,'text':name+' ('+latin+')'} for label,name,latin in choices]
prompt=('A patient has bright-yellow discoloration of the skin and sclera, dark scanty urine, '
        'a bitter taste, nausea, a yellow greasy tongue coating and a rapid pulse. Which herb '
        'most specifically addresses the jaundice in this presentation?')
promptes=('Una persona presenta coloración amarilla brillante de la piel y las escleróticas, '
          'orina oscura y escasa, sabor amargo, náuseas, saburra amarilla y grasosa y pulso rápido. '
          '¿Qué hierba aborda más específicamente la ictericia de este cuadro?')
explanation=(
 'Inference step 1: Bright-yellow jaundice with dark urine, a yellow greasy coating and a rapid pulse indicates Damp-Heat jaundice. '
 'Inference step 2: All four choices drain Dampness or promote urination, but Yin Chen specifically clears Damp-Heat and relieves jaundice. '
 'C. Yin Chen (Artemisiae scopariae Herba), also called Yin Chen Hao, is correct because its characteristic indication is jaundice due to Dampness, especially the Damp-Heat pattern described here. '
 'A. Che Qian Zi (Plantaginis Semen) promotes urination and clears urinary Damp-Heat; it is plausible for the dark scanty urine, but is less specific for the dominant jaundice. '
 'B. Ze Xie (Alismatis Rhizoma) promotes urination, drains Dampness and clears lower-burner heat; it may address fluid or urinary signs but is less specifically directed at jaundice. '
 'D. Di Fu Zi (Kochiae Fructus) clears Damp-Heat, promotes urination and relieves itching; those actions make it a plausible nearby choice, but it is not the best match for the dominant jaundice. '
 'The decisive finding is bright-yellow jaundice in a Damp-Heat pattern, rather than a generic urinary or skin complaint.')
explanationes=(
 'Paso de inferencia 1: La ictericia amarilla brillante con orina oscura, saburra amarilla y grasosa y pulso rápido indica ictericia por Humedad-Calor. '
 'Paso de inferencia 2: Las cuatro opciones drenan Humedad o favorecen la micción, pero Yin Chen aclara específicamente Humedad-Calor y alivia la ictericia. '
 'C. Yin Chen (Artemisiae scopariae Herba), también llamada Yin Chen Hao, es correcta porque su indicación característica es la ictericia por Humedad, especialmente el patrón de Humedad-Calor descrito aquí. '
 'A. Che Qian Zi (Plantaginis Semen) favorece la micción y aclara Humedad-Calor urinaria; es plausible por la orina oscura y escasa, pero es menos específica para la ictericia predominante. '
 'B. Ze Xie (Alismatis Rhizoma) favorece la micción, drena Humedad y aclara Calor del calentador inferior; puede abordar signos urinarios o de líquidos, pero se dirige menos específicamente a la ictericia. '
 'D. Di Fu Zi (Kochiae Fructus) aclara Humedad-Calor, favorece la micción y alivia el prurito; esas acciones la hacen una alternativa cercana, pero no es la mejor correspondencia con la ictericia predominante. '
 'El dato decisivo es la ictericia amarilla brillante en un patrón de Humedad-Calor, no una manifestación urinaria o cutánea inespecífica.')
draft={
 'questionId':qid,'domainId':'single_herb','prompt':prompt,'options':options,'correctLabel':'C',
 'explanation':explanation,'contentType':base.get('contentType','single_herb'),
 'cognitiveLevel':'analyze','difficultyBand':'analysis',
 'translations':{'es':{'prompt':promptes,'options':copy.deepcopy(options),'correctLabel':'C','explanation':explanationes}},
 'sourceCard':{
   'url':'/blocks/myprogress/single-herbs-study-guide.php?herb=Yin+Chen',
   'urlEs':'/blocks/myprogress/single-herbs-study-guide.php?herb=Yin+Chen&lang=es',
   'label':'Supporting study guide: Yin Chen and Damp-Heat jaundice',
   'labelEs':'Guía de apoyo: Yin Chen e ictericia por Humedad-Calor'},
 'nomenclature':{
   'herbs':[{'pinyin':name,'pharmaceuticalName':latin} for _,name,latin in choices],
   'formulas':[]},
}
if 'source' in base:draft['source']=copy.deepcopy(base['source'])
if not path.is_file():raise SystemExit('ERROR: no existe el archivo de revisiones '+str(path))
with open(str(path)+'.lock','a') as lock:
    fcntl.flock(lock,fcntl.LOCK_EX)
    store=json.loads(path.read_text());questions=store.get('questions')
    if not isinstance(questions,dict):raise SystemExit('ERROR: estructura de revisiones inválida.')
    previous=questions.get(qid,{})
    if not isinstance(previous,dict):raise SystemExit('ERROR: revisión anterior inválida.')
    if previous.get('yinChenClinicalRepairVersion')==version:
        print('Sin cambios: esta corrección ya se aplicó; se conservan las ediciones posteriores.')
        raise SystemExit(0)
    if previous.get('status') in ['deleted','exclusion_candidate']:
        raise SystemExit('ERROR: la pregunta está eliminada o marcada para exclusión; no se modificó.')
    now=datetime.datetime.now(datetime.timezone.utc)
    backup=backuproot/('yin-chen-question-rewrite-'+now.strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True);shutil.copy2(path,backup/path.name)
    record=copy.deepcopy(previous)
    history=list(record.get('history') or [])
    if previous:history.append({k:v for k,v in previous.items() if k not in ['history','original']})
    record.update({
      'questionId':qid,'domainId':'single_herb','status':'draft','draft':draft,
      'history':history[-20:],'revisionNumber':int(previous.get('revisionNumber',0))+1,
      'savedAt':now.isoformat(),'savedByName':'Yin Chen clinical distractor correction',
      'requestReclassification':False,'exclusionReason':'','archiveModule':'',
      'reviewDestination':'board','yinChenClinicalRepairVersion':version,
    })
    record.setdefault('original',copy.deepcopy(base))
    record.setdefault('approved',None)
    for stale in ['lastAutomaticReviewFailure','reviewProposal','reviewGuidelineViolations','guidelineValidation','proposalStatus']:
        record.pop(stale,None)
    questions[qid]=record;store['updatedAt']=now.isoformat()
    info=path.stat();fd,temporary=tempfile.mkstemp(prefix='.yin-chen-question-',dir=path.parent)
    try:
        with os.fdopen(fd,'w') as handle:
            json.dump(store,handle,ensure_ascii=False,indent=4);handle.write('\n');handle.flush();os.fsync(handle.fileno())
            os.fchmod(handle.fileno(),info.st_mode & 0o777);os.fchown(handle.fileno(),info.st_uid,info.st_gid)
        os.replace(temporary,path)
    finally:
        if os.path.exists(temporary):os.unlink(temporary)
    print('OK: '+qid+' reescrita como borrador de revisión en inglés y español.')
    print('OK: respuesta C = Yin Chen; las cuatro opciones son hierbas que drenan Humedad o favorecen la micción.')
    print('OK: ictericia por Humedad-Calor como pista diferencial; distractores paralelos y plausibles.')
    print('OK: no se publicaron preguntas ni se modificaron exámenes o sus integrantes.')
    print('RESPALDO:',backup)
PY
