# 03 — Menus endpoint

**What to build:** A headless frontend can fetch a Menu by its location and render site navigation — `GET /api/v1/menus/{location}` returns the Menu with its ordered, nested item tree (labels, URLs, nesting), scoped to the token's tenant.

**Blocked by:** 01 — foundation + Pages (needs the `/api/v1` group, auth, tenant context, and the resource registry).

**Status:** ready-for-agent

- [ ] `cms-menus` registers its API controller + Eloquent Resource into the registry (same guarded pattern).
- [ ] `GET /api/v1/menus/{location}` returns the Menu for that location (reusing `MenuRepositoryInterface::forLocation`), tenant-scoped; unknown location → `404`.
- [ ] The Menu Resource emits the ordered item tree — nested items with labels and URLs preserving order and hierarchy.
- [ ] Menus have no workflow state, so no published filter applies; visibility is location + tenant only.
- [ ] Feature tests: correct menu + ordered nested tree returned; cross-tenant menu → `404` (isolation); unauth `401`.
- [ ] Pint + PHPStan max + full Pest suite green.
