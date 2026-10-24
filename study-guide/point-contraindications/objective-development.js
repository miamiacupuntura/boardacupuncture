(() => {
  'use strict';

  const units = {
    'objective-1': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> distinguish a patient-context precaution, a local-site concern, vulnerable anatomy and an adverse response before interpreting a point-specific warning.</p>
        <div class="objective-reading-grid">
          <article class="objective-panel"><h3>Use a four-pass screen</h3><ol><li><b>Patient:</b> weakness, hunger, fatigue, anxiety, pregnancy and bleeding tendency may change the decision.</li><li><b>Site:</b> infection, ulceration, tumor, recent trauma or vulnerable tissue requires separate assessment.</li><li><b>Anatomy:</b> identify the eye, pleura, organ, vessel, nerve or canal involved.</li><li><b>Response:</b> recognize dizziness, nausea, pallor, cold sweating, unusual pain or loss of consciousness as a safety event.</li></ol></article>
          <article class="objective-panel"><h3>Translate the examination question</h3><ul><li>If symptoms are described, identify the event before choosing an action.</li><li>If a point is named, identify the nearby structure before recalling a keyword.</li><li>If a modality is named, keep the warning attached to that modality.</li><li>If the wording is historical, verify it against current training and the complete source.</li></ul></article>
        </div>
        <aside class="common-error"><b>Important source check:</b> the older question bank contains procedural wording for events such as a stuck needle. Treat it as legacy examination content, not as independent clinical instruction. Current supervised training and emergency protocols control the response.</aside>
        <details class="application-prompt"><summary>Apply the objective</summary><p>A learner sees dizziness, nausea, pallor and cold sweating described in a question. The first task is to recognize a possible systemic adverse response—not to search randomly for a point name. Then identify the safe response category and verify the current protocol.</p></details>
      </section>`,
    'objective-2': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> organize Du Mai warnings by region and explain why a posterior midline point cannot be studied from the channel name alone.</p>
        <div class="table-scroll"><table class="pattern-table"><thead><tr><th>Study zone</th><th>Pattern represented in the course bank</th><th>What the learner should conclude</th></tr></thead><tbody>
          <tr><td>Posterior trunk</td><td>Du 5–14 repeatedly refer to the spinal canal, sometimes with a separate method label.</td><td>The recurring concept is midline spinal anatomy. Do not convert a short bank phrase into an unsupervised technique rule.</td></tr>
          <tr><td>Upper neck / cranial transition</td><td>Du 15–16 add brain or cranial context to the spinal-region pattern.</td><td>Recognize that the vulnerable structure changes as the channel reaches the upper neck and head.</td></tr>
          <tr><td>Face and source-specific entries</td><td>Du 17, 24 and 25 use needling or moxa labels in the source bank.</td><td>Keep the modality and source attached to the statement; verify current applicability in the full entry.</td></tr>
        </tbody></table></div>
        <div class="objective-reading-grid"><article class="objective-panel"><h3>Memory rule</h3><p><b>Midline does not mean one rule.</b> Use four questions: Where is the point? Which structure is near it? Is the warning anatomical or method-specific? Is the wording current or historical?</p></article><article class="objective-panel"><h3>Closest distractor</h3><p>A method label such as moxa is not interchangeable with spinal-canal risk. One describes a modality; the other describes vulnerable anatomy.</p></article></div>
        <details class="application-prompt"><summary>Apply the objective</summary><p>Compare one posterior-trunk entry with one upper-neck entry. Name the anatomical zone first, then state what additional source information must be checked before the warning can be interpreted.</p></details>
      </section>`,
    'objective-3': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> distinguish five Gall Bladder regional patterns and prevent a warning from being generalized to the entire channel.</p>
        <div class="region-map">
          <article><span>HEAD</span><b>GB 1–3</b><p>Eye, ear and jaw relationships; the bank also preserves method and positioning phrases that require full-source review.</p></article>
          <article><span>UPPER NECK</span><b>GB 20</b><p>Cranial and spinal-region anatomy is the primary recognition cue.</p></article>
          <article><span>SHOULDER / THORAX</span><b>GB 21–24</b><p>Pleura and lung are the repeated anatomical family; pregnancy or moxa may appear as separate contextual labels.</p></article>
          <article><span>LATERAL ABDOMEN</span><b>GB 25–26</b><p>Peritoneal and visceral relationships replace the thoracic pleural pattern.</p></article>
          <article><span>LOWER LIMB</span><b>GB 33, GB 39</b><p>Method-specific wording and local bony anatomy must be kept distinct.</p></article>
        </div>
        <aside class="memory-rule"><b>Region-first recall:</b> head → neck → thorax → abdomen → lower limb. Predict the risk family from the region, then verify the individual point.</aside>
        <aside class="common-error"><b>Common error:</b> answering “pneumothorax” for every Gall Bladder point because several thoracic entries share that risk. The lateral abdomen, head and lower limb require different reasoning.</aside>
        <p class="anatomy-cross-reference"><b>Anatomy connection:</b> the supplied needling-safety excerpt maps the pleural cavity from the supraclavicular region through the thoracic cage. The complete pleura and organ comparison appears later in Objective 11.</p>
        <details class="application-prompt"><summary>Apply the objective</summary><p>Why should GB 22 and GB 25 not be placed in the same risk family? One is studied through thoracic pleural relationships; the other through abdominal cavity and organ relationships.</p></details>
      </section>`,
    'objective-4': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> reconstruct the Kidney channel’s anterior-trunk safety pattern as a regional progression rather than 17 isolated facts.</p>
        <ol class="risk-ladder">
          <li><span>1</span><div><b>Pelvic / lower abdominal context · KD 11–13</b><p>The course bank emphasizes bladder status. This is a patient and organ-context cue.</p></div></li>
          <li><span>2</span><div><b>Abdominal cavity · KD 14–20</b><p>The repeated family is peritoneal and visceral relationship.</p></div></li>
          <li><span>3</span><div><b>Side-dependent upper abdomen · KD 21</b><p>The source distinguishes right-sided liver context from left-sided peritoneal context. Laterality matters.</p></div></li>
          <li><span>4</span><div><b>Upper abdomen to chest · KD 22–27</b><p>The bank transitions toward lung or pleural risk, with an additional liver relationship in the lower part of the cluster.</p></div></li>
        </ol>
        <div class="objective-reading-grid"><article class="objective-panel"><h3>What changes along the route?</h3><p>The point code increases while the region moves superiorly. The nearby organ or cavity changes with the region; this is why a single keyword cannot answer the whole bank.</p></article><article class="objective-panel"><h3>How to retrieve it</h3><p>Draw four boxes labeled bladder, peritoneum, side-dependent organ and pleura/lung. Place each point cluster into a box, then explain the transition aloud.</p></article></div>
        <aside class="common-error"><b>Common error:</b> confusing a full-bladder precaution with general peritoneal risk. The first depends on patient or organ state; the second is a regional anatomical relationship.</aside>
        <p class="anatomy-cross-reference"><b>Anatomy connection:</b> the source places the kidneys on the posterior abdominal wall, approximately T12–L3, and the bladder behind the pubic symphysis with superior expansion when full. It also notes that liver position and enlargement change upper-abdominal relationships. A complete comparison of kidneys, bladder, liver, spleen, heart, lung and pleura appears later in Objective 11.</p>
      </section>`,
    'objective-5': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> connect Liver channel warnings to the actual location and audit a questionable source answer instead of memorizing it uncritically.</p>
        <div class="table-scroll"><table class="pattern-table"><thead><tr><th>Point region</th><th>Reasoning focus</th><th>Source task</th></tr></thead><tbody>
          <tr><td>LV 12 · inguinal region</td><td>Major local structures and the precise anatomical entry must be reviewed.</td><td>The existing bank answer refers to the fibula, which conflicts with the usual region of LV 12. Flag it for correction and verify the authoritative point entry.</td></tr>
          <tr><td>LV 13 · free end of the eleventh rib</td><td>Underlying organs and possible enlargement change the relationship.</td><td>Confirm how the complete reference describes liver and spleen enlargement.</td></tr>
          <tr><td>LV 14 · intercostal / upper abdominal region</td><td>Thoracic and upper-abdominal relationships must be differentiated.</td><td>Do not assume the same warning as LV 13 merely because the points are consecutive.</td></tr>
        </tbody></table></div>
        <aside class="source-audit"><b>Source-audit lesson:</b> a question bank is assessment material, not the final authority. When an answer conflicts with location anatomy, document the discrepancy, consult the full reference and correct the bank before treating it as settled content.</aside>
        <details class="application-prompt"><summary>Apply the objective</summary><p>Write a one-sentence audit note for LV 12: identify the location conflict, name the reference that should be checked, and avoid inventing a replacement answer without evidence.</p></details>
      </section>`,
    'objective-6': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> name the specific structure represented by each of the three Pericardium channel warning regions.</p>
        <div class="three-point-map"><article><span>C · CHEST</span><h3>PC 1</h3><p>Study the relationship to pleura and lung. This is a thoracic anatomical category.</p></article><article><span>E · ELBOW</span><h3>PC 3</h3><p>Study the brachial artery and vein. This is a major vascular category.</p></article><article><span>W · WRIST</span><h3>PC 7</h3><p>Study the median nerve relationship. This is a neural category.</p></article></div>
        <aside class="memory-rule"><b>C-E-W:</b> Chest → Elbow → Wrist. Then add the structure: pleura/lung → brachial vessels → median nerve.</aside>
        <aside class="common-error"><b>Common error:</b> remembering “Pericardium caution” without naming the structure. The three entries test three different anatomical families.</aside>
        <p class="anatomy-cross-reference"><b>Anatomy connection:</b> the supplied source distinguishes major-vessel warnings from major-nerve warnings. Unexpected sharp pain, bleeding, electric sensation or neurologic symptoms require assessment under the current supervised protocol. The vascular and neural source notes appear later in Objective 11.</p>
        <details class="application-prompt"><summary>Apply the objective</summary><p>If the alternatives are “median nerve,” “brachial vessels” and “pleura,” first locate the region. A wrist question, an elbow question and a chest question should not be answered from the channel name alone.</p></details>
      </section>`,
    'objective-7': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> organize Ren Mai cautions into four anterior-midline regions and distinguish anatomy, patient context, modality and classical wording.</p>
        <div class="table-scroll"><table class="pattern-table"><thead><tr><th>Region</th><th>Course-bank cluster</th><th>Interpretive task</th></tr></thead><tbody>
          <tr><td>Perineal / pelvic · Ren 1–4</td><td>Pregnancy, bladder status and classical needling language.</td><td>Separate patient or organ context from a historical method label.</td></tr>
          <tr><td>Lower and middle abdomen · Ren 5–13</td><td>Peritoneal cavity is repeated; pregnancy, water-illness and modality phrases appear at selected entries.</td><td>Do not generalize one selected method restriction to the entire group.</td></tr>
          <tr><td>Upper abdomen / chest · Ren 14–17</td><td>Liver or heart relationships and classical needling or moxa language.</td><td>Identify anatomy first, then preserve the source date and modality.</td></tr>
          <tr><td>Throat / face · Ren 22–23</td><td>Experience limitation and a source-listed moxa restriction.</td><td>Recognize the sensitive region and verify the complete current entry.</td></tr>
        </tbody></table></div>
        <aside class="memory-rule"><b>Four-zone map:</b> pelvis → abdomen → chest → throat. At each zone ask: anatomy, patient context, modality, or historical source?</aside>
        <aside class="common-error"><b>Common error:</b> treating every Ren Mai statement as a direct anatomical contraindication. Several entries are conditional, modality-specific or explicitly classical.</aside>
        <p class="anatomy-cross-reference"><b>Anatomy connection:</b> the source describes the bladder behind the pubic symphysis and capable of extending into the hypogastric area when full. It also describes the heart resting on the diaphragm and the liver crossing the epigastrium. The complete anatomical source map appears later in Objective 11.</p>
      </section>`,
    'objective-8': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> distinguish movement risk, pleural anatomy, local ear or jaw condition and source-specific restrictions in the San Jiao bank.</p>
        <div class="region-map four-up">
          <article><span>FOREARM</span><b>SJ 5–6</b><p>The bank links movement during treatment with a bent-needle event. The learning point is patient positioning and movement awareness.</p></article>
          <article><span>SOURCE RESTRICTION</span><b>SJ 8</b><p>A short contraindication phrase requires full-source and current-guidance verification.</p></article>
          <article><span>SHOULDER</span><b>SJ 15</b><p>The relevant family is thoracic pleura/lung—not an ear or modality warning.</p></article>
          <article><span>EAR / JAW</span><b>SJ 17, 19, 21, 23</b><p>Jaw movement, classical bleeding language, local discharge and moxa labels represent different categories.</p></article>
        </div>
        <div class="objective-reading-grid"><article class="objective-panel"><h3>Local condition matters</h3><p>Discharge or active local pathology is not simply a memorized point contraindication; it calls for assessment of the site and appropriate care or referral.</p></article><article class="objective-panel"><h3>Method wording matters</h3><p>A source note about moxa or bleeding must remain attached to its named method and historical context.</p></article></div>
        <details class="application-prompt"><summary>Apply the objective</summary><p>Classify SJ 15 and an ear-area entry before comparing them: one is a pleural anatomical pattern; the other may involve local condition, movement or modality-specific wording.</p></details>
      </section>`,
    'objective-9': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> reduce the 32-item Urinary Bladder bank to four retrievable regional clusters while preserving point-level verification.</p>
        <div class="cluster-study-grid">
          <article><h3>Head · UB 1–7</h3><p>The bank repeatedly uses a moxa label. Because the region includes the eye and sensitive cranial structures, retain both regional anatomy and source-specific modality context.</p></article>
          <article><h3>Thoracic back · UB 11–21 and UB 41–50</h3><p>Pleura/lung and pneumothorax risk form the dominant repeated pattern across the inner and outer lines.</p></article>
          <article><h3>Lumbar back · UB 22–23 and UB 51–52</h3><p>The repeated family changes to kidney relationship. This is the main contrast with the thoracic clusters.</p></article>
          <article><h3>Distal context · UB 60</h3><p>Pregnancy is a patient-context category, not a pleural or kidney injury mechanism.</p></article>
        </div>
        <aside class="memory-rule"><b>Retrieval strategy:</b> draw two back lines. Shade thoracic levels as pleural, lumbar levels as kidney, then place head modality wording and distal pregnancy context outside those anatomical clusters.</aside>
        <aside class="common-error"><b>Common error:</b> memorizing a long run of point numbers without noticing the regional transition. Also remember that an omitted point is not automatically safe; this is a selected bank.</aside>
        <p class="anatomy-cross-reference"><b>Anatomy connection:</b> the source places the posterior pleural reflections approximately T1–T12 and the kidneys deep to the paravertebral muscles around T12–L3. This supports the bank’s transition from thoracic pleural clusters to lumbar kidney clusters. The complete anatomical map appears later in Objective 11.</p>
        <details class="application-prompt"><summary>Apply the objective</summary><p>Compare UB 20, UB 23 and UB 60. State why their answers belong respectively to thoracic anatomy, lumbar organ relationship and patient context.</p></details>
      </section>`,
    'objective-10': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> decide whether a statement concerns the patient, the local tissue, a named modality or a historical convention.</p>
        <div class="table-scroll"><table class="pattern-table"><thead><tr><th>Decision category</th><th>Examples represented in the six-item bank</th><th>Reasoning rule</th></tr></thead><tbody>
          <tr><td>Patient condition</td><td>Hunger, overeating, fatigue, nervousness, weakness and spontaneous bleeding.</td><td>Screen the person and determine whether treatment should be delayed, modified or referred according to current protocol.</td></tr>
          <tr><td>Pregnancy context</td><td>The bank rejects a blanket statement about all needling but preserves selected region or modality precautions.</td><td>Avoid both extremes: pregnancy is neither irrelevant nor a universal one-line prohibition.</td></tr>
          <tr><td>Local tissue</td><td>Infection, ulceration, scar or tumor.</td><td>Assess the site and the condition; do not treat a local pathology phrase as a generic channel rule.</td></tr>
          <tr><td>Named modality</td><td>Scarring moxibustion and pregnancy-related moxa wording.</td><td>Keep the restriction attached to the stated method and confirm the current standard.</td></tr>
        </tbody></table></div>
        <aside class="source-audit"><b>True/false discipline:</b> identify the absolute word first—“all,” “never,” or an unqualified “do not.” Many weak items are wrong because they erase patient assessment, region, timing or modality.</aside>
        <details class="application-prompt"><summary>Apply the objective</summary><p>Rewrite the blanket statement “pregnant patients cannot receive acupuncture” into a safer study statement that requires individual assessment, current pregnancy guidance, point or region review and practitioner scope.</p></details>
      </section>`,
    'objective-11': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> compare two warnings by mechanism, state which deserves immediate priority and explain what evidence is still missing.</p>
        <div class="table-scroll"><table class="pattern-table"><thead><tr><th>Comparison</th><th>First warning</th><th>Second warning</th><th>Discriminating question</th></tr></thead><tbody>
          <tr><td>Thorax vs abdomen</td><td>Pleura/lung relationship</td><td>Peritoneum, viscera or organ enlargement</td><td>Which cavity or organ lies beneath the region?</td></tr>
          <tr><td>Neural vs vascular</td><td>Spinal canal, brain or named nerve</td><td>Named artery or vein</td><td>What exact structure—not merely “danger”—is involved?</td></tr>
          <tr><td>Anatomy vs pregnancy</td><td>Direct local tissue relationship</td><td>Patient and obstetric context</td><td>Is the mechanism local anatomy or a condition that changes the decision?</td></tr>
          <tr><td>Modality vs universal rule</td><td>Restriction attached to moxa or another method</td><td>Claim applied to every treatment method</td><td>Does the complete source support broadening the restriction?</td></tr>
          <tr><td>Current vs historical</td><td>Current safety or emergency protocol</td><td>Classical or undated phrase</td><td>What are the source date, scope and current verification status?</td></tr>
        </tbody></table></div>
        <section class="visual-reading-guide"><h3>How to read the supplied anatomical illustrations</h3><div><article><span>ANTERIOR THORAX</span><p>The paired lungs occupy most of the rib cage, the heart projects centrally and to the left, and the inferior pleural reflections extend below the resting lung borders near the costal margins.</p></article><article><span>ANTERIOR ABDOMEN / PELVIS</span><p>The liver dominates the right upper abdomen, the spleen lies high on the left, the kidneys are posterior, and the bladder occupies the lower midline with position changing as it fills.</p></article><article><span>POSTERIOR TRUNK</span><p>The pleura descends through the thoracic back while the kidneys sit lower, deep to the paravertebral region around T12–L3. This visual transition supports thoracic-versus-lumbar classification.</p></article></div><p>These diagrams are regional orientation aids. They do not define a safe insertion route or replace individual anatomy, palpation, imaging, supervision or the complete point reference.</p></section>
        <details class="source-reading" open><summary>Source reading · “Needling — Depth of Needling” excerpt</summary><p>The supplied excerpt presents avoidance of injury as a fundamental principle and emphasizes competent training under expert supervision. For this guide, the passage supports recognition and escalation—not independent technique.</p><div class="objective-reading-grid"><article class="objective-panel"><h3>Possible pneumothorax</h3><p>The source lists chest pain or tightness, cough and shortness of breath. It also notes tachycardia, hypotension, excessive sweating or impaired consciousness in a substantial event.</p></article><article class="objective-panel"><h3>Time and urgency</h3><p>Effects may be delayed and appear hours after treatment. Suspected pneumothorax is described as a medical emergency requiring expert assistance.</p></article><article class="objective-panel"><h3>Abdominal distinction</h3><p>The source separates the abdominal cavity from thoracic risk and calls for additional judgment when body build, age or tissue depth changes anatomical relationships.</p></article><article class="objective-panel"><h3>Neurovascular distinction</h3><p>The source associates unexpected sharp pain or bleeding with possible vessel involvement and an electric sensation with possible direct nerve stimulation. These are recognition cues that require the current supervised safety protocol.</p></article></div><h3>Anatomical map summarized from the supplied excerpt</h3><div class="table-scroll"><table class="pattern-table anatomy-source-table"><thead><tr><th>Structure</th><th>Relationship described in the source</th><th>Study significance</th></tr></thead><tbody><tr><td>Pleura</td><td>Parietal pleura lines the thoracic cavity; visceral pleura covers each lung. The potential space between them is the pleural cavity.</td><td>Penetration of the pleural space may produce pneumothorax even without direct lung penetration.</td></tr><tr><td>Pleural extent</td><td>The source traces it from the supraclavicular region to about rib 8 at the mid-clavicular line, rib 10 at the mid-axillary line and rib 12 near the lateral erector-spinae border; posterior reflections run approximately T1–T12.</td><td>Thoracic risk includes chest, back and supraclavicular regions; it is not limited to the anterior chest.</td></tr><tr><td>Lung</td><td>The lung fills the pleural cavities toward the diaphragm. Its lower border changes with respiration and is normally above the pleural reflection.</td><td>Breathing changes internal relationships, while pleural space extends below the resting lung border.</td></tr><tr><td>Heart</td><td>The source places the cardiac outline roughly from the second to sixth intercostal spaces, crossing from the right parasternal area toward the left mammillary line and resting inferiorly on the diaphragm.</td><td>Cardiac enlargement can change relationships in the epigastric and lower anterior thoracic region.</td></tr><tr><td>Liver</td><td>Located beneath the diaphragm in the right subcostal area and extending across the epigastrium; enlargement may project below the right costal margin and farther across the epigastrium.</td><td>Laterality and organ enlargement matter when interpreting upper-abdominal warnings.</td></tr><tr><td>Spleen</td><td>Described beneath ribs 9–11 on the left, deep to the posterolateral rib cage; enlargement extends anteriorly and inferiorly.</td><td>A left upper-abdominal or lower-thoracic relationship can change substantially with enlargement.</td></tr><tr><td>Kidneys</td><td>Located on the posterior abdominal wall, deep to the paravertebral muscles, approximately from T12 to L3.</td><td>This explains the shift from thoracic pleural patterns to lumbar kidney relationships on the back.</td></tr><tr><td>Urinary bladder</td><td>Located behind the pubic symphysis; when full it may extend superiorly through the hypogastric area toward the level of Ren 4.</td><td>Bladder fullness is a changing organ-state factor, not a fixed point property.</td></tr><tr><td>Major vessels</td><td>The source flags points located over or near named vessels and describes unexpected sharp pain or bleeding as warning cues.</td><td>Identify the actual artery or vein and follow current assessment and bleeding-response protocols.</td></tr><tr><td>Major nerves</td><td>The source flags points located over or near named nerves and treats an electric sensation as possible direct nerve stimulation.</td><td>Distinguish neural from vascular warning signs and use the current supervised protocol.</td></tr></tbody></table></div><p class="source-note"><b>Technique boundary:</b> the excerpt also discusses procedural direction, reinsertion and timed bleeding-management steps. Those instructions are intentionally not reproduced here and require formal supervised clinical training and the complete current protocol.</p></details>
        <div class="case-grid"><article><h3>Case A</h3><p>A thoracic point is being considered for a pregnant patient. Record the pleural anatomical family and the pregnancy context separately; one label does not replace the other.</p></article><article><h3>Case B</h3><p>An ear-area entry mentions discharge and moxa. Separate local pathology from the named modality and verify both.</p></article><article><h3>Case C</h3><p>A legacy chart says “contraindicated” without explanation. The missing mechanism and source status become part of the answer.</p></article></div>
        <aside class="memory-rule"><b>Priority rule:</b> classify first, identify the structure or context second, verify the source third, then state the safe next action within scope.</aside>
      </section>`,
    'objective-12': `
      <section class="objective-development">
        <p class="objective-outcome"><b>Learning outcome:</b> produce a concise, auditable safety note for an unfamiliar case using the full SAFER sequence.</p>
        <div class="integrated-case-template">
          <article><span>S</span><div><h3>Screen</h3><p>Record relevant patient factors, pregnancy status, local pathology, symptoms and the modality under consideration.</p></div></article>
          <article><span>A</span><div><h3>Anatomy</h3><p>Name the vulnerable structure or regional relationship. Avoid a vague label such as “dangerous point.”</p></div></article>
          <article><span>F</span><div><h3>Find the source</h3><p>Identify the complete point reference, current safety guidance and whether the course-bank phrase is historical, conditional or disputed.</p></div></article>
          <article><span>E</span><div><h3>Explain</h3><p>State the mechanism, the limits of the evidence and why the closest alternative classification is weaker.</p></div></article>
          <article><span>R</span><div><h3>Respond safely</h3><p>Document the next step within training and scope: pause, obtain supervision, use the current protocol or refer appropriately.</p></div></article>
        </div>
        <section class="worked-case"><h3>Worked reasoning example</h3><p><b>Scenario:</b> an undated chart gives a short contraindication for a thoracic point, while the patient also reports dizziness before treatment.</p><p><b>Documented interpretation:</b> the thoracic location raises a pleura/lung anatomical category; dizziness is a separate patient or adverse-response concern; the undated phrase requires complete-source verification. The safe next step is not derived from the short chart alone—the current screening and clinical protocol controls the response.</p></section>
        <div class="objective-reading-grid"><article class="objective-panel"><h3>Minimum evidence</h3><ul><li>All five SAFER headings completed.</li><li>At least one complete source identified.</li><li>One close alternative explained.</li><li>A next action kept within scope.</li></ul></article><article class="objective-panel"><h3>Self-check</h3><ul><li>Did I separate anatomy from patient context?</li><li>Did I preserve the named modality?</li><li>Did I flag historical or conflicting wording?</li><li>Did I avoid procedural claims beyond training?</li></ul></article></div>
      </section>`
  };

  Object.entries(units).forEach(([id, html]) => {
    const objective = document.getElementById(id);
    const body = objective?.querySelector(':scope > div');
    if (!body || body.querySelector('.objective-development')) return;
    const template = document.createElement('template');
    template.innerHTML = html.trim();
    const insertBefore = body.querySelector('.source-reading') || body.querySelector('.source-note') || body.querySelector('.objective-actions');
    if (insertBefore) body.insertBefore(template.content, insertBefore);
    else body.append(template.content);
  });

  const sourceChart = document.getElementById('source-chart');
  if (sourceChart && !document.getElementById('board-study-strategy')) {
    const strategy = document.createElement('section');
    strategy.id = 'board-study-strategy';
    strategy.className = 'card board-study-strategy';
    strategy.innerHTML = `
      <span class="eyebrow">HIGH-YIELD BOARD STUDY STRATEGY</span>
      <h2>Study the risk pattern before the point list</h2>
      <p>Use regional anatomy and source context to organize recall. A mnemonic is a retrieval cue—not a substitute for the complete point entry, current safety guidance or supervised clinical training.</p>
      <div class="strategy-grid">
        <article><span>1</span><h3>Group by region</h3><p>Build separate maps for ocular/facial, thoracic, abdominal/pelvic, posterior spinal/cranial and major neurovascular regions.</p></article>
        <article><span>2</span><h3>Name the mechanism</h3><p>Ask whether the warning concerns vulnerable anatomy, patient context, pregnancy, local pathology, a named modality or historical wording.</p></article>
        <article><span>3</span><h3>Compare close alternatives</h3><p>Explain why pleura is not peritoneum, why a nerve is not a vessel, and why pregnancy context is not a direct tissue-injury mechanism.</p></article>
        <article><span>4</span><h3>Verify the source</h3><p>Short banks may preserve legacy language or errors. Check the full entry before treating a phrase as a current universal rule.</p></article>
      </div>
      <section class="mnemonic-section"><div><span class="mnemonic-code">V · O · P · N</span><h3>Four high-yield answer families</h3><ul><li><b>V · Vessels:</b> named major arteries or veins.</li><li><b>O · Organs and cavities:</b> pleura, lungs, heart, liver, spleen, kidneys, bladder and peritoneal relationships.</li><li><b>P · Pregnancy and patient context:</b> conditional factors requiring individual assessment and current guidance.</li><li><b>N · Nerves and neural axis:</b> peripheral nerves, spinal canal and cranial structures.</li></ul></div><div><span class="mnemonic-code">R · E · D</span><h3>Thoracic-risk recognition</h3><ul><li><b>R · Region:</b> chest, upper back and supraclavicular areas can share pleural risk.</li><li><b>E · Emergency signs:</b> chest pain or tightness, cough, shortness of breath, tachycardia, hypotension, sweating or impaired consciousness.</li><li><b>D · Delayed onset:</b> symptoms may appear hours later; suspected pneumothorax requires emergency evaluation.</li></ul></div></section>
      <section class="mnemonic-section"><div><span class="mnemonic-code">P · P · B</span><h3>Deep-abdominal recognition</h3><ul><li><b>P · Peritoneal cavity:</b> identify the cavity and the nearby hollow or solid organs rather than memorizing “abdomen” as one risk.</li><li><b>P · Pregnancy context:</b> treat gestation as a separate patient factor requiring current, point-specific obstetric guidance.</li><li><b>B · Bladder state:</b> a full bladder can rise above the pubic symphysis into the lower abdomen, so organ state changes the anatomical relationship.</li></ul></div><div><span class="mnemonic-code">V · A · N</span><h3>Vascular and neural recognition</h3><ul><li><b>V · Venous pattern:</b> slower, darker bleeding is a source-described recognition cue.</li><li><b>A · Arterial pattern:</b> faster, brighter-red bleeding is a source-described recognition cue.</li><li><b>N · Neural warning:</b> an unexpected electric shock-like sensation can indicate direct nerve stimulation.</li></ul><p class="mnemonic-boundary">These cues identify a possible event; they do not determine treatment. Pause, assess and follow the current supervised adverse-event protocol.</p></div></section>
      <section class="landmark-snapshots"><h3>High-yield landmark snapshots</h3><div><article><span>THORAX · ST 12 + INTERCOSTAL REGIONS</span><p>Recognize the pleural cavity and lung throughout the chest, upper back and supraclavicular region. ST 12 also requires awareness of major subclavian neurovascular structures.</p></article><article><span>ORBIT · BL 1 + ST 1</span><p>Classify these as advanced orbital-risk points involving the globe, optic structures and local vessels—not as ordinary facial points.</p></article><article><span>POSTERIOR CRANIUM · DU 15 + DU 16</span><p>Connect the lower occipital region with the foramen magnum, medulla and upper spinal canal. The danger family is central neural anatomy.</p></article><article><span>LOWER MIDLINE · REN 3–5</span><p>Separate fixed regional anatomy from changing bladder state and pregnancy context. A full bladder may extend above the pubic symphysis into the lower abdomen.</p></article><article><span>MOXA · NAMED MODALITY</span><p>Keep a moxibustion restriction attached to the named modality. Thin or sensitive skin, superficial vessels, local pathology and historical point lists require separate source checks.</p></article></div><p class="landmark-boundary">Recognition only: exact positioning, insertion direction and depth belong to the complete current point reference, formal supervised training and the patient-specific protocol.</p></section>
      <section class="pregnancy-study"><h3>Pregnancy trigger group · H-S-J-Z</h3><p><b>Hegu LI 4 · Sanyinjiao SP 6 · Jianjing GB 21 · Zhiyin BL 67</b></p><p>These are frequently cited classical and examination cautions. Use the group to trigger a pregnancy-source check—not to replace individual obstetric assessment, the complete point entry, current jurisdictional guidance or practitioner scope.</p></section>
      <aside class="study-warning"><b>Avoid an oversimplified absolute/relative list.</b><span>Some restrictions are current and categorical; others depend on pregnancy stage, patient condition, local anatomy, modality, source tradition or the wording of a particular examination bank. Always state which kind of rule you are using.</span></aside>
      <section class="pade-source-check"><div><span class="mnemonic-code">P · A · D · E</span><h3>Use it as a source check—not a technique recipe</h3><p>PADE can help locate missing information inside the SAFER process, but it must not supply an insertion decision by itself.</p></div><ol><li><b>P · Position and patient</b><span>Confirm posture, body habitus, pregnancy, symptoms and local condition in the complete protocol.</span></li><li><b>A · Angle and direction</b><span>Verify point-specific technique only in the current full reference and supervised training.</span></li><li><b>D · Depth</b><span>Never infer or transfer a cun value from a mnemonic, neighboring point or regional shortcut.</span></li><li><b>E · Exclusions</b><span>Determine whether wording is current, conditional, modality-specific, jurisdiction-specific or historical.</span></li></ol></section>
      <section class="safer-reminder"><h3>Keep one consistent SAFER method</h3><ol><li><b>Screen</b><span>patient and local factors</span></li><li><b>Anatomy</b><span>structure or regional relationship</span></li><li><b>Find</b><span>the complete current source</span></li><li><b>Explain</b><span>mechanism and limits</span></li><li><b>Respond</b><span>safely within scope</span></li></ol></section>`;
    sourceChart.before(strategy);

    const practiceNav = document.querySelector('.study-nav details:last-of-type');
    const memoryLink = practiceNav?.querySelector('a[href="#flashcards"]');
    if (practiceNav && memoryLink) {
      const link = document.createElement('a');
      link.href = '#board-study-strategy';
      link.textContent = 'Board study strategy';
      practiceNav.insertBefore(link, memoryLink);
    }
  }

  const integratedSequence = document.querySelector('#objective-12 > div > .sequence');
  if (integratedSequence) {
    integratedSequence.classList.add('safer-sequence');
    integratedSequence.innerHTML = '<li><b>Screen</b><span>Identify patient and local factors.</span></li><li><b>Anatomy</b><span>Name the structure or regional relationship.</span></li><li><b>Find</b><span>Consult the complete and current source.</span></li><li><b>Explain</b><span>State the rationale and evidence limits.</span></li><li><b>Respond</b><span>Document a safe next step within scope.</span></li>';
  }

  const referenceList = document.querySelector('#references ul');
  if (referenceList && !referenceList.querySelector('[data-depth-source]')) {
    const item = document.createElement('li');
    item.dataset.depthSource = 'true';
    item.innerHTML = '<b>Deadman, Al-Khafaji &amp; Baker.</b> <i>A Manual of Acupuncture.</i> “Point location and needling,” supplied PDF scan pages 64–67. Used here for regional anatomy, pneumothorax recognition, abdominal caution and major-organ relationships; procedural directions are outside this guide’s scope.';
    referenceList.append(item);
  }
})();
