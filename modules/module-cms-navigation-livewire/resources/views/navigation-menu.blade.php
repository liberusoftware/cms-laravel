<nav aria-label="{{ $location }} navigation">
    <ul>
        @forelse ($nodes as $node)
            @include('cms-navigation-livewire::navigation-node', ['node' => $node])
        @empty
            <li>No navigation items.</li>
        @endforelse
    </ul>
</nav>
