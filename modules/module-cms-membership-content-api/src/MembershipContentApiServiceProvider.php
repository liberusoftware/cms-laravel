<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContentApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\MembershipContentApi\Http\MembershipContentController;

final class MembershipContentApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('membership-content-api', new ApiEndpoint('cms/membership-content', MembershipContentController::class, 'index', 'cms.membership-content.index'));
        $registry->registerEndpoint('membership-content-api', new ApiEndpoint('cms/membership-content', MembershipContentController::class, 'store', 'cms.membership-content.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('membership-content-api', new ApiEndpoint('cms/membership-content/{content}', MembershipContentController::class, 'update', 'cms.membership-content.update', 'PATCH', ['abilities:content:write']));
        $registry->registerEndpoint('membership-content-api', new ApiEndpoint('cms/membership-content/{content}/rules', MembershipContentController::class, 'rule', 'cms.membership-content.rules.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('membership-content-api', new ApiEndpoint('cms/membership-content/{content}/downloads', MembershipContentController::class, 'download', 'cms.membership-content.downloads.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('membership-content-api', new ApiEndpoint('cms/membership-content/entitlements', MembershipContentController::class, 'grant', 'cms.membership-content.entitlements.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('membership-content-api', new ApiEndpoint('cms/membership-content/entitlements', MembershipContentController::class, 'revoke', 'cms.membership-content.entitlements.destroy', 'DELETE', ['abilities:content:write']));
    }
}
