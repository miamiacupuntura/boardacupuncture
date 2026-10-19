(() => {
  'use strict';
  function initialize() {
  const directory = document.getElementById('point-categories-mock-exams');
  const table = directory?.querySelector('table');
  const rows = table?.querySelectorAll('tbody tr');
  if (!directory || !table || !rows?.length) return false;
  if (table.dataset.sixColumnMock === 'true') return true;
  table.dataset.sixColumnMock = 'true';

  const headerRow = table.querySelector('thead tr');
  if (headerRow) {
    headerRow.replaceChildren();
    ['Exam', 'Attempts', 'Last Attempt', 'Average', 'Grade', 'Action'].forEach(label => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = label;
      headerRow.append(th);
    });
  }

  const metricLabels = ['Attempts', 'Last Attempt', 'Average', 'Grade'];
  rows.forEach(row => {
    const action = row.querySelector('td:last-child');
    if (!action) return;
    metricLabels.forEach(label => {
      const td = document.createElement('td');
      td.dataset.label = label;
      td.textContent = '—';
      row.insertBefore(td, action);
    });
    action.dataset.label = 'Action';
  });

  const style = document.createElement('style');
  style.textContent = `
    #point-categories-mock-exams{max-width:100%;overflow-x:hidden}
    #point-categories-mock-exams table{width:100%;min-width:0;table-layout:fixed;border-collapse:collapse}
    #point-categories-mock-exams th,#point-categories-mock-exams td{padding:12px 7px;text-align:center;vertical-align:middle;overflow-wrap:anywhere}
    #point-categories-mock-exams tbody th{text-align:left;font-weight:500}
    #point-categories-mock-exams th:first-child{width:36%}
    #point-categories-mock-exams th:last-child{width:19%}
    #point-categories-mock-exams tbody tr:nth-child(odd){background:#f4f6f8}
    #point-categories-mock-exams tbody tr:nth-child(even){background:#fff}
    #point-categories-mock-exams .mock-directory-exam-button{display:inline-flex;width:100%;align-items:center;justify-content:center;padding:10px 8px;line-height:1.2;white-space:normal}
    @media(max-width:760px){
      #point-categories-mock-exams{box-sizing:border-box;width:100%;min-width:0;overflow-x:hidden!important}
      #point-categories-mock-exams table{box-sizing:border-box;width:100%!important;min-width:0!important;max-width:100%!important}
      #point-categories-mock-exams thead{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap}
      #point-categories-mock-exams tbody{display:grid;width:100%;min-width:0;gap:12px}
      #point-categories-mock-exams tbody tr{display:grid;box-sizing:border-box;width:100%;min-width:0;max-width:100%;grid-template-columns:repeat(2,minmax(0,1fr));padding:12px;border:1px solid #d4e0e4;border-radius:10px;background:#fff;overflow:hidden}
      #point-categories-mock-exams tbody th{grid-column:1/-1;width:auto;padding:4px 4px 10px;border-bottom:1px solid #d4e0e4}
      #point-categories-mock-exams tbody td{display:grid;box-sizing:border-box;width:auto;min-width:0!important;grid-template-columns:minmax(0,1fr) auto;gap:8px;padding:8px 4px;text-align:right}
      #point-categories-mock-exams tbody td::before{content:attr(data-label);font-weight:750;color:#38525c;text-align:left}
      #point-categories-mock-exams tbody td:last-child{grid-column:1/-1;display:block;padding-top:10px}
      #point-categories-mock-exams tbody td:last-child::before{content:none}
      #point-categories-mock-exams .mock-directory-exam-button{box-sizing:border-box;width:100%;max-width:100%;min-width:0}
    }
  `;
  document.head.append(style);
  return true;
  }

  if (!initialize()) {
    const observer = new MutationObserver(() => {
      if (initialize()) observer.disconnect();
    });
    observer.observe(document.documentElement, {childList: true, subtree: true});
  }
})();
