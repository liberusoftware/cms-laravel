<section aria-label="Media library">
    <label>
        Folder
        <input type="text" wire:model.live="folder" maxlength="255">
    </label>
    <ul>
        @forelse ($items as $item)
            <li>
                <a href="{{ $item->url() }}">{{ $item->fileName() }}</a>
                <span>{{ $item->mimeType() }}</span>
            </li>
        @empty
            <li>No media found.</li>
        @endforelse
    </ul>
</section>
