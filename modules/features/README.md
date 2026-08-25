# CMS feature scopes

Feature scopes are framework-neutral capabilities. Their authoritative product
specifications live in the Liberu documentation repository under
`projects/cms/features/`; this index records the scopes implemented in this
application and prevents API or UI code from becoming the domain owner.

Current implemented scopes are represented by the corresponding `cms-*`
modules: content and revisions, content types and fields, pages, posts and
taxonomy, media, blocks, forms, menus, themes, SEO, search, notifications,
users/access, audit, observability, and the delivery API.

New scopes must first receive a product feature specification and issue plan,
then be mapped one-to-one to core, API, Filament, and Livewire adapters before
code is added here.
