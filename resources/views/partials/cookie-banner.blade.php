<div
    x-data="cookieConsent()"
    x-show="!decided"
    x-cloak
    style="position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;padding:1.5rem;background:rgba(0,0,0,0.6)"
    role="dialog"
    aria-modal="true"
    aria-label="Cookie-Einstellungen">

    <!-- Modal box -->
    <div style="background:#ffffff;border-radius:1rem;padding:2rem;width:100%;max-width:500px;box-shadow:0 25px 50px rgba(0,0,0,0.4);max-height:85vh;overflow-y:auto;position:relative;z-index:1">

        <!-- Header -->
        <div style="margin-bottom:1.5rem">
            <h2 style="margin:0 0 0.5rem;font-size:1.25rem;color:#1a2e1a;font-weight:700">Cookie-Einstellungen</h2>
            <p style="margin:0;font-size:0.875rem;color:#374151;line-height:1.5">
                Wir verwenden Cookies, um Ihnen die bestmögliche Nutzung unserer Website zu ermöglichen.
                Technisch notwendige Cookies werden immer gesetzt.
                <a href="/datenschutz" style="color:#c9a227">Mehr erfahren</a>
            </p>
        </div>

        <!-- Technisch notwendig -->
        <div style="padding:1rem 0;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:flex-start;gap:1rem">
            <div>
                <div style="font-weight:600;font-size:0.9rem;color:#111827">Technisch notwendig</div>
                <div style="font-size:0.8rem;color:#6b7280;margin-top:0.25rem">Session, Sicherheit, Grundfunktionen. Immer aktiv.</div>
            </div>
            <span style="background:#22c55e;color:#fff;padding:0.2rem 0.75rem;border-radius:1rem;font-size:0.75rem;white-space:nowrap;flex-shrink:0">Immer aktiv</span>
        </div>

        <!-- Analyse & Statistik -->
        <div style="padding:1rem 0;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:flex-start;gap:1rem">
            <div>
                <div style="font-weight:600;font-size:0.9rem;color:#111827">Analyse &amp; Statistik</div>
                <div style="font-size:0.8rem;color:#6b7280;margin-top:0.25rem">Hilft uns zu verstehen, wie Besucher unsere Website nutzen (z.B. Matomo).</div>
            </div>
            <button
                @click="preferences.analytics = !preferences.analytics"
                :aria-checked="preferences.analytics.toString()"
                role="switch"
                aria-label="Analyse und Statistik"
                :style="preferences.analytics ? 'background:#c9a227' : 'background:#d1d5db'"
                style="position:relative;display:inline-block;width:48px;height:26px;border-radius:13px;border:none;cursor:pointer;flex-shrink:0;padding:0;transition:background-color 0.2s">
                <span
                    :style="preferences.analytics ? 'left:25px' : 'left:3px'"
                    style="display:block;width:20px;height:20px;background:#ffffff;border-radius:50%;position:absolute;top:3px;transition:left 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.3)">
                </span>
            </button>
        </div>

        <!-- Komfort & Karten -->
        <div style="padding:1rem 0;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:flex-start;gap:1rem">
            <div>
                <div style="font-weight:600;font-size:0.9rem;color:#111827">Komfort &amp; Karten</div>
                <div style="font-size:0.8rem;color:#6b7280;margin-top:0.25rem">Ermöglicht eingebettete Karten und Komfortfunktionen (z.B. OpenStreetMap).</div>
            </div>
            <button
                @click="preferences.comfort = !preferences.comfort"
                :aria-checked="preferences.comfort.toString()"
                role="switch"
                aria-label="Komfort und Karten"
                :style="preferences.comfort ? 'background:#c9a227' : 'background:#d1d5db'"
                style="position:relative;display:inline-block;width:48px;height:26px;border-radius:13px;border:none;cursor:pointer;flex-shrink:0;padding:0;transition:background-color 0.2s">
                <span
                    :style="preferences.comfort ? 'left:25px' : 'left:3px'"
                    style="display:block;width:20px;height:20px;background:#ffffff;border-radius:50%;position:absolute;top:3px;transition:left 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.3)">
                </span>
            </button>
        </div>

        <!-- Buttons -->
        <div style="display:flex;flex-wrap:wrap;gap:0.75rem;margin-top:1.5rem">
            <button @click="rejectAll()"
                style="padding:0.625rem 1.25rem;border:2px solid #d1d5db;background:transparent;color:#6b7280;border-radius:2rem;cursor:pointer;font-size:0.875rem;flex:1;min-width:120px">
                Alle ablehnen
            </button>
            <button @click="savePreferences()"
                style="padding:0.625rem 1.25rem;border:2px solid #1a2e1a;background:transparent;color:#1a2e1a;border-radius:2rem;cursor:pointer;font-size:0.875rem;flex:1;min-width:120px">
                Auswahl speichern
            </button>
            <button @click="acceptAll()"
                style="padding:0.625rem 1.25rem;border:2px solid #c9a227;background:#c9a227;color:#1a2e1a;border-radius:2rem;cursor:pointer;font-size:0.875rem;font-weight:700;flex:1;min-width:120px">
                Alle akzeptieren
            </button>
        </div>

        <!-- Footer note -->
        <p style="font-size:0.72rem;color:#9ca3af;margin-top:1rem;margin-bottom:0;line-height:1.5">
            Sie können Ihre Einwilligung jederzeit über den Link „Cookie-Einstellungen" im Footer widerrufen.
            <a href="/datenschutz" style="color:#c9a227">Datenschutzerklärung</a>
        </p>

    </div>
</div>

<script>
function cookieConsent() {
    return {
        decided: false,
        preferences: {
            analytics: false,
            comfort: false
        },
        init() {
            const stored = localStorage.getItem('fwz_cookie_consent');
            if (stored) {
                try {
                    const data = JSON.parse(stored);
                    if (data && data.decided) {
                        this.decided = true;
                        this.preferences = data.preferences || { analytics: false, comfort: false };
                        this.applyConsent();
                    }
                } catch(e) {
                    this.decided = false;
                }
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
                preferences: this.preferences
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
        }
    }
}
</script>
