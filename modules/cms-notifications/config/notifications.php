<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Subscriptions
    |--------------------------------------------------------------------------
    |
    | Maps a CMS event name to the notifications it triggers. Each subscription
    | names a channel ("mail" or "log"), an optional recipient (`to`, a string
    | or array of addresses), and a subject. Defaults use the log channel so the
    | module works out of the box; switch to "mail" and set `to` to deliver.
    |
    */

    'subscriptions' => [

        'forms.submitted' => [
            [
                'channel' => 'log',
                'to' => env('NOTIFICATIONS_FORMS_TO'),
                'subject' => 'New form submission',
            ],
        ],

        'content.published' => [
            [
                'channel' => 'log',
                'subject' => 'Content published',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Delivery queue
    |--------------------------------------------------------------------------
    |
    | Retry policy for the SendNotification job under at-least-once delivery.
    | `tries` bounds the attempts; `backoff` is the seconds to wait before each
    | retry (the last value repeats). Delivery is idempotency-guarded on the
    | NotificationLog row, so a retry after a successful send does not re-deliver.
    |
    */

    'queue' => [
        'tries' => (int) env('NOTIFICATIONS_TRIES', 3),
        'backoff' => [10, 30, 60],
    ],

];
