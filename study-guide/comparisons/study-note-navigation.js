(function() {
  'use strict';

  const note = document.querySelector('article.study-note');
  const main = note && note.closest('main');
  if (!note || !main || document.querySelector('[data-study-note-navigation]')) {
    return;
  }

  const spanish = document.documentElement.lang.toLowerCase().startsWith('es')
    || /-es\.html$/i.test(window.location.pathname);
  const copy = spanish ? {
    studyGuide: 'Guía de estudio',
    previous: '← Anterior',
    next: 'Siguiente →',
    jump: 'Ir a…',
    loading: 'Cargando recorrido…',
    collection: 'Colección',
    unavailable: 'No disponible'
  } : {
    studyGuide: 'Study Guide',
    previous: '← Previous',
    next: 'Next →',
    jump: 'Jump to…',
    loading: 'Loading path…',
    collection: 'Collection',
    unavailable: 'Unavailable'
  };
  const currentUrl = new URL(window.location.href);
  const currentFile = currentUrl.pathname.split('/').pop() || '';
  const currentTitle = (note.querySelector('.study-note__title,h1')?.textContent || document.title)
    .replace(/\s+/g, ' ')
    .trim();

  const style = document.createElement('style');
  style.dataset.studyNoteNavigationStyles = '';
  style.textContent = `
    .study-note-route{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:18px 0 12px;padding:11px 14px;border:1px solid #d9e2e7;border-radius:9px;background:#fff;color:#64748b;font-size:12px;font-weight:750}
    .study-note-route a{color:#315fa5;text-decoration:none}.study-note-route a:hover{text-decoration:underline}.study-note-route__current{color:#17232a}
    .study-note-activity-nav{position:sticky;top:76px;z-index:20;display:grid;grid-template-columns:minmax(180px,1fr) minmax(250px,1.15fr) minmax(180px,1fr);gap:12px;align-items:stretch;margin:0 0 18px;padding:13px;border:1px solid #d5e0e5;border-radius:12px;background:rgba(255,255,255,.97);box-shadow:0 7px 22px rgba(15,23,42,.1);backdrop-filter:blur(8px)}
    .study-note-activity-nav__link{display:flex;min-height:58px;flex-direction:column;justify-content:center;padding:9px 12px;border:1px solid #dbe4e9;border-radius:9px;background:#fff;color:#17344a;text-decoration:none}.study-note-activity-nav__link:hover{border-color:#f77e25;color:#17344a;text-decoration:none}.study-note-activity-nav__link--next{text-align:right}.study-note-activity-nav__link[aria-disabled="true"]{visibility:hidden;pointer-events:none}
    .study-note-activity-nav__label{color:#64748b;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.study-note-activity-nav__name{margin-top:3px;color:#e66716;font-size:12px;font-weight:850;line-height:1.35}
    .study-note-activity-nav__jump{display:flex;flex-direction:column;justify-content:center}.study-note-activity-nav__jump label{margin:0 0 4px;color:#64748b;font-size:10px;font-weight:900;letter-spacing:.04em;text-transform:uppercase}.study-note-activity-nav__jump select{width:100%;min-height:40px;padding:7px 34px 7px 10px;border:1px solid #aebdc7;border-radius:8px;background:#fff;color:#17232a;font-weight:750}
    .study-note-activity-nav[data-loading="true"]{opacity:.78}.study-note-activity-nav[data-loading="true"] select{cursor:wait}
    @media(max-width:800px){.study-note-activity-nav{position:static;grid-template-columns:1fr 1fr}.study-note-activity-nav__jump{grid-column:1/-1;grid-row:1}.study-note-activity-nav__link--next{text-align:right}}
  `;
  document.head.appendChild(style);

  const sameDirectory = function(first, second) {
    return first.pathname.slice(0, first.pathname.lastIndexOf('/') + 1)
      === second.pathname.slice(0, second.pathname.lastIndexOf('/') + 1);
  };
  const isAlternateLanguage = function(url) {
    const file = url.pathname.split('/').pop() || '';
    return spanish ? !/-es\.html$/i.test(file) : /-es\.html$/i.test(file);
  };
  const isHtmlCollectionLink = function(url) {
    return url.origin === currentUrl.origin
      && sameDirectory(url, currentUrl)
      && /\.html$/i.test(url.pathname)
      && url.pathname !== currentUrl.pathname
      && !isAlternateLanguage(url);
  };
  const cleanLabel = function(value) {
    return String(value || '')
      .replace(/^(?:←|→)\s*/, '')
      .replace(/^(?:back to|volver a|open|abrir|leer capítulo)\s*/i, '')
      .replace(/\s+/g, ' ')
      .trim();
  };
  const escapeHtml = function(value) {
    return String(value).replace(/[&<>"']/g, function(character) {
      return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[character];
    });
  };

  const headerLinks = [...document.querySelectorAll('.comparison-topbar a[href]')];
  const candidates = headerLinks.map(function(link) {
    return {link: link, url: new URL(link.href, currentUrl), label: cleanLabel(link.textContent)};
  }).filter(function(item) {
    return isHtmlCollectionLink(item.url)
      && !/^(?:english|español)$/i.test(item.link.textContent.trim());
  });
  const preferredCandidate = candidates.find(function(item) {
    return item.link.classList.contains('comparison-brand');
  }) || candidates.find(function(item) {
    return /back|volver|pairs|parejas|formulas|fórmulas|compar|patterns|patrones|famil|safety|seguridad|tramp|navigator|guide|guía/i
      .test(item.link.textContent);
  }) || candidates[0] || null;
  const collectionUrl = preferredCandidate ? preferredCandidate.url : null;
  const collectionLabel = preferredCandidate?.label || copy.collection;

  const route = document.createElement('nav');
  route.className = 'study-note-route';
  route.setAttribute('aria-label', spanish ? 'Ruta de estudio' : 'Study path');
  const studyGuideUrl = '/blocks/myprogress/study-guide.php?area=herbology';
  route.innerHTML = '<a href="' + studyGuideUrl + '">' + escapeHtml(copy.studyGuide) + '</a><span aria-hidden="true">›</span>'
    + (collectionUrl
      ? '<a href="' + escapeHtml(collectionUrl.href) + '">' + escapeHtml(collectionLabel) + '</a><span aria-hidden="true">›</span>'
      : '')
    + '<span class="study-note-route__current">' + escapeHtml(currentTitle) + '</span>';

  const panel = document.createElement('nav');
  panel.className = 'study-note-activity-nav';
  panel.dataset.studyNoteNavigation = '';
  panel.dataset.loading = 'true';
  panel.setAttribute('aria-label', spanish ? 'Navegación de Study Note' : 'Study Note navigation');
  panel.innerHTML = `
    <a class="study-note-activity-nav__link" data-study-note-previous aria-disabled="true">
      <span class="study-note-activity-nav__label">${copy.previous}</span><span class="study-note-activity-nav__name"></span>
    </a>
    <div class="study-note-activity-nav__jump"><label for="study-note-jump">${copy.jump}</label><select id="study-note-jump" data-study-note-jump disabled><option>${copy.loading}</option></select></div>
    <a class="study-note-activity-nav__link study-note-activity-nav__link--next" data-study-note-next aria-disabled="true">
      <span class="study-note-activity-nav__label">${copy.next}</span><span class="study-note-activity-nav__name"></span>
    </a>`;
  main.insertBefore(route, note);
  main.insertBefore(panel, note);

  const previous = panel.querySelector('[data-study-note-previous]');
  const next = panel.querySelector('[data-study-note-next]');
  const jump = panel.querySelector('[data-study-note-jump]');
  const contextualUrl = function(url) {
    const destination = new URL(url.href, currentUrl);
    ['from', 'focus', 'related-pairs'].forEach(function(name) {
      if (currentUrl.searchParams.has(name)) {
        destination.searchParams.set(name, currentUrl.searchParams.get(name));
      }
    });
    return destination.href;
  };
  const setLink = function(link, item) {
    const name = link.querySelector('.study-note-activity-nav__name');
    link.setAttribute('aria-disabled', item ? 'false' : 'true');
    if (!item) {
      name.textContent = '';
      link.removeAttribute('href');
      return;
    }
    link.href = contextualUrl(item.url);
    name.textContent = item.title;
  };

  const discoverItems = function(html, baseUrl) {
    const documentCopy = new DOMParser().parseFromString(html, 'text/html');
    const selectors = [
      '.comparison-card__open[href]', '.comparison-card a[href]',
      '.comparison-grid a[href]', '[data-comparison-grid] a[href]',
      'main article a[href]'
    ];
    const links = [...documentCopy.querySelectorAll(selectors.join(','))];
    const seen = new Set();
    const items = [];
    links.forEach(function(link) {
      const url = new URL(link.getAttribute('href'), baseUrl);
      if (!isHtmlCollectionLink(url) || url.pathname === baseUrl.pathname || seen.has(url.pathname)) {
        return;
      }
      const article = link.closest('article,.comparison-card');
      const heading = article?.querySelector('h3,h2') || link;
      const title = cleanLabel(heading.textContent) || url.pathname.split('/').pop().replace(/(?:-es)?\.html$/i, '').replace(/-/g, ' ');
      seen.add(url.pathname);
      items.push({url: url, title: title});
    });
    return items;
  };

  const finish = function(items) {
    let currentIndex = items.findIndex(function(item) {
      return item.url.pathname === currentUrl.pathname;
    });
    if (currentIndex === -1) {
      items = [{url: currentUrl, title: currentTitle}];
      currentIndex = 0;
    }
    jump.innerHTML = '';
    items.forEach(function(item, index) {
      const option = document.createElement('option');
      option.value = item.url.href;
      option.textContent = (index + 1) + ' · ' + item.title;
      option.selected = index === currentIndex;
      jump.appendChild(option);
    });
    jump.disabled = items.length < 2;
    jump.addEventListener('change', function() {
      window.location.assign(contextualUrl(new URL(jump.value)));
    });
    setLink(previous, items[currentIndex - 1] || null);
    setLink(next, items[currentIndex + 1] || null);
    panel.dataset.loading = 'false';
  };

  if (!collectionUrl) {
    finish([{url: currentUrl, title: currentTitle}]);
    return;
  }

  fetch(collectionUrl.href, {credentials: 'same-origin'})
    .then(function(response) {
      if (!response.ok) {
        throw new Error('Collection index unavailable');
      }
      return response.text();
    })
    .then(function(html) {
      finish(discoverItems(html, collectionUrl));
    })
    .catch(function() {
      finish([{url: currentUrl, title: currentTitle}]);
    });
})();
