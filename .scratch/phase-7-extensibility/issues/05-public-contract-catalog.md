# 05 — Public contract catalog + stability policy

**What to build:** The promise that makes the "public API" real. Designate which
`cms-contracts` interfaces third parties may depend on, publish the catalog + a
written semver/deprecation policy, and guard the boundary with an arch-test so an
accidental breaking change can't ship green. Lands late so the catalog covers the
new `HookBus`/`Filter`/`FieldTypeRegistry` contracts too. No new dependency (an
automated BC-checker was considered and rejected for 0.x churn + approval cost).

**Blocked by:** 01 + 03 (public contracts must exist) — realistically after 02 so the
filter-point classes are catalogued.

**Status:** ready-for-agent

- [ ] **Designate** the public surface with an `@api` docblock convention on every
      extension-facing interface/VO (registries: Admin/Api/Search/Sitemap/Preview/
      Dashboard/Permission; `Block`/`Widget`/`Theme`; `HookBus`/`Filter` + the 4
      filter points; `FieldTypeRegistry`; `EventBus`/`CmsEvent`). Mark internal kernel
      wiring (`ModuleStateRepositoryInterface`, `TenantContextInterface`, etc.)
      `@internal`.
- [ ] **`docs/EXTENSION-API.md`** — the catalog: every public contract, one line on
      what an extension does with it, and a link. Grouped by surface (register a
      resource / endpoint / block / widget; listen to events; add a hook / filter
      point; add a field type). Note the "consume vs. define" distinction for hooks.
- [ ] **Semver + deprecation policy** (in the same doc or `docs/STABILITY.md`): what
      `@api` guarantees, how a public contract may change, the deprecation window,
      what `@internal` means (may change without notice), and the "extensions must not
      import `App\` or another module's concrete classes" rule (already arch-enforced).
- [ ] **Arch-test guard** (`tests/Feature/Cms/PublicApiTest.php`): assert the `@api`
      interfaces exist under the expected namespaces, and that no `@internal` contract
      leaks into an `@api` signature (parameter/return types). Keep it maintainable —
      a reflection sweep over the contracts namespace.
- [ ] Cross-link from `CONTEXT-MAP.md` and update `docs/STACK.md` deviations if the
      public-API policy changes any stated boundary.
- [ ] DoD: Pint clean · PHPStan **max** clean · arch-test green · full suite green on
      SQLite.
