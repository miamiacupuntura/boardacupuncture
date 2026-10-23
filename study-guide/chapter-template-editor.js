(function () {
  'use strict';
  const api = window.StudyGuideChapterTemplate;
  const bar = document.querySelector('.preview-view-switch');
  if (!api || !bar || document.getElementById('instructor-chapter-template')) return;
  const storageKey = 'acupuncturetests:chapter-template:draft:v2';
  const panel = document.createElement('section');
  panel.id = 'instructor-chapter-template';
  panel.className = 'chapter-template-editor';
  panel.hidden = true;
  panel.innerHTML = '<details><summary>Instructor Chapter Template — Create the next chapter</summary><div class="chapter-template-body"><p class="chapter-template-note">Complete the editable fields, preview the standard chapter structure, and export the JSON file. This draft stays in this browser and does not publish anything to Moodle.</p><div class="chapter-template-fields"></div><div class="chapter-template-actions"><button type="button" data-action="preview">Preview template</button><button type="button" data-action="save">Save draft</button><button type="button" data-action="export">Export JSON</button><button type="button" data-action="reset" class="secondary">Reset fields</button></div><p class="chapter-template-status" role="status"></p><section class="chapter-template-preview" hidden aria-live="polite"></section></div></details>';
  bar.insertAdjacentElement('afterend', panel);
  const fields = panel.querySelector('.chapter-template-fields');
  const definitions = [
    ['slug','Chapter slug','text'],['courseTitle','Course title','text'],['chapterTitle','Chapter title','text'],['chapterDescription','Chapter description','textarea'],
    ['backUrl','Back / course URL','url'],['heroImageUrl','Hero image URL','url'],['heroImageAlt','Hero image alternative text','text'],
    ['chapterReading','Chapter reading and references','textarea'],['ncbahmLearningPlan','NCBAHM Exam Learning Plan','textarea'],
    ['schoolExamUrl','School Level Exam URL','url'],['boardExamUrl','Board Level Exam URL','url'],
    ['continuingEducationPlan','Continuing Education Learning Plan','textarea'],['continuingEducationReview','Continuing Education Review','textarea'],
    ['continuingEducationExamUrl','Continuing Education Exam URL','url'],
    ['ceApprovalStatus','CE approval status','select',['preparation','submitted','approved','inactive','expired']],
    ['cePubliclyActive','Public CE catalog status','select',['false','true']],
    ['ceRequirementGroup','CE requirement group','select',['General','Biomedical Sciences','Mandatory']],
    ['ceSubjectArea','CE Broker subject area','select',['General','Biomedical Sciences','Laboratory Test Findings','Image Findings','Medical Errors','Laws and Rules of the Board','HIV/AIDS','End-of-Life Care and Palliative Health Care','Human Trafficking']],
    ['ceBrokerSubjectCode','CE Broker subject-area code','text'],['ceDeliveryMethod','CE Broker delivery method','text'],
    ['ceDeliveryFormat','Learner-facing delivery description','text'],['minutesPerObjective','Planning minutes per objective','number'],
    ['approvedCeCreditHours','Board-approved CE credit hours','number'],['ceApprovalNumber','Board approval number','text'],
    ['ceApprovedOn','Approval date','date'],['ceExpiresOn','Approval expiration date','date'],
    ['ceProviderName','Approved CE provider name','text'],['ceProviderNumber','CE provider number','text'],
    ['ceCourseLocation','Certificate course location','text'],
    ['ceCertificateEnabled','Certificate enabled','select',['false','true']],
    ['spanishChapterTitle','Spanish chapter title','text'],
    ['spanishChapterDescription','Spanish chapter description','textarea'],['objectivesText','Objectives (one per line)','textarea']
  ];
  definitions.forEach(([name,label,type,options]) => {
    const wrapper = document.createElement('label');
    wrapper.className = type === 'textarea' ? 'chapter-template-wide' : '';
    wrapper.textContent = label;
    const input = document.createElement(type === 'textarea' ? 'textarea' : type === 'select' ? 'select' : 'input');
    input.name = name;
    if (type !== 'textarea' && type !== 'select') input.type = type;
    if (type === 'select') options.forEach(value => { const option=document.createElement('option'); option.value=value; option.textContent=value; input.append(option); });
    if (name === 'approvedCeCreditHours') { input.min='0'; input.step='0.25'; input.placeholder='Leave blank until approved'; }
    if (name === 'minutesPerObjective') { input.min='1'; input.step='1'; }
    if (name === 'objectivesText') input.placeholder = 'Objective | Subject matter | Teaching method';
    wrapper.append(input); fields.append(wrapper);
  });
  function objectiveText(items) { return items.map(item => [item.title,item.subjectMatter,item.teachingMethod].join(' | ')).join('\n'); }
  function fill(config) {
    const value = api.normalize(config);
    definitions.forEach(([name]) => {
      const input = fields.elements ? fields.elements[name] : fields.querySelector('[name="'+name+'"]');
      if (input) input.value = name === 'objectivesText' ? objectiveText(value.objectives) : (value[name] === null || value[name] === undefined ? '' : String(value[name]));
    });
  }
  function read() {
    const config = {};
    definitions.forEach(([name,,type]) => {
      if (name === 'objectivesText') return;
      const raw = fields.querySelector('[name="'+name+'"]').value.trim();
      config[name] = type === 'number' && raw !== '' ? Number(raw) : raw;
    });
    config.objectives = fields.querySelector('[name="objectivesText"]').value.split(/\n+/).map(line => line.trim()).filter(Boolean).map(line => {
      const parts = line.split('|').map(part => part.trim());
      return { title: parts[0] || '', subjectMatter: parts[1] || '', teachingMethod: parts[2] || '' };
    });
    return api.normalize(config);
  }
  function status(message, bad) { const node=panel.querySelector('.chapter-template-status'); node.textContent=message; node.classList.toggle('is-error',!!bad); }
  function preview() {
    const result = api.validate(read());
    const target = panel.querySelector('.chapter-template-preview'); target.hidden=false; target.replaceChildren();
    const title=document.createElement('h3'); title.textContent=result.value.chapterTitle; target.append(title);
    const description=document.createElement('p'); description.textContent=result.value.chapterDescription; target.append(description);
    const ceHeading=document.createElement('h4'); ceHeading.textContent='Continuing Education approval record'; target.append(ceHeading);
    const ceSummary=document.createElement('p');
    const estimate=api.estimatedStudyHours(result.value);
    const publicHours=result.value.ceApprovalStatus==='approved'&&result.value.cePubliclyActive&&Number(result.value.approvedCeCreditHours)>0 ? result.value.approvedCeCreditHours+' approved CE credit hours' : 'No public CE credit value';
    ceSummary.textContent='Status: '+result.value.ceApprovalStatus+' · Classification: '+result.value.ceRequirementGroup+' / '+result.value.ceSubjectArea+' · Delivery: '+result.value.ceDeliveryMethod+' · Internal estimate: '+estimate+' hours · '+publicHours;
    target.append(ceSummary);
    const heading=document.createElement('h4'); heading.textContent='Learning objectives'; target.append(heading);
    const list=document.createElement('ol'); result.value.objectives.forEach(item=>{const li=document.createElement('li');li.textContent=item.title;list.append(li);}); target.append(list);
    const sequence=document.createElement('h4'); sequence.textContent='Standard chapter order'; target.append(sequence);
    const order=document.createElement('ol'); order.className='chapter-template-order'; result.value.blockOrder.forEach(item=>{const li=document.createElement('li');li.textContent=item;order.append(li);}); target.append(order);
    status(result.errors.length ? result.errors.join(' ') : (result.warnings.length ? 'Preview ready. Before publishing: '+result.warnings.join(' ') : 'Preview ready. All required links and translations are present.'),result.errors.length);
  }
  panel.addEventListener('click', event => {
    const action=event.target.closest('button[data-action]')?.dataset.action; if(!action)return;
    if(action==='preview') preview();
    if(action==='save'){localStorage.setItem(storageKey,JSON.stringify(read()));status('Draft saved in this browser.');}
    if(action==='export'){const result=api.download(read());status(result.errors.length?'Exported with validation errors: '+result.errors.join(' '):'Template exported as JSON.',result.errors.length);}
    if(action==='reset'){localStorage.removeItem(storageKey);fill(api.defaults);panel.querySelector('.chapter-template-preview').hidden=true;status('Fields reset to the reusable template.');}
  });
  let saved; try{saved=JSON.parse(localStorage.getItem(storageKey)||'null');}catch(e){} fill(saved||api.defaults);
  function updateVisibility(){
    const instructor=Array.from(bar.querySelectorAll('button')).find(button=>button.textContent.trim()==='Instructor');
    panel.hidden=!(instructor&&instructor.getAttribute('aria-pressed')==='true');
  }
  bar.addEventListener('click',()=>setTimeout(updateVisibility));
  new MutationObserver(updateVisibility).observe(bar,{subtree:true,attributes:true,attributeFilter:['aria-pressed']});
  updateVisibility();
  const style=document.createElement('style');
  style.textContent='.chapter-template-editor{max-width:1180px;margin:14px auto;padding:0 16px;font-family:system-ui,sans-serif}.chapter-template-editor>details{border:2px solid #087774;border-radius:14px;background:#f7fbfb;box-shadow:0 5px 18px #173b4414}.chapter-template-editor>details>summary{padding:16px 18px;font-size:1.12rem;font-weight:800;color:#075f60;cursor:pointer}.chapter-template-body{padding:0 18px 20px}.chapter-template-note{padding:12px 14px;border-left:4px solid #ff7a21;background:#fff8ef;line-height:1.5}.chapter-template-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.chapter-template-fields label{display:grid;gap:6px;color:#173b44;font-weight:700}.chapter-template-fields input,.chapter-template-fields textarea,.chapter-template-fields select{box-sizing:border-box;width:100%;min-width:0;padding:11px;border:1px solid #9eb9bd;border-radius:8px;background:#fff;color:#102d3b;font:inherit}.chapter-template-fields textarea{min-height:100px;resize:vertical}.chapter-template-wide{grid-column:1/-1}.chapter-template-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:18px}.chapter-template-actions button{min-height:44px;padding:10px 16px;border:0;border-radius:9px;background:#087774;color:#fff;font-weight:800;cursor:pointer}.chapter-template-actions .secondary{border:1px solid #087774;background:#fff;color:#075f60}.chapter-template-status{min-height:24px;font-weight:700;color:#075f60}.chapter-template-status.is-error{color:#a42828}.chapter-template-preview{margin-top:16px;padding:18px;border:1px solid #bfd2d4;border-radius:12px;background:#fff}.chapter-template-preview h3{font-size:1.45rem}.chapter-template-order{columns:2;column-gap:32px}.chapter-template-order li{break-inside:avoid;margin-bottom:7px}@media(max-width:650px){.chapter-template-editor{padding:0 10px}.chapter-template-body{padding:0 12px 16px}.chapter-template-fields{grid-template-columns:1fr}.chapter-template-wide{grid-column:auto}.chapter-template-actions{display:grid;grid-template-columns:1fr}.chapter-template-actions button{width:100%;min-height:48px}.chapter-template-order{columns:1}.chapter-template-fields input,.chapter-template-fields textarea,.chapter-template-fields select{font-size:16px}}';
  document.head.append(style);
})();
