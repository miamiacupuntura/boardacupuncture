const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const source = fs.readFileSync(path.join(__dirname, '../review.js'), 'utf8');

class Element {
    constructor(tag) { this.tag = tag; this.children = []; this.style = {}; this.dataset = {}; this.listeners = {}; }
    append(...children) { this.children.push(...children); }
    replaceChildren(...children) { this.children = children; }
    addEventListener(name, handler) { this.listeners[name] = handler; }
    querySelectorAll(tag) {
        return this.children.flatMap(child => [ ...(child.tag === tag ? [child] : []), ...child.querySelectorAll(tag)]);
    }
}
const pending = {id: 2, revision: 1, status: 'pending', title: 'Draft', alttext: 'Draft alt', url: '/private-preview'};
const approved = {id: 1, revision: 2, status: 'approved', title: '<script>plain title</script>', alttext: 'Approved alt', url: '/approved-image'};
function setup(images, capabilities = {}) {
    const root = new Element('section');
    const nodes = Object.fromEntries(['gallery', 'controls', 'review', 'status', 'audit'].map(name => [name, new Element('div')]));
    root.append(...Object.values(nodes));
    root.querySelector = selector => nodes[selector.match(/data-sg-(\w+)/)[1]];
    const strings = Object.fromEntries(['generate', 'approve', 'reject', 'withdraw', 'replace', 'title', 'alttext', 'review',
        'audit', 'error', 'previewerror', 'confirmcost', 'confirmwithdraw', 'confirmapprove', 'confirmreject', 'nocurrent'].map(key => [key, key]));
    root.dataset.config = JSON.stringify(Object.assign({cmid: 10, topic: 'channels-and-collaterals',
        objective: 'primary-channel-circulation', api: '/local/studyguideai/api.php', sesskey: 'fixture-only',
        strings, generate: false, approve: false, manage: false}, capabilities));
    root.dataset.images = JSON.stringify(images);
    const requests = [];
    const sandbox = {URLSearchParams, document: {readyState: 'complete', querySelectorAll: () => [root],
        createElement: tag => new Element(tag)}, confirm: () => true,
        fetch: async (url, options) => {
            requests.push({url, options, params: Object.fromEntries(new URLSearchParams(options.body))});
            return {ok: true, json: async () => ({success: true, images: []})};
        }};
    vm.runInNewContext(source, sandbox);
    root.querySelectorAll('img').forEach(img => { if (img.listeners.load) { img.listeners.load(); } });
    return {root, nodes, requests, sandbox};
}
const flush = () => new Promise(resolve => setImmediate(resolve));
const findButton = (root, label) => root.querySelectorAll('button').find(button => button.textContent === label);
const instructor = {generate: true, approve: true, manage: true};

test('students see approved content and no review/decision controls', () => {
    const {root, nodes} = setup([approved]);
    assert.equal(root.querySelectorAll('button').length, 0);
    assert.equal(nodes.review.children.length, 0);
    assert.equal(nodes.gallery.querySelectorAll('img')[0].src, approved.url);
    assert.equal(nodes.gallery.querySelectorAll('img')[0].alt, approved.alttext);
    assert.equal(nodes.gallery.querySelectorAll('figcaption')[0].textContent, approved.title);
});

test('publication requires image review and uses edited title and alt text', async () => {
    const {root, requests} = setup([pending], instructor);
    const approve = findButton(root, 'approve');
    approve.listeners.click();
    await flush();
    assert.equal(requests.length, 0);
    const inputs = root.querySelectorAll('input');
    inputs.find(input => input.type === 'text').value = 'Reviewed title';
    inputs.find(input => input.type === 'checkbox').checked = true;
    root.querySelectorAll('textarea')[0].value = 'Reviewed description';
    approve.listeners.click();
    await flush();
    assert.equal(requests.length, 1);
    assert.equal(requests[0].params.title, 'Reviewed title');
    assert.equal(requests[0].params.alttext, 'Reviewed description');
    assert.equal(requests[0].params.reviewed, '1');
    assert.equal(requests[0].params.revision, '1');
    assert.equal(requests[0].params.sesskey, 'fixture-only');
    assert.equal(requests[0].options.method, 'POST');
});

test('replacement names the current approved image explicitly', async () => {
    const {root, requests} = setup([approved, pending], instructor);
    root.querySelectorAll('input').find(input => input.type === 'checkbox').checked = true;
    findButton(root, 'replace').listeners.click();
    await flush();
    assert.equal(requests[0].params.action, 'replace');
    assert.equal(requests[0].params.replaceid, '1');
    assert.equal(requests[0].params.imageid, '2');
});

test('cancelling generation or withdrawal makes no request', async () => {
    const {root, requests, sandbox} = setup([approved], instructor);
    sandbox.confirm = () => false;
    findButton(root, 'generate').listeners.click();
    findButton(root, 'withdraw').listeners.click();
    await flush();
    assert.equal(requests.length, 0);
});

test('stale decision shows server error without automatic retry', async () => {
    const {root, nodes, requests, sandbox} = setup([pending], instructor);
    sandbox.fetch = async (url, options) => {
        requests.push(options);
        return {ok: false, json: async () => ({success: false, error: 'Reload before deciding'})};
    };
    root.querySelectorAll('input').find(input => input.type === 'checkbox').checked = true;
    findButton(root, 'approve').listeners.click();
    await flush();
    assert.equal(requests.length, 1);
    assert.equal(nodes.status.textContent, 'Reload before deciding');
    assert.equal(findButton(root, 'approve').disabled, false);
});


test('a failed preview cannot be approved even when the review checkbox is checked', async () => {
    const {root, requests, nodes} = setup([pending], instructor);
    const img = root.querySelectorAll('img')[0];
    img.listeners.error();
    root.querySelectorAll('input').find(input => input.type === 'checkbox').checked = true;
    const approve = findButton(root, 'approve');
    assert.equal(approve.disabled, true);
    approve.listeners.click();
    await flush();
    assert.equal(requests.length, 0);
});
