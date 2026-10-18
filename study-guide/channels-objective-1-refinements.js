(() => {
  'use strict';

  const root = document.getElementById('channels-objective-1');
  if (!root || root.dataset.refined === 'v1') return;
  root.dataset.refined = 'v1';

  const flashcardSection = root.querySelector('.objective-flashcards');
  const originalCards = [...root.querySelectorAll('.guided-flashcard')];
  if (flashcardSection && originalCards.length) {
    const grid = document.createElement('div');
    grid.className = 'channel-function-flashcard-grid';
    originalCards.forEach((original, index) => {
      const rawQuestion = original.querySelector('summary')?.textContent || '';
      const question = rawQuestion.replace(/^Flashcard\s+\d+\s*[—-]\s*/i,'').trim();
      const answerText = original.querySelector('p')?.textContent || '';
      const card = document.createElement('article');
      card.className = 'channel-function-flashcard';
      const number = document.createElement('p');
      number.className = 'flashcard-number-badge';
      number.textContent = `Flashcard ${index + 1}`;
      const heading = document.createElement('h5');
      heading.textContent = question;
      const show = document.createElement('button');
      show.type = 'button';
      show.className = 'flashcard-show-button';
      show.textContent = 'Show answer';
      show.setAttribute('aria-expanded','false');
      const answer = document.createElement('div');
      answer.className = 'flashcard-revealed-answer';
      answer.hidden = true;
      const answerLabel = document.createElement('strong');
      answerLabel.textContent = 'Answer';
      const answerParagraph = document.createElement('p');
      answerParagraph.textContent = answerText;
      const hide = document.createElement('button');
      hide.type = 'button';
      hide.textContent = 'Hide answer';
      answer.append(answerLabel, answerParagraph, hide);
      show.onclick = () => {
        answer.hidden = false;
        show.hidden = true;
        show.setAttribute('aria-expanded','true');
        answer.focus({preventScroll:true});
      };
      hide.onclick = () => {
        answer.hidden = true;
        show.hidden = false;
        show.setAttribute('aria-expanded','false');
        show.focus({preventScroll:true});
      };
      answer.tabIndex = -1;
      card.append(number, heading, show, answer);
      grid.append(card);
    });
    originalCards.forEach(card => card.remove());
    flashcardSection.append(grid);
  }

  const matching = root.querySelector('.functions-matching');
  if (matching) {
    const matchingHeading = [...root.querySelectorAll('.objective-self-test h5')]
      .find(heading => heading.textContent.trim() === '2. Matching');
    if (matchingHeading) matchingHeading.textContent = '2. Matching — Identify the Function';
    const explanation = document.createElement('div');
    explanation.className = 'matching-purpose-explanation';
    const purpose = document.createElement('p');
    purpose.innerHTML = '<strong>Objective:</strong> recognize which of the four channel functions best explains each cue.';
    const instruction = document.createElement('p');
    instruction.textContent = 'For every cue on the left, choose the corresponding function from the menu. Each function is used once. Focus on the relationship being described—not on selecting an acupuncture point or making a diagnosis.';
    explanation.append(purpose,instruction);
    matching.before(explanation);
    const checkButton = matching.nextElementSibling;
    const result = checkButton?.nextElementSibling;
    if (checkButton?.tagName === 'BUTTON' && result) {
      checkButton.textContent = 'Check matching answers / Show answer key';
      checkButton.onclick = () => {
        const selects = [...matching.querySelectorAll('select')];
        const answered = selects.filter(select => select.value).length;
        const correct = selects.filter(select => select.value === select.dataset.answer).length;
        result.replaceChildren();
        const summary = document.createElement('p');
        summary.textContent = answered
          ? `${correct} of ${selects.length} relationships correct. Complete answer key:`
          : 'No answers were selected. Complete answer key:';
        const key = document.createElement('ul');
        [
          ['Nourishing tissues and sense organs','Transport and integration'],
          ['Containing exterior pathogenic factors','Protection'],
          ['Pain or discoloration along a pathway','Response to dysfunction'],
          ['Effect attributed to needling or pressure','Transmission of stimulation']
        ].forEach(([cue,answer]) => {
          const item = document.createElement('li');
          const cueText = document.createElement('strong');
          cueText.textContent = `${cue} → `;
          item.append(cueText,answer);
          key.append(item);
        });
        result.append(summary,key);
      };
    }
  }

  const sequence = root.querySelector('.visual-sequence-task');
  const sequenceAnswer = sequence?.nextElementSibling;
  if (sequence && sequenceAnswer?.tagName === 'DETAILS') {
    const correct = [
      'Skin and body hair',
      'Minute collaterals',
      'Luo-Connecting Channels',
      'Primary Channels',
      'Zangfu and bowels'
    ];
    const options = [
      'Choose a layer',
      'Primary Channels',
      'Skin and body hair',
      'Zangfu and bowels',
      'Minute collaterals',
      'Luo-Connecting Channels'
    ];
    sequence.replaceChildren();
    const selects = correct.map((answer, index) => {
      const item = document.createElement('li');
      const label = document.createElement('label');
      label.textContent = `Step ${index + 1} — `;
      const select = document.createElement('select');
      select.dataset.answer = answer;
      options.forEach((text) => {
        const option = document.createElement('option');
        option.textContent = text;
        option.value = text === 'Choose a layer' ? '' : text;
        select.append(option);
      });
      label.append(select);
      item.append(label);
      sequence.append(item);
      return select;
    });
    const button = document.createElement('button');
    button.type = 'button';
    button.textContent = 'Check visual sequence / Show answer key';
    const result = document.createElement('p');
    result.setAttribute('role','status');
    button.onclick = () => {
      const answered = selects.filter(select => select.value).length;
      const correctCount = selects.filter(select => select.value === select.dataset.answer).length;
      const status = answered === 0
        ? 'No answers were selected.'
        : `${correctCount} of 5 steps are correct.`;
      result.innerHTML = `${status} <strong>Complete answer key:</strong> Skin and body hair → Minute collaterals → Luo-Connecting Channels → Primary Channels → Zangfu and bowels.`;
    };
    sequence.after(button, result);
    sequenceAnswer.querySelector('summary').textContent = 'Show the correct sequence and its limitation';
  }

  const fillLine = root.querySelector('.fill-blank-line');
  if (fillLine) {
    const fillHeading = [...root.querySelectorAll('.objective-self-test h5')]
      .find(heading => heading.textContent.trim() === '3. Fill in the Blank');
    if (fillHeading) fillHeading.textContent = '3. Word Bank Selection';
    const checkButton = fillLine.nextElementSibling;
    const result = checkButton?.nextElementSibling;
    const answers = ['roots','stems','flowers'];
    const choices = ['Choose a word',...answers];
    const explanation = document.createElement('p');
    explanation.className = 'analogy-mnemonic-explanation';
    explanation.textContent = 'Use this as a mnemonic: roots → stems → flowers helps you remember the functional relationship among zangfu, channels, and the tissues or sense organs they nourish. It is a study analogy, not a literal anatomical equivalence.';
    fillLine.before(explanation);
    const wordBank = document.createElement('div');
    wordBank.className = 'analogy-word-bank';
    const bankLabel = document.createElement('strong');
    bankLabel.textContent = 'Word bank:';
    wordBank.append(bankLabel);
    answers.forEach(word => {
      const chip = document.createElement('span');
      chip.textContent = word;
      wordBank.append(chip);
    });
    explanation.after(wordBank);
    fillLine.replaceChildren();
    const selects = [];
    [
      ['Zangfu are the…','roots'],
      ['Channels are the…','stems'],
      ['Tissues and sense organs are the…','flowers']
    ].forEach(([labelText,answer]) => {
      const label = document.createElement('label');
      label.className = 'analogy-choice';
      const name = document.createElement('span');
      name.textContent = labelText;
      const select = document.createElement('select');
      select.dataset.answer = answer;
      choices.forEach(text => {
        const option = document.createElement('option');
        option.textContent = text;
        option.value = text === 'Choose a word' ? '' : text;
        select.append(option);
      });
      label.append(name,select);
      fillLine.append(label);
      selects.push(select);
    });
    if (checkButton?.tagName === 'BUTTON' && result) {
      checkButton.textContent = 'Check the three relationships';
      checkButton.onclick = () => {
        result.textContent = selects.some(select => !select.value)
          ? 'Choose one word for each relationship before checking.'
          : selects.every(select => select.value === select.dataset.answer)
            ? 'Correct: zangfu → roots; channels → stems; tissues and sense organs → flowers.'
            : 'Review the roots–stems–flowers analogy and try again.';
      };
    }
  }

  const legacyHeading = document.getElementById('old-objectives-heading');
  if (legacyHeading) legacyHeading.classList.add('legacy-material-divider');
  for (let number = 1; number <= 5; number += 1) {
    document.getElementById(`sgv2-objective-${number}`)?.classList.add('legacy-objective-card');
  }

  const style = document.createElement('style');
  style.textContent = `
    #channels-objective-1 .channel-function-flashcard-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:14px}
    #channels-objective-1 .channel-function-flashcard{display:flex;flex-direction:column;min-height:220px;padding:18px;border:1px solid #b8d9d6;border-top:5px solid #087774;border-radius:12px;background:#f7fcfb;box-sizing:border-box}
    #channels-objective-1 .channel-function-flashcard h5{font-size:1rem;line-height:1.45;margin:8px 0 18px;color:#163f50}
    #channels-objective-1 .flashcard-number-badge{align-self:flex-start;margin:0;padding:5px 9px;border-radius:999px;background:#e2f2ee;color:#075f60;font-size:.78rem;font-weight:800}
    #channels-objective-1 .flashcard-show-button{margin-top:auto;align-self:flex-start}
    #channels-objective-1 .flashcard-revealed-answer{margin-top:auto;padding:13px;border-radius:9px;background:#fff3e3;border-left:4px solid #ef7e2d}
    #channels-objective-1 .flashcard-revealed-answer p{margin:7px 0 12px;line-height:1.6}
    #channels-objective-1 .interactive-mc-question .chapter-practice-option{margin:5px 0;padding:9px 10px;border-radius:7px;background:#fff}
    #channels-objective-1 .interactive-mc-question .chapter-practice-option:hover{background:#edf7f5}
    #channels-objective-1 .matching-purpose-explanation{margin:10px 0 14px;padding:13px 15px;border-left:4px solid #087774;border-radius:8px;background:#edf8f5;line-height:1.65}
    #channels-objective-1 .matching-purpose-explanation p{margin:5px 0}
    #channels-objective-1 .visual-sequence-task{display:grid;gap:10px;padding-left:28px}
    #channels-objective-1 .visual-sequence-task label{display:grid;grid-template-columns:90px minmax(220px,420px);align-items:center;gap:10px}
    #channels-objective-1 .analogy-word-bank{display:flex;flex-wrap:wrap;align-items:center;gap:9px;margin:12px 0;padding:12px;border-radius:9px;background:#fff5e8}
    #channels-objective-1 .analogy-mnemonic-explanation{margin:10px 0 12px;padding:12px 14px;border-left:4px solid #087774;border-radius:8px;background:#edf8f5;line-height:1.65}
    #channels-objective-1 .analogy-word-bank strong{margin-right:4px}
    #channels-objective-1 .analogy-word-bank span{padding:5px 10px;border:1px solid #e5b77e;border-radius:999px;background:#fff;font-weight:700}
    #channels-objective-1 .fill-blank-line{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
    #channels-objective-1 .analogy-choice{display:flex;flex-direction:column;gap:7px;padding:12px;border:1px solid #cbdedd;border-radius:9px;background:#fff}
    .channels-new-objective{display:block;margin:18px 0!important;border-radius:12px;overflow:hidden}
    .channels-new-objective>summary{display:block;padding:15px 18px!important;line-height:1.4}
    .legacy-material-divider{margin-top:34px!important;border-top:5px solid #a9804e!important;background:#fff9ef!important}
    .legacy-objective-card{display:block;margin:14px 0!important;border-left:4px solid #a9804e!important;opacity:.96}
    .legacy-objective-card>summary{display:block;padding:14px 17px!important;line-height:1.4}
    @media(max-width:720px){#channels-objective-1 .channel-function-flashcard-grid,#channels-objective-1 .fill-blank-line{grid-template-columns:1fr}}
    @media(max-width:620px){#channels-objective-1 .visual-sequence-task label{grid-template-columns:1fr}}
  `;
  document.head.append(style);
})();
