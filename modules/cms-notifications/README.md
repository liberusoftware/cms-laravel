# CMS Notifications

## Repository

Source, issues, and release history: https://github.com/liberusoftware/module-cms-notifications

Composer package: https://packagist.org/packages/liberusoftware/module-cms-notifications

Turns CMS events into notifications. It listens on the event bus and, for each
configured subscription, records an audit log row and queues delivery on a
channel — so the module that emitted the event never knows a notification was
sent.

## How it works

1. A module emits a `CmsEvent` (e.g. `FormSubmitted`, `ContentPublished`).
2. For each subscription on that event's name, a `NotificationLog` row is
   written (`status = pending`) and a queued `SendNotification` job is dispatched
   with that row's id.
3. The job delivers the message on its channel and marks the row `sent`.

## Delivery reliability (at-least-once)

`SendNotification` is hardened for a real queue, where a job can run more than
once:

- **Retries.** Explicit `tries` and `backoff` (`config('cms-notifications.queue')`,
  default 3 tries at 10/30/60s). A transient channel failure (e.g. SMTP blip) is
  retried rather than lost.
- **Idempotency guard.** The job is keyed on its `NotificationLog` row: if the row
  is already `sent`, a retry returns without re-delivering — so a duplicate email,
  the one user-visible duplicate, is avoided. No `ShouldBeUnique`, no generic dedup
  layer (there is exactly one queued job).
- **Failure is observable.** After the retries are exhausted, `failed()` marks the
  row `failed` and emits a `notification.failed` metric (tagged by channel) through
  the metrics seam, `bound()`-guarded so it is inert without observability.

### `failed_jobs` retention

Exhausted jobs land in Laravel's `failed_jobs` table (the framework
`0001_01_01_000002_create_jobs_table` migration — present, no action needed).
Prune it on a schedule in production, e.g. `php artisan queue:prune-failed
--hours=168` (keep one week). Worker supervision and this retention schedule are
covered by the production/ops checklist.

## Subscriptions

Configure in `config('cms-notifications.subscriptions')`, keyed by event name:

```php
'forms.submitted' => [
    ['channel' => 'mail', 'to' => 'team@example.com', 'subject' => 'New form submission'],
],
'content.published' => [
    ['channel' => 'log', 'subject' => 'Content published'],
],
```

Built-in events: `forms.submitted`, `content.published`. Defaults use the `log`
channel so the module works with no configuration; switch to `mail` and set `to`
to deliver email.

## Channels

- **mail** — plain-text email to the subscription's recipients.
- **log** — writes to the application log.

Add a channel by implementing `NotificationChannelInterface` and registering it
on the `ChannelManager`.

## Config

Publish with `php artisan vendor:publish --tag=cms-notifications-config`.
