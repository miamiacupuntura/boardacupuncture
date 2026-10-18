(() => {
  'use strict';
  const configs={
    'channels-objective-3':{
      title:'The Twelve Divergent Channels — where this fits in the outline',
      levels:[
        ['Foundational Theory','Understand the systems used to organize channel relationships.'],
        ['Channels and Collaterals','Distinguish the principal channel and collateral systems.'],
        ['The Twelve Divergent Channels','Recognize their pathways, convergence and clinical significance.'],
        ['Objective 3','Differentiate Yang and Yin Divergent patterns and interpret source-supported examples.']
      ],
      recognize:'Recall that Divergent Channels branch from Primary Channels but have no points of their own. Distinguish the usual Yang route from the Yin route and identify what each added connection helps explain.',
      trap:'Do not assign an acupuncture point to a Divergent Channel. BL57, BL58 and BL62 are Primary Channel points whose indications are partly explained by the Bladder Divergent pathway.',
      practice:'Read the requested relationship first: pathway structure, paired zangfu connection, head-and-face distribution, or explanation of a point indication. Then eliminate options that invent separate Divergent Channel points or treat every pathway as identical.'
    },
    'channels-objective-4':{
      title:'The Eight Extraordinary Vessels — where this fits in the outline',
      levels:[
        ['Foundational Theory','Understand the systems that organize and regulate the channel network.'],
        ['Channels and Collaterals','Distinguish Primary, Divergent, Extraordinary and collateral systems.'],
        ['The Eight Extraordinary Vessels','Recognize their names, functions, relationships and selected pathways.'],
        ['Objective 4','Differentiate Ren, Du, Chong, Dai, Qiao and Wei relationships using the supplied source.']
      ],
      recognize:'Name all eight vessels; identify Ren and Du as the only two with points of their own; compare reservoir, linking and protective functions; and distinguish the paired ideas Yin/Yang, quietness/activity and interior/exterior.',
      trap:'Do not confuse a vessel’s single limb confluent point with its multiple coalescent points. Do not claim that the six other vessels possess independently numbered points.',
      practice:'First identify the vessel, then ask whether the item tests points, a network relationship, a function or a pathway. Use the paired contrasts and eliminate answers that reverse Ren/Du, Yin/Yang Qiao or Yin/Yang Wei.'
    },
    'channels-objective-5':{
      title:'The Luo-Connecting Channels — where this fits in the outline',
      levels:[
        ['Foundational Theory','Understand how channels communicate within the network.'],
        ['Channels and Collaterals','Distinguish Primary Channels from their connecting collateral pathways.'],
        ['The Luo-Connecting Channels','Recognize the standard count, origins, paired-channel connections and named destinations.'],
        ['Objective 5','Differentiate the twelve Primary-Channel Luo and the three additional principal Luo pathways.']
      ],
      recognize:'Recall the standard fifteen principal Luo-Connecting Channels: twelve associated with the Primary Channels, one from Ren, one from Du and the Great Luo of the Spleen. Match each pathway with its Luo point, paired channel and distinctive continuation.',
      trap:'Do not replace the standard answer of fifteen with the qualified Stomach Great Luo reference unless the question specifically invokes that classical passage. Also distinguish a Luo-Connecting point from the pathway that arises from it.',
      practice:'Identify whether the stem asks for a number, an origin point, an interior–exterior connection, a later pathway destination or a function. Eliminate answers that confuse a Primary Channel route with its Luo continuation.'
    },
    'channels-objective-6':{
      title:'The Twelve Sinew Channels — where this fits in the outline',
      levels:[
        ['Foundational Theory','Understand the channel systems that distribute influence through different tissue levels.'],
        ['Channels and Collaterals','Distinguish Primary, Luo, Divergent, Extraordinary and Sinew systems.'],
        ['The Twelve Sinew Channels','Recognize their peripheral, superficial and musculotendinous organization.'],
        ['Objective 6','Differentiate shared Sinew Channel features, direction, pathway regions and clinical access.']
      ],
      recognize:'All twelve originate at the extremities and ascend toward the trunk or head. They broadly follow their associated Primary Channels as wider superficial distributions involving muscles, tendons and ligaments, and they do not penetrate the zangfu.',
      trap:'Do not reverse their direction by copying the circulation direction of a Primary Channel. Do not assign them independent acupuncture points or internal zangfu penetration.',
      practice:'First ask which limb surface and muscular region are involved, then follow the pathway from distal origin upward through its binding and converging regions. For clinical questions, distinguish shallow or superficial access from an invented Sinew-specific point list.'
    },
    'channels-objective-7':{
      title:'The Minute Collaterals — where this fits in the outline',
      levels:[
        ['Foundational Theory','Understand how qi and blood are distributed through the channel network.'],
        ['Channels and Collaterals','Recognize the hierarchy from major pathways to progressively finer branches.'],
        ['The Minute Collaterals','Identify their limited classical description and essential distribution function.'],
        ['Objective 7','Distinguish a source-supported function from a proposed historical origin.']
      ],
      recognize:'The Minute Collaterals are the progressively finer ramifications needed for qi and blood to reach every part of the body. Classical discussion is limited, and the idea that they may reflect observation of small superficial vessels is presented only as a possibility.',
      trap:'Do not describe the proposed superficial-vessel origin as proven, equate Minute Collaterals directly with modern blood vessels, or invent an independent set of named points and longitudinal routes.',
      practice:'When reading the stem, identify whether it asks about evidence or function. Choose progressive branching and fine distribution for function questions; retain cautious language such as “possibly” for questions about historical origin.'
    },
    'channels-objective-8':{
      title:'The Twelve Cutaneous Regions — where this fits in the outline',
      levels:[
        ['Foundational Theory','Understand the relationship between exterior, surface and deeper channel layers.'],
        ['Channels and Collaterals','Recognize broad skin territories linked with superficial channel networks.'],
        ['The Twelve Cutaneous Regions','Interpret exterior invasion, surface manifestations and traditional color associations.'],
        ['Objective 8','Distinguish a skin region from a channel and connect surface findings with the underlying network.']
      ],
      recognize:'Cutaneous Regions are not channels; they are skin territories overlying broad superficial channel networks. Exterior factors may enter through the skin toward deeper jingluo layers, while deeper disturbances may appear at the surface as abnormal sensation, lesions or discoloration. Memorize blue-green for pain, red for heat and white for deficiency and cold.',
      trap:'Do not assign independent pathways, zangfu penetration or separate acupuncture points to the Cutaneous Regions. Do not reverse the color associations or treat a color or lesion as a stand-alone biomedical diagnosis.',
      practice:'First decide whether the question asks about identity, direction of influence, a surface manifestation, color correspondence or treatment rationale. Then eliminate answers that turn a linked skin territory into a separate channel.'
    },
    'channels-objective-9':{
      title:'Integrated Chapter Review — where this fits in the outline',
      levels:[
        ['Foundational Theory','Understand the network of channels, collaterals, vessels and surface regions.'],
        ['Channels and Collaterals','Compare the principal systems rather than studying one pathway in isolation.'],
        ['Integrated Chapter Review','Use identity, depth, points, zangfu relationships and function to distinguish the systems.'],
        ['Objective 9','Apply Deadman’s superficial-to-deep sequence and cumulative comparison without inventing a ninth system.']
      ],
      recognize:'Identify each system from its defining feature and recall the source sequence from Cutaneous Regions at the surface to the deep pathways of the Primary and Divergent Channels. Distinguish a channel, collateral, extraordinary vessel and skin territory.',
      trap:'Objective 9 is a course review structure, not a ninth channel system or a verbatim Deadman heading. Relative depth is a traditional teaching sequence, not a fixed modern anatomical measurement.',
      practice:'Classify the clue first: territory, fine ramification, musculotendinous route, connecting pathway, main channel, deeper branch or extraordinary vessel. Then verify the number, points, zangfu relationship and function before choosing.'
    }
  };
  const mountBoardButtons=()=>Object.entries(configs).forEach(([id,cfg])=>{
    const root=document.getElementById(id);if(!root||root.dataset.boardModal==='complete')return;
    const old=root.querySelector('.objective-introduction .board-connection-objective');if(!old)return;
    root.dataset.boardModal='complete';
    const button=document.createElement('button');button.type='button';button.className='channels-board-trigger';button.textContent='How does this connect to the Board?';
    const dialog=document.createElement('dialog');dialog.className='channels-board-dialog';dialog.setAttribute('aria-labelledby',`${id}-board-title`);
    const panel=document.createElement('section');panel.className='channels-board-panel';
    const title=document.createElement('h3');title.id=`${id}-board-title`;title.textContent=cfg.title;
    const intro=document.createElement('p');intro.textContent='Follow these four levels to understand where this objective fits:';
    const list=document.createElement('ol');list.className='channels-board-path';
    cfg.levels.forEach(([name,description])=>{const item=document.createElement('li');const strong=document.createElement('strong');strong.textContent=name;const text=document.createElement('p');text.textContent=description;item.append(strong,text);list.append(item)});
    const explanation=document.createElement('p');explanation.textContent='Connect the pathway description with the relationship or point indication requested. The fourth level identifies the study topic; it does not assign an official outline code.';
    const addSection=(heading,text)=>{const h=document.createElement('h4');h.textContent=heading;const p=document.createElement('p');p.textContent=text;panel.append(h,p)};
    const scope=document.createElement('p');scope.className='channels-board-scope';scope.textContent='Board-study connection; these examples are not official examination questions or claims about question frequency.';
    const close=document.createElement('button');close.type='button';close.className='channels-board-close';close.textContent='← Return to the objective';
    panel.append(title,intro,list,explanation);addSection('What to recognize',cfg.recognize);addSection('Common trap',cfg.trap);addSection('Practice approach',cfg.practice);panel.append(scope,close);dialog.append(panel);
    old.replaceWith(button,dialog);
    button.addEventListener('click',()=>dialog.showModal());close.addEventListener('click',()=>dialog.close());dialog.addEventListener('click',event=>{if(event.target===dialog)dialog.close()});
  });
  mountBoardButtons();
  setTimeout(mountBoardButtons,0);
  if(document.getElementById('channels-board-modal-styles'))return;
  const style=document.createElement('style');style.id='channels-board-modal-styles';style.textContent=`
    .channels-board-trigger{min-height:44px;margin:14px 0;padding:10px 17px;border:2px solid #df681c;border-radius:10px;background:#ef7e2d;color:#17232a;font-weight:800;cursor:pointer}
    .channels-board-trigger:hover,.channels-board-trigger:focus-visible{background:#ff913f}
    .channels-board-dialog{width:min(760px,calc(100vw - 32px));max-height:86vh;margin:auto;padding:0;border:1px solid #9ebdbc;border-radius:14px;background:#fff;color:#122b3a;overflow:auto;box-shadow:0 18px 60px rgba(0,32,45,.35)}
    .channels-board-dialog::backdrop{background:rgba(20,43,50,.64)}
    .channels-board-panel{padding:24px 20px}.channels-board-panel h3{margin-top:0}.channels-board-panel h4{margin:18px 0 6px;font-size:.9rem}
    .channels-board-path{padding-left:25px}.channels-board-path li{margin:8px 0;padding:11px 12px;border-left:4px solid #078684;border-radius:6px;background:#edf7f6}.channels-board-path li strong{display:block}.channels-board-path li p{margin:5px 0 0}
    .channels-board-scope{font-size:.82rem;color:#526872}.channels-board-close{padding:10px 15px;border:2px solid #fff;outline:2px solid #168b9d;border-radius:9px;background:#ff7f22;font-weight:800;cursor:pointer}
  `;document.head.append(style);
})();
