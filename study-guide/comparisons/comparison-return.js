(function() {
  'use strict';

  const params = new URLSearchParams(window.location.search);
  const focus = params.get('focus');
  const guideUrl = new URL('/blocks/myprogress/single-herbs-study-guide.php', window.location.origin);
  if (params.get('from') === 'single-herbs' && focus && /^\d+$/.test(focus)) {
    guideUrl.searchParams.set('focus', focus);
  }

  const topbar = document.querySelector('.comparison-topbar__inner');
  if (!topbar) {
    return;
  }

  const isSpanish = document.documentElement.lang.toLowerCase().startsWith('es');
  const existingReturnLink = topbar.querySelector('.comparison-back--single-herbs');
  if (existingReturnLink) {
    existingReturnLink.href = guideUrl.toString();
    existingReturnLink.textContent = isSpanish
      ? '← Volver a la guía de Single Herbs'
      : '← Back to Single Herbs Study Guide';
  }

  if (params.get('from') === 'single-herbs' && focus) {
    document.querySelectorAll('a[hreflang]').forEach(function(link) {
      const languageUrl = new URL(link.href, window.location.href);
      languageUrl.searchParams.set('from', 'single-herbs');
      languageUrl.searchParams.set('focus', focus);
      link.href = languageUrl.toString();
    });
  }

  if (existingReturnLink) {
    return;
  }

  const returnLink = document.createElement('a');
  returnLink.className = 'comparison-back comparison-back--single-herbs';
  returnLink.href = guideUrl.toString();
  returnLink.textContent = isSpanish
    ? '← Volver a la guía de Single Herbs'
    : '← Back to Single Herbs Study Guide';

  const existingBacks = topbar.querySelectorAll('.comparison-back');
  const comparisonBack = existingBacks.length ? existingBacks[existingBacks.length - 1] : null;
  const actionContainer = comparisonBack && comparisonBack.parentElement !== topbar
    ? comparisonBack.parentElement
    : topbar;
  actionContainer.insertBefore(returnLink, comparisonBack);

})();
