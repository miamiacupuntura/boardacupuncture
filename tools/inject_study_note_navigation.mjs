#!/usr/bin/env node

import fs from 'node:fs';
import path from 'node:path';

const args = new Set(process.argv.slice(2));
const write = args.has('--write');
const check = args.has('--check');
const rootArgument = process.argv.find(argument => argument.startsWith('--root='));
const root = path.resolve(rootArgument ? rootArgument.slice('--root='.length) : 'study-guide/comparisons');
const source = '/study-guide/comparisons/study-note-navigation.js?v=20261104-5';
const tag = `<script src="${source}" defer></script>`;
const marker = '/study-guide/comparisons/study-note-navigation.js';

if (!fs.existsSync(root) || !fs.statSync(root).isDirectory()) {
  throw new Error(`Study Note directory does not exist: ${root}`);
}

const htmlFiles = [];
const walk = directory => {
  for (const entry of fs.readdirSync(directory, {withFileTypes: true})) {
    const absolute = path.join(directory, entry.name);
    if (entry.isDirectory()) {
      walk(absolute);
    } else if (entry.isFile() && entry.name.endsWith('.html')) {
      htmlFiles.push(absolute);
    }
  }
};
walk(root);

let studyNotes = 0;
let installed = 0;
let missing = 0;
let changed = 0;
const failures = [];

for (const file of htmlFiles) {
  const original = fs.readFileSync(file, 'utf8');
  if (!/class=["'][^"']*\bstudy-note\b/i.test(original)) {
    continue;
  }
  studyNotes += 1;
  if (original.includes(marker)) {
    installed += 1;
    if (write) {
      const normalized = original.replace(
        /<script\s+src=["']\/study-guide\/comparisons\/study-note-navigation\.js(?:\?[^"']*)?["']\s+defer><\/script>/i,
        tag
      );
      if (normalized !== original) {
        fs.writeFileSync(file, normalized);
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
    failures.push(path.relative(root, file));
    continue;
  }
  const updated = original.replace(/<\/body>/i, `${tag}</body>`);
  fs.writeFileSync(file, updated);
  changed += 1;
}

console.log(JSON.stringify({root, studyNotes, installed, missing, changed, failures: failures.length}, null, 2));

if (failures.length) {
  console.error(`Could not inject navigation into: ${failures.slice(0, 20).join(', ')}`);
  process.exit(1);
}
if (studyNotes < 1) {
  console.error('No .study-note pages were found.');
  process.exit(1);
}
if (check && missing > 0) {
  console.error(`${missing} Study Note pages are missing the shared navigation loader.`);
  process.exit(1);
}
if (!write && !check) {
  console.log('Dry run only. Pass --write to inject the loader or --check to enforce complete coverage.');
}
