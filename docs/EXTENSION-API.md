# Liberu CMS — Public Extension API

This is the surface a third-party extension may depend on. Everything listed here
lives in the `liberusoftware/cms-contracts` package and is tagged `@api` in its
docblock. Anything in that package tagged `@internal` — or any concrete class in
another module — is **not** part of this contract and may change without notice.

A Pest guard (`tests/Feature/Cms/PublicApiTest.php`) enforces two invariants on
every CI run: every contract is designated `@api` or `@internal`, and no
`@internal` type leaks through an `@api` method signature. A new contract with no
designation fails the build, so the catalogue below cannot silently drift.

## Stability & deprecation policy

- **`@api` is a semver promise.** Within a major version, an `@api` interface,
  class, enum, or value object will not change in a way that breaks an extension
  that used it as documented — no removed methods, no narrowed parameter types, no
  widened return types, no renamed classes.
- **Additive change is always allowed.** New optional methods, new value objects,
  and new filter points can arrive in a minor release.
- **Deprecation window.** A public contract slated for removal is marked
  `@deprecated` (with the replacement named) for at least one minor release before
  it is removed in the next major.
- **`@internal` means no promise.** Internal contracts are kernel wiring; depend on
  them and you accept that any release may change them.
- **Import rules (arch-enforced).** An extension must not import the host `App\`
  namespace, nor another module's concrete classes. Talk to the platform only
  through the contracts below (and events). See `tests/Feature/Cms/ArchitectureTest.php`.

## Catalogue

### Build & register a module
- `Module\ModuleInterface` — implement (via `Liberu\Cms\Core\Module\AbstractModule`) to declare a module: its key, name, version, and whether it is foundational.
- `Module\ModuleManagerInterface` — query and toggle modules: `isEnabled()`, `enable()`, `disable()`, boot order, dependencies.
- `Module\ModuleDependencyExceptionInterface` — catch when an enable/disable is rejected by dependency rules.

### Hooks — transform a value (consume **and** define)
- `Hooks\HookBusInterface` — `listen()` a callback on a filter point, or `apply()` a filter at your own emit site. Resolve it from the container.
- `Hooks\Filter` — implement to **define your own** filter point: a value object with a mutable payload that callbacks transform in place.
- `Hooks\Filters\BlockRenderFilter` — **consume** to rewrite a block's rendered HTML.
- `Hooks\Filters\AdminFormSchemaFilter` — **consume** to add or modify components on another module's Filament form.
- `Hooks\Filters\ApiResourceFilter` — **consume** to reshape a Delivery API payload before it serializes.
- `Hooks\Filters\ContentQueryFilter` — **consume** to adapt a content query before it runs (tenant isolation is re-asserted after your callback — you cannot widen past the current tenant).

### Events — react to something that happened
- `Events\EventBusInterface` — `dispatch()` a `CmsEvent`, or `listen()` for one.
- `Events\CmsEvent` — implement to define a cross-module event.
- `Events\Content\ContentPublished`, `Events\Content\ContentStateChanged`, `Events\Form\FormSubmitted`, `Events\Media\MediaUploaded`, `Events\Theme\ThemeActivated` — listen to react to platform activity.

### Admin surfaces
- `Admin\AdminResourceRegistryInterface` — register a module-owned Filament resource or page into the panel.
- `Admin\AdminDashboardRegistryInterface` + `Admin\DashboardStat` — contribute a stat to the admin dashboard.

### Delivery (headless) API
- `Api\ApiResourceRegistryInterface` + `Api\ApiEndpoint` — register a versioned `/api/v1` endpoint owned by your module.

### Content field types
- `Fields\FieldTypeRegistryInterface` + `Fields\FieldTypeDefinition` — register a custom content-type field kind (edit component + validation predicate) that appears in the schema editor and validates like a built-in.

### Blocks
- `Block\BlockTypeRegistryInterface` — register a block type so the renderer can resolve it by key.
- `Block\BlockTypeInterface` — implement a block type (given data + children HTML, return HTML).
- `Block\BlockRendererInterface` — render a JSON block tree to HTML.

### Widgets
- `Widget\WidgetInterface` + `Widget\WidgetArea` — implement a widget and place it in a defined area (sidebar, dashboard, footer).

### Themes
- `Theme\ThemeManagerInterface` — register, activate, and resolve themes and their view overrides.
- `Theme\ThemeInterface` — implement a theme (key, parent, view paths).

### Search
- `Search\SearchRegistryInterface` — register a searchable source into the Delivery API search endpoint.
- `Search\SearchableSourceInterface` + `Search\SearchResult` — implement a source and shape its results.
- `Search\SearchIndexInterface` — the swappable backend that executes matching (database `LIKE` by default, Scout/Meilisearch opt-in) and reports `isReady()`; bind your own to replace the driver behind the unchanged query surface.
- `Search\ScoutSearchableSourceInterface` — implement (in addition to `SearchableSourceInterface`) to opt a source's model into the Scout driver via `scoutSearch()`.

### SEO / sitemap
- `Seo\SitemapRegistryInterface` — register a sitemap URL provider.
- `Seo\SitemapUrlProviderInterface` + `Seo\SitemapUrl` — implement a provider and describe its URLs.

### Preview / draft
- `Preview\PreviewRegistryInterface` — register a previewable source for signed draft links.
- `Preview\PreviewableSourceInterface` — implement draft-inclusive resolution + the API resource for a preview.

### Permissions & access
- `Access\PermissionRegistrarInterface` + `Access\PermissionGroup` + `Access\AccessScope` — declare a module's permission group and its abilities.
- `Access\AccessControlInterface` — check abilities (`can`, `cannot`, `authorize`) over the framework gate.

### Content workflow
- `Content\WorkflowInterface` + `Content\WorkflowState` — drive draft → review → published → archived transitions.
- `Content\PublishableInterface`, `Content\RevisionInterface`, `Content\WorkflowExceptionInterface` — the publishing, revision, and error contracts content models expose.

### Media
- `Media\MediaRepositoryInterface` + `Media\MediaItemInterface` — query the media library and read a media item (URL, metadata).

### Observability
- `Health\HealthCheckInterface` + `Health\HealthCheckRegistryInterface` — contribute a readiness probe for a dependency your module owns (coarse ok/fail + criticality); it appears in `GET /health/ready`.
- `Metrics\MetricsRecorderInterface` — record a counter / timing / gauge without knowing the backend; resolve it from the container (guard with `bound()`), or bind your own recorder to replace the log-channel default.

## Not public (`@internal`)

These are kernel wiring — do not depend on them:
`Module\ModuleRegistryInterface`, `Module\ModuleStateRepositoryInterface`,
`Tenancy\TenantContextInterface`, `Tenancy\TenantModelResolverInterface`.
