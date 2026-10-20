(() => {
  'use strict';
  function initialize() {
  const directory = document.getElementById('point-categories-mock-exams');
  const table = directory?.querySelector('table');
  const rows = table?.querySelectorAll('tbody tr');
  if (!directory || !table || !rows?.length) return false;
  if (table.dataset.sixColumnMock === 'true') return true;
  table.dataset.sixColumnMock = 'true';
  table.classList.remove('sgv2-formb');

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
