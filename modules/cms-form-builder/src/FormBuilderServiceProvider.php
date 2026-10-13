<?php

declare(strict_types=1);

namespace Liberu\Cms\FormBuilder;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\FormBuilder\Services\FormBuilderService;

final class FormBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FormBuilderService::class);
    }
}
