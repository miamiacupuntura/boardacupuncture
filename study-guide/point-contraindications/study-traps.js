(() => {
  'use strict';

  const boardFormats = document.getElementById('board-question-formats');
  if (!boardFormats || document.getElementById('study-traps')) return;

  const section = document.createElement('section');
  section.className = 'card study-traps';
  section.id = 'study-traps';
  section.innerHTML = `
    <header class="section-heading"><div><span class="eyebrow">BOARD + CONTINUING EDUCATION · STUDY HABITS</span><h2>Four study traps that weaken safety reasoning</h2><p>Replace isolated memorization with regional anatomy, patient context, source classification and adverse-event recognition.</p></div><span class="stage">Study audit</span></header>
    <div class="trap-overview" aria-label="Four study traps"><span>1 · Channel lists</span><span>2 · One-size-fits-all labels</span><span>3 · Missing patient context</span><span>4 · No consequence link</span></div>
    <div class="trap-list">
      <details class="study-trap" open><summary><span>Trap 1</span><strong>Studying by channel name instead of anatomical region</strong></summary><div class="trap-body"><article><b>The weak habit</b><p>Trying to memorize every “dangerous Stomach point” or every restriction on one channel as an isolated list.</p></article><article><b>Why it fails</b><p>Injury mechanisms follow anatomy. Thoracic points from different channels can share pleural risk, while points on the same channel can involve entirely different structures.</p></article><article><b>Replace it with</b><p>Build regional maps: orbital/facial, supraclavicular and thoracic, abdominal/pelvic, posterior spinal/suboccipital, and major neurovascular regions. Then place individual points inside the map.</p></article></div></details>
      <details class="study-trap"><summary><span>Trap 2</span><strong>Flattening every caution into an “absolute prohibition”</strong></summary><div class="trap-body"><article><b>The weak habit</b><p>Treating anatomical risk, patient context, a named-modality restriction and historical wording as if they all meant “never use this point.”</p></article><article><b>Why it fails</b><p>The source may describe a point-specific technique limitation, a temporary patient condition, a modality-only restriction, a jurisdictional rule or a legacy statement. Those categories are not interchangeable.</p></article><article><b>Replace it with</b><p>Ask what kind of rule it is, whom it applies to, which modality is named, and whether the complete current source supports absolute language. Pregnancy point groups such as LI 4/SP 6 or H-S-J-Z should trigger this check—not replace it.</p></article></div></details>
      <details class="study-trap"><summary><span>Trap 3</span><strong>Ignoring the patient’s changing physiological context</strong></summary><div class="trap-body"><article><b>The weak habit</b><p>Assuming a point has one permanent safety status regardless of the patient’s condition at that moment.</p></article><article><b>Why it fails</b><p>Pregnancy possibility and symptoms, bladder distension or urinary retention, bleeding risk, frailty, local pathology and implanted electronic devices when electroacupuncture is considered can change the decision.</p></article><article><b>Replace it with</b><p>Attach one screening question to every risk family. Clarify the relevant history and symptoms under the current clinic protocol before selecting a point, modality or referral pathway.</p></article></div></details>
      <details class="study-trap"><summary><span>Trap 4</span><strong>Memorizing the point without the emergency consequence</strong></summary><div class="trap-body"><article><b>The weak habit</b><p>Remembering only a point code or the word “contraindicated,” without naming the structure, warning sign or response category.</p></article><article><b>Why it fails</b><p>A clinical vignette may provide symptoms and timing rather than the answer label. Without the anatomy-to-symptom link, pneumothorax, orbital compromise, significant bleeding or central neurological warning signs can be misclassified.</p></article><article><b>Replace it with</b><p>Build every safety card with S-W-R: <b>Structure</b> at risk → <b>Warning sign</b> cluster → <b>Response category</b> such as pause and assess, urgent referral or emergency activation under the current protocol.</p></article></div></details>
    </div>
    <section class="study-triad" aria-labelledby="study-triad-title"><div><span class="mnemonic-code">S · W · R</span><h3 id="study-triad-title">The minimum useful safety card</h3></div><ol><li><b>Structure</b><span>Name the vulnerable anatomy or patient context.</span></li><li><b>Warning signs</b><span>Connect symptoms, timing and severity to the risk.</span></li><li><b>Response category</b><span>State the safe escalation level within scope and protocol.</span></li></ol></section>
    <aside class="trap-boundary"><b>Language check</b><span>Words such as “always,” “never,” “absolute” and “safe” require a complete, current and context-appropriate source. Exact needling technique and emergency procedures remain outside this conceptual study block.</span></aside>`;

  boardFormats.before(section);

  const practiceNav = document.querySelector('.study-nav details:last-of-type');
  const boardFormatsLink = practiceNav?.querySelector('a[href="#board-question-formats"]');
  if (practiceNav && boardFormatsLink && !practiceNav.querySelector('a[href="#study-traps"]')) {
    const link = document.createElement('a');
    link.href = '#study-traps';
    link.textContent = 'Study traps to avoid';
    practiceNav.insertBefore(link, boardFormatsLink);
  }
})();
