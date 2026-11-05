(() => {
  'use strict';

  const topbar = document.querySelector('.comparison-topbar__inner');
  if (!topbar) return;

  const path = window.location.pathname;
  const isFormula = /\/formula-practice(?:-es)?\.html$/.test(path);
  const isHerb = /\/herb-comparison-bank(?:-es)?\.html$/.test(path);
  if (!isFormula && !isHerb) return;

  const spanish = document.documentElement.lang === 'es';
  const brand = topbar.querySelector('.comparison-brand');
  const actions = topbar.querySelector(':scope > span:last-child');
  if (!brand || !actions) return;

  brand.href = `/blocks/menu/herbology-board.php?track=${isFormula ? 'formulas' : 'herbs'}`;
  const brandLabel = brand.querySelector('span:last-child');
  if (brandLabel) {
    brandLabel.textContent = isFormula
      ? (spanish ? 'Práctica de fórmulas' : 'Formulas Practice')
      : (spanish ? 'Práctica de hierbas individuales' : 'Single Herbs Practice');
  }

  const language = actions.querySelector('a[href*="practice-es"], a[href*="practice.html"], a[href*="comparison-bank-es"], a[href*="comparison-bank.html"]');
  const switcher = document.createElement('a');
  switcher.className = 'comparison-back';
  switcher.href = `/blocks/menu/herbology-board.php?track=${isFormula ? 'herbs' : 'formulas'}`;
  switcher.textContent = isFormula
    ? (spanish ? 'Hierbas individuales' : 'Single Herbs')
    : (spanish ? 'Fórmulas' : 'Formulas');

  const dashboard = document.createElement('a');
  dashboard.className = 'comparison-back';
  dashboard.href = '/my/';
  dashboard.textContent = 'Dashboard';

  actions.replaceChildren(...(language ? [language] : []), switcher, dashboard);
  actions.style.display = 'flex';
  actions.style.gap = '8px';
  actions.style.flexWrap = 'wrap';
})();
