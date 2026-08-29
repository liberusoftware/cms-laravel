<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContentApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class EventResource extends JsonResource
{
    public function toArray($request): array
    {
        $event = $this->resource;

        return ['id' => (string) $event->getKey(), 'type' => 'cms-events-content', 'key' => $event->key, 'title' => $event->title, 'description' => $event->description, 'status' => $event->status, 'starts_at' => $event->starts_at?->toISOString(), 'ends_at' => $event->ends_at?->toISOString(), 'timezone' => $event->timezone, 'venue' => $event->venue?->only(['name', 'address', 'timezone']), 'sessions' => $event->sessions?->map(fn ($session): array => ['key' => $session->key, 'title' => $session->title, 'starts_at' => $session->starts_at?->toISOString(), 'ends_at' => $session->ends_at?->toISOString(), 'room' => $session->room, 'speakers' => $session->speakers?->map(fn ($speaker): array => ['name' => $speaker->name, 'bio' => $speaker->bio])->values()])->values(), 'registrations' => $event->registrations?->map(fn ($registration): array => ['provider' => $registration->provider, 'external_key' => $registration->external_key, 'url' => $registration->url, 'status' => $registration->status])->values(), 'structured_data' => $event->structured_data, 'created_at' => $event->created_at?->toISOString(), 'updated_at' => $event->updated_at?->toISOString()];
    }
}
