---
name: Pint empty constructors
description: A formatting edge case involving empty constructors and promoted properties.
---

Avoid an empty constructor whose only purpose is property promotion when the active Pint rules repeatedly flag both brace placement and empty-body formatting. Declare readonly properties and assign them in a non-empty constructor instead.

**Why:** The configured brace-position and single-line-empty-body fixers can both report the same empty promoted constructor, making manual formatting oscillate without passing.

**How to apply:** When this exact pair of Pint findings appears, make the constructor body meaningful through explicit assignments rather than repeatedly moving empty braces.