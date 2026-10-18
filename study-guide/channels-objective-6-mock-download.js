(() => {
  'use strict';
  const root=document.getElementById('channels-objective-6');
  const box=root?.querySelector('.objective-instructor-resource');
  if(!box)return;
  box.replaceChildren();
  const h=document.createElement('h4');h.textContent='Instructor Resource — Mock 10';
  const p=document.createElement('p');p.textContent='Ten multiple-choice questions on the Twelve Sinew Channels. Every item uses A–D options, and each continuous feedback paragraph explains the correct response and why all three distractors are incorrect without referring to option letters.';
  const link=document.createElement('a');
  link.className='sinew-mock-download';
  link.href='downloads/The_Twelve_Sinew_Channels_Mock_10.docx?v=3';
  link.download='The_Twelve_Sinew_Channels_Mock_10.docx';
  link.textContent='Download The Twelve Sinew Channels (Mock 10)';
  box.append(h,p,link);
  const exam=root.querySelector('.objective-exam');
  if(exam){
    exam.replaceChildren();
    const examTitle=document.createElement('h4');examTitle.textContent='Exam with score';
    const guidance=document.createElement('p');guidance.textContent='Choose when to take this scored exam: after studying this objective, or after completing the reading for all objectives in the chapter. The exam opens in another tab.';
    const examLink=document.createElement('a');
    examLink.className='sinew-mock-exam';
    examLink.href='https://www.acupuncturetests.com/mod/scorm/view.php?id=9351';
    examLink.target='_blank';examLink.rel='noopener noreferrer';
    examLink.textContent='The Twelve Sinew Channels (Mock 10)';
    exam.append(examTitle,guidance,examLink);
  }
  const style=document.createElement('style');
  style.textContent='.sinew-mock-download,.sinew-mock-exam{display:inline-block;min-height:44px;box-sizing:border-box;padding:12px 18px;border-radius:10px;background:#ff7f22;color:#17232a!important;font-weight:800;text-decoration:none}.sinew-mock-download:hover,.sinew-mock-download:focus-visible,.sinew-mock-exam:hover,.sinew-mock-exam:focus-visible{background:#ff963f}@media(max-width:760px){.sinew-mock-download,.sinew-mock-exam{display:block;width:100%;text-align:center}}';
  document.head.append(style);
})();
