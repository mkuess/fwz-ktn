/* FWZ Kärnten – main.js */
(function () {
  'use strict';

  /* ── Mobile nav: Escape key closes (Alpine owns open/close state) ── */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var toggle = document.querySelector('.nav-toggle');
      if (toggle && toggle.getAttribute('aria-expanded') === 'true') {
        toggle.click();
      }
    }
  });

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
