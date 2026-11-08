(function() {
    'use strict';

    const start = () => {
        const quiz = document.querySelector('[data-clinical-cases-quiz]');
        if (!quiz) {
            return;
        }

        const questions = [...quiz.querySelectorAll('[data-clinical-question]')];
        const navigation = quiz.querySelector('[data-clinical-navigation]');
        const nextButton = quiz.querySelector('[data-clinical-next]');
        const restartButton = quiz.querySelector('[data-clinical-restart]');
        const finishButton = quiz.querySelector('[data-clinical-finish]');
        const progress = quiz.querySelector('[data-clinical-progress]');
        const scoreOutput = quiz.querySelector('[data-clinical-score]');
        const progressBar = quiz.querySelector('[data-clinical-progress-bar]');
        const resultPanel = quiz.querySelector('[data-clinical-result-panel]');
        const resultScore = quiz.querySelector('[data-clinical-result]');
        const resultOutcome = quiz.querySelector('[data-clinical-outcome]');
        const resultMessage = quiz.querySelector('[data-clinical-result-message]');
        const incorrectReview = quiz.querySelector('[data-clinical-incorrect-review]');
        const incorrectList = quiz.querySelector('[data-clinical-incorrect-list]');
        const retryButton = quiz.querySelector('[data-clinical-retry-errors]');
        const returnLink = quiz.querySelector('[data-clinical-return]');
        const historyLink = quiz.querySelector('[data-clinical-history-link]');
        const saveStatus = quiz.querySelector('[data-clinical-save-status]');
        const history = document.querySelector('[data-clinical-history]');
        const historyEmpty = history?.querySelector('[data-clinical-history-empty]');
        const historyList = history?.querySelector('[data-clinical-history-list]');
        const analysisPanel = document.querySelector('[data-clinical-analysis]');
        const analysisHeadline = analysisPanel?.querySelector('[data-clinical-analysis-headline]');
        const analysisScore = analysisPanel?.querySelector('[data-clinical-analysis-score]');
        const analysisErrors = analysisPanel?.querySelector('[data-clinical-analysis-errors]');
        const analysisPriority = analysisPanel?.querySelector('[data-clinical-analysis-priority]');
        const analysisFindings = analysisPanel?.querySelector('[data-clinical-analysis-findings]');
        const analysisConclusion = analysisPanel?.querySelector('[data-clinical-analysis-conclusion]');
        const passThreshold = Number(quiz.dataset.passThreshold || 70);
        let activeQuestions = [...questions];
        let currentIndex = 0;
        let answered = 0;
        let score = 0;
        let complete = false;
        let attemptStartedAt = Date.now();
        let attemptUid = '';
        let attemptSaved = false;

        const createAttemptUid = () => {
            const random = Math.random().toString(36).slice(2, 14);
            return `clinical${Date.now().toString(36)}${random}`;
        };

        const optionText = (question, label) => {
            const input = [...question.querySelectorAll('input[type="radio"]')]
                .find(option => option.value === label);
            return input?.closest('label')?.querySelector('span')?.textContent?.trim() || label;
        };

        const questionText = question => {
            const legend = question.querySelector('legend');
            return legend?.textContent?.replace(/\s+/g, ' ').trim() || '';
        };

        const updateStatus = () => {
            progress.textContent = `${quiz.dataset.questionWord} ${Math.min(currentIndex + 1, activeQuestions.length)} `
                + `${quiz.dataset.ofWord} ${activeQuestions.length}`;
            scoreOutput.textContent = `${score}/${answered}`;
            finishButton.disabled = answered === 0;
            progressBar.style.width = activeQuestions.length > 0
                ? `${Math.round((answered / activeQuestions.length) * 100)}%`
                : '0%';
        };

        const showQuestion = index => {
            questions.forEach(question => {
                question.hidden = true;
            });
            const question = activeQuestions[index];
            if (!question) {
                return;
            }
            question.hidden = false;
            currentIndex = index;
            nextButton.disabled = question.dataset.answered !== '1';
            nextButton.textContent = index === activeQuestions.length - 1
                ? quiz.dataset.resultsWord
                : quiz.dataset.nextWord;
            updateStatus();
        };

        const answerQuestion = (question, input) => {
            if (question.dataset.answered === '1' || complete || !activeQuestions.includes(question)) {
                return;
            }
            question.dataset.answered = '1';
            question.dataset.selected = input.value;
            const correct = input.value === question.dataset.correct;
            question.dataset.lastCorrect = correct ? '1' : '0';
            question.classList.add(correct ? 'is-correct' : 'is-incorrect');
            question.querySelectorAll('input[type="radio"]').forEach(option => {
                option.disabled = true;
            });
            const feedback = question.querySelector('[data-clinical-feedback]');
            const verdict = question.querySelector('[data-clinical-verdict]');
            verdict.textContent = correct ? quiz.dataset.correctWord : quiz.dataset.incorrectWord;
            feedback.hidden = false;
            answered++;
            if (correct) {
                score++;
            }
            nextButton.disabled = false;
            updateStatus();
        };

        const renderIncorrectAnswers = incorrectQuestions => {
            incorrectList.replaceChildren();
            incorrectQuestions.forEach(question => {
                const item = document.createElement('article');
                const heading = document.createElement('h5');
                const answers = document.createElement('dl');
                const selectedRow = document.createElement('div');
                const selectedTerm = document.createElement('dt');
                const selectedDescription = document.createElement('dd');
                const correctRow = document.createElement('div');
                const correctTerm = document.createElement('dt');
                const correctDescription = document.createElement('dd');

                heading.textContent = questionText(question);
                selectedTerm.textContent = quiz.dataset.yourAnswerWord;
                selectedDescription.textContent = question.dataset.selected
                    ? optionText(question, question.dataset.selected)
                    : quiz.dataset.unansweredWord;
                correctTerm.textContent = quiz.dataset.correctAnswerWord;
                correctDescription.textContent = optionText(question, question.dataset.correct);
                selectedRow.append(selectedTerm, selectedDescription);
                correctRow.append(correctTerm, correctDescription);
                answers.append(selectedRow, correctRow);
                item.append(heading, answers);
                incorrectList.append(item);
            });
            incorrectReview.hidden = incorrectQuestions.length === 0;
            retryButton.hidden = incorrectQuestions.length === 0;
        };

        const addCount = (counts, value) => {
            const key = String(value || '').replace(/\s+/g, ' ').trim();
            if (key) {
                counts.set(key, (counts.get(key) || 0) + 1);
            }
        };

        const topEntry = counts => [...counts.entries()]
            .sort((left, right) => right[1] - left[1] || left[0].localeCompare(right[0]))[0] || null;

        const shortFormulaName = value => String(value || '').split('(', 1)[0].trim();

        const renderAnalysis = (attemptQuestions, total, attemptScore) => {
            if (!analysisPanel || !analysisFindings) {
                return;
            }
            const spanish = quiz.dataset.examLanguage === 'es';
            const incorrect = attemptQuestions.filter(question => question.dataset.lastCorrect !== '1');
            const correct = attemptQuestions.filter(question => question.dataset.lastCorrect === '1');
            const patternErrors = new Map();
            const formulaErrors = new Map();
            const distractors = new Map();
            const patternStrengths = new Map();

            incorrect.forEach(question => {
                addCount(patternErrors, question.dataset.clinicalPattern);
                addCount(formulaErrors, question.dataset.clinicalFormula);
                addCount(
                    distractors,
                    shortFormulaName(optionText(question, question.dataset.selected || ''))
                );
            });
            correct.forEach(question => addCount(patternStrengths, question.dataset.clinicalPattern));

            const topPattern = topEntry(patternErrors);
            const topFormula = topEntry(formulaErrors);
            const topDistractor = topEntry(distractors);
            const topStrength = topEntry(patternStrengths);
            const errorRate = total > 0 ? Math.round((incorrect.length / total) * 100) : 0;
            const priority = errorRate >= 40
                ? (spanish ? 'Alta' : 'High')
                : errorRate >= 20
                    ? (spanish ? 'Enfoque específico' : 'Focused review')
                    : (spanish ? 'Refuerzo' : 'Refinement');

            analysisScore.textContent = String(attemptScore) + '/' + String(total);
            analysisErrors.textContent = String(incorrect.length) + ' · ' + String(errorRate) + '%';
            analysisPriority.textContent = incorrect.length === 0
                ? (spanish ? 'Mantenimiento' : 'Maintenance')
                : priority;
            analysisFindings.replaceChildren();

            const addFinding = text => {
                const item = document.createElement('li');
                item.textContent = text;
                analysisFindings.append(item);
            };

            if (incorrect.length === 0) {
                analysisHeadline.textContent = spanish
                    ? 'No se detectaron dificultades recurrentes: todas las respuestas de este intento fueron correctas.'
                    : 'No recurring difficulty was detected: every answer in this attempt was correct.';
                addFinding(spanish
                    ? 'La selección de patrones y fórmulas fue consistente durante todo el intento.'
                    : 'Pattern and formula selection remained consistent throughout the attempt.');
                analysisConclusion.textContent = spanish
                    ? 'Mantén el dominio con repasos espaciados y casos mezclados de mayor dificultad.'
                    : 'Maintain mastery with spaced review and more difficult mixed cases.';
            } else {
                analysisHeadline.textContent = errorRate >= 40
                    ? (spanish
                        ? 'El principal problema es la diferenciación entre patrones y fórmulas con presentaciones similares.'
                        : 'The main difficulty is differentiating patterns and formulas with similar presentations.')
                    : (spanish
                        ? 'Los errores están concentrados y permiten definir una prioridad de estudio específica.'
                        : 'The errors are concentrated enough to define a focused study priority.');

                if (topPattern) {
                    addFinding(spanish
                        ? 'Patrón con más dificultad: “' + topPattern[0] + '” (' + topPattern[1] + ' errores).'
                        : 'Most difficult pattern: “' + topPattern[0] + '” (' + topPattern[1] + ' errors).');
                }
                if (topFormula) {
                    addFinding(spanish
                        ? 'Fórmula correcta omitida con mayor frecuencia: ' + topFormula[0]
                            + ' (' + topFormula[1] + ' veces).'
                        : 'Most frequently missed correct formula: ' + topFormula[0]
                            + ' (' + topFormula[1] + ' times).');
                }
                if (topDistractor) {
                    addFinding(spanish
                        ? 'Distractor elegido con mayor frecuencia: ' + topDistractor[0]
                            + ' (' + topDistractor[1] + ' veces).'
                        : 'Most frequently selected distractor: ' + topDistractor[0]
                            + ' (' + topDistractor[1] + ' times).');
                }
                if (topStrength) {
                    addFinding(spanish
                        ? 'Área relativamente más sólida: “' + topStrength[0]
                            + '” (' + topStrength[1] + ' respuestas correctas).'
                        : 'Relatively strongest area: “' + topStrength[0]
                            + '” (' + topStrength[1] + ' correct answers).');
                }

                if (topFormula && topDistractor && topFormula[0] !== topDistractor[0]) {
                    analysisConclusion.textContent = spanish
                        ? 'Prioriza la diferenciación entre ' + topFormula[0] + ' y ' + topDistractor[0]
                            + '. Comienza por los signos decisivos del patrón'
                            + (topPattern ? ' “' + topPattern[0] + '”.' : '.')
                        : 'Prioritize differentiating ' + topFormula[0] + ' from ' + topDistractor[0]
                            + '. Begin with the decisive pattern signs'
                            + (topPattern ? ' for “' + topPattern[0] + '.”' : '.');
                } else if (topFormula) {
                    analysisConclusion.textContent = spanish
                        ? 'Repasa las indicaciones, signos decisivos y diferenciales de ' + topFormula[0]
                            + ' antes de repetir únicamente los errores.'
                        : 'Review the indications, decisive signs, and differentials for ' + topFormula[0]
                            + ' before retrying only the missed cases.';
                } else {
                    analysisConclusion.textContent = spanish
                        ? 'Repasa primero la identificación del patrón y luego vuelve a relacionarlo con la fórmula.'
                        : 'Review pattern identification first, then reconnect each pattern to its formula.';
                }
            }
            analysisPanel.hidden = false;
        };

        const addHistoryValue = (article, label, value) => {
            const item = document.createElement('div');
            const term = document.createElement('span');
            const description = document.createElement('strong');
            term.textContent = label;
            description.textContent = value;
            item.append(term, description);
            article.append(item);
        };

        const updateHistory = attempt => {
            if (!history || !historyList || !attempt) {
                return;
            }
            const article = document.createElement('article');
            const completed = Boolean(attempt.completed);
            const passed = completed && Boolean(attempt.passed);
            const attemptKind = Number(attempt.total) < questions.length
                ? history.dataset.retryAttemptWord
                : history.dataset.fullAttemptWord;
            article.setAttribute('role', 'listitem');
            article.className = completed ? (passed ? 'is-passed' : 'is-failed') : 'is-incomplete';
            addHistoryValue(article, history.dataset.dateWord, attempt.completedlabel);
            addHistoryValue(article, history.dataset.attemptWord, attemptKind);
            addHistoryValue(
                article,
                history.dataset.resultWord,
                String(attempt.score) + '/' + String(attempt.total) + ' · '
                    + String(attempt.percentage) + '%'
            );
            addHistoryValue(
                article,
                history.dataset.statusWord,
                completed
                    ? (passed ? history.dataset.passedWord : history.dataset.failedWord)
                    : history.dataset.incompleteWord
            );
            addHistoryValue(article, history.dataset.durationWord, attempt.duration);
            historyList.prepend(article);
            while (historyList.children.length > 10) {
                historyList.lastElementChild.remove();
            }
            historyList.hidden = false;
            if (historyEmpty) {
                historyEmpty.hidden = true;
            }
        };

        const saveAttempt = async (total, examType = 'clinicalcases') => {
            if (attemptSaved || !quiz.dataset.historyUrl) {
                return;
            }
            const savingUid = attemptUid;
            saveStatus.textContent = quiz.dataset.historySaving;
            saveStatus.classList.remove('is-saved', 'is-error');
            saveStatus.hidden = false;
            const body = new URLSearchParams({
                sesskey: quiz.dataset.sesskey,
                attemptuid: savingUid,
                examtype: examType,
                score: String(score),
                total: String(total),
                duration: String(Math.max(0, Math.round((Date.now() - attemptStartedAt) / 1000))),
                language: quiz.dataset.examLanguage,
            });
            try {
                const response = await fetch(quiz.dataset.historyUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    keepalive: true,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                    body,
                });
                const payload = await response.json();
                if (!response.ok || !payload.success) {
                    throw new Error('Clinical exam history save failed.');
                }
                if (savingUid === attemptUid) {
                    attemptSaved = true;
                    saveStatus.textContent = quiz.dataset.historySaved;
                    saveStatus.classList.add('is-saved');
                    updateHistory(payload.attempt);
                    historyLink.hidden = false;
                }
            } catch (error) {
                if (savingUid === attemptUid) {
                    saveStatus.textContent = quiz.dataset.historyError;
                    saveStatus.classList.add('is-error');
                }
            }
        };

        const finish = () => {
            complete = true;
            questions.forEach(question => {
                question.hidden = true;
            });
            navigation.hidden = true;
            const total = activeQuestions.length;
            const percentage = total > 0 ? Math.round((score / total) * 100) : 0;
            const passed = percentage >= passThreshold;
            const incorrectQuestions = activeQuestions.filter(question => question.dataset.lastCorrect !== '1');

            resultScore.textContent = `${score}/${total} · ${percentage}%`;
            resultOutcome.textContent = passed ? quiz.dataset.passedWord : quiz.dataset.failedWord;
            const completionMessage = passed ? quiz.dataset.passedMessage : quiz.dataset.failedMessage;
            resultMessage.textContent = completionMessage + ' ' + String(answered) + '/'
                + String(total) + ' ' + quiz.dataset.answeredWord + '.';
            resultPanel.classList.toggle('is-passed', passed);
            resultPanel.classList.toggle('is-failed', !passed);
            renderIncorrectAnswers(incorrectQuestions);
            renderAnalysis(activeQuestions, total, score);
            resultPanel.hidden = false;
            returnLink.hidden = false;
            progress.textContent = quiz.dataset.resultsWord;
            scoreOutput.textContent = `${score}/${total}`;
            progressBar.style.width = '100%';
            saveAttempt(total);
            resultPanel.scrollIntoView({block: 'start', behavior: 'smooth'});
        };

        const startAttempt = attemptQuestions => {
            activeQuestions = [...attemptQuestions];
            activeQuestions.forEach(question => {
                question.dataset.answered = '0';
                question.dataset.lastCorrect = '0';
                delete question.dataset.selected;
                question.classList.remove('is-correct', 'is-incorrect');
                question.querySelectorAll('input[type="radio"]').forEach(input => {
                    input.checked = false;
                    input.disabled = false;
                });
                const feedback = question.querySelector('[data-clinical-feedback]');
                const verdict = question.querySelector('[data-clinical-verdict]');
                feedback.hidden = true;
                verdict.textContent = '';
            });
            currentIndex = 0;
            answered = 0;
            score = 0;
            complete = false;
            attemptStartedAt = Date.now();
            attemptUid = createAttemptUid();
            attemptSaved = false;
            saveStatus.hidden = true;
            historyLink.hidden = true;
            resultPanel.hidden = true;
            if (analysisPanel) {
                analysisPanel.hidden = true;
            }
            navigation.hidden = false;
            nextButton.hidden = false;
            showQuestion(0);
            quiz.scrollIntoView({block: 'start', behavior: 'smooth'});
        };

        const retryErrors = () => {
            const retryQuestions = activeQuestions.filter(question => question.dataset.lastCorrect !== '1');
            if (retryQuestions.length > 0) {
                startAttempt(retryQuestions);
            }
        };

        const restartExam = async () => {
            if (!window.confirm(quiz.dataset.restartConfirm)) {
                return;
            }
            restartButton.disabled = true;
            await saveAttempt(activeQuestions.length, 'clinicalincomplete');
            startAttempt(questions);
            restartButton.disabled = false;
        };

        const finishExamEarly = () => {
            if (complete || answered === 0) {
                return;
            }
            if (window.confirm(quiz.dataset.finishConfirm)) {
                finish();
            }
        };

        questions.forEach(question => {
            question.dataset.answered = '0';
            question.dataset.lastCorrect = '0';
            question.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', () => answerQuestion(question, input));
            });
        });
        nextButton.addEventListener('click', () => {
            if (currentIndex < activeQuestions.length - 1) {
                showQuestion(currentIndex + 1);
            } else {
                finish();
            }
        });
        retryButton.addEventListener('click', retryErrors);
        restartButton.addEventListener('click', restartExam);
        finishButton.addEventListener('click', finishExamEarly);
        quiz.addEventListener('submit', event => event.preventDefault());

        attemptUid = createAttemptUid();
        showQuestion(0);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, {once: true});
    } else {
        start();
    }
})();
