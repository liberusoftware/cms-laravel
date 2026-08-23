# cms-themes

The theme engine for Liberu CMS: register Blade themes, inherit from a parent
theme, resolve view overrides along the inheritance chain, and switch the active
theme at runtime.

## Concepts

- A **theme** (`ThemeInterface`) is a named views directory with an optional
  parent. Register the built-in `Theme` value class or your own descriptor.
- The **active** theme is persisted (in `cms_theme_state`) and falls back to the
  configured default; the module registers a `default` theme pointing at
  `resources/views`.
- **Override resolution**: `resolveView('layouts.app')` walks the active theme
  then its ancestors and returns the first matching file — so a child theme need
  only override the views it changes. At boot, the active theme's view paths are
  prepended to Blade's finder, so ordinary `view()` calls pick up overrides.

```php
$themes = app(\Liberu\Cms\Contracts\Theme\ThemeManagerInterface::class);

$themes->register(new Theme('base', 'Base', '/themes/base/views'));
$themes->register(new Theme('shop', 'Shop', '/themes/shop/views', parent: 'base'));

$themes->setActive('shop');           // → ThemeActivated event
$themes->resolveView('home');         // /themes/shop/views/home.blade.php
$themes->resolveView('layouts.app');  // falls back to /themes/base/views/... 
$themes->inheritanceChain();          // [shop, base]
```

## Config (`config/cms-themes.php`)

| Key | Default | Purpose |
|-----|---------|---------|
| `default` | `default` | Theme key used when none is active. |

## Events

- **Emits:** `Theme\ThemeActivated` on switch.
- **Listens:** none.

## Public contracts

`ThemeManagerInterface`, `ThemeInterface` (in `cms-contracts`).
