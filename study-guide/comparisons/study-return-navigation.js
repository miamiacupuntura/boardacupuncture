(function () {
  'use strict';

  const topbar = document.querySelector('.comparison-topbar__inner');
  if (!topbar) {
    return;
  }

  const spanish = document.documentElement.lang.toLowerCase().startsWith('es')
    || /-es\.html$/.test(window.location.pathname);
  const dashboardUrl = '/my/';
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
    if (url.pathname === '/blocks/myprogress/study-guide.php'
        && url.searchParams.get('area') === 'herbology') {
      link.remove();
    }
  });

  let dashboardLink = [...actions.querySelectorAll('a')].find(link => {
    const url = new URL(link.href, window.location.href);
    return url.pathname === dashboardUrl;
  });
  if (!dashboardLink) {
    dashboardLink = document.createElement('a');
    dashboardLink.className = 'comparison-back';
    dashboardLink.href = dashboardUrl;
    actions.appendChild(dashboardLink);
  }
  dashboardLink.textContent = spanish ? '← Panel principal' : '← Back to Dashboard';

  if (document.querySelector('.practice-study-route')) {
    return;
  }

  const page = window.location.pathname.split('/').pop().replace('-es.html', '.html');
  const routes = {
    'formula-navigator.html': ['formulas', spanish ? 'Fórmulas' : 'Formulas', spanish ? 'Buscar una fórmula' : 'Find a Formula'],
    'formula-practice.html': ['formulas', spanish ? 'Fórmulas' : 'Formulas', 'Quiz'],
    'formula-question-bank.html': ['formulas', spanish ? 'Fórmulas' : 'Formulas', spanish ? 'Preguntas de estudio y flashcards' : 'Study Questions & Flashcards'],
    'single-herb-practice.html': ['herbs', spanish ? 'Hierbas individuales' : 'Single Herbs', 'Quiz']
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
  nav.setAttribute('aria-label', spanish ? 'Ruta de navegación de práctica' : 'Practice navigation path');
  nav.innerHTML = `<a href="/blocks/menu/herbology-board.php${rootQuery}">${spanish ? 'Práctica de Herbología' : 'Herbology Practice'}</a>`
    + '<span aria-hidden="true">›</span>'
    + `<a href="/blocks/menu/herbology-board.php?track=${route[0]}${languageQuery}">${route[1]}</a>`
    + '<span aria-hidden="true">›</span>'
    + `<span aria-current="page">${route[2]}</span>`;
  hero.insertAdjacentElement('afterend', nav);
})();
