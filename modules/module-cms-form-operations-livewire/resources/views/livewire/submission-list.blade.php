<div>
    <label>
        <span class="sr-only">Filter submissions by form</span>
        <input type="search" wire:model.live.debounce.300ms="formId" inputmode="numeric" placeholder="Form ID">
    </label>
    <ul>
        @forelse ($submissions as $submission)
            <li wire:key="submission-{{ $submission->public_id }}">Form {{ $submission->form_id }} — {{ $submission->status }} — {{ $submission->created_at?->toISOString() }}</li>
        @empty
            <li>No submissions found.</li>
        @endforelse
    </ul>
    {{ $submissions->links() }}
</div>
