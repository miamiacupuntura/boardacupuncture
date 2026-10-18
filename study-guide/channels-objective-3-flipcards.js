(() => {
  'use strict';
  const section=document.querySelector('#channels-objective-3 .objective-flashcards');
  if(!section||section.dataset.flipCards==='complete')return;
  section.dataset.flipCards='complete';
  const sources=[...section.querySelectorAll(':scope > details.divergent-card')];
  if(!sources.length)return;
  const grid=document.createElement('div');grid.className='divergent-flipcard-grid';
  sources.forEach((source,index)=>{
    const summary=source.querySelector(':scope > summary');
    const question=(summary?.textContent||'').replace(/^Flashcard\s+\d+\s+—\s*/, '').trim();
    const answer=[...source.children].filter(n=>n!==summary).map(n=>n.textContent.trim()).filter(Boolean).join(' ');
    const card=document.createElement('article');card.className='divergent-flipcard';card.tabIndex=0;
    const inner=document.createElement('div');inner.className='divergent-flipcard-inner';
    const front=document.createElement('section');front.className='divergent-flipcard-face divergent-flipcard-front';front.setAttribute('aria-hidden','false');
    const frontBadge=document.createElement('span');frontBadge.className='divergent-flipcard-badge';frontBadge.textContent=`Flashcard ${index+1}`;
    const heading=document.createElement('h5');heading.textContent=question;
    const prompt=document.createElement('p');prompt.className='divergent-flipcard-prompt';prompt.textContent='Answer from memory before turning the card over.';
    const show=document.createElement('button');show.type='button';show.textContent='Show answer';show.setAttribute('aria-expanded','false');
    front.append(frontBadge,heading,prompt,show);
    const back=document.createElement('section');back.className='divergent-flipcard-face divergent-flipcard-back';back.setAttribute('aria-hidden','true');back.inert=true;
    const backBadge=document.createElement('span');backBadge.className='divergent-flipcard-badge';backBadge.textContent=`Flashcard ${index+1} — Answer`;
    const backHeading=document.createElement('h5');backHeading.textContent=question;
    const answerText=document.createElement('p');answerText.className='divergent-flipcard-answer';answerText.textContent=answer;
    const hide=document.createElement('button');hide.type='button';hide.textContent='Show question';
    back.append(backBadge,backHeading,answerText,hide);inner.append(front,back);card.append(inner);grid.append(card);
    const flip=showAnswer=>{card.classList.toggle('is-flipped',showAnswer);front.setAttribute('aria-hidden',String(showAnswer));back.setAttribute('aria-hidden',String(!showAnswer));front.inert=showAnswer;back.inert=!showAnswer;show.setAttribute('aria-expanded',String(showAnswer));(showAnswer?hide:show).focus({preventScroll:true})};
    show.onclick=()=>flip(true);hide.onclick=()=>flip(false);
    card.addEventListener('keydown',event=>{if((event.key==='Enter'||event.key===' ')&&event.target===card){event.preventDefault();flip(!card.classList.contains('is-flipped'))}});
    source.remove();
  });
  section.append(grid);
  const style=document.createElement('style');style.textContent=`
    #channels-objective-3 .divergent-flipcard-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:14px}
    #channels-objective-3 .divergent-flipcard{min-height:270px;perspective:1200px}
    #channels-objective-3 .divergent-flipcard-inner{display:grid;height:100%;transform-style:preserve-3d;transition:transform .5s}
    #channels-objective-3 .divergent-flipcard.is-flipped .divergent-flipcard-inner{transform:rotateY(180deg)}
    #channels-objective-3 .divergent-flipcard-face{grid-area:1/1;display:flex;min-width:0;min-height:270px;box-sizing:border-box;flex-direction:column;padding:20px;border:1px solid #b8d9d6;border-top:5px solid #087774;border-radius:13px;background:#f7fcfb;backface-visibility:hidden;box-shadow:0 7px 18px rgba(11,64,74,.08)}
    #channels-objective-3 .divergent-flipcard-back{transform:rotateY(180deg);border-top-color:#ef7e2d;background:#fff8ef}
    #channels-objective-3 .divergent-flipcard-face[aria-hidden="true"]{visibility:hidden}
    #channels-objective-3 .divergent-flipcard-badge{align-self:flex-start;padding:5px 9px;border-radius:999px;background:#e2f2ee;color:#075f60;font-size:.78rem;font-weight:800}
    #channels-objective-3 .divergent-flipcard-back .divergent-flipcard-badge{background:#ffe4cf;color:#8b4217}
    #channels-objective-3 .divergent-flipcard h5{margin:17px 0 10px;font-size:1.05rem;line-height:1.4}
    #channels-objective-3 .divergent-flipcard-prompt{color:#536872;font-size:.9rem}
    #channels-objective-3 .divergent-flipcard-answer{margin:0 0 18px;padding:13px;border-left:4px solid #ef7e2d;border-radius:8px;background:#fff;line-height:1.55}
    #channels-objective-3 .divergent-flipcard button{align-self:flex-start;margin-top:auto;padding:10px 16px;border:0;border-radius:8px;background:#087774;color:#fff;font-weight:800;cursor:pointer}
    #channels-objective-3 .divergent-flipcard-back button{background:#d9671f}
    @media(max-width:760px){#channels-objective-3 .divergent-flipcard-grid{grid-template-columns:1fr}}
    @media(prefers-reduced-motion:reduce){#channels-objective-3 .divergent-flipcard-inner{transition:none}}
  `;document.head.append(style);
})();
