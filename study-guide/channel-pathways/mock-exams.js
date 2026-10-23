(function () {
  'use strict';

  const banks = window.CHANNEL_PATHWAY_MOCKS || {};
  const letters = ['A', 'B', 'C', 'D'];
  const wordFiles = {
    'objective-1': 'Primary Channels Exam (10 Questions).docx',
    'objective-2': 'Divergent Channels Exam (10 Questions).docx',
    'objective-3': 'Luo Connecting Channels Exam (10 Questions).docx',
    'objective-4': 'Sinew Channels Exam (10 Questions).docx',
    'objective-5': 'Extraordinary Vessels Exam (10 Questions).docx',
    'objective-6': 'Same Family Comparison Exam (10 Questions).docx',
    'objective-7': 'Look Alike Pathways Exam (10 Questions).docx',
    'objective-8': 'Mixed Image Justification Exam (10 Questions).docx',
    'objective-9': 'Anatomical Region Organization Exam (10 Questions).docx',
    'objective-10': 'Internal Connections Exam (10 Questions).docx',
    'objective-11': 'Convergence Landmarks Exam (10 Questions).docx',
    'objective-12': 'Integrated Pathway Mapping Exam (10 Questions).docx',
    'continuing-education': 'Channel Pathways Continuing Education Exam (10 Questions).docx'
  };
  const scormLinks = {
    'objective-1': 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9368',
    'objective-2': 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9371',
    'objective-3': 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9366',
    'objective-4': 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9370',
    'objective-5': 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9372',
    'objective-6': 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9369',
    'objective-7': 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9373',
    'objective-8': 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9367'
  };
  const resultsStorageKey = 'channel-pathways-mock-results-v1';
  let savedResults = loadResults();

  function loadResults() {
    try {
      const parsed = JSON.parse(localStorage.getItem(resultsStorageKey) || 'null');
      return parsed && parsed.banks ? parsed : {banks: {}};
    } catch (_) {
      return {banks: {}};
    }
  }

  function stateFor(bankId, bank) {
    if (!savedResults.banks[bankId]) {
      savedResults.banks[bankId] = {title: bank.title, total: bank.questions.length, currentAnswers: {}, attempts: [], currentCompletionSaved: false};
    }
    const state = savedResults.banks[bankId];
    state.title = bank.title;
    state.total = bank.questions.length;
    state.currentAnswers = state.currentAnswers || {};
    state.attempts = state.attempts || [];
    return state;
  }

  function saveResults() {
    try { localStorage.setItem(resultsStorageKey, JSON.stringify(savedResults)); } catch (_) { /* Browser storage may be unavailable. */ }
    window.dispatchEvent(new CustomEvent('mockprogresschange', {detail: getResultsSummary()}));
  }

  function recordAnswer(bankId, bank, questionIndex, selectedIndex, isCorrect) {
    const state = stateFor(bankId, bank);
    state.currentAnswers[String(questionIndex)] = {selectedIndex, isCorrect};
    const answers = Object.values(state.currentAnswers);
    if (answers.length === state.total && !state.currentCompletionSaved) {
      const correct = answers.filter(answer => answer.isCorrect).length;
      state.attempts.push({correct, total: state.total, completedAt: new Date().toISOString()});
      state.attempts = state.attempts.slice(-5);
      state.currentCompletionSaved = true;
    }
    saveResults();
  }

  function resetSavedAttempt(bankId, bank) {
    const state = stateFor(bankId, bank);
    state.currentAnswers = {};
    state.currentCompletionSaved = false;
    saveResults();
  }

  function getResultsSummary() {
    const rows = Object.entries(banks).map(([bankId, bank]) => {
      const state = savedResults.banks[bankId] || {};
      const attempts = state.attempts || [];
      const latest = attempts.length ? attempts[attempts.length - 1] : null;
      const best = attempts.length ? Math.max(...attempts.map(attempt => attempt.correct)) : null;
      const currentAnswers = Object.values(state.currentAnswers || {});
      return {
        bankId,
        title: bank.title,
        total: bank.questions.length,
        currentChecked: currentAnswers.length,
        currentCorrect: currentAnswers.filter(answer => answer.isCorrect).length,
        attempts: attempts.length,
        latestCorrect: latest ? latest.correct : null,
        latestPercent: latest ? Math.round((latest.correct / latest.total) * 100) : null,
        bestCorrect: best,
        bestPercent: best === null ? null : Math.round((best / bank.questions.length) * 100)
      };
    });
    const completed = rows.filter(row => row.latestPercent !== null);
    const completedCorrect = completed.reduce((sum, row) => sum + row.latestCorrect, 0);
    const completedQuestions = completed.reduce((sum, row) => sum + row.total, 0);
    return {
      rows,
      bankCount: rows.length,
      completedCount: completed.length,
      inProgressCount: rows.filter(row => row.currentChecked > 0 && row.currentChecked < row.total).length,
      completedAverage: completedQuestions ? Math.round((completedCorrect / completedQuestions) * 100) : null,
      weakest: rows.filter(row => row.latestPercent !== null).sort((a, b) => a.latestPercent - b.latestPercent).slice(0, 3),
      strongest: rows.filter(row => row.latestPercent !== null).sort((a, b) => b.latestPercent - a.latestPercent)[0] || null
    };
  }

  window.CHANNEL_PATHWAY_MOCK_RESULTS = {getSummary: getResultsSummary};

  function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
  }

  function updateProgress(shell) {
    const cards = [...shell.querySelectorAll('.mock-question')];
    const checked = cards.filter(card => card.dataset.checked === 'true');
    const correct = checked.filter(card => card.dataset.correct === 'true');
    const progress = shell.querySelector('.mock-progress');
    if (progress) progress.textContent = `${checked.length} of ${cards.length} checked · ${correct.length} correct`;
  }

  function resetBank(shell, bankId, bank) {
    shell.querySelectorAll('.mock-question').forEach(card => {
      card.dataset.checked = 'false';
      card.dataset.correct = 'false';
      card.classList.remove('is-correct', 'is-incorrect');
      card.querySelectorAll('input').forEach(input => { input.checked = false; input.disabled = false; });
      card.querySelectorAll('.mock-option').forEach(option => option.classList.remove('correct-option', 'selected-wrong'));
      card.querySelector('.mock-feedback').hidden = true;
      card.querySelector('.check-answer').disabled = false;
      card.querySelector('.answer-required').textContent = '';
    });
    updateProgress(shell);
    resetSavedAttempt(bankId, bank);
  }

  function orderedQuestion(question, index) {
    let wrongIndex = 0;
    const original = question.options.map((text, optionIndex) => ({
      text,
      isCorrect: optionIndex === question.correct,
      why: optionIndex === question.correct ? '' : question.why[wrongIndex++]
    }));
    const offset = index % original.length;
    const ordered = original.slice(offset).concat(original.slice(0, offset));
    return {
      ...question,
      options: ordered.map(option => option.text),
      correct: ordered.findIndex(option => option.isCorrect),
      why: ordered.filter(option => !option.isCorrect).map(option => option.why)
    };
  }

  function importText(bank) {
    return bank.questions.map((sourceQuestion, index) => {
      const question = orderedQuestion(sourceQuestion, index);
      const options = question.options.map((option, optionIndex) =>
        `${letters[optionIndex]}) ${option}${optionIndex === question.correct ? ' +++++' : ''}`
      ).join('\n');
      let wrongIndex = 0;
      const distractors = question.options.map((option, optionIndex) => {
        if (optionIndex === question.correct) return '';
        return `${option} — ${question.why[wrongIndex++]}`;
      }).filter(Boolean).join(' ');
      return `${index + 1}. ${question.stem}\n\n${options}\n\nFeedback: ${question.feedback} Why are the other options incorrect? ${distractors}`;
    }).join('\n\n');
  }

  function downloadText(bankId, bank) {
    const blob = new Blob([importText(bank)], {type: 'text/plain;charset=utf-8'});
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = wordFiles[bankId].replace(/\.docx$/, '.txt');
    link.click();
    setTimeout(() => URL.revokeObjectURL(link.href), 1000);
  }

  function buildQuestion(question, index, bankId, shell) {
    let wrongIndex = 0;
    const originalOptions = question.options.map((text, optionIndex) => ({
      text,
      isCorrect: optionIndex === question.correct,
      why: optionIndex === question.correct ? '' : question.why[wrongIndex++]
    }));
    const offset = index % originalOptions.length;
    const options = originalOptions.slice(offset).concat(originalOptions.slice(0, offset));
    const correctIndex = options.findIndex(option => option.isCorrect);
    const card = el('article', 'mock-question');
    card.dataset.checked = 'false';
    card.dataset.correct = 'false';

    const heading = el('h4', 'mock-question-title', `${index + 1}. ${question.stem}`);
    const fieldset = document.createElement('fieldset');
    fieldset.setAttribute('aria-labelledby', `${bankId}-question-${index}`);
    heading.id = `${bankId}-question-${index}`;
    fieldset.append(heading);

    const optionList = el('div', 'mock-options');
    options.forEach((option, optionIndex) => {
      const label = el('label', 'mock-option');
      const input = document.createElement('input');
      input.type = 'radio';
      input.name = `${bankId}-q-${index}`;
      input.value = String(optionIndex);
      const letter = el('b', 'option-letter', `${letters[optionIndex]})`);
      const text = el('span', '', option.text);
      label.append(input, letter, text);
      optionList.append(label);
    });
    fieldset.append(optionList);

    const controls = el('div', 'mock-question-controls');
    const check = el('button', 'check-answer', 'Check answer');
    check.type = 'button';
    const required = el('span', 'answer-required');
    required.setAttribute('role', 'status');
    controls.append(check, required);
    fieldset.append(controls);

    const feedback = el('section', 'mock-feedback');
    feedback.hidden = true;
    feedback.setAttribute('aria-live', 'polite');
    const result = el('p', 'mock-result');
    const answer = el('p', 'correct-answer');
    const explanation = el('p', 'feedback-explanation');
    const whyTitle = el('h5', '', 'Why are the other options incorrect?');
    const whyList = el('ul', 'wrong-answer-feedback');
    feedback.append(result, answer, explanation, whyTitle, whyList);
    fieldset.append(feedback);
    card.append(fieldset);

    function showAnswer(selectedIndex, persist) {
      const isCorrect = selectedIndex === correctIndex;
      required.textContent = '';
      card.dataset.checked = 'true';
      card.dataset.correct = String(isCorrect);
      card.classList.toggle('is-correct', isCorrect);
      card.classList.toggle('is-incorrect', !isCorrect);
      fieldset.querySelectorAll('input').forEach(input => { input.disabled = true; });
      fieldset.querySelectorAll('.mock-option').forEach((option, optionIndex) => {
        option.classList.toggle('correct-option', optionIndex === correctIndex);
        option.classList.toggle('selected-wrong', optionIndex === selectedIndex && !isCorrect);
      });
      result.textContent = isCorrect ? 'Correct' : 'Review this answer';
      answer.textContent = `Correct answer: ${letters[correctIndex]}) ${options[correctIndex].text}`;
      explanation.textContent = `Feedback: ${question.feedback}`;
      whyList.replaceChildren();
      options.forEach((option, optionIndex) => {
        if (optionIndex === correctIndex) return;
        const item = document.createElement('li');
        const optionName = el('b', '', `${letters[optionIndex]}) ${option.text} — `);
        item.append(optionName, document.createTextNode(option.why));
        whyList.append(item);
      });
      feedback.hidden = false;
      check.disabled = true;
      updateProgress(shell);
      if (persist) recordAnswer(bankId, banks[bankId], index, selectedIndex, isCorrect);
    }

    check.addEventListener('click', () => {
      const selected = fieldset.querySelector('input:checked');
      if (!selected) {
        required.textContent = 'Select one answer first.';
        return;
      }
      showAnswer(Number(selected.value), true);
    });

    const stored = savedResults.banks[bankId] && savedResults.banks[bankId].currentAnswers ? savedResults.banks[bankId].currentAnswers[String(index)] : null;
    if (stored && Number.isInteger(Number(stored.selectedIndex)) && options[Number(stored.selectedIndex)]) {
      const selected = fieldset.querySelector(`input[value="${Number(stored.selectedIndex)}"]`);
      if (selected) selected.checked = true;
      showAnswer(Number(stored.selectedIndex), false);
    }

    return card;
  }

  function buildBank(host, bankId, bank) {
    const shell = document.createElement('details');
    shell.className = 'mock-shell';
    shell.id = `mock-${bankId}`;
    const summary = el('summary', '', bank.title);
    const content = el('div', 'mock-content');
    const intro = el('p', 'mock-intro', bank.intro);
    const note = el('p', 'mock-format-note', 'Format: select one answer, check it, then review the correct answer, its explanation and why each alternative is incorrect.');
    const importNote = el('p', 'mock-import-note', 'Import files keep A–D beside the four options, but deliberately omit option letters inside Feedback so the exam-authoring program does not confuse them with additional answers.');
    const toolbar = el('div', 'mock-toolbar');
    const progress = el('strong', 'mock-progress', `0 of ${bank.questions.length} checked · 0 correct`);
    const tools = el('div', 'mock-import-tools');
    const copy = el('button', 'copy-import', 'Copy import format');
    copy.type = 'button';
    copy.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(importText(bank));
        copy.textContent = 'Copied — feedback has no option letters';
      } catch (_) {
        copy.textContent = 'Copy unavailable — download the file';
      }
    });
    const textDownload = el('button', 'download-import', 'Download TXT');
    textDownload.type = 'button';
    textDownload.addEventListener('click', () => downloadText(bankId, bank));
    const wordDownload = el('a', 'word-import', 'Download Word (.docx)');
    wordDownload.href = `assets/downloads/${wordFiles[bankId]}`;
    wordDownload.download = wordFiles[bankId];
    if (scormLinks[bankId]) {
      const openExam = el('a', 'word-import', 'Open exam');
      openExam.href = scormLinks[bankId];
      openExam.target = '_blank';
      openExam.rel = 'noopener';
      tools.append(openExam);
    }
    const reset = el('button', 'reset-mock', 'Reset this practice exam');
    reset.type = 'button';
    reset.addEventListener('click', () => resetBank(shell, bankId, bank));
    tools.append(copy, textDownload, wordDownload, reset);
    toolbar.append(progress, tools);
    const list = el('div', 'mock-question-list');
    bank.questions.forEach((question, index) => list.append(buildQuestion(question, index, bankId, shell)));
    content.append(intro, note, importNote, toolbar, list);
    shell.append(summary, content);
    host.append(shell);
    updateProgress(shell);
  }

  document.querySelectorAll('[data-mock-bank]').forEach(host => {
    const bankId = host.dataset.mockBank;
    if (banks[bankId]) buildBank(host, bankId, banks[bankId]);
  });

  const directory = document.getElementById('board-mock-directory');
  if (directory) {
    Object.keys(banks).filter(id => id.startsWith('objective-')).forEach((bankId, index) => {
      const link = el('a', 'mock-directory-link');
      link.href = `#mock-${bankId}`;
      const topic = banks[bankId].title.replace(/^Practice Exam — Objective \d+:\s*/, '').replace(/\s*\(10 Questions\)$/, '');
      link.innerHTML = `<span>Objective ${index + 1} · ${topic}</span><b>Open Practice Exam</b>`;
      link.addEventListener('click', event => {
        event.preventDefault();
        const objective = document.getElementById(bankId);
        const mock = document.getElementById(`mock-${bankId}`);
        if (objective) objective.open = true;
        if (mock) {
          mock.open = true;
          history.replaceState(null, '', link.getAttribute('href'));
          mock.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
      });
      directory.append(link);
    });
  }

  if (location.hash.startsWith('#mock-')) {
    const mock = document.querySelector(location.hash);
    if (mock) {
      mock.open = true;
      let parent = mock.parentElement;
      while (parent) {
        if (parent.tagName === 'DETAILS') parent.open = true;
        parent = parent.parentElement;
      }
      requestAnimationFrame(() => mock.scrollIntoView({block: 'start'}));
    }
  }
  window.dispatchEvent(new CustomEvent('mockprogresschange', {detail: getResultsSummary()}));
})();
