# cms-users

Identity & Access for Liberu CMS — the **single authorization boundary** every
other module depends on. Foundational and non-removable.

## The rule it enforces

No module ever touches the users table, a role model, or the permission backend.
Modules ask one contract, `AccessControlInterface`, "may the current user do X?".
The implementation here answers via the framework gate, which the host's
Shield/Spatie setup populates — so consumers stay decoupled from the backend and
from any concrete `User`.

## Public contracts (in `cms-contracts`)

| Contract | Purpose |
|----------|---------|
| `AccessControlInterface` | `can` / `cannot` / `canForUser` / `authorize`. The authorization boundary. |
| `PermissionRegistrarInterface` | Modules declare the permissions they own. |
| `PermissionGroup` (DTO) | A module's permission set: key, label, `AccessScope`, abilities. |
| `AccessScope` (enum) | `Content` · `Media` · `Module` (Part B §7). |

## Usage from another module

```php
use Liberu\Cms\Contracts\Access\AccessControlInterface;

if (app(AccessControlInterface::class)->can('pages.publish', $page)) {
    // ...
}

// Declare your module's permissions (in your provider's boot):
$registrar->register(new PermissionGroup('pages', 'Pages', AccessScope::Content, [
    'view', 'create', 'update', 'delete', 'publish',
]));
```

Then materialise them into the backend:

```bash
php artisan cms:sync-permissions
```

## Shield vs. Spatie — resolved

They are layered, not competing: **Spatie** is the permission engine (stores
roles/permissions, wires the gate), **Shield** is the Filament admin UI and
policy/permission generator built on top of Spatie. This module wraps the
**gate** for ability checks and uses **Spatie** only internally (Golden Rule 2d)
to materialise permissions. Tenancy is inherited automatically: permission
checks evaluate within the active team's context.

## Events

- **Emits / Listens:** none yet.

## Extension points

- Register `PermissionGroup`s via `PermissionRegistrarInterface`.
- Swap the backend by rebinding `AccessControlInterface`.
