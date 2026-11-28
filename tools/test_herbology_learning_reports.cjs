const assert=require('node:assert/strict');
const {percent,comparable}=require('./herbology-learning-report.js');
assert.equal(percent({correct:3,total:4}),75);
assert.equal(percent({correct:0,total:0}),null);
const h=[{id:'c',completed:3,group:'a',overall:{correct:8,total:10},topics:{}},
{id:'a',completed:1,group:'a',overall:{correct:4,total:10},topics:{dui_yao:{correct:1,total:2}}},
{id:'b',completed:2,group:'b',overall:{correct:9,total:10},topics:{dui_yao:{correct:3,total:4}}}];
assert.deepEqual(comparable(h,{group:'a'}).map(p=>p.id),['a','c']);
assert.deepEqual(comparable(h,{group:'a'},'dui_yao').map(p=>p.id),['a']);
assert.deepEqual(comparable(h,{group:'a'},'dui_yao','all').map(p=>p.id),['a','b']);
assert.equal(h[0].id,'c'); // Never mutate history order or fabricate missing-topic zeroes.
// Exercise the report renderer with a minimal DOM and the real client code.
const fs=require('node:fs'),vm=require('node:vm');
class E{
 constructor(tag){this.tagName=tag.toUpperCase();this.children=[];this.dataset={};this.style={};this.events={};this.attributes={};this.hidden=false;this.textContent='';this.value='';}
 append(...nodes){for(const n of nodes){this.children.push(n);n.parent=this;if(this.tagName==='SELECT'&&!this.hasOption){this.value=n.value;this.hasOption=true;}}}
 replaceChildren(...nodes){this.children=[];this.append(...nodes);}
 setAttribute(k,v){this.attributes[k]=v;}
 addEventListener(k,fn){this.events[k]=fn;}
 all(){return [this,...this.children.flatMap(n=>n.all())];}
}
async function test(lang){
 const head=new E('head'),main=new E('main');
 const doc={readyState:'complete',head,body:main,createElement:t=>new E(t),createElementNS:(ns,t)=>new E(t),querySelector:s=>s==='[data-hlr-root]'?main.all().find(n=>n.dataset.hlrRoot):s==='.herb-board'?main:null};
 const report={schemaVersion:1,id:'current',completed:20,number:1,version:2,mode:'linear',group:'a',overall:{correct:87,wrong:12,unanswered:1,total:100},
 topics:{dui_yao:{correct:14,wrong:4,unanswered:0,total:18},precautions:{correct:3,wrong:2,unanswered:0,total:5},processing:{correct:0,wrong:1,unanswered:0,total:1},modifications:{correct:0,wrong:5,unanswered:0,total:5}},
 domains:{dui_yao:{correct:14,wrong:4,unanswered:0,total:18}},
 items:[{position:1,prompt:'<script>Never execute</script>',topics:['dui_yao'],options:[{label:'A',text:'Answer A'},{label:'B',text:'Answer B'}],correct:'B',selected:'A',state:'wrong',explanation:'Saved feedback'}]};
 const history=[{...report,items:undefined}];
 const ctx={document:doc,console,URLSearchParams,Date,window:null,fetch:async(url,opts)=>({ok:true,json:async()=>new URLSearchParams(opts.body).get('action')==='history'?{success:true,history}:{success:true,report,history}})};ctx.window=ctx;
 vm.createContext(ctx);vm.runInContext(fs.readFileSync(__dirname+'/herbology-learning-report.js','utf8'),ctx);
 ctx.HerbologyLearning.init({lang,sesskey:'key',url:'/private',active:false,topics:{dui_yao:['Dui Yao','Dui Yao'],precautions:['Precauciones','Precautions'],processing:['Procesamiento','Processing'],modifications:['Modificaciones','Modifications']}});
 await new Promise(r=>setImmediate(r));
 const open=main.all().find(n=>n.tagName==='BUTTON');assert(open);
 open.events.click();await new Promise(r=>setImmediate(r));
 let nodes=main.all();const text=nodes.map(n=>n.textContent).join(' ');
 assert(text.includes('4')&&text.includes('18')&&text.includes(lang==='es'?'Has fallado':'You missed'));
 assert(nodes.some(n=>n.tagName==='SVG'));assert(nodes.some(n=>n.tagName==='TABLE'));
 assert(nodes.some(n=>n.textContent.includes(lang==='es'?'primer punto':'first comparable point')));
 assert(nodes.some(n=>n.textContent==='<script>Never execute</script>')); // text nodes, never HTML injection.
 const topicSelect=nodes.find(n=>n.tagName==='SELECT'&&n.attributes['aria-label']===(lang==='es'?'Tema de la curva':'Learning curve topic'));
 topicSelect.value='dui_yao';topicSelect.events.change();
 assert(main.all().some(n=>n.textContent==='14/18'));
}

async function active(){
 const head=new E('head'),main=new E('main'),exam=new E('form'),result=new E('output');result.hidden=true;main.append(exam);exam.append(result);
 exam.insertAdjacentElement=(where,node)=>main.append(node);
 const nodes=Array.from({length:100},(_,i)=>({querySelector:()=>({value:i===0?'A':'B'})}));
 exam.querySelector=()=>result;exam.querySelectorAll=()=>nodes;
 const observers=[],calls=[];
 const doc={readyState:'complete',head,body:main,createElement:t=>new E(t),createElementNS:(ns,t)=>new E(t),querySelector:s=>s==='[data-hlr-root]'?main.all().find(n=>n.dataset.hlrRoot):s.startsWith('[data-simulation-exam]')?exam:s==='.herb-board'?main:null};
 const report={id:'saved',completed:20,number:1,version:2,mode:'linear',group:'g',lang:'es',overall:{correct:99,wrong:1,unanswered:0,total:100},topics:{dui_yao:{correct:17,wrong:1,unanswered:0,total:18}},domains:{dui_yao:{correct:17,wrong:1,unanswered:0,total:18}},items:[]};
 const ctx={document:doc,console,URLSearchParams,Date,window:null,MutationObserver:class{constructor(fn){observers.push(fn);}observe(){}},fetch:async(url,opts)=>{const body=new URLSearchParams(opts.body);calls.push(Object.fromEntries(body));return {ok:true,json:async()=>body.get('action')==='begin'?{success:true,token:'second-token'}:{success:true,report,history:[report]}}}};ctx.window=ctx;
 vm.createContext(ctx);vm.runInContext(fs.readFileSync(__dirname+'/herbology-learning-report.js','utf8'),ctx);
 ctx.HerbologyLearning.init({lang:'es',sesskey:'key',url:'/private',active:true,token:'first-token',topics:{dui_yao:['Dui Yao','Dui Yao']},items:nodes.map((n,i)=>({id:'Q-'+i}))});
 assert.equal(calls.length,0); // No grading request before completion.
 result.hidden=false;observers[1]();await new Promise(r=>setImmediate(r));
 assert.equal(calls[0].action,'submit');assert.equal(calls[0].token,'first-token');
 const answers=JSON.parse(calls[0].answers);assert.equal(answers.length,100);assert.equal(answers[0].id,'Q-0');assert.equal(answers[0].answer,'A');assert.equal(answers[0].position,1);
 assert(main.all().some(n=>n.textContent==='Informe guardado en tu cuenta.'));
 result.hidden=true;observers[1]();result.hidden=false;observers[1]();await new Promise(r=>setImmediate(r));
 assert.equal(calls[1].action,'begin');assert.equal(calls[2].action,'submit');assert.equal(calls[2].token,'second-token');
}
(async()=>{await test('es');await test('en');await active();console.log('PASS: comparable curves, no invented zeroes, EN/ES report rendering, topic filters, tables/SVG and safe text rendering');})().catch(e=>{console.error(e);process.exit(1);});
