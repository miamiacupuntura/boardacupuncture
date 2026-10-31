import { writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { mostImportantFormulaBatch1 } from './most_important_formulas_batch_1.mjs';
import { mostImportantFormulaBatch2 } from './most_important_formulas_batch_2.mjs';
import { mostImportantFormulaBatch3 } from './most_important_formulas_batch_3.mjs';
import { mostImportantFormulaBatch4 } from './most_important_formulas_batch_4.mjs';
import { mostImportantFormulaBatch5 } from './most_important_formulas_batch_5.mjs';
import { mostImportantFormulaBatch6 } from './most_important_formulas_batch_6.mjs';
import { mostImportantFormulaBatch7 } from './most_important_formulas_batch_7.mjs';
import { mostImportantFormulaBatch8 } from './most_important_formulas_batch_8.mjs';
import { mostImportantFormulaBatch9 } from './most_important_formulas_batch_9.mjs';
import {
  classicFormulaCategories,
  classicFormulaFallbacks,
  classicFormulaOutline,
  classicFormulaSafetyTiers
} from './classic_formula_navigator_data.mjs';

const repositoryRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const practiceData = JSON.parse(
  await import('node:fs').then(({ readFileSync }) =>
    readFileSync(resolve(repositoryRoot, 'study-guide/comparisons/formula-practice-data.json'), 'utf8'))
);
const practiceCounts = new Map(practiceData.formulas.map(formula => [formula.number, formula.questionCount]));
const expandedCards = [
  ...mostImportantFormulaBatch1, ...mostImportantFormulaBatch2, ...mostImportantFormulaBatch3,
  ...mostImportantFormulaBatch4, ...mostImportantFormulaBatch5, ...mostImportantFormulaBatch6,
  ...mostImportantFormulaBatch7, ...mostImportantFormulaBatch8, ...mostImportantFormulaBatch9
];
const expandedByName = new Map(expandedCards.map(card => [card.name, card]));
const categoryByKey = new Map(classicFormulaCategories.map(([key, en, es]) => [key, { key, en, es }]));

const categories = classicFormulaCategories.map(([key, name, nameEs], index) => ({
  number: index + 1,
  key,
  name,
  nameEs
}));

const formulas = classicFormulaOutline.map(([number, name, categoryKey, kings]) => {
  const content = expandedByName.get(name) || classicFormulaFallbacks[name];
  if (!content) {
    throw new Error(`Missing study-guide content for ${name}`);
  }
  const category = categoryByKey.get(categoryKey);
  return {
    number,
    name,
    categoryKey,
    category: category.en,
    categoryEs: category.es,
    kings,
    pattern: content.pattern,
    patternEs: content.patternEs,
    action: content.action,
    actionEs: content.actionEs,
    clue: content.clue,
    clueEs: content.clueEs,
    ingredients: content.ingredients,
    ingredientsEs: content.ingredientsEs,
    safety: content.safety,
    safetyEs: content.safetyEs,
    safetyTier: classicFormulaSafetyTiers[name] || 'standard',
    practiceCount: practiceCounts.get(number) || 0
  };
});

if (categories.length !== 14 || formulas.length !== 63) {
  throw new Error(`Unexpected formula guide totals: ${categories.length} categories, ${formulas.length} formulas`);
}

const output = { version: 1, categories, formulas };
const outputPath = resolve(repositoryRoot, 'blocks/myprogress/formulas-study-guide-data.json');
writeFileSync(outputPath, `${JSON.stringify(output, null, 2)}\n`);
console.log(`Generated ${outputPath} with ${categories.length} categories and ${formulas.length} formulas.`);
