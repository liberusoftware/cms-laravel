# CMS Audit and History

## Repository

Source, issues, and release history: https://github.com/liberusoftware/module-cms-audit-and-history

Composer package: https://packagist.org/packages/liberusoftware/module-cms-audit-and-history

Audit logging for Liberu CMS. Turns security-relevant events into **append-only**
audit records (Part B §18 / OWASP A09), with a read-only admin viewer.

## What it captures

Registered only while the module is enabled (disabling it stops auditing):

| Action | Source |
|--------|--------|
| `auth.login`, `auth.logout`, `auth.failed` | Framework auth events (`Illuminate\Auth\Events\*`) |
| `content.published` | `ContentPublished` (EventBus) |
| `content.state_changed` | `ContentStateChanged` (EventBus) |

Each record snapshots the actor (id + a human label so it survives user
deletion), the subject type/id, the acting team, the request IP, the action,
metadata, and a timestamp.

## Design

- **Pure listener.** Depends only on `cms-contracts` + `cms-core`; no emitter
  imports anything from here. Content events cross the `EventBusInterface`;
  authentication events are framework events on the underlying dispatcher.
- **Append-only / tamper-evident.** `AuditLog` refuses updates and deletes, and
  the Filament viewer disables every write path. `team_id` is a plain column
  (no `HasTenant`), so tenant scoping never rewrites history.
- **Gated viewer.** `AuditLogResource` is read-only and gated by the module-owned
  `audit.view` permission (the ticket-04 authorization pattern), filterable by
  action, actor, and date.

## Not yet captured

Role/permission changes, tenant switches, and module enable/disable are not
emitted as events by their sources today; capturing them is a follow-up that
adds event emission at each site (then a handler here).
