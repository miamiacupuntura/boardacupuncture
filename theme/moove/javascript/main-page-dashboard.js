(function () {
    'use strict';

    const dashboardLabels = new Set([
        'Main Page',
        'Foundation Notes',
    ]);
    const siteRoot = window.M && M.cfg && M.cfg.wwwroot
        ? M.cfg.wwwroot.replace(/\/$/, '')
        : '';
    const dashboardUrl = `${siteRoot}/my/`;

    function isDashboardBreadcrumb(link) {
        return link instanceof HTMLAnchorElement &&
            link.closest('.breadcrumb') !== null &&
            dashboardLabels.has(link.textContent.replace(/\s+/g, ' ').trim());
    }

    function rewriteMainPageBreadcrumb() {
        document.querySelectorAll('.breadcrumb a').forEach(link => {
            if (isDashboardBreadcrumb(link)) {
                link.href = dashboardUrl;
            }
        });
    }

    document.addEventListener('click', event => {
        const link = event.target.closest('.breadcrumb a');
        if (!isDashboardBreadcrumb(link)) {
            return;
        }
        event.preventDefault();
        window.location.assign(dashboardUrl);
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', rewriteMainPageBreadcrumb, {once: true});
    } else {
        rewriteMainPageBreadcrumb();
    }
})();
