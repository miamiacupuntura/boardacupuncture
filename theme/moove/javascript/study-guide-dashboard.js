(function () {
    'use strict';

    const subjects = new Map([
        ['Foundations', 'foundations'],
        ['Acupuncture & Point Location', 'pointlocation'],
        ['Chinese Herbology', 'herbology'],
        ['Biomedicine', 'biomedicine'],
        ['Clean Needle Techniques', 'cleanneedle'],
    ]);

    const actionLabels = new Set(['Modules', 'Simulation', 'Notes', 'Exam Key', 'Tricky Questions']);

    const boardPracticeLabels = new Map([
        ['Foundations Board Level', 'Foundations Practice'],
        ['Acupuncture & Point Location Board Level', 'Acupuncture Practice'],
        ['Chinese Herbology Board Level', 'Herbology Practice'],
        ['Biomedicine Board Level', 'Biomedicine Practice'],
        ['Clean Needle Techniques Board Level', 'CNT Practice'],
    ]);

    function normalise(value) {
        return String(value || '').replace(/\s+/g, ' ').trim();
    }

    function findSubjectCard(heading) {
        let candidate = heading.parentElement;
        for (let level = 0; candidate && level < 7; level++, candidate = candidate.parentElement) {
            const subjectHeadingCount = [...candidate.querySelectorAll('h1, h2, h3, h4, h5, strong')]
                .filter(element => subjects.has(normalise(element.textContent))).length;
            if (subjectHeadingCount > 1) {
                return null;
            }
            const actions = [...candidate.querySelectorAll('a, button')]
                .filter(element => actionLabels.has(normalise(element.textContent)));
            if (actions.length >= 2) {
                return {card: candidate, actions: actions};
            }
        }
        return null;
    }

    function addStudyGuideLinks() {
        const headings = document.querySelectorAll('h1, h2, h3, h4, h5, strong');
        headings.forEach(heading => {
            const subject = normalise(heading.textContent);
            const area = subjects.get(subject);
            if (!area) {
                return;
            }

            const result = findSubjectCard(heading);
            if (!result) {
                return;
            }

            const notesAction = result.actions.find(element => normalise(element.textContent) === 'Notes');
            if (notesAction) {
                notesAction.textContent = 'Exam Key';
                notesAction.setAttribute('aria-label', `Open ${subject} exam keys and feedback`);
            }

            const example = result.actions[0];
            const boardHeading = `${subject} Board Level`;
            const boardCard = [...document.querySelectorAll('.board-hub__card--board-level')]
                .find(card => {
                    const cardHeading = card.querySelector('h1, h2, h3, h4, h5, strong');
                    return cardHeading && normalise(cardHeading.textContent) === boardHeading;
                });
            const linkTarget = boardCard || result.card;
            const linkActions = linkTarget.querySelector(':scope .board-hub__actions') || linkTarget;
            let link = linkTarget.querySelector(':scope .study-guide-dashboard-link')
                || result.card.querySelector(':scope .study-guide-dashboard-link');
            if (!link) {
                link = document.createElement('a');
                link.className = `${example.className || ''} board-hub__action--guide study-guide-dashboard-link`.trim();
                link.href = `/blocks/myprogress/study-guide.php?area=${encodeURIComponent(area)}`;
                link.textContent = 'Study Guide';
                link.setAttribute('aria-label', `Open ${subject} Study Guide chapters`);
            }
            if (boardCard) {
                if (link.parentElement !== linkActions || link !== linkActions.firstElementChild) {
                    linkActions.prepend(link);
                }
            } else if (link.parentElement !== linkActions || link !== linkActions.lastElementChild) {
                linkActions.append(link);
            }

            if (area === 'herbology' && !result.card.querySelector(':scope .comparisons-dashboard-link')) {
                const comparisons = document.createElement('a');
                comparisons.className = `${example.className || ''} comparisons-dashboard-link`.trim();
                comparisons.href = '/study-guide/comparisons/index.html';
                comparisons.textContent = 'Comparisons';
                comparisons.setAttribute('aria-label', 'Open Comparative Study Notes');
                const schoolActions = result.card.querySelector(':scope .board-hub__actions') || result.card;
                schoolActions.append(comparisons);
            }
        });
    }

    function renameBoardPracticeButtons() {
        document.querySelectorAll('.board-hub__card--board-level').forEach(card => {
            const heading = card.querySelector('h1, h2, h3, h4, h5, strong');
            const label = heading ? boardPracticeLabels.get(normalise(heading.textContent)) : null;
            const action = card.querySelector('.board-hub__action--board-mock');
            if (label && action) {
                action.textContent = label;
                action.setAttribute('aria-label', `Open ${label}`);
            }
        });
    }

    function addContinuingEducationPortal() {
        const heading = [...document.querySelectorAll('h1, h2, h3')]
            .find(element => normalise(element.textContent) === 'Your NCBAHM Board Preparation');
        if (!heading || document.querySelector('.continuing-education-dashboard-link')) {
            return;
        }

        const panel = document.createElement('aside');
        panel.className = 'continuing-education-dashboard-panel';
        panel.setAttribute('aria-label', 'Continuing Education courses');
        const text = document.createElement('div');
        text.innerHTML = '<strong>Continuing Education for licensed acupuncturists</strong><span>Open the separate catalog of Board-approved and active CE courses.</span>';
        const link = document.createElement('a');
        link.className = 'continuing-education-dashboard-link';
        link.href = '/study-guide/continuing-education.html';
        link.textContent = 'Continuing Education Courses';
        panel.append(text, link);

        const tipsTricks = document.querySelector('.tips-tricks-dashboard-panel');
        if (tipsTricks) {
            tipsTricks.insertAdjacentElement('afterend', panel);
            return;
        }
        const boardGuidelines = document.querySelector('.board-guidelines-dashboard-panel');
        if (boardGuidelines) {
            boardGuidelines.insertAdjacentElement('afterend', panel);
            return;
        }
        const description = heading.nextElementSibling;
        (description || heading).insertAdjacentElement('afterend', panel);
    }

    function addBoardGuidelinesPortal() {
        const heading = [...document.querySelectorAll('h1, h2, h3')]
            .find(element => normalise(element.textContent) === 'Your NCBAHM Board Preparation');
        if (!heading || document.querySelector('.board-guidelines-dashboard-link')) {
            return;
        }

        const panel = document.createElement('aside');
        panel.className = 'board-guidelines-dashboard-panel';
        panel.setAttribute('aria-label', 'NCBAHM Board Guidelines');
        const text = document.createElement('div');
        text.innerHTML = '<strong>NCBAHM Board Guidelines</strong><span>Current study scope, official-list boundaries and a separate guideline for each Board.</span>';
        const link = document.createElement('a');
        link.className = 'board-guidelines-dashboard-link';
        link.href = '/study-guide/board-guidelines.html';
        link.textContent = 'Open Board Guidelines';
        panel.append(text, link);

        const description = heading.nextElementSibling;
        (description || heading).insertAdjacentElement('afterend', panel);
    }

    function addTipsTricksPortal() {
        const heading = [...document.querySelectorAll('h1, h2, h3')]
            .find(element => normalise(element.textContent) === 'Your NCBAHM Board Preparation');
        if (!heading || document.querySelector('.tips-tricks-dashboard-link')) {
            return;
        }

        const panel = document.createElement('aside');
        panel.className = 'tips-tricks-dashboard-panel';
        panel.setAttribute('aria-label', 'Board Tips and Tricks');
        const text = document.createElement('div');
        text.innerHTML = '<strong>Board Tips &amp; Tricks</strong><span>Fast clues, memory aids, do-not-confuse comparisons and safety reminders from the Study Guide.</span>';
        const link = document.createElement('a');
        link.className = 'tips-tricks-dashboard-link';
        link.href = '/study-guide/tips-and-tricks.html';
        link.textContent = 'Open Tips & Tricks';
        panel.append(text, link);

        const boardGuidelines = document.querySelector('.board-guidelines-dashboard-panel');
        if (boardGuidelines) {
            boardGuidelines.insertAdjacentElement('afterend', panel);
            return;
        }
        const description = heading.nextElementSibling;
        (description || heading).insertAdjacentElement('afterend', panel);
    }

    function addModuleSchoolPortal() {
        const heading = [...document.querySelectorAll('h1, h2, h3')]
            .find(element => normalise(element.textContent) === 'Your NCBAHM Board Preparation');
        if (!heading || document.querySelector('.module-school-dashboard-link')) {
            return;
        }

        const panel = document.createElement('aside');
        panel.className = 'module-school-dashboard-panel';
        panel.setAttribute('aria-label', 'Module School');
        const text = document.createElement('div');
        text.innerHTML = '<strong>Module School</strong><span>Integrated clinical modules combining patterns, herbs, formulas and acupuncture points.</span>';
        const link = document.createElement('a');
        link.className = 'module-school-dashboard-link';
        link.href = '/blocks/myprogress/module-school.php';
        link.textContent = 'Open Module School';
        panel.append(text, link);

        const continuingEducation = document.querySelector('.continuing-education-dashboard-panel');
        if (continuingEducation) {
            continuingEducation.insertAdjacentElement('afterend', panel);
            return;
        }
        const description = heading.nextElementSibling;
        (description || heading).insertAdjacentElement('afterend', panel);
    }

    const style = document.createElement('style');
    style.textContent = `
        .study-guide-dashboard-link {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            margin: 4px;
            padding: 8px 13px !important;
            border: 0 !important;
            border-radius: 6px !important;
            background: #13705c !important;
            color: #fff !important;
            font-weight: 700;
            text-decoration: none !important;
        }
        .study-guide-dashboard-link:hover,
        .study-guide-dashboard-link:focus {
            background: #0d5a4a !important;
            color: #fff !important;
        }
        .study-guide-dashboard-link:focus-visible {
            outline: 3px solid #185cca;
            outline-offset: 2px;
        }
        .board-hub__card--board-level .study-guide-dashboard-link {
            width: 100%;
            height: 52px;
            min-height: 52px;
            flex: 1 1 100%;
            min-width: 0;
            margin: 0 !important;
            order: 1;
        }
        .board-hub__card--board-level .board-hub__action {
            width: 100%;
            height: 52px;
            min-height: 52px;
            flex: 1 1 100%;
            min-width: 0;
            padding: 10px 9px !important;
            font-size: .82rem;
        }
        .board-hub__card--board-level .board-hub__action--board-mock {
            order: 2;
        }
        .board-hub__card--board-level .board-hub__action--board-quiz {
            order: 3;
        }
        .board-hub__card--board-level .board-hub__action--board-exams {
            order: 4;
        }
        .board-hub__card--board-level .board-hub__action--board-simulation {
            order: 5;
        }
        .board-hub__card:not(.board-hub__card--board-level) .board-hub__action--simulation {
            flex-basis: 100%;
            order: 100;
        }
        .board-hub__card:not(.board-hub__card--board-level) .board-hub__action--games {
            flex: 1 1 calc(50% - 8px);
            min-width: 0;
        }
        .board-hub__card--school-level:not(.board-hub__card--herbology) .board-hub__action--questions {
            flex-basis: 100%;
        }
        .comparisons-dashboard-link {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            margin: 4px;
            padding: 8px 13px !important;
            border: 0 !important;
            border-radius: 6px !important;
            background: #b45309 !important;
            color: #fff !important;
            font-weight: 700;
            text-decoration: none !important;
        }
        .comparisons-dashboard-link:hover,
        .comparisons-dashboard-link:focus {
            background: #92400e !important;
            color: #fff !important;
        }
        .comparisons-dashboard-link:focus-visible {
            outline: 3px solid #185cca;
            outline-offset: 2px;
        }
        .continuing-education-dashboard-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            max-width: 1040px;
            box-sizing: border-box;
            margin: 18px auto 22px;
            padding: 16px 18px;
            border: 1px solid #d4c6e7;
            border-left: 5px solid #7652a5;
            border-radius: 11px;
            background: #faf7fd;
            text-align: left;
        }
        .board-guidelines-dashboard-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            max-width: 1040px;
            box-sizing: border-box;
            margin: 18px auto 22px;
            padding: 16px 18px;
            border: 1px solid #a9d4cb;
            border-left: 5px solid #08756a;
            border-radius: 11px;
            background: #f1faf8;
            text-align: left;
        }
        .board-guidelines-dashboard-panel strong,
        .board-guidelines-dashboard-panel span { display: block; }
        .board-guidelines-dashboard-panel span { margin-top: 3px; color: #526c69; font-size: .9rem; }
        .board-guidelines-dashboard-link {
            display: inline-flex;
            box-sizing: border-box;
            min-height: 44px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border-radius: 8px;
            background: #08756a;
            color: #fff !important;
            font-weight: 800;
            text-decoration: none !important;
        }
        .board-guidelines-dashboard-link:hover,
        .board-guidelines-dashboard-link:focus { background: #055e56; }
        .board-guidelines-dashboard-link:focus-visible { outline: 3px solid #185cca; outline-offset: 2px; }
        .tips-tricks-dashboard-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            max-width: 1040px;
            box-sizing: border-box;
            margin: -4px auto 22px;
            padding: 16px 18px;
            border: 1px solid #efc7a8;
            border-left: 5px solid #f37424;
            border-radius: 11px;
            background: #fff8f2;
            text-align: left;
        }
        .tips-tricks-dashboard-panel strong,
        .tips-tricks-dashboard-panel span { display: block; }
        .tips-tricks-dashboard-panel span { margin-top: 3px; color: #755844; font-size: .9rem; }
        .tips-tricks-dashboard-link {
            display: inline-flex;
            box-sizing: border-box;
            min-height: 44px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border-radius: 8px;
            background: #c9570c;
            color: #fff !important;
            font-weight: 800;
            text-decoration: none !important;
        }
        .tips-tricks-dashboard-link:hover,
        .tips-tricks-dashboard-link:focus { background: #a74408; }
        .tips-tricks-dashboard-link:focus-visible { outline: 3px solid #185cca; outline-offset: 2px; }
        .continuing-education-dashboard-panel strong,
        .continuing-education-dashboard-panel span { display: block; }
        .continuing-education-dashboard-panel span { margin-top: 3px; color: #5e6874; font-size: .9rem; }
        .continuing-education-dashboard-link {
            display: inline-flex;
            box-sizing: border-box;
            min-height: 44px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border-radius: 8px;
            background: #7652a5;
            color: #fff !important;
            font-weight: 800;
            text-decoration: none !important;
        }
        .continuing-education-dashboard-link:hover,
        .continuing-education-dashboard-link:focus { background: #654391; }
        .continuing-education-dashboard-link:focus-visible { outline: 3px solid #185cca; outline-offset: 2px; }
        .module-school-dashboard-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            max-width: 1040px;
            box-sizing: border-box;
            margin: 18px auto 22px;
            padding: 16px 18px;
            border: 1px solid #b9d4e8;
            border-left: 5px solid #176b9a;
            border-radius: 11px;
            background: #f4f9fc;
            text-align: left;
        }
        .module-school-dashboard-panel strong,
        .module-school-dashboard-panel span { display: block; }
        .module-school-dashboard-panel span { margin-top: 3px; color: #52677a; font-size: .9rem; }
        .module-school-dashboard-link {
            display: inline-flex;
            box-sizing: border-box;
            min-height: 44px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border-radius: 8px;
            background: #176b9a;
            color: #fff !important;
            font-weight: 800;
            text-decoration: none !important;
        }
        .module-school-dashboard-link:hover,
        .module-school-dashboard-link:focus { background: #115579; }
        .module-school-dashboard-link:focus-visible { outline: 3px solid #185cca; outline-offset: 2px; }
        @media (max-width: 650px) {
            .continuing-education-dashboard-panel,
            .board-guidelines-dashboard-panel,
            .tips-tricks-dashboard-panel,
            .module-school-dashboard-panel { align-items: stretch; flex-direction: column; margin: 16px 0 20px; }
            .continuing-education-dashboard-link,
            .board-guidelines-dashboard-link,
            .tips-tricks-dashboard-link,
            .module-school-dashboard-link { width: 100%; }
        }
    `;
    document.head.append(style);

    addStudyGuideLinks();
    renameBoardPracticeButtons();
    addBoardGuidelinesPortal();
    addContinuingEducationPortal();
    document.addEventListener('DOMContentLoaded', () => {
        addStudyGuideLinks();
        renameBoardPracticeButtons();
        addBoardGuidelinesPortal();
        addContinuingEducationPortal();
    }, {once: true});

    let refreshTimer;
    const observer = new MutationObserver(() => {
        window.clearTimeout(refreshTimer);
        refreshTimer = window.setTimeout(() => {
            addStudyGuideLinks();
            renameBoardPracticeButtons();
            addBoardGuidelinesPortal();
            addContinuingEducationPortal();
        }, 80);
    });
    observer.observe(document.documentElement, {childList: true, subtree: true});
})();
