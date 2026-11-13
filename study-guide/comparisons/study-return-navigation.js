(function () {
  'use strict';

  const topbar = document.querySelector('.comparison-topbar__inner');
  if (!topbar) {
    return;
  }

  const spanish = document.documentElement.lang.toLowerCase().startsWith('es')
    || /-es\.html$/.test(window.location.pathname);
  const studyGuideUrl = spanish
    ? '/blocks/myprogress/study-guide.php?area=herbology&view=library&lang=es'
    : '/blocks/myprogress/study-guide.php?area=herbology&view=library';
  let actions = topbar.querySelector('.herb-bank-top-actions,.formula-modifications__top-actions');

  if (!actions) {
    actions = [...topbar.children].find(element =>
      element.tagName === 'SPAN' && !element.classList.contains('comparison-brand')
    );
  }

  if (!actions) {
    actions = document.createElement('span');
    actions.style.display = 'flex';
    actions.style.flexWrap = 'wrap';
    actions.style.gap = '8px';
    topbar.appendChild(actions);
  }

  [...actions.querySelectorAll('a')].forEach(link => {
    const url = new URL(link.href, window.location.href);
    if (url.pathname === '/my/') {
      link.remove();
    }
  });

  let studyGuideLink = [...actions.querySelectorAll('a')].find(link => {
    const url = new URL(link.href, window.location.href);
    return url.pathname === '/blocks/myprogress/study-guide.php'
      && url.searchParams.get('area') === 'herbology'
      && url.searchParams.get('view') === 'library';
  });
  if (!studyGuideLink) {
    studyGuideLink = document.createElement('a');
    studyGuideLink.className = 'comparison-back';
    actions.appendChild(studyGuideLink);
  }
  studyGuideLink.href = studyGuideUrl;
  studyGuideLink.textContent = spanish ? '← Guía de estudio' : '← Study Guide';

  if (document.querySelector('.practice-study-route')) {
    return;
  }

  const page = window.location.pathname.split('/').pop().replace('-es.html', '.html');
  const routes = {
    'formula-navigator.html': ['formulas', spanish ? 'Fórmulas' : 'Formulas', spanish ? 'Buscar una fórmula' : 'Find a Formula'],
    'formula-practice.html': ['quiz', '', spanish ? 'Quiz de fórmulas' : 'Formula Quiz'],
    'formula-question-bank.html': ['formulas', spanish ? 'Fórmulas' : 'Formulas', spanish ? 'Preguntas de estudio y flashcards' : 'Study Questions & Flashcards'],
    'single-herb-practice.html': ['quiz', '', spanish ? 'Quiz de hierbas individuales' : 'Single Herbs Quiz']
  };
  const route = routes[page];
  const hero = document.querySelector('.comparison-hero');
  if (!route || !hero) {
    return;
  }

  const languageQuery = spanish ? '&lang=es' : '';
  const rootQuery = spanish ? '?lang=es' : '';
  const nav = document.createElement('nav');
  nav.className = 'practice-study-route';
  if (route[0] === 'quiz') {
    nav.setAttribute('aria-label', spanish ? 'Ruta de navegación de Quiz' : 'Quiz navigation path');
    nav.innerHTML = `<a href="/blocks/menu/herbology-board.php?track=quiz${languageQuery}">${spanish ? 'Quiz de Herbología' : 'Herbology Quiz'}</a>`
      + '<span aria-hidden="true">›</span>'
      + `<span aria-current="page">${route[2]}</span>`;
  } else {
    nav.setAttribute('aria-label', spanish ? 'Ruta de navegación de práctica' : 'Practice navigation path');
    nav.innerHTML = `<a href="/blocks/menu/herbology-board.php${rootQuery}">${spanish ? 'Práctica de Herbología' : 'Herbology Practice'}</a>`
      + '<span aria-hidden="true">›</span>'
      + `<a href="/blocks/menu/herbology-board.php?track=${route[0]}${languageQuery}">${route[1]}</a>`
      + '<span aria-hidden="true">›</span>'
      + `<span aria-current="page">${route[2]}</span>`;
  }
  hero.insertAdjacentElement('afterend', nav);
})();
