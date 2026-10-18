(() => {
  'use strict';

  const root = document.getElementById('channels-objective-1');
  if (!root || root.dataset.developed === 'functions-v1') return;
  root.dataset.developed = 'functions-v1';

  const make = (tag, text, parent, className) => {
    const node = document.createElement(tag);
    if (text) node.textContent = text;
    if (className) node.className = className;
    if (parent) parent.append(node);
    return node;
  };
  const reset = (selector, heading) => {
    const section = root.querySelector(selector);
    section.replaceChildren();
    make('h4', heading, section);
    return section;
  };

  const introduction = reset('.objective-introduction', 'Introduction and Understanding the Objective');
  make('p','What will you study? The four functions attributed to the channel and collateral network in the source: integration and transport, protection, response to dysfunction, and transmission of therapeutic stimulation.',introduction);
  make('p','Why does it matter? These functions explain why channel theory links zangfu, tissues, sense organs, body regions and treatment responses within one traditional physiological model.',introduction);
  make('p','What will you learn to do? Identify each function, distinguish it from the others and use a short clinical example to explain the relationship being described.',introduction);
  const board = make('div','',introduction,'board-connection-objective');
  const boardButton = make('button','How does this connect to the Board?',board,'board-modal-trigger');
  boardButton.type = 'button';
  boardButton.setAttribute('aria-haspopup','dialog');
  boardButton.setAttribute('aria-controls','channels-objective-1-board-dialog');

  const boardDialog = make('dialog','',document.body,'channels-board-dialog');
  boardDialog.id = 'channels-objective-1-board-dialog';
  boardDialog.setAttribute('aria-labelledby','channels-objective-1-board-title');
  const boardPanel = make('div','',boardDialog,'channels-board-dialog-panel');
  const boardTitle = make('h3','The Functions of the Channels — where this fits in the outline',boardPanel);
  boardTitle.id = 'channels-objective-1-board-title';
  make('p','Follow these four levels to understand where this objective fits:',boardPanel);
  const boardPath = make('ol','',boardPanel,'channels-board-path');
  [
    ['Foundational theory','Understand the concepts used to organize the body in Chinese medicine.'],
    ['Channels and collaterals','Recognize the systems that form the channel network.'],
    ['Functions of the channel network','Distinguish what the network is described as doing.'],
    ['Four functions','Identify transport and integration, protection, response to dysfunction, and transmission of therapeutic stimulation.']
  ].forEach(([label,description]) => {
    const item = make('li','',boardPath);
    make('strong',label,item);
    make('p',description,item);
  });
  make('p','Use this hierarchy as a study map. The fourth level identifies this lesson topic; it does not assign an official examination-outline code.',boardPanel);
  make('h5','What to recognize',boardPanel);
  make('p','Identify which of the four functions is described by the question before considering a pathway, organ relationship or treatment method.',boardPanel);
  make('h5','Common trap',boardPanel);
  make('p','Pain along a channel does not by itself prove disease of the related zangfu. Also distinguish ongoing transport of Qi and Blood from the response attributed to deliberate therapeutic stimulation.',boardPanel);
  make('h5','Practice approach',boardPanel);
  make('p','Underline the main cue: nourishment and connection, exterior protection, manifestation or transmission of dysfunction, or response to stimulation. Then explain why the remaining functions do not match.',boardPanel);
  make('p','Board-style study connection; these examples are not official examination questions or claims about question frequency.',boardPanel,'channels-board-scope');
  const boardClose = make('button','← Return to the objective',boardPanel,'channels-board-close');
  boardClose.type = 'button';
  const openBoardDialog = () => {
    if (typeof boardDialog.showModal === 'function') boardDialog.showModal();
    else boardDialog.setAttribute('open','');
  };
  const closeBoardDialog = () => {
    if (typeof boardDialog.close === 'function') boardDialog.close();
    else boardDialog.removeAttribute('open');
    boardButton.focus();
  };
  boardButton.addEventListener('click',openBoardDialog);
  boardClose.addEventListener('click',closeBoardDialog);
  boardDialog.addEventListener('click',event => {
    if (event.target === boardDialog) closeBoardDialog();
  });

  const development = reset('.objective-development','Explore the Topic — Four Functions of the Channels');
  const overview = make('table','',development,'functions-overview-table');
  overview.innerHTML = '<caption>Four functions — recognition map</caption><thead><tr><th>Function</th><th>Central question</th><th>Recognition cue</th></tr></thead><tbody><tr><th>1. Transport and integration</th><td>How does the body act as an interconnected whole?</td><td>Qi and Blood reach and connect zangfu, tissues, sense organs, interior/exterior, left/right and above/below.</td></tr><tr><th>2. Protection</th><td>How is exterior penetration described?</td><td>Successively deeper layers may contain—or fail to contain—an external pathogenic factor.</td></tr><tr><th>3. Response to dysfunction</th><td>How can disorder appear or spread through the network?</td><td>Channel disease, zangfu reflection, transmission between systems and visible change.</td></tr><tr><th>4. Transmission of stimulation</th><td>How can treatment at a point influence another region?</td><td>Needling, heat, pressure, massage or cupping regulates Qi and Blood along the related network.</td></tr></tbody>';

  const topics = [
    ['1. Transporting Qi and Blood and rendering the body an integrated whole','The interwoven network distributes Qi and Blood so that zangfu, tissues and sense organs are nourished, energized and warmed. The source uses a plant analogy: the zangfu are roots, the channels are stems and the tissues or sense organs are flowers. The Heart “flowers” into the tongue.','Integration occurs in several directions: channels connect zangfu with one another, link the interior with the exterior, relate the two sides of the body and connect above with below. The Lung Primary Channel, for example, is described as originating in the Stomach region and connecting with the Large Intestine; the Heart Primary Channel connects with the Small Intestine and Lung.'],
    ['2. Protecting the body','Different channel systems occupy different functional depths. The source describes the channel network as a series of barriers that may contain an exterior pathogenic factor and limit deeper penetration.','A stiff neck after exposure to wind and cold illustrates a relatively superficial presentation involving local stagnation. Persistent or strong pathogenic influence, or weakened resistance, is described as allowing deeper involvement of joints, sinews, bones or zangfu. “Exterior” and “interior” here are technical concepts within Chinese medicine.'],
    ['3. Responding to dysfunction in the body','The network can show dysfunction in four ways: the channel itself may be affected; zangfu disharmony may appear along a related channel; disorder may transmit through channel relationships; or visible changes may appear along a pathway.','Channel disease may produce pain, weakness, distention, numbness or tingling. Examples of reflection include Heart Blood stasis with pain along the Heart pathway, Liver Qi stagnation affecting Liver or Gall Bladder regions, Stomach Heat manifesting in the mouth, and Liver Fire affecting the eyes. Visible purple, red or pale changes are traditionally associated with Blood stasis, Heat or deficiency.'],
    ['4. Transmitting stimulation to a diseased area','Channel theory provides the traditional explanation for how stimulation at an acupuncture point may influence a related region or function.','The source includes needling, heat, pressure, massage and cupping as forms of stimulation. This function concerns therapeutic transmission and regulation; it should not be confused with the transport of Qi and Blood described in Function 1.']
  ];
  topics.forEach(([title,main,example]) => {
    const card = make('article','',development,'function-learning-card');
    make('h5',title,card);
    make('p',main,card);
    const detail = make('details','',card);
    make('summary','Explore the example and distinction',detail);
    make('p',example,detail);
  });
  make('p','Reference: Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Functions of the Channels.” The protection sequence is discussed there with a passage attributed to Essential Questions. Terminology is summarized for study rather than reproduced as a complete clinical protocol.',development,'source-note');

  const visual = reset('.objective-visual','Visual Learning — Three Ways to Read the Network');
  const visuals = make('div','',visual,'channel-functions-visual-grid');
  const integration = make('figure','',visuals,'concept-diagram');
  integration.innerHTML = '<figcaption>Integration map</figcaption><div class="root-node">Zangfu<br><small>roots</small></div><div class="diagram-arrow">↓ Qi &amp; Blood ↓</div><div class="stem-node">Channels<br><small>stems</small></div><div class="diagram-arrow">↓ nourishment ↓</div><div class="flower-node">Tissues &amp; sense organs<br><small>flowers</small></div>';
  const depth = make('figure','',visuals,'concept-diagram depth-diagram');
  depth.innerHTML = '<figcaption>Conceptual progression from surface toward depth</figcaption><ol><li>Skin and body hair</li><li>Minute collaterals</li><li>Luo-Connecting Channels</li><li>Primary Channels</li><li>Zangfu and bowels</li></ol><p><small>A learning model from the cited passage—not a literal anatomical cross-section or an inevitable disease sequence.</small></p>';
  const response = make('figure','',visuals,'concept-diagram');
  response.innerHTML = '<figcaption>Four responses to dysfunction</figcaption><ul><li>Channel itself becomes diseased</li><li>Zangfu disharmony reflects onto a channel</li><li>Disease transmits through channel relationships</li><li>The pathway shows visible change</li></ul>';
  const visualQuestion = make('details','',visual,'visual-curiosity-question');
  make('summary','Why is therapeutic transmission shown separately from circulation?',visualQuestion);
  make('p','Circulation describes the continuing transport and integration of Qi and Blood. Therapeutic transmission describes the response attributed to deliberate stimulation at a point or region. They use the same network but answer different questions.',visualQuestion);

  const flashcards = reset('.objective-flashcards','Flashcards — Guided Recall');
  const cards = [
    ['Which function explains nourishment of zangfu, tissues and sense organs?','Transporting Qi and Blood and rendering the body an integrated whole.'],
    ['What does the roots–stems–flowers analogy represent?','Zangfu are compared with roots, channels with stems, and tissues or sense organs with flowers.'],
    ['Which function is illustrated by progressively deeper penetration of an exterior pathogenic factor?','Protecting the body.'],
    ['What are the four ways channels may respond to dysfunction?','Disease of the channel itself; zangfu disharmony reflecting onto the channel; transmission through the network; and visible changes along a pathway.'],
    ['Does pain along a channel always prove disease of the related zangfu?','No. The source distinguishes disease of the channel itself from zangfu disharmony reflected onto a channel.'],
    ['Which function explains the effect attributed to needling, heat, pressure, massage or cupping?','Transmission of stimulation through the channel network to the affected area.']
  ];
  cards.forEach(([question,answer],index) => {
    const card = make('details','',flashcards,'guided-flashcard');
    make('summary',`Flashcard ${index + 1} — ${question}`,card);
    make('p',answer,card);
  });

  const selfTest = reset('.objective-self-test','Self-Test — Practice without score');
  make('p','Answer in this order: Multiple Choice → Matching → Fill in the Blank → Visual Identification. Check the explanation only after choosing an answer.',selfTest);
  const questions = [
    ['A patient has localized aching and tingling after a lumbar strain. Which response is illustrated most directly?',['Disease of the channel itself','Protection from an exterior pathogen','Visible channel discoloration','Therapeutic transmission'],0,'The example begins with local impairment of Qi and Blood after trauma, so it illustrates disease of the channel itself.'],
    ['Red, painful eyes attributed to Liver Fire illustrate which relationship?',['Zangfu disharmony reflecting onto a related channel and sense organ','The minute collaterals preventing penetration','The Girdling vessel linking both sides','A Sinew Channel transmitting treatment'],0,'The example describes an internal Liver disharmony manifesting through its channel relationship with the eyes.'],
    ['Which statement best distinguishes transport from therapeutic transmission?',['Transport concerns ongoing circulation and integration; therapeutic transmission concerns the response to deliberate stimulation.','Transport occurs only in the limbs; therapeutic transmission occurs only in the trunk.','They are two names for exactly the same function.','Therapeutic transmission is a Five Shu category.'],0,'Both involve the channel network, but they answer different questions: continuing physiological transport versus response to treatment stimulation.']
  ];
  make('h5','1. Multiple Choice',selfTest);
  questions.forEach(([stem,options,correct,explanation],qIndex) => {
    const fieldset = make('fieldset','',selfTest,'interactive-mc-question');
    make('legend',`${qIndex + 1}. ${stem}`,fieldset);
    const radios = options.map((option,index) => {
      const label = make('label','',fieldset,'chapter-practice-option');
      const input = make('input','',label);
      input.type = 'radio'; input.name = `channel-function-q-${qIndex}`; input.value = String(index);
      make('span',`${String.fromCharCode(65 + index)}. ${option}`,label);
      return input;
    });
    const button = make('button','Check answer and explanation',fieldset);
    button.type = 'button';
    const feedback = make('div','',fieldset,'mc-answer-feedback'); feedback.hidden = true;
    button.addEventListener('click',() => {
      const selected = radios.find(radio => radio.checked);
      feedback.hidden = false; feedback.replaceChildren();
      if (!selected) { make('p','Choose one answer before checking.',feedback); return; }
      make('p',Number(selected.value) === correct ? 'Correct.' : `Review this distinction. Answer: ${options[correct]}.`,feedback);
      make('p',explanation,feedback);
    });
  });

  make('h5','2. Matching',selfTest);
  const matching = make('div','',selfTest,'functions-matching');
  const matches = [
    ['Nourishing tissues and sense organs','Transport and integration'],
    ['Containing exterior pathogenic factors','Protection'],
    ['Pain or discoloration along a pathway','Response to dysfunction'],
    ['Effect attributed to needling or pressure','Transmission of stimulation']
  ];
  const matchOptions = ['Choose a function','Transport and integration','Protection','Response to dysfunction','Transmission of stimulation'];
  matches.forEach(([cue,answer]) => {
    const label = make('label','',matching,'chapter-matching-row');
    make('span',cue,label);
    const select = make('select','',label); select.dataset.answer = answer;
    matchOptions.forEach(option => { const item = make('option',option,select); item.value = option === 'Choose a function' ? '' : option; });
  });
  const matchButton = make('button','Check matching answers',selfTest); matchButton.type='button';
  const matchResult = make('p','',selfTest); matchResult.setAttribute('role','status');
  matchButton.onclick=()=>{const selects=[...matching.querySelectorAll('select')];matchResult.textContent=selects.some(s=>!s.value)?'Complete all four matches first.':selects.every(s=>s.value===s.dataset.answer)?'Correct. Each cue identifies a different channel function.':'Review the four-function recognition map and try again.';};

  make('h5','3. Fill in the Blank',selfTest);
  const fill = make('p','',selfTest,'fill-blank-line');
  fill.append('In the plant analogy, the zangfu are the ');
  const roots = make('input','',fill); roots.type='text'; roots.setAttribute('aria-label','Zangfu analogy');
  fill.append(', the channels are the ');
  const stems = make('input','',fill); stems.type='text'; stems.setAttribute('aria-label','Channels analogy');
  fill.append(', and the tissues and sense organs are the ');
  const flowers = make('input','',fill); flowers.type='text'; flowers.setAttribute('aria-label','Tissues and sense organs analogy');
  fill.append('.');
  const fillButton=make('button','Check the analogy',selfTest);fillButton.type='button';const fillResult=make('p','',selfTest);fillResult.setAttribute('role','status');
  fillButton.onclick=()=>{const values=[roots.value,stems.value,flowers.value].map(v=>v.trim().toLowerCase());fillResult.textContent=values.every(Boolean)?(values[0]==='roots'&&values[1]==='stems'&&values[2]==='flowers'?'Correct: roots → stems → flowers.':'Review the visual integration map and try again.'):'Complete all three spaces first.';};

  make('h5','4. Visual Identification',selfTest);
  make('p','Without reopening the explanation, arrange the conceptual protection sequence from the body surface toward greater depth.',selfTest);
  const sequence = make('ol','',selfTest,'visual-sequence-task');
  ['Skin and body hair','Minute collaterals','Luo-Connecting Channels','Primary Channels','Zangfu and bowels'].forEach(item => make('li',item,sequence));
  const sequenceAnswer=make('details','',selfTest);make('summary','Check the sequence and its limitation',sequenceAnswer);make('p','The displayed order is the sequence summarized from the cited passage. It is a conceptual learning model, not a literal anatomical cross-section and not a claim that every illness must progress through every stage.',sequenceAnswer);

  const continuing = reset('.objective-continuing-education','In-depth Reading — Continuing Education');
  make('h5','From network metaphor to clinical reasoning',continuing);
  make('p','The source presents the channels as a functional network joining structures that might otherwise be studied separately. The roots–stems–flowers analogy emphasizes relationship: zangfu provide the internal basis, channels distribute influence, and tissues or sense organs express that relationship.',continuing);
  make('h5','Why depth matters—but should not be oversimplified',continuing);
  make('p','The protection model moves from surface structures toward deeper systems. It supports reasoning about relative exterior/interior involvement and severity. It should not be converted into a rigid anatomical ladder or assumed to describe the course of every disease.',continuing);
  make('h5','Traditional language and biomedical interpretation',continuing);
  make('p','Qi, Blood, channels, Wind, Cold, Damp, Heat, Fire and Dryness are technical concepts in Chinese medicine. They cannot be translated one-to-one as nerves, vessels, microbes, temperature or weather exposure. Modern anatomy may help explain tissues encountered in a region, but it does not by itself validate or replace the traditional network model.',continuing);
  make('h5','Clinical reasoning boundary',continuing);
  make('p','A channel sign is evidence to interpret, not a complete diagnosis. Local tenderness may reflect local trauma or overuse; a zangfu pattern requires a broader constellation of findings. Treatment selection therefore depends on pattern differentiation, examination, safety and scope of practice—not on one pathway symptom alone.',continuing);
  make('h5','Comparing channel systems by functional level',continuing);
  make('p','As a supplementary teaching model, Jeffrey C. Yuen compares channel systems through Wei, Ying and Yuan levels: Sinew Channels are associated with the exterior or Wei level, Luo vessels with the internal or Ying level, and the Eight Extraordinary Vessels with the constitutional or Yuan level. Divergent Channels are described as communicating between exterior and constitutional levels. This framework helps compare systems, but it is a source-specific model rather than a literal anatomical layering of the body.',continuing);
  make('p','Reference: Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Functions of the Channels.”',continuing,'source-note');
  make('p','Supplementary perspective: Jeffrey C. Yuen, Channel Systems of Chinese Medicine: Luo Vessels, New England School of Acupuncture Continuing Education Department, 2004, “Role Within the Meridian System.”',continuing,'source-note');
  continuing.remove();

  const instructorResource = reset('.objective-instructor-resource','Instructor Resource');
  instructorResource.hidden = true;
  const scoredExam = reset('.objective-exam','Exam (with score)');
  make('p','Choose when to take these scored exams: after studying this objective, or after completing the reading for all objectives in the chapter. You can return to this objective through Study Navigation and use the same exam buttons.',scoredExam,'objective-exam-guidance');
  const functionsExamLink = make('a','Functions of the Channels (Mock 10)',scoredExam,'channels-scored-exam-link');
  functionsExamLink.href = 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9342';
  functionsExamLink.target = '_blank';
  functionsExamLink.rel = 'noopener noreferrer';
  make('p','The following chapter exam reviews relative depth across the channel systems; it is not limited to the four functions in this objective.',scoredExam,'sgv2-muted');
  const examLink = make('a','Order of the Channels — Most Superficial to Deepest (Sequence 1)',scoredExam,'channels-scored-exam-link');
  examLink.href = 'https://www.acupuncturetests.com/mod/scorm/view.php?id=1215';
  examLink.target = '_blank';
  examLink.rel = 'noopener noreferrer';

  const style = make('style','',document.head);
  style.textContent = `
    #channels-objective-1 .functions-overview-table{width:100%;border-collapse:collapse;margin:14px 0}
    #channels-objective-1 .functions-overview-table th,#channels-objective-1 .functions-overview-table td{padding:11px;border:1px solid #cbdedd;text-align:left;vertical-align:top}
    #channels-objective-1 .function-learning-card{padding:16px;margin:14px 0;border-left:4px solid #087774;background:#f4faf9;border-radius:9px}
    #channels-objective-1 .function-learning-card h5{margin-top:0;color:#075f60}
    #channels-objective-1 .board-connection-objective{margin:16px 0}
    #channels-objective-1 .board-modal-trigger{display:inline-flex;align-items:center;min-height:44px;padding:10px 17px;border:2px solid #df681c;border-radius:10px;background:#ef7e2d;color:#17232a;font-weight:800;cursor:pointer;box-shadow:0 4px 10px rgba(193,88,20,.18)}
    #channels-objective-1 .board-modal-trigger::before{content:'▸';margin-right:8px}
    .channels-board-dialog{width:min(780px,calc(100vw - 32px));max-height:86vh;margin:auto;padding:0;border:1px solid #9ebdbc;border-radius:14px;background:#fff;color:#122b3a;box-shadow:0 18px 60px rgba(0,32,45,.35);overflow:auto}
    .channels-board-dialog::backdrop{background:rgba(20,43,50,.64)}
    .channels-board-dialog-panel{padding:24px 20px 18px}
    .channels-board-dialog-panel h3{margin:0 0 18px;color:#082f42;font-size:1.35rem}
    .channels-board-dialog-panel h5{margin:22px 0 8px;color:#082f42}
    .channels-board-dialog-panel>p{line-height:1.55}
    .channels-board-path{margin:14px 0 16px;padding-left:28px}
    .channels-board-path li{margin:8px 0;padding:11px 12px;border-left:4px solid #078684;border-radius:6px;background:#edf7f6}
    .channels-board-path li strong{display:block;margin-bottom:5px}
    .channels-board-path li p{margin:0;line-height:1.45}
    .channels-board-scope{font-size:.82rem}
    .channels-board-close{min-height:42px;margin-top:12px;padding:9px 15px;border:2px solid #fff;border-radius:10px;outline:2px solid #168b9d;background:#ff7f22;color:#17232a;font-weight:800;cursor:pointer}
    .channels-board-close:hover,.channels-board-close:focus-visible{background:#ed6d11}
    .channel-functions-visual-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
    .concept-diagram{margin:0;padding:16px;border:1px solid #bcd9de;border-radius:12px;background:#fff;text-align:center}
    .concept-diagram figcaption{font-weight:800;margin-bottom:14px;color:#174d62}
    .concept-diagram .root-node,.concept-diagram .stem-node,.concept-diagram .flower-node{padding:12px;border-radius:9px;background:#e7f4ef}
    .concept-diagram .diagram-arrow{padding:8px;color:#a65017;font-weight:700}
    .concept-diagram ul,.concept-diagram ol{text-align:left;padding-left:22px}
    #channels-objective-1 .guided-flashcard{padding:10px 14px;margin:10px 0;border:1px solid #cbdedd;border-radius:9px;background:#f8fbfb}
    #channels-objective-1 fieldset{margin:16px 0;padding:14px;border:1px solid #cbdedd;border-radius:10px}
    #channels-objective-1 fieldset legend{font-weight:700}
    #channels-objective-1 .chapter-practice-option{display:flex;gap:8px;padding:7px 0}
    #channels-objective-1 .chapter-matching-row{display:grid;grid-template-columns:minmax(220px,1fr) minmax(220px,1fr);gap:12px;align-items:center;margin:9px 0}
    #channels-objective-1 select,#channels-objective-1 input[type=text]{min-height:42px;max-width:100%}
    #channels-objective-1 .fill-blank-line input{width:120px;margin:5px}
    #channels-objective-1 button{margin:8px 8px 8px 0}
    #channels-objective-1 .objective-exam-guidance{margin:14px 0 18px;padding:14px 16px;border-left:4px solid #ff7f16;background:#fff3e7;border-radius:8px;line-height:1.55}
    #channels-objective-1 .channels-scored-exam-link{display:inline-flex;align-items:center;min-height:48px;margin:10px 0;padding:10px 18px;border-radius:11px;background:#ff7f16;color:#17232a;font-weight:800;text-decoration:none;box-shadow:0 4px 10px rgba(193,88,20,.18)}
    #channels-objective-1 .channels-scored-exam-link:hover,#channels-objective-1 .channels-scored-exam-link:focus-visible{background:#ed6d11;text-decoration:underline}
    @media(max-width:780px){.channel-functions-visual-grid{grid-template-columns:1fr}#channels-objective-1 .chapter-matching-row{grid-template-columns:1fr}}
  `;
})();
