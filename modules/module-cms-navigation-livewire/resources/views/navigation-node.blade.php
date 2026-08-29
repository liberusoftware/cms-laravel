<li>
    <a href="{{ url($node->url) }}" @if ($node->active) aria-current="page" @endif>{{ $node->label }}</a>
    @if ($node->children !== [])
        <ul>
            @foreach ($node->children as $child)
                @include('cms-navigation-livewire::navigation-node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
