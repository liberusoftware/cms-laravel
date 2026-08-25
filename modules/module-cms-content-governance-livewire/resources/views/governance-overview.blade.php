<div>
    <h2>Content governance</h2>
    @forelse ($records as $record)
        <article wire:key="governance-record-{{ $record->id }}"><strong>{{ $record->subject_type }}: {{ $record->subject_key }}</strong><span>{{ $record->classification }}</span>@if ($record->legal_hold)<span>Legal hold</span>@endif</article>
    @empty
        <p>No governance records are available.</p>
    @endforelse
</div>
