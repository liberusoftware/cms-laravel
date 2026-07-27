# 06 — Reference extension (`cms-hello`) + "Build your first extension" guide

**What to build:** The capstone and proof. Grow the existing `cms-hello` PoC into the
canonical reference extension that exercises the *whole* public surface, and write
the developer guide that teaches it. In-repo so it is CI-tested living documentation;
the guide documents the genuinely-external steps so nothing about being in-repo hides
a real third-party requirement.

**Blocked by:** all of 01–05 (it consumes every seam and the catalogued public API).

**Status:** ready-for-agent

- [ ] Grow `cms-hello` to demonstrate, minimally but really:
  - [ ] registers a **Filament admin resource** (via `AdminResourceRegistry`),
  - [ ] registers an **API endpoint** (via `ApiResourceRegistry`),
  - [ ] registers a **block type** (via the block registry),
  - [ ] **consumes** a core filter point (e.g. an `AdminFormSchemaFilter` or
        `BlockRenderFilter` callback that visibly changes output),
  - [ ] **defines its own** filter point (a `Filter` subclass) and applies it, so the
        "extensions are themselves extensible" story is demonstrated,
  - [ ] **adds a custom `FieldType`** via `FieldTypeRegistry`.
  - [ ] Stays removable + self-gating; imports only contracts + core (+ the registries
        it targets); arch-test green.
- [ ] Feature tests over `cms-hello` proving each of the above works through the real
      wiring (resource visible, endpoint responds, block renders, filter fires, custom
      field validates/renders).
- [ ] **`docs/BUILD-YOUR-FIRST-EXTENSION.md`** — a tutorial that: runs
      `cms:make-module`, adds a block with `cms:make-block`, a hook with
      `cms:make-hook`, a field type with `cms:make-field-type`; explains consuming vs.
      defining a filter point; and documents the **external-package path** (composer
      `path`/`vcs` repository entry, PSR-4 autoload, the no-`App\`/no-cross-module-
      concrete-imports rule enforced by arch-test, `composer update` + `migrate`).
      Cross-links `docs/EXTENSION-API.md`.
- [ ] Update `docs/specs/phase-7-extensibility.md` status → done, and the
      `.scratch/phase-7-extensibility/README.md` table.
- [ ] DoD: Pint clean · PHPStan **max** clean · arch-test green · full suite green on
      SQLite. Phase 7 complete.
