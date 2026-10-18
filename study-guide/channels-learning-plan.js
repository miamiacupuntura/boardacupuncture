(() => {
  'use strict';

  const chapterTitle = document.querySelector('#sgv2-introduction h2');
  if (chapterTitle) chapterTitle.textContent = 'Chapter: THE CHANNELS AND COLLATERALS';

  const plan = document.getElementById('sgv2-formb');
  const table = plan?.querySelector('table');
  if (!table) return;

  const rows = [
    ['1. Explain the functions of the channels and collaterals.','Communication between the surface, channels and zangfu; circulation of Qi and Blood; nourishment, protection, regulation and transmission of disorders.','System overview, function map, guided comparisons, clinical inference and retrieval practice.'],
    ['2. Describe the organization and circulation of the Twelve Primary Channels.','Channel nomenclature, Yin–Yang pairings, general directions, continuous circulation and external and internal pathways.','Directional diagrams, channel-family tables, pathway comparisons, flashcards and sequence activities.'],
    ['3. Distinguish the Twelve Divergent Channels from the Primary Channels.','Deep pathways, zangfu connections, the six confluences and the distinctive role of the Divergent Channels.','Primary-versus-divergent comparison, confluence diagrams, case-based reasoning and matching.'],
    ['4. Explain the organization and functions of the Eight Extraordinary Vessels.','Du, Ren, Chong, Dai, Yang Qiao, Yin Qiao, Yang Wei and Yin Wei; emphasis on the six vessels developed here and links to the separate Du and Ren chapters.','Vessel map, paired comparison, terminology table, functional summaries and visual identification.'],
    ['5. Describe the Luo-Connecting Channels and distinguish them from Luo-Connecting points.','The Luo vessel network, its pathways and relationships, and the distinction between a connecting channel and the point category bearing the same name.','Channel-versus-point comparison, pathway diagrams, classification checks and clinical examples.'],
    ['6. Describe the organization and functions of the Twelve Sinew Channels.','Superficial musculoskeletal pathways, binding regions, channel groupings and their relationship to pain, tension and movement.','Regional body maps, movement examples, pathway tracing, matching and visual identification.'],
    ['7. Explain the Minute Collaterals and their place in the channel network.','Fine ramifications of the collateral network and their relationship to progressively smaller distributions at the body surface.','Branching-network diagram, scale comparison, concise explanation and concept checks.'],
    ['8. Describe the Twelve Cutaneous Regions.','Surface territories associated with the Primary Channels and their role in observing and understanding external manifestations.','Anterior and posterior body diagrams, region-to-channel matching and guided visual identification.'],
    ['9. Complete an integrated chapter review of the channel and collateral systems.','Cumulative comparison of Primary, Divergent, Extraordinary, Luo-Connecting and Sinew Channels, Minute Collaterals and Cutaneous Regions; this is a review objective, not an additional system.','Comparison matrix, relative-depth map, cumulative reasoning, flashcards and chapter self-test.']
  ];

  table.querySelector('caption')?.replaceChildren('THE CHANNELS AND COLLATERALS — Learning Plan');
  const headRow = table.querySelector('thead tr');
  if (headRow) {
    headRow.replaceChildren();
    ['Learner Objectives','Subject Matter','Teaching Method','Class Hours'].forEach(label => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = label;
      headRow.append(th);
    });
  }

  const body = table.querySelector('tbody');
  body.replaceChildren();
  rows.forEach(([objectiveText, subjectText, methodText], index) => {
    const tr = document.createElement('tr');
    const objective = document.createElement('th');
    objective.scope = 'row';
    objective.dataset.label = 'Learner Objectives';
    const objectiveLink = document.createElement('a');
    objectiveLink.href = `#channels-objective-${index + 1}`;
    objectiveLink.textContent = objectiveText;
    objectiveLink.className = 'learning-plan-objective-link';
    objectiveLink.addEventListener('click', event => {
      const target = document.getElementById(`channels-objective-${index + 1}`);
      if (!target) return;
      event.preventDefault();
      target.open = true;
      window.history.replaceState(null, '', objectiveLink.href);
      target.scrollIntoView({block: 'start'});
      const summary = target.querySelector(':scope > summary');
      if (summary) {
        summary.tabIndex = -1;
        summary.focus({preventScroll: true});
      }
    });
    objective.append(objectiveLink);
    const subject = document.createElement('td');
    subject.dataset.label = 'Subject Matter';
    subject.textContent = subjectText;
    const method = document.createElement('td');
    method.dataset.label = 'Teaching Method';
    method.textContent = methodText;
    const hours = document.createElement('td');
    hours.dataset.label = 'Class Hours';
    hours.textContent = 'To be estimated';
    tr.append(objective, subject, method, hours);
    body.append(tr);
  });

  const note = plan.querySelector('.sgv2-muted');
  if (note) note.textContent = 'The chapter follows the organizational sequence used in Deadman’s “The channels and collaterals.” Objectives will be developed and reviewed one at a time.';

  const firstLegacy = document.getElementById('sgv2-objective-1');
  if (firstLegacy && !document.getElementById('old-objectives-heading')) {
    const legacyAnchor = document.createElement('section');
    legacyAnchor.id = 'old-objectives-heading';
    legacyAnchor.hidden = true;
    firstLegacy.before(legacyAnchor);
  }

  const style = document.createElement('style');
  style.textContent = '.learning-plan-objective-link{color:#075f60;font-weight:750;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:3px}.learning-plan-objective-link:hover{color:#a64f12}.learning-plan-objective-link:focus-visible{outline:3px solid #1769c2;outline-offset:3px;border-radius:3px}';
  document.head.append(style);
})();
