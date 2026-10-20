(function () {
  'use strict';

  const blockOrder = [
    'Introduction and learning paths',
    'Course overview and chapter reading',
    'Learning objectives and resources',
    'Visual review and flashcards',
    'Self-test and Exam Chapter Review (Lecture)',
    'Review objectives and My Student Notes',
    'NCBAHM Exam Learning Plan',
    'School Level Exam',
    'Board Level Exam',
    'Continuing Education Learning Plan',
    'Continuing Education Review',
    'Continuing Education Exam'
  ];

  const defaults = {
    templateVersion: 1,
    slug: 'new-chapter',
    courseTitle: 'Acupuncture & Point Location',
    chapterTitle: 'New Chapter',
    chapterDescription: 'Add a concise explanation of what the learner will understand and practice.',
    backUrl: 'https://www.acupuncturetests.com/study-guide/',
    heroImageUrl: '',
    heroImageAlt: '',
    chapterReading: 'Add the verified chapter reading and references.',
    ncbahmLearningPlan: 'Explain the chapter’s relevance, expected question types, study materials, preparation strategy and mastery criteria.',
    schoolExamUrl: '',
    boardExamUrl: '',
    continuingEducationPlan: 'Explain the continuing-education purpose, learning sequence and completion requirements.',
    continuingEducationReview: 'Add the ten-question review with answer-specific feedback before the scored activity.',
    continuingEducationExamUrl: '',
    spanishChapterTitle: '',
    spanishChapterDescription: '',
    objectives: [
      { title: 'Objective 1 — Add a measurable learner objective.', subjectMatter: 'Add the verified subject matter.', teachingMethod: 'Reading, visual comparison, retrieval practice and feedback.' }
    ],
    blockOrder: blockOrder.slice()
  };

  function copy(value) { return JSON.parse(JSON.stringify(value)); }
  function normalize(config) {
    const value = Object.assign(copy(defaults), config || {});
    value.objectives = Array.isArray(config && config.objectives) && config.objectives.length
      ? config.objectives.map(item => Object.assign({}, defaults.objectives[0], item))
      : copy(defaults.objectives);
    value.blockOrder = blockOrder.slice();
    return value;
  }
  function validate(config) {
    const value = normalize(config);
    const errors = [];
    const warnings = [];
    if (!value.chapterTitle.trim()) errors.push('Chapter title is required.');
    if (!value.slug.trim()) errors.push('Chapter slug is required.');
    if (!value.objectives.length) errors.push('At least one objective is required.');
    if (!value.chapterReading.trim()) warnings.push('Chapter reading is still a placeholder.');
    if (!value.schoolExamUrl.trim()) warnings.push('School Level Exam link is missing.');
    if (!value.boardExamUrl.trim()) warnings.push('Board Level Exam link is missing.');
    if (!value.continuingEducationExamUrl.trim()) warnings.push('Continuing Education Exam link is missing.');
    if (!value.spanishChapterTitle.trim()) warnings.push('Spanish chapter title is missing.');
    return { value, errors, warnings };
  }
  function download(config) {
    const result = validate(config);
    const blob = new Blob([JSON.stringify(result.value, null, 2) + '\n'], { type: 'application/json;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = (result.value.slug || 'chapter') + '-chapter-template.json';
    link.click();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
    return result;
  }

  window.StudyGuideChapterTemplate = { version: 1, blockOrder, defaults, normalize, validate, download };
})();
