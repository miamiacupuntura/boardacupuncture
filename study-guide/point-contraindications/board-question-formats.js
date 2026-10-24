(() => {
  'use strict';

  const quickCheck = document.getElementById('quick-check');
  if (!quickCheck || document.getElementById('board-question-formats')) return;

  const questions = [
    {
      id: 'complication',
      format: 'FORMAT 1 · CLINICAL COMPLICATION VIGNETTE',
      title: 'Connect post-treatment symptoms to regional anatomy',
      tests: 'Tests: emergency recognition, anatomical mechanism and prioritization.',
      prompt: 'After treatment in a thoracic and supraclavicular region, a patient develops sharp chest pain, a dry cough and progressively worsening shortness of breath. Which interpretation and next step are strongest?',
      options: [
        'Expected local soreness; reassure the patient and continue routine observation.',
        'Possible pneumothorax related to pleural-region risk; stop the activity and obtain urgent medical evaluation under the emergency protocol.',
        'A normal deep treatment sensation with no anatomical significance.',
        'Anxiety is the most likely explanation, so anatomical risk can be excluded.'
      ],
      answer: 1,
      feedback: 'The symptom cluster and thoracic or supraclavicular region should trigger possible pneumothorax recognition. Symptoms may be delayed. The exam skill is identifying pleural risk and urgent escalation—not reconstructing an insertion technique.'
    },
    {
      id: 'screening',
      format: 'FORMAT 2 · SAFETY SCREENING',
      title: 'Resolve the patient-context question before choosing a point',
      tests: 'Tests: screening, pregnancy context, source verification and scope.',
      prompt: 'Before treatment, a patient reports a late menstrual period and says pregnancy is possible. The planned approach includes lower-abdominal work and points frequently cited in pregnancy caution lists. What is the best next step?',
      options: [
        'Proceed because pregnancy cautions apply only after the first trimester.',
        'Use the memorized H-S-J-Z list as a complete obstetric protocol.',
        'Pause and clarify pregnancy status and urgent symptoms, then apply current pregnancy-specific guidance, informed consent and referral rules before proceeding.',
        'Proceed if the patient signs a general consent form.'
      ],
      answer: 2,
      feedback: 'Pregnancy is a patient-context decision requiring an updated screen and current guidance. Classical point lists are useful retrieval triggers, but they do not replace individual assessment, obstetric red flags, jurisdictional scope or the complete source.'
    },
    {
      id: 'adverse-event',
      format: 'FORMAT 3 · ADVERSE-EVENT MANAGEMENT',
      title: 'Recognize a high-risk event and select the response category',
      tests: 'Tests: recognition, stopping criteria, escalation and documentation.',
      prompt: 'During or after work near BL 1, rapid orbital swelling, severe pain, protrusion of the eye or a visual change develops. Which response category is most appropriate?',
      options: [
        'Continue the session because swelling is an expected treatment response.',
        'Apply heat and vigorous massage before reassessing.',
        'Stop the activity, protect the patient and activate urgent ophthalmic or emergency assessment according to the current adverse-event protocol.',
        'Repeat stimulation to determine whether the symptom is reproducible.'
      ],
      answer: 2,
      feedback: 'Rapid swelling, severe pain, proptosis or visual change after orbital-region treatment can threaten ocular or optic structures. The appropriate exam-level response is immediate recognition, cessation and urgent escalation under the current protocol.'
    }
  ];

  const section = document.createElement('section');
  section.className = 'card board-format-lab';
  section.id = 'board-question-formats';
  section.innerHTML = `
    <header class="section-heading"><div><span class="eyebrow">BOARD PREPARATION · QUESTION-FORMAT PRACTICE</span><h2>Three ways safety reasoning may be tested</h2><p>Read the clinical cue, classify the risk and choose the safest evidence-based response. Check each answer for immediate feedback.</p></div><span class="score-pill" id="board-format-progress">0 / ${questions.length} checked</span></header>
    <aside class="format-source-boundary"><b>Practice questions created for this guide</b><span>These are not official, recalled or reproduced NCCAOM questions, and they do not establish the current examination blueprint. They model common clinical-reasoning formats using the sources and safety boundaries of this chapter.</span></aside>
    <div class="format-overview">
      <article><span>1</span><div><b>Complication vignette</b><p>Match symptoms and timing to the vulnerable anatomy.</p></div></article>
      <article><span>2</span><div><b>Safety screening</b><p>Identify the patient factor that must be resolved first.</p></div></article>
      <article><span>3</span><div><b>Adverse-event response</b><p>Recognize stopping and escalation criteria.</p></div></article>
    </div>
    <div class="board-format-list">
      ${questions.map((question, index) => `
        <details class="board-format-question" data-question="${question.id}" ${index === 0 ? 'open' : ''}>
          <summary><span>${question.format}</span><strong>${question.title}</strong><small>${question.tests}</small></summary>
          <div class="board-format-body">
            <fieldset><legend>${question.prompt}</legend><div class="board-format-options">${question.options.map((option, optionIndex) => `<label><input type="radio" name="board-format-${question.id}" value="${optionIndex}"><span><b>${String.fromCharCode(65 + optionIndex)}.</b> ${option}</span></label>`).join('')}</div></fieldset>
            <div class="board-format-actions"><button type="button" data-check-answer>Check answer</button><p class="board-format-feedback" role="status" aria-live="polite"></p></div>
          </div>
        </details>`).join('')}
    </div>
    <p class="format-technique-boundary">This sampler evaluates recognition and decision-making. Exact needling position, direction and depth require complete current references and supervised clinical training.</p>`;

  quickCheck.before(section);

  const practiceNav = document.querySelector('.study-nav details:last-of-type');
  const quickCheckLink = practiceNav?.querySelector('a[href="#quick-check"]');
  if (practiceNav && quickCheckLink && !practiceNav.querySelector('a[href="#board-question-formats"]')) {
    const link = document.createElement('a');
    link.href = '#board-question-formats';
    link.textContent = 'Board question formats';
    practiceNav.insertBefore(link, quickCheckLink);
  }

  const storageKey = 'acupuncturetests-point-contraindications-board-formats-v1';
  const readState = () => {
    try { return JSON.parse(localStorage.getItem(storageKey)) || {}; } catch (_) { return {}; }
  };
  const writeState = state => localStorage.setItem(storageKey, JSON.stringify(state));

  function updateProgress() {
    const state = readState();
    const checked = questions.filter(question => state[question.id]?.checked).length;
    const correct = questions.filter(question => state[question.id]?.checked && state[question.id]?.selected === question.answer).length;
    document.getElementById('board-format-progress').textContent = checked ? `${correct} correct · ${checked} / ${questions.length} checked` : `0 / ${questions.length} checked`;
  }

  function showResult(element, question, selected) {
    const feedback = element.querySelector('.board-format-feedback');
    const isCorrect = selected === question.answer;
    element.classList.toggle('correct', isCorrect);
    element.classList.toggle('incorrect', !isCorrect);
    feedback.innerHTML = `<b>${isCorrect ? 'Correct.' : `Review this format. The strongest answer is ${String.fromCharCode(65 + question.answer)}.`}</b> ${question.feedback}`;
  }

  questions.forEach(question => {
    const element = section.querySelector(`[data-question="${question.id}"]`);
    const saved = readState()[question.id];
    if (Number.isInteger(saved?.selected)) {
      const input = element.querySelector(`input[value="${saved.selected}"]`);
      if (input) input.checked = true;
      if (saved.checked) showResult(element, question, saved.selected);
    }
    element.querySelectorAll('input[type="radio"]').forEach(input => input.addEventListener('change', () => {
      const state = readState();
      state[question.id] = { selected: Number(input.value), checked: false, updatedAt: new Date().toISOString() };
      writeState(state);
      element.classList.remove('correct', 'incorrect');
      element.querySelector('.board-format-feedback').textContent = '';
      updateProgress();
    }));
    element.querySelector('[data-check-answer]').addEventListener('click', () => {
      const selectedInput = element.querySelector('input[type="radio"]:checked');
      const feedback = element.querySelector('.board-format-feedback');
      if (!selectedInput) {
        feedback.textContent = 'Select one answer before checking.';
        return;
      }
      const selected = Number(selectedInput.value);
      const state = readState();
      state[question.id] = { selected, checked: true, updatedAt: new Date().toISOString() };
      writeState(state);
      showResult(element, question, selected);
      updateProgress();
    });
  });

  updateProgress();
})();
