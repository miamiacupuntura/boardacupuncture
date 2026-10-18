(() => {
  'use strict';

  const objective = document.getElementById('channels-objective-1');
  const visual = objective?.querySelector('.objective-visual');
  const existingGrid = visual?.querySelector('.channel-functions-visual-grid');
  if (!visual || !existingGrid || document.getElementById('integrated-whole-visual')) return;

  const figure = document.createElement('figure');
  figure.id = 'integrated-whole-visual';
  figure.className = 'integrated-whole-visual visual-student';

  const title = document.createElement('h5');
  title.textContent = 'Function 1 — Transporting Qi and Blood: the body as an integrated whole';

  const image = document.createElement('img');
  image.src = 'assets/channel-functions-integrated-whole.webp?v=20261015-1';
  image.alt = 'Conceptual human-tree diagram showing zangfu as roots, channels as stems, tissues and sense organs as flowers, and relationships between interior and exterior, above and below, left and right, and zangfu with zangfu.';
  image.loading = 'lazy';

  const actions = document.createElement('div');
  actions.className = 'integrated-whole-actions';
  const enlarge = document.createElement('button');
  enlarge.type = 'button';
  enlarge.textContent = 'Enlarge image';
  actions.append(enlarge);

  const caption = document.createElement('figcaption');
  caption.innerHTML = '<strong>How to read it:</strong> roots represent the zangfu, stems represent the channel network, and flowers represent the tissues and sense organs nourished by Qi and Blood. The surrounding arrows emphasize interior–exterior, above–below, left–right and zangfu–zangfu integration. This is a traditional functional model, not a literal anatomical map.';

  enlarge.addEventListener('click', () => image.click());

  figure.append(title, image, actions, caption);
  existingGrid.before(figure);

  const gallery = document.createElement('section');
  gallery.className = 'channel-functions-image-gallery';
  const galleryTitle = document.createElement('h5');
  galleryTitle.textContent = 'Detailed Visual Gallery — Four Functions of the Channels';
  const galleryIntro = document.createElement('p');
  galleryIntro.textContent = 'Open each panel only when you are ready to compare that function. Every diagram uses the standard viewer with Close, Zoom in and Fit image controls.';
  gallery.append(galleryTitle, galleryIntro);

  const galleryItems = [
    {
      title: 'Functions 1 and 2 — Transport, integration and protection',
      src: 'assets/channel-functions-transport-protection.webp?v=20261016-1',
      alt: 'Two-panel conceptual diagram comparing transport and integration of Qi and Blood with protection against progressively deeper exterior penetration.',
      caption: 'Compare distribution throughout an integrated network with the traditional description of successive protective layers.'
    },
    {
      title: 'Function 3 — Four ways channels respond to dysfunction',
      src: 'assets/channel-functions-dysfunction.webp?v=20261016-1',
      alt: 'Four-panel diagram showing disease of a channel, zangfu disharmony reflecting onto a channel, transmission through the channel network, and visible pathway changes.',
      caption: 'Keep the four responses separate: a local channel sign does not automatically establish a zangfu diagnosis.'
    },
    {
      title: 'Function 4 — Transmission of therapeutic stimulation',
      src: 'assets/channel-functions-therapeutic-transmission.webp?v=20261016-1',
      alt: 'Three-stage diagram showing application of a stimulus, traditional transmission through a channel and response attributed in a related area.',
      caption: 'This diagram distinguishes ongoing transport from the response traditionally attributed to deliberate stimulation.'
    }
  ];

  galleryItems.forEach((item) => {
    const panel = document.createElement('details');
    panel.className = 'channel-gallery-panel visual-student';
    const summary = document.createElement('summary');
    summary.textContent = item.title;
    const panelImage = document.createElement('img');
    panelImage.src = item.src;
    panelImage.alt = item.alt;
    panelImage.loading = 'lazy';
    const panelCaption = document.createElement('p');
    panelCaption.textContent = item.caption;
    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'channel-gallery-enlarge';
    toggle.textContent = 'Enlarge image';
    toggle.addEventListener('click', () => panelImage.click());
    panel.append(summary, panelImage, panelCaption, toggle);
    gallery.append(panel);
  });
  existingGrid.before(gallery);

  const style = document.createElement('style');
  style.textContent = `
    .integrated-whole-visual{margin:16px 0 22px;padding:16px;background:#fff;border:1px solid #c9dcda;border-radius:14px;box-shadow:0 8px 20px rgba(11,64,74,.08)}
    .integrated-whole-visual h5{margin:0 0 12px;color:#074f65;font-size:1.08rem}
    .integrated-whole-visual img{display:block;width:100%;height:auto;border-radius:10px;cursor:zoom-in}
    .integrated-whole-actions{display:flex;gap:10px;margin:12px 0}
    .integrated-whole-actions button{padding:10px 16px;border:0;border-radius:9px;background:#087774;color:#fff;font-weight:700;cursor:pointer}
    .integrated-whole-visual figcaption{line-height:1.55;color:#294b59}
    .channel-functions-image-gallery{margin:18px 0 24px;padding:16px;background:#f7fbfb;border:1px solid #c9dcda;border-radius:14px}
    .channel-functions-image-gallery>h5{margin:0 0 6px;color:#074f65;font-size:1.08rem}
    .channel-gallery-panel{margin-top:12px;border:1px solid #bfd5d3;border-radius:10px;background:#fff;overflow:hidden}
    .channel-gallery-panel summary{padding:13px 15px;background:#eaf5f4;color:#075f60;font-weight:800;cursor:pointer}
    .channel-gallery-panel img{display:block;width:100%;height:auto;margin:0;cursor:zoom-in}
    .channel-gallery-panel p{margin:12px 15px 8px;line-height:1.5}
    .channel-gallery-enlarge{display:inline-block;margin:0 15px 15px;padding:10px 16px;border:0;border-radius:9px;background:#087774;color:#fff;font-weight:700;cursor:pointer}
  `;
  document.head.append(style);
})();
