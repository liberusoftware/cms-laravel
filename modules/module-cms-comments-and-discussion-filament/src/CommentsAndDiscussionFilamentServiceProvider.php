<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussionFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CommentsAndDiscussionFilament\Resources\CommentResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class CommentsAndDiscussionFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('comments-and-discussion', CommentResource::class);
        }
    }
}
