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
  const fromLibrary = currentUrl.searchParams.get('from') === 'library';
  const currentFile = currentUrl.pathname.split('/').pop() || '';
  const currentTitle = (note.querySelector('.study-note__title,h1')?.textContent || document.title)
    .replace(/\s+/g, ' ')
    .trim();
  const duiYaoPath = [
    [1, 'ma-huang-gui-zhi-dui-yao', 'Má Huáng + Guì Zhī'],
    [2, 'ma-huang-xing-ren-dui-yao', 'Má Huáng + Xìng Rén'],
    [3, 'ma-huang-shi-gao-dui-yao', 'Má Huáng + Shí Gāo'],
    [4, 'gui-zhi-bai-shao-dui-yao', 'Guì Zhī + Bái Sháo'],
    [5, 'jing-jie-fang-feng-dui-yao', 'Jīng Jiè + Fáng Fēng'],
    [6, 'cang-er-zi-xin-yi-hua-dui-yao', 'Cāng Ěr Zǐ + Xīn Yí Huā'],
    [7, 'sang-ye-ju-hua-dui-yao', 'Sāng Yè + Jú Huā'],
    [8, 'ge-gen-sheng-ma-dui-yao', 'Gé Gēn + Shēng Má'],
    [9, 'chai-hu-sheng-ma-dui-yao', 'Chái Hú + Shēng Má'],
    [10, 'zhi-zi-dan-dou-chi-dui-yao', 'Zhī Zǐ + Dàn Dòu Chǐ'],
    [11, 'shi-gao-zhi-mu-dui-yao', 'Shí Gāo + Zhī Mǔ'],
    [12, 'zhi-mu-huang-bai-dui-yao', 'Zhī Mǔ + Huáng Bǎi'],
    [13, 'sang-bai-pi-di-gu-pi-dui-yao', 'Sāng Bái Pí + Dì Gǔ Pí'],
    [14, 'huang-lian-rou-gui-dui-yao', 'Huáng Lián + Ròu Guì'],
    [15, 'jin-yin-hua-lian-qiao-dui-yao', 'Jīn Yín Huā + Lián Qiào'],
    [16, 'niu-bang-zi-lian-qiao-dui-yao', 'Niú Bàng Zǐ + Lián Qiào'],
    [17, 'qing-hao-bie-jia-dui-yao', 'Qīng Hāo + Biē Jiǎ'],
    [18, 'ren-shen-huang-qi-dui-yao', 'Rén Shēn + Huáng Qí'],
    [19, 'bai-zhu-fu-ling-dui-yao', 'Bái Zhú + Fú Líng'],
    [20, 'bai-shao-gan-cao-dui-yao', 'Bái Sháo + Gān Cǎo'],
    [21, 'jie-geng-gan-cao-dui-yao', 'Jié Gěng + Gān Cǎo'],
    [22, 'sheng-jiang-da-zao-dui-yao', 'Shēng Jiāng + Dà Zǎo'],
    [23, 'dang-gui-huang-qi-dui-yao', 'Dāng Guī + Huáng Qí'],
    [24, 'dang-gui-chuan-xiong-dui-yao', 'Dāng Guī + Chuān Xiōng'],
    [25, 'dang-gui-xiang-fu-dui-yao', 'Dāng Guī + Xiāng Fù'],
    [26, 'ai-ye-e-jiao-dui-yao', 'Ài Yè + Ē Jiāo'],
    [27, 'mai-men-dong-sha-shen-dui-yao', 'Mài Mén Dōng + Shā Shēn'],
    [28, 'gou-qi-zi-ju-hua-dui-yao', 'Gǒu Qǐ Zǐ + Jú Huā'],
    [29, 'han-lian-cao-nu-zhen-zi-dui-yao', 'Hàn Lián Cǎo + Nǚ Zhēn Zǐ'],
    [30, 'gui-ban-bie-jia-dui-yao', 'Guī Bǎn + Biē Jiǎ'],
    [31, 'du-zhong-xu-duan-dui-yao', 'Dù Zhòng + Xù Duàn'],
    [32, 'dang-gui-rou-cong-rong-dui-yao', 'Dāng Guī + Ròu Cōng Róng'],
    [33, 'di-yu-huai-mi-dui-yao', 'Dì Yú + Huái Mǐ'],
    [34, 'tao-ren-hong-hua-dui-yao', 'Táo Rén + Hóng Huā'],
    [35, 'ru-xiang-mo-yao-dui-yao', 'Rǔ Xiāng + Mò Yào'],
    [36, 'san-leng-e-zhu-dui-yao', 'Sān Léng + É Zhú'],
    [37, 'ban-xia-chen-pi-dui-yao', 'Bàn Xià + Chén Pí'],
    [38, 'ban-xia-sheng-jiang-dui-yao', 'Bàn Xià + Shēng Jiāng'],
    [39, 'gua-lou-xie-bai-dui-yao', 'Guā Lóu + Xiè Bái'],
    [40, 'zi-wan-kuan-dong-hua-dui-yao', 'Zǐ Wǎn + Kuǎn Dōng Huā'],
    [41, 'ting-li-zi-da-zao-dui-yao', 'Tíng Lì Zǐ + Dà Zǎo'],
    [42, 'fu-zi-gan-jiang-dui-yao', 'Fù Zǐ + Gān Jiāng'],
    [43, 'fu-zi-rou-gui-dui-yao', 'Fù Zǐ + Ròu Guì'],
    [44, 'gan-jiang-bai-zhu-dui-yao', 'Gān Jiāng + Bái Zhú'],
    [45, 'chai-hu-huang-qin-dui-yao', 'Chái Hú + Huáng Qín'],
    [46, 'chai-hu-bai-shao-dui-yao', 'Chái Hú + Bái Sháo'],
    [47, 'huo-xiang-pei-lan-dui-yao', 'Huò Xiāng + Pèi Lán'],
    [48, 'jin-qian-cao-hai-jin-sha-dui-yao', 'Jīn Qián Cǎo + Hǎi Jīn Shā'],
    [49, 'da-huang-mang-xiao-dui-yao', 'Dà Huáng + Máng Xiāo'],
    [50, 'long-gu-mu-li-dui-yao', 'Lóng Gǔ + Mǔ Lì'],
    [51, 'tian-ma-gou-teng-dui-yao', 'Tiān Má + Gōu Téng']
  ];

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
  const isDuiYaoNote = /-dui-yao(?:-es)?\.html$/i.test(currentFile);

  const route = document.createElement('nav');
  route.className = 'study-note-route';
  route.setAttribute('aria-label', spanish ? 'Ruta de estudio' : 'Study path');
  const studyGuideUrl = '/blocks/myprogress/study-guide.php?area=herbology';
  if (isDuiYaoNote) {
    const languageQuery = spanish ? '?lang=es' : '';
    const studyGuideQuery = spanish ? '&lang=es' : '';
    route.innerHTML = '<a href="' + studyGuideUrl + studyGuideQuery + '">' + escapeHtml(copy.studyGuide) + '</a><span aria-hidden="true">›</span>'
      + '<a href="/blocks/myprogress/herb-formula-category-map.php' + languageQuery + '">' + (spanish ? 'Mapa Bensky' : 'Bensky Map') + '</a><span aria-hidden="true">›</span>'
      + '<a href="/blocks/myprogress/single-herbs-study-guide.php' + languageQuery + '">' + (spanish ? 'Hierbas' : 'Herbs') + '</a><span aria-hidden="true">→</span>'
      + '<a href="' + (spanish ? 'dui-yao-es.html' : 'dui-yao.html') + '">Duì Yào</a><span aria-hidden="true">→</span>'
      + '<a href="/blocks/myprogress/formulas-study-guide.php' + languageQuery + '">' + (spanish ? 'Fórmulas' : 'Formulas') + '</a><span aria-hidden="true">›</span>'
      + '<span class="study-note-route__current" aria-current="page">' + escapeHtml(currentTitle) + '</span>';
  } else {
    route.innerHTML = '<a href="' + studyGuideUrl + '">' + escapeHtml(copy.studyGuide) + '</a><span aria-hidden="true">›</span>'
      + (collectionUrl
        ? '<a href="' + escapeHtml(collectionUrl.href) + '">' + escapeHtml(collectionLabel) + '</a><span aria-hidden="true">›</span>'
        : '')
      + '<span class="study-note-route__current" aria-current="page">' + escapeHtml(currentTitle) + '</span>';
  }

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
  if (!fromLibrary) {
    main.insertBefore(route, note);
  }
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

  const discoverDuiYaoItems = function() {
    const suffix = spanish ? '-es.html' : '.html';
    return Promise.resolve(duiYaoPath.map(function(item) {
      return {
        url: new URL(item[1] + suffix, currentUrl),
        title: item[2]
      };
    }));
  };

  const loadManifest = function() {
    if (window.studyNoteNavigationManifest?.collections) {
      return Promise.resolve(window.studyNoteNavigationManifest);
    }
    return new Promise(function(resolve) {
      const script = document.createElement('script');
      script.src = new URL('study-note-navigation-manifest.js?v=20261104-1', currentUrl).href;
      script.onload = function() { resolve(window.studyNoteNavigationManifest || null); };
      script.onerror = function() { resolve(null); };
      document.head.appendChild(script);
    });
  };

  const discoverManifestItems = function(manifest, collectionFile) {
    const entries = manifest?.collections?.[collectionFile];
    if (!Array.isArray(entries)) {
      return [];
    }
    return entries
      .filter(function(item) {
        return spanish ? /-es\.html$/i.test(item[0]) : !/-es\.html$/i.test(item[0]);
      })
      .map(function(item) {
        return {url: new URL(item[0], currentUrl), title: item[1]};
      });
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
    jump.onchange = function() {
      const destination = new URL(jump.options[jump.selectedIndex].value, currentUrl);
      window.location.href = contextualUrl(destination);
    };
    jump.addEventListener('keydown', function(event) {
      if (event.key !== 'Enter' || jump.disabled) {
        return;
      }
      event.preventDefault();
      jump.onchange();
    });
    setLink(previous, items[currentIndex - 1] || null);
    setLink(next, items[currentIndex + 1] || null);
    panel.dataset.loading = 'false';
  };

  if (!collectionUrl) {
    finish([{url: currentUrl, title: currentTitle}]);
    return;
  }

  const collectionFile = collectionUrl.pathname.split('/').pop() || '';
  const itemRequest = /^dui-yao(?:-es)?\.html$/i.test(collectionFile)
    ? discoverDuiYaoItems()
    : loadManifest().then(function(manifest) {
      const items = discoverManifestItems(manifest, collectionFile);
      if (items.length) {
        return items;
      }
      return fetch(collectionUrl.href, {credentials: 'same-origin', cache: 'no-store'})
        .then(function(response) {
          if (!response.ok) {
            throw new Error('Collection index unavailable');
          }
          return response.text();
        })
        .then(function(html) {
          return discoverItems(html, collectionUrl);
        });
    });

  itemRequest
    .then(function(items) {
      if (!items.length) {
        throw new Error('Collection is empty');
      }
      finish(items);
    })
    .catch(function() {
      finish([{url: currentUrl, title: currentTitle}]);
    });
})();
