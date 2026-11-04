import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = relative => fs.readFileSync(path.join(root, relative), 'utf8');
const roster = JSON.parse(read('blocks/myprogress/formula-board-roster-2026.json'));
const guideData = JSON.parse(read('blocks/myprogress/formulas-study-guide-data.json'));
const php = read('blocks/myprogress/formulas-study-guide.php');
const categoryMapPhp = read('blocks/myprogress/herb-formula-category-map.php');
const studyGuideHubPhp = read('blocks/myprogress/study-guide.php');

const fail = message => {
    throw new Error(`Bensky formula chapter audit failed: ${message}`);
};
const assert = (condition, message) => {
    if (!condition) fail(message);
};

const expectedPrincipalCounts = {
    1: 21, 2: 17, 3: 12, 4: 32, 5: 7, 6: 11, 7: 6,
    8: 45, 9: 17, 10: 9, 11: 7, 12: 20, 13: 23, 14: 17,
    15: 8, 16: 33, 17: 17, 18: 8, 19: 3, 20: 14, 21: 11,
};

assert(roster.total === 181 && roster.formulas.length === 181, 'Appendix C must contain 181 formulas.');
assert(new Set(roster.formulas.map(formula => formula.name)).size === 181, 'Appendix C names must be unique.');
assert(
    JSON.stringify(roster.categorySystem?.principalFormulaCountsByChapter) === JSON.stringify(expectedPrincipalCounts),
    'the 21 principal chapter counts do not match the reviewed Bensky contents pages.',
);

for (const formula of roster.formulas) {
    assert(Array.isArray(formula.benskyCategories) && formula.benskyCategories.length > 0,
        `${formula.name} has no Bensky chapter placement.`);
    assert(formula.benskyCategories.some(category => category.chapter === formula.benskyCategory?.chapter),
        `${formula.name} primary chapter is absent from its complete placement list.`);
}

const expectedOfficialPlacements = new Map([
    ['Bai Hu Tang', [4]],
    ['Da Cheng Qi Tang', [2]],
    ['Ban Xia Xie Xin Tang', [3]],
    ['Run Chang Wan', [2]],
    ['Ji Chuan Jian', [2]],
    ['Ma Xing Shi Gan Tang', [4]],
    ['Dang Gui Liu Huang Tang', [4]],
    ['Bai He Gu Jin Tang', [4, 8]],
]);
for (const [name, expectedChapters] of expectedOfficialPlacements) {
    const formula = roster.formulas.find(item => item.name === name);
    assert(formula, `missing official formula ${name}.`);
    const actualChapters = formula.benskyCategories.map(category => category.chapter);
    assert(JSON.stringify(actualChapters) === JSON.stringify(expectedChapters),
        `${name} is mapped to ${actualChapters.join(', ')}, expected ${expectedChapters.join(', ')}.`);
}

const runChangWan = guideData.supplementalFormulas.find(formula => formula.name === 'Run Chang Wan');
const legacyExpansion = guideData.supplementalCategories.find(category => category.key === 'official-formulas-ch3');
assert(runChangWan?.number === 'C02-01', 'Run Chang Wan detailed profile must carry its Chapter 2 identifier.');
assert(legacyExpansion?.sourceChapter === 2, 'the legacy expansion route must identify Bensky Chapter 2.');

const xiaoXianXiong = guideData.supplementalFormulas.find(formula => formula.name === 'Xiao Xian Xiong Tang');
assert(xiaoXianXiong?.chapterRole === 'associated', 'Xiao Xian Xiong Tang must be marked as associated.');
assert(xiaoXianXiong?.associatedWith === 'Qing Qi Hua Tan Wan',
    'Xiao Xian Xiong Tang must remain associated with Qing Qi Hua Tan Wan.');
assert(php.includes('array_splice($transformphlegmchapterformulas, 7, 0, [$transformphlegmassociatedformula]);'),
    'the complete Xiao Xian Xiong Tang profile is no longer visible after its parent formula.');
assert(php.includes("'Fórmula asociada bajo '"),
    'the Chapter 17 interface does not label associated formulas separately.');

const extractPlan = variable => {
    const match = php.match(new RegExp(`\\$${variable} = \\[([\\s\\S]*?)\\n\\];`));
    assert(match, `could not locate $${variable}.`);
    return [...match[1].matchAll(/^\s*\['([^']+)'/gm)].map(item => item[1]);
};

const planExpectations = new Map([
    ['expeldampchapterplan', 33],
    ['transformphlegmchapterplan', 17],
    ['digestivechapterplan', 8],
    ['expelparasiteschapterplan', 3],
    ['treatabscesseschapterplan', 14],
    ['externalapplicationchapterplan', 11],
]);
for (const [variable, expectedCount] of planExpectations) {
    const plan = extractPlan(variable);
    assert(plan.length === expectedCount, `$${variable} contains ${plan.length}, expected ${expectedCount}.`);
}

const dampPlan = extractPlan('expeldampchapterplan');
assert(
    JSON.stringify(dampPlan.slice(0, 3)) === JSON.stringify([
        'Ping Wei San', 'Bu Huan Jin Zheng Qi San', 'Huo Xiang Zheng Qi San',
    ]),
    'Chapter 16 opening order does not match Bensky.',
);
const phlegmPlan = extractPlan('transformphlegmchapterplan');
assert(!phlegmPlan.includes('Xiao Xian Xiong Tang'),
    'Xiao Xian Xiong Tang is still counted as a Chapter 17 principal formula.');
assert(phlegmPlan.at(-1) === 'Gua Di San', 'Chapter 17 principal sequence must end with Gua Di San.');

assert(!/18 principal formulas in (?:complete )?Bensky Chapter 17/.test(php),
    'stale English Chapter 17 total remains in the PHP guide.');
assert(!/18 fórmulas principales del capítulo 17/.test(php),
    'stale Spanish Chapter 17 total remains in the PHP guide.');
assert(php.includes("$categories['purge']['formulas'][] = $runchangwanprofile;"),
    'Run Chang Wan is not integrated into the Purge category.');
assert(php.includes("'official-formulas-ch3' => 'purge'"),
    'the legacy Run Chang Wan route does not resolve to Purge.');
assert(php.includes("$runchangwanprofile['integratedOfficialProfile'] = true;"),
    'Run Chang Wan is not marked as an integrated official profile.');
assert(php.includes("'formulaNames' => ['Run Chang Wan']"),
    'Run Chang Wan has no dedicated clinical section under Purge.');
assert(!php.includes('$nextcategorycardnumber'),
    'the removed additional category card is still being numbered.');
assert(!php.includes('foreach ($officialexpansionindexcategories as $category)'),
    'the removed additional category card is still rendered.');

const chapterIndexMatch = php.match(/\$benskychapters = \[([\s\S]*?)\n\];\n\$benskychapterbykey/);
assert(chapterIndexMatch, 'the 21-chapter Bensky index is missing.');
const chapterIndexRows = [...chapterIndexMatch[1].matchAll(/\['number' => (\d+), 'key' => '([^']+)', 'name' => '([^']+)', 'nameEs' => '[^']+', 'complete' => (true|false)/g)];
assert(chapterIndexRows.length === 21, `the Bensky index contains ${chapterIndexRows.length} chapters, expected 21.`);
assert(chapterIndexRows.every((row, index) => Number(row[1]) === index + 1),
    'the Bensky chapter cards are not numbered consecutively from 1 through 21.');
assert(chapterIndexRows.filter(row => row[4] === 'true').length === 17,
    'the index must identify complete coverage for Bensky Chapters 5 through 21.');
assert(chapterIndexRows[19][2] === 'treat-abscesses-chapter-20'
    && chapterIndexRows[19][3] === 'Formulas That Treat Abscesses and Sores',
    'Bensky Chapter 20 is missing or mislabeled in the visible index.');
assert(chapterIndexRows[20][2] === 'external-application-chapter-21'
    && chapterIndexRows[20][3] === 'Formulas for External Application',
    'Bensky Chapter 21 is missing or mislabeled in the visible index.');
assert(php.includes('foreach ($benskychapters as $chapter)'),
    'the main guide does not render the 21-chapter Bensky index.');

const formulaMapMatch = categoryMapPhp.match(/\$formulachapters = \[([\s\S]*?)\n\];/);
const herbMapMatch = categoryMapPhp.match(/\$herbcategories = \[([\s\S]*?)\n\];/);
assert(formulaMapMatch && herbMapMatch, 'the combined herb-formula category arrays are missing.');
const mappedFormulaChapters = [...formulaMapMatch[1].matchAll(/^\s+(\d+) => \['Formulas/gm)].map(item => Number(item[1]));
const mappedHerbCards = [...herbMapMatch[1].matchAll(/^\s+\[(\d+),\s*(\d+),/gm)].map(item => ({card: Number(item[1]), chapter: Number(item[2])}));
assert(JSON.stringify(mappedFormulaChapters) === JSON.stringify(Array.from({length: 21}, (_, index) => index + 1)),
    'the combined map does not contain all 21 formula chapters in order.');
assert(mappedHerbCards.length === 34 && mappedHerbCards.every((item, index) => item.card === index + 1),
    'the combined map does not contain all 34 herb-category cards in order.');
assert(mappedHerbCards.filter(item => item.card >= 3 && item.card <= 7).every(item => item.chapter === 2),
    'the five Clear Heat herb categories are not preserved under Bensky herb Chapter 2.');
assert(categoryMapPhp.includes("[4, 20], 'direct'")
    && categoryMapPhp.includes("[4, 16], 'direct'")
    && categoryMapPhp.includes("[4, 13], 'direct'"),
    'the Clear Heat map is missing its toxicity, Dampness, or Blood relationships.');
assert(studyGuideHubPhp.includes("'title' => 'Bensky Category Crosswalk'")
    && studyGuideHubPhp.includes("'path' => '/blocks/myprogress/herb-formula-category-map.php'"),
    'the fourth combined card is missing from the Chinese Herbology hub.');

console.log('Bensky formula chapter audit passed: 21 chapters, 181 official formulas, and principal/associated roles verified.');
