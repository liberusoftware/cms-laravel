<div>
    @foreach ($migrations as $migration)
        <div wire:key="joomla-migration-{{ $migration->id }}">{{ $migration->public_id }}: {{ $migration->status }} ({{ $migration->processed_records }}/{{ $migration->total_records }})</div>
    @endforeach
</div>
