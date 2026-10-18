(() => {
  'use strict';
  const root=document.getElementById('channels-objective-4');const box=root?.querySelector('.objective-instructor-resource');
  if(!box)return;box.replaceChildren();
  const h=document.createElement('h4');h.textContent='Instructor Resource — Mock 10';
  const p=document.createElement('p');p.textContent='Ten multiple-choice questions on the Eight Extraordinary Vessels. Each feedback paragraph explains the correct response and all three distractors without relying on option letters.';
  box.append(h,p);
})();
