(() => {
  'use strict';
  const content=document.querySelector('.sgv2-content');
  const nav=document.querySelector('.sgv2-navigation ul');
  const lastObjective=document.getElementById('channels-objective-9');
  if(!content||!nav||!lastObjective||document.documentElement.dataset.channelsClean==='complete')return;
  document.documentElement.dataset.channelsClean='complete';
  const currentObjectives=[...Array(9)].map((_,i)=>document.getElementById(`channels-objective-${i+1}`)).filter(Boolean);
  const titleOf=objective=>objective.querySelector(':scope>summary')?.childNodes[0]?.textContent.trim()||objective.querySelector(':scope>summary')?.textContent.trim()||objective.id;
  const examMetricLabels=['Attempts','Last Attempt','Average','Grade'];

  document.getElementById('old-objectives-heading')?.remove();
  for(let i=1;i<=5;i++)document.getElementById(`sgv2-objective-${i}`)?.remove();
  [...nav.children].forEach(li=>{const href=li.querySelector('a')?.getAttribute('href')||'';if(li.classList.contains('channels-nav-label-old')||/^#sgv2-objective-[1-5]$/.test(href))li.remove()});
  const objectiveLabel=nav.querySelector('.channels-nav-label');if(objectiveLabel)objectiveLabel.textContent='Objectives';
  const overview=document.getElementById('sgv2-course-overview');
  if(overview){const summary=overview.querySelector(':scope>summary');[...overview.children].filter(node=>node!==summary).forEach(node=>node.remove());const title=document.createElement('h3');title.textContent='The Channels and Collaterals';const heading=document.createElement('h4');heading.textContent='Description';const p1=document.createElement('p');p1.textContent='This chapter develops nine objectives: the functions of the channel network; the Twelve Primary, Divergent and Sinew Channels; the Eight Extraordinary Vessels; the Luo-Connecting Channels; the Minute Collaterals; the Twelve Cutaneous Regions; and an integrated comparison of the systems.';const p2=document.createElement('p');p2.textContent='Each objective combines source-based explanation, visual learning, guided recall, practice without score and its assigned Mock 10 or scored activity. The final chapter blocks gather the approved images, deeper reading, self-tests, exam history and review links in the same order used in Point Categories.';overview.append(title,heading,p1,p2)}

  const gallery=document.getElementById('chapter-image-library');
  let continuing=document.getElementById('channels-chapter-continuing-education');
  if(!continuing){continuing=document.createElement('details');continuing.id='channels-chapter-continuing-education';continuing.className='sgv2-card channels-chapter-continuing';const summary=document.createElement('summary');summary.textContent='How the Channels and Collaterals Chapter Aligns with Continuing Education';const intro=document.createElement('p');intro.textContent='This continuous reading extends comparison and clinical reasoning across the chapter. It does not award continuing-education credit or replace the assigned sources.';continuing.append(summary,intro);const fallback=[
    ['The functional network and relative depth','The functional network joins zangfu, channels, tissues and sense organs. Relative depth supports exterior–interior reasoning but is a conceptual relationship, not a measured anatomical ladder.','Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Functions of the Channels.”'],
    ['Primary Channels as the reference network','A channel and its zangfu share a traditional relationship but are not identical. Surface charts omit internal branches, and pathway location alone does not establish a zangfu diagnosis.','Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Twelve Primary Channels.”'],
    ['Separation, convergence and reconnection','Divergent Channels reinforce deeper relationships through separation, convergence and reconnection. Yuen’s Wei–Yuan comparison is a teaching framework, not a literal anatomical tunnel.','Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Twelve Divergent Channels.”'],
    ['Reservoirs and constitutional regulation','The reservoir analogy describes regulation rather than a literal container. The Yuan-level comparison helps discuss constitution while pathway lists still require complete pattern differentiation.','Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Eight Extraordinary Vessels.”'],
    ['Luo pathways, points and the principal count','Luo pathways and Luo-Connecting points are related but distinct study questions. The standard principal count remains fifteen; the Stomach Great Luo is a qualified additional classical reference.','Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Luo-Connecting Channels.”'],
    ['Peripheral musculotendinous pathways','Sinew Channels remain peripheral, emphasize muscles, tendons and ligaments, and ascend from the extremities. Their traditional pathway model should not be reduced to a single nerve or fascial line.','Deadman, Al-Khafaji and Baker, A Manual of Acupuncture, “The Twelve Sinew Channels.”']
  ];currentObjectives.forEach((objective,index)=>{const source=objective.querySelector(':scope .objective-continuing-education');if(source){const heading=source.querySelector(':scope>h4');if(heading)heading.remove();while(source.firstChild)continuing.append(source.firstChild);source.remove()}else if(fallback[index]){const h=document.createElement('h4');h.textContent=fallback[index][0];const p=document.createElement('p');p.textContent=fallback[index][1];const ref=document.createElement('p');ref.className='source-note';ref.textContent='Core reference: '+fallback[index][2];continuing.append(h,p,ref)}});}

  const lecture=document.getElementById('topic-questions-answers');
  const lectureSummary=lecture?.querySelector(':scope>details>summary');if(lectureSummary)lectureSummary.textContent='Self-Test — Practice without score';
  const board=document.getElementById('chapter-mock-review');
  const boardSummary=board?.querySelector(':scope>summary');if(boardSummary)boardSummary.textContent='Self-Test — Practice without score (Chapter Board Simulation)';
  const notes=document.getElementById('student_notes_form');

  const history=document.getElementById('chapter-exam-history');
  if(history){history.replaceChildren();history.className='sgv2-card channels-exam-history';const details=document.createElement('details');const summary=document.createElement('summary');summary.textContent='School Level Exam — Channels and Collaterals';const intro=document.createElement('p');intro.textContent='Preview layout. Personal attempts, dates and grades are available through View Exam History in Moodle.';const wrap=document.createElement('div');wrap.className='channels-exam-table-wrap';const table=document.createElement('table');table.className='channels-exam-table';const thead=document.createElement('thead');const headRow=document.createElement('tr');['Exam',...examMetricLabels,'Action'].forEach(label=>{const th=document.createElement('th');th.scope='col';th.textContent=label;headRow.append(th)});thead.append(headRow);const tbody=document.createElement('tbody');const seen=new Set();currentObjectives.forEach(objective=>{[...objective.querySelectorAll('.objective-exam a[href]')].filter(source=>!/Mock\s*10/i.test(source.textContent)).forEach(source=>{if(seen.has(source.href))return;seen.add(source.href);const row=document.createElement('tr');const name=document.createElement('th');name.scope='row';name.textContent=source.textContent.trim();row.append(name);examMetricLabels.forEach(label=>{const td=document.createElement('td');td.dataset.label=label;td.textContent='—';row.append(td)});const action=document.createElement('td');action.dataset.label='Action';const a=document.createElement('a');a.href=source.href;a.target='_blank';a.rel='noopener noreferrer';a.className='channels-start-exam';a.textContent='Start Exam';action.append(a);row.append(action);tbody.append(row)})});table.append(thead,tbody);wrap.append(table);const footer=document.createElement('div');footer.className='channels-history-footer';const historyLink=document.createElement('a');historyLink.href='https://www.acupuncturetests.com/mod/scorm/examhistory/?courseid=34&sectionid=2264';historyLink.textContent='View Exam History';historyLink.className='channels-history-button';footer.append(historyLink);details.append(summary,intro,wrap,footer);history.append(details)}

  let alignment=document.getElementById('channels-ncbahm-alignment');
  if(!alignment){alignment=document.createElement('details');alignment.id='channels-ncbahm-alignment';alignment.className='sgv2-card';const summary=document.createElement('summary');summary.textContent='How this Chapter aligns with the NCBAHM Exam';const note=document.createElement('p');note.textContent='This chapter supports foundational channel-and-collateral knowledge used in acupuncture point location, treatment planning and point selection. It does not predict a specific examination item.';const table=document.createElement('table');table.innerHTML='<thead><tr><th>Study task</th><th>Board-relevant ability</th></tr></thead><tbody><tr><td>Trace the four directional channel groups</td><td>Recognize channel circulation and sequence</td></tr><tr><td>Identify internal connections</td><td>Distinguish surface pathways, internal branches and zang–fu relationships</td></tr><tr><td>Decode channel names</td><td>Use Hand/Foot, Yin/Yang and six-stage terminology accurately</td></tr><tr><td>Order channel systems by depth</td><td>Compare cutaneous, sinew, Luo, Primary and Divergent relationships without collapsing them</td></tr><tr><td>Recognize Regular Channel organization</td><td>Apply the six Yin/six Yang and paired-channel framework</td></tr></tbody>';alignment.append(summary,note,table)}

  let mocks=document.getElementById('channels-mock-exams');
  if(!mocks){mocks=document.createElement('details');mocks.id='channels-mock-exams';mocks.className='sgv2-card channels-mock-exams';const summary=document.createElement('summary');summary.textContent='Board Level Exam — Channels and Collaterals';const intro=document.createElement('p');const table=document.createElement('table');table.className='channels-mock-table';const caption=document.createElement('caption');caption.textContent='The Channels and Collaterals — 9 objectives';const thead=document.createElement('thead');const headRow=document.createElement('tr');['Exam',...examMetricLabels,'Action'].forEach(label=>{const th=document.createElement('th');th.scope='col';th.textContent=label;headRow.append(th)});thead.append(headRow);const tbody=document.createElement('tbody');let available=0;currentObjectives.forEach((objective,index)=>{const source=[...objective.querySelectorAll('.objective-exam a[href]')].find(a=>/Mock\s*10/i.test(a.textContent));const row=document.createElement('tr');const topic=document.createElement('th');topic.scope='row';topic.textContent=`${index+1}. ${titleOf(objective).replace(/^Objective \d+\s*[—–-]\s*/, '')}`;row.append(topic);examMetricLabels.forEach(label=>{const td=document.createElement('td');td.dataset.label=label;td.textContent='—';row.append(td)});const action=document.createElement('td');action.dataset.label='Action';if(source){available++;const a=document.createElement('a');a.href=source.href;a.target='_blank';a.rel='noopener noreferrer';a.className='channels-mock-start';a.textContent='Start Mock 10';a.setAttribute('aria-label',`Start Mock 10 — ${topic.textContent}`);action.append(a)}else{const pending=document.createElement('span');pending.className='channels-mock-pending';pending.textContent='Awaiting exam link';action.append(pending)}row.append(action);tbody.append(row)});intro.textContent=`Access the Mock 10 exams for all nine objectives here. ${available} have available links; personal attempts, dates and grades are available in Moodle. Exams open in another tab.`;table.append(caption,thead,tbody);mocks.append(summary,intro,table)}

  let continuingExam=document.getElementById('channels-continuing-education-exam');
  if(!continuingExam){
    continuingExam=document.createElement('details');
    continuingExam.id='channels-continuing-education-exam';
    continuingExam.className='sgv2-card channels-mock-exams channels-continuing-exam';
    const summary=document.createElement('summary');summary.textContent='Continuing Education Exam — Channels and Collaterals';
    const intro=document.createElement('p');intro.textContent='After reviewing the 10 questions and feedback above, open the SCORM activity here. Personal attempts, dates and grades are available in Moodle. The exam opens in another tab.';
    const table=document.createElement('table');table.className='channels-mock-table channels-continuing-exam-table';
    const caption=document.createElement('caption');caption.textContent='The Channels and Collaterals — Continuing Education';
    const thead=document.createElement('thead');const headRow=document.createElement('tr');
    ['Exam',...examMetricLabels,'Action'].forEach(label=>{const th=document.createElement('th');th.scope='col';th.textContent=label;headRow.append(th)});thead.append(headRow);
    const tbody=document.createElement('tbody');const row=document.createElement('tr');const name=document.createElement('th');name.scope='row';name.textContent='Channels and Collaterals Continuing Education (Mock 10)';row.append(name);
    examMetricLabels.forEach(label=>{const td=document.createElement('td');td.dataset.label=label;td.textContent='—';row.append(td)});
    const action=document.createElement('td');action.dataset.label='Action';const link=document.createElement('a');link.href='https://www.acupuncturetests.com/mod/scorm/view.php?id=9356';link.target='_blank';link.rel='noopener noreferrer';link.className='channels-mock-start';link.textContent='Start Exam';action.append(link);row.append(action);tbody.append(row);
    table.append(caption,thead,tbody);continuingExam.append(summary,intro,table);
  }

  const final=document.getElementById('sgv2-final-review');
  if(final){let list=final.querySelector('ol');if(!list){list=document.createElement('ol');final.append(list)}list.replaceChildren();currentObjectives.forEach(objective=>{const li=document.createElement('li');const a=document.createElement('a');a.href='#'+objective.id;a.textContent=titleOf(objective);a.onclick=()=>objective.open=true;li.append(a);list.append(li)})}
  document.querySelectorAll('.preview-exam-action').forEach(node=>node.remove());
  [...content.children].find(node=>node.matches('div:not([id]):not([class])')&&node.querySelector('a[href*="course/view.php"]'))?.remove();

  let cursor=lastObjective;
  [gallery,lecture,board,alignment,history,mocks,final,notes,continuing,continuingExam].filter(Boolean).forEach(node=>{cursor.after(node);cursor=node});

  const cleanupHrefs=['#chapter-image-library','#channels-chapter-continuing-education','#topic-questions-answers','#chapter-mock-review','#student_notes_form','#channels-ncbahm-alignment','#chapter-exam-history','#channels-mock-exams','#channels-continuing-education-exam','#sgv2-final-review'];
  [...nav.children].forEach(li=>{if(cleanupHrefs.includes(li.querySelector('a')?.getAttribute('href')))li.remove();if(li.classList.contains('channels-final-nav-label'))li.remove()});
  const objectiveNine=nav.querySelector('a[href="#channels-objective-9"]')?.closest('li');let navCursor=objectiveNine;
  const navItem=(label,href,group=false)=>{const li=document.createElement('li');if(group){li.className='channels-nav-label channels-final-nav-label';li.textContent=label}else{const a=document.createElement('a');a.href=href;a.textContent=label;li.append(a)}navCursor.after(li);navCursor=li};
  navItem('Chapter Summary Image — Visual Review','#chapter-image-library');
  navItem('Practice & review','',true);navItem('Self-Test — Practice without score','#topic-questions-answers');
  navItem('Exams','',true);navItem('Self-Test — Chapter Board Simulation','#chapter-mock-review');navItem('How this Chapter aligns with the NCBAHM Exam','#channels-ncbahm-alignment');navItem('School Level Exam — Channels and Collaterals','#chapter-exam-history');navItem('Board Level Exam — Channels and Collaterals','#channels-mock-exams');navItem('Review objectives','#sgv2-final-review');navItem('My study notes','#student_notes_form');
  navItem('Further reading','',true);navItem('How the Channels and Collaterals Chapter Aligns with Continuing Education','#channels-chapter-continuing-education');navItem('Continuing Education Exam — Channels and Collaterals','#channels-continuing-education-exam');

  const style=document.createElement('style');style.textContent=`
    .channels-chapter-continuing{margin-top:0}
    .channels-chapter-continuing>summary{cursor:pointer}
    #chapter-image-library>details>summary,#chapter-exam-history>details>summary,#channels-mock-exams>summary,#channels-continuing-education-exam>summary,#channels-ncbahm-alignment>summary{cursor:pointer}
    .channels-chapter-continuing>h4{margin-top:26px;padding-top:18px;border-top:1px solid #c8dddd;color:#145264}
    .channels-chapter-continuing>h4:first-of-type{margin-top:16px;padding-top:0;border-top:0}
    .channels-chapter-continuing .source-note{color:#536872;font-size:.92rem}
    #channels-ncbahm-alignment table{width:100%;border-collapse:collapse}
    #channels-ncbahm-alignment th,#channels-ncbahm-alignment td{padding:10px;border:1px solid #cbd8dc;text-align:left;vertical-align:top}
    .channels-exam-history h3{margin:0 0 16px}
    .channels-exam-table-wrap,.channels-mock-exams{max-width:100%;overflow-x:hidden}
    .channels-exam-table-wrap{margin-top:18px}
    .channels-exam-table,.channels-mock-table{width:100%;min-width:0;table-layout:fixed;border-collapse:collapse}
    .channels-exam-table th,.channels-exam-table td,.channels-mock-table th,.channels-mock-table td{padding:12px 7px;border-bottom:1px solid #d4e0e4;text-align:center;vertical-align:middle;overflow-wrap:anywhere}
    .channels-exam-table thead th,.channels-mock-table thead th{background:#e7f1f4;color:#123b4a;font-weight:800}
    .channels-exam-table tbody th,.channels-mock-table tbody th{text-align:left;font-weight:500}
    .channels-exam-table tbody tr:nth-child(odd),.channels-mock-table tbody tr:nth-child(odd){background:#f4f6f8}
    .channels-exam-table tbody tr:nth-child(even),.channels-mock-table tbody tr:nth-child(even){background:#fff}
    .channels-exam-table th:first-child,.channels-mock-table th:first-child{width:36%}
    .channels-exam-table th:last-child,.channels-mock-table th:last-child{width:19%}
    .channels-mock-table{margin-top:16px}
    .channels-mock-table caption{padding:10px 0;font-weight:800;text-align:left}
    .channels-start-exam,.channels-history-button,.channels-mock-start{display:inline-flex;width:100%;min-height:44px;box-sizing:border-box;align-items:center;justify-content:center;padding:10px 8px;border-radius:10px;background:#ff7f22;color:#17232a!important;font-weight:800;line-height:1.2;white-space:normal;text-decoration:none}
    .channels-start-exam:hover,.channels-start-exam:focus-visible,.channels-history-button:hover,.channels-history-button:focus-visible,.channels-mock-start:hover,.channels-mock-start:focus-visible{background:#f06e12;outline:3px solid rgba(8,119,116,.24);outline-offset:2px}
    .channels-history-footer{display:flex;justify-content:flex-end;margin-top:16px}
    .channels-history-button{width:auto;padding-inline:16px}
    .channels-mock-pending{color:#536872}
    .channels-final-nav-label{margin-top:14px!important}
    @media(max-width:760px){
      .channels-exam-table thead,.channels-mock-table thead{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap}
      .channels-exam-table tbody,.channels-mock-table tbody{display:grid;gap:12px}
      .channels-exam-table tbody tr,.channels-mock-table tbody tr{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));padding:12px;border:1px solid #d4e0e4;border-radius:10px;background:#fff}
      .channels-exam-table tbody th,.channels-mock-table tbody th{grid-column:1/-1;width:auto;padding:4px 4px 10px;border-bottom:1px solid #d4e0e4}
      .channels-exam-table tbody td,.channels-mock-table tbody td{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px;padding:8px 4px;text-align:right}
      .channels-exam-table tbody td::before,.channels-mock-table tbody td::before{content:attr(data-label);font-weight:750;color:#38525c;text-align:left}
      .channels-exam-table tbody td:last-child,.channels-mock-table tbody td:last-child{grid-column:1/-1;display:block;padding-top:10px}
      .channels-exam-table tbody td:last-child::before,.channels-mock-table tbody td:last-child::before{content:none}
      .channels-history-footer{justify-content:stretch}
      .channels-history-button{width:100%}
    }
  `;
  document.head.append(style);
})();
