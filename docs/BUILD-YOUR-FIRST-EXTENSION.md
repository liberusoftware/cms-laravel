# Build your first extension

A Liberu CMS extension is an ordinary Composer package that talks to the platform
only through the public contracts in `liberu-cms/cms-contracts` (see
[EXTENSION-API.md](./EXTENSION-API.md)). This guide builds one from scratch. The
in-repo `modules/cms-hello` package is the worked example — it exercises
every seam below and is covered by `tests/Feature/Cms/HelloReferenceExtensionTest.php`.

## 1. Scaffold the module

```bash
php artisan cms:make-module Portfolio
composer update liberu-cms/cms-portfolio
php artisan migrate
```

You now have `modules/cms-portfolio` with a service provider that boots,
enables, and disables out of the box. Everything below is added to that module.

## 2. Add a block

```bash
php artisan cms:make-block portfolio Project
```

This writes `src/Blocks/ProjectBlock.php` (a `BlockTypeInterface`). Register it in
your provider's `bootModule()` — the command prints the exact snippet:

```php
use Liberu\Cms\Contracts\Block\BlockTypeRegistryInterface;

if ($this->app->bound(BlockTypeRegistryInterface::class)) {
    $this->app->make(BlockTypeRegistryInterface::class)->register(new Blocks\ProjectBlock);
}
```

## 3. Add a hook — consume vs. define

Hooks let you **transform a value mid-flight**, which events and registries cannot.
There are two directions:

- **Consume** a core filter point to adapt existing behaviour. Register a callback
  on the HookBus for one of the built-in filters (`BlockRenderFilter`,
  `AdminFormSchemaFilter`, `ApiResourceFilter`, `ContentQueryFilter`):

  ```php
  use Liberu\Cms\Contracts\Hooks\Filters\AdminFormSchemaFilter;
  use Liberu\Cms\Contracts\Hooks\HookBusInterface;

  $this->app->make(HookBusInterface::class)->listen(
      AdminFormSchemaFilter::class,
      function (AdminFormSchemaFilter $filter): void {
          if ($filter->resource === 'pages') {
              $filter->components[] = TextInput::make('subtitle');
          }
      },
  );
  ```

- **Define** your own filter point so other extensions can adapt *your* output —
  extensions are themselves extensible:

  ```bash
  php artisan cms:make-hook portfolio Summary
  ```

  This writes a `SummaryFilter` (a `Filter` with a mutable payload) and an example
  `SummaryListener`. Apply the filter where you produce the value:

  ```php
  $summary = $this->app->make(HookBusInterface::class)
      ->apply(new SummaryFilter($rawSummary))
      ->value;
  ```

The `ContentQueryFilter` is special: tenant isolation is re-asserted **after** your
callback runs, so you can narrow a query but never widen it past the current tenant.

## 4. Add a content-type field kind

```bash
php artisan cms:make-field-type portfolio Rating
```

This writes `src/Fields/RatingFieldType.php`. Register it in `registerModule()`:

```php
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

if ($this->app->bound(FieldTypeRegistryInterface::class)) {
    Fields\RatingFieldType::registerInto($this->app->make(FieldTypeRegistryInterface::class));
}
```

The kind now appears in the content-type schema editor and validates like a built-in.

## 5. Register admin & API surfaces

Guard every registration with a `bound()` check so the module stays **embeddable**
(the registry may be absent in a headless or bare host):

```php
// registerModule()
if ($this->app->bound(AdminResourceRegistryInterface::class)) {
    $this->app->make(AdminResourceRegistryInterface::class)
        ->registerResource('portfolio', ProjectResource::class);
}

if ($this->app->bound(ApiResourceRegistryInterface::class)) {
    $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
        'portfolio',
        new ApiEndpoint('projects', ProjectApiController::class, 'index', 'portfolio.projects'),
    );
}
```

## Shipping it as a real (out-of-tree) package

The in-repo example lives under `modules/`, but a third-party extension is just a
Composer package. In your host app's `composer.json`:

```jsonc
"repositories": [
    { "type": "path", "url": "../cms-portfolio" }   // or "type": "vcs", "url": "git@github.com:you/cms-portfolio.git"
],
"require": {
    "you/cms-portfolio": "*"
}
```

Then `composer update you/cms-portfolio && php artisan migrate`. The package needs
PSR-4 autoloading and a Laravel package-discovery `extra.laravel.providers` entry
pointing at your service provider (both are in the `cms:make-module` output).

### The two hard rules

1. **Never import the host `App\` namespace**, and never import another module's
   concrete classes — talk only to `liberu-cms/cms-contracts` (and events). This is
   enforced by `tests/Feature/Cms/ArchitectureTest.php`; violate it and CI fails.
2. **Depend only on `@api` contracts.** Anything marked `@internal` may change in any
   release. See [EXTENSION-API.md](./EXTENSION-API.md) for the catalog and the
   semver/deprecation policy.

Keep your registrations `bound()`-guarded and your module removable, and your
extension will install, enable, disable, and uninstall cleanly in any host.
