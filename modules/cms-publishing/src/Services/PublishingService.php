<?php

declare(strict_types=1);

namespace Liberu\Cms\Publishing\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Publishing\Events\PublicationReleaseChanged;
use Liberu\Cms\Publishing\Models\PublicationRelease;
use Liberu\Cms\Publishing\Models\PublicationReleaseEvent;

final class PublishingService
{
    public function create(array $attributes): PublicationRelease
    {
        if (blank($attributes['key'] ?? null)) {
            throw ValidationException::withMessages(['key' => 'A release key is required.']);
        }

        return PublicationRelease::query()->create([...$attributes, 'state' => $attributes['state'] ?? 'scheduled']);
    }

    public function schedule(PublicationRelease $release): PublicationRelease
    {
        $this->ensureState($release, ['draft', 'scheduled', 'unpublished'], 'schedule');
        if ($release->embargo_until !== null && $release->publish_at !== null && $release->embargo_until->greaterThan($release->publish_at)) {
            throw ValidationException::withMessages(['embargo_until' => 'An embargo cannot end after publication.']);
        }
        if ($release->expires_at !== null && $release->publish_at !== null && $release->expires_at->lessThanOrEqualTo($release->publish_at)) {
            throw ValidationException::withMessages(['expires_at' => 'Expiry must be after publication.']);
        }

        $release->forceFill(['state' => 'scheduled'])->save();

        return $this->record($release, 'scheduled');
    }

    public function publish(PublicationRelease $release): PublicationRelease
    {
        $this->ensureState($release, ['scheduled', 'unpublished'], 'publish');
        if ($release->embargo_until?->isFuture()) {
            throw ValidationException::withMessages(['embargo_until' => 'The release is still embargoed.']);
        }
        $release->forceFill(['state' => 'published', 'publish_at' => $release->publish_at ?? now()])->save();

        return $this->record($release, 'published');
    }

    public function unpublish(PublicationRelease $release): PublicationRelease
    {
        $this->ensureState($release, ['published'], 'unpublish');
        $release->forceFill(['state' => 'unpublished'])->save();

        return $this->record($release, 'unpublished');
    }

    public function archive(PublicationRelease $release): PublicationRelease
    {
        $this->ensureState($release, ['draft', 'scheduled', 'published', 'unpublished'], 'archive');
        $release->forceFill(['state' => 'archived'])->save();

        return $this->record($release, 'archived');
    }

    /** @return array<int, PublicationRelease> */
    public function processDue(): array
    {
        return DB::transaction(function (): array {
            $processed = [];
            PublicationRelease::query()->where('state', 'scheduled')->get()->each(function (PublicationRelease $release) use (&$processed): void {
                if ($release->isExpired()) {
                    $processed[] = $this->archive($release);
                } elseif ($release->isDue()) {
                    $processed[] = $this->publish($release);
                }
            });

            return $processed;
        });
    }

    private function record(PublicationRelease $release, string $event): PublicationRelease
    {
        PublicationReleaseEvent::create(['release_id' => $release->getKey(), 'event' => $event, 'payload' => ['targets' => $release->targets, 'cache_tags' => $release->cache_tags], 'occurred_at' => now()]);
        event(new PublicationReleaseChanged($release, $event));

        return $release->fresh();
    }

    /** @param list<string> $allowed */
    private function ensureState(PublicationRelease $release, array $allowed, string $transition): void
    {
        if (! in_array($release->state, $allowed, true)) {
            throw ValidationException::withMessages(['state' => "Cannot {$transition} a release from its current state."]);
        }
    }
}
