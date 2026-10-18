(() => {
  'use strict';
  const objective = document.getElementById('channels-objective-9');
  const exam = objective?.querySelector('.objective-exam');
  if (!exam || exam.dataset.examReady === 'true') return;
  exam.dataset.examReady = 'true';
  exam.replaceChildren();

  const heading = document.createElement('h4');
  heading.textContent = 'Exam (with score)';
  const guidance = document.createElement('p');
  guidance.className = 'channels-scored-exam-guidance';
  guidance.textContent = 'Choose when to take this scored exam: after studying this objective, or after completing the reading for all objectives in the chapter. You can return to this objective through Study Navigation and use the same exam button.';
  const link = document.createElement('a');
  link.className = 'channels-scored-exam-button';
  link.href = 'https://www.acupuncturetests.com/mod/scorm/view.php?id=9354';
  link.target = '_blank';
  link.rel = 'noopener';
  link.textContent = 'Integrated Channel Systems Review (Mock 10)';
  exam.append(heading, guidance, link);

  const style = document.createElement('style');
  style.textContent = `
    .channels-scored-exam-guidance{margin:14px 0 20px;padding:14px 16px;border-left:4px solid #ff7f16;background:#fff3e7;border-radius:8px;line-height:1.55}
    .channels-scored-exam-button{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:11px 18px;border-radius:10px;background:#ff7f16;color:#17232a;font-weight:800;text-decoration:none;box-shadow:0 4px 10px rgba(193,88,20,.2)}
    .channels-scored-exam-button:hover{transform:translateY(-1px)}
    .channels-scored-exam-button:focus-visible{outline:3px solid #185cca;outline-offset:3px}
  `;
  document.head.append(style);
})();
