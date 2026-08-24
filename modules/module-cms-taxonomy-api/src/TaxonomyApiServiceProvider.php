<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\TaxonomyApi\Http\TaxonomyController;

final class TaxonomyApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomies', TaxonomyController::class, 'index', 'cms.taxonomy.index'));
        }
    }
}
