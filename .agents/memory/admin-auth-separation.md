---
name: Admin authentication separation
description: Why FWZ-admin members synchronize to Filament users while member and admin login areas remain isolated.
---

FWZ-admin members are the source for managed admin identities, but Filament continues authenticating through the `users` provider. Approved members with the `admin` role synchronize to a linked admin user. Only explicitly marked admin users may access the Filament panel.

**Why:** The project has separate `members` and `users` authentication providers. Creating an “FWZ Admin” as a member previously did not update the Filament user password, so the same email could exist in both tables with different credentials. Allowing every `users` record into Filament also provided no explicit admin boundary.

**How to apply:** Changes to an approved admin member must keep the linked Filament user identity and password synchronized. Removing approval, changing the admin role, or deleting the member must revoke admin access. Admin members must not authenticate through the regular member portal.