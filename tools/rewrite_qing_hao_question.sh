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
qid='SCORM-SH-4C9B4B6C5FEE'
version='qing-hao-residual-yin-heat-v1'
directory=dataroot/'board-domain-banks'
path=directory/'herbology-question-overrides.json'
roster=json.loads((root/'blocks/myprogress/single-herb-card-index.json').read_text())
official={h['name']:h['latin'] for h in roster['herbs']}
choices=[('A','Qing Hao','Artemisiae annuae Herba'),('B','Di Gu Pi','Lycii Cortex'),
         ('C','Bai Wei','Cynanchi atrati Radix'),('D','Huang Qin','Scutellariae Radix')]
for _,name,latin in choices:
    if official.get(name)!=latin:raise SystemExit('ERROR: nombre no coincidente con el roster: '+name)
formulas=json.loads((root/'blocks/myprogress/formulas-study-guide-data.json').read_text())
evidence=next((f for f in formulas['formulas'] if f['name']=='Qing Hao Bie Jia Tang'),None)
if not evidence or 'Qing Hao' not in evidence.get('ingredientRoles',''):
    raise SystemExit('ERROR: no se encontró el perfil de apoyo Qing Hao Bie Jia Tang; no se cambió nada.')
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
prompt=('A patient recovering from a febrile illness has fever at night that subsides by morning '
        'without sweating, with a red tongue and scant coating. Which herb is most appropriate '
        'to vent lingering heat from the Yin level?')
promptes=('Una persona en recuperación de una enfermedad febril presenta fiebre nocturna que cede '
          'por la mañana sin sudoración, con lengua roja y saburra escasa. ¿Qué hierba es la más '
          'adecuada para exteriorizar el Calor residual del nivel Yin?')
explanation=(
 'Inference step 1: Night fever that recedes by morning without sweating after a febrile illness points to lingering heat hidden in the Yin level. '
 'Inference step 2: Qing Hao vents this hidden heat outward; this is a key role it plays in Qing Hao Bie Jia Tang. '
 'A. Qing Hao (Artemisiae annuae Herba) is correct because it clears and vents residual heat from the Yin level. It is not selected here to subdue Liver Yang or brighten the eyes. '
 'B. Di Gu Pi (Lycii Cortex) clears deficiency heat, including bone-steaming heat, and also clears Lung heat; it is less specific for the tested action of venting hidden heat outward in this post-febrile presentation. '
 'C. Bai Wei (Cynanchi atrati Radix) clears deficiency heat, cools the Blood and can benefit urination; it does not best match the characteristic hidden-heat pattern and venting action tested here. '
 'D. Huang Qin (Scutellariae Radix) clears excess heat and dries Dampness, especially in the upper burner; this is not a Damp-Heat presentation. '
 'The distinction is the post-febrile night-to-morning fever pattern and the action of venting heat hidden in Yin, rather than treating Liver Yang rising.')
explanationes=(
 'Paso de inferencia 1: La fiebre nocturna que cede por la mañana sin sudoración tras una enfermedad febril orienta a Calor residual oculto en el nivel Yin. '
 'Paso de inferencia 2: Qing Hao exterioriza ese Calor oculto; esta es una función clave que cumple en Qing Hao Bie Jia Tang. '
 'A. Qing Hao (Artemisiae annuae Herba) es correcta porque aclara y exterioriza el Calor residual del nivel Yin. Aquí no se elige para someter el Yang de Hígado ni para mejorar la visión. '
 'B. Di Gu Pi (Lycii Cortex) aclara Calor por deficiencia, incluido el Calor de huesos humeantes, y también Calor de Pulmón; es menos específica para la acción evaluada de exteriorizar Calor oculto en este cuadro posfebril. '
 'C. Bai Wei (Cynanchi atrati Radix) aclara Calor por deficiencia, enfría la Sangre y puede favorecer la micción; no es la mejor correspondencia con el patrón de Calor oculto y la acción de exteriorizarlo que se evalúan aquí. '
 'D. Huang Qin (Scutellariae Radix) aclara Calor de exceso y seca Humedad, especialmente en el calentador superior; este no es un cuadro de Humedad-Calor. '
 'La clave es el patrón posfebril de fiebre nocturna que cede por la mañana y la acción de exteriorizar Calor oculto en Yin, no tratar un ascenso de Yang de Hígado.')
draft={
 'questionId':qid,'domainId':'single_herb','prompt':prompt,'options':options,'correctLabel':'A',
 'explanation':explanation,'contentType':base.get('contentType','single_herb'),
 'cognitiveLevel':'analyze','difficultyBand':'analysis',
 'translations':{'es':{'prompt':promptes,'options':copy.deepcopy(options),'correctLabel':'A','explanation':explanationes}},
 'sourceCard':{
   'url':'/blocks/myprogress/formula-list.php?formula=Qing+Hao+Bie+Jia+Tang&sort=az',
   'urlEs':'/blocks/myprogress/formula-list.php?formula=Qing+Hao+Bie+Jia+Tang&sort=az&lang=es',
   'label':'Supporting profile: Qing Hao vents hidden heat in Qing Hao Bie Jia Tang',
   'labelEs':'Perfil de apoyo: Qing Hao exterioriza Calor oculto en Qing Hao Bie Jia Tang'},
 'nomenclature':{
   'herbs':[{'pinyin':name,'pharmaceuticalName':latin} for _,name,latin in choices],
   'formulas':[{'pinyin':'Qing Hao Bie Jia Tang','englishName':'Sweet Wormwood and Soft-Shelled Turtle Shell Decoction'}]},
}
# Use the site's own roster English name for the supporting formula identity.
formularoster=json.loads((root/'blocks/myprogress/formula-board-roster-2026.json').read_text())
formula=next((f for f in formularoster['formulas'] if f['name']=='Qing Hao Bie Jia Tang'),None)
if not formula:raise SystemExit('ERROR: la fórmula de apoyo no está en el roster; no se cambió nada.')
draft['nomenclature']['formulas'][0]['englishName']=formula['englishName']
if 'source' in base:draft['source']=copy.deepcopy(base['source'])
if not path.is_file():raise SystemExit('ERROR: no existe el archivo de revisiones '+str(path))
with open(str(path)+'.lock','a') as lock:
    fcntl.flock(lock,fcntl.LOCK_EX)
    store=json.loads(path.read_text());questions=store.get('questions')
    if not isinstance(questions,dict):raise SystemExit('ERROR: estructura de revisiones inválida.')
    previous=questions.get(qid,{})
    if not isinstance(previous,dict):raise SystemExit('ERROR: revisión anterior inválida.')
    if previous.get('qingHaoClinicalRepairVersion')==version:
        print('Sin cambios: esta corrección ya se aplicó; se conservan las ediciones posteriores.')
        raise SystemExit(0)
    if previous.get('status') in ['deleted','exclusion_candidate']:
        raise SystemExit('ERROR: la pregunta está eliminada o marcada para exclusión; no se modificó.')
    now=datetime.datetime.now(datetime.timezone.utc)
    backup=backuproot/('qing-hao-question-rewrite-'+now.strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True);shutil.copy2(path,backup/path.name)
    record=copy.deepcopy(previous)
    history=list(record.get('history') or [])
    if previous:history.append({k:v for k,v in previous.items() if k not in ['history','original']})
    record.update({
      'questionId':qid,'domainId':'single_herb','status':'draft','draft':draft,
      'history':history[-20:],'revisionNumber':int(previous.get('revisionNumber',0))+1,
      'savedAt':now.isoformat(),'savedByName':'Qing Hao clinical correction',
      'requestReclassification':False,'exclusionReason':'','archiveModule':'',
      'reviewDestination':'board','qingHaoClinicalRepairVersion':version,
    })
    record.setdefault('original',copy.deepcopy(base))
    record.setdefault('approved',None)
    for stale in ['lastAutomaticReviewFailure','reviewProposal','reviewGuidelineViolations','guidelineValidation','proposalStatus']:
        record.pop(stale,None)
    questions[qid]=record;store['updatedAt']=now.isoformat()
    info=path.stat();fd,temporary=tempfile.mkstemp(prefix='.qing-hao-question-',dir=path.parent)
    try:
        with os.fdopen(fd,'w') as handle:
            json.dump(store,handle,ensure_ascii=False,indent=4);handle.write('\n');handle.flush();os.fsync(handle.fileno())
            os.fchmod(handle.fileno(),info.st_mode & 0o777);os.fchown(handle.fileno(),info.st_uid,info.st_gid)
        os.replace(temporary,path)
    finally:
        if os.path.exists(temporary):os.unlink(temporary)
    print('OK: '+qid+' reescrita como borrador de revisión en inglés y español.')
    print('OK: respuesta A = Qing Hao; Calor residual oculto en Yin, no ascenso de Yang de Hígado.')
    print('OK: cuatro hierbas cotejadas con el roster; Yin Chai Hu retirado de esta pregunta.')
    print('OK: no se publicaron preguntas ni se modificaron exámenes o sus integrantes.')
    print('RESPALDO:',backup)
PY
