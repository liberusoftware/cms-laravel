<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectoryApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContactDirectoryApi\Http\ContactDirectoryController;

final class ContactDirectoryApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/contact-directory')->middleware('api')->group(function (): void {
            Route::get('contacts', [ContactDirectoryController::class, 'index'])->name('cms.contact-directory.contacts.index');
            Route::post('contacts', [ContactDirectoryController::class, 'store'])->name('cms.contact-directory.contacts.store');
            Route::post('categories', [ContactDirectoryController::class, 'category'])->name('cms.contact-directory.categories.store');
            Route::post('locations', [ContactDirectoryController::class, 'location'])->name('cms.contact-directory.locations.store');
            Route::post('forms', [ContactDirectoryController::class, 'form'])->name('cms.contact-directory.forms.store');
        });
    }
}
