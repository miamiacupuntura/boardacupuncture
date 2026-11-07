(() => {
  'use strict';

  const spanish = document.documentElement.lang === 'es';
  const copy = {
    loadError: spanish ? 'No se pudo cargar el banco de hierbas.' : 'The Single Herbs bank could not be loaded.',
    ready: spanish ? 'Banco de preguntas listo.' : 'Question bank ready.',
    start: count => spanish ? `Comenzar práctica (${count}) →` : `Start practice (${count}) →`,
    position: (position, total) => spanish ? `Pregunta ${position} de ${total}` : `Question ${position} of ${total}`,
    correct: spanish ? 'Correcto' : 'Correct',
    incorrect: spanish ? 'Incorrecto' : 'Incorrect',
    correctAnswer: spanish ? 'Respuesta correcta:' : 'Correct answer:',
    safety: spanish ? 'Seguridad:' : 'Safety:',
    source: spanish ? 'Abrir tarjeta fuente →' : 'Open source card →',
    finish: (answered, total) => spanish
      ? `Finalizar práctica (${answered}/${total} respondidas)`
      : `Finish practice (${answered}/${total} answered)`,
    result: (correct, answered, percent, skipped) => spanish
      ? `${correct} correctas de ${answered} respondidas — ${percent}%. ${skipped} sin responder.`
      : `${correct} correct out of ${answered} answered — ${percent}%. ${skipped} unanswered.`,
    history: (total, correct, percent) => spanish
      ? [[total, 'respuestas'], [correct, 'correctas'], [`${percent}%`, 'precisión']]
      : [[total, 'answers'], [correct, 'correct'], [`${percent}%`, 'accuracy']],
    resetConfirm: spanish
      ? '¿Borrar el historial guardado en este dispositivo?'
      : 'Clear the history saved on this device?'
  };
  const storageKey = 'single-herb-practice-progress-v1';
  const letters = ['A', 'B', 'C', 'D'];
  const elements = {
    total: document.querySelector('[data-herb-total]'),
    status: document.querySelector('[data-herb-status]'),
    setup: document.querySelector('[data-herb-setup]'),
    sizes: [...document.querySelectorAll('[data-herb-size]')],
    available: document.querySelector('[data-herb-available]'),
    start: document.querySelector('[data-herb-start]'),
    history: document.querySelector('[data-herb-history]'),
    historyStats: document.querySelector('[data-herb-history-stats]'),
    reset: document.querySelector('[data-herb-reset]'),
    workspace: document.querySelector('[data-herb-workspace]'),
    returnToSetup: document.querySelector('[data-herb-return]'),
    position: document.querySelector('[data-herb-position]'),
    sessionTotal: document.querySelector('[data-herb-session-total]'),
    score: document.querySelector('[data-herb-score]'),
    progress: document.querySelector('[data-herb-progress-bar]'),
    list: document.querySelector('[data-herb-question-list]'),
    finish: document.querySelector('[data-herb-finish]'),
    results: document.querySelector('[data-herb-results]'),
    finalScore: document.querySelector('[data-herb-final-score]'),
    repeat: document.querySelector('[data-herb-repeat]'),
    configure: document.querySelector('[data-herb-configure]')
  };
  const state = { questions: [], session: [], size: 25, answers: new Map(), score: 0 };

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

  function profileText(profile) {
    const action = value(profile.actionSummary);
    const indication = value(profile.indicationSummary);
    return spanish
      ? `¿Qué hierba corresponde mejor a este perfil? ${action} Indicaciones principales: ${indication}`
      : `Which herb best matches this profile? ${action} Principal indications: ${indication}`;
  }

  function buildQuestions(data) {
    const profiles = (data.herbs || []).filter(profile =>
      profile.status === 'verified'
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
          text: `${choice.name} (${choice.latin})`
        }))
      };
    });
  }

  function updateSetup() {
    const available = state.questions.length;
    if (state.size > available) state.size = Math.min(available, 25);
    elements.available.textContent = String(available);
    elements.total.textContent = String(available);
    elements.sizes.forEach(button => {
      const size = Number(button.dataset.herbSize);
      button.disabled = size > available;
      button.setAttribute('aria-pressed', String(size === state.size));
    });
    elements.start.textContent = copy.start(Math.min(state.size, available));
    elements.start.disabled = available === 0;
  }

  function recordAnswer(correct) {
    const history = readHistory();
    history.total += 1;
    if (correct) history.correct += 1;
    writeHistory(history);
  }

  function updateProgress() {
    const answered = state.answers.size;
    elements.position.textContent = String(answered);
    elements.sessionTotal.textContent = String(state.session.length);
    elements.score.textContent = String(state.score);
    elements.progress.style.width = `${state.session.length ? (answered / state.session.length) * 100 : 0}%`;
    elements.finish.textContent = copy.finish(answered, state.session.length);
  }

  function createQuestion(question, index) {
    const card = document.createElement('article');
    card.className = 'formula-practice-question';
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
        const source = document.createElement('a');
        const params = new URLSearchParams({ herb: profile.name });
        if (spanish) params.set('lang', 'es');
        source.href = `/blocks/myprogress/single-herbs-study-guide.php?${params}`;
        source.textContent = copy.source;
        feedback.classList.add(correct ? 'is-correct' : 'is-incorrect');
        feedback.append(verdict, answerLine, explanation, indication, safety, source);
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
    updateProgress();
  }

  function startSession() {
    state.session = shuffle(state.questions).slice(0, Math.min(state.size, state.questions.length));
    state.answers = new Map();
    state.score = 0;
    elements.setup.hidden = true;
    elements.history.hidden = true;
    elements.results.hidden = true;
    elements.workspace.hidden = false;
    renderSession();
    elements.workspace.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function showSetup() {
    elements.workspace.hidden = true;
    elements.results.hidden = true;
    elements.setup.hidden = false;
    renderHistory();
    updateSetup();
    elements.setup.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function finishSession() {
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
    updateSetup();
  }));
  elements.start.addEventListener('click', startSession);
  elements.repeat.addEventListener('click', startSession);
  elements.configure.addEventListener('click', showSetup);
  elements.returnToSetup.addEventListener('click', showSetup);
  elements.finish.addEventListener('click', finishSession);
  elements.reset.addEventListener('click', () => {
    if (!window.confirm(copy.resetConfirm)) return;
    try { localStorage.removeItem(storageKey); } catch (_) { /* Optional. */ }
    renderHistory();
  });

  fetch('/blocks/myprogress/single-herb-properties.json', { credentials: 'same-origin' })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    })
    .then(data => {
      state.questions = buildQuestions(data);
      if (!state.questions.length) throw new Error('No verified profiles');
      elements.status.textContent = copy.ready;
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
