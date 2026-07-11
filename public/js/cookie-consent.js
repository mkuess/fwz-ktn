/* FWZ Kärnten – cookie-consent.js
   Granular opt-in, DSGVO-konform, kein Dark Pattern.
   Version: 1 — bei inhaltlichen Kategorieänderungen hochzählen → Nutzer wird neu gefragt. */
(function () {
  'use strict';

  var CONSENT_KEY = 'fwz_consent';
  var CONSENT_VERSION = 1;

  function getConsent() {
    try {
      var raw = localStorage.getItem(CONSENT_KEY);
      if (!raw) return null;
      var obj = JSON.parse(raw);
      if (obj.version !== CONSENT_VERSION) return null;
      return obj;
    } catch (e) { return null; }
  }

  function saveConsent(statistics, marketing) {
    localStorage.setItem(CONSENT_KEY, JSON.stringify({
      version: CONSENT_VERSION,
      ts: Date.now(),
      statistics: !!statistics,
      marketing: !!marketing,
    }));
  }

  function hideBanner() {
    var b = document.getElementById('cookie-banner');
    if (b) b.remove();
  }

  function hideDialog() {
    var d = document.getElementById('cookie-dialog');
    if (d) d.classList.remove('open');
  }

  function applyConsent(consent) {
    if (consent && consent.statistics) {
      /* Activate statistics scripts:
         document.querySelectorAll('script[data-consent="statistics"]').forEach(function (s) {
           var n = document.createElement('script'); n.src = s.dataset.src; document.head.appendChild(n);
         }); */
    }
  }

  function showBanner() {
    var banner = document.createElement('div');
    banner.id = 'cookie-banner';
    banner.setAttribute('role', 'region');
    banner.setAttribute('aria-label', 'Cookie-Einwilligung');
    banner.innerHTML =
      '<p>Wir verwenden ausschließlich technisch notwendige Cookies. Optionale Analysefunktionen aktivieren wir nur mit Ihrer Einwilligung. <a href="/datenschutz">Mehr erfahren</a></p>' +
      '<div class="cookie-actions">' +
        '<button class="btn primary" id="cookie-accept-all">Alle akzeptieren</button>' +
        '<button class="btn light" id="cookie-necessary">Nur notwendige</button>' +
        '<button class="btn light" id="cookie-settings-btn">Einstellungen</button>' +
      '</div>';
    document.body.appendChild(banner);

    document.getElementById('cookie-accept-all').addEventListener('click', function () {
      saveConsent(true, true); applyConsent({ statistics: true }); hideBanner();
    });
    document.getElementById('cookie-necessary').addEventListener('click', function () {
      saveConsent(false, false); hideBanner();
    });
    document.getElementById('cookie-settings-btn').addEventListener('click', function () {
      openDialog();
    });
  }

  function openDialog() {
    var existing = document.getElementById('cookie-dialog');
    if (existing) { existing.classList.add('open'); return; }

    var dialog = document.createElement('div');
    dialog.id = 'cookie-dialog';
    dialog.setAttribute('role', 'dialog');
    dialog.setAttribute('aria-modal', 'true');
    dialog.setAttribute('aria-labelledby', 'cookie-dialog-title');
    dialog.innerHTML =
      '<div class="cookie-dialog-inner">' +
        '<h2 id="cookie-dialog-title">Cookie-Einstellungen</h2>' +
        '<div class="cookie-category"><label><input type="checkbox" checked disabled> <div><strong>Notwendig</strong><br><small>Für den Betrieb der Website zwingend erforderlich. Können nicht deaktiviert werden.</small></div></label></div>' +
        '<div class="cookie-category"><label><input type="checkbox" id="c-statistics"> <div><strong>Statistik</strong><br><small>Helfen uns zu verstehen, wie Besucher:innen die Website nutzen (anonymisiert).</small></div></label></div>' +
        '<div class="cookie-category"><label><input type="checkbox" id="c-marketing"> <div><strong>Marketing</strong><br><small>Werden derzeit nicht eingesetzt.</small></div></label></div>' +
        '<div class="cookie-dialog-actions">' +
          '<button class="btn primary" id="cookie-save">Auswahl speichern</button>' +
          '<button class="btn light" id="cookie-close">Schließen</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(dialog);
    dialog.classList.add('open');

    /* Restore previous choices */
    var prev = getConsent();
    if (prev) {
      document.getElementById('c-statistics').checked = !!prev.statistics;
      document.getElementById('c-marketing').checked = !!prev.marketing;
    }

    document.getElementById('cookie-save').addEventListener('click', function () {
      var s = document.getElementById('c-statistics').checked;
      var m = document.getElementById('c-marketing').checked;
      saveConsent(s, m); applyConsent({ statistics: s }); hideBanner(); hideDialog();
    });
    document.getElementById('cookie-close').addEventListener('click', hideDialog);

    /* Close on backdrop click */
    dialog.addEventListener('click', function (e) { if (e.target === dialog) hideDialog(); });

    /* Focus trap */
    var focusable = dialog.querySelectorAll('button, input, a');
    var first = focusable[0]; var last = focusable[focusable.length - 1];
    dialog.addEventListener('keydown', function (e) {
      if (e.key !== 'Tab') return;
      if (e.shiftKey) { if (document.activeElement === first) { e.preventDefault(); last.focus(); } }
      else { if (document.activeElement === last) { e.preventDefault(); first.focus(); } }
    });
    if (first) first.focus();
  }

  /* ── Init ── */
  document.addEventListener('DOMContentLoaded', function () {
    var consent = getConsent();
    if (consent) { applyConsent(consent); return; }
    showBanner();
  });
})();
