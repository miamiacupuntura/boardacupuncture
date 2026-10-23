(function () {
  'use strict';

  const regions = window.CHANNEL_REGION_AREAS || [];
  const grid = document.getElementById('region-grid');
  if (!grid || !regions.length) return;

  const search = document.getElementById('region-search');
  const zone = document.getElementById('region-zone');
  const alphabet = document.getElementById('alphabet-filter');
  const status = document.getElementById('region-results-status');
  const cards = [];
  let activeLetter = 'All';

  document.getElementById('region-total').textContent = regions.length;

  function allItems(region) {
    return region.groups.reduce((list, group) => list.concat(group.items), []);
  }

  function regionCard(region) {
    const details = document.createElement('details');
    details.className = 'region-card';
    details.dataset.zone = region.zone;
    details.dataset.letter = region.area.charAt(0).toUpperCase();
    details.dataset.search = [region.area, region.zone, region.mnemonic]
      .concat(region.aliases || [], allItems(region), region.groups.map(group => group.title))
      .join(' ').toLowerCase();

    const summary = document.createElement('summary');
    const titleWrap = document.createElement('span');
    titleWrap.className = 'region-card-title';
    const area = document.createElement('strong');
    area.textContent = region.area;
    const regionZone = document.createElement('small');
    regionZone.textContent = region.zone;
    titleWrap.append(area, regionZone);
    const count = document.createElement('span');
    count.className = 'region-count';
    const total = allItems(region).length;
    count.textContent = total + (total === 1 ? ' pathway' : ' pathways');
    summary.append(titleWrap, count);

    const body = document.createElement('div');
    body.className = 'region-card-body';
    region.groups.forEach(group => {
      const section = document.createElement('section');
      const heading = document.createElement('h3');
      heading.textContent = group.title;
      const list = document.createElement('ul');
      group.items.forEach(item => {
        const entry = document.createElement('li');
        entry.textContent = item;
        list.append(entry);
      });
      section.append(heading, list);
      body.append(section);
    });
    const mnemonic = document.createElement('aside');
    mnemonic.className = 'region-mnemonic';
    const label = document.createElement('b');
    label.textContent = 'Memory cue';
    const text = document.createElement('span');
    text.textContent = region.mnemonic;
    mnemonic.append(label, text);
    body.append(mnemonic);
    details.append(summary, body);
    return details;
  }

  regions.forEach(region => {
    const card = regionCard(region);
    cards.push(card);
    grid.append(card);
  });

  const availableLetters = [...new Set(regions.map(region => region.area.charAt(0).toUpperCase()))].sort();
  ['All'].concat(availableLetters).forEach(letter => {
    const button = document.createElement('button');
    button.type = 'button';
    button.textContent = letter;
    button.dataset.letter = letter;
    button.classList.toggle('active', letter === 'All');
    button.addEventListener('click', () => {
      activeLetter = letter;
      alphabet.querySelectorAll('button').forEach(item => item.classList.toggle('active', item === button));
      applyFilters();
    });
    alphabet.append(button);
  });

  function applyFilters() {
    const query = search.value.trim().toLowerCase();
    const selectedZone = zone.value;
    let visible = 0;
    cards.forEach(card => {
      const matchesQuery = !query || card.dataset.search.includes(query);
      const matchesZone = selectedZone === 'All' || card.dataset.zone === selectedZone;
      const matchesLetter = activeLetter === 'All' || card.dataset.letter === activeLetter;
      card.hidden = !(matchesQuery && matchesZone && matchesLetter);
      if (!card.hidden) visible += 1;
    });
    status.textContent = visible + ' of ' + regions.length + ' anatomical areas shown';
  }

  search.addEventListener('input', applyFilters);
  zone.addEventListener('change', applyFilters);
  document.getElementById('expand-regions').addEventListener('click', () => cards.forEach(card => { if (!card.hidden) card.open = true; }));
  document.getElementById('collapse-regions').addEventListener('click', () => cards.forEach(card => { card.open = false; }));
  applyFilters();

  const flashZone = document.getElementById('region-flash-zone');
  const flashArea = document.getElementById('region-flash-area');
  const flashFront = document.getElementById('region-flash-front');
  const flashAnswer = document.getElementById('region-flash-answer');
  const flashCounter = document.getElementById('region-flash-counter');
  const reveal = document.getElementById('reveal-region-card');
  let flashPool = [];
  let flashIndex = 0;
  let current = null;
  let revealed = false;

  function shuffle(list) {
    const copy = [...list];
    for (let index = copy.length - 1; index > 0; index -= 1) {
      const swap = Math.floor(Math.random() * (index + 1));
      [copy[index], copy[swap]] = [copy[swap], copy[index]];
    }
    return copy;
  }

  function renderAnswer(region) {
    flashAnswer.replaceChildren();
    const heading = document.createElement('h3');
    heading.textContent = region.area;
    flashAnswer.append(heading);
    region.groups.forEach(group => {
      const section = document.createElement('section');
      const title = document.createElement('h4');
      title.textContent = group.title;
      const list = document.createElement('ul');
      group.items.forEach(item => {
        const entry = document.createElement('li');
        entry.textContent = item;
        list.append(entry);
      });
      section.append(title, list);
      flashAnswer.append(section);
    });
    const mnemonic = document.createElement('aside');
    const label = document.createElement('b');
    label.textContent = 'Memory cue: ';
    mnemonic.append(label, document.createTextNode(region.mnemonic));
    flashAnswer.append(mnemonic);
  }

  function renderFlashcard() {
    if (!flashPool.length) return;
    current = flashPool[flashIndex];
    revealed = false;
    flashArea.textContent = current.area;
    flashFront.hidden = false;
    flashAnswer.hidden = true;
    reveal.textContent = 'Reveal pathways';
    flashCounter.textContent = (flashIndex + 1) + ' / ' + flashPool.length;
    renderAnswer(current);
  }

  function setFlashPool(randomize) {
    const selected = regions.filter(region => flashZone.value === 'All' || region.zone === flashZone.value);
    flashPool = randomize ? shuffle(selected) : selected;
    flashIndex = 0;
    renderFlashcard();
  }

  function toggleAnswer() {
    revealed = !revealed;
    flashFront.hidden = revealed;
    flashAnswer.hidden = !revealed;
    reveal.textContent = revealed ? 'Show question' : 'Reveal pathways';
  }

  flashZone.addEventListener('change', () => setFlashPool(false));
  reveal.addEventListener('click', toggleAnswer);
  document.getElementById('previous-region-card').addEventListener('click', () => { flashIndex = (flashIndex - 1 + flashPool.length) % flashPool.length; renderFlashcard(); });
  document.getElementById('next-region-card').addEventListener('click', () => { flashIndex = (flashIndex + 1) % flashPool.length; renderFlashcard(); });
  document.getElementById('shuffle-region-cards').addEventListener('click', () => setFlashPool(true));
  setFlashPool(false);
})();
