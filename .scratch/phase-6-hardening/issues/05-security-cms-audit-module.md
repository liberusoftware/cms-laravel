# 05 — Security: `cms-audit` module (audit logging)

**What to build:** A new removable `cms-audit` package that turns security-relevant events into append-only audit records, satisfying Part B §18 and OWASP A09. Mirrors the proven `cms-notifications` pattern: listen on the EventBus (and framework auth events), import nobody.

**Blocked by:** 04 (policies) — the admin viewer resource is gated by a module-owned permission.

**Status:** ready-for-agent

- [ ] New `cms-audit` package (`Liberu\Cms\Audit`, path repo, sibling layout), depending only on `cms-contracts` + `cms-core`; added to `phpstan.neon`; self-gating provider.
- [ ] Subscribes to security-relevant events and writes an **append-only** `audit_log` row (actor, action, subject type/id, tenant/team, ip, timestamp, metadata):
  - Auth: login success / failure / logout (framework `Illuminate\Auth\Events\*` — no host import needed).
  - Content: publish / unpublish (`ContentPublished` and workflow transitions on the EventBus).
  - Access: permission / role changes.
  - Tenant switches; module enable/disable (via ModuleManager events).
- [ ] Records are **not editable/deletable** through the app (tamper-evident intent); no `HasTenant` write-back that could rewrite history — store `team_id` as a plain column.
- [ ] Read-only Filament viewer (via `AdminResourceRegistry`), gated by a `cms-audit` module permission (ticket 04 pattern); filterable by actor/action/date.
- [ ] Proves cross-module event wiring: emitters (cms-forms, content modules, cms-core) import nothing from cms-audit.
- [ ] Behavior guarantees: each listed event produces exactly one audit row with correct actor/subject/tenant; a login failure is captured; records survive with no update/delete path.
- [ ] DoD: Part B §18 events captured; arch test green; Pint + PHPStan max + full suite green (MySQL + Postgres).
