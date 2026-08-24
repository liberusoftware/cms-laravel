<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeedsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\SyndicationAndFeedsFilament\Resources\FeedResource;

final class SyndicationAndFeedsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('syndication-and-feeds', FeedResource::class);
        }
    }
}
