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

  /* ── Hero image slider ── */
  var heroSlider = document.querySelector('[data-hero-slider]');
  if (heroSlider) {
    var heroSlides = Array.from(heroSlider.querySelectorAll('[data-hero-slide]'));
    var heroDots = Array.from(heroSlider.querySelectorAll('[data-hero-dot]'));
    var heroIndex = 0;
    var heroTimer;
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function showHeroSlide(index) {
      heroIndex = (index + heroSlides.length) % heroSlides.length;

      heroSlides.forEach(function (slide, slideIndex) {
        var isActive = slideIndex === heroIndex;
        slide.classList.toggle('is-active', isActive);
        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
      });

      heroDots.forEach(function (dot, dotIndex) {
        var isActive = dotIndex === heroIndex;
        dot.classList.toggle('is-active', isActive);
        dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });
    }

    function stopHeroTimer() {
      window.clearInterval(heroTimer);
    }

    function startHeroTimer() {
      if (prefersReducedMotion || heroSlides.length < 2) return;
      stopHeroTimer();
      heroTimer = window.setInterval(function () {
        showHeroSlide(heroIndex + 1);
      }, 6000);
    }

    function restartHeroTimer() {
      stopHeroTimer();
      startHeroTimer();
    }

    heroDots.forEach(function (dot) {
      dot.addEventListener('click', function () {
        showHeroSlide(Number(dot.dataset.heroDot));
        restartHeroTimer();
      });
    });

    heroSlider.addEventListener('mouseenter', stopHeroTimer);
    heroSlider.addEventListener('mouseleave', startHeroTimer);
    heroSlider.addEventListener('focusin', stopHeroTimer);
    heroSlider.addEventListener('focusout', function (event) {
      if (!heroSlider.contains(event.relatedTarget)) startHeroTimer();
    });

    showHeroSlide(0);
    startHeroTimer();
  }

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
