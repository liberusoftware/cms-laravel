<div>
    <label for="cms-search-query">Search</label>
    <input id="cms-search-query" wire:model.live="query" type="search" autocomplete="off">
    @forelse ($results as $result)
        <article wire:key="search-result-{{ $result->type }}-{{ $result->id }}"><strong>{{ $result->title }}</strong><span>{{ $result->excerpt }}</span></article>
    @empty
        @if ($query !== '')<p>No results found.</p>@endif
    @endforelse
</div>
