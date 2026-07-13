(function () {
  'use strict';

  var SEARCH_URL  = '/vereine/suche';
  var DEBOUNCE_MS = 300;
  var PLACEHOLDER = '/img/placeholder-verein-logo.svg';

  var input       = document.getElementById('vereine-suche');
  var listbox     = document.getElementById('vereine-listbox');
  var grid        = document.getElementById('vereine-grid');
  var resultCount = document.querySelector('.vereine-result-count');
  var chips       = document.querySelectorAll('.chips .chip[data-kategorie]');
  var combobox    = document.querySelector('.combobox-wrap');

  if (!input || !listbox || !grid) return;

  var activeKategorie = '';
  var debounceTimer   = null;
  var activeIndex     = -1;

  /* ── Category chips ─────────────────────────────────────────────────── */
  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      chips.forEach(function (c) { c.classList.remove('active'); });
      chip.classList.add('active');
      activeKategorie = chip.dataset.kategorie || '';
      fetchAndRender(input.value.trim(), activeKategorie, true);
    });
  });

  /* ── Input ───────────────────────────────────────────────────────────── */
  input.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      var q = input.value.trim();
      fetchAndRender(q, activeKategorie, q.length === 0);
    }, DEBOUNCE_MS);
  });

  input.addEventListener('keydown', function (e) {
    var options = listbox.querySelectorAll('[role="option"]');
    if (!options.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      activeIndex = Math.min(activeIndex + 1, options.length - 1);
      highlightOption(options);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      activeIndex = Math.max(activeIndex - 1, 0);
      highlightOption(options);
    } else if (e.key === 'Enter' && activeIndex >= 0) {
      e.preventDefault();
      var chosen = options[activeIndex].dataset.name || '';
      input.value = chosen;
      closeListbox();
      fetchAndRender(chosen, activeKategorie, true);
    } else if (e.key === 'Escape') {
      closeListbox();
    }
  });

  document.addEventListener('click', function (e) {
    if (!input.contains(e.target) && !listbox.contains(e.target)) {
      closeListbox();
    }
  });

  /* ── Fetch ───────────────────────────────────────────────────────────── */
  function fetchAndRender(q, kategorie, updateGrid) {
    var url = SEARCH_URL + '?q=' + encodeURIComponent(q) + '&kategorie=' + encodeURIComponent(kategorie);
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var results = data.results || data;
        var total   = data.total != null ? data.total : results.length;

        if (q.length >= 2) {
          renderListbox(results, q);
        } else {
          closeListbox();
        }
        if (updateGrid || kategorie !== '') {
          renderGrid(results);
          announce(total);
        }
      })
      .catch(function () {});
  }

  /* ── Listbox ─────────────────────────────────────────────────────────── */
  function renderListbox(results, q) {
    activeIndex = -1;
    listbox.innerHTML = '';

    var hits = results.slice(0, 8);
    if (!hits.length) { closeListbox(); return; }

    hits.forEach(function (org, i) {
      var opt = document.createElement('div');
      opt.setAttribute('role', 'option');
      opt.setAttribute('id', 'vo-' + i);
      opt.setAttribute('aria-selected', 'false');
      opt.dataset.name = org.name;
      opt.className = 'vereine-option';
      opt.innerHTML =
        '<strong>' + esc(org.name) + '</strong>' +
        (org.ort ? '<span class="vereine-option__ort">' + esc(org.ort) + '</span>' : '');
      opt.addEventListener('mousedown', function (e) {
        e.preventDefault();
        input.value = org.name;
        closeListbox();
        fetchAndRender(org.name, activeKategorie, true);
      });
      listbox.appendChild(opt);
    });

    listbox.hidden = false;
    if (combobox) combobox.setAttribute('aria-expanded', 'true');
  }

  function highlightOption(options) {
    options.forEach(function (o, i) {
      var on = i === activeIndex;
      o.classList.toggle('is-active', on);
      o.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    input.setAttribute('aria-activedescendant', activeIndex >= 0 ? 'vo-' + activeIndex : '');
  }

  function closeListbox() {
    listbox.hidden = true;
    listbox.innerHTML = '';
    activeIndex = -1;
    if (combobox) combobox.setAttribute('aria-expanded', 'false');
    input.removeAttribute('aria-activedescendant');
  }

  /* ── Grid ────────────────────────────────────────────────────────────── */
  function renderGrid(results) {
    if (!results.length) {
      grid.innerHTML = '<p class="muted">Keine Vereine gefunden.</p>';
      return;
    }
    grid.innerHTML = results.map(function (org) {
      var src = org.logo_url || PLACEHOLDER;
      return '<div class="org-card">' +
        '<img class="org-logo" src="' + esc(src) + '" alt="' + esc(org.name) + '" loading="lazy" onerror="this.src=\'' + PLACEHOLDER + '\'">' +
        '<div class="name">' + esc(org.name) + '</div>' +
        '<div class="place">' + esc(org.ort || '') + '</div>' +
        '</div>';
    }).join('');
  }

  /* ── Live region ─────────────────────────────────────────────────────── */
  function announce(n) {
    if (resultCount) {
      resultCount.textContent = n === 1 ? '1 Verein gefunden' : n + ' Vereine gefunden';
    }
  }

  /* ── Util ────────────────────────────────────────────────────────────── */
  function esc(s) {
    return String(s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
})();
