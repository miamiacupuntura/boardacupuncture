import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const indexes = [
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
  'most-important-formulas'
];

let updated = 0;
for (const language of ['en', 'es']) {
  const suffix = language === 'es' ? '-es' : '';
  const link = `<a href="formula-theory${suffix}.html">${language === 'es' ? 'Composición de Fórmulas' : 'Formula Composition'}</a>`;
  for (const base of indexes) {
    const file = resolve(root, `${base}${suffix}.html`);
    if (!existsSync(file)) throw new Error(`Missing study-group index: ${file}`);
    let html = readFileSync(file, 'utf8');
    if (html.includes(`href="formula-theory${suffix}.html"`)) continue;
    const marker = '</nav>';
    const markerIndex = html.indexOf(marker);
    if (markerIndex === -1) throw new Error(`Navigation not found in ${file}`);
    html = `${html.slice(0, markerIndex)}${link}${html.slice(markerIndex)}`;
    writeFileSync(file, html);
    updated += 1;
  }
}

console.log(`Added Formula Composition navigation to ${updated} indexes`);

