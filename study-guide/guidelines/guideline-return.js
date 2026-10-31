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
            const roster = document.createElement('section');
            roster.className = 'guide-formula-roster';
            roster.setAttribute('aria-labelledby', 'guide-formula-roster-title');

            const rosterTitle = document.createElement('h3');
            rosterTitle.id = 'guide-formula-roster-title';
            rosterTitle.textContent = isSpanish
                ? 'Listado de fórmulas del Appendix C'
                : 'Appendix C formula list';
            roster.appendChild(rosterTitle);

            const rosterIntro = document.createElement('p');
            rosterIntro.textContent = isSpanish
                ? 'Las 63 fórmulas están organizadas en las mismas 14 categorías de la guía de estudio. Selecciona una para abrir su ficha.'
                : 'The 63 formulas are organized into the same 14 categories used by the study guide. Select one to open its card.';
            roster.appendChild(rosterIntro);

            const rosterStatus = document.createElement('p');
            rosterStatus.className = 'guide-formula-roster__status';
            rosterStatus.setAttribute('role', 'status');
            rosterStatus.textContent = isSpanish ? 'Cargando listado de fórmulas…' : 'Loading formula list…';
            roster.appendChild(rosterStatus);
            formulaContent.appendChild(roster);

            fetch('/blocks/myprogress/formulas-study-guide-data.json', {credentials: 'same-origin'})
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Formula roster unavailable');
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (!Array.isArray(data.categories) || !Array.isArray(data.formulas)) {
                        throw new Error('Invalid formula roster');
                    }

                    const rosterGrid = document.createElement('div');
                    rosterGrid.className = 'guide-formula-roster__grid';

                    data.categories.forEach(function(category) {
                        const formulas = data.formulas.filter(function(formula) {
                            return formula.categoryKey === category.key;
                        });
                        const categoryCard = document.createElement('article');
                        categoryCard.className = 'guide-formula-category';

                        const categoryHeading = document.createElement('div');
                        categoryHeading.className = 'guide-formula-category__heading';
                        const categoryTitle = document.createElement('h4');
                        categoryTitle.textContent = category.number + '. ' +
                            (isSpanish ? category.nameEs : category.name);
                        const categoryCount = document.createElement('span');
                        categoryCount.textContent = formulas.length + ' ' +
                            (isSpanish ? 'fórmulas' : 'formulas');
                        categoryHeading.appendChild(categoryTitle);
                        categoryHeading.appendChild(categoryCount);
                        categoryCard.appendChild(categoryHeading);

                        const formulaList = document.createElement('ol');
                        formulaList.className = 'guide-formula-category__list';
                        formulas.forEach(function(formula) {
                            const item = document.createElement('li');
                            const link = document.createElement('a');
                            const languageQuery = isSpanish ? '&lang=es' : '';
                            link.href = '/blocks/myprogress/formulas-study-guide.php?focus=' +
                                encodeURIComponent(category.key) + languageQuery + '#formula-' + formula.number;
                            link.textContent = formula.name;
                            item.appendChild(link);
                            if (formula.priority === 'core') {
                                const priority = document.createElement('span');
                                priority.className = 'guide-formula-category__priority';
                                priority.textContent = isSpanish ? 'Prioridad actual' : 'Current priority';
                                item.appendChild(priority);
                            }
                            formulaList.appendChild(item);
                        });
                        categoryCard.appendChild(formulaList);
                        rosterGrid.appendChild(categoryCard);
                    });

                    rosterStatus.remove();
                    roster.appendChild(rosterGrid);
                })
                .catch(function() {
                    rosterStatus.classList.add('guide-formula-roster__status--error');
                    rosterStatus.textContent = isSpanish
                        ? 'No se pudo cargar el listado. Usa el botón siguiente para abrir la guía de fórmulas.'
                        : 'The list could not be loaded. Use the button below to open the Formula Study Guide.';
                });

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
