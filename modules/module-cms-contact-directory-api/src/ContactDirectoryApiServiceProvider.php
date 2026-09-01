<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectoryApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContactDirectoryApi\Http\ContactDirectoryController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContactDirectoryApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('contact-directory-api', new ApiEndpoint('cms/contact-directory/contacts', ContactDirectoryController::class, 'index', 'cms.contact-directory.contacts.index'));
        $registry->registerEndpoint('contact-directory-api', new ApiEndpoint('cms/contact-directory/contacts', ContactDirectoryController::class, 'store', 'cms.contact-directory.contacts.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('contact-directory-api', new ApiEndpoint('cms/contact-directory/categories', ContactDirectoryController::class, 'category', 'cms.contact-directory.categories.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('contact-directory-api', new ApiEndpoint('cms/contact-directory/locations', ContactDirectoryController::class, 'location', 'cms.contact-directory.locations.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('contact-directory-api', new ApiEndpoint('cms/contact-directory/forms', ContactDirectoryController::class, 'form', 'cms.contact-directory.forms.store', 'POST', ['abilities:content:write']));
    }
}
