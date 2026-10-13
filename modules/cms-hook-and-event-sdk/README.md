# CMS Hook and Event SDK

This module publishes the stable extension boundary for CMS integrations. It
uses the typed `HookBusInterface` and `EventBusInterface` contracts and the
priority-ordered `HookBus` implementation supplied by CMS Core. Applications
can therefore install the SDK without coupling extensions to a presentation
framework or a provider SDK.

Hooks are typed filters, run in ascending priority order, and are isolated by
filter class. Events are past-tense `CmsEvent` objects dispatched through the
framework boundary. Consumers should depend on the contracts package rather
than concrete implementations.
