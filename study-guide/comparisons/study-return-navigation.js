(function () {
  'use strict';

  const topbar = document.querySelector('.comparison-topbar__inner');
  if (!topbar) {
    return;
  }

  const spanish = document.documentElement.lang.toLowerCase().startsWith('es')
    || /-es\.html$/.test(window.location.pathname);
  const libraryUrl = '/blocks/myprogress/study-guide.php?area=herbology&view=library';
  const studyGuideUrl = '/blocks/myprogress/study-guide.php?area=herbology';
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

  const links = [...actions.querySelectorAll('a')];
  let studyGuideLink = links.find(link => {
    const url = new URL(link.href, window.location.href);
    return url.pathname === '/blocks/myprogress/study-guide.php'
      && url.searchParams.get('area') === 'herbology'
      && url.searchParams.get('view') !== 'library';
  });

  if (!studyGuideLink) {
    studyGuideLink = document.createElement('a');
    studyGuideLink.className = 'comparison-back';
    studyGuideLink.href = studyGuideUrl;
    actions.prepend(studyGuideLink);
  }
  studyGuideLink.textContent = spanish ? '← Guía de estudio' : '← Study Guide';

  const libraryLink = links.find(link => {
    const url = new URL(link.href, window.location.href);
    return url.pathname === '/blocks/myprogress/study-guide.php'
      && url.searchParams.get('area') === 'herbology'
      && url.searchParams.get('view') === 'library';
  });

  if (!libraryLink) {
    const link = document.createElement('a');
    link.className = 'comparison-back';
    link.href = libraryUrl;
    link.textContent = spanish ? '← Biblioteca' : '← Library';
    actions.prepend(link);
  }
})();
