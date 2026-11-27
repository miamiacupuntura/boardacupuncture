#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
MOODLE_DATA_ROOT="${MOODLE_DATA_ROOT:-/mnt/volume_nyc1_01/moodledata}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
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
alias="'jiu da huang' => 'da huang',"
if alias not in source:
    anchor="'zhi gan cao' => 'gan cao',"
    if source.count(anchor)!=1 or '$guidelineherbaliases = [' not in source:
        raise SystemExit('ERROR: no se identificó el mapa de validación; no se cambió nada.')
    source=source.replace(anchor,anchor+"\n    // Roster identity only: preserve the wine-prepared name and its clinical distinction.\n    "+alias,1)
Path(sys.argv[2]).write_text(source)
PY
php -l "$TEMP_DIR/page.php"
BACKUP="$BACKUP_ROOT/jiu-da-huang-roster-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/admin-herbology-question-bank.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
if ! install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET"; then
    cp -a "$BACKUP/admin-herbology-question-bank.php" "$TARGET"
    exit 1
fi
python3 - "$MOODLE_DATA_ROOT/board-domain-banks/herbology-question-overrides.json" "$BACKUP" <<'PY'
import copy,datetime,fcntl,json,os,re,shutil,sys,tempfile
from pathlib import Path
path,backup=map(Path,sys.argv[1:])
qid='SCORM-FC-F2055959C9BD'
if not path.is_file():
    print('AVISO: validador corregido; no se encontró el archivo de revisiones para actualizar el aviso guardado.')
    raise SystemExit(0)
def clean(text,spanish=False):
    if not isinstance(text,str):return text
    parts=re.split(r'(?<=[.!?])\s+',text)
    result=[];inserted=False
    for sentence in parts:
        falseclaim=(re.search(r'\bJiu Da Huang\b',sentence,re.I)
            and re.search(r'outside|fuera',sentence,re.I)
            and re.search(r'roster|Appendix|Appendices|Apéndice|listado|lista',sentence,re.I))
        if not falseclaim:result.append(sentence);continue
        if not inserted:
            result.append('Jiu Da Huang es Da Huang preparado con vino: para la validación del roster se reconoce la identidad Da Huang, conservando el nombre y la distinción de su preparación.' if spanish else 'Jiu Da Huang is wine-prepared Da Huang: roster validation recognizes the Da Huang identity while preserving the preparation name and its clinical distinction.')
            inserted=True
    return ' '.join(result)
def edit(snapshot):
    if not isinstance(snapshot,dict):return 0
    changed=0
    before=snapshot.get('explanation')
    if isinstance(before,str):
        after=clean(before);snapshot['explanation']=after;changed+=after!=before
    translations=snapshot.get('translations',{})
    if isinstance(translations,dict):
        for lang,translation in translations.items():
            if isinstance(translation,dict) and isinstance(translation.get('explanation'),str):
                before=translation['explanation'];translation['explanation']=clean(before,lang=='es');changed+=translation['explanation']!=before
    return changed
with open(str(path)+'.lock','a') as lock:
    fcntl.flock(lock,fcntl.LOCK_EX)
    store=json.loads(path.read_text())
    record=store.get('questions',{}).get(qid)
    if not isinstance(record,dict):
        print('AVISO: validador corregido; no hay revisión guardada de '+qid)
        raise SystemExit(0)
    previous=copy.deepcopy(record)
    changes=edit(record)
    for key in ['draft','approved','reviewProposal']:changes+=edit(record.get(key))
    if not changes:
        print('OK: no quedan avisos obsoletos de Jiu Da Huang en las explicaciones guardadas.')
        raise SystemExit(0)
    shutil.copy2(path,backup/path.name)
    history=list(record.get('history') or [])
    history.append({k:v for k,v in previous.items() if k not in ['history','original']})
    record['history']=history[-20:]
    now=datetime.datetime.now(datetime.timezone.utc).isoformat()
    record['revisionNumber']=int(record.get('revisionNumber',0))+1
    record['savedAt']=now;store['updatedAt']=now
    info=path.stat();fd,temporary=tempfile.mkstemp(prefix='.jiu-da-huang-roster-',dir=path.parent)
    try:
        with os.fdopen(fd,'w') as handle:
            json.dump(store,handle,ensure_ascii=False,indent=4);handle.write('\n');handle.flush();os.fsync(handle.fileno())
            os.fchmod(handle.fileno(),info.st_mode & 0o777);os.fchown(handle.fileno(),info.st_uid,info.st_gid)
        os.replace(temporary,path)
    finally:
        if os.path.exists(temporary):os.unlink(temporary)
    print('OK: explicaciones corregidas en inglés y español:',changes)
PY
echo 'OK: Jiu Da Huang se reconoce como preparación de Da Huang para validar el roster.'
echo 'OK: se conserva Jiu Da Huang en las opciones; no se publican preguntas ni se cambia su estado editorial.'
echo "RESPALDO: $BACKUP"
