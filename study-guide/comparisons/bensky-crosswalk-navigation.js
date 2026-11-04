(function() {
  'use strict';

  const navigation = document.querySelector('[data-map-navigation]');
  const jump = document.querySelector('[data-map-jump]');
  const previous = document.querySelector('[data-map-previous]');
  const next = document.querySelector('[data-map-next]');
  const previousName = document.querySelector('[data-map-previous-name]');
  const nextName = document.querySelector('[data-map-next-name]');
  const routeCurrent = document.querySelector('[data-map-route-current]');
  const rows = [...document.querySelectorAll('tr[id^="map-card-"]')];

  if (!navigation || !jump || !previous || !next || !rows.length) {
    return;
  }

  const options = [...jump.options];
  const maximum = options.length;
  let active = 1;

  const optionLabel = function(number) {
    const option = options.find(item => Number(item.value) === number);
    if (!option) {
      return '';
    }
    return option.textContent.replace(/^\d+\s*·\s*/, '');
  };

  const updateLink = function(link, nameNode, number) {
    const available = number >= 1 && number <= maximum;
    link.setAttribute('aria-disabled', available ? 'false' : 'true');
    if (!available) {
      nameNode.textContent = '';
      return;
    }
    link.href = '#map-card-' + number;
    nameNode.textContent = optionLabel(number);
  };

  const setActive = function(number, replaceHash) {
    if (!Number.isInteger(number) || number < 1 || number > maximum) {
      return;
    }
    active = number;
    jump.value = String(number);
    updateLink(previous, previousName, number - 1);
    updateLink(next, nextName, number + 1);
    if (routeCurrent) {
      routeCurrent.textContent = optionLabel(number);
    }
    rows.forEach(row => {
      if (row.id === 'map-card-' + number) {
        row.setAttribute('aria-current', 'true');
      } else {
        row.removeAttribute('aria-current');
      }
    });
    if (replaceHash && window.location.hash !== '#map-card-' + number) {
      window.history.replaceState(window.history.state, '', '#map-card-' + number);
    }
  };

  const goTo = function(number) {
    const row = document.getElementById('map-card-' + number);
    if (!row) {
      return;
    }
    window.history.pushState(window.history.state, '', '#map-card-' + number);
    setActive(number, false);
    row.scrollIntoView({behavior: 'smooth', block: 'start'});
  };

  jump.addEventListener('change', function() {
    goTo(Number(jump.value));
  });
  previous.addEventListener('click', function(event) {
    event.preventDefault();
    if (active > 1) {
      goTo(active - 1);
    }
  });
  next.addEventListener('click', function(event) {
    event.preventDefault();
    if (active < maximum) {
      goTo(active + 1);
    }
  });

  const hashMatch = window.location.hash.match(/^#map-card-(\d+)$/);
  const initial = hashMatch ? Number(hashMatch[1]) : 1;
  setActive(initial, false);

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function(entries) {
      const visible = entries
        .filter(entry => entry.isIntersecting)
        .sort((first, second) => Math.abs(first.boundingClientRect.top) - Math.abs(second.boundingClientRect.top));
      if (!visible.length) {
        return;
      }
      const number = Number(visible[0].target.id.replace('map-card-', ''));
      setActive(number, true);
    }, {rootMargin: '-14% 0px -72% 0px', threshold: 0});
    rows.forEach(row => observer.observe(row));
  }
})();
