---
name: Leaflet map assets and popup escaping
description: Reliable external Leaflet loading and safe dynamic marker popup content.
---

For Leaflet CDN assets, verify any Subresource Integrity value against the exact fetched version. For dynamic marker popups, escape text through a normal detached DOM element and read its `innerHTML`; do not rely on `HTMLTemplateElement.textContent` for this transformation.

**Why:** A stale or fabricated CSS SRI hash causes the browser to block Leaflet's stylesheet completely. Assigning text through a template element can leave the popup's serialized text and link targets empty, so a marker appears to have only icons and broken links.

**How to apply:** When adding or upgrading a Leaflet CDN version, calculate the SRI hash from the served file before shipping. Generate popup markup only after escaping all dynamic text and URL attributes with a normal element; validate external website schemes server-side and keep `noopener` on new-tab links.