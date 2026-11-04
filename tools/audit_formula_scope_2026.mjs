import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = relative => fs.readFileSync(path.join(root, relative), 'utf8');
const roster = JSON.parse(read('blocks/myprogress/formula-board-roster-2026.json'));
const guideData = JSON.parse(read('blocks/myprogress/formulas-study-guide-data.json'));
const source2026Names = read('tools/data/ncbahm_2026_appendix_c_formulas.txt')
    .split(/\r?\n/).map(value => value.trim()).filter(Boolean)
    .map(value => value.replace(/ \([^\n]*\)$/, ''));
const oldRoster = read('tools/data/nccaom_2020_appendix_b_formulas.txt')
    .split(/\r?\n/).map(value => value.trim()).filter(Boolean);

if (roster.total !== 181 || roster.formulas.length !== 181) {
    throw new Error('The official 2026 formula roster must contain exactly 181 formulas.');
}

const officialNames = new Set(roster.formulas.map(formula => formula.name));
const source2026Set = new Set(source2026Names);
if (source2026Set.size !== 181 || officialNames.size !== 181) {
    throw new Error('The Appendix C source and application roster must each contain 181 unique formulas.');
}
const missingFromApplication = source2026Names.filter(name => !officialNames.has(name));
const absentFromSource = [...officialNames].filter(name => !source2026Set.has(name));
if (missingFromApplication.length || absentFromSource.length) {
    throw new Error(
        `The application roster differs from Appendix C. Missing: ${missingFromApplication.join(', ')}; extra: ${absentFromSource.join(', ')}`
    );
}
const aliases = new Map([
    ['Ge Gen Huang Lian Huang Qin Tang', 'Ge Gen Huang Qin Huang Lian Tang'],
    ['Jin Gui Shen Qi Wan', 'Shen Qi Wan'],
    ['Qing Wen Bai Du San', 'Qing Wen Bai Du Yin'],
    ['Tai Shan Pan Shi San', 'Taishan Pan Shi San'],
]);
const normalize = name => aliases.get(name) ?? name;
const removed = oldRoster.filter(name => !officialNames.has(normalize(name))).sort();
const expectedRemoved = ['Bi Xie Feng Qing Yin', 'Wei Jing Tang'];
if (JSON.stringify(removed) !== JSON.stringify(expectedRemoved)) {
    throw new Error(`Unexpected 2020→2026 removals: ${removed.join(', ')}`);
}

const previouslyMisclassified = [
    'Run Chang Wan',
    'Juan Bi Tang',
    'Zhi Bai Di Huang Wan',
    'Qi Ju Di Huang Wan',
    'Tao Hong Si Wu Tang',
    'Gui Zhi Jia Long Gu Mu Li Tang',
];
for (const name of previouslyMisclassified) {
    if (!officialNames.has(name)) {
        throw new Error(`${name} must be present in the official 2026 roster.`);
    }
}

const officialExpansion = guideData.supplementalCategories.find(
    category => category.key === 'official-formulas-ch3'
);
if (!officialExpansion || officialExpansion.sourceType !== 'official-expansion') {
    throw new Error('Run Chang Wan must be stored as an official expansion, not historical material.');
}
const runChangWan = guideData.supplementalFormulas.find(formula => formula.name === 'Run Chang Wan');
const juanBiTang = guideData.supplementalFormulas.find(formula => formula.name === 'Juan Bi Tang');
if (runChangWan?.categoryKey !== 'official-formulas-ch3' || !juanBiTang) {
    throw new Error('The Run Chang Wan or Juan Bi Tang detailed profile is misclassified.');
}

const forbiddenClaims = [
    'Run Chang Wan was listed in NCCAOM 2020 Appendix B, but it is not in the current NCBAHM 2026 Appendix C',
    'Run Chang Wan is widely taught and used in clinical practice for dry constipation from Blood and fluid deficiency, yet the current Appendix C does not include it',
    'Run Chang Wan es una fórmula muy enseñada y utilizada en la práctica para estreñimiento seco por deficiencia de Sangre y líquidos, pero el Appendix C vigente no la incluye',
    'Juan Bi Tang is not included in the current official 2026 NCBAHM Appendix C roster',
    'Las cuatro variantes resultantes no están en el Appendix C de 2026',
    'The four resulting variants are not in 2026 Appendix C',
];
const auditedFiles = [
    'blocks/myprogress/formulas-study-guide-data.json',
    'blocks/myprogress/formulas-study-guide.php',
    'tools/generate_board_guidelines.py',
    'tools/generate_dui_yao.mjs',
    'study-guide/guidelines/chinese-herbal-medicine.html',
    'study-guide/guidelines/chinese-herbal-medicine-es.html',
    'study-guide/comparisons/dui-yao.html',
    'study-guide/comparisons/dui-yao-es.html',
];
for (const relative of auditedFiles) {
    const content = read(relative);
    for (const claim of forbiddenClaims) {
        if (content.includes(claim)) {
            throw new Error(`Obsolete scope claim remains in ${relative}: ${claim}`);
        }
    }
}

if (officialExpansion.number === 'H1') {
    throw new Error('The official expansion still uses the obsolete historical H1 label.');
}

const officialSupplementalProfiles = guideData.supplementalFormulas.filter(
    formula => officialNames.has(formula.name) || formula.name === 'Jin Gui Shen Qi Wan'
);
if (officialSupplementalProfiles.length !== 83) {
    throw new Error(`Expected 83 official supplemental profiles; found ${officialSupplementalProfiles.length}.`);
}

const collectStrings = value => {
    if (typeof value === 'string') return [value];
    if (Array.isArray(value)) return value.flatMap(collectStrings);
    if (value && typeof value === 'object') return Object.values(value).flatMap(collectStrings);
    return [];
};
const scopeNegation = /(?:(?:not|does not|isn't|outside|absent|no (?:está|figura|aparece|se incluye)|fuera).{0,100}(?:appendix c|apéndice c|official|oficial|board|2026)|(?:appendix c|apéndice c|official|oficial|board|2026).{0,100}(?:not|does not|isn't|outside|absent|no (?:está|figura|aparece|se incluye)|fuera))/i;
for (const value of collectStrings(guideData)) {
    if (!scopeNegation.test(value)) continue;
    const namedOfficial = previouslyMisclassified.find(name => value.includes(name));
    if (namedOfficial) {
        throw new Error(`A negative scope claim remains for official formula ${namedOfficial}: ${value}`);
    }
}

console.log('Formula scope audit passed: 181 current formulas, 2 verified removals, and 6 corrected official entries.');
