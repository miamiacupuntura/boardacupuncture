(() => {
  'use strict';
  const PATTERN_VERSION = '1.0';
  const examColumns = ['Exam', 'Attempts', 'Last Attempt', 'Average', 'Grade', 'Action'];
  const finalBlockOrder = ['gallery', 'continuingEducation', 'practice', 'boardSimulation', 'examHistory', 'mockExams', 'objectiveReview', 'studentNotes'];
  const chapters = {
    channels: {
      root: '.sgv2-layout',
      hero: '#sgv2-introduction',
      navigation: 'nav[aria-label="Study Guide sections"]',
      objectiveSelector: '.sgv2-layout > .sgv2-content > details[id^="channels-objective-"]',
      objectiveCount: 9,
      finalBlocks: {
        gallery: '#chapter-image-library', continuingEducation: '#channels-chapter-continuing-education', practice: '#topic-questions-answers',
        boardSimulation: '#chapter-mock-review', examHistory: '#chapter-exam-history', mockExams: '#channels-mock-exams',
        objectiveReview: '#sgv2-final-review', studentNotes: '#student_notes_form'
      }
    },
    pointCategories: {
      root: '#point-categories-draft',
      hero: '#pc-introduction',
      navigation: '#point-categories-study-navigation',
      objectiveSelector: '#point-categories-draft .sgv2-content > details.sgv2-objective[id^="pc-objective-"]',
      objectiveCount: 26,
      finalBlocks: {
        gallery: '#pc-image', continuingEducation: '#pc-continuing-reading', practice: '#pc-lecture',
        boardSimulation: '#pc-simulation', examHistory: '#pc-exam-history', mockExams: '#point-categories-mock-exams',
        objectiveReview: '#pc-objective-return', studentNotes: '#pc-notes'
      }
    }
  };

  function markChapter(name, config) {
    const root = document.querySelector(config.root);
    const hero = document.querySelector(config.hero);
    const navigation = document.querySelector(config.navigation);
    if (!root || !hero || !navigation) return false;
    root.dataset.chapterPattern = PATTERN_VERSION;
    root.dataset.chapterKey = name;
    hero.dataset.chapterPatternRole = 'hero';
    navigation.dataset.chapterPatternRole = 'navigation';
    Object.entries(config.finalBlocks).forEach(([role, selector]) => {
      document.querySelector(selector)?.setAttribute('data-chapter-pattern-role', role);
    });
    return true;
  }

  function validateChapter(name) {
    const config = chapters[name];
    if (!config) return {valid: false, errors: [`Unknown chapter: ${name}`]};
    const errors = [];
    for (const key of ['root', 'hero', 'navigation']) if (!document.querySelector(config[key])) errors.push(`Missing ${key}: ${config[key]}`);
    const objectiveCount = document.querySelectorAll(config.objectiveSelector).length;
    if (objectiveCount !== config.objectiveCount) errors.push(`Expected ${config.objectiveCount} objectives; found ${objectiveCount}`);
    Object.entries(config.finalBlocks).forEach(([role, selector]) => {if (!document.querySelector(selector)) errors.push(`Missing ${role}: ${selector}`)});
    const tables = [...document.querySelectorAll(`${config.finalBlocks.examHistory} table, ${config.finalBlocks.mockExams} table`)];
    tables.forEach((table, index) => {
      const labels = [...table.querySelectorAll('thead th')].map(th => th.textContent.trim());
      if (labels.join('|') !== examColumns.join('|')) errors.push(`Exam table ${index + 1} does not use the standard six columns`);
    });
    return {valid: errors.length === 0, errors, objectiveCount, patternVersion: PATTERN_VERSION};
  }

  function apply() {Object.entries(chapters).forEach(([name, config]) => markChapter(name, config));}
  apply();
  document.querySelectorAll('.chapter-selector button').forEach(button => button.addEventListener('click', () => setTimeout(apply, 0)));

  window.AcupunctureChapterPattern = Object.freeze({
    version: PATTERN_VERSION,
    examColumns: Object.freeze([...examColumns]),
    finalBlockOrder: Object.freeze([...finalBlockOrder]),
    chapters: Object.freeze(chapters),
    apply,
    validateChapter
  });
})();
