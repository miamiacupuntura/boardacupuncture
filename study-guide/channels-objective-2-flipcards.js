(() => {
  'use strict';
  const root=document.getElementById('channels-objective-2');
  const section=root?.querySelector('.objective-flashcards');
  if(!section||section.dataset.flipCards==='complete')return;
  section.dataset.flipCards='complete';
  const original=[...section.querySelectorAll(':scope > details.primary-flashcard')];
  const grid=document.createElement('div');grid.className='primary-flipcard-grid';
  original.forEach((source,index)=>{
    const summary=source.querySelector(':scope > summary');
    const question=(summary?.textContent||'').replace(/^Flashcard\s+\d+\s+—\s*/,'');
    const answer=[...source.children].filter(node=>node!==summary).map(node=>node.textContent.trim()).filter(Boolean).join(' ');
    const card=document.createElement('article');card.className='primary-flipcard';card.tabIndex=0;
    const inner=document.createElement('div');inner.className='primary-flipcard-inner';
    const front=document.createElement('section');front.className='primary-flipcard-face primary-flipcard-front';front.setAttribute('aria-hidden','false');
    const badge=document.createElement('span');badge.className='primary-flipcard-badge';badge.textContent=`Flashcard ${index+1}`;
    const frontHeading=document.createElement('h5');frontHeading.textContent=question;
    const prompt=document.createElement('p');prompt.className='primary-flipcard-prompt';prompt.textContent='Answer from memory before turning the card over.';
    const show=document.createElement('button');show.type='button';show.textContent='Show answer';show.setAttribute('aria-expanded','false');
    front.append(badge,frontHeading,prompt,show);
    const back=document.createElement('section');back.className='primary-flipcard-face primary-flipcard-back';back.setAttribute('aria-hidden','true');back.inert=true;
    const backBadge=document.createElement('span');backBadge.className='primary-flipcard-badge';backBadge.textContent=`Flashcard ${index+1} — Answer`;
    const answerHeading=document.createElement('h5');answerHeading.textContent=question;
    const answerText=document.createElement('p');answerText.className='primary-flipcard-answer';answerText.textContent=answer;
    const hide=document.createElement('button');hide.type='button';hide.textContent='Show question';
    back.append(backBadge,answerHeading,answerText,hide);inner.append(front,back);card.append(inner);grid.append(card);
    const flip=(showAnswer)=>{card.classList.toggle('is-flipped',showAnswer);front.setAttribute('aria-hidden',String(showAnswer));back.setAttribute('aria-hidden',String(!showAnswer));back.inert=!showAnswer;front.inert=showAnswer;show.setAttribute('aria-expanded',String(showAnswer));(showAnswer?hide:show).focus({preventScroll:true});};
    show.onclick=()=>flip(true);hide.onclick=()=>flip(false);
    card.addEventListener('keydown',event=>{if((event.key==='Enter'||event.key===' ')&&event.target===card){event.preventDefault();flip(!card.classList.contains('is-flipped'));}});
    source.remove();
  });
  section.append(grid);
  const style=document.createElement('style');style.textContent=`
    #channels-objective-2 .primary-flipcard-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:14px}
    #channels-objective-2 .primary-flipcard{min-height:260px;perspective:1200px}
    #channels-objective-2 .primary-flipcard-inner{display:grid;height:100%;transform-style:preserve-3d;transition:transform .5s}
    #channels-objective-2 .primary-flipcard.is-flipped .primary-flipcard-inner{transform:rotateY(180deg)}
    #channels-objective-2 .primary-flipcard-face{grid-area:1/1;display:flex;min-width:0;min-height:260px;box-sizing:border-box;flex-direction:column;padding:20px;border:1px solid #b8d9d6;border-top:5px solid #087774;border-radius:13px;background:#f7fcfb;backface-visibility:hidden;box-shadow:0 7px 18px rgba(11,64,74,.08)}
    #channels-objective-2 .primary-flipcard-back{transform:rotateY(180deg);border-top-color:#ef7e2d;background:#fff8ef}
    #channels-objective-2 .primary-flipcard-face[aria-hidden="true"]{visibility:hidden}
    #channels-objective-2 .primary-flipcard-badge{align-self:flex-start;padding:5px 9px;border-radius:999px;background:#e2f2ee;color:#075f60;font-size:.78rem;font-weight:800}
    #channels-objective-2 .primary-flipcard-back .primary-flipcard-badge{background:#ffe4cf;color:#8b4217}
    #channels-objective-2 .primary-flipcard h5{margin:17px 0 10px;font-size:1.05rem;line-height:1.4}
    #channels-objective-2 .primary-flipcard-prompt{color:#536872;font-size:.9rem}
    #channels-objective-2 .primary-flipcard-answer{margin:0 0 18px;padding:13px;border-left:4px solid #ef7e2d;border-radius:8px;background:#fff;line-height:1.55}
    #channels-objective-2 .primary-flipcard button{align-self:flex-start;margin-top:auto;padding:10px 16px;border:0;border-radius:8px;background:#087774;color:#fff;font-weight:800;cursor:pointer}
    #channels-objective-2 .primary-flipcard-back button{background:#d9671f}
    @media(max-width:760px){#channels-objective-2 .primary-flipcard-grid{grid-template-columns:1fr}}
    @media(prefers-reduced-motion:reduce){#channels-objective-2 .primary-flipcard-inner{transition:none}}
  `;document.head.append(style);
})();
