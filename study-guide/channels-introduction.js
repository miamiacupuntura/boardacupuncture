(() => {
  'use strict';

  const header = document.getElementById('sgv2-introduction');
  const plan = document.getElementById('sgv2-formb');
  if (!header || !plan || document.getElementById('channels-chapter-introduction')) return;

  const make = (tag, text, parent, className) => {
    const node = document.createElement(tag);
    if (text) node.textContent = text;
    if (className) node.className = className;
    if (parent) parent.append(node);
    return node;
  };

  const introduction = make('section', '', null, 'sgv2-card channels-chapter-introduction');
  introduction.id = 'channels-chapter-introduction';
  make('h3', 'Introduction — Understanding Jingluo', introduction);
  make('p', '“Channels and collaterals” translates the Chinese term jingluo. The expression describes an interconnected network rather than only the lines commonly shown on an acupuncture chart.', introduction, 'channels-intro-lead');

  const comparison = make('div', '', introduction, 'jingluo-comparison');
  const jing = make('article', '', comparison, 'jingluo-card jing-card');
  make('h4', 'Jing — the principal channels', jing);
  make('p', 'Jing carries a geographical sense of a channel or longitude. In the tree analogy, the jing are the trunk and main branches: comparatively deep, generally longitudinal pathways that connect with the zangfu.', jing);
  const jingList = make('ul', '', jing);
  ['Twelve Primary Channels', 'Eight Extraordinary Vessels', 'Twelve Divergent Channels'].forEach(item => make('li', item, jingList));

  const luo = make('article', '', comparison, 'jingluo-card luo-card');
  make('h4', 'Luo — the connecting network', luo);
  make('p', 'Luo means “to attach” or “a net.” It describes finer and generally more superficial branches that interconnect the principal pathways with connective tissues and cutaneous regions.', luo);
  const luoList = make('ul', '', luo);
  ['Twelve Luo-Connecting Channels of the Primary Channels', 'Luo channels of the Conception and Governing Vessels', 'Great Luo-Connecting Channel of the Spleen', 'Myriad Minute Collaterals'].forEach(item => make('li', item, luoList));

  const scope = make('div', '', introduction, 'channels-network-scope');
  make('h4', 'A network larger than the standard channel chart', scope);
  make('p', 'The complete framework also includes the Twelve Sinew Channels and Twelve Cutaneous Regions. A familiar chart usually displays only the superficial courses of the Twelve Primary Channels; it does not portray the full network or its internal branches. The tree image helps: trunks and major branches establish the structure, while progressively finer branches extend throughout the body.', scope);
  make('p', 'Within traditional Chinese medicine, this system organizes relationships among the zangfu, Qi and Blood, skin, muscles, flesh, sinews, bones, limbs and sense organs. It should be studied as a traditional functional map, not treated as a literal substitute for nerves, blood vessels or modern anatomical structures.', scope, 'channels-intro-caution');

  const history = make('details', '', introduction, 'channels-history');
  make('summary', 'How may channel theory have developed?', history);
  make('p', 'Two broad explanations are commonly discussed. One proposes that tender or therapeutically responsive sites were recognized first and later organized into channel pathways. The other proposes that perceived pathway sensations—through massage, contemplative practice or qigong—were recognized first, with individual points identified later.', history);
  make('p', 'The Mawangdui silk manuscripts from a Western Han tomb are important to this discussion because they describe eleven channel pathways without presenting a system of individual acupuncture points. This finding supports the possibility that pathway concepts preceded the mature point system, although it does not by itself settle the entire historical question.', history);

  make('p', 'Reference: Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Channels and Collaterals: Introduction” and “History of Channel Theory.”', introduction, 'source-note');
  plan.before(introduction);

  const style = make('style', '', document.head);
  style.textContent = `
    .channels-chapter-introduction{border-top:4px solid #087774}
    .channels-intro-lead{font-size:1.06rem;line-height:1.65}
    .jingluo-comparison{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin:18px 0}
    .jingluo-card{padding:18px;border-radius:12px;border:1px solid #c8dcda}
    .jing-card{background:#eef8f7;border-top:5px solid #087774}
    .luo-card{background:#fff7ed;border-top:5px solid #ef7e2d}
    .jingluo-card h4,.channels-network-scope h4{margin-top:0}
    .channels-network-scope{padding:16px 18px;background:#f6f8fb;border-left:4px solid #245a82;border-radius:10px}
    .channels-intro-caution{font-weight:600;color:#274759}
    .channels-history{margin-top:16px;padding:12px 16px;background:#f4faf9;border:1px solid #c8dcda;border-radius:10px}
    .channels-history summary{font-weight:800;color:#075f60;cursor:pointer}
    @media(max-width:720px){.jingluo-comparison{grid-template-columns:1fr}}
  `;
})();
