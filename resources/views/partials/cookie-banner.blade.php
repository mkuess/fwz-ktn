<div
    x-data="cookieConsent()"
    x-show="!decided"
    x-cloak
    style="position:fixed;bottom:0;left:0;right:0;z-index:99999;background:#fff;border-top:2px solid #e5e7eb;box-shadow:0 -4px 20px rgba(0,0,0,0.1);padding:1rem 1.5rem;font-family:inherit"
    role="dialog"
    aria-modal="true"
    aria-label="Cookie-Einstellungen">

    <!-- Simple bar -->
    <div x-show="!showDetails" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;max-width:1200px;margin:0 auto">
        <p style="margin:0;font-size:0.875rem;color:#374151;flex:1;min-width:200px">
            Wir verwenden Cookies, um Ihnen die bestmögliche Nutzung unserer Website zu ermöglichen.
            Technisch notwendige Cookies werden immer gesetzt.
            <a href="/datenschutz" style="color:#c9a227;text-decoration:underline">Mehr erfahren</a>
        </p>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center">
            <button @click="rejectAll()" style="padding:0.5rem 1.25rem;border:2px solid #6b7280;background:transparent;color:#374151;border-radius:2rem;cursor:pointer;font-size:0.875rem;white-space:nowrap">
                Alle ablehnen
            </button>
            <button @click="showDetails = true" style="padding:0.5rem 1.25rem;border:2px solid #1a2e1a;background:transparent;color:#1a2e1a;border-radius:2rem;cursor:pointer;font-size:0.875rem;white-space:nowrap">
                Einstellungen
            </button>
            <button @click="acceptAll()" style="padding:0.5rem 1.25rem;border:2px solid #c9a227;background:#c9a227;color:#fff;border-radius:2rem;cursor:pointer;font-size:0.875rem;font-weight:600;white-space:nowrap">
                Alle akzeptieren
            </button>
        </div>
    </div>

    <!-- Detailed settings -->
    <div x-show="showDetails" style="max-width:600px;margin:0 auto">
        <h3 style="margin:0 0 0.75rem;font-size:1rem;color:#1a2e1a">Cookie-Einstellungen</h3>

        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:0.75rem 0;border-bottom:1px solid #e5e7eb">
            <div>
                <strong style="font-size:0.875rem;color:#111827">Technisch notwendig</strong>
                <p style="margin:0.25rem 0 0;font-size:0.75rem;color:#6b7280">Session, Sicherheit, Grundfunktionen. Immer aktiv.</p>
            </div>
            <div style="background:#22c55e;color:#fff;padding:0.25rem 0.75rem;border-radius:1rem;font-size:0.75rem;white-space:nowrap;margin-left:1rem">Immer aktiv</div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:0.75rem 0;border-bottom:1px solid #e5e7eb">
            <div>
                <strong style="font-size:0.875rem;color:#111827">Analyse &amp; Statistik</strong>
                <p style="margin:0.25rem 0 0;font-size:0.75rem;color:#6b7280">Hilft uns zu verstehen, wie Besucher unsere Website nutzen (z.B. Matomo).</p>
            </div>
            <button
                @click="preferences.analytics = !preferences.analytics"
                :style="preferences.analytics ? 'background:#c9a227' : 'background:#d1d5db'"
                style="width:44px;height:24px;border-radius:12px;border:none;cursor:pointer;position:relative;transition:background 0.2s;flex-shrink:0;margin-left:1rem"
                :aria-checked="preferences.analytics"
                role="switch">
                <span :style="preferences.analytics ? 'left:22px' : 'left:2px'" style="position:absolute;top:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:left 0.2s"></span>
            </button>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:0.75rem 0;border-bottom:1px solid #e5e7eb">
            <div>
                <strong style="font-size:0.875rem;color:#111827">Komfort &amp; Karten</strong>
                <p style="margin:0.25rem 0 0;font-size:0.75rem;color:#6b7280">Ermöglicht eingebettete Karten und Komfortfunktionen (z.B. OpenStreetMap).</p>
            </div>
            <button
                @click="preferences.comfort = !preferences.comfort"
                :style="preferences.comfort ? 'background:#c9a227' : 'background:#d1d5db'"
                style="width:44px;height:24px;border-radius:12px;border:none;cursor:pointer;position:relative;transition:background 0.2s;flex-shrink:0;margin-left:1rem"
                :aria-checked="preferences.comfort"
                role="switch">
                <span :style="preferences.comfort ? 'left:22px' : 'left:2px'" style="position:absolute;top:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:left 0.2s"></span>
            </button>
        </div>

        <div style="display:flex;gap:0.75rem;margin-top:1rem;flex-wrap:wrap">
            <button @click="showDetails = false" style="padding:0.5rem 1.25rem;border:2px solid #6b7280;background:transparent;color:#374151;border-radius:2rem;cursor:pointer;font-size:0.875rem">
                Zurück
            </button>
            <button @click="rejectAll()" style="padding:0.5rem 1.25rem;border:2px solid #6b7280;background:transparent;color:#374151;border-radius:2rem;cursor:pointer;font-size:0.875rem">
                Alle ablehnen
            </button>
            <button @click="savePreferences()" style="padding:0.5rem 1.25rem;border:2px solid #1a2e1a;background:#1a2e1a;color:#fff;border-radius:2rem;cursor:pointer;font-size:0.875rem">
                Auswahl speichern
            </button>
            <button @click="acceptAll()" style="padding:0.5rem 1.25rem;border:2px solid #c9a227;background:#c9a227;color:#fff;border-radius:2rem;cursor:pointer;font-size:0.875rem;font-weight:600">
                Alle akzeptieren
            </button>
        </div>
        <p style="font-size:0.7rem;color:#9ca3af;margin-top:0.75rem">
            Sie können Ihre Einwilligung jederzeit über den Link "Cookie-Einstellungen" im Footer widerrufen.
            <a href="/datenschutz" style="color:#c9a227">Datenschutzerklärung</a>
        </p>
    </div>
</div>

<script>
function cookieConsent() {
    return {
        decided: false,
        showDetails: false,
        preferences: { analytics: false, comfort: false },
        init() {
            const stored = localStorage.getItem('fwz_cookie_consent');
            if (stored) {
                try {
                    const data = JSON.parse(stored);
                    this.decided = true;
                    this.preferences = data.preferences || { analytics: false, comfort: false };
                    this.applyConsent();
                } catch(e) {
                    // Invalid JSON — treat as undecided, show banner
                    localStorage.removeItem('fwz_cookie_consent');
                    this.decided = false;
                }
            } else {
                this.decided = false;
            }
        },
        acceptAll() {
            this.preferences = { analytics: true, comfort: true };
            this.save();
        },
        rejectAll() {
            this.preferences = { analytics: false, comfort: false };
            this.save();
        },
        savePreferences() {
            this.save();
        },
        save() {
            localStorage.setItem('fwz_cookie_consent', JSON.stringify({
                decided: true,
                timestamp: new Date().toISOString(),
                preferences: this.preferences,
            }));
            this.decided = true;
            this.applyConsent();
        },
        applyConsent() {
            if (this.preferences.analytics) {
                console.log('Analytics consent granted');
            }
            if (this.preferences.comfort) {
                console.log('Comfort consent granted');
            }
        },
    }
}
</script>
