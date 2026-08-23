# CMS SEO

SEO for the server-rendered (standalone) site: a public `sitemap.xml` and
`robots.txt`, plus `<head>` meta / OpenGraph / JSON-LD tags for content pages.
Both the sitemap and the head tags are unauthenticated and unscoped — they
mirror what the public web routes serve.

## Sitemap

`GET /sitemap.xml` aggregates URLs from every module that registers a
`SitemapUrlProviderInterface` with the `SitemapRegistry`, so the sitemap tracks
the installed modules without this package importing one. A headless-only
deployment that ships no public-URL modules simply gets an empty urlset.

A content module contributes its URLs like this:

```php
// in the module's registerModule(), guarded for headless safety
if ($this->app->bound(SitemapRegistryInterface::class)) {
    $this->app->make(SitemapRegistryInterface::class)
        ->registerProvider($this->app->make(PageSitemapProvider::class));
}
```

## robots.txt

`GET /robots.txt` renders the crawl groups from `config('cms-seo.robots.groups')`
and appends the sitemap location. Customize via the published config.

## Head tags

Add meta / OpenGraph / Twitter / JSON-LD tags to a server-rendered page by
pushing the component into the layout's `head` stack:

```blade
@push('head')
    <x-cms-seo::meta
        :title="$page->title"
        :description="$page->excerpt"
        :canonical="url('/'.$page->slug)"
        type="article"
        :publishedTime="$page->publishedAt()?->format(DATE_ATOM)"
    />
@endpush
```

The component takes scalar props only (it imports no content models), escapes
all attribute values, and encodes the JSON-LD with `JSON_HEX_TAG` so
author-supplied text cannot break out of the `<script>`.

## Config

Publish with `php artisan vendor:publish --tag=cms-seo-config`. Keys:
`robots.groups` (crawl rules), `meta.site_name` (defaults to `app.name`),
`meta.default_description`, `meta.twitter`.
