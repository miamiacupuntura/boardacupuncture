(() => {
  'use strict';

  const plan = document.getElementById('sgv2-formb');
  const legacyHeading = document.getElementById('old-objectives-heading');
  const navigation = document.querySelector('main.sgv2-layout > .sgv2-navigation');
  if (!plan || !legacyHeading || !navigation || document.getElementById('channels-objective-1')) return;

  const objectives = [
    ['The Functions of the Channels','Explain how the channel and collateral network connects, transports, nourishes, protects and reflects physiological and pathological change.'],
    ['The Twelve Primary Channels','Describe their nomenclature, pairings, circulation and external and internal pathways.'],
    ['The Twelve Divergent Channels','Distinguish their deeper pathways, zangfu connections and six confluences from the Primary Channels.'],
    ['The Eight Extraordinary Vessels','Explain the organization and functions of Du, Ren, Chong, Dai, Yang Qiao, Yin Qiao, Yang Wei and Yin Wei.'],
    ['The Luo-Connecting Channels','Describe the Luo vessel network and distinguish a Luo-Connecting channel from a Luo-Connecting point.'],
    ['The Twelve Sinew Channels','Describe their superficial musculoskeletal pathways, binding regions and relationship to movement.'],
    ['The Minute Collaterals','Explain the finer ramifications of the collateral network and their place within the system.'],
    ['The Twelve Cutaneous Regions','Describe the surface territories related to the Primary Channels and interpret their visual organization.'],
    ['Integrated Chapter Review: Comparing the Channel Systems','Compare the systems by pathway, relative depth, connections and principal functions without treating the review as an additional channel system.']
  ];

  const make = (tag, text, parent, className) => {
    const node = document.createElement(tag);
    if (text) node.textContent = text;
    if (className) node.className = className;
    if (parent) parent.append(node);
    return node;
  };

  const block = (objective, title, description, className = '') => {
    const section = make('section', '', objective, `sgv2-resource new-objective-block ${className}`.trim());
    make('h4', title, section);
    make('p', description, section, 'sgv2-muted');
    return section;
  };

  objectives.forEach(([title, outcome], index) => {
    const number = index + 1;
    const objective = make('details', '', null, 'sgv2-objective channels-new-objective');
    objective.id = `channels-objective-${number}`;
    objective.dataset.objectiveStatus = 'new';
    make('summary', `Objective ${number} — ${title}`, objective);

    const introduction = block(
      objective,
      'Introduction and Understanding the Objective',
      outcome,
      'objective-introduction'
    );
    const prompts = make('div', '', introduction, 'objective-introduction-prompts');
    make('p', `What will you study? ${title}.`, prompts);
    make('p', `What will you learn to do? ${outcome}`, prompts);
    const board = make('details', '', introduction, 'board-connection-objective');
    make('summary', 'How does this connect to the Board?', board);
    make('p', 'This connection will be completed after the source content and related assessment questions are assigned to this objective.', board);

    block(objective, 'Explore the Topic', 'Source reading, organized explanations, comparison tables and carefully selected clinical connections will be developed here.', 'objective-development');
    block(objective, 'Visual Learning', number === 8 ? 'The Twelve Cutaneous Regions diagram and guided visual-identification activity will be developed here.' : 'Diagrams and visual comparisons for this objective will be developed here.', 'objective-visual');
    block(objective, 'Flashcards — Guided Recall', 'Question-and-answer cards will be added after the core relationships have been reviewed.', 'objective-flashcards');

    const selfTest = block(objective, 'Self-Test — Practice without score', 'Activities will be added only after the instructional content is approved.', 'objective-self-test');
    const order = make('ol', '', selfTest, 'self-test-order');
    ['Multiple Choice','Matching','Fill in the Blank','Visual Identification, when applicable'].forEach(item => make('li', item, order));

    block(objective, 'In-depth Reading — Continuing Education', 'Historical context, classical interpretation, modern anatomical perspective and clinical reasoning will be developed here.', 'objective-continuing-education');
    block(objective, 'Instructor Resource — Mock 10', 'The Word-ready Mock 10 will be prepared after the objective and its distractors have been reviewed.', 'objective-instructor-resource');
    block(objective, 'Exam with score', 'Related AcupunctureTests exams will be placed here after each question set has been assigned to the correct objective.', 'objective-exam');

    legacyHeading.before(objective);
  });

  const planRows = plan.querySelectorAll('tbody tr');
  planRows.forEach((row, index) => {
    const cell = row.querySelector('th');
    if (!cell || index >= objectives.length) return;
    const text = cell.textContent;
    const link = make('a', text);
    link.href = `#channels-objective-${index + 1}`;
    cell.replaceChildren(link);
    link.addEventListener('click', () => {
      document.getElementById(`channels-objective-${index + 1}`).open = true;
    });
  });

  const navList = navigation.querySelector('ul');
  const firstOldLink = navList?.querySelector('a[href="#sgv2-objective-1"]')?.closest('li');
  if (navList && firstOldLink) {
    const newLabel = make('li', 'New Objectives', null, 'channels-nav-label');
    firstOldLink.before(newLabel);
    objectives.forEach(([title], index) => {
      const item = make('li');
      const link = make('a', `${index + 1}. ${title}`, item);
      link.href = `#channels-objective-${index + 1}`;
      link.addEventListener('click', () => {
        document.getElementById(`channels-objective-${index + 1}`).open = true;
      });
      firstOldLink.before(item);
    });
    firstOldLink.before(make('li', 'Old Objectives — Material under review', null, 'channels-nav-label channels-nav-label-old'));
  }

  const style = make('style', '', document.head);
  style.textContent = `
    .channels-nav-label{margin-top:14px;padding:8px 10px;color:#075f60;font-weight:800;border-top:1px solid #cbdedd}
    .channels-nav-label-old{color:#775b39}
    .channels-new-objective{border-top:4px solid #087774}
    .channels-new-objective > summary{font-size:1.08rem}
    .new-objective-block{margin-block:14px}
    .objective-introduction-prompts{border-left:4px solid #ef7e2d;padding:4px 14px;background:#fff8ef;border-radius:8px}
    .self-test-order{margin-block:10px}
  `;
})();
