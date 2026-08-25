# Liberu CMS modules

CMS packages are Composer modules and live directly beneath this directory.
The application discovers them through the path repository configured in the
root `composer.json`; `packages/` is not an application source directory.

The implementation boundaries follow the Liberu documentation indexes:

- [Feature scopes](features/README.md)
- [Core modules](core/README.md)
- [API modules](api/README.md)
- [Filament modules](filament/README.md)
- [Livewire modules](livewire/README.md)

Each module owns its public contracts, configuration, persistence, service
provider, tests, and documentation. Framework-specific adapters must depend on
the matching core module through public contracts.
