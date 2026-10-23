(function () {
  'use strict';

  const form = document.getElementById('self-evaluation-form');
  if (!form) return;

  const storageKey = 'channel-pathways-self-evaluation-v1';
  const inputs = [...form.querySelectorAll('.final-board-checklist input[type="checkbox"]')];
  const readyCount = document.getElementById('board-ready-count');
  const saveStatus = document.getElementById('self-save-status');
  const clear = document.getElementById('clear-self-evaluation');
  let saveTimer;

  function collect() {
    const checks = {};
    inputs.forEach(input => { checks[input.name] = input.checked; });
    return {checks};
  }

  function updateCount() {
    const complete = inputs.filter(input => input.checked).length;
    readyCount.textContent = `${complete} / ${inputs.length} complete`;
  }

  function save() {
    updateCount();
    try {
      localStorage.setItem(storageKey, JSON.stringify(collect()));
      saveStatus.textContent = 'Checklist saved in this browser.';
    } catch (error) {
      saveStatus.textContent = 'Your selections are visible now, but this browser could not save them.';
    }
  }

  function scheduleSave() {
    window.clearTimeout(saveTimer);
    saveStatus.textContent = 'Saving…';
    saveTimer = window.setTimeout(save, 200);
  }

  function restore() {
    let data;
    try { data = JSON.parse(localStorage.getItem(storageKey) || 'null'); }
    catch (error) { data = null; }
    Object.entries(data?.checks || {}).forEach(([name, checked]) => {
      const input = form.elements.namedItem(name);
      if (input) input.checked = Boolean(checked);
    });
    updateCount();
  }

  form.addEventListener('change', scheduleSave);
  clear.addEventListener('click', () => {
    if (!window.confirm('Clear the Final Board preparation checklist?')) return;
    form.reset();
    try { localStorage.removeItem(storageKey); } catch (error) { /* Storage may be unavailable. */ }
    updateCount();
    saveStatus.textContent = 'Checklist cleared.';
  });

  restore();
}());
