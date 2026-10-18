(() => {
  'use strict';
  const sheets=[
    [5,1,'Five Shu Overview','Five_Shu_Overview'],
    [12,9,'Mother Points','Mother_Points'],
    [2,10,'Child Points','Child_Points'],
    [16,11,'Xi-Cleft Points','Xi_Cleft_Points'],
    [17,13,'Yuan-Source Points','Yuan_Source_Points'],
    [13,14,'Luo-Connecting Points','Luo_Connecting_Points'],
    [7,15,'Group Luo Points','Group_Luo_Points'],
    [1,16,'Back-Shu Points','Back_Shu_Points'],
    [6,17,'Front-Mu Points','Front_Mu_Points'],
    [10,18,'Eight Hui-Meeting Points','Hui_Meeting_Points'],
    [22,19,'Eight Confluent Points','Confluent_Points'],
    [8,7,'Lower He-Sea Points','Lower_He_Sea_Points'],
    [3,21,'Four and Six Command Points','Command_Points'],
    [23,22,'The Four Seas','Four_Seas'],
    [4,26,'Entry and Exit Points','Entry_Exit_Points']
  ];
  for(const [sourceId,number,title,slug] of sheets){
    const root=document.getElementById(`pc-objective-${sourceId}`);
    if(!root||root.querySelector('.point-category-worksheet'))continue;
    const section=document.createElement('section');section.className='sgv2-resource point-category-worksheet';
    section.innerHTML=`<h4>Worksheet — Recreate the Source Table</h4><p>Complete the ${title} table from memory, then use the separate instructor key to verify every correspondence.</p><div class="pc-worksheet-actions"><a class="pc-worksheet-download student" href="downloads/${slug}_Worksheet.docx?v=1" download>Download Objective ${number} Worksheet</a><a class="pc-worksheet-download key" href="downloads/${slug}_Answer_Key.docx?v=1" download>Download Answer Key</a></div><p class="pc-worksheet-note">The worksheet preserves the chapter’s point abbreviations and category distinctions. It is a recall activity, not a point-location or treatment guide.</p>`;
    const exam=[...root.querySelectorAll('.sgv2-resource,section')].find(n=>/^Exam \(with score\)/i.test(n.querySelector(':scope > h4,:scope > h3')?.textContent?.trim()||''));
    if(exam)exam.before(section);else root.append(section);
  }
  if(!document.getElementById('point-category-worksheet-style')){
    const style=document.createElement('style');style.id='point-category-worksheet-style';style.textContent=`.point-category-worksheet{margin-block:18px}.pc-worksheet-actions{display:flex;flex-wrap:wrap;gap:12px;margin:14px 0}.pc-worksheet-download{display:inline-block;padding:12px 16px;border-radius:10px;text-decoration:none;font-weight:800}.pc-worksheet-download.student{background:#ff7f17;color:#111}.pc-worksheet-download.key{background:#087774;color:#fff}.pc-worksheet-download:hover{filter:brightness(.96);transform:translateY(-1px)}.pc-worksheet-note{padding:10px 12px;border-left:4px solid #087774;border-radius:7px;background:#eef8f6}@media(max-width:680px){.pc-worksheet-actions{flex-direction:column}.pc-worksheet-download{text-align:center}}`;document.head.append(style);
  }
})();
