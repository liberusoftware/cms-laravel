<x-layouts.app :title="$item->title">
    <article class="max-w-3xl mx-auto px-4 py-12">
        {{-- Header --}}
        <header class="mb-8">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900">
                {{ $item->title }}
            </h1>

            <div class="mt-3 flex items-center gap-4 text-sm text-gray-500">
                <span>
                    {{ optional($item->published_at)->format('M d, Y') }}
                </span>

                @if($item->user)
                    <span>•</span>
                    <span>
                        By {{ $item->user->name }}
                    </span>
                @endif
            </div>
        </header>

        {{-- Content --}}
        <div class="prose prose-lg max-w-none">
            @sanitize($item->content)
        </div>

        {{-- Footer --}}
        <footer class="mt-12 pt-6 border-t text-sm text-gray-500">
            <a
                href="{{ url($item->collection->slug) }}"
                class="hover:underline"
            >
                ← Back to {{ $item->collection->name }}
            </a>
        </footer>
    </article>
</x-layouts.app>
