(function () {
  'use strict';

  const registry = window.AcupunctureContinuingEducation;
  if (!registry) return;

  const chapters = [
    {key: 'channels', hero: '#sgv2-introduction'},
    {key: 'point-categories', hero: '#pc-introduction'}
  ];

  const copy = {
    en: {
      boardTitle: 'Board preparation access',
      boardText: 'Use this chapter for study and exam preparation. This access does not award or claim Continuing Education credit.',
      ceTitle: 'Continuing Education course status',
      preparation: 'In preparation — not yet approved for Continuing Education credit.',
      submitted: 'Submitted for review — not yet approved or active.',
      inactive: 'Board approval is not currently active.',
      expired: 'The prior approval period has ended. This course is not currently active.',
      approved: 'Board-approved and active',
      approvedUnavailable: 'The approval record is not complete or is outside its active dates. CE credit is not currently available.',
      subject: 'Subject area',
      method: 'Delivery method',
      hours: 'Approved CE credit hours',
      approval: 'Approval number',
      provider: 'Approved provider'
    },
    es: {
      boardTitle: 'Acceso para preparación del Board',
      boardText: 'Usa este capítulo para estudiar y prepararte para los exámenes. Este acceso no otorga ni reclama créditos de Continuing Education.',
      ceTitle: 'Estado del curso de Continuing Education',
      preparation: 'En preparación — todavía no está aprobado para créditos de Continuing Education.',
      submitted: 'Presentado para revisión — todavía no está aprobado ni activo.',
      inactive: 'La aprobación del Board no está activa actualmente.',
      expired: 'El período de aprobación anterior terminó. Este curso no está activo actualmente.',
      approved: 'Aprobado por el Board y activo',
      approvedUnavailable: 'El registro de aprobación está incompleto o fuera de sus fechas activas. El crédito CE no está disponible actualmente.',
      subject: 'Materia',
      method: 'Modalidad',
      hours: 'Horas de crédito CE aprobadas',
      approval: 'Número de aprobación',
      provider: 'Proveedor aprobado'
    }
  };

  function language() {
    return document.documentElement.lang.toLowerCase().startsWith('es') ? 'es' : 'en';
  }

  function createStatus(config) {
    const hero = document.querySelector(config.hero);
    const course = registry.getCourse(config.key);
    if (!hero || !course || document.getElementById(`ce-status-${config.key}`)) return null;

    const section = document.createElement('section');
    section.id = `ce-status-${config.key}`;
    section.className = 'chapter-ce-status';
    section.dataset.courseKey = config.key;
    hero.insertAdjacentElement('afterend', section);
    return section;
  }

  function render(section) {
    const course = registry.getCourse(section.dataset.courseKey);
    if (!course) return;
    const words = copy[language()];
    const ceRoute = window.ACUPUNCTURE_ENTRY_TRACK === 'continuing-education';
    const active = registry.isApprovedAndActive(course);
    const status = course.approval.status;
    section.classList.toggle('is-approved', active);
    section.classList.toggle('is-ce-route', ceRoute);
    section.replaceChildren();

    const title = document.createElement('h3');
    title.textContent = ceRoute ? words.ceTitle : words.boardTitle;
    section.append(title);

    const message = document.createElement('p');
    if (!ceRoute) {
      message.textContent = words.boardText;
      section.append(message);
      return;
    }

    if (!active) {
      message.textContent = status === 'approved' ? words.approvedUnavailable : (words[status] || words.preparation);
      section.append(message);
      return;
    }

    message.className = 'chapter-ce-status__badge';
    message.textContent = words.approved;
    section.append(message);
    const list = document.createElement('dl');
    [
      [words.subject, course.classification.subjectArea],
      [words.method, course.delivery.method],
      [words.hours, String(course.approval.approvedCreditHours)],
      [words.approval, course.approval.approvalNumber],
      [words.provider, course.provider.name]
    ].forEach(([label, value]) => {
      const dt = document.createElement('dt'); dt.textContent = label;
      const dd = document.createElement('dd'); dd.textContent = value;
      list.append(dt, dd);
    });
    section.append(list);
  }

  const sections = chapters.map(createStatus).filter(Boolean);
  if (!sections.length) return;
  const update = () => sections.forEach(render);
  update();
  new MutationObserver(update).observe(document.documentElement, {attributes: true, attributeFilter: ['lang']});

  const style = document.createElement('style');
  style.textContent = `
    .chapter-ce-status{box-sizing:border-box;margin:12px 0 20px;padding:15px 18px;border:1px solid #cbd8dc;border-left:5px solid #38689a;border-radius:10px;background:#f5f9fc;color:#173747}
    .chapter-ce-status.is-ce-route{border-left-color:#7652a5;background:#faf7fd}.chapter-ce-status.is-approved{border-left-color:#167657;background:#f1faf6}
    .chapter-ce-status h3{margin:0 0 6px;font-size:1.05rem}.chapter-ce-status p{margin:0;line-height:1.5}.chapter-ce-status__badge{font-weight:800;color:#126247}
    .chapter-ce-status dl{display:grid;grid-template-columns:minmax(130px,auto) 1fr;gap:6px 16px;margin:12px 0 0}.chapter-ce-status dt{font-weight:800}.chapter-ce-status dd{margin:0}
    @media(max-width:600px){.chapter-ce-status{margin:10px 0 16px;padding:14px}.chapter-ce-status dl{grid-template-columns:1fr;gap:2px}.chapter-ce-status dd{margin-bottom:8px}}
  `;
  document.head.append(style);
})();
