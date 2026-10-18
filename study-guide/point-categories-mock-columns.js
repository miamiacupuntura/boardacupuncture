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

  rows.forEach(row => {
    const action = row.querySelector('td:last-child');
    if (!action) return;
    for (let i = 0; i < 4; i += 1) {
      const td = document.createElement('td');
      td.textContent = '—';
      row.insertBefore(td, action);
    }
  });

  const style = document.createElement('style');
  style.textContent = `
    #point-categories-mock-exams{overflow-x:auto}
    #point-categories-mock-exams table{min-width:760px;table-layout:auto;border-collapse:collapse}
    #point-categories-mock-exams th,#point-categories-mock-exams td{text-align:center;vertical-align:middle;overflow-wrap:normal}
    #point-categories-mock-exams tbody th{text-align:left;font-weight:500}
    #point-categories-mock-exams th:first-child{width:54%}
    #point-categories-mock-exams tbody tr:nth-child(odd){background:#f4f6f8}
    #point-categories-mock-exams tbody tr:nth-child(even){background:#fff}
    #point-categories-mock-exams .mock-directory-exam-button{white-space:nowrap}
    @media(max-width:760px){#point-categories-mock-exams table{min-width:700px}}
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
