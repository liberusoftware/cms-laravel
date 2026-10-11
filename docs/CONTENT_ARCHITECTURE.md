# Content Architecture

## Overview

The CMS domain models the full lifecycle of digital content: modeling, authoring, publishing, and delivery. The system separates content structure from presentation, supports scheduled publishing and revisions, and provides a media pipeline optimized for CDN delivery.

## Content Types and Fields

- Content types are defined as `ContentType` records with a unique machine name, label, and field configuration.
- Each content type owns an ordered collection of `ContentField` definitions (text, rich text, reference, media, boolean, etc.).
- Custom fields extend `BaseField`, implementing validators, transformers, and API serializers.
- Field validators run during draft save and again during publish, supporting conditional rules (required only when status is `scheduled`).
- Transformers convert stored values to output representations (e.g., markdown to HTML, media ID to signed URL).

## Page and Article Management

- `Page` and `Article` entities share a common `Publishable` concern supporting drafts, revisions, and scheduling.
- Drafts are autosaved on an interval; revisions are immutable snapshots created explicitly or before major edits.
- Scheduled publishing uses a scheduler daemon that transitions status from `scheduled` to `published` at the specified time.
- Revision history is queryable per entity, with diff support and one-click restore.

## Media Library

- Uploads are streamed to temporary storage, scanned for malware, and then moved to permanent object storage.
- Image processing generates derivatives (thumbnails, responsive sizes) via a job queue.
- All derivatives are fingerprinted and pushed to the configured CDN.
- `MediaAsset` records track metadata, alt text, focal points, and usage references across content.

## Menu Builder

- Menus are hierarchical trees of `MenuItem` records with optional permission requirements.
- Targets may be internal routes, content entities, or external URLs; resolution is cached per permission set.
- Menu rendering walks the tree, filters by the current user's roles, and marks the active path.
- Menu cache is invalidated when items change or when permissions affecting visibility are updated.

## Template and Theme System

- Themes are directory-based packages containing layouts, partials, and templates registered via a manifest.
- Template inheritance uses a block/override system: child templates override named blocks from parents.
- Template resolution checks the active theme first, then falls back to the base theme, then to core defaults.
- Blade is the default engine; a pluggable renderer interface allows alternative template languages.

## Search and Indexing

- Content is indexed asynchronously through a message queue after save or publish.
- Full-text search uses a pluggable driver (database, Elasticsearch, or Meilisearch).
- Faceted search exposes aggregations by content type, tag, author, and date range.
- Search results are cached per query signature with invalidation tied to content changes.

## Multi-Language Content

- Each translatable entity has associated `Translation` records keyed by locale.
- Fallback chains are configurable per locale (e.g., `fr-CH` falls back to `fr`, then to `en`).
- Translation workflow statuses (needs review, in progress, published) track completeness per locale.
- The admin UI surfaces untranslated fields and per-locale completeness indicators.

## Role-Based Content Access

- Editorial roles (author, editor, publisher, admin) control transitions between workflow states.
- Custom roles can be defined per content type with granular permissions (view, edit, publish, delete).
- Workflow states include `draft`, `in_review`, `approved`, `scheduled`, `published`, and `archived`.
- Access checks run in middleware for frontend queries and in policies for admin operations.

## Module Boundaries and Extension Points

| Module | Responsibility | Extension Points |
|--------|---------------|------------------|
| Content | Types, fields, entities | Custom field types, validators, transformers |
| Media | Uploads, processing, CDN | Storage drivers, image processors |
| Menus | Tree building, rendering | Custom menu types, target resolvers |
| Themes | Templates, assets | Custom renderers, child themes |
| Search | Indexing, querying | Search drivers, faceted aggregators |
| i18n | Translations, fallback | Custom fallback chains |
| Access | Roles, workflows | Custom states, transition guards |

### Extension Points

- Register new field types via `FieldRegistry::register()`.
- Hook into publishing via the `ContentPublished` event.
- Add custom search drivers implementing the `SearchDriverInterface`.
- Define new workflow states and transitions through config-driven workflow definitions.
