(() => {
  'use strict';

  const spanish = document.documentElement.lang === 'es';
  const params = new URLSearchParams(window.location.search);
  const sesskey = params.get('sesskey') || '';
  const resources = [
    {key:'families', en:'Formula Families / Variations', es:'Familias y variaciones de fórmulas', path:'families.html'},
    {key:'nametraps', en:'Name Traps', es:'Trampas de nombres', path:'name-traps.html'},
    {key:'herbparts', en:'Herb Families & Parts', es:'Familias y partes de hierbas', path:'herb-families.html'},
    {key:'formulanames', en:'Formula Name Traps', es:'Trampas de nombres de fórmulas', path:'formula-name-traps.html'},
    {key:'misleading', en:'Misleading Formula Names', es:'Nombres engañosos de fórmulas', path:'misleading-formula-names.html'},
    {key:'advanced', en:'Advanced Name Traps', es:'Trampas avanzadas de nombres', path:'advanced-name-traps.html'},
    {key:'supplements', en:'Supplemental Herb Families', es:'Familias suplementarias de hierbas', path:'supplemental-herb-families.html'},
    {key:'secondary', en:'Herbs Secondary Actions', es:'Acciones secundarias de hierbas', path:'secondary-actions.html'},
    {key:'pathology', en:'Pathology Comparisons', es:'Comparaciones de patologías', path:'pathologies.html'},
    {key:'patterns', en:'Pattern Comparisons', es:'Comparaciones de patrones', path:'patterns.html'},
    {key:'maciocia', en:'Pattern Comparison Maciocia', es:'Comparación de patrones Maciocia', path:'patterns-tips-maciocia.html'},
    {key:'safety', en:'Safety Library', es:'Biblioteca de seguridad', path:'safety.html'},
    {key:'composition', en:'Formula Composition', es:'Composición de fórmulas', path:'formula-theory.html'},
    {key:'incompat', en:'Herbal Incompatibilities', es:'Incompatibilidades herbales', path:'herbal-incompatibilities.html'},
    {key:'combinations', en:'Herbal Combination', es:'Combinaciones herbales', path:'/blocks/myprogress/common-herbal-combinations.php', dynamic:true}
  ];
  const copy = {
    loading: spanish ? 'Cargando el banco…' : 'Loading question bank…',
    ready: spanish ? 'Banco listo.' : 'Question bank ready.',
    failed: spanish ? 'No fue posible crear preguntas suficientes para este recurso.' : 'Not enough questions could be created from this resource.',
    prompt: spanish ? '¿Qué tópico corresponde a esta descripción?' : 'Which topic matches this description?',
    correct: spanish ? 'Correcto.' : 'Correct.',
    incorrect: spanish ? 'Incorrecto.' : 'Incorrect.',
    answer: spanish ? 'Respuesta correcta' : 'Correct answer',
    next: spanish ? 'Siguiente' : 'Next',
    finish: spanish ? 'Finalizar Quiz' : 'Finish Quiz',
    saved: spanish ? 'Resultado guardado en Exam History.' : 'Result saved to Exam History.',
    saveFailed: spanish ? 'No se pudo guardar el resultado.' : 'The result could not be saved.',
    chosen: spanish ? 'Tu respuesta' : 'Your answer',
    why: spanish ? 'Por qué' : 'Why',
    distinction: spanish ? 'Fórmula individual vs. familia de fórmulas' : 'Individual formula vs. formula family',
    distinctionNote: spanish ? 'Una fórmula es una receta específica. Una familia reúne una fórmula raíz y sus variaciones estructurales.' : 'A formula is one specific prescription. A family includes a root formula and its structural variations.',
    rootFormula: spanish ? 'Fórmula raíz' : 'Root formula',
    familyMembers: spanish ? 'Miembros de la familia' : 'Family members',
    progression: spanish ? 'Cómo distinguirlas' : 'How to distinguish them',
    architecture: spanish ? 'Arquitectura' : 'Architecture',
    detailFailed: spanish ? 'La explicación detallada no está disponible; revisa la pista y la respuesta correcta.' : 'The detailed explanation is unavailable; review the clue and correct answer.'
  };
  const prompts = {
    families: spanish ? '¿Qué familia o variación de fórmulas corresponde a estas características?' : 'Which formula family or variation matches these characteristics?',
    nametraps: spanish ? '¿Qué trampa de nombre se identifica con estas diferencias?' : 'Which name-trap entry is identified by these distinctions?',
    herbparts: spanish ? '¿Qué familia botánica corresponde a estas funciones y partes medicinales?' : 'Which botanical family matches these functions and medicinal-part relationships?',
    formulanames: spanish ? '¿Qué distinción entre nombres de fórmulas corresponde a estas pistas?' : 'Which formula-name distinction matches these clues?',
    misleading: spanish ? '¿Qué fórmula con nombre engañoso corresponde a estas acciones reales?' : 'Which misleadingly named formula matches these actual actions?',
    advanced: spanish ? '¿Qué distinción avanzada de nombres corresponde a estas pistas?' : 'Which advanced name distinction matches these clues?',
    supplements: spanish ? '¿Qué grupo de estudio contiene los medicamentos descritos a continuación?' : 'Which named study group contains the medicinals described below?',
    secondary: spanish ? '¿Qué hierba o acción secundaria corresponde a estas pistas clínicas?' : 'Which herb or secondary action matches these clinical clues?',
    pathology: spanish ? '¿Qué comparación patológica corresponde a este perfil clínico?' : 'Which pathology comparison matches this clinical profile?',
    patterns: spanish ? '¿Qué comparación de patrones corresponde a este conjunto de signos?' : 'Which pattern comparison matches this set of signs?',
    maciocia: spanish ? '¿Qué comparación de patrones de Maciocia corresponde a estas claves?' : 'Which Maciocia pattern comparison matches these key findings?',
    safety: spanish ? '¿Qué tema de seguridad corresponde a esta precaución clínica?' : 'Which safety topic matches this clinical precaution?',
    composition: spanish ? '¿Qué principio de composición de fórmulas corresponde a esta descripción?' : 'Which formula-composition principle matches this description?',
    incompat: spanish ? '¿Qué incompatibilidad o interacción herbal corresponde a esta advertencia?' : 'Which herbal incompatibility or interaction matches this warning?',
    combinations: spanish ? '¿Qué combinación herbal corresponde a estas funciones clínicas?' : 'Which herbal combination matches these clinical functions?'
  };
  const clueStopwords = new Set('the a an and or of to for with without one two three four job jobs part parts family families formula formulas pattern patterns herb herbs root roots leaf leaves seed seeds fruit fruits blood heat cold warm qi yin yang shen damp wind phlegm fire tonic tonics comparison comparisons trap traps advanced supplemental secondary action actions group groups genus topic topics el la los las un una uno dos tres cuatro de del y o para con sin familia familias formula formulas patron patrones hierba hierbas raiz raices hoja hojas semilla semillas fruto frutos sangre calor frio tibio viento flema fuego tonico tonicos comparacion comparaciones trampa trampas accion acciones grupo grupos genero tema temas'.split(' '));
  const semanticStopwords = new Set('the a an and or of to for with without from into in on at by is are was were be this that these those which what matches match best corresponds corresponding description topic entry rule el la los las un una unos unas y o de del al para por con sin desde en es son esta este estas estos que cual cuales corresponde descripcion tema regla'.split(' '));
  const el = {
    list: document.querySelector('[data-misc-list]'),
    setup: document.querySelector('[data-misc-setup]'),
    title: document.querySelector('[data-misc-title]'),
    status: document.querySelector('[data-misc-status]'),
    size: document.querySelector('[data-misc-size]'),
    start: document.querySelector('[data-misc-start]'),
    work: document.querySelector('[data-misc-work]'),
    workTitle: document.querySelector('[data-misc-work-title]'),
    position: document.querySelector('[data-misc-position]'),
    bar: document.querySelector('[data-misc-bar]'),
    prompt: document.querySelector('[data-misc-prompt]'),
    context: document.querySelector('[data-misc-context]'),
    options: document.querySelector('[data-misc-options]'),
    feedback: document.querySelector('[data-misc-feedback]'),
    next: document.querySelector('[data-misc-next]'),
    results: document.querySelector('[data-misc-results]'),
    score: document.querySelector('[data-misc-score]'),
    saved: document.querySelector('[data-misc-saved]'),
    back: document.querySelector('[data-misc-back]'),
    repeat: document.querySelector('[data-misc-repeat]'),
    language: document.querySelector('[data-misc-language]')
  };
  const state={resource:null,items:[],questions:[],index:0,score:0,answered:false,startedAt:0,attemptUid:''};
  const familyFeedbackCache=new Map();

  function shuffle(values){const result=values.slice();for(let i=result.length-1;i>0;i-=1){const j=Math.floor(Math.random()*(i+1));const value=result[i];result[i]=result[j];result[j]=value;}return result;}
  function stripHan(value){return String(value||'').replace(/[\u3400-\u4dbf\u4e00-\u9fff\uf900-\ufaff]/g,' ');}
  function clean(value){return stripHan(value).replace(/\s+/g,' ').trim();}
  function unique(values){return Array.from(new Set(values.map(clean).filter(Boolean)));}
  function fold(value){return clean(value).normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLocaleLowerCase().replace(/[^a-z0-9\u3400-\u9fff]+/g,' ').trim();}
  function escapeRegex(value){return String(value).replace(/[.*+?^$()|[\]{}\\]/g,'\\$&');}
  function titleClues(title){
    const chunks=clean(title).split(/\s+[—–]\s+|[,;/]|\s+(?:and|y)\s+/i).map(value=>clean(value.replace(/[\u3400-\u9fff]+/g,' '))).filter(Boolean);
    const words=(clean(title).match(/[A-Za-zÀ-ÿĀ-ž0-9'’]+/g)||[]).map(clean);
    const clues=[];
    chunks.forEach(chunk=>{
      const meaningful=(chunk.match(/[A-Za-zÀ-ÿĀ-ž0-9'’]+/g)||[]).filter(word=>!clueStopwords.has(fold(word)));
      if(meaningful.some(word=>fold(word).length>=3))clues.push(chunk);
    });
    words.forEach(word=>{const normalized=fold(word);if(normalized.length>=4&&!clueStopwords.has(normalized))clues.push(word);});
    return unique(clues).sort((a,b)=>b.length-a.length);
  }
  function redactAnswerClues(detail,title){
    let result=clean(detail);
    titleClues(title).forEach(clue=>{result=result.replace(new RegExp(escapeRegex(clue),'giu'),'');});
    return clean(result
      .replace(/\b(?:the|a|an)\s+(?=(?:genus|family|group)\b)/gi,'The ')
      .replace(/\b(?:el|la|un|una)\s+(?=(?:género|familia|grupo)\b)/gi,'La ')
      .replace(/\s+([,.;:])/g,'$1')
      .replace(/([,;:])(?:\s*[,;:])+/g,'$1')
      .replace(/(^|[.!?]\s+)[,;:\-–—]+\s*/g,'$1')
      .replace(/\s*•\s*(?=•|$)/g,'')
      .replace(/\(\s*\)/g,''));
  }
  function semanticTokens(value){
    return new Set(fold(value).split(' ').filter(token=>token.length>=3&&!semanticStopwords.has(token)));
  }
  function similarity(left,right){
    const leftTokens=semanticTokens(left),rightTokens=semanticTokens(right);
    let shared=0;leftTokens.forEach(token=>{if(rightTokens.has(token))shared+=1;});
    return shared/Math.sqrt(Math.max(1,leftTokens.size*rightTokens.size));
  }
  function choices(correct,pool){
    const ranked=pool.filter(item=>item!==correct).map(item=>({item,score:similarity(correct.title+' '+correct.detail,item.title+' '+item.detail)})).sort((a,b)=>b.score-a.score);
    const close=ranked.filter(candidate=>candidate.score>0).slice(0,8);
    const rest=ranked.filter(candidate=>!close.includes(candidate));
    const distractors=shuffle(close).concat(shuffle(rest)).slice(0,3).map(candidate=>candidate.item.title);
    return shuffle([correct.title].concat(distractors));
  }
  function attemptUid(){if(globalThis.crypto&&typeof globalThis.crypto.randomUUID==='function')return globalThis.crypto.randomUUID();return 'misc-'+Date.now()+'-'+Math.random().toString(36).slice(2);}
  function translatedPath(resource){
    if(resource.dynamic){
      const url=new URL(resource.path,window.location.origin);
      if(spanish)url.searchParams.set('lang','es');
      return url.toString();
    }
    const path=spanish?resource.path.replace(/\.html$/,'-es.html'):resource.path;
    const url=new URL(path,window.location.href);url.searchParams.set('v','20261111-7');return url.toString();
  }
  function readableElementText(element){
    const clone=element.cloneNode(true);
    clone.querySelectorAll('br').forEach(br=>br.replaceWith(' '));
    return clean(clone.textContent);
  }
  function readableParts(node,title){
    const parts=[];
    const add=value=>{
      const text=clean(value).replace(/^(?:Rule|Clinical focus|Key distinction|Regla|Enfoque clínico|Diferencia clave)\s*:\s*/i,'');
      if(text.length>=18&&text.length<=360&&fold(text)!==fold(title))parts.push(text);
    };
    Array.from(node.querySelectorAll('.comparison-card__category')).forEach(element=>add(readableElementText(element)));
    Array.from(node.querySelectorAll('p')).slice(0,3).forEach(element=>add(readableElementText(element)));
    Array.from(node.querySelectorAll('dl')).slice(0,1).forEach(list=>{
      const terms=Array.from(list.querySelectorAll('dt'));
      terms.slice(0,3).forEach(term=>{const definition=term.nextElementSibling;if(definition)add(readableElementText(term)+': '+readableElementText(definition));});
    });
    if(parts.length<1){
      Array.from(node.querySelectorAll('tr')).slice(0,3).forEach(row=>{
        const cells=Array.from(row.querySelectorAll('th,td')).map(readableElementText).filter(Boolean);
        if(cells.length>1)add(cells.join(': '));
      });
    }
    const selected=[];let length=0;
    unique(parts).some(part=>{if(selected.length>=2||length+part.length>520)return true;selected.push(part);length+=part.length;return false;});
    return selected.join(' • ');
  }
  function questionContext(item){
    const context=redactAnswerClues(item.detail,item.title);
    const tokens=semanticTokens(context);
    if(context.length<35||tokens.size<6)return '';
    const normalized=fold(context);
    const leaks=titleClues(item.title).some(clue=>{const value=fold(clue);return value.length>=4&&normalized.includes(value);});
    if(leaks)return '';
    if(state.resource&&state.resource.key==='supplements')return (spanish?'Medicamentos y funciones: ':'Medicinals and functions: ')+context;
    return context;
  }
  function extractItems(html){
    const doc=new DOMParser().parseFromString(html,'text/html');
    let nodes=Array.from(doc.querySelectorAll('article.comparison-card'));
    if(nodes.length<4)nodes=Array.from(doc.querySelectorAll('article'));
    if(nodes.length<4)nodes=Array.from(doc.querySelectorAll('main section')).filter(node=>node.querySelector('h2,h3'));
    const items=[];
    nodes.forEach(node=>{
      const heading=node.querySelector('h3,h2');
      const title=clean(heading&&heading.textContent);
      if(!title||title.length>160)return;
      const detail=readableParts(node,title);
      if(detail.length<35)return;
      const chapter=node.querySelector('[data-chapter-url]');
      const chapterUrl=chapter?chapter.getAttribute('data-chapter-url')||'':'';
      items.push({title,detail,chapterUrl});
    });
    const seen=new Set();
    return items.filter(item=>{const key=fold(item.title);if(seen.has(key))return false;seen.add(key);return true;});
  }
  function renderResources(){
    resources.forEach(resource=>{
      const button=document.createElement('button');
      button.type='button';
      button.className='misc-practice__resource';
      button.innerHTML='<strong></strong><span></span>';
      button.querySelector('strong').textContent=spanish?resource.es:resource.en;
      button.querySelector('span').textContent=spanish?'Abrir Quiz multiple choice':'Open multiple-choice Quiz';
      button.addEventListener('click',()=>selectResource(resource));
      el.list.append(button);
    });
  }
  async function selectResource(resource){
    state.resource=resource;state.items=[];el.title.textContent=spanish?resource.es:resource.en;el.workTitle.textContent=spanish?resource.es:resource.en;
    el.status.textContent=copy.loading;el.status.classList.remove('is-error');el.start.disabled=true;
    el.setup.hidden=false;el.work.hidden=true;el.results.hidden=true;
    try{
      const response=await fetch(translatedPath(resource),{credentials:'same-origin'});
      if(!response.ok)throw new Error('load');
      state.items=extractItems(await response.text());
      if(state.items.length<4)throw new Error('items');
      el.status.textContent=copy.ready+' '+state.items.length+' '+(spanish?'tópicos disponibles.':'topics available.');
      el.start.disabled=false;
      el.setup.scrollIntoView({behavior:'smooth',block:'start'});
      const url=new URL(window.location.href);url.searchParams.set('source',resource.key);history.replaceState({},'',url);
    }catch(_){el.status.textContent=copy.failed;el.status.classList.add('is-error');}
  }
  function buildQuestions(){
    const valid=state.items.map(item=>Object.assign({},item,{context:questionContext(item)})).filter(item=>item.context);
    if(valid.length<4)return [];
    const prompt=prompts[state.resource.key]||copy.prompt;
    return shuffle(valid.map(item=>({prompt,context:item.context,correct:item.title,options:choices(item,valid),explanation:item.detail,item})));
  }
  function renderQuestion(){
    const question=state.questions[state.index];state.answered=false;
    el.position.textContent=(state.index+1)+' / '+state.questions.length;
    el.bar.style.width=(((state.index+1)/state.questions.length)*100)+'%';
    el.prompt.textContent=question.prompt;el.context.textContent=question.context;el.options.replaceChildren();
    el.feedback.hidden=true;el.feedback.className='misc-practice__feedback';el.next.hidden=true;
    question.options.forEach((option,index)=>{
      const button=document.createElement('button');button.type='button';button.className='misc-practice__option';
      button.textContent=String.fromCharCode(65+index)+'. '+option;
      button.addEventListener('click',()=>answer(button,option));el.options.append(button);
    });
  }
  function feedbackLine(parent,label,value,className){
    if(!value)return;
    const line=document.createElement('p');line.className=className||'misc-practice__feedback-line';
    const strong=document.createElement('strong');strong.textContent=label+': ';
    line.append(strong,document.createTextNode(value));parent.append(line);
  }
  function familySection(doc){
    return Array.from(doc.querySelectorAll('section')).find(section=>{
      const heading=section.querySelector('h2');
      return heading&&/(family members|miembros de la familia)/i.test(clean(heading.textContent));
    });
  }
  function tableValue(doc,labels){
    const wanted=labels.map(fold);
    for(const row of Array.from(doc.querySelectorAll('tr'))){
      const heading=row.querySelector('th'),cell=row.querySelector('td');
      if(heading&&cell&&wanted.includes(fold(heading.textContent)))return readableElementText(cell);
    }
    return '';
  }
  function familyMemberNames(doc){
    const section=familySection(doc);
    if(!section)return [];
    return Array.from(section.querySelectorAll('tbody tr')).map(row=>{
      const heading=row.querySelector('th');if(!heading)return '';
      const clone=heading.cloneNode(true);clone.querySelectorAll('.formula-family__badge').forEach(badge=>badge.remove());
      return clean(clone.textContent);
    }).filter(Boolean).slice(0,6);
  }
  async function familyTeaching(item){
    if(!item.chapterUrl)return null;
    if(!familyFeedbackCache.has(item.chapterUrl)){
      familyFeedbackCache.set(item.chapterUrl,(async()=>{
        const response=await fetch(new URL(item.chapterUrl,window.location.href),{credentials:'same-origin'});
        if(!response.ok)throw new Error('family-detail');
        const doc=new DOMParser().parseFromString(await response.text(),'text/html');
        return {
          root:tableValue(doc,['Root','Raíz']),
          architecture:tableValue(doc,['Architecture','Arquitectura']),
          members:familyMemberNames(doc),
          progression:tableValue(doc,['How to choose','Cómo elegir'])
        };
      })());
    }
    return familyFeedbackCache.get(item.chapterUrl);
  }
  function teachingPanel(){
    const panel=document.createElement('div');panel.className='misc-practice__feedback-teaching';
    const heading=document.createElement('strong');heading.className='misc-practice__feedback-heading';heading.textContent=copy.distinction;
    const note=document.createElement('p');note.textContent=copy.distinctionNote;
    panel.append(heading,note);return panel;
  }
  async function enrichFamilyFeedback(panel,item,questionIndex){
    try{
      const detail=await familyTeaching(item);
      if(state.index!==questionIndex||!state.answered||!detail)return;
      feedbackLine(panel,copy.rootFormula,detail.root);
      feedbackLine(panel,copy.architecture,detail.architecture);
      if(detail.members.length)feedbackLine(panel,copy.familyMembers,detail.members.join(' → '));
      feedbackLine(panel,copy.progression,detail.progression);
    }catch(_){
      if(state.index===questionIndex&&state.answered)feedbackLine(panel,copy.why,copy.detailFailed);
    }
  }
  function answer(button,value){
    if(state.answered)return;state.answered=true;const question=state.questions[state.index];const questionIndex=state.index;const correct=value===question.correct;
    if(correct)state.score+=1;
    Array.from(el.options.children).forEach(option=>{option.disabled=true;if(option.textContent.slice(3)===question.correct)option.classList.add('is-correct');});
    if(!correct)button.classList.add('is-incorrect');
    el.feedback.hidden=false;el.feedback.classList.add(correct?'is-correct':'is-incorrect');el.feedback.replaceChildren();
    const title=document.createElement('strong');title.className='misc-practice__feedback-title';title.textContent=correct?copy.correct:copy.incorrect;el.feedback.append(title);
    if(!correct)feedbackLine(el.feedback,copy.chosen,value,'misc-practice__feedback-choice');
    feedbackLine(el.feedback,copy.answer,question.correct,'misc-practice__feedback-answer');
    if(state.resource&&state.resource.key==='families'){
      const panel=teachingPanel();el.feedback.append(panel);void enrichFamilyFeedback(panel,question.item,questionIndex);
    }else{
      feedbackLine(el.feedback,copy.why,question.explanation,'misc-practice__feedback-explanation');
    }
    el.next.hidden=false;el.next.textContent=state.index+1===state.questions.length?copy.finish:copy.next;
  }
  function startQuiz(){
    state.questions=buildQuestions().slice(0,Math.min(Number(el.size.value),state.items.length,99));
    if(state.questions.length<4){el.status.textContent=copy.failed;el.status.classList.add('is-error');el.setup.hidden=false;el.work.hidden=true;return;}
    state.index=0;state.score=0;state.startedAt=Date.now();state.attemptUid=attemptUid();
    el.setup.hidden=true;el.results.hidden=true;el.work.hidden=false;renderQuestion();el.work.scrollIntoView({behavior:'smooth',block:'start'});
  }
  async function save(){
    if(!sesskey||!state.resource)return;
    const body=new URLSearchParams({sesskey,attemptuid:state.attemptUid,examtype:'bqm_'+state.resource.key,score:String(state.score),total:String(state.questions.length),duration:String(Math.max(0,Math.round((Date.now()-state.startedAt)/1000))),language:spanish?'es':'en'});
    try{const response=await fetch('/blocks/menu/herbology-exam-history.php',{method:'POST',credentials:'same-origin',keepalive:true,headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body});const payload=await response.json();if(!response.ok||!payload.success)throw new Error('save');el.saved.textContent=copy.saved;}catch(_){el.saved.textContent=copy.saveFailed;}
  }
  function finish(){
    el.work.hidden=true;el.results.hidden=false;const percentage=state.questions.length?Math.round((state.score/state.questions.length)*100):0;
    el.score.textContent=state.score+' / '+state.questions.length+' · '+percentage+'%';el.saved.textContent='';void save();el.results.scrollIntoView({behavior:'smooth',block:'start'});
  }
  el.start.addEventListener('click',startQuiz);
  el.next.addEventListener('click',()=>{if(!state.answered)return;if(state.index+1>=state.questions.length){finish();return;}state.index+=1;renderQuestion();});
  el.back.addEventListener('click',()=>{el.setup.hidden=true;el.work.hidden=true;el.results.hidden=true;document.querySelector('[data-misc-hub]').scrollIntoView({behavior:'smooth'});});
  el.repeat.addEventListener('click',()=>selectResource(state.resource));
  if(el.language){const alternate=new URL(spanish?'miscellaneous-practice.html':'miscellaneous-practice-es.html',window.location.href);params.forEach((value,key)=>alternate.searchParams.set(key,value));el.language.href=alternate.toString();}
  renderResources();
  const initial=resources.find(resource=>resource.key===params.get('source'));if(initial)setTimeout(()=>selectResource(initial),0);
})();