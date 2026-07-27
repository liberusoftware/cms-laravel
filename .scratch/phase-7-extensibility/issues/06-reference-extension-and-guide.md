# 06 — Reference extension (`cms-hello`) + "Build your first extension" guide

**What to build:** The capstone and proof. Grow the existing `cms-hello` PoC into the
canonical reference extension that exercises the *whole* public surface, and write
the developer guide that teaches it. In-repo so it is CI-tested living documentation;
the guide documents the genuinely-external steps so nothing about being in-repo hides
a real third-party requirement.

**Blocked by:** all of 01–05 (it consumes every seam and the catalogued public API).

**Status:** DONE (branch `feature/cms-hooks`, not pushed) — **PHASE 7 COMPLETE**

- [x] Grew `cms-hello` to exercise the whole surface, each through a contract +
      `bound()`-guarded:
  - [x] **Filament admin resource** `GreetingResource` (+ `ListGreetings`) via
        `AdminResourceRegistry`, gated by a module-owned `hello` permission group.
  - [x] **API endpoint** `GET /api/v1/greetings` (`GreetingApiController`) via
        `ApiResourceRegistry`.
  - [x] **block type** `GreetingBlock` via `BlockTypeRegistryInterface`.
  - [x] **consumes** a core filter point: a `BlockRenderFilter` listener that tags
        hello's own block output (`<!-- greeted by cms-hello -->`).
  - [x] **defines its own** filter point `GreetingFilter` and applies it inside
        `GreetingBlock::render` — another extension can transform the greeting.
  - [x] **custom field kind** `greeting` via `GreetingFieldType` → `FieldTypeRegistry`.
  - [x] Removable + embeddable (all registrations `bound()`-guarded; arch + Embeddability
        + Removability tests green). Added `filament/filament ^5` to cms-hello composer.
- [x] Feature tests `tests/Feature/Cms/HelloReferenceExtensionTest.php` (6) prove each
      seam through real wiring (block renders + core-marker; own-filter transform;
      field kind registered; API endpoint responds under a Sanctum token; resource
      registered; resource gated by `hello.view`).
- [x] **`docs/BUILD-YOUR-FIRST-EXTENSION.md`** — tutorial using all 3 generators +
      consume-vs-define hooks + the external-package path (composer path/vcs repo,
      PSR-4, the two hard rules: no `App\`/no-cross-module imports [arch-enforced],
      `@api`-only). Cross-links `docs/EXTENSION-API.md`.
- [x] Updated `docs/specs/phase-7-extensibility.md` (→ COMPLETE) + this README table.
- [x] **Fixed a test collision:** ticket-04's `MakeArtifactCommandTest` generated a
      `Greeting`-named hook into cms-hello and `deleteDirectory`'d src/Blocks|Hooks|
      Fields in afterEach — which now clashed with the reference extension's real files.
      Reworked it to use `Demo*` probe names + delete only the specific generated files.
- [x] DoD: Pint clean · PHPStan **max** clean · arch-test green · full suite **609
      green on SQLite** (was 603). **PHASE 7 COMPLETE.**
