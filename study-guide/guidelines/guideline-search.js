(function() {
    'use strict';

    const isSpanish = document.documentElement.lang === 'es';
    const copy = isSpanish ? {
        eyebrow: 'Búsqueda inteligente',
        title: 'Busca en toda la guía',
        description: 'Encuentra hierbas, fórmulas Board, fórmulas de Bensky, patrones, acciones y secciones. Admite nombres sin acentos y pequeños errores.',
        placeholder: 'Ej.: Mu Li San, sudor nocturno, Huang Qi…',
        shortcut: 'Pulsa / para buscar',
        ready: 'Escribe al menos 2 caracteres para ver resultados.',
        loading: 'Cargando el índice completo de fórmulas…',
        results: function(count) { return count === 1 ? '1 resultado' : count + ' resultados'; },
        none: 'No encontré una coincidencia. Prueba menos palabras, otro nombre pinyin o una acción clínica.',
        open: 'Abrir resultado',
        section: 'Sección de la guía',
        herb: 'Hierba · Appendix A',
        boardFormula: 'Fórmula · Dominio Board',
        benskyFormula: 'Fórmula · Bensky',
        table: 'Comparación',
        examples: 'Prueba:',
        suggestions: ['Mu Li San', 'Si Shen Wan', 'Huang Qi', 'sangrado uterino']
    } : {
        eyebrow: 'Smart search',
        title: 'Search the complete guide',
        description: 'Find herbs, Board formulas, Bensky formulas, patterns, actions, and sections. Accent-free names and small spelling errors are supported.',
        placeholder: 'Example: Mu Li San, night sweating, Huang Qi…',
        shortcut: 'Press / to search',
        ready: 'Type at least 2 characters to see results.',
        loading: 'Loading the complete formula index…',
        results: function(count) { return count === 1 ? '1 result' : count + ' results'; },
        none: 'No match found. Try fewer words, another pinyin name, or a clinical action.',
        open: 'Open result',
        section: 'Guide section',
        herb: 'Herb · Appendix A',
        boardFormula: 'Formula · Board Domain',
        benskyFormula: 'Formula · Bensky',
        table: 'Comparison',
        examples: 'Try:',
        suggestions: ['Mu Li San', 'Si Shen Wan', 'Huang Qi', 'uterine bleeding']
    };

    const normalize = function(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9\u3400-\u9fff]+/g, ' ')
            .trim();
    };

    const compact = function(value) {
        return normalize(value).replace(/\s+/g, '');
    };

    const trimText = function(value, limit) {
        const text = String(value || '').replace(/\s+/g, ' ').trim();
        return text.length > limit ? text.slice(0, limit - 1).trimEnd() + '…' : text;
    };

    const editDistance = function(left, right) {
        if (Math.abs(left.length - right.length) > 2) {
            return 3;
        }
        const row = Array.from({length: right.length + 1}, function(_, index) { return index; });
        for (let i = 1; i <= left.length; i++) {
            let previous = row[0];
            row[0] = i;
            for (let j = 1; j <= right.length; j++) {
                const saved = row[j];
                row[j] = Math.min(row[j] + 1, row[j - 1] + 1, previous + (left[i - 1] === right[j - 1] ? 0 : 1));
                previous = saved;
            }
        }
        return row[right.length];
    };

    const hero = document.querySelector('.guide-hero');
    if (!hero) {
        return;
    }

    const search = document.createElement('section');
    search.className = 'guide-smart-search';
    search.setAttribute('aria-labelledby', 'guide-smart-search-title');
    search.innerHTML = [
        '<div class="guide-smart-search__heading">',
        '<div><p class="guide-eyebrow">' + copy.eyebrow + '</p><h2 id="guide-smart-search-title">' + copy.title + '</h2><p>' + copy.description + '</p></div>',
        '<span class="guide-smart-search__shortcut" aria-hidden="true">' + copy.shortcut + '</span>',
        '</div>',
        '<div class="guide-smart-search__field">',
        '<svg aria-hidden="true" viewBox="0 0 24 24"><path d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>',
        '<input id="guide-smart-search-input" type="search" autocomplete="off" spellcheck="false" placeholder="' + copy.placeholder + '" aria-describedby="guide-smart-search-status" aria-controls="guide-smart-search-results">',
        '<button type="button" class="guide-smart-search__clear" aria-label="' + (isSpanish ? 'Borrar búsqueda' : 'Clear search') + '" hidden>×</button>',
        '</div>',
        '<div class="guide-smart-search__examples"><strong>' + copy.examples + '</strong> ' + copy.suggestions.map(function(term) {
            return '<button type="button" data-search-term="' + term + '">' + term + '</button>';
        }).join('') + '</div>',
        '<p id="guide-smart-search-status" class="guide-smart-search__status" role="status" aria-live="polite">' + copy.loading + '</p>',
        '<div id="guide-smart-search-results" class="guide-smart-search__results" role="listbox" hidden></div>'
    ].join('');
    hero.insertAdjacentElement('afterend', search);

    const input = search.querySelector('input');
    const clearButton = search.querySelector('.guide-smart-search__clear');
    const status = search.querySelector('.guide-smart-search__status');
    const results = search.querySelector('.guide-smart-search__results');
    const entries = new Map();
    let activeIndex = -1;

    const addEntry = function(entry) {
        entry.title = trimText(entry.title, 120);
        entry.summary = trimText(entry.summary, 240);
        entry.searchText = normalize([entry.title, entry.summary, entry.extra || ''].join(' '));
        entry.compactText = compact([entry.title, entry.extra || ''].join(' '));
        const key = normalize(entry.title) + '|' + entry.href;
        entries.set(key, entry);
    };

    const ensureId = function(element, prefix, index) {
        if (!element.id) {
            element.id = prefix + '-' + index;
        }
        element.classList.add('guide-search-target');
        return '#' + element.id;
    };

    document.querySelectorAll('.guide-reading-step, .guide-card, .guide-section:not(.guide-section--annex)').forEach(function(element, index) {
        const heading = element.querySelector('h2, h3');
        if (!heading) {
            return;
        }
        addEntry({
            title: heading.textContent,
            summary: element.textContent.replace(heading.textContent, ''),
            href: ensureId(element, 'guide-search-section', index + 1),
            type: copy.section,
            rank: 30
        });
    });

    document.querySelectorAll('.guide-formula-category__list li').forEach(function(element, index) {
        const link = element.querySelector('a');
        if (!link) {
            return;
        }
        const category = element.closest('.guide-formula-category');
        const categoryName = category && category.querySelector('h4') ? category.querySelector('h4').textContent : '';
        addEntry({
            title: link.textContent,
            summary: categoryName,
            extra: element.textContent,
            href: link.getAttribute('href'),
            type: copy.boardFormula,
            rank: 80
        });
    });

    document.querySelectorAll('.guide-herb-roster li').forEach(function(element, index) {
        const name = element.querySelector('strong');
        const latin = element.querySelector('span');
        if (!name) {
            return;
        }
        addEntry({
            title: name.textContent,
            summary: latin ? latin.textContent : '',
            href: ensureId(element, 'guide-herb', index + 1),
            type: copy.herb,
            rank: 70
        });
    });

    document.querySelectorAll('.guide-table tbody tr').forEach(function(element, index) {
        const firstCell = element.querySelector('th, td');
        if (!firstCell || element.closest('.guide-formula-roster')) {
            return;
        }
        addEntry({
            title: firstCell.textContent,
            summary: element.textContent.replace(firstCell.textContent, ''),
            href: ensureId(element, 'guide-comparison', index + 1),
            type: copy.table,
            rank: 20
        });
    });

    const formulaFocus = function(formula) {
        if (formula.categoryKey === 'stabilize-bind-ch9') {
            return 'stabilize-bind-chapter-9';
        }
        return formula.categoryKey;
    };

    const addFormulaData = function(formula, supplemental) {
        const focus = formulaFocus(formula);
        const language = isSpanish ? '&lang=es' : '';
        const source = supplemental
            ? ((isSpanish ? 'Bensky · capítulo ' : 'Bensky · Chapter ') + (formula.categoryKey === 'stabilize-bind-ch9' ? '9' : ''))
            : ((isSpanish ? 'Dominio Board · Fórmula #' : 'Board Domain · Formula #') + formula.number);
        addEntry({
            title: formula.name,
            summary: [formula.englishName, isSpanish ? formula.patternEs : formula.pattern, isSpanish ? formula.actionEs : formula.action].filter(Boolean).join(' · '),
            extra: [formula.kings, formula.ingredients, formula.ingredientsEs, formula.clue, formula.clueEs, formula.group, formula.groupEs].filter(Boolean).join(' '),
            href: '/blocks/myprogress/formulas-study-guide.php?focus=' + encodeURIComponent(focus) + language + '#formula-' + encodeURIComponent(formula.number),
            type: supplemental ? copy.benskyFormula : copy.boardFormula,
            source: source,
            rank: supplemental ? 90 : 100
        });
    };

    const scoreEntry = function(entry, query) {
        const queryNormalized = normalize(query);
        const queryCompact = compact(query);
        const title = normalize(entry.title);
        const titleCompact = compact(entry.title);
        const tokens = queryNormalized.split(' ').filter(Boolean);
        let score = entry.rank;

        if (title === queryNormalized || titleCompact === queryCompact) {
            score += 1000;
        } else if (title.startsWith(queryNormalized) || titleCompact.startsWith(queryCompact)) {
            score += 700;
        } else if (title.includes(queryNormalized) || titleCompact.includes(queryCompact)) {
            score += 500;
        } else if (tokens.every(function(token) { return entry.searchText.includes(token); })) {
            score += 300;
        } else if (queryCompact.length >= 4 && entry.compactText.includes(queryCompact)) {
            score += 260;
        } else {
            const titleWords = title.split(' ');
            const fuzzy = tokens.every(function(token) {
                if (token.length < 3) {
                    return titleWords.includes(token);
                }
                return titleWords.some(function(word) {
                    const limit = token.length >= 7 ? 2 : 1;
                    return editDistance(token, word) <= limit;
                });
            });
            if (!fuzzy) {
                return 0;
            }
            score += 120;
        }

        score += tokens.reduce(function(total, token) {
            return total + (title.includes(token) ? 35 : 0);
        }, 0);
        return score;
    };

    const setActiveResult = function(index) {
        const links = Array.from(results.querySelectorAll('a'));
        links.forEach(function(link) { link.classList.remove('is-active'); });
        if (!links.length) {
            activeIndex = -1;
            return;
        }
        activeIndex = Math.max(0, Math.min(index, links.length - 1));
        links[activeIndex].classList.add('is-active');
        links[activeIndex].scrollIntoView({block: 'nearest'});
    };

    const render = function() {
        const query = input.value.trim();
        clearButton.hidden = !query;
        activeIndex = -1;

        if (normalize(query).length < 2) {
            results.hidden = true;
            results.replaceChildren();
            status.textContent = copy.ready;
            return;
        }

        const matches = Array.from(entries.values())
            .map(function(entry) { return {entry: entry, score: scoreEntry(entry, query)}; })
            .filter(function(match) { return match.score > 0; })
            .sort(function(left, right) { return right.score - left.score || left.entry.title.localeCompare(right.entry.title); })
            .slice(0, 12);

        results.replaceChildren();
        results.hidden = false;
        status.textContent = matches.length ? copy.results(matches.length) : copy.none;
        if (!matches.length) {
            const empty = document.createElement('p');
            empty.className = 'guide-smart-search__empty';
            empty.textContent = copy.none;
            results.appendChild(empty);
            return;
        }

        matches.forEach(function(match, index) {
            const entry = match.entry;
            const link = document.createElement('a');
            link.href = entry.href;
            link.className = 'guide-smart-search__result';
            link.setAttribute('role', 'option');
            link.innerHTML = '<span class="guide-smart-search__result-main"><span class="guide-smart-search__result-type">' + entry.type + '</span><strong>' + entry.title + '</strong><small>' + entry.summary + '</small></span><span class="guide-smart-search__result-action">' + copy.open + ' →</span>';
            link.addEventListener('mouseenter', function() { setActiveResult(index); });
            link.addEventListener('click', function() {
                if (entry.href.charAt(0) === '#') {
                    results.hidden = true;
                }
            });
            results.appendChild(link);
        });
    };

    input.addEventListener('input', render);
    input.addEventListener('keydown', function(event) {
        const links = results.querySelectorAll('a');
        if (event.key === 'ArrowDown' && links.length) {
            event.preventDefault();
            setActiveResult(activeIndex + 1);
        } else if (event.key === 'ArrowUp' && links.length) {
            event.preventDefault();
            setActiveResult(activeIndex <= 0 ? links.length - 1 : activeIndex - 1);
        } else if (event.key === 'Enter' && activeIndex >= 0 && links[activeIndex]) {
            event.preventDefault();
            links[activeIndex].click();
        } else if (event.key === 'Escape') {
            input.value = '';
            render();
            input.blur();
        }
    });

    clearButton.addEventListener('click', function() {
        input.value = '';
        render();
        input.focus();
    });

    search.querySelectorAll('[data-search-term]').forEach(function(button) {
        button.addEventListener('click', function() {
            input.value = button.dataset.searchTerm;
            render();
            input.focus();
        });
    });

    document.addEventListener('keydown', function(event) {
        const target = event.target;
        const isTyping = target && /INPUT|TEXTAREA|SELECT/.test(target.tagName);
        if (event.key === '/' && !isTyping && !event.metaKey && !event.ctrlKey && !event.altKey) {
            event.preventDefault();
            input.focus();
            search.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
    });

    fetch('/blocks/myprogress/formulas-study-guide-data.json', {credentials: 'same-origin'})
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Formula index unavailable');
            }
            return response.json();
        })
        .then(function(data) {
            (data.formulas || []).forEach(function(formula) { addFormulaData(formula, false); });
            (data.supplementalFormulas || []).forEach(function(formula) { addFormulaData(formula, true); });
            render();
        })
        .catch(function() {
            render();
        });

    render();
})();
