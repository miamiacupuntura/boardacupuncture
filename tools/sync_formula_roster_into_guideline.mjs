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

console.log('Embedded 181 official Appendix C formulas in both guideline languages.');
