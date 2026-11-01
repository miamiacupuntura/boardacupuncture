(function() {
  'use strict';

  const params = new URLSearchParams(window.location.search);
  const focus = params.get('focus');
  const isSingleHerbsReturn = params.get('from') === 'single-herbs'
    && focus
    && /^\d+$/.test(focus);
  const formulaGuideLabels = {
    'release-exterior': {
      en: '← Back to Release the Exterior',
      es: '← Volver a Liberar el Exterior'
    },
    'clear-heat': {
      en: '← Back to Clear Heat',
      es: '← Volver a Aclarar Calor'
    },
    'purge': {
      en: '← Back to Purge',
      es: '← Volver a Purgar'
    },
    'harmonize': {
      en: '← Back to Harmonize',
      es: '← Volver a Armonizar'
    },
    'expel-damp': {
      en: '← Back to Expel Dampness',
      es: '← Volver a Eliminar Humedad'
    },
    'warm-interior': {
      en: '← Back to Warm the Interior',
      es: '← Volver a Calentar el Interior'
    },
    'tonify': {
      en: '← Back to Tonify',
      es: '← Volver a Tonificar'
    },
    'regulate-qi': {
      en: '← Back to Regulate Qi',
      es: '← Volver a Regular el Qi'
    },
    'invigorate-blood': {
      en: '← Back to Invigorate Blood',
      es: '← Volver a Movilizar Sangre'
    },
    'stabilize-bind': {
      en: '← Back to Stabilize and Bind',
      es: '← Volver a Estabilizar y contener'
    },
    'calm-shen': {
      en: '← Back to Calm Shen',
      es: '← Volver a Calmar el Shen'
    },
    'expel-wind': {
      en: '← Back to Expel Wind',
      es: '← Volver a Expulsar Viento'
    },
    'transform-phlegm': {
      en: '← Back to Transform Phlegm',
      es: '← Volver a Transformar Flema'
    },
    'digestive': {
      en: '← Back to Reduce Food Stagnation',
      es: '← Volver a Reducir retención de alimentos'
    }
  };
  const isFormulaGuideReturn = params.get('from') === 'formula-study-guide'
    && Object.prototype.hasOwnProperty.call(formulaGuideLabels, focus);
  if (!isSingleHerbsReturn && !isFormulaGuideReturn) {
    return;
  }

  const isSpanish = document.documentElement.lang.toLowerCase().startsWith('es');
  const relatedPairSlugs = (params.get('related-pairs') || '')
    .split(',')
    .filter(function(slug) {
      return /^[a-z0-9-]+$/.test(slug);
    });
  const currentPageName = window.location.pathname.split('/').pop() || '';
  const currentDuiYaoSlug = currentPageName.replace(/-es\.html$/, '').replace(/\.html$/, '');
  const isDuiYaoIndex = currentDuiYaoSlug === 'dui-yao';
  const isRelatedFormulaPair = relatedPairSlugs.includes(currentDuiYaoSlug);
  const shouldNameFormulaCategory = isDuiYaoIndex || isRelatedFormulaPair;
  const guideUrl = new URL(
    isFormulaGuideReturn
      ? '/blocks/myprogress/formulas-study-guide.php'
      : '/blocks/myprogress/single-herbs-study-guide.php',
    window.location.origin
  );
  guideUrl.searchParams.set('focus', focus);
  if (isSpanish) {
    guideUrl.searchParams.set('lang', 'es');
  }
  if (isFormulaGuideReturn) {
    guideUrl.hash = 'related-dui-yao-title';
  }

  const topbar = document.querySelector('.comparison-topbar__inner');
  if (!topbar) {
    return;
  }

  const returnClass = isFormulaGuideReturn
    ? 'comparison-back--formula-study-guide'
    : 'comparison-back--single-herbs';
  const existingReturnLink = topbar.querySelector('.' + returnClass);
  const backLinks = topbar.querySelectorAll('.comparison-back');
  if (!backLinks.length) {
    return;
  }

  let returnLink = existingReturnLink;
  if (!returnLink && isFormulaGuideReturn) {
    returnLink = document.createElement('a');
    returnLink.className = 'comparison-back ' + returnClass;
    const actionGroup = backLinks[0].parentElement;
    (actionGroup || topbar).appendChild(returnLink);
  } else if (!returnLink) {
    returnLink = backLinks[backLinks.length - 1];
    returnLink.classList.add(returnClass);
  }
  returnLink.href = guideUrl.toString();
  returnLink.textContent = isFormulaGuideReturn
    ? (shouldNameFormulaCategory
      ? formulaGuideLabels[focus][isSpanish ? 'es' : 'en']
      : (isSpanish ? '← Volver a la guía de fórmulas' : '← Back to Formula Study Guide'))
    : (isSpanish ? '← Volver a la guía de Single Herbs' : '← Back to Single Herbs Study Guide');

  if (isFormulaGuideReturn) {
    document.querySelectorAll('a[href]').forEach(function(link) {
      if (link === returnLink) {
        return;
      }

      const destination = new URL(link.href, window.location.href);
      const isInternalDuiYaoPage = destination.origin === window.location.origin
        && /\/(?:dui-yao(?:-es)?|[^/]+-dui-yao(?:-es)?)\.html$/.test(destination.pathname);
      if (!isInternalDuiYaoPage) {
        return;
      }

      destination.searchParams.set('from', 'formula-study-guide');
      destination.searchParams.set('focus', focus);
      if (relatedPairSlugs.length) {
        destination.searchParams.set('related-pairs', relatedPairSlugs.join(','));
      } else {
        destination.searchParams.delete('related-pairs');
      }
      link.href = destination.toString();
    });
  } else {
    topbar.querySelectorAll('.comparison-back').forEach(function(link) {
      if (link === returnLink) {
        return;
      }
      const destination = new URL(link.href, window.location.href);
      destination.searchParams.set('from', 'single-herbs');
      destination.searchParams.set('focus', focus);
      link.href = destination.toString();
    });
  }

})();
