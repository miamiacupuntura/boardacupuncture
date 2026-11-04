import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(scriptDir, '..');
const data = JSON.parse(fs.readFileSync(
    path.join(rootDir, 'blocks/myprogress/formulas-study-guide-data.json'),
    'utf8',
));
const officialRosterPath = path.join(rootDir, 'blocks/myprogress/formula-board-roster-2026.json');
const officialRoster = JSON.parse(fs.readFileSync(officialRosterPath, 'utf8'));
const officialFormulaLines = fs.readFileSync(
    path.join(rootDir, 'tools/data/ncbahm_2026_appendix_c_formulas.txt'),
    'utf8',
).split(/\r?\n/).map(line => line.trim()).filter(Boolean);

if (data.categories.length !== 14 || data.formulas.length !== 63 || officialFormulaLines.length !== 181) {
    throw new Error('Expected 14 study categories, 63 detailed core formulas, and 181 official Appendix C formulas.');
}

const benskyCategories = new Map([
    [1, {code: 'release-exterior', en: 'Formulas that Release the Exterior', es: 'Fórmulas que liberan el Exterior'}],
    [2, {code: 'clear-heat', en: 'Formulas that Clear Heat', es: 'Fórmulas que aclaran el Calor'}],
    [3, {code: 'drain-downward', en: 'Formulas that Drain Downward', es: 'Fórmulas que drenan hacia abajo'}],
    [4, {code: 'harmonize', en: 'Formulas that Harmonize', es: 'Fórmulas que armonizan'}],
    [5, {code: 'dispel-summerheat', en: 'Formulas that Dispel Summerheat', es: 'Fórmulas que dispersan el Calor de Verano'}],
    [6, {code: 'warm-interior-cold', en: 'Formulas that Warm Interior Cold', es: 'Fórmulas que calientan el Frío interno'}],
    [7, {code: 'treat-exterior-interior-excess', en: 'Formulas that Treat Exterior and Interior Excess', es: 'Fórmulas que tratan exceso exterior e interior'}],
    [8, {code: 'tonify', en: 'Formulas that Tonify', es: 'Fórmulas que tonifican'}],
    [9, {code: 'stabilize-bind', en: 'Formulas that Stabilize and Bind', es: 'Fórmulas que estabilizan y contienen'}],
    [10, {code: 'calm-spirit', en: 'Formulas that Calm the Spirit', es: 'Fórmulas que calman el Espíritu'}],
    [11, {code: 'open-sensory-orifices', en: 'Formulas that Open the Sensory Orifices', es: 'Fórmulas que abren los orificios sensoriales'}],
    [12, {code: 'regulate-qi', en: 'Formulas that Regulate the Qi', es: 'Fórmulas que regulan el Qi'}],
    [13, {code: 'regulate-blood', en: 'Formulas that Regulate the Blood', es: 'Fórmulas que regulan la Sangre'}],
    [14, {code: 'expel-wind', en: 'Formulas that Expel Wind', es: 'Fórmulas que expulsan el Viento'}],
    [15, {code: 'treat-dryness', en: 'Formulas that Treat Dryness', es: 'Fórmulas que tratan la Sequedad'}],
    [16, {code: 'expel-dampness', en: 'Formulas that Expel Dampness', es: 'Fórmulas que expulsan la Humedad'}],
    [17, {code: 'dispel-phlegm', en: 'Formulas that Dispel Phlegm', es: 'Fórmulas que disipan la Flema'}],
    [18, {code: 'reduce-food-stagnation', en: 'Formulas that Reduce Food Stagnation', es: 'Fórmulas que reducen el estancamiento de alimentos'}],
    [19, {code: 'expel-parasites', en: 'Formulas that Expel Parasites', es: 'Fórmulas que expulsan parásitos'}],
    [20, {code: 'treat-abscesses-sores', en: 'Formulas that Treat Abscesses and Sores', es: 'Fórmulas que tratan abscesos y llagas'}],
    [21, {code: 'external-application', en: 'Formulas for External Application', es: 'Fórmulas para aplicación externa'}],
]);

const profileCategoryChapter = new Map([
    ['release-exterior', 1], ['clear-heat', 2], ['purge', 3], ['harmonize', 4],
    ['dispel-summerheat', 5], ['warm-interior-cold', 6], ['exterior-interior-excess', 7],
    ['tonify-qi-ch8', 8], ['tonify-blood-ch8', 8], ['tonify-qi-blood-ch8', 8],
    ['nourish-tonify-yin-ch8', 8], ['warm-tonify-yang-ch8', 8], ['tonify-yin-yang-ch8', 8],
    ['stabilize-bind', 9], ['stabilize-bind-ch9', 9], ['calm-shen', 10], ['calm-shen-ch10', 10],
    ['open-orifices-chapter-11', 11], ['regulate-qi', 12], ['regulate-qi-ch12', 12],
    ['invigorate-blood', 13], ['regulate-blood-ch13', 13], ['expel-wind', 14],
    ['expel-wind-ch14', 14], ['treat-dryness-ch15', 15], ['expel-damp', 16],
    ['expel-damp-ch16', 16], ['transform-phlegm', 17], ['transform-phlegm-ch17', 17],
    ['digestive', 18], ['digestive-chapter-18', 18], ['expel-parasites-chapter-19', 19],
    ['treat-abscesses-ch20', 20], ['treat-abscesses-chapter-20', 20],
    ['external-application-chapter-21', 21],
]);

const manualFormulaChapter = new Map(Object.entries({
    'Bai Tou Weng Tang': 2, 'Chai Hu Gui Zhi Tang': 4, 'Chai Hu Jia Long Gu Mu Li Tang': 4,
    'Da Qing Long Tang': 1, 'Dang Gui Liu Huang Tang': 9, 'Dang Gui Yin Zi': 14,
    'Dao Chi San': 2, 'Du Qi Wan': 8, 'Fu Zi Li Zhong Wan': 6, 'Ge Xia Zhu Yu Tang': 13,
    'Gua Lou Xie Bai Ban Xia Tang': 12, 'Huang Qi Jian Zhong Tang': 8, 'Ji Chuan Jian': 3,
    'Jia Jian Wei Rui Tang': 1, 'Jia Wei Xiao Yao San': 4, 'Jing Fang Bai Du San': 1,
    'Liang Ge San': 2, 'Ling Jiao Gou Teng Yin': 14, 'Liu Jun Zi Tang': 8,
    'Ma Xing Shi Gan Tang': 1,
    'Ma Zi Ren Wan': 3, 'Pu Ji Xiao Du Yin': 2, 'Qi Ju Di Huang Wan': 8,
    'Qing Gu San': 2, 'Qing Wei San': 2, 'Qing Wen Bai Du Yin': 2, 'Qing Ying Tang': 2,
    'Run Chang Wan': 3, 'Shao Fu Zhu Yu Tang': 13, 'Shao Yao Tang': 2,
    'Shen Tong Zhu Yu Tang': 13, 'Sheng Ma Ge Gen Tang': 1, 'Si Miao Wan': 16,
    'Taishan Pan Shi San': 8, 'Tao Hong Si Wu Tang': 13, 'Tong Xie Yao Fang': 4,
    'Xi Jiao Di Huang Wan': 2, 'Xiang Sha Liu Jun Zi Tang': 8, 'Xiang Su San': 1,
    'Xiao Qing Long Tang': 1, 'Xie Bai San': 2, 'Xie Huang San': 2, 'Xie Xin Tang': 2,
    'You Gui Yin': 8, 'Yu Nu Jian': 2, 'Zeng Ye Cheng Qi Tang': 3,
    'Zhi Bai Di Huang Wan': 8, 'Zhu Ye Shi Gao Tang': 2, 'Zuo Gui Yin': 8, 'Zuo Jin Wan': 2,
}));

const detailedProfiles = new Map(
    [...data.formulas, ...data.supplementalFormulas].map(formula => [formula.name, formula]),
);
const profileAliases = new Map([['Shen Qi Wan', 'Jin Gui Shen Qi Wan']]);
const sourceEntries = new Map(officialFormulaLines.map(entry => {
    const splitAt = entry.indexOf(' (');
    return [entry.slice(0, splitAt), entry.slice(splitAt + 2, -1)];
}));
const categorizedOfficialFormulas = officialRoster.formulas.map(formula => {
    const profile = detailedProfiles.get(formula.name)
        ?? detailedProfiles.get(profileAliases.get(formula.name));
    const chapter = manualFormulaChapter.get(formula.name)
        ?? profileCategoryChapter.get(profile?.categoryKey);
    const category = benskyCategories.get(chapter);
    if (!category) {
        throw new Error(`Missing Bensky category for official formula: ${formula.name}`);
    }
    if (sourceEntries.get(formula.name) !== formula.englishName) {
        throw new Error(`Official formula source mismatch: ${formula.name}`);
    }
    return {...formula, benskyCategory: {chapter, ...category}};
});
officialRoster.formulas = categorizedOfficialFormulas;
officialRoster.categorySystem = {
    author: 'Bensky et al.',
    source: 'Chinese Herbal Medicine: Formulas & Strategies',
    level: 'primary chapter',
};
fs.writeFileSync(officialRosterPath, `${JSON.stringify(officialRoster, null, 2)}\n`);

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
    const items = categorizedOfficialFormulas.map((formula, index) => {
        const {name, englishName, benskyCategory} = formula;
        officialByName.set(name, englishName);
        const profile = detailedProfiles.get(name) ?? detailedProfiles.get(profileAliases.get(name));
        const label = `<strong>${escapeHtml(name)}</strong> <span>(<em>${escapeHtml(englishName)}</em>)</span>`;
        const category = `<small class="guide-official-formula-category">${spanish ? 'Capítulo' : 'Chapter'} ${benskyCategory.chapter} · ${escapeHtml(spanish ? benskyCategory.es : benskyCategory.en)}</small>`;
        if (!profile) {
            return `<li data-official-formula-id="${index + 1}"><div>${label}${category}</div></li>`;
        }
        const query = `focus=${encodeURIComponent(profile.categoryKey)}${spanish ? '&amp;lang=es' : ''}`;
        return `<li data-official-formula-id="${index + 1}"><a href="/blocks/myprogress/formulas-study-guide.php?${query}#formula-${profile.number}">${label}${category}</a></li>`;
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

    return `<!-- formula-roster:start --><section class="guide-formula-roster" aria-labelledby="guide-formula-roster-title"><h3 id="guide-formula-roster-title">${spanish ? 'Listado oficial de fórmulas del Appendix C · 181' : 'Official Appendix C formula roster · 181'}</h3><p>${spanish ? 'Transcripción alfabética del Appendix C del temario NCBAHM 2026. Cada entrada conserva el nombre en pinyin, el nombre inglés oficial y su categoría primaria según los capítulos de Bensky. Cuando ya existe una ficha detallada en la guía, el nombre abre esa ficha.' : 'Alphabetical transcription of Appendix C in the 2026 NCBAHM outline. Each entry preserves the pinyin name, official English name, and primary category according to the Bensky chapters. Where a detailed study-guide profile already exists, the name opens that profile.'}</p><p><a href="formulas-appendix-c-2026-preview.html"><strong>${spanish ? 'Abrir el índice independiente con buscador →' : 'Open the standalone searchable index →'}</strong></a></p><ol class="guide-official-formula-roster">${items}</ol><p class="guide-source-note"><strong>${spanish ? 'Fuente oficial:' : 'Official source:'}</strong> <a href="https://www.ncbahm.org/wp-content/uploads/pdf/NCBAHM_CH_Exam_Content_Outline-w-Bibliography_Jan_2026.pdf" target="_blank" rel="noopener">NCBAHM · Chinese Herbology Content Outline 2026 ↗</a></p></section><section class="guide-formula-roster" aria-labelledby="guide-detailed-core-title"><h3 id="guide-detailed-core-title">${spanish ? 'Núcleo ya desarrollado en la plataforma · 63 fichas detalladas' : 'Existing platform core · 63 detailed profiles'}</h3><p>${spanish ? 'Estas 63 fichas permanecen organizadas en 14 categorías para estudio profundo. Son cobertura interna de la plataforma, no el total de Appendix C. Cada nombre incluye ahora su equivalente inglés oficial; Jin Gui Shen Qi Wan se vincula con la entrada oficial Shen Qi Wan (Kidney Qi Pill).' : 'These 63 profiles remain organized into 14 categories for deep study. They are the platform’s internal coverage, not the full Appendix C total. Each name now includes its official English equivalent; Jin Gui Shen Qi Wan is cross-referenced to the official Shen Qi Wan (Kidney Qi Pill) entry.'}</p><div class="guide-formula-roster__grid">${coreCategories}</div></section><!-- formula-roster:end -->`;
}

function buildStandalonePreview() {
    const items = categorizedOfficialFormulas.map((formula, index) => {
        const {name, englishName, benskyCategory} = formula;
        const id = String(index + 1).padStart(3, '0');
        const category = `Chapter ${benskyCategory.chapter} · ${benskyCategory.en}`;
        return `<li data-search="${escapeHtml(`${id} ${name} ${englishName} ${category}`.toLowerCase())}"><span class="id">ID ${id}</span><span><strong>${escapeHtml(name)}</strong><em>${escapeHtml(englishName)}</em><small>${escapeHtml(category)}</small></span></li>`;
    }).join('');
    return `<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Appendix C 2026 · 181 fórmulas oficiales</title><style>:root{font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#17344a;background:#eef5f6}*{box-sizing:border-box}body{margin:0}.wrap{width:min(1180px,calc(100% - 32px));margin:34px auto}.hero{padding:28px;border:1px solid #c9dce1;border-radius:16px;background:#fff;box-shadow:0 12px 30px #234b5b12}.page-nav{display:flex;gap:10px;flex-wrap:wrap;margin:0 0 22px}.page-nav__button{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:10px 16px;border:1px solid #9bbac5;border-radius:10px;background:#fff;color:#174c69;font-weight:850;text-decoration:none;box-shadow:0 3px 10px #17344a12}.page-nav__button:hover,.page-nav__button:focus-visible{border-color:#08756d;background:#edf8f6;color:#075e58}.page-nav__button--primary{border-color:#08756d;background:#08756d;color:#fff}.page-nav__button--primary:hover,.page-nav__button--primary:focus-visible{background:#075e58;color:#fff}.eyebrow{margin:0 0 6px;color:#08756d;font-size:.8rem;font-weight:900;letter-spacing:.12em;text-transform:uppercase}h1{margin:0;font-size:clamp(1.8rem,4vw,3rem)}.lead{max-width:850px;color:#526b7b;line-height:1.6}.facts{display:flex;gap:10px;flex-wrap:wrap;margin:18px 0}.fact{padding:8px 12px;border-radius:999px;background:#e3f4f1;color:#07665f;font-weight:800}.source{color:#526b7b}.source a{color:#075fc8}.search{width:100%;margin:18px 0 8px;padding:14px 16px;border:1px solid #a9c2cc;border-radius:10px;font:inherit}.count{margin:8px 0 18px;color:#526b7b;font-weight:700}ol{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:0;padding:0;list-style:none}li{display:flex;gap:12px;min-width:0;padding:14px;border:1px solid #d4e2e7;border-radius:10px;background:#fff}.id{flex:0 0 auto;height:max-content;padding:5px 7px;border-radius:7px;background:#ddf2ef;color:#08756d;font-size:.72rem;font-weight:900}li span:last-child{min-width:0}strong,em,small{display:block;overflow-wrap:anywhere}strong{font-size:1rem}em{margin-top:4px;color:#627786;font-size:.86rem}small{margin-top:8px;color:#8a4512;font-size:.72rem;font-weight:800}li[hidden]{display:none}@media(max-width:850px){ol{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.wrap{width:min(100% - 20px,1180px);margin:14px auto}.hero{padding:18px}.page-nav{display:grid;grid-template-columns:1fr;margin-bottom:18px}.page-nav__button{width:100%}ol{grid-template-columns:1fr}}</style></head><body><main class="wrap"><section class="hero"><nav class="page-nav" aria-label="Navegación principal"><a class="page-nav__button" href="/my/">← Dashboard</a><a class="page-nav__button page-nav__button--primary" href="/blocks/myprogress/formulas-study-guide.php?lang=es">Guía de estudio de fórmulas →</a></nav><p class="eyebrow">Current guideline 2026 · Appendix C</p><h1>181 fórmulas oficiales</h1><p class="lead">Cada registro muestra un ID estable, el nombre en pinyin, el nombre oficial en inglés y su categoría primaria según los capítulos de Bensky. Este es el listado oficial completo; las 63 fichas detalladas de la plataforma son solamente un subconjunto de estudio.</p><div class="facts"><span class="fact">181 oficiales</span><span class="fact">Categorías de Bensky</span><span class="fact">Fuente: NCBAHM 2026</span></div><p class="source"><a href="https://www.ncbahm.org/wp-content/uploads/pdf/NCBAHM_CH_Exam_Content_Outline-w-Bibliography_Jan_2026.pdf" target="_blank" rel="noopener">Abrir documento oficial de NCBAHM ↗</a></p><label for="q"><strong>Buscar por ID, pinyin, nombre inglés o categoría</strong></label><input class="search" id="q" type="search" placeholder="Ejemplo: 001, Ba Zhen Tang, Eight-Treasure, Tonify…" autocomplete="off"><p class="count" id="count">181 fórmulas mostradas</p><ol id="roster">${items}</ol></section></main><script>const q=document.querySelector('#q'),rows=[...document.querySelectorAll('#roster li')],count=document.querySelector('#count');q.addEventListener('input',()=>{const term=q.value.trim().toLowerCase();let shown=0;for(const row of rows){row.hidden=term!==''&&!row.dataset.search.includes(term);if(!row.hidden)shown++}count.textContent=shown+' fórmula'+(shown===1?'':'s')+' mostrada'+(shown===1?'':'s')});</script></body></html>`;
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
