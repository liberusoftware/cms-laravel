# cms-pages

Hierarchical pages for Liberu CMS, built on the content foundation.

Depends on the **Media** module (declared in the descriptor) so it cannot be
enabled without it. Uses the **cms-content** foundation for editorial workflow
and versioning, and resolves featured images through the **media contract** —
never importing the Media module's classes.

## The `Page` model

- **Workflow** (via `HasWorkflow`): `Draft → Review → Published → Archived` +
  scheduling. Implements `PublishableInterface`.
- **Versioning** (via `HasRevisions`): numbered snapshots and `revertTo()`.
- **Hierarchy**: `parent()` / `children()` self-relations.
- **Slugs**: auto-generated (unique) from the title when left blank.
- **Featured media**: `featuredMedia()` returns a `MediaItemInterface` resolved
  from the media contract, or null.

```php
$page = Page::create(['title' => 'About Us', 'content' => '…']);
$page->slug;            // "about-us"
$page->publish();       // → Published, emits ContentPublished
$page->isLive();        // true
$page->recordRevision();
$page->revertTo(1);
```

## Repository

`PageRepositoryInterface` (module-internal until another module needs pages):
`find`, `findBySlug`, `published`, `roots`.

## Config (`config/cms-pages.php`)

| Key | Default | Purpose |
|-----|---------|---------|
| `default_template` | `default` | Template used when none is chosen. |

## Events

- **Emits:** via the content foundation — `ContentStateChanged`, `ContentPublished`.
- **Listens:** none.

## Migration note

This module owns the `cms_pages` table. The legacy host `pages` table / model /
Filament resource remain in place during the strangler migration; cutover and
retirement are tracked in `docs/OPEN-QUESTIONS.md`.
