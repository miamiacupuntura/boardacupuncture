(() => {
  'use strict';
  const box=document.querySelector('#channels-objective-7 .objective-instructor-resource');if(!box)return;
  box.replaceChildren();
  const h=document.createElement('h4');h.textContent='Instructor Resource — Mock 10';
  const p=document.createElement('p');p.textContent='Ten multiple-choice questions on the Minute Collaterals. Correct options are marked with +++++, and each item has one continuous feedback paragraph explaining the correct response and all three distractors without relying on option letters.';
  const link=document.createElement('a');link.className='minute-mock-download';link.href='downloads/The_Minute_Collaterals_Mock_10.docx?v=2';link.download='The_Minute_Collaterals_Mock_10.docx';link.textContent='Download The Minute Collaterals (Mock 10)';box.append(h,p,link);
  const exam=document.querySelector('#channels-objective-7 .objective-exam');
  if(exam){
    exam.replaceChildren();
    const title=document.createElement('h4');title.textContent='Exam with score';
    const guidance=document.createElement('p');guidance.textContent='Choose when to take this scored exam: after studying this objective, or after completing the reading for all objectives in the chapter. The exam opens in another tab.';
    const examLink=document.createElement('a');examLink.className='minute-mock-exam';examLink.href='https://www.acupuncturetests.com/mod/scorm/view.php?id=9352';examLink.target='_blank';examLink.rel='noopener noreferrer';examLink.textContent='The Minute Collaterals (Mock 10)';
    exam.append(title,guidance,examLink);
  }
  const style=document.createElement('style');style.textContent='.minute-mock-download,.minute-mock-exam{display:inline-block;min-height:44px;box-sizing:border-box;padding:12px 18px;border-radius:10px;background:#ff7f22;color:#17232a!important;font-weight:800;text-decoration:none}.minute-mock-download:hover,.minute-mock-download:focus-visible,.minute-mock-exam:hover,.minute-mock-exam:focus-visible{background:#ff963f}@media(max-width:760px){.minute-mock-download,.minute-mock-exam{display:block;width:100%;text-align:center}}';document.head.append(style);
})();
