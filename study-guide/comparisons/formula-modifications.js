(() => {
  'use strict';

  const spanish = document.documentElement.lang.toLowerCase().startsWith('es');
  const elements = {
    search: document.querySelector('[data-modification-search]'),
    category: document.querySelector('[data-modification-category]'),
    status: document.querySelector('[data-modification-status]'),
    grid: document.querySelector('[data-modification-grid]'),
    empty: document.querySelector('[data-modification-empty]'),
    total: document.querySelector('[data-modification-total]'),
    formulaTotal: document.querySelector('[data-formula-total]'),
    visibleFormulas: document.querySelector('[data-visible-formulas]'),
    visibleModifications: document.querySelector('[data-visible-modifications]')
  };
  const labels = spanish ? {
    category: 'Categoría',
    entries: count => `${count} modificaciones`,
    open: count => `Ver ${count} modificaciones`,
    trigger: 'Situación clínica:',
    action: 'Modificación:',
    source: 'Fuente:',
    ready: 'Biblioteca de modificaciones lista.',
    error: 'No fue posible cargar las modificaciones.'
  } : {
    category: 'Category',
    entries: count => `${count} modifications`,
    open: count => `View ${count} modifications`,
    trigger: 'Clinical trigger:',
    action: 'Modification:',
    source: 'Source:',
    ready: 'Modification library ready.',
    error: 'The modification library could not be loaded.'
  };
  let groups = [];

  function normalized(value) {
    return String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  }

  function displayed(question) {
    return spanish && question.translationEs ? question.translationEs : question;
  }

  function correctOption(question) {
    const copy = displayed(question);
    return (copy.options || []).find(option => option.label === question.correctLabel)?.text || '';
  }

  function clinicalTrigger(question, formulaName) {
    const prompt = String(displayed(question).prompt || '').replace(/[¿?]+$/g, '').trim();
    const markers = spanish ? [' para ', ' cuando ', ' en casos de ', ' si '] : [' for ', ' when ', ' in cases of ', ' if '];
    const lower = prompt.toLowerCase();
    for (const marker of markers) {
      const index = lower.indexOf(marker);
      if (index !== -1) {
        const value = prompt.slice(index + marker.length).trim();
        return value.charAt(0).toUpperCase() + value.slice(1);
      }
    }
    const withoutFormula = prompt.replace(new RegExp(formulaName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi'), '').replace(/\s+/g, ' ').trim();
    return withoutFormula.replace(/^(how|which|what|cómo|qué|cuál)\b[^:]*?:?\s*/i, '').trim() || prompt;
  }

  function sourceFrom(question) {
    const feedback = String(displayed(question).feedback?.[0] || '');
    const marker = spanish ? /\s+Fuente:\s*(.+)$/i : /\s+Source:\s*(.+)$/i;
    return feedback.match(marker)?.[1] || '';
  }

  function createCard(group) {
    const article = document.createElement('article');
    article.className = 'formula-modification-card';
    article.dataset.category = group.formula.categoryKey;

    const header = document.createElement('header');
    header.className = 'formula-modification-card__header';
    const meta = document.createElement('div');
    meta.className = 'formula-modification-card__meta';
    const category = document.createElement('span');
    category.textContent = spanish ? group.formula.categoryEs : group.formula.categoryEn;
    const count = document.createElement('span');
    count.textContent = labels.entries(group.entries.length);
    meta.append(category, count);
    const heading = document.createElement('h2');
    heading.textContent = group.formula.name;
    header.append(meta, heading);

    const details = document.createElement('details');
    const summary = document.createElement('summary');
    summary.textContent = labels.open(group.entries.length);
    const list = document.createElement('ol');
    group.entries.forEach(question => {
      const item = document.createElement('li');
      const trigger = document.createElement('p');
      trigger.className = 'formula-modification-card__trigger';
      const triggerLabel = document.createElement('strong');
      triggerLabel.textContent = labels.trigger;
      trigger.append(triggerLabel, ` ${clinicalTrigger(question, group.formula.name)}`);
      const action = document.createElement('p');
      action.className = 'formula-modification-card__action';
      const actionLabel = document.createElement('strong');
      actionLabel.textContent = labels.action;
      action.append(actionLabel, ` ${correctOption(question)}`);
      const sourceText = sourceFrom(question);
      if (sourceText) {
        const source = document.createElement('small');
        source.className = 'formula-modification-card__source';
        source.textContent = `${labels.source} ${sourceText}`;
        action.append(source);
      }
      item.append(trigger, action);
      list.append(item);
    });
    details.append(summary, list);
    article.append(header, details);
    article.dataset.search = normalized([
      group.formula.name,
      group.formula.categoryEn,
      group.formula.categoryEs,
      ...group.entries.flatMap(question => [clinicalTrigger(question, group.formula.name), correctOption(question)])
    ].join(' '));
    return article;
  }

  function applyFilters() {
    const query = normalized(elements.search.value);
    const category = elements.category.value;
    let visibleFormulas = 0;
    let visibleModifications = 0;
    elements.grid.querySelectorAll('.formula-modification-card').forEach((card, index) => {
      const group = groups[index];
      const visible = (!query || card.dataset.search.includes(query)) && (category === 'all' || card.dataset.category === category);
      card.hidden = !visible;
      if (visible) {
        visibleFormulas++;
        visibleModifications += group.entries.length;
        if (query) card.querySelector('details').open = true;
      }
    });
    elements.visibleFormulas.textContent = String(visibleFormulas);
    elements.visibleModifications.textContent = String(visibleModifications);
    elements.empty.hidden = visibleFormulas !== 0;
  }

  Promise.all([
    fetch('formula-practice-data.json', { credentials: 'same-origin' }).then(response => response.ok ? response.json() : Promise.reject(new Error(`HTTP ${response.status}`))),
    fetch('formula-modifications-manifest.json', { credentials: 'same-origin' })
      .then(response => response.ok ? response.json() : Promise.reject(new Error(`HTTP ${response.status}`)))
      .then(manifest => Promise.all((manifest.files || []).map(filename =>
        fetch(filename, { credentials: 'same-origin' }).then(response => response.ok ? response.json() : Promise.reject(new Error(`HTTP ${response.status}`)))
      )))
  ]).then(([bank, supplements]) => {
    const formulaByNumber = new Map(bank.formulas.map(formula => [formula.number, formula]));
    const questions = supplements.flatMap(supplement => supplement.questions || []);
    const grouped = new Map();
    questions.forEach(question => {
      const number = question.formulaNumbers?.[0];
      if (!formulaByNumber.has(number)) return;
      if (!grouped.has(number)) grouped.set(number, []);
      grouped.get(number).push(question);
    });
    groups = [...grouped.entries()].sort((a, b) => a[0] - b[0]).map(([number, entries]) => ({ formula: formulaByNumber.get(number), entries }));

    const categories = [...new Map(groups.map(group => [group.formula.categoryKey, group.formula])).values()];
    categories.forEach(formula => {
      const option = document.createElement('option');
      option.value = formula.categoryKey;
      option.textContent = spanish ? formula.categoryEs : formula.categoryEn;
      elements.category.append(option);
    });
    groups.forEach(group => elements.grid.append(createCard(group)));
    elements.total.textContent = String(questions.length);
    elements.formulaTotal.textContent = String(groups.length);
    elements.status.textContent = labels.ready;
    elements.status.classList.add('is-ready');
    applyFilters();
  }).catch(error => {
    console.error(error);
    elements.status.textContent = labels.error;
    elements.status.classList.add('is-error');
  });

  elements.search.addEventListener('input', applyFilters);
  elements.category.addEventListener('change', applyFilters);
  const requestedFormula = new URLSearchParams(window.location.search).get('q');
  if (requestedFormula) elements.search.value = requestedFormula;
})();
