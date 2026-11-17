#!/usr/bin/env node

import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';

const scriptDirectory = path.dirname(fileURLToPath(import.meta.url));
const repositoryRoot = path.resolve(scriptDirectory, '..');
const bankPath = path.join(repositoryRoot, 'blocks/menu/herbology-domain-candidates.json');
const profilesPath = path.join(repositoryRoot, 'blocks/myprogress/single-herb-properties.json');
const bank = JSON.parse(fs.readFileSync(bankPath, 'utf8'));
const profilesData = JSON.parse(fs.readFileSync(profilesPath, 'utf8'));
const profiles = (profilesData.herbs || []).filter(profile => profile.name
    && profile.actionSummary?.en
    && profile.indicationSummary?.en);
const profileByName = new Map(profiles.map(profile => [profile.name, profile]));
const profilesByGuideNumber = new Map();

for (const profile of profiles) {
    const guideNumber = Number(profile.guideNumber || 0);
    if (!profilesByGuideNumber.has(guideNumber)) {
        profilesByGuideNumber.set(guideNumber, []);
    }
    profilesByGuideNumber.get(guideNumber).push(profile);
}

function stableNumber(value) {
    return [...String(value)].reduce(
        (total, character) => ((total * 31) + character.charCodeAt(0)) >>> 0,
        17,
    );
}

function optionProfiles(correctProfile, answerPool, seed) {
    const alternatives = [...new Map(answerPool
        .filter(profile => profile.name !== correctProfile.name)
        .map(profile => [profile.name, profile])).values()];
    const selected = [];
    let cursor = stableNumber(seed) % alternatives.length;
    while (selected.length < 3 && selected.length < alternatives.length) {
        const candidate = alternatives[cursor % alternatives.length];
        if (!selected.some(profile => profile.name === candidate.name)) {
            selected.push(candidate);
        }
        cursor += 17;
    }
    const correctPosition = stableNumber(`${seed}-correct`) % 4;
    selected.splice(correctPosition, 0, correctProfile);
    return selected;
}

function profileName(profile) {
    return profile.latin ? `${profile.name} (${profile.latin})` : profile.name;
}

function explanation(correctProfile, choices, language) {
    const isSpanish = language === 'es';
    const action = correctProfile.actionSummary?.[language] || correctProfile.actionSummary.en;
    const indication = correctProfile.indicationSummary?.[language] || correctProfile.indicationSummary.en;
    const temperature = correctProfile.temperature?.[language]
        || correctProfile.temperature?.en
        || (isSpanish ? 'de temperatura registrada' : 'of the recorded temperature');
    const flavor = correctProfile.flavor?.[language]
        || correctProfile.flavor?.en
        || (isSpanish ? 'con el sabor registrado' : 'with the recorded flavor profile');
    const distinctions = choices
        .filter(profile => profile.name !== correctProfile.name)
        .map(profile => {
            const distractorAction = profile.actionSummary?.[language] || profile.actionSummary?.en;
            return `${profileName(profile)}: ${distractorAction}`;
        });
    if (isSpanish) {
        return `${profileName(correctProfile)} es ${temperature} y ${flavor}. ${action} `
            + `El perfil verificado identifica este uso principal: ${indication} `
            + `Por qué no las otras: ${distinctions.join(' ')}`;
    }
    return `${profileName(correctProfile)} is ${temperature} and ${flavor}. ${action} `
        + `The verified profile identifies this principal use: ${indication} `
        + `Why the others are weaker: ${distinctions.join(' ')}`;
}

function correctProfileForQuestion(question) {
    const correctText = question.options?.find(option => option.label === question.correctLabel)?.text || '';
    return profiles.find(profile => correctText === profile.name
        || correctText.startsWith(`${profile.name} (`));
}

let updated = 0;
for (const question of bank.domains?.single_herb || []) {
    const correctProfile = correctProfileForQuestion(question);
    if (!correctProfile) {
        throw new Error(`Cannot resolve the correct herb for ${question.questionId}.`);
    }
    const guideNumber = Number(correctProfile.guideNumber || 0);
    const sameCategory = profilesByGuideNumber.get(guideNumber) || [];
    let choices = optionProfiles(
        correctProfile,
        sameCategory.length >= 4 ? sameCategory : profiles,
        `herb-${guideNumber || correctProfile.name}`,
    );
    let prompt = `A patient presents with ${correctProfile.indicationSummary.en} Which herb best matches this presentation?`;
    let promptEs = `Un paciente presenta ${correctProfile.indicationSummary.es || correctProfile.indicationSummary.en} ¿Qué hierba corresponde mejor a esta presentación?`;

    if (question.questionId === 'SH-0017' && correctProfile.name === 'Gu Ya') {
        choices = ['Shen Qu', 'Mai Ya', 'Gu Ya', 'Ji Nei Jin'].map(name => {
            const profile = profileByName.get(name);
            if (!profile) throw new Error(`Missing required comparison profile: ${name}.`);
            return profile;
        });
        prompt = 'An older adult has chronic weak digestion, poor appetite and mild grain-and-starch retention. '
            + 'There is no marked distension after heavy food or alcohol, no lactation or Liver-Qi clue, and no '
            + 'stubborn mixed-food retention, stones or enuresis. Which herb is the best fit?';
        promptEs = 'Un adulto mayor presenta digestión débil crónica, poco apetito y retención leve de granos y almidones. '
            + 'No hay distensión marcada después de alimentos pesados o alcohol, ninguna pista de lactancia o Qi de Hígado, '
            + 'ni retención persistente de alimentos mixtos, cálculos o enuresis. ¿Qué hierba corresponde mejor?';
        question.difficultyBand = 'analysis';
        question.cognitiveLevel = 'infer';
        question.classification.reason = 'Multi-clue clinical differentiation among four Food Stagnation herbs';
        question.review.notes = 'Practice-ready inference item with same-category distractors and comparative feedback.';
    } else {
        question.difficultyBand = 'application';
        question.cognitiveLevel = 'apply';
        question.classification.reason = 'Clinical selection with same-category distractors from verified herb profiles';
        question.review.notes = 'Practice-ready application item with comparative distractor feedback.';
    }

    question.contentType = 'case';
    question.prompt = prompt;
    question.options = choices.map((profile, index) => ({
        label: String.fromCharCode(65 + index),
        text: profileName(profile),
    }));
    question.correctLabel = question.options.find(
        option => option.text === profileName(correctProfile),
    )?.label;
    question.explanation = explanation(correctProfile, choices, 'en');
    question.translations = {
        es: {
            prompt: promptEs,
            options: question.options,
            explanation: explanation(correctProfile, choices, 'es'),
        },
    };
    const nomenclature = choices.map(profile => ({
        pinyin: profile.name,
        pharmaceuticalName: profile.latin || '',
    }));
    question.nomenclature = {
        herbs: nomenclature,
        formulas: [],
        feedback: {herbs: nomenclature, formulas: []},
    };
    updated++;
}

bank.classificationVersion = 'herbology-domain-source-grounded-2026-11-17-v9-clinical-herb-differentiation';
bank.policy = 'Existing keyed questions and source-grounded questions are assigned to one primary Domain. Curated individual-herb items use clinical presentations, same-category distractors and comparative feedback.';
fs.writeFileSync(bankPath, `${JSON.stringify(bank, null, 2)}\n`);
process.stdout.write(`${JSON.stringify({updated}, null, 2)}\n`);
