(function () {
  'use strict';

  const params = new URLSearchParams(window.location.search);
  const enabled = params.get('instructor') === '1' || window.location.hash === '#instructor-chapter-template';
  if (!enabled) return;

  const bar = document.querySelector('.preview-view-switch');
  const panel = document.getElementById('instructor-chapter-template');
  if (!bar || !panel) return;

  document.documentElement.classList.add('instructor-authoring-view');
  const instructor = Array.from(bar.querySelectorAll('button')).find(button => button.textContent.trim() === 'Instructor');
  if (instructor) instructor.click();
  panel.hidden = false;
  const editor = panel.querySelector('details');
  if (editor) editor.open = true;

  const notice = document.createElement('span');
  notice.className = 'instructor-authoring-notice';
  notice.textContent = 'Instructor authoring view — drafts stay in this browser and are not published to Moodle.';
  bar.append(notice);

  const exit = document.createElement('button');
  exit.type = 'button';
  exit.className = 'instructor-authoring-exit';
  exit.textContent = 'Exit Instructor View';
  exit.addEventListener('click', () => {
    const url = new URL(window.location.href);
    url.searchParams.delete('instructor');
    if (url.hash === '#instructor-chapter-template') url.hash = '';
    window.location.assign(url.pathname + url.search + url.hash);
  });
  bar.append(exit);

  const style = document.createElement('style');
  style.textContent = `
    .instructor-authoring-view .preview-view-switch{
      display:flex!important;
      visibility:visible!important;
      position:sticky!important;
      top:0!important;
      z-index:2000!important;
      margin:0 0 14px!important;
    }
    .instructor-authoring-view #instructor-chapter-template{display:block!important}
    .instructor-authoring-notice{flex:1 1 280px;color:#36577f;line-height:1.4}
    .preview-view-switch .instructor-authoring-exit{border-color:#9a3f2f;background:#fff;color:#8c3225}
    @media(max-width:600px){
      .instructor-authoring-view .preview-view-switch{align-items:stretch}
      .instructor-authoring-view .preview-view-switch>strong,
      .instructor-authoring-view .preview-view-switch>[role="status"],
      .instructor-authoring-notice{flex-basis:100%}
      .instructor-authoring-view .preview-view-switch button{flex:1 1 45%;min-height:46px}
      .instructor-authoring-view .preview-view-switch .instructor-authoring-exit{flex-basis:100%}
    }
  `;
  document.head.append(style);

  requestAnimationFrame(() => panel.scrollIntoView({block: 'start'}));
})();
