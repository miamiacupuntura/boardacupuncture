(() => {
  'use strict';

  const spanish = document.documentElement.lang === 'es';
  const pageSize = 20;
  const copy = {
    loadError: spanish ? 'No se pudo cargar el banco de fórmulas.' : 'The formula bank could not be loaded.',
    ready: spanish ? 'Banco de estudio listo.' : 'Study bank ready.',
    question: spanish ? 'Pregunta' : 'Question',
    correctAnswer: spanish ? 'Respuesta correcta:' : 'Correct answer:',
    feedback: spanish ? 'Feedback:' : 'Feedback:',
    related: spanish ? 'Fórmulas relacionadas:' : 'Related formulas:',
    noMatches: spanish ? 'No hay preguntas con estos filtros.' : 'No questions match these filters.',
    previous: spanish ? '← Anterior' : '← Previous',
    next: spanish ? 'Siguiente →' : 'Next →',
    reveal: spanish ? 'Mostrar respuesta' : 'Reveal answer',
    hide: spanish ? 'Ocultar respuesta' : 'Hide answer'
  };
  const elements = {
    total: document.querySelector('[data-formula-study-total]'),
    status: document.querySelector('[data-formula-study-status]'),
    controls: document.querySelector('[data-formula-study-controls]'),
    search: document.querySelector('[data-formula-study-search]'),
    formula: document.querySelector('[data-formula-study-formula]'),
    category: document.querySelector('[data-formula-study-category]'),
    modes: document.querySelector('[data-formula-study-modes]'),
    modeButtons: [...document.querySelectorAll('[data-formula-study-mode]')],
    views: [...document.querySelectorAll('[data-formula-study-view]')],
    count: document.querySelector('[data-formula-study-count]'),
    range: document.querySelector('[data-formula-study-range]'),
    list: document.querySelector('[data-formula-study-list]'),
    pagination: document.querySelector('[data-formula-study-pagination]'),
    flashPosition: document.querySelector('[data-formula-flash-position]'),
    flashTotal: document.querySelector('[data-formula-flash-total]'),
    flashLabel: document.querySelector('[data-formula-flash-label]'),
    flashQuestion: document.querySelector('[data-formula-flash-question]'),
    flashBody: document.querySelector('[data-formula-flash-body]'),
    flashReveal: document.querySelector('[data-formula-flash-reveal]'),
    flashAnswer: document.querySelector('[data-formula-flash-answer]'),
    flashPrevious: document.querySelector('[data-formula-flash-previous]'),
    flashNext: document.querySelector('[data-formula-flash-next]'),
    flashShuffle: document.querySelector('[data-formula-flash-shuffle]')
  };
  const state = {
    data: null,
    translations: new Map(),
    formulaByNumber: new Map(),
    filtered: [],
    mode: 'answers',
    page: 1,
    flashIndex: 0,
    flashRevealed: false
  };

  function option(parent, value, text) {
    const item = document.createElement('option');
    item.value = String(value);
    item.textContent = text;
    parent.append(item);
  }

  function asArray(value) {
    if (Array.isArray(value)) return value;
    if (value === null || value === undefined || value === '') return [];
    return [String(value)];
  }

  function displayed(question) {
    return spanish ? (state.translations.get(String(question.sourceNumber)) || question) : question;
  }

  function mergeData(data, translations, supplements) {
    const knownQuestions = new Set(data.questions.map(question => String(question.sourceNumber)));
    const knownCategories = new Set(data.categories.map(category => category.key));
    (translations?.questions || []).forEach(question => {
      state.translations.set(String(question.sourceNumber), question);
    });
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
        if (translationEs) state.translations.set(String(question.sourceNumber), translationEs);
      });
    });
    data.totals.questions = data.questions.length;
    data.totals.categories = data.categories.length;
    return data;
  }

  function searchText(question) {
    const shown = displayed(question);
    const formulaNames = (question.formulaNumbers || [])
      .map(number => state.formulaByNumber.get(Number(number))?.name || '')
      .join(' ');
    return [
      shown.prompt,
      ...asArray(shown.body),
      ...(shown.options || []).map(answer => answer.text),
      ...asArray(shown.feedback),
      formulaNames
    ].join(' ').toLocaleLowerCase(spanish ? 'es' : 'en');
  }

  function applyFilters() {
    const query = elements.search.value.trim().toLocaleLowerCase(spanish ? 'es' : 'en');
    const formula = elements.formula.value;
    const category = elements.category.value;
    state.filtered = state.data.questions.filter(question => {
      if (formula !== 'all' && !(question.formulaNumbers || []).includes(Number(formula))) return false;
      if (category !== 'all' && !(question.categoryKeys || []).includes(category)) return false;
      return !query || searchText(question).includes(query);
    });
    state.page = 1;
    state.flashIndex = 0;
    state.flashRevealed = false;
    render();
  }

  function appendParagraphs(parent, values) {
    asArray(values).forEach(value => {
      const paragraph = document.createElement('p');
      paragraph.textContent = value;
      parent.append(paragraph);
    });
  }

  function formulaLinks(question) {
    const container = document.createElement('div');
    container.className = 'question-study-associations';
    const label = document.createElement('strong');
    label.textContent = copy.related;
    container.append(label);
    (question.formulaNumbers || []).forEach(number => {
      const formula = state.formulaByNumber.get(Number(number));
      if (!formula) return;
      const link = document.createElement('a');
      link.href = `${formula.slug}${spanish ? '-es' : ''}.html`;
      link.textContent = formula.name;
      container.append(link);
    });
    return container;
  }

  function answerBlock(question) {
    const shown = displayed(question);
    const correct = (shown.options || []).find(answer => answer.label === question.correctLabel);
    const block = document.createElement('div');
    block.className = 'question-study-feedback';
    const answer = document.createElement('p');
    const strong = document.createElement('strong');
    strong.textContent = `${copy.correctAnswer} ${question.correctLabel}. ${correct?.text || ''}`;
    answer.append(strong);
    const feedbackLabel = document.createElement('p');
    const feedbackStrong = document.createElement('strong');
    feedbackStrong.textContent = copy.feedback;
    feedbackLabel.append(feedbackStrong);
    block.append(answer, feedbackLabel);
    appendParagraphs(block, shown.feedback);
    if ((question.formulaNumbers || []).length) block.append(formulaLinks(question));
    return block;
  }

  function createCard(question, absoluteIndex) {
    const shown = displayed(question);
    const card = document.createElement('article');
    card.className = 'question-study-card';
    const label = document.createElement('p');
    label.className = 'comparison-eyebrow';
    label.textContent = `${copy.question} ${absoluteIndex + 1}`;
    const prompt = document.createElement('h3');
    prompt.textContent = shown.prompt || '';
    const body = document.createElement('div');
    body.className = 'question-study-body';
    appendParagraphs(body, shown.body);
    const options = document.createElement('ol');
    options.className = 'question-study-options';
    (shown.options || []).forEach(answer => {
      const item = document.createElement('li');
      if (answer.label === question.correctLabel) item.classList.add('is-correct');
      item.textContent = `${answer.label}. ${answer.text}`;
      options.append(item);
    });
    card.append(label, prompt, body, options, answerBlock(question));
    return card;
  }

  function pageButton(label, page, current = false) {
    const button = document.createElement('button');
    button.type = 'button';
    button.textContent = label;
    if (current) button.setAttribute('aria-current', 'page');
    button.addEventListener('click', () => {
      state.page = page;
      renderAnswers();
      elements.views[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    return button;
  }

  function renderPagination(totalPages) {
    elements.pagination.replaceChildren();
    if (totalPages <= 1) return;
    if (state.page > 1) elements.pagination.append(pageButton(copy.previous, state.page - 1));
    const first = Math.max(1, Math.min(state.page - 2, totalPages - 4));
    const last = Math.min(totalPages, first + 4);
    for (let page = first; page <= last; page += 1) {
      elements.pagination.append(pageButton(String(page), page, page === state.page));
    }
    if (state.page < totalPages) elements.pagination.append(pageButton(copy.next, state.page + 1));
  }

  function renderAnswers() {
    const total = state.filtered.length;
    const totalPages = Math.max(1, Math.ceil(total / pageSize));
    state.page = Math.min(state.page, totalPages);
    const start = (state.page - 1) * pageSize;
    const shown = state.filtered.slice(start, start + pageSize);
    elements.count.textContent = String(total);
    elements.range.textContent = total ? `${start + 1}–${start + shown.length}` : '0';
    elements.list.replaceChildren();
    if (!shown.length) {
      const empty = document.createElement('p');
      empty.className = 'question-study-empty';
      empty.textContent = copy.noMatches;
      elements.list.append(empty);
    } else {
      shown.forEach((question, index) => elements.list.append(createCard(question, start + index)));
    }
    renderPagination(totalPages);
  }

  function renderFlashcard() {
    const total = state.filtered.length;
    elements.flashTotal.textContent = String(total);
    if (!total) {
      elements.flashPosition.textContent = '0';
      elements.flashLabel.textContent = '';
      elements.flashQuestion.textContent = copy.noMatches;
      elements.flashBody.replaceChildren();
      elements.flashAnswer.replaceChildren();
      elements.flashAnswer.hidden = true;
      elements.flashReveal.disabled = true;
      return;
    }
    state.flashIndex = ((state.flashIndex % total) + total) % total;
    const question = state.filtered[state.flashIndex];
    const shown = displayed(question);
    elements.flashPosition.textContent = String(state.flashIndex + 1);
    elements.flashLabel.textContent = `${copy.question} ${state.flashIndex + 1}`;
    elements.flashQuestion.textContent = shown.prompt || '';
    elements.flashBody.replaceChildren();
    appendParagraphs(elements.flashBody, shown.body);
    elements.flashAnswer.replaceChildren(answerBlock(question));
    elements.flashAnswer.hidden = !state.flashRevealed;
    elements.flashReveal.disabled = false;
    elements.flashReveal.textContent = state.flashRevealed ? copy.hide : copy.reveal;
  }

  function render() {
    elements.views.forEach(view => {
      view.hidden = view.dataset.formulaStudyView !== state.mode;
    });
    elements.modeButtons.forEach(button => {
      button.setAttribute('aria-pressed', String(button.dataset.formulaStudyMode === state.mode));
    });
    if (state.mode === 'answers') renderAnswers();
    else renderFlashcard();
  }

  function populateControls() {
    state.data.formulas.forEach(formula => option(elements.formula, formula.number, `${formula.number}. ${formula.name}`));
    state.data.categories.forEach(category => option(
      elements.category,
      category.key,
      spanish ? (category.es || category.en) : (category.en || category.es)
    ));
  }

  elements.search.addEventListener('input', applyFilters);
  elements.formula.addEventListener('change', applyFilters);
  elements.category.addEventListener('change', applyFilters);
  elements.modeButtons.forEach(button => button.addEventListener('click', () => {
    state.mode = button.dataset.formulaStudyMode;
    render();
  }));
  elements.flashReveal.addEventListener('click', () => {
    state.flashRevealed = !state.flashRevealed;
    renderFlashcard();
  });
  elements.flashPrevious.addEventListener('click', () => {
    state.flashIndex -= 1;
    state.flashRevealed = false;
    renderFlashcard();
  });
  elements.flashNext.addEventListener('click', () => {
    state.flashIndex += 1;
    state.flashRevealed = false;
    renderFlashcard();
  });
  elements.flashShuffle.addEventListener('click', () => {
    for (let index = state.filtered.length - 1; index > 0; index -= 1) {
      const target = Math.floor(Math.random() * (index + 1));
      [state.filtered[index], state.filtered[target]] = [state.filtered[target], state.filtered[index]];
    }
    state.flashIndex = 0;
    state.flashRevealed = false;
    renderFlashcard();
  });

  const bankRequest = fetch('formula-practice-data.json', { credentials: 'same-origin' }).then(response => {
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
  });
  const translationsRequest = spanish
    ? fetch('formula-practice-translations-es.json', { credentials: 'same-origin' }).then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    })
    : Promise.resolve(null);
  const supplementsRequest = fetch('formula-modifications-manifest.json', { credentials: 'same-origin' })
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
      console.error('Formula supplements unavailable', error);
      return [];
    });

  Promise.all([bankRequest, translationsRequest, supplementsRequest])
    .then(([data, translations, supplements]) => {
      state.data = mergeData(data, translations, supplements);
      state.formulaByNumber = new Map(data.formulas.map(formula => [Number(formula.number), formula]));
      elements.total.textContent = String(data.questions.length);
      populateControls();
      state.filtered = [...data.questions];
      elements.status.textContent = copy.ready;
      elements.controls.hidden = false;
      elements.modes.hidden = false;
      render();
    })
    .catch(error => {
      console.error(error);
      elements.status.textContent = copy.loadError;
      elements.status.classList.add('is-error');
    });
})();
