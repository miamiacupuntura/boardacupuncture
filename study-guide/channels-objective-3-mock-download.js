(() => {
  'use strict';
  const section=document.querySelector('#channels-objective-3 .objective-instructor-resource');
  if(!section||document.getElementById('divergent-mock10-download'))return;
  const description=section.querySelector('p');
  if(description)description.textContent='Download the reviewed ten-question Word document for import or instructor editing. The document begins directly with Question 1 and contains no header or footer.';
  const link=document.createElement('a');
  link.id='divergent-mock10-download';
  link.className='primary-exam-link';
  link.href='downloads/The_Twelve_Divergent_Channels_Mock_10.docx?v=1';
  link.download='The_Twelve_Divergent_Channels_Mock_10.docx';
  link.textContent='Download The Twelve Divergent Channels (Mock 10) — Word';
  section.append(link);

  const exam=document.querySelector('#channels-objective-3 .objective-exam');
  if(exam&&!document.getElementById('divergent-mock10-exam')){
    const guidance=exam.querySelector('p');
    if(guidance)guidance.textContent='Choose when to take this scored exam: after studying this objective, or after completing the reading for all objectives in the chapter. You can return through Study Navigation and use the same exam button.';
    const examLink=document.createElement('a');
    examLink.id='divergent-mock10-exam';
    examLink.className='primary-exam-link';
    examLink.href='https://www.acupuncturetests.com/mod/scorm/view.php?id=9344';
    examLink.target='_blank';
    examLink.rel='noopener';
    examLink.textContent='The Twelve Divergent Channels (Mock 10)';
    exam.append(examLink);
  }
})();
