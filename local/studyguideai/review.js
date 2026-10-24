/* Study Guide illustration review: all decisions are also validated by the server. */
(function() {
    'use strict';
    function initialise(root) {
        if (root.dataset.initialised) { return; }
        root.dataset.initialised = 'true';
        const config = JSON.parse(root.dataset.config);
        const text = config.strings;
        const gallery = root.querySelector('[data-sg-gallery]');
        const controls = root.querySelector('[data-sg-controls]');
        const reviews = root.querySelector('[data-sg-review]');
        const status = root.querySelector('[data-sg-status]');
        const audit = root.querySelector('[data-sg-audit]');
        let images = JSON.parse(root.dataset.images);
        let busy = false;
        function element(tag, content) {
            const el = document.createElement(tag);
            if (content !== undefined) { el.textContent = content; }
            return el;
        }
        function button(label, callback) {
            const el = element('button', label);
            el.type = 'button';
            el.addEventListener('click', callback);
            return el;
        }
        function disable(value) {
            busy = value;
            root.querySelectorAll('button').forEach(el => {
                el.disabled = value || el.dataset.previewBlocked === 'true';
            });
        }
        async function request(action, extra) {
            if (busy) { return; }
            disable(true);
            status.textContent = '';
            try {
                const params = new URLSearchParams(Object.assign({
                    action, cmid: config.cmid, topic: config.topic, objective: config.objective, sesskey: config.sesskey
                }, extra || {}));
                const response = await fetch(config.api, {
                    method: 'POST', credentials: 'same-origin',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: params.toString()
                });
                const data = await response.json();
                if (!response.ok || !data.success) { throw new Error(data.error || text.error); }
                if (data.audit) {
                    audit.replaceChildren();
                    audit.append(element('h4', text.audit));
                    const entries = element('ol');
                    data.audit.forEach(entry => {
                        const snapshot = JSON.parse(entry.snapshot);
                        const label = text['audit_' + entry.action] || entry.action;
                        entries.append(element('li', [label, snapshot.title || '', entry.actor,
                            new Date(entry.timecreated * 1000).toLocaleString()].filter(Boolean).join(' — ')));
                    });
                    audit.append(entries);
                    audit.hidden = false;
                } else {
                    images = data.images;
                    render();
                }
            } catch (error) {
                status.textContent = error.message || text.error;
                // A stale revision never retries approval automatically. Refresh the list explicitly.
            } finally {
                disable(false);
            }
        }
        function render() {
            gallery.replaceChildren();
            controls.replaceChildren();
            reviews.replaceChildren();
            const current = images.find(item => item.status === 'approved');
            if (current && current.url) {
                const figure = element('figure');
                const img = element('img');
                img.src = current.url;
                img.alt = current.alttext;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                figure.append(img, element('figcaption', current.title));
                if (config.manage) {
                    figure.append(button(text.withdraw, () => {
                        if (confirm(text.confirmwithdraw)) {
                            request('withdraw', {imageid: current.id, revision: current.revision});
                        }
                    }));
                }
                gallery.append(figure);
            } else if (config.approve || config.generate) {
                gallery.append(element('p', text.nocurrent));
            }
            if (config.generate) {
                controls.append(button(text.generate, () => {
                    if (confirm(text.confirmcost)) { request('generate', {confirmed: 1}); }
                }));
            }
            if (config.approve || config.generate || config.manage) {
                controls.append(button('Refresh', () => request('list')));
            }
            if (config.manage) { controls.append(button(text.audit, () => request('audit'))); }
            images.filter(item => item.status === 'pending').forEach(item => {
                if (!config.approve) { return; }
                const form = element('div');
                const img = element('img');
                let previewReady = false;
                let approvalButton;
                img.addEventListener('load', () => {
                    previewReady = true;
                    if (approvalButton) {
                        approvalButton.dataset.previewBlocked = 'false';
                        approvalButton.disabled = busy;
                    }
                });
                img.addEventListener('error', () => {
                    previewReady = false;
                    status.textContent = text.previewerror;
                    if (approvalButton) {
                        approvalButton.dataset.previewBlocked = 'true';
                        approvalButton.disabled = true;
                    }
                });
                img.src = item.url;
                img.alt = item.alttext;
                img.style.maxWidth = '100%';
                const title = element('input');
                title.type = 'text'; title.value = item.title; title.maxLength = 255;
                const alt = element('textarea');
                alt.value = item.alttext; alt.maxLength = 1000;
                const titleLabel = element('label', text.title + ' '); titleLabel.append(title);
                const altLabel = element('label', text.alttext + ' '); altLabel.append(alt);
                const reviewed = element('input'); reviewed.type = 'checkbox';
                const reviewLabel = element('label', text.review + ' '); reviewLabel.append(reviewed);
                form.append(img, titleLabel, altLabel, reviewLabel);
                const action = current ? 'replace' : 'approve';
                if (!current || config.manage) {
                    approvalButton = button(current ? text.replace : text.approve, () => {
                        if (!previewReady || !reviewed.checked || !title.value.trim() || !alt.value.trim()) {
                            status.textContent = text.review;
                            return;
                        }
                        if (confirm(text.confirmapprove)) {
                            request(action, {imageid: item.id, revision: item.revision, title: title.value,
                                alttext: alt.value, reviewed: 1, replaceid: current ? current.id : 0});
                        }
                    });
                    approvalButton.dataset.previewBlocked = 'true';
                    approvalButton.disabled = true;
                    form.append(approvalButton);
                }
                form.append(button(text.reject, () => {
                    if (confirm(text.confirmreject)) { request('reject', {imageid: item.id, revision: item.revision}); }
                }));
                reviews.append(form);
            });
        }
        render();
    }
    function start() { document.querySelectorAll('.studyguideai').forEach(initialise); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', start); } else { start(); }
})();
