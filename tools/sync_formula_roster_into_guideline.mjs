import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(scriptDir, '..');
const data = JSON.parse(fs.readFileSync(
    path.join(rootDir, 'blocks/myprogress/formulas-study-guide-data.json'),
    'utf8',
));
const officialFormulaLines = fs.readFileSync(
    path.join(rootDir, 'tools/data/ncbahm_2026_appendix_c_formulas.txt'),
    'utf8',
).split(/\r?\n/).map(line => line.trim()).filter(Boolean);

if (data.categories.length !== 14 || data.formulas.length !== 63 || officialFormulaLines.length !== 181) {
    throw new Error('Expected 14 study categories, 63 detailed core formulas, and 181 official Appendix C formulas.');
}

const escapeHtml = value => String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;');

const files = [
    {
        path: 'study-guide/guidelines/chinese-herbal-medicine.html',
        spanish: false,
        anchor: '<p><strong>Useful question:</strong> which ingredient or pair explains the difference between this formula and another in the same family?</p>',
    },
    {
        path: 'study-guide/guidelines/chinese-herbal-medicine-es.html',
        spanish: true,
        anchor: '<p><strong>Pregunta útil:</strong> ¿qué ingrediente o pareja explica la diferencia entre esta fórmula y otra de la misma familia?</p>',
    },
];

function buildRoster(spanish) {
    const officialByName = new Map();
    const detailedProfiles = new Map(
        [...data.formulas, ...data.supplementalFormulas].map(formula => [formula.name, formula]),
    );
    const items = officialFormulaLines.map((entry, index) => {
        const splitAt = entry.indexOf(' (');
        if (splitAt < 1 || !entry.endsWith(')')) {
            throw new Error(`Invalid official Appendix C formula entry: ${entry}`);
        }
        const name = entry.slice(0, splitAt);
        const englishName = entry.slice(splitAt + 2, -1);
        officialByName.set(name, englishName);
        const profile = detailedProfiles.get(name);
        const label = `<strong>${escapeHtml(name)}</strong> <span>(<em>${escapeHtml(englishName)}</em>)</span>`;
        if (!profile) {
            return `<li data-official-formula-id="${index + 1}">${label}</li>`;
        }
        const query = `focus=${encodeURIComponent(profile.categoryKey)}${spanish ? '&amp;lang=es' : ''}`;
        return `<li data-official-formula-id="${index + 1}"><a href="/blocks/myprogress/formulas-study-guide.php?${query}#formula-${profile.number}">${label}</a></li>`;
    }).join('');

    const coreCategories = data.categories.map(category => {
        const formulas = data.formulas.filter(formula => formula.categoryKey === category.key);
        const coreItems = formulas.map(formula => {
            const query = `focus=${encodeURIComponent(category.key)}${spanish ? '&amp;lang=es' : ''}`;
            const officialLookupName = formula.name === 'Jin Gui Shen Qi Wan' ? 'Shen Qi Wan' : formula.name;
            const englishName = officialByName.get(officialLookupName);
            if (!englishName) {
                throw new Error(`Missing English name for detailed core formula: ${formula.name}`);
            }
            return `<li data-formula-core-profile="${formula.number}"><a href="/blocks/myprogress/formulas-study-guide.php?${query}#formula-${formula.number}"><strong>${escapeHtml(formula.name)}</strong> <span>(<em>${escapeHtml(englishName)}</em>)</span></a><span class="guide-formula-category__priority">${spanish ? 'Ficha ' : 'Profile '}${formula.number}</span></li>`;
        }).join('');
        return `<article class="guide-formula-category"><div class="guide-formula-category__heading"><h4>${category.number}. ${escapeHtml(spanish ? category.nameEs : category.name)}</h4><span>${formulas.length} ${spanish ? 'fichas' : 'profiles'}</span></div><ol class="guide-formula-category__list">${coreItems}</ol></article>`;
    }).join('');

    return `<!-- formula-roster:start --><section class="guide-formula-roster" aria-labelledby="guide-formula-roster-title"><h3 id="guide-formula-roster-title">${spanish ? 'Listado oficial de fórmulas del Appendix C · 181' : 'Official Appendix C formula roster · 181'}</h3><p>${spanish ? 'Transcripción alfabética del Appendix C del temario NCBAHM 2026. Cada entrada conserva el nombre en pinyin y el nombre inglés oficial entre paréntesis. Cuando ya existe una ficha detallada en la guía, el nombre abre esa ficha.' : 'Alphabetical transcription of Appendix C in the 2026 NCBAHM outline. Each entry preserves the pinyin name and the official English name in parentheses. Where a detailed study-guide profile already exists, the name opens that profile.'}</p><ol class="guide-official-formula-roster">${items}</ol><p class="guide-source-note"><strong>${spanish ? 'Fuente oficial:' : 'Official source:'}</strong> <a href="https://www.ncbahm.org/wp-content/uploads/pdf/NCBAHM_CH_Exam_Content_Outline-w-Bibliography_Jan_2026.pdf" target="_blank" rel="noopener">NCBAHM · Chinese Herbology Content Outline 2026 ↗</a></p></section><section class="guide-formula-roster" aria-labelledby="guide-detailed-core-title"><h3 id="guide-detailed-core-title">${spanish ? 'Núcleo ya desarrollado en la plataforma · 63 fichas detalladas' : 'Existing platform core · 63 detailed profiles'}</h3><p>${spanish ? 'Estas 63 fichas permanecen organizadas en 14 categorías para estudio profundo. Son cobertura interna de la plataforma, no el total de Appendix C. Cada nombre incluye ahora su equivalente inglés oficial; Jin Gui Shen Qi Wan se vincula con la entrada oficial Shen Qi Wan (Kidney Qi Pill).' : 'These 63 profiles remain organized into 14 categories for deep study. They are the platform’s internal coverage, not the full Appendix C total. Each name now includes its official English equivalent; Jin Gui Shen Qi Wan is cross-referenced to the official Shen Qi Wan (Kidney Qi Pill) entry.'}</p><div class="guide-formula-roster__grid">${coreCategories}</div></section><!-- formula-roster:end -->`;
}

function buildStandalonePreview() {
    const items = officialFormulaLines.map((entry, index) => {
        const splitAt = entry.indexOf(' (');
        const name = entry.slice(0, splitAt);
        const englishName = entry.slice(splitAt + 2, -1);
        const id = String(index + 1).padStart(3, '0');
        return `<li data-search="${escapeHtml(`${id} ${name} ${englishName}`.toLowerCase())}"><span class="id">ID ${id}</span><span><strong>${escapeHtml(name)}</strong><em>${escapeHtml(englishName)}</em></span></li>`;
    }).join('');
    return `<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Appendix C 2026 · 181 fórmulas oficiales</title><style>:root{font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#17344a;background:#eef5f6}*{box-sizing:border-box}body{margin:0}.wrap{width:min(1180px,calc(100% - 32px));margin:34px auto}.hero{padding:28px;border:1px solid #c9dce1;border-radius:16px;background:#fff;box-shadow:0 12px 30px #234b5b12}.eyebrow{margin:0 0 6px;color:#08756d;font-size:.8rem;font-weight:900;letter-spacing:.12em;text-transform:uppercase}h1{margin:0;font-size:clamp(1.8rem,4vw,3rem)}.lead{max-width:850px;color:#526b7b;line-height:1.6}.facts{display:flex;gap:10px;flex-wrap:wrap;margin:18px 0}.fact{padding:8px 12px;border-radius:999px;background:#e3f4f1;color:#07665f;font-weight:800}.source{color:#526b7b}.source a{color:#075fc8}.search{width:100%;margin:18px 0 8px;padding:14px 16px;border:1px solid #a9c2cc;border-radius:10px;font:inherit}.count{margin:8px 0 18px;color:#526b7b;font-weight:700}ol{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:0;padding:0;list-style:none}li{display:flex;gap:12px;min-width:0;padding:14px;border:1px solid #d4e2e7;border-radius:10px;background:#fff}.id{flex:0 0 auto;height:max-content;padding:5px 7px;border-radius:7px;background:#ddf2ef;color:#08756d;font-size:.72rem;font-weight:900}li span:last-child{min-width:0}strong,em{display:block;overflow-wrap:anywhere}strong{font-size:1rem}em{margin-top:4px;color:#627786;font-size:.86rem}li[hidden]{display:none}@media(max-width:850px){ol{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.wrap{width:min(100% - 20px,1180px);margin:14px auto}.hero{padding:18px}ol{grid-template-columns:1fr}}</style></head><body><main class="wrap"><section class="hero"><p class="eyebrow">Current guideline 2026 · Appendix C</p><h1>181 fórmulas oficiales</h1><p class="lead">Cada registro muestra un ID estable, el nombre en pinyin y el nombre oficial en inglés. Este es el listado oficial completo; las 63 fichas detalladas de la plataforma son solamente un subconjunto de estudio.</p><div class="facts"><span class="fact">181 oficiales</span><span class="fact">63 fichas internas</span><span class="fact">Fuente: NCBAHM 2026</span></div><p class="source"><a href="https://www.ncbahm.org/wp-content/uploads/pdf/NCBAHM_CH_Exam_Content_Outline-w-Bibliography_Jan_2026.pdf" target="_blank" rel="noopener">Abrir documento oficial de NCBAHM ↗</a></p><label for="q"><strong>Buscar por ID, pinyin o nombre inglés</strong></label><input class="search" id="q" type="search" placeholder="Ejemplo: 001, Ba Zhen Tang, Eight-Treasure…" autocomplete="off"><p class="count" id="count">181 fórmulas mostradas</p><ol id="roster">${items}</ol></section></main><script>const q=document.querySelector('#q'),rows=[...document.querySelectorAll('#roster li')],count=document.querySelector('#count');q.addEventListener('input',()=>{const term=q.value.trim().toLowerCase();let shown=0;for(const row of rows){row.hidden=term!==''&&!row.dataset.search.includes(term);if(!row.hidden)shown++}count.textContent=shown+' fórmula'+(shown===1?'':'s')+' mostrada'+(shown===1?'':'s')});</script></body></html>`;
}

for (const file of files) {
    const fullPath = path.join(rootDir, file.path);
    let html = fs.readFileSync(fullPath, 'utf8');
    html = html.replace(/<!-- formula-roster:start -->[\s\S]*?<!-- formula-roster:end -->/g, '');
    if (!html.includes(file.anchor)) {
        throw new Error(`Could not find formula insertion point in ${file.path}`);
    }
    html = html.replace(file.anchor, file.anchor + buildRoster(file.spanish));
    html = html.replace(
        '<article class="guide-reading-step"><div class="guide-reading-step__number">7</div>',
        '<article class="guide-reading-step" id="appendix-c-study"><div class="guide-reading-step__number">7</div>',
    );
    html = html.replaceAll('guidelines.css?v=20261031-2', 'guidelines.css?v=20261031-3');
    html = html.replaceAll('guideline-return.js?v=20261031-2', 'guideline-return.js?v=20261031-3');
    fs.writeFileSync(fullPath, html);
}

fs.writeFileSync(
    path.join(rootDir, 'study-guide/guidelines/formulas-appendix-c-2026-preview.html'),
    buildStandalonePreview(),
);

console.log('Embedded 181 official Appendix C formulas and generated the standalone preview.');
