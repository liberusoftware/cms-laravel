<div>
    <h2>Content templates</h2>
    @forelse ($templates as $template)
        <article wire:key="content-template-{{ $template->id }}"><strong>{{ $template->name }}</strong><span>v{{ $template->version }}</span>@if ($template->published)<span>Published</span>@endif</article>
    @empty
        <p>No templates are available for this content type.</p>
    @endforelse
</div>
