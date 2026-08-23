# cms-menus

Menu & navigation for Liberu CMS: multi-level menus assigned to locations, with
**permission-aware rendering**.

## Models

- **`Menu`** — a named menu at a `location` (header, footer, sidebar, mobile, …).
- **`MenuItem`** — a link with `label`, `url`, `sort`, an optional `parent_id`
  for nesting, and an optional `permission`.

## Permission-aware tree

`MenuBuilder::tree($menu)` returns the visible, nested tree (`MenuNode[]`) for
the current user. An item with a required `permission` — and its whole subtree —
is hidden from users who lack it, authorising through the **access contract** so
this module never touches roles or the users table.

```php
$menu = app(\Liberu\Cms\Menus\Contracts\MenuRepositoryInterface::class)->forLocation('header');
$tree = app(\Liberu\Cms\Menus\MenuBuilder::class)->tree($menu);
// [MenuNode{ label, url, children: [...] }, ...] — only what the user may see
```

## Notes

- Owns `cms_menus` / `cms_menu_items`. The legacy host `Menu`/`MenuItem` +
  Filament Menu Builder wiring remain during the strangler migration; the admin
  builder is kept behind this module's contract per the architecture (see
  `docs/OPEN-QUESTIONS.md`).

## Public contracts

`MenuRepositoryInterface` (module-internal), and it depends on the platform
`AccessControlInterface`.

## Events

- **Emits / Listens:** none.
