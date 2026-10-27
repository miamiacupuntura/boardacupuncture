(() => {
  const groups = new Set([
    'index', 'formulas', 'pathologies', 'patterns', 'families', 'dui-yao',
    'name-traps', 'herb-families', 'safety', 'formula-name-traps',
    'misleading-formula-names', 'advanced-name-traps',
    'supplemental-herb-families', 'patterns-tips-maciocia',
    'most-important-formulas', 'formula-theory'
  ]);
  const params = new URLSearchParams(window.location.search);
  const group = groups.has(params.get('group')) ? params.get('group') : 'index';
  const language = params.get('lang') === 'es' ? 'es' : 'en';
  const es = language === 'es';
  const suffix = es ? '-es' : '';
  const indexUrl = `${group}${suffix}.html`;
  let chapters = [];
  let current = Math.max(0, Number.parseInt(params.get('chapter') || '1', 10) - 1);

  const el = id => document.getElementById(id);
  const ui = {
    groupLink: el('reader-group-link'), brand: el('reader-brand'), language: el('reader-language'), dashboard: el('reader-dashboard'),
    progress: el('reader-progress'),
    previous: el('reader-previous'), next: el('reader-next'), index: el('reader-index'), loading: el('reader-loading'), error: el('reader-error'),
    chapter: el('reader-chapter'), meter: el('reader-meter'), meterFill: el('reader-meter-fill'), bottom: el('reader-bottom-controls'), bottomProgress: el('reader-bottom-progress'),
    bottomPrevious: el('reader-bottom-previous'), bottomNext: el('reader-bottom-next'), bottomIndex: el('reader-bottom-index'), footer: el('reader-footer')
  };

  function setLanguageLabels() {
    document.documentElement.lang = language;
    ui.brand.textContent = es ? 'Lectura Continua' : 'Continuous Reading';
    ui.language.textContent = es ? 'English' : 'Español';
    ui.language.href = `continuous-reader.html?group=${encodeURIComponent(group)}&lang=${es ? 'en' : 'es'}&chapter=${current + 1}`;
    ui.dashboard.textContent = es ? '← Dashboard' : '← Back to Dashboard';
    ui.previous.textContent = ui.bottomPrevious.textContent = es ? '← Anterior' : '← Previous';
    ui.next.textContent = ui.bottomNext.textContent = es ? 'Siguiente →' : 'Next →';
    ui.index.textContent = ui.bottomIndex.textContent = es ? 'Todos los capítulos' : 'All chapters';
    ui.loading.textContent = es ? 'Cargando capítulo…' : 'Loading chapter…';
    ui.footer.textContent = es ? 'Lectura continua · Anterior · Siguiente · Todos los capítulos' : 'Continuous reading · Previous · Next · All chapters';
  }

  function setError(message) {
    ui.loading.classList.add('reader-hidden');
    ui.chapter.replaceChildren();
    ui.bottom.classList.add('reader-hidden');
    ui.error.textContent = message;
    ui.error.classList.remove('reader-hidden');
  }

  function updateControls() {
    const text = es ? `Capítulo ${current + 1} de ${chapters.length}` : `Chapter ${current + 1} of ${chapters.length}`;
    ui.progress.textContent = ui.bottomProgress.textContent = text;
    ui.meter.setAttribute('aria-label', es ? 'Progreso de lectura' : 'Reading progress');
    ui.meter.setAttribute('aria-valuemax', String(chapters.length));
    ui.meter.setAttribute('aria-valuenow', String(current + 1));
    ui.meterFill.style.width = `${((current + 1) / chapters.length) * 100}%`;
    ui.previous.disabled = ui.bottomPrevious.disabled = current === 0;
    const last = current === chapters.length - 1;
    ui.next.textContent = ui.bottomNext.textContent = last ? (es ? 'Finalizar →' : 'Finish →') : (es ? 'Siguiente →' : 'Next →');
    ui.index.href = ui.bottomIndex.href = indexUrl;
    ui.groupLink.href = indexUrl;
    const url = new URL(window.location.href);
    url.searchParams.set('group', group);
    url.searchParams.set('lang', language);
    url.searchParams.set('chapter', String(current + 1));
    history.replaceState({chapter: current + 1}, '', url);
    ui.language.href = `continuous-reader.html?group=${encodeURIComponent(group)}&lang=${es ? 'en' : 'es'}&chapter=${current + 1}`;
  }

  async function loadChapter(position, scroll = true) {
    if (!chapters.length) return;
    if (position < 0) position = 0;
    if (position >= chapters.length) {
      window.location.href = indexUrl;
      return;
    }
    current = position;
    updateControls();
    ui.error.classList.add('reader-hidden');
    ui.bottom.classList.add('reader-hidden');
    ui.chapter.replaceChildren();
    ui.loading.classList.remove('reader-hidden');
    try {
      const response = await fetch(chapters[current].url, {cache: 'no-cache'});
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
      const article = doc.querySelector('article.study-note') || doc.querySelector('main article') || doc.querySelector('main');
      if (!article) throw new Error('chapter content not found');
      article.querySelectorAll('nav[aria-label="Chapter navigation"],nav[aria-label="Navegación entre capítulos"]').forEach(node => node.remove());
      ui.chapter.append(document.importNode(article, true));
      document.title = `${chapters[current].title} | ${es ? 'Lectura Continua' : 'Continuous Reading'} | AcupunctureTests`;
      ui.loading.classList.add('reader-hidden');
      ui.bottom.classList.remove('reader-hidden');
      if (scroll) window.scrollTo({top: 0, behavior: 'smooth'});
      if (chapters[current + 1]) fetch(chapters[current + 1].url).catch(() => {});
    } catch (error) {
      setError(es ? `No se pudo cargar este capítulo: ${error.message}` : `This chapter could not be loaded: ${error.message}`);
    }
  }

  function move(delta) {
    if (delta > 0 && current === chapters.length - 1) {
      window.location.href = indexUrl;
      return;
    }
    loadChapter(current + delta);
  }

  async function initialize() {
    setLanguageLabels();
    ui.index.href = ui.bottomIndex.href = ui.groupLink.href = indexUrl;
    try {
      const response = await fetch(indexUrl, {cache: 'no-cache'});
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
      const groupTitle = doc.querySelector('.comparison-hero h1')?.textContent?.trim();
      if (groupTitle) ui.brand.textContent = groupTitle;
      chapters = [...doc.querySelectorAll('.comparison-card__open')]
        .map(link => ({url: link.dataset.chapterUrl || link.getAttribute('href'), title: link.closest('.comparison-card')?.querySelector('h3')?.textContent?.trim(), order: Number(link.dataset.chapterNumber)}))
        .filter(chapter => chapter.url && chapter.title && Number.isFinite(chapter.order))
        .sort((first, second) => first.order - second.order);
      if (!chapters.length) throw new Error(es ? 'no se encontraron tarjetas' : 'no chapter cards were found');
      current = Math.min(current, chapters.length - 1);
      await loadChapter(current, false);
    } catch (error) {
      setError(es ? `No se pudo iniciar la lectura continua: ${error.message}` : `Continuous reading could not start: ${error.message}`);
    }
  }

  ui.previous.addEventListener('click', () => move(-1));
  ui.bottomPrevious.addEventListener('click', () => move(-1));
  ui.next.addEventListener('click', () => move(1));
  ui.bottomNext.addEventListener('click', () => move(1));
  window.addEventListener('keydown', event => {
    if (event.key === 'ArrowLeft' && current > 0) move(-1);
    if (event.key === 'ArrowRight') move(1);
  });
  window.addEventListener('popstate', () => {
    const requested = Math.max(0, Number.parseInt(new URLSearchParams(window.location.search).get('chapter') || '1', 10) - 1);
    if (requested !== current) loadChapter(requested, false);
  });

  initialize();
})();
