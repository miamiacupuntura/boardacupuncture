(() => {
  'use strict';

  const path = window.location.pathname;
  const isFormulaNavigator = /\/formula-navigator(?:-es)?\.html$/.test(path);
  const isFormulaPractice = /\/formula-practice(?:-es)?\.html$/.test(path);
  const isHerb = /\/herb-comparison-bank(?:-es)?\.html$/.test(path);
  if (!isFormulaNavigator && !isFormulaPractice && !isHerb) return;

  const main = document.querySelector('main.comparison-shell');
  const hero = main && main.querySelector(':scope > .comparison-hero');
  if (!main || !hero || main.querySelector('.practice-linear-nav')) return;

  const spanish = document.documentElement.lang === 'es';
  const isFormula = isFormulaNavigator || isFormulaPractice;
  const track = isFormula ? 'formulas' : 'herbs';
  const hubUrl = `/blocks/menu/herbology-board.php?track=${track}`;

  function returnUrl() {
    const requested = new URLSearchParams(window.location.search).get('return');
    if (requested) {
      try {
        const target = new URL(requested, window.location.origin);
        if (target.origin === window.location.origin && target.href !== window.location.href) return target.href;
      } catch (_) {}
    }
    if (document.referrer) {
      try {
        const previous = new URL(document.referrer);
        if (previous.href !== window.location.href) return previous.href;
      } catch (_) {}
    }
    return hubUrl;
  }

  if (!document.getElementById('practice-linear-nav-styles')) {
    const styles = document.createElement('style');
    styles.id = 'practice-linear-nav-styles';
    styles.textContent = `
      .practice-linear-nav{display:grid;grid-template-columns:auto minmax(0,1fr);align-items:center;gap:12px;margin:18px 0 12px;padding:10px 13px;border:1px solid #d5e0e5;border-radius:9px;background:#fff;color:#64748b;font-size:12px;font-weight:800;box-shadow:0 2px 8px rgba(15,23,42,.04)}
      .practice-linear-nav__back{display:inline-flex;min-height:34px;align-items:center;padding:6px 11px;border:1px solid #cbd5dc;border-radius:7px;background:#f8fafc;color:#17324d;text-decoration:none;white-space:nowrap}
      .practice-linear-nav__back:hover{border-color:#08756a;color:#075e54;text-decoration:none}
      .practice-linear-nav__trail{display:flex;min-width:0;align-items:center;gap:8px;overflow-x:auto;white-space:nowrap;scrollbar-width:thin}
      .practice-linear-nav__trail a{color:#315fa5;text-decoration:none}.practice-linear-nav__trail a:hover{text-decoration:underline}
      .practice-linear-nav__trail a.is-active{color:#075e54;font-weight:900}
      .practice-linear-nav__trail strong{color:#17232a}.practice-linear-nav__separator{color:#94a3b8}
      @media(max-width:600px){.practice-linear-nav{gap:8px;margin:10px 0;padding:8px}.practice-linear-nav__back{min-height:32px;padding:5px 8px;font-size:11px}.practice-linear-nav__trail{gap:6px;font-size:11px}}
    `;
    document.head.appendChild(styles);
  }

  const nav = document.createElement('nav');
  nav.className = 'practice-linear-nav';
  nav.setAttribute('aria-label', spanish ? 'Ruta de navegación de práctica' : 'Practice navigation path');

  const back = document.createElement('a');
  back.className = 'practice-linear-nav__back';
  back.href = returnUrl();
  back.textContent = spanish ? '← Regresar' : '← Back';

  const trail = document.createElement('div');
  trail.className = 'practice-linear-nav__trail';

  const practiceHome = document.createElement('a');
  practiceHome.href = hubUrl;
  practiceHome.textContent = spanish ? 'Exámenes de práctica' : 'Practice Exams';

  const herbsLink = document.createElement('a');
  herbsLink.href = '/blocks/menu/herbology-board.php?track=herbs';
  herbsLink.textContent = spanish ? 'Hierbas individuales' : 'Single Herbs';
  if (isHerb) herbsLink.className = 'is-active';

  const formulasLink = document.createElement('a');
  formulasLink.href = '/blocks/menu/herbology-board.php?track=formulas';
  formulasLink.textContent = spanish ? 'Fórmulas' : 'Formulas';
  if (isFormula) formulasLink.className = 'is-active';

  const siblingSeparator = document.createElement('span');
  siblingSeparator.className = 'practice-linear-nav__separator';
  siblingSeparator.setAttribute('aria-hidden', 'true');
  siblingSeparator.textContent = '|';

  const current = document.createElement('strong');
  current.setAttribute('aria-current', 'page');
  current.textContent = isFormulaNavigator
    ? (spanish ? 'Buscar una fórmula' : 'Find a formula')
    : isFormulaPractice
      ? (spanish ? 'Preguntas de práctica' : 'Practice questions')
      : (spanish ? 'Banco comparativo' : 'Comparison bank');

  const separator = () => {
    const item = document.createElement('span');
    item.className = 'practice-linear-nav__separator';
    item.setAttribute('aria-hidden', 'true');
    item.textContent = '›';
    return item;
  };

  trail.append(practiceHome, separator(), herbsLink, siblingSeparator, formulasLink, separator(), current);
  nav.append(back, trail);
  main.insertBefore(nav, hero);
})();
