# Liberu CMS package inventory

The application owns 22 independently releasable Composer packages. Each
package uses the `liberusoftware/` vendor namespace, has a public GitHub
repository, a protected `main` branch with a blocking `package-quality`
workflow, and a tested `v0.1.0` source tag and GitHub Release. The application
uses local path repositories during development; production installation must
use the corresponding public Composer/Packagist source after submission.

| Package | Repository | Release |
| --- | --- | --- |
| `liberusoftware/module-cms-admin` | [module-cms-admin](https://github.com/liberusoftware/module-cms-admin) | `v0.1.0` |
| `liberusoftware/module-cms-api` | [module-cms-api](https://github.com/liberusoftware/module-cms-api) | `v0.1.0` |
| `liberusoftware/module-cms-audit` | [module-cms-audit](https://github.com/liberusoftware/module-cms-audit) | `v0.1.0` |
| `liberusoftware/module-cms-blocks` | [module-cms-blocks](https://github.com/liberusoftware/module-cms-blocks) | `v0.1.0` |
| `liberusoftware/module-cms-content` | [module-cms-content](https://github.com/liberusoftware/module-cms-content) | `v0.1.0` |
| `liberusoftware/module-cms-content-types` | [module-cms-content-types](https://github.com/liberusoftware/module-cms-content-types) | `v0.1.0` |
| `liberusoftware/module-cms-contracts` | [module-cms-contracts](https://github.com/liberusoftware/module-cms-contracts) | `v0.1.0` |
| `liberusoftware/module-cms-core` | [module-cms-core](https://github.com/liberusoftware/module-cms-core) | `v0.1.0` |
| `liberusoftware/module-cms-forms` | [module-cms-forms](https://github.com/liberusoftware/module-cms-forms) | `v0.1.0` |
| `liberusoftware/module-cms-hello` | [module-cms-hello](https://github.com/liberusoftware/module-cms-hello) | `v0.1.0` |
| `liberusoftware/module-cms-hello-livewire` | [module-cms-hello-livewire](https://github.com/liberusoftware/module-cms-hello-livewire) | `v0.1.0` |
| `liberusoftware/module-cms-media` | [module-cms-media](https://github.com/liberusoftware/module-cms-media) | `v0.1.0` |
| `liberusoftware/module-cms-menus` | [module-cms-menus](https://github.com/liberusoftware/module-cms-menus) | `v0.1.0` |
| `liberusoftware/module-cms-notifications` | [module-cms-notifications](https://github.com/liberusoftware/module-cms-notifications) | `v0.1.0` |
| `liberusoftware/module-cms-observability` | [module-cms-observability](https://github.com/liberusoftware/module-cms-observability) | `v0.1.0` |
| `liberusoftware/module-cms-pages` | [module-cms-pages](https://github.com/liberusoftware/module-cms-pages) | `v0.1.0` |
| `liberusoftware/module-cms-posts` | [module-cms-posts](https://github.com/liberusoftware/module-cms-posts) | `v0.1.0` |
| `liberusoftware/module-cms-search` | [module-cms-search](https://github.com/liberusoftware/module-cms-search) | `v0.1.0` |
| `liberusoftware/module-cms-seo` | [module-cms-seo](https://github.com/liberusoftware/module-cms-seo) | `v0.1.0` |
| `liberusoftware/module-cms-themes` | [module-cms-themes](https://github.com/liberusoftware/module-cms-themes) | `v0.1.0` |
| `liberusoftware/module-cms-users` | [module-cms-users](https://github.com/liberusoftware/module-cms-users) | `v0.1.0` |
| `liberusoftware/module-cms-widgets` | [module-cms-widgets](https://github.com/liberusoftware/module-cms-widgets) | `v0.1.0` |

## Publication gate

Before release promotion, submit each repository through Packagist, verify
the indexed version/source/dist/dependencies, and run a clean `composer
require` against Packagist. The current environment can verify the tagged
source through GitHub VCS repositories, but cannot complete that publication
gate without Packagist submission authority.

With approved environment secrets, submit the inventory using
[`scripts/publish-packages.sh`](../scripts/publish-packages.sh), then verify
each package through Packagist before promoting the application release.
