# Context Map

The CMS is a multi-context system; each context is a Composer package under `modules/cms-*`. This map lists the contexts that have a documented glossary. Contexts gain a `CONTEXT.md` lazily, as their language is resolved during design — absence here means "not yet documented," not "no such context."

The stable surface a third-party extension may build against is catalogued in [docs/EXTENSION-API.md](./docs/EXTENSION-API.md) (the `@api` contracts + the semver/deprecation policy), guarded by `tests/Feature/Cms/PublicApiTest.php`.

## Contexts

- [CMS API](./modules/cms-api/CONTEXT.md) — headless HTTP delivery layer serving published content to decoupled frontends
- [CMS Search](./modules/cms-search/CONTEXT.md) — full-text search over published content, with a swappable index driver (database default, Meilisearch/Scout in production)
- [CMS Observability](./modules/cms-observability/CONTEXT.md) — readiness health checks and a backend-agnostic metrics recorder seam

## Relationships

- **CMS API → content modules** (cms-pages, cms-posts, cms-menus, cms-content-types): each content module registers its API endpoints and Eloquent Resources into the Delivery API via `ApiResourceRegistryInterface`; the API package never imports a module.
- **CMS API → host tenancy**: the API resolves the tenant from the Delivery token into a request tenant context that the host's `TenantModelResolverInterface` implementation reads. See `docs/adr/0001-delivery-api-auth-and-tenancy.md`.
- **CMS Observability ← feature modules** (cms-search, cms-media): modules contribute readiness health checks via `HealthCheckRegistryInterface` and record metrics via `MetricsRecorderInterface`; observability never imports a module, and modules never import observability. See `docs/adr/0003-observability-as-seams.md`.
- **CMS Search → search index driver**: the `/api/v1/search` surface delegates matching to a `SearchIndexInterface` driver (`DatabaseSearchIndex` default, `ScoutSearchIndex`/Meilisearch opt-in), so the query surface and result shape are stable across backends.
