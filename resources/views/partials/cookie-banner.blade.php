<div x-data="cookieConsent()" style="position:fixed;inset:0;z-index:99999;pointer-events:none">

    <!-- Overlay + centering — x-show here so Alpine toggles this flex div, not the outer -->
    <div
        x-show="!decided"
        x-cloak
        style="position:fixed;inset:0;display:flex;align-items:center;justify-content:center;padding:1.5rem;background:rgba(0,0,0,0.6);pointer-events:all"
        role="dialog"
        aria-modal="true"
        aria-label="Cookie-Einstellungen">

        <!-- Modal card -->
        <div style="background:#ffffff;border-radius:1rem;padding:2rem;width:100%;max-width:480px;box-shadow:0 25px 50px rgba(0,0,0,0.4);max-height:85vh;overflow-y:auto">

            <!-- Header -->
            <div style="margin-bottom:1.5rem">
                <h2 style="margin:0 0 0.5rem;font-size:1.25rem;color:#1a2e1a;font-weight:700">Cookie-Einstellungen</h2>
                <p style="margin:0;font-size:0.875rem;color:#374151;line-height:1.5">
                    Wir verwenden Cookies, um Ihnen die bestmögliche Nutzung unserer Website zu ermöglichen.
                    Technisch notwendige Cookies werden immer gesetzt.
                    <a href="/datenschutz" style="color:var(--yellow)">Mehr erfahren</a>
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

            <!-- Buttons -->
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;margin-top:1.5rem">
                <button @click="accept()"
                    style="padding:0.625rem 1.25rem;border:2px solid #d1d5db;background:transparent;color:#6b7280;border-radius:2rem;cursor:pointer;font-size:0.875rem;flex:1;min-width:120px">
                    Ablehnen
                </button>
                <button @click="accept()"
                    style="padding:0.625rem 1.25rem;border:2px solid var(--yellow);background:var(--yellow);color:#1a2e1a;border-radius:2rem;cursor:pointer;font-size:0.875rem;font-weight:700;flex:1;min-width:120px">
                    Verstanden
                </button>
            </div>

            <!-- Footer note -->
            <p style="font-size:0.72rem;color:#9ca3af;margin-top:1rem;margin-bottom:0;line-height:1.5">
                Technisch notwendige Cookies können nicht deaktiviert werden, da sie für die grundlegende Funktionsfähigkeit der Website erforderlich sind.
                <a href="/datenschutz" style="color:var(--yellow)">Datenschutzerklärung</a>
            </p>

        </div>
    </div>
</div>

<script>
function cookieConsent() {
    return {
        decided: false,
        init() {
            const stored = localStorage.getItem('fwz_cookie_consent');
            if (stored) {
                try {
                    const data = JSON.parse(stored);
                    if (data && data.decided) {
                        this.decided = true;
                    }
                } catch(e) {
                    this.decided = false;
                }
            }
        },
        accept() {
            localStorage.setItem('fwz_cookie_consent', JSON.stringify({
                decided: true,
                timestamp: new Date().toISOString()
            }));
            this.decided = true;
        }
    }
}
</script>
