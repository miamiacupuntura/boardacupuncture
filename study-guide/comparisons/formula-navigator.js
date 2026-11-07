(() => {
  'use strict';

  const spanish = document.documentElement.lang === 'es';
  const search = document.querySelector('[data-formula-search]');
  const cards = [...document.querySelectorAll('.formula-navigator-card')];
  const categorySelect = document.querySelector('[data-formula-category-filter]');
  const routeSelect = document.querySelector('[data-formula-route-filter]');
  const count = document.querySelector('[data-visible-count]');
  const empty = document.querySelector('[data-formula-empty]');
  const comparePanel = document.querySelector('[data-formula-compare-panel]');
  const compareOutput = document.querySelector('[data-compare-output]');
  const compareGuidance = document.querySelector('[data-compare-guidance]');
  const clearComparison = document.querySelector('[data-clear-comparison]');
  const comparisonData = JSON.parse(document.getElementById('formula-navigator-data')?.textContent || '[]');
  const byNumber = new Map(comparisonData.map(formula => [String(formula.number), formula]));
  const params = new URLSearchParams(window.location.search);
  let activeCategory = params.get('category') || 'all';
  let activeRoute = params.get('route') || 'all';
  const selected = new Set();

  function normalise(value) {
    return String(value || '').toLocaleLowerCase().normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, ' ').trim();
  }

  function setSelectValue(select, key) {
    if (![...select.options].some(option => option.value === key)) key = 'all';
    select.value = key;
    return key;
  }

  function updateUrl() {
    const url = new URL(window.location.href);
    const query = search.value.trim();
    query ? url.searchParams.set('q', query) : url.searchParams.delete('q');
    activeCategory !== 'all' ? url.searchParams.set('category', activeCategory) : url.searchParams.delete('category');
    activeRoute !== 'all' ? url.searchParams.set('route', activeRoute) : url.searchParams.delete('route');
    history.replaceState(null, '', url);
  }

  function refresh() {
    const query = normalise(search.value);
    let visible = 0;
    cards.forEach(card => {
      const categoryMatches = activeCategory === 'all' || card.dataset.category === activeCategory;
      const routeMatches = activeRoute === 'all' || card.dataset.routes.split(' ').includes(activeRoute);
      const queryMatches = !query || normalise(`${card.dataset.search} ${card.textContent}`).includes(query);
      card.hidden = !(categoryMatches && routeMatches && queryMatches);
      if (!card.hidden) visible += 1;
    });
    count.textContent = String(visible);
    empty.hidden = visible !== 0;
    updateUrl();
  }

  function row(label, formulas, key) {
    return `<tr><th scope="row">${label}</th>${formulas.map(formula => `<td>${escapeHtml(formula[key])}</td>`).join('')}</tr>`;
  }

  function escapeHtml(value) {
    const span = document.createElement('span');
    span.textContent = value;
    return span.innerHTML;
  }

  function renderComparison() {
    const formulas = [...selected].map(number => byNumber.get(number)).filter(Boolean);
    comparePanel.hidden = formulas.length === 0;
    compareGuidance.textContent = formulas.length < 2
      ? (spanish ? 'Selecciona al menos una fórmula más.' : 'Select at least one more formula.')
      : (spanish ? `${formulas.length} fórmulas seleccionadas.` : `${formulas.length} formulas selected.`);
    if (formulas.length < 2) {
      compareOutput.replaceChildren();
      return;
    }
    compareOutput.innerHTML = `<table class="formula-compare-table"><thead><tr><th>${spanish ? 'Criterio' : 'Criterion'}</th>${formulas.map(formula => `<th scope="col">${escapeHtml(formula.name)}</th>`).join('')}</tr></thead><tbody>${row(spanish ? 'Categoría' : 'Category', formulas, 'category')}${row(spanish ? 'Patrón' : 'Pattern', formulas, 'pattern')}${row(spanish ? 'Acción' : 'Action', formulas, 'action')}${row(spanish ? 'Pista decisiva' : 'Decisive clue', formulas, 'clue')}${row(spanish ? 'Emperador' : 'King herbs', formulas, 'kings')}</tbody></table>`;
  }

  categorySelect.addEventListener('change', () => {
    activeCategory = categorySelect.value;
    refresh();
  });
  routeSelect.addEventListener('change', () => {
    activeRoute = routeSelect.value;
    refresh();
  });
  search.addEventListener('input', refresh);

  document.querySelectorAll('[data-compare-formula]').forEach(input => input.addEventListener('change', () => {
    if (input.checked && selected.size >= 4) {
      input.checked = false;
      window.alert(spanish ? 'Puedes comparar un máximo de cuatro fórmulas.' : 'You can compare up to four formulas.');
      return;
    }
    input.checked ? selected.add(input.value) : selected.delete(input.value);
    renderComparison();
  }));

  clearComparison.addEventListener('click', () => {
    selected.clear();
    document.querySelectorAll('[data-compare-formula]').forEach(input => { input.checked = false; });
    renderComparison();
  });

  search.value = params.get('q') || '';
  activeCategory = setSelectValue(categorySelect, activeCategory);
  activeRoute = setSelectValue(routeSelect, activeRoute);
  refresh();
})();
