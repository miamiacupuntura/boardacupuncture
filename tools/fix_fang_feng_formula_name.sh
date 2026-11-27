#!/usr/bin/env bash
set -euo pipefail
DATA_ROOT="${MOODLE_DATA_ROOT:-/mnt/volume_nyc1_01/moodledata}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/backups/acupuncturetests}"
command -v python3 >/dev/null || { echo 'ERROR: falta python3' >&2; exit 1; }
python3 - "$DATA_ROOT/board-domain-banks/herbology-question-overrides.json" "$BACKUP_ROOT" <<'PY'
import copy,datetime,fcntl,json,os,re,shutil,sys,tempfile
from pathlib import Path
path,backuproot=map(Path,sys.argv[1:])
qid='SCORM-FC-F2055959C9BD'
canonical='Fang Feng Tong Sheng San'
alias=re.compile(r'(?:Ledebouriella|Saposhnikovia)\s+Powder\s+that\s+Sagely\s+Unblocks',re.I)
duplicate=re.compile(re.escape(canonical)+r'\s*\(\s*'+re.escape(canonical)+r'\s*\)',re.I)
def correct(text):
    result=alias.sub(canonical,text)
    while True:
        updated=duplicate.sub(canonical,result)
        if updated==result:return result
        result=updated
def edit(snapshot):
    if not isinstance(snapshot,dict):return 0
    changed=0
    for key in ['prompt','explanation']:
        if isinstance(snapshot.get(key),str):
            before=snapshot[key];snapshot[key]=correct(before);changed+=snapshot[key]!=before
    translations=snapshot.get('translations')
    if isinstance(translations,dict):
        for translated in translations.values():changed+=edit(translated)
    return changed
if not path.is_file():raise SystemExit('ERROR: no existe '+str(path))
with open(str(path)+'.lock','a') as lock:
    fcntl.flock(lock,fcntl.LOCK_EX)
    store=json.loads(path.read_text())
    record=store.get('questions',{}).get(qid)
    if not isinstance(record,dict):
        raise SystemExit('ERROR: no se encontró la revisión guardada de '+qid+'; no se cambió nada.')
    previous=copy.deepcopy(record)
    changes=edit(record)
    for key in ['draft','approved','reviewProposal']:
        changes+=edit(record.get(key))
    if not changes:
        print('Sin cambios: no se encontraron nombres duplicados o antiguos en la revisión de '+qid)
        raise SystemExit(0)
    # The correction is textual only: keep choices, answer key and workflow state.
    for key in ['status','proposalStatus','reviewDestination','archiveModule','extraGuidelineDomain']:
        assert record.get(key)==previous.get(key)
    for key in ['draft','approved','reviewProposal']:
        if isinstance(record.get(key),dict):
            assert record[key].get('options')==previous[key].get('options')
            assert record[key].get('correctLabel')==previous[key].get('correctLabel')
    now=datetime.datetime.now(datetime.timezone.utc)
    backup=backuproot/('fang-feng-formula-name-'+now.strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True)
    shutil.copy2(path,backup/path.name)
    history=list(record.get('history') or [])
    history.append({k:v for k,v in previous.items() if k not in ['history','original']})
    record['history']=history[-20:]
    record['revisionNumber']=int(record.get('revisionNumber',0))+1
    record['savedAt']=now.isoformat()
    store['updatedAt']=now.isoformat()
    metadata=path.stat()
    fd,temporary=tempfile.mkstemp(prefix='.fang-feng-name-',dir=path.parent)
    try:
        with os.fdopen(fd,'w') as handle:
            json.dump(store,handle,ensure_ascii=False,indent=4);handle.write('\n')
            handle.flush();os.fsync(handle.fileno())
            os.fchmod(handle.fileno(),metadata.st_mode & 0o777)
            os.fchown(handle.fileno(),metadata.st_uid,metadata.st_gid)
        os.replace(temporary,path)
    finally:
        if os.path.exists(temporary):os.unlink(temporary)
    print('OK: nombre corregido a '+canonical+' en '+qid)
    print('Campos de texto corregidos:',changes)
    print('OK: opciones, respuesta correcta y estado editorial conservados.')
    print('RESPALDO:',backup)
PY
