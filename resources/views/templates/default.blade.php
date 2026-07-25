<x-layouts.app>
    @push('head')
        <x-cms-seo::meta
            :title="$page->title"
            :description="$page->excerpt"
            :canonical="url($page->slug === 'home' ? '/' : '/'.$page->slug)"
            type="article"
            :publishedTime="$page->publishedAt()?->format(DATE_ATOM)"
        />
    @endpush

    <h1>{{ $page->title }}</h1>

    @sanitize($page->content)
</x-layouts.app>
