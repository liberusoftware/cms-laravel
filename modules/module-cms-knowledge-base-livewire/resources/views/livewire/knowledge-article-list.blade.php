<div>
    <label>
        <span class="sr-only">Search knowledge articles</span>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search knowledge articles">
    </label>
    <ul>
        @forelse ($articles as $article)
            <li wire:key="article-{{ $article->public_id }}">{{ $article->title }}</li>
        @empty
            <li>No articles found.</li>
        @endforelse
    </ul>
    {{ $articles->links() }}
</div>
