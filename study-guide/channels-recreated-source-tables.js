(() => {
  'use strict';

  const fallbackTitles = [
    'Four Functions of the Channels',
    'Four Directional Groups of the Primary Channels',
    'Three Circuits of the Primary Channels',
    'Twelve Primary Channel Names',
    'Six-Channel Pairs and Limb Positions',
    'Yang and Yin Divergent Channel Patterns',
    'Four Principal Relationships of the Divergent Channels',
    'The Eight Extraordinary Vessels',
    'Selected Extraordinary Vessel Relationships',
    'The Twelve Luo-Connecting Channels',
    'Integrated Comparison of the Channel Systems'
  ];

  function stripIds(root) {
    root.removeAttribute?.('id');
    root.querySelectorAll?.('[id]').forEach(node => node.removeAttribute('id'));
    return root;
  }

  function build() {
    const host = document.getElementById('chapter-image-library');
    const gallery = host?.querySelector(':scope > details');
    if (!gallery || document.getElementById('channels-recreated-source-tables')) return;

    const tables = [...document.querySelectorAll('details[id^="channels-objective-"]:not([id*="board"]) table')];
    if (!tables.length) return;

    const section = document.createElement('section');
    section.id = 'channels-recreated-source-tables';
    section.className = 'channels-source-table-library';
    const heading = document.createElement('h3');
    heading.textContent = 'Recreated Source Tables';
    const intro = document.createElement('p');
    intro.textContent = 'These clean recreations gather the instructional tables from The Channels and Collaterals in one review library. Select any table to enlarge it. They support comparison and recall and do not replace the assigned source text.';
    const note = document.createElement('aside');
    note.className = 'channels-source-table-note';
    note.innerHTML = '<strong>Scope of this gallery</strong><span>The Learning Plan, exam-history tables and Mock Exam directory are intentionally excluded because they are administrative tools rather than source-content tables.</span>';
    const grid = document.createElement('div');
    grid.className = 'channels-source-table-grid';
    section.append(heading, intro, note, grid);
    gallery.append(section);

    const dialog = document.createElement('dialog');
    dialog.className = 'channels-source-table-dialog';
    dialog.setAttribute('aria-label', 'Enlarged recreated source table');
    const controls = document.createElement('div');
    controls.className = 'channels-source-table-controls';
    const close = document.createElement('button');
    close.type = 'button';
    close.textContent = 'Close / Cerrar';
    const fit = document.createElement('button');
    fit.type = 'button';
    fit.textContent = 'Fit table';
    controls.append(close, fit);
    const dialogTitle = document.createElement('h3');
    const viewport = document.createElement('div');
    viewport.className = 'channels-source-table-dialog-viewport';
    dialog.append(controls, dialogTitle, viewport);
    document.body.append(dialog);

    let lastTrigger = null;
    const open = (source, title, trigger) => {
      lastTrigger = trigger;
      dialogTitle.textContent = title;
      viewport.replaceChildren(stripIds(source.cloneNode(true)));
      viewport.scrollLeft = 0;
      dialog.showModal();
      close.focus();
    };
    close.addEventListener('click', () => dialog.close());
    fit.addEventListener('click', () => {
      viewport.scrollLeft = 0;
      viewport.querySelector('table')?.classList.toggle('fit-table');
    });
    dialog.addEventListener('click', event => {
      if (event.target === dialog) dialog.close();
    });
    dialog.addEventListener('close', () => lastTrigger?.focus());

    tables.forEach((table, index) => {
      const objective = table.closest('details[id^="channels-objective-"]');
      const objectiveName = objective?.querySelector(':scope > summary')?.textContent
        .replace(/🖼️\s*View image/gi, '')
        .replace(/\s+/g, ' ')
        .trim();
      const title = table.querySelector('caption')?.textContent.trim() || fallbackTitles[index] || `Source Table ${index + 1}`;
      const card = document.createElement('article');
      card.className = 'channels-source-table-card';
      const cardTitle = document.createElement('h4');
      cardTitle.textContent = title;
      const context = document.createElement('p');
      context.className = 'channels-source-table-context';
      context.textContent = objectiveName || `Table ${index + 1}`;
      const preview = document.createElement('button');
      preview.type = 'button';
      preview.className = 'channels-source-table-preview';
      preview.setAttribute('aria-label', `Enlarge ${title}`);
      const previewFrame = document.createElement('div');
      previewFrame.append(stripIds(table.cloneNode(true)));
      preview.append(previewFrame);
      const action = document.createElement('button');
      action.type = 'button';
      action.className = 'channels-source-table-open';
      action.textContent = 'Enlarge table';
      const show = event => open(table, title, event.currentTarget);
      preview.addEventListener('click', show);
      action.addEventListener('click', show);
      card.append(cardTitle, context, preview, action);
      grid.append(card);
    });

    const style = document.createElement('style');
    style.textContent = `
      .channels-source-table-library{margin-top:30px;padding-top:24px;border-top:5px solid #087774}
      .channels-source-table-library>h3{margin:0 0 8px;color:#174f60;font-size:1.35rem}
      .channels-source-table-note{display:flex;flex-direction:column;gap:5px;margin:14px 0;padding:13px 15px;border-left:5px solid #a36b2b;border-radius:9px;background:#fff7ea}
      .channels-source-table-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-top:18px}
      .channels-source-table-card{display:flex;min-width:0;flex-direction:column;padding:14px;border:1px solid #bcd9de;border-radius:12px;background:#fbfefe}
      .channels-source-table-card h4{margin:0 0 4px;color:#123b4a}
      .channels-source-table-context{margin:0 0 10px;color:#526772;font-size:.86rem}
      .channels-source-table-preview{display:block;width:100%;height:250px;padding:0;border:1px solid #d6e4e6;border-radius:9px;overflow:hidden;background:#fff;cursor:zoom-in;text-align:left}
      .channels-source-table-preview>div{width:720px;transform:scale(.55);transform-origin:top left;pointer-events:none}
      .channels-source-table-preview table{width:100%;background:#fff;font-size:14px}
      .channels-source-table-open{align-self:flex-start;margin-top:12px;padding:0;border:0;background:transparent;color:#086c69;font:inherit;font-weight:800;text-decoration:underline;cursor:pointer}
      .channels-source-table-dialog{width:min(96vw,1500px);max-height:94vh;padding:16px;border:0;border-radius:13px;box-shadow:0 22px 70px rgba(0,0,0,.4);overflow:auto}
      .channels-source-table-dialog::backdrop{background:rgba(7,30,40,.76)}
      .channels-source-table-controls{position:sticky;top:0;z-index:2;display:flex;gap:9px;padding:7px;background:#fff}
      .channels-source-table-controls button{min-height:44px;padding:9px 14px;border:1px solid #87aaa9;border-radius:7px;background:#fff;font-weight:750}
      .channels-source-table-controls button:first-child{border-color:#f47e2c;background:#f47e2c;color:#17232a}
      .channels-source-table-dialog>h3{text-align:center;color:#174f60}
      .channels-source-table-dialog-viewport{max-width:100%;overflow:auto}
      .channels-source-table-dialog-viewport table{width:max(100%,900px);border-collapse:collapse;background:#fff}
      .channels-source-table-dialog-viewport table.fit-table{width:100%;font-size:.82rem}
      .channels-source-table-dialog-viewport th,.channels-source-table-dialog-viewport td{padding:10px;border:1px solid #cbd8dc;text-align:left;vertical-align:top}
      @media(max-width:760px){.channels-source-table-grid{grid-template-columns:1fr}.channels-source-table-preview{height:220px}.channels-source-table-dialog{width:calc(100vw - 8px);max-width:none;max-height:calc(100vh - 8px);box-sizing:border-box;padding:8px}.channels-source-table-controls{flex-wrap:wrap}.channels-source-table-controls button{flex:1 1 auto}}
    `;
    document.head.append(style);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => setTimeout(build, 0), {once: true});
  } else {
    setTimeout(build, 0);
  }
})();
