#!/usr/bin/env python3
"""Fix student card provenance/classification and make chooser switching local."""
import argparse,datetime as DT,json,os,subprocess,tempfile
from pathlib import Path

ASSETS=['exam-audit-classification.php','exam-audit-student-cards.php','herbology-fast-exam-chooser.js']

def patch_page(source):
    marker='// Instant published-exam selection v1'
    anchor="$PAGE->requires->js(new moodle_url('/blocks/menu/assessment-profile-banner.js'"
    if source.count(anchor)!=1:raise RuntimeError('No se identificó el registro de scripts; no se cambió nada.')
    block="""// Instant published-exam selection v1
if (in_array($track, ['simulation', 'exams'], true) && !$mixdomains) {
    $PAGE->requires->js(new moodle_url('/blocks/myprogress/herbology-fast-exam-chooser.js', [
        'v' => (string)filemtime(__DIR__ . '/../myprogress/herbology-fast-exam-chooser.js'),
    ]));
}
"""
    if marker not in source:source=source.replace(anchor,block+anchor,1)
    inventory_marker='// Published snapshot student inventory v1'
    if inventory_marker not in source:
        inventory_anchor='    $studentcoveragecachepath = $CFG->dataroot'
        if source.count(inventory_anchor)!=1:raise RuntimeError('No se identificó la caché del inventario estudiantil.')
        inventory_block="""    // Published snapshot student inventory v1
    require_once(__DIR__ . '/../myprogress/exam-audit-classification.php');
    $snapshotstudentsets = block_myprogress_exam_audit_snapshot_sets($questionrevisionstore, $studentexamquestions);
    $studentexamquestions = array_fill_keys($studentexamnumbers, []);
    $studentexamquestionids = array_fill_keys($studentexamnumbers, []);
    foreach ($snapshotstudentsets as $snapshotnumber => $snapshotquestions) {
        $studentexamquestions[$snapshotnumber] = array_column($snapshotquestions, null, 'questionId');
        $studentexamquestionids[$snapshotnumber] = array_fill_keys(array_keys($studentexamquestions[$snapshotnumber]), true);
    }
"""
        source=source.replace(inventory_anchor,inventory_block+inventory_anchor,1)
        cacheline=". '/cache/board-herbology-student-content-inventory.json';"
        if source.count(cacheline)!=1:raise RuntimeError('No se identificó la caché del selector.')
        source=source.replace(cacheline,". '/cache/board-herbology-student-content-inventory-' . ($ises ? 'es' : 'en') . '.json';",1)
        source=source.replace("'version' => 'student-bensky-inventory-v3-dui-yao',","'version' => 'student-bensky-inventory-v4-published-snapshots',",1)
        idsline="        'examQuestionIds' => array_map('array_keys', $studentexamquestionids),"
        if source.count(idsline)!=1:raise RuntimeError('No se identificó la firma del inventario.')
        source=source.replace(idsline,idsline+"\n        'publishedVersions' => array_map(static fn(array $record): array => [$record['version'] ?? 0, $record['publishedAt'] ?? ''], (array)($questionrevisionstore['publishedExams'] ?? [])),",1)
    return source

def patch_learning(source):
    marker='// Canonical published-exam hierarchy classification v1'
    if marker in source:return source
    anchor="defined('MOODLE_INTERNAL') || die();"
    loop="foreach($patterns as $tag=>$pattern){if(preg_match('~'.$pattern.'~iu',$text)){$tags[]=$tag;}}"
    if source.count(anchor)!=1 or source.count(loop)!=1:raise RuntimeError('No se identificó el clasificador del informe de aprendizaje; no se cambió nada.')
    source=source.replace(anchor,anchor+"\n"+marker+"\nrequire_once(__DIR__.'/exam-audit-classification.php');",1)
    return source.replace(loop,"if(block_myprogress_exam_audit_is_hierarchy($q)){$tags[]='hierarchy';}\n    foreach($patterns as $tag=>$pattern){if($tag!=='hierarchy'&&preg_match('~'.$pattern.'~iu',$text)){$tags[]=$tag;}}",1)

def atomic(path,data,owner):
    exists=path.exists();st=path.stat() if exists else owner
    fd,temp=tempfile.mkstemp(prefix='.chooser-fix-',dir=path.parent)
    try:
        with os.fdopen(fd,'wb') as f:f.write(data);f.flush();os.fsync(f.fileno())
        os.chmod(temp,st.st_mode&0o777 if exists else 0o644);os.chown(temp,st.st_uid,st.st_gid);os.replace(temp,path)
    finally:
        if os.path.exists(temp):os.unlink(temp)

def main():
    ap=argparse.ArgumentParser();ap.add_argument('--moodle-root',type=Path,default=Path('/var/www/html/moodle'))
    ap.add_argument('--backup-root',type=Path,default=Path('/var/backups/acupuncturetests'));args=ap.parse_args()
    src=Path(__file__).parent;root=args.moodle_root;page=root/'blocks/menu/herbology-board.php'
    originalpage=page.read_bytes();payload=patch_page(originalpage.decode()).encode()
    if 'block_myprogress_render_exam_audit_cards' not in originalpage.decode():raise RuntimeError('Las fichas de auditoría no están instaladas en este selector.')
    updates={root/'blocks/myprogress'/name:(src/name).read_bytes() for name in ASSETS}
    learning=root/'blocks/myprogress/herbology-learning-report-lib.php'
    if learning.is_file():updates[learning]=patch_learning(learning.read_text()).encode()
    updates[page]=payload
    originals={p:p.read_bytes() if p.exists() else None for p in updates}
    # Parse all staged PHP before any installed file is changed.
    with tempfile.TemporaryDirectory() as td:
        for i,(path,data) in enumerate(updates.items()):
            if path.suffix=='.php':
                staged=Path(td)/(str(i)+'.php');staged.write_bytes(data);subprocess.run(['php','-l',str(staged)],check=True)
    backup=args.backup_root/('exam-chooser-snapshot-fix-'+DT.datetime.now(DT.timezone.utc).strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True,exist_ok=False)
    for i,(path,data) in enumerate(originals.items()):
        if data is not None:(backup/(str(i)+'-'+path.name)).write_bytes(data)
    (backup/'restore.json').write_text(json.dumps([{'path':str(p),'backup':str(backup/(str(i)+'-'+p.name)) if data is not None else None} for i,(p,data) in enumerate(originals.items())],indent=2))
    for path,expected in originals.items():
        if (path.read_bytes() if path.exists() else None)!=expected:raise RuntimeError('Cambió un archivo durante la preparación; no se instaló nada.')
    installed=[];owner=page.stat()
    try:
        for path,data in updates.items():atomic(path,data,owner);installed.append(path)
    except BaseException:
        for path in reversed(installed):
            if originals[path] is None:path.unlink(missing_ok=True)
            else:atomic(path,originals[path],owner)
        raise
    print('OK: fichas de todos los exámenes completos publicadas, leídas de sus versiones congeladas.')
    print('OK: jerarquía clasificada con las mismas reglas del informe; no se fijó ningún conteo artificial.')
    print('OK: cambiar de examen en el selector no recarga la página; Comenzar abre la modalidad del examen seleccionado.')
    print('No se cambiaron preguntas, publicaciones, resultados ni temporizadores.')
    print('RESPALDO:',backup)
if __name__=='__main__':main()
