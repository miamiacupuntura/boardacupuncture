(() => {
  'use strict';

  const storageKey = 'acupuncturetests-point-contraindications-v1';
  const questions = [
    {
      prompt: 'A brief course table labels a point “contraindicated” but gives no date or explanation. What is the best next study step?',
      options: ['Treat it as a universal permanent prohibition.', 'Check the complete point entry and current safety guidance.', 'Ignore the warning because it is brief.', 'Convert it into an insertion-depth rule.'],
      answer: 1,
      feedback: 'A short or historical label must retain its source context and be checked against complete, current references.'
    },
    {
      prompt: 'Which item belongs primarily to an anatomical-risk category?',
      options: ['Pregnancy context', 'A source-specific moxa restriction', 'Pleura beneath a thoracic region', 'A historical prohibition'],
      answer: 2,
      feedback: 'The pleura is a vulnerable anatomical structure. Pregnancy, modality and historical notes are different kinds of precautions.'
    },
    {
      prompt: 'Why should pregnancy cautions be listed separately from vessel or nerve warnings?',
      options: ['They are never important.', 'They are contextual and require individual obstetric assessment.', 'They apply only to textbooks.', 'They describe the same injury mechanism.'],
      answer: 1,
      feedback: 'Pregnancy is a patient/context consideration, not the same injury mechanism as direct trauma to a vessel or nerve.'
    },
    {
      prompt: 'What does the F in the SAFER method mean?',
      options: ['Force recall', 'Find the complete source', 'Follow the channel color', 'Finish without checking'],
      answer: 1,
      feedback: 'Find the source: verify the complete point entry and current safety material.'
    },
    {
      prompt: 'A warning names moxibustion specifically. What should you avoid assuming?',
      options: ['That the source matters.', 'That it automatically prohibits every treatment modality.', 'That the point has a location.', 'That the wording may be historical.'],
      answer: 1,
      feedback: 'Keep a restriction attached to the named modality unless a complete source supports a broader conclusion.'
    },
    {
      prompt: 'What is the most useful way to organize the large Urinary Bladder caution bank?',
      options: ['Memorize random point numbers.', 'Use regional clusters and their underlying structures.', 'Assume every point has the same risk.', 'Study only the distal points.'],
      answer: 1,
      feedback: 'Regional clustering reveals repeated patterns such as thoracic pleural and lumbar organ relationships.'
    },
    {
      prompt: 'Which comparison best separates channel identity from safety reasoning?',
      options: ['The channel name alone determines the risk.', 'Local anatomy explains the warning even when points share a channel.', 'Every point in one channel has one restriction.', 'Color determines the structure at risk.'],
      answer: 1,
      feedback: 'Safety warnings belong to the individual location, anatomy, patient and method—not simply to the channel name.'
    },
    {
      prompt: 'If a point does not appear in a short caution table, what can you conclude?',
      options: ['It is safe in every patient.', 'It has no anatomical relationships.', 'Nothing definitive; verify its complete point entry.', 'It requires no training.'],
      answer: 2,
      feedback: 'Absence from a selected table is not evidence of safety. Always consult the complete reference.'
    },
    {
      prompt: 'Which flashcard answer is the strongest?',
      options: ['Only a memorized keyword.', 'Risk family, structure/context, source and why a distractor fails.', 'Only the channel color.', 'An unsupported depth value.'],
      answer: 1,
      feedback: 'A complete card supports classification, source verification and discrimination between close alternatives.'
    },
    {
      prompt: 'What is the correct boundary for this chapter?',
      options: ['It replaces supervised needling instruction.', 'It authorizes procedures outside scope.', 'It teaches conceptual recognition and source-based safety reasoning.', 'It proves every historical rule is current.'],
      answer: 2,
      feedback: 'This is conceptual education. Technique requires formal training, supervision, current references and appropriate scope.'
    }
  ];

  const quizForm = document.getElementById('quick-check-form');
  const scoreLabel = document.getElementById('quiz-score');
  const resultBox = document.getElementById('quiz-result');

  if (window.matchMedia('(max-width: 700px)').matches) {
    document.querySelectorAll('.study-nav details').forEach(section => { section.open = false; });
  }

  function loadState() {
    try { return JSON.parse(localStorage.getItem(storageKey)) || {}; } catch (_) { return {}; }
  }

  function saveState(patch) {
    const state = Object.assign(loadState(), patch, { updatedAt: new Date().toISOString() });
    localStorage.setItem(storageKey, JSON.stringify(state));
    return state;
  }

  function formValues(form) {
    const data = new FormData(form);
    const values = {};
    for (const [key, value] of data.entries()) {
      if (Object.prototype.hasOwnProperty.call(values, key)) {
        values[key] = Array.isArray(values[key]) ? values[key].concat(value) : [values[key], value];
      } else values[key] = value;
    }
    return values;
  }

  function restoreForm(form, values) {
    if (!values) return;
    for (const element of form.elements) {
      if (!element.name) continue;
      const saved = values[element.name];
      if (element.type === 'checkbox') element.checked = Array.isArray(saved) ? saved.includes(element.value) : saved === element.value;
      else if (element.type === 'radio') element.checked = saved === element.value;
      else if (typeof saved === 'string') element.value = saved;
    }
  }

  function attachAutosave(form, field, status) {
    restoreForm(form, loadState()[field]);
    form.addEventListener('input', () => {
      saveState({ [field]: formValues(form) });
      if (status) {
        status.textContent = 'Saved automatically in this browser.';
        window.clearTimeout(status._timer);
        status._timer = window.setTimeout(() => { status.textContent = 'Responses save automatically in this browser.'; }, 1800);
      }
    });
  }

  function renderQuiz() {
    const saved = loadState().quizAnswers || {};
    quizForm.replaceChildren();
    questions.forEach((question, index) => {
      const section = document.createElement('section');
      section.className = 'quiz-question';
      section.dataset.index = String(index);
      const fieldset = document.createElement('fieldset');
      const legend = document.createElement('legend');
      legend.textContent = `${index + 1}. ${question.prompt}`;
      fieldset.append(legend);
      const list = document.createElement('div');
      list.className = 'radio-list';
      question.options.forEach((option, optionIndex) => {
        const label = document.createElement('label');
        const input = document.createElement('input');
        input.type = 'radio';
        input.name = `question-${index}`;
        input.value = String(optionIndex);
        input.checked = saved[index] === String(optionIndex);
        const span = document.createElement('span');
        span.textContent = option;
        label.append(input, span);
        list.append(label);
      });
      fieldset.append(list);
      const feedback = document.createElement('p');
      feedback.className = 'feedback';
      feedback.textContent = question.feedback;
      section.append(fieldset, feedback);
      quizForm.append(section);
    });
    updateAnswered();
  }

  function quizAnswers() {
    const answers = {};
    questions.forEach((_, index) => {
      const selected = quizForm.querySelector(`input[name="question-${index}"]:checked`);
      if (selected) answers[index] = selected.value;
    });
    return answers;
  }

  function updateAnswered() {
    scoreLabel.textContent = `${Object.keys(quizAnswers()).length} / ${questions.length} answered`;
  }

  quizForm.addEventListener('change', () => {
    saveState({ quizAnswers: quizAnswers() });
    updateAnswered();
  });

  document.getElementById('grade-quiz').addEventListener('click', () => {
    const answers = quizAnswers();
    let correct = 0;
    questions.forEach((question, index) => {
      const box = quizForm.querySelector(`[data-index="${index}"]`);
      box.classList.remove('correct', 'incorrect');
      if (answers[index] === undefined) return;
      const isCorrect = Number(answers[index]) === question.answer;
      box.classList.add(isCorrect ? 'correct' : 'incorrect');
      if (isCorrect) correct += 1;
    });
    const answered = Object.keys(answers).length;
    saveState({ quizAnswers: answers, quizScore: correct, quizAnswered: answered });
    resultBox.innerHTML = answered < questions.length
      ? `<b>${correct} correct from ${answered} answered.</b> Complete all ten items for a full recommendation.`
      : `<b>${correct} / ${questions.length} correct.</b> ${correct >= 8 ? 'Strong safety reasoning. Continue with the Moodle examinations and verify every point entry.' : correct >= 6 ? 'Developing well. Review the feedback for missed risk categories before the examinations.' : 'Return to SAFER and the seven risk families, then repeat this check without notes.'}`;
    resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });

  document.getElementById('reset-quiz').addEventListener('click', () => {
    quizForm.reset();
    quizForm.querySelectorAll('.quiz-question').forEach(box => box.classList.remove('correct', 'incorrect'));
    resultBox.textContent = '';
    saveState({ quizAnswers: {}, quizScore: null, quizAnswered: 0 });
    updateAnswered();
  });

  document.getElementById('print-worksheet').addEventListener('click', () => window.print());

  const startingForm = document.getElementById('starting-form');
  const finalForm = document.getElementById('final-form');
  attachAutosave(startingForm, 'starting', document.getElementById('starting-status'));
  attachAutosave(finalForm, 'final');

  document.getElementById('generate-evaluation').addEventListener('click', () => {
    const state = saveState({ final: formValues(finalForm) });
    const final = state.final || {};
    const required = ['depth', 'review', 'useful', 'result'];
    const missing = required.filter(name => !final[name]);
    const evaluation = document.getElementById('evaluation');
    evaluation.hidden = false;
    if (missing.length) {
      evaluation.innerHTML = '<h3>Complete the four rating questions</h3><p>Select one response for study depth, additional review, usefulness and expected results. Then generate the evaluation again.</p>';
      evaluation.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      return;
    }
    const score = Number(state.quizScore);
    const answered = Number(state.quizAnswered || 0);
    const depth = Number(final.depth);
    const review = Number(final.review);
    const result = Number(final.result);
    const useful = Number(final.useful);
    const topics = Array.isArray(final['review-topic']) ? final['review-topic'] : final['review-topic'] ? [final['review-topic']] : [];
    let heading = 'Targeted review recommended';
    let summary = 'You have a useful foundation. Consolidate the weakest categories before relying on point-by-point recall.';
    if (answered === 10 && score >= 8 && depth >= 3 && review >= 3 && result >= 3) {
      heading = 'Ready for focused examination practice';
      summary = 'Your reasoning check and reflection indicate good conceptual readiness. Continue with the Moodle banks and verify errors against complete sources.';
    } else if (answered < 10 || score < 6 || depth < 2 || review < 2) {
      heading = 'Rebuild the safety framework first';
      summary = 'Review SAFER and the seven risk families, then repeat the ten-item check before moving to the larger Moodle banks.';
    }
    const recommendations = [];
    if (answered < 10) recommendations.push('Complete all ten safety reasoning questions so the result includes objective evidence.');
    else if (score < 8) recommendations.push('Review every missed item and write the correct risk family in an error log.');
    else recommendations.push('Use the ten Moodle banks for point-level retrieval and record errors by risk family.');
    if (topics.length) recommendations.push(`Prioritize: ${topics.join(', ')}.`);
    else recommendations.push('Choose one regional bank at a time and begin with its repeated anatomical pattern.');
    if (depth < 3) recommendations.push('Create at least five two-sided safety cards and complete one recall round without notes.');
    if (useful < 3) recommendations.push('Use the region-first worksheet rather than rereading the chapter from beginning to end.');
    recommendations.push('Confirm legacy or historical wording with current full point entries and safety guidance.');
    evaluation.innerHTML = `<span class="eyebrow">YOUR STUDY EVALUATION</span><h3>${heading}</h3><p>${summary}</p><p><b>Reasoning check:</b> ${answered === 10 && Number.isFinite(score) ? `${score} / 10` : 'not yet completed'}. <b>Reported study depth:</b> ${depth} / 4. <b>Expected result:</b> ${result} / 4.</p><h4>Recommended next steps</h4><ul>${recommendations.map(item => `<li>${item}</li>`).join('')}</ul>`;
    saveState({ evaluation: { heading, generatedAt: new Date().toISOString() } });
    evaluation.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });

  renderQuiz();
})();
