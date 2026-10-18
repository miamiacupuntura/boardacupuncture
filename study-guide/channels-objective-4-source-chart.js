(() => {
  'use strict';
  const root=document.getElementById('channels-objective-4');
  const development=root?.querySelector('.objective-development');
  const marker=development?.querySelector('.source-progress-note');
  if(!development||document.getElementById('qiao-wei-source-chart'))return;
  const section=document.createElement('section');section.id='qiao-wei-source-chart';section.className='qiao-wei-source-chart';
  section.innerHTML=`
    <h5>6. Qiao and Wei origins and coalescent points — supplied chart</h5>
    <p>The following list is transcribed from the supplied comparison chart. Parentheses indicate that the printed source qualifies or parenthesizes the entry; confirm those entries against the full individual vessel discussion before clinical use.</p>
    <div class="qiao-wei-table-wrap"><table><thead><tr><th>Vessel</th><th>English name</th><th>Origin recorded in chart</th><th>Coalescent points recorded in chart</th><th>Organizing idea</th></tr></thead><tbody>
      <tr><th>Yang Qiao Mai</th><td>Yang Motility Vessel</td><td>UB62</td><td>UB62, UB61, UB59, GB29, SI10, LI15, LI16, parenthesized ST9, ST4, ST3, ST1, UB1, GB20</td><td>Activity; links Yang channel regions</td></tr>
      <tr><th>Yin Qiao Mai</th><td>Yin Motility Vessel</td><td>KID6; chart also notes KID2 as a variant</td><td>Parenthesized KID2, KID6, KID8, parenthesized ST12, parenthesized ST9, UB1</td><td>Quietness; Kidney–Bladder relationship</td></tr>
      <tr><th>Yang Wei Mai</th><td>Yang Linking Vessel</td><td>UB63</td><td>UB63, GB35, SI10, SJ15, GB21, parenthesized ST8, GB13–GB20, DU16, DU15</td><td>Links Yang channels and Du; exterior</td></tr>
      <tr><th>Yin Wei Mai</th><td>Yin Linking Vessel</td><td>KID9</td><td>KID9, parenthesized SP12, SP13, SP15, SP16, LIV14, REN22, REN23</td><td>Links Yin channels and Ren; interior</td></tr>
    </tbody></table></div>
    <p class="abbreviation-note"><strong>Abbreviation note:</strong> BL and UB both refer to the Bladder Channel. This guide preserves UB when transcribing the supplied chart and may use BL in images or point names drawn from another convention.</p>`;
  if(marker)marker.before(section);else development.append(section);
  const style=document.createElement('style');style.textContent='.qiao-wei-source-chart{margin:18px 0}.qiao-wei-table-wrap{overflow-x:auto}.qiao-wei-source-chart table{min-width:840px}.qiao-wei-source-chart .abbreviation-note{padding:11px 13px;border-left:4px solid #087774;border-radius:7px;background:#edf7f6}';document.head.append(style);
})();
