---
name: Organisation category assignment
description: Behaviour rule for assigning categories to multiple organisations in administration.
---

Bulk category assignment adds the selected existing category to every selected organisation without replacing any categories already assigned.

**Why:** An organisation can belong to multiple activity areas, so a bulk operation should extend classification rather than silently remove existing classifications.

**How to apply:** Keep the action limited to existing categories, require an explicit category selection, and use additive relation updates. Verify the selected records before confirming the operation.