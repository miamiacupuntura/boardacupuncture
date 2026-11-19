(() => {
    'use strict';

    const exam = document.getElementById('herbology-mock-exam');
    if (!exam) {
        return;
    }

    const questions = [...exam.querySelectorAll('.herb-board__exam-question')];
    const previous = exam.querySelector('[data-mock-previous]');
    const next = exam.querySelector('[data-mock-next]');
    const progressText = exam.querySelector('[data-mock-progress-text]');
    const progressBar = exam.querySelector('[data-mock-progress-bar]');
    const scoreText = exam.querySelector('[data-mock-score]');
    const result = exam.querySelector('[data-mock-result]');
    const controls = exam.querySelector('[data-mock-controls]');
    const restart = exam.querySelector('[data-mock-restart]');
    let currentIndex = 0;
    let answered = 0;
    let score = 0;

    const updateSummary = () => {
        const percent = answered > 0 ? Math.round((score / answered) * 100) : 0;
        progressText.textContent = `${exam.dataset.questionWord} ${currentIndex + 1} ${exam.dataset.ofWord} ${questions.length}`;
        progressBar.style.width = `${((currentIndex + 1) / questions.length) * 100}%`;
        scoreText.textContent = `${exam.dataset.scoreWord}: ${percent}% (${score}/${answered})`;
        previous.disabled = currentIndex === 0;
        next.textContent = currentIndex === questions.length - 1
            ? exam.dataset.finishWord
            : exam.dataset.nextWord;
    };

    const showQuestion = (index, shouldScroll = true) => {
        currentIndex = Math.max(0, Math.min(index, questions.length - 1));
        questions.forEach((question, questionIndex) => {
            question.hidden = questionIndex !== currentIndex;
        });
        updateSummary();
        if (shouldScroll) {
            questions[currentIndex].scrollIntoView({behavior: 'smooth', block: 'start'});
        }
    };

    const finish = () => {
        questions.forEach(question => { question.hidden = true; });
        const percent = questions.length > 0 ? Math.round((score / questions.length) * 100) : 0;
        result.textContent = `${score} ${exam.dataset.ofWord} ${questions.length} ${exam.dataset.correctWord} · ${percent}%`;
        result.hidden = false;
        controls.hidden = true;
        restart.hidden = false;
        progressText.textContent = exam.dataset.completedWord;
        progressBar.style.width = '100%';
        result.scrollIntoView({behavior: 'smooth', block: 'center'});
    };

    questions.forEach(question => {
        question.hidden = true;
        question.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', () => {
                if (question.dataset.answered === '1') {
                    return;
                }
                question.dataset.answered = '1';
                const correct = input.value === question.dataset.correct;
                answered += 1;
                if (correct) {
                    score += 1;
                    question.classList.add('is-correct');
                } else {
                    question.classList.add('is-incorrect');
                }
                question.querySelectorAll('input[type="radio"]').forEach(option => {
                    option.disabled = true;
                    if (option.value === question.dataset.correct) {
                        option.closest('label')?.classList.add('is-answer');
                    }
                });
                const feedback = question.querySelector('.herb-board__exam-feedback');
                const verdict = feedback.querySelector('.herb-board__exam-verdict');
                verdict.textContent = correct ? exam.dataset.correctFeedback : exam.dataset.incorrectFeedback;
                feedback.hidden = false;
                updateSummary();
            });
        });
    });

    previous.addEventListener('click', () => showQuestion(currentIndex - 1));
    next.addEventListener('click', () => {
        if (currentIndex === questions.length - 1) {
            finish();
        } else {
            showQuestion(currentIndex + 1);
        }
    });
    restart.addEventListener('click', () => window.location.reload());

    showQuestion(0, false);
})();
