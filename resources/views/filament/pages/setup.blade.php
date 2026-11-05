<x-filament-panels::page>
    <div class="cms-setup-note">
        <x-filament::icon icon="heroicon-o-bookmark" class="h-5 w-5" />
        <p>{{ __('Progress is saved when you select Next. You can leave and return to finish later.') }}</p>
    </div>
    <form wire:submit="complete" class="space-y-6">
        {{ $this->form }}
    </form>
    <div>
        <x-filament::link :href="\Filament\Facades\Filament::getUrl()" icon="heroicon-o-arrow-left">
            {{ __('Back to overview') }}
        </x-filament::link>
    </div>
</x-filament-panels::page>
