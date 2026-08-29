<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent\Services;

use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\EventsContent\Models\Event;
use Liberu\Cms\EventsContent\Models\RegistrationReference;
use Liberu\Cms\EventsContent\Models\Session;
use Liberu\Cms\EventsContent\Models\Speaker;
use Liberu\Cms\EventsContent\Models\Venue;

final class EventsContentService
{
    public function speaker(array $attributes, ?int $teamId = null): Speaker
    {
        if (trim((string) ($attributes['name'] ?? '')) === '') {
            throw ValidationException::withMessages(['name' => 'Speaker name is required.']);
        }

        return Speaker::query()->create(['name' => trim($attributes['name']), 'bio' => $attributes['bio'] ?? null, 'email' => $attributes['email'] ?? null, 'metadata' => $attributes['metadata'] ?? [], 'team_id' => $teamId]);
    }

    public function venue(array $attributes, ?int $teamId = null): Venue
    {
        if (trim((string) ($attributes['name'] ?? '')) === '') {
            throw ValidationException::withMessages(['name' => 'Venue name is required.']);
        }

        return Venue::query()->create(['name' => trim($attributes['name']), 'address' => $attributes['address'] ?? null, 'timezone' => $attributes['timezone'] ?? 'UTC', 'latitude' => $attributes['latitude'] ?? null, 'longitude' => $attributes['longitude'] ?? null, 'team_id' => $teamId]);
    }

    public function event(array $attributes, ?int $teamId = null): Event
    {
        $start = $this->date($attributes['starts_at'] ?? null, 'starts_at');
        $end = $this->date($attributes['ends_at'] ?? null, 'ends_at');
        if (trim((string) ($attributes['key'] ?? '')) === '' || trim((string) ($attributes['title'] ?? '')) === '') {
            throw ValidationException::withMessages(['event' => 'Event key and title are required.']);
        }
        if ($end->lessThanOrEqualTo($start)) {
            throw ValidationException::withMessages(['ends_at' => 'Event end must be after its start.']);
        }

        return Event::query()->updateOrCreate(['key' => $attributes['key'], 'team_id' => $teamId], ['title' => $attributes['title'], 'description' => $attributes['description'] ?? null, 'status' => $attributes['status'] ?? 'draft', 'timezone' => $attributes['timezone'] ?? 'UTC', 'starts_at' => $start, 'ends_at' => $end, 'venue_id' => $attributes['venue_id'] ?? null, 'team_id' => $teamId]);
    }

    public function session(Event $event, array $attributes): Session
    {
        $start = $this->date($attributes['starts_at'] ?? null, 'starts_at');
        $end = $this->date($attributes['ends_at'] ?? null, 'ends_at');
        if (trim((string) ($attributes['key'] ?? '')) === '' || trim((string) ($attributes['title'] ?? '')) === '') {
            throw ValidationException::withMessages(['session' => 'Session key and title are required.']);
        }
        if ($end->lessThanOrEqualTo($start) || $start->lessThan($event->starts_at) || $end->greaterThan($event->ends_at)) {
            throw ValidationException::withMessages(['session' => 'Session must fit within the event window.']);
        }
        $session = $event->sessions()->updateOrCreate(['key' => $attributes['key']], ['title' => $attributes['title'], 'description' => $attributes['description'] ?? null, 'starts_at' => $start, 'ends_at' => $end, 'room' => $attributes['room'] ?? null]);
        if (isset($attributes['speaker_ids'])) {
            $session->speakers()->sync(array_map('intval', $attributes['speaker_ids']));
        }

        return $session->load('speakers');
    }

    public function registration(Event $event, array $attributes): RegistrationReference
    {
        if (trim((string) ($attributes['provider'] ?? '')) === '' || trim((string) ($attributes['external_key'] ?? '')) === '') {
            throw ValidationException::withMessages(['registration' => 'Provider and external registration key are required.']);
        }
        if (isset($attributes['url']) && filter_var($attributes['url'], FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages(['url' => 'Registration URL must be valid.']);
        }

        return $event->registrations()->updateOrCreate(['provider' => $attributes['provider']], ['external_key' => $attributes['external_key'], 'url' => $attributes['url'] ?? null, 'status' => $attributes['status'] ?? 'active', 'metadata' => $attributes['metadata'] ?? []]);
    }

    public function publish(Event $event): Event
    {
        if (! $event->sessions()->exists()) {
            throw ValidationException::withMessages(['event' => 'An event needs at least one session before publication.']);
        }
        $event->update(['status' => 'published', 'structured_data' => $this->structuredData($event)]);

        return $event->refresh();
    }

    public function archive(Event $event): Event
    {
        if ($event->status !== 'published') {
            throw ValidationException::withMessages(['event' => 'Only published events can be archived.']);
        }
        $event->update(['status' => 'archived', 'archived_at' => now()]);

        return $event->refresh();
    }

    /** @return array<string, mixed> */
    public function structuredData(Event $event): array
    {
        $event->loadMissing('venue');

        return ['@context' => 'https://schema.org', '@type' => 'Event', 'name' => $event->title, 'description' => $event->description, 'startDate' => $event->starts_at?->toIso8601String(), 'endDate' => $event->ends_at?->toIso8601String(), 'eventStatus' => $event->status, 'location' => $event->venue ? ['@type' => 'Place', 'name' => $event->venue->name, 'address' => $event->venue->address] : null];
    }

    private function date(mixed $value, string $field): Carbon
    {
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            throw ValidationException::withMessages([$field => 'A valid date and time is required.']);
        }
    }
}
