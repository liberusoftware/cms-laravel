# CMS Notifications

Turns CMS events into notifications. It listens on the event bus and, for each
configured subscription, records an audit log row and queues delivery on a
channel — so the module that emitted the event never knows a notification was
sent.

## How it works

1. A module emits a `CmsEvent` (e.g. `FormSubmitted`, `ContentPublished`).
2. For each subscription on that event's name, a `NotificationLog` row is
   written and a queued `SendNotification` job is dispatched.
3. The job delivers the message on its channel.

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
