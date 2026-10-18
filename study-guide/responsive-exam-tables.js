(() => {
  'use strict';

  const tableSelector = [
    '.channels-exam-table',
    '.channels-mock-table',
    '#point-categories-mock-exams table'
  ].join(',');
  const labels = ['Attempts', 'Last Attempt', 'Average', 'Grade', 'Action'];

  function labelTableCells(root = document) {
    root.querySelectorAll(tableSelector).forEach(table => {
      table.querySelectorAll('tbody tr').forEach(row => {
        [...row.querySelectorAll(':scope > td')].forEach((cell, index) => {
          cell.dataset.label = labels[index] || '';
        });
      });
    });
  }

  labelTableCells();
  new MutationObserver(records => {
    if (records.some(record => record.addedNodes.length)) labelTableCells();
  }).observe(document.documentElement, {childList: true, subtree: true});

  const style = document.createElement('style');
  style.textContent = `
    .channels-exam-table-wrap,
    .channels-mock-exams,
    #point-categories-mock-exams {
      max-width: 100%;
      overflow-x: hidden;
    }

    .channels-exam-table,
    .channels-mock-table,
    #point-categories-mock-exams table {
      width: 100%;
      min-width: 0;
      table-layout: fixed;
      border-collapse: collapse;
    }

    .channels-exam-table th,
    .channels-exam-table td,
    .channels-mock-table th,
    .channels-mock-table td,
    #point-categories-mock-exams th,
    #point-categories-mock-exams td {
      padding: 12px 7px;
      text-align: center;
      vertical-align: middle;
      overflow-wrap: anywhere;
    }

    .channels-exam-table th:first-child,
    .channels-mock-table th:first-child,
    #point-categories-mock-exams th:first-child {
      width: 36%;
    }

    .channels-exam-table th:last-child,
    .channels-mock-table th:last-child,
    #point-categories-mock-exams th:last-child {
      width: 19%;
    }

    .channels-start-exam,
    .channels-mock-start,
    #point-categories-mock-exams .mock-directory-exam-button {
      display: inline-flex;
      width: 100%;
      min-height: 44px;
      box-sizing: border-box;
      align-items: center;
      justify-content: center;
      padding: 10px 8px;
      line-height: 1.2;
      white-space: normal;
    }

    @media (max-width: 760px) {
      .channels-exam-table thead,
      .channels-mock-table thead,
      #point-categories-mock-exams thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
        clip-path: inset(50%);
        white-space: nowrap;
      }

      .channels-exam-table tbody,
      .channels-mock-table tbody,
      #point-categories-mock-exams tbody {
        display: grid;
        gap: 12px;
      }

      .channels-exam-table tbody tr,
      .channels-mock-table tbody tr,
      #point-categories-mock-exams tbody tr {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        padding: 12px;
        border: 1px solid #d4e0e4;
        border-radius: 10px;
        background: #fff;
      }

      .channels-exam-table tbody th,
      .channels-mock-table tbody th,
      #point-categories-mock-exams tbody th {
        grid-column: 1 / -1;
        width: auto;
        padding: 4px 4px 10px;
        border-bottom: 1px solid #d4e0e4;
      }

      .channels-exam-table tbody td,
      .channels-mock-table tbody td,
      #point-categories-mock-exams tbody td {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
        padding: 8px 4px;
        text-align: right;
      }

      .channels-exam-table tbody td::before,
      .channels-mock-table tbody td::before,
      #point-categories-mock-exams tbody td::before {
        content: attr(data-label);
        color: #38525c;
        font-weight: 750;
        text-align: left;
      }

      .channels-exam-table tbody td:last-child,
      .channels-mock-table tbody td:last-child,
      #point-categories-mock-exams tbody td:last-child {
        grid-column: 1 / -1;
        display: block;
        padding-top: 10px;
      }

      .channels-exam-table tbody td:last-child::before,
      .channels-mock-table tbody td:last-child::before,
      #point-categories-mock-exams tbody td:last-child::before {
        content: none;
      }
    }
  `;
  document.head.append(style);
})();
