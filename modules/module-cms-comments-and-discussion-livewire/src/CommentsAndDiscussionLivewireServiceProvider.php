<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussionLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CommentsAndDiscussionLivewire\Livewire\CommentThread;
use Livewire\Livewire;

final class CommentsAndDiscussionLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-comments-and-discussion.comment-thread', CommentThread::class);
    }
}
