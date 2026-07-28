<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Storage disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk uploaded media is stored on.
    |
    */

    'disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Upload constraints (secure uploads — OWASP A08)
    |--------------------------------------------------------------------------
    |
    | Maximum size in kilobytes and the MIME types accepted. Anything outside
    | these bounds is rejected before it is stored.
    |
    */

    'max_size_kb' => 20480,

    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'video/mp4',
        'audio/mpeg',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ],

    /*
    |--------------------------------------------------------------------------
    | Readiness
    |--------------------------------------------------------------------------
    |
    | Whether the media storage health check (contributed to the observability
    | readiness probe) is critical. Degraded by default: the app still serves
    | content while uploads are unavailable, so a storage hiccup never pulls the
    | instance out of rotation.
    |
    */

    'readiness' => [
        'critical' => false,
    ],

];
