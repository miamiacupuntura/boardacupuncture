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
  'formula-theory'
];

let updated = 0;
let wiredCards = 0;
for (const language of ['en', 'es']) {
  const suffix = language === 'es' ? '-es' : '';
  for (const group of groups) {
    const file = resolve(root, `${group}${suffix}.html`);
    if (!existsSync(file)) throw new Error(`Missing group index: ${file}`);
    let html = readFileSync(file, 'utf8');
    const original = html;
    html = html.replace(/<p data-continuous-reading[^>]*>[\s\S]*?<\/p>/g, '');
    const cardPattern = /<a class="comparison-card__open"(?: data-chapter-url="([^"]+)")? href="([^"]+)">[\s\S]*?<\/a>/g;
    const cards = [...html.matchAll(cardPattern)];
    if (!cards.length) throw new Error(`No study cards found in ${file}`);
    let chapter = 0;
    html = html.replace(cardPattern, (_match, storedUrl, currentUrl) => {
      chapter += 1;
      const detailUrl = storedUrl || currentUrl;
      const readerUrl = `continuous-reader.html?group=${group}&amp;lang=${language}&amp;chapter=${chapter}`;
      const label = language === 'es'
        ? `Leer capítulo ${chapter} de ${cards.length} →`
        : `Read chapter ${chapter} of ${cards.length} →`;
      return `<a class="comparison-card__open" data-chapter-url="${detailUrl}" href="${readerUrl}">${label}</a>`;
    });
    writeFileSync(file, html);
    wiredCards += chapter;
    if (html !== original) updated += 1;
  }
}

console.log(`Wired ${wiredCards} cards for continuous reading across ${updated} updated indexes`);

