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
        explanation: `${pair.title} performs this combined action. Representative formula: ${pair.formulas?.en || 'see the pair profile'}.`,
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
            explanation: `${pair.title} realiza esta acción combinada. Fórmula representativa: ${pair.formulas?.es || 'consulta el perfil del par'}.`,
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
    classificationVersion: 'herbology-domain-source-grounded-2026-11-06-v5-board-scope',
    status: 'practice-ready',
    policy: 'Existing keyed questions and source-grounded generated questions are assigned to one primary study Domain. Complete questions may be used directly in practice exams.',
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
