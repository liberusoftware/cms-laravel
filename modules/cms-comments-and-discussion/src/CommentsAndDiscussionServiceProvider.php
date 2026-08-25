<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussion;

use Liberu\Cms\CommentsAndDiscussion\Services\CommentService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class CommentsAndDiscussionServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new CommentsAndDiscussionModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/comments-and-discussion.php', 'comments-and-discussion');
        $this->app->singleton(CommentService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup(
                'comments-and-discussion', 'Comments and Discussion', AccessScope::Content,
                ['view', 'create', 'update', 'moderate', 'report', 'subscribe', 'delete'],
            ));
        }
    }
}
