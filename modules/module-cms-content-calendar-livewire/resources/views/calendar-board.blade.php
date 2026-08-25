<div>
    <h2>Content calendar</h2>
    @forelse ($items as $item)
        <article wire:key="calendar-item-{{ $item->id }}">
            <strong>{{ $item->title }}</strong>
            <time datetime="{{ $item->starts_at?->toIso8601String() }}">{{ $item->starts_at?->format('Y-m-d H:i') }}</time>
        </article>
    @empty
        <p>No calendar items match these filters.</p>
    @endforelse
</div>
