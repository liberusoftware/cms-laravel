<div>
    @if ($layout)
        @foreach (($layout->definition['regions'] ?? []) as $region => $items)
            <section wire:key="layout-region-{{ $region }}"><h3>{{ $region }}</h3><span>{{ count($items) }} components</span></section>
        @endforeach
    @endif
</div>
