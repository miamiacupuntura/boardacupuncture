#!/usr/bin/env python3
"""Avoid full-bank work when opening already-published exam modalities."""
import argparse,datetime as DT,json,os,subprocess,tempfile
from pathlib import Path

def patch(source):
    marker='// Published attempt fast preparation v1'
    if marker in source:return source
    if '// Frozen Exam 1 snapshot adapter v1' not in source:raise RuntimeError('Falta la lectura de la versión publicada de Exam 1; no se cambió nada.')
    anchor="$domainseedpath = __DIR__ . '/herbology-domain-bank.json';"
    pre=r'''// Published attempt fast preparation v1
$fastpublishedattempt = false;
$attemptpreloadedstore = null;
$attemptpreparestarted = microtime(true);
if (in_array($track, ['exams', 'simulation'], true) && $mixdomains) {
    require_once(__DIR__ . '/../myprogress/herbology-published-attempt-fast.php');
    $attemptpreloadedstore = block_myprogress_herbology_override_load();
    $attemptselectednumber = $track === 'simulation' ? $simulationexamnumber : $practiceexamnumber;
    $fastpublishedattempt = block_menu_fast_attempt_supported($attemptpreloadedstore, $attemptselectednumber);
}
$attemptdisplaystamp = $fastpublishedattempt
    ? (string)hash_file('sha256', __DIR__ . '/../myprogress/herbology-question-overrides.php') : '';
$attemptdisplaycache = $CFG->dataroot . '/cache/board-published-attempt-display-v1';
'''
    if source.count(anchor)!=1:raise RuntimeError('No se identificó la preparación del banco.')
    source=source.replace(anchor,pre+anchor,1)
    guards={
      'if ($usesdomainbank && !is_readable($domainbankpath) && is_readable($domainseedpath)) {':'if ($usesdomainbank && !$fastpublishedattempt && !is_readable($domainbankpath) && is_readable($domainseedpath)) {',
      'if ($usesdomainbank && is_readable($domainbankpath)) {':'if ($usesdomainbank && !$fastpublishedattempt && is_readable($domainbankpath)) {',
      'if ($usesdomainbank && is_readable($domaincandidatepath)) {':'if ($usesdomainbank && !$fastpublishedattempt && is_readable($domaincandidatepath)) {',
      '$questionrevisionstore = block_myprogress_herbology_override_load();':'$questionrevisionstore = is_array($attemptpreloadedstore) ? $attemptpreloadedstore : block_myprogress_herbology_override_load();',
      "if ($usesdomainbank && isset($questionrevisionstore['publishedExams']['simulation_exam_1'])) {":"if ($usesdomainbank && !$fastpublishedattempt && isset($questionrevisionstore['publishedExams']['simulation_exam_1'])) {",
    }
    for old,new in guards.items():
        if source.count(old)!=1:raise RuntimeError('No se identificó un paso de preparación; no se cambió nada: '+old)
        source=source.replace(old,new,1)
    begin="    $domainbank['domains'] = block_myprogress_herbology_merge_custom_questions("
    nextloop='    foreach ($publishedsimulationexams as &$publishedsimulationexam) {'
    start=source.index(begin);end=source.index(nextloop,start);legacy=source[start:end]
    replacement='    if (!$fastpublishedattempt) {\n'+legacy+'''    } else {
        $fastexamone = block_menu_fast_published_display(
            $questionrevisionstore['publishedExams']['simulation_exam_1'], 1,
            $attemptdisplaystamp, $attemptdisplaycache,
            'block_myprogress_herbology_remove_pinyin_tones_from_question'
        );
        $domainbank['domains'] = block_menu_fast_exam_one_domains($fastexamone);
    }
'''
    source=source[:start]+replacement+source[end:]
    start=source.index(nextloop);end=source.index('    unset($publishedsimulationexam);',start)+len('    unset($publishedsimulationexam);')
    legacy=source[start:end]
    replacement='    if (!$fastpublishedattempt) {\n'+legacy+'''\n    } else {
        foreach ($publishedsimulationexams as $fastnumber => $fastrecord) {
            if ($exammode === 'officialadaptive' || (int)$fastnumber === $attemptselectednumber) {
                $publishedsimulationexams[$fastnumber] = block_menu_fast_published_display(
                    $fastrecord, (int)$fastnumber, $attemptdisplaystamp, $attemptdisplaycache,
                    'block_myprogress_herbology_remove_pinyin_tones_from_question'
                );
            }
        }
    }'''
    source=source[:start]+replacement+source[end:]
    begin='$mockcategorydefinitions = ['
    endanchor="if ($track === 'exams' && $mixdomains && count($mixedquestions) === 100) {"
    start=source.index(begin);end=source.index(endanchor,start);legacy=source[start:end]
    replacement='''$selectedmockdefinition = null;
$selectedmockquestions = [];
$studentcontentinventory = [];
$renderstudentcontentinventory = static function(): void {};
if (!$fastpublishedattempt) {
'''+legacy+'''}
if ($fastpublishedattempt && !headers_sent()) {
    header('Server-Timing: herbology_prepare;dur=' . round((microtime(true) - $attemptpreparestarted) * 1000, 1), false);
}

'''
    source=source[:start]+replacement+source[end:]
    return source

def atomic(path,data,owner):
    st=path.stat() if path.exists() else owner;exists=path.exists()
    fd,temp=tempfile.mkstemp(prefix='.attempt-fast-',dir=path.parent)
    try:
        with os.fdopen(fd,'wb') as f:f.write(data);f.flush();os.fsync(f.fileno())
        os.chmod(temp,st.st_mode&0o777 if exists else 0o644);os.chown(temp,st.st_uid,st.st_gid);os.replace(temp,path)
    finally:
        if os.path.exists(temp):os.unlink(temp)

def main():
    ap=argparse.ArgumentParser();ap.add_argument('--moodle-root',type=Path,default=Path('/var/www/html/moodle'))
    ap.add_argument('--backup-root',type=Path,default=Path('/var/backups/acupuncturetests'));args=ap.parse_args()
    page=args.moodle_root/'blocks/menu/herbology-board.php';helper=args.moodle_root/'blocks/myprogress/herbology-published-attempt-fast.php'
    original=page.read_bytes();modified=patch(original.decode()).encode()
    updates={helper:Path(__file__).with_name(helper.name).read_bytes(),page:modified}
    originals={p:p.read_bytes() if p.exists() else None for p in updates}
    with tempfile.TemporaryDirectory() as td:
        for i,(path,content) in enumerate(updates.items()):
            f=Path(td)/(str(i)+'.php');f.write_bytes(content);subprocess.run(['php','-l',str(f)],check=True)
    backup=args.backup_root/('fast-published-attempts-'+DT.datetime.now(DT.timezone.utc).strftime('%Y%m%d-%H%M%S-%f'))
    backup.mkdir(parents=True,exist_ok=False)
    for i,(path,data) in enumerate(originals.items()):
        if data is not None:(backup/(str(i)+'-'+path.name)).write_bytes(data)
    (backup/'restore.json').write_text(json.dumps([{'path':str(p),'backup':str(backup/(str(i)+'-'+p.name)) if data is not None else None} for i,(p,data) in enumerate(originals.items())],indent=2))
    for path,expected in originals.items():
        if (path.read_bytes() if path.exists() else None)!=expected:raise RuntimeError('El código cambió durante la preparación; no se instaló nada.')
    installed=[];owner=page.stat()
    try:
        for path,data in updates.items():atomic(path,data,owner);installed.append(path)
    except BaseException:
        for path in reversed(installed):
            if originals[path] is None:path.unlink(missing_ok=True)
            else:atomic(path,originals[path],owner)
        raise
    print('OK: las modalidades publicadas no recorren ni actualizan el banco completo al abrirse.')
    print('OK: texto publicado preparado por versión; la selección adaptativa y la mezcla siguen ejecutándose en cada intento.')
    print('No se cambiaron preguntas, respuestas, cuotas, resultados ni temporizadores.')
    print('RESPALDO:',backup)
if __name__=='__main__':main()
