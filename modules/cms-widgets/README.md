# cms-widgets

The widget system for Liberu CMS: register widgets and render them by area
(sidebar, dashboard, footer).

## Model

A widget implements `WidgetInterface` — `key`, `title`, `area` (a `WidgetArea`
enum), `order`, and `render()` (returns HTML). The `WidgetRegistry` collects
them; `forArea()` returns an area's widgets ordered ascending, and
`renderArea()` concatenates their HTML.

```php
$registry = app(\Liberu\Cms\Widgets\WidgetRegistry::class);
$registry->register(new SocialLinksWidget(['GitHub' => 'https://github.com/...']));

$html = $registry->renderArea(\Liberu\Cms\Contracts\Widget\WidgetArea::Footer);
```

## Prebuilt widgets

- `SearchWidget` (sidebar) — a search form.
- `SocialLinksWidget` (footer) — escaped social links.

## Custom widgets

Implement `WidgetInterface` and register it. Escape any user-provided output.

## Public contracts

`WidgetInterface`, `WidgetArea` (in `cms-contracts`).

## Events

- **Emits / Listens:** none.
