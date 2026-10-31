(() => {
  'use strict';

  const returnNavigation = document.createElement('script');
  returnNavigation.src = 'study-return-navigation.js?v=20261031-1';
  document.head.appendChild(returnNavigation);

  const spanish = document.documentElement.lang === 'es';
  const text = {
    loadingError: spanish ? 'No se pudo cargar el banco de preguntas.' : 'The question bank could not be loaded.',
    available: spanish ? 'Banco listo.' : 'Question bank ready.',
    noQuestions: spanish ? 'No hay preguntas con esta combinación de filtros.' : 'No questions match these filters.',
    startPractice: count => spanish ? `Comenzar práctica (${count}) →` : `Start practice (${count}) →`,
    source: number => spanish ? `Pregunta de fuente ${number}` : `Source question ${number}`,
    questionPosition: (position, total, source) => spanish
      ? `Pregunta ${position} de ${total} · Fuente ${source}`
      : `Question ${position} of ${total} · Source ${source}`,
    related: spanish ? 'Relacionada con:' : 'Related to:',
    general: spanish ? 'Pregunta general de fórmulas' : 'General formula question',
    correct: spanish ? 'Correcto' : 'Correct',
    incorrect: spanish ? 'Incorrecto' : 'Incorrect',
    correctAnswer: spanish ? 'Respuesta correcta:' : 'Correct answer:',
    showEnglish: 'Ver inglés original',
    showSpanish: 'Ver traducción al español',
    allAvailable: count => spanish ? `Todas (${count})` : `All (${count})`,
    maximumAvailable: count => spanish
      ? `Máximo disponible para esta selección: ${count} preguntas.`
      : `Maximum available for this selection: ${count} questions.`,
    sessionMaximum: spanish ? 'Máximo por sesión en el banco completo: 100 preguntas.' : 'Maximum per full-bank session: 100 questions.',
    finishProgress: (answered, total) => spanish
      ? `Finalizar práctica (${answered}/${total} respondidas)`
      : `Finish practice (${answered}/${total} answered)`,
    skipForNow: spanish ? 'Saltar por ahora ↓' : 'Skip for now ↓',
    historyEmpty: spanish ? 'Completa una sesión para comenzar tu historial.' : 'Complete a session to start your history.',
    attempts: spanish ? 'respuestas' : 'answers',
    accuracy: spanish ? 'precisión' : 'accuracy',
    needsReview: spanish ? 'Fórmulas para reforzar' : 'Formulas to reinforce',
    resetConfirm: spanish ? '¿Borrar el historial guardado en este dispositivo?' : 'Clear the history saved on this device?',
    questionWord: spanish ? 'preguntas' : 'questions',
    modeHelp: {
      all: spanish ? 'Incluye preguntas de todas las fórmulas y temas generales.' : 'Includes questions from every formula and general topic.',
      formula: spanish ? 'Practica todas las preguntas relacionadas con una fórmula específica.' : 'Practice every question related to one specific formula.',
      category: spanish ? 'Practica preguntas de las fórmulas que pertenecen a una categoría.' : 'Practice questions from formulas in one category.'
    }
  };
  const storageKey = 'formula-practice-progress-v1';
  const elements = {
    status: document.querySelector('[data-practice-status]'),
    setup: document.querySelector('[data-practice-setup]'),
    modeButtons: [...document.querySelectorAll('[data-practice-mode]')],
    modeHelp: document.querySelector('[data-practice-mode-help]'),
    formulaField: document.querySelector('[data-practice-formula-field]'),
    categoryField: document.querySelector('[data-practice-category-field]'),
    formula: document.querySelector('[data-practice-formula]'),
    category: document.querySelector('[data-practice-category]'),
    sizeButtons: [...document.querySelectorAll('[data-practice-size]')],
    maxSizeButton: document.querySelector('[data-practice-size-max]'),
    maxHelp: document.querySelector('[data-practice-max-help]'),
    available: document.querySelector('[data-practice-available]'),
    start: document.querySelector('[data-practice-start]'),
    history: document.querySelector('[data-practice-history]'),
    historyStats: document.querySelector('[data-practice-history-stats]'),
    weaknesses: document.querySelector('[data-practice-weaknesses]'),
    reset: document.querySelector('[data-practice-reset]'),
    workspace: document.querySelector('[data-practice-workspace]'),
    returnToSetup: document.querySelector('[data-practice-return]'),
    questionLanguage: document.querySelector('[data-question-language]'),
    position: document.querySelector('[data-practice-position]'),
    total: document.querySelector('[data-practice-total]'),
    score: document.querySelector('[data-practice-score]'),
    progressBar: document.querySelector('[data-practice-progress-bar]'),
    questionList: document.querySelector('[data-practice-question-list]'),
    finish: document.querySelector('[data-practice-finish]'),
    results: document.querySelector('[data-practice-results]'),
    finalScore: document.querySelector('[data-practice-final-score]'),
    breakdown: document.querySelector('[data-practice-breakdown]'),
    repeat: document.querySelector('[data-practice-repeat]'),
    configure: document.querySelector('[data-practice-configure]'),
    language: document.querySelector('[data-practice-language]'),
    questionTotals: [...document.querySelectorAll('[data-practice-question-total]')],
    categoryTotals: [...document.querySelectorAll('[data-practice-category-total]')]
  };
  const state = {
    data: null,
    translations: new Map(),
    showOriginal: false,
    formulaByNumber: new Map(),
    mode: 'all',
    size: 25,
    useMaximum: false,
    questions: [],
    score: 0,
    answers: [],
    answeredBySource: new Map()
  };

  function readHistory() {
    try {
      const parsed = JSON.parse(localStorage.getItem(storageKey) || 'null');
      if (parsed && typeof parsed === 'object') return parsed;
    } catch (_) { /* Storage may be unavailable or contain invalid JSON. */ }
    return { total: 0, correct: 0, formulas: {}, lastAt: null };
  }

  function writeHistory(history) {
    try { localStorage.setItem(storageKey, JSON.stringify(history)); } catch (_) { /* Optional persistence. */ }
  }

  function percentage(correct, total) {
    return total ? Math.round((correct / total) * 100) : 0;
  }

  function shuffle(values) {
    const copy = [...values];
    for (let index = copy.length - 1; index > 0; index -= 1) {
      const target = Math.floor(Math.random() * (index + 1));
      [copy[index], copy[target]] = [copy[target], copy[index]];
    }
    return copy;
  }

  function option(parent, value, label) {
    const element = document.createElement('option');
    element.value = String(value);
    element.textContent = label;
    parent.append(element);
  }

  function filteredQuestions() {
    if (state.mode === 'formula') {
      const formula = Number(elements.formula.value);
      return state.data.questions.filter(question => question.formulaNumbers.includes(formula));
    }
    if (state.mode === 'category') {
      const category = elements.category.value;
      return state.data.questions.filter(question => question.categoryKeys.includes(category));
    }
    return [...state.data.questions];
  }

  function updateUrl() {
    const url = new URL(window.location.href);
    url.searchParams.delete('formula');
    url.searchParams.delete('category');
    if (state.mode === 'formula') url.searchParams.set('formula', elements.formula.value);
    if (state.mode === 'category') url.searchParams.set('category', elements.category.value);
    url.searchParams.set('size', String(state.size));
    history.replaceState(null, '', url);
    if (elements.language) elements.language.href = `${elements.language.getAttribute('href').split('?')[0]}${url.search}`;
  }

  function setMode(mode, refresh = true) {
    state.mode = ['all', 'formula', 'category'].includes(mode) ? mode : 'all';
    elements.modeButtons.forEach(button => {
      button.setAttribute('aria-pressed', String(button.dataset.practiceMode === state.mode));
    });
    elements.formulaField.hidden = state.mode !== 'formula';
    elements.categoryField.hidden = state.mode !== 'category';
    elements.modeHelp.textContent = text.modeHelp[state.mode];
    if (refresh) updateAvailability();
  }

  function updateAvailability() {
    const count = filteredQuestions().length;
    const presets = elements.sizeButtons.map(button => Number(button.dataset.practiceSize));
    if (state.mode === 'all' && !presets.includes(state.size)) {
      state.size = 25;
      state.useMaximum = false;
    }
    if (state.useMaximum && state.mode !== 'all') state.size = count;
    if (state.useMaximum && presets.includes(count)) state.useMaximum = false;
    if (count > 0 && state.size > count) {
      state.size = count;
      state.useMaximum = state.mode !== 'all';
    }
    if (count > 0 && state.size < 1) state.size = Math.min(25, count);
    const sessionCount = Math.min(count, state.size);
    elements.sizeButtons.forEach(button => {
      const size = Number(button.dataset.practiceSize);
      button.disabled = size > count;
      button.setAttribute('aria-pressed', String(!state.useMaximum && size === state.size));
    });
    const showMaximum = state.mode !== 'all' && count > 0 && !presets.includes(count);
    elements.maxSizeButton.hidden = !showMaximum;
    elements.maxSizeButton.textContent = text.allAvailable(count);
    elements.maxSizeButton.setAttribute('aria-pressed', String(showMaximum && state.useMaximum));
    elements.maxHelp.textContent = state.mode === 'all'
      ? text.sessionMaximum
      : text.maximumAvailable(count);
    elements.available.textContent = String(count);
    elements.start.disabled = count === 0;
    elements.start.textContent = text.startPractice(sessionCount);
    elements.status.textContent = count ? text.available : text.noQuestions;
    updateUrl();
  }

  function renderHistory() {
    const history = readHistory();
    elements.history.hidden = false;
    elements.historyStats.replaceChildren();
    const stats = [
      [history.total, text.attempts],
      [history.correct, spanish ? 'correctas' : 'correct'],
      [`${percentage(history.correct, history.total)}%`, text.accuracy]
    ];
    stats.forEach(([value, label]) => {
      const card = document.createElement('span');
      const strong = document.createElement('strong');
      strong.textContent = String(value);
      card.append(strong, ` ${label}`);
      elements.historyStats.append(card);
    });

    elements.weaknesses.replaceChildren();
    const formulaStats = Object.entries(history.formulas || {})
      .map(([number, result]) => ({ number: Number(number), ...result }))
      .filter(result => result.total >= 3)
      .sort((a, b) => percentage(a.correct, a.total) - percentage(b.correct, b.total))
      .slice(0, 5);
    if (!history.total) {
      const message = document.createElement('p');
      message.textContent = text.historyEmpty;
      elements.weaknesses.append(message);
    } else if (formulaStats.length) {
      const heading = document.createElement('h3');
      heading.textContent = text.needsReview;
      const list = document.createElement('ul');
      formulaStats.forEach(result => {
        const formula = state.formulaByNumber.get(result.number);
        if (!formula) return;
        const item = document.createElement('li');
        const link = document.createElement('a');
        link.href = `${formula.slug}${spanish ? '-es' : ''}.html`;
        link.textContent = formula.name;
        item.append(link, ` — ${percentage(result.correct, result.total)}% (${result.total} ${text.attempts})`);
        list.append(item);
      });
      elements.weaknesses.append(heading, list);
    }
  }

  function populateControls() {
    state.data.formulas.forEach(formula => {
      option(elements.formula, formula.number, `${formula.number}. ${formula.name} (${formula.questionCount})`);
    });
    state.data.categories.forEach(category => {
      const count = state.data.questions.filter(question => question.categoryKeys.includes(category.key)).length;
      option(elements.category, category.key, `${spanish ? category.es : category.en} (${count})`);
    });
    const params = new URLSearchParams(window.location.search);
    const requestedFormula = params.get('formula');
    const requestedCategory = params.get('category');
    const requestedSize = Number(params.get('size'));
    if ([...elements.formula.options].some(item => item.value === requestedFormula)) elements.formula.value = requestedFormula;
    if ([...elements.category.options].some(item => item.value === requestedCategory)) elements.category.value = requestedCategory;
    if (Number.isInteger(requestedSize) && requestedSize > 0 && requestedSize <= state.data.totals.questions) state.size = requestedSize;
    if (requestedFormula && [...elements.formula.options].some(item => item.value === requestedFormula)) setMode('formula', false);
    else if (requestedCategory && [...elements.category.options].some(item => item.value === requestedCategory)) setMode('category', false);
    else setMode('all', false);
    state.useMaximum = state.mode !== 'all' && requestedSize === filteredQuestions().length;
    elements.setup.hidden = false;
    elements.status.hidden = false;
    updateAvailability();
    renderHistory();
  }

  function mergeSupplements(data, translations, supplements) {
    const knownCategories = new Set(data.categories.map(category => category.key));
    const knownQuestions = new Set(data.questions.map(question => String(question.sourceNumber)));
    supplements.filter(Boolean).forEach(supplement => {
      (supplement.categories || []).forEach(category => {
        if (!category?.key || knownCategories.has(category.key)) return;
        data.categories.push(category);
        knownCategories.add(category.key);
      });
      (supplement.questions || []).forEach(entry => {
        if (!entry?.sourceNumber || knownQuestions.has(String(entry.sourceNumber))) return;
        const { translationEs, ...question } = entry;
        data.questions.push(question);
        knownQuestions.add(String(question.sourceNumber));
        if (translations && translationEs) {
          translations.questions.push({
            sourceNumber: question.sourceNumber,
            ...translationEs
          });
        }
      });
    });
    data.totals.questions = data.questions.length;
    data.totals.categories = data.categories.length;
    data.formulas.forEach(formula => {
      formula.questionCount = data.questions.filter(question =>
        question.formulaNumbers.includes(formula.number)
      ).length;
    });
  }

  function associationLink(formula) {
    const link = document.createElement('a');
    link.href = `${formula.slug}${spanish ? '-es' : ''}.html`;
    link.textContent = formula.name;
    return link;
  }

  function displayedQuestion(question) {
    if (!spanish || state.showOriginal) return question;
    return state.translations.get(question.sourceNumber) || question;
  }

  function renderQuestionCopy(question, card) {
    const displayed = displayedQuestion(question);
    const prompt = card.querySelector('[data-card-prompt]');
    const body = card.querySelector('[data-card-body]');
    const options = card.querySelector('[data-card-options]');
    const correctAnswer = card.querySelector('[data-card-correct-answer]');
    const feedbackText = card.querySelector('[data-card-feedback-text]');
    prompt.textContent = displayed.prompt;
    body.replaceChildren();
    displayed.body.forEach(value => {
      const paragraph = document.createElement('p');
      paragraph.textContent = value;
      body.append(paragraph);
    });
    const displayedOptions = new Map(displayed.options.map(option => [option.label, option.text]));
    options.querySelectorAll('button').forEach(button => {
      const answerText = button.querySelector('span');
      if (answerText) answerText.textContent = displayedOptions.get(button.dataset.answerLabel) || '';
    });
    if (!state.answeredBySource.has(question.sourceNumber)) return;
    const correctOption = displayed.options.find(option => option.label === question.correctLabel);
    correctAnswer.textContent = `${text.correctAnswer} ${question.correctLabel}) ${correctOption?.text || ''}`;
    feedbackText.replaceChildren();
    displayed.feedback.forEach(value => {
      const paragraph = document.createElement('p');
      paragraph.textContent = value;
      feedbackText.append(paragraph);
    });
  }

  function createQuestionCard(question, index) {
    const card = document.createElement('article');
    card.className = 'formula-practice-question';
    card.dataset.questionSource = String(question.sourceNumber);
    const source = document.createElement('p');
    source.className = 'comparison-eyebrow';
    source.textContent = text.questionPosition(index + 1, state.questions.length, question.sourceNumber);
    const prompt = document.createElement('h2');
    prompt.dataset.cardPrompt = '';
    const body = document.createElement('div');
    body.dataset.cardBody = '';
    const options = document.createElement('div');
    options.className = 'formula-practice-options';
    options.dataset.cardOptions = '';
    question.options.forEach(answer => {
      const button = document.createElement('button');
      button.type = 'button';
      button.dataset.answerLabel = answer.label;
      const badge = document.createElement('strong');
      badge.textContent = answer.label;
      const answerText = document.createElement('span');
      button.append(badge, answerText);
      button.addEventListener('click', () => answerQuestion(question, answer.label, card));
      options.append(button);
    });

    const skip = document.createElement('button');
    skip.type = 'button';
    skip.className = 'formula-practice-skip';
    skip.textContent = text.skipForNow;
    skip.addEventListener('click', () => {
      const target = card.nextElementSibling || elements.finish;
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    const feedback = document.createElement('section');
    feedback.className = 'formula-practice-feedback';
    feedback.dataset.cardFeedback = '';
    feedback.hidden = true;
    const verdict = document.createElement('h3');
    verdict.dataset.cardVerdict = '';
    const correctAnswer = document.createElement('p');
    correctAnswer.dataset.cardCorrectAnswer = '';
    const associations = document.createElement('div');
    associations.className = 'formula-practice-associations';
    associations.dataset.cardAssociations = '';
    const label = document.createElement('strong');
    if (question.formulaNumbers.length) {
      label.textContent = text.related;
      associations.append(label);
      question.formulaNumbers.forEach(number => {
        const formula = state.formulaByNumber.get(number);
        if (formula) associations.append(associationLink(formula));
      });
    } else {
      label.textContent = text.general;
      associations.append(label);
    }
    const feedbackText = document.createElement('div');
    feedbackText.dataset.cardFeedbackText = '';
    feedback.append(verdict, correctAnswer, associations, feedbackText);
    card.append(source, prompt, body, options, skip, feedback);
    renderQuestionCopy(question, card);
    return card;
  }

  function updateSessionProgress() {
    const answered = state.answers.length;
    elements.position.textContent = String(answered);
    elements.total.textContent = String(state.questions.length);
    elements.score.textContent = String(state.score);
    elements.progressBar.style.width = `${state.questions.length ? (answered / state.questions.length) * 100 : 0}%`;
    elements.finish.textContent = text.finishProgress(answered, state.questions.length);
  }

  function renderQuestions() {
    elements.questionList.replaceChildren();
    state.questions.forEach((question, index) => {
      elements.questionList.append(createQuestionCard(question, index));
    });
    updateSessionProgress();
  }

  function recordAnswer(question, correct) {
    const history = readHistory();
    history.total += 1;
    if (correct) history.correct += 1;
    question.formulaNumbers.forEach(number => {
      const formula = history.formulas[String(number)] || { total: 0, correct: 0 };
      formula.total += 1;
      if (correct) formula.correct += 1;
      history.formulas[String(number)] = formula;
    });
    history.lastAt = new Date().toISOString();
    writeHistory(history);
  }

  function answerQuestion(question, selectedLabel, card) {
    if (state.answeredBySource.has(question.sourceNumber)) return;
    const correct = selectedLabel === question.correctLabel;
    if (correct) state.score += 1;
    state.answers.push({ question, correct });
    state.answeredBySource.set(question.sourceNumber, { selectedLabel, correct });
    recordAnswer(question, correct);
    [...card.querySelectorAll('[data-card-options] button')].forEach(button => {
      button.disabled = true;
      if (button.dataset.answerLabel === question.correctLabel) button.classList.add('is-correct');
      else if (button.dataset.answerLabel === selectedLabel) button.classList.add('is-incorrect');
    });
    const feedback = card.querySelector('[data-card-feedback]');
    feedback.hidden = false;
    feedback.classList.add(correct ? 'is-correct' : 'is-incorrect');
    card.querySelector('[data-card-verdict]').textContent = correct ? text.correct : text.incorrect;
    card.querySelector('.formula-practice-skip').hidden = true;
    renderQuestionCopy(question, card);
    updateSessionProgress();
  }

  function renderBreakdown() {
    elements.breakdown.replaceChildren();
    const results = new Map();
    state.answers.forEach(answer => answer.question.formulaNumbers.forEach(number => {
      const result = results.get(number) || { total: 0, correct: 0 };
      result.total += 1;
      if (answer.correct) result.correct += 1;
      results.set(number, result);
    }));
    if (!results.size) return;
    const heading = document.createElement('h3');
    heading.textContent = spanish ? 'Resultado por asociación de fórmula' : 'Result by formula association';
    const list = document.createElement('ul');
    [...results.entries()].sort((a, b) => b[1].total - a[1].total).forEach(([number, result]) => {
      const formula = state.formulaByNumber.get(number);
      const item = document.createElement('li');
      item.textContent = `${formula?.name || number}: ${result.correct}/${result.total} (${percentage(result.correct, result.total)}%)`;
      list.append(item);
    });
    elements.breakdown.append(heading, list);
  }

  function finishSession() {
    elements.workspace.hidden = true;
    elements.results.hidden = false;
    const answered = state.answers.length;
    const skipped = state.questions.length - answered;
    const score = percentage(state.score, answered);
    elements.finalScore.textContent = spanish
      ? `${state.score} correctas de ${answered} respondidas — ${score}%. ${skipped} sin responder.`
      : `${state.score} correct out of ${answered} answered — ${score}%. ${skipped} unanswered.`;
    renderBreakdown();
    renderHistory();
    elements.results.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function startSession() {
    const pool = filteredQuestions();
    if (!pool.length) return;
    state.questions = shuffle(pool).slice(0, Math.min(state.size, pool.length));
    state.score = 0;
    state.answers = [];
    state.answeredBySource = new Map();
    elements.setup.hidden = true;
    elements.history.hidden = true;
    elements.results.hidden = true;
    elements.workspace.hidden = false;
    renderQuestions();
    elements.workspace.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function returnToSetup() {
    elements.workspace.hidden = true;
    elements.results.hidden = true;
    elements.setup.hidden = false;
    renderHistory();
    updateAvailability();
    elements.setup.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  elements.formula.addEventListener('change', updateAvailability);
  elements.category.addEventListener('change', updateAvailability);
  elements.modeButtons.forEach(button => button.addEventListener('click', () => {
    setMode(button.dataset.practiceMode);
  }));
  elements.sizeButtons.forEach(button => button.addEventListener('click', () => {
    state.size = Number(button.dataset.practiceSize);
    state.useMaximum = false;
    updateAvailability();
  }));
  elements.maxSizeButton.addEventListener('click', () => {
    state.useMaximum = true;
    state.size = filteredQuestions().length;
    updateAvailability();
  });
  elements.start.addEventListener('click', startSession);
  elements.finish.addEventListener('click', finishSession);
  elements.repeat.addEventListener('click', startSession);
  elements.configure.addEventListener('click', returnToSetup);
  elements.returnToSetup.addEventListener('click', returnToSetup);
  elements.questionLanguage?.addEventListener('click', () => {
    state.showOriginal = !state.showOriginal;
    elements.questionLanguage.textContent = state.showOriginal ? text.showSpanish : text.showEnglish;
    elements.questionLanguage.setAttribute('aria-pressed', String(state.showOriginal));
    state.questions.forEach(question => {
      const card = elements.questionList.querySelector(`[data-question-source="${question.sourceNumber}"]`);
      if (card) renderQuestionCopy(question, card);
    });
  });
  elements.reset.addEventListener('click', () => {
    if (!window.confirm(text.resetConfirm)) return;
    try { localStorage.removeItem(storageKey); } catch (_) { /* Optional persistence. */ }
    renderHistory();
  });

  const bankRequest = fetch('formula-practice-data.json', { credentials: 'same-origin' })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    });
  const translationsRequest = spanish
    ? fetch('formula-practice-translations-es.json', { credentials: 'same-origin' })
      .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
      })
      .catch(error => {
        console.error('Spanish translation layer unavailable', error);
        return null;
      })
    : Promise.resolve(null);

  const requestedCategory = new URLSearchParams(window.location.search).get('category');
  const supplementsRequest = requestedCategory === 'modifications'
    ? fetch('formula-modifications-manifest.json', { credentials: 'same-origin' })
      .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
      })
      .then(manifest => Promise.all((manifest.files || []).map(filename =>
        fetch(filename, { credentials: 'same-origin' }).then(response => {
          if (!response.ok) throw new Error(`HTTP ${response.status}`);
          return response.json();
        })
      )))
      .catch(error => {
        console.error('Formula modification supplement unavailable', error);
        return [];
      })
    : Promise.resolve([]);

  Promise.all([bankRequest, translationsRequest, supplementsRequest])
    .then(([data, translations, supplements]) => {
      if (data.totals?.questions < 572 || data.formulas?.length !== 63) throw new Error('Unexpected bank totals');
      mergeSupplements(data, translations, supplements);
      state.data = data;
      state.formulaByNumber = new Map(data.formulas.map(formula => [formula.number, formula]));
      elements.questionTotals.forEach(element => { element.textContent = String(data.totals.questions); });
      elements.categoryTotals.forEach(element => { element.textContent = String(data.totals.categories); });
      if (translations?.questions?.length === data.questions.length) {
        state.translations = new Map(translations.questions.map(question => [question.sourceNumber, question]));
      } else if (spanish) {
        state.showOriginal = true;
        if (elements.questionLanguage) elements.questionLanguage.hidden = true;
      }
      populateControls();
    })
    .catch(error => {
      console.error(error);
      elements.status.textContent = text.loadingError;
      elements.status.classList.add('is-error');
    });
})();
