#!/usr/bin/env node

import fs from 'node:fs';
import path from 'node:path';

const write = process.argv.includes('--write');
const check = process.argv.includes('--check');
const rootArgument = process.argv.find(argument => argument.startsWith('--root='));
const root = path.resolve(rootArgument ? rootArgument.slice('--root='.length) : 'study-guide/comparisons');
const source = '/study-guide/comparisons/library-navigation.js?v=20261107-8';
const marker = '/study-guide/comparisons/library-navigation.js';
const tag = `<script src="${source}" defer></script>`;

if (!fs.existsSync(root) || !fs.statSync(root).isDirectory()) {
  throw new Error(`Comparison directory does not exist: ${root}`);
}

const files = fs.readdirSync(root).filter(file => file.endsWith('.html'));
let installed = 0;
let missing = 0;
let changed = 0;
const failures = [];

for (const file of files) {
  const absolute = path.join(root, file);
  const original = fs.readFileSync(absolute, 'utf8');
  if (original.includes(marker)) {
    installed += 1;
    if (write) {
      const normalized = original.replace(
        /<script\s+src=["']\/study-guide\/comparisons\/library-navigation\.js(?:\?[^"']*)?["']\s+defer><\/script>/i,
        tag
      );
      if (normalized !== original) {
        fs.writeFileSync(absolute, normalized);
        changed += 1;
      }
    }
    continue;
  }
  missing += 1;
  if (!write) {
    continue;
  }
  if (!/<\/body>/i.test(original)) {
    failures.push(file);
    continue;
  }
  fs.writeFileSync(absolute, original.replace(/<\/body>/i, `${tag}</body>`));
  changed += 1;
}

console.log(JSON.stringify({root, htmlFiles: files.length, installed, missing, changed, failures: failures.length}, null, 2));

if (failures.length) {
  console.error(`Could not inject Library navigation into: ${failures.slice(0, 20).join(', ')}`);
  process.exit(1);
}
if (!files.length) {
  console.error('No comparison HTML files were found.');
  process.exit(1);
}
if (check && missing > 0) {
  console.error(`${missing} comparison pages are missing the Library navigation loader.`);
  process.exit(1);
}
