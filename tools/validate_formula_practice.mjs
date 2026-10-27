#!/usr/bin/env node

import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const path = resolve(process.argv[2] || 'study-guide/comparisons/formula-practice-data.json');
const raw = readFileSync(path, 'utf8');
const data = JSON.parse(raw);

function requireValue(condition, message) {
  if (!condition) throw new Error(message);
}

requireValue(data.totals?.questions === 897, 'Expected 897 safe questions');
requireValue(data.totals?.matchedQuestions === 725, 'Expected 725 formula-linked questions');
requireValue(data.totals?.generalQuestions === 172, 'Expected 172 general questions');
requireValue(data.formulas?.length === 63, 'Expected 63 formulas');
requireValue(data.categories?.length === 14, 'Expected 14 categories');
requireValue(data.questions?.length === 897, 'Question array length does not match totals');
requireValue(!raw.includes('+++++'), 'A source answer marker leaked into practice data');
requireValue(!raw.includes('&amp;'), 'An encoded ampersand leaked into practice data');

const sourceNumbers = new Set();
for (const question of data.questions) {
  requireValue(!sourceNumbers.has(question.sourceNumber), `Duplicate source question ${question.sourceNumber}`);
  sourceNumbers.add(question.sourceNumber);
  requireValue(question.options?.length >= 2, `Question ${question.sourceNumber} has fewer than two options`);
  requireValue(
    question.options.some(option => option.label === question.correctLabel),
    `Question ${question.sourceNumber} has no matching correct option`
  );
  requireValue(
    question.formulaNumbers.every(number => Number.isInteger(number) && number >= 1 && number <= 63),
    `Question ${question.sourceNumber} has an invalid formula association`
  );
}
requireValue(data.formulas.every(formula => formula.questionCount > 0), 'Every formula must have related questions');

console.log(`Validated ${data.questions.length} practice questions across ${data.formulas.length} formulas`);
