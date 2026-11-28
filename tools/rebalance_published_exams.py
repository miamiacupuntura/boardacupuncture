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
from urllib.parse import urlencode
import audit_exam_diversity as audit
import plan_exam_versions as planner


def complete_es(q):
    es = audit.spanish(q)
    opts = audit.options(es)
    return (audit.usable(es.get('prompt')) and audit.usable(es.get('explanation'))
            and len(opts) == 4 and all(audit.usable(o['text']) for o in opts)
            and len({o['label'] for o in opts}) == 4
            and (not es.get('correctLabel') or es['correctLabel'] == q.get('correctLabel')))


def student_issues(q):
    opts = audit.options(q)
    issues=[]
    for field in ['prompt','explanation']:
        if not audit.usable(q.get(field)):issues.append(field)
    if len(opts)!=4 or not all(audit.usable(o['text']) for o in opts):issues.append('options')
    if len({audit.norm(o['text']) for o in opts})!=4:issues.append('distinct option texts')
    if len({o['label'] for o in opts})!=4:issues.append('distinct option labels')
    if q.get('correctLabel') not in {o['label'] for o in opts}:issues.append('correctLabel')
    for group,fields in [('source',['title','locator']),('sourceCard',['url','urlEs'])]:
        record=q.get(group) if isinstance(q.get(group),dict) else {}
        for field in fields:
            if not audit.usable(record.get(field)):issues.append(group+'.'+field)
    return issues


def student_ready(q):
    return not student_issues(q)


def question_identity(q):
    return (audit.norm(q.get('prompt')),tuple((o['label'],audit.norm(o['text'])) for o in audit.options(q)),q.get('correctLabel'))


def presentation_signature(q):
    return (audit.norm(q.get('prompt')), tuple(sorted(audit.norm(o['text']) for o in audit.options(q))))


def duplicate_signature(q):
    return presentation_signature(q)+(audit.norm(audit.answer(q)),)


def repeated_items(exams):
    seen={};duplicates={};presentations=C.defaultdict(list)
    for exam in exams:
        for q in exam['questions']:
            qid=q['questionId'];signature=duplicate_signature(q)
            presentations[presentation_signature(q)].append((exam['name'],q))
            if signature in seen and seen[signature]!=qid:duplicates[qid]=seen[signature]
            else:seen[signature]=qid
    conflicts=[]
    for group in presentations.values():
        if len({audit.norm(audit.answer(q)) for _,q in group})>1:
            conflicts.append(', '.join(name+' / '+q['questionId'] for name,q in group))
    return duplicates,conflicts


def prepare_reviewed_replacements(root, store, catalog):
    """Stage the two explicitly authored, bilingual, source-reviewed additions.

    These are new questions, never renamed duplicates or automatically approved
    SCORM candidates. They are persisted only with a successful exam update.
    """
    path=Path(__file__).with_name('balanced-exam-replacements.json')
    if not path.is_file():raise RuntimeError('Falta el archivo de las dos preguntas nuevas; no se cambió ningún examen.')
    questions=audit.read_json(path).get('questions',[])
    expected={'BAL-FD-20261128-001':'formula_differentiation','BAL-DY-20261128-001':'dui_yao'}
    if {q.get('questionId'):q.get('domainId') for q in questions}!=expected or len(questions)!=2:
        raise RuntimeError('El lote de sustitución no contiene las dos preguntas previstas.')
    profilepath=root/'blocks/myprogress/formulas-study-guide-data.json'
    pairpath=root/'blocks/myprogress/dui-yao-smart-data.json'
    profiles={audit.norm(q['name']):q for q in audit.read_json(profilepath)['formulas']}
    names=['Si Jun Zi Tang','Bu Zhong Yi Qi Tang','Gui Pi Tang','Shi Quan Da Bu Tang']
    roster={audit.norm(q['name']) for q in audit.read_json(root/'blocks/myprogress/formula-board-roster-2026.json')['formulas']}
    if any(audit.norm(n) not in profiles or audit.norm(n) not in roster for n in names):
        raise RuntimeError('No se encontraron todos los perfiles y nombres oficiales de la sustituta de fórmulas.')
    target=profiles[audit.norm('Bu Zhong Yi Qi Tang')]
    if 'sinking' not in audit.norm(target.get('pattern')) or not re.search(r'\brais\w*\b',audit.norm(target.get('action'))):
        raise RuntimeError('El perfil de Bu Zhong Yi Qi Tang no respalda las pistas preparadas; no se publica la sustituta.')
    pairs={frozenset(audit.norm(n) for n in q['herbs']):q for q in audit.read_json(pairpath)['pairs']}
    for herbs in [('Bai Shao','Gan Cao'),('Gui Zhi','Bai Shao'),('Chai Hu','Bai Shao'),('Jie Geng','Gan Cao')]:
        if frozenset(map(audit.norm,herbs)) not in pairs:raise RuntimeError('Falta una pareja fuente de la pregunta nueva de Dui Yao.')
    pair=pairs[frozenset(map(audit.norm,['Bai Shao','Gan Cao']))]
    if 'relax spasm' not in audit.norm(pair.get('action',{}).get('en')):
        raise RuntimeError('La pareja fuente no respalda la acción antiespasmódica; no se publica la sustituta.')
    herb_roster={audit.norm(q['name']) for q in audit.read_json(root/'blocks/myprogress/single-herb-card-index.json')['herbs']}
    if any(audit.norm(n) not in herb_roster for n in ['Bai Shao','Gan Cao','Gui Zhi','Chai Hu','Jie Geng']):
        raise RuntimeError('La sustituta de Dui Yao contiene un nombre fuera del roster.')
    reviewed=[];now=DT.datetime.now(DT.timezone.utc).isoformat()
    for q in questions:
        qid=q['questionId']
        if not student_ready(q) or not complete_es(q):raise RuntimeError('Sustituta incompleta: '+qid)
        current=store.get('customQuestions',{}).get(qid)
        if current is not None:
            if current.get('status')!='approved' or duplicate_signature(current.get('approved') or {})!=duplicate_signature(q):
                raise RuntimeError('Ya existe otra revisión del ID de sustitución: '+qid)
            catalog[qid]=dict(current['approved'],_planAlreadyReviewed=True)
            continue
        record={'status':'approved','domainId':q['domainId'],'revisionNumber':1,'approved':copy.deepcopy(q),
                'savedAt':now,'savedByName':'Source-reviewed exam balancing addition',
                'sourceReview':{'reviewer':'Assistant source review; installed by administrator command',
                    'method':'Authored bilingual question; comparison with existing formula/pair profiles and official roster',
                    'files':{str(p):hashlib.sha256(p.read_bytes()).hexdigest() for p in [profilepath,pairpath]}}}
        store.setdefault('customQuestions',{})[qid]=record
        catalog[qid]=dict(q,_planAlreadyReviewed=True)
        reviewed.append(qid)
    return reviewed


def mirror_references(q, fields, catalog, store):
    targets=[catalog.get(q['questionId'],{})]
    for section in ['questions','customQuestions']:
        record=store.get(section,{}).get(q['questionId'],{})
        snapshot=record.get('approved') or (record.get('draft') if record.get('status')=='approved' else None)
        if isinstance(snapshot,dict):targets.append(snapshot)
    for target in targets:
        if question_identity(target)!=question_identity(q):continue
        for field in fields:
            group,name=field.split('.')
            if not isinstance(target.get(group),dict):target[group]={}
            if not audit.usable(target[group].get(name)):target[group][name]=q[group][name]


def add_original_review_links(exams, catalog, store):
    notes=[]
    for exam in exams:
        for q in exam['questions']:
            issues=set(student_issues(q))
            if not issues or issues-{'sourceCard.url','sourceCard.urlEs'}:continue
            if not isinstance(q.get('sourceCard'),dict):q['sourceCard']={}
            card=q['sourceCard']
            for name,lang in [('url','en'),('urlEs','es')]:
                if not audit.usable(card.get(name)):
                    card[name]='/blocks/myprogress/herbology-exam-source-note.php?'+urlencode({'question':q['questionId'],'lang':lang})
            for name,text in [('label','Original reference and saved explanation'),('labelEs','Referencia original y explicación guardada'),
                              ('referenceType','published_question_review')]:
                if not audit.usable(card.get(name)):card[name]=text
            mirror_references(q,['sourceCard.'+name for name in ['url','urlEs','label','labelEs','referenceType']],catalog,store)
            notes.append({'id':q['questionId'],'exam':exam['name'],'type':'original_citation_and_saved_explanation'})
    return notes


def recover_source_metadata(exams, root, data, catalog, store):
    """Fill missing references only from the same ID and same question content.

    Never synthesize source URLs or transfer references between different
    question revisions. All unresolved cases must be replaced by the planner.
    """
    byid=C.defaultdict(list)
    for q in catalog.values():
        if q.get('questionId'):byid[q['questionId']].append(q)
    for path in [root/'blocks/menu/herbology-domain-candidates.json',root/'blocks/menu/herbology-domain-bank.json',
                 data/'board-domain-banks/herbology-domain-bank.json',data/'board-domain-banks/herbology-scorm-question-bank.json']:
        for questions in audit.read_json(path,optional=True).get('domains',{}).values():
            for q in questions:
                if isinstance(q,dict) and q.get('questionId'):byid[q['questionId']].append(q)
    recovered=[]
    for exam in exams:
        for q in exam['questions']:
            if student_ready(q):continue
            fields=[]
            for candidate in byid.get(q.get('questionId'),[]):
                if question_identity(candidate)!=question_identity(q):continue
                for group,names in [('source',['title','locator']),('sourceCard',['url','urlEs'])]:
                    source=candidate.get(group) if isinstance(candidate.get(group),dict) else {}
                    if not isinstance(q.get(group),dict):q[group]={}
                    for name in names:
                        if not audit.usable(q[group].get(name)) and audit.usable(source.get(name)):
                            q[group][name]=source[name];fields.append(group+'.'+name)
            if fields:
                recovered.append({'id':q['questionId'],'exam':exam['name'],'fields':fields})
                mirror_references(q,fields,catalog,store)
    return recovered


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
        stems=[C.Counter(audit.norm(q.get('prompt')) for q in p['items']) for p in plans]
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
                            sa=audit.norm(qa.get('prompt'));sb=audit.norm(qb.get('prompt'))
                            if sa!=sb and (stems[a][sb] or stems[b][sa]):continue
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
    exists=path.exists()
    st = path.stat() if exists else path.parent.stat()
    fd, temp = tempfile.mkstemp(prefix='.rebalance-', dir=path.parent)
    try:
        with os.fdopen(fd, 'wb') as out:out.write(content);out.flush();os.fsync(out.fileno())
        os.chmod(temp, (st.st_mode & 0o777) if exists else 0o644)
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
    catalog = planner.load_catalog(root,data)
    additions=prepare_reviewed_replacements(root,store,catalog)
    if additions:print('Preguntas nuevas bilingües revisadas contra sus perfiles fuente: '+', '.join(additions),flush=True)
    recovered=recover_source_metadata(raw,root,data,catalog,store)
    notes=add_original_review_links(raw,catalog,store)
    routepath=root/'blocks/myprogress/herbology-exam-source-note.php'
    template=Path(__file__).with_name('herbology-exam-source-note.php')
    if notes or additions:
        if not template.is_file():raise RuntimeError('Falta el archivo de la ficha de consulta; no se cambió ningún examen.')
        originals[routepath]=routepath.read_bytes() if routepath.exists() else None
        print('Fichas de referencia y explicación guardada preparadas:',len(notes),flush=True)
    invalid={q['questionId']:student_issues(q) for e in raw for q in e['questions'] if not student_ready(q)}
    duplicate_ids,conflicts=repeated_items(raw)
    if conflicts:
        raise RuntimeError('NO SE CAMBIÓ NINGÚN EXAMEN. Enunciado y opciones iguales con respuestas contradictorias:\n'+'\n'.join(conflicts))
    if duplicate_ids:print('Copias completas a sustituir: '+', '.join(sorted(duplicate_ids)),flush=True)
    if recovered:print('Referencias recuperadas de la misma pregunta:',len(recovered),flush=True)
    if invalid:print('Preguntas incompletas a sustituir antes de repartir temas: '+', '.join(sorted(invalid)),flush=True)
    normalized,mode = audit.normalize_exams(raw,root,data,True)
    herbs = audit.read_json(root/'blocks/myprogress/single-herb-card-index.json')['herbs']
    formulas = audit.read_json(root/'blocks/myprogress/formula-board-roster-2026.json')['formulas']
    stats,_,_ = audit.analyze(normalized,raw,herbs,formulas)
    approved = set()
    for key in ['questions','customQuestions']:
        approved.update(qid for qid,r in store.get(key,{}).items() if r.get('status') == 'approved')
    frozen = {q['questionId']:q for e in raw for q in e['questions']}
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
    # Apply the student-facing checks before retaining existing members, not
    # just to incoming candidates. The exclusion sentinel prevents the planner
    # from reintroducing an incomplete published snapshot from its raw pool.
    for qid in invalid:catalog[qid]={'questionId':qid,'_planExcluded':True}
    for qid in duplicate_ids:catalog[qid]={'questionId':qid,'_planExcluded':True}
    plans = planner.make_plan(raw,normalized,stats,catalog)
    # The hierarchy reference is an unmet content need, not a reason to stop
    # improving the published corpus's other topics. Structural failures still
    # block the entire write; an underfilled exam is never published.
    blocked = [p for p in plans if p['after']['count'] != 100 or any(
        p['after']['domains'].get(d,0) != w for d,(_,w) in audit.DOMAINS.items())]
    if blocked:
        backup.mkdir(parents=True,exist_ok=False)
        gaps = ['NO SE CAMBIÓ NINGÚN EXAMEN. Faltan preguntas ya aprobadas y bilingües:']
        for p in blocked:gaps.append(p['name']+': '+'; '.join(p['blocks']))
        gaps.append('Jerarquía aprobada, bilingüe y con fuente completa en el catálogo: '+str(sum(audit.hierarchy(q) for q in catalog.values())))
        for qid,issues in sorted(invalid.items()):gaps.append(qid+': faltan o son inválidos '+', '.join(issues))
        (backup/'FALTANTES.txt').write_text('\n'.join(gaps)+'\n')
        raise RuntimeError('\n'.join(gaps)+'\nDetalle: '+str(backup/'FALTANTES.txt'))
    swaps = redistribute(plans)
    now = DT.datetime.now(DT.timezone.utc).isoformat()
    before = {e['key']:e for e in raw}; seenids=set(); seenitems=set(); changes=[]
    if notes or additions:changes.append((routepath,template.read_bytes()))
    finalexams=[]
    for p in plans:
        qs=p['items']; key=p['key']; old=before[key]
        numbermatch = re.search(r'(?:exam|esam)[ _-]*(\d+)',key+' '+p['name'],re.I)
        if not numbermatch:raise RuntimeError('Número de examen no resuelto: '+p['name'])
        number=int(numbermatch[1]); snapshotkey='simulation_exam_'+str(number)
        counts=C.Counter(q['domainId'] for q in qs)
        if len(qs)!=100 or sum(audit.hierarchy(q) for q in qs)>5 or any(counts[d]!=w for d,(_,w) in audit.DOMAINS.items()):raise RuntimeError('Cuotas inválidas: '+p['name'])
        if sum(not complete_es(q) for q in qs)>sum(not complete_es(q) for q in old['questions']):raise RuntimeError('Regresión de traducción: '+p['name'])
        for q in qs:
            qid=q['questionId']; signature=duplicate_signature(q)
            if qid in seenids:raise RuntimeError('ID repetido entre exámenes: '+qid)
            if signature in seenitems:raise RuntimeError('Pregunta completa repetida (enunciado, opciones y respuesta): '+qid)
            if not student_ready(q):raise RuntimeError('Pregunta sin estructura o tarjeta fuente completa: '+qid)
            seenids.add(qid);seenitems.add(signature)
        qs.sort(key=lambda q:(list(audit.DOMAINS).index(q['domainId']),q['questionId']))
        clean=[]
        for i,q in enumerate(qs,1):
            q={k:v for k,v in q.items() if not k.startswith('_')}
            q['examTemplate']={'examNumber':number,'globalPosition':i};clean.append(q)
        oldrecord=store.get('publishedExams',{}).get(snapshotkey,{})
        record=dict(oldrecord, id=snapshotkey, examNumber=number, name='Exam '+str(number), status='published',
                    version=int(oldrecord.get('version') or 0)+1, targetCount=100, domainCounts=dict(counts), questions=clean,
                    publishedAt=now, publishedByName='Authorized exam diversity rebalance', diversityBalancedAt=now,
                    diversityPolicy={'hierarchyTargetPer100':5,'hierarchyActual':sum(audit.hierarchy(q) for q in qs),
                        'hierarchyPending':max(0,5-sum(audit.hierarchy(q) for q in qs)),
                        'status':'hierarchy_target_met' if sum(audit.hierarchy(q) for q in qs)==5 else 'topic_balance_applied_hierarchy_pending',
                        'domainWeights':[28,24,18,15,10,5],'topicSwaps':len(swaps)})
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
    pending=sum(max(0,5-e['hierarchy_count']) for e in finalstats)
    report['balance_status']='topic_balance_applied_hierarchy_pending' if pending else 'hierarchy_target_met'
    report['hierarchy_questions_pending']=pending
    report['source_references_recovered']=recovered
    report['incomplete_questions_replaced']=invalid
    report['original_review_notes_created']=notes
    report['complete_duplicate_questions_replaced']=duplicate_ids
    report['new_source_reviewed_questions']=additions
    sharedstems=C.defaultdict(list)
    for exam in finalexams:
        for q in exam['questions']:sharedstems[audit.norm(q.get('prompt'))].append({'exam':exam['name'],'id':q['questionId']})
    report['shared_stems_with_different_options']=[group for group in sharedstems.values() if len(group)>1]
    if report['shared_stems_with_different_options']:
        evidence=audit.table(['Preguntas con enunciado compartido','Revisión'],[
            [', '.join(item['exam']+' / '+item['id'] for item in group),'Opciones diferentes; revisar posible solapamiento de objetivos']
            for group in report['shared_stems_with_different_options']])
        document=document.replace('</main>', '<h2>Enunciados compartidos con opciones distintas</h2>'+evidence+'</main>')
    if pending:
        document=document.replace('<h2>Resumen ejecutivo basado en los datos</h2>',
            '<p class="note">Se aplicó el reparto de temas con preguntas existentes. Sigue pendiente incorporar '+str(pending)+
            ' preguntas de jerarquía aprobadas y bilingües para llegar a cinco por examen. No se declara cumplida esa meta.</p><h2>Resumen ejecutivo basado en los datos</h2>')
    backup.mkdir(parents=True,exist_ok=False)
    for i,(path,content) in enumerate(originals.items()):
        if content is not None:(backup/(str(i)+'-'+path.name)).write_bytes(content)
    (backup/'restauracion.json').write_text(json.dumps([{'path':str(p),'backup':str(backup/(str(i)+'-'+p.name)) if content is not None else None} for i,(p,content) in enumerate(originals.items())],indent=2))
    store['updatedAt']=now
    changes.append((storepath,store))
    # Under the site's existing revision-store lock, refuse concurrent changes.
    for path,content in originals.items():
        current=path.read_bytes() if path.exists() else None
        if current!=content:raise RuntimeError('Cambió un archivo durante el cálculo: '+str(path))
    installed=[]
    try:
        for path,doc in changes:
            payload=doc if isinstance(doc,bytes) else (json.dumps(doc,ensure_ascii=False,indent=2)+'\n').encode()
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
        for path in reversed(installed):
            if originals[path] is None:path.unlink(missing_ok=True)
            else:write_atomic(path,originals[path])
        raise
    for e in finalstats:print(f"{e['name']}: {e['registered_questions']} preguntas; jerarquía {e['hierarchy_count']}; español incompleto {e['missing_spanish']}.",flush=True)
    print('DOMINIOS: 28 / 24 / 18 / 15 / 10 / 5. IDs compartidos: 0.')
    print('Intercambios adicionales para repartir tipos de pregunta:',len(swaps))
    if pending:print('PENDIENTE: faltan',pending,'preguntas de jerarquía aprobadas y bilingües para llegar a cinco por examen.')
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
