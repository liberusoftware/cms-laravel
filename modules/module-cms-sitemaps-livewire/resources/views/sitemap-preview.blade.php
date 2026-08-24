<div><p>{{ count($entries) }} sitemap entries</p><ul>@foreach (array_slice($entries, 0, 10) as $entry)<li>{{ $entry->url }}</li>@endforeach</ul></div>
