(() => {
  'use strict';

  const topbar = document.querySelector('.comparison-topbar__inner');
  if (!topbar) return;

  const path = window.location.pathname;
  const isFormula = /\/formula-(?:practice|navigator)(?:-es)?\.html$/.test(path);
  const isHerb = /\/herb-comparison-bank(?:-es)?\.html$/.test(path);
  if (!isFormula && !isHerb) return;

  const spanish = document.documentElement.lang === 'es';
  const brand = topbar.querySelector('.comparison-brand');
  if (!brand) return;

  brand.href = `/blocks/menu/herbology-board.php?track=${isFormula ? 'formulas' : 'herbs'}`;
  const brandLabel = brand.querySelector('span:last-child');
  if (brandLabel) {
    brandLabel.textContent = isFormula
      ? (spanish ? 'Práctica de fórmulas' : 'Formulas Practice')
      : (spanish ? 'Práctica de hierbas individuales' : 'Single Herbs Practice');
  }

  if (!document.getElementById('practice-area-panel-styles')) {
    const styles = document.createElement('style');
    styles.id = 'practice-area-panel-styles';
    styles.textContent = `
      .practice-area-panel{display:flex;align-items:center;gap:9px;margin:-9px 0 18px;padding:12px 14px;border:1px solid #cbdce7;border-radius:11px;background:#fff;box-shadow:0 3px 10px rgba(15,23,42,.05)}
      .practice-area-panel__label{margin-right:auto;color:#52677a;font-size:12px;font-weight:900;letter-spacing:.06em;text-transform:uppercase}
      .practice-area-panel a{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:8px 15px;border:1px solid #b9ccda;border-radius:8px;background:#fff;color:#17324d;font-weight:850;text-decoration:none}
      .practice-area-panel a[aria-current="page"]{border-color:#08756a;background:#08756a;color:#fff}
      .practice-area-panel a:hover{border-color:#08756a;text-decoration:none}
      @media(max-width:600px){.practice-area-panel{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-top:-5px;padding:10px}.practice-area-panel__label{grid-column:1/-1;margin:0;font-size:10px}.practice-area-panel a{width:100%;min-height:38px;padding:7px 8px;font-size:13px}}
    `;
    document.head.appendChild(styles);
  }

  const main = document.querySelector('main.comparison-shell');
  const hero = main && main.querySelector(':scope > .comparison-hero');
  if (hero && !main.querySelector('.practice-area-panel')) {
    const panel = document.createElement('nav');
    panel.className = 'practice-area-panel';
    panel.setAttribute('aria-label', spanish ? 'Navegación de práctica de herbolaria' : 'Herbology practice navigation');

    const label = document.createElement('span');
    label.className = 'practice-area-panel__label';
    label.textContent = spanish ? 'Área de práctica' : 'Practice area';

    const herbs = document.createElement('a');
    herbs.href = '/blocks/menu/herbology-board.php?track=herbs';
    herbs.textContent = spanish ? 'Hierbas individuales' : 'Single Herbs';
    if (isHerb) herbs.setAttribute('aria-current', 'page');

    const formulas = document.createElement('a');
    formulas.href = '/blocks/menu/herbology-board.php?track=formulas';
    formulas.textContent = spanish ? 'Fórmulas' : 'Formulas';
    if (isFormula) formulas.setAttribute('aria-current', 'page');

    panel.append(label, herbs, formulas);
    hero.insertAdjacentElement('afterend', panel);
  }
})();
