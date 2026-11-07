(function() {
  'use strict';

  const currentUrl = new URL(window.location.href);
  const stepBackScrollKey = 'herbology-step-back-scroll';
  const revealStepBackDestination = function(event) {
    try {
      if (window.sessionStorage.getItem(stepBackScrollKey) !== 'top') {
        return;
      }
      window.sessionStorage.removeItem(stepBackScrollKey);
      if (event && event.persisted) {
        window.location.reload();
        return;
      }
      window.scrollTo(0, 0);
      window.requestAnimationFrame(function() {
        window.scrollTo(0, 0);
      });
    } catch (error) {
      // Navigation still works when session storage is unavailable.
    }
  };
  window.addEventListener('pageshow', revealStepBackDestination);
  revealStepBackDestination();
  const spanish = document.documentElement.lang.toLowerCase().startsWith('es')
    || /-es\.html$/i.test(currentUrl.pathname);
  const copy = spanish ? {
    library: 'Biblioteca',
    libraryName: 'Biblioteca de Herbología',
    allResources: 'Inicio de Library',
    studyGuides: 'Guías de estudio',
    practice: 'Herbology Practice',
    quiz: 'Quiz',
    exams: 'Exámenes',
    back: '← Atrás',
    current: 'Página actual',
    navigation: 'Navegación de Library',
    openNavigation: 'Ver navegación',
    closeNavigation: 'Ocultar navegación'
  } : {
    library: 'Library',
    libraryName: 'Herbology Library',
    allResources: 'Library home',
    studyGuides: 'Study Guides',
    practice: 'Herbology Practice',
    quiz: 'Quiz',
    exams: 'Exams',
    back: '← Back',
    current: 'Current page',
    navigation: 'Library navigation',
    openNavigation: 'Show navigation',
    closeNavigation: 'Hide navigation'
  };
  const languageParams = spanish ? '&lang=es' : '';
  const libraryUrl = '/blocks/myprogress/study-guide.php?area=herbology&view=library' + languageParams;
  const routes = [
    [copy.allResources, libraryUrl],
    [copy.studyGuides, libraryUrl + '#library-group-core-guides'],
    [copy.practice, '/blocks/menu/herbology-board.php?track=herbs&from=library' + languageParams],
    [copy.quiz, '/blocks/menu/herbology-board.php?track=quiz&from=library' + languageParams],
    [copy.exams, '/blocks/menu/herbology-board.php?track=exams&from=library' + languageParams]
  ];
  const currentFile = currentUrl.pathname.split('/').pop() || '';
  const baseFile = currentFile.replace(/-es\.html$/i, '.html');
  const title = (document.querySelector('main h1,h1')?.textContent || document.title)
    .replace(/\s+/g, ' ')
    .trim();
  const collectionHubs = {
    'dui-yao.html': ['Duì Yào Pairs', 'dui-yao'],
    'herb-comparison-bank.html': [spanish ? 'Banco comparativo de hierbas' : 'Herbal Comparison Study Bank', 'herb-comparison-bank'],
    'formula-navigator.html': [spanish ? 'Centro de estudio de fórmulas' : 'Formula Study Center', 'formula-navigator'],
    'formula-practice.html': [spanish ? 'Quiz de fórmulas' : 'Formula Quiz', 'formula-practice'],
    'families.html': [spanish ? 'Familias de fórmulas' : 'Formula Families', 'families'],
    'name-traps.html': [spanish ? 'Trampas de nombres' : 'Name Traps', 'name-traps'],
    'herb-families.html': [spanish ? 'Familias y partes de hierbas' : 'Herb Families & Parts', 'herb-families'],
    'safety.html': [spanish ? 'Biblioteca de seguridad' : 'Safety Library', 'safety'],
    'formula-name-traps.html': [spanish ? 'Trampas de nombres de fórmulas' : 'Formula Name Traps', 'formula-name-traps'],
    'misleading-formula-names.html': [spanish ? 'Nombres engañosos de fórmulas' : 'Misleading Formula Names', 'misleading-formula-names'],
    'advanced-name-traps.html': [spanish ? 'Trampas avanzadas de nombres' : 'Advanced Name Traps', 'advanced-name-traps'],
    'supplemental-herb-families.html': [spanish ? 'Familias herbales suplementarias' : 'Supplemental Herb Families', 'supplemental-herb-families'],
    'patterns-tips-maciocia.html': [spanish ? 'Pistas de patrones Maciocia' : 'Patterns Tips Maciocia', 'patterns-tips-maciocia'],
    'most-important-formulas.html': [spanish ? 'Fórmulas más importantes' : 'Most Important Formulas', 'most-important-formulas'],
    'formula-theory.html': [spanish ? 'Composición de fórmulas' : 'Formula Composition', 'formula-theory'],
    'herbal-incompatibilities.html': [spanish ? 'Incompatibilidades herbales' : 'Herbal Incompatibilities', 'herbal-incompatibilities'],
    'secondary-actions.html': [spanish ? 'Acciones secundarias' : 'Secondary Actions', 'secondary-actions'],
    'formulas.html': [spanish ? 'Comparaciones de fórmulas' : 'Formula Comparisons', 'formulas'],
    'index.html': [spanish ? 'Comparaciones de hierbas individuales' : 'Single Herbs Comparisons', 'index'],
    'pathologies.html': [spanish ? 'Comparaciones de patologías' : 'Pathology Comparisons', 'pathologies'],
    'patterns.html': [spanish ? 'Comparaciones de patrones' : 'Pattern Comparisons', 'patterns'],
    'formula-modifications.html': [spanish ? 'Modificaciones de fórmulas' : 'Formula Modifications', 'formula-modifications']
  };

  const htmlEscape = function(value) {
    return String(value).replace(/[&<>"']/g, function(character) {
      return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[character];
    });
  };
  const localComparisonUrl = function(link) {
    try {
      const url = new URL(link.href, currentUrl);
      return url.origin === currentUrl.origin
        && url.pathname.startsWith('/study-guide/comparisons/')
        && /\.html$/i.test(url.pathname)
        ? url
        : null;
    } catch (error) {
      return null;
    }
  };

  const brand = document.querySelector('.comparison-topbar .comparison-brand');
  const originalBrandUrl = brand ? localComparisonUrl(brand) : null;
  const originalBrandLabel = brand?.textContent.replace(/\s+/g, ' ').trim().replace(/^☯\s*/, '') || '';
  let collection = collectionHubs[baseFile] || null;
  if (!collection && originalBrandUrl) {
    const originalFile = originalBrandUrl.pathname.split('/').pop() || '';
    const normalizedOriginalFile = originalFile.replace(/-es\.html$/i, '.html');
    collection = collectionHubs[normalizedOriginalFile]
      || [originalBrandLabel || copy.library, normalizedOriginalFile.replace(/\.html$/i, '')];
  }
  if (!collection) {
    collection = [copy.library, ''];
  }
  const collectionFile = collection[1]
    ? collection[1] + (spanish ? '-es.html' : '.html')
    : '';
  const onCollectionHub = collectionFile === currentFile;

  const preserveLibraryContext = function(link) {
    const destination = localComparisonUrl(link);
    if (!destination) {
      return;
    }
    destination.searchParams.set('from', 'library');
    link.href = destination.toString();
  };
  document.querySelectorAll('a[href]').forEach(function(link) {
    preserveLibraryContext(link);
  });
  document.addEventListener('click', function(event) {
    const link = event.target.closest('a[href]');
    if (link) {
      preserveLibraryContext(link);
    }
  }, true);

  document.querySelectorAll('nav[aria-label="Study path"],nav[aria-label="Ruta de estudio"],.study-note-route').forEach(function(route) {
    route.remove();
  });
  document.querySelectorAll('.comparison-topbar .comparison-back').forEach(function(link) {
    let path = '';
    try {
      path = new URL(link.href, currentUrl).pathname;
    } catch (error) {
      path = '';
    }
    if (/^\/my\/?$/i.test(path)) {
      link.href = libraryUrl;
      link.textContent = spanish ? '← Volver a Library' : '← Back to Library';
    } else if (/\/(?:single-herbs-study-guide|formulas-study-guide|herb-formula-category-map)\.php$/i.test(path)) {
      link.remove();
    }
  });
  if (brand) {
    brand.href = libraryUrl;
    const label = brand.querySelector('span:last-child');
    if (label) {
      label.textContent = copy.libraryName;
    } else {
      brand.textContent = copy.libraryName;
    }
  }

  const style = document.createElement('style');
  style.dataset.libraryContextNavigationStyles = '';
  style.textContent = `
    .library-context-nav{margin:18px 0 16px;padding:14px;border:1px solid #b8d5d2;border-radius:11px;background:#f7fcfb;box-shadow:0 3px 12px rgba(15,23,42,.04)}
    .library-context-nav__top{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 0 11px}.library-context-nav__trail{display:flex;align-items:center;gap:8px;flex-wrap:wrap;color:#64748b;font-size:12px;font-weight:800}.library-context-nav__trail a{color:#245fa7;text-decoration:none}.library-context-nav__trail a:hover{text-decoration:underline}.library-context-nav__step-back{padding-right:8px;border-right:1px solid #bfd0da}.library-context-nav__trail-current{color:#17232a}.library-context-nav__toggle{display:none;min-height:40px;align-items:center;justify-content:center;padding:7px 11px;border:1px solid #0b817d;border-radius:8px;background:#fff;color:#075f5b;font-size:12px;font-weight:850;cursor:pointer}
    .library-context-nav__routes{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px}.library-context-nav__route{display:flex;min-height:44px;align-items:center;justify-content:center;padding:8px 10px;border:1px solid #bfd0da;border-radius:8px;background:#fff;color:#17344a;font-size:12px;font-weight:850;text-align:center;text-decoration:none}.library-context-nav__route:hover,.library-context-nav__route:focus{border-color:#0b817d;background:#edf9f6;color:#075f5b;text-decoration:none}.library-context-nav__route--library{border-color:#0b817d;background:#0b817d;color:#fff}.library-context-nav__route--library:hover,.library-context-nav__route--library:focus{background:#076b67;color:#fff}
    @media(max-width:700px){.library-context-nav{margin:12px 0;padding:11px}.library-context-nav__top{margin:0}.library-context-nav__trail{min-width:0}.library-context-nav__toggle{display:inline-flex;flex:0 0 auto}.library-context-nav__routes{display:none;grid-template-columns:1fr 1fr;margin-top:11px}.library-context-nav[data-expanded="true"] .library-context-nav__routes{display:grid}.library-context-nav__route{min-height:48px}.library-context-nav__route:last-child{grid-column:1/-1}}
  `;
  document.head.appendChild(style);

  const panel = document.createElement('nav');
  panel.className = 'library-context-nav';
  panel.dataset.libraryContextNavigation = '';
  panel.setAttribute('aria-label', copy.navigation);
  let trail = '<a class="library-context-nav__step-back" data-library-step-back href="' + htmlEscape(libraryUrl) + '">' + htmlEscape(copy.back) + '</a>'
    + '<a href="' + htmlEscape(libraryUrl) + '">' + htmlEscape(copy.library) + '</a>';
  if (collectionFile) {
    trail += '<span aria-hidden="true">›</span>';
    trail += onCollectionHub
      ? '<span class="library-context-nav__trail-current" aria-current="page">' + htmlEscape(collection[0]) + '</span>'
      : '<a href="' + htmlEscape(collectionFile + '?from=library') + '">' + htmlEscape(collection[0]) + '</a>';
  }
  if (!onCollectionHub) {
    trail += '<span aria-hidden="true">›</span><span class="library-context-nav__trail-current" aria-current="page">' + htmlEscape(title || copy.current) + '</span>';
  }
  panel.innerHTML = '<div class="library-context-nav__top"><div class="library-context-nav__trail">' + trail + '</div>'
    + '<button class="library-context-nav__toggle" type="button" aria-expanded="false" aria-controls="library-context-routes">' + htmlEscape(copy.openNavigation) + '</button></div>'
    + '<div class="library-context-nav__routes" id="library-context-routes">'
    + routes.map(function(route, index) {
      return '<a class="library-context-nav__route' + (index === 0 ? ' library-context-nav__route--library' : '') + '" href="' + htmlEscape(route[1]) + '">' + htmlEscape(route[0]) + '</a>';
    }).join('')
    + '</div>';

  const main = document.querySelector('main.comparison-shell,main');
  if (main) {
    main.insertBefore(panel, main.firstChild);
  } else {
    document.body.insertBefore(panel, document.body.firstChild);
  }
  const toggle = panel.querySelector('.library-context-nav__toggle');
  const stepBack = panel.querySelector('[data-library-step-back]');
  stepBack.addEventListener('click', function(event) {
    if (window.history.length > 1) {
      event.preventDefault();
      try {
        window.sessionStorage.setItem(stepBackScrollKey, 'top');
      } catch (error) {
        // History navigation still works when session storage is unavailable.
      }
      window.history.back();
    }
    // The Library URL on the link remains the safe fallback without history.
  });
  toggle.addEventListener('click', function() {
    const expanded = panel.dataset.expanded !== 'true';
    panel.dataset.expanded = String(expanded);
    toggle.setAttribute('aria-expanded', String(expanded));
    toggle.textContent = expanded ? copy.closeNavigation : copy.openNavigation;
  });
})();
