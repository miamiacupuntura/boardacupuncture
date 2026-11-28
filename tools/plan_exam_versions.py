#!/usr/bin/env python3
"""Prepare reviewable, unpublished exam versions without changing Moodle."""
import argparse
import collections as C
import copy
import csv
import datetime as DT
import hashlib
import html
import json
from pathlib import Path
import re
import audit_exam_diversity as audit

def load_catalog(root, data):
    catalog = {}
    paths = [root/'blocks/menu/herbology-domain-candidates.json', data/'board-domain-banks/herbology-scorm-question-bank.json']
    for path in paths:
        doc = audit.read_json(path, optional=True)
        for domain, questions in doc.get('domains', {}).items():
            for q in questions:
                if isinstance(q, dict) and q.get('questionId'):
                    catalog[q['questionId']] = dict(q, domainId=q.get('domainId') or domain)
    store = audit.read_json(data/'board-domain-banks/herbology-question-overrides.json')
    records = dict(store.get('questions', {}))
    records.update(store.get('customQuestions', {}))
    for qid, record in records.items():
        if not isinstance(record, dict): continue
        if record.get('status') in ['deleted','study_archive','extra_guideline','exclusion_candidate']:
            catalog[qid]={'questionId':qid,'_planExcluded':True};continue
        snapshot = record.get('draft') or record.get('approved')
        if isinstance(snapshot, dict):
            q = dict(catalog.get(qid, {}));q.update({k:v for k,v in snapshot.items() if k in audit.FIELDSET})
            q['questionId'] = qid;q['domainId'] = record.get('domainId') or snapshot.get('domainId') or q.get('domainId')
            q['_planAlreadyReviewed'] = record.get('status') == 'approved'
            q['_planPendingRevision'] = record.get('status') == 'draft'
            catalog[qid] = q
    return catalog

def role_signature(q):
    text = audit.norm(q.get('prompt'))
    mapping={'envoy':'envoy','mensajero':'envoy','deputy':'deputy','minister':'deputy','ministro':'deputy',
             'assistant':'assistant','asistente':'assistant','chief':'chief','emperor':'chief','emperador':'chief'}
    roles=list(re.finditer(r'\b(?:envoy|mensajero|deputy|minister|ministro|assistant|asistente|chief|emperor|emperador)\b',text[-250:]))
    if roles:return mapping[roles[-1].group()]
    pinyin=list(re.finditer(r'\b(?:role|papel|rol|matches|corresponde|como)\s+(?:the\s+)?(jun|chen|zuo|shi)\b',text[-250:]))
    if pinyin:return {'jun':'chief','chen':'deputy','zuo':'assistant','shi':'envoy'}[pinyin[-1].group(1)]
    return 'hierarchy-unspecified'

def primary_topic(q):
    if audit.hierarchy(q): return 'Jerarquía'
    text = audit.norm(q.get('prompt'))
    for topic in ['Procesamiento/preparación','Interacciones','Embarazo/lactancia','Contraindicaciones/toxicidad','Dosis/administración','Comparación/diferencial','Composición/modificaciones']:
        if re.search(audit.TOPICS[topic],text): return topic
    return 'Caso clínico' if audit.CLINICAL.search(text) else 'Identificación/propiedades'

def strip_parentheses(text):
    depth=0;out=[]
    for char in audit.plain(text):
        if char=='(': depth+=1
        elif char==')': depth=max(0,depth-1)
        elif depth==0: out.append(char)
    return audit.norm(''.join(out))

def make_plan(raw, normalized, stats, catalog):
    normalized_by_id={q.get('questionId'):q for e in normalized for q in e['questions']}
    raw_by_id={q.get('questionId'):q for e in raw for q in e['questions']}
    reviewed_ids={q.get('questionId') for e in raw for q in e['questions']
                  if e['state']!='borrador' or q.get('_auditRevisionStatus')=='approved'}
    # Frozen published questions can be redistributed into new versions. All
    # replacements still require human review; no old snapshot is overwritten.
    for qid,q in raw_by_id.items():
        if qid and qid not in catalog: catalog[qid]=dict(q,_planAlreadyReviewed=True)
    stat_by_key={e['key']:e for e in stats}
    metadata={}
    def meta(q):
        qid=q.get('questionId','')
        cachekey=id(q)
        if cachekey in metadata:return metadata[cachekey][1]
        original=raw_by_id.get(qid)
        same_snapshot=bool(original and original.get('prompt')==q.get('prompt') and original.get('options')==q.get('options') and original.get('contentType')==q.get('contentType'))
        display=normalized_by_id.get(qid,q) if same_snapshot else q
        opts=audit.options(q);key=str(q.get('correctLabel','')).upper()
        span=audit.spanish(q)
        excluded=bool(q.get('_planExcluded') or catalog.get(qid,{}).get('_planExcluded'))
        eligible=(not excluded and q.get('domainId') in audit.DOMAINS and audit.usable(q.get('prompt'))
                  and len(opts)==4 and key in [o['label'] for o in opts]
                  and len({audit.norm(o['text']) for o in opts})==4
                  and not any(audit.BADCHOICES.search(audit.norm(o['text'])) for o in opts)
                  and audit.usable(q.get('explanation')))
        info={'domain':q.get('domainId'),'hierarchy':audit.hierarchy(display),'role':role_signature(display),
              'topic':primary_topic(display),'concept':strip_parentheses(audit.answer(display)),
              'stem':audit.norm(display.get('prompt')),'key':key,'eligible':eligible,
              'spanish_complete':audit.usable(span.get('prompt')) and len(audit.options(span))==4 and audit.usable(span.get('explanation')),
              'excluded':excluded,'reviewed':bool(q.get('_planAlreadyReviewed')) or (qid in reviewed_ids and not q.get('_planPendingRevision'))}
        metadata[cachekey]=(q,info);return info
    plans=[];owner={};removed=C.defaultdict(list);stem_counts=C.Counter()
    for exam in raw:
        if int(exam.get('target') or 100)!=100:continue
        p={'key':exam['key'],'name':exam['name'],'previous_version':exam.get('version'),'proposed_version':int(exam.get('version') or 0)+1,
           'target':100,'hierarchy_target':5,'before':stat_by_key[exam['key']], 'items':[], 'changes':[], 'blocks':[],
           'source_state':exam['state']}
        seen_stems=set()
        for position, original in enumerate(exam['questions'],1):
            q=copy.deepcopy(original);qid=q.get('questionId','');info=meta(q)
            reason=None
            if not qid or q.get('_auditMissing') or info['domain'] not in audit.DOMAINS:reason='ID o dominio sin resolver en el conjunto registrado'
            elif info['excluded']:reason='Pregunta excluida, archivada o eliminada en el banco actual'
            elif qid in owner:reason='ID ya conservado en otra versión propuesta'
            elif info['stem'] and info['stem'] in seen_stems:reason='Enunciado idéntico a otro ya conservado en el mismo examen'
            if reason:
                p['changes'].append({'action':'sale','id':qid,'domain':info['domain'],'reason':reason,'position':position})
                if qid:removed[qid].append(exam['key'])
            else:
                p['items'].append(q);owner[qid]=p['key'];seen_stems.add(info['stem']);stem_counts[info['stem']]+=1
        plans.append(p)
    if not plans:raise RuntimeError('No se encontraron exámenes con objetivo de 100 preguntas.')
    def remove(p,q,reason):
        p['items'].remove(q);qid=q['questionId'];owner.pop(qid,None);removed[qid].append(p['key']);stem_counts[meta(q)['stem']]-=1
        p['changes'].append({'action':'sale','id':qid,'domain':meta(q)['domain'],'reason':reason,'topic':meta(q)['topic'],'answer_concept':meta(q)['concept']})
    def add(p,q,reason):
        q=copy.deepcopy(q);qid=q['questionId'];p['items'].append(q);owner[qid]=p['key'];stem_counts[meta(q)['stem']]+=1
        p['changes'].append({'action':'entra','id':qid,'domain':meta(q)['domain'],'reason':reason,
                             'previously_reviewed':meta(q)['reviewed'],'review_required':True,
                             'origin_exam_keys':removed.get(qid,[]),'topic':meta(q)['topic'],'answer_concept':meta(q)['concept'],'prompt':q.get('prompt','')})
    def removal_rank(p,q):
        info=meta(q);concepts=C.Counter(meta(x)['concept'] for x in p['items']);topics=C.Counter(meta(x)['topic'] for x in p['items'])
        return (concepts[info['concept']],topics[info['topic']],not info['spanish_complete'],not info['reviewed'],q['questionId'])
    # Keep five hierarchy questions with varied roles and answers, then remove
    # domain surpluses. No question is deleted from the bank.
    for p in plans:
        hs=[q for q in p['items'] if meta(q)['hierarchy']]
        if len(hs)>5:
            kept=[];roles=C.Counter();concepts=C.Counter()
            while hs and len(kept)<5:
                q=min(hs,key=lambda x:(roles[meta(x)['role']],concepts[meta(x)['concept']],not meta(x)['spanish_complete'],not meta(x)['reviewed'],x['questionId']))
                hs.remove(q);kept.append(q);roles[meta(q)['role']]+=1;concepts[meta(q)['concept']]+=1
            for q in hs:remove(p,q,'Exceso sobre cinco preguntas de jerarquía; se conserva variedad de roles')
        for domain,(_,target) in audit.DOMAINS.items():
            while sum(meta(q)['domain']==domain for q in p['items'])>target:
                candidates=[q for q in p['items'] if meta(q)['domain']==domain and not meta(q)['hierarchy']]
                if not candidates:candidates=[q for q in p['items'] if meta(q)['domain']==domain]
                q=max(candidates,key=lambda x:removal_rank(p,x));remove(p,q,'Exceso del dominio respecto a la distribución 28/24/18/15/10/5')
    # Pool includes retired cohort questions and unused bank material.
    for qid in removed:
        if qid in raw_by_id and not catalog.get(qid,{}).get('_planExcluded'):catalog[qid]=raw_by_id[qid]
    pool=[q for qid,q in catalog.items() if qid and meta(q)['eligible']]
    def rank(p,q):
        info=meta(q);concepts=C.Counter(meta(x)['concept'] for x in p['items']);topics=C.Counter(meta(x)['topic'] for x in p['items']);keys=C.Counter(meta(x)['key'] for x in p['items']);roles=C.Counter(meta(x)['role'] for x in p['items'] if meta(x)['hierarchy'])
        return (not info['reviewed'],not info['spanish_complete'],concepts[info['concept']],
                roles[info['role']] if info['hierarchy'] else topics[info['topic']],
                keys[info['key']],0 if q['questionId'] in removed else 1,
                hashlib.sha256((p['key']+'|'+q['questionId']).encode()).hexdigest())
    def available(q,p):
        if q['questionId'] in owner:return False
        stem=meta(q)['stem']
        return stem_counts[stem]==0
    # Fill hierarchy deficits first, freeing same-domain non-hierarchy slots.
    for p in plans:
        while sum(meta(q)['hierarchy'] for q in p['items'])<5:
            choices=[]
            for q in pool:
                info=meta(q)
                if not info['hierarchy'] or not available(q,p):continue
                domain=info['domain'];current=sum(meta(x)['domain']==domain for x in p['items']);target=audit.DOMAINS[domain][1]
                if current<target or any(meta(x)['domain']==domain and not meta(x)['hierarchy'] for x in p['items']):choices.append(q)
            if not choices:
                p['blocks'].append('No hay suficientes preguntas de jerarquía distintas y estructuralmente completas para llegar a cinco sin inventar contenido.');break
            q=min(choices,key=lambda x:(rank(p,x)[:-1],0 if meta(x)['domain']=='formula_composition' else 1,rank(p,x)[-1]))
            domain=meta(q)['domain'];target=audit.DOMAINS[domain][1]
            if sum(meta(x)['domain']==domain for x in p['items'])>=target:
                out=max([x for x in p['items'] if meta(x)['domain']==domain and not meta(x)['hierarchy']],key=lambda x:removal_rank(p,x))
                remove(p,out,'Sustitución por jerarquía dentro del mismo dominio, conservando su cuota')
                if out['questionId'] not in {x['questionId'] for x in pool}:pool.append(out)
            add(p,q,'Completar cinco preguntas de jerarquía con diversidad de roles y respuestas')
    # Complete all remaining slots with other topics in their own domains.
    for p in plans:
        for domain,(_,target) in audit.DOMAINS.items():
            while sum(meta(q)['domain']==domain for q in p['items'])<target:
                choices=[q for q in pool if meta(q)['domain']==domain and not meta(q)['hierarchy'] and available(q,p)]
                if not choices:
                    missing=target-sum(meta(q)['domain']==domain for q in p['items'])
                    p['blocks'].append(f'Faltan {missing} preguntas no jerárquicas completas y distintas para {domain}.');break
                q=min(choices,key=lambda x:rank(p,x));add(p,q,'Completar el dominio con otro tema, favoreciendo menor repetición de respuestas y formatos')
    # Full snapshots are staging files, not replacements of publishedExams.
    for p in plans:
        old_ids={q.get('questionId') for q in raw_by_exam(raw,p['key'])}
        p['items'].sort(key=lambda q:(list(audit.DOMAINS).index(meta(q)['domain']),0 if q.get('questionId') in old_ids else 1,(q.get('examTemplate') or {}).get('globalPosition',9999),q['questionId']))
        exam_number=re.search(r'\b(?:exam|esam)\s*(\d+)\b',p['name'],re.I)
        for position,q in enumerate(p['items'],1):
            q['examTemplate']=dict(q.get('examTemplate') or {},globalPosition=position)
            if exam_number:q['examTemplate']['examNumber']=int(exam_number[1])
        p['after']={'count':len(p['items']),'domains':dict(C.Counter(meta(q)['domain'] for q in p['items'])),'hierarchy':sum(meta(q)['hierarchy'] for q in p['items']),
                    'topics':dict(C.Counter(meta(q)['topic'] for q in p['items'])),'spanish_incomplete':sum(not meta(q)['spanish_complete'] for q in p['items'])}
        p['retained_ids']=[q['questionId'] for q in p['items'] if q['questionId'] in old_ids]
        reasons=C.defaultdict(list)
        for change in p['changes']:
            if change['action']=='entra':reasons[change['id']].append('Incorporación o traslado: revisar contenido y validación del roster')
        for q in p['items']:
            if not meta(q)['spanish_complete']:reasons[q['questionId']].append('Completar o verificar la versión española')
            if q.get('_auditRevisionStatus') in ['draft','pending_reclassification'] or q.get('_planPendingRevision'):
                reasons[q['questionId']].append('Borrador existente pendiente de aprobación')
        retained_set=set(p['retained_ids'])
        for signal in p['before'].get('review_signals',[]):
            if signal['id'] in retained_set:reasons[signal['id']].extend(signal['signals'])
        p['review_reasons']={qid:list(dict.fromkeys(items)) for qid,items in reasons.items()}
        p['review_required_ids']=list(reasons)
        outgoing=C.defaultdict(list);incoming=C.defaultdict(list)
        for change in p['changes']:
            (outgoing if change['action']=='sale' else incoming)[change.get('domain')].append(change)
        p['exchanges']=[]
        for domain in audit.DOMAINS:
            for i,change in enumerate(incoming[domain]):
                old=outgoing[domain][i] if i<len(outgoing[domain]) else None
                p['exchanges'].append({'domain':domain,'out_id':old['id'] if old else None,'in_id':change['id'],
                                       'new_topic':change.get('topic'),'new_answer_concept':change.get('answer_concept'),
                                       'reason':change['reason']})
        expected={d:w for d,(_,w) in audit.DOMAINS.items()}
        p['structure_complete']=p['after']['count']==100 and p['after']['hierarchy']==5 and all(p['after']['domains'].get(d,0)==v for d,v in expected.items())
        p['status']='draft_requires_content_review' if p['structure_complete'] else 'draft_blocked_by_shortage'
    return plans

def raw_by_exam(raw,key):
    return next(e['questions'] for e in raw if e['key']==key)

def main():
    parser=argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--moodle-root',type=Path,default=Path('/var/www/html/moodle'))
    parser.add_argument('--data-root',type=Path,default=Path('/mnt/volume_nyc1_01/moodledata'))
    parser.add_argument('--output-dir',type=Path)
    parser.add_argument('--no-normalize',action='store_true')
    args=parser.parse_args()
    print('Leyendo el estado actual; los archivos del banco y los exámenes no se modificarán.',flush=True)
    raw,sources=audit.load_exams(args.moodle_root,args.data_root)
    normalized,mode=audit.normalize_exams(raw,args.moodle_root,args.data_root,not args.no_normalize)
    herbs=audit.read_json(args.moodle_root/'blocks/myprogress/single-herb-card-index.json')['herbs']
    formulas=audit.read_json(args.moodle_root/'blocks/myprogress/formula-board-roster-2026.json')['formulas']
    stats,shared,near=audit.analyze(normalized,raw,herbs,formulas)
    catalog=load_catalog(args.moodle_root,args.data_root)
    plans=make_plan(raw,normalized,stats,catalog)
    now=DT.datetime.now(DT.timezone.utc)
    out=args.output_dir or Path('/var/backups/acupuncturetests')/('exam-version-plan-'+now.strftime('%Y%m%d-%H%M%S-%f'))
    out.mkdir(parents=True,exist_ok=False)
    versions=out/'versiones-propuestas';versions.mkdir()
    report={'created_at':now.isoformat(),'normalization':mode,'sources':sources,'status':'proposal_only_not_published','plans':plans}
    (out/'plan-detallado.json').write_text(json.dumps(report,ensure_ascii=False,indent=2))
    with (out/'cambios-por-pregunta.csv').open('w',newline='',encoding='utf-8-sig') as handle:
        writer=csv.writer(handle);writer.writerow(['examen','accion','id','dominio','tema','respuesta_o_concepto','motivo','requiere_revision'])
        for p in plans:
            for change in p['changes']:writer.writerow([p['name'],change['action'],change['id'],change.get('domain'),change.get('topic'),change.get('answer_concept'),change['reason'],change.get('review_required',False)])
    with (out/'sustituciones-pareadas.csv').open('w',newline='',encoding='utf-8-sig') as handle:
        writer=csv.writer(handle);writer.writerow(['examen','dominio','sale','entra','tema_nuevo','respuesta_nueva','motivo'])
        for p in plans:
            for pair in p['exchanges']:writer.writerow([p['name'],pair['domain'],pair['out_id'] or 'casilla nueva',pair['in_id'],pair['new_topic'],pair['new_answer_concept'],pair['reason']])
    esc=lambda value:html.escape(str(value))
    htmlparts=['<!doctype html><html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Plan de versiones equilibradas</title><style>body{font:16px/1.5 system-ui;color:#173449;max-width:1200px;margin:25px auto;padding:15px}h1,h2{color:#006d69}table{border-collapse:collapse;width:100%;margin:15px 0}td,th{text-align:left;padding:9px;border-bottom:1px solid #d8e3e7;vertical-align:top}th{background:#edf7f5}details{padding:12px;border:1px solid #d8e3e7;margin:12px 0}summary{cursor:pointer;font-weight:600}.note{background:#fff7e5;padding:15px}</style><h1>Plan de versiones equilibradas</h1><p class="note">Archivos de preparación: no se han instalado en Moodle ni publicado. Los IDs propuestos deben pasar revisión de contenido y las validaciones del banco. Las preguntas nuevas se eligieron por estructura, dominio y diversidad; no se certifica automáticamente su corrección clínica.</p>']
    summary=['PLAN DE VERSIONES EQUILIBRADAS — '+now.isoformat(),mode,'']
    for p in plans:
        safe=re.sub(r'[^A-Za-z0-9_-]','-',p['key'])
        clean_questions=[{k:v for k,v in q.items() if not k.startswith('_')} for q in p['items']]
        (versions/(safe+'-v'+str(p['proposed_version'])+'.json')).write_text(json.dumps({'id':p['key'],'name':p['name'],'version':p['proposed_version'],'status':p['status'],'targetCount':100,'questions':clean_questions,'reviewRequiredQuestionIds':p['review_required_ids'],'reviewReasons':p['review_reasons'],'blocks':p['blocks']},ensure_ascii=False,indent=2))
        outgoing=[c for c in p['changes'] if c['action']=='sale'];incoming=[c for c in p['changes'] if c['action']=='entra']
        summary.append(f"{p['name']}: {p['before']['registered_questions']} → {p['after']['count']} preguntas; jerarquía {p['before']['hierarchy_count']} → {p['after']['hierarchy']}; conserva {len(p['retained_ids'])} IDs, salen {len(outgoing)}, entran {len(incoming)}; {'estructura completa' if p['structure_complete'] else 'pendiente por faltantes'}.")
        summary.append('  Dominios propuestos: '+' / '.join(str(p['after']['domains'].get(d,0)) for d in audit.DOMAINS))
        summary.append(f"  Revisión de contenido/traducción indicada en {len(p['review_required_ids'])} IDs; español incompleto: {p['after']['spanish_incomplete']}.")
        for block in p['blocks']:summary.append('  BLOQUEO: '+block)
        htmlparts+=['<h2>'+esc(p['name'])+'</h2>',audit.table(['Indicador','Antes','Propuesto'],[['Preguntas',p['before']['registered_questions'],p['after']['count']],['Jerarquía',p['before']['hierarchy_count'],p['after']['hierarchy']],['Español incompleto',p['before']['missing_spanish'],p['after']['spanish_incomplete']]]),'<p>'+esc(p['status'])+'</p>',audit.table(['Dominio','Preguntas propuestas','Objetivo'],[[name,p['after']['domains'].get(domain,0),weight] for domain,(name,weight) in audit.DOMAINS.items()]),'<details><summary>Preguntas que salen y entran</summary>'+audit.table(['Acción','ID','Dominio','Tema','Respuesta / concepto','Motivo'],[[c['action'],c['id'],c.get('domain'),c.get('topic',''),c.get('answer_concept',''),c['reason']] for c in p['changes']])+'</details>','<details><summary>IDs conservados</summary><p>'+esc(', '.join(p['retained_ids']))+'</p></details>']
        if p['blocks']:htmlparts.append('<p class="note">'+esc('; '.join(p['blocks']))+'</p>')
    htmlparts.append('</html>');(out/'plan-versiones.html').write_text(''.join(htmlparts))
    summary+=['','No se modificaron preguntas, exámenes publicados ni intentos.','No se importó ni publicó ninguna versión.','PLAN: '+str(out/'plan-versiones.html'),'DETALLE: '+str(out/'plan-detallado.json'),'CAMBIOS: '+str(out/'cambios-por-pregunta.csv'),'VERSIONES: '+str(versions)]
    (out/'RESUMEN_PLAN.txt').write_text('\n'.join(summary)+'\n');print('\n'.join(summary))

if __name__=='__main__':main()
