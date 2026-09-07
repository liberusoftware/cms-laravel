<x-filament-panels::page>
    <div class="mb-6 grid max-w-5xl gap-4 md:grid-cols-[1fr_auto] md:items-center">
        <div class="space-y-1">
            <p class="text-base font-medium text-gray-950 dark:text-white">A short checklist before you start building.</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
        </div>
        <div class="rounded-xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-900 dark:border-primary-900 dark:bg-primary-950 dark:text-primary-100">
            <span class="font-medium">3 steps</span>
            <span class="mx-1 text-primary-400">·</span>
            Usually takes 2 minutes
        </div>
    </div>
    <div class="mb-6 max-w-5xl rounded-xl border border-gray-200 bg-white px-4 py-3 text-xs text-gray-500 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
        You can change these choices later from Account, Teams, and Settings. OAuth secrets are encrypted per workspace. Delivery tokens are shown only once.
    </div>

    {{ $this->form }}
</x-filament-panels::page>
