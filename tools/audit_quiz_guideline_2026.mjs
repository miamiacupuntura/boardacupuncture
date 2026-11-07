import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const readText = relative => fs.readFileSync(path.join(root, relative), 'utf8');
const readJson = relative => JSON.parse(readText(relative));
const appendixNames = relative => readText(relative)
    .split(/\r?\n/)
    .map(value => value.trim())
    .filter(Boolean)
    .map(value => value.replace(/ \([^\n]*\)$/, ''));

const expectedHerbs = new Set(appendixNames('tools/data/ncbahm_2026_appendix_a_herbs.txt'));
const expectedFormulas = new Set(appendixNames('tools/data/ncbahm_2026_appendix_c_formulas.txt'));
const herbRoster = readJson('blocks/myprogress/single-herb-card-index.json');
const herbProfiles = readJson('blocks/myprogress/single-herb-properties.json');
const formulaRoster = readJson('blocks/myprogress/formula-board-roster-2026.json');
const base = readJson('study-guide/comparisons/formula-practice-data.json');
const manifest = readJson('study-guide/comparisons/formula-modifications-manifest.json');
const supplements = manifest.files.map(filename => readJson(`study-guide/comparisons/${filename}`));
const recordedInventory = readJson('tools/data/quiz-guideline-2026-inventory.json');

const aliases = new Map([
    ['Ge Gen Huang Lian Huang Qin Tang', 'Ge Gen Huang Qin Huang Lian Tang'],
    ['Jin Gui Shen Qi Wan', 'Shen Qi Wan'],
    ['Qing Wen Bai Du San', 'Qing Wen Bai Du Yin'],
    ['Tai Shan Pan Shi San', 'Taishan Pan Shi San'],
]);
const formulaNamePattern = /\b(?:[A-Z][A-Za-zÀ-ÿ¡]*\s+){2,7}(?:Tang|San|Wan|Yin|Dan|Jian|Fang)\b(?!\s+[A-Z][a-z])/g;
const instructionPrefix = /^(?:(?:Add|Combine|Replace|Increase|Decrease|Remove|Both|Bath|To)\s+)+/;
const canonical = name => aliases.get(name) ?? name;
const difference = (left, right) => [...left].filter(value => !right.has(value)).sort();

if (expectedHerbs.size !== 304 || herbRoster.total !== 304 || herbRoster.herbs.length !== 304) {
    throw new Error('Appendix A and the application herb roster must contain exactly 304 herbs.');
}
if (expectedFormulas.size !== 181 || formulaRoster.total !== 181 || formulaRoster.formulas.length !== 181) {
    throw new Error('Appendix C and the application formula roster must contain exactly 181 formulas.');
}

const rosterHerbs = new Set(herbRoster.herbs.map(herb => herb.name));
const allProfileHerbs = new Set(herbProfiles.herbs.map(herb => herb.name));
const profileHerbs = new Set(herbProfiles.herbs
    .filter(herb => herb.status === 'verified')
    .map(herb => herb.name));
const rosterFormulas = new Set(formulaRoster.formulas.map(formula => formula.name));
for (const [label, values] of [
    ['herb roster vs. Appendix A', difference(rosterHerbs, expectedHerbs)],
    ['Appendix A vs. herb roster', difference(expectedHerbs, rosterHerbs)],
    ['all herb profiles vs. Appendix A', difference(allProfileHerbs, expectedHerbs)],
    ['Appendix A vs. all herb profiles', difference(expectedHerbs, allProfileHerbs)],
    ['formula roster vs. Appendix C', difference(rosterFormulas, expectedFormulas)],
    ['Appendix C vs. formula roster', difference(expectedFormulas, rosterFormulas)],
]) {
    if (values.length) throw new Error(`${label}: ${values.join(', ')}`);
}

const formulas = [];
const questions = [];
const formulaNumbers = new Set();
const questionNumbers = new Set();
for (const data of [base, ...supplements]) {
    for (const formula of data.formulas || []) {
        const number = Number(formula.number);
        if (!number || formulaNumbers.has(number)) continue;
        formulaNumbers.add(number);
        formulas.push(formula);
    }
    for (const question of data.questions || []) {
        const sourceNumber = String(question.sourceNumber);
        if (!sourceNumber || questionNumbers.has(sourceNumber)) continue;
        questionNumbers.add(sourceNumber);
        questions.push(question);
    }
}

const offRosterFormulas = formulas.filter(formula => !expectedFormulas.has(canonical(formula.name)));
if (offRosterFormulas.length) {
    throw new Error(`Formula selectors outside Appendix C: ${offRosterFormulas.map(item => item.name).join(', ')}`);
}
const allowedNumbers = new Set(formulas.map(formula => Number(formula.number)));
const outsideNames = new Map();
const retainedQuestions = questions.filter(question => {
    const associationsAreOfficial = question.formulaNumbers?.length
        && question.formulaNumbers.every(number => allowedNumbers.has(Number(number)));
    if (!associationsAreOfficial) return false;
    const strings = [
        question.prompt,
        ...(question.body || []),
        ...(question.options || []).map(option => option.text),
        ...(question.feedback || []),
    ];
    const names = strings.flatMap(value => String(value || '').match(formulaNamePattern) || [])
        .map(name => name.replace(instructionPrefix, '').trim())
        .filter(name => name.split(/\s+/).length >= 3);
    const outside = names.filter(name => !expectedFormulas.has(canonical(name)));
    for (const name of outside) outsideNames.set(name, (outsideNames.get(name) || 0) + 1);
    return outside.length === 0;
});

const inventory = {
    guideline2026: { herbs: expectedHerbs.size, formulas: expectedFormulas.size },
    singleHerbsQuiz: {
        activeProfiles: profileHerbs.size,
        inactiveIncompleteProfiles: expectedHerbs.size - profileHerbs.size,
        outsideGuideline: difference(profileHerbs, expectedHerbs).length,
    },
    formulaQuiz: {
        selectableFormulas: formulas.length,
        sourceQuestions: questions.length,
        retainedQuestions: retainedQuestions.length,
        removedQuestions: questions.length - retainedQuestions.length,
        normalizedAliases: Object.fromEntries([...aliases].filter(([alias]) => formulas.some(item => item.name === alias))),
    },
};
if (inventory.singleHerbsQuiz.activeProfiles !== 194
        || inventory.formulaQuiz.selectableFormulas !== 76
        || inventory.formulaQuiz.sourceQuestions !== 929
        || inventory.formulaQuiz.retainedQuestions !== 851) {
    throw new Error(`Unexpected Quiz inventory: ${JSON.stringify(inventory)}`);
}
const recordedSummary = {
    guideline2026: {
        herbs: recordedInventory.guideline.herbs.total,
        formulas: recordedInventory.guideline.formulas.total,
    },
    singleHerbsQuiz: recordedInventory.singleHerbsQuiz,
    formulaQuiz: {
        selectableFormulas: recordedInventory.formulaQuiz.selectableFormulas,
        sourceQuestions: recordedInventory.formulaQuiz.sourceQuestions,
        retainedQuestions: recordedInventory.formulaQuiz.retainedQuestions,
        removedQuestions: recordedInventory.formulaQuiz.removedQuestions,
        normalizedAliases: recordedInventory.formulaQuiz.normalizedAliases,
    },
};
if (JSON.stringify(recordedSummary) !== JSON.stringify(inventory)) {
    throw new Error('The recorded Quiz inventory is stale.');
}
const excludedNames = [...outsideNames.keys()].sort();
if (JSON.stringify(recordedInventory.formulaQuiz.excludedFormulaNames) !== JSON.stringify(excludedNames)) {
    throw new Error('The recorded list of excluded formula names is stale.');
}

console.log(JSON.stringify(inventory, null, 2));
console.log(`Excluded formula names (${outsideNames.size}): ${excludedNames.join(', ')}`);
