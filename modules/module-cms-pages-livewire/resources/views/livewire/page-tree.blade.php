<div>
    <label for="cms-pages-search">Search pages</label>
    <input id="cms-pages-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($pages as $page)
            <li wire:key="cms-page-{{ $page->getKey() }}">
                <a href="{{ url($page->path()) }}">{{ $page->title }}</a>
                @if ($page->children->isNotEmpty())
                    <ul>
                        @foreach ($page->children as $child)
                            <li wire:key="cms-page-{{ $child->getKey() }}"><a href="{{ url($child->path()) }}">{{ $child->title }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @empty
            <li>No pages found.</li>
        @endforelse
    </ul>
</div>
