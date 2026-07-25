<?php

declare(strict_types=1);

return [

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

];
