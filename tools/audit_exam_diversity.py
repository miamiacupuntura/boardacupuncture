#!/usr/bin/env python3
"""Read-only diversity audit of frozen exams and registered draft members."""
import argparse
import collections as C
import csv
import datetime as DT
import hashlib
import html
import itertools
import json
import math
from pathlib import Path
import re
import subprocess
import tempfile
import unicodedata
from urllib.parse import urlencode

DOMAINS = {
    'formula_differentiation': ('Selección y diferenciación de fórmulas', 28),
    'single_herb': ('Hierbas individuales', 24),
    'dui_yao': ('Dui Yao y compatibilidad', 18),
    'safety_dispensing': ('Seguridad y dispensación', 15),
    'formula_composition': ('Composición y modificación', 10),
    'scope_aliases': ('Roster, alias y sustituciones', 5),
}
TOPICS = {
    'Composición/modificaciones': r'\b(?:composition|modif\w*|add(?:ed|ing)?|remove\w*|substitut\w*|composicion|agreg\w*|anad\w*|retir\w*)\b',
    'Procesamiento/preparación': r'\b(?:process\w*|prepar\w*|honey|wine.prepared|charred|roasted|miel|carboniz\w*|tostad\w*)\b',
    'Dosis/administración': r'\b(?:dose|dosage|grams?|milligrams?|dosis|gramos?|miligramos?|decoct\w*|decocc\w*)\b|\b\d+(?:\.\d+)?\s*(?:g|mg)\b',
    'Canales/meridianos': r'\b(?:channels?|meridians?|canales?|meridianos?)\b',
    'Propiedades/sabores': r'\b(?:temperature|flavo[u]?r|acrid|sweet|bitter|salty|sour|temperatura|sabores?|acre|dulce|amargo|salado|acido)\b',
    'Embarazo/lactancia': r'\b(?:pregnan\w*|lactat\w*|breastfeed\w*|embaraz\w*|lactancia)\b',
    'Interacciones': r'\b(?:interact\w*|warfarin|digoxin|anticoagul\w*|interaccion\w*|medicamentos?)\b',
    'Contraindicaciones/toxicidad': r'\b(?:contraindicat\w*|toxic\w*|caution|unsafe|avoid|precaucion|evitar)\b',
    'Comparación/diferencial': r'\b(?:distinguish\w*|differentiat\w*|compar\w*|distinc\w*|diferenci\w*)\b',
    'Identificación/acción': r'\b(?:which herb|which formula|what herb|what formula|que hierba|que formula|best addresses|most appropriate)\b',
}
CLINICAL = re.compile(r'\b(?:patient|presents?|padece|paciente|persona presenta|year.old|years.old|anos de edad)\b')
HIERARCHY = re.compile(r'\b(?:hierarch\w*|jerarqu\w*|jun chen zuo shi|emperador|mensajero)\b|\b(?:chief|deputy|envoy|monarch|minister|assistant)\s+(?:herbs?|ingredients?|role)|(?:matches?|identify|identif\w*|actua como|corresponde|papel|role|as an?)\W+(?:the\W+)?(?:chief|deputy|assistant|envoy|jun|chen|zuo|shi)\b')
BADCHOICES = re.compile(r'\b(?:all of the above|none of the above|both a and b|a and b are correct|todas las anteriores|ninguna de las anteriores|ambas son correctas)\b')
STOP = set('a an the of for with and or in on to is are what which this that patient presents following best most appropriate herb formula option correct answer choose select identify according identification ingredients hierarchy de del la las el los en un una con por para que cual paciente presenta siguiente formula hierba opcion respuesta correcta seleccion identifique'.split())
FIELDSET = ['prompt', 'options', 'correctLabel', 'explanation', 'translations', 'contentType', 'cognitiveLevel', 'difficultyBand', 'source', 'sourceCard', 'nomenclature']

def norm(value):
    value = ''.join(c for c in unicodedata.normalize('NFKD', str(value or '')) if not unicodedata.combining(c)).lower()
    return re.sub(r'[^a-z0-9]+', ' ', value).strip()

def plain(value):
    return re.sub(r'<[^>]+>', ' ', str(value or ''))

def read_json(path, optional=False):
    if not path.is_file() and optional:
        return {}
    return json.loads(path.read_text())

def usable(text):
    return bool(str(text or '').strip()) and norm(text) not in {'not completed', 'pending', 'missing', 'no completado', 'pendiente'}

def hierarchy(q):
    # Test the actual stem, not a role mentioned incidentally in the feedback.
    stem = norm(plain(q.get('prompt')))
    return q.get('contentType') == 'formula_hierarchy' or bool(HIERARCHY.search(stem)) or bool(re.search(r'\b(?:which|what|que|cual)\b.{0,100}\b(?:chief|deputy|assistant|envoy)\b(?!\s+complaint)',stem))

def spanish(q):
    translations = q.get('translations')
    value = translations.get('es') if isinstance(translations, dict) else None
    return value if isinstance(value, dict) else {}

def options(q):
    result = []
    for i, item in enumerate(q.get('options') or []):
        result.append({'label': str(item.get('label', chr(65+i))).upper(), 'text': plain(item.get('text', ''))} if isinstance(item, dict) else {'label': chr(65+i), 'text': plain(item)})
    return result

def answer(q):
    return next((o['text'] for o in options(q) if o['label'] == str(q.get('correctLabel', '')).upper()), '')

def tokens(text):
    return {x for x in norm(plain(text)).split() if x not in STOP and len(x) > 2}

def targets(size):
    values = {d: int(size*w/100) for d, (_, w) in DOMAINS.items()}
    fractions = sorted(DOMAINS, key=lambda d: -(size*DOMAINS[d][1]/100-values[d]))
    for d in fractions[:size-sum(values.values())]:
        values[d] += 1
    return values

def runtime_questions(root):
    """Reconstruct the existing DY-REV records exactly from their source table.

    These are already generated by Moodle's question-bank page, rather than
    new questions authored by the auditor. Nothing is written to the bank.
    """
    source_path=root/'blocks/myprogress/dui-yao-smart-data.json'
    code_path=root/'blocks/myprogress/admin-herbology-question-bank.php'
    if not source_path.is_file() or not code_path.is_file():return []
    code=code_path.read_text()
    count=re.search(r'array_slice\(\$duiyaosmartpairs,\s*0,\s*(\d+)\)',code)
    offsets=re.search(r'\$duiyaosupplementoffsets\s*=\s*\[([0-9,\s]+)\]',code)
    if "'DY-REV-'" not in code or not count or not offsets:return []
    offsets=[int(v.strip()) for v in offsets[1].split(',') if v.strip()]
    if len(offsets)!=3:return []
    pairs=read_json(source_path).get('pairs',[])
    if len(pairs)<32:return []
    herbs=read_json(root/'blocks/myprogress/single-herb-card-index.json').get('herbs',[])
    official={norm(h['name']):h for h in herbs}
    if any(norm(name) not in official for pair in pairs for name in pair.get('herbs',[])):
        raise ValueError('La tabla de Dui Yao incluye un nombre no encontrado en el roster; revisar la fuente antes de auditar.')
    result=[]
    for i,pair in enumerate(pairs[:int(count[1])]):
        if not all([pair.get('title'),pair.get('slug'),(pair.get('action') or {}).get('en'),(pair.get('action') or {}).get('es')]):continue
        distractors=[pairs[(i+offset)%len(pairs)] for offset in offsets]
        chosen=[];j=0;key='ABCD'[i%4]
        for option_index in range(4):
            if option_index==i%4:chosen.append(pair)
            else:chosen.append(distractors[j]);j+=1
        if any(not (p.get('action') or {}).get(lang) for p in chosen for lang in ['en','es']):continue
        opts={lang:[{'label':label,'text':p['action'][lang].strip()} for label,p in zip('ABCD',chosen)] for lang in ['en','es']}
        en=['Inference step 1: Identify the therapeutic direction created by the two herbs acting together.',
            'Inference step 2: '+pair['title']+' performs the action stated in option '+key+'.']
        es=['Paso de inferencia 1: Identifique la dirección terapéutica creada por las dos hierbas actuando juntas.',
            'Paso de inferencia 2: '+pair['title']+' realiza la acción indicada en la opción '+key+'.']
        number=int(pair.get('number') or i+1)
        for label,p in zip('ABCD',chosen):
            if label==key:
                en.append('Option '+label+': Correct. This is the verified combined action of pair '+str(number)+'.')
                es.append('Opción '+label+': Correcta. Esta es la acción combinada verificada del par '+str(number)+'.')
            else:
                en.append('Option '+label+': Incorrect. This action describes '+p['title']+', not '+pair['title']+'.')
                es.append('Opción '+label+': Incorrecta. Esta acción describe '+p['title']+', no '+pair['title']+'.')
        result.append({'questionId':f'DY-REV-{number:03d}','domainId':'dui_yao','contentType':'dui_yao',
            'cognitiveLevel':'apply','difficultyBand':'application','prompt':'Which combined action is most characteristic of the Duì Yào pair '+pair['title']+'?',
            'options':opts['en'],'correctLabel':key,'explanation':' '.join(en),
            'translations':{'es':{'prompt':'¿Qué acción combinada es más característica del par Duì Yào '+pair['title']+'?','options':opts['es'],'explanation':' '.join(es)}},
            'source':{'title':'Verified 51-pair Duì Yào study bank','section':'Reverse action recognition','locator':'Official pair '+str(number)+' · '+pair['title']},
            'sourceCard':{'url':'/study-guide/comparisons/'+pair['slug']+'.html','urlEs':'/study-guide/comparisons/'+pair['slug']+'-es.html','label':'Source card: '+pair['title'],'labelEs':'Tarjeta fuente: '+pair['title']},
            'nomenclature':{'herbs':[{'pinyin':official[norm(name)]['name'],'pharmaceuticalName':official[norm(name)]['latin']} for name in pair['herbs']],'formulas':[]},
            '_auditRuntimeSource':'existing-dy-rev-source-table','examTemplate':{'globalPosition':0}})
    return result

def load_exams(root, data):
    paths = [root/'blocks/menu/herbology-domain-candidates.json', data/'board-domain-banks/herbology-scorm-question-bank.json', data/'board-domain-banks/herbology-question-overrides.json']
    sources = []
    index = {}
    base = read_json(paths[0])
    for source_path in paths[:2]:
        document = read_json(source_path, optional=True)
        if source_path.is_file():
            sources.append({'file': str(source_path), 'sha256': hashlib.sha256(source_path.read_bytes()).hexdigest()})
        for domain, questions in document.get('domains', {}).items():
            for question in questions:
                if isinstance(question, dict) and question.get('questionId'):
                    index[question['questionId']] = dict(question, domainId=question.get('domainId') or domain)
    dynamic=runtime_questions(root)
    for question in dynamic:index.setdefault(question['questionId'],question)
    if dynamic:
        runtime_path=root/'blocks/myprogress/dui-yao-smart-data.json'
        sources.append({'file':str(runtime_path),'sha256':hashlib.sha256(runtime_path.read_bytes()).hexdigest()})
    store = read_json(paths[2])
    sources.append({'file': str(paths[2]), 'sha256': hashlib.sha256(paths[2].read_bytes()).hexdigest()})
    revisions = store.get('questions', {})
    customs = store.get('customQuestions', {})
    for qid, record in customs.items():
        if isinstance(record, dict):
            q = record.get('approved') or record.get('draft') or record
            if isinstance(q, dict):
                index[qid] = dict(q, questionId=qid, domainId=record.get('domainId') or q.get('domainId'))

    def effective(qid, pending=True):
        original = index.get(qid)
        if not original:
            return {'questionId': qid, '_auditMissing': True}
        q = dict(original)
        record = revisions.get(qid) or customs.get(qid) or {}
        snapshot = (record.get('draft') if pending else None) or record.get('approved') or {}
        if isinstance(snapshot, dict):
            q.update({k: v for k, v in snapshot.items() if k in FIELDSET})
        q['domainId'] = record.get('domainId') or q.get('domainId')
        q['_auditRevisionStatus'] = record.get('status', 'original')
        return q

    exams = []
    published_project_ids = set()
    for key, record in store.get('publishedExams', {}).items():
        if not isinstance(record, dict) or not isinstance(record.get('questions'), list):
            continue
        qs = [dict(q) for q in record['questions'] if isinstance(q, dict)]
        for q in qs:
            q['domainId'] = q.get('domainId') or index.get(q.get('questionId'), {}).get('domainId')
        exams.append({'key': key, 'name': record.get('name', key), 'state': 'publicado', 'version': record.get('version'), 'target': record.get('targetCount', 100), 'provenance': 'Snapshot publicado; no se sustituyó por el borrador actual del banco.', 'questions': qs})
        if record.get('projectId'):
            published_project_ids.add(record['projectId'])
    has_exam1 = any(re.search(r'(?:exam|esam)[ _-]*0*1\b', norm(e['name'])) or e['key'] == 'simulation_exam_1' for e in exams)
    if not has_exam1:
        configured = []
        for domain, qs in base.get('domains', {}).items():
            for q in qs:
                template = q.get('examTemplate') or {}
                if int(template.get('globalPosition') or 0) > 0 and int(template.get('examNumber') or 1) == 1:
                    entry = effective(q['questionId'], pending=False)
                    entry['_auditPosition'] = int(template['globalPosition'])
                    configured.append(entry)
        if configured:
            configured.sort(key=lambda q: q['_auditPosition'])
            exams.insert(0, {'key': 'exam1-configured', 'name': 'Exam 1', 'state': 'configurado en banco', 'version': None, 'target': 100, 'provenance': 'Reconstruido de posiciones del banco base y revisiones aprobadas; no existe snapshot histórico independiente en publishedExams.', 'questions': configured})
    for key, record in store.get('examProjects', {}).items():
        if not isinstance(record, dict) or key in published_project_ids:
            continue
        ids = list(dict.fromkeys(str(x) for x in record.get('questionIds', []) if x))
        state = 'borrador' if record.get('status') != 'published' else 'publicado sin snapshot localizado'
        exams.append({'key': key, 'name': record.get('name', key), 'state': state, 'version': record.get('publishedVersion'), 'target': record.get('targetCount', 100), 'provenance': 'Miembros registrados del proyecto, con borradores vigentes. La cola automática de candidatos todavía no incorporados no forma parte de este conteo.', 'questions': [effective(qid) for qid in ids]})
    return exams, sources

def normalize_exams(exams, root, data, enabled):
    if not enabled:
        return exams, 'Sin normalización de pantalla: se analizó el texto guardado.'
    helper = root/'blocks/myprogress/herbology-question-overrides.php'
    if not helper.is_file():
        return exams, 'No se encontró el normalizador: se analizó el texto guardado.'
    php = '''<?php
define('MOODLE_INTERNAL', true);
$CFG=(object)['dirroot'=>$argv[1], 'dataroot'=>$argv[2]];
require $argv[1].'/blocks/myprogress/herbology-question-overrides.php';
$exams=json_decode(stream_get_contents(STDIN),true,512,JSON_THROW_ON_ERROR);
foreach ($exams as &$exam) {
 foreach ($exam['questions'] as &$question) {
  $question=block_myprogress_herbology_canonicalize_pinyin_case_in_question($question);
 }
 unset($question);
}
unset($exam);
echo json_encode($exams,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
'''
    try:
        with tempfile.TemporaryDirectory(prefix='exam-diversity-') as temp:
            script = Path(temp)/'normalize.php'
            script.write_text(php)
            result = subprocess.run(['php', str(script), str(root), str(data)], input=json.dumps(exams, ensure_ascii=False), text=True, capture_output=True, timeout=180, check=True)
            normalized = json.loads(result.stdout)
        return normalized, 'Se aplicó el normalizador de nombres de la pantalla a copias en memoria; no se guardaron revisiones.'
    except (OSError, subprocess.SubprocessError, ValueError) as error:
        return exams, 'No se pudo aplicar el normalizador; se analizó texto guardado. Detalle: '+str(error)[:250]

def name_matcher(names):
    lookup = {norm(name): name for name in names}
    if not lookup:
        return lambda text: []
    pattern = re.compile(r'(?<![a-z0-9])(?:'+'|'.join(re.escape(k) for k in sorted(lookup, key=len, reverse=True))+r')(?![a-z0-9])')
    return lambda text: list(dict.fromkeys(lookup[m.group()] for m in pattern.finditer(norm(text))))

def analyze(exams, raw, herbs, formulas):
    herb_names = name_matcher([h['name'] for h in herbs])
    formula_names = name_matcher([f['name'] for f in formulas]+['An Gong Niu Huang Wan', 'Yin Chen Zhu Fu Tang'])
    formula_meta = {f['name']: f for f in formulas}
    raw_by_key = {e['key']: e for e in raw}
    results = []
    global_ids = C.defaultdict(list)
    all_stems = []
    for exam in exams:
        qs = exam['questions']; n = len(qs); target = int(exam['target'] or 100)
        domains = C.Counter(); topics = C.Counter(); cognitive = C.Counter(); letters = C.Counter(); sources = C.Counter()
        correct_concepts = C.defaultdict(list); formulas_seen = C.Counter(); herbs_seen = C.Counter(); chapters = C.Counter()
        domain_rows = C.defaultdict(lambda: {'n': 0, 'hierarchy': 0, 'topics': C.Counter(), 'cognitive': C.Counter(), 'concepts': set(), 'formulas': set(), 'herbs': set()})
        flags = []; rows = []; exact = C.defaultdict(list); hierarchy_ids = []; clinical = 0; profiles = 0; missing_es = 0; weak_feedback = 0
        last_letter = None; run = 0; longest_run = 0
        raw_map = {q.get('questionId'): q for q in raw_by_key[exam['key']]['questions']}
        for position, q in enumerate(qs, 1):
            qid = str(q.get('questionId') or f'posición-{position}')
            if q.get('questionId'): global_ids[qid].append(exam['key'])
            stem = plain(q.get('prompt')); normalized = norm(stem); opts = options(q); key = str(q.get('correctLabel', '')).upper()
            domain = q.get('domainId') or 'sin clasificar'; domains[domain] += 1
            cognitive_label = str(q.get('cognitiveLevel') or q.get('difficultyBand') or 'sin etiqueta').lower()
            cognitive[cognitive_label] += 1; letters[key or '?'] += 1
            if key and key == last_letter: run += 1
            else: run = 1; last_letter = key
            longest_run = max(longest_run, run)
            row_topics = [label for label, regex in TOPICS.items() if re.search(regex, normalized)]
            if hierarchy(q):
                row_topics.insert(0, 'Jerarquía de ingredientes'); hierarchy_ids.append(qid)
            if CLINICAL.search(normalized): clinical += 1; row_topics.append('Caso clínico explícito')
            elif sum(bool(re.search(TOPICS[t], normalized)) for t in ['Propiedades/sabores', 'Canales/meridianos', 'Dosis/administración']) >= 2:
                profiles += 1; row_topics.append('Perfil descriptivo de materia médica')
            if not row_topics: row_topics.append('Tema no identificado automáticamente')
            topics.update(set(row_topics))
            main = stem+' '+' '.join(o['text'] for o in opts)
            fs = formula_names(main)
            # Formula component names are not counted as independent herb mentions.
            herb_text = norm(main)
            for name in fs: herb_text = re.sub(r'\b'+re.escape(norm(name))+r'\b', ' ', herb_text)
            hs = herb_names(herb_text)
            formulas_seen.update(fs); herbs_seen.update(hs)
            for name in fs:
                record = formula_meta.get(name, {})
                categories = record.get('benskyCategories') or [record.get('benskyCategory') or {}]
                for chapter in {c.get('chapter') for c in categories if c.get('chapter')}:
                    chapters[str(chapter)] += 1
            correct_text = answer(q)
            af = formula_names(correct_text); ah = herb_names(correct_text)
            concept = ' + '.join(af or ah) or norm(correct_text)[:180] or 'sin respuesta identificable'
            correct_concepts[concept].append(qid)
            d = domain_rows[domain]; d['n'] += 1; d['hierarchy'] += hierarchy(q); d['topics'].update(set(row_topics)); d['cognitive'][cognitive_label] += 1; d['concepts'].add(concept); d['formulas'].update(fs); d['herbs'].update(hs)
            source = q.get('source') or {}
            source_label = source.get('title') or source.get('label') or 'sin fuente' if isinstance(source, dict) else str(source)
            sources[source_label] += 1
            exact_signature = (norm(stem), tuple((o['label'], norm(o['text'])) for o in opts), key)
            if normalized: exact[exact_signature].append(qid)
            if len(tokens(stem)) >= 8: all_stems.append((exam['key'], qid, tokens(stem), key))
            qflags = []
            if q.get('_auditMissing'): qflags.append('ID registrado sin pregunta fuente localizada')
            if q.get('_auditRevisionStatus') in ['deleted', 'study_archive', 'extra_guideline', 'exclusion_candidate']: qflags.append('Miembro del proyecto con estado excluido/archivado')
            if len(opts) != 4 or key not in [o['label'] for o in opts]: qflags.append('Estructura o clave incompleta')
            if len({norm(o['text']) for o in opts}) < len(opts): qflags.append('Opciones idénticas después de normalizar texto')
            if any(BADCHOICES.search(norm(o['text'])) for o in opts): qflags.append('Opción combinada o todas/ninguna')
            lengths = [len(tokens(o['text'])) for o in opts]
            if lengths and min(lengths) <= 4 and max(lengths) >= 18: qflags.append('Asimetría marcada de longitud entre opciones; revisar posible pista')
            es = spanish(q)
            if not isinstance(es, dict) or not usable(es.get('prompt')) or len(options(es)) != 4 or not usable(es.get('explanation')):
                missing_es += 1; qflags.append('Versión española incompleta')
            elif es.get('correctLabel') and str(es['correctLabel']).upper() != key: qflags.append('Clave española distinta de la inglesa')
            feedback = plain(q.get('explanation'))
            wrong = [o['label'] for o in opts if o['label'] != key]
            if not usable(feedback) or len(tokens(feedback)) < 25 or any(not re.search(r'(?:\b(?:option|opcion)\s+'+re.escape(label)+r'\b|\b'+re.escape(label)+r'[.:])', feedback, re.I) for label in wrong):
                weak_feedback += 1; qflags.append('Retroalimentación breve o sin referencia explícita a cada distractor')
            raw_q = raw_map.get(qid, {})
            before = plain(raw_q.get('prompt'))+' '+' '.join(o['text'] for o in options(raw_q))
            for preparation in ['Zhi Gan Cao','Sheng Gan Cao','Jiu Da Huang','Sheng Da Huang','Chao Da Huang','Da Huang Tan','Zhi Ban Xia','Jiang Ban Xia','Fa Ban Xia','Sheng Di Huang','Shu Di Huang']:
                if norm(preparation) in norm(before) and norm(preparation) not in norm(main):
                    qflags.append('La normalización eliminó el nombre de preparación: '+preparation)
            if qflags: flags.append({'id': qid, 'position': position, 'signals': qflags})
            rows.append({'position': position, 'id': qid, 'domain': domain, 'topics': sorted(set(row_topics)), 'cognitive_label': cognitive_label, 'correct_label': key, 'answer_concept': concept, 'formula_mentions': fs, 'herb_mentions': hs, 'signals': qflags, 'prompt': stem, 'options': opts, 'explanation': feedback})
        quota = int(target*0.05)
        expected_domains = targets(target)
        exact_dups = [ids for ids in exact.values() if len(ids) > 1]
        results.append({
            'key': exam['key'], 'name': exam['name'], 'state': exam['state'], 'version': exam.get('version'), 'provenance': exam['provenance'],
            'registered_questions': n, 'target': target, 'complete': n == target,
            'domains': dict(domains), 'expected_domains': expected_domains,
            'hierarchy_count': len(hierarchy_ids), 'hierarchy_reference': quota, 'hierarchy_delta': len(hierarchy_ids)-quota, 'hierarchy_ids': hierarchy_ids,
            'topics': dict(topics), 'cognitive_labels': dict(cognitive), 'clinical_stems': clinical, 'descriptive_profiles': profiles,
            'unique_answer_concepts': len(correct_concepts), 'top_answers': [{'concept': c, 'count': len(ids), 'ids': ids} for c, ids in sorted(correct_concepts.items(), key=lambda item: -len(item[1]))[:12]],
            'formula_mentions': dict(formulas_seen), 'herb_mentions': dict(herbs_seen), 'formula_chapters': dict(chapters), 'sources': dict(sources),
            'correct_letters': dict(letters), 'longest_same_letter_run': longest_run,
            'missing_spanish': missing_es, 'feedback_review_count': weak_feedback, 'exact_duplicate_groups': exact_dups,
            'domain_diversity': {k: dict(v, topics=dict(v['topics']), cognitive=dict(v['cognitive']), concepts=len(v['concepts']), formulas=len(v['formulas']), herbs=len(v['herbs'])) for k, v in domain_rows.items()},
            'review_signals': flags, 'questions': rows,
        })
    near = []
    for (ea, qa, ta, ka), (eb, qb, tb, kb) in itertools.combinations(all_stems, 2):
        if qa == qb or abs(len(ta)-len(tb)) > max(len(ta), len(tb))*0.25: continue
        score = len(ta & tb)/len(ta | tb)
        if score >= 0.85:
            near.append({'exam_a': ea, 'id_a': qa, 'exam_b': eb, 'id_b': qb, 'stem_similarity': round(score, 3), 'same_key': ka == kb})
    near.sort(key=lambda x: -x['stem_similarity'])
    return results, {qid: sorted(set(es)) for qid, es in global_ids.items() if len(set(es)) > 1}, near

def table(headers, rows):
    esc = lambda x: html.escape(str(x))
    return '<table><thead><tr>'+''.join('<th>'+esc(h)+'</th>' for h in headers)+'</tr></thead><tbody>'+''.join('<tr>'+''.join('<td>'+esc(v)+'</td>' for v in row)+'</tr>' for row in rows)+'</tbody></table>'

def render(report):
    results = report['exams']; esc = lambda x: html.escape(str(x))
    parts = ['<!doctype html><html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Auditoría de diversidad de exámenes</title><style>body{font:16px/1.55 system-ui,sans-serif;color:#173449;background:#f5f8fa;margin:0}main{max-width:1200px;margin:24px auto;padding:24px;background:white}h1,h2,h3{color:#006d69}table{width:100%;border-collapse:collapse;margin:14px 0}th,td{padding:9px;text-align:left;border-bottom:1px solid #d6e0e5;vertical-align:top;overflow-wrap:anywhere}th{background:#edf6f4}details{margin:14px 0;border:1px solid #d6e0e5;padding:12px}summary{cursor:pointer;font-weight:650}a{color:#075e9d}.note{padding:14px;border-left:4px solid #d18b22;background:#fff8e9}.scroll{overflow:auto}small{color:#52677a}li{margin-bottom:7px}@media print{details{display:block}summary{display:none}main{margin:0;padding:0}.scroll{overflow:visible}}</style><main>',
             '<h1>Diversidad de preguntas: auditoría por examen y dentro de cada dominio</h1>', '<p>Fecha UTC: '+esc(report['created_at'])+'</p>',
             '<p class="note">La distribución por dominio y la diversidad se evalúan por separado. La referencia acordada para jerarquía es 5 preguntas por cada 100. No se ha cambiado ninguna pregunta, examen, intento ni publicación.</p>',
             '<h2>Resumen ejecutivo basado en los datos</h2>']
    findings = []
    for e in results:
        lead = e['name']+': '
        if not e['complete']: findings.append(lead+f"solo hay {e['registered_questions']} miembros registrados de un objetivo de {e['target']}; no se trata como un examen terminado.")
        if e['hierarchy_delta'] > 0: findings.append(lead+f"tiene {e['hierarchy_count']} preguntas de jerarquía: excede en {e['hierarchy_delta']} la referencia de {e['hierarchy_reference']}.")
        elif e['complete'] and e['hierarchy_delta'] < 0: findings.append(lead+f"tiene {e['hierarchy_count']} preguntas de jerarquía: faltan {-e['hierarchy_delta']} para la referencia de {e['hierarchy_reference']}.")
        if e['complete'] and (any(e['domains'].get(d,0)!=v for d,v in e['expected_domains'].items()) or any(d not in DOMAINS for d in e['domains'])): findings.append(lead+'sus conteos por dominio difieren del esquema de referencia; revisar la tabla de dominios.')
        if e['exact_duplicate_groups']: findings.append(lead+f"contiene {len(e['exact_duplicate_groups'])} grupos de enunciado, opciones y clave idénticos.")
        if e['top_answers'] and e['top_answers'][0]['count'] >= max(5, math.ceil(e['registered_questions']*0.10)):
            top = e['top_answers'][0]; findings.append(lead+f"la respuesta «{top['concept']}» aparece {top['count']} veces; puede evaluar objetivos distintos y requiere revisar el contexto.")
        if e['missing_spanish']: findings.append(lead+f"tiene {e['missing_spanish']} versiones españolas incompletas.")
    if not findings: findings.append('No se detectaron excesos de jerarquía, duplicados exactos ni carencias españolas bajo estas reglas; esto no certifica la calidad clínica de todas las preguntas.')
    parts.append('<ul>'+''.join('<li>'+esc(f)+'</li>' for f in findings)+'</ul>')
    parts.append('<div class="scroll">'+table(['Examen / estado','Registradas / objetivo','Jerarquía / referencia','Casos explícitos','Perfiles descriptivos','Respuestas distintas','Español incompleto','Feedback a revisar'], [[e['name']+' · '+e['state'],f"{e['registered_questions']} / {e['target']}",f"{e['hierarchy_count']} / {e['hierarchy_reference']}",e['clinical_stems'],e['descriptive_profiles'],e['unique_answer_concepts'],e['missing_spanish'],e['feedback_review_count']] for e in results])+'</div>')
    parts.append('<h2>Qué mide y qué requiere revisión humana</h2><ul><li>Jerarquía: clasificación explícita o pregunta que evalúa el rol jerárquico; una mención de Chief en la explicación no basta.</li><li>Temas: detección de términos en el enunciado. Son etiquetas múltiples y no suman 100. Un caso clínico sin diagnóstico escrito puede no revelar todos sus temas al detector.</li><li>Casos y perfiles: señales de estructura textual, no una medida validada del nivel de razonamiento.</li><li>Nivel cognitivo: se informa la etiqueta guardada. Una etiqueta «inferencia» no demuestra que la pregunta exija inferencia.</li><li>Repetir la misma respuesta no demuestra duplicación: se aportan los IDs para comparar los objetivos.</li><li>Longitud desigual, respuestas combinadas y feedback incompleto son señales de revisión. La plausibilidad clínica de cada distractor requiere análisis de contenido.</li><li>Sin respuestas de alumnos no se calculan dificultad real, discriminación ni fiabilidad psicométrica.</li></ul>')
    for e in results:
        parts += ['<h2>'+esc(e['name'])+'</h2><p>'+esc(e['provenance'])+'</p>', '<h3>Diversidad dentro de cada dominio</h3>']
        drows = []
        for key, (name, _) in DOMAINS.items():
            d = e['domain_diversity'].get(key, {})
            drows.append([name,d.get('n',0),e['expected_domains'][key],d.get('hierarchy',0),d.get('concepts',0),d.get('formulas',0),d.get('herbs',0),'; '.join(f'{k}: {v}' for k,v in sorted(d.get('topics',{}).items(), key=lambda x:-x[1]))])
        parts.append('<div class="scroll">'+table(['Dominio','Actual','Objetivo al completar','Jerarquía','Respuestas distintas','Fórmulas mencionadas','Hierbas mencionadas','Temas presentes en enunciados'],drows)+'</div>')
        parts += ['<h3>Distribución temática y cognitiva</h3>',table(['Tema en el enunciado','Preguntas'],sorted(e['topics'].items(),key=lambda x:-x[1])),table(['Etiqueta cognitiva guardada','Preguntas'],e['cognitive_labels'].items()),'<h3>Concentración de respuestas</h3>',table(['Respuesta o conjunto','Veces','IDs'],[[r['concept'],r['count'],', '.join(r['ids'])] for r in e['top_answers']]),'<h3>Posición de la respuesta correcta</h3>',table(['A','B','C','D','Clave no identificada','Mayor racha de una letra'],[[e['correct_letters'].get(k,0) for k in 'ABCD']+[e['correct_letters'].get('?',0),e['longest_same_letter_run']]])]
        parts.append('<details><summary>Cobertura de fórmulas, hierbas, capítulos y fuentes</summary><p>Son menciones en enunciado/opciones, no todas constituyen el objetivo evaluado.</p>'+table(['Fórmula','Preguntas donde se menciona'],sorted(e['formula_mentions'].items(),key=lambda x:-x[1]))+table(['Hierba','Preguntas donde se menciona'],sorted(e['herb_mentions'].items(),key=lambda x:-x[1]))+table(['Capítulo de fórmulas','Menciones por pregunta'],sorted(e['formula_chapters'].items()))+table(['Fuente','Preguntas'],sorted(e['sources'].items(),key=lambda x:-x[1]))+'</details>')
        parts.append('<details><summary>Preguntas con señales de revisión: '+str(len(e['review_signals']))+'</summary>'+table(['Posición','ID','Señales'],[[r['position'],r['id'],'; '.join(r['signals'])] for r in e['review_signals']])+'</details>')
        evidence=[]
        for question in e['questions']:
            if not question['signals']: continue
            url='https://acupuncturetests.com/blocks/myprogress/admin-herbology-question-bank.php?'+urlencode({'q':question['id']})
            evidence.append('<details><summary>'+esc(question['id'])+' · '+esc('; '.join(question['signals']))+'</summary><p>'+esc(question['prompt'])+'</p>'+table(['Opción','Contenido'],[[o['label']+(' ✓' if o['label']==question['correct_label'] else ''),o['text']] for o in question['options']])+'<p><strong>Explicación guardada:</strong> '+esc(question['explanation'])+'</p><a href="'+html.escape(url,quote=True)+'">Abrir pregunta en el banco</a></details>')
        parts.append('<details><summary>Evidencia textual de las señales de revisión</summary>'+''.join(evidence)+'</details>')
        parts.append('<details><summary>Inventario pregunta por pregunta</summary>'+table(['N.º','ID','Dominio','Tema','Clave','Respuesta','Enunciado'],[[q['position'],q['id'],q['domain'],'; '.join(q['topics']),q['correct_label'],q['answer_concept'],q['prompt']] for q in e['questions']])+'</details>')
    parts += ['<h2>Coincidencias entre exámenes</h2>', '<p>Un mismo ID en dos exámenes es una coincidencia comprobada de identidad. Los enunciados similares son candidatos de revisión, no duplicados clínicos confirmados.</p>',table(['ID compartido','Exámenes'],[[qid,', '.join(es)] for qid,es in report['shared_ids'].items()]),table(['Examen A / ID','Examen B / ID','Similitud de palabras del enunciado'],[[r['exam_a']+' / '+r['id_a'],r['exam_b']+' / '+r['id_b'],r['stem_similarity']] for r in report['near_stem_pairs'][:100]]),'<h2>Plan para equilibrar versiones nuevas</h2><ol><li>Resolver primero errores clínicos, nomenclatura y ambigüedad de respuestas. No convertir un error de contenido en una simple excepción de guideline.</li><li>Fijar 5 preguntas de jerarquía por cada 100, distribuidas entre fórmulas y roles diferentes.</li><li>Cambiar las preguntas excedentes por preguntas de otros temas dentro del mismo dominio; así se conserva 28/24/18/15/10/5.</li><li>En exámenes con menos de 5, proponer sustituciones del mismo dominio antes de modificar otros dominios.</li><li>Usar el inventario por pregunta para variar fórmulas, hierbas, objetivos y formatos, y verificar individualmente la plausibilidad de los distractores.</li><li>Preparar cada nueva versión con un registro explícito de IDs que salen, IDs que entran y objetivo de cada cambio; publicar después de revisar ese registro.</li></ol>', '<h2>Procedencia y límites del informe</h2><p>'+esc(report['normalization'])+'</p><p>Los snapshots publicados se auditan como están congelados, con normalización de presentación en memoria. Los proyectos incompletos no incluyen candidatos dinámicos aún no registrados. No se ejecutan las reparaciones automáticas ni la selección de candidatos del panel: los proyectos se reconstruyen de sus archivos y revisiones. No se leyeron intentos ni datos de alumnos.</p>',table(['Archivo de entrada','SHA-256'],[[x['file'],x['sha256']] for x in report['sources']]),'</main></html>']
    return ''.join(parts), findings

def main():
    parser=argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--moodle-root',type=Path,default=Path('/var/www/html/moodle'))
    parser.add_argument('--data-root',type=Path,default=Path('/mnt/volume_nyc1_01/moodledata'))
    parser.add_argument('--output-dir',type=Path)
    parser.add_argument('--no-normalize',action='store_true')
    args=parser.parse_args()
    raw,sources=load_exams(args.moodle_root,args.data_root)
    if not raw: raise SystemExit('No se encontraron snapshots publicados ni proyectos registrados; no se generó un informe vacío.')
    normalized,mode=normalize_exams(raw,args.moodle_root,args.data_root,not args.no_normalize)
    herbs=read_json(args.moodle_root/'blocks/myprogress/single-herb-card-index.json')['herbs']
    formulas=read_json(args.moodle_root/'blocks/myprogress/formula-board-roster-2026.json')['formulas']
    results,shared,near=analyze(normalized,raw,herbs,formulas)
    now=DT.datetime.now(DT.timezone.utc)
    report={'created_at':now.isoformat(),'normalization':mode,'sources':sources,'hierarchy_policy':'5 por cada 100; referencia acordada, no requisito oficial NCBAHM','exams':results,'shared_ids':shared,'near_stem_pairs':near}
    document,findings=render(report)
    out=args.output_dir or Path('/var/backups/acupuncturetests')/('exam-diversity-'+now.strftime('%Y%m%d-%H%M%S-%f'))
    out.mkdir(parents=True,exist_ok=False)
    (out/'informe-diversidad.html').write_text(document)
    (out/'auditoria-detallada.json').write_text(json.dumps(report,ensure_ascii=False,indent=2))
    with (out/'resumen-examenes.csv').open('w',newline='') as handle:
        writer=csv.writer(handle);writer.writerow(['examen','estado','registradas','objetivo','jerarquia','referencia_jerarquia','casos_explicitos','perfiles_descriptivos','respuestas_distintas','espanol_incompleto','feedback_a_revisar'])
        for e in results:writer.writerow([e['name'],e['state'],e['registered_questions'],e['target'],e['hierarchy_count'],e['hierarchy_reference'],e['clinical_stems'],e['descriptive_profiles'],e['unique_answer_concepts'],e['missing_spanish'],e['feedback_review_count']])
    summary=['AUDITORÍA DE DIVERSIDAD — '+now.isoformat(),mode,'']
    for e in results:
        summary.append(f"{e['name']} [{e['state']}]: {e['registered_questions']}/{e['target']} preguntas; jerarquía {e['hierarchy_count']}/{e['hierarchy_reference']}; casos explícitos {e['clinical_stems']}; perfiles descriptivos {e['descriptive_profiles']}; respuestas distintas {e['unique_answer_concepts']}; español incompleto {e['missing_spanish']}.")
        summary.append('  Dominios: '+' / '.join(str(e['domains'].get(d,0)) for d in DOMAINS))
        summary.append('  Temas: '+', '.join(f'{k}={v}' for k,v in sorted(e['topics'].items(),key=lambda x:-x[1])))
        summary.append('  Respuestas frecuentes: '+'; '.join(f"{x['concept']} ({x['count']})" for x in e['top_answers'][:4]))
    summary+=['','HALLAZGOS:']+['- '+f for f in findings]+['',f'IDs compartidos entre exámenes: {len(shared)}',f'Pares de enunciados similares para revisar: {len(near)}','No se modificaron preguntas, exámenes ni publicaciones.','INFORME: '+str(out/'informe-diversidad.html'),'JSON: '+str(out/'auditoria-detallada.json')]
    (out/'RESUMEN_PARA_COMPARTIR.txt').write_text('\n'.join(summary)+'\n')
    print('\n'.join(summary))
    print('RESUMEN:',out/'RESUMEN_PARA_COMPARTIR.txt')

if __name__=='__main__':
    main()
