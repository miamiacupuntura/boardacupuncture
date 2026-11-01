(function () {
  'use strict';

  const search = document.querySelector('[data-tips-search]');
  const buttons = [...document.querySelectorAll('[data-tips-filter]')];
  const cards = [...document.querySelectorAll('[data-tip-card]')];
  const status = document.querySelector('[data-tips-status]');
  const empty = document.querySelector('[data-tips-empty]');
  let activeCategory = 'all';

  function normalise(value) {
    return String(value || '')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .replace(/\s+/g, ' ')
      .trim();
  }

  function update() {
    const query = normalise(search ? search.value : '');
    let visible = 0;

    cards.forEach(card => {
      const categoryMatches = activeCategory === 'all' || card.dataset.category === activeCategory;
      const searchMatches = !query || normalise(card.textContent).includes(query);
      const show = categoryMatches && searchMatches;
      card.hidden = !show;
      if (show) {
        visible += 1;
      }
    });

    if (status) {
      const template = visible === 1 ? status.dataset.single : status.dataset.plural;
      status.textContent = template.replace('{count}', String(visible));
    }
    if (empty) {
      empty.hidden = visible !== 0;
    }
  }

  buttons.forEach(button => {
    button.addEventListener('click', () => {
      activeCategory = button.dataset.tipsFilter || 'all';
      buttons.forEach(item => item.setAttribute('aria-pressed', String(item === button)));
      update();
    });
  });

  if (search) {
    search.addEventListener('input', update);
  }

  update();
})();
