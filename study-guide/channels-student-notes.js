(() => {
  'use strict';

  const root = document.getElementById('student_notes_form');
  if (!root || root.dataset.notesWorkspace === 'ready') return;
  root.dataset.notesWorkspace = 'ready';
  root.addEventListener('submit', event => event.preventDefault());

  const container = root.querySelector(':scope > details') || root;
  const summary = container.querySelector(':scope > summary');
  [...container.children].filter(node => node !== summary).forEach(node => node.remove());

  const make = (tag, text, parent = container) => {
    const node = document.createElement(tag);
    if (text !== undefined) node.textContent = text;
    parent.append(node);
    return node;
  };

  const intro = make('p', 'Write personal notes and attach study images for the Channels and Collaterals chapter. Your work is restored when you return using the same browser and device.');
  intro.className = 'channels-notes-intro';
  const storageNotice = make('p', 'Private browser storage: these notes and images are not submitted for a grade and are not synchronized with Moodle.');
  storageNotice.className = 'channels-notes-storage-notice';

  const workspace = make('div', '');
  workspace.className = 'channels-notes-workspace';
  const noteLabel = make('label', 'Personal note', workspace);
  noteLabel.htmlFor = 'channels-personal-note';
  const note = make('textarea', '', workspace);
  note.id = 'channels-personal-note';
  note.placeholder = 'Write your personal notes, reminders, questions or clinical connections here...';
  note.rows = 10;

  const imageSection = make('section', '', workspace);
  imageSection.className = 'channels-notes-image-section';
  make('h4', 'Add study images', imageSection);
  const imageHelp = make('p', 'Choose up to 8 PNG, JPEG or WebP images. Each original file may be up to 5 MB. Large images are reduced for private browser storage.', imageSection);
  imageHelp.id = 'channels-notes-image-help';
  const file = make('input', '', imageSection);
  file.type = 'file';
  file.id = 'channels-note-images';
  file.accept = 'image/png,image/jpeg,image/webp';
  file.multiple = true;
  file.setAttribute('aria-describedby', imageHelp.id);

  const controls = make('div', '', imageSection);
  controls.className = 'channels-notes-controls';
  const upload = make('button', 'Add selected images', controls);
  upload.type = 'button';
  upload.className = 'channels-notes-upload';
  const save = make('button', 'Save Note', controls);
  save.type = 'button';
  save.className = 'channels-notes-save';
  upload.disabled = true;
  save.disabled = true;

  const status = make('p', 'Loading your saved note…', workspace);
  status.className = 'channels-notes-status';
  status.setAttribute('role', 'status');
  status.setAttribute('aria-live', 'polite');
  const gallery = make('div', '', workspace);
  gallery.className = 'channels-notes-gallery';
  gallery.setAttribute('aria-label', 'Attached study images');

  const DB_NAME = 'acupuncture-study-guide-notes';
  const STORE = 'chapter-notes';
  const RECORD = 'channels-collaterals';
  const MAX_IMAGES = 8;
  const MAX_FILE_SIZE = 5 * 1024 * 1024;
  const supportedTypes = ['image/png', 'image/jpeg', 'image/webp'];
  let db;
  let images = [];
  let objectUrls = [];

  const revokeObjectUrls = () => {
    objectUrls.forEach(url => URL.revokeObjectURL(url));
    objectUrls = [];
  };

  function openDatabase() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(DB_NAME, 1);
      request.onupgradeneeded = () => {
        if (!request.result.objectStoreNames.contains(STORE)) {
          request.result.createObjectStore(STORE, {keyPath: 'id'});
        }
      };
      request.onsuccess = () => resolve(request.result);
      request.onerror = () => reject(request.error);
    });
  }

  function getRecord() {
    return new Promise((resolve, reject) => {
      const request = db.transaction(STORE, 'readonly').objectStore(STORE).get(RECORD);
      request.onsuccess = () => resolve(request.result || null);
      request.onerror = () => reject(request.error);
    });
  }

  function putRecord() {
    return new Promise((resolve, reject) => {
      const transaction = db.transaction(STORE, 'readwrite');
      transaction.objectStore(STORE).put({
        id: RECORD,
        text: note.value,
        images,
        updatedAt: new Date().toISOString()
      });
      transaction.oncomplete = resolve;
      transaction.onerror = () => reject(transaction.error);
      transaction.onabort = () => reject(transaction.error);
    });
  }

  function render() {
    revokeObjectUrls();
    gallery.replaceChildren();
    if (!images.length) {
      const empty = make('p', 'No images attached yet.', gallery);
      empty.className = 'channels-notes-empty';
      return;
    }
    images.forEach((item, index) => {
      const card = make('article', '', gallery);
      card.className = 'channels-note-image-card';
      const url = URL.createObjectURL(item.blob);
      objectUrls.push(url);
      const image = make('img', '', card);
      image.src = url;
      image.alt = item.name ? `Study image: ${item.name}` : 'Attached study image';
      const meta = make('div', '', card);
      meta.className = 'channels-note-image-meta';
      const name = make('span', item.name || `Study image ${index + 1}`, meta);
      name.title = item.name || '';
      const remove = make('button', 'Delete image', meta);
      remove.type = 'button';
      remove.className = 'channels-note-image-delete';
      remove.setAttribute('aria-label', `Delete ${item.name || `study image ${index + 1}`}`);
      remove.addEventListener('click', async () => {
        images = images.filter(saved => saved.id !== item.id);
        render();
        try {
          await putRecord();
          status.textContent = 'Image deleted from this browser.';
        } catch (error) {
          status.textContent = 'The image was removed from this view, but the browser could not update storage.';
        }
      });
    });
  }

  function loadImage(source) {
    return new Promise((resolve, reject) => {
      const url = URL.createObjectURL(source);
      const image = new Image();
      image.onload = () => {
        URL.revokeObjectURL(url);
        resolve(image);
      };
      image.onerror = () => {
        URL.revokeObjectURL(url);
        reject(new Error(`${source.name} could not be read as an image.`));
      };
      image.src = url;
    });
  }

  function canvasBlob(canvas, type, quality) {
    return new Promise((resolve, reject) => {
      canvas.toBlob(blob => {
        if (blob) resolve(blob);
        else reject(new Error('Image conversion failed.'));
      }, type, quality);
    });
  }

  async function prepareImage(source) {
    if (source.size > MAX_FILE_SIZE) throw new Error(`${source.name} exceeds 5 MB.`);
    if (!supportedTypes.includes(source.type)) throw new Error(`${source.name} is not a supported image type.`);
    const sourceImage = await loadImage(source);
    const maxDimension = 1600;
    const scale = Math.min(1, maxDimension / Math.max(sourceImage.naturalWidth, sourceImage.naturalHeight));
    const canvas = document.createElement('canvas');
    canvas.width = Math.max(1, Math.round(sourceImage.naturalWidth * scale));
    canvas.height = Math.max(1, Math.round(sourceImage.naturalHeight * scale));
    canvas.getContext('2d').drawImage(sourceImage, 0, 0, canvas.width, canvas.height);
    const preservePng = source.type === 'image/png' && source.size < 1200000 && scale === 1;
    const type = preservePng ? 'image/png' : 'image/jpeg';
    const blob = await canvasBlob(canvas, type, preservePng ? undefined : 0.86);
    return {
      id: crypto.randomUUID ? crypto.randomUUID() : `${Date.now()}-${Math.random()}`,
      name: source.name,
      type,
      blob
    };
  }

  upload.addEventListener('click', async () => {
    const selected = [...file.files];
    if (!selected.length) {
      status.textContent = 'Choose one or more images first.';
      return;
    }
    if (images.length + selected.length > MAX_IMAGES) {
      status.textContent = `You may keep up to ${MAX_IMAGES} images in this chapter note.`;
      return;
    }
    upload.disabled = true;
    status.textContent = 'Preparing images…';
    try {
      const prepared = [];
      for (const selectedFile of selected) prepared.push(await prepareImage(selectedFile));
      images = [...images, ...prepared];
      await putRecord();
      file.value = '';
      render();
      status.textContent = `${prepared.length} image${prepared.length === 1 ? '' : 's'} added and saved in this browser.`;
    } catch (error) {
      status.textContent = error.message || 'The images could not be added.';
    } finally {
      upload.disabled = false;
    }
  });

  save.addEventListener('click', async () => {
    save.disabled = true;
    try {
      await putRecord();
      status.textContent = 'Note saved in this browser.';
    } catch (error) {
      status.textContent = 'The browser could not save this note. Copy the text before leaving the page.';
    } finally {
      save.disabled = false;
    }
  });

  const closeWorkspace = event => {
    revokeObjectUrls();
    if (!event.persisted) db?.close();
  };
  window.addEventListener('pagehide', closeWorkspace);
  window.addEventListener('pageshow', event => {
    if (event.persisted) render();
  });
  (async () => {
    try {
      db = await openDatabase();
      const record = await getRecord();
      if (record) {
        note.value = typeof record.text === 'string' ? record.text : '';
        images = Array.isArray(record.images)
          ? record.images.filter(image => image && image.blob instanceof Blob)
          : [];
      }
      render();
      upload.disabled = false;
      save.disabled = false;
      status.textContent = record ? 'Saved note restored from this browser.' : 'Ready for your note.';
    } catch (error) {
      render();
      status.textContent = 'Private browser storage is unavailable. Images and notes cannot be saved in this browser.';
    }
  })();

  const style = make('style', '', document.head);
  style.textContent = `
    #student_notes_form .channels-notes-storage-notice{padding:10px 12px;border-left:4px solid #d79a21;background:#fff7df;border-radius:6px}
    #student_notes_form .channels-notes-workspace label,#student_notes_form .channels-notes-image-section h4{display:block;font-weight:750;margin:16px 0 8px;color:#173f4c}
    #channels-personal-note{display:block;width:100%;min-height:220px;box-sizing:border-box;padding:14px;border:2px solid #425b63;border-radius:10px;font:inherit;line-height:1.55;resize:vertical;background:#fff}
    .channels-notes-image-section{margin-top:18px;padding-top:4px}.channels-notes-image-section input[type=file]{display:block;max-width:100%;margin:12px 0}
    .channels-notes-controls{display:flex;flex-wrap:wrap;gap:12px;margin:14px 0}.channels-notes-controls button,.channels-note-image-delete{border:0;border-radius:9px;padding:11px 16px;font-weight:750;cursor:pointer}
    .channels-notes-upload{background:#4e68e8;color:#fff}.channels-notes-save{background:#f47e2c;color:#fff}.channels-notes-controls button:disabled{opacity:.55;cursor:wait}
    .channels-notes-status{min-height:1.5em;font-weight:650;color:#185f5a}.channels-notes-gallery{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-top:14px}
    .channels-note-image-card{min-width:0;border:1px solid #c8d9dc;border-radius:10px;padding:10px;background:#f8fbfb}.channels-note-image-card img{display:block;width:100%;height:220px;object-fit:contain;background:#fff;border-radius:7px}
    .channels-note-image-meta{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:10px}.channels-note-image-meta span{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.92rem}
    .channels-note-image-delete{flex:0 0 auto;background:#9f2f2f;color:#fff;padding:8px 11px}.channels-notes-empty{grid-column:1/-1;color:#5d6f75;font-style:italic}
    @media(max-width:700px){.channels-notes-gallery{grid-template-columns:1fr}.channels-note-image-card img{height:auto;max-height:320px}.channels-notes-controls button{width:100%}}
  `;
})();
