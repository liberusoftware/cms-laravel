<div>
    <h2>Contact directory</h2>
    @forelse ($contacts as $contact)
        <article wire:key="contact-{{ $contact->id }}">
            <strong>{{ $contact->name }}</strong>
            @if ($contact->department)<span>{{ $contact->department }}</span>@endif
            @if ($contact->email)<a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>@endif
        </article>
    @empty
        <p>No public contacts are available.</p>
    @endforelse
</div>
