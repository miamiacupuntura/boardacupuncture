import { readFileSync, writeFileSync } from 'node:fs';
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
import {
  classicFormulaCategories,
  classicFormulaFallbacks,
  classicFormulaOutline,
  classicFormulaRoutes,
  classicFormulaSafetyTiers
} from './classic_formula_navigator_data.mjs';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const cssVersion = '20261028-4';
const practiceCssVersion = '20261028-3';
const practiceData = JSON.parse(readFileSync(resolve(root, 'formula-practice-data.json'), 'utf8'));
if (practiceData.totals?.questions !== 572 || practiceData.formulas?.length !== 63) {
  throw new Error('Formula practice data is missing or has unexpected totals');
}
const practiceCountByNumber = new Map(practiceData.formulas.map(formula => [formula.number, formula.questionCount]));
const expandedCards = [
  ...mostImportantFormulaBatch1, ...mostImportantFormulaBatch2, ...mostImportantFormulaBatch3,
  ...mostImportantFormulaBatch4, ...mostImportantFormulaBatch5, ...mostImportantFormulaBatch6,
  ...mostImportantFormulaBatch7, ...mostImportantFormulaBatch8, ...mostImportantFormulaBatch9
];
const expandedByName = new Map(expandedCards.map(card => [card.name, card]));
const categories = new Map(classicFormulaCategories.map(([key, en, es]) => [key, {key, en, es}]));
const routes = new Map(classicFormulaRoutes.map(([key, en, es]) => [key, {key, en, es}]));

function slugify(value) {
  return value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
}

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function compact(value) {
  return String(value).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, ' ').trim();
}

const formulas = classicFormulaOutline.map(([number, name, categoryKey, kings, routeKeys]) => {
  const expanded = expandedByName.get(name);
  const fallback = classicFormulaFallbacks[name];
  if (!expanded && !fallback) throw new Error(`Missing navigator content for ${name}`);
  const content = expanded || fallback;
  const category = categories.get(categoryKey);
  const slug = `formula-navigator-${String(number).padStart(2, '0')}-${slugify(name)}`;
  return {
    number, name, slug, categoryKey, category, kings, routeKeys,
    practiceCount: practiceCountByNumber.get(number) || 0,
    routeLabels: routeKeys.map(key => routes.get(key)),
    expandedSlug: expanded?.slug || null,
    safetyTier: classicFormulaSafetyTiers[name] || 'standard',
    pattern: content.pattern, patternEs: content.patternEs,
    action: content.action, actionEs: content.actionEs,
    clue: content.clue, clueEs: content.clueEs,
    ingredients: content.ingredients, ingredientsEs: content.ingredientsEs,
    safety: content.safety, safetyEs: content.safetyEs,
    search: [name, category.en, category.es, kings, ...routeKeys, content.pattern, content.patternEs,
      content.action, content.actionEs, content.clue, content.clueEs, content.ingredients, content.ingredientsEs]
      .map(compact).join(' ')
  };
});

function guideName(es) {
  return 'Herbs Practice Exams';
}

function nav(es, current) {
  const suffix = es ? '-es' : '';
  const modes = [
    ['finder', `formula-navigator${suffix}.html`, es ? 'Buscar una fórmula' : 'Find a formula'],
    ['practice', `formula-practice${suffix}.html`, es ? 'Practicar preguntas' : 'Practice questions'],
    ['compare', `formulas${suffix}.html`, es ? 'Comparar fórmulas' : 'Compare formulas']
  ];
  return `<nav class="comparison-collection-links" aria-label="${es ? 'Modos de Herbs Practice Exams' : 'Herbs Practice Exams modes'}">${modes.map(([key, href, label]) => `<a${key === current ? ' aria-current="page"' : ''} href="${href}">${label}</a>`).join('')}</nav>`;
}

function riskBadge(formula, es) {
  if (formula.safetyTier === 'critical') return `<span class="formula-risk formula-risk--critical">${es ? 'Seguridad crítica' : 'Critical safety'}</span>`;
  if (formula.safetyTier === 'high') return `<span class="formula-risk formula-risk--high">${es ? 'Precaución alta' : 'High caution'}</span>`;
  return `<span class="formula-risk">${es ? 'Revisión de seguridad' : 'Safety reviewed'}</span>`;
}

function detailPage(formula, es) {
  const suffix = es ? '-es' : '';
  const alternate = `${formula.slug}${es ? '' : '-es'}.html`;
  const v = key => esc(es ? formula[`${key}Es`] : formula[key]);
  const category = esc(es ? formula.category.es : formula.category.en);
  const routeText = formula.routeLabels.map(route => es ? route.es : route.en).join(' · ');
  const expanded = formula.expandedSlug
    ? `<a class="formula-related-link" href="${formula.expandedSlug}${suffix}.html">${es ? 'Abrir ficha ampliada en Fórmulas Esenciales →' : 'Open expanded Essential Formula profile →'}</a>`
    : `<span class="formula-related-note">${es ? 'Ficha creada desde el compendio histórico normalizado.' : 'Profile created from the normalized historical compendium.'}</span>`;
  const query = encodeURIComponent(formula.name);
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${v('pattern')}"><title>${esc(formula.name)} | ${guideName(es)} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="formula-navigator${suffix}.html"><span class="comparison-brand__mark" aria-hidden="true">☿</span><span>${guideName(es)}</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="formula-navigator${suffix}.html">${es ? '← Volver a Herbs Practice Exams' : '← Back to Herbs Practice Exams'}</a></span></div></header>
<main class="comparison-shell"><article class="study-note"><div class="formula-detail-heading"><div><p class="comparison-eyebrow">${es ? `Fórmula ${formula.number} de 63` : `Formula ${formula.number} of 63`}</p><h1 class="study-note__title">${esc(formula.name)}</h1></div>${riskBadge(formula, es)}</div>
<p>${v('pattern')}</p>
<section class="study-note__block"><h2 class="study-note__heading">${es ? 'Navegación diagnóstica' : 'Diagnostic navigation'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Categoría' : 'Category'}</th><td>${category}</td></tr><tr><th>${es ? 'Ruta clínica' : 'Clinical route'}</th><td>${esc(routeText)}</td></tr><tr><th>${es ? 'Patrón' : 'Pattern'}</th><td>${v('pattern')}</td></tr><tr><th>${es ? 'Pista decisiva' : 'Decisive clue'}</th><td>${v('clue')}</td></tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--green">${es ? 'Arquitectura esencial' : 'Essential architecture'}</h2><table class="study-note__focus"><tbody><tr><th>${es ? 'Hierba(s) emperador' : 'King herb(s)'}</th><td>${esc(formula.kings)}</td></tr><tr><th>${es ? 'Acción' : 'Action'}</th><td>${v('action')}</td></tr><tr><th>${es ? 'Ingredientes' : 'Ingredients'}</th><td>${v('ingredients')}</td></tr></tbody></table></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--purple">${es ? 'Seguridad y procedencia' : 'Safety and provenance'}</h2><p>${v('safety')}</p><p><small>${es ? 'Contenido educativo derivado del compendio histórico aportado y enlazado con la biblioteca actual. No sustituye diagnóstico ni prescripción profesional.' : 'Educational content derived from the supplied historical compendium and linked to the current library. It does not replace diagnosis or professional prescribing.'}</small></p></section>
<section class="study-note__block"><h2 class="study-note__heading study-note__heading--orange">${es ? 'Conectar con la biblioteca' : 'Connect to the library'}</h2><div class="formula-related-links"><a class="formula-related-link formula-related-link--practice" href="formula-practice${suffix}.html?formula=${formula.number}">${es ? `Practicar ${formula.practiceCount} preguntas relacionadas →` : `Practice ${formula.practiceCount} related questions →`}</a>${expanded}<a class="formula-related-link" href="formulas${suffix}.html?q=${query}">${es ? 'Buscar comparaciones de esta fórmula →' : 'Find comparisons for this formula →'}</a><a class="formula-related-link" href="safety${suffix}.html">${es ? 'Abrir Biblioteca de Seguridad →' : 'Open Safety Library →'}</a></div></section>
</article></main><footer class="comparison-footer"><div class="comparison-shell">${guideName(es)} · ${es ? 'Estudio comparativo y seguro.' : 'Comparative, safety-aware study.'}</div></footer></body></html>`;
}

function indexPage(es) {
  const suffix = es ? '-es' : '';
  const categoryOptions = classicFormulaCategories.map(([key, en, esLabel]) => `<option value="${key}">${es ? esLabel : en}</option>`).join('');
  const routeOptions = classicFormulaRoutes.map(([key, en, esLabel]) => `<option value="${key}">${es ? esLabel : en}</option>`).join('');
  const cards = formulas.map(formula => {
    const category = es ? formula.category.es : formula.category.en;
    const routeText = formula.routeLabels.map(route => es ? route.es : route.en).join(' · ');
    const pattern = es ? formula.patternEs : formula.pattern;
    const clue = es ? formula.clueEs : formula.clue;
    return `<article class="comparison-card formula-navigator-card" data-sequence="${formula.number}" data-formula-number="${formula.number}" data-category="${formula.categoryKey}" data-routes="${formula.routeKeys.join(' ')}" data-search="${esc(formula.search)}"><div class="formula-card-topline"><span class="comparison-card__type">${es ? `Fórmula ${formula.number}` : `Formula ${formula.number}`}</span>${riskBadge(formula, es)}</div><h3>${esc(formula.name)}</h3><p>${esc(pattern)}</p><p class="formula-card-clue"><strong>${es ? 'Pista:' : 'Clue:'}</strong> ${esc(clue)}</p><div class="comparison-card__category"><strong>${esc(category)}</strong><br><span>${esc(routeText)}</span><br><span>${es ? 'Emperador:' : 'King:'} ${esc(formula.kings)}</span><br><span><strong>${formula.practiceCount}</strong> ${es ? 'preguntas relacionadas' : 'related questions'}</span></div><label class="formula-compare-toggle"><input type="checkbox" value="${formula.number}" data-compare-formula> <span>${es ? 'Comparar' : 'Compare'}</span></label><div class="formula-card-actions"><a class="comparison-card__open" data-chapter-url="${formula.slug}${suffix}.html" data-chapter-number="${formula.number}" href="${formula.slug}${suffix}.html">${es ? 'Abrir ficha →' : 'Open profile →'}</a><a class="formula-card-practice" href="formula-practice${suffix}.html?formula=${formula.number}">${es ? 'Practicar' : 'Practice'}</a></div></article>`;
  }).join('');
  const clientData = formulas.map(formula => ({
    number: formula.number, name: formula.name,
    category: es ? formula.category.es : formula.category.en,
    pattern: es ? formula.patternEs : formula.pattern,
    action: es ? formula.actionEs : formula.action,
    clue: es ? formula.clueEs : formula.clue,
    kings: formula.kings
  }));
  const safeJson = JSON.stringify(clientData).replaceAll('<', '\\u003c');
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? 'Guía para buscar, estudiar y comparar 63 fórmulas herbales chinas.' : 'A guide to finding, studying, and comparing 63 Chinese herbal formulas.'}"><title>${guideName(es)} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="formula-navigator${es ? '' : '-es'}.html"><link rel="stylesheet" href="comparisons.css?v=${cssVersion}"><script defer src="formula-navigator.js?v=20261028-2"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner"><a class="comparison-brand" href="formula-navigator${es ? '-es' : ''}.html"><span class="comparison-brand__mark" aria-hidden="true">☿</span><span>Herbs Practice Exams</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" href="formula-navigator${es ? '' : '-es'}.html">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology">${es ? '← Herbolaria China' : '← Chinese Herbology'}</a></span></div></header>
<main class="comparison-shell"><section class="comparison-hero formula-navigator-hero"><p class="comparison-eyebrow">${es ? 'Estudio y decisión clínica' : 'Study and clinical decision'}</p><h1>${guideName(es)}</h1><p>${es ? 'Busca una fórmula, practica preguntas o compara pistas clínicas dentro del mismo espacio.' : 'Find a formula, practice questions, or compare clinical clues within the same workspace.'}</p><p class="formula-guide-summary">${es ? `Incluye 63 fórmulas organizadas en 14 categorías y ${practiceData.totals.questions} preguntas de práctica.` : `Includes 63 formulas organized into 14 categories and ${practiceData.totals.questions} practice questions.`}</p></section>${nav(es, 'finder')}
<section class="formula-navigator-panel" id="formula-finder" aria-labelledby="navigator-tools-title"><div class="formula-guide-section-heading"><p class="comparison-eyebrow">${es ? 'Paso 1' : 'Step 1'}</p><h2 id="navigator-tools-title">${es ? 'Busca una fórmula' : 'Find a formula'}</h2><p>${es ? 'Escribe una pista o usa uno o ambos filtros. Los resultados se actualizan automáticamente.' : 'Enter a clue or use one or both filters. Results update automatically.'}</p></div><label class="formula-search-label"><span>${es ? 'Nombre, patrón, acción, síntoma o hierba' : 'Name, pattern, action, symptom, or herb'}</span><input class="comparison-search" type="search" placeholder="${es ? 'Ej.: tos, vértigo, sangrado, Ban Xia…' : 'E.g. cough, vertigo, bleeding, Ban Xia…'}" data-formula-search></label><div class="formula-filter-selects"><label><span>${es ? 'Categoría de la fórmula' : 'Formula category'}</span><select data-formula-category-filter><option value="all">${es ? 'Todas las categorías' : 'All categories'}</option>${categoryOptions}</select></label><label><span>${es ? 'Manifestación principal' : 'Main manifestation'}</span><select data-formula-route-filter><option value="all">${es ? 'Todas las manifestaciones' : 'All manifestations'}</option>${routeOptions}</select></label></div></section>
<section class="formula-compare-panel" data-formula-compare-panel hidden aria-live="polite"><div class="formula-compare-heading"><div><p class="comparison-eyebrow">${es ? 'Modo comparación' : 'Comparison mode'}</p><h2>${es ? 'Compara hasta cuatro fórmulas' : 'Compare up to four formulas'}</h2></div><button type="button" data-clear-comparison>${es ? 'Limpiar' : 'Clear'}</button></div><p data-compare-guidance>${es ? 'Selecciona al menos dos tarjetas.' : 'Select at least two cards.'}</p><div class="formula-compare-table-wrap" data-compare-output></div></section>
<section id="formula-results"><header class="comparison-summary"><div><p class="comparison-eyebrow">${es ? 'Paso 2' : 'Step 2'}</p><h2>${es ? 'Fórmulas encontradas' : 'Matching formulas'}</h2></div><p><span data-visible-count>63</span> ${es ? 'de 63 fórmulas' : 'of 63 formulas'}</p></header><div class="comparison-grid" data-comparison-grid>${cards}</div><p class="comparison-empty" data-formula-empty hidden>${es ? 'Ninguna fórmula coincide. Prueba otra manifestación o elimina un filtro.' : 'No formulas match. Try another manifestation or clear a filter.'}</p></section>
<section class="formula-source-note"><h2>${es ? 'Cómo usar las fichas' : 'How to use the profiles'}</h2><p>${es ? 'Abre una fórmula para revisar su patrón, acción, ingredientes y seguridad. Marca entre dos y cuatro tarjetas si quieres compararlas en esta misma página.' : 'Open a formula to review its pattern, action, ingredients, and safety. Select two to four cards to compare them on this page.'}</p></section></main>
<script type="application/json" id="formula-navigator-data">${safeJson}</script><footer class="comparison-footer"><div class="comparison-shell">${es ? 'Material educativo histórico normalizado; no sustituye diagnóstico ni prescripción profesional.' : 'Normalized historical educational material; not a substitute for diagnosis or professional prescribing.'}</div></footer></body></html>`;
}

function practicePage(es) {
  const suffix = es ? '-es' : '';
  const alternate = `formula-practice${es ? '' : '-es'}.html`;
  return `<!doctype html><html lang="${es ? 'es' : 'en'}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="${es ? `Práctica con ${practiceData.totals.questions} preguntas centradas en las 63 fórmulas principales.` : `Practice with ${practiceData.totals.questions} questions focused on the 63 core formulas.`}"><title>${es ? 'Práctica de Fórmulas' : 'Formula Practice'} | AcupunctureTests</title><link rel="alternate" hreflang="${es ? 'en' : 'es'}" href="${alternate}"><link rel="stylesheet" href="comparisons.css?v=${practiceCssVersion}"><script defer src="formula-practice.js?v=20261028-4"></script></head><body>
<header class="comparison-topbar"><div class="comparison-shell comparison-topbar__inner" style="flex-wrap:wrap;padding:10px 0"><a class="comparison-brand" href="formula-navigator${suffix}.html"><span class="comparison-brand__mark" aria-hidden="true">☿</span><span>Herbs Practice Exams</span></a><span style="display:flex;gap:8px;flex-wrap:wrap"><a class="comparison-back" data-practice-language href="${alternate}">${es ? 'English' : 'Español'}</a><a class="comparison-back" href="/blocks/myprogress/study-guide.php?area=herbology">${es ? '← Herbolaria China' : '← Chinese Herbology'}</a></span></div></header>
<main class="comparison-shell formula-practice-page"><section class="comparison-hero formula-practice-hero"><p class="comparison-eyebrow">${es ? 'Práctica conectada · Aprende a tu ritmo' : 'Connected practice · Learn at your pace'}</p><h1>${es ? 'Práctica de Fórmulas' : 'Formula Practice'}</h1><p>${es ? 'Crea simulacros de 10, 25, 50 o 100 preguntas, o abre todas las disponibles para una fórmula.' : 'Build 10, 25, 50, or 100-question mocks, or open every available question for one formula.'}</p><div class="formula-navigator-stats"><span><strong>${practiceData.totals.questions}</strong> ${es ? 'preguntas sobre las fórmulas principales' : 'questions on the core formulas'}</span><span><strong>63</strong> ${es ? 'fórmulas para estudiar' : 'formulas to study'}</span><span><strong>✓</strong> ${es ? 'práctica personalizada con resultados' : 'personalized practice with results'}</span></div></section>${nav(es, 'practice')}
${es ? '<aside class="formula-translation-notice" role="note"><strong>Traducción automática al español.</strong> Puede contener errores de idioma o terminología. Ante cualquier diferencia, consulta el texto original en inglés.</aside>' : ''}
<p class="formula-practice-status" data-practice-status role="status">${es ? 'Cargando banco de preguntas…' : 'Loading question bank…'}</p>
<section class="formula-practice-setup" data-practice-setup hidden aria-labelledby="practice-setup-title"><div><p class="comparison-eyebrow">${es ? 'Configurar sesión' : 'Configure session'}</p><h2 id="practice-setup-title">${es ? 'Elige una forma de practicar' : 'Choose one way to practice'}</h2></div><fieldset class="formula-practice-focus"><legend>${es ? 'Enfoque de la práctica' : 'Practice focus'}</legend><div><button type="button" data-practice-mode="all" aria-pressed="true">${es ? 'Banco completo' : 'Full bank'}</button><button type="button" data-practice-mode="formula" aria-pressed="false">${es ? 'Por fórmula' : 'By formula'}</button><button type="button" data-practice-mode="category" aria-pressed="false">${es ? 'Por categoría' : 'By category'}</button></div><p data-practice-mode-help>${es ? 'Incluye preguntas de todas las fórmulas y temas generales.' : 'Includes questions from every formula and general topic.'}</p></fieldset><div class="formula-practice-controls"><label data-practice-formula-field hidden><span>${es ? 'Selecciona una fórmula' : 'Select a formula'}</span><select data-practice-formula></select></label><label data-practice-category-field hidden><span>${es ? 'Selecciona una categoría' : 'Select a category'}</span><select data-practice-category></select></label></div><fieldset class="formula-practice-size"><legend>${es ? 'Número de preguntas' : 'Number of questions'}</legend><div>${[10, 25, 50, 100].map(size => `<button type="button" data-practice-size="${size}" aria-pressed="${size === 25}">${size}</button>`).join('')}<button type="button" data-practice-size-max aria-pressed="false" hidden></button></div><p class="formula-practice-max-help" data-practice-max-help></p></fieldset><div class="formula-practice-availability"><p><strong data-practice-available>0</strong> ${es ? 'preguntas disponibles para este enfoque' : 'questions available for this focus'}</p><button type="button" class="formula-practice-start" data-practice-start>${es ? 'Comenzar práctica →' : 'Start practice →'}</button></div></section>
<section class="formula-practice-history" data-practice-history hidden aria-labelledby="practice-history-title"><div><p class="comparison-eyebrow">${es ? 'Progreso en este dispositivo' : 'Progress on this device'}</p><h2 id="practice-history-title">${es ? 'Historial acumulado' : 'Cumulative history'}</h2></div><div class="formula-practice-history-stats" data-practice-history-stats></div><div data-practice-weaknesses></div><button type="button" class="formula-practice-reset" data-practice-reset>${es ? 'Borrar historial local' : 'Clear local history'}</button></section>
<section class="formula-practice-workspace" data-practice-workspace hidden><div class="formula-practice-session-actions"><button type="button" data-practice-return>${es ? '← Volver a configuración' : '← Back to setup'}</button>${es ? '<button type="button" class="formula-practice-language-toggle" data-question-language aria-pressed="false">Ver inglés original</button>' : ''}</div><div class="formula-practice-progress" aria-live="polite"><div><strong><span data-practice-position>0</span> / <span data-practice-total>25</span></strong> ${es ? 'respondidas' : 'answered'}</div><strong><span data-practice-score>0</span> ${es ? 'correctas' : 'correct'}</strong></div><div class="formula-practice-progress-track" aria-hidden="true"><span data-practice-progress-bar></span></div><p class="formula-practice-skip-note">${es ? 'Todas las preguntas aparecen abajo. Puedes responderlas en cualquier orden y dejar sin contestar las que quieras saltar.' : 'All questions appear below. Answer them in any order and leave any question unanswered if you want to skip it.'}</p><div class="formula-practice-question-list" data-practice-question-list></div><button type="button" class="formula-practice-finish" data-practice-finish>${es ? 'Finalizar práctica' : 'Finish practice'}</button></section>
<section class="formula-practice-results" data-practice-results hidden aria-labelledby="practice-results-title"><p class="comparison-eyebrow">${es ? 'Sesión completada' : 'Session complete'}</p><h2 id="practice-results-title">${es ? 'Resultado' : 'Result'}</h2><p class="formula-practice-final-score" data-practice-final-score></p><div class="formula-practice-breakdown" data-practice-breakdown></div><div class="formula-hero-actions"><button type="button" data-practice-repeat>${es ? 'Repetir con los mismos filtros' : 'Repeat with same filters'}</button><button type="button" data-practice-configure>${es ? 'Cambiar configuración' : 'Change setup'}</button></div></section>
<section class="formula-source-note"><h2>${es ? 'Alcance y seguridad del banco' : 'Bank scope and safety'}</h2><p>${es ? 'La traducción al español es automática y puede contener errores. Cada pregunta permite consultar el inglés original, que prevalece ante cualquier diferencia. Solo se incluyen preguntas con una única respuesta marcada explícitamente en la fuente.' : 'Questions preserve their original English wording to avoid changing clinical meaning. Only questions with one explicitly marked source answer are included. An association means the formula appears in the prompt, options, or feedback; it does not mean that formula is always the correct answer.'}</p></section></main>
<footer class="comparison-footer"><div class="comparison-shell">${es ? 'Práctica educativa; no sustituye diagnóstico ni prescripción profesional.' : 'Educational practice; not a substitute for diagnosis or professional prescribing.'}</div></footer></body></html>`;
}

for (const formula of formulas) {
  writeFileSync(resolve(root, `${formula.slug}.html`), detailPage(formula, false));
  writeFileSync(resolve(root, `${formula.slug}-es.html`), detailPage(formula, true));
}
writeFileSync(resolve(root, 'formula-navigator.html'), indexPage(false));
writeFileSync(resolve(root, 'formula-navigator-es.html'), indexPage(true));
writeFileSync(resolve(root, 'formula-practice.html'), practicePage(false));
writeFileSync(resolve(root, 'formula-practice-es.html'), practicePage(true));

console.log(`Generated ${formulas.length * 2} navigator profiles, 2 indexes, and 2 practice pages`);
