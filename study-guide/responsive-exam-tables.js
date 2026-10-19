(() => {
  'use strict';

  const tableSelector = [
    '.study-guide-exam-table',
    '.channels-exam-table',
    '.channels-mock-table',
    '.pc-history-table',
    '#point-categories-mock-exams table'
  ].join(',');

  const shellSelector = [
    '.study-guide-exam-table-shell',
    '.channels-exam-table-wrap',
    '.channels-mock-exams',
    '.pc-history-table-wrap',
    '#point-categories-mock-exams'
  ].join(',');

  function columnLabels(table) {
    return [...table.querySelectorAll('thead th')]
      .slice(1)
      .map(header => header.textContent.trim());
  }

  function normalizeTable(table) {
    table.classList.add('study-guide-exam-table');

    const shell = table.closest(shellSelector) || table.parentElement;
    if (shell) {
      shell.classList.add('study-guide-exam-table-shell');
      if (shell.tagName !== 'DETAILS' && !shell.hasAttribute('aria-label')) {
        const caption = table.querySelector('caption')?.textContent.trim();
        shell.setAttribute('aria-label', caption || 'Exam history and actions');
        shell.setAttribute('role', 'region');
      }
    }

    const labels = columnLabels(table);
    table.querySelectorAll('tbody tr').forEach(row => {
      row.classList.add('study-guide-exam-row');

      [...row.querySelectorAll(':scope > td')].forEach((cell, index) => {
        cell.dataset.label = labels[index] || '';
      });

      const actionCell = row.querySelector(':scope > td:last-child');
      actionCell?.classList.add('study-guide-exam-action-cell');
      actionCell?.querySelector('a,button')?.classList.add('study-guide-exam-action');
    });
  }

  function normalizeTables() {
    document.querySelectorAll(tableSelector).forEach(normalizeTable);
  }

  normalizeTables();
  new MutationObserver(normalizeTables).observe(document.documentElement, {
    childList: true,
    subtree: true,
    characterData: true
  });

  const style = document.createElement('style');
  style.id = 'shared-responsive-exam-table-styles';
  style.textContent = `
    .study-guide-exam-table-shell {
      container: study-guide-exams / inline-size;
      max-width: 100%;
      margin-block: 18px;
      overflow-x: auto !important;
      overflow-y: hidden;
    }

    .study-guide-exam-table {
      width: 100%;
      min-width: 0 !important;
      table-layout: fixed;
      border-collapse: collapse;
      background: #fff;
    }

    .study-guide-exam-table caption {
      padding: 10px 0;
      color: #123b4a;
      font-weight: 800;
      text-align: left;
    }

    .study-guide-exam-table th,
    .study-guide-exam-table td {
      min-width: 0 !important;
      padding: 12px 7px;
      border-bottom: 1px solid #d4e0e4;
      text-align: center;
      vertical-align: middle;
      overflow-wrap: anywhere;
    }

    .study-guide-exam-table thead th {
      background: #e7f1f4;
      color: #123b4a;
      font-weight: 800;
    }

    .study-guide-exam-table tbody th {
      text-align: left;
      font-weight: 500;
    }

    .study-guide-exam-table tbody tr:nth-child(odd) {
      background: #f4f6f8;
    }

    .study-guide-exam-table tbody tr:nth-child(even) {
      background: #fff;
    }

    .study-guide-exam-table th:first-child {
      width: 36%;
    }

    .study-guide-exam-table th:last-child {
      width: 19%;
    }

    .study-guide-exam-table .study-guide-exam-action {
      display: inline-flex;
      width: 100%;
      min-height: 44px;
      box-sizing: border-box;
      align-items: center;
      justify-content: center;
      padding: 10px 8px;
      border: 0;
      border-radius: 10px;
      background: #ff7f22;
      color: #17232a !important;
      font-weight: 800;
      line-height: 1.2;
      white-space: normal;
      text-align: center;
      text-decoration: none;
    }

    .study-guide-exam-table .study-guide-exam-action:hover,
    .study-guide-exam-table .study-guide-exam-action:focus-visible {
      background: #f06e12;
      outline: 3px solid rgba(8,119,116,.24);
      outline-offset: 2px;
    }

    /* Match the compact Moodle table on phones instead of converting rows
       into cards. Landscape retains all six columns. */
    @media (max-width: 900px) {
      .preview-view-switch {
        position: static;
      }

      .study-guide-exam-table {
        display: table;
        table-layout: fixed;
        font-size: .82rem;
      }

      .study-guide-exam-table thead {
        position: static;
        width: auto;
        height: auto;
        overflow: visible;
        clip: auto;
        clip-path: none;
        white-space: normal;
      }

      .study-guide-exam-table tbody {
        display: table-row-group;
      }

      .study-guide-exam-table tbody tr {
        display: table-row;
      }

      .study-guide-exam-table th,
      .study-guide-exam-table td,
      .study-guide-exam-table tbody th,
      .study-guide-exam-table tbody td,
      .study-guide-exam-table tbody .study-guide-exam-action-cell {
        display: table-cell;
        padding: 10px 6px !important;
        border-radius: 0;
      }

      .study-guide-exam-table tbody td::before,
      .study-guide-exam-table tbody .study-guide-exam-action-cell::before {
        content: none;
      }

      .study-guide-exam-table th:first-child {
        width: 34%;
      }

      .study-guide-exam-table th:last-child {
        width: 17%;
      }

      .study-guide-exam-table .study-guide-exam-action {
        width: 64px;
        min-width: 0;
        min-height: 58px;
        padding: 8px 6px;
        border-radius: 28px;
      }
    }

    @media (max-width: 520px) {
      .study-guide-exam-table {
        font-size: .78rem;
        min-width: 640px !important;
      }

      .study-guide-exam-table th,
      .study-guide-exam-table td,
      .study-guide-exam-table tbody th,
      .study-guide-exam-table tbody td {
        padding: 10px 5px !important;
      }

      .study-guide-exam-table th:first-child {
        width: 34%;
      }
    }
  `;
  document.head.append(style);
})();
