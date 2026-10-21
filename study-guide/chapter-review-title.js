(() => {
  'use strict';

  const titles = {
    en: 'Exam Chapter Review (Lecture)',
    es: 'Repaso del examen del capítulo (lección)'
  };

  function applyTitle() {
    const language = document.documentElement.lang.toLowerCase().startsWith('es') ? 'es' : 'en';
    const title = titles[language];
    const selectors = [
      '#chapter-mock-review > summary',
      '#pc-simulation > summary',
      'a[href="#chapter-mock-review"]',
      'a[href="#pc-simulation"]'
    ];
    document.querySelectorAll(selectors.join(',')).forEach(element => {
      if (element.textContent.trim() !== title) element.textContent = title;
      if (element.tagName === 'A') element.title = title;
    });
  }

  const initialize = () => applyTitle();
  applyTitle();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize, {once: true});
  } else {
    initialize();
  }
  new MutationObserver(applyTitle).observe(document.documentElement, {attributes: true, attributeFilter: ['lang']});
})();
