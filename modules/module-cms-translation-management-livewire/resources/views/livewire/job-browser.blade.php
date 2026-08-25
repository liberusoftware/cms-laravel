<div>
    <input wire:model.live="search" placeholder="Search translation jobs">
    @forelse ($jobs as $job)
        <article wire:key="translation-job-{{ $job->public_id }}">
            <h2>{{ $job->name }}</h2>
            <p>{{ $job->source_locale }} → {{ $job->target_locale }} · {{ $job->status }}</p>
            <button type="button" wire:click="reconcile('{{ $job->public_id }}')">Reconcile</button>
        </article>
    @empty
        <p>No translation jobs.</p>
    @endforelse
    {{ $jobs->links() }}
</div>
