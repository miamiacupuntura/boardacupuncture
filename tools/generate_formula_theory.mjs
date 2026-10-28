import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { formulaTheoryTopics as topics } from './formula_theory_topics.mjs';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261027-5';
const detailCssVersion = '20261026-2';

function esc(value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;');
}

function nav(es) {
  const suffix = es ? '-es' : '';
  const links = [
    ['index', es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'],
    ['formulas', es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'],
    ['pathologies', es ? 'Patologías' : 'Pathology Comparisons'],
    ['patterns', es ? 'Patrones' : 'Pattern Comparisons'],
    ['families', es ? 'Familias de Fórmulas' : 'Formula Families'],
    ['dui-yao', es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'],
    ['name-traps', es ? 'Trampas de Nombres' : 'Name Traps'],
    ['herb-families', es ? 'Familias y Partes' : 'Herb Families & Parts'],
    ['safety', es ? 'Biblioteca de Seguridad' : 'Safety Library'],
    ['formula-name-traps', es ? 'Trampas de Fórmulas' : 'Formula Name Traps'],
    ['misleading-formula-names', es ? 'Nombres Engañosos' : 'Misleading Formula Names'],
    ['advanced-name-traps', es ? 'Trampas Avanzadas' : 'Advanced Name Traps'],
    ['supplemental-herb-families', es ? 'Familias Adicionales' : 'Supplemental Herb Families'],
    ['patterns-tips-maciocia', 'Patterns Tips Maciocia'],
    ['most-important-formulas', es ? 'Fórmulas Esenciales' : 'Most Important Formulas']
  ];
  const html = links.map(([file, label]) => `<a href="${file}${suffix}.html">${label}</a>`).join('');
  const after = [
    ['herbal-incompatibilities', es ? 'Incompatibilidades' : 'Herbal Incompatibilities'],
    ['secondary-actions', es ? 'Acciones Secundarias' : 'Secondary Actions'],
    ['formula-navigator', es ? 'Navegador de 63 Fórmulas' : '63-Formula Navigator']
  ].map(([file, label]) => `<a href="${file}${suffix}.html">${label}</a>`).join('');
  return `<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}">${html}<a aria-current="page" href="formula-theory${suffix}.html">${es ? 'Composición de Fórmulas' : 'Formula Composition'}</a>${after}</nav>`;
}

function detailPage(topic, language) {
  const es = language === 'es';
  const data = topic[language];
  const suffix = es ? '-es' : '';
  const alternate = `${topic.slug}${es ? '' : '-es'}.html`;
  const index = `formula-theory${suffix}.html`;
  const position = topics.findIndex(item => item.slug === topic.slug);
  const previous = position > 0 ? topics[position - 1] : null;
  const next = position < topics.length - 1 ? topics[position + 1] : null;
  const previousButton = previous
    ? `<a class="comparison-back" href="${previous.slug}${suffix}.html">${es ? '← Capítulo anterior' : '← Previous chapter'}</a>`
    : `<a class="comparison-back" href="${index}">${es ? '← Índice del grupo' : '← Group index'}</a>`;
  const nextButton = next
    ? `<a class="comparison-card__open" href="${next.slug}${suffix}.html">${es ? 'Siguiente capítulo →' : 'Next chapter →'}</a>`
    : `<a class="comparison-card__open" href="${index}">${es ? 'Finalizar y ver capítulos →' : 'Finish and view chapters →'}</a>`;
  const chapterNavigation = `<nav aria-label="${es ? 'Navegación entre capítulos' : 'Chapter navigation'}" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:28px;padding-top:22px;border-top:1px solid #d5e0ea">${previousButton}<a class="comparison-back" href="${index}">${es ? `Todos los capítulos · ${topic.number}/${topics.length}` : `All chapters · ${topic.number}/${topics.length}`}</a>${nextButton}</nav>`;
  const sections = data.sections.map(section => {
    const rows = section.rows.map(row => `<tr><th scope="row">${esc(row[0])}</th><td>${esc(row[1])}</td><td>${esc(row[2])}</td></tr>`).join('');
    return `<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${esc(section.heading)}</h2><table><thead><tr><th>${es ? 'Concepto' : 'Concept'}</th><th>${es ? 'Qué significa' : 'What it means'}</th><th>${es ? 'Aplicación' : 'Application'}</th></tr></thead><tbody>${rows}</tbody></table></section>`;
  }).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${esc(topic.summary[language])}"><title>${esc(topic.title[language])} | ${es ? 'Composición de Fórmulas' : 'Formula Composition'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${detailCssVersion}"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="${index}"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>${es ? 'Composición de Fórmulas' : 'Formula Composition'}</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="${index}">${es ? '← Volver al grupo' : '← Back to group'}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">${es ? `Teoría ${topic.number} de ${topics.length}` : `Theory ${topic.number} of ${topics.length}`}</p><h1 class="study-note__title">${esc(topic.title[language])}</h1><p>${esc(topic.summary[language])}</p>
<section class="study-note__block"><h2 class="study-note__heading">${es ? 'Regla central' : 'Core rule'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Regla' : 'Rule'}</th><td>${esc(data.rule)}</td></tr></tbody></table></section>${sections}
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Uso responsable' : 'Responsible use'}</h2><p>${es ? 'Resumen educativo para análisis de fórmulas y preparación de examen. Las dosis, preparaciones tóxicas, interacciones y decisiones clínicas requieren fuentes vigentes y supervisión profesional.' : 'Educational summary for formula analysis and exam preparation. Doses, toxic preparations, interactions and clinical decisions require current references and professional supervision.'}</p></section>
${chapterNavigation}
</article></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Grupo 16 · Composición, estrategia y preparación' : 'Group 16 · Composition, strategy and preparation'}</div></footer></body></html>`;
}

function indexPage(language) {
  const es = language === 'es';
  const suffix = es ? '-es' : '';
  const cards = topics.map(topic => `<article class="comparison-card" data-type="formula-theory" data-sequence="${topic.number}" data-search="${esc(topic.search)}"><span class="comparison-card__type">${es ? `Teoría ${topic.number}` : `Theory ${topic.number}`}</span><h3>${esc(topic.title[language])}</h3><p>${esc(topic.summary[language])}</p><div class="comparison-card__category"><strong>${es ? 'Regla:' : 'Rule:'}</strong><br>${esc(topic[language].rule)}</div><a class="comparison-card__open" data-chapter-url="${topic.slug}${suffix}.html" data-chapter-number="${topic.number}" href="continuous-reader.html?group=formula-theory&amp;lang=${language}&amp;chapter=${topic.number}">${es ? 'Leer y continuar →' : 'Read and continue →'}</a></article>`).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? 'Teoría bilingüe de composición, jerarquía, dosificación y preparación de fórmulas herbales chinas.' : 'Bilingual theory of Chinese herbal formula composition, hierarchy, dosing and preparation.'}"><title>${es ? 'Composición de Fórmulas' : 'Formula Composition & Theory'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="formula-theory${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261026-3"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="formula-theory${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/my/">${es ? '← Dashboard' : '← Back to Dashboard'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${es ? 'Paso 3 · Estrategia de fórmulas' : 'Step 3 · Formula strategy'}</p><h1>${es ? 'Composición y Teoría de Fórmulas' : 'Formula Composition & Theory'}</h1><p>${es ? 'Aprende relaciones herbales, jerarquía Jun–Chen–Zuo–Shi, arquitectura de dosis, preparación y un método de examen paso a paso.' : 'Learn herb relationships, Jun–Chen–Zuo–Shi hierarchy, dose architecture, preparation and a step-by-step exam method.'}</p></section>
${nav(es)}
<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar teoría de fórmulas' : 'Search formula theory'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar relación, rol, dosis o preparación' : 'Search relationship, role, dose or preparation'}" data-comparison-search></label></section><section><header class="comparison-summary"><h2>${es ? 'Temas disponibles' : 'Available topics'}</h2><p><span data-visible-count>${topics.length}</span> ${es ? 'temas de teoría' : 'theory topics'}</p></header><div class="comparison-grid" data-comparison-grid>${cards}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'Ningún tema coincide.' : 'No theory topics match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Relaciones · Roles · Dosis · Preparación · Método de examen' : 'Relationships · Roles · Dose · Preparation · Exam method'}</div></footer></body></html>`;
}

for (const topic of topics) {
  writeFileSync(resolve(root, `${topic.slug}.html`), detailPage(topic, 'en'));
  writeFileSync(resolve(root, `${topic.slug}-es.html`), detailPage(topic, 'es'));
}
writeFileSync(resolve(root, 'formula-theory.html'), indexPage('en'));
writeFileSync(resolve(root, 'formula-theory-es.html'), indexPage('es'));

console.log(`Generated ${topics.length * 2} Formula Theory pages and 2 indexes`);
