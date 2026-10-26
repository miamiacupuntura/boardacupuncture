import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { heartPericardiumCards } from './pattern_heart_pericardium_batch.mjs';
import { liverCards } from './pattern_liver_batch.mjs';
import { lungCards } from './pattern_lung_batch.mjs';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261026-2';
const batches = [
  { key: 'heart-pericardium', number: 1, title: 'Heart and Pericardium', titleEs: 'Corazón y Pericardio', cards: heartPericardiumCards },
  { key: 'liver', number: 2, title: 'Liver', titleEs: 'Hígado', cards: liverCards },
  { key: 'lung', number: 3, title: 'Lung', titleEs: 'Pulmón', cards: lungCards }
];
const cards = batches.flatMap(batch => batch.cards.map((card, index) => ({
  ...card,
  batchKey: batch.key,
  batchNumber: batch.number,
  batchTitle: batch.title,
  batchTitleEs: batch.titleEs,
  batchIndex: index + 1,
  batchSize: batch.cards.length
})));

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function nav(es) {
  const suffix = es ? '-es' : '';
  return `<nav class="comparison-collection-links" aria-label="${es ? 'Grupos de estudio' : 'Study groups'}"><a href="index${suffix}.html">${es ? 'Comparaciones de Hierbas' : 'Single Herbs Comparisons'}</a><a href="formulas${suffix}.html">${es ? 'Comparaciones de Fórmulas' : 'Formula Comparisons'}</a><a href="pathologies${suffix}.html">${es ? 'Patologías' : 'Pathology Comparisons'}</a><a href="patterns${suffix}.html">${es ? 'Patrones' : 'Pattern Comparisons'}</a><a href="families${suffix}.html">${es ? 'Familias de Fórmulas' : 'Formula Families'}</a><a href="dui-yao${suffix}.html">${es ? 'Parejas Duì Yào' : 'Duì Yào Pairs'}</a><a href="name-traps${suffix}.html">${es ? 'Trampas de Nombres' : 'Name Traps'}</a><a href="herb-families${suffix}.html">${es ? 'Familias y Partes' : 'Herb Families &amp; Parts'}</a><a href="safety${suffix}.html">${es ? 'Biblioteca de Seguridad' : 'Safety Library'}</a><a href="formula-name-traps${suffix}.html">${es ? 'Trampas de Fórmulas' : 'Formula Name Traps'}</a><a href="misleading-formula-names${suffix}.html">${es ? 'Nombres Engañosos' : 'Misleading Formula Names'}</a><a href="advanced-name-traps${suffix}.html">${es ? 'Trampas Avanzadas' : 'Advanced Name Traps'}</a><a href="supplemental-herb-families${suffix}.html">${es ? 'Familias Adicionales' : 'Supplemental Herb Families'}</a><a aria-current="page" href="patterns-tips-maciocia${suffix}.html">Patterns Tips Maciocia</a></nav>`;
}

function detailPage(card, language) {
  const es = language === 'es';
  const data = card[language];
  const title = es ? card.titleEs : card.title;
  const suffix = es ? '-es' : '';
  const filename = `${card.slug}-maciocia-tip${suffix}.html`;
  const alternate = `${card.slug}-maciocia-tip${es ? '' : '-es'}.html`;
  const names = data.names.map(name => `<th scope="col">${esc(name)}</th>`).join('');
  const fast = data.fast.map(value => `<td>${value}</td>`).join('');
  const rows = data.rows.map(row => `<tr><th scope="row">${esc(row[0])}</th>${row.slice(1).map(value => `<td>${value}</td>`).join('')}</tr>`).join('');
  const prompts = data.prompts.map(value => `<li>${esc(value)}</li>`).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${esc(es ? card.summaryEs : card.summary)}"><title>${esc(title)} | Patterns Tips Maciocia | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="patterns-tips-maciocia${suffix}.html"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>Patterns Tips Maciocia</span></a><span style="display:flex;flex-wrap:wrap;gap:8px"><a class="comparison-back" href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="patterns-tips-maciocia${suffix}.html">${es ? '← Volver a Tips' : '← Back to Tips'}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><p class="comparison-eyebrow">${es ? `Lote ${card.batchNumber} · ${card.batchTitleEs} · Tip ${card.batchIndex} de ${card.batchSize}` : `Batch ${card.batchNumber} · ${card.batchTitle} · Tip ${card.batchIndex} of ${card.batchSize}`}</p><h1 class="study-note__title">${esc(title)}</h1><p>${esc(es ? card.summaryEs : card.summary)}</p>
<section class="study-note__block"><h2 class="study-note__heading">${es ? 'Pistas rápidas de diferenciación' : 'Fast differentiation clues'}</h2><table><thead><tr><th>${es ? 'Pista' : 'Clue'}</th>${names}</tr></thead><tbody><tr class="shared-row"><th>${es ? 'Compartido' : 'Shared'}</th><td colspan="${data.names.length}">${data.shared}</td></tr><tr><th>${es ? 'Diferencia rápida' : 'Fast distinction'}</th>${fast}</tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${es ? 'Tabla clínica' : 'Clinical table'}</h2><table><thead><tr><th>${es ? 'Comparar' : 'Compare'}</th>${names}</tr></thead><tbody>${rows}</tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Examen y seguridad' : 'Exam and safety'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Por qué importa' : 'Why it matters'}</th><td>${data.why}</td></tr><tr><th>${es ? 'Memoria' : 'Memory cue'}</th><td>${data.memory}</td></tr><tr><th>${es ? 'Seguridad' : 'Safety'}</th><td>${data.safety}</td></tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--green">${es ? 'Preguntas de repaso' : 'Review prompts'}</h2><ul class="study-note__prompts">${prompts}</ul></section></article></main><footer class="comparison-footer"><div class="comparison-shell">Patterns Tips Maciocia · ${es ? 'Material educativo; no sustituye diagnóstico ni urgencias.' : 'Educational material; not a substitute for diagnosis or emergency care.'}</div></footer></body></html>`;
}

function indexPage(language) {
  const es = language === 'es';
  const suffix = es ? '-es' : '';
  const cardHtml = cards.map((card, index) => `<article class="comparison-card" data-type="patterns-tips-maciocia" data-batch="${card.batchKey}" data-sequence="${index + 1}" data-search="${esc(card.search)}"><span class="comparison-card__type">${es ? `Lote ${card.batchNumber} · Tip ${card.batchIndex}` : `Batch ${card.batchNumber} · Tip ${card.batchIndex}`}</span><h3>${esc(es ? card.titleEs : card.title)}</h3><p>${esc(es ? card.summaryEs : card.summary)}</p><div class="comparison-card__category"><strong>${es ? 'Sistema:' : 'System:'}</strong><br>${esc(es ? card.batchTitleEs : card.batchTitle)}</div><a class="comparison-card__open" href="${card.slug}-maciocia-tip${suffix}.html">${es ? 'Abrir Tip →' : 'Open Tip →'}</a></article>`).join('');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? 'Tabla clínica independiente de patrones por órganos, signos, lengua, pulso y fórmulas.' : 'Independent organ-pattern clinical table organized by signs, tongue, pulse and formulas.'}"><title>Patterns Tips Maciocia | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="patterns-tips-maciocia${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="comparisons.js?v=20261026-3"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="patterns-tips-maciocia${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/my/">${es ? '← Dashboard' : '← Back to Dashboard'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero"><p class="comparison-eyebrow">${es ? 'Grupo 14 · Tabla clínica por órganos' : 'Group 14 · Organ-pattern clinical table'}</p><h1>Patterns Tips Maciocia</h1><p>${es ? 'Colección independiente basada en la tabla grande entregada: compara signos, lengua, pulso y fórmulas por sistema sin mezclarla con Pattern Comparisons.' : 'Independent collection based on the supplied master table: compare signs, tongue, pulse and formulas by system without mixing it into Pattern Comparisons.'}</p></section>
${nav(es)}
<section class="comparison-tools comparison-tools--search-only"><label><span class="sr-only">${es ? 'Buscar tips' : 'Search tips'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Buscar patrón, signo, lengua, pulso o fórmula' : 'Search pattern, sign, tongue, pulse or formula'}" data-comparison-search></label></section>
<section><header class="comparison-summary"><h2>${es ? 'Tips disponibles · Lotes 1–3' : 'Available tips · Batches 1–3'}</h2><p><span data-visible-count>${cards.length}</span> ${es ? 'tips clínicos' : 'clinical tips'}</p></header><div class="comparison-grid" data-comparison-grid>${cardHtml}</div><p class="comparison-empty" data-comparison-empty hidden>${es ? 'Ningún tip coincide.' : 'No tips match this search.'}</p></section></main><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Patterns Tips Maciocia · Corazón, Pericardio, Hígado y Pulmón.' : 'Patterns Tips Maciocia · Heart, Pericardium, Liver and Lung.'}</div></footer></body></html>`;
}

cards.forEach(card => {
  writeFileSync(resolve(root, `${card.slug}-maciocia-tip.html`), detailPage(card, 'en'));
  writeFileSync(resolve(root, `${card.slug}-maciocia-tip-es.html`), detailPage(card, 'es'));
});
writeFileSync(resolve(root, 'patterns-tips-maciocia.html'), indexPage('en'));
writeFileSync(resolve(root, 'patterns-tips-maciocia-es.html'), indexPage('es'));
console.log(`Generated ${cards.length * 2} Patterns Tips Maciocia pages and 2 indexes`);
