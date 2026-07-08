---
name: Filament smart CSV import (multi-step wizard with column mapping)
description: Reusable pattern for a self-service CSV import (auto-detect delimiter, user maps columns, updateOrCreate dedup) built as a plain Filament Action, not the Importer/ImportAction system
---

## Filament `Action` supports wizard steps natively
`Filament\Actions\Action` (via `Concerns\HasWizard`) has a `->steps([Step::make(...)->schema([...]), ...])` method that internally calls `->form($steps)`. All step fields share one `$data` array delivered to `->action(function (array $data) {...})` — no need for session/cache to pass state between steps.

**Why:** This means a multi-step "upload file → map columns → import" flow can be built as a single custom Action, avoiding Filament's built-in `Importer`/`ImportAction` class system (which does its own column-mapping UI but processes rows via queued jobs and has no built-in per-row dedup — `resolveRecord()` returning `new Model()` always inserts, never updates).

**How to apply:** Use this approach when the CSV format is inconsistent/unknown ahead of time (unknown delimiter, unknown/renamed headers) and you want synchronous updateOrCreate-based dedup instead of Filament's async import job pipeline.

## Reactive column options across wizard steps
A `Filament\Forms\Components\Select` in a later step can read a `FileUpload` field's live state from an earlier step via `->options(function (Get $get) { $get('csv_file') ... })`, as long as the FileUpload has `->live()` and `->storeFiles(false)` (so `$get()` returns the in-memory `TemporaryUploadedFile` instance immediately, without waiting for a page submit/store round-trip).

**Why:** `storeFiles(false)` keeps the upload as a `Livewire\Features\SupportFileUploads\TemporaryUploadedFile` in Livewire's component state rather than persisting to a disk, so its real path is available synchronously to sibling closures during the same request via `getRealPath()`.

**How to apply:** Reuse this for any "upload then configure based on file contents" UI (CSV column mapping, spreadsheet header detection, etc.) — re-parse the file inside each closure (options/default/content) since Filament doesn't cache the result between them automatically; keep parsing cheap or memoize per-request if the file is large.
