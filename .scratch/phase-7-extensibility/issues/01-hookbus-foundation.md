# 01 — HookBus foundation (value-transforming hooks)

**What to build:** The kernel primitive that lets an extension *intercept and
modify* a value mid-flight, not just listen or add a sibling. A typed, open,
priority-ordered filter pipeline that mirrors `EventBus` exactly — a `Filter` marker
in `cms-contracts`, a `final readonly HookBus` in `cms-core`. Tracer bullet: wire
**one** filter point (block render) end-to-end so the whole path is proven before the
rest of the points land in ticket 02.

**Blocked by:** None — first ticket of Phase 7. Base branch off
`feature/cms-auth-hardening` HEAD (`2538bf1`).

**Status:** DONE (branch `feature/cms-hooks`, not pushed)

- [x] `Liberu\Cms\Contracts\Hooks\Filter` marker interface (`name(): string`,
      stable dot-notation), analogous to `CmsEvent`. Tagged `@api`.
- [x] `Liberu\Cms\Contracts\Hooks\HookBusInterface`: `listen(class-string<Filter>,
      Closure|string|array $callback, int $priority = 0): void` and
      `apply(Filter): Filter` (generic `@template T of Filter`). Tagged `@api`.
- [x] `Liberu\Cms\Core\Hooks\HookBus` — plain `final` (**not** `readonly`: it holds
      the mutable `$callbacks`/`$sequence` registry itself, since unlike `EventBus`
      there is no framework primitive to delegate to). Keyed by concrete `Filter`
      class; `apply()` runs every registered callback for that class in **ascending
      priority order** with an explicit `order` tiebreaker (deterministic regardless
      of `usort` stability), each mutating the filter in place, returns the same
      instance. Class/array callbacks container-resolved (guarded by `is_callable`,
      throws `InvalidArgumentException` otherwise). Empty list → identity.
- [x] Bind `HookBusInterface → HookBus` as a singleton in `cms-core`'s provider,
      immediately after the `EventBus` binding.
- [x] **Tracer bullet — block render point:** `Liberu\Cms\Contracts\Hooks\Filters\
      BlockRenderFilter` (mutable `html` + read-only `blockType`/`data`). The
      `cms-blocks` renderer returns `$this->hooks->apply(new BlockRenderFilter(...))
      ->html`; fires only for known block types. `@api` on the filter class.
- [x] Arch-test green: `cms-blocks` imports only `Liberu\Cms\Contracts\Hooks\*` (not
      even core), no host `App\` imports.
- [x] Tests: `tests/Unit/Cms/HookBusTest.php` (7 — identity, mutate, priority+ties,
      class keying, class-string + [class,method] callbacks, unresolvable→throws) +
      `tests/Feature/Cms/BlockRenderHookTest.php` (5 — real-renderer HTML transform,
      read-only context, no-hook untouched, container-rebound-honoured, unknown-type
      no-fire).
- [x] Reviewed via `/code-review` (Standards + Spec parallel agents): no hard
      violations, no scope creep; added the rebound-bus test the Spec axis flagged
      missing. DoD met: Pint clean · PHPStan **max** clean · arch-test green · full
      suite **566 green on SQLite** (was 554). MySQL/Postgres via the Phase-6 ticket-03
      CI matrix on push.
