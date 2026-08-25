<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentCalendar\Services\ContentCalendarService;

uses(RefreshDatabase::class);

it('schedules campaigns and filters calendar items by channel and site', function (): void {
    $service = app(ContentCalendarService::class);
    $campaign = $service->campaign(['name' => 'Launch'], 3);
    $service->schedule(['title' => 'Announcement', 'campaign_id' => $campaign->id, 'channel' => 'web', 'site' => 'main', 'starts_at' => '2026-09-01 10:00'], 3);
    $service->schedule(['title' => 'Email', 'channel' => 'email', 'site' => 'main', 'starts_at' => '2026-09-02 10:00'], 3);

    expect($service->items(3, 'web', 'main')->total())->toBe(1);
});

it('rejects overlapping schedules and supports drag-drop rescheduling', function (): void {
    $service = app(ContentCalendarService::class);
    $item = $service->schedule(['title' => 'First', 'channel' => 'web', 'site' => 'main', 'starts_at' => '2026-09-01 10:00', 'deadline_at' => '2026-09-01 12:00'], 3);

    expect(fn () => $service->schedule(['title' => 'Conflict', 'channel' => 'web', 'site' => 'main', 'starts_at' => '2026-09-01 11:00'], 3))
        ->toThrow(ValidationException::class);

    expect($service->reschedule($item, '2026-09-02 10:00')->starts_at->format('Y-m-d H:i'))->toBe('2026-09-02 10:00');
});
