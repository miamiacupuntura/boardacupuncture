(function() {
  'use strict';

  const params = new URLSearchParams(window.location.search);
  const focus = params.get('focus');
  if (params.get('from') !== 'single-herbs' || !focus || !/^\d+$/.test(focus)) {
    return;
  }

  const isSpanish = document.documentElement.lang.toLowerCase().startsWith('es');
  const guideUrl = new URL('/blocks/myprogress/single-herbs-study-guide.php', window.location.origin);
  guideUrl.searchParams.set('focus', focus);
  if (isSpanish) {
    guideUrl.searchParams.set('lang', 'es');
  }

  const actions = document.querySelector('.comparison-topbar__inner');
  if (!actions) {
    return;
  }

  const existingReturnLink = actions.querySelector('.comparison-back--single-herbs');
  const backLinks = actions.querySelectorAll('.comparison-back');
  if (!backLinks.length) {
    return;
  }

  const returnLink = existingReturnLink || backLinks[backLinks.length - 1];
  returnLink.classList.add('comparison-back--single-herbs');
  returnLink.href = guideUrl.toString();
  returnLink.textContent = isSpanish
    ? '← Volver a la guía de Single Herbs'
    : '← Back to Single Herbs Study Guide';

  backLinks.forEach(function(link) {
    if (link === returnLink) {
      return;
    }
    const alternate = new URL(link.href, window.location.href);
    alternate.searchParams.set('from', 'single-herbs');
    alternate.searchParams.set('focus', focus);
    link.href = alternate.toString();
  });
})();
