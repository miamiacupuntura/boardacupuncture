(() => {
  'use strict';
  const host=document.getElementById('pc-image');
  if(!host||document.getElementById('recreated-point-category-tables'))return;
  const images=[
    ['Objective 1 — Five Shu Points','The complete five-point sequence for all Twelve Primary Channels.','five-shu-points.svg'],
    ['Objective 1 — Yin and Yang Five Phase Correspondences','Compare the different Five Phase sequence used by Yin and Yang channels.','five-shu-yin-yang-elements.svg'],
    ['Objective 1 — Actions of the Five Shu Points','Compare the source-listed classical actions without treating the three source columns as interchangeable.','five-shu-actions.svg'],
    ['Objective 1 — Mother, Horary and Child Points','Compare three distinct Five Element relationships across all Twelve Primary Channels.','five-element-relationships.svg'],
    ['Objective 7 — Lower He-Sea Points','The six Fu and their Lower He-Sea correspondences.','lower-he-sea-points.svg'],
    ['Objective 9 — Mother Points','The Mother point of every Primary Channel.','mother-points.svg'],
    ['Objective 10 — Child Points','The Child point of every Primary Channel.','child-points.svg'],
    ['Objective 11 — Xi-Cleft Points','The twelve Primary Channel Xi-Cleft correspondences.','xi-cleft-points.svg'],
    ['Objective 13 — Yuan-Source Points','The Yuan-Source point of every Primary Channel.','yuan-source-points.svg'],
    ['Objective 14 — Luo-Connecting Points','The Luo-Connecting point of every Primary Channel.','luo-connecting-points.svg'],
    ['Objective 15 — Group Luo Points','The four three-channel groups and their Group Luo points.','group-luo-points.svg'],
    ['Objective 16 — Back-Shu Points','The zangfu and their Back-Shu correspondences on the Bladder Channel.','back-shu-points.svg'],
    ['Objective 17 — Front-Mu Points','The zangfu and their Front-Mu correspondences.','front-mu-points.svg'],
    ['Objective 18 — Eight Hui-Meeting Points','The eight tissues, substances and organ groupings with their Hui-Meeting points.','hui-meeting-points.svg'],
    ['Objective 19 — Eight Confluent Points','The Extraordinary Vessels and their limb confluent points.','confluent-points.svg'],
    ['Objective 20 — Twelve Heavenly Star Points','Ma Dan-yang’s twelve-point grouping, organized as six pairs.','heavenly-star-points-table.svg'],
    ['Objective 21 — Four and Six Command Points','The classical four Command correspondences and the expanded six-point list.','command-points.svg'],
    ['Objective 22 — The Four Seas','Points and source-listed fullness and emptiness patterns for each Sea.','four-seas.svg'],
    ['Objective 23 — Window of Heaven Points','Compare the point lists associated with Lingshu chapters 21 and 2 and their principal source themes.','window-of-heaven-points.svg'],
    ['Objective 24 — Thirteen Ghost Points','Sun Si-miao’s traditional Ghost Point grouping.','thirteen-ghost-points.svg'],
    ['Objective 24 — Nine Needles for Returning Yang','The traditional nine-point Returning Yang grouping.','nine-needles-returning-yang.svg'],
    ['Objective 24 — Points of the Eye System','Periorbital, temporal and occipital points organized around the eye-system network.','eye-system-points.svg'],
    ['Objective 25 — Crossing Points · Part 2','The continuation of the source matrix, comparing intersections with the Twelve Primary Channels and Eight Extraordinary Vessels.','crossing-points-part-2.svg'],
    ['Objective 26 — Entry and Exit Points','The Entry–Exit convention used in this chapter.','entry-exit-points.svg']
  ];
  const details=document.createElement('details');details.id='recreated-point-category-tables';details.className='recreated-table-library';details.open=false;
  details.innerHTML='<summary>Recreated Source Tables — Image Gallery</summary><p>These clean study images recreate the supplied tables as separate, readable panels. Select any image to enlarge it. The tables support recall and comparison; they are not point-location or treatment guides.</p>';
  const grid=document.createElement('div');grid.className='recreated-table-grid';details.append(grid);host.append(details);
  const dialog=document.createElement('dialog');dialog.className='recreated-table-dialog';dialog.setAttribute('aria-label','Recreated Point Categories table');
  const controls=document.createElement('div');controls.className='recreated-table-dialog-controls';
  const close=document.createElement('button');close.type='button';close.textContent='Close';
  const zoom=document.createElement('button');zoom.type='button';zoom.textContent='Zoom in';
  const fit=document.createElement('button');fit.type='button';fit.textContent='Fit image';controls.append(close,zoom,fit);
  const heading=document.createElement('h4'),full=document.createElement('img'),caption=document.createElement('p');dialog.append(controls,heading,full,caption);document.body.append(dialog);
  let scale=1;const apply=()=>{full.style.width=scale===1?'auto':`${scale*100}%`;full.style.maxWidth=scale===1?'100%':'none'};
  close.onclick=()=>dialog.close();zoom.onclick=()=>{scale=Math.min(3,scale+.5);apply()};fit.onclick=()=>{scale=1;apply()};dialog.addEventListener('click',e=>{if(e.target===dialog)dialog.close()});
  for(const [title,text,file] of images){
    const card=document.createElement('article');card.className='recreated-table-card';
    const h=document.createElement('h5');h.textContent=title;
    const button=document.createElement('button');button.type='button';button.className='recreated-table-preview';button.setAttribute('aria-label',`Enlarge ${title}`);
    const img=document.createElement('img');img.src=`assets/point-category-tables/${file}?v=1`;img.alt=`${title}. ${text}`;img.loading='lazy';button.append(img);
    const p=document.createElement('p');p.textContent=text;
    const action=document.createElement('span');action.textContent='Enlarge table';action.className='recreated-table-action';card.append(h,button,p,action);grid.append(card);
    const open=()=>{scale=1;heading.textContent=title;full.src=img.src;full.alt=img.alt;caption.textContent=text;apply();dialog.showModal()};button.onclick=open;action.onclick=open;action.tabIndex=0;action.setAttribute('role','button');action.onkeydown=e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();open()}};
  }
  const style=document.createElement('style');style.textContent=`.recreated-table-library{margin:22px 0;padding:0 16px 16px;border:1px solid #bdd8da;border-radius:12px;background:#f8fcfc}.recreated-table-library>summary{margin:0 -16px;padding:14px 16px;border-radius:12px;background:#e8f4f4;color:#174f60;font-size:1.08rem;font-weight:800;cursor:pointer}.recreated-table-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.recreated-table-card{display:flex;min-width:0;flex-direction:column;padding:13px;border:1px solid #c9dddc;border-radius:10px;background:#fff}.recreated-table-card h5{margin:0 0 10px;color:#173f50}.recreated-table-preview{display:block;width:100%;height:250px;padding:0;border:0;border-radius:8px;overflow:hidden;background:#eef7f6;cursor:zoom-in}.recreated-table-preview img{width:100%;height:100%;object-fit:contain}.recreated-table-card p{margin:11px 0}.recreated-table-action{margin-top:auto;color:#087774;font-weight:800;text-decoration:underline;cursor:pointer}.recreated-table-dialog{width:min(96vw,1500px);max-height:94vh;padding:16px;border:0;border-radius:13px;box-shadow:0 22px 70px rgba(0,0,0,.4);overflow:auto}.recreated-table-dialog::backdrop{background:rgba(7,30,40,.76)}.recreated-table-dialog-controls{position:sticky;top:0;z-index:2;display:flex;gap:9px;padding:7px;background:#fff}.recreated-table-dialog-controls button{padding:9px 14px;border:1px solid #87aaa9;border-radius:7px;background:#fff;font-weight:700}.recreated-table-dialog h4{text-align:center}.recreated-table-dialog img{display:block;height:auto;margin:12px auto;transition:width .15s ease}.recreated-table-dialog p{text-align:center}@media(max-width:760px){.recreated-table-grid{grid-template-columns:1fr}.recreated-table-preview{height:auto;min-height:180px}.recreated-table-dialog{width:96vw;padding:10px}}`;document.head.append(style);
})();
