# cms-blocks

The block system and page-builder rendering for Liberu CMS: a registry of block
types and a recursive, **XSS-safe** renderer.

## Model

A block is a JSON-shaped array:

```php
['type' => 'columns', 'data' => ['columns' => 2], 'children' => [
    ['type' => 'heading', 'data' => ['level' => 2, 'text' => 'Hello']],
    ['type' => 'text', 'data' => ['text' => 'World']],
]]
```

`BlockRenderer` (bound to `BlockRendererInterface`) resolves each block's type
from the `BlockTypeRegistry` and renders it, recursing into `children` — the
basis of nested page-builder layouts. An unknown type renders to an empty string
rather than throwing, so removing a block type never breaks a page.

```php
$renderer = app(\Liberu\Cms\Contracts\Block\BlockRendererInterface::class);
$html = $renderer->renderMany($page->blocks);
```

## Prebuilt types

`text`, `heading`, `image`, `code`, `cta`, and `columns` (a nesting container).
All output is HTML-escaped — blocks never emit untrusted markup (OWASP A03).

## Custom types

Implement `BlockTypeInterface` (or extend `AbstractBlockType`) and register it:

```php
app(\Liberu\Cms\Blocks\BlockTypeRegistry::class)->register(new MyBlock);
```

## Public contracts

`BlockRendererInterface`, `BlockTypeInterface` (in `cms-contracts`).

## Events

- **Emits / Listens:** none.
