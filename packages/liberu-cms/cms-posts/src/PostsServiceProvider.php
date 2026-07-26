<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts;

use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Admin\DashboardStat;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Preview\PreviewRegistryInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Posts\Contracts\PostRepositoryInterface;
use Liberu\Cms\Posts\Filament\PostResource;
use Liberu\Cms\Posts\Http\Controllers\PostApiController;
use Liberu\Cms\Posts\Http\Controllers\PostWriteController;
use Liberu\Cms\Posts\Models\Post;
use Liberu\Cms\Posts\Preview\PostPreviewSource;
use Liberu\Cms\Posts\Repositories\PostRepository;
use Liberu\Cms\Posts\Search\PostSearchSource;

final class PostsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new PostsModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/posts.php', 'cms-posts');

        $this->app->singleton(PostRepositoryInterface::class, PostRepository::class);

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('posts', PostResource::class);
        }

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('posts', new ApiEndpoint('posts', PostApiController::class, 'index', 'posts.index'));
            $registry->registerEndpoint('posts', new ApiEndpoint('posts/{slug}', PostApiController::class, 'show', 'posts.show'));
            $registry->registerEndpoint('posts', new ApiEndpoint('posts', PostWriteController::class, 'store', 'posts.store', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('posts', new ApiEndpoint('posts/{id}', PostWriteController::class, 'update', 'posts.update', 'PUT', ['abilities:content:write']));
            $registry->registerEndpoint('posts', new ApiEndpoint('posts/{id}', PostWriteController::class, 'destroy', 'posts.destroy', 'DELETE', ['abilities:content:write']));
        }

        if ($this->app->bound(PreviewRegistryInterface::class)) {
            $this->app->make(PreviewRegistryInterface::class)
                ->registerSource($this->app->make(PostPreviewSource::class));
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        if ($this->app->bound(AdminDashboardRegistryInterface::class)) {
            $this->app->make(AdminDashboardRegistryInterface::class)->registerStat(
                new DashboardStat('Posts', fn (): int => Post::count(), 'heroicon-o-newspaper', 'primary'),
            );
        }

        if ($this->app->bound(SearchRegistryInterface::class)) {
            $this->app->make(SearchRegistryInterface::class)
                ->registerSource($this->app->make(PostSearchSource::class));
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/posts.php' => $this->app->configPath('cms-posts.php'),
            ], 'cms-posts-config');
        }
    }
}
