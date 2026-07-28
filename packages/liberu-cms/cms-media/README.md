# cms-media

The media library for Liberu CMS: secure uploads, storage, folders, and
metadata, exposed to the rest of the platform behind a content-agnostic contract.

## Public contracts (in `cms-contracts`)

| Contract | Purpose |
|----------|---------|
| `MediaRepositoryInterface` | `find` / `inFolder` / `delete`. How other modules resolve and manage media by key. |
| `MediaItemInterface` | A read-only media value: disk, path, url, file name, mime type, size, folder, metadata. |
| `MediaUploaded` (event) | Broadcast after a file is stored — for image processing, indexing, CDN warmers. |

Content modules store a media **key** and resolve it through the repository; they
never touch the media model, disk, or storage backend.

## Uploading

`StoreUpload` (a module service) validates and stores an `UploadedFile`:

```php
$media = app(\Liberu\Cms\Media\Media\StoreUpload::class)($request->file('file'), folder: 'articles');
$media->url();
```

- **Secure by default (OWASP A08):** MIME type is derived from file *contents*
  (not the client's claim) and checked against an allow-list; size is bounded.
  Violations throw `InvalidUpload`.
- Image uploads capture `width`/`height` metadata automatically.

## Config (`config/cms-media.php`)

| Key | Default | Purpose |
|-----|---------|---------|
| `disk` | `public` | Storage disk for uploads. |
| `max_size_kb` | `20480` | Maximum upload size. |
| `allowed_mime_types` | image/video/audio/doc set | Accepted content types. |
| `readiness.critical` | `false` | Whether a storage-check failure pulls the instance out of rotation (503) vs. degraded (200). |

## Events

- **Emits:** `Liberu\Cms\Contracts\Events\Media\MediaUploaded`.
- **Listens:** none.

## Readiness

Contributes a `storage` health check (a put/delete probe on the media disk) to
the observability readiness registry via `HealthCheckRegistryInterface`, guarded
by `bound()` so it is inert when observability is absent. **Degraded**, not
critical: the app still serves content while uploads are unavailable. The Media
module imports the contract only — nothing from `cms-observability`.

## Extension points / roadmap

- **Image processing** (resize, thumbnails, WebP): a `MediaUploaded` listener is
  the intended seam. Full processing needs an imaging library
  (e.g. `intervention/image`) — a dependency to add with approval.
- **CDN / remote disks** (S3, R2, Spaces): configure the `disk`.
