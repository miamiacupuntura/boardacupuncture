(() => {
  'use strict';

  const cePlan = document.getElementById('continuing-education-plan');
  if (!cePlan || document.getElementById('ce-safety-scenarios')) return;

  const cases = [
    {
      number: 1,
      title: 'Pregnancy screening was not updated',
      tag: 'Pregnancy · screening · escalation',
      scenario: 'A patient at 34 weeks of pregnancy presents with severe low-back pain and swelling. The practitioner proceeds without updating the obstetric history or checking current pregnancy-specific guidance. Later, the patient reports contractions and bleeding.',
      error: 'The failure is not a single memorized point name. It is the missing pregnancy screen, failure to identify obstetric warning signs, and failure to verify the complete point and modality guidance before proceeding.',
      consequence: 'Contractions and bleeding during pregnancy require urgent obstetric assessment. The timing raises a possible treatment-related concern, but causation cannot be established from sequence alone. Complete documentation and incident review are essential.',
      prevention: 'Use an updated pregnancy and symptom screen, identify urgent referral criteria, verify current point- and modality-specific guidance, obtain appropriate consent, and remain within training and jurisdictional scope.',
      model: 'SAFER: Screen pregnancy stage and symptoms; identify pregnancy as patient context rather than direct tissue injury; find the current obstetric and point references; explain the limits of causation; activate the urgent obstetric pathway and document the event.'
    },
    {
      number: 2,
      title: 'Thoracic warning signs were dismissed',
      tag: 'Pleura · delayed symptoms · emergency',
      scenario: 'After treatment in an anterior thoracic and supraclavicular region, a patient develops sudden sharp chest pain and a dry cough. The symptoms are dismissed. Several hours later, the patient develops severe shortness of breath and increasing distress.',
      error: 'The practitioner failed to connect the anatomical region with pleural risk and misclassified new chest symptoms as an expected treatment sensation instead of a possible adverse event.',
      consequence: 'Chest pain, cough and shortness of breath after treatment near the thorax can indicate pneumothorax. Symptoms may be delayed, and deterioration can become life-threatening.',
      prevention: 'Recognize the thoracic risk family before treatment, screen new symptoms promptly, stop the activity, follow the current emergency protocol and obtain urgent medical evaluation when pneumothorax is suspected.',
      model: 'SAFER: Screen the new symptoms and timing; name pleura and lung as the risk structures; consult the current adverse-event protocol; explain why delayed onset does not rule out injury; escalate for emergency assessment.'
    },
    {
      number: 3,
      title: 'An orbital-risk point was treated as routine',
      tag: 'Orbit · vascular/optic structures · urgent referral',
      scenario: 'During work near an orbital point, the patient moves unexpectedly. Rapid swelling, severe orbital pain, protrusion of the eye or a visual change develops soon afterward.',
      error: 'The practitioner failed to treat the orbit as an advanced high-risk region, did not adequately control for movement, or continued despite findings that required immediate reassessment.',
      consequence: 'Rapid orbital swelling, severe pain, proptosis or vision change may indicate significant bleeding or pressure threatening ocular and optic structures. Delay can increase the risk of permanent harm.',
      prevention: 'Limit orbital work to appropriately trained practitioners using complete current guidance. Stop when concerning symptoms appear, protect the patient, and activate urgent ophthalmic or emergency evaluation.',
      model: 'SAFER: Screen movement and symptoms; identify globe, optic and vascular structures; find the current orbital adverse-event protocol; explain the threat to vision; obtain urgent specialist or emergency assessment.'
    },
    {
      number: 4,
      title: 'Bladder state was overlooked',
      tag: 'Lower abdomen · organ state · hematuria',
      scenario: 'A patient with urinary retention receives treatment in the lower anterior midline without an updated bladder-status check. New pelvic pain and visible blood in the urine occur afterward.',
      error: 'The fixed point location was considered without the changing organ state. A distended bladder can rise above the pubic symphysis and alter lower-abdominal relationships.',
      consequence: 'New pelvic pain or hematuria after treatment requires prompt medical assessment for possible urinary-tract or bladder injury and other causes. The event must be documented without assuming a diagnosis beyond the evidence.',
      prevention: 'Screen urinary symptoms and bladder status, recognize when organ distension changes anatomy, verify the complete current point reference, and refer or escalate when findings exceed the practitioner’s scope.',
      model: 'SAFER: Screen retention, pain and hematuria; identify bladder state and lower-abdominal anatomy; find the complete source and clinical protocol; explain the differential and evidence limits; arrange prompt medical evaluation.'
    },
    {
      number: 5,
      title: 'Suboccipital neural risk was underestimated',
      tag: 'Foramen magnum · central neural anatomy · emergency',
      scenario: 'During treatment in the lower occipital region, a patient develops sudden loss of consciousness, respiratory difficulty or new neurological findings.',
      error: 'The practitioner failed to recognize the relationship of the region to the foramen magnum, medulla and upper spinal canal, or continued despite an acute neurological or respiratory warning sign.',
      consequence: 'Loss of consciousness, respiratory compromise or an acute neurological deficit is a medical emergency. These findings require immediate activation of the clinic emergency plan and emergency medical services.',
      prevention: 'Treat the suboccipital region as critical central neural anatomy, use only complete current guidance and formal supervised training, and rehearse emergency activation, documentation and transfer procedures.',
      model: 'SAFER: Screen the acute neurological and respiratory findings; name the central neural structures; use the clinic emergency protocol rather than a short point note; explain the urgency; activate emergency services and document the sequence.'
    }
  ];

  const section = document.createElement('section');
  section.className = 'card ce-case-lab';
  section.id = 'ce-safety-scenarios';
  section.innerHTML = `
    <header class="section-heading"><div><span class="eyebrow">CONTINUING EDUCATION · CASE APPLICATION LAB</span><h2>Analyze five safety breakdowns with SAFER</h2><p>Work through each scenario before opening its model analysis. Focus on screening, risk classification, warning signs, source limits, escalation and documentation.</p></div><span class="score-pill" id="ce-case-progress">0 / ${cases.length} analyzed</span></header>
    <aside class="case-disclaimer"><b>Fictional composite scenarios</b><span>These cases illustrate common safety-failure patterns. They are not verified reports, legal opinions or proof that a named intervention caused an outcome. Legal duties and clinical protocols vary by jurisdiction and current standard.</span></aside>
    <div class="ce-case-list">
      ${cases.map(item => `
        <details class="ce-case" data-case="${item.number}">
          <summary><span>Case ${item.number}</span><strong>${item.title}</strong><small>${item.tag}</small></summary>
          <div class="ce-case-body">
            <div class="ce-case-grid">
              <article><span>1 · COMPOSITE SCENARIO</span><p>${item.scenario}</p></article>
              <article><span>2 · MISSED SAFEGUARD</span><p>${item.error}</p></article>
              <article><span>3 · POTENTIAL IMPACT</span><p>${item.consequence}</p></article>
              <article><span>4 · PREVENTION / RESPONSE</span><p>${item.prevention}</p></article>
            </div>
            <fieldset class="case-workbook"><legend>Your SAFER analysis</legend><div class="case-workbook-grid"><label><span>Risk family and vulnerable structure/context</span><textarea rows="2" data-field="risk" placeholder="Name the category and the specific anatomy or patient factor."></textarea></label><label><span>Most important warning sign or missed screen</span><textarea rows="2" data-field="warning" placeholder="Identify the cue that should change the decision."></textarea></label><label><span>Safe next action within scope</span><textarea rows="2" data-field="action" placeholder="State the escalation, source check or documentation step."></textarea></label><label><span>Why the closest alternative explanation is weaker</span><textarea rows="2" data-field="alternative" placeholder="Explain the evidence and its limits."></textarea></label></div><label class="case-complete"><input type="checkbox" data-field="complete"><span>I completed and checked this case analysis.</span></label><p class="case-save-status" role="status">Responses save automatically in this browser.</p></fieldset>
            <details class="model-analysis"><summary>Compare with the model SAFER analysis</summary><p>${item.model}</p></details>
          </div>
        </details>`).join('')}
    </div>
    <p class="case-time-note">This application lab reinforces Objectives 1, 10, 11 and 12. It does not independently award credit or change the chapter’s 180-minute planning framework.</p>`;

  cePlan.before(section);

  const practiceNav = document.querySelector('.study-nav details:last-of-type');
  const cePlanLink = practiceNav?.querySelector('a[href="#continuing-education-plan"]');
  if (practiceNav && cePlanLink && !practiceNav.querySelector('a[href="#ce-safety-scenarios"]')) {
    const link = document.createElement('a');
    link.href = '#ce-safety-scenarios';
    link.textContent = 'CE safety case lab';
    practiceNav.insertBefore(link, cePlanLink);
  }

  const storageKey = 'acupuncturetests-point-contraindications-ce-cases-v1';
  const readState = () => {
    try { return JSON.parse(localStorage.getItem(storageKey)) || {}; } catch (_) { return {}; }
  };
  const state = readState();

  function updateProgress() {
    const completed = [...section.querySelectorAll('[data-field="complete"]')].filter(input => input.checked).length;
    document.getElementById('ce-case-progress').textContent = `${completed} / ${cases.length} analyzed`;
  }

  section.querySelectorAll('.ce-case').forEach(caseElement => {
    const caseId = caseElement.dataset.case;
    const saved = state[caseId] || {};
    caseElement.querySelectorAll('[data-field]').forEach(input => {
      const field = input.dataset.field;
      if (input.type === 'checkbox') input.checked = Boolean(saved[field]);
      else if (typeof saved[field] === 'string') input.value = saved[field];
      input.addEventListener('input', () => {
        const latest = readState();
        latest[caseId] = latest[caseId] || {};
        latest[caseId][field] = input.type === 'checkbox' ? input.checked : input.value;
        latest[caseId].updatedAt = new Date().toISOString();
        localStorage.setItem(storageKey, JSON.stringify(latest));
        const status = caseElement.querySelector('.case-save-status');
        status.textContent = 'Saved automatically in this browser.';
        window.clearTimeout(status._timer);
        status._timer = window.setTimeout(() => { status.textContent = 'Responses save automatically in this browser.'; }, 1800);
        updateProgress();
      });
    });
  });

  updateProgress();
})();
