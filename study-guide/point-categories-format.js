(() => {
  'use strict';

  const SCORM_URL = 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9355';
  const labels = {
    en: {
      back: '← Back to Study Guide index',
      school: 'School Level Exam',
      board: 'Board Level Exam',
      continuing: 'Continuing Education',
      continuingExam: 'Continuing Education Exam',
      boardPlan: 'NCBAHM Exam Learning Plan — Point Categories',
      schoolTitle: 'School Level Exam — Point Categories',
      boardTitle: 'Board Level Exam — Point Categories',
      cePlan: 'Continuing Education Learning Plan — Point Categories',
      ceReview: 'How the Point Categories Chapter Aligns with Continuing Education',
      ceExamTitle: 'Continuing Education Exam — Point Categories',
      ceIntro: 'After reviewing the 10 questions and feedback above, open the SCORM activity here. Personal attempts, dates and grades are available in Moodle. The exam opens in another tab.',
      ceCaption: 'Point Categories — Continuing Education',
      ceName: 'Point Categories Continuing Education (Mock 10)',
      attempts: 'Attempts',
      lastAttempt: 'Last Attempt',
      average: 'Average',
      grade: 'Grade',
      action: 'Action',
      exam: 'Exam',
      start: 'Start Exam',
      summaryImage: 'Chapter Summary Image — Visual Review Gallery',
      navTitle: 'Study Navigation',
      navStart: 'Start here',
      navObjectives: 'Objectives',
      navResources: 'Chapter resources',
      navPractice: 'Practice & exams',
      navBoard: 'Board preparation',
      navContinuing: 'Continuing education',
      introduction: 'Introduction',
      overview: 'Course Overview',
      reading: 'Chapter Reading',
      visual: 'Visual Review Gallery',
      historical: 'Historical Gallery',
      selfTest: 'Self-Test',
      lectureReview: 'Exam Chapter Review (Lecture)',
      reviewObjectives: 'Review Objectives',
      notes: 'My Student Notes',
      ceReviewNav: 'Continuing Education Review'
    },
    es: {
      back: '← Volver al índice de la guía de estudio',
      school: 'Examen de nivel escolar',
      board: 'Examen de nivel Board',
      continuing: 'Educación continua',
      continuingExam: 'Examen de educación continua',
      boardPlan: 'Plan de aprendizaje para el examen NCBAHM — Categorías de puntos',
      schoolTitle: 'Examen de nivel escolar — Categorías de puntos',
      boardTitle: 'Examen de nivel Board — Categorías de puntos',
      cePlan: 'Plan de aprendizaje de educación continua — Categorías de puntos',
      ceReview: 'Cómo el capítulo Categorías de puntos se alinea con la educación continua',
      ceExamTitle: 'Examen de educación continua — Categorías de puntos',
      ceIntro: 'Después de repasar las 10 preguntas y su retroalimentación, abre aquí la actividad SCORM. Los intentos, las fechas y las calificaciones personales están disponibles en Moodle. El examen se abre en otra pestaña.',
      ceCaption: 'Categorías de puntos — Educación continua',
      ceName: 'Educación continua de Categorías de puntos (Mock 10)',
      attempts: 'Intentos',
      lastAttempt: 'Último intento',
      average: 'Promedio',
      grade: 'Calificación',
      action: 'Acción',
      exam: 'Examen',
      start: 'Comenzar examen',
      summaryImage: 'Imagen resumen del capítulo — Galería de repaso visual',
      navTitle: 'Navegación de estudio',
      navStart: 'Comienza aquí',
      navObjectives: 'Objetivos',
      navResources: 'Recursos del capítulo',
      navPractice: 'Práctica y repaso',
      navBoard: 'Preparación para el Board',
      navContinuing: 'Educación continua',
      introduction: 'Introducción',
      overview: 'Descripción del curso',
      reading: 'Lectura del capítulo',
      visual: 'Galería de repaso visual',
      historical: 'Galería histórica',
      selfTest: 'Autoevaluación',
      lectureReview: 'Repaso del capítulo (Lecture)',
      reviewObjectives: 'Repasar objetivos',
      notes: 'Mis notas de estudiante',
      ceReviewNav: 'Repaso de educación continua'
    }
  };

  const language = () => document.documentElement.lang.toLowerCase().startsWith('es') ? 'es' : 'en';
  const text = () => labels[language()];
  function backDestination() {
    const params = new URLSearchParams(window.location.search);
    const track = window.ACUPUNCTURE_ENTRY_TRACK || params.get('track') || 'board';
    const spanish = language() === 'es';
    const returnRoute = params.get('return') || window.ACUPUNCTURE_RETURN_ROUTE || '';
    if (track === 'continuing-education') return {
      href: returnRoute === 'ce-preview' ? 'continuing-education.html?preview=approved' : 'continuing-education.html',
      label: spanish ? '← Volver a los cursos de Continuing Education' : '← Back to Continuing Education courses'
    };
    if (returnRoute === 'board-catalog') return {
      href: 'board-courses.html',
      label: spanish ? '← Volver a los cursos de preparación para el Board' : '← Back to Board preparation courses'
    };
    return {href: '/blocks/myprogress/study-guide.php?area=pointlocation', label: text().back};
  }

  function openTarget(event) {
    const href = event.currentTarget.dataset.pointTarget || event.currentTarget.getAttribute('href');
    const target = href?.startsWith('#') ? document.querySelector(href) : null;
    if (!target) return;
    event.preventDefault();
    if (target.tagName === 'DETAILS') target.open = true;
    for (let parent = target.parentElement; parent; parent = parent.parentElement) {
      if (parent.tagName === 'DETAILS') parent.open = true;
    }
    history.replaceState(null, '', href);
    target.scrollIntoView({behavior: 'smooth', block: 'start'});
    const heading = target.querySelector(':scope > summary, :scope > h2, :scope > h3') || target;
    heading.tabIndex = -1;
    heading.focus({preventScroll: true});
  }

  function makeHeroButton(className, href, labelKey) {
    const link = document.createElement('a');
    link.className = `point-categories-hero-button ${className}`;
    link.href = href;
    link.dataset.pointHeroLabel = labelKey;
    link.addEventListener('click', openTarget);
    return link;
  }

  function ensureHeroButtons() {
    const actions = document.querySelector('#pc-introduction .point-categories-hero-actions');
    if (!actions) return;
    const backRoute = backDestination();
    const back = actions.querySelector('.point-categories-hero-back') || makeHeroButton('point-categories-hero-back', backRoute.href, 'back');
    back.href = backRoute.href;
    back.textContent = backRoute.label;
    delete back.dataset.pointHeroLabel;
    back.removeEventListener('click', openTarget);
    const school = makeHeroButton('point-categories-hero-exam', '#pc-exam-history', 'school');
    const board = makeHeroButton('channels-hero-board-exam', '#point-categories-mock-exams', 'board');
    const continuing = makeHeroButton('channels-hero-continuing', '#pc-learning-plan', 'continuing');
    const continuingExam = makeHeroButton('channels-hero-continuing-exam', '#pc-continuing-education-exam', 'continuingExam');
    actions.replaceChildren(back, school, board, continuing, continuingExam);
  }

  function makeCell(label, className = '') {
    const cell = document.createElement('td');
    cell.dataset.label = label;
    if (className) cell.className = className;
    cell.textContent = '—';
    return cell;
  }

  function ensureContinuingExam() {
    let block = document.getElementById('pc-continuing-education-exam');
    if (block) return block;
    block = document.createElement('details');
    block.id = 'pc-continuing-education-exam';
    block.className = 'sgv2-card channels-mock-exams channels-continuing-exam study-guide-exam-table-shell';
    const summary = document.createElement('summary');
    summary.dataset.pointLabel = 'ceExamTitle';
    const intro = document.createElement('p');
    intro.dataset.pointLabel = 'ceIntro';
    const table = document.createElement('table');
    table.className = 'channels-mock-table channels-continuing-exam-table study-guide-exam-table';
    const caption = document.createElement('caption');
    caption.dataset.pointLabel = 'ceCaption';
    const head = document.createElement('thead');
    const headRow = document.createElement('tr');
    ['exam', 'attempts', 'lastAttempt', 'average', 'grade', 'action'].forEach(key => {
      const heading = document.createElement('th');
      heading.scope = 'col';
      heading.dataset.pointLabel = key;
      headRow.append(heading);
    });
    head.append(headRow);
    const body = document.createElement('tbody');
    const row = document.createElement('tr');
    row.className = 'study-guide-exam-row';
    const name = document.createElement('th');
    name.scope = 'row';
    name.dataset.pointLabel = 'ceName';
    const attempts = makeCell('Attempts');
    const lastAttempt = makeCell('Last Attempt');
    const average = makeCell('Average');
    const grade = makeCell('Grade');
    const action = document.createElement('td');
    action.dataset.label = 'Action';
    action.className = 'study-guide-exam-action-cell';
    const link = document.createElement('a');
    link.href = SCORM_URL;
    link.target = '_blank';
    link.rel = 'noopener noreferrer';
    link.className = 'channels-mock-start study-guide-exam-action';
    link.dataset.pointLabel = 'start';
    action.append(link);
    row.append(name, attempts, lastAttempt, average, grade, action);
    body.append(row);
    table.append(caption, head, body);
    block.append(summary, intro, table);
    document.querySelector('#point-categories-draft .sgv2-content')?.append(block);
    return block;
  }

  function renameBlocks() {
    const map = {
      'pc-board-alignment': 'boardPlan',
      'pc-exam-history': 'schoolTitle',
      'point-categories-mock-exams': 'boardTitle',
      'pc-learning-plan': 'cePlan',
      'pc-continuing-reading': 'ceReview',
      'pc-image': 'summaryImage',
      'pc-continuing-education-exam': 'ceExamTitle'
    };
    Object.entries(map).forEach(([id, key]) => {
      const summary = document.querySelector(`#${id} > summary`);
      if (summary) {
        summary.dataset.pointLabel = key;
        summary.textContent = text()[key];
      }
    });
  }

  function orderBlocks() {
    const content = document.querySelector('#point-categories-draft .sgv2-content');
    if (!content) return;
    const afterObjectives = [
      'pc-image',
      'pc-history-gallery',
      'pc-lecture',
      'pc-simulation',
      'pc-objective-return',
      'pc-notes',
      'pc-board-alignment',
      'pc-exam-history',
      'point-categories-mock-exams',
      'pc-learning-plan',
      'pc-continuing-reading',
      'pc-continuing-education-exam'
    ];
    afterObjectives.forEach(id => {
      const node = document.getElementById(id);
      if (node) content.append(node);
    });
    const reading = document.getElementById('pc-reading');
    const overview = document.getElementById('pc-overview');
    const firstObjective = content.querySelector('details[id^="pc-objective-"]');
    if (reading && firstObjective) firstObjective.before(reading);
    if (overview && reading) reading.before(overview);
  }

  function rebuildNavigation() {
    const nav = document.getElementById('point-categories-study-navigation');
    if (!nav) return;
    const copy = text();
    const section = (title, items, className = '') => {
      const wrapper = document.createElement('section');
      wrapper.className = `flat-navigation-section ${className}`.trim();
      const heading = document.createElement('h4');
      heading.textContent = title;
      const list = document.createElement('ul');
      items.filter(Boolean).forEach(([href, label]) => {
        if (!document.querySelector(href)) return;
        const item = document.createElement('li');
        const link = document.createElement('a');
        link.href = href === '#pc-continuing-reading' ? `./index.html${href}` : href;
        link.dataset.pointTarget = href;
        link.textContent = label;
        link.title = label;
        link.addEventListener('click', openTarget);
        item.append(link);
        list.append(item);
      });
      if (!list.children.length) return;
      wrapper.append(heading, list);
      nav.append(wrapper);
    };
    nav.replaceChildren();
    const title = document.createElement('h3');
    title.textContent = copy.navTitle;
    nav.append(title);
    section(copy.navStart, [
      ['#pc-introduction', copy.introduction],
      ['#pc-overview', copy.overview],
      ['#pc-reading', copy.reading]
    ]);
    const objectives = [...document.querySelectorAll('#point-categories-draft details[id^="pc-objective-"]')]
      .filter(objective => /^pc-objective-\d+$/.test(objective.id))
      .map(objective => [`#${objective.id}`, objective.querySelector(':scope > summary')?.textContent.replace(/\s*🖼️\s*View image\s*$/i, '').trim()]);
    section(copy.navObjectives, objectives, 'flat-navigation-objectives');
    section(copy.navResources, [
      ['#pc-image', copy.visual],
      ['#pc-history-gallery', copy.historical]
    ]);
    section(copy.navPractice, [
      ['#pc-lecture', copy.selfTest],
      ['#pc-simulation', copy.lectureReview],
      ['#pc-objective-return', copy.reviewObjectives],
      ['#pc-notes', copy.notes]
    ]);
    section(copy.navBoard, [
      ['#pc-board-alignment', copy.boardPlan.replace(/\s+—.*$/, '')],
      ['#pc-exam-history', copy.school],
      ['#point-categories-mock-exams', copy.board]
    ]);
    section(copy.navContinuing, [
      ['#pc-learning-plan', copy.cePlan.replace(/\s+—.*$/, '')],
      ['#pc-continuing-reading', copy.ceReviewNav],
      ['#pc-continuing-education-exam', copy.continuingExam]
    ]);
    nav.dataset.flatNavigation = 'true';
  }

  function translate() {
    const copy = text();
    document.querySelectorAll('[data-point-label]').forEach(node => {
      const value = copy[node.dataset.pointLabel];
      if (value) node.textContent = value;
    });
    document.querySelectorAll('[data-point-hero-label]').forEach(node => {
      const value = copy[node.dataset.pointHeroLabel];
      if (value) node.textContent = value;
    });
    const backRoute = backDestination();
    document.querySelectorAll('.point-categories-hero-back').forEach(node => {
      node.href = backRoute.href;
      node.textContent = backRoute.label;
    });
    const cells = document.querySelectorAll('#pc-continuing-education-exam td[data-label]');
    const keys = ['attempts', 'lastAttempt', 'average', 'grade', 'action'];
    cells.forEach((cell, index) => { cell.dataset.label = copy[keys[index]]; });
  }

  function apply() {
    if (!document.getElementById('point-categories-draft')) return;
    ['feedback-readable-layout', 'shu-stream-clinical-examples', 'ying-shu-combination-visual'].forEach(id => {
      document.querySelectorAll(`script#${id}`).forEach((script, index) => {
        script.id = `${id}-script-${index + 1}`;
      });
    });
    ensureHeroButtons();
    ensureContinuingExam();
    renameBlocks();
    orderBlocks();
    rebuildNavigation();
    translate();
  }

  apply();
  requestAnimationFrame(apply);
  window.addEventListener('load', () => setTimeout(apply, 1200), {once: true});
  new MutationObserver(() => setTimeout(() => {
    renameBlocks();
    orderBlocks();
    rebuildNavigation();
    translate();
  }, 0)).observe(document.documentElement, {attributes: true, attributeFilter: ['lang']});
})();
