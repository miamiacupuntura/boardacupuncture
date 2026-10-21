(() => {
 const normalizeFlashcards = () => {
  const makeCard = (source, label, question, questionExtras, answerNodes) => {
    if (!source || source.dataset.standardFlashcard === 'true') return;
    const card = document.createElement('article');
    card.className = 'study-flashcard';
    card.dataset.standardFlashcard = 'true';
    const badge = document.createElement('p');badge.className='study-flashcard-label';badge.textContent=label || 'Flashcard';
    const heading = document.createElement('h5');heading.className='study-flashcard-question';heading.textContent=question || 'Review question';
    const prompt = document.createElement('div');prompt.className='study-flashcard-prompt';
    (questionExtras || []).forEach(node => prompt.append(node.cloneNode(true)));
    const button = document.createElement('button');button.type='button';button.className='study-flashcard-toggle';button.textContent='Show Answer';button.setAttribute('aria-expanded','false');
    const answer = document.createElement('div');answer.className='study-flashcard-answer';answer.hidden=true;
    (answerNodes || []).forEach(node => answer.append(node.cloneNode(true)));
    if (!answer.childNodes.length) { const p=document.createElement('p');p.textContent='Answer not available.';answer.append(p); }
    const answerTitle=document.createElement('strong');answerTitle.className='study-flashcard-answer-title';answerTitle.textContent='Answer';answer.prepend(answerTitle);
    const answerId=`study-flashcard-answer-${Math.random().toString(36).slice(2,10)}`;answer.id=answerId;button.setAttribute('aria-controls',answerId);
    button.addEventListener('click',()=>{const show=answer.hidden;answer.hidden=!show;button.textContent=show?'Hide Answer':'Show Answer';button.setAttribute('aria-expanded',String(show));});
    card.append(badge,heading);if(prompt.childNodes.length)card.append(prompt);card.append(button,answer);
    source.replaceWith(card);
    const parent=card.parentElement;if(parent)parent.classList.add('study-flashcard-grid');
    return card;
  };

  if (!window.createStudyFlashcard) window.createStudyFlashcard = ({number, question, options=[], answer, explanation='', reference=''}) => {
    const mount=document.createElement('span');
    const extras=[];
    if(options.length){const list=document.createElement('ol');list.type='A';options.forEach(option=>{const item=document.createElement('li');item.textContent=option;list.append(item)});extras.push(list)}
    const line=(label,text)=>{const p=document.createElement('p');const strong=document.createElement('strong');strong.textContent=label+' ';p.append(strong,document.createTextNode(text));return p};
    const answerNodes=[line('Correct answer:',answer)];
    if(explanation)answerNodes.push(line('Why is this correct?',explanation));
    if(reference)answerNodes.push(line('Reference:',reference));
    const holder=document.createElement('div');holder.append(mount);makeCard(mount,`Flashcard ${number}`,question,extras,answerNodes);return holder.firstElementChild;
  };

  document.querySelectorAll('.mc-flashcard, .he-sea-flashcard').forEach(source=>{
    const front=source.querySelector('.mc-flashcard-face:not(.mc-flashcard-back),.he-sea-flashcard-front');
    const back=source.querySelector('.mc-flashcard-back,.he-sea-flashcard-back');if(!front||!back)return;
    const label=front.querySelector('h4,h5,.flashcard-number')?.textContent.trim();
    const q=front.querySelector('h4,h5')?.nextElementSibling;
    const question=q?.textContent.trim()||front.querySelector('p')?.textContent.trim();
    const extras=[...front.children].filter(n=>!n.matches('h4,h5,button')&&n!==q);
    const answers=[...back.children].filter(n=>!n.matches('button'));
    makeCard(source,label,question,extras,answers);
  });

  const flipSelectors='.primary-flipcard,.divergent-flipcard,.extraordinary-flipcard,.sinew-flipcard,.minute-flipcard,.cutaneous-flipcard';
  document.querySelectorAll(flipSelectors).forEach(source=>{
    const front=source.querySelector('[class*="-front"]');const back=source.querySelector('[class*="-back"]');if(!front||!back)return;
    const label=front.querySelector('[class*="badge"]')?.textContent.trim();const q=front.querySelector('h5,h4');
    const answers=[...back.children].filter(n=>!n.matches('button,[class*="badge"],h4,h5'));
    makeCard(source,label,q?.textContent.trim(),[],answers);
  });

  document.querySelectorAll('.channel-function-flashcard').forEach(source=>{
    const label=source.querySelector('.flashcard-number-badge')?.textContent.trim();const q=source.querySelector('h5,h4');const box=source.querySelector('.flashcard-revealed-answer');
    const answers=box?[...box.children].filter(n=>!n.matches('button,strong')):[];makeCard(source,label,q?.textContent.trim(),[],answers);
  });

  document.querySelectorAll('details.chapter-flashcard').forEach(source=>{
    const summary=source.querySelector(':scope>summary');const raw=summary?.childNodes[0]?.textContent.trim()||summary?.textContent.trim()||'';
    const match=raw.match(/^(Flashcard\s+\d+)\s*[—–-]\s*(.*)$/i);const answers=[...source.children].filter(n=>n!==summary);
    makeCard(source,match?.[1]||'Flashcard',match?.[2]||raw,[],answers);
  });
 };

 const applyHierarchy = () => {
  normalizeFlashcards();
  const layout=[...document.querySelectorAll('.sgv2-layout')].find(candidate=>candidate.getClientRects().length&&getComputedStyle(candidate).display!=='none');
  const chooser=document.querySelector('.chapter-selector');
  if(layout&&window.matchMedia('(min-width:1151px)').matches){
    layout.style.setProperty('width','calc(100% - 32px)','important');
    layout.style.setProperty('max-width','1200px','important');
    layout.style.setProperty('margin-left','auto','important');
    layout.style.setProperty('margin-right','auto','important');
    layout.style.setProperty('grid-template-columns','240px minmax(0, 1fr) 230px','important');
    layout.style.setProperty('column-gap','24px','important');
  }
  if(layout&&chooser&&!layout.querySelector(':scope>.chapter-navigation-rail')){
    const rail=document.createElement('aside');rail.className='chapter-navigation-rail';rail.setAttribute('aria-label','Chapter navigation');
    const title=document.createElement('h3');title.textContent='Chapters';
    const intro=document.createElement('p');intro.textContent='Move between the available study-guide chapters.';intro.className='chapter-navigation-intro';
    const list=document.createElement('nav');list.className='chapter-navigation-list';
    const sourceButtons=[...chooser.querySelectorAll('button')];
    const sync=()=>{const active=sourceButtons.findIndex(button=>button.getAttribute('aria-pressed')==='true');[...list.children].forEach((button,index)=>{button.classList.toggle('is-current',index===active);button.setAttribute('aria-current',index===active?'page':'false')})};
    sourceButtons.forEach((source,index)=>{const button=document.createElement('button');button.type='button';button.className='chapter-navigation-link';button.textContent=source.textContent.trim();button.addEventListener('click',()=>{source.click();setTimeout(()=>{sync();window.scrollTo({top:chooser.offsetTop,behavior:'smooth'})},50)});list.append(button)});
    const observer=new MutationObserver(sync);sourceButtons.forEach(button=>observer.observe(button,{attributes:true,attributeFilter:['aria-pressed']}));
    rail.append(title,intro,list);layout.append(rail);sync();
  }
  const objectives = 'details.sgv2-objective, details[id^="channels-objective-"], details[id^="pc-objective-"]';

  document.querySelectorAll(objectives).forEach(objective => {
    objective.classList.add('chapter-primary-block');
    [...objective.children].forEach(child => {
      if (child.matches('section.sgv2-resource, article.sgv2-resource')) child.classList.add('chapter-content-section');
      if (!child.matches('details.sgv2-resource')) return;
      child.open = true;
      child.classList.add('chapter-static-section');
      const summary = child.querySelector(':scope > summary');
      if (summary) {
        summary.setAttribute('role', 'heading');
        summary.setAttribute('aria-level', '4');
        summary.addEventListener('click', event => event.preventDefault());
      }
      child.addEventListener('toggle', () => { if (!child.open) child.open = true; });
    });
    objective.querySelectorAll('details:not(.chapter-static-section)').forEach(control => control.classList.add('chapter-interactive-control'));

    objective.querySelectorAll('details').forEach(card => {
      const parentSection = card.closest('section, article');
      const sectionTitle = parentSection?.querySelector(':scope > h3, :scope > h4, :scope > h5')?.textContent || '';
      const identity = `${card.className} ${sectionTitle}`;
      if (!/flash\s*card|flashcard|flip-card|flipcard/i.test(identity)) return;
      card.classList.add('chapter-flashcard');
      const summary = card.querySelector(':scope > summary');
      if (!summary || summary.querySelector('.flashcard-action-label')) return;
      const cue = document.createElement('span');
      cue.className = 'flashcard-action-label';
      cue.setAttribute('aria-hidden', 'true');
      cue.textContent = 'Show answer';
      summary.append(cue);
      card.addEventListener('toggle', () => { cue.textContent = card.open ? 'Hide answer' : 'Show answer'; });
    });
  });

  if (document.getElementById('chapter-content-hierarchy-style')) return;
  const style = document.createElement('style');
  style.id = 'chapter-content-hierarchy-style';
  style.textContent = `
    .chapter-primary-block > summary{font-weight:800}
    .chapter-content-section{position:relative;margin:18px 16px!important;padding:20px!important;border:1px solid #c9dcda!important;border-left:4px solid #087774!important;border-radius:10px!important;background:#fff!important;box-shadow:none!important}
    .chapter-content-section>h3:first-child,.chapter-content-section>h4:first-child,.chapter-content-section>h5:first-child{margin:0 0 14px!important;padding:0 0 10px!important;border-bottom:1px solid #d9e7e5;background:transparent!important;color:#124f60!important}
    .chapter-static-section{display:block;margin:18px 16px!important;padding:0 20px 20px!important;border:1px solid #c9dcda!important;border-left:4px solid #087774!important;border-radius:10px!important;background:#fff!important;box-shadow:none!important}
    .chapter-static-section>summary{display:block!important;margin:0!important;padding:17px 0 11px!important;border:0!important;border-bottom:1px solid #d9e7e5!important;border-radius:0!important;background:transparent!important;color:#124f60!important;cursor:default!important;font-weight:800!important;list-style:none!important}
    .chapter-static-section>summary::-webkit-details-marker,.chapter-static-section>summary::marker{display:none!important}.chapter-static-section>summary::before,.chapter-static-section>summary::after{content:none!important}
    .chapter-primary-block .chapter-interactive-control:not(.board-connection-objective){margin:10px 0;border:1px solid #d8e1e0;border-left:3px solid #df7a2a;border-radius:7px;background:#fffaf4;box-shadow:none}
    .chapter-primary-block .chapter-interactive-control:not(.board-connection-objective)>summary{padding:10px 12px!important;border:0!important;border-radius:6px!important;background:transparent!important;color:#334e59!important;font-size:.95rem!important;font-weight:700!important}
    .chapter-primary-block details.chapter-flashcard{min-height:132px!important;margin:0!important;padding:0!important;overflow:hidden;border:1px solid #bed5d3!important;border-top:4px solid #087774!important;border-left:1px solid #bed5d3!important;border-radius:11px!important;background:#f8fcfb!important;box-shadow:0 4px 12px rgba(15,70,78,.06)!important}
    .chapter-primary-block details.chapter-flashcard>summary{display:flex!important;min-height:128px!important;box-sizing:border-box;flex-direction:column;align-items:flex-start;justify-content:space-between;gap:14px;padding:18px!important;border:0!important;background:transparent!important;color:#173e4d!important;cursor:pointer!important;font-size:1rem!important;font-weight:800!important;line-height:1.45!important;list-style:none!important}
    .chapter-primary-block details.chapter-flashcard>summary::-webkit-details-marker,.chapter-primary-block details.chapter-flashcard>summary::marker{display:none!important}
    .chapter-primary-block details.chapter-flashcard>summary::before,.chapter-primary-block details.chapter-flashcard>summary::after{content:none!important}
    .chapter-primary-block details.chapter-flashcard[open]{border-top-color:#df7a2a!important;background:#fffaf4!important}
    .chapter-primary-block details.chapter-flashcard[open]>summary{min-height:auto!important;padding-bottom:12px!important;border-bottom:1px solid #ead9c9!important}
    .chapter-primary-block details.chapter-flashcard>summary+.flashcard-answer,.chapter-primary-block details.chapter-flashcard>summary~p,.chapter-primary-block details.chapter-flashcard>summary~div{margin:0!important;padding:16px 18px 18px!important;color:#263f49!important;line-height:1.6!important}
    .flashcard-action-label{display:inline-flex;align-items:center;min-height:30px;padding:4px 10px;border:1px solid #087774;border-radius:999px;background:#fff;color:#076661;font-size:.78rem;font-weight:800;line-height:1}
    details.chapter-flashcard[open] .flashcard-action-label{border-color:#c45f19;color:#9a4813}
    .study-flashcard-grid{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:16px!important;align-items:start!important}
    .study-flashcard{display:flex;min-width:0;box-sizing:border-box;flex-direction:column;padding:20px;border:1px solid #b8d9d6;border-top:5px solid #087774;border-radius:13px;background:#f7fcfb;box-shadow:0 6px 16px rgba(11,64,74,.07)}
    .study-flashcard-label{align-self:flex-start;margin:0 0 15px!important;padding:5px 10px;border-radius:999px;background:#e2f2ee;color:#075f60;font-size:.78rem;font-weight:800;letter-spacing:.02em}
    .study-flashcard-question{margin:0 0 14px!important;color:#173e4d;font-size:1.03rem;line-height:1.5}
    .study-flashcard-prompt{margin:0 0 15px;color:#314d59;line-height:1.55}
    .study-flashcard-prompt ol,.study-flashcard-prompt ul{margin:8px 0;padding-left:26px}.study-flashcard-prompt li{margin:7px 0}
    .study-flashcard-toggle{align-self:flex-start;min-height:42px;margin-top:auto;padding:9px 16px;border:0;border-radius:8px;background:#087774;color:#fff;font-weight:800;cursor:pointer}
    .study-flashcard-toggle:hover,.study-flashcard-toggle:focus-visible{background:#056461}.study-flashcard-toggle:focus-visible{outline:3px solid #1769c2;outline-offset:3px}
    .study-flashcard-answer{margin-top:16px;padding:15px 16px;border-left:4px solid #df7a2a;border-radius:8px;background:#fff7ef;color:#263f49;line-height:1.6}
    .study-flashcard-answer[hidden]{display:none!important}.study-flashcard-answer-title{display:block;margin-bottom:9px;color:#984611}
    .study-flashcard-answer p:first-of-type{margin-top:0}.study-flashcard-answer p:last-child{margin-bottom:0}.study-flashcard-answer ul,.study-flashcard-answer ol{padding-left:24px}
    @media(min-width:1151px){.sgv2-layout{width:calc(100% - 32px)!important;max-width:1200px!important;margin-left:auto!important;margin-right:auto!important;grid-template-columns:240px minmax(0,1fr) 230px!important;column-gap:24px!important}.chapter-navigation-rail{display:block;position:sticky;top:18px;align-self:start;grid-column:3!important;grid-row:1!important;padding:18px 15px;border:1px solid #c8dcda;border-top:5px solid #087774;border-radius:13px;background:#fff;box-shadow:0 7px 18px rgba(14,63,72,.07)}.chapter-navigation-rail h3{margin:0 0 8px;color:#075f60;font-size:1.05rem}.chapter-navigation-intro{margin:0 0 14px;color:#526872;font-size:.86rem;line-height:1.45}.chapter-navigation-list{display:grid;gap:9px}.chapter-navigation-link{width:100%;padding:11px 12px;border:1px solid #a9cfcc;border-radius:8px;background:#f7fbfb;color:#174757;font-weight:750;line-height:1.35;text-align:left;cursor:pointer}.chapter-navigation-link:hover,.chapter-navigation-link:focus-visible{border-color:#087774;background:#edf7f5}.chapter-navigation-link:focus-visible{outline:3px solid #1769c2;outline-offset:2px}.chapter-navigation-link.is-current{border-color:#087774;border-left:5px solid #087774;background:#dcefeb;color:#075f60}}
    @media(max-width:1150px){.chapter-navigation-rail{display:none!important}}
    @media(max-width:680px){.chapter-content-section,.chapter-static-section{margin:12px 8px!important;padding-left:14px!important;padding-right:14px!important}.study-flashcard-grid{grid-template-columns:1fr!important}.study-flashcard{padding:17px}.study-flashcard-toggle{width:100%}}
  `;
  document.head.append(style);
 };
 applyHierarchy();
 window.addEventListener('load', () => setTimeout(applyHierarchy, 700), {once:true});
 setTimeout(applyHierarchy, 1800);
 if(!window.__chapterTemplateObserver){
  let refreshTimer;
  window.__chapterTemplateObserver=new MutationObserver(()=>{
   clearTimeout(refreshTimer);refreshTimer=setTimeout(applyHierarchy,100);
  });
  const startObserver=()=>{
   window.__chapterTemplateObserver.observe(document.body,{childList:true,subtree:true,attributes:true,attributeFilter:['aria-pressed']});
   document.body.addEventListener('click',event=>{
    if(!event.target.closest('.chapter-selector button,.chapter-navigation-link'))return;
    [0,100,400,900,1600].forEach(delay=>setTimeout(applyHierarchy,delay));
   },true);
  };
  if(document.body)startObserver();else window.addEventListener('DOMContentLoaded',startObserver,{once:true});
 }
})();
