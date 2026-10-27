(function () {
  'use strict';

  const search = document.querySelector('[data-comparison-search]');
  const filters = [...document.querySelectorAll('[data-filter]')];
  const cards = [...document.querySelectorAll('.comparison-card')];
  const grid = document.querySelector('[data-comparison-grid]');
  const count = document.querySelector('[data-visible-count]');
  const empty = document.querySelector('[data-comparison-empty]');
  let activeFilter = 'all';

  if (search) {
    search.value = new URLSearchParams(window.location.search).get('q') || '';
  }

  function normalise(value) {
    return String(value || '').toLocaleLowerCase().normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, ' ').trim();
  }

  function alphabeticalTitle(card) {
    const heading = card.querySelector('h3');
    return normalise(heading ? heading.textContent : card.textContent)
      .replace(/^[^a-z0-9]+/, '');
  }

  function compareCards(first, second) {
    const firstSequence = Number(first.dataset.sequence);
    const secondSequence = Number(second.dataset.sequence);
    const hasSequence = first.hasAttribute('data-sequence') && second.hasAttribute('data-sequence');

    if (hasSequence && Number.isFinite(firstSequence) && Number.isFinite(secondSequence)) {
      return firstSequence - secondSequence;
    }

    return alphabeticalTitle(first).localeCompare(
      alphabeticalTitle(second),
      'en',
      {numeric: true, sensitivity: 'base'}
    );
  }

  if (grid) {
    cards.sort(compareCards).forEach(card => grid.appendChild(card));
  }

  function refresh() {
    const query = normalise(search ? search.value : '');
    let visible = 0;
    cards.forEach(card => {
      const typeMatches = activeFilter === 'all' || card.dataset.type === activeFilter;
      const textMatches = !query || normalise(card.dataset.search + ' ' + card.textContent).includes(query);
      card.hidden = !(typeMatches && textMatches);
      if (!card.hidden) {
        visible++;
      }
    });
    if (count) {
      count.textContent = String(visible);
    }
    if (empty) {
      empty.hidden = visible !== 0;
    }
  }

  filters.forEach(button => {
    button.addEventListener('click', () => {
      activeFilter = button.dataset.filter || 'all';
      filters.forEach(candidate => {
        candidate.setAttribute('aria-pressed', candidate === button ? 'true' : 'false');
      });
      refresh();
    });
  });
  if (search) {
    search.addEventListener('input', () => {
      const url = new URL(window.location.href);
      search.value.trim() ? url.searchParams.set('q', search.value.trim()) : url.searchParams.delete('q');
      history.replaceState(null, '', url);
      refresh();
    });
  }
  refresh();
})();
