(function () {
  'use strict';

  const config = window.STUDY_GUIDE_ASSESSMENT_CONFIG;
  const banks = window.STUDY_GUIDE_ASSESSMENT_BANKS;
  if (!config || !banks) return;

  const letters = ['A', 'B', 'C', 'D'];
  const storageKey = `study-guide-assessment:${config.courseId}:v1`;
  let store = loadStore();

  function loadStore() {
    try {
      const parsed = JSON.parse(localStorage.getItem(storageKey) || 'null');
      return parsed && parsed.banks ? parsed : {banks: {}};
    } catch (_) { return {banks: {}}; }
  }

  function saveStore() {
    try { localStorage.setItem(storageKey, JSON.stringify(store)); } catch (_) { /* Storage may be unavailable. */ }
    renderHistory();
  }

  function stateFor(bankId) {
    const bank = banks[bankId];
    if (!store.banks[bankId]) store.banks[bankId] = {answers: {}, attempts: []};
    const state = store.banks[bankId];
    state.answers = state.answers || {};
    state.attempts = state.attempts || [];
    state.total = bank.questions.length;
    state.title = bank.title;
    return state;
  }

  function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
  }

  function ordered(question, index) {
    const options = [
      {text: question.answer, correct: true, feedback: ''},
      ...question.distractors.map(item => ({text: item[0], correct: false, feedback: item[1]}))
    ];
    const offset = index % options.length;
    return {...question, options: options.slice(offset).concat(options.slice(0, offset))};
  }

  function questionText(bank) {
    return bank.questions.map((source, index) => {
      const question = ordered(source, index);
      const correctIndex = question.options.findIndex(option => option.correct);
      const choices = question.options.map((option, optionIndex) => `${letters[optionIndex]}) ${option.text}`).join('\n');
      const wrong = question.options.filter(option => !option.correct).map(option => `- ${option.text}: ${option.feedback}`).join('\n');
      return `Question name: ${question.id}\nQuestion type: Multiple Choice — Single answer\nQuestion text: ${question.stem}\n\n${choices}\n\nCorrect answer: ${letters[correctIndex]}) ${question.options[correctIndex].text}\nGeneral feedback: ${question.rationale}\nWhy the other options are incorrect:\n${wrong}`;
    }).join('\n\n---\n\n');
  }

  function download(bankId, bank) {
    const blob = new Blob([questionText(bank)], {type: 'text/plain;charset=utf-8'});
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${config.courseId}-${bankId}-question-bank.txt`;
    link.click();
    setTimeout(() => URL.revokeObjectURL(link.href), 1000);
  }

  async function copyBank(button, bank) {
    try {
      await navigator.clipboard.writeText(questionText(bank));
      const original = button.textContent;
      button.textContent = 'Copied';
      setTimeout(() => { button.textContent = original; }, 1400);
    } catch (_) { button.textContent = 'Copy unavailable'; }
  }

  function updateProgress(shell, bankId) {
    const state = stateFor(bankId);
    const values = Object.values(state.answers);
    const correct = values.filter(answer => answer.correct).length;
    shell.querySelector('.assessment-progress').textContent = `${values.length} of ${state.total} checked · ${correct} correct`;
  }

  function record(bankId, questionId, selected, correct) {
    const state = stateFor(bankId);
    state.answers[questionId] = {selected, correct};
    if (Object.keys(state.answers).length === state.total && !state.savedCompletion) {
      state.attempts.push({correct: Object.values(state.answers).filter(answer => answer.correct).length, total: state.total, completedAt: new Date().toISOString()});
      state.attempts = state.attempts.slice(-8);
      state.savedCompletion = true;
    }
    saveStore();
  }

  function buildQuestion(source, index, bankId, shell) {
    const question = ordered(source, index);
    const card = el('article', 'assessment-question');
    card.dataset.questionId = question.id;
    const code = el('span', 'question-code', question.id);
    const heading = el('h4', '', `${index + 1}. ${question.stem}`);
    const options = el('div', 'assessment-options');
    question.options.forEach((option, optionIndex) => {
      const label = el('label', 'assessment-option');
      const input = document.createElement('input');
      input.type = 'radio';
      input.name = `${bankId}-${question.id}`;
      input.value = String(optionIndex);
      label.append(input, el('b', '', `${letters[optionIndex]})`), el('span', '', option.text));
      options.append(label);
    });
    const controls = el('div', 'assessment-question-controls');
    const check = el('button', '', 'Check answer');
    check.type = 'button';
    const required = el('span', 'answer-required');
    controls.append(check, required);
    const feedback = el('section', 'assessment-feedback');
    feedback.hidden = true;
    feedback.setAttribute('aria-live', 'polite');

    check.addEventListener('click', () => {
      const selected = options.querySelector('input:checked');
      if (!selected) { required.textContent = 'Select one answer first.'; return; }
      required.textContent = '';
      const selectedIndex = Number(selected.value);
      const correctIndex = question.options.findIndex(option => option.correct);
      const isCorrect = selectedIndex === correctIndex;
      card.classList.toggle('is-correct', isCorrect);
      card.classList.toggle('is-incorrect', !isCorrect);
      options.querySelectorAll('input').forEach(input => { input.disabled = true; });
      options.children[correctIndex].classList.add('correct-option');
      if (!isCorrect) options.children[selectedIndex].classList.add('selected-wrong');
      feedback.replaceChildren();
      feedback.append(
        el('p', '', isCorrect ? 'Correct.' : 'Not yet. Review the rationale below.'),
        el('p', '', `Correct answer: ${letters[correctIndex]}) ${question.options[correctIndex].text}`),
        el('p', '', `General feedback: ${question.rationale}`),
        el('h5', '', 'Why the other options are incorrect')
      );
      const list = el('ul', '');
      question.options.forEach((option, optionIndex) => {
        if (!option.correct) list.append(el('li', '', `${letters[optionIndex]}) ${option.feedback}`));
      });
      feedback.append(list);
      feedback.hidden = false;
      check.disabled = true;
      record(bankId, question.id, selectedIndex, isCorrect);
      updateProgress(shell, bankId);
    });
    card.append(code, heading, options, controls, feedback);
    return card;
  }

  function resetBank(shell, bankId) {
    store.banks[bankId] = {answers: {}, attempts: stateFor(bankId).attempts || [], savedCompletion: false};
    shell.querySelectorAll('.assessment-question').forEach(card => {
      card.classList.remove('is-correct', 'is-incorrect');
      card.querySelectorAll('input').forEach(input => { input.checked = false; input.disabled = false; });
      card.querySelectorAll('.assessment-option').forEach(option => option.classList.remove('correct-option', 'selected-wrong'));
      card.querySelector('.assessment-feedback').hidden = true;
      card.querySelector('button').disabled = false;
      card.querySelector('.answer-required').textContent = '';
    });
    saveStore();
    updateProgress(shell, bankId);
  }

  function buildBank(bankId, bank, open) {
    const shell = el('details', 'assessment-bank course-assessment');
    shell.id = `${bankId}-mock-exam`;
    shell.open = Boolean(open);
    const summaryTitle = /\(Mock \d+\)$/.test(bank.title) ? bank.title : `${bank.title} (${bank.questions.length} Questions)`;
    shell.append(el('summary', '', summaryTitle));
    const body = el('div', 'assessment-body');
    body.append(el('p', 'assessment-intro', bank.intro));
    const note = el('p', 'assessment-format-note');
    note.innerHTML = '<b>Authoring format:</b> named single-answer multiple choice, four options, correct answer, general rationale and feedback for every distractor.';
    const tools = el('div', 'assessment-tools');
    const copy = el('button', 'secondary', 'Copy question bank');
    const save = el('button', 'secondary', 'Download TXT');
    const reset = el('button', 'secondary', 'Start a new attempt');
    const progress = el('span', 'assessment-progress');
    copy.type = save.type = reset.type = 'button';
    copy.addEventListener('click', () => copyBank(copy, bank));
    save.addEventListener('click', () => download(bankId, bank));
    reset.addEventListener('click', () => resetBank(shell, bankId));
    tools.append(copy, save, reset, progress);
    body.append(note, tools);
    bank.questions.forEach((question, index) => body.append(buildQuestion(question, index, bankId, shell)));
    shell.append(body);
    updateProgress(shell, bankId);
    return shell;
  }

  function summary() {
    const rows = Object.entries(banks).map(([bankId, bank]) => {
      const state = stateFor(bankId);
      const latest = state.attempts[state.attempts.length - 1];
      const best = state.attempts.length ? Math.max(...state.attempts.map(attempt => attempt.correct)) : null;
      return {bankId, title: bank.title, total: bank.questions.length, checked: Object.keys(state.answers).length, latest, best};
    });
    const completed = rows.filter(row => row.latest);
    const average = completed.length ? Math.round(completed.reduce((sum, row) => sum + (row.latest.correct / row.total) * 100, 0) / completed.length) : null;
    return {rows, completed, average, inProgress: rows.filter(row => row.checked && row.checked < row.total).length};
  }

  function renderHistory() {
    const host = document.getElementById('assessment-history-content');
    if (!host) return;
    const data = summary();
    const objectiveRows = data.rows.filter(row => row.bankId !== 'continuing-education');
    const weakest = data.completed.slice().sort((a, b) => (a.latest.correct / a.total) - (b.latest.correct / b.total))[0];
    host.querySelector('[data-metric="completed"]').textContent = `${objectiveRows.filter(row => row.latest).length} / ${objectiveRows.length}`;
    host.querySelector('[data-metric="average"]').textContent = data.average === null ? '—' : `${data.average}%`;
    host.querySelector('[data-metric="progress"]').textContent = String(data.inProgress);
    host.querySelector('[data-metric="weakest"]').textContent = weakest ? weakest.title.replace(/^Practice Exam — /, '') : '—';
    const tbody = host.querySelector('tbody');
    tbody.replaceChildren();
    data.rows.forEach(row => {
      const tr = document.createElement('tr');
      const latest = row.latest ? `${row.latest.correct}/${row.total} (${Math.round(row.latest.correct / row.total * 100)}%)` : '—';
      const best = row.best === null ? '—' : `${row.best}/${row.total}`;
      const name = el('th', '', row.title);
      name.scope = 'row';
      tr.append(name);
      [`${row.checked}/${row.total}`, latest, best].forEach(value => tr.append(el('td', '', value)));
      const action = el('td', 'history-action');
      const moodleUrl = row.bankId === 'continuing-education'
        ? config.ceExamLink
        : config.moodleMockLinks?.[row.bankId];
      if (moodleUrl) {
        const link = el('a', 'history-exam-button', row.latest ? `Retry Mock ${row.total}` : `Start Mock ${row.total}`);
        link.href = moodleUrl;
        action.append(link);
      } else {
        action.textContent = 'Not connected';
      }
      tr.append(action);
      tbody.append(tr);
    });
    host.querySelector('.history-empty').hidden = data.completed.length > 0 || data.inProgress > 0;
  }

  function buildHistory() {
    const section = el('section', 'card exam-history');
    section.id = 'automatic-exam-history';
    section.innerHTML = `<span class="eyebrow">EXAM HISTORY</span><h2>Exam History — ${config.title}</h2><p>Use one table to open all 12 objective exams and the Continuing Education exam. Practice completed inside this guide is saved automatically in this browser.</p><div id="assessment-history-content"><div class="history-metrics"><article><strong data-metric="completed">—</strong><span>Objective exams completed</span></article><article><strong data-metric="average">—</strong><span>Completed-exam average</span></article><article><strong data-metric="progress">0</strong><span>Exams in progress</span></article><article><strong data-metric="weakest">—</strong><span>Lowest completed result</span></article></div><p class="history-empty">No local practice-exam activity has been recorded yet. You can start any Moodle Mock 10 from the table below.</p><div class="history-table-wrap" tabindex="0" aria-label="Exam History table — scroll horizontally on small screens"><table class="history-table"><thead><tr><th scope="col">Exam</th><th scope="col">Local progress</th><th scope="col">Latest local result</th><th scope="col">Best local score</th><th scope="col">Action</th></tr></thead><tbody></tbody></table></div><p class="history-note"><b>Two separate records:</b> the progress columns show only practice completed inside this guide. Official SCORM attempts, dates and scores remain in Moodle Exam History and are not transmitted to this page.</p></div>`;
    const directory = el('section', 'assessment-directory');
    directory.innerHTML = '<h3>Objective practice directory</h3><p>Use this directory after reading the objectives in order.</p>';
    const grid = el('div', 'assessment-directory-grid');
    Object.entries(banks).filter(([id]) => id !== 'continuing-education').forEach(([id, bank]) => {
      const link = el('a', '', bank.shortTitle || bank.title);
      link.href = `#${id}-mock-exam`;
      grid.append(link);
    });
    directory.append(grid);
    section.append(directory);
    return section;
  }

  function buildCertificate() {
    const section = el('section', 'ce-certificate');
    section.id = 'certificate-preview';
    section.innerHTML = `<span class="certificate-status">PREVIEW ONLY · NOT ENABLED</span><p>Certificate of Completion</p><h3>${config.certificateTitle || config.title}</h3><p>This preview shows the certificate layout that can be activated only after the course is approved and its required Moodle completion conditions are connected.</p><div class="certificate-name">Student Name</div><p>has completed the required learning activities and assessment for</p><b>${config.title}</b><div class="certificate-meta"><div><b>Completion date</b><span>Generated by approved Moodle completion</span></div><div><b>Certificate ID</b><span>Generated after verified completion</span></div><div><b>Provider / approval</b><span>Pending confirmation</span></div></div><p class="certificate-boundary"><b>Planning framework:</b> ${config.planningHours || 3} hours. This preview does not award Continuing Education credit and is not a valid certificate.</p>`;
    return section;
  }

  Object.entries(banks).forEach(([bankId, bank]) => {
    if (bankId === 'continuing-education') return;
    const objective = document.getElementById(bankId);
    if (objective) {
      const exam = buildBank(bankId, bank, false);
      const body = objective.querySelector(':scope > div');
      if (body) {
        let actions = body.querySelector(':scope > .objective-actions');
        if (!actions) {
          actions = el('div', 'objective-actions');
          body.append(actions);
        }
        const openExam = el('a', 'objective-mock-button', `Open ${bank.questions.length}-question mock exam`);
        const moodleMockUrl = config.moodleMockLinks?.[bankId];
        openExam.href = moodleMockUrl || `#${exam.id}`;
        if (!moodleMockUrl) openExam.addEventListener('click', () => { exam.open = true; });
        actions.prepend(openExam);
      }
      objective.append(exam);
    }
  });

  const practiceExams = document.getElementById('practice-exams');
  if (practiceExams) practiceExams.before(buildHistory());

  const cePlan = document.getElementById('continuing-education-plan');
  if (cePlan) {
    const ceTable = cePlan.querySelector('.ce-learning-table');
    const ceSummary = cePlan.querySelector(':scope > summary');
    if (ceTable && ceSummary) ceSummary.after(ceTable);
    if (!document.getElementById('continuing-education-start')) {
      const tab = el('div', 'ce-start-tab', config.cePlanTitle || 'Continuing Education begins here');
      tab.id = 'continuing-education-start';
      cePlan.before(tab);
    }
    const ceHost = document.getElementById('continuing-education-exam');
    if (ceHost && banks['continuing-education']) {
      const examPanel = el('details', 'card ce-exam-panel');
      examPanel.id = 'continuing-education-exam';
      examPanel.append(el('summary', '', config.ceExamTitle || 'Continuing Education Exam'));
      if (config.ceExamLink) {
        const access = el('aside', 'ce-exam-status');
        const copy = el('div', '');
        copy.append(
          el('span', 'eyebrow', 'MOODLE CONTINUING EDUCATION MOCK'),
          el('h3', '', banks['continuing-education'].title),
          el('p', '', `Open the ${banks['continuing-education'].questions.length}-question Moodle activity. Official attempts and results remain in Moodle Exam History.`)
        );
        const link = el('a', '', `Open Continuing Education Mock ${banks['continuing-education'].questions.length}`);
        link.href = config.ceExamLink;
        access.append(copy, link);
        examPanel.append(access);
      }
      const examBank = buildBank('continuing-education', banks['continuing-education'], true);
      examBank.classList.add('ce-assessment-bank');
      examPanel.append(examBank);
      ceHost.remove();
      const alignment = document.getElementById('ce-safety-scenarios');
      if (alignment) alignment.after(examPanel);
      else cePlan.after(examPanel);
      examPanel.after(buildCertificate());
    }
  }

  renderHistory();
})();
