# Context Map

The CMS is a multi-context system; each context is a Composer package under `packages/liberu-cms/cms-*`. This map lists the contexts that have a documented glossary. Contexts gain a `CONTEXT.md` lazily, as their language is resolved during design — absence here means "not yet documented," not "no such context."

## Contexts

- [CMS API](./packages/liberu-cms/cms-api/CONTEXT.md) — headless HTTP delivery layer serving published content to decoupled frontends

## Relationships

- **CMS API → content modules** (cms-pages, cms-posts, cms-menus, cms-content-types): each content module registers its API endpoints and Eloquent Resources into the Delivery API via `ApiResourceRegistryInterface`; the API package never imports a module.
- **CMS API → host tenancy**: the API resolves the tenant from the Delivery token into a request tenant context that the host's `TenantModelResolverInterface` implementation reads. See `docs/adr/0001-delivery-api-auth-and-tenancy.md`.
