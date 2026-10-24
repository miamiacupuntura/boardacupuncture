(function () {
    'use strict';

    const subjects = new Map([
        ['Foundations', 'foundations'],
        ['Acupuncture & Point Location', 'pointlocation'],
        ['Chinese Herbology', 'herbology'],
        ['Biomedicine', 'biomedicine'],
        ['Clean Needle Techniques', 'cleanneedle'],
    ]);

    const actionLabels = new Set(['Modules', 'Simulation', 'Notes', 'Exam Key', 'Games']);

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
            if (!result || result.card.querySelector(':scope .study-guide-dashboard-link')) {
                return;
            }

            const notesAction = result.actions.find(element => normalise(element.textContent) === 'Notes');
            if (notesAction) {
                notesAction.textContent = 'Exam Key';
                notesAction.setAttribute('aria-label', `Open ${subject} exam keys and feedback`);
            }

            const link = document.createElement('a');
            const example = result.actions[0];
            link.className = `${example.className || ''} study-guide-dashboard-link`.trim();
            link.href = `/blocks/myprogress/study-guide.php?area=${encodeURIComponent(area)}`;
            link.textContent = 'Study Guide';
            link.setAttribute('aria-label', `Open ${subject} Study Guide chapters`);

            const lastAction = result.actions[result.actions.length - 1];
            lastAction.insertAdjacentElement('afterend', link);

            if (area === 'herbology' && !result.card.querySelector(':scope .comparisons-dashboard-link')) {
                const comparisons = document.createElement('a');
                comparisons.className = `${example.className || ''} comparisons-dashboard-link`.trim();
                comparisons.href = '/study-guide/comparisons/index.html';
                comparisons.textContent = 'Comparisons';
                comparisons.setAttribute('aria-label', 'Open Comparative Study Notes');
                link.insertAdjacentElement('afterend', comparisons);
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
        @media (max-width: 650px) {
            .continuing-education-dashboard-panel { align-items: stretch; flex-direction: column; margin: 16px 0 20px; }
            .continuing-education-dashboard-link { width: 100%; }
        }
    `;
    document.head.append(style);

    addStudyGuideLinks();
    addContinuingEducationPortal();
    document.addEventListener('DOMContentLoaded', () => { addStudyGuideLinks(); addContinuingEducationPortal(); }, {once: true});

    let refreshTimer;
    const observer = new MutationObserver(() => {
        window.clearTimeout(refreshTimer);
        refreshTimer = window.setTimeout(() => { addStudyGuideLinks(); addContinuingEducationPortal(); }, 80);
    });
    observer.observe(document.documentElement, {childList: true, subtree: true});
})();
