(() => {
  'use strict';
  const chapterIndexUrl = '/blocks/myprogress/study-guide.php?area=pointlocation';
  const chapterIndexLabel = '← Back to Study Guide index';
  function ensureMobileIndexBack() {
    const selector = document.querySelector('.chapter-selector');
    if (!selector) return;
    let bar = document.querySelector('.study-guide-mobile-index-back');
    if (!bar) {
      bar = document.createElement('div');
      bar.className = 'study-guide-mobile-index-back';
      const link = document.createElement('a');
      link.className = 'point-categories-hero-button point-categories-hero-back';
      bar.append(link);
      selector.before(bar);
    }
    const link = bar.querySelector('a');
    link.href = chapterIndexUrl;
    link.textContent = chapterIndexLabel;
  }
  const channels = document.getElementById('sgv2-introduction');
  function ensurePointHero() {
    const hero = document.getElementById('pc-introduction');
    if (!hero) return;
    if (hero.querySelectorAll('.point-categories-hero-button').length === 2) {
      hero.classList.add('shared-chapter-hero');
      const back = hero.querySelector('.point-categories-hero-back');
      if (back) {
        back.href = chapterIndexUrl;
        back.textContent = chapterIndexLabel;
      }
      return;
    }
    hero.removeAttribute('style');
    hero.className = 'point-categories-hero shared-chapter-hero';
    hero.replaceChildren();
    const copy = document.createElement('div'); copy.className = 'point-categories-hero-copy';
    const eyebrow = document.createElement('p'); eyebrow.className = 'point-categories-hero-eyebrow'; eyebrow.textContent = 'STUDY GUIDE · POINT CATEGORIES';
    const h1 = document.createElement('h1'); h1.textContent = 'Acupuncture & Point Location';
    const h2 = document.createElement('h2'); h2.textContent = 'Point Categories';
    const lead = document.createElement('p'); lead.className = 'point-categories-hero-lead'; lead.textContent = 'Connect point classifications, channel correspondences and clinical reasoning through 26 focused objectives.';
    const actions = document.createElement('div'); actions.className = 'point-categories-hero-actions';
    const back = document.createElement('a'); back.href = chapterIndexUrl; back.className = 'point-categories-hero-button point-categories-hero-back'; back.textContent = chapterIndexLabel;
    const exam = document.createElement('a'); exam.href = '#pc-exam-history'; exam.className = 'point-categories-hero-button point-categories-hero-exam'; exam.textContent = 'Start Exam';
    exam.addEventListener('click', event => {const target=document.getElementById('pc-exam-history');if(!target)return;event.preventDefault();target.open=true;history.replaceState(null,'','#pc-exam-history');target.scrollIntoView({behavior:'smooth',block:'start'});target.querySelector(':scope > summary')?.focus({preventScroll:true})});
    actions.append(back, exam); copy.append(eyebrow, h1, h2, lead, actions);
    const figure = document.createElement('figure'); figure.className = 'point-categories-hero-art';
    const image = document.createElement('img'); image.src = 'assets/point-categories-chapter-cover.webp'; image.alt = 'Conceptual Point Categories chapter illustration'; image.loading = 'eager'; image.fetchPriority = 'high';
    const caption = document.createElement('figcaption'); caption.textContent = 'Conceptual chapter cover — not a point-location or needling guide.';
    figure.append(image, caption); hero.append(copy, figure);
  }
  ensureMobileIndexBack();
  ensurePointHero();
  document.querySelectorAll('.chapter-selector button')[1]?.addEventListener('click', () => setTimeout(ensurePointHero, 0));
  if (!channels) return;

  const oldBack = chapterIndexUrl;
  channels.className = 'sgv2-card point-categories-hero shared-chapter-hero channels-chapter-hero';
  channels.replaceChildren();

  const copy = document.createElement('div');
  copy.className = 'point-categories-hero-copy';
  const eyebrow = document.createElement('p');
  eyebrow.className = 'point-categories-hero-eyebrow';
  eyebrow.textContent = 'STUDY GUIDE · CHANNELS AND COLLATERALS';
  const h1 = document.createElement('h1');
  h1.textContent = 'Acupuncture & Point Location';
  const h2 = document.createElement('h2');
  h2.textContent = 'The Channels and Collaterals';
  new MutationObserver(() => {
    if (/^Chapter:/i.test(h2.textContent.trim()) || h2.textContent.trim() === 'THE CHANNELS AND COLLATERALS') h2.textContent = 'The Channels and Collaterals';
  }).observe(h2, {childList: true, characterData: true, subtree: true});
  const lead = document.createElement('p');
  lead.className = 'point-categories-hero-lead';
  lead.textContent = 'Connect channel functions, pathways and relationships across nine focused objectives.';
  const actions = document.createElement('div');
  actions.className = 'point-categories-hero-actions';
  const back = document.createElement('a');
  back.href = oldBack;
  back.className = 'point-categories-hero-button point-categories-hero-back';
  back.textContent = chapterIndexLabel;
  const exam = document.createElement('a');
  exam.href = '#chapter-exam-history';
  exam.className = 'point-categories-hero-button point-categories-hero-exam';
  exam.textContent = 'School Level Exam';
  exam.addEventListener('click', event => {
    const target = document.getElementById('chapter-exam-history');
    if (!target) return;
    event.preventDefault();
    const details = target.matches('details') ? target : target.querySelector(':scope > details');
    if (details) details.open = true;
    history.replaceState(null, '', '#chapter-exam-history');
    target.scrollIntoView({behavior: 'smooth', block: 'start'});
    target.querySelector('summary')?.focus({preventScroll: true});
  });
  const boardExam = document.createElement('a');
  boardExam.href = '#channels-mock-exams';
  boardExam.className = 'point-categories-hero-button channels-hero-board-exam';
  boardExam.textContent = 'Board Level Exam';
  boardExam.addEventListener('click', event => {
    const target = document.getElementById('channels-mock-exams');
    if (!target) return;
    event.preventDefault();
    target.open = true;
    history.replaceState(null, '', '#channels-mock-exams');
    target.scrollIntoView({behavior: 'smooth', block: 'start'});
    target.querySelector(':scope > summary')?.focus({preventScroll: true});
  });
  const continuingExam = document.createElement('a');
  continuingExam.href = '#channels-continuing-education-exam';
  continuingExam.className = 'point-categories-hero-button channels-hero-continuing-exam';
  continuingExam.textContent = 'Continuing Education Exam';
  continuingExam.addEventListener('click', event => {
    const target = document.getElementById('channels-continuing-education-exam');
    if (!target) return;
    event.preventDefault();
    target.open = true;
    history.replaceState(null, '', '#channels-continuing-education-exam');
    target.scrollIntoView({behavior: 'smooth', block: 'start'});
    target.querySelector(':scope > summary')?.focus({preventScroll: true});
  });
  actions.append(back, exam, boardExam, continuingExam);
  copy.append(eyebrow, h1, h2, lead, actions);

  const figure = document.createElement('figure');
  figure.className = 'point-categories-hero-art';
  const image = document.createElement('img');
  image.src = 'assets/primary-channel-four-groups.webp';
  image.alt = 'Four directional groups of the Twelve Primary Channels';
  image.loading = 'eager';
  image.fetchPriority = 'high';
  const caption = document.createElement('figcaption');
  caption.textContent = 'Chapter overview — pathway directions, not a point-location or needling guide.';
  figure.append(image, caption);
  channels.append(copy, figure);

  const style = document.createElement('style');
  style.textContent = `
    .shared-chapter-hero{position:relative;display:grid;grid-template-columns:minmax(0,1.08fr) minmax(310px,.92fr);align-items:center;gap:22px;min-height:250px;padding:22px 24px;overflow:hidden;border:1px solid #b8d7d5;border-top:5px solid #087774;border-radius:14px;background:linear-gradient(125deg,#eef9f6 0%,#f9fcff 54%,#fff5df 100%);box-shadow:0 10px 28px rgba(16,65,74,.09)}
    .shared-chapter-hero .point-categories-hero-copy{position:relative;z-index:1;min-width:0}
    .shared-chapter-hero .point-categories-hero-eyebrow{margin:0 0 6px;color:#08706c;font-size:.78rem;font-weight:850;letter-spacing:.12em}
    .shared-chapter-hero .point-categories-hero-lead{max-width:660px;margin:0;color:#38525c;font-size:1rem;line-height:1.48}
    .shared-chapter-hero .point-categories-hero-actions{display:flex;flex-wrap:wrap;gap:11px;margin-top:16px}
    .shared-chapter-hero .point-categories-hero-button{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:9px 16px;border-radius:9px;font-weight:800;text-decoration:none;box-sizing:border-box}
    .shared-chapter-hero .point-categories-hero-back,.study-guide-mobile-index-back .point-categories-hero-back{border:2px solid #176b70;background:#fff;color:#145960}
    .study-guide-mobile-index-back{display:none}
    .shared-chapter-hero .point-categories-hero-exam{border:2px solid #f47e2c;background:#f47e2c;color:#17232a;box-shadow:0 5px 12px rgba(202,91,13,.18)}
    .shared-chapter-hero .channels-hero-board-exam{border:2px solid #087774;background:#087774;color:#fff;box-shadow:0 5px 12px rgba(8,119,116,.18)}
    .shared-chapter-hero .channels-hero-continuing-exam{border:2px solid #7652a5;background:#7652a5;color:#fff;box-shadow:0 5px 12px rgba(118,82,165,.18)}
    .shared-chapter-hero .point-categories-hero-button:focus-visible{outline:3px solid #185cca;outline-offset:3px}
    .shared-chapter-hero .point-categories-hero-button:hover{transform:translateY(-1px)}
    .shared-chapter-hero .point-categories-hero-art{position:relative;margin:0;min-width:0;align-self:stretch;max-height:250px;border-radius:11px;overflow:hidden;background:#dcefeb}
    .shared-chapter-hero .point-categories-hero-art img{display:block;width:100%;height:100%;min-height:220px;object-fit:cover;object-position:center}
    .shared-chapter-hero .point-categories-hero-art figcaption{position:absolute;left:8px;right:8px;bottom:8px;padding:6px 8px;border-radius:7px;background:rgba(7,40,48,.78);color:#fff;font-size:.72rem;line-height:1.3;text-align:center}
    #sgv2-introduction.channels-chapter-hero{position:relative;display:grid;grid-template-columns:minmax(0,1.08fr) minmax(310px,.92fr);align-items:center;gap:22px;min-height:250px;padding:22px 24px;overflow:hidden;border:1px solid #b8d7d5;border-top:5px solid #087774;border-radius:14px;background:linear-gradient(125deg,#eef9f6 0%,#f9fcff 54%,#fff5df 100%);box-shadow:0 10px 28px rgba(16,65,74,.09)}
    #sgv2-introduction.channels-chapter-hero h1{margin:0;color:#102d3b;font-size:clamp(1.65rem,3vw,2.35rem);line-height:1.08}
    #sgv2-introduction.channels-chapter-hero h2{margin:5px 0 9px;color:#a64f12;font-size:clamp(1.3rem,2.2vw,1.8rem);line-height:1.15}
    #sgv2-introduction.channels-chapter-hero .point-categories-hero-art img{object-position:center 12%}
    @media(min-width:761px){.chapter-selector{display:none!important}}
    @media(max-width:760px){.study-guide-mobile-index-back{display:block;position:sticky;top:0;z-index:1000;margin:0 0 14px;padding:10px 0;background:rgba(238,247,247,.96);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}.study-guide-mobile-index-back .point-categories-hero-back{display:flex;align-items:center;justify-content:center;width:100%;min-height:46px;padding:10px 14px;border-radius:9px;font-weight:800;text-decoration:none;box-sizing:border-box}.shared-chapter-hero .point-categories-hero-back{display:none}.shared-chapter-hero,#sgv2-introduction.channels-chapter-hero{grid-template-columns:1fr;padding:18px;gap:16px}.shared-chapter-hero .point-categories-hero-art{order:-1;height:180px;max-height:180px}.shared-chapter-hero .point-categories-hero-art img{min-height:180px}.shared-chapter-hero .point-categories-hero-actions{display:grid;grid-template-columns:1fr}.shared-chapter-hero .point-categories-hero-button{padding:9px 10px}}
    @media(max-width:430px){.shared-chapter-hero .point-categories-hero-actions{grid-template-columns:1fr}.shared-chapter-hero .point-categories-hero-art{height:155px;max-height:155px}.shared-chapter-hero .point-categories-hero-art img{min-height:155px}}
  `;
  document.head.append(style);
})();
