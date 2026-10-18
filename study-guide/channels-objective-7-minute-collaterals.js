(() => {
  'use strict';
  const root=document.getElementById('channels-objective-7');if(!root)return;
  const el=(tag,text,parent,cls)=>{const n=document.createElement(tag);if(text)n.textContent=text;if(cls)n.className=cls;if(parent)parent.append(n);return n};
  const reset=(selector,title)=>{const n=root.querySelector(selector);if(!n)return null;n.replaceChildren();el('h4',title,n);return n};

  const intro=reset('.objective-introduction','Introduction and Understanding the Objective');
  el('p','The Minute Collaterals are the progressively finer branches of the channel and collateral network. Their essential role is distribution: they allow qi and blood carried by larger pathways to reach every part of the body.',intro);
  const prompts=el('div','',intro,'minute-prompts');
  el('p','What will you study? The limited classical description, the possible observational origin of the concept and the distribution function of progressively finer collateral branches.',prompts);
  el('p','What will you learn to do? Place the Minute Collaterals within the channel hierarchy and distinguish a source-supported function from a historical possibility.',prompts);
  const board=el('details','',intro,'board-connection-objective');el('summary','How does this connect to the Board?',board);el('p','Focus on the hierarchy and function: larger channels and collaterals branch into increasingly minute pathways so qi and blood can reach all regions of the body.',board);

  const dev=reset('.objective-development','Explore the Topic');
  const priority=el('div','',dev,'minute-source-priority');el('strong','Board Review source priority',priority);el('span','Deadman is the principal source. Chinese Acupuncture and Moxibustion (CAM), Maciocia and Handbook of Oriental Medicine will be used in that order only when they add relevant clarification without changing the core statement.',priority);
  el('h5','What the source establishes',dev);
  const established=el('ul','',dev);[
    'Classical texts say little about the Minute Collaterals.',
    'Larger channels and collaterals cannot by themselves account for distribution to every small region of the body.',
    'They therefore branch into progressively finer pathways.',
    'The Minute Collaterals serve the function of carrying qi and blood into the smallest reaches of the network.'
  ].forEach(x=>el('li',x,established));
  const evidence=el('div','',dev,'minute-evidence');el('strong','Evidence distinction',evidence);el('span','Deadman presents the idea that the Minute Collaterals may have derived their identity from observation of small superficial blood vessels as a possibility, not as a confirmed historical fact. The distribution function is the clearer and more testable point.',evidence);
  el('h5','A branching network, not a separate major route',dev);
  el('p','Minute Collaterals are best understood as terminal ramifications within the wider channel-and-collateral system. Their identity depends on increasing subdivision and distribution rather than on one named longitudinal pathway, a new zangfu pairing or an independent series of acupuncture points.',dev);
  const comparison=el('div','',dev,'minute-comparison');
  [['Primary Channels','Major organized pathways and the main reference network.'],['Luo-Connecting Channels','Named collateral pathways that reinforce connections and have defined origins.'],['Minute Collaterals','Ever-finer ramifications that extend qi and blood throughout the body.']].forEach(([name,text])=>{const a=el('article','',comparison);el('h6',name,a);el('p',text,a)});
  el('p','Core reference: Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Minute Collaterals.”',dev,'source-note');

  const visual=reset('.objective-visual','Visual Learning');
  el('h5','From major pathway to tissue-level distribution',visual);
  el('p','Read the diagram from left to right. Each stage divides into smaller branches, increasing the area that the network can supply.',visual);
  const flow=el('div','',visual,'minute-flow');
  [['Major channels','Large organized routes'],['Collaterals','Connecting branches'],['Minute collaterals','Progressively finer ramifications'],['Whole body','Qi and blood reach every region']].forEach(([name,text],i)=>{const a=el('article','',flow);el('strong',name,a);el('span',text,a);if(i<3)el('b','→',flow,'minute-arrow')});
  const reading=el('div','',visual,'minute-reading');el('strong','How to interpret it',reading);el('span','The arrows represent increasing subdivision, not four separate circulatory systems. “Minute” describes the small scale of the ramifications, while “collateral” places them within the branching network.',reading);

  const cards=reset('.objective-flashcards','Flashcards — Guided Recall');
  [
    ['How extensively are the Minute Collaterals described in classical texts?','Only briefly; relatively little is said about them.'],
    ['What may have contributed to the concept of Minute Collaterals?','Possibly the observation of small blood vessels at the body surface. This is presented as a possibility, not a confirmed origin.'],
    ['Why must larger channels and collaterals continue to branch?','So qi and blood can reach every part of the body.'],
    ['What is the principal function of the Minute Collaterals?','To provide the finest level of distribution within the channel-and-collateral network.'],
    ['Does “minute” mean clinically unimportant?','No. It refers to increasingly small ramifications, not to an unimportant function.'],
    ['Are Minute Collaterals presented as one separate named pathway?','No. They are progressively finer branches of the larger network.'],
    ['Which statement is firmer: their exact historical origin or their distribution function?','Their distribution function is clearer; the superficial-vessel origin is only proposed as a possibility.'],
    ['What is the key sequence for remembering their place in the system?','Larger channels and collaterals branch into increasingly minute pathways, extending qi and blood throughout the body.']
  ].forEach(([q,a],i)=>{const d=el('details','',cards,'minute-card');el('summary',`Flashcard ${i+1} — ${q}`,d);el('p',a,d)});

  const test=reset('.objective-self-test','Self-Test — Practice without score');
  const questions=[
    ['What best defines the Minute Collaterals?',['Progressively finer branches of the channel and collateral network','A ninth Extraordinary Vessel','A separate group of zangfu organs','The twelve independently numbered Sinew pathways'],0,'They are fine ramifications that extend the larger network. The other choices substitute unrelated systems or invent an independent structure.'],
    ['Why are Minute Collaterals necessary?',['They allow qi and blood to reach every part of the body.','They replace all Primary Channels.','They create new acupuncture points inside the organs.','They prevent any branching of the Luo network.'],0,'Their central function is fine distribution. They extend rather than replace the larger network and are not defined by new internal points or by preventing branches.'],
    ['How should the proposed relationship to superficial blood vessels be stated?',['It is a possible observational origin.','It is a proven historical fact.','It means Minute Collaterals are identical to modern veins.','It disproves the distribution function.'],0,'Deadman presents this as a possibility. The source does not establish it as historical proof or equate the traditional concept one-to-one with modern vessels.']
  ];
  questions.forEach(([q,opts,correct,feedback],i)=>{const fs=el('fieldset','',test);el('legend',`${i+1}. ${q}`,fs);opts.forEach((o,j)=>{const l=el('label','',fs);const input=el('input','',l);input.type='radio';input.name=`minute-q-${i}`;input.value=j;el('span',o,l)});const b=el('button','Check answer and explanation',fs);b.type='button';const out=el('div','',fs,'minute-answer');out.hidden=true;b.onclick=()=>{const chosen=fs.querySelector('input:checked');out.hidden=false;out.innerHTML=`<strong>${chosen&&+chosen.value===correct?'Correct.':'Correct answer: '+opts[correct]}</strong><p>${feedback}</p>`}});

  const ce=reset('.objective-continuing-education','In-depth Reading — Continuing Education');
  el('h5','From named routes to network architecture',ce);el('p','The Minute Collaterals shift attention away from memorizing one named trajectory and toward understanding a distributed network. As branches become smaller and more numerous, the system can conceptually account for regions lying beyond the course of a major channel.',ce);
  el('h5','Observation and interpretation',ce);el('p','The suggestion that the concept may have arisen from observing small superficial vessels is historically plausible within the source but remains qualified. It should not be converted into a claim that Minute Collaterals are anatomically identical to capillaries, veins or any single modern tissue.',ce);
  el('h5','Clinical reasoning boundary',ce);el('p','Visible superficial vessels or color changes may carry meaning within traditional assessment, but they are not stand-alone diagnoses. New, painful, inflamed or otherwise concerning vascular changes require appropriate biomedical evaluation.',ce);
  el('p','Core reference: Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Minute Collaterals.”',ce,'source-note');

  const instructor=reset('.objective-instructor-resource','Instructor Resource — Mock 10');el('p','The Word-ready Mock 10 will be prepared after the objective content and distractors are reviewed.',instructor);
  const exam=reset('.objective-exam','Exam with score');el('p','The scored exam link will be added when it is supplied for this objective.',exam);
  const style=el('style','',document.head);style.textContent=`#channels-objective-7 .minute-prompts,#channels-objective-7 .minute-source-priority,#channels-objective-7 .minute-evidence,#channels-objective-7 .minute-reading{margin:14px 0;padding:13px 15px;border-left:5px solid #087774;border-radius:9px;background:#edf7f6}#channels-objective-7 .minute-evidence{border-left-color:#ef7e2d;background:#fff8ef}#channels-objective-7 .minute-source-priority strong,#channels-objective-7 .minute-evidence strong,#channels-objective-7 .minute-reading strong{display:block;margin-bottom:5px}.minute-comparison{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:14px 0}.minute-comparison article{padding:14px;border:1px solid #bfd5d3;border-radius:10px;background:#f8fbfb}.minute-comparison h6{margin:0 0 7px;color:#075f60;font-size:1rem}.minute-comparison p{margin:0}.minute-flow{display:flex;align-items:stretch;gap:10px;margin:16px 0}.minute-flow article{display:flex;flex:1;min-width:0;flex-direction:column;justify-content:center;padding:15px;border:1px solid #b8d9d6;border-radius:11px;background:#f7fcfb;text-align:center}.minute-flow article strong{color:#075f60}.minute-flow article span{margin-top:5px;font-size:.88rem}.minute-arrow{align-self:center;color:#ef7e2d;font-size:1.7rem}.minute-card{margin:10px 0;padding:11px 14px;border:1px solid #bfd5d3;border-radius:9px;background:#f8fbfb}#channels-objective-7 fieldset{margin:14px 0;padding:14px;border:1px solid #bfd5d3;border-radius:10px}#channels-objective-7 fieldset label{display:flex;gap:8px;padding:6px}.minute-answer{margin:10px 0;padding:12px 14px;border-left:4px solid #087774;background:#edf7f6}@media(max-width:760px){.minute-comparison{grid-template-columns:1fr}.minute-flow{flex-direction:column}.minute-arrow{transform:rotate(90deg)}}`;
})();
