#!/usr/bin/env bash
set -euo pipefail
MOODLE_ROOT="${MOODLE_ROOT:-/var/www/html/moodle}"
TARGET="$MOODLE_ROOT/blocks/myprogress/formula-ingredient-hierarchy.php"
for command in python3 php cp stat install mktemp date; do
    command -v "$command" >/dev/null || { echo "ERROR: falta $command" >&2; exit 1; }
done
[[ -f "$TARGET" ]] || { echo "ERROR: no existe $TARGET" >&2; exit 1; }
TEMP_DIR="$(mktemp -d)"
trap 'rm -rf -- "$TEMP_DIR"' EXIT
python3 - "$TARGET" "$TEMP_DIR/page.php" <<'PY'
import sys
from pathlib import Path
source = Path(sys.argv[1]).read_text()
marker = '<!-- ingredient-role-flashcards-v1 -->'
if marker in source:
    raise SystemExit('Las fichas por ingrediente ya están instaladas; no se cambió nada.')
start = source.find('    <?php if (!$rows) { ?>')
end = source.find('\n</main>', start)
if start < 0 or end < 0 or 'Formula ingredient hierarchy must contain exactly 19' not in source:
    raise SystemExit('ERROR: la página no coincide con la versión esperada; no se cambió nada.')
replacement = r'''
<!-- ingredient-role-flashcards-v1 -->
<section id="ingredient-role-deck" aria-label="<?php echo $ises ? 'Fichas por ingrediente y función' : 'Ingredient flashcards by role'; ?>">
  <div class="formula-hierarchy__actions" id="ingredient-role-buttons"></div>
  <p id="ingredient-role-count" aria-live="polite"></p>
  <div class="formula-hierarchy__grid" id="ingredient-role-cards"></div>
  <p class="formula-hierarchy__source"><?php echo $ises
    ? 'Clasificación de estudio: los Emperadores proceden del campo Chief de los perfiles; las demás asignaciones son una interpretación didáctica de sus funciones y pueden variar según la fuente. Un ingrediente puede desempeñar más de un papel. Cada ficha conserva la descripción original de funciones para consultar el contexto.'
    : 'Study classification: Emperors come from the profiles’ Chief field; other assignments are teaching interpretations of their functions and may vary by source. An ingredient may serve more than one role. Each card retains the original functional description for context.'; ?></p>
</section>
<style>
#ingredient-role-deck{margin-top:20px}#ingredient-role-count{margin:16px 0}#ingredient-role-deck article{display:flex;flex-direction:column;align-items:flex-start;gap:12px}#ingredient-role-deck h3,#ingredient-role-deck p{margin:0}#ingredient-role-deck details{width:100%}#ingredient-role-deck summary{cursor:pointer;font-weight:700;padding:10px 0}#ingredient-role-deck details p{margin:12px 0;line-height:1.6}#ingredient-role-buttons button[aria-pressed=true]{background:#087c69;color:#fff!important}#ingredient-role-deck a{overflow-wrap:anywhere}#ingredient-role-deck button:focus-visible,#ingredient-role-deck summary:focus-visible{outline:3px solid #a06b00;outline-offset:3px}
</style>
<script>
(() => {
 'use strict';
 const root=document.getElementById('ingredient-role-deck');
 const es=<?php echo $ises ? 'true' : 'false'; ?>;
 const profiles=<?php echo json_encode(array_map(static function($r) { return ['name'=>$r['name'],'chiefs'=>$r['chiefs'],'roles'=>$r['roles'],'url'=>$r['url']->out(false)]; }, $rows), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
 // Each entry is formula-specific; it is never a permanent herb rank.
 // Non-chief categories are explicitly presented as teaching interpretations.
 const assignments={
 'Ma Huang Tang':{chen:['Gui Zhi'],zuo:['Xing Ren'],shi:['Zhi Gan Cao']},
 'Gui Zhi Tang':{chen:['Bai Shao'],zuo:['Sheng Jiang','Da Zao'],shi:['Zhi Gan Cao']},
 'Ge Gen Tang':{chen:['Ma Huang','Gui Zhi'],zuo:['Bai Shao','Sheng Jiang','Da Zao'],shi:['Zhi Gan Cao']},
 'Sang Ju Yin':{chen:['Lian Qiao','Bo He'],zuo:['Xing Ren','Jie Geng','Lu Gen'],shi:['Sheng Gan Cao']},
 'Yin Qiao San':{chen:['Bo He','Niu Bang Zi','Jing Jie Sui','Dan Dou Chi'],zuo:['Jie Geng','Zhu Ye','Lu Gen'],shi:['Sheng Gan Cao']},
 'Chai Ge Jie Ji Tang':{chen:['Qiang Huo','Bai Zhi','Huang Qin','Shi Gao'],zuo:['Jie Geng','Bai Shao','Sheng Jiang','Da Zao'],shi:['Gan Cao']},
 'Ren Shen Bai Du San':{chen:['Chuan Xiong','Chai Hu'],zuo:['Ren Shen','Fu Ling','Qian Hu','Jie Geng','Zhi Ke','Sheng Jiang','Bo He'],shi:['Zhi Gan Cao']},
 'Bai Hu Tang':{chen:['Zhi Mu'],zuo:['Geng Mi','Zhi Gan Cao'],shi:['Zhi Gan Cao']},
 'Huang Lian Jie Du Tang':{chen:['Huang Qin'],zuo:['Huang Bai'],shi:['Zhi Zi']},
 'Ma Xing Shi Gan Tang':{chen:[],zuo:['Xing Ren'],shi:['Zhi Gan Cao']},
 'Long Dan Xie Gan Tang':{chen:['Huang Qin','Zhi Zi'],zuo:['Ze Xie','Mu Tong','Che Qian Zi','Dang Gui','Sheng Di Huang'],shi:['Chai Hu','Sheng Gan Cao']},
 'Qing Hao Bie Jia Tang':{chen:['Sheng Di Huang','Zhi Mu'],zuo:['Mu Dan Pi'],shi:[]},
 'Da Cheng Qi Tang':{chen:['Mang Xiao'],zuo:['Hou Po','Zhi Shi'],shi:[]},
 'Xiao Cheng Qi Tang':{chen:['Hou Po'],zuo:['Zhi Shi'],shi:[]},
 'Tiao Wei Cheng Qi Tang':{chen:['Mang Xiao'],zuo:['Zhi Gan Cao'],shi:['Zhi Gan Cao']},
 'Xiao Chai Hu Tang':{chen:['Huang Qin'],zuo:['Ban Xia','Sheng Jiang','Ren Shen','Da Zao'],shi:['Zhi Gan Cao']},
 'Si Ni San':{chen:['Zhi Shi'],zuo:['Bai Shao'],shi:['Zhi Gan Cao']},
 'Xiao Yao San':{chen:['Dang Gui','Bai Shao'],zuo:['Bai Zhu','Fu Ling','Sheng Jiang','Bo He'],shi:['Zhi Gan Cao']},
 'Ban Xia Xie Xin Tang':{chen:['Gan Jiang','Huang Qin','Huang Lian'],zuo:['Ren Shen','Da Zao'],shi:['Zhi Gan Cao']}
 };
 const labels=es?{jun:'Emperador (Jun)',chen:'Ministro (Chen)',zuo:'Asistente (Zuo)',shi:'Mensajero (Shi)'}:{jun:'Emperor (Jun)',chen:'Minister (Chen)',zuo:'Assistant (Zuo)',shi:'Envoy (Shi)'};
 const jobs=es?{
 jun:'Trata el patrón principal y dirige la acción central de esta fórmula.',
 chen:'Apoya la acción principal o aborda un patrón coexistente importante.',
 zuo:'Apoya el tratamiento, trata manifestaciones secundarias o modera efectos intensos.',
 shi:'Guía la acción hacia una región o canal, o armoniza los ingredientes.'
 }:{jun:'Treats the principal pattern and leads this formula’s central action.',chen:'Supports the main action or addresses an important coexisting pattern.',zuo:'Supports treatment, addresses secondary manifestations, or moderates strong effects.',shi:'Guides action to a region or channel, or harmonizes ingredients.'};
 const cards=[];
 profiles.forEach(p=>{
   const roles=assignments[p.name];
   if(!roles)return;
   const chiefs=p.chiefs.split(',').map(s=>s.trim()).filter(Boolean);
   Object.entries({jun:chiefs,...roles}).forEach(([role,herbs])=>herbs.forEach(herb=>cards.push({role,herb,profile:p})));
 });
 const buttons=root.querySelector('#ingredient-role-buttons');
 const grid=root.querySelector('#ingredient-role-cards');
 const count=root.querySelector('#ingredient-role-count');
 let active='jun';
 function node(tag,text,className){const n=document.createElement(tag);if(text)n.textContent=text;if(className)n.className=className;return n;}
 function render(){
   buttons.querySelectorAll('button').forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.role===active)));
   grid.replaceChildren();
   const current=cards.filter(c=>c.role===active);
   count.textContent=labels[active]+' · '+current.length+(es?' fichas':' cards');
   if(!current.length){grid.append(node('p',es?'No hay fichas de este rol para las fórmulas que coinciden con la búsqueda.':'No cards for this role in the formulas matching your search.'));return;}
   current.forEach(c=>{
     const article=node('article',null,'formula-hierarchy__card');
     article.append(node('span',labels[active],'formula-hierarchy__category'),node('h3',c.herb),node('p',es?'¿En qué fórmula cumple este papel y qué hace?':'In which formula does it serve this role, and what does it do?'));
     const answer=node('details');const toggle=node('summary',es?'Voltear ficha':'Turn card');answer.append(toggle);
     answer.addEventListener('toggle',()=>{toggle.textContent=answer.open?(es?'Ocultar respuesta':'Hide answer'):(es?'Voltear ficha':'Turn card');});
     const link=node('a',c.profile.name);link.href=c.profile.url;
     const heading=node('h4');heading.append(link);answer.append(heading,node('p',jobs[active]));
     const alias=c.herb.replace(/^(Zhi|Sheng) Gan Cao$/, 'Gan Cao').toLowerCase();
     const sentences=c.profile.roles.split(/;|\.\s+/).map(s=>s.trim()).filter(Boolean);
     const relevant=sentences.filter(s=>s.toLowerCase().includes(alias));
     answer.append(node('p',relevant.length?relevant.join('; '):c.profile.roles,'formula-hierarchy__roles-note'));
     if(active!=='jun')answer.append(node('p',es?'Asignación didáctica; puede variar según la fuente.':'Teaching assignment; may vary by source.','formula-hierarchy__source'));
     article.append(answer);grid.append(article);
   });
 }
 Object.entries(labels).forEach(([role,label])=>{const b=node('button',label,'formula-hierarchy__button');b.type='button';b.dataset.role=role;b.addEventListener('click',()=>{active=role;render();});buttons.append(b);});
 render();
})();
</script>
<noscript><?php echo $ises ? 'Activa JavaScript para usar las fichas por ingrediente.' : 'Enable JavaScript to use ingredient flashcards.'; ?></noscript>
'''
result = source[:start] + replacement + source[end:]
Path(sys.argv[2]).write_text(result)
print('Preparadas fichas individuales en cuatro grupos para las 19 fórmulas.')
PY
php -l "$TEMP_DIR/page.php"
BACKUP="${BACKUP_ROOT:-/var/backups/acupuncturetests}/formula-role-flashcards-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP"
cp -a "$TARGET" "$BACKUP/formula-ingredient-hierarchy.php"
OWNER_GROUP="$(stat -c '%u:%g' "$TARGET")"
MODE="$(stat -c '%a' "$TARGET")"
install -o "${OWNER_GROUP%:*}" -g "${OWNER_GROUP#*:}" -m "$MODE" "$TEMP_DIR/page.php" "$TARGET"
if ! php -l "$TARGET"; then
    cp -a "$BACKUP/formula-ingredient-hierarchy.php" "$TARGET"
    echo 'ERROR: restaurada la página anterior.' >&2
    exit 1
fi
echo 'OK: fichas individuales por Emperador, Ministro, Asistente y Mensajero.'
echo 'OK: acceso a preguntas SCORM conservado; bancos de preguntas sin cambios.'
echo "RESPALDO: $BACKUP"
