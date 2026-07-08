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
