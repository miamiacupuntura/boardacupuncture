import { existsSync, readFileSync, writeFileSync } from 'node:fs';
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
import {
  formulaStudyGuideSupplements,
  releaseExteriorCategoryGuide
} from './formula_study_guide_supplements.mjs';
import {
  summerheatCategory,
  summerheatCategoryGuide,
  summerheatFormulas
} from './summerheat_formula_supplement.mjs';
import {
  warmInteriorColdCategory,
  warmInteriorColdCategoryGuide,
  warmInteriorColdFormulas
} from './warm_interior_cold_formula_supplement.mjs';

const repositoryRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const outputPath = resolve(repositoryRoot, 'blocks/myprogress/formulas-study-guide-data.json');
const existingData = existsSync(outputPath)
  ? JSON.parse(readFileSync(outputPath, 'utf8'))
  : { categoryGuides: {}, formulas: [] };
const existingFormulaByName = new Map((existingData.formulas || []).map(formula => [formula.name, formula]));
const practiceData = JSON.parse(
  readFileSync(resolve(repositoryRoot, 'study-guide/comparisons/formula-practice-data.json'), 'utf8')
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

const generatedFormulas = classicFormulaOutline.map(([number, name, categoryKey, kings]) => {
  const content = expandedByName.get(name) || classicFormulaFallbacks[name];
  if (!content) {
    throw new Error(`Missing study-guide content for ${name}`);
  }
  const category = categoryByKey.get(categoryKey);
  const supplement = formulaStudyGuideSupplements[name] || {};
  const existing = existingFormulaByName.get(name) || {};
  return {
    number,
    name,
    categoryKey,
    category: category.en,
    categoryEs: category.es,
    kings,
    pattern: supplement.pattern || content.pattern,
    patternEs: supplement.patternEs || content.patternEs,
    action: supplement.action || content.action,
    actionEs: supplement.actionEs || content.actionEs,
    clue: supplement.clue || content.clue,
    clueEs: supplement.clueEs || content.clueEs,
    ingredients: supplement.ingredients || content.ingredients,
    ingredientsEs: supplement.ingredientsEs || content.ingredientsEs,
    safety: supplement.safety || content.safety,
    safetyEs: supplement.safetyEs || content.safetyEs,
    safetyTier: classicFormulaSafetyTiers[name] || 'standard',
    practiceCount: practiceCounts.get(number) || 0,
    ...(supplement.priority ? { priority: supplement.priority } : {}),
    ...(supplement.examFocus ? { examFocus: supplement.examFocus, examFocusEs: supplement.examFocusEs } : {}),
    ...(supplement.ingredientRoles ? { ingredientRoles: supplement.ingredientRoles, ingredientRolesEs: supplement.ingredientRolesEs } : {}),
    ...(supplement.comparison ? { comparison: supplement.comparison, comparisonEs: supplement.comparisonEs } : {}),
    ...(existing.modificationPreview ? {
      modificationPreview: existing.modificationPreview,
      modificationPreviewEs: existing.modificationPreviewEs
    } : {})
  };
});

// Some category deep-dives are curated directly in the canonical JSON after the
// base roster is generated. Keep those reviewed fields intact on subsequent
// runs while still refreshing practice totals and adding new supplements.
const formulas = generatedFormulas.map(formula => {
  const existing = existingFormulaByName.get(formula.name);
  return existing
    ? {...existing, practiceCount: practiceCounts.get(formula.number) || 0}
    : formula;
});

if (categories.length !== 14 || formulas.length !== 63) {
  throw new Error(`Unexpected formula guide totals: ${categories.length} categories, ${formulas.length} formulas`);
}

const output = {
  version: 3,
  categories,
  categoryGuides: {
    ...(existingData.categoryGuides || {}),
    'release-exterior': existingData.categoryGuides?.['release-exterior'] || releaseExteriorCategoryGuide,
    'dispel-summerheat': summerheatCategoryGuide,
    'warm-interior-cold': warmInteriorColdCategoryGuide
  },
  formulas,
  supplementalCategories: [summerheatCategory, warmInteriorColdCategory],
  supplementalFormulas: [...summerheatFormulas, ...warmInteriorColdFormulas]
};
writeFileSync(outputPath, `${JSON.stringify(output, null, 2)}\n`);
console.log(`Generated ${outputPath} with ${categories.length} categories and ${formulas.length} formulas.`);
console.log(`Added ${summerheatFormulas.length} supplemental formulas from Chapter ${summerheatCategory.sourceChapter}.`);
console.log(`Added ${warmInteriorColdFormulas.length} supplemental formulas from Chapter ${warmInteriorColdCategory.sourceChapter}.`);
