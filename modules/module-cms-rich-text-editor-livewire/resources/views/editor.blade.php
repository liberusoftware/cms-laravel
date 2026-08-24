<div><textarea wire:model.live="content" aria-label="Rich text content"></textarea>@if ($hints)<ul>@foreach ($hints as $hint)<li>{{ $hint }}</li>@endforeach</ul>@endif</div>
