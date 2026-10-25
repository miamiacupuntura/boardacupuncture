(function () {
    'use strict';

    const label = 'Main Page';
    const siteRoot = window.M && M.cfg && M.cfg.wwwroot
        ? M.cfg.wwwroot.replace(/\/$/, '')
        : '';
    const dashboardUrl = `${siteRoot}/my/`;

    function isMainPageBreadcrumb(link) {
        return link instanceof HTMLAnchorElement &&
            link.closest('.breadcrumb') !== null &&
            link.textContent.replace(/\s+/g, ' ').trim() === label;
    }

    function rewriteMainPageBreadcrumb() {
        document.querySelectorAll('.breadcrumb a').forEach(link => {
            if (isMainPageBreadcrumb(link)) {
                link.href = dashboardUrl;
            }
        });
    }

    document.addEventListener('click', event => {
        const link = event.target.closest('.breadcrumb a');
        if (!isMainPageBreadcrumb(link)) {
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
