{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url->loc }}</loc>
@if ($url->lastModified)
        <lastmod>{{ $url->lastModified->format(DATE_ATOM) }}</lastmod>
@endif
@if ($url->changeFrequency)
        <changefreq>{{ $url->changeFrequency }}</changefreq>
@endif
@if ($url->priority !== null)
        <priority>{{ number_format($url->priority, 1) }}</priority>
@endif
    </url>
@endforeach
</urlset>
