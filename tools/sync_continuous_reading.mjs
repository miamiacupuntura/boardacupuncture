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
for (const language of ['en', 'es']) {
  const suffix = language === 'es' ? '-es' : '';
  for (const group of groups) {
    const file = resolve(root, `${group}${suffix}.html`);
    if (!existsSync(file)) throw new Error(`Missing group index: ${file}`);
    let html = readFileSync(file, 'utf8');
    if (html.includes('data-continuous-reading')) continue;
    const heroStart = html.indexOf('<section class="comparison-hero"');
    if (heroStart === -1) throw new Error(`Hero not found in ${file}`);
    const heroEnd = html.indexOf('</section>', heroStart);
    if (heroEnd === -1) throw new Error(`Hero ending not found in ${file}`);
    const label = language === 'es' ? 'Comenzar lectura continua →' : 'Start continuous reading →';
    const button = `<p data-continuous-reading style="margin-top:22px"><a class="comparison-card__open" href="continuous-reader.html?group=${group}&amp;lang=${language}">${label}</a></p>`;
    html = `${html.slice(0, heroEnd)}${button}${html.slice(heroEnd)}`;
    writeFileSync(file, html);
    updated += 1;
  }
}

console.log(`Added continuous-reading entry points to ${updated} group indexes`);

