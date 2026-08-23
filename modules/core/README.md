# CMS core modules

Core modules are presentation-neutral Laravel packages. The current core
boundaries are:

| Module | Package |
| --- | --- |
| Contracts | `liberu-cms/cms-contracts` |
| Module lifecycle and tenancy kernel | `liberu-cms/cms-core` |
| Content and revisions | `liberu-cms/cms-content` |
| Content types and fields | `liberu-cms/cms-content-types` |
| Pages | `liberu-cms/cms-pages` |
| Posts and taxonomy | `liberu-cms/cms-posts` |
| Media | `liberu-cms/cms-media` |
| Menus | `liberu-cms/cms-menus` |
| Forms | `liberu-cms/cms-forms` |
| Blocks | `liberu-cms/cms-blocks` |
| Themes | `liberu-cms/cms-themes` |
| Search | `liberu-cms/cms-search` |
| SEO | `liberu-cms/cms-seo` |
| Users and access | `liberu-cms/cms-users` |
| Notifications | `liberu-cms/cms-notifications` |
| Audit | `liberu-cms/cms-audit` |
| Observability | `liberu-cms/cms-observability` |

Core code must not depend on Filament, Livewire, frontend frameworks, or host
application `App\\` classes. See the Liberu architecture and core-module
standards for new module decisions.
