import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = relative => fs.readFileSync(path.join(root, relative), 'utf8');
const roster = JSON.parse(read('blocks/myprogress/formula-board-roster-2026.json'));
const guideData = JSON.parse(read('blocks/myprogress/formulas-study-guide-data.json'));
const php = read('blocks/myprogress/formulas-study-guide.php');

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
assert(php.includes('$nextcategorycardnumber = count($categories) + 1;'),
    'additional category cards are not numbered independently from Bensky chapters.');
assert(php.includes('<span class="formula-study-guide__category-number"><?php echo $nextcategorycardnumber++; ?></span>'),
    'the independent card number is not rendered on official expansion cards.');

console.log('Bensky formula chapter audit passed: 21 chapters, 181 official formulas, and principal/associated roles verified.');
