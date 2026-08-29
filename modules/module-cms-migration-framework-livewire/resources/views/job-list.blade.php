<div>
    @foreach ($jobs as $job)
        <div wire:key="migration-job-{{ $job->id }}">{{ $job->source }}: {{ $job->status }} ({{ $job->processed_records }}/{{ $job->total_records }})</div>
    @endforeach
</div>
