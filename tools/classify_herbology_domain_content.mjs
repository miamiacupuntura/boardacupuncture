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
const formulaPracticeSource = 'study-guide/comparisons/formula-practice-data.json';
const formulaSpanishSource = 'study-guide/comparisons/formula-practice-translations-es.json';
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
    sourceCard = null,
    reason,
    translationEs = null,
}) {
    const [difficultyBand, cognitiveLevel, contentType] = classifyCognitiveMetadata(prompt, domainId);
    const candidate = {
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
    if (sourceCard?.url && sourceCard?.urlEs) {
        candidate.sourceCard = sourceCard;
    }
    if (translationEs?.prompt && translationEs?.options?.length === 4) {
        candidate.translations = {es: translationEs};
    }
    return candidate;
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

function normalizeEntityText(value) {
    return String(value || '').toLowerCase().normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, ' ')
        .trim();
}

function normalizedTextWithSourceMap(value) {
    const source = String(value || '');
    let normalized = '';
    const sourceMap = [];
    let pendingSpaceIndex = -1;
    for (let index = 0; index < source.length; index++) {
        const characters = source[index].toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        for (const character of characters) {
            if (/[a-z0-9]/.test(character)) {
                if (pendingSpaceIndex >= 0 && normalized !== '' && !normalized.endsWith(' ')) {
                    normalized += ' ';
                    sourceMap.push(pendingSpaceIndex);
                }
                pendingSpaceIndex = -1;
                normalized += character;
                sourceMap.push(index);
            } else if (normalized !== '' && !normalized.endsWith(' ')) {
                pendingSpaceIndex = index;
            }
        }
    }
    return {normalized, sourceMap};
}

function findEntityMatches(value, entityRegistry) {
    const source = String(value || '');
    const {normalized, sourceMap} = normalizedTextWithSourceMap(source);
    if (!normalized || sourceMap.length === 0) {
        return [];
    }
    const occupied = new Uint8Array(normalized.length);
    const matches = [];
    for (const entity of entityRegistry) {
        let offset = 0;
        while (offset < normalized.length) {
            const matchStart = normalized.indexOf(entity.normalizedName, offset);
            if (matchStart < 0) {
                break;
            }
            const matchEnd = matchStart + entity.normalizedName.length;
            const hasLeftBoundary = matchStart === 0 || normalized[matchStart - 1] === ' ';
            const hasRightBoundary = matchEnd === normalized.length || normalized[matchEnd] === ' ';
            const overlaps = occupied.slice(matchStart, matchEnd).some(Boolean);
            if (hasLeftBoundary && hasRightBoundary && !overlaps) {
                const rawStart = sourceMap[matchStart];
                const rawEnd = sourceMap[matchEnd - 1] + 1;
                if (Number.isInteger(rawStart) && Number.isInteger(rawEnd) && rawEnd > rawStart) {
                    occupied.fill(1, matchStart, matchEnd);
                    matches.push({...entity, rawStart, rawEnd});
                }
            }
            offset = matchStart + 1;
        }
    }
    return matches.sort((left, right) => left.rawStart - right.rawStart || right.rawEnd - left.rawEnd);
}

function enrichEntityNamesInText(value, entityRegistry) {
    const source = String(value || '');
    const matches = findEntityMatches(source, entityRegistry);
    const replacements = matches.filter(match => {
        const tail = source.slice(match.rawEnd, match.rawEnd + 220);
        const normalizedTail = normalizeEntityText(tail);
        if (normalizedTail.startsWith(match.normalizedDescriptor)) {
            return false;
        }
        const parenthetical = tail.match(/^\s*\(([^)]{1,200})\)/)?.[1] || '';
        if (match.kind === 'formula' && parenthetical && !/source text|texto de la fuente/i.test(parenthetical)) {
            return false;
        }
        if (match.kind === 'herb' && /\b(?:Rx|Rz|Hb|Fr|Rm|Sm|Cx|Fl|Fm|Blb|Pc|Sp|Os|Radix|Rhizoma|Rhizome|Herba|Flos|Fructus|Semen|Cortex|Ramulus|Caulis|Folium|Bulbus|Tuber|Concha|Cornu|Calculus|Plastrum|Carapax|Pericarpium|Sclerotium|Spica|Arillus|Colla|Gelatinum|Medulla|Squama|Poria|Polyporus|Moschus|Cinnabaris|Realgar|Mirabilitum|Gypsum)\b/i.test(parenthetical)) {
            return false;
        }
        return true;
    });
    let enriched = source;
    for (const match of replacements.sort((left, right) => right.rawEnd - left.rawEnd)) {
        enriched = `${enriched.slice(0, match.rawEnd)} (${match.descriptor})${enriched.slice(match.rawEnd)}`;
    }
    return enriched;
}

function enrichQuestionNomenclature(question, entityRegistry) {
    const englishFields = [
        question.prompt,
        ...(question.options || []).map(option => option.text),
        question.explanation,
    ];
    const spanishTranslation = question.translations?.es;
    const spanishFields = spanishTranslation ? [
        spanishTranslation.prompt,
        ...(spanishTranslation.options || []).map(option => option.text),
        spanishTranslation.explanation,
    ] : [];
    const referencedEntities = new Map();
    for (const field of [...englishFields, ...spanishFields]) {
        for (const match of findEntityMatches(field, entityRegistry)) {
            referencedEntities.set(`${match.kind}:${match.name}`, match);
        }
    }
    const correctOptionText = (question.options || [])
        .find(option => option.label === question.correctLabel)?.text || '';
    const correctOptionTextEs = (spanishTranslation?.options || [])
        .find(option => option.label === question.correctLabel)?.text || '';
    const feedbackEntities = new Map();
    for (const field of [correctOptionText, question.explanation, correctOptionTextEs, spanishTranslation?.explanation]) {
        for (const match of findEntityMatches(field, entityRegistry)) {
            feedbackEntities.set(`${match.kind}:${match.name}`, match);
        }
    }
    if (feedbackEntities.size === 0) {
        for (const field of [question.prompt, spanishTranslation?.prompt]) {
            for (const match of findEntityMatches(field, entityRegistry)) {
                feedbackEntities.set(`${match.kind}:${match.name}`, match);
            }
        }
    }
    const references = [...referencedEntities.values()];
    const feedbackReferences = [...feedbackEntities.values()];
    question.nomenclature = {
        herbs: references.filter(reference => reference.kind === 'herb').map(reference => ({
            pinyin: reference.name,
            pharmaceuticalName: reference.descriptor,
        })),
        formulas: references.filter(reference => reference.kind === 'formula').map(reference => ({
            pinyin: reference.name,
            englishName: reference.descriptor,
        })),
        feedback: {
            herbs: feedbackReferences.filter(reference => reference.kind === 'herb').map(reference => ({
                pinyin: reference.name,
                pharmaceuticalName: reference.descriptor,
            })),
            formulas: feedbackReferences.filter(reference => reference.kind === 'formula').map(reference => ({
                pinyin: reference.name,
                englishName: reference.descriptor,
            })),
        },
    };
    question.prompt = enrichEntityNamesInText(question.prompt, entityRegistry);
    question.options = (question.options || []).map(option => ({
        ...option,
        text: enrichEntityNamesInText(option.text, entityRegistry),
    }));
    question.explanation = enrichEntityNamesInText(question.explanation, entityRegistry);
    if (spanishTranslation) {
        question.translations.es = {
            ...spanishTranslation,
            prompt: enrichEntityNamesInText(spanishTranslation.prompt, entityRegistry),
            options: (spanishTranslation.options || []).map(option => ({
                ...option,
                text: enrichEntityNamesInText(option.text, entityRegistry),
            })),
            explanation: enrichEntityNamesInText(spanishTranslation.explanation, entityRegistry),
        };
    }
}

const legacyQuestions = readJsonLines(legacySource);
const formulaPracticeData = readJson(formulaPracticeSource);
const formulaProfileByNumber = new Map((formulaPracticeData.formulas || [])
    .map(formula => [Number(formula.number), formula]));
const formulaPracticeBySource = new Map((formulaPracticeData.questions || [])
    .map(question => [String(question.sourceNumber), question]));
const formulaSourceCard = formulaNumber => {
    const formula = formulaProfileByNumber.get(Number(formulaNumber));
    if (!formula?.slug) {
        return null;
    }
    return {
        url: `/study-guide/comparisons/${formula.slug}.html`,
        urlEs: `/study-guide/comparisons/${formula.slug}-es.html`,
        label: `Source card: ${formula.name}`,
        labelEs: `Tarjeta fuente: ${formula.name}`,
    };
};
const formulaModificationSourceCard = formulaNumber => {
    const formula = formulaProfileByNumber.get(Number(formulaNumber));
    if (!formula?.name) {
        return null;
    }
    const query = encodeURIComponent(formula.name);
    return {
        url: `/study-guide/comparisons/formula-modifications.html?q=${query}`,
        urlEs: `/study-guide/comparisons/formula-modifications-es.html?q=${query}`,
        label: `Modification source card: ${formula.name}`,
        labelEs: `Tarjeta fuente de modificación: ${formula.name}`,
    };
};
const normalizeMatchText = value => String(value || '').toLowerCase().normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, ' ')
    .trim();
const formulaQuestionSourceCard = (sourceNumber, correctOptionText) => {
    const formulaQuestion = formulaPracticeBySource.get(String(sourceNumber));
    const formulaNumbers = formulaQuestion?.formulaNumbers || [];
    const normalizedCorrectOption = ` ${normalizeMatchText(correctOptionText)} `;
    const matchedFormulaNumber = formulaNumbers.find(formulaNumber => {
        const formulaName = formulaProfileByNumber.get(Number(formulaNumber))?.name;
        return formulaName
            && normalizedCorrectOption.includes(` ${normalizeMatchText(formulaName)} `);
    });
    return formulaSourceCard(matchedFormulaNumber || formulaNumbers[0]);
};
const singleHerbSourceCard = herbName => ({
    url: `/blocks/myprogress/single-herbs-study-guide.php?herb=${encodeURIComponent(herbName)}`,
    urlEs: `/blocks/myprogress/single-herbs-study-guide.php?lang=es&herb=${encodeURIComponent(herbName)}`,
    label: `Source card: ${herbName}`,
    labelEs: `Tarjeta fuente: ${herbName}`,
});
const formulaSpanishQuestions = readJson(formulaSpanishSource).questions || [];
const formulaSpanishBySource = new Map(formulaSpanishQuestions.map(question => [String(question.sourceNumber), question]));
for (const question of legacyQuestions) {
    const [domainId, reason] = classifyLegacyQuestion(question);
    const spanish = formulaSpanishBySource.get(String(question.source_number));
    const correctOptionText = question.options
        .find(option => option.label === question.correct_labels[0])?.text || '';
    const candidate = createCandidate({
        questionId: `CF-${String(question.source_number).padStart(4, '0')}`,
        domainId,
        prompt: question.prompt,
        options: question.options.map(option => ({label: option.label, text: option.text})),
        correctLabel: question.correct_labels[0],
        explanation: (question.feedback || []).join('\n\n'),
        sourceTitle: 'Complete Final.docx',
        sourceLocator: `Question ${question.source_number}`,
        sourceCard: formulaQuestionSourceCard(question.source_number, correctOptionText),
        reason,
        translationEs: spanish ? {
            prompt: spanish.prompt,
            options: spanish.options,
            explanation: (spanish.feedback || []).join('\n\n') || (question.feedback || []).join('\n\n'),
        } : null,
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
            sourceCard: formulaModificationSourceCard(question.formulaNumbers?.[0]),
            reason: 'Question was authored specifically for formula modification practice',
            translationEs: question.translationEs ? {
                prompt: question.translationEs.prompt,
                options: question.translationEs.options,
                explanation: (question.translationEs.feedback || []).join('\n\n'),
            } : null,
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
const formulasStudyGuide = readJson('blocks/myprogress/formulas-study-guide-data.json');
const englishFormulaTheoryPages = fs.readdirSync(resolveFromRoot('study-guide/comparisons'))
    .filter(filename => /^formula-theory-.*\.html$/.test(filename) && !filename.endsWith('-es.html')).length;
const englishSafetyPages = fs.readdirSync(resolveFromRoot('study-guide/comparisons'))
    .filter(filename => /^safety-.*\.html$/.test(filename) && !filename.endsWith('-es.html')).length;

const usableHerbProfiles = (herbProfiles.herbs || []).filter(herb => herb.name
    && herb.actionSummary?.en
    && herb.indicationSummary?.en);

const formulaRosterByName = new Map((formulaRoster.formulas || [])
    .map(formula => [normalizeEntityText(formula.name), formula]));
const coreFormulaProfiles = (formulasStudyGuide.formulas || []).filter(formula => formula.name
    && formula.pattern
    && formula.action
    && formula.clue
    && formula.patternEs
    && formula.actionEs
    && formula.clueEs
    && formulaRosterByName.has(normalizeEntityText(formula.name)));
const coreFormulaNames = coreFormulaProfiles.map(formula => formula.name);
const relatedFormulaCategories = {
    purge: ['digestive'],
    'stabilize-bind': ['tonify'],
    'calm-shen': ['tonify'],
    'expel-wind': ['transform-phlegm'],
    digestive: ['expel-damp', 'regulate-qi'],
};
for (const formula of coreFormulaProfiles) {
    const categoryNames = coreFormulaProfiles
        .filter(candidate => candidate.categoryKey === formula.categoryKey)
        .map(candidate => candidate.name);
    const relatedNames = coreFormulaProfiles
        .filter(candidate => (relatedFormulaCategories[formula.categoryKey] || [])
            .includes(candidate.categoryKey))
        .map(candidate => candidate.name);
    const answerPool = categoryNames.length >= 4
        ? categoryNames
        : [...new Set([...categoryNames, ...relatedNames, ...coreFormulaNames])];
    const options = buildOptions(formula.name, answerPool, `formula-profile-${formula.number}`);
    const correctOption = options.find(option => option.text === formula.name);
    const rosterEntry = formulaRosterByName.get(normalizeEntityText(formula.name));
    domains.formula_differentiation.push(createCandidate({
        questionId: `FD-${String(formula.number).padStart(3, '0')}`,
        domainId: 'formula_differentiation',
        prompt: `A patient has the following presentation: ${formula.clue} Which formula best matches this presentation?`,
        options,
        correctLabel: correctOption.label,
        explanation: `${formula.name} treats ${formula.pattern} Action: ${formula.action} Decisive clue: ${formula.examFocus || formula.clue} Safety: ${formula.safety}`,
        sourceTitle: '2026 Appendix C core formula study profiles',
        sourceLocator: `Formula ${formula.number}: ${formula.name} (${rosterEntry.englishName})`,
        sourceCard: formulaSourceCard(formula.number),
        reason: 'Board-style clinical selection generated from an official Appendix C formula profile',
        translationEs: {
            prompt: `Un paciente presenta el siguiente cuadro: ${formula.clueEs} ¿Qué fórmula corresponde mejor a esta presentación?`,
            options,
            explanation: `${formula.name} trata ${formula.patternEs} Acción: ${formula.actionEs} Pista decisiva: ${formula.examFocusEs || formula.clueEs} Seguridad: ${formula.safetyEs}`,
        },
    }));
}

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
        sourceCard: singleHerbSourceCard(herb.name),
        reason: 'Source-grounded question generated from a verified individual-herb profile',
        translationEs: {
            prompt: `¿Qué hierba corresponde mejor a este perfil? ${herb.actionSummary.es || herb.actionSummary.en} Indicaciones principales: ${herb.indicationSummary.es || herb.indicationSummary.en}`,
            options,
            explanation: `${herb.name} (${herb.latin || 'nombre farmacéutico no disponible'}) es ${herb.temperature?.es || 'de la temperatura registrada'} y ${herb.flavor?.es || 'presenta el perfil de sabor registrado'}. ${herb.actionSummary.es}`,
        },
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
        explanation: `${pair.title} performs this combined action. It is official Appendix B pair ${pair.number} of 51.`,
        sourceTitle: 'Official Duì Yào pair profiles',
        sourceLocator: `Pair ${pair.number || pair.slug}`,
        sourceCard: {
            url: `/study-guide/comparisons/${pair.slug}.html`,
            urlEs: `/study-guide/comparisons/${pair.slug}-es.html`,
            label: `Source card: ${pair.title}`,
            labelEs: `Tarjeta fuente: ${pair.title}`,
        },
        reason: 'Source-grounded question generated from an official pair profile',
        translationEs: {
            prompt: `¿Qué par Duì Yào tiene esta acción combinada? ${pair.action.es || pair.action.en}`,
            options,
            explanation: `${pair.title} realiza esta acción combinada. Es el par oficial ${pair.number} de 51 del Appendix B.`,
        },
    }));
}

const safetyEntries = Object.entries(herbSafety.herbs || {}).filter(([, safety]) => safety.caution?.en);
const safetyHerbNames = safetyEntries.map(([name]) => name);
for (const [index, [herbName, safety]] of safetyEntries.entries()) {
    const options = buildOptions(herbName, safetyHerbNames, `safety-${herbName}`);
    const correctOption = options.find(option => option.text === herbName);
    const preparation = safety.preparation?.en ? ` Preparation: ${safety.preparation.en}` : '';
    const preparationEs = safety.preparation?.es ? ` Preparación: ${safety.preparation.es}` : '';
    domains.safety_dispensing.push(createCandidate({
        questionId: `SD-${String(index + 1).padStart(3, '0')}`,
        domainId: 'safety_dispensing',
        prompt: `Which herb is associated with this safety guidance? ${safety.caution.en}${preparation}`,
        options,
        correctLabel: correctOption.label,
        explanation: `${herbName}: ${safety.caution.en}${preparation}`,
        sourceTitle: 'Single Herb safety supplement',
        sourceLocator: `Safety record: ${herbName}`,
        sourceCard: singleHerbSourceCard(herbName),
        reason: 'Source-grounded question generated from a named herb safety record',
        translationEs: {
            prompt: `¿Qué hierba se asocia con esta orientación de seguridad? ${safety.caution.es || safety.caution.en}${preparationEs}`,
            options,
            explanation: `${herbName}: ${safety.caution.es || safety.caution.en}${preparationEs}`,
        },
    }));
}

const scopeSourceCard = {
    url: '/study-guide/comparisons/safety-obsolete-prohibited-substances.html',
    urlEs: '/study-guide/comparisons/safety-obsolete-prohibited-substances-es.html',
    label: 'Source card: obsolete, protected and product-identity safety',
    labelEs: 'Tarjeta fuente: seguridad, identidad y sustancias obsoletas o protegidas',
};
const scopeQuestions = [
    {
        id: 'identity-mu-tong',
        prompt: 'A supplier offers a product labeled only “Mu Tong,” without a scientific name. What is the safest decision before dispensing it?',
        options: ['Accept it because all Mu Tong products are equivalent', 'Quarantine it until the exact botanical identity and source are authenticated', 'Use half the usual dose to offset uncertainty', 'Relabel it as Guan Mu Tong'],
        correct: 1,
        explanation: 'Mu Tong is not a sufficient identity. Guan Mu Tong is an obsolete Aristolochia source associated with nephropathy and cancer, so the exact species must be authenticated before use.',
        promptEs: 'Un proveedor ofrece un producto rotulado solamente “Mu Tong”, sin nombre científico. ¿Cuál es la decisión más segura antes de dispensarlo?',
        optionsEs: ['Aceptarlo porque todos los productos Mu Tong son equivalentes', 'Ponerlo en cuarentena hasta autenticar la identidad botánica exacta y su procedencia', 'Usar la mitad de la dosis para compensar la incertidumbre', 'Cambiarle la etiqueta a Guan Mu Tong'],
        explanationEs: 'Mu Tong no constituye una identidad suficiente. Guan Mu Tong es una fuente obsoleta de Aristolochia asociada con nefropatía y cáncer; debe autenticarse la especie exacta antes de usarla.',
    },
    {
        id: 'identity-fang-ji',
        prompt: 'Which identity error creates the principal safety danger when a product is sold simply as “Fang Ji”?',
        options: ['Confusing Han Fang Ji (Stephania tetrandra) with Guang Fang Ji (Aristolochia fangchi)', 'Confusing Fang Feng with Jing Jie', 'Confusing Mu Gua with Wu Jia Pi', 'Confusing Fu Ling with Zhu Ling'],
        correct: 0,
        explanation: 'Guang Fang Ji is an obsolete Aristolochia source and must not be substituted for Han Fang Ji by relying on the shared name Fang Ji.',
        promptEs: '¿Qué error de identidad crea el principal peligro de seguridad cuando un producto se vende solamente como “Fang Ji”?',
        optionsEs: ['Confundir Han Fang Ji (Stephania tetrandra) con Guang Fang Ji (Aristolochia fangchi)', 'Confundir Fang Feng con Jing Jie', 'Confundir Mu Gua con Wu Jia Pi', 'Confundir Fu Ling con Zhu Ling'],
        explanationEs: 'Guang Fang Ji es una fuente obsoleta de Aristolochia y no debe sustituir a Han Fang Ji basándose en el nombre compartido Fang Ji.',
    },
    {
        id: 'substitute-xi-jiao',
        prompt: 'A classical formula calls for Xi Jiao (rhinoceros horn). Which response best follows the current safety boundary?',
        options: ['Obtain historical stock because the formula is classical', 'Replace it automatically with any horn product', 'Do not use rhinoceros horn; select a lawful function-matched strategy such as Shui Niu Jiao when appropriate', 'Remove every heat-clearing ingredient from the formula'],
        correct: 2,
        explanation: 'Rhinoceros horn is protected wildlife material whose trade is prohibited. A lawful substitute must be selected according to the intended function rather than copied automatically.',
        promptEs: 'Una fórmula clásica indica Xi Jiao (cuerno de rinoceronte). ¿Qué respuesta respeta mejor el límite actual de seguridad?',
        optionsEs: ['Conseguir existencias antiguas porque la fórmula es clásica', 'Sustituirlo automáticamente por cualquier producto de cuerno', 'No usar cuerno de rinoceronte; elegir una estrategia legal equivalente por función, como Shui Niu Jiao cuando corresponda', 'Eliminar todos los ingredientes que aclaran Calor'],
        explanationEs: 'El cuerno de rinoceronte es material de fauna protegida cuyo comercio está prohibido. El sustituto legal debe elegirse según la función buscada, no copiarse automáticamente.',
    },
    {
        id: 'substitute-pangolin-lactation',
        prompt: 'A historical prescription uses Chuan Shan Jia specifically to promote lactation. Which function-matched legal alternative is identified in the study source?',
        options: ['Wang Bu Liu Xing', 'Zhu Sha', 'Guan Mu Tong', 'Hu Gu'],
        correct: 0,
        explanation: 'Chuan Shan Jia is a protected pangolin product. For the lactation function, the source identifies Wang Bu Liu Xing as a function-specific alternative.',
        promptEs: 'Una prescripción histórica usa Chuan Shan Jia específicamente para promover la lactancia. ¿Qué alternativa legal equivalente por función identifica la fuente de estudio?',
        optionsEs: ['Wang Bu Liu Xing', 'Zhu Sha', 'Guan Mu Tong', 'Hu Gu'],
        explanationEs: 'Chuan Shan Jia es un producto protegido de pangolín. Para la función de lactancia, la fuente identifica Wang Bu Liu Xing como alternativa específica por función.',
    },
    {
        id: 'substitute-pangolin-abscess',
        prompt: 'A historical prescription uses Chuan Shan Jia to pierce a closed abscess. Which source-listed substitute best matches that specific function?',
        options: ['Zao Jiao Ci', 'Wang Bu Liu Xing', 'Niu Huang', 'Shui Niu Jiao'],
        correct: 0,
        explanation: 'Substitution is function-specific: the source identifies Zao Jiao Ci for piercing a closed sore, while Wang Bu Liu Xing is the lactation alternative.',
        promptEs: 'Una prescripción histórica usa Chuan Shan Jia para penetrar un absceso cerrado. ¿Qué sustituto de la fuente corresponde mejor a esa función específica?',
        optionsEs: ['Zao Jiao Ci', 'Wang Bu Liu Xing', 'Niu Huang', 'Shui Niu Jiao'],
        explanationEs: 'La sustitución depende de la función: la fuente identifica Zao Jiao Ci para penetrar una lesión cerrada, mientras Wang Bu Liu Xing corresponde a lactancia.',
    },
    {
        id: 'obsolete-zhu-sha',
        prompt: 'A historical formula contains Zhu Sha for calming the spirit. What is the appropriate modern interpretation?',
        options: ['Retain it because mineral ingredients cannot be absorbed', 'Retain it only for children', 'Exclude it because cinnabar contains mercury and choose a safer strategy', 'Heat it first to remove the mercury risk'],
        correct: 2,
        explanation: 'Zhu Sha is cinnabar, a mercury-containing obsolete substance. Heating can increase danger; historical inclusion does not authorize modern internal use.',
        promptEs: 'Una fórmula histórica contiene Zhu Sha para calmar el Shen. ¿Cuál es la interpretación moderna apropiada?',
        optionsEs: ['Conservarlo porque los minerales no pueden absorberse', 'Conservarlo solamente para niños', 'Excluirlo porque el cinabrio contiene mercurio y elegir una estrategia más segura', 'Calentarlo primero para eliminar el riesgo de mercurio'],
        explanationEs: 'Zhu Sha es cinabrio, una sustancia obsoleta que contiene mercurio. El calor puede aumentar el peligro; su presencia histórica no autoriza el uso interno moderno.',
    },
    {
        id: 'cites-identity',
        prompt: 'Which information is necessary before assigning a wildlife-trade or import status to a medicinal sold under a pinyin name?',
        options: ['The pinyin name alone', 'The retail price and package color', 'Exact scientific species, medicinal part, source and propagation or processing status', 'Whether it appears in a classical formula'],
        correct: 2,
        explanation: 'CITES and import status can depend on exact species, part, source and propagation. A pinyin-only label cannot establish the legal identity.',
        promptEs: '¿Qué información es necesaria antes de asignar un estado de comercio de fauna o importación a un medicinal vendido con nombre pinyin?',
        optionsEs: ['Solamente el nombre pinyin', 'El precio y color del envase', 'Especie científica exacta, parte medicinal, procedencia y estado de propagación o procesamiento', 'Si aparece en una fórmula clásica'],
        explanationEs: 'El estado CITES y de importación puede depender de especie exacta, parte, procedencia y propagación. Una etiqueta solo en pinyin no establece identidad legal.',
    },
    {
        id: 'scope-versus-law',
        prompt: 'A supplied historical handout says an herb was excluded from an examination list. What can be concluded from that statement alone?',
        options: ['The herb is federally illegal', 'The herb is clinically safe', 'Only that the handout reported an exam-scope exclusion; current safety and legality require separate verification', 'The herb may be dispensed without identity testing'],
        correct: 2,
        explanation: 'Exam scope, clinical risk and legal or protected status are separate questions. A historical exclusion list is not a current legal ruling.',
        promptEs: 'Un material histórico suministrado dice que una hierba fue excluida de una lista de examen. ¿Qué puede concluirse solamente de esa afirmación?',
        optionsEs: ['Que la hierba es ilegal a nivel federal', 'Que la hierba es clínicamente segura', 'Solo que el material informó una exclusión del examen; seguridad y legalidad vigentes requieren verificación separada', 'Que puede dispensarse sin comprobar identidad'],
        explanationEs: 'Alcance del examen, riesgo clínico y estado legal o protegido son preguntas separadas. Una lista histórica de exclusión no es un dictamen legal vigente.',
    },
    {
        id: 'ma-huang-product-rule',
        prompt: 'Which statement most accurately describes the US regulatory boundary for Ma Huang?',
        options: ['Every Ephedra plant and every professional-use circumstance are identically illegal', 'Dietary supplements containing ephedrine alkaloids are federally restricted, but the exact product, use and practitioner scope still matter', 'Ma Huang has no federal or state restrictions', 'Its status is determined only by the dose'],
        correct: 1,
        explanation: 'The source warns against converting a product-specific restriction into a universal statement about every botanical or professional-use circumstance.',
        promptEs: '¿Qué afirmación describe con mayor precisión el límite regulatorio de Ma Huang en Estados Unidos?',
        optionsEs: ['Toda planta Ephedra y toda circunstancia de uso profesional son ilegalmente idénticas', 'Los suplementos con alcaloides de efedrina están restringidos federalmente, pero importan el producto exacto, uso y alcance profesional', 'Ma Huang no tiene restricciones federales ni estatales', 'Su estado depende solamente de la dosis'],
        explanationEs: 'La fuente advierte que una restricción específica de producto no debe convertirse en una afirmación universal sobre toda planta o circunstancia profesional.',
    },
    {
        id: 'supplier-policy',
        prompt: 'A supplier states that it does not carry a substance as a raw herb. What does that statement establish?',
        options: ['A universal federal prohibition', 'A private commercial policy that must be labeled separately from government regulation', 'Automatic removal from every Board outline', 'Proof that every processed product is legal'],
        correct: 1,
        explanation: 'A private supplier decision is not by itself a federal ban. Commercial policy and government regulation must be recorded separately.',
        promptEs: 'Un proveedor indica que no vende una sustancia como hierba cruda. ¿Qué establece esa afirmación?',
        optionsEs: ['Una prohibición federal universal', 'Una política comercial privada que debe distinguirse de la regulación gubernamental', 'Eliminación automática de todo temario Board', 'Prueba de que todo producto procesado es legal'],
        explanationEs: 'La decisión privada de un proveedor no constituye por sí sola una prohibición federal. Política comercial y regulación gubernamental deben registrarse por separado.',
    },
    {
        id: 'aristolochia-stop',
        prompt: 'A botanical certificate identifies Aristolochia manshuriensis in a product labeled Guan Mu Tong. What is the correct action?',
        options: ['Dispense it only after honey processing', 'Reduce the dose', 'Reject it; processing or dose reduction does not neutralize aristolochic acid', 'Use it only externally'],
        correct: 2,
        explanation: 'Aristolochia identity is a stop signal. Guan Mu Tong is linked to aristolochic-acid nephropathy and cancer and should not be used.',
        promptEs: 'Un certificado botánico identifica Aristolochia manshuriensis en un producto rotulado Guan Mu Tong. ¿Cuál es la acción correcta?',
        optionsEs: ['Dispensarlo solamente después de procesarlo con miel', 'Reducir la dosis', 'Rechazarlo; procesamiento o reducción de dosis no neutralizan el ácido aristolóquico', 'Usarlo solamente de forma externa'],
        explanationEs: 'La identidad Aristolochia es señal de detenerse. Guan Mu Tong se asocia con nefropatía y cáncer por ácido aristolóquico y no debe usarse.',
    },
    {
        id: 'substitute-xiong-dan',
        prompt: 'Which approach best addresses a historical reference to Xiong Dan (bear bile)?',
        options: ['Source wild bear bile because natural products are stronger', 'Use bovine or synthetic bile alternatives when they lawfully match the intended function', 'Replace it with tiger bone', 'Treat every bitter herb as equivalent'],
        correct: 1,
        explanation: 'Bear bile raises wildlife, legal and ethical restrictions. The source identifies bovine or synthetic bile products as lawful alternatives selected by intended function.',
        promptEs: '¿Qué enfoque responde mejor a una referencia histórica a Xiong Dan (bilis de oso)?',
        optionsEs: ['Obtener bilis de oso silvestre porque lo natural es más fuerte', 'Usar alternativas biliares bovinas o sintéticas cuando correspondan legalmente a la función buscada', 'Sustituirla por hueso de tigre', 'Tratar toda hierba amarga como equivalente'],
        explanationEs: 'La bilis de oso implica restricciones de fauna, legales y éticas. La fuente identifica productos biliares bovinos o sintéticos como alternativas legales según la función.',
    },
    {
        id: 'chapter-nineteen-boundary',
        prompt: 'How many of the 17 obsolete substances in the Chapter 19 safety atlas belong to the current 2026 Appendix A roster?',
        options: ['17', '12', '5', '0'],
        correct: 3,
        explanation: 'None of the 17 Chapter 19 obsolete substances belongs to the current 304-substance Appendix A roster; they remain historical-recognition and safety material.',
        promptEs: '¿Cuántas de las 17 sustancias obsoletas del atlas de seguridad del capítulo 19 pertenecen al listado vigente del Appendix A 2026?',
        optionsEs: ['17', '12', '5', '0'],
        explanationEs: 'Ninguna de las 17 sustancias obsoletas del capítulo 19 pertenece al listado vigente de 304 sustancias del Appendix A; permanecen como material de reconocimiento histórico y seguridad.',
    },
    {
        id: 'quarantine-obsolete',
        prompt: 'An obsolete or prohibited ingredient is discovered in a prepared batch. What is the best immediate response?',
        options: ['Dispense the batch with a warning', 'Dilute the batch', 'Stop dispensing, quarantine it, preserve records and follow regulator or poison-control guidance', 'Remove the ingredient name from the label'],
        correct: 2,
        explanation: 'The safety source directs the practitioner to stop dispensing, quarantine the batch, preserve records and follow applicable regulator or poison-control guidance.',
        promptEs: 'Se descubre un ingrediente obsoleto o prohibido en un lote preparado. ¿Cuál es la mejor respuesta inmediata?',
        optionsEs: ['Dispensar el lote con una advertencia', 'Diluir el lote', 'Detener la dispensación, ponerlo en cuarentena, conservar registros y seguir orientación regulatoria o de toxicología', 'Eliminar el nombre del ingrediente de la etiqueta'],
        explanationEs: 'La fuente de seguridad indica detener la dispensación, poner el lote en cuarentena, conservar registros y seguir la orientación regulatoria o de toxicología aplicable.',
    },
    {
        id: 'protected-hu-gu',
        prompt: 'A historical prescription includes Hu Gu for wandering Wind-Damp pain. Which statement is correct?',
        options: ['Tiger bone may be used if the dose is small', 'Tiger bone is protected wildlife material; use a lawful plant-based strategy matched to the pattern', 'Any animal bone is an equivalent substitute', 'Historical inclusion overrides wildlife law'],
        correct: 1,
        explanation: 'Tiger-bone trade is illegal and fuels poaching. The historical function should be replaced with a lawful strategy using appropriate herbs rather than another wildlife product.',
        promptEs: 'Una prescripción histórica incluye Hu Gu para dolor migratorio por Viento-Humedad. ¿Qué afirmación es correcta?',
        optionsEs: ['Puede usarse hueso de tigre si la dosis es pequeña', 'El hueso de tigre es material de fauna protegida; debe usarse una estrategia vegetal legal según el patrón', 'Cualquier hueso animal es sustituto equivalente', 'La inclusión histórica prevalece sobre la ley de fauna'],
        explanationEs: 'El comercio de hueso de tigre es ilegal y fomenta la caza furtiva. La función histórica debe reemplazarse con una estrategia legal de hierbas, no con otro producto de fauna.',
    },
];
for (const [index, question] of scopeQuestions.entries()) {
    const options = question.options.map((text, optionIndex) => ({
        label: String.fromCharCode(65 + optionIndex),
        text,
    }));
    const optionsEs = question.optionsEs.map((text, optionIndex) => ({
        label: String.fromCharCode(65 + optionIndex),
        text,
    }));
    domains.scope_aliases.push(createCandidate({
        questionId: `RA-SAFE-${String(index + 1).padStart(3, '0')}`,
        domainId: 'scope_aliases',
        prompt: question.prompt,
        options,
        correctLabel: String.fromCharCode(65 + question.correct),
        explanation: question.explanation,
        sourceTitle: 'Obsolete, Prohibited & Protected Substances safety card',
        sourceLocator: `Board-safety item ${index + 1}: ${question.id}`,
        sourceCard: scopeSourceCard,
        reason: 'Source-grounded Board-style decision about identity, scope, substitution or protected materials',
        translationEs: {
            prompt: question.promptEs,
            options: optionsEs,
            explanation: question.explanationEs,
        },
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

const historicalSafetyHerbIdentities = [
    ['Guan Mu Tong', 'Aristolochiae manshuriensis Caulis'],
    ['Guang Fang Ji', 'Aristolochiae Fangchi Radix'],
    ['Qing Mu Xiang', 'Aristolochiae Radix'],
    ['Chuan Shan Jia', 'Manitis Squama'],
    ['Xi Jiao', 'Rhinocerotis Cornu'],
    ['Hu Gu', 'Tigris Os'],
    ['Xiong Dan', 'Ursi Fel'],
    ['Zhu Sha', 'Cinnabaris'],
    ['Xiong Huang', 'Realgar'],
    ['Ma Dou Ling', 'Aristolochiae Fructus'],
    ['Ying Su Ke', 'Papaveris Pericarpium'],
];
const supplementalFormulaIdentities = [
    ['Zhi Zi Chi Tang', 'Gardenia and Prepared Soybean Decoction'],
    ['Jiao Tai Wan', 'Pill to Restore Proper Communication'],
    ['Jie Geng Tang', 'Platycodon Decoction'],
    ['Fo Shou San', "Buddha's Hand Powder"],
    ['Xiang Fu Si Wu Tang', 'Cyperus Four-Substance Decoction'],
    ['Xiang Leng Wan', 'Aucklandia and Sparganium Pill'],
    ['Xiao Ban Xia Tang', 'Minor Pinellia Decoction'],
    ['She Gan Ma Huang Tang', 'Belamcanda and Ephedra Decoction'],
    ['Ting Li Da Zao Xie Fei Tang', 'Descurainia and Jujube Decoction to Drain the Lungs'],
    ['Shen Qi Wan', 'Kidney Qi Pill'],
    ['San Jin Tang', 'Three-Gold Decoction'],
];
const entityRegistry = [
    ...(formulaRoster.formulas || [])
        .filter(formula => formula.name && formula.englishName)
        .map(formula => ({
            kind: 'formula',
            name: formula.name,
            descriptor: formula.englishName,
            normalizedName: normalizeEntityText(formula.name),
            normalizedDescriptor: normalizeEntityText(formula.englishName),
        })),
    ...(formulasStudyGuide.supplementalFormulas || [])
        .filter(formula => formula.name && formula.englishName)
        .map(formula => ({
            kind: 'formula',
            name: formula.name,
            descriptor: formula.englishName,
            normalizedName: normalizeEntityText(formula.name),
            normalizedDescriptor: normalizeEntityText(formula.englishName),
        })),
    ...supplementalFormulaIdentities.map(([name, englishName]) => ({
        kind: 'formula',
        name,
        descriptor: englishName,
        normalizedName: normalizeEntityText(name),
        normalizedDescriptor: normalizeEntityText(englishName),
    })),
    ...(herbProfiles.herbs || [])
        .filter(herb => herb.name && herb.latin)
        .map(herb => ({
            kind: 'herb',
            name: herb.name,
            descriptor: herb.latin,
            normalizedName: normalizeEntityText(herb.name),
            normalizedDescriptor: normalizeEntityText(herb.latin),
        })),
    ...historicalSafetyHerbIdentities.map(([name, pharmaceuticalName]) => ({
        kind: 'herb',
        name,
        descriptor: pharmaceuticalName,
        normalizedName: normalizeEntityText(name),
        normalizedDescriptor: normalizeEntityText(pharmaceuticalName),
    })),
].filter(entity => entity.normalizedName && entity.normalizedDescriptor)
    .sort((left, right) => right.normalizedName.length - left.normalizedName.length
        || (left.kind === 'formula' ? -1 : 1)
        || left.name.localeCompare(right.name));

// Normalize legacy modification shorthand used by the selected master template to
// the exact Appendix A pinyin names before pharmaceutical labels are attached.
const templateTermReplacements = {
    'FM-MOD-481-1': [[/\bSu Zi\b/g, 'Zi Su Zi']],
    'FM-MOD-481-3': [[/\bSu Zi\b/g, 'Zi Su Zi']],
    'FM-MOD-049-01': [[/\bXin Yi Hua\b/g, 'Xin Yi']],
    'FM-MOD-049-03': [[/\bXin Yi Hua\b/g, 'Xin Yi']],
    'FM-MOD-055-02': [[/\bDan Pi\b/g, 'Mu Dan Pi'], [/\bFu Ping\b/g, 'Jing Jie']],
    'FM-MOD-061-05': [[/\bBei Mu\b/g, 'Chuan Bei Mu']],
    'FM-MOD-047-01': [[/\bPao Jiang\b/g, 'Gan Jiang']],
};
const replaceTemplateTerms = (value, replacements) => replacements.reduce(
    (text, [pattern, replacement]) => String(text || '').replace(pattern, replacement),
    value,
);
for (const question of Object.values(domains).flat()) {
    const replacements = templateTermReplacements[question.questionId];
    if (!replacements) {
        continue;
    }
    question.prompt = replaceTemplateTerms(question.prompt, replacements);
    question.options = question.options.map(option => ({
        ...option,
        text: replaceTemplateTerms(option.text, replacements),
    }));
    question.explanation = replaceTemplateTerms(question.explanation, replacements);
    if (question.translations?.es) {
        question.translations.es.prompt = replaceTemplateTerms(question.translations.es.prompt, replacements);
        question.translations.es.options = question.translations.es.options.map(option => ({
            ...option,
            text: replaceTemplateTerms(option.text, replacements),
        }));
        question.translations.es.explanation = replaceTemplateTerms(
            question.translations.es.explanation,
            replacements,
        );
    }
}

for (const domainId of domainIds) {
    for (const question of domains[domainId]) {
        enrichQuestionNomenclature(question, entityRegistry);
    }
}

const officialHerbNames = new Set((herbProfiles.herbs || []).map(herb => normalizeEntityText(herb.name)));
const officialFormulaNames = new Set((formulaRoster.formulas || []).map(formula => normalizeEntityText(formula.name)));
const appendixDNames = new Set([
    'Xi Jiao',
    'Hu Gu',
    'Xiong Dan',
    'Chuan Shan Jia',
    'Guan Mu Tong',
    'Guang Fang Ji',
    'Ma Dou Ling',
    'Zhu Sha',
    'Ying Su Ke',
].map(normalizeEntityText));
const clinicalDomainIds = new Set([
    'formula_differentiation',
    'single_herb',
    'dui_yao',
    'formula_composition',
]);
const scopeAudit = {
    removedQuestions: 0,
    offRosterFormulaQuestions: 0,
    herbsOutsideAppendicesAAndD: 0,
    appendixDInClinicalQuestions: 0,
    legacyIdentificationQuestions: 0,
    removedByDomain: Object.fromEntries(domainIds.map(domainId => [domainId, 0])),
};
for (const domainId of domainIds) {
    domains[domainId] = domains[domainId].filter(question => {
        const formulasOutsideAppendixC = (question.nomenclature?.formulas || [])
            .filter(formula => !officialFormulaNames.has(normalizeEntityText(formula.pinyin)));
        const herbsOutsideGuideline = (question.nomenclature?.herbs || [])
            .filter(herb => {
                const normalizedName = normalizeEntityText(herb.pinyin);
                return !officialHerbNames.has(normalizedName) && !appendixDNames.has(normalizedName);
            });
        const appendixDInClinicalQuestion = clinicalDomainIds.has(domainId)
            && (question.nomenclature?.herbs || [])
                .some(herb => appendixDNames.has(normalizeEntityText(herb.pinyin)));
        const legacyIdentificationQuestion = domainId === 'formula_differentiation'
            && /^\s*IDENTIFICATION\b/i.test(question.prompt || '');
        if (formulasOutsideAppendixC.length === 0
                && herbsOutsideGuideline.length === 0
                && !appendixDInClinicalQuestion
                && !legacyIdentificationQuestion) {
            return true;
        }
        scopeAudit.removedQuestions++;
        scopeAudit.removedByDomain[domainId]++;
        if (formulasOutsideAppendixC.length > 0) {
            scopeAudit.offRosterFormulaQuestions++;
        }
        if (herbsOutsideGuideline.length > 0) {
            scopeAudit.herbsOutsideAppendicesAAndD++;
        }
        if (appendixDInClinicalQuestion) {
            scopeAudit.appendixDInClinicalQuestions++;
        }
        if (legacyIdentificationQuestion) {
            scopeAudit.legacyIdentificationQuestions++;
        }
        return false;
    });
}

const examTemplateVersion = 'herbology-guideline-2026-template-v1';
const examTemplateQuestionIds = {
    formula_differentiation: [
        'FD-001', 'FD-002', 'FD-008', 'FD-010', 'FD-013', 'FD-015', 'FD-016',
        'FD-019', 'FD-020', 'FD-023', 'FD-028', 'FD-031', 'FD-032', 'FD-033',
        'FD-038', 'FD-040', 'FD-041', 'FD-046', 'FD-048', 'FD-049', 'FD-051',
        'FD-052', 'FD-054', 'FD-055', 'FD-057', 'FD-058', 'FD-059', 'FD-063',
    ],
    single_herb: [
        'SH-0020', 'SH-0025', 'SH-0015', 'SH-0016', 'SH-0006', 'SH-0014',
        'SH-0024', 'SH-0007', 'SH-0005', 'SH-0023', 'SH-0002', 'SH-0001',
        'SH-0018', 'SH-0004', 'SH-0013', 'SH-0009', 'SH-0019', 'SH-0003',
        'SH-0021', 'SH-0017', 'SH-0029', 'SH-0008', 'SH-0026', 'SH-0010',
    ],
    dui_yao: [
        'DY-001', 'DY-004', 'DY-011', 'DY-015', 'DY-018', 'DY-020',
        'DY-023', 'DY-026', 'DY-027', 'DY-028', 'DY-034', 'DY-037',
        'DY-039', 'DY-042', 'DY-045', 'DY-046', 'DY-049', 'DY-051',
    ],
    safety_dispensing: [
        'SD-001', 'SD-005', 'SD-006', 'SD-007', 'SD-008',
        'SD-009', 'SD-012', 'SD-013', 'SD-014', 'SD-016',
        'SD-017', 'SD-019', 'SD-021', 'SD-024', 'SD-025',
    ],
    formula_composition: [
        'FM-MOD-481-1', 'FM-MOD-481-3', 'FM-MOD-049-01', 'FM-MOD-049-03',
        'FM-MOD-052-02', 'FM-MOD-055-02', 'FM-MOD-058-05', 'FM-MOD-061-05',
        'FM-MOD-063-01', 'FM-MOD-047-01',
    ],
    scope_aliases: [
        'RA-SAFE-001', 'RA-SAFE-002', 'RA-SAFE-003', 'RA-SAFE-006', 'RA-SAFE-014',
    ],
};
const expectedTemplateWeights = {
    formula_differentiation: 28,
    single_herb: 24,
    dui_yao: 18,
    safety_dispensing: 15,
    formula_composition: 10,
    scope_aliases: 5,
};
const templateQuestionById = new Map(Object.values(domains).flat()
    .map(question => [question.questionId, question]));
const templateDomainQueues = {};
for (const domainId of domainIds) {
    const questionIds = examTemplateQuestionIds[domainId] || [];
    if (questionIds.length !== expectedTemplateWeights[domainId]) {
        throw new Error(`Template ${domainId} must contain ${expectedTemplateWeights[domainId]} questions.`);
    }
    templateDomainQueues[domainId] = questionIds.map((questionId, domainIndex) => {
        const question = templateQuestionById.get(questionId);
        if (!question || question.domainId !== domainId) {
            throw new Error(`Template question ${questionId} is missing from ${domainId}.`);
        }
        if (!question.prompt
                || question.options?.length !== 4
                || !question.correctLabel
                || !question.explanation
                || !question.sourceCard?.url
                || !question.sourceCard?.urlEs
                || !question.translations?.es?.prompt
                || question.translations?.es?.options?.length !== 4
                || !question.translations?.es?.explanation) {
            throw new Error(`Template question ${questionId} is not source-linked and bilingual.`);
        }
        question.examTemplate = {
            version: examTemplateVersion,
            domainPosition: domainIndex + 1,
            globalPosition: 0,
        };
        return question;
    });
}
const templateSequence = [];
const schedulingScores = Object.fromEntries(domainIds.map(domainId => [domainId, 0]));
for (let position = 1; position <= 100; position++) {
    for (const domainId of domainIds) {
        if (templateDomainQueues[domainId].length > 0) {
            schedulingScores[domainId] += expectedTemplateWeights[domainId];
        }
    }
    const selectedDomainId = domainIds
        .filter(domainId => templateDomainQueues[domainId].length > 0)
        .sort((left, right) => schedulingScores[right] - schedulingScores[left]
            || domainIds.indexOf(left) - domainIds.indexOf(right))[0];
    const question = templateDomainQueues[selectedDomainId].shift();
    schedulingScores[selectedDomainId] -= 100;
    question.examTemplate.globalPosition = position;
    templateSequence.push(question.questionId);
}

const nomenclatureSummary = Object.values(domains).flat().reduce((summary, question) => {
    const herbCount = question.nomenclature?.herbs?.length || 0;
    const formulaCount = question.nomenclature?.formulas?.length || 0;
    if (herbCount + formulaCount > 0) {
        summary.questionsWithNomenclature++;
    }
    summary.herbReferences += herbCount;
    summary.formulaReferences += formulaCount;
    return summary;
}, {
    questionsWithNomenclature: 0,
    herbReferences: 0,
    formulaReferences: 0,
});

const classifiedCounts = Object.fromEntries(domainIds.map(domainId => [domainId, domains[domainId].length]));
const practiceReadyCounts = Object.fromEntries(domainIds.map(domainId => [
    domainId,
    domains[domainId].filter(question => question.prompt
        && question.options.length === 4
        && question.correctLabel
        && question.explanation
        && question.source.title
        && question.source.locator
        && question.sourceCard?.url
        && question.sourceCard?.urlEs).length,
]));
const output = {
    version: 1,
    classificationVersion: 'herbology-domain-source-grounded-2026-11-06-v7-guideline-template',
    status: 'practice-ready',
    policy: 'Existing keyed questions and source-grounded generated questions are assigned to one primary study Domain. Herb pinyin is paired with its pharmaceutical name and formula pinyin with its English name in prompts, options and feedback.',
    generatedFrom: {
        legacyKeyedQuestions: {
            path: legacySource,
            sha256: sha256(legacySource),
            count: legacyQuestions.length,
        },
        formulaPracticeQuestions: {
            path: formulaPracticeSource,
            sha256: sha256(formulaPracticeSource),
            count: formulaPracticeData.questions?.length || 0,
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
        nomenclature: nomenclatureSummary,
        guideline2026ScopeAudit: scopeAudit,
    },
    examTemplate: {
        version: examTemplateVersion,
        totalQuestions: templateSequence.length,
        weights: expectedTemplateWeights,
        sequence: templateSequence,
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
            boardSafetyItems: scopeQuestions.length,
            formulaRosterEntries: formulaRoster.total || (formulaRoster.formulas || []).length,
            herbRosterEntries: herbProfiles.total || (herbProfiles.herbs || []).length,
            authoringSourceLabel: `${scopeQuestions.length} Board-style identity, scope, substitution and protected-material decisions`,
        },
    },
    domains,
};

const outputPath = resolveFromRoot('blocks/menu/herbology-domain-candidates.json');
fs.writeFileSync(outputPath, `${JSON.stringify(output, null, 2)}\n`);
process.stdout.write(`${JSON.stringify(output.summary, null, 2)}\n`);
