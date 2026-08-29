<div>
    @if ($preview)
        <p>Restore preview for {{ $preview['artifact_type'] }} backup</p>
        <p>Confirmation required before restore.</p>
    @else
        <p>Select a backup to preview.</p>
    @endif
</div>
