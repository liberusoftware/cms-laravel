<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Search driver
    |--------------------------------------------------------------------------
    |
    | The index backend that executes searches. `database` is a portable `LIKE`
    | over the content repositories — zero-infra and the default. `scout` routes
    | Scout-aware sources through Laravel Scout (Meilisearch) in production. The
    | query surface, result shape, and ranking are identical either way.
    |
    */

    'driver' => env('SEARCH_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Minimum query length
    |--------------------------------------------------------------------------
    |
    | Queries shorter than this are rejected with a 422 to avoid matching
    | everything on a single character.
    |
    */

    'min_query_length' => (int) env('SEARCH_MIN_QUERY_LENGTH', 2),

    /*
    |--------------------------------------------------------------------------
    | Per-source limit
    |--------------------------------------------------------------------------
    |
    | The maximum number of rows each content source returns before results are
    | merged and ranked. Bounds the work a single query can do.
    |
    */

    'per_source_limit' => (int) env('SEARCH_PER_SOURCE_LIMIT', 50),

    /*
    |--------------------------------------------------------------------------
    | Readiness
    |--------------------------------------------------------------------------
    |
    | Whether the search health check (contributed to the observability readiness
    | probe, backed by the active driver's reachability) is critical. Degraded by
    | default: the app still serves content while search is unavailable, so a
    | search hiccup never pulls the instance out of rotation.
    |
    */

    'readiness' => [
        'critical' => false,
    ],

];
