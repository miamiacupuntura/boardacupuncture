(() => {
  'use strict';
  const root=document.getElementById('channels-objective-2');
  if(!root||root.dataset.sixPairing==='complete')return;
  root.dataset.sixPairing='complete';
  const make=(tag,text,parent,className)=>{const node=document.createElement(tag);if(text)node.textContent=text;if(className)node.className=className;if(parent)parent.append(node);return node;};

  const development=root.querySelector('.objective-development');
  const interiorHeading=[...development.querySelectorAll(':scope > h5')].find(node=>node.textContent.startsWith('5. Interior'));
  if(interiorHeading){
    const section=document.createDocumentFragment();
    make('h5','5. Six-channel pairing — matching Hand and Foot channels',section);
    make('p','The second pairing uses shared Yin–Yang identity and comparable anatomical position rather than pairing a zang with its related fu. A Hand channel joins a Foot channel with the same identity: Taiyin, Yangming, Shaoyin, Taiyang, Jueyin or Shaoyang. Each pair is often discussed as one of the “six channels.”',section);
    const sixTable=make('table','',section,'six-channel-table');
    sixTable.innerHTML='<caption>Six-channel pairs and corresponding limb positions</caption><thead><tr><th>Six-channel identity</th><th>Hand channel</th><th>Arm position</th><th>Foot channel</th><th>Leg position</th></tr></thead><tbody><tr><th>Taiyin</th><td>Lung</td><td>Anterior inside</td><td>Spleen</td><td>Anterior inside</td></tr><tr><th>Yangming</th><td>Large Intestine</td><td>Anterior outside</td><td>Stomach</td><td>Anterior outside</td></tr><tr><th>Jueyin</th><td>Pericardium</td><td>Middle inside</td><td>Liver</td><td>Middle inside</td></tr><tr><th>Shaoyang</th><td>Sanjiao</td><td>Middle outside</td><td>Gall Bladder</td><td>Middle outside</td></tr><tr><th>Shaoyin</th><td>Heart</td><td>Posterior inside</td><td>Kidney</td><td>Posterior inside</td></tr><tr><th>Taiyang</th><td>Small Intestine</td><td>Posterior outside</td><td>Bladder</td><td>Posterior outside</td></tr></tbody>';
    const distinction=make('div','',section,'pairing-distinction');
    distinction.innerHTML='<article><strong>Interior–exterior pairing</strong><span>Yin ↔ Yang · same limb · related zang and fu</span><small>Example: Lung ↔ Large Intestine</small></article><article><strong>Six-channel pairing</strong><span>Yin ↔ Yin or Yang ↔ Yang · Hand ↔ Foot · same identity and comparable position</span><small>Example: Hand Yangming LI ↔ Foot Yangming ST</small></article>';
    make('p','Why it matters: classical descriptions may refer to Yangming, Shaoyang or another identity as one channel while meaning both its Hand and Foot members. “Harmonise Shaoyang,” for example, refers to the Sanjiao–Gall Bladder pairing.',section,'primary-example');
    make('h5','6. How the two pairing systems form each circuit',section);
    make('p','Within each circuit, the regional handoffs alternate between the two pairing systems: a Hand Yin channel passes to its interior–exterior Hand Yang partner; at the face/head that channel passes to its Foot Yang six-channel partner; at the foot the Foot Yang channel passes to its interior–exterior Foot Yin partner.',section);
    const flow=make('div','',section,'paired-circuit-flow');
    flow.innerHTML='<div><b>Hand Yin</b><small>interior–exterior handoff</small></div><span>→</span><div><b>Hand Yang</b><small>six-channel handoff</small></div><span>→</span><div><b>Foot Yang</b><small>interior–exterior handoff</small></div><span>→</span><div><b>Foot Yin</b><small>returns toward chest</small></div>';
    make('p','Deadman describes each individual circuit from its Yin channel on the chest through these four stages. Across all twelve channels, however, the three circuits connect into a continuous circulation; “begins on the chest” is therefore the conventional starting point for describing each circuit, not an isolated absolute origin.',section,'circuit-clarification');
    interiorHeading.textContent='7. Interior–exterior pairings';
    interiorHeading.before(section);
  }

  const flash=root.querySelector('.objective-flashcards');
  if(flash){
    const card1=make('details','',flash,'primary-flashcard');make('summary','Flashcard 9 — How does six-channel pairing differ from interior–exterior pairing?',card1);make('p','Six-channel pairing joins Hand and Foot channels with the same Yin–Yang identity and comparable limb position. Interior–exterior pairing joins a Yin and Yang channel on the same limb and their related zangfu.',card1);
    const card2=make('details','',flash,'primary-flashcard');make('summary','Flashcard 10 — Which channels form the Yangming pair?',card2);make('p','The Hand Yangming Large Intestine Channel and Foot Yangming Stomach Channel.',card2);
  }

  const test=root.querySelector('.objective-self-test');
  if(test){
    const heading=make('h5','5. Pairing Comparison',test);
    make('p','Choose the type of relationship represented by each example, then reveal the completed comparison.',test);
    const task=make('div','',test,'pairing-comparison-task');
    const rows=[['Lung ↔ Large Intestine','Interior–exterior'],['Large Intestine ↔ Stomach','Six-channel'],['Pericardium ↔ Sanjiao','Interior–exterior'],['Pericardium ↔ Liver','Six-channel']];
    rows.forEach(([cue,answer])=>{const label=make('label','',task);make('span',cue,label);const select=make('select','',label);select.dataset.answer=answer;['Choose pairing','Interior–exterior','Six-channel'].forEach(value=>{const option=make('option',value,select);option.value=value==='Choose pairing'?'':value;});});
    const button=make('button','Show correct pairings',test);button.type='button';const result=make('div','',test,'primary-answer-box');button.onclick=()=>{result.innerHTML='<strong>Correct comparison</strong><ul>'+rows.map(([cue,answer])=>`<li>${cue}: ${answer}</li>`).join('')+'</ul><p>Ask two questions: Are the channels Yin and Yang on the same limb? Or do they share the same Taiyin/Yangming/etc. identity across Hand and Foot?</p>';};
  }

  const style=make('style','',document.head);
  style.textContent='.pairing-distinction{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin:14px 0}.pairing-distinction article{display:flex;flex-direction:column;gap:7px;padding:14px;border:1px solid #bcd6d3;border-radius:10px;background:#f2f9f8}.pairing-distinction small{color:#4d6670}.paired-circuit-flow{display:grid;grid-template-columns:1fr auto 1fr auto 1fr auto 1fr;gap:8px;align-items:center;margin:14px 0}.paired-circuit-flow div{display:flex;min-height:86px;flex-direction:column;justify-content:center;gap:6px;padding:10px;text-align:center;border:1px solid #bcd6d3;border-radius:10px;background:#edf7f6}.paired-circuit-flow>span{font-size:1.35rem;color:#ad531b}.circuit-clarification{padding:12px 14px;border-left:4px solid #087774;background:#edf7f6;border-radius:8px}.pairing-comparison-task label{display:grid;grid-template-columns:minmax(180px,1fr) minmax(220px,1fr);gap:12px;align-items:center;margin:9px 0}.pairing-comparison-task select{min-height:42px}@media(max-width:760px){.pairing-distinction{grid-template-columns:1fr}.paired-circuit-flow{grid-template-columns:1fr}.paired-circuit-flow>span{transform:rotate(90deg);text-align:center}.pairing-comparison-task label{grid-template-columns:1fr}}';
})();
