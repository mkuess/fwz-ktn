---
name: Mobile footer anchors
description: Reliable in-page footer navigation from the Alpine-powered mobile menu.
---

When an in-page anchor is clicked inside the full-screen Alpine mobile menu, prevent the native click, close the overlay first, preserve the URL fragment explicitly, and scroll only in Alpine's next render tick.

**Why:** Native fragment navigation can scroll the document underneath the still-open full-screen drawer. Preventing that navigation fixes the overlay order but also suppresses the URL hash unless it is restored explicitly.

**How to apply:** For links inside the mobile drawer that target elements on the same page, update the menu state and browser history before using `$nextTick` to scroll the target into view. Desktop links can remain ordinary fragment links.