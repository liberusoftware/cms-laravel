<div>@if ($result)<strong>SEO score: {{ $result['score'] }}</strong><ul>@foreach ($result['issues'] as $issue)<li>{{ $issue }}</li>@endforeach</ul>@else<span>SEO check unavailable.</span>@endif</div>
