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
                selectedDescription.textContent = optionText(question, question.dataset.selected || '');
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

        const saveAttempt = async total => {
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
                examtype: 'clinicalcases',
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
            resultMessage.textContent = passed ? quiz.dataset.passedMessage : quiz.dataset.failedMessage;
            resultPanel.classList.toggle('is-passed', passed);
            resultPanel.classList.toggle('is-failed', !passed);
            renderIncorrectAnswers(incorrectQuestions);
            resultPanel.hidden = false;
            returnLink.hidden = false;
            progress.textContent = quiz.dataset.resultsWord;
            scoreOutput.textContent = `${score}/${total}`;
            progressBar.style.width = '100%';
            saveAttempt(total);
            resultPanel.scrollIntoView({block: 'start', behavior: 'smooth'});
        };

        const retryErrors = () => {
            const retryQuestions = activeQuestions.filter(question => question.dataset.lastCorrect !== '1');
            if (retryQuestions.length === 0) {
                return;
            }
            activeQuestions = retryQuestions;
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
            navigation.hidden = false;
            nextButton.hidden = false;
            showQuestion(0);
            quiz.scrollIntoView({block: 'start', behavior: 'smooth'});
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
