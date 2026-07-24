# Headless delivery API authenticates with Team-owned tokens and resolves tenancy from the token

The Phase 5 read-only delivery API (`cms-api`) authenticates with Sanctum tokens whose **tokenable is the `Team`** (the tenant), so a presented token both authenticates the request and identifies which tenant's published content to serve. Because the existing `TenantModelResolverInterface` only knew the Filament panel's tenant (`Filament::getTenant()`), an `cms-api` middleware sets a request-scoped **tenant context** and the host resolver now reads "API-set tenant first, else Filament tenant" — so the panel and the API share one source of truth and the existing tenancy global scope filters API queries unchanged.

## Considered Options

- **User-owned tokens (reuse `User` `HasApiTokens`)** — rejected: a user can belong to multiple teams, so the token can't unambiguously name a tenant, and a delivery token models an application, not a person.
- **Explicit `team_id` filtering in every API query** — rejected: it enforces tenancy in two different places (panel global scope + API manual filter), which drifts and risks a cross-tenant leak.

## Consequences

- The `Team` model gains `HasApiTokens`, and the host `FilamentTenantResolver` (plus a small tenant-context holder) is modified — both host-side, which is where tenancy is legitimately bound.
- Because the authenticated principal is a `Team`, the per-user `AccessControl` gate does not apply to the API; this is acceptable for v1, which serves only **published** content.
