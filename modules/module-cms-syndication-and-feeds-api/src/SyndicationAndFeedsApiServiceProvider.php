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
        }
    }
}
