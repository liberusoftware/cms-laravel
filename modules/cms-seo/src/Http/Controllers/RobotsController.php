<?php

declare(strict_types=1);

namespace Liberu\Cms\Seo\Http\Controllers;

use Illuminate\Http\Response;

/**
 * Renders robots.txt from the configured crawl groups and appends the sitemap
 * location so crawlers can discover it.
 */
final readonly class RobotsController
{
    public function __invoke(): Response
    {
        $groups = config('cms-seo.robots.groups', []);
        $groups = is_array($groups) ? $groups : [];

        $lines = [];

        foreach ($groups as $group) {
            if (! is_array($group)) {
                continue;
            }

            $userAgent = $group['user_agent'] ?? '*';
            $lines[] = 'User-agent: '.(is_string($userAgent) ? $userAgent : '*');

            $disallow = $group['disallow'] ?? [];

            if (is_array($disallow)) {
                foreach ($disallow as $path) {
                    if (is_string($path)) {
                        $lines[] = 'Disallow: '.$path;
                    }
                }
            }

            $lines[] = '';
        }

        $lines[] = 'Sitemap: '.url('/sitemap.xml');

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain');
    }
}
