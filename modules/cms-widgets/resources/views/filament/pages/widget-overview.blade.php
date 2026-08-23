<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-3">
        @foreach ($this->areas() as $area)
            <x-filament::section wire:key="area-{{ $area['area'] }}">
                <x-slot name="heading">{{ $area['area'] }}</x-slot>

                @if (count($area['widgets']) === 0)
                    <p class="text-sm italic text-gray-400 dark:text-gray-500">
                        No widgets assigned.
                    </p>
                @else
                    <ol class="flex flex-col gap-3">
                        @foreach ($area['widgets'] as $widget)
                            <li
                                wire:key="widget-{{ $area['area'] }}-{{ $widget['key'] }}"
                                class="flex items-center justify-between gap-2"
                            >
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $widget['title'] }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $widget['key'] }}
                                    </span>
                                </div>

                                <x-filament::badge color="gray" size="sm">
                                    #{{ $widget['order'] }}
                                </x-filament::badge>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
