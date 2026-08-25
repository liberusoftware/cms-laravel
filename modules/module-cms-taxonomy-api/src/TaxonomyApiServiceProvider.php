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
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomies', TaxonomyController::class, 'index', 'cms.taxonomy.index'));
            $r->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomies', TaxonomyController::class, 'create', 'cms.taxonomy.create', 'POST', ['abilities:taxonomy:create']));
            $r->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomies/{taxonomy}', TaxonomyController::class, 'show', 'cms.taxonomy.show'));
            $r->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomies/{taxonomy}', TaxonomyController::class, 'update', 'cms.taxonomy.update', 'PATCH', ['abilities:taxonomy:update']));
            $r->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomies/{taxonomy}', TaxonomyController::class, 'delete', 'cms.taxonomy.delete', 'DELETE', ['abilities:taxonomy:delete']));
            $r->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomies/{taxonomy}/terms', TaxonomyController::class, 'terms', 'cms.taxonomy.terms'));
            $r->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomies/{taxonomy}/terms', TaxonomyController::class, 'addTerm', 'cms.taxonomy.terms.create', 'POST', ['abilities:taxonomy:create']));
            $r->registerEndpoint('taxonomy-api', new ApiEndpoint('cms/taxonomy-terms/{term}/move', TaxonomyController::class, 'moveTerm', 'cms.taxonomy.terms.move', 'POST', ['abilities:taxonomy:update']));
        }
    }
}
