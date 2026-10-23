(function () {
  'use strict';
  const beforeForm = document.getElementById('study-needs-form');
  const afterForm = document.getElementById('end-study-form');
  if (!beforeForm || !afterForm) return;

  const storageKey = 'channel-pathways-study-reflection-v1';
  const beforeStatus = document.getElementById('study-needs-status');
  const afterStatus = document.getElementById('end-study-status');
  const result = document.getElementById('study-reflection-result');
  const generate = document.getElementById('generate-study-reflection');
  let saveTimer;

  const depthLabels = {1: 'Overview / skimmed', 2: 'Working review', 3: 'Independent practice', 4: 'Teach-back mastery'};
  const usefulnessLabels = {1: 'Not useful yet', 2: 'Somewhat useful', 3: 'Useful', 4: 'Very useful'};
  const reviewLabels = {1: 'Extensive', 2: 'Moderate', 3: 'Targeted', 4: 'Minimal'};
  const expectationLabels = {1: 'Not yet', 2: 'Partly', 3: 'Met', 4: 'Exceeded'};
  const topicActions = {
    'Primary directional groups': 'Draw the four Primary directional groups twice from memory, then check anterior–middle–posterior order.',
    'Primary routes and internal branches': 'Trace two Primary routes from distal origin through internal connections and name two landmarks for each.',
    'Divergent Channels': 'Use S–E–C–R to compare two Yin–Yang Divergent pairs and mark separation, deep entry, convergence and rejoining.',
    'Luo-Connecting Channels': 'Make five O–P–D cards: Luo origin, paired connection and distinctive destination.',
    'Sinew Channels': 'Trace two Sinew routes with D–S–B: distal origin, superficial spread and joint-binding regions.',
    'Extraordinary Vessels': 'Compare the defining geometry and landmark regions of the Extraordinary Vessels included in this chapter.',
    'Anatomical regions': 'Reverse-map two anatomical regions using the atlas, separating every route by channel system.',
    'Internal connections and action words': 'Build a table for enters, connects, traverses, disperses, binds and rejoins with one route example each.',
    'Convergence landmarks': 'Review BL-1, DU-14, LI-15, GB-30, the chest and supraclavicular fossa as shared pathway hubs.',
    'Image identification and look-alikes': 'Complete 10 black-and-white image questions and reject each closest distractor with one contradictory feature.'
  };

  function formData(form) {
    const data = {};
    new FormData(form).forEach((value, key) => {
      if (Object.prototype.hasOwnProperty.call(data, key)) data[key] = [].concat(data[key], value);
      else data[key] = value;
    });
    return data;
  }

  function normalizeArray(value) { return value ? [].concat(value) : []; }

  function collect() { return {before: formData(beforeForm), after: formData(afterForm)}; }

  function apply(form, data) {
    if (!data) return;
    Object.entries(data).forEach(([name, stored]) => {
      const values = normalizeArray(stored).map(String);
      const controls = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
      controls.forEach(control => {
        if (control.type === 'checkbox' || control.type === 'radio') control.checked = values.includes(control.value);
        else control.value = values[0] || '';
      });
    });
  }

  function save() {
    try {
      localStorage.setItem(storageKey, JSON.stringify(collect()));
      beforeStatus.textContent = 'Saved in this browser.';
      afterStatus.textContent = 'Saved in this browser.';
    } catch (error) {
      beforeStatus.textContent = 'Visible now, but this browser could not save the responses.';
      afterStatus.textContent = 'Visible now, but this browser could not save the responses.';
    }
  }

  function scheduleSave(event) {
    window.clearTimeout(saveTimer);
    const status = event.currentTarget === beforeForm ? beforeStatus : afterStatus;
    status.textContent = 'Saving…';
    saveTimer = window.setTimeout(save, 250);
  }

  function restore() {
    let data;
    try { data = JSON.parse(localStorage.getItem(storageKey) || 'null'); } catch (error) { data = null; }
    if (!data) return;
    apply(beforeForm, data.before);
    apply(afterForm, data.after);
  }

  function renderList(id, items, emptyText) {
    const container = document.getElementById(id);
    container.replaceChildren();
    (items.length ? items : [emptyText]).forEach(text => {
      const item = document.createElement('li');
      item.textContent = text;
      container.appendChild(item);
    });
  }

  function number(value) {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : null;
  }

  function confidenceText(before, after) {
    if (before === null && after === null) return 'Not rated';
    if (before === null) return `— → ${after}`;
    if (after === null) return `${before} → —`;
    const change = after - before;
    return `${before} → ${after} (${change > 0 ? '+' : ''}${change})`;
  }

  function mockSummary() {
    return window.CHANNEL_PATHWAY_MOCK_RESULTS ? window.CHANNEL_PATHWAY_MOCK_RESULTS.getSummary() : {rows: [], bankCount: 13, completedCount: 0, inProgressCount: 0, completedAverage: null, weakest: [], strongest: null};
  }

  function mockLevel(summary) {
    if (!summary.completedCount && !summary.inProgressCount) return 'No activity yet';
    if (!summary.completedCount) return 'Practice in progress';
    if (summary.completedAverage < 60) return 'Foundation review recommended';
    if (summary.completedAverage < 75) return 'Developing';
    if (summary.completedAverage < 85) return 'Approaching independent readiness';
    return 'Strong practice performance';
  }

  function renderMockRows(list, summary, emptyText) {
    if (!list) return;
    list.replaceChildren();
    const activeRows = summary.rows.filter(row => row.latestPercent !== null || row.currentChecked > 0);
    if (!activeRows.length) {
      const empty = document.createElement('p');
      empty.textContent = emptyText;
      list.appendChild(empty);
      return;
    }
    activeRows.forEach(row => {
      const item = document.createElement('article');
      item.className = 'mock-result-row';
      const title = document.createElement('b');
      title.textContent = row.title;
      const score = document.createElement('strong');
      score.textContent = row.latestPercent === null ? `${row.currentChecked}/${row.total} answered` : `${row.latestCorrect}/${row.total} · ${row.latestPercent}%`;
      const detail = document.createElement('small');
      detail.textContent = row.latestPercent === null ? 'Finish this exam to include it in the average.' : `${row.attempts} completed ${row.attempts === 1 ? 'attempt' : 'attempts'}${row.attempts > 1 ? ` · best ${row.bestPercent}%` : ''}${row.currentChecked > 0 && row.currentChecked < row.total ? ` · current retake ${row.currentChecked}/${row.total}` : ''}`;
      item.append(title, score, detail);
      list.appendChild(item);
    });
  }

  function renderMockDashboard(summary) {
    const initialStatus = document.getElementById('initial-mock-status');
    if (initialStatus) initialStatus.textContent = summary.completedCount ? `${summary.completedCount} of ${summary.bankCount} practice exams completed · ${summary.completedAverage}% average` : summary.inProgressCount ? `${summary.inProgressCount} practice ${summary.inProgressCount === 1 ? 'exam' : 'exams'} in progress` : 'No saved practice exam results yet.';

    const boardLevel = document.getElementById('board-progress-level');
    if (boardLevel) boardLevel.textContent = mockLevel(summary);
    const boardCompleted = document.getElementById('board-completed-count');
    if (boardCompleted) boardCompleted.textContent = `${summary.completedCount} / ${summary.bankCount}`;
    const boardAverage = document.getElementById('board-average-score');
    if (boardAverage) boardAverage.textContent = summary.completedAverage === null ? '—' : `${summary.completedAverage}%`;
    const boardInProgress = document.getElementById('board-in-progress-count');
    if (boardInProgress) boardInProgress.textContent = String(summary.inProgressCount);
    const boardResults = document.getElementById('board-progress-results');
    if (boardResults) renderMockRows(boardResults, summary, 'No practice exam activity has been recorded yet. Open an exam below and check an answer to begin.');
  }

  function generateReflection() {
    save();
    const data = collect();
    const initialConfidence = number(data.before['initial-confidence']);
    const finalConfidence = number(data.after['final-confidence']);
    const plannedDepth = number(data.before['planned-depth']);
    const actualDepth = number(data.after['actual-depth']);
    const reviewNeed = number(data.after['review-need']);
    const usefulness = number(data.after.usefulness);
    const expectations = number(data.after['expectations-met']);
    const mocks = mockSummary();
    const originalTopics = normalizeArray(data.before['review-topic']);
    const remainingTopics = normalizeArray(data.after['remaining-topic']);
    const expectedResults = normalizeArray(data.before['expected-result']);
    const recommendations = [];

    if (finalConfidence === null) recommendations.push('Rate your current confidence to complete the before–after comparison.');
    if (initialConfidence === null) recommendations.push('Return to the starting survey and record your initial confidence; it was not saved before the course.');
    if (finalConfidence !== null && finalConfidence <= 2) recommendations.push('Use the chapter tables with notes, then repeat a 10-question practice exam without notes before moving to mixed images.');
    if (finalConfidence !== null && initialConfidence !== null && finalConfidence <= initialConfidence) recommendations.push('Your confidence did not increase. Review missed practice-exam questions by error type and verify each correction with two landmarks.');
    if (plannedDepth !== null && actualDepth !== null && actualDepth < plannedDepth) recommendations.push(`You planned ${depthLabels[plannedDepth].toLowerCase()} but reported ${depthLabels[actualDepth].toLowerCase()}. Schedule one focused session to close that gap.`);
    if (reviewNeed !== null && reviewNeed <= 2) recommendations.push('Plan two short review sessions rather than one broad rereading: one for pathway organization and one for image application.');
    remainingTopics.slice(0, 4).forEach(topic => recommendations.push(topicActions[topic] || `Complete targeted retrieval practice for ${topic}.`));
    if (!remainingTopics.length && finalConfidence !== null && finalConfidence >= 3 && (mocks.completedAverage === null || mocks.completedAverage >= 75)) recommendations.push('Maintain readiness with one timed mixed exam and explain every incorrect distractor before the final examination.');
    if (expectations !== null && expectations <= 2) recommendations.push('Compare your original expected results with the activities completed and choose one measurable result to finish next.');
    if (usefulness !== null && usefulness <= 2) recommendations.push('Use the “What helped you most?” response to repeat the useful format and identify what course resource or explanation was missing.');
    if (!mocks.completedCount) recommendations.push('Complete at least one full practice exam so the final evaluation can compare your self-assessment with objective practice results.');
    else if (mocks.completedAverage < 60) recommendations.push('Your completed-exam average indicates that the foundations need review. Correct every missed item by system, route and landmark before another mixed exam.');
    else if (mocks.completedAverage < 75) recommendations.push('Your completed-exam average is developing. Review the two lowest-scoring objectives, then repeat their exams without notes.');
    else if (mocks.completedAverage < 85) recommendations.push('You are approaching independent readiness. Use your lowest-scoring objective for one targeted review session before another mixed exam.');
    else recommendations.push('Your completed-exam average shows strong practice performance. Maintain it with spaced image retrieval and explanation of distractors.');
    mocks.weakest.filter(row => row.latestPercent < 80).slice(0, 2).forEach(row => recommendations.push(`${row.title}: review this bank first (${row.latestPercent}% on the latest completed attempt).`));

    const requiredAfter = [finalConfidence, actualDepth, reviewNeed, usefulness, expectations].filter(value => value !== null).length;
    let level = 'Complete the reflection';
    if (requiredAfter === 5) {
      if (finalConfidence >= 3 && reviewNeed >= 3 && expectations >= 3 && (mocks.completedAverage === null || mocks.completedAverage >= 75)) level = 'Ready for targeted practice';
      else if (finalConfidence >= 2 && expectations >= 2) level = 'Continue focused review';
      else level = 'Rebuild key foundations';
    }

    document.getElementById('reflection-level').textContent = level;
    document.getElementById('reflection-confidence').textContent = confidenceText(initialConfidence, finalConfidence);
    document.getElementById('reflection-mock-score').textContent = mocks.completedAverage === null ? 'No completed practice exams' : `${mocks.completedAverage}% · ${mocks.completedCount}/${mocks.bankCount}`;
    document.getElementById('reflection-depth').textContent = actualDepth ? `${actualDepth}/4 · ${depthLabels[actualDepth]}` : 'Not rated';
    document.getElementById('reflection-usefulness').textContent = usefulness ? `${usefulness}/4 · ${usefulnessLabels[usefulness]}` : 'Not rated';
    document.getElementById('reflection-review').textContent = reviewNeed ? `${reviewNeed}/4 · ${reviewLabels[reviewNeed]}` : 'Not rated';

    const summaryParts = [];
    if (expectations) summaryParts.push(`Your expected results were ${expectationLabels[expectations].toLowerCase()}.`);
    if (plannedDepth && actualDepth) summaryParts.push(`You planned ${depthLabels[plannedDepth].toLowerCase()} and completed ${depthLabels[actualDepth].toLowerCase()}.`);
    if (data.before['planned-time']) summaryParts.push(`Your initial time estimate was ${data.before['planned-time']}.`);
    if (mocks.completedAverage !== null) summaryParts.push(`Your ${mocks.completedCount} completed practice ${mocks.completedCount === 1 ? 'exam has' : 'exams have'} an average of ${mocks.completedAverage}%.`);
    document.getElementById('reflection-summary').textContent = summaryParts.join(' ') || 'Complete the final ratings to receive a meaningful comparison and recommendation.';

    const originalItems = originalTopics.map(topic => topic);
    expectedResults.forEach(outcome => originalItems.push(`Expected result: ${outcome}.`));
    if (data.before['starting-goal']) originalItems.push(`Goal: ${data.before['starting-goal']}`);
    renderList('reflection-original-topics', originalItems, 'No starting priorities were recorded.');
    renderList('reflection-remaining-topics', remainingTopics, 'No remaining topic was selected. Maintain learning with spaced mixed practice.');
    renderList('reflection-recommendations', recommendations.slice(0, 7), 'Complete the final ratings to receive recommendations.');

    const personalPlan = [];
    if (data.after['most-helpful']) personalPlan.push(`Most helpful: ${data.after['most-helpful']}`);
    if (data.after['next-step']) personalPlan.push(`Next step: ${data.after['next-step']}`);
    renderList('reflection-personal-plan', personalPlan, 'Write what helped most and one next step to create a personal plan.');

    result.hidden = false;
    result.scrollIntoView({behavior: 'smooth', block: 'start'});
  }

  beforeForm.addEventListener('change', scheduleSave);
  beforeForm.addEventListener('input', scheduleSave);
  afterForm.addEventListener('change', scheduleSave);
  afterForm.addEventListener('input', scheduleSave);
  generate.addEventListener('click', generateReflection);
  window.addEventListener('mockprogresschange', event => renderMockDashboard(event.detail));
  restore();
  renderMockDashboard(mockSummary());
}());
