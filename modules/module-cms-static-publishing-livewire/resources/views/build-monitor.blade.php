<div><ul>@forelse ($builds as $build)<li>{{ $build->kind }} / {{ $build->state }} / {{ count($build->manifest ?? []) }} routes</li>@empty<li>No builds.</li>@endforelse</ul></div>
