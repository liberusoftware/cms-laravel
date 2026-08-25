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
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences', AudienceController::class, 'index', 'cms.personalization.audiences.index'));
            $registry->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences', AudienceController::class, 'create', 'cms.personalization.audiences.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences/{key}', AudienceController::class, 'show', 'cms.personalization.audiences.show'));
            $registry->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences/{key}', AudienceController::class, 'update', 'cms.personalization.audiences.update', 'PATCH', ['abilities:content:write']));
            $registry->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences/{key}', AudienceController::class, 'destroy', 'cms.personalization.audiences.destroy', 'DELETE', ['abilities:content:write']));
            $registry->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences/{key}/variants', AudienceController::class, 'createVariant', 'cms.personalization.variants.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('personalization-api', new ApiEndpoint('cms/personalization/audiences/{key}/decision', AudienceController::class, 'decide', 'cms.personalization.audiences.decide', 'POST', ['abilities:content:read']));
        }
    }
}
