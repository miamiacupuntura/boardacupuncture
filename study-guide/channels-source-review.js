(function(){
  const objective=document.getElementById('sgv2-objective-1');
  if(!objective)return;
  const el=(tag,text,parent,cls)=>{
    const node=document.createElement(tag);
    if(text)node.textContent=text;
    if(cls)node.className=cls;
    if(parent)parent.append(node);
    return node;
  };
  const findCard=title=>[...objective.querySelectorAll('.sgv2-resource')]
    .find(node=>node.querySelector(':scope > h4')?.textContent===title);

  const intro=findCard('Introduction and Understanding the Objective');
  if(intro&&!intro.querySelector('.source-evidence')){
    const source=el('section','',intro,'source-evidence');
    el('h5','What does the Chapter source actually say?',source);
    el('p','Chinese Acupuncture and Moxibustion describes an “interminable circulation of yin and yang,” so the cycle has no absolute starting region. Its quotation from the Miraculous Pivot gives the general directions as Hand Yin: chest → hand; Hand Yang: hand → head; Foot Yang: head → foot; and Foot Yin: foot → abdomen. The same discussion then states that the Hand Yin and Foot Yin groups meet in the chest.',source);
    const wording=el('details','',source);
    el('summary','Why do some memory maps say Feet → Chest?',wording);
    el('p','“Feet → Chest” compresses the ascending Foot Yin pathway and its thoracic connection into one memory label. It should not replace the source wording “foot → abdomen,” nor imply that the cycle begins or ends in the chest.',wording);
    el('p','Reference: Cheng Xinnong, Chinese Acupuncture and Moxibustion, Chapter 5, “Distribution of the Fourteen Meridians” and “Cyclical Flow of Qi in the Twelve Regular Meridians,” pp. 54–59 in the supplied text.',source);
  }

  const visual=findCard('Visual Learning');
  if(visual&&!visual.querySelector('.source-anatomy-note')){
    const note=el('details','',visual,'source-anatomy-note');
    el('summary','How does the source support the anatomical orientation?',note);
    el('p','The source places Yin channels mainly on the medial limbs and Yang channels mainly on the lateral limbs. On the lower limb it distinguishes Foot Yangming at the anterior border, Foot Shaoyang laterally and Foot Taiyang posteriorly. It describes the three Foot Yin channels along the medial lower limb, with their relative positions changing along the course.',note);
  }

  const ce=objective.querySelector('.objective-continuing-education');
  if(ce&&!ce.querySelector('.source-reading-extension')){
    const extension=el('section','',ce,'source-reading-extension');
    el('h5','Source-reading distinction: pathway, connection and mnemonic',extension);
    el('p','A directional mnemonic is not a literal transcript of every channel course. The text separately describes the fixed superficial course, internal organ relationships, regional connections and cyclic order. Clinical or examination reasoning should identify which relationship the question is asking about before choosing an answer.',extension);
  }

  const style=el('style','',document.head);
  style.textContent='.source-evidence,.source-anatomy-note{margin-top:14px;padding:14px;border-left:4px solid #bb741d;border-radius:8px;background:#fff9ef}.source-evidence h5{margin-top:0}.source-reading-extension{margin-top:16px;padding-top:12px;border-top:1px solid #c8dddd}';
})();
