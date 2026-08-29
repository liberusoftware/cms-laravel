<?php

namespace Liberu\Cms\EmbedsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\EmbedsApi\Http\EmbedsController;

class EmbedsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        } $r = $this->app->make(ApiResourceRegistryInterface::class);
        $r->registerEndpoint('embeds-api', new ApiEndpoint('cms/embeds', EmbedsController::class, 'index', 'cms.embeds.index'));
        $r->registerEndpoint('embeds-api', new ApiEndpoint('cms/embeds/{id}', EmbedsController::class, 'show', 'cms.embeds.show'));
        $r->registerEndpoint('embeds-api', new ApiEndpoint('cms/embeds', EmbedsController::class, 'store', 'cms.embeds.store', 'POST', ['abilities:content:write']));
        $r->registerEndpoint('embeds-api', new ApiEndpoint('cms/embeds/{id}/render', EmbedsController::class, 'render', 'cms.embeds.render', 'POST'));
    }
}
