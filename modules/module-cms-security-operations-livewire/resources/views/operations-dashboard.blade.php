<div><ul>@forelse ($operations as $operation)<li>{{ $operation->kind }}: {{ $operation->status }}</li>@empty<li>No security operations recorded.</li>@endforelse</ul></div>
