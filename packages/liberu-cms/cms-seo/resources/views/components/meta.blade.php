@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'type' => 'website',
    'publishedTime' => null,
])
@php
    $siteName = config('cms-seo.meta.site_name') ?: config('app.name');
    $description = $description ?: config('cms-seo.meta.default_description');
    $twitter = config('cms-seo.meta.twitter');
    $jsonLd = array_filter([
        '@context' => 'https://schema.org',
        '@type' => $type === 'article' ? 'Article' : 'WebPage',
        'headline' => $title,
        'name' => $title,
        'description' => $description ?: null,
        'url' => $canonical,
        'image' => $image,
        'datePublished' => $publishedTime,
    ], fn ($value) => $value !== null && $value !== '');
@endphp
@if ($description)
<meta name="description" content="{{ $description }}" />
@endif
@if ($canonical)
<link rel="canonical" href="{{ $canonical }}" />
@endif
<meta property="og:type" content="{{ $type }}" />
@if ($title)
<meta property="og:title" content="{{ $title }}" />
@endif
@if ($description)
<meta property="og:description" content="{{ $description }}" />
@endif
@if ($canonical)
<meta property="og:url" content="{{ $canonical }}" />
@endif
@if ($siteName)
<meta property="og:site_name" content="{{ $siteName }}" />
@endif
@if ($image)
<meta property="og:image" content="{{ $image }}" />
@endif
<meta name="twitter:card" content="{{ $image ? 'summary_large_image' : 'summary' }}" />
@if ($twitter)
<meta name="twitter:site" content="{{ $twitter }}" />
@endif
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
