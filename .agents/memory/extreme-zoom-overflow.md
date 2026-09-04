---
name: Extreme zoom overflow
description: How to diagnose narrow-viewport overflow during accessibility checks.
---

At 200% zoom or very narrow CSS viewports, allow grid children and long labels to shrink and wrap. Diagnose the exact overflowing element before changing global overflow behavior.

**Why:** A responsive page can pass normal mobile widths yet still overflow because CSS Grid preserves a descendant's intrinsic min-content width. Off-screen controls can also enlarge the document even when they are not currently visible.

**How to apply:** Compare document scroll width with client width, then inspect element bounding boxes and grid descendants. Prefer `min-width: 0` and safe text wrapping over hiding horizontal overflow on `body`.