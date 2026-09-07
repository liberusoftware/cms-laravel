<x-filament-panels::page>
    <form wire:submit="analyze" class="space-y-4">
        <textarea wire:model="html" rows="14" class="w-full rounded-lg border-gray-300" placeholder="Paste HTML to analyze"></textarea>
        <x-filament::button type="submit">Analyze</x-filament::button>
    </form>
    <ul class="mt-6 space-y-2">
        @foreach ($findings as $finding)
            <li><strong>{{ $finding['severity'] }}</strong>: {{ $finding['message'] }}</li>
        @endforeach
    </ul>
</x-filament-panels::page>
