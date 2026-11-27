#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
MOODLE_DATA_ROOT="${MOODLE_DATA_ROOT:-/mnt/volume_nyc1_01/moodledata}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
command -v python3 >/dev/null || { echo 'ERROR: falta python3' >&2; exit 1; }
python3 - "$MOODLE_ROOT" "$MOODLE_DATA_ROOT" "$BACKUP_ROOT" <<'PY'
import copy,datetime,fcntl,json,os,shutil,sys,tempfile
from pathlib import Path
root,dataroot,backuproot=map(Path,sys.argv[1:])
qid='SCORM-SH-5A7EE893091C'
version='gan-sui-excess-water-retention-v1'
directory=dataroot/'board-domain-banks'
path=directory/'herbology-question-overrides.json'
roster=json.loads((root/'blocks/myprogress/single-herb-card-index.json').read_text())
official={h['name']:h['latin'] for h in roster['herbs']}
choices=[('A','Ting Li Zi','Lepidii/Descurainiae Semen'),('B','Gan Sui','Kansui Radix'),
         ('C','Sang Bai Pi','Mori Cortex'),('D','Ze Xie','Alismatis Rhizoma')]
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
prompt=('A patient of robust constitution has marked generalized edema, a tense, distended '
        'abdomen with fluid accumulation, very scanty urination and chest fullness with shortness '
        'of breath. The pulse is deep and forceful. Which herb most directly addresses the severe '
        'excess accumulation of water in the chest and abdomen?')
promptes=('Una persona de constitución robusta presenta edema generalizado marcado, abdomen tenso '
          'y distendido con acumulación de líquido, micción muy escasa y plenitud torácica con '
          'disnea. El pulso es profundo y fuerte. ¿Qué hierba aborda más directamente la acumulación '
          'intensa de agua por exceso en el tórax y el abdomen?')
explanation=(
 'Inference step 1: Marked edema, tense abdominal fluid accumulation and very scanty urination indicate substantial retained water. A robust constitution and a deep, forceful pulse support an excess pattern. '
 'Inference step 2: The combination of severity and chest-abdominal water accumulation points to a drastic water-expelling action rather than a milder diuretic or an herb focused mainly on Lung cough and wheezing. '
 'B. Gan Sui (Kansui Radix) is correct: it strongly expels retained water and thin mucus through downward purgation and is associated with severe water accumulation in the chest and abdomen. '
 'A. Ting Li Zi (Lepidii/Descurainiae Semen) drains the Lung, reduces phlegm-fluid and promotes urination. It is plausible for dyspnea with fluid retention, but is more focused on Lung excess and cough or wheezing than the dominant tense abdominal accumulation here. '
 'C. Sang Bai Pi (Mori Cortex) clears Lung heat, calms wheezing and promotes urination to reduce edema. It is plausible because edema and dyspnea coexist, but Lung-heat cough is not the defining pattern in this stem. '
 'D. Ze Xie (Alismatis Rhizoma) promotes urination and drains Dampness. It is plausible for scanty urination and edema, but lacks Gan Sui’s drastic water-expelling strength. '
 'The discriminating findings are severe chest-abdominal water accumulation and a forceful excess presentation.')
explanationes=(
 'Paso de inferencia 1: El edema marcado, la acumulación de líquido con abdomen tenso y la micción muy escasa indican una retención importante de agua. La constitución robusta y el pulso profundo y fuerte apoyan un patrón de exceso. '
 'Paso de inferencia 2: La combinación de intensidad y acumulación de agua toracoabdominal orienta a una acción expulsante drástica, frente a una diuresis más suave o una acción centrada principalmente en tos y sibilancias de Pulmón. '
 'B. Gan Sui (Kansui Radix) es correcta: expulsa intensamente el agua retenida y las mucosidades fluidas mediante purgación hacia abajo, y se relaciona con acumulaciones importantes de agua en tórax y abdomen. '
 'A. Ting Li Zi (Lepidii/Descurainiae Semen) drena el Pulmón, reduce Flema-Líquidos y favorece la micción. Es plausible por la disnea con retención de líquidos, pero se centra más en exceso de Pulmón con tos o sibilancias que en la acumulación abdominal tensa predominante. '
 'C. Sang Bai Pi (Mori Cortex) aclara Calor de Pulmón, calma sibilancias y favorece la micción para reducir edema. Es plausible porque coexisten edema y disnea, pero la tos por Calor de Pulmón no define este cuadro. '
 'D. Ze Xie (Alismatis Rhizoma) favorece la micción y drena Humedad. Es plausible por la micción escasa y el edema, pero carece de la intensidad expulsante drástica de Gan Sui. '
 'Los datos diferenciales son la acumulación intensa de agua toracoabdominal y la presentación de exceso con pulso fuerte.')
draft={
 'questionId':qid,'domainId':'single_herb','prompt':prompt,'options':options,'correctLabel':'B',
 'explanation':explanation,'contentType':base.get('contentType','single_herb'),
 'cognitiveLevel':'analyze','difficultyBand':'analysis',
 'translations':{'es':{'prompt':promptes,'options':copy.deepcopy(options),'correctLabel':'B','explanation':explanationes}},
 'sourceCard':{
   'url':'/blocks/myprogress/single-herbs-study-guide.php?herb=Gan+Sui',
   'urlEs':'/blocks/myprogress/single-herbs-study-guide.php?herb=Gan+Sui&lang=es',
   'label':'Supporting study guide: Gan Sui and retained water',
   'labelEs':'Guía de apoyo: Gan Sui y retención de agua'},
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
    if previous.get('ganSuiClinicalRepairVersion')==version:
        print('Sin cambios: esta corrección ya se aplicó; se conservan las ediciones posteriores.')
        raise SystemExit(0)
    if previous.get('status') in ['deleted','exclusion_candidate']:
        raise SystemExit('ERROR: la pregunta está eliminada o marcada para exclusión; no se modificó.')
    now=datetime.datetime.now(datetime.timezone.utc)
    backup=backuproot/('gan-sui-question-rewrite-'+now.strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True);shutil.copy2(path,backup/path.name)
    record=copy.deepcopy(previous)
    history=list(record.get('history') or [])
    if previous:history.append({k:v for k,v in previous.items() if k not in ['history','original']})
    record.update({
      'questionId':qid,'domainId':'single_herb','status':'draft','draft':draft,
      'history':history[-20:],'revisionNumber':int(previous.get('revisionNumber',0))+1,
      'savedAt':now.isoformat(),'savedByName':'Gan Sui clinical distractor correction',
      'requestReclassification':False,'exclusionReason':'','archiveModule':'',
      'reviewDestination':'board','ganSuiClinicalRepairVersion':version,
    })
    record.setdefault('original',copy.deepcopy(base))
    record.setdefault('approved',None)
    for stale in ['lastAutomaticReviewFailure','reviewProposal','reviewGuidelineViolations','guidelineValidation','proposalStatus']:
        record.pop(stale,None)
    questions[qid]=record;store['updatedAt']=now.isoformat()
    info=path.stat();fd,temporary=tempfile.mkstemp(prefix='.gan-sui-question-',dir=path.parent)
    try:
        with os.fdopen(fd,'w') as handle:
            json.dump(store,handle,ensure_ascii=False,indent=4);handle.write('\n');handle.flush();os.fsync(handle.fileno())
            os.fchmod(handle.fileno(),info.st_mode & 0o777);os.fchown(handle.fileno(),info.st_uid,info.st_gid)
        os.replace(temporary,path)
    finally:
        if os.path.exists(temporary):os.unlink(temporary)
    print('OK: '+qid+' reescrita como borrador de revisión en inglés y español.')
    print('OK: respuesta B = Gan Sui; las cuatro opciones son plausibles para retención de líquidos.')
    print('OK: pistas diferenciales de retención intensa de agua por exceso; distractores del roster y nombres sin duplicados.')
    print('OK: no se publicaron preguntas ni se modificaron exámenes o sus integrantes.')
    print('RESPALDO:',backup)
PY
