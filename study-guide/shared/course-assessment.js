(function () {
  'use strict';

  const config = window.STUDY_GUIDE_ASSESSMENT_CONFIG;
  const banks = window.STUDY_GUIDE_ASSESSMENT_BANKS;
  if (!config || !banks) return;

  const letters = ['A', 'B', 'C', 'D'];
  const resultColumns = ['Exam', 'Attempts', 'Last Attempt', 'Average', 'Grade', 'Action'];
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

  function buildExamTable(rows, label) {
    const wrap = el('div', 'history-table-wrap');
    wrap.tabIndex = 0;
    wrap.setAttribute('aria-label', `${label} — scroll horizontally on small screens`);
    const table = el('table', 'history-table exam-level-table');
    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    resultColumns.forEach(column => {
      const th = el('th', '', column);
      th.scope = 'col';
      headerRow.append(th);
    });
    thead.append(headerRow);
    const tbody = document.createElement('tbody');
    rows.forEach(item => {
      const row = document.createElement('tr');
      const name = el('th', '', item.title);
      name.scope = 'row';
      row.append(name);
      (item.values || ['—', '—', '—', '—']).forEach(value => row.append(el('td', '', String(value))));
      const action = el('td', 'history-action');
      if (item.href) {
        const link = el('a', 'history-exam-button', item.actionLabel);
        link.href = item.href;
        if (item.newTab) {
          link.target = '_blank';
          link.rel = 'noopener';
        }
        link.setAttribute('aria-label', item.ariaLabel || `${item.actionLabel} — ${item.title}`);
        action.append(link);
      } else {
        action.textContent = item.missingLabel || 'Not connected';
      }
      row.append(action);
      tbody.append(row);
    });
    table.append(thead, tbody);
    wrap.append(table);
    return wrap;
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
    const summaryTitle = bank.reviewTitle || (/\(Mock \d+\)$/.test(bank.title) ? bank.title : `${bank.title} (${bank.questions.length} Questions)`);
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
      return {bankId, title: bank.title, shortTitle: bank.shortTitle, total: bank.questions.length, checked: Object.keys(state.answers).length, attempts: state.attempts, latest, best};
    });
    const completed = rows.filter(row => row.latest);
    const average = completed.length ? Math.round(completed.reduce((sum, row) => sum + (row.latest.correct / row.total) * 100, 0) / completed.length) : null;
    return {rows, completed, average, inProgress: rows.filter(row => row.checked && row.checked < row.total).length};
  }

  function renderHistory() {
    const host = document.getElementById('assessment-history-content');
    if (!host) return;
    const objectiveRows = summary().rows.filter(row => row.bankId !== 'continuing-education');
    const tbody = host.querySelector('tbody');
    tbody.replaceChildren();
    objectiveRows.forEach(row => {
      const tr = document.createElement('tr');
      const average = row.attempts.length
        ? `${Math.round(row.attempts.reduce((sum, attempt) => sum + (attempt.correct / row.total) * 100, 0) / row.attempts.length)}%`
        : '—';
      const latestGrade = row.latest ? `${Math.round(row.latest.correct / row.total * 100)}%` : '—';
      const lastAttempt = row.latest
        ? new Date(row.latest.completedAt).toLocaleString([], {dateStyle: 'short', timeStyle: 'short'})
        : '—';
      const displayTitle = (row.shortTitle || row.title).replace(/^Objective (\d+) · /, '$1. ');
      const name = el('th', '', displayTitle);
      name.scope = 'row';
      tr.append(name);
      [row.attempts.length || '—', lastAttempt, average, latestGrade].forEach(value => tr.append(el('td', '', String(value))));
      const action = el('td', 'history-action');
      const moodleUrl = config.moodleMockLinks?.[row.bankId];
      if (moodleUrl) {
        const link = el('a', 'history-exam-button', `Start Mock ${row.total}`);
        link.href = moodleUrl;
        link.setAttribute('aria-label', `Start Mock ${row.total} — ${displayTitle}`);
        action.append(link);
      } else {
        action.textContent = 'Not connected';
      }
      tr.append(action);
      tbody.append(tr);
    });
  }

  function buildSchoolHistory() {
    const section = el('details', 'card exam-history exam-level-panel school-exam-panel');
    section.id = 'school-level-exams';
    section.open = true;
    section.append(el('summary', '', config.schoolExamTitle || `School Level Exam — ${config.chapterTitle || config.title}`));
    const body = el('div', 'exam-level-body');
    body.append(el('p', 'exam-level-intro', 'Personal attempts, dates and grades are available through View Exam History in Moodle.'));
    const rows = (config.schoolExams || []).map(exam => ({
      title: exam.title,
      href: exam.url,
      actionLabel: 'Start Exam'
    }));
    const wrap = buildExamTable(rows, 'School Level Exam table');
    body.append(wrap);
    if (config.moodleExamHistoryLink) {
      const footer = el('div', 'exam-history-footer');
      const link = el('a', 'moodle-history-button', 'View Exam History');
      link.href = config.moodleExamHistoryLink;
      footer.append(link);
      body.append(footer);
    }
    section.append(body);
    return section;
  }

  function buildHistory() {
    const section = el('details', 'card exam-history exam-level-panel board-exam-panel');
    section.id = 'board-level-exams';
    section.open = true;
    section.append(el('summary', '', config.boardExamTitle || `Board Level Exam — ${config.chapterTitle || config.title}`));
    const body = el('div', 'exam-level-body');
    const objectiveCount = Object.keys(banks).filter(id => id !== 'continuing-education').length;
    const availableCount = Object.keys(config.moodleMockLinks || {}).length;
    body.append(el('p', 'exam-level-intro', `Access the Mock 10 exams for all objectives here. ${availableCount} of ${objectiveCount} have available links; personal attempts, dates and grades are available in Moodle.`));
    body.append(el('h3', 'exam-level-heading', `${config.chapterTitle || config.title} — ${objectiveCount} objectives`));
    const wrap = buildExamTable([], 'Board Level Exam table');
    const history = el('div', '');
    history.id = 'assessment-history-content';
    history.append(wrap);
    body.append(history);
    section.append(body);
    return section;
  }

  function buildCertificate() {
    const section = el('section', 'ce-certificate');
    section.id = 'certificate-preview';
    section.innerHTML = `<span class="certificate-status">PREVIEW ONLY · NOT ENABLED</span><p>Certificate of Completion</p><h3>${config.certificateTitle || config.title}</h3><p>This preview shows the certificate layout that can be activated only after the course is approved and its required Moodle completion conditions are connected.</p><div class="certificate-name">Student Name</div><p>has completed the required learning activities and assessment for</p><b>${config.title}</b><div class="certificate-meta"><div><b>Completion date</b><span>Generated by approved Moodle completion</span></div><div><b>Certificate ID</b><span>Generated after verified completion</span></div><div><b>Provider / approval</b><span>Pending confirmation</span></div></div><p class="certificate-boundary"><b>Planning framework:</b> ${config.planningHours || 3} hours. This preview does not award Continuing Education credit and is not a valid certificate.</p>`;
    return section;
  }

  function buildCeExam() {
    const bank = banks['continuing-education'];
    const section = el('details', 'card ce-exam-panel exam-level-panel');
    section.id = 'continuing-education-exam';
    section.open = true;
    section.append(el('summary', '', config.ceExamTitle || `Continuing Education Exam — ${config.chapterTitle || config.title}`));
    const body = el('div', 'exam-level-body');
    body.append(el('p', 'exam-level-intro', `After reviewing the ${bank.questions.length} questions and feedback above, open the SCORM activity here. Personal attempts, dates and grades are available in Moodle. The exam opens in another tab.`));
    body.append(el('h3', 'exam-level-heading', `${config.chapterTitle || config.title} — Continuing Education`));
    const wrap = buildExamTable([{
      title: bank.title,
      href: config.ceExamLink,
      actionLabel: 'Start Exam',
      newTab: true,
      ariaLabel: `Start Exam — ${bank.title} (opens in a new tab)`
    }], 'Continuing Education Exam table');
    body.append(wrap);
    section.append(body);
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
        const openExam = el('a', 'objective-mock-button', bank.title);
        const moodleMockUrl = config.moodleMockLinks?.[bankId];
        openExam.href = moodleMockUrl || `#${exam.id}`;
        if (!moodleMockUrl) openExam.addEventListener('click', () => { exam.open = true; });
        actions.prepend(openExam);
      }
      objective.append(exam);
    }
  });

  document.querySelectorAll('.hero-actions .school, .hero-button.school').forEach(link => link.href = '#school-level-exams');
  document.querySelectorAll('.hero-actions .board, .hero-button.board').forEach(link => link.href = '#board-level-exams');
  const languageToggle = document.getElementById('language-toggle');
  if (languageToggle && config.spanishChapterLink) {
    languageToggle.addEventListener('click', () => { window.location.href = config.spanishChapterLink; });
  }

  const schoolHost = document.getElementById('school-level-exams') || document.getElementById('practice-exams') || document.querySelector('[data-assessment-exam-directory]');
  const boardHistory = buildHistory();
  if (schoolHost && config.schoolExams?.length) {
    const schoolHistory = buildSchoolHistory();
    schoolHost.replaceWith(schoolHistory);
    schoolHistory.after(boardHistory);
  } else if (schoolHost) {
    schoolHost.before(boardHistory);
  } else {
    const insertionAnchor = document.querySelector(config.examDirectoryAfterSelector || '#quick-check');
    if (insertionAnchor && config.schoolExams?.length) {
      const schoolHistory = buildSchoolHistory();
      insertionAnchor.after(schoolHistory, boardHistory);
    } else if (insertionAnchor) {
      insertionAnchor.after(boardHistory);
    }
  }

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
    const ceHost = document.getElementById('continuing-education-exam') || document.querySelector('[data-assessment-ce-exam]');
    if (banks['continuing-education']) {
      const examBank = buildBank('continuing-education', banks['continuing-education'], true);
      examBank.classList.add('ce-assessment-bank');
      const examPanel = buildCeExam();
      if (ceHost) ceHost.remove();
      const alignment = document.getElementById('ce-safety-scenarios');
      const insertAfter = alignment || cePlan;
      insertAfter.after(examBank);
      const certificate = buildCertificate();
      examBank.after(certificate);
      certificate.after(examPanel);
    }
  }

  renderHistory();
})();
