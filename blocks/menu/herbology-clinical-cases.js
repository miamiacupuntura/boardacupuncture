(function() {
    'use strict';

    const start = () => {
        const quiz = document.querySelector('[data-clinical-cases-quiz]');
        if (!quiz) {
            return;
        }
        const questions = [...quiz.querySelectorAll('[data-clinical-question]')];
        const nextButton = quiz.querySelector('[data-clinical-next]');
        const progress = quiz.querySelector('[data-clinical-progress]');
        const scoreOutput = quiz.querySelector('[data-clinical-score]');
        const progressBar = quiz.querySelector('[data-clinical-progress-bar]');
        const result = quiz.querySelector('[data-clinical-result]');
        const returnLink = quiz.querySelector('[data-clinical-return]');
        let currentIndex = 0;
        let answered = 0;
        let score = 0;
        let complete = false;

        const updateStatus = () => {
            progress.textContent = `${quiz.dataset.questionWord} ${Math.min(currentIndex + 1, questions.length)} `
                + `${quiz.dataset.ofWord} ${questions.length}`;
            scoreOutput.textContent = `${score}/${answered}`;
            progressBar.style.width = `${Math.round((answered / questions.length) * 100)}%`;
        };

        const showQuestion = index => {
            questions.forEach((question, questionIndex) => {
                question.hidden = questionIndex !== index;
            });
            currentIndex = index;
            nextButton.disabled = questions[index].dataset.answered !== '1';
            nextButton.textContent = index === questions.length - 1
                ? quiz.dataset.resultsWord
                : quiz.dataset.nextWord;
            updateStatus();
        };

        const answerQuestion = (question, input) => {
            if (question.dataset.answered === '1' || complete) {
                return;
            }
            question.dataset.answered = '1';
            const correct = input.value === question.dataset.correct;
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

        const finish = () => {
            complete = true;
            questions.forEach(question => {
                question.hidden = true;
            });
            nextButton.hidden = true;
            const percentage = Math.round((score / questions.length) * 100);
            result.textContent = `${score}/${questions.length} · ${percentage}%`;
            result.hidden = false;
            returnLink.hidden = false;
            progress.textContent = quiz.dataset.resultsWord;
            progressBar.style.width = '100%';
        };

        questions.forEach(question => {
            question.dataset.answered = '0';
            question.querySelectorAll('input[type="radio"]').forEach(input => {
                input.addEventListener('change', () => answerQuestion(question, input));
            });
        });
        nextButton.addEventListener('click', () => {
            if (currentIndex < questions.length - 1) {
                showQuestion(currentIndex + 1);
            } else {
                finish();
            }
        });
        quiz.addEventListener('submit', event => event.preventDefault());
        showQuestion(0);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, {once: true});
    } else {
        start();
    }
})();
