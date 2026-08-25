<div>
    @forelse ($widgets as $widget)
        <section data-widget="{{ $widget['key'] }}"><h3>{{ $widget['title'] }}</h3></section>
    @empty
        <span>No widgets configured.</span>
    @endforelse
</div>
