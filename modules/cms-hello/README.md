# cms-hello

A trivial proof-of-concept module. It exists to exercise the entire module
pipeline end to end and to be the module CI enables, disables, and re-enables.

Non-foundational by design: the "disable any module and the app still boots"
guarantee is verified against it.

## What it demonstrates (the full §5 module contract)

- **Descriptor** — `HelloModule` (key `hello`).
- **Self-gating provider** — `HelloServiceProvider` extends `ModuleServiceProvider`.
- **Own contract + service** — `GreeterInterface` bound to `Greeter`.
- **Cross-module event** — `HelloGreeted` dispatched over the `EventBus`.
- **Versioned API route** — `GET /api/v1/hello/{name?}`.
- **Migration** — `hello_greetings`.
- **Config** — `config/hello.php` (`hello.greeting`), publishable + merged.
- **Tests** — unit + feature, including enable/disable.

## Install

```bash
composer update liberu-cms/cms-hello
php artisan migrate
```

## Try it

```
GET /api/v1/hello/ada  ->  { "module": "hello", "message": "Hello, ada!" }
```

## Config keys

| Key | Default | Purpose |
|-----|---------|---------|
| `hello.greeting` | `Hello, :name!` | Template; `:name` is substituted. |

## Events

- **Emits:** `Liberu\Cms\Hello\Events\HelloGreeted` (`hello.greeted`) — on every greeting.
- **Listens:** none.

## Public contracts

- `Liberu\Cms\Hello\Contracts\GreeterInterface` — resolve to produce greetings.

## Disable

```php
app(\Liberu\Cms\Contracts\Module\ModuleManagerInterface::class)->disable('hello');
```

The route 404s and the provider goes inert; the rest of the app is unaffected.
