(() => {
  'use strict';
  const box=document.querySelector('#channels-objective-8 .objective-instructor-resource');if(!box)return;
  box.replaceChildren();
  const h=document.createElement('h4');h.textContent='Instructor Resource — Mock 10';
  const p=document.createElement('p');p.textContent='Ten multiple-choice questions on the Twelve Cutaneous Regions. Correct options are marked with +++++, and each item contains one continuous feedback paragraph explaining the correct response and why all three distractors are incorrect without relying on option letters.';
  const link=document.createElement('a');link.className='cutaneous-mock-download';link.href='downloads/The_Twelve_Cutaneous_Regions_Mock_10.docx?v=1';link.download='The_Twelve_Cutaneous_Regions_Mock_10.docx';link.textContent='Download The Twelve Cutaneous Regions (Mock 10)';box.append(h,p,link);
  const style=document.createElement('style');style.textContent='.cutaneous-mock-download{display:inline-block;min-height:44px;box-sizing:border-box;padding:12px 18px;border-radius:10px;background:#ff7f22;color:#17232a!important;font-weight:800;text-decoration:none}.cutaneous-mock-download:hover,.cutaneous-mock-download:focus-visible{background:#ff963f}@media(max-width:760px){.cutaneous-mock-download{display:block;width:100%;text-align:center}}';document.head.append(style);
})();
