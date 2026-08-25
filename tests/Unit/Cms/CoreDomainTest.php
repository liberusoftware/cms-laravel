<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Contracts\Events\Core\ChannelCreated;
use Liberu\Cms\Contracts\Events\Core\ContentIdentityCreated;
use Liberu\Cms\Contracts\Events\Core\SettingChanged;
use Liberu\Cms\Contracts\Events\Core\SiteCreated;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Core\Queries\CoreQueryService;

uses(RefreshDatabase::class);

it('owns sites and channels and emits creation events', function (): void {
    $created = [];
    app(EventBusInterface::class)->listen(SiteCreated::class, function (SiteCreated $event) use (&$created): void {
        $created[] = $event;
    });
    app(EventBusInterface::class)->listen(ChannelCreated::class, function (ChannelCreated $event) use (&$created): void {
        $created[] = $event;
    });

    $site = Site::create([
        'key' => 'marketing',
        'name' => 'Marketing',
        'domain' => 'marketing.test',
    ]);
    $channel = $site->channels()->create([
        'key' => 'web',
        'name' => 'Website',
    ]);

    expect($site->fresh()->channels)->toHaveCount(1)
        ->and($channel->site->is($site))->toBeTrue();

    expect($created)->toHaveCount(2)
        ->and($created[0])->toBeInstanceOf(SiteCreated::class)
        ->and($created[1])->toBeInstanceOf(ChannelCreated::class);
});

it('stores content identity, ownership, aliases, and settings at the site boundary', function (): void {
    $created = [];
    app(EventBusInterface::class)->listen(ContentIdentityCreated::class, function (ContentIdentityCreated $event) use (&$created): void {
        $created[] = $event;
    });
    app(EventBusInterface::class)->listen(SettingChanged::class, function (SettingChanged $event) use (&$created): void {
        $created[] = $event;
    });

    $site = Site::create(['key' => 'docs', 'name' => 'Docs']);
    $channel = $site->channels()->create(['key' => 'api', 'name' => 'API', 'type' => 'headless']);
    $identity = $site->identities()->create([
        'channel_id' => $channel->id,
        'content_type' => 'page',
        'content_id' => '42',
        'canonical_path' => '/guides/start',
        'metadata' => ['locale' => 'en'],
    ]);
    $alias = $site->aliases()->create([
        'channel_id' => $channel->id,
        'alias' => '/start',
        'target_type' => 'page',
        'target_id' => '42',
    ]);
    $setting = $site->cmsSettings()->create([
        'key' => 'footer_text',
        'value' => ['value' => 'Docs'],
    ]);

    expect($identity->channel->is($channel))->toBeTrue()
        ->and($alias->site->is($site))->toBeTrue()
        ->and($setting->fresh()->value)->toBe(['value' => 'Docs']);

    expect($created)->toHaveCount(2)
        ->and($created[0])->toBeInstanceOf(ContentIdentityCreated::class)
        ->and($created[1])->toBeInstanceOf(SettingChanged::class);
});

it('enforces the site and channel identity constraints', function (): void {
    $site = Site::create(['key' => 'one', 'name' => 'One']);
    $site->channels()->create(['key' => 'web', 'name' => 'Web']);

    expect(fn () => Site::create(['key' => 'one', 'name' => 'Duplicate']))
        ->toThrow(QueryException::class);

    expect(fn () => $site->channels()->create(['key' => 'web', 'name' => 'Duplicate']))
        ->toThrow(QueryException::class);
});

it('paginates aliases and identities through the core query boundary', function (): void {
    $site = Site::create(['key' => 'public', 'name' => 'Public']);
    $channel = $site->channels()->create(['key' => 'web', 'name' => 'Web']);
    $site->aliases()->create([
        'channel_id' => $channel->id,
        'alias' => '/old-home',
        'target_type' => 'page',
        'target_id' => '1',
    ]);
    $site->identities()->create([
        'channel_id' => $channel->id,
        'content_type' => 'page',
        'content_id' => '1',
        'canonical_path' => '/home',
    ]);

    $otherSite = Site::create(['key' => 'private', 'name' => 'Private']);
    $otherChannel = $otherSite->channels()->create(['key' => 'web', 'name' => 'Web']);
    $otherSite->aliases()->create([
        'channel_id' => $otherChannel->id,
        'alias' => '/private',
        'target_type' => 'page',
        'target_id' => '2',
    ]);

    $queries = app(CoreQueryService::class);

    expect($queries->aliases('public', 1)->total())->toBe(1)
        ->and($queries->aliases('public', 1)->items()[0]->alias)->toBe('/old-home')
        ->and($queries->identities('public', 1)->total())->toBe(1)
        ->and($queries->identities('public', 1)->items()[0]->canonical_path)->toBe('/home');
});
