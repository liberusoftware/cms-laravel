<section aria-label="Audit history">
    <label>
        Action
        <input type="text" wire:model.live="action" maxlength="255">
    </label>
    <ol>
        @forelse ($logs as $log)
            <li><time datetime="{{ $log->created_at?->toISOString() }}">{{ $log->created_at }}</time> {{ $log->action }}</li>
        @empty
            <li>No audit history found.</li>
        @endforelse
    </ol>
</section>
