(function () {
  'use strict';
  const form = document.getElementById('self-evaluation-form');
  if (!form) return;
  const storageKey = 'channel-pathways-self-evaluation-v1';
  const score = document.getElementById('self-score');
  const ratedCount = document.getElementById('self-rated-count');
  const progress = document.getElementById('self-progress-bar');
  const readyCount = document.getElementById('board-ready-count');
  const saveStatus = document.getElementById('self-save-status');
  const clear = document.getElementById('clear-self-evaluation');
  const generate = document.getElementById('generate-evaluation');
  const evaluation = document.getElementById('personalized-evaluation');
  const evaluationLevel = document.getElementById('evaluation-level');
  const evaluationScore = document.getElementById('evaluation-score');
  const evaluationAverage = document.getElementById('evaluation-average');
  const evaluationReadiness = document.getElementById('evaluation-readiness');
  const evaluationSummary = document.getElementById('evaluation-summary');
  const evaluationPriorities = document.getElementById('evaluation-priorities');
  const evaluationRecommendations = document.getElementById('evaluation-recommendations');
  const evaluationActions = document.getElementById('evaluation-actions');
  const evaluationPending = document.getElementById('evaluation-pending');
  const competencyGuidance = {
    'primary-groups': {label: 'Primary directional groups', advice: 'Draw the four directional groups from memory, then verify anterior–middle–posterior order and the Chest → Hand → Head → Foot cycle.'},
    systems: {label: 'Channel-system organization', advice: 'Use S–E–C–R, O–P–D and D–S–B to classify Divergent, Luo and Sinew routes before naming the organ family.'},
    route: {label: 'Color-independent route recognition', advice: 'Practice black-and-white images and identify direction, surface, depth and two landmarks before looking at the options.'},
    landmarks: {label: 'Two-landmark justification', advice: 'Create flashcards that require two independent landmarks and one sentence explaining why the closest alternative fails.'},
    family: {label: 'Same-family comparison', advice: 'Complete a four-column Primary–Divergent–Luo–Sinew matrix for the two organ families you confuse most.'},
    region: {label: 'Anatomical region reverse recall', advice: 'Choose two regions from the atlas and work backward: list every pathway reaching each region, then separate them by system and direction.'},
    verbs: {label: 'Internal action words', advice: 'Build a verb table for enters, connects, traverses, disperses, binds and rejoins; attach one verified pathway example to each verb.'},
    convergence: {label: 'Convergence landmarks', advice: 'Review BL-1, DU-14, LI-15, GB-30 and the supraclavicular fossa as hubs, identifying incoming and outgoing directions.'},
    alternative: {label: 'Closest-alternative rejection', advice: 'For every missed question, write one precise exclusion: wrong depth, wrong surface, wrong destination or wrong system behavior.'},
    errors: {label: 'Practice-exam error correction', advice: 'Sort missed questions by error type and correct the underlying rule before repeating the same practice exam.'}
  };
  let saveTimer;

  function collect() {
    const ratings = {}, actions = {}, checks = {};
    form.querySelectorAll('.competency-row').forEach(row => {
      const key = row.dataset.competency;
      const selected = row.querySelector('input[type="radio"]:checked');
      ratings[key] = selected ? Number(selected.value) : null;
      actions[key] = row.querySelector('.next-action').value;
    });
    form.querySelectorAll('.final-board-checklist input[type="checkbox"]').forEach(input => { checks[input.name] = input.checked; });
    return {ratings, actions, checks};
  }

  function updateSummary(data) {
    const values = Object.values(data.ratings).filter(value => Number.isFinite(value));
    const total = values.reduce((sum, value) => sum + value, 0);
    const complete = Object.values(data.checks).filter(Boolean).length;
    score.textContent = `${total} / 40`;
    ratedCount.textContent = `${values.length} of 10 rated`;
    readyCount.textContent = `${complete} / 10 complete`;
    progress.style.width = `${values.length * 10}%`;
  }

  function save() {
    const data = collect();
    updateSummary(data);
    try { localStorage.setItem(storageKey, JSON.stringify(data)); saveStatus.textContent = 'Saved in this browser.'; }
    catch (error) { saveStatus.textContent = 'Your selections are visible now, but this browser could not save them.'; }
    if (!evaluation.hidden) generateEvaluation(false);
  }

  function scheduleSave() { window.clearTimeout(saveTimer); saveStatus.textContent = 'Saving…'; saveTimer = window.setTimeout(save, 250); }

  function restore() {
    let data;
    try { data = JSON.parse(localStorage.getItem(storageKey) || 'null'); } catch (error) { data = null; }
    if (!data) { updateSummary(collect()); return; }
    Object.entries(data.ratings || {}).forEach(([key, value]) => { const input = form.querySelector(`input[type="radio"][name="${key}"][value="${value}"]`); if (input) input.checked = true; });
    Object.entries(data.actions || {}).forEach(([key, value]) => { const input = form.querySelector(`[data-competency="${key}"] .next-action`); if (input) input.value = value; });
    Object.entries(data.checks || {}).forEach(([key, value]) => { const input = form.elements.namedItem(key); if (input) input.checked = Boolean(value); });
    updateSummary(collect());
  }

  function renderList(container, items, emptyMessage) {
    container.replaceChildren();
    (items.length ? items : [emptyMessage]).forEach(item => {
      const li = document.createElement('li');
      li.textContent = item;
      container.appendChild(li);
    });
  }

  function evaluationBand(average, rated) {
    if (rated < 10) return {name: 'Incomplete assessment', summary: `You have rated ${rated} of 10 competencies. Complete every rating for a balanced recommendation.`};
    if (average < 1.75) return {name: 'Foundation rebuilding', summary: 'Your responses indicate that the channel-system foundations need structured review before timed Board practice.'};
    if (average < 2.5) return {name: 'Developing with support', summary: 'You recognize several concepts, but still depend on notes. Focused comparison and retrieval practice should come before full mixed practice exams.'};
    if (average < 3.25) return {name: 'Independent practice', summary: 'You can work independently in many areas. Target the lowest-rated systems and strengthen written justification under time limits.'};
    if (average < 3.75) return {name: 'Board-ready with targeted review', summary: 'Your self-ratings show strong working readiness. Use mixed images, error analysis and the remaining checklist items to close specific gaps.'};
    return {name: 'Strong mastery', summary: 'Your responses show strong command of the material. Maintain accuracy with timed mixed practice and teach-back explanations rather than broad rereading.'};
  }

  function generateEvaluation(shouldScroll) {
    const data = collect();
    const ratedEntries = Object.entries(data.ratings).filter(([, value]) => Number.isFinite(value));
    const total = ratedEntries.reduce((sum, [, value]) => sum + value, 0);
    const average = ratedEntries.length ? total / ratedEntries.length : 0;
    const complete = Object.values(data.checks).filter(Boolean).length;
    const band = evaluationBand(average, ratedEntries.length);
    const ordered = ratedEntries.slice().sort((a, b) => a[1] - b[1]);
    const priorityEntries = ordered.filter(([, value]) => value <= 2);
    const selectedPriorities = (priorityEntries.length ? priorityEntries : ordered.slice(0, 2)).slice(0, 4);
    const priorities = selectedPriorities.map(([key, value]) => `${competencyGuidance[key].label}: rating ${value}/4.`);
    const recommendations = selectedPriorities.slice(0, 3).map(([key]) => competencyGuidance[key].advice);

    if (ratedEntries.length < 10) recommendations.unshift(`Rate the remaining ${10 - ratedEntries.length} competencies before deciding that you are ready for final Board review.`);
    if (complete < 6) recommendations.push('Complete the unfinished readiness activities before beginning a full timed mixed examination.');
    else if (complete < 9) recommendations.push('Finish the remaining readiness items, then complete one timed mixed practice exam and review every distractor.');
    else recommendations.push('Use a final timed mixed practice exam, followed by a short error-log review instead of another broad rereading session.');

    const writtenActions = Object.entries(data.actions).filter(([, value]) => value.trim()).map(([key, value]) => `${competencyGuidance[key].label}: ${value.trim()}`);
    const pending = Object.entries(data.checks).filter(([, value]) => !value).map(([key]) => {
      const input = form.elements.namedItem(key);
      return input && input.closest('label') ? input.closest('label').textContent.trim() : key;
    });

    evaluation.hidden = false;
    evaluation.dataset.level = average >= 3.25 && ratedEntries.length === 10 ? 'strong' : average >= 2.5 ? 'developing' : 'foundation';
    evaluationLevel.textContent = band.name;
    evaluationScore.textContent = `${total} / 40`;
    evaluationAverage.textContent = average.toFixed(1);
    evaluationReadiness.textContent = `${complete} / 10`;
    evaluationSummary.textContent = `${band.summary} You completed ${complete} of 10 final readiness actions.`;
    renderList(evaluationPriorities, priorities, 'Complete at least one competency rating to identify a priority.');
    renderList(evaluationRecommendations, recommendations, 'Complete the ratings to receive study recommendations.');
    renderList(evaluationActions, writtenActions, 'No personal next actions have been entered yet.');
    renderList(evaluationPending, pending, 'All final Board-readiness actions are marked complete.');
    if (shouldScroll) evaluation.scrollIntoView({behavior: 'smooth', block: 'start'});
  }

  form.addEventListener('change', scheduleSave);
  form.addEventListener('input', scheduleSave);
  generate.addEventListener('click', () => generateEvaluation(true));
  clear.addEventListener('click', () => {
    if (!window.confirm('Clear all self-evaluation ratings, next actions and checklist responses?')) return;
    form.reset();
    try { localStorage.removeItem(storageKey); } catch (error) { /* Storage may be unavailable. */ }
    updateSummary(collect());
    saveStatus.textContent = 'Responses cleared.';
    evaluation.hidden = true;
  });
  restore();
}());
