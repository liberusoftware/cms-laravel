# 04 — Reliability: queue hardening

**What to build:** Make the CMS's one queued job (`cms-notifications/SendNotification`)
safe under at-least-once delivery, and make its failures observable through the metrics
seam. Deliberately lean — no generic idempotency machinery, because there is exactly one
job and nothing else needs it.

**Blocked by:** 01 (needs the metrics seam for the failure metric). Parallel with 02/03.

**Status:** DONE

## Harden `SendNotification` (`cms-notifications`)

- [ ] Explicit `$tries` + `backoff` (values documented in the ticket/config).
- [ ] `failed(Throwable $e)` handler: mark the job's `NotificationLog` row `failed`
      **and** emit a `notification.failed` metric (`bound()`-guarded on the recorder).
- [ ] **Idempotency guard** keyed on the `NotificationLog` id: a retried job that already
      delivered must not re-send (the one user-visible duplicate: a duplicate email). No
      `ShouldBeUnique`, no generic dedup layer.
- [ ] Confirm `failed_jobs` table/migration is present; document retention.

## Deferred to the checklist (ticket 05)

- Graceful degradation (search/cache down → serve empty/last-known rather than 500).
- Horizon + worker supervision.

## Tests / DoD

- [ ] Retry after a delivered notification does **not** re-send (idempotency guard).
- [ ] A failing job marks its `NotificationLog` `failed` and emits `notification.failed`
      (fake recorder).
- [ ] `$tries`/`backoff` honoured.
- [ ] Arch clean; Pint · PHPStan **max** (no new baseline) · full suite green on SQLite ·
      `/code-review`.
