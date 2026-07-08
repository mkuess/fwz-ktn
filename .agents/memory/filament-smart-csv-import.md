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
