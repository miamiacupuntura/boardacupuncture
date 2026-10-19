(() => {
  'use strict';

  const PHONE_QUERY = '(max-width:600px)';
  const CHAPTER_ROOT_SELECTOR = '[data-chapter-pattern],.sgv2-layout,#point-categories-draft';
  const INDEX_URL = '/blocks/myprogress/study-guide.php?area=pointlocation';
  const INDEX_LABEL = '← Back to Study Guide index';
  const reusableShellClasses = [
    'study-guide-mobile-table-shell',
    'point-category-mobile-table-shell',
    'point-reference-table-wrap',
    'objective-correspondence-table-wrap',
    'study-guide-exam-table-shell',
    'channels-exam-table-wrap',
    'pc-history-table-wrap'
  ];

  function chapterRoots() {
    return [...new Set(document.querySelectorAll(CHAPTER_ROOT_SELECTOR))];
  }

  function ensureBackButton() {
    let bar = document.querySelector('.study-guide-mobile-index-back');
    if (!bar) {
      const anchor = document.querySelector('.chapter-selector') || chapterRoots()[0];
      if (!anchor) return;
      bar = document.createElement('div');
      bar.className = 'study-guide-mobile-index-back';
      const link = document.createElement('a');
      link.className = 'point-categories-hero-button point-categories-hero-back';
      bar.append(link);
      anchor.before(bar);
    }
    const link = bar.querySelector('a') || bar.appendChild(document.createElement('a'));
    link.classList.add('point-categories-hero-button', 'point-categories-hero-back');
    if (link.getAttribute('href') !== INDEX_URL) link.href = INDEX_URL;
    if (link.textContent !== INDEX_LABEL) link.textContent = INDEX_LABEL;
  }

  function markObjectiveNavigation(root) {
    root.querySelectorAll('.flat-navigation-objectives').forEach(section => {
      section.dataset.mobileNavigationObjectives = 'true';
    });
    root.querySelectorAll('.study-navigation-group,.flat-navigation-section').forEach(section => {
      const heading = section.querySelector(':scope > summary,:scope > h4');
      if (/^(objectives|objetivos)$/i.test(heading?.textContent.trim() || '')) {
        section.dataset.mobileNavigationObjectives = 'true';
      }
    });
  }

  function columnCount(table) {
    return Math.max(0, ...[...table.rows].slice(0, 8).map(row =>
      [...row.cells].reduce((total, cell) => total + (cell.colSpan || 1), 0)
    ));
  }

  function isDenseTable(table, columns) {
    if (table.matches('[data-mobile-table-wide="true"],.point-reference-table')) return true;
    if (columns >= 5) return true;
    return [...table.querySelectorAll('th,td')].some(cell => cell.textContent.trim().length > 90);
  }

  function reusableShell(table) {
    const parent = table.parentElement;
    if (!parent || parent.tagName !== 'DIV') return null;
    if (reusableShellClasses.some(name => parent.classList.contains(name))) return parent;
    const overflow = getComputedStyle(parent).overflowX;
    return overflow === 'auto' || overflow === 'scroll' ? parent : null;
  }

  function enhanceTable(table) {
    if (table.matches('.study-guide-exam-table,[data-mobile-table-default="false"]')) return;
    if (table.dataset.mobileChapterDefault !== 'true') {
      table.dataset.mobileChapterDefault = 'true';
      table.classList.add('study-guide-content-table');

      const columns = columnCount(table);
      table.dataset.mobileColumns = String(columns);
      const dense = isDenseTable(table, columns);
      table.classList.toggle('study-guide-content-table-wide', dense);
      table.classList.toggle('study-guide-content-table-compact', !dense && columns > 0 && columns <= 4);
      if (dense) table.style.setProperty('--study-guide-mobile-table-min', `${Math.max(640, columns * 125)}px`);
    }

    let shell = reusableShell(table);
    if (!shell) {
      shell = document.createElement('div');
      table.before(shell);
      shell.append(table);
    }
    shell.classList.add('study-guide-mobile-table-shell');
    if (!shell.hasAttribute('role')) shell.setAttribute('role', 'region');
    if (!shell.hasAttribute('aria-label')) {
      shell.setAttribute('aria-label', table.querySelector('caption')?.textContent.trim() || 'Scrollable study table');
    }
  }

  function enhance() {
    ensureBackButton();
    chapterRoots().forEach(root => {
      root.classList.add('shared-mobile-chapter-defaults');
      markObjectiveNavigation(root);
      if (matchMedia(PHONE_QUERY).matches) root.querySelectorAll('table').forEach(enhanceTable);
    });
  }

  const style = document.createElement('style');
  style.id = 'shared-mobile-chapter-default-styles';
  style.textContent = `
    .study-guide-mobile-table-shell{max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}
    @media(min-width:761px){.chapter-selector{display:none!important}}
    @media(max-width:760px){
      [data-mobile-navigation-objectives="true"]{display:none!important}
      .study-guide-mobile-index-back{display:block;position:sticky;top:0;z-index:1000;margin:0 0 14px;padding:10px 0;background:rgba(238,247,247,.96);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}
      .study-guide-mobile-index-back .point-categories-hero-back{display:flex;align-items:center;justify-content:center;width:100%;min-height:46px;box-sizing:border-box;padding:10px 14px;border:2px solid #176b70;border-radius:9px;background:#fff;color:#145960;font-weight:800;text-align:center;text-decoration:none}
    }
    @media(max-width:600px){
      .shared-mobile-chapter-defaults .study-guide-mobile-table-shell{position:relative;box-sizing:border-box;width:calc(100vw - 30px)!important;max-width:calc(100vw - 30px)!important;margin-left:calc((100% - (100vw - 30px))/2)!important;margin-right:calc((100% - (100vw - 30px))/2)!important;overflow-x:auto!important;overscroll-behavior-x:contain;background:#fff}
      .shared-mobile-chapter-defaults .study-guide-content-table{display:table!important}
      .shared-mobile-chapter-defaults .study-guide-content-table caption{display:table-caption!important;position:static!important;width:auto!important;height:auto!important;overflow:visible!important;clip:auto!important;clip-path:none!important}
      .shared-mobile-chapter-defaults .study-guide-content-table thead{display:table-header-group!important;position:static!important;width:auto!important;height:auto!important;overflow:visible!important;clip:auto!important;clip-path:none!important}
      .shared-mobile-chapter-defaults .study-guide-content-table tbody{display:table-row-group!important;width:auto!important}
      .shared-mobile-chapter-defaults .study-guide-content-table tr{display:table-row!important;width:auto!important;margin:0!important;border-radius:0!important;overflow:visible!important}
      .shared-mobile-chapter-defaults .study-guide-content-table th,.shared-mobile-chapter-defaults .study-guide-content-table td{display:table-cell!important;width:auto}
      .shared-mobile-chapter-defaults .study-guide-content-table [data-label]::before{display:none!important;content:none!important}
      .shared-mobile-chapter-defaults .study-guide-content-table caption,.shared-mobile-chapter-defaults .study-guide-content-table th,.shared-mobile-chapter-defaults .study-guide-content-table td{box-sizing:border-box;padding:10px 8px!important;text-align:left!important;vertical-align:top!important;white-space:normal!important;overflow-wrap:normal!important;word-break:normal!important}
      .shared-mobile-chapter-defaults .study-guide-content-table-compact{width:100%!important;min-width:0!important;table-layout:fixed!important}
      .shared-mobile-chapter-defaults .study-guide-content-table-compact[data-mobile-columns="2"] tr>*:first-child{width:var(--study-guide-mobile-first-column,38%)!important;max-width:var(--study-guide-mobile-first-column,38%)!important}
      .shared-mobile-chapter-defaults .study-guide-content-table-compact[data-mobile-columns="3"] tr>*:first-child{width:var(--study-guide-mobile-first-column,40%)!important;max-width:var(--study-guide-mobile-first-column,40%)!important}
      .shared-mobile-chapter-defaults .study-guide-content-table-compact[data-mobile-columns="4"] tr>*:first-child{width:var(--study-guide-mobile-first-column,38%)!important;max-width:var(--study-guide-mobile-first-column,38%)!important}
      .shared-mobile-chapter-defaults .study-guide-content-table-wide{width:var(--study-guide-mobile-table-min)!important;min-width:var(--study-guide-mobile-table-min)!important;table-layout:fixed!important}
    }
  `;
  document.head.append(style);

  let queued = false;
  const queueEnhance = () => {
    if (queued) return;
    queued = true;
    queueMicrotask(() => {
      queued = false;
      enhance();
    });
  };

  new MutationObserver(queueEnhance).observe(document.documentElement, {childList: true, subtree: true});
  matchMedia(PHONE_QUERY).addEventListener?.('change', queueEnhance);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', queueEnhance, {once: true});
  else queueEnhance();
})();
