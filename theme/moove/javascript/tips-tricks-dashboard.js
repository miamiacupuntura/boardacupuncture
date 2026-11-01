(function () {
    'use strict';

    function normalise(value) {
        return String(value || '').replace(/\s+/g, ' ').trim();
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
        text.innerHTML = '<strong>Board Tips &amp; Tricks</strong>'
            + '<span>Fast clues, memory aids, do-not-confuse comparisons and safety reminders from the Study Guide.</span>';

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

        const header = heading.closest('.board-hub__heading');
        (header || heading).insertAdjacentElement('afterend', panel);
    }

    if (!document.querySelector('#tips-tricks-dashboard-styles')) {
        const style = document.createElement('style');
        style.id = 'tips-tricks-dashboard-styles';
        style.textContent = `
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
            @media (max-width: 650px) {
                .tips-tricks-dashboard-panel {
                    align-items: stretch;
                    flex-direction: column;
                    margin: 16px 0 20px;
                }
                .tips-tricks-dashboard-link { width: 100%; }
            }
        `;
        document.head.append(style);
    }

    addTipsTricksPortal();
    document.addEventListener('DOMContentLoaded', addTipsTricksPortal, {once: true});

    let refreshTimer;
    const observer = new MutationObserver(() => {
        window.clearTimeout(refreshTimer);
        refreshTimer = window.setTimeout(addTipsTricksPortal, 80);
    });
    observer.observe(document.documentElement, {childList: true, subtree: true});
})();
