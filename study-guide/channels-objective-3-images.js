(() => {
  'use strict';
  const section=document.querySelector('#channels-objective-3 .objective-visual');
  if(!section||document.getElementById('divergent-image-gallery'))return;
  section.querySelector('.divergent-models')?.remove();
  const gallery=document.createElement('div');gallery.id='divergent-image-gallery';gallery.className='divergent-image-gallery';
  const items=[
    ['Yang and Yin Divergent Patterns','assets/divergent-yang-yin-patterns.webp?v=1','Comparative diagram showing the recurring Yang Divergent and Yin Divergent pathway patterns.','Compare where each pathway separates, what it connects internally and how it rejoins the Yang channel system.','Interpretation: both pathways separate on a limb, but the Yang pattern emphasizes paired zangfu and an upper-body return, while the Yin pattern reaches the Yang system by converging with its paired Yang Divergent.'],
    ['Four Functions of the Divergent Channels','assets/divergent-four-functions.webp?v=1','Four-part diagram presenting the principal functions of the Divergent Channels.','Use the four numbered panels to distinguish relationship, distribution, integration and clinical explanation.','Interpretation: the four panels move from strengthening paired relationships to extending distribution, adding otherwise missing connections and explaining why a Primary Channel point may affect a distant region.'],
    ['Clinical Relationships Explained by Divergent Pathways','assets/divergent-clinical-relationships.webp?v=1','Relationship diagram linking Lung Divergent with the nose, Stomach Divergent with Heart, and Bladder Divergent with rectal and Heart relationships.','These source-based examples show what a deeper connection helps explain; they do not assign points to a Divergent Channel.','Interpretation: follow each arrow from the Divergent Channel to the region or zangfu it reaches. The destination provides a traditional explanation for the listed indication; it is not a separate diagnosis or a new point pathway.']
  ];
  items.forEach(([title,src,alt,caption,interpretation])=>{
    const figure=document.createElement('figure');figure.className='visual-student divergent-image-card';
    const heading=document.createElement('h5');heading.textContent=title;
    const image=document.createElement('img');image.src=src;image.alt=alt;image.loading='lazy';
    const text=document.createElement('figcaption');text.textContent=caption;
    const comment=document.createElement('p');comment.className='divergent-image-interpretation';comment.textContent=interpretation;
    const button=document.createElement('button');button.type='button';button.textContent='Enlarge image';button.addEventListener('click',()=>image.click());
    figure.append(heading,image,text,comment,button);gallery.append(figure);
  });
  const firstDetails=section.querySelector(':scope > details');
  if(firstDetails)firstDetails.before(gallery);else section.append(gallery);
  const warning=document.createElement('p');warning.className='divergent-image-warning';warning.textContent='Conceptual learning diagrams: pathway lines and relationships are schematic. They are not point-location maps, anatomical cross-sections or treatment instructions.';gallery.before(warning);
  const style=document.createElement('style');style.textContent=`
    .divergent-image-warning{padding:11px 13px;border-left:4px solid #ef7e2d;border-radius:7px;background:#fff8ef}
    .divergent-image-gallery{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px;margin:15px 0}
    .divergent-image-card{display:flex;min-width:0;flex-direction:column;margin:0;padding:13px;border:1px solid #c6d9d7;border-radius:11px;background:#fff;box-shadow:0 6px 16px rgba(11,64,74,.07)}
    .divergent-image-card:first-child{grid-column:1/-1}.divergent-image-card h5{margin:0 0 10px;color:#163f50;font-size:1rem;line-height:1.4}
    .divergent-image-card img{display:block;width:100%;height:auto;border-radius:8px;cursor:zoom-in}.divergent-image-card figcaption{margin:11px 0;line-height:1.5;color:#39535e}
    .divergent-image-interpretation{margin:0 0 12px;padding:10px 12px;border-left:4px solid #087774;border-radius:7px;background:#edf7f6;color:#294750;font-size:.92rem;line-height:1.5}
    .divergent-image-card button{align-self:flex-start;margin-top:auto;padding:9px 14px;border:0;border-radius:8px;background:#087774;color:#fff;font-weight:800;cursor:pointer}.divergent-image-card button:hover,.divergent-image-card button:focus-visible{background:#056461}
    @media(max-width:760px){.divergent-image-gallery{grid-template-columns:1fr}.divergent-image-card:first-child{grid-column:auto}}
  `;document.head.append(style);
})();
