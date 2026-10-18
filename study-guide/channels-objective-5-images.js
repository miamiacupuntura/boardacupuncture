(() => {
  'use strict';
  const root=document.getElementById('channels-objective-5');
  const visual=root?.querySelector('.objective-visual');
  if(!visual||document.getElementById('luo-pathway-gallery'))return;
  visual.replaceChildren();
  const make=(tag,text,parent,className)=>{const node=document.createElement(tag);if(text)node.textContent=text;if(className)node.className=className;if(parent)parent.append(node);return node};
  make('h4','Visual Learning — Luo-Connecting Channels',visual);
  make('p','Begin with the complete network, then open the individual channel sheets. Each sheet places the Luo pathway beside related channel systems so that you can identify the Luo branch without confusing it with the Primary, Divergent or Sinew pathway.',visual,'luo-gallery-intro');
  const data=[
    ['Complete Luo-Connecting network','overview.webp','General comparison of the Luo-Connecting pathways associated with the Primary Channels, Governing Vessel and Conception Vessel.','Use this overview to recognize the network as a set of connecting branches. It is a comparison map, not a depth sequence or an exact point-location guide.'],
    ['Du Mai — Governing Vessel','channel-07.webp','Governing Vessel Luo pathway sheet.','Follow the labeled Luo branch associated with Du Mai; keep it distinct from the main Governing Vessel pathway.'],
    ['Ren Mai — Conception Vessel','channel-08.webp','Conception Vessel Luo pathway sheet.','The Ren Mai sheet highlights its Luo distribution over the anterior trunk rather than presenting another Primary Channel pair.'],
    ['Lung — Hand Taiyin','channel-09.webp','Hand Taiyin Lung channel sheet including Luo, Divergent and Sinew pathways.','Locate the panel marked LUO first. The neighboring Divergent and Sinew drawings are included for comparison and should not be read as continuations of the Luo branch.'],
    ['Large Intestine — Hand Yangming','channel-10.webp','Hand Yangming Large Intestine channel sheet including its Luo pathway.','Identify where the Luo branch separates from the Primary Channel and compare its destination with the other pathways on the sheet.'],
    ['Stomach — Foot Yangming','channel-11.webp','Foot Yangming Stomach channel sheet including its Luo pathway.','Use the orange Luo point and the labeled branch to separate the connecting pathway from the broader Primary, Divergent and Sinew routes.'],
    ['Spleen — Foot Taiyin','channel-12.webp','Foot Taiyin Spleen channel sheet including its Luo pathway.','Read the ordinary Spleen Luo shown on this sheet separately from the Great Luo of the Spleen, which is an additional collateral discussed in the objective text.'],
    ['Heart — Hand Shaoyin','channel-13.webp','Hand Shaoyin Heart channel sheet including its Luo pathway.','The Luo pathway is related to its channel but is not identical to the channel’s complete superficial or deep course.'],
    ['Small Intestine — Hand Taiyang','channel-14.webp','Hand Taiyang Small Intestine channel sheet including its Luo pathway.','Trace only the area labeled LUO before comparing it with the Divergent and Sinew drawings.'],
    ['Urinary Bladder — Foot Taiyang','channel-15.webp','Foot Taiyang Urinary Bladder channel sheet including its Luo pathway.','The Luo branch is a connecting route; do not substitute the larger Primary Channel pathway simply because both belong to the Bladder system.'],
    ['Kidney — Foot Shaoyin','channel-16.webp','Foot Shaoyin Kidney channel sheet including its Luo pathway.','Use the branch point and destination labels to distinguish the Luo connection from the deep Kidney pathway.'],
    ['Pericardium — Hand Jueyin','channel-17.webp','Hand Jueyin Pericardium channel sheet including its Luo pathway.','Compare the Luo branch with the related Sanjiao connection without treating the colored organ region as the entire course of the collateral.'],
    ['Sanjiao — Hand Shaoyang','channel-18.webp','Hand Shaoyang Sanjiao channel sheet including its Luo pathway.','The sheet supports paired comparison with Pericardium while preserving the difference between a Luo branch and a general interior–exterior relationship.'],
    ['Gall Bladder — Foot Shaoyang','channel-19.webp','Foot Shaoyang Gall Bladder channel sheet including its Luo pathway.','Focus on the labeled Luo route at the lower limb before comparing it with the longer channel systems on the page.'],
    ['Liver — Foot Jueyin','channel-20.webp','Foot Jueyin Liver channel sheet including its Luo pathway.','Use this sheet to compare the Foot Jueyin Luo branch with its paired Gall Bladder relationship and with the non-Luo pathways shown nearby.']
  ];
  const gallery=make('div','',visual,'luo-pathway-gallery');gallery.id='luo-pathway-gallery';
  data.forEach(([title,file,alt,note],index)=>{
    const card=make('figure','',gallery,'visual-student luo-pathway-card');make(index===0?'h5':'h6',title,card);
    const img=make('img','',card);img.src=`assets/luo-connecting/${file}?v=6`;img.alt=alt;img.loading=index===0?'eager':'lazy';make('figcaption',note,card);
    const button=make('button','Enlarge image',card);button.type='button';
    const dialog=make('dialog','',document.body,'standard-board-dialog luo-image-dialog');dialog.setAttribute('aria-label',title);dialog.style.width='min(1180px,calc(100vw - 24px))';
    const close=make('button','Close / Cerrar',dialog);close.type='button';const large=img.cloneNode();large.loading='eager';dialog.append(large);
    const open=()=>{dialog.showModal();dialog.scrollTop=0;close.focus()};button.onclick=open;img.tabIndex=0;img.setAttribute('role','button');img.setAttribute('aria-label',`Enlarge ${title}`);img.onclick=e=>{e.stopPropagation();open()};img.onkeydown=e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();e.stopPropagation();open()}};close.onclick=()=>dialog.close();dialog.addEventListener('click',e=>{if(e.target===dialog)dialog.close()});dialog.onclose=()=>button.focus();
  });
  make('p','Source: user-supplied Channel Pathways PDF. These educational diagrams support pathway comparison; confirm exact point locations, terminology and clinical details in the assigned text.',visual,'luo-source-note');
  const style=make('style','',document.head);style.textContent=`
    .luo-gallery-intro,.luo-source-note{line-height:1.55}.luo-pathway-gallery{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:16px}
    .luo-pathway-card{display:flex;flex-direction:column;min-width:0;margin:0;padding:14px;border:1px solid #bfd7d6;border-radius:12px;background:#f8fcfb}.luo-pathway-card:first-child{grid-column:1/-1;background:#eef9f6}
    .luo-pathway-card h5,.luo-pathway-card h6{margin:0 0 10px;color:#124852;font-size:1rem}.luo-pathway-card img{display:block;width:100%;height:330px;object-fit:contain;background:#fff;border-radius:8px;cursor:zoom-in}.luo-pathway-card:first-child img{height:auto;max-height:680px}
    .luo-pathway-card figcaption{flex:1;margin:11px 0;line-height:1.45;color:#344f59}.luo-pathway-card button,.luo-image-dialog button{align-self:flex-start;border:0;border-radius:8px;padding:10px 15px;background:#087774;color:#fff;font-weight:800;cursor:pointer}
    .luo-image-dialog{max-height:94vh;overflow:auto;padding:18px;border:0;border-radius:12px}.luo-image-dialog::backdrop{background:rgba(9,31,38,.78)}.luo-image-dialog img{display:block;width:100%;max-width:none;height:auto;background:#fff}.luo-image-dialog button{position:sticky;z-index:2;top:0;margin:0 0 12px;background:#f47e2c;color:#17232a}
    @media(max-width:720px){.luo-pathway-gallery{grid-template-columns:1fr}.luo-pathway-card:first-child{grid-column:auto}.luo-pathway-card img{height:auto;max-height:520px}.luo-image-dialog{width:calc(100vw - 10px)!important;max-width:none;max-height:calc(100vh - 10px);box-sizing:border-box;padding:10px;border-radius:8px}.luo-image-dialog img{width:760px}.luo-image-dialog button{min-height:44px;padding:11px 16px}}
  `;
})();
