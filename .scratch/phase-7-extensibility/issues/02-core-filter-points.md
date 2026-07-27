# 02 — Remaining 3 core filter points

**What to build:** With the `HookBus` proven (ticket 01 wired block render), ship the
other three curated filter points so the mechanism is demonstrated across every
layer — admin, delivery, data. Each is a concrete `@api` `Filter` class emitted from
its layer's real site.

**Blocked by:** 01 (HookBus + `BlockRenderFilter` tracer).

**Status:** ready-for-agent

- [ ] **`AdminFormSchemaFilter`** (admin) — carries the resource identity + the
      mutable Filament form component list. Each content-module Filament resource
      applies it inside `form()` so an extension can add/modify components on another
      module's edit form (the canonical WordPress "add fields to the Page form" case).
      Applied in at least Pages + Posts resources; documented as the pattern for the rest.
- [ ] **`ApiResourceFilter`** (delivery) — carries the model + the mutable resource
      array. `cms-api` Eloquent Resources apply it in `toArray()` before returning so
      an extension can reshape a response. Applied across the shared resource path so
      all v1 resources honour it.
- [ ] **`ContentQueryFilter`** (data) — carries a query identity/key + the Eloquent
      builder. Content repositories apply it **after** global scopes are attached,
      just before execution, so a callback can narrow/annotate a query.
- [ ] **Tenant-safety guard (ContentQueryFilter):** a test proves a registered
      callback **cannot** re-introduce cross-tenant rows (e.g. calling
      `withoutGlobalScope(TenantScope::class)` inside a filter still yields only the
      current tenant's rows, or is otherwise neutralised). Document the guarantee on
      the filter class.
- [ ] Tests (`tests/Feature/Cms/*HookTest.php`): admin-form callback adds a field
      visible on the resource form; API callback changes a serialized payload; query
      callback filters results; the tenant-safety test above.
- [ ] Update the `docs/specs/phase-7-extensibility.md` filter-point table if any
      emitting site/name changed during implementation.
- [ ] DoD: Pint clean · PHPStan **max** clean · arch-test green · full suite green on
      SQLite. Each affected module still imports only contracts + core (+ framework).
