(() => {
  'use strict';
  const section=document.querySelector('#channels-objective-7 .objective-flashcards');
  if(!section||section.dataset.flipCards==='complete')return;
  const sources=[...section.querySelectorAll(':scope > details.minute-card')];if(!sources.length)return;
  section.dataset.flipCards='complete';
  const grid=document.createElement('div');grid.className='minute-flipcard-grid';
  sources.forEach((source,index)=>{
    const summary=source.querySelector(':scope > summary');
    const question=(summary?.textContent||'').replace(/^Flashcard\s+\d+\s+—\s*/,'').trim();
    const answer=[...source.children].filter(n=>n!==summary).map(n=>n.textContent.trim()).filter(Boolean).join(' ');
    const card=document.createElement('article');card.className='minute-flipcard';card.tabIndex=0;
    const inner=document.createElement('div');inner.className='minute-flipcard-inner';
    const face=(side,badgeText)=>{const n=document.createElement('section');n.className=`minute-flipcard-face minute-flipcard-${side}`;const badge=document.createElement('span');badge.className='minute-flipcard-badge';badge.textContent=badgeText;const h=document.createElement('h5');h.textContent=question;n.append(badge,h);return n};
    const front=face('front',`Flashcard ${index+1}`);front.setAttribute('aria-hidden','false');
    const prompt=document.createElement('p');prompt.className='minute-flipcard-prompt';prompt.textContent='Answer from memory before turning the card over.';
    const show=document.createElement('button');show.type='button';show.textContent='Show answer';show.setAttribute('aria-expanded','false');front.append(prompt,show);
    const back=face('back',`Flashcard ${index+1} — Answer`);back.setAttribute('aria-hidden','true');back.inert=true;
    const answerText=document.createElement('p');answerText.className='minute-flipcard-answer';answerText.textContent=answer;
    const hide=document.createElement('button');hide.type='button';hide.textContent='Show question';back.append(answerText,hide);
    inner.append(front,back);card.append(inner);grid.append(card);
    const flip=value=>{card.classList.toggle('is-flipped',value);front.setAttribute('aria-hidden',String(value));back.setAttribute('aria-hidden',String(!value));front.inert=value;back.inert=!value;show.setAttribute('aria-expanded',String(value));(value?hide:show).focus({preventScroll:true})};
    show.onclick=()=>flip(true);hide.onclick=()=>flip(false);card.addEventListener('keydown',e=>{if((e.key==='Enter'||e.key===' ')&&e.target===card){e.preventDefault();flip(!card.classList.contains('is-flipped'))}});source.remove();
  });
  section.append(grid);
  const style=document.createElement('style');style.textContent=`#channels-objective-7 .minute-flipcard-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:14px}#channels-objective-7 .minute-flipcard{min-height:280px;perspective:1200px}#channels-objective-7 .minute-flipcard-inner{display:grid;height:100%;transform-style:preserve-3d;transition:transform .5s}#channels-objective-7 .minute-flipcard.is-flipped .minute-flipcard-inner{transform:rotateY(180deg)}#channels-objective-7 .minute-flipcard-face{grid-area:1/1;display:flex;min-width:0;min-height:280px;box-sizing:border-box;flex-direction:column;padding:20px;border:1px solid #b8d9d6;border-top:5px solid #087774;border-radius:13px;background:#f7fcfb;backface-visibility:hidden;box-shadow:0 7px 18px rgba(11,64,74,.08)}#channels-objective-7 .minute-flipcard-back{transform:rotateY(180deg);border-top-color:#ef7e2d;background:#fff8ef}#channels-objective-7 .minute-flipcard-face[aria-hidden="true"]{visibility:hidden}#channels-objective-7 .minute-flipcard-badge{align-self:flex-start;padding:5px 9px;border-radius:999px;background:#e2f2ee;color:#075f60;font-size:.78rem;font-weight:800}#channels-objective-7 .minute-flipcard-back .minute-flipcard-badge{background:#ffe4cf;color:#8b4217}#channels-objective-7 .minute-flipcard h5{margin:17px 0 10px;font-size:1.05rem;line-height:1.4}#channels-objective-7 .minute-flipcard-prompt{color:#536872;font-size:.9rem}#channels-objective-7 .minute-flipcard-answer{margin:0 0 18px;padding:13px;border-left:4px solid #ef7e2d;border-radius:8px;background:#fff;line-height:1.55}#channels-objective-7 .minute-flipcard button{align-self:flex-start;margin-top:auto;min-height:44px;padding:10px 16px;border:0;border-radius:8px;background:#087774;color:#fff;font-weight:800;cursor:pointer}#channels-objective-7 .minute-flipcard-back button{background:#d9671f}@media(max-width:760px){#channels-objective-7 .minute-flipcard-grid{grid-template-columns:1fr}#channels-objective-7 .minute-flipcard button{width:100%}}@media(prefers-reduced-motion:reduce){#channels-objective-7 .minute-flipcard-inner{transition:none}}`;
  document.head.append(style);
})();
