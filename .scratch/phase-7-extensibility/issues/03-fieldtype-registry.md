# 03 — `FieldTypeRegistry` (open the closed field-type seam)

**What to build:** Turn the closed `FieldType` enum into a registry so third parties
can add custom content-type field kinds (`color`, `relation`, `geo`, …) without
editing core. Today `Liberu\Cms\ContentTypes\Fields\FieldType` is an `enum` and its
Filament-component + validation-rule mappings are hardcoded — a hard "must edit core"
wall. This is the one closed extension point worth opening in Phase 7.

**Blocked by:** None (independent of the hook chain). Stacked after ticket 02 on the
branch; could be developed in parallel.

**Status:** DONE (branch `feature/cms-hooks`, not pushed)

- [x] `Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface` (`@api`): register /
      get / has / all / `options()`. `FieldTypeDefinition` VO (`@api`, mirrors the
      registry VOs) carries `key`, `label`, a **Filament component factory**
      (`Closure(string $statePath, list<string> $options): TComponent`) and a
      **`matches` predicate** (`Closure(mixed): bool`) for validation. **Filament-free**
      — the component type threads through a `@template-covariant TComponent of object`
      generic (covariant so `FieldTypeDefinition<TextInput>` is a `<object>`).
- [x] Impl `FieldTypeRegistry` in `cms-content-types` (mirrors `BlockTypeRegistry`),
      bound singleton in the provider; extensions register kinds in `registerModule()`
      guarded by `bound()` (content-types loads first alpha).
- [x] **Retired the closed `FieldType` enum entirely** (implementer's call — cleaner
      single source of truth). Built-ins (text/textarea/richtext/number/boolean/date/
      select/media) are now ordinary pre-registered `FieldTypeDefinition`s seeded by
      `DefaultFieldTypes::registerInto()` in `registerModule()`; **no behaviour change**
      (same labels via `ucfirst`, same component + match semantics). `FieldDefinition::
      $type` is now a `string` kind key (was the enum), so a custom kind is first-class
      and never coerced to Text.
- [x] `SchemaValidator` injects the registry; `matchesType(string,mixed)` resolves the
      kind's `matches` predicate — unknown/unregistered kind → `false` → the existing
      `InvalidContentData::wrongType` path (now `(string $field, string $type)`).
- [x] `ContentEntryResource::componentFor` builds each field's component from the
      registry (unknown kind → safe `TextInput` fallback; a factory that doesn't return
      a `Field` → `RuntimeException`). `ContentTypeResource` schema editor uses
      `registry->options()` + default `'text'`.
- [x] Tests: `tests/Feature/Cms/FieldTypeRegistryTest.php` (5) — built-ins seeded, a
      **custom `color` kind** registers + appears in the schema-editor options +
      validates via its predicate (`#fff` ok, `not-a-color` rejected) + its factory
      builds a real Filament `Field`; unregistered kind rejected. Existing
      ContentTypeModuleTest/CmsContentEntryResourceTest/CmsContentTypeResourceTest/
      ContentEntrySchemaValidationTest all still green (built-ins unchanged;
      `FieldType::Text` assertion updated to `'text'`).
- [x] DoD: Pint clean · PHPStan **max** clean · arch-test green (`cms-content-types`
      imports only contracts + core + filament) · full suite **580 green on SQLite**
      (was 575).
