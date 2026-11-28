const assert=require('node:assert/strict'),fs=require('node:fs'),vm=require('node:vm');
const code=fs.readFileSync(__dirname+'/herbology-fast-exam-chooser.js','utf8');
function fixture(track='simulation',active=false){
 const param=track==='simulation'?'simulation':'examset';const base='https://school.test/blocks/menu/herbology-board.php';
 const events={},wevents={},pushes=[];let scrolls=0,fetches=0;
 const location={href:base+'?track='+track};
 function link(kind,href){return {kind,href,attributes:{},target:'',hasAttribute(){return false;},setAttribute(k,v){this.attributes[k]=v;},removeAttribute(k){delete this.attributes[k];},closest(selector){return (this.kind==='tab'&&selector.includes('.herb-board__simulation-exam-switch'))||(this.kind==='card'&&selector.includes('.exam-audit-card'))||(this.kind==='mode'&&selector.includes('.herb-board__simulation-modes'))?this:null;}};}
 const tabs=Array.from({length:7},(_,i)=>link('tab',base+`?track=${track}&${param}=${i+1}`));
 const cards=Array.from({length:7},(_,i)=>link('card',base+`?track=${track}&${param}=${i+1}`));
 const modes=['linear','adaptive','officialadaptive'].map(mode=>link('mode',base+`?track=${track}&exam=${mode}&${param}=1`));
 const clinical=link('mode',base+'?track=clinicalcases');modes.push(clinical);
 const language=link('language',base+`?track=${track}&lang=es`);
 const heading={textContent:'Exam 1'},minimum={textContent:'Exam 1:'};
 const nav={dataset:{},querySelectorAll:()=>tabs,insertAdjacentElement(where,node){this.status=node;}};
 const doc={readyState:'complete',documentElement:{lang:'en'},createElement:()=>({setAttribute(){},hidden:true,textContent:''}),
  querySelector:s=>s.startsWith('[data-simulation-exam]')?(active?{}:null):s==='.herb-board__simulation-exam-switch'?nav:s==='.herb-board__simulation-modes,.herb-board__exam-models'?{scrollIntoView(){scrolls++;}}:null,
  getElementById:()=>heading,
  querySelectorAll:s=>s==='.herb-board__exam-minimum strong'?[minimum]:s==='a[href]'?[...tabs,...cards,...modes,language]:s.startsWith('.herb-board__simulation-exam-switch')?[...tabs,...cards]:s.startsWith('.herb-board__simulation-modes')?modes:[],
  addEventListener:(name,fn)=>events[name]=fn};
 const win={location,history:{pushState(state,title,url){pushes.push(url);location.href=url;}},addEventListener:(name,fn)=>wevents[name]=fn};
 vm.runInNewContext(code,{document:doc,window:win,URL,Set,Number,fetch(){fetches++;throw new Error('Chooser must not fetch');}});
 function click(target,extra={}){const e={button:0,target,defaultPrevented:false,preventDefault(){this.defaultPrevented=true;},...extra};events.click(e);return e;}
 return {param,base,tabs,cards,modes,language,heading,minimum,nav,events,wevents,win,pushes,click,get scrolls(){return scrolls;},get fetches(){return fetches;}};
}
for(const track of ['simulation','exams']){
 const f=fixture(track);
 assert(f.events.click);const e=f.click(f.tabs[5]);assert(e.defaultPrevented);
 assert.equal(new URL(f.win.location.href).searchParams.get(f.param),'6');
 assert.equal(f.tabs[5].attributes['aria-current'],'page');assert.equal(f.tabs[0].attributes['aria-current'],undefined);
 for(const mode of f.modes.slice(0,3)){assert.equal(new URL(mode.href).searchParams.get(f.param),'6');assert(new URL(mode.href).searchParams.get('exam'));}
 assert.equal(new URL(f.language.href).searchParams.get(f.param),'6');assert.equal(f.modes[3].href,f.base+'?track=clinicalcases');
 const before=f.pushes.length;const ctrl=f.click(f.tabs[2],{ctrlKey:true});assert(!ctrl.defaultPrevented);assert.equal(f.pushes.length,before);
 f.win.location.href=f.base+'?track='+track;f.wevents.popstate();assert.equal(f.tabs[0].attributes['aria-current'],'page');
 f.click(f.cards[6]);assert.equal(f.scrolls,1);assert.equal(new URL(f.modes[0].href).searchParams.get(f.param),'7');
 assert.equal(f.fetches,0);
}
assert.equal(fixture('simulation',true).events.click,undefined);
console.log('PASS: instantaneous selection, correct Begin URLs for all modes, Back, language links, modified clicks, no requests and no active-exam interference');
