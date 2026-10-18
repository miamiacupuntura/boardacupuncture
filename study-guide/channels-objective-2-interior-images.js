(() => {
  'use strict';
  const root=document.getElementById('channels-objective-2');
  const examples=root?.querySelector('.interior-pathway-examples');
  if(!examples||document.getElementById('primary-interior-image-gallery'))return;
  const gallery=document.createElement('section');gallery.id='primary-interior-image-gallery';gallery.className='primary-interior-image-gallery';
  const heading=document.createElement('h5');heading.textContent='Interior Pathway Examples — Visual Gallery';
  const intro=document.createElement('p');intro.textContent='Compare the visible channel or point cue with the deeper connection used to explain the traditional indication. Select any image to enlarge it.';
  const warning=document.createElement('p');warning.className='interior-image-warning';warning.textContent='Learning diagrams only: pathway lines and point markers are schematic. Use the individual channel and point-location chapters for precise study.';
  const grid=document.createElement('div');grid.className='primary-interior-image-grid';
  const items=[
    ['Lung Channel — Middle Jiao and Stomach region','assets/primary-interior-lung-middle-jiao.webp?v=1','Conceptual diagram of the Lung Channel originating in the Middle Jiao near the Stomach, rising through the diaphragm to the Lung and continuing toward the arm.','This diagram links the Lung Channel’s Middle Jiao origin with the source-listed relationship to nausea and vomiting.'],
    ['Stomach Channel — DU24, DU26 and the brain','assets/primary-interior-stomach-du-brain.webp?v=1','Conceptual head diagram showing the Stomach Channel meeting the Governing Vessel at DU24 Shenting and DU26 Renzhong, with the Du Vessel related to the brain.','This connection is used in the source to help explain spirit-related actions of Stomach Channel points.'],
    ['LI4 Hegu — Large Intestine and Gall Bladder meeting','assets/primary-interior-li-gb-head.webp?v=2','Conceptual diagram showing LI4 Hegu, the Large Intestine Channel approaching the face, and meetings with the Gall Bladder Channel at GB14, GB5 and GB6.','The meeting helps explain the source-listed affinity of LI4 for frontal and temporal head pain.'],
    ['PC6 Neiguan — Pericardium pathway through the Middle Jiao','assets/primary-interior-pc6-middle-jiao.webp?v=1','Conceptual diagram showing PC6 Neiguan and the Pericardium Channel descending through the chest to the Middle Jiao and Stomach region.','This deep pathway is used to explain the traditional relationship of PC6 with nausea and vomiting.']
  ];
  items.forEach(([title,src,alt,caption])=>{
    const figure=document.createElement('figure');figure.className='visual-student primary-interior-image-card';
    const label=document.createElement('h6');label.textContent=title;
    const image=document.createElement('img');image.src=src;image.alt=alt;image.loading='lazy';
    const text=document.createElement('figcaption');text.textContent=caption;
    const button=document.createElement('button');button.type='button';button.textContent='Enlarge image';button.addEventListener('click',()=>image.click());
    figure.append(label,image,text,button);grid.append(figure);
  });
  gallery.append(heading,intro,warning,grid);examples.after(gallery);
  const style=document.createElement('style');style.textContent=`
    #primary-interior-image-gallery{margin:18px 0;padding:16px;border:1px solid #bfd5d3;border-radius:14px;background:#f7fbfb}
    #primary-interior-image-gallery>h5{margin:0 0 7px;color:#075f60;font-size:1.08rem}
    #primary-interior-image-gallery .interior-image-warning{padding:11px 13px;border-left:4px solid #ef7e2d;border-radius:7px;background:#fff8ef}
    .primary-interior-image-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px;margin-top:15px}
    .primary-interior-image-card{display:flex;min-width:0;flex-direction:column;margin:0;padding:13px;border:1px solid #c6d9d7;border-radius:11px;background:#fff;box-shadow:0 6px 16px rgba(11,64,74,.07)}
    .primary-interior-image-card h6{margin:0 0 10px;color:#163f50;font-size:1rem;line-height:1.4}
    .primary-interior-image-card img{display:block;width:100%;height:auto;border-radius:8px;cursor:zoom-in}
    .primary-interior-image-card figcaption{margin:11px 0;line-height:1.5;color:#39535e}
    .primary-interior-image-card button{align-self:flex-start;margin-top:auto;padding:9px 14px;border:0;border-radius:8px;background:#087774;color:#fff;font-weight:800;cursor:pointer}
    .primary-interior-image-card button:hover,.primary-interior-image-card button:focus-visible{background:#056461}
    @media(max-width:760px){.primary-interior-image-grid{grid-template-columns:1fr}}
  `;document.head.append(style);
})();
