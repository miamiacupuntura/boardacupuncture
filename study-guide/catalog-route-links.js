(function () {
  'use strict';

  const params = new URLSearchParams(window.location.search);
  const track = window.ACUPUNCTURE_ENTRY_TRACK || params.get('track') || 'board';
  const storedReturnRoute = sessionStorage.getItem('acupuncture-study-guide-return') || '';
  const returnRoute = params.get('return') || storedReturnRoute;
  if (storedReturnRoute) sessionStorage.removeItem('acupuncture-study-guide-return');
  window.ACUPUNCTURE_RETURN_ROUTE = returnRoute;

  function destination() {
    const spanish = document.documentElement.lang.toLowerCase().startsWith('es');
    if (track === 'continuing-education') {
      return {
        href: returnRoute === 'ce-preview'
          ? 'continuing-education.html?preview=approved'
          : 'continuing-education.html',
        label: spanish
          ? '← Volver a los cursos de Continuing Education'
          : '← Back to Continuing Education courses'
      };
    }
    if (returnRoute === 'board-catalog') {
      return {
        href: 'board-courses.html',
        label: spanish
          ? '← Volver a los cursos de preparación para el Board'
          : '← Back to Board preparation courses'
      };
    }
    return {
      href: '/blocks/myprogress/study-guide.php?area=pointlocation',
      label: spanish
        ? '← Volver al índice de Study Guide'
        : '← Back to Study Guide index'
    };
  }

  function connectBackLinks() {
    const route = destination();
    document.querySelectorAll(
      '.point-categories-hero-back, .study-guide-mobile-index-back a'
    ).forEach(link => {
      if (link.getAttribute('href') !== route.href) link.href = route.href;
      if (link.textContent.trim() !== route.label) link.textContent = route.label;
      link.setAttribute('aria-label', route.label.replace(/^←\s*/, ''));
    });
  }

  connectBackLinks();
  document.addEventListener('click', event => {
    if (event.target.closest('.chapter-selector button, .chapter-navigation-link')) {
      setTimeout(connectBackLinks, 0);
    }
  }, true);
  new MutationObserver(connectBackLinks).observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['lang']
  });
})();
