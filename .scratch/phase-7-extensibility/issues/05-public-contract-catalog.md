# 05 — Public contract catalog + stability policy

**What to build:** The promise that makes the "public API" real. Designate which
`cms-contracts` interfaces third parties may depend on, publish the catalog + a
written semver/deprecation policy, and guard the boundary with an arch-test so an
accidental breaking change can't ship green. Lands late so the catalog covers the
new `HookBus`/`Filter`/`FieldTypeRegistry` contracts too. No new dependency (an
automated BC-checker was considered and rejected for 0.x churn + approval cost).

**Blocked by:** 01 + 03 (public contracts must exist) — realistically after 02 so the
filter-point classes are catalogued.

**Status:** DONE (branch `feature/cms-hooks`, not pushed)

- [x] **Designated all 53 cms-contracts types** `@api` or `@internal` (9 from tickets
      01–04 already tagged; 44 tagged now via a scripted docblock sweep). **49 `@api`**
      (registries Admin/Api/Search/Sitemap/Preview/Dashboard/Permission; Block/Widget/
      Theme; Hooks + 4 filter points; FieldType; Events/CmsEvent + the 5 event classes;
      Module{Interface,ManagerInterface,DependencyExceptionInterface}; Access/Content/
      Media/Workflow contracts) + **4 `@internal`** (`ModuleRegistryInterface`,
      `ModuleStateRepositoryInterface`, `TenantContextInterface`,
      `TenantModelResolverInterface` — kernel/tenancy wiring).
- [x] **`docs/EXTENSION-API.md`** — the catalog: every public contract with a one-line
      "what an extension does with it", grouped by surface (module / hooks / events /
      admin / delivery API / field types / blocks / widgets / themes / search / SEO /
      preview / permissions / workflow / media). Hooks section calls out the
      **consume-vs-define** distinction explicitly.
- [x] **Semver + deprecation policy** (top of `docs/EXTENSION-API.md`): `@api` = a
      no-break-within-major promise; additive change allowed in minors; `@deprecated`
      for ≥1 minor before removal; `@internal` = no promise; the no-`App\`/
      no-cross-module-concrete-imports rule (arch-enforced).
- [x] **Arch-test guard** `tests/Feature/Cms/PublicApiTest.php` (reflection sweep over
      the contracts namespace): (1) **every** contract is designated `@api` XOR
      `@internal` — a new untagged contract fails the build; (2) no `@internal` type
      leaks through an `@api` method signature (params + return, incl. union/
      intersection); (3) a dataset asserts the 16 core public FQCNs exist + are `@api`.
      **Windows gotcha:** derive the FQCN from Finder's `getRelativePathname()` (not
      `getRealPath()` minus `base_path()`) — the two disagree on `\` vs `/`.
- [x] Cross-linked `docs/EXTENSION-API.md` from `CONTEXT-MAP.md`. `docs/STACK.md`
      unchanged (the policy formalizes the existing contract layer; no boundary moved).
- [x] DoD: Pint clean · PHPStan **max** clean · arch-test green · full suite **603
      green on SQLite** (was 585).
