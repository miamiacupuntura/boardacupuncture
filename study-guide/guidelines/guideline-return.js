(function() {
    const params = new URLSearchParams(window.location.search);
    const returnTarget = params.get('return');
    if (!returnTarget) {
        return;
    }

    let returnUrl;
    try {
        returnUrl = new URL(returnTarget, window.location.origin);
    } catch (error) {
        return;
    }

    if (returnUrl.origin !== window.location.origin ||
            !returnUrl.pathname.startsWith('/blocks/myprogress/')) {
        return;
    }

    const actions = document.querySelector('.guide-actions');
    if (!actions) {
        return;
    }

    const isSpanish = document.documentElement.lang === 'es';
    const backLink = document.createElement('a');
    backLink.className = 'guide-button guide-button--outline';
    backLink.href = returnUrl.href;
    backLink.textContent = isSpanish ? '← Volver a la guía de estudio' : '← Back to Study Guide';

    const dashboardLink = Array.from(actions.querySelectorAll('a')).find(function(link) {
        return link.getAttribute('href') === '/my/';
    });
    actions.insertBefore(backLink, dashboardLink || null);

    const languageLink = Array.from(actions.querySelectorAll('a')).find(function(link) {
        return link.getAttribute('hreflang') || /English|Español/.test(link.textContent);
    });
    if (languageLink) {
        const languageUrl = new URL(languageLink.href, window.location.href);
        languageUrl.searchParams.set('return', returnUrl.href);
        languageLink.href = languageUrl.href;
    }
})();
