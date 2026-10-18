(() => {
  'use strict';
  const channelsNav = document.querySelector('nav[aria-label="Study Guide sections"]');
  const pointsNav = document.getElementById('point-categories-study-navigation');

  function organizeChannels(nav) {
    if (!nav || nav.querySelector(':scope > .study-navigation-group')) return;
    const source = nav.querySelector(':scope > ul');
    if (!source) return;
    const groups = [];
    let current = {title: 'Start here', items: []};
    groups.push(current);
    [...source.children].forEach(item => {
      if (item.classList.contains('channels-nav-label')) {
        current = {title: item.textContent.trim(), items: []};
        groups.push(current);
      } else {
        current.items.push(item);
      }
    });
    source.remove();
    groups.filter(group => group.items.length).forEach((group, index) => {
      const details = document.createElement('details');
      details.className = 'study-navigation-group';
      details.open = index === 0;
      const summary = document.createElement('summary');
      summary.textContent = group.title;
      const list = document.createElement('ul');
      group.items.forEach(item => list.append(item));
      details.append(summary, list);
      nav.append(details);
    });
  }

  function standardize(nav) {
    if (!nav) return;
    nav.classList.add('organized-study-nav', 'shared-study-navigation');
    nav.parentElement?.classList.add('shared-chapter-layout');
    const heading = nav.querySelector(':scope > h3');
    if (heading) heading.textContent = 'Study Navigation';
    const groups = [...nav.querySelectorAll(':scope > .study-navigation-group')];
    groups.forEach((group, index) => {
      group.open = index === 0;
      if (group.dataset.simpleAccordionBound) return;
      group.dataset.simpleAccordionBound = 'true';
      group.addEventListener('toggle', () => {
        if (!group.open) return;
        groups.forEach(other => {
          if (other !== group) other.open = false;
        });
      });
    });

    const links = [...nav.querySelectorAll(':scope > .study-navigation-group a')];
    if (!links.some(link => link.getAttribute('aria-current') === 'location')) {
      const matchingLink = links.find(link => link.getAttribute('href') === window.location.hash);
      (matchingLink || links[0])?.setAttribute('aria-current', 'location');
    }
    if (!nav.dataset.simpleActiveLinkBound) {
      nav.dataset.simpleActiveLinkBound = 'true';
      nav.addEventListener('click', event => {
        const link = event.target.closest('a');
        if (!link || !nav.contains(link)) return;
        nav.querySelectorAll('a').forEach(item => {
          item.removeAttribute('aria-current');
          item.classList.remove('active');
        });
        link.setAttribute('aria-current', 'location');
      });
    }
  }
  organizeChannels(channelsNav);
  standardize(channelsNav);
  standardize(pointsNav);
  document.querySelectorAll('.chapter-selector button')[0]?.addEventListener('click', () => setTimeout(() => standardize(channelsNav), 0));
  document.querySelectorAll('.chapter-selector button')[1]?.addEventListener('click', () => setTimeout(() => standardize(document.getElementById('point-categories-study-navigation')), 0));

  const style = document.createElement('style');
  style.textContent = `
    .shared-chapter-layout{display:grid!important;grid-template-columns:240px minmax(0,1fr)!important;column-gap:24px!important;align-items:start!important}
    .shared-chapter-layout[hidden]{display:none!important}
    .shared-chapter-layout>.sgv2-content{min-width:0;width:100%}
    .shared-study-navigation{position:sticky!important;top:150px!important;width:240px!important;max-height:calc(100vh - 170px);padding:18px!important;overflow-y:auto;overflow-x:hidden;box-sizing:border-box;border:1px solid #d4e3e6;border-top:5px solid #087774;border-radius:14px;background:#fff;scrollbar-width:thin;scrollbar-color:#9bbfc1 transparent}
    .shared-study-navigation>h3{margin:0 0 14px;color:#08706c;font-size:1.05rem;line-height:1.3}
    .shared-study-navigation>.study-navigation-group{margin:0 0 8px!important;padding:0!important;border:0!important;background:transparent!important}
    .shared-study-navigation>.study-navigation-group>summary{display:flex;align-items:center;min-height:42px;box-sizing:border-box;margin:0!important;padding:9px 11px!important;border:1px solid transparent!important;border-radius:9px;background:#eaf4f5!important;color:#075f60!important;font-weight:800;line-height:1.3;cursor:pointer;list-style:none}
    .shared-study-navigation>.study-navigation-group>summary::-webkit-details-marker{display:none}
    .shared-study-navigation>.study-navigation-group>summary::before{content:'▸';display:inline-block;flex:0 0 auto;margin-right:7px;transition:transform .15s ease}
    .shared-study-navigation>.study-navigation-group[open]>summary::before{transform:rotate(90deg)}
    .shared-study-navigation>.study-navigation-group>summary:hover{background:#dceced!important}
    .shared-study-navigation>.study-navigation-group>summary:focus-visible{outline:3px solid #1769c2;outline-offset:2px}
    .shared-study-navigation>.study-navigation-group>ul{margin:5px 0 10px!important;padding:0!important;list-style:none!important}
    .shared-study-navigation>.study-navigation-group li{margin:0!important;padding:0!important}
    .shared-study-navigation>.study-navigation-group a{display:block!important;margin:1px 0!important;padding:8px 9px 8px 15px!important;border:0!important;border-left:3px solid transparent!important;border-radius:7px!important;background:transparent;color:#244b62;text-decoration:none;line-height:1.3;overflow-wrap:anywhere}
    .shared-study-navigation>.study-navigation-group a:hover{border-left-color:#63aaa8;background:#f0f7f7;color:#075f60}
    .shared-study-navigation>.study-navigation-group a:focus-visible{outline:3px solid #1769c2;outline-offset:1px}
    .shared-study-navigation>.study-navigation-group a[aria-current="location"],.shared-study-navigation>.study-navigation-group a.active{border-left-color:#087774;background:#dff0ed;color:#075f60;font-weight:750}
    @media(max-width:900px){.shared-chapter-layout{grid-template-columns:minmax(0,1fr)!important;gap:16px!important}.shared-study-navigation{position:static!important;top:auto!important;width:100%!important;max-height:none;overflow:visible}}
  `;
  document.head.append(style);
})();
