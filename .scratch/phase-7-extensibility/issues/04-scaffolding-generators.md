# 04 — Scaffolding generators (`make-block` / `make-hook` / `make-field-type`)

**What to build:** Within-module artifact generators that lower the barrier to
authoring extensions — peers of the existing `cms:make-module` (which already
scaffolds a whole bootable module). "You have a module, now stub a feature." The
three chosen reinforce this phase's seams; `make-hook` is highest value because the
`Filter` idiom is brand-new and non-obvious.

**Blocked by:** 01 (HookBus/`Filter` idiom for `make-hook`) + 03 (`FieldTypeRegistry`
for `make-field-type`).

**Status:** ready-for-agent

- [ ] `cms:make-block {module} {name}` — scaffolds a `BlockTypeInterface` impl in the
      target module + its registry registration, following the existing prebuilt
      block types. Bootable/registered out of the box.
- [ ] `cms:make-hook {module} {name}` — scaffolds **both** a `Filter` marker class
      (typed mutable payload + `name()`) **and** a consumer-callback stub with the
      `HookBus::listen(...)` registration line, so the author sees both sides of the
      new idiom.
- [ ] `cms:make-field-type {module} {name}` — scaffolds a `FieldTypeDefinition`
      registration into `FieldTypeRegistry` (component factory + validation fragment
      + cast stub).
- [ ] Follow `MakeModuleCommand` conventions exactly: `cms:make-*` signature,
      `#[\Override]`, private-`Filesystem` ctor, inline stub methods, `strtr`
      replacements, `components->info/bulletList` output, graceful "already exists"
      failure. Commands auto-register (Laravel 12 structure) — no manual wiring.
- [ ] Generated artifacts pass Pint + PHPStan max as-emitted (the stubs must be
      clean, typed, and boot without edits — like `make-module`'s output).
- [ ] Tests (`tests/Feature/Cms/Make*CommandTest.php`): each command writes the
      expected files into a temp module and the result is registerable; re-running
      against an existing target fails gracefully. Clean up generated files.
- [ ] DoD: Pint clean · PHPStan **max** clean · arch-test green · full suite green on
      SQLite.
