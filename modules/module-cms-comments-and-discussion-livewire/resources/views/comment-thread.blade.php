<div>
    @forelse ($comments as $comment)
        <article wire:key="comment-{{ $comment->id }}"><p>{{ $comment->body }}</p><small>{{ $comment->created_at?->toIso8601String() }}</small></article>
    @empty
        <p>No approved comments yet.</p>
    @endforelse
    <form wire:submit="submit"><textarea wire:model="body" aria-label="Comment"></textarea><button type="submit">Post comment</button></form>
</div>
