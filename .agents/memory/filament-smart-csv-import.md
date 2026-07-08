---
name: Filament smart CSV import (multi-step wizard with column mapping)
description: Reusable pattern for a self-service CSV import (auto-detect delimiter, user maps columns, updateOrCreate dedup) built as a plain Filament Action, not the Importer/ImportAction system
---

## Filament `Action` supports wizard steps natively
`Filament\Actions\Action` (via `Concerns\HasWizard`) has a `->steps([Step::make(...)->schema([...]), ...])` method that internally calls `->form($steps)`. All step fields share one `$data` array delivered to `->action(function (array $data) {...})` — no need for session/cache to pass state between steps.

**Why:** This means a multi-step "upload file → map columns → import" flow can be built as a single custom Action, avoiding Filament's built-in `Importer`/`ImportAction` class system (which does its own column-mapping UI but processes rows via queued jobs and has no built-in per-row dedup — `resolveRecord()` returning `new Model()` always inserts, never updates).

**How to apply:** Use this approach when the CSV format is inconsistent/unknown ahead of time (unknown delimiter, unknown/renamed headers) and you want synchronous updateOrCreate-based dedup instead of Filament's async import job pipeline.

## Reactive column options across wizard steps
A `Filament\Forms\Components\Select` in a later step can read a `FileUpload` field's live state from an earlier step via `->options(function (Get $get) { $get('csv_file') ... })`, as long as the FileUpload has `->live()` and `->storeFiles(false)` (so the file is available synchronously in the same request without a disk round-trip).

**Why:** `storeFiles(false)` keeps the upload as a `Livewire\Features\SupportFileUploads\TemporaryUploadedFile` rather than persisting it to a disk.

**Gotcha — `$get()` returns the raw, non-dehydrated state, which is an array, not the file itself.** Filament's `BaseFileUpload` always stores its internal state as `[uuid => TemporaryUploadedFile]`, *even for a single non-multiple upload* — the "give me just one file" behavior (`Arr::first($state)`) only happens in `dehydrateStateUsing()`, which runs when the form is submitted and populates `$data` in the action closure. Any `Get $get` call made *inside* step closures (`options()`, `default()`, `content()`, etc., which run mid-form before submission) gets the raw keyed array instead. Always unwrap with something like `is_array($file) ? Arr::first($file) : $file` before treating the value as a single file/`TemporaryUploadedFile`, or headers/options silently stay empty with no error.

**How to apply:** Reuse this for any "upload then configure based on file contents" UI (CSV column mapping, spreadsheet header detection, etc.) — re-parse the file inside each closure since Filament doesn't cache the result between them; keep parsing cheap or memoize per-request if the file is large. Also add `skipInputBOM()`/explicit BOM stripping and `str_getcsv()`/League\Csv enclosure settings for quoted headers, since real-world exports (Excel, etc.) often include a BOM or quote every field.

## Debugging "0 imported, all skipped (unknown reason)"
When a user reports every row skipped with no clear reason, the real uploaded file is almost always sitting in `storage/app/private/livewire-tmp/*-meta<base64(original_filename)>-.csv` (Livewire keeps temp uploads there during the request/session). Decode the base64 segment in the filename to identify which upload it is, then read its actual first line directly (`fopen`/`fgets`, respecting the real delimiter) instead of trusting what the resource's field-mapping list assumes the columns are called.

**Why:** The failure was caused by the resource's `importRow` closure hard-requiring columns (e.g. `type`, `password`) that didn't exist at all in the real export, silently returning bare `false` for every row with no diagnostic. Guessing at the schema from the code wastes turns; the ground truth is the byte content of the uploaded file.

**How to apply:** For any "all rows skipped" CSV bug, first locate and read the actual livewire-tmp file before touching the importRow logic. Make `importRow` return a `string` skip-reason (not just `bool`) so `SmartCsvImportAction` can aggregate a breakdown like "12 imported, 3 skipped (2 missing name, 1 invalid email)" instead of an opaque count — and only treat truly indispensable fields (e.g. `name`) as hard-required, auto-filling/defaulting everything else (password, type, email placeholder) rather than rejecting the row.

## Bulk-import timeouts are almost always bcrypt cost, not I/O
Hashing ~450 auto-generated passwords at Laravel's default bcrypt cost (12 rounds, `BCRYPT_ROUNDS` in `.env`) took ~90s in benchmarking — comfortably blowing PHP's 30s `max_execution_time` — while the same count at `['rounds' => 4]` took under 1s.

**Why:** Auto-generated import passwords are throwaway (user resets on first login anyway), so paying full bcrypt cost for them is pure waste. `Hash::isHashed()` treats any valid bcrypt string (any cost) as already-hashed, so pre-hashing with `Hash::make($value, ['rounds' => 4])` before assigning to a `'hashed'`-cast attribute is safe — Eloquent won't re-hash it at the model's default cost.

**How to apply:** For any bulk import/seed path that generates-and-hashes many passwords, lower the bcrypt cost explicitly for the generated value; also add `set_time_limit(300)` at the start of the import action and batch DB writes in chunks (e.g. `array_chunk` + one `DB::transaction` per chunk) to cut per-row commit overhead.

## CSV encoding: garbled German umlauts mean raw Windows-1252 bytes, not double-encoding
Real-world German/Austrian CSV exports (Excel) are frequently Windows-1252, not UTF-8. If read as UTF-8 without conversion, umlauts (ü/ö/ä/ß) become single high-byte characters that fail `mb_check_encoding($v, 'UTF-8')` — this is different from the "mojibake" pattern (literal `?`/`�`) and can't be fixed by a naive re-encode of already-broken data.

**Why:** `mb_detect_encoding($content, 'UTF-8', true) === false` is a reliable one-shot check for "this file is not valid UTF-8"; converting such content from Windows-1252 (the common case for AT/DE exports) to UTF-8 fixes it in one pass. Data already inserted before the fix must be repaired in-place per affected text column via `mb_check_encoding()`-gated `mb_convert_encoding($v, 'UTF-8', 'Windows-1252')` UPDATEs — never blanket-reconvert every row, since already-correct UTF-8 rows would get corrupted by a second conversion pass.

**How to apply:** Centralize encoding normalization in one shared helper called before any CSV parsing (so every importer benefits automatically), and when fixing historical bad data, gate each column update on `!mb_check_encoding($value, 'UTF-8')` so only genuinely broken values are touched.
