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
    'Certificate preview and completion status',
    'Continuing Education Exam'
  ];

  const defaults = {
    templateVersion: 3,
    slug: 'new-chapter',
    courseTitle: 'Acupuncture & Point Location',
    chapterTitle: 'New Chapter',
    chapterDescription: 'Add a concise explanation of what the learner will understand and practice.',
    backUrl: 'https://www.acupuncturetests.com/study-guide/',
    heroImageUrl: '',
    heroImageAlt: '',
    chapterReading: 'Add the verified chapter reading and references.',
    ncbahmLearningPlan: 'Explain the chapter’s relevance, expected question types, study materials, preparation strategy and mastery criteria.',
    moodleExamHistoryUrl: '',
    schoolExams: [],
    continuingEducationPlan: 'Explain the continuing-education purpose, learning sequence and completion requirements.',
    continuingEducationReview: 'Add the ten-question review with answer-specific feedback before the scored activity.',
    continuingEducationReviewQuestionCount: 10,
    continuingEducationExamTitle: 'New Chapter Continuing Education (Mock 10)',
    continuingEducationExamUrl: '',
    ceApprovalStatus: 'preparation',
    cePubliclyActive: false,
    ceRequirementGroup: 'General',
    ceSubjectArea: 'General',
    ceBrokerSubjectCode: 'GN',
    ceDeliveryMethod: 'Computer-Based Training',
    ceDeliveryFormat: 'Online, asynchronous and self-paced',
    minutesPerObjective: 15,
    approvedCeCreditHours: '',
    ceApprovalNumber: '',
    ceApprovedOn: '',
    ceExpiresOn: '',
    ceProviderName: '',
    ceProviderNumber: '',
    ceCourseLocation: 'Online',
    ceCertificateEnabled: false,
    spanishChapterTitle: '',
    spanishChapterDescription: '',
    spanishChapterUrl: '',
    objectives: [
      { title: 'Objective 1 — Add a measurable learner objective.', subjectMatter: 'Add the verified subject matter.', teachingMethod: 'Reading, visual comparison, retrieval practice and feedback.', mockTitle: 'Objective 1 (Mock 10)', mockUrl: '', legacyExamTitle: '', legacyExamUrl: '' }
    ],
    blockOrder: blockOrder.slice()
  };

  function copy(value) { return JSON.parse(JSON.stringify(value)); }
  function normalize(config) {
    const value = Object.assign(copy(defaults), config || {});
    value.templateVersion = 3;
    value.objectives = Array.isArray(config && config.objectives) && config.objectives.length
      ? config.objectives.map(item => Object.assign({}, defaults.objectives[0], item))
      : copy(defaults.objectives);
    value.schoolExams = Array.isArray(config && config.schoolExams)
      ? config.schoolExams.map(item => ({title: String(item.title || '').trim(), url: String(item.url || '').trim()})).filter(item => item.title || item.url)
      : [];
    if (!value.schoolExams.length && config && config.schoolExamUrl) {
      value.schoolExams = [{title: `${value.chapterTitle} School Level Exam`, url: String(config.schoolExamUrl).trim()}];
    }
    value.blockOrder = blockOrder.slice();
    value.minutesPerObjective = Math.max(1, Number(value.minutesPerObjective) || 15);
    value.continuingEducationReviewQuestionCount = Math.max(0, Number(value.continuingEducationReviewQuestionCount) || 0);
    value.approvedCeCreditHours = value.approvedCeCreditHours === '' || value.approvedCeCreditHours === null
      ? '' : Number(value.approvedCeCreditHours);
    value.cePubliclyActive = value.cePubliclyActive === true || value.cePubliclyActive === 'true';
    value.ceCertificateEnabled = value.ceCertificateEnabled === true || value.ceCertificateEnabled === 'true';
    return value;
  }
  function estimatedStudyHours(config) {
    const value = normalize(config);
    return (value.objectives.length * value.minutesPerObjective) / 60;
  }
  function assessmentConfig(config) {
    const value = normalize(config);
    const moodleMockLinks = {};
    value.objectives.forEach((objective, index) => {
      moodleMockLinks[`objective-${index + 1}`] = objective.mockUrl;
    });
    return {
      courseId: value.slug,
      title: value.chapterTitle,
      chapterTitle: value.chapterTitle,
      schoolExamTitle: `School Level Exam — ${value.chapterTitle}`,
      boardExamTitle: `Board Level Exam — ${value.chapterTitle}`,
      spanishChapterLink: value.spanishChapterUrl,
      moodleExamHistoryLink: value.moodleExamHistoryUrl,
      cePlanTitle: `Continuing Education Learning Plan — ${value.chapterTitle}`,
      ceExamTitle: `Continuing Education Exam — ${value.chapterTitle}`,
      ceExamLink: value.continuingEducationExamUrl,
      certificateTitle: value.chapterTitle,
      planningHours: estimatedStudyHours(value),
      moodleMockLinks,
      schoolExams: value.schoolExams
    };
  }
  function validate(config) {
    const value = normalize(config);
    const errors = [];
    const warnings = [];
    if (!value.chapterTitle.trim()) errors.push('Chapter title is required.');
    if (!value.slug.trim()) errors.push('Chapter slug is required.');
    if (!value.objectives.length) errors.push('At least one objective is required.');
    if (!value.chapterReading.trim()) warnings.push('Chapter reading is still a placeholder.');
    if (!value.moodleExamHistoryUrl.trim()) warnings.push('Moodle Exam History link is missing.');
    if (!value.schoolExams.length) warnings.push('School Level Exam activities are missing.');
    value.schoolExams.forEach((exam, index) => {
      if (!exam.title) errors.push(`School Level Exam ${index + 1} needs its exact Moodle activity name.`);
      if (!exam.url) errors.push(`School Level Exam ${index + 1} needs its Moodle URL.`);
    });
    value.objectives.forEach((objective, index) => {
      if (!objective.mockTitle.trim()) errors.push(`Objective ${index + 1} needs its complete Mock 10 title.`);
      if (!objective.mockUrl.trim()) warnings.push(`Objective ${index + 1} Mock 10 link is missing.`);
      if ((objective.legacyExamTitle && !objective.legacyExamUrl) || (!objective.legacyExamTitle && objective.legacyExamUrl)) errors.push(`Objective ${index + 1} must provide both the legacy exam title and URL.`);
    });
    if (value.continuingEducationReviewQuestionCount !== 10) errors.push('Continuing Education Review must contain exactly 10 questions.');
    if (!value.continuingEducationExamTitle.trim()) errors.push('The complete Continuing Education Mock 10 activity name is required.');
    if (!value.continuingEducationExamUrl.trim()) warnings.push('Continuing Education Exam link is missing.');
    if (!value.spanishChapterTitle.trim()) warnings.push('Spanish chapter title is missing.');
    if (!value.spanishChapterUrl.trim()) warnings.push('Spanish chapter URL is missing; the Español hero button cannot be connected.');
    if (value.cePubliclyActive && value.ceApprovalStatus !== 'approved') errors.push('A CE course cannot be publicly active until its approval status is Approved.');
    if (value.ceApprovalStatus === 'approved') {
      if (!(Number(value.approvedCeCreditHours) > 0)) errors.push('Approved CE credit hours are required for an approved course.');
      if (!value.ceApprovalNumber.trim()) errors.push('The Board approval number is required for an approved course.');
      if (!value.ceSubjectArea.trim()) errors.push('The CE Broker subject area is required for an approved course.');
      if (!value.ceDeliveryMethod.trim()) errors.push('The CE Broker delivery method is required for an approved course.');
      if (!value.ceProviderName.trim()) errors.push('The approved provider name is required for an approved course.');
    }
    if (value.ceCertificateEnabled && !value.cePubliclyActive) errors.push('Certificates can be enabled only for an approved and publicly active course.');
    if (value.ceApprovalStatus !== 'approved') warnings.push('Estimated study time is internal planning only and will not be displayed as approved CE credit.');
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

  window.StudyGuideChapterTemplate = { version: 3, blockOrder, defaults, normalize, validate, estimatedStudyHours, assessmentConfig, download };
})();
