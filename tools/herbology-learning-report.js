(function(root) {
    'use strict';
    const percent = row => row && row.total > 0 ? Math.round(row.correct * 1000 / row.total) / 10 : null;
    const comparable = (history, report, topic = '', scope = 'same') => history.filter(x => scope === 'all' || x.group === report.group)
        .sort((a, b) => a.completed - b.completed || a.id.localeCompare(b.id))
        .map(x => ({...x, metric: topic ? x.topics[topic] : x.overall})).filter(x => x.metric && x.metric.total > 0);
    if (typeof module !== 'undefined' && module.exports) { module.exports = {percent, comparable}; }
    if (!root.document) { return; }
    const init = config => {
        const boot = () => {
            if (document.querySelector('[data-hlr-root]')) { return; }
            const es = config.lang === 'es';
            const t = (a, b) => es ? a : b;
            const topicName = key => config.topics[key]?.[es ? 0 : 1] || key;
            const modeName = mode => ({linear: t('Lineal', 'Linear'), adaptive: t('Adaptativo', 'Adaptive'), officialadaptive: t('Blueprint NCBAHM', 'NCBAHM blueprint')})[mode] || mode;
            const el = (tag, text, cls) => { const node = document.createElement(tag); if (text !== undefined) { node.textContent = String(text); } if (cls) { node.className = cls; } return node; };
            const api = async (action, values = {}) => {
                const body = new URLSearchParams({sesskey: config.sesskey, action, ...values});
                const response = await fetch(config.url, {method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'}, body});
                const data = await response.json();
                if (!response.ok || !data.success) { throw new Error('Report request failed'); }
                return data;
            };
            const style = el('style');
            style.textContent = '.hlr{margin:22px 0;padding:22px;border:1px solid #bdd4de;border-top:4px solid #008f87;border-radius:12px;background:#fff;color:#173d50}.hlr h3{margin-top:22px}.hlr table{width:100%;border-collapse:collapse;margin:12px 0}.hlr th,.hlr td{padding:8px;text-align:left;border-bottom:1px solid #d9e4e9}.hlr-scroll{overflow:auto}.hlr-kpis{display:flex;gap:18px;flex-wrap:wrap;font-size:1.12rem}.hlr-bar{display:flex;height:18px;background:#eef2f4;border-radius:4px;overflow:hidden;margin:5px 0 12px}.hlr-good{background:#008b7f}.hlr-bad{background:#c85b2a}.hlr-blank{background:#85929d}.hlr small,.hlr-note{color:#52677a;line-height:1.5}.hlr button,.hlr select{padding:8px 12px;margin:6px;border:1px solid #9ab9c7;border-radius:6px;background:#f5fbfa;color:#075b59;cursor:pointer}.hlr figure{margin:18px 0}.hlr svg{display:block;width:100%;max-width:760px;height:auto}.hlr details{padding:12px;border:1px solid #d7e4e8;border-radius:7px;margin:10px 0}.hlr summary{cursor:pointer;font-weight:600}.hlr-feedback{white-space:pre-wrap}.hlr-status{font-weight:600}.hlr [hidden]{display:none!important}@media print{.hlr button,.hlr select{display:none}.hlr details{break-inside:avoid}}';
            document.head.append(style);
            const panel = el('section', undefined, 'hlr'); panel.dataset.hlrRoot = '1'; panel.setAttribute('aria-live', 'polite');
            const exam = document.querySelector('[data-simulation-exam],#herbology-domain-exam');
            const result = exam?.querySelector('[data-simulation-result],#herbology-domain-exam-result');
            const locale = es ? 'es' : 'en';
            const date = stamp => new Date(stamp * 1000).toLocaleString(locale);
            const table = (headers, rows) => {
                const wrap = el('div', undefined, 'hlr-scroll'), tab = el('table'), head = el('thead'), tr = el('tr');
                headers.forEach(label => { const th = el('th', label); th.scope = 'col'; tr.append(th); }); head.append(tr); tab.append(head);
                const body = el('tbody'); rows.forEach(row => { const line = el('tr'); row.forEach(value => line.append(el('td', value))); body.append(line); }); tab.append(body); wrap.append(tab); return wrap;
            };
            const render = (report, history) => {
                panel.replaceChildren(); panel.hidden = false;
                panel.append(el('h2', t('Tu informe de aprendizaje', 'Your learning report')),
                    el('p', `${t('Examen', 'Exam')} ${report.number} · ${t('Versión', 'Version')} ${report.version} · ${modeName(report.mode)} · ${(report.lang || config.lang).toUpperCase()} · ${date(report.completed)}`));
                const kpis = el('div', undefined, 'hlr-kpis'), o = report.overall;
                [t(`Aciertos: ${o.correct}/${o.total} (${percent(o)}%)`, `Correct: ${o.correct}/${o.total} (${percent(o)}%)`),
                    t(`Errores: ${o.wrong}`, `Incorrect: ${o.wrong}`), t(`Sin responder: ${o.unanswered}`, `Unanswered: ${o.unanswered}`)].forEach(x => kpis.append(el('strong', x)));
                panel.append(kpis, el('p', t('Informe guardado en tu cuenta.', 'Report saved in your account.'), 'hlr-status'));
                const topics = Object.entries(report.topics).sort((a, b) => b[1].wrong - a[1].wrong || b[1].total - a[1].total);
                panel.append(el('h3', t('Qué necesitas repasar', 'What to review')));
                const summary = el('ul');
                topics.filter(([, x]) => x.wrong > 0).forEach(([key, x]) => summary.append(el('li', t(`Has fallado ${x.wrong} de ${x.total} preguntas de ${topicName(key)}.`, `You missed ${x.wrong} of ${x.total} questions on ${topicName(key)}.`))));
                if (!summary.children.length) { summary.append(el('li', t('No hubo respuestas incorrectas.', 'No answers were incorrect.'))); }
                panel.append(summary);
                if (o.unanswered) { panel.append(el('p', t(`Además, dejaste ${o.unanswered} preguntas sin responder; se muestran por separado.`, `You also left ${o.unanswered} questions unanswered; they are shown separately.`))); }
                panel.append(el('p', t('Una pregunta puede evaluar varios temas. Las cifras por tema no se suman para obtener el total del examen.', 'A question can assess several topics. Topic counts do not add up to the exam total.'), 'hlr-note'));
                const bars = el('figure'); bars.append(el('figcaption', t('Resultados por tema: aciertos, errores y sin responder', 'Results by topic: correct, incorrect and unanswered')));
                const legend = el('p'); [t('Aciertos', 'Correct'), t('Errores', 'Incorrect'), t('Sin responder', 'Unanswered')].forEach((label, i) => { const item = el('span', label + '  '); item.style.borderLeft = '10px solid ' + ['#008b7f', '#c85b2a', '#85929d'][i]; item.style.paddingLeft = '5px'; item.style.marginRight = '10px'; legend.append(item); }); bars.append(legend);
                topics.forEach(([key, x]) => {
                    bars.append(el('strong', `${topicName(key)} · ${percent(x)}% (${x.correct}/${x.total})`));
                    const bar = el('div', undefined, 'hlr-bar'); bar.setAttribute('aria-hidden', 'true');
                    ['correct', 'wrong', 'unanswered'].forEach((state, i) => { const segment = el('span', undefined, ['hlr-good', 'hlr-bad', 'hlr-blank'][i]); segment.style.width = `${x[state] * 100 / x.total}%`; bar.append(segment); }); bars.append(bar);
                }); panel.append(bars, table([t('Tema', 'Topic'), t('Aciertos', 'Correct'), t('Errores', 'Incorrect'), t('Sin responder', 'Unanswered'), t('Total', 'Total'), '%'], topics.map(([key, x]) => [topicName(key), x.correct, x.wrong, x.unanswered, x.total, percent(x)])));
                const domainNames = {
                    formula_differentiation: t('Selección de fórmulas', 'Formula selection'), single_herb: t('Hierbas individuales', 'Individual herbs'), dui_yao: 'Dui Yao',
                    safety_dispensing: t('Seguridad y dispensación', 'Safety and dispensing'), formula_composition: t('Composición de fórmulas', 'Formula composition'), scope_aliases: t('Nombres y sustituciones', 'Names and substitutions')
                };
                panel.append(el('h3', t('Resultados por dominio', 'Results by domain')), table([t('Dominio', 'Domain'), t('Aciertos', 'Correct'), t('Errores', 'Incorrect'), t('Sin responder', 'Unanswered'), '%'], Object.entries(report.domains).map(([key, x]) => [domainNames[key] || key, `${x.correct}/${x.total}`, x.wrong, x.unanswered, percent(x)])));
                panel.append(el('h3', t('Tu curva de aprendizaje', 'Your learning curve')));
                const select = el('select'); select.setAttribute('aria-label', t('Tema de la curva', 'Learning curve topic'));
                const all = el('option', t('Resultado global', 'Overall result')); all.value = ''; select.append(all);
                topics.forEach(([key]) => { const option = el('option', topicName(key)); option.value = key; select.append(option); });
                const scope = el('select'); scope.setAttribute('aria-label', t('Intentos que se comparan', 'Attempts to compare'));
                [['all', t('Entre exámenes (orientativo)', 'Across exams (indicative)')], ['same', t('Mismo examen y versión', 'Same exam and version')]].forEach(([value, label]) => { const option = el('option', label); option.value = value; scope.append(option); });
                const chart = el('div'); panel.append(select, scope, chart);
                const draw = () => {
                    chart.replaceChildren();
                    const points = comparable(history, report, select.value, scope.value);
                    chart.append(el('p', scope.value === 'all'
                        ? t('Hasta 50 intentos recientes entre exámenes. Las preguntas y su dificultad pueden cambiar: interpreta cada porcentaje junto con su número de preguntas.', 'Up to 50 recent attempts across exams. Questions and their difficulty can change: read each percentage together with its question count.')
                        : t('Se compara el mismo examen, versión, modalidad y conjunto de preguntas, hasta 50 informes recientes.', 'The same exam, version, mode and question set are compared, up to 50 recent reports.'), 'hlr-note'));
                    if (points.length < 2) { chart.append(el('p', t('Este es tu primer punto comparable. La curva se completará al realizar nuevos intentos.', 'This is your first comparable point. More attempts will build the curve.'))); }
                    if (!points.length) { chart.append(el('p', t('Sin preguntas de este tema en los intentos disponibles.', 'No questions on this topic in the available attempts.'))); return; }
                    const ns = 'http://www.w3.org/2000/svg'; const svg = document.createElementNS(ns, 'svg'); svg.setAttribute('viewBox', '0 0 660 230'); svg.setAttribute('aria-hidden', 'true');
                    const shape = (tag, attrs, text) => { const n = document.createElementNS(ns, tag); Object.entries(attrs).forEach(([k, v]) => n.setAttribute(k, v)); if (text !== undefined) { n.textContent = text; } svg.append(n); return n; };
                    [0, 25, 50, 75, 100].forEach(value => { const y = 185 - value * 1.6; shape('line', {x1: 40, y1: y, x2: 630, y2: y, stroke: '#d7e4e8'}); shape('text', {x: 3, y: y + 4, fill: '#52677a', 'font-size': 12}, `${value}%`); });
                    const coords = points.map((p, i) => [points.length === 1 ? 335 : 45 + i * 580 / (points.length - 1), 185 - percent(p.metric) * 1.6]);
                    if (coords.length > 1) { shape('polyline', {points: coords.map(p => p.join(',')).join(' '), fill: 'none', stroke: '#007d76', 'stroke-width': 3}); }
                    coords.forEach(([x, y], i) => { const dot = shape('circle', {cx: x, cy: y, r: 5, fill: '#007d76'}); const title = document.createElementNS(ns, 'title'); title.textContent = `${date(points[i].completed)}: ${percent(points[i].metric)}% (${points[i].metric.correct}/${points[i].metric.total})`; dot.append(title); });
                    shape('text', {x: 230, y: 220, fill: '#52677a', 'font-size': 12}, t('Intentos en orden cronológico', 'Attempts in chronological order'));
                    chart.append(svg, table([t('Fecha', 'Date'), t('Examen / versión', 'Exam / version'), t('Aciertos', 'Correct'), '%'], points.map(p => [date(p.completed), `${p.number} / ${p.version} · ${modeName(p.mode)}`, `${p.metric.correct}/${p.metric.total}`, percent(p.metric)])));
                }; select.addEventListener('change', draw); scope.addEventListener('change', draw); draw();
                panel.append(el('p', t('El seguimiento por tema comienza con esta actualización. Los intentos anteriores conservan su nota global, pero no se inventan detalles que no se guardaron.', 'Topic tracking starts with this update. Older attempts retain their overall score; unavailable detail is not reconstructed.'), 'hlr-note'));
                panel.append(el('h3', t('Revisión pregunta por pregunta', 'Question-by-question review')));
                const filter = el('select'); filter.setAttribute('aria-label', t('Filtrar revisión', 'Filter question review'));
                [['issues', t('Errores y sin responder', 'Incorrect and unanswered')], ['all', t('Todas las preguntas', 'All questions')], ...topics.map(([key]) => [key, topicName(key)])].forEach(([value, label]) => { const option = el('option', label); option.value = value; filter.append(option); });
                const review = el('div'); panel.append(filter, review);
                const showReview = () => {
                    review.replaceChildren();
                    report.items.filter(q => filter.value === 'all' || (filter.value === 'issues' ? q.state !== 'correct' : q.topics.includes(filter.value))).sort((a, b) => (a.position ?? Infinity) - (b.position ?? Infinity)).forEach(q => {
                        const detail = el('details'), state = q.state === 'correct' ? t('Acierto', 'Correct') : q.state === 'wrong' ? t('Error', 'Incorrect') : t('Sin responder', 'Unanswered');
                        detail.append(el('summary', `${q.position === null ? t('Sin presentar', 'Not presented') : t('Pregunta', 'Question') + ' ' + q.position} · ${state} · ${q.topics.map(topicName).join(', ')}`), el('p', q.prompt));
                        const answer = label => q.options.find(o => o.label === label)?.text || '';
                        detail.append(el('p', `${t('Tu respuesta', 'Your answer')}: ${q.selected ? q.selected + '. ' + answer(q.selected) : t('Sin responder', 'Unanswered')}`), el('p', `${t('Respuesta correcta', 'Correct answer')}: ${q.correct}. ${answer(q.correct)}`), el('p', q.explanation, 'hlr-feedback'));
                        review.append(detail);
                    });
                    if (!review.children.length) { review.append(el('p', t('No hay preguntas en este filtro.', 'No questions match this filter.'))); }
                }; filter.addEventListener('change', showReview); showReview();
                const print = el('button', t('Imprimir informe', 'Print report')); print.type = 'button'; print.addEventListener('click', () => window.print()); panel.append(print);
            };
            if (config.active && exam && result) {
                exam.insertAdjacentElement('afterend', panel); panel.hidden = true;
                const nodes = [...exam.querySelectorAll('.herb-board__simulation-question,.herb-board__exam-question')];
                if (nodes.length !== config.items.length) { panel.hidden = false; panel.textContent = t('No se pudo vincular el informe con este examen.', 'The report could not be linked to this exam.'); return; }
                const adaptive = ['adaptive', 'officialadaptive'].includes(config.mode), positions = new Map();
                const capturePositions = () => { if (adaptive) { nodes.forEach(node => { const n = Number(node.dataset?.displayNumber || node.dataset?.displayPosition); if (!node.hidden && n >= 1 && n <= nodes.length) { positions.set(node, n); } }); } };
                capturePositions();
                new MutationObserver(capturePositions).observe(exam, {attributes: true, subtree: true, attributeFilter: ['hidden']});
                let ready = Promise.resolve(config.token), generation = 0, visible = !result.hidden;
                const finish = () => {
                    const seq = generation;
                    const answers = nodes.map((node, i) => ({id: config.items[i].id, answer: node.querySelector('input[type="radio"]:checked')?.value || '', position: adaptive ? (positions.get(node) ?? null) : Number(node.dataset?.displayNumber || i + 1)}));
                    panel.hidden = false; panel.replaceChildren(el('p', t('Preparando y guardando tu informe detallado…', 'Preparing and saving your detailed report…')));
                    const pending = ready;
                    const send = async () => {
                        try { const token = await pending; const data = await api('submit', {token, answers: JSON.stringify(answers)}); if (seq === generation) { render(data.report, data.history); } }
                        catch (error) { if (seq !== generation) { return; } panel.replaceChildren(el('p', t('No se pudo guardar el informe detallado. Tu resultado global sigue visible. Puedes reintentar.', 'The detailed report could not be saved. Your overall result remains visible. You can retry.'))); const retry = el('button', t('Reintentar', 'Retry')); retry.type = 'button'; retry.addEventListener('click', () => { retry.disabled = true; void send(); }); panel.append(retry); }
                    }; void send();
                };
                new MutationObserver(() => {
                    const now = !result.hidden;
                    if (now && !visible) { finish(); }
                    if (!now && visible) { generation++; positions.clear(); capturePositions(); panel.hidden = true; ready = ready.then(token => api('begin', {token})).then(x => x.token); ready.catch(() => {}); }
                    visible = now;
                }).observe(result, {attributes: true, attributeFilter: ['hidden']});
                if (visible) { finish(); }
            } else {
                (document.querySelector('.herb-board') || document.querySelector('main') || document.body).append(panel);
                panel.append(el('h2', t('Mis informes detallados', 'My detailed reports')));
                void api('history').then(data => {
                    if (!data.history.length) { panel.append(el('p', t('Al terminar tu próximo examen recibirás aquí el detalle por tema y comenzarás tu curva de aprendizaje.', 'Complete your next exam to receive a topic breakdown and start your learning curve.'))); return; }
                    data.history.slice(0, 10).forEach(row => { const button = el('button', `${t('Examen', 'Exam')} ${row.number} · ${date(row.completed)} · ${percent(row.overall)}%`); button.type = 'button'; button.addEventListener('click', () => { void api('load', {id: row.id}).then(data => render(data.report, data.history)).catch(() => { button.textContent = t('No se pudo abrir. Reintentar.', 'Unable to open. Retry.'); }); }); panel.append(button); });
                }).catch(() => panel.append(el('p', t('No se pudo cargar el historial detallado. Recarga para reintentar.', 'Detailed history could not be loaded. Reload to retry.'))));
            }
        };
        if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot, {once: true}); } else { boot(); }
    };
    root.HerbologyLearning = {init};
})(typeof window === 'undefined' ? globalThis : window);
