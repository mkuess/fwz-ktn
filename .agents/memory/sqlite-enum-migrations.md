---
name: SQLite enum column migrations
description: How to safely add/convert enum columns on SQLite-backed Laravel apps without hitting doctrine/dbal issues
---

Adding a brand-new `enum()` column via `Schema::table(...)->enum(...)` works fine on SQLite (it's a direct `ADD COLUMN`, no doctrine/dbal diffing involved).

Converting an **existing** column to `enum()` via `->change()` is riskier: SQLite has no native ALTER-COLUMN-TYPE, so Laravel routes `->change()` through doctrine/dbal to rebuild the table, and doctrine's enum-type support on SQLite is inconsistent (`Unknown database type enum requested` errors are a known failure mode).

**Why:** Simple `->change()` calls that only touch nullability or default on well-known types (string, foreignId) work reliably in this stack (confirmed via a migration that ran `foreignId(...)->nullable()->change()` without issue) — the risk is specifically the `enum` type going through doctrine's schema diff.

**How to apply:** When a column needs to become an enum and it already exists (e.g. was previously a plain nullable `string`), don't `->change()` it. Instead, in one migration: backfill/normalize existing values with `DB::table(...)->update(...)` first, then `Schema::table` to `dropColumn` the old column, then a second `Schema::table` call to re-add it as `enum(...)->default(...)`. This sidesteps doctrine/dbal entirely (drop + add are both direct SQL on SQLite 3.35+, which supports native `DROP COLUMN`). Requires no data loss since the value backfill happens before the drop.
