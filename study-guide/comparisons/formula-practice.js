(() => {
  'use strict';

  const spanish = document.documentElement.lang === 'es';
  const text = {
    loadingError: spanish ? 'No se pudo cargar el banco de preguntas.' : 'The question bank could not be loaded.',
    available: spanish ? 'Banco listo.' : 'Question bank ready.',
    noQuestions: spanish ? 'No hay preguntas con esta combinación de filtros.' : 'No questions match these filters.',
    startPractice: count => spanish ? `Comenzar práctica (${count}) →` : `Start practice (${count}) →`,
    source: number => spanish ? `Pregunta de fuente ${number}` : `Source question ${number}`,
    related: spanish ? 'Relacionada con:' : 'Related to:',
    general: spanish ? 'Pregunta general de fórmulas' : 'General formula question',
    correct: spanish ? 'Correcto' : 'Correct',
    incorrect: spanish ? 'Incorrecto' : 'Incorrect',
    correctAnswer: spanish ? 'Respuesta correcta:' : 'Correct answer:',
    noFeedback: spanish ? 'La fuente no proporcionó retroalimentación adicional.' : 'The source supplied no additional feedback.',
    next: spanish ? 'Siguiente pregunta →' : 'Next question →',
    finish: spanish ? 'Ver resultado →' : 'View result →',
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
    available: document.querySelector('[data-practice-available]'),
    start: document.querySelector('[data-practice-start]'),
    history: document.querySelector('[data-practice-history]'),
    historyStats: document.querySelector('[data-practice-history-stats]'),
    weaknesses: document.querySelector('[data-practice-weaknesses]'),
    reset: document.querySelector('[data-practice-reset]'),
    workspace: document.querySelector('[data-practice-workspace]'),
    returnToSetup: document.querySelector('[data-practice-return]'),
    position: document.querySelector('[data-practice-position]'),
    total: document.querySelector('[data-practice-total]'),
    score: document.querySelector('[data-practice-score]'),
    progressBar: document.querySelector('[data-practice-progress-bar]'),
    source: document.querySelector('[data-practice-source]'),
    prompt: document.querySelector('[data-practice-prompt]'),
    body: document.querySelector('[data-practice-body]'),
    associations: document.querySelector('[data-practice-associations]'),
    options: document.querySelector('[data-practice-options]'),
    feedback: document.querySelector('[data-practice-feedback]'),
    verdict: document.querySelector('[data-practice-verdict]'),
    correctAnswer: document.querySelector('[data-practice-correct-answer]'),
    feedbackText: document.querySelector('[data-practice-feedback-text]'),
    next: document.querySelector('[data-practice-next]'),
    results: document.querySelector('[data-practice-results]'),
    finalScore: document.querySelector('[data-practice-final-score]'),
    breakdown: document.querySelector('[data-practice-breakdown]'),
    repeat: document.querySelector('[data-practice-repeat]'),
    configure: document.querySelector('[data-practice-configure]'),
    language: document.querySelector('[data-practice-language]')
  };
  const state = {
    data: null,
    formulaByNumber: new Map(),
    mode: 'all',
    size: 25,
    questions: [],
    index: 0,
    score: 0,
    answered: false,
    answers: []
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
    const sessionCount = Math.min(count, state.size);
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
    if ([10, 25, 50, 100].includes(requestedSize)) state.size = requestedSize;
    elements.sizeButtons.forEach(button => button.setAttribute('aria-pressed', String(Number(button.dataset.practiceSize) === state.size)));
    if (requestedFormula && [...elements.formula.options].some(item => item.value === requestedFormula)) setMode('formula', false);
    else if (requestedCategory && [...elements.category.options].some(item => item.value === requestedCategory)) setMode('category', false);
    else setMode('all', false);
    elements.setup.hidden = false;
    elements.status.hidden = false;
    updateAvailability();
    renderHistory();
  }

  function associationLink(formula) {
    const link = document.createElement('a');
    link.href = `${formula.slug}${spanish ? '-es' : ''}.html`;
    link.textContent = formula.name;
    return link;
  }

  function renderQuestion() {
    const question = state.questions[state.index];
    state.answered = false;
    elements.position.textContent = String(state.index + 1);
    elements.total.textContent = String(state.questions.length);
    elements.score.textContent = String(state.score);
    elements.progressBar.style.width = `${((state.index + 1) / state.questions.length) * 100}%`;
    elements.source.textContent = text.source(question.sourceNumber);
    elements.prompt.textContent = question.prompt;
    elements.body.replaceChildren();
    question.body.forEach(value => {
      const paragraph = document.createElement('p');
      paragraph.textContent = value;
      elements.body.append(paragraph);
    });

    elements.associations.replaceChildren();
    const label = document.createElement('strong');
    if (question.formulaNumbers.length) {
      label.textContent = text.related;
      elements.associations.append(label);
      question.formulaNumbers.forEach(number => {
        const formula = state.formulaByNumber.get(number);
        if (formula) elements.associations.append(associationLink(formula));
      });
    } else {
      label.textContent = text.general;
      elements.associations.append(label);
    }

    elements.options.replaceChildren();
    question.options.forEach(answer => {
      const button = document.createElement('button');
      button.type = 'button';
      button.dataset.answerLabel = answer.label;
      const badge = document.createElement('strong');
      badge.textContent = answer.label;
      const answerText = document.createElement('span');
      answerText.textContent = answer.text;
      button.append(badge, answerText);
      button.addEventListener('click', () => answerQuestion(answer.label));
      elements.options.append(button);
    });
    elements.feedback.hidden = true;
    elements.feedback.classList.remove('is-correct', 'is-incorrect');
    elements.next.hidden = true;
    elements.next.textContent = state.index === state.questions.length - 1 ? text.finish : text.next;
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

  function answerQuestion(selectedLabel) {
    if (state.answered) return;
    state.answered = true;
    const question = state.questions[state.index];
    const correct = selectedLabel === question.correctLabel;
    if (correct) state.score += 1;
    state.answers.push({ question, correct });
    recordAnswer(question, correct);
    elements.score.textContent = String(state.score);
    [...elements.options.querySelectorAll('button')].forEach(button => {
      button.disabled = true;
      if (button.dataset.answerLabel === question.correctLabel) button.classList.add('is-correct');
      else if (button.dataset.answerLabel === selectedLabel) button.classList.add('is-incorrect');
    });
    const correctOption = question.options.find(option => option.label === question.correctLabel);
    elements.feedback.hidden = false;
    elements.feedback.classList.add(correct ? 'is-correct' : 'is-incorrect');
    elements.verdict.textContent = correct ? text.correct : text.incorrect;
    elements.correctAnswer.textContent = `${text.correctAnswer} ${question.correctLabel}) ${correctOption?.text || ''}`;
    elements.feedbackText.replaceChildren();
    const feedback = question.feedback.length ? question.feedback : [text.noFeedback];
    feedback.forEach(value => {
      const paragraph = document.createElement('p');
      paragraph.textContent = value;
      elements.feedbackText.append(paragraph);
    });
    elements.next.hidden = false;
    elements.next.focus();
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
    const score = percentage(state.score, state.questions.length);
    elements.finalScore.textContent = spanish
      ? `${state.score} de ${state.questions.length} correctas — ${score}%`
      : `${state.score} of ${state.questions.length} correct — ${score}%`;
    renderBreakdown();
    renderHistory();
    elements.results.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function startSession() {
    const pool = filteredQuestions();
    if (!pool.length) return;
    state.questions = shuffle(pool).slice(0, Math.min(state.size, pool.length));
    state.index = 0;
    state.score = 0;
    state.answers = [];
    elements.setup.hidden = true;
    elements.history.hidden = true;
    elements.results.hidden = true;
    elements.workspace.hidden = false;
    renderQuestion();
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
    elements.sizeButtons.forEach(item => item.setAttribute('aria-pressed', String(item === button)));
    updateUrl();
  }));
  elements.start.addEventListener('click', startSession);
  elements.next.addEventListener('click', () => {
    if (state.index === state.questions.length - 1) finishSession();
    else { state.index += 1; renderQuestion(); }
  });
  elements.repeat.addEventListener('click', startSession);
  elements.configure.addEventListener('click', returnToSetup);
  elements.returnToSetup.addEventListener('click', returnToSetup);
  elements.reset.addEventListener('click', () => {
    if (!window.confirm(text.resetConfirm)) return;
    try { localStorage.removeItem(storageKey); } catch (_) { /* Optional persistence. */ }
    renderHistory();
  });

  fetch('formula-practice-data.json', { credentials: 'same-origin' })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    })
    .then(data => {
      if (data.totals?.questions !== 897 || data.formulas?.length !== 63) throw new Error('Unexpected bank totals');
      state.data = data;
      state.formulaByNumber = new Map(data.formulas.map(formula => [formula.number, formula]));
      populateControls();
    })
    .catch(error => {
      console.error(error);
      elements.status.textContent = text.loadingError;
      elements.status.classList.add('is-error');
    });
})();
