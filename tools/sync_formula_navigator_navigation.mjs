import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../study-guide/comparisons');
const indexes = [
  'index', 'formulas', 'pathologies', 'patterns', 'families', 'dui-yao',
  'name-traps', 'herb-families', 'safety', 'formula-name-traps',
  'misleading-formula-names', 'advanced-name-traps',
  'supplemental-herb-families', 'patterns-tips-maciocia',
  'most-important-formulas', 'formula-theory', 'herbal-incompatibilities',
  'secondary-actions'
];

let updated = 0;
for (const language of ['en', 'es']) {
  const suffix = language === 'es' ? '-es' : '';
  const href = `formula-navigator${suffix}.html`;
  for (const base of indexes) {
    const file = resolve(root, `${base}${suffix}.html`);
    if (!existsSync(file)) throw new Error(`Missing study-group index: ${file}`);
    const html = readFileSync(file, 'utf8');
    const escapedHref = href.replace('.', '\\.');
    const updatedHtml = html.replace(new RegExp(`<a(?: [^>]*)?href="${escapedHref}">[^<]*</a>`, 'g'), '');
    if (updatedHtml === html) continue;
    writeFileSync(file, updatedHtml);
    updated += 1;
  }
}

console.log(`Removed Formula Navigator navigation from ${updated} indexes`);
