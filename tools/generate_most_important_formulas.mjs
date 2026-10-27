import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { mostImportantFormulaBatch1 } from './most_important_formulas_batch_1.mjs';
import { mostImportantFormulaBatch2 } from './most_important_formulas_batch_2.mjs';
import { mostImportantFormulaBatch3 } from './most_important_formulas_batch_3.mjs';
import { mostImportantFormulaBatch4 } from './most_important_formulas_batch_4.mjs';
import { mostImportantFormulaBatch5 } from './most_important_formulas_batch_5.mjs';
import { mostImportantFormulaBatch6 } from './most_important_formulas_batch_6.mjs';
import { mostImportantFormulaBatch7 } from './most_important_formulas_batch_7.mjs';
import { mostImportantFormulaBatch8 } from './most_important_formulas_batch_8.mjs';
import { mostImportantFormulaBatch9 } from './most_important_formulas_batch_9.mjs';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261026-2';
const totalOfficial = 85;
const batches = [
  { number: 1, cards: mostImportantFormulaBatch1 },
  { number: 2, cards: mostImportantFormulaBatch2 },
  { number: 3, cards: mostImportantFormulaBatch3 },
  { number: 4, cards: mostImportantFormulaBatch4 },
  { number: 5, cards: mostImportantFormulaBatch5 },
  { number: 6, cards: mostImportantFormulaBatch6 },
  { number: 7, cards: mostImportantFormulaBatch7 },
  { number: 8, cards: mostImportantFormulaBatch8 },
  { number: 9, cards: mostImportantFormulaBatch9 }
];
const cards = batches.flatMap(batch => batch.cards.map((card, index) => ({ ...card, batch: batch.number, batchIndex: index + 1, batchSize: batch.cards.length })));
const currentBatch = Math.max(...batches.map(batch => batch.number));
const totalBatches = Math.ceil(totalOfficial / 10);

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function nav(es) {
  const s = es ? '-es' : '';
  return `<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index${s}.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${s}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a href="pathologies${s}.html">${es ? 'Patologías' : 'Pathology Comparisons'}</a><a href="patterns${s}.html">${es ? 'Patrones' : 'Pattern Comparisons'}</a><a href="families${s}.html">${es ? 'Familias de Fórmulas' : 'Formula Families'}</a><a href="dui-yao${s}.html">${es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'}</a><a href="name-traps${s}.html">${es ? 'Trampas de Nombres' : 'Name Traps'}</a><a href="herb-families${s}.html">${es ? 'Familias y Partes' : 'Herb Families &amp; Parts'}</a><a href="safety${s}.html">${es ? 'Biblioteca de Seguridad' : 'Safety Library'}</a><a href="formula-name-traps${s}.html">${es ? 'Trampas de Fórmulas' : 'Formula Name Traps'}</a><a href="misleading-formula-names${s}.html">${es ? 'Nombres Engañosos' : 'Misleading Formula Names'}</a><a href="advanced-name-traps${s}.html">${es ? 'Trampas Avanzadas' : 'Advanced Name Traps'}</a><a href="supplemental-herb-families${s}.html">${es ? 'Familias Adicionales' : 'Supplemental Herb Families'}</a><a href="patterns-tips-maciocia${s}.html">Patterns Tips Maciocia</a><a aria-current="page" href="most-important-formulas${s}.html">${es ? 'Fórmulas Esenciales' : 'Most Important Formulas'}</a></nav>`;
}

function detailPage(card, es) {
  const s = es ? '-es' : '';
  const alt = `${card.slug}${es ? '' : '-es'}.html`;
  const v = key => esc(es ? card[`${key}Es`] : card[key]);
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${v('pattern')}"><title>${esc(card.name)} | ${es ? 'Fórmulas Esenciales' : 'Most Important Formulas'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alt}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head><body><header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="most-important-formulas${s}.html"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>${es ? 'Fórmulas Esenciales' : 'Most Important Formulas'}</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="${alt}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="most-important-formulas${s}.html">${es ? '← Volver a fórmulas' : '← Back to formulas'}</a></span></div></header><main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">${es ? `Lote ${card.batch} · Fórmula ${card.batchIndex} de ${card.batchSize}` : `Batch ${card.batch} · Formula ${card.batchIndex} of ${card.batchSize}`}</p><h1 class="study-note__title">${esc(es ? card.nameEs : card.name)}</h1><p>${v('pattern')}</p><section class="study-note__block"><h2 class="study-note__heading">${es ? 'Resumen de alto rendimiento' : 'High-yield summary'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Categoría' : 'Category'}</th><td>${v('category')}</td></tr><tr><th>${es ? 'Patrón' : 'Pattern'}</th><td>${v('pattern')}</td></tr><tr><th>${es ? 'Función' : 'Action'}</th><td>${v('action')}</td></tr><tr><th>${es ? 'Pista decisiva' : 'Decisive clue'}</th><td>${v('clue')}</td></tr></tbody></table></section><section class="study-note__block"><h2 class="study-note__heading study-note__heading--green">${es ? 'Ingredientes' : 'Ingredients'}</h2><p>${v('ingredients')}</p><p><small>${es ? 'Las preparaciones comerciales y los linajes pueden variar; verifica siempre la fuente utilizada.' : 'Commercial preparations and lineages may vary; always verify the source being used.'}</small></p></section><section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${es ? 'Diferenciación y memoria' : 'Differentiation and memory'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'No confundir' : 'Do not confuse'}</th><td>${v('contrast')}</td></tr><tr><th>${es ? 'Memoria' : 'Memory cue'}</th><td>${v('memory')}</td></tr></tbody></table></section><section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Seguridad' : 'Safety'}</h2><p>${v('safety')}</p></section></article></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Material educativo; no sustituye diagnóstico ni prescripción profesional.' : 'Educational material; not a substitute for diagnosis or professional prescribing.'}</div></footer></body></html>`;
}

function indexPage(es) {
  const s = es ? '-es' : '';
  const items = cards.map(card => `<article class="comparison-card" data-type="most-important-formulas" data-batch="${card.batch}" data-search="${esc(card.search)}"><span class="comparison-card__type">${es ? `Lote ${card.batch} · Fórmula ${card.batchIndex}` : `Batch ${card.batch} · Formula ${card.batchIndex}`}</span><h3>${esc(es ? card.nameEs : card.name)}</h3><p>${esc(es ? card.patternEs : card.pattern)}</p><div class="comparison-card__category"><strong>${es ? 'Categoría:' : 'Category:'}</strong><br>${esc(es ? card.categoryEs : card.category)}</div><a class="comparison-card__open" href="${card.slug}${s}.html">${es ? 'Abrir Resumen →' : 'Open Summary →'}</a></article>`).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? `Resumen bilingüe de las ${totalOfficial} fórmulas más importantes para repaso de examen.` : `Bilingual high-yield review of ${totalOfficial} most important formulas.`}"><title>${es ? 'Fórmulas Esenciales' : 'Most Important Formulas'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="most-important-formulas${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261026-3"></script></head><body><header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="most-important-formulas${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/my/">${es ? '← Dashboard' : '← Back to Dashboard'}</a></span></div></header><main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${es ? 'Grupo 15 · Repaso de fórmulas' : 'Group 15 · Formula review'}</p><h1>${es ? 'Fórmulas Esenciales' : 'Most Important Formulas'}</h1><p>${es ? `Resumen independiente de ${totalOfficial} fórmulas: patrón, función, pista decisiva, contraste, memoria y seguridad.` : `Independent review of ${totalOfficial} formulas: pattern, action, decisive clue, contrast, memory and safety.`}</p></section>${nav(es)}<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar fórmulas' : 'Search formulas'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar fórmula, patrón, acción o pista' : 'Search formula, pattern, action or clue'}" data-comparison-search></label></section><section><header class="comparison-summary"><h2>${es ? `Fórmulas disponibles · Lotes 1–${currentBatch} de ${totalBatches}` : `Available formulas · Batches 1–${currentBatch} of ${totalBatches}`}</h2><p><span data-visible-count>${cards.length}</span> ${es ? `de ${totalOfficial} fórmulas` : `of ${totalOfficial} formulas`}</p></header><div class="comparison-grid" data-comparison-grid>${items}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'Ninguna fórmula coincide.' : 'No formulas match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? `Fórmulas Esenciales · Lotes 1–${currentBatch} de ${totalBatches}.` : `Most Important Formulas · Batches 1–${currentBatch} of ${totalBatches}.`}</div></footer></body></html>`;
}

for (const card of cards) {
  writeFileSync(resolve(root, `${card.slug}.html`), detailPage(card, false));
  writeFileSync(resolve(root, `${card.slug}-es.html`), detailPage(card, true));
}
writeFileSync(resolve(root, 'most-important-formulas.html'), indexPage(false));
writeFileSync(resolve(root, 'most-important-formulas-es.html'), indexPage(true));
console.log(`Generated ${cards.length * 2} Most Important Formula pages and 2 indexes`);
