---
name: Public registration settings
description: Rules for managing the availability of public member and organisation sign-up flows.
---

Public registration availability is controlled by two independent persistent settings: one for members and one for organisations. Read setting values through the shared cached access path, and invalidate that cache immediately after a setting is changed.

**Why:** The same availability state is needed by route protection and several shared public views. Individual database lookups in each Blade conditional multiply identical queries and can make the website depend unnecessarily on repeated settings-table access.

**How to apply:** New public sign-up entry points must check the matching setting both before rendering a link and on the protected server-side endpoint. Use the shared settings access/update methods rather than querying or caching the flags independently.