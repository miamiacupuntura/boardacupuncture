#!/usr/bin/env node

import fs from 'node:fs';
import path from 'node:path';

const rootArgument = process.argv.find(argument => argument.startsWith('--root='));
const outputArgument = process.argv.find(argument => argument.startsWith('--output='));
const check = process.argv.includes('--check');
const root = path.resolve(rootArgument ? rootArgument.slice('--root='.length) : 'study-guide/comparisons');
const output = path.resolve(outputArgument
  ? outputArgument.slice('--output='.length)
  : path.join(root, 'study-note-navigation-manifest.js'));

const decodeEntities = value => String(value || '')
  .replace(/&nbsp;/gi, ' ')
  .replace(/&amp;/gi, '&')
  .replace(/&quot;/gi, '"')
  .replace(/&#39;|&apos;/gi, "'")
  .replace(/&lt;/gi, '<')
  .replace(/&gt;/gi, '>')
  .replace(/&#(\d+);/g, (_, number) => String.fromCodePoint(Number(number)))
  .replace(/&#x([\da-f]+);/gi, (_, number) => String.fromCodePoint(Number.parseInt(number, 16)));

const plainText = value => decodeEntities(String(value || '')
  .replace(/<script\b[\s\S]*?<\/script>/gi, ' ')
  .replace(/<style\b[\s\S]*?<\/style>/gi, ' ')
  .replace(/<[^>]+>/g, ' '))
  .replace(/\s+/g, ' ')
  .trim();

const attribute = (attributes, name) => {
  const match = attributes.match(new RegExp(`(?:^|\\s)${name}\\s*=\\s*(["'])(.*?)\\1`, 'i'));
  return match ? decodeEntities(match[2]) : '';
};

const anchors = html => [...html.matchAll(/<a\b([^>]*)>([\s\S]*?)<\/a>/gi)].map(match => ({
  href: attribute(match[1], 'href'),
  classes: attribute(match[1], 'class'),
  label: plainText(match[2])
}));

const cleanFile = href => {
  const value = String(href || '').split('#')[0].split('?')[0];
  if (!value || /^(?:[a-z]+:|\/)/i.test(value) || value.includes('/')) {
    return '';
  }
  return value.endsWith('.html') ? value : '';
};

if (!fs.existsSync(root) || !fs.statSync(root).isDirectory()) {
  throw new Error(`Study Note directory does not exist: ${root}`);
}

const files = fs.readdirSync(root).filter(file => file.endsWith('.html'));
const notes = [];
for (const file of files) {
  const html = fs.readFileSync(path.join(root, file), 'utf8');
  if (!/class=["'][^"']*\bstudy-note\b/i.test(html)) {
    continue;
  }
  const header = html.match(/<header\b[^>]*class=["'][^"']*\bcomparison-topbar\b[^"']*["'][\s\S]*?<\/header>/i)?.[0] || '';
  const brand = anchors(header).find(link => /(?:^|\s)comparison-brand(?:\s|$)/.test(link.classes));
  const collection = cleanFile(brand?.href);
  const titleMatch = html.match(/<h1\b[^>]*class=["'][^"']*\bstudy-note__title\b[^"']*["'][^>]*>([\s\S]*?)<\/h1>/i)
    || html.match(/<h1\b[^>]*>([\s\S]*?)<\/h1>/i);
  const title = plainText(titleMatch?.[1]);
  if (!collection || !title) {
    throw new Error(`Study Note is missing collection or title metadata: ${file}`);
  }
  notes.push({file, collection, title});
}

const grouped = new Map();
for (const note of notes) {
  if (!grouped.has(note.collection)) {
    grouped.set(note.collection, []);
  }
  grouped.get(note.collection).push(note);
}

const collections = {};
let missingFromIndexes = 0;
for (const collection of [...grouped.keys()].sort()) {
  const members = grouped.get(collection);
  const memberByFile = new Map(members.map(item => [item.file, item]));
  const indexPath = path.join(root, collection);
  const ordered = [];
  const seen = new Set();
  if (fs.existsSync(indexPath)) {
    const indexHtml = fs.readFileSync(indexPath, 'utf8');
    for (const link of anchors(indexHtml)) {
      const linkedFile = cleanFile(link.href);
      if (!memberByFile.has(linkedFile) || seen.has(linkedFile)) {
        continue;
      }
      ordered.push(memberByFile.get(linkedFile));
      seen.add(linkedFile);
    }
  }
  const remaining = members
    .filter(item => !seen.has(item.file))
    .sort((first, second) => first.title.localeCompare(second.title, 'en'));
  missingFromIndexes += remaining.length;
  ordered.push(...remaining);
  collections[collection] = ordered.map(item => [item.file, item.title]);
}

const manifest = {
  version: 1,
  studyNotes: notes.length,
  collections
};
const generated = `window.studyNoteNavigationManifest=${JSON.stringify(manifest)};\n`;

if (check) {
  const existing = fs.existsSync(output) ? fs.readFileSync(output, 'utf8') : '';
  if (existing !== generated) {
    console.error(`Study Note navigation manifest is stale: ${output}`);
    process.exit(1);
  }
} else {
  fs.writeFileSync(output, generated);
}

console.log(JSON.stringify({
  output,
  studyNotes: notes.length,
  collections: Object.keys(collections).length,
  missingFromIndexes
}, null, 2));
