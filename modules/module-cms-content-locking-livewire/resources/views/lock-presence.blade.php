<div>
    @if ($lock)<span>Being edited by user {{ $lock->holder_id }} until {{ $lock->expires_at?->format('H:i') }}.</span>@else<span>Available for editing.</span>@endif
</div>
