(function() {
    'use strict';

    const start = () => {
        const exam = document.querySelector('[data-simulation-exam]');
        if (!exam) {
            return;
        }

        const questions = [...exam.querySelectorAll('.herb-board__simulation-question')];
        const mode = exam.dataset.mode || 'linear';
        const clockButton = exam.querySelector('[data-simulation-clock]');
        const timeOutput = exam.querySelector('[data-simulation-time]');
        const counter = exam.querySelector('[data-simulation-counter]');
        const previousButton = exam.querySelector('[data-simulation-previous]');
        const nextButton = exam.querySelector('[data-simulation-next]');
        const flagButton = exam.querySelector('[data-simulation-flag]');
        const reviewButton = exam.querySelector('[data-simulation-review]');
        const endButton = exam.querySelector('[data-simulation-end]');
        const controls = exam.querySelector('[data-simulation-controls]');
        const reviewPanel = exam.querySelector('[data-simulation-review-panel]');
        const reviewSummary = exam.querySelector('[data-simulation-review-summary]');
        const reviewGrid = exam.querySelector('[data-simulation-review-grid]');
        const result = exam.querySelector('[data-simulation-result]');
        const resultScore = exam.querySelector('[data-simulation-score]');
        const resultMessage = exam.querySelector('[data-simulation-result-message]');
        const domainResults = exam.querySelector('[data-simulation-domain-results]');
        const difficultyRank = {foundation: 0, application: 1, analysis: 2};
        const originalNextText = nextButton.textContent;
        const startedAt = Date.now();
        const deadline = startedAt + (Number(exam.dataset.duration) * 1000);
        let timerId = null;
        let timeVisible = true;
        let finished = false;
        let current = null;
        let linearIndex = 0;
        let adaptiveHistory = [];
        let adaptiveRemaining = [...questions];
        let reviewOpen = false;

        const selectedValue = question => question.querySelector('input[type="radio"]:checked')?.value || '';
        const isAnswered = question => selectedValue(question) !== '';
        const isCorrect = question => selectedValue(question) === question.dataset.correct;

        const formatTime = totalSeconds => {
            const safe = Math.max(0, totalSeconds);
            const hours = Math.floor(safe / 3600);
            const minutes = Math.floor((safe % 3600) / 60);
            const seconds = safe % 60;
            return [hours, minutes, seconds].map(value => String(value).padStart(2, '0')).join(':');
        };

        const tick = () => {
            if (finished) {
                return;
            }
            const remaining = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
            timeOutput.textContent = timeVisible ? formatTime(remaining) : '--:--:--';
            clockButton.classList.toggle('is-warning', remaining <= 300);
            if (remaining === 0) {
                finishExam(true);
            }
        };

        const updateCounter = () => {
            const position = current ? Number(current.dataset.displayPosition || 1) : 1;
            counter.textContent = `${position} ${exam.dataset.ofWord} ${questions.length}`;
        };

        const updateFlag = () => {
            if (!flagButton || mode === 'adaptive' || !current) {
                return;
            }
            const flagged = current.dataset.flagged === '1';
            flagButton.classList.toggle('is-active', flagged);
            flagButton.textContent = flagged
                ? `⚑ ${exam.dataset.flaggedWord}`
                : `⚑ ${exam.dataset.flaggedWord === 'Marcada' ? 'Marcar para revisar' : 'Flag for Review'}`;
            flagButton.setAttribute('aria-pressed', flagged ? 'true' : 'false');
        };

        const showQuestion = question => {
            reviewOpen = false;
            reviewPanel.hidden = true;
            questions.forEach(item => {
                item.hidden = item !== question;
            });
            current = question;
            current.querySelector('[data-simulation-number]').textContent = current.dataset.displayPosition;
            updateCounter();
            updateFlag();
            nextButton.textContent = originalNextText;
            if (mode === 'linear') {
                linearIndex = questions.indexOf(question);
                previousButton.disabled = linearIndex === 0;
            }
            current.querySelector('input[type="radio"]:checked')?.focus();
        };

        const takeAdaptiveQuestion = () => {
            if (adaptiveRemaining.length === 0) {
                return null;
            }
            if (!current || !isAnswered(current)) {
                return adaptiveRemaining.shift();
            }
            const currentRank = difficultyRank[current.dataset.difficulty] ?? 1;
            const target = isCorrect(current) ? Math.min(2, currentRank + 1) : Math.max(0, currentRank - 1);
            let index = adaptiveRemaining.findIndex(question =>
                (difficultyRank[question.dataset.difficulty] ?? 1) === target
            );
            if (index < 0) {
                index = 0;
            }
            return adaptiveRemaining.splice(index, 1)[0];
        };

        const lockAdaptiveQuestion = question => {
            question.querySelectorAll('input[type="radio"]').forEach(input => {
                input.disabled = true;
            });
            question.dataset.locked = '1';
        };

        const openReview = () => {
            reviewOpen = true;
            questions.forEach(question => {
                question.hidden = true;
            });
            reviewPanel.hidden = false;
            const answered = questions.filter(isAnswered).length;
            const flagged = questions.filter(question => question.dataset.flagged === '1').length;
            const answeredSummary = `${answered} ${exam.dataset.answeredWord.toLowerCase()}`;
            const unansweredSummary = `${questions.length - answered} ${exam.dataset.unansweredWord.toLowerCase()}`;
            const flaggedSummary = `${flagged} ${exam.dataset.flaggedWord.toLowerCase()}`;
            reviewSummary.textContent = `${answeredSummary} · ${unansweredSummary} · ${flaggedSummary}`;
            reviewGrid.replaceChildren();
            questions.forEach((question, index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = String(index + 1);
                button.className = isAnswered(question) ? 'is-answered' : 'is-unanswered';
                if (question.dataset.flagged === '1') {
                    button.classList.add('is-flagged');
                }
                const answerStatus = isAnswered(question)
                    ? exam.dataset.answeredWord
                    : exam.dataset.unansweredWord;
                button.setAttribute('aria-label', `${exam.dataset.questionWord} ${index + 1}: ${answerStatus}`);
                button.addEventListener('click', () => showQuestion(question));
                reviewGrid.append(button);
            });
            previousButton.disabled = true;
            nextButton.textContent = exam.dataset.questionWord === 'Pregunta' ? 'Volver a la pregunta' : 'Return to Question';
            reviewPanel.querySelector('h3')?.focus();
        };

        const finishExam = expired => {
            if (finished) {
                return;
            }
            finished = true;
            clearInterval(timerId);
            questions.forEach(question => {
                question.hidden = true;
                question.querySelectorAll('input').forEach(input => {
                    input.disabled = true;
                });
            });
            reviewPanel.hidden = true;
            controls.hidden = true;
            const score = questions.filter(isCorrect).length;
            const answered = questions.filter(isAnswered).length;
            const percentage = Math.round((score / questions.length) * 100);
            resultScore.textContent = `${score}/${questions.length} · ${percentage}%`;
            resultMessage.textContent = expired
                ? exam.dataset.timeExpired
                : `${answered} ${exam.dataset.ofWord} ${questions.length} ${exam.dataset.answeredWord.toLowerCase()}.`;
            const domains = new Map();
            questions.forEach(question => {
                const id = question.dataset.domain;
                if (!domains.has(id)) {
                    domains.set(id, {
                        number: Number(question.dataset.domainNumber),
                        name: question.dataset.domainName,
                        total: 0,
                        correct: 0,
                    });
                }
                const domain = domains.get(id);
                domain.total++;
                if (isCorrect(question)) {
                    domain.correct++;
                }
            });
            domainResults.replaceChildren();
            [...domains.values()].sort((left, right) => left.number - right.number).forEach(domain => {
                const row = document.createElement('div');
                const label = document.createElement('span');
                const value = document.createElement('strong');
                const domainWord = exam.dataset.questionWord === 'Pregunta' ? 'Dominio' : 'Domain';
                label.textContent = `${domainWord} ${domain.number} · ${domain.name}`;
                value.textContent = `${domain.correct}/${domain.total}`;
                row.append(label, value);
                domainResults.append(row);
            });
            result.hidden = false;
            result.scrollIntoView({block: 'start', behavior: 'smooth'});
        };

        questions.forEach((question, index) => {
            question.dataset.displayPosition = String(index + 1);
            question.dataset.flagged = '0';
        });

        clockButton.addEventListener('click', () => {
            timeVisible = !timeVisible;
            clockButton.classList.toggle('is-hidden', !timeVisible);
            tick();
        });

        flagButton?.addEventListener('click', () => {
            current.dataset.flagged = current.dataset.flagged === '1' ? '0' : '1';
            updateFlag();
        });

        reviewButton?.addEventListener('click', openReview);

        previousButton?.addEventListener('click', () => {
            if (mode === 'linear' && linearIndex > 0) {
                showQuestion(questions[linearIndex - 1]);
            }
        });

        nextButton.addEventListener('click', () => {
            if (reviewOpen) {
                showQuestion(current || questions[0]);
                return;
            }
            if (mode === 'adaptive') {
                if (current) {
                    lockAdaptiveQuestion(current);
                }
                const next = takeAdaptiveQuestion();
                if (!next) {
                    finishExam(false);
                    return;
                }
                next.dataset.displayPosition = String(adaptiveHistory.length + 1);
                adaptiveHistory.push(next);
                showQuestion(next);
                return;
            }
            if (linearIndex < questions.length - 1) {
                showQuestion(questions[linearIndex + 1]);
            } else {
                openReview();
            }
        });

        endButton.addEventListener('click', () => {
            // eslint-disable-next-line no-alert
            if (window.confirm(exam.dataset.submitConfirm)) {
                finishExam(false);
            }
        });

        exam.addEventListener('submit', event => event.preventDefault());

        if (mode === 'adaptive') {
            const first = takeAdaptiveQuestion();
            first.dataset.displayPosition = '1';
            adaptiveHistory.push(first);
            showQuestion(first);
        } else {
            showQuestion(questions[0]);
        }
        tick();
        timerId = window.setInterval(tick, 1000);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, {once: true});
    } else {
        start();
    }
})();
