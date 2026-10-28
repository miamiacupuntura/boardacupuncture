#!/usr/bin/env node
import { readFileSync } from 'node:fs';
import { createHash } from 'node:crypto';
import { resolve } from 'node:path';

const input = resolve(process.argv[2] || 'study-guide/comparisons/herb-comparison-bank-data.json');
const sourceBytes = readFileSync(input);
const data = JSON.parse(sourceBytes);
const fail = message => { throw new Error(`Herbal comparison bank: ${message}`); };

if (data.version !== 1) fail(`unsupported version ${data.version}`);
if (data.chapters?.length !== 17) fail(`expected 17 chapters, found ${data.chapters?.length}`);
if (data.questions?.length !== 249) fail(`expected 249 questions, found ${data.questions?.length}`);
if (data.charts?.length !== 117) fail(`expected 117 charts, found ${data.charts?.length}`);
if (data.totals?.chapters !== 17 || data.totals?.questions !== 249 || data.totals?.charts !== 117) fail('declared totals do not match');

const questionIds = new Set();
for (const chapter of data.chapters) {
  if (chapter.number < 1 || chapter.number > 17) fail(`invalid chapter number ${chapter.number}`);
  if (!chapter.title || !chapter.titleEs) fail(`chapter ${chapter.number} has no bilingual title`);
  const questions = data.questions.filter(question => question.chapter === chapter.number);
  const expected = Array.from({ length: questions.length }, (_, index) => index + 1);
  if (questions.map(question => question.number).join(',') !== expected.join(',')) fail(`chapter ${chapter.number} question order is not continuous`);
  if (questions.length !== chapter.questionCount) fail(`chapter ${chapter.number} question total differs`);
  const charts = data.charts.filter(chart => chart.chapter === chapter.number);
  if (charts.length !== chapter.chartCount) fail(`chapter ${chapter.number} chart total differs`);
  for (const question of questions) {
    if (question.id !== `${question.chapter}-${question.number}`) fail(`invalid question id ${question.id}`);
    if (questionIds.has(question.id)) fail(`duplicate question id ${question.id}`);
    questionIds.add(question.id);
    if (!question.question?.trim() || !question.answer?.trim() || !question.memoryCue?.trim()) fail(`question ${question.id} has incomplete content`);
    if (/\bp a r t\b|C h a p t e r|\n\nReferences(?:\n|$)/.test(question.answer)) fail(`question ${question.id} contains a running header or reference section`);
    if (!Number.isInteger(question.pdfPage) || question.pdfPage < 1) fail(`question ${question.id} has invalid source page`);
  }
}

const figures = new Set();
const temperatures = new Set(['very hot', 'warm', 'slightly warm', 'neutral', 'slightly cold', 'cold', 'very cold']);
for (const chart of data.charts) {
  if (!/^\d+\.\d+$/.test(chart.figure)) fail(`invalid figure id ${chart.figure}`);
  if (figures.has(chart.figure)) fail(`duplicate figure ${chart.figure}`);
  figures.add(chart.figure);
  if (!chart.title?.trim() || !chart.bars?.length) fail(`figure ${chart.figure} has incomplete content`);
  if (Math.max(...chart.bars.map(bar => bar.value)) !== 100) fail(`figure ${chart.figure} is not normalized to 100`);
  for (const bar of chart.bars) {
    if (!bar.herb?.trim()) fail(`figure ${chart.figure} contains an unnamed herb`);
    if (!Number.isInteger(bar.value) || bar.value < 0 || bar.value > 100) fail(`figure ${chart.figure} has invalid strength ${bar.value}`);
    if (!/^#[0-9a-f]{6}$/i.test(bar.color)) fail(`figure ${chart.figure} has invalid color ${bar.color}`);
    if (!temperatures.has(bar.temperature)) fail(`figure ${chart.figure} has invalid temperature ${bar.temperature}`);
  }
}

if (process.argv[3]) {
  const translation = JSON.parse(readFileSync(resolve(process.argv[3]), 'utf8'));
  const expectedHash = createHash('sha256').update(sourceBytes).digest('hex');
  if (translation.sourceDataSha256 !== expectedHash) fail('Spanish translation was generated from a different source revision');
  if (translation.questions?.length !== data.questions.length) fail('Spanish question total differs from the source');
  if (translation.charts?.length !== data.charts.length) fail('Spanish chart total differs from the source');
  const translatedIds = new Set(translation.questions.map(question => question.id));
  const translatedFigures = new Set(translation.charts.map(chart => chart.figure));
  for (const question of data.questions) {
    if (!translatedIds.has(question.id)) fail(`Spanish translation is missing question ${question.id}`);
  }
  for (const chart of data.charts) {
    if (!translatedFigures.has(chart.figure)) fail(`Spanish translation is missing figure ${chart.figure}`);
  }
}

console.log(`Validated herbal comparison bank: ${data.chapters.length} chapters, ${data.questions.length} questions, ${data.charts.length} charts.`);
