<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\EventsContent\Services\EventsContentService;

uses(RefreshDatabase::class);

it('manages an event with sessions, speakers, registration, structured data, and archive state', function (): void {
    $service = app(EventsContentService::class);
    $speaker = $service->speaker(['name' => 'Ada Lovelace']);
    $venue = $service->venue(['name' => 'Main Hall', 'address' => '1 Example Street']);
    $event = $service->event(['key' => 'summit', 'title' => 'CMS Summit', 'starts_at' => '2026-09-01 10:00:00', 'ends_at' => '2026-09-01 16:00:00', 'venue_id' => $venue->id]);
    $service->session($event, ['key' => 'opening', 'title' => 'Opening', 'starts_at' => '2026-09-01 10:00:00', 'ends_at' => '2026-09-01 11:00:00', 'speaker_ids' => [$speaker->id]]);
    $service->registration($event, ['provider' => 'tickets', 'external_key' => 'evt-1', 'url' => 'https://tickets.example.test/events/1']);
    $published = $service->publish($event);
    $archived = $service->archive($published);

    expect($published->structured_data['@type'])->toBe('Event')
        ->and($archived->status)->toBe('archived')
        ->and($archived->archived_at)->not->toBeNull();
});

it('rejects invalid windows, sessions outside the event, and publication without sessions', function (): void {
    $service = app(EventsContentService::class);
    expect(fn () => $service->event(['key' => 'bad', 'title' => 'Bad', 'starts_at' => '2026-09-01 12:00:00', 'ends_at' => '2026-09-01 11:00:00']))->toThrow(ValidationException::class);
    $event = $service->event(['key' => 'empty', 'title' => 'Empty', 'starts_at' => '2026-09-01 10:00:00', 'ends_at' => '2026-09-01 11:00:00']);
    expect(fn () => $service->publish($event))->toThrow(ValidationException::class);
});
