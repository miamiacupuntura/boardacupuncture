import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const scriptDir = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(scriptDir, '..');
const files = [
    {path: 'study-guide/guidelines/chinese-herbal-medicine.html', spanish: false},
    {path: 'study-guide/guidelines/chinese-herbal-medicine-es.html', spanish: true},
];

const panels = [
    {
        id: 'appendix-a-current-roster',
        group: 'reference',
        en: ['Official reference · Appendix A', '304 individual herbs with identity and category.'],
        es: ['Referencia oficial · Appendix A', '304 hierbas individuales con identidad y categoría.'],
    },
    {
        id: 'dui-yao-annex',
        group: 'reference',
        en: ['Official reference · Appendix B', '51 Duì Yào pairs, actions, priority, and audit.'],
        es: ['Referencia oficial · Appendix B', '51 parejas Duì Yào, acciones, prioridad y auditoría.'],
    },
    {
        id: 'historical-roster-crosswalk',
        group: 'reference',
        en: ['Historical comparison', 'Open the detailed crosswalk between previous and current rosters.'],
        es: ['Comparación histórica', 'Abre el cruce detallado entre los listados anterior y vigente.'],
    },
    {
        id: 'final-discoveries-summary',
        group: 'advanced',
        en: ['Consolidated review', 'Key findings from the herb, formula, and Duì Yào cross-check.'],
        es: ['Repaso consolidado', 'Hallazgos clave del cruce de hierbas, fórmulas y Duì Yào.'],
    },
    {
        id: 'new-student-site-reference',
        group: 'advanced',
        en: ['Platform workflow', 'How the AcupunctureTests resources fit together.'],
        es: ['Flujo de la plataforma', 'Cómo se conectan los recursos de AcupunctureTests.'],
    },
    {
        id: 'dui-yao-formula-bridge',
        group: 'advanced',
        en: ['Advanced analysis · Formula bridges', 'When to choose a formula, a pair, or both.'],
        es: ['Análisis avanzado · Puentes con fórmulas', 'Cuándo elegir una fórmula, una pareja o ambas.'],
    },
    {
        id: 'dui-yao-formula-modifications',
        group: 'advanced',
        en: ['Advanced analysis · Formula variants', 'Pairs added to a base formula and structural distinctions.'],
        es: ['Análisis avanzado · Variantes de fórmulas', 'Parejas añadidas a una fórmula base y distinciones estructurales.'],
    },
    {
        id: 'hypothetical-exam-model',
        group: 'advanced',
        en: ['Editorial appendix', 'Hypothetical practice weights—not official exam percentages.'],
        es: ['Apéndice editorial', 'Pesos hipotéticos de práctica; no son porcentajes oficiales.'],
    },
];

function findMatchingSectionEnd(html, openingStart) {
    const tokenPattern = /<section\b[^>]*>|<\/section>/g;
    tokenPattern.lastIndex = openingStart;
    let depth = 0;
    let token;
    while ((token = tokenPattern.exec(html)) !== null) {
        if (token[0].startsWith('</')) {
            depth -= 1;
            if (depth === 0) {
                return {start: token.index, end: tokenPattern.lastIndex};
            }
        } else {
            depth += 1;
        }
    }
    throw new Error(`Could not find matching </section> at offset ${openingStart}`);
}

function convertSectionToPanel(html, panel, spanish) {
    if (new RegExp(`<details[^>]*\\bid="${panel.id}"`).test(html)) {
        return html;
    }
    const openingPattern = new RegExp(`<section([^>]*\\bid="${panel.id}"[^>]*)>`);
    const match = openingPattern.exec(html);
    if (!match) {
        throw new Error(`Missing section #${panel.id}`);
    }
    const end = findMatchingSectionEnd(html, match.index);
    const labels = spanish ? panel.es : panel.en;
    const summary = `<summary class="guide-panel-summary"><span><strong>${labels[0]}</strong><small>${labels[1]}</small></span></summary>`;
    const detailsOpening = `<details${match[1]} class="${extractClass(match[1])} guide-organized-panel" data-guide-panel-group="${panel.group}">${summary}`;
    const normalizedOpening = detailsOpening.replace(/\sclass="[^"]*"(?=[^>]*\sclass=")/, '');
    return html.slice(0, match.index) + normalizedOpening + html.slice(match.index + match[0].length, end.start) + '</details>' + html.slice(end.end);
}

function extractClass(attributes) {
    const match = attributes.match(/\bclass="([^"]*)"/);
    return match ? match[1] : '';
}

function buildMap(spanish) {
    if (spanish) {
        return '<section class="guide-content-map" id="organized-guide-map" aria-labelledby="organized-guide-map-title"><p class="guide-eyebrow">Mapa organizado</p><h2 id="organized-guide-map-title">Una guía, tres niveles de lectura</h2><p class="guide-content-map__intro">No se eliminó contenido. La ruta esencial permanece visible; los listados completos y el análisis avanzado quedan plegados para evitar repeticiones visuales.</p><div class="guide-content-map__grid"><article><span class="guide-status">Ruta principal</span><h3>Aprende en orden</h3><p>Conceptos, Appendices A–D, método clínico, sesión de estudio y autoevaluación.</p><a href="#core-study-route">Comenzar la ruta →</a></article><article><span class="guide-status">Referencias oficiales</span><h3>Consulta los listados completos</h3><p>181 fórmulas, 304 hierbas, 51 parejas Duì Yào y el cruce histórico.</p><a href="#guide-formula-roster-title">Abrir las 181 fórmulas →</a><a href="#appendix-a-current-roster">Abrir las 304 hierbas →</a></article><article><span class="guide-status">Análisis avanzado</span><h3>Profundiza cuando lo necesites</h3><p>Hallazgos, puentes con fórmulas, variantes estructurales y modelo editorial.</p><a href="#final-discoveries-summary">Abrir el repaso consolidado →</a></article></div><div class="guide-content-map__controls" aria-label="Controles de contenido"><button type="button" data-guide-panels="reference">Abrir referencias oficiales</button><button type="button" data-guide-panels="advanced">Abrir análisis avanzado</button><button type="button" data-guide-panels="close">Cerrar paneles</button></div></section>';
    }
    return '<section class="guide-content-map" id="organized-guide-map" aria-labelledby="organized-guide-map-title"><p class="guide-eyebrow">Organized map</p><h2 id="organized-guide-map-title">One guide, three reading levels</h2><p class="guide-content-map__intro">No content was removed. The essential route stays visible; complete rosters and advanced analysis are folded to prevent visual repetition.</p><div class="guide-content-map__grid"><article><span class="guide-status">Core route</span><h3>Learn in order</h3><p>Concepts, Appendices A–D, clinical method, study session, and self-check.</p><a href="#core-study-route">Start the route →</a></article><article><span class="guide-status">Official references</span><h3>Consult the complete rosters</h3><p>181 formulas, 304 herbs, 51 Duì Yào pairs, and the historical crosswalk.</p><a href="#guide-formula-roster-title">Open all 181 formulas →</a><a href="#appendix-a-current-roster">Open all 304 herbs →</a></article><article><span class="guide-status">Advanced analysis</span><h3>Go deeper when needed</h3><p>Findings, formula bridges, structural variants, and the editorial model.</p><a href="#final-discoveries-summary">Open the consolidated review →</a></article></div><div class="guide-content-map__controls" aria-label="Content controls"><button type="button" data-guide-panels="reference">Open official references</button><button type="button" data-guide-panels="advanced">Open advanced analysis</button><button type="button" data-guide-panels="close">Close panels</button></div></section>';
}

function insertAfterQuickAccess(html, content) {
    if (html.includes('id="organized-guide-map"')) {
        return html;
    }
    const navStart = html.indexOf('<nav class="guide-quick-access"');
    if (navStart === -1) {
        throw new Error('Missing quick-access navigation');
    }
    const navEnd = html.indexOf('</nav>', navStart);
    if (navEnd === -1) {
        throw new Error('Missing quick-access closing tag');
    }
    const insertAt = navEnd + '</nav>'.length;
    return html.slice(0, insertAt) + content + html.slice(insertAt);
}

for (const file of files) {
    const fullPath = path.join(rootDir, file.path);
    let html = fs.readFileSync(fullPath, 'utf8');
    html = html.replace('<section class="guide-reading-flow" aria-label=', '<section class="guide-reading-flow" id="core-study-route" aria-label=');
    html = insertAfterQuickAccess(html, buildMap(file.spanish));
    for (const panel of panels) {
        html = convertSectionToPanel(html, panel, file.spanish);
    }
    html = html.replaceAll('guidelines.css?v=20261101-3', 'guidelines.css?v=20261103-1');
    if (!html.includes('guideline-organization.js')) {
        html = html.replace('</body>', '<script src="guideline-organization.js?v=20261103-1"></script></body>');
    }
    fs.writeFileSync(fullPath, html);
}

console.log('Organized both Chinese Herbal Medicine guideline languages without removing content.');
