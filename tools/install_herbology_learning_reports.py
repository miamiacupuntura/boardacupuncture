#!/usr/bin/env python3
"""Install per-user learning reports without replacing exam content or history."""
import argparse,datetime as DT,json,os,shutil,subprocess,tempfile
from pathlib import Path

FILES=['herbology-learning-report-lib.php','herbology-learning-report.php','herbology-learning-report.js']

def patch(source):
    marker='// Detailed Herbology learning reports v1'
    if marker in source:return source
    anchor="$PAGE->requires->js(new moodle_url('/blocks/menu/assessment-profile-banner.js'"
    if source.count(anchor)!=1:raise RuntimeError('No se identificó el registro de scripts; no se cambió nada.')
    block=r'''// Detailed Herbology learning reports v1
if (in_array($track, ['exams', 'simulation'], true)) {
    require_once(__DIR__ . '/../myprogress/herbology-learning-report-lib.php');
    $learningconfig = block_menu_hlr_boot(
        $mixdomains && count($mixedquestions) === 100 ? $mixedquestions : [],
        $ises, $track, (string)$exammode,
        $track === 'simulation' ? $simulationexamnumber : $practiceexamnumber,
        $questionrevisionstore
    );
    $PAGE->requires->js(new moodle_url('/blocks/myprogress/herbology-learning-report.js', ['v' => '2026112801']));
    $PAGE->requires->js_init_code('window.HerbologyLearning.init(' . json_encode(
        $learningconfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
    ) . ');');
}
'''
    return source.replace(anchor,block+anchor,1)

def atomic(path,data,owner):
    old=path.stat() if path.exists() else owner
    fd,temp=tempfile.mkstemp(prefix='.hlr-',dir=path.parent)
    try:
        with os.fdopen(fd,'wb') as f:f.write(data);f.flush();os.fsync(f.fileno())
        os.chmod(temp,(old.st_mode&0o777) if path.exists() else 0o644);os.chown(temp,old.st_uid,old.st_gid)
        os.replace(temp,path)
    finally:
        if os.path.exists(temp):os.unlink(temp)

def main():
    ap=argparse.ArgumentParser();ap.add_argument('--moodle-root',type=Path,default=Path('/var/www/html/moodle'))
    ap.add_argument('--backup-root',type=Path,default=Path('/var/backups/acupuncturetests'));args=ap.parse_args()
    root=args.moodle_root;src=Path(__file__).parent;page=root/'blocks/menu/herbology-board.php'
    oldpage=page.read_bytes();newpage=patch(oldpage.decode()).encode()
    updates={root/'blocks/myprogress'/name:(src/name).read_bytes() for name in FILES};updates[page]=newpage
    originals={p:p.read_bytes() if p.exists() else None for p in updates}
    with tempfile.TemporaryDirectory() as td:
        for index,(path,data) in enumerate(updates.items()):
            if path.suffix=='.php':
                f=Path(td)/(str(index)+'.php');f.write_bytes(data);subprocess.run(['php','-l',str(f)],check=True)
    backup=args.backup_root/('herbology-learning-reports-'+DT.datetime.now(DT.timezone.utc).strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True,exist_ok=False)
    for i,(path,data) in enumerate(originals.items()):
        if data is not None:(backup/(str(i)+'-'+path.name)).write_bytes(data)
    (backup/'restore.json').write_text(json.dumps([{'path':str(p),'backup':str(backup/(str(i)+'-'+p.name)) if data is not None else None} for i,(p,data) in enumerate(originals.items())],indent=2))
    for path,expected in originals.items():
        if (path.read_bytes() if path.exists() else None)!=expected:raise RuntimeError('Cambió un archivo durante la preparación; no se instaló nada.')
    owner=page.stat();installed=[]
    try:
        for path,data in updates.items():atomic(path,data,owner);installed.append(path)
    except BaseException:
        for path in reversed(installed):
            if originals[path] is None:path.unlink(missing_ok=True)
            else:atomic(path,originals[path],owner)
        raise
    print('OK: informe EN/ES al finalizar Exams y Simulation Exams; aciertos, errores y sin responder por tema y dominio.')
    print('OK: revisión de cada respuesta, gráficos y curvas de hasta 50 informes comparables; historial privado por alumno.')
    print('OK: los resultados anteriores se conservan. El seguimiento detallado comienza con los nuevos intentos.')
    print('RESPALDO:',backup)
if __name__=='__main__':main()
