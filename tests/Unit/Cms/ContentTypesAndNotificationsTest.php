<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Fields\FieldDefinition;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\ContentTypes\Search\ContentEntrySearchSource;
use Liberu\Cms\Notifications\Jobs\SendNotification;
use Liberu\Cms\Notifications\Models\NotificationLog;
use Liberu\Cms\Notifications\NotificationDispatcher;

uses(RefreshDatabase::class);

it('normalizes field definitions from untrusted schema arrays', function (): void {
    $definition = FieldDefinition::fromArray([
        'name' => 123,
        'label' => null,
        'type' => null,
        'required' => 1,
        'options' => ['one', 2, 'two'],
    ]);

    expect($definition->toArray())->toBe([
        'name' => '',
        'label' => '',
        'type' => 'text',
        'required' => true,
        'options' => ['one', 'two'],
    ]);
});

it('maps content entry search hits with and without a loaded content type', function (): void {
    $withType = (new ContentEntry)->forceFill([
        'id' => 11,
        'title' => 'A searchable title',
        'slug' => 'searchable-title',
    ])->setRelation('type', (new ContentType)->forceFill(['key' => 'portfolio']));
    $withoutType = (new ContentEntry)->forceFill([
        'id' => 12,
        'title' => 'Body match',
        'slug' => 'body-match',
    ])->setRelation('type', null);

    $repository = Mockery::mock(ContentEntryRepositoryInterface::class);
    $repository->expects('search')->once()->andReturn([$withType, $withoutType]);

    $results = iterator_to_array((new ContentEntrySearchSource($repository))->search('search'));

    expect($results[0]->type)->toBe('portfolio')
        ->and($results[0]->score)->toBe(2.0)
        ->and($results[1]->type)->toBe('content-entry')
        ->and($results[1]->score)->toBe(1.0);
});

it('normalizes notification subscriptions and queues their delivery', function (): void {
    Queue::fake();
    config()->set('cms-notifications.subscriptions', [
        'custom.event' => [
            ['channel' => '', 'subject' => null, 'to' => ['first@example.com', '', 42]],
            'ignored',
        ],
    ]);

    app(NotificationDispatcher::class)->dispatch('custom.event', 7, ['url' => 'https://example.test']);

    expect(NotificationLog::query()->firstOrFail()->only(['channel', 'recipient', 'team_id']))
        ->toBe(['channel' => 'log', 'recipient' => 'first@example.com', 'team_id' => 7]);
    Queue::assertPushed(SendNotification::class);
});

it('ignores malformed notification subscription configuration', function (): void {
    Queue::fake();
    config()->set('cms-notifications.subscriptions', 'invalid');

    app(NotificationDispatcher::class)->dispatch('custom.event', 'tenant', []);

    Queue::assertNothingPushed();
    expect(NotificationLog::query()->count())->toBe(0);
});
