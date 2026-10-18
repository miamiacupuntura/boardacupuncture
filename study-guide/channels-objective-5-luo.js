(() => {
  'use strict';
  const root=document.getElementById('channels-objective-5');
  if(!root||root.dataset.luoBuilt)return;root.dataset.luoBuilt='true';
  const el=(tag,text,parent,cls)=>{const n=document.createElement(tag);if(text)n.textContent=text;if(cls)n.className=cls;if(parent)parent.append(n);return n};
  const reset=(selector,title)=>{const n=root.querySelector(selector);if(!n)return null;n.replaceChildren();el('h4',title,n);return n};

  const intro=reset('.objective-introduction','Introduction to: The Luo-Connecting Channels');
  el('p','What will you study? The organization, pathways and principal function of the fifteen Luo-Connecting Channels.',intro);
  el('p','What will you learn to do? Distinguish a Luo-Connecting Channel from its Luo-Connecting point, identify the fifteen principal Luo pathways and explain how they reinforce internally–externally paired channels and zangfu.',intro);
  const distinction=el('div','',intro,'luo-essential-distinction');distinction.innerHTML='<strong>Essential distinction</strong><span>A <b>Luo-Connecting point</b> is the named point from which a Luo pathway branches. A <b>Luo-Connecting Channel</b> is the superficial connecting pathway that leaves from that point and travels toward its paired channel or another described region.</span>';
  const board=el('details','',intro,'board-connection-objective');el('summary','How does this connect to the Board?',board);el('p','Board-style questions may ask you to count the principal Luo channels, match a Luo point with its pathway destination, distinguish the twelve Primary-Channel Luo from the three additional Luo, or identify the principal function of strengthening interior–exterior relationships. Read the stem carefully: a question about a point category is not automatically asking for the entire Luo pathway.',board);

  const dev=reset('.objective-development','Understanding the Objective');
  el('h5','1. Organization of the principal Luo network',dev);
  const count=el('div','',dev,'luo-count-map');
  [['12','From the Twelve Primary Channels'],['2','Ren Mai and Du Mai'],['1','Great Luo of the Spleen'],['15','Principal Luo-Connecting Channels']].forEach(([n,t])=>{const a=el('article','',count);el('b',n,a);el('span',t,a)});
  el('p','The Luo-Connecting Channels branch from Primary Channels and Extraordinary Vessels and are distributed relatively superficially over the body.',dev);
  const note=el('div','',dev,'luo-source-caution');note.innerHTML='<strong>Source qualification:</strong> the <em>Essential Questions</em> also mentions a Stomach Great Luo, sometimes described as a sixteenth Luo-Connecting Channel. The standard count taught here remains fifteen principal Luo-Connecting Channels.';

  el('h5','2. General pathway pattern',dev);
  const pattern=el('ol','',dev,'luo-pattern');['A Luo pathway branches from the Luo-Connecting point of its own Primary Channel.','It connects with the internally–externally paired channel.','After making that connection, it usually continues along its own described pathway.'].forEach(x=>el('li',x,pattern));

  el('h5','3. Luo pathways of the Twelve Primary Channels',dev);
  const table=el('table','',dev,'luo-pathway-table');table.innerHTML='<thead><tr><th>Primary Channel</th><th>Luo-Connecting point</th><th>Source-described continuation</th></tr></thead><tbody><tr><td>Lung</td><td>Lieque LU-7</td><td>Palm and thenar eminence</td></tr><tr><td>Large Intestine</td><td>Pianli LI-6</td><td>Jaw, teeth and ear</td></tr><tr><td>Stomach</td><td>Fenglong ST-40</td><td>Nape, head and throat</td></tr><tr><td>Spleen</td><td>Gongsun SP-4</td><td>Abdomen, Stomach and intestines</td></tr><tr><td>Heart</td><td>Tongli HT-5</td><td>Heart, root of the tongue and eye</td></tr><tr><td>Small Intestine</td><td>Zhizheng SI-7</td><td>Shoulder</td></tr><tr><td>Bladder</td><td>Feiyang BL-58</td><td>Kidney Channel</td></tr><tr><td>Kidney</td><td>Dazhong KID-4</td><td>Perineum and lumbar vertebrae</td></tr><tr><td>Pericardium</td><td>Neiguan PC-6</td><td>Pericardium and Heart</td></tr><tr><td>Sanjiao</td><td>Waiguan SJ-5</td><td>Meets the Pericardium Channel at the chest</td></tr><tr><td>Gall Bladder</td><td>Guangming GB-37</td><td>Dorsum of the foot</td></tr><tr><td>Liver</td><td>Ligou LIV-5</td><td>Genitals</td></tr></tbody>';

  el('h5','4. The three additional principal Luo pathways',dev);
  const extra=el('div','',dev,'luo-extra-grid');
  [['Ren Mai — Conception Vessel','Jiuwei REN-15','Spreads over the abdomen.'],['Du Mai — Governing Vessel','Changqiang DU-1','Travels up both sides of the spine to the top of the head; at the scapular region it joins the Bladder Channel and threads through the spine.'],['Great Luo of the Spleen','Dabao SP-21','Spreads through the chest and lateral costal region.']].forEach(([name,point,path])=>{const a=el('article','',extra);el('h6',name,a);el('b',point,a);el('p',path,a)});

  el('h5','5. Principal function',dev);el('p','The Luo-Connecting Channels strengthen the connection between internally–externally paired channels and their related zangfu. Their pathways add a superficial connecting network to relationships already expressed by the Primary and Divergent Channels.',dev);
  el('p','The actions of individual Luo-Connecting points belong to the Point Categories chapter. This objective focuses on the Luo pathways and their organization.',dev,'luo-boundary');

  const cards=reset('.objective-flashcards','Flashcards — Guided Recall');
  const cardData=[
    ['How many principal Luo-Connecting Channels are there?','Fifteen.'],
    ['How is the standard total of fifteen formed?','Twelve from the Primary Channels, one from Ren Mai, one from Du Mai and the Great Luo of the Spleen.'],
    ['Where does a Primary-Channel Luo pathway begin?','At the Luo-Connecting point of its own channel.'],
    ['What relationship do the Luo pathways principally strengthen?','The relationship between internally–externally paired channels and zangfu.'],
    ['Which Lung point gives rise to its Luo pathway?','Lieque LU-7; the pathway continues toward the palm and thenar eminence.'],
    ['Which Luo travels toward the jaw, teeth and ear?','The Large Intestine Luo from Pianli LI-6.'],
    ['Which Luo reaches the Heart, root of the tongue and eye?','The Heart Luo from Tongli HT-5.'],
    ['Which Luo spreads over the abdomen?','The Ren Mai Luo from Jiuwei REN-15.'],
    ['Which Luo spreads through the chest and lateral costal region?','The Great Luo of the Spleen from Dabao SP-21.'],
    ['Why is a “sixteenth Luo” sometimes mentioned?','The Essential Questions mentions a Stomach Great Luo; this is a qualified additional reference, while the principal standard count remains fifteen.']
  ];
  const cardGrid=el('div','',cards,'luo-flip-grid');cardData.forEach(([q,a],i)=>{const d=el('details','',cardGrid,'luo-flip-card');el('summary',`Flashcard ${i+1} — ${q}`,d);el('p',a,d)});

  const test=reset('.objective-self-test','Self-Test — Practice without score');
  el('p','Use these activities to distinguish counts, points and pathway destinations. Reveal the explanation only after choosing an answer.',test);
  el('h5','1. Multiple Choice',test);
  const questions=[
    ['Which combination produces the standard count of fifteen principal Luo-Connecting Channels?',['Twelve Primary-Channel Luo, Ren Luo, Du Luo and the Great Luo of the Spleen','Twelve Primary-Channel Luo plus three Divergent Channels','Eight Extraordinary Vessels plus seven Primary Channels','Twelve Primary Channels plus Ren, Du and Chong'],0,'The standard fifteen are twelve Primary-Channel Luo plus Ren, Du and the Great Luo of the Spleen. Chong is not substituted for the Great Luo of the Spleen.'],
    ['Which pathway begins at Jiuwei REN-15?',['The Ren Mai Luo','The Du Mai Luo','The Great Luo of the Spleen','The Stomach Luo'],0,'The Ren Mai Luo begins at REN-15 and spreads over the abdomen.'],
    ['Which statement best describes the principal Luo function?',['They strengthen connections between internally–externally paired channels and zangfu.','They contain every acupuncture point of the Primary Channels.','They replace the internal pathways of the Primary Channels.','They form the six confluences of the Divergent Channels.'],0,'The Luo system reinforces paired relationships. It does not replace Primary or Divergent pathways and does not contain all channel points.']
  ];
  questions.forEach(([q,opts,correct,feedback],i)=>{const fs=el('fieldset','',test);el('legend',`${i+1}. ${q}`,fs);opts.forEach((o,j)=>{const label=el('label','',fs);const input=el('input','',label);input.type='radio';input.name=`luo-q-${i}`;input.value=j;el('span',o,label)});const b=el('button','Check answer and explanation',fs);b.type='button';const out=el('div','',fs,'luo-answer');out.hidden=true;b.onclick=()=>{const selected=fs.querySelector('input:checked');out.hidden=false;out.innerHTML=`<strong>${selected&&+selected.value===correct?'Correct.':'Correct answer: '+opts[correct]}</strong><p>${feedback}</p>`}});
  el('h5','2. Matching — Point to pathway destination',test);el('p','Match each Luo-Connecting point with its source-described destination. The answer key can be revealed even when some rows are unanswered.',test);
  const pairs=[['LU-7','Palm and thenar eminence'],['LI-6','Jaw, teeth and ear'],['ST-40','Nape, head and throat'],['SP-4','Abdomen, Stomach and intestines'],['HT-5','Heart, root of tongue and eye'],['LIV-5','Genitals']];
  const options=pairs.map(x=>x[1]);const matching=el('div','',test,'luo-matching');pairs.forEach(([point])=>{const label=el('label','',matching);el('span',point,label);const select=el('select','',label);['Choose a destination',...options].forEach(x=>{const o=el('option',x,select);o.value=x==='Choose a destination'?'':x})});const show=el('button','Show correct relationships',test);show.type='button';const key=el('div','',test,'luo-answer');key.hidden=true;show.onclick=()=>{key.hidden=false;key.innerHTML='<strong>Correct relationships</strong><ul>'+pairs.map(([a,b])=>`<li>${a} → ${b}</li>`).join('')+'</ul>'};

  const ce=reset('.objective-continuing-education','In-depth Reading — Continuing Education');
  el('h5','Superficial does not mean unimportant',ce);el('p','The Luo network is described as relatively superficial, yet it reinforces relationships among paired channels and zangfu. Relative depth identifies its place in the channel system; it does not measure clinical importance.',ce);
  el('h5','Point category versus pathway',ce);el('p','A Luo-Connecting point has point-specific actions and indications. The Luo-Connecting Channel is the pathway arising from that point. Keeping the two questions separate prevents memorizing a point list as though it were a complete pathway map.',ce);
  el('h5','The qualified sixteenth Luo',ce);el('p','The Stomach Great Luo is presented as an additional classical reference, not as a replacement in the standard list of fifteen. When an assessment asks for the principal Luo count, answer fifteen unless the question specifically names the Essential Questions passage.',ce);
  el('h5','A three-layer teaching framework',ce);el('p','In Jeffrey C. Yuen’s continuing-education text, the channel systems are compared through three functional levels: Wei, Ying and Yuan. The Sinew Channels are associated with Wei and the exterior, the Luo vessels with Ying and the interior, and the Eight Extraordinary Vessels with Yuan and constitutional influence. The Divergent Channels are presented as communicating between the exterior and constitutional levels, while the Primary Channels remain the main reference for exterior–interior relationships. This is a pedagogical framework used in this source, not a universally standardized anatomical classification.',ce);
  el('h5','Transverse and longitudinal: useful labels with a history',ce);el('p','Yuen notes that “Transverse Luo” and “Longitudinal Luo” are terms adopted in European teaching to distinguish two study perspectives. Transverse Luo emphasizes connection within an internally–externally paired Yin–Yang relationship and is commonly discussed through Source–Luo pairing. Longitudinal Luo emphasizes the trajectory arising from the Luo point and the signs associated with that pathway. The text cautions that Chinese sources may describe the longitudinal route as a subpathway rather than use the same terminology.',ce);
  el('h5','Traditional relationship to Ying, Blood and visible signs',ce);el('p','The course text relates the Luo system to Ying and Blood and discusses visible superficial vessels or discoloration within traditional fullness and stasis models; emptiness may be less externally visible. These observations are clues within a complete traditional pattern assessment, not stand-alone diagnoses. Normal vascular variation, injury and possible biomedical pathology require appropriate evaluation, and Luo terminology should not be equated one-to-one with veins, arteries or modern hematologic disease.',ce);
  el('h5','From theory to clinical organization',ce);el('p','Yuen later organizes discussion of Luo applications under cardiovascular, musculoskeletal or traumatic, and gastrointestinal themes. For this objective, those categories are most useful as a map of traditional reasoning: first identify the Luo pathway and its paired-channel relationship, then place the finding within the complete pattern. They should not be treated as an independent treatment protocol or used outside the practitioner’s training and scope of practice.',ce);
  el('p','Core reference: Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Luo-Connecting Channels.”',ce,'source-note');
  el('p','Supplementary continuing-education reference: Jeffrey C. Yuen, Channel Systems of Chinese Medicine: Luo Vessels, New England School of Acupuncture Continuing Education Department, 2004—especially “Role Within the Meridian System,” “General Features of the Luo Passage Channels,” and the discussion of Transverse and Longitudinal Luo.',ce,'source-note');
  ce.remove();

  const instructor=reset('.objective-instructor-resource','Instructor Resource — Mock 10');el('p','Download the Word-ready Mock 10 with ten multiple-choice questions, marked answers and complete feedback for the correct response and all three distractors.',instructor);const download=el('a','Download The Luo-Connecting Channels (Mock 10)',instructor,'luo-mock-download');download.href='/downloads/Luo_Connecting_Channels_Mock_10.docx?v=1';download.target='_blank';download.rel='noopener';download.setAttribute('download','');
  const exam=reset('.objective-exam','Exam with score');el('p','Choose when to take this scored exam: after studying this objective, or after completing the reading for all objectives in the chapter. The exam opens in another tab.',exam,'primary-exam-guidance');const examLink=el('a','Luo Connecting Channels (Mock 10)',exam,'primary-exam-link');examLink.href='https://www.acupuncturetests.com/mod/scorm/view.php?id=9349';examLink.target='_blank';examLink.rel='noopener noreferrer';

  const style=el('style','',document.head);style.textContent=`
    #channels-objective-5 table{width:100%;border-collapse:collapse;margin:14px 0}#channels-objective-5 th,#channels-objective-5 td{padding:10px;border:1px solid #bfd5d3;text-align:left;vertical-align:top}
    .luo-essential-distinction,.luo-source-caution,.luo-boundary{margin:14px 0;padding:12px 14px;border-left:4px solid #ef7e2d;border-radius:8px;background:#fff8ef}.luo-essential-distinction strong{display:block;margin-bottom:5px}
    .luo-count-map{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin:14px 0}.luo-count-map article{padding:14px;border:1px solid #bad5d3;border-radius:10px;background:#f5fbfa;text-align:center}.luo-count-map b{display:block;color:#087774;font-size:1.8rem}.luo-count-map span{display:block;margin-top:5px}
    .luo-pattern{padding-left:24px}.luo-pattern li{margin:8px 0}.luo-extra-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:14px 0}.luo-extra-grid article{padding:14px;border:1px solid #bfd5d3;border-radius:10px;background:#f7fbfb}.luo-extra-grid h6{margin:0 0 8px;font-size:1rem;color:#124852}
    .luo-flip-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.luo-flip-card{padding:11px 14px;border:1px solid #bfd5d3;border-radius:9px;background:#f8fbfb}
    #channels-objective-5 fieldset{margin:14px 0;padding:14px;border:1px solid #bfd5d3;border-radius:10px}#channels-objective-5 fieldset label{display:flex;gap:8px;padding:6px}.luo-answer{margin:10px 0;padding:12px 14px;border-left:4px solid #087774;background:#edf7f6}.luo-matching label{display:grid;grid-template-columns:110px 1fr;gap:10px;margin:8px 0}.luo-matching select{min-height:40px}
    .luo-mock-download{display:inline-block;margin-top:10px;padding:13px 17px;border-radius:10px;background:#ff7f17;color:#111;text-decoration:none;font-weight:800}.luo-mock-download:hover,.luo-mock-download:focus-visible{background:#ef6f08}
    @media(max-width:760px){.luo-count-map{grid-template-columns:repeat(2,1fr)}.luo-extra-grid,.luo-flip-grid{grid-template-columns:1fr}.luo-matching label{grid-template-columns:1fr}.luo-mock-download{display:flex;min-height:48px;align-items:center;justify-content:center;text-align:center}}
  `;
})();
