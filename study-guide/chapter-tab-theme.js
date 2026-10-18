(() => {
  'use strict';
  const style = document.createElement('style');
  style.id = 'standard-chapter-tab-theme';
  style.textContent = `
    :root{--chapter-tab-bg:#eaf4f5;--chapter-tab-bg-open:#dfeff0;--chapter-tab-text:#124f5d;--chapter-tab-focus:#1769c2}
    .sgv2-content > details > summary,
    .sgv2-content > section > details > summary,
    #student_notes_form summary,
    #point-categories-draft > details > summary,
    #point-categories-draft .sgv2-content > details > summary{
      background:var(--chapter-tab-bg)!important;color:var(--chapter-tab-text)!important;border-color:transparent!important
    }
    .sgv2-content > details[open] > summary,
    .sgv2-content > section > details[open] > summary,
    #student_notes_form details[open] > summary,
    #point-categories-draft > details[open] > summary,
    #point-categories-draft .sgv2-content > details[open] > summary{
      background:var(--chapter-tab-bg-open)!important;color:var(--chapter-tab-text)!important
    }
    .sgv2-content > details > summary:hover,
    .sgv2-content > section > details > summary:hover,
    #student_notes_form summary:hover,
    #point-categories-draft > details > summary:hover,
    #point-categories-draft .sgv2-content > details > summary:hover{background:#d8eaec!important}
    .sgv2-content > details > summary:focus-visible,
    .sgv2-content > section > details > summary:focus-visible,
    #student_notes_form summary:focus-visible,
    #point-categories-draft > details > summary:focus-visible,
    #point-categories-draft .sgv2-content > details > summary:focus-visible{
      outline:3px solid var(--chapter-tab-focus)!important;outline-offset:3px
    }
    #chapter-exam-history,#sgv2-final-review,#chapter-mock-review,#student_notes_form,
    #pc-objective-return,#pc-simulation,#pc-notes,#pc-exam-history{border-top-color:#d4e3e6!important}
  `;
  document.head.append(style);
})();
