(() => {
  'use strict';

  const es = document.documentElement.lang === 'es';
  const copy = es ? {
    all: 'Todos los capítulos', chapter: 'Capítulo', question: 'Pregunta', page: 'PDF pág.', questions: 'preguntas', charts: 'gráficos',
    shown: 'visibles', reveal: 'Mostrar explicación', hide: 'Ocultar explicación', study: 'Estudiar como flashcard',
    empty: 'No se encontraron resultados. Cambia el capítulo o la búsqueda.', noCharts: 'Este capítulo no contiene gráficos de barras.',
    loadingError: 'No se pudo cargar el banco comparativo. Intenta recargar la página.', strongest: 'Mayor fuerza relativa',
    weakest: 'Menor fuerza relativa', spread: 'Diferencia máxima', points: 'puntos', temperatures: 'Temperaturas', original: 'Explicación del texto', progress: 'Progreso',
    again: 'repetir', hard: 'difíciles', good: 'recordadas', unrated: 'sin evaluar'
  } : {
    all: 'All chapters', chapter: 'Chapter', question: 'Question', page: 'PDF p.', questions: 'questions', charts: 'charts',
    shown: 'shown', reveal: 'Reveal explanation', hide: 'Hide explanation', study: 'Study as flashcard',
    empty: 'No results found. Change the chapter or search.', noCharts: 'This chapter does not contain bar charts.',
    loadingError: 'The comparison bank could not be loaded. Please reload the page.', strongest: 'Highest relative strength',
    weakest: 'Lowest relative strength', spread: 'Maximum difference', points: 'points', temperatures: 'Temperatures', original: 'Source explanation', progress: 'Progress',
    again: 'again', hard: 'hard', good: 'remembered', unrated: 'unrated'
  };

  const q = (selector, root = document) => root.querySelector(selector);
  const qa = (selector, root = document) => [...root.querySelectorAll(selector)];
  const els = {
    modes: qa('[data-bank-mode]'), views: qa('[data-bank-view]'), chapter: q('[data-bank-chapter]'), search: q('[data-bank-search]'),
    status: q('[data-bank-status]'), questionList: q('[data-question-list]'), questionCount: q('[data-question-count]'),
    chartList: q('[data-chart-list]'), chartCount: q('[data-chart-count]'), flashLabel: q('[data-flash-label]'),
    flashQuestion: q('[data-flash-question]'), flashAnswer: q('[data-flash-answer]'), flashReveal: q('[data-flash-reveal]'),
    flashRating: q('[data-flash-rating]'), flashPosition: q('[data-flash-position]'), flashTotal: q('[data-flash-total]'),
    previous: q('[data-flash-previous]'), next: q('[data-flash-next]'), shuffle: q('[data-flash-shuffle]'),
    memorySummary: q('[data-memory-summary]')
  };
  const languageLink = q('.herb-bank-top-actions a[href^="herb-comparison-bank"]');
  const state = { data: null, mode: 'questions', chapter: 1, search: '', deck: [], index: 0, revealed: false, reading: {} };
  const progressKey = 'herb-comparison-bank-progress-v1';
  let progress = readProgress();

  function readProgress() {
    try { return JSON.parse(localStorage.getItem(progressKey)) || {}; } catch (_) { return {}; }
  }
  function saveProgress() {
    try { localStorage.setItem(progressKey, JSON.stringify(progress)); } catch (_) {}
  }
  function normalize(value) {
    return String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  }
  function paragraphs(text) {
    return String(text || '').split(/\n\s*\n/).map(value => value.replace(/\s*\n\s*/g, ' ').trim()).filter(Boolean);
  }
  function create(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
  }
  function selectedChapter() {
    return state.data.chapters.find(chapter => chapter.number === state.chapter);
  }
  function questionLabel(item) {
    return `${copy.chapter} ${item.chapter} · ${copy.question} ${item.number} · ${copy.page} ${item.pdfPage}`;
  }
  function questionMatches(item) {
    if (state.chapter && item.chapter !== state.chapter) return false;
    const needle = normalize(state.search);
    return !needle || normalize(`${item.question} ${item.answer} ${item._sourceQuestion || ''} ${item._sourceAnswer || ''} ${item._alternateQuestion || ''} ${item._alternateAnswer || ''}`).includes(needle);
  }
  function chartMatches(item) {
    if (state.chapter && item.chapter !== state.chapter) return false;
    const needle = normalize(state.search);
    return !needle || normalize(`${item.title} ${item._sourceTitle || ''} ${item._alternateTitle || ''} ${item.bars.map(bar => `${bar.herb} ${bar.temperature}`).join(' ')}`).includes(needle);
  }
  function filteredQuestions() { return state.data.questions.filter(questionMatches); }
  function filteredCharts() { return state.data.charts.filter(chartMatches); }

  function applySpanishTranslations(translations, translateContent = true) {
    const questions = new Map((translations.questions || []).map(item => [item.id, item]));
    const charts = new Map((translations.charts || []).map(item => [item.figure, item]));
    state.data.questions = state.data.questions.map(item => {
      const translated = questions.get(item.id);
      if (!translated) return item;
      return translateContent
        ? { ...item, _sourceQuestion: item.question, _sourceAnswer: item.answer, ...translated }
        : { ...item, _alternateQuestion: translated.question, _alternateAnswer: translated.answer };
    });
    state.data.charts = state.data.charts.map(item => {
      const translated = charts.get(item.figure);
      if (!translated) return item;
      return translateContent
        ? { ...item, _sourceTitle: item.title, ...translated }
        : { ...item, _alternateTitle: translated.title };
    });
  }

  function populateChapters() {
    els.chapter.replaceChildren();
    const all = create('option', '', `${copy.all} (${state.data.totals.questions} ${copy.questions})`);
    all.value = '0';
    els.chapter.append(all);
    state.data.chapters.forEach(chapter => {
      const title = es ? chapter.titleEs : chapter.title;
      const option = create('option', '', `${chapter.number}. ${title} · ${chapter.questionCount} ${copy.questions} · ${chapter.chartCount} ${copy.charts}`);
      option.value = String(chapter.number);
      els.chapter.append(option);
    });
    els.chapter.value = String(state.chapter);
  }

  function renderQuestions() {
    const items = filteredQuestions();
    els.questionCount.textContent = String(items.length);
    els.questionList.replaceChildren();
    if (!items.length) return els.questionList.append(create('p', 'herb-bank-empty', copy.empty));
    const fragment = document.createDocumentFragment();
    items.forEach(item => {
      const article = create('article', 'herb-question');
      article.id = `question-${item.id}`;
      article.dataset.bankItem = item.id;
      article.append(create('p', 'comparison-eyebrow', questionLabel(item)), create('h3', '', item.question));
      const details = create('details');
      const summary = create('summary', '', copy.reveal);
      details.addEventListener('toggle', () => { summary.textContent = details.open ? copy.hide : copy.reveal; });
      const answer = create('div', 'herb-question__answer');
      paragraphs(item.answer).forEach(value => answer.append(create('p', '', value)));
      details.append(summary, answer);
      const actions = create('div', 'herb-question__actions');
      const study = create('button', '', copy.study);
      study.type = 'button';
      study.addEventListener('click', () => {
        state.deck = items;
        state.index = Math.max(0, items.findIndex(entry => entry.id === item.id));
        setMode('flashcards');
        window.scrollTo({ top: q('[data-bank-view="flashcards"]').offsetTop - 20, behavior: 'smooth' });
      });
      actions.append(study);
      article.append(details, actions);
      fragment.append(article);
    });
    els.questionList.append(fragment);
  }

  function renderFlashcard(keepDeck = false) {
    if (!keepDeck) state.deck = filteredQuestions();
    if (state.index >= state.deck.length) state.index = 0;
    const item = state.deck[state.index];
    els.flashTotal.textContent = String(state.deck.length);
    els.flashPosition.textContent = item ? String(state.index + 1) : '0';
    els.flashAnswer.replaceChildren();
    state.revealed = false;
    els.flashAnswer.hidden = true;
    els.flashRating.hidden = true;
    els.flashReveal.hidden = !item;
    if (!item) {
      els.flashLabel.textContent = '';
      els.flashQuestion.textContent = copy.empty;
      renderMemorySummary();
      updateLanguageLink();
      return;
    }
    els.flashLabel.textContent = questionLabel(item);
    els.flashQuestion.textContent = item.question;
    els.flashReveal.textContent = copy.reveal;
    const cue = create('p', 'memory-cue', item.memoryCue);
    const heading = create('h4', '', copy.original);
    els.flashAnswer.append(cue, heading);
    paragraphs(item.answer).forEach(value => els.flashAnswer.append(create('p', '', value)));
    renderMemorySummary();
    updateLanguageLink();
  }
  function revealFlashcard() {
    if (!state.deck.length) return;
    state.revealed = !state.revealed;
    els.flashAnswer.hidden = !state.revealed;
    els.flashRating.hidden = !state.revealed;
    els.flashReveal.textContent = state.revealed ? copy.hide : copy.reveal;
    updateLanguageLink();
  }
  function moveCard(delta) {
    if (!state.deck.length) return;
    state.index = (state.index + delta + state.deck.length) % state.deck.length;
    renderFlashcard(true);
  }
  function rateCard(rating) {
    const item = state.deck[state.index];
    if (!item) return;
    progress[item.id] = { rating, reviewedAt: new Date().toISOString() };
    saveProgress();
    moveCard(1);
  }
  function renderMemorySummary() {
    const counts = { again: 0, hard: 0, good: 0 };
    Object.values(progress).forEach(value => { if (counts[value.rating] !== undefined) counts[value.rating] += 1; });
    const unrated = Math.max(0, state.data.totals.questions - Object.keys(progress).length);
    els.memorySummary.replaceChildren(
      create('span', '', `${copy.progress}: ${counts.good} ${copy.good}`),
      create('span', '', `${counts.hard} ${copy.hard}`),
      create('span', '', `${counts.again} ${copy.again}`),
      create('span', '', `${unrated} ${copy.unrated}`)
    );
  }

  function chartInsight(chart) {
    const values = chart.bars.map(bar => bar.value);
    const max = Math.max(...values);
    const min = Math.min(...values.filter(value => value > 0));
    const strongest = chart.bars.filter(bar => bar.value === max).map(bar => bar.herb).join(', ');
    const weakest = chart.bars.filter(bar => bar.value === min).map(bar => bar.herb).join(', ');
    const temperatureNames = es ? {
      'very hot': 'muy calientes', warm: 'calientes', 'slightly warm': 'ligeramente calientes', neutral: 'neutras',
      'slightly cold': 'ligeramente frías', cold: 'frías', 'very cold': 'muy frías'
    } : {};
    const groups = [...new Set(chart.bars.map(bar => temperatureNames[bar.temperature] || bar.temperature))].join(', ');
    return `${copy.strongest}: ${strongest} (${max}). ${copy.weakest}: ${weakest} (${min}). ${copy.spread}: ${max - min} ${copy.points}. ${copy.temperatures}: ${groups}.`;
  }
  function renderCharts() {
    const items = filteredCharts();
    els.chartCount.textContent = String(items.length);
    els.chartList.replaceChildren();
    if (!items.length) return els.chartList.append(create('p', 'herb-bank-empty', state.chapter && selectedChapter()?.chartCount === 0 ? copy.noCharts : copy.empty));
    const fragment = document.createDocumentFragment();
    items.forEach(chart => {
      const card = create('article', 'herb-chart-card');
      card.dataset.bankItem = chart.figure;
      card.append(create('p', 'comparison-eyebrow', `${copy.chapter} ${chart.chapter} · Fig. ${chart.figure}`));
      card.append(create('h3', '', chart.title));
      card.append(create('p', 'herb-chart-card__insight', chartInsight(chart)));
      const scroll = create('div', 'herb-chart-scroll');
      const plot = create('div', 'herb-chart-plot');
      plot.style.setProperty('--chart-min', `${Math.max(620, chart.bars.length * 70)}px`);
      plot.setAttribute('role', 'img');
      plot.setAttribute('aria-label', `${chart.title}. ${chartInsight(chart)}`);
      const bars = create('div', 'herb-chart-bars');
      chart.bars.forEach(bar => {
        const column = create('div', 'herb-chart-column');
        const value = create('span', 'herb-chart-value', String(bar.value));
        const shape = create('span', 'herb-chart-bar');
        shape.style.setProperty('--bar-value', String(bar.value));
        shape.style.setProperty('--bar-color', bar.color);
        shape.title = `${bar.herb}: ${bar.value} · ${bar.temperature}`;
        const label = create('span', 'herb-chart-label', bar.herb);
        column.append(value, shape, label);
        bars.append(column);
      });
      plot.append(bars); scroll.append(plot); card.append(scroll); fragment.append(card);
    });
    els.chartList.append(fragment);
  }

  function updateUrl() {
    const url = new URL(location.href);
    ['at', 'progress', 'expanded', 'card', 'reveal'].forEach(key => url.searchParams.delete(key));
    url.searchParams.set('mode', state.mode);
    url.searchParams.set('chapter', String(state.chapter));
    if (state.search) url.searchParams.set('q', state.search); else url.searchParams.delete('q');
    history.replaceState(null, '', url);
    updateLanguageLink();
  }

  function readingContext() {
    if (state.mode === 'flashcards') {
      const item = state.deck[state.index];
      return item ? { card: item.id, reveal: state.revealed ? '1' : '' } : {};
    }
    const selector = state.mode === 'charts' ? '.herb-chart-card' : '.herb-question';
    const anchorY = Math.min(180, Math.round(innerHeight * .28));
    let element = document.elementFromPoint(Math.round(innerWidth / 2), anchorY)?.closest(selector);
    if (!element) {
      element = [...document.querySelectorAll(selector)].find(node => {
        const rect = node.getBoundingClientRect();
        return rect.bottom > anchorY && rect.top < innerHeight;
      });
    }
    if (!element) return state.reading;
    const rect = element.getBoundingClientRect();
    const progress = Math.max(0, Math.min(1, (anchorY - rect.top) / Math.max(1, rect.height)));
    const details = state.mode === 'questions' ? q('details', element) : null;
    state.reading = {
      at: element.dataset.bankItem,
      progress: progress.toFixed(4),
      expanded: details?.open ? '1' : ''
    };
    return state.reading;
  }

  function updateLanguageLink() {
    if (!languageLink || !state.data) return;
    const target = es ? 'herb-comparison-bank.html' : 'herb-comparison-bank-es.html';
    const url = new URL(target, location.href);
    url.searchParams.set('mode', state.mode);
    url.searchParams.set('chapter', String(state.chapter));
    if (state.search) url.searchParams.set('q', state.search);
    Object.entries(readingContext()).forEach(([key, value]) => { if (value) url.searchParams.set(key, value); });
    languageLink.href = `${url.pathname.split('/').pop()}${url.search}${url.hash}`;
  }

  function restoreReadingPosition(params) {
    if (state.mode === 'flashcards') {
      const card = params.get('card');
      const position = card ? state.deck.findIndex(item => item.id === card) : -1;
      if (position >= 0) {
        state.index = position;
        renderFlashcard(true);
      }
      if (params.get('reveal') === '1' && !state.revealed) revealFlashcard();
      return;
    }
    const itemId = params.get('at');
    if (!itemId) return;
    const selector = state.mode === 'charts' ? '.herb-chart-card' : '.herb-question';
    const element = [...document.querySelectorAll(selector)].find(node => node.dataset.bankItem === itemId);
    if (!element) return;
    if (state.mode === 'questions' && params.get('expanded') === '1') q('details', element).open = true;
    const progress = Math.max(0, Math.min(1, Number(params.get('progress')) || 0));
    requestAnimationFrame(() => requestAnimationFrame(() => {
      const anchorY = Math.min(180, Math.round(innerHeight * .28));
      window.scrollTo(0, Math.max(0, element.offsetTop + element.offsetHeight * progress - anchorY));
      updateLanguageLink();
    }));
  }
  function setMode(mode, update = true) {
    state.mode = ['questions', 'flashcards', 'charts'].includes(mode) ? mode : 'questions';
    state.reading = {};
    els.modes.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.bankMode === state.mode)));
    els.views.forEach(view => { view.hidden = view.dataset.bankView !== state.mode; });
    if (state.mode === 'flashcards') renderFlashcard();
    if (state.mode === 'charts') renderCharts();
    if (update) updateUrl();
  }
  function refresh() {
    state.index = 0;
    state.reading = {};
    renderQuestions();
    renderFlashcard();
    renderCharts();
    updateUrl();
  }
  function bind() {
    els.modes.forEach(button => button.addEventListener('click', () => setMode(button.dataset.bankMode)));
    els.chapter.addEventListener('change', () => { state.chapter = Number(els.chapter.value); refresh(); });
    let searchTimer;
    els.search.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => { state.search = els.search.value.trim(); refresh(); }, 160);
    });
    els.flashReveal.addEventListener('click', revealFlashcard);
    els.previous.addEventListener('click', () => moveCard(-1));
    els.next.addEventListener('click', () => moveCard(1));
    els.shuffle.addEventListener('click', () => {
      state.deck = filteredQuestions().sort(() => Math.random() - .5);
      state.index = 0;
      renderFlashcard(true);
    });
    qa('[data-flash-rate]').forEach(button => button.addEventListener('click', () => rateCard(button.dataset.flashRate)));
    if (languageLink) {
      languageLink.addEventListener('pointerdown', updateLanguageLink);
      languageLink.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') updateLanguageLink(); });
    }
    let scrollFrame = 0;
    addEventListener('scroll', () => {
      if (scrollFrame) return;
      scrollFrame = requestAnimationFrame(() => { scrollFrame = 0; updateLanguageLink(); });
    }, { passive: true });
  }
  async function init() {
    try {
      const params = new URLSearchParams(location.search);
      const response = await fetch('herb-comparison-bank-data.json');
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      state.data = await response.json();
      if (es || params.has('q')) {
        const translated = await fetch('herb-comparison-bank-translations-es.json');
        if (!translated.ok) throw new Error(`Spanish translation HTTP ${translated.status}`);
        applySpanishTranslations(await translated.json(), es);
      }
      const requestedChapter = Number(params.get('chapter'));
      if (params.has('chapter') && Number.isInteger(requestedChapter) && requestedChapter >= 0 && requestedChapter <= 17) state.chapter = requestedChapter;
      state.search = params.get('q') || '';
      els.search.value = state.search;
      populateChapters();
      bind();
      renderQuestions(); renderCharts(); renderFlashcard();
      setMode(params.get('mode') || 'questions', false);
      restoreReadingPosition(params);
      updateLanguageLink();
      els.status.hidden = true;
    } catch (error) {
      console.error(error);
      els.status.textContent = copy.loadingError;
      els.status.style.background = '#fff0f0';
      els.status.style.color = '#8a2424';
    }
  }
  init();
})();
