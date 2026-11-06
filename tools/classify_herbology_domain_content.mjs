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
        feedbackStatus: 'needs-review',
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
            status: 'classified',
            notes: `Automated first-pass classification: ${reason}. Requires human review before approval.`,
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

const classifiedCounts = Object.fromEntries(domainIds.map(domainId => [domainId, domains[domainId].length]));
const approvalReadyCounts = Object.fromEntries(domainIds.map(domainId => [
    domainId,
    domains[domainId].filter(question => question.prompt
        && question.options.length >= 2
        && question.correctLabel
        && question.explanation
        && question.source.title
        && question.source.locator).length,
]));
const output = {
    version: 1,
    classificationVersion: 'herbology-domain-first-pass-2026-11-06',
    status: 'classified-not-approved',
    policy: 'Existing keyed multiple-choice items are assigned to one primary working Domain. Automated assignments remain classified drafts until human review. Q&A references and content cards are inventoried as authoring sources but are not converted into multiple-choice questions automatically.',
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
        approvalReadyByDomain: approvalReadyCounts,
        approvalReadyTotal: Object.values(approvalReadyCounts).reduce((sum, count) => sum + count, 0),
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
