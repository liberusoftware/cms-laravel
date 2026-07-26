# 04 — Security: module-owned authorization policies

**What to build:** Every CMS admin resource enforces per-resource `view/create/update/delete` authorization through the Phase 1 `AccessControlInterface` / `PermissionGroup` contract — closing OWASP A01 Finding 1.2. Today only the Module-management page checks a permission; every other resource is open to any panel user.

**Blocked by:** 03 (portability) — lands last in the foundation-first order, on green dual-DB CI.

**Status:** ready-for-agent

- [ ] Each content/admin module ships a policy for its model and **declares** `view/create/update/delete` permissions via the existing `PermissionRegistrarInterface` / `PermissionGroup` (extend the groups each module already registers). No host `App\` coupling (arch test stays green).
- [ ] Filament resources authorize through those permissions (`canViewAny`/`canCreate`/`canEdit`/`canDelete` or policies) resolving via `AccessControlInterface`. Covers Pages, Posts (+Category/Tag), Media, ContentTypes, ContentEntries, Menus/MenuItems, Forms, FormSubmissions, and the new API-token + audit-log resources.
- [ ] Shield remains the **assignment** UI only (roles/permissions), not per-resource policy generation.
- [ ] Seed a baseline role set (e.g. admin / editor / author / viewer) mapped to the declared permissions, assignable via Shield; document the default self-registered role (ties to ticket 07 / OWASP A04).
- [ ] Behavior guarantees: a low-privilege user is **forbidden** from each gated action (feature/Livewire tests per resource); an authorized user succeeds; unauthorized create/edit/delete → forbidden, not silent success.
- [ ] DoD: no ungated CMS resource; per-resource forbidden tests pass; arch test green; Pint + PHPStan max + full suite green (MySQL + Postgres).
