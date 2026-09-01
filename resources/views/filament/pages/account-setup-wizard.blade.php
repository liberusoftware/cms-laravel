<x-filament-panels::page>
    <div class="mb-6 max-w-3xl space-y-1">
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-500">You can change these choices later from Account, Teams, and Settings.</p>
    </div>

    {{ $this->form }}
</x-filament-panels::page>
