(() => {
  'use strict';

  const requestedAnchor = String(window.studyGuideRequestedAnchor || '').trim();
  if (requestedAnchor) return;

  const openChannelsIntroduction = () => {
    const introduction = document.getElementById('sgv2-introduction');
    if (!introduction) return;

    window.scrollTo({ top: 0, left: 0, behavior: 'auto' });

    const introductionLink = document.querySelector(
      '.sgv2-navigation a[href="#sgv2-introduction"]'
    );
    document
      .querySelectorAll('.sgv2-navigation a[aria-current="location"]')
      .forEach((link) => link.removeAttribute('aria-current'));
    introductionLink?.setAttribute('aria-current', 'location');
  };

  if ('scrollRestoration' in history) history.scrollRestoration = 'manual';

  window.addEventListener('load', () => {
    requestAnimationFrame(() => requestAnimationFrame(openChannelsIntroduction));
  });

  window.addEventListener('pageshow', (event) => {
    if (event.persisted) requestAnimationFrame(openChannelsIntroduction);
  });
})();
