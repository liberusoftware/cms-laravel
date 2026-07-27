# CMS Search

Full-text search over published content, exposed on the Delivery API at
`/api/v1/search`. Content modules register searchable sources; the actual matching is
delegated to a swappable index driver so the same query surface runs on a plain
database in development and on Meilisearch in production.

## Language

**Searchable source**:
The contract (`SearchableSourceInterface`) through which a content module contributes
its own model to search — mapping a hit to a `SearchResult` — without the search
package importing the module.
_Avoid_: provider, indexable, source model

**Search index driver**:
The swappable implementation (`SearchIndexInterface`) *below* the sources that actually
executes a query and reports whether its backend is reachable (`isReady()`). Selected
by `cms-search.driver`; it changes *how* search runs, never the query surface or the
result shape.
_Avoid_: search engine, backend, adapter

**Database driver**:
The default `SearchIndexInterface` implementation (`DatabaseSearchIndex`) — a portable
`LIKE` over the published-content repositories, ranked by `SearchScoring`. Zero-infra;
the standalone/embedded default.
_Avoid_: SQL search, fallback, LIKE search

**Scout driver**:
The opt-in `SearchIndexInterface` implementation (`ScoutSearchIndex`) backed by
Meilisearch through Laravel Scout. Enabled only in production; models become
`Searchable` when it is active.
_Avoid_: Meilisearch driver, Scout engine, external search

**Search scoring**:
The relevance ranking applied to database-driver results (a title match outranks a body
match). Owned by the search package, not the driver's backend.
_Avoid_: relevance, weighting, boost
