<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Notifications\Filament\NotificationLogResource;
use Liberu\Cms\Notifications\Filament\Pages\ListNotificationLogs;
use Liberu\Cms\Notifications\Models\NotificationLog;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);

    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);
    Filament::setTenant($this->team);
});

it('renders the notifications log list', function (): void {
    Livewire::test(ListNotificationLogs::class)->assertSuccessful();
});

it('lists notification log records', function (): void {
    $log = NotificationLog::create([
        'event' => 'forms.submitted',
        'channel' => 'log',
        'recipient' => 'ops@example.com',
        'context' => ['formSlug' => 'contact'],
    ]);

    Livewire::test(ListNotificationLogs::class)->assertCanSeeTableRecords([$log]);
});

it('is read-only', function (): void {
    expect(NotificationLogResource::canCreate())->toBeFalse();
});
