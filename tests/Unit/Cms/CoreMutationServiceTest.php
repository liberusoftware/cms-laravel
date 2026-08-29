<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\Core\Models\Site;

uses(RefreshDatabase::class);

it('centralizes core creation defaults and alias normalization', function (): void {
    $service = app(CoreMutationService::class);
    $site = $service->createSite(['key' => 'docs', 'name' => 'Docs']);
    $channel = $service->createChannel($site, ['key' => 'web', 'name' => 'Web']);
    $alias = $service->createAlias($site, [
        'channel_id' => $channel->id,
        'alias' => 'guides/start',
        'target_type' => 'page',
        'target_id' => '42',
    ]);

    expect($site->default_locale)->toBe('en')
        ->and($channel->type)->toBe('web')
        ->and($alias->alias)->toBe('/guides/start');
});

it('rejects invalid core mutations before persistence', function (): void {
    expect(fn () => app(CoreMutationService::class)->createSite(['key' => '', 'name' => 'Docs']))
        ->toThrow(ValidationException::class);

    expect(Site::query()->count())->toBe(0);
});

it('upserts settings within a site and environment boundary', function (): void {
    $service = app(CoreMutationService::class);
    $site = $service->createSite(['key' => 'docs', 'name' => 'Docs']);

    $first = $service->putSetting($site, 'footer', ['value' => 'One']);
    $second = $service->putSetting($site, 'footer', ['value' => 'Two']);

    expect($second->id)->toBe($first->id)
        ->and($second->fresh()->value)->toBe(['value' => 'Two']);
});

it('rejects invalid redirect statuses and blank setting environments', function (): void {
    $service = app(CoreMutationService::class);
    $site = $service->createSite(['key' => 'docs', 'name' => 'Docs']);

    expect(fn () => $service->createAlias($site, [
        'alias' => 'guides/start',
        'target_type' => 'page',
        'target_id' => '42',
        'redirect_status' => 200,
    ]))->toThrow(ValidationException::class);

    expect(fn () => $service->putSetting($site, 'footer', [], ' '))
        ->toThrow(ValidationException::class);
});

it('updates and deletes sites and channels through the mutation boundary', function (): void {
    $service = app(CoreMutationService::class);
    $site = $service->createSite(['key' => 'docs', 'name' => 'Docs']);
    $channel = $service->createChannel($site, ['key' => 'web', 'name' => 'Web']);

    $service->updateSite($site, ['name' => 'Documentation', 'team_id' => 999]);
    $service->updateChannel($channel, ['name' => 'Website']);

    expect($site->fresh()->name)->toBe('Documentation')
        ->and($site->fresh()->team_id)->toBeNull()
        ->and($channel->fresh()->name)->toBe('Website');

    $service->deleteChannel($channel);
    $service->deleteSite($site);

    expect($site->fresh())->toBeNull()
        ->and($channel->fresh())->toBeNull();
});
