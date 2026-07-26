# 01 — cms-api foundation + Pages endpoint (tracer bullet)

**What to build:** A consumer holding a Delivery token can call `GET /api/v1/pages` and `GET /api/v1/pages/{slug}` and receive their Team's **published** Pages as JSON — content sanitized, featured media embedded, scoped to the token's tenant. This ticket stands up the entire Delivery API skeleton once, proving the full path end to end, then rides Pages through it.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

Foundation (built here because the tracer bullet needs it):
- [ ] New `cms-api` package (`Liberu\Cms\Api`, path repo, sibling layout), depending only on `cms-contracts` + `cms-core`; added to `phpstan.neon`.
- [ ] `/api/v1` route group owned by `cms-api`, built in `boot()` from the resource registry (after all providers register).
- [ ] New `ApiResourceRegistryInterface` in `cms-contracts`, mirroring `AdminResourceRegistryInterface`; `cms-api` binds the implementation in `register()` (before content modules, so their `app()->bound(...)` guard passes).
- [ ] Sanctum **Team-owned tokens**: `HasApiTokens` added to the host `Team` model; `auth:sanctum` on the group resolves the Team as the authenticated principal.
- [ ] Request **tenant context** holder (bound in `cms-api`), set from the authenticated Team by the API auth middleware; host `TenantModelResolverInterface` implementation reads the API-set tenant first, else `Filament::getTenant()`. Existing tenancy global scope reused unchanged.
- [ ] `cms-api:issue-token {team}` artisan command mints a Delivery token for a Team and prints it once.
- [ ] CORS enabled for `api/v1/*`.

Pages vertical slice:
- [ ] `cms-pages` registers its API controller + Eloquent Resource into the registry during `registerModule()`, guarded by `app()->bound(ApiResourceRegistryInterface::class)`.
- [ ] `GET /api/v1/pages` returns a paginated collection of published Pages (`per_page` honored up to a config cap); `GET /api/v1/pages/{slug}` returns one published Page.
- [ ] `content` returned as HTML sanitized via the existing `HtmlSanitizer`; `excerpt` plain; internal columns (`team_id`, workflow internals) omitted; featured media embedded (URL + alt) via the media contract.

Behavior guarantees (feature tests, single HTTP seam):
- [ ] Unauthenticated request → `401`.
- [ ] Draft/review/archived Page → invisible (`404` on single, absent from collection).
- [ ] A Page belonging to another tenant → `404` (isolation; existence not leaked). Mirror `TenantIsolationTest`.
- [ ] Content sanitization proven through the endpoint (XSS payload stripped), mirroring `PageContentSanitizationTest`.
- [ ] Pagination metadata present; `per_page` capped.
- [ ] The API works with only `cms-pages` installed (headless-safe registration).
- [ ] Pint + PHPStan max + full Pest suite green.
