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
    saveFailed: spanish ? 'No se pudo guardar el resultado.' : 'The result could not be saved.'
  };
  const el = {
    list: document.querySelector('[data-misc-list]'),
    setup: document.querySelector('[data-misc-setup]'),
    title: document.querySelector('[data-misc-title]'),
    status: document.querySelector('[data-misc-status]'),
    size: document.querySelector('[data-misc-size]'),
    start: document.querySelector('[data-misc-start]'),
    work: document.querySelector('[data-misc-work]'),
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

  function shuffle(values){const result=values.slice();for(let i=result.length-1;i>0;i-=1){const j=Math.floor(Math.random()*(i+1));const value=result[i];result[i]=result[j];result[j]=value;}return result;}
  function clean(value){return String(value||'').replace(/\s+/g,' ').trim();}
  function unique(values){return Array.from(new Set(values.map(clean).filter(Boolean)));}
  function choices(correct,pool){return shuffle([correct].concat(shuffle(unique(pool).filter(value=>value!==correct)).slice(0,3)));}
  function attemptUid(){if(globalThis.crypto&&typeof globalThis.crypto.randomUUID==='function')return globalThis.crypto.randomUUID();return 'misc-'+Date.now()+'-'+Math.random().toString(36).slice(2);}
  function translatedPath(resource){
    if(resource.dynamic){
      const url=new URL(resource.path,window.location.origin);
      if(spanish)url.searchParams.set('lang','es');
      return url.toString();
    }
    return spanish?resource.path.replace(/\.html$/,'-es.html'):resource.path;
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
      const preferred=node.querySelector('p,.comparison-card__category,table,dl');
      let detail=clean(preferred&&preferred.textContent);
      if(!detail){
        const clone=node.cloneNode(true);
        clone.querySelectorAll('a,button,script,style').forEach(child=>child.remove());
        detail=clean(clone.textContent).replace(title,'').trim();
      }
      if(detail.length<18)return;
      items.push({title,detail:detail.slice(0,420)});
    });
    const seen=new Set();
    return items.filter(item=>{const key=item.title.toLocaleLowerCase();if(seen.has(key))return false;seen.add(key);return true;});
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
    state.resource=resource;state.items=[];el.title.textContent=spanish?resource.es:resource.en;
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
    const titles=state.items.map(item=>item.title);
    return shuffle(state.items.map(item=>({prompt:copy.prompt,context:item.detail,correct:item.title,options:choices(item.title,titles)})));
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
  function answer(button,value){
    if(state.answered)return;state.answered=true;const question=state.questions[state.index];const correct=value===question.correct;
    if(correct)state.score+=1;
    Array.from(el.options.children).forEach(option=>{option.disabled=true;if(option.textContent.slice(3)===question.correct)option.classList.add('is-correct');});
    if(!correct)button.classList.add('is-incorrect');
    el.feedback.hidden=false;el.feedback.classList.add(correct?'is-correct':'is-incorrect');
    el.feedback.textContent=(correct?copy.correct:copy.incorrect)+' '+copy.answer+': '+question.correct;
    el.next.hidden=false;el.next.textContent=state.index+1===state.questions.length?copy.finish:copy.next;
  }
  function startQuiz(){
    state.questions=buildQuestions().slice(0,Math.min(Number(el.size.value),state.items.length,99));
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