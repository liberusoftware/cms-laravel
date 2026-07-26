<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Per-token rate limit
    |--------------------------------------------------------------------------
    |
    | Requests per minute allowed on the /api/v1 group, keyed by the token's
    | Team (or the client IP for anything not yet authenticated). Exceeding it
    | yields a 429 with a Retry-After header.
    |
    */

    'rate_limit' => (int) env('CMS_API_RATE_LIMIT', 60),

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | The default page size and the hard cap a consumer may request via the
    | `per_page` query parameter.
    |
    */

    'pagination' => [
        'default' => (int) env('CMS_API_PER_PAGE', 15),
        'max' => (int) env('CMS_API_MAX_PER_PAGE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Preview links
    |--------------------------------------------------------------------------
    |
    | How long a minted preview link stays valid, in minutes. The link is a
    | signed URL scoped to a single draft-inclusive item and its tenant; once it
    | expires the signature no longer validates and the link 403s.
    |
    */

    'preview' => [
        'ttl' => (int) env('CMS_API_PREVIEW_TTL', 60),
    ],

];
