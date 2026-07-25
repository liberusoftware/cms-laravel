<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | robots.txt
    |--------------------------------------------------------------------------
    |
    | Groups rendered into robots.txt. Each group names a user agent and the
    | paths it may not crawl. The sitemap URL is appended automatically.
    |
    */

    'robots' => [
        'groups' => [
            [
                'user_agent' => '*',
                'disallow' => ['/admin', '/app', '/livewire'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta defaults
    |--------------------------------------------------------------------------
    |
    | Fallbacks for the <head> meta tags. `site_name` defaults to the app name
    | when null; `twitter` is the site's @handle.
    |
    */

    'meta' => [
        'site_name' => env('SEO_SITE_NAME'),
        'default_description' => env('SEO_DEFAULT_DESCRIPTION', ''),
        'twitter' => env('SEO_TWITTER_HANDLE'),
    ],

];
