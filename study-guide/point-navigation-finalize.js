(() => {
  'use strict';

  const objectiveOrder = [5, 20, 19, 18, 14, 24, 8, 21, 12, 2, 16, 26, 17, 13, 7, 1, 6, 10, 22, 9, 3, 23, 15, 25, 11, 4];
  const groups = [
    {title: 'Start here', hrefs: ['#pc-introduction', '#pc-overview', '#pc-learning-plan', '#pc-reading', '#pc-board-alignment']},
    {title: 'Objectives', hrefs: objectiveOrder.map(id => `#pc-objective-${id}`)},
    {title: 'Chapter resources', hrefs: ['#pc-image', '#pc-history-gallery', '#pc-continuing-reading']},
    {title: 'Practice & exams', hrefs: ['#pc-lecture', '#pc-simulation', '#pc-exam-history', '#point-categories-mock-exams', '#pc-objective-return', '#pc-notes']}
  ];

  function finalize() {
    const nav = document.getElementById('point-categories-study-navigation');
    if (!nav || nav.dataset.finalNavigation === 'true') return;
    const links = new Map([...nav.querySelectorAll('a[href^="#"]')].map(link => [link.getAttribute('href'), link]));
    const heading = nav.querySelector(':scope > h3') || document.createElement('h3');
    heading.textContent = 'Study Navigation';
    nav.replaceChildren(heading);

    groups.forEach((definition, groupIndex) => {
      const details = document.createElement('details');
      details.className = 'study-navigation-group';
      details.open = groupIndex === 0;
      const summary = document.createElement('summary');
      summary.textContent = definition.title;
      const list = document.createElement('ul');
      definition.hrefs.forEach(href => {
        const link = links.get(href);
        if (!link) return;
        const item = document.createElement('li');
        item.append(link);
        list.append(item);
      });
      if (!list.children.length) return;
      details.append(summary, list);
      nav.append(details);
    });

    const navigationGroups = [...nav.querySelectorAll(':scope > .study-navigation-group')];
    navigationGroups.forEach(group => {
      group.addEventListener('toggle', () => {
        if (!group.open) return;
        navigationGroups.forEach(other => {
          if (other !== group) other.open = false;
        });
      });
    });
    nav.dataset.finalNavigation = 'true';
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => setTimeout(finalize, 0), {once: true});
  } else {
    setTimeout(finalize, 0);
  }
})();
