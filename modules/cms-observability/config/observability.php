<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Readiness
    |--------------------------------------------------------------------------
    |
    | The public `GET /health/ready` probe. `throttle` is the per-IP request
    | budget per minute (it touches the database, cache, and queue). `critical`
    | overrides each check's default criticality: a critical check that fails
    | returns 503 and pulls the instance out of rotation, a non-critical one only
    | downgrades the reported status to "degraded" while still serving 200.
    |
    */

    'readiness' => [

        'throttle' => (int) env('CMS_READINESS_THROTTLE', 60),

        'critical' => [
            'database' => true,
            'cache' => false,
            'queue' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics
    |--------------------------------------------------------------------------
    |
    | The metrics recorder seam. Enabled by default, it writes one structured
    | line per metric to its own isolated log channel so it never pollutes the
    | application log — exercising the seam out of the box at no cost. Bind your
    | own recorder (Pulse / StatsD / Prometheus) for real aggregation, or set
    | `enabled` to false to swap in the no-op recorder.
    |
    */

    'metrics' => [

        'enabled' => (bool) env('CMS_METRICS_ENABLED', true),

        'channel' => (string) env('CMS_METRICS_CHANNEL', 'cms-metrics'),

    ],

];
