<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussionApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CommentsAndDiscussionApi\Http\CommentController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class CommentsAndDiscussionApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $r = $this->app->make(ApiResourceRegistryInterface::class);
        $prefix = 'cms.comments-and-discussion.';
        $r->registerEndpoint('comments-and-discussion-api', new ApiEndpoint('cms/comments-and-discussion/{type}/{id}', CommentController::class, 'index', $prefix.'index'));
        $r->registerEndpoint('comments-and-discussion-api', new ApiEndpoint('cms/comments-and-discussion', CommentController::class, 'store', $prefix.'create', 'POST', ['abilities:comments-and-discussion:create']));
        $r->registerEndpoint('comments-and-discussion-api', new ApiEndpoint('cms/comments-and-discussion/{comment}', CommentController::class, 'show', $prefix.'show'));
        $r->registerEndpoint('comments-and-discussion-api', new ApiEndpoint('cms/comments-and-discussion/{comment}', CommentController::class, 'update', $prefix.'update', 'PATCH', ['abilities:comments-and-discussion:update']));
        $r->registerEndpoint('comments-and-discussion-api', new ApiEndpoint('cms/comments-and-discussion/{comment}/moderate', CommentController::class, 'moderate', $prefix.'moderate', 'POST', ['abilities:comments-and-discussion:moderate']));
        $r->registerEndpoint('comments-and-discussion-api', new ApiEndpoint('cms/comments-and-discussion/{comment}/reports', CommentController::class, 'report', $prefix.'report', 'POST', ['abilities:comments-and-discussion:report']));
        $r->registerEndpoint('comments-and-discussion-api', new ApiEndpoint('cms/comments-and-discussion/{comment}/subscriptions', CommentController::class, 'subscribe', $prefix.'subscribe', 'POST', ['abilities:comments-and-discussion:subscribe']));
    }
}
