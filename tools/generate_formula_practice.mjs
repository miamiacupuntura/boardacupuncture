#!/usr/bin/env node

import { createHash } from 'node:crypto';
import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import {
  classicFormulaCategories,
  classicFormulaOutline
} from './classic_formula_navigator_data.mjs';

const repositoryRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const defaultSource = resolve(repositoryRoot, 'output/complete-final-import/keyed-questions.jsonl');
const defaultOutput = resolve(repositoryRoot, 'study-guide/comparisons/formula-practice-data.json');
const copyOverrides = JSON.parse(readFileSync(
  resolve(repositoryRoot, 'tools/formula_practice_copy_overrides.json'),
  'utf8'
));

function parseArguments(argv) {
  const options = { source: defaultSource, output: defaultOutput };
  for (let index = 0; index < argv.length; index += 1) {
    const argument = argv[index];
    if (argument === '--source') options.source = resolve(argv[++index]);
    else if (argument === '--output') options.output = resolve(argv[++index]);
    else if (argument === '--help') {
      console.log('Usage: generate_formula_practice.mjs [--source keyed-questions.jsonl] [--output formula-practice-data.json]');
      process.exit(0);
    } else {
      throw new Error(`Unknown argument: ${argument}`);
    }
  }
  return options;
}

function compact(value) {
  return String(value || '').toLowerCase().normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}

function decodeSourceEntities(value) {
  let decoded = String(value || '');
  for (let pass = 0; pass < 3; pass += 1) {
    const next = decoded
      .replaceAll('&amp;', '&')
      .replaceAll('&lt;', '<')
      .replaceAll('&gt;', '>')
      .replaceAll('&quot;', '"')
      .replaceAll('&#39;', "'");
    if (next === decoded) break;
    decoded = next;
  }
  return decoded;
}

function normalizeEnglishCopy(value, prompt = false) {
  let normalized = decodeSourceEntities(value)
    .replace(/\r\n?/g, '\n')
    .split('\n')
    .map(line => line.replace(/[ \t]+/g, ' ').trim())
    .join('\n')
    .replace(/ +([,.;:!?])/g, '$1')
    .replace(/\.\s+and\b/gi, ', and')
    .replace(/\bwhaite\b/gi, 'white')
    .replace(/\bsuitable far the case\b/gi, 'suitable for the case')
    .replace(/\bFu Ung Tang\b/g, 'Fu Ling Tang')
    .replace(/\bWhat is difference\?/gi, 'What is the difference?')
    .replace(/\bWhat are ingredients\b/gi, 'What are the ingredients')
    .replace(/\bWhat are manifestations\b/gi, 'What are the manifestations')
    .replace(/\bClinical Manifestations:/g, 'Clinical manifestations:')
    .replace(/\bTreatment Principle:/g, 'Treatment principle:')
    .replace(/\bFloating-Rapid\b/gi, 'floating and rapid')
    .replace(/\bFloating-Tight\b/gi, 'floating and tight')
    .replace(/\bDeep-Weak-Slow\b/gi, 'deep, weak, and slow')
    .replace(/\bFrequent-pale-abundant urination\b/gi, 'frequent, pale, abundant urination')
    .replace(/\bThin-white coating\b/gi, 'thin, white coating')
    .trim();
  if (!prompt) return normalized;
  normalized = normalized
    .replace(/\bWhich of the following herbal formula\b/gi, 'Which of the following herbal formulas')
    .replace(/\bWhich of the following formula\b/gi, 'Which of the following formulas')
    .replace(/(Which of the following (?:herbal )?formulas) treat\b/gi, '$1 treats')
    .replace(/(Which of the following (?:herbal )?formulas) uses for\b/gi, '$1 is used for')
    .replace(/(Which of the following herbal formulas) for\b/gi, '$1 is appropriate for')
    .replace(/,\s+what ((?:is|are) (?:their|the) differences?\?)/gi, '. What $1');
  return normalized;
}

const naturalIdentificationHeadings = new Map([
  ['IDENTIFICATION OF HERBAL FORMULA ACCORDING ZANG FU PATTERNS', 'Which herbal formula corresponds to the Zang Fu pattern described below?'],
  ['IDENTIFICATION OF THE FORMULA ACCORDING TO THE GENERAL DESCRIPTION', 'Which formula corresponds to the general description below?'],
  ['IDENTIFICATION OF HERBAL FORMULA ACCORDING TO THE FOUR LEVELS', 'Which herbal formula corresponds to the Four Levels pattern described below?'],
  ['IDENTIFICATION OF THE FORMULA ACCORDING TO THE MODIFICATION', 'Which formula corresponds to the modification described below?'],
  ['IDENTIFICATION OF THE FORMULA MODIFICATION ACCORDING TO THE SYMPTOMS', 'Which formula modification corresponds to the symptoms described below?']
]);

function naturalizeIdentificationPrompt(value) {
  const prompt = decodeSourceEntities(value);
  const newline = prompt.indexOf('\n');
  const heading = (newline === -1 ? prompt : prompt.slice(0, newline)).trim().toUpperCase();
  const replacement = naturalIdentificationHeadings.get(heading);
  if (!replacement) return prompt;
  const remainder = newline === -1 ? '' : prompt.slice(newline + 1).trim();
  return remainder ? `${replacement}\n\n${remainder}` : replacement;
}

function readJsonLines(path) {
  if (!existsSync(path)) throw new Error(`Source question file does not exist: ${path}`);
  return readFileSync(path, 'utf8').split(/\r?\n/).filter(Boolean).map((line, index) => {
    try {
      return JSON.parse(line);
    } catch (error) {
      throw new Error(`Invalid JSON on source line ${index + 1}: ${error.message}`);
    }
  });
}

function sourceAudit(sourcePath) {
  const auditPath = resolve(dirname(sourcePath), 'audit.json');
  if (!existsSync(auditPath)) return null;
  const audit = JSON.parse(readFileSync(auditPath, 'utf8'));
  return {
    filename: audit.source?.filename || null,
    sha256: audit.source?.sha256 || null
  };
}

const args = parseArguments(process.argv.slice(2));
const categoryMap = new Map(
  classicFormulaCategories.map(([key, en, es]) => [key, { key, en, es }])
);
const formulas = classicFormulaOutline.map(([number, name, categoryKey]) => ({
  number,
  name,
  categoryKey,
  categoryEn: categoryMap.get(categoryKey)?.en || categoryKey,
  categoryEs: categoryMap.get(categoryKey)?.es || categoryKey,
  slug: `formula-navigator-${String(number).padStart(2, '0')}-${compact(name).replaceAll(' ', '-')}`,
  compactName: compact(name),
  questionCount: 0
}));

const sourceQuestions = readJsonLines(args.source);
const seenNumbers = new Set();
const questions = sourceQuestions.map(question => {
  if (question.classification !== 'keyed_multiple_choice') {
    throw new Error(`Question ${question.source_number} is not explicitly keyed multiple choice`);
  }
  if (seenNumbers.has(question.source_number)) {
    throw new Error(`Duplicate source question number: ${question.source_number}`);
  }
  seenNumbers.add(question.source_number);
  if (!Array.isArray(question.options) || question.options.length < 2) {
    throw new Error(`Question ${question.source_number} has fewer than two options`);
  }
  if (!Array.isArray(question.correct_labels) || question.correct_labels.length !== 1) {
    throw new Error(`Question ${question.source_number} does not have exactly one explicit answer`);
  }
  const correctLabel = question.correct_labels[0];
  if (!question.options.some(option => option.label === correctLabel)) {
    throw new Error(`Question ${question.source_number} has a missing keyed option`);
  }

  const override = copyOverrides[String(question.source_number)] || {};
  const prompt = normalizeEnglishCopy(
    override.promptEn || naturalizeIdentificationPrompt(question.prompt),
    true
  );
  const body = (question.body || []).map(value => normalizeEnglishCopy(value));
  const options = question.options.map(option => ({
    label: option.label,
    text: normalizeEnglishCopy(option.text)
  }));
  const feedback = (question.feedback || []).map(value => normalizeEnglishCopy(value));
  const searchable = compact([
    prompt,
    ...body,
    ...options.map(option => option.text),
    ...feedback
  ].join(' '));
  const matches = formulas.filter(formula => searchable.includes(formula.compactName));
  matches.forEach(formula => { formula.questionCount += 1; });

  return {
    sourceNumber: question.source_number,
    prompt,
    body,
    options,
    correctLabel,
    feedback,
    formulaNumbers: matches.map(formula => formula.number),
    categoryKeys: [...new Set(matches.map(formula => formula.categoryKey))]
  };
});

if (questions.some(question => JSON.stringify(question).includes('+++++'))) {
  throw new Error('An answer marker leaked into the student practice data');
}
if (formulas.some(formula => formula.questionCount === 0)) {
  const missing = formulas.filter(formula => formula.questionCount === 0).map(formula => formula.name);
  throw new Error(`No related questions found for: ${missing.join(', ')}`);
}

const sourceBytes = readFileSync(args.source);
const matchedQuestionCount = questions.filter(question => question.formulaNumbers.length > 0).length;
const comparisonQuestionCount = questions.filter(question => question.formulaNumbers.length > 1).length;
const data = {
  version: 1,
  generatedFrom: {
    keyedJsonlSha256: createHash('sha256').update(sourceBytes).digest('hex'),
    document: sourceAudit(args.source)
  },
  policy: 'Only questions with one explicit source answer marker are included. Formula links are mention-based and may include comparison distractors.',
  totals: {
    questions: questions.length,
    matchedQuestions: matchedQuestionCount,
    generalQuestions: questions.length - matchedQuestionCount,
    comparisonQuestions: comparisonQuestionCount,
    formulas: formulas.length,
    categories: categoryMap.size
  },
  categories: [...categoryMap.values()],
  formulas: formulas.map(({ compactName, ...formula }) => formula),
  questions
};

writeFileSync(args.output, `${JSON.stringify(data)}\n`, 'utf8');
console.log(`Generated ${args.output}`);
console.log(`${questions.length} safe questions: ${matchedQuestionCount} formula-linked, ${questions.length - matchedQuestionCount} general, ${comparisonQuestionCount} comparative`);
