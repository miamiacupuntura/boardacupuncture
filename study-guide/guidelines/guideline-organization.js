(function() {
    'use strict';

    const panels = Array.from(document.querySelectorAll('details.guide-organized-panel'));
    if (!panels.length) {
        return;
    }

    const openForTarget = function(target) {
        if (!target) {
            return;
        }
        if (target.matches('details.guide-organized-panel')) {
            target.open = true;
        }
        let parent = target.parentElement;
        while (parent) {
            if (parent.matches('details.guide-organized-panel')) {
                parent.open = true;
            }
            parent = parent.parentElement;
        }
    };

    const openCurrentHash = function() {
        if (!window.location.hash) {
            return;
        }
        let target;
        try {
            target = document.querySelector(window.location.hash);
        } catch (error) {
            return;
        }
        openForTarget(target);
    };

    document.addEventListener('click', function(event) {
        const control = event.target.closest('[data-guide-panels]');
        if (control) {
            const group = control.dataset.guidePanels;
            panels.forEach(function(panel) {
                panel.open = group === 'close' ? false : panel.dataset.guidePanelGroup === group;
            });
            if (group !== 'close') {
                const first = panels.find(function(panel) { return panel.dataset.guidePanelGroup === group; });
                if (first) {
                    first.scrollIntoView({behavior: 'smooth', block: 'start'});
                }
            }
            return;
        }

        const link = event.target.closest('a[href*="#"]');
        if (!link) {
            return;
        }
        let url;
        try {
            url = new URL(link.href, window.location.href);
        } catch (error) {
            return;
        }
        if (url.origin !== window.location.origin || url.pathname !== window.location.pathname || !url.hash) {
            return;
        }
        let target;
        try {
            target = document.querySelector(url.hash);
        } catch (error) {
            return;
        }
        openForTarget(target);
    });

    document.addEventListener('beforematch', function(event) {
        openForTarget(event.target);
    });
    window.addEventListener('hashchange', openCurrentHash);
    openCurrentHash();
})();
