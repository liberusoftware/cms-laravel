# CMS Filament modules

The current Filament surfaces are provided by the owning CMS modules and the
administration module `liberusoftware/module-cms-admin`. They cover content types,
content, pages, posts, media, menus, forms, notifications, audit logs, API
tokens, themes, widgets, and module management.

Filament resources, pages, widgets, schemas, and actions remain adapters. They
must delegate authorization and domain transitions to the matching core
module, respect tenant/team scope, and expose safe validation and failure
states.
