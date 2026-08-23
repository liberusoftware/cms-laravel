<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Installed modules</x-slot>

        <x-slot name="description">
            Every module registered with the CMS. Enable or disable optional modules; the
            system keeps dependencies satisfied and never lets a required module be turned off.
        </x-slot>

        <div class="divide-y divide-gray-100 dark:divide-white/10">
            @foreach ($this->modules() as $module)
                <div
                    wire:key="module-{{ $module->key }}"
                    class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                {{ $module->name }}
                            </span>

                            <x-filament::badge color="gray" size="sm">
                                {{ $module->key }}
                            </x-filament::badge>

                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                v{{ $module->version }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            @if ($module->enabled)
                                <x-filament::badge color="success" size="sm">Enabled</x-filament::badge>
                            @else
                                <x-filament::badge color="danger" size="sm">Disabled</x-filament::badge>
                            @endif

                            @if ($module->dependencies !== [])
                                <span>Requires: {{ implode(', ', $module->dependencies) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($module->isToggleable())
                            @if ($module->enabled)
                                <x-filament::button
                                    color="danger"
                                    size="sm"
                                    icon="heroicon-o-x-circle"
                                    wire:click="disable('{{ $module->key }}')"
                                    wire:confirm="Disable the {{ $module->name }} module?"
                                >
                                    Disable
                                </x-filament::button>
                            @else
                                <x-filament::button
                                    color="success"
                                    size="sm"
                                    icon="heroicon-o-check-circle"
                                    wire:click="enable('{{ $module->key }}')"
                                >
                                    Enable
                                </x-filament::button>
                            @endif
                        @else
                            <span class="text-xs italic text-gray-400 dark:text-gray-500">
                                {{ $module->lockReason() }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>
