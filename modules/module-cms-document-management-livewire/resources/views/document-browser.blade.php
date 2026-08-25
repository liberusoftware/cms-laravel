<div>
    @foreach ($documents as $document)
        <article><strong>{{ $document->title }}</strong> <span>{{ $document->status }}</span></article>
    @endforeach
</div>
