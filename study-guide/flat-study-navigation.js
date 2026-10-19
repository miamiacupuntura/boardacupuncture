(() => {
  'use strict';

  const cleanTitle = text => text
    .replace(/🖼️\s*View image/gi, '')
    .replace(/\s+/g, ' ')
    .trim();

  function existingLinks(nav) {
    return new Map([...nav.querySelectorAll('a[href^="#"]')].map(link => [link.getAttribute('href'), link]));
  }

  function makeLink(nav, links, href, label) {
    const link = links.get(href) || document.createElement('a');
    link.href = href;
    link.textContent = label;
    link.title = label;
    delete link.dataset.studyNavTarget;
    return link;
  }

  function bindFlatInteractions(nav) {
    if (nav.dataset.flatInteractionBound) return;
    nav.dataset.flatInteractionBound = 'true';
    nav.addEventListener('click', event => {
      const link = event.target.closest('a[href^="#"]');
      if (!link || !nav.contains(link)) return;
      const target = document.getElementById(link.getAttribute('href').slice(1));
      if (!target) return;
      event.preventDefault();
      event.stopPropagation();
      if (target.tagName === 'DETAILS') target.open = true;
      nav.querySelectorAll('a').forEach(item => {
        item.removeAttribute('aria-current');
        item.classList.remove('active');
      });
      link.setAttribute('aria-current', 'location');
      window.history.replaceState(null, '', link.getAttribute('href'));
      target.scrollIntoView({block: 'start'});
      const focusTarget = target.querySelector(':scope > summary, :scope > h2, :scope > h3, :scope > h4') || target;
      focusTarget.tabIndex = -1;
      focusTarget.focus({preventScroll: true});
    }, true);
  }

  function addSection(nav, title, items) {
    const section = document.createElement('section');
    section.className = 'flat-navigation-section';
    if (title === 'Objectives') section.classList.add('flat-navigation-objectives');
    const heading = document.createElement('h4');
    heading.textContent = title;
    const list = document.createElement('ul');
    items.filter(Boolean).forEach(link => {
      const item = document.createElement('li');
      item.append(link);
      list.append(item);
    });
    if (!list.children.length) return;
    section.append(heading, list);
    nav.append(section);
  }

  function currentObjectiveLinks(nav, links, selector) {
    return [...document.querySelectorAll(selector)]
      .map(objective => {
        const summary = objective.querySelector(':scope > summary');
        if (!summary) return null;
        return makeLink(nav, links, `#${objective.id}`, cleanTitle(summary.textContent));
      });
  }

  function ensureChannelsAlignment() {
    let alignment = document.getElementById('channels-ncbahm-alignment');
    if (alignment) return alignment;
    alignment = document.createElement('details');
    alignment.id = 'channels-ncbahm-alignment';
    alignment.className = 'sgv2-card';
    const summary = document.createElement('summary');
    summary.textContent = 'How this Chapter aligns with the NCBAHM Exam';
    const note = document.createElement('p');
    note.textContent = 'This chapter supports foundational channel-and-collateral knowledge used in acupuncture point location, treatment planning and point selection. It does not predict a specific examination item.';
    const table = document.createElement('table');
    table.innerHTML = '<thead><tr><th>Study task</th><th>Board-relevant ability</th></tr></thead><tbody><tr><td>Trace the four directional channel groups</td><td>Recognize channel circulation and sequence</td></tr><tr><td>Identify internal connections</td><td>Distinguish surface pathways, internal branches and zang–fu relationships</td></tr><tr><td>Decode channel names</td><td>Use Hand/Foot, Yin/Yang and six-stage terminology accurately</td></tr><tr><td>Order channel systems by depth</td><td>Compare cutaneous, sinew, Luo, Primary and Divergent relationships without collapsing them</td></tr><tr><td>Recognize Regular Channel organization</td><td>Apply the six Yin/six Yang and paired-channel framework</td></tr></tbody>';
    alignment.append(summary, note, table);
    const reading = document.getElementById('chapter-general-reading');
    const firstObjective = document.getElementById('channels-objective-1');
    if (reading) reading.after(alignment);
    else if (firstObjective) firstObjective.before(alignment);
    return alignment;
  }

  function removeLegacyChannelsObjectives() {
    for (let number = 1; number <= 5; number += 1) {
      document.getElementById(`sgv2-objective-${number}`)?.remove();
      document.querySelectorAll(`a[href="#sgv2-objective-${number}"]`).forEach(link => link.remove());
    }
    document.getElementById('old-objectives-heading')?.remove();
  }

  function rebuildChannels() {
    const nav = document.querySelector('nav[aria-label="Study Guide sections"]');
    if (!nav) return;
    removeLegacyChannelsObjectives();
    ensureChannelsAlignment();
    const links = existingLinks(nav);
    nav.replaceChildren();
    const title = document.createElement('h3');
    title.textContent = 'Study Navigation';
    nav.append(title);

    addSection(nav, 'Start here', [
      makeLink(nav, links, '#sgv2-introduction', 'Introduction'),
      makeLink(nav, links, '#sgv2-course-overview', 'Course Overview'),
      makeLink(nav, links, '#sgv2-formb', 'Learning Plan'),
      makeLink(nav, links, '#chapter-general-reading', 'Chapter Reading'),
      makeLink(nav, links, '#channels-ncbahm-alignment', 'NCBAHM Alignment')
    ]);
    addSection(nav, 'Objectives', currentObjectiveLinks(nav, links, 'details[id^="channels-objective-"]:not([id*="board"])'));
    addSection(nav, 'Chapter resources', [
      makeLink(nav, links, '#chapter-image-library', 'Visual Review Gallery'),
      makeLink(nav, links, '#channels-chapter-continuing-education', 'Continuing Education')
    ]);
    addSection(nav, 'Practice & exams', [
      makeLink(nav, links, '#topic-questions-answers', 'Self-Test'),
      makeLink(nav, links, '#chapter-mock-review', 'Board Simulation'),
      makeLink(nav, links, '#chapter-exam-history', 'Exam History & Start Exam'),
      makeLink(nav, links, '#channels-mock-exams', 'Mock Exams'),
      makeLink(nav, links, '#sgv2-final-review', 'Review Objectives'),
      makeLink(nav, links, '#student_notes_form', 'My Student Notes')
    ]);
    bindFlatInteractions(nav);
    nav.dataset.flatNavigation = 'true';
  }

  function rebuildPoints() {
    const nav = document.getElementById('point-categories-study-navigation');
    if (!nav) return;
    const links = existingLinks(nav);
    nav.replaceChildren();
    const title = document.createElement('h3');
    title.textContent = 'Study Navigation';
    nav.append(title);

    addSection(nav, 'Start here', [
      makeLink(nav, links, '#pc-introduction', 'Introduction'),
      makeLink(nav, links, '#pc-overview', 'Course Overview'),
      makeLink(nav, links, '#pc-learning-plan', 'Learning Plan'),
      makeLink(nav, links, '#pc-reading', 'Chapter Reading'),
      makeLink(nav, links, '#pc-board-alignment', 'NCBAHM Alignment')
    ]);
    addSection(nav, 'Objectives', currentObjectiveLinks(nav, links, 'details[id^="pc-objective-"]'));
    addSection(nav, 'Chapter resources', [
      makeLink(nav, links, '#pc-image', 'Visual Review Gallery'),
      makeLink(nav, links, '#pc-history-gallery', 'Historical Gallery'),
      makeLink(nav, links, '#pc-continuing-reading', 'Continuing Education')
    ]);
    addSection(nav, 'Practice & exams', [
      makeLink(nav, links, '#pc-lecture', 'Self-Test'),
      makeLink(nav, links, '#pc-simulation', 'Board Simulation'),
      makeLink(nav, links, '#pc-exam-history', 'Exam History & Start Exam'),
      makeLink(nav, links, '#point-categories-mock-exams', 'Mock Exams'),
      makeLink(nav, links, '#pc-objective-return', 'Review Objectives'),
      makeLink(nav, links, '#pc-notes', 'My Student Notes')
    ]);
    bindFlatInteractions(nav);
    nav.dataset.flatNavigation = 'true';
  }

  function rebuild() {
    rebuildChannels();
    rebuildPoints();
  }

  const style = document.createElement('style');
  style.textContent = `
    .shared-study-navigation>.study-navigation-group{display:none!important}
    .shared-study-navigation>.flat-navigation-section{margin:0 0 14px;padding:0;border:0;background:transparent}
    .shared-study-navigation>.flat-navigation-section>h4{margin:0 0 4px;padding:8px 10px;border-radius:8px;background:#eaf4f5;color:#075f60;font-size:.9rem;font-weight:850;line-height:1.25}
    .shared-study-navigation>.flat-navigation-section>ul{margin:0!important;padding:0!important;list-style:none!important}
    .shared-study-navigation>.flat-navigation-section li{margin:0!important;padding:0!important}
    .shared-study-navigation>.flat-navigation-section a{display:block!important;margin:1px 0!important;padding:7px 8px 7px 13px!important;border:0!important;border-left:3px solid transparent!important;border-radius:6px!important;background:transparent;color:#244b62;text-decoration:none;font-size:.88rem;line-height:1.3;overflow-wrap:anywhere}
    .shared-study-navigation>.flat-navigation-section a:hover{border-left-color:#63aaa8!important;background:#f0f7f7;color:#075f60}
    .shared-study-navigation>.flat-navigation-section a:focus-visible{outline:3px solid #1769c2;outline-offset:1px}
    .shared-study-navigation>.flat-navigation-section a[aria-current="location"],.shared-study-navigation>.flat-navigation-section a.active{border-left-color:#087774!important;background:#dff0ed;color:#075f60;font-weight:750}
    @media(max-width:760px){.sgv2-navigation>.flat-navigation-objectives{display:none!important}}
  `;
  document.head.append(style);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => setTimeout(rebuild, 0), {once: true});
  } else {
    setTimeout(rebuild, 0);
  }
})();
