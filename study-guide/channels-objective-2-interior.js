(() => {
  'use strict';
  const root=document.getElementById('channels-objective-2');
  if(!root||root.dataset.interiorPathways==='complete')return;
  root.dataset.interiorPathways='complete';
  const make=(tag,text,parent,className)=>{const node=document.createElement(tag);if(text)node.textContent=text;if(className)node.className=className;if(parent)parent.append(node);return node;};

  const development=root.querySelector('.objective-development');
  const circuitHeading=[...development.querySelectorAll(':scope > h5')].find(node=>node.textContent.startsWith('3. Three complete'));
  if(development&&circuitHeading){
    const section=document.createDocumentFragment();
    make('h5','2A. Why the interior pathways matter',section);
    make('p','Because acupuncture points lie on the external pathway, study often emphasizes the visible surface route. The source argues that the deeper course is essential for understanding why points may affect regions or functions that are not obvious from the superficial line alone.',section);
    const examples=make('div','',section,'interior-pathway-examples');
    [
      ['Lung Channel → Middle Jiao','The Lung Channel originates in the Middle Jiao, in the region of the Stomach. This deep relationship helps explain why Lung Channel points may be used for nausea and vomiting.'],
      ['Stomach Channel → Du Vessel → Brain','The Stomach Channel meets the Governing Vessel at DU24 Shenting and DU26 Renzhong. Because the Du Vessel enters the brain, the source uses this connection to help explain actions of Stomach Channel points on disorders of the spirit.'],
      ['Large Intestine Channel → Gall Bladder Channel','The internal pathway of the Large Intestine Channel meets the Gall Bladder Channel at GB14 Yangbai, GB5 Xuanlu and GB6 Xuanli. This helps explain the affinity of LI4 Hegu for both frontal and temporal head pain.'],
      ['Pericardium Channel → Middle Jiao','The Pericardium Channel descends through the Middle Jiao. This relationship helps explain why PC6 Neiguan may treat nausea and vomiting.']
    ].forEach(([heading,text])=>{const card=make('article','',examples);make('h6',heading,card);make('p',text,card);});
    const map=make('div','',section,'interior-connections-map');
    map.innerHTML='<div><b>Surface point or channel</b><span>LU points · ST points · LI4 · PC6</span></div><span class="map-arrow">→</span><div><b>Interior connection</b><span>Middle Jiao · Du Vessel · GB Channel · Middle Jiao</span></div><span class="map-arrow">→</span><div><b>Traditional explanatory link</b><span>Digestive symptoms · spirit · head pain · nausea/vomiting</span></div>';
    make('p','Interpretive limit: these connections help explain traditional actions and indications; they do not mean that every point on a channel has the same effect or that the interior pathway alone determines treatment.',section,'interior-pathway-limit');
    circuitHeading.before(section);
  }

  const flash=root.querySelector('.objective-flashcards');
  if(flash){
    const card=make('details','',flash,'primary-flashcard');make('summary','Flashcard 11 — Why must the interior pathways be studied?',card);make('p','They reveal deep zangfu, vessel and channel connections that help explain actions and indications not evident from the superficial pathway alone.',card);
    const card2=make('details','',flash,'primary-flashcard');make('summary','Flashcard 12 — Which two examples connect a Primary Channel with the Middle Jiao?',card2);make('p','The Lung Channel originates in the Middle Jiao, and the Pericardium Channel descends through the Middle Jiao.',card2);
  }

  const test=root.querySelector('.objective-self-test');
  if(test){
    make('h5','6. Matching — Interior pathway connections',test);
    make('p','Match the channel or point cue with the deep connection used by the source to explain its traditional actions.',test);
    const rows=[['Lung Channel','Originates in the Middle Jiao near the Stomach'],['Stomach Channel','Meets DU24 and DU26'],['LI4 Hegu','LI internal pathway meets GB14, GB5 and GB6'],['PC6 Neiguan','Pericardium pathway descends through the Middle Jiao']];
    const task=make('div','',test,'interior-matching-task');
    const choices=['Choose a connection',...rows.map(row=>row[1])];
    rows.forEach(([cue,answer])=>{const label=make('label','',task);make('span',cue,label);const select=make('select','',label);select.dataset.answer=answer;choices.forEach(value=>{const option=make('option',value,select);option.value=value==='Choose a connection'?'':value;});});
    const button=make('button','Show correct interior connections',test);button.type='button';const result=make('div','',test,'primary-answer-box');button.onclick=()=>{result.innerHTML='<strong>Correct connections</strong><ul>'+rows.map(([cue,answer])=>`<li>${cue}: ${answer}</li>`).join('')+'</ul><p>These are explanatory relationships from the cited source, not exclusive prescriptions.</p>';};
  }

  const continuing=root.querySelector('.objective-continuing-education');
  if(continuing){
    const reference=continuing.querySelector('.source-note');
    const heading=make('h5','From pathway knowledge to point indications');
    const paragraph=make('p','The examples demonstrate a recurring method of traditional reasoning: follow the complete channel—including its internal course and meetings—to interpret why a distal point may affect the digestive system, head or spirit. This complements individual point actions; it does not replace them.');
    if(reference)reference.before(heading,paragraph);else continuing.append(heading,paragraph);
  }

  const style=make('style','',document.head);
  style.textContent='.interior-pathway-examples{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin:14px 0}.interior-pathway-examples article{padding:14px;border:1px solid #bfd5d3;border-radius:10px;background:#f7fbfb}.interior-pathway-examples h6{margin:0 0 8px;color:#075f60;font-size:1rem}.interior-connections-map{display:grid;grid-template-columns:1fr auto 1fr auto 1fr;gap:9px;align-items:center;margin:16px 0}.interior-connections-map>div{display:flex;min-height:98px;flex-direction:column;justify-content:center;gap:7px;padding:12px;text-align:center;border:1px solid #bfd5d3;border-radius:10px;background:#edf7f6}.interior-connections-map .map-arrow{font-size:1.4rem;color:#ad531b}.interior-pathway-limit{padding:12px 14px;border-left:4px solid #ef7e2d;background:#fff8ef;border-radius:8px}.interior-matching-task label{display:grid;grid-template-columns:minmax(170px,1fr) minmax(260px,1.5fr);gap:12px;align-items:center;margin:9px 0}.interior-matching-task select{min-height:42px}@media(max-width:760px){.interior-pathway-examples{grid-template-columns:1fr}.interior-connections-map{grid-template-columns:1fr}.interior-connections-map .map-arrow{transform:rotate(90deg);text-align:center}.interior-matching-task label{grid-template-columns:1fr}}';
})();
