(function () {
  'use strict';

  function viewButton(label) {
    return Array.from(document.querySelectorAll('.preview-view-switch button')).find(button => button.textContent.trim() === label);
  }
  function addToggle(hero) {
    if (!hero || hero.querySelector('.chapter-language-toggle')) return;
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'chapter-language-toggle';
    button.addEventListener('click', () => {
      const spanish = document.documentElement.lang.toLowerCase().startsWith('es');
      const destination = spanish ? viewButton('Student') : (viewButton('Estudiante') || viewButton('Español'));
      if (destination) destination.click();
    });
    hero.append(button);
  }
  function update() {
    document.querySelectorAll('#sgv2-introduction, #pc-introduction').forEach(addToggle);
    const spanish = document.documentElement.lang.toLowerCase().startsWith('es');
    document.querySelectorAll('.chapter-language-toggle').forEach(button => {
      button.textContent = spanish ? 'English' : 'Español';
      button.setAttribute('aria-label', spanish ? 'View this chapter in English' : 'Ver este capítulo en español');
      button.lang = spanish ? 'en' : 'es';
    });
  }
  const style = document.createElement('style');
  style.textContent = '.chapter-language-toggle{position:absolute;right:20px;bottom:20px;z-index:3;min-height:44px;padding:9px 16px;border:2px solid #087774;border-radius:999px;background:#fff;color:#075f60;font:700 1rem/1 system-ui,sans-serif;cursor:pointer;box-shadow:0 3px 10px #173b4420}.chapter-language-toggle:hover,.chapter-language-toggle:focus-visible{background:#087774;color:#fff;outline:3px solid #ffb15b;outline-offset:2px}#sgv2-introduction,#pc-introduction{position:relative}#sgv2-introduction h1,#sgv2-introduction h2,#pc-introduction h1,#pc-introduction h2{padding-right:0}@media(max-width:600px){.chapter-language-toggle{position:static;width:100%;margin-top:14px;min-height:48px}#sgv2-introduction h1,#sgv2-introduction h2,#pc-introduction h1,#pc-introduction h2{padding-right:0}}';
  document.head.append(style);
  new MutationObserver(update).observe(document.documentElement, { attributes: true, attributeFilter: ['lang'] });
  update();
})();
