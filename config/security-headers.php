<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP Strict Transport Security (HSTS)
    |--------------------------------------------------------------------------
    |
    | Sent so browsers pin the site to HTTPS. Browsers ignore it on plain HTTP,
    | so it is safe to leave enabled in every environment. Enable "preload" only
    | once you are ready to submit the domain to the HSTS preload list.
    |
    */

    'hsts' => [
        'enabled' => env('SECURITY_HSTS_ENABLED', true),
        'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
        'include_subdomains' => (bool) env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => (bool) env('SECURITY_HSTS_PRELOAD', false),
    ],

    'frame_options' => env('SECURITY_FRAME_OPTIONS', 'DENY'),

    'content_type_options' => 'nosniff',

    'referrer_policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | Shipped in report-only mode by default: Filament and Livewire rely on
    | inline scripts and styles, so enforcing this policy outright would break
    | the admin panel. Collect reports, tune the policy, then flip
    | SECURITY_CSP_REPORT_ONLY to false to enforce it.
    |
    */

    'csp' => [
        'enabled' => (bool) env('SECURITY_CSP_ENABLED', true),
        'report_only' => (bool) env('SECURITY_CSP_REPORT_ONLY', true),
        'policy' => env('SECURITY_CSP_POLICY', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "media-src 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ])),
    ],

];
