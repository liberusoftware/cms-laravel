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

];
