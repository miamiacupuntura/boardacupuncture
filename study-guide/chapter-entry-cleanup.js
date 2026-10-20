(() => {
  'use strict';

  function removeChapterNavigation() {
    document.querySelectorAll('.chapter-selector, .chapter-navigation-rail').forEach(element => element.remove());
    const desktop = window.matchMedia('(min-width:1151px)').matches;
    const phone = window.matchMedia('(max-width:760px)').matches;
    document.querySelectorAll('.sgv2-layout').forEach(layout => {
      const columns = desktop ? '240px minmax(0, 1fr)' : phone ? 'minmax(0, 1fr)' : '';
      if (columns && layout.style.getPropertyValue('grid-template-columns') !== columns) {
        layout.style.setProperty('grid-template-columns', columns, 'important');
      } else if (!columns && layout.style.getPropertyValue('grid-template-columns')) {
        layout.style.removeProperty('grid-template-columns');
      }
    });
  }

  const style = document.createElement('style');
  style.id = 'chapter-entry-cleanup-style';
  style.textContent = `
    .chapter-selector,
    .chapter-navigation-rail{display:none!important}
    @media(min-width:1151px){
      .sgv2-layout,
      #point-categories-draft .sgv2-layout{grid-template-columns:240px minmax(0,1fr)!important}
    }
    @media(max-width:760px){
      .sgv2-layout>.sgv2-navigation,
      #point-categories-draft .sgv2-navigation{display:none!important}
      .sgv2-layout,
      #point-categories-draft .sgv2-layout{grid-template-columns:minmax(0,1fr)!important}
      .sgv2-layout>.sgv2-content,
      #point-categories-draft .sgv2-content{width:100%;min-width:0;max-width:100%}
    }
  `;
  document.head.append(style);

  const initialize = () => setTimeout(removeChapterNavigation, 0);
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize, {once: true});
  } else {
    initialize();
  }
  let queued = false;
  const observer = new MutationObserver(() => {
    if (queued) return;
    queued = true;
    queueMicrotask(() => {
      queued = false;
      removeChapterNavigation();
    });
  });
  const observe = () => observer.observe(document.body, {childList: true, subtree: true, attributes: true, attributeFilter: ['style']});
  if (document.body) observe();
  else document.addEventListener('DOMContentLoaded', observe, {once: true});
  window.addEventListener('resize', removeChapterNavigation);
})();
