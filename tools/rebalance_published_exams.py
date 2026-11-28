#!/usr/bin/env python3
"""Apply a guarded redistribution of existing approved published exam content."""
import argparse
import collections as C
import copy
import datetime as DT
import fcntl
import hashlib
import json
import os
from pathlib import Path
import re
import shutil
import subprocess
import sys
import tempfile
import audit_exam_diversity as audit
import plan_exam_versions as planner


def complete_es(q):
    es = audit.spanish(q)
    opts = audit.options(es)
    return (audit.usable(es.get('prompt')) and audit.usable(es.get('explanation'))
            and len(opts) == 4 and all(audit.usable(o['text']) for o in opts)
            and len({o['label'] for o in opts}) == 4
            and (not es.get('correctLabel') or es['correctLabel'] == q.get('correctLabel')))


def student_ready(q):
    opts = audit.options(q)
    return (audit.usable(q.get('prompt')) and audit.usable(q.get('explanation'))
            and len(opts) == 4 and all(audit.usable(o['text']) for o in opts)
            and len({audit.norm(o['text']) for o in opts}) == 4
            and q.get('correctLabel') in {o['label'] for o in opts}
            and bool((q.get('source') or {}).get('title'))
            and bool((q.get('source') or {}).get('locator'))
            and bool((q.get('sourceCard') or {}).get('url'))
            and bool((q.get('sourceCard') or {}).get('urlEs')))


def features(q):
    stem = audit.norm(q.get('prompt'))
    return tuple(int(bool(re.search(regex, stem))) for regex in audit.TOPICS.values()) + (int(bool(audit.CLINICAL.search(stem))),)


def redistribute(plans):
    """Same-domain swaps conserve corpus totals, hierarchy and Spanish coverage.

    Minimize dispersion of observable stem topics. This is not psychometric
    equating and does not assign clinical difficulty to an exam.
    """
    vectors = {id(q): features(q) for p in plans for q in p['items']}
    feature = lambda q: vectors[id(q)]
    totals = [[sum(feature(q)[k] for q in p['items']) for k in range(len(audit.TOPICS)+1)] for p in plans]
    cohort = [sum(t[k] for t in totals) for k in range(len(totals[0]))]
    weights = [1/max(5, n/len(plans)) for n in cohort]
    swaps = []
    initial_dispersion = sum(weights[k]*sum((t[k]-cohort[k]/len(plans))**2 for t in totals) for k in range(len(weights)))
    for iteration in range(60):
        best = None; bestgain = 1e-8
        for a in range(len(plans)):
            for b in range(a+1, len(plans)):
                groups = []
                for p in [plans[a], plans[b]]:
                    g = C.defaultdict(list)
                    for q in p['items']:
                        g[(q['domainId'], audit.hierarchy(q), complete_es(q))].append(q)
                    # One representative per distinct feature vector suffices.
                    groups.append({key: list({feature(q): q for q in qs}.values()) for key, qs in g.items()})
                for group in groups[0].keys() & groups[1].keys():
                    for qa in groups[0][group]:
                        fa = feature(qa)
                        for qb in groups[1][group]:
                            fb = feature(qb)
                            delta = [y-x for x,y in zip(fa,fb)]
                            gain = sum(w*(-2*d*(totals[a][k]-totals[b][k])-2*d*d) for k,(d,w) in enumerate(zip(delta,weights)))
                            if gain > bestgain:
                                bestgain = gain; best = (a,b,qa,qb,delta)
        if not best: break
        a,b,qa,qb,delta = best
        ia = plans[a]['items'].index(qa); ib = plans[b]['items'].index(qb)
        plans[a]['items'][ia], plans[b]['items'][ib] = qb,qa
        for k,d in enumerate(delta): totals[a][k] += d; totals[b][k] -= d
        if len(swaps) and len(swaps)%10==0:print('Repartiendo temas:',len(swaps),'intercambios verificados...',flush=True)
        swaps.append({'a':plans[a]['name'], 'b':plans[b]['name'], 'a_to_b':qa['questionId'], 'b_to_a':qb['questionId']})
    final_dispersion = sum(weights[k]*sum((t[k]-cohort[k]/len(plans))**2 for t in totals) for k in range(len(weights)))
    if final_dispersion > initial_dispersion + 1e-8:raise RuntimeError('La redistribución empeoraría la dispersión de temas.')
    print(f'Dispersión temática: {initial_dispersion:.2f} → {final_dispersion:.2f}.',flush=True)
    return swaps


def set_exam_one(doc, questions):
    doc = copy.deepcopy(doc)
    selected = {q['questionId']: q for q in questions}
    for domain, qs in doc.get('domains', {}).items():
        doc['domains'][domain]=[q for q in qs if q.get('questionId') not in selected]
        for q in doc['domains'][domain]:
            if (q.get('examTemplate') or {}).get('globalPosition'):
                q['examTemplate'] = dict(q['examTemplate'], globalPosition=0)
    for qid,q in selected.items():
        doc.setdefault('domains', {}).setdefault(q['domainId'], []).append(copy.deepcopy(q))
    return doc


def write_atomic(path, content):
    st = path.stat()
    fd, temp = tempfile.mkstemp(prefix='.rebalance-', dir=path.parent)
    try:
        with os.fdopen(fd, 'wb') as out:out.write(content);out.flush();os.fsync(out.fileno())
        os.chmod(temp, st.st_mode & 0o777)
        os.chown(temp, st.st_uid, st.st_gid)
        os.replace(temp,path)
    finally:
        if os.path.exists(temp):os.unlink(temp)


def run(root, data, backup):
    storepath = data/'board-domain-banks/herbology-question-overrides.json'
    basepath = root/'blocks/menu/herbology-domain-candidates.json'
    paths = [storepath, basepath, root/'blocks/menu/herbology-domain-bank.json', data/'board-domain-banks/herbology-domain-bank.json']
    paths = [p for p in paths if p.is_file()]
    originals = {p:p.read_bytes() for p in paths}
    store = audit.read_json(storepath)
    all_raw,sources = audit.load_exams(root,data)
    raw = [e for e in all_raw if e['state'] in ['publicado','configurado en banco'] and len(e['questions']) == 100]
    if not raw:raise RuntimeError('No hay exámenes publicados completos para equilibrar.')
    # Drafts remain outside the published corpus and retain their own IDs.
    reserved = {q['questionId'] for e in all_raw if e not in raw for q in e['questions'] if q.get('questionId')}
    normalized,mode = audit.normalize_exams(raw,root,data,True)
    herbs = audit.read_json(root/'blocks/myprogress/single-herb-card-index.json')['herbs']
    formulas = audit.read_json(root/'blocks/myprogress/formula-board-roster-2026.json')['formulas']
    stats,_,_ = audit.analyze(normalized,raw,herbs,formulas)
    approved = set()
    for key in ['questions','customQuestions']:
        approved.update(qid for qid,r in store.get(key,{}).items() if r.get('status') == 'approved')
    frozen = {q['questionId']:q for e in raw for q in e['questions']}
    catalog = planner.load_catalog(root,data)
    # Only already published snapshots or administrator-approved bank records.
    # Do not auto-approve original SCORM candidates or pending AI revisions.
    catalog = {qid:q for qid,q in catalog.items() if qid in approved and qid not in reserved
               and student_ready(q) and complete_es(q) and not q.get('_planExcluded') and not q.get('_planPendingRevision')}
    for qid,q in frozen.items():
        if student_ready(q) and complete_es(q):catalog[qid] = copy.deepcopy(q)
    for section in ['questions','customQuestions']:
        for qid,record in store.get(section,{}).items():
            if record.get('status') in ['deleted','study_archive','extra_guideline','exclusion_candidate']:
                catalog[qid]={'questionId':qid,'_planExcluded':True}
    plans = planner.make_plan(raw,normalized,stats,catalog)
    blocked = [p for p in plans if not p['structure_complete']]
    if blocked:
        backup.mkdir(parents=True,exist_ok=False)
        gaps = ['NO SE CAMBIÓ NINGÚN EXAMEN. Faltan preguntas ya aprobadas y bilingües:']
        for p in blocked:gaps.append(p['name']+': '+'; '.join(p['blocks']))
        gaps.append('Jerarquía aprobada, bilingüe y con fuente completa en el catálogo: '+str(sum(audit.hierarchy(q) for q in catalog.values())))
        (backup/'FALTANTES.txt').write_text('\n'.join(gaps)+'\n')
        raise RuntimeError('\n'.join(gaps)+'\nDetalle: '+str(backup/'FALTANTES.txt'))
    swaps = redistribute(plans)
    now = DT.datetime.now(DT.timezone.utc).isoformat()
    before = {e['key']:e for e in raw}; seenids=set(); seenstems=set(); changes=[]
    finalexams=[]
    for p in plans:
        qs=p['items']; key=p['key']; old=before[key]
        numbermatch = re.search(r'(?:exam|esam)[ _-]*(\d+)',key+' '+p['name'],re.I)
        if not numbermatch:raise RuntimeError('Número de examen no resuelto: '+p['name'])
        number=int(numbermatch[1]); snapshotkey='simulation_exam_'+str(number)
        counts=C.Counter(q['domainId'] for q in qs)
        if len(qs)!=100 or sum(audit.hierarchy(q) for q in qs)!=5 or any(counts[d]!=w for d,(_,w) in audit.DOMAINS.items()):raise RuntimeError('Cuotas inválidas: '+p['name'])
        if sum(not complete_es(q) for q in qs)>sum(not complete_es(q) for q in old['questions']):raise RuntimeError('Regresión de traducción: '+p['name'])
        for q in qs:
            qid=q['questionId']; stem=audit.norm(q.get('prompt'))
            if qid in seenids or stem in seenstems:raise RuntimeError('Pregunta repetida: '+qid)
            if not student_ready(q):raise RuntimeError('Pregunta sin estructura o tarjeta fuente completa: '+qid)
            seenids.add(qid);seenstems.add(stem)
        qs.sort(key=lambda q:(list(audit.DOMAINS).index(q['domainId']),q['questionId']))
        clean=[]
        for i,q in enumerate(qs,1):
            q={k:v for k,v in q.items() if not k.startswith('_')}
            q['examTemplate']={'examNumber':number,'globalPosition':i};clean.append(q)
        oldrecord=store.get('publishedExams',{}).get(snapshotkey,{})
        record=dict(oldrecord, id=snapshotkey, examNumber=number, name='Exam '+str(number), status='published',
                    version=int(oldrecord.get('version') or 0)+1, targetCount=100, domainCounts=dict(counts), questions=clean,
                    publishedAt=now, publishedByName='Authorized exam diversity rebalance', diversityBalancedAt=now,
                    diversityPolicy={'hierarchyPer100':5,'domainWeights':[28,24,18,15,10,5],'topicSwaps':len(swaps)})
        store.setdefault('publishedExams',{})[snapshotkey]=record
        project=store.get('examProjects',{}).get(record.get('projectId'))
        if project is not None:
            project.update(status='published', questionIds=[q['questionId'] for q in clean], confirmedQuestionIds=[q['questionId'] for q in clean],
                currentCount=100, missingCount=0, publishedVersion=record['version'], publishedAt=now,updatedAt=now)
        if number==1:
            # Unlike later exams, Exam 1 is rendered from the live bank plus
            # approved revisions. Refuse a historical snapshot that would be
            # silently replaced by a different current revision on that page.
            for q in clean:
                qid=q['questionId']
                revision=store.get('questions',{}).get(qid) or store.get('customQuestions',{}).get(qid) or {}
                live=revision.get('approved') or (revision.get('draft') if revision.get('status')=='approved' else {}) or {}
                for field in audit.FIELDSET:
                    if field in live and live[field]!=q.get(field):
                        raise RuntimeError('Exam 1 usaría una revisión diferente de '+qid+'; no se cambió ningún examen.')
            for path in paths:
                if path!=storepath:
                    document=set_exam_one(audit.read_json(path),clean)
                    originals.setdefault(path,path.read_bytes())
                    # Staged below; no production write until all checks pass.
                    changes.append((path,document))
            # Custom questions are merged over the bank before rendering.
            positions={q['questionId']:q['examTemplate'] for q in clean}
            for qid,r in store.get('customQuestions',{}).items():
                for state in ['approved','draft']:
                    if isinstance(r.get(state),dict):r[state]['examTemplate']=positions.get(qid,{'globalPosition':0})
        finalexams.append(dict(old,key=snapshotkey,name=record['name'],state='publicado',version=record['version'],questions=clean,
            provenance='Versión '+str(record['version'])+' publicada tras redistribuir preguntas existentes. Métricas del conjunto final de 100 preguntas.'))
    # Validate the resulting frozen cohort and render only final exam metrics.
    normalized_final,finalmode=audit.normalize_exams(finalexams,root,data,True)
    finalstats,shared,near=audit.analyze(normalized_final,finalexams,herbs,formulas)
    if shared:raise RuntimeError('IDs compartidos en el resultado final.')
    report={'created_at':now,'normalization':finalmode,'sources':sources,'hierarchy_policy':'5 por 100: referencia acordada',
            'exams':finalstats,'shared_ids':shared,'near_stem_pairs':near}
    document,findings=audit.render(report)
    document=document.replace('No se ha cambiado ninguna pregunta, examen, intento ni publicación.',
        'Se publicaron las nuevas composiciones de los exámenes completos. No se reescribieron enunciados, respuestas ni explicaciones; no se modificaron intentos.')
    document=re.sub(r'<h2>Plan para equilibrar versiones nuevas</h2>.*?</ol>', '', document, flags=re.S)
    backup.mkdir(parents=True,exist_ok=False)
    for i,(path,content) in enumerate(originals.items()):(backup/(str(i)+'-'+path.name)).write_bytes(content)
    (backup/'restauracion.json').write_text(json.dumps([{'path':str(p),'backup':str(backup/(str(i)+'-'+p.name))} for i,p in enumerate(originals)],indent=2))
    store['updatedAt']=now
    changes.append((storepath,store))
    # Under the site's existing revision-store lock, refuse concurrent changes.
    for path,content in originals.items():
        if path.read_bytes()!=content:raise RuntimeError('Cambió un archivo durante el cálculo: '+str(path))
    installed=[]
    try:
        for path,doc in changes:
            payload=(json.dumps(doc,ensure_ascii=False,indent=2)+'\n').encode()
            write_atomic(path,payload);installed.append(path)
        actual,_=audit.load_exams(root,data)
        actualmap={e['key']:e for e in actual}
        for e in finalexams:
            if actualmap.get(e['key'],{}).get('questions')!=e['questions']:
                raise RuntimeError('La lectura posterior no coincide con el examen guardado: '+e['name'])
        (backup/'auditoria-final.html').write_text(document)
        (backup/'auditoria-final.json').write_text(json.dumps(report,ensure_ascii=False,indent=2))
        (backup/'intercambios-tematicos.json').write_text(json.dumps(swaps,ensure_ascii=False,indent=2))
    except BaseException:
        for path in reversed(installed):write_atomic(path,originals[path])
        raise
    for e in finalstats:print(f"{e['name']}: {e['registered_questions']} preguntas; jerarquía {e['hierarchy_count']}; español incompleto {e['missing_spanish']}.",flush=True)
    print('DOMINIOS: 28 / 24 / 18 / 15 / 10 / 5. IDs compartidos: 0.')
    print('Intercambios adicionales para repartir tipos de pregunta:',len(swaps))
    print('Los proyectos incompletos siguen pendientes; no se publicaron como terminados.')
    print('Las tarjetas del alumno se recalculan con las nuevas preguntas al recargar.')
    print('AUDITORÍA FINAL:',backup/'auditoria-final.html')
    print('RESPALDO:',backup)


def main():
    ap=argparse.ArgumentParser(description=__doc__)
    ap.add_argument('--moodle-root',type=Path,default=Path('/var/www/html/moodle'))
    ap.add_argument('--data-root',type=Path,default=Path('/mnt/volume_nyc1_01/moodledata'))
    ap.add_argument('--backup-root',type=Path,default=Path('/var/backups/acupuncturetests'))
    args=ap.parse_args()
    print('Equilibrando versiones publicadas con preguntas ya aprobadas. Se verifican los resultados antes de guardar.',flush=True)
    lockpath=args.data_root/'board-domain-banks/herbology-question-overrides.json.lock'
    with lockpath.open('a+') as lock:
        fcntl.flock(lock,fcntl.LOCK_EX)
        backup=args.backup_root/('exam-balanced-versions-'+DT.datetime.now(DT.timezone.utc).strftime('%Y%m%d-%H%M%S-%f'))
        run(args.moodle_root,args.data_root,backup)


if __name__=='__main__':
    try:main()
    except Exception as error:raise SystemExit('ERROR: '+str(error))
