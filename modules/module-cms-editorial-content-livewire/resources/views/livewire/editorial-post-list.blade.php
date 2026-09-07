<div>
    <label>
        <span class="sr-only">Search posts</span>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search posts">
    </label>
    <ul>
        @forelse ($posts as $post)
            <li wire:key="post-{{ $post->public_id }}">{{ $post->title }} <span>{{ $post->status }}</span></li>
        @empty
            <li>No posts found.</li>
        @endforelse
    </ul>
    {{ $posts->links() }}
</div>
