<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeedsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\SyndicationAndFeedsApi\Http\FeedController;

final class SyndicationAndFeedsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('syndication-and-feeds-api', new ApiEndpoint('cms/syndication-and-feeds/{feed}', FeedController::class, 'show', 'cms.syndication-and-feeds.show'));
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('syndication-and-feeds-api', new ApiEndpoint('cms/syndication-and-feeds', FeedController::class, 'index', 'cms.syndication-and-feeds.index'));
            $registry->registerEndpoint('syndication-and-feeds-api', new ApiEndpoint('cms/syndication-and-feeds', FeedController::class, 'create', 'cms.syndication-and-feeds.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('syndication-and-feeds-api', new ApiEndpoint('cms/syndication-and-feeds/{feed}/items', FeedController::class, 'item', 'cms.syndication-and-feeds.item', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('syndication-and-feeds-api', new ApiEndpoint('cms/syndication-and-feeds/{feed}/import', FeedController::class, 'import', 'cms.syndication-and-feeds.import', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('syndication-and-feeds-api', new ApiEndpoint('cms/syndication-and-feeds/{feed}/syndicate', FeedController::class, 'syndicate', 'cms.syndication-and-feeds.syndicate', 'POST', ['abilities:content:write']));
        }
    }
}
