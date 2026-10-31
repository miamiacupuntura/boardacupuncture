import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const comparisons = path.join(root, 'study-guide', 'comparisons');
const guidePath = path.join(root, 'blocks', 'myprogress', 'formulas-study-guide-data.json');
const practicePath = path.join(comparisons, 'formula-practice-data.json');
const manifestPath = path.join(comparisons, 'formula-modifications-manifest.json');

const guide = JSON.parse(fs.readFileSync(guidePath, 'utf8'));
const practice = JSON.parse(fs.readFileSync(practicePath, 'utf8'));
const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
const questions = manifest.files.flatMap(filename => {
  const supplement = JSON.parse(fs.readFileSync(path.join(comparisons, filename), 'utf8'));
  return supplement.questions || [];
});

const formulaByNumber = new Map(practice.formulas.map(formula => [formula.number, formula]));
const firstQuestionByFormula = new Map();
questions.forEach(question => {
  const number = question.formulaNumbers?.[0];
  if (number && !firstQuestionByFormula.has(number)) firstQuestionByFormula.set(number, question);
});

function clinicalTrigger(prompt, formulaName, spanish) {
  const clean = String(prompt || '').replace(/[¿?]+$/g, '').trim();
  const markers = spanish ? [' para ', ' cuando ', ' en casos de ', ' si '] : [' for ', ' when ', ' in cases of ', ' if '];
  const lower = clean.toLowerCase();
  for (const marker of markers) {
    const index = lower.indexOf(marker);
    if (index !== -1) {
      const value = clean.slice(index + marker.length).trim();
      return value.charAt(0).toUpperCase() + value.slice(1);
    }
  }
  const escapedName = formulaName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  return clean
    .replace(new RegExp(escapedName, 'gi'), '')
    .replace(/^(how|which|what|cómo|qué|cuál)\b[^:]*?:?\s*/i, '')
    .replace(/\s+/g, ' ')
    .trim();
}

function correctOption(question, copy) {
  return (copy.options || []).find(option => option.label === question.correctLabel)?.text || '';
}

function sourceFrom(copy, spanish) {
  const feedback = String(copy.feedback?.[0] || '');
  const marker = spanish ? /\s+Fuente:\s*(.+)$/i : /\s+Source:\s*(.+)$/i;
  return feedback.match(marker)?.[1] || '';
}

let previewCount = 0;
guide.formulas.forEach(formula => {
  delete formula.modificationPreview;
  delete formula.modificationPreviewEs;
  const question = firstQuestionByFormula.get(formula.number);
  const practiceFormula = formulaByNumber.get(formula.number);
  if (!question || !practiceFormula) return;
  const spanishCopy = question.translationEs || question;
  formula.modificationPreview = {
    trigger: clinicalTrigger(question.prompt, practiceFormula.name, false),
    change: correctOption(question, question),
    source: sourceFrom(question, false)
  };
  formula.modificationPreviewEs = {
    trigger: clinicalTrigger(spanishCopy.prompt, practiceFormula.name, true),
    change: correctOption(question, spanishCopy),
    source: sourceFrom(spanishCopy, true)
  };
  previewCount++;
});

if (questions.length !== 305 || previewCount !== 54) {
  throw new Error(`Unexpected modification coverage: ${questions.length} questions, ${previewCount} formula previews`);
}

fs.writeFileSync(guidePath, `${JSON.stringify(guide, null, 2)}\n`);
process.stdout.write(`Added representative modification previews to ${previewCount} formulas.\n`);
