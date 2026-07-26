# 04 — API token-management UI

**What to build:** An admin can issue, name, scope (read vs `content:write`), view, and revoke Delivery API tokens for their Team from the Filament panel — replacing the `cms-api:issue-token` CLI as the primary flow — with the plaintext token shown exactly once on creation.

**Blocked by:** None — wraps the existing Sanctum Team-token mechanism from Phase 5 · Increment 1/4.

**Status:** ready-for-agent

- [ ] Filament page/resource (in `cms-api`, via the Phase 4 `AdminResourceRegistry`) listing the current Team's tokens: name, abilities, last-used, created; **never** the plaintext token after creation.
- [ ] Create action mints a token with chosen abilities (read-only or `content:write`), tenant-scoped to the acting Team, and reveals the plaintext **once**.
- [ ] Revoke action deletes a token; revoked token immediately fails API auth (`401`).
- [ ] Tenant-scoped: an admin sees/manages only their own Team's tokens (mirror the resource tenancy pattern; test cross-tenant invisibility).
- [ ] Gated by an admin permission (aligns with Phase 6 module-owned policies — declare the permission now).
- [ ] Behavior guarantees (Livewire/feature tests): create shows token once; list hides plaintext; revoke kills API access; cross-tenant tokens invisible.
- [ ] Pint + PHPStan max + full Pest suite green.
