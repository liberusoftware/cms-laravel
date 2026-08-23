<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Themes</x-slot>

        <x-slot name="description">
            Every registered theme. Activating a theme switches the view layer to it and
            falls back through its inheritance parent for any views it does not override.
        </x-slot>

        <div class="divide-y divide-gray-100 dark:divide-white/10">
            @foreach ($this->themes() as $theme)
                <div
                    wire:key="theme-{{ $theme['key'] }}"
                    class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                {{ $theme['name'] }}
                            </span>

                            <x-filament::badge color="gray" size="sm">
                                {{ $theme['key'] }}
                            </x-filament::badge>

                            @if ($theme['active'])
                                <x-filament::badge color="success" size="sm">Active</x-filament::badge>
                            @endif
                        </div>

                        @if ($theme['parent'])
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Inherits from: {{ $theme['parent'] }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($theme['active'])
                            <span class="text-xs italic text-gray-400 dark:text-gray-500">
                                Currently active
                            </span>
                        @else
                            <x-filament::button
                                color="primary"
                                size="sm"
                                icon="heroicon-o-check-circle"
                                wire:click="activate('{{ $theme['key'] }}')"
                            >
                                Activate
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>
