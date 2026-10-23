(function(){
  'use strict';
  const items=window.CHANNEL_PATHWAY_IMAGES||[];
  const gallery=document.getElementById('gallery');
  const dialog=document.getElementById('image-dialog');

  function card(item,compact){
    if(compact){
      const article=document.createElement('article');article.className='mini-card';
      const zoom=document.createElement('button');zoom.type='button';zoom.className='mini-image-button';zoom.setAttribute('aria-label','Enlarge '+item.answer);
      const img=document.createElement('img');img.loading='lazy';img.src=item.src;img.alt=item.answer+' pathway illustration';
      const zoomLabel=document.createElement('span');zoomLabel.className='mini-zoom-label';zoomLabel.textContent='Enlarge image';zoom.append(img,zoomLabel);
      zoom.addEventListener('click',()=>openDialog(item));
      const body=document.createElement('div');const name=document.createElement('b');name.textContent=item.answer;const family=document.createElement('small');family.textContent=item.family;
      body.append(name,family);article.append(zoom,body);return article;
    }
    const article=document.createElement('article');article.className='gallery-card';article.dataset.family=item.family;
    const zoom=document.createElement('button');zoom.type='button';zoom.className='gallery-image-button';zoom.setAttribute('aria-label','Enlarge '+item.answer);
    const img=document.createElement('img');img.loading='lazy';img.src=item.src;img.alt=item.answer+' pathway illustration';zoom.append(img);
    zoom.addEventListener('click',()=>openDialog(item));
    const body=document.createElement('div');body.className='gallery-card-content';
    const family=document.createElement('span');family.className='family-label';family.textContent=item.family;
    const heading=document.createElement('h3');heading.textContent=item.answer;
    const clue=document.createElement('p');clue.textContent=item.clue;
    body.append(family,heading,clue);article.append(zoom,body);return article;
  }

  function openDialog(item){
    dialog.querySelector('img').src=item.src;dialog.querySelector('img').alt=item.answer+' pathway illustration';
    dialog.querySelector('h2').textContent=item.answer;dialog.querySelector('p').textContent=item.clue;dialog.showModal();
  }
  dialog.querySelector('.dialog-close').addEventListener('click',()=>dialog.close());
  dialog.addEventListener('click',event=>{if(event.target===dialog)dialog.close();});

  items.forEach(item=>gallery.append(card(item,false)));
  document.querySelectorAll('.mini-atlas').forEach(atlas=>{
    const families=atlas.dataset.family.split(',');
    items.filter(item=>families.includes(item.family)).forEach(item=>atlas.append(card(item,true)));
  });

  const mainContent=document.getElementById('main-content');
  ['continuing-education-plan','continuing-education-alignment','continuing-education-exam'].forEach(id=>{
    const section=document.getElementById(id);if(section)mainContent.append(section);
  });

  document.querySelectorAll('.filter').forEach(button=>button.addEventListener('click',()=>{
    document.querySelectorAll('.filter').forEach(b=>b.classList.toggle('active',b===button));
    const value=button.dataset.filter;
    gallery.querySelectorAll('.gallery-card').forEach(node=>node.hidden=value!=='All'&&node.dataset.family!==value);
  }));

  const flashFamily=document.getElementById('flashcard-family');
  const flashImage=document.getElementById('flashcard-image');
  const flashFront=document.getElementById('flashcard-front');
  const flashBack=document.getElementById('flashcard-back');
  const flashCounter=document.getElementById('flashcard-counter');
  let flashPool=[];let flashIndex=0;let flashCurrent=null;let flashRevealed=false;

  function shuffle(list){
    const copy=[...list];
    for(let index=copy.length-1;index>0;index--){const swap=Math.floor(Math.random()*(index+1));[copy[index],copy[swap]]=[copy[swap],copy[index]];}
    return copy;
  }
  function renderFlashcard(){
    if(!flashPool.length)return;
    flashCurrent=flashPool[flashIndex];flashRevealed=false;
    flashImage.src=flashCurrent.src;flashImage.alt='Unidentified channel pathway flashcard image';
    document.getElementById('flashcard-family-answer').textContent=flashCurrent.family;
    document.getElementById('flashcard-name').textContent=flashCurrent.answer;
    document.getElementById('flashcard-clue').textContent=flashCurrent.clue;
    flashFront.hidden=false;flashBack.hidden=true;
    document.getElementById('flip-flashcard').textContent='Flip card';
    flashCounter.textContent=(flashIndex+1)+' / '+flashPool.length;
  }
  function setFlashPool(randomize){
    const family=flashFamily.value;
    const selected=items.filter(item=>family==='All'||item.family===family);
    flashPool=randomize?shuffle(selected):selected;flashIndex=0;renderFlashcard();
  }
  function flipFlashcard(){
    if(!flashCurrent)return;
    flashRevealed=!flashRevealed;flashFront.hidden=flashRevealed;flashBack.hidden=!flashRevealed;
    document.getElementById('flip-flashcard').textContent=flashRevealed?'Show question':'Flip card';
  }
  flashFamily.addEventListener('change',()=>setFlashPool(false));
  document.getElementById('previous-flashcard').addEventListener('click',()=>{flashIndex=(flashIndex-1+flashPool.length)%flashPool.length;renderFlashcard();});
  document.getElementById('next-flashcard').addEventListener('click',()=>{flashIndex=(flashIndex+1)%flashPool.length;renderFlashcard();});
  document.getElementById('shuffle-flashcards').addEventListener('click',()=>setFlashPool(true));
  document.getElementById('flip-flashcard').addEventListener('click',flipFlashcard);
  document.getElementById('flashcard-image-button').addEventListener('click',()=>{if(flashCurrent)openDialog(flashCurrent);});
  setFlashPool(false);

  document.querySelectorAll('.study-nav a').forEach(link=>link.addEventListener('click',event=>{
    const target=document.querySelector(link.getAttribute('href'));
    if(!target)return;
    event.preventDefault();
    if(target.tagName==='DETAILS')target.open=true;
    document.querySelectorAll('.study-nav a').forEach(item=>item.removeAttribute('aria-current'));
    link.setAttribute('aria-current','location');
    link.closest('.nav-section').open=true;
    history.replaceState(null,'',link.getAttribute('href'));
    target.scrollIntoView({behavior:'smooth',block:'start'});
  }));

  function revealNestedDetails(target){
    let node=target;
    while(node){
      if(node.tagName==='DETAILS')node.open=true;
      node=node.parentElement;
    }
  }

  document.querySelectorAll('a[href="#school-level-activities"]').forEach(link=>link.addEventListener('click',event=>{
    const target=document.getElementById('school-level-activities');
    if(!target)return;
    event.preventDefault();
    revealNestedDetails(target);
    history.replaceState(null,'','#school-level-activities');
    requestAnimationFrame(()=>target.scrollIntoView({behavior:'smooth',block:'start'}));
  }));

  if(location.hash==='#school-level-activities'){
    const target=document.getElementById('school-level-activities');
    if(target){
      revealNestedDetails(target);
      requestAnimationFrame(()=>target.scrollIntoView({block:'start'}));
    }
  }

  const familySelect=document.getElementById('practice-family');
  const practiceImage=document.getElementById('practice-image');
  const answerPanel=document.getElementById('answer-panel');
  let current=null;
  function chooseCard(){
    const family=familySelect.value;const pool=items.filter(item=>family==='All'||item.family===family);
    if(!pool.length)return;
    const alternatives=pool.length>1?pool.filter(item=>!current||item.id!==current.id):pool;
    current=alternatives[Math.floor(Math.random()*alternatives.length)];
    practiceImage.src=current.src;practiceImage.alt='Unidentified channel pathway practice image';answerPanel.hidden=true;
    document.getElementById('reveal-answer').hidden=false;
  }
  document.getElementById('new-card').addEventListener('click',chooseCard);familySelect.addEventListener('change',chooseCard);
  document.getElementById('reveal-answer').addEventListener('click',event=>{
    document.getElementById('answer-family').textContent=current.family;
    document.getElementById('answer-name').textContent=current.answer;
    document.getElementById('answer-clue').textContent=current.clue;
    answerPanel.hidden=false;event.currentTarget.hidden=true;
  });
  chooseCard();

  const notes=document.getElementById('notes');const status=document.getElementById('notes-status');
  try{notes.value=localStorage.getItem('channel-pathway-student-notes')||'';}catch(error){status.textContent='Notes storage is unavailable in this browser.';}
  document.getElementById('save-notes').addEventListener('click',()=>{
    try{localStorage.setItem('channel-pathway-student-notes',notes.value);status.textContent='Notes saved on this device.';}
    catch(error){status.textContent='Unable to save notes on this device. Copy your text before leaving.';}
  });
})();
