<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Liberu\Cms\Core\Models\Channel;
use Liberu\Cms\Core\Models\ContentAlias;
use Liberu\Cms\Core\Models\ContentIdentity;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\CoreApi\Http\Resources\CoreAliasResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreChannelResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreIdentityResource;
use Liberu\Cms\CoreApi\Http\Resources\CoreSiteResource;

it('serializes core sites through an explicit API resource contract', function (): void {
    $site = new Site([
        'id' => 7,
        'key' => 'docs',
        'name' => 'Documentation',
        'domain' => 'docs.example.test',
        'default_locale' => 'en',
        'timezone' => 'UTC',
        'status' => 'active',
        'settings' => ['theme' => 'docs'],
    ]);
    $site->setRawAttributes([...$site->getAttributes(), 'id' => 7]);

    $payload = (new CoreSiteResource($site))->toArray(Request::create('/'));

    expect($payload)->toMatchArray([
        'id' => '7',
        'type' => 'cms-site',
        'key' => 'docs',
        'name' => 'Documentation',
        'settings' => ['theme' => 'docs'],
    ])->not->toHaveKey('team_id');
});

it('serializes channel type without conflating it with the resource type', function (): void {
    $channel = new Channel(['id' => 3, 'site_id' => 7, 'key' => 'web', 'name' => 'Web', 'type' => 'web']);
    $channel->setRawAttributes([...$channel->getAttributes(), 'id' => 3]);

    expect((new CoreChannelResource($channel))->toArray(Request::create('/')))->toMatchArray([
        'id' => '3',
        'type' => 'cms-channel',
        'site_id' => '7',
        'channel_type' => 'web',
    ]);
});

it('serializes core aliases and identities with explicit resource types', function (): void {
    $alias = new ContentAlias(['id' => 4, 'site_id' => 7, 'alias' => '/old', 'target_type' => 'page', 'target_id' => '9', 'redirect_status' => 301]);
    $identity = new ContentIdentity(['id' => 5, 'site_id' => 7, 'content_type' => 'page', 'content_id' => '9', 'canonical_path' => '/new', 'status' => 'active', 'metadata' => ['locale' => 'en']]);
    $alias->setRawAttributes([...$alias->getAttributes(), 'id' => 4]);
    $identity->setRawAttributes([...$identity->getAttributes(), 'id' => 5]);

    expect((new CoreAliasResource($alias))->toArray(Request::create('/')))->toMatchArray(['id' => '4', 'type' => 'cms-content-alias', 'alias' => '/old'])
        ->and((new CoreIdentityResource($identity))->toArray(Request::create('/')))->toMatchArray(['id' => '5', 'type' => 'cms-content-identity', 'canonical_path' => '/new']);
});
