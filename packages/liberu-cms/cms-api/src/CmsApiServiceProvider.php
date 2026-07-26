<?php

declare(strict_types=1);

namespace Liberu\Cms\Api;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Laravel\Sanctum\PersonalAccessToken;
use Liberu\Cms\Api\Console\IssueTokenCommand;
use Liberu\Cms\Api\Console\PreviewLinkCommand;
use Liberu\Cms\Api\Http\Controllers\PreviewController;
use Liberu\Cms\Api\Http\Middleware\ForceJsonResponse;
use Liberu\Cms\Api\Http\Middleware\SetApiTenant;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Preview\PreviewRegistryInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

/**
 * Owns the Delivery API infrastructure: it binds the endpoint registry before
 * the content modules register (so their `app()->bound(...)` guard passes), then
 * in boot() — after every provider has registered — reads the populated registry
 * and defines the versioned, authenticated, rate-limited route group. This is
 * the Phase 4 registry + provider-timing pattern applied to the API.
 */
final class CmsApiServiceProvider extends ModuleServiceProvider
{
    private const string VERSION = 'v1';

    private const string RATE_LIMITER = 'cms-api';

    public function module(): ModuleInterface
    {
        return new CmsApiModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/cms-api.php', 'cms-api');
        $this->mergeModuleConfig(__DIR__.'/../config/cors.php', 'cors');

        $this->app->singleton(ApiResourceRegistryInterface::class, ApiResourceRegistry::class);
        $this->app->singleton(PreviewRegistryInterface::class, PreviewRegistry::class);
    }

    protected function bootModule(): void
    {
        $router = $this->app->make('router');
        $router->aliasMiddleware('abilities', CheckAbilities::class);
        $router->aliasMiddleware('ability', CheckForAnyAbility::class);

        $this->configureRateLimiting();
        $this->registerRoutes();
        $this->registerPreviewRoute();

        if ($this->app->runningInConsole()) {
            $this->commands([IssueTokenCommand::class, PreviewLinkCommand::class]);

            $this->publishes([
                __DIR__.'/../config/cms-api.php' => $this->app->configPath('cms-api.php'),
            ], 'cms-api-config');
        }
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for(self::RATE_LIMITER, function (Request $request): Limit {
            $configured = config('cms-api.rate_limit', 60);
            $perMinute = is_numeric($configured) ? (int) $configured : 60;

            return Limit::perMinute($perMinute)->by(self::RATE_LIMITER.':'.$this->throttleKey($request));
        });
    }

    /**
     * Throttle per Delivery token when authenticated, else per client IP.
     */
    private function throttleKey(Request $request): string
    {
        $principal = $request->user();

        if ($principal instanceof HasApiTokens) {
            $token = $principal->currentAccessToken();

            if ($token instanceof PersonalAccessToken) {
                $id = $token->getKey();

                return 'token:'.(is_int($id) || is_string($id) ? (string) $id : '');
            }
        }

        return 'ip:'.($request->ip() ?? 'unknown');
    }

    private function registerRoutes(): void
    {
        $endpoints = $this->app->make(ApiResourceRegistryInterface::class)->endpoints();

        if ($endpoints === []) {
            return;
        }

        Route::prefix('api/'.self::VERSION)
            ->middleware([
                ForceJsonResponse::class,
                'auth:sanctum',
                SetApiTenant::class,
                'throttle:'.self::RATE_LIMITER,
                SubstituteBindings::class,
            ])
            ->group(function () use ($endpoints): void {
                foreach ($endpoints as $group) {
                    foreach ($group as $endpoint) {
                        Route::match([$endpoint->method], $endpoint->uri, [$endpoint->controller, $endpoint->action])
                            ->middleware($endpoint->middleware)
                            ->name('cms-api.'.$endpoint->name);
                    }
                }
            });
    }

    /**
     * Registers the unauthenticated preview route. It sits outside the token
     * group: access is granted by a valid, unexpired signature (checked by
     * ValidateSignature) rather than a Delivery token, so a shared link works
     * without a login and stops working when it expires.
     */
    private function registerPreviewRoute(): void
    {
        Route::prefix('api/'.self::VERSION)
            ->middleware([ValidateSignature::class, SubstituteBindings::class])
            ->get('preview/{type}/{id}', PreviewController::class)
            ->whereNumber('id')
            ->name('cms-api.preview');
    }
}
