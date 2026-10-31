(function() {
    const params = new URLSearchParams(window.location.search);
    const returnTarget = params.get('return');
    const isSpanish = document.documentElement.lang === 'es';

    const formulaStep = Array.from(document.querySelectorAll('.guide-reading-step')).find(function(step) {
        const eyebrow = step.querySelector('.guide-eyebrow');
        return eyebrow && eyebrow.textContent.includes('Appendix C');
    });
    if (formulaStep) {
        formulaStep.id = 'appendix-c-study';
        if (window.location.hash === '#appendix-c-study') {
            window.requestAnimationFrame(function() {
                formulaStep.scrollIntoView({block: 'start'});
            });
        }
        const formulaContent = formulaStep.querySelector('.guide-reading-step__number + div');
        if (formulaContent && !formulaContent.querySelector('.guide-formula-list-link')) {
            const formulaActions = document.createElement('div');
            formulaActions.className = 'guide-actions guide-actions--section guide-formula-list-link';

            const formulaLink = document.createElement('a');
            formulaLink.className = 'guide-button';
            formulaLink.href = '/blocks/myprogress/formulas-study-guide.php' + (isSpanish ? '?lang=es' : '');
            formulaLink.textContent = isSpanish ? 'Abrir el listado de fórmulas →' : 'Open the formula list →';
            formulaActions.appendChild(formulaLink);
            formulaContent.appendChild(formulaActions);
        }
    }

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
