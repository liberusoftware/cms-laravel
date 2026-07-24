---
title: "Phase 5 · Increment 1 — cms-api (read-only headless Delivery API)"
triage: ready-for-agent
status: spec
---

# Phase 5 · Increment 1 — `cms-api` read-only Delivery API

> Publish target: GitHub issue on `ducz07/cms-laravel` with label `ready-for-agent` once `gh` is available. Captured here in the interim.

## Problem Statement

The CMS today can only surface its content through the Filament admin panel and the server-rendered web templates. A team that wants to build a decoupled frontend — a separate SPA, a mobile app, a static-site generator, another service — has no supported way to read published content out of the system. The project's founding rule is "API-first; runs headless," but there is no HTTP API to deliver content to an external consumer, and no way for such a consumer to authenticate or to be scoped to a single tenant's content.

## Solution

Introduce a **Delivery API**: a versioned (`/api/v1`), read-only JSON HTTP surface that serves *published* content — Pages, Posts (with taxonomy), Menus, and Content-Entries — to decoupled frontends. A consumer authenticates with a **Delivery token** (a Sanctum token owned by a Team); presenting the token both authenticates the request and identifies which tenant's content to serve. Content is returned as sanitized HTML, paginated, and scoped to the token's tenant and to the `Published` workflow state.

The API is delivered as a new infrastructure package, `cms-api`, that owns versioning, authentication, the tenant context, rate limiting, and error handling. Each content module registers its own endpoints and Eloquent Resources into the API through a registry contract, so the API package never imports a content module — the same shape as the Phase 4 `AdminResourceRegistry`.

See `docs/adr/0001-delivery-api-auth-and-tenancy.md` for the auth/tenancy decision, and `packages/liberu-cms/cms-api/CONTEXT.md` for the glossary.

## User Stories

1. As a frontend developer, I want to fetch a list of published Pages as JSON, so that I can render them in a decoupled frontend.
2. As a frontend developer, I want to fetch a single published Page by its slug, so that I can build slug-based routes in my app.
3. As a frontend developer, I want to fetch a paginated list of published Posts newest-first, so that I can render a blog index without loading everything at once.
4. As a frontend developer, I want to fetch a single published Post by its slug, so that I can render an article page.
5. As a frontend developer, I want to filter Posts by category slug, so that I can render a category archive.
6. As a frontend developer, I want to filter Posts by tag slug, so that I can render a tag archive.
7. As a frontend developer, I want each Post/Page to include its featured media URL and alt text, so that I can render images without a second request.
8. As a frontend developer, I want a Post to include its categories and tags inline, so that I can render taxonomy links without extra requests.
9. As a frontend developer, I want to fetch a Menu by its location, so that I can render site navigation.
10. As a frontend developer, I want the Menu response to include its ordered item tree (labels, URLs, nesting), so that I can render multi-level navigation.
11. As a frontend developer, I want to fetch published Content-Entries of a given type, so that I can render custom content that the CMS defines dynamically.
12. As a frontend developer, I want to fetch a single Content-Entry by slug, so that I can render a custom content detail page.
13. As a frontend developer, I want a Content-Entry to expose its typed fields as structured JSON, so that I can consume custom schemas without knowing them at build time.
14. As a frontend developer, I want `content` returned as sanitized HTML, so that I can inject it into the DOM without introducing an XSS vulnerability.
15. As a frontend developer, I want consistent pagination metadata on every collection, so that I can build "next page" controls generically.
16. As a frontend developer, I want to control page size with a `per_page` parameter up to a capped maximum, so that I can tune payload size without being able to abuse the API.
17. As an API consumer, I want to authenticate with a bearer token, so that I can access the tenant's content programmatically.
18. As an API consumer, I want an unauthenticated request to be rejected with `401`, so that access is gated.
19. As an API consumer, I want requests scoped automatically to my tenant, so that I only ever see my own Team's content.
20. As an API consumer, I want a request for another tenant's resource (or an unpublished/non-existent one) to return `404`, so that resource existence across tenants is not leaked.
21. As an API consumer, I want draft/review/archived content to be invisible, so that only published content is ever delivered.
22. As an API consumer, I want to be rate-limited per token with a `429` and `Retry-After`, so that I understand and can back off from limits.
23. As a platform operator, I want to mint a Delivery token for a specific Team from the command line, so that I can onboard a consumer without building UI first.
24. As a platform operator, I want to revoke a Delivery token, so that I can cut off a compromised or retired consumer.
25. As a platform operator, I want the API to work when only some content modules are installed, so that a headless deployment can ship a subset of modules.
26. As a platform operator, I want a README describing routes, auth, and response shapes, so that I can hand consumers a reference without a formal spec yet.
27. As a security reviewer, I want tenant isolation enforced by the same mechanism the admin panel uses, so that there is one source of truth and no second place for a leak to hide.
28. As a maintainer, I want each content module to own its own API Resource, so that changing a model's wire shape is a local change in that module.
29. As a maintainer, I want the API package to depend only on contracts, so that it never imports a content module and the golden rules hold.
30. As a frontend developer, I want CORS enabled for the API paths, so that a browser app on another origin can call it.

## Implementation Decisions

### Packages

- **New package `cms-api`** (`Liberu\Cms\Api`, path repository, same layout as siblings). Owns: the `/api/v1` route group, the Sanctum auth middleware, the tenant-context mechanism, per-token rate limiting, JSON error handling, CORS enablement for API paths, and the `cms-api:issue-token` command. Depends only on `cms-contracts` and `cms-core`.
- **Modified content modules** (`cms-pages`, `cms-posts`, `cms-menus`, `cms-content-types`): each registers its API controller + Eloquent Resource into the registry during `registerModule()`, guarded by `app()->bound(ApiResourceRegistryInterface::class)` (headless-safe), exactly as they register Filament resources today.
- **Modified `cms-media` usage**: no standalone endpoint; a featured-media representation (URL + alt) is embedded in Page/Post Resources via the existing media contract.
- **Host changes** (permitted — tenancy is bound in the host): add `HasApiTokens` to the `Team` model; make the host `TenantModelResolverInterface` implementation read the API-set tenant context first, falling back to `Filament::getTenant()`.

### Contracts / interfaces

- **New `ApiResourceRegistryInterface`** in `cms-contracts`, mirroring `AdminResourceRegistryInterface`: a module announces the API endpoint(s) it owns, tagged by module key; `cms-api` reads the catalogue to build routes. Shape follows the admin registry (register + list, grouped by module key).
- **Tenant context**: a request-scoped holder (bound in `cms-api`) that names the current tenant during an API request. The API auth middleware sets it from the authenticated Team. The host tenancy resolver reads it before Filament. This keeps the existing tenancy global scope working unchanged for API queries.
- **Reuse existing repository contracts** for all reads: `PageRepositoryInterface` (`findBySlug`, `published`, `roots`), `PostRepositoryInterface` (`findBySlug`, `published`, `byCategory`, `byTag`), `ContentEntryRepositoryInterface` (`findBySlug`, `ofType`, `published`), `MenuRepositoryInterface` (`forLocation`). No new query logic; single-item lookups apply a published check at the controller/Resource boundary where `findBySlug` currently ignores status.

### Auth & tenancy (see ADR-0001)

- Sanctum tokens whose **tokenable is `Team`**. `auth:sanctum` returns the Team; the Team *is* the tenant.
- The Delivery token carries a read ability/scope; v1 only reads, so a single read ability suffices.
- Cross-tenant / unpublished / missing resources are indistinguishable from the consumer's side — all `404` — so existence is not leaked across tenants.

### API contract

- **Versioning**: URI path `/api/v1`.
- **Routes** (indicative, not binding on exact strings): collection + single per resource; Posts collection accepts `category` / `tag` / `per_page`; Menus fetched by location.
- **Serialization**: one Eloquent API Resource per exposed model, owned by its module. Internal columns (`team_id`, workflow internals, timestamps not needed by consumers) are omitted.
- **`content` field**: returned as HTML sanitized through the existing `Liberu\Cms\Content\Support\HtmlSanitizer`; `excerpt` as plain text.
- **Pagination**: page-based, standard `JsonResource` `data`/`meta`/`links`; `per_page` honored up to a hard cap from config.
- **Rate limiting**: per-token throttle on the `/api/v1` group, default from `config('cms-api.rate_limit')`, `429` + `Retry-After`; per-IP fallback for unauthenticated paths.
- **Errors**: Laravel default JSON exception rendering — `401` (missing/invalid token), `404` (unknown slug / unpublished / cross-tenant), `429` (throttle).
- **CORS**: enabled for `api/v1/*`.

### Operations

- **`cms-api:issue-token {team}`** artisan command mints a Delivery token for a Team and prints it once. Revocation via Sanctum's token deletion (documented in the README). Admin UI for token management is out of scope.

### Registration/ordering

- `cms-api` binds `ApiResourceRegistryInterface` in its `register()`; it must register before the content modules so their `app()->bound(...)` guard passes — the same ordering constraint proved by `cms-admin`. `cms-api` reads the populated registry and defines routes in `boot()` (after all providers have registered), carrying over the Phase 4 provider-timing lesson.

## Testing Decisions

- **What a good test is here**: asserts *external behavior* of the Delivery API — the HTTP contract — not internal wiring. It issues a real Delivery token, calls a real `/api/v1` route, and asserts status code and JSON shape/visibility. It does not assert controller internals, registry internals, or query builder calls.
- **Single, highest seam**: Pest **feature tests** hitting `/api/v1/*` end to end (route → auth + tenant-context middleware → controller → repository → Resource → JSON).
- **Modules tested**: `cms-api` (auth, tenant context, versioning, rate limiting, error shapes) and each content endpoint (Pages, Posts + taxonomy, Menus, Content-Entries) via feature tests. Host `Team` token issuance covered through the `cms-api:issue-token` command + an authenticated request.
- **Coverage to include**: published content is returned; draft/review/archived is invisible (`404`/absent); unauthenticated request is `401`; cross-tenant resource is `404` (isolation); `content` is returned sanitized (XSS payload stripped through the endpoint, mirroring `PageContentSanitizationTest`); pagination metadata present and `per_page` capped; taxonomy filters return the right subset; rate limit returns `429`.
- **Prior art**: existing module resource tests that register the tenancy global scope + creation observer in `beforeEach`; `TenantIsolationTest`; `PageContentSanitizationTest` (payload stripped through the public controller) as the model for the API sanitization test; model factories with existing states (e.g. Page factory `home()` / published states).

## Out of Scope

- Write/CRUD operations (create/update/delete) — a later Phase-5 increment.
- Free-text **Search** — its own Phase-5 increment; the taxonomy filter here is not search.
- **SEO** (sitemaps, structured data), **Forms**, **Notifications** — later Phase-5 increments.
- Preview/draft access via token scope — future; v1 serves only published content.
- Standalone browsable Media endpoint — media is embedded only.
- Generated **OpenAPI** spec and an admin **token-management UI** — deferred follow-ups (README ships in v1).
- Generic query grammar: `?include`, `?fields`, arbitrary `?filter`, sorting, GraphQL — deliberately excluded.
- ContentType definitions as an endpoint (schemas are global/shared); only Content-*Entries* are delivered.

## Further Notes

- **Tracer bullet**: build **Pages first**. It exercises the entire stack once — the registry contract, the `/api/v1` route group, Sanctum Team-token auth, the tenant context + resolver change, the Eloquent Resource, and HTML sanitization. Once Pages is green end to end, Posts (+taxonomy), Menus, and Content-Entries are the same pattern repeated and can be separate tickets.
- **Toolchain**: Windows/Herd — run PHP/Composer via PowerShell; `composer` operations need `--ignore-platform-reqs` (see the `windows-herd-toolchain` memory).
- **Gates** (per existing CI): Pint, PHPStan max (+Larastan), and the full Pest suite must stay green; add `cms-api` to `phpstan.neon`.
- **Menus caveat**: Menus have no workflow state, so "published" doesn't apply; `forLocation` is the visibility seam, still tenant-scoped.
