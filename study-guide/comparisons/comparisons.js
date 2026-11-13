(function () {
  'use strict';

  const returnNavigation = document.createElement('script');
  returnNavigation.src = 'study-return-navigation.js?v=20261113-3';
  document.head.appendChild(returnNavigation);

  const params = new URLSearchParams(window.location.search);
  const returnFormula = (params.get('returnformula') || '').trim();
  const relatedCategory = (params.get('related') || '').trim();
  const initialQuery = params.get('q') || '';

  function addFormulaReturnButton() {
    if (!returnFormula || document.querySelector('[data-return-to-formula-card]')) {
      return;
    }
    const topbar = document.querySelector('.comparison-topbar__inner');
    if (!topbar) {
      return;
    }
    let actions = [...topbar.children].find(element =>
      element.tagName === 'SPAN' && !element.classList.contains('comparison-brand')
    );
    if (!actions) {
      actions = document.createElement('span');
      actions.style.display = 'flex';
      actions.style.flexWrap = 'wrap';
      actions.style.gap = '8px';
      topbar.appendChild(actions);
    }
    const back = document.createElement('a');
    const url = new URL('/blocks/myprogress/formula-list.php', window.location.origin);
    url.searchParams.set('formula', returnFormula);
    url.searchParams.set('sort', 'az');
    if (document.documentElement.lang.toLowerCase().startsWith('es')) {
      url.searchParams.set('lang', 'es');
    }
    back.className = 'comparison-back';
    back.dataset.returnToFormulaCard = '';
    back.href = url.toString();
    back.textContent = (document.documentElement.lang.toLowerCase().startsWith('es')
      ? '← Volver a '
      : '← Back to ') + returnFormula;
    actions.prepend(back);
  }

  returnNavigation.addEventListener('load', addFormulaReturnButton);
  addFormulaReturnButton();

  const search = document.querySelector('[data-comparison-search]');
  const filters = [...document.querySelectorAll('[data-filter]')];
  const cards = [...document.querySelectorAll('.comparison-card')];
  const grid = document.querySelector('[data-comparison-grid]');
  const count = document.querySelector('[data-visible-count]');
  const empty = document.querySelector('[data-comparison-empty]');
  let activeFilter = 'all';

  if (search) {
    search.value = initialQuery;
  }

  if (returnFormula) {
    document.querySelectorAll('.comparison-card__open').forEach(link => {
      const url = new URL(link.href, window.location.href);
      url.searchParams.set('returnformula', returnFormula);
      link.href = url.toString();
    });
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

  let relatedNotice;

  function showRelatedNotice(active) {
    if (!returnFormula) return;
    if (!relatedNotice) {
      relatedNotice = document.createElement('p');
      relatedNotice.setAttribute('role', 'status');
      relatedNotice.style.margin = '-8px 0 22px';
      relatedNotice.style.padding = '13px 15px';
      relatedNotice.style.border = '1px solid #e6c88e';
      relatedNotice.style.borderLeft = '5px solid #c37a12';
      relatedNotice.style.borderRadius = '9px';
      relatedNotice.style.background = '#fff8e9';
      relatedNotice.style.color = '#71420c';
      const tools = document.querySelector('.comparison-tools');
      if (tools) tools.insertAdjacentElement('afterend', relatedNotice);
    }
    relatedNotice.hidden = !active;
    if (active) {
      const spanish = document.documentElement.lang.toLowerCase().startsWith('es');
      relatedNotice.textContent = relatedCategory
        ? (spanish
          ? `No hay una comparación directa para ${returnFormula}. Se muestran comparaciones relacionadas de ${relatedCategory}.`
          : `No direct comparison is available for ${returnFormula}. Showing related comparisons from ${relatedCategory}.`)
        : (spanish
          ? `No hay una comparación directa para ${returnFormula}. Se muestran todas las comparaciones disponibles.`
          : `No direct comparison is available for ${returnFormula}. Showing all available comparisons.`);
    }
  }

  function applyQuery(query) {
    let visible = 0;
    cards.forEach(card => {
      const typeMatches = activeFilter === 'all' || card.dataset.type === activeFilter;
      const textMatches = !query || normalise(card.dataset.search + ' ' + card.textContent).includes(query);
      card.hidden = !(typeMatches && textMatches);
      if (!card.hidden) visible++;
    });
    return visible;
  }

  function refresh(allowRelatedFallback = false) {
    const query = normalise(search ? search.value : '');
    let visible = applyQuery(query);
    let showingRelated = false;
    if (visible === 0 && allowRelatedFallback && returnFormula
        && query === normalise(initialQuery)) {
      visible = applyQuery(relatedCategory ? normalise(relatedCategory) : '');
      showingRelated = visible > 0;
    }
    showRelatedNotice(showingRelated);
    if (count) count.textContent = String(visible);
    if (empty) empty.hidden = visible !== 0;
  }

  filters.forEach(button => {
    button.addEventListener('click', () => {
      activeFilter = button.dataset.filter || 'all';
      filters.forEach(candidate => {
        candidate.setAttribute('aria-pressed', candidate === button ? 'true' : 'false');
      });
      refresh(true);
    });
  });
  if (search) {
    search.addEventListener('input', () => {
      const url = new URL(window.location.href);
      search.value.trim() ? url.searchParams.set('q', search.value.trim()) : url.searchParams.delete('q');
      history.replaceState(null, '', url);
      refresh(false);
    });
  }
  refresh(true);
})();
