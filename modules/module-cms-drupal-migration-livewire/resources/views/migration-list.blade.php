<div>
    @foreach ($migrations as $migration)
        <div wire:key="drupal-migration-{{ $migration->id }}">{{ $migration->public_id }}: {{ $migration->status }} ({{ $migration->processed_records }}/{{ $migration->total_records }})</div>
    @endforeach
</div>
