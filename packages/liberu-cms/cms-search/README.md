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

## Driver

v1 uses a database `LIKE` query per source. The `SearchableSourceInterface` seam
lets a source swap in a dedicated engine (e.g. Laravel Scout) later without
changing the API.
