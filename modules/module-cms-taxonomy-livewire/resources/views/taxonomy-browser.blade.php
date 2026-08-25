<div><input wire:model.live="search" placeholder="Search terms"><ul>@foreach ($terms as $term)<li>{{ $term->name }} ({{ $term->assignments_count ?? 0 }})</li>@endforeach</ul></div>
