# CMS API — Delivery API

A versioned (`/api/v1`), read-only, Sanctum-authenticated JSON API that serves
**published** CMS content to decoupled frontends. This package owns the route
group, authentication, the request tenant context, per-token rate limiting, and
CORS. Each content module contributes its own endpoints and Eloquent Resources
through `ApiResourceRegistryInterface`, so this package never imports a content
module.

## Authentication

Every request must present a **Delivery token** as a bearer token:

```
Authorization: Bearer <token>
```

A Delivery token is a Sanctum token whose tokenable is a **Team** — the tenant.
Presenting it both authenticates the request and scopes every read to that one
Team's published content. A missing or invalid token returns `401`.

### Issuing a token

```
php artisan cms-api:issue-token {team} [--name=delivery] [--write]
```

Prints the plaintext token once. Store it immediately; it is not recoverable.
Tokens are read-only (`content:read`) by default; pass `--write` to also grant
`content:write` for the create/update/delete endpoints.

### Abilities

- **Read** endpoints require only a valid token.
- **Write** endpoints (`POST`/`PUT`/`DELETE`) require the `content:write`
  ability; a read-only token receives `403`.

### Revoking a token

Delete the corresponding row from `personal_access_tokens` (e.g. via
`$team->tokens()->where('id', $id)->delete()`), or delete all of a Team's tokens
with `$team->tokens()->delete()`.

## Tenancy & visibility

- Reads are scoped to the token's Team automatically — you only ever see your own
  content.
- Only **published** content is delivered. Draft / review / archived content, a
  resource belonging to another tenant, and a non-existent resource are all
  indistinguishable: each returns `404`, so existence is never leaked across
  tenants.

## Rate limiting

The `/api/v1` group is throttled per token (default 60 requests/minute, see
`config('cms-api.rate_limit')`). Exceeding the limit returns `429` with a
`Retry-After` header.

## Pagination

Collection endpoints return the standard `data` / `meta` / `links` envelope.
Control the page size with `?per_page=` up to the cap in
`config('cms-api.pagination.max')` (default 100).

## Routes (v1)

| Method | URI | Description |
| ------ | --- | ----------- |
| GET | `/api/v1/pages` | Published pages |
| GET | `/api/v1/pages/{slug}` | A published page by slug |
| GET | `/api/v1/posts` | Published posts, newest first (`?category=`, `?tag=`, `?per_page=`) |
| GET | `/api/v1/posts/{slug}` | A published post by slug (categories, tags, featured media inline) |
| GET | `/api/v1/menus/{location}` | A menu by location, with its ordered item tree |
| GET | `/api/v1/content/{type}` | Published entries of a content type |
| GET | `/api/v1/content/{type}/{slug}` | A published content entry by slug |
| POST | `/api/v1/pages` · `/posts` · `/content-entries` | Create content (requires `content:write`) |
| PUT | `/api/v1/pages/{id}` · `/posts/{id}` · `/content-entries/{id}` | Update content (requires `content:write`) |
| DELETE | `/api/v1/pages/{id}` · `/posts/{id}` · `/content-entries/{id}` | Delete content (requires `content:write`) |

Writes are tenant-stamped on create, tenant-scoped on update/delete (a
cross-tenant id is `404`), and a `status` change is applied through the
editorial workflow — an illegal transition returns `422`.

Exact routes depend on which content modules are installed; a headless
deployment can ship a subset of modules and the API exposes only their
endpoints.

## CORS

The package enables CORS for `api/v1/*` by merging its own `cors` config into the
host's (the host's own `config/cors.php` values win), so a browser app on another
origin can call the API. When running with `config:cache`, publish the host CORS
config so the settings persist.
