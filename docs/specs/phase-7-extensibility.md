# Phase 7 — Extensibility (plan)

Status: **planned** (not started). Base branch: new branch off
`feature/cms-auth-hardening` HEAD (`2538bf1`), continuing the stacked,
push-bottom-up workflow used through Phases 5–6.

## Thesis

The CMS already ships a great deal of extension machinery — a dependency-resolving
module system + generator (`cms:make-module`), the `EventBus`, and a family of
registries (Admin, Api, Search, Sitemap, Preview, Dashboard, Block, Widget,
Permission). Phase 7 is therefore **not** a greenfield "add extensibility" phase.
It does two things:

1. **Turns the existing internal seams into a *public, documented, stable*
   extension surface** third parties can build against with confidence.
2. **Adds the one primitive that is genuinely missing** — *value-transforming
   hooks*. Today an extension can only **listen** (`EventBus`, fire-and-forget) or
   **add a sibling** to a registry. It cannot **intercept and modify** an existing
   value mid-flight (rewrite a block's HTML, add fields to another module's edit
   form, reshape an API response, alter a query). That is the single most-requested
   real-world extension capability.

Non-goal: runtime plugin installation. Extensions are **Composer packages added at
deploy time** (Golden Rule: "everything is a removable Composer package"), keeping
immutable deploys and zero runtime code-execution surface. The admin *Modules* page
keeps governing enable/disable of what is installed.

## Locked design decisions

| # | Decision | Choice |
|---|----------|--------|
| 1 | Next phase | Phase 7 now; Phase 6.5 (perf/observability/reliability) deferred |
| 2 | Install model | Composer, deploy-time |
| 3 | New primitive | Typed, value-transforming **HookBus** (filter pipeline) |
| 4 | Hook reach | **Open** — any module may declare its own filter points and hook any point; class-keyed, typed payload |
| 5 | Hook home | Kernel — `Filter` marker in `cms-contracts`, `HookBus` in `cms-core` (mirrors `EventBus`) |
| 6 | Core filter points | **4, one per layer**: block render · admin form schema · API resource payload · content query |
| 7 | Public-API stability | `@api` docblock convention + docs catalog + written semver/deprecation policy + Pest arch-test guard (no new dependency) |
| 8 | Open closed points | `FieldType` enum → `FieldTypeRegistry` (custom kinds contribute Filament component + validation) |
| 9 | Scaffolding | `cms:make-block`, `cms:make-hook`, `cms:make-field-type` |
| 10 | Reference extension | Grow the existing in-repo `cms-hello` to exercise the whole surface + "Build your first extension" guide |
| 11 | Deferred | Extension settings surface · version-compatibility declaration · webhooks |

## The HookBus (contract sketch)

Mirrors `EventBus` exactly: a typed marker in `cms-contracts`, a `final readonly`
wrapper in `cms-core` that resolves collaborators from the container per call.

```php
// cms-contracts — Liberu\Cms\Contracts\Hooks
interface Filter
{
    /** Stable dot-notation name, e.g. "blocks.render", "pages.admin.form". */
    public function name(): string;
}

interface HookBusInterface
{
    /**
     * @param  class-string<Filter>  $filterClass
     * @param  Closure|class-string|array{0: class-string, 1: string}  $callback
     */
    public function listen(string $filterClass, Closure|string|array $callback, int $priority = 0): void;

    /**
     * Run every registered callback for $filter->name(), in priority order,
     * each mutating the filter's payload in place; returns the same instance.
     *
     * @template T of Filter
     * @param  T  $filter
     * @return T
     */
    public function apply(Filter $filter): Filter;
}
```

- **Typed & PHPStan-max clean:** each filter point is a concrete `Filter` class
  carrying a *mutable* typed payload (plus any read-only context). Callbacks receive
  the object and mutate; `apply()` returns the same instance, so there is no
  `mixed`-typed generic return to fight the analyser.
- **Open:** `apply()`/`listen()` are keyed by the concrete `Filter` class, so any
  module can define new `Filter` subclasses and expose them — the ecosystem is
  itself composable. Core ships a curated, documented set (below).
- **Priority-ordered**, deterministic; container-resolved dispatch so a rebound bus
  (tests) is always honoured.

### The 4 curated core filter points

| Filter | Layer | Payload / effect | Emitting site |
|--------|-------|------------------|---------------|
| `BlockRenderFilter` | Presentation | mutate a block's rendered HTML before emit | `cms-blocks` renderer |
| `AdminFormSchemaFilter` | Admin | add/modify Filament form components on a resource — *the* canonical WordPress use case (extend another module's edit form) | content-module Filament resources |
| `ApiResourceFilter` | Delivery | reshape a resource array before serialization | `cms-api` resources |
| `ContentQueryFilter` | Data | alter an Eloquent builder before execution | content repositories |

**Tenant-safety guard (ContentQueryFilter):** the filter runs **after** global
scopes are applied, and a test proves a callback cannot re-introduce cross-tenant
rows (no `withoutGlobalScope` escape). This is the one point where a careless
extension could break isolation, so it is guarded and documented explicitly.

## Public API stability

`cms-contracts` today mixes genuinely extension-facing interfaces (the registries,
`Block/Widget/Theme`, and now `HookBus`/`Filter`/`FieldTypeRegistry`) with internal
kernel wiring (`ModuleStateRepositoryInterface`, `TenantContextInterface`). Phase 7:

- **Designate** the public subset with an `@api` docblock convention.
- **Catalog** it in `docs/EXTENSION-API.md` with a written **semver + deprecation
  policy** (how a public contract may change, deprecation window, what "internal"
  means).
- **Guard** it with a Pest arch-test: the `@api` interfaces exist, and internal
  (`@internal`) contracts are not leaked into extension-facing signatures.

No new dependency (an automated BC-checker was considered and rejected for 0.x churn
and the approval cost).

## FieldTypeRegistry (open the closed seam)

`Liberu\Cms\ContentTypes\Fields\FieldType` is a closed `enum`, and its Filament-
component + validation-rule mappings are hardcoded — so a third party cannot add a
`color`, `relation`, or `geo` field kind without editing core. Phase 7 introduces a
`FieldTypeRegistry` (contract in `cms-contracts`, impl in `cms-content-types`): a
custom field kind registers `key → { Filament component factory, validation-rule
fragment, cast }`. The existing built-ins **re-seed the registry** (no behaviour
change); `SchemaValidator` and the `ContentEntryResource` dynamic form read from the
registry instead of the enum.

## Scaffolding generators

Peers of `cms:make-module`, but *within-module artifact* generators (you already
have a module — now stub a feature):

- `cms:make-block` — a `BlockTypeInterface` impl + registry registration.
- `cms:make-hook` — a `Filter` marker class **and** a consumer-callback stub
  (highest value: the idiom is brand-new).
- `cms:make-field-type` — a custom field kind registered into `FieldTypeRegistry`.

## Reference extension + developer guide

Grow the existing `cms-hello` PoC into the canonical reference: it registers an
admin resource + an API endpoint + a block type, **consumes** a core filter point,
**defines its own** filter point, and **adds a custom `FieldType`** via the new
registry — staying in-repo so it is CI-tested living documentation. The
`docs/BUILD-YOUR-FIRST-EXTENSION.md` guide documents the external-package path
(composer path/VCS repository entry, the no-`App\`-imports rule already enforced by
arch-test) so nothing about being "in-repo" hides a real third-party step.

## Increments (foundation-first, stacked branches)

| # | Ticket | Blocked by |
|---|--------|-----------|
| 01 | HookBus foundation (tracer: block-render point) | base |
| 02 | Remaining 3 core filter points | 01 |
| 03 | FieldTypeRegistry | base (parallelizable; stacked after 02) |
| 04 | Scaffolding generators | 01 + 03 |
| 05 | Public contract catalog + stability | 01 + 03 (+ 02) |
| 06 | Reference extension + dev guide | all |

Tickets live in `.scratch/phase-7-extensibility/issues/`. ADR:
`docs/adr/0002-extension-hooks-and-public-api.md`.
