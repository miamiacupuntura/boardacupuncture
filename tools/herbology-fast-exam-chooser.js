(function() {
    'use strict';
    const start = () => {
        const initial = new URL(window.location.href);
        const track = initial.searchParams.get('track');
        if (!['simulation', 'exams'].includes(track) || initial.searchParams.get('exam')
            || document.querySelector('[data-simulation-exam],#herbology-domain-exam')) { return; }
        const nav = document.querySelector('.herb-board__simulation-exam-switch');
        if (!nav || nav.dataset.fastChooser === '1') { return; }
        nav.dataset.fastChooser = '1';
        const param = track === 'simulation' ? 'simulation' : 'examset';
        const tabLinks = [...nav.querySelectorAll('a[href]')];
        const numberOf = url => Number(url.searchParams.get(param) || 1);
        const allowed = new Set(tabLinks.map(a => numberOf(new URL(a.href, window.location.href))));
        const status = document.createElement('p'); status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite');
        status.className = 'herb-board__fast-selection-status'; status.hidden = true; nav.insertAdjacentElement('afterend', status);
        const update = (url, push, announce) => {
            const number = numberOf(url);
            if (!allowed.has(number) || url.searchParams.get('track') !== track || url.searchParams.get('exam')) { return false; }
            document.querySelectorAll('.herb-board__simulation-exam-switch a[href],.exam-audit-card a[href]').forEach(a => {
                const target = new URL(a.href, window.location.href);
                if (target.pathname !== initial.pathname || target.searchParams.get('track') !== track) { return; }
                if (numberOf(target) === number) { a.setAttribute('aria-current', 'page'); } else { a.removeAttribute('aria-current'); }
            });
            const heading = document.getElementById('herb-board-simulation-title');
            if (track === 'simulation' && heading) { heading.textContent = heading.textContent.replace(/\b(Exam|Examen)\s+\d+/i, `$1 ${number}`); }
            document.querySelectorAll('.herb-board__exam-minimum strong').forEach(node => {
                node.textContent = node.textContent.replace(/\b(Exam|Examen)\s+\d+/i, `$1 ${number}`);
            });
            document.querySelectorAll('.herb-board__simulation-modes a[href],.herb-board__exam-models a[href]').forEach(a => {
                const target = new URL(a.href, window.location.href);
                if (target.pathname === initial.pathname && target.searchParams.get('track') === track && target.searchParams.get('exam')) {
                    target.searchParams.set(param, String(number)); a.href = target.href;
                }
            });
            // Preserve the selected exam when switching language or opening
            // the on-page history. Never rewrite other exam-selection tabs.
            document.querySelectorAll('a[href]').forEach(a => {
                if (a.closest('.herb-board__simulation-exam-switch,.exam-audit-card,.herb-board__simulation-modes,.herb-board__exam-models')) { return; }
                const target = new URL(a.href, window.location.href);
                if (target.origin !== initial.origin || target.pathname !== initial.pathname || target.searchParams.get('track') !== track || target.searchParams.get('exam')) { return; }
                if (target.searchParams.has('lang') || /herb-board-(?:simulation|exam)-history/.test(target.hash)) {
                    target.searchParams.set(param, String(number)); a.href = target.href;
                }
            });
            if (push && window.location.href !== url.href) { window.history.pushState({herbologyExam: number}, '', url.href); }
            if (announce) {
                const es = (url.searchParams.get('lang') || document.documentElement.lang || '').startsWith('es');
                status.textContent = es ? `Examen ${number} seleccionado. Elige una modalidad para comenzar.` : `Exam ${number} selected. Choose a mode to begin.`;
                status.hidden = false;
            }
            return true;
        };
        document.addEventListener('click', event => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) { return; }
            const link = event.target.closest('.herb-board__simulation-exam-switch a[href],.exam-audit-card a[href]');
            if (!link || link.target === '_blank' || link.hasAttribute('download')) { return; }
            const url = new URL(link.href, window.location.href);
            if (url.origin !== initial.origin || url.pathname !== initial.pathname || !update(url, true, true)) { return; }
            event.preventDefault();
            if (link.closest('.exam-audit-card')) {
                document.querySelector('.herb-board__simulation-modes,.herb-board__exam-models')?.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        });
        window.addEventListener('popstate', () => { update(new URL(window.location.href), false, true); });
        update(initial, false, false);
    };
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', start, {once: true}); } else { start(); }
})();
