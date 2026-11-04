(function() {
  'use strict';

  const routeCurrent = document.querySelector('[data-map-route-current]');
  const rows = [...document.querySelectorAll('tr[id^="map-card-"]')];

  if (!routeCurrent || !rows.length) {
    return;
  }

  const rowLabel = function(row) {
    const heading = row.querySelector('.herb-formula-map__cell h4');
    return heading ? heading.textContent.replace(/\s+/g, ' ').trim() : '';
  };

  const setActive = function(activeRow, replaceHash) {
    routeCurrent.textContent = rowLabel(activeRow);
    rows.forEach(row => {
      if (row === activeRow) {
        row.setAttribute('aria-current', 'true');
      } else {
        row.removeAttribute('aria-current');
      }
    });
    if (replaceHash && window.location.hash !== '#' + activeRow.id) {
      window.history.replaceState(window.history.state, '', '#' + activeRow.id);
    }
  };

  const hashMatch = window.location.hash.match(/^#map-card-(\d+)$/);
  const initialRow = hashMatch ? document.getElementById('map-card-' + hashMatch[1]) : rows[0];
  setActive(initialRow || rows[0], false);

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function(entries) {
      const visible = entries
        .filter(entry => entry.isIntersecting)
        .sort((first, second) => Math.abs(first.boundingClientRect.top) - Math.abs(second.boundingClientRect.top));
      if (!visible.length) {
        return;
      }
      setActive(visible[0].target, true);
    }, {rootMargin: '-14% 0px -72% 0px', threshold: 0});
    rows.forEach(row => observer.observe(row));
  }
})();
