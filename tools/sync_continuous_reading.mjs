import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const groups = [
  'index',
  'formulas',
  'pathologies',
  'patterns',
  'families',
  'dui-yao',
  'name-traps',
  'herb-families',
  'safety',
  'formula-name-traps',
  'misleading-formula-names',
  'advanced-name-traps',
  'supplemental-herb-families',
  'patterns-tips-maciocia',
  'most-important-formulas',
  'formula-navigator',
  'formula-theory'
];

let updated = 0;
let wiredCards = 0;

function decodeText(value) {
  return String(value)
    .replace(/<[^>]*>/g, ' ')
    .replace(/&#(\d+);/g, (_match, code) => String.fromCodePoint(Number(code)))
    .replace(/&#x([0-9a-f]+);/gi, (_match, code) => String.fromCodePoint(Number.parseInt(code, 16)))
    .replaceAll('&amp;', '&')
    .replaceAll('&quot;', '"')
    .replaceAll('&#39;', "'")
    .replaceAll('&lt;', '<')
    .replaceAll('&gt;', '>');
}

function normalise(value) {
  return decodeText(value).toLocaleLowerCase().normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, ' ').trim()
    .replace(/^[^a-z0-9]+/, '');
}

function displayOrder(html, file) {
  const articlePattern = /<article class="[^"]*\bcomparison-card\b[^"]*"([^>]*)>([\s\S]*?)<\/article>/g;
  const cards = [...html.matchAll(articlePattern)].map((match, sourceIndex) => {
    const attributes = match[1];
    const body = match[2];
    const heading = body.match(/<h3>([\s\S]*?)<\/h3>/)?.[1];
    const link = body.match(/<a class="comparison-card__open"(?: data-chapter-url="([^"]+)")?(?: data-chapter-number="[^"]+")? href="([^"]+)">[\s\S]*?<\/a>/);
    if (!heading || !link) throw new Error(`Incomplete comparison card ${sourceIndex + 1} in ${file}`);
    const sequenceValue = attributes.match(/data-sequence="([^"]+)"/)?.[1];
    return {
      sourceIndex,
      title: normalise(heading),
      detailUrl: link[1] || link[2],
      hasSequence: sequenceValue !== undefined,
      sequence: Number(sequenceValue)
    };
  });
  if (!cards.length) throw new Error(`No study cards found in ${file}`);
  const ordered = [...cards].sort((first, second) => {
    if (first.hasSequence && second.hasSequence && Number.isFinite(first.sequence) && Number.isFinite(second.sequence)) {
      return first.sequence - second.sequence;
    }
    const titleOrder = first.title.localeCompare(second.title, 'en', {numeric: true, sensitivity: 'base'});
    return titleOrder || first.sourceIndex - second.sourceIndex;
  });
  return new Map(ordered.map((card, index) => [card.detailUrl, index + 1]));
}

for (const language of ['en', 'es']) {
  const suffix = language === 'es' ? '-es' : '';
  for (const group of groups) {
    const file = resolve(root, `${group}${suffix}.html`);
    if (!existsSync(file)) throw new Error(`Missing group index: ${file}`);
    let html = readFileSync(file, 'utf8');
    const original = html;
    html = html.replace(/<p data-continuous-reading[^>]*>[\s\S]*?<\/p>/g, '');
    const orderByUrl = displayOrder(html, file);
    const cardPattern = /<a class="comparison-card__open"(?: data-chapter-url="([^"]+)")?(?: data-chapter-number="[^"]+")? href="([^"]+)">[\s\S]*?<\/a>/g;
    const cards = [...html.matchAll(cardPattern)];
    let processed = 0;
    html = html.replace(cardPattern, (_match, storedUrl, currentUrl) => {
      const detailUrl = storedUrl || currentUrl;
      const chapter = orderByUrl.get(detailUrl);
      if (!chapter) throw new Error(`Display order missing for ${detailUrl} in ${file}`);
      processed += 1;
      const readerUrl = `continuous-reader.html?group=${group}&amp;lang=${language}&amp;chapter=${chapter}`;
      const label = language === 'es' ? 'Leer y continuar →' : 'Read and continue →';
      return `<a class="comparison-card__open" data-chapter-url="${detailUrl}" data-chapter-number="${chapter}" href="${readerUrl}">${label}</a>`;
    });
    writeFileSync(file, html);
    wiredCards += processed;
    if (html !== original) updated += 1;
  }
}

console.log(`Wired ${wiredCards} cards for continuous reading across ${updated} updated indexes`);

