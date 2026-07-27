<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Forms\Filament\FormResource;
use Liberu\Cms\Forms\Filament\FormSubmissionResource;
use Liberu\Cms\Forms\Support\SubmissionValidator;

/**
 * Wires the Forms module: the submission validator, the per-IP submit throttle,
 * the public submit route, and the migrations.
 */
final class CmsFormsServiceProvider extends ModuleServiceProvider
{
    private const string RATE_LIMITER = 'cms-forms';

    public function module(): ModuleInterface
    {
        return new CmsFormsModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/forms.php', 'cms-forms');

        $this->app->singleton(SubmissionValidator::class);

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $registry = $this->app->make(AdminResourceRegistryInterface::class);
            $registry->registerResource('forms', FormResource::class);
            $registry->registerResource('forms', FormSubmissionResource::class);
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        $this->configureRateLimiting();
        $this->loadModuleRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $registrar = $this->app->make(PermissionRegistrarInterface::class);
            $registrar->register(new PermissionGroup('forms', 'Forms', AccessScope::Content, ['view', 'create', 'update', 'delete']));
            $registrar->register(new PermissionGroup('form-submissions', 'Form submissions', AccessScope::Content, ['view', 'delete']));
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/forms.php' => $this->app->configPath('cms-forms.php'),
            ], 'cms-forms-config');
        }
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for(self::RATE_LIMITER, function (Request $request): Limit {
            $configured = config('cms-forms.rate_limit', 10);
            $perMinute = is_numeric($configured) ? (int) $configured : 10;

            return Limit::perMinute($perMinute)->by(self::RATE_LIMITER.':'.($request->ip() ?? 'unknown'));
        });
    }
}
