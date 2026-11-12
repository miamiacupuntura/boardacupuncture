(() => {
  'use strict';

  const spanish = document.documentElement.lang === 'es';
  const copy = {
    loadError: spanish ? 'No se pudo cargar el banco de hierbas.' : 'The Single Herbs bank could not be loaded.',
    ready: spanish ? 'Banco de preguntas listo.' : 'Question bank ready.',
    noQuestions: spanish ? 'No hay preguntas para esta categoría.' : 'No questions are available for this category.',
    start: count => spanish ? `Comenzar Quiz (${count}) →` : `Start Quiz (${count}) →`,
    allAvailable: count => spanish ? `Todas (${count})` : `All (${count})`,
    maximumAvailable: count => spanish
      ? `Máximo disponible para esta selección: ${count} preguntas.`
      : `Maximum available for this selection: ${count} questions.`,
    sessionMaximum: spanish
      ? 'Máximo por sesión en el banco completo: 100 preguntas.'
      : 'Maximum per full-bank session: 100 questions.',
    modeHelp: {
      all: spanish
        ? 'Incluye preguntas de todos los perfiles verificados de hierbas individuales.'
        : 'Includes questions from every verified Single Herbs profile.',
      category: spanish
        ? 'Incluye preguntas de las hierbas que pertenecen a una categoría del libro de Yang.'
        : "Includes questions from the herbs in one Yang book category."
    },
    position: (position, total) => spanish ? `Pregunta ${position} de ${total}` : `Question ${position} of ${total}`,
    correct: spanish ? 'Correcto' : 'Correct',
    incorrect: spanish ? 'Incorrecto' : 'Incorrect',
    correctAnswer: spanish ? 'Respuesta correcta:' : 'Correct answer:',
    safety: spanish ? 'Seguridad:' : 'Safety:',
    reviewOptions: spanish ? 'Revisar las cuatro opciones:' : 'Review all four options:',
    openCard: spanish ? 'Abrir tarjeta →' : 'Open card →',
    backToQuestion: position => spanish ? `← Volver a la pregunta ${position}` : `← Back to question ${position}`,
    finish: (answered, total) => spanish
      ? `Finalizar Quiz (${answered}/${total} respondidas)`
      : `Finish Quiz (${answered}/${total} answered)`,
    result: (correct, answered, percent, skipped) => spanish
      ? `${correct} correctas de ${answered} respondidas — ${percent}%. ${skipped} sin responder.`
      : `${correct} correct out of ${answered} answered — ${percent}%. ${skipped} unanswered.`,
    history: (total, correct, percent) => spanish
      ? [[total, 'respuestas'], [correct, 'correctas'], [`${percent}%`, 'precisión']]
      : [[total, 'answers'], [correct, 'correct'], [`${percent}%`, 'accuracy']],
    resetConfirm: spanish
      ? '¿Borrar el historial guardado en este dispositivo?'
      : 'Clear the history saved on this device?',
    restartConfirm: spanish
      ? '¿Reiniciar esta sesión? Se borrarán las respuestas de la sesión actual.'
      : 'Restart this session? Answers from the current session will be cleared.'
  };
  const storageKey = 'single-herb-practice-progress-v1';
  const centralHistoryParams = new URLSearchParams(window.location.search);
  const centralHistorySesskey = centralHistoryParams.get('sesskey') || '';
  const centralHistoryType = centralHistoryParams.get('historytype') || '';
  let centralAttemptUid = '';
  let centralStartedAt = Date.now();
  let centralSaved = false;
  const createCentralAttemptUid = () => {
    if (globalThis.crypto && typeof globalThis.crypto.randomUUID === 'function') {
      return globalThis.crypto.randomUUID();
    }
    return `single-herb-quiz-${Date.now()}-${Math.random().toString(36).slice(2)}`;
  };
  const letters = ['A', 'B', 'C', 'D'];
  // The verified profiles use detailed clinical guide numbers. Group those
  // narrower subjects into the broader chapter sequence used in Yang's book.
  const guideNumbersByYangChapter = new Map([
    [2, [1, 2]],
    [3, [3, 4, 5, 6, 7, 8]],
    [4, [9, 10, 11]],
    [5, [12]],
    [6, [16]],
    [7, [30]],
    [8, [13, 14, 15]],
    [9, [17]],
    [10, [18]],
    [11, [19]],
    [12, [20]],
    [13, [21]],
    [14, [23, 24, 25, 26]],
    [15, [27]],
    [16, [28, 29]],
    [17, [31]]
  ]);
  const elements = {
    total: document.querySelector('[data-herb-total]'),
    status: document.querySelector('[data-herb-status]'),
    setup: document.querySelector('[data-herb-setup]'),
    modeButtons: [...document.querySelectorAll('[data-herb-mode]')],
    modeHelp: document.querySelector('[data-herb-mode-help]'),
    categoryField: document.querySelector('[data-herb-category-field]'),
    category: document.querySelector('[data-herb-category]'),
    sizes: [...document.querySelectorAll('[data-herb-size]')],
    maxSize: document.querySelector('[data-herb-size-max]'),
    maxHelp: document.querySelector('[data-herb-max-help]'),
    available: document.querySelector('[data-herb-available]'),
    start: document.querySelector('[data-herb-start]'),
    history: document.querySelector('[data-herb-history]'),
    historyStats: document.querySelector('[data-herb-history-stats]'),
    reset: document.querySelector('[data-herb-reset]'),
    workspace: document.querySelector('[data-herb-workspace]'),
    returnToSetup: document.querySelector('[data-herb-return]'),
    position: document.querySelector('[data-herb-position]'),
    sessionTotal: document.querySelector('[data-herb-session-total]'),
    answered: document.querySelector('[data-herb-answered]'),
    score: document.querySelector('[data-herb-score]'),
    progress: document.querySelector('[data-herb-progress-bar]'),
    list: document.querySelector('[data-herb-question-list]'),
    previous: document.querySelector('[data-herb-previous]'),
    next: document.querySelector('[data-herb-next]'),
    restart: document.querySelector('[data-herb-restart]'),
    finish: document.querySelector('[data-herb-finish]'),
    results: document.querySelector('[data-herb-results]'),
    finalScore: document.querySelector('[data-herb-final-score]'),
    repeat: document.querySelector('[data-herb-repeat]'),
    configure: document.querySelector('[data-herb-configure]'),
    language: document.querySelector('[data-herb-language]'),
    sourceViewer: document.querySelector('[data-herb-source-viewer]'),
    sourceFrame: document.querySelector('[data-herb-source-frame]'),
    sourceTitle: document.querySelector('[data-herb-source-title]'),
    sourceReturn: document.querySelector('[data-herb-source-return]')
  };
  const state = {
    allQuestions: [],
    categories: [],
    mode: 'all',
    session: [],
    currentIndex: 0,
    size: 25,
    useMaximum: false,
    answers: new Map(),
    score: 0
  };
  let activeSourceLink = null;

  function value(field, language = spanish ? 'es' : 'en') {
    if (!field) return '';
    if (typeof field === 'string') return field;
    return field[language] || field.en || field.es || '';
  }

  function percentage(correct, total) {
    return total ? Math.round((correct / total) * 100) : 0;
  }

  function shuffle(values) {
    const result = [...values];
    for (let index = result.length - 1; index > 0; index -= 1) {
      const target = Math.floor(Math.random() * (index + 1));
      [result[index], result[target]] = [result[target], result[index]];
    }
    return result;
  }

  function option(parent, optionValue, label) {
    const item = document.createElement('option');
    item.value = String(optionValue);
    item.textContent = label;
    parent.append(item);
  }

  function filteredQuestions() {
    if (state.mode !== 'category') return [...state.allQuestions];
    const guideNumbers = new Set(guideNumbersByYangChapter.get(Number(elements.category.value)) || []);
    return state.allQuestions.filter(question => guideNumbers.has(Number(question.profile.guideNumber || 0)));
  }

  function updateUrl() {
    const url = new URL(window.location.href);
    url.searchParams.delete('category');
    if (state.mode === 'category') url.searchParams.set('category', elements.category.value);
    url.searchParams.set('size', String(state.size));
    history.replaceState(null, '', url);
    if (elements.language) {
      elements.language.href = `${elements.language.getAttribute('href').split('?')[0]}${url.search}`;
    }
  }

  function setMode(mode, refresh = true) {
    state.mode = mode === 'category' ? 'category' : 'all';
    elements.modeButtons.forEach(button => {
      button.setAttribute('aria-pressed', String(button.dataset.herbMode === state.mode));
    });
    elements.categoryField.hidden = state.mode !== 'category';
    elements.modeHelp.textContent = copy.modeHelp[state.mode];
    if (refresh) updateSetup();
  }

  function readHistory() {
    try {
      const stored = JSON.parse(localStorage.getItem(storageKey) || 'null');
      if (stored && typeof stored === 'object') return stored;
    } catch (_) { /* Local progress is optional. */ }
    return { total: 0, correct: 0 };
  }

  function writeHistory(history) {
    try { localStorage.setItem(storageKey, JSON.stringify(history)); } catch (_) { /* Optional. */ }
  }

  function renderHistory() {
    const history = readHistory();
    elements.historyStats.replaceChildren();
    copy.history(history.total, history.correct, percentage(history.correct, history.total)).forEach(([number, label]) => {
      const card = document.createElement('span');
      const strong = document.createElement('strong');
      strong.textContent = String(number);
      card.append(strong, ` ${label}`);
      elements.historyStats.append(card);
    });
    elements.history.hidden = false;
  }

  function closeSourceViewer() {
    if (!elements.sourceViewer || elements.sourceViewer.hidden) return;
    const questionCard = activeSourceLink?.closest('.formula-practice-question');
    elements.sourceViewer.hidden = true;
    document.body.classList.remove('formula-practice-source-open');
    if (elements.sourceFrame) elements.sourceFrame.src = 'about:blank';
    questionCard?.scrollIntoView({ block: 'center' });
    activeSourceLink?.focus();
    activeSourceLink = null;
  }

  function openSourceViewer(event, link, profile, position) {
    if (!elements.sourceViewer || !elements.sourceFrame || !elements.sourceTitle || !elements.sourceReturn) return;
    event.preventDefault();
    activeSourceLink = link;
    elements.sourceTitle.textContent = `${profile.name} (${profile.latin})`;
    elements.sourceReturn.textContent = copy.backToQuestion(position);
    elements.sourceFrame.src = link.href;
    elements.sourceViewer.hidden = false;
    document.body.classList.add('formula-practice-source-open');
    elements.sourceReturn.focus();
  }

  function profileText(profile) {
    const action = value(profile.actionSummary);
    const indication = value(profile.indicationSummary);
    return spanish
      ? `¿Qué hierba corresponde mejor a este perfil? ${action} Indicaciones principales: ${indication}`
      : `Which herb best matches this profile? ${action} Principal indications: ${indication}`;
  }

  function buildQuestions(data, guideline) {
    const officialNames = new Set((guideline.herbs || []).map(herb => herb.name));
    const profiles = (data.herbs || []).filter(profile =>
      profile.status === 'verified'
      && officialNames.has(profile.name)
      && profile.name
      && profile.latin
      && value(profile.actionSummary, 'en')
      && value(profile.indicationSummary, 'en')
    );
    const byCategory = new Map();
    profiles.forEach(profile => {
      const key = Number(profile.guideNumber || 0);
      const values = byCategory.get(key) || [];
      values.push(profile);
      byCategory.set(key, values);
    });
    return profiles.map((profile, index) => {
      const peers = [
        ...(byCategory.get(Number(profile.guideNumber || 0)) || []),
        ...profiles
      ].filter(candidate => candidate.name !== profile.name);
      const uniquePeers = [...new Map(peers.map(candidate => [candidate.name, candidate])).values()];
      const offset = (index * 7) % Math.max(1, uniquePeers.length);
      const distractors = [];
      for (let step = 0; step < uniquePeers.length && distractors.length < 3; step += 1) {
        distractors.push(uniquePeers[(offset + step) % uniquePeers.length]);
      }
      const correctIndex = index % 4;
      const choices = distractors.slice(0, 3);
      choices.splice(correctIndex, 0, profile);
      return {
        id: `SH-PRACTICE-${String(index + 1).padStart(3, '0')}`,
        prompt: profileText(profile),
        profile,
        correctLabel: letters[correctIndex],
        options: choices.map((choice, optionIndex) => ({
          label: letters[optionIndex],
          text: `${choice.name} (${choice.latin})`,
          profile: choice
        }))
      };
    });
  }

  function buildCategories(data) {
    return (data.chapters || [])
      .filter(chapter => guideNumbersByYangChapter.has(Number(chapter.number)))
      .map(chapter => ({
        number: Number(chapter.number),
        title: spanish ? (chapter.titleEs || chapter.title) : chapter.title
      }));
  }

  function populateControls() {
    state.categories.forEach(category => {
      const guideNumbers = new Set(guideNumbersByYangChapter.get(category.number) || []);
      const count = state.allQuestions.filter(question =>
        guideNumbers.has(Number(question.profile.guideNumber || 0))
      ).length;
      option(elements.category, category.number, `${category.title} (${count})`);
    });
    const params = new URLSearchParams(window.location.search);
    const requestedCategory = params.get('category');
    const requestedSize = Number(params.get('size'));
    const validCategory = [...elements.category.options].some(item => item.value === requestedCategory);
    if (validCategory) elements.category.value = requestedCategory;
    if (Number.isInteger(requestedSize) && requestedSize > 0 && requestedSize <= state.allQuestions.length) {
      state.size = requestedSize;
    }
    setMode(validCategory ? 'category' : 'all', false);
    state.useMaximum = state.mode === 'category' && requestedSize === filteredQuestions().length;
  }

  function updateSetup() {
    const available = filteredQuestions().length;
    const presets = elements.sizes.map(button => Number(button.dataset.herbSize));
    if (state.mode === 'all' && !presets.includes(state.size)) {
      state.size = 25;
      state.useMaximum = false;
    }
    if (state.useMaximum && state.mode === 'category') state.size = available;
    if (state.useMaximum && presets.includes(available)) state.useMaximum = false;
    if (available > 0 && state.size > available) {
      state.size = available;
      state.useMaximum = state.mode === 'category';
    }
    if (available > 0 && state.size < 1) state.size = Math.min(25, available);
    elements.available.textContent = String(available);
    elements.total.textContent = String(state.allQuestions.length);
    elements.sizes.forEach(button => {
      const size = Number(button.dataset.herbSize);
      button.disabled = size > available;
      button.setAttribute('aria-pressed', String(!state.useMaximum && size === state.size));
    });
    const showMaximum = state.mode === 'category' && available > 0 && !presets.includes(available);
    elements.maxSize.hidden = !showMaximum;
    elements.maxSize.textContent = copy.allAvailable(available);
    elements.maxSize.setAttribute('aria-pressed', String(showMaximum && state.useMaximum));
    elements.maxHelp.textContent = state.mode === 'all'
      ? copy.sessionMaximum
      : copy.maximumAvailable(available);
    elements.start.textContent = copy.start(Math.min(state.size, available));
    elements.start.disabled = available === 0;
    elements.status.textContent = available ? copy.ready : copy.noQuestions;
    updateUrl();
  }

  function recordAnswer(correct) {
    const history = readHistory();
    history.total += 1;
    if (correct) history.correct += 1;
    writeHistory(history);
  }

  function updateProgress() {
    const answered = state.answers.size;
    const current = state.session.length ? state.currentIndex + 1 : 0;
    elements.position.textContent = String(current);
    elements.sessionTotal.textContent = String(state.session.length);
    elements.answered.textContent = String(answered);
    elements.score.textContent = String(state.score);
    elements.progress.style.width = `${state.session.length ? (current / state.session.length) * 100 : 0}%`;
    elements.previous.disabled = state.currentIndex <= 0;
    elements.next.disabled = state.currentIndex >= state.session.length - 1;
    elements.finish.textContent = copy.finish(answered, state.session.length);
  }

  function showQuestion(index, focus = true) {
    if (!state.session.length) return;
    state.currentIndex = Math.max(0, Math.min(index, state.session.length - 1));
    [...elements.list.children].forEach((card, cardIndex) => {
      const active = cardIndex === state.currentIndex;
      card.hidden = !active;
      card.setAttribute('aria-hidden', String(!active));
    });
    updateProgress();
    if (focus) {
      elements.list.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function createQuestion(question, index) {
    const card = document.createElement('article');
    card.className = 'formula-practice-question';
    card.dataset.herbGuideNumber = String(question.profile.guideNumber || '');
    const label = document.createElement('p');
    label.className = 'comparison-eyebrow';
    label.textContent = copy.position(index + 1, state.session.length);
    const prompt = document.createElement('h2');
    prompt.textContent = question.prompt;
    const options = document.createElement('div');
    options.className = 'formula-practice-options';
    const feedback = document.createElement('section');
    feedback.className = 'formula-practice-feedback';
    feedback.hidden = true;
    question.options.forEach(answer => {
      const button = document.createElement('button');
      button.type = 'button';
      button.dataset.answerLabel = answer.label;
      const badge = document.createElement('strong');
      badge.textContent = answer.label;
      const text = document.createElement('span');
      text.textContent = answer.text;
      button.append(badge, text);
      button.addEventListener('click', () => {
        if (state.answers.has(question.id)) return;
        const correct = answer.label === question.correctLabel;
        state.answers.set(question.id, { selected: answer.label, correct });
        if (correct) state.score += 1;
        recordAnswer(correct);
        [...options.querySelectorAll('button')].forEach(optionButton => {
          optionButton.disabled = true;
          if (optionButton.dataset.answerLabel === question.correctLabel) optionButton.classList.add('is-correct');
          else if (optionButton.dataset.answerLabel === answer.label) optionButton.classList.add('is-incorrect');
        });
        const correctOption = question.options.find(option => option.label === question.correctLabel);
        const verdict = document.createElement('h3');
        verdict.textContent = correct ? copy.correct : copy.incorrect;
        const answerLine = document.createElement('p');
        const answerStrong = document.createElement('strong');
        answerStrong.textContent = `${copy.correctAnswer} ${question.correctLabel}. ${correctOption.text}`;
        answerLine.append(answerStrong);
        const explanation = document.createElement('p');
        const profile = question.profile;
        explanation.textContent = `${profile.name} (${profile.latin}) — ${value(profile.temperature)}, ${value(profile.flavor)}. ${value(profile.actionSummary)}`;
        const indication = document.createElement('p');
        indication.textContent = value(profile.indicationSummary);
        const safety = document.createElement('p');
        const safetyStrong = document.createElement('strong');
        safetyStrong.textContent = `${copy.safety} `;
        safety.append(safetyStrong, value(profile.safetyNote));
        const reviewHeading = document.createElement('p');
        const reviewStrong = document.createElement('strong');
        reviewStrong.textContent = copy.reviewOptions;
        reviewHeading.append(reviewStrong);
        const optionLinks = document.createElement('div');
        optionLinks.className = 'herb-feedback-option-links';
        question.options.forEach(option => {
          const source = document.createElement('a');
          const params = new URLSearchParams({ herb: option.profile.name, sort: 'az' });
          if (spanish) params.set('lang', 'es');
          source.href = `/blocks/myprogress/single-herbs-list.php?${params}`;
          source.textContent = `${option.label}. ${option.profile.name} — ${copy.openCard}`;
          source.dataset.herbSourceLink = '';
          if (option.label === question.correctLabel) {
            source.classList.add('is-correct');
            source.setAttribute('aria-label', `${source.textContent} (${copy.correct})`);
          } else if (option.label === answer.label) {
            source.classList.add('is-selected-incorrect');
            source.setAttribute('aria-label', `${source.textContent} (${copy.incorrect})`);
          }
          source.addEventListener('click', event => openSourceViewer(event, source, option.profile, index + 1));
          optionLinks.append(source);
        });
        feedback.classList.add(correct ? 'is-correct' : 'is-incorrect');
        feedback.append(verdict, answerLine, explanation, indication, safety, reviewHeading, optionLinks);
        feedback.hidden = false;
        updateProgress();
      });
      options.append(button);
    });
    card.append(label, prompt, options, feedback);
    return card;
  }

  function renderSession() {
    elements.list.replaceChildren();
    state.session.forEach((question, index) => elements.list.append(createQuestion(question, index)));
    showQuestion(0, false);
  }

  function startSession() {
    const pool = filteredQuestions();
    centralAttemptUid = createCentralAttemptUid();
    centralStartedAt = Date.now();
    centralSaved = false;
    state.session = shuffle(pool).slice(0, Math.min(state.size, pool.length));
    state.currentIndex = 0;
    state.answers = new Map();
    state.score = 0;
    elements.setup.hidden = true;
    elements.history.hidden = true;
    elements.results.hidden = true;
    elements.workspace.hidden = false;
    renderSession();
    elements.workspace.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function restartSession() {
    if (!window.confirm(copy.restartConfirm)) return;
    centralAttemptUid = createCentralAttemptUid();
    centralStartedAt = Date.now();
    centralSaved = false;
    state.currentIndex = 0;
    state.answers = new Map();
    state.score = 0;
    renderSession();
    elements.list.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function showSetup() {
    elements.workspace.hidden = true;
    elements.results.hidden = true;
    elements.setup.hidden = false;
    renderHistory();
    updateSetup();
    elements.setup.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  async function saveCentralAttempt() {
    if (!centralHistorySesskey || !centralHistoryType || centralSaved || !centralAttemptUid) return;
    centralSaved = true;
    const body = new URLSearchParams({
      sesskey: centralHistorySesskey,
      attemptuid: centralAttemptUid,
      examtype: centralHistoryType,
      score: String(state.score),
      total: String(state.session.length),
      duration: String(Math.max(0, Math.round((Date.now() - centralStartedAt) / 1000))),
      language: spanish ? 'es' : 'en'
    });
    try {
      const response = await fetch('/blocks/menu/herbology-exam-history.php', {
        method: 'POST',
        credentials: 'same-origin',
        keepalive: true,
        headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        body
      });
      const payload = await response.json();
      if (!response.ok || !payload.success) throw new Error('History save failed.');
    } catch (_) {
      centralSaved = false;
    }
  }

  function finishSession() {
    void saveCentralAttempt();
    const answered = state.answers.size;
    const skipped = state.session.length - answered;
    elements.finalScore.textContent = copy.result(state.score, answered, percentage(state.score, answered), skipped);
    elements.workspace.hidden = true;
    elements.results.hidden = false;
    renderHistory();
    elements.history.hidden = true;
    elements.results.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  elements.sizes.forEach(button => button.addEventListener('click', () => {
    state.size = Number(button.dataset.herbSize);
    state.useMaximum = false;
    updateSetup();
  }));
  elements.maxSize.addEventListener('click', () => {
    state.useMaximum = true;
    state.size = filteredQuestions().length;
    updateSetup();
  });
  elements.modeButtons.forEach(button => button.addEventListener('click', () => {
    state.useMaximum = false;
    setMode(button.dataset.herbMode);
  }));
  elements.category.addEventListener('change', () => {
    state.useMaximum = false;
    updateSetup();
  });
  elements.start.addEventListener('click', startSession);
  elements.previous.addEventListener('click', () => showQuestion(state.currentIndex - 1));
  elements.next.addEventListener('click', () => showQuestion(state.currentIndex + 1));
  elements.restart.addEventListener('click', restartSession);
  elements.repeat.addEventListener('click', startSession);
  elements.configure.addEventListener('click', showSetup);
  elements.returnToSetup.addEventListener('click', showSetup);
  elements.finish.addEventListener('click', finishSession);
  elements.reset.addEventListener('click', () => {
    if (!window.confirm(copy.resetConfirm)) return;
    try { localStorage.removeItem(storageKey); } catch (_) { /* Optional. */ }
    renderHistory();
  });
  if (elements.sourceViewer) {
    elements.sourceViewer.querySelectorAll('[data-herb-source-close]').forEach(control => {
      control.addEventListener('click', closeSourceViewer);
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && !elements.sourceViewer.hidden) closeSourceViewer();
    });
  }

  Promise.all([
    fetch('/blocks/myprogress/single-herb-properties.json', { credentials: 'same-origin' }),
    fetch('herb-comparison-bank-data.json', { credentials: 'same-origin' }),
    fetch('/blocks/myprogress/single-herb-card-index.json', { credentials: 'same-origin' })
  ])
    .then(async responses => {
      responses.forEach(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
      });
      return Promise.all(responses.map(response => response.json()));
    })
    .then(([herbData, yangData, guideline]) => {
      if (guideline.total !== 304 || guideline.herbs?.length !== 304) {
        throw new Error('Unexpected 2026 herb guideline inventory');
      }
      state.allQuestions = buildQuestions(herbData, guideline);
      state.categories = buildCategories(yangData);
      if (!state.allQuestions.length || state.allQuestions.length > guideline.total || !state.categories.length) {
        throw new Error('Single Herbs Quiz does not match the 2026 guideline inventory');
      }
      populateControls();
      elements.setup.hidden = false;
      updateSetup();
      renderHistory();
    })
    .catch(error => {
      console.error(error);
      elements.status.textContent = copy.loadError;
      elements.status.classList.add('is-error');
    });
})();
