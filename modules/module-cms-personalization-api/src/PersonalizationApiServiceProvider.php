<?php

declare(strict_types=1);

namespace Liberu\Cms\PersonalizationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\PersonalizationApi\Http\AudienceController;

final class PersonalizationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences/{key}', AudienceController::class, 'show', 'cms.personalization.audiences.show'));
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences/{key}/decision', AudienceController::class, 'decide', 'cms.personalization.audiences.decide', 'POST', ['abilities:content:read']));
        }
    }
}
