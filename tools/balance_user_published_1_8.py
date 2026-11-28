#!/usr/bin/env python3
"""Balance Exams 1-8 from human-approved publications only; reserve leftovers for 9."""
import argparse,collections as C,copy,datetime as DT,fcntl,hashlib,html,json,re,subprocess,tempfile
from pathlib import Path
import audit_exam_diversity as a
import plan_exam_versions as p
import rebalance_published_exams as r

EXCLUDED={'deleted','study_archive','extra_guideline','exclusion_candidate'}

def human_actor(record, actors=None):
    if not isinstance(record,dict) or record.get('status') in EXCLUDED:return 0
    if not isinstance(record.get('approved'),dict) and record.get('status')!='approved':return 0
    explicit=[int(record.get(k) or 0) for k in ['approvedById','publishedById','republishedById'] if int(record.get(k) or 0)>0]
    evidence=explicit or ([int(record.get('savedById') or 0)] if record.get('status')=='approved' else [])
    return next((actor for actor in evidence if actor>0 and (actors is None or actor in actors)),0)

def human_approval(record,actors=None):return human_actor(record,actors)>0

def exam_number(key,record):
    if not isinstance(record,dict):return None
    n=int(record.get('examNumber') or record.get('publishedExamNumber') or 0)
    if n:return n
    match=re.search(r'(?:simulation[_ -]exam[_ -]|\b(?:exam|esam)\s*)(\d+)\b',key+' '+str(record.get('name','')),re.I)
    return int(match[1]) if match else None

def clinical_hash(q):
    fields=['questionId','domainId','contentType','prompt','options','correctLabel','explanation','translations']
    return hashlib.sha256(json.dumps({k:q.get(k) for k in fields},sort_keys=True,ensure_ascii=False,separators=(',',':')).encode()).hexdigest()

def collect(root,data,store):
    catalog=p.load_catalog(root,data)
    records=dict(store.get('questions',{}));records.update(store.get('customQuestions',{}))
    actors={int(exam.get('publishedById') or 0) for key,exam in store.get('publishedExams',{}).items() if exam_number(key,exam) in range(1,9) and int(exam.get('publishedById') or 0)>0}
    pool={};origins={};members={i:set() for i in range(1,9)};protected=set()
    for key,exam in store.get('publishedExams',{}).items():
        n=exam_number(key,exam)
        if not n or exam.get('status')!='published':continue
        for q in exam.get('questions',[]):
            qid=q.get('questionId');record=records.get(qid,{}) or {}
            if n>8:protected.add(qid);continue
            if not qid or record.get('status') in EXCLUDED:continue
            # The earlier system-authored replacements are not evidence of a
            # human publication unless the administrator subsequently published them.
            automated=qid.startswith('BAL-') or bool(record.get('sourceReview'))
            if automated and not human_approval(record,actors):continue
            pool[qid]=copy.deepcopy(q);origins[qid]={'kind':'published_exam','exam':n,'version':exam.get('version')}
            members[n].add(qid)
    for key,project in store.get('examProjects',{}).items():
        n=exam_number(key,project)
        if n not in range(1,10):continue
        evidence=project.get('sourcePublicationEvidence',{})
        for q in project.get('approvedSourceSnapshots',[]):
            qid=q.get('questionId');proof=evidence.get(qid,{})
            if not qid or qid in protected or (records.get(qid,{}) or {}).get('status') in EXCLUDED:continue
            if qid.startswith('BAL-') and not human_approval(records.get(qid,{}),actors):continue
            authenticated=(proof.get('kind')=='published_exam' and int(proof.get('exam') or 0) in range(1,9)) or (proof.get('kind')=='human_approved' and int(proof.get('actor') or 0) in actors)
            if not authenticated or (proof.get('content_hash') and proof['content_hash']!=clinical_hash(q)):continue
            if qid not in pool:pool[qid]=copy.deepcopy(q);origins[qid]=copy.deepcopy(proof)
    for qid,record in records.items():
        if qid in protected or not human_approval(record,actors):continue
        approved=record.get('approved') or record.get('draft')
        if not isinstance(approved,dict):continue
        base=copy.deepcopy(approved)
        base.update(questionId=qid,domainId=approved.get('domainId') or record.get('domainId'))
        pool[qid]=base
        origins[qid]={'kind':'human_approved','actor':human_actor(record,actors)}
    for key,project in store.get('examProjects',{}).items():
        n=exam_number(key,project)
        if n in members:members[n].update(qid for qid in project.get('questionIds',[]) if qid in pool)
    # Restore references from the identical question, then create navigation
    # notes when only links are missing. Never synthesize a question or approval.
    exam={'name':'Human-published pool','questions':list(pool.values())}
    recovered=r.recover_source_metadata([exam],root,data,catalog,{})
    notes=r.add_original_review_links([exam],catalog,{})
    rejected={};unique={};duplicates={};valid={}
    for qid,q in sorted(pool.items(),key=lambda item:(not r.complete_es(item[1]),item[0])):
        if q.get('domainId') not in a.DOMAINS or not r.student_ready(q):
            rejected[qid]=r.student_issues(q) or ['unknown domain'];continue
        signature=r.duplicate_signature(q)
        if signature in unique:duplicates[qid]=unique[signature];continue
        unique[signature]=qid;valid[qid]=q
    presentations=C.defaultdict(list)
    for qid,q in valid.items():presentations[r.presentation_signature(q)].append(qid)
    conflicts=[]
    for ids in presentations.values():
        if len({a.norm(a.answer(valid[qid])) for qid in ids})>1:
            conflicts.append(ids)
            for qid in ids:valid.pop(qid,None)
    for qid,q in list(valid.items()):
        es=a.spanish(q)
        if es.get('correctLabel') and es['correctLabel']!=q.get('correctLabel'):
            rejected[qid]=['Spanish answer key differs from the published English key'];valid.pop(qid)
    for qid,q in valid.items():origins[qid]['content_hash']=clinical_hash(q)
    return valid,origins,members,{'conflicting_answer_keys':conflicts,'rejected':rejected,'duplicate_copies':duplicates,'links_recovered':recovered,'review_notes':notes,'publisher_ids':sorted(actors)}

def allocate(pool,members,numbers):
    plans={n:[] for n in numbers};used=set()
    meta={qid:{'h':a.hierarchy(q),'es':r.complete_es(q),'role':p.role_signature(q),
               'topic':p.primary_topic(q),'concept':p.strip_parentheses(a.answer(q))} for qid,q in pool.items()}
    domains={n:C.Counter() for n in numbers};roles={n:C.Counter() for n in numbers}
    topics={n:C.Counter() for n in numbers};concepts={n:C.Counter() for n in numbers};stems={n:set() for n in numbers}
    def rank(n,q):
        m=meta[q['questionId']]
        return (not m['es'],q['questionId'] not in members.get(n,set()),roles[n][m['role']] if m['h'] else topics[n][m['topic']],
                concepts[n][m['concept']],q['questionId'])
    def add(n,q):
        qid=q['questionId'];m=meta[qid];plans[n].append(copy.deepcopy(q));used.add(qid)
        stems[n].add(a.norm(q.get('prompt')));domains[n][q['domainId']]+=1;roles[n][m['role']]+=m['h'];topics[n][m['topic']]+=1;concepts[n][m['concept']]+=1
    for target in range(3):
        for n in numbers:
            choices=[q for qid,q in pool.items() if qid not in used and meta[qid]['h'] and (n!=1 or meta[qid]['es']) and a.norm(q.get('prompt')) not in stems[n] and domains[n][q['domainId']]<a.DOMAINS[q['domainId']][1]]
            if choices:add(n,min(choices,key=lambda q:rank(n,q)))
    for d,(_,quota) in a.DOMAINS.items():
        needed=sum(quota-domains[n][d] for n in numbers)
        ordinary=sum(qid not in used and q['domainId']==d and not meta[qid]['h'] for qid,q in pool.items())
        extra=max(0,needed-ordinary)
        # Three is the minimum/reference, not a reason to discard approved
        # hierarchy items when they are the only way to complete a domain.
        while extra:
            added=False
            for n in numbers:
                if not extra:break
                if domains[n][d]>=quota:continue
                choices=[q for qid,q in pool.items() if qid not in used and q['domainId']==d and meta[qid]['h'] and (n!=1 or meta[qid]['es']) and a.norm(q.get('prompt')) not in stems[n]]
                if choices:add(n,min(choices,key=lambda q:rank(n,q)));extra-=1;added=True
            if not added:break
        for n in numbers:
            while domains[n][d]<quota:
                choices=[q for qid,q in pool.items() if qid not in used and q['domainId']==d and not meta[qid]['h'] and (n!=1 or meta[qid]['es']) and a.norm(q.get('prompt')) not in stems[n]]
                if not choices:break
                add(n,min(choices,key=lambda q:rank(n,q)))
    return plans,used

def protect_existing_spanish(plans,store):
    budgets={n:(0 if n==1 else sum(not r.complete_es(q) for q in store.get('publishedExams',{}).get('simulation_exam_'+str(n),{}).get('questions',[]))) for n in range(1,8)}
    budgets[8]=100
    def missing(n):return sum(not r.complete_es(q) for q in plans[n])
    for target in range(1,8):
        while missing(target)>budgets[target]:
            found=None
            for donor in range(1,9):
                if donor==target or missing(donor)>=budgets[donor]:continue
                for i,q in enumerate(plans[target]):
                    if r.complete_es(q):continue
                    for j,v in enumerate(plans[donor]):
                        if not r.complete_es(v) or v['domainId']!=q['domainId'] or a.hierarchy(v)!=a.hierarchy(q):continue
                        sa=a.norm(q.get('prompt'));sb=a.norm(v.get('prompt'))
                        if any(k!=i and a.norm(x.get('prompt'))==sb for k,x in enumerate(plans[target])):continue
                        if any(k!=j and a.norm(x.get('prompt'))==sa for k,x in enumerate(plans[donor])):continue
                        found=(donor,i,j);break
                    if found:break
                if found:break
            if not found:break
            donor,i,j=found;plans[target][i],plans[donor][j]=plans[donor][j],plans[target][i]
    return {n:missing(n)-budgets[n] for n in range(1,8) if missing(n)>budgets[n]}

def metrics(plans):
    return {n:{'count':len(qs),'hierarchy':sum(a.hierarchy(q) for q in qs),'spanish_incomplete':sum(not r.complete_es(q) for q in qs),
        'domains':dict(C.Counter(q['domainId'] for q in qs)),
        'missing_domains':{d:max(0,w-sum(q['domainId']==d for q in qs)) for d,(_,w) in a.DOMAINS.items()}}
        for n,qs in plans.items()}

def patch_admin(source):
    marker='// Published exam counters and snapshot viewer v1'
    if marker in source:return source
    # These counters run only in the display layer; no revision is promoted.
    anchor='        <div class="question-bank__exam-grid">'
    if source.count(anchor)!=1:raise RuntimeError('No se identificó la cuadrícula del constructor.')
    block='''        <?php
        // Published exam counters and snapshot viewer v1
        require_once(__DIR__ . '/published-exam-admin-view.php');
        $publishedexam1display = block_myprogress_published_exam_count($revisionstore, 1);
        $publishedexam2display = block_myprogress_published_exam_count($revisionstore, 2);
        ?>
'''
    source=source.replace(anchor,block+anchor,1)
    start=source.index('                <h4>Exam 1</h4>');end=source.index('            <?php\n            $extraguidelineprogresspercent',start)
    cards=source[start:end]
    cards=cards.replace("(int)$totals['exam1']","(int)($publishedexam1display ?? $totals['exam1'])")
    cards=cards.replace('count($exam2positions)','($publishedexam2display ?? count($exam2positions))')
    cards=cards.replace('$exam1listurl->out(false)',"($publishedexam1display !== null ? new moodle_url('/blocks/myprogress/published-exam-admin.php', ['exam'=>1, 'lang'=>$displaylang]) : $exam1listurl)->out(false)")
    cards=cards.replace('$exam2approvedurl->out(false)',"(new moodle_url('/blocks/myprogress/published-exam-admin.php', ['exam'=>2, 'lang'=>$displaylang]))->out(false)")
    source=source[:start]+cards+source[end:]
    line="                $projectpercent = $projecttarget > 0 ? min(100, (int)round($projectcount * 100 / $projecttarget)) : 0;"
    if source.count(line)!=1:raise RuntimeError('No se identificaron los conteos de los proyectos.')
    addition='''                $frozencount = $projectispublished ? block_myprogress_published_exam_count($revisionstore, $projectexamnumber) : null;
                $projectsourceview = !$projectispublished && is_array($project['approvedSourceSnapshots'] ?? null);
                if ($projectsourceview) { $frozencount = block_myprogress_snapshot_usable_count($project['approvedSourceSnapshots']); }
                if ($frozencount !== null) {
                    $projectcount = $frozencount;
                    $projectmissing = max(0, $projecttarget - $projectcount);
                    $projectneedsrebalance = false;
                    $projectisreadytopublish = false;
                }
'''
    source=source.replace(line,addition+line,1)
    anchor="$projectincludedurl->set_anchor('pending-questions');"
    if source.count(anchor)!=1:raise RuntimeError('No se identificó el enlace de preguntas publicadas.')
    source=source.replace(anchor,anchor+'''
                if ($projectsourceview) {
                    $projectincludedurl = new moodle_url('/blocks/myprogress/published-exam-admin.php', ['project'=>$projectid,'lang'=>$displaylang]);
                } else if ($projectispublished && $frozencount !== null) {
                    $projectincludedurl = new moodle_url('/blocks/myprogress/published-exam-admin.php', ['exam'=>$projectexamnumber,'lang'=>$displaylang]);
                }
''',1)
    oldlabel="<?php echo $displaylang === 'es' ? 'Gestionar preguntas' : 'Manage questions'; ?> (<?php echo $projectcount; ?>)"
    newlabel="<?php echo $projectispublished ? ($displaylang === 'es' ? 'Ver versión publicada' : 'View published version') : ($projectsourceview ? ($displaylang === 'es' ? 'Ver borrador' : 'View draft') : ($displaylang === 'es' ? 'Gestionar preguntas' : 'Manage questions')); ?> (<?php echo $projectcount; ?>)"
    if source.count(oldlabel)!=1:raise RuntimeError('No se identificó el botón del constructor.')
    source=source.replace(oldlabel,newlabel,1)
    return source

def transact(updates,backup,verify=None,artifacts=None):
    originals={p:p.read_bytes() if p.exists() else None for p in updates}
    backup.mkdir(parents=True,exist_ok=False)
    for i,(path,data) in enumerate(originals.items()):
        if data is not None:(backup/(str(i)+'-'+path.name)).write_bytes(data)
    (backup/'restore.json').write_text(json.dumps([{'path':str(p),'backup':str(backup/(str(i)+'-'+p.name)) if data is not None else None} for i,(p,data) in enumerate(originals.items())],indent=2))
    if (artifacts or {}).get('VERSION_ORIGINAL.json') is not None:
        (backup/'VERSION_ORIGINAL.json').write_bytes(artifacts['VERSION_ORIGINAL.json'])
    installed=[]
    try:
        for path,data in updates.items():r.write_atomic(path,data);installed.append(path)
        if verify:verify()
        for name,payload in (artifacts or {}).items():
            if name!='VERSION_ORIGINAL.json':(backup/name).write_bytes(payload)
    except BaseException:
        for path in reversed(installed):
            if originals[path] is None:path.unlink(missing_ok=True)
            else:r.write_atomic(path,originals[path])
        raise


def run(root,data,out,apply_available=False):
    print('Preparando 1–8 sólo con publicaciones del administrador; sin crear ni aprobar preguntas nuevas.',flush=True)
    storepath=data/'board-domain-banks/herbology-question-overrides.json';store=a.read_json(storepath)
    sourcehash=hashlib.sha256(storepath.read_bytes()).hexdigest()
    original_store=copy.deepcopy(store)
    pool,origins,members,diagnostics=collect(root,data,store)
    plans,used=allocate(pool,members,list(range(1,9)))
    language_regressions=protect_existing_spanish(plans,store)
    # Redistribution changes only ownership and presentation order, never the
    # approved question wording, options, key, explanation or translations.
    asplans=[{'name':'Exam '+str(n),'items':plans[n]} for n in range(1,9)]
    swaps=r.redistribute(asplans)
    plans={n:asplans[n-1]['items'] for n in range(1,9)};stats=metrics(plans)
    content_complete=not language_regressions and all(s['count']==100 and s['hierarchy']>=3 and not any(s['missing_domains'].values()) for s in stats.values())
    structurally_ready={n:s['count']==100 and not any(s['missing_domains'].values()) for n,s in stats.items()}
    can_apply=content_complete or (apply_available and not language_regressions and all(structurally_ready[n] for n in range(1,8)))
    publish_numbers=[n for n in range(1,9) if structurally_ready[n]] if can_apply else []
    all_eight_published=can_apply and len(publish_numbers)==8
    start_nine=all_eight_published and content_complete
    if can_apply and 8 not in publish_numbers and original_store.get('publishedExams',{}).get('simulation_exam_8',{}).get('status')=='published':
        raise RuntimeError('Exam 8 ya estaba publicado; no se cambia a un borrador incompleto.')
    remaining={qid:q for qid,q in pool.items() if qid not in used}
    nine={9:[]}
    if start_nine:nine,_=allocate(remaining,{},[9])
    # The snapshot adapter keeps Exam 1 consistent with all other publications.
    frontend_originals,frontupdates=r.stage_exam_one_frontend(root)
    updates=dict(frontupdates)
    basedir=Path(__file__).parent
    for name in ['published-exam-admin-view.php','published-exam-admin.php','herbology-exam-source-note.php']:
        payload=(basedir/name).read_bytes();r.lint_php(payload);updates[root/'blocks/myprogress'/name]=payload
    admin=root/'blocks/myprogress/admin-herbology-question-bank.php';adminold=admin.read_bytes()
    adminnew=patch_admin(adminold.decode()).encode();r.lint_php(adminnew);updates[admin]=adminnew
    now=DT.datetime.now(DT.timezone.utc).isoformat();provenance={qid:origins[qid] for qid in used}
    changed=[]
    if can_apply:
        allids=set();signatures=set()
        for n in publish_numbers:
            qs=plans[n];qs.sort(key=lambda q:(list(a.DOMAINS).index(q['domainId']),q['questionId']))
            clean=[]
            for pos,q in enumerate(qs,1):
                q={k:v for k,v in q.items() if not k.startswith('_')};qid=q['questionId'];sig=r.duplicate_signature(q)
                if qid not in origins or qid in allids or sig in signatures:raise RuntimeError('Publicación no autorizada o pregunta repetida: '+qid)
                allids.add(qid);signatures.add(sig);q['examTemplate']={'examNumber':n,'globalPosition':pos};clean.append(q)
            key='simulation_exam_'+str(n);old=store.setdefault('publishedExams',{}).get(key,{})
            projectid=old.get('projectId')
            if n==8 and not projectid:
                projectid=next((key for key,record in store.get('examProjects',{}).items() if exam_number(key,record)==8),'approved-balance-exam-8')
            version=int(old.get('version') or 0)+1
            store['publishedExams'][key]=dict(old,id=key,name='Exam '+str(n),examNumber=n,status='published',version=version,
                targetCount=100,questions=clean,domainCounts=stats[n]['domains'],publishedAt=now,
                publishedByName='Redistribution of administrator-published sources',projectId=projectid,
                diversityPolicy={'hierarchyTargetPer100':3,'hierarchyActual':stats[n]['hierarchy'],'hierarchyPending':max(0,3-stats[n]['hierarchy']),
                    'sources':'administrator publications only','noNewQuestions':True})
            if projectid:
                project=store.setdefault('examProjects',{}).setdefault(projectid,{})
                project.update(id=projectid,name='Exam '+str(n),status='published',targetCount=100,questionIds=[q['questionId'] for q in clean],
                    confirmedQuestionIds=[q['questionId'] for q in clean],currentCount=100,missingCount=0,publishedExamNumber=n,publishedExamKey=key,
                    publishedVersion=version,publishedAt=now,updatedAt=now)
                # Old exclusions must not silently filter out a now-included member.
                project['excludedQuestionIds']=[qid for qid in project.get('excludedQuestionIds',[]) if qid not in allids]
            changed.append(n)
            if n==1:
                for path in [root/'blocks/menu/herbology-domain-candidates.json',root/'blocks/menu/herbology-domain-bank.json',data/'board-domain-banks/herbology-domain-bank.json']:
                    if path.is_file():updates[path]=(json.dumps(r.set_exam_one(a.read_json(path),clean),ensure_ascii=False,indent=2)+'\n').encode()
        if start_nine:
            projectid=next((key for key,record in store.get('examProjects',{}).items() if exam_number(key,record)==9),'approved-balance-exam-9')
            old9=store.get('examProjects',{}).get(projectid,{})
            if old9.get('status')=='published':raise RuntimeError('Exam 9 ya está publicado; no se sobrescribe con un borrador.')
            qs9=[{k:v for k,v in q.items() if not k.startswith('_')} for q in nine[9]]
            store.setdefault('examProjects',{})[projectid]=dict(old9,id=projectid,name='Exam 9',status='draft',targetCount=100,
                questionIds=[q['questionId'] for q in qs9],confirmedQuestionIds=[q['questionId'] for q in qs9],currentCount=len(qs9),missingCount=100-len(qs9),
                approvedSourceSnapshots=qs9,sourcePublicationEvidence={q['questionId']:origins[q['questionId']] for q in qs9},createdAt=old9.get('createdAt',now),updatedAt=now)
            store['approvedBalanceReserveIds']=[qid for qid in remaining if qid not in {q['questionId'] for q in qs9}]
        if 8 not in publish_numbers:
            project8id=next((key for key,record in store.get('examProjects',{}).items() if exam_number(key,record)==8),'approved-balance-exam-8')
            prior8=store.get('examProjects',{}).get(project8id,{})
            qs8=[{k:v for k,v in q.items() if not k.startswith('_')} for q in plans[8]]
            store.setdefault('examProjects',{})[project8id]=dict(prior8,id=project8id,name='Exam 8',status='draft',targetCount=100,
                questionIds=[q['questionId'] for q in qs8],confirmedQuestionIds=[q['questionId'] for q in qs8],
                currentCount=len(qs8),missingCount=100-len(qs8),approvedSourceSnapshots=qs8,
                sourcePublicationEvidence={q['questionId']:origins[q['questionId']] for q in qs8},updatedAt=now)
            store['approvedBalanceReserveIds']=list(remaining)
        store['updatedAt']=now;updates[storepath]=(json.dumps(store,ensure_ascii=False,indent=2)+'\n').encode()
    report={'created_at':now,'status':('applied_all_8' if all_eight_published else 'applied_1_7_exam8_draft') if can_apply else 'blocked_existing_exams_preserved',
        'hierarchy_target_per_exam':3,'published_exams_updated':publish_numbers,
        'eligible_published_questions':len(pool),'pool_domains':dict(C.Counter(q['domainId'] for q in pool.values())),
        'available_hierarchy':sum(a.hierarchy(q) for q in pool.values()),'exams':stats,'exam9':metrics(nine)[9],
        'sources':provenance,'spanish_regressions_blocking_publication':language_regressions,'diagnostics':diagnostics,'topic_swaps':swaps,'new_questions_created':0}
    if hashlib.sha256(storepath.read_bytes()).hexdigest()!=sourcehash:raise RuntimeError('Cambió el banco durante la preparación; vuelva a ejecutar.')
    if admin.read_bytes()!=adminold:raise RuntimeError('Cambió el panel durante la preparación; no se instala una copia anterior.')
    title=(('Exámenes 1–8 publicados; Exam 9 en borrador' if start_nine else 'Exámenes 1–8 disponibles; meta de jerarquía pendiente') if all_eight_published else 'Reparto aplicado a 1–7; Exam 8 en borrador') if can_apply else 'Plan pendiente; las versiones publicadas se conservaron'
    rows=[[f'Exam {n}',s['count'],s['hierarchy'],s['spanish_incomplete'],' / '.join(str(s['domains'].get(d,0)) for d in a.DOMAINS)] for n,s in stats.items()]
    document='<meta charset="utf-8"><title>'+html.escape(title)+'</title><main style="font:16px/1.5 system-ui;max-width:1100px;margin:25px auto"><h1>'+html.escape(title)+'</h1>'
    document+='<p>Solo publicaciones del administrador; cero preguntas nuevas o aprobaciones automáticas.</p>'
    document+='<h2>'+('Resultado aplicado; exámenes incompletos en borrador' if can_apply else 'Propuesta NO publicada')+'</h2>'+a.table(['Examen','Preguntas / 100','Jerarquía / 3','Español incompleto','Dominios 28/24/18/15/10/5'],rows)
    if not all_eight_published:
        actual=[]
        for n in range(1,9):
            published=store.get('publishedExams',{}).get('simulation_exam_'+str(n))
            actual.append([f'Exam {n}',len(published.get('questions',[])) if published else 'No publicado',published.get('version','') if published else ''])
        document+='<h2>Estado publicado que permanece disponible</h2>'+a.table(['Examen','Preguntas','Versión'],actual)
    document+='<h2>Fuentes utilizables</h2>'+a.table(['Dominio','Preguntas publicadas disponibles'],[[d,num] for d,num in report['pool_domains'].items()])
    document+='<p>Jerarquía disponible: '+str(report['available_hierarchy'])+'; objetivo para los ocho exámenes: 24.</p></main>'
    lines=['Solo preguntas publicadas por el administrador; no se creó ni aprobó ninguna pregunta nueva.']
    if not can_apply:lines.append('NO SE PUBLICÓ EL NUEVO LOTE. Los exámenes actuales se conservaron; se guardó el plan y se corrigió la lectura del constructor.')
    for n,s in stats.items():
        lines.append(f"{'PUBLICADO' if n in publish_numbers else ('BORRADOR' if can_apply and n==8 else 'PROPUESTA')} Exam {n}: {s['count']}/100; jerarquía {s['hierarchy']}/3; español incompleto {s['spanish_incomplete']}.")
        if s['hierarchy']<3:lines.append(f"  FALTAN {3-s['hierarchy']} preguntas de jerarquía ya publicadas.")
        for d,num in s['missing_domains'].items():
            if num:lines.append(f'  FALTAN {num} preguntas publicadas de {d}.')
    if start_nine:lines.append(f"Exam 9: borrador con {len(nine[9])}/100 preguntas sobrantes, sin IDs compartidos con 1–8.")
    else:lines.append('Exam 9 no se inició: primero deben completarse los ocho conjuntos.')
    for n,num in language_regressions.items():lines.append(f'FALTAN {num} traducciones ya publicadas para no empeorar el español de Exam {n}.')
    lines.append('Jerarquía pendiente para llegar a tres por examen: '+str(sum(max(0,3-s['hierarchy']) for s in stats.values())))
    lines.extend(['INFORME: '+str(out/'informe.html'),'DETALLE: '+str(out/'resultado.json'),'PLAN REVISABLE: '+str(out/'versiones-propuestas.json'),'VERSIÓN ORIGINAL: '+str(out/'VERSION_ORIGINAL.json'),'RESPALDO: '+str(out)])
    def verify():
        if not can_apply:return
        saved=a.read_json(storepath);seen=set()
        for n in publish_numbers:
            qs=saved['publishedExams']['simulation_exam_'+str(n)]['questions']
            if len(qs)!=100 or (not apply_available and sum(a.hierarchy(q) for q in qs)<3):raise RuntimeError('Falló la comprobación de la publicación de Exam '+str(n))
            for q in qs:
                if q['questionId'] not in origins or q['questionId'] in seen:raise RuntimeError('ID ajeno o repetido después de guardar.')
                seen.add(q['questionId'])
        draftid=projectid if start_nine else (project8id if 8 not in publish_numbers else None)
        if draftid:
            for q in saved['examProjects'][draftid]['approvedSourceSnapshots']:
                if q['questionId'] in seen:raise RuntimeError('El borrador comparte una pregunta con los exámenes publicados.')
    artifacts={'VERSION_ORIGINAL.json':json.dumps({'saved_at':now,'original_revision_store':original_store},ensure_ascii=False,indent=2).encode(),'resultado.json':json.dumps(report,ensure_ascii=False,indent=2).encode(),
        'versiones-propuestas.json':json.dumps({str(n):qs for n,qs in plans.items()},ensure_ascii=False,indent=2).encode(),
        'informe.html':document.encode(),'RESUMEN.txt':('\n'.join(lines)+'\n').encode()}
    for path,expected in frontend_originals.items():
        if (path.read_bytes() if path.exists() else None)!=expected:raise RuntimeError('Cambió el código de Exam 1 durante la preparación; no se instaló nada.')
    transact(updates,out,verify,artifacts)
    print('\n'.join(lines))

if __name__=='__main__':
    parser=argparse.ArgumentParser();parser.add_argument('--moodle-root',type=Path,default=Path('/var/www/html/moodle'))
    parser.add_argument('--data-root',type=Path,default=Path('/mnt/volume_nyc1_01/moodledata'))
    parser.add_argument('--backup-root',type=Path,default=Path('/var/backups/acupuncturetests'));parser.add_argument('--apply-available',action='store_true');args=parser.parse_args()
    with (args.data_root/'board-domain-banks/herbology-question-overrides.json.lock').open('a+') as lock:
        fcntl.flock(lock,fcntl.LOCK_EX)
        run(args.moodle_root,args.data_root,args.backup_root/('approved-exams-1-8-'+DT.datetime.now(DT.timezone.utc).strftime('%Y%m%d-%H%M%S-%f')),args.apply_available)
