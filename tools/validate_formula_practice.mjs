#!/usr/bin/env node

import { createHash } from 'node:crypto';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';

const path = resolve(process.argv[2] || 'study-guide/comparisons/formula-practice-data.json');
const raw = readFileSync(path, 'utf8');
const data = JSON.parse(raw);

function requireValue(condition, message) {
  if (!condition) throw new Error(message);
}

requireValue(data.totals?.questions === 572, 'Expected 572 core-formula questions');
requireValue(data.totals?.matchedQuestions === 572, 'Every question must be linked to a core formula');
requireValue(data.totals?.generalQuestions === 0, 'General or supplemental-formula questions must be excluded');
requireValue(data.formulas?.length === 63, 'Expected 63 formulas');
requireValue(data.categories?.length === 14, 'Expected 14 categories');
requireValue(data.questions?.length === 572, 'Question array length does not match totals');
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
  requireValue(
    !question.prompt.startsWith('IDENTIFICATION'),
    `Question ${question.sourceNumber} still has an unnatural identification heading`
  );
  requireValue(
    !/\bWhich of the following (?:herbal )?formula\b/i.test(question.prompt),
    `Uncorrected singular formula wording in question ${question.sourceNumber}`
  );
  requireValue(
    !/,\s+what (?:is|are) (?:their|the) differences?\?/i.test(question.prompt),
    `Comma splice remains in question ${question.sourceNumber}`
  );
}
requireValue(data.formulas.every(formula => formula.questionCount > 0), 'Every formula must have related questions');
const translationPath = resolve(dirname(path), 'formula-practice-translations-es.json');
const translationData = JSON.parse(readFileSync(translationPath, 'utf8'));
requireValue(translationData.automatic === true, 'Spanish layer must identify itself as automatic translation');
requireValue(translationData.questions?.length === 572, 'Expected 572 Spanish translations');
requireValue(
  translationData.sourceDataSha256 === createHash('sha256').update(raw).digest('hex'),
  'Spanish translation layer does not match the current English bank'
);
requireValue(
  translationData.disclaimerEs?.includes('Puede contener errores'),
  'Spanish translation disclaimer is missing'
);
const translatedByNumber = new Map(
  translationData.questions.map(question => [question.sourceNumber, question])
);
const englishResidue = /\b(?:which|what|both|the|and|with|due|treats?|atrophy|marked|patterns?|include|except|chief|deputy|assistant|envoy|tongue|pulse|heat|cold|blood|dampness|phlegm|wiry|slippery|floating|tight|thin|white|yellow|red)\b/i;
let changedPrompts = 0;
let changedOptions = 0;
let translatedOptionCount = 0;
for (const question of data.questions) {
  const translated = translatedByNumber.get(question.sourceNumber);
  requireValue(translated, `Missing Spanish translation for question ${question.sourceNumber}`);
  requireValue(!Object.hasOwn(translated, 'correctLabel'), `Spanish layer must not duplicate the answer key for question ${question.sourceNumber}`);
  requireValue(translated.prompt?.trim(), `Empty Spanish prompt for question ${question.sourceNumber}`);
  requireValue(
    translated.body?.length === question.body.length && translated.body.every(value => value?.trim()),
    `Spanish body mismatch for question ${question.sourceNumber}`
  );
  requireValue(
    translated.options?.length === question.options.length,
    `Spanish option count mismatch for question ${question.sourceNumber}`
  );
  requireValue(
    translated.options.every((option, index) => option.label === question.options[index].label && option.text?.trim()),
    `Spanish options do not preserve labels for question ${question.sourceNumber}`
  );
  requireValue(
    !englishResidue.test(translated.prompt),
    `Spanish prompt contains untranslated English in question ${question.sourceNumber}`
  );
  requireValue(
    translated.options.every(option => !englishResidue.test(option.text)),
    `Spanish option contains untranslated English in question ${question.sourceNumber}`
  );
  requireValue(
    ![translated.prompt, ...translated.options.map(option => option.text)].some(value => /&(?:quot|amp);/.test(value)),
    `Spanish student-facing copy contains an encoded entity in question ${question.sourceNumber}`
  );
  requireValue(
    translated.feedback?.length === question.feedback.length && translated.feedback.every(value => value?.trim()),
    `Spanish feedback mismatch for question ${question.sourceNumber}`
  );
  if (translated.prompt !== question.prompt) changedPrompts += 1;
  translatedOptionCount += translated.options.length;
  changedOptions += translated.options.filter((option, index) => option.text !== question.options[index].text).length;

  const sourceSegments = [question.prompt, ...question.body, ...question.options.map(option => option.text), ...question.feedback];
  const translatedSegments = [translated.prompt, ...translated.body, ...translated.options.map(option => option.text), ...translated.feedback];
  for (let index = 0; index < sourceSegments.length; index += 1) {
    for (const formula of data.formulas) {
      if (sourceSegments[index].toLocaleLowerCase('en').includes(formula.name.toLocaleLowerCase('en'))) {
        requireValue(
          translatedSegments[index].includes(formula.name),
          `Spanish text changed formula name ${formula.name} in question ${question.sourceNumber}`
        );
      }
    }
    if (/spermatorrh(?:ea|oea)/i.test(sourceSegments[index])) {
      requireValue(
        /espermatorrea/i.test(translatedSegments[index]),
        `Spanish text mistranslated spermatorrhea in question ${question.sourceNumber}`
      );
    }
  }
}
requireValue(changedPrompts >= data.questions.length * 0.95, 'Too few prompts were translated into Spanish');
requireValue(changedOptions >= translatedOptionCount * 0.9, 'Too few answer options were translated into Spanish');
requireValue(
  !/ZX(?:FORMULA|TERM|SEG)|P\d{4}P/.test(JSON.stringify(translationData.questions)),
  'Spanish translation contains an unresolved protection token'
);
console.log(`Validated ${data.questions.length} practice questions and ${translationData.questions.length} Spanish translations`);
