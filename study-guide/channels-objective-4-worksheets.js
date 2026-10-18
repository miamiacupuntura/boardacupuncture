(() => {
  'use strict';
  const root=document.getElementById('channels-objective-4');
  const instructor=root?.querySelector('.objective-instructor-resource');
  if(!root||!instructor||document.getElementById('extraordinary-worksheets'))return;
  const section=document.createElement('section');
  section.id='extraordinary-worksheets';section.className='extraordinary-worksheets';
  section.innerHTML=`
    <h4>Worksheets — Recreate the Extraordinary Vessel Tables</h4>
    <p>Use the two worksheets to reconstruct the supplied comparison tables. First complete the vessel names, origins, meeting or coalescent points, and principal relationships from memory. Then compare your work with the instructor answer key.</p>
    <div class="worksheet-note"><strong>Study distinction:</strong> a vessel’s <em>confluent point</em> is not the same as the complete list of <em>meeting or coalescent points</em> along its pathway. Parentheses in the answer key preserve qualified entries shown in the supplied source chart.</div>
    <div class="worksheet-grid">
      <article><span class="worksheet-number">1</span><h5>Du · Ren · Chong · Dai</h5><p>Reconstruct the comparison of the Governing, Conception, Penetrating and Girdling vessels.</p><ul><li>Chinese and English names</li><li>Origin</li><li>Meeting or coalescent points</li><li>Main organizing relationship</li></ul></article>
      <article><span class="worksheet-number">2</span><h5>Yang Qiao · Yin Qiao · Yang Wei · Yin Wei</h5><p>Reconstruct the comparison of the Motility and Linking vessels.</p><ul><li>Chinese and English names</li><li>Origin</li><li>Meeting or coalescent points</li><li>Activity, quietness, exterior or interior</li></ul></article>
    </div>
    <div class="worksheet-downloads"><a class="worksheet-download student" href="/downloads/Eight_Extraordinary_Vessels_Worksheet.docx?v=2" target="_blank" rel="noopener">Open Student Worksheet</a><a class="worksheet-download key" href="/downloads/Eight_Extraordinary_Vessels_Worksheet_Answer_Key.docx?v=2" target="_blank" rel="noopener">Open Instructor Answer Key</a><a class="worksheet-download bundle" href="/downloads/Eight_Extraordinary_Vessels_Worksheets.zip?v=2" target="_blank" rel="noopener">Download Both as ZIP</a></div>
    <p class="worksheet-download-help">The Word files open in a new browser tab or download automatically, depending on your browser settings. If an individual Word link is blocked, use the ZIP button.</p>
    <p class="worksheet-abbreviation"><strong>Abbreviation note:</strong> BL and UB both identify the Bladder Channel. The answer key preserves the abbreviations used in the supplied tables.</p>`;
  instructor.before(section);
  const style=document.createElement('style');style.textContent=`.extraordinary-worksheets{margin:18px 0;padding:18px;border:1px solid #c9dcda;border-radius:12px;background:#fff}.worksheet-note,.worksheet-abbreviation{margin:13px 0;padding:11px 13px;border-left:4px solid #ef7e2d;border-radius:7px;background:#fff7ed}.worksheet-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin:16px 0}.worksheet-grid article{position:relative;padding:16px 16px 14px 58px;border:1px solid #bdd6d4;border-radius:10px;background:#f4fafa}.worksheet-grid h5{margin:0 0 7px}.worksheet-grid p{margin:5px 0}.worksheet-grid ul{padding-left:20px}.worksheet-number{position:absolute;left:15px;top:16px;display:grid;width:30px;height:30px;place-items:center;border-radius:50%;background:#087774;color:#fff;font-weight:800}.worksheet-downloads{display:flex;flex-wrap:wrap;gap:12px;margin:16px 0}.worksheet-download{display:inline-block;padding:13px 17px;border-radius:10px;text-decoration:none;font-weight:800}.worksheet-download.student{background:#ff7f17;color:#111}.worksheet-download.key{background:#087774;color:#fff}.worksheet-download.bundle{background:#244f72;color:#fff}.worksheet-download-help{font-size:.92rem;color:#48636d}.worksheet-download:hover{filter:brightness(.96);transform:translateY(-1px)}@media(max-width:760px){.worksheet-grid{grid-template-columns:1fr}.worksheet-downloads{flex-direction:column}.worksheet-download{text-align:center}}`;document.head.append(style);
})();
