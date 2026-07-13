/* FWZ Kärnten – main.js */
(function () {
  'use strict';

  /* ── Mobile nav toggle ── */
  var toggle = document.querySelector('.nav-toggle');
  var panel = document.getElementById('mobile-nav-panel');

  function openPanel() {
    panel.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    toggle.querySelector('span').textContent = '✕';
    document.body.style.overflow = 'hidden';
  }

  function closePanel() {
    panel.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
    toggle.querySelector('span').textContent = '☰';
    document.body.style.overflow = '';
  }

  if (toggle && panel) {
    toggle.addEventListener('click', function () {
      panel.hidden ? openPanel() : closePanel();
    });

    panel.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') closePanel();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !panel.hidden) closePanel();
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
