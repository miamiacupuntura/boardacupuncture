#!/usr/bin/env node

import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const scriptDirectory = path.dirname(fileURLToPath(import.meta.url));
const repositoryRoot = path.resolve(scriptDirectory, '..');
const resolveFromRoot = relative => path.join(repositoryRoot, relative);
const readJson = relative => JSON.parse(fs.readFileSync(resolveFromRoot(relative), 'utf8'));
const readJsonLines = relative => fs.readFileSync(resolveFromRoot(relative), 'utf8')
    .split(/\r?\n/)
    .filter(Boolean)
    .map(line => JSON.parse(line));
const sha256 = relative => crypto.createHash('sha256')
    .update(fs.readFileSync(resolveFromRoot(relative)))
    .digest('hex');

const legacySource = 'output/complete-final-import/keyed-questions.jsonl';
const modificationSources = [
    'study-guide/comparisons/formula-modifications-generated.json',
    'study-guide/comparisons/formula-modifications-supplement.json',
];

if (!fs.existsSync(resolveFromRoot(legacySource))) {
    throw new Error(`Missing classified source: ${legacySource}`);
}

const domainIds = [
    'formula_differentiation',
    'single_herb',
    'dui_yao',
    'safety_dispensing',
    'formula_composition',
    'scope_aliases',
];
const domains = Object.fromEntries(domainIds.map(domainId => [domainId, []]));

const safetyPattern = /\b(cautions?|contraindicat\w*|traditional dose|dosage|what (?:is )?the dose|how much|side effects?|adverse effects?|drug interactions?|preparation method|decoct(?:ed)? first|overdose|prohibited)\b/i;
const scopePattern = /\b(alias(?:es)?|pharmaceutical name|official (?:list|roster)|obsolete|historical alternative|legal substitute|endangered|name variant)\b/i;
const compositionPattern = /\b(ingredients?|chief herbs?|chief ingredient|deputy|assistant|envoy|minister|composition|composed of|consists? of|component|which herbs? in|paired herbs?|modify|modification|add (?:which|what)|remove|subtract|replace)\b/i;
const comparisonPattern = /\b(difference|differentiate|compare|comparison|versus|\bvs\.?\b|not correct|except)\b/i;
const casePattern = /\b(patient|tongue|pulse|manifested by|clinical manifestations?|complains?|presents?|symptoms?)\b/i;

function classifyLegacyQuestion(question) {
    const testedTask = question.prompt;
    if (/^\s*IDENTIFICATION\b/i.test(testedTask)) {
        return ['formula_differentiation', 'Formula identification from a supplied pattern or profile'];
    }
    if (safetyPattern.test(testedTask)) {
        return ['safety_dispensing', 'Explicit dosage, contraindication, preparation or safety language'];
    }
    if (scopePattern.test(testedTask)) {
        return ['scope_aliases', 'Explicit nomenclature, roster or substitution language'];
    }
    if (compositionPattern.test(testedTask)) {
        return ['formula_composition', 'Ingredient role, formula construction or modification language'];
    }
    return ['formula_differentiation', 'Formula indication, pattern recognition or selection'];
}

function classifyCognitiveMetadata(prompt, domainId) {
    if (domainId === 'safety_dispensing') {
        return ['application', 'apply', 'safety'];
    }
    if (comparisonPattern.test(prompt)) {
        return ['analysis', 'analyze', 'comparison'];
    }
    if (casePattern.test(prompt)) {
        return ['application', 'apply', 'case'];
    }
    if (domainId === 'formula_composition') {
        return ['foundation', 'understand', 'recall'];
    }
    return ['application', 'apply', 'recall'];
}

function createCandidate({
    questionId,
    domainId,
    prompt,
    options,
    correctLabel,
    explanation,
    sourceTitle,
    sourceLocator,
    reason,
}) {
    const [difficultyBand, cognitiveLevel, contentType] = classifyCognitiveMetadata(prompt, domainId);
    return {
        questionId,
        questionTrack: 'board',
        criteriaVersion: '1.0-draft',
        itemFormat: 'multiple-choice',
        feedbackStatus: 'practice-ready',
        examId: 'chinese-herbal-medicine',
        outlineVersion: 'pending-official-confirmation',
        domainId,
        objectiveId: `working-${domainId}`,
        difficultyBand,
        cognitiveLevel,
        contentType,
        language: 'en',
        source: {
            title: sourceTitle,
            locator: sourceLocator,
        },
        review: {
            status: 'practice-ready',
            notes: `Source-grounded practice preparation: ${reason}.`,
        },
        prompt,
        options,
        correctLabel,
        explanation,
        classification: {
            method: 'deterministic-rule-v1',
            confidence: domainId === 'formula_differentiation' ? 'medium' : 'high',
            reason,
        },
    };
}

function stableNumber(value) {
    return [...String(value)].reduce((total, character) => ((total * 31) + character.charCodeAt(0)) >>> 0, 17);
}

function buildOptions(correctAnswer, answerPool, seed) {
    const alternatives = [...new Set(answerPool)].filter(answer => answer && answer !== correctAnswer);
    const selected = [];
    let cursor = stableNumber(seed) % alternatives.length;
    while (selected.length < 3 && selected.length < alternatives.length) {
        const candidate = alternatives[cursor % alternatives.length];
        if (!selected.includes(candidate)) {
            selected.push(candidate);
        }
        cursor += 17;
    }
    const correctPosition = stableNumber(`${seed}-correct`) % 4;
    const answers = [...selected];
    answers.splice(correctPosition, 0, correctAnswer);
    return answers.map((text, index) => ({label: String.fromCharCode(65 + index), text}));
}

const legacyQuestions = readJsonLines(legacySource);
for (const question of legacyQuestions) {
    const [domainId, reason] = classifyLegacyQuestion(question);
    const candidate = createCandidate({
        questionId: `CF-${String(question.source_number).padStart(4, '0')}`,
        domainId,
        prompt: question.prompt,
        options: question.options.map(option => ({label: option.label, text: option.text})),
        correctLabel: question.correct_labels[0],
        explanation: (question.feedback || []).join('\n\n'),
        sourceTitle: 'Complete Final.docx',
        sourceLocator: `Question ${question.source_number}`,
        reason,
    });
    domains[domainId].push(candidate);
}

let modificationCount = 0;
for (const relative of modificationSources) {
    const source = readJson(relative);
    for (const question of source.questions || []) {
        const questionId = `FM-${String(question.sourceNumber).replace(/[^A-Za-z0-9-]+/g, '-')}`;
        domains.formula_composition.push(createCandidate({
            questionId,
            domainId: 'formula_composition',
            prompt: question.prompt,
            options: (question.options || []).map(option => ({label: option.label, text: option.text})),
            correctLabel: question.correctLabel,
            explanation: (question.feedback || []).join('\n\n'),
            sourceTitle: question.provenance?.sourceTitle || 'Formula modification study content',
            sourceLocator: question.sourceId || String(question.sourceNumber),
            reason: 'Question was authored specifically for formula modification practice',
        }));
        modificationCount++;
    }
}

for (const domainId of domainIds) {
    const ids = new Set();
    domains[domainId] = domains[domainId].filter(question => {
        if (ids.has(question.questionId)) {
            return false;
        }
        ids.add(question.questionId);
        return true;
    });
}

const herbComparisonBank = readJson('study-guide/comparisons/herb-comparison-bank-data.json');
const duiYaoPairs = readJson('study-guide/comparisons/dui-yao-data.json');
const herbProfiles = readJson('blocks/myprogress/single-herb-properties.json');
const herbSafety = readJson('blocks/myprogress/single-herb-safety-supplement.json');
const formulaRoster = readJson('blocks/myprogress/formula-board-roster-2026.json');
const englishFormulaTheoryPages = fs.readdirSync(resolveFromRoot('study-guide/comparisons'))
    .filter(filename => /^formula-theory-.*\.html$/.test(filename) && !filename.endsWith('-es.html')).length;
const englishSafetyPages = fs.readdirSync(resolveFromRoot('study-guide/comparisons'))
    .filter(filename => /^safety-.*\.html$/.test(filename) && !filename.endsWith('-es.html')).length;

const usableHerbProfiles = (herbProfiles.herbs || []).filter(herb => herb.name
    && herb.actionSummary?.en
    && herb.indicationSummary?.en);
const herbNames = usableHerbProfiles.map(herb => herb.name);
for (const herb of usableHerbProfiles) {
    const options = buildOptions(herb.name, herbNames, `herb-${herb.guideNumber || herb.name}`);
    const correctOption = options.find(option => option.text === herb.name);
    domains.single_herb.push(createCandidate({
        questionId: `SH-${String(herb.guideNumber || stableNumber(herb.name)).padStart(4, '0')}`,
        domainId: 'single_herb',
        prompt: `Which herb best matches this profile? ${herb.actionSummary.en} Principal indications: ${herb.indicationSummary.en}`,
        options,
        correctLabel: correctOption.label,
        explanation: `${herb.name} (${herb.latin || 'pharmaceutical name not supplied'}) is ${herb.temperature?.en || 'of the recorded temperature'} and ${herb.flavor?.en || 'has the recorded flavor profile'}. ${herb.actionSummary.en}`,
        sourceTitle: 'Single Herb verified study profiles',
        sourceLocator: `Study Guide herb ${herb.guideNumber || herb.name}`,
        reason: 'Source-grounded question generated from a verified individual-herb profile',
    }));
}

const usablePairs = (Array.isArray(duiYaoPairs) ? duiYaoPairs : []).filter(pair => pair.title && pair.action?.en);
const pairTitles = usablePairs.map(pair => pair.title);
for (const pair of usablePairs) {
    const options = buildOptions(pair.title, pairTitles, `duiyao-${pair.number || pair.slug}`);
    const correctOption = options.find(option => option.text === pair.title);
    domains.dui_yao.push(createCandidate({
        questionId: `DY-${String(pair.number || stableNumber(pair.slug)).padStart(3, '0')}`,
        domainId: 'dui_yao',
        prompt: `Which Duì Yào pair has this combined action? ${pair.action.en}`,
        options,
        correctLabel: correctOption.label,
        explanation: `${pair.title} performs this combined action. Representative formula: ${pair.formulas?.en || 'see the pair profile'}.`,
        sourceTitle: 'Official Duì Yào pair profiles',
        sourceLocator: `Pair ${pair.number || pair.slug}`,
        reason: 'Source-grounded question generated from an official pair profile',
    }));
}

const safetyEntries = Object.entries(herbSafety.herbs || {}).filter(([, safety]) => safety.caution?.en);
const safetyHerbNames = safetyEntries.map(([name]) => name);
for (const [index, [herbName, safety]] of safetyEntries.entries()) {
    const options = buildOptions(herbName, safetyHerbNames, `safety-${herbName}`);
    const correctOption = options.find(option => option.text === herbName);
    const preparation = safety.preparation?.en ? ` Preparation: ${safety.preparation.en}` : '';
    domains.safety_dispensing.push(createCandidate({
        questionId: `SD-${String(index + 1).padStart(3, '0')}`,
        domainId: 'safety_dispensing',
        prompt: `Which herb is associated with this safety guidance? ${safety.caution.en}${preparation}`,
        options,
        correctLabel: correctOption.label,
        explanation: `${herbName}: ${safety.caution.en}${preparation}`,
        sourceTitle: 'Single Herb safety supplement',
        sourceLocator: `Safety record: ${herbName}`,
        reason: 'Source-grounded question generated from a named herb safety record',
    }));
}

const usableFormulaRoster = (formulaRoster.formulas || []).filter(formula => formula.name && formula.englishName);
const formulaNames = usableFormulaRoster.map(formula => formula.name);
for (const formula of usableFormulaRoster) {
    const options = buildOptions(formula.name, formulaNames, `roster-${formula.id || formula.name}`);
    const correctOption = options.find(option => option.text === formula.name);
    domains.scope_aliases.push(createCandidate({
        questionId: `RA-${String(formula.id || stableNumber(formula.name)).padStart(3, '0')}`,
        domainId: 'scope_aliases',
        prompt: `Within the current formula roster, which pinyin name corresponds to “${formula.englishName}”?`,
        options,
        correctLabel: correctOption.label,
        explanation: `${formula.englishName} is listed as ${formula.name} in the current formula roster.`,
        sourceTitle: '2026 Formula Board roster',
        sourceLocator: `Roster entry ${formula.id || formula.name}`,
        reason: 'Source-grounded nomenclature question generated from the current formula roster',
    }));
}

for (const domainId of domainIds) {
    const ids = new Set();
    domains[domainId] = domains[domainId].filter(question => {
        if (ids.has(question.questionId)) {
            return false;
        }
        ids.add(question.questionId);
        return true;
    });
}

const classifiedCounts = Object.fromEntries(domainIds.map(domainId => [domainId, domains[domainId].length]));
const practiceReadyCounts = Object.fromEntries(domainIds.map(domainId => [
    domainId,
    domains[domainId].filter(question => question.prompt
        && question.options.length === 4
        && question.correctLabel
        && question.explanation
        && question.source.title
        && question.source.locator).length,
]));
const output = {
    version: 1,
    classificationVersion: 'herbology-domain-source-grounded-2026-11-06-v2',
    status: 'practice-ready',
    policy: 'Existing keyed questions and source-grounded generated questions are assigned to one primary study Domain. Complete questions may be used directly in practice exams.',
    generatedFrom: {
        legacyKeyedQuestions: {
            path: legacySource,
            sha256: sha256(legacySource),
            count: legacyQuestions.length,
        },
        formulaModificationQuestions: modificationSources.map(relative => ({
            path: relative,
            sha256: sha256(relative),
        })),
    },
    summary: {
        classifiedQuestionCandidates: Object.values(classifiedCounts).reduce((sum, count) => sum + count, 0),
        legacyKeyedQuestions: legacyQuestions.length,
        generatedModificationQuestions: modificationCount,
        byDomain: classifiedCounts,
        practiceReadyByDomain: practiceReadyCounts,
        practiceReadyTotal: Object.values(practiceReadyCounts).reduce((sum, count) => sum + count, 0),
    },
    sourceInventory: {
        formula_differentiation: {
            keyedQuestionCandidates: classifiedCounts.formula_differentiation,
            referenceFormulaRoster: formulaRoster.total || (formulaRoster.formulas || []).length,
            authoringSourceLabel: `${formulaRoster.total || (formulaRoster.formulas || []).length} official formula roster entries`,
        },
        single_herb: {
            keyedQuestionCandidates: classifiedCounts.single_herb,
            qAndAAuthoringSources: herbComparisonBank.totals?.questions || 0,
            herbProfiles: herbProfiles.total || (herbProfiles.herbs || []).length,
            authoringSourceLabel: `${herbComparisonBank.totals?.questions || 0} Q&A records · ${herbProfiles.total || (herbProfiles.herbs || []).length} herb profiles`,
        },
        dui_yao: {
            keyedQuestionCandidates: classifiedCounts.dui_yao,
            pairAuthoringSources: Array.isArray(duiYaoPairs) ? duiYaoPairs.length : 0,
            authoringSourceLabel: `${Array.isArray(duiYaoPairs) ? duiYaoPairs.length : 0} official pair profiles`,
        },
        safety_dispensing: {
            keyedQuestionCandidates: classifiedCounts.safety_dispensing,
            herbSafetyEntries: Object.keys(herbSafety.herbs || {}).length,
            categoryCautions: Object.keys(herbSafety.categoryCautions || {}).length,
            categoryPregnancyGroups: Object.keys(herbSafety.categoryPregnancy || {}).length,
            categoryInteractionGroups: Object.keys(herbSafety.categoryInteractions || {}).length,
            safetyStudyPages: englishSafetyPages,
            authoringSourceLabel: `${Object.keys(herbSafety.herbs || {}).length} herb safety records · ${englishSafetyPages} safety study pages`,
        },
        formula_composition: {
            keyedQuestionCandidates: classifiedCounts.formula_composition,
            generatedModificationQuestions: modificationCount,
            formulaTheoryStudyPages: englishFormulaTheoryPages,
            authoringSourceLabel: `${modificationCount} modification questions · ${englishFormulaTheoryPages} theory pages`,
        },
        scope_aliases: {
            keyedQuestionCandidates: classifiedCounts.scope_aliases,
            formulaRosterEntries: formulaRoster.total || (formulaRoster.formulas || []).length,
            herbRosterEntries: herbProfiles.total || (herbProfiles.herbs || []).length,
            authoringSourceLabel: `${formulaRoster.total || (formulaRoster.formulas || []).length} formulas · ${herbProfiles.total || (herbProfiles.herbs || []).length} herbs in current rosters`,
        },
    },
    domains,
};

const outputPath = resolveFromRoot('blocks/menu/herbology-domain-candidates.json');
fs.writeFileSync(outputPath, `${JSON.stringify(output, null, 2)}\n`);
process.stdout.write(`${JSON.stringify(output.summary, null, 2)}\n`);
