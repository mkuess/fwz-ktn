---
name: Admin authentication separation
description: How FWZ admins use both the member portal and the separately authorized Filament administration.
---

FWZ-admin members are the source for managed admin identities. Approved members with the `admin` role may log into the normal member portal and also synchronize to a linked admin user for Filament. Only explicitly marked admin users may access the Filament panel.

**Why:** Admin status adds administrative access; it must not remove the person's normal member access. The project still has separate `members` and `users` authentication providers, so access to the member portal does not itself grant access to Filament.

**How to apply:** Let approved admin members authenticate through the regular member guard like other approved members. Keep the linked Filament user identity and password synchronized. Removing approval, changing the admin role, or deleting the member must still revoke administrative access.