# CMS Search

Full-text search over **published** content, exposed on the Delivery API. The
search module aggregates results from every content module that registers a
`SearchableSourceInterface`, so it never imports a module (same pattern as the
admin, API, and sitemap registries).

## Endpoint

```
GET /api/v1/search?q=<terms>&per_page=<n>
```

Registered into the Delivery API route group, so it inherits the same auth
(Delivery token), tenant scoping, and rate limiting. Results are scoped to the
token's Team and to published content, merged across content types, ranked by
score (highest first), and paginated with the standard `data`/`meta`/`links`
envelope.

- A missing or too-short `q` returns `422` (minimum length from
  `config('cms-search.min_query_length')`).
- Each source returns at most `config('cms-search.per_source_limit')` rows before
  ranking.

### Result shape

```json
{
  "data": [
    { "type": "page", "id": 12, "title": "About", "slug": "about", "excerpt": "…", "score": 2.0 }
  ],
  "meta": { "current_page": 1, "per_page": 15, "total": 1 },
  "links": {}
}
```

`type` + `slug` let the consumer build its own link to the underlying content.

## Adding a searchable content type

A content module registers a source in its `bootModule()` (boot phase, because
the search module's registry binds after the content modules load):

```php
if ($this->app->bound(SearchRegistryInterface::class)) {
    $this->app->make(SearchRegistryInterface::class)
        ->registerSource($this->app->make(PageSearchSource::class));
}
```

## Driver seam

Matching is delegated to a `SearchIndexInterface` driver that sits *below* the
sources, selected by `config('cms-search.driver')`. It changes *how* search runs,
never the query surface, `SearchResult` shape, or ranking:

| Driver | `driver` | Backend | Default |
|--------|----------|---------|---------|
| `DatabaseSearchIndex` | `database` | Portable `LIKE` over the content repositories, ranked by `SearchScoring` (title 2.0 > body 1.0). Zero-infra. | ✅ |
| `ScoutSearchIndex` | `scout` | Meilisearch via Laravel Scout. | opt-in |

Under the Scout driver a source is queried through Scout only if it implements
`ScoutSearchableSourceInterface` (adding `scoutSearch()` over a Scout `Searchable`
model); any other source keeps its database `search()`, so enabling Scout is safe
before every module has adopted it.

`isReady()` reports the active backend's reachability for the readiness probe. For
the database and collection engines that is genuine reachability; for a remote
Meilisearch backend it confirms the engine is *configured* — a deep index ping is
a **documented deferral** (unverifiable on this stack, same honesty bar as Redis /
Octane). An operator wanting a true remote signal binds their own probe.

**No Meilisearch is bundled or required.** The default is zero-infra database
search; Scout wiring is proven in CI on Scout's in-memory collection engine, never
a Meilisearch service (bind it in production).

## Observability

- **Readiness:** contributes a degraded `search` health check (backed by the
  active driver's `isReady()`) via `HealthCheckRegistryInterface`. Criticality is
  owned by `cms-search.readiness.critical` (default `false`).
- **Metrics:** the controller records `search.query` (count), `search.latency`
  (timing), and `search.results` (gauge) via `MetricsRecorderInterface`,
  `bound()`-guarded. The Search module imports nothing from `cms-observability`.
