# 04 — Scaffolding generators (`make-block` / `make-hook` / `make-field-type`)

**What to build:** Within-module artifact generators that lower the barrier to
authoring extensions — peers of the existing `cms:make-module` (which already
scaffolds a whole bootable module). "You have a module, now stub a feature." The
three chosen reinforce this phase's seams; `make-hook` is highest value because the
`Filter` idiom is brand-new and non-obvious.

**Blocked by:** 01 (HookBus/`Filter` idiom for `make-hook`) + 03 (`FieldTypeRegistry`
for `make-field-type`).

**Status:** DONE (branch `feature/cms-hooks`, not pushed)

- [x] `cms:make-block {module} {name}` — scaffolds `src/Blocks/{Name}Block.php`
      implementing `BlockTypeInterface` (escapes via `e()`); prints the
      `BlockTypeRegistryInterface->register(...)` bootModule snippet.
- [x] `cms:make-hook {module} {name}` — scaffolds **both** `src/Hooks/{Name}Filter.php`
      (mutable `value` payload + `name()` = `{module}.{name}`) **and**
      `src/Hooks/{Name}Listener.php` (invokable that transforms the payload); prints
      the `HookBus::listen(...)` snippet + the emit-site apply line.
- [x] `cms:make-field-type {module} {name}` — scaffolds `src/Fields/{Name}FieldType.php`
      with a static `registerInto(FieldTypeRegistryInterface)` that registers a
      `FieldTypeDefinition` (TextInput factory + `is_string` predicate to adjust);
      prints the registerModule snippet.
- [x] Shared `AbstractMakeArtifactCommand` base (cms-core/Console) holds
      `resolveTarget()` (validates the module exists → graceful error) + `writeStub()`
      (refuses to overwrite). Three concrete commands follow `MakeModuleCommand`
      conventions (`cms:make-*`, `#[\Override]`, `Filesystem` ctor, nowdoc stubs,
      `strtr`, `components->info/bulletList`). Registered in `CmsCoreServiceProvider`.
- [x] **Enabling contract:** added `BlockTypeRegistryInterface` (`@api`, cms-contracts)
      + aliased `BlockTypeRegistry` to it in `BlocksServiceProvider`, so the generated
      block registers cross-module via the contract (arch-clean) — mirrors every other
      registry; also unblocks the ticket-06 reference extension.
- [x] Generated artifacts pass **Pint + PHPStan max as-emitted** — verified by
      generating real samples into cms-hello (in the phpstan path), running both gates
      clean, then removing them.
- [x] Tests: `tests/Feature/Cms/MakeArtifactCommandTest.php` (5) — each command writes
      the expected file(s), the result autoloads + is registerable (block registers,
      hook Filter/Listener behave, field kind registers), missing module fails, and
      re-run refuses to overwrite. Generated files removed in `afterEach`.
- [x] DoD: Pint clean · PHPStan **max** clean · arch-test green · full suite **585
      green on SQLite** (was 580).
