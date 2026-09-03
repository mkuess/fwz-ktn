---
name: Filament upload previews
description: Preventing endless FilePond loading states for existing public uploads behind a custom domain.
---

Existing public uploads shown in Filament should use a same-origin `/storage/...` preview URL instead of relying on the filesystem disk's absolute URL.

**Why:** Behind a custom domain, an absolute disk URL can point at a different configured host. FilePond then knows the stored filename but remains at “Laden / Dateigröße berechnen” because it cannot complete the preview request, even though the public file exists.

**How to apply:** Keep the upload on the public disk, verify the stored file exists, URL-encode each path segment, and return a root-relative preview URL from the upload component's existing-file metadata callback.