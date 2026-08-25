<li wire:key="cms-page-{{ $page->getKey() }}">
    <a href="{{ url($page->path()) }}">{{ $page->title }}</a>
    @if ($page->children->isNotEmpty())
        <ul>
            @foreach ($page->children as $child)
                @include('cms-pages-livewire::livewire.page-node', ['page' => $child])
            @endforeach
        </ul>
    @endif
</li>
