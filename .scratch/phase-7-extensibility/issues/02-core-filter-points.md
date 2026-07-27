# 02 — Remaining 3 core filter points

**What to build:** With the `HookBus` proven (ticket 01 wired block render), ship the
other three curated filter points so the mechanism is demonstrated across every
layer — admin, delivery, data. Each is a concrete `@api` `Filter` class emitted from
its layer's real site.

**Blocked by:** 01 (HookBus + `BlockRenderFilter` tracer).

**Status:** DONE (branch `feature/cms-hooks`, not pushed)

- [x] **`AdminFormSchemaFilter`** (admin, cms-contracts) — mutable `list<TComponent>
      $components` + readonly `string $resource`. **Deliberately Filament-free** (the
      component type threads through a `@template TComponent of object` generic so
      cms-contracts keeps zero Filament imports; the emitting resource keeps full
      static typing). Applied in **Pages + Posts** Filament `form()` via
      `app(HookBusInterface::class)->apply(new AdminFormSchemaFilter($components,
      'pages'))->components`; documented as the pattern for the rest.
- [x] **`ApiResourceFilter`** (delivery, cms-contracts) — mutable `array<string,mixed>
      $data` + readonly `mixed $model` (callback discriminates via `instanceof`).
      Shared `Liberu\Cms\Core\Http\Concerns\FiltersApiResource` trait (`@mixin
      JsonResource`) wraps each `toArray()`; applied in **Page/Post/Menu/ContentEntry**
      Delivery resources.
- [x] **`ContentQueryFilter`** (data, cms-contracts) — readonly `string $key` +
      `Builder<TModel> $query` (`@template TModel`), `name()` returns the key. Shared
      `Liberu\Cms\Core\Database\Concerns\FiltersContentQueries` trait applies it just
      before execution in **Page + Post** repositories (all read paths).
- [x] **Tenant-safety guard:** the `FiltersContentQueries` trait re-asserts
      `where(qualifyColumn('team_id'), tenantId)` **after** the filter runs, so a
      callback that drops the tenant global scope still can't read other tenants'
      rows. Guarantee documented on both the filter class and the trait; proven by
      test.
- [x] Tests (9, `tests/Feature/Cms/{AdminFormSchemaHookTest,ApiResourceHookTest,
      ContentQueryHookTest}.php`): admin-form callback adds a top-level field (+ scoped
      to the target resource + no-hook clean); API callback reshapes payload (+ model-
      type scoping + no-hook clean); query callback narrows results, carries the query
      key, **and cannot leak cross-tenant rows even after `withoutGlobalScope`**.
- [x] No name/site drift from the plan table → `docs/specs/phase-7-extensibility.md`
      left as-is.
- [x] DoD: Pint clean · PHPStan **max** clean · arch-test green (each module still
      imports only contracts + core) · full suite **575 green on SQLite** (was 566).
