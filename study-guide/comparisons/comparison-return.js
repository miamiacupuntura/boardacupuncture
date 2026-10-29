(function() {
  'use strict';

  const params = new URLSearchParams(window.location.search);
  const focus = params.get('focus');
  const isSingleHerbsReturn = params.get('from') === 'single-herbs'
    && focus
    && /^\d+$/.test(focus);
  if (!isSingleHerbsReturn) {
    return;
  }

  const isSpanish = document.documentElement.lang.toLowerCase().startsWith('es');
  const guideUrl = new URL('/blocks/myprogress/single-herbs-study-guide.php', window.location.origin);
  guideUrl.searchParams.set('focus', focus);
  if (isSpanish) {
    guideUrl.searchParams.set('lang', 'es');
  }

  const topbar = document.querySelector('.comparison-topbar__inner');
  if (!topbar) {
    return;
  }

  const existingReturnLink = topbar.querySelector('.comparison-back--single-herbs');
  const backLinks = topbar.querySelectorAll('.comparison-back');
  if (!backLinks.length) {
    return;
  }

  const returnLink = existingReturnLink || backLinks[backLinks.length - 1];
  returnLink.classList.add('comparison-back--single-herbs');
  returnLink.href = guideUrl.toString();
  returnLink.textContent = isSpanish
    ? '← Volver a la guía de Single Herbs'
    : '← Back to Single Herbs Study Guide';

  document.querySelectorAll('a[hreflang]').forEach(function(link) {
    const languageUrl = new URL(link.href, window.location.href);
    languageUrl.searchParams.set('from', 'single-herbs');
    languageUrl.searchParams.set('focus', focus);
    link.href = languageUrl.toString();
  });

})();
