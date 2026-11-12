(() => {
  'use strict';

  const spanish = document.documentElement.lang === 'es';
  const text = {
    loadingError: spanish ? 'No se pudo cargar el banco de preguntas.' : 'The question bank could not be loaded.',
    available: spanish ? 'Banco listo.' : 'Question bank ready.',
    noQuestions: spanish ? 'No hay preguntas con esta combinación de filtros.' : 'No questions match these filters.',
    startPractice: count => spanish ? `Comenzar Quiz (${count}) →` : `Start Quiz (${count}) →`,
    source: number => spanish ? `Pregunta de fuente ${number}` : `Source question ${number}`,
    questionPosition: (position, total, source) => spanish
      ? `Pregunta ${position} de ${total} · Fuente ${source}`
      : `Question ${position} of ${total} · Source ${source}`,
    related: spanish ? 'Relacionada con:' : 'Related to:',
    general: spanish ? 'Pregunta general de fórmulas' : 'General formula question',
    duiAssociation: spanish ? 'Pregunta oficial de Duì Yào' : 'Official Duì Yào question',
    correct: spanish ? 'Correcto' : 'Correct',
    incorrect: spanish ? 'Incorrecto' : 'Incorrect',
    correctAnswer: spanish ? 'Respuesta correcta:' : 'Correct answer:',
    backToQuestion: position => spanish ? `← Volver a la pregunta ${position}` : `← Back to question ${position}`,
    showEnglish: 'Ver inglés original',
    showSpanish: 'Ver traducción al español',
    allAvailable: count => spanish ? `Todas (${count})` : `All (${count})`,
    maximumAvailable: count => spanish
      ? `Máximo disponible para esta selección: ${count} preguntas.`
      : `Maximum available for this selection: ${count} questions.`,
    sessionMaximum: spanish ? 'Selecciona 5, 10, 15 o 20 preguntas por sesión.' : 'Choose 5, 10, 15 or 20 questions per session.',
    finishProgress: (answered, total) => spanish
      ? `Finalizar Quiz (${answered}/${total} respondidas)`
      : `Finish Quiz (${answered}/${total} answered)`,
    skipForNow: spanish ? 'Saltar por ahora ↓' : 'Skip for now ↓',
    historyEmpty: spanish ? 'Completa una sesión para comenzar tu historial.' : 'Complete a session to start your history.',
    attempts: spanish ? 'respuestas' : 'answers',
    accuracy: spanish ? 'precisión' : 'accuracy',
    needsReview: spanish ? 'Fórmulas para reforzar' : 'Formulas to reinforce',
    resetConfirm: spanish ? '¿Borrar el historial guardado en este dispositivo?' : 'Clear the history saved on this device?',
    restartConfirm: spanish ? '¿Reiniciar esta sesión? Se borrarán las respuestas de la sesión actual.' : 'Restart this session? Answers from the current session will be cleared.',
    questionWord: spanish ? 'preguntas' : 'questions',
    modificationsTitle: spanish ? 'Quiz de modificaciones de fórmulas' : 'Formula Modifications Quiz',
    modificationsLead: spanish ? 'Practica exclusivamente las modificaciones clínicas de fórmulas en sesiones enfocadas.' : 'Practice only clinical formula modifications in focused Quiz sessions.',
    modificationsSetup: spanish ? 'Configura el Quiz de modificaciones' : 'Configure the Formula Modifications Quiz',
    openModificationCard: spanish ? 'Abrir la ficha exacta de esta modificación →' : 'Open the exact modification card →',
    modeHelp: {
      all: spanish ? 'Incluye preguntas de todas las fórmulas y temas generales.' : 'Includes questions from every formula and general topic.',
      formula: spanish ? 'Incluye todas las preguntas del Quiz relacionadas con una fórmula específica.' : 'Includes every Quiz question related to one specific formula.',
      category: spanish ? 'Incluye preguntas del Quiz de las fórmulas que pertenecen a una categoría.' : 'Includes Quiz questions from formulas in one category.',
      weak: spanish ? 'Prioriza fórmulas con menos de 80% de precisión; si aún no hay historial, comienza con un diagnóstico general.' : 'Prioritizes formulas below 80% accuracy; if no history exists yet, it starts with a general diagnostic.',
      dui: spanish ? 'Practica acciones y asociaciones de los 51 pares Duì Yào oficiales.' : 'Practice actions and associations from the 51 official Duì Yào pairs.',
      modifications: spanish ? 'Practica modificaciones clínicas verificadas y sus cambios de ingredientes.' : 'Practice verified clinical modifications and their ingredient changes.',
      clinical: spanish ? 'Filtra viñetas de pacientes y preguntas construidas como casos clínicos.' : 'Filter patient vignettes and questions written as clinical cases.',
      patterns: spanish ? 'Practica diagnóstico, patrones, síndromes y mecanismos de desarmonía.' : 'Practice diagnosis, patterns, syndromes and mechanisms of disharmony.',
      cautions: spanish ? 'Practica precauciones, contraindicaciones, toxicidad y uso inapropiado.' : 'Practice cautions, contraindications, toxicity and inappropriate use.',
      indications: spanish ? 'Practica indicaciones, manifestaciones y pistas diagnósticas decisivas.' : 'Practice indications, manifestations and decisive diagnostic clues.',
      actions: spanish ? 'Practica acciones terapéuticas, funciones y principios de tratamiento.' : 'Practice therapeutic actions, functions and treatment principles.'
    }
  };
  const centralHistoryParams = new URLSearchParams(window.location.search);
  const modificationsOnly = centralHistoryParams.get('scope') === 'modifications';
  const storageKey = modificationsOnly
    ? 'formula-modifications-practice-progress-v1'
    : 'formula-practice-progress-v1';
  const centralHistorySesskey = centralHistoryParams.get('sesskey') || '';
  const centralHistoryType = centralHistoryParams.get('historytype') || '';
  let centralAttemptUid = '';
  let centralStartedAt = Date.now();
  let centralSaved = false;
  const createCentralAttemptUid = () => {
    if (globalThis.crypto && typeof globalThis.crypto.randomUUID === 'function') {
      return globalThis.crypto.randomUUID();
    }
    return `formula-quiz-${Date.now()}-${Math.random().toString(36).slice(2)}`;
  };
  const categoryOrder = [
    'release-exterior', 'purge', 'harmonize', 'clear-heat', 'dispel-summerheat',
    'warm-interior', 'exterior-interior-excess', 'tonify', 'stabilize-bind',
    'calm-shen', 'regulate-qi', 'invigorate-blood', 'expel-wind', 'treat-dryness',
    'expel-damp', 'transform-phlegm', 'digestive', 'treat-abscesses-sores', 'modifications'
  ];
  const formulaAliases2026 = new Map([
    ['Ge Gen Huang Lian Huang Qin Tang', 'Ge Gen Huang Qin Huang Lian Tang'],
    ['Jin Gui Shen Qi Wan', 'Shen Qi Wan'],
    ['Qing Wen Bai Du San', 'Qing Wen Bai Du Yin'],
    ['Tai Shan Pan Shi San', 'Taishan Pan Shi San']
  ]);
  const formulaNamePattern = /\b(?:[A-Z][A-Za-zÀ-ÿ¡]*\s+){2,7}(?:Tang|San|Wan|Yin|Dan|Jian|Fang)\b(?!\s+[A-Z][a-z])/g;
  const formulaInstructionPrefix = /^(?:(?:Add|Combine|Replace|Increase|Decrease|Remove|Both|Bath|To)\s+)+/;
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
    answered: document.querySelector('[data-practice-answered]'),
    score: document.querySelector('[data-practice-score]'),
    progressBar: document.querySelector('[data-practice-progress-bar]'),
    questionList: document.querySelector('[data-practice-question-list]'),
    previous: document.querySelector('[data-practice-previous]'),
    next: document.querySelector('[data-practice-next]'),
    restart: document.querySelector('[data-practice-restart]'),
    finish: document.querySelector('[data-practice-finish]'),
    results: document.querySelector('[data-practice-results]'),
    finalScore: document.querySelector('[data-practice-final-score]'),
    breakdown: document.querySelector('[data-practice-breakdown]'),
    repeat: document.querySelector('[data-practice-repeat]'),
    configure: document.querySelector('[data-practice-configure]'),
    language: document.querySelector('[data-practice-language]'),
    questionTotals: [...document.querySelectorAll('[data-practice-question-total]')],
    formulaTotals: [...document.querySelectorAll('[data-practice-formula-total]')],
    categoryTotals: [...document.querySelectorAll('[data-practice-category-total]')],
    sourceViewer: document.querySelector('[data-practice-source-viewer]'),
    sourceFrame: document.querySelector('[data-practice-source-frame]'),
    sourceTitle: document.querySelector('[data-practice-source-title]'),
    sourceReturn: document.querySelector('[data-practice-source-return]')
  };
  const state = {
    data: null,
    translations: new Map(),
    showOriginal: false,
    formulaByNumber: new Map(),
    mode: 'all',
    size: 10,
    useMaximum: false,
    questions: [],
    currentIndex: 0,
    score: 0,
    answers: [],
    answeredBySource: new Map()
  };
  let activeSourceLink = null;

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

  const topicalModes = new Set(['dui', 'modifications', 'clinical', 'patterns', 'cautions', 'indications', 'actions']);

  function questionsForScope(questions) {
    if (modificationsOnly) {
      return questions.filter(question => (question.categoryKeys || []).includes('modifications'));
    }
    return questions;
  }

  function questionSearchText(question) {
    return [
      question.prompt,
      ...(question.body || []),
      ...(question.options || []).map(option => option.text),
      ...(question.feedback || [])
    ].join(' ');
  }

  function questionMatchesFocus(question, mode) {
    const keys = question.categoryKeys || [];
    if (mode === 'dui') return keys.includes('dui-yao');
    if (mode === 'modifications') return keys.includes('modifications');
    if (keys.includes('dui-yao') || keys.includes('modifications')) return false;
    const value = questionSearchText(question);
    if (mode === 'clinical') return /\b(patient|year[- ]old|presents?|complains?|clinical case|case describes?|history of|symptoms include|woman|female|male|man)\b/i.test(value);
    if (mode === 'patterns') return /\b(pattern|syndrome|diagnosis|diagnostic|pathogenesis|etiology|disharmony|presentation)\b/i.test(value);
    if (mode === 'cautions') return /\b(caution|contraindication|contraindicated|safety|avoid|warning|precaution|prohibited|pregnan|toxic|overdose|should not|must not|adverse|risk|improper|inappropriate|attention|long-term|hypertension|dosage)\b/i.test(value);
    if (mode === 'indications') return /\b(indication|indicated|key point|best used|used for|treats?|appropriate for|clinical manifestation)\b/i.test(value);
    if (mode === 'actions') return /\b(action|actions|function|functions|therapeutic|effect|effects|treatment principle|strategy)\b/i.test(value);
    return true;
  }

  function filteredQuestions() {
    const scoped = questionsForScope(state.data.questions);
    if (state.mode === 'formula') {
      const formula = Number(elements.formula.value);
      return scoped.filter(question => question.formulaNumbers.includes(formula));
    }
    if (state.mode === 'category') {
      const category = elements.category.value;
      return scoped.filter(question => question.categoryKeys.includes(category));
    }
    if (state.mode === 'weak') {
      const history = readHistory();
      if (!Number(history.total || 0)) return scoped.filter(question => !(question.categoryKeys || []).includes('dui-yao'));
      const weakFormulas = new Set(Object.entries(history.formulas || {})
        .map(([number, result]) => ({ number: Number(number), total: Number(result.total || 0), correct: Number(result.correct || 0) }))
        .filter(result => result.total > 0 && percentage(result.correct, result.total) < 80)
        .sort((a, b) => percentage(a.correct, a.total) - percentage(b.correct, b.total) || b.total - a.total)
        .slice(0, 8)
        .map(result => result.number));
      if (!weakFormulas.size) return [];
      return scoped.filter(question => (question.formulaNumbers || []).some(number => weakFormulas.has(Number(number))));
    }
    if (topicalModes.has(state.mode)) return scoped.filter(question => questionMatchesFocus(question, state.mode));
    return scoped;
  }

  function updateUrl() {
    const url = new URL(window.location.href);
    url.searchParams.delete('formula');
    url.searchParams.delete('category');
    url.searchParams.delete('focus');
    if (state.mode === 'formula') url.searchParams.set('formula', elements.formula.value);
    if (state.mode === 'category') url.searchParams.set('category', elements.category.value);
    if (state.mode === 'weak') url.searchParams.set('focus', 'weak');
    if (topicalModes.has(state.mode)) url.searchParams.set('focus', state.mode);
    url.searchParams.set('size', String(state.size));
    history.replaceState(null, '', url);
    if (elements.language) elements.language.href = `${elements.language.getAttribute('href').split('?')[0]}${url.search}`;
  }

  function setMode(mode, refresh = true) {
    state.mode = ['all', 'formula', 'category', 'weak', ...topicalModes].includes(mode) ? mode : 'all';
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
    if (!presets.includes(state.size)) state.size = 10;
    state.useMaximum = false;
    if (count > 0 && state.size > count) {
      const availablePreset = presets.filter(size => size <= count).sort((left, right) => right - left)[0];
      state.size = availablePreset || count;
    }
    if (count > 0 && state.size < 1) state.size = Math.min(10, count);
    const sessionCount = Math.min(count, state.size);
    elements.sizeButtons.forEach(button => {
      const size = Number(button.dataset.practiceSize);
      button.disabled = size > count;
      button.setAttribute('aria-pressed', String(!state.useMaximum && size === state.size));
    });
    const showMaximum = false;
    elements.maxSizeButton.hidden = true;
    elements.maxSizeButton.setAttribute('aria-pressed', 'false');
    elements.maxHelp.textContent = text.sessionMaximum;
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
        link.href = formulaProfileUrl(formula);
        link.textContent = formula.name;
        item.append(link, ` — ${percentage(result.correct, result.total)}% (${result.total} ${text.attempts})`);
        list.append(item);
      });
      elements.weaknesses.append(heading, list);
    }
  }

  function populateControls() {
    const scopedQuestions = questionsForScope(state.data.questions);
    const scopedFormulaNumbers = new Set(scopedQuestions.flatMap(question => question.formulaNumbers || []).map(Number));
    state.data.formulas.filter(formula => scopedFormulaNumbers.has(Number(formula.number))).forEach(formula => {
      const count = scopedQuestions.filter(question => question.formulaNumbers.includes(Number(formula.number))).length;
      option(elements.formula, formula.number, `${formula.number}. ${formula.name} (${count})`);
    });
    state.data.categories.filter(category =>
      modificationsOnly ? category.key === 'modifications' : category.key !== 'modifications'
    ).forEach(category => {
      const count = scopedQuestions.filter(question => question.categoryKeys.includes(category.key)).length;
      option(elements.category, category.key, `${spanish ? category.es : category.en} (${count})`);
    });
    const params = new URLSearchParams(window.location.search);
    const requestedFormula = params.get('formula');
    const requestedCategory = params.get('category');
    const requestedFocus = params.get('focus');
    const requestedSize = Number(params.get('size'));
    if ([...elements.formula.options].some(item => item.value === requestedFormula)) elements.formula.value = requestedFormula;
    if ([...elements.category.options].some(item => item.value === requestedCategory)) elements.category.value = requestedCategory;
    if ([5, 10, 15, 20].includes(requestedSize) && requestedSize <= scopedQuestions.length) state.size = requestedSize;
    if (modificationsOnly) {
      elements.category.value = 'modifications';
      setMode('category', false);
      elements.modeButtons[0]?.closest('fieldset')?.setAttribute('hidden', '');
      elements.formulaField.hidden = true;
      elements.categoryField.hidden = true;
      const heroTitle = document.querySelector('.formula-practice-hero h1');
      const heroLead = document.querySelector('.formula-practice-hero h1 + p');
      const setupTitle = document.querySelector('#practice-setup-title');
      if (heroTitle) heroTitle.textContent = text.modificationsTitle;
      if (heroLead) heroLead.textContent = text.modificationsLead;
      if (setupTitle) setupTitle.textContent = text.modificationsSetup;
      document.title = text.modificationsTitle + ' | AcupunctureTests';
    } else if (requestedFocus === 'weak') setMode('weak', false);
    else if (topicalModes.has(requestedFocus)) setMode(requestedFocus, false);
    else if (requestedFormula && [...elements.formula.options].some(item => item.value === requestedFormula)) setMode('formula', false);
    else if (requestedCategory && [...elements.category.options].some(item => item.value === requestedCategory)) setMode('category', false);
    else setMode('all', false);
    state.useMaximum = false;
    elements.setup.hidden = false;
    elements.status.hidden = false;
    updateAvailability();
    renderHistory();
  }

  function formulaProfileUrl(formula) {
    if (spanish && formula.profileUrlEs) return formula.profileUrlEs;
    if (formula.profileUrl) return formula.profileUrl;
    return `${formula.slug}${spanish ? '-es' : ''}.html`;
  }

  function mergeSupplements(data, translations, supplements) {
    const knownCategories = new Set(data.categories.map(category => category.key));
    const knownFormulas = new Set(data.formulas.map(formula => Number(formula.number)));
    const knownQuestions = new Set(data.questions.map(question => String(question.sourceNumber)));
    supplements.filter(Boolean).forEach(supplement => {
      (supplement.categories || []).forEach(category => {
        if (!category?.key || knownCategories.has(category.key)) return;
        data.categories.push(category);
        knownCategories.add(category.key);
      });
      (supplement.formulas || []).forEach(formula => {
        const number = Number(formula?.number);
        if (!number || knownFormulas.has(number)) return;
        data.formulas.push(formula);
        knownFormulas.add(number);
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
    data.totals.formulas = data.formulas.length;
    data.totals.categories = data.categories.filter(category => category.key !== 'modifications').length;
    data.categories.sort((left, right) =>
      categoryOrder.indexOf(left.key) - categoryOrder.indexOf(right.key)
    );
    data.formulas.forEach(formula => {
      formula.questionCount = data.questions.filter(question =>
        question.formulaNumbers.includes(formula.number)
      ).length;
    });
  }

  function duiChoiceIndices(pairs, correctIndex, valueForPair) {
    const indices = [correctIndex];
    const values = new Set([valueForPair(pairs[correctIndex])]);
    for (let step = 1; step < pairs.length && indices.length < 4; step += 1) {
      const candidate = (correctIndex + step * 7) % pairs.length;
      const value = valueForPair(pairs[candidate]);
      if (!value || values.has(value)) continue;
      indices.push(candidate);
      values.add(value);
    }
    const correctPosition = correctIndex % indices.length;
    [indices[0], indices[correctPosition]] = [indices[correctPosition], indices[0]];
    return {indices, correctLabel: String.fromCharCode(65 + correctPosition)};
  }

  function duiOptions(pairs, choice, valueForPair) {
    return choice.indices.map((pairIndex, optionIndex) => ({
      label: String.fromCharCode(65 + optionIndex),
      text: valueForPair(pairs[pairIndex])
    }));
  }

  function appendDuiYaoQuestions(data, translations, pairs) {
    if (!Array.isArray(pairs) || pairs.length !== 51) throw new Error('Unexpected Duì Yào inventory');
    const known = new Set(data.questions.map(question => String(question.sourceNumber)));
    pairs.forEach((pair, index) => {
      const title = String(pair.title || '');
      const characters = String(pair.characters || '');
      const actionEn = String(pair.action?.en || '');
      const actionEs = String(pair.action?.es || actionEn);
      const categoryEn = String(pair.category?.en || '');
      const categoryEs = String(pair.category?.es || categoryEn);
      const pairChoice = duiChoiceIndices(pairs, index, candidate => String(candidate.title || ''));
      const actionChoice = duiChoiceIndices(pairs, index, candidate => String(candidate.action?.en || ''));

      const questions = [
        {
          sourceNumber: 'DUY-' + String(pair.number).padStart(2, '0') + '-PAIR',
          prompt: 'Which Duì Yào pair matches this combined action?',
          body: [actionEn],
          options: duiOptions(pairs, pairChoice, candidate => String(candidate.title || '')),
          correctLabel: pairChoice.correctLabel,
          feedback: [title + ' ' + characters + ': ' + actionEn],
          formulaNumbers: [],
          categoryKeys: ['dui-yao'],
          translationEs: {
            prompt: '¿Qué pareja Duì Yào corresponde a esta acción combinada?',
            body: [actionEs],
            options: duiOptions(pairs, pairChoice, candidate => String(candidate.title || '')),
            feedback: [title + ' ' + characters + ': ' + actionEs]
          }
        },
        {
          sourceNumber: 'DUY-' + String(pair.number).padStart(2, '0') + '-ACTION',
          prompt: 'What is the primary combined action of this Duì Yào pair?',
          body: [title + (characters ? ' · ' + characters : '') + (categoryEn ? ' · ' + categoryEn : '')],
          options: duiOptions(pairs, actionChoice, candidate => String(candidate.action?.en || '')),
          correctLabel: actionChoice.correctLabel,
          feedback: [actionEn],
          formulaNumbers: [],
          categoryKeys: ['dui-yao'],
          translationEs: {
            prompt: '¿Cuál es la acción combinada principal de esta pareja Duì Yào?',
            body: [title + (characters ? ' · ' + characters : '') + (categoryEs ? ' · ' + categoryEs : '')],
            options: duiOptions(pairs, actionChoice, candidate => String(candidate.action?.es || candidate.action?.en || '')),
            feedback: [actionEs]
          }
        }
      ];

      questions.forEach(entry => {
        if (known.has(entry.sourceNumber)) return;
        const {translationEs, ...question} = entry;
        data.questions.push(question);
        known.add(question.sourceNumber);
        if (translations) translations.questions.push({sourceNumber: question.sourceNumber, ...translationEs});
      });
    });
    data.totals.questions = data.questions.length;
  }

  function formulaNamesInQuestion(question) {
    const values = [
      question.prompt,
      ...(question.body || []),
      ...(question.options || []).map(item => item.text),
      ...(question.feedback || [])
    ];
    return values.flatMap(value => String(value || '').match(formulaNamePattern) || [])
      .map(name => name.replace(formulaInstructionPrefix, '').trim())
      .filter(name => name.split(/\s+/).length >= 3);
  }

  function applyGuidelineScope(data, guideline) {
    if (guideline.total !== 181 || guideline.formulas?.length !== 181) {
      throw new Error('Unexpected 2026 formula guideline inventory');
    }
    const officialNames = new Set(guideline.formulas.map(formula => formula.name));
    data.formulas = data.formulas.filter(formula => {
      const officialName = formulaAliases2026.get(formula.name) || formula.name;
      if (!officialNames.has(officialName)) return false;
      formula.name = officialName;
      return true;
    });
    const allowedNumbers = new Set(data.formulas.map(formula => Number(formula.number)));
    data.questions = data.questions.filter(question => {
      if (!question.formulaNumbers?.length || !question.formulaNumbers.every(number => allowedNumbers.has(Number(number)))) {
        return false;
      }
      return formulaNamesInQuestion(question).every(name =>
        officialNames.has(formulaAliases2026.get(name) || name)
      );
    });
    const activeCategoryKeys = new Set(data.questions.flatMap(question => question.categoryKeys || []));
    data.categories = data.categories.filter(category => activeCategoryKeys.has(category.key));
    data.formulas = data.formulas.filter(formula =>
      data.questions.some(question => question.formulaNumbers.includes(Number(formula.number)))
    );
    data.formulas.forEach(formula => {
      formula.questionCount = data.questions.filter(question =>
        question.formulaNumbers.includes(Number(formula.number))
      ).length;
    });
    data.totals.questions = data.questions.length;
    data.totals.formulas = data.formulas.length;
    data.totals.categories = data.categories.filter(category => category.key !== 'modifications').length;
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

  function simplifySourceFrame() {
    if (!elements.sourceFrame) return;
    try {
      const sourceDocument = elements.sourceFrame.contentDocument;
      if (!sourceDocument) return;
      sourceDocument.querySelectorAll('.comparison-topbar .comparison-back').forEach(link => {
        const destination = new URL(link.href, elements.sourceFrame.contentWindow.location.href);
        if (/\/formula-navigator(?:-es)?\.html$/i.test(destination.pathname)) link.remove();
      });
      sourceDocument.querySelector('[data-library-step-back]')?.remove();
      sourceDocument.querySelector('.formula-related-link--practice')?.remove();
    } catch (_) {
      // Formula profiles are same-origin; leave the source readable if access is unavailable.
    }
  }

  function openSourceViewer(event, link, formula, position) {
    if (!elements.sourceViewer || !elements.sourceFrame || !elements.sourceTitle || !elements.sourceReturn) return;
    event.preventDefault();
    activeSourceLink = link;
    elements.sourceTitle.textContent = formula.name;
    elements.sourceReturn.textContent = text.backToQuestion(position);
    elements.sourceFrame.src = link.href;
    elements.sourceViewer.hidden = false;
    document.body.classList.add('formula-practice-source-open');
    elements.sourceReturn.focus();
  }

  function associationLink(formula, position) {
    const link = document.createElement('a');
    link.href = formulaProfileUrl(formula);
    link.textContent = formula.name;
    link.addEventListener('click', event => openSourceViewer(event, link, formula, position));
    return link;
  }

  function modificationCardLink(question) {
    if (!(question.categoryKeys || []).includes('modifications')) return null;
    const formula = state.formulaByNumber.get(Number(question.formulaNumbers?.[0]));
    if (!formula) return null;
    const url = new URL(spanish ? 'formula-modifications-es.html' : 'formula-modifications.html', window.location.href);
    url.searchParams.set('q', formula.name);
    url.searchParams.set('formula', String(formula.number));
    url.searchParams.set('source', String(question.sourceNumber));
    url.searchParams.set('return', window.location.href);
    const link = document.createElement('a');
    link.className = 'comparison-back formula-practice-modification-link';
    link.href = url.toString();
    link.target = 'formulaModificationReview';
    link.textContent = text.openModificationCard;
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
    const modificationLink = modificationCardLink(question);
    if (modificationLink) feedbackText.append(modificationLink);
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
      if (index < state.questions.length - 1) {
        showQuestion(index + 1);
      } else {
        elements.finish.focus();
      }
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
        if (formula) associations.append(associationLink(formula, index + 1));
      });
    } else {
      label.textContent = (question.categoryKeys || []).includes('dui-yao') ? text.duiAssociation : text.general;
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
    const current = state.questions.length ? state.currentIndex + 1 : 0;
    elements.position.textContent = String(current);
    elements.total.textContent = String(state.questions.length);
    elements.answered.textContent = String(answered);
    elements.score.textContent = String(state.score);
    elements.progressBar.style.width = `${state.questions.length ? (current / state.questions.length) * 100 : 0}%`;
    elements.previous.disabled = state.currentIndex <= 0;
    elements.next.disabled = state.currentIndex >= state.questions.length - 1;
    elements.finish.textContent = text.finishProgress(answered, state.questions.length);
  }

  function showQuestion(index, focus = true) {
    if (!state.questions.length) return;
    state.currentIndex = Math.max(0, Math.min(index, state.questions.length - 1));
    [...elements.questionList.children].forEach((card, cardIndex) => {
      const active = cardIndex === state.currentIndex;
      card.hidden = !active;
      card.setAttribute('aria-hidden', String(!active));
    });
    updateSessionProgress();
    if (focus) {
      elements.questionList.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function renderQuestions() {
    elements.questionList.replaceChildren();
    state.questions.forEach((question, index) => {
      elements.questionList.append(createQuestionCard(question, index));
    });
    showQuestion(0, false);
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
    const focusKey = state.mode || 'all';
    history.focuses = history.focuses && typeof history.focuses === 'object' ? history.focuses : {};
    const focus = history.focuses[focusKey] || {total: 0, correct: 0};
    focus.total += 1;
    if (correct) focus.correct += 1;
    history.focuses[focusKey] = focus;
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

  async function saveCentralAttempt() {
    if (!centralHistorySesskey || !centralHistoryType || centralSaved || !centralAttemptUid) return;
    centralSaved = true;
    const body = new URLSearchParams({
      sesskey: centralHistorySesskey,
      attemptuid: centralAttemptUid,
      examtype: centralHistoryType,
      score: String(state.score),
      total: String(state.questions.length),
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
    centralAttemptUid = createCentralAttemptUid();
    centralStartedAt = Date.now();
    centralSaved = false;
    state.questions = shuffle(pool).slice(0, Math.min(state.size, pool.length));
    state.currentIndex = 0;
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

  function restartSession() {
    if (!window.confirm(text.restartConfirm)) return;
    centralAttemptUid = createCentralAttemptUid();
    centralStartedAt = Date.now();
    centralSaved = false;
    state.currentIndex = 0;
    state.score = 0;
    state.answers = [];
    state.answeredBySource = new Map();
    renderQuestions();
    elements.questionList.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
  elements.previous.addEventListener('click', () => showQuestion(state.currentIndex - 1));
  elements.next.addEventListener('click', () => showQuestion(state.currentIndex + 1));
  elements.restart.addEventListener('click', restartSession);
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
  if (elements.sourceViewer) {
    elements.sourceFrame?.addEventListener('load', simplifySourceFrame);
    elements.sourceViewer.querySelectorAll('[data-practice-source-close]').forEach(control => {
      control.addEventListener('click', closeSourceViewer);
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && !elements.sourceViewer.hidden) closeSourceViewer();
    });
  }

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

  const guidelineRequest = fetch('/blocks/myprogress/formula-board-roster-2026.json', { credentials: 'same-origin' })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    });
  const duiYaoRequest = fetch('dui-yao-data.json', { credentials: 'same-origin' })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    });

  Promise.all([bankRequest, translationsRequest, supplementsRequest, guidelineRequest, duiYaoRequest])
    .then(([data, translations, supplements, guideline, duiYaoPairs]) => {
      if (data.totals?.questions < 572 || data.formulas?.length !== 63) throw new Error('Unexpected bank totals');
      mergeSupplements(data, translations, supplements);
      applyGuidelineScope(data, guideline);
      appendDuiYaoQuestions(data, translations, duiYaoPairs);
      state.data = data;
      state.formulaByNumber = new Map(data.formulas.map(formula => [formula.number, formula]));
      const scopedQuestions = questionsForScope(data.questions);
      const scopedFormulaNumbers = new Set(scopedQuestions.flatMap(question => question.formulaNumbers || []).map(Number));
      const scopedCategories = new Set(scopedQuestions.flatMap(question => question.categoryKeys || [])
        .filter(key => !['modifications', 'dui-yao'].includes(key)));
      elements.questionTotals.forEach(element => { element.textContent = String(scopedQuestions.length); });
      elements.formulaTotals.forEach(element => { element.textContent = String(scopedFormulaNumbers.size); });
      elements.categoryTotals.forEach(element => { element.textContent = String(scopedCategories.size); });
      const translationMap = new Map((translations?.questions || []).map(question => [question.sourceNumber, question]));
      if (translations) {
        state.translations = translationMap;
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
