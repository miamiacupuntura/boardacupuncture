import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(scriptDir, '..');
const data = JSON.parse(fs.readFileSync(
    path.join(rootDir, 'blocks/myprogress/formulas-study-guide-data.json'),
    'utf8',
));

if (data.categories.length !== 14 || data.formulas.length !== 63) {
    throw new Error('Expected 14 categories and 63 formulas.');
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
    const categories = data.categories.map(category => {
        const formulas = data.formulas.filter(formula => formula.categoryKey === category.key);
        const items = formulas.map(formula => {
            const query = `focus=${encodeURIComponent(category.key)}${spanish ? '&amp;lang=es' : ''}`;
            const priority = formula.priority === 'core'
                ? `<span class="guide-formula-category__priority">${spanish ? 'Prioridad actual' : 'Current priority'}</span>`
                : '';
            return `<li><a href="/blocks/myprogress/formulas-study-guide.php?${query}#formula-${formula.number}">${escapeHtml(formula.name)}</a>${priority}</li>`;
        }).join('');
        return `<article class="guide-formula-category"><div class="guide-formula-category__heading"><h4>${category.number}. ${escapeHtml(spanish ? category.nameEs : category.name)}</h4><span>${formulas.length} ${spanish ? 'fórmulas' : 'formulas'}</span></div><ol class="guide-formula-category__list">${items}</ol></article>`;
    }).join('');

    return `<!-- formula-roster:start --><section class="guide-formula-roster" aria-labelledby="guide-formula-roster-title"><h3 id="guide-formula-roster-title">${spanish ? 'Listado de fórmulas del Appendix C' : 'Appendix C formula list'}</h3><p>${spanish ? 'Las 63 fórmulas están organizadas en las mismas 14 categorías de la guía de estudio. Selecciona una para abrir directamente su ficha.' : 'The 63 formulas are organized into the same 14 categories used by the study guide. Select one to open its card directly.'}</p><div class="guide-formula-roster__grid">${categories}</div></section><!-- formula-roster:end -->`;
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

console.log('Embedded 63 formulas in both guideline languages.');
