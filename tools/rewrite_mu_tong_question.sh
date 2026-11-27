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
qid='SCORM-SH-6DDE90FA01DA'
version='mu-tong-plausible-urinary-distractors-v1'
directory=dataroot/'board-domain-banks'
path=directory/'herbology-question-overrides.json'
roster=json.loads((root/'blocks/myprogress/single-herb-card-index.json').read_text())
official={h['name']:h['latin'] for h in roster['herbs']}
choices=[('A','Qu Mai','Dianthi Herba'),('B','Dong Kui Zi','Malvae Fructus'),
         ('C','Mu Tong','Akebiae Caulis'),('D','Bian Xu','Polygoni avicularis Herba')]
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
prompt=('A patient has painful, dark, scanty urination together with painful ulcers on the tongue, '
        'irritability and a distinctly red tongue tip. Which herb best addresses both the oral '
        'and urinary findings?')
promptes=('Una persona presenta micción dolorosa, oscura y escasa, junto con úlceras dolorosas '
          'en la lengua, irritabilidad y punta de la lengua marcadamente roja. ¿Qué hierba '
          'aborda mejor tanto las manifestaciones orales como las urinarias?')
explanation=(
 'Inference step 1: The urinary symptoms alone could fit several herbs that promote urination and treat painful urinary dysfunction. '
 'Inference step 2: Tongue ulcers, irritability and a red tongue tip add a Heart-fire pattern transmitting heat to the Small Intestine. '
 'C. Mu Tong (Akebiae Caulis) is correct because it clears Heart heat, conducts heat downward through the Small Intestine and promotes urination, addressing both parts of the presentation. '
 'A. Qu Mai (Dianthi Herba) is a plausible alternative: it clears lower-burner Damp-Heat and treats painful urinary dysfunction. It is not excluded because the patient lacks constipation; the decisive distinction is Mu Tong’s Heart-heat action. '
 'B. Dong Kui Zi (Malvae Fructus) also promotes urination and can promote lactation. It is not merely a breast herb, but it is less specific for the Heart-fire signs in this case. '
 'D. Bian Xu (Polygoni avicularis Herba) clears lower-burner Damp-Heat and treats painful urinary dysfunction, in addition to its antiparasitic use. Its urinary action makes it plausible, but it does not best connect the oral Heart-fire signs with the urinary symptoms. '
 'The discriminating clue is the combination of tongue ulcers and a red tongue tip with painful urination, rather than painful urination alone.')
explanationes=(
 'Paso de inferencia 1: Los síntomas urinarios por sí solos podrían corresponder a varias hierbas que favorecen la micción y tratan la disfunción urinaria dolorosa. '
 'Paso de inferencia 2: Las úlceras linguales, la irritabilidad y la punta roja de la lengua añaden un patrón de Fuego de Corazón que transmite Calor al Intestino Delgado. '
 'C. Mu Tong (Akebiae Caulis) es correcta porque aclara Calor de Corazón, conduce el Calor hacia abajo por el Intestino Delgado y favorece la micción, abordando ambas partes del cuadro. '
 'A. Qu Mai (Dianthi Herba) es una alternativa plausible: aclara Humedad-Calor del calentador inferior y trata la disfunción urinaria dolorosa. No se descarta porque falte estreñimiento; la diferencia decisiva es la acción de Mu Tong sobre el Calor de Corazón. '
 'B. Dong Kui Zi (Malvae Fructus) también favorece la micción y puede favorecer la lactancia. No es únicamente una hierba para la mama, pero es menos específica para los signos de Fuego de Corazón de este caso. '
 'D. Bian Xu (Polygoni avicularis Herba) aclara Humedad-Calor del calentador inferior y trata la disfunción urinaria dolorosa, además de su uso antiparasitario. Su acción urinaria la hace plausible, pero no relaciona tan específicamente los signos orales de Fuego de Corazón con los urinarios. '
 'La pista diferencial es la combinación de úlceras linguales y punta roja de la lengua con micción dolorosa, no la micción dolorosa aislada.')
draft={
 'questionId':qid,'domainId':'single_herb','prompt':prompt,'options':options,'correctLabel':'C',
 'explanation':explanation,'contentType':base.get('contentType','single_herb'),
 'cognitiveLevel':'analyze','difficultyBand':'analysis',
 'translations':{'es':{'prompt':promptes,'options':copy.deepcopy(options),'correctLabel':'C','explanation':explanationes}},
 'sourceCard':{
   'url':'/blocks/myprogress/single-herbs-study-guide.php?herb=Mu+Tong',
   'urlEs':'/blocks/myprogress/single-herbs-study-guide.php?herb=Mu+Tong&lang=es',
   'label':'Supporting study guide: Mu Tong and urinary herbs',
   'labelEs':'Guía de apoyo: Mu Tong y hierbas para trastornos urinarios'},
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
    if previous.get('muTongDistractorRepairVersion')==version:
        print('Sin cambios: esta corrección ya se aplicó; se conservan las ediciones posteriores.')
        raise SystemExit(0)
    if previous.get('status') in ['deleted','exclusion_candidate']:
        raise SystemExit('ERROR: la pregunta está eliminada o marcada para exclusión; no se modificó.')
    now=datetime.datetime.now(datetime.timezone.utc)
    backup=backuproot/('mu-tong-question-rewrite-'+now.strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True);shutil.copy2(path,backup/path.name)
    record=copy.deepcopy(previous)
    history=list(record.get('history') or [])
    if previous:history.append({k:v for k,v in previous.items() if k not in ['history','original']})
    record.update({
      'questionId':qid,'domainId':'single_herb','status':'draft','draft':draft,
      'history':history[-20:],'revisionNumber':int(previous.get('revisionNumber',0))+1,
      'savedAt':now.isoformat(),'savedByName':'Mu Tong clinical distractor correction',
      'requestReclassification':False,'exclusionReason':'','archiveModule':'',
      'reviewDestination':'board','muTongDistractorRepairVersion':version,
    })
    record.setdefault('original',copy.deepcopy(base))
    record.setdefault('approved',None)
    for stale in ['lastAutomaticReviewFailure','reviewProposal','reviewGuidelineViolations','guidelineValidation','proposalStatus']:
        record.pop(stale,None)
    questions[qid]=record;store['updatedAt']=now.isoformat()
    info=path.stat();fd,temporary=tempfile.mkstemp(prefix='.mu-tong-question-',dir=path.parent)
    try:
        with os.fdopen(fd,'w') as handle:
            json.dump(store,handle,ensure_ascii=False,indent=4);handle.write('\n');handle.flush();os.fsync(handle.fileno())
            os.fchmod(handle.fileno(),info.st_mode & 0o777);os.fchown(handle.fileno(),info.st_uid,info.st_gid)
        os.replace(temporary,path)
    finally:
        if os.path.exists(temporary):os.unlink(temporary)
    print('OK: '+qid+' reescrita como borrador de revisión en inglés y español.')
    print('OK: respuesta C = Mu Tong; las cuatro opciones son hierbas plausibles para problemas urinarios.')
    print('OK: pistas diferenciales de Calor de Corazón; nombres y acciones de distractores corregidos.')
    print('OK: no se publicaron preguntas ni se modificaron exámenes o sus integrantes.')
    print('RESPALDO:',backup)
PY
