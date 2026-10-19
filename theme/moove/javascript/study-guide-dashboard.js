(function () {
    'use strict';

    const subjects = new Map([
        ['Foundations', 'foundations'],
        ['Acupuncture & Point Location', 'pointlocation'],
        ['Chinese Herbology', 'herbology'],
        ['Biomedicine', 'biomedicine'],
        ['Clean Needle Techniques', 'cleanneedle'],
    ]);

    const actionLabels = new Set(['Modules', 'Simulation', 'Notes', 'Games']);

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

            const link = document.createElement('a');
            const example = result.actions[0];
            link.className = `${example.className || ''} study-guide-dashboard-link`.trim();
            link.href = `/blocks/myprogress/study-guide.php?area=${encodeURIComponent(area)}`;
            link.textContent = 'Study Guide';
            link.setAttribute('aria-label', `Open ${subject} Study Guide chapters`);

            const lastAction = result.actions[result.actions.length - 1];
            lastAction.insertAdjacentElement('afterend', link);
        });
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
    `;
    document.head.append(style);

    addStudyGuideLinks();
    document.addEventListener('DOMContentLoaded', addStudyGuideLinks, {once: true});

    let refreshTimer;
    const observer = new MutationObserver(() => {
        window.clearTimeout(refreshTimer);
        refreshTimer = window.setTimeout(addStudyGuideLinks, 80);
    });
    observer.observe(document.documentElement, {childList: true, subtree: true});
})();
