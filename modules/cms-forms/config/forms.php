<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Honeypot field
    |--------------------------------------------------------------------------
    |
    | The name of a hidden field that real users leave empty. A submission that
    | fills it is treated as a bot and silently discarded.
    |
    */

    'honeypot' => env('FORMS_HONEYPOT_FIELD', '_hp'),

    /*
    |--------------------------------------------------------------------------
    | Rate limit
    |--------------------------------------------------------------------------
    |
    | Submissions per minute allowed per client IP.
    |
    */

    'rate_limit' => (int) env('FORMS_RATE_LIMIT', 10),

    /*
    |--------------------------------------------------------------------------
    | Success message
    |--------------------------------------------------------------------------
    */

    'success_message' => 'Thank you for your submission.',

];
