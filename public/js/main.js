/* FWZ Kärnten – main.js */
(function () {
  'use strict';

  /* ── Mobile nav toggle ── */
  var toggle = document.querySelector('.nav-toggle');
  var header = document.querySelector('.header');
  if (toggle && header) {
    toggle.addEventListener('click', function () {
      var open = header.getAttribute('data-open') === 'true';
      header.setAttribute('data-open', String(!open));
      toggle.setAttribute('aria-expanded', String(!open));
      toggle.querySelector('span').textContent = !open ? '✕' : '☰';
    });
  }

  /* ── Category filter chips ── */
  var chips = document.querySelectorAll('.chip');
  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      chips.forEach(function (c) { c.setAttribute('aria-pressed', 'false'); });
      chip.setAttribute('aria-pressed', 'true');
    });
  });

  /* ── Footer year ── */
  var yearEl = document.getElementById('current-year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  /* ── Open cookie settings from footer link ── */
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-action="open-settings"]');
    if (el) {
      e.preventDefault();
      var dialog = document.getElementById('cookie-dialog');
      if (dialog) {
        dialog.classList.add('open');
        var firstFocus = dialog.querySelector('button, input, a');
        if (firstFocus) firstFocus.focus();
      }
    }
  });
})();
