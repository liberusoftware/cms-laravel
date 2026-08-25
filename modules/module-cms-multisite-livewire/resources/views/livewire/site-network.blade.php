<section data-cms-multisite-network>
    @foreach ($sites as $site)
        <article wire:key="site-{{ $site->id }}"><strong>{{ $site->name }}</strong><span>{{ $site->status }}</span><small>{{ $site->domain }}</small></article>
    @endforeach
</section>
