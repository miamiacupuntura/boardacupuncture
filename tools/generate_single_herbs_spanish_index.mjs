import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const englishIndex = readFileSync(resolve(root, 'index.html'), 'utf8');

function plain(value) {
  return String(value)
    .replace(/<br\s*\/?\s*>/gi, ' ')
    .replace(/<[^>]+>/g, '')
    .replaceAll('&amp;', '&')
    .replaceAll('&quot;', '"')
    .replaceAll('&#39;', "'")
    .replaceAll('&nbsp;', ' ')
    .replace(/\s+/g, ' ')
    .trim();
}

function esc(value) {
  return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;');
}

function shorten(value, limit = 250) {
  if (value.length <= limit) return value;
  const shortened = value.slice(0, limit - 1).replace(/\s+\S*$/, '');
  return `${shortened}…`;
}

const englishLinks = [...englishIndex.matchAll(/class="comparison-card__open" href="([^"]+\.html)"/g)].map(match => match[1]);
const cards = [];

for (const englishFile of englishLinks) {
  const spanishFile = englishFile.replace(/\.html$/, '-es.html');
  const spanishPath = resolve(root, spanishFile);
  if (!existsSync(spanishPath)) continue;

  const html = readFileSync(spanishPath, 'utf8');
  const title = plain(html.match(/<h1[^>]*class="study-note__title"[^>]*>([\s\S]*?)<\/h1>/i)?.[1] ?? englishFile);
  const why = plain(html.match(/<th[^>]*>Por qué importa<\/th>\s*<td[^>]*>([\s\S]*?)<\/td>/i)?.[1] ?? 'Compara las diferencias decisivas entre estas hierbas medicinales.');
  const category = plain(
    html.match(/<th[^>]*>Categoría compartida<\/th>\s*<td[^>]*>([\s\S]*?)<\/td>/i)?.[1]
      ?? html.match(/<th[^>]*>Categorías?<\/th>\s*<td[^>]*>([\s\S]*?)<\/td>/i)?.[1]
      ?? 'Comparación de hierbas'
  );
  const search = plain(`${title} ${why} ${category}`).toLowerCase();

  cards.push(`<article class="comparison-card" data-type="herbs" data-search="${esc(search)}"><span class="comparison-card__type">Hierbas</span><h3>${esc(title)}</h3><p>${esc(shorten(why))}</p><div class="comparison-card__category"><strong>Enfoque:</strong><br>${esc(category)}</div><a class="comparison-card__open" href="${esc(spanishFile)}">Abrir Nota de Estudio →</a></article>`);
}

const page = `<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Notas comparativas en español sobre hierbas individuales de medicina china.">
  <title>Comparaciones de Hierbas Individuales | AcupunctureTests</title>
  <link rel="alternate" hreflang="en" href="index.html">
  <link rel="stylesheet" href="comparisons.css?v=20261027-4">
  <link rel="stylesheet" href="herb-comparison-bank.css?v=20261028-1">
  <script defer src="comparisons.js?v=20261025-1"></script>
</head>
<body>
  <header class="comparison-topbar">
    <div class="comparison-shell comparison-topbar__inner">
      <a class="comparison-brand" href="/my/"><span class="comparison-brand__mark" aria-hidden="true">☯</span><span>AcupunctureTests</span></a>
      <span style="display:flex;flex-wrap:wrap;gap:8px"><a class="comparison-back" href="index.html">English</a><a class="comparison-back" href="/my/">← Dashboard</a></span>
    </div>
  </header>
  <main class="comparison-shell">
    <section class="comparison-hero" aria-labelledby="comparisons-title"><p class="comparison-eyebrow">Paso 1 · Hierbas Individuales</p><h1 id="comparisons-title">Comparaciones de Hierbas Individuales</h1><p>Compara hierbas medicinales chinas por categoría, propiedades, acciones, indicaciones y las pistas decisivas que las distinguen en los exámenes.</p></section>
    <aside class="herb-bank-entry"><div><p class="comparison-eyebrow">Nuevo · Secuencia completa del libro</p><h2>Banco de Preguntas de Comparaciones Herbales</h2><p>Estudia 249 preguntas, 17 capítulos, 117 gráficos de fuerza y flashcards de memoria.</p></div><a href="herb-comparison-bank-es.html">Abrir el banco →</a></aside>
    <nav class="comparison-collection-links" aria-label="Grupos de estudio"><a aria-current="page" href="index-es.html">Comparaciones de Hierbas</a><a href="formulas-es.html">Comparaciones de Fórmulas</a><a href="pathologies-es.html">Comparaciones de Patologías</a><a href="patterns-es.html">Comparaciones de Patterns</a><a href="families-es.html">Familias de Fórmulas</a><a href="name-traps-es.html">Trampas de Nombres</a><a href="herb-families-es.html">Familias y Partes</a><a href="safety-es.html">Biblioteca de Seguridad</a><a href="formula-name-traps-es.html">Trampas de Fórmulas</a><a href="misleading-formula-names-es.html">Nombres Engañosos</a><a href="advanced-name-traps-es.html">Trampas Avanzadas</a><a href="supplemental-herb-families-es.html">Familias Adicionales</a><a href="patterns-tips-maciocia-es.html">Patterns Tips Maciocia</a><a href="most-important-formulas-es.html">Fórmulas Esenciales</a></nav>
    <section class="comparison-tools comparison-tools--search-only" aria-label="Buscar comparaciones de hierbas"><label><span class="sr-only">Buscar</span><input class="comparison-search" type="search" placeholder="Buscar hierbas o categorías" data-comparison-search></label></section>
    <section><header class="comparison-summary"><h2>Notas disponibles</h2><p><span data-visible-count>${cards.length}</span> notas comparativas en español</p></header><div class="comparison-grid" data-comparison-grid>${cards.join('\n')}</div><p class="comparison-empty" data-comparison-empty hidden>No hay comparaciones que coincidan con la búsqueda.</p></section>
  </main>
  <footer class="comparison-footer"><div class="comparison-shell">Biblioteca en español en crecimiento · Las fichas restantes se incorporarán por lotes.</div></footer>
</body>
</html>`;

writeFileSync(resolve(root, 'index-es.html'), page);
console.log(`Generated Spanish Single Herbs index with ${cards.length} translated notes`);
