(() => {
  'use strict';
  const root=document.getElementById('channels-objective-2');
  const section=root?.querySelector('.objective-instructor-resource');
  if(!section||section.dataset.mockDownload==='complete')return;
  section.dataset.mockDownload='complete';section.hidden=false;section.replaceChildren();
  const heading=document.createElement('h4');heading.textContent='Instructor Resource — Mock 10';
  const title=document.createElement('h5');title.textContent='The Twelve Primary Channels (Mock 10)';
  const description=document.createElement('p');description.textContent='Download ten original multiple-choice study questions with a separate answer key and concise explanations. The document is formatted for Microsoft Word.';
  const note=document.createElement('p');note.className='mock-download-note';note.textContent='These are original study questions based on the cited chapter material. They are not official examination questions or claims about examination frequency.';
  const link=document.createElement('a');link.className='primary-mock-download';link.href='downloads/The_Twelve_Primary_Channels_Mock_10.docx?v=4';link.download='The_Twelve_Primary_Channels_Mock_10.docx';link.textContent='Download The Twelve Primary Channels (Mock 10)';
  section.append(heading,title,description,link,note);
  const style=document.createElement('style');style.textContent='#channels-objective-2 .primary-mock-download{display:inline-flex;align-items:center;min-height:48px;margin:8px 0 12px;padding:11px 18px;border-radius:10px;background:#087774;color:#fff;font-weight:800;text-decoration:none;box-shadow:0 4px 10px rgba(5,88,87,.18)}#channels-objective-2 .primary-mock-download:hover,#channels-objective-2 .primary-mock-download:focus-visible{background:#056461;text-decoration:underline}#channels-objective-2 .mock-download-note{font-size:.88rem;color:#526872}';document.head.append(style);
})();
