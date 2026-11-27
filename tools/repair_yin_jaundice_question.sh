#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/herbology-question-overrides.php"
for tool in python3 php cp stat install mktemp date; do
    command -v "$tool" >/dev/null || { echo "ERROR: falta $tool" >&2; exit 1; }
done
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$TEMP_DIR/overrides.php" <<'PY'
from pathlib import Path
import sys
source=Path(sys.argv[1]).read_text()
marker='// complete-yin-chen-zhu-fu-name-v1'
if marker not in source:
    old=r"Ling\s+Jiao\s+Gou\s+Teng\s+Tang)(?![A-Za-z0-9])/iu'"
    new=r"Ling\s+Jiao\s+Gou\s+Teng\s+Tang|Yin\s+Chen\s+Zhu\s+Fu\s+Tang)(?![A-Za-z0-9])/iu'"
    if source.count(old)!=1:raise SystemExit('ERROR: no se identificó la protección de fórmulas completas.')
    source=source.replace(old,new,1)
    anchor='        $embeddedherbformularepairs = ['
    if source.count(anchor)!=1:raise SystemExit('ERROR: no se identificó la reparación de nombres.')
    repair=r'''
            // complete-yin-chen-zhu-fu-name-v1
            '/(?<![A-Za-z0-9])Yin\s+Chen\s*\(\s*Artemisiae\s+scopariae\s+Herba\s*\)'
                . '\s+Zhu\s+Fu\s+Tang(?![A-Za-z0-9])/iu' => 'Yin Chen Zhu Fu Tang',
'''
    source=source.replace(anchor,anchor+repair,1)
    anchor='        $replaced = preg_replace_callback($pattern, static function(array $match) use ($canonicalnames): string {'
    if source.count(anchor)!=1:raise SystemExit('ERROR: no se identificó el final del normalizador.')
    collapse=r'''
        // Collapse identical herb displays produced by two equivalent aliases.
        // This changes presentation only; it does not expand roster eligibility.
        if (strpos($value, '/') !== false) {
            foreach ($herbdisplays as $identity) {
                $identityname = (string)($identity['name'] ?? '');
                $identitylatin = (string)($identity['latin'] ?? '');
                if ($identityname === '' || $identitylatin === '' || stripos($value, $identityname) === false) { continue; }
                $display = $identityname . ' (' . $identitylatin . ')';
                $displaypattern = preg_quote($display, '~');
                $collapsed = preg_replace_callback(
                    '~(?<![A-Za-z0-9])' . $displaypattern . '(?:\s*/\s*' . $displaypattern . ')+~iu',
                    static fn(array $match): string => $display,
                    $value
                );
                if (is_string($collapsed)) { $value = $collapsed; }
            }
        }
'''
    source=source.replace(anchor,collapse+anchor,1)
Path(sys.argv[2]).write_text(source)
PY
php -l "$TEMP_DIR/overrides.php"
cat > "$TEMP_DIR/test.php" <<'PHP'
<?php
define('MOODLE_INTERNAL',true);
$CFG=(object)['dirroot'=>$argv[2]];
require $argv[1];
foreach (['Yin Chen Zhu Fu Tang','Yin Chen (Artemisiae scopariae Herba) Zhu Fu Tang'] as $input) {
    $q=block_myprogress_herbology_canonicalize_pinyin_case_in_question(['prompt'=>$input]);
    if ($q['prompt']!=='Yin Chen Zhu Fu Tang') { throw new RuntimeException('Compound formula regression: '.$q['prompt']); }
}
$q=block_myprogress_herbology_canonicalize_pinyin_case_in_question(['options'=>[['label'=>'C','text'=>'Yin Chen (Artemisiae scopariae Herba) / Yin Chen (Artemisiae scopariae Herba)']]]);
if ($q['options'][0]['text']!=='Yin Chen (Artemisiae scopariae Herba)') { throw new RuntimeException('Duplicate herb display regression'); }
echo "OK: fórmula completa y sinónimos duplicados comprobados.\n";
PHP
php "$TEMP_DIR/test.php" "$TEMP_DIR/overrides.php" "$MOODLE_ROOT"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/yin-jaundice-name-normalization-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/herbology-question-overrides.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/overrides.php" "$TARGET" || ! php -l "$TARGET"; then
    cp -a "$BACKUP/herbology-question-overrides.php" "$TARGET"
    echo 'ERROR: se restauró el normalizador anterior.' >&2
    exit 1
fi
echo 'OK: Yin Chen Zhu Fu Tang se conserva como nombre completo; sinónimos idénticos se muestran una sola vez.'
echo "RESPALDO DE CÓDIGO: $BACKUP"

# Correct the affected Cold-Damp jaundice question as a review draft.
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
MOODLE_DATA_ROOT="${MOODLE_DATA_ROOT:-/mnt/volume_nyc1_01/moodledata}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
command -v python3 >/dev/null || { echo 'ERROR: falta python3' >&2; exit 1; }
python3 - "$MOODLE_ROOT" "$MOODLE_DATA_ROOT" "$BACKUP_ROOT" <<'PY'
import copy,datetime,fcntl,json,os,shutil,sys,tempfile
from pathlib import Path
root,dataroot,backuproot=map(Path,sys.argv[1:])
qid='SCORM-SH-9EBFDC14791B'
version='yin-chen-cold-damp-jaundice-v1'
directory=dataroot/'board-domain-banks'
path=directory/'herbology-question-overrides.json'
roster=json.loads((root/'blocks/myprogress/single-herb-card-index.json').read_text())
official={h['name']:h['latin'] for h in roster['herbs']}
choices=[('A','Fu Ling','Poria'),('B','Ze Xie','Alismatis Rhizoma'),
         ('C','Yin Chen','Artemisiae scopariae Herba'),('D','Bai Zhu','Atractylodis macrocephalae Rhizoma')]
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
prompt=('A patient has dull-yellow discoloration of the skin and sclera, heaviness, fatigue, '
        'poor appetite, abdominal distention, loose stools and a feeling of cold. The tongue is '
        'pale with a white greasy coating, and the pulse is deep, slow and weak. Alongside herbs '
        'that warm the middle and support the Spleen, which herb most specifically addresses '
        'the jaundice in this presentation?')
promptes=('Una persona presenta coloración amarilla apagada de la piel y las escleróticas, '
          'pesadez, fatiga, poco apetito, distensión abdominal, heces blandas y sensación de frío. '
          'La lengua es pálida con saburra blanca y grasosa, y el pulso es profundo, lento y débil. '
          'Junto con hierbas que calientan el centro y apoyan al Bazo, ¿qué hierba aborda más '
          'específicamente la ictericia de este cuadro?')
explanation=(
 'Inference step 1: Dull-yellow jaundice, coldness, loose stools, a pale tongue, a white greasy coating and a deep, slow, weak pulse indicate Cold-Damp, or Yin-type, jaundice with impaired Spleen transformation. '
 'Inference step 2: Treatment must address the Cold and Spleen weakness while also targeting the retained Dampness and jaundice. The question asks for the herb specifically directed at jaundice within that combination. '
 'C. Yin Chen (Artemisiae scopariae Herba) is correct because it is specifically associated with resolving Dampness and relieving jaundice. In Cold-Damp jaundice it is combined with warming, supporting herbs; Yin Chen itself is not a Yang-warming herb and is not presented here as stand-alone treatment. '
 'A. Fu Ling (Poria) drains Dampness and supports the Spleen, making it a plausible supportive ingredient, but it is less specifically directed at jaundice. '
 'B. Ze Xie (Alismatis Rhizoma) promotes urination and drains Dampness, but does not specifically address the jaundice as directly as Yin Chen and does not supply the required warming action. '
 'D. Bai Zhu (Atractylodis macrocephalae Rhizoma) strengthens the Spleen and dries Dampness, fitting the poor appetite and loose stools, but it is not the most specific jaundice-directed herb. '
 'The decisive distinction is the jaundice-targeting role of Yin Chen alongside warming support, rather than relabeling this cold presentation as Damp-Heat.')
explanationes=(
 'Paso de inferencia 1: La ictericia amarilla apagada, el frío, las heces blandas, la lengua pálida con saburra blanca y grasosa y el pulso profundo, lento y débil indican ictericia por Frío-Humedad, o de tipo Yin, con alteración de la transformación del Bazo. '
 'Paso de inferencia 2: Es necesario abordar el Frío y la debilidad del Bazo, además de la Humedad retenida y la ictericia. La pregunta pide la hierba que se dirige específicamente a la ictericia dentro de esa combinación. '
 'C. Yin Chen (Artemisiae scopariae Herba) es correcta porque se relaciona específicamente con resolver Humedad y aliviar la ictericia. En la ictericia por Frío-Humedad se combina con hierbas calentadoras y de apoyo; Yin Chen no es una hierba que caliente el Yang ni se plantea aquí como tratamiento aislado. '
 'A. Fu Ling (Poria) drena Humedad y apoya al Bazo, por lo que es un ingrediente de apoyo plausible, pero se dirige menos específicamente a la ictericia. '
 'B. Ze Xie (Alismatis Rhizoma) favorece la micción y drena Humedad, pero no se dirige a la ictericia tan específicamente como Yin Chen ni aporta la acción calentadora necesaria. '
 'D. Bai Zhu (Atractylodis macrocephalae Rhizoma) fortalece el Bazo y seca Humedad, por lo que concuerda con poco apetito y heces blandas, pero no es la hierba más específica para la ictericia. '
 'La diferencia decisiva es la función de Yin Chen dirigida a la ictericia junto con apoyo calentador, sin reclasificar este cuadro frío como Humedad-Calor.')
draft={
 'questionId':qid,'domainId':'single_herb','prompt':prompt,'options':options,'correctLabel':'C',
 'explanation':explanation,'contentType':base.get('contentType','single_herb'),
 'cognitiveLevel':'analyze','difficultyBand':'analysis',
 'translations':{'es':{'prompt':promptes,'options':copy.deepcopy(options),'correctLabel':'C','explanation':explanationes}},
 'sourceCard':{
   'url':'/blocks/myprogress/single-herbs-study-guide.php?herb=Yin+Chen',
   'urlEs':'/blocks/myprogress/single-herbs-study-guide.php?herb=Yin+Chen&lang=es',
   'label':'Supporting study guide: Yin Chen and Cold-Damp jaundice',
   'labelEs':'Guía de apoyo: Yin Chen e ictericia por Frío-Humedad'},
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
    if previous.get('yinJaundiceClinicalRepairVersion')==version:
        print('Sin cambios: esta corrección ya se aplicó; se conservan las ediciones posteriores.')
        raise SystemExit(0)
    if previous.get('status') in ['deleted','exclusion_candidate']:
        raise SystemExit('ERROR: la pregunta está eliminada o marcada para exclusión; no se modificó.')
    now=datetime.datetime.now(datetime.timezone.utc)
    backup=backuproot/('yin-jaundice-question-rewrite-'+now.strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True);shutil.copy2(path,backup/path.name)
    record=copy.deepcopy(previous)
    history=list(record.get('history') or [])
    if previous:history.append({k:v for k,v in previous.items() if k not in ['history','original']})
    record.update({
      'questionId':qid,'domainId':'single_herb','status':'draft','draft':draft,
      'history':history[-20:],'revisionNumber':int(previous.get('revisionNumber',0))+1,
      'savedAt':now.isoformat(),'savedByName':'Yin-type jaundice clinical correction',
      'requestReclassification':False,'exclusionReason':'','archiveModule':'',
      'reviewDestination':'board','yinJaundiceClinicalRepairVersion':version,
    })
    record.setdefault('original',copy.deepcopy(base))
    record.setdefault('approved',None)
    for stale in ['lastAutomaticReviewFailure','reviewProposal','reviewGuidelineViolations','guidelineValidation','proposalStatus']:
        record.pop(stale,None)
    questions[qid]=record;store['updatedAt']=now.isoformat()
    info=path.stat();fd,temporary=tempfile.mkstemp(prefix='.yin-jaundice-question-',dir=path.parent)
    try:
        with os.fdopen(fd,'w') as handle:
            json.dump(store,handle,ensure_ascii=False,indent=4);handle.write('\n');handle.flush();os.fsync(handle.fileno())
            os.fchmod(handle.fileno(),info.st_mode & 0o777);os.fchown(handle.fileno(),info.st_uid,info.st_gid)
        os.replace(temporary,path)
    finally:
        if os.path.exists(temporary):os.unlink(temporary)
    print('OK: '+qid+' reescrita como borrador de revisión en inglés y español.')
    print('OK: respuesta C = Yin Chen; se conserva la ictericia Yin por Frío-Humedad.')
    print('OK: combinación con apoyo calentador explicada; distractores del roster sin acciones que revelen la respuesta.')
    print('OK: no se publicaron preguntas ni se modificaron exámenes o sus integrantes.')
    print('RESPALDO:',backup)
PY
