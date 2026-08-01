<x-layouts.app :title="$collection->name">
    <div class="max-w-6xl mx-auto px-4 py-12">
        {{-- Header --}}
        <header class="mb-10">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900">
                {{ $collection->name }}
            </h1>

            @if($collection->description)
                <p class="mt-3 max-w-2xl text-lg text-gray-600">
                    {{ $collection->description }}
                </p>
            @endif
        </header>

        {{-- Items --}}
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($items as $item)
                <a
                    href="{{ url($collection->slug . '/' . $item->slug) }}"
                    class="group block rounded-xl border border-gray-200 p-6 transition hover:border-gray-300 hover:shadow-sm"
                >
                    <h2 class="text-xl font-semibold text-gray-900 group-hover:underline">
                        {{ $item->title }}
                    </h2>

                    @if($item->excerpt)
                        <p class="mt-3 text-sm text-gray-600 line-clamp-3">
                            {{ $item->excerpt }}
                        </p>
                    @endif

                    <div class="mt-4 text-sm text-gray-500">
                        {{ optional($item->published_at)->format('M d, Y') }}
                    </div>
                </a>
            @empty
                <p class="text-gray-500">
                    No content yet.
                </p>
            @endforelse
        </div>

        @if($items->hasPages())
            <div class="mt-12">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
