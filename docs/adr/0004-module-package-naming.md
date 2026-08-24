# ADR 0004: Independent module package naming

## Status

Accepted for the `development` refactor.

## Decision

Every independently released CMS package uses the `module-cms-` prefix in its
Composer name and GitHub repository name:

| Boundary | Naming pattern |
| --- | --- |
| Core | `liberusoftware/module-cms-{module}` |
| API adapter | `liberusoftware/module-cms-{module}-api` |
| Filament adapter | `liberusoftware/module-cms-{module}-filament` |
| Livewire adapter | `liberusoftware/module-cms-{module}-livewire` |
| Host application | `liberusoftware/cms-laravel` |
| Theme | `liberusoftware/theme-{theme}` |

The current repository directories remain under `modules/cms-*` so source
layout and documentation paths stay stable. Their Composer package identities,
root requirements, installer metadata, lockfile entries, and future GitHub
repositories use the independent `module-cms-*` identity. The host application
is not itself an independently released module and therefore keeps its existing
name.

Presentation adapters depend on the matching core package and never move domain
ownership into API, Filament, or Livewire code. A package is not submitted to
Packagist until its independent repository, release tag, test evidence, README,
changelog, and install verification exist.

## Consequences

- Existing local path packages are renamed consistently before repository
  creation, preventing Composer installer target collisions.
- Downstream consumers must update old `liberusoftware/cms-*` package names to
  their `liberusoftware/module-cms-*` equivalents at the first published major
  release.
- Documentation and examples must use the canonical package identity when they
  describe independent installation, while source paths may continue to use the
  repository's `modules/cms-*` layout.
