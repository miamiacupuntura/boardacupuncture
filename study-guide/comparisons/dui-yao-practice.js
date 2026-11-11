(() => {
  'use strict';

  const spanish = document.documentElement.lang === 'es';
  const copy = {
    title: spanish ? 'Quiz de Dui Yao' : 'Dui Yao Quiz',
    ready: spanish ? '51 parejas oficiales cargadas.' : '51 official pairs loaded.',
    loadError: spanish ? 'No se pudo cargar el banco de Dui Yao.' : 'The Dui Yao question bank could not be loaded.',
    choose: spanish ? 'Selecciona una respuesta.' : 'Choose one answer.',
    correct: spanish ? 'Correcto.' : 'Correct.',
    incorrect: spanish ? 'Incorrecto.' : 'Incorrect.',
    next: spanish ? 'Siguiente' : 'Next',
    finish: spanish ? 'Finalizar Quiz' : 'Finish Quiz',
    saved: spanish ? 'Resultado guardado en Exam History.' : 'Result saved to Exam History.',
    saveError: spanish ? 'El resultado no pudo guardarse. Intenta nuevamente desde Exam History.' : 'The result could not be saved. Try again from Exam History.',
    actionPrompt: spanish ? '¿Qué pareja Dui Yao corresponde a esta acción combinada?' : 'Which Dui Yao pair matches this combined action?',
    reverseActionPrompt: spanish ? '¿Cuál es la acción combinada principal de esta pareja?' : 'What is the primary combined action of this pair?',
    categoryPrompt: spanish ? '¿Qué categoría corresponde mejor a esta pareja?' : 'Which category best matches this pair?',
    formulaPrompt: spanish ? '¿Qué fórmula clásica está asociada con esta pareja?' : 'Which classical formula is associated with this pair?',
    cluePrompt: spanish ? 'Identifica la pareja por su pista clínica y su fórmula asociada.' : 'Identify the pair from its clinical clue and associated formula.',
    explanation: spanish ? 'Respuesta correcta' : 'Correct answer',
    whyNot: spanish ? '¿Por qué no' : 'Why not',
    reviewFour: spanish ? 'Repasar las cuatro opciones' : 'Review the four options',
    correctBadge: spanish ? 'Correcta' : 'Correct',
    openReview: spanish ? 'Ver relación y aplicación →' : 'Review relationship and application →',
    differentPair: spanish ? 'es una pareja Dui Yao diferente. La pista corresponde a' : 'is a different Dui Yao pair. The clue matches',
    differentAction: spanish ? 'es una acción combinada diferente. La pareja corresponde a' : 'is a different combined action. The pair matches',
    differentCategory: spanish ? 'es una categoría diferente. La pareja corresponde a' : 'is a different category. The pair matches',
    differentFormula: spanish ? 'es una asociación de fórmula diferente. La pareja corresponde a' : 'is a different formula association. The pair matches'
  };

  const elements = {
    status: document.querySelector('[data-dui-status]'),
    setup: document.querySelector('[data-dui-setup]'),
    size: document.querySelector('[data-dui-size]'),
    start: document.querySelector('[data-dui-start]'),
    workspace: document.querySelector('[data-dui-workspace]'),
    position: document.querySelector('[data-dui-position]'),
    progress: document.querySelector('[data-dui-progress]'),
    prompt: document.querySelector('[data-dui-prompt]'),
    context: document.querySelector('[data-dui-context]'),
    options: document.querySelector('[data-dui-options]'),
    feedback: document.querySelector('[data-dui-feedback]'),
    next: document.querySelector('[data-dui-next]'),
    results: document.querySelector('[data-dui-results]'),
    score: document.querySelector('[data-dui-score]'),
    save: document.querySelector('[data-dui-save]'),
    repeat: document.querySelector('[data-dui-repeat]'),
    language: document.querySelector('[data-dui-language]')
  };

  const params = new URLSearchParams(window.location.search);
  const historySesskey = params.get('sesskey') || '';
  const historyType = params.get('historytype') || 'bqd_duiyao';
  const initialSize = Number(params.get('size') || 25);
  const state = {
    records: [],
    questions: [],
    index: 0,
    score: 0,
    answered: false,
    startedAt: 0,
    attemptUid: ''
  };

  function shuffle(values) {
    const copyValues = values.slice();
    for (let index = copyValues.length - 1; index > 0; index -= 1) {
      const target = Math.floor(Math.random() * (index + 1));
      const value = copyValues[index];
      copyValues[index] = copyValues[target];
      copyValues[target] = value;
    }
    return copyValues;
  }

  function unique(values) {
    return Array.from(new Set(values.map(value => String(value || '').trim()).filter(Boolean)));
  }

  function choices(correct, nearbyPool, fullPool) {
    const orderedPool = unique([].concat(shuffle(nearbyPool || []), shuffle(fullPool || [])));
    const distractors = orderedPool.filter(value => value !== correct).slice(0, 3);
    return shuffle([correct].concat(distractors));
  }

  function createAttemptUid() {
    if (globalThis.crypto && typeof globalThis.crypto.randomUUID === 'function') {
      return globalThis.crypto.randomUUID();
    }
    return 'dui-yao-' + Date.now() + '-' + Math.random().toString(36).slice(2);
  }

  function buildQuestions() {
    const records = shuffle(state.records).filter(record => {
      const action = String((record.action && record.action[spanish ? 'es' : 'en']) || '').trim();
      return action.length > 0 && String(record.title || '').trim().length > 0;
    });
    const titles = unique(records.map(record => record.title));

    return records.map(record => {
      const title = String(record.title || '');
      const action = String((record.action && record.action[spanish ? 'es' : 'en']) || '');
      const category = String((record.category && record.category[spanish ? 'es' : 'en']) || '');
      const relatedTitles = records.filter(candidate => {
        const candidateCategory = String((candidate.category && candidate.category[spanish ? 'es' : 'en']) || '');
        return candidate !== record && candidateCategory === category;
      }).map(candidate => candidate.title);

      return {
        prompt: copy.actionPrompt,
        context: action,
        correct: title,
        options: choices(title, relatedTitles, titles),
        explanation: title + ' — ' + action,
        reviewType: 'pair'
      };
    });
  }

  function renderQuestion() {
    const question = state.questions[state.index];
    state.answered = false;
    elements.position.textContent = String(state.index + 1) + ' / ' + String(state.questions.length);
    elements.progress.style.width = (((state.index + 1) / state.questions.length) * 100) + '%';
    elements.prompt.textContent = question.prompt;
    elements.context.textContent = question.context;
    elements.options.replaceChildren();
    elements.feedback.hidden = true;
    elements.feedback.className = 'dui-practice__feedback';
    elements.next.hidden = true;

    question.options.forEach((option, optionIndex) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'dui-practice__option';
      button.textContent = String.fromCharCode(65 + optionIndex) + '. ' + option;
      button.addEventListener('click', () => answerQuestion(button, option));
      elements.options.append(button);
    });
  }

  function reviewUrl(question, option) {
    const review = new URL(spanish ? 'dui-yao-option-review-es.html' : 'dui-yao-option-review.html', window.location.href);
    review.searchParams.set('type', question.reviewType || 'pair');
    review.searchParams.set('value', option);
    review.searchParams.set('return', window.location.href);
    return review.toString();
  }

  function differentReason(question) {
    if (question.reviewType === 'action') return copy.differentAction;
    if (question.reviewType === 'category') return copy.differentCategory;
    if (question.reviewType === 'formula') return copy.differentFormula;
    return copy.differentPair;
  }

  function appendReviewFeedback(question, answer, isCorrect) {
    const heading = document.createElement('strong');
    heading.className = 'dui-practice__feedback-heading';
    heading.textContent = isCorrect ? copy.correct : copy.incorrect;
    elements.feedback.append(heading);

    if (!isCorrect) {
      const reason = document.createElement('p');
      reason.textContent = copy.whyNot + ' “' + answer + '”? “' + answer + '” ' + differentReason(question) + ' “' + question.correct + '”.';
      elements.feedback.append(reason);
    }

    const reviewTitle = document.createElement('strong');
    reviewTitle.className = 'dui-practice__review-title';
    reviewTitle.textContent = copy.reviewFour;
    elements.feedback.append(reviewTitle);

    const links = document.createElement('div');
    links.className = 'dui-practice__review-list';
    question.options.forEach((option, index) => {
      const link = document.createElement('a');
      link.className = 'dui-practice__review-link';
      link.href = reviewUrl(question, option);
      link.target = 'duiYaoOptionReview';
      if (option === question.correct) link.classList.add('is-correct');
      link.textContent = String.fromCharCode(65 + index) + '. ' + option + ' · ' + (option === question.correct ? copy.correctBadge + ' · ' : '') + copy.openReview;
      links.append(link);
    });
    elements.feedback.append(links);
  }

  function answerQuestion(button, answer) {
    if (state.answered) return;
    state.answered = true;
    const question = state.questions[state.index];
    const isCorrect = answer === question.correct;
    if (isCorrect) state.score += 1;

    Array.from(elements.options.children).forEach(optionButton => {
      optionButton.disabled = true;
      const optionText = optionButton.textContent.slice(3);
      if (optionText === question.correct) optionButton.classList.add('is-correct');
    });
    if (!isCorrect) button.classList.add('is-incorrect');

    elements.feedback.hidden = false;
    elements.feedback.classList.add(isCorrect ? 'is-correct' : 'is-incorrect');
    elements.feedback.replaceChildren();
    appendReviewFeedback(question, answer, isCorrect);
    elements.next.hidden = false;
    elements.next.textContent = state.index + 1 === state.questions.length ? copy.finish : copy.next;
  }

  async function saveAttempt() {
    if (!historySesskey || !historyType) {
      elements.save.textContent = copy.saveError;
      return;
    }
    const body = new URLSearchParams({
      sesskey: historySesskey,
      attemptuid: state.attemptUid,
      examtype: historyType,
      score: String(state.score),
      total: String(state.questions.length),
      duration: String(Math.max(0, Math.round((Date.now() - state.startedAt) / 1000))),
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
      if (!response.ok || !payload.success) throw new Error('save failed');
      elements.save.textContent = copy.saved;
    } catch (_) {
      elements.save.textContent = copy.saveError;
    }
  }

  function finishQuiz() {
    elements.workspace.hidden = true;
    elements.results.hidden = false;
    const percentage = state.questions.length ? Math.round((state.score / state.questions.length) * 100) : 0;
    elements.score.textContent = String(state.score) + ' / ' + String(state.questions.length) + ' · ' + String(percentage) + '%';
    elements.save.textContent = '';
    void saveAttempt();
    elements.results.scrollIntoView({behavior: 'smooth', block: 'start'});
  }

  function startQuiz() {
    const requestedSize = Number(elements.size.value);
    state.questions = buildQuestions().slice(0, Math.min(requestedSize, 99));
    state.index = 0;
    state.score = 0;
    state.startedAt = Date.now();
    state.attemptUid = createAttemptUid();
    elements.setup.hidden = true;
    elements.results.hidden = true;
    elements.workspace.hidden = false;
    renderQuestion();
    elements.workspace.scrollIntoView({behavior: 'smooth', block: 'start'});
  }

  elements.start.addEventListener('click', startQuiz);
  elements.next.addEventListener('click', () => {
    if (!state.answered) return;
    if (state.index + 1 >= state.questions.length) {
      finishQuiz();
      return;
    }
    state.index += 1;
    renderQuestion();
  });
  elements.repeat.addEventListener('click', () => {
    elements.results.hidden = true;
    elements.setup.hidden = false;
    elements.setup.scrollIntoView({behavior: 'smooth', block: 'start'});
  });

  if (elements.language) {
    const alternate = spanish ? 'dui-yao-practice.html' : 'dui-yao-practice-es.html';
    const alternateUrl = new URL(alternate, window.location.href);
    params.forEach((value, key) => alternateUrl.searchParams.set(key, value));
    elements.language.href = alternateUrl.toString();
  }

  if ([10, 25, 51].includes(initialSize)) elements.size.value = String(initialSize);

  fetch('dui-yao-data.json', {credentials: 'same-origin'})
    .then(response => {
      if (!response.ok) throw new Error('load failed');
      return response.json();
    })
    .then(records => {
      state.records = Array.isArray(records) ? records : [];
      if (!state.records.length) throw new Error('empty bank');
      elements.status.textContent = copy.ready;
      elements.start.disabled = false;
    })
    .catch(() => {
      elements.status.textContent = copy.loadError;
      elements.status.classList.add('is-error');
    });
})();
