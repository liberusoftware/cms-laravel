<x-layouts.app :title="$page->title">
    @push('head')
        <x-cms-seo::meta
            :title="$page->title"
            :description="$page->excerpt"
            :canonical="url($page->slug === 'home' ? '/' : '/'.$page->slug)"
            type="article"
            :publishedTime="$page->publishedAt()?->format(DATE_ATOM)"
        />
    @endpush

    <article class="max-w-3xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900">
            {{ $page->title }}
        </h1>

        <div class="mt-8 prose prose-lg max-w-none">
            @sanitize($page->content)
        </div>
    </article>
</x-layouts.app>
