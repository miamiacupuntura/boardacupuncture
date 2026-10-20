(() => {
  'use strict';

  const copy = {
    en: {
      eyebrow: 'CHAPTER LEARNING PATHS',
      title: 'One Chapter, Two Learning Paths',
      lead: 'Use this chapter to strengthen your Board Exam preparation and complete a structured Continuing Education review.',
      boardBadge: 'BOARD PREPARATION',
      boardTitle: 'Board Exam Preparation',
      boardText: 'Review essential concepts, practice questions and Board-level mock exams.',
      boardButton: 'Start Board Preparation',
      continuingBadge: 'CONTINUING EDUCATION',
      continuingTitle: 'Continuing Education',
      continuingText: 'Follow the learning plan, review explanatory feedback and complete the Continuing Education exam.',
      continuingButton: 'Start Continuing Education',
      note: 'This chapter supports exam preparation and continuing education review. Completion does not guarantee Board examination success. Continuing Education credit depends on the applicable course requirements.'
    },
    es: {
      eyebrow: 'RUTAS DE APRENDIZAJE DEL CAPÍTULO',
      title: 'Un capítulo, dos rutas de aprendizaje',
      lead: 'Usa este capítulo para fortalecer tu preparación para el Board y completar un repaso estructurado de Continuing Education.',
      boardBadge: 'PREPARACIÓN PARA EL BOARD',
      boardTitle: 'Preparación para el Board Exam',
      boardText: 'Repasa conceptos esenciales, preguntas de práctica y exámenes mock de nivel Board.',
      boardButton: 'Comenzar preparación para el Board',
      continuingBadge: 'CONTINUING EDUCATION',
      continuingTitle: 'Continuing Education',
      continuingText: 'Sigue el plan de aprendizaje, repasa el feedback explicativo y completa el examen de Continuing Education.',
      continuingButton: 'Comenzar Continuing Education',
      note: 'Este capítulo apoya la preparación para exámenes y el repaso de Continuing Education. Completarlo no garantiza aprobar el Board. El crédito de Continuing Education depende de los requisitos aplicables al curso.'
    }
  };

  const configurations = [
    {
      key: 'channels',
      hero: '#sgv2-introduction',
      board: '#channels-ncbahm-alignment',
      continuing: '#sgv2-formb'
    },
    {
      key: 'point-categories',
      hero: '#pc-introduction',
      board: '#point-categories-mock-exams',
      continuing: '#pc-continuing-reading'
    }
  ];

  const make = (tag, className, parent) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (parent) parent.append(node);
    return node;
  };

  function goToTarget(event) {
    const selector = event.currentTarget.getAttribute('href');
    const target = selector?.startsWith('#') ? document.querySelector(selector) : null;
    if (!target) return;
    event.preventDefault();
    if (target.tagName === 'DETAILS') target.open = true;
    for (let ancestor = target.parentElement; ancestor; ancestor = ancestor.parentElement) {
      if (ancestor.tagName === 'DETAILS') ancestor.open = true;
    }
    history.replaceState(null, '', selector);
    target.scrollIntoView({behavior: 'smooth', block: 'start'});
    const focusTarget = target.querySelector(':scope > summary, :scope > h2, :scope > h3, :scope > h4') || target;
    focusTarget.tabIndex = -1;
    focusTarget.focus({preventScroll: true});
  }

  function createCard(parent, type, href) {
    const article = make('article', `learning-path-card ${type}-path`, parent);
    const badge = make('span', 'learning-path-badge', article);
    badge.dataset.copy = `${type}Badge`;
    const heading = make('h4', '', article);
    heading.dataset.copy = `${type}Title`;
    const description = make('p', '', article);
    description.dataset.copy = `${type}Text`;
    const link = make('a', 'learning-path-button', article);
    link.href = href;
    link.dataset.copy = `${type}Button`;
    link.addEventListener('click', goToTarget);
  }

  function createBlock(config) {
    const hero = document.querySelector(config.hero);
    if (!hero || document.getElementById(`dual-learning-paths-${config.key}`)) return null;
    const section = make('section', 'dual-learning-paths');
    section.id = `dual-learning-paths-${config.key}`;
    section.setAttribute('aria-labelledby', `dual-learning-title-${config.key}`);
    section.dataset.chapter = config.key;

    const heading = make('div', 'dual-learning-heading', section);
    const eyebrow = make('p', 'dual-learning-eyebrow', heading);
    eyebrow.dataset.copy = 'eyebrow';
    const title = make('h3', '', heading);
    title.id = `dual-learning-title-${config.key}`;
    title.dataset.copy = 'title';
    const lead = make('p', 'dual-learning-lead', heading);
    lead.dataset.copy = 'lead';

    const grid = make('div', 'dual-learning-grid', section);
    createCard(grid, 'board', config.board);
    createCard(grid, 'continuing', config.continuing);
    const note = make('p', 'dual-learning-note', section);
    note.dataset.copy = 'note';
    hero.after(section);
    return section;
  }

  const blocks = configurations.map(createBlock).filter(Boolean);
  if (!blocks.length) return;

  function translate() {
    const language = document.documentElement.lang.toLowerCase().startsWith('es') ? 'es' : 'en';
    blocks.forEach(block => {
      block.querySelectorAll('[data-copy]').forEach(node => {
        node.textContent = copy[language][node.dataset.copy];
      });
    });
  }
  translate();
  new MutationObserver(translate).observe(document.documentElement, {attributes: true, attributeFilter: ['lang']});

  const style = document.createElement('style');
  style.id = 'dual-learning-paths-style';
  style.textContent = `
    .dual-learning-paths{box-sizing:border-box;margin:18px 0 24px;padding:24px;border:1px solid #bed6d5;border-top:5px solid #087774;border-radius:14px;background:linear-gradient(135deg,#f4fbfa 0%,#fff 50%,#faf6ff 100%);box-shadow:0 8px 22px rgba(16,65,74,.08);color:#173747}
    .dual-learning-heading{max-width:760px;margin:0 auto 20px;text-align:center}.dual-learning-eyebrow{margin:0 0 7px!important;color:#08706c;font-size:.75rem;font-weight:850;letter-spacing:.12em}.dual-learning-heading h3{margin:0 0 8px;color:#123b4a;font-size:clamp(1.45rem,3vw,1.75rem);line-height:1.15}.dual-learning-lead{margin:0!important;color:#4a616c;font-size:1rem;line-height:1.5}
    .dual-learning-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.learning-path-card{display:flex;min-width:0;box-sizing:border-box;flex-direction:column;padding:20px;border:1px solid #c8d9dc;border-radius:12px;background:#fff}.board-path{border-top:5px solid #087774}.continuing-path{border-top:5px solid #7652a5}.learning-path-badge{align-self:flex-start;margin-bottom:12px;padding:5px 9px;border-radius:999px;font-size:.7rem;font-weight:850;letter-spacing:.06em}.board-path .learning-path-badge{background:#dff1ee;color:#075f60}.continuing-path .learning-path-badge{background:#eee6f8;color:#5d3c88}.learning-path-card h4{margin:0 0 9px;color:#173747;font-size:1.25rem}.learning-path-card p{margin:0 0 17px;color:#50666f;line-height:1.5}
    .learning-path-button{display:inline-flex;align-items:center;justify-content:center;min-height:44px;box-sizing:border-box;margin-top:auto;padding:10px 14px;border-radius:9px;color:#fff!important;text-decoration:none;font-weight:800;text-align:center}.board-path .learning-path-button{background:#087774}.continuing-path .learning-path-button{background:#7652a5}.board-path .learning-path-button:hover{background:#05635f}.continuing-path .learning-path-button:hover{background:#654391}.learning-path-button:focus-visible{outline:3px solid #1769c2;outline-offset:3px}
    .dual-learning-note{margin:18px 2px 0!important;padding-top:14px;border-top:1px solid #d8e3e3;color:#65757c;font-size:.76rem;line-height:1.5}
    @media(max-width:620px){.dual-learning-paths{padding:18px 14px}.dual-learning-grid{grid-template-columns:1fr}.learning-path-card{padding:17px}.learning-path-button{width:100%}}
  `;
  document.head.append(style);
})();
