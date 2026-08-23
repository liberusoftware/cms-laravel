<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Support;

use Illuminate\Support\Facades\URL;

/**
 * Mints signed, expiring preview links for a single content item. The link
 * carries the type, id, and owning tenant — all covered by the signature — so it
 * grants view of exactly one draft-inclusive item and nothing else, and stops
 * working once it expires.
 */
final class PreviewLink
{
    public function for(string $type, int $id, int|string|null $team, ?int $ttlMinutes = null): string
    {
        $configured = config('cms-api.preview.ttl', 60);
        $ttl = $ttlMinutes ?? (is_numeric($configured) ? (int) $configured : 60);

        return URL::temporarySignedRoute(
            'cms-api.preview',
            now()->addMinutes($ttl),
            ['type' => $type, 'id' => $id, 'team' => $team],
        );
    }
}
