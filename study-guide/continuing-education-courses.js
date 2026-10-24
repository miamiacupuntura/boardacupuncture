(function () {
  'use strict';

  const STATUS = Object.freeze({
    PREPARATION: 'preparation',
    SUBMITTED: 'submitted',
    APPROVED: 'approved',
    INACTIVE: 'inactive',
    EXPIRED: 'expired'
  });

  const PRICING = Object.freeze({
    currency: 'USD',
    amountPerCreditHour: 10
  });

  const courses = [
    {
      key: 'channels',
      title: 'The Channels and Collaterals',
      shortDescription: 'Channel functions, pathways, relationships and organization presented through guided reading, visual review and structured practice.',
      boardUrl: 'index.html?track=board&return=board-catalog#sgv2-introduction',
      continuingEducationUrl: 'index.html?track=continuing-education&return=ce-catalog#sgv2-introduction',
      objectiveCount: 9,
      minutesPerObjective: 15,
      board: {
        publiclyActive: true
      },
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
        enrolmentUrl: ''
      }
    },
    {
      key: 'point-categories',
      title: 'Point Categories',
      shortDescription: 'Point-category definitions, clinical relationships and key distinctions reinforced through chapter-wide review and Mock practice.',
      boardUrl: 'index.html?track=board&return=board-catalog#point-categories-draft',
      continuingEducationUrl: 'index.html?track=continuing-education&return=ce-catalog#point-categories-draft',
      objectiveCount: 26,
      minutesPerObjective: 15,
      board: {
        publiclyActive: true
      },
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
        enrolmentUrl: ''
      }
    },
    {
      key: 'point-contraindications',
      title: 'Point Contraindications and Anatomical Safety',
      shortDescription: 'Region-based safety recognition, source verification, adverse-event awareness and integrated clinical reasoning across 12 learning objectives.',
      boardUrl: 'point-contraindications/index.html?track=board&return=board-catalog',
      continuingEducationUrl: 'point-contraindications/index.html?track=continuing-education&return=ce-catalog#continuing-education-start',
      objectiveCount: 12,
      minutesPerObjective: 15,
      board: {
        publiclyActive: true
      },
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
        enrolmentUrl: ''
      }
    }
  ];

  function estimatedHours(course) {
    return (Number(course.objectiveCount) * Number(course.minutesPerObjective)) / 60;
  }

  function coursePrice(course) {
    const hours = Number(course && course.approval && course.approval.approvedCreditHours);
    return hours > 0 ? hours * PRICING.amountPerCreditHour : null;
  }

  function formatPrice(course) {
    const amount = coursePrice(course);
    if (amount === null) return '';
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: PRICING.currency,
      minimumFractionDigits: 2
    }).format(amount);
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
    schemaVersion: 2,
    status: STATUS,
    pricing: PRICING,
    courses: Object.freeze(courses),
    estimatedHours,
    coursePrice,
    formatPrice,
    isApprovedAndActive,
    getCourse
  });
})();
