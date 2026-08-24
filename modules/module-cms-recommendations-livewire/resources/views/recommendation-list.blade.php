<div><ul>@forelse ($items as $item)<li><strong>{{ $item['title'] }}</strong><small>{{ $item['explanation'] }}</small></li>@empty<li>No recommendations available.</li>@endforelse</ul></div>
