<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussionLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\CommentsAndDiscussion\Services\CommentService;
use Livewire\Component;

final class CommentThread extends Component
{
    public string $commentableType = '';

    public string $commentableId = '';

    public string $body = '';

    public ?int $parentId = null;

    public function submit(CommentService $service): void
    {
        $this->validate(['body' => ['required', 'string', 'max:10000']]);
        $service->create(['commentable_type' => $this->commentableType, 'commentable_id' => $this->commentableId, 'parent_id' => $this->parentId, 'body' => $this->body], auth()->id(), auth()->user()?->current_team_id);
        $this->reset('body', 'parentId');
        $this->dispatch('comment-created');
    }

    public function render(CommentService $service): View
    {
        return view('module-cms-comments-and-discussion-livewire::comment-thread', ['comments' => $this->commentableType === '' || $this->commentableId === '' ? [] : $service->list($this->commentableType, $this->commentableId, auth()->user()?->current_team_id)]);
    }
}
