(function () {
  'use strict';

  const STATUS = Object.freeze({
    PREPARATION: 'preparation',
    SUBMITTED: 'submitted',
    APPROVED: 'approved',
    INACTIVE: 'inactive',
    EXPIRED: 'expired'
  });

  const courses = [
    {
      key: 'channels',
      title: 'The Channels and Collaterals',
      shortDescription: 'Channel functions, pathways, relationships and organization presented through guided reading, visual review and structured practice.',
      boardUrl: 'index.html?track=board#sgv2-introduction',
      continuingEducationUrl: 'index.html?track=continuing-education#sgv2-introduction',
      objectiveCount: 9,
      minutesPerObjective: 15,
      classification: {
        requirementGroup: 'General',
        subjectArea: 'General',
        ceBrokerSubjectCode: 'GN'
      },
      delivery: {
        method: 'Computer-Based Training',
        format: 'Online, asynchronous and self-paced'
      },
      provider: {
        name: '',
        number: ''
      },
      approval: {
        status: STATUS.PREPARATION,
        publiclyActive: false,
        approvedCreditHours: null,
        approvalNumber: '',
        approvedOn: '',
        expiresOn: ''
      },
      certificate: {
        enabled: false
      },
      payment: {
        currency: 'USD',
        price: null,
        enrolmentUrl: ''
      }
    },
    {
      key: 'point-categories',
      title: 'Point Categories',
      shortDescription: 'Point-category definitions, clinical relationships and key distinctions reinforced through chapter-wide review and Mock practice.',
      boardUrl: 'index.html?track=board#point-categories-draft',
      continuingEducationUrl: 'index.html?track=continuing-education#point-categories-draft',
      objectiveCount: 26,
      minutesPerObjective: 15,
      classification: {
        requirementGroup: 'General',
        subjectArea: 'General',
        ceBrokerSubjectCode: 'GN'
      },
      delivery: {
        method: 'Computer-Based Training',
        format: 'Online, asynchronous and self-paced'
      },
      provider: {
        name: '',
        number: ''
      },
      approval: {
        status: STATUS.PREPARATION,
        publiclyActive: false,
        approvedCreditHours: null,
        approvalNumber: '',
        approvedOn: '',
        expiresOn: ''
      },
      certificate: {
        enabled: false
      },
      payment: {
        currency: 'USD',
        price: null,
        enrolmentUrl: ''
      }
    }
  ];

  function estimatedHours(course) {
    return (Number(course.objectiveCount) * Number(course.minutesPerObjective)) / 60;
  }

  function isApprovedAndActive(course) {
    const approval = course && course.approval;
    const today = new Date().toISOString().slice(0, 10);
    const withinApprovalDates = Boolean(
      (!approval || !approval.approvedOn || approval.approvedOn <= today) &&
      (!approval || !approval.expiresOn || approval.expiresOn >= today)
    );
    return Boolean(
      approval &&
      approval.status === STATUS.APPROVED &&
      approval.publiclyActive === true &&
      Number(approval.approvedCreditHours) > 0 &&
      String(approval.approvalNumber || '').trim() &&
      String(course.provider && course.provider.name || '').trim() &&
      withinApprovalDates
    );
  }

  function getCourse(key) {
    return courses.find(course => course.key === key) || null;
  }

  window.AcupunctureContinuingEducation = Object.freeze({
    schemaVersion: 1,
    status: STATUS,
    courses: Object.freeze(courses),
    estimatedHours,
    isApprovedAndActive,
    getCourse
  });
})();
