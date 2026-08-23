# cms-contracts

The shared vocabulary of Liberu CMS: interfaces, cross-module events, and DTOs.

**Everything depends on this package; this package depends on nothing** (only PHP).
It contains no framework code and no implementations, so it can be required by
any Laravel application, module, or external consumer without pulling in the CMS.

## What lives here

| Namespace | Purpose |
|-----------|---------|
| `Liberu\Cms\Contracts\Module` | Module descriptor, registry, manager, and state-repository contracts that drive discovery, enable/disable, and boot ordering. |
| `Liberu\Cms\Contracts\Events` | `CmsEvent` marker for cross-module events and the `EventBusInterface` they travel over. |

## The rule this package enforces

Modules communicate through the interfaces and events declared here — never by
importing each other's concrete classes. If module A needs something from
module B, the capability is expressed as a contract in this package and bound in
the container by B. A resolves the contract; it never names B.

## Key contracts

- **`ModuleInterface`** — a module's metadata descriptor (`key`, `name`,
  `version`, `dependencies`, `isFoundational`).
- **`ModuleRegistryInterface`** — the catalogue of every known module.
- **`ModuleManagerInterface`** — the authority on whether a module is enabled,
  plus dependency-aware `enable()` / `disable()` and `bootOrder()`.
- **`ModuleStateRepositoryInterface`** — persistence for enable/disable decisions.
- **`EventBusInterface`** + **`CmsEvent`** — the type-safe cross-module event seam.

## Consumed by

`cms-core` (implements these), and every CMS module (depends on the interfaces).

## Emits / listens

None. This package defines event *types* but never dispatches or handles them.
