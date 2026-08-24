# CMS core modules

Core modules are presentation-neutral Laravel packages. The current core
boundaries are:

| Module | Package |
| --- | --- |
| Contracts | `liberusoftware/cms-contracts` |
| Module lifecycle and tenancy kernel | `liberusoftware/cms-core` |
| Content and revisions | `liberusoftware/cms-content` |
| Content types and fields | `liberusoftware/cms-content-types` |
| Pages | `liberusoftware/cms-pages` |
| Posts and taxonomy | `liberusoftware/cms-posts` |
| Media | `liberusoftware/cms-media` |
| Menus | `liberusoftware/cms-menus` |
| Forms | `liberusoftware/cms-forms` |
| Blocks | `liberusoftware/cms-blocks` |
| Themes | `liberusoftware/cms-themes` |
| Search | `liberusoftware/cms-search` |
| SEO | `liberusoftware/cms-seo` |
| Users and access | `liberusoftware/cms-users` |
| Notifications | `liberusoftware/cms-notifications` |
| Audit | `liberusoftware/cms-audit` |
| Observability | `liberusoftware/cms-observability` |

Core code must not depend on Filament, Livewire, frontend frameworks, or host
application `App\\` classes. See the Liberu architecture and core-module
standards for new module decisions.
