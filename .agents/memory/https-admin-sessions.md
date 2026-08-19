---
name: HTTPS admin sessions
description: Session-cookie requirements for Filament behind the project's HTTPS reverse proxy.
---

For the HTTPS-hosted admin panel, session cookies must be explicitly marked secure while retaining a `lax` SameSite policy.

**Why:** Filament regenerates the Laravel session after a successful login. If the browser does not retain the replacement cookie across the HTTPS proxy, Livewire's follow-up request can use a stale CSRF/session pair and show a 419 “page expired” modal.

**How to apply:** Keep `SESSION_SECURE_COOKIE=true` and `SESSION_SAME_SITE=lax` in the environment used by the HTTPS application. Leave CSRF protection enabled; do not work around this with a CSRF exclusion.