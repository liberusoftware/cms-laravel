# 05 — Security: `cms-audit` module (audit logging)

**What to build:** A new removable `cms-audit` package that turns security-relevant events into append-only audit records, satisfying Part B §18 and OWASP A09. Mirrors the proven `cms-notifications` pattern: listen on the EventBus (and framework auth events), import nobody.

**Blocked by:** 04 (policies) — the admin viewer resource is gated by a module-owned permission.

**Status:** DONE (branch `feature/cms-audit`, not pushed)

- [x] New `cms-audit` package (`Liberu\Cms\Audit`, path repo, sibling layout), depends only on `cms-contracts` + `cms-core` (+ framework); added to `phpstan.neon` + root composer require; self-gating `CmsAuditServiceProvider`. Installed via `composer update liberu-cms/cms-audit --ignore-platform-reqs`.
- [x] Subscribes to security-relevant events and writes an **append-only** `cms_audit_logs` row (action, actor_id + actor_label snapshot, subject_type/id, team_id, ip_address, metadata, created_at):
  - [x] Auth: `auth.login` / `auth.failed` / `auth.logout` — framework `Illuminate\Auth\Events\{Login,Failed,Logout}` registered on the underlying dispatcher (EventBus is typed to `CmsEvent`, so auth events go direct). No host import.
  - [x] Content: `content.published` (`ContentPublished`) + `content.state_changed` (`ContentStateChanged` workflow transitions) on the EventBus.
  - [ ] **Access (role/permission) + tenant switches + module enable/disable: NOT captured — their sources emit no events today.** Spatie fires none, `HasTeams::switchTeam` fires none, `ModuleManager` (final readonly, no EventBus dep) fires none. Documented as a follow-up in the package README: add emission at each site, then a handler here (the listener architecture makes that a few lines).
- [x] Records not editable/deletable through the app: `AuditLog` throws on `updating`/`deleting`, `UPDATED_AT = null`, and the viewer disables every write path. `team_id` is a plain column (no `HasTenant`).
- [x] Read-only Filament viewer `AuditLogResource` via `AdminResourceRegistry`, gated by the module-owned `audit.view` permission (ticket-04 `AuthorizesWithPermissions` trait); filterable by action (SelectFilter), actor (searchable), and date (range filter).
- [x] Cross-module wiring proven: cms-audit imports only contracts + core + framework; no emitter imports it (arch test green).
- [x] Behaviour guarantees: `tests/Feature/Cms/AuditLogTest.php` (9 tests) — login/logout/failed capture (actor snapshot; failed has email but no actor), publish + state-change subjects/metadata, exactly-one-row, update/delete both throw, viewer gated by `audit.view`.
- [x] DoD: the capturable §18 events (auth + content) captured; arch test green; Pint + PHPStan max clean; full suite **543 green on SQLite** (MySQL + Postgres via ticket-03 CI matrix on push — no pgsql/mysql PDO locally). Remaining §18 categories deferred per the README note above.
