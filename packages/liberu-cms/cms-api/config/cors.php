<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Delivery API CORS
|--------------------------------------------------------------------------
|
| The Delivery API owns CORS for its own paths so a browser app on another
| origin can call it. These values are merged into the host's `cors` config
| (the host's own settings win), enabling CORS on the versioned API group.
|
*/

return [
    'paths' => ['api/v1/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
