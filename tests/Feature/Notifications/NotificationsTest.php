<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Liberu\Cms\Forms\Models\Form;
use Liberu\Cms\Notifications\Jobs\SendNotification;
use Liberu\Cms\Notifications\Models\NotificationLog;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

it('logs and queues a notification when a form is submitted', function (): void {
    Queue::fake();

    $team = Team::factory()->create();
    Form::factory()->create(['slug' => 'contact', 'team_id' => $team->id]);

    $this->postJson('/forms/contact', ['email' => 'v@example.com', 'message' => 'hi'])
        ->assertCreated();

    $this->assertDatabaseHas('cms_notification_logs', [
        'event' => 'forms.submitted',
        'team_id' => $team->id,
    ]);

    Queue::assertPushed(SendNotification::class);
});

it('notifies when content is published', function (): void {
    Queue::fake();

    $page = Page::factory()->create(['slug' => 'about']);
    $page->publish();

    expect(NotificationLog::query()->where('event', 'content.published')->exists())->toBeTrue();

    Queue::assertPushed(SendNotification::class);
});

it('queues a mail-channel message with the configured recipient', function (): void {
    config()->set('cms-notifications.subscriptions', [
        'forms.submitted' => [
            ['channel' => 'mail', 'to' => 'ops@example.com', 'subject' => 'New submission'],
        ],
    ]);

    Queue::fake();

    Form::factory()->create(['slug' => 'contact']);

    $this->postJson('/forms/contact', ['email' => 'v@example.com', 'message' => 'hi'])
        ->assertCreated();

    Queue::assertPushed(SendNotification::class, function (SendNotification $job): bool {
        return $job->message->channel === 'mail'
            && in_array('ops@example.com', $job->message->to, true)
            && $job->message->subject === 'New submission';
    });
});

it('does nothing for an event with no subscriptions', function (): void {
    config()->set('cms-notifications.subscriptions', []);

    Queue::fake();

    Form::factory()->create(['slug' => 'contact']);

    $this->postJson('/forms/contact', ['email' => 'v@example.com', 'message' => 'hi'])->assertCreated();

    Queue::assertNothingPushed();
    expect(NotificationLog::query()->count())->toBe(0);
});
