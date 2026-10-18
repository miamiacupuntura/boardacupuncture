(() => {
  'use strict';
  const exam=document.querySelector('#channels-objective-2 .objective-exam');
  if(!exam||exam.querySelector('a[href*="id=9343"]'))return;
  const link=document.createElement('a');
  link.className='primary-exam-link';
  link.href='https://www.acupuncturetests.com/mod/scorm/view.php?id=9343';
  link.target='_blank';
  link.rel='noopener noreferrer';
  link.textContent='The Twelve Primary Channels (Mock 10)';
  exam.append(link);
})();
