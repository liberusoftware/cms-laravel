# 03 — `FieldTypeRegistry` (open the closed field-type seam)

**What to build:** Turn the closed `FieldType` enum into a registry so third parties
can add custom content-type field kinds (`color`, `relation`, `geo`, …) without
editing core. Today `Liberu\Cms\ContentTypes\Fields\FieldType` is an `enum` and its
Filament-component + validation-rule mappings are hardcoded — a hard "must edit core"
wall. This is the one closed extension point worth opening in Phase 7.

**Blocked by:** None (independent of the hook chain). Stacked after ticket 02 on the
branch; could be developed in parallel.

**Status:** ready-for-agent

- [ ] `Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface` (`@api`): register a
      custom field kind `key → { Filament component factory, validation-rule
      fragment, cast/normaliser }`; look up + list. A small `FieldTypeDefinition` VO
      carries the parts (mirrors `SearchResult`/`ApiEndpoint` style).
- [ ] Impl `FieldTypeRegistry` in `cms-content-types`, bound singleton in its
      provider; modules register kinds in `registerModule()` guarded by `bound()`.
- [ ] **Built-ins re-seed the registry** — the existing enum cases (text, textarea,
      richtext, number, boolean, date, select, media) register through the same path,
      so there is **no behaviour change**; keep the enum as the built-in key source
      or retire it in favour of registry keys (implementer's call — no external
      behaviour may change, and `FieldType::options()` callers keep working).
- [ ] `SchemaValidator` builds validation rules from the registry (not the hardcoded
      enum match); unknown/unregistered kind → the existing validation-error path.
- [ ] `ContentEntryResource` dynamic form builds each field's Filament component from
      the registry (not the hardcoded `match`).
- [ ] Tests: a **custom** field kind registered in a test provider appears in the
      content-type schema editor options, validates per its rule fragment, and
      renders in the ContentEntry form; all built-in kinds still behave exactly as
      before (regression over the existing content-types tests).
- [ ] DoD: Pint clean · PHPStan **max** clean · arch-test green · full suite green on
      SQLite. `cms-content-types` imports only contracts + core (+ filament).
