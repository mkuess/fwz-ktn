(function () {
  'use strict';

  var SEARCH_URL = '/vereine/suche';
  var DEBOUNCE_MS = 300;
  var PLACEHOLDER = '/img/placeholder-verein-logo.svg';

  var input = document.getElementById('vereine-suche');
  var listbox = document.getElementById('vereine-listbox');
  var grid = document.getElementById('vereine-grid');
  var resultCount = document.querySelector('.vereine-result-count');
  var chips = document.querySelectorAll('.chips .chip[data-kategorie]');
  var combobox = document.querySelector('.combobox-wrap');
  var loadMore = document.getElementById('vereine-load-more');

  if (!input || !grid) return;

  var isInfinite = grid.dataset.infiniteScroll === 'true';
  var pageSize = parseInt(grid.dataset.searchLimit || (isInfinite ? '12' : '8'), 10);
  var currentPage = parseInt(grid.dataset.initialPage || '1', 10);
  var nextPage = currentPage + 1;
  var hasMore = isInfinite && grid.dataset.hasMore === 'true';
  var activeKategorie = grid.dataset.activeCategory || '';
  var currentQuery = input.value.trim();
  var debounceTimer = null;
  var activeIndex = -1;
  var requestSequence = 0;
  var loading = false;

  chips.forEach(function (chip) {
    if (chip.classList.contains('active')) {
      activeKategorie = chip.dataset.kategorie || '';
    }

    chip.addEventListener('click', function () {
      chips.forEach(function (candidate) {
        candidate.classList.remove('active');
        candidate.setAttribute('aria-pressed', 'false');
      });
      chip.classList.add('active');
      chip.setAttribute('aria-pressed', 'true');
      activeKategorie = chip.dataset.kategorie || '';
      currentQuery = input.value.trim();
      fetchAndRender(currentQuery, activeKategorie, 1, false);
    });
  });

  input.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      currentQuery = input.value.trim();
      fetchAndRender(currentQuery, activeKategorie, 1, false);
    }, DEBOUNCE_MS);
  });

  input.addEventListener('keydown', function (event) {
    if (!listbox) return;

    var options = listbox.querySelectorAll('[role="option"]');
    if (!options.length) return;

    if (event.key === 'ArrowDown') {
      event.preventDefault();
      activeIndex = Math.min(activeIndex + 1, options.length - 1);
      highlightOption(options);
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      activeIndex = Math.max(activeIndex - 1, 0);
      highlightOption(options);
    } else if (event.key === 'Enter' && activeIndex >= 0) {
      event.preventDefault();
      input.value = options[activeIndex].dataset.name || '';
      currentQuery = input.value.trim();
      closeListbox();
      fetchAndRender(currentQuery, activeKategorie, 1, false);
    } else if (event.key === 'Escape') {
      closeListbox();
    }
  });

  document.addEventListener('click', function (event) {
    if (!listbox || (!input.contains(event.target) && !listbox.contains(event.target))) {
      closeListbox();
    }
  });

  function fetchAndRender(query, category, page, append) {
    if (append && (loading || !hasMore)) return;

    var requestId = ++requestSequence;
    loading = true;
    grid.setAttribute('aria-busy', 'true');
    updateLoadMore();

    var url = SEARCH_URL
      + '?q=' + encodeURIComponent(query)
      + '&kategorie=' + encodeURIComponent(category)
      + '&limit=' + encodeURIComponent(pageSize)
      + '&page=' + encodeURIComponent(page);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Die Vereinssuche konnte nicht geladen werden.');
        }

        return response.json();
      })
      .then(function (data) {
        if (requestId !== requestSequence) return;

        var results = data.results || [];
        renderGrid(results, append);
        announce(data.total != null ? data.total : results.length);

        if (listbox && query.length >= 2 && !append) {
          renderListbox(results);
        } else if (listbox && !append) {
          closeListbox();
        }

        currentPage = data.page || page;
        nextPage = currentPage + 1;
        hasMore = isInfinite && Boolean(data.has_more);
        loading = false;
        grid.setAttribute('aria-busy', 'false');
        updateLoadMore();
      })
      .catch(function () {
        if (requestId !== requestSequence) return;

        loading = false;
        grid.setAttribute('aria-busy', 'false');
        updateLoadMore();
        announceError('Die Vereine konnten nicht geladen werden. Bitte versuche es erneut.');
      });
  }

  function renderListbox(results) {
    if (!listbox) return;

    activeIndex = -1;
    listbox.innerHTML = '';

    var hits = results.slice(0, 8);
    if (!hits.length) {
      closeListbox();
      return;
    }

    hits.forEach(function (organisation, index) {
      var option = document.createElement('div');
      option.setAttribute('role', 'option');
      option.setAttribute('id', 'vo-' + index);
      option.setAttribute('aria-selected', 'false');
      option.dataset.name = organisation.name;
      option.className = 'vereine-option';
      option.innerHTML =
        '<strong>' + escapeHtml(organisation.name) + '</strong>' +
        (organisation.ort
          ? '<span class="vereine-option__ort">' + escapeHtml(organisation.ort) + '</span>'
          : '');
      option.addEventListener('mousedown', function (event) {
        event.preventDefault();
        input.value = organisation.name;
        currentQuery = organisation.name;
        closeListbox();
        fetchAndRender(currentQuery, activeKategorie, 1, false);
      });
      listbox.appendChild(option);
    });

    listbox.hidden = false;
    if (combobox) combobox.setAttribute('aria-expanded', 'true');
  }

  function highlightOption(options) {
    options.forEach(function (option, index) {
      var active = index === activeIndex;
      option.classList.toggle('is-active', active);
      option.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    input.setAttribute('aria-activedescendant', activeIndex >= 0 ? 'vo-' + activeIndex : '');
  }

  function closeListbox() {
    if (!listbox) return;

    listbox.hidden = true;
    listbox.innerHTML = '';
    activeIndex = -1;
    if (combobox) combobox.setAttribute('aria-expanded', 'false');
    input.removeAttribute('aria-activedescendant');
  }

  function renderGrid(results, append) {
    if (!results.length && !append) {
      grid.innerHTML = '<p class="muted">Keine Vereine gefunden.</p>';
      return;
    }

    var html = results.map(renderCard).join('');
    if (append) {
      grid.insertAdjacentHTML('beforeend', html);
    } else {
      grid.innerHTML = html;
    }
  }

  function renderCard(organisation) {
    var categories = Array.isArray(organisation.categories)
      ? organisation.categories.slice(0, 2)
      : [];
    var categoryHtml = categories.length
      ? '<div class="org-card-categories">' + categories.map(function (category) {
        return '<span>' + escapeHtml(category) + '</span>';
      }).join('') + '</div>'
      : '';
    var logo = organisation.logo_url || PLACEHOLDER;
    var href = '/vereine/' + encodeURIComponent(String(organisation.id));

    return '<a href="' + href + '" style="text-decoration:none;color:inherit;display:block">' +
      '<div class="org-card" style="cursor:pointer">' +
      '<img class="org-logo" src="' + escapeHtml(logo) + '" alt="' + escapeHtml(organisation.name) +
      '" loading="lazy" onerror="this.src=\'' + PLACEHOLDER + '\'">' +
      '<div class="name">' + escapeHtml(organisation.name) + '</div>' +
      (organisation.ort ? '<div class="place">' + escapeHtml(organisation.ort) + '</div>' : '') +
      categoryHtml +
      '</div>' +
      '</a>';
  }

  function announce(number) {
    if (!resultCount) return;

    resultCount.textContent = number === 1 ? '1 Verein gefunden' : number + ' Vereine gefunden';
  }

  function announceError(message) {
    if (resultCount) {
      resultCount.textContent = message;
    }
  }

  function updateLoadMore() {
    if (!loadMore) return;

    loadMore.hidden = !hasMore && !loading;
    loadMore.setAttribute('aria-busy', loading ? 'true' : 'false');
  }

  function loadNextPage() {
    fetchAndRender(currentQuery, activeKategorie, nextPage, true);
  }

  if (isInfinite && loadMore) {
    updateLoadMore();

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        if (entries[0].isIntersecting) {
          loadNextPage();
        }
      }, { rootMargin: '500px 0px' });
      observer.observe(loadMore);
    } else {
      window.addEventListener('scroll', function () {
        if (loadMore.getBoundingClientRect().top <= window.innerHeight + 500) {
          loadNextPage();
        }
      }, { passive: true });
    }
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }
})();