# cms-core

## Repository

Source, issues, and release history: https://github.com/liberusoftware/module-cms-core

Composer package: https://packagist.org/packages/liberusoftware/module-cms-core

The Liberu CMS kernel. It turns a plain Laravel application into a host for
independently installable, removable modules — standalone, embedded, or headless.

Depends only on `cms-contracts` and the framework. It never depends on a module.

## What it provides

| Service (bound as singleton) | Contract | Responsibility |
|------------------------------|----------|----------------|
| `ModuleRegistry` | `ModuleRegistryInterface` | Catalogue of every module whose provider has booted. |
| `ModuleManager` | `ModuleManagerInterface` | Enable/disable with dependency validation; topological boot order. |
| `DatabaseModuleStateRepository` | `ModuleStateRepositoryInterface` | Persists enable/disable in `cms_modules`; degrades gracefully pre-migration. |
| `EventBus` | `EventBusInterface` | Type-safe wrapper over the framework dispatcher for cross-module events. |

Plus the base classes every module extends: `AbstractModule` (descriptor) and
`ModuleServiceProvider` (self-gating isolation).

## How a module stays isolated and removable

A module's provider extends `ModuleServiceProvider`. It is always discovered by
Laravel, but during `boot()` it:

1. announces its descriptor to the registry (so the dependency graph is complete
   even for disabled modules), then
2. loads its routes, migrations, views, config, and listeners **only** when
   `ModuleManager::isEnabled()` is true.

Disabling a module leaves its provider registered but inert. Nothing else in the
app changes, which is what makes "disable any module, suite stays green" true.

## Enable / disable

```php
$manager = app(\Liberu\Cms\Contracts\Module\ModuleManagerInterface::class);

$manager->isEnabled('hello');   // bool
$manager->enable('hello');      // throws if a dependency is missing/disabled
$manager->disable('hello');     // throws if foundational or an enabled module needs it
$manager->bootOrder();          // ['contracts-first', ..., 'dependents-last']
```

Config-driven kill switch (ideal for embedded hosts) in `config/cms.php`:

```php
'disabled_modules' => ['hello'],
```

## Generate a module

```bash
php artisan cms:make-module Portfolio
composer update liberusoftware/cms-portfolio
php artisan migrate
```

The generated package under `modules/cms-portfolio` conforms to the
module contract and passes CI before you add a single feature.

## Domain foundation

Core also owns tenant-scoped sites, channels, content identities, aliases,
ownership metadata, settings, and their public creation/change events. Optional
presentation adapters are provided by:

- liberusoftware/module-cms-core-api
- liberusoftware/module-cms-core-filament
- liberusoftware/module-cms-core-livewire
