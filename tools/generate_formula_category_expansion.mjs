import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const repositoryRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const source = JSON.parse(readFileSync(resolve(repositoryRoot, 'blocks/myprogress/formulas-study-guide-data.json'), 'utf8'));
const output = resolve(repositoryRoot, 'study-guide/comparisons/formula-category-expansion.json');

const categoryDefinitions = [
  {
    key: 'dispel-summerheat',
    sourceKeys: ['dispel-summerheat'],
    en: 'Dispel Summerheat',
    es: 'Eliminar Calor de Verano',
    focus: 'dispel-summerheat'
  },
  {
    key: 'exterior-interior-excess',
    sourceKeys: ['exterior-interior-excess'],
    en: 'Treat Exterior and Interior Excess',
    es: 'Tratar exceso Exterior e Interior',
    focus: 'exterior-interior-excess'
  },
  {
    key: 'treat-dryness',
    sourceKeys: ['treat-dryness-ch15'],
    en: 'Treat Dryness',
    es: 'Tratar la Sequedad',
    focus: 'treat-dryness-ch15'
  },
  {
    key: 'treat-abscesses-sores',
    sourceKeys: ['treat-abscesses-ch20'],
    en: 'Treat Abscesses and Sores',
    es: 'Tratar abscesos y llagas',
    focus: 'treat-abscesses-chapter-20'
  }
];

const officialFormulaNames = new Set([
  'Liu Yi San',
  'Da Chai Hu Tang',
  'Fang Feng Tong Sheng San',
  'Ge Gen Huang Qin Huang Lian Tang',
  'Mai Men Dong Tang',
  'Qing Zao Jiu Fei Tang',
  'Sang Xing Tang',
  'Sha Shen Mai Men Dong Tang',
  'Xing Su San',
  'Zeng Ye Tang',
  'Da Huang Mu Dan Tang',
  'Wu Wei Xiao Du Yin',
  'Yang He Tang'
]);

const categoryBySourceKey = new Map();
categoryDefinitions.forEach(category => {
  category.sourceKeys.forEach(key => categoryBySourceKey.set(key, category));
});

const profiles = (source.supplementalFormulas || [])
  .filter(formula => officialFormulaNames.has(formula.name))
  .map(formula => ({ ...formula, category: categoryBySourceKey.get(formula.categoryKey) }))
  .filter(formula => formula.category);

if (profiles.length !== officialFormulaNames.size) {
  const found = new Set(profiles.map(formula => formula.name));
  const missing = [...officialFormulaNames].filter(name => !found.has(name));
  throw new Error(`Missing official category-expansion profiles: ${missing.join(', ')}`);
}

const letters = ['A', 'B', 'C', 'D'];
const formulas = profiles.map((profile, index) => {
  const number = 64 + index;
  const params = new URLSearchParams({ focus: profile.category.focus });
  const paramsEs = new URLSearchParams({ focus: profile.category.focus, lang: 'es' });
  return {
    number,
    name: profile.name,
    englishName: profile.englishName,
    categoryKey: profile.category.key,
    categoryEn: profile.category.en,
    categoryEs: profile.category.es,
    questionCount: 4,
    profileUrl: `/blocks/myprogress/formulas-study-guide.php?${params}#formula-${profile.number}`,
    profileUrlEs: `/blocks/myprogress/formulas-study-guide.php?${paramsEs}#formula-${profile.number}`
  };
});
const formulaByName = new Map(formulas.map(formula => [formula.name, formula]));

function optionText(profile) {
  return `${profile.name} (${profile.englishName})`;
}

function sentence(value) {
  return String(value || '').trim().replace(/[.!?]+$/, '');
}

function distractorsFor(profile, formulaIndex) {
  const sameCategory = profiles.filter(candidate =>
    candidate.name !== profile.name && candidate.category.key === profile.category.key
  );
  const allOthers = profiles.filter(candidate => candidate.name !== profile.name);
  const candidates = [...new Map([...sameCategory, ...allOthers].map(candidate => [candidate.name, candidate])).values()];
  const offset = (formulaIndex * 5) % candidates.length;
  return [0, 1, 2].map(step => candidates[(offset + step) % candidates.length]);
}

function questionCopies(profile) {
  return [
    {
      en: `Which formula best matches this pattern? ${profile.pattern} Decisive clue: ${profile.clue}`,
      es: `¿Qué fórmula corresponde mejor a este patrón? ${profile.patternEs} Pista decisiva: ${profile.clueEs}`
    },
    {
      en: `Which formula has this principal action? ${profile.action}`,
      es: `¿Qué fórmula tiene esta acción principal? ${profile.actionEs}`
    },
    {
      en: `Which formula is built around the following chief ingredient${profile.kings.includes(',') ? 's' : ''}? ${profile.kings}`,
      es: `¿Qué fórmula se construye alrededor de ${profile.kings.includes(',') ? 'los siguientes ingredientes principales' : 'el siguiente ingrediente principal'}? ${profile.kings}`
    },
    {
      en: `Which formula is identified by this Board distinction? ${profile.examFocus}`,
      es: `¿Qué fórmula se identifica por esta distinción de Board? ${profile.examFocusEs}`
    }
  ];
}

const questions = [];
profiles.forEach((profile, formulaIndex) => {
  const formula = formulaByName.get(profile.name);
  const distractors = distractorsFor(profile, formulaIndex);
  questionCopies(profile).forEach((prompt, questionIndex) => {
    const correctIndex = (formulaIndex + questionIndex) % 4;
    const choices = [...distractors];
    choices.splice(correctIndex, 0, profile);
    const feedback = [
      `${profile.name} (${profile.englishName}) treats ${sentence(profile.pattern)}.`,
      `Action: ${sentence(profile.action)}.`,
      `Decisive clue: ${sentence(profile.examFocus)}.`,
      `Safety: ${sentence(profile.safety)}.`
    ];
    const feedbackEs = [
      `${profile.name} (${profile.englishName}) trata ${sentence(profile.patternEs)}.`,
      `Acción: ${sentence(profile.actionEs)}.`,
      `Pista decisiva: ${sentence(profile.examFocusEs)}.`,
      `Seguridad: ${sentence(profile.safetyEs)}.`
    ];
    questions.push({
      sourceNumber: `FCAT-${String(questions.length + 1).padStart(3, '0')}`,
      prompt: prompt.en,
      body: [],
      options: choices.map((choice, optionIndex) => ({ label: letters[optionIndex], text: optionText(choice) })),
      correctLabel: letters[correctIndex],
      feedback,
      formulaNumbers: [formula.number],
      categoryKeys: [profile.category.key],
      translationEs: {
        prompt: prompt.es,
        body: [],
        options: choices.map((choice, optionIndex) => ({ label: letters[optionIndex], text: optionText(choice) })),
        feedback: feedbackEs
      }
    });
  });
});

const result = {
  version: 1,
  generatedFrom: 'formulas-study-guide-data.json · 2026 official roster category expansion',
  policy: 'Four source-grounded practice questions per official formula missing from the original 63-formula category set.',
  categories: categoryDefinitions.map(({ key, en, es }) => ({ key, en, es })),
  formulas,
  questions
};

writeFileSync(output, `${JSON.stringify(result, null, 2)}\n`);
console.log(`Wrote ${questions.length} questions for ${formulas.length} formulas across ${result.categories.length} categories.`);
